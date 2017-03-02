<?php


use Layout\IfDbLayout;

IfDbLayout::create()->setElementFiles([
    'body' => Helper::layoutElementIf("modules/crud/crud-generators.php", "crud.access.generator"),
])->display();


