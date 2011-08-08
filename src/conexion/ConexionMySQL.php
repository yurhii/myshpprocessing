<?PHP
class ConexionMySQL{
	var $dtb;
	function ConexionMySQL($database = "", $server = "macpro.local:8889", $username = "root", $password = "root"){
		if($database == ""){
	 		$database = $this->database;
		}
		$servidor = $_SERVER["SERVER_NAME"];	
		if(strpos($servidor, "mapware") == false){
			$this->dtb = mysql_connect("localhost:8889", "root", "root") or die("connection failed");
			mysql_select_db($database, $this->dtb) or die( "Unable to select database");
		}else{
			$dtb = mysql_connect("localhost", "lfbarba2_main", "samahil210") or die("no connection to db");
			mysql_select_db("lfbarba2_MapWare", $this->dtb) or die("no db selected");
		}
	}
}
?>