<?php


use Boot\BootBridge;
use Layout\Goofy;

$ll = 'modules/boot/boot';
Spirit::set('ll', $ll);
define('LL', $ll);


?>
<div class="tac bignose install-page">
    <h3><?php echo __("Reset page", LL); ?></h3>

    <p>
        <?php echo __("Use this page to reset your <b>nullos admin</b> application.", LL); ?>
    </p>
    <p>
        <?php echo linkt("Need help?", doclink('modules/boot-module/reset-page.md'), true); ?>
    </p>
    <?php


    $form = QuickFormZ::create();

    $_resetOptions = [];
    $resetOptions = [];
    BootBridge::registerBootResetOptions($_resetOptions);

    foreach ($_resetOptions as $option) {
        $resetOptions[$option->getIdentifier()] = $option;
    }


    $form->formTreatmentFunc = function (array $formattedValues, &$msg) use ($form, $resetOptions) {
        if (array_key_exists('options', $formattedValues) && is_array($formattedValues['options'])) {
            $options = $formattedValues['options'];
            foreach ($options as $id) {
                if (array_key_exists($id, $resetOptions)) {
                    $o = $resetOptions[$id];
                    $o->reset();
                }
            }
            $msg = Goofy::alertSuccess(__("The website has been successfully reset.", LL), true, true);
            return true;
        }
    };

    $form->title = __("Reset form", LL);
    $defaults = [];
    foreach ($resetOptions as $id => $o) {
        if ($o->isChecked()) {
            $defaults[] = $id;
        }
    }
    $form->defaultValues = [
        'options' => $defaults,
    ];

    $cOptions = [];
    foreach ($resetOptions as $option) {
        $cOptions[$option->getIdentifier()] = $option->getTranslatedLabel();
    }

    $form->addControl('options')->type('checkboxList', $cOptions)->addConstraint('minChecked', 1);
    $form->addControl('button')->type('checkUncheckAll', "options", __("Check all", 'form'), __("Uncheck all", 'form'))->label("");


    $form->play();

    ?>
</div>