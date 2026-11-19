<?php
defined('ABSPATH') || die;

/*
 * Two-level settings tabs.
 *
 * Top-level tabs: Extra Blocks, Block Features, Post Notes, License.
 * The "Extra Blocks" tab groups these sub-tabs:
 *   General, Images, Maps, Email & Forms, reCAPTCHA, Data Export.
 *
 * Legacy deep-links (e.g. ?tab=maps, used by some blocks) are mapped to the
 * matching Extra Blocks sub-tab so they keep working.
 */

// Sub-tabs that live under "Extra Blocks".
// Maps / Email & Forms / reCAPTCHA / Data Export depend on their legacy block
// being enabled (Settings > Legacy), so they're hidden otherwise.
// The Extra Blocks tab lands on the "general" content (Blocks icon color);
// it is not listed as a sub-tab. Maps / Forms / reCAPTCHA / Data Export are
// the only sub-tabs, shown when their legacy block is enabled.
$extra_blocks_subtabs = [];
if ($this->settingIsEnabled('enable_advgb_blocks')) {
    $legacy_state = AdvancedGutenbergMain::getLegacyBlocksState();

    // Map block -> Maps tab
    if (! empty($legacy_state['map'])) {
        $extra_blocks_subtabs[] = 'maps';
    }

    // Contact Form block -> Email & Forms, reCAPTCHA, Data Export tabs
    if (! empty($legacy_state['contact-form'])) {
        $extra_blocks_subtabs[] = 'forms';
        $extra_blocks_subtabs[] = 'recaptcha';
        $extra_blocks_subtabs[] = 'data';
    }
}

// Allowed top-level tabs ("legacy" is now a top-level tab, not a sub-tab)
$allowed_top_tabs = ['extra-blocks', 'block-features', 'post-notes', 'legacy', 'license'];

$current_tab    = 'extra-blocks';
$current_subtab = 'general';

$requested_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : '';

if (in_array($requested_tab, $extra_blocks_subtabs, true)) {
    // Legacy deep-link to a sub-tab slug
    $current_tab    = 'extra-blocks';
    $current_subtab = $requested_tab;
} elseif (in_array($requested_tab, $allowed_top_tabs, true)) {
    $current_tab = $requested_tab;

    if ($current_tab === 'extra-blocks') {
        $requested_subtab = isset($_GET['subtab']) ? sanitize_text_field($_GET['subtab']) : '';
        if (in_array($requested_subtab, $extra_blocks_subtabs, true)) {
            $current_subtab = $requested_subtab;
        }
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
            'title' => esc_html__('Extra Blocks', 'advanced-gutenberg'),
            'slug' => 'extra-blocks'
        ],
        [
            'title' => esc_html__('Block Features', 'advanced-gutenberg'),
            'slug' => 'block-features'
        ]
    ];

    if ($this->settingIsEnabled('enable_post_notes')) {
        array_push(
            $tabs,
            [
                'title' => esc_html__('Post Notes', 'advanced-gutenberg'),
                'slug' => 'post-notes'
            ]
        );
    }

    // Legacy is a top-level tab (next to Extra Blocks, Block Features, Post Notes)
    array_push(
        $tabs,
        [
            'title' => esc_html__('Legacy', 'advanced-gutenberg'),
            'slug' => 'legacy'
        ]
    );

    if (defined('ADVANCED_GUTENBERG_PRO_LOADED')) {
        array_push(
            $tabs,
            [
                'title' => esc_html__('License', 'advanced-gutenberg'),
                'slug' => 'license'
            ]
        );
    }

    // Output top-level tabs menu
    echo $this->buildTabs(
        'advgb_settings',
        $current_tab,
        $tabs
    );
    ?>

    <div class="wrap">
        <?php
        if ($current_tab === 'extra-blocks') {
            // Sub-tab navigation for Extra Blocks
            $subtabs_titles = [
                'maps'      => esc_html__('Maps', 'advanced-gutenberg'),
                'forms'     => esc_html__('Email & Forms', 'advanced-gutenberg'),
                'recaptcha' => esc_html__('reCAPTCHA', 'advanced-gutenberg'),
                'data'      => esc_html__('Data Export', 'advanced-gutenberg'),
            ];

            if (! empty($extra_blocks_subtabs)) {
                printf(
                    '<nav aria-label="%s"><ul class="nav-tab-wrapper advgb-subtab-wrapper" style="margin-top:15px;">',
                    esc_attr__('Extra Blocks settings', 'advanced-gutenberg')
                );
                foreach ($extra_blocks_subtabs as $subtab_slug) {
                    if (! isset($subtabs_titles[$subtab_slug])) {
                        continue;
                    }
                    $subtab_url = admin_url(
                        'admin.php?page=advgb_settings&tab=extra-blocks&subtab=' . $subtab_slug
                    );
                    printf(
                        '<li class="nav-tab%1$s"><a href="%2$s"%4$s>%3$s</a></li>',
                        ($subtab_slug === $current_subtab ? ' nav-tab-active' : ''),
                        esc_url($subtab_url),
                        esc_html($subtabs_titles[$subtab_slug]),
                        ($subtab_slug === $current_subtab ? ' aria-current="page"' : '')
                    );
                }
                echo '</ul></nav>';
            }

            // Load active content (defaults to "general" = Blocks icon color)
            $this->loadPageTab('settings', $current_subtab);
        } else {
            // Load active top-level tab content (block-features, post-notes, license)
            $this->loadPageTab('settings', $current_tab);
        }
        ?>
    </div>
</div>
