<?php

namespace model\request;

class RecipeIdRequest
{
    public string $recipeId;

    public function __construct(string $recipeId)
    {
        $this->recipeId = $recipeId;
    }
}