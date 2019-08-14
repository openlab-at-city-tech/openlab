<?php if (!empty($messages)): ?>
    <div class="updated" style="clear:both"><p><?php echo $messages; ?></p></div>
<?php endif; ?>

<?php
echo do_shortcode('[cminds_free_activation id="cmtt"]');
?>

<div id="cminds_settings_container">

    <div class="cminds_settings_description">

        <div class="clear"></div>
        
        <p>
            <strong>Supported Shortcodes:</strong> <a href="javascript:void(0)" onclick="jQuery(this).parent().next().slideToggle()">Show/Hide</a>
        </p>

        <ul style="display:none;list-style-type:disc;margin-left:20px;">
            <li><strong>Exclude from parsing</strong> - [glossary_exclude] text [/glossary_exclude]</li>
            <li><del><strong>Show glossary category index</strong> - [glossary cat="cat name"]</del> - Only in <a href="<?php echo CMTT_URL; ?>"  target="_blank">Pro+</a></li>
            <li><del><strong>Show Merriam-Webster Dictionary</strong> - [glossary_dictionary term="term name"]</del>- Only in <a href="<?php echo CMTT_URL; ?>" target="_blank">Pro+</a></li>
            <li><del><strong>Show Merriam-Webster Thesaurus</strong> - [glossary_thesaurus term="term name"]</del>- Only in <a href="<?php echo CMTT_URL; ?>"  target="_blank">Pro+</a></li>
            <li><del><strong>Translate</strong> - [glossary_translate term="text-to-translate" source="english" target="spanish"]</del>- Only in <a href="<?php echo CMTT_URL; ?>"  target="_blank">Pro+</a></li>
            <li><del><strong>Custom glossary tooltip</strong> - [glossary_tooltip content="text"] term [/glossary_tooltip]</del> - Only in <a href="<?php echo CMTT_URL; ?>"  target="_blank">Pro+</a></li>
            <li><del><strong>Apply tooltip</strong> - [cm_tooltip_parse] text [/cm_tooltip_parse] </del>- Only in <a href="<?php echo CMTT_URL; ?>"  target="_blank">Pro+</a></li>
            <li><del><strong>Wikipedia</strong> - [glossary_wikipedia term="term name"]</del> - Only in <a href="<?php echo CMTT_URL; ?>"  target="_blank">Ecommerce version</a></li>
            <li><strong>    For more shortcode options <a href="https://creativeminds.helpscoutdocs.com/article/134-cmtg-shortcodes-available-shortcodes" target="_blank">please visit the shortcode help document</a> </strong></li>
        </ul>

        <p>
            <?php
            $glossaryId = get_option('cmtt_glossaryID');
            if ($glossaryId > 0) :

                $glossaryIndexPageEditLink = admin_url('post.php?post=' . $glossaryId . '&action=edit');
                $glossaryIndexPageLink = get_page_link($glossaryId);
                ?>
                <strong>Link to the Glossary Index Page:</strong> <a href="<?php echo $glossaryIndexPageLink; ?>" target="_blank"><?php echo $glossaryIndexPageLink; ?></a> (<a title="Edit the Glossary Index Page" href="<?php echo $glossaryIndexPageEditLink; ?>">edit</a>)
                <?php
            endif;
            ?>
        </p>
        <p>
            <strong>Example of Glossary Term link:</strong> <?php echo trailingslashit(home_url(get_option('cmtt_glossaryPermalink'))) . 'sample-term' ?>
        </p>
        <form method="post">
            <div>
                <div class="cmtt_field_help_container">Warning! This option will completely erase all of the data stored by the CM Tooltip Glossary in the database: terms, options, synonyms etc. <br/> It will also remove the Glossary Index Page. <br/> It cannot be reverted.</div>
                <input onclick="return confirm('All database items of CM Tooltip Glossary (terms, options etc.) will be erased. This cannot be reverted.')" type="submit" name="cmtt_tooltipPluginCleanup" value="Cleanup database" class="button cmtt-cleanup-button"/>
                <span style="display: inline-block;position: relative;"></span>
            </div>
        </form>

        <?php
// check permalink settings
        if (get_option('permalink_structure') == '') {
            echo '<span style="color:red">Your WordPress Permalinks needs to be set to allow plugin to work correctly. Please Go to <a href="' . admin_url() . 'options-permalink.php" target="new">Settings->Permalinks</a> to set Permalinks to Post Name.</span><br><br>';
        }
        ?>
    </div>

    <?php
//include plugin_dir_path(__FILE__) . '/call_to_action.phtml';
    ?>

    <br/>
    <div class="clear"></div>

    <form method="post">
        <?php wp_nonce_field('cmtt-update-options'); ?>
        <input type="hidden" name="action" value="update" />

        <div id="tabs" class="glossarySettingsTabs">
            <div class="glossary_loading"></div>

            <?php
            CMTooltipGlossaryBackend::renderSettingsTabsControls();

            CMTooltipGlossaryBackend::renderSettingsTabs();
            ?>

            <!-- Start Server information Module -->
            <div id="tabs-0">
                <div class='block'>
                    <?php echo do_shortcode('[cminds_free_guide id="cmtt"]'); ?>
                </div>
            </div>

            <div id="tabs-1">
                <div class="block">
                    <h3>General Settings</h3>
                    <table class="floated-form-table form-table">
                        <tr valign="top" class="whole-line">
                            <th scope="row">Glossary Index Page ID</th>
                            <td>
                                <?php wp_dropdown_pages(array('name' => 'cmtt_glossaryID', 'selected' => (int) get_option('cmtt_glossaryID', -1), 'show_option_none' => '-None-', 'option_none_value' => '0')) ?>
                                <br/><input type="checkbox" name="cmtt_glossaryID" value="-1" /> Generate page for Glossary Index
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select the page ID of the page you would like to use as the Glossary Index Page. If you select "-None-" terms will still be highlighted in relevant posts/pages but there won't be a central list of terms (Glossary Index Page). If you check the checkbox a new page would be generated automatically. WARNING! You have to manually remove old pages!</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Glossary Terms Permalink</th>
                            <td><input type="text" name="cmtt_glossaryPermalink" value="<?php echo get_option('cmtt_glossaryPermalink'); ?>" /></td>
                            <td colspan="2" class="cmtt_field_help_container">Enter the name you would like to use for the permalink to the Glossary Terms.
                                By default this is "glossary", however you can update this if you wish.
                                If you are using a parent please indicate this in path eg. "/path/glossary", otherwise just leave glossary or the name you have chosen.
                                <br/><br/>
                                The permalink of the Glossary Index Page will change automatically, but you can change it manually (if you like) using the "edit" link near the "Link to the Glossary Index Page" above.
                                <br/><br/>WARNING! If you already use this permalink the plugin's behavior may be unpredictable.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Only show terms on single posts/pages (not Homepage, authors etc.)?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryOnlySingle" value="0" />
                                <input type="checkbox" name="cmtt_glossaryOnlySingle" <?php checked(true, get_option('cmtt_glossaryOnlySingle')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you wish to only highlight glossary terms when viewing a single page/post.
                                This can be used so terms aren't highlighted on your homepage, or author pages and other taxonomy related pages.</td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">Highlight terms on posts?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryOnPosts" value="0" />
                                <input type="checkbox" name="cmtt_glossaryOnPosts" <?php checked(true, get_option('cmtt_glossaryOnPosts')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you wish for the glossary to highlight terms on posts.
                                With this deselected, posts won't be searched for matching glossary terms.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Highlight terms on pages?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryOnPages" value="0" />
                                <input type="checkbox" name="cmtt_glossaryOnPages" <?php checked(true, get_option('cmtt_glossaryOnPages')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you wish for the glossary to highlight terms on pages.
                                With this deselected, pages won't be searched for matching glossary terms.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Highlight first term occurance only?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryFirstOnly" value="0" />
                                <input type="checkbox" name="cmtt_glossaryFirstOnly" <?php checked(true, get_option('cmtt_glossaryFirstOnly')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want to only highlight the first occurance of each term on a page/post.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Terms case-sensitive?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryCaseSensitive" value="0" />
                                <input type="checkbox" name="cmtt_glossaryCaseSensitive" <?php checked(true, get_option('cmtt_glossaryCaseSensitive')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want glossary terms to be case-sensitive.</td>
                        </tr>
                    </table>
                    <div class="clear"></div>
                </div>
                <div class="block">
                    <h3>Debug/Conflict Settings</h3>
                    <p>
                        This section holds the settings which are known for causing/solving conflicts with some themes/plugins. <br/>
                        If you experience any problems with Glossary Index Page, please consult the <a href="https://tooltip.cminds.com/cm-tooltip-user-guide/" target="_blank">User Guide</a> and the help icons or search the Support forums before changing those settings (default ones should work best in most cases).
                    </p>
                    <table class="floated-form-table form-table">
                        <tr valign="top">
                            <th scope="row">Only highlight on "main" WP query?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryOnMainQuery" value="0" />
                                <input type="checkbox" name="cmtt_glossaryOnMainQuery" <?php checked(1, get_option('cmtt_glossaryOnMainQuery')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">
                                Select this option if you wish to only highlight glossary terms on main glossary query.
                                Unchecking this box may fix problems with highlighting terms on some themes which manipulate the WP_Query.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Run the function outputting the Glossary Index Page only once</th>
                            <td>
                                <input type="hidden" name="cmtt_removeGlossaryCreateListFilter" value="0" />
                                <input type="checkbox" name="cmtt_removeGlossaryCreateListFilter" <?php checked(1, get_option('cmtt_removeGlossaryCreateListFilter')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">
                                Select this option if you wish to remove the filter responsible for outputting the Glossary Index. <br/>
                                When this option is selected the function responsible for rendering the Glossary Index page (hooked to "the_content" filter) <br/>
                                will run only once and then it will be removed. It's known that this conflicts with some translation plugins (e.g. qTranslate, Jetpack, PageBuilder).
                            </td>
                        </tr>
                    </table>
                    <div class="clear"></div>
                </div>
                <div class="block">
                    <h3>Page Builder Support</h3>
                    <p>Support Advanced Custom fields (ACF) and Page Builders including Divi, Beaver Builder, WPBakery, Elementor and more.</p>
                    <table>
                        <tr valign="top">
                            <th scope="row" valign="middle" align="left" >Page Builder Support:</th>
                            <td><span style="color:red">Available only in Premium versions of the plugin </span>   
                            </td>

                        </tr>
                    </table>
                </div>
                <div class="block">
                    <h3>Referrals</h3>
                    <p>Refer new users to any of the CM Plugins and you'll receive a minimum of <strong>15%</strong> of their purchase! For more information please visit CM Plugins <a href="http://www.cminds.com/referral-program/" target="new">Affiliate page</a></p>
                    <table>
                        <tr valign="top">
                            <th scope="row" valign="middle" align="left" >Enable referrals:</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryReferral" value="0" />
                                <input type="checkbox" name="cmtt_glossaryReferral" <?php checked(1, get_option('cmtt_glossaryReferral')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Enable referrals link at the bottom of the question and the answer page<br><br></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" valign="middle" align="left" ><?php _e('Affiliate Code', 'cm-tooltip-ecommerce'); ?>:</th>
                            <td>
                                <input type="text" name="cmtt_glossaryAffiliateCode" value="<?php echo get_option('cmtt_glossaryAffiliateCode'); ?>" placeholder="<?php _e('Affiliate Code', 'cm-tooltip-ecommerce'); ?>"/>
                            </td>
                            <td colspan="2" class="cmtt_field_help_container"><?php _e('Please add your affiliate code in here.', 'cm-tooltip-ecommerce'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="block">
                    <?php
                    global $cmindsPluginPackage;
                    $freePackage = $cmindsPluginPackage['cmtt'];
                    ?>
                    <h3>Author attribution</h3>
                    <p>Please help us spread a word about our plugin by leaving a discreet powered by link.</p>
                    <table>
                        <tr valign="top">
                            <th scope="row" valign="middle" align="left" >Enable PoweredBy link on your Glossary Index:</th>
                            <td>
                                <input type="hidden" name="<?php echo $freePackage->getPoweredByOption(); ?>" value="0"/>
                                <input type="checkbox" name="<?php echo $freePackage->getPoweredByOption(); ?>" value="1" <?php checked(1, $freePackage->isPoweredByEnabled()); ?>/>
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Enable referrals link at the bottom of the question and the answer page<br><br></td>
                        </tr>
                    </table>
                </div>


            </div>
            <div id="tabs-2">
                <div class="block">
                    <h3>Glossary Index Page Settings</h3>
                    <table class="floated-form-table form-table">
                        <tr valign="top">
                            <th scope="row">Style glossary index page differently?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryDiffLinkClass" value="0" />
                                <input type="checkbox" name="cmtt_glossaryDiffLinkClass" <?php checked(true, get_option('cmtt_glossaryDiffLinkClass')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you wish for the links in the glossary index page to be styled differently than the regular way glossary terms links are styled.  By selecting this option you will be able to use the class 'glossaryLinkMain' to style only the links on the glossary index page otherwise they will retain the class 'glossaryLink' and will be identical to the linked terms on all other pages.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Show glossary index page as tiles</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryListTiles" value="0" />
                                <input type="checkbox" name="cmtt_glossaryListTiles" <?php checked(true, get_option('cmtt_glossaryListTiles')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you wish the glossary index page to be displayed as tiles. This is not recommended when you have long terms.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Tile width</th>
                            <td><input type="text" name="cmtt_glossarySmallTileWidth" value="<?php echo get_option('cmtt_glossarySmallTileWidth', '85px'); ?>" /></td>
                            <td colspan="2" class="cmtt_field_help_container">
                                Select the width of the single tile in the "Small tiles" view
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="tabs-3">
                <div class="block">
                    <h3>Glossary Term - Links</h3>
                    <table class="floated-form-table form-table">
                        <tr valign="top">
                            <th scope="row">Remove link to the glossary term page?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryTermLink" value="0" />
                                <input type="checkbox" name="cmtt_glossaryTermLink" <?php checked(true, get_option('cmtt_glossaryTermLink')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you do not want to show links from posts or pages to the glossary term pages. This will only apply to Post / Pages and not to the glossary index page, for glossary index page please visit index page tab in settings. Keep in mind that the plugin use a <strong>&lt;span&gt;</strong> tag instead of a link tag and if you are using a custom CSS you should take this into account</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Open glossary term page in a new windows/tab?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryInNewPage" value="0" />
                                <input type="checkbox" name="cmtt_glossaryInNewPage" <?php checked(true, get_option('cmtt_glossaryInNewPage')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want glossary term page to open in a new window/tab.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Show HTML "title" attribute for glossary links</th>
                            <td>
                                <input type="hidden" name="cmtt_showTitleAttribute" value="0" />
                                <input type="checkbox" name="cmtt_showTitleAttribute" <?php checked(true, get_option('cmtt_showTitleAttribute')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want to use glossary name as HTML "title" for link</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Show back link on the bottom</th>
                            <td>
                                <input type="hidden" name="cmtt_glossary_addBackLinkBottom" value="0" />
                                <input type="checkbox" name="cmtt_glossary_addBackLinkBottom" <?php checked(true, get_option('cmtt_glossary_addBackLinkBottom')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want to show link back to glossary index from glossary term page</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div id="tabs-4">
                <div class="block">
                    <h3>Tooltip - Content</h3>
                    <table class="floated-form-table form-table">
                        <tr valign="top">
                            <th scope="row">Show tooltip when the user hovers over the term?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryTooltip" value="0" />
                                <input type="checkbox" name="cmtt_glossaryTooltip" <?php checked(true, get_option('cmtt_glossaryTooltip')); ?> value="1" /></td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you wish for the definition to show in a tooltip when the user hovers over the term.  The tooltip can be styled differently using the tooltip.css and tooltip.js files in the plugin folder.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Limit tooltip length?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryLimitTooltip" value="0" />
                                <input type="text" name="cmtt_glossaryLimitTooltip" value="<?php echo get_option('cmtt_glossaryLimitTooltip'); ?>"  />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want to show only a limited number of chars and add "(...)" at the end of the tooltip text. Minimum is 30 chars.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Clean tooltip text?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryFilterTooltip" value="0" />
                                <input type="checkbox" name="cmtt_glossaryFilterTooltip" <?php checked(true, get_option('cmtt_glossaryFilterTooltip')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want to remove extra spaces and special characters from tooltip text.</td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Use term excerpt for hover?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryExcerptHover" value="0" />
                                <input type="checkbox" name="cmtt_glossaryExcerptHover" <?php checked(true, get_option('cmtt_glossaryExcerptHover')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want to use the term excerpt (if it exists) as hover text.
                                <br/>NOTE: You have to manually create the excerpts for term pages using the "Excerpt" field.
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Avoid parsing protected tags?</th>
                            <td>
                                <input type="hidden" name="cmtt_glossaryProtectedTags" value="0" />
                                <input type="checkbox" name="cmtt_glossaryProtectedTags" <?php checked(true, get_option('cmtt_glossaryProtectedTags')); ?> value="1" />
                            </td>
                            <td colspan="2" class="cmtt_field_help_container">Select this option if you want to avoid using the glossary for the following tags: Script, A, H1, H2, H3, PRE, Object.</td>
                        </tr>
                    </table>
                </div>
            </div>
            <!-- Start Server information Module -->
            <div id="tabs-99">
                <div class='block'>
                    <?php echo do_shortcode('[cminds_upgrade_box id="cmtt"]'); ?>
                </div>
            </div>
        </div>
        <p class="submit" style="clear:left">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" name="cmtt_glossarySave" />
        </p>
    </form>
</div>