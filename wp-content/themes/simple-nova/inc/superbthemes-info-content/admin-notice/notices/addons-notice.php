<?php
defined('ABSPATH') || exit;
if (!class_exists('TGM_Plugin_Activation') || (isset($_GET['page']) && $_GET['page'] === 'tgmpa-install-plugins')) {
    return;
}

$tgmpa = \TGM_Plugin_Activation::get_instance();
if (!isset($tgmpa->plugins['superb-blocks']) || $tgmpa->is_plugin_active('superb-blocks')) {
    return;
}

$plugin_installed = $tgmpa->is_plugin_installed('superb-blocks');
$action = $plugin_installed ? 'update' : 'install';
$nonce_url = wp_nonce_url(
    add_query_arg(
        array(
            'plugin'           => 'superb-blocks',
            'tgmpa-' . $action => $action . '-plugin',
        ),
        $tgmpa->get_tgmpa_url()
    ),
    'tgmpa-' . $action,
    'tgmpa-nonce'
);

$theme_name = wp_get_theme()->name;
?>

<div class="notice notice-info is-dismissible <?php echo esc_attr($notice['unique_id']); ?>">
    <span class="st-sa-notification-wrapper">
        <span class="st-sa-notification-wrapper-info"><?php echo esc_html__("Get Started", 'simple-nova'); ?></span>
        <span class="st-sa-notification-wrapper-headline"><?php echo esc_html(sprintf(__("Unlock %s & the Full Potential of WordPress with Superb Addons", 'simple-nova'), $theme_name)); ?></span>
        <span class="st-sa-notification-wrapper-paragraph"><?php echo esc_html__("With over 500 patterns, blocks, widgets and sections at your fingertips, you can create a professional website in minutes. Superb Addons works perfectly with Elementor and and the WordPress editor Gutenberg. Unlock the full potential of WordPress.", 'simple-nova'); ?></span>
        <span class="st-sa-notification-buttons-wrapper">
            <a href="https://superbthemes.com/superb-addons/" class="st-sa-notification-buttons-white" target="_blank"><?php echo esc_html__("Read More", 'simple-nova'); ?></a>
            <a href="<?php echo esc_url($nonce_url); ?>" class="st-sa-notification-buttons-purple"><?php echo esc_html__("Install & Activate", 'simple-nova'); ?></a>
        </span>
    </span>
    <style>
        .st-sa-notification-wrapper {
            padding: 30px 400px 30px 30px;
            display: inline-block;
            width: 100%;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            position: relative;
            background-size: contain;
        }

        .st-sa-notification-wrapper:after {
            display: block;
            width: 380px;
            content: " ";
            background-image: url(<?php echo esc_url(get_template_directory_uri() . "/inc/superbthemes-info-content/admin-notice/notices/addons-notice.png"); ?>);
            ?>);
            background-position: bottom center;
            position: absolute;
            bottom: -1px;
            right: -38px;
            height: 235px;
            background-size: contain;
            background-repeat: no-repeat;
        }

        .st-sa-notification-wrapper .st-sa-notification-wrapper-info {
            background: #fff8e1;
            color: #ff8f00;
            font-weight: bold;
            padding: 6px 10px;
            border-radius: 30px;
            display: inline-block;
        }

        .st-sa-notification-wrapper .st-sa-notification-wrapper-headline {
            width: 100%;
            display: inline-block;
            font-weight: 500;
            color: #263238;
            font-size: 23px;
            line-height: 130%;
            margin: 15px 0 20px;
        }

        .st-sa-notification-wrapper .st-sa-notification-wrapper-paragraph {
            display: inline-block;
            width: 100%;
            color: #546e7a;
            font-size: 14px;
            line-height: 144%;
            max-width: 600px;
        }

        .st-sa-notification-buttons-wrapper {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
        }

        a.st-sa-notification-buttons-white,
        a.st-sa-notification-buttons-white:hover,
        a.st-sa-notification-buttons-white:active {
            border: 1px solid #cfd8dc;
            padding: 10px 15px;
            -webkit-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.26);
            box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.26);
            color: #263238;
            font-weight: 500;
            margin-right: 15px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 10px 0 0;
        }

        a.st-sa-notification-buttons-purple,
        a.st-sa-notification-buttons-purple:hover,
        a.st-sa-notification-buttons-purple:active {
            border: 1px solid #cfd8dc;
            padding: 10px 15px;
            -webkit-box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.26);
            box-shadow: 0px 1px 2px 0px rgba(0, 0, 0, 0.26);
            color: #fff;
            font-weight: 500;
            margin-right: 15px;
            text-decoration: none;
            border-radius: 6px;
            background: -webkit-gradient(linear, left top, left bottom, from(#9524ff), to(#4312e2)), -webkit-gradient(linear, left bottom, left top, from(rgba(255, 255, 255, 0.2)), to(rgba(255, 255, 255, 0.2)));
            background: -o-linear-gradient(top, #9524ff 0%, #4312e2 100%), -o-linear-gradient(bottom, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.2));
            background: linear-gradient(180deg, #9524ff 0%, #4312e2 100%), linear-gradient(0deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.2));
            border: 1px solid #ffffff33;
            margin: 10px 10px 0 0;
        }

        @media screen and (max-width: 1200px) {
            .st-sa-notification-wrapper {
                padding: 30px 380px 30px 30px;
            }
        }

        @media screen and (max-width: 1060px) {
            .st-sa-notification-wrapper {
                padding: 20px 0px 20px 20px;
                background-image: none !important;
            }

            .st-sa-notification-wrapper:after {
                display: none;
            }
        }
    </style>
</div>