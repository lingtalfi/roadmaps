<?php


namespace AdminTable;


use AdminTable\Table\QuickAdminTable;
use AdminTable\View\AdminTableRenderer;
use AssetsList\AssetsList;

class NullosAdminTable extends QuickAdminTable
{
    public function __construct()
    {
        parent::__construct();
        AssetsList::css('/style/planets/adminTable/admintable.css');
        $this->setRenderer(AdminTableRenderer::create());
    }

    public static function create()
    {
        return new self();
    }

}