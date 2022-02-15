# PublishPress WordPress Reviews Library

The PublishPress WordPress Reviews is a library for displaying a banner to users asking for a five-star review.

## Installation

We do recommend using composer for adding this library as a requirement:

```shell
$ composer require publishpress/wordpress-reviews
```

## How to use it

If your plugin does not load the composer's autoloader yet, you need to add the following code:

```php
<?php

require_once 'vendor/autoload.php';
```

**Only free plugins should initialize this library**.

It can be instantiated and initialized in the method that loads the main WordPress hooks.

When instantiating this library, you have to pass three params:

* the plugin slug (the same one used in the URL of the WordPress repository)
* the plugin's name
* the URL for the logo (optional)

### Configuring the criteria to display the banner in the Free plugin

It by default displays the banner when the following conditional is true:

```php
is_admin() && current_user_can('edit_posts')
```

But you can specify custom criteria to display the banner hooking into the filter `<plugin_slug>_wp_reviews_allow_display_notice`.

```php
<?php

use PublishPress\WordPressReviews\ReviewsController;

class MyPlugin
{
    /**
    * @var  ReviewsController
    */
    private $reviewController;
    
    public function __construct()
    {
        $this->reviewController = new ReviewsController(
            'my-plugin',
            'My Plugin',
            MY_PLUGIN_URL . '/assets/img/logo.png'
        );
    }
    
    public function init()
    {
        // .......
        add_filter('my-plugin_wp_reviews_allow_display_notice', [$this, 'shouldDisplayBanner']);
        
        $this->reviewController->init();
    }
    
    public function shouldDisplayBanner($shouldDisplay)
    {
        global $pagenow;

        if (! is_admin() || ! current_user_can('edit_posts')) {
            return false;
        }

        if ($pagenow === 'admin.php' && isset($_GET['page'])) {
            if ($_GET['page'] === 'pp-page1') {
                return true;
            }

            if ($_GET['page'] === 'pp-page2') {
                return true;
            }
        }

        if ($pagenow === 'edit.php' && isset($_GET['post_type'])) {
            if ($_GET['post_type'] === 'pp_custom_post_type') {
                return true;
            }
        }

        return false;
    }
    
    // .......
}
```

### Configuring the criteria to display the banner in the Pro plugin

In case the Pro plugin has additional pages where you want to display the banner, feel free to use the same filter as
in the free plugin but with a higher priority. You can choose to override the conditions used in the Free plugin
or to append more conditions, for different pages.

```php
add_filter('my-plugin_wp_reviews_allow_display_notice', [$this, 'shouldDisplayBanner'], 20);
```

```php
 /**
 * @param $shouldDisplay
 * @return bool|null
 */
public function shouldDisplayBanner($shouldDisplay)
{
    global $pagenow;

    if ($shouldDisplay) {
        return true;
    }
    
    if ($pagenow === 'edit.php' && isset($_GET['post_type'])) {
        if ($_GET['post_type'] === 'custom-posttype') {
            return true;
        }
    }

    return $shouldDisplay;
}
```

### Backward compatibility with older versions

By default, the library will use the plugin's slug as a prefix for the actions, metadata, and options:

```php
[
    'action_ajax_handler' => $this->pluginSlug . '_action',
    'option_installed_on' => $this->pluginSlug . '_wp_reviews_installed_on',
    'nonce_action' => $this->pluginSlug . '_wp_reviews_action',
    'user_meta_dismissed_triggers' => '_' . $this->pluginSlug . '_wp_reviews_dismissed_triggers',
    'user_meta_last_dismissed' => '_' . $this->pluginSlug . '_wp_reviews_last_dismissed',
    'user_meta_already_did' => '_' . $this->pluginSlug . '_wp_reviews_already_did',
    'filter_triggers' => $this->pluginSlug . '_wp_reviews_triggers',
]
```

If you already use the original library in your plugin and want to keep compatibility with current sites data, you can customize the
hooks and keys for the data stored in the DB using the filter `<plugin_slug>_wp_reviews_meta_map`:

```php
<?php

add_filter('my-plugin_wp_reviews_meta_map', 'my_plugin_wp_reviews_meta_map');

function my_plugin_wp_reviews_meta_map($metaMap)
{
    // You can override all the array, or specific keys.
    $metaMap = [
        'action_ajax_handler' => 'legacy_slug_ajax_action',
        'option_installed_on' => 'legacy_slug_wp_reviews_installed_on',
        'nonce_action' => 'legacy_slug_wp_reviews_action',
        'user_meta_dismissed_triggers' => '_legacy_slug_wp_reviews_dismissed_triggers',
        'user_meta_last_dismissed' => '_legacy_slug_wp_reviews_last_dismissed',
        'user_meta_already_did' => '_legacy_slug_wp_reviews_already_did',
        'filter_triggers' => 'legacy_slug_wp_reviews_triggers',
    ];

    return $metaMap;
}
```

## Common questions

### Should I use this library on Pro plugins?

Pro plugins that embed the free plugin code **should not instantiate or initialize this library** otherwise, users will
probably see duplicated admin notices or will be asked for a review twice.

Keeping the library activated only by the free plugin allows both versions, free and pro,
to share the same options and metadata stored in the database, avoiding duplicated banners or review requests.

Please, **only initialize this library in the Free plugin** and do not disable or block it in the Pro version. We want to keep it enabled
for both free and pro users.


## Testing

You can test the banner in the WordPress admin by changing the option `<plugin-slug>_wp_reviews_installed_on` in the options table. Set it for older data to make sure the time difference is bigger than the selected trigger

## Copyright

Based on the [library](https://github.com/danieliser/WP-Product-In-Dash-Review-Requests) created by [Daniel Iser](https://danieliser.com).