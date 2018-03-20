<?php

namespace KeriganSolutions\FacebookFeed\Contracts;

interface DataFetcher
{
    public function get($limit, $before, $after);
}