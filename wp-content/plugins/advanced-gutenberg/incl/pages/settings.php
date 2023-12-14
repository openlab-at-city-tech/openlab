<?php
defined( 'ABSPATH' ) || die;

// Check current tab parameter from URL. e.g. 'admin.php?page=lorem&tab=something'
$current_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] )
                ? sanitize_text_field( $_GET['tab'] )
                : 'general';
?>

<div class="publishpress-admin wrap">

    <?php if ( isset( $_GET['save'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action ?>
        <div id="message" class="updated fade">
            <p>
                <?php esc_html_e( 'Settings saved successfully!', 'advanced-gutenberg' ); ?>
            </p>
        </div>
    <?php endif; ?>

    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'Settings', 'advanced-gutenberg' ) ?>
        </h1>
    </header>

    <?php
    $tabs = [
        [
            'title' => __( 'General', 'advanced-gutenberg' ),
            'slug' => 'general'
        ]
    ];

    if( $this->settingIsEnabled( 'enable_advgb_blocks' ) ) {
        array_push(
            $tabs,
            [
                'title' => __( 'Images', 'advanced-gutenberg' ),
                'slug' => 'images'
            ],
            [
                'title' => __( 'Maps', 'advanced-gutenberg' ),
                'slug' => 'maps'
            ],
            [
                'title' => __( 'Email & Forms', 'advanced-gutenberg' ),
                'slug' => 'forms'
            ],
            [
                'title' => __( 'reCAPTCHA', 'advanced-gutenberg' ),
                'slug' => 'recaptcha'
            ],
            [
                'title' => __( 'Data Export', 'advanced-gutenberg' ),
                'slug' => 'data'
            ]
        );
    }

    if( defined( 'ADVANCED_GUTENBERG_PRO_LOADED' ) ) {
        array_push(
            $tabs,
            [
                'title' => __( 'License', 'advanced-gutenberg' ),
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
        $this->loadPageTab( 'settings', $current_tab );
        ?>
    </div>
</div>
