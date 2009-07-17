<?
ini_set("include_path", ini_get("include_path") . ":src");
require_once("MapWareCore.php");
$core = new MapWareCore();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SHP Processing</title>
</head>

<body>

<h1>Administrador de procesamiento de shp</h1>
<div><a href="amdin_console/altaBajaShpFiles.php">Dar de alta un nuevo shp file</a></div>

<div><a href="amdin_console/procesarBajasFromDBF.php">Baja de claves from dbf</a></div>

<div><a href="shpProcess/insertNextShp.php">Comenzar a procesar SHP's</a></div>

<div><a href="labelingProcess/createLabels.php">Comenzar el creado de labels</a></div>

<div><a href="amdin_console/path_tipo_manager.php">Administrar colores, tama&ntilde;os y orden de paths</a></div>

<div><a href="drawingProcess/drawingController.php">Comenzar dibujado</a></div>

<div><a href="sateliteProcess/processSatelite.php">Comenzar procesado de satelite</a></div>

<div><a href="hibridoProcess">Comenzar procesado de hibrido</a></div>
</body>
</html>