# Recommended Blocks

The Recommended Blocks package adds a list of Gutenberg Blocks to your plugin. Each block will automatically install and activate the recommended plugin from the WordPress repository.

### Usage

```php

use Smashballoon\Framework\Packages\Blocks\RecommendedBlocks;

$recommended_blocks = new RecommendedBlocks();
$recommended_blocks->setup();

```

### Development

Run `npm install` and `npm run build` to build the JavaScript file. The built file will be located in the `build` directory. The built JavaScript and CSS file is enqueued in the `RecommendedBlocks` class.

The recommended blocks are defined in the `blocks.js` file in the `src` directory. This file contains an array of block objects. Each block object contains the following properties:

### Sample block object

```js
{
    name: 'instagram-feed',
    title: __('Instagram Feed', 'smashballoon'),
    description: __('Display your Instagram feeds.', 'smashballoon'),
    pluginPath: 'instagram-feed/instagram-feed.php',
    proPluginPath: 'instagram-feed-pro/instagram-feed.php',
    pluginDescription: __('Custom Instagram Feed is a highly customizable way to display feeds from your Instagram account. Promote your latest content and update your site content automatically.', 'smashballoon'),
    keywords: [
        __('Instagram', 'smashballoon'),
        __('Photos', 'smashballoon'),
        __('Social Media', 'smashballoon'),
    ],
}

```

Add a block object to the `recommendedBlocks` array in the `blocks.js` file to add a new block to the list.

The `name` property is the block name and should be unique.

The `title` property is the block title.

The `description` property is the block description.

The `pluginPath` property is the plugin path in the WordPress repository.

The `proPluginPath` property is the pro plugin path in the plugins repository. This is optional and will check if the pro plugin is installed before recommending the plugin from the WordPress.org

The `pluginDescription` property is the plugin description. This will be displayed in the plugin details modal when the user clicks on the Recommended block. This should contain a brief description of the plugin.

The `keywords` property is an array of keywords for the block. The user can search for blocks using these keywords.

