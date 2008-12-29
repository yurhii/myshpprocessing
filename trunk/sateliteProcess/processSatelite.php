<?php
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
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
}elseif(isset($_REQUEST["insert_low_def"])){
	$satelite = new SateliteProcessing();
	$satelite->insertLowDefSateliteOriginals();
}elseif(isset($_REQUEST["mosaico"])){
	?>
	<form>
		Folder:<input type="text" name="folder"/></br>
		Filename:<input type="text" name="filename"/></br>
		ImagePrefix:<input type="text" name="imagePrefix"/></br>
		<input type="submit" />
	</form>
	<?
}elseif(isset($_REQUEST["folder"])){
	$satelite = new SateliteProcessing(true);
	$satelite->insertMosaicos($_REQUEST["filename"], $_REQUEST["folder"], $_REQUEST["imagePrefix"]);
}else{
	?>
	<a href="processSatelite.php?start=1">Start</a> </br>
	<a href="processSatelite.php?mosaico=1">mosaico</a> </br>
	<a href="processSatelite.php?insert_low_def=1">insert_low_def</a> </br>
	<a href="processSatelite.php?preview=1">preview</a> </br>
	<?
	die("no action defined (start, preview, insert_low_def, mosaico)");
}
?>