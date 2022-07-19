<?php
defined('ABSPATH') || die;

if (!function_exists('register_block_type') && !defined('GUTENBERG_DEVELOPMENT_MODE')) {
    echo '<div class="ju-notice-msg ju-notice-error">'. esc_html__('You need to activate Gutenberg to use our plugin!', 'advanced-gutenberg') .'</div>';
    return false;
}
$phpver = phpversion();

$saved_settings = get_option('advgb_settings');

// Block Access page
if( !isset($saved_settings['enable_block_access']) || $saved_settings['enable_block_access'] ) {
    $tabs_data[] = [
        'id' => 'block-access',
        'title' => __('Block Access', 'advanced-gutenberg'),
        'icon' => 'account-circle',
        'order' => 1,
    ];
}

// Settings
$tabs_data[] = [
    'id' => 'settings',
    'title' => __('Settings', 'advanced-gutenberg'),
    'icon' => 'build',
    'order' => 3,
];

// Block Settings and Email Form pages
if( !isset($saved_settings['enable_advgb_blocks']) || $saved_settings['enable_advgb_blocks'] ) {
    $tabs_data[] = [
        'id' => 'block-settings',
        'title' => __('Block Settings', 'advanced-gutenberg'),
        'icon' => 'settings',
        'order' => 4,
    ];
    $tabs_data[] = [
        'id' => 'email-form',
        'title' => __('Email & Form', 'advanced-gutenberg'),
        'icon' => 'mail',
        'order' => 5,
    ];
}

// Custom styles page
if( !isset($saved_settings['enable_custom_styles']) || $saved_settings['enable_custom_styles'] ) {
    $tabs_data[] = [
        'id' => 'custom-styles',
        'title' => __('Custom Styles', 'advanced-gutenberg'),
        'icon' => 'code',
        'order' => 6,
    ];
}

// Upgrade to Pro page
if(!defined('ADVANCED_GUTENBERG_PRO')) {
    array_push(
        $tabs_data,
        array(
            'id' => 'pro',
            'title' => __('Blocks Pro', 'advanced-gutenberg'),
            'icon' => 'star',
            'order' => 7,
        )
    );
}

// Pro pages
if(defined('ADVANCED_GUTENBERG_PRO')) {
    if ( method_exists( 'PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_pages' ) ) {
        $tabs_data = PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_pages( $tabs_data );
    }
}

// Pro
if(!defined('ADVANCED_GUTENBERG_PRO')) {
?>
    <div class="pp-version-notice-bold-purple">
        <div class="pp-version-notice-bold-purple-message">
            <?php _e('You\'re using PublishPress Blocks Free. The Pro version has more features and support.', 'advanced-gutenberg') ?>
        </div>
        <div class="pp-version-notice-bold-purple-button">
            <a href="https://publishpress.com/links/blocks" target="_blank">
                <?php _e('Upgrade to Pro', 'advanced-gutenberg') ?>
            </a>
        </div>
    </div>
<?php } ?>

<div class="ju-main-wrapper" style="display: none">
    <div class="ju-left-panel-toggle">
        <i class="dashicons dashicons-leftright ju-left-panel-toggle-icon"></i>
    </div>
    <div class="ju-left-panel">
        <div class="ju-menu-search">
            <i class="mi mi-search ju-menu-search-icon"></i>
            <input type="text" class="ju-menu-search-input"
                   placeholder="<?php esc_attr_e('Search settings', 'advanced-gutenberg') ?>"
            >
        </div>
        <ul class="tabs ju-menu-tabs">
            <?php foreach ($tabs_data as $thisTab) :
                $tab_title = $thisTab['title'];
                $icon = $thisTab['icon'];
                ?>
                <li class="tab" data-tab-title="<?php echo esc_attr($thisTab['title']) ?>">
                    <a href="#<?php echo esc_attr($thisTab['id']) ?>"
                       class="link-tab white-text waves-effect waves-light"
                    >
                        <i class="mi mi-<?php echo esc_attr($icon) ?> menu-tab-icon"></i>
                        <span class="tab-title"><?php echo esc_html($tab_title) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="ju-right-panel">
        <?php
        if (defined('GUTENBERG_VERSION') && version_compare(GUTENBERG_VERSION, GUTENBERG_VERSION_REQUIRED, 'lt')) {
            $gutenbergUpdateUrl = wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => 'upgrade-plugin',
                        'plugin' => 'gutenberg'
                    ),
                    admin_url('update.php')
                ),
                'upgrade-plugin_gutenberg'
            );

            echo '<div class="ju-notice-msg ju-notice-error">'
                 . esc_html__('Our plugin works great with Gutenberg version', 'advanced-gutenberg')
                 . ' <b>' . esc_html(GUTENBERG_VERSION_REQUIRED). '</b> '
                 . esc_html__('and above', 'advanced-gutenberg') . '. '
                 . esc_html__('Your current version is', 'advanced-gutenberg')
                 . ' <b>' . esc_html(GUTENBERG_VERSION) . '</b>. '
                 . '<a href="' . esc_url($gutenbergUpdateUrl) . '">' . esc_html__('Update now', 'advanced-gutenberg') . '</a>'
                 . '<i class="dashicons dashicons-dismiss ju-notice-close"></i>'
             . '</div>';
        } ?>

        <?php foreach ($tabs_data as $thisTab) : ?>
            <div class="ju-content-wrapper" id="<?php echo esc_attr($thisTab['id']) ?>" style="display: none">
                <?php $this->loadView($thisTab['id']) ?>
            </div>
        <?php endforeach; ?>

        <?php if( !isset($saved_settings['enable_pp_branding']) || $saved_settings['enable_pp_branding'] ) { ?>
            <footer>
                <div class="ppma-rating">
                    <a href="https://wordpress.org/support/plugin/advanced-gutenberg/reviews/#new-post" target="_blank" rel="noopener noreferrer" class="ag-footer-link">If you like <strong>PublishPress Blocks</strong> please leave us a <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span> rating. Thank you!</a>
                </div>
                <hr>
                <nav>
                    <ul>
                        <li>
                            <a href="https://publishpress.com" target="_blank" rel="noopener noreferrer" title="About PublishPress Blocks" class="ag-footer-link">About</a>
                        </li>
                        <li>
                            <a href="https://publishpress.com/knowledge-base/installation/" target="_blank" rel="noopener noreferrer" title="Documentation" class="ag-footer-link">Documentation</a>
                        </li>
                        <li>
                            <a href="https://publishpress.com/contact" target="_blank" rel="noopener noreferrer" title="Contact the PublishPress team" class="ag-footer-link">Contact</a>
                        </li>
                        <li>
                            <a href="https://twitter.com/publishpresscom" target="_blank" rel="noopener noreferrer" class="ag-footer-link">
                                <span class="dashicons dashicons-twitter"></span>
                            </a>
                        </li>
                        <li>
                            <a href="https://facebook.com/publishpress" target="_blank" rel="noopener noreferrer" class="ag-footer-link">
                                <span class="dashicons dashicons-facebook"></span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="ppma-pressshack-logo">
                    <a href="https://publishpress.com" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url(plugins_url('assets/images/publishpress-logo.png', dirname(dirname(__FILE__)))) ?>"
                         alt="<?php esc_attr_e('PublishPress Blocks logo', 'advanced-gutenberg') ?>">
                    </a>
                </div>
            </footer>
        <?php } ?>
    </div>
</div>
