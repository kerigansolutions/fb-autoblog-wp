<?php

namespace KeriganSolutions\FacebookFeed\LinkConverters;

use KeriganSolutions\FacebookFeed\Contracts\ConvertsLinks;

class LinkConverter
{
    public function convert(ConvertsLinks $video)
    {
        return $video->link();
    }
}