# Developer Documentation

## Hooks

The plugin exposes a number of filters for hooking. Code using these filters should ideally be put into a mu-plugin or site-specific plugin (which is beyond the scope of this readme to explain).

Bear in mind that most, if not all, of the features controlled by these filters are configurable via the plugin's settings page. These filters are likely only of interest to advanced users able to code.

### `c2c_text_hover_filters` _(filter)_

The `c2c_text_hover_filters` hook allows you to customize what hooks get text hover applied to them.

#### Arguments:

* **$hooks** _(array)_: Array of hooks that will be text hovered.

#### Example:

```php
/**
 * Enable text hover for post/page titles.
 *
 * @param array $filters Filters handled by the Text Hover plugin.
 * @return array
 */
function more_text_hovers( $filters ) {
	$filters[] = 'the_title'; // Here you could put in the name of any filter you want
	return $filters;
}
add_filter( 'c2c_text_hover_filters', 'more_text_hovers' );
```

### `c2c_text_hover_third_party_filters` _(filter)_

The `c2c_text_hover_third_party_filters` hook allows you to customize what third-party hooks get text hover applied to them. Note: the results of this filter are then passed through the `c2c_text_hover_filters` filter, so third-party filters can be modified using either hook.

#### Arguments:

* **$filters** _(array)_: The third-party filters whose text should have text hover applied.

  Default `[ 'acf/format_value/type=text', 'acf/format_value/type=textarea', 'acf/format_value/type=url', 'acf_the_content', 'elementor/frontend/the_content', 'elementor/widget/render_content' ]`.

#### Example:

```php
/**
 * Stop text hovers for ACF text fields and add text hovers for a custom filter.
 *
 * @param array $filters
 * @return array
 */
function my_c2c_text_hover_third_party_filters( $filters ) {
	// Remove a filter already in the list.
	unset( $filters[ 'acf/format_value/type=text' ] );
	// Add a filter to the list.
	$filters[] = 'my_plugin_filter';
	return $filters;
}
add_filter( 'c2c_text_hover_third_party_filters', 'my_c2c_text_hover_third_party_filters' );
```

### `c2c_text_hover_filter_priority` _(filter)_

The `c2c_text_hover_filter_priority` hook allows you to override the default priority for the `c2c_text_hover` filter.

#### Arguments:

* **$priority** _(int)_: The priority for the `c2c_text_hover` filter. The default value is 2.
* **$filter** _(string)_: The filter name.

#### Example:

```php
/**
 * Change the default priority of the 'c2c_text_hover' filter to run after most other plugins.
 *
 * @param int $priority The priority for the 'c2c_text_hover' filter.
 * @return int
 */
function my_change_priority_for_c2c_text_hover( $priority, $filter ) {
	return 1000;
}
add_filter( 'c2c_text_hover_filter_priority', 'my_change_priority_for_c2c_text_hover', 10, 2 );
```

### `c2c_text_hover` _(filter)_

The `c2c_text_hover` hook allows you to customize or override the setting defining all of the text hover terms and their hover texts.

#### Arguments:

* **$text_hover_array** _(array)_: Array of text hover terms and their hover texts. This will be the value set via the plugin's settings page.

#### Example:

```php
/**
 * Add dynamic text hover.
 *
 * @param array $text_hover_array Array of all text hover terms and their hover texts.
 * @return array
 */
function my_text_hovers( $text_hover_array ) {
	// Add new term and hover text
	$text_hover_array['Matt'] => 'Matt Mullenweg';
	// Unset a term that we never want hover texted
	if ( isset( $text_hover_array['Drupal'] ) )
		unset( $text_hover_array['Drupal'] );
	// Important!
	return $text_hover_array;
}
add_filter( 'c2c_text_hover', 'my_text_hovers' );
```

### `c2c_text_hover_comments` _(filter)_

The `c2c_text_hover_comments` hook allows you to customize or override the setting indicating if text hover should be enabled in comments.

#### Arguments:

* **$state** _(bool)_: Either true or false indicating if text hover is enabled for comments. The default value will be the value set via the plugin's settings page.

#### Example:

```php
// Prevent text hover from ever being enabled in comments.
add_filter( 'c2c_text_hover_comments', '__return_false' );
```

### `c2c_text_hover_case_sensitive` _(filter)_

The `c2c_text_hover_case_sensitive` hook allows you to customize or override the setting indicating if text hover should be case sensitive.

#### Arguments:

* **$state** _(bool)_: Either true or false indicating if text hover is case sensitive. This will be the value set via the plugin's settings page.

#### Example:

```php
// Prevent text hover from ever being case sensitive.
add_filter( 'c2c_text_hover_case_sensitive', '__return_false' );
```

### `c2c_text_hover_once` _(filter)_

The `c2c_text_hover_once` hook allows you to customize or override the setting indicating if text hovering should be limited to once per term per piece of text being processed regardless of how many times the term appears.

#### Arguments:

* **$state** _(bool)_: Either true or false indicating if text hovering is to only occur once per term. The default value will be the value set via the plugin's settings page.

#### Example:

```php
// Only show hovertext for a term/shortcut once per post.
add_filter( 'c2c_text_hover_once', '__return_true' );
```

### `c2c_text_hover_use_pretty_tooltips` _(filter)_

The `c2c_text_hover_use_pretty_tooltips` hook allows you to customize or override the setting indicating if text hovering should use prettier tooltips to display the hover text. If false, the browser's default tooltips will be used.

#### Arguments:

* **$state** _(bool)_: Either true or false indicating if prettier tooltips should be used. The default value will be the value set via the plugin's settings page.

#### Example:

```php
// Prevent pretty tooltips from being used.
add_filter( 'c2c_text_hover_use_pretty_tooltips', '__return_false' );
```
