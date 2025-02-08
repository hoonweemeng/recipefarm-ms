<?php

namespace model\base;

class Bookmark
{
    public ?string $bookmarkId;
    public ?string $recipeId;
    public ?string $userId;
    public ?string $timestamp;

    public function __construct(?string $bookmarkId, ?string $recipeId, ?string $userId, ?string $timestamp)
    {
        $this->bookmarkId = $bookmarkId;
        $this->recipeId = $recipeId;
        $this->userId = $userId;
        $this->timestamp = $timestamp;
    }
}