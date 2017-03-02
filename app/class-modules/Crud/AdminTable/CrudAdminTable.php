<?php


namespace Crud\AdminTable;


use AdminTable\Listable\QuickPdoListable;
use AdminTable\Table\QuickAdminTable;
use AssetsList\AssetsList;
use Crud\CrudConfig;
use Crud\CrudHelper;
use QuickPdo\QuickPdo;

class CrudAdminTable extends QuickAdminTable
{

    public $title;
    private $table;

    public function __construct($table, $query, $fields, array $ric)
    {
        parent::__construct();
        $this->table = $table;

        AssetsList::css('/style/planets/adminTable/admintable.css');


        $this->setListable(QuickPdoListable::create()
            ->setQuery($query)
            ->setFields($fields)
        );
        $this->setRic($ric);
        $this->setRenderer(CrudAdminTableRenderer::create());
        $ll = CrudConfig::getLangDir() . '/datatable';

        //------------------------------------------------------------------------------/
        // EDIT, DELETE, AND DELETE_ALL ACTIONS
        //------------------------------------------------------------------------------/
        $this
            ->setExtraColumn('edit', '<a class="action-link" href="' . CrudHelper::getUpdateFormUrl('{table}', '{ric}') . '">' . __('Edit', $ll) . '</a>')
            ->setTransformer('edit', function ($v, $item, $ric) use ($table) {
                return str_replace(['{ric}', '{table}'], [$ric, $table], $v);
            });
        $this->setActionLink('delete', __("Delete", $ll), function ($ric) use ($table) {
            $q = "delete from $table where ";
            $markers = [];
            $q .= CrudHelper::getWhereFragmentFromRic($ric, $markers);
            QuickPdo::freeQuery($q, $markers);
        });

        $this->setMultipleActionHandler('deleteAll', __('Delete all', $ll), function (array $rics) use ($table) {
            $q = "delete from $table where ";
            $markers = [];
            $i = 0;
            foreach ($rics as $ric) {
                if (0 !== $i) {
                    $q .= ' or ';
                }
                $q .= "(";
                foreach ($ric as $k => $v) {
                    $marker = 'm' . $i++;
                    $q .= "$k=:" . $marker;
                    $markers[$marker] = $v;
                }
                $q .= ")";
            }
            QuickPdo::freeQuery($q, $markers);
        }, true);

    }



    //------------------------------------------------------------------------------/
    //
    //------------------------------------------------------------------------------/
    protected function getListParameters()
    {
        $p = new CrudListParameters();
        $p->title = $this->title;
        $p->table = $this->table;
        $p->hasNewItemLink = $this->widgets;
        return $p;
    }

    protected function getListWidgets()
    {
        return new CrudListWidgets();
    }


}