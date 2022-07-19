
<div class="media-clean-box-content">
    <div class="media-clean-box-title">
        <?php esc_html_e("Scan for unused media","folders") ?>
    </div>
    <div class="media-clean-box-border"></div>
    <div class="m-top">
        <?php wp_enqueue_script("folders-lottie-player", WCP_FOLDER_URL."assets/js/lottie-player.js") ?>
        <lottie-player src="<?php echo WCP_FOLDER_URL."assets/js/lottie-player.json" ?>" background="transparent"  speed="1"  style="width: 300px; height: 300px; margin: 0 auto"  loop autoplay></lottie-player>
    </div>
    <div class="media-clean-box-desc">
        <?php esc_html_e("Find unused media files which aren't used in your website. An internal trash allows you to make sure everything works properly before deleting the media entries (and files) permanently.","folders") ?>
    </div>
    <div class="m-bottom">
        <a href="<?php echo esc_url($this->getFoldersUpgradeURL()) ?>" class="media-clean-box-button">
            <?php esc_html_e("Upgrade to Folders Pro","folders") ?>
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18C14.4183 18 18 14.4183 18 10C18 5.58172 14.4183 2 10 2C5.58172 2 2 5.58172 2 10C2 14.4183 5.58172 18 10 18ZM9.5547 7.16795C9.24784 6.96338 8.8533 6.94431 8.52814 7.11833C8.20298 7.29235 8 7.63121 8 8V12C8 12.3688 8.20298 12.7077 8.52814 12.8817C8.8533 13.0557 9.24784 13.0366 9.5547 12.8321L12.5547 10.8321C12.8329 10.6466 13 10.3344 13 10C13 9.66565 12.8329 9.35342 12.5547 9.16795L9.5547 7.16795Z" />
            </svg>
        </a>
    </div>
    <div class="skip-scan-btn">
        <a href="<?php echo admin_url("upload.php?hide_menu=scan-files&nonce=".wp_create_nonce("folders-scan-files")) ?>"><?php esc_html_e("Hide this page from the menu", "folders"); ?></a>
    </div>
</div>