<?php
defined('ABSPATH') || die;

$email_settings    = get_option('advgb_email_sender');
$contactform_saved = get_option('advgb_contacts_saved');
$contacts_count    = $contactform_saved ? count($contactform_saved) : 0;
$newsletter_saved  = get_option('advgb_newsletter_saved');
$newsletter_count  = $newsletter_saved ? count($newsletter_saved) : 0;
$recaptcha_config  = get_option('advgb_recaptcha_config');
$website_title     = get_option('blogname');
$admin_email       = get_option('admin_email');

$contact_form_sender_name     = isset($email_settings['contact_form_sender_name']) && $email_settings['contact_form_sender_name'] ? $email_settings['contact_form_sender_name'] : $website_title;
$contact_form_sender_email    = isset($email_settings['contact_form_sender_email']) && $email_settings['contact_form_sender_email'] ? $email_settings['contact_form_sender_email'] : $admin_email;
$contact_form_email_title     = isset($email_settings['contact_form_email_title']) && $email_settings['contact_form_email_title'] ? $email_settings['contact_form_email_title'] : __('Website Contact', 'advanced-gutenberg');
$contact_form_email_receiver  = isset($email_settings['contact_form_email_receiver']) && $email_settings['contact_form_email_receiver'] ? $email_settings['contact_form_email_receiver'] : $admin_email;

$recaptcha_enabled    = isset($recaptcha_config['recaptcha_enable']) && $recaptcha_config['recaptcha_enable'] ? 'checked' : '';
$recaptcha_site_key   = isset($recaptcha_config['recaptcha_site_key']) ? $recaptcha_config['recaptcha_site_key'] : '';
$recaptcha_secret_key = isset($recaptcha_config['recaptcha_secret_key']) ? $recaptcha_config['recaptcha_secret_key'] : '';
$recaptcha_language   = isset($recaptcha_config['recaptcha_language']) ? $recaptcha_config['recaptcha_language'] : '';
$recaptcha_theme      = isset($recaptcha_config['recaptcha_theme']) ? $recaptcha_config['recaptcha_theme'] : '';
?>

<div id="advgb-settings-container">
    <div class="ju-top-tabs-wrapper">
        <ul class="tabs ju-top-tabs">
            <li class="tab">
                <a href="#email-tab" class="link-tab">
                    <?php esc_html_e('Email settings', 'advanced-gutenberg') ?>
                </a>
            </li>
            <li class="tab">
                <a href="#captcha-tab" class="link-tab">
                    <?php esc_html_e('Forms reCaptcha', 'advanced-gutenberg') ?>
                </a>
            </li>
            <li class="tab">
                <a href="#export-block-data" class="link-tab">
                    <?php esc_html_e('Forms data', 'advanced-gutenberg') ?>
                </a>
            </li>
        </ul>
    </div>

    <?php if (isset($_GET['save_settings'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action ?>
        <div class="ju-notice-msg ju-notice-success">
            <?php esc_html_e('Settings saved successfully', 'advanced-gutenberg'); ?>
            <i class="dashicons dashicons-dismiss ju-notice-close"></i>
        </div>
    <?php endif; ?>

    <h1 class="advgb-settings-header"><?php esc_html_e('Email and Form settings', 'advanced-gutenberg') ?></h1>

    <div class="tab-content clearfix" id="email-tab">
        <form method="POST">
            <?php wp_nonce_field('advgb_email_config_nonce', 'advgb_email_config_nonce_field') ?>
            <ul class="settings-list clearfix">
                <li class="ju-settings-option settings-separator">
                    <h3 class="settings-separator-title">
                        <?php esc_html_e('Contact Form: Sender Information', 'advanced-gutenberg') ?>
                    </h3>
                </li>

                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="contact_form_sender_name"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('Sender name', 'advanced-gutenberg') ?>
                        </label>
                        <div>
                            <input type="text"
                                   name="contact_form_sender_name"
                                   id="contact_form_sender_name"
                                   class="ju-input full-width"
                                   value="<?php echo esc_html($contact_form_sender_name) ?>"/>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="contact_form_sender_email"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('Sender email', 'advanced-gutenberg') ?>
                        </label>
                        <div>
                            <input type="email"
                                   name="contact_form_sender_email"
                                   id="contact_form_sender_email"
                                   class="ju-input full-width"
                                   value="<?php echo esc_html($contact_form_sender_email) ?>"/>
                        </div>
                    </div>
                </li>

                <li class="ju-settings-option settings-separator">
                    <h3 class="settings-separator-title">
                        <?php esc_html_e('Contact Form: Receiver Information', 'advanced-gutenberg') ?>
                    </h3>
                </li>

                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="contact_form_email_title"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('Email title', 'advanced-gutenberg') ?>
                        </label>
                        <div>
                            <input type="text"
                                   name="contact_form_email_title"
                                   id="contact_form_email_title"
                                   class="ju-input full-width"
                                   value="<?php echo esc_html($contact_form_email_title) ?>"/>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="contact_form_email_receiver"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('Email receiver', 'advanced-gutenberg') ?>
                            <small>(<?php esc_html_e('separate by comma', 'advanced-gutenberg') ?>)</small>
                        </label>
                        <div>
                            <input type="email"
                                   name="contact_form_email_receiver"
                                   id="contact_form_email_receiver"
                                   class="ju-input full-width"
                                   value="<?php echo esc_html($contact_form_email_receiver) ?>"/>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="save-settings-block">
                <button type="submit"
                        class="button button-primary pp-primary-button"
                        id="save_email_config"
                        name="save_email_config"
                >
                    <span><?php esc_html_e('Save', 'advanced-gutenberg') ?></span>
                </button>
            </div>
        </form>
    </div>

    <div id="captcha-tab" class="tab-content clearfix">
        <form method="POST">
            <?php wp_nonce_field('advgb_captcha_nonce', 'advgb_captcha_nonce_field') ?>
            <ul class="settings-list clearfix">
                <li class="ju-settings-option full-width clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="recaptcha_enable"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('Enable reCAPTCHA', 'advanced-gutenberg') ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox"
                                       name="recaptcha_enable"
                                       id="recaptcha_enable"
                                       value="1"
                                    <?php echo esc_attr($recaptcha_enabled) ?>
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option full-width clearfix" style="background-color: transparent">
                    <div class="advgb-captcha-intro">
                        <?php esc_html_e('Use the Google reCaptcha to avoid spam in PublishPress Blocks forms. Get credentials for your domain by registering', 'advanced-gutenberg') ?>
                        <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">
                            <?php esc_html_e(' here', 'advanced-gutenberg') ?>
                        </a>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="recaptcha_site_key"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('reCAPTCHA Site Key', 'advanced-gutenberg') ?>
                            <span class="advgb-field-required">(<?php esc_html_e('required', 'advanced-gutenberg') ?>)</span>
                        </label>
                        <div>
                            <input type="text"
                                   name="recaptcha_site_key"
                                   id="recaptcha_site_key"
                                   class="ju-input full-width"
                                   value="<?php echo esc_html($recaptcha_site_key) ?>"/>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="recaptcha_secret_key"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('reCAPTCHA Secret Key', 'advanced-gutenberg') ?>
                            <span class="advgb-field-required">(<?php esc_html_e('required', 'advanced-gutenberg') ?>)</span>
                        </label>
                        <div>
                            <input type="text"
                                   name="recaptcha_secret_key"
                                   id="recaptcha_secret_key"
                                   class="ju-input full-width"
                                   value="<?php echo esc_html($recaptcha_secret_key) ?>"/>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="recaptcha_language"
                               class="ju-setting-label"
                        >
                            <?php esc_html_e('reCAPTCHA Language', 'advanced-gutenberg') ?>
                            (<a target="_blank" href="https://developers.google.com/recaptcha/docs/language"><?php esc_html_e('List', 'advanced-gutenberg') ?></a>)
                        </label>
                        <div>
                            <input type="text"
                                   name="recaptcha_language"
                                   id="recaptcha_language"
                                   class="ju-input full-width"
                                   placeholder="<?php esc_html_e('Auto detect', 'advanced-gutenberg'); ?>"
                                   value="<?php echo esc_html($recaptcha_language) ?>"/>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper no-child-float clearfix">
                        <label for="recaptcha_theme"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'We strongly recommend not using Invisible reCAPTCHA if you have more than 1 Newsletter or Contact forms block in a page.',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('reCAPTCHA Theme', 'advanced-gutenberg') ?>
                        </label>
                        <div>
                            <select class="ju-select full-width" name="recaptcha_theme" id="recaptcha_theme">
                                <option value="light" <?php echo $recaptcha_theme === 'light' ? 'selected' : '' ?>>Light</option>
                                <option value="dark" <?php echo $recaptcha_theme === 'dark' ? 'selected' : '' ?>>Dark</option>
                                <option value="invisible" <?php echo $recaptcha_theme === 'invisible' ? 'selected' : '' ?>>Invisible</option>
                            </select>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="save-settings-block">
                <button type="submit"
                        class="button button-primary pp-primary-button"
                        id="save_recaptcha_config"
                        name="save_recaptcha_config"
                >
                    <span><?php esc_html_e('Save', 'advanced-gutenberg') ?></span>
                </button>
            </div>
        </form>
    </div>

    <div id="export-block-data" class="tab-content clearfix">
        <form method="POST" id="export-block-data-form">
            <?php wp_nonce_field('advgb_export_data_nonce', 'advgb_export_data_nonce_field') ?>
            <ul class="advgb-export-field">
                <li class="advgb-export-item ju-settings-option full-width clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label class="advgb-export-data-title ju-setting-label">
                            <?php esc_html_e('Download Contacts Form data', 'advanced-gutenberg'); ?>
                            <?php echo ' ('. esc_html($contacts_count) . ')'; ?>
                        </label>
                        <div class="advgb-export-actions">
                            <button type="submit" class="button pp-default-button advgb-export-download"
                                    name="block_data_export" value="contact_form.csv"
                            >
                                <?php esc_html_e('CSV', 'advanced-gutenberg'); ?>
                            </button>
                            <button type="submit" class="button pp-default-button advgb-export-download"
                                    name="block_data_export" value="contact_form.json"
                            >
                                <?php esc_html_e('JSON', 'advanced-gutenberg'); ?>
                            </button>
                        </div>
                    </div>
                </li>
                <li class="advgb-export-item ju-settings-option full-width clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label class="advgb-export-data-title ju-setting-label">
                            <?php esc_html_e('Download Newsletter Form data', 'advanced-gutenberg'); ?>
                            <?php echo ' ('. esc_html($newsletter_count) . ')'; ?>
                        </label>
                        <div class="advgb-export-actions">
                            <button type="submit" class="button pp-default-button advgb-export-download"
                                    name="block_data_export" value="newsletter.csv"
                            >
                                <?php esc_html_e('CSV', 'advanced-gutenberg'); ?>
                            </button>
                            <button type="submit" class="button pp-default-button advgb-export-download"
                                    name="block_data_export" value="newsletter.json"
                            >
                                <?php esc_html_e('JSON', 'advanced-gutenberg'); ?>
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </form>
    </div>
</div>
