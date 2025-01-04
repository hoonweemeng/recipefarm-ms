<?php

namespace model\request;
use model\genericmodel\Pagination;

class PaginationRequest
{
    public Pagination $pagination;

    public function __construct(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }
}