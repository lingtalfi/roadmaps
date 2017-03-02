<?php

use BumbleBee\Autoload\ButineurAutoloader;
use Lang\LangModule;
use Privilege\Privilege;
use Privilege\PrivilegeUser;
use QuickPdo\QuickPdo;


// https://postimg.org/image/ixw7ww9cv/


//--------------------------------------------
// PHP CONF
//--------------------------------------------
ini_set('display_errors', 1);


//------------------------------------------------------------------------------/
// UNIVERSE AUTOLOADER (bigbang)
//------------------------------------------------------------------------------/
require_once __DIR__ . '/class-planets/BumbleBee/Autoload/BeeAutoloader.php';
require_once __DIR__ . '/class-planets/BumbleBee/Autoload/ButineurAutoloader.php';
ButineurAutoloader::getInst()
    ->addLocation(__DIR__ . "/class")
    ->addLocation(__DIR__ . "/class-core")
    ->addLocation(__DIR__ . "/class-modules")
    ->addLocation(__DIR__ . "/class-planets");
ButineurAutoloader::getInst()->start();


//--------------------------------------------
// FUNCTIONS
//--------------------------------------------
require_once __DIR__ . "/functions/main-functions.php";





//--------------------------------------------
// DB
//--------------------------------------------
$dbUser = 'root';
$dbPass = 'root';
$dbName = 'roadmaps';
$host = 'host=localhost';
$host = 'unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock';


//--------------------------------------------
//
//--------------------------------------------
// privilege
$privilegeSessionTimeout = null; // unlimited session

//--------------------------------------------
// PHP
//--------------------------------------------
date_default_timezone_set("Europe/Paris");
ini_set('error_log', __DIR__ . "/logs/php.err.log");
if (null !== $privilegeSessionTimeout) { // or session expires when browser quits
    ini_set('session.cookie_lifetime', $privilegeSessionTimeout);
} else {
    ini_set('session.cookie_lifetime', 10 * 12 * 31 * 86400); // ~10 years
}

session_start();


//--------------------------------------------
// REDIRECTION
//--------------------------------------------
if ('/index.php' === $_SERVER['PHP_SELF']) {
    define('URL_PREFIX', '');
} else {

    define('URL_PREFIX', substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
}


//--------------------------------------------
// DATABASE CONNEXION
//--------------------------------------------
QuickPdo::setConnection("mysql:$host;dbname=$dbName", $dbUser, $dbPass, [
//QuickPdo::setConnection("mysql:host=$host;dbname=$dbName", $dbUser, $dbPass, [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY','')), NAMES 'utf8'",
//    PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES','')), NAMES 'utf8'",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);


//--------------------------------------------
// GENERAL CONFIG
//--------------------------------------------
define('APP_ROOT_DIR', __DIR__);
define('WEBSITE_NAME', "Roadmaps");


Spirit::set('ricSeparator', '--*--');




//--------------------------------------------
// TRANSLATION
//--------------------------------------------
define('APP_DICTIONARY_PATH', APP_ROOT_DIR . "/lang/" . LangModule::getLang("en"));







        
        