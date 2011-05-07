<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Processing Hibrido Controller</title>
</head>

<body>
<script type="text/javascript">
function openDrawing(cpu){
	port = (cpu % 2 == 0) ? "" : ":8888";
	port = ":8888";
	window.open("http://localhost"+port+"/MapWare/shpProcessing/nse/processNSE.php?start=1&nivel=1&cpu="+cpu);
}
for(i=1; i<=7 ; i++){
	openDrawing(i);
}
</script>
</body>
</html>
