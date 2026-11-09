<?php
    if ( !defined( 'ABSPATH' ) )
        exit();

    $trp           = TRP_Translate_Press::get_trp_instance();
    $trp_languages = $trp->get_component( 'languages' );

    $default_language      = isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '';
    $translation_languages = isset( $this->settings['translation-languages'] ) ? $this->settings['translation-languages'] : array();

    // All target languages (every translation language except the default).
    $target_languages = array_values( array_diff( $translation_languages, array( $default_language ) ) );

    $language_names = $trp_languages->get_language_names(
        array_unique( array_merge( array( $default_language ), $translation_languages ) ),
        'english_name'
    );

    // Requested term + its stored translations.
    $term        = isset( $_GET['term'] ) ? trim( sanitize_text_field( wp_unslash( $_GET['term'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only page render, capability-gated.
    $glossary    = get_option( TRP_Glossary_Queries::OPTION_NAME, array() );
    $term_exists = is_array( $glossary ) && array_key_exists( $term, $glossary );
    $translations = ( $term_exists && is_array( $glossary[ $term ] ) ) ? $glossary[ $term ] : array();

    // Only an error when a specific term was requested but no longer exists (stale link).
    // With no term the page works as a generic search & replace tool.
    $show_error = ( $term !== '' && ! $term_exists );

    $glossary_page_url = add_query_arg( array( 'page' => 'trp_machine_translation_glossary' ), admin_url( 'admin.php' ) );
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <?php do_action( 'trp_settings_navigation_tabs' ); ?>

    <div class="advanced_setting_tab_class">
        <div id="trp-settings__wrap">

            <?php
            // Automatic Translation subtabs (General / Glossary / Advanced), same as the Glossary page.
            $machine_translation_tab = $trp->get_component( 'machine_translation_tab' );
            if ( $machine_translation_tab && method_exists( $machine_translation_tab, 'trp_machine_translation_content_table' ) ) {
                $machine_translation_tab->trp_machine_translation_content_table();
            }
            ?>

            <p class="trp-glossary-back-link">
                <a href="<?php echo esc_url( $glossary_page_url ); ?>">&larr; <?php esc_html_e( 'Back to Glossary', 'translatepress-multilingual' ); ?></a>
            </p>

            <?php if ( ! $show_error ) : ?>
                <!-- Standalone lookup tool: preview how a term is currently translated. Independent of the replace form below. -->
                <div class="trp-settings-container">
                    <h3 class="trp-settings-primary-heading">
                        <?php esc_html_e( 'Search Existing Translations', 'translatepress-multilingual' ); ?>
                    </h3>
                    <div class="trp-settings-separator"></div>

                    <p class="trp-description-text">
                        <?php
                        esc_html_e( 'Search the existing dictionary entries to see how a term is currently translated in each language.', 'translatepress-multilingual' );
                        echo ' ';
                        $string_translation_url = add_query_arg( 'trp-string-translation', 'true', home_url() ) . '#/regular/';
                        printf(
                            /* translators: %s is a link to the String Translation Regular tab. */
                            esc_html__( 'To fully explore all the translations, use %s.', 'translatepress-multilingual' ),
                            '<a href="' . esc_url( $string_translation_url ) . '" target="_blank">' . esc_html__( 'String Translation', 'translatepress-multilingual' ) . '</a>'
                        );
                        ?>
                    </p>

                    <p class="search-box trp-glossary-search-dictionary-box">
                        <label class="screen-reader-text" for="trp-glossary-search-dictionary-input"><?php esc_html_e( 'Search existing translations', 'translatepress-multilingual' ); ?></label>
                        <input type="search" id="trp-glossary-search-dictionary-input" value="<?php echo esc_attr( $term ); ?>" size="40" />
                        <button type="button" id="trp-glossary-search-dictionary-submit" class="button"><?php esc_html_e( 'Search', 'translatepress-multilingual' ); ?></button>
                        <span class="spinner trp-glossary-search-spinner"></span>
                    </p>

                    <div id="trp-glossary-search-dictionary-results"></div>
                </div>
            <?php endif; ?>

            <div class="trp-settings-container">
                <h3 class="trp-settings-primary-heading">
                    <?php esc_html_e( 'Replace Existing Translations', 'translatepress-multilingual' ); ?>
                </h3>
                <div class="trp-settings-separator"></div>

                <?php if ( $show_error ) : ?>

                    <div class="notice notice-error" role="alert">
                        <p><?php esc_html_e( 'The requested glossary term could not be found. It may have been deleted.', 'translatepress-multilingual' ); ?></p>
                    </div>
                    <p><a href="<?php echo esc_url( $glossary_page_url ); ?>" class="button"><?php esc_html_e( '&laquo; Back to Glossary', 'translatepress-multilingual' ); ?></a></p>

                <?php else : ?>

                    <p class="trp-description-text">
                        <?php
                            esc_html_e( 'For each language, enter the current (existing) translation you want to find; every case-insensitive occurrence in the stored translations will be replaced with the new translation, keeping the original capitalization.', 'translatepress-multilingual' );
                        ?>
                        <br />
                        <?php esc_html_e( 'Leaving a language field empty will skip that language entirely (no changes will be made for it). Only one existing translation per language can be entered at a time. If you have multiple translations to replace for the same language, simply run the replacement again with the next value.', 'translatepress-multilingual' ); ?>
                        <br />
                        <em><?php esc_html_e( 'The "Existing translation" is not the term in the default language, but rather the already existing wrong translation you want to correct.', 'translatepress-multilingual' ); ?></em>
                    </p>

                    <div id="trp-glossary-replace-response"></div>

                    <form id="trp-glossary-replace-form" class="form-wrap" data-term="<?php echo esc_attr( $term ); ?>">
                        <table class="wp-list-table widefat fixed striped table-view-list">
                            <thead>
                                <tr>
                                    <th scope="col" class="manage-column"><?php esc_html_e( 'Language', 'translatepress-multilingual' ); ?></th>
                                    <th scope="col" class="manage-column"><?php esc_html_e( 'Existing translation', 'translatepress-multilingual' ); ?></th>
                                    <th scope="col" class="manage-column"><?php esc_html_e( 'New translation', 'translatepress-multilingual' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $target_languages as $language_code ) : ?>
                                    <?php $new_value = isset( $translations[ $language_code ] ) ? $translations[ $language_code ] : ''; ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html( isset( $language_names[ $language_code ] ) ? $language_names[ $language_code ] : $language_code ); ?>
                                        </td>
                                        <td>
                                            <input type="text" class="trp-glossary-replace-existing regular-text"
                                                   data-lang="<?php echo esc_attr( $language_code ); ?>"
                                                   value="" size="30" />
                                        </td>
                                        <td>
                                            <input type="text" class="trp-glossary-replace-new regular-text"
                                                   data-lang="<?php echo esc_attr( $language_code ); ?>"
                                                   value="<?php echo esc_attr( $new_value ); ?>" size="30" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <p class="submit">
                            <button type="button" id="trp-glossary-replace-submit" class="button button-primary">
                                <?php esc_html_e( 'Replace in existing translations', 'translatepress-multilingual' ); ?>
                            </button>
                            <a href="<?php echo esc_url( $glossary_page_url ); ?>" class="button"><?php esc_html_e( 'Cancel', 'translatepress-multilingual' ); ?></a>
                            <span class="spinner trp-glossary-replace-spinner"></span>
                        </p>
                    </form>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
