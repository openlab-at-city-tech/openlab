<?php
/**
 * Displays header site branding.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$site_title  = '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . get_bloginfo( 'name', 'display' ) . '</a>';
$title_tag   = ( is_front_page() && is_home() ) ? ( 'h1' ) : ( 'p' );
$description = get_bloginfo( 'description', 'display' );

if (
	(bool) get_theme_support( 'custom-logo', 'unlink-homepage-logo' )
	&& is_front_page()
	&& ! is_paged()
) {
	$site_title = get_bloginfo( 'name', 'display' );
}

?>

<div class="site-branding">
	<?php the_custom_logo(); ?>
	<div class="site-branding-text">
		<<?php echo tag_escape( $title_tag ) ?> class="site-title"><?php
			echo $site_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?></<?php echo tag_escape( $title_tag ) ?>>

		<?php

		if ( $description || is_customize_preview() ) :
			$class = ( ! is_customize_preview() && 40 < strlen( $description ) ) ? ( ' screen-reader-text' ) : ( '' );
			?>
			<p class="site-description<?php echo esc_attr( $class ); ?>"><?php
				echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?></p>
			<?php
		endif;

		?>
	</div>
</div>
