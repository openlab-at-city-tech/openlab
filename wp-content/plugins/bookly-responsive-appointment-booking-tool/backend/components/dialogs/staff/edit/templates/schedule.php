<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Dialogs\Staff\Edit\Proxy;
/**
 * @var Bookly\Backend\Components\Schedule\Component $schedule
 * @var array $ss_ids
 */
?>
<div>
    <form>
        <?php Proxy\Locations::renderLocationSwitcher( $staff_id, $location_id, 'custom_schedule' ) ?>
        <?php $schedule->render() ?>
        <?php foreach ( $ss_ids as $id => $index ) : ?>
            <input type="hidden" name="ssi[<?php echo esc_attr( $id ) ?>]" value="<?php echo esc_attr( $index ) ?>"/>
        <?php endforeach ?>

        <div class="bookly-js-modal-footer">
            <?php Buttons::renderSubmit( 'bookly-schedule-save' ) ?>
        </div>
    </form>
</div>