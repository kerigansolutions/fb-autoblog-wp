<?php

namespace KeriganSolutions\FacebookFeed\LinkConverters;

use KeriganSolutions\FacebookFeed\Contracts\ConvertsLinks;

class FacebookConverter implements ConvertsLinks
{
    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function link()
    {
        return 'https://www.facebook.com/plugins/video.php?href='. $this->link;
    }
}
