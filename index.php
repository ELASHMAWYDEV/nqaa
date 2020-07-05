<?php

//config file
require 'config.php';
//modules
define(__DIR__, dirname(__FILE__));
define('DS',DIRECTORY_SEPARATOR);
define('ROOT',  __DIR__ . DS);
define('APP', ROOT . 'app' . DS);
define('MODEL', APP . 'model' . DS);
define('CONTROLLER', APP . 'controller' . DS);
define('VIEW', APP . 'view' . DS);
define('CORE', APP . 'core' . DS);
define('AJAX', APP . 'ajax' . DS);
//public folder
define('PUBLIC', ROOT . 'public' . DS);

$modules = [ROOT,APP,MODEL,CONTROLLER,CORE,AJAX];


function class_loader($class_name) {
    global $modules;
    foreach ($modules as $module) {
        if (file_exists($module . $class_name . '.php')) {
            require($module . $class_name . '.php');
            return;
        }
    }
}
spl_autoload_register('class_loader');


new Application;
// var_dump($_SERVER['REQUEST_URI']);