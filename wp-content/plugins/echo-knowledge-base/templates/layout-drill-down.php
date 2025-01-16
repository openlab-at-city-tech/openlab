<?php
/**
 * KB Template for KB Main Page.
 *
 * @author 		Echo Plugins
 */

global $epkb_password_checked;

$kb_id = EPKB_Utilities::get_eckb_kb_id();
$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

/**
 * Display MAIN PAGE content
 */
if ( empty( $hide_header_footer ) ) {
	get_header();
}

// initialize Main Page title
if ( $kb_config[ 'template_main_page_display_title' ] === 'off' ) {
	$kb_main_pg_title_escaped = '';
} else {
	$kb_main_pg_title_escaped = '<h1 class="eckb_main_title">' . esc_html( get_the_title() ) . '</h1>';
}

$template_style_escaped = EPKB_Utilities::get_inline_style(
           'padding-top::       template_main_page_padding_top,
	        padding-bottom::    template_main_page_padding_bottom,
	        padding-left::      template_main_page_padding_left,
	        padding-right::     template_main_page_padding_right,
	        margin-top::        template_main_page_margin_top,
	        margin-bottom::     template_main_page_margin_bottom,
	        margin-left::       template_main_page_margin_left,
	        margin-right::      template_main_page_margin_right,', $kb_config );       ?>

	<div class="eckb-kb-template" <?php echo $template_style_escaped; ?>>	        <?php

	    echo $kb_main_pg_title_escaped;

		while ( have_posts() ) {

		    the_post();

			if ( post_password_required() ) {
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo get_the_password_form();
				echo '</div>';
				get_footer();
				return;
			}
			$epkb_password_checked = true;

			// get post content
			$post = empty( $GLOBALS['post'] ) ? '' : $GLOBALS['post'];
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				continue;
			}
			$post_content = $post->post_content;

			// output the full content of the KB Main Page using 'the_content' filter
			$post_content = apply_filters( 'the_content', $post_content );
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo str_replace( ']]>', ']]&gt;', $post_content ); // the replacement is required to run Elementor editor correctly

		}  ?>

	</div>   <?php

if ( empty( $hide_header_footer ) ) {
	get_footer();
}
