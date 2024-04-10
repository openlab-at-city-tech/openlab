<?php
/**
 * Template part for displaying a post's header
 *
 * @package kadence
 */

namespace Kadence;

$classes   = array();
$classes[] = 'entry-header';
if ( is_singular( get_post_type() ) ) {
	$classes[] = get_post_type() . '-title';
	$classes[] = 'title-align-' . ( kadence()->sub_option( get_post_type() . '_title_align', 'desktop' ) ? kadence()->sub_option( get_post_type() . '_title_align', 'desktop' ) : 'inherit' );
	$classes[] = 'title-tablet-align-' . ( kadence()->sub_option( get_post_type() . '_title_align', 'tablet' ) ? kadence()->sub_option( get_post_type() . '_title_align', 'tablet' ) : 'inherit' );
	$classes[] = 'title-mobile-align-' . ( kadence()->sub_option( get_post_type() . '_title_align', 'mobile' ) ? kadence()->sub_option( get_post_type() . '_title_align', 'mobile' ) : 'inherit' );
}
?>
<header class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php
	do_action( 'kadence_single_before_entry_header' );
	/**
	 * Kadence Entry Header
	 *
	 * Hooked kadence_entry_header 10
	 */
	do_action( 'kadence_entry_header', get_post_type(), 'normal' );
	
	do_action( 'kadence_single_after_entry_header' );
	?>
</header><!-- .entry-header -->
