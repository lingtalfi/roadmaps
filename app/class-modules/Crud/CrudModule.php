<?php


namespace Crud;


use Crud\AdminTable\CrudAdminTable;
use Crud\ResetOption\CrudFilesResetOption;
use Crud\ResetOption\GeneratorsPreferencesResetOption;
use Crud\ResetOption\LeftMenuPreferencesResetOption;
use Layout\LayoutHelper;
use Privilege\Privilege;

class CrudModule
{


    public static function decorateUri2PagesMap(array &$uri2pagesMap)
    {
        $uri2pagesMap[CrudConfig::getCrudUri()] = CrudConfig::getCrudPage();
        $uri2pagesMap[CrudConfig::getCrudGeneratorsUri()] = CrudConfig::getCrudGeneratorsPage();
    }


    public static function displayToolsLeftMenuLinks()
    {
        $ll = "modules/crud/crud";
        if (Privilege::has('crud.access.generator')):
            ?>
            <li>
                <a href="<?php echo self::getUrl(); ?>"><?php echo __("Crud Generators", $ll); ?></a>
            </li>
            <?php
        endif;
    }

    public static function getUrl()
    {
        return CrudConfig::getCrudGeneratorsUri();
    }


    public static function displayLeftMenuBlocks()
    {
        $prettyTables = CrudConfig::getLeftMenuTableLabels();
        $sections = CrudConfig::getLeftMenuSections();
        $classes = CrudConfig::getLeftMenuSectionsClasses();

        foreach ($sections as $label => $tables):
            $class = (array_key_exists($label, $classes)) ? $classes[$label] : '';
            ?>
            <section class="section-block table-links <?php echo $class; ?>">
                <?php LayoutHelper::displayLeftMenuExpandableTitle($label); ?>
                <ul class="linkslist">
                    <?php foreach ($tables as $table):
                        $original = $table;
                        if (array_key_exists($table, $prettyTables)) {
                            $table = $prettyTables[$table];
                        } else {
                            if (false !== ($pos = strpos($table, '.'))) {
                                $table = substr($table, $pos + 1);
                            }
                        }
                        ?>
                        <li>
                            <a href="<?php echo CrudHelper::getListUrl($original); ?>"><?php echo ucfirst($table); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php
        endforeach;
    }


    public static function registerBootResetOptions(array &$options)
    {
        $options[] = new LeftMenuPreferencesResetOption('crud_leftmenu', __('empty the left menu preferences', 'modules/crud/crud'));
        $options[] = new CrudFilesResetOption('crud_files', __('remove the crud files', 'modules/crud/crud'));
        $options[] = new GeneratorsPreferencesResetOption('crud_files_prefs', __('empty the crud files preferences', 'modules/crud/crud'));
    }




    //--------------------------------------------
    //
    //--------------------------------------------

    /**
     * default DataTable instance for all lists (configure nipp, widgets,...)
     */
    public static function getDataTable($table, $query, $fields, array $ric)
    {
        $o = new CrudAdminTable($table, $query, $fields, $ric);
        $o->nbItemsPerPage = "50";
        return $o;
    }

    /**
     * default Form instance for all forms (configure errorLocation, ...)
     */
    public static function getForm($table, array $ric, $mode = null)
    {
        $o = new Form($table, $ric, $mode);
        $o->setFormatter("done_date", function ($v) {
            if ("" === $v) {
                $v = null;
            }
            return $v;
        });
        $o->setFormatter("parent_task_id", function ($v) {
            if ("0" === $v) {
                $v = null;
            }
            return $v;
        });
        return $o;
    }


    public static function resetCrudConfig()
    {
        // reset CrudConfig
        $src = __DIR__ . "/template/CrudConfigBlank-tmp.php";
        $dst = __DIR__ . "/CrudConfig.php";
        $s = file_get_contents($src);
        file_put_contents($dst, $s);
    }

}