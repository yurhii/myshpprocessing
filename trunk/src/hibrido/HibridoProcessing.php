<?PHP	
class HibridoProcessing extends MapWareCore{
	
	var $imagenes_por_request = 200;
	var $cpu;
	
	var $bufferSize;
	var $nivelInicial;
	var $nivelFinal;
	
	var $layer;
	var $image;
	var $imageCanvas;
	var $square;
	
	var $tipoProcesamiento;
	
	var $nivel;
	
	function HibridoProcessing($nivel = 1, $cpuNumber = 1, $tipoProcesamiento = 0){
		$this->openMySQLConn();
		$this->defineMapWareBounds();
		$this->nivel = $nivel;
		$this->cpu = $cpuNumber;
		$this->tipoProcesamiento = $tipoProcesamiento;
	}
	function startProcessingAllNoMatchToOurSateliteAssets(){
		$this->actualizarEscalaPorNivel($this->nivel);
		
		$query = "select imagenes.*, astext(imagenes.mysql_puntos) as mysql_puntos_text
		from imagenes
		where imagenes.nivel = '$this->nivel'";
		if($this->tipoProcesamiento == 0){
			$query .= "and mapa_exists = '1' and hibrido_exists = '0'";
		}else if ($this->tipoProcesamiento == 1){
			$query .= "and mapa_exists = '2' and hibrido_exists = '0'";
		}
		$query .= "AND `cpu` = '".$this->cpu."'
		limit $this->imagenes_por_request";
		$res = mysql_query($query) or die($query);
		$this->processImagesForHibrid($res);
		//
		return (mysql_num_rows($res) != 0);
	}
	
	function startProcessingToMatchOurSateliteAssets(){
		$this->actualizarEscalaPorNivel($this->nivel);
		
		$query = "select imagenes.*, astext(imagenes.mysql_puntos) as mysql_puntos_text
		from satelite_originales_por_imagen
		join imagenes on satelite_originales_por_imagen.i = imagenes.i
			and satelite_originales_por_imagen.j = imagenes.j and satelite_originales_por_imagen.nivel = imagenes.nivel
		where imagenes.nivel = '$this->nivel'";
		if($this->tipoProcesamiento == 0){
			$query .= "and mapa_exists = '1' and hibrido_exists = '0'";
		}else if ($this->tipoProcesamiento == 1){
			$query .= "and mapa_exists = '2' and hibrido_exists = '0'";
		}
		$query .= "AND `cpu` = '".$this->cpu."'
		limit $this->imagenes_por_request";
		$res = mysql_query($query) or die($query);
		$this->processImagesForHibrid($res);
		//
		return (mysql_num_rows($res) != 0);
	}
	
	function processImagesForHibrid($res){
		while($this->image = mysql_fetch_array($res)){
			//***************************************extraer elementos y dibujar*****************************************/
			//saber en que cuadro estamos
			$this->square = array($this->image["i"], $this->image["j"]);
			//crear la imagen correspondiente y fijar sus variables
			//la creamos 20 pixeles mas grande para evitar la raya
			//crear imagen con transparencia en el fondo
			$this->imageCanvas = new image($this->resize * $this->squareSize+20, $this->resize * $this->squareSize+20, '00000000', true);
			$this->imageCanvas->setVars($this->escala, $this->xmin, $this->ymin);
			//
			$query = "SELECT * FROM tables
			WHERE hibridDrawLayerOrder != 0
			ORDER BY `hibridDrawLayerOrder` asc";
			$layers = mysql_query($query) or die($query);
			//dibujar cada capa de informacion geografica
			while($this->layer = mysql_fetch_array($layers)){
				$catalgos = explode(",", $this->layer["campoCatalogo"]);
				$this->layer["pathCampoTipo"] = $catalgos[0]."_id";
				if($this->nivel >= $this->layer["drawFromNivel"] && $this->nivel <= $this->layer["drawToNivel"]){
					$this->drawLayer();
				}
			}
			//dibujar labels	
			$this->drawLabels();
			
			$image200px = imagecreatetruecolor(200, 200);
			//transparencia de esta imagen
			//imagealphablending($image200px, false);
			imagesavealpha($image200px, true);
			$transparent = imagecolorallocatealpha($image200px, 0, 0, 0, 127);
			imagefill($image200px, 0, 0, $transparent);
			//copiar imagen en esta
			imagecopyresampled($image200px, $this->imageCanvas->image, 0, 0, 0, 0, 200, 200, 200*$this->resize, 200*$this->resize);
			//definir la ruta del archivo
			$archivo = "temporales/hibrid".$this->cpu.".png";
			//
			imagepng($image200px, $archivo, 3) or die("no generation of image");
			chmod($archivo, 0777);
			//tomar la informacion del archivo e ingresarla a la base de datos
			$file = mysql_real_escape_string(file_get_contents($archivo));
			//guardar en base de datos que la imagen ya fue dibujada
			$query = "UPDATE `imagenes`
			SET `hibrido` = '$file', `hibrido_exists` = '1'
			WHERE `i` = '".$this->image["i"]."' and `j` = '".$this->image["j"]."' 
			and `nivel` = '".$this->image["nivel"]."'";
			
			mysql_query($query) or die($query);
		}
	}
	
	function drawLayer(){
		//dependiendo de la clase de la capa a dibujar
		switch($this->layer["class"]){
			case "RecordPolyLine":
				$this->drawPath(true);
				break;
			case "RecordPolygon":
				$this->drawPolygon();
				break;
			case "RecordPoint":
				
				break;
		}
	}
	function drawLabels(){
		$query = "SELECT labels.x, labels.y, labels.size, labels.text, labels.angle, tables.colorLabel 
		FROM labels 
		join labels_por_imagen on labels.clave = labels_por_imagen.clave
		join tables on tables.table_name = labels.table_name
		WHERE clean = '2'
		AND labels_por_imagen.i = '".$this->image["i"]."' AND labels_por_imagen.j = '".$this->image["j"]."'
		AND labels_por_imagen.nivel = '".$this->image["nivel"]."'";
		//AND MBRINTERSECTS(mysql_puntos, geomfromtext('".$this->image["mysql_puntos_text"]."')) = 1";	
		$labels = mysql_query($query) or die($query);
		//imprimir labels
		while($label = mysql_fetch_array($labels)){
			$x = $label["x"];
			$x -= $this->xmin + $this->square[0]*$this->squareSize*$this->escala;
			$y = $label["y"];
			$y -= $this->ymin + $this->square[1]*$this->squareSize*$this->escala;
			//regresar el fontsize a tamaño normal
			$fontsize = $label["size"]/pow(2, 13 - $this->nivel);
			$this->imageCanvas->drawText($this->resize * $x/$this->escala, $this->resize * $y/$this->escala, $label["angle"], $this->font, $this->resize * $fontsize, $label["colorLabel"], $label["text"]);
		}
		mysql_free_result($labels);
	}
	function drawPath($withBkg){
		$table_memory_index = rand() % 2;
		//sacar el codogio de colores y el thick de mapWareCartographicEsthetic con la informacion del campo que guarda el tipo de path
		// es decir pathCampoTipo en shp_tablas
		//sacar los tipos de paths asociados a esta tabla
		$query = "SELECT `paths_tipos`.tipo_id, l_r.longitud_minima FROM `paths_tipos`
		join `paths_tipos__length__restrictions` as l_r on l_r.path_tipo_id = paths_tipos.id
		WHERE `table_name` = '".$this->layer["table_name"]."' and paths_tipos.drawOrder > 0
		AND l_r.nivel = '".$this->nivel."'";
		$path_tipos = mysql_query($query) or die($query);
		//si hay paths a dibujar a este nivel continuamos
		if(mysql_num_rows($path_tipos) != 0){
			//sacar los paths a dibujar con todos los atributos necesarios
			$query = "SELECT `".$this->layer["table_name"]."`.text_puntos, 
			`colores`.`color`, `thicks`.`thick`, `thicks`.`thickBkg`, `".$this->layer["table_name"]."_memory$table_memory_index`.`drawOrder`
			FROM  `".$this->layer["table_name"]."_memory$table_memory_index` 
			JOIN ".$this->layer["table_name"]." on  `".$this->layer["table_name"]."_memory$table_memory_index`.clave = ".$this->layer["table_name"].".clave
			JOIN `paths_tipos` ON `paths_tipos`.`tipo_id` =  `".$this->layer["table_name"]."_memory$table_memory_index`.`".$this->layer["pathCampoTipo"]."`
			JOIN `paths_tipos__colores` as `colores`
			ON `colores`.`path_tipo_id` = `paths_tipos`.`id`
			JOIN `paths_tipos__thicks` as `thicks`
			ON `thicks`.`path_tipo_id` = `paths_tipos`.`id` and `thicks`.`nivel` = '".$this->nivel."'
			JOIN `paths_tipos__length__restrictions` as `length`
			ON `length`.`path_tipo_id` = `paths_tipos`.`id` AND `length`.`nivel` = '".$this->nivel."'
			join ".$this->layer["table_name"]."_por_imagen_$this->nivel on ".$this->layer["table_name"]."_por_imagen_$this->nivel.clave = `".$this->layer["table_name"]."_memory$table_memory_index`.clave
			WHERE 
			".$this->layer["table_name"]."_por_imagen_$this->nivel.i = ".$this->image["i"]." AND
			".$this->layer["table_name"]."_por_imagen_$this->nivel.j = ".$this->image["j"]." AND
			".$this->layer["table_name"]."_por_imagen_$this->nivel.nivel = '".$this->nivel."'
			AND (";
			//MBRIntersects(`".$this->layer["table_name"]."`.mysql_puntos, geomfromtext('".$this->image["mysql_puntos_text"]."') ) = '1'
			//filtrar por longitud
			$where_clause = array();
			while($filtro = mysql_fetch_array($path_tipos)){
				$subquery = " `".$this->layer["table_name"]."`.`".$this->layer["pathCampoTipo"]."` = '".$filtro["tipo_id"]."' ";
				if($filtro["longitud_minima"] > 0){
					$subquery .= " AND `".$this->layer["table_name"]."_memory$table_memory_index`.`longitud` > '".$filtro["longitud_minima"]."' ";
				}
				array_push($where_clause,  "(".$subquery.")");
			}
			$query .= implode(" OR ", $where_clause);
			$query .= ")";
			//order by `".$this->layer["table_name"]."_memory$table_memory_index`.drawOrder desc"; 
			$paths = mysql_query($query) or die($query);
			//dibujar bkg
			if($withBkg){
				$this->drawPathLine($paths, true);
			}
			//dibujar path sobre el bkg
			$this->drawPathLine($paths, false);
			mysql_free_result($paths);
		}
	}
	function drawPathLine($paths, $bkg = false){
		//regresar el playhead al cero
		if(mysql_num_rows($paths) != 0){
			mysql_data_seek($paths, 0);
		}
		
		$storedPaths = array();
		$index = 0;
		while($path = mysql_fetch_array($paths)){
			$storedPaths[$path["drawOrder"]."_".$index] = $path;
			$index++;
		}
		ksort($storedPaths);
		
		$storedPaths = array_reverse($storedPaths);
		
		//para cada path a dibujar
		foreach ($storedPaths as $key => $path) {
			$puntos = explode(",", $path["text_puntos"]);
			//set color
			$color = $path["color"];
			//set thick
			$thick = $bkg ? $path["thickBkg"] : $path["thick"];
			//ajustamos el thick para el hibrido que debe ser mas delgado
			$thick /= 3;
			//si es bkg el color es gris	
			$color = $bkg ? 'A5A5A500' : $color;
			for($k = 1; $k < count($puntos); $k++){
				$xy = explode(" ", $puntos[$k]);
				$xy[0] -= $this->xmin + ($this->square[0])*$this->squareSize*$this->escala;
				$xy[1] -= $this->ymin + ($this->square[1])*$this->squareSize*$this->escala;
				$xypre  = explode(" ", $puntos[$k-1]);
				$xypre[0] -= $this->xmin + ($this->square[0])*$this->squareSize*$this->escala;
				$xypre[1] -= $this->ymin + ($this->square[1])*$this->squareSize*$this->escala;
				$this->imageCanvas->drawPolygonLine($this->resize * $xypre[0]/$this->escala, $this->resize * $xypre[1]/$this->escala, $this->resize * $xy[0]/$this->escala, $this->resize * $xy[1]/$this->escala, $color, $this->resize * $thick);
			}
		}
	}
	function drawPolygon(){
		//sacar la informacion asociada a la imagen a dibujar
		$query = "SELECT `text_puntos` FROM `".$this->layer["table_name"]."`
		WHERE MBRIntersects(mysql_puntos, geomfromtext('".$this->image["mysql_puntos_text"]."') ) = '1'
		order by `size` desc";
		$poligonos = mysql_query($query) or die($query);
		while($poligono = mysql_fetch_array($poligonos)){
			//split en partes
			$partes = explode("z", $poligono["text_puntos"]);
			for($k=0; $k<count($partes); $k++){
				$puntos = explode(",", $partes[$k]);
				$puntosPolygon = array();
				for($n=0; $n<count($puntos); $n++){
					//convertimos cada punto en un punto referenciado a la imagen
					$xy = explode(" ", $puntos[$n]);
					$xy[0] -= $this->xmin + ($this->square[0])*$this->squareSize*$this->escala;
					$xy[1] -= $this->ymin + ($this->square[1])*$this->squareSize*$this->escala;
					array_push($puntosPolygon, $this->resize * $xy[0]/$this->escala);
					array_push($puntosPolygon, $this->resize * $xy[1]/$this->escala);
				}
				//color transparente para el fill
				$this->imageCanvas->drawFilledPolygon($puntosPolygon, $this->layer["colorBorder"], "FFFFFF7f");
			}
		}
		//de ser especificado dibujar un punto abajo del label de este poligono (caso areas_urbanas, etc)
		if($this->layer["drawPointInCenter"] == 1 && $this->nivel >= $this->layer["drawPointInCenterFromNivel"] && $this->nivel <= $this->layer["drawPointInCenterToNivel"]){
			$query = "SELECT X(centroid(envelope(mysql_puntos))) as x, Y(centroid(envelope(mysql_puntos))) as y
			from `".$this->layer["table_name"]."`
			join (
				select `objeto_clave` from `labels` where `clean` = '2' and 
				`nivel` = ".$this->nivel." and 
				`table_name` = '".$this->layer["table_name"]."'
			) `labels` on labels.objeto_clave = `".$this->layer["table_name"]."`.clave";
			$xy = mysql_query($query) or die($query);
			//dibujar puntos de areas urbanas en vez de su poligono
			$color0 = '66666600';
			$color1 = 'BE0C2C00';
			$color2 = 'E7304F00';
			while ($p = mysql_fetch_array($xy)){
				$x = $p["x"] - $this->xmin - $this->square[0]*$this->squareSize*$this->escala;
				$y = $p["y"] - $this->ymin - $this->square[1]*$this->squareSize*$this->escala;
				$this->imageCanvas->drawFilledCircle($this->resize * $x/$this->escala,$this->resize * $y/$this->escala, $this->resize*4, $color0);
				$this->imageCanvas->drawFilledCircle($this->resize * $x/$this->escala,$this->resize * $y/$this->escala, $this->resize*3, $color1);
				$this->imageCanvas->drawFilledCircle($this->resize * $x/$this->escala,$this->resize * $y/$this->escala, $this->resize*1.5, $color2);
			}
			mysql_free_result($poligonos);
			mysql_free_result($xy);
		}
	}

}
?>