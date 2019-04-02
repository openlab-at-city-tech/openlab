---
title: How Genesis Works
menuTitle: How Genesis Works
layout: layouts/base.njk
permalink: basics/how-genesis-works/index.html
tags: docs
---

After you understand <a href="{{ '/basics/' | url }}">the basics</a>, it's helpful to know four things when building or modifying Genesis child themes:

1. <a href="#1.-genesis-outputs-the-html-structure-for-you">Genesis outputs the HTML structure for you</a>.
2. You <a href="#2.-control-genesis-html-with-hooks-and-filters">control that HTML with hooks and filters.</a>
3. You can <a href="#3.-inspect-the-position-of-hooks">visually inspect the position of hooks</a> during development using a debugging plugin to help remove or add content.
4. <a href="#4.-child-themes-should-provide-their-own-css">Child themes should provide all CSS</a> and not inherit CSS from Genesis.

## 1. Genesis outputs the HTML structure for you

When you create a WordPress theme without using Genesis, the [template files](https://developer.wordpress.org/themes/basics/template-files/#template-files) include HTML to define the structure of your website. To make changes to the HTML structure or attributes, you edit that HTML directly.

**When you build child themes with Genesis, Genesis provides the HTML for you.** Your child theme template files contain little HTML. You alter the HTML that Genesis outputs with hooks and filters. Although this is a different approach, it brings huge benefits:

### Why does Genesis output HTML?

1. **You don't have to write your HTML from scratch**. Websites have similar structures, so the HTML that Genesis uses is designed to help you achieve almost any layout without major modification. Genesis brings the HTML, you bring your styling, and you still have the power to adjust or add to HTML when you need to.
2. **You benefit from years of optimization with SEO and accessibility in mind**. Genesis HTML has been refined since 2010 with the help of many contributors, as well as SEO and accessibility experts. Using Genesis saves you from making structural mistakes that could cause your site to be less accessible or harder to find in search results.
3. **You can open any Genesis child theme and see a familiar HTML structure**. This makes styling faster, especially when making changes to a Genesis child theme you have never worked with before. It also makes maintaining a range of themes more consistent, and helps to establish standards and conventions for teams of developers.
4. **You can alter HTML in one place instead of many**. Hooks and filters enable you to control the output of similar HTML wherever it appears, instead of having to update the same HTML in multiple template files.
5. **Plugins gain as much control as themes**. Plugins targeting Genesis child themes can use the same Genesis hooks and filters that Genesis child themes benefit from. This lets plugins alter HTML attributes, remove content, and inject content at specific locations in any Genesis child theme. As Genesis provides a large range of hooks throughout the HTML structure, this gives plugin developers much more flexibility to inject and alter content than they have with non-Genesis themes.

## 2. Control Genesis HTML with hooks and filters

Let's create a custom page template to demonstrate how to add, remove, and alter HTML in a Genesis child theme. If you would like to follow along, you'll need to [get Genesis](https://www.studiopress.com/features/#genesis-feature-9) and any Genesis child theme, such as [Genesis Sample](https://github.com/studiopress/genesis-sample/releases).

With those themes installed, activate the child theme and create a new page with a slug of 'test'.

### Create a page template with Genesis

Add a page template that WordPress will use for your new 'test' page using the regular [WordPress page template hierarchy](https://developer.wordpress.org/themes/template-files-section/page-template-files/#page-templates-within-the-template-hierarchy), like this:

1. Create a file named `page-test.php` in the root of your child theme.
2. Leave its contents blank for now.

Visit the URL of the 'test' page you created. You should see a blank page.

If you were not using Genesis, you'd now have to construct your HTML in that file, perhaps by copying it from other files or by outputting content and modifications around a skeleton of header and footer includes.

With Genesis, you only need to add this code to your `page-test.php` file:

```php
<?php
genesis();
```

Refresh the 'test' page URL again. You'll now see the full site and page content.

#### What just happened?

When you call `genesis()`, the Genesis Framework determines your site HTML from:

- The default HTML structure in the Genesis parent theme.
- Hooks and filters you've added in your child theme and plugins that change the HTML output.
- User preferences in Genesis settings that alter how the site is displayed.

Let's now see how to influence the HTML for our custom page template. 

### Add HTML content via a hook

To add some HTML displaying a notice immediately below the page title, modify your `page-test.php` template to 'hook' a function to the `genesis_before_entry_content` action:

```php
<?php

add_action( 'genesis_before_entry_content', 'theme_prefix_show_notice' );
/**
 * Display a custom notice.
 */
function theme_prefix_show_notice() {
	echo '<p class="notice">This page has a custom template.</p>';
}

genesis();
```

You'll see the HTML you wanted to output above your regular page content.

Genesis provides a wide range of other actions you can hook content to. Discover them from the <a href="{{ '/basics/genesis-hooks/' | url }}">Genesis hooks documentation</a> or by using a plugin to <a href="#3.-inspect-the-position-of-hooks">visualize hook names and positions</a>.

<p class="notice-small">
<strong>You can use the tips we're applying to this custom page template to add, remove, and adjust content throughout your site.</strong> You can also hook and unhook actions in your <code>functions.php</code> file, where you don't need to include the <code>genesis()</code> call. You can use <a href="https://codex.wordpress.org/Conditional_Tags">WordPress conditional tags</a> to determine which pages to affect.
</p>

### Remove HTML content by unhooking it

You can also remove content that Genesis added. For example, you can unhook the functions that Genesis used to add the title content with this in your template file:

```php
<?php
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title', 10 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 12 );

genesis();
```

To discover which actions and functions to unhook to remove other content, we recommend browsing Genesis source code or using a <a href="#3.-inspect-the-position-of-hooks">plugin to find hook names and positions</a>.

### Alter page settings and HTML attributes using filters

You can use [WordPress filters](https://developer.wordpress.org/plugins/hooks/filters/) to alter page settings or modify existing code such as HTML attributes.

For example, to force a full-width page layout, you can add this filter above the `genesis()` call:

```php
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );
```

Or to add an ID attribute to the `site-container` div element, you can do this:

```php
add_filter( 'genesis_attr_site-container', 'theme_prefix_container_attributes' );
/**
 * Modifiy the site-container div attributes.
 *
 * @param array $attr The original attributes.
 * @return array The modified attributes.
 */
function theme_prefix_container_attributes( $attr ) {
	$attr['id'] = 'site-container';

	return $attr;
}
```

Find <a href="{{ '/basics/genesis-filters/' | url }}">other Genesis filters here</a> and in the [StudioPress snippets](https://my.studiopress.com/documentation/snippets/).

<p class="notice-small">
<strong>You can still build templates from scratch without using the HTML Genesis provides if your business needs demand it.</strong> Omit the <code>genesis()</code> function from your template file and provide your own HTML instead. Note that Genesis filters and actions that usually affect HTML structure or attributes will no longer function for that template. Bypassing Genesis HTML output is best used sparingly for special cases, such as custom landing page templates that may require no Genesis features.
</p>

## 3. Inspect the position of hooks

How do you know what hooks existing content is attached to, and what hooks to use to add new content?

You could read the Genesis source code, but it's often faster to use a plugin to help visualize hooks available on the page you want to modify. There are several third-party plugins to help with this:

- [Simply Show Hooks](https://wordpress.org/plugins/simply-show-hooks/)
- [Genesis Visual Hook Guide](https://wordpress.org/plugins/genesis-visual-hook-guide/)
- [Hooks Visualizer](https://wordpress.org/plugins/hooks-visualizer/)

## 4. Child themes should provide their own CSS

You should use your own styles in your child theme's `style.css` file, and not inherit the parent Genesis styles.

The Genesis parent theme `style.css` file is not intended to be inherited by the child theme, and may be removed or altered in future versions of Genesis.

Genesis automatically enqueues the `style.css` file from your Genesis child theme folder.

## Where to go from here

- Learn about <a href="{{ '/developer-features/' | url }}">Genesis Developer features</a> like <a href="{{ '/developer-features/theme-support/' | url }}">theme supports</a> and the <a href="{{ '/developer-features/onboarding/' | url }}">Onboarding API</a>.
- [Get Genesis](https://www.studiopress.com/features/#genesis-feature-9), then download and modify an existing Genesis child theme, such as [Genesis Sample](https://github.com/studiopress/genesis-sample/releases).
- Join the <a href="{{ '/contribute/community/' | url }}">Genesis community</a> to ask questions and get help.
- <a href="{{ '/contribute/' | url }}">Contribute to Genesis</a> and help to shape its future.
