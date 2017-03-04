<?php

// die;

date_default_timezone_set("Europe/London");
ini_set('display_errors', '1');     # don't show any errors...
error_reporting(E_ALL & ~E_STRICT);

ob_start();
session_start();

// Load all libraries!
// Load all helpers!
foreach (glob("libs/*.php") as $filename) {
    require $filename;
}

// Load all helpers!
foreach (glob("libs/helpers/*.php") as $filename) {
    require $filename;
}
// Load
require 'config/conf.php';

//run equinox
$app = new Bootstrap();
