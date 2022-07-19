<?php
if (!defined('ABSPATH')) {exit;}
class Folders_upgrade_box {
    public $plugin_name = "Folders";
    public $plugin_slug = "folders";

    public function __construct() {
        add_action("wp_ajax_".$this->plugin_slug."_upgrade_hide_box", array($this, "upgrade_to_pro"));
        add_action('admin_notices', array($this, 'admin_notices'));
    }

    public function upgrade_to_pro() {
        $nonce = filter_input(INPUT_POST, 'nonce');
        $days = filter_input(INPUT_POST, 'days');
        if(!empty($nonce) && wp_verify_nonce($nonce, $this->plugin_slug."_upgrade_box")) {
            if($days == -1) {
                add_option($this->plugin_slug."_hide_upgrade_box", "1");
            } else {
                $date = date("Y-m-d", strtotime("+".$days." days"));
                update_option($this->plugin_slug."_show_upgrade_box_after", $date);
            }
        }
        die;
    }

    public function admin_notices() {
        $is_hidden = get_option($this->plugin_slug."_hide_upgrade_box");
        if($is_hidden !== false) {
            return;
        }
        $current_count = get_option($this->plugin_slug."_show_upgrade_box_after");
        if($current_count === false) {
            $date = date("Y-m-d", strtotime("+15 days"));
            add_option($this->plugin_slug."_show_upgrade_box_after", $date);
            return;
        } else if($current_count < 35) {
            return;
        }
        $date_to_show = get_option($this->plugin_slug."_show_upgrade_box_after");
        if($date_to_show !== false) {
            $current_date = date("Y-m-d");
            if($current_date < $date_to_show) {
                return;
            }
        }
        ?>
        <style>
            .<?php  echo esc_attr($this->plugin_slug) ?>-tab-integration-action{
                padding: 0 10px;
                float: right;
            }
            .<?php  echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box .<?php echo esc_attr($this->plugin_slug) ?>-tab-integration-action a {
                display: inline-block;
                float: right;
                text-decoration: none;
                position: unset;
                padding: 0px;
                font-size: 13px;
                right: 50px;
                top: 7px;
                color: #3C85F7;
                background-color: #fff;
                border-radius: 5px;
                border: 2px solid #3C85F7;
                min-width: 110px;
                text-align: center;
                line-height: 25px;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box .<?php echo esc_attr($this->plugin_slug) ?>-tab-integration-action a:hover{
                background-color:#fff
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box p a {
                display: inline-block;
                float: right;
                text-decoration: none;
                color: #999999;
                position: absolute;
                right: 12px;
                top: 12px;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box p a:hover, .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box p a:focus {
                color: #333333;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box .button span {
                display: inline-block;
                line-height: 27px;
                font-size: 16px;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup {
                position: fixed;
                width: 100%;
                height: 100%;
                z-index: 10001;
                background: rgba(0,0,0,0.65);
                top: 0;
                left: 0;
                display: none;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup-content {
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
                border-radius: 5px;: ;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-title {
                padding: 0 0 10px 0;
                font-weight: bold;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-options a {
                display: block;
                margin: 5px 0 5px 0;
                color: #333;
                text-decoration: none;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-options a.dismiss {
                color: #999;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-options a:hover, .affiliate-options a:focus {
                color: #0073aa;
            }
            button.<?php echo esc_attr($this->plugin_slug) ?>-close-upgrade-box-popup {
                position: absolute;
                top: 5px;
                right: 0;
                border: none;
                background: transparent;
                cursor: pointer;
            }
            a.button.button-primary.<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-btn {
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
            .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box p {
                line-height: 30px;
                vertical-align: middle;
                padding: 0 10px 0 0;
                font-size: 14px;
            }
            .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box p img {
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
            .upgrade-box-default {
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
                padding: 24px 0;
            }
            .thanks-msg-footer {
                font-weight: bold;
            }
        </style>
        <?php
        $customize_folders = get_option("customize_folders");
        if(isset($customize_folders['show_folder_in_settings']) && $customize_folders['show_folder_in_settings'] == "yes") {
            $upgradeURL = admin_url("options-general.php?page=wcp_folders_settings&setting_page=upgrade-to-pro");
        } else {
            $upgradeURL = admin_url("admin.php?page=folders-upgrade-to-pro");
        }
        ?>
        <div class="notice notice-info premio-notice <?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box <?php echo  esc_attr($this->plugin_slug) ?>-premio-upgrade-box">
            <div class="upgrade-box-default" id="default-upgrade-box-<?php echo esc_attr($this->plugin_slug) ?>">
                <p>
                    <?php printf(esc_html__("%s to experience more exclusive features like dynamic folders, subfolders, access restriction & more", 'folders'), "<b>".esc_html__("Upgrade to ",'folders')." ".$this->plugin_name." Pro</b>") ?>
					<span class="<?php echo esc_attr($this->plugin_slug) ?>-tab-integration-action">
						<a class="upgradenow-box-btn" data-days="-1" href="<?php echo esc_url($upgradeURL); ?>" target="_blank" ><?php esc_html_e("Upgrade now", 'folders'); ?></a>
					</span>					
                    <a href="javascript:;" class="dismiss-btn <?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-dismiss-btn"><span class="dashicons dashicons-no-alt"></span></a>
                </p>
                <div class="clear clearfix"></div>
            </div>            
        </div>
        <div class="<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup">
            <div class="<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup-content">
                <button class="<?php echo esc_attr($this->plugin_slug) ?>-close-upgrade-box-popup"><span class="dashicons dashicons-no-alt"></span></button>
                <div class="<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-title"> <?php esc_html_e("Would you like us to remind you about this later? ",'folders')?></div>
                <div class="<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-options">
                    <a href="javascript:;" data-days="7"> <?php esc_html_e("Remind me in 7 days ",'folders')?></a>
                    <a href="javascript:;" data-days="30"> <?php esc_html_e("Remind me in 30 days",'folders')?></a>
                    <a href="javascript:;" data-days="-1" class="dismiss"> <?php esc_html_e("Don't remind me about this ",'folders')?></a>
                </div>
            </div>
        </div>
        <script>
            jQuery(document).ready(function(){
                jQuery("body").addClass("has-premio-box");
				
				jQuery(document).on("click",".upgradenow-box-btn",function(){
					jQuery(".notice.notice-info.premio-notice").hide();
					
					var dataDays = jQuery(this).attr("data-days");
					
                    jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup").remove();
                    jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box").remove();
                    jQuery("body").removeClass("has-premio-box");
                    
					jQuery.ajax({
                        url: "<?php echo admin_url("admin-ajax.php") ?>",
                        data: "action=<?php echo esc_attr($this->plugin_slug) ?>_upgrade_box&days="+dataDays+"&nonce=<?php echo esc_attr(wp_create_nonce($this->plugin_slug."_upgrade_box")) ?>",
                        type: "post",
                        success: function() {
                            jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup").remove();
                            jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box").remove();
                        }
                    });
				});
                jQuery(document).on("click", ".<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-dismiss-btn, .<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box-future-btn", function(){
                    jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup").show();
                });
                jQuery(document).on("click", ".<?php echo esc_attr($this->plugin_slug) ?>-close-upgrade-box-popup", function(){
                    jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup").hide();
                });               
                
                jQuery(document).on("click", ".<?php echo esc_attr($this->plugin_slug)?>-upgrade-box-options a", function(){
                    var dataDays = jQuery(this).attr("data-days");
                    jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup").remove();
                    jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box").remove();
                    jQuery("body").removeClass("has-premio-box");
                    jQuery.ajax({
                        url: "<?php echo admin_url("admin-ajax.php") ?>",
                        data: "action=<?php echo esc_attr($this->plugin_slug) ?>_upgrade_hide_box&days="+dataDays+"&nonce=<?php echo esc_attr(wp_create_nonce($this->plugin_slug."_upgrade_box")) ?>",
                        type: "post",
                        success: function() {
                            jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-upgrade-box-popup").remove();
                            jQuery(".<?php echo esc_attr($this->plugin_slug) ?>-premio-upgrade-box").remove();
                        }
                    });
                });
            });
        </script>
        <?php
    }
}
$Folders_upgrade_box = new Folders_upgrade_box();