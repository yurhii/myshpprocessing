<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
if(isset($_REQUEST["nivel"]) && isset($_REQUEST["override"])){
	$nivel = $_REQUEST["nivel"];
	$override = ($_REQUEST["override"] == "1");
	//
	$core = new MapWareCore();
	$query = "select * from tables 
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
	<script type="text/javascript">
	function startWithOverride(){
		if(confirm("Esta seguro que desea empezar el labeling desde cero")){
			window.location = "createLabels.php?nivel=1&override=1";
		}
	}
	</script>
	<a href="#" onClick = "startWithOverride()" >Start with Override</a><br>
	<a href="createLabels.php?nivel=1&override=0">Start without Override</a>
	<?
}
?>
