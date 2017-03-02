<?php

namespace Boot\ResetOption;


interface ResetOptionInterface
{
    /**
     * the html name passed to the form in the "Reset" page of the Boot module
     */
    public function getIdentifier();

    /**
     * is checked by default?
     */
    public function isChecked();

    public function getTranslatedLabel();



    public function reset();
}