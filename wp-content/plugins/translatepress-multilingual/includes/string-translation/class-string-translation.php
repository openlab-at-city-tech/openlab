<?php


class TRP_String_Translation {
    protected $settings;

    protected $loader;

    /* @var TRP_Translation_Manager */
    protected $translation_manager;

    // flat structure of string_types_config
    protected $string_types = array();

    // actual classes that may get retrieved from elsewhere through get_string_type_API()
    protected $string_type_apis = array();
    /**
     * @var array
     */
    protected $gettext_domains;

    public function __construct( $settings, $loader ) {
        $this->settings = $settings;
        $this->loader   = $loader;


    }

    public function register_ajax_hooks() {
        // Build a flat structure of string types
        $string_types_config = $this->string_types_config(false);
        foreach ( $string_types_config as $string_type_key => $string_type_value ) {
            if ( $string_type_value['category_based'] ) {
                foreach ( $string_type_value['categories'] as $substring_type_key => $substring_type_value ) {
                    $this->string_types[ $substring_type_key ] = $substring_type_value;
                }
            } else {
                $this->string_types[ $string_type_key ] = $string_type_value;
            }
        }

        // Include all classes and hooks needed for Visual Editor
        foreach ( $this->string_types as $string_type_key => $string_type_value ) {
	        if ( $string_type_key == 'emails' || (isset($string_type_value['type']) && $string_type_value['type'] == 'upsale-slugs' ) ) {
				// it's just gettext. We are using it to create an extra tab with this filter
				continue;
	        }

            require_once $string_type_value['plugin_path'] . 'includes/string-translation/class-string-translation-api-' . $string_type_key . '.php';
            $class_name                                 = 'TRP_String_Translation_API_' . $string_type_value['class_name_suffix'];
            $this->string_type_apis[ $string_type_key ] = new $class_name( $this->settings );

            // Different hook for String Translation compared to Visual Editor
            add_action( 'wp_ajax_trp_string_translation_get_strings_' . $string_type_key, array( $this->string_type_apis[ $string_type_key ], 'get_strings' ) );

			if ( $string_type_key == 'gettext' ) {
				add_action( 'wp_ajax_trp_string_translation_get_missing_gettext_strings', array(
					$this->string_type_apis[ 'gettext' ],
					'get_missing_gettext_strings'
				) );
				add_action( 'wp_ajax_trp_string_translation_get_strings_by_original_ids_gettext', array(
					$this->string_type_apis[ 'gettext' ],
					'get_strings_by_original_ids'
				) );
			}

	        // Same hook as for Visual Editor save translations
            add_action( 'wp_ajax_trp_save_translations_' . $string_type_key, array( $this->string_type_apis[ $string_type_key ], 'save_strings' ) );
        }
    }

    public function get_string_types() {
        return $this->string_types;
    }

    public function get_string_type_API( $string_type ) {
        return $this->string_type_apis[ $string_type ];
    }

    /**
     * Start String Translation Editor.
     *
     * Hooked to template_include.
     *
     * @param string $page_template Current page template.
     * @return string                       Template for translation Editor.
     */
    public function string_translation_editor( $page_template ) {
        if ( !$this->is_string_translation_editor() ) {
            return $page_template;
        }

        return TRP_PLUGIN_DIR . 'includes/string-translation/string-translation-editor.php';
    }

    /**
     * Return true if we are on String translation page.
     *
     * Also wp_die and show 'Cheating' message if we are on translation page but user does not have capabilities to view it
     *
     * @return bool
     */
    public function is_string_translation_editor() {
        if ( isset( $_REQUEST['trp-string-translation'] ) && sanitize_text_field( $_REQUEST['trp-string-translation'] ) === 'true' ) {
            if ( current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) && !is_admin() ) {
                return true;
            } else {
                wp_die(
                    '<h1>' . esc_html__( 'Cheatin&#8217; uh?' ) . '</h1>' . //phpcs:ignore
                    '<p>' . esc_html__( 'Sorry, you are not allowed to access this page.' ) . '</p>', //phpcs:ignore
                    403
                );
            }
        }
        return false;
    }

    /**
     * Enqueue script and styles for String Translation Editor page
     *
     * Hooked to trp_string_translation_editor_footer
     */
    public function enqueue_scripts_and_styles() {
        $trp = TRP_Translate_Press::get_trp_instance();
        if ( !$this->translation_manager ) {
            $this->translation_manager = $trp->get_component( 'translation_manager' );
        }


        wp_enqueue_style( 'trp-editor-style', TRP_PLUGIN_URL . 'assets/css/trp-editor.css', array( 'dashicons', 'buttons' ), TRP_PLUGIN_VERSION );
        wp_enqueue_script( 'trp-string-translation-editor', TRP_PLUGIN_URL . 'assets/js/trp-string-translation-editor.js', array(), TRP_PLUGIN_VERSION );

        wp_localize_script( 'trp-string-translation-editor', 'trp_editor_data', $this->translation_manager->get_trp_editor_data() );
        wp_localize_script( 'trp-string-translation-editor', 'trp_string_translation_data', $this->get_string_translation_data() );


        // Show upload media dialog in default language
        switch_to_locale( $this->settings['default-language'] );
        // Necessary for add media button
        wp_enqueue_media();

        // Necessary for add media button
        wp_print_media_templates();
        restore_current_locale();

        // Necessary for translate-dom-changes to have a nonce as the same user as the Editor.
        // The Preview iframe (which loads translate-dom-changes script) can load as logged out which sets an different nonce

        $scripts_to_print = apply_filters( 'trp-scripts-for-editor', array( 'jquery', 'jquery-ui-core', 'jquery-effects-core', 'jquery-ui-resizable', 'trp-string-translation-editor') );
        $styles_to_print  = apply_filters( 'trp-styles-for-editor', array( 'dashicons', 'trp-editor-style', 'media-views', 'imgareaselect', 'common', 'forms', 'list-tables', 'buttons' /*'wp-admin', 'common', 'site-icon', 'buttons'*/ ) );
        wp_print_scripts( $scripts_to_print );
        wp_print_styles( $styles_to_print );

        // Necessary for add media button
        print_footer_scripts();

    }

    public function get_string_translation_data() {
        $string_translation_data = array(
            'string_types_config'        => $this->string_types_config(true),
            'st_editor_strings'          => $this->get_st_editor_strings(),
            'translation_status_filters' => $this->get_translation_status_filters(),
            'default_actions'            => $this->get_default_actions(),
            'config'                     => $this->get_configuration_options()
        );
        return apply_filters( 'trp_string_translation_data', $string_translation_data );
    }

    public function get_translation_status_filters() {
        $filters = array(
            'translation_status' => array(
                'human_reviewed'     => esc_html__( 'Manually translated', 'translatepress-multilingual' ),
                'machine_translated' => esc_html__( 'Automatically translated', 'translatepress-multilingual' ),
                'not_translated'     => esc_html__( 'Not translated', 'translatepress-multilingual' )
            )

        );
        return apply_filters( 'trp_st_default_filters', $filters );
    }

    public function get_default_actions() {
        $actions = array(
            'bulk_actions' => array(
                'trp_default' => array( 'name' => esc_html__( 'Bulk Actions', 'translatepress-multilingual' ) ),
                'delete'      => array(
                    'name'  => esc_html__( 'Delete entries', 'translatepress-multilingual' ),
                    'nonce' => wp_create_nonce( 'string_translation_save_strings_delete' )
                ),
            ),
            'actions'      => array(
                'edit'   => esc_html__( 'Edit', 'translatepress-multilingual' )
            )
        );
        return apply_filters( 'trp_st_default_actions', $actions );
    }

    public function get_gettext_domains() {
        if ( !$this->gettext_domains ) {
            $trp          = TRP_Translate_Press::get_trp_instance();
            $trp_query    = $trp->get_component( 'query' );
            $trp_settings = $trp->get_component( 'settings' );
            $settings     = $trp_settings->get_settings();

            global $wpdb;
            $query = 'SELECT DISTINCT domain FROM `' . $trp_query->get_table_name_for_gettext_original_strings() . '` ORDER BY domain ASC';

            $this->gettext_domains = $wpdb->get_results( $query, OBJECT_K );
            foreach ( $this->gettext_domains as $domain => $value ) {
                $this->gettext_domains[ $domain ] = $domain;
            }
        }

        return $this->gettext_domains;
    }

    public function get_st_editor_strings() {
        $st_editor_strings = array(
	        'filter'                 => esc_html__( 'Filter', 'translatepress-multilingual' ),
	        'clear_filter'           => esc_html__( 'Clear filters', 'translatepress-multilingual' ),
	        'filter_by_language'     => esc_html__( 'Language', 'translatepress-multilingual' ),
	        'add_new'                => esc_html__( 'Add New', 'translatepress-multilingual' ),
	        'rescan_gettext'         => esc_html__( 'Rescan plugins and theme for strings', 'translatepress-multilingual' ),
	        'scanning_gettext'       => esc_html__( 'Scanning plugins and theme for strings...', 'translatepress-multilingual' ),
	        'gettext_scan_completed' => esc_html__( 'Plugins and theme scan is complete', 'translatepress-multilingual' ),
	        'gettext_scan_error'     => esc_html__( 'Plugins and theme scan did not finish due to an error', 'translatepress-multilingual' ),
	        'importexport'           => esc_html__( 'Import / Export', 'translatepress-multilingual' ),
	        'items'                  => esc_html__( 'items', 'translatepress-multilingual' ),
	        'of'                     => esc_html_x( 'of', 'page 1 of 3', 'translatepress-multilingual' ),
	        'see_more'               => esc_html__( 'See More', 'translatepress-multilingual' ),
	        'see_less'               => esc_html__( 'See Less', 'translatepress-multilingual' ),
	        'apply'                  => esc_html__( 'Apply', 'translatepress-multilingual' ),
	        'no_strings_match_query' => esc_html__( 'No strings match your query.', 'translatepress-multilingual' ),
	        'no_strings_match_rescan'=> esc_html__( 'Try to rescan plugins and theme for strings.', 'translatepress-multilingual' ),
	        'request_error'          => esc_html__( 'An error occurred while loading results. Most likely you were logged out. Reload page?', 'translatepress-multilingual' ),

	        'select_all'               => esc_html__( 'Select All', 'translatepress-multilingual' ),
	        'select_visible'           => esc_html__( 'Select Visible', 'translatepress-multilingual' ),
	        'select_all_warning'       => esc_html__( 'You are about to perform this action on all the strings matching your filter, not just the visibly checked. To perform the action only to the visible strings click "Select Visible" from the table header dropdown.', 'translatepress-multilingual' ),
	        'select_visible_warning'   => esc_html__( 'You are about to perform this action only on the visible strings. To perform the action on all the strings matching the filter click "Select All" from the table header dropdown.', 'translatepress-multilingual' ),
	        'type_a_word_for_security' => esc_html__( 'To continue please type the word:', 'translatepress-multilingual' ),
	        'incorect_word_typed'      => esc_html__( 'The word typed was incorrect. Action was cancelled.', 'translatepress-multilingual' ),

	        'in'                         => esc_html_x( 'in', 'Untranslated in this language', 'translatepress-multilingual' ),

	        // specific bulk actions
	        'delete_warning'             => esc_html__( 'Warning: This action cannot be undone. Deleting a string will remove its current translation. The original string will appear again in this interface after TranslatePress detects it. This action is NOT equivalent to excluding the string from being translated again.', 'translatepress-multilingual' ),

	        // tooltips
	        'next_page'                  => esc_html__( 'Navigate to next page', 'translatepress-multilingual' ),
	        'previous_page'              => esc_html__( 'Navigate to previous page', 'translatepress-multilingual' ),
	        'first_page'                 => esc_html__( 'Navigate to first page', 'translatepress-multilingual' ),
	        'last_page'                  => esc_html__( 'Navigate to last page', 'translatepress-multilingual' ),
	        'navigate_to_page'           => esc_html__( 'Type a page number to navigate to', 'translatepress-multilingual' ),
	        'wrong_page'                 => esc_html__( 'Incorrect page number. Type a page number between 1 and total number of pages', 'translatepress-multilingual' ),
	        'search_tooltip'             => esc_html__( 'Search original strings containing typed keywords while also matching selected filters', 'translatepress-multilingual' ),
	        'filter_tooltip'             => esc_html__( 'Filter strings according to selected translation status, filters and keywords and selected filters', 'translatepress-multilingual' ),
	        'clear_filter_tooltip'       => esc_html__( 'Removes selected filters', 'translatepress-multilingual' ),
	        'select_all_tooltip'         => esc_html__( 'See options for selecting all strings', 'translatepress-multilingual' ),
	        'sort_by_column'             => esc_html__( 'Click to sort strings by this column', 'translatepress-multilingual' ),
	        'filter_by_language_tooltip' => esc_html__( 'Language in which the translation status filter applies. Leave unselected for the translation status to apply to ANY language', 'translatepress-multilingual' ),
            'search_placeholder'         => esc_html__('Search', 'translatepress-multilingual'),
        );
        return apply_filters( 'trp_st_editor_strings', $st_editor_strings );
    }

    /**
     * @return mixed
     */
    public function string_types_config($needs_gettext = false) {
        $string_types_config = array(
            'gettext' =>
                array(
                    'type'                   => 'gettext',
                    'name'                   => esc_html__( 'Plugins and Theme String Translation', 'translatepress-multilingual' ),
                    'tab_name'               => esc_html__( 'Gettext', 'translatepress-multilingual' ),
                    'search_name'            => esc_html__( 'Search Gettext Strings', 'translatepress-multilingual' ),
                    'class_name_suffix'      => 'Gettext',
//				    'add_new'                => true,
                    'scan_gettext'           => true,
                    'plugin_path'            => TRP_PLUGIN_DIR,
                    'nonces'                 => $this->get_nonces_for_type( 'gettext' ),
                    'table_columns'          => array(
                        'id'         => esc_html__( 'ID', 'translatepress-multilingual' ),
                        'original'   => esc_html__( 'Original String', 'translatepress-multilingual' ),
                        'translated' => esc_html__( 'Translation', 'translatepress-multilingual' ),
                        'domain'     => esc_html__( 'Domain', 'translatepress-multilingual' ),
                    ),
                    'show_original_language' => true,
                    'category_based'         => false,
                    'filters'                => array(
                        'domain' => array_merge(
                            array( 'trp_default' => esc_html__( 'Filter by domain', 'translatepress-multilingual' ) ),
                            $needs_gettext ? $this->get_gettext_domains() : array()
                        ),
                        'type' => array(
                            'trp_default' => esc_html__( 'Filter by type', 'translatepress-multilingual' ),
                            'email'       => esc_html__( 'Email text', 'translatepress-multilingual' )
                        ),
                    )
                ),
            'emails' =>
                array(
                    'type'                   => 'gettext',
                    'name'                   => esc_html__( 'Emails String Translation', 'translatepress-multilingual' ),
                    'tab_name'               => esc_html__( 'Emails', 'translatepress-multilingual' ),
                    'search_name'            => esc_html__( 'Search Email Strings', 'translatepress-multilingual' ),
                    'class_name_suffix'      => 'Gettext',
                    //				    'add_new'                => true,
                    'scan_gettext'           => true,
                    'plugin_path'            => TRP_PLUGIN_DIR,
                    'nonces'                 => $this->get_nonces_for_type( 'gettext' ),
                    'table_columns'          => array(
                        'id'         => esc_html__( 'ID', 'translatepress-multilingual' ),
                        'original'   => esc_html__( 'Original String', 'translatepress-multilingual' ),
                        'translated' => esc_html__( 'Translation', 'translatepress-multilingual' ),
                        'domain'     => esc_html__( 'Domain', 'translatepress-multilingual' ),
                    ),
                    'show_original_language' => true,
                    'category_based'         => false,
                    'filters'                => array(
                        'domain' => array_merge(
                            array( 'trp_default' => esc_html__( 'Filter by domain', 'translatepress-multilingual' ) ),
                            $needs_gettext ? $this->get_gettext_domains() : array()
                        ),
                    )
                ),
            'regular' =>
                array(
                    'type'                   => 'regular',
                    'name'                   => esc_html__( 'User Inputted String Translation', 'translatepress-multilingual' ),
                    'tab_name'               => esc_html__( 'Regular', 'translatepress-multilingual' ),
                    'search_name'            => esc_html__( 'Search Regular Strings', 'translatepress-multilingual' ),
                    'class_name_suffix'      => 'Regular',
                    //				    'add_new'                => true,
                    'plugin_path'            => TRP_PLUGIN_DIR,
                    'nonces'                 => $this->get_nonces_for_type( 'regular' ),
                    'table_columns'          => array(
                        'id'         => esc_html__( 'ID', 'translatepress-multilingual' ),
                        'original'   => esc_html__( 'Original String', 'translatepress-multilingual' ),
                        'translated' => esc_html__( 'Translation', 'translatepress-multilingual' )
                    ),
                    'show_original_language' => false,
                    'category_based'         => false,
                    'filters'                => array(
                        'translation-block-type' => array(
                            'trp_default'       => esc_html__( 'Filter by Translation Block', 'translatepress-multilingual' ),
                            'individual_string' => 'Individual string',
                            'translation_block' => 'Translation Block'
                        )
                    )
                )
        );


        if ( !apply_filters('trp_show_regular_strings_string_translation', false ) ){
            unset($string_types_config['regular']);
        }
        $seo_pack_active = class_exists( 'TRP_IN_Seo_Pack');
        if( !$seo_pack_active ){
            $upsale_slugs_string_type = array(
                'slugs' => array(
                    'type'              => 'upsale-slugs',
                    'name'              => __( 'URL Slugs Translation', 'translatepress-multilingual' ),
                    'tab_name'          => __( 'Slugs', 'translatepress-multilingual' ),
                    'class_name_suffix' => 'Regular',
                    'plugin_path'       => TRP_PLUGIN_DIR,
                    'category_based'    => false,
                    'nonces'                 => $this->get_nonces_for_type( 'regular' ),
                )
            );
            $string_types_config = $upsale_slugs_string_type + $string_types_config;
        }

        return apply_filters( 'trp_st_string_types_config', $string_types_config, $this );
    }

    public function get_nonces_for_type( $type ) {
        $nonces = array(
            'get_strings'  => wp_create_nonce( 'string_translation_get_strings_' . $type ),
            'get_missing_strings'  => wp_create_nonce( 'string_translation_get_missing_strings_' . $type ),
            'get_strings_by_original_id'  => wp_create_nonce( 'string_translation_get_strings_by_original_ids_' . $type ),
            'save_strings' => wp_create_nonce( 'string_translation_save_strings_' . $type )
        );
        return apply_filters( 'trp_string_translation_nonces', $nonces, $type );
    }

    public function get_configuration_options() {
        $config = array(
            'items_per_page'      => 20,
            'see_more_max_length' => 5000
        );
        return apply_filters( 'trp_string_translation_config', $config );
    }

    public function register_string_types( $registered_string_types ) {
        foreach ( $this->string_types as $string_type => $value ) {
            if ( !in_array( $string_type, $registered_string_types ) ) {
                $registered_string_types[] = $string_type;
            }
        }

        return $registered_string_types;
    }

    /*
     * hooked to trp_editor_nonces
     */
    public function add_nonces_for_saving_translation( $nonces ) {
        foreach ( $this->string_types as $string_type => $string_config ) {
            if ( !isset( $nonces[ 'savetranslationsnonce' . $string_type ] ) ) {
                $nonces[ 'savetranslationsnonce' . $string_type ] = $string_config['nonces']['save_strings'];
            }
        }
        return $nonces;
    }

}