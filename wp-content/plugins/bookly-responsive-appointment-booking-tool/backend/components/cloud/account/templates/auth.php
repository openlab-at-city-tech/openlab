<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls;
use Bookly\Lib\Utils\Common;
/** @var array $promo_texts */
?>
<div class="btn-group">
    <button id="bookly-cloud-register-button" type="button" class="btn btn-success">
        <i class="fas fa-user-plus mr-2"></i><?php esc_html_e( 'Register', 'bookly' ) ?>
    </button>
    <button id="bookly-cloud-login-button" type="button" class="btn btn-info">
        <i class="fas fa-sign-in-alt mr-2"></i><?php esc_html_e( 'Log In', 'bookly' ) ?>
    </button>
</div>

<div id="bookly-cloud-auth-modal" class="bookly-modal bookly-fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <span class="bookly-js-modal-title bookly-js-title-register"><?php esc_html_e( 'Registration', 'bookly' ) ?></span>
                    <span class="bookly-js-modal-title bookly-js-title-login"><?php esc_html_e( 'Login', 'bookly' ) ?></span>
                    <span class="bookly-js-modal-title bookly-js-title-forgot bookly-js-title-recovery-code bookly-js-title-recovery-password"><?php esc_html_e( 'Forgot password', 'bookly' ) ?></span>
                </h4>
                <button type="button" class="close" data-dismiss="bookly-modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="bookly-form-register" class="bookly-js-modal-form">
                    <?php if ( $promo_texts['form'] ): ?>
                        <div class="form-group"><?php echo Common::stripScripts( $promo_texts['form'] ) ?></div>
                    <?php endif ?>
                    <div class="form-group">
                        <label for="bookly-r-username"><?php esc_html_e( 'Email', 'bookly' ) ?></label>
                        <input id="bookly-r-username" name="username" class="form-control" required="required" value="" type="text">
                    </div>
                    <div class="form-group">
                        <label for="bookly-r-password"><?php esc_html_e( 'Password', 'bookly' ) ?></label>
                        <input id="bookly-r-password" name="password" class="form-control" required="required" value="" type="password">
                    </div>
                    <div class="form-group">
                        <label for="bookly-r-repeat-password"><?php esc_html_e( 'Repeat password', 'bookly' ) ?></label>
                        <input id="bookly-r-repeat-password" name="password_repeat" class="form-control" required="required" value="" type="password">
                    </div>
                    <div class="form-group">
                        <label for="bookly-r-country"><?php esc_html_e( 'Country', 'bookly' ) ?></label>
                        <select id="bookly-r-country" class="form-control" name="country"></select>
                        <small class="text-muted"><?php esc_html_e( 'Your country is the location from where you consume Bookly SMS services and is used to provide you with the payment methods available in that country', 'bookly' ) ?></small>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="bookly-r-tos" name="accept_tos" required="required" value="1">
                            <label class="custom-control-label" for="bookly-r-tos">
                                <?php printf( __( 'I accept <a href="%1$s" target="_blank">Service Terms</a> and <a href="%2$s" target="_blank">Privacy Policy</a>', 'bookly' ), 'https://www.booking-wp-plugin.com/terms/', 'https://www.booking-wp-plugin.com/privacy/' ) ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="bookly-form-login" class="bookly-js-modal-form">
                    <div class="form-group">
                        <label for="bookly-username"><?php esc_html_e( 'Email', 'bookly' ) ?></label>
                        <input id="bookly-username" class="form-control" type="text" required="required" value="" name="username"/>
                    </div>
                    <div class="form-group">
                        <label for="bookly-password"><?php esc_html_e( 'Password', 'bookly' ) ?></label>
                        <input id="bookly-password" class="form-control" type="password" required="required" name="password"/>
                    </div>
                </div>
                <div id="bookly-form-forgot" class="bookly-js-modal-form">
                    <div class="form-group">
                        <label for="bookly-f-username"><?php esc_html_e( 'Email', 'bookly' ) ?></label>
                        <input id="bookly-f-username" class="form-control" type="text" name="username" value=""/>
                    </div>
                </div>
                <div id="bookly-form-recovery-code" class="bookly-js-modal-form">
                    <div class="form-group">
                        <label for="bookly-f-code"><?php esc_html_e( 'Enter code from email', 'bookly' ) ?></label>
                        <input id="bookly-f-code" name="code" class="form-control" value="" type="text"/>
                    </div>
                </div>
                <div id="bookly-form-recovery-password" class="bookly-js-modal-form">
                    <div class="form-group">
                        <label for="bookly-f-password"><?php esc_html_e( 'New password', 'bookly' ) ?></label>
                        <input id="bookly-f-password" name="password" class="form-control" value="" type="password"/>
                    </div>
                    <div class="form-group">
                        <label for="bookly-f-password-repeat"><?php esc_html_e( 'Repeat new password', 'bookly' ) ?></label>
                        <input id="bookly-f-password-repeat" name="password_repeat" class="form-control" value="" type="password"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="bookly-js-modal-buttons bookly-js-buttons-register mr-auto">
                    <a href="#" class="bookly-js-modal-form-switch" data-target="login"><?php esc_html_e( 'Log In', 'bookly' ) ?></a>
                </div>
                <div class="bookly-js-modal-buttons bookly-js-buttons-register btn-group">
                    <?php Controls\Buttons::renderSubmit( null, null, __( 'Register', 'bookly' ), array( 'name' => 'form-register' ) ) ?>
                    <?php if ( $promo_texts['button'] ) : ?>
                        <div class="border border-left-0 rounded px-2 d-flex align-items-center">
                            <h6 class="m-0"><?php echo Common::stripScripts( $promo_texts['button'] ) ?></h6>
                        </div>
                    <?php endif ?>
                </div>
                <div class="bookly-js-modal-buttons bookly-js-buttons-login mr-auto">
                    <a href="#" class="bookly-js-modal-form-switch" data-target="register"><?php esc_html_e( 'Register', 'bookly' ) ?></a><br/>
                    <a href="#" class="bookly-js-modal-form-switch" data-target="forgot"><?php esc_html_e( 'Forgot password', 'bookly' ) ?></a>
                </div>
                <div class="bookly-js-modal-buttons bookly-js-buttons-login">
                    <?php Controls\Buttons::renderSubmit( null, null, __( 'Log In', 'bookly' ), array( 'name' => 'form-login' ) ) ?>
                </div>
                <div class="bookly-js-modal-buttons bookly-js-buttons-forgot mr-auto">
                    <a href="#" class="bookly-js-modal-form-switch" data-target="login"><?php esc_html_e( 'Log In', 'bookly' ) ?></a>
                </div>
                <div class="bookly-js-modal-buttons bookly-js-buttons-forgot">
                    <?php Controls\Buttons::renderSubmit( null, null, __( 'Next', 'bookly' ), array( 'name' => 'form-forgot', 'data-step' => 0, 'data-next' => 'recovery-code' ) ) ?>
                </div>
                <div class="bookly-js-modal-buttons bookly-js-buttons-recovery-code">
                    <?php Controls\Buttons::renderSubmit( null, null, __( 'Next', 'bookly' ), array( 'name' => 'form-forgot', 'data-step' => 1, 'data-next' => 'recovery-password' ) ) ?>
                </div>
                <div class="bookly-js-modal-buttons bookly-js-buttons-recovery-password">
                    <?php Controls\Buttons::renderSubmit( null, null, __( 'Apply', 'bookly' ), array( 'name' => 'form-forgot', 'data-step' => 2, 'data-next' => 'login' ) ) ?>
                </div>
                <?php Controls\Buttons::renderCancel( __( 'Close', 'bookly' ) ) ?>
            </div>
        </div>
    </div>
</div>