<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils\Common;
/**
 * @var string $type
 * @var array $times
 * @var string|null $selected
 */
if ( $selected === null ) {
    $type == 'from' ? reset( $times ) : end( $times );
    $selected = key( $times );
}
?>
<div class="bookly-form-group bookly-time-<?php echo esc_attr( $type ) ?> bookly-left">
    <span class="bookly-bold"><?php echo Common::getTranslatedOption( $type == 'from' ? 'bookly_l10n_label_start_from' : 'bookly_l10n_label_finish_by' ) ?></span>
    <div>
        <select class="bookly-js-select-time-<?php echo esc_attr( $type ) ?>">
            <?php foreach ( $times as $key => $time ) : ?>
                <option value="<?php echo esc_attr( $key ) ?>"<?php selected( $selected == $key ) ?>><?php echo esc_html( $time ) ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div>