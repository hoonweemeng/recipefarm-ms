<?php

namespace model\request;

class LikeIdRequest
{
    public string $likeId;

    public function __construct(string $likeId)
    {
        $this->likeId = $likeId;
    }
}