<?php
namespace controller;

use DAL\UserDAL;
use model\base\User;
use model\genericmodel\GenericResponse;
use model\request\LoginRequest;
use utils\Utility;

class UserController
{
    public function __construct(private UserDAL $userDAL)
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
                $this->login();
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
        
        $data = Utility:: getRequestBody(User::class);
        Utility:: trimData($data);

        $userId = Utility:: generateUUID();
        $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);
        
        $this->userDAL->createUser($userId, $data->email, $data->username, $hashedPassword);
    }

    public function login(): void 
    {
        
        $data = Utility:: getRequestBody(LoginRequest::class);
        Utility:: trimData($data);

        $user = $this->userDAL->getUserDetailByEmail($data->email);

        if ($user != null && password_verify($data->password, $user->password))
        {
            //remove password
            $user->password = null;
            $response = new GenericResponse(true,null,$user);
            echo json_encode($response);
        }
        else {
            
            $response = new GenericResponse(false,"Invalid credentials.",null);
            echo json_encode($response);
        }
    }

}









