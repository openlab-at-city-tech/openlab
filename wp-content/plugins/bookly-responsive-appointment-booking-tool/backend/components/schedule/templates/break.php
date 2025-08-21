<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
/**
 * @var int $id
 * @var string $start
 * @var string $end
 * @var string $interval
 */
?>
<div>
    <div class="btn-group btn-group-sm mt-2" data-entity-id="<?php echo esc_attr( $id ) ?>">
        <button type="button" class="btn btn-info bookly-js-toggle-popover bookly-js-break-interval" data-start="<?php echo esc_attr( $start ) ?>" data-end="<?php echo esc_attr( $end ) ?>">
            <?php echo esc_html( $interval ) ?>
        </button>
        <button type="button" title="<?php esc_attr_e( 'Delete break', 'bookly' ) ?>" class="btn btn-info bookly-js-delete-break" data-style="zoom-in" data-spinner-size="20"><span class="ladda-label">&times;</span></button>
    </div>
</div>