<?php
class CleanLabels extends MapWareCore {
	var $nivel;
	var $tables = array();
	var $notCleanLabels = array();
	//contadores
	function CleanLabels($nivelNumber = 1){
		/*$puntos1 = array(array(0, 0), array(1, -1), array(4, 2), array(3, 3));
		$puntos2 = array(array(1, 3), array(4, -1), array(5, 0), array(2, 4));
		$label1 = new LabelBox(array(0, 0), array(1, -1), array(4, 2), array(3, 3));
		$label2 = new LabelBox(array(1, 3), array(4, -1), array(5, 0), array(2, 4));
		//$label2 = new LabelBox(array(1, 0), array(4, -4), array(5, -3), array(2, 1));
		if($label1->intersects($label2)){
			echo "se intersectan";
		}else{
			echo "no se intersectan";
		}
		die("");*/
		
		$this->openMySQLConn();
		//set nivel a limpiar
		$this->nivel = $nivelNumber;
		//definir variables y limites globales
		$this->defineMapWareBounds();
		//sacamos las propiedades de las tables
		$query = "select * from tables
		where labelOrder != 0";
		$res = mysql_query($query) or die($query);
		while($row = mysql_fetch_array($res)){
			$this->tables[$row["table_name"]] = $row;
		}
		//preparar una tabla en memoria que representara los labels de este nivel
		$query = "DROP TABLE `labels_memory_$this->nivel`";
		mysql_query($query);
		$query = "CREATE TABLE `labels_memory_$this->nivel` (
		  `order_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		  `id` int(11) NOT NULL,
		  `clave` int(11) NOT NULL,
		  `xmax` int(11) NOT NULL,
		  `xmin` int(11) NOT NULL,
		  `ymax` int(11) NOT NULL,
		  `ymin` int(11) NOT NULL,
		  `text` varchar(255) NOT NULL,
		  `nivel` int(11) NOT NULL,
		  `table_name` varchar(100) NOT NULL,
		  `clean` enum('0','1','2') NOT NULL default '1',
		  KEY `id` (`id`),
		  KEY `nivel` (`nivel`),
		  KEY `tipo` (`table_name`),
		  KEY `grupo` (`clave`),
		  KEY `clean` (`clean`),
		  KEY `xmin` (`xmin`),
		  KEY `ymax` (`ymax`),
		  KEY `ymin` (`ymin`),
		  KEY `nivel_2` (`nivel`,`clean`),
		  KEY `xmax` (`xmax`)
		) ENGINE=MEMORY  DEFAULT CHARSET=utf8";
		mysql_query($query) or die($query);
		$query = "insert into `labels_memory_$this->nivel` 
		select null as order_id, id, clave, xmax, xmin, ymax, ymin, text, nivel, table_name, '1' as clean
		from labels
		where nivel = $this->nivel and clean in ('1', '2')
		order by labelValue asc";
		mysql_query($query) or die($query);
	}
	function startCleaning(){
		$query = "select memoria.clave, memoria.xmax, memoria.xmin, memoria.ymax, memoria.ymin, 
		memoria.clean, astext(mysql_puntos) as puntos,
		astext(envelope(mysql_puntos)) as boundingBox, 
		memoria.table_name, memoria.`text`
		from `labels_memory_$this->nivel` as memoria
		join labels on labels.id = memoria.id
		where memoria.clean = '1'
		ORDER BY order_id";
		$res = mysql_query($query) or die($query);
		error_log("empezamos");
		while($new = mysql_fetch_array($res)){
			if(!isset($this->notCleanLabels[$new["clave"]])){
				//todos estan limpios hasta que se pruebe lo contrario
				$clean = true;
				//sacamos los labels de la base de datos que intersectan en caja a new
				$withPadding = ($this->tables[$new["table_name"]]["class"] == "RecordPolygon") ? true : false;
				$intersectLabels = $this->getIntersectLabels($new, $withPadding);
				//arreglo que contendra las claves de los labels intersectados por new
				$intersectados = array();
				while($intersection = mysql_fetch_array($intersectLabels)){
					//comparamos los labels $new y $label, si no se intersectan agregamos el new a cleanLabels 
					if($new["clave"] != $intersection["clave"] && $this->labelsSeIntersectan($new, $intersection)){
						//veamos si al que intersecto es un clean label, en cuyo caso este ya se frego y no esta limpio
						if($intersection["clean"] == 2){
							$clean = false;	
							break;
						}
						array_push($intersectados, $intersection["clave"]);
					}
				}
				if($clean){
					//si esta limpio lo guardamos dentro de nuestros clean labels
					$this->guardarLabel($new["clave"], 2);
					//cambiamos a clean 0 todos los labels qeu intersecto new
					for($h=0; $h<count($intersectados); $h++){
						$this->guardarLabel($intersectados[$h], 0);
						$this->notCleanLabels[$intersectados[$h]] = true;
					}
					
				}else{
					$this->guardarLabel($new["clave"], 0);
				}
				$intersectados = NULL;
				mysql_free_result($intersectLabels);
			}
		}
		//actualizar la base de datos de labels con la info de `labels_memory_$this->nivel`
		$query = "update labels
		join `labels_memory_$this->nivel` on `labels_memory_$this->nivel`.id = labels.id
		set labels.clean = `labels_memory_$this->nivel`.clean
		where `labels`.nivel = '$this->nivel'";
		mysql_query($query) or die($query);
		//borrar la base en memoria
		$query = "DROP TABLE `labels_memory_$this->nivel`";
		mysql_query($query);
		//regresamos false para que continue el proceso
		return false;
	}
	function guardarLabel($clave, $cleanStatus){
		$query = "update `labels_memory_$this->nivel` set clean = '$cleanStatus' where clave = '$clave'";
		mysql_query($query) or die($query);
	}
	function labelsSeIntersectan($label1, $label2){
		//comparamos si los nombres de los dos labels son iguales en cuyo caso comparamos los bounds en vez del label
		//tambien en el caso de ser labels poligonos usamos los bounds en vez del label
		if($this->tables[$label1["table_name"]]["class"] == "RecordPolygon" || $label1["text"] == $label2["text"]){
			$points = str_replace("POLYGON((", "", $label1["boundingBox"]);
		}else{
			$points = str_replace("POLYGON((", "", $label1["puntos"]);
		}
		$points = str_replace("))", "", $points);
		$points = explode(",", $points);
		for($i=0; $i<count($points); $i++){
			$points[$i] = explode(" ", $points[$i]);
		}
		$labelBox1 = new LabelBox($points[0], $points[1], $points[2], $points[3]);
		//
		//ahora con el segundo label
		//
		if($this->tables[$label2["table_name"]]["class"] == "RecordPolygon" || $label1["text"] == $label2["text"]){
			$points = str_replace("POLYGON((", "", $label2["boundingBox"]);
		}else{
			$points = str_replace("POLYGON((", "", $label2["puntos"]);
		}
		$points = str_replace("))", "", $points);
		$points = explode(",", $points);
		for($i=0; $i<count($points); $i++){
			$points[$i] = explode(" ", $points[$i]);
		}
		$labelBox2 = new LabelBox($points[0], $points[1], $points[2], $points[3]);
		//si se va dibujar el punto en el centro, a la hora de limpiar se tiene que tomar n cuenta el 
		//punto para que los labels no hagan overlap al punto
		if($this->tables[$label1["table_name"]]["drawPointInCenter"] == 1){
			$labelBox1->addPaddingDownToLabelBox();
		}
		if($this->tables[$label2["table_name"]]["drawPointInCenter"] == 1){
			$labelBox2->addPaddingDownToLabelBox();
		}
		//de ser poligonos les damos cierto padding para evitar saturacion en la pantalla
		if($this->tables[$label1["table_name"]]["class"] == "RecordPolygon" && 
		$this->tables[$label2["table_name"]]["class"] == "RecordPolygon"){
			$labelBox1->addPaddingToLabelBox(0.25);
			$labelBox2->addPaddingToLabelBox(0.25);
		}else{
			if($this->tables[$label1["table_name"]]["class"] == "RecordPolygon"){
				$labelBox1->addPaddingToLabelBox();
			}
			if($this->tables[$label2["table_name"]]["class"] == "RecordPolygon"){
				$labelBox2->addPaddingToLabelBox();
			}
		}
		if($label1["text"] == $label2["text"]){
			$labelBox1->addPaddingToLabelBox(1);
			$labelBox2->addPaddingToLabelBox(1);
		}
		$seIntersectan = $labelBox1->intersects($labelBox2);
		return $seIntersectan;
	}
	function getIntersectLabels($label, $withPadding = false){
		$xmin = $label["xmin"];
		$xmax = $label["xmax"];
		$ymin = $label["ymin"];
		$ymax = $label["ymax"];
		if($withPadding){
			$padding = 1 * abs($ymax - $ymin);
			$xmin -= $padding;
			$xmax += $padding;
			$ymin -= $padding;
			$ymax += $padding;
		}
		$upperLeft = $this->getCuadroFromPointAtNivel($xmin, $ymin, $this->nivel);
		$lowerRight = $this->getCuadroFromPointAtNivel($xmax, $ymax, $this->nivel);
		//
		$arrayI = array();
		for($h=$upperLeft[0]; $h<= $lowerRight[0]; $h++){
			array_push($arrayI, $h);
		}
		$strI = "labels_por_imagen.i = '".implode("' or labels_por_imagen.i ='", $arrayI)."'";
		//
		$arrayJ = array();
		for($h=$upperLeft[1]; $h<= $lowerRight[1]; $h++){
			array_push($arrayJ, $h);
		}
		$strJ = "labels_por_imagen.j = '".implode("' or labels_por_imagen.j ='", $arrayJ)."'";
		
		$query = "select memory.clave, memory.xmax, memory.xmin, memory.ymax, memory.ymin, 
		memory.clean, astext(mysql_puntos) as puntos,
		astext(envelope(mysql_puntos)) as boundingBox, 
		memory.table_name, memory.`text`
		from `labels_memory_$this->nivel` as memory
		join labels on labels.id = memory.id
		join labels_por_imagen on labels_por_imagen.clave = memory.clave
		WHERE (".$strI.") and (".$strJ.") and 
		labels_por_imagen.nivel = '$this->nivel'
		AND memory.clean != '0'
		order by memory.clean desc";
		$intersectLabels = mysql_query($query) or die($query);
		return $intersectLabels;
	}
}
?>