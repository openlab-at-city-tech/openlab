<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/** @var string $id */
/** @var string $codes */
/** @var string $value */
/** @var string $doc_slug */
/** @var string $additional_classes */
?>
<div id="<?php echo esc_attr( $id ) ?>" class="bookly-ace-editor<?php if ( $additional_classes ) echo ' ' . esc_attr( $additional_classes ) ?>"<?php if ( $codes ) : ?> data-codes="<?php echo esc_attr( $codes ) ?>"<?php endif ?> data-value="<?php echo esc_attr( $value ) ?>"></div>
<?php if ( $doc_slug ) : ?>
    <small class="form-text text-muted"><?php printf( __( 'Start typing "{" to see the available codes. For more information, see the <a href="%s" target="_blank">documentation</a> page', 'bookly' ), 'https://api.booking-wp-plugin.com/go/' . $doc_slug ) ?></small>
<?php endif ?>