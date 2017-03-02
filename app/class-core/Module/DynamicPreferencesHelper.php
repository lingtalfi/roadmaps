<?php


namespace Module;


/**
 * I found myself using preferences as cookies, to store very tmp info, like the current
 * number of the page, or things like that.
 *
 * Why not use cookies?
 * I don't know, really...
 *
 * Anyway, preferences works fine, but then checking whether or not the parameter is in the $_GET
 * and if not in the preferences and if not setting a default value is a pain in the ass (or am I getting too old?).
 *
 * So this class helps me (maybe you too?) to have more concise conditions (make the code looks cleaner).
 *
 *
 * This class also forces you to have identical $_GET keys and prefs keys, which is not a bad thing after all.
 *
 * Also by convention, I prefix my key with underscores (just for being able to tell immediately
 * whether or not a parameter if dynamic when I open the asset file).
 *
 */
class DynamicPreferencesHelper
{
    public static function get($key, array $prefs, $default = null)
    {
        if (array_key_exists($key, $_GET)) {
            return $_GET[$key];
        } elseif (array_key_exists('_' . $key, $prefs)) {
            return $prefs['_' . $key];
        }
        return $default;
    }

    public static function getP($key, array $prefs, $default = null)
    {
        if (array_key_exists($key, $_POST)) {
            return $_POST[$key];
        } elseif (array_key_exists('_' . $key, $prefs)) {
            return $prefs['_' . $key];
        }
        return $default;
    }
}