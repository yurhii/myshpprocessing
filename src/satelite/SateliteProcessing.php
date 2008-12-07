<?PHP	
class SateliteProcessing extends MapWareCore{
	var $shp_data;
	var $dbf_data;
	var $resources;
	var $shp;
	function SateliteProcessing(){
		$this->openMySQLConn();
		$this->defineMapWareBounds();
		//extraer de la tabla de satelite_imagenes los insumos a generar
		$query = "select * from satelite_originales 
		where generada = 0
		order by hd asc, imagename asc limit 1";
		$res = mysql_query($query) or die($query);
		$this->resources = mysql_fetch_array($res) or die("Terminado<br />");
		$this->shp = new ShapeFile(SATELITE_RESOURCES.$this->resources["folder"]."/".$this->resources["filename"].".shp") or die("no shp");
		// along this file the class will use file.shx and file.dbf
		// Let's see all the records:
		$this->shp_data = $this->shp->records[$this->resources["indice"]]->shp_data;
		$this->dbf_data = $this->shp->records[$this->resources["indice"]]->dbf_data;
	}
	function preview(){
		echo "<pre>";
		print_r( $this->shp->records);
		echo "</pre>";
	}
	function startProcessing(){
		//cuadro representando a la imagen de satelite
		$aPuntos = array();
		//lower left
		$punto = array($this->convertXFromShpToMapWare($this->shp_data["xmin"]), $this->convertYFromShpToMapWare($this->shp_data["ymax"]) );
		array_push($aPuntos, $punto);
		//lower right
		$punto = array($this->convertXFromShpToMapWare($this->shp_data["xmax"]), $this->convertYFromShpToMapWare($this->shp_data["ymax"]) );
		array_push($aPuntos, $punto);
		//upper right
		$punto = array($this->convertXFromShpToMapWare($this->shp_data["xmax"]), $this->convertYFromShpToMapWare($this->shp_data["ymin"]) );
		array_push($aPuntos, $punto);
		//upper left
		$punto = array($this->convertXFromShpToMapWare($this->shp_data["xmin"]), $this->convertYFromShpToMapWare($this->shp_data["ymin"]) );
		array_push($aPuntos, $punto);
		
		//cargar laimagen
		$imagePath = SATELITE_RESOURCES.$this->resources["folder"]."/".$this->resources["imagename"].".jpg";
		$image_original = imagecreatefromjpeg($imagePath) or die("no image defined");
		//definir los niveles en los cuales se va a generar esta imagen de satelite
		$nivelInicial = ($this->resources["hd"] == 1) ? 7 : 1;
		$nivelFinal = ($this->resources["hd"] == 1) ? 14 : 9;
		//loop sobre los niveles
		for($nivel=$nivelInicial; $nivel<=$nivelFinal; $nivel++){
			//actualizar datos globales por nivel
			$this->actualizarEscalaPorNivel($nivel);
			//
			$upperLeft = $this->getCuadroFromPoint($aPuntos[0][0], $aPuntos[0][1]);
			$lowerRight = $this->getCuadroFromPoint($aPuntos[2][0], $aPuntos[2][1]);
			//
			//info imagen sat
			$info = getimagesize($imagePath);
			//width en latitud y longitud de la imagen de satelite
			$satWidth = $aPuntos[1][0] - $aPuntos[0][0];
			$satHeight = $aPuntos[2][1] - $aPuntos[1][1];
			//width y height que debe tener la imagen de satelite en pixeles
			$pixelWidthSat = $satWidth / $this->escala;
			$pixelHeightSat = $satHeight / $this->escala;
			//escala correspondiente entre el pixelaje que debe tener y el real de la imagen de satelite
			$escalaImagenSatelite = $info[0] / $pixelWidthSat;
			//copio la imagen al tamaño que le corresponde en este nivel para evitar desfaces
			$image = imagecreatetruecolor(round($info[0]/$escalaImagenSatelite), round($info[1]/$escalaImagenSatelite));
			imagecopyresampled($image, $image_original, 0, 0, 0, 0, round($info[0]/$escalaImagenSatelite), round($info[1]/$escalaImagenSatelite), $info[0], $info[1]) or die("no se pudo generar copia a nivel");
			//
			echo "************************Nivel".$nivel."<br />";
			//Si la imagen es de hd entonces se crea un padding (bufferSize) alrededor con imagen de satelite de bajar resolucion
			$bufferSize = ($this->resources["hd"] == 1 && $nivel >= 10) ? 4 : 0;
			//creamos y guardamos las imagenes en la base de datos
			$this->crearGuardarImagenes($upperLeft, $lowerRight, $nivel, $this->resources["clave"], "satelite_originales", $bufferSize, "satelite");
			//para todos los cuadros que toca la imagen de satelite
			for($i=$upperLeft[0] - $bufferSize; $i<=$lowerRight[0] + $bufferSize; $i++){
				for($j=$upperLeft[1] - $bufferSize; $j<=$lowerRight[1] + $bufferSize; $j++){
					//cuadro
					$square = array($i, $j);
					//ver si ya existe una imagen en estas coordenadas
					$query = "select * from imagenes
					where satelite_exists = '1' and i = '$i' and j = '$j' and nivel = '$nivel'";
					$exists = mysql_query($query) or die($query);
					//crear nueva imagen
					if( mysql_num_rows($exists) != 0 ){
						//en caso de que ya existiera antes una imagen de satelite asociada
						$newImage = imagecreatefromjpeg("http://localhost/mapware/Site/images/satelite.jpg.php?clave=".$i."_".$j."_".$nivel);
					}else{
						$newImage = imagecreatetruecolor(200, 200);
						//de ser hd copiar sobre una imagen de lowDef ampliada
						if($this->resources["hd"] == 1){
							// copiamos la imagen de sat de baja resolucion en esta imagen para que forme parte del buffer
							// puede que sea cubierta por la imagen de hd en donde esta se encuentre
							$lowDef_i = ceil( ($i+1)/pow(2, $nivel-8) ) - 1;
							$lowDef_j = ceil( ($j+1)/pow(2, $nivel-8) ) - 1;
							$query = "select xmin, ymin from imagenes where i = $lowDef_i and j = $lowDef_j and nivel = 8";
							$lowDef = mysql_fetch_array(mysql_query($query)) or die($query);
							//que tantas veces es mas grande la newImage que la de baja resolucion que se va a sacar
							$escala_ld = pow(2, $nivel-8);	
							//sacar la imagen que se va a usar
							$image_ld = imagecreatefromjpeg("http://localhost/mapware/Site/images/satelite.jpg.php?clave=".$lowDef_i."_".$lowDef_j."_8") or die("no image ld");
							//
							$destX_ld = ($lowDef["xmin"] - $this->xmin - $square[0]*$this->squareSize*$this->escala) / $this->escala;
							$destY_ld = ($lowDef["ymin"] - $this->ymin - $square[1]*$this->squareSize*$this->escala) / $this->escala;
							//			
							$sourceX_ld = -1*$destX_ld;
							$sourceY_ld = -1*$destY_ld;
							imagecopyresampled($newImage, $image_ld, 0, 0, round($sourceX_ld/$escala_ld), round($sourceY_ld/$escala_ld), 200, 200, round(200/$escala_ld), round(200/$escala_ld)) or die("no copy resampled");
						}
					}
					//
					//
					//lugar que representa la esquina superior izqierda de la imagen de satelite dentro del cuadro, igual que en generar imagenes simplemente referenciamos una coordenada de latitud, longitud a la imagen de 200 px a dibujar
					$destX = $aPuntos[0][0] - ($this->xmin + $square[0]*$this->squareSize*$this->escala);
					$destX = round($destX/$this->escala);
					$destY = $aPuntos[0][1] - ($this->ymin + $square[1]*$this->squareSize*$this->escala);
					$destY = round($destY/$this->escala);
					//
					//sourceX y sourceY representan la esquina superior izquierda correspondiente a la imagen a  
					//dibujar dentro de la imagen de satelite completa
					$sourceX = round(-1*$destX);
					$sourceY = round(-1*$destY);
					if($destX > 0){
						//ver cuanto hay que copiar, solo lo suficiente para llenar la imagen newImage
						//como sourceX es negativo ya que destX positivo
						$Width = min(round(210 + $sourceX), round($info[0]/$escalaImagenSatelite));
						//
						if($destY > 0){
							$Height = min(round(210 + $sourceY), round($info[1]/$escalaImagenSatelite));
							//copiar el pedazo de imagen correspondiente
							imagecopy($newImage, $image, $destX, $destY, 0, 0, $Width, $Height) or die("no image copy");
						}else{
							$Height= min(210, round($info[1]/$escalaImagenSatelite- $sourceY));
							//copiar el pedazo de imagen correspondiente
							imagecopy($newImage, $image, $destX, 0, 0, $sourceY, $Width, $Height) or die("no image copy");
						}
					}else{
						//ver cuanto hay que coipar, solo lo suficiente para llenar la imagen newImage no mas
						$Width = min(210, round($info[0]/$escalaImagenSatelite - $sourceX));
						//
						if($destY > 0){
							$Height = min(round(210 + $sourceY), round($info[1]/$escalaImagenSatelite));
							//copiar el pedazo correspondiente
							imagecopy($newImage, $image, 0, $destY, $sourceX, 0, $Width, $Height) or die("no image copy");
						}else{
							$Height= min(210, round($info[1]/$escalaImagenSatelite- $sourceY));
							//copiar el pedazo correspondiente
							imagecopy($newImage, $image, 0, 0, $sourceX, $sourceY, $Width, $Height) or die("no image copy");
						}
					}
					
					//set compression level = 3
					$archivo = "temporales/satelite_temporal.jpg";
					imagejpeg($newImage, $archivo, 90);
					chmod($archivo, 0777);
					//tomar la informacion del archivo e ingresarla a la base de datos
					$file = mysql_real_escape_string(file_get_contents($archivo));
					//guardar en base de datos que la imagen ya fue dibujada
					$query = "UPDATE `imagenes`
					SET `satelite` = '$file', `satelite_exists` = '1'
					WHERE `i` = '".$i."' and `j` = '".$j."' 
					and `nivel` = '".$nivel."'";
					mysql_query($query) or die($query);
				}
			}
		}//cierre del for de niveles
		//
		//actualizar el campo de generado para no volver a usar esta imagen
		$query = "update satelite_originales set generada = 1 where clave = '".$this->resources["clave"]."'";
		mysql_query($query) or die($query);
	}
	
	function insertLowDefSateliteOriginals(){
		//cargar los shape files
		$shp = new ShapeFile(SATELITE_RESOURCES."low_def_nacional/gradicula 2008.shp") or die("no gradicula shp"); // along this file the class will use file.shx and file.dbf
		echo "insertLowDefSateliteOriginals</br>";
		$shp->fetchAllRecords();
		for($i=0; $i<count($shp->records); $i++){
			$dbf_data = $shp->records[$i]->dbf_data;
			$query = "insert into satelite_originales 
			(nombre, folder, filename, imagename, hd, indice)
			values
			('low_def_nacional".$this->limpiar($dbf_data["IMG"])."', 'low_def_nacional', 'gradicula 2008', 'img_".str_replace("-", "_", $this->limpiar($dbf_data["IMG"]))."', 0, $i)";
			mysql_query($query) or die($query);
		}
	}
}
?>	
