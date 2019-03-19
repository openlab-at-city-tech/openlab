---
title: Genesis Constants
menuTitle: Constants
layout: layouts/base.njk
permalink: developer-features/constants/index.html
tags: docs
---

## Child theme constants

We recommend that you define the following constants in your Genesis child theme's `functions.php` file:

```php
define( 'CHILD_THEME_NAME', 'Genesis Sample' );
define( 'CHILD_THEME_URL', 'https://www.studiopress.com/' );
define( 'CHILD_THEME_VERSION', '2.8.0' );
```

- `CHILD_THEME_NAME` is used for stylesheet handles and in the default footer.
- `CHILD_THEME_URL` is used in the default site footer.
- `CHILD_THEME_VERSION` is used as a cache-busting string when enqueuing the child theme stylesheet.

Alternatives to setting these constants manually (such as pulling the info from the stylesheet header) are being considered.

## Directory and URL constants

These constants are available to child theme developers for convenience and performance:

<table>
  <tr>
    <th>Constant</th>
    <th>Equivalent</th> 
  </tr>
  <tr>
    <td><code>PARENT_DIR</code></td>
    <td><a href="https://codex.wordpress.org/Function_Reference/get_template_directory"><code>get_template_directory()</code></a></td> 
  </tr>
  <tr>
    <td><code>CHILD_DIR</code></td>
    <td><a href="https://codex.wordpress.org/Function_Reference/get_stylesheet_directory"><code>get_stylesheet_directory()</code></a></td> 
  </tr>
  <tr>
    <td><code>PARENT_URL</code></td>
    <td><a href="https://codex.wordpress.org/Function_Reference/get_template_directory_uri"><code>get_template_directory_uri()</code></a></td> 
  </tr>
  <tr>
    <td><code>CHILD_URL</code></td>
    <td><a href="https://codex.wordpress.org/Function_Reference/get_stylesheet_directory_uri"><code>get_stylesheet_directory_uri()</code></a></td> 
  </tr>
</table>

You can use the code in the *Constant* column wherever you might use the code in the *Equivalent* column in your child theme. So instead of:

```php
wp_enqueue_script(
	'custom-theme',
	get_stylesheet_directory_uri() . '/js/custom-theme.js',
	array( 'jquery' ),
	CHILD_THEME_VERSION,
	true
);
```

You can do this for brevity and to avoid an additional function call:

```php
wp_enqueue_script(
	'custom-theme',
	CHILD_URL . '/js/custom-theme.js', // <-- Constant used here.
	array( 'jquery' ),
	CHILD_THEME_VERSION,
	true
);
```

## Additional constants

Genesis uses additional constants that are mostly useful for those contributing to Genesis itself:

<table>
  <tr>
    <th>Constant</th>
    <th>Example Values</th> 
  </tr>
  <tr>
    <td><code>PARENT_THEME_NAME</code></td>
    <td>Genesis</td> 
  </tr>
  <tr>
    <td><code>PARENT_THEME_VERSION</code></td>
    <td>2.8.0, 2.8.0-beta2</td> 
  </tr>
  <tr>
    <td><code>PARENT_THEME_BRANCH</code></td>
    <td>2.8</td> 
  </tr>
  <tr>
    <td><code>GENESIS_IMAGES_URL</code></td>
    <td>https://example.com/wp-content/themes/genesis/images</td> 
  </tr>
  <tr>
    <td><code>GENESIS_ADMIN_IMAGES_URL</code></td>
    <td>https://example.com/wp-content/themes/genesis/lib/admin/images</td> 
  </tr>
  <tr>
    <td><code>GENESIS_CSS_URL</code></td>
    <td>https://example.com/wp-content/themes/genesis/lib/css</td> 
  </tr>
  <tr>
    <td><code>GENESIS_VIEWS_DIR</code></td>
    <td>/path/to/site/wp-content/themes/genesis/lib/views</td> 
  </tr>
  <tr>
    <td><code>GENESIS_CONFIG_DIR</code></td>
    <td>/path/to/site/wp-content/themes/genesis/config</td> 
  </tr>
  <tr>
    <td><code>GENESIS_SETTINGS_FIELD</code></td>
    <td>genesis-settings</td> 
  </tr>
  <tr>
    <td><code>GENESIS_SEO_SETTINGS_FIELD</code></td>
    <td>genesis-seo-settings</td> 
  </tr>
  <tr>
    <td><code>GENESIS_CPT_ARCHIVE_SETTINGS_FIELD_PREFIX</code></td>
    <td>genesis-cpt-archive-settings-</td> 
  </tr> 
</table>


### Testing for Genesis features

It is generally better to test for a specific function or class rather than using the Genesis version constants. We recommend this:

```php
if ( function_exists( 'genesis_get_config' ) ) {
	// `genesis_get_config()` exists and is safe to use.
}
```

Over comparisons like this:

```php
if ( version_compare( PARENT_THEME_VERSION, '2.8.0', '>=' ) ) {
    // Genesis version is 2.8.0 or higher.
}
```

Checking for the function by name ensures your code will not throw a fatal error if that function is deprecated and removed in a future version of Genesis.

