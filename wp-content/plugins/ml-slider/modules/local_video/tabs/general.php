<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$sources = $this->videoHelper->get_all_sources( $this->slide->ID );

$mute_checked       = isset( $this->slide_settings['mute'] ) && filter_var(
                        $this->slide_settings['mute'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$controls_checked   = ! isset( $this->slide_settings['controls'] ) 
                    || $this->slide_settings['controls'] == 'on' 
                    ? true : false;
$autoPlay_checked   = isset( $this->slide_settings['autoPlay'] ) && filter_var(
                        $this->slide_settings['autoPlay'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$lazyLoad_checked   = isset( $this->slide_settings['lazyLoad'] ) && filter_var(
                        $this->slide_settings['lazyLoad'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$loop_checked       = isset($this->slide_settings['loop']) && filter_var(
                        $this->slide_settings['loop'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$fit_checked        = isset($this->slide_settings['fit']) && filter_var(
                        $this->slide_settings['fit'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$fade_checked       = isset($this->slide_settings['fade']) && filter_var(
                        $this->slide_settings['fade'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
?>
<div class="thumb-col-settings">
    <div class="metaslider-ui-inner metaslider-slide-thumb">
        <div class="thumb">
            <?php echo $this->videoHelper->get_admin_video_embed( $sources ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    </div>
    <div>
        <div class="list-video-sources">
        <?php 
        foreach ( $sources as $video ) :
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->videoHelper->admin_video_source_row( $video, $this->slide->ID );
        endforeach; 
        ?>
        </div>

        <div class="row mb-0"<?php echo count( $sources ) < 3 ? '' : ' style="display:none;"' ?>>
            <button data-slide-type="local_video" data-button-text="<?php 
                esc_attr_e( 'Add a new video source', 'ml-slider' ) ?>" data-slide-id="<?php
                esc_attr_e( $this->slide->ID ) ?>" class="add-video-source button button-secondary">
                <?php esc_html_e( 'Add a new video source', 'ml-slider' ); ?>
            </button>
            <span class="dashicons dashicons-info tipsy-tooltip-top" original-title="<?php 
                esc_attr_e(
                    'Adding more formats for the same video can help improve support for different platforms.', 
                    'ml-slider'
                ) ?>" style="line-height:50px;"></span>
        </div>
        <div class="row mt-2">
            <ul class="ms-split-li">
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][mute]',
                            (bool) $mute_checked
                        );
                        ?><span>
                            <?php esc_html_e( 'Mute video', 'ml-slider' ); ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][controls]',
                            (bool) $controls_checked
                        );
                        ?><span>
                            <?php esc_html_e( 'Show controls', 'ml-slider' ); ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][autoPlay]',
                            (bool) $autoPlay_checked
                        );
                        ?><span>
                            <?php 
                            esc_html_e( 'Auto play', 'ml-slider' );
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            echo $this->info_tooltip( __(
                                'May require video to be muted', 
                                'ml-slider'
                            ) );
                            ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][lazyLoad]',
                            (bool) $lazyLoad_checked
                        );
                        ?> <span>
                            <?php esc_html_e( 'Lazy load video', 'ml-slider' ); ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][loop]',
                            (bool) $loop_checked
                        );
                        ?><span>
                            <?php esc_html_e( 'Loop video', 'ml-slider' ); ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][fit]',
                            (bool) $fit_checked
                        );
                        ?><span>
                            <?php esc_html_e( 'Fit video in container', 'ml-slider' ); ?>
                        </span>
                    </label>
                </li>
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][fade]',
                            (bool) $fade_checked
                        );
                        ?><span>
                            <?php 
                            esc_html_e( 'Fade in transition', 'ml-slider' );
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            echo $this->info_tooltip( __(
                                'Fade in before video loads. Recommended when slide and slideshow Auto play are enabled, and slideshow Transition effect is fade.',
                                'ml-slider'
                            ) );
                            ?>
                        </span>
                    </label>
                </li>
            </ul>
        </div>
    </div>
</div>
