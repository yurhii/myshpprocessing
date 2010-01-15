<? 
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Exportar paso2</title>
</head>

<body>
<h1>Exportando</h1>
<?
$db_type = "";
$export = new Export();
$sql_file =  $export->generateQuery($db_type);
if($db_type == "MSSQL"){
	?>
	<a href="http://192.168.2.64:8080/DataImporter/export.jsp?file=<? echo $sql_file; ?>&db=<? echo $_REQUEST["db"]; ?>">Archivo Guardado, continuarImportando a MSSQL</a>
	<?
}elseif($db_type == "MySQL"){
	?>
	Continuar manualmente
    <a href="../">Regresar</a>
	<?
}
?>
</body>
</html>
