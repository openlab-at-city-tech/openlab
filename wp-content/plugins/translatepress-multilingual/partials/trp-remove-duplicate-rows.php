<?php
    if ( !defined('ABSPATH' ) )
        exit();
?>

<div id="trp-settings-page" class="wrap">
    <?php require_once TRP_PLUGIN_DIR . 'partials/settings-header.php'; ?>

    <div id="trp-settings__wrap" class="grid feat-header">
        <div class="grid-cell trp-settings-container">
            <h2 class="trp-settings-primary-heading"><?php esc_html_e('Optimize TranslatePress database tables', 'translatepress-multilingual' );?> </h2>
            <div class="trp-settings-separator"></div>
	        <?php if ( empty( $_GET['trp_rm_duplicates'] ) ){ ?>
                <div class="trp-settings-warning">
			        <?php echo wp_kses_post( __( '<strong>IMPORTANT NOTE:</strong> Before performing this action it is strongly recommended to first backup the database.', 'translatepress-multilingual' ) )?>
                </div>
                <form onsubmit="return confirm('<?php echo esc_js( __( 'IMPORTANT: It is strongly recommended to first backup the database!! Are you sure you want to continue?', 'translatepress-multilingual' ) ); ?>');">
                    <table class="form-table">
                        <tr>
                            <th scope="row" class="trp-primary-text-bold"><?php esc_attr_e('Operations to perform', 'translatepress-multilingual');?></th>
                            <td>
                                <input type="hidden" name="trp_rm_nonce" value="<?php echo esc_attr( wp_create_nonce('tpremoveduplicaterows') )?>">
                                <input type="hidden" name="page" value="trp_remove_duplicate_rows">
                                <input type="hidden" name="trp_rm_batch" value="1">
                                <input type="hidden" name="trp_rm_duplicates" value="<?php echo esc_attr( $this->settings['translation-languages'][0] ); ?>">
                                <div class="trp-settings-options__wrapper">
                                    <div class="trp-settings-options-item trp-settings-checkbox">
                                        <input type="checkbox" id="trp_rm_cdata_original_and_dictionary" name="trp_rm_cdata_original_and_dictionary" value="yes" checked>
                                        <label for="trp_rm_cdata_original_and_dictionary">
                                            <div class="trp-checkbox-content">
                                                <b class="trp-primary-text-bold"><?php esc_html_e('Remove CDATA for original and dictionary strings', 'translatepress-multilingual'); ?></b>
                                                <span class="trp-description-text">
                                                <?php echo wp_kses(__('Removes CDATA from trp_original_strings and trp_dictionary_* tables.<br>This type of content should not be detected by TranslatePress. It might have been introduced in the database in older versions of the plugin.', 'translatepress-multilingual'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="trp-settings-options-item trp-settings-checkbox">
                                        <input type="checkbox" id="trp_rm_untranslated_links" name="trp_rm_untranslated_links" value="yes" checked>
                                        <label for="trp_rm_untranslated_links">
                                            <div class="trp-checkbox-content">
                                                <b class="trp-primary-text-bold"><?php esc_html_e('Remove untranslated links from dictionary tables', 'translatepress-multilingual'); ?></b>
                                                <span class="trp-description-text">
                                                <?php echo wp_kses(__('Removes untranslated links and images from all trp_dictionary_* tables. These tables contain translations for user-inputted strings such as post content, post title, menus etc.', 'translatepress-multilingual'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="trp-settings-options-item trp-settings-checkbox">
                                        <input type="checkbox" id="trp_rm_duplicates_gettext" name="trp_rm_duplicates_gettext" value="yes" checked>
                                        <label for="trp_rm_duplicates_gettext">
                                            <div class="trp-checkbox-content">
                                                <b class="trp-primary-text-bold"><?php esc_html_e('Remove duplicate rows for gettext strings', 'translatepress-multilingual'); ?></b>
                                                <span class="trp-description-text">
                                                <?php echo wp_kses(__('Cleans up all trp_gettext_* tables of duplicate rows. These tables contain translations for themes and plugin strings.', 'translatepress-multilingual'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="trp-settings-options-item trp-settings-checkbox">
                                        <input type="checkbox" id="trp_rm_duplicates_dictionary" name="trp_rm_duplicates_dictionary" value="yes" checked>
                                        <label for="trp_rm_duplicates_dictionary">
                                            <div class="trp-checkbox-content">
                                                <b class="trp-primary-text-bold"><?php esc_html_e('Remove duplicate rows for dictionary strings', 'translatepress-multilingual'); ?></b>
                                                <span class="trp-description-text">
                                                <?php echo wp_kses(__('Cleans up all trp_dictionary_* tables of duplicate rows. These tables contain translations for user-inputted strings such as post content, post title, menus etc.', 'translatepress-multilingual'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="trp-settings-options-item trp-settings-checkbox">
                                        <input type="checkbox" id="trp_rm_duplicates_original_strings" name="trp_rm_duplicates_original_strings" value="yes" checked>
                                        <label for="trp_rm_duplicates_original_strings">
                                            <div class="trp-checkbox-content">
                                                <b class="trp-primary-text-bold"><?php esc_html_e('Remove duplicate rows for original dictionary strings', 'translatepress-multilingual'); ?></b>
                                                <span class="trp-description-text">
                                                <?php echo wp_kses(__('Cleans up all trp_original_strings table of duplicate rows. This table contains strings in the default language, without any translation.<br>The trp_original_meta table, which contains meta information that refers to the post parent’s ID, is also regenerated.<br>Such duplicates can appear in exceptional situations of unexpected behavior.', 'translatepress-multilingual'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="trp-settings-options-item trp-settings-checkbox">
                                        <input type="checkbox" id="trp_replace_original_id_null" name="trp_replace_original_id_null" value="yes">
                                        <label for="trp_replace_original_id_null">
                                            <div class="trp-checkbox-content">
                                                <b class="trp-primary-text-bold"><?php esc_html_e('Replace gettext strings that have original ID NULL with the correct original IDs', 'translatepress-multilingual'); ?></b>
                                                <span class="trp-description-text">
                                                <?php echo wp_kses(__('Fixes an edge case issue where some gettext strings have the original ID incorrectly set to NULL, causing problems in the Translation Editor.<br>This operation corrects the original IDs in the trp_gettext_* tables.<br>Only check this option if you encountered an issue in the Translation Editor where clicking the green pencil did not bring up the gettext string for translation in the left sidebar.<br>Otherwise, please leave this option unchecked because it\'s an intensive operation.', 'translatepress-multilingual'), array('br' => array())); ?>
                                            </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <input type="submit" class="trp-submit-btn" name="trp_rm_duplicates_of_the_selected_option" value="<?php esc_attr_e( 'Optimize Database', 'translatepress-multilingual' ); ?>">
                </form>
            <?php } ?>

        </div>
    </div>

</div>