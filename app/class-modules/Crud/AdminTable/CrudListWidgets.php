<?php


namespace Crud\AdminTable;


use AdminTable\Table\ListWidgets;

class CrudListWidgets extends ListWidgets
{

    public function __construct()
    {
        parent::__construct();
        $this->widgets['newItemLink'] = true;
    }

    public function disableNewItemLink()
    {
        $this->widgets['newItemLink'] = false;
        return $this;
    }
}