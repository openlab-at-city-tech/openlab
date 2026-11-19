<?php
// Import & Export Translations page; TRP_Import_Export::render_page() sets $sources, $export, $import and $max_upload_size.

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

$trp_ie_first_source = '';
$trp_ie_available    = 0;
foreach ( $sources as $trp_ie_slug => $trp_ie_source ) {
    if ( $trp_ie_source['available'] ) {
        $trp_ie_available++;
        if ( $trp_ie_first_source === '' ) {
            $trp_ie_first_source = $trp_ie_slug;
        }
    }
}

$trp_ie_page_url = admin_url( 'admin.php?page=trp_import_export' );
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <?php do_action( 'trp_settings_navigation_tabs' ); ?>

    <?php // Same wrapper as the Advanced page, so the sub-tabs sit at the same height. ?>
    <div class="advanced_setting_tab_class">
    <div id="trp-settings__wrap" class="trp-ie-page">

        <?php // The Advanced tab's sub-tabs, with Import & Export as the current one. ?>
        <?php do_action( 'trp_before_output_advanced_settings_options' ); ?>

        <div class="trp-ie-cards">

            <div class="trp-settings-container trp-ie-card">
                <h2 class="trp-settings-primary-heading"><?php esc_html_e( 'Export', 'translatepress-multilingual' ); ?></h2>
                <div class="trp-settings-separator"></div>

                <p class="trp-description-text"><?php esc_html_e( 'Download this site\'s translations and settings as one .zip file.', 'translatepress-multilingual' ); ?></p>

                <?php if ( $export['view'] === 'error' ) : ?>
                    <div class="trp-ie-notice trp-ie-notice--error" role="alert"><span><?php echo esc_html( $export['message'] ); ?></span></div>
                <?php endif; ?>

                <?php if ( $export['view'] === 'progress' ) : ?>
                    <?php $this->render_progress( $export['job'] ); ?>
                <?php else : ?>
                    <?php if ( $export['view'] === 'ready' ) : ?>
                        <div class="trp-ie-notice trp-ie-notice--success">
                            <strong><?php esc_html_e( 'Your export file is ready.', 'translatepress-multilingual' ); ?></strong>
                            <span><?php echo esc_html( $export['file_name'] . ' (' . size_format( $export['file_size'], 1 ) . ').' ); ?></span>
                            <span><?php esc_html_e( 'The file is deleted from this server once it has been downloaded.', 'translatepress-multilingual' ); ?></span>
                        </div>
                        <?php if ( $export['keys'] ) : ?>
                            <?php /* translators: %s is a list of keys, e.g. "TranslatePress license key and DeepL API key". */ ?>
                            <div class="trp-ie-notice trp-ie-notice--error"><span><?php echo esc_html( sprintf( __( 'This file holds your %s. Only give it to people you trust.', 'translatepress-multilingual' ), $export['keys'] ) ); ?></span></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <form method="post" action="<?php echo esc_url( $trp_ie_page_url ); ?>" class="trp-ie-export-form">
                        <?php wp_nonce_field( TRP_Import_Export::NONCE_NAME ); ?>
                        <input type="hidden" name="trp_ie_action" value="export_start">
                        <?php if ( $export['site_keys'] ) : ?>
                            <label class="trp-ie-choice trp-ie-keys-choice">
                                <input type="checkbox" name="include_keys" value="yes" id="trp-ie-include-keys" aria-describedby="trp-ie-keys-warning">
                                <span class="trp-primary-text"><?php
                                /* translators: %s is a list of keys, e.g. "TranslatePress license key and DeepL API key". */
                                echo esc_html( sprintf( __( 'Include the %s', 'translatepress-multilingual' ), $export['site_keys'] ) );
                                ?></span>
                            </label>
                            <div class="trp-ie-notice trp-ie-notice--error trp-hidden" id="trp-ie-keys-warning"><span><?php
                                /* translators: %s is a list of keys, e.g. "TranslatePress license key and DeepL API key". */
                                echo esc_html( sprintf( __( 'The export file will hold your %s. Only give it to people you trust.', 'translatepress-multilingual' ), $export['site_keys'] ) );
                                ?></span></div>
                        <?php endif; ?>
                        <div class="trp-ie-actions">
                        <?php if ( $export['view'] === 'ready' ) : ?>
                            <a href="<?php echo esc_url( $export['download_url'] ); ?>" class="trp-submit-btn"><?php esc_html_e( 'Download', 'translatepress-multilingual' ); ?></a>
                            <button type="submit" class="trp-ie-link-btn"><?php esc_html_e( 'Generate a new export file', 'translatepress-multilingual' ); ?></button>
                        <?php else : ?>
                            <button type="submit" class="trp-submit-btn"><?php esc_html_e( 'Generate export file', 'translatepress-multilingual' ); ?></button>
                        <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>

                <details class="trp-ie-details">
                    <summary class="trp-primary-text-bold"><?php esc_html_e( 'What the export file contains', 'translatepress-multilingual' ); ?></summary>

                    <?php // Counting every table is slow on a large site, so the table loads when this is opened. ?>
                    <div data-trp-ie-summary="<?php echo esc_url( wp_nonce_url( add_query_arg( 'trp_ie_summary', 1, $trp_ie_page_url ), TRP_Import_Export::NONCE_NAME ) ); ?>">
                        <p class="trp-description-text"><span class="spinner is-active"></span><?php esc_html_e( 'Counting the translations…', 'translatepress-multilingual' ); ?></p>
                    </div>

                    <p class="trp-description-text"><?php esc_html_e( 'TranslatePress settings and which add-ons are active. The license key and API keys are left out unless you include them.', 'translatepress-multilingual' ); ?></p>
                    <p class="trp-description-text"><?php esc_html_e( 'For the default language, theme and plugin strings are the ones whose wording on this site differs from the original.', 'translatepress-multilingual' ); ?></p>
                </details>

                <p class="trp-description-text trp-ie-note"><?php esc_html_e( 'The file may include translations of drafts and private pages, so share it with care.', 'translatepress-multilingual' ); ?></p>
            </div>

            <div class="trp-settings-container trp-ie-card">
                <h2 class="trp-settings-primary-heading"><?php esc_html_e( 'Import', 'translatepress-multilingual' ); ?></h2>
                <div class="trp-settings-separator"></div>

                <?php if ( ! empty( $import['message'] ) ) : ?>
                    <?php $trp_ie_message = is_array( $import['message'] ) ? $import['message'] : array( 'text' => $import['message'] ); ?>
                    <div class="trp-ie-notice trp-ie-notice--error" role="alert">
                        <?php if ( ! empty( $trp_ie_message['title'] ) ) : ?>
                            <strong><?php echo esc_html( $trp_ie_message['title'] ); ?></strong>
                        <?php endif; ?>
                        <span><?php echo esc_html( $trp_ie_message['text'] ); ?></span>
                        <?php if ( ! empty( $trp_ie_message['list'] ) ) : ?>
                            <ul class="trp-ie-list">
                                <?php foreach ( $trp_ie_message['list'] as $trp_ie_line ) : ?>
                                    <li><?php echo esc_html( $trp_ie_line ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <?php if ( ! empty( $trp_ie_message['after'] ) ) : ?>
                            <span><?php echo esc_html( $trp_ie_message['after'] ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $import['view'] === 'progress' ) : ?>
                    <?php $this->render_progress( $import['job'] ); ?>

                <?php elseif ( $import['view'] === 'upload' ) : ?>
                    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url( $trp_ie_page_url ); ?>" id="trp-ie-upload-form">
                        <?php wp_nonce_field( TRP_Import_Export::NONCE_NAME ); ?>
                        <input type="hidden" name="trp_ie_action" value="import_upload">
                        <?php if ( $trp_ie_available > 1 ) : ?>
                            <div class="trp-ie-field">
                                <label class="trp-primary-text-bold" for="trp-ie-import-source"><?php esc_html_e( 'Import from', 'translatepress-multilingual' ); ?></label>
                                <select id="trp-ie-import-source" name="source" class="trp-select">
                                    <?php foreach ( $sources as $trp_ie_slug => $trp_ie_source ) : ?>
                                        <option value="<?php echo esc_attr( $trp_ie_slug ); ?>" <?php disabled( ! $trp_ie_source['available'] ); ?> <?php selected( $trp_ie_slug, $trp_ie_first_source ); ?>><?php echo esc_html( $trp_ie_source['name'] ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else : ?>
                            <input type="hidden" name="source" value="<?php echo esc_attr( $trp_ie_first_source ); ?>">
                        <?php endif; ?>

                        <?php if ( $trp_ie_first_source === '' ) : ?>
                            <p class="trp-description-text"><?php esc_html_e( 'No import sources are available.', 'translatepress-multilingual' ); ?></p>
                        <?php else : ?>
                            <div class="trp-ie-upload">
                                <span class="dashicons dashicons-upload trp-ie-upload__icon" aria-hidden="true"></span>
                                <label class="trp-primary-text-bold" for="trp-ie-import-file"><?php esc_html_e( 'Choose an export file', 'translatepress-multilingual' ); ?></label>
                                <input type="file" id="trp-ie-import-file" name="import_file" accept=".zip,application/zip" required>
                                <span class="trp-description-text">
                                    <?php
                                    /* translators: %s is the maximum upload size, e.g. 64 MB. */
                                    echo esc_html( sprintf( __( 'Up to %s.', 'translatepress-multilingual' ), size_format( $max_upload_size ) ) );
                                    ?>
                                </span>
                                <button type="submit" class="trp-submit-btn"><?php esc_html_e( 'Upload and review', 'translatepress-multilingual' ); ?></button>
                            </div>
                        <?php endif; ?>
                    </form>

                <?php elseif ( $import['view'] === 'review' ) : ?>
                    <?php
                    $trp_ie_preview = $import['preview'];
                    $trp_ie_site    = function ( $url ) {
                        return strtolower( untrailingslashit( preg_replace( '#^https?://#i', '', (string) $url ) ) );
                    };
                    // Only the parts the file has.
                    $trp_ie_parts = array();
                    foreach ( array( 'dictionary_rows' => __( 'Page translations', 'translatepress-multilingual' ), 'gettext_rows' => __( 'Theme and plugin strings', 'translatepress-multilingual' ), 'slug_rows' => __( 'Translated slugs', 'translatepress-multilingual' ) ) as $trp_ie_column => $trp_ie_heading ) {
                        if ( array_sum( wp_list_pluck( $trp_ie_preview['languages'], $trp_ie_column ) ) > 0 ) {
                            $trp_ie_parts[ $trp_ie_column ] = $trp_ie_heading;
                        }
                    }
                    ?>
                    <form method="post" action="<?php echo esc_url( $trp_ie_page_url ); ?>">
                        <?php wp_nonce_field( TRP_Import_Export::NONCE_NAME ); ?>
                        <h3 class="trp-settings-secondary-heading trp-ie-review-heading">
                            <?php
                            /* translators: %s is a file name. */
                            echo esc_html( sprintf( __( 'Review %s', 'translatepress-multilingual' ), $trp_ie_preview['file_name'] ) );
                            ?>
                        </h3>
                        <p class="trp-primary-text">
                            <?php
                            echo esc_html( sprintf(
                                $trp_ie_site( $trp_ie_preview['site_url'] ) === $trp_ie_site( home_url( '/' ) )
                                    /* translators: 1: a site address, 2: a date and time, 3: a language name. */
                                    ? __( 'Exported from this site (%1$s) on %2$s, with %3$s as the default language.', 'translatepress-multilingual' )
                                    /* translators: 1: a site address, 2: a date and time, 3: a language name. */
                                    : __( 'Exported from %1$s on %2$s, with %3$s as the default language.', 'translatepress-multilingual' ),
                                $trp_ie_preview['site_url'],
                                $trp_ie_preview['exported_at'],
                                $trp_ie_preview['default_language_name']
                            ) );
                            ?>
                        </p>

                        <div class="trp-ie-table-wrap trp-ie-table-wrap--languages" tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Languages in the file', 'translatepress-multilingual' ); ?>">
                            <table class="trp-ie-table">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php esc_html_e( 'Language', 'translatepress-multilingual' ); ?></th>
                                        <th scope="col"><?php esc_html_e( 'On this site now', 'translatepress-multilingual' ); ?></th>
                                        <?php foreach ( $trp_ie_parts as $trp_ie_heading ) : ?>
                                            <th scope="col" class="trp-ie-num"><?php echo esc_html( $trp_ie_heading ); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ( $trp_ie_preview['languages'] as $trp_ie_language ) : ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html( $trp_ie_language['name'] ); ?>
                                            <?php if ( $trp_ie_language['is_default'] ) : ?>
                                                <span class="trp-description-text"><?php esc_html_e( '(the file\'s default language)', 'translatepress-multilingual' ); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $trp_ie_language['on_site'] ? esc_html__( 'Yes', 'translatepress-multilingual' ) : esc_html__( 'No', 'translatepress-multilingual' ); ?></td>
                                        <?php foreach ( $trp_ie_parts as $trp_ie_column => $trp_ie_heading ) : ?>
                                            <td class="trp-ie-num"><?php echo esc_html( $trp_ie_language[ $trp_ie_column ] ? number_format_i18n( $trp_ie_language[ $trp_ie_column ] ) : '—' ); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <fieldset class="trp-ie-fieldset">
                            <legend class="trp-primary-text-bold"><?php esc_html_e( 'When a translation already exists on this site', 'translatepress-multilingual' ); ?></legend>
                            <p class="trp-description-text"><?php esc_html_e( 'Nothing on this site is deleted either way.', 'translatepress-multilingual' ); ?></p>
                            <label class="trp-ie-choice">
                                <input type="radio" name="conflict_mode" value="keep" checked>
                                <span>
                                    <span class="trp-primary-text"><?php esc_html_e( 'Keep this site\'s translations', 'translatepress-multilingual' ); ?></span>
                                    <span class="trp-description-text"><?php esc_html_e( 'Adds what is missing. Automatic translations are replaced by reviewed ones from the file.', 'translatepress-multilingual' ); ?></span>
                                </span>
                            </label>
                            <label class="trp-ie-choice">
                                <input type="radio" name="conflict_mode" value="overwrite">
                                <span>
                                    <span class="trp-primary-text"><?php esc_html_e( 'Use the file\'s translations', 'translatepress-multilingual' ); ?></span>
                                    <span class="trp-description-text"><?php esc_html_e( 'The file\'s version replaces this site\'s, even reviewed ones.', 'translatepress-multilingual' ); ?></span>
                                </span>
                            </label>
                        </fieldset>

                        <?php if ( $trp_ie_preview['settings_included'] ) : ?>
                            <div class="trp-ie-fieldset">
                                <div class="trp-ie-choice">
                                    <input type="checkbox" id="trp-ie-import-settings" name="import_settings" value="yes" aria-describedby="trp-ie-settings-replaces trp-ie-settings-keeps">
                                    <span>
                                        <label for="trp-ie-import-settings" class="trp-primary-text"><?php esc_html_e( 'Also import the settings', 'translatepress-multilingual' ); ?></label>
                                        <span class="trp-description-text" id="trp-ie-settings-replaces"><strong><?php esc_html_e( 'Replaces:', 'translatepress-multilingual' ); ?></strong> <?php esc_html_e( 'languages, language URL codes (such as /fr/), which add-ons are active, language switcher, advanced options, automatic slug translation, the automatic translation log setting, language detection, glossary, post type and taxonomy slug translations.', 'translatepress-multilingual' ); ?></span>
                                        <span class="trp-description-text" id="trp-ie-settings-keeps"><strong><?php esc_html_e( 'Keeps:', 'translatepress-multilingual' ); ?></strong>
                                            <?php
                                            if ( class_exists( 'TRP_Multiple_Domains' ) ) {
                                                esc_html_e( 'Multiple Domains, and the other automatic translation settings: whether it is on, its engine, word limit, crawler blocking and usage alerts. The license key and API keys are a separate choice.', 'translatepress-multilingual' );
                                            } else {
                                                esc_html_e( 'The other automatic translation settings: whether it is on, its engine, word limit, crawler blocking and usage alerts. The license key and API keys are a separate choice.', 'translatepress-multilingual' );
                                            }
                                            ?>
                                        </span>
                                    </span>
                                </div>
                                <?php if ( $trp_ie_preview['variants']['with']['changes'] ) : ?>
                                    <div class="trp-ie-notice trp-ie-notice--warning">
                                        <strong><?php esc_html_e( 'If you import the settings:', 'translatepress-multilingual' ); ?></strong>
                                        <ul class="trp-ie-list">
                                            <?php foreach ( $trp_ie_preview['variants']['with']['changes'] as $trp_ie_line ) : ?>
                                                <li><?php echo esc_html( $trp_ie_line ); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( ! empty( $trp_ie_preview['keys'] ) ) : ?>
                            <fieldset class="trp-ie-fieldset">
                                <?php $trp_ie_key_count = count( $trp_ie_preview['keys'] ); ?>
                                <legend class="trp-primary-text-bold"><?php echo esc_html( wp_sprintf( '%l', array_values( $trp_ie_preview['keys'] ) ) ); ?></legend>
                                <label class="trp-ie-choice">
                                    <input type="radio" name="import_keys" value="yes" required>
                                    <span>
                                        <span class="trp-primary-text"><?php echo esc_html( _n( 'Import the key', 'Import the keys', $trp_ie_key_count, 'translatepress-multilingual' ) ); ?></span>
                                        <span class="trp-description-text">
                                            <?php
                                            if ( isset( $trp_ie_preview['keys'][ TRP_Import_Export_Writer::LICENSE_KEY ] ) ) {
                                                esc_html_e( 'The license is activated for this site, which uses one of its activations.', 'translatepress-multilingual' );
                                                echo ' ';
                                            }
                                            esc_html_e( 'Automatic translation is set as on the other site: on or off, and its engine.', 'translatepress-multilingual' );
                                            ?>
                                        </span>
                                    </span>
                                </label>
                                <label class="trp-ie-choice">
                                    <input type="radio" name="import_keys" value="no" required>
                                    <span>
                                        <span class="trp-primary-text"><?php echo esc_html( _n( 'Don\'t import the key', 'Don\'t import the keys', $trp_ie_key_count, 'translatepress-multilingual' ) ); ?></span>
                                        <span class="trp-description-text"><?php esc_html_e( 'This site keeps its own license, API keys and automatic translation engine.', 'translatepress-multilingual' ); ?></span>
                                    </span>
                                </label>
                            </fieldset>
                        <?php endif; ?>

                        <?php foreach ( $trp_ie_preview['variants'] as $trp_ie_variant => $trp_ie_plan ) : ?>
                            <?php if ( $trp_ie_plan['notes'] ) : ?>
                                <div class="trp-ie-notice trp-ie-notice--info trp-ie-plan<?php echo $trp_ie_variant === 'with' ? ' trp-hidden' : ''; ?>" data-trp-ie-plan="<?php echo esc_attr( $trp_ie_variant ); ?>">
                                    <strong><?php esc_html_e( 'Skipped with these options', 'translatepress-multilingual' ); ?></strong>
                                    <ul class="trp-ie-list">
                                        <?php foreach ( $trp_ie_plan['notes'] as $trp_ie_line ) : ?>
                                            <li><?php echo esc_html( $trp_ie_line ); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <p class="trp-ie-backup"><?php esc_html_e( 'An import can\'t be undone. Back up your database first.', 'translatepress-multilingual' ); ?></p>
                        <div class="trp-ie-actions">
                            <button type="submit" name="trp_ie_action" value="import_start" class="trp-submit-btn" id="trp-ie-import-start" data-trp-ie-confirm="<?php esc_attr_e( 'An import can\'t be undone. Back up your database first. Start the import?', 'translatepress-multilingual' ); ?>"><?php esc_html_e( 'Start import', 'translatepress-multilingual' ); ?></button>
                            <button type="submit" name="trp_ie_action" value="import_cancel" class="trp-button-secondary" formnovalidate><?php esc_html_e( 'Cancel', 'translatepress-multilingual' ); ?></button>
                        </div>
                    </form>

                <?php elseif ( $import['view'] === 'report' ) : ?>
                    <?php
                    $trp_ie_report = $import['report'];
                    $trp_ie_labels = array(
                        'inserted'  => __( 'New', 'translatepress-multilingual' ),
                        'updated'   => $trp_ie_report['keep'] ? __( 'Upgraded to reviewed', 'translatepress-multilingual' ) : __( 'Replaced', 'translatepress-multilingual' ),
                        'unchanged' => __( 'Already up to date', 'translatepress-multilingual' ),
                        'kept'      => __( 'Kept (this site\'s version differs)', 'translatepress-multilingual' ),
                        'skipped'   => __( 'Could not be imported', 'translatepress-multilingual' ),
                    );
                    // Only columns with something in them; "New" always shows. Skipped rows are explained in the notes instead of the totals.
                    $trp_ie_columns = array_filter( $trp_ie_labels, function ( $key ) use ( $trp_ie_report ) {
                        return $key === 'inserted' || $trp_ie_report['totals'][ $key ] > 0;
                    }, ARRAY_FILTER_USE_KEY );
                    $trp_ie_lists   = array(
                        array( 'warning', __( 'Settings changed', 'translatepress-multilingual' ), $trp_ie_report['changes'] ),
                        array( 'info', __( 'Skipped as planned', 'translatepress-multilingual' ), $trp_ie_report['planned'] ),
                        array( 'warning', __( 'Notes', 'translatepress-multilingual' ), $trp_ie_report['notes'] ),
                    );
                    ?>
                    <div class="trp-ie-notice trp-ie-notice--success">
                        <h3 class="trp-ie-notice__title">
                            <?php
                            /* translators: %s is a file name. */
                            echo esc_html( sprintf( __( '%s imported.', 'translatepress-multilingual' ), $trp_ie_report['file_name'] ) );
                            ?>
                        </h3>
                        <span>
                            <?php
                            echo esc_html( sprintf(
                                $trp_ie_report['keep']
                                    /* translators: %s is a date and time. */
                                    ? __( 'Finished on %s, keeping this site\'s translations where both had one.', 'translatepress-multilingual' )
                                    /* translators: %s is a date and time. */
                                    : __( 'Finished on %s, using the file\'s translations where both had one.', 'translatepress-multilingual' ),
                                $trp_ie_report['finished_text']
                            ) . ( $trp_ie_report['settings'] ? ' ' . __( 'The settings were imported too.', 'translatepress-multilingual' ) : '' ) );
                            ?>
                        </span>
                    </div>

                    <dl class="trp-ie-counts">
                        <?php foreach ( array_diff_key( $trp_ie_columns, array( 'skipped' => true ) ) as $trp_ie_key => $trp_ie_label ) : ?>
                            <div><dt><?php echo esc_html( $trp_ie_label ); ?></dt><dd><?php echo esc_html( number_format_i18n( $trp_ie_report['totals'][ $trp_ie_key ] ) ); ?></dd></div>
                        <?php endforeach; ?>
                    </dl>

                    <?php foreach ( $trp_ie_lists as $trp_ie_list ) : ?>
                        <?php if ( $trp_ie_list[2] ) : ?>
                            <div class="trp-ie-notice trp-ie-notice--<?php echo esc_attr( $trp_ie_list[0] ); ?>">
                                <strong><?php echo esc_html( $trp_ie_list[1] ); ?></strong>
                                <ul class="trp-ie-list">
                                    <?php foreach ( $trp_ie_list[2] as $trp_ie_line ) : ?>
                                        <li><?php echo esc_html( $trp_ie_line ); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ( $trp_ie_report['steps'] ) : ?>
                        <details class="trp-ie-details">
                            <summary class="trp-primary-text-bold"><?php esc_html_e( 'Details', 'translatepress-multilingual' ); ?></summary>
                            <div class="trp-ie-table-wrap">
                                <table class="trp-ie-table">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?php esc_html_e( 'Part of the file', 'translatepress-multilingual' ); ?></th>
                                            <?php foreach ( $trp_ie_columns as $trp_ie_label ) : ?>
                                                <th scope="col" class="trp-ie-num"><?php echo esc_html( $trp_ie_label ); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ( $trp_ie_report['steps'] as $trp_ie_step ) : ?>
                                        <tr>
                                            <th scope="row"><?php echo esc_html( $trp_ie_step['report_label'] ); ?></th>
                                            <?php foreach ( $trp_ie_columns as $trp_ie_key => $trp_ie_label ) : ?>
                                                <td class="trp-ie-num"><?php echo esc_html( number_format_i18n( $trp_ie_step[ $trp_ie_key ] ) ); ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    <?php endif; ?>

                    <div class="trp-ie-actions">
                        <a class="trp-button-secondary" href="<?php echo esc_url( $trp_ie_page_url ); ?>"><?php esc_html_e( 'Import another file', 'translatepress-multilingual' ); ?></a>
                        <?php if ( $trp_ie_report['settings'] ) : ?>
                            <a class="trp-button-secondary" href="<?php echo esc_url( admin_url( 'options-general.php?page=translate-press' ) ); ?>"><?php esc_html_e( 'Check the languages in General settings', 'translatepress-multilingual' ); ?></a>
                            <a class="trp-button-secondary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View the site', 'translatepress-multilingual' ); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
    </div>
</div>
