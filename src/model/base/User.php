<?php

namespace model\base;

class User
{
    public ?string $userId;
    public string $email;
    public string $username;
    public ?string $password;
    public string $bio;
    public ?string $profileImage;
    public ?string $profileImageExt;

    public function __construct(string $userId = null, string $email = '', string $username = '', ?string $password = null, string $bio = "", ?string $profileImage = null, ?string $profileImageExt = null)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->bio = $bio;
        $this->profileImage = $profileImage;
        $this->profileImageExt = $profileImageExt;
    }
}