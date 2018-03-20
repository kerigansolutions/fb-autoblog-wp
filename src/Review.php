<?php
namespace KeriganSolutions\FacebookFeed;

use KeriganSolutions\FacebookFeed\Fetchers\PostFetcher;

class Review
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function format()
    {
        return $this;
    }
}