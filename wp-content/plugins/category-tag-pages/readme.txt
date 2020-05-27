=== Category Tag Pages ===
Contributors: marziocarro
Tags: tag, tags, category, categories, pages, tag pages, category pages, page-tag, page-tags, page-category, page-categories, taxonomy, taxonomies
Requires at least: 3.0
Tested up to: 5.3
Stable tag:trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Adds categories and tags functionality for your pages.

== Description ==

Adds categories and tags functionality for your pages.

This plugin addsthe 'post_tag' and the 'category' taxonomies, which are the names of the existing taxonomies used for tags and categories the Post type 'page'.

This enables the categories metabox and the tags metabox in the New or Edit Page interface.

The plugin has no settings, does not alter the database and only uses hooks to
achieve it's goal. It works perfectly fine with Multisite installations. 

This plugin WILL NOT add any display of categories or tags to your template files.

**Plugin's website:** [http://technotes.marziocarro.com/category/wordpress-plugins/](http://technotes.marziocarro.com/category/wordpress-plugins/)

**Author's website:** [http://technotes.marziocarro.com/](http://technotes.marziocarro.com/)

== Installation ==

1. Copy the `category-tag-pages` directory into your WordPress plugins directory (usually wp-content/plugins).

2. In the WordPress Admin Menu go to the Plugins tab and activate the 'Category Tag Pages' plugin.

== Screenshots ==

1. A screenshot of the WordPress backend Pages section with the Categories metabox and the Tags metabox marked red.

== Changelog ==

= 1.0 =

initial release

== Upgrade Notice ==

No upgrade notices so far...

== Frequently Asked Questions ==

= Why do the Post Tags sections for posts and pages in the Admin Menu show the same tag count? =

The reason for that is that WordPress combines the number of occurrences of tags used in posts and pages in the taxonomy `post_tag`. Though, if you click on the number of a certain tag, WordPress will only show the related posts or pages of the selected tag.

= Why do the Post Categories sections for posts and pages in the Admin Menu show the same category count? =

For the same reason above. The only difference is that it uses the taxonomy `category`.

