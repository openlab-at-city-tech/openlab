# Carrington CMS Theme Framework for WordPress
http://carringtontheme.com

by Crowd Favorite  
http://crowdfavorite.com

Released under the GPL license  
http://www.opensource.org/licenses/gpl-license.php

---

## Online Developer Resources

Please see the latest online developer resources for Carrington here:

http://carringtontheme.com/developers/


## What is Carrington?

1. A collection of elegant, high-end WordPress themes for end-users.
2. A designer and developer friendly CMS theme framework for WordPress.
3. A set of best practices for theme organization.


## Basic Framework Concept

Carrington is a CMS theme framework that virtually eliminates the need for custom conditional code in themes. Instead, template naming conventions along with the Carrington engine replace the need for this conditional code.

Theme functionality is broken up into thoughtfully crafted abstractions to enable customizations at different levels (the loop, the post/page content, comments, etc.) and the Carrington engine chooses which template to be used for each segment of the theme.

The abstractions and supported template types are designed to easily handle most of the customization scenarios we commonly see without the need to write custom code to use them.


## Context and Templates

WordPress provides a number of functions to help you determine what type of view a theme is showing. These include:

- `is_home()`
- `is_single()`
- `is_page()`
- `is_archive()`
- `in_category()`
- etc.

Carrington abstracts these to deduce a "context" that is used when selecting a template. There are three context types used by the Carrington framework:

1. Comment (dirs: comment)
2. Post (dirs: content, excerpt)
3. General (dirs: attachment, comments, footer, header, loop, posts, sidebar, single)

Each directory implements one of these contexts for selecting the appropriate template to use. Templates are used in page views based on how they match the given context(s) for the overall page and the specific pieces of content being displayed.

Read about the options available in each directory in the README file for that directory.

Note: "default.php" is a supported default file name for all directories, however we have found in real world usage that {dirname}-default.php is a preferable naming system. When you have a half-dozen "default.php" files open in your favorite text editor, telling them apart in the file list can be more difficult than it should be.


## Theme Organization

WordPress themes generally have a file structure similar to this:

- 404.php
- archive.php
- archives.php
- [...]
- sidebar.php
- single.php
- style.css

While this organization works well in many instances, it doesn't well support the concept of atomic elements that are combined to create a theme. For example, a representation of just a post's content, or just a comment, is not represented here.

Carrington respects the supported WordPress file naming conventions (for example `get_header()` will still work), but eschews them in favor of an organizational structure that better suits the abstraction and customization commonly needed for a WordPress theme.

Template files are layered into each other using the following basic approach:

1. top level templates that include
2. common elements like a header, footer and sidebar along with a
3. loop that includes 
4. atomic post/page content or excerpt templates and, where appropriate, a
5. comments area template that includes 
6. atomic template for comments and a 
7. template for the comment form


## Actions and Filters

The Carrington core is a theme framework, not a parent/child theme system. It includes a core set of functions that enable the override template hierarchy. These functions include actions and filters where appropriate so that their functionality can be customized and overridden as needed. These actions and filters use the same hook and filter system used in the WordPress core.


### Filters

- `cfct_context` - allows you to apply filters to the return value of the `cfct_context()` function; the function that checks to see what posts file, loop file, etc. to show.
- `cfct_filename` - filter the output of the `cfct_filename()` function.
- `cfct_general_match_order` - set the order in which general templates are chosen (make it check for a cat-general template ahead of a cat-news template, etc.).
- `cfct_choose_general_template` - filter the output of the `cfct_choose_general_template()` function (the selected general template).
- `cfct_single_match_order` - set the order in which single and content templates are chosen (make it check for author templates ahead of meta template, etc.).
- `cfct_choose_single_template` - filter the output of the `cfct_choose_single_template()` function (the selected single template).
- `cfct_choose_content_template` - filter the output of the `cfct_choose_content_template()` function (the selected content template).
- `cfct_comment_match_order` - set the order in which content templates are chosen (make it check for role templates ahead of user templates, etc.).
- `cfct_choose_comment_template` - filter the output of the `cfct_choose_comment_template()` function (the selected comment template).
- `cfct_meta_templates` - filter the return value of the `cfct_meta_templates()` function (change the list of files returned).
- `cfct_cat_templates` - filter the return value of the `cfct_cat_templates()` function (change the list of files returned).
- `cfct_tag_templates` - filter the return value of the `cfct_tag_templates()` function (change the list of files returned).
- `cfct_author_templates` - filter the return value of the `cfct_author_templates()` function (change the list of files returned).
- `cfct_role_templates` - filter the return value of the `cfct_role_templates()` function (change the list of files returned).
- `cfct_parent_templates` - filter the return value of the `cfct_role_templates()` function (change the list of files returned).
- `cfct_single_templates` - filter the return value of the `cfct_parent_templates()` function (change the list of files returned).
- `cfct_comment_templates` - filter the return value of the `cfct_single_templates()` function (change the list of files returned).
`cfct_post_gallery_columns` - set the number of columns to show in the gallery.
`gallery_style` - retained from the WP function code copied in and altered for gallery display.
`cfct_option_defaults` - allows you to set defaults (alter/add/etc.) for Carrington options.
`cfct_files_{path}` - allows you to define the available files for a path.


### Actions

- `cfct_settings_form` - allows you to add your own fields to the Carrington Settings form (left for compatibility` - recommend using _top and _bottom as needed instead of this).
- `cfct_settings_form_top` - allows you to add your own fields at the top of the Carrington Settings form.
- `cfct_settings_form_bottom` - allows you to add your own fields at the bottom of the Carrington Settings form.
- `cfct_settings_form_after` - allows you to add your content after the Carrington Settings form. Useful if you want to add a second form to the page, or some other content.
- `cfct_update_settings` - allows you to take action when the Carrington settings are being saved (perhaps to also save fields you've added in the `cfct_settings_form` action).


## Plugins

Any .php files in the *plugins/* directory will be automatically loaded by Carrington. This is a great way to bundle in custom functions or to hook into Carrington's actions or filters and be able to distribute everything as a single theme package.

---

## Tips

There is very minor extra processing associated with the file system and context checks that Carrington requires. This overhead is virtually unnoticeable, however the use of a caching plugin is recommended as a general best practice.
