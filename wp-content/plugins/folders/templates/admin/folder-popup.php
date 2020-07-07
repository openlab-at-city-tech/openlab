<?php if(!defined('ABSPATH')) exit; ?>
<div class="folder-popup" id="folder-intro-popup">
    <div class="folder-popup-box">
        <div class="folder-popup-header">
            Welcome to Folders &#127881;
            <button class="close-folder-popup"><span class="dashicons dashicons-no-alt"></span></button>
            <div class="clear"></div>
        </div>
        <div class="folder-popup-content">
            Select the places where you want Folders to appear (Media Library, Posts, Pages, Custom Posts). Need help? Visit our <a target="_blank" href="https://premio.io/help/folders/?utm_soruce=wordpressfolders">Help Center</a>.
            <iframe width="420" height="240" src="https://www.youtube.com/embed/RqkWGRYek0w?rel=0&start=22"></iframe>
        </div>
        <div class="folder-popup-footer">
            <button type="button">Go to Folders</button>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery(document).on("click", ".folder-popup-box button, #folder-intro-popup", function(e){
            e.stopPropagation();
            var nonceVal = "<?php echo wp_create_nonce("folder_update_popup_status") ?>";
            jQuery("#folder-intro-popup").remove();
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'folder_update_popup_status',
                    nonce: nonceVal
                },
                beforeSend: function (xhr) {

                },
                success: function (res) {

                },
                error: function (xhr, status, error) {

                }
            });
        });

        jQuery(document).on("click", ".folder-popup-box", function(e){
            e.stopPropagation();
        });
    });
</script>