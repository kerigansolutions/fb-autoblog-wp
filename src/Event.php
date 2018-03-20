<?php

namespace KeriganSolutions\FacebookFeed;

use Carbon\Carbon;


class Event
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}