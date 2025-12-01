<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class TRP_Language_Switcher_Tab
 *
 * Extracts language-switcher settings (with backwards-compat migration),
 * then renders the single Vue mount point carrying the serialized config.
 */
class TRP_Language_Switcher_Tab {
    private array $settings;

    /**
     *
     * @param array $settings
     */
    public function __construct( array $settings ) {
        $this->settings = $settings;

        add_filter( 'trp_settings_tabs', [$this, 'add_tab_to_navigation'] );
        add_action( 'admin_menu', [$this, 'add_submenu_page'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_assets'] );

        add_action( 'wp_ajax_trp_language_switcher_save', [ $this, 'ajax_save_language_switcher' ] );
        add_action( 'wp_ajax_trp_disable_legacy_language_switcher', [ $this, 'ajax_disable_legacy_language_switcher' ] );
    }

    /**
     * Retrieve the nested language_switcher config.
     * - If option is missing/empty: seed with defaults.
     * - If option exists but misses keys: complete only the missing keys (deep), then update option.
     *
     * @return array
     */
    public function get_initial_config(): array {
        $saved    = get_option( 'trp_language_switcher_settings', null );
        $defaults = self::default_switcher_config();
        $changed  = false;

        // Option is not set or is invalid - use defaults
        if ( !is_array( $saved ) || empty( $saved ) ) {
            update_option( 'trp_language_switcher_settings', $defaults );

            return $defaults;
        }

        // Recursive merge that only fills missing keys
        $merge_missing = static function ( array $have, array $defaults ) use ( &$changed, &$merge_missing ): array {
            foreach ( $defaults as $key => $def_val ) {
                if ( !array_key_exists( $key, $have ) ) {
                    $have[ $key ] = $def_val;
                    $changed = true;

                    continue;
                }

                if ( is_array( $def_val ) && is_array( $have[ $key ] ) ) {
                    $have[ $key ] = $merge_missing( $have[ $key ], $def_val );
                }
            }

            return $have;
        };

        $completed = $merge_missing( $saved, $defaults );

        if ( $changed )
            update_option( 'trp_language_switcher_settings', $completed );

        return $completed;
    }

    /**
     * Return the complete default Language Switcher config (all scopes).
     *
     * @return array
     */
    public static function default_switcher_config(): array {
        $layout_customizer_default_map = [
            'floater' => [
                'position'         => 'bottom-right',
                'width'            => 'default',
                'customWidth'      => 216,
                'padding'          => 'default',
                'customPadding'    => 0,
                'flagIconPosition' => 'before',
                'languageNames'    => 'full',
            ],
            'shortcode' => [
                'flagIconPosition' => 'before',
                'languageNames'    => 'full'
            ],
            'menu' => [
                'flagIconPosition' => 'before',
                'languageNames'    => 'full',
                'flagShape'        => 'rect'
            ]
        ];

        $layoutCustomizerDefault = [
            'floater' => [
                'desktop' => $layout_customizer_default_map['floater'],
                'mobile'  => $layout_customizer_default_map['floater']
            ],
            'shortcode' => [
                'desktop' => $layout_customizer_default_map['shortcode'],
                'mobile'  => $layout_customizer_default_map['shortcode']
            ],
            'menu' => [
                'desktop' => $layout_customizer_default_map['menu'],
                'mobile'  => $layout_customizer_default_map['menu']
            ]
        ];

        return [
            'floater' => [
                'enabled'           => true,
                'type'              => 'dropdown',
                'bgColor'           => '#ffffff',
                'bgHoverColor'      => '#0000000d',
                'textColor'         => '#143852',
                'textHoverColor'    => '#1d2327',
                'borderColor'       => '#1438521a',
                'borderWidth'       => 1,
                'borderRadius'      => [8, 8, 0, 0],
                'size'              => 'normal',
                'flagShape'         => 'rect',
                'flagRadius'        => 2,
                'enableCustomCss'   => false,
                'customCss'         => '',
                'oppositeLanguage'  => false,
                'showPoweredBy'     => false,
                'layoutCustomizer'  => $layoutCustomizerDefault['floater'],
                'enableTransitions' => true,
            ],
            'shortcode' => [
                'bgColor'           => '#ffffff',
                'bgHoverColor'      => '#0000000d',
                'textColor'         => '#143852',
                'textHoverColor'    => '#1d2327',
                'borderColor'       => '#1438521a',
                'borderWidth'       => 1,
                'borderRadius'      => 5,
                'size'              => 'normal',
                'flagShape'         => 'rect',
                'flagRadius'        => 2,
                'enableCustomCss'   => false,
                'customCss'         => '',
                'clickLanguage'     => false,
                'layoutCustomizer'  => $layoutCustomizerDefault['shortcode'],
                'enableTransitions' => true,
                'oppositeLanguage'  => false
            ],
            'menu' => [
                'layoutCustomizer' => $layoutCustomizerDefault['menu'],
            ],
        ];
    }

    /**
     * AJAX: Save one language-switcher scope.
     * Action: trp_language_switcher_save
     */
    public function ajax_save_language_switcher(): void {
        if ( ! current_user_can( apply_filters( 'trp_settings_capability', 'manage_options' ) ) )
            wp_send_json_error( __( 'Permission denied.', 'translatepress-multilingual' ), 403 );

        $nonce = isset( $_POST['nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
            : '';

        if ( ! wp_verify_nonce( $nonce, 'trp_language_switcher_save' ) )
            wp_send_json_error( __( 'Invalid nonce.', 'translatepress-multilingual' ), 403 );

        $scope  = sanitize_key( wp_unslash( $_POST['scope'] ?? '' ) );

        $config_raw = isset( $_POST['config'] ) ? wp_unslash( $_POST['config'] ) : '{}'; //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $config_str = is_string( $config_raw ) ? $config_raw : '{}';
        $config     = json_decode( $config_str, true );

        $allowed_scopes = [ 'floater', 'shortcode', 'menu' ];

        if ( ! in_array( $scope, $allowed_scopes, true ) || ! is_array( $config ) )
            wp_send_json_error( __( 'Settings scope unknown.', 'translatepress-multilingual' ), 400 );

        $sanitised = $this->sanitize_scope_config( $scope, $config );

        $options = get_option( 'trp_language_switcher_settings', [] );

        $options[ $scope ] = $sanitised;

        update_option( 'trp_language_switcher_settings', $options );

        wp_send_json_success( __( 'Settings saved.', 'translatepress-multilingual' ) );
    }

    /**
     * AJAX: disable the legacy Language Switcher.
     *
     * Validates capability and nonce, sets
     * trp_advanced_settings['load_legacy_language_switcher'] = 'no',
     * and returns a JSON response.
     *
     * Expects POST: 'nonce' for action 'trp_disable_legacy'.
     *
     * @return void
     */
    public function ajax_disable_legacy_language_switcher(): void {
        if ( ! current_user_can( apply_filters( 'trp_settings_capability', 'manage_options' ) ) ) {
            wp_send_json_error( __( 'Permission denied.', 'translatepress-multilingual' ), 403 );
        }

        $nonce = isset( $_POST['nonce'] )
            ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
            : '';

        if ( ! wp_verify_nonce( $nonce, 'trp_disable_legacy' ) ) {
            wp_send_json_error( __( 'Invalid nonce.', 'translatepress-multilingual' ), 403 );
        }

        $adv = get_option( 'trp_advanced_settings', [] );
        if ( ! is_array( $adv ) ) {
            $adv = [];
        }

        // Flip legacy OFF
        $adv['load_legacy_language_switcher'] = 'no';
        update_option( 'trp_advanced_settings', $adv );

        TRP_Plugin_Notifications::get_instance()->dismiss_notification( 'trp_ls_v2_intro' );

        wp_send_json_success( __( 'Legacy disabled.', 'translatepress-multilingual' ) );
    }


    /**
     * Sanitise a single-scope config using allow-listed rules.
     *
     * @param string $scope  floater|shortcode|menu
     * @param array  $data   Incoming config
     * @return array
     */
    private function sanitize_scope_config( string $scope, array $data ): array {
        /* Rule map  */
        $rules = [
            'floater' => [
                'enabled'           => 'bool',
                'type'              => 'text',
                'bgColor'           => 'color',
                'bgHoverColor'      => 'color',
                'textColor'         => 'color',
                'textHoverColor'    => 'color',
                'borderColor'       => 'color',
                'borderWidth'       => 'int',
                'borderRadius'      => 'int_array',
                'size'              => 'text',
                'flagShape'         => 'text',
                'flagRadius'        => 'int',
                'enableCustomCss'   => 'bool',
                'customCss'         => 'css',
                'oppositeLanguage'  => 'bool',
                'layoutCustomizer'  => 'layoutCustomizer',
                'showPoweredBy'     => 'bool',
                'enableTransitions' => 'bool'
            ],
            'shortcode' => [
                'bgColor'           => 'color',
                'bgHoverColor'      => 'color',
                'textColor'         => 'color',
                'textHoverColor'    => 'color',
                'borderColor'       => 'color',
                'borderWidth'       => 'int',
                'borderRadius'      => 'int',
                'size'              => 'text',
                'flagShape'         => 'text',
                'flagRadius'        => 'int',
                'enableCustomCss'   => 'bool',
                'customCss'         => 'css',
                'layoutCustomizer'  => 'layoutCustomizer',
                'clickLanguage'     => 'bool',
                'enableTransitions' => 'bool',
                'oppositeLanguage'  => 'bool'
            ],
            'menu' => [
                'flagShape'        => 'text',
                'flagIconPosition' => 'text',
                'languageNames'    => 'text',
                'layoutCustomizer' => 'layoutCustomizer',
            ],
        ];

        /* sanitiser callbacks */
        $filters = [
            'bool'             => static fn ( $v ) => (bool) $v,
            'int'              => 'intval',
            'text'             => 'sanitize_text_field',
            'color'            => static function ( $v ) {
                $v = trim( strtolower( $v ) );

                if ( $v === 'transparent' )
                    return $v;

                // Allow 4, 5, 7 or 9-character hex codes (with #)
                if ( preg_match( '/^#(?:[0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $v ) )
                    return $v;

                return '';
            },
            'int_array'        => static fn ( $v ) => array_map( 'intval', (array) $v ),
            'css'              => [ $this, 'sanitize_custom_css' ],
            'layoutCustomizer' => [ $this, 'sanitize_layout_customizer' ],
        ];

        /* iterate */
        $out = [];
        foreach ( $rules[ $scope ] as $key => $rule ) {
            if ( array_key_exists( $key, $data ) ) {
                $out[ $key ] = $filters[ $rule ]( $data[ $key ] );
            }
        }
        return $out;
    }

    /**
     * Sanitize a raw CSS string for storage.
     *
     * - Rejects any HTML markup.
     * - Removes dangerous CSS constructs.
     *
     * @param string $css Raw user input.
     * @return string Sanitized CSS (or empty string on fatal error).
     */
    public function sanitize_custom_css( string $css ): string {
        // Reject any HTML tags
        if ( preg_match( '#</?\w+#', $css ) )
            return '';

        // Neutralize CSS expressions & javascript: URLs
        $css = preg_replace( '/expression\s*\([^)]*\)/i', '', $css );
        $css = preg_replace(
            '/url\s*\(\s*[\'"]?\s*javascript:[^)]*\)/i',
            'url("about:blank")',
            $css
        );

        $css = preg_replace( '/@import\s+[^;]+;/i', '', $css );

        return trim( $css );
    }

    /**
     * Sanitises the layoutCustomizer object for both desktop and mobile.
     *
     * @param mixed $value
     * @return array
     */
    private function sanitize_layout_customizer( array $value ): array {
        $out = [
            'desktop' => [],
            'mobile'  => []
        ];

        $int_fields  = [ 'customWidth', 'customPadding' ];
        $text_fields = [ 'position', 'width', 'padding', 'flagIconPosition', 'languageNames', 'flagShape' ];

        foreach ( [ 'desktop', 'mobile' ] as $device ) {
            foreach ( array_merge( $int_fields, $text_fields ) as $key ) {
                if ( ! isset( $value[ $device ][ $key ] ) ) continue;

                $raw = $value[ $device ][ $key ];

                if ( in_array( $key, $int_fields, true ) ) {
                    $out[ $device ][ $key ] = (int) $raw;
                } else {
                    $out[ $device ][ $key ] = sanitize_text_field( $raw );
                }
            }
        }

        return $out;
    }

    /**
     * Adds the Language Switcher tab
     *
     * Hooked: trp_settings_tabs
     *
     * @param array $tabs
     * @return array
     */
    public function add_tab_to_navigation( array $tabs ): array {
        $tab = [
            'name'  => __( 'Language Switcher', 'translatepress-multilingual' ),
            'url'   => admin_url( 'admin.php?page=trp_language_switcher' ),
            'page'  => 'trp_language_switcher'
        ];

        array_splice( $tabs, 1, 0, [$tab] );

        return $tabs;
    }

    /**
     * Adds a hidden submenu page for TranslatePress language switcher tab
     *
     * Hooked: admin_menu
     */
    public function add_submenu_page(): void {
        add_submenu_page(
            'TRPHidden',
            __( 'Language Switcher', 'translatepress-multilingual' ),
            'TRPHidden',
            apply_filters( 'trp_settings_capability', 'manage_options' ),
            'trp_language_switcher',
            [$this, 'language_switcher_page_content']
        );
    }

    /**
     * Echoes the Vue mount point <div>.
     *
     * @return void
     */
    public function language_switcher_page_content(): void {
        require_once TRP_PLUGIN_DIR . 'partials/language-switcher-configurator-page.php';
    }

    /**
     * Build the data array sent to the Vue app via wp_localize_script().
     *
     * @return array
     */
    private function get_localize_payload(): array {
        $config = $this->get_initial_config();

        $trp                 = TRP_Translate_Press::get_trp_instance();
        $languages_component = $trp->get_component( 'languages' );

        $published_codes      = $this->settings['publish-languages'] ?? [];
        $short_language_names = $this->settings['url-slugs'] ?? [];
        $published            = [];

        $all_languages = $languages_component->get_language_names( $published_codes );

        foreach ( $published_codes as $code ) {
            if ( ! isset( $all_languages[ $code ] ) )
                continue;

            /** Custom language flag support */
            $flag_path = apply_filters( 'trp_flags_path', '', $code );

            $lang_data = [
                'name'      => $all_languages[ $code ],
                'shortName' => strtoupper( $short_language_names[ $code ] ),
            ];

            // Only include flagPath if it’s non-empty, works only for custom languages
            if ( ! empty( $flag_path ) )
                $lang_data['flagPath'] = $flag_path;

            $published[ $code ] = $lang_data;
        }

        $default_code = $this->settings['default-language'];
        $default_name = $all_languages[ $default_code ];

        return [
            'lsConfig'  => $config,
            'languages' => [
                'published' => $published,
                'default'   => [
                    'code' => $default_code,
                    'name' => $default_name,
                ],
            ],
            'misc'  => [
                'pluginUrl' => TRP_PLUGIN_URL
            ],
            'nonce' => wp_create_nonce( 'trp_language_switcher_save' ),
        ];
    }

    /**
     * Enqueue the Vue app script & CSS.
     * @param ?string $hook
     */
    public function enqueue_assets( $hook = null ): void {
        if ( !is_string( $hook ) && function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
            $hook   = $screen ? $screen->id : '';
        }

        if ( 'admin_page_trp_language_switcher' !== $hook )
            return;

        $script_url = TRP_PLUGIN_URL . 'assets/js/trp-lang-switcher-configurator.js';
        $style_url  = TRP_PLUGIN_URL . 'assets/css/trp-lang-switcher-configurator.css';

        $version = TRP_PLUGIN_VERSION;
        $script_handle = 'tp-lang-switcher-configurator';

        wp_enqueue_style(
            'tp-lang-switcher-configurator-style',
            $style_url,
            [],
            $version
        );

        wp_register_script(
            $script_handle,
            $script_url,
            [ 'wp-i18n', 'wp-element'],
            $version,
            true
        );

        wp_set_script_translations( $script_handle, 'translatepress-multilingual' );

        wp_localize_script( $script_handle, 'tpLangSwitcherData', $this->get_localize_payload() );

        wp_enqueue_script( $script_handle );
    }

    public function is_legacy_enabled(): bool {
        return ( $this->settings['trp_advanced_settings']['load_legacy_language_switcher'] ?? 'no' ) === 'yes';
    }

}
