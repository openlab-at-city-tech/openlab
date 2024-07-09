<?php
/**
 * Admin folders help
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}
?>
<style>
    .folder-help-btn,.folder-help-form{position:fixed;z-index:1001;display:none}.folders-help .folder-help-btn{display:block!important}.folder-help-btn{right:20px;bottom:20px}.folder-help-btn a{display:block;border:3px solid #fff;width:50px;height:50px;-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%;position:relative}.folder-help-btn a img{width:100%;height:auto;display:block;-webkit-border-radius:50%;-moz-border-radius:50%;border-radius:50%}.folder-help-form{right:85px;border:1px solid #e9edf0;bottom:25px;background:#fff;-webkit-border-radius:10px;-moz-border-radius:10px;border-radius:10px;width:320px;direction:ltr;opacity:0;transition:.4s;-webkit-transition:.4s;-moz-transition:.4s}.folder-help-form.active{opacity:1;pointer-events:inherit;display:block}.folder-help-header{background:#f4f4f4;border-bottom:1px solid #e9edf0;padding:5px 20px;-webkit-border-radius:10px;-moz-border-radius:10px;border-radius:10px 10px 0 0;font-size:16px;text-align:right}.folder-help-header b{float:left}.folder-help-content{margin-bottom:10px;padding:20px 20px 10px}.folder-help-form p{margin:0 0 1em}.folder-form-field{margin-bottom:10px}.folder-form-field input,.folder-form-field textarea{-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;padding:5px;width:100%;box-sizing:border-box;border:1px solid #c5c5c5}.folder-form-field textarea{width:100%;height:100px;margin-bottom:0}.folder-help-button{border:none;padding:8px 0;width:100%;background:#ff6624;color:#fff;border-radius:18px;cursor:pointer}.folder-help-form .error-message{font-weight:400;font-size:14px;display:block}.folder-help-form input.input-error,.folder-help-form textarea.input-error{border-color:#dc3232}.folder-help-btn span.tooltiptext{position:absolute;background:#000;font-size:12px;color:#fff;top:-35px;width:140%;text-align:center;left:-20%;border-radius:5px;direction:ltr}p.error-p,p.success-p{margin:0;font-size:14px;text-align:center}.folder-help-btn span.tooltiptext:after{bottom:-20px;content:"";transform:translateX(-50%);height:10px;width:0;border-width:10px 5px 0;border-style:solid;border-color:#000 transparent transparent;left:50%;position:absolute}p.success-p{color:green}p.error-p{color:#dc3232}html[dir=rtl] .folder-help-btn{left:20px;right:auto}html[dir=rtl] .folder-help-form{left:85px;right:auto}.folder-popup-body h3{line-height:24px}.folder-popup-overlay .form-control input{width:100%;margin:0 0 15px}body.plugins-php .tooltiptext{display:none}.help-form-footer{text-align:center}.help-form-footer p{margin:0;padding:0}.help-form-footer p+p{margin:0;padding:10px 0}
</style>
<div class="folder-help-form">
    <form action="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" method="post" id="folder-help-form">
        <div class="folder-help-header">
            <b>Gal Dubinski</b> Co-Founder at Premio
        </div>
        <div class="folder-help-content">
            <p><?php esc_html_e("Hello! Are you experiencing any problems with Folders? Please let me know :)", 'folders'); ?></p>
            <div class="folder-form-field">
                <input type="text" name="user_email" id="user_email" placeholder="<?php esc_html_e("Email", 'folders'); ?>">
            </div>
            <div class="folder-form-field">
                <textarea type="text" name="textarea_text" id="textarea_text" placeholder="<?php esc_html_e("How can I help you?", 'folders'); ?>"></textarea>
            </div>
            <div class="form-button">
                <button type="submit" class="folder-help-button" ><?php esc_html_e("Chat") ?></button>
                <input type="hidden" name="action" value="wcp_folder_send_message_to_owner"  >
                <input type="hidden" id="folder_help_nonce" name="folder_help_nonce" value="<?php echo esc_attr(wp_create_nonce('wcp_folder_help_nonce')) ?>"  >
            </div>
        </div>
        <div class="help-form-footer">
            <p>Or</p>
            <p><a href="https://premio.io/help/folders/?utm_source=pluginspage" target="_blank"><?php esc_html_e("Visit our Help Center >>", 'folders'); ?></a></p>
        </div>
    </form>
</div>
<div class="folder-help-btn">
    <!-- Free/Pro Only URL Change -->
    <a class="folder-help-tooltip" href="javascript:;"><img src="<?php echo esc_url(WCP_FOLDER_URL."assets/images/owner.jpg") ?>" alt="<?php esc_html_e("Need help?", 'folders'); ?>"  /></a>
    <?php
    $option = get_option("hide_folders_cta");
    if ($option !== "yes") { ?>
        <span class="tooltiptext">Need help?</span>
    <?php } ?>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery("#folder-help-form").submit(function(){
            jQuery(".folder-help-button").attr("disabled",true);
            jQuery(".folder-help-button").text("<?php esc_html_e("Sending Request...") ?>");
            formData = jQuery(this).serialize();
            jQuery.ajax({
                url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                data: formData,
                type: "post",
                success: function(responseText){
                    jQuery("#folder-help-form").find(".error-message").remove();
                    jQuery("#folder-help-form").find(".input-error").removeClass("input-error");
                    responseText = responseText.slice(0, - 1);
                    responseArray = jQuery.parseJSON(responseText);
                    if(responseArray.error == 1) {
                        jQuery(".folder-help-button").attr("disabled",false);
                        jQuery(".folder-help-button").text("<?php esc_html_e("Chat", 'folders'); ?>");
                        for(i=0;i<responseArray.errors.length;i++) {
                            jQuery("#"+responseArray.errors[i]['key']).addClass("input-error");
                            jQuery("#"+responseArray.errors[i]['key']).after('<span class="error-message">'+responseArray.errors[i]['message']+'</span>');
                        }
                    } else if(responseArray.status == 1) {
                        jQuery(".folder-help-button").text("<?php esc_html_e("Done!", 'folders'); ?>");
                        setTimeout(function(){
                            jQuery(".folder-help-header").remove();
                            jQuery(".help-form-footer").remove();
                            jQuery(".folder-help-content").html("<p class='success-p'><?php esc_html_e("Your message is sent successfully.", 'folders'); ?></p>");
                        },1000);
                    } else if(responseArray.status == 0) {
                        jQuery(".folder-help-content").html("<p class='error-p'><?php printf(esc_html__("There is some problem in sending request. Please send us mail on %1\$s", 'folders'), "<a href='mailto:contact@premio.io'>contact@premio.io</a>"); ?></p>");
                    }
                }
            });
            return false;
        });
        jQuery(".folder-help-tooltip").click(function(e){
            e.stopPropagation();
            jQuery(".folder-help-btn").toggle();
            jQuery(".folder-help-form").toggleClass("active");
            if(jQuery(".folder-help-btn .tooltiptext").length) {
                jQuery(".folder-help-btn .tooltiptext").remove();
                jQuery.ajax({
                    url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                    data: {
                        nonce: "<?php echo esc_attr(wp_create_nonce("hide_folders_cta")) ?>",
                        action: "hide_folders_cta"
                    },
                    type: "post",
                    success: function (responseText) {

                    }
                });
            }

        });
        jQuery(".folder-help-form").click(function(e){
            e.stopPropagation();
        });
        jQuery("body").click(function(){
            jQuery(".folder-help-form").removeClass("active");
            if(jQuery(".folder-help-form").hasClass("active")) {
                jQuery(".folder-help-btn").show();
            } else {
                jQuery(".folder-help-btn").hide();
            }
        });
    });
</script>
