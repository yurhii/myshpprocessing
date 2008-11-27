<?PHP
//este srictp requeiere que max_allowed_packet=500M ( esto en MAMP/bin/startMysql.sh )	
class InsertShapeFile extends MapWareCore{
	var $shp;//ShapeFile
	//esta variable indica si este para estos elementos cartograficos debe haber o no labels
	var $withLabels = false;
	//
	var $table_name = NULL;
	//variable que define el tipo de shp, poligono, path o punto
	var $class;
	//array que contiene le nombre de los campos que seran catalogos
	var $catalogos = array();
	//variable que nos dice para el caso de paths cual es el campo que define su catalogo principal
	var $pathCampoTipo = "";
	//nos indica si este shape file se va a dibujar en algun momento
	var $aDibujar = false;
	//
	var $nivelInicialDeReferencia;
	var $nivelFinalDeReferencia;
	//campo que sera asignado como campo clave y se le cambiara el nombre por default es clave
	var $campoClave;
	function InsertShapeFile($id_shp_file, $source_url, $tableName, $labelThisShp = false, $array_catalogos = array(), $nivelInicial = 1, $nivelFinal = 13, $dibujar = false, $setCampoClave = "clave"){
		$this->openMySQLConn();
		//definimos el array con los nombres de los campos parte de catalogos
		$this->catalogos = $array_catalogos;
		//
		$this->pathCampoTipo = $array_catalogos[0];
		//
		$this->aDibujar = $dibujar;
		//dependiendo de si es informacion de detalle (calle, parques, industrias)
		//la dibujaremos y referenciaremos en niveles mayores al nivelMinimoDeDibujadoDeDetalle o no
		$this->nivelInicialDeReferencia = $nivelInicial;
		$this->nivelFinalDeReferencia = $nivelFinal;
		$this->withLabels = $labelThisShp;
		$this->table_name = $tableName;
		//definir el campo clave del shape_file
		$this->campoClave = $setCampoClave;
		//definir los bordes del area a utulizar en base a las dimensiones del pais
		$this->defineMapWareBounds();
		//define shapeFile from url
		$this->shp = new ShapeFile("../".$source_url) or die("no shape file"); // along this file the class will use file.shx and file.dbf
	}
	function preview($todos = false){
		$this->shp->fetchAllRecords();
		// Let's see all the records:
		$top = ($todos) ? count($this->shp->records) : 1;
		for($i=0; $i< $top;$i++){
			echo "Total de rows en el ShapeFile => ".count($this->shp ->records);
			echo "<pre>";
			print_r($this->shp->records[$i]);
			echo "</pre>";
		}
	}
	//clases posibles polygon, path, point
	function insertShpData(){
		$first_record = false;
		//for($indice = 0; $indice< count($this->shp->records);$indice++){
		while($shp_record = $this->shp->fetchOneRecord()){
			$shp_data = $shp_record->shp_data;
			$dbf_data = $shp_record->dbf_data;
			$this->class = $shp_record->record_class[$shp_record->record_shape_type];
			//
			if(!$first_record){
				$this->crearTablas($shp_record);
				$first_record = true;
			}
			switch($this->class){
				case "RecordPolyLine":
					$inserted_clave = $this->insertRecordPolyLine($shp_data, $dbf_data);
					break;
				case "RecordPolygon":
					$inserted_clave = $this->insertRecordPolygon($shp_data, $dbf_data);
					break;
				case "RecordPoint":
					$inserted_clave = $this->insertRecordPoint($shp_data, $dbf_data);
					break;
			}
			//crear y guardar lasimagenes asociadas a este row
			$upperLeftPoint = array($this->convertXFromShpToMapWare($shp_data["xmin"]),
								$this->convertYFromShpToMapWare($shp_data["ymax"]));
			$lowerRightPoint = array($this->convertXFromShpToMapWare($shp_data["xmax"]),
								$this->convertYFromShpToMapWare($shp_data["ymin"]));
			for($nivel = $this->nivelInicialDeReferencia; $nivel <= $this->nivelFinalDeReferencia; $nivel++){
				$upperLeft = $this->getCuadroFromPointAtNivel($upperLeftPoint[0], $upperLeftPoint[1], $nivel);
				$lowerRight = $this->getCuadroFromPointAtNivel($lowerRightPoint[0], $lowerRightPoint[1], $nivel);
				if($nivel > 9 && $this->table_name == "areas_urbanas"){
					$padding = 8;
				}elseif($this->table_name == "areas_urbanas"){
					$padding = 3;
				}elseif($this->table_name == "calles"){
					$padding = 1;
				}else{
					$padding = 0;
				}
				$this->actualizarEscalaPorNivel($nivel);
				$this->crearGuardarImagenes($upperLeft, $lowerRight, $nivel, $inserted_clave, $this->table_name, $padding);
			}
		}
	}
	function insertRecordPolyLine($shp_data, $dbf_data){
		$aPartes = array();
		for($k=0; $k < count($shp_data["parts"]); $k++){
			$aPuntos = array();
			for($j=0; $j< count($shp_data["parts"][$k]["points"]); $j++){
				array_push($aPuntos, $this->convertXFromShpToMapWare($shp_data["parts"][$k]["points"][$j]["x"])." ".$this->convertYFromShpToMapWare($shp_data["parts"][$k]["points"][$j]["y"]));
			}
			array_push($aPartes, implode(",", $aPuntos));
			unset($aPuntos);
		}
		if(count($aPartes) > 1){
			$geomString = "MultiLineString((".implode("),(", $aPartes)."))";
		}else{
			$geomString = "LineString(".$aPartes[0].")";
		}
		//texto de puntos separado por comas y con z por partes
		$mysql_text = implode("z", $aPartes);
		unset($aPartes);
		//insertar los campos que de informacion del dbf del shape file
		//arreglo que contendra los campos y sus valores
		$campos = array();
		$valores = array();
		foreach($dbf_data as $key=>$valor){
			$value = $this->limpiar($valor);
			$key = $this->limpiar($key);
			//por default los shp traen un campo de deleted inservible
			if($key != "deleted"){
				$field = strtolower($key);
				$field_value = $value;
				//ver si el campo es parte de los catalogos, de ser asi no se añade el valor al catalogo y en la table se
				//inserta la referencia a este
				for($h=0; $h<count($this->catalogos); $h++){
					if(strtolower($this->catalogos[$h]) == strtolower($key)){
						//insertamos el valor en el catalogo
						$query = "insert into `".$this->table_name."__".strtolower($key)."__catalogo`
						(`".strtolower($key)."`)
						values
						('".$value."')";
						if(mysql_query($query)){
							$inserted_id = mysql_insert_id();
							//Si ademas  el catalogo es el catalogo primario (pathCampoTipo) del shp en shp_tablas, 
							//entonces guardamos la referencia en paths_tipos
							//y guardamos en paths_tipos__length__restrictions los datos necesarios por cada nivel
							if(strtolower($key)."_id" == $this->pathCampoTipo){
								//si logramos insertar el campo en el catalogo y es un path 
								$query = "insert into paths_tipos
								(table_name, tipo_id, descripcion)
								values
								('".$this->table_name."', '".$inserted_id."', '".$value."')";
								if(mysql_query($query)){
									$path_tipo_id = mysql_insert_id();
								}else{
									$query = "select * from paths_tipos
									where table_name = '".$this->table_name."'
									and tipo_id = '".$inserted_id."' limit 1";
									$path_tipo = mysql_fetch_array(mysql_query($query)) or die($query);
									$path_tipo_id = $path_tipo["id"];
								}
								for($n=$this->nivelInicialDeReferencia; $n<=$this->nivelFinalDeReferencia; $n++){
									$query = "insert into paths_tipos__length__restrictions
									(`path_tipo_id`, `nivel`, `longitud_minima`)
									values
									('".$path_tipo_id."', '".$n."', 0)";
									mysql_query($query);
								}
							}
						}else{
							$query = "select id from `".$this->table_name."__".strtolower($key)."__catalogo`
							where `".strtolower($key)."` = '".$value."'";
							$result = mysql_fetch_array(mysql_query($query)) or die($query);
							$inserted_id = $result["id"];
						}
						//en table_name ingresamos el id del catalogo correspondiente al valor en lugar del value del shp
						$field = strtolower($key)."_id";
						$field_value = $inserted_id;
					}
				}
				if(strtolower($field) == "longitud"){
					$field_value  = $this->convertXFromShpToMapWare($field_value);
				}
				//guardamos el campo y su valor para meterse en el query de insert
				array_push($campos, "`".$field."`");
				if(strtolower($field) == "nombre"){
					array_push($valores, "'".$this->changeLatin($field_value)."'");
				}else{
					array_push($valores, "'".$field_value."'");
				}
			}
			//al nombre agregarle el alias
			if(strtolower($key) == "nombre"){
				array_push($campos, "`alias`");
				array_push($valores, "'".$this->relpaceFromDictionary($valor)."'");
			}
		}
		array_push($campos, "`mysql_puntos`");
		array_push($valores, "GeomFromText('".$geomString."')");
		//puntos como texto
		array_push($campos, "`text_puntos`");
		array_push($valores, "'".$mysql_text."'");
		//bounds
		array_push($campos, "`xmin`");
		array_push($valores, "'".floor($this->convertXFromShpToMapWare($shp_data["xmin"]))."'");
		array_push($campos, "`xmax`");
		array_push($valores, "'".ceil($this->convertXFromShpToMapWare($shp_data["xmax"]))."'");
		//como estos valores se vuelven su negativo se intercambian al insertarse
		array_push($campos, "`ymin`");
		array_push($valores, "'".floor($this->convertYFromShpToMapWare($shp_data["ymax"]))."'");
		array_push($campos, "`ymax`");
		array_push($valores, "'".ceil($this->convertYFromShpToMapWare($shp_data["ymin"]))."'");
		//query de insertado
		$query = "insert into ".$this->table_name."
		(".implode(", ", $campos).")
		values
		(".implode(", ", $valores).")";
		die($query);
		mysql_query($query);
		return $this->limpiar($dbf_data[strtoupper($this->campoClave)]);
	}
	function insertRecordPolygon($shp_data, $dbf_data){
		$aPartes = array();
		for($k=0; $k < count($shp_data["parts"]); $k++){
			$aPuntos = array();
			if(count($shp_data["parts"][$k]["points"]) < 3){
				return false;
			}
			for($j=0; $j< count($shp_data["parts"][$k]["points"]); $j++){
				array_push($aPuntos, $this->convertXFromShpToMapWare($shp_data["parts"][$k]["points"][$j]["x"])." ".$this->convertYFromShpToMapWare($shp_data["parts"][$k]["points"][$j]["y"]));
			}
			array_push($aPartes, implode(",", $aPuntos));
			unset($aPuntos);
		}
		$geomString = "Polygon((".implode("),(", $aPartes)."))";
		$mysql_text = implode("z", $aPartes); 
		//size del poligono
		$size = abs(max( $this->convertYFromShpToMapWare($shp_data["ymax"]) - $this->convertYFromShpToMapWare($shp_data["ymin"]), $this->convertYFromShpToMapWare($shp_data["xmax"]) - $this->convertYFromShpToMapWare($shp_data["xmin"]) ));
		unset($aPartes);
		//insertar los campos que de informacion del dbf del shape file
		//arreglo que contendra los campos y sus valores
		$campos = array();
		$valores = array();
		//insertar los campos que de informacion del dbf del shape file
		foreach($dbf_data as $key=>$valor){
			$value = $this->limpiar($valor);
			$key = $this->limpiar($key);
			//por default los shp traen un campo de deleted inservible
			if($key != "deleted"){
				$field = strtolower($key);
				$field_value = $value;
				//ver si el campo es parte de los catalogos, de ser asi no se añade el valor al catalogo y en la table se
				//inserta la referencia a este
				for($h=0; $h<count($this->catalogos); $h++){
					if(strtolower($this->catalogos[$h]) == strtolower($key)){
						//insertamos el valor en el catalogo
						$query = "insert into `".$this->table_name."__".strtolower($key)."__catalogo`
						(`".strtolower($key)."`)
						values
						('".$value."')";
						if(mysql_query($query)){
							$inserted_id = mysql_insert_id();
						}else{
							$query = "select id from `".$this->table_name."__".strtolower($key)."__catalogo`
							where `".strtolower($key)."` = '".$value."'";
							$result = mysql_fetch_array(mysql_query($query)) or die($query);
							$inserted_id = $result["id"];
						}
						//en table_name ingresamos el id del catalogo correspondiente al valor en lugar del value del shp
						$field = strtolower($key)."_id";
						$field_value = $inserted_id;
					}
				}
				//agregar valores y campos al array correspondiente para el query de insert
				array_push($campos, "`".$field."`");
				if(strtolower($field) == "nombre"){
					array_push($valores, "'".$this->changeLatin($field_value)."'");
				}else{
					array_push($valores, "'".$field_value."'");
				}
				//al nombre agregarle el alias
				if(strtolower($key) == "nombre"){
					array_push($campos, "`alias`");
					array_push($valores, "'".$this->relpaceFromDictionary($valor)."'");
				}
			}
		}
		//insertar campos geograficos asociados al shp
		array_push($campos, "`mysql_puntos`");
		array_push($valores, "GeomFromText('".$geomString."')");
		//puntos como texto
		array_push($campos, "`text_puntos`");
		array_push($valores, "'".$mysql_text."'");
		//bounds
		array_push($campos, "`xmin`");
		array_push($valores, "'".floor($this->convertXFromShpToMapWare($shp_data["xmin"]))."'");
		array_push($campos, "`xmax`");
		array_push($valores, "'".ceil($this->convertXFromShpToMapWare($shp_data["xmax"]))."'");
		//como estos valores se vuelven su negativo se intercambian al insertarse
		array_push($campos, "`ymin`");
		array_push($valores, "'".floor($this->convertYFromShpToMapWare($shp_data["ymax"]))."'");
		array_push($campos, "`ymax`");
		array_push($valores, "'".ceil($this->convertYFromShpToMapWare($shp_data["ymin"]))."'");
		array_push($campos, "`size`");
		array_push($valores, "'".$size."'");
		//query de insertado
		$query = "insert into ".$this->table_name."
		(".implode(", ", $campos).")
		values
		(".implode(", ", $valores).")";
		$success = mysql_query($query);
		//regresamos la clave del objeto
		return $this->limpiar($dbf_data[strtoupper($this->campoClave)]);
	}
	function insertRecordPoint($shp_data, $dbf_data){
		$x = $this->convertXFromShpToMapWare($shp_data["x"]);
		$y = $this->convertYFromShpToMapWare($shp_data["y"]);
		$geomString = "POINT($x $y)";
		$mysql_text = $x." ".$y;
		$campos = array();
		$valores = array();
		//insertar los campos que de informacion del dbf del shape file
		foreach($dbf_data as $key=>$valor){
			$value = $this->limpiar($valor);
			$key = $this->limpiar($key);
			//por default los shp traen un campo de deleted inservible
			if($key != "deleted"){
				$field = strtolower($key);
				$field_value = $value;
				//ver si el campo es parte de los catalogos, de ser asi no se añade el valor al catalogo y en la table se
				//inserta la referencia a este
				for($h=0; $h<count($this->catalogos); $h++){
					if(strtolower($this->catalogos[$h]) == strtolower($key)){
						//insertamos el valor en el catalogo
						$query = "insert into `".$this->table_name."__".strtolower($key)."__catalogo`
						(`".strtolower($key)."`)
						values
						('".$value."')";
						if(mysql_query($query)){
							//el inserted id representa el id del catalogo correspondiente a este valor del catalogo
							$inserted_id = mysql_insert_id();
						}else{
							$query = "select id from `".$this->table_name."__".strtolower($key)."__catalogo`
							where `".strtolower($key)."` = '".$value."'";
							$result = mysql_fetch_array(mysql_query($query)) or die($query);
							$inserted_id = $result["id"];
						}
						//en table_name ingresamos el id del catalogo correspondiente al valor en lugar del value del shp
						$field = strtolower($key)."_id";
						$field_value = $inserted_id;
					}
				}
				//guardamos el campo y su valor para meterse en el query de insert
				array_push($campos, "`".$field."`");
				if(strtolower($field) == "nombre"){
					array_push($valores, "'".$this->changeLatin($field_value)."'");
				}else{
					array_push($valores, "'".$field_value."'");
				}
			}
			//al nombre agregarle el alias
			if(strtolower($key) == "nombre"){
				array_push($campos, "`alias`");
				array_push($valores, "'".$this->relpaceFromDictionary($valor)."'");
			}
		}
		//insertar campos geograficos asociados al shp
		array_push($campos, "`mysql_puntos`");
		array_push($valores, "GeomFromText('".$geomString."')");
		//puntos como texto
		array_push($campos, "`text_puntos`");
		array_push($valores, "'".$mysql_text."'");
		//query de insertado
		$query = "insert into ".$this->table_name."
		(".implode(", ", $campos).")
		values
		(".implode(", ", $valores).")";
		mysql_query($query) or die($query);
		//regresamos el valor del cmapo clave de este row
		return $this->limpiar($dbf_data[strtoupper($this->campoClave)]);
	}
	function crearTablas($shp_record){
		$shp_data = $shp_record->shp_data;
		$dbf_data = $shp_record->dbf_data;
		$query = "SHOW TABLES LIKE '".$this->table_name."'";
		//ver si la tabla ya existe de lo contrario crearla
		if(mysql_num_rows(mysql_query($query)) == 0){
			//crear la tabla
			$query = "create table ".$this->table_name." ( ";
			$campos = array();
			$indices = array();
			foreach($dbf_data as $key=>$value){
				$value = $this->limpiar($value);
				$key = $this->limpiar($key);
				//por default los shp traen un campo de deleted inservible
				if($key != "deleted"){
					//si es int, float o double
					//y al convertirlo a numero tiene como string la misma longitud que el original
					//esto es ya que algunas claves pueden empezar por cero
					$valueNumericString = "".($value*1)."";
					if(is_numeric($value) && strlen($valueNumericString) == strlen($value) && strtolower($key) != $this->campoClave){
						if($value == round($value)){
							$tipo = "int";
						}else{
							$tipo = "double";
						}
					}else{
						if(strtolower($key) != $this->campoClave){
							$tipo = "varchar(255)";
						}else{
							$tipo = "varchar(50)";
						}
					}
					//ver si el campo es parte de los catalogos, de ser asi no se añade su tipo si no un int
					//y se crea el catalogo correspondiente
					for($k=0; $k<count($this->catalogos); $k++){
						if(strtolower($this->catalogos[$k]) == strtolower($key)){
							//creamos el catalogo
							$queryCreateCatalogo = "create table ".$this->table_name."__".strtolower($key)."__catalogo (
							`id` int not null  AUTO_INCREMENT PRIMARY KEY,
							`".strtolower($key)."` ".$tipo." not null,
							UNIQUE(`".strtolower($key)."`)
							) ENGINE = ".$this->engine;
							mysql_query($queryCreateCatalogo) or die($queryCreateCatalogo);
							//modificar el tipo para que en table_name sea un integer con foregin key apuntando al catalogo
							$key = $key."_ID";
							$tipo = "int";
						}
					}
					array_push($campos,  "`".strtolower($key)."` ".$tipo." not null");
					if(strtolower($key) == "nombre"){
						array_push($campos, "`alias` varchar(255) not null");
					}
					array_push($indices, "index(`".strtolower($key)."`)");
				}
			}
			//agregar los campos necesarios para la informacion geografica dependiendo de la clase de shp
			switch($this->class){
				case "RecordPolyLine":
					array_push($campos, "`mysql_puntos` multilinestring not null");
					array_push($indices, "SPATIAL INDEX(`mysql_puntos`)");
					array_push($campos, "`text_puntos` longtext not null");
					array_push($campos, "`xmin` int not null");
					array_push($indices, "INDEX(`xmin`)");
					array_push($campos, "`xmax` int not null");
					array_push($indices, "INDEX(`xmax`)");
					array_push($campos, "`ymin` int not null");
					array_push($indices, "INDEX(`ymin`)");
					array_push($campos, "`ymax` int not null");
					array_push($indices, "INDEX(`ymax`)");
					break;
				case "RecordPolygon":
					array_push($campos, "`mysql_puntos` multipolygon not null");
					array_push($indices, "SPATIAL INDEX(`mysql_puntos`)");
					array_push($campos, "`text_puntos` longtext not null");
					array_push($campos, "`xmin` int not null");
					array_push($indices, "INDEX(`xmin`)");
					array_push($campos, "`xmax` int not null");
					array_push($indices, "INDEX(`xmax`)");
					array_push($campos, "`ymin` int not null");
					array_push($indices, "INDEX(`ymin`)");
					array_push($campos, "`ymax` int not null");
					array_push($indices, "INDEX(`ymax`)");
					array_push($campos, "`size` int not null");
					array_push($indices, "INDEX(`size`)");
					break;
				case "RecordPoint":
					array_push($campos, "`mysql_punto` point not null");
					array_push($indices, "SPATIAL INDEX(`mysql_puntos`)");
					break;
			}
			//añadir campos al query
			$query .= implode(", ", $campos).", ";
			$query .= "`fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
			$query .= implode(", ", $indices);
			//primary key
			$query .= ", primary key (`".$this->campoClave."`) ";
			$query .= " ) ENGINE = ".$this->engine;
			mysql_query($query) or die($query);
			/*********Crear table de objetos por imagen ********/
			$query = "CREATE TABLE  `".$this->table_name."_por_imagen` (
			`clave` VARCHAR( 255 ) NOT NULL ,
			`i` INT NOT NULL ,
			`j` INT NOT NULL ,
			`nivel` int(2) NOT NULL,
			PRIMARY KEY(
			     `clave`,
			     `i`,
			     `j`,
			     `nivel`),
			KEY(i, j, nivel)
			) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
			mysql_query($query) or die($query);
			
		}
	}
}
?>