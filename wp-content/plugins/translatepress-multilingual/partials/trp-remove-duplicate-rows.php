<div id="trp-addons-page" class="wrap">

    <h1> <?php esc_html_e( 'TranslatePress Settings', 'translatepress-multilingual' );?></h1>

    <div class="grid feat-header">
        <div class="grid-cell">
            <h2><?php esc_html_e('Optimize TranslatePress database tables', 'translatepress-multilingual' );?> </h2>
	        <?php if ( empty( $_GET['trp_rm_duplicates'] ) ){ ?>
                <div>
			        <?php echo wp_kses_post( __( '<strong>IMPORTANT NOTE: Before performing this action it is strongly recommended to first backup the database.</strong><br><br>', 'translatepress-multilingual' ) )?>
                </div>
                <form onsubmit="return confirm('<?php echo esc_js( __( 'IMPORTANT: It is strongly recommended to first backup the database!! Are you sure you want to continue?', 'translatepress-multilingual' ) ); ?>');">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_attr_e('Operations to perform', 'translatepress-multilingual');?></th>
                            <td>
                                <input type="hidden" name="trp_rm_nonce" value="<?php echo esc_attr( wp_create_nonce('tpremoveduplicaterows') )?>">
                                <input type="hidden" name="page" value="trp_remove_duplicate_rows">
                                <input type="hidden" name="trp_rm_batch" value="1">
                                <input type="hidden" name="trp_rm_duplicates" value="<?php echo esc_attr( $this->settings['translation-languages'][0] ); ?>">

                                <input type="checkbox" name="trp_rm_cdata_original_and_dictionary" id="trp_rm_cdata_original_and_dictionary" checked><label for="trp_rm_cdata_original_and_dictionary"><?php esc_attr_e( 'Remove CDATA for original and dictionary strings', 'translatepress-multilingual' ); ?></label></input><br>
                                <p class="description">
                                    <?php echo wp_kses ( __( 'Removes CDATA from trp_original_strings and trp_dictionary_* tables.<br>This type of content should not be detected by TranslatePress. It might have been introduced in the database in older versions of the plugin.', 'translatepress-multilingual' ), array( 'br' => array() )  ); ?>
                                </p>
                                <br>
                                <input type="checkbox" name="trp_rm_untranslated_links" id="trp_rm_untranslated_links" checked><label for="trp_rm_untranslated_links"><?php esc_attr_e( 'Remove untranslated links from dictionary tables', 'translatepress-multilingual' ); ?></label></input><br>
                                <p class="description">
                                    <?php echo wp_kses ( __( 'Removes untranslated links and images from all trp_dictionary_* tables. These tables contain translations for user-inputted strings such as post content, post title, menus etc.', 'translatepress-multilingual' ), array( 'br' => array() )  ); ?>
                                </p>
                                <br>
                                <input type="checkbox" name="trp_rm_duplicates_gettext" id="trp_rm_duplicates_gettext" checked><label for="trp_rm_duplicates_gettext"><?php esc_attr_e( 'Remove duplicate rows for gettext strings', 'translatepress-multilingual' ); ?></label></input><br>
                                <p class="description">
                                    <?php echo wp_kses ( __( 'Cleans up all trp_gettext_* tables of duplicate rows. These tables contain translations for themes and plugin strings.', 'translatepress-multilingual' ), array( 'br' => array() )  ); ?>
                                </p>
                                <br>
                                <input type="checkbox" name="trp_rm_duplicates_dictionary" id="trp_rm_duplicates_dictionary" checked><label for="trp_rm_duplicates_dictionary"><?php esc_attr_e( 'Remove duplicate rows for dictionary strings', 'translatepress-multilingual' ); ?></label></input><br>
                                <p class="description">
                                    <?php echo wp_kses ( __( 'Cleans up all trp_dictionary_* tables of duplicate rows. These tables contain translations for user-inputted strings such as post content, post title, menus etc.', 'translatepress-multilingual' ), array( 'br' => array() )  ); ?>
                                </p>
                                <br>
                                <input type="checkbox" name="trp_rm_duplicates_original_strings" id="trp_rm_duplicates_original_strings" checked><label for="trp_rm_duplicates_original_strings"><?php esc_attr_e( 'Remove duplicate rows for original dictionary strings', 'translatepress-multilingual' ); ?></label></input><br>
                                <p class="description">
                                    <?php echo wp_kses ( __( 'Cleans up all trp_original_strings table of duplicate rows. This table contains strings in the default language, without any translation.<br>The trp_original_meta table, which contains meta information that refers to the post parent’s id, is also regenerated.<br>Such duplicates can appear in exceptional situations of unexpected behavior.', 'translatepress-multilingual' ), array( 'br' => array() )  ); ?>
                                </p>
                                <br>
                                <input type="checkbox" name="trp_replace_original_id_null" id="trp_replace_original_id_null"><label for="trp_replace_original_id_null"><?php esc_attr_e( 'Replace gettext strings that have original id NULL with the correct original ids', 'translatepress-multilingual' ); ?></label></input><br>
                                <p class="description">
                                    <?php echo wp_kses ( __( 'Fixes an edge case issue where some gettext strings have the original id incorrectly set to NULL, causing problems in the Translation Editor.<br>This operation corrects the original ids in the trp_gettext_* tables.<br>Only check this option if you encountered an issue in the Translation Editor where clicking the green pencil did not bring up the gettext string for translation in the left sidebar.<br>Otherwise, please leave this option unchecked because it\'s an intensive operation.', 'translatepress-multilingual' ), array( 'br' => array() )  ); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <input type="submit" class="button-primary" name="trp_rm_duplicates_of_the_selected_option" value="<?php esc_attr_e( 'Optimize Database', 'translatepress-multilingual' ); ?>">
                </form>
            <?php } ?>

        </div>
    </div>

</div>