---
title: Genesis Configuration
menuTitle: Configuration
layout: layouts/base.njk
permalink: developer-features/configuration/index.html
tags: docs
minVersion: Genesis 2.8.0+
---

The Genesis configuration API lets Genesis child theme developers do two&nbsp;things:

1. [Override certain Genesis parent theme settings](#override-genesis-features).
2. [Load configuration data](#load-child-theme-settings-from-your-theme's-config-folder) from the child theme's `config` folder.

<p class="notice-small">
To use these features, create the <code>config</code> folder in the root of your child theme (at the same level as <code>style.css</code>) if it does not exist already.
</p>

## Override Genesis features

The following config files from the Genesis parent theme can be overridden by placing a file of the same name in your child theme's `config` folder.

- `genesis/config/breadcrumbs.php`
- `genesis/config/customizer-seo-settings.php`
- `genesis/config/customizer-theme-settings.php`
- `genesis/config/layouts.php`

You can copy the code in these files to your child theme, then make desired changes.

For example, to alter the layouts your child theme offers so that it only includes a “full-width” and “content-sidebar” layout instead of the six layouts Genesis offers by default, you can create a file at `your-child-theme/config/layouts.php` with the following content:

```php
<?php
/**
 * Your Theme Name
 *
 * Overrides `genesis/config/layouts.php` to set default theme layouts.
 *
 * @package Theme Name
 * @author  Your Name
 * @license GPL-2.0-or-later
 * @link    https://example.com/
 */

// Path to layout images in the Genesis parent theme.
$url = GENESIS_ADMIN_IMAGES_URL . '/layouts/';

return array(
	'content-sidebar'         => array(
		'label'   => __( 'Content, Primary Sidebar', 'your-theme-slug' ),
		'img'     => $url . 'cs.gif',
		'default' => is_rtl() ? false : true,
		'type'    => array( 'site' ),
	),
	'full-width-content'      => array(
		'label' => __( 'Full Width Content', 'your-theme-slug' ),
		'img'   => $url . 'c.gif',
		'type'  => array( 'site' ),
	),
);
```

Another approach is to use the child theme config files to load parent theme config, then add and unset default values instead of reproducing the parent config file contents explicitly. For example, the same result above can be achieved with a `your-child-theme/config/layouts.php` file that looks like this:

```php
<?php
/**
 * Your Theme Name
 *
 * Overrides `genesis/config/layouts.php` to set default theme layouts.
 *
 * @package Theme Name
 * @author  Your Name
 * @license GPL-2.0-or-later
 * @link    https://example.com/
 */

$layouts = array();

$genesis_layouts_config = get_template_directory() . '/config/layouts.php';

if ( is_readable( $genesis_layouts_config ) ) {
	$layouts = require $genesis_layouts_config;
	unset( $layouts['sidebar-content'] );
	unset( $layouts['content-sidebar-sidebar'] );
	unset( $layouts['sidebar-sidebar-content'] );
	unset( $layouts['sidebar-content-sidebar'] );
}

return $layouts;
```

## Load child theme settings from your theme's config folder

Genesis 2.8.0+ includes a `genesis_get_config()` function. This allows you to fetch custom configuration data from your child theme's `config` folder.

Child themes contain PHP configuration data — the data that makes your theme different to other themes — that is scattered across PHP files. Your `functions.php` file might include code that looks like this, for example:

```php
add_theme_support(
	'custom-logo',
	array(
		'height'      => 120,
		'width'       => 700,
		'flex-height' => true,
		'flex-width'  => true,
	)
);
```

And you may have a separate file that sets up WordPress block editor features, such as custom font sizes:

```php
add_theme_support(
	'editor-font-sizes',
	array(
		array(
			'name'      => __( 'Small', 'your-theme-slug' ),
			'shortName' => __( 'S', 'your-theme-slug' ),
			'size'      => 12,
			'slug'      => 'small',
		),
		array(
			'name'      => __( 'Normal', 'your-theme-slug' ),
			'shortName' => __( 'M', 'your-theme-slug' ),
			'size'      => 16,
			'slug'      => 'normal',
		),
		array(
			'name'      => __( 'Large', 'your-theme-slug' ),
			'shortName' => __( 'L', 'your-theme-slug' ),
			'size'      => 20,
			'slug'      => 'large',
		),
		array(
			'name'      => __( 'Larger', 'your-theme-slug' ),
			'shortName' => __( 'XL', 'your-theme-slug' ),
			'size'      => 24,
			'slug'      => 'larger',
		),
	)
);
```

With `genesis_get_config()`, you can instead write code like this:

```php
add_theme_support( 'custom-logo', genesis_get_config( 'custom-logo' ) );
```

With a file at `your-theme-name/config/custom-logo.php` that looks like this:

```php
return array(
	'height'      => 120,
	'width'       => 700,
	'flex-height' => true,
	'flex-width'  => true,
);
```

The `editor-font-sizes` theme support becomes:

```php
add_theme_support( 'editor-font-sizes', genesis_get_config( 'editor-font-sizes' ) );
```

With a file at `your-theme-name/config/editor-font-sizes.php` that looks like this:

```php
return array(
	array(
		'name'      => __( 'Small', 'your-theme-slug' ),
		'shortName' => __( 'S', 'your-theme-slug' ),
		'size'      => 12,
		'slug'      => 'small',
	),
	array(
		'name'      => __( 'Normal', 'your-theme-slug' ),
		'shortName' => __( 'M', 'your-theme-slug' ),
		'size'      => 16,
		'slug'      => 'normal',
	),
	array(
		'name'      => __( 'Large', 'your-theme-slug' ),
		'shortName' => __( 'L', 'your-theme-slug' ),
		'size'      => 20,
		'slug'      => 'large',
	),
	array(
		'name'      => __( 'Larger', 'your-theme-slug' ),
		'shortName' => __( 'XL', 'your-theme-slug' ),
		'size'      => 24,
		'slug'      => 'larger',
	),
);
```

Moving your configuration data to the `config` folder in this way is optional. You can use it for all PHP data in your theme, for select data, or not at all. The advantages of this approach are:

- **It puts theme configuration in a single place.** Instead of being spread throughout the theme in disparate PHP files, the bulk of what makes your theme unique outside of its CSS will be stored in a single folder. The benefits of this include better readability and maintainability.

- **It opens the door to new tooling.** This includes theme generators (where a website generates a custom build of a theme by writing new config files that match user preferences), as well as marketing pages that can automatically generate a list of theme features by skimming content from each theme's config folder. Although these tools are already possible with configuration spread throughout a theme, centralizing config makes this easier.

<p class="notice-small">
The <a href="#override-genesis-features">parent theme config file names</a> are reserved for use by Genesis. You should not name a config file <code>breadcrumbs.php</code>, for example, unless you intend to override Genesis breadcrumbs configuration.
</p>
