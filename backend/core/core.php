<?php
/**
 * User: Yannick
 * Date: Samstag 26.04.14
 * Time: 10:42
 */

error_reporting(E_ALL);
$corePath = realpath(dirname(__FILE__)) ."/";

//
// Autoloader
//

spl_autoload_register('myAutoloader');
function myAutoloader($class_name)
{
    $corePath = realpath(dirname(__FILE__));

    //
    // Model classes allways start with "Model_" and are stored in /class
    //

    if(substr( $class_name, 0, 6 ) === "Model_"){
        $filename = explode("_",$class_name)[1];
        if(file_exists($corePath."/class/$filename.php")){
            require_once($corePath."/class/" . $filename . ".php");
            return;
        }else{
            //It can happen that no model class exists
        }
    }

    //
    // Manager classes allways contain "Manager" and are stored in /manager
    //

    if (strpos($class_name,'Manager') !== false) {
        $dirname = explode("Manager",$class_name)[0];
        require_once($corePath . "/manager/" . $dirname . "/". $class_name . ".php");
        return;
    }
}

/**
 * Plugins laden
 */
$path = $corePath."plugins/";
$xml = simplexml_load_file($path."plugin.xml");
foreach ($xml as $plugin)
    include $path.$plugin;

/**
 * Datenbankverbindung herstellen
 */
R::setup("mysql:host=127.0.0.1;dbname=rb","root","");