<?php


namespace Crud\Util;

use ArrayExport\ArrayExport;
use Bat\FileSystemTool;
use Crud\CrudConfig;
use QuickPdo\QuickPdoInfoTool;


/**
 * This class generates crud generators preferences for a given database.
 */
class CrudFilesPreferencesGenerator
{


    /**
     * @return bool
     */
    public static function generate($db = null)
    {

        $dst = CrudConfig::getCrudFilesPreferencesAutoFile();


        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }

        $prefs = [
            'actionColumnsPosition' => 'right',
        ];


        //--------------------------------------------
        // TABLES
        //--------------------------------------------
        $tables = QuickPdoInfoTool::getTables($db);
        $fullTables = array_map(function ($v) use ($db) {
            return $db . '.' . $v;
        }, $tables);


        //--------------------------------------------
        // PRETTY TABLE NAMES
        //--------------------------------------------
        $prettyTables = [];
        foreach ($fullTables as $table) {
            if (false !== strpos($table, '_')) {
                $p = explode('.', $table, 2);
                $ftable = array_pop($p);
                $prettyTables[$table] = str_replace('_', ' ', $ftable);
            }
        }
        $prefs['prettyTableNames'] = $prettyTables;


        //--------------------------------------------
        // FOREIGN KEY PRETTIER COLUMNS
        //--------------------------------------------
        $foreignKeyPrettierColumns = CrudGeneratorHelper::generateForeignKeyPrettierColumns($tables);
        $prefs['foreignKeyPrettierColumns'] = $foreignKeyPrettierColumns;


        //--------------------------------------------
        // PRETTY COLUMN NAMES
        //--------------------------------------------
        $cols = [];
        foreach ($tables as $table) {
            $names = QuickPdoInfoTool::getColumnNames($table, $db);
            foreach ($names as $name) {
                if (false !== strpos($name, '_')) {
                    $cleanName = $name;
                    if ('_id' === substr($name, -3)) {
                        $cleanName = substr($name, 0, -3);
                    }
                    $cols[$name] = str_replace('_', ' ', $cleanName);
                }
            }
        }
        $prefs['prettyColumnNames'] = $cols;


        //--------------------------------------------
        // TRANSFORMERS
        //--------------------------------------------
        $prefs['urlTransformerIf'] = function ($c) {
            return (false !== strpos($c, 'url_'));
        };


        $content = '<?php' . PHP_EOL;
        $content .= '$prefs = ';


//        $content .= var_export($prefs, true);
        $content .= ArrayExport::export($prefs);


        $content .= ';' . PHP_EOL;
        return FileSystemTool::mkfile($dst, $content);
    }
}