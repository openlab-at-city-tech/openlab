<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Modules\Services\Proxy;
use Bookly\Lib;
?>
<form id="bookly-create-service-modal" class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php esc_html_e( 'Create service', 'bookly' ) ?></h5>
                <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="bookly-new-service-title"><?php esc_html_e( 'Title', 'bookly' ) ?></label>
                    <input class="form-control bookly-js-new-service-title" id="bookly-new-service-title" name="title" type="text" />
                </div>
                <?php if ( count( $service_types = Proxy\Shared::prepareServiceTypes( array( Lib\Entities\Service::TYPE_SIMPLE => ucfirst( Lib\Entities\Service::TYPE_SIMPLE ) ) ) ) > 1 ) : ?>
                    <div class="form-group">
                        <label for="bookly-new-service-type"><?php esc_html_e( 'Type', 'bookly' ) ?></label>
                        <select class="form-control bookly-js-new-service-type" id="bookly-new-service-type" name="type">
                            <?php foreach ( $service_types as $type => $title ): ?>
                                <option data-icon="<?php echo esc_attr( $type_icons[ $type ] ) ?>" value="<?php echo esc_attr( $type ) ?>"><?php echo esc_html( $title ) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                <?php endif ?>
            </div>
            <div class="modal-footer">
                <?php Buttons::renderSubmit( null, 'bookly-js-save', __( 'Create service', 'bookly' ) ) ?>
                <?php Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</form>