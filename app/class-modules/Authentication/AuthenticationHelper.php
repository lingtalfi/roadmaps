<?php

namespace Authentication;

class AuthenticationHelper
{


    /**
     * Test whether the given user-pass pair matches a privileged user.
     *
     * If not, returns false.
     * If so, return the profile name associated with the authenticated privileged user.
     *
     */
    public static function authenticationMatch($user, $pass)
    {
        $credentials = AuthenticationConfig::getCredentials();
        $credential = $user . ':' . $pass;
        if (array_key_exists($credential, $credentials)) {
            return $credentials[$credential];
        }
        return false;
    }


}