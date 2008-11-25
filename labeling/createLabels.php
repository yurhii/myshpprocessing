<?PHP
$urlRoot = "../";
include($urlRoot."src/includer.inc.php");
$nivel = isset($_REQUEST["nivel"]) ? $_REQUEST["nivel"] : die("no nivel defined");
$core = new mapWareCore();
$query = "select * from tables 
join tables__attributes on tables__attributes.table_name = tables.table_name
WHERE labelOrder != 0
order by `labelOrder` asc";
$res = mysql_query($query) or die($query);
while($row = mysql_fetch_array($res)){
	if($nivel >= $row["labelFromNivel"] && $nivel <= $row["labelToNivel"]){
		$process = new createLabels($row["table_name"], $nivel);
		$process->closeMySQLConn();
	}
}
if($nivel < $core->nivelMaximoMapa - 1){
	?>
	<script type="text/javascript">
	window.location = "createLabels.php?nivel=<? echo $nivel+2; ?>";
	</script>
	<?
} elseif($nivel == $core->nivelMaximoMapa){
	?>
	<script type="text/javascript">
	window.location = "cleanLabels.php?nivel=1";
	</script>
	<?
}
?>
