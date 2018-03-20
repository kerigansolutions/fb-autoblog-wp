<?php

namespace KeriganSolutions\FacebookFeed\Parsers;

use KeriganSolutions\FacebookFeed\Review;
use KeriganSolutions\FacebookFeed\Contracts\DataParser;

class ReviewParser implements DataParser
{
    public function parse($rawPosts)
    {
        $parsed = [];
        foreach ($rawPosts as $data) {
            if ($data->rating >= 4) {
                $review = (new Review($data))->format()->data;
                array_push($parsed, $review);
            }
        }

        return $parsed;
    }
}
