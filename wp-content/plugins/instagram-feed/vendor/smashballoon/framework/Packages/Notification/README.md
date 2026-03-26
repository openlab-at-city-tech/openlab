# Notices

Add in-plugin notifications to your plugin.

### Usage

```php

use Smashballoon\Framework\Packages\Notification\Notices\SBNotices;

$sbi_notices = SBNotices::instance( 'instagram-feed-pro' );

```
Replace the `instagram-feed-pro` with your plugin slug.

### Add a notice

```php
$sbi_notices->add_notice( 'notice-id', 'notice-type', 'notice-args' );
```
notice-id: Unique ID for the notice.<br>
notice-type: Type of notice. Can be `error`, `warning`, `information`.<br>
notice-args: Array of arguments for the notice.

### Remove a notice

```php
$sbi_notices->remove_notice( 'notice-id' );
```

### Sample notice arguments

```php
array(
    'class' => 'notice notice-error',
    'id' => 'notice-id',
    'title' => array(
        'text' => 'Notice Title',
        'tag' => 'h3',
        'class' => 'notice-title',
    ),
    'message' => '<p>Notice message</p>',
    'priority' => 10,
    'dismissible' => true,
    'dismiss' => array(
        'class' => 'notice-dismiss',
        'icon' => 'https://www.example.com/icon.png',
        'tag' => 'button'
    ),
    'page' => array('instagram-feed'),
    'capability' => 'manage_options',
    'buttons' => array(
        array(
            'text' => 'Button Text',
            'class' => 'button button-primary',
            'id' => 'button-id',
            'url' => 'https://www.example.com',
            'target' => '_blank',
            'tag' => 'a',
        ),
    ),
    'buttons_wrap_start' => '<div class="notice-buttons">',
    'buttons_wrap_end' => '</div>',
    'icon' => array(
        'src' => 'https://www.example.com/icon.png',
        'alt' => 'Icon Alt',
        'wrap' => '<div class="notice-icon"><img {src} {alt}></div>',
    ),
    'wrap_schema' => '<div {class} {id}><div>{dismiss}</div>{icon}{title}{message}{buttons}</div>',
)

```
