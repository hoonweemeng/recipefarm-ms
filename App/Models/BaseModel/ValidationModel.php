<?php

namespace RecipeFarm\Models\BaseModel;

class ValidationModel {
    public string $field;
    public string $message;

    public function __construct(string $field, string $message) {
        $this->field = $field;
        $this->message = $message;
    }

    public function toArray(): array {
        return [
            'field' => $this->field,
            'message' => $this->message,
        ];
    }
}
?>
