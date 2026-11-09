<?php
    if ( !defined( 'ABSPATH' ) )
        exit();

    $trp                   = TRP_Translate_Press::get_trp_instance();
    $trp_languages         = $trp->get_component( 'languages' );

    $default_language      = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
    $translation_languages = isset( $this->settings['translation-languages'] ) ? $this->settings['translation-languages'] : array();

    // Native names for every language code we need to display.
    $language_names = $trp_languages->get_language_names( array_unique( array_merge( array( $default_language ), $translation_languages ) ) );

    // Target languages = all translation languages except the default one (it has its own field/column).
    $target_languages = array_values( array_diff( $translation_languages, array( $default_language ) ) );

    // English language names used for the Translations column (matches the JS rendering).
    $english_language_names = $trp_languages->get_language_names(
        array_unique( array_merge( array( $default_language ), $translation_languages ) ),
        'english_name'
    );

    // Existing glossary terms.
    $glossary_terms = get_option( TRP_Glossary_Queries::OPTION_NAME, array() );
    if ( ! is_array( $glossary_terms ) ) {
        $glossary_terms = array();
    }

    // Managing glossary terms is only available for the TranslatePress AI engine.
    $glossary_enabled = $this->is_tp_ai_active();
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'trp_glossary_settings' ); ?>

        <?php do_action( 'trp_settings_navigation_tabs' ); ?>

        <div class="advanced_setting_tab_class">
            <div id="trp-settings__wrap">
                <?php
                $machine_translation_tab = $trp->get_component( 'machine_translation_tab' );

                if ( $machine_translation_tab && method_exists( $machine_translation_tab, 'trp_machine_translation_content_table' ) ) {
                    $machine_translation_tab->trp_machine_translation_content_table();
                }
                ?>

                <input type="hidden" name="tab" id="trp_machine_translation_settings_referer" value="trp_mt_subtab_glossary">

                <div id="trp-glossary-ajax-response"></div>

                <div id="col-container" class="wp-clearfix trp-glossary-columns<?php echo $glossary_enabled ? '' : ' trp-glossary-locked'; ?>">

                    <?php if ( ! $glossary_enabled ) : ?>
                        <div class="trp-glossary-lock-overlay">
                            <div class="trp-glossary-lock-message">
                                <p class="trp-glossary-lock-title"><strong><?php esc_html_e( 'Glossary is available only with the TranslatePress AI engine.', 'translatepress-multilingual' ); ?></strong></p>
                                <p>
                                    <?php
                                    printf(
                                        /* translators: %s is a link labelled "TranslatePress AI". */
                                        esc_html__( 'Select the %s automatic translation engine to add and manage glossary terms. For other translation engines (such as DeepL or Google Translate), glossary functions are available in the translation provider’s dashboard.', 'translatepress-multilingual' ),
                                        '<a href="' . esc_url( admin_url( 'admin.php?page=trp_machine_translation&tab=trp_mt_subtab_general' ) ) . '">' . esc_html__( 'TranslatePress AI', 'translatepress-multilingual' ) . '</a>'
                                    );
                                    ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>


                    <!-- Left column: add/edit glossary term form -->
                    <div id="col-left">
                        <div class="col-wrap">
                            <div class="trp-settings-container trp-settings-container-trp_mt_subtab_glossary">
                                <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'Add Glossary Term', 'translatepress-multilingual' ); ?></h3>
                                <div class="trp-settings-separator"></div>

                                <p class="trp-description-text">
                                    <?php esc_html_e( 'Define words or phrases that should not be translated automatically, and optionally provide replacements per language.', 'translatepress-multilingual' ); ?>
                                </p>

                                <div class="form-wrap">
                                    <!-- Default language term -->
                                    <div class="form-field form-required">
                                        <label for="trp-glossary-term-default">
                                            <?php
                                            /* translators: %s is the default language name. */
                                            printf( esc_html__( 'Term (%s)', 'translatepress-multilingual' ), esc_html( isset( $language_names[ $default_language ] ) ? $language_names[ $default_language ] : $default_language ) );
                                            ?>
                                        </label>
                                        <input name="trp_glossary_settings[term][<?php echo esc_attr( $default_language ); ?>]" id="trp-glossary-term-default" type="text" value="" size="40" aria-required="true" />
                                        <p><?php esc_html_e( 'The original word or phrase in the default language.', 'translatepress-multilingual' ); ?></p>
                                    </div>

                                    <!-- One field per translation language -->
                                    <?php foreach ( $target_languages as $language_code ) : ?>
                                        <div class="form-field">
                                            <label for="trp-glossary-term-<?php echo esc_attr( $language_code ); ?>">
                                                <?php echo esc_html( isset( $language_names[ $language_code ] ) ? $language_names[ $language_code ] : $language_code ); ?>
                                            </label>
                                            <input name="trp_glossary_settings[term][<?php echo esc_attr( $language_code ); ?>]" id="trp-glossary-term-<?php echo esc_attr( $language_code ); ?>" type="text" value="" size="40" />
                                        </div>
                                    <?php endforeach; ?>

                                    <p class="submit">
                                        <button type="button" id="trp-glossary-add-term" class="button button-primary">
                                            <?php esc_html_e( 'Add Term', 'translatepress-multilingual' ); ?>
                                        </button>
                                        <span class="spinner trp-glossary-spinner"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column: glossary terms list table -->
                    <div id="col-right">
                        <div class="col-wrap">
                            <div class="trp-settings-container trp-settings-container-trp_mt_subtab_glossary trp-glossary-table-container">
                                <h3 class="trp-settings-primary-heading"><?php esc_html_e( 'Glossary', 'translatepress-multilingual' ); ?></h3>
                                <div class="trp-settings-separator"></div>

                                <?php
                                $default_language_name = isset( $language_names[ $default_language ] ) ? $language_names[ $default_language ] : $default_language;
                                /* translators: %s is the default language name. */
                                $term_column_label = sprintf( esc_html__( 'Term (%s)', 'translatepress-multilingual' ), $default_language_name );
                                ?>

                                <p class="search-box trp-glossary-search-box">
                                    <label class="screen-reader-text" for="trp-glossary-search-input"><?php esc_html_e( 'Search glossary terms', 'translatepress-multilingual' ); ?></label>
                                    <input type="search" id="trp-glossary-search-input" name="s" value="" placeholder="<?php esc_attr_e( 'Search terms', 'translatepress-multilingual' ); ?>" />
                                    <input type="button" id="trp-glossary-search-submit" class="button" value="<?php esc_attr_e( 'Search Glossary', 'translatepress-multilingual' ); ?>" />
                                </p>

                                <div class="tablenav top trp-glossary-tablenav" style="display:none;">
                                    <div class="tablenav-pages"></div>
                                </div>

                                <table class="wp-list-table widefat fixed striped table-view-list trp-glossary-table">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="manage-column column-primary"><?php echo esc_html( $term_column_label ); ?></th>
                                            <th scope="col" class="manage-column"><?php esc_html_e( 'Translations', 'translatepress-multilingual' ); ?></th>
                                        </tr>
                                    </thead>

                                    <!-- Rows are populated via AJAX (trp_glossary_get_terms) on page load. -->
                                    <tbody id="the-list">
                                        <tr class="no-items trp-glossary-loading">
                                            <td class="colspanchange" colspan="2">
                                                <?php esc_html_e( 'Loading…', 'translatepress-multilingual' ); ?>
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <th scope="col" class="manage-column column-primary"><?php echo esc_html( $term_column_label ); ?></th>
                                            <th scope="col" class="manage-column"><?php esc_html_e( 'Translations', 'translatepress-multilingual' ); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <div class="tablenav bottom trp-glossary-tablenav" style="display:none;">
                                    <div class="tablenav-pages"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <p class="description trp-glossary-replace-tool-link">
                    <?php
                    $replace_tool_url = add_query_arg( array( 'page' => 'trp_glossary_replace' ), admin_url( 'admin.php' ) );
                    printf(
                        /* translators: %s is a link to the search & replace tool. */
                        esc_html__( 'To edit existing translations, use the %s.', 'translatepress-multilingual' ),
                        '<a href="' . esc_url( $replace_tool_url ) . '">' . esc_html__( 'search & replace tool', 'translatepress-multilingual' ) . '</a>'
                    );
                    ?>
                </p>
            </div>
        </div>
    </form>
</div>
