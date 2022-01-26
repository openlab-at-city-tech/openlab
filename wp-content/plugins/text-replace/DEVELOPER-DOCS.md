# Developer Documentation

## Hooks

The plugin exposes a number of filters for hooking. Code using these filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain). Less ideally, you could put them in your active theme's functions.php file.

Bear in mind that most, if not all, of the features controlled by these filters are configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.

### `c2c_text_replace_filters` _(filter)_

The 'c2c_text_replace_filters' hook allows you to customize what hooks get text replacement applied to them.

#### Arguments:

* **$hooks** _(array)_: Array of hooks that will be text replaced.

#### Example:

```php
/**
 * Enable text replacement for post/page titles.
 *
 * @param array $filters Filters handled by the Text Replace plugin.
 * @return array
 */
function more_text_replacements( $filters ) {
	$filters[] = 'the_title'; // Here you could put in the name of any filter you want
	return $filters;
}
add_filter( 'c2c_text_replace_filters', 'more_text_replacements' );
```

### `c2c_text_replace_third_party_filters` _(filter)_

The 'c2c_text_replace_third_party_filters' hook allows you to customize what third-party hooks get text replacement applied to them. Note: the results of this filter are then passed through the `c2c_text_replace_filters` filter, so third-party filters can be modified using either hook.

#### Arguments:

* **$filters** _(array)_: The third-party filters whose text should have text replacement applied.

 Default `[ 'acf/format_value/type=text', 'acf/format_value/type=textarea', 'acf/format_value/type=url', 'acf_the_content', 'elementor/frontend/the_content', 'elementor/widget/render_content' ]`.

#### Example:

```php
/**
 * Stop text replacements for ACF text fields and add text replacements for a custom filter.
 *
 * @param array $filters
 * @return array
 */
function my_c2c_text_replace_third_party_filters( $filters ) {
	// Remove a filter already in the list.
	unset( $filters[ 'acf/format_value/type=text' ] );
	// Add a filter to the list.
	$filters[] = 'my_plugin_filter';
	return $filters;
}
add_filter( 'c2c_text_replace_third_party_filters', 'my_c2c_text_replace_third_party_filters' );
```

### `c2c_text_replace_filter_priority` _(filter)_

The 'c2c_text_replace_filter_priority' hook allows you to override the default priority for the 'c2c_text_replace' filter.

#### Arguments:

* **$priority** _(int)_: The priority for the 'c2c_text_replace' filter. The default value is 2.
* **$filter** _(string)_: The filter name.

#### Example:

```php
/**
 * Change the default priority of the 'c2c_text_replace' filter to run after most other plugins.
 *
 * @param int $priority The priority for the 'c2c_text_replace' filter.
 * @return int
 */
function my_change_priority_for_c2c_text_replace( $priority, $filter ) {
	return 1000;
}
add_filter( 'c2c_text_replace_filter_priority', 'my_change_priority_for_c2c_text_replace', 10, 2 );
```

### `c2c_text_replace` _(filter)_

The 'c2c_text_replace' hook allows you to customize or override the setting defining all of the text replacement shortcuts and their replacements.

#### Arguments:

* **$text_replacement_array** _(array)_: Array of text replacement shortcuts and their replacements. This will be the value set via the plugin's settings page.

#### Example:

```php
/**
 * Add dynamic shortcuts.
 *
 * @param array $replacements Array of replacement terms and their replacement text.
 * @return array
 */
function my_text_replacements( $replacements ) {
	// Add replacement
	$replacements[':matt:'] => 'Matt Mullenweg';
	// Unset a replacement that we never want defined
	if ( isset( $replacements[':wp:'] ) )
		unset( $replacements[':wp:'] );
	// Important!
	return $replacements;
}
add_filter( 'c2c_text_replace', 'my_text_replacements' );
```

### `c2c_text_replace_comments` _(filter)_

The 'c2c_text_replace_comments' hook allows you to customize or override the setting indicating if text replacement should be enabled in comments.

#### Arguments:

* **$state** _(bool)_: Either true or false indicating if text replacement is enabled for comments. The default value will be the value set via the plugin's settings page.

#### Example:

```php
// Prevent text replacements from ever being enabled in comments.
add_filter( 'c2c_text_replace_comments', '__return_false' );
```

## `c2c_text_replace_case_sensitive` _(filter)_

The 'c2c_text_replace_case_sensitive' hook allows you to customize or override the setting indicating if text replacement should be case sensitive.

### Arguments:

* **$state** _(bool)_: Either true or false indicating if text replacement is case sensitive. This will be the value set via the plugin's settings page.

### Example:

```php
// Prevent text replacement from ever being case sensitive.
add_filter( 'c2c_text_replace_case_sensitive', '__return_false' );
```

### `c2c_text_replace_once` _(filter)_

The 'c2c_text_replace_once' hook allows you to customize or override the setting indicating if text replacement should be limited to once per term per piece of text being processed regardless of how many times the term appears.

#### Arguments:

* **$state** _(bool)_: Either true or false indicating if text replacement is to only occur once per term. The default value will be the value set via the plugin's settings page.

#### Example:

```php
// Only replace a term/shortcut once per post.
add_filter( 'c2c_text_replace_once', '__return_true' );
```
