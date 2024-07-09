<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;

?>
<div id=bookly-test-email-notifications-modal class="bookly-modal bookly-fade" tabindex=-1 role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title"><?php esc_html_e( 'Test email notifications', 'bookly' ) ?></h5>
                    <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="bookly_test_to_email"><?php esc_html_e( 'To email', 'bookly' ) ?></label>
                                <input id="bookly_test_to_email" class="form-control" type="text" name="to_email" value="<?php echo esc_attr( get_option( 'bookly_co_email', '' ) ) ?>"/>
                            </div>
                        </div>
                    </div>
                    <?php self::renderTemplate( '_common_settings', array( 'tail' => '_test' ) ) ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="bookly-dropdown">
                                    <button class="btn btn-default bookly-dropdown-toggle" data-toggle="bookly-dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                                        <?php esc_html_e( 'Notification templates', 'bookly' ) ?>
                                        (<span class="bookly-js-count">0</span>)
                                    </button>
                                    <div class="bookly-dropdown-menu">
                                        <div class="bookly-dropdown-item my-0 pl-3">
                                            <?php Inputs::renderCheckBox( __( 'All templates', 'bookly' ), null, null, array( 'id' => 'bookly-check-all-entities' ) ) ?>
                                        </div>
                                        <div class="bookly-dropdown-divider"></div>
                                        <div id="bookly-js-test-notifications-list"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php Inputs::renderCsrf() ?>
                    <?php Buttons::render( null, 'btn-success', __( 'Send', 'bookly' ), array( 'disabled' => 'disabled' ) ) ?>
                </div>
            </form>
        </div>
    </div>
</div>