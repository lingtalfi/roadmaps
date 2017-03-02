<?php


use ApplicationLog\ApplicationLogModule;

class Logger
{


    /**
     * msg is of any type, including exception, array, string, ...
     *
     * Target identifier would be used to start special actions on special messages.
     * For instance, a critical error might send a private email to the admin, while
     * other less important errors might just be logged in a file.
     *
     * Personally, I use either the "critical" target identifier, or nothing (let it null).
     * critical will send immediately an email to the admin.
     *
     * Your mileage may vary.
     *
     *
     */
    public static function log($msg, $targetIdentifier = null)
    {
        ApplicationLogModule::log($msg, $targetIdentifier); // quietly log to the log/nullos.log file
    }
}