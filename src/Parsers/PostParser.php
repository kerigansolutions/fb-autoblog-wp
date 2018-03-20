<?php

namespace KeriganSolutions\FacebookFeed\Parsers;

use KeriganSolutions\FacebookFeed\Post;
use KeriganSolutions\FacebookFeed\Contracts\DataParser;

class PostParser implements DataParser
{
    public function parse($rawPosts)
    {
        $parsed = [];
        foreach ($rawPosts as $data) {
            $post = (new Post($data))->format()->data;
            array_push($parsed, $post);
        }

        return $parsed;
    }
}
