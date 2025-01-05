<?php
namespace controller;

use DAL\RecipeDAL;
use model\base\User;
use model\genericmodel\GenericResponse;
use utils\Utility;

class UserController
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

    }

}









