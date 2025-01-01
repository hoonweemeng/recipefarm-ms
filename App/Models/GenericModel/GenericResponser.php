<?php

namespace RecipeFarm\Models\GenericModels;

use RecipeFarm\Models\BaseModel\ValidationModel;

class GenericResponse {
    public bool $success;
    public ?string $errorMessage;
    public ?array $validationErrorMessage;
    public $data;

    // Constructor for success responses
    public function __construct($data = null, string $errorMessage = null, array $validationErrorMessage = null) {
        if ($data !== null) {
            $this->success = true;
            $this->data = $data;
            $this->errorMessage = null;
            $this->validationErrorMessage = null;
        } elseif ($errorMessage !== null) {
            $this->success = false;
            $this->errorMessage = $errorMessage;
            $this->validationErrorMessage = null;
            $this->data = null;
        } elseif ($validationErrorMessage !== null) {
            $this->success = false;
            $this->errorMessage = null;
            $this->validationErrorMessage = $validationErrorMessage;
            $this->data = null;
        } else {
            throw new \InvalidArgumentException("Invalid arguments provided to GenericResponse.");
        }
    }

    // Static method for creating a success response
    public static function success($data): self {
        return new self($data);
    }

    // Static method for creating an error response
    public static function error(string $errorMessage): self {
        return new self(null, $errorMessage);
    }

    // Static method for creating a validation error response
    public static function validationErrors(array $validationErrorMessage): self {
        return new self(null, null, $validationErrorMessage);
    }

    // Convert the response object to an associative array
    public function toArray(): array {
        return [
            'success' => $this->success,
            'errorMessage' => $this->errorMessage,
            'validationErrorMessage' => $this->validationErrorMessage,
            'data' => $this->data,
        ];
    }
}
?>
