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
if(isset($_REQUEST["dbfurl"]) && isset($_REQUEST["table_name"])){
	$url = $_REQUEST["dbfurl"];
	$table_name = $_REQUEST["table_name"];
	if(!file_exists(SHAPE_FILES.$url)){
		echo "El archivo ingresado no existe. <a href=\"procesarBajasFromDBF.php\">Regresar</a>";
	}else{
		//procesar el dbf
		$process = new BajaDeClaves();
		$process->bajaDeClavesFromDBF(SHAPE_FILES.$url, $table_name);
		
		$process->closeMySQLConn();
	}
	
}else{
	?>
	<form>
	Ingresar el url del dbf<input type="text" name="dbfurl" value="" /><br/>
	Tabla de la cual se removeran las claves<input type="text" name="table_name" value="calles" /><br/>
	<input type="submit" value="Procesar"/>
	</form>
	<?
}
?>
</body>
</html>