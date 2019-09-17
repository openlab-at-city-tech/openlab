<?php
defined('ABSPATH') or die('Nope, not accessing this');
?>
<div class="folder-help-form">
    <form action="<?php echo admin_url( 'admin-ajax.php' ) ?>" method="post" id="folder-help-form">
        <div class="folder-help-header">
            <b>Gal Dubinski</b> Co-Founder at Premio
        </div>
        <div class="folder-help-content">
            <p><?php echo __("Hello! Are you experiencing any problems with Folders? Please let me know :)", WCP_FOLDER) ?></p>
            <div class="folder-form-field">
                <input type="text" name="user_email" id="user_email" placeholder="<?php echo __("Email", WCP_FOLDER) ?>">
            </div>
            <div class="folder-form-field">
                <textarea type="text" name="textarea_text" id="textarea_text" placeholder="<?php echo __("How can I help you?", WCP_FOLDER) ?>"></textarea>
            </div>
            <div class="form-button">
                <button type="submit" class="folder-help-button" ><?php echo __("Chat") ?></button>
                <input type="hidden" name="action" value="wcp_folder_send_message_to_owner"  >
                <input type="hidden" id="folder_help_nonce" name="folder_help_nonce" value="<?php echo wp_create_nonce('wcp_folder_help_nonce') ?>"  >
            </div>
        </div>
    </form>
</div>
<div class="folder-help-btn">
    <a class="folder-help-tooltip" href="javascript:;"><img src="<?php echo WCP_FOLDER_URL ?>assets/images/owner.png" alt="<?php echo __("Need help?", WCP_FOLDER) ?>"  /></a>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery("#folder-help-form").submit(function(){
            jQuery(".folder-help-button").attr("disabled",true);
            jQuery(".folder-help-button").text("<?php echo __("Sending Request...") ?>");
            formData = jQuery(this).serialize();
            jQuery.ajax({
                url: "<?php echo admin_url( 'admin-ajax.php' ) ?>",
                data: formData,
                type: "post",
                success: function(responseText){
                    jQuery("#folder-help-form").find(".error-message").remove();
                    jQuery("#folder-help-form").find(".input-error").removeClass("input-error");
                    responseText = responseText.slice(0, - 1);
                    responseArray = jQuery.parseJSON(responseText);
                    if(responseArray.error == 1) {
                        jQuery(".folder-help-button").attr("disabled",false);
                        jQuery(".folder-help-button").text("<?php echo __("Chat", WCP_FOLDER) ?>");
                        for(i=0;i<responseArray.errors.length;i++) {
                            jQuery("#"+responseArray.errors[i]['key']).addClass("input-error");
                            jQuery("#"+responseArray.errors[i]['key']).after('<span class="error-message">'+responseArray.errors[i]['message']+'</span>');
                        }
                    } else if(responseArray.status == 1) {
                        jQuery(".folder-help-button").text("<?php echo __("Done!", WCP_FOLDER) ?>");
                        setTimeout(function(){
                            jQuery(".folder-help-header").remove();
                            jQuery(".folder-help-content").html("<p class='success-p'><?php echo __("Your message is sent successfully.", WCP_FOLDER) ?></p>");
                        },1000);
                    } else if(responseArray.status == 0) {
                        jQuery(".folder-help-content").html("<p class='error-p'><?php echo __("There is some problem in sending request. Please send us mail on <a href='mailto:contact@premio.io'>contact@premio.io</a>", WCP_FOLDER) ?></p>");
                    }
                }
            });
            return false;
        });
        jQuery(".folder-help-tooltip").click(function(e){
            e.stopPropagation();
            jQuery(".folder-help-btn").toggle();
            jQuery(".folder-help-form").toggleClass("active");

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