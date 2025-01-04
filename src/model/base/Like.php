<?php

namespace model\base;

class Like
{
    public string $likeId;
    public string $recipeId;
    public string $userId;

    public function __construct(string $likeId, string $recipeId, string $userId)
    {
        $this->likeId = $likeId;
        $this->recipeId = $recipeId;
        $this->userId = $userId;
    }
}