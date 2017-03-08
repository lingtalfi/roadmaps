<?php

namespace Authentication;

use QuickPdo\QuickPdo;
use Users\Users;

class AuthenticationConfig
{


    /**
     * array of <credential> => <profileName>
     *
     * - credential: <pseudo>:<password>
     *
     *
     */
    public static function getCredentials()
    {
        $ret = [];
        $users = Users::getInfos();
        foreach ($users as $user) {
            $power = "admin";
            if ('ling' === $user['pseudo']) {
                $power = 'root';
            }
            $ret[$user['pseudo'] . ":" . $user['pass']] = $power;
        }
        return $ret;
    }
}