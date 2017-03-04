<?php

class Users_Model extends Model {

    public function __construct() {

        parent::__construct();
    }

    public function usersList() {
        global $con;
        //function that displays list of users
        $query_q = 'SELECT * FROM crm_users';
        $query_r = mysqli_query($con,$query_q);
        $users   = array();
        while ($row = mysqli_fetch_array($query_r,MYSQLI_ASSOC)) {

            $user           = array();
            $user['id']     = $row['id'];
            $user['fname']  = $row['fname'];
            $user['lname']  = $row['lname'];
            $user['email']  = $row['email'];
            $user['status'] = ($row['status']==1?'Admin':'Staff');
            array_push($users, $user);
        }
        return $users;
    }

    public function addUser() {

        global $con;
        $eqApp = new Apps();
        //function that adds a new user
        if (isset($_POST['fname']) && isset($_SESSION['x_equi'])) {
        	//Run JSON header
            header('Content-type: application/json');
            $checkEmail_q = 'SELECT * FROM crm_users WHERE email = "' . $_POST['email'] . '"';
            $checkEmail_r = mysqli_query($con,$checkEmail_q);
            if (mysqli_num_rows($checkEmail_r) > 0) {
                $result = array("message"=> "fail");
            } else {
                $result = mysqli_query($con,"SHOW TABLE STATUS LIKE 'crm_users'");
                $row    = mysqli_fetch_array($result,MYSQLI_ASSOC);
                $latest = $row['Auto_increment'];
                $eqApp->insertSql("crm_users");
                $result = array("message"=> "fail","id"=>$latest);
            }
            echo json_encode($result);
            exit;
        }
    }

	/**
	 *	Function to edit user by userid values
	 */
    public function editUser($id) {
        //ajax function that fetches a specific user
        if (isset($id)) {

            global $con;

            $query_q = 'SELECT * FROM crm_users WHERE id = "' . $id . '"';
            $query_r = mysqli_query($con,$query_q);
            if (mysqli_num_rows($query_r)) {
                $row = mysqli_fetch_array($query_r,MYSQLI_ASSOC);

                $user           = array();
                $user['id']     = $row['id'];
                $user['fname']  = $row['fname'];
                $user['lname']  = $row['lname'];
                $user['email']  = $row['email'];
                $user['pass']   = $row['pass'];
                $user['status'] = $row['status'];

                return $user;
            } else {
                header('location: ' . _EQROOT_ . 'users');
            }
        } else {

            header('location: ' . _EQROOT_ . 'users');
        }
    }

    /**
  	 * 	Function to save user by POST values
     */
    public function saveUser() {

        global $con;

        $eqApp = new Apps();
        //ajax function that saves a user
        if (isset($_POST['fname'])) {
            //create JSON header
            header('Content-type: application/json');
            //in case they change their email... check if that email already exists
            $checkEmail_q = 'SELECT * FROM crm_users WHERE email = "' . $_POST['email'] . '" AND id <> "' . $_POST['id'] . '"';
            $checkEmail_r = mysqli_query($con,$checkEmail_q);
            if (mysqli_num_rows($checkEmail_r) > 0) {
                //if email exists return fail JSON object
                echo '{ "message": "fail" }';
            } else {
                if(isset($_SESSION['x_equi'])) {
                    //save and overwrite current session objects
                    $_SESSION['x_equi']        = $_POST['email'];
                    $_SESSION['x_equi_status'] = $_POST['status'];
                    $_SESSION['x_equi_name']   = $_POST['fname'] . ' ' . $_POST['lname'];
                    $_SESSION['x_equi_fname']  = $_POST['fname'];
                    $eqApp->udpdateSql("crm_users");
                    echo '{ "message": "success" }';
                }
            }
            exit;
        } else {
            header('location: ' . _EQROOT_);
        }
    }

    /**
     *	Function to delete user by user id
     */
    public function deleteUser($id) {

        global $con;

        if (isset($id)) {
            //function to delete the user by id
            $query_q = 'SELECT * FROM crm_users WHERE id = "' . $id . '" AND id <> "1"';
            $query   = mysqli_query($con,$query_q);
            if (mysqli_num_rows($query)) {
                //delete user and go back to /users
                $delete_q = 'DELETE FROM crm_users WHERE id = "' . $id . '"';
                mysqli_query($con,$delete_q) or die(mysqli_error($con));
                header('location: ' . _EQROOT_ . 'users');
            } else {
                //go back to /users without deleting if id doesnt exist
                header('location: ' . _EQROOT_ . 'users');
            }
        } else {
            //go back to /users if id is empty
            header('location: ' . _EQROOT_ . 'users');
        }
    }
}
