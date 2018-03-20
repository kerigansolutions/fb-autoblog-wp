<?php

namespace KeriganSolutions\FacebookFeed\Fetchers;

use KeriganSolutions\FacebookFeed\Contracts\DataFetcher;

class FacebookRequest
{
    protected $limit;
    protected $before;
    protected $after;

    public function __construct($limit, $before, $after)
    {
        $this->limit = $limit;
        $this->before = $before;
        $this->after = $after;
    }

    public function fetch(DataFetcher $fetcher)
    {
        return $fetcher->get($this->limit, $this->before, $this->after);
    }
}