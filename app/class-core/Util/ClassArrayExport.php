<?php

namespace Util;


use ArrayExport\ArrayExport;
use ArrayToString\SymbolManager\PhpArrayToStringSymbolManager;


/**
 * Fix indentation problems when we inject an array into a template class
 */
class ClassArrayExport
{

    public static function export(array $arr)
    {
        return ArrayExport::export($arr, null, function (PhpArrayToStringSymbolManager $manager) {
            $manager->setIndentationCallback(function ($spaceSymbol, $nbSpaces, $level) {
                if (0 === $level) {
                    return str_repeat($spaceSymbol, 8);
                }
                if (1 === $level) {
                    return str_repeat($spaceSymbol, 12);
                }
                return str_repeat($spaceSymbol, 16);
            });
        });
    }
}