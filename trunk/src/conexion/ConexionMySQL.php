<?PHP
class ConexionMySQL{
	var $dtb;
	function ConexionMySQL($database = "", $server = "macpro.local:8889", $username = "root", $password = "root"){
		$this->dtb = mysql_connect($server, $username, $password) or die("connection failed");
		mysql_select_db($database, $this->dtb) or die( "Unable to select database");
	}
}
?>