<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$recommended_plugin = get_option( 'hide_folder_recommended_plugin' );
global $current_user;
$email_id = isset($current_user->user_email) ? $current_user->user_email : '';
$upgradeURL = \Folders\Admin\License::get_pro_url();
?>
<div class="folders-footer-menu">
    <div class="folders-footer-inner">
        <div class="folders-footer-links">
            <a href="https://wordpress.org/support/plugin/folders/" target="_blank" class="folders-footer-link"><?php esc_html_e('Get Support', 'folders'); ?></a>
            <?php if ( false === $recommended_plugin ) { ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=recommended-folder-plugins')) ?>" class="folders-footer-link"><?php esc_html_e('Recommended Plugins', 'folders'); ?></a>
            <?php } ?>
            <a href="#" class="folders-footer-link folders-help-button"><?php esc_html_e('Need help?', 'folders'); ?></a>
        </div>
        <div class="folders-footer-powered">
            <div class="folders-footer-powered-text"><?php esc_html_e('Powered by', 'folders'); ?></div>
            <a href="https://premio.io" target="_blank" class="folders-footer-link"><?php esc_html_e('Premio', 'folders'); ?></a>
        </div>
    </div>
</div>
<div class="help-fixed-menu">
    <div class="folders-help-menu folders-help-popup folders-contact-popup">
        <div class="folders-contact-card">
            <form action="" method="post" id="folders-contact-form" >
                <div class="folders-contact-header">
                    <div class="folders-contact-title"><?php esc_html_e('Contact Us', 'folders'); ?></div>
                </div>
                <div class="folders-contact-intro">
                    <div class="folders-contact-desc"><?php esc_html_e('Are you experiencing any issues with Folders? Please let us know, we’d be happy to help 🙏', 'folders'); ?></div>
                </div>
                <div class="folders-contact-divider"></div>
                <div class="folders-contact-body">
                    <div class="folders-contact-field">
                        <label class="screen-reader-text" for="contact-form-email"><?php esc_html_e('Email', 'folders'); ?></label>
                        <input type="text" name="email" data-field="email" id="contact-form-email" value="<?php echo esc_attr($email_id) ?>" placeholder="<?php esc_html_e('Email', 'folders'); ?>" class="folders-contact-input is-required is-email">
                    </div>
                    <div class="folders-contact-field">
                        <label class="screen-reader-text" for="contact-form-message"><?php esc_html_e('Message', 'folders'); ?></label>
                        <textarea type="text" name="message" data-field="message" id="contact-form-message" placeholder="<?php esc_html_e('How can I help you?', 'folders'); ?>" class="folders-contact-input folders-contact-textarea is-required"></textarea>
                    </div>
                    <div class="folders-contact-field">
                        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('folders-contact-form'); ?>">
                        <button type="submit" class="form-button folders-contact-submit folders-contact-form-button" data-loading="<?php esc_html_e('Sending Message', 'folders'); ?>" data-text="<?php esc_html_e('Chat', 'folders'); ?>">
                            <?php esc_html_e('Chat', 'folders'); ?>
                            <span class="folders-loader"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="folders-help-menu folders-help-popup folders-help-actions">
        <a href="<?php echo esc_url($upgradeURL)?> " target="_blank" class="folders-help-action folder-upgrade-icon">
            <span class="help-button-cta"><?php esc_html_e('Upgrade to Pro', 'folders'); ?></span>
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M29.8376 9.18729C29.5516 8.94852 29.2042 8.79496 28.8351 8.74412C28.466 8.69329 28.0901 8.74723 27.7501 8.89979L21.4251 11.7123L17.7501 5.08729C17.5745 4.77799 17.32 4.52077 17.0126 4.34182C16.7052 4.16288 16.3558 4.0686 16.0001 4.0686C15.6444 4.0686 15.2951 4.16288 14.9877 4.34182C14.6803 4.52077 14.4258 4.77799 14.2501 5.08729L10.5751 11.7123L4.25013 8.89979C3.90952 8.74746 3.53308 8.69344 3.16337 8.74386C2.79366 8.79427 2.44542 8.94711 2.15803 9.18508C1.87064 9.42306 1.65555 9.73669 1.53708 10.0905C1.41861 10.4443 1.40148 10.8242 1.48763 11.1873L4.66263 24.7248C4.72334 24.9869 4.83663 25.2339 4.99563 25.4509C5.15463 25.6679 5.35603 25.8504 5.58763 25.9873C5.90119 26.175 6.25969 26.2743 6.62513 26.2748C6.80278 26.2745 6.9795 26.2492 7.15013 26.1998C12.9374 24.5998 19.0504 24.5998 24.8376 26.1998C25.3661 26.3387 25.928 26.2623 26.4001 25.9873C26.6332 25.8522 26.8356 25.6702 26.9949 25.4529C27.1541 25.2356 27.2665 24.9877 27.3251 24.7248L30.5126 11.1873C30.5978 10.8241 30.5797 10.4444 30.4605 10.091C30.3412 9.73757 30.1255 9.42455 29.8376 9.18729Z" fill="currentColor"/>
            </svg>
        </a>
        <a href="https://wordpress.org/support/plugin/folders/" target="_blank" class="folders-help-action">
            <span class="help-button-cta"><?php esc_html_e('Contact Support', 'folders'); ?></span>
            <img src="<?php echo esc_url( FOLDERS_IMAGE_URL ); ?>footer/help-circle.svg" class="folders-help-action-icon" alt="Knowledge Base">
        </a>
        <button type="button" class="folders-help-action folders-help-button">
            <span class="help-button-cta"><?php esc_html_e('Contact Us', 'folders'); ?></span>
            <img src="<?php echo esc_url( FOLDERS_IMAGE_URL ); ?>footer/head-phone.svg" class="folders-help-action-icon" alt="Contact Support">
        </button>
    </div>
    <div class="folders-help-toggle">
        <button type="button" class="folders-help-toggle-btn folders-help-toggle-open folders-menu-button">
            <img src="<?php echo esc_url( FOLDERS_IMAGE_URL ); ?>footer/help-icon.svg" class="folders-help-action-icon" alt="Tooltip">
        </button>
        <button type="button" class="folders-help-toggle-btn folders-help-toggle-close folders-menu-button">
            <img src="<?php echo esc_url( FOLDERS_IMAGE_URL ); ?>footer/close.svg" class="folders-help-action-icon" alt="Tooltip">
        </button>
    </div>
</div>
