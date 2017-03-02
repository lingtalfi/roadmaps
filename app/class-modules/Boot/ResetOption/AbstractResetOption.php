<?php

namespace Boot\ResetOption;

abstract class AbstractResetOption implements ResetOptionInterface
{

    private $identifier;
    private $translatedLabel;
    private $checked;

    public function __construct($identifier, $translatedLabel, $checked = false)
    {
        $this->identifier = $identifier;
        $this->translatedLabel = $translatedLabel;
        $this->checked = (bool)$checked;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getTranslatedLabel()
    {
        return $this->translatedLabel;
    }

    public function isChecked()
    {
        return $this->checked;
    }


}