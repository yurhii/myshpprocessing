<?php

class BajaDeClaves extends MapWareCore{
	var $dbf;
	
	function BajaDeClaves(){
		$this->openMySQLConn();
	}
	
	function bajaDeClavesFromDBF($dbfurl, $table_name){
		//sacamos la informacion de la tabla
		$query = "select class, crearImagenesFromNivel, crearImagenesToNivel 
		from tables
		where tables.table_name = '$table_name'";
		$r = mysql_query($query) or die($query);
		if(mysql_num_rows($r) != 0){
			$table_class = mysql_fetch_array($r);
		}else{
			$table_class = false;
		}
		
		$this->dbf = dbase_open($dbfurl, 0);
		for ($i = 1; $i <= dbase_numrecords($this->dbf); $i++) {
			$dbfrow = dbase_get_record_with_names($this->dbf, $i);
			//para cada nivel borramos toda referencia de este elemento
			for($nivel=$table_class["crearImagenesFromNivel"]; $nivel<= $table_class["crearImagenesToNivel"]; $nivel++){
				if($table_class == false || $table_class["class"] != "RecordPolyLine"){
					$tabla_por_imagen = $table_name."_por_imagen";
				}else{
					$tabla_por_imagen = $table_name."_por_imagen_".$nivel; 
				}
				//todas las imagenes asociadas a este elemento
				$query = "update imagenes
				join $tabla_por_imagen on $tabla_por_imagen.i = imagenes.i
				and $tabla_por_imagen.j = imagenes.j
				and $tabla_por_imagen.nivel = imagenes.nivel
				SET aDibujar = '1'
				where $tabla_por_imagen.clave = '".$dbfrow["CLAVE"]."'";
				mysql_query($query) or die($query);
				
				//echo "imagenes afectadas ".mysql_affected_rows()."<br/>";
				
				//borrar las referencias
				$query = "delete from $tabla_por_imagen where clave = '".$dbfrow["CLAVE"]."'";
				mysql_query($query) or die($query);
				
				//borrar el elemento
				
				$query = "delete from $table_name where clave = '".$dbfrow["CLAVE"]."'";
				mysql_query($query) or die($query);
			}
			/*echo "<pre>";
			print_r($dbfrow);
			echo "</pre>";*/
		}
	}
	
}
?>