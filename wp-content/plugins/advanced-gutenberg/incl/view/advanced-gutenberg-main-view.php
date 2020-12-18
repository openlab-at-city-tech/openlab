<?php
defined('ABSPATH') || die;

if (!function_exists('register_block_type') && !defined('GUTENBERG_DEVELOPMENT_MODE')) {
    echo '<div class="ju-notice-msg ju-notice-error">'. esc_html__('You need to activate Gutenberg to use our plugin!', 'advanced-gutenberg') .'</div>';
    return false;
}
$phpver = phpversion();

$tabs_data = array(
    array(
        'id' => 'profiles',
        'title' => __('Profiles', 'advanced-gutenberg'),
        'icon' => 'account-circle',
    ),
    array(
        'id' => 'settings',
        'title' => __('Configuration', 'advanced-gutenberg'),
        'icon' => 'settings',
    ),
    array(
        'id' => 'email-form',
        'title' => __('Email & Form', 'advanced-gutenberg'),
        'icon' => 'mail',
    ),
    array(
        'id' => 'custom-styles',
        'title' => __('Custom Styles', 'advanced-gutenberg'),
        'icon' => 'code',
    ),
    array(
        'id' => 'translations',
        'title' => __('Translation', 'advanced-gutenberg'),
        'icon' => 'text-format',
    ),
);
?>

<div class="ju-main-wrapper" style="display: none">
    <div class="ju-left-panel-toggle">
        <i class="dashicons dashicons-leftright ju-left-panel-toggle-icon"></i>
    </div>
    <div class="ju-left-panel">
        <div class="ju-menu-search">
            <i class="mi mi-search ju-menu-search-icon"></i>
            <input type="text" class="ju-menu-search-input"
                   placeholder="<?php esc_html_e('Search settings', 'advanced-gutenberg') ?>"
            >
        </div>
        <ul class="tabs ju-menu-tabs">
            <?php foreach ($tabs_data as $thisTab) :
                $tab_title = $thisTab['title'];
                $icon = $thisTab['icon'];
                if (isset($_GET['view']) && $_GET['view'] === 'profile' && $tab_title === 'Profiles') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No action, nonce is not required
                    $tab_title = __('Back to profiles list', 'advanced-gutenberg');
                    $icon = 'arrow-back';
                }
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
                 . '<a href="' . esc_attr($gutenbergUpdateUrl) . '">' . esc_html__('Update now', 'advanced-gutenberg') . '</a>'
                 . '<i class="dashicons dashicons-dismiss ju-notice-close"></i>'
             . '</div>';
        } ?>

        <?php foreach ($tabs_data as $thisTab) : ?>
            <div class="ju-content-wrapper" id="<?php echo esc_attr($thisTab['id']) ?>" style="display: none">
                <?php $this->loadView($thisTab['id']) ?>
            </div>
        <?php endforeach; ?>
        
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
                     alt="<?php esc_html_e('PublishPress Blocks logo', 'advanced-gutenberg') ?>">
                </a>
            </div>
        </footer>
    </div>
</div>