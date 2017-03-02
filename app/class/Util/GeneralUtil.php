<?php


namespace Util;

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
}