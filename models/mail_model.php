<?php

class Mail_Model extends Model{
	
	public function __construct() {
	
		parent::__construct();
	}

	public function fetchCampaigns($id = NULL) {

		$q_q = 'SELECT * FROM crm_mailings';
		if($id) { $q_q .= ' WHERE id = "' . $id . '"'; }
		$q_r = mysql_query($q_q);
		$a = array();

		if($id && mysql_num_rows($q_r) != 1) {
			$eqApp->redirect(_SITEROOT_.'mail');
		}

		while($q = mysql_fetch_array($q_r)){

			$b['id'] = $q['id'];
			$b['name'] = $q['name'];
			$b['subject'] = $q['subject'];
			$b['date'] = $q['date'];
			$b['fromname'] = $q['fromname'];
			$b['fromemail'] = $q['fromemail'];
			$b['cats'] = $q['cats'];
			$b['body'] = $q['body'];
			$b['status'] = $q['status'];
			$b['attachment'] = $q['attachment'];

			array_push($a,$b);
		}
		return $a;
	}
	
	public function createCampagin($id = null) {


		if(!empty($_POST['name']) && !empty($_POST['fromname']) && !empty($_POST['fromemail']) && !empty($_POST['contact']) && !empty($_POST['subject']) && !empty($_POST['body'])) {

	        $contact 	  = implode(",",$_POST['contact']);
	        $allowedFiles = array("jpg","png","gif","jpeg","doc","docx","xls","zip");

	        $arrResult 			= array();
	        $arrResult['error'] = 0;

	        if(isset($id)) {
	        	$d = $this->fetchCampaigns($id);
	        }

			if(!empty($_FILES['file']['name'])) {
				if($_FILES['file']['size'] <= 10000000 && $_FILES['file']["error"] == 0){

					$ext = end(explode('.',$_FILES['file']["name"]));
					if (!in_array($ext,$allowedFiles)){

						$arrResult['error'] = 1;
						$arrResult['attach'] = 1;
						$arrResult['message'] = 'Unsuccessful, the file you attached is not allowed.';

					} else {

						// if($id &&)
						if($id) {
							if(!empty($d[0]['attachment'])) {
								unlink('../uploads/mail/' . $d[0]['attachment']);
							}
						}

						$fileInput = rand().$_FILES['file']["name"];

						if (!file_exists('../uploads/')) {
                            mkdir('../uploads/', 0777);
                        }

                        if (!file_exists('../uploads/mail/')) {
                            mkdir('../uploads/mail/', 0777);
                        }                        

                        move_uploaded_file($_FILES['file']['tmp_name'],'../uploads/mail/' . $fileInput);
                        
						$x = move_uploaded_file($_FILES['file']["tmp_name"], "../uploads/" . $fileInput);

			            $arrResult['error'] = 0;
						$arrResult['attach'] = 1;
						$arrResult['message'] = 'Successfully created with attachment';
						$arrResult['file'] = $fileInput;
                    }
				} else {

					$arrResult['error'] = 1;
					$arrResult['attach'] = 1;
					$arrResult['message'] = 'Unsuccessful, the file you attached is too big. Less than 10MB.';
				}
			}
			if(!$id){ 
				$query_q = 'INSERT INTO crm_mailings(
	        				name,
	        				fromname,
							fromemail,
							subject,
							body,
							date,
							cats,
							status'.
							($arrResult['attach']?',attachment':'').') VALUES (
							"'.$_POST['name'].'",
							"'.$_POST['fromname'].'",
							"'.$_POST['fromemail'].'",
							"'.$_POST['subject'].'",
							"'.$_POST['body'].'",
							"'.date("Y-m-d H:i:s").'",
							"'.$contact.'",
							"1"
							'.($arrResult['attach'] && !$arrResult['error']?',"'.$fileInput.'"':'').')';
			} else {
				$query_q = 'UPDATE crm_mailings SET
			        				name = "'.$_POST['name'].'",
			        				fromname = "'.$_POST['fromname'].'",
									fromemail = "'.$_POST['fromemail'].'",
									subject = "'.$_POST['subject'].'",
									body = "'.$_POST['body'].'",
									date = "'.date("Y-m-d H:i:s").'",
									cats = "'.$contact.'"
									'.($arrResult['attach'] && !$arrResult['error']?',attachment = "'.$fileInput.'"':'').'
								WHERE id = "'.$id.'"';

			}
			
			
			if($arrResult['error'] == 0) {
				mysql_query($query_q) or die (mysql_error());
			}
		} else {

			$arrResult = array(
                'response' => 'error',
                'message' => 'incomplete'
            );
		}	

		header('Content-type: application/json');
		echo json_encode($arrResult);

		exit;
	}

	public function send($id) {

		$q = 'UPDATE crm_mailings SET status = "0" WHERE id = "'.$id.'"';

		mysql_query($q);

		header('Content-type: application/json');
		echo json_encode(array('response'=>'success','redirect'=>'mail/view/'.$id));

		exit;
	}



	public function sendMail(){

		// var_dump($_FILES);
		// die;


	    $email_to      = $_POST['to']; // The email you are sending to (example)
		$email_from    = $_POST['from']; // The email you are sending from (example)
		$email_subject = "subject line"; // The Subject of the email
		$email_txt     = stripslashes($_POST['body']); // Message that the email has in it
		$fileatt       = $_FILES['file']['tmp_name']; // Path to the file (example)
		$fileatt_type  = $_FILES['file']['type']; // File Type
		$fileatt_name  = $_FILES['file']['name']; // Filename that will be used for the file as the attachment
		$file          = fopen($fileatt,'rb');
		$data          = fread($file,filesize($fileatt));
		fclose($file);
		$semi_rand     = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		$headers       ="From: $email_from"; // Who the email is from (example)
		$headers      .= "\nReply-To: $email_from\n".
						 "Return-Path: $email_from\n".
						 "MIME-Version: 1.0\n" .
						 "Content-Type: multipart/mixed;\n" .
						 "boundary=\"{$mime_boundary}\"";
		
		$email_message .= "This is a multi-part message in MIME format.\n\n" .
						  "--{$mime_boundary}\n" .
						  "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
						  "Content-Transfer-Encoding: 7bit\n\n" . $email_txt . "\n\n";

		$data = chunk_split(base64_encode($data));

		$email_message .= "--{$mime_boundary}\n" .
						  "Content-Type: {$fileatt_type};\n" .
						  " name=\"{$fileatt_name}\"\n" .
						  "Content-Transfer-Encoding: base64\n\n" .
							$data . "\n\n" .
						  "--{$mime_boundary}--\n";

		mail($email_to,$email_subject,$email_message,$headers,"-f".$_POST['from']);
		die;
		//  $headers =  'MIME-Version: 1.0' . "\r\n";
		//  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		//  $headers .= 'From: eddythemeddy@gmail.com' . "\r\n";
		//  $headers .= "Reply-To: eddythemeddy@gmail.com\r\n";
		//  $headers .= "Return-Path: eddythemeddy@gmail.com\r\n";;
		//  $headers .= "X-Priority: 3\r\n";
		//  $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

		//  // Email Variables
		//  $toUser  = "Ilan Slazenger <eddythemeddy@gmail.com>"; // recipient
		//  $subject = "Email from clinic.amoneni.co.uk"; // subject
		//  $body    = stripslashes($_POST['body']); // content

		//  if (mail($toUser,$subject,$body,$headers,"-feddythemeddy@gmail.com")) {
		//      echo "sent";
		//  } else {
		//      echo "failed";
		//  }
	}
}

