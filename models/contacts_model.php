<?php

class Contacts_Model extends Model {
    
    public function __construct() {
        
        parent::__construct();
    }
    
    public function index() {
        
        $query_q = 'SELECT * FROM crm_contacts ';
        if (!empty($_GET['cats'])) {
            $query_q .= 'WHERE 
                         FIND_IN_SET("' . $_GET['cats'] . '",cats) ';
        }
        if (!empty($_GET['search'])) {
            $query_q .= (isset($_GET['cats']) ? "AND" : "WHERE") . ' (fname LIKE "' . $_GET['search'] . '%" OR lname LIKE "' . $_GET['search'] . '%") ';
        }
        $query_q .= 'ORDER BY 
                     fname ASC, 
                     lname ASC';
        $query = mysql_query($query_q);
        
        $contacts = array();
        while ($row = mysql_fetch_array($query)) {
            
            $contact             = array();
            $contact['id']       = $row['id'];
            $contact['title']    = $row['title'];
            $contact['fname']    = $row['fname'];
            $contact['lname']    = $row['lname'];
            $contact['email']    = $row['email'];
            $contact['password'] = $row['password'];
            $contact['address']  = $row['address'];
            $contact['address2'] = $row['address2'];
            $contact['city']     = $row['city'];
            $contact['postcode'] = $row['postcode'];
            $contact['phone']    = $row['phone'];
            $contact['mobile']   = $row['mobile'];
            $contact['company']  = $row['company'];
            $contact['cats']     = $row['cats'];
            array_push($contacts, $contact);
        }
        return $contacts;
    }
    
    public function add() {
        
        $eqApp = new Apps();
        
        if (isset($_POST['fname'])) {
            
            if (isset($_POST['cats'])) {
                $cats = "";
                $d    = 0;
                foreach ($_POST['cats'] as $key => $value) {
                    $d++;
                    $cats .= $value . ($d < count($_POST['cats']) ? "," : "");
                    
                }
                $_POST['cats'] = $cats;
            } else {
                $_POST['cats'] = '';
            }
            
            $eqApp->insertSql("crm_contacts");
            $getLatest_q = 'SELECT * FROM crm_contacts 
                            ORDER BY 
                            id DESC LIMIT 1';
            $getLatest_r = mysql_query($getLatest_q);
            $getLatest   = mysql_fetch_array($getLatest_r);
            //SEND JSON with LATEST ID ADDED AND REDIRECT TO THAT PAGE
            header('Content-type: application/json');
            echo '{ "message": "success" , "id": "' . $getLatest['id'] . '"  }';
            exit;
        }
        
    }
    
    public function edit($id) {
        
        $eqApp = new Apps();

        if (isset($_POST['fname'])) {
            
            if (isset($_POST['cats'])) {
                $cats = "";
                $d    = 0;
                foreach ($_POST['cats'] as $key => $value) {
                    $d++;
                    $cats .= $value . ($d < count($_POST['cats']) ? "," : "");
                    
                }
                $_POST['cats'] = $cats;
            } else {
                $_POST['cats'] = '';
            }
            $eqApp->udpdateSql("crm_contacts");
            exit;
        } else {
            if ($id) {
                $query_q = 'SELECT * FROM crm_contacts 
                            WHERE 
                            id = "' . $id . '" ';
                $query   = mysql_query($query_q);
                if (mysql_num_rows($query) == 0) {
                    header('Location:' . _EQROOT_ . 'contacts');
                }
                $row = mysql_fetch_array($query);
                return $row;
            } else {
                header('location: ' . _EQROOT_ . 'contacts');
            }
        }
    }
    
    public function delete($id) {
        
        $query_q = 'DELETE FROM crm_contacts 
                    WHERE 
                    id = "' . $id . '" ';
        $query   = mysql_query($query_q);
        header('Location:' . _EQROOT_ . 'contacts');
    }
    
    public function cats() {
        
        $query_q = 'SELECT * FROM 
                    crm_contacts_cats 
                    ORDER BY id DESC';
        $query   = mysql_query($query_q);
        
        $concats = array();
        while ($row = mysql_fetch_array($query)) {
            
            $cat         = array();
            $cat['id']   = $row['id'];
            $cat['name'] = $row['name'];
            array_push($concats, $cat);
        }
        return $concats;
    }
    
    public function addcat() {

        $eqApp = new Apps();
        
        if (isset($_POST['name'])) {

            header('Content-type: application/json');
            $getCat_q = 'SELECT * FROM crm_contacts_cats 
                         WHERE 
                         name = "' . $_POST['name'] . '"';
            $getCat_r = mysql_query($getCat_q);
            if (mysql_num_rows($getCat_r) > 0) {
                echo '{ "message": "error" }';
            } else {
                $eqApp->insertSql("crm_contacts_cats");
                $getLatest_q = 'SELECT * FROM crm_contacts_cats 
                                ORDER BY 
                                id DESC LIMIT 1';
                $getLatest_r = mysql_query($getLatest_q);
                $getLatest   = mysql_fetch_array($getLatest_r);
                echo '{ "message": "success" , "id": "' . $getLatest['id'] . '"  }';
            }
            exit;
        }
    }
    
    public function editcat($id) {

        $eqApp = new Apps();
        
        if (isset($_POST['name'])) {

            header('Content-type: application/json');
            $getCat_q = 'SELECT * FROM crm_contacts_cats 
                         WHERE 
                         name = "' . $_POST['name'] . '" 
                         AND id <> "' . $_POST['id'] . '"';
            $getCat_r = mysql_query($getCat_q);
            if (mysql_num_rows($getCat_r) > 0) {
                echo '{ "message": "error" }';
            } else {
                $eqApp->udpdateSql("crm_contacts_cats");
                echo '{ "message": "success" , "id": "' . $_POST['id'] . '"  }';
            }
            exit;
        } else {
            
            $query_q = 'SELECT * FROM
                        crm_contacts_cats 
                        WHERE id = "' . $id . '" ';
            $query   = mysql_query($query_q);
            if (mysql_num_rows($query) == 0) {
                header('Location:' . _EQROOT_ . 'contacts/cats');
            }
            $row = mysql_fetch_array($query);
            return $row;
        }
    }
}