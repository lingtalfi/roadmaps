<?php


namespace Crud\AdminTable;


use AdminTable\Table\ListParameters;
use AdminTable\View\AdminTableRenderer;
use Crud\CrudConfig;
use Crud\CrudHelper;
use Icons\Icons;

class CrudAdminTableRenderer extends AdminTableRenderer
{



    public static function create()
    {
        return new self();
    }


    public function renderTable(ListParameters $p)
    {
        if ($p instanceof CrudListParameters) { // fake condition, just want nice syntax highlighting...
            $ll = CrudConfig::getLangDir() . '/datatable';
            if (null !== $p->title): ?>
                <h3 class="list-title"><?php echo $p->title; ?></h3>
            <?php endif;

            if ($p->hasNewItemLink): ?>
                <p class="crudadmintable-newitemlink">
                    <a href="<?php echo CrudHelper::getInsertFormUrl($p->table); ?>"><?php Icons::printIcon('add', 'blue'); ?>
                        <span><?php echo __("Create a new item", $ll); ?></span></a>
                </p>
            <?php endif;

            parent::renderTable($p);
        }
    }


    protected function printHiddenFields($exclude, ListParameters $p, $page, $sortColumn, $sortColumnDir, $search, $nbItemsPerPageChoice)
    {

        parent::printHiddenFields($exclude, $p, $page, $sortColumn, $sortColumnDir, $search, $nbItemsPerPageChoice);
        ?>
        <input type="hidden" name="name"
               value="<?php echo htmlspecialchars($p->table); ?>">

        <?php
    }


}