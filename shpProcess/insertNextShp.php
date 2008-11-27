<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
if(isset($_REQUEST["process"]) || isset($_REQUEST["preview"])){
	$process = new ProcessShapeFile();
	if(isset($_REQUEST["process"])){
		$process->startProcessing();
		?>
		<script type="text/javascript">
		window.location = "insertNextShp.php?process=1";
		</script>
		<?
	}else if(isset($_REQUEST["preview"])){
		$process->preview(isset($_REQUEST["all"]));
	}
	$process->closeMySQLConn();
}else{
	?>
	<h1>Dar de alta nuevos SHP</h1>
	<a href="insertNextShp.php?preview=1">Preview</a><br>
	<a href="insertNextShp.php?process=1">Process</a>
	<?
}
?>