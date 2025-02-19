<?php

namespace model\genericmodel;

class GenericResponse {
    public bool $success;
    public ?string $errorMessage;
    public ?array $validationErrors;
    public $data;

    public function __construct(bool $success = false, ?string $errorMessage = null, ?array $validationErrors = null, $data = null) {
        $this->success = $success;
        $this->errorMessage = $errorMessage;
        $this->validationErrors = $validationErrors;
        $this->data = $data;
    }

    // Return the response as a JSON-encoded string
    public function toJson() {
        // If the data is an object and has a toArray method, convert it
        if (is_object($this->data)) {
            $this->data = $this->data->toArray();  // Serialize object
        } elseif (is_array($this->data)) {
            // If data is an array, process each item
            foreach ($this->data as $key => $item) {
                if (is_object($item) && method_exists($item, 'toArray')) {
                    $this->data[$key] = $item->toArray();  // Serialize nested object
                }
            }
        }

        return json_encode([
            'success' => $this->success,
            'errorMessage' => $this->errorMessage,
            'data' => $this->data
        ]);
    }
}
?>
