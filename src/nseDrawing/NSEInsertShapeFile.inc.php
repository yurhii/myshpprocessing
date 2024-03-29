<?PHP
//este srictp requeiere que max_allowed_packet=500M ( esto en MAMP/bin/startMysql.sh )	
class NSEInsertShapeFile extends MapWareCore{
	var $shp;//ShapeFile
	//esta variable indica si este para estos elementos cartograficos debe haber o no labels
	var $withLabels = false;
	//
	var $table_name = NULL;
	//variable que define el tipo de shp, poligono, path o punto
	var $clase;
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
	function NSEInsertShapeFile($source_url, $tableName){
		$this->openMySQLConn();
		//nos conectamos a la base de nse si estamos en el server
		if(strpos($_SERVER["SERVER_NAME"], "mapware") != false){
			mysql_select_db("lfbarba2_nse");
		}
		//
		$this->pathCampoTipo = "";
		//
		$this->aDibujar = true;
		//dependiendo de si es informacion de detalle (calle, parques, industrias)
		//la dibujaremos y referenciaremos en niveles mayores al nivelMinimoDeDibujadoDeDetalle o no
		$this->nivelInicialDeReferencia = 7;
		$this->nivelFinalDeReferencia = 13;
		$this->withLabels = false;
		$this->table_name = $tableName;
		//definir el campo clave del shape_file
		$this->campoClave = "cv";
		//definir los bordes del area a utulizar en base a las dimensiones del pais
		$this->defineMapWareBounds();
		//define shapeFile from url
		$this->shp = new ShapeFile(SHAPE_FILES.$source_url) or die("no shape file"); // along this file the class will use file.shx and file.dbf
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
		$count_index = 0;
		while($shp_record = $this->shp->fetchOneRecord()){
			$shp_data = $shp_record->shp_data;
			$dbf_data = $shp_record->dbf_data;
			$this->clase = $shp_record->record_class[$shp_record->record_shape_type];
			//
			if(!$first_record){
				$this->crearTablas($shp_record);
				$first_record = true;
			}
			//insertamos el poligono
			$inserted_clave = $this->insertRecordPolygon($shp_data, $dbf_data);
			
			//crear y guardar lasimagenes asociadas a este row
			$upperLeftPoint = array($this->convertXFromShpToMapWare($shp_data["xmin"]),
								$this->convertYFromShpToMapWare($shp_data["ymax"]));
			$lowerRightPoint = array($this->convertXFromShpToMapWare($shp_data["xmax"]),
							$this->convertYFromShpToMapWare($shp_data["ymin"]));
								
			for($nivel = $this->nivelInicialDeReferencia; $nivel <= $this->nivelFinalDeReferencia; $nivel++){
				if($nivel != 0){
					$upperLeft = $this->getCuadroFromPointAtNivel($upperLeftPoint[0], $upperLeftPoint[1], $nivel);
					$lowerRight = $this->getCuadroFromPointAtNivel($lowerRightPoint[0], $lowerRightPoint[1], $nivel);
					//no se requiere de padding ya que l bkg sera transparente
					$padding = 0;
					$this->actualizarEscalaPorNivel($nivel);
					//creamos todas las nse_imagenes necesarias
					for($i=$upperLeft[0] - $padding; $i<=$lowerRight[0] + $padding; $i++){
						for($j=$upperLeft[1] - $padding; $j<=$lowerRight[1] + $padding; $j++){
							//creamos una clave unica por i, j y nivel
							$clave_imagen = $i."_".$j."_".$nivel;
							//insertar imagen
							$query = "insert into Santander.nse_imagenes (i, j, nivel) values ($i, $j, $nivel)";
							mysql_query($query);
							
							
							//tabla de elementos por imagen para poder hacer el dibujado despues
							$query = "insert into Santander.nse_por_imagen 
							(clave, i, j, nivel)
							values
							('$inserted_clave', '$i', '$j', '$nivel')";
							mysql_query($query);
						}
					}
				}
			}
			$count_index++;
			//log de avance
			if($count_index % 500 == 0){
				error_log($count_index." records insertados");
			}
		}
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
				//agregar valores y campos al array correspondiente para el query de insert
				if(strtolower($key) == $this->campoClave){
					array_push($campos, "`clave`");
				}else{
					array_push($campos, "`".$field."`");
				}
				
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
		$query = "insert into Santander.".$this->table_name."
		(".implode(", ", $campos).")
		values
		(".implode(", ", $valores).")";
		$success = mysql_query($query);
		//regresamos la clave del objeto
		return $this->limpiar($dbf_data[strtoupper($this->campoClave)]);
	}
	
	function crearTablas($shp_record){
		$shp_data = $shp_record->shp_data;
		$dbf_data = $shp_record->dbf_data;
		$query = "SHOW TABLES LIKE '".$this->table_name."'";
		//ver si la tabla ya existe de lo contrario crearla
		if(mysql_num_rows(mysql_query($query)) == 0){
			//crear la tabla
			$query = "create table Santander.".$this->table_name." ( ";
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
					
					if(strtolower($key) != $this->campoClave){
						array_push($campos,  "`".strtolower($key)."` ".$tipo." not null");
						array_push($indices, "index(`".strtolower($key)."`)");
					}else{
						array_push($campos,  "`clave` ".$tipo." not null");
					}
					if(strtolower($key) == "nombre"){
						array_push($campos, "`alias` varchar(255) not null");
					}
				}
			}
			//agregar los campos necesarios para la informacion geografica dependiendo de la clase de shp
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
			//añadir campos al query
			$query .= implode(", ", $campos).", ";
			$query .= "`fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, ";
			$query .= implode(", ", $indices);
			//primary key
			$query .= ", primary key (`clave`) ";
			$query .= " ) ENGINE = ".$this->engine;
			mysql_query($query);
			
			
			/*********Crear table de objetos por imagen ********/
			$query = "CREATE TABLE  Santander.".$this->table_name."_por_imagen (
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
			mysql_query($query);
			
			
		}
	}
}
?>