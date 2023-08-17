<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-modal bookly-fade bookly-js-modal bookly-js-login">
    <div class="bookly-modal-dialog">
        <div class="bookly-modal-content">
            <form>
                <div class="bookly-modal-header">
                    <div><?php esc_html_e( 'Login', 'bookly' ) ?></div>
                    <button type="button" class="bookly-close bookly-js-close">×</button>
                </div>
                <div class="bookly-modal-body bookly-form">
                    <div class="bookly-form-group">
                        <label><?php esc_html_e( 'Username' ) ?></label>
                        <div>
                            <input type="text" name="log" required />
                        </div>
                    </div>
                    <div class="bookly-form-group">
                        <label><?php esc_html_e( 'Password' ) ?></label>
                        <div>
                            <input type="password" name="pwd" required />
                        </div>
                    </div>
                    <div class="bookly-label-error"></div>
                    <div>
                        <div>
                            <label>
                                <input type="checkbox" name="rememberme" />
                                <span><?php esc_html_e( 'Remember Me' ) ?></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="bookly-modal-footer">
                    <a class="bookly-left bookly-btn-cancel" href="<?php echo esc_url( wp_lostpassword_url() ) ?>" target="_blank"><?php esc_html_e( 'Lost your password?' ) ?></a>
                    <button class="bookly-btn-submit ladda-button" type="submit" data-spinner-size="40" data-style="zoom-in">
                        <span class="ladda-label"><?php esc_html_e( 'Log In' ) ?></span>
                    </button>
                    <a href="#" class="bookly-btn-cancel bookly-js-close"><?php esc_html_e( 'Cancel' ) ?></a>
                </div>
            </form>
        </div>
    </div>
</div>