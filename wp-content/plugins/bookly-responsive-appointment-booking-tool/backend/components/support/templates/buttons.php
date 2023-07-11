<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Controls\Buttons;
use Bookly\Backend\Components\Controls\Inputs;
use Bookly\Backend\Components\Support\Lib\Urls;
use Bookly\Lib\Utils;
/**
 * @var array $demo_links
 * @var bool $show_contact_us_notice
 * @var bool $show_feedback_notice
 */
/**
 * View demo
 */
?>
<?php
    if ( isset ( $demo_links[ $page_slug ] ) ) :
        $target = Utils\Common::prepareUrlReferrers( $demo_links[ $page_slug ], 'demo' );
        $dismiss = get_user_meta( get_current_user_id(), 'bookly_dismiss_demo_site_description', true );
?>
    <div class="col-auto">
        <a class="btn btn-default" title="<?php esc_attr_e( 'View this page at Bookly Pro Demo', 'bookly' ) ?>"
            <?php if ( $dismiss ) : ?>
                href="<?php echo esc_attr( $target ) ?>"
            <?php else : ?>
                href="#bookly-demo-site-info-modal"  data-toggle="bookly-modal"
            <?php endif ?>
        >
            <i class="fas fa-fw fa-certificate"></i><span class="d-none d-lg-inline ml-2"><?php esc_html_e( 'View this page at Bookly Pro Demo', 'bookly' ) ?></span>
        </a>
        <?php if ( ! $dismiss ) : ?>
        <div id="bookly-demo-site-info-modal" class="bookly-modal bookly-fade text-left" tabindex=-1>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php esc_html_e( 'Visit demo', 'bookly' ) ?></h5>
                        <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            <?php esc_html_e( 'The demo is a version of Bookly Pro with all installed add-ons so that you can try all the features and capabilities of the system and then choose the most suitable configuration according to your business needs.', 'bookly' ) ?>
                        </p>

                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input form-check-input" id="bookly-js-dont-show-again-demo" type="checkbox"/>
                            <label class="custom-control-label" for="bookly-js-dont-show-again-demo"><?php esc_html_e( 'don\'t show this notification again', 'bookly' ) ?></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?php Buttons::renderSubmit( null, 'bookly-js-proceed-to-demo', __( 'Proceed to demo', 'bookly' ), array( 'data-target' => $target ) ) ?>
                        <?php Buttons::renderCancel() ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
    </div>
<?php endif ?>

<?php
/**
 * Documentation
 */
?>
<div class="col-auto">
    <a href="<?php echo esc_url( 'https://api.booking-wp-plugin.com/go/' . $page_slug ) ?>" id="bookly-help-btn" target="_blank" class="btn btn-default" title="<?php esc_attr_e( 'Documentation', 'bookly' ) ?>">
        <i class="far fa-question-circle"></i><span class="d-none d-lg-inline ml-2"><?php esc_html_e( 'Documentation', 'bookly' ) ?></span>
    </a>
</div>

<?php
/**
 * Contact us
 */
?>
<div class="col-auto">
    <a href="#bookly-contact-us-modal" id="bookly-contact-us-btn" class="btn btn-default" title="<?php esc_attr_e( 'Contact us', 'bookly' ) ?>" data-toggle="bookly-modal"
        <?php if ( $show_contact_us_notice ) : ?>
            data-processed="false"
            data-content="<?php echo esc_attr( '<button type="button" class="close ml-2"><span>&times;</span></button>' . __( 'Need help? Contact us here.', 'bookly' ) ) ?>"
        <?php endif ?>
    >
        <i class="fas fa-headset"></i><span class="d-none d-lg-inline ml-2"><?php esc_html_e( 'Contact us', 'bookly' ) ?></span>
    </a>
    <div id="bookly-contact-us-modal" class="bookly-modal bookly-fade text-left" tabindex=-1>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php esc_html_e( 'Leave us a message', 'bookly' ) ?></h5>
                    <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bookly-support-name"><?php esc_html_e( 'Your name', 'bookly' ) ?></label>
                        <input type="text" id="bookly-support-name" class="form-control" value="<?php echo esc_attr( $current_user->user_firstname . ' ' . $current_user->user_lastname ) ?>" />
                    </div>
                    <div class="form-group">
                        <label for="bookly-support-email"><?php esc_html_e( 'Email address', 'bookly' ) ?> <span class="text-danger">*</span></label>
                        <input type="text" id="bookly-support-email" class="form-control" value="<?php echo esc_attr( $current_user->user_email ) ?>" />
                    </div>
                    <div class="form-group">
                        <label for="bookly-support-msg"><?php esc_html_e( 'How can we help you?', 'bookly' ) ?> <span class="text-danger">*</span></label>
                        <textarea id="bookly-support-msg" class="form-control" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php Inputs::renderCsrf() ?>
                    <?php Buttons::render( 'bookly-support-send', 'btn-success', __( 'Send', 'bookly' ) ) ?>
                    <?php Buttons::renderCancel() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Feature requests
 */
?>
<?php
    $dismiss = get_user_meta( get_current_user_id(), 'bookly_dismiss_feature_requests_description', true );
?>
<div class="col-auto">
    <a class="btn btn-default" title="<?php esc_attr_e( 'Feature requests', 'bookly' ) ?>"
        <?php if ( $dismiss ) : ?>
            href="<?php echo Utils\Common::prepareUrlReferrers( Urls::FEATURES_REQUEST_PAGE, 'notification_bar' ) ?>" target="_blank"
        <?php else : ?>
            href="#bookly-feature-requests-modal" data-toggle="bookly-modal"
        <?php endif ?>
    >
        <i class="far fa-lightbulb"></i><span class="d-none d-lg-inline ml-2"><?php esc_html_e( 'Feature requests', 'bookly' ) ?></span>
    </a>
    <?php if ( ! $dismiss ) : ?>
        <div id="bookly-feature-requests-modal" class="bookly-modal bookly-fade text-left" tabindex=-1>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php esc_html_e( 'Feature requests', 'bookly' ) ?></h5>
                        <button type="button" class="close" data-dismiss="bookly-modal" aria-label="Close"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>
                            <?php esc_html_e( 'In the Feature Requests section of our Community, you can make suggestions about what you\'d like to see in our future releases.', 'bookly' ) ?>
                        </p>
                        <p>
                            <?php esc_html_e( 'Before you post, please check if the same suggestion has already been made. If so, vote for ideas you like and add a comment with the details about your situation.', 'bookly' ) ?>
                        </p>
                        <p>
                            <?php esc_html_e( 'It\'s much easier for us to address a suggestion if we clearly understand the context of the issue, the problem, and why it matters to you. When commenting or posting, please consider these questions so we can get a better idea of the problem you\'re facing:', 'bookly' ) ?>
                        </p>
                        <ul>
                            <li><?php esc_html_e( 'What is the issue you\'re struggling with?', 'bookly' ) ?></li>
                            <li><?php esc_html_e( 'Where in your workflow do you encounter this issue?', 'bookly' ) ?></li>
                            <li><?php esc_html_e( 'Is this something that impacts just you, your whole team, or your customers?', 'bookly' ) ?></li>
                        </ul>

                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input form-check-input" id="bookly-js-dont-show-again-feature" type=checkbox />
                            <label class="custom-control-label" for="bookly-js-dont-show-again-feature"><?php esc_html_e( 'don\'t show this notification again', 'bookly' ) ?></label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?php Buttons::renderSubmit( null, 'bookly-js-proceed-requests', __( 'Proceed to Feature requests', 'bookly' ) ) ?>
                        <?php Buttons::renderCancel() ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php
/**
 * Feedback
 */
?>
<div class="col-auto">
    <a href="<?php echo Utils\Common::prepareUrlReferrers( Urls::REVIEWS_PAGE, 'feedback' ) ?>" id="bookly-feedback-btn" target="_blank" class="btn btn-default" title="<?php esc_attr_e( 'Feedback', 'bookly' ) ?>"
        <?php if ( $show_feedback_notice ) : ?>
            data-content="<?php echo esc_attr( '<button type="button" class="close ml-2"><span>&times;</span></button>' . __( 'We care about your experience of using Bookly!<br/>Leave a review and tell others what you think.', 'bookly' ) ) ?>"
        <?php endif ?>
    >
        <i class="far fa-comment-dots"></i><span class="d-none d-lg-inline ml-2"><?php esc_html_e( 'Feedback', 'bookly' ) ?></span>
    </a>
</div>