<?
ini_set("include_path", ini_get("include_path") . ":src");
require_once("MapWareCore.php");
$core = new MapWareCore();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SHP Processing</title>
<script type="text/javascript">
function abrirCreateLabels(){
	window.open("labelingProcess/createLabels.php?nivel=2");
	window.location = "labelingProcess/createLabels.php?nivel=1";
}
</script>
</head>

<body>

<h1>Administrador de procesamiento de shp</h1>
<div><a href="amdin_console/altaBajaShpFiles.php">Dar de alta un nuevo shp file</a></div>

<div><a href="shpProcess/insertNextShp.php">Comenzar a procesar SHP's</a></div>

<div><a style="cursor:pointer;" href="#" onclick="abrirCreateLabels()">Comenzar el creado de labels</a></div>

<div><a href="amdin_console/path_tipo_manager.php">Administrar colores, tama&ntilde;os y orden de paths</a></div>
</body>
</html>