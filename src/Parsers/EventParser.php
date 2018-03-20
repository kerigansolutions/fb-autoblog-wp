<?php

namespace KeriganSolutions\FacebookFeed\Parsers;

use KeriganSolutions\FacebookFeed\Event;
use KeriganSolutions\FacebookFeed\Contracts\DataParser;

class EventParser implements DataParser
{
    public function parse($rawEvents)
    {
        $parsed = [];
        foreach ($rawEvents as $data) {
            $event = (new Event($data))->data;
            array_push($parsed, $event);
        }

        return $parsed;
    }
}
