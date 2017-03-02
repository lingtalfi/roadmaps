<?php

use Crud\Util\CrudFilesPreferencesGenerator;
use Crud\Util\CrudFilesGenerator;
use Crud\Util\LeftMenuPreferencesGenerator;
use Layout\Goofy;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoInfoTool;


$ll = 'modules/crud/crud-generators';
Spirit::set('ll', $ll);
define('LL', $ll);

?>
<div class="tac bignose install-page">
    <h3><?php echo __("Crud generators page", LL); ?></h3>
    <p>
        <?php echo __("Use this page to generate various files and objects.", LL); ?>
    </p>
    <p>
        <?php echo linkt("Need help?", doclink('modules/crud-module/crud-generators-page.md'), true); ?>
    </p>

    <div>
        <?php
        $dbs = [];
        if (false !== ($rows = QuickPdo::fetchAll('show databases', [], \PDO::FETCH_COLUMN))) {
            foreach ($rows as $db) {
                $dbs[$db] = $db;
            }
        }
        $form2 = QuickFormZ::create();

        $form2->title = __("Generate forms and lists", LL);
        $form2->labels = [
            'database' => __("database", LL),
        ];

        $form2->defaultValues = [
            'database' => QuickPdoInfoTool::getDatabase(),
            'options' => [
                'prefs',
                'files',
                'leftmenu',
            ],
        ];
        $form2->formTreatmentFunc = function (array $formattedValues, &$msg) use ($form2, $dbs) {
            $selectedDb = (string)$formattedValues['database'];
            if (array_key_exists($selectedDb, $dbs)) {
                if (array_key_exists('options', $formattedValues) && is_array($formattedValues['options'])) {
                    $options = $formattedValues['options'];

                    try {

                        if (in_array('prefs', $options, true)) {
                            CrudFilesPreferencesGenerator::generate();
                        }

                        if (in_array('files', $options, true)) {
                            CrudFilesGenerator::generateCrudFormsFromPreferences();
                            CrudFilesGenerator::generateCrudListsFromPreferences();
                        }

                        if (in_array('leftmenu', $options, true)) {
                            LeftMenuPreferencesGenerator::create()->generate();
                        }

                        $msg = Goofy::alertSuccess(__("The operation was successful.", "modules/crud/crud-generators"), true, true);
                        return true;

                    } catch (\Exception $e) {
                        Logger::log($e, "crud.page.generators");
                        return false;
                    }
                }
            } else {
                return false;
            }
        };

        $form2->addControl('database')->type('select', $dbs)->addConstraint('required');
        $form2->addControl('options')->type('checkboxList', [
            'prefs' => __('create the crud files preferences', LL),
            'files' => __('create the crud files', LL),
            'leftmenu' => __('create the left menu preferences', LL),
        ])->addConstraint('minChecked', 1);
        $form2->addControl('button')->type('checkUncheckAll', "options", __("Check all", 'form'), __("Uncheck all", 'form'))->label("");
        $form2->play();


        ?>
    </div>
</div>