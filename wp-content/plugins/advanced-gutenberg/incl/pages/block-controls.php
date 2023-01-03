<?php
defined( 'ABSPATH' ) || die;

// Check current tab parameter from URL. e.g. 'admin.php?page=lorem&tab=something'
$current_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] )
                ? sanitize_text_field( $_GET['tab'] )
                : 'controls';
?>

<div class="publishpress-admin wrap">

    <?php if ( isset( $_GET['save'] ) && $_GET['save'] === 'success' ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display message, no action
        if( $current_tab === 'controls' ) {
            $message = esc_html__( 'Controls saved successfully!', 'advanced-gutenberg' );
        } elseif( $current_tab === 'blocks' ) {
            $message = esc_html__( 'Blocks saved successfully!', 'advanced-gutenberg' );
        } else {
            $message = esc_html__( 'Settings saved successfully!', 'advanced-gutenberg' );
        }
        ?>
        <div id="message" class="updated fade">
            <p>
                <?php echo $message; ?>
            </p>
        </div>
    <?php endif; ?>

    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'Block Controls', 'advanced-gutenberg' ) ?>
        </h1>
    </header>

    <?php
    $tabs = [
        [
            'title' => __( 'Controls', 'advanced-gutenberg' ),
            'slug' => 'controls'
        ],
        [
            'title' => __( 'Blocks', 'advanced-gutenberg' ),
            'slug' => 'blocks'
        ]
    ];

    // Output tabs menu
    echo $this->buildTabs(
        'advgb_block_controls',
        $current_tab,
        $tabs
    );
    ?>

    <div class="wrap">
        <?php
        // Load active settings tab
        $this->loadPageTab( 'block-controls', $current_tab );
        ?>
    </div>
</div>
