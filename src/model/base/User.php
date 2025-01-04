<?php

namespace model\base;

class User
{
    public string $userId;
    public string $email;
    public string $username;
    public ?string $password;
    public string $bio;
    public ?string $profileImage;
    public ?string $profileImageExt;

    public function __construct(string $email = '', string $username = '', ?string $password = null)
    {
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->bio = '';
        $this->profileImage = null;
        $this->profileImageExt = null;
    }
}