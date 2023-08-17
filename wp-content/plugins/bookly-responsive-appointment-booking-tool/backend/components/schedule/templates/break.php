<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/**
 * @var int $id
 * @var string $interval
 */
?>
<div class="btn-group btn-group-sm mb-1" data-entity-id="<?php echo esc_attr( $id ) ?>">
    <button type="button" class="btn btn-info bookly-js-toggle-popover bookly-js-break-interval">
        <?php echo esc_html( $interval ) ?>
    </button>
    <button type="button" title="<?php esc_attr_e( 'Delete break', 'bookly' ) ?>" class="btn btn-info bookly-js-delete-break mr-1" data-style="zoom-in" data-spinner-size="20"><span class="ladda-label">&times;</span></button>
</div>