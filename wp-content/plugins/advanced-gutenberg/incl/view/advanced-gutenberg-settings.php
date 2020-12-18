<?php
defined('ABSPATH') || die;

wp_enqueue_style('minicolors_css');
wp_enqueue_style('advgb_qtip_style');
wp_enqueue_style('codemirror_css');
wp_enqueue_style('codemirror_hint_style');
wp_enqueue_style('advgb_settings_style');

wp_enqueue_media();
wp_enqueue_script('qtip_js');
wp_enqueue_script('less_js');
wp_enqueue_script('minicolors_js');
wp_enqueue_script('advgb_codemirror_js');
wp_enqueue_script('codemirror_hint');
wp_enqueue_script('codemirror_mode_css');
wp_enqueue_script('codemirror_hint_css');
wp_enqueue_script('advgb_settings_js');

// ThickBox JS and CSS
add_thickbox();

$saved_settings    = get_option('advgb_settings');
$blocks_list_saved = get_option('advgb_blocks_list');
$advgb_blocks      = array();

if (gettype($blocks_list_saved) === 'array') {
    foreach ($blocks_list_saved as $block) {
        if (strpos($block['name'], 'advgb/') === false) {
            continue;
        } else {
            $block['icon'] = htmlentities($block['icon']);
            array_push($advgb_blocks, $block);
        }
    }
}

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
$excluded_blocks_config = array(
    'advgb/container',
    'advgb/accordion-item',
    'advgb/accordion',
    'advgb/tabs',
    'advgb/tab',
);

$gallery_lightbox_checked         = $saved_settings['gallery_lightbox'] ? 'checked' : '';
$gallery_lightbox_caption_checked = $saved_settings['gallery_lightbox_caption'] ? 'checked' : '';
$disable_wpautop_checked          = !empty($saved_settings['disable_wpautop']) ? 'checked' : '';
$google_api_key_saved             = isset($saved_settings['google_api_key']) ? $saved_settings['google_api_key'] : '';
$enable_blocks_spacing            = isset($saved_settings['enable_blocks_spacing']) && $saved_settings['enable_blocks_spacing'] ? 'checked' : '';
$blocks_spacing                   = isset($saved_settings['blocks_spacing']) ? $saved_settings['blocks_spacing'] : 0;
$blocks_icon_color                = isset($saved_settings['blocks_icon_color']) ? $saved_settings['blocks_icon_color'] : '#5952de';
$editor_width                     = isset($saved_settings['editor_width']) ? $saved_settings['editor_width'] : '0';
$default_thumb                    = plugins_url('assets/blocks/recent-posts/recent-post-default.png', ADVANCED_GUTENBERG_PLUGIN);
$rp_default_thumb                 = isset($saved_settings['rp_default_thumb']) ? $saved_settings['rp_default_thumb'] : array('url' => $default_thumb, 'id' => 0);
$enable_columns_visual_guide      = isset($saved_settings['enable_columns_visual_guide']) && $saved_settings['enable_columns_visual_guide'] ? 'checked' : '';
if (!isset($saved_settings['enable_columns_visual_guide'])) {
    $enable_columns_visual_guide = 'checked';
}
?>

<div id="advgb-settings-container">
    <div class="ju-top-tabs-wrapper">
        <ul class="tabs ju-top-tabs">
            <li class="tab">
                <a href="#config-tab" class="link-tab">
                    <?php esc_html_e('Configuration', 'advanced-gutenberg') ?>
                </a>
            </li>
            <li class="tab">
                <a href="#block-config-tab" class="link-tab">
                    <?php esc_html_e('Default blocks config', 'advanced-gutenberg') ?>
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

    <h1 class="advgb-settings-header"><?php esc_html_e('Configuration', 'advanced-gutenberg') ?></h1>

    <div id="config-tab" class="tab-content clearfix" style="display: none;">
        <form method="post">
            <?php wp_nonce_field('advgb_settings_nonce', 'advgb_settings_nonce_field') ?>
            <ul class="settings-list clearfix">
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="gallery_lightbox"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'Open gallery images as a lightbox style popup',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Open galleries in lightbox', 'advanced-gutenberg') ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" name="gallery_lightbox"
                                       id="gallery_lightbox"
                                       value="1"
                                    <?php echo esc_attr($gallery_lightbox_checked) ?>
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix" id="gallery_lightbox_caption_wrapper">
                    <div class="settings-option-wrapper clearfix">
                        <label for="gallery_lightbox_caption"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'Display caption text on images loaded as lightbox in galleries',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Image caption', 'advanced-gutenberg') ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" name="gallery_lightbox_caption"
                                       id="gallery_lightbox_caption"
                                       value="1"
                                    <?php echo esc_attr($gallery_lightbox_caption_checked) ?>
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="ag_disable_wpautop"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'Remove the WordPress function autop, used to prevent unwanted paragraph to be added in some blocks',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Remove Autop', 'advanced-gutenberg') ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" name="disable_wpautop"
                                       id="ag_disable_wpautop"
                                       value="1"
                                    <?php echo esc_attr($disable_wpautop_checked) ?>
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option full-width clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="google_api_key"
                               class="ju-setting-label advgb_qtip"
                               style="float: none; margin-bottom: 10px;"
                               data-qtip="<?php esc_attr_e(
                                   'A Google API key is required to use the Map block without any warning.',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Google API Key', 'advanced-gutenberg') ?>
                        </label>
                        <span style="display: block; float: none;">
                            <input type="text"
                                   name="google_api_key"
                                   id="google_api_key"
                                   class="ju-input"
                                   style="margin-left: 10px; width: 370px; display: block; max-width: 100%"
                                   value="<?php echo esc_html($google_api_key_saved) ?>"
                            >
                            <a target="_blank"
                               href="https://developers.google.com/maps/documentation/javascript/get-api-key"
                               style="display: inline-block; margin: 15px; margin-left: 10px; color: #655997; line-height: 1;">
                                <?php esc_html_e('How to create a Google API Key', 'advanced-gutenberg') ?>
                            </a>
                        </span>
                    </div>
                </li>

                <li class="ju-settings-option settings-separator">
                    <h2 class="settings-separator-title">
                        <?php esc_html_e('Blocks Settings', 'advanced-gutenberg') ?>
                    </h2>
                </li>

                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="enable_blocks_spacing"
                               class="advgb_qtip ju-setting-label"
                               data-qtip="<?php esc_attr_e(
                                   'Enable block spacing settings',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Enable blocks spacing', 'advanced-gutenberg') ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" name="enable_blocks_spacing"
                                       id="enable_blocks_spacing"
                                       value="1"
                                    <?php echo esc_attr($enable_blocks_spacing) ?>
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix" id="blocks_spacing_wrapper">
                    <div class="settings-option-wrapper clearfix">
                        <label for="blocks_spacing"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'Apply a minimal vertical block spacing automatically. Default is None. Values in pixels',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Blocks spacing', 'advanced-gutenberg') ?>
                            <span> (px)</span>
                        </label>
                        <span>
                            <input type="number"
                                   min="0"
                                   name="blocks_spacing"
                                   id="blocks_spacing"
                                   class="ju-input"
                                   style="margin-left: 10px; width: 80px"
                                   value="<?php echo esc_html($blocks_spacing) ?>"
                            >
                        </span>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="blocks_icon_color"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'Set color for blocks icons on admin, only apply to PublishPress Blocks',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Blocks icon color', 'advanced-gutenberg') ?>
                        </label>
                        <span>
                            <input type="text"
                                   name="blocks_icon_color"
                                   id="blocks_icon_color"
                                   class="ju-input minicolors minicolors-input"
                                   value="<?php echo esc_html($blocks_icon_color) ?>"/>
                        </span>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="editor_width"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'Define the admin Gutenberg editor width size',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Editor width', 'advanced-gutenberg') ?>
                        </label>
                        <div>
                            <select class="ju-select" name="editor_width" id="editor_width">
                                <option value="" <?php echo $editor_width === '' ? 'selected' : '' ?>>Original</option>
                                <option value="75" <?php echo $editor_width === '75' ? 'selected' : '' ?>>Large</option>
                                <option value="95" <?php echo $editor_width === '95' ? 'selected' : '' ?>>Full width</option>
                            </select>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="editor_width"
                               class="ju-setting-label advgb_qtip"
                               data-qtip="<?php esc_attr_e(
                                   'Set the default post thumbnail to use in Recent Posts blocks.',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Default thumbnail', 'advanced-gutenberg') ?>
                        </label>
                        <div class="setting-actions-wrapper">
                            <input type="hidden" id="post_default_thumb" name="post_default_thumb" value="<?php echo esc_attr($rp_default_thumb['url']); ?>" />
                            <input type="hidden" id="post_default_thumb_id" name="post_default_thumb_id" value="<?php echo esc_attr($rp_default_thumb['id']); ?>" />
                            <div class="setting-actions" id="post_default_thumb_actions">
                                <img class="thumb-selected"
                                     src="<?php echo esc_attr($rp_default_thumb['url']); ?>"
                                     alt="thumb"
                                     data-default="<?php echo esc_attr($default_thumb); ?>"
                                />
                                <i class="dashicons dashicons-edit ju-button" id="thumb_edit" title="<?php esc_html_e('Edit', 'advanced-gutenberg'); ?>"></i>
                                <i class="dashicons dashicons-no ju-button orange-button" id="thumb_remove" title="<?php esc_html_e('Reset to default', 'advanced-gutenberg'); ?>"></i>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="ju-settings-option clearfix">
                    <div class="settings-option-wrapper clearfix">
                        <label for="enable_columns_visual_guide"
                               class="advgb_qtip ju-setting-label"
                               data-qtip="<?php esc_attr_e(
                                   'Enable border to materialize PublishPress Blocks Column block',
                                   'advanced-gutenberg'
                               ) ?>"
                        >
                            <?php esc_html_e('Enable columns visual guide', 'advanced-gutenberg') ?>
                        </label>
                        <div class="ju-switch-button">
                            <label class="switch">
                                <input type="checkbox" name="enable_columns_visual_guide"
                                       id="enable_columns_visual_guide"
                                       value="1"
                                    <?php echo esc_attr($enable_columns_visual_guide) ?>
                                />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="save-settings-block">
                <button type="submit"
                        class="button button-primary pp-primary-button"
                        id="save-settings"
                        name="save_settings"
                >
                    <span><?php esc_html_e('Save', 'advanced-gutenberg') ?></span>
                </button>
            </div>
        </form>
    </div>

    <div id="block-config-tab" class="tab-content clearfix">
        <div class="advgb-search-wrapper">
            <input type="text"
                   class="advgb-search-input blocks-config-search"
                   placeholder="<?php esc_html_e('Search blocks', 'advanced-gutenberg') ?>"
            >
            <i class="mi mi-search"></i>
        </div>
        <ul class="blocks-config-list clearfix">
            <?php foreach ($advgb_blocks as $block) : ?>
                <?php $iconColor = '';
                if (in_array($block['name'], $excluded_blocks_config)) {
                    continue;
                }
                if (isset($block['iconColor'])) :
                    $iconColor = 'style=color:' . $block['iconColor'];
                endif; ?>
            <li class="block-config-item ju-settings-option" title="<?php echo esc_attr($block['title']); ?>">
                <span class="block-icon" <?php echo esc_attr($iconColor) ?>>
                    <?php echo html_entity_decode(html_entity_decode(stripslashes($block['icon']))); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped ?>
                </span>
                <span class="block-title"><?php echo esc_html($block['title']); ?></span>
                <i class="mi mi-settings block-config-button"
                   title="<?php esc_html_e('Edit', 'advanced-gutenberg') ?>"
                   data-block="<?php echo esc_attr($block['name']); ?>"
                ></i>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if (count($advgb_blocks) === 0) : ?>
            <div class="blocks-not-loaded" style="text-align: center">
                <p><?php esc_html_e('We are updating blocks list...', 'advanced-gutenberg'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
