<?php

// Exit if accessed directly
if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Translation_Manager
 *
 * Handles Front-end Translation Editor, including Ajax requests.
 */
class TRP_Translation_Manager {
    protected $settings;
    protected $url_converter;

    /**
     * TRP_Translation_Manager constructor.
     *
     * @param array $settings Settings option.
     */
    public function __construct( $settings ) {
        $this->settings = $settings;
    }

    /**
     * Function that determines if an ajax request came from the frontend
     *
     * Moved to TRP_Gettext_Manager. Keeping it in case there is third pary code that uses this
     * @return bool
     */
    static function is_ajax_on_frontend() {
        return TRP_Gettext_Manager::is_ajax_on_frontend();
    }

    /**
     * function that strips the gettext tags from a string
     *
     * Moved to TRP_Gettext_Manager. Keeping it in case third party uses it.
     * @param $string
     * @return mixed
     */
    static function strip_gettext_tags( $string ) {
        return TRP_Gettext_Manager::strip_gettext_tags($string);
    }

    /**
     * Returns boolean whether current page is part of the Translation Editor.
     *
     * @param string $mode 'true' | 'preview'
     * @return bool                 Whether current page is part of the Translation Editor.
     */
    protected function conditions_met( $mode = 'true' ) {
        if ( isset( $_REQUEST['trp-edit-translation'] ) && sanitize_text_field( $_REQUEST['trp-edit-translation'] ) == $mode ) {
            if ( current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) && !is_admin() ) {
                return true;
            } elseif ( sanitize_text_field( $_REQUEST['trp-edit-translation'] ) == "preview" ) {
                return true;
            } else {
                wp_die(
                    '<h1>' . esc_html__( 'Cheatin&#8217; uh?' ) . '</h1>' . //phpcs:ignore  WordPress.WP.I18n.MissingArgDomain
                    '<p>' . esc_html__( 'Sorry, you are not allowed to access this page.' ) . '</p>', //phpcs:ignore  WordPress.WP.I18n.MissingArgDomain
                    403
                );
            }
        }
        return false;
    }

    /**
     * Start Translation Editor.
     *
     * Hooked to template_include.
     *
     * @param string $page_template Current page template.
     * @return string                       Template for translation Editor.
     */
    public function translation_editor( $page_template ) {
        if ( !$this->conditions_met() ) {
            return $page_template;
        }

        return TRP_PLUGIN_DIR . 'partials/translation-manager.php';
    }

    public function get_merge_rules() {
        $localized_text = $this->string_groups();

        $merge_rules = array(
            'top_parents'           => array( 'p', 'div', 'li', 'ol', 'ul', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'h7', 'body', 'footer', 'article', 'main', 'iframe', 'section', 'figure', 'figcaption', 'blockquote', 'cite', 'tr', 'td', 'th', 'table', 'tbody', 'thead', 'tfoot', 'form', 'label' ),
            'self_object_type'      => array( 'translate-press' ),
            'incompatible_siblings' => array( '[data-trpgettextoriginal]', '[data-trp-node-group="' . $localized_text['dynamicstrings'] . '"]' )
        );

        return apply_filters( 'trp_merge_rules', $merge_rules );
    }

    public function localized_text() {
        $update_seo_add_on = ( class_exists( 'TRP_Seo_Pack' ) && !defined( 'TRP_SP_PLUGIN_VERSION' ) );

        return $this->string_groups() + array(
                // attribute names
                'src'         => esc_html__( 'Source', 'translatepress-multilingual' ),
                'srcset'      => esc_html__( 'Srcset', 'translatepress-multilingual' ),
                'alt'         => esc_html__( 'Alt attribute', 'translatepress-multilingual' ),
                'title'       => esc_html__( 'Title attribute', 'translatepress-multilingual' ),
                'href'        => esc_html__( 'Anchor link', 'translatepress-multilingual' ),
                'placeholder' => esc_html__( 'Placeholder attribute', 'translatepress-multilingual' ),
                'submit'      => esc_html__( 'Submit attribute', 'translatepress-multilingual' ),
                'text'        => esc_html__( 'Text', 'translatepress-multilingual' ),
                'poster'      => esc_html__( 'Video Poster', 'translatepress-multilingual' ),

                // plural form name variants
                'plural_form_text'           => esc_html__( 'plural form', 'translatepress-multilingual' ),
                'plural_form_one'            => esc_html__( 'one', 'translatepress-multilingual' ),
                'plural_form_few'            => esc_html__( 'few', 'translatepress-multilingual' ),
                'plural_form_many'           => esc_html__( 'many', 'translatepress-multilingual' ),
                'plural_form_other'          => esc_html__( 'other', 'translatepress-multilingual' ),

                'saved'                                      => esc_html__( 'Saved', 'translatepress-multilingual' ),
                'save_translation'                           => esc_html__( 'Save', 'translatepress-multilingual' ),
                'saving_translation'                         => esc_html__( 'Saving translation...', 'translatepress-multilingual' ),
                'unsaved_changes'                            => esc_html__( 'You have unsaved changes!', 'translatepress-multilingual' ),
                'discard'                                    => esc_html__( 'Discard changes', 'translatepress-multilingual' ),
                'discard_all'                                => esc_html__( 'Discard All', 'translatepress-multilingual' ),
                'strings_loading'                            => esc_attr__( 'Loading Strings...', 'translatepress-multilingual' ),
                'select_string'                              => esc_attr__( 'Select string to translate...', 'translatepress-multilingual' ),
                'close'                                      => esc_attr__( 'Close Editor', 'translatepress-multilingual' ),
                'from'                                       => esc_html__( 'From', 'translatepress-multilingual' ),
                'to'                                         => esc_html__( 'To', 'translatepress-multilingual' ),
                'add_media'                                  => esc_html__( 'Add Media', 'translatepress-multilingual' ),
                'other_lang'                                 => esc_html__( 'Other languages', 'translatepress-multilingual' ),
                'context'                                    => esc_html__( 'Context', 'translatepress-multilingual' ),
                'view_as'                                    => esc_html__( 'View Website As', 'translatepress-multilingual' ),
                'view_as_pro'                                => esc_html__( 'Available in our Pro Versions', 'translatepress-multilingual' ),

                //wp media upload
                'select_or_upload'                           => esc_html__( 'Select or Upload Media', 'translatepress-multilingual' ),
                'use_this_media'                             => esc_html__( 'Use this media', 'translatepress-multilingual' ),

                // title attributes
                'edit'                                       => esc_attr__( 'Translate', 'translatepress-multilingual' ),
                'merge'                                      => esc_attr__( 'Translate entire block element', 'translatepress-multilingual' ),
                'split'                                      => esc_attr__( 'Split block to translate strings individually', 'translatepress-multilingual' ),
                'save_title_attr'                            => esc_attr__( 'Save changes to translation. Shortcut: CTRL(⌘) + S', 'translatepress-multilingual' ),
                'next_title_attr'                            => esc_attr__( 'Navigate to next string in dropdown list. Shortcut: CTRL(⌘) + ALT + Right Arrow', 'translatepress-multilingual' ),
                'previous_title_attr'                        => esc_attr__( 'Navigate to previous string in dropdown list. Shortcut: CTRL(⌘) + ALT + Left Arrow', 'translatepress-multilingual' ),
                'discard_all_title_attr'                     => esc_attr__( 'Discard all changes. Shortcut: CTRL(⌘) + ALT + Z', 'translatepress-multilingual' ),
                'discard_individual_changes_title_attribute' => esc_attr__( 'Discard changes to this text box. To discard changes to all text boxes use shortcut: CTRL(⌘) + ALT + Z', 'translatepress-multilingual' ),
                'dismiss_tooltip_title_attribute'            => esc_attr__( 'Dismiss tooltip', 'translatepress-multilingual' ),
                'quick_intro_title_attribute'                => esc_attr__( 'Quick Intro', 'translatepress-multilingual' ),

                'split_confirmation'         => esc_js( __( 'Are you sure you want to split this phrase into smaller parts?', 'translatepress-multilingual' ) ),
                'translation_not_loaded_yet' => wp_kses( __( 'This string is not ready for translation yet. <br>Try again in a moment...', 'translatepress-multilingual' ), array( 'br' => array() ) ),

                'bor_update_notice'                 => esc_js( __( 'For this option to work, please update the Browse as other role add-on to the latest version.', 'translatepress-multilingual' ) ),
                'seo_update_notice'                 => ( $update_seo_add_on ) ? esc_js( __( 'To translate slugs, please update the SEO Pack add-on to the latest version.', 'translatepress-multilingual' ) ) : 'seo_pack_update_not_needed',

                //Notice when the user has not defined a secondary language
                'extra_lang_row1'                   => wp_kses( sprintf( __( 'You can add a new language from <a href="%s">Settings->TranslatePress</a>', 'translatepress-multilingual' ), esc_url( admin_url( 'options-general.php?page=translate-press' ) ) ), array( 'a' => [ 'href' => [] ] ) ),
                'extra_lang_row2'                   => wp_kses( __( 'However, you can still use TranslatePress to <strong style="background: #f5fb9d;">modify gettext strings</strong> available in your page.', 'translatepress-multilingual' ), array( 'strong' => [ 'style' => [] ] ) ),
                'extra_lang_row3'                   => esc_html__( 'Strings that are user-created cannot be modified, only those from themes and plugins.', 'translatepress-multilingual' ),
                //Pro version upselling
                'extra_upsell_title'                => esc_html__( 'Extra Translation Features', 'translatepress-multilingual' ),
                'extra_upsell_row1'                 => esc_html__( 'Support for 130+ Extra Languages', 'translatepress-multilingual' ),
                'extra_upsell_row2'                 => esc_html__( 'Access to TranslatePress AI', 'translatepress-multilingual' ),
                'extra_upsell_row3'                 => esc_html__( 'Translate SEO Title, Description, Slug', 'translatepress-multilingual' ),
                'extra_upsell_row4'                 => esc_html__( 'Publish only when translation is complete', 'translatepress-multilingual' ),
                'extra_upsell_row5'                 => esc_html__( 'Translate by Browsing as User Role', 'translatepress-multilingual' ),
                'extra_upsell_row6'                 => esc_html__( 'Different Menu Items for each Language', 'translatepress-multilingual' ),
                'extra_upsell_row7'                 => esc_html__( 'Automatic User Language Detection', 'translatepress-multilingual' ),
                'extra_upsell_button'               => wp_kses( sprintf( '<a class="button-primary" target="_blank" href="%s">%s</a>', esc_url( trp_add_affiliate_id_to_link( 'https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpeditor&utm_campaign=tpfree' ) ), __( 'Upgrade to PRO', 'translatepress-multilingual' ) ), array( 'a' => [ 'class' => [], 'target' => [], 'href' => [] ] ) ),
                // Black Friday
                'extra_upsell_bf_row1'              => esc_html__( 'Upgrade to PRO with our biggest discount of the year!', 'translatepress-multilingual' ),
                'extra_upsell_bf_row2'              => esc_html__( 'This Black Friday, get access to these features and more at a fraction of the costs:', 'translatepress-multilingual' ),
                'extra_upsell_bf_button'            => wp_kses( sprintf( '<a class="button-primary" target="_blank" href="%s">%s</a>', esc_url( trp_add_affiliate_id_to_link( 'https://translatepress.com/black-friday/?utm_source=tpeditor&utm_medium=clientsite&utm_campaign=BF-2024' ) ), __( 'Upgrade to PRO', 'translatepress-multilingual' ) ), array( 'a' => [ 'class' => [], 'target' => [], 'href' => [] ] ) ),
                // Translation Memory
                'translation_memory_no_suggestions' => esc_html__( 'No available suggestions', 'translatepress-multilingual' ),
                'translation_memory_suggestions'    => esc_html__( 'Suggestions from translation memory', 'translatepress-multilingual' ),
                'translation_memory_click_to_copy'  => esc_html__( 'Click to Copy', 'translatepress-multilingual' ),
                //human or machine translation tooltips
                'human_translation'                 => esc_html__('Human Translation', 'translatepress-multilingual'),
                'machine_translation'               => esc_html__('Machine Translation', 'translatepress-multilingual'),
                'percentage_bar'                    => array(
                    'tooltip_text_default' => esc_html__( 'Text on this page is %s% translated into all languages.', 'translatepress-multilingual'),
                    'tooltip_text_general' => esc_html__( '%1$s% of text on this page is translated into %2$s.', 'translatepress-multilingual'),
                    'minibar_text'         => esc_html__('This page is %1$s% translated into %2$s.', 'translatepress-multilingual')
                ),
                'multiple_types_alert'              => esc_html__( "The slug that you are trying to edit is present in other slug types:%s%.\nEditing it will replace each occurrence, regardless of the current type.", 'translatepress-multilingual')
            );
    }

    public function get_help_panel_content() {
        $edit_icon = '<svg class="trp-edit-icon-inline" xmlns="http://www.w3.org/2000/svg" visibility="hidden" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M20.1 5.1L16.9 2 6.2 12.7l-1.3 4.4 4.5-1.3L20.1 5.1zM4 20.8h8v-1.5H4v1.5z"></path></svg>';
        return apply_filters( 'trp_help_panel_content', array(
            array(
                'title'   => esc_html__( 'Quick Intro', 'translatepress-multilingual' ),
                'content' => wp_kses(sprintf( __( 'Hover any text on the page, click %s,<br> then modify the translation in the sidebar.', 'translatepress-multilingual' ), $edit_icon),
                    array( 'svg' => array( 'class' => array(),'xmlns' => array(),'visibility'=>array(), 'viewbox' => array(), 'aria-hidden' => array(), 'focusable' => array() ), 'path' => array('d'=>array() ), "br" => array() ) ),
                'event'   => 'trp_hover_text_help_panel'
            ),
            array(
                'title'   => esc_html__( 'Quick Intro', 'translatepress-multilingual' ),
                'content' => wp_kses( __( 'Don\'t forget to Save Translation. Use keyboard shortcut CTRL(⌘) + S', 'translatepress-multilingual' ), array() ),
                'event'   => 'trp_save_translation_help_panel'
            ),
            array(
                'title'   => esc_html__( 'Quick Intro', 'translatepress-multilingual' ),
                'content' => wp_kses( __( 'Switch language to see the translation changes directly on the page.', 'translatepress-multilingual' ), array() ),
                'event'   => 'trp_switch_language_help_panel'
            ),
            array(
                'title'   => esc_html__( 'Quick Intro', 'translatepress-multilingual' ),
                'content' => wp_kses( __( 'Search for any text in this page in the dropdown.', 'translatepress-multilingual' ), array() ),
                'event'   => 'trp_search_string_help_panel'
            )
        ) );
    }

    public function get_license_notice_content(){
        $license_notice_content = false; // false will hide the license notice panel

        // paid version plugin (business/developer/personal) is active
        $free_version = !class_exists( 'TRP_Handle_Included_Addons' );

        if ( !$free_version ){
            $license_status = trp_get_license_status();
            if ( $license_status != 'valid' && $license_status != 'free-version' ) {
                $translatepress_product = ( defined( 'TRANSLATE_PRESS' ) ) ? TRANSLATE_PRESS : "TranslatePress";
                switch ( $license_status ) {
                    case 'expired':
                        {
                            $status_text  = wp_kses( sprintf( __( 'Your %s license has <span class="trp-license-status-emphasized">expired</span>.', 'translatepress-multilingual' ), '<strong>' . $translatepress_product . '</strong>' ), array( 'strong' => array(),'span' => array( 'class' => array() ) ) );

                            if( trp_bf_show_promotion() ){
                                $instructions = esc_html__( '<strong>This Black Friday, renew your license at a special price</strong> to continue receiving access to product downloads, automatic updates, and support.', 'translatepress-multilingual' );
                                $button       = esc_html__( 'Get Deal', 'translatepress-multilingual' );
                                $link         = 'https://translatepress.com/account/?utm_source=tpeditor&utm_medium=clientsite&utm_campaign=BF-2024';
                            } else {
                                $instructions = esc_html__( 'Please renew your license to continue receiving access to product downloads, automatic updates and support.', 'translatepress-multilingual' );
                                $button       = esc_html__( 'Renew Now', 'translatepress-multilingual' );
                                $link         = 'https://translatepress.com/account/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpeditor&utm_campaign=TP-Renewal';
                            }
                            
                            break;
                        }
                    case 'revoked':
                        {
                            $status_text  = wp_kses( sprintf( __( 'Your %s license was <span class="trp-license-status-emphasized">refunded</span>.', 'translatepress-multilingual' ), '<strong>' . $translatepress_product . '</strong>' ), array( 'strong' => array(),'span' => array( 'class' => array() ) ) );
                            $instructions = esc_html__( 'Please purchase a new license to continue receiving access to product downloads, automatic updates and support.', 'translatepress-multilingual' );
                            $button       = esc_html__( 'Purchase a new license', 'translatepress-multilingual' );
                            $link = 'https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpeditor&utm_campaign=TP-Refund';
                            break;
                        }
                    //  case 'missing' :
                    //  case 'invalid' :
                    //  case 'site_inactive' :
                    //  case 'item_name_mismatch' :
                    //  case 'no_activations_left':
                    default:
                        {
                            $status_text  = wp_kses( sprintf( __( 'Your %s license is <span class="trp-license-status-emphasized">empty or incorrect</span>.', 'translatepress-multilingual' ), '<strong>' . $translatepress_product . '</strong>' ), array( 'strong' => array(),'span' => array( 'class' => array() ) ) );
                            $instructions = esc_html__( 'Please enter a valid license to continue receiving access to product downloads, automatic updates and support.', 'translatepress-multilingual' );
                            $button       = esc_html__( 'Enter a valid license', 'translatepress-multilingual' );
                            $link         = admin_url( 'admin.php?page=trp_license_key' );
                            break;
                        }
                }

                $button_class = 'trp-license-notice-button';

                if( trp_bf_show_promotion() )
                    $button_class = 'trp-license-notice-button-red';

                $license_notice_content = '<p>' . $status_text . '</p><p>' . $instructions . '</p><p><a href="' . esc_url($link) . '" class="button-primary '. esc_attr( $button_class ) .'" target="_blank">' . $button . '</a></p>';
            }
        }

        return $license_notice_content;
    }

    public function get_default_editor_user_meta() {
        return apply_filters( 'trp_default_editor_user_meta', array(
            'helpPanelOpened'          => false,
            'dismissTooltipSave'       => false,
            'dismissTooltipNext'       => false,
            'dismissTooltipPrevious'   => false,
            'dismissTooltipDismissAll' => false,
            'dismissTooltipHumanorMachineTranslation' => false,
            'dismissPreviousTabTooltip' => false,
        ) );
    }

    public function get_editor_user_meta() {
        $user_meta = get_user_meta( get_current_user_id(), 'trp_editor_user_meta', true );
        $user_meta = wp_parse_args( $user_meta, $this->get_default_editor_user_meta() );
        return apply_filters( 'trp_editor_user_meta', $user_meta );
    }

    public function save_editor_user_meta() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
            check_ajax_referer( 'trp_editor_user_meta', 'security' );
            if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_save_editor_user_meta' && !empty( $_POST['user_meta'] ) ) {
                $submitted_user_meta = json_decode( stripslashes( $_POST['user_meta'] ), true ); /* phpcs:ignore */ /* sanitized bellow */
                $existing_user_meta = $this->get_editor_user_meta();
                foreach ( $existing_user_meta as $key => $existing ) {
                    if ( isset( $submitted_user_meta[ $key ] ) ) {
                        $existing_user_meta[ $key ] = (bool)$submitted_user_meta[ $key ];
                    }
                }
                update_user_meta( get_current_user_id(), 'trp_editor_user_meta', $existing_user_meta );
            }
        }
        echo trp_safe_json_encode( array() );//phpcs:ignore
        die();
    }

    public function string_groups() {
        $string_groups = array(
            'slugs'           => esc_html__( 'Slugs', 'translatepress-multilingual' ),
            'metainformation' => esc_html__( 'Meta Information', 'translatepress-multilingual' ),
            'stringlist'      => esc_html__( 'String List', 'translatepress-multilingual' ),
            'gettextstrings'  => esc_html__( 'Gettext Strings', 'translatepress-multilingual' ),
            'images'          => esc_html__( 'Images', 'translatepress-multilingual' ),
            'videos'          => esc_html__( 'Videos', 'translatepress-multilingual' ),
            'audios'          => esc_html__( 'Audios', 'translatepress-multilingual' ),
            'dynamicstrings'  => esc_html__( 'Dynamically Added Strings', 'translatepress-multilingual' ),
        );
        return apply_filters( 'trp_string_groups', $string_groups );
    }

    public function editor_nonces() {
        $nonces = array(
            'gettranslationsnonceregular'   => wp_create_nonce( 'get_translations' ),
            'savetranslationsnonceregular'  => wp_create_nonce( 'save_translations' ),
            'gettranslationsnoncegettext'   => wp_create_nonce( 'gettext_get_translations' ),
            'savetranslationsnoncegettext'  => wp_create_nonce( 'gettext_save_translations' ),
            'gettranslationsnoncepostslug'  => wp_create_nonce( 'postslug_get_translations' ),
            'savetranslationsnoncepostslug' => wp_create_nonce( 'postslug_save_translations' ),
            'splittbnonce'                  => wp_create_nonce( 'split_translation_block' ),
            'mergetbnonce'                  => wp_create_nonce( 'merge_translation_block' ),
            'logged_out'                    => wp_create_nonce( 'trp_view_aslogged_out' . get_current_user_id() ),
            'getsimilarstring'              => wp_create_nonce( 'getsimilarstring' ),
            'trp_editor_user_meta'          => wp_create_nonce( 'trp_editor_user_meta' ),
            'scangettextnonce'              => wp_create_nonce( 'scangettextnonce' ),
            'get_missing_strings'           => wp_create_nonce( 'string_translation_get_missing_strings_gettext' ),
            'get_strings_by_original_id'    => wp_create_nonce( 'string_translation_get_strings_by_original_ids_gettext' )
        );

        return apply_filters( 'trp_editor_nonces', $nonces );
    }

    /**
     * Navigation tabs for Website editing, Url Slugs, String Translation
     *
     * @return array
     */
    public function get_editors_navigation() {
        return apply_filters( 'trp_editors_navigation', array(
            'show' => true,
            'tabs' => array(
                array(
                    'handle'  => 'visualeditor',
                    'label'   => __( 'Translation Editor', 'translatepress-multilingual' ),
                    'path'    => add_query_arg( 'trp-edit-translation', 'true', home_url() ),
                    'tooltip' => esc_html__('Edit translations by visually selecting them on each site page', 'translatepress-multilingual')
                ),
                array(
                    'handle'  => 'stringtranslation',
                    'label'   => __( 'String Translation', 'translatepress-multilingual' ),
                    'path'    => add_query_arg( 'trp-string-translation', 'true', home_url() ) . '#/slugs/',
                    'tooltip' => esc_html__('Edit url slug translations, plugins and theme translation (emails, forms etc.)', 'translatepress-multilingual')
                )
            )
        ) );
    }

    /**
     * Enqueue scripts and styles for translation Editor parent window.
     *
     * hooked to trp_translation_manager_footer
     */
    public function enqueue_scripts_and_styles() {
        wp_enqueue_style( 'trp-editor-style', TRP_PLUGIN_URL . 'assets/css/trp-editor.css', array( 'dashicons', 'buttons' ), TRP_PLUGIN_VERSION );
        wp_enqueue_script( 'trp-editor', TRP_PLUGIN_URL . 'assets/js/trp-editor.js', array(), TRP_PLUGIN_VERSION );

        wp_localize_script( 'trp-editor', 'trp_editor_data', $this->get_trp_editor_data() );


        // Show upload media dialog in default language
        switch_to_locale( $this->settings['default-language'] );
        // Necessary for add media button
        wp_enqueue_media();

        // Necessary for add media button
        wp_print_media_templates();
        restore_current_locale();

        // Necessary for translate-dom-changes to have a nonce as the same user as the Editor.
        // The Preview iframe (which loads translate-dom-changes script) can load as logged out which sets an different nonce
        $nonces = $this->editor_nonces();
        wp_add_inline_script( 'trp-editor', 'var trp_dynamic_nonce = "' . $nonces['gettranslationsnonceregular'] . '";' );

        $scripts_to_print = apply_filters( 'trp-scripts-for-editor', array( 'jquery', 'jquery-ui-core', 'jquery-effects-core', 'jquery-ui-resizable', 'trp-editor' ) );
        $styles_to_print  = apply_filters( 'trp-styles-for-editor', array( 'dashicons', 'trp-editor-style', 'media-views', 'imgareaselect', 'buttons' /*'wp-admin', 'common', 'site-icon', 'buttons'*/ ) );
        wp_print_scripts( $scripts_to_print );
        wp_print_styles( $styles_to_print );

        // Necessary for add media button
        print_footer_scripts();

    }

    /**
     * Localize all the data needed by the translation editor
     *
     * @return array
     */
    public function get_trp_editor_data() {
        global $TRP_LANGUAGE;
        $trp                = TRP_Translate_Press::get_trp_instance();
        $trp_languages      = $trp->get_component( 'languages' );
        $translation_render = $trp->get_component( 'translation_render' );
        $url_converter      = $trp->get_component( 'url_converter' );

        $language_names = $trp_languages->get_language_names( $this->settings['translation-languages'] );

        // move the current language to the beginning of the array
        $translation_languages = $this->settings['translation-languages'];
        if ( $TRP_LANGUAGE != $this->settings['default-language'] ) {
            $current_language_key = array_search( $TRP_LANGUAGE, $this->settings['translation-languages'] );
            unset( $translation_languages[ $current_language_key ] );
            $translation_languages = array_merge( array( $TRP_LANGUAGE ), array_values( $translation_languages ) );
        }
        $default_language_key = array_search( $this->settings['default-language'], $translation_languages );
        unset( $translation_languages[ $default_language_key ] );
        $ordered_secondary_languages = array_values( $translation_languages );

        $current_language_published = ( in_array( $TRP_LANGUAGE, $this->settings['publish-languages'] ) );
        $current_url                = $url_converter->cur_page_url();

        $selectors       = $translation_render->get_accessors_array( '-' ); // suffix selectors such as array( '-alt', '-src', '-title', '-content', '-value', '-placeholder', '-href', '-outertext', '-innertext' )
        $selectors[]     = '';                                              // empty string suffix added for using just the base attribute data-trp-translate-id  (instead of data-trp-translate-id-alt)
        $data_attributes = $translation_render->get_base_attribute_selectors();

        //setup view_as roles
        $view_as_roles = array(
            __( 'Current User', 'translatepress-multilingual' ) => 'current_user',
            __( 'Logged Out', 'translatepress-multilingual' )   => 'logged_out'
        );
        $all_roles     = wp_roles()->roles;

        if ( !empty( $all_roles ) ) {
            foreach ( $all_roles as $role )
                $view_as_roles[ $role['name'] ] = '';
        }

        $view_as_roles = apply_filters( 'trp_view_as_values', $view_as_roles );
        $string_groups = apply_filters( 'trp_string_group_order', array_values( $this->string_groups() ) );

        $flags_path      = array();
        $flags_file_name = array();
        foreach ( $this->settings['translation-languages'] as $language_code ) {
            $default_path                      = TRP_PLUGIN_URL . 'assets/images/flags/';
            $flags_path[ $language_code ]      = apply_filters( 'trp_flags_path', $default_path, $language_code );
            $default_flag_file_name            = $language_code . '.png';
            $flags_file_name[ $language_code ] = apply_filters( 'trp_flag_file_name', $default_flag_file_name, $language_code );
        }

        $editors_navigation = $this->get_editors_navigation();
        $string_types       = array( 'regular', 'gettext', 'postslug' );


        $trp_editor_data = array(
            'trp_localized_strings'       => $this->localized_text(),
            'trp_settings'                => $this->settings,
            'language_names'              => $language_names,
            'ordered_secondary_languages' => $ordered_secondary_languages,
            'current_language'            => $TRP_LANGUAGE,
            'on_screen_language'          => ( isset( $ordered_secondary_languages[0] ) ) ? $ordered_secondary_languages[0] : '',
            'view_as_roles'               => $view_as_roles,
            'url_to_load'                 => add_query_arg( 'trp-edit-translation', 'preview', $current_url ),
            'string_selectors'            => $selectors,
            'data_attributes'             => $data_attributes,
            'editor_nonces'               => $this->editor_nonces(),
            'ajax_url'                    => apply_filters( 'trp_wp_ajax_url', admin_url( 'admin-ajax.php' ) ),
            'string_types'                => apply_filters( 'trp_string_types', $string_types ),
            'string_group_order'          => $string_groups,
            'merge_rules'                 => $this->get_merge_rules(),
            'paid_version'                => trp_is_paid_version() ? 'true' : 'false',
            'black_friday'                => trp_bf_show_promotion() ? 'true' : 'false',
            'trp_license_status'          => trp_get_license_status(),
            'flags_path'                  => $flags_path,
            'flags_file_name'             => $flags_file_name,
            'editors_navigation'          => $editors_navigation,
            'help_panel_content'          => $this->get_help_panel_content(),
            'user_meta'                   => $this->get_editor_user_meta(),
            'upgraded_gettext'            => ! ( ( get_option( 'trp_updated_database_gettext_original_id_update', 'yes' ) == 'no' ) ),
            'notice_upgrade_gettext'      => $this->display_notice_to_upgrade_gettext_in_editor(''),
            'notice_upgrade_slugs'        => $this->display_notice_to_upgrade_slugs_in_editor(''),
            'upsale_slugs'                => $this->is_seo_pack_active(),
            'upsale_slugs_text'           => $this->upsale_slugs_text(),
            'license_notice_content'      => $this->get_license_notice_content()
        );

        return apply_filters( 'trp_editor_data', $trp_editor_data );
    }

    /**
     * Enqueue scripts and styles for translation Editor preview window.
     */
    public function enqueue_preview_scripts_and_styles() {
        if ( $this->conditions_met( 'preview' ) ) {
            wp_enqueue_script( 'trp-translation-manager-preview-script', TRP_PLUGIN_URL . 'assets/js/trp-iframe-preview-script.js', array( 'jquery' ), TRP_PLUGIN_VERSION );
            wp_enqueue_style( 'trp-preview-iframe-style', TRP_PLUGIN_URL . 'assets/css/trp-preview-iframe-style.css', array( 'dashicons' ), TRP_PLUGIN_VERSION );
        }
    }

    /**
     * Display button to enter translation Editor in admin bar
     *
     * Hooked to admin_bar_menu.
     *
     * @param $wp_admin_bar
     */
    public function add_shortcut_to_translation_editor( $wp_admin_bar ) {
        if ( !current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
            return;
        }

        if ( is_admin() ) {
            $url = add_query_arg( 'trp-edit-translation', 'true', trailingslashit( home_url() ) );

            $title      = __( 'Translate Site', 'translatepress-multilingual' );
            $url_target = '_blank';
        } else {

            if ( !$this->url_converter ) {
                $trp                 = TRP_Translate_Press::get_trp_instance();
                $this->url_converter = $trp->get_component( 'url_converter' );
            }

            $url = $this->url_converter->cur_page_url();

            $url = apply_filters( 'trp_edit_translation_url', add_query_arg( 'trp-edit-translation', 'true', $url ) );

            $title      = __( 'Translate Page', 'translatepress-multilingual' );
            $url_target = '';
        }

        $wp_admin_bar->add_node(
            array(
                'id'    => 'trp_edit_translation',
                'title' => '<span class="ab-icon"></span><span class="ab-label">' . $title . '</span>',
                'href'  => $url,
                'meta'  => array(
                    'class'  => 'trp-edit-translation',
                    'target' => $url_target
                )
            )
        );

        $wp_admin_bar->add_node(
            array(
                'id'     => 'trp_settings_page',
                'title'  => __( 'Settings', 'translatepress-multilingual' ),
                'href'   => admin_url( 'options-general.php?page=translate-press' ),
                'parent' => 'trp_edit_translation',
                'meta'   => array(
                    'class' => 'trp-settings-page'
                )
            )
        );

    }

    /**
     * adds shortcut to trp editor in gutenberg editor
     */

    function trp_add_shortcut_to_trp_editor_gutenberg(){
        wp_enqueue_script( 'custom-link-in-toolbar', TRP_PLUGIN_URL. '/assets/js/trp-gutenberg-editor-shortcut.js', array("jquery"), TRP_PLUGIN_VERSION, true );
        wp_localize_script( 'custom-link-in-toolbar', 'trp_localized', array( 'dont_adjust_width' => apply_filters( 'trp_dont_adjust_width_of_ls_in_gutenberg', false ) ) );
        $trp           = TRP_Translate_Press::get_trp_instance();
        $url_converter = $trp->get_component('url_converter');

        //$settings = $trp->get_component('settings');

        global $post;
        global $TRP_LANGUAGE;

        $url_translation_editor = array();

        add_filter('trp_add_language_to_home_url_check_for_admin', '__return_false');

        if ($post) {
            $trp_permalink_post = $url_converter->get_url_for_language( $TRP_LANGUAGE, get_permalink( $post->ID ) );
            if ( $post->post_status !== "publish" ) {
                $trp_permalink_post = $url_converter->get_url_for_language( $TRP_LANGUAGE, get_preview_post_link( $post->ID ) );
            }
        }else{
            $trp_permalink_post = $url_converter->get_url_for_language( $TRP_LANGUAGE, home_url() );
        }

        $url_translation_editor = apply_filters('trp_edit_translation_url', add_query_arg('trp-edit-translation', 'true', $trp_permalink_post));

        $title = esc_attr__('Opens post in the translation editor. Post must be saved as draft or published beforehand.', 'translatepress-multilingual');

        $trp_editor_button[0] =  "<a id='trp-link-id' class='components-button' href='" . esc_url($url_translation_editor) ."'  title='"  . $title ."' ><button class='button-primary' style='height: 33px'>" . esc_html__('Translate Page', 'translatepress-multilingual') ."</button></a>";

        wp_localize_script('custom-link-in-toolbar', 'trp_url_tp_editor', $trp_editor_button);

        remove_filter('trp_add_language_to_home_url_check_for_admin', '__return_false');
    }

    /**
     * Add the glyph icon for Translate Site button in admin bar
     *
     * hooked to admin_head action
     */
    public function add_styling_to_admin_bar_button() {
        echo "<style type='text/css'> #wpadminbar #wp-admin-bar-trp_edit_translation .ab-icon:before {    content: '\\f326';    top: 3px;}
		#wpadminbar #wp-admin-bar-trp_edit_translation > .ab-item {
			text-indent: 0;
		}

		#wpadminbar li#wp-admin-bar-trp_edit_translation {
			display: block;
		}</style>";
    }


    /**
     * Function to hide admin bar when in editor preview mode.
     *
     * Hooked to show_admin_bar.
     *
     * @param bool $show_admin_bar TRUE | FALSE
     * @return bool
     */
    public function hide_admin_bar_when_in_editor( $show_admin_bar ) {

        if ( $this->conditions_met( 'preview' ) ) {
            return false;
        }

        return $show_admin_bar;

    }

	/**
	 * Function that determines if a request is a rest api request based on the URL.
	 * @return bool
	 */
	static function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			// Probably a CLI request
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) !== false; /* phpcs:ignore */

		return apply_filters( 'trp_is_rest_api_request', $is_rest_api_request );
	}

    /**
     * Filter sanitize_title() to use our own remove_accents() function so it's based on the default language, not current locale.
     *
     * Also removes trp gettext tags before running the filter because it strip # and ! and / making it impossible to strip the #trpst later
     *
     * @param string $title
     * @param string $raw_title
     * @param string $context
     * @return string
     * @since 1.3.1
     *
     */
    public function trp_sanitize_title( $title, $raw_title, $context ) {
        // remove trp_tags before sanitization, because otherwise some characters (#,!,/, spaces ) are stripped later, and it becomes impossible to strip trp-gettext later
        $raw_title = TRP_Gettext_Manager::strip_gettext_tags( $raw_title );

        if ( 'save' == $context )
            $title = trp_remove_accents( $raw_title );

        remove_filter( 'sanitize_title', array( $this, 'trp_sanitize_title' ), 1 );
        $title = apply_filters( 'sanitize_title', $title, $raw_title, $context );
        add_filter( 'sanitize_title', array( $this, 'trp_sanitize_title' ), 1, 3 );

        return $title;
    }

    /**
     * Add the current language as a class to the body
     * @param $classes
     * @return array
     */
    public function add_language_to_body_class( $classes ) {
        global $TRP_LANGUAGE;
        if ( !empty( $TRP_LANGUAGE ) ) {
            $classes[] = 'translatepress-' . $TRP_LANGUAGE;
        }
        return $classes;
    }

    /**
     * Function that switches the view of the user to other roles
     */
    public function trp_view_as_user() {
        if ( !is_admin() || TRP_Gettext_Manager::is_ajax_on_frontend() ) {
            if ( isset( $_REQUEST['trp-edit-translation'] ) && $_REQUEST['trp-edit-translation'] === 'preview' && isset( $_REQUEST['trp-view-as'] ) && isset( $_REQUEST['trp-view-as-nonce'] ) ) {

                if ( apply_filters( 'trp_allow_translator_role_to_view_page_as_other_roles', true ) ) {
                    $current_user_can_change_roles = current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) || current_user_can( 'manage_options' );
                } else {
                    $current_user_can_change_roles = current_user_can( 'manage_options' );
                }

                if ($current_user_can_change_roles) {
                    if (!wp_verify_nonce( sanitize_text_field($_REQUEST['trp-view-as-nonce'] ), 'trp_view_as' . sanitize_text_field($_REQUEST['trp-view-as']) . get_current_user_id())) {
                        wp_die(esc_html__('Security check', 'translatepress-multilingual'));
                    } else {
                        global $current_user;
                        $view_as = sanitize_text_field( $_REQUEST['trp-view-as'] );
                        if ( $view_as === 'current_user' ) {
                            return;
                        } elseif ( $view_as === 'logged_out' ) {
                            $current_user = new WP_User( 0, 'trp_logged_out' );
                        } else {
                            $current_user = apply_filters( 'trp_temporary_change_current_user_role', $current_user, $view_as );
                        }
                    }
                }
            }
        }
    }

    /**
     * Return true if the string contains characters which are not allowed in the query
     *
     * Only valid for utf8.
     * Function is an extract of strip_invalid_text() function from wp-includes/wp-db.php
     *
     * @param $string
     *
     * @return bool
     */
    public function has_bad_characters( $string ) {
        $regex = '/
					(
						(?: [\x00-\x7F]                  # single-byte sequences   0xxxxxxx
						|   [\xC2-\xDF][\x80-\xBF]       # double-byte sequences   110xxxxx 10xxxxxx
						|   \xE0[\xA0-\xBF][\x80-\xBF]   # triple-byte sequences   1110xxxx 10xxxxxx * 2
						|   [\xE1-\xEC][\x80-\xBF]{2}
						|   \xED[\x80-\x9F][\x80-\xBF]
						|   [\xEE-\xEF][\x80-\xBF]{2}';

        $regex .= '
						|    \xF0[\x90-\xBF][\x80-\xBF]{2} # four-byte sequences   11110xxx 10xxxxxx * 3
						|    [\xF1-\xF3][\x80-\xBF]{3}
						|    \xF4[\x80-\x8F][\x80-\xBF]{2}
					';


        $regex           .= '){1,40}                          # ...one or more times
					)
					| .                                  # anything else
					/x';
        $stripped_string = preg_replace( $regex, '$1', $string );

        if ( $stripped_string === $string ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Records a series of strings which may have encoding issues
     *
     * Does not alter dictionary.
     *
     * @param $dictionary
     * @param $prepared_query
     * @param $strings_array
     *
     * @return mixed
     */
    public function display_possible_db_errors( $dictionary, $prepared_query, $strings_array ) {
        global $trp_editor_notices;
        if ( trp_is_translation_editor( 'preview' ) && is_array( $dictionary ) && count( $dictionary ) === 0 ) {
            if ( $this->has_bad_characters( $prepared_query ) ) {
                $html = "<div class='trp-notice trp-notice-warning'><p class='trp-bad-encoded-strings'>" . __( '<strong>Warning:</strong> Some strings have possibly incorrectly encoded characters. This may result in breaking the queries, rendering the page untranslated in live mode. Consider revising the following strings or their method of outputting.', 'translatepress-multilingual' ) . "</p>";
                $html .= "<ul class='trp-bad-encoded-strings-list'>";
                foreach ( $strings_array as $string ) {
                    if ( $this->has_bad_characters( $string ) ) {
                        $html .= "<li>" . $string . "</li>";
                    }
                }
                $html .= "</ul></div>";

                $trp_editor_notices .= $html;
            }
        }

        // no modifications to the dictionary
        return $dictionary;
    }

	public function display_notice_to_upgrade_gettext_in_editor( $trp_editor_notices ) {
		if (  ( get_option( 'trp_updated_database_gettext_original_id_update', 'yes' ) == 'no' ) ){
			$url = add_query_arg( array(
				'page'                      => 'trp_update_database',
			), site_url('wp-admin/admin.php') );

			// maybe change notice color to blue #28B1FF
			$html = "<div class='trp-notice trp-notice-warning'>";
			$html .= '<p><strong>' . esc_html__( 'TranslatePress data update', 'translatepress-multilingual' ) . '</strong> &#8211; ' . esc_html__( 'We need to update your translations database to the latest version.', 'translatepress-multilingual' ) . '</p>';
			$html .= '<p>' . esc_html__( 'Updating will allow editing translations of localized text from plugins and theme. Existing translation will still work as expected.', 'translatepress-multilingual' ) . '</p>';

			$html .= '<p><a class="trp-button-primary" target="_blank" href="' . esc_url( $url ) . '" onclick="return confirm( \'' . __( 'IMPORTANT: It is strongly recommended to first backup the database!\nAre you sure you want to continue?', 'translatepress-multilingual' ) . '\');" class="button-primary">' . esc_html__( 'Run the updater', 'translatepress-multilingual' ) . '</a></p>';
			$html .= '</div>';

			$trp_editor_notices = $html;
		}

		return $trp_editor_notices;
	}

    public function display_notice_to_upgrade_slugs_in_editor( $trp_editor_notices ) {
        if (  ( get_option( 'trp_migrate_old_slug_to_new_parent_and_translate_slug_table_term_meta_284', 'not_set' ) == 'no' ) ){
            $url = add_query_arg( array(
                'page'                      => 'trp_update_database',
            ), site_url('wp-admin/admin.php') );

            $html = "<div class='trp-notice trp-notice-warning'>";
            $html .= '<p><strong>' . esc_html__( 'TranslatePress data update', 'translatepress-multilingual' ) . '</strong> &#8211; ' . esc_html__( 'We need to update your translations database to the latest version.', 'translatepress-multilingual' ) . '</p>';
            $html .= '<p>' . esc_html__( 'Updating will allow editing translations of slugs. Existing translation will still work as expected.', 'translatepress-multilingual' ) . '</p>';

            $html .= '<p><a class="trp-button-primary" target="_blank" href="' . esc_url( $url ) . '" onclick="return confirm( \'' . __( 'IMPORTANT: It is strongly recommended to first backup the database!\nAre you sure you want to continue?', 'translatepress-multilingual' ) . '\');" class="button-primary">' . esc_html__( 'Run the updater', 'translatepress-multilingual' ) . '</a></p>';
            $html .= '</div>';

            $trp_editor_notices = $html;
        }

        return $trp_editor_notices;
    }

    /**
     * Receives and returns the date format in which a date (eg publish date) is presented on the frontend
     * The format is saved in the advanced settings tab for each language except the default one
     *
     * @param $date_format
     *
     * @return mixed
     */
    public function filter_the_date( $date_format ) {
        global $TRP_LANGUAGE;

        if ( !empty( $TRP_LANGUAGE ) && $this->settings["default-language"] === $TRP_LANGUAGE ) {
            return $date_format;
        } else {
            if ( isset ( $this->settings["trp_advanced_settings"]["language_date_format"][ $TRP_LANGUAGE ] ) && !empty ( $this->settings["trp_advanced_settings"]["language_date_format"][ $TRP_LANGUAGE ] ) ) {
                return $this->settings["trp_advanced_settings"]["language_date_format"][ $TRP_LANGUAGE ];
            } else {
                return $date_format;
            }
        }
    }

    /**
     * Prevent indexing edit translation preview pages.
     *
     * Hooked to trp_head, wp_head
     *
     */
    public function output_noindex_tag()
    {
        if( $this->conditions_met( 'true' ) || $this->conditions_met( 'preview' ) ){
            echo '<meta name="robots" content="noindex, nofollow">';
        }
    }

	public function upsale_slugs_text(){
		// Check if SEO Pack is inactive
		if ($this->is_seo_pack_active() === false) {
			// Check if Pro version (Personal, Business or Developer) is active
			if (trp_is_paid_version()) {
				// Display activation message instead of upsale for Pro users
				$html = '<div class="trp-text-and-image-upsale-slugs">';
				$html .= '<div class="trp-text-upsale-slugs">';
				$html .= '<p>';
				$html .= esc_html__('Please activate the SEO Addon from <br/>WordPress -> Settings -> TranslatePress -> Addons section', 'translatepress-multilingual' );
				$html .= '</p>';
				$html .= '<a target="_blank" href="' . esc_url(admin_url('admin.php?page=trp_addons_page')) . '" class="trp-learn-more-upsale button-primary">';
				$html .= esc_html__('Go to Addons', 'translatepress-multilingual' );
				$html .= '</a>';
				$html .= '</div>';
				$html .= '</div>';
				
				return $html;
			}
		}
	
		// Default upsale text for free version
		$upsale_url = 'https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpstringeditor&utm_campaign=tpfree';

		$html = '<div class="trp-text-and-image-upsale-slugs">';
		$html .= '<div class="trp-text-upsale-slugs">';
		$html .= '<p>';
		$html .= esc_html__('The SEO Pack add-on allows translation of all the URL slugs:', 'translatepress-multilingual' );
		$html .= '<ul class="trp-url-slugs-list">';
		$html .= '<li>';
		$html .= esc_html__('Taxonomy slugs', 'translatepress-multilingual' );
		$html .= '</li>';
		$html .= '<li>';
		$html .= esc_html__('Term slugs', 'translatepress-multilingual' );
		$html .= '</li>';
		$html .= '<li>';
		$html .= esc_html__('Post slugs (this includes pages and custom post types)', 'translatepress-multilingual' );
		$html .= '</li>';
		$html .= '<li>';
		$html .= esc_html__('Post type base slugs', 'translatepress-multilingual' );
		$html .= '</li>';
		$html .= '<li>';
		$html .= esc_html__('WooCommerce slugs', 'translatepress-multilingual' );
		$html .= '</li>';
		$html .= '</ul>';
		$html .= '</p>';
		$html .= '<p>';
		$html .= esc_html__('The SEO Pack add-on is available with ALL premium versions of the plugin.', 'translatepress-multilingual' );
		$html .= '</p>';
		$html .= '<a target="_blank" href="' . esc_url($upsale_url) . '" class="trp-learn-more-upsale button-primary">';
		$html .= esc_html__('Upgrade to Pro', 'translatepress-multilingual' );
		$html .= '</a>';
		$html .= '</div>';
		$html .= '<div class="trp-image-upsale-slugs">';
		$html .= '<div class="trp-image-container">';
		$html .= '<img src="' . esc_url(TRP_PLUGIN_URL.'assets/images/slug-upsale-new-editor-new.png') . '" class="trp-image-zoom" alt="SEO Pack Add-on">';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public function is_seo_pack_active(){
		return class_exists( 'TRP_IN_Seo_Pack');
	}

}
