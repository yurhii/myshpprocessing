<?PHP	
class NSEProcessing extends MapWareCore{
	
	var $imagenes_por_request = 200;
	var $cpu;
	
	var $bufferSize;
	var $nivelInicial;
	var $nivelFinal;
	
	var $layer;
	var $image;
	var $imageCanvas;
	var $square;
	
	var $nivel;
	
	function NSEProcessing($nivel = 1, $cpuNumber = 1){
		$this->openMySQLConn();
		$this->defineMapWareBounds();
		$this->nivel = $nivel;
		$this->cpu = $cpuNumber;
	}
	
	function startProcessingAllNoMatchToOurSateliteAssets(){
		$this->actualizarEscalaPorNivel($this->nivel);
		
		$query = "select imagenes.*, astext(imagenes.mysql_puntos) as mysql_puntos_text
		from imagenes
		where imagenes.nivel = '$this->nivel'
		and mapa_exists in ('0','1') 
		AND `cpu` = '".$this->cpu."'
		limit $this->imagenes_por_request";
		$res = mysql_query($query) or die($query);
		$this->processImages($res);
		//
		return (mysql_num_rows($res) != 0);
	}
	
	
	function processImages($res){
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
			
			$this->drawNSE();
			
			$image200px = imagecreatetruecolor(200, 200);
			//transparencia de esta imagen
			//imagealphablending($image200px, false);
			imagesavealpha($image200px, true);
			$transparent = imagecolorallocatealpha($image200px, 0, 0, 0, 127);
			imagefill($image200px, 0, 0, $transparent);
			//copiar imagen en esta
			imagecopyresampled($image200px, $this->imageCanvas->image, 0, 0, 0, 0, 200, 200, 200*$this->resize, 200*$this->resize);
			//definir la ruta del archivo
			$archivo = "temporales/nse".$this->cpu.".png";
			//
			imagepng($image200px, $archivo, 3) or die("no generation of image");
			chmod($archivo, 0777);
			//tomar la informacion del archivo e ingresarla a la base de datos
			$file = mysql_real_escape_string(file_get_contents($archivo));
			//guardar en base de datos que la imagen ya fue dibujada
			$query = "UPDATE `imagenes_nse`
			SET `hibrido` = '$file', `hibrido_exists` = '1'
			WHERE `i` = '".$this->image["i"]."' and `j` = '".$this->image["j"]."' 
			and `nivel` = '".$this->image["nivel"]."'";
			
			mysql_query($query) or die($query);
		}
	}
	
	
	function drawNSE(){
		//sacar la informacion asociada a la imagen a dibujar
		$query = "SELECT `text_puntos` 
		FROM `nse`
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
	}

}
?>