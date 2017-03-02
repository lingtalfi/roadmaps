<?php

namespace Layout;


use QuickPdo\QuickPdo;

class IfDbLayout extends Layout
{


    protected function __construct()
    {
        parent::__construct();
        $this->onDisplayBefore = function () {
            if (false === QuickPdo::hasConnection()) {
                $this->setElementFiles([
                    'body' => 'db-required.php',
                ]);
            }
        };
    }

    public static function create()
    {
        return new self;
    }

}