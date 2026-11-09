<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class TRP_Abilities
 *
 * Registers TranslatePress abilities with the WordPress Abilities API.
 * Only registers if the Abilities API is present (WordPress 6.9+ or the
 * Abilities API feature plugin). Reuses existing settings and license
 * code paths instead of duplicating logic.
 *
 * Registered abilities (all require `manage_options`):
 *   translatepress/add-language                    POST   add a translation language
 *   translatepress/remove-language                 POST   remove a translation language
 *   translatepress/update-language                 POST   change a language's slug / publish flag
 *   translatepress/set-default-language            POST   promote a configured language to default
 *   translatepress/list-languages                  GET    list configured languages
 *   translatepress/list-available-languages        GET    list the full locale catalog
 *   translatepress/set-license-key                 POST   save and validate a license key
 *   translatepress/enable-automatic-translation    POST   turn on Automatic Translation (requires valid license)
 *
 * Calling them from PHP:
 *
 *   $ability = wp_get_ability( 'translatepress/add-language' );
 *   $result  = $ability->execute( array( 'language_code' => 'fr_FR' ) );
 *   if ( is_wp_error( $result ) ) { ... }
 *
 * Calling them over REST (requires authentication):
 *
 *   POST /wp-json/wp-abilities/v1/abilities/translatepress/add-language/run
 *   Content-Type: application/json
 *   { "input": { "language_code": "fr_FR" } }
 *
 * See the Abilities API docs for schemas, annotations, and execution details:
 *
 * @see https://developer.wordpress.org/apis/abilities-api/
 */
class TRP_Abilities {

    const CATEGORY = 'translatepress';

    public function __construct() {
        if ( ! $this->is_abilities_api_available() ) {
            return;
        }

        add_action( 'wp_abilities_api_categories_init', array( $this, 'register_category' ) );
        add_action( 'wp_abilities_api_init', array( $this, 'register_abilities' ) );
    }

    private function is_abilities_api_available(): bool {
        return class_exists( 'WP_Ability' ) && function_exists( 'wp_register_ability' );
    }

    public function register_category(): void {
        wp_register_ability_category(
            self::CATEGORY,
            array(
                'label'       => __( 'TranslatePress', 'translatepress-multilingual' ),
                'description' => __( 'Abilities for managing TranslatePress configuration.', 'translatepress-multilingual' ),
            )
        );
    }

    public function register_abilities(): void {
        wp_register_ability( 'translatepress/add-language', array(
            'label'               => __( 'Add a translation language', 'translatepress-multilingual' ),
            'description'         => __( 'Adds a translation language to TranslatePress. Enforces the same per-license limit as the settings UI.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(
                    'language_code' => array(
                        'type'        => 'string',
                        'description' => __( 'WordPress locale code (e.g. fr_FR, de_DE).', 'translatepress-multilingual' ),
                        'minLength'   => 1,
                    ),
                    'slug'          => array(
                        'type'        => 'string',
                        'description' => __( 'Optional URL slug. Defaults to the ISO language code if omitted.', 'translatepress-multilingual' ),
                    ),
                ),
                'required'             => array( 'language_code' ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'added'                 => array( 'type' => 'string' ),
                    'translation_languages' => array(
                        'type'  => 'array',
                        'items' => array( 'type' => 'string' ),
                    ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_add_language' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array( 'show_in_rest' => true ),
        ) );

        wp_register_ability( 'translatepress/set-license-key', array(
            'label'               => __( 'Set TranslatePress license key', 'translatepress-multilingual' ),
            'description'         => __( 'Saves a TranslatePress license key and triggers a remote license check, mirroring the onboarding flow.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(
                    'license_key' => array(
                        'type'        => 'string',
                        'description' => __( 'The license key to activate.', 'translatepress-multilingual' ),
                        'minLength'   => 1,
                    ),
                ),
                'required'             => array( 'license_key' ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'status' => array( 'type' => 'string' ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_set_license_key' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array( 'show_in_rest' => true ),
        ) );

        wp_register_ability( 'translatepress/enable-automatic-translation', array(
            'label'               => __( 'Enable Automatic Translation', 'translatepress-multilingual' ),
            'description'         => __( 'Turns on Automatic Translation. A valid TranslatePress license is required only when the resolved engine is TranslatePress AI (mtapi); Google Translate and DeepL do not require one. If no engine is passed and none is configured, defaults to mtapi.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(
                    'engine' => array(
                        'type'        => 'string',
                        'description' => __( 'Optional translation-engine slug to select (e.g. "mtapi", "google_translate_v2", "deepl"). When omitted, keeps the already-configured engine, or defaults to "mtapi" if none is configured.', 'translatepress-multilingual' ),
                        'minLength'   => 1,
                    ),
                ),
                'additionalProperties' => false,
                // Empty-object input; the same `default: stdClass` trick used by the
                // GET abilities lets this be called with no `input` payload at all.
                'default'              => new stdClass(),
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'machine_translation' => array( 'type' => 'string' ),
                    'translation_engine'  => array( 'type' => 'string' ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_enable_automatic_translation' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array( 'show_in_rest' => true ),
        ) );

        wp_register_ability( 'translatepress/list-languages', array(
            'label'               => __( 'List configured languages', 'translatepress-multilingual' ),
            'description'         => __( 'Returns the languages currently configured in TranslatePress, including the default language and per-language slug/publish state.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(),
                'additionalProperties' => false,
                // GET requests cannot deliver an object via query string (the Abilities
                // API does not JSON-decode `?input=...`). Providing a default lets callers
                // omit the parameter entirely and have normalize_input() fill it in.
                'default'              => new stdClass(),
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'default_language'      => array( 'type' => 'string' ),
                    'translation_languages' => array(
                        'type'  => 'array',
                        'items' => array(
                            'type'       => 'object',
                            'properties' => array(
                                'code'      => array( 'type' => 'string' ),
                                'name'      => array( 'type' => 'string' ),
                                'slug'      => array( 'type' => 'string' ),
                                'published' => array( 'type' => 'boolean' ),
                                'is_default'=> array( 'type' => 'boolean' ),
                            ),
                        ),
                    ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_list_languages' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array(
                'show_in_rest' => true,
                'annotations'  => array( 'readonly' => true ),
            ),
        ) );

        wp_register_ability( 'translatepress/list-available-languages', array(
            'label'               => __( 'List available language codes', 'translatepress-multilingual' ),
            'description'         => __( 'Returns the full catalog of language codes TranslatePress recognizes, with their English names.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(),
                'additionalProperties' => false,
                'default'              => new stdClass(),
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'languages' => array(
                        'type'  => 'array',
                        'items' => array(
                            'type'       => 'object',
                            'properties' => array(
                                'code' => array( 'type' => 'string' ),
                                'name' => array( 'type' => 'string' ),
                            ),
                        ),
                    ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_list_available_languages' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array(
                'show_in_rest' => true,
                'annotations'  => array( 'readonly' => true ),
            ),
        ) );

        wp_register_ability( 'translatepress/remove-language', array(
            'label'               => __( 'Remove a translation language', 'translatepress-multilingual' ),
            'description'         => __( 'Removes a configured translation language. The default language cannot be removed without first reassigning the default.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(
                    'language_code' => array(
                        'type'        => 'string',
                        'description' => __( 'Locale code of the language to remove.', 'translatepress-multilingual' ),
                        'minLength'   => 1,
                    ),
                ),
                'required'             => array( 'language_code' ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'removed'               => array( 'type' => 'string' ),
                    'translation_languages' => array(
                        'type'  => 'array',
                        'items' => array( 'type' => 'string' ),
                    ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_remove_language' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array(
                'show_in_rest' => true,
                // Marked destructive for semantic clarity. We deliberately omit
                // 'idempotent' because the Abilities API routes destructive+idempotent
                // to DELETE, whose query-string `input` is not JSON-decoded — so
                // object-shaped input would fail schema validation over the wire.
                'annotations'  => array( 'destructive' => true ),
            ),
        ) );

        wp_register_ability( 'translatepress/update-language', array(
            'label'               => __( 'Update a translation language', 'translatepress-multilingual' ),
            'description'         => __( 'Replaces the slug and publish status of an already-configured translation language.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(
                    'language_code' => array(
                        'type'        => 'string',
                        'description' => __( 'Locale code of the language to update.', 'translatepress-multilingual' ),
                        'minLength'   => 1,
                    ),
                    'slug'          => array(
                        'type'        => 'string',
                        'description' => __( 'URL slug for this language.', 'translatepress-multilingual' ),
                        'minLength'   => 1,
                    ),
                    'published'     => array(
                        'type'        => 'boolean',
                        'description' => __( 'Whether this language is published on the front end.', 'translatepress-multilingual' ),
                    ),
                ),
                'required'             => array( 'language_code', 'slug', 'published' ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'updated'   => array( 'type' => 'string' ),
                    'slug'      => array( 'type' => 'string' ),
                    'published' => array( 'type' => 'boolean' ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_update_language' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array( 'show_in_rest' => true ),
        ) );

        wp_register_ability( 'translatepress/set-default-language', array(
            'label'               => __( 'Set the default language', 'translatepress-multilingual' ),
            'description'         => __( 'Promotes an already-configured language to be the site default. The previous default remains in the translation languages list.', 'translatepress-multilingual' ),
            'category'            => self::CATEGORY,
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(
                    'language_code' => array(
                        'type'        => 'string',
                        'description' => __( 'Locale code to promote to default. Must already be a configured translation language.', 'translatepress-multilingual' ),
                        'minLength'   => 1,
                    ),
                ),
                'required'             => array( 'language_code' ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'default_language'         => array( 'type' => 'string' ),
                    'previous_default_language'=> array( 'type' => 'string' ),
                ),
            ),
            'execute_callback'    => array( $this, 'execute_set_default_language' ),
            'permission_callback' => array( $this, 'permission_manage_options' ),
            'meta'                => array( 'show_in_rest' => true ),
        ) );
    }

    public function permission_manage_options() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_Error(
                'trp_abilities_forbidden',
                __( 'You do not have permission to manage TranslatePress settings.', 'translatepress-multilingual' ),
                array( 'status' => 403 )
            );
        }
        return true;
    }

    /**
     * Adds a single translation language by reusing TRP_Settings::sanitize_settings().
     *
     * Mirrors TRP_Step_Languages::save_languages() — same option key, same
     * sanitization path — so register_setting() hooks, table creation, etc.
     * all fire identically to a UI save.
     */
    public function execute_add_language( $input ) {
        $language_code = isset( $input['language_code'] ) ? sanitize_text_field( $input['language_code'] ) : '';

        if ( ! trp_is_valid_language_code( $language_code ) ) {
            return new WP_Error(
                'trp_invalid_language_code',
                __( 'Invalid language code. Allowed characters: A-Z, a-z, 0-9, hyphen, underscore.', 'translatepress-multilingual' )
            );
        }

        $trp        = TRP_Translate_Press::get_trp_instance();
        $languages  = $trp->get_component( 'languages' )->get_languages();
        $settings_c = $trp->get_component( 'settings' );

        if ( ! array_key_exists( $language_code, $languages ) ) {
            return new WP_Error(
                'trp_unknown_language',
                sprintf(
                    /* translators: %s: language code */
                    __( 'Language code "%s" is not recognized by TranslatePress.', 'translatepress-multilingual' ),
                    $language_code
                )
            );
        }

        $trp_settings          = get_option( 'trp_settings', array() );
        $default_language      = isset( $trp_settings['default-language'] ) ? $trp_settings['default-language'] : 'en_US';
        $translation_languages = isset( $trp_settings['translation-languages'] ) ? (array) $trp_settings['translation-languages'] : array();
        $publish_languages     = isset( $trp_settings['publish-languages'] ) ? (array) $trp_settings['publish-languages'] : array();
        $url_slugs             = isset( $trp_settings['url-slugs'] ) ? (array) $trp_settings['url-slugs'] : array();

        if ( $language_code === $default_language || in_array( $language_code, $translation_languages, true ) ) {
            return new WP_Error(
                'trp_language_already_added',
                sprintf(
                    /* translators: %s: language code */
                    __( 'Language "%s" is already configured.', 'translatepress-multilingual' ),
                    $language_code
                )
            );
        }

        // Same limit gate the UI uses — extra-languages add-on extends this via the filter when a pro license is valid.
        $max_secondary = (int) apply_filters( 'trp_secondary_languages', 1 );
        $secondary_count = count( array_diff( $translation_languages, array( $default_language ) ) );
        if ( $secondary_count >= $max_secondary ) {
            return new WP_Error(
                'trp_language_limit_reached',
                sprintf(
                    /* translators: %d: maximum number of secondary languages */
                    __( 'Your current license allows up to %d additional language(s). Upgrade or activate a Pro license to add more.', 'translatepress-multilingual' ),
                    $max_secondary
                ),
                array( 'max_secondary_languages' => $max_secondary )
            );
        }

        $translation_languages[] = $language_code;
        $publish_languages[]     = $language_code;

        $slug = isset( $input['slug'] ) ? sanitize_title( $input['slug'] ) : '';
        if ( '' === $slug ) {
            $slug = sanitize_title( strtok( $language_code, '_' ) );
        }
        $url_slugs[ $language_code ] = $slug;

        $trp_settings['default-language']      = $default_language;
        $trp_settings['translation-languages'] = array_values( array_unique( $translation_languages ) );
        $trp_settings['publish-languages']     = array_values( array_unique( $publish_languages ) );
        $trp_settings['url-slugs']             = $url_slugs;

        $sanitized = $settings_c->sanitize_settings( $trp_settings );

        // sanitize_settings() can short-circuit with a get_option fallback when invalid codes slip through.
        if ( ! is_array( $sanitized ) || ! in_array( $language_code, (array) $sanitized['translation-languages'], true ) ) {
            return new WP_Error(
                'trp_settings_rejected',
                __( 'TranslatePress rejected the new language during settings sanitization.', 'translatepress-multilingual' )
            );
        }

        update_option( 'trp_settings', $sanitized );

        return array(
            'added'                 => $language_code,
            'translation_languages' => array_values( (array) $sanitized['translation-languages'] ),
        );
    }

    /**
     * Saves the license key and triggers a live check by reusing
     * TRP_AI_API_Key_Check::force_check_tp_api_key(). Mirrors TRP_Step_AI_API_Key::handle().
     */
    public function execute_set_license_key( $input ) {
        // Input schema's minLength gate already rejects empty values before we get here.
        $license = sanitize_text_field( $input['license_key'] );
        update_option( 'trp_license_key', $license );

        $trp = TRP_Translate_Press::get_trp_instance();
        $trp->get_component( 'plugin_updater' )->force_check_tp_api_key( 'true' );

        $license_details = get_option( 'trp_license_details' );
        if ( ! empty( $license_details['invalid'][0] ) ) {
            $detail = $license_details['invalid'][0];
            $error  = isset( $detail->error ) ? $detail->error : 'license_error';
            return new WP_Error(
                'trp_license_' . $error,
                $this->license_error_message( $error, $detail )
            );
        }

        if ( function_exists( 'trp_mtapi_sync_license_call' ) ) {
            trp_mtapi_sync_license_call( $license );
        }

        return array(
            'status' => (string) get_option( 'trp_license_status', '' ),
        );
    }

    /**
     * Turns on Automatic Translation.
     *
     * License gating rule: only the TranslatePress AI (mtapi) engine relies on a
     * TranslatePress license. Google Translate v2 and DeepL are third-party
     * services with their own API keys, so we deliberately do NOT check
     * `trp_license_status` when the resolved engine is one of those — enforcing
     * a TP license on top would be against WP.org guidelines (locking
     * unrelated third-party functionality behind our license check).
     *
     * Engine resolution, in order:
     *   1. `$input['engine']` when provided, wins.
     *   2. Existing `trp_machine_translation_settings['translation-engine']`.
     *   3. Falls back to 'mtapi'.
     */
    public function execute_enable_automatic_translation( $input = array() ) {
        // Normalize: REST delivers an assoc array; direct PHP callers may pass
        // an stdClass (that's what the `default: new stdClass()` schema produces
        // when no `input` payload is supplied).
        $input = is_array( $input ) ? $input : (array) $input;

        $settings = get_option( 'trp_machine_translation_settings', array() );
        if ( ! is_array( $settings ) ) {
            $settings = array();
        }

        $requested_engine = isset( $input['engine'] ) ? sanitize_text_field( (string) $input['engine'] ) : '';
        $existing_engine  = isset( $settings['translation-engine'] ) ? (string) $settings['translation-engine'] : '';

        if ( '' !== $requested_engine ) {
            $engine = $requested_engine;
        } elseif ( '' !== $existing_engine ) {
            $engine = $existing_engine;
        } else {
            $engine = 'mtapi';
        }

        if ( 'mtapi' === $engine && 'valid' !== get_option( 'trp_license_status' ) ) {
            return new WP_Error(
                'trp_license_not_valid',
                __( 'Enabling Automatic Translation with the TranslatePress AI engine requires an active TranslatePress license. Activate a license first with translatepress/set-license-key, or pass a different engine (e.g. "google_translate_v2", "deepl").', 'translatepress-multilingual' )
            );
        }

        $settings['machine-translation'] = 'yes';
        $settings['translation-engine']  = $engine;

        update_option( 'trp_machine_translation_settings', $settings );

        return array(
            'machine_translation' => 'yes',
            'translation_engine'  => $engine,
        );
    }

    /**
     * Mirrors the error strings used in TRP_Step_AI_API_Key::check_license_validation_results().
     */
    private function license_error_message( string $error, $detail ): string {
        switch ( $error ) {
            case 'expired':
                $expires = isset( $detail->expires ) ? strtotime( $detail->expires, current_time( 'timestamp' ) ) : false;
                if ( $expires ) {
                    return sprintf(
                        /* translators: %s: license expiration date */
                        __( 'Your license key expired on %s.', 'translatepress-multilingual' ),
                        date_i18n( get_option( 'date_format' ), $expires )
                    );
                }
                return __( 'Your license key has expired.', 'translatepress-multilingual' );
            case 'revoked':
                return __( 'Your license key has been disabled.', 'translatepress-multilingual' );
            case 'missing':
                return __( 'Your TranslatePress license key is invalid or missing.', 'translatepress-multilingual' );
            case 'invalid':
            case 'site_inactive':
                return __( 'Your license key is disabled for this URL. Re-enable it from your TranslatePress account.', 'translatepress-multilingual' );
            case 'item_name_mismatch':
                return __( 'License key mismatch. The license you entered does not match the TranslatePress version you have installed.', 'translatepress-multilingual' );
            case 'no_activations_left':
                return __( 'Your license key has reached its activation limit.', 'translatepress-multilingual' );
            case 'website_already_on_free_license':
                return trp_get_tp_ai_api_key_labels( 'already_on_free' );
            default:
                return __( 'An error occurred while activating the license, please try again.', 'translatepress-multilingual' );
        }
    }

    public function execute_list_languages() {
        $trp_settings = get_option( 'trp_settings', array() );
        $trp          = TRP_Translate_Press::get_trp_instance();
        $names        = $trp->get_component( 'languages' )->get_languages();

        $default     = isset( $trp_settings['default-language'] ) ? $trp_settings['default-language'] : '';
        $translation = isset( $trp_settings['translation-languages'] ) ? (array) $trp_settings['translation-languages'] : array();
        $publish     = isset( $trp_settings['publish-languages'] ) ? (array) $trp_settings['publish-languages'] : array();
        $url_slugs   = isset( $trp_settings['url-slugs'] ) ? (array) $trp_settings['url-slugs'] : array();

        $items = array();
        foreach ( $translation as $code ) {
            $items[] = array(
                'code'       => $code,
                'name'       => isset( $names[ $code ] ) ? $names[ $code ] : $code,
                'slug'       => isset( $url_slugs[ $code ] ) ? $url_slugs[ $code ] : '',
                'published'  => in_array( $code, $publish, true ),
                'is_default' => ( $code === $default ),
            );
        }

        return array(
            'default_language'      => $default,
            'translation_languages' => $items,
        );
    }

    public function execute_list_available_languages() {
        $trp   = TRP_Translate_Press::get_trp_instance();
        $names = $trp->get_component( 'languages' )->get_languages();

        $items = array();
        foreach ( $names as $code => $name ) {
            $items[] = array( 'code' => $code, 'name' => $name );
        }
        return array( 'languages' => $items );
    }

    public function execute_remove_language( $input ) {
        $code = sanitize_text_field( $input['language_code'] );

        if ( ! trp_is_valid_language_code( $code ) ) {
            return new WP_Error(
                'trp_invalid_language_code',
                __( 'Invalid language code. Allowed characters: A-Z, a-z, 0-9, hyphen, underscore.', 'translatepress-multilingual' )
            );
        }

        $trp_settings          = get_option( 'trp_settings', array() );
        $default_language      = isset( $trp_settings['default-language'] ) ? $trp_settings['default-language'] : 'en_US';
        $translation_languages = isset( $trp_settings['translation-languages'] ) ? (array) $trp_settings['translation-languages'] : array();
        $publish_languages     = isset( $trp_settings['publish-languages'] ) ? (array) $trp_settings['publish-languages'] : array();
        $url_slugs             = isset( $trp_settings['url-slugs'] ) ? (array) $trp_settings['url-slugs'] : array();

        if ( ! in_array( $code, $translation_languages, true ) ) {
            return new WP_Error(
                'trp_language_not_configured',
                sprintf(
                    /* translators: %s: language code */
                    __( 'Language "%s" is not currently configured.', 'translatepress-multilingual' ),
                    $code
                )
            );
        }

        if ( $code === $default_language ) {
            return new WP_Error(
                'trp_cannot_remove_default',
                __( 'The default language cannot be removed. Set another language as default first.', 'translatepress-multilingual' )
            );
        }

        $trp_settings['translation-languages'] = array_values( array_diff( $translation_languages, array( $code ) ) );
        $trp_settings['publish-languages']     = array_values( array_diff( $publish_languages, array( $code ) ) );
        unset( $url_slugs[ $code ] );
        $trp_settings['url-slugs']             = $url_slugs;

        $sanitized = $this->save_trp_settings( $trp_settings );
        if ( is_wp_error( $sanitized ) ) {
            return $sanitized;
        }

        return array(
            'removed'               => $code,
            'translation_languages' => array_values( (array) $sanitized['translation-languages'] ),
        );
    }

    public function execute_update_language( $input ) {
        $code      = sanitize_text_field( $input['language_code'] );
        $slug      = sanitize_title( $input['slug'] );
        $published = (bool) $input['published'];

        if ( ! trp_is_valid_language_code( $code ) ) {
            return new WP_Error(
                'trp_invalid_language_code',
                __( 'Invalid language code. Allowed characters: A-Z, a-z, 0-9, hyphen, underscore.', 'translatepress-multilingual' )
            );
        }

        if ( '' === $slug ) {
            return new WP_Error(
                'trp_invalid_slug',
                __( 'Slug is empty after sanitization. Use only URL-safe characters.', 'translatepress-multilingual' )
            );
        }

        $trp_settings          = get_option( 'trp_settings', array() );
        $default_language      = isset( $trp_settings['default-language'] ) ? $trp_settings['default-language'] : 'en_US';
        $translation_languages = isset( $trp_settings['translation-languages'] ) ? (array) $trp_settings['translation-languages'] : array();
        $publish_languages     = isset( $trp_settings['publish-languages'] ) ? (array) $trp_settings['publish-languages'] : array();
        $url_slugs             = isset( $trp_settings['url-slugs'] ) ? (array) $trp_settings['url-slugs'] : array();

        if ( ! in_array( $code, $translation_languages, true ) ) {
            return new WP_Error(
                'trp_language_not_configured',
                sprintf(
                    /* translators: %s: language code */
                    __( 'Language "%s" is not currently configured. Add it first with translatepress/add-language.', 'translatepress-multilingual' ),
                    $code
                )
            );
        }

        // The default language is always published; refuse to unpublish it.
        if ( $code === $default_language && ! $published ) {
            return new WP_Error(
                'trp_cannot_unpublish_default',
                __( 'The default language is always published and cannot be unpublished.', 'translatepress-multilingual' )
            );
        }

        // Reject slug collision with another configured language.
        foreach ( $url_slugs as $other_code => $other_slug ) {
            if ( $other_code !== $code && $other_slug === $slug ) {
                return new WP_Error(
                    'trp_duplicate_slug',
                    sprintf(
                        /* translators: 1: slug, 2: language code */
                        __( 'Slug "%1$s" is already in use by language "%2$s".', 'translatepress-multilingual' ),
                        $slug,
                        $other_code
                    )
                );
            }
        }

        $url_slugs[ $code ] = $slug;
        $trp_settings['url-slugs'] = $url_slugs;

        $publish_set = array_flip( $publish_languages );
        if ( $published ) {
            $publish_set[ $code ] = true;
        } else {
            unset( $publish_set[ $code ] );
        }
        $trp_settings['publish-languages'] = array_keys( $publish_set );

        $sanitized = $this->save_trp_settings( $trp_settings );
        if ( is_wp_error( $sanitized ) ) {
            return $sanitized;
        }

        return array(
            'updated'   => $code,
            'slug'      => isset( $sanitized['url-slugs'][ $code ] ) ? $sanitized['url-slugs'][ $code ] : $slug,
            'published' => in_array( $code, (array) $sanitized['publish-languages'], true ),
        );
    }

    public function execute_set_default_language( $input ) {
        $code = sanitize_text_field( $input['language_code'] );

        if ( ! trp_is_valid_language_code( $code ) ) {
            return new WP_Error(
                'trp_invalid_language_code',
                __( 'Invalid language code. Allowed characters: A-Z, a-z, 0-9, hyphen, underscore.', 'translatepress-multilingual' )
            );
        }

        $trp_settings          = get_option( 'trp_settings', array() );
        $previous_default      = isset( $trp_settings['default-language'] ) ? $trp_settings['default-language'] : 'en_US';
        $translation_languages = isset( $trp_settings['translation-languages'] ) ? (array) $trp_settings['translation-languages'] : array();

        if ( $code === $previous_default ) {
            return array(
                'default_language'          => $code,
                'previous_default_language' => $previous_default,
            );
        }

        if ( ! in_array( $code, $translation_languages, true ) ) {
            return new WP_Error(
                'trp_language_not_configured',
                sprintf(
                    /* translators: %s: language code */
                    __( 'Language "%s" is not currently configured. Add it first with translatepress/add-language.', 'translatepress-multilingual' ),
                    $code
                )
            );
        }

        $trp_settings['default-language'] = $code;
        // sanitize_settings() will reinsert the new default at the head and keep the old default in the list.

        $sanitized = $this->save_trp_settings( $trp_settings );
        if ( is_wp_error( $sanitized ) ) {
            return $sanitized;
        }

        return array(
            'default_language'          => (string) $sanitized['default-language'],
            'previous_default_language' => $previous_default,
        );
    }

    /**
     * Runs trp_settings through TRP_Settings::sanitize_settings() and persists it.
     * Returns the sanitized array on success or WP_Error if sanitization rejected the payload.
     */
    private function save_trp_settings( array $trp_settings ) {
        $settings_c = TRP_Translate_Press::get_trp_instance()->get_component( 'settings' );
        $sanitized  = $settings_c->sanitize_settings( $trp_settings );

        if ( ! is_array( $sanitized ) || ! isset( $sanitized['translation-languages'] ) ) {
            return new WP_Error(
                'trp_settings_rejected',
                __( 'TranslatePress rejected the new settings during sanitization.', 'translatepress-multilingual' )
            );
        }

        update_option( 'trp_settings', $sanitized );
        return $sanitized;
    }
}
