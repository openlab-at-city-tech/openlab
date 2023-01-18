<?php
/**
 * Class Folders Review
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

class folders_review_box
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

        add_action("wp_ajax_".$this->pluginSlug."_review_box", [$this, "form_review_box"]);

        add_action('admin_notices', [$this, 'admin_notices']);

    }//end __construct()


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
            $nonce = filter_input(INPUT_POST, 'nonce');
            $days  = filter_input(INPUT_POST, 'days');
            if (!empty($nonce) && wp_verify_nonce($nonce, $this->pluginSlug."_review_box")) {
                if ($days == -1) {
                    add_option($this->pluginSlug."_hide_review_box", "1");
                } else {
                    $date = date("Y-m-d", strtotime("+".$days." days"));
                    update_option($this->pluginSlug."_show_review_box_after", $date);
                }
            }

            die;
        }

    }//end form_review_box()


    /**
     * Display Review Box
     *
     * @since  1.0.0
     * @access public
     * @return html
     */
    public function admin_notices()
    {
        if (current_user_can('manage_options')) {
            $isHidden = get_option($this->pluginSlug."_hide_review_box");
            if ($isHidden !== false) {
                return;
            }

            $currentCount = get_option($this->pluginSlug."_show_review_box_after");
            if ($currentCount === false) {
                $date = date("Y-m-d", strtotime("+14 days"));
                add_option($this->pluginSlug."_show_review_box_after", $date);
                return;
            } else if ($currentCount < 35) {
                return;
            }

            $dateToShow = get_option($this->pluginSlug."_show_review_box_after");
            if ($dateToShow !== false) {
                $currentDate = date("Y-m-d");
                if ($currentDate < $dateToShow) {
                    return;
                }
            }
            ?>
            <style>
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box p a {
                    display: inline-block;
                    float: right;
                    text-decoration: none;
                    color: #999999;
                    position: absolute;
                    right: 12px;
                    top: 12px;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box p a:hover, .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box p a:focus {
                    color: #333333;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box .button span {
                    display: inline-block;
                    line-height: 27px;
                    font-size: 16px;
                }

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

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup-content {
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
                :;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-title {
                    padding: 0 0 10px 0;
                    font-weight: bold;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-options a {
                    display: block;
                    margin: 5px 0 5px 0;
                    color: #333;
                    text-decoration: none;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-options a.dismiss {
                    color: #999;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-review-box-options a:hover, .affiliate-options a:focus {
                    color: #0073aa;
                }

                button.<?php echo esc_attr($this->pluginSlug) ?>-close-review-box-popup {
                    position: absolute;
                    top: 5px;
                    right: 0;
                    border: none;
                    background: transparent;
                    cursor: pointer;
                }

                a.button.button-primary.<?php echo esc_attr($this->pluginSlug) ?>-review-box-btn {
                    font-size: 14px;
                    background: #F51366;
                    color: #fff;
                    border: solid 1px #F51366;
                    border-radius: 3px;
                    line-height: 24px;
                    -webkit-box-shadow: 0 3px 5px -3px #333333;
                    -moz-box-shadow: 0 3px 5px -3px #333333;
                    box-shadow: 0 3px 5px -3px #333333;
                    text-shadow: none;
                }

                .notice.notice-info.premio-notice {
                    position: relative;
                    padding: 1px 30px 1px 12px;
                }

                .notice.notice-info.premio-notice ul li {
                    margin: 0;
                }

                .notice.notice-info.premio-notice ul li a {
                    color: #0073aa;
                    font-size: 14px;
                    text-decoration: underline;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box p {
                    display: inline-block;
                    line-height: 30px;
                    vertical-align: middle;
                    padding: 0 10px 0 0;
                }

                .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box p img {
                    width: 30px;
                    height: 30px;
                    display: inline-block;
                    margin: 0 10px;
                    vertical-align: middle;
                    border-radius: 15px;
                }

                .review-thanks-img img {
                    width: 100%;
                    height: auto;
                    max-width: 200px;
                }

                .review-thanks-msg {
                    padding: 5px 0 0 10px;
                    display: inline-block;
                    text-align: left;
                }

                .review-thanks-box {
                    padding: 10px 0 10px 0;
                    position: relative;
                    text-align: center;
                    display: none;
                }

                .review-box-default {
                }

                .review-thanks-btn {
                    border: 0;
                    background: transparent;
                    position: absolute;
                    right: -30px;
                    top: 5px;
                }

                .review-thanks-img {
                    display: inline-block;
                    vertical-align: top;
                    width: 200px;
                }

                .thanks-msg-title {
                    font-weight: bold;
                    font-size: 18px;
                }

                .thanks-msg-desc {
                    padding: 20px 0;
                }

                .thanks-msg-footer {
                    font-weight: bold;
                }
            </style>
            <div
                    class="notice notice-info premio-notice <?php echo esc_attr($this->pluginSlug) ?>-premio-review-box <?php echo esc_attr($this->pluginSlug) ?>-premio-review-box">
                <div class="review-box-default" id="default-review-box-<?php echo esc_attr($this->pluginSlug) ?>">
                    <p>
                        <?php printf(esc_html__("Hi there, it seems like %s is bringing you some value, and that's pretty awesome! Can you please show us some love and rate %s on WordPress? It'll only take 2 minutes of your time, and will really help us spread the word - %s, %s"), "<b>".esc_attr($this->pluginName)."</b>", esc_attr($this->pluginName), "<b>Gal Dubinski</b>", "Co-founder") ?>
                        <img width="30px" src="<?php echo esc_url(plugin_dir_url(__FILE__)."../assets/images/premio-owner.jpg") ?>"/>
                        <a href="javascript:;" class="dismiss-btn <?php echo esc_attr($this->pluginSlug) ?>-premio-review-dismiss-btn"><span class="dashicons dashicons-no-alt"></span></a>
                    </p>

                    <div class="clear clearfix"></div>
                    <ul>
                        <li><a class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box-hide-btn" href="https://wordpress.org/support/plugin/folders/reviews/?filter=5" target="_blank"><?php esc_html_e("I'd love to help :) ", 'folders')?></a></li>
                        <li><a class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box-future-btn" href="javascript:;"><?php esc_html_e("Not this time ", 'folders')?></a></li>
                        <li><a class="<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box-hide-btn" href="javascript:;"><?php esc_html_e("I've already rated you ", 'folders')?></a></li>
                    </ul>
                </div>
                <div class="review-thanks-box" id="review-thanks-<?php echo esc_attr($this->pluginSlug) ?>">
                    <button class="<?php echo esc_attr($this->pluginSlug) ?>-close-thanks-btn review-thanks-btn"><span class="dashicons dashicons-no-alt"></span></button>
                    <div class="review-thanks-img">
                        <img width="30px" src="<?php echo esc_url(plugin_dir_url(__FILE__)."/images/thanks.gif") ?>"/>
                    </div>
                    <div class="review-thanks-msg">
                        <div class="thanks-msg-title"><?php esc_html_e("You are awesome ", 'folders')?> &#128591;</div>
                        <div class="thanks-msg-desc"><?php esc_html_e("Thanks for your support, We really appreciate it!", 'folders')?></div>
                        <div class="thanks-msg-footer"><?php esc_html_e("Premio team ", 'folders')?></div>
                    </div>
                    <div class="clear clearfix"></div>
                </div>
            </div>
            <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup">
                <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup-content">
                    <button class="<?php echo esc_attr($this->pluginSlug) ?>-close-review-box-popup"><span class="dashicons dashicons-no-alt"></span></button>
                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-title">
                        <?php esc_html_e("Would you like us to remind you about this later?", 'folders')?>
                    </div>
                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-review-box-options">
                        <a href="javascript:;" data-days="3"><?php esc_html_e("Remind me in 3 days ", 'folders')?></a>
                        <a href="javascript:;" data-days="10"><?php esc_html_e("Remind me in 10 days ", 'folders')?></a>
                        <a href="javascript:;" data-days="-1" class="dismiss"><?php esc_html_e("Don't remind me about this ", 'folders')?></a>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).on('ready', function () {
                    jQuery("body").addClass("has-premio-box");
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug) ?>-premio-review-dismiss-btn, .<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box-future-btn", function () {
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup").show();
                    });
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug) ?>-close-review-box-popup", function () {
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup").hide();
                    });
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug) ?>-close-thanks-btn", function () {
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup").remove();
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box").remove();
                    });
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug)?>-premio-review-box-hide-btn", function () {
                        jQuery("#default-review-box-<?php echo esc_attr($this->pluginSlug) ?>").hide();
                        jQuery("#review-thanks-<?php echo esc_attr($this->pluginSlug) ?>").show();
                        jQuery.ajax({
                            url: "<?php echo admin_url("admin-ajax.php") ?>",
                            data: "action=<?php echo esc_attr($this->pluginSlug) ?>_review_box&days=-1&nonce=<?php echo esc_attr(wp_create_nonce($this->pluginSlug."_review_box")) ?>",
                            type: "post",
                            success: function () {

                            }
                        });
                    });
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug) ?>-review-box-options a", function () {
                        var dataDays = jQuery(this).attr("data-days");
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-review-box-popup").remove();
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-premio-review-box").remove();
                        jQuery("body").removeClass("has-premio-box");
                        jQuery.ajax({
                            url: "<?php echo admin_url("admin-ajax.php") ?>",
                            data: "action=<?php echo esc_attr($this->pluginSlug) ?>_review_box&days=" + dataDays + "&nonce=<?php echo esc_attr(wp_create_nonce($this->pluginSlug."_review_box")) ?>",
                            type: "post",
                            success: function () {
                                jQuery(".<?php echo esc_attr($this->pluginSlug)?>-review-box-popup").remove();
                                jQuery(".<?php echo esc_attr($this->pluginSlug)?>-premio-review-box").remove();
                            }
                        });
                    });
                });
            </script>
            <?php
        }//end if

    }//end admin_notices()


}//end class

$folders_review_box = new folders_review_box();
