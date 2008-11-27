<?php
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
if(isset($_REQUEST["start"])){
	$nivel = isset($_REQUEST["nivel"]) ? $_REQUEST["nivel"] : 1;
	$cpu = isset($_REQUEST["cpu"]) ? $_REQUEST["cpu"] : 1;
	$satelite = new HibridoProcessing($nivel, $cpu);
	if($satelite->startProcessingAllNoMatchToOurSateliteAssets()){
		?>
		<script type="text/javascript">
			window.location = "processHibrido.php?start=1&nivel=<? echo $nivel; ?>&cpu=<? echo $cpu; ?>";

		</script>
		<?
	}elseif($nivel < $satelite->nivelMaximoMapa){
		?>
		<script type="text/javascript">
			window.location = "processHibrido.php?start=1&nivel=<? echo $nivel + 1; ?>&cpu=<? echo $cpu; ?>";
		</script>
		<?
	}else{
		?>
		<script type="text/javascript">
		window.close();
		</script>
		<?
	}
	$satelite->closeMySQLConn();
}else{
	die("no action defined (start)");
}
?>