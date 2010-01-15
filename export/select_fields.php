<? 
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
$core = new MapWareCore();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Exportar paso2</title>
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
<script type="text/javascript">
function getSelectedItem(selectObj){
	var index = selectObj.selectedIndex;
	var opciones = selectObj.getElementsByTagName("option");
	return opciones.item(index);
}
function addAllFields(){
	var f1 = document.getElementById("fields1");
	var f2 = document.getElementById("fields2");
	if(f1.length != 0){
		var longitud = 1*f1.length;
		var opciones = new Array();
		for(j=0; j<longitud; j++){
			opciones[j] = f1.options[j];
		}
		for(i=0; i<longitud; i++){
			f2.add(opciones[i]);
		}
	}
}
function addField(){
	var f1 = document.getElementById("fields1");
	var f2 = document.getElementById("fields2");
	if(f1.length != 0 && f1.selectedIndex != -1){
		var selected = getSelectedItem(f1);
		f2.add(selected, null);
	}
	
}
function removeField(){
	var f1 = document.getElementById("fields1");
	var f2 = document.getElementById("fields2");
	if(f2.length != 0 && f2.selectedIndex != -1){
		var selected = getSelectedItem(f2);
		f1.add(selected, null);
	}
}
function exportar(){
	var f2 = document.getElementById("fields2");
	if(f2.length != 0){
		var a_fields = new Array();
		for(i=0; i<f2.length; i++){
			a_fields.push(f2.options[i].value);
		}
		document.getElementById("fields_value").value = a_fields.join(",");
		return true;
	}else{
		alert("al menos un campo debe exportarse");
		return false;
	}
}
</script>
</head>

<body>
<?
if(!isset($_REQUEST["table"])){
	die("no table");
}
?>
<h1>Elige los campos a exportar</h1>
<form method="post" action="export.php" onsubmit="return exportar()">
<input type="hidden" name="fields" id="fields_value" value="" />
<input type="hidden" name="table" value="<? echo $_REQUEST["table"]; ?>" />
<input type="hidden" name="db" value="<? echo $_REQUEST["db"]; ?>" />
<input type="hidden" name="db_type" value="<? echo $_REQUEST["db_type"]; ?>" />
<table>
	<tr>
		<td><select id="fields1" size="20" style="width:400px;">
		<?
		$query = "show fields from ".$_REQUEST["table"];
		$fields = mysql_query($query) or die($query);
		while($field = mysql_fetch_array($fields)){
			?>
			<option value="<? echo $field["Field"]; ?>"><? echo $field["Field"]; ?></option>
			<?
		}
		?>
		</select></td>
		<td><input onclick="addField()" type="button" value="a&ntilde;adir" /><br />
				<input onclick="removeField()" type="button" value="remover" />
				<input onclick="addAllFields()" type="button" value="all" /></td>
		<td><select id="fields2" size="20" style="width:400px;"></select></td>
		<td><table class="example" cellpadding="0" cellspacing="0">
			<?
			$query = "select * from ".$_REQUEST["table"]." limit 1";
			$res = mysql_fetch_assoc(mysql_query($query));
			if($res != false){
				foreach($res as $key=>$value){
					?><tr>
						<td><? echo $key; ?></td>
						<td><? echo substr($value, 0, 1000); ?></td>
					</tr><?
				}
			}else{
				?><tr>
					<td>No hay informaci&oacute;n disponible</td>
				</tr><?
			}	
			?>
		</table></td>
	</tr>
</table>
<input type="submit" value="Continuar" />
</form>
</body>
</html>
