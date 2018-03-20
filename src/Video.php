<?php

namespace KeriganSolutions\FacebookFeed;

use KeriganSolutions\FacebookFeed\LinkConverters\LinkConverter;
use KeriganSolutions\FacebookFeed\LinkConverters\VimeoConverter;
use KeriganSolutions\FacebookFeed\LinkConverters\YoutubeConverter;
use KeriganSolutions\FacebookFeed\LinkConverters\FacebookConverter;

class Video
{
    public function getConvertedLink($link, $caption)
    {
        $converter = new LinkConverter();

        if ($caption == 'youtube.com') {
            return $converter->convert(new YoutubeConverter($link));
        } elseif ($caption == 'vimeo.com') {
            return $converter->convert(new VimeoConverter($link));
        } else {
            return $converter->convert(new FacebookConverter($link));
        }
    }
}