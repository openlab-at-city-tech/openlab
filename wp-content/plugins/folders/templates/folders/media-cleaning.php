<?php
$upgradeURL = \Folders\Admin\License::get_pro_url();
?>
<div class="media-clean-box-content">
    <div class="media-clean-box-title">
        <?php esc_html_e("Scan for unused media", 'folders') ?>
    </div>
    <div class="media-clean-box-border"></div>
    <div class="scan-steps scan-step-1">
        <div class="m-top">
            <script src="<?php echo esc_url(FOLDERS_PLUGIN_URL) ?>assets/js/lottie-player.js"></script>
            <lottie-player
                src="<?php echo esc_url(FOLDERS_PLUGIN_URL) ?>assets/js/lottie-player.json"
                background="transparent" speed="1"
                style="width: 300px; height: 300px; margin: 0 auto" loop autoplay>
            </lottie-player>
        </div>
        <div class="media-clean-box-desc">
            <?php esc_html_e("Find unused media files which aren't used in your website. An internal trash allows you to make sure everything works properly before deleting the media entries (and files) permanently.", 'folders') ?>
        </div>
        <div class="m-bottom">
            <a href="<?php echo esc_url($upgradeURL) ?>" target="_blank" class="media-clean-box-button">
                <img class="w-5 h-5" src="<?php echo esc_url( FOLDERS_IMAGE_URL . 'settings/pro-crown.svg' ); ?>" alt="">
                <?php esc_html_e("Upgrade to Pro", 'folders') ?>
            </a>
        </div>
        <a class="hide-media-clean-page" href="<?php echo esc_url(admin_url("upload.php?hide_menu=scan-files&nonce=".wp_create_nonce("folders-scan-files"))) ?>"><?php esc_html_e("Hide this page from the menu", "folders"); ?></a>
    </div>
</div>
