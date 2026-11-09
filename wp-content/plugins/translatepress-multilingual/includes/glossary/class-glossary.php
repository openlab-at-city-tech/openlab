<?php

if ( !defined( 'ABSPATH' ) )
    exit();

/**
 * Class TRP_Glossary
 *
 * Provides glossary substitutions during machine translation by exposing
 * filter callbacks for `trp_machine_translation_span_terms` and
 * `trp_machine_translation_string_after_receive`.
 *
 * Terms are read from the `trp_glossary` option managed by
 * TRP_Glossary_Queries. Each glossary term (a default-language string) that has
 * a translation for the target language is wrapped in a span (a 1TPnT placeholder
 * inside attribute values) before it is sent to TranslatePress AI, and the span
 * is replaced with that translation in the response. Matching against page
 * strings is case-insensitive.
 */
class TRP_Glossary {

    /**
     * Settings array passed in at construction (full trp settings, like other tabs).
     *
     * @var array
     */
    private $settings;

    /**
     * Cached glossary terms loaded from the `trp_glossary` option.
     * Structure: array( default_term => array( target_locale => translation ) ).
     *
     * @var array|null
     */
    protected $terms = null;

    /**
     * Per-target-language replacement maps, keyed by target locale.
     * Each map is: lowercase( default_term ) => translation.
     *
     * @var array
     */
    protected $replacement_cache = array();

    public function __construct( $settings = array() ) {
        $this->settings = $settings;

        add_filter( 'trp_settings_active_tab', array( $this, 'filter_active_tab' ) );
    }

    /**
     * Keep the Automatic Translation nav tab highlighted while on the glossary page.
     *
     * @param string $active_tab
     * @return string
     */
    public function filter_active_tab( $active_tab ) {
        if ( $active_tab === 'trp_machine_translation_glossary' || $active_tab === 'trp_glossary_replace' ) {
            return 'trp_machine_translation';
        }
        return $active_tab;
    }

    /**
     * Add submenu page for the glossary settings.
     *
     * Hooked to admin_menu.
     */
    public function add_submenu_page() {
        add_submenu_page(
            'TRPHidden',
            'TranslatePress Glossary',
            'TRPHidden',
            // Same capability as the glossary actions and the replace page, so admins and
            // TranslatePress translator accounts can access the UI they're allowed to use.
            apply_filters( 'trp_translating_capability', 'manage_options' ),
            'trp_machine_translation_glossary',
            array( $this, 'glossary_page_content' )
        );
    }

    /**
     * Register settings option.
     *
     * Hooked to admin_init.
     */
    public function register_setting() {
        register_setting( 'trp_glossary_settings', 'trp_glossary_settings', array( $this, 'sanitize_settings' ) );
    }

    /**
     * Sanitize submitted glossary settings.
     *
     * @param mixed $submitted_settings
     * @return array
     */
    public function sanitize_settings( $submitted_settings ) {
        $settings = is_array( $submitted_settings ) ? $submitted_settings : array();

        return apply_filters( 'trp_extra_sanitize_glossary_settings', $settings, $submitted_settings );
    }

    /**
     * Output admin notices after saving glossary settings.
     *
     * Hooked to admin_notices.
     */
    public function admin_notices() {
        if ( isset( $_GET['page'] ) && $_GET['page'] === 'trp_machine_translation_glossary' ) {
            settings_errors( 'trp_glossary_settings' );
        }
    }

    /**
     * Render the glossary settings page.
     */
    public function glossary_page_content() {
        require_once TRP_PLUGIN_DIR . 'partials/glossary-settings-page.php';
    }

    /**
     * Lazy-load the glossary terms from the `trp_glossary` option.
     *
     * @return array Structure: array( default_term => array( target_locale => translation ) ).
     */
    protected function get_terms() {
        if ( $this->terms === null ) {
            $terms       = get_option( TRP_Glossary_Queries::OPTION_NAME, array() );
            $this->terms = is_array( $terms ) ? $terms : array();
        }

        return $this->terms;
    }

    /**
     * The glossary only drives automatic translation for the TranslatePress AI
     * engine (`mtapi`). Other engines (DeepL, Google Translate) manage glossaries
     * in their own provider dashboards, so we don't interfere with their output.
     *
     * @return bool
     */
    public function is_tp_ai_active() {
        $mt_settings = get_option( 'trp_machine_translation_settings', array() );
        return is_array( $mt_settings )
            && isset( $mt_settings['translation-engine'] )
            && $mt_settings['translation-engine'] === 'mtapi';
    }

    /**
     * Build a case-insensitive, whole-word regex for a literal term.
     *
     * Uses Unicode-aware lookarounds (not \b) so a match must not be adjacent to
     * a letter/number/underscore on either side — accented letters are handled
     * correctly. This makes "care" match in "Handle with care." but not in
     * "caregiver", and "grated cheese" not match inside "ungrated cheese".
     *
     * Used by the Replace existing translations tool, which keeps this strict rule: unlike machine
     * translation (TRP_Machine_Translation_Spans::word_edges()), "zł" doesn't match in "100zł".
     *
     * @param string $term
     * @return string PCRE pattern.
     */
    public function whole_word_pattern( $term ) {
        return '/(?<![\p{L}\p{N}_])' . preg_quote( $term, '/' ) . '(?![\p{L}\p{N}_])/iu';
    }

    /**
     * Whether a language separates words (so whole-word matching makes sense).
     *
     * Scripts like Chinese, Japanese and Thai run words together with no spaces,
     * so the lookaround word boundaries never match there — those languages must
     * fall back to plain substring matching.
     *
     * @param string $language_code Full locale (e.g. zh_CN) or language prefix.
     * @return bool
     */
    public function uses_word_boundaries( $language_code ) {
        return TRP_Machine_Translation_Spans::uses_word_boundaries( $language_code );
    }

    /**
     * Regex to match a term, honoring word boundaries for spaced languages and
     * falling back to a substring match for languages that don't use spaces.
     *
     * @param string $term
     * @param string $language_code Language the matched text belongs to.
     * @return string PCRE pattern.
     */
    public function term_match_pattern( $term, $language_code = '' ) {
        if ( $language_code !== '' && ! $this->uses_word_boundaries( $language_code ) ) {
            return '/' . preg_quote( $term, '/' ) . '/iu';
        }
        return $this->whole_word_pattern( $term );
    }

    /**
     * Build (and cache) the lowercase-term => translation map for a target language.
     * Only terms that have a non-empty translation for that language are included.
     *
     * @param string $target_language_code Full locale, e.g. de_DE.
     * @return array
     */
    protected function get_target_replacements( $target_language_code ) {
        if ( !isset( $this->replacement_cache[ $target_language_code ] ) ) {
            $map = array();

            foreach ( $this->get_terms() as $term => $translations ) {
                $term = (string) $term;
                if ( $term === '' || !is_array( $translations ) ) {
                    continue;
                }
                if ( isset( $translations[ $target_language_code ] ) && $translations[ $target_language_code ] !== '' ) {
                    $map[ mb_strtolower( $term, 'UTF-8' ) ] = $translations[ $target_language_code ];
                }
            }

            $this->replacement_cache[ $target_language_code ] = $map;
        }

        return $this->replacement_cache[ $target_language_code ];
    }

    /**
     * Whether the glossary applies to this machine translation request: TranslatePress AI engine,
     * translating from the default language (glossary terms are default-language strings).
     *
     * @param string|null $source_language_code
     * @return bool
     */
    protected function applies_to_request( $source_language_code ) {
        if ( ! $this->is_tp_ai_active() ) {
            return false;
        }
        $default_language = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
        return $default_language === '' || $source_language_code === null || $source_language_code === $default_language;
    }

    /**
     * Add the glossary terms to the text wrapped in spans before the string is sent to TranslatePress AI,
     * as <span id="trp-glossary-N">. The model sees the real term (a placeholder leaves a hole it tends to
     * fill with invented words), the server keeps the span around the aligned words and unwrap_terms()
     * overwrites them with the configured translation. A term inside a quoted attribute value, where a tag
     * can't go, is sent as a 1TPnT placeholder instead (TRP_Machine_Translation_Spans::wrap()).
     *
     * Hooked to trp_machine_translation_span_terms. Only terms with a translation for the target language
     * are added. Priority beats excluded words of the same length; a longer excluded phrase still wins.
     *
     * @param array       $groups
     * @param string      $string               String about to be sent.
     * @param string      $target_language_code
     * @param string|null $source_language_code
     * @return array
     */
    public function add_span_terms( $groups, $string = '', $target_language_code = '', $source_language_code = null ) {
        if ( !is_string( $string ) || $string === '' || !$this->applies_to_request( $source_language_code ) ) {
            return $groups;
        }

        $terms = array();
        foreach ( $this->get_terms() as $term => $translations ) {
            $term = (string) $term;
            if ( $term === '' || !is_array( $translations ) || !isset( $translations[ $target_language_code ] ) || $translations[ $target_language_code ] === '' ) {
                continue;
            }
            if ( mb_stripos( $string, $term, 0, 'UTF-8' ) !== false ) {
                $terms[] = $term;
            }
        }
        if ( empty( $terms ) ) {
            return $groups;
        }

        $default_language = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
        $groups           = (array) $groups;
        $groups[]         = array(
            'prefix'   => 'trp-glossary-',
            'pattern'  => TRP_Machine_Translation_Spans::words_pattern( $terms, $default_language === '' || $this->uses_word_boundaries( $default_language ), true, true, true ),
            'priority' => 10,
        );

        return $groups;
    }

    /**
     * Replace the content of each glossary span in the TranslatePress AI response with the configured
     * translation and drop the span, and replace each glossary 1TPnT token (a term inside an attribute value)
     * with it. Text spelled exactly like a configured term gets that term's translation as configured; any other
     * casing goes through match_case(). When the model drops a span the translation is kept as returned and the
     * miss is logged.
     *
     * Hooked to trp_machine_translation_string_after_receive.
     *
     * @param string      $machine_string       Translation returned by the engine.
     * @param string      $sent_string          String that was sent, see TRP_Machine_Translation_Spans::wrap_protected_text().
     * @param string      $original_string
     * @param string      $target_language_code
     * @param string|null $source_language_code
     * @return string
     */
    public function unwrap_terms( $machine_string, $sent_string = '', $original_string = '', $target_language_code = '', $source_language_code = null ) {
        if ( !is_string( $machine_string ) || !is_string( $sent_string ) ) {
            return $machine_string;
        }
        $variants = strpos( $sent_string, 'trp-glossary-' ) === false ? array() : TRP_Machine_Translation_Spans::get_span_contents( $sent_string, 'trp-glossary-' );
        $tokens   = TRP_Machine_Translation_Spans::token_texts( $sent_string, 'trp-glossary-' );
        if ( empty( $variants ) && empty( $tokens ) ) {
            return $machine_string;
        }

        $terms           = $this->get_terms();
        $map             = $this->get_target_replacements( $target_language_code );
        $translation_for = function ( $variant ) use ( $terms, $map, $target_language_code, &$translation_for ) {
            if ( isset( $terms[ $variant ][ $target_language_code ] ) && $terms[ $variant ][ $target_language_code ] !== '' ) {
                return $terms[ $variant ][ $target_language_code ]; // spelled exactly as configured: translation as configured
            }
            $lookup = mb_strtolower( $variant, 'UTF-8' );
            if ( isset( $map[ $lookup ] ) ) {
                return $this->match_case( $variant, $map[ $lookup ] );
            }
            // A term with digits after it (zł100) was wrapped whole, only the term part is translated
            $code = TRP_Machine_Translation_Spans::split_trailing_digits( $variant );
            return $code === null ? $variant : $translation_for( $code[0] ) . $code[1];
        };

        $result         = $machine_string;
        $missing_tokens = array();
        if ( !empty( $tokens ) ) {
            list( $result, $found ) = TRP_Machine_Translation_Spans::restore_tokens( $result, $sent_string, 'trp-glossary-', function ( $number, $text ) use ( $translation_for ) {
                return $translation_for( $text );
            } );
            $missing_tokens = array_diff_key( $tokens, array_flip( $found ) );
        }

        $drifted = array();
        $found   = array();
        if ( !empty( $variants ) ) {
            list( $result, $found ) = TRP_Machine_Translation_Spans::replace_spans( $result, 'trp-glossary-', function ( $id, $content, $neighbours = array() ) use ( $variants, $translation_for, $target_language_code, &$drifted ) {
                if ( !isset( $variants[ $id ] ) ) {
                    return $content;
                }
                $restored = TRP_Machine_Translation_Spans::restore_span( $content, $variants[ $id ], $translation_for( $variants[ $id ] ), $target_language_code, $neighbours );
                if ( $restored === null ) {
                    $drifted[ $id ] = $variants[ $id ];
                    return $content;
                }
                return $restored;
            } );
        }

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $missing = array_diff_key( $variants, array_flip( $found ) );
            if ( !empty( $missing ) ) {
                error_log( 'TranslatePress glossary: trp-glossary span missing or left unclosed in the ' . $target_language_code . ' translation for "' . implode( '", "', $missing ) . '". Sent: ' . $sent_string . ' Received: ' . $machine_string );
            }
            if ( !empty( $drifted ) ) {
                error_log( 'TranslatePress glossary: trp-glossary span drifted in the ' . $target_language_code . ' translation for "' . implode( '", "', $drifted ) . '", kept the engine text. Sent: ' . $sent_string . ' Received: ' . $machine_string );
            }
            if ( !empty( $missing_tokens ) ) {
                error_log( 'TranslatePress glossary: glossary placeholder missing in the ' . $target_language_code . ' translation for "' . implode( '", "', $missing_tokens ) . '". Sent: ' . $sent_string . ' Received: ' . $machine_string );
            }
        }

        return $result;
    }

    /**
     * Copy the capitalization pattern of the matched source term onto the
     * translation. Deterministic and intentionally simple:
     *   - ALL CAPS   (WORLD) -> uppercase the translation; needs two cased letters
     *   - all lower  (world) -> lowercase the first letter only, configured capitals elsewhere (d'Aurelda) stay
     *   - Capitalized(World) -> upper-case the first letter of the translation; a single cased
     *                           letter (A, C#) counts as Capitalized: match_case('C#','do dièse') -> 'Do dièse'
     *   - no cased letters (42, 東京) or anything mixed (iPhone, 3D, Hello World) -> leave the translation as configured
     *
     * Machine translation (unwrap_terms) skips this for text spelled exactly like the configured term,
     * so R&D -> R et D and C# -> do dièse stay as configured. The Replace existing translations tool
     * (TRP_Glossary_Queries::case_aware_replace) always applies it.
     *
     * @param string $source      The exact source term variant matched in the text.
     * @param string $translation The configured translation for the target language.
     * @return string
     */
    public function match_case( $source, $translation ) {
        if ( $translation === '' ) {
            return $translation;
        }

        $upper = mb_strtoupper( $source, 'UTF-8' );
        $lower = mb_strtolower( $source, 'UTF-8' );

        if ( $source === $upper && preg_match_all( '/[\p{Lu}\p{Ll}\p{Lt}]/u', $source ) > 1 ) { // ALL CAPS
            return mb_strtoupper( $translation, 'UTF-8' );
        }
        if ( $source === $lower ) {                               // all lower (or no cased letters)
            return $upper === $lower ? $translation : $this->mb_lcfirst( $translation );
        }
        if ( $this->mb_ucfirst( $lower ) === $source ) {          // Capitalized first letter only
            return $this->mb_ucfirst( $translation );
        }

        return $translation;                                      // mixed casing -> verbatim
    }

    /**
     * Multibyte-safe lcfirst().
     *
     * @param string $string
     * @return string
     */
    public function mb_lcfirst( $string ) {
        if ( $string === '' ) {
            return $string;
        }
        $first = mb_substr( $string, 0, 1, 'UTF-8' );
        return mb_strtolower( $first, 'UTF-8' ) . mb_substr( $string, 1, null, 'UTF-8' );
    }

    /**
     * Multibyte-safe ucfirst().
     *
     * @param string $string
     * @return string
     */
    public function mb_ucfirst( $string ) {
        if ( $string === '' ) {
            return $string;
        }
        $first = mb_substr( $string, 0, 1, 'UTF-8' );
        return mb_strtoupper( $first, 'UTF-8' ) . mb_substr( $string, 1, null, 'UTF-8' );
    }
}
