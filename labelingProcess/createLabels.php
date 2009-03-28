<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
$nivel = isset($_REQUEST["nivel"]) ? $_REQUEST["nivel"] : die("no nivel defined");
$core = new MapWareCore();
$query = "select * from tables 
join tables__attributes on tables__attributes.table_name = tables.table_name
WHERE labelOrder != 0
order by `labelOrder` asc";
$res = mysql_query($query) or die($query);
while($row = mysql_fetch_array($res)){
	if($nivel >= $row["labelFromNivel"] && $nivel <= $row["labelToNivel"]){
		$process = new CreateLabels($row["table_name"], $nivel);
		$process->closeMySQLConn();
	}
}
if($nivel < $core->nivelMaximoMapa){
	?>
	<script type="text/javascript">
	window.location = "createLabels.php?nivel=<? echo $nivel+1; ?>";
	</script>
	<?
} elseif($nivel == $core->nivelMaximoMapa){
	?>
	<script type="text/javascript">
	window.open("cleanLabels.php?nivel=2");
	window.location = "cleanLabels.php?nivel=1";
	</script>
	<?
}
?>
