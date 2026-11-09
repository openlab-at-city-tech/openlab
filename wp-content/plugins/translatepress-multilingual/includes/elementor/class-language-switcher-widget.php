<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class TRP_Elementor_Language_Switcher_Widget extends Widget_Base {
    public function get_name() {
        return 'trp_language_switcher';
    }

    public function get_title() {
        return __( 'Language Switcher', 'translatepress-multilingual' );
    }

    public function get_icon() {
        return 'dashicons dashicons-translation';
    }

    public function get_categories() {
        return [ 'translatepress' ];
    }

    public function get_keywords() {
        return [ 'translatepress', 'language', 'switcher', 'translation', 'multilingual' ];
    }

    public function get_style_depends() {
        return [ 'trp-language-switcher-v2' ];
    }

    public function get_script_depends() {
        return [ 'trp-language-switcher-js-v2' ];
    }

    protected function register_controls() {
        $custom_settings_condition = [
            'trp_style_source' => 'custom',
        ];

        $this->start_controls_section(
            'trp_language_switcher_content',
            [
                'label' => __( 'Language Switcher', 'translatepress-multilingual' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        if ( $this->is_legacy_switcher_enabled() ) {
            $this->add_control(
                'trp_legacy_switcher_notice',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => $this->get_legacy_switcher_notice_html(),
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'trp_language_switcher_style',
                [
                    'label' => __( 'Style', 'translatepress-multilingual' ),
                    'tab'   => Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'trp_legacy_switcher_style_notice',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => $this->get_legacy_switcher_notice_html(),
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                ]
            );

            $this->end_controls_section();

            return;
        }

        $this->add_control(
            'trp_style_source',
            [
                'label'   => __( 'Settings', 'translatepress-multilingual' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'global',
                'options' => [
                    'global' => __( 'Use shortcode settings', 'translatepress-multilingual' ),
                    'custom' => __( 'Custom', 'translatepress-multilingual' ),
                ],
            ]
        );

        $this->add_responsive_control(
            'trp_language_names',
            [
                'label'     => __( 'Language names', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::SELECT,
                'devices'   => [ 'desktop', 'mobile' ],
                'default'   => 'full',
                'options'   => [
                    'full'  => __( 'Full names', 'translatepress-multilingual' ),
                    'short' => __( 'Short names', 'translatepress-multilingual' ),
                    'none'  => __( 'Hide names', 'translatepress-multilingual' ),
                ],
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_responsive_control(
            'trp_flag_position',
            [
                'label'     => __( 'Flag position', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::SELECT,
                'devices'   => [ 'desktop', 'mobile' ],
                'default'   => 'before',
                'options'   => [
                    'before' => __( 'Before text', 'translatepress-multilingual' ),
                    'after'  => __( 'After text', 'translatepress-multilingual' ),
                    'hide'   => __( 'Hide flags', 'translatepress-multilingual' ),
                ],
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_open_on_click',
            [
                'label'        => __( 'Open on click', 'translatepress-multilingual' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
                'condition'    => $custom_settings_condition,
            ]
        );

        if ( $this->has_exactly_two_published_languages() ) {
            $this->add_control(
                'trp_opposite_language',
                [
                    'label'        => __( 'Show opposite language', 'translatepress-multilingual' ),
                    'type'         => Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default'      => '',
                    'condition'    => $custom_settings_condition,
                ]
            );
        }

        $this->add_control(
            'trp_size',
            [
                'label'     => __( 'Size', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'normal',
                'options'   => [
                    'normal' => __( 'Normal', 'translatepress-multilingual' ),
                    'large'  => __( 'Large', 'translatepress-multilingual' ),
                ],
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_flag_shape',
            [
                'label'     => __( 'Flag shape', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'rect',
                'options'   => [
                    'rect'    => __( 'Rectangle', 'translatepress-multilingual' ),
                    'square'  => __( 'Square', 'translatepress-multilingual' ),
                    'rounded' => __( 'Rounded', 'translatepress-multilingual' ),
                ],
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_enable_transitions',
            [
                'label'        => __( 'Transitions', 'translatepress-multilingual' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => $custom_settings_condition,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'trp_language_switcher_style',
            [
                'label' => __( 'Style', 'translatepress-multilingual' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'trp_shortcode_settings_notice',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => $this->get_shortcode_settings_notice_html(),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition'       => [
                    'trp_style_source' => 'global',
                ],
            ]
        );

        $this->add_control(
            'trp_bg_color',
            [
                'label'     => __( 'Background', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#ffffff',
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_bg_hover_color',
            [
                'label'     => __( 'Hover background', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#0000000d',
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_text_color',
            [
                'label'     => __( 'Text', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#143852',
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_text_hover_color',
            [
                'label'     => __( 'Hover text', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1d2327',
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_border_color',
            [
                'label'     => __( 'Border color', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#1438521a',
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_border_width',
            [
                'label'     => __( 'Border width', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                    'unit' => 'px',
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_border_radius',
            [
                'label'     => __( 'Border radius', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'condition' => $custom_settings_condition,
            ]
        );

        $this->add_control(
            'trp_flag_radius',
            [
                'label'     => __( 'Flag radius', 'translatepress-multilingual' ),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 2,
                    'unit' => 'px',
                ],
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'condition' => $custom_settings_condition,
            ]
        );

        $this->end_controls_section();
    }

    protected function _register_controls() {
        $this->register_controls();
    }

    private function get_legacy_switcher_notice_html(): string {
        return sprintf(
            /* translators: %s: URL to the TranslatePress language switcher settings page. */
            __( 'Disable the legacy TranslatePress language switcher to use the customizable Elementor widget. <a href="%s" target="_blank" rel="noopener noreferrer">Open language switcher settings</a>.', 'translatepress-multilingual' ),
            esc_url( admin_url( 'admin.php?page=trp_language_switcher' ) )
        );
    }

    private function get_shortcode_settings_notice_html(): string {
        return sprintf(
            /* translators: %s: URL to the TranslatePress shortcode language switcher settings page. */
            __( 'The language switcher inherits styling options from shortcode. <a href="%s" target="_blank" rel="noopener noreferrer">Open shortcode settings</a>.', 'translatepress-multilingual' ),
            esc_url( admin_url( 'admin.php?page=trp_language_switcher#/shortcode' ) )
        );
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $switcher = $this->get_v2_switcher();

        if ( ! $switcher ) {
            $this->render_legacy_fallback();
            return;
        }

        $switcher->enqueue_assets();

        $is_editor = $this->is_elementor_editor();
        $config    = ( isset( $settings['trp_style_source'] ) && $settings['trp_style_source'] === 'custom' )
            ? $this->build_switcher_config( $settings )
            : [];

        if ( $is_editor ) {
            $this->render_editor_responsive_switchers( $switcher, $config );
            return;
        }

        echo '<div class="trp-elementor-language-switcher">';
        echo $switcher->render_inline_switcher( $config, $is_editor, 'elementor' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
    }

    private function render_editor_responsive_switchers( TRP_Language_Switcher_V2 $switcher, array $config ): void {
        $mobile_breakpoint = $this->get_elementor_mobile_breakpoint();
        $widget_id         = 'trp-elementor-language-switcher-' . $this->get_id();

        echo '<style>';
        echo '#' . esc_attr( $widget_id ) . ' .trp-elementor-language-switcher--mobile{display:none;}';
        echo '@media (max-width:' . esc_attr( (string) $mobile_breakpoint ) . 'px){';
        echo '#' . esc_attr( $widget_id ) . ' .trp-elementor-language-switcher--desktop{display:none;}';
        echo '#' . esc_attr( $widget_id ) . ' .trp-elementor-language-switcher--mobile{display:block;}';
        echo '}';
        echo '</style>';

        echo '<div id="' . esc_attr( $widget_id ) . '" class="trp-elementor-language-switcher trp-elementor-language-switcher--editor">';
        echo '<div class="trp-elementor-language-switcher--desktop">';
        echo $switcher->render_inline_switcher( $config, true, 'elementor', 'desktop' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
        echo '<div class="trp-elementor-language-switcher--mobile">';
        echo $switcher->render_inline_switcher( $config, true, 'elementor', 'mobile' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
        echo '</div>';
    }

    private function get_elementor_mobile_breakpoint(): int {
        if (
            class_exists( '\Elementor\Plugin' )
            && isset( \Elementor\Plugin::$instance->breakpoints )
            && method_exists( \Elementor\Plugin::$instance->breakpoints, 'get_active_breakpoints' )
        ) {
            $mobile_breakpoint = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints( 'mobile' );

            if ( is_object( $mobile_breakpoint ) && method_exists( $mobile_breakpoint, 'get_value' ) ) {
                return max( 1, (int) $mobile_breakpoint->get_value() );
            }
        }

        return 767;
    }

    private function get_v2_switcher() {
        $trp = TRP_Translate_Press::get_trp_instance();

        if ( $trp->get_component( 'language_switcher_tab' )->is_legacy_enabled() ) {
            return null;
        }

        try {
            return TRP_Language_Switcher_V2::instance();
        } catch ( RuntimeException $exception ) {
            $settings = $trp->get_component( 'settings' )->get_settings();
            return TRP_Language_Switcher_V2::instance( $settings, $trp );
        }
    }

    private function render_legacy_fallback() {
        echo '<div class="trp-elementor-language-switcher">';
        echo do_shortcode( '[language-switcher]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
    }

    private function is_legacy_switcher_enabled(): bool {
        if ( ! class_exists( 'TRP_Translate_Press' ) ) {
            return false;
        }

        $trp = TRP_Translate_Press::get_trp_instance();

        return $trp->get_component( 'language_switcher_tab' )->is_legacy_enabled();
    }

    private function has_exactly_two_published_languages(): bool {
        if ( ! class_exists( 'TRP_Translate_Press' ) ) {
            return false;
        }

        $trp      = TRP_Translate_Press::get_trp_instance();
        $settings = $trp->get_component( 'settings' )->get_settings();

        $published_languages = isset( $settings['publish-languages'] ) && is_array( $settings['publish-languages'] )
            ? array_values( array_filter( $settings['publish-languages'] ) )
            : [];

        return count( $published_languages ) === 2;
    }

    private function build_switcher_config( array $settings ): array {
        $flag_position = $this->responsive_select_values( $settings, 'trp_flag_position', [ 'before', 'after', 'hide' ], 'before' );
        $language_names = $this->responsive_select_values( $settings, 'trp_language_names', [ 'full', 'short', 'none' ], 'full' );

        return [
            'bgColor'           => $this->sanitize_color_value( $settings['trp_bg_color'] ?? '#ffffff', '#ffffff' ),
            'bgHoverColor'      => $this->sanitize_color_value( $settings['trp_bg_hover_color'] ?? '#0000000d', '#0000000d' ),
            'textColor'         => $this->sanitize_color_value( $settings['trp_text_color'] ?? '#143852', '#143852' ),
            'textHoverColor'    => $this->sanitize_color_value( $settings['trp_text_hover_color'] ?? '#1d2327', '#1d2327' ),
            'borderColor'       => $this->sanitize_color_value( $settings['trp_border_color'] ?? '#1438521a', '#1438521a' ),
            'borderWidth'       => $this->slider_value( $settings['trp_border_width'] ?? [], 1 ),
            'borderRadius'      => $this->slider_value( $settings['trp_border_radius'] ?? [], 5 ),
            'size'              => $this->sanitize_select_value( $settings['trp_size'] ?? 'normal', [ 'normal', 'large' ], 'normal' ),
            'flagShape'         => $this->sanitize_select_value( $settings['trp_flag_shape'] ?? 'rect', [ 'rect', 'square', 'rounded' ], 'rect' ),
            'flagRadius'        => $this->slider_value( $settings['trp_flag_radius'] ?? [], 2 ),
            'clickLanguage'     => ( $settings['trp_open_on_click'] ?? '' ) === 'yes',
            'enableTransitions' => ( $settings['trp_enable_transitions'] ?? '' ) === 'yes',
            'oppositeLanguage'  => $this->has_exactly_two_published_languages() && ( $settings['trp_opposite_language'] ?? '' ) === 'yes',
            'layoutCustomizer'  => [
                'desktop' => [
                    'flagIconPosition' => $flag_position['desktop'],
                    'languageNames'    => $language_names['desktop'],
                ],
                'mobile'  => [
                    'flagIconPosition' => $flag_position['mobile'],
                    'languageNames'    => $language_names['mobile'],
                ],
            ],
        ];
    }

    private function responsive_select_values( array $settings, string $control_name, array $allowed, string $default ): array {
        $desktop = $this->sanitize_select_value( $settings[ $control_name ] ?? $default, $allowed, $default );
        $mobile  = $this->sanitize_select_value( $settings[ $control_name . '_mobile' ] ?? $desktop, $allowed, $desktop );

        return [
            'desktop' => $desktop,
            'mobile'  => $mobile,
        ];
    }

    private function sanitize_select_value( $value, array $allowed, string $default ): string {
        $value = is_scalar( $value ) ? (string) $value : '';

        return in_array( $value, $allowed, true ) ? $value : $default;
    }

    private function sanitize_color_value( $value, string $default ): string {
        $value = is_scalar( $value ) ? trim( strtolower( (string) $value ) ) : '';

        if ( $value === 'transparent' || preg_match( '/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $value ) ) {
            return $value;
        }

        return $default;
    }

    private function slider_value( $value, int $default ): int {
        if ( is_array( $value ) && isset( $value['size'] ) && is_numeric( $value['size'] ) ) {
            return max( 0, (int) $value['size'] );
        }

        if ( is_numeric( $value ) ) {
            return max( 0, (int) $value );
        }

        return $default;
    }

    private function is_elementor_editor(): bool {
        return class_exists( '\Elementor\Plugin' )
            && isset( \Elementor\Plugin::$instance->editor )
            && \Elementor\Plugin::$instance->editor->is_edit_mode();
    }
}
