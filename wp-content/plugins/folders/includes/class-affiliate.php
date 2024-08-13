<?php
/**
 * Class Folders affiliate program
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}

class Folder_affiliate_program
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
        add_action("wp_ajax_".$this->pluginSlug."_affiliate_program", [$this, "affiliate_program"]);

        add_action('admin_notices', [$this, 'admin_notices']);

    }//end __construct()


    /**
     * Updates settings for Affiliate Box
     *
     * @since  1.0.0
     * @access public
     * @return status
     */
    public function affiliate_program()
    {
        if (current_user_can('manage_options')) {
            $nonce = filter_input(INPUT_POST, 'nonce');
            $days  = filter_input(INPUT_POST, 'days');
            if (!empty($nonce) && wp_verify_nonce($nonce, $this->pluginSlug."_affiliate_program")) {
                if ($days == -1) {
                    add_option($this->pluginSlug."_hide_affiliate_box", "1");
                } else {
                    $date = gmdate("Y-m-d", strtotime("+".$days." days"));
                    update_option($this->pluginSlug."_show_affiliate_box_after", $date);
                }
            }

            die;
        }

    }//end affiliate_program()


    /**
     * Display Affiliate box
     *
     * @since  1.0.0
     * @access public
     * @return html
     */
    public function admin_notices()
    {
        $current_date = gmdate("Y-m-d H:i:s");
        if (current_user_can('manage_options')) {
            $is_hidden = get_option($this->pluginSlug."_hide_affiliate_box");
            if ($is_hidden !== false) {
                return;
            }

            $date_to_show = get_option($this->pluginSlug."_show_affiliate_box_after");
            if ($date_to_show === false || empty($date_to_show)) {
                $date = gmdate("Y-m-d", strtotime("+5 days"));
                update_option($this->pluginSlug."_show_affiliate_box_after", $date);
                return;
            }

            $current_date = gmdate("Y-m-d");
            if ($current_date < $date_to_show) {
                return;
            }
            ?>
            <style>
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate p a {
                    display: inline-block;
                    float: right;
                    text-decoration: none;
                    color: #999999;
                    position: absolute;
                    right: 12px;
                    top: 12px;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate p a:hover, .<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate p a:focus {
                    color: #333333;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate .button span {
                    display: inline-block;
                    line-height: 27px;
                    font-size: 16px;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate {
                    padding: 1px 100px 12px 12px;
                    margin: 15px 15px 2px;
                    position: relative;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    z-index: 10001;
                    background: rgba(0,0,0,0.65);
                    top: 0;
                    left: 0;
                    display: none;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup-content {
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
                .<?php echo esc_attr($this->pluginSlug) ?>-affiliate-title {
                    padding: 0 0 10px 0;
                    font-weight: bold;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-affiliate-options a {
                    display: block;
                    margin: 5px 0 5px 0;
                    color: #333;
                    text-decoration: none;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-affiliate-options a.dismiss {
                    color: #999;
                }
                .<?php echo esc_attr($this->pluginSlug) ?>-affiliate-options a:hover, .affiliate-options a:focus {
                    color: #0073aa;
                }
                button.<?php echo esc_attr($this->pluginSlug) ?>-close-affiliate-popup {
                    position: absolute;
                    top: 5px;
                    right: 0;
                    border: none;
                    background: transparent;
                    cursor: pointer;
                }
                a.button.button-primary.<?php echo esc_attr($this->pluginSlug) ?>-affiliate-btn {
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
            </style>
            <div class="notice notice-info chaty-notice <?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate <?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate">
                <p><?php
                    $message = esc_html__("Hi there, you've been using %1\$s for a while now. Do you know that %2\$s has an affiliate program? Join now and get %3\$s ", "folders");
                    printf(esc_attr($message), esc_attr($this->pluginName), "<b>".esc_attr($this->pluginName)."</b>", "<b>".esc_html__("25% lifetime commission", "folders")."</b>") ?> <a href="javascript:;" class="dismiss-btn"><span class="dashicons dashicons-no-alt"></span> <?php esc_html_e("Dismiss", "folders") ?></a></p>
                <div class="clear clearfix"></div>
                <a class="button button-primary <?php echo esc_attr($this->pluginSlug) ?>-affiliate-btn" target="_blank" href="https://premio.io/affiliates/?utm_source=inapp&plugin=folders&domain=<?php echo esc_url($_SERVER['HTTP_HOST']) ?>"><?php esc_html_e("Tell me more", "folders") ?> <span class="dashicons dashicons-arrow-right-alt"></span></a>
            </div>
            <div class="<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup">
                <div class="<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup-content">
                    <button class="<?php echo esc_attr($this->pluginSlug) ?>-close-affiliate-popup"><span class="dashicons dashicons-no-alt"></span></button>
                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-affiliate-title"><?php esc_html_e("Would you like us to remind you about this later?", "folders") ?></div>
                    <div class="<?php echo esc_attr($this->pluginSlug) ?>-affiliate-options">
                        <a href="javascript:;" data-days="3"><?php esc_html_e("Remind me in 3 days", "folders") ?></a>
                        <a href="javascript:;" data-days="10"><?php esc_html_e("Remind me in 10 days", "folders") ?></a>
                        <a href="javascript:;" data-days="-1" class="dismiss"><?php esc_html_e("Don't remind me about this", "folders") ?></a>
                    </div>
                </div>
            </div>
            <script>
                jQuery(document).ready(function(){
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate p a.dismiss-btn", function(){
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup").show();
                    });
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug) ?>-close-affiliate-popup", function(){
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup").hide();
                    });
                    jQuery(document).on("click", ".<?php echo esc_attr($this->pluginSlug) ?>-affiliate-options a", function(){
                        var dataDays = jQuery(this).attr("data-days");
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup").hide();
                        jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate").hide();
                        jQuery.ajax({
                            url: "<?php echo esc_url(admin_url("admin-ajax.php")) ?>",
                            data: "action=<?php echo esc_attr($this->pluginSlug) ?>_affiliate_program&days="+dataDays+"&nonce=<?php echo esc_attr(wp_create_nonce($this->pluginSlug."_affiliate_program")) ?>",
                            type: "post",
                            success: function() {
                                jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-affiliate-popup").remove();
                                jQuery(".<?php echo esc_attr($this->pluginSlug) ?>-premio-affiliate").remove();
                            }
                        });
                    });
                });
            </script>
            <?php
        }//end if

    }//end admin_notices()


}//end class

$Folder_affiliate_program = new Folder_affiliate_program();
