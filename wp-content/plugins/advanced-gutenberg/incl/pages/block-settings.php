<?php
defined('ABSPATH') || die;

// ThickBox JS and CSS
add_thickbox();

$blocks_list_saved = get_option('advgb_blocks_list');
$advgb_blocks      = [];
$free_version      = false;

// Global per-block enable/disable state (default: enabled unless explicitly off)
$blocks_enabled_opt = get_option('advgb_blocks_enabled');
if (! is_array($blocks_enabled_opt)) {
    $blocks_enabled_opt = [];
}
$advgb_is_block_enabled = function ($name) use ($blocks_enabled_opt) {
    return ! isset($blocks_enabled_opt[$name]) || ! empty($blocks_enabled_opt[$name]);
};

// Content Display default thumbnail (configured via the Content Display block's gear)
$advgb_settings_opt = get_option('advgb_settings');
$cd_default_thumb   = ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/blocks/recent-posts/recent-post-default.png';
$cd_thumb           = isset($advgb_settings_opt['rp_default_thumb'])
    ? $advgb_settings_opt['rp_default_thumb']
    : [ 'url' => $cd_default_thumb, 'id' => 0 ];
// Blocks hidden from this screen on all sites:
// deprecated blocks plus inner/child blocks that aren't configured directly.
$hidden_blocks     = array_merge(
    AdvancedGutenbergMain::hiddenDeprecatedBlocks(),
    AdvancedGutenbergMain::disabledLegacyBlocks(),
    [
        'advgb/accordion-item', // Accordion Item
        'advgb/list-item',      // List Item
        'advgb/tab',            // Tab Item
        'advgb/feature-list',   // Feature List
    ]
);
if (gettype($blocks_list_saved) === 'array') {
    foreach ($blocks_list_saved as $block) {
        if (strpos($block['name'], 'advgb/') === false) {
            continue;
        } elseif (in_array($block['name'], $hidden_blocks, true)) {
            continue;
        } else {
            $block['icon'] = htmlentities($block['icon']);
            array_push($advgb_blocks, $block);
        }
    }
}

if (!defined('ADVANCED_GUTENBERG_PRO_LOADED')) {
    $advgb_blocks = array_merge($advgb_blocks, PublishPress\Blocks\Utilities::getProBlocks());
    $free_version      = true;
}

// Remove hidden blocks (includes Pro promo blocks merged in above, e.g. Feature List)
$advgb_blocks = array_values(array_filter($advgb_blocks, function ($block) use ($hidden_blocks) {
    return ! in_array($block['name'], $hidden_blocks, true);
}));

/**
 * Sort array
 *
 * @param string $key Array key to sort
 *
 * @return Closure
 */
function sortBy($key)
{
    return function ($a, $b) use ($key) {
        return strnatcmp($a[$key], $b[$key]);
    };
}

usort($advgb_blocks, sortBy('title'));
$excluded_blocks_config = [
    'advgb/container',
    'advgb/accordion-item',
    'advgb/accordion',
    'advgb/tabs',
    'advgb/tab',
    'advgb/recent-posts',
    'advgb/login-form',
    'advgb/search-bar',
    'advgb/countdown',
    'advgb/feature-list',
    'advgb/feature',
    'advgb/pricing-table'
];

$new_titles = [
    'advgb/accordions' => __('Accordion - PublishPress', 'advanced-gutenberg'),
    'advgb/button' => __('Button - PublishPress', 'advanced-gutenberg'),
    'advgb/icon' => __('Icon - PublishPress', 'advanced-gutenberg'),
    'advgb/image' => __('Image - PublishPress', 'advanced-gutenberg'),
    'advgb/list' => __('List - PublishPress', 'advanced-gutenberg'),
    'advgb/table' => __('Table - PublishPress', 'advanced-gutenberg'),
    'advgb/adv-tabs' => __('Tabs - PublishPress', 'advanced-gutenberg'),
    'advgb/video' => __('Video - PublishPress', 'advanced-gutenberg'),
    'advgb/columns' => __('Columns - PublishPress', 'advanced-gutenberg'),
    'advgb/column' => __('Column - PublishPress', 'advanced-gutenberg'),
    'advgb/contact-form' => __('Contact Form - PublishPress', 'advanced-gutenberg'),
    'advgb/count-up' => __('Count Up - PublishPress', 'advanced-gutenberg'),
    'advgb/images-slider' => __('Images Slider - PublishPress', 'advanced-gutenberg'),
    'advgb/infobox' => __('Info Box - PublishPress', 'advanced-gutenberg'),
    'advgb/map' => __('Map - PublishPress', 'advanced-gutenberg'),
    'advgb/newsletter' => __('Newsletter - PublishPress', 'advanced-gutenberg'),
    'advgb/social-links' => __('Social Links - PublishPress', 'advanced-gutenberg'),
    'advgb/summary' => __('Table of Contents - PublishPress', 'advanced-gutenberg'),
    'advgb/testimonial' => __('Testimonial - PublishPress', 'advanced-gutenberg'),
    'advgb/woo-products' => __('Woo Products - PublishPress', 'advanced-gutenberg')
];

// Short description of what each block does, shown as a hover tooltip
$block_descriptions = [
    'advgb/accordions'    => __('Show content in collapsible panels that expand when clicked.', 'advanced-gutenberg'),
    'advgb/button'        => __('Add a customizable call-to-action button with colors, sizes and icons.', 'advanced-gutenberg'),
    'advgb/icon'          => __('Insert a vector icon from a large library, with size and color controls.', 'advanced-gutenberg'),
    'advgb/image'         => __('Display an image with extra styling, captions and link options.', 'advanced-gutenberg'),
    'advgb/list'          => __('Build styled ordered or unordered lists with custom icons.', 'advanced-gutenberg'),
    'advgb/table'         => __('Create responsive tables with styling and per-cell formatting.', 'advanced-gutenberg'),
    'advgb/adv-tabs'      => __('Organize content into horizontal or vertical tabbed sections.', 'advanced-gutenberg'),
    'advgb/video'         => __('Embed a self-hosted or external video with a custom poster image.', 'advanced-gutenberg'),
    'advgb/columns'       => __('Lay out content in flexible, responsive multi-column rows.', 'advanced-gutenberg'),
    'advgb/column'        => __('A single column used inside the Columns block.', 'advanced-gutenberg'),
    'advgb/container'     => __('Wrap blocks in a styled container with background, padding and borders.', 'advanced-gutenberg'),
    'advgb/contact-form'  => __('Add a customizable contact form that emails submissions to you.', 'advanced-gutenberg'),
    'advgb/count-up'      => __('Animate numbers counting up to highlight stats and milestones.', 'advanced-gutenberg'),
    'advgb/images-slider' => __('Show multiple images in a responsive carousel slider.', 'advanced-gutenberg'),
    'advgb/infobox'       => __('Present an icon or image with a heading and text in a styled box.', 'advanced-gutenberg'),
    'advgb/map'           => __('Embed an interactive Google Map with custom markers.', 'advanced-gutenberg'),
    'advgb/newsletter'    => __('Collect email subscribers with a customizable signup form.', 'advanced-gutenberg'),
    'advgb/login-form'    => __('Display a front-end login and registration form.', 'advanced-gutenberg'),
    'advgb/search-bar'    => __('Add a styled site search bar anywhere on your pages.', 'advanced-gutenberg'),
    'advgb/social-links'  => __('Link to your social profiles with a row of social icons.', 'advanced-gutenberg'),
    'advgb/summary'       => __('Generate an automatic table of contents from your headings.', 'advanced-gutenberg'),
    'advgb/testimonial'   => __('Showcase customer testimonials with photo, name and role.', 'advanced-gutenberg'),
    'advgb/woo-products'  => __('Display WooCommerce products in a customizable grid.', 'advanced-gutenberg'),
    'advgb/countdown'     => __('Display a countdown timer to a specific date or event.', 'advanced-gutenberg'),
    'advgb/feature'       => __('Highlight a single feature with an icon, title and description.', 'advanced-gutenberg'),
    'advgb/pricing-table' => __('Compare pricing plans side by side with features and buttons.', 'advanced-gutenberg'),
];

// Pro
if (defined('ADVANCED_GUTENBERG_PRO_LOADED')) {
    if (method_exists('PPB_AdvancedGutenbergPro\Utils\Definitions', 'advgb_pro_default_block_settings')) {
        $excludedProBlocks = PPB_AdvancedGutenbergPro\Utils\Definitions::advgb_pro_default_block_settings('excluded_blocks');
        foreach ($excludedProBlocks as $excludedProBlock) {
            array_push(
                $excluded_blocks_config,
                $excludedProBlock
            );
        }
    }
}
?>
<div class="publishpress-admin wrap">
    <?php if (isset($_GET['save']) && $_GET['save'] === 'success') : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only ?>
        <div id="message" class="updated fade">
            <p><?php esc_html_e('Settings saved successfully!', 'advanced-gutenberg'); ?></p>
        </div>
    <?php endif; ?>
    <header>
        <h1 class="wp-heading-inline">
            <?php esc_html_e('Extra Blocks', 'advanced-gutenberg') ?>
        </h1>
    </header>
    <?php
    $this->screenDescription(
        __(
            'Manage the extra PublishPress blocks available in the editor, including content displays, sliders, buttons, icons, tabs, accordions, and more.',
            'advanced-gutenberg'
        )
    );
    ?>
    <div class="wrap">
        <div class="advgb-search-wrapper" style="padding-bottom: 20px;">
            <label for="advgb-block-settings-search" class="screen-reader-text">
                <?php esc_html_e('Search blocks', 'advanced-gutenberg') ?>
            </label>
            <input type="text"
                   id="advgb-block-settings-search"
                   class="advgb-search-input blocks-config-search"
                   placeholder="<?php esc_attr_e('Search blocks', 'advanced-gutenberg') ?>"
            >
        </div>
        <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=advgb_block_settings')) ?>">
        <?php wp_nonce_field('advgb_blocks_enabled_nonce', 'advgb_blocks_enabled_nonce_field') ?>
        <?php $advgb_toggle_names = []; ?>
        <ul class="blocks-config-list clearfix">
            <?php foreach ($advgb_blocks as $block) : ?>
                <?php $iconColor = '';
                if (isset($block['iconColor'])) :
                    $iconColor = (string) $block['iconColor'];
                endif;

                // Use new block title
                if (isset($new_titles[$block['name']])) {
                    $block['title'] = $new_titles[$block['name']];
                }
                // Remove the " - PublishPress" suffix from block names
                $block['title'] = preg_replace('/\s*-?\s*PublishPress\s*$/', '', $block['title']);
                $blur_class = '';
                $isProPromo = false;
                if ($free_version && isset($block['isPro']) && $block['isPro'] ===  true) {
                    $blur_class = 'advgb-blur';
                    $isProPromo = true;
                }
                ?>
            <li class="block-config-item advgb-settings-option">
                <span class="block-icon <?php echo esc_attr($blur_class); ?>" <?php echo $iconColor ? 'style="color:' . esc_attr($iconColor) . '"' : ''; ?>>
                    <?php echo html_entity_decode(html_entity_decode(stripslashes($block['icon']))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped ?>
                </span>
                <span class="block-title  <?php echo esc_attr($blur_class); ?>"><?php echo esc_html(__($block['title'], 'advanced-gutenberg')); ?></span>
                <?php if (isset($block_descriptions[$block['name']])) :
                    $advgb_tooltip_id = 'advgb-block-tooltip-' . sanitize_html_class(str_replace('/', '-', $block['name'])); ?>
                    <span class="ppb-tooltips-library advgb-block-tooltip"
                          data-toggle="ppbtooltip"
                          data-placement="top">
                        <button type="button"
                                class="advgb-block-tooltip-trigger"
                                aria-label="<?php echo esc_attr(sprintf(__('About the %s block', 'advanced-gutenberg'), wp_strip_all_tags(__($block['title'], 'advanced-gutenberg')))); ?>"
                                aria-describedby="<?php echo esc_attr($advgb_tooltip_id); ?>">
                            <i class="dashicons dashicons-info-outline" aria-hidden="true"></i>
                        </button>
                        <span class="tooltip-text" role="tooltip" id="<?php echo esc_attr($advgb_tooltip_id); ?>">
                            <?php echo esc_html($block_descriptions[$block['name']]); ?>
                        </span>
                    </span>
                <?php endif; ?>
                <?php if (! $isProPromo) : // Enable/Disable toggle (global, per block)
                    $advgb_toggle_names[] = $block['name']; ?>
                    <label class="advgb-switch-button advgb-block-enable-toggle"
                           title="<?php esc_attr_e('Enable / disable this block', 'advanced-gutenberg') ?>">
                        <span class="switch">
                            <input type="checkbox"
                                   name="advgb_blocks_enabled[<?php echo esc_attr($block['name']); ?>]"
                                   value="1"
                                   aria-label="<?php echo esc_attr(sprintf(__('Enable %s block', 'advanced-gutenberg'), wp_strip_all_tags(__($block['title'], 'advanced-gutenberg')))); ?>"
                                <?php checked($advgb_is_block_enabled($block['name'])); ?>
                            />
                            <span class="slider" aria-hidden="true"></span>
                        </span>
                    </label>
                <?php endif; ?>
                <?php if ($block['name'] === 'advgb/recent-posts') : ?>
                    <a href="#TB_inline?width=560&height=360&inlineId=advgb-content-display-config"
                       class="thickbox advgb-cd-config-link"
                       aria-label="<?php esc_attr_e('Configure Recent Posts block', 'advanced-gutenberg') ?>"
                       title="<?php esc_attr_e('Configure', 'advanced-gutenberg') ?>">
                        <i class="dashicons dashicons-admin-generic block-config-button" aria-hidden="true"></i>
                    </a>
                <?php elseif ($isProPromo) : ?>
                    <span class="advgb-pro-small-overlay-text" style="float: right;">
                        <a class="advgb-pro-link clickable" href="<?php echo esc_url(ADVANCED_GUTENBERG_UPGRADE_LINK); ?>" target="_blank">
                            <span class="dashicons dashicons-lock"></span> <?php esc_html_e('Pro', 'advanced-gutenberg') ?>
                        </a>
                    </span>
                <?php elseif (!in_array($block['name'], $excluded_blocks_config)) : ?>
                    <button type="button"
                            class="dashicons dashicons-admin-generic block-config-button"
                            aria-label="<?php echo esc_attr(sprintf(__('Edit %s block settings', 'advanced-gutenberg'), wp_strip_all_tags(__($block['title'], 'advanced-gutenberg')))); ?>"
                            title="<?php esc_attr_e('Edit', 'advanced-gutenberg') ?>"
                            data-block="<?php echo esc_attr($block['name']); ?>"
                    ></button>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if (! empty($advgb_toggle_names)) : ?>
            <input type="hidden" name="advgb_blocks_all" value="<?php echo esc_attr(implode(',', $advgb_toggle_names)) ?>"/>
            <div class="advgb-form-buttons-bottom">
                <button type="submit" class="button button-primary" name="save_blocks_enabled">
                    <?php esc_html_e('Save', 'advanced-gutenberg') ?>
                </button>
            </div>
        <?php endif; ?>
        </form>

        <?php if (count($advgb_blocks) === 0) : ?>
            <div class="blocks-not-loaded" style="text-align: center">
                <p>
                    <?php esc_html_e('No blocks available. Please go to a post edit (without saving either modifying anything). Then come back to Block Settings to see the blocks list.', 'advanced-gutenberg'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php // Content Display config popup (Default thumbnail), opened by the block's gear ?>
<div id="advgb-content-display-config" style="display:none;">
    <div class="publishpress-admin advgb-cd-config-inner">
        <h2 class="advgb-cd-config-title"><?php esc_html_e('Content Display', 'advanced-gutenberg') ?></h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=advgb_block_settings')) ?>">
            <?php wp_nonce_field('advgb_content_display_nonce', 'advgb_content_display_nonce_field') ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <?php esc_html_e('Default thumbnail', 'advanced-gutenberg') ?>
                    </th>
                    <td>
                        <div class="setting-actions-wrapper">
                            <input type="hidden" id="post_default_thumb" name="post_default_thumb"
                                   value="<?php echo esc_attr($cd_thumb['url']); ?>"/>
                            <input type="hidden" id="post_default_thumb_id" name="post_default_thumb_id"
                                   value="<?php echo esc_attr($cd_thumb['id']); ?>"/>
                            <div class="setting-actions" id="post_default_thumb_actions">
                                <img class="thumb-selected"
                                     src="<?php echo esc_url($cd_thumb['url']); ?>"
                                     alt="<?php esc_attr_e('Default post thumbnail', 'advanced-gutenberg'); ?>"
                                     data-default="<?php echo esc_url($cd_default_thumb); ?>"/>
                                <button type="button"
                                        class="dashicons dashicons-edit"
                                        id="thumb_edit"
                                        aria-label="<?php esc_attr_e('Select default thumbnail', 'advanced-gutenberg'); ?>"
                                        title="<?php esc_attr_e('Edit', 'advanced-gutenberg'); ?>"></button>
                                <button type="button"
                                        class="dashicons dashicons-no"
                                        id="thumb_remove"
                                        aria-label="<?php esc_attr_e('Reset thumbnail to default', 'advanced-gutenberg'); ?>"
                                        title="<?php esc_attr_e('Reset to default', 'advanced-gutenberg'); ?>"></button>
                            </div>
                        </div>
                        <p class="description">
                            <?php esc_html_e(
                                'Set the default post thumbnail to use in Content Display blocks for posts without a featured image.',
                                'advanced-gutenberg'
                            ) ?>
                        </p>
                    </td>
                </tr>
            </table>

            <div class="advgb-form-buttons-bottom">
                <button type="submit" class="button button-primary" name="save_content_display_thumb">
                    <?php esc_html_e('Save', 'advanced-gutenberg') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(function ($) {
    // Info tooltips: Esc hides the open tooltip; it can show again on next hover/focus.
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            $(document.querySelectorAll('.advgb-block-tooltip:hover, .advgb-block-tooltip:focus-within'))
                .addClass('advgb-tooltip-dismissed');
        }
    });
    $(document).on('mouseleave focusout', '.advgb-block-tooltip', function () {
        $(this).removeClass('advgb-tooltip-dismissed');
    });

    var frame;
    $(document).on('click', '#thumb_edit', function (e) {
        e.preventDefault();
        if (frame) { frame.open(); return; }
        frame = wp.media({
            title: '<?php echo esc_js(__('Select default thumbnail', 'advanced-gutenberg')); ?>',
            button: { text: '<?php echo esc_js(__('Use this image', 'advanced-gutenberg')); ?>' },
            library: { type: 'image' },
            multiple: false
        });
        frame.on('select', function () {
            var img = frame.state().get('selection').first().toJSON();
            $('#post_default_thumb').val(img.url);
            $('#post_default_thumb_id').val(img.id);
            $('#advgb-content-display-config .thumb-selected').attr('src', img.url);
        });
        frame.open();
    });
    $(document).on('click', '#thumb_remove', function (e) {
        e.preventDefault();
        var $img = $('#advgb-content-display-config .thumb-selected');
        $img.attr('src', $img.data('default'));
        $('#post_default_thumb').val($img.data('default'));
        $('#post_default_thumb_id').val(0);
    });
});
</script>

<style>
.advgb-block-tooltip {
    vertical-align: middle;
    margin-left: 6px;
    line-height: 30px;
}
.advgb-block-tooltip .advgb-block-tooltip-trigger {
    background: none;
    border: 0;
    padding: 0;
    margin: 0;
    color: inherit;
    font: inherit;
    line-height: inherit;
    cursor: pointer;
    vertical-align: top;
}
.advgb-block-tooltip .advgb-block-tooltip-trigger:focus-visible {
    outline: 2px solid #2271b1;
    outline-offset: 2px;
}
.advgb-block-tooltip.advgb-tooltip-dismissed .tooltip-text {
    display: none !important;
}
.advgb-block-tooltip .dashicons-info-outline {
    color: #646970;
    font-size: 18px;
    width: 18px;
    height: 18px;
    line-height: 30px;
    transition: color .2s ease;
}
.advgb-block-tooltip:hover .dashicons-info-outline,
.advgb-block-tooltip:focus-within .dashicons-info-outline {
    color: #655997;
}
.advgb-block-tooltip .tooltip-text {
    font-weight: 400;
    line-height: 1.5;
}

/* Content Display gear link: align right like other blocks, no link underline */
.advgb-cd-config-link,
.advgb-cd-config-link:hover,
.advgb-cd-config-link:focus,
.advgb-cd-config-link:active {
    float: right;
    text-decoration: none;
    box-shadow: none;
}
.advgb-cd-config-link:focus:not(:focus-visible) {
    outline: none;
}
.advgb-cd-config-link:focus-visible {
    outline: 2px solid #2271b1;
    outline-offset: 2px;
}
.advgb-cd-config-link .block-config-button {
    float: none;
}

/* Content Display config popup styling */
.advgb-cd-config-inner {
    padding: 10px 24px 24px;
}
.advgb-cd-config-inner .advgb-cd-config-title {
    margin: 0 0 10px;
    padding: 0;
    font-size: 1.4em;
}
.advgb-cd-config-inner .form-table th {
    padding-left: 0;
}

/* Per-block enable/disable toggle */
.block-config-item .advgb-block-enable-toggle {
    float: right;
    margin: 0 4px 0 10px;
    line-height: 30px;
}
.block-config-item .advgb-block-enable-toggle .switch {
    margin: 2px 0;
}
</style>
