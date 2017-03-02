<?php


namespace Crud;

use Crud\Auto\LeftMenuPreferences;

class CrudConfig
{

    private static $tables = null;


    /**
     * Tables allowed by the crud.php script (which displays lists/forms)
     */
    public static function getTables()
    {
        if (null === self::$tables) {
            self::$tables = [

            ];
        }
        return self::$tables;
    }

    public static function getLeftMenuSections()
    {
        return LeftMenuPreferences::getLeftMenuSectionBlocks();
    }

    public static function getLeftMenuSectionsClasses()
    {
        return [];
    }

    public static function getLeftMenuTableLabels()
    {
        return LeftMenuPreferences::getTableLabels();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public static function getLangDir()
    {
        return "modules/crud";
    }

    public static function getCrudUri()
    {
        return "/table";
    }

    public static function getCrudPage()
    {
        return 'modules/crud/crud.php';
    }

    public static function getCrudGeneratorsUri()
    {
        return "/crud-generators";
    }

    public static function getCrudGeneratorsPage()
    {
        return "modules/crud/crud-generators.php";
    }


    public static function getCrudDir()
    {
        return APP_ROOT_DIR . "/crud";
    }


    public static function getCrudGenListDir()
    {
        return self::getCrudDir() . '/auto-list';
    }

    public static function getCrudGenFormDir()
    {
        return self::getCrudDir() . '/auto-form';
    }


    public static function getCrudListDir()
    {
        return self::getCrudDir() . '/list';
    }


    public static function getCrudFormDir()
    {
        return self::getCrudDir() . '/form';
    }


    //--------------------------------------------
    // CRUD GENERATORS PREFERENCES
    //--------------------------------------------
    public static function getCrudFilesPreferencesDir()
    {
        return APP_ROOT_DIR . '/assets/modules/crud';
    }

    public static function getCrudFilesPreferencesAutoFile()
    {
        return self::getCrudFilesPreferencesDir() . '/auto-crud-files-preferences.php';
    }

    public static function getCrudFilesPreferencesUserFile()
    {
        return self::getCrudFilesPreferencesDir() . '/crud-files-preferences.php';
    }

}