<?php declare( strict_types=1 );
/**
 * The authorize button view, allowing the user to authorize their install with
 * the license server via the origin site.
 *
 * @see \TEC\Common\StellarWP\Uplink\Components\Admin\Authorize_Button_Controller
 *
 * @var string $link_text The link text, changes based on whether the user is authorized to authorize :)
 * @var string $url The location the link goes to, either the custom origin URL, or a link to the admin.
 * @var string $target The link target.
 * @var string $tag The HTML tag to use for the wrapper.
 * @var string $classes The CSS classes for the hyperlink.
 * @var string $slug The slug of the product the authorize button is for.
 */

defined( 'ABSPATH' ) || exit;
?>

<<?php echo esc_html( $tag ) ?> class="uplink-authorize-container">
	<a href="<?php echo esc_url( $url ) ?>"
	   target="<?php echo $target ? esc_attr( $target ) : '' ?>"
	   <?php echo $classes ? sprintf( 'class="%s"', esc_attr( $classes ) ) : '' ?>
	   data-plugin-slug="<?php echo esc_attr( $slug ); ?>"
	>
		<?php echo esc_html( $link_text ) ?>
	</a>
</<?php echo esc_html( $tag ) ?>>

