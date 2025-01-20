<?php
namespace controller;

use DAL\UserDAL;
use model\base\User;
use model\base\ValidationModel;
use model\genericmodel\GenericResponse;
use model\genericmodel\IdModel;
use model\request\LoginRequest;
use model\request\RecipeIdRequest;
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
                $this->getUserDetail();
                break;

            default:
                Utility:: errorNotFound();

        }
    }

    public function getUserDetail(): void 
    {        
        $userId = Utility:: getUserId();
        $user = $this->userDAL->getUserDetailByUserId($userId);

        if ($user == null) {
            $response = new GenericResponse(false, "User does not exist.", null, null);
            echo json_encode($response);
        }
        else {
            $user->password = null;
            $response = new GenericResponse(true,null,null,$user);
            echo json_encode($response);
        }
    }

    public function createUser(): void 
    {
        
        $data = Utility:: getRequestBody(User::class);
        Utility:: trimData($data);

        $validationList = [
            $this->validateEmail($data->email),
            $this->validatePassword($data->password),
            $this->validateUsername($data->username)
        ];

        $invalidEntries = array_values(array_filter($validationList, fn($v) => !$v->isValid));

        if (!empty($invalidEntries)) {
            $response = new GenericResponse(false, null, $invalidEntries, null);
            echo json_encode($response);
            exit;
        }

        $userId = Utility:: generateUUID();
        $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);
        
        $this->userDAL->createUser($userId, $data->email, $data->username, $hashedPassword);

        $response = new GenericResponse(true, null, null, new IdModel($userId));
        echo json_encode($response);
    }

    public function login(): void 
    {
        
        $data = Utility:: getRequestBody(LoginRequest::class);
        Utility:: trimData($data);

        $user = $this->userDAL->getUserDetailByEmail($data->email);

        $hashedPassword = $user->password;

        if ($user != null && password_verify($data->password, $hashedPassword))
        {
            //remove password
            $user->password = null;
            $response = new GenericResponse(true,null,null,$user);
            echo json_encode($response);
        }
        else {
            
            $response = new GenericResponse(false,"Invalid credentials.",null);
            echo json_encode($response);
        }
    }


    private function validateEmail(string $email): ValidationModel
    {
        $fieldTitle = 'email';
        if (empty(trim($email))) {
            return new ValidationModel($fieldTitle, false, 'Email Address is required.');
        }

        $emailRegex = '/^[^@\s]+@[^@\s]+\.[^@\s]+$/';
        if (!preg_match($emailRegex, $email)) {
            return new ValidationModel($fieldTitle, false, 'Email Address is invalid.');
        }

        if ($this->userDAL->checkIfEmailExist($email)) {
            return new ValidationModel($fieldTitle, false, 'This email address has already been used.');
        }

        return new ValidationModel($fieldTitle, true);
    }

    private function validatePassword(string $password): ValidationModel
    {
        $fieldTitle = 'password';
        if (empty(trim($password))) {
            return new ValidationModel($fieldTitle, false, 'Password is required.');
        }

        if (strlen($password) < 8) {
            return new ValidationModel($fieldTitle, false, 'Password must be at least 8 characters long.');
        }

        return new ValidationModel($fieldTitle, true);
    }

    private function validateUsername(string $username): ValidationModel
    {
        $fieldTitle = 'username';
        if (empty(trim($username))) {
            return new ValidationModel($fieldTitle, false, 'Username is required.');
        }

        if (strpos($username, ' ') !== false) {
            return new ValidationModel($fieldTitle, false, 'Username must not contain whitespace.');
        }

        if (strlen($username) > 50) {
            return new ValidationModel($fieldTitle, false, 'Username must not exceed 50 characters long.');
        }

        if ($this->userDAL->checkIfUsernameExist($username)) {
            return new ValidationModel($fieldTitle, false, 'Username is taken. Please choose another.');
        }

        return new ValidationModel($fieldTitle, true);
    }

}









