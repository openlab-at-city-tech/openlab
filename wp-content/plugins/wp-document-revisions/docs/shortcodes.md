# WP-Documents-Revisions Shortcodes and Widget

These shortcodes and widget are available both in their historic form and as blocks.

Existing shortcodes can be converted to and from their block forms.

They are held in a grouping called `WP Document Revisions`.

## Documents Shortcode

In a post or page, simply type `[documents]` to display a list of documents. 

### WP_Query parameters

The shortcode accepts *most* [Standard WP_Query parameters](https://developer.wordpress.org/reference/classes/wp_query/) which should allow you to fine tune the output. Parameters are passed in the form of, for example, `[documents numberposts="5"]`. 

Specifically, the shortcode accepts: `author__in`, `author__not_in`, `author_name`, `author`, `cat`, `category__and`, `category__in`, `category__not_in`, `category_name`, `date_query`, `day`, `has_password`, `hour`, `m`, `meta_compare`, `meta_key`, `meta_query`, `meta_value_num`, `meta_value`, `minute`, `monthnum`, `name`, `numberposts`, `order`, `orderby`, `p`, `page_id`, `pagename`, `post__in`, `post__not_in`, `post_name__in`, `post_parent__in`, `post_parent__not_in`, `post_parent`, `post_password`, `post_status`, `s`, `second`, `tag__and`, `tag__in`, `tag__not_in`, `tag_id`, `tag_slug__and`, `tag_slug__in`, `tag`, `tax_query`, `title`, `w` and `year`.

If you're using a custom taxonomy, you can add the taxonomy name as a parameter in your shortcode. For example, if your custom taxonomy is called "document_categories", you can write insert a shortcode like this:

`[documents document_categories="category-name" numberposts="6"]`

(Where "category-name" is the taxonomy term's slug)

Strictly it uses accepts the "query_var" parameter of the taxonomies used for documents. That is, if you have defined a taxonomy for your documents with slug "document_categories". If you have not defined the query_var parameter then you use the slug. However if you have set query_var to "doc_cat", say, then you can insert a shortcode as

`[documents doc_cat="category-name" numberposts="5"]`

Important parameters WP_Query will be the ordering and number of posts to display.

`numberposts` (with a number parameter) will give the maximum number of posts to display.

`order` (with value 'ASC' or 'DESC') gives the ordering,

`orderby` (with a string value) gives the field to order the documents. Common values are "title", "date", "name", "modified" and "ID".

### Display parameters

It is also possible to add formatting parameters: 

`show_edit` (with a true/false parameter) that can add a link next to each document shown in the list that the user is able to edit by them. This permits the user to edit the document directly from the list. A value set here will override the default behaviour.

As delivered, administrators will have the show_edit implicitly active. A filter `document_shortcode_show_edit` can be used to set this for additional user roles.

`new_tab` (with a true/false parameter) that will open the document in a new browser tab rather than in the current one.

Both of these boolean variables can be entered without a value (with default value true). 

### Block Usage

When using the block version of the shortcode called `Document List`, some compromises have been necessary.

Since queries are often selecting a single taxonomy value, the block provides the possibility to select single values from up to three taxonomies. Since there can be more than three taxomomies attached to documents, a filter `document_block_taxonomies` allows the list of taxonomies to be edited to select the taxonomies to be displayed.

The parameters `numberposts`, `order`, `orderby`, `show_edit` and `new_tab` are directly supported. However, since there are many other parameters are possible, as well as differet structures, additional parameters may be entered as a text field as held in the shortcode.

## Document Revisions Shortcode

In a post or page, simply type `[document_revisions id="100"]` where ID is the ID of the document for which you would like to list revisions. 

You can find the ID in the URL of the edit document page. 

To limit the number of revisions displayed, passed the "number" argument, e.g., to display the 5 most recent revisions `[document_revisions id="100" number="5"]`.

### Display parameters

It is also possible to add formatting parameters:

`numberposts` (with a number parameter) will give the maximum number of revisions to display.

`summary` (with a true/false parameter) that will add the excerpt for the revision to the output.

`new_tab` (with a true/false parameter) that will open the revision in a new browser tab rather than in the current one.

Both of these boolean variables can be entered without a value (with default value true ). 

### Block Usage

When using the block version of the shortcode called `Document Revisions`, a change have been necessary.

`number` is a reserved word within javascript so `numberposts` is also supported even for the shortcode format. `numberposts` is used by the block.

Since the block is dynamically displayed as parameters are entered, if the post number entered is not a document, then an appropriate message will be entered.

## Latest Documents Widget

Go to your theme's widgets page (if your theme supports widgets), and drag the widget to a sidebar of you choice. Once in a sidebar, you will be presented with options to customize the widget's functionality.

### Block Usage

The block version of the widget called `Document Widget`can be used on pages or posts. It cannot be converted to or from a shortcode block.
 