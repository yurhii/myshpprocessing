<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");
if(isset($_REQUEST["nivel"])){
	$nivel = $_REQUEST["nivel"];
	$cpu = isset($_REQUEST["cpu"]) ? $_REQUEST["cpu"] : 1;
	$draw = new DrawImage($nivel, $cpu);
	if($draw->startDrawing()){
		?>
		<script type="text/javascript">
		window.location = "drawImage.php?nivel=<? echo $nivel; ?>&cpu=<? echo $cpu; ?>";
		</script>
		<?
	}elseif($nivel < $draw->nivelMaximoMapa){
		?>
		<script type="text/javascript">
		//window.close();
		window.location = "drawImage.php?nivel=<? echo $nivel+1; ?>&cpu=<? echo $cpu; ?>";
		</script>
		<?
	}else{
		?>
		<script type="text/javascript">
		window.close();
		</script>
		<?
	}
	$draw->closeMySQLConn();
}else{
	die("no nivel defined");
}
?>