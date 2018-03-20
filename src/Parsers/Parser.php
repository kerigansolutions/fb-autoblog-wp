<?php

namespace KeriganSolutions\FacebookFeed\Parsers;

use KeriganSolutions\FacebookFeed\Post;
use KeriganSolutions\FacebookFeed\Contracts\DataParser;

class Parser
{
    public function parse(DataParser $parser, $posts)
    {
        return $parser->parse($posts);
    }
}
