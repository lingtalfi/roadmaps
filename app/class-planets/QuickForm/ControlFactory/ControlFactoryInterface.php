<?php


namespace QuickForm\ControlFactory;


use QuickForm\QuickForm;
use QuickForm\QuickFormControl;

interface ControlFactoryInterface
{

    /**
     * Displays a control.
     *
     * - name is the html name attribute.
     *
     * - Returns bool: whether or not the factory was able to handle the given control
     */
    public function displayControl($name, QuickFormControl $c, QuickForm $f);

    /**
     * Prepares a control before the form is submitted.
     * (set isFake to true)
     */
    public function prepareControl($name, QuickFormControl $c);
}