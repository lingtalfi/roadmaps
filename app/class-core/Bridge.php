<?php



/**
 * This class serves as a model for any Bridge.
 *
 * It contains the case the methods to handle dynamic instances re-use.
 * Note: if your Bridge doesn't use this feature, then you don't need this model.
 *
 */
class Bridge
{


    private static $instances = [];


    //--------------------------------------------
    // APPLICATION SERVICES
    //--------------------------------------------


    //--------------------------------------------
    // INSTANCES PREPARATION
    //--------------------------------------------
    private static function getBob()
    {
        return new Bob();
    }



    //--------------------------------------------
    // PRIVATE
    //--------------------------------------------
    private static function getInstance($name)
    {
        if (!array_key_exists($name, self::$instances)) {
            self::$instances[$name] = call_user_func('Bridge::get' . $name);
        }
        return self::$instances[$name];

    }


}