<?php
/**
 * The template for displaying single KB Article.
 *
 * @author 		Echo Plugins
 */

global $epkb_password_checked;

// this is KB Article URL so get KB ID
$kb_id = isset( $GLOBALS['post']->post_type ) ? EPKB_KB_Handler::get_kb_id_from_post_type( $GLOBALS['post']->post_type ) : EPKB_KB_Config_DB::DEFAULT_KB_ID;
if ( is_wp_error( $kb_id ) ) {
    $kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
}

$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

/**
 * Display ARTICLE PAGE content
 */
if ( empty( $hide_header_footer ) ) {
	get_header();
}

$template_style_escaped = EPKB_Utilities::get_inline_style(
    ' padding-top::       template_article_padding_top,
	        padding-bottom::    template_article_padding_bottom,
	        padding-left::      template_article_padding_left,
	        padding-right::     template_article_padding_right,
	        margin-top::        template_article_margin_top,
	        margin-bottom::     template_article_margin_bottom,
	        margin-left::       template_article_margin_left,
	        margin-right::      template_article_margin_right,', $kb_config );

//CSS Article Reset / Defaults
$article_class_escaped = '';

if ( $kb_config[ 'templates_for_kb_article_reset'] === 'on' ) {
	$article_class_escaped .= 'eckb-article-resets ';
}
if ( $kb_config[ 'templates_for_kb_article_defaults'] === 'on' ) {
	$article_class_escaped .= 'eckb-article-defaults ';
}		?>

	<div class="eckb-kb-template <?php echo $article_class_escaped; ?>" <?php echo $template_style_escaped; ?>>	      <?php

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

			$content = get_the_content();
			$content = apply_filters( 'the_content', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );
			$content = EPKB_Layouts_Setup::get_kb_page_output_hook( $content, false );
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $content;

		}          	?>

	</div> <?php

if ( empty( $hide_header_footer ) ) {
	get_footer();
}