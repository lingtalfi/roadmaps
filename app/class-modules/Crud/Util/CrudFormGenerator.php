<?php


namespace Crud\Util;

use ArrayToString\ArrayToStringUtil;
use ArrayToString\SymbolManager\InlineArgsArrayToStringSymbolManager;
use Bat\FileSystemTool;
use Crud\CrudConfig;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoInfoTool;


/**
 * Helps creating the default crud files in app/crud/form.
 *
 *
 * Mapping a sql column to a (QuickForm) type
 * --------------------------------------------
 * The approach here is the following:
 *
 * - if the column is specified in column2Type, it will be used (let the user override any guessed choice)
 *      - the column can be prefixed with a table name (using the table.column notation)
 *
 * - otherwise, the internal CrudFormGenerator algorithm will be used:
 *      - foreign keys are converted to select
 *      - else, the sqlType2Type map is applied
 *      - else, if it's an enum
 *          - if the number of items is less or equal to 3, a radioList will be used
 *          - if the number of items is more than 3, a select will be used
 *      - else, if everything fails, text will be used
 *
 *
 * In every cases, the column type can be either:
 * - a string, the column type
 * - an array:
 *      - 0: string, the column type
 *      - 1: array, the type arguments
 *
 *
 *
 * Mapping a sql column to a constraint
 * --------------------------------------------
 * The algorithm is:
 *
 * - if there is a value defined in the column2Constraint public property array, use it (see the column2Constraint comments below for more details on the format)
 * - else, if the column is  in the foreignKeyPrettierColumns array, add "required" constraint
 * - else, no constraint
 *
 *
 * Mapping a sql column to a default values
 * --------------------------------------------
 * The algorithm is:
 * - if there is a default value defined in the defaultValues public property array, use it
 * - otherwise, use the default values from the database
 *      - but if it's null, and the column is of type int|tinyint, and it's not a foreign key, we add a default value of 0 (case of the active column)
 *
 */
class CrudFormGenerator extends AbstractCrudGenerator
{


    public $foreignKeyPrettierColumns;
    public $prettyTableNames;
    public $fixPrettyColumnNames;
    /**
     * array of enumLabel => [value, label] // injected in a radio|select
     *
     * enumLabel is either:
     * - the enum label as written in the sql database
     * - or {column}.{enum} is also accepted
     * - or {table}.{column}.{enum} is also accepted
     *
     *
     */
    public $enumInfo;

    // types
    public $sqlType2Type;

    /**
     *
     * The keys are the name of the columns. They can be prefixed with the table name, using the (table.column) notation.
     *
     *
     * In column2Type and sqlType2Type arrays, the type value can either a string or an array.
     * If it's a string, it's the type, if it's an array, it must contain two entries:
     *
     * - 0: type (string)
     * - 1: typeArgs (array)
     *
     */
    public $column2Type;

    // constraints
    /**
     *
     * You can add your own constraints via the column2Constraint array,
     * which keys are a target, and which values represent the constraint to apply.
     *
     * <target> => <constraint>,
     * ...
     *
     *
     * Target is a column name; it can be prefixed by a table name followed by a dot.
     *
     * - target: <column> | <table> <.> <column>
     *
     * The constraint is either a string, or an array.
     * If it's a string, it is the constraint name.
     * The array let you define constraint arguments, the syntax is the following:
     *
     * - array: [ str:constraintName, array:constraintArgs ]
     *
     *
     * As for now, constraints do not apply on foreign keys.
     *
     * As for now, you can only apply one constraint per column (remember that this is just a helper,
     * so it gives you roughly where you want to go, but you still need to customize things a bit).
     *
     *
     */
    public $column2Constraint;

    /**
     * array of <column> => <defaultValue>
     *  - column can be either:
     *      - string, a column name
     *      - string, a full column name: {table}.{column}
     * - defaultValue: if null, it's ignored
     *
     */
    public $defaultValues;

    public $db;


    //--------------------------------------------
    //
    //--------------------------------------------
    private $arrToStringTool;
    //--------------------------------------------
    // TMP VARIABLES
    //--------------------------------------------
    private $_table;
    private $_fkInfo;
    private $_foreignKeyPrettierColumns;
    private $_columnTypes;
    private $_enums;
    private $_primaryKey;
    private $_db;
    private $_defaultValues;


    /**
     * table null means all tables
     */
    public function __construct()
    {
        parent::__construct();
        $this->foreignKeyPrettierColumns = null;
        $this->prettyTableNames = [];
        $this->fixPrettyColumnNames = [];
        $this->defaultValues = [];
        $this->column2Type = [];
        $this->sqlType2Type = [
            'varchar' => 'text',
            'tinyint' => 'text',
            'int' => 'text',
//            'enum' => 'text', // enum have a special treatment
            'char' => 'text',
            'text' => 'message',
            'datetime' => 'date6',
            'date' => 'date3',
        ];
        $this->column2Constraint = [];
        $this->enumInfo = [];
        $this->db = null;
        $this->arrToStringTool = ArrayToStringUtil::create()->setSymbolManager(new InlineArgsArrayToStringSymbolManager());
    }


    public function generateForms()
    {
        $db = $this->db;
        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }

        $tables = QuickPdoInfoTool::getTables($db);
        $autoFormDir = CrudConfig::getCrudGenFormDir();
        FileSystemTool::mkdir($autoFormDir, 0777, true);
        foreach ($tables as $table) {
            $this->generateForm($table, $autoFormDir . "/" . $db . '.' . $table . '.php');
        }
    }

    public function generateForm($table, $outFile = null)
    {
        $this->_table = $table;
        $this->_fkInfo = null;
        $this->_foreignKeyPrettierColumns = null;
        $this->_columnTypes = null;
        $this->_enums = null;
        $this->_primaryKey = null;

        // initialize temp variables
        $this->_db = $this->db;
        if (null === $this->_db) {
            $this->_db = QuickPdoInfoTool::getDatabase();
        }
        $columnNames = QuickPdoInfoTool::getColumnNames($table, $this->_db);
        $this->_primaryKey = QuickPdoInfoTool::getPrimaryKey($table, $this->_db);
        $autoIncrementedColumn = QuickPdoInfoTool::getAutoIncrementedField($table, $this->_db);
        $this->_fkInfo = QuickPdoInfoTool::getForeignKeysInfo($table, $this->_db);
        $this->_defaultValues = QuickPdoInfoTool::getColumnDefaultValues($table);
        $this->_foreignKeyPrettierColumns = $this->foreignKeyPrettierColumns;
        if (null === $this->_foreignKeyPrettierColumns) {
            $this->_foreignKeyPrettierColumns = CrudGeneratorHelper::generateForeignKeyPrettierColumns();
        }
        $prettyTableNames = $this->prettyTableNames;
        $fixPrettyColumnNames = $this->fixPrettyColumnNames;
        $columnFullTypes = QuickPdoInfoTool::getColumnDataTypes($table, true);
        $this->_enums = [];
        $this->_columnTypes = [];
        foreach ($columnFullTypes as $name => $type) {
            if (0 === strpos($type, 'enum')) {
                // convert the enum to an actual php array
                $s = substr($type, 5, -1);
                $p = explode(',', $s);
                $enum = array_map(function ($v) {
                    /**
                     * From my personal tests with:
                     * - show columns from users;
                     * - alter table users add sexe enum("a'e", 'a"t', 'bk\'k', 'op', "i''i") not null;
                     * - alter table users drop sexe;
                     *
                     * Mysql does not escape the double quotes, it only escape single quote using the mechanism
                     * of doubling it:
                     *
                     * +---------------------+------------------------------------------+------+-----+---------+----------------+
                     * | Field               | Type                                     | Null | Key | Default | Extra          |
                     * +---------------------+------------------------------------------+------+-----+---------+----------------+
                     * | id                  | int(11)                                  | NO   | PRI | NULL    | auto_increment |
                     * | ...
                     * | sexe                | enum('a''e','a"t','bk''k','op','i''''i') | NO   |     | NULL    |                |
                     * +---------------------+------------------------------------------+------+-----+---------+----------------+
                     *
                     */

                    return str_replace("''", "'", substr($v, 1, -1));
                }, $p);
                $this->_enums[$name] = $enum;
            }
            $type = explode('(', $type, 2)[0];
            $this->_columnTypes[$name] = $type;
        }


//        a("table", $table);
//        a("fkInfo", $this->_fkInfo);
//        a("foreignKeyPrettierColumns", $foreignKeyPrettierColumns);
//        a("prettyTableNames", $prettyTableNames);
//        a("columnTypes", $columnTypes);
//        a("enums", $enums);
//        a("defaultValues", $defaultValues);
//        az();

        $this->out = '';
        $this->line('<?php');
        $this->line('');
        $this->line('');
        $this->line('use Crud\CrudModule;');
        $this->line('');


        //--------------------------------------------
        // INSTANTIATION
        //--------------------------------------------
        if (0 === count($this->_primaryKey)) {
            $this->_primaryKey = $columnNames;
        }

        $ric = array_map(function ($v) {
            return "'" . $v . "'";
        }, $this->_primaryKey);


        $this->line('$form = CrudModule::getForm("' . $this->_db . '.' . $table . '", [' . implode(', ', $ric) . ']);');
        $this->line('');
        $this->line('');
        $this->line('');


        //--------------------------------------------
        // LABELS
        //--------------------------------------------
        $labels = [];
        foreach ($columnNames as $c) {
            if (array_key_exists($c, $fixPrettyColumnNames)) {
                $labels[$c] = $fixPrettyColumnNames[$c];
            } else {
                $labels[$c] = str_replace('_', ' ', $c);
            }
        }
        if (count($labels) > 0) {
            $this->line('$form->labels = [');
            foreach ($labels as $k => $v) {
                $this->line('    "' . $this->dqe($k) . '" => "' . $this->dqe($v) . '",');
            }
            $this->line('];');
            $this->line('');
            $this->line('');
        }


        //--------------------------------------------
        // WRITE TITLE
        //--------------------------------------------
        if (array_key_exists($table, $prettyTableNames)) {
            $title = $prettyTableNames[$table];
        } else {
            $title = str_replace('_', ' ', $table);
        }
        $title = ucfirst($title);
        $this->line('$form->title = "' . $this->dqe($title) . '";');
        $this->line('');
        $this->line('');


        //--------------------------------------------
        // ADD CONTROLS
        //--------------------------------------------
        $controls = [];
        foreach ($columnNames as $k => $c) {
            if ($autoIncrementedColumn !== $c) {
                $controls[] = $c;
            }
        }


        $controlsInfo = [];
        foreach ($controls as $name) {
            $controlsInfo[$name] = [
                'typeInfo' => $this->getTypeInfo($name),
                'constraintInfo' => $this->getConstraintInfo($name),
                'defaultValue' => $this->getDefaultValue($name),
            ];
        }


        foreach ($controlsInfo as $name => $cInfo) {
            $this->displayControl($name, $cInfo);
        }




        //--------------------------------------------
        // PRINT FORM
        //--------------------------------------------
        $this->line('');
        $this->line('');
        $this->line('$form->display();');


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

    private function arrayToString(array $arr)
    {
        return $this->arrToStringTool->toString($arr);
    }

    private function argumentsToLine(array $args)
    {
        $s = '';
        $i = 0;
        foreach ($args as $v) {
            if ($i++ > 0) {
                $s .= ', ';
            }
            if (is_array($v)) {
                $s .= $this->arrayToString($v);
            } else {
                if (is_string($v)) {
                    $s .= $this->dqw($v);
                } else {
                    // int, float
                    $s .= $v;
                }
            }
        }
        return $s;
    }


    private function displayControl($name, $info)
    {
        $typeInfo = $info['typeInfo'];
        $constraintInfo = $info['constraintInfo'];
        $defaultValue = $info['defaultValue'];


        $line = '';

        //--------------------------------------------
        // TYPE
        //--------------------------------------------
        $line .= '$form->addControl("' . $name . '")->type(';
        if (is_string($typeInfo)) {
            $line .= $this->dqw($typeInfo);
        } else {
            $line .= $this->dqw($typeInfo[0]);
            if (count($typeInfo[1]) > 0) {
                $line .= ", ";
                $line .= $this->argumentsToLine($typeInfo[1]);
            }
        }
        $line .= ')';

        //--------------------------------------------
        // VALUE
        //--------------------------------------------
        if (null !== $defaultValue) {
            $line .= PHP_EOL;
            $line .= '->value(';
            $line .= $this->argumentsToLine([$defaultValue]);
            $line .= ')';
        }

        //--------------------------------------------
        // CONSTRAINT
        //--------------------------------------------
        if (null !== $constraintInfo) {
            $line .= PHP_EOL;
            $line .= '->addConstraint(';
            if (is_string($constraintInfo)) {
                $line .= $this->dqw($constraintInfo);
            } else {
                $line .= $this->dqw($constraintInfo[0]);
                if (count($constraintInfo[1]) > 0) {
                    $line .= ", ";
                    $line .= $this->argumentsToLine($constraintInfo[1]);
                }
            }
            $line .= ')';
        }


        $line .= ';';
        $this->line($line);
    }

    /**
     * Return an either
     *
     * - null: no constraint
     * - string: the name of the constraint
     * - array: [constraintName, constraintArgs]
     *      - constraintName: string, the constraint name
     *      - constraintArgs: an array containing the constraint arguments
     *
     */
    private function getConstraintInfo($name)
    {
        $constraint = null;
        if (array_key_exists($this->_table . '.' . $name, $this->column2Constraint)) {
            $constraint = $this->column2Constraint[$this->_table . '.' . $name];
        } elseif (array_key_exists($name, $this->column2Constraint)) {
            $constraint = $this->column2Constraint[$name];
        } elseif (array_key_exists($this->_db . '.' . $this->_table, $this->_foreignKeyPrettierColumns) && $name === $this->_foreignKeyPrettierColumns[$this->_db . '.' . $this->_table]) {
            $constraint = "required";
        } // case of the "configuration" table with 2 columns: cle, valeur, where the primary key is of type varchar!
        elseif (array_key_exists(0, $this->_primaryKey) && $name === $this->_primaryKey[0] && array_key_exists($this->_primaryKey[0], $this->_columnTypes) && 'varchar' === $this->_columnTypes[$this->_primaryKey[0]]) {
            $constraint = "required";
        }
        return $constraint;
    }


    private function getDefaultValue($name)
    {
        $d = null;
        if (array_key_exists($name, $this->defaultValues)) {
            $d = $this->defaultValues[$name];
        } elseif (array_key_exists($this->_table . "." . $name, $this->defaultValues)) {
            $d = $this->defaultValues[$this->_table . "." . $name];
        } elseif (array_key_exists($name, $this->_defaultValues)) {
            $d = $this->_defaultValues[$name];
            if (null === $d) {
                if (false === array_key_exists($name, $this->_fkInfo)) {
                    // if it's an int or tinyint, we add a default value of 0
                    $type = $this->_columnTypes[$name];
                    if ('int' === $type || 'tinyint' === $type) {
                        $d = 0;
                    }
                }
            }
        }
        return $d;
    }

    private function getTypeInfo($name)
    {
        $ret = null;
        //--------------------------------------------
        // COLUMN TYPE (user override)
        //--------------------------------------------
        if (
            array_key_exists($name, $this->column2Type) ||
            array_key_exists($this->_table . '.' . $name, $this->column2Type)
        ) {
            $ret = $this->column2Type[$name];
        }
        //--------------------------------------------
        // DEFAULT ALGORITHM
        //--------------------------------------------
        //--------------------------------------------
        // FOREIGN KEYS
        //--------------------------------------------
        elseif (array_key_exists($name, $this->_fkInfo)) {
            $info = $this->_fkInfo[$name];

            $prettyColumn = null;
            $ftable = $info[0] . '.' . $info[1];
            if (array_key_exists($ftable, $this->_foreignKeyPrettierColumns)) {
                $prettyColumn = $this->_foreignKeyPrettierColumns[$ftable];
            } else {
                $prettyColumn = $info[2];
//                throw new \Exception("Please provide an entry for the foreignKeyPrettierColumns with the key " . $info[1] . '.' . $info[2] . ', table is ' . $ftable);
            }
            $ret = [
                'selectByRequest',
                [
                    'select ' . $info[2] . ', ' . $prettyColumn . ' from ' . $info[0] . '.' . $info[1],
                ],
            ];
        } else {
            //--------------------------------------------
            // SQLTYPE 2 TYPE
            //--------------------------------------------
            $columnType = $this->_columnTypes[$name];
            if (array_key_exists($columnType, $this->sqlType2Type)) {
                $ret = $this->sqlType2Type[$columnType];
            } elseif ('enum' === $columnType) {
                //--------------------------------------------
                // ENUM
                //--------------------------------------------
                $enum = $this->_enums[$name];
                $n = count($enum);


                $finalEnum = [];
                // enhance enum entries (key and values)
                foreach ($enum as $k => $v) {
                    if (array_key_exists($v, $this->enumInfo)) {
                        list($k, $v) = $this->enumInfo[$v];
                    } elseif (array_key_exists($name . '.' . $v, $this->enumInfo)) {
                        list($k, $v) = $this->enumInfo[$name . '.' . $v];
                    } elseif (array_key_exists($this->_table . '.' . $name . '.' . $v, $this->enumInfo)) {
                        list($k, $v) = $this->enumInfo[$this->_table . '.' . $name . '.' . $v];
                    }
                    $finalEnum[$k] = $v;
                }


                if ($n <= 3) {
                    $ret = [
                        'radioList',
                        [$finalEnum],
                    ];
                } else {
                    $ret = [
                        'select',
                        [$finalEnum],
                    ];
                }
            } else {
                $ret = "text";
            }
        }

        return $ret;
    }
}