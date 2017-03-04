<?php

class Database
{

	public function __construct(){

		global $config_sett;
		global $con;

		$con = mysqli_connect($config_sett['dbhost'],$config_sett['dbuser'],$config_sett['dbpass']) or die("MySQL Error: " . mysql_error());
		mysqli_select_db($con, $config_sett['dbname']) or die("MySQL Error: " . mysql_error());
	}
}
