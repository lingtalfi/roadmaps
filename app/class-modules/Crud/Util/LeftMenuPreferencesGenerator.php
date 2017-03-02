<?php


namespace Crud\Util;

use QuickPdo\QuickPdoInfoTool;
use Util\ClassArrayExport;
use Util\ClassCloner;


/**
 * This class generates the left menu website section's "starting point" for a given database.
 */
class LeftMenuPreferencesGenerator
{

    private $withLeftMenu;
    private $withTableLabels;


    public function __construct()
    {
        $this->withLeftMenu = true;
        $this->withTableLabels = true;
    }

    public static function create()
    {
        return new self();
    }


    /**
     * @return bool
     */
    public function generate($db = null)
    {

        $tpl = __DIR__ . "/template/LeftMenuPreferences.tpl.php";
        $dst = APP_ROOT_DIR . "/class-modules/Crud/Auto/LeftMenuPreferences.php";


        if (null === $db) {
            $db = QuickPdoInfoTool::getDatabase();
        }

        $sections = [];
        $prettyTables = [];


        $tables = QuickPdoInfoTool::getTables($db);
        $fullTables = array_map(function ($v) use ($db) {
            return $db . '.' . $v;
        }, $tables);


        if (true === $this->withLeftMenu) {
            $sections['Website'] = $fullTables;
        }

        if (true === $this->withTableLabels) {
            foreach ($fullTables as $table) {
                if (false !== strpos($table, '_')) {
                    $p = explode('.', $table, 2);
                    $ftable = array_pop($p);
                    $prettyTables[$table] = str_replace('_', ' ', $ftable);
                }
            }
        }

        $sSection = ClassArrayExport::export($sections);
        $sPrettyTables = ClassArrayExport::export($prettyTables);

        ClassCloner::replicate($tpl, $dst, [
            '//{leftMenuSection}' => 'return ' . $sSection . ';',
            '//{tableLabels}' => 'return ' . $sPrettyTables . ';',
        ]);
    }

    public function withoutLeftMenuSections()
    {
        $this->withLeftMenu = false;
        return $this;
    }

    public function withoutTableLabels()
    {
        $this->withTableLabels = false;
        return $this;
    }


}