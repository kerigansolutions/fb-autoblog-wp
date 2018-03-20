<?php

namespace KeriganSolutions\FacebookFeed\LinkConverters;

use KeriganSolutions\FacebookFeed\Contracts\ConvertsLinks;

class VimeoConverter implements ConvertsLinks
{
    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }
    public function link()
    {
        $vimeoId = parse_url($this->link, PHP_URL_PATH);

        return 'https://player.vimeo.com/video' . $vimeoId .'?autoplay=0';
    }
}