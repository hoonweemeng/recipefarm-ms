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
    

}









