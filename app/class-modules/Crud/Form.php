<?php

namespace Crud;


use QuickForm\QuickForm;
use QuickPdo\QuickPdo;


class Form
{

    public $insertDefaults;
    public $controlErrorLocation; // local(default)|top
    public $title; // string(default="$table form")|null, null means no title
    public $labels;
    public $allowMultipleErrorsPerControl;
    public $messages;
    public $validationTranslatorContext;


    private $table;
    private $ric;
    private $mode;
    //
    private $controls;
    private $ricSeparator;
    private $_translatorContext;
    private $formatters;

    //
    private $qform;

    public function __construct($table, array $ric, $mode = null)
    {
        $this->table = $table;
        $this->ric = $ric;
        if (null === $mode) {
            $mode = (array_key_exists('ric', $_GET)) ? 'update' : 'insert';
        }
        $this->mode = $mode;
        $this->controls = [];
        $this->labels = [];
        $this->insertDefaults = [];
        $this->formatters = [];


        $this->messages = [];

        /**
         * if this class ever sets the ricSeparator, it should also update the property in the Spirit object,
         * so that both are synced.
         * That's because the ricSeparator is shared between the list and the form features,
         * transiting via the http url.
         */
        $this->ricSeparator = \Spirit::get('ricSeparator');
        $this->validationTranslatorContext = "form-validation";
        $this->controlErrorLocation = "local";

        $this->allowMultipleErrorsPerControl = true;
        $this->qform = new QuickForm();
        $this->_translatorContext = CrudConfig::getLangDir() . '/form';
        $this->title = __("{table} form", $this->_translatorContext, ['table' => ucfirst($table)]);
    }

    /**
     * @param array $formatters
     */
    public function setFormatter($field, \Closure $formatter)
    {
        $this->formatters[$field] = $formatter;
    }


    /**
     * column: the column to insert/update
     */
    public function addControl($name)
    {
        return $this->qform->addControl($name);
    }

    public function display()
    {

        //--------------------------------------------
        // PROXY TO QUICKFORM
        //--------------------------------------------
        $this->qform->validationTranslateFunc = function ($v) {
            return __($v, $this->_translatorContext);
        };

        $messages = $this->messages;
        $this->qform->messages = [
            'formHasControlErrors' => (array_key_exists('formHasControlErrors', $messages)) ? $messages['formHasControlErrors'] : __('The form has the following errors, please fix them and resubmit the form', $this->_translatorContext),
            'submit' => (array_key_exists('submit', $messages)) ? $messages['submit'] : __('Submit', $this->_translatorContext), // submit btn value
            'formNotDisplayed' => (array_key_exists('formNotDisplayed', $messages)) ? $messages['formNotDisplayed'] : __('Oops, something went wrong with the database, sorry...', $this->_translatorContext),
        ];
        $this->qform->allowMultipleErrorsPerControl = $this->allowMultipleErrorsPerControl;
        $this->qform->controlErrorLocation = $this->controlErrorLocation;
        $this->qform->labels = $this->labels;
        $this->qform->title = $this->title;


        //--------------------------------------------
        // START ---
        //--------------------------------------------
        if ('insert' === $this->mode) {
            $this->qform->defaultValues = $this->insertDefaults;
        }
        $table = $this->table;
        $updateRowFound = false;
        if ('update' === $this->mode && array_key_exists('ric', $_GET)) {
            $ric = $this->getRicArray();
            $markers = [];
            $q = "select * from $table where ";
            $q .= CrudHelper::getWhereFragmentFromRic($ric, $markers);
            $item = QuickPdo::fetch($q, $markers);
            if (false !== $item) {
                $updateRowFound = true;
                $this->qform->defaultValues = $item;
            }
        }


        //--------------------------------------------
        // FORM SUBMITTED
        //--------------------------------------------
        $this->qform->formTreatmentFunc = function (array $formattedValues, &$msg) {
            $dbInteractionIsSuccess = false;
            try {

                $this->prepareValues($formattedValues);

                /**
                 * Note that below I assume that pdo works in Exception error mode, so that
                 * every error goes to the catch block...
                 */
                if ('update' === $this->mode) {
                    $ric = $this->getRicArray();
                    $where = [];
                    foreach ($ric as $k => $v) {
                        $where[] = [$k, '=', $v];
                    }
                    QuickPdo::update($this->table, $formattedValues, $where);
                    $dbInteractionIsSuccess = true;
                    $msg = __("The data has been successfully updated", $this->_translatorContext);

                } else {
                    QuickPdo::insert($this->table, $formattedValues);
                    $dbInteractionIsSuccess = true;
                    $msg = __("The data has been successfully inserted", $this->_translatorContext);
                }
            } catch (\Exception $e) {
                $msg = __("Oops, something went wrong with the database, sorry...", $this->_translatorContext);
                \Logger::log($e, 'crud.pdoException.form.' . $this->mode);
            }
            return $dbInteractionIsSuccess;
        };


        $displayForm = ('update' !== $this->mode || ('update' === $this->mode && true === $updateRowFound));
        $this->qform->displayForm = $displayForm;


        $this->qform->play();

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getRicArray()
    {
        $ricValue = $_GET['ric'];
        $ricValueArr = explode($this->ricSeparator, $ricValue);
        return array_combine($this->ric, $ricValueArr);
    }


    private function prepareValues(array &$values)
    {
        foreach ($this->formatters as $field => $formatter) {
            if (array_key_exists($field, $values)) {
                $values[$field] = call_user_func($formatter, $values[$field]);
            }
        }
    }
}