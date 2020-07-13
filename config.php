<?php
/*
*
* ALL VARIABLES IN THIS FILE MUST BE EDITED ACCORDING TO THE SERVER
* THIS CONFIG FILE WAS CREATE BY:
*   ------------- ELASHMAWY DEV -----------
*   ------- https://elashmawydev.com ------
*
*   =>Edit only the -> //Editable <-
*
*/
//error show
ini_set("display_errors", FALSE);
ini_set("error_log", ROOT . 'errors.log');
ini_set("log_errors", TRUE);


//Database
define('HOST', 'localhost'); //Editable 
define('DB_NAME', 'nqaa'); //Editable
define('DB_USER', 'root'); //Editable
define('DB_PASS', ''); //Editable

define('ROOT_FOLDER', ''); //Editable => set the sub folder for the website {Leave Empty for Root /}

#***** YOU MUST SET THE ROOT_FOLDER LIKE THIS => admin/    OR    nq/

//url
$secured = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'? "https://" : "http://";
define('ROOT_URL', $secured . $_SERVER['HTTP_HOST'] . '/' . ROOT_FOLDER);
define('PUBLIC_URL', $secured . $_SERVER['HTTP_HOST'] . '/' . ROOT_FOLDER . 'public/');
