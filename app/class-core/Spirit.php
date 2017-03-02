<?php


/**
 * This is the spirit of the application,
 * it contains the variables of it
 */
Class Spirit
{


    private static $vars = [];


    public static function set($k, $v)
    {
        self::$vars[$k] = $v;
    }

    public static function get($k)
    {
        return self::$vars[$k];
    }

}