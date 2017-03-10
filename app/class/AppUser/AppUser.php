<?php


namespace AppUser;


class AppUser
{


    public static function getUser($throwEx = true)
    {
        if (array_key_exists('user_selected', $_SESSION)) {
            return $_SESSION['user_selected'];
        }
        if (true === $throwEx) {
            throw new \Exception("User not set");
        }
        return false;
    }
}