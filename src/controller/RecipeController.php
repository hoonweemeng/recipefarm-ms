<?php
namespace controller;

use DAL\RecipeDAL;
use utils\Utility;
use model\genericmodel\GenericResponse;
use model\base\Recipe;
use model\base\User;
use model\request\RecipeIdRequest;

class RecipeController
{
    public function __construct(private RecipeDAL $recipeDAL)
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
            case "create":
                $this->createRecipe();
                break;

            case "update":
                // Code to execute   
                break;

            case "delete":
                // Code to execute   
                break;
                
            case "search":
                // Code to execute   
                break;
                
            case "latest":
                // Code to execute   
                break;
                
            case "detail":
                // Code to execute   
                break;

            default:
                Utility:: errorNotFound();

        }
    }

    public function createRecipe(): void
    {
        $data = Utility:: getRequestBody(Recipe::class);
        
        //validate
        $userId = Utility:: getUserId();
        if (!isset($userId))
        {
            //error
            echo 'user not logged in';
            exit;
        }
        else {
            $data->userId = $userId;
        }

        $recipeId = Utility:: generateUUID();
        $data->recipeId = $recipeId;
        $this->recipeDAL->createRecipe($data);
                
        $response = new GenericResponse(true, null, null, new RecipeIdRequest($recipeId));
        echo json_encode($response);
    }

    public function updateRecipe(): void
    {
        
    }
    
}









