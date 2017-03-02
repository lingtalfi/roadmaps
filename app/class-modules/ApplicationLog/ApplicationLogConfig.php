<?php



namespace ApplicationLog;

class ApplicationLogConfig
{

    public static function logPath(){
        return APP_ROOT_DIR . "/logs/nullos.log";
    }

    public static function inYourFaceStyle(){
//        return (true===\Helper::isLocal()); // <-- for young jedi only
        return false; // <-- for old (tired) jedi
    }
}