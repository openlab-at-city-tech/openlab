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
	<style type="text/css">
		p.print-this-page-description {
			margin-top: 5px;
		}
	</style>

	<label for="print-this-page-toggle">
		<input type="checkbox" id="print-this-page-toggle" value="1" name="print-this-page-toggle" <?php checked( $show ); ?>> <?php printf( "Add a 'Print this Page' link to this %s allowing site users to easily print its contents.", esc_html( $post->post_type ) ); ?>
	</label>


	<p class="description print-this-page-description">To change settings for the entire site, go to <a href="<?php echo esc_attr( admin_url( 'options-reading.php' ) ); ?>">Reading Settings.</a></p>
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
			update_post_meta( $post_id, 'print_this_page_disable', '0' );
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

	if ( '' === $disable ) {
		$option  = get_option( 'openlab-print-this-page', 'off' );
		$disable = 'on' === $option ? '0' : '1';
	}

	return '1' !== $disable;
}

/**
 * Adds the link into the page content.
 */
add_filter(
	'the_content',
	function( $content ) {
		// Handled in a different way on the main site.
		if ( bp_is_root_blog() ) {
			return $content;
		}

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

/**
 * Adds settings field to Settings > Reading.
 */
add_action(
	'admin_init',
	function() {
		register_setting(
			'reading',
			'openlab-print-this-page',
			[
				'sanitize_callback' => function( $setting ) {
					return 'on' === $setting ? 'on' : 'off';
				}
			]
		);

		add_settings_field(
			'openlab-print-this-page',
			'Print This Page',
			function() {
				$option = get_option( 'openlab-print-this-page', 'off' );
				?>
				<fieldset>
					<legend class="screen-reader-text">"Print This Page" default setting</legend>
					<input type="radio" value="on" id="print-this-page-on" name="openlab-print-this-page" <?php checked( 'on', $option ); ?> /> <label for="print-this-page-on">Enable 'Print This Page' button on all posts and pages.<br /></label><br />
					<input type="radio" value="off" id="print-this-page-off" name="openlab-print-this-page" <?php checked( 'off', $option ); ?> /> <label for="print-this-page-off">Disable 'Print This Page' button on all posts and pages.</label>
				</label>

				<p class="description">You may override the default setting on individual posts and pages.</p>
				<?php
			},
			'reading'
		);
	}
);
