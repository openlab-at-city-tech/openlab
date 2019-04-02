---
title: Genesis Theme Support
menuTitle: Theme Support
layout: layouts/base.njk
permalink: developer-features/theme-support/index.html
tags: docs
---

## Default theme supports

Genesis adds the following [theme supports](https://developer.wordpress.org/reference/functions/add_theme_support/) to Genesis child themes by default: 

```php
add_theme_support( 'menus' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'genesis-inpost-layouts' );
add_theme_support( 'genesis-archive-layouts' );
add_theme_support( 'genesis-admin-menu' );
add_theme_support( 'genesis-seo-settings-menu' );
add_theme_support( 'genesis-import-export-menu' );
add_theme_support( 'genesis-customizer-theme-settings' );
add_theme_support( 'genesis-customizer-seo-settings' );
add_theme_support( 'genesis-auto-updates' );
add_theme_support( 'genesis-breadcrumbs' );
```

These enable the following features:

<table>
  <tr>
    <th>Feature</th>
    <th>Description</th> 
  </tr>
  <tr>
    <td>menus</td>
    <td>WordPress menus.</td>
  </tr>
  <tr>
    <td>post-thumbnails</td>
    <td>See <a href="https://codex.wordpress.org/Post_Thumbnails">post thumbnails</a>.</td>
  </tr>
  <tr>
    <td>title-tag</td>
    <td>See <a href="https://codex.wordpress.org/Title_Tag">title tag</a>.</td>
  </tr>
  <tr>
    <td>automatic-feed-links</td>
    <td>See <a href="https://codex.wordpress.org/Automatic_Feed_Links">automatic feed links</a>.</td>
  </tr>
  <tr>
    <td>genesis-inpost-layouts</td>
    <td>Genesis layouts for posts and pages.</td>
  </tr>
  <tr>
    <td>genesis-archive-layouts</td>
    <td>Genesis layouts on archives.</td>
  </tr>
  <tr>
    <td>genesis-admin-menu</td>
    <td>Displays the Genesis menu.</td>
  </tr>
  <tr>
    <td>genesis-seo-settings-menu</td>
    <td>Displays the Genesis SEO settings menu.</td>
  </tr>
  <tr>
    <td>genesis-import-export-menu</td>
    <td>Displays the Genesis import/export menu.</td>
  </tr>
  <tr>
    <td>genesis-customizer-theme-settings</td>
    <td>Adds Genesis theme settings to the Customizer.</td>
  </tr>
  <tr>
    <td>genesis-customizer-seo-settings</td>
    <td>Adds Genesis SEO settings to the Customizer.</td>
  </tr>
  <tr>
    <td>genesis-auto-updates</td>
    <td>Adds a UI option to enable Genesis update checks.</td>
  </tr>  
  <tr>
    <td>genesis-breadcrumbs</td>
    <td>Genesis breadcrumb features and options.</td>
  </tr>
</table>

To opt-out of these, you can remove support in your child theme's `functions.php` with the [`remove_theme_support()`](https://developer.wordpress.org/reference/functions/remove_theme_support/) function:

```php
remove_theme_support( 'automatic-feed-links' );
```

## Opt-in theme supports

You can add support for additional WordPress and Genesis features using the [`add_theme_support()`](https://developer.wordpress.org/reference/functions/add_theme_support/) WordPress function in your child theme's `functions.php` file. 

### HTML5

We strongly recommend that you add the WordPress HTML5 theme support to ensure Genesis outputs HTML5 and not XHTML:

```php
add_theme_support(
	'html5',
	array(
		'caption',
		'comment-form',
		'comment-list',
		'gallery',
		'search-form',
	)
);
```

### Genesis accessibility

Add support for Genesis accessibility features (also strongly recommended).

```php
add_theme_support(
	'genesis-accessibility',
	array(
		'404-page',
		'drop-down-menu',
		'headings',		
		'search-form',
		'skip-links',
	)
);
```

<table>
  <tr>
    <th>Option</th>
    <th>Description</th> 
  </tr>
  <tr>
    <td>404-page</td>
    <td>Add an additional heading and adjust heading levels on the 404 page.</td>
  </tr>
  <tr>
    <td>genesis-scripts</td>
    <td>Add scripts to improve accessibility of drop-down menus.</td>
  </tr>
  <tr>
    <td>headings</td>
    <td>Add additional headings for screen reader users to aid navigation.</td>
  </tr>
  <tr>
    <td>search-form</td>
    <td>Improve search form labels.</td>
  </tr>
  <tr>
    <td>skip-links</td>
    <td>Add <a href="https://webaim.org/techniques/skipnav/">skip links</a> markup.</td>
  </tr>
</table>

### Genesis menus

Add a Primary and Secondary navigation menu with given names:

```php
add_theme_support(
	'genesis-menus',
	array(
		'primary'   => __( 'Primary Menu', 'genesis-sample' ),
		'secondary' => __( 'Secondary Menu', 'genesis-sample' ),
	)
);
```

### Genesis structural wraps

Add `div` elements with a `.wrap` class to wrap HTML that Genesis outputs. This can assist with styling for full-width layouts.

```php
add_theme_support(
	'genesis-structural-wraps',
	array(
		'header',
		'menu-primary',
		'menu-secondary',
		'footer-widgets',
		'footer'
	)
);
```

### Genesis responsive viewport

Add a viewport meta tag for responsive display on mobile browsers.

```php
add_theme_support( 'genesis-responsive-viewport' );
```

### Genesis after entry widget area

Add a widget area after post entries. Useful for calls to action.

```php
add_theme_support( 'genesis-after-entry-widget-area' );
```

### Genesis footer widget areas

Add the given number of footer widget areas. You must provide your own CSS to control the layout of these.

```php
add_theme_support( 'genesis-footer-widgets', 3 );
```

## Support for Genesis features in custom post types

Genesis automatically adds these features to the Post and Page type:

<table>
  <tr>
    <th>Option</th>
    <th>Description</th> 
  </tr>
  <tr>
    <td>genesis-seo</td>
    <td>Add a Genesis SEO panel to your post type.</td>
  </tr>
  <tr>
    <td>genesis-scripts</td>
    <td>Add a Genesis Scripts field to your post type for per-page scripts.</td>
  </tr>
  <tr>
    <td>genesis-layouts</td>
    <td>Add Genesis layout options to your post type.</td>
  </tr>
</table>

You can extend these features to your own custom post types using [`add_post_type_support()`](https://developer.wordpress.org/reference/functions/add_post_type_support/):

```php
add_post_type_support( 'your-custom-post-type-slug', array( 'genesis-seo', 'genesis-scripts', 'genesis-layouts' ) );
```