<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class that renders the Vue-configured language switchers.
 *
 * Relies on the config stored in $this->settings['language-switcher']:
 *  floater    (array)  – settings for the floating switcher
 *  shortcode  (array)  – settings for [language-switcher] shortcode
 *  menu       (array)  – settings for menu items
 */
class TRP_Language_Switcher_V2 {
    private array $settings;
    private array $config;
    private TRP_Translate_Press $trp;
    private TRP_Url_Converter $url_converter;
    private TRP_Languages $languages;
    private ?string $current_lang ;
    private static ?self  $instance = null;
    /**
     * @var 'desktop' | 'mobile'
     */
    private string $viewport;

    /**
     * Get singleton instance.
     *
     * @param null $settings
     * @param null $trp
     * @return self
     */
    public static function instance( $settings = null, $trp = null ): self {
        if ( self::$instance === null ) {
            if ( $settings === null || $trp === null )
                throw new RuntimeException( 'TRP_Language_Switcher_V2::instance() requires $settings and $trp when called manually.' );

            self::$instance = new self( $settings, $trp );
        }

        return self::$instance;
    }

    /**
     * @param array $settings TRP settings.
     * @param TRP_Translate_Press $trp TRP root instance.
     */
    private function __construct( array $settings, TRP_Translate_Press $trp ) {
        $this->settings      = $settings;
        $this->url_converter = $trp->get_component( 'url_converter' );
        $this->languages     = $trp->get_component( 'languages' );
        $this->trp           = $trp;
        $this->viewport      = wp_is_mobile() ? 'mobile' : 'desktop';

        $ls_option = get_option( 'trp_language_switcher_settings' );

        $this->config = $ls_option !== false ? $ls_option : [];
    }

    /**
     * Initialize language switcher functionalities
     *
     * Hooked on init 1
     *
     * @return void
     */
    public function init() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'wp_footer', [ $this, 'render_floater' ], 99 );
        add_shortcode( 'language-switcher', [ $this, 'render_shortcode' ] );
        add_filter( 'wp_get_nav_menu_items', [ $this, 'filter_menu_items' ], 10, 3 );

        $this->register_ls_menu_switcher();
        $this->resolve_language_context();

        add_filter( 'get_user_option_metaboxhidden_nav-menus', [ $this, 'cpt_always_visible_in_menus' ] );
    }

    public function enqueue_assets(): void {
        wp_enqueue_style(
            'trp-language-switcher-v2',
            trailingslashit( TRP_PLUGIN_URL ) . 'assets/css/trp-language-switcher-v2.css',
            [],
            TRP_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'trp-language-switcher-js-v2',
            trailingslashit( TRP_PLUGIN_URL ) . 'assets/js/trp-frontend-language-switcher.js',
            [],
            TRP_PLUGIN_VERSION
        );

    }

    /** Make LS CPT available in Menus */
    public function register_ls_menu_switcher() : void {
        register_post_type('language_switcher', [
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_nav_menus'   => true,
            'show_in_menu'        => false,
            'show_in_admin_bar'   => false,
            'can_export'          => false,
            'public'              => false,
            'label'               => 'Language Switcher',
        ]);
    }

    /**
     * Establishes language context for the request and schedules a canonical redirect if needed.
     * Sets $current_lang/$needed_lang TRP globals; redirects on missing/mismatched language or slug mismatch.
     *
     * @return void
     */
    private function resolve_language_context(): void {
        $lang_from_url = $this->url_converter->get_lang_from_url_string(); // may be null
        $needed_lang = $this->determine_needed_language($lang_from_url, $this->trp);
        $this->current_lang = $lang_from_url ?? $needed_lang;

        global $TRP_LANGUAGE, $TRP_NEEDED_LANGUAGE;
        $TRP_LANGUAGE = $needed_lang;
        $TRP_NEEDED_LANGUAGE = $needed_lang;

        $allow = apply_filters('trp_allow_language_redirect', true, $needed_lang, $this->url_converter->cur_page_url());
        if (!$allow) return;

        $missing_in_url = ($lang_from_url === null);
        $add_subdir     = ($this->settings['add-subdirectory-to-default-language'] ?? 'no') === 'yes';
        $default        = $this->settings['default-language'] ?? '';
        $slug_mismatch  = (!$missing_in_url && $needed_lang === $lang_from_url && $this->is_slug_mismatch( $needed_lang ));


        if (
            ( $missing_in_url && $add_subdir ) ||
            ( $missing_in_url && $needed_lang !== $default ) ||
            ( !$missing_in_url && $needed_lang !== $lang_from_url ) ||
            $slug_mismatch
        ) {
            add_action('template_redirect', [ $this, 'redirect_to_correct_language' ], 10);
        }
    }

    /**
     * Returns true if the current request path differs from the canonical path for $lang.
     * Compares normalized paths only (ignores query/fragment) and honors trailing-slash settings.
     *
     * @param string $lang Language code (e.g. 'de_DE').
     * @return bool        True when a canonical redirect is needed.
     */
    private function is_slug_mismatch(string $lang): bool {
        $canonical = (string) $this->url_converter->get_url_for_language($lang, null, '');
        $current   = (string) $this->url_converter->cur_page_url();

        // Extract and normalize just the path (ignore query + fragments)
        $currPath = rawurldecode(rtrim((string) wp_parse_url($current,   PHP_URL_PATH),  '/'));
        $canonPath= rawurldecode(rtrim((string) wp_parse_url($canonical, PHP_URL_PATH), '/'));

        $currPath  = user_trailingslashit($currPath);
        $canonPath = user_trailingslashit($canonPath);

        return $currPath !== $canonPath;
    }

    private function determine_needed_language( ?string $lang_from_url, TRP_Translate_Press $trp ): string {
        if ( $lang_from_url === null ) {
            if (
                ($this->settings['add-subdirectory-to-default-language'] ?? 'no') === 'yes'
                && isset( $this->settings['publish-languages'][0] )
            ) {
                $needed_language = $this->settings['publish-languages'][0];
            } else {
                $needed_language = $this->settings['default-language'];
            }
        } else {
            $needed_language = $lang_from_url;
        }
        return apply_filters( 'trp_needed_language', $needed_language, $lang_from_url, $this->settings, $trp );
    }

    public function redirect_to_correct_language(): void {
        if ((defined('DOING_AJAX') && DOING_AJAX) || is_customize_preview()) return;
        if ($this->url_converter->is_sitemap_path()) return;

        global $TRP_NEEDED_LANGUAGE;

        $currLang = $this->url_converter->get_lang_from_url_string();
        if ( $currLang === $TRP_NEEDED_LANGUAGE && !$this->is_slug_mismatch( $TRP_NEEDED_LANGUAGE ) )
            return;

        $dest = esc_url_raw((string) apply_filters(
            'trp_link_to_redirect_to',
            $this->url_converter->get_url_for_language($TRP_NEEDED_LANGUAGE, null, ''),
            $TRP_NEEDED_LANGUAGE
        ));

        $should_add_subdir = ( $this->settings['add-subdirectory-to-default-language'] ?? 'no' ) === 'yes';
        $status = ( $should_add_subdir && $TRP_NEEDED_LANGUAGE === ( $this->settings['default-language'] ?? '' ) )
            ? (int) apply_filters('trp_redirect_status', 301, 'redirect_to_add_subdirectory_to_default_language')
            : (int) apply_filters('trp_redirect_status', 302, 'redirect_to_a_different_language_according_to_url_slug');

        wp_safe_redirect( $dest, $status );
        exit;
    }

    /** Keep LS box visible in the Menus screen */
    public function cpt_always_visible_in_menus( $result ) {
        if ( is_array( $result ) && in_array( 'add-post-type-language_switcher', $result, true ) ) {
            $result = array_diff( $result, ['add-post-type-language_switcher'] );
        }
        return $result;
    }

    /**
     * Floating switcher – inserted in footer.
     */
    public function render_floater(): void {
        if ( !$this->floater_enabled() )
            return;

        $config = $this->config['floater'];

        $layout    = $config['layoutCustomizer'][ $this->viewport ] ?? $config['layoutCustomizer']['desktop'];
        $name_type = $layout['languageNames'] ?? 'full';

        $positionClass = 'trp-switcher-position-'
        . ( strpos( $layout['position'], 'top' ) !== false
            ? 'top'
            : 'bottom' );

        $is_opposite = (bool) $config['oppositeLanguage'];

        $list = $this->get_language_items( $name_type, $is_opposite );

        $current_language = $list[0];

        global $TRP_LANGUAGE;

        /** We do this in order to keep the language order consistent. Currently selected language is always first.  */
        if ( $config['type'] === 'side-by-side' && $TRP_LANGUAGE !== $this->settings['default-language'] )
            $list = array_reverse( $list );

        $styles   = $this->build_floater_style_attr( $config, $layout );
        $viewport = $this->viewport;

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo apply_filters(
            'trp_floater_ls_html_v2',
            $this->get_template(
                $this->template_path( "floating-switcher.php" ),
                compact( 'list', 'styles', 'config', 'viewport', 'positionClass', 'is_opposite', 'current_language' ),
                true
            )
        );

        if (
            !empty( $config['enableCustomCss'] )
            && !empty( $config['customCss'] )
            && is_string( $config['customCss'] )
        ) {
            $css = str_ireplace( '</style', '', $config['customCss'] );

            echo '<style id="trp-language-switcher-custom-css">' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }

    /**
     * Shortcode switcher.
     *
     * Usage: [language-switcher]
     *
     * Reads settings from $this->config['shortcode'] and renders
     * a dropdown switcher with first item as the current language.
     *
     * @param array $atts Shortcode attributes (currently unused).
     * @return string
     */
    public function render_shortcode( $atts = [] ): string {
        $atts = shortcode_atts( [
            'is_editor' => 'false',
        ], $atts, 'language-switcher' );

        /** @var bool $is_editor is Gutenberg editor the place of render */
        $is_editor = filter_var( $atts['is_editor'], FILTER_VALIDATE_BOOLEAN );

        $config = ( isset( $this->config['shortcode'] ) && is_array( $this->config['shortcode'] ) )
            ? $this->config['shortcode']
            : [];

        $viewport = $this->viewport;
        $layout   = $config['layoutCustomizer'][ $viewport ]
            ?? ( $config['layoutCustomizer']['desktop'] ?? [] );

        $name_type     = $layout['languageNames']    ?? 'full';
        $flag_position = $layout['flagIconPosition'] ?? 'before';
        $flag_shape    = $config['flagShape']        ?? 'rect';
        $open_on_click = ! empty( $config['clickLanguage'] );

        $flag_position = ( $flag_position === 'after' ) ? 'after' : 'before';
        $flag_ratio    = ( $flag_shape === 'square' ) ? 'square' : 'rect';

        $list = $this->get_language_items( $name_type, false );

        if ( empty( $list ) || !isset( $list[0]['code'] ) )
            return ''; // nothing to render

        foreach ( $list as &$item ) {
            $code         = $item['code'];
            $item['url']  = $this->url_converter->get_url_for_language( $code );
            $item['flag'] = $this->get_flag_html( $code, $flag_ratio );
            $item['name'] = isset( $item['name'] ) && is_string( $item['name'] ) ? $item['name'] : '';
        }
        unset( $item );

        $style_value = $this->build_shortcode_style_value( $config, $layout );

        // Render the partial (string), then allow filtering of the final HTML
        $html = $this->get_template(
            $this->template_path( 'shortcode-switcher.php' ),
            compact( 'list', 'config', 'style_value', 'flag_position', 'open_on_click', 'is_editor' ),
            true
        );

        $html = apply_filters( 'trp_shortcode_ls_html_v2', $html, $list, $config, $layout );

        if ( ! empty( $config['enableCustomCss'] ) && ! empty( $config['customCss'] ) && is_string( $config['customCss'] ) ) {
            $css  = str_ireplace( '</style', '', $config['customCss'] );
            $html .= '<style id="trp-language-switcher-shortcode-custom-css">' . $css . '</style>';
        }

        return $html;
    }


    /**
     * Menu language switcher items.
     *
     * @param array $items Menu items.
     * @param WP_Term $menu Menu term.
     * @param stdClass $args Nav menu args.
     * @return array
     */
    public function filter_menu_items( array $items, $menu, $args ): array {
        if ( empty( $this->config['menu'] ) || !is_array( $this->config['menu'] ) ) {
            return $items;
        }

        $cfg    = $this->config['menu'];
        $layout = $cfg['layoutCustomizer'][ $this->viewport ] ?? ( $cfg['layoutCustomizer']['desktop'] ?? [] );

        $flagPos = in_array( ( $layout['flagIconPosition'] ?? 'before' ), [ 'before', 'after', 'hide' ], true ) ? $layout['flagIconPosition'] : 'before';
        $nameOpt = in_array( ( $layout['languageNames'] ?? 'full' ), [ 'full', 'short', 'none' ], true ) ? $layout['languageNames'] : 'full';
        $shape   = in_array( ( $layout['flagShape'] ?? 'rect' ), [ 'rect', 'square', 'rounded' ], true ) ? $layout['flagShape'] : 'rect';

        $user_labels = [];
        foreach ( $items as $it ) {
            if ( $it->object !== 'language_switcher' ) continue;

            $ls_id  = $it->object_id ?: get_post_meta( $it->ID ?? 0, '_menu_item_object_id', true );
            $ls_post = $ls_id ? get_post( $ls_id ) : null;
            if ( !$ls_post || $ls_post->post_type !== 'language_switcher' ) continue;

            $token = $ls_post->post_content;
            if ( isset( $it->post_title ) && $it->post_title !== '' ) {
                $user_labels[ $token ] = $it->post_title;
            }
        }

        // Cache display names
        $published_codes = $this->settings['publish-languages'] ?? [];
        $full_names      = $this->languages->get_language_names( $published_codes );

        $current_present      = false; // did we see the pseudo 'current_language'?
        $real_current_indexes = [];    // collect real items that equal current language

        foreach ( $items as $i => $item ) {
            if ( $item->object !== 'language_switcher' ) continue;

            $ls_id  = $item->object_id ?: get_post_meta( $item->ID ?? 0, '_menu_item_object_id', true );
            $ls_post = $ls_id ? get_post( $ls_id ) : null;
            if ( !$ls_post || $ls_post->post_type !== 'language_switcher' ) continue;

            $orig = $ls_post->post_content;
            $code = $orig;

            if ( $orig === 'current_language' ) {
                $current_present = true;
                $code = $this->current_lang;
            } elseif ( $orig === 'opposite_language' ) {
                $code = $this->get_opposite_language();
            }

            if ( $orig !== 'current_language' && $code === $this->current_lang && !is_admin() ) {
                $real_current_indexes[] = $i;
            }

            $label_html = $user_labels[ $orig ] ?? null;
            if ( $label_html === null ) {
                $label_html = $this->build_menu_item_label_viewport(
                    $code,
                    [
                        'flagPosition' => $flagPos,
                        'nameOption'   => $nameOpt,
                        'flagShape'    => $shape,
                    ],
                    $full_names
                );
            } else {
                // Allow flags around a plain user label if configured
                if ( $flagPos !== 'hide' ) {
                    $flag_html  = $this->get_flag_html( $code, $shape );
                    $label_html = ( $flagPos === 'before' )
                        ? '<span data-no-translation>' . $flag_html . ' <span class="trp-ls-language-name">' . wp_kses_post( $label_html ) . '</span></span>'
                        : '<span data-no-translation><span class="trp-ls-language-name">' . wp_kses_post( $label_html ) . '</span> ' . $flag_html . '</span>';
                } else {
                    $label_html = '<span class="trp-ls-language-name" data-no-translation>' . wp_kses_post( $label_html ) . '</span>';
                }
            }

            $item->url     = esc_url( $this->url_converter->get_url_for_language( $code ) );
            $item->title   = $label_html;
            $item->classes = array_values( array_unique( array_merge(
                $item->classes ?? [],
                [
                    'trp-language-switcher-container',
                    'trp-menu-ls-item',
                    'trp-menu-ls-' . esc_attr( $this->viewport ),
                ]
            ) ) );

            if ( $code === $this->current_lang )
                $item->classes[] = 'current-language-menu-item';
        }

        if ( $current_present && $real_current_indexes ) {
            foreach ( array_reverse( $real_current_indexes ) as $idx ) {
                if ( isset( $items[ $idx ] ) ) {
                    unset( $items[ $idx ] );
                }
            }
            $items = array_values( $items );
        }

        return $items;
    }

    /**
     * Build one menu item label based on viewport-scoped config:
     * - flagPosition: 'before'|'after'|'hide'
     * - nameOption  : 'full'|'short'|'none'
     * - flagShape   : 'rect'|'square'|'rounded'
     *
     * @param string $code
     * @param array  $opts
     * @param array  $full_names  map[code => full name]
     * @return string HTML
     */
    private function build_menu_item_label_viewport( string $code, array $opts, array $full_names ): string {
        $flagPos = $opts['flagPosition'] ?? 'before';
        $nameOpt = $opts['nameOption']   ?? 'full';
        $shape   = $opts['flagShape']    ?? 'rect';

        $flag_html = $flagPos === 'hide' ? '' : $this->get_flag_html( $code, $shape );

        // Name
        $name_html = '';
        if ( $nameOpt === 'full' ) {
            $name = $full_names[ $code ] ?? $code;
            $name_html = '<span class="trp-ls-language-name">' . esc_html($name) . '</span>';
        } elseif ( $nameOpt === 'short' ) {
            $short = strtoupper( $this->url_converter->get_url_slug( $code, false ) );
            $name_html = '<span class="trp-ls-language-name">' . esc_html($short) . '</span>';
        } // 'none' => empty

        // Compose order
        $inner = ($flagPos === 'before')
            ? trim($flag_html . ' ' . $name_html)
            : trim($name_html . ' ' . $flag_html);

        return '<span class="trp-menu-ls-label" data-no-translation>' . $inner . '</span>';
    }

    /**
     * Language list.
     *
     * @param string $language_name_option
     * @param bool   $opposite_only Whether to return only current + opposite language.
     * @return array
     */
    private function get_language_items( string $language_name_option = 'full', bool $opposite_only = false ): array {
        $codes = current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) )
            ? ($this->settings['translation-languages'] ?? [])
            : ($this->settings['publish-languages']     ?? []);

        // Guard: establish a safe "current"
        $current = $this->current_lang ?: ($this->settings['default-language'] ?? null);
        if ( !$current || !in_array( $current, $codes, true ) ) {
            $current = $codes[0] ?? ($this->settings['default-language'] ?? '');
        }

        $name_resolvers = [
            'short' => function () use ( $codes ) {
                return array_combine(
                    $codes,
                    array_map(
                        fn( $code ) => esc_html( strtoupper( $this->url_converter->get_url_slug( $code, false ) ) ),
                        $codes
                    )
                );
            },
            'full' => fn() => $this->languages->get_language_names( $codes ),
            'none' => fn() => array_fill_keys( $codes, '' )
        ];

        // Default to 'full' if the option is invalid or missing
        $resolver = $name_resolvers[ $language_name_option ] ?? $name_resolvers['full'];
        $names    = $resolver();

        if ( $opposite_only ) {
            $opp_code = $this->get_opposite_language();
            return [[ 'code' => $opp_code, 'name' => $names[$opp_code] ?? '' ]];
        }

        $list = [
            [
                'code' => $current,
                'name' => $names[ $current ] ?? '',
            ]
        ];

        foreach ( $names as $code => $name ) {
            if ( $code !== $current ) $list[] = [ 'code' => $code, 'name' => $name ];
        }

        return $list;
    }

    private function get_opposite_language(): string {
        foreach ( $this->settings['publish-languages'] as $code ) {
            if ( $code !== $this->current_lang ) {
                return $code;
            }
        }
        return $this->current_lang;
    }

    /**
     * Build inline style attribute with CSS variables.
     *
     * @param array $cfg     Floater config.
     * @param array $layout  Layout settings based on the current viewport.
     *
     * @return string
     */
    private function build_floater_style_attr( array $cfg, array $layout ): string {
        $position  = $layout['position'] ?? 'bottom-right';
        $largeFont = $cfg['size'] === 'large';

        $edgeMap = [
            'bottom-right' => [ '--bottom' => '0px', '--right' => '10vw' ],
            'bottom-left'  => [ '--bottom' => '0px', '--left'  => '10vw' ],
            'top-right'    => [ '--top'    => '0px', '--right' => '10vw' ],
            'top-left'     => [ '--top'    => '0px', '--left'  => '10vw' ],
        ];

        $positionVars = $edgeMap[$position] ?? [];

        $vars = apply_filters( 'trp_floating_language_switcher_style_vars',
                array_merge(
                    [
                        '--bg'               => $cfg['bgColor'] ?: 'transparent',
                        '--bg-hover'         => $cfg['bgHoverColor'] ?: 'transparent',
                        '--text'             => $cfg['textColor'] ?: '#000',
                        '--text-hover'       => $cfg['textHoverColor'] ?: '#000',
                        '--border'           => $cfg['borderWidth'] ? "{$cfg['borderWidth']}px solid {$cfg['borderColor']}" : 'none',
                        '--border-radius'    => $cfg['borderRadius'] ? $this->build_radius( $cfg['borderRadius'] ) : '8px 8px 0 0',
                        '--flag-radius'      => isset( $cfg['flagRadius'] ) ? "{$cfg['flagRadius']}px" : '2px',
                        '--flag-size'        => $largeFont ? '20px' : '18px',
                        '--aspect-ratio'     => $cfg['flagShape'] === 'rect' ? '4/3' : '1',
                        '--font-size'        => $largeFont ? '16px' : '14px',
                        '--switcher-width'   => (
                            $layout['width'] === 'custom'
                                ? ( $layout['customWidth'] ?? 216 )  . 'px'
                                : 'auto'
                        ),
                        '--switcher-padding' => (
                            $layout['padding'] === 'custom'
                                ? ( $layout['customPadding'] ?? 0 ). 'px'
                                : '10px 0'
                        ),
                        '--transition-duration' => $cfg['enableTransitions'] ? '0.2s' : '0s'
                    ],
                    $positionVars
                )
        );

        $pairs = array();
        foreach ( $vars as $k => $v ) {
            if ( ! is_string( $k ) || ! preg_match( '/^--[a-z0-9-]+$/i', $k ) ) {
                continue;
            }
            $pairs[] = $k . ':' . $v;
        }

        return implode( ';', $pairs );
    }

    /**
     * Build inline style attribute with CSS variables for the shortcode switcher.
     *
     * @param array $cfg    Shortcode config.
     * @param array $layout Layout settings based on current viewport.
     * @return string       style="--var: value; ..."
     */
    private function build_shortcode_style_value( array $cfg, array $layout ): string {
        $large_font = isset( $cfg['size'] ) && $cfg['size'] === 'large';

        $font_size = $large_font ? '16px' : '14px';
        $flag_size = $large_font ? '20px' : '18px';

        // Scalar border radius (shortcode config uses int)
        $radius_scalar = isset( $cfg['borderRadius'] ) && is_numeric( $cfg['borderRadius'] )
            ? (int) $cfg['borderRadius']
            : 5;

        $border_width = isset( $cfg['borderWidth'] ) ? (int) $cfg['borderWidth'] : 0;
        $border_color = isset( $cfg['borderColor'] ) ? (string) $cfg['borderColor'] : '#1438521a';

        $border = $border_width > 0 ? sprintf( '%dpx solid %s', $border_width, $border_color ) : 'none';

        $vars = [
            '--bg'            => isset( $cfg['bgColor'] )        ? (string) $cfg['bgColor']        : '#ffffff',
            '--bg-hover'      => isset( $cfg['bgHoverColor'] )   ? (string) $cfg['bgHoverColor']   : '#0000000d',
            '--text'          => isset( $cfg['textColor'] )      ? (string) $cfg['textColor']      : '#a9adb0',
            '--text-hover'    => isset( $cfg['textHoverColor'] ) ? (string) $cfg['textHoverColor'] : '#1d2327',

            // Support both a single --border var and split width/color vars (if your CSS uses either).
            '--border'        => $border,
            '--border-width'  => $border_width . 'px',
            '--border-color'  => $border_color,

            '--border-radius' => $radius_scalar . 'px',
            '--flag-radius'   => isset( $cfg['flagRadius'] ) ? (int) $cfg['flagRadius'] . 'px' : '2px',
            '--flag-size'     => $flag_size,
            '--aspect-ratio'  => ( isset( $cfg['flagShape'] ) && $cfg['flagShape'] === 'rect' ) ? '4/3' : '1',
            '--font-size'     => $font_size,
            '--transition-duration' => ( $cfg['enableTransitions'] ?? true ) ? '0.2s' : '0s'
        ];

        $pairs = [];
        foreach ( $vars as $k => $v ) {
            $pairs[] = $k . ':' . $v;
        }

        return implode( ';', $pairs );
    }

    private function build_radius( array $r ): string {
        return implode(
            ' ',
            array_map( static fn( $v ) => intval( $v ) . 'px', $r )
        );
    }

    private function template_path( string $file ): string {
        return trailingslashit( TRP_PLUGIN_DIR ) . 'partials/' . $file;
    }

    /**
     * Tiny templating helper.
     *
     * @param string $path Absolute path.
     * @param array $vars  Vars to extract.
     * @param bool $return Return string or echo.
     * @return string
     */
    private function get_template( string $path, array $vars = [], bool $return = false ): string {
        if ( !file_exists( $path ) ) {
            return '';
        }

        ob_start();
        extract( $vars, EXTR_SKIP );
        include $path;
        $content = ob_get_clean();

        if ( $return ) {
            return $content;
        }

        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template HTML, values escaped in template.
        echo $content;
        return '';
    }

    /**
     * Determine if the floater should render.
     *
     * @return bool
     */
    private function floater_enabled(): bool {
        return !empty( $this->config['floater']['enabled'] );
    }

    /**
     * Returns the HTML element that shows a flag using the
     * lipis/flag-icons CSS classes.
     *
     * @param string $language_code Either “en”, “en_US”, “pt-BR”, etc.
     * @return string               <span class="fi fi-xx">…</span>
     */
    public function get_flag_html( string $language_code, string $shape ): string {
        // Allow override via filter (custom flag URL)
        $flag_path = apply_filters( 'trp_flags_path', '', $language_code );
        $name      = $this->languages->get_language_names( [ $language_code ] )[ $language_code ] ?? $language_code;

        if ( filter_var( $flag_path, FILTER_VALIDATE_URL ) ) {
            $classes = [ 'trp-flag-image', 'trp-custom-flag' ];
            if ( $shape === 'rounded' ) $classes[] = 'trp-flag-rounded';
            if ( $shape === 'square' )  $classes[] = 'trp-flag-square';

            $html = sprintf(
                '<img src="%s" class="%s" alt="%s" loading="lazy" decoding="async" />',
                esc_url( $flag_path ),
                esc_attr( implode( ' ', $classes ) ),
                esc_attr( $name )
            );
            return apply_filters( 'trp_flag_html', $html, $language_code, $flag_path );
        }

        // Decide folder: square/rounded use 1x1, default is 4x3
        $ratio = ( $shape === 'square' || $shape === 'rounded' ) ? '1x1' : '4x3';

        // Locale-based filename (hyphen → underscore)
        $locale_file = str_replace( '-', '_', trim( $language_code ) ) . '.svg';

        // Absolute URL & path
        $url  = trailingslashit( TRP_PLUGIN_URL ) . 'assets/flags/' . $ratio . '/' . rawurlencode( $locale_file );
        $path = trailingslashit( TRP_PLUGIN_DIR ) . 'assets/flags/' . $ratio . '/' . $locale_file;

        // If missing, output nothing
        if ( ! is_readable( $path ) ) {
            return '';
        }

        // Classes
        $classes = [ 'trp-flag-image' ];
        if ( $shape === 'rounded' ) $classes[] = 'trp-flag-rounded';
        if ( $shape === 'square' )  $classes[] = 'trp-flag-square';

        $html = sprintf(
            '<img src="%s" class="%s" alt="%s" loading="lazy" decoding="async" />',
            esc_url( $url ),
            esc_attr( implode( ' ', $classes ) ),
            esc_attr( $name )
        );

        return apply_filters( 'trp_flag_html', $html, $language_code, $url );
    }

    /**
     * Legacy function to add flag
     *
     * @param $language_code
     * @param $language_name
     * @param $location
     * @return string
     * @deprecated
     */
    public function add_flag( $language_code, $language_name, $location = NULL ) {
        $flags_path     = TRP_PLUGIN_URL . 'assets/images/flags/';
        $flags_path     = apply_filters( 'trp_flags_path', $flags_path, $language_code );
        $flag_file_name = $language_code . '.png';
        if ( $location == 'ls_shortcode' ) {
            $flag_url = $flags_path . $flag_file_name;
            return esc_url( $flag_url );
        }

        return $this->get_flag_html( $language_code, 'rect' );
    }

    /**
     * Legacy function for rendering shortcode LS
     *
     * @param $atts
     * @return void
     * @deprecated
     */
    public function language_switcher( $atts ){
        return $this->render_shortcode();
    }

    /**
     * Legacy function for rendering floater LS
     *
     */
    public function add_floater_language_switcher(){
        return $this->render_floater();
    }

    /**
     * Legacy function used in older versions of Automatic Language Detection Add-on
     *
     */
    public function add_shortcode_preferences( $settings, $language_code, $language_name ) {
        if ( $settings['flags'] ){
            $flag = $this->add_flag($language_code, $language_name);
        } else {
            $flag = '';
        }

        if ( $settings['full_names'] ){
            $full_name = $language_name;
        } else {
            $full_name = '';
        }

        if ( $settings['short_names'] ){
            $short_name = strtoupper( $this->url_converter->get_url_slug( $language_code, false ) );
        } else {
            $short_name = '';
        }

        return $flag . ' ' . esc_html( $short_name . $full_name );
    }

}
