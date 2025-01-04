<?php

namespace model\request;

class BookmarkIdRequest
{
    public string $bookmarkId;

    public function __construct(string $bookmarkId)
    {
        $this->bookmarkId = $bookmarkId;
    }
}