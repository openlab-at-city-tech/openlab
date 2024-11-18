<?php
/**
 * The template for displaying Comments for KB Article.
 *
 * This template can be overridden by copying it to yourtheme/kb_templates/feature-comments.php.
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

if ( post_password_required() ) { ?>
	<p><?php esc_html_e( 'This page is password protected. Enter the password to view comments.', 'echo-knowledge-base' ); ?></p>	<?php
	return;
}

// all themes show comments even if disabled as long as there is comment for given article.
if ( comments_open() || get_comments_number() ) {

	echo '<div class="epkb-comments-container">';

	if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo do_blocks( '<!-- wp:post-comments /-->' );
	} else {
		comments_template( '', true );
	}

	echo '</div>';
}