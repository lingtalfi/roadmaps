<?php
use QuickForm\ControlFactory\MagicControlFactory;

/**
 * A translated version of the QuickForm.
 *
 */
class QuickFormZ
{

    public static function create()
    {
        $form = new QuickForm\QuickForm();
        $form->addControlFactory(MagicControlFactory::create());
        $form->validationTranslateFunc = function ($msg) {
            return __($msg, 'form');
        };
        $form->messages = [
            'formSubmittedOk' => __('The form data have been successfully treated', 'form'),
            'formHasControlErrors' => __('The form has the following errors, please fix them and resubmit the form', 'form'),
            'submit' => __('Submit', 'form'),
            'formNotDisplayed' => __('Oops, there was a problem with the form', 'form'),
        ];
        return $form;
    }
}