<?php
/**
 * Controls output elements on archive pages.
 *
 * @package Genesis
 *
 */

add_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
/**
 * Add Title/Description to Category/Tag/Taxonomy archive pages.
 *
 * @since 1.3
 */
function genesis_do_taxonomy_title_description() {
	global $wp_query;

	if ( ! is_category() && ! is_tag() && ! is_tax() )
		return;

	if ( get_query_var( 'paged' ) >= 2 )
		return;

	$term = is_tax() ? get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ) : $wp_query->get_queried_object();

	if ( ! $term || ! isset( $term->meta ) )
		return;

	global $_genesis_formatting_allowedtags;

	$title = $term->meta['display_title'] ? sprintf( '<h1>%s</h1>', esc_html( $term->name ) ) : '';
	$description = $term->meta['display_description'] ? wpautop( wp_kses( $term->description, $_genesis_formatting_allowedtags ) ) : '';

	if ( $title || $description ) {
		printf( '<div class="taxonomy-description">%s</div>', $title . $description );
	}

}

add_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
/**
 * Add custom headline and intro text to author archive pages
 *
 * @since 1.4
 */
function genesis_do_author_title_description() {

	if ( ! is_author() )
		return;

	if ( get_query_var( 'paged' ) >= 2 )
		return;

	$headline = get_the_author_meta( 'headline', (int) get_query_var( 'author' ) );
	$intro_text = get_the_author_meta( 'intro_text', (int) get_query_var( 'author' ) );

	global $_genesis_formatting_allowedtags;

	$headline = $headline ? sprintf( '<h1>%s</h1>', esc_html( $headline ) ) : '';
	$intro_text = $intro_text ? wpautop( wp_kses( $intro_text, $_genesis_formatting_allowedtags ) ) : '';

	if ( $headline || $intro_text ) {
		printf( '<div class="author-description">%s</div>', $headline . $intro_text );
	}

}

add_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
/**
 * Add author box to the top of author archive
 *
 * @since 1.4
 */
function genesis_do_author_box_archive() {

	if ( ! is_author() || get_query_var( 'paged' ) >= 2 )
		return;

	if ( get_the_author_meta( 'genesis_author_box_archive', get_query_var( 'author' ) ) ) {
		genesis_author_box( 'archive' );
	}

}