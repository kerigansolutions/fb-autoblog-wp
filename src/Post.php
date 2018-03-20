<?php
namespace KeriganSolutions\FacebookFeed;

use KeriganSolutions\FacebookFeed\Fetchers\EventsFetcher;
use Carbon\Carbon;

class Post
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function format()
    {
        $fetcher = new EventsFetcher();

        if ($this->data->type == 'video') {
            $video            = new Video();
            $caption  = $this->data->caption ?? 'Try Facebook Player';

            $this->data->link = $video->getConvertedLink($this->data->link, $caption);
        }

        if ($this->data->type == 'event') {
            $this->data->full_picture = $this->data->attachments->data[0]->media->image->src ?? null;
            $this->data->message = $this->data->caption;
        }

        if (isset($this->data->attachments->data[0]->media->image->width) && $this->data->attachments->data[0]->media->image->width <= 100) {
            $this->full_picture = null;
        }

        if (! isset($this->data->message)) {
            $this->data->message = $this->data->description ?? 'Click here to read more on Facebook';
        }

        $this->data->diff = Carbon::parse($this->data->created_time)->diffForHumans();

        return $this;
    }
}