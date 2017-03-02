<?php

namespace Boot\ResetOption;

class InitResetOption extends AbstractResetOption
{
    public function reset()
    {
        $init = __DIR__ . "/../../../init.php";
        if (file_exists($init)) {
            unlink($init);
        }
    }

}