=== Accordion Shortcodes ===
Contributors: philbuchanan
Author URI: http://philbuchanan.com/
Donate Link: http://philbuchanan.com/
Tags: accordion, accordions, shortcodes, responsive accordions, accordions plugin, jquery accordions, accordions short-code, accordions plugin wordpress, accordions plugin jquery
Requires at least: 3.3
Tested up to: 4.8
Stable tag: 2.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shortcodes for creating responsive accordion drop-downs.

== Description ==

Accordion Shortcodes is a simple plugin that adds a few shortcodes for adding accordion drop-downs to your pages.

The accordions should blend seamlessly with your theme. However, you may want to edit your theme's main stylesheet in order to add some custom styling (see below for sample CSS).

= Features =

* Adds two shortcodes for adding accordions to your site
* Supports multiple accordions with individual settings on a single page
* Two buttons in the TinyMCE editor make it easy to add and configure the accordion shortcodes
* Responsive
* No default CSS added
* Only adds JavaScript on pages that use the shortcodes
* Support for item IDs and direct links
* Accessible (for users requiring tabbed keyboard navigation control)

= Optional Features =

* Open the first accordion item by default
* Open all accordion items by default
* Disable auto closing of accordion items
* Manually close items by clicking the title again
* Scroll page to title when it's clicked open
* Set the HTML tag for the title element
* Change the semantic structure of your accordions (advanced)

= The Shortcodes =

The two shortcodes that are added are:

`[accordion]...[/accordion]`

and

`[accordion-item title=""]...[/accordion-item]`

= Basic Usage Example =

    [accordion]
    [accordion-item title="Title of accordion item"]Drop-down content goes here.[/accordion-item]
    [accordion-item title="Second accordion item"]Drop-down content goes here.[/accordion-item]
    [/accordion]

This will output the following HTML:

    <div class="accordion">
        <h3 class="accordion-title">Title of accordion item</h3>
        <div class="accordion-content">
            Drop-down content goes here.
        </div>
        <h3 class="accordion-title">Second accordion item</h3>
        <div class="accordion-content">
            Drop-down content goes here.
        </div>
    </div>

== Installation ==
1. Upload the 'accordion-shortcodes' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Add the shortcodes to your content.

== Frequently Asked Questions ==

= Why isn't the JavaScript file loading on my site? =

This is most likely caused by a poorly coded theme. This plugin makes use of the `wp_footer()` function to load the JavaScript file and it's dependancy (jQuery). Check your theme to ensure that the `wp_footer()` function is being called right before the closing `</body>` tag in your theme's footer.php file.

= How can I change the look of the accordion? =

No CSS is added by default to the accordion. The accordion should look fine with every theme.

That said, you can change the looking of the accordion as long as you are comfortable with editing your theme's stylesheet. If you are familiar with that process, you can add some CSS to make the accordion look the way you want.

= How can I make all accordion content disply when printing the page? =

Add this CSS to your theme's CSS stylesheet:

`@media print {
	.accordion-content {
		display: block !important;
	}
}`

= Can I use other shortcodes inside each accordion item? =

Absolutely! You can use any of the WordPress format settings and headings as well.

You cannot, however nest an accordion within another accordion. This is a limitation of how WordPress processes shortcodes.

= How do I accommodate fixed position headers if I'm using `scroll`? =

The scroll setting accepts numeric values as well. So, instead of setting `[accordion scroll="true"]`, you can define a pixel offset for the final scroll position like this: `[accordion scroll="50"]`. Set the numeric value to the pixel height of your fixed header.

= Is it possible to open/close all accordions with a single button click? =

Yes! Although you will need to know some simple code to get it working.

In your theme, you'll need to have a Javascript file with the following code:

`$('.js-open-everything').click(function() {
	$.each($('.accordion-title'), function(index, value) {
		if (!$(this).hasClass('open')) {
			$(this).trigger('click');
		}
	});
});`

Then, you can add a button to your page:

`<button class="js-open-everything">Open Everything</button>`

= I have a lot of extra space showing around my accordions (and other shortcodes). How can I remove it? =

WordPress automatically adds paragraphs and line breaks to content formatted in the editor, so if your shortcodes aren't formatted just right, you'll see a lot of extra spacing. Putting this function in your theme's functions.php file should fix it:

`/**
 * Fixes empty <p> and <br> tags showing before and after shortcodes in the
 * output content.
 */
function pb_the_content_shortcode_fix($content) {
	$array = array(
		'<p>['    => '[',
		']</p>'   => ']',
		']<br />' => ']',
		']<br>'   => ']'
	);
	return strtr($content, $array);
}
add_filter('the_content', 'pb_the_content_shortcode_fix');`

== Other Notes ==

= Sample CSS =

Here is some sample CSS to get you started if you want to customize the look and feel of the accordion.

    /* Accordion Styles */
    .accordion {
        border-bottom: 1px solid #dbdbdb;
        margin-bottom: 20px;
    }
    .accordion-title {
        border-top: 1px solid #dbdbdb;
        margin: 0;
        padding: 20px 0;
        cursor: pointer;
    }
    .accordion-title:hover {}
    .accordion-title:first-child {border: none;}
    .accordion-title.open {cursor: default;}
    .accordion-content {padding-bottom: 20px;}

= Opening an Accordion Via ID =

You can optionally add a unique ID to each of your accordion items and then use that ID in the URL to open that item by default. For example, say you have the following accordions:

    [accordion]
    [accordion-item id="item-1" title="Title of accordion item"]Drop-down content goes here.[/accordion-item]
    [accordion-item id="item-2" title="Second accordion item"]Drop-down content goes here.[/accordion-item]
    [accordion-item id="item-3" title="A Third accordion"]Drop-down content goes here.[/accordion-item]
    [/accordion]

You could use this URL to open the third item by default:

    http://yourdomain.com/your/path/#item-3

All you need to do is ensure that the part after the `#` in the URL matches the ID set on the accordion item.

= Advanced Accordion Settings =

There are a few advanced settings you can add to the opening accordion shortcode. The full shortcode, with all the default settings looks like this:

    [accordion autoclose="true" openfirst="false" openall="false" clicktoclose="false"]

**autoclose**: Sets whether accordion items close automatically when you open the next item. Set `autoclose="true/false"` on the opening accordion shortcode like this: `[accordion autoclose="false"]`. Default is `true`.

**openfirst**: Sets whether the first accordion item is open by default. This setting will be overridden if **openall** is set to true. Set `openfirst="true/false"` on the opening accordion shortcode like this: `[accordion openfirst="true"]`. Default is `false`.

**openall**: Sets whether all accordion items are open by default. It is recommended that this setting be used with **clicktoclose**. Set `openall="true/false"` on the opening accordion shortcode like this: `[accordion openall="true"]`. Default is `false`.

**clicktoclose**: Sets whether clicking an open title closes it. Set `clicktoclose="true/false"` on the opening accordion shortcode like this: `[accordion clicktoclose="true"]`. Default is `false`.

**scroll**: Sets whether to scroll to the title when it's clicked open. This is useful if you have a lot of content within your accordion items. Set `scroll="true/false"` on the opening accordion shortcode like this: `[accordion scroll="true"]`. Default is `false`. You may also specify an integer for a pixel offset if you'd like the page to scroll further (useful when the site uses a fixed position header navigation). NOTE: Only use pixel offset integers of > 0. If you do not want a scroll offset, but still want scrolling, simply use `scroll="true"`.

**class**: Sets a custom CSS class for the accordion group or individual accordion items. Set `class="your-class-name"` on the opening accordion or accordion-item shortcode like this: `[accordion class="your-class-name"]` or `[accordion-item class="your-class-name"]`. Added a class to the accordion-item will add the class to the title HTML tag.

**tag**: Set the global HTML tag to use for all accordion titles. Set `tag="h2"` on the opening accordion shortcode like this: `[accordion tag="h2"]`. Default is `h3`.

**semantics**: You can change the entire semantic structure of the accordions to use a definition list (dl, dt, dd) by setting `semantics="dl"` on the opening accordion shortcode like this: `[accordion semantics="dl"]`. By default the accordion will use `div` tags for the wrapper and content containers. If you set this option to `dl`, it is recommended that you do not also use the `tag` option. This feature is not selectable from the accordion button dialog box and must be added manually.

= Advanced Accordion Item Settings =

**state**: Sets the initial state of the accordion item to `open` or `closed`. Set `state=open` or `state=closed` on the opening accordion item shortcode like this: `[accordion-item state=open]`. This setting will override all other accordion settings except when linking to an accordion item via ID.

**tag**: You can also set the HTML tag for the titles of each accordion item individually by adding `tag="tagname"` to each `[accordion-item]` shortcode. Make sure to **not** include the angle brackets around the tag name. Example: to use `<h2>` instead of the default `<h3>` tag: `[accordion-item title="Item title" tag="h2"]Item content[/accordion-item]`. Using a tag attribute on an individual accordion item will override the global setting. The list of valid tags is: h1, h2, h3, h4, h5, h6, p, div.

= Filtering Shortcodes =

You can filter the settings and content of the shortcodes by adding some simply code to the functions.php file of your theme.

For example, you could set the `openfirst` option for all accordions across the entire site using:

    add_filter('shortcode_atts_accordion', 'set_accordion_shortcode_defaults', 10, 3);
    function set_accordion_shortcode_defaults($atts) {
        // Override the openfirst setting here
        $atts['openfirst'] = true;
        return $atts;
    }

= Compatibility Mode =

If you have a theme that already includes the shortcodes `[accordion]` or `[accordion-item]` you can enable compatibility mode.

To enable compatibility mode add `define('AS_COMPATIBILITY', true);` to your wp-config.php file. This will add a prefix of 'as-' to the two accordion shortcodes.

With compatibility mode enabled, make sure your shortcodes start with `as-` like this: `[as-accordion]...[/as-accordion]` and `[as-accordion-item]...[/as-accordion-item]`.

= Disable TinyMCE Buttons =

You can optionally disable the TinyMCE extension which will remove the buttons from the editor button bar. To disable the TinyMCE extension add `define('AS_TINYMCE', false);` to your wp-config.php file.

= Issues/Suggestions =

For bug reports or feature requests or if you'd like to contribute to the plugin you can check everything out on [Github](https://github.com/philbuchanan/Accordion-Shortcodes/).

== Screenshots ==

1. The Accordion Group and Accordion Item shortcode buttons in the editor
2. The Accordion Group shortcode insertion dialog box
3. The Accordion Item shortcode insertion dialog box

== Changelog ==
= 2.3.3 =
* Now compatible up to WordPress 4.8
* FIXED: aria-mutliselectable is now on the accordion group instead of each accordion item title

= 2.3.2 =
* Now compatible up to WordPress 4.7
* FIXED: Accordion titles now truly accessible via keyboard control

= 2.3.1 =
* Now compatible up to WordPress 4.6
* FIXED: A bug with a deprecated function in jQuery

= 2.3.0 =
* NEW: Added setting to set initial state (open or closed) of individual accordion items on page load
* NEW: Added wp-config option to disable the TinyMCE extension
* Now compatible up to WordPress 4.5

= 2.2.6 =
* FIXED: Scroll offset was ignored when an accordion was linked to from another page

= 2.2.5 =
* Now compatible up to WordPress 4.4

= 2.2.4 =
* Now compatible up to WordPress 4.3

= 2.2.3 =
FIXED: A bug where the content editor would break in custom post types

= 2.2.1 =
FIXED: A bug where setting both scroll and openfirst would scroll the window without user interaction

= 2.2 =
* NEW: Accessible for users requiring tabbed keyboard navigation control (this took way too long)
* NEW: A classname of 'read' is now added to accordion item titles as they are opened. This allows you to style all read accordion items
* NEW: Compatibility mode adds a prefix to the shortcodes for themes that already include accordion shortcodes with matching names
* FIXED: Animation queue not clearing
* Now compatible up to WordPress 4.2

= 2.1.1 =
* FIXED: An issue where openfirst would not work if title tag was set to div
* FIXED: An issue where title tag setting was not respected when using multiple accordions on one page

= 2.1 =
* NEW: Use multiple accordions on a single page! Each shortcode will now respect its own individual settings.
* Now compatible up to WordPress 4.1

= 2.0.1 =
* NEW: Add a custom CSS classname to your accordion item group or accordion item shortcode
* NEW: Set an integer for scroll property to offset the scrolling by that many pixels
* Now compatible up to WordPress 4.0

= 2.0 =
* NEW: Buttons in the editor to easily add shortcodes with various settings
* NEW: Support for item IDs on accordion items and direct linking to a specific item
* NEW: Change the entire semantic structure of your accordions by using definition lists
* ENHANCED: Class added if JavaScript is disabled (so you can style your accordions differently if necessary)
* ENHANCED: Each accordion now has its own unique ID (accordion-1, accordion-2...) so you can target each one on a page
* FIXED: A few incredibly small bugs/annoyances

== Upgrade Notice ==
= 2.3.2 =
You may notice a focus state around your accordion items when clicking them. This is necessary to support accessibility within the plugin. If you really must remove the focus state (though not recommended) you can do so by adding this CSS to your theme's stylesheet: `.accordion-title {outline: none;}`.

= 2.3.1 =
Fixed a minor bug that could cause warnings in the developer console. Also now compatible up to WordPress 4.6.

= 2.3.0 =
Added setting to set initial state (open or closed) of individual accordion items on page load. Also now compatible up to WordPress 4.5.

= 2.2.6 =
Fixes an issues where the scroll offset was ignored when an accordion was linked to from another page.

= 2.2.5 =
Now compatible up to WordPress 4.4.

= 2.2.4 =
Now compatible up to WordPress 4.3.

= 2.2.3 =
Fixes a bug where the content editor would break in custom post types.

= 2.2.1 =
Fixes a bug introduced in v2.2 when using the scroll and openfirst setting together.

= 2.2 =
Drastically improved accessibility. New 'read' class added to opened accordion items. Compatibility mode added for theme's with the same accordion shortcode names. WordPress 4.2 compatibility.

= 2.1.1 =
Fixes a few minor issues accidentally introduced in the 2.1 update.

= 2.1 =
This update brings the much request support for multiple accordions on a single page! Each shortcode will now respect its own individual settings.

= 2.0.1 =
WordPress 4.0 compatibility.

= 2.0 =
Big changes for version 2.0!
