<?php

namespace OpenLab\PrintThisPage;

/**
 * Functionality related to the 'Print This Page' feature.
 */

/**
 * Returns the post types where the feature is enabled.
 */
function post_types() {
	return [ 'post', 'page' ];
}

/**
 * Adds the 'Print This Page' toggle metabox.
 */
add_action(
	'add_meta_boxes',
	function() {
		$screens = post_types();
		foreach ( $screens as $screen ) {
			add_meta_box(
				'openlab_print_this_page',
				'Print This Page',
				'\OpenLab\PrintThisPage\metabox',
				$screen,
				'side'
			);
		}
	}
);

/**
 * Markup for the Print This Page metabox.
 */
function metabox( $post ) {
	$show = show_for_post( $post->ID );

	?>
	<label for="print-this-page-toggle">
		<input type="checkbox" id="print-this-page-toggle" value="1" name="print-this-page-toggle" <?php checked( $show ); ?>> <?php printf( "Add a 'Print this Page' link to this %s allowing site users to easily print its contents.", esc_html( $post->post_type ) ); ?>
	</label>

	<p class="description">To change defaults for the entire site, go to Reading Settings." And link to Settings > Reading</p>
	<?php wp_nonce_field( 'print_this_page_toggle', 'print-this-page-toggle-nonce', false ); ?>
	<?php
}

/**
 * Saves metabox values.
 */
add_action(
	'save_post',
	function( $post_id ) {
		// Bail if the nonce field is not provided.
		if ( empty( $_POST['print-this-page-toggle-nonce'] ) ) {
			return;
		}

		check_admin_referer( 'print_this_page_toggle', 'print-this-page-toggle-nonce' );

		$disable = empty( $_POST['print-this-page-toggle'] );

		if ( $disable ) {
			update_post_meta( $post_id, 'print_this_page_disable', '1' );
		} else {
			delete_post_meta( $post_id, 'print_this_page_disable' );
		}
	}
);

/**
 * Returns whether the 'Print this page' interface should show for a given post.
 *
 * We have this wrapper because we default to showing, which means that we actually
 * have a 'disable' postmeta, which is confusing.
 */
function show_for_post( $post_id ) {
	$disable = get_post_meta( $post_id, 'print_this_page_disable', true );
	return '1' !== $disable;
}

/**
 * Adds the link into the page content.
 */
add_filter(
	'the_content',
	function( $content ) {
		if ( ! is_single() && ! is_singular() ) {
			return $content;
		}

		if ( ! is_main_query() ) {
			return $content;
		}

		if ( is_buddypress() ) {
			return $content;
		}

		if ( ! show_for_post( get_queried_object_id() ) ) {
			return $content;
		}

		$queried_object = get_queried_object();
		if ( ! isset( $queried_object->post_type ) || ! in_array( $queried_object->post_type, post_types(), true ) ) {
			return $content;
		}

		wp_enqueue_style(
			'print-this-page-styles',
			WDS_CITYTECH_URL . 'assets/css/print-this-page.css'
		);

		wp_enqueue_script(
			'print-this-page',
			WDS_CITYTECH_URL . '/assets/js/print-this-page.js',
			[ 'jquery' ]
		);

		$link = '<span class="ol-print-this-page" id="ol-print-this-page"><a href="#"><span class="fa fa-print"></span> Print this page</a></span>';

		$content .= $link;

		return $content;
	},
	300
);
