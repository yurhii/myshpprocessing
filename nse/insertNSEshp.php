<?PHP
ini_set("include_path", ini_get("include_path") . ":../src");
require_once("MapWareCore.php");

$insertShp = new NSEInsertShapeFile("agebs/agebs manzana/entregado 6 de mayo.shp", "nse");
$insertShp->insertShpData();


?>