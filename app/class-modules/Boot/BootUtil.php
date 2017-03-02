<?php

namespace Boot;


use FileCreator\FileCreator;

class BootUtil
{


    public static function generateInitTmp(array $tags = [], array $options)
    {
        $values = array_replace([
            'dbNameLocal' => 'my_db',
            'dbUserLocal' => 'root',
            'dbPassLocal' => 'root',
            //
            'dbNameDistant' => 'my_db',
            'dbUserDistant' => 'root',
            'dbPassDistant' => 'root',


            'websiteName' => 'Nullos admin',
            'timezone' => 'Europe/Paris',
            'lang' => 'en',
        ], $tags);


        $useDb = (array_key_exists('useDb', $options)) ? $options['useDb'] : false;


        $c = new FileCreator();
        $c->block('<?php

use BumbleBee\Autoload\ButineurAutoloader;
use Lang\LangModule;
use Privilege\Privilege;
use Privilege\PrivilegeUser;
use QuickPdo\QuickPdo;


//------------------------------------------------------------------------------/
// UNIVERSE AUTOLOADER (bigbang)
//------------------------------------------------------------------------------/
require_once __DIR__ . \'/class-planets/BumbleBee/Autoload/BeeAutoloader.php\';
require_once __DIR__ . \'/class-planets/BumbleBee/Autoload/ButineurAutoloader.php\';
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
');

        $c->space(3);

        if (true === $useDb) {
            $db = '
    // db
    $dbUser = \'' . $values['dbUserLocal'] . '\';
    $dbPass = \'' . $values['dbPassLocal'] . '\';
    $dbName = \'' . $values['dbNameLocal'] . '\';
            ';
            $db2 = '
    // db
    $dbUser = \'' . $values['dbUserDistant'] . '\';
    $dbPass = \'' . $values['dbPassDistant'] . '\';
    $dbName = \'' . $values['dbNameDistant'] . '\';
            ';
        } else {
            $db = '';
            $db2 = '';
        }

        $c->block('
//--------------------------------------------
// LOCAL VS PROD
//--------------------------------------------
if (true === Helper::isLocal()) {
    // php
    ini_set(\'display_errors\', 1);

    ' . $db . '

    // privilege
    $privilegeSessionTimeout = null; // unlimited session
} else {
    // php
    ini_set(\'display_errors\', 0);

    ' . $db2 . '

    // privilege
    $privilegeSessionTimeout = 60 * 5;
}


//--------------------------------------------
// PHP
//--------------------------------------------
date_default_timezone_set("' . self::dquote($values['timezone']) . '");
ini_set(\'error_log\', __DIR__ . "/log/php.err.log");
if (null !== $privilegeSessionTimeout) { // or session expires when browser quits
    ini_set(\'session.cookie_lifetime\', $privilegeSessionTimeout);
}
else{
    ini_set(\'session.cookie_lifetime\', 10 * 12 * 31 * 86400); // ~10 years
}
session_start();



//--------------------------------------------
// REDIRECTION
//--------------------------------------------
if (\'/index.php\' === $_SERVER[\'PHP_SELF\']) {
    define(\'URL_PREFIX\', \'\');
} else {

    define(\'URL_PREFIX\', substr($_SERVER[\'PHP_SELF\'], 0, strrpos($_SERVER[\'PHP_SELF\'], \'/\')));
}

        
        
        ');


        if (true === $useDb) {
            $c->block('
//--------------------------------------------
// DATABASE CONNEXION
//--------------------------------------------
QuickPdo::setConnection("mysql:host=localhost;dbname=$dbName", $dbUser, $dbPass, [
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES \'utf8\'",
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,\'ONLY_FULL_GROUP_BY\',\'\')), NAMES \'utf8\'",
//    PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode=(SELECT REPLACE(@@sql_mode,\'STRICT_TRANS_TABLES\',\'\')), NAMES \'utf8\'",
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);            
            ');
            $c->space(3);
        }


        $c->block('
//--------------------------------------------
// GENERAL CONFIG
//--------------------------------------------
// paths
define(\'APP_ROOT_DIR\', __DIR__);


// website
// used in mail communication and authentication form,
// used in html title, and at the top of the left menu
define(\'WEBSITE_NAME\', "' . self::dquote($values['websiteName']) . '");


Spirit::set(\'ricSeparator\', \'--*--\');


//--------------------------------------------
// PRIVILEGE
//--------------------------------------------
PrivilegeUser::$sessionTimeout = 60 * 5 * 1000;
PrivilegeUser::refresh();
if (array_key_exists(\'disconnect\', $_GET)) {
    PrivilegeUser::disconnect();
    if (\'\' !== $_SERVER[\'HTTP_REFERER\']) {
        header(\'Location: \' . $_SERVER[\'HTTP_REFERER\']);
        exit;
    }
}
Privilege::setProfiles([
    \'root\' => [
        \'*\',
    ],
    \'admin\' => [],
]);


//--------------------------------------------
// TRANSLATION
//--------------------------------------------
define(\'APP_DICTIONARY_PATH\', APP_ROOT_DIR . "/lang/" . LangModule::getLang("' . self::dquote($values['lang']) . '"));







        
        ');


        $file = APP_ROOT_DIR . "/init.php";
        if (false !== file_put_contents($file, $c->render())) {
            return true;
        }
        return false;
    }

    private static function dquote($m)
    {
        return str_replace('"', '\"', $m);
    }

}