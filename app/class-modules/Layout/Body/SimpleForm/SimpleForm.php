<?php


namespace Layout\Body\SimpleForm;


use Layout\Goofy;

class SimpleForm
{


    public static function demo()
    {
        ?>
        <div class="tac bignose install-page">
            <h3>My page</h3>
            <p>
                Use this page to configure your theme.
            </p>
            <p>
                <a href="#">Need help?</a>
            </p>
            <div>
                <?php


                $form = \QuickFormZ::create();

                $form->title = "Simple form";
                $form->header = Goofy::technicalNote("This will replace the front content", true);
                $form->labels = [
                    'dbName' => "Name",
                    'dbUser' => "User",
                    'dbPass' => "Password",
                ];

                $form->addFieldset('Database information', [
                    'dbName',
                    'dbUser',
                    'dbPass',
                ]);
                $form->defaultValues = [
                    'dbUser' => 'root',
                    'dbPass' => 'root',
                ];


                $form->addControl('dbName')->type('text', 'my_db')->addConstraint('required');
                $form->addControl('dbUser')->type('text')->addConstraint('required');
                $form->addControl('dbPass')->type('text');


                $form->formTreatmentFunc = function (array $formattedValues, &$msg) {
                    return true;
                };

                $form->play();

                ?>
            </div>
        </div>
        <?php
    }
}