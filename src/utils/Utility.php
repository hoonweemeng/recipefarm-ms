<?php

namespace utils;

class Utility
{
    public static function getUserId(): ?string
    {
        return getallheaders()["userId"] ?? null;
    }

    public static function errorNotFound(): void {
        http_response_code(404);
        header("Content-Type: application/json");
        echo json_encode(["error" => "Resource not found"]);
        exit;
    }

    public static function generateUUID(): string {
        // Get current timestamp in milliseconds
        $timestamp = (int)(microtime(true) * 1000);  // Millisecond precision
        
        // Generate a random value for added randomness (48-bit random value)
        $randomBytes = bin2hex(random_bytes(6));  // Generates 12 random hex digits (48 bits)

        // Format the UUID as 8-4-4-4-12 using timestamp and random bytes
        $uuid = sprintf('%08x-%04x-%04x-%04x-%012x',
            $timestamp & 0xFFFFFFFF, // Use the lower 32 bits of the timestamp
            mt_rand(0, 0xFFFF), // Random 4 hex digits
            mt_rand(0, 0xFFFF), // Random 4 hex digits
            mt_rand(0, 0xFFFF), // Random 4 hex digits
            hexdec(substr($randomBytes, 0, 12)) // Random 12 hex digits
        );
        
        return $uuid;
    } 
    
    // Json Handler

    public static function toJson(object $object): string {
        return json_encode($object);
    }

    // Convert JSON to an object of the given class
    public static function fromJson(string $json, string $className): object {
        $data = json_decode($json, true);
        if ($data === null) {
            throw new \InvalidArgumentException("Invalid JSON provided");
        }
        return self::mapToClass($data, $className);
    }

    public static function getRequestBody(string $className): object {
        return Utility:: fromJson(file_get_contents("php://input"), $className);
    }

    // Map an associative array to a class object
    private static function mapToClass(array $data, string $className): object {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class $className does not exist");
        }
        $reflectionClass = new \ReflectionClass($className);
        $object = $reflectionClass->newInstanceWithoutConstructor();

        foreach ($data as $key => $value) {
            if ($reflectionClass->hasProperty($key)) {
                $property = $reflectionClass->getProperty($key);
                if ($property->isPublic()) {
                    $property->setValue($object, $value);
                } else {
                    // Optionally handle private/protected properties
                    $setter = 'set' . ucfirst($key);
                    if (method_exists($object, $setter)) {
                        $object->$setter($value);
                    }
                }
            }
        }

        return $object;
    }

    public static function trimData($model)
    {
        if ($model === null) {
            throw new \InvalidArgumentException('Model cannot be null');
        }

        // Get all public properties of the model
        $properties = get_object_vars($model);

        foreach ($properties as $property => $value) {
            // Check if the property is a string and is writable
            if (is_string($value) && property_exists($model, $property)) {
                // Trim the string value and set it back to the model property
                $model->$property = trim($value);
            }
        }
    }
    

}









