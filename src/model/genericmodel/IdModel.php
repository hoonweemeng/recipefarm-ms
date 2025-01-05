<?php

namespace model\genericmodel;

class IdModel
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}