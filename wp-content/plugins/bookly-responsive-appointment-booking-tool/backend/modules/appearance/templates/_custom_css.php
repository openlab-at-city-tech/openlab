<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib\Utils\Common;
/** @var string $custom_css custom css text */
?>

<div class="form-group">
    <?php Buttons::renderDefault( null, 'mr-2', __( 'Edit custom CSS', 'bookly' ), array( 'data-toggle' => 'bookly-modal', 'data-target' => '#bookly-custom-css-dialog' ), true ) ?>
</div>

<div id="bookly-custom-css-dialog" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Edit custom CSS', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bookly-custom-css" class="control-label"><?php esc_html_e( 'Set up your custom CSS styles', 'bookly' ) ?></label>
                    <textarea id="bookly-custom-css" class="form-control" rows="10"><?php echo Common::stripScripts( $custom_css ) ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <div id="bookly-custom-css-error"></div>
                <?php Buttons::renderSubmit( 'bookly-custom-css-save' ) ?>
                <?php Buttons::renderCancel() ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var saved_css = <?php echo json_encode( $custom_css ) ?>;
</script>
