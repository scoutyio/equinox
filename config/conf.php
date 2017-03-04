<?php

global $config_sett;
$config_sett = array();

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your Root. Typically this will be your base URL **WITH** a trailing slash:
|
| http://example.com/ OR /
|
*/
define("_SITEROOT_","http://localhost/");
$_SESSION['_SITEROOT_'] = _SITEROOT_;
/*
|--------------------------------------------------------------------------
| Header file
|--------------------------------------------------------------------------
|
| This is your header file for equinox, usually best to call it header.php.
|
*/
define("_SITEBODY_","template/header.php");
/*
|--------------------------------------------------------------------------
| Footer file
|--------------------------------------------------------------------------
|
| This is your footer file for equinox, usually best to call it header.php.
|
*/
define("_SITEFOOT_","template/footer.php");
/*
|--------------------------------------------------------------------------
| Plugin Folder
|--------------------------------------------------------------------------
|
| This is your folder where all of your plugins are kept.
| MUST HAVE A TRAILING "/".
|
*/
define("_PLUG_", _SITEROOT_."plugins/");
/*
|--------------------------------------------------------------------------
| Resource Page
|--------------------------------------------------------------------------
|
| This is your folder where all of your plugins are kept.
| MUST HAVE A TRAILING "/".
|
*/
define("_RES_",_SITEROOT_."assets/");
/*
|--------------------------------------------------------------------------
| Template
|--------------------------------------------------------------------------
|
| This is your folder where all of your plugins are kept.
| MUST HAVE A TRAILING "/".
|
*/
define("_TMPL_",_SITEROOT_."template/");
/*
|--------------------------------------------------------------------------
| 404 PAge
|--------------------------------------------------------------------------
|
| This is your folder where all of your plugins are kept.
| MUST HAVE A TRAILING "/".
|
*/
define("SITE_404","template/404.php");
/*
|--------------------------------------------------------------------------
| Equinox URL
|--------------------------------------------------------------------------
|
| URL to your Equinox Root. Typically this will be your base / Equinox Folder
| **WITH** a trailing slash:
| http://example.com/equinox/
|
*/
define("_EQROOT_",_SITEROOT_."equinox/");
$_SESSION['_EQROOT_'] = _EQROOT_;
/*
|--------------------------------------------------------------------------
| Equinox Public folder
|--------------------------------------------------------------------------
|
| Public folder URL to your Equinox root. Typically this will be your base URL,
| WITH a trailing slash:
|
*/
define("EQ_PUB",_EQROOT_."public/");
$_SESSION['EQ_PUB'] = EQ_PUB;
/*
|--------------------------------------------------------------------------
| Equinox Upload folder
|--------------------------------------------------------------------------
|
| URL to your uploads folder where all components will be uploaded. Typically
| this will be your base URL,
| WITH a trailing slash:
|
*/
define("_UPLOADS_",_SITEROOT_."uploads/");
/*
|--------------------------------------------------------------------------
| Equinox 404 controller
|--------------------------------------------------------------------------
|
| File name of 404 controller. Typically this will be your base URL,
| WITH a trailing slash:
|
*/
define("EQ_404","error");
/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
|
| This is your connection settings to the database.
|
*/
$config_sett['dbhost'] = 'localhost';
$config_sett['dbname'] = 'soccer';
$config_sett['dbuser'] = 'root';
$config_sett['dbpass'] = 'root';
