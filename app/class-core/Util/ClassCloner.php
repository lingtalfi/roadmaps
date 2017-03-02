<?php

namespace Util;

use Bat\FileSystemTool;

class ClassCloner
{

    public static function replicate($tpl, $dst, array $replacements)
    {
        $content = file_get_contents($tpl);
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        FileSystemTool::mkfile($dst, $content);
    }
}