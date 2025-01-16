<?php
/**
 * The template for displaying Tags for KB Article.
 *
 * This template can be overridden by copying it to yourtheme/kb_templates/feature-tags.php.
 *
 * HOWEVER, on occasion Echo Plugins will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		Echo Plugins
 */
/** @var WP_Post $article */
/** @var EPKB_KB_Config_DB $kb_config */

if ( empty( $article ) || ! $article instanceof WP_Post ) {
	return;
}

$tag_class1 = EPKB_Utilities::get_css_class( '::section_box_shadow', $kb_config );

$tag_style1_escaped = EPKB_Utilities::get_inline_style( '', $kb_config );

$tag_style2_escaped = EPKB_Utilities::get_inline_style( '', $kb_config );

$taxonomy_name = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_config['id'] );
$tags_list = get_the_term_list( $article->ID, $taxonomy_name, '<ul class="eckb-tag-list"><li ' . $tag_style2_escaped . '>', '</li><li '. $tag_style2_escaped .'>', '</li></ul>' );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

if ( ! is_wp_error( $tags_list ) && $tags_list ) {
	echo '<div class="eckb-tag-container" ' . $tag_style1_escaped . ' >';
	printf( '<span class="eckb-tag-description">%1$s </span>%2$s', esc_html_x( 'Tags', 'Text used in front of tag names.', 'echo-knowledge-base' ) . ': ',	wp_kses_post( $tags_list ) );
	echo '</div>';
}
