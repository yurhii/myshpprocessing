<?PHP
define("INFINITO", 10000000000);

define("SATELITE_RESOURCES", "../../satelite_resources/");

define("SHAPE_FILES", "../../shapeFiles/");

include_once("conexion/ConexionMySQL.php");
//shp
include_once("shp/ProcessShapeFile.php");

include_once("labeling/CreateLabels.php"); 
include_once("labeling/CleanLabels.php"); 
//
include_once("geometric/LabelBox.php");
//
include_once("drawing/DrawImage.php");
//
include_once("satelite/SateliteProcessing.php");
include_once("hibrido/HibridoProcessing.php");

class MapWareCore{
	var $engine = "MyISAM";
	var $dataBase = "MapWareProcessing";
	/************************************************Variables***********************************/
	var $nivelMaximoMapa = 13;
	var $nivelMaximoSatelite = 14;
	//este indicador nos dice hasta que nivel tenemos cubierto todo el pais con imagenes
	//despues de este solo se dibujan las zonas que tengan informacion cartografica
	var $nivelMaximoDeCoberturaTotal = 9;
	//variable que indica cual es el nivel al cual esta actualizada la escala en este momento 
	var $currentNivel = 0;
	//dimensiones del pais en coordenadas mapWare
	var $xmin;
	var $xmax;
	var $ymin;
	var $ymax;
	var $width;
	var $height;
	//esta variable nos indica de que tamaño generamos las imagenes para aumentar la resolucion, el doble es bueno
	var $resize = 2;
	//dimensiones de una imagen en mapWare
	var $squareSize = 200;
	//estos datos se actualizan con la funcion actualizarDatosPorNivel
	//pixel dimensions del pais por nivel
	var $pixelWidth = 0;
	var $pixelHeight = 0;
	var $escala = 0;
	//
	//url de la font font a usar en el dibujado
	var $font = "../fonts/ArialBold.ttf";
	//conexion a mysql
	var $conn;
	/************************************************Funciones***********************************/
	/************Mysql Handling**********/
	function mapWareCore(){
		$this->openMySQLConn();
		$query = "show tables like 'imagenes'";
		$img_exists = mysql_num_rows(mysql_query($query));
		if($img_exists == 0){
			$this->createProcessingTables();
		}
	}
	function openMySQLConn(){
		//get Mysql connection a la base de datos mapWareCartographicFrameWork
		$this->conn = new ConexionMySQL($this->dataBase);
	}
	function closeMySQLConn(){
		mysql_close($this->conn->dtb);
	}
	/*************Miscelaneas*********/
	function getFontsize($nivel){
		$fontsize = ($nivel > 10) ? 9 * pow(2, 13-$nivel) : 8 * pow(2, 13-$nivel);
		return $fontsize;
	}
	/************Strings************/
	function convertXFromShpToMapWare($number){
		$number *= 100000;//conversion a metros
		$number = round($number, 2);
		return $number;
	}
	function convertYFromShpToMapWare($number){
		$number *= 100000;//conversion a metros
		$number = round($number, 2);
		return -1*$number;
	}
	function limpiar($str){
		if(strlen($str) == 1){
			return $str;
		}
		while (ord($str) == 32 || ord($str) == 10){
			$str =  substr($str, 1, strlen($str));
		}
		while (strrpos($str, chr(32)) == strlen($str)-1 || strrpos($str, chr(10)) == strlen($str)-1){
			$str =  substr($str, 0, strlen($str)-1);
		}
		//cambiar comillas para que no tenga bronca con lo querys
		$str = str_replace("'", "\\'", $str); 
		return $str;
	}
	function toUpperLower($str){
		$words = explode(" ", $str);
		for($n=0; $n < count($words); $n++){
			$words[$n] = strtoupper(substr($words[$n], 0, 1)).strtolower(substr($words[$n], 1, strlen($words[$n])));
		}
		return implode(" ", $words);
	}
	function changeLatin($txt){
		$txt = str_replace("Á", "A", $txt);
		$txt = str_replace("ü", "A", $txt);
		//Cambiar caracteres latinos
		$latinos = array('/á/', '/é/', '/ó/', '/ú/', '/ñ/', '/í/');
		$noLatinos = array('A', 'E', 'O', 'U', '_', 'I');
		$txt = preg_replace($latinos, $noLatinos, $txt);
		//
		//acentos
		$latinos = array('/Ã¡/', '/Ã©/', '/Ã³/', '/Ãº/', '/Ã±/', '/Ã/');
		$noLatinos = array('A', 'E', 'O', 'U', '_', 'I');
		//
		$txt = str_replace(utf8_encode("á"), "A", $txt);
		$txt = str_replace(utf8_encode("é"), "E", $txt);
		$txt = str_replace(utf8_encode("í"), "I", $txt);
		$txt = str_replace(utf8_encode("ó"), "O", $txt);
		$txt = str_replace(utf8_encode("ú"), "U", $txt);
		$txt = str_replace(utf8_encode("ñ"), "_", $txt);
		//
		$txt = str_replace("á", "A", $txt);
		$txt = str_replace("é", "E", $txt);
		$txt = str_replace("í", "I", $txt);
		$txt = str_replace("ó", "O", $txt);
		$txt = str_replace("ú", "U", $txt);
		$txt = str_replace("ñ", "_", $txt);
		//
		$txt = preg_replace($latinos, $noLatinos, $txt);
		$txt = strtoupper($txt);
		return $txt;
	}
	function relpaceFromDictionary($txt){
		$txt=" ".$txt." ";
		$txt = strtoupper($txt);
		//latinos
		$txt = $this->changeLatin($txt);
		//remplazar txts sin sentido en la busqueda
		$txt = strtolower($txt);
		$query ="select * from omiciones";
		$omiciones = mysql_query($query) or die($query);
		for($j=0; $j<mysql_num_rows($omiciones); $j++){
			$txt = str_replace(" ".mysql_result($omiciones, $j, 'palabra')." ", " ", $txt);
		}
		//cambiar suaves por s, fuertes por k, cambiar ll por y, cambiar v por b, cambiar ge, gi, por j
		$query ="select * from sonidos_iguales";
		$sonidos = mysql_query($query)  or die($query);
		for($j=0; $j<mysql_num_rows($sonidos); $j++){
			$txt = str_replace(mysql_result($sonidos, $j, 'search'), mysql_result($sonidos, $j, 'replace'), $txt);
		}
		//borrar h muda
		$txt = str_replace("ch", "####", $txt);
		$txt = str_replace("h", "", $txt);
		$txt = str_replace("####", "ch", $txt); 
		//
		$txt = strtoupper($txt);
		return $this->limpiar(substr($txt, 1, strlen($txt)-2));
	}
	/***********Mathematic***********/
	function distancia($x1, $y1, $x2, $y2){
		return sqrt(pow($x1-$x2, 2) + pow($y1-$y2, 2));
	}
	/*************Cartographic***********/
	//esta funcion requiere de una conexion abierta
	function defineMapWareBounds(){
		//definir variables relacionadas con las dimensiones del pais
		$query = "select xmax, xmin, ymax, ymin from global_bounds";
		$bound_estados = mysql_query($query) or die($query);
		$boundsPais = mysql_fetch_array($bound_estados);
		$this->xmin = $boundsPais["xmin"];
		$this->xmax = $boundsPais["xmax"];
		$this->ymin = $boundsPais["ymin"];
		$this->ymax = $boundsPais["ymax"];
		$this->width = ($this->xmax-$this->xmin);
		$this->height = ($this->ymax-$this->ymin);
	}
	function actualizarEscalaPorNivel($nivel){
		$this->currentNivel = $nivel;
		//pixel size
		$this->pixelWidth = 800 * pow(2, $nivel - 1);
		$this->pixelHeight = 600 * pow(2, $nivel - 1);
		$this->escala = $this->width/$this->pixelWidth;
	}
	//*************las siguientes dos funciones nos dicen a que cuadro i, j pertenece el punto dado
	function getCuadroFromPointAtNivel($x, $y, $nivel){
		$nivelBefore = $this->currentNivel*1;
		$this->actualizarEscalaPorNivel($nivel);
		$array_cuadro = $this->getCuadroFromPoint($x, $y);
		//regresamos los datos generales al nivel que estaban
		$this->actualizarEscalaPorNivel($nivelBefore);
		return $array_cuadro;
	}
	function getCuadroFromPoint($x, $y){	
		$xx = $x - $this->xmin;
		$yy = $y - $this->ymin;
		$pixelX = $xx/$this->escala;
		$pixelY = $yy/$this->escala;
		$cuadroX = floor($pixelX/$this->squareSize);
		$cuadroY = floor($pixelY/$this->squareSize);
		return array($cuadroX, $cuadroY);
	}
	//*************Las siguientes dos dunciones nos dicen en la imagen el pixel asociado a la coordenada ingresada
	function convertXFromMapWareToImage($x, $imageSquare){
		//$imageSquare representa el cuadro en un array(i, j) con la posicion de la imagen en el mosaico
		$x -= $this->xmin + ($imageSquare[0]*$this->squareSize*$this->escala);
		return $this->resize * $x/$this->escala;
	}
	function convertYFromMapWareToImage($y, $imageSquare){
		// $imageSquare representa el cuadro en un array(i, j) con la posicion de la imagen en el mosaico
		$y -= $this->ymin + ($imageSquare[1]*$this->squareSize*$this->escala);
		return $this->resize * $y/$this->escala;
	}
	//***************Creado y guardado de imagenes nuevas
	function crearGuardarImagenes($upperLeft, $lowerRight, $nivel, $clave_asociada, $table_name_asociado, $padding = 0, $tipoImagen = "mapa"){
		for($i=$upperLeft[0] - $padding; $i<=$lowerRight[0] + $padding; $i++){
			for($j=$upperLeft[1] - $padding; $j<=$lowerRight[1] + $padding; $j++){
				//creamos una clave unica por i, j y nivel
				$clave_imagen = $i."_".$j."_".$nivel;
				//definimos los limites de la imagen en MapWare
				$img_xmin = $this->xmin + $i * $this->squareSize*$this->escala;
				$img_xmax = $this->xmin + ($i+1)*$this->squareSize*$this->escala;
				$img_ymin = $this->ymin + $j * $this->squareSize*$this->escala;
				$img_ymax = $this->ymin + ($j+1)*$this->squareSize*$this->escala;
				//insertar imagen
				if($tipoImagen == "mapa"){
					$this->insertarImagenMapa($clave_imagen, $img_xmin, $img_xmax, $img_ymin, $img_ymax, $nivel, $i, $j);
				}elseif($tipoImagen == "satelite"){
					$this->insertarImagenSatelite($clave_imagen, $img_xmin, $img_xmax, $img_ymin, $img_ymax, $nivel, $i, $j);
				}
				//ver que clase de tabla es 
				$query = "select class from tables where table_name = '$table_name_asociado'";
				$r = mysql_query($query) or die($query);
				if(mysql_num_rows($r) != 0){
					$table_class = mysql_fetch_array($r);
				}else{
					$table_class = false;
				}
				//como para los paths esta desagregada la tabla de elementos por imagen tenemos que saberlo para poder meterlos
				if($table_class == false || $table_class["class"] != "RecordPolyLine"){
					$query = "insert into ".$table_name_asociado."_por_imagen 
					(clave, i, j, nivel)
					values
					('$clave_asociada', '$i', '$j', '$nivel')";
				}else{
					$query = "insert into ".$table_name_asociado."_por_imagen_".$nivel."
					(clave, i, j, nivel)
					values
					('$clave_asociada', '$i', '$j', '$nivel')";
				}
				mysql_query($query);
			}
		}
	}
	function insertarImagenSatelite($clave_imagen, $img_xmin, $img_xmax, $img_ymin, $img_ymax, $nivel, $i, $j){
		$polygon = "polygon(($img_xmin $img_ymin,$img_xmax $img_ymin,$img_xmax $img_ymax,$img_xmin $img_ymax,$img_xmin $img_ymin))";
		//intentamos guardar esta imagen nueva (puede fallar)
		$query = "insert into imagenes 
		( `i`, `j`, `nivel`, `mysql_puntos`, `aDibujar`) 
		values 
		($i, $j, '$nivel', geomfromtext('$polygon'), '0')";
		mysql_query($query);
	}
	function insertarImagenMapa($clave_imagen, $img_xmin, $img_xmax, $img_ymin, $img_ymax, $nivel, $i, $j){
		$polygon = "polygon(($img_xmin $img_ymin,$img_xmax $img_ymin,$img_xmax $img_ymax,$img_xmin $img_ymax,$img_xmin $img_ymin))";
		//elegimos un cpu de los 7 disponibles at random
		$cpu = rand(1, 7);
		//intentamos guardar esta imagen nueva (puede fallar)
		$query = "insert into imagenes 
		( `i`, `j`, `nivel`, `mysql_puntos`, `cpu`, `aDibujar`) 
		values 
		($i, $j, '$nivel', geomfromtext('$polygon'), '$cpu', '1')";
		mysql_query($query);
	}
	/*****************Geometry miscelaneous functions*************************/
	//sacar el punto de interseccion entre las lineas con pendientes y ordenadas de origen m1, b1 y m2, b2
	function linesIntersection($m1, $m2, $b1, $b2){
		//as pendientes no pueden ser iguales ya que en ese caso son paralelas
		if($m1 == $m2){
			return false;
		}
		//para el caso de lineas verticales
		if($m1 == INFINITO){
			//regresamos un punto en la recta 2 ya que la coodenada x en la recta 1 es contsante
			$x = $b1;
			$y = $m2*$x + $b2; 
		}		
		if($m2 == INFINITO){
			//regresamos un punto en la recta 1 ya que la coodenada x en la recta 2 es contsante
			$x = $b2;
			$y = $m1*$x + $b1; 
		}
		
		if($m1 != INFINITO && $m2 != INFINITO){
			$x = ($b2 - $b1)/($m1 - $m2);
			$y = ($m1*$x) + $b1;
		}
		return array($x, $y);
	}
	//ver si el punto $xy esta en el segmento $p, $q
	function isPointInSegment($p, $q, $xy){
		//si las x son iguales quiere decir que el segmento es vertical
		if(($q[0] - $p[0]) == 0){
			//$xy esta en el segmetno syss la x es la misma y la y esta entre las dos
			return ($xy[0] == $q[0] && $xy[1] >= min($q[1], $p[1]) && $xy[1] <= max($q[1], $p[1]));
		}
		if(($q[1] - $p[1]) == 0){
			//$xy esta en el segmetno syss la y es la misma y la x esta entre las dos
			return ($xy[1] == $q[1] && $xy[0] >= min($q[0], $p[0]) && $xy[0] <= max($q[0], $p[0]));
		}
		$k1 = round(($xy[0] - $p[0])/($q[0] - $p[0]), 2);
		$k2 = round(($xy[1] - $p[1])/($q[1] - $p[1]), 2);
		if($k1 == $k2 && $k1 >= 0 && $k1 <= 1){
			return true;
		}else{
			return false;
		}
	}
	
	function isClockwise($mysqlPolygon){
		$area = getArea($mysqlPolygon);
		return ($area<0);
	}
	function getPolygonArea($mysqlPolygon){
		$puntos = str_replace("POLYGON", "", $mysqlPolygon);
		$puntos = str_replace("((", "", $puntos);
		$puntos = str_replace("))", "", $puntos);
		$partes = explode("),(", $puntos);
		$sum = 0;
		for($j=0; $j<count($partes); $j++){
			$a_puntos = explode(",", $partes[$j]);
			for($i=0; $i<count($a_puntos)-1; $i++){
				$xy0 = explode(" ", $a_puntos[$i]);
				$xy1 = explode(" ", $a_puntos[$i+1]);
				$sum += $xy0[0]*$xy1[1]-$xy1[0]*$xy0[1];
			}
		}
		return .5*$sum;
	}
	function isPointInside($mysqlPolygon, $q){
		$puntos = str_replace("POLYGON", "", $mysqlPolygon);
		$puntos = str_replace("((", "", $puntos);
		$puntos = str_replace("))", "", $puntos);
		$partes = explode("),(", $puntos);
		for($j=0; $j<count($partes); $j++){
			$a_puntos = explode(",", $partes[$j]);
			for($i=0; $i<count($a_puntos); $i++){
				$a_puntos[$i] = explode(" ", $a_puntos[$i]);
			}
			if(InsidePolygon($a_puntos, $q)){
				return true;
			}
		}
		return false;
	}
	function segmentsIntersects($p1, $p2, $q1, $q2){
		//analizamos el primer semgento
		if($p1[0] != $p2[0]){
			$m1 = ($p1[1] - $p2[1]) / ($p1[0] - $p2[0]);
			$b1 = $p1[1] - ($m1*$p1[0]);
		}else{
			//regresamos la pendiente infinita y en vez de la ordenada de origen regresamos el valor de la x
			//ya que es constante
			$m1 = INFINITO;
			$b1 = $p1[0];
		}
		//analizamos el segundo semgento
		if($q1[0] != $q2[0]){
			$m2 = ($q1[1] - $q2[1]) / ($q1[0] - $q2[0]);
			$b2 = $q1[1] - ($m2*$q1[0]);
		}else{
			//regresamos la pendiente infinita y en vez de la ordenada de origen regresamos el valor de la x
			//ya que es constante
			$m2 = INFINITO;
			$b2 = $q1[0];
		}
		//punto de interseccion
		$xy = $this->linesIntersection($m1, $m2, $b1, $b2);
		if($xy == false){
			return false;
		}
		//ver si el punto esta en los segmentos
		return ($this->isPointInSegment($p1, $p2, $xy) && $this->isPointInSegment($q1, $q2, $xy));
	}
	function Angle2d($x1, $y1, $x2, $y2){
		$theta1 = atan2($y1, $x1);
		$theta2 = atan2($y2, $x2);
		$dtheta = $theta2 - $theta1;
		while ($dtheta > pi())
	    	$dtheta -= 2*pi();
		while ($dtheta < -1*pi())
	    	$dtheta += 2*pi();
		return($dtheta);	
	}
	function InsidePolygon($polygon, $p){
		$angle = 0;
		for($i=0; $i<count($polygon); $i++){
			$x1 = $polygon[$i][0] - $p[0];
			$y1 = $polygon[$i][1] - $p[1];
			$x2 = $polygon[($i+1)%count($polygon)][0] - $p[0];
			$y2 = $polygon[($i+1)%count($polygon)][1] - $p[1];
			$angle += $this->Angle2D($x1, $y1, $x2, $y2);
		}
		if (abs($angle) < pi())
	      return false;
	   else
	      return true;
	}
	/*******************************DataBase SetUp************************/
	function createProcessingTables(){
		$query = "CREATE TABLE `global_bounds` (
		  `xmin` double NOT NULL,
		  `xmax` double NOT NULL,
		  `ymin` double NOT NULL,
		  `ymax` double NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		if(mysql_query($query)){
			$query = "INSERT INTO `global_bounds` VALUES (-11712512.68, -8670935.82, -3271886.96, -1453391.17);";
			mysql_query($query);
		}
		
		$query = "CREATE TABLE `imagenes` (
		  `i` int(11) NOT NULL,
		  `j` int(11) NOT NULL,
		  `nivel` int(2) NOT NULL,
		  `mysql_puntos` multipolygon NOT NULL,
		  `mapa_exists` enum('0','1') NOT NULL,
		  `satelite_exists` enum('0','1') NOT NULL,
		  `hibrido_exists` enum('0','1') NOT NULL,
		  `fecha_dibujado` timestamp NOT NULL default CURRENT_TIMESTAMP,
		  `mapa` blob NOT NULL,
		  `satelite` blob NOT NULL,
		  `hibrido` blob NOT NULL,
		  `aDibujar` enum('0','1') NOT NULL default '1',
		  `cpu` enum('0','1','2','3','4','5','6','7') NOT NULL,
		  PRIMARY KEY  (`i`,`j`,`nivel`),
		  KEY `mapa_exists` (`mapa_exists`),
		  KEY `satelite_exists` (`satelite_exists`),
		  KEY `hibrido_exists` (`hibrido_exists`),
		  KEY `nivel_cpu_aDibujar` (`nivel`, `cpu`, `aDibujar`),
		  SPATIAL KEY `mysql_puntos` (`mysql_puntos`),
		  KEY `aDibujar` (`aDibujar`),
		  KEY `nivel` (`nivel`),
		  KEY `cpu` (`cpu`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		mysql_query($query);
		
		$query = "CREATE TABLE `labels` (
		  `id` int(11) NOT NULL auto_increment,
		  `clave` int(11) NOT NULL,
		  `x` double NOT NULL,
		  `y` double NOT NULL,
		  `xmax` int(11) NOT NULL,
		  `xmin` int(11) NOT NULL,
		  `ymax` int(11) NOT NULL,
		  `ymin` int(11) NOT NULL,
		  `angle` float NOT NULL,
		  `text` varchar(255) NOT NULL,
		  `size` int(11) NOT NULL,
		  `nivel` int(11) NOT NULL,
		  `table_name` varchar(100) NOT NULL,
		  `objeto_clave` varchar(50) NOT NULL,
		  `identifier` varchar(20) NOT NULL COMMENT 'parte_segmento',
		  `clean` enum('0','1','2') NOT NULL default '1',
		  `labelValue` int(11) NOT NULL,
		  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
		  `mysql_puntos` polygon NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `xmax_2` (`xmax`,`xmin`,`ymax`,`ymin`,`nivel`,`table_name`,`objeto_clave`),
		  KEY `nivel` (`nivel`),
		  KEY `tipo` (`table_name`),
		  KEY `grupo` (`clave`),
		  KEY `clean` (`clean`),
		  KEY `objeto` (`objeto_clave`),
		  KEY `labelValue` (`labelValue`),
		  SPATIAL KEY `mysql_puntos` (`mysql_puntos`),
		  KEY `nivel_4` (`nivel`,`clean`,`labelValue`),
		  KEY `xmin` (`xmin`),
		  KEY `ymax` (`ymax`),
		  KEY `ymin` (`ymin`),
		  KEY `nivel_2` (`nivel`,`clean`),
		  KEY `xmax` (`xmax`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 MAX_ROWS=4294967295 AVG_ROW_LENGTH=50 COMMENT='clean:0=>no sirve, 1=>por checar, 2=>si funciona' AUTO_INCREMENT=1 ;";
		mysql_query($query);
		
		$query = "CREATE TABLE `labels_por_imagen` (
		  `clave` varchar(255) NOT NULL,
		  `i` int(11) NOT NULL,
		  `j` int(11) NOT NULL,
		  `nivel` enum('0','1','2','3','4','5','6','7','8','9','10','11','12','13','14') NOT NULL,
		  PRIMARY KEY  (`clave`,`i`,`j`,`nivel`),
		  KEY `i` (`i`,`j`,`nivel`),
		  KEY `clave` (`clave`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			mysql_query($query);
			
			$query = "CREATE TABLE `omiciones` (
			  `id` int(11) NOT NULL auto_increment,
			  `palabra` varchar(20) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;";
			if(mysql_query($query)){
				$query = "INSERT INTO `omiciones` VALUES (1, 'la');
				INSERT INTO `omiciones` VALUES (2, 'el');
				INSERT INTO `omiciones` VALUES (3, 'lo');
				INSERT INTO `omiciones` VALUES (4, 'los');
				INSERT INTO `omiciones` VALUES (5, 'las');
				INSERT INTO `omiciones` VALUES (6, 'en');
				INSERT INTO `omiciones` VALUES (7, 'a');
				INSERT INTO `omiciones` VALUES (8, 'ante');
				INSERT INTO `omiciones` VALUES (9, 'bajo');
				INSERT INTO `omiciones` VALUES (10, 'cabe');
				INSERT INTO `omiciones` VALUES (11, 'con');
				INSERT INTO `omiciones` VALUES (12, 'contra');
				INSERT INTO `omiciones` VALUES (13, 'desde');
				INSERT INTO `omiciones` VALUES (14, 'de');
				INSERT INTO `omiciones` VALUES (15, 'del');
				INSERT INTO `omiciones` VALUES (16, 'para');
				INSERT INTO `omiciones` VALUES (17, 'por');
				INSERT INTO `omiciones` VALUES (18, 'sin');
				INSERT INTO `omiciones` VALUES (19, 'sobre');
				INSERT INTO `omiciones` VALUES (20, 'tras');
				INSERT INTO `omiciones` VALUES (21, 'un');
				INSERT INTO `omiciones` VALUES (22, 'una');
				INSERT INTO `omiciones` VALUES (23, 'unos');
				INSERT INTO `omiciones` VALUES (24, 'unas');
				INSERT INTO `omiciones` VALUES (26, ',');
				INSERT INTO `omiciones` VALUES (27, '.');";
				$split = explode(";", $query);
				for($i=0; $i<count($split); $i++){
					mysql_query($split[$i]);
				}
			}
		
		$query = "CREATE TABLE `paths_tipos` (
		  `id` int(11) NOT NULL auto_increment,
		  `table_name` varchar(200) NOT NULL,
		  `tipo_id` int(11) NOT NULL,
		  `drawOrder` int(11) NOT NULL,
		  `descripcion` text NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `table_name` (`table_name`,`tipo_id`),
		  KEY `tipo_id` (`tipo_id`),
		  KEY `table_name_2` (`table_name`),
		  KEY `drawOrder` (`drawOrder`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		mysql_query($query);
		
		$query = "CREATE TABLE `paths_tipos__colores` (
		  `path_tipo_id` int(11) NOT NULL,
		  `color` varchar(8) NOT NULL,
		  `descripcion` varchar(255) NOT NULL,
		  PRIMARY KEY  (`path_tipo_id`),
		  UNIQUE KEY `path_tipo_id` (`path_tipo_id`,`color`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
				";
		mysql_query($query);
		
		$query = "CREATE TABLE `paths_tipos__length__restrictions` (
		  `path_tipo_id` int(11) NOT NULL,
		  `nivel` int(11) NOT NULL,
		  `longitud_minima` int(11) NOT NULL,
		  UNIQUE KEY `path_tipo_id` (`path_tipo_id`,`nivel`),
		  KEY `nivel` (`nivel`),
		  KEY `path_tipo` (`path_tipo_id`),
		  KEY `longitud_minima` (`longitud_minima`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		mysql_query($query);
		
		$query = "CREATE TABLE `paths_tipos__thicks` (
		  `nivel` int(11) NOT NULL,
		  `path_tipo_id` int(11) NOT NULL,
		  `thick` float NOT NULL,
		  `thickBkg` float NOT NULL,
		  KEY `tipo` (`path_tipo_id`),
		  KEY `nivel` (`nivel`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		mysql_query($query);
		
		$query = "CREATE TABLE `satelite_originales_por_imagen` (
		  `clave` varchar(255) NOT NULL,
		  `i` int(11) NOT NULL,
		  `j` int(11) NOT NULL,
		  `nivel` int(2) NOT NULL,
		  PRIMARY KEY  (`clave`,`i`,`j`,`nivel`),
		  KEY `i` (`i`,`j`,`nivel`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		mysql_query($query);
		
		$query = "CREATE TABLE `shape_files` (
		  `id` int(11) NOT NULL auto_increment,
		  `table_name` varchar(200) NOT NULL,
		  `url` varchar(255) NOT NULL,
		  `processed` enum('0','1') NOT NULL,
		  `campoClave` varchar(20) NOT NULL default 'clave',
		  PRIMARY KEY  (`id`),
		  KEY `table_name` (`table_name`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;";
		if(mysql_query($query)){
			$query = "INSERT INTO `shape_files` VALUES (1, 'estados', 'estados/limite estatal.shp', '0', 'clave');
				INSERT INTO `shape_files` VALUES (2, 'municipios', 'municipios/limite municipal.shp', '0', 'clave');
				INSERT INTO `shape_files` VALUES (3, 'parques', 'equipamiento/parque o jardin.shp', '0', 'clave');
				INSERT INTO `shape_files` VALUES (4, 'areas_urbanas', 'areas_urbanas/area urbana.shp', '0', 'clave');
				INSERT INTO `shape_files` VALUES (5, 'industrias', 'equipamiento/industria.shp', '0', 'clave');
				INSERT INTO `shape_files` VALUES (6, 'calles', 'calles/calles.shp', '0', 'clave');
				INSERT INTO `shape_files` VALUES (7, 'colonias', 'colonias/colonias.shp', '0', 'clave');
				INSERT INTO `shape_files` VALUES (8, 'puntos_de_interes', 'informacion/puntos de interes nacional.shp', '0', 'clave');";
			$split = explode(";", $query);
			for($i=0; $i<count($split); $i++){
				mysql_query($split[$i]);
			}
		}
		
		$query = "CREATE TABLE `sonidos_iguales` (
		  `id` int(11) NOT NULL auto_increment,
		  `search` varchar(5) NOT NULL,
		  `replace` varchar(5) NOT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;";
		if(mysql_query($query)){
			$query = "INSERT INTO `sonidos_iguales` VALUES (1, 'cc', 'x');
			INSERT INTO `sonidos_iguales` VALUES (2, 'ci', 'si');
			INSERT INTO `sonidos_iguales` VALUES (3, 'z', 's');
			INSERT INTO `sonidos_iguales` VALUES (4, 'ca', 'ka');
			INSERT INTO `sonidos_iguales` VALUES (5, 'co', 'ko');
			INSERT INTO `sonidos_iguales` VALUES (6, 'cu', 'ku');
			INSERT INTO `sonidos_iguales` VALUES (7, 'que', 'ke');
			INSERT INTO `sonidos_iguales` VALUES (8, 'qui', 'ki');
			INSERT INTO `sonidos_iguales` VALUES (9, 'll', 'y');
			INSERT INTO `sonidos_iguales` VALUES (10, 'v', 'b');
			INSERT INTO `sonidos_iguales` VALUES (11, 'ge', 'je');
			INSERT INTO `sonidos_iguales` VALUES (12, 'gi', 'ji');
			INSERT INTO `sonidos_iguales` VALUES (13, 'ce', 'se');
			INSERT INTO `sonidos_iguales` VALUES (14, 'cl', 'kl');
			INSERT INTO `sonidos_iguales` VALUES (15, 'cr', 'kr');
			INSERT INTO `sonidos_iguales` VALUES (16, 'cy', 'ky');";
			$split = explode(";", $query);
			for($i=0; $i<count($split); $i++){
				mysql_query($split[$i]);
			}
		}
		$query = "CREATE TABLE `satelite_originales` (
		  `clave` int(11) NOT NULL auto_increment,
		  `nombre` varchar(255) NOT NULL,
		  `folder` varchar(255) NOT NULL,
		  `filename` varchar(255) NOT NULL,
		  `imagename` varchar(255) NOT NULL,
		  `hd` enum('0','1') NOT NULL,
		  `indice` int(11) NOT NULL,
		  `generada` int(1) NOT NULL,
		  `hibrido` int(11) NOT NULL,
		  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`clave`),
		  KEY `hd` (`hd`),
		  KEY `generada` (`generada`),
		  KEY `hibrido` (`hibrido`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=84 ;";
		mysql_query($query);
		
		$query = "CREATE TABLE `tables` (
		  `table_name` varchar(200) NOT NULL,
		  `class` enum('RecordPolyLine','RecordPolygon','RecordPoint') NOT NULL,
		  `drawLayerOrder` int(11) NOT NULL COMMENT '0 is for no draw',
		  `hibridDrawLayerOrder` int(11) NOT NULL COMMENT '0 is for no drawing',
		  `labelOrder` int(11) NOT NULL COMMENT '0 is for no labeling',
		  `fecha` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`table_name`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		if(mysql_query($query)){
			$query = "INSERT INTO `tables` VALUES ('areas_urbanas', 'RecordPolygon', 3, 0, 4, '2008-11-27 15:08:01');
			INSERT INTO `tables` VALUES ('estados', 'RecordPolygon', 1, 1, 5, '2008-11-27 15:08:01');
			INSERT INTO `tables` VALUES ('municipios', 'RecordPolygon', 2, 2, 6, '2008-11-27 15:08:01');
			INSERT INTO `tables` VALUES ('parques', 'RecordPolygon', 4, 0, 2, '2008-11-27 15:08:01');
			INSERT INTO `tables` VALUES ('industrias', 'RecordPolygon', 5, 0, 0, '2008-11-27 14:57:35');
			INSERT INTO `tables` VALUES ('calles', '', 6, 3, 3, '2008-11-27 15:08:01');
			INSERT INTO `tables` VALUES ('colonias', 'RecordPolygon', 0, 0, 1, '2008-11-27 15:07:36');
			INSERT INTO `tables` VALUES ('puntos_de_interes', 'RecordPoint', 0, 0, 0, '2008-11-27 15:30:55');";
			$split = explode(";", $query);
			for($i=0; $i<count($split); $i++){
				mysql_query($split[$i]);
			}
		}
		
		
		$query = "CREATE TABLE `tables__attributes` (
		  `table_name` varchar(200) NOT NULL,
		  `catalogos` text NOT NULL,
		  `crearImagenesFromNivel` int(11) NOT NULL,
		  `crearImagenesToNivel` int(11) NOT NULL,
		  `drawFromNivel` int(11) NOT NULL,
		  `drawToNivel` int(11) NOT NULL,
		  `labelFromNivel` int(11) NOT NULL,
		  `labelToNivel` int(11) NOT NULL,
		  `colorLabel` varchar(8) NOT NULL,
		  `colorFill` varchar(8) NOT NULL,
		  `colorBorder` varchar(8) NOT NULL,
		  `drawPointInCenter` enum('0','1') NOT NULL,
		  `drawPointInCenterFromNivel` int(11) NOT NULL,
		  `drawPointInCenterToNivel` int(11) NOT NULL,
		  PRIMARY KEY  (`table_name`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='El primer valor del catalogo es el campo de tipo';";
		if(mysql_query($query)){
			$query = "INSERT INTO `tables__attributes` VALUES ('estados', '', 1, 9, 1, 13, 1, 9, '98969700', 'F2EFEA00', 'B3AFAF00', '0', 0, 0);
			INSERT INTO `tables__attributes` VALUES ('areas_urbanas', 'tipo', 1, 13, 1, 13, 1, 9, '33333300', 'EDEAD200', 'DEDEDE00', '1', 1, 8);
			INSERT INTO `tables__attributes` VALUES ('industrias', 'tipo', 0, 0, 7, 13, 12, 13, 'FD606F00', 'FFB4B400', 'FFB4B47f', '0', 0, 0);
			INSERT INTO `tables__attributes` VALUES ('municipios', '', 0, 0, 1, 11, 0, 0, '', 'EDEAD27f', 'DEDEDE00', '0', 0, 0);
			INSERT INTO `tables__attributes` VALUES ('parques', 'tipo', 0, 0, 7, 13, 8, 13, '0C570200', 'B5E29D00', 'B5E29D7f', '0', 0, 0);
			INSERT INTO `tables__attributes` VALUES ('colonias', '', 0, 0, 0, 0, 8, 11, '00339900', '', '', '0', 0, 0);
			INSERT INTO `tables__attributes` VALUES ('calles', 'tipo', 7, 13, 7, 13, 8, 13, '10101000', '', '', '0', 0, 0);
			INSERT INTO `tables__attributes` VALUES ('puntos_de_interes', 'tipo,tema', 0, 0, 0, 0, 0, 0, '', '', '', '0', 0, 0);";
			$split = explode(";", $query);
			for($i=0; $i<count($split); $i++){
				mysql_query($split[$i]);
			}
		}
	}
}
?>