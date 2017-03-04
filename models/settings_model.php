<?php

class Settings_Model extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function fetchAll() {

        global $con;

        $query_q  = 'SELECT * FROM crm_settings';
        $query    = mysqli_query($con,$query_q);
        $compDets = array();
        $compDets['id']               = '';
        $compDets['name']             = '';
        $compDets['email']            = '';
        $compDets['address']          = '';
        $compDets['address2']         = '';
        $compDets['city']             = '';
        $compDets['stateprov']        = '';
        $compDets['postcode']         = '';
        $compDets['logo']             = '';
        $compDets['meta_keywords']    = '';
        $compDets['meta_description'] = '';
        $compDets['phone']            = '';
        $compDets['fax']              = '';
        while ($row = mysqli_fetch_array($query,MYSQLI_ASSOC)) {

            $compDets['id']               = $row['id'];
            $compDets['name']             = $row['name'];
            $compDets['email']            = $row['email'];
            $compDets['address']          = $row['address'];
            $compDets['address2']         = $row['address2'];
            $compDets['city']             = $row['city'];
            $compDets['stateprov']        = $row['stateprov'];
            $compDets['postcode']         = $row['postcode'];
            $compDets['logo']             = $row['logo'];
            $compDets['meta_keywords']    = $row['meta_keywords'];
            $compDets['meta_description'] = $row['meta_description'];
            $compDets['phone']            = $row['phone'];
            $compDets['fax']              = $row['fax'];
        }
        return $compDets;
    }

    public function saveSettings() {

        global $con;

        $eqApp = new Apps();

        if ($_POST['name']) {

            $check_q = "SELECT * FROM crm_settings";
            $check_r = mysqli_query($con,$check_q);
            if (mysqli_num_rows($check_r) == 0) {

                $eqApp->insertSql("crm_settings");
            } else {

                $eqApp->udpdateSql("crm_settings");
            }
        }
    }

    public function saveLogo() {

        if (isset($_FILES['fileInput'])) {

            if (!empty($_FILES['fileInput']["name"])) {

                if ($_FILES['fileInput']["error"] == 0) {

                    $ext = end(explode('.', $_FILES['fileInput']["name"]));
                    if ($ext == "jpg" || $ext == "png" || $ext == "gif" || $ext == "jpeg" || $ext == "flv") {

                        $fileInput = rand() . $_FILES['fileInput']["name"];

                        $check_q = 'SELECT * FROM crm_settings';
                        $check_r = mysqli_query($con,$check_q);
                        $check_n = mysqli_num_rows($check_r);
                        if ($check_n == 0) {
                            $save_q = "INSERT INTO crm_settings (logo) VALUES('" . $fileInput . "')";
                        } else {
                            $check  = mysqli_fetch_array($check_r,MYSQLI_ASSOC);
                            $save_q = "UPDATE crm_settings set logo = '" . $fileInput . "' WHERE id = '" . $check['id'] . "'";
                        }
                        mysqli_query($con,$save_q);
                        $x = move_uploaded_file($_FILES['fileInput']["tmp_name"], "../uploads/" . $fileInput);
                        echo '<img src="' . _SITEROOT_ . 'uploads/' . $fileInput . '" align="center" width="225">';

                    } else {

                    }
                }
            } else {
                $_SESSION['error'] = "No file was selected";
            }
        }
        exit;
    }
}
