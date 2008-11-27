<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
$nivel = isset($_REQUEST["nivel"]) ? $_REQUEST["nivel"] : die("no nivel");
$clean = new CleanLabels($nivel);
$clean->startCleaning();
$clean->closeMySQLConn();
if($nivel < $clean->nivelMaximoMapa - 1){
	?>
	<script type="text/javascript">
	window.location = "cleanLabels.php?nivel=<? echo $nivel+2; ?>";
	</script>
	<?
} elseif($nivel == $clean->nivelMaximoMapa){
	?>
	<script type="text/javascript">
	window.location = "../drawing/drawingController.php";
	</script>
	<?
}
?>