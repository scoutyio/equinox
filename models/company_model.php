<?php

class Company_Model extends Model{
	
	public function __construct(){
	
		parent::__construct();
	}
	
	public function loadCompany(){
		
		//load company details
		$query_q = "SELECT * FROM settings";
		$query_r = mysql_query($query_q);
		$query = mysql_fetch_array($query_r);
		$comp = array();
		$comp['id'] = $query['id'];
		$comp['name'] = $query['name'];
		$comp['email'] = $query['email'];
		$comp['address'] = $query['address'];
		$comp['city'] = $query['city'];
		$comp['stateprov'] = $query['stateprov'];
		$comp['postcode'] = $query['postcode'];
		$comp['phone'] = $query['phone'];
		$comp['fax'] = $query['fax'];
		$comp['meta_keywords'] = $query['meta_keywords'];
		$comp['meta_description'] = $query['meta_description'];
		$comp['logo'] = $query['logo'];
		
		return $comp;
	}
	
	public function update(){
		
		//add or update company
		if(isset($_POST['name'])){
			
			$eqapp = new Apps();
			$check_q = "SELECT * FROM settings";
			$check_r = mysql_query($check_q);
			if(mysql_num_rows($check_r)==0){
				//if a row doesnt exist, then add a row
				$eqapp->insertSql("settings");
			}else{
				//if a row does exist then update it
				$eqapp->udpdateSql("settings");
			}
		}	
	}
	
	public function logo(){
		
		if(isset($_FILES['fileInput'])){
			if(!empty($_FILES['fileInput']["name"])){
				
				if ($_FILES['fileInput']["error"] == 0){
					//get the file's extention
					$ext = end(explode('.',$_FILES['fileInput']["name"]));
					//make sure it is an image
					if($ext == "jpg" || $ext == "png" || $ext == "gif" || $ext == "jpeg" || $ext == "flv"){
						//create random file name
						$fileInput = rand().$_FILES['fileInput']["name"];
			
						if(!isset($_POST['id'])){
							//if there is no row then insert a new row just with logo 
							$save_q = "INSERT INTO settings 
										(
											logo
										) 
									   VALUES
									    (
									    	'".$fileInput."'
									    )";
						}else{
							//if there is a row then update the row with this logo
							$save_q = "UPDATE settings 
									   SET 
									   logo = '".$fileInput."' 
									   WHERE id = '".$_POST['id']."'";
						}
						mysql_query($save_q);
						move_uploaded_file($_FILES['fileInput']['tmp_name'],'resources/uploads/logo/'.$fileInput);
						echo '<img src="'._EQROOT_.'resources/uploads/'.$fileInput.'" align="center" width="225">';
					}
					else{	
						
					}
				}
			}else{
				$_SESSION['error'] ="No file was selected";
			}
		}
		exit;	
	}
}