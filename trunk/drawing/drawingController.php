<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Drawing Controller</title>
</head>

<body>
<?
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
$nivel = isset($_REQUEST["nivel"]) ? $_REQUEST["nivel"] : 1;
$core = new MapWareCore();

$table_name = "calles";

//ver si existe la vista de paths ordenada por el pathCampoTipo de la tabla
$query = "show tables like '".$table_name."_memory1'";
$viewExists = mysql_num_rows(mysql_query($query));
$query = "select * from `".$table_name."_memory1` limit 10";
$cuenta = mysql_query($query);
if($viewExists == 0 || $cuenta == false || mysql_num_rows($cuenta) == 0){
	$createQuery = "CREATE TABLE `".$table_name."_memory0` (
	  `drawOrder` int(11) NOT NULL,
	  `tipo_id` int(11) NOT NULL,
	  `longitud` double NOT NULL,
	  `grupo` int(11) NOT NULL,
	  `clave` varchar(50) NOT NULL,
	  PRIMARY KEY  (`clave`),
	  KEY `drawOrder` (`drawOrder`),
	  KEY `tipo_id` (`tipo_id`),
	  KEY `longitud` (`longitud`),
	  KEY `grupo` (`grupo`),
	  KEY `clave` (`clave`)
	) ENGINE=MEMORY DEFAULT CHARSET=utf8;";
	mysql_query($createQuery);
	
	$query = "insert into `".$table_name."_memory0`
	select `paths_tipos`.`drawOrder`, 
	`".$table_name."`.`tipo_id`, 
	longitud, grupo, clave
	from `".$table_name."`
	JOIN `paths_tipos` on `paths_tipos`.`tipo_id` = `".$table_name."`.`tipo_id`
	ORDER BY `paths_tipos`.`drawOrder` desc";
	mysql_query($query) or die($query);
	
	//memory table 2
	$createQuery = "CREATE TABLE `".$table_name."_memory1` (
	 `drawOrder` int(11) NOT NULL,
	  `tipo_id` int(11) NOT NULL,
	  `longitud` double NOT NULL,
	  `grupo` int(11) NOT NULL,
	  `clave` varchar(50) NOT NULL,
	  PRIMARY KEY  (`clave`),
	  KEY `tipo_id` (`tipo_id`),
	  KEY `longitud` (`longitud`),
	  KEY `grupo` (`grupo`),
	  KEY `clave` (`clave`)
	) ENGINE=MEMORY DEFAULT CHARSET=utf8;";
	mysql_query($createQuery);
	
	$query = "insert into `".$table_name."_memory1`
	select `paths_tipos`.`drawOrder`, 
	`".$table_name."`.`tipo_id`, 
	longitud, grupo, clave
	from `".$table_name."`
	JOIN `paths_tipos` on `paths_tipos`.`tipo_id` = `".$table_name."`.`tipo_id`
	ORDER BY `paths_tipos`.`drawOrder` desc";
	mysql_query($query) or die($query);
}
?>
<script type="text/javascript">
function openDrawing(cpu){
	port = (cpu % 2 == 0) ? "" : ":8888";
	port = ":8888";
	window.open("http://localhost"+port+"/MapWare/processing/drawing/drawImage.php?nivel=<? echo $nivel; ?>&cpu="+cpu);
}
for(i=1; i<=7 ; i++){
	openDrawing(i);
}
</script>
</body>
</html>
