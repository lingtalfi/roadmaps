<?php


namespace Util;

use QuickPdo\QuickPdo;

class GeneralUtil
{
    public static function toDecimal($string)
    {
        if (preg_match('!([0-9]+(,|.[0-9]+)?)!', trim($string), $m)) {
            return str_replace(',', '.', $m[1]);
        }
        return "0.00";
    }


    public static function getDollarToEuroRate()
    {
        return 0.94;
    }

    public static function formatDollar($price, $comma = '.')
    {
        $p = explode($comma, $price);
        $comma = '';
        if (array_key_exists(1, $p)) {
            $comma = '.' . str_pad($p[1], 2, "0", \STR_PAD_RIGHT);
        }
        $main = (string)$p[0];
        $len = strlen($main);
        if ($len > 2) {
            $s = '';
            $rev = strrev($main);
            for ($i = 0; $i < strlen($main); $i++) {
                if (0 === $i % 3) {
                    $s .= ',';
                }
                $s .= $rev[$i];
            }
            $main = strrev($s);
            $main = substr($main, 0, -1); // remove last comma
        }

        return $main . $comma;
    }

    public static function unric($ric)
    {
        $ricSep = '--*--';
        return explode($ricSep, $ric);
    }


    public static function gmMysqlToTime($dateTime)
    {
        $year = substr($dateTime, 0, 4);
        $month = substr($dateTime, 5, 2);
        $day = substr($dateTime, 8, 2);
        $hour = substr($dateTime, 11, 2);
        $minute = substr($dateTime, 14, 2);
        $second = substr($dateTime, 17, 2);
        return gmmktime($hour, $minute, $second, $month, $day, $year);
    }


    public static function debugLog($msg, $type = null)
    {
        $file = "/myphp/roadmaps/app/logs/nullos.log";
        if (null !== $type) {
            $msg = $type . PHP_EOL . $msg;
        }
        $msg .= PHP_EOL . "-------------";
        $msg = date("His: ") . $msg;
        file_put_contents($file, $msg . PHP_EOL, FILE_APPEND);


//        $tasks = QuickPdo::fetchAll("select start_date from task");
//        foreach ($tasks as $task) {
//            if ('00:00:00' !== substr($task['start_date'], -8)) {
//                file_put_contents($file, "-----ERROR IS JUST ABOVE-----", FILE_APPEND);
//            }
//        }


    }
}



