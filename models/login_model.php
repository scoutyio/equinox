<?php

class Login_Model extends Model
{
	public function __construct(){

		parent::__construct();
	}

	public function process() {

		global $con;

		//run process of checking the post values of email and passwords when logged in
		$login_query = "SELECT * FROM crm_users WHERE email = '".$_POST['username']."' AND pass = '".$_POST['password']."'";
		$checklogin = mysqli_query($con,$login_query) or die (mysql_error());

		if(mysqli_num_rows($checklogin) == 1) {
			//if user with those credentials exist
			$row = mysqli_fetch_array($checklogin,MYSQLI_ASSOC);

			$_SESSION['x_equi'] = $row['email'];
			$_SESSION['x_equi_status'] = $row['status'];
			$_SESSION['x_equi_user'] = $row['id'];
			$_SESSION['x_equi_name'] = $row['fname'].' '.$row['lname'];
			$_SESSION['x_equi_fname'] = $row['fname'];

			header('Content-type: application/json');
			echo '{ "message": "success" '.(isset($_GET['return'])?',"visitedpage": "'.$_GET['return'].'"':'').'}';
			exit;
		} else {
			//if user with those credentials don't exist
			header('Content-type: application/json');
			echo '{ "message": "<p><i class=\"fa fa-warning fa-1x\"></i><strong>Sorry : </strong>Username or password incorrect</p>" }';
			exit;
		}
	}

	public function logout() {
		//logout function ... unset all exisitng session variables
		unset($_SESSION['x_equi']);
		unset($_SESSION['x_equi_id']);
		unset($_SESSION['x_equi_name']);
		unset($_SESSION['x_equi_status']);
		unset($_SESSION['x_equi_fname']);
		unset($_SESSION['x_equi_user']);
		header( "refresh:0;"._EQROOT_.'login');
	}

}
