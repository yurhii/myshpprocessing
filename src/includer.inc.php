<?php
if(!isset($urlRoot)){
	die("no urlRoot defined");
}
include($urlRoot."src/conexion/ConexionMySQL.inc.php");
include($urlRoot."src/core/MapWareCore.inc.php");
//shp
include($urlRoot."src/shp/ShapeFile.inc.php"); 
include($urlRoot."src/shp/InsertShapeFile.inc.php");
include($urlRoot."src/shp/processShapeFile.inc.php");

include($urlRoot."src/labeling/createLabels.inc.php"); 
include($urlRoot."src/labeling/cleanLabels.inc.php"); 
//
include($urlRoot."src/geometric/LabelBox.inc.php");
//
include($urlRoot."src/drawing/drawImage.inc.php");
include($urlRoot."src/drawing/image.inc.php");
//
include($urlRoot."src/satelite/SateliteProcessing.inc.php");
include($urlRoot."src/hibrido/HibridoProcessing.inc.php");
/*//
include($urlRoot."src/geometric/polygon.inc.php");
//
include($urlRoot."src/cp/InsertFromSepomex.inc.php");
//
include($urlRoot."src/export/export.inc.php");*/
?>