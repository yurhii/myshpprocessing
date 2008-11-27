<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
$core = new MapWareCore();
$table_name = "calles";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Path tipo Manager</title>
<style type="text/css" media="all">
td{
vertical-align:top;
text-align:left;
}
.colorchart{
width:10px;
height:10px;
border:solid 1px #000000;
}
.path_tipo{
border:solid 1px #999999;
padding:20px;
background-color:#F2EFEA;
margin:15px;
}
.tabla_tipo tr td{
text-align:center;
}
</style>
<script type="text/javascript">
function chooseColor(color, tipo_id){
	var colorInput = document.getElementById("color_"+tipo_id);
	colorInput.value = color+"00";
	colorInput.style["background-color"] = "#"+color;
}
</script>
<?
if(isset($_REQUEST["setPathTipo"])){
	$query = "select drawFromNivel, drawToNivel, catalogos
	from tables 
	join tables__attributes on tables__attributes.table_name = tables.table_name
	where tables.table_name = '".$table_name."'";
	$shp_tabla = mysql_fetch_array(mysql_query($query)) or die($query);
	$catalogos = explode(",", $shp_tabla["catalogos"]);
	$shp_tabla["pathCampoTipo"] = $catalogos[0]."_id";
	$campo_de_tipo = str_replace("_id", "", $shp_tabla["pathCampoTipo"]);
	$query = "select catalogo.*, colores.color, colores.descripcion , paths_tipos.id as path_tipo_id
	from ".$table_name."__".$campo_de_tipo."__catalogo as catalogo
	join paths_tipos on paths_tipos.tipo_id = catalogo.id
	left join paths_tipos__colores as colores on colores.path_tipo_id = paths_tipos.id
	order by paths_tipos.drawOrder asc";
	$catalogo = mysql_query($query) or die($query);
	while($tipo = mysql_fetch_array($catalogo)){
		if(isset($_REQUEST["color_".$tipo["path_tipo_id"]])){
			//guard drawORder
			$drawOrder = $_REQUEST["drawOrder_".$tipo["path_tipo_id"]];
			$query = "update paths_tipos set drawOrder = '$drawOrder' where
			id = '".$tipo["path_tipo_id"]."'";
			mysql_query($query) or die($query);
			//guardar colores
			$query = "delete from paths_tipos__colores 
			where path_tipo_id = '".$tipo["path_tipo_id"]."'";
			mysql_query($query) or die($query);
			$query = "insert into paths_tipos__colores 
			(path_tipo_id, color, descripcion)
			values
			('".$tipo["path_tipo_id"]."', '".$_REQUEST["color_".$tipo["path_tipo_id"]]."', '".$_REQUEST["descripcion_".$tipo["path_tipo_id"]]."') ";
			mysql_query($query) or die($query);
			//guaradr longitudes
			for($i=$shp_tabla["drawFromNivel"]; $i<= $shp_tabla["drawToNivel"]; $i++){
				//longitudes minimas
				$long = $_REQUEST["longitud_minima_".$tipo["path_tipo_id"]."_".$i];
				$query = "delete from paths_tipos__length__restrictions
				where nivel = '$i' and path_tipo_id = '".$tipo["path_tipo_id"]."'";
				mysql_query($query) or die($query);
				$query = "insert into paths_tipos__length__restrictions
				(path_tipo_id, nivel, longitud_minima)
				values
				('".$tipo["path_tipo_id"]."', '$i', '$long')";
				mysql_query($query) or die($query);
				//thicks
				$thick = $_REQUEST["thick_".$tipo["path_tipo_id"]."_".$i];
				$thickBkg = $_REQUEST["thickBkg_".$tipo["path_tipo_id"]."_".$i];
				$query = "delete from paths_tipos__thicks
				where nivel = '$i' and path_tipo_id = '".$tipo["path_tipo_id"]."'";
				mysql_query($query) or die($query);
				$query = "insert into paths_tipos__thicks
				(path_tipo_id, nivel, thick, thickBkg)
				values
				('".$tipo["path_tipo_id"]."', '$i', '$thick', '$thickBkg')";
				mysql_query($query) or die($query);
			}	
		}
	}
}
?>
</head>

<body>
<form method="post">
<input type="hidden" name="setPathTipo" value="1" />
<input type="submit" value="Enviar" />
<?
//sacar los datos de shp_Tablas
$query = "select drawFromNivel, drawToNivel, catalogos
from tables 
join tables__attributes on tables__attributes.table_name = tables.table_name
where tables.table_name = '".$table_name."'";
$shp_tabla = mysql_fetch_array(mysql_query($query)) or die($query);
$catalogos = explode(",", $shp_tabla["catalogos"]);
$shp_tabla["pathCampoTipo"] = $catalogos[0]."_id";
$campo_de_tipo = str_replace("_id", "", $shp_tabla["pathCampoTipo"]);
$query = "select catalogo.*, colores.color, colores.descripcion, paths_tipos.id as path_tipo_id, paths_tipos.drawOrder
from ".$table_name."__".$campo_de_tipo."__catalogo as catalogo
join paths_tipos on paths_tipos.tipo_id = catalogo.id
left join paths_tipos__colores as colores on colores.path_tipo_id = paths_tipos.id
order by paths_tipos.drawOrder asc";
$catalogo = mysql_query($query) or die($query);
while($tipo = mysql_fetch_array($catalogo)){
	?>
	<div class="path_tipo">
	<h3 style="font-weight:bold;"><? echo $tipo["path_tipo_id"]." => ".utf8_encode($tipo[$campo_de_tipo]); ?></h3>
	DrawOrder: <input type="text" name="drawOrder_<? echo $tipo["path_tipo_id"]; ?>" value="<? echo $tipo["drawOrder"]; ?>" />
		<table class="tabla_tipo" width="100%" cellpadding="0" cellspacing="0" border="1">
			<tr>
				<td>
					<h3>Color:</h3>
					<? include("color_chart.inc.php"); ?>
					<input style="height:30px; width:230px; background-color:<? echo "#".substr($tipo["color"], 0, 6); ?>;" type="text" name="color_<? echo $tipo["path_tipo_id"]; ?>" id="color_<? echo $tipo["path_tipo_id"]; ?>" value="<? echo $tipo["color"]; ?>"/> <br />
					Nota:7f transparente<br />
					Descripci&oacute;n de color:<br />
					<textarea style="width:230px; height:150px;" name="descripcion_<? echo $tipo["path_tipo_id"]; ?>"><? echo $tipo["descripcion"]; ?></textarea><br />
				</td>
				<td>
					<h3>Longitudes M&iacute;nimas:</h3>
					<?
					//para cada nivel en el que este referenciado este path
					for($i=$shp_tabla["drawFromNivel"]; $i<= $shp_tabla["drawToNivel"]; $i++){
						$query = "select longitud_minima
						from paths_tipos__length__restrictions
						where path_tipo_id = '".$tipo["path_tipo_id"]."' and nivel = '$i'";
						$long = mysql_fetch_array(mysql_query($query)) or die($query);
						?>
						LongMin nivel <? echo $i; ?>: <input type="text" name="longitud_minima_<? echo $tipo["path_tipo_id"]; ?>_<? echo $i; ?>" value="<? echo $long["longitud_minima"]; ?>" /><br />
						<?
					}	
					?>
				</td>
				<td>
					<h3>Thicks:</h3>
					<?
					//para cada nivel en el que este referenciado este path
					for($i=$shp_tabla["drawFromNivel"]; $i<= $shp_tabla["drawToNivel"]; $i++){
						$query = "select thick, thickBkg
						from paths_tipos__thicks
						where path_tipo_id = '".$tipo["path_tipo_id"]."' and nivel = '$i'";
						$thicks = mysql_fetch_array(mysql_query($query));
						?>
						Thick nivel <? echo $i; ?>: <input type="text" name="thick_<? echo $tipo["path_tipo_id"]; ?>_<? echo $i; ?>" value="<? echo $thicks["thick"]; ?>" /> --- 
						ThickBkg nivel <? echo $i; ?>: <input type="text" name="thickBkg_<? echo $tipo["path_tipo_id"]; ?>_<? echo $i; ?>" value="<? echo $thicks["thickBkg"]; ?>" /><br />
						<?
					}	
					?>
				</td>
			</tr>
		</table>
	</div>
	<?
}
?>
</form>
</body>
</html>
