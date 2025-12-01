<?php
defined('ABSPATH') || die;

$allowed_tabs = ['general', 'block-features', 'images', 'maps', 'forms', 'recaptcha', 'data', 'license'];
$current_tab = 'general';
if (isset($_GET['tab']) && !empty($_GET['tab'])) {
    $requested_tab = sanitize_text_field($_GET['tab']);
    if (in_array($requested_tab, $allowed_tabs, true)) {
        $current_tab = $requested_tab;
    }
}
?>

<div class="publishpress-admin pp-blocks-settings wrap">

    <?php if (isset($_GET['save'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action ?>
        <div id="message" class="updated fade">
            <p>
                <?php esc_html_e('Settings saved successfully!', 'advanced-gutenberg'); ?>
            </p>
        </div>
    <?php endif; ?>

    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e('Settings', 'advanced-gutenberg') ?>
        </h1>
    </header>

    <?php
    $tabs = [
        [
            'title' => esc_html__('General', 'advanced-gutenberg'),
            'slug' => 'general'
        ],
        [
            'title' => esc_html__('Block Features', 'advanced-gutenberg'),
            'slug' => 'block-features'
        ]
    ];

    if ($this->settingIsEnabled('enable_advgb_blocks')) {
        array_push(
            $tabs,
            [
                'title' => esc_html__('Images', 'advanced-gutenberg'),
                'slug' => 'images'
            ],
            [
                'title' => esc_html__('Maps', 'advanced-gutenberg'),
                'slug' => 'maps'
            ],
            [
                'title' => esc_html__('Email & Forms', 'advanced-gutenberg'),
                'slug' => 'forms'
            ],
            [
                'title' => esc_html__('reCAPTCHA', 'advanced-gutenberg'),
                'slug' => 'recaptcha'
            ],
            [
                'title' => esc_html__('Data Export', 'advanced-gutenberg'),
                'slug' => 'data'
            ]
        );
    }

    if (defined('ADVANCED_GUTENBERG_PRO_LOADED')) {
        array_push(
            $tabs,
            [
                'title' => esc_html__('License', 'advanced-gutenberg'),
                'slug' => 'license'
            ]
        );
    }

    // Output tabs menu
    echo $this->buildTabs(
        'advgb_settings',
        $current_tab,
        $tabs
    );
    ?>

    <div class="wrap">
        <?php
        // Load active settings tab
        $this->loadPageTab('settings', $current_tab);
        ?>
    </div>
</div>
