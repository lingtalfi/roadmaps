<?php


namespace Crud\Util;

use Bat\FileSystemTool;
use QuickPdo\QuickPdoInfoTool;


/**
 * Helps creating the default crud files in app/crud/list
 */
class AbstractCrudGenerator
{

    protected $out;


    public function __construct()
    {
        $this->out = '';
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function line($m)
    {
        $this->out .= $m . PHP_EOL;
    }

    protected function dqe($m)
    {
        return str_replace('"', '\"', $m);
    }


    protected function dqw($m)
    {
        return '"' . $this->dqe($m) . '"';
    }

    protected function sqe($m)
    {
        return str_replace("'", "\\'", $m);
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public static function getForeignKeyPrettierColumns()
    {
        return [
            'equipe' => 'nom',
            'membres' => 'pseudo',
            'videos' => 'titre',
            'users' => 'pseudo',
            'concours' => 'titre',
            'pays' => 'nom',
            'instruments' => 'nom',
            'niveaux' => 'nom',
            'styles_musicaux' => 'nom',
        ];
    }
}