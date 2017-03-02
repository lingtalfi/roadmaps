<?php


use Layout\IfDbLayout;

IfDbLayout::create()->setElementFiles([
    'body' => Helper::layoutElementIf("modules/crud/crud.php", "crud.access.table"),
])->display();


