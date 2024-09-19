<?php
/**
 * Email Signup Form
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (defined('ABSPATH') === false) {
    exit;
}
$email = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == "playground.wordpress.net")?"":get_option('admin_email');
?>
<style>
    body {
        background: #ffffff !important;
    }
</style>
<div class="starts-testimonials-updates-form">
    <div class="updates-form-form-left">
        <div class="updates-form-form-left-text">premio</div>
        <img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/wcupdate_email.svg") ?>" style="width: 230px;margin: 60px 0px 20px 0px;" />
        <p><?php esc_html_e('Grow your WordPress or Shopify websites with our plugins', 'stars-testimonials'); ?></p>
    </div>
    <div class="updates-form-form-right">
        <div class="update-title"><?php esc_html_e('Be the first to know product updates, tips & discounts', 'stars-testimonials'); ?></div>
        <p><?php esc_html_e('Be among the first to know about our latest features & what we’re working on. Plus insider offer & flash sales', 'stars-testimonials'); ?></p>
        <div class="updates-form">
            <div class="update-form-input">
                <div class="mail-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0" mask-type="alpha" maskUnits="userSpaceOnUse" x="2" y="4" width="20" height="16">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM12 13L4 8V18H20V8L12 13Z" fill="white"/>
                        </mask>
                        <g mask="url(#mask0)">
                            <rect width="24" height="24" fill="#94A3B8"/>
                        </g>
                    </svg>
                </div>
                <input id="folder_update_status" autocomplete="off" value="<?php echo esc_attr($email) ?>" placeholder="Email address">
                <button href="javascript:;" class="button button-primary form-submit-btn yes befirst-btn"><?php esc_html_e('Sign Up', 'stars-testimonials'); ?></button>
                <p id="suggestion"></p>
            </div>
            <!--div class="update-form-skip-button">
                <button href="javascript:;" class="button button-secondary form-cancel-btn no">Skip</button>
            </div-->
        </div>
        <div class="update-notice-latter">
            <span><a href="javascript:;" class="form-cancel-btn no"><?php esc_html_e('No, I will do it later', 'stars-testimonials'); ?></a></span>
        </div>
        <div class="update-notice">
            <?php esc_html_e('You can remove yourself from the list whenever you want, no strings attached', 'stars-testimonials'); ?>
        </div>
    </div>
</div>
<div id="mystickyelement-update-email-overlay" class="stickyelement-overlay" style="display:block;" data-id="0" data-from="widget-status"></div>
<style>

    @font-face {
        font-family: 'Lato';
        src: url('<?php echo esc_url(WCP_FOLDER_URL."assets/fonts/Lato-Regular.woff");?>');
    }

    #wpwrap{
        background: url('<?php echo esc_url(WCP_FOLDER_URL."assets/images/update-bg.jpg") ?>');
        background-position: bottom center;
        background-size: cover;
    }

    .starts-testimonials-updates-form {
        width: 768px;
        padding: 0px 30px 0px 0px;
        box-shadow: 0px 20px 25px rgb(0 0 0 / 10%), 0px 10px 10px rgb(0 0 0 / 4%);
        display: flex;
        margin: 100px auto 0;
        font-family: Lato, sans-serif;
        border-radius: 18.42px 0px 0px 18.419px;
        background:#fff;
    }
    .update-title {
        font-style: normal;
        font-weight: 700;
        font-size: 30px;
        align-items: center;
        color: #334155;
        position: relative;
        line-height: 36px;
        font-family:Lato;
    }

    .update-title:after{
        content: '';
        background: #605DEC;
        width: 19%;
        height: 2px;
        position: absolute;
        bottom: -16px;
        left: 0;
    }

    .updates-form-form-left {
        padding: 25px 0px 50px 0px;
        background: linear-gradient(180deg, #3C139A 25.79%, #2A0D76 72.72%);
        border-radius: 14.42px 0px 0px 14.419px;
        text-align:center;
        width: 70%;
    }

    .updates-form-form-left p{
        font-size: 16px;
        line-height: 1.5;
        margin: 0;
        color: #fff;
        padding: 0px 36px;
        font-family:Lato;
    }

    .updates-form-form-left span{
        color: #fff;
        margin-left: 0px;
        margin-top: 0px;
        position: absolute;
        font-size: 20px;
        line-height: 0.5;
        font-family:Lato;
    }

    .updates-form-form-left-text{
        color: #fff;
        font-size: 20px;
        font-weight: 600;
        text-align: left;
        padding-left: 30px;
        font-family:Lato;
    }
    .updates-form-form-right{
        margin-left: 16px;
        padding: 70px 47px 70px 45px;
    }
    .updates-form-form-right p {
        font-size: 16px;
        font-style: normal;
        font-weight: normal;
        line-height: 150%;
        position: relative;
        color: #475569;
        margin: 32px 0px 20px 0px;
        font-family:Lato;

    }
    /*.updates-form-form-right p:after {
        content: "";
        border: 1px solid #3C85F7;
        position: absolute;
        bottom: 5px;
        left: 0;
        width: 90px;

    }*/
    .updates-form {
        /*display: flex;*/
    }
    .update-form-input {
        position: relative;
    }
    .update-form-input input {
        width: 385px;
        background: #F4F4F5;
        border: 1px solid #F4F4F5;
        box-sizing: border-box;
        border-radius: 8px;
        height: 42px;
        line-height: 40px;
        padding: 0 50px 0 40px;
        font-size: 13px;
        box-sizing: border-box;
        color: #334155;
    }
    .update-form-input .form-submit-btn {
        background: #5A00F0;
        border-radius: 8px;
        border: none;
        color: #fff;
        font-style: normal;
        font-weight: 500;
        font-size: 13px;
        line-height: 150%;
        height: 36px;
        padding: 0 10px;
        position: absolute;
        right: 3px;
        top: 3px;
        width: 100px;
        font-family:Lato;
    }
    .update-form-input #starts_testimonials_update_email:hover{
        border-bottom: 1px solid #5A00F0;
        border-radius: 8px 8px 0px 0px;
    }

    .update-form-input #starts_testimonials_update_email:focus-visible{
        border :1px solid #5A00F0 !important;
        outline: 1px !important;
        border-radius: 8px;
    }

    .update-form-input .form-submit-btn:hover{
        background: #5A00F0;
    }

    .updates-form .form-cancel-btn.no {
        margin: 0 0 0 3px;
        background: transparent;
        border: none;
        color: #64748B;
        font-size: 13px;
        line-height: 40px;
        padding: 0 0 0 5px;
    }

    .updates-form .form-cancel-btn.no:hover {
        color: #334155;
    }

    .mail-icon {
        position: absolute;
        top: 8px;
        left: 10px;
        z-index: 1;
    }

    .update-notice {
        margin: 50px 22px 0 0px;
        font-size: 16px;
        line-height: 150%;
        color: #28375A;
        text-align:center;
        font-family:Lato;
        padding: 0;
    }

    .update-notice-latter{
        margin: 20px 0px;
        width: 100%;
        text-align: center;
        color:#28375A;
    }

    .update-notice-latter span a{
        text-decoration: underline;
        cursor: pointer;
        color:#28375A;
    }
    #suggestion {
        margin: 10px 0 0;
        padding: 0;
        font-size: 14px;
        color: #970029;
    }
    #suggestion i {
        color: #2596be;
        font-weight: bold;
        cursor: pointer;
    }
    .eac-sugg{
        color:#c1c1c1;
        margin-left: -4px;
    }
    .wp-core-ui .button-primary:disabled, .wp-core-ui .button-primary[disabled] {
        background: #e7e7e7!important;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        jQuery(document).on("click", ".updates-form button, a.form-cancel-btn", function () {
            var updateStatus = 0;
            if (jQuery(this).hasClass("yes")) {
                updateStatus = 1;
            }
            $(".updates-form button").attr("disabled", true);
            $.ajax({
                url: ajaxurl,
                data: {
                    action: "folder_update_status",
                    status: updateStatus,
                    nonce: '<?php echo esc_attr(wp_create_nonce("folder_update_status")) ?>',
                    email: jQuery("#folder_update_status").val()
                },
                type: 'post',
                cache: false,
                success: function () {
                    window.location.reload();
                }
            })
        });

        jQuery("#folder_update_status").emailautocomplete({
            domains: ['example.com'],
            caseSensitive: false
        });

        jQuery(document).on("click", "#suggestion i", function (){
            $("#folder_update_status").val($(this).text()).focus();
            $("#suggestion").html('');
        });

        jQuery(document).on("change", "#folder_update_status", function (){
            isValidEmailAddress();
        });
        jQuery(document).on("keyup", "#folder_update_status", function (){
            if(isValidEmailAddress()) {
                jQuery(this).mailcheck({
                    suggested: function(element, suggestion) {
                        // callback code
                        jQuery('#suggestion').html("Did you mean <b><i>" + suggestion.full + "</b></i>?");
                    },
                    empty: function(element) {
                        // callback code
                        jQuery('#suggestion').html('');
                    }
                });
            } else {
                jQuery('#suggestion').html('');
            }
        });
    });
    function isValidEmailAddress() {
        if(jQuery.trim(jQuery("#folder_update_status").val()) == "") {
            jQuery(".form-submit-btn").prop("disabled", true);
            return false;
        } else if(!isValidEmail(jQuery("#folder_update_status").val())) {
            jQuery(".form-submit-btn").prop("disabled", true);
            return false;
        } else {
            jQuery(".form-submit-btn").prop("disabled", false);
        }
        return true;
    }

    function isValidEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }
</script>
