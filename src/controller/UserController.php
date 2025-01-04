<?php
namespace controller;

use DAL\RecipeDAL;
use model\base\User;
use utils\Utility;

class UserController
{
    public function __construct(private RecipeDAL $gateway)
    {
    }
    
    
    public function processRequest(string $method, ?string $id): void
    {
        if ($method != "POST")  
        { 
            Utility:: errorNotFound();
        }

        switch ($id)  
        {
            case "register":
                $this->createUser();
                break;

            case "update":
                // Code to execute   
                break;

            case "delete":
                // Code to execute   
                break;
                
            case "login":
                // Code to execute   
                break;
                
            case "detail":
                // Code to execute   
                break;

            default:
                Utility:: errorNotFound();

        }
    }

    public function createUser(): void
    {
        $data = Utility:: fromJson(file_get_contents("php://input"), User::class);
        echo $data->email;
    }

    public function updateRecipe(): void
    {
        
    }
    
    private function processResourceRequest(string $method, string $id): void
    {
        $product = $this->gateway->get($id);
        
        if ( ! $product) {
            http_response_code(404);
            echo json_encode(["message" => "Product not found"]);
            return;
        }
        
        switch ($method) {
            case "GET":
                echo json_encode($product);
                break;
                
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                
                $errors = $this->getValidationErrors($data, false);
                
                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                
                $rows = $this->gateway->update($product, $data);
                
                echo json_encode([
                    "message" => "Product $id updated",
                    "rows" => $rows
                ]);
                break;
                
            case "DELETE":
                $rows = $this->gateway->delete($id);
                
                echo json_encode([
                    "message" => "Product $id deleted",
                    "rows" => $rows
                ]);
                break;
                
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }
    
    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;
                
            case "POST":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                
                $errors = $this->getValidationErrors($data);
                
                if ( ! empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                
                $id = $this->gateway->create($data);
                
                http_response_code(201);
                echo json_encode([
                    "message" => "Product created",
                    "id" => $id
                ]);
                break;
            
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
    
    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        
        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }
        
        if (array_key_exists("size", $data)) {
            if (filter_var($data["size"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "size must be an integer";
            }
        }
        
        return $errors;
    }
}









