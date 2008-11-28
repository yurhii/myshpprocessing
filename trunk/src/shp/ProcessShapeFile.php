<?php
include("ShapeFile.inc.php"); 
include("InsertShapeFile.inc.php");

class ProcessShapeFile extends MapWareCore{
	var $insertShp;
	//explode array
	var $shp = array();
	var $table = array();
	function ProcessShapeFile(){
		$this->openMySQLConn();
		//tomar un shape file sin procesar
		$query = "SELECT * FROM `shape_files` 
		WHERE `processed` = '0' 
		LIMIT 1";
		$this->shp = mysql_fetch_array(mysql_query($query)) or die("no hay shape files a procesar");
		//ver si ya esta definida una tabla con este nombre con sus datos correspondientes
		$query = "SELECT * from tables 
		join tables__attributes on tables__attributes.table_name = tables.table_name
		WHERE tables.`table_name`= '".$this->shp["table_name"]."'";
		$res = mysql_query($query) or die($query);
		$table_exists = mysql_num_rows($res);
		if($table_exists == 0){
			die("no ha creado una tabla en tables con este shape_file");
		}
		$this->table = mysql_fetch_array($res);	
		//si existe contiudar creando un insert object
		$this->insertShp = new InsertShapeFile(
			$this->shp["id"], $this->shp["url"], 
			$this->table["table_name"], $this->table["labelOrder"] != 0, explode(",", $this->table["catalogos"]), 
			$this->table["crearImagenesFromNivel"], $this->table["crearImagenesToNivel"],
			$this->table["drawLayerOrder"] != 0, $this->shp["campoClave"]
		);
	}
	function startProcessing(){
		$this->insertShp->insertShpData();
		$this->setProcessed();
	}
	function preview($todos){
		$this->insertShp->preview($todos);
	}
	function setProcessed(){
		$query = "update shape_files set processed = '1' where id = '".$this->shp["id"]."'";
		mysql_query($query) or die($query);
	}
}
?>