<?php

namespace model\request;
use model\genericmodel\Pagination;

class GetUserRecipeRequest extends PaginationRequest
{
    public string $userId;

    public function __construct(string $userId, Pagination $pagination)
    {
        $this->userId = $userId;
        $this->pagination = $pagination;
    }
}