<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Lib\Config;
use Bookly\Lib\Utils\Common;

/** @var Bookly\Lib\Entities\Staff $staff */
?>
<form id="bookly-staff-edit-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title h5"></div>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>×</span></button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <div class="mr-auto">
                    <?php if ( Common::isCurrentUserAdmin() ) : ?>
                        <?php Buttons::renderDelete( 'bookly-staff-delete', 'bookly-js-hide-on-loading' ) ?>
                        <?php if ( Config::proActive() ) : ?>
                            <?php Buttons::render( null, 'btn-danger ladda-button bookly-js-staff-archive bookly-js-hide-on-loading', __( 'Archive', 'bookly' ) . '…', array(), '<i class="fas fa-fw fa-archive mr-1"></i>{caption}' ) ?>
                        <?php endif ?>
                    <?php endif ?>
                </div>
                <div class="flex-fill">
                    <span class="bookly-js-errors text-danger" style="max-width: 353px;display: inline-grid;"></span>
                </div>
                <div class="ml-auto">
                    <?php Buttons::renderSubmit( null, 'bookly-js-save bookly-js-hide-on-loading' ) ?>
                    <?php Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
                </div>
            </div>
        </div>
    </div>
</form>