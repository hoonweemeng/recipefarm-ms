<?php

namespace model\base;

class ValidationModel
{
    public string $fieldTitle;
    public bool $isValid;
    public string $message;

    public function __construct(string $fieldTitle, bool $isValid, string $message = '')
    {
        $this->fieldTitle = $fieldTitle;
        $this->isValid = $isValid;
        $this->message = $message;
    }
}