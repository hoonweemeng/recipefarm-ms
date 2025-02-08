<?php
namespace controller;

use DAL\RecipeDAL;
use utils\Utility;
use model\genericmodel\GenericResponse;
use model\base\Recipe;
use model\genericmodel\IdModel;
use model\genericmodel\Pagination;
use model\request\PaginationRequest;
use model\request\GetUserRecipeRequest;

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
                $this->updateRecipe(); 
                break;

            case "delete":
                // Code to execute   
                break;
                
            case "search":
                // Code to execute   
                break;
                
            case "latest":
                $this->latestRecipe();   
                break;
                
            case "detail":
                // Code to execute   
                break;

            case "user":
                $this->getUserRecipe();
                break;

            case "bookmark":
                $this->getBookmarkRecipe();
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
            echo json_encode(new GenericResponse(false, "user not logged in", null, null));
            exit;
        }
        else {
            $data->userId = $userId;
        }

        $recipeId = Utility:: generateUUID();
        $data->recipeId = $recipeId;
        $this->recipeDAL->createRecipe($data);
                
        $response = new GenericResponse(true, null, null, new IdModel($recipeId));
        echo json_encode($response);
    }

    public function updateRecipe(): void
    {
        $data = Utility:: getRequestBody(Recipe::class);
        
        //validate
        $userId = Utility:: getUserId();
        if (!isset($userId))
        {
            //error
            echo json_encode(new GenericResponse(false, "user not logged in", null, null));
            exit;
        }
        else {
            $data->userId = $userId;
        }

        $this->recipeDAL->updateRecipe($data);
                
        $response = new GenericResponse(true, null, null, new IdModel($data->recipeId));
        echo json_encode($response);
    }

    public function latestRecipe(): void
    {
        $data = Utility:: getRequestBody(PaginationRequest::class);

        $recipeList = $this->recipeDAL->getLatestRecipes($data->pagination);
                
        $response = new GenericResponse(true, null, null, $recipeList);
        echo json_encode($response);
    }

    public function getUserRecipe(): void
    {
        $data = Utility:: getRequestBody(GetUserRecipeRequest::class);

        $recipeList = $this->recipeDAL->getUserRecipes($data->userId, $data->Pagination);
                
        $response = new GenericResponse(true, null, null, $recipeList);
        echo json_encode($response);
    }

    public function getBookmarkRecipe(): void
    {
        $data = Utility:: getRequestBody(PaginationRequest::class);

        //validate
        $userId = Utility:: getUserId();
        if (!isset($userId))
        {
            //error
            echo json_encode(new GenericResponse(false, "user not logged in", null, null));
            exit;
        }

        $recipeList = $this->recipeDAL->getBookmarkRecipes($userId, $data->pagination);
                
        $response = new GenericResponse(true, null, null, $recipeList);
        echo json_encode($response);
    }

}









