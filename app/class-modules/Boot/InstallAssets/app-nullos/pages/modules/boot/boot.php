<?php

use Layout\Layout;

if (true === defined('I_AM_JUST_THE_FALLBACK_INIT')) {
    $action = "boot";
} else {
    $action = (array_key_exists('action', $_GET)) ? $_GET['action'] : 'boot';
    if (!in_array($action, ['boot', 'reset'])) {
        $action = "boot";
    }
}

Layout::create()->setElementFiles([
    'body' => Helper::layoutElementIf("modules/boot/$action.php", "boot.access.$action"),
])->display();


