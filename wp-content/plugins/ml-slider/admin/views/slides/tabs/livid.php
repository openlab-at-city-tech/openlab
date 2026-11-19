<?php
if (!defined('ABSPATH')) {
    die('No direct access.');
}

$mute_checked = isset($slide_settings['mute']) && $slide_settings['mute'] === 'on';
$controls_checked = ! isset($slide_settings['controls']) || $slide_settings['controls'] === 'on';
$autoplay_checked = isset($slide_settings['autoPlay']) && $slide_settings['autoPlay'] === 'on';
$loop_checked = isset($slide_settings['loop']) && $slide_settings['loop'] === 'on';
$lazy_load_checked = ! isset($slide_settings['lazyLoad']) || $slide_settings['lazyLoad'] === 'on';
?>
<div class="thumb-col-settings">
    <?php echo $this->get_admin_slide_thumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <div>
        <div class="row mb-2">
            <div class="mb-2">
                <label>
                    <?php /* translators: Livid is a video hosting service - keep the name as-is. */ esc_html_e('Livid Video URL', 'ml-slider') ?>
                    <span class="dashicons dashicons-info tipsy-tooltip-top" title="<?php esc_attr_e('Paste the link to a video hosted on livid.com. We will create the embed for you.', 'ml-slider') ?>" style="line-height: 19px;"></span>
                </label>
            </div>
            <input class="url livid_url" data-lpignore="true" data-slide-id="<?php echo esc_attr($slide_id); ?>" type="text" name="attachment[<?php echo esc_attr($slide_id); ?>][livid_url]" placeholder="https://livid.com/watch/IoMGtM7uIDai" value="<?php echo esc_url($livid_url); ?>" />
            <ul class="ms-split-li mt-2">
                <li>
                    <label>
                        <?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr($slide_id) . '][settings][mute]',
                            (bool) $mute_checked
                        );
                        ?>
                        <span><?php esc_html_e('Mute video', 'ml-slider') ?></span>
                    </label>
                </li>
                <li>
                    <label>
                        <?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr($slide_id) . '][settings][controls]',
                            (bool) $controls_checked
                        );
                        ?>
                        <span>
                            <?php
                            esc_html_e('Show controls', 'ml-slider');
                            /* translators: Livid is a video hosting service - keep the name as-is. */
                            echo $this->info_tooltip(__('Hiding the controls only works if the video is on a Livid Pro or Premium account', 'ml-slider')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label>
                        <?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr($slide_id) . '][settings][autoPlay]',
                            (bool) $autoplay_checked
                        );
                        ?>
                        <span>
                            <?php
                            esc_html_e('Auto play', 'ml-slider');
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            echo $this->info_tooltip(__('May require the video to be muted', 'ml-slider'));
                            ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label>
                        <?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr($slide_id) . '][settings][lazyLoad]',
                            (bool) $lazy_load_checked
                        );
                        ?>
                        <span>
                            <?php
                            esc_html_e('Lazy load video', 'ml-slider');
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            echo $this->info_tooltip(__('The video only loads once a visitor clicks play', 'ml-slider'));
                            ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label>
                        <?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr($slide_id) . '][settings][loop]',
                            (bool) $loop_checked
                        );
                        ?>
                        <span><?php esc_html_e('Loop video', 'ml-slider') ?></span>
                    </label>
                </li>
            </ul>
        </div>
    </div>
</div>
