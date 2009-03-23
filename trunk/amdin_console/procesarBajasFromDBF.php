<?
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Baja de claves desde un DBF</title>
</head>

<body>
<?
if(isset($_REQUEST["dbfurl"])){
	$url = $_REQUEST["dbfurl"];
	if(!file_exists($url)){
		echo "El archivo ingresado no existe. <a href=\"procesarBajasFromDBF.php\">Regresar</a>";
	}else{
		//procesar el dbf
		$process = new InsertShapeFile();
		$process->bajaDeClavesFromDBF($url);
		
		$process->closeMySQLConn();
	}
	
}else{
	?>
	<form>
	Ingresar el url del dbf<input type="text" name="dbfurl" value="" /><br/>
	<input type="submit" value="Procesar"/>
	</form>
	<?
}
?>
</body>
</html>