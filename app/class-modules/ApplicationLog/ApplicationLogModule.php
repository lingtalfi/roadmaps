<?php


namespace ApplicationLog;

use Bat\ExceptionTool;

class ApplicationLogModule
{


    public static function log($thing, $identifier = null)
    {
        if (true === ApplicationLogConfig::inYourFaceStyle()) { // <---(in your face development style)
            a("LOGGER");
            az(func_get_args());
        }

        $logPath = ApplicationLogConfig::logPath();

        if ($thing instanceof \Exception) {
            $thing = ExceptionTool::toString($thing);
        }
        if (null === $identifier) {
            $identifier = '(no identifier)';
        }
        $thing = $identifier . ' -- ' . date('Y-m-d H:i:s') . PHP_EOL . str_repeat('-', 10) . PHP_EOL . $thing;
        file_put_contents($logPath, $thing . PHP_EOL . PHP_EOL, FILE_APPEND);
    }



    //------------------------------------------------------------------------------/
    // SAAS
    //------------------------------------------------------------------------------/
    public static function decorateLogToWatch(array &$logs)
    {
        $logs["Application log"] = ApplicationLogConfig::logPath();
    }
}