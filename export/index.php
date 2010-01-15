<? 
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
$core = new MapWareCore();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Exportar paso1</title>
<style type="text/css" media="all">
body{
font-size:14px;
}
select{
font-size:14px;
}
option{
font-size:14px;
}
table{
border:1px solid #999999;
background-color:#F2F6F9;
}
.example tr:hover{
background-color:#5F68B5;
color:#FFFFFF;
}
td{
padding:5px;
border:1px solid #CCCCCC;
}
</style>
</head>

<body>
<h1>Elige una tabla</h1>
<form method="post" action="select_fields.php">
<select name="table">
<?
$query = "show tables";
$tables = mysql_query($query) or die($query);
while($table = mysql_fetch_array($tables)){
	?>
	<option value="<? echo $table["Tables_in_".$core->dataBase]; ?>"><? echo $table["Tables_in_".$core->dataBase]; ?></option>
	<?
}
?>
</select><br />
<h1>Destination DB Type</h1>
<select name="db_type">
<option value="MSSQL">Microsoft SQL Server 2005</option>
<option value="MySQL">MySQL 5.0</option>
</select>
<br/>
<br/>
<select name="db">
<option value="SIGSTATIC">SIGSTATIC</option>
<option value="SIG">SIG</option>
<option value="SIGCCRS">SIGCCRS</option>
</select>
<input type="submit" value="Continuar" />
</form>
</body>
</html>
