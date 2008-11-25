<?php
$urlRoot = "../";
include($urlRoot."src/includer.inc.php");
if(isset($_REQUEST["preview"])){
	$satelite = new SateliteProcessing();
	$satelite->preview();
}elseif(isset($_REQUEST["start"])){
	$satelite = new SateliteProcessing();
	$satelite->startProcessing();
	?>
	<script type="text/javascript">
		window.location = "processSatelite.php?start=1";
	</script>
	<?
}else{
	die("no action defined (start, preview)");
}
?>