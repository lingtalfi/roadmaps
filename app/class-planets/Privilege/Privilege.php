<?php

namespace Privilege;

class Privilege
{

    private static $profiles = [];


    public static function setProfiles(array $profiles)
    {
        self::$profiles = $profiles;
    }

    public static function has($privilege)
    {
        return true;
    }
}