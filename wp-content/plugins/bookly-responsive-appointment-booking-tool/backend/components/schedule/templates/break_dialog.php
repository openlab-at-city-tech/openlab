<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
/**
 * @var Bookly\Backend\Components\Schedule\Select $start_select
 * @var Bookly\Backend\Components\Schedule\Select $end_select
 */
?>
<div class="d-none bookly-js-edit-break-body">
    <div>
        <div class="form-row align-items-center">
            <div class="col">
                <?php $start_select->render() ?>
            </div>
            <div class="col-auto">
                <?php esc_html_e( 'to', 'bookly' ) ?>
            </div>
            <div class="col">
                <?php $end_select->render() ?>
            </div>
        </div>
        <hr>
        <div class="text-right">
            <?php Buttons::render(null, 'btn-success bookly-js-save-break', __( 'Save', 'bookly' ) ) ?>
            <?php Buttons::renderDefault( null, 'bookly-js-close', __( 'Close', 'bookly' ) ) ?>
        </div>
    </div>
</div>