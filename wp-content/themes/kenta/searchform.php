<?php
/**
 * Template for displaying search forms
 *
 * @package Kenta
 */

$uniqid      = uniqid( 'search-form-' );
$aria_label  = ! empty( $args['aria_label'] ) ? 'aria-label="' . esc_attr( $args['aria_label'] ) . '"' : '';
$placeholder = $args['placeholder'] ?? __( 'Search', 'kenta' );

// Backward compatibility, in case a child theme template uses a `label` argument.
if ( empty( $aria_label ) && ! empty( $args['label'] ) ) {
	$aria_label = 'aria-label="' . esc_attr( $args['label'] ) . '"';
}
?>
<form role="search" <?php echo esc_attr( $aria_label ); ?> method="get"
      action="<?php echo esc_url( home_url( '/' ) ); ?>"
      class="search-form"
>
    <div class="relative">
        <label class="flex items-center flex-grow mb-0" for="<?php echo esc_attr( $uniqid ); ?>">
            <span class="screen-reader-text"><?php _e( 'Search for:', 'kenta' ); ?></span>
            <input type="search" id="<?php echo esc_attr( $uniqid ); ?>"
                   placeholder="<?php echo esc_attr( $placeholder ) ?>"
                   value="<?php echo get_search_query(); ?>" name="s"
                   class="search-input"
            />
			<?php if ( ! ( isset( $args['disable_submit'] ) && $args['disable_submit'] ) ): ?>
                <button type="submit" class="search-submit">
                    <i class="fas fa-search"></i>
                </button>
			<?php endif; ?>
        </label>
    </div>
</form>
