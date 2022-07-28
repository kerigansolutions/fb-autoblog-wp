# KMA WordPress Facebook Feed
Easily pull posts and events into your WordPress Site from a Facebook page that you manage. Previous versions of this package retrieved posts in real time. Since 2.0, WorDPress is required and a spcial Admin page in WordPress is created. Posts and Events are fetched and added to the WP database using a cron that runs every hour. 

## Status
Currently the app is configured to work with Posts and Events. Reviews and Albums are in progress.

## Installation
`composer require kerigansolutions/fb-autoblog-wp`

## Setup
1. Make sure you have admin access to the page you need to manage.
2. Log into WordPress and go to the new Facebook Settings menu item.
3. Authorize the app using app secret (FYI only KMA knows this).
4. Use the Auth tool to authorize the app to use a Facebook page you manage.
5. Use the sync tool to build the database of posts.
6. Program a view to show the data in your templates.

### Include or Extend the WP Admin class:
```php

use KeriganSolutions\FacebookFeed;
use KeriganSolutions\FacebookFeed\WP\FacebookPost;

class Facebook extends FacebookFeed\WP\Admin
{

  public $postsEnabled = true;
  public $eventsEnabled = false;
  public $photosEnabled = false;
  public $reviewsEnabled = false;

  // retrieve posts from WP database
  public function getFbPosts($num = -1, $args = [])
  {
    return (new FacebookPost())->query($num, $args);
  }
  
}
```

### Or make the API call and retrieve the results directly:
```php
use KeriganSolutions\FacebookFeed\WP\FacebookPost;

$feed  = new Facebook;
$results = $feed->query(5);

```
```javascript
fetch("/wp-json/kerigansolutions/v1/get-kma-fb-post", {
    method: 'GET',
    mode: 'cors',
    cache: 'no-cache',
    headers: {
        'Content-Type': 'application/json',
    },
})
.then(r => r.json())
.then((res) => {
    // do something with res
})
```