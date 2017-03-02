<?php


namespace Module;

use ArrayStore\ArrayStore;


/**
 * This class originates from the observation that module often needs to store preferences
 * in the app-nullos/assets/modules/$myModule directory using an ArrayStore object.
 *
 * To accommodate the fetching/storing process, I provide this class for module authors.
 * By extending this class, most of the work is done automatically for you.
 *
 * In your concrete class, what is left to do is create the concrete method getDefaultPreferences,
 * which should return an array (the array of the default preferences for your module).
 *
 * This module uses the appropriate naming conventions, so you really don't have to worry about that.
 * Let it to me, and go back to what you was doing before :)
 *
 *
 * One thing to be aware of though: now your config is not ALL in your ModuleConfig class,
 * but rather ALL in your ModuleConfig class EXCEPT for the preferences which are now in your ModulePreferences file.
 * So things are better organized now I guess.
 *
 *
 *
 *
 */
abstract class ModulePreferences
{
    private static $prefStores = [];

    /**
     * You should override this method
     * @return array of default preferences for the module
     */
    public static function getDefaultPreferences()
    {
        return [];
    }



    //------------------------------------------------------------------------------/
    // DON'T TOUCH BELOW THIS LINE
    //------------------------------------------------------------------------------/
    public static function getPreferences()
    {
        $ret = self::getPreferencesStore()->retrieve();
        if (0 === count($ret)) {
            $ret = static::getDefaultPreferences();
        } else {
            $ret = array_replace(static::getDefaultPreferences(), $ret);
        }
        return $ret;
    }


    public static function setPreferences(array $preferences)
    {
        $prefs = self::getPreferences();
        $newPrefs = array_replace_recursive($prefs, $preferences);
        self::getPreferencesStore()->store($newPrefs);
    }


    /**
     * @return ArrayStore
     */
    private static function getPreferencesStore()
    {
        $moduleClassName = get_called_class();
        $p = explode('\\', $moduleClassName);
        $moduleName = $p[0];
        if (false === array_key_exists($moduleName, self::$prefStores)) {
            $prefFile = APP_ROOT_DIR . "/assets/modules/" . lcfirst($moduleName) . '/prefs.php';
            self::$prefStores[$moduleName] = ArrayStore::create()->path($prefFile);
        }
        return self::$prefStores[$moduleName];
    }
}