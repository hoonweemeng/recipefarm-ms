<?php

namespace model\genericmodel;

class Pagination
{
    public int $currentPage;
    public int $pageSize;

    public function __construct(int $currentPage, int $pageSize)
    {
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
    }
}