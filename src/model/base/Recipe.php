<?php

namespace model\base;


class Recipe
{
    public string $recipeId;
    public string $title;
    public string $description;
    public int $duration;
    public int $servings;
    public string $ingredients;
    public string $instructions;
    public ?string $recipeImage;
    public ?string $recipeImageExt;
    public \DateTime $timestamp;
    public string $userId;
    public int $likes;

    public function __construct(
        string $recipeId,
        string $title,
        string $description,
        int $duration,
        int $servings,
        string $ingredients,
        string $instructions,
        ?string $recipeImage,
        ?string $recipeImageExt,
        \DateTime $timestamp,
        string $userId,
        int $likes
    ) {
        $this->recipeId = $recipeId;
        $this->title = $title;
        $this->description = $description;
        $this->duration = $duration;
        $this->servings = $servings;
        $this->ingredients = $ingredients;
        $this->instructions = $instructions;
        $this->recipeImage = $recipeImage;
        $this->recipeImageExt = $recipeImageExt;
        $this->timestamp = $timestamp;
        $this->userId = $userId;
        $this->likes = $likes;
    }
}