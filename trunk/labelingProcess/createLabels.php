<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
if(isset($_REQUEST["nivel"]) && isset($_REQUEST["override"])){
	$nivel = $_REQUEST["nivel"];
	$override = ($_REQUEST["override"] == "1");
	//
	$core = new MapWareCore();
	$query = "select * from tables 
	join tables__attributes on tables__attributes.table_name = tables.table_name
	WHERE labelOrder != 0
	order by `labelOrder` asc";
	$res = mysql_query($query) or die($query);
	while($row = mysql_fetch_array($res)){
		if($nivel >= $row["labelFromNivel"] && $nivel <= $row["labelToNivel"]){
			$process = new CreateLabels($row["table_name"], $nivel, $override);
			$process->closeMySQLConn();
		}
	}
	if($nivel < $core->nivelMaximoMapa){
		?>
		<script type="text/javascript">
		window.location = "createLabels.php?nivel=<? echo $nivel+1; ?>&override=<? echo $_REQUEST["override"]; ?>";
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
}else{
	?>
	<a href="createLabels.php?nivel=1&override=1">Start with Override</a><br>
	<a href="createLabels.php?nivel=1&override=0">Start without Override</a>
	<?
}
?>
