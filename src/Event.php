<?php

namespace KeriganSolutions\FacebookFeed;

class Event
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}