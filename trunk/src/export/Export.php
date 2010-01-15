<?php
class export extends MapWareCore{
	var $sql_file = array();
	var $db_type = "";
	var $fields = "";
	var $table_name = "";
	var $delimiterR = "";
	var $delimiterL = "";
	var $mysql_to_mssql = array("int"=>"int", "text"=>"text", "tinyint"=>"smallint", "varchar"=>"varchar", "date"=>"datetime", "smallint"=>"smallint",
	"mediumint"=>"int", "bigint"=>"int", "float"=>"float", "double"=>"float","decimal"=>"decimal","datetime"=>"datetime","timestamp"=>"timestamp",
	"time"=>"timestamp","year"=>"timestamp", "char"=>"char","tinyblob"=>"image","tinytext"=>"text","blob"=>"image","mediumblob"=>"image",
	"longblob"=>"image","longtext"=>"text","enum"=>"int","set"=>"int","binary"=>"binary","varbinary"=>"varbinary","polygon"=>"polygon",
	"linestring"=>"linestring", "multilinestring"=>"multilinestring","multipolygon"=>"multipolygon", "point"=>"point");
	var $with_parenthise = array("int"=>false, "text"=>false, "tinyint"=>false, "varchar"=>true, "date"=>false, "smallint"=>false,
	"mediumint"=>false, "bigint"=>false, "float"=>false, "double"=>false,"decimal"=>true,"datetime"=>false,"timestamp"=>false,
	"time"=>false,"year"=>false, "char"=>true,"tinyblob"=>false,"tinytext"=>false,"blob"=>false,"mediumblob"=>false,
	"longblob"=>false,"longtext"=>false,"enum"=>false,"set"=>false,"binary"=>true,"varbinary"=>true,"polygon"=>false,"linestring"=>false,
	"multilinestring"=>false,"multipolygon"=>false,"point"=>false);
	function Export(){
		$this->openMySQLConn();
		if(!isset($_REQUEST["fields"])){
			die("no fields");
		}
		$this->fields = explode(",", $_REQUEST["fields"]);
		$this->table_name = $_REQUEST["table"];
		$this->db_type = $_REQUEST["db_type"];
		switch($this->db_type){
			case "MSSQL":
				$this->delimiterL = "[";
				$this->delimiterR = "]";
				break;
			case "MySQL":
				$this->delimiterR = $this->delimiterL = "`";
				break;
		}
	}
	function generateQuery(&$db_type){
		$db_type = $this->db_type;
		$deleteQuery = "drop table $this->table_name;";
		array_push($this->sql_file, $deleteQuery);
		//query para creacion de tabla
		$queryTable = "create table";
		if($this->db_type == "MySQL"){
			$queryTable .=" if not exists ";	
		}
		$queryTable .= $this->delimiterL.$this->table_name.$this->delimiterR." (";
		$a_fields_query = array();
		$a_extra_querys = array();
		for($i=0; $i<count($this->fields); $i++){
			$query = "show fields from $this->table_name like '".$this->fields[$i]."'";
			$field_data = mysql_fetch_array(mysql_query($query)) or die($query);
			$txt = $this->delimiterL.$field_data["Field"].$this->delimiterR." ".$this->getFieldType($field_data["Type"]);
			$txt .= " not null ";
			switch($field_data["Key"]){
				case "PRI":
					$txt .= " primary key ";
					break;
				case "MUL":
					if($field_data["Field"] != "mysql_puntos"){
						array_push($a_extra_querys, "CREATE INDEX ".$field_data["Field"]." ON ".$this->delimiterL.$this->table_name.$this->delimiterR." (".$field_data["Field"].");");
					}else{
						array_push($a_extra_querys, "CREATE SPATIAL INDEX ".$field_data["Field"]." ON ".$this->delimiterL.$this->table_name.$this->delimiterR." (".$field_data["Field"].");");
					}
					break;
				case "UNI":
					/*array_push($a_extra_querys, "CREATE UNIQUE INDEX ".$field_data["Field"]." 
					ON ".$this->delimiterL.$this->table_name.$this->delimiterR." (".$field_data["Field"].");");*/
					break;
			}
			array_push($a_fields_query, $txt);
		}
		$queryTable .= implode(", ", $a_fields_query)." );";
		array_push($this->sql_file, $queryTable);
		array_push($this->sql_file, implode("
		", $a_extra_querys));
		
		//insertado de informacion
		$fields_modified = array();
		for($i=0; $i<count($this->fields); $i++){
			if($this->fields[$i] == "mysql_puntos"){
				array_push($fields_modified, "astext(mysql_puntos)");
			}else{
				array_push($fields_modified, $this->fields[$i]);
			}
			
		}
		$query = "select ".implode(", ", $fields_modified)." from `".$this->table_name."`";
		$res = mysql_query($query) or die($query);
		//cuando haya puntos meteremos dos fields en vez d euno por lo que el offset se incrementara
		$offset = 0;
		while($row = mysql_fetch_row($res)){
			for($i=0; $i<count($row); $i++){
				if($this->fields[$i] == "mysql_puntos"){
					$row[$i] = "geomfromtext('".$row[$i]."')";
				}elseif($this->fields[$i] == "text_puntos"){
					$row[$i] = "geomfromtext('".$row[$i]."')";
				}else {
					$row[$i] = "'".$row[$i]."'";
				}
			}
			$queryInsert = "insert into ".$this->delimiterL.$this->table_name.$this->delimiterR." 
			([".implode("], [", $fields_modified)."])
			values (".implode(", ", $row).");";
			array_push($this->sql_file, $queryInsert);
		}
		//guardar el archivo de exportado
		if(file_exists("sqls/".$this->table_name.".sql")){
			unlink("sqls/".$this->table_name.".sql");
		}
		$output = fopen("sqls/".$this->table_name.".sql", "w");
		fwrite($output, implode("
", $this->sql_file));
		return $this->table_name.".sql";
	}
	function getFieldType($fulltype){
		if(strpos($fulltype, "(") != false){
			$type = substr($fulltype, 0, strpos($fulltype, "("));
			$parenthise = substr($fulltype, strpos($fulltype, "("), 10);
		}else{
			$type = $fulltype;
			$parenthise = "";
		}
		switch($this->db_type){
			case "MSSQL":
				return ($this->with_parenthise[$type]) ? $this->mysql_to_mssql[$type].$parenthise : $this->mysql_to_mssql[$type];
				break;
			case "MySQL":
				return $fulltype;
				break;
		}
	}
}
?>