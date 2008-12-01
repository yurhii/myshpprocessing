<?PHP
class CreateLabels extends MapWareCore {
	var $table_name;
	var $class;
	//esta variable indica que las referencias con imagenes se crean de la tabla labels i.e. en labels_por_imagen en vez
	//de usar table_name para las referencias.
	var $nonStandardReferenceTable = "labels";
	//esta variable indica si queremos que el centro del objeto quede entre los dos reinglones del label o no
	//esta pensado apra el caso de labels de areas urbanas en donde hay un punto dibujado el cual no se quiere encimar
	//con el label
	var $hayDesface;
	var $nivel;
	//
	var $labelOrderValue;
	//nos dice en el caso de paths cual es el campo que define su catalogo principal, como viene de un catalogo acaba en _id
	var $pathCampoTipo;
	//variable que nos dice cual es el current index a insertar en la base de datos
	var $nextAutoIndex;
	function CreateLabels($tableName, $nivelNumber = NULL){
		$this->openMySQLConn();
		$this->nivel = $nivelNumber;
		if(!is_numeric($this->nivel)){
			die("no hay nivel especificado");
		}
		//definir variables y limites globales
		$this->defineMapWareBounds();
		//guardar el nombre de la tabla a etiquetar
		$this->table_name = $tableName;
		//sacar el next Auto index
		//extraer el next autindex que es el que se le va a asignar al momento del insert
		$next_autoindex = "SELECT AUTO_INCREMENT FROM  `information_schema`.`TABLES` 
		WHERE  `TABLE_NAME` =  'labels'
		AND  `TABLE_SCHEMA` =  '".$this->dataBase."'";
		$res = mysql_fetch_array(mysql_query($next_autoindex)) or die($next_autoindex);
		$this->nextAutoIndex = $res["AUTO_INCREMENT"];
		//sacar la clase del shp que corresponde a esta tabla
		$query = "select * from `tables` 
		join tables__attributes on tables__attributes.table_name = tables.table_name
		where tables.`table_name` = '".$this->table_name."'";
		$res = mysql_fetch_array(mysql_query($query)) or die($query);
		$this->class = $res["class"];
		$this->labelOrderValue = $res["labelOrder"];
		$catalogos = explode(",", $res["catalogos"]);
		$this->pathCampoTipo = $catalogos[0]."_id";
		//definir si los labels van sobre el centro o desfasados para que el centro quede libre (caso areas_urbanas)
		$this->hayDesface = ($res["drawPointInCenter"] != '0');
		switch($this->class){
			case "RecordPolyLine":
				$this->labelPath();
				break;
			case "RecordPolygon":
				$this->labelPolygon();
				break;
			case "RecordPoint":
				
				break;
		}
	}
	function labelPath(){
		$query = "SELECT text_puntos, GeometryType(mysql_puntos) as GeometryType,
		clave, nombre, length(mysql_puntos) as longitud, paths_tipos.drawOrder, 
		NumGeometries(mysql_puntos) as numGeometries
		FROM `".$this->table_name."`
		JOIN paths_tipos ON paths_tipos.tipo_id = `".$this->table_name."`.`".$this->pathCampoTipo."`
		AND paths_tipos.table_name =  '".$this->table_name."'
		JOIN  `paths_tipos__length__restrictions` AS l_r ON l_r.path_tipo_id = paths_tipos.id
		AND l_r.nivel =  '".$this->nivel."'
		WHERE nombre !=  ''
		AND length(mysql_puntos) > longitud_minima";
		//AND labeled < '".$this->nivel."'";
		$paths = mysql_query($query) or die($query);
		while($row = mysql_fetch_array($paths)){
			//poligono en mayusculas y minusculas
			$nombre = $this->toUpperLower($row["nombre"]);
			//datos del path
			$longitud = 1*$row["longitud"];
			//procesamos los mysql_puntos
			$partes = explode("z", $row["text_puntos"]);
			//vamos a poner el label en el segmento mayor
			$segmento_mayor = array();
			$longitud_mayor = 0;
			for($k=0; $k<count($partes); $k++){
				$puntos = explode(",", $partes[$k]);
				for($n=1; $n<count($puntos); $n++){
					$p0  = explode(" ", $puntos[$n-1]);
					$p1 = explode(" ", $puntos[$n]);
					$longitud_segmento = $this->distancia($p0[0], $p0[1], $p1[0], $p1[1]); 
					if($longitud_segmento >= $longitud_mayor){
						$longitud_mayor = $longitud_segmento;
						$segmento_mayor["p0"] = $p0;
						$segmento_mayor["p1"] = $p1;
						$segmento_mayor["k"] = $k;
						$segmento_mayor["n"] = $n;
					}
				}
			}
			//definir la caja para el segmento mayor
			$box = $this->getPathLabelBox($segmento_mayor["p0"][0], $segmento_mayor["p0"][1], $segmento_mayor["p1"][0], $segmento_mayor["p1"][1], $nombre);
			$labelValue = $this->labelOrderValue*10000000 + $row["drawOrder"]*100000 - $longitud - $longitud_mayor;
			$insertado = $this->insertBox($box, $row, $labelValue, $segmento_mayor["k"], $segmento_mayor["n"]);
			//precisar que el label de esta calle ya fue guardado
			//imperativo para que el proceso no se trave!!!!!!!!!!!!!!
			/*$query = "update `".$this->table_name."` 
			set labeled = '".$this->nivel."' 
			where clave = '".$row["clave"]."'";
			mysql_query($query) or die($query);*/
		}
	}
	function getPathLabelBox($xx1, $yy1, $xx2, $yy2, $text){
		//sabemos de izquierda a derecha y de arriba a abajo como estan los puntos
		if(min($xx1, $xx2) == $xx1){
			$x1 = $xx1;
			$y1 = $yy1;
			$x2 = $xx2;
			$y2 = $yy2;
		}else{
			$x1 = $xx2;
			$y1 = $yy2;
			$x2 = $xx1;
			$y2 = $yy1;
		}
		//definimos el angulo
		$cociente = (($x2-$x1) == 0) ? .00000001: ($x2-$x1);
		$tan = ($y2-$y1)/$cociente;
		$angRadianes = abs(atan($tan));
		//vemos en que cuadrante se encuentra nuestro path a etiquetar
		if($x1 <= $x2 && $y1 <= $y2){
			$cuadrante = 1;
			$angRadianes = -1*$angRadianes;
		}
		if($x1 <= $x2 && $y1 > $y2){
			$cuadrante = 4;
		}
		//pasar angulo a grados
		$angGrados = $angRadianes*180/pi();
		//ajustamos la caja generada por imagettfbbox
		$box = $this->moverAlOrigen(imagettfbbox($this->getFontsize($this->nivel), $angGrados, $this->font, $text));
		//distancia maxima en x,y de la caja
		$xStringLength = $this->distancia($box[0], 0, $box[2], 0);
		$yStringLength = $this->distancia(0, $box[1], 0, $box[3]);
		//como $x1 < $x2
		//esto centra sobre el path a la caja, sin importar quien es mas grande
		//la $x y $y representan el punto desde el cual la caja debe empezar a dibujarse
		$x = $x1 + .5 * ($this->distancia($x1, 0, $x2, 0) - $xStringLength) + .5 * sin($angRadianes)*$this->distancia($box[0], $box[1], $box[6], $box[7]);
		if(min($y1, $y2) == $y1){
			$y = $y1 + .5 * ($this->distancia(0, $y1, 0, $y2) - $yStringLength) + .5 * cos($angRadianes)*$this->distancia($box[0], $box[1], $box[6], $box[7]);
		}else{
			$y = $y1 - .5 * ($this->distancia(0, $y1, 0, $y2) - $yStringLength) + .5 * cos($angRadianes)*$this->distancia($box[0], $box[1], $box[6], $box[7]);
		}
		//poscionar la caja en las coordenadas x, y
		$box[0] += $x;
		$box[1] += $y;
		$box[2] += $x;
		$box[3] += $y;
		$box[4] += $x;
		$box[5] += $y;
		$box[6] += $x;
		$box[7] += $y;
		$box["x"] = $x;
		$box["y"] = $y;
		$box["angle"] = $angGrados;
		$box["text"] = $text;
		return $box;
	}
	function labelPolygon(){
		$query = "select clave, nombre, X(centroid(envelope(mysql_puntos))) as centroX, Y(centroid(envelope(mysql_puntos))) as centroY, size
		from `".$this->table_name."`
		WHERE nombre != '' ";
		//and labeled < '".$this->nivel."'";
		$poligonos = mysql_query($query) or die($query);
		while($row = mysql_fetch_array($poligonos)){
			//poligono en mayusculas y minusculas
			$nombre = $this->toUpperLower($this->limpiar($row["nombre"]));
			//coordenadas x, y del centro del poligono
			$xy = array($row["centroX"], $row["centroY"]);
			$size = $row["size"];
			//si tiene varias palabras (mas de dos) lo separamos en dos reinglones
			$words = explode(" ", $nombre);
			$words = array_chunk($words, ceil(count($words)/2));
			//texto de ambos reinglones
			$text1 = implode(" ", $words[0]);
			$text2 = (isset($words[1])) ? implode(" ", $words[1]) : "";
			//cajas de ambos reinglones con angulo cero
			$box1 = $this->moverAlOrigen(imagettfbbox($this->getFontsize($this->nivel), 0, $this->font, $text1));
			$box2 = $this->moverAlOrigen(imagettfbbox($this->getFontsize($this->nivel), 0, $this->font, $text2));
			//deface es la altura de la caja
			$desface = ($text2 != "") ? 2*abs($box1[7]-$box1[1]) : abs($box1[7]-$box1[1]);
			//la caja uno se sube de forma que su parte inferior toque el centro del poligono
			$box1["x"] = $xy[0] - abs($box1[2]-$box1[0])/2;
			$box1["y"] = $xy[1];
			if($this->hayDesface){
				$box1["y"] -= $desface;
			}
			$box1["angle"] = 0;
			$box1["text"] = $text1;
			//se actualizan sus coordenadas con respecto al centro del poligono
			for($k=0; $k<8; $k++){
				$box1[$k] += ($k % 2 == 0) ? $box1["x"] : $box1["y"];
			}
			//mismo procedimiento para la caja 2
			//pero la caja dos se baja para que su parte superior toque el centro del poligono
			$box2["x"] = $xy[0] - abs($box2[2]-$box2[0])/2;
			$box2["y"] = $xy[1] + abs($box2[7] - $box2[1]);
			if($this->hayDesface){
				$box2["y"] -= $desface;
			}
			$box2["angle"] = 0;
			$box2["text"] = $text2;
			//se actualizan sus coordenadas con respecto al centro del poligono
			for($k=0; $k<8; $k++){
				$box2[$k] += ($k % 2 == 0) ? $box2["x"] : $box2["y"];
			}
			//esta es una clave para ordenar elementos al momento de sacarlos en el clean
			//depende del tipo y el size
			$labelValue = $this->labelOrderValue*10000000 - $row["size"];
			//insertar ambas cajas
			$poligon_text1 = $this->insertBox($box1, $row, $labelValue, $box2);
			//precisar que el label de esta calle ya fue guardado
			//imperativo para que el proceso no se trave!!!!!!!!!!!!!!
			/*$query = "update `".$this->table_name."` 
			set labeled = '".$this->nivel."' 
			where clave = '".$row["clave"]."'";
			mysql_query($query) or die($query);*/
		}
	}
	function insertBox($box1, $row, $labelValue, $box2 = array(), $parte = 0, $indice = 0){
		$polygon1 = "polygon(($box1[0] $box1[1],$box1[2] $box1[3],$box1[4] $box1[5],$box1[6] $box1[7],$box1[0] $box1[1]))";
		$p1xmax = ceil(max($box1[0], $box1[2], $box1[4], $box1[6]));
		$p1xmin = floor(min($box1[0], $box1[2], $box1[4], $box1[6]));
		$p1ymax = ceil(max($box1[1], $box1[3], $box1[5], $box1[7]));
		$p1ymin = floor(min($box1[1], $box1[3], $box1[5], $box1[7]));
		//si hay segunda caja
		if(isset($box2["text"])){
			$polygon2 = "polygon(($box2[0] $box2[1],$box2[2] $box2[3],$box2[4] $box2[5],$box2[6] $box2[7],$box2[0] $box2[1]))";
			$p2xmax = ceil(max($box2[0], $box2[2], $box2[4], $box2[6]));
			$p2xmin = floor(min($box2[0], $box2[2], $box2[4], $box2[6]));
			$p2ymax = ceil(max($box2[1], $box2[3], $box2[5], $box2[7]));
			$p2ymin = floor(min($box2[1], $box2[3], $box2[5], $box2[7]));
		}else{
			//los defimos para que no haya error abajo a tomar el max y min de los dos
			$p2xmax = $p1xmax;
			$p2xmin = $p1xmin;
			$p2ymax = $p1ymax;
			$p2ymin = $p1ymin;
		}
		//si hay dos cajas con texto guardamos la union de ambas
		if(isset($box2["text"]) && $box2["text"] != ""){
			$polygon = "envelope(geomfromtext('GeometryCollection($polygon1,$polygon2)'))";
		}else{
			$polygon = "geomfromtext('$polygon1')";
		}
		//insert label asociado a box1
		$query = "INSERT INTO  `labels` 
		( `clave`, `x` ,  `y`, `xmax`, `xmin`, `ymax`, `ymin`,  `angle` ,  `text` ,  `size`,
		`nivel`, `table_name`, `objeto_clave`, `identifier`, `labelValue`, `mysql_puntos`) 
		VALUES 
		( ".$this->nextAutoIndex.", '".$box1["x"]."', '".$box1["y"]."', $p1xmax, $p1xmin, $p1ymax, $p1ymin, '".$box1["angle"]."', '".$box1["text"]."', '".$this->getFontsize($this->nivel)."', 
		'".$this->nivel."', '".$this->table_name."', '".$row["clave"]."', '".$parte."_".$indice."', '$labelValue', $polygon  )";
		//vemos si se llevo a cabo un insert o un update
		$was_insert = mysql_query($query);
		if(!$was_insert){
			$query = "update labels set clean = '1' where clave = '".$this->nextAutoIndex."'";
			mysql_query($query) or die($query);
		}
		if(isset($box2["text"]) && $box2["text"] != ""){
			//insert label asociado a box2
			$query = "INSERT INTO  `labels` 
			( `clave`, `x` ,  `y` ,  `xmax`, `xmin`, `ymax`, `ymin`, `angle` ,  `text` ,  `size`,
			`nivel`, `table_name`, `objeto_clave`, `identifier`, `labelValue`, `mysql_puntos`) 
			VALUES 
			( ".$this->nextAutoIndex.", '".$box2["x"]."', '".$box2["y"]."', $p2xmax, $p2xmin, $p2ymax, $p2ymin, '".$box2["angle"]."', '".$box2["text"]."', '".$this->getFontsize($this->nivel)."', 
			'".$this->nivel."', '".$this->table_name."', '".$row["clave"]."', '".$parte."_".$indice."', '$labelValue', $polygon  )
			ON DUPLICATE KEY UPDATE clean = '1'";
			mysql_query($query) or die($query);
			//aumentamos el autoindex
		}
		//crear y guardar la referencia a las imagenes
		//sacamos las dimensiones de los bounds de la caja para crear las imagenes asociadas
		$upperLeft = $this->getCuadroFromPointAtNivel(min($p1xmin, $p2xmin), min($p1ymin, $p2ymin), $this->nivel);
		$lowerRight = $this->getCuadroFromPointAtNivel(max($p1xmax, $p2xmax), max($p1ymax, $p2ymax), $this->nivel);
		if($was_insert){
			$this->crearGuardarImagenes($upperLeft, $lowerRight, $this->nivel, $this->nextAutoIndex, "labels", 0);
			//aumentamos el autoindex
			if(isset($box2["text"]) && $box2["text"] != ""){
				$this->nextAutoIndex = $this->nextAutoIndex + 2;
			}else{
				$this->nextAutoIndex = $this->nextAutoIndex + 1;
			}
		}
	}
	function moverAlOrigen($box){
		if($box == false){
			die("no box");
		}
		$xchange = $box[0];
		$ychange = $box[1];
		for($k=0; $k<8; $k++){
			$box[$k] -= ($k % 2 == 0) ? $xchange : $ychange;
		}
		return $box;
	}
}
?>