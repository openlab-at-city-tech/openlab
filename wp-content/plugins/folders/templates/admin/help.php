<?php
if (! defined('ABSPATH')) {
    exit;
}
$data = WCP_FOLDER_FOOTER_HELP_DATA;  
?>
 
<div class="premio-footer-help">
    <div class="premio-help-wrap">

        <div class="premio-help-menu">
 
            <?php foreach($data['footer_menu'] as $key => $value):  ?>
                <?php if($value['status'] == 1): ?>
                    <a target="_blank" href="<?php echo esc_url($value['link']) ?>"><?php echo esc_html($value['title']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?> 
        </div>
        <div class="premio-help-content">
            <p><?php esc_html_e("Powered by ", "folders") ?><a target="_blank" href="<?php echo esc_url($data['premio_site_info']) ?>"><?php esc_html_e("Premio", "folders") ?></a></p>
        </div>
    </div>
    <div class="premio-help-button-wrap">
    <!-- Free/Pro Only URL Change -->
        <a class="premio-help-button" href="#"><img src="<?php echo esc_url($data['help_icon']) ?>" alt="<?php esc_html_e("Need help?", 'folders'); ?>"  /></a>
        <a class="premio-help-close-btn" href="#"><img src="<?php echo esc_url($data['close_icon']) ?>" alt="<?php esc_html_e("Close", 'folders'); ?>"  /></a>
        
        <?php 
            $option = get_option("hide_folders_cta");
            if ($option !== "yes") { ?>
                <span class="tooltiptext"><?php esc_html_e("Support", "folders") ?></span>
        <?php  } ?> 
        <div class="premio-help-absulate-content">
            <?php foreach($data['support_widget'] as $key => $value): 
                $link = $value['link'] == false ? '#' : esc_url($value['link']);
                $class = $key == 'contact' ? 'contact-us-btn' : 'premio-click-to-close';
                $target = $key == 'contact' ? '' : '_blank';
                $pro_class = $key == 'upgrade_to_pro' ? ' pro' : '';
            ?>
                <a target="<?php echo esc_attr($target); ?>" href="<?php echo esc_attr( $link ) ?>" class="premio-help-absulate-content-single <?php echo esc_attr($class); ?>">
                    <span class="text"><?php  echo esc_html($value['title']) ?></span>
                    <span class="icon-img <?php echo esc_attr($pro_class); ?>"><img src="<?php echo esc_url($value['icon']) ?>" alt=""></span>
                </a>
            <?php endforeach; ?> 
        </div>

    </div>
    <div class="premio-help-form">
    
        <form action="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" method="post" id="premio-help-form">
            <div class="premio-help-header">
                <b>Gal Dubinski</b>  <?php esc_html_e("Co-Founder at Premio", "folders") ?>
            </div>
            <div class="premio-help-content">
                <p><?php esc_html_e("Hello! Are you experiencing any problems with Folders? Please let me know :)", 'folders'); ?></p>
                   <br>
                <div class="premio-form-field">
                    <input type="text" name="user_email" id="user_email" placeholder="<?php esc_html_e("Email", 'folders'); ?>">
                 
                </div>
                <div class="premio-form-field">
                    <textarea type="text" name="textarea_text" id="textarea_text" placeholder="<?php esc_html_e("How can I help you?", 'folders'); ?>"></textarea>
                </div>
                <div class="form-button">
                    <button type="submit" class="premio-help-button-submit" ><?php esc_html_e("Chat", 'folders') ?></button>
                    <input type="hidden" name="action" value="wcp_folder_send_message_to_owner"  >
                    <input type="hidden" id="nonce" name="nonce" value="<?php echo esc_attr(wp_create_nonce('wcp_folder_help_nonce')) ?>"  >
                </div>
            </div>
            <div class="help-form-footer">
                <p><?php esc_html_e("Or", 'folders'); ?></p>
                <p><a href="<?php echo esc_url($data['help_center_link']) ?>" target="_blank"><?php esc_html_e("Visit our Help Center >>", 'folders'); ?></a></p>
            </div>
        </form> 
        <div class="premio-form-response"></div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){

        // Prevent default behavior of anchor tags with href="#"
        jQuery("a[href='#']").click(function(e){ 
            e.preventDefault();
        });


        jQuery(".premio-help-button").click(function(e){
            e.stopPropagation();
             jQuery(".premio-help-button-wrap .tooltiptext").hide();
            jQuery(".premio-help-close-btn").addClass('show');
            jQuery(".premio-help-button").addClass('hide');
            jQuery(".premio-help-absulate-content").addClass('active');
            jQuery(".premio-help-absulate-content").removeClass('hide');
             
        });
        jQuery(".premio-help-close-btn").click(function(e){
            e.stopPropagation(); 
            jQuery(".premio-help-close-btn").removeClass('show');
            jQuery(".premio-help-button").removeClass('hide');
            jQuery(".premio-help-absulate-content").removeClass('active');
            jQuery(".premio-help-absulate-content").addClass('hide');
            jQuery(".premio-help-form").hide();
             
        });
        jQuery(".premio-click-to-close").click(function(e){ 
            jQuery(".premio-help-close-btn").removeClass('show');
            jQuery(".premio-help-button").removeClass('hide');
            jQuery(".premio-help-absulate-content").removeClass('active');
            jQuery(".premio-help-absulate-content").addClass('hide'); 
             
        });
        jQuery("#premio-help-form").submit(function(){
            jQuery(".premio-help-button-submit").attr("disabled",true);
            jQuery(".premio-help-button-submit").text("<?php esc_html_e("Sending Request...", "folders") ?>");
            formData = jQuery(this).serialize();
            jQuery.ajax({
                url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                data: formData,
                type: "post",
                success: function(responseArray){
                    jQuery("#premio-help-form").find(".error-message").remove();
                    jQuery("#premio-help-form").find(".input-error").removeClass("input-error");
                    if(responseArray.error == 1) {
                        jQuery(".premio-help-button-submit").attr("disabled",false);
                        jQuery(".premio-help-button-submit").text("<?php esc_html_e("Chat", 'folders'); ?>");
                        for(i=0;i<responseArray.errors.length;i++) {
                            jQuery("#"+responseArray.errors[i]['key']).addClass("input-error");
                            jQuery("#"+responseArray.errors[i]['key']).after('<span class="error-message">'+responseArray.errors[i]['message']+'</span>');
                        }
                    } else if(responseArray.status == 1) {
                        jQuery(".premio-help-button-submit").text("<?php esc_html_e("Done!", 'folders'); ?>");
                        setTimeout(function(){
                            jQuery("#user_email").val("");
                            jQuery("#textarea_text").val("");
                            jQuery("#premio-help-form").hide();
                            jQuery(".premio-help-header").hide();
                            jQuery(".help-form-footer").hide();
                            jQuery(".premio-form-response").html("<p class='success-p'><?php esc_html_e("Your message is sent successfully.", 'folders'); ?></p>");
                        },1000);
                    } else if(responseArray.status == 0) {
                        jQuery("#premio-help-form").hide();
                        jQuery(".premio-help-header").hide();
                        jQuery(".help-form-footer").hide();
                        jQuery(".premio-form-response").html("<p class='error-p'><?php printf(esc_html__("There is some problem in sending request. Please send us mail on %1\$s", 'folders'), "<a href='mailto:contact@premio.io'>contact@premio.io</a>"); ?></p>");
                    }
                }
            });
            return false;
        });
        jQuery(".contact-us-btn").click(function(e){
            e.stopPropagation(); 
            jQuery(".premio-help-form").show(); 
            jQuery(".premio-help-form").addClass('active');  
            jQuery(".premio-help-absulate-content").removeClass('active');
            jQuery(".premio-help-absulate-content").addClass('hide');
            if(jQuery(".premio-help-button-wrap .tooltiptext").length) {
                jQuery(".premio-help-button-wrap .tooltiptext").remove();
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
      
        jQuery(".premio-help-form").click(function(e){
            e.stopPropagation();
        });
        jQuery("body").click(function(){
            if(jQuery(".premio-help-form").hasClass("active")) { 
                jQuery(".premio-help-button").addClass('show'); 
                jQuery(".premio-help-button").removeClass('hide'); 
                
                jQuery(".premio-help-close-btn").addClass('hide');  
                jQuery(".premio-help-close-btn").removeClass('show'); 
            }
            
            jQuery(".premio-help-form").removeClass("active");
        });
    });
</script>
