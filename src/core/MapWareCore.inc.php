<?PHP
define("INFINITO", 10000000000);
class mapWareCore{
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
				
				$query = "insert into ".$table_name_asociado."_por_imagen 
				(clave, i, j, nivel)
				values
				('$clave_asociada', '$i', '$j', '$nivel')";
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
}
?>