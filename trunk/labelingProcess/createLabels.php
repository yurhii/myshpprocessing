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
if($nivel < $core->nivelMaximoMapa - 1){
	?>
	<script type="text/javascript">
	window.location = "createLabels.php?nivel=<? echo $nivel+2; ?>";
	</script>
	<?
} elseif($nivel == $core->nivelMaximoMapa || $nivel == $core->nivelMaximoMapa - 1){
	?>
	<script type="text/javascript">
	window.location = "cleanLabels.php?nivel=<?
	if($nivel % 2 == 0){
		echo 2;
	}else{
		echo 1;
	} 
	?>";
	</script>
	<?
}
?>
