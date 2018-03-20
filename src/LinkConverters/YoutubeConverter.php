<?php

namespace KeriganSolutions\FacebookFeed\LinkConverters;

use KeriganSolutions\FacebookFeed\Contracts\ConvertsLinks;

class YoutubeConverter implements ConvertsLinks
{
    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }
    public function link()
    {
        parse_str(parse_url($this->link, PHP_URL_QUERY), $urlArray);

        return 'https://youtube.com/embed/'. $urlArray['v'];

    }
}