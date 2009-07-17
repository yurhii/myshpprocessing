<?php
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
if(isset($_REQUEST["start"])){
	$nivel = isset($_REQUEST["nivel"]) ? $_REQUEST["nivel"] : 1;
	$cpu = isset($_REQUEST["cpu"]) ? $_REQUEST["cpu"] : 1;
	$tipoProcesamiento = isset($_REQUEST["tipo"]) ? $_REQUEST["tipo"] : 0;
	$hibrido = new HibridoProcessing($nivel, $cpu, $tipoProcesamiento);
	if($hibrido->startProcessingToMatchOurSateliteAssets()){
		?>
		<script type="text/javascript">
			window.location = "processHibrido.php?start=1&nivel=<? echo $nivel; ?>&cpu=<? echo $cpu; ?>&tipo=<? echo $tipoProcesamiento; ?>";
		</script>
		<?
	}elseif($nivel < $hibrido->nivelMaximoMapa){
		?>
		<script type="text/javascript">
			window.location = "processHibrido.php?start=1&nivel=<? echo $nivel + 1; ?>&cpu=<? echo $cpu; ?>&tipo=<? echo $tipoProcesamiento; ?>";
		</script>
		<?
	}elseif($tipoProcesamiento * 1 < 1){
		?>
		<script type="text/javascript">
			window.location = "processHibrido.php?start=1&nivel=1&cpu=<? echo $cpu; ?>&tipo=<? echo $tipoProcesamiento + 1; ?>";
		</script>
		<?
	}else{
		?>
		<script type="text/javascript">
		window.close();
		</script>
		<?
	}
	$hibrido->closeMySQLConn();
}else{
	die("no action defined (start)");
}
?>