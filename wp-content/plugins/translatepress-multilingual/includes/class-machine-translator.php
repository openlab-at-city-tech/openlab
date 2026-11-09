<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Machine_Translator
 *
 * Facilitates Machine Translation calls.
 */
class TRP_Machine_Translator {
    protected $settings;
	protected $referer;
	protected $url_converter;
	protected $machine_translator_logger;
	protected $machine_translation_codes;
	protected $trp_languages;
    protected $trp_query;
    protected $correct_api_key = null;

    /* Engines extend this class, so more than one instance exists per request. Wire up the cron only once. */
    protected static $lock_cleanup_hooked = false;

    /* Owner tokens of the machine translation locks this instance claimed and has not released yet */
    protected $held_lock_owners = array();

    /* Largest string, in bytes, sent to an engine: the smallest engine body limit ( TranslatePress AI returns longer ones untranslated ) */
    const MAX_STRING_BYTES = 99400;

    /* Strings dropped from translation because a concurrent request holds their lock, keyed by string */
    protected $lock_skipped_strings = array();

    /**
     * TRP_Machine_Translator constructor.
     *
     * @param array $settings         Settings option.
     */
    public function __construct( $settings ){
        $this->settings = $settings;

        $trp = TRP_Translate_Press::get_trp_instance();

        if ( ! $this->machine_translator_logger ) {
            $this->machine_translator_logger = $trp->get_component('machine_translator_logger');
        }

        if ( ! $this->trp_languages ) {
            $this->trp_languages = $trp->get_component('languages');
        }

        $this->machine_translation_codes = $this->trp_languages->get_iso_codes($this->settings['translation-languages']);

        $this->schedule_machine_translation_lock_cleanup();
    }

    /**
     * Schedule the recurring cleanup of the locks left behind by requests killed mid translation.
     */
    protected function schedule_machine_translation_lock_cleanup(){
        if ( self::$lock_cleanup_hooked ){
            return;
        }
        self::$lock_cleanup_hooked = true;

        add_action( 'trp_machine_translation_lock_cleanup', array( $this, 'cleanup_machine_translation_locks_cron' ) );

        if ( ! wp_next_scheduled( 'trp_machine_translation_lock_cleanup' ) ){
            wp_schedule_event( time() + HOUR_IN_SECONDS, 'hourly', 'trp_machine_translation_lock_cleanup' );
        }
    }

    /**
     * Cron callback clearing abandoned machine translation locks.
     */
    public function cleanup_machine_translation_locks_cron(){
        $this->get_trp_query()->cleanup_machine_translation_locks( $this->get_machine_translation_lock_timeout() );
    }

    /**
     * Whether automatic translation is available.
     *
     * @param array $languages
     * @return bool
     */
    public function is_available( $languages = array() ) {
        /**
         * Return false in case it was directly called from parent and not from a derived class (DeepL / Google / TPAI)
         * Calling this method on the parent class means it was called too early and machine translation is not available at this point
         */
        if ( get_class( $this ) === __CLASS__ )
            return false;

        $settings = $this->settings['trp_machine_translation_settings'] ?? array();
        $enabled  = ( $settings['machine-translation'] ?? '' ) === 'yes';
        $is_available = false;

        if ( $enabled ) {
            $engine = $settings['translation-engine'] ?? null;

            if ( $engine === 'deepl' && get_option( 'trp_license_status' ) !== 'valid' ) {
                $is_available = false;
            } elseif ( empty( $languages ) ) {
                $is_available = true;
            } else {
                $is_available = $this->check_languages_availability( $languages );
            }
        }

        return apply_filters( 'trp_machine_translator_is_available', $is_available, $languages, $settings );
    }

    public function check_languages_availability( $languages, $force_recheck = false ){
        if ( !method_exists( $this, 'get_supported_languages' ) || !method_exists( $this, 'get_engine_specific_language_codes' )){
            return true;
        }
        $force_recheck = ( current_user_can('manage_options') &&
            !empty( $_GET['trp_recheck_supported_languages']) && $_GET['trp_recheck_supported_languages'] === '1' &&
            wp_verify_nonce( sanitize_text_field( $_GET['trp_recheck_supported_languages_nonce'] ), 'trp_recheck_supported_languages' ) ) ? true : $force_recheck; //phpcs:ignore
        $data = get_option('trp_db_stored_data', array() );
        if ( isset( $_GET['trp_recheck_supported_languages'] )) {
            unset($_GET['trp_recheck_supported_languages'] );
        }

        // if supported languages are not stored, fetch them and update option
        if ( empty( $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'] ) || $force_recheck || ( method_exists($this,'check_formality') && !isset($data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages']))){
            if ( empty( $data['trp_mt_supported_languages'] ) ) {
                $data['trp_mt_supported_languages'] = array();
            }
            if ( empty( $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ] ) ) {
                $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ] = array( 'languages' => array() );
            }

            $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ]['languages'] = $this->get_supported_languages();
            if (method_exists($this, 'check_formality')) {
                $data['trp_mt_supported_languages'][ $this->settings['trp_machine_translation_settings']['translation-engine'] ]['formality-supported-languages'] = $this->check_formality();
            }
            $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'] = date("Y-m-d H:i:s" );
            update_option('trp_db_stored_data', $data );
        }

        $languages_iso_to_check = $this->get_engine_specific_language_codes( $languages );

        $all_are_available = !array_diff($languages_iso_to_check, $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['languages']);

        return apply_filters('trp_mt_available_supported_languages', $all_are_available, $languages, $this->settings );
    }

    public function get_last_checked_supported_languages(){
        $data = get_option('trp_db_stored_data', array() );
        if ( empty( $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'] ) ){
            $this->check_languages_availability( $this->settings['translation-languages'], true);
        }
        return $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['last-checked'];
    }

    /**
     * Output an SVG based on translation engine and error flag.
     *
     * @param bool $show_errors true to show an error SVG, false if not.
     */
    public function automatic_translation_svg_output( $show_errors ) {
        if ( method_exists( $this, 'automatic_translate_error_check' ) ) {
            if ( $show_errors ) {
                trp_output_svg( 'error' );
            } else {
                trp_output_svg( 'check' );
            }
        }
        
    }

    /**
     *
     * @deprecated
     * Check the automatic translation API keys for Google Translate and DeepL
     *
     * @param TRP_Translate_Press $machine_translator Machine translator instance.
     * @param string $translation_engine              The translation engine (can be google_translate_v2 and deepl).
     * @param string $api_key                         The API key to check.
     *
     * @return array [ (string) $message, (bool) $error ].
     */
    public function automatic_translate_error_check( $machine_translator, $translation_engine, $api_key ) {

        $is_error       = false;
        $return_message = '';

        switch ( $translation_engine ) {
            case 'google_translate_v2':
                if ( empty( $api_key ) ) {
                    $is_error = true;
                    $return_message = __( 'Please enter your Google Translate key.', 'translatepress-multilingual' );
                } else {
                    // Perform test.
                    $response = $machine_translator->test_request();
                    $code     = wp_remote_retrieve_response_code( $response );
                    if ( 200 !== $code ) {
                        $is_error        = true;
                        $translate_response = trp_gt_response_codes( $code );
                        $return_message     = $translate_response['message'];
                    }
                }
                break;
            case 'deepl':
                if ( empty( $api_key ) ) {
                    $is_error = true;
                    $return_message = __( 'Please enter your DeepL API key.', 'translatepress-multilingual' );
                } else {
                    // Perform test.
                    $is_error= false;
                    $response = $machine_translator->test_request();
                    $code     = wp_remote_retrieve_response_code( $response );
                    if ( 200 !== $code && ( method_exists( 'TRP_DeepL', 'deepl_response_codes' ) || method_exists( 'TRP_IN_DeepL', 'deepl_response_codes' ) ) ) {

						// Test whether the old deepL add-on or the new repackaging model is used
						if ( method_exists( 'TRP_DeepL', 'deepl_response_codes' ) ) {
							$translate_response = TRP_DeepL::deepl_response_codes( $code );
						} else {
							$translate_response = TRP_IN_DeepL::deepl_response_codes( $code );
						}
	                    $is_error       = true;
                        $return_message = $translate_response['message'];
                    }
                }
                break;
            default:
                break;
        }


        $this->correct_api_key=array(
            'message' => $return_message,
            'error'   => $is_error,
        );

        return $this->correct_api_key;
    }

    // checking if the api_key is correct in order to display unsupported languages

    public function is_correct_api_key(){

        if(method_exists($this, 'check_api_key_validity')){
            $verification = $this->check_api_key_validity();
        }else {
            //we only need this values for automatic translate error check function for backwards compatibility

            $machine_translator = $this;
            $translation_engine = $this->settings['trp_machine_translation_settings']['translation-engine'];
            $api_key = $this->get_api_key();
            $verification = $this->automatic_translate_error_check( $machine_translator, $translation_engine, $api_key );
        }
        if($verification['error']== false) {
            return true;
        }
        return false;
    }


	/**
	 * Return site referer
	 *
	 * @return string
	 */
	public function get_referer(){
		if( ! $this->referer ) {
			if( ! $this->url_converter ) {
				$trp = TRP_Translate_Press::get_trp_instance();
				$this->url_converter = $trp->get_component( 'url_converter' );
			}

			$this->referer = $this->url_converter->get_abs_home();
		}

		return apply_filters( 'trp_machine_translator_referer', $this->referer );
	}

    /**
     * Verifies that the machine translation request is valid
     * @deprecated  since TP 1.6.0 (only here to support Deepl Add-on version 1.0.0)
     *
     * @param  string $to_language language we're looking to translate to
     * @return bool
     */
    public function verify_request( $to_language ){

        if( empty( $this->get_api_key() ) ||
            empty( $to_language ) || $to_language == $this->settings['default-language'] ||
            empty( $this->machine_translation_codes[$this->settings['default-language']] )
          )
            return false;

        // Method that can be extended in the child class to add extra validation
        if( !$this->extra_request_validations( $to_language ) )
            return false;

        // Check if crawlers are blocked
        if( !empty( $this->settings['trp_machine_translation_settings']['block-crawlers'] ) && $this->settings['trp_machine_translation_settings']['block-crawlers'] == 'yes' && $this->is_crawler() )
            return false;

        // Check if daily quota is met
        if( $this->machine_translator_logger->quota_exceeded() )
            return false;

        return true;

    }

    /**
     * Verifies that the machine translation request is valid
     *
     * @param  string $target_language_code language we're looking to translate to
     * @param  string $source_language_code language we're looking to translate from
     * @return bool
     */
    public function verify_request_parameters($target_language_code, $source_language_code){
        if( empty( $this->get_api_key() ) ||
            empty( $target_language_code ) || empty( $source_language_code ) ||
            empty( $this->machine_translation_codes[$target_language_code] ) ||
            empty( $this->machine_translation_codes[$source_language_code] ) ||
            $this->machine_translation_codes[$target_language_code] == $this->machine_translation_codes[$source_language_code]
        )
            return false;

        // Method that can be extended in the child class to add extra validation
        if( !$this->extra_request_validations( $target_language_code ) )
            return false;

        // Check if crawlers are blocked
        if( !empty( $this->settings['trp_machine_translation_settings']['block-crawlers'] ) && $this->settings['trp_machine_translation_settings']['block-crawlers'] == 'yes' && $this->is_crawler() )
            return false;

        // Check if daily quota is met
        if( $this->machine_translator_logger->quota_exceeded() )
            return false;

        return true;
    }

    /**
     * Verifies user agent to check if the request is being made by a crawler
     *
     * @return boolean
     */
    private function is_crawler(){
        if( !isset( $_SERVER['HTTP_USER_AGENT'] ) )
            return false;

        $crawlers = apply_filters( 'trp_machine_translator_crawlers', 'rambler|abacho|acoi|accona|aspseek|altavista|estyle|scrubby|lycos|geona|ia_archiver|alexa|sogou|skype|facebook|twitter|pinterest|linkedin|naver|bing|google|yahoo|duckduckgo|yandex|baidu|teoma|xing|java\/1.7.0_45|bot|crawl|slurp|spider|mediapartners|\sask\s|\saol\s' );

        return preg_match( '/'. $crawlers .'/i', sanitize_text_field ( $_SERVER['HTTP_USER_AGENT'] ) );
    }

    /**
     * Check if a string should be sent for translation based on minimum length and content criteria
     *
     * @param string $string The string to check
     * @return bool True if the string should be translated, false otherwise
     */
    private function should_translate_string( $string ) {
        // Reject single strings that exceed the smallest engine body limit
        if ( strlen( $string ) > self::MAX_STRING_BYTES ) {
            return false;
        }

        // Trim whitespace for accurate length check
        $trimmed = trim( $string );

        // Check if string is empty after trimming
        if ( empty( $trimmed ) ) {
            return false;
        }

        // Get minimum length (default: 2 characters)
        // Allow customization via filter
        $min_length = apply_filters( 'trp_minimum_translation_length', 2 );

        // Check minimum length
        if ( mb_strlen( $trimmed, 'UTF-8' ) < $min_length ) {
            return false;
        }

        // Check if string is only punctuation/special characters (optional, can be disabled via filter)
        $skip_punctuation_only = apply_filters( 'trp_skip_punctuation_only_strings', true );
        if ( $skip_punctuation_only && preg_match( '/^[[:punct:][:space:]]+$/u', $trimmed ) ) {
            return false;
        }

        // Base64-encoded strings — require at least one digit or `=` padding so letter-only words
        // separated by `/` (e.g. "Certifikater/Batchstyring", "TCP/IP-style paths") aren't matched.
        if ( preg_match( '/^[A-Za-z0-9+\/]{20,}={0,2}$/', $trimmed ) && preg_match( '/[0-9=]/', $trimmed ) ) {
            return false;
        }

        // Pure numbers, dates, dimensions (no letters at all)
        if ( preg_match( '/^[\d\s.,;:x×\-–—+\/()%°\'\"]+$/', $trimmed ) ) {
            return false;
        }

        // ISO 8601 datetimes (e.g. 2026-01-06T16:50:29+00:00, ...Z, with optional fractional seconds)
        if ( preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?([+-]\d{2}:?\d{2}|Z)?$/', $trimmed ) ) {
            return false;
        }

        // Standalone email addresses
        if ( preg_match( '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $trimmed ) ) {
            return false;
        }

        // Bare URLs or srcset-style URL lists (https://... 300w, https://... 1024w)
        if ( preg_match( '/^https?:\/\//', $trimmed ) && ! preg_match( '/[a-zA-ZÀ-ž]{2,}\s+[a-zA-ZÀ-ž]{2,}/', preg_replace( '/https?:\/\/\S+/', '', $trimmed ) ) ) {
            return false;
        }

        // Standalone media file references with no scheme: a bare filename ("photo.png"), a relative
        // path ("wp-content/uploads/2026/06/img.webp"), a protocol-relative URL ("//cdn.com/a.jpg"),
        // or a srcset-style comma list of these with width/density descriptors ("a.png 300w, b.png 600w").
        // The https:// rule above misses these because they have no scheme, so image/media file names
        // would otherwise be sent for machine translation. Anchored to the whole string so real prose
        // that merely mentions a file (e.g. "Download report.pdf now") is left translatable.
        $media_ext = 'png|jpe?g|gif|bmp|webp|avif|svg|ico|tiff?|mp4|m4v|webm|ogv|mov|avi|mp3|ogg|wav|m4a|flac|pdf';
        $media_ref = '\S+\.(?:' . $media_ext . ')(?:\?\S*)?';
        if ( preg_match( '/^' . $media_ref . '(?:\s+\d+(?:\.\d+)?[wx])?(?:\s*,\s*' . $media_ref . '(?:\s+\d+(?:\.\d+)?[wx])?)*$/i', $trimmed ) ) {
            return false;
        }

        // HTML media/void tags that are purely markup (no meaningful translatable text outside attributes)
        if ( preg_match( '/^<(img|source|video|picture|input|br|hr|meta|link)\s/i', $trimmed ) && ! preg_match( '/>([^<]{10,})</', $trimmed ) ) {
            return false;
        }

        // Self-closing HTML tags only (multiple img/source tags with no text between them)
        if ( preg_match( '/^(<[a-z]+\s[^>]*\/>\s*)+$/i', $trimmed ) && ! preg_match( '/>([^<]{10,})</', $trimmed ) ) {
            return false;
        }

        // HTML-only strings — at least one tag and no letters remaining once tags/comments are stripped
        if ( preg_match( '/<[a-z!\/][^>]*>/i', $trimmed ) && ! preg_match( '/[a-zA-ZÀ-ž]/', preg_replace( '/<[^>]+>/', '', $trimmed ) ) ) {
            return false;
        }

        // WordPress plugin/theme file paths (wp-content/plugins/...:123)
        if ( preg_match( '/^wp-content\/(plugins|themes)\/\S+\.(php|js|css)(:\d+)?$/', $trimmed ) ) {
            return false;
        }

        // Divi/shortcode builder markup (starts with [ shortcode tag and is mostly attributes)
        if ( preg_match( '/^\[?\/?et_pb_/', $trimmed ) || preg_match( '/^\[\/et_pb_/', $trimmed ) ) {
            return false;
        }

        // JavaScript code blocks.
        // Inline/minified scripts (GTM, gtag, analytics, etc.) sometimes reach this point, often
        // split into arbitrary ~2KB chunks that start mid-statement. The patterns below are loose
        // on purpose and matched anywhere in the string (not anchored to the start) so a chunk that
        // begins in the middle of an expression is still recognised. They look for syntax that does
        // not occur in natural-language content. Designed against real machine_translation_log data.

        // A "prose run" = 3+ consecutive natural-language words (3+ letters each, separated only by
        // spaces/common punctuation). Minified JS has no such runs; help text and UI labels do. It
        // is used below to exempt only the loosest JS signals from matching genuine content.
        $has_prose_run = preg_match( '/(?:\b\p{L}{3,}\b[\s,.\'’]+){2,}\b\p{L}{3,}\b/u', $trimmed );

        // Structural keywords: anchored function/var declarations, function expressions, for/var
        // declarations, `new Foo(`, control flow and operator keywords. These never occur in prose.
        if ( preg_match( '/^\(\s*function\s*\(/', $trimmed )
            || preg_match( '/=\s*function\s*\(/', $trimmed )
            || preg_match( '/^(var|let|const|function)\s+\w+\s*[=(]/', $trimmed )
            || preg_match( '/\bfor\s*\(\s*var\s/', $trimmed )
            || preg_match( '/;\s*var\s+[\w$]/', $trimmed )
            || preg_match( '/=\s*new\s+[A-Z]\w*\(/', $trimmed )
            || preg_match( '/\belse\s+if\s*\(/', $trimmed )
            || preg_match( '/;\s*case\s/', $trimmed )
            || preg_match( '/\b(typeof|instanceof)\b/', $trimmed ) ) {
            return false;
        }

        // Object/member idioms: prototype chains, the `void 0` minifier idiom, BigInt(), and the
        // common built-in method calls minifiers emit (e.g. `.push(`, `.charCodeAt(`).
        if ( preg_match( '/\.prototype\./', $trimmed )
            || preg_match( '/\bvoid 0\b/', $trimmed )
            || preg_match( '/\bBigInt\s*\(/', $trimmed )
            || preg_match( '/\.(push|pop|shift|unshift|charCodeAt|charAt|substring|substr|indexOf|lastIndexOf|slice|splice|concat|toString|hasOwnProperty|call|apply|replace|split|join|sort|filter|map|forEach)\s*\(/', $trimmed ) ) {
            return false;
        }

        // Minified operator "soup" (strict members): `.length` access, pre/post increment inside a
        // loop, ternaries like `)?1:0` and array indexing like `=a[b`.
        if ( preg_match( '/\b\w+\.length\b/', $trimmed )
            || preg_match( '/\+\+[a-z]\)|[a-z]\+\+\)/', $trimmed )
            || preg_match( '/\)\?[\w\'"]+:/', $trimmed )
            || preg_match( '/=\w+\[\w/', $trimmed ) ) {
            return false;
        }

        // Base64/URL-safe charset table emitted by consent-string builders (e.g. GTM). The full
        // ordered alphabet run never appears in natural language.
        if ( preg_match( '/0123456789abcdefghijklmnopqrstuvwxyz/i', $trimmed ) ) {
            return false;
        }

        // Loose JS signals: a minified `function x(a,b){`, bitwise shifts (`<<x`/`x>>`), and logical
        // operators (`&&`/`||`). These can appear in genuine content (CTAs ending in `>>`, help text
        // quoting code, URLs/search titles with `||`), so they only fire when there is NO prose run.
        if ( ! $has_prose_run && (
               preg_match( '/\bfunction\s*[\w$]*\s*\([\w$,\s]*\)\s*\{/', $trimmed )
            || preg_match( '/<<\w|\w>>>?/', $trimmed )
            || preg_match( '/&&|\|\|/', $trimmed ) ) ) {
            return false;
        }

        // PDF internals
        if ( preg_match( '/endobj\s*\n?\s*xref/', $trimmed ) ) {
            return false;
        }

        // XML declarations and SVG markup
        if ( preg_match( '/^<\?xml\s/', $trimmed ) || preg_match( '/^<svg[\s>]/i', $trimmed ) ) {
            return false;
        }

        // Data URIs (base64-encoded images, fonts, SVG+XML, etc.)
        if ( preg_match( '/^data:[a-z]+\/[a-z0-9+.\-]+(;[a-z0-9=\-]+)*,/i', $trimmed ) ) {
            return false;
        }

        // Mustache/Handlebars/Vue template tags with no translatable text outside the tags
        if ( preg_match( '/\{\{[^{}]*\}\}/', $trimmed ) && ! preg_match( '/[a-zA-ZÀ-ž]/', preg_replace( '/\{\{+[^{}]*\}+\}/', '', $trimmed ) ) ) {
            return false;
        }

        // CSS fragments — custom property declarations, at-rules, or url(data:...) constructs.
        // Loose by design, but guarded: only skip if the string doesn't contain real HTML content
        // tags (so HTML pages with inline `style="--foo: ..."` attributes aren't rejected).
        if ( ( preg_match( '/--[a-zA-Z][a-zA-Z0-9_-]*\s*:\s*\S/', $trimmed )
            || preg_match( '/^\s*@(media|import|font-face|keyframes|supports|charset|page|namespace|layer)\b/i', $trimmed )
            || preg_match( '/\burl\s*\(\s*[\'"]?data:/i', $trimmed ) )
            && ! preg_match( '/<(p|div|span|a|h[1-6]|article|section|ul|ol|li|main|header|footer|nav|figure|figcaption|blockquote|table|tr|td|th|trp-post-container)\b/i', $trimmed ) ) {
            return false;
        }

        // Binary/mojibake data — three or more clusters of 2+ consecutive '?' or U+FFFD
        // characters. Real prose almost never repeats '?' or replacement chars; binary
        // data captured as text (PDF/font/image bytes, broken encodings) does so heavily.
        if ( preg_match_all( '/[?\x{FFFD}]{2,}/u', $trimmed ) >= 3 ) {
            return false;
        }

        return true;
    }

    /**
     * Function to be used externally
     *
     * Strings locked by a concurrent request ( in flight or recently translated ) are dropped from the
     * return array. Locks claimed here are held until the caller saves the translations and calls
     * release_locks( true ).
     *
     * @param array $strings
     * @param string $target_language_code
     * @param string|null $source_language_code
     * @param string $dictionary_context        Dictionary the caller saves to: 'regular' or 'gettext'.
     * @return array
     */
    public function translate($strings, $target_language_code, $source_language_code = null, $dictionary_context = 'regular' ){
        if ( !empty($strings) && is_array($strings) && method_exists( $this, 'translate_array' ) && apply_filters( 'trp_disable_automatic_translations_due_to_error', false ) === false ) {

            // TRP_Machine_Translation_Spans wraps excluded words in spans (1TPnT tokens inside attribute values) and sends %% as 1TPnT; %s, %d go as they are (1TPnT for Google), %, $, # always
            $imploded_strings = implode(" ", $strings);
            $trp_exclude_words_from_automatic_translation = apply_filters('trp_exclude_words_from_automatic_translation', array(), $imploded_strings, $target_language_code, $source_language_code);
            $shortcode_tags_to_execute = apply_filters( 'trp_do_these_shortcodes_before_automatic_translation', array('trp_language', 'language-include', 'language-exclude') );

            $strings = array_unique($strings);
            $original_strings = $strings;

            // Filter out strings that are too short to translate
            $strings_to_skip = array();
            $strings_to_translate = array();
            foreach ($strings as $key => $string) {
                if ( !$this->should_translate_string($string) ) {
                    $strings_to_skip[$key] = $string;
                } else {
                    $strings_to_translate[$key] = $string;
                }
            }

            // decode and wrap before claiming, so a string skipped for its sent size is never locked
            $decoded_strings = array();
            $sent_strings = array();
            foreach ($strings_to_translate as $key => $string) {
                $decoded_strings[$key] = trp_do_these_shortcodes( html_entity_decode( $string ), $shortcode_tags_to_execute );
                /**
                 * Filter a string right before it is sent to the translation engine. TranslatePress wraps
                 * the excluded words, and for TranslatePress AI the glossary terms, in spans here (priority 10),
                 * as 1TPnT tokens inside attribute values, and the escaped percent sign %% as a 1TPnT token;
                 * markup added here must be removed again on trp_machine_translation_string_after_receive.
                 *
                 * @param string      $string               Decoded string, shortcodes executed.
                 * @param string      $target_language_code
                 * @param string|null $source_language_code
                 * @param mixed       $exclude_words        Result of trp_exclude_words_from_automatic_translation for this call, normally an array.
                 */
                $sent_strings[$key] = apply_filters( 'trp_machine_translation_string_before_send', $decoded_strings[$key], $target_language_code, $source_language_code, $trp_exclude_words_from_automatic_translation );
                // the limit applies to what is sent; wrapping can push a string over it
                if ( strlen( $sent_strings[$key] ) > self::MAX_STRING_BYTES ) {
                    $strings_to_skip[$key] = $string;
                    unset( $strings_to_translate[$key] );
                }
            }

            // If all strings are too short, return them as-is. Keyed by the original string, the same
            // way the regular return below is: callers look the result up by original string
            // ( isset( $machine_strings[ $string ] ) ), so returning the incoming index-keyed array
            // here meant nothing was ever matched. The translations were then saved against the array
            // index instead of the original ( rows with original = "10" ), the real string stayed
            // NOT TRANSLATED, and it was collected and re-processed again on every single page load.
            if ( empty($strings_to_translate) ) {
                $skipped_strings_return_array = array();
                foreach ( $original_strings as $original_string ) {
                    $skipped_strings_return_array[ $original_string ] = $original_string;
                }
                return $skipped_strings_return_array;
            }

            // Continue with only the strings that meet the minimum length
            $strings = $strings_to_translate;

            // claim the strings so concurrent requests don't send the same string to the engine twice
            $strings = $this->claim_strings_for_machine_translation( $strings, $target_language_code, $source_language_code, $dictionary_context );
            $sent_strings = array_intersect_key( $sent_strings, $strings );
            $strings = array_intersect_key( $decoded_strings, $strings );

            $machine_strings = array();
            if ( !empty( $strings ) ) {
                try {
                    $machine_strings = $this->translate_array($sent_strings, $target_language_code, $source_language_code);
                } finally {
                    if ( empty( $machine_strings ) ) {
                        // nothing to save, free the locks now
                        $this->release_locks();
                    }
                }
            }

            $machine_strings_return_array = array();
            if (!empty($machine_strings)) {
                foreach ($machine_strings as $key => $machine_string) {
                    // An engine is expected to return the keys it was given. If it re-indexed them we
                    // have no original string to key the result by, and using the bare key would save
                    // the translation against a meaningless original. Skip instead, so the string is
                    // simply left untranslated for this request.
                    if ( !isset( $original_strings[$key] ) ) {
                        continue;
                    }

                    $machine_string   = $this->decode_engine_entities( $machine_string, $sent_strings[$key], $original_strings[$key], $dictionary_context );
                    /**
                     * Filter a translation returned by the engine, before quotes and punctuation are restored and it
                     * is saved. TranslatePress unwraps the glossary spans and tokens at priority 10, the excluded
                     * word ones at 20, and strips leftover spans (restoring leftover tokens) at 30.
                     *
                     * @param string      $machine_string       Translation returned by the engine, with the entities it added decoded (see decode_engine_entities()).
                     * @param string      $sent_string          String that was sent, after trp_machine_translation_string_before_send.
                     * @param string      $original_string      Original string as passed to translate().
                     * @param string      $target_language_code
                     * @param string|null $source_language_code
                     * @param mixed       $exclude_words        Result of trp_exclude_words_from_automatic_translation for this call, normally an array.
                     */
                    $processed_string = apply_filters( 'trp_machine_translation_string_after_receive', $machine_string, $sent_strings[$key], $original_strings[$key], $target_language_code, $source_language_code, $trp_exclude_words_from_automatic_translation );

                    // Restore quote patterns (use $strings which is decoded, not $original_strings with HTML entities)
                    $processed_string = $this->restore_translation_quotes($strings[$key], $processed_string);

                    // Restore punctuation and spacing patterns (use $strings which is decoded, not $original_strings with HTML entities)
                    $processed_string = $this->restore_punctuation_patterns($strings[$key], $processed_string);

                    $machine_strings_return_array[$original_strings[$key]] = $processed_string;
                }
            }

            // Add skipped strings back to the return array with their original values
            foreach ($strings_to_skip as $key => $skipped_string) {
                $machine_strings_return_array[$original_strings[$key]] = $original_strings[$key];
            }

            return $machine_strings_return_array;
        }else {
            return array();
        }
    }

    /**
     * Decode the entities an engine added (Google &#39; &quot;, OpenRouter &#x27;). Entities that were already in the
     * sent text stay. &lt; &gt; &amp; (any notation) stay escaped in regular strings, which are HTML, so text like
     * 5 &lt; 6 or an engine-escaped tag doesn't become markup. In gettext strings, which are plain text, each of them
     * is decoded only when the original contains that character unescaped.
     *
     * @param string $machine_string
     * @param string $sent_string
     * @param string $original_string       Original string as the caller passed it, before html_entity_decode().
     * @param string $dictionary_context    'regular' or 'gettext', see translate().
     * @return string
     */
    public function decode_engine_entities( $machine_string, $sent_string, $original_string = '', $dictionary_context = 'regular' ) {
        if ( !is_string( $machine_string ) || strpos( $machine_string, '&' ) === false ) {
            return $machine_string;
        }
        $entity_pattern = '(?:#[0-9]+|#x[0-9a-f]+|[a-z][a-z0-9]*);';
        $keep_escaped   = array( '<' => true, '>' => true, '&' => true );
        if ( $dictionary_context === 'gettext' && is_string( $original_string ) ) {
            $keep_escaped['<'] = strpos( $original_string, '<' ) === false;
            $keep_escaped['>'] = strpos( $original_string, '>' ) === false;
            $keep_escaped['&'] = !preg_match( '/&(?!' . $entity_pattern . ')/i', $original_string );
        }
        $decoded = preg_replace_callback( '/&' . $entity_pattern . '/i', function ( $entity ) use ( $sent_string, $keep_escaped ) {
            if ( is_string( $sent_string ) && strpos( $sent_string, $entity[0] ) !== false ) {
                return $entity[0];
            }
            $character = html_entity_decode( $entity[0], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
            // &nvlt; decodes to < plus a combining mark
            foreach ( $keep_escaped as $escaped => $keep ) {
                if ( $keep && strpos( $character, $escaped ) !== false ) {
                    return $entity[0];
                }
            }
            return $character;
        }, $machine_string );
        return $decoded === null ? $machine_string : $decoded;
    }

    /**
     * Claim each string in the machine translation locks table, returning only the ones this request may translate.
     *
     * Drops the strings another request is translating right now or translated in the last lock timeout
     * seconds ( locks are kept after a save as recently translated markers ). Also defers strings when
     * lock contention persists after retries. These strings are handled on a following page load.
     * The claimed locks stay in place after returning: they must
     * also cover the dictionary save in the caller.
     *
     * @param array $strings                        Strings to translate, keys are preserved.
     * @param string $target_language_code
     * @param string|null $source_language_code
     * @param string $dictionary_context            'regular' or 'gettext', see translate().
     * @return array                                The strings this request should translate. All of them when
     *                                              locking is disabled via filter or unavailable.
     */
    protected function claim_strings_for_machine_translation( $strings, $target_language_code, $source_language_code, $dictionary_context ){
        if ( empty( $strings ) || !is_array( $strings ) ){
            return array();
        }

        if ( !apply_filters( 'trp_prevent_concurrent_machine_translation', true, $strings, $target_language_code, $source_language_code ) ){
            return $strings;
        }

        $trp_query = $this->get_trp_query();

        // if the locks table can't be created, don't hold up translations because of it
        if ( !$trp_query->check_machine_translation_lock_table() ){
            return $strings;
        }

        // the context keeps regular and gettext locks for the same text apart
        $string_hashes = array();
        foreach( $strings as $key => $string ){
            $string_hashes[$key] = sha1( $dictionary_context . '|' . $source_language_code . '|' . $target_language_code . '|' . $string );
        }

        $lock_owner = wp_generate_uuid4();
        $claimed_hashes = $trp_query->claim_machine_translation_locks( array_values( $string_hashes ), $lock_owner, $this->get_machine_translation_lock_timeout() );

        // Don't send an unprotected batch to the engine when another request prevents claiming it.
        if ( $claimed_hashes === false ){
            $trp_query->delete_machine_translation_locks( array( $lock_owner ) );
            $this->remember_lock_skipped_strings( $strings );
            return array();
        }

        // locking is unavailable, translate everything rather than nothing
        if ( $claimed_hashes === null ){
            $trp_query->delete_machine_translation_locks( array( $lock_owner ) );
            return $strings;
        }

        // every string is locked by concurrent requests, nothing was claimed
        if ( empty( $claimed_hashes ) ){
            $this->remember_lock_skipped_strings( $strings );
            return array();
        }

        $this->hold_lock_owner( $lock_owner );

        $claimed_hashes = array_flip( $claimed_hashes );

        $claimed_strings = array();
        foreach( $string_hashes as $key => $string_hash ){
            if ( isset( $claimed_hashes[$string_hash] ) ){
                $claimed_strings[$key] = $strings[$key];
            }
        }

        $this->remember_lock_skipped_strings( array_diff_key( $strings, $claimed_strings ) );

        return $claimed_strings;
    }

    /**
     * Remember strings skipped because they are locked or lock contention prevented claiming them.
     */
    protected function remember_lock_skipped_strings( $strings ){
        foreach( $strings as $string ){
            $this->lock_skipped_strings[ $string ] = true;
        }
    }

    /**
     * Strings this request dropped because a concurrent request holds their lock, keyed by string.
     *
     * The lock holder inserts these into the dictionary when it saves, so callers must not register
     * them as untranslated strings themselves: that would duplicate the holder's rows.
     *
     * @return array
     */
    public function get_lock_skipped_strings(){
        return $this->lock_skipped_strings;
    }

    /**
     * Remember a lock owner token until its locks are released.
     */
    protected function hold_lock_owner( $lock_owner ){
        if ( empty( $this->held_lock_owners ) ){
            // PHP_INT_MAX so the hook still runs when registered during shutdown ( gettext translates on shutdown )
            add_action( 'shutdown', array( $this, 'release_locks' ), PHP_INT_MAX, 0 );
        }
        $this->held_lock_owners[] = $lock_owner;
    }

    /**
     * Stop tracking every machine translation lock this instance still holds.
     *
     * Callers of translate() must call this after saving the returned translations, with
     * $translations_saved = true: the locks then stay in place until the lock timeout as "recently
     * translated" markers, blocking concurrent requests whose dictionary read predates the save from
     * re-translating the same strings. Otherwise the locks are deleted so the strings can be retried.
     *
     * @param bool $translations_saved      Whether the translations were saved to the dictionary.
     */
    public function release_locks( $translations_saved = false ){
        if ( empty( $this->held_lock_owners ) ){
            return;
        }

        $lock_owners            = $this->held_lock_owners;
        $this->held_lock_owners = array();

        if ( $translations_saved ){
            $this->get_trp_query()->refresh_machine_translation_locks( $lock_owners );
        } else {
            $this->get_trp_query()->delete_machine_translation_locks( $lock_owners );
        }
    }

    /**
     * Seconds after which a machine translation lock is considered abandoned and can be taken over.
     *
     * Must exceed the engine call plus the dictionary save — every engine sends its HTTP request with
     * a 45s timeout, so a shorter value lets a concurrent request take over a lock whose engine call
     * is still in flight and re-bill the same strings. Must also stay above
     * trp_machine_translation_time_budget, so locks kept after a save outlive any concurrent request
     * still working off a dictionary read that predates the save.
     *
     * @return int
     */
    public function get_machine_translation_lock_timeout(){
        return (int) apply_filters( 'trp_machine_translation_lock_timeout', 50 ); // seconds
    }

    /**
     * @return TRP_Query
     */
    protected function get_trp_query(){
        if ( !$this->trp_query ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->trp_query = $trp->get_component( 'query' );
        }

        return $this->trp_query;
    }

    /**
     * Maximum number of strings the engine sends to the API in a single request.
     *
     * Frontend callers (regular DOM + gettext) chunk by this size and save each chunk to the
     * database before requesting the next one, so an aborted/overlapping page load never re-sends
     * (and re-bills) an already-saved chunk. Engines override this with their own API limit.
     *
     * @return int
     */
    public function get_chunk_size() {
        return 50;
    }


    public function test_request(){}

    public function get_api_key(){
        return false;
    }

    public function extra_request_validations( $to_language ){
        return true;
    }

    /**
     * Restore and normalize quotes in translated strings to match the original
     *
     * @param string $original_string The original untranslated string
     * @param string $translated_string The translated string from the API
     * @return string The translated string with quotes restored and normalized
     */
    public function restore_translation_quotes($original_string, $translated_string) {
        // Allow disabling this functionality via filter
        if ( apply_filters( 'trp_disable_restore_translation_quotes', false ) ) {
            return $translated_string;
        }

        // Check if original string is empty or translated string is empty
        if ( empty($original_string) || empty($translated_string) ) {
            return $translated_string;
        }

        // Define all quote characters to check for
        $quote_chars = [
            // Straight quotes
            "'",   // U+0027 Apostrophe / single quote
            '"',   // U+0022 Double quote

            // Curly / typographic quotes
            '‘',   // U+2018 Left single curly quote
            '’',   // U+2019 Right single curly quote (also apostrophe)
            '“',   // U+201C Left double curly quote
            '”',   // U+201D Right double curly quote

            // Common international quotes
            '„',   // U+201E Low double quote (German, Polish)
            '‚',   // U+201A Low single quote
            '«',   // U+00AB Left double angle quote (French, Italian, etc.)
            '»',   // U+00BB Right double angle quote
        ];

        // Step 1: Restore boundary quotes if they were stripped
        $original_first_char = mb_substr($original_string, 0, 1, 'UTF-8');
        $original_last_char = mb_substr($original_string, -1, 1, 'UTF-8');
        $translated_first_char = mb_substr($translated_string, 0, 1, 'UTF-8');
        $translated_last_char = mb_substr($translated_string, -1, 1, 'UTF-8');

        // Check if original starts with a quote character and translated doesn't
        if ( in_array($original_first_char, $quote_chars, true) && !in_array($translated_first_char, $quote_chars, true) ) {
            $translated_string = $original_first_char . $translated_string;
        }

        // Check if original ends with a quote character and translated doesn't
        // Need to recalculate last char if we added a quote at the beginning
        if ( in_array($original_last_char, $quote_chars, true) && !in_array($translated_last_char, $quote_chars, true) ) {
            $translated_string = $translated_string . $original_last_char;
        }

        // Step 2: Normalize ALL quotes - collect all quotes from original (excluding apostrophes)
        $original_quotes = array();
        $original_len = mb_strlen($original_string, 'UTF-8');

        for ($i = 0; $i < $original_len; $i++) {
            $char = mb_substr($original_string, $i, 1, 'UTF-8');
            if ( in_array($char, $quote_chars, true) ) {
                // Check if this is an apostrophe (letter-apostrophe-letter pattern)
                $prev_char = ($i > 0) ? mb_substr($original_string, $i - 1, 1, 'UTF-8') : '';
                $next_char = ($i < $original_len - 1) ? mb_substr($original_string, $i + 1, 1, 'UTF-8') : '';

                $is_apostrophe = ($char === "'" || $char === '’') &&
                    preg_match('/^\p{L}$/u', $prev_char) &&
                    preg_match('/^\p{L}$/u', $next_char);

                if ( !$is_apostrophe ) {
                    $original_quotes[] = $char;
                }
            }
        }

        // If we found quotes in the original, replace them in order in the translation
        if ( !empty($original_quotes) ) {
            $quote_index = 0;
            $translated_len = mb_strlen($translated_string, 'UTF-8');
            $translated_chars = array();

            // Convert translation to array of characters
            for ($i = 0; $i < $translated_len; $i++) {
                $translated_chars[] = mb_substr($translated_string, $i, 1, 'UTF-8');
            }

            // Replace all quotes in translation in order (excluding apostrophes)
            for ($i = 0; $i < $translated_len; $i++) {
                $char = $translated_chars[$i];
                if ( in_array($char, $quote_chars, true) ) {
                    // Check if this is an apostrophe (letter-apostrophe-letter pattern)
                    $prev_char = ($i > 0) ? $translated_chars[$i - 1] : '';
                    $next_char = ($i < $translated_len - 1) ? $translated_chars[$i + 1] : '';

                    $is_apostrophe = ($char === "'" || $char === '’') &&
                        preg_match('/^\p{L}$/u', $prev_char) &&
                        preg_match('/^\p{L}$/u', $next_char);

                    if ( !$is_apostrophe && $quote_index < count($original_quotes) ) {
                        $translated_chars[$i] = $original_quotes[$quote_index];
                        $quote_index++;
                    }
                }
            }

            $translated_string = implode('', $translated_chars);
        }

        return $translated_string;
    }

    /**
     * Restore punctuation and spacing patterns at string boundaries
     *
     * Handles patterns at the beginning or end of strings:
     * - Two-character: comma, period or semicolon with a space before or after it
     * - Single character: "," (comma), "." (period), ";" (semicolon), " " (space)
     * No mark is added when the translation already has one at that boundary (see boundary_has_punctuation()):
     * the same mark, a script-native sentence/clause mark such as the Chinese full stop U+3002, or a final "!" / "?".
     *
     * @param string $original_string The original string (entity-decoded, shortcodes executed, excluded words replaced by placeholders)
     * @param string $translated_string The translated string from the API
     * @return string The translated string with punctuation patterns restored
     */
    public function restore_punctuation_patterns($original_string, $translated_string) {
        // Allow disabling this functionality via filter
        if ( apply_filters( 'trp_disable_restore_punctuation_patterns', false ) ) {
            return $translated_string;
        }

        // Check if original string is empty or translated string is empty
        if ( empty($original_string) || empty($translated_string) ) {
            return $translated_string;
        }

        // Define patterns to check (longer patterns first to avoid partial matches)
        // leading or trailing spaces are trimmed by trp_full_trim() inside translate_page but we still check here as "space+comma" at the end is valid for example
        $patterns = [', ', '. ', '; ', ' ,', ' .', ' ;', ',', '.', ';', ' '];

        // Check all patterns and restore at both leading and trailing positions
        foreach ($patterns as $pattern) {
            $pattern_len = mb_strlen($pattern, 'UTF-8');
            $punctuation = trim($pattern);

            // Check and restore leading pattern
            $original_start = mb_substr($original_string, 0, $pattern_len, 'UTF-8');
            $translated_start = mb_substr($translated_string, 0, $pattern_len, 'UTF-8');

            // Spacing differences must not cause an existing punctuation mark to be duplicated.
            if ($original_start === $pattern && $translated_start !== $pattern
                && ($punctuation === '' || !$this->boundary_has_punctuation($translated_string, $punctuation, false, $original_string))) {
                $translated_string = $pattern . $translated_string;
            }

            // Check and restore trailing pattern
            $original_end = mb_substr($original_string, -$pattern_len, $pattern_len, 'UTF-8');
            $translated_end = mb_substr($translated_string, -$pattern_len, $pattern_len, 'UTF-8');

            if ($original_end === $pattern && $translated_end !== $pattern
                && ($punctuation === '' || !$this->boundary_has_punctuation($translated_string, $punctuation, true, $original_string))) {
                $translated_string = $translated_string . $pattern;
            }
        }

        return $translated_string;
    }

    /**
     * Whether the translation already starts or ends with a punctuation mark that stands in for $punctuation
     *
     * Only the first/last 1 KB (after trimming plain whitespace) is inspected. Whitespace and invisible characters are ignored, and at the end also
     * closing HTML tags (stripped before entities are decoded, so an escaped "&lt;/b&gt;" is text, not a tag).
     * A boundary has the mark when it ends/starts with:
     * - the same ASCII mark (e.g. "Bonjour.&nbsp;", "Lire <a>plus.</a>"); always kept, the filter is not consulted;
     * - at the end, "!" or "?" as the last visible character (not inside brackets, quotes or a tag: "(vraiment !)" still gets ".");
     * - a script-native sentence/clause mark, at the end also behind closing quotes, brackets or tags (U+3002 inside closing quotes or a link).
     * An ASCII "." behind a closer does not count, so the "." of "(etc.)" does not replace the sentence's full stop.
     * At the start, opening marks such as Spanish inverted question/exclamation marks do not count.
     * Beyond the window the old behaviour applies (the mark is out of reach and gets restored): more than 1 KB of trailing
     * tags or of entity/invisible spacing (&nbsp;, U+00A0, U+200B), or invalid UTF-8 inside the window (preg fails, so only
     * the old byte-level exact match is done). NUL and vertical tab next to the mark are trimmed like other whitespace.
     *
     * @param string $translated_string The translation being processed (after placeholder and quote restoration; may contain HTML entities)
     * @param string $punctuation The ASCII mark that would be restored: ",", "." or ";"
     * @param bool $at_end True to check the end of the string, false for the start
     * @param string $original_string The original string (entity-decoded, shortcodes executed, excluded words replaced by placeholders), passed to the filter
     * @return bool
     */
    private function boundary_has_punctuation($translated_string, $punctuation, $at_end, $original_string) {
        $ignored = '[\s\p{Z}\x{00AD}\x{061C}\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}\x{2066}-\x{2069}\x{FEFF}]';
        // Plain whitespace is trimmed from the whole string first, so a long run of it cannot hide the mark from the window
        $text   = $at_end ? rtrim($translated_string) : ltrim($translated_string);
        $limit  = 1024;
        // Byte length even when mbstring.func_overload (PHP < 8) makes strlen() count characters, since mb_strcut works in bytes
        $length = mb_strlen($text, '8bit');
        if ( $length > $limit ) {
            // At the end the cut must run to the end of the string: mb_strcut moves a mid-character start back, which would drop the last character
            $text = $at_end ? mb_strcut($text, $length - $limit, $length, 'UTF-8') : mb_strcut($text, 0, $limit, 'UTF-8');
            if ( $at_end ) {
                // A cut inside an entity leaves a fragment such as "sp;" that would decode to a literal ";"
                $text = preg_replace('/^[#a-zA-Z0-9]{0,31};/', '', $text);
            }
        }

        if ( $at_end ) {
            $visible   = preg_replace('/' . $ignored . '+$/u', '', $text);
            $untagged  = is_string($visible) ? $this->strip_trailing_closing_tags($visible, $ignored) : null;
            $behind_tag = $untagged !== $visible;
        } else {
            $untagged  = preg_replace('/^' . $ignored . '+/u', '', $text);
            $behind_tag = false;
        }

        // Engines like Google v2 return HTML entities (&quot;); decode only to inspect the boundary
        $bare = is_string($untagged) ? html_entity_decode($untagged, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
        $bare = is_string($bare) ? preg_replace($at_end ? '/' . $ignored . '+$/u' : '/^' . $ignored . '+/u', '', $bare) : null;

        if ( !is_string($bare) ) {
            // preg failure (e.g. invalid UTF-8): keep the byte-level exact-match check
            $trimmed = $at_end ? rtrim($translated_string) : ltrim($translated_string);
            return ( $at_end ? substr($trimmed, -1) : substr($trimmed, 0, 1) ) === $punctuation;
        }

        $char = mb_substr($bare, $at_end ? -1 : 0, 1, 'UTF-8');
        if ( $char === $punctuation ) {
            return true;
        }

        // Script-native sentence and clause marks: CJK full/half-width, Arabic/Urdu, Devanagari, Armenian, Ethiopic, Myanmar, Khmer, Tibetan, ellipses
        $native_marks = '/^[\x{3002}\x{FF0E}\x{FF61}\x{FF0C}\x{3001}\x{FF64}\x{FF1B}\x{FF1A}\x{FF01}\x{FF1F}\x{060C}\x{061B}\x{061F}\x{06D4}\x{0964}\x{0965}\x{0589}\x{1362}\x{1363}\x{104A}\x{104B}\x{17D4}\x{0F0D}\x{2026}\x{2025}]$/u';

        if ( $at_end && !$behind_tag && ( $char === '!' || $char === '?' ) ) {
            $has_mark = true;
        } elseif ( $at_end ) {
            $inner    = preg_replace('/[\s\p{Z}\x{00AD}\x{061C}\x{200B}-\x{200F}\x{202A}-\x{202E}\x{2060}\x{2066}-\x{2069}\x{FEFF}\p{Pe}\p{Pf}\p{Pi}"\']+$/u', '', $bare);
            $char     = is_string($inner) ? mb_substr($inner, -1, 1, 'UTF-8') : $char;
            $has_mark = preg_match($native_marks, $char) === 1;
        } else {
            $has_mark = preg_match($native_marks, $char) === 1;
        }

        /**
         * Override whether the translation already has a mark at this boundary, so the original's ASCII mark is not restored.
         * Not applied when the same ASCII mark is already present (that is always kept).
         *
         * @param bool   $has_mark          Decision so far.
         * @param string $char              Boundary character that was inspected (after decoding and trimming); may be '' or an ASCII
         *                                  mark that did not count (e.g. the "." of "(etc.)").
         * @param string $punctuation       ASCII mark that would be restored: ",", "." or ";".
         * @param bool   $at_end            True for the end of the string, false for the start.
         * @param string $original_string   Original string (entity-decoded, shortcodes executed, excluded words replaced by placeholders).
         * @param string $translated_string Translation being processed (after placeholder and quote restoration; may contain HTML entities).
         */
        return (bool) apply_filters( 'trp_restore_punctuation_boundary_has_mark', $has_mark, $char, $punctuation, $at_end, $original_string, $translated_string );
    }

    /**
     * Strip trailing closing HTML tags (and whitespace between them)
     *
     * @param string $text
     * @param string $ignored Character class of whitespace/invisible characters
     * @return string|null Null on preg failure
     */
    private function strip_trailing_closing_tags($text, $ignored) {
        do {
            $previous = $text;
            $text = preg_replace('/<\/[a-zA-Z][a-zA-Z0-9-]*\s*>$/', '', $text);
            if ( is_string($text) ) {
                $text = preg_replace('/' . $ignored . '+$/u', '', $text);
            }
        } while ( is_string($text) && $text !== $previous );

        return $text;
    }

}
