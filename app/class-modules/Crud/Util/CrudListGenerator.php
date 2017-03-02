<?php


namespace Crud\Util;

use Bat\FileSystemTool;
use Crud\CrudConfig;
use QuickPdo\QuickPdoInfoTool;


/**
 * Helps creating the default crud files in app/crud/list
 */
class CrudListGenerator extends AbstractCrudGenerator
{


    public $foreignKeyPrettierColumns;
    public $prettyTableNames;
    public $fixPrettyColumnNames;
    public $actionColumnsPosition;
    public $urlTransformerIf;
    public $db;

    /**
     * table null means all tables
     */
    public function __construct()
    {
        parent::__construct();
        $this->foreignKeyPrettierColumns = [];
        $this->prettyTableNames = [];
        $this->fixPrettyColumnNames = [];
        $this->actionColumnsPosition = 'right';
        $this->db = null;
    }


    public function generateLists()
    {
        $db = $this->db;
        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }
        $tables = QuickPdoInfoTool::getTables($db);
        $autoListDir = CrudConfig::getCrudGenListDir();
        FileSystemTool::mkdir($autoListDir, 0777, true);
        foreach ($tables as $table) {
            $this->generateList($table, $autoListDir . "/" . $db . '.' . $table . '.php');
        }
    }

    public function generateList($table, $outFile = null)
    {

        $this->out = '';
        $this->line('<?php');
        $this->line('');
        $this->line('use Crud\CrudHelper;');
        $this->line('use Crud\CrudModule;');
        $this->line('');


        $foreignKeyPrettierColumns = $this->foreignKeyPrettierColumns;
        $prettyTableNames = $this->prettyTableNames;
        $fixPrettyColumnNames = $this->fixPrettyColumnNames;
        $fkTransformersInfo = [];

        $db = $this->db;
        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }


        $fullTable = $db . '.' . $table;

        $columnNames = QuickPdoInfoTool::getColumnNames($table, $db);
        $primaryKey = QuickPdoInfoTool::getPrimaryKey($table, $db);
        $autoIncrementedColumn = QuickPdoInfoTool::getAutoIncrementedField($table, $db);
        $fkInfo = QuickPdoInfoTool::getForeignKeysInfo($table, $db);
        $hasForeignKey = (count($fkInfo) > 0);


        //--------------------------------------------
        // WRITE THE FIELDS PART
        //--------------------------------------------
        $fields = [];


        $foreignTables = [];
        $tableAliases = [];
        $mainAlias = null;
        $replacedForeignKeys = [];
        $foreignKeyAliases = [];


        if (false === $hasForeignKey) {
            $fields = $columnNames;
        } else {
            $foreignTables = self::getForeignTables($table, $db);
            $tableAliases = self::getTableAliases($table, $foreignTables);
            $mainAlias = $tableAliases[$table];

            foreach ($columnNames as $c) {
                $fields[] = $mainAlias . '.' . $c;

                if (array_key_exists($c, $foreignTables)) {
                    $foreignTable = $foreignTables[$c];
                    if (array_key_exists($foreignTable, $foreignKeyPrettierColumns)) {
                        $prettyColumnName = $foreignKeyPrettierColumns[$foreignTable];
                        $auxAlias = $tableAliases[$foreignTable];

                        $cleanedForeignTable = $foreignTable;
                        $cleanedForeignTable = explode('.', $cleanedForeignTable);
                        $cleanedForeignTable = array_pop($cleanedForeignTable);


                        $foreignKeyAlias = $cleanedForeignTable . '_' . $prettyColumnName;

                        $fields[] = $auxAlias . '.' . $prettyColumnName . ' as ' . $foreignKeyAlias;
                        $replacedForeignKeys[] = $c;
                        $fkTransformersInfo[] = [
                            $cleanedForeignTable . '_' . $prettyColumnName,
                            $foreignTable,
                            $c,
                        ];
                        $foreignKeyAliases[$c] = $foreignKeyAlias;

                    } else {
                        // todo... fallback to choose a decent column with automated heuristics
                        // in which case don't forget to update $replacedForeignKeys and $fkTransformersInfo too...
                    }
                }
            }

        }

        $this->fields($fields);
        $this->line('');
        $this->line('');

        //--------------------------------------------
        // WRITE THE QUERY PART
        //--------------------------------------------
        $this->line('$query = "select');
        $this->line('%s');
        $al = (true === $hasForeignKey) ? ' ' . $mainAlias : '';
        $this->line('from ' . $fullTable . $al);
        foreach ($foreignTables as $k => $t) {
            $this->line("inner join " . $t . " " . $tableAliases[$t] . " on " . $tableAliases[$t] . '.' . $fkInfo[$k][2] . '=' . $mainAlias . '.' . $k);
        }
        $this->line('";');
        $this->line('');
        $this->line('');
        if (0 === count($primaryKey)) {
            $primaryKey = $columnNames;
        }
        $ric = array_map(function ($v) {
            return "'" . $v . "'";
        }, $primaryKey);
        $this->line('$table = CrudModule::getDataTable("' . $fullTable . '", $query, $fields, [' . implode(', ', $ric) . ']);');
        $this->line('');


        //--------------------------------------------
        // WRITE TITLE
        //--------------------------------------------
        if (array_key_exists($fullTable, $prettyTableNames)) {
            $title = $prettyTableNames[$fullTable];
        } else {
            $p = explode('.', $fullTable, 2);
            $t = array_pop($p);
            $title = str_replace('_', ' ', $t);
        }
        $title = ucfirst($title);
        $this->line('$table->title = "' . $this->dqe($title) . '";');
        $this->line('');
        $this->line('');


        //--------------------------------------------
        // WRITE ACTION COLUMNS POSITION
        //--------------------------------------------
//        $this->line('$table->actionColumnsPosition = "' . $this->actionColumnsPosition . '";');
//        $this->line('');
//        $this->line('');


        //--------------------------------------------
        // COLUMN HEADERS
        //--------------------------------------------
        $headers = [];
        foreach ($columnNames as $c) {
            $column = $c;
            if (array_key_exists($c, $foreignKeyAliases)) {
                $column = $foreignKeyAliases[$c];
            }

            if (array_key_exists($c, $fixPrettyColumnNames)) {
                $headers[$column] = $fixPrettyColumnNames[$c];
            } else {
                $headers[$column] = str_replace('_', ' ', $c);
            }
        }
//        a($fixPrettyColumnNames);
//        a($columnNames);
//        az($headers);


        if (count($headers) > 0) {
            $this->line('$table->columnLabels= [');
            foreach ($headers as $k => $v) {
                $this->line('    "' . $this->dqe($k) . '" => "' . $this->dqe($v) . '",');
            }
            $this->line('];');
            $this->line('');
            $this->line('');
        }


        //--------------------------------------------
        // HIDDEN COLUMNS
        //--------------------------------------------
        $hidden = [];
        if (false !== $autoIncrementedColumn) {
            $hidden[] = $autoIncrementedColumn;
        }
        $hidden = array_merge($hidden, $replacedForeignKeys);
        if (count($hidden) > 0) {
            $this->line('$table->hiddenColumns = [');
            foreach ($hidden as $v) {
                $this->line('    "' . $this->dqe($v) . '",');
            }
            $this->line('];');
            $this->line('');
            $this->line('');
        }


        //--------------------------------------------
        // TRANSFORMERS -- URL
        //--------------------------------------------
        $urlTransformerColumns = [];
        if (is_callable($this->urlTransformerIf)) {
            foreach ($columnNames as $c) {
                if (true === call_user_func($this->urlTransformerIf, $c)) {
                    $urlTransformerColumns[] = $c;
                }
            }
        }
        if (count($urlTransformerColumns) > 0) {
            foreach ($urlTransformerColumns as $v) {
                $this->line('$table->setTransformer(\'' . $this->sqe($v) . '\', function ($v) {');
                $this->line('    return \'<a href="\' . htmlspecialchars($v) . \'">\' . $v . \'</a>\';');
                $this->line('});');
            }
            $this->line('');
            $this->line('');
        }

        //--------------------------------------------
        // TRANSFORMERS -- SHORTEN LONG TEXT
        //--------------------------------------------
        $textColumns = [];
        $types = QuickPdoInfoTool::getColumnDataTypes($fullTable, false);
        foreach ($types as $c => $type) {
            if ('text' === $type) {
                $textColumns[] = $c;
            }
        }
        if (count($textColumns) > 0) {
            $this->line('$n = 30;');
            foreach ($textColumns as $v) {
                $this->line('$table->setTransformer(\'' . $this->sqe($v) . '\', function ($v) use ($n) {');
                $this->line('    return substr($v, 0, $n) . \'...\';');
                $this->line('});');
            }
            $this->line('');
            $this->line('');
        }


        //--------------------------------------------
        // TRANSFORMERS -- FK
        //--------------------------------------------
        if (count($fkTransformersInfo) > 0) {
            foreach ($fkTransformersInfo as $info) {
                $this->line('$table->setTransformer(\'' . $info[0] . '\', function ($v, array $item) {');
                $this->line('    return \'<a href="\' . CrudHelper::getUpdateFormUrl(\'' . $info[1] . '\', $item[\'' . $info[2] . '\']) . \'">\' . $v . \'</a>\';');
                $this->line('});');
                $this->line('');
            }
            $this->line('');
            $this->line('');
            $this->line('');
        }


        //--------------------------------------------
        // PRINT TABLE
        //--------------------------------------------
        $this->line('$table->displayTable();');


        //--------------------------------------------
        // OUTPUT THE RESULT SOMEWHERE
        //--------------------------------------------
        if (null !== $outFile) {
            file_put_contents($outFile, $this->out);
        } else {
            echo $this->out;
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function fields(array $fields)
    {
        $this->line('$fields = \'');
        $c = count($fields);
        foreach ($fields as $i => $field) {
            $comma = ((int)($i + 1) === (int)$c) ? '' : ',';
            $this->line($field . $comma);
        }
        $this->line('\';');
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getTableAliases($table, array $foreignTables)
    {
        $p = explode('.', $table, 2);
        $noDotTable = $table;
        if (2 === count($p)) {
            $noDotTable = $p[1];
        }
        $tableAliases = [
            $table => substr($noDotTable, 0, 1),
        ];
        foreach ($foreignTables as $t) {
            $c = 1;
            do {
                $noDotTable = $t;
                $p = explode('.', $t, 2);
                if (2 === count($p)) {
                    $noDotTable = $p[1];
                }
                $try = substr($noDotTable, 0, $c++);

            } while (in_array($try, $tableAliases, true));
            $tableAliases[$t] = $try;
        }
        return $tableAliases;
    }

    private static function getForeignTables($table, $db = null)
    {
        $fkInfo = QuickPdoInfoTool::getForeignKeysInfo($table, $db);
        return array_map(function ($v) {
            return $v[0] . '.' . $v[1];
        }, $fkInfo);
    }
}