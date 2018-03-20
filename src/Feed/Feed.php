<?php

namespace KeriganSolutions\FacebookFeed\Feed;

use KeriganSolutions\FacebookFeed\Contracts\DataParser;

class Feed
{
    public $paging;
    public $posts;
    protected $parser;

    public function __construct(DataParser $parser, $rawFeed)
    {
        $this->posts = $rawFeed->data;
        $this->paging = $rawFeed->paging;
        $this->parser = $parser;
    }

    public function output()
    {
        $this->configureFeed();

        return $this;
    }

    protected function configureFeed()
    {
        $this->posts = $this->parser->parse($this->posts);
    }
}
