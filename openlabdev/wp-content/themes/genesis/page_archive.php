<?php
/**
 * WARNING: This file is part of the core Genesis framework. DO NOT edit
 * this file under any circumstances. Please do all modifications
 * in the form of a child theme.
 *
 * Template Name: Archive
 * This file handles archives pages.
 *
 * @package Genesis
 */

/** Remove standard post content output **/
remove_action( 'genesis_post_content', 'genesis_do_post_content' );

add_action( 'genesis_post_content', 'genesis_page_archive_content' );
/**
 * This function outputs sitemap-esque columns displaying all pages,
 * categories, authors, monthly archives, and recent posts.
 *
 * @since 1.6
 */
function genesis_page_archive_content() { ?>

	<div class="archive-page">

		<h4><?php _e( 'Pages:', 'genesis' ); ?></h4>
		<ul>
			<?php wp_list_pages( 'title_li=' ); ?>
		</ul>

		<h4><?php _e( 'Categories:', 'genesis' ); ?></h4>
		<ul>
			<?php wp_list_categories( 'sort_column=name&title_li=' ); ?>
		</ul>

	</div><!-- end .archive-page-->

	<div class="archive-page">

		<h4><?php _e( 'Authors:', 'genesis' ); ?></h4>
		<ul>
			<?php wp_list_authors( 'exclude_admin=0&optioncount=1' ); ?>
		</ul>

		<h4><?php _e( 'Monthly:', 'genesis' ); ?></h4>
		<ul>
			<?php wp_get_archives( 'type=monthly' ); ?>
		</ul>

		<h4><?php _e( 'Recent Posts:', 'genesis' ); ?></h4>
		<ul>
			<?php wp_get_archives( 'type=postbypost&limit=100' ); ?>
		</ul>

	</div><!-- end .archive-page-->

<?php
}

genesis();