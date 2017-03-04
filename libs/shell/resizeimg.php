<?php
require "config/conf.php";
require "libs/helpers/SimpleImage.php";

mysql_connect($config_sett['dbhost'],$config_sett['dbuser'],$config_sett['dbpass']) or die("MySQL Error: " . mysql_error());
mysql_select_db($config_sett['dbname']) or die("MySQL Error: " . mysql_error());

//Get specific record's images
$c_q = 'SELECT * FROM cms_content WHERE id = "'.$argv[1].'"';
$c_r = mysql_query($c_q);
$c = mysql_fetch_array($c_r);

$q_q = 'SELECT * FROM cms_plugin_structure WHERE id = "'.$argv[2].'"';
$q_r = mysql_query($q_q);
$q = mysql_fetch_array($q_r);

$r_q = 'SELECT photoresize, db_name FROM cms_records WHERE type = "photo" AND recordset = "'.$q['db_name'].'" ORDER BY recordset ASC';
$r_r = mysql_query($r_q);
$rules = array();

while($r = mysql_fetch_array($r_r)) {
	$rule = array();
	$rule['id'] = $argv[1];
	$rule['photoresize'] = $r['photoresize'];
	$rule['db_name'] = $r['db_name'];
	$rule['filename'] = $c[$r['db_name']];

	array_push($rules, $rule);
}

foreach($rules as $da) {

	if(!empty($da['filename']) && !empty($da['photoresize'])) {
		$file = '../uploads/' . $da['id'] . '/' . $da['db_name'] . '/' . $da['filename'];
		$resizes = explode(",",$da['photoresize']);
		if(file_exists($file)) {
			foreach($resizes as $resize) {	
				$a = explode("x",$resize);
				if($a[0]=="auto" || $a[1]=="auto") {
					if($a[0]=="auto") {
						$img = new abeautifulsite\SimpleImage($file);
						$img->fit_to_height($a[1])->save('../uploads/' . $da['id'] . '/' . $da['db_name'] . '/autox' . $a[1] . '_' . $da['filename']);
					} else {
						$img = new abeautifulsite\SimpleImage($file);
						$img->fit_to_width($a[0])->save('../uploads/' . $da['id'] . '/' . $da['db_name'] . '/' . $a[0] . 'xauto_' . $da['filename']);
					}
				} else {
					$img = new abeautifulsite\SimpleImage($file);
					$img->resize($a[0], $a[1])->save('../uploads/' . $da['id'] . '/' . $da['db_name'] . '/' . $a[0] . 'x' . $a[1] . '_' . $da['filename']);
				}
			}
		}
	}
}

// mkdir('hi', 0777);
// //get all plugins that require image resize

// $r_q = 'SELECT DISTINCT recordset, photoresize, db_name FROM cms_records WHERE type = "photo" ORDER BY recordset ASC';
// $r_r = mysql_query($r_q);
// $rules = array();
// while($r = mysql_fetch_array($r_r)) {
// 	$rule = array();
// 	$rule['recordset'] = $r['recordset'];
// 	$rule['photoresize'] = $r['photoresize'];
// 	$rule['db_name'] = $r['db_name'];

// 	array_push($rules, $r);
// }

// print_r($rules);