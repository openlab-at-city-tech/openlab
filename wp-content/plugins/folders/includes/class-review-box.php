<?php
/**
 * Review Class
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (defined('ABSPATH') === false) {
    exit;
}

class Folders_Free_Review_Box
{

    /**
     * The Name of this plugin.
     *
     * @var    string    $pluginName    The Name of this plugin.
     * @since  1.0.0
     * @access public
     */
    public $pluginName = "Folders";

    /**
     * The Slug of this plugin.
     *
     * @var    string    $pluginSlug    The Slug of this plugin.
     * @since  1.0.0
     * @access public
     */
    public $pluginSlug = "folders";

    /**
     * The Plugin review status.
     *
     * @var    string    $reviewStatus    The Slug of this plugin.
     * @since  1.0.0
     * @access public
     */
    public $reviewStatus = true;

    /**
     * The plugin slug for WordPress
     *
     * @var    string    $wpPluginSlug    The Slug of this plugin.
     * @since  1.0.0
     * @access public
     */
    public $wpPluginSlug = "folders";

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $isHidden = get_option( $this->pluginSlug . "_hide_review_box" );
        if ( $isHidden !== false ) {
            $this->reviewStatus = false;
        }

        $currentCount = get_option( $this->pluginSlug . "_show_review_box_after" );
        if ( $currentCount === false ) {
            $date = gmdate( "Y-m-d", strtotime( "+14 days" ) );
            add_option( $this->pluginSlug . "_show_review_box_after", $date );
            $this->reviewStatus = false;
        }

        $dateToShow = get_option( $this->pluginSlug . "_show_review_box_after" );
        if ( $dateToShow !== false ) {
            $currentDate = gmdate( "Y-m-d" );
            if ( $currentDate < $dateToShow ) {
                $this->reviewStatus = false;
            }
        }

	    //$this->reviewStatus = true;
        $page_views = intval(get_option("get_folders_page_views"));
        if($this->reviewStatus) {
            add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
            add_action('admin_notices', [$this, 'admin_notices']);
        } else {
            if($page_views == 1 || $page_views == 2) {
                add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
            }
        }
        add_action("wp_ajax_".$this->pluginSlug."_review_box", [$this, "form_review_box"]);
        add_action("wp_ajax_".$this->pluginSlug."_review_box_message", [$this, "form_review_box_message"]);

    }//end __construct()


    public function enqueue_scripts() {
        if (current_user_can('manage_options')) {
            wp_enqueue_style($this->pluginSlug."-star-rating-svg", plugins_url('../assets/css/star-rating-svg.css', __FILE__), [], WCP_FOLDER_VERSION);
            wp_enqueue_script($this->pluginSlug."-star-rating-svg", plugins_url('../assets/js/jquery.star-rating-svg.min.js', __FILE__), ['jquery'], WCP_FOLDER_VERSION, true);
            wp_localize_script(
                $this->pluginSlug."-star-rating-svg",
                'pr_rating_settings',
                ['has_settings' => 1]
            );

        }
    }

    /**
     * Updates settings for Review Box Message
     *
     * @since  1.0.0
     * @access public
     * @return $status
     */
    public function form_review_box_message()
    {
        if (current_user_can('manage_options')) {
            $nonce = filter_input(INPUT_POST, 'nonce');

            if (!empty($nonce) && wp_verify_nonce($nonce, $this->pluginSlug."_review_box_message")) {
                add_option($this->pluginSlug."_hide_review_box", "1");
                update_option("get_folders_page_views", -1);
                $rating  = filter_input(INPUT_POST, 'rating');
                $message = filter_input(INPUT_POST, 'message');

                global $current_user;
                $postMessage = [];

                $domain    = site_url();
                $user_name = $current_user->first_name." ".$current_user->last_name;
                $email     = $current_user->user_email;

                $messageData          = [];
                $messageData['key']   = "email";
                $messageData['value'] = $email;
                $postMessage[]        = $messageData;

                $messageData          = [];
                $messageData['key']   = "stars";
                $messageData['value'] = $rating;
                $postMessage[]        = $messageData;

                $messageData          = [];
                $messageData['key']   = "message";
                $messageData['value'] = $message;
                $postMessage[]        = $messageData;

                $apiParams = [
                    'title'   => 'Review for '.$this->pluginName.' WordPress',
                    'domain'  => $domain,
                    'email'   => "contact@premio.io",
                    'url'     => site_url(),
                    'name'    => $user_name,
                    'message' => $postMessage,
                    'plugin'  => $this->pluginName,
                    'type'    => "Review",
                ];

                // Sending message to Crisp API
                $apiResponse = wp_safe_remote_post("https://premioapps.com/premio/send-feedback-api.php", ['body' => $apiParams, 'timeout' => 15, 'sslverify' => true]);

                if (is_wp_error($apiResponse)) {
                    wp_safe_remote_post("https://premioapps.com/premio/send-feedback-api.php", ['body' => $apiParams, 'timeout' => 15, 'sslverify' => false]);
                }
            }
            die;
        }

    }//end form_review_box_message()

    /**
     * Updates settings for Review Box
     *
     * @since  1.0.0
     * @access public
     * @return status
     */
    public function form_review_box()
    {
        if (current_user_can('manage_options')) {
            $nonce = esc_attr(filter_input(INPUT_POST, 'nonce'));
            $days  = esc_attr(filter_input(INPUT_POST, 'days'));
            if (!empty($nonce) && wp_verify_nonce($nonce, $this->pluginSlug."_review_box")) {
                update_option("get_folders_page_views", 4);
                if ($days == -1) {
                    add_option($this->pluginSlug."_hide_review_box", "1");
                    update_option("get_folders_page_views", -1);
                } else {
                    $date = gmdate("Y-m-d", strtotime("+".$days." days"));
                    update_option($this->pluginSlug."_show_review_box_after", $date);
                }
            }
            die;
        }
    }//end form_review_box()


    /**
     * Show Review HTML
     *
     * @since  1.0.0
     * @access public
     * @return html
     */
    public function admin_notices()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>

        <!-- premio default review box -->
        <div class="notice notice-info premio-notice <?php echo esc_attr($this->pluginSlug) ?>-premio-review-box">

            <!-- premio review box default -->
            <style id="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default-stylesheet">
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box {
                    position: relative;
                    border-left-color: #B78DEB;
                    padding: 18px 25px 18px 15px !important;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__title {
                    color: #000000;
                    font-size: 18px;
                    line-height: 27px;
                    font-weight: 600;
                    font-family: 'Arial';
                    margin: 0;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__title span{
                    color: #B78DEB;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default p {
                    color: #595959;
                    line-height: 21px;
                    vertical-align: middle;
                    padding: 0 10px 7px 0;
                    font-size: 14px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-default__dismiss-btn {
                    position: absolute;
                    right: 5px;
                    top: 5px;
                    opacity: .6;
                    border: 0;
                    padding: 0;
                    background-color: transparent;
                    cursor: pointer;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-default__dismiss-btn:hover {
                    opacity: 1;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__co-founder {
                    display: inline-flex;
                    align-items: center;
                    gap: 15px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__co-founder-img {
                    width: 30px;
                    height: 30px;
                    display: inline-block;
                    vertical-align: middle;
                    border-radius: 15px;
                }
            </style>

            <!-- premio review box thank you -->
            <style id="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you-stylesheet">
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you {
                    display: none;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you .thanks-wrap {
                    display: inline-flex;
                    gap: 12px;
                    align-items: center;
                    flex-wrap: wrap;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__dismiss-btn {
                    position: absolute;
                    top: 5px;
                    right: 5px;
                    opacity: .6;
                    border: 0;
                    padding: 0;
                    background-color: transparent;
                    cursor: pointer;
                }

                <?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__dismiss-btn:hover {
                    opacity: 1;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__image {
                    width: 100%;
                    height: auto;
                    max-width: 200px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__message .title {
                    font-weight: bold;
                    font-size: 18px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__message .desc {
                    padding: 8px 0;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__message .footer {
                    font-weight: bold;
                }

            </style>

            <!-- review box popup stylesheet-->
            <style id="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup-stylesheet">

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    z-index: 10001;
                    background: rgba(0, 0, 0, 0.65);
                    top: 0;
                    left: 0;
                    display: none;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__dismiss-btn {
                    position: absolute;
                    right: 5px;
                    top: 5px;
                    opacity: .6;
                    border: 0;
                    padding: 0;
                    background-color: transparent;
                    cursor: pointer;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__dismiss-btn:hover {
                    opacity: 1;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__content {
                    background: #ffffff;
                    padding: 20px;
                    position: absolute;
                    max-width: 450px;
                    width: 100%;
                    margin: 0 auto;
                    top: 45%;
                    left: 0;
                    right: 0;
                    -webkit-border-radius: 5px;
                    -moz-border-radius: 5px;
                    border-radius: 5px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__title {
                    padding: 0 0 10px 0;
                    font-weight: bold;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__options a {
                    display: block;
                    margin: 10px 0 5px 0;
                    color: #333;
                    text-decoration: none;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__options .dismiss {
                    color: #999;
                }
            </style>

            <!-- feedback popup stylesheet-->
            <style id="<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup-stylesheet">

                .<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    z-index: 10001;
                    background: rgba(0, 0, 0, 0.65);
                    top: 0;
                    left: 0;
                    display: none;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__dismiss-btn {
                    position: absolute;
                    right: 5px;
                    top: 5px;
                    opacity: .6;
                    border: 0;
                    padding: 0;
                    background-color: transparent;
                    cursor: pointer;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__dismiss-btn:hover {
                    opacity: 1;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__content {
                    background: #ffffff;
                    padding: 20px;
                    position: absolute;
                    max-width: 450px;
                    width: 100%;
                    margin: 0 auto;
                    top: 50%;
                    transform: translateY(-50%);
                    left: 0;
                    right: 0;
                    -webkit-border-radius: 5px;
                    -moz-border-radius: 5px;
                    border-radius: 5px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__form {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__form textarea {
                    padding: 10px;
                    margin-top: 15px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__form button {
                    border: none;
                    padding: 10px 0;
                    width: 100%;
                    background: #ff6624;
                    color: #fff;
                    border-radius: 4px;
                    cursor: pointer;
                    display: inline-block;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__options .dismiss {
                    color: #999;
                }
                .please-rate-us {
                    padding: 10px 0 0;
                }
                .please-rate-us .rate-us-title {
                    vertical-align: middle;
                }
                .please-rate-us .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__rating {
                    vertical-align: middle;
                }
            </style>

            <!-- default layout -->
            <div class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default">
                <h2 class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__title">
                    <?php esc_html_e('Your', 'folders') ?> <span><?php esc_html_e('feedback', 'folders') ?></span> <?php esc_html_e('matters, please leave a review', 'folders') ?> 🙏
                </h2>

                <button class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-default__dismiss-btn">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>

                <p><?php
                    $message = esc_html__("Hi there, it seems like %1\$s is bringing you some value, and that's pretty awesome! Can you please show us some love and rate %2\$s on WordPress? It'll only take 2 minutes of your time, and will really help us spread the word", 'folders');
                    printf(esc_attr($message), "<b>".esc_attr($this->pluginName)."</b>", esc_attr($this->pluginName));?></p>

                <div class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__co-founder">
                    <span>
                        <b><?php esc_html_e('Gal Dubinski', 'folders') ?></b>,
                        <?php esc_html_e('Co-founder', 'folders') ?>
                    </span>
                    <img class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__co-founder-img" width="30" height="30" src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/owner.jpg") ?>" />
                </div>

                <div class="please-rate-us">
                    <div class="rate-us-title"><?php esc_html_e("Please rate us:"); ?></div>
                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__default__rating"></div>
                </div>
            </div> <!--end .premio-review-box__default-->

            <div class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you">
                <div class="thanks-wrap">
                    <button class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__dismiss-btn">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>

                    <img class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__image" width="200" src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/thanks.gif") ?>" />

                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box__thank-you__message">
                        <div class="title"><?php esc_html_e("You are awesome ", 'folders')?> &#128591;</div>
                        <div class="desc"><?php esc_html_e("Thanks for your support, we really appreciate it!", 'folders')?></div>
                        <div class="footer"><?php esc_html_e("Premio team ", 'folders')?></div>
                    </div>
                </div>
            </div> <!--end .premio-review-box__thank-you-->

            <!-- review popup -->
            <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup">
                <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__content">
                    <button class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__dismiss-btn">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__title">
                        <?php esc_html_e("Would you like us to remind you about this later?", 'folders')?>
                    </div>

                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup__options">
                        <a href="#" data-days="3"><?php esc_html_e("Remind me in 3 days ", 'folders')?></a>
                        <a href="#" data-days="10"><?php esc_html_e("Remind me in 10 days ", 'folders')?></a>
                        <a href="#" data-days="-1" class="dismiss"><?php esc_html_e("Don't remind me about this ", 'folders')?></a>
                    </div>
                </div>
            </div> <!--end .review-box-popup-->

            <!-- feedback popup -->
            <div class="<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup">
                <div class="<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__content">
                    <button class="<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__dismiss-btn">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                    <form class="<?php echo esc_attr($this->pluginSlug) ?>-feedback-popup__form">
                        <textarea name="message" id="message" cols="30" rows="5" placeholder="What's your feedback?"></textarea>
                        <button id="submit-btn" type="submit"><?php esc_html_e('Submit', 'folders') ?></button>
                    </form>
                </div>
            </div> <!--end .feedback-popup-->

        </div> <!--end .premio-notice-->

        <script>
            (function($) {

                $(document).ready(function() {
                    function FoldersFreeReview() {
                        this.prefix = "<?php echo esc_attr($this->pluginSlug) ?>";
                        this.reviewLink = "https://wordpress.org/support/plugin/<?php echo esc_attr($this->wpPluginSlug) ?>/reviews/?filter=5";
                        this.rating = 5;

                        this.renderRating();
                        this.bindEvents();
                    }

                    FoldersFreeReview.prototype.getSelectors = function () {
                        return {
                            body: 'body',
                            rating: `.${this.prefix}-premio-review-box__default__rating`,
                            reviewBox: `.${this.prefix}-premio-review-box`,
                            feedbackForm: `.${this.prefix}-feedback-popup__form`,
                            reminderPopup: `.${this.prefix}-review-box-popup`,
                            feedbackPopup: `.${this.prefix}-feedback-popup`,
                            reviewBoxDefault: `.${this.prefix}-premio-review-box__default`,
                            reviewBoxThankYou: `.${this.prefix}-premio-review-box__thank-you`,
                            defaultDismissBtn: `.${this.prefix}-review-box-default__dismiss-btn`,
                            thankYouDismissBtn: `.${this.prefix}-premio-review-box__thank-you__dismiss-btn`,
                            feedbackDismissBtn: `.${this.prefix}-feedback-popup__dismiss-btn`,
                            reminderPopupOptions: `.${this.prefix}-review-box-popup__options a`,
                            reminderPopupDismissBtn: `.${this.prefix}-review-box-popup__dismiss-btn`,
                        }
                    }

                    FoldersFreeReview.prototype.getElements = function () {
                        const selectors = this.getSelectors();
                        return {
                            $body: $(selectors.body),
                            $rating: $(selectors.rating),
                            $reviewBox: $(selectors.reviewBox),
                            $feedbackForm: $(selectors.feedbackForm),
                            $reminderPopup: $(selectors.reminderPopup),
                            $feedbackPopup: $(selectors.feedbackPopup),
                            $reviewBoxDefault: $(selectors.reviewBoxDefault),
                            $reviewBoxThankYou: $(selectors.reviewBoxThankYou),
                            $defaultDismissBtn: $(selectors.defaultDismissBtn),
                            $thankYouDismissBtn: $(selectors.thankYouDismissBtn),
                            $feedbackDismissBtn: $(selectors.feedbackDismissBtn),
                            $reminderPopupOptions: $(selectors.reminderPopupOptions),
                            $reminderPopupDismissBtn: $(selectors.reminderPopupDismissBtn)
                        }
                    }

                    FoldersFreeReview.prototype.bindEvents = function () {
                        const elements = this.getElements();
                        const selectors = this.getSelectors();

                        elements.$body.addClass("has-premio-box");
                        elements.$defaultDismissBtn.on('click', this.toggleReminderPopup.bind(elements));
                        elements.$reminderPopupDismissBtn.on('click', this.toggleReminderPopup.bind(elements, false));
                        elements.$reminderPopupOptions.on('click', this.reminderHandler.bind(this));
                        elements.$thankYouDismissBtn.on('click', this.thankYouDismissHandler.bind(this));
                        elements.$feedbackDismissBtn.on('click', this.feedbackToggle.bind(this, false));
                        elements.$feedbackForm.on('submit', this.feedbackFormHandler.bind(this));

                        //close reminder/feedback popup when click outside
                        $(window).on('click', ev => {
                            const $target = $(ev.target);
                            if (
                                elements.$reminderPopup.hasClass('open') &&
                                $target.parents(selectors.reminderPopup).length === 0
                            ) {
                                elements.$reminderPopupDismissBtn.trigger('click');
                            }

                            if (
                                elements.$feedbackPopup.hasClass('open') &&
                                $target.parents(selectors.feedbackPopup).length === 0
                            ) {
                                elements.$feedbackDismissBtn.trigger('click');
                            }
                        })
                    }

                    FoldersFreeReview.prototype.feedbackFormHandler = function (ev) {
                        ev.preventDefault();
                        const elements = this.getElements();
                        const message = elements.$feedbackForm.find('#message').val();
                        const rating = this.rating;

                        $.ajax({
                            url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                            data: {
                                action: "<?php echo esc_attr($this->pluginSlug) ?>_review_box_message",
                                rating: rating,
                                nonce: "<?php echo esc_attr(wp_create_nonce($this->pluginSlug . "_review_box_message")) ?>",
                                message: message
                            },
                            type: "post",
                        });
                        elements.$feedbackDismissBtn.trigger('click');
                        elements.$reviewBox.remove();
                        elements.$reminderPopup.remove();
                        // send hide request after submitting feedback form
                        this.sendHideRequest(-1);
                    }

                    FoldersFreeReview.prototype.thankYouDismissHandler = function () {
                        const elements = this.getElements();
                        elements.$reviewBox.remove();
                        elements.$reminderPopup.remove();
                        this.sendHideRequest(-1);
                    }

                    FoldersFreeReview.prototype.reminderHandler = function (ev) {
                        ev.preventDefault();
                        const dataDays = $(ev.target).data("days");
                        const elements = this.getElements();

                        elements.$body.removeClass("has-premio-box");
                        elements.$reminderPopupDismissBtn.trigger('click');
                        elements.$reviewBox.remove();
                        this.sendHideRequest(dataDays);
                    }

                    FoldersFreeReview.prototype.sendHideRequest = function (dataDays = -1) {
                        $.ajax({
                            url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                            data: "action=<?php echo esc_attr($this->pluginSlug) ?>_review_box&days=" + dataDays + "&nonce=<?php echo esc_attr(wp_create_nonce($this->pluginSlug . "_review_box")) ?>",
                            type: "post",
                        });
                    }

                    FoldersFreeReview.prototype.toggleReminderPopup = function (action = true) {
                        if (action) {
                            this.$reminderPopup.fadeIn(200, function () {
                                $(this).addClass('open')
                            });
                        } else {
                            this.$reminderPopup.fadeOut(200).removeClass('open');
                        }
                    }

                    FoldersFreeReview.prototype.feedbackToggle = function (action = true) {
                        const elements = this.getElements();
                        if (action) {
                            elements.$feedbackPopup.fadeIn(200, function () {
                                $(this).addClass('open')
                            });
                        } else {
                            elements.$rating.starRating('unload');
                            elements.$reviewBoxDefault.append(`<div class="${this.prefix}-premio-review-box__default__rating"></div>`)
                            elements.$feedbackPopup.fadeOut(200).removeClass('open');
                            this.renderRating();
                        }
                    }

                    FoldersFreeReview.prototype.renderRating = function () {
                        const self = this;
                        const elements = self.getElements();
                        elements.$rating.starRating({
                            initialRating: self.rating,
                            useFullStars: true,
                            strokeColor: '#894A00',
                            strokeWidth: 10,
                            minRating: 1,
                            starSize: 25,
                            callback(currentRate) {
                                if (currentRate !== 5) {
                                    self.rating = currentRate;
                                    self.feedbackToggle(true);
                                } else {
                                    elements.$reviewBoxDefault.hide();
                                    elements.$reviewBoxThankYou.show();
                                    window.open(self.reviewLink, '_blank');
                                    self.sendHideRequest(-1);
                                }
                            }
                        })
                    }

                    new FoldersFreeReview();
                });

            })( jQuery )
        </script>
        <?php

    }//end admin_notices()

}//end class

$Folders_Free_Review_Box = new Folders_Free_Review_Box();
