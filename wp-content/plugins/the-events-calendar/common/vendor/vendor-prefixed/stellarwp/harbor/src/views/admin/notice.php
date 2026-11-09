<?php declare( strict_types=1 );
/**
 * Render a WordPress dashboard notice.
 *
 * @see \StellarWP\Harbor\Notice\Notice_Controller
 *
 * @var string                             $message           The message to display.
 * @var string                             $classes           The CSS classes for the notice.
 * @var string                             $id                Optional unique ID for persistent dismissal.
 * @var array<string, array<string, bool>> $allowed_tags      The allowed HTML tags for wp_kses().
 * @var string[]                           $allowed_protocols The allowed protocols for wp_kses().
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="<?php echo esc_attr( $classes ); ?>"<?php echo $id ? ' data-lw-harbor-notice-id="' . esc_attr( $id ) . '"' : ''; ?>>
	<p><?php echo wp_kses( $message, $allowed_tags, $allowed_protocols ); ?></p>
</div>
