<script type="text/javascript">
    jQuery(document).ready(function() {
        // hides as soon as the DOM is ready
        jQuery('div.wpptopdf-option-body').hide();
        // shows on clicking the noted link
        jQuery('h3').click(function() {
            jQuery(this).toggleClass("open");
            jQuery(this).next("div").slideToggle('1000');
            return false;
        });
        jQuery('.button-secondary').click(function() {
            return confirm('Are you sure you want to clear cache? Its not required if you don\'t have change any PDF Formatting Options.');
        });
    });
</script>
<div id="wpptopdf-options" class="wpptopdf-option wrap">
<div id="icon-options-general" class="icon32"><br></div>
<h2>WP Post to PDF Options</h2>

<p>For detailed documentation visit <a target="_blank" title="WP Post to PDF" rel="bookmark"
                                       href="http://www.techna2.com/blog/documentation/wp-post-to-pdf/">WP Post to PDF</a>
</p>

<p>Feel free to drop comments, issues or suggestions.</p>

<form method="post" action="options.php">
<?php settings_fields('wpptopdf_options');
$wpptopdfopts = get_option('wpptopdf'); ?>
<h3>Accessibility Options</h3>

<div class="wpptopdf-option-body">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Allowed Custom Post Types</th>
            <td>
                <?php 
                $post_types = get_post_types(array('public'   => true),'names');
                foreach( $post_types as $post_type){ ?>
                    <input name="wpptopdf[<?php echo $post_type; ?>]"
                           value="1" <?php echo ($wpptopdfopts[$post_type]) ? 'checked="checked"' : ''; ?>
                           type="checkbox"/> <?php echo $post_type; ?><br/>
                <?php } ?>
                
                <p>Select custom post types for which you want to have PDF download facility.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Non Public Only</th>
            <td>
                <input name="wpptopdf[nonPublic]"
                       value="1" <?php echo ($wpptopdfopts['nonPublic']) ? 'checked="checked"' : ''; ?>
                       type="checkbox"/>

                <p>Select if you want to disable PDF download facility for public users. Only logged in users
                    will be able
                    to use PDF download facility in this case.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Only on Single</th>
            <td>
                <input name="wpptopdf[onSingle]"
                       value="1" <?php echo ($wpptopdfopts['onSingle']) ? 'checked="checked"' : ''; ?>
                       type="checkbox"/>

                <p>Select if you want to display PDF download icon only on single page. Front page will not
                    display PDF
                    download icon in this case.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Exclude/Include</th>
            <td>
                <input name="wpptopdf[include]"
                       value="0" <?php echo ($wpptopdfopts['include']) ? '' : 'checked="checked"'; ?>
                       type="radio"/> Exclude Following&nbsp;&nbsp;&nbsp;
                <input name="wpptopdf[include]"
                       value="1" <?php echo ($wpptopdfopts['include']) ? 'checked="checked"' : ''; ?>
                       type="radio"/> Include Following
                <br/>
                <input type="text" name="wpptopdf[excludeThis]"
                       value="<?php echo ($wpptopdfopts['excludeThis']) ? $wpptopdfopts['excludeThis'] : ''; ?>"/>

                <p>Enter list of comma separated post/page IDs on which you want to exclude/include PDF download
                    facility. <br/><span
                            class="wpptopdf-notice">To
          use PDF download on all page/post, tick "Exclude Following" and leave textbox empty.</span></p>
            </td>
        </tr>

	    <tr valign="top">
            <th scope="row">Exclude/Include from Cache</th>
            <td>
                <input name="wpptopdf[includeCache]"
                       value="0" <?php echo ($wpptopdfopts['includeCache']) ? '' : 'checked="checked"'; ?>
                       type="radio"/> Exclude Following&nbsp;&nbsp;&nbsp;
                <input name="wpptopdf[includeCache]"
                       value="1" <?php echo ($wpptopdfopts['includeCache']) ? 'checked="checked"' : ''; ?>
                       type="radio"/> Include Following
                <br/>
                <input type="text" name="wpptopdf[excludeThisCache]"
                       value="<?php echo ($wpptopdfopts['excludeThisCache']) ? $wpptopdfopts['excludeThisCache'] : ''; ?>"/>

                <p>Enter list of comma separated post/page IDs for which you want to disable PDF cache.
	                PDF file will be generated on the fly when requested for these Posts/Pages. This is usefull when
	                you content of your page/post is updated frequently by someother plugin like 'RSS in Page'.<br/><span
                            class="wpptopdf-notice">To
          use caching on all page/post, tick "Exclude Following" and leave textbox empty.</span></p>
            </td>
        </tr>

    </table>
</div>
<h3>Presentation Options</h3>

<div class="wpptopdf-option-body">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Icon Position</th>
            <td>
                <?php
                      $iconPosition = array('Before' => 'before', 'After' => 'after', 'Before and After' => 'beforeandafter', 'Manual' => 'manual');
                echo '<select name="wpptopdf[iconPosition]">';
                foreach ($iconPosition as $key => $value) {
                    if ($wpptopdfopts['iconPosition'])
                        $checked = ($wpptopdfopts['iconPosition'] == $value) ? 'selected="selected"' : '';
                    echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
                }
                echo '</select>';
                ?>
                <p>Select where to put PDF download icon. <br/><span class="wpptopdf-notice">If you select manual, use following code within your theme
          to add
          icon.</span><br/>
                    <code><?php echo htmlentities('<?php if (function_exists("wpptopdf_display_icon")) echo wpptopdf_display_icon();?>'); ?></code>
                </p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">PDF Download Link</th>
            <td>
                <textarea id="imageIconSrc"
                          name="wpptopdf[imageIcon]"><?php echo ($wpptopdfopts['imageIcon']) ? $wpptopdfopts['imageIcon'] : '<img alt="Download PDF" src="' . WPPT0PDF_URL . '/asset/images/pdf.png">'; ?></textarea>

                <p>Enter content you want to dispaly in PDF download link. You can use HTML. <br/><span
                        class="wpptopdf-notice">Use following code in textbox above for pertty PDF icon.</span><br/><code><?php echo htmlentities('<img alt="Download PDF" src="' . WPPT0PDF_URL . '/asset/images/pdf.png">');  ?></code>
                </p>
            </td>
        </tr>
    </table>
</div>
<h3>PDF Formatting Options</h3>

<div class="wpptopdf-option-body">
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Other Plugins</th>
            <td>
                <input name="wpptopdf[otherPlugin]"
                       value="1" <?php echo ($wpptopdfopts['otherPlugin']) ? 'checked="checked"' : ''; ?>
                       type="checkbox"/>

                <p>Select if you want to accomodate changes applied by other plugins, at runtime, into PDF.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Process Shortcodes</th>
            <td>
                <input name="wpptopdf[processShortcodes]"
                       value="1" <?php echo ($wpptopdfopts['processShortcodes']) ? 'checked="checked"' : ''; ?>
                       type="checkbox"/>

                <p>Select if you want to process shortcodes and display its output in generated PDF file.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Display Author Detail</th>
            <td>
                <input name="wpptopdf[authorDetail]"
                       value="1" <?php echo ($wpptopdfopts['authorDetail']) ? 'checked="checked"' : ''; ?>
                       type="checkbox"/>

                <p>Select if you want to display author name in PDF file.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Dispaly Featured Image</th>
            <td>
                <input name="wpptopdf[featuredImage]"
                       value="1" <?php echo ($wpptopdfopts['featuredImage']) ? 'checked="checked"' : ''; ?>
                       type="checkbox"/>

                <p>Select if you want to display featured image in PDF file. It will display featured image just below title if its set for particular post.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Header Logo Image</th>
            <td>
                <?php if (file_exists(WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-logo.png')) { ?>
                <img src="<?php echo WP_CONTENT_URL . '/uploads/wp-post-to-pdf-logo.png'; ?>"
                     alt="<?php bloginfo('name');?>"/>
                <p>To change this image, replace it here
                    '<?php echo WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-logo.png'; ?>'</p>
                <?php

            }
            else {
                ?>
                <p><span
                        class="wpptopdf-notice">Logo image not found, upload it at  '<?php echo WP_CONTENT_DIR . '/uploads/wp-post-to-pdf-logo.png'; ?>
                    '.</span></p>
                <?php } ?>
            </td>
        </tr>
        <?php $fonts = array('Helvetica' => 'helvetica', 'Times' => 'times', 'Courier' => 'courier', 'Arial Unicode' => 'arialunicid0'); ?>
        <tr valign="top">
            <th scope="row">Header Font</th>
            <td>
                <?php
                      echo '<select name="wpptopdf[headerFont]">';
                    foreach ($fonts as $key => $value) {
                        if ($wpptopdfopts['headerFont'])
                            $checked = ($wpptopdfopts['headerFont'] == $value) ? 'selected="selected"' : '';
                        echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
                    }
                    echo '</select>';
                    ?>
                    <p>Select font for text in header part.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Header Font Size</th>
            <td>
                <input type="text" name="wpptopdf[headerFontSize]"
                       value="<?php echo ($wpptopdfopts['headerFontSize']) ? $wpptopdfopts['headerFontSize'] : '10'; ?>"/>

                <p>Enter font size for text in header part.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Footer Font</th>
            <td>
                <?php
                      echo '<select name="wpptopdf[footerFont]">';
                    foreach ($fonts as $key => $value) {
                        if ($wpptopdfopts['footerFont'])
                            $checked = ($wpptopdfopts['footerFont'] == $value) ? 'selected="selected"' : '';
                        echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
                    }
                    echo '</select>';
                    ?>
                    <p>Select font for text in footer part.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Footer Font Size</th>
            <td>
                <input type="text" name="wpptopdf[footerFontSize]"
                       value="<?php echo ($wpptopdfopts['footerFontSize']) ? $wpptopdfopts['footerFontSize'] : '10'; ?>"/>

                <p>Enter font size for text in footer part.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Content Font</th>
            <td>
                <?php
                    echo '<select name="wpptopdf[contentFont]">';
                    foreach ($fonts as $key => $value) {
                        if ($wpptopdfopts['contentFont'])
                            $checked = ($wpptopdfopts['contentFont'] == $value) ? 'selected="selected"' : '';
                        echo '<option value="' . $value . '" ' . $checked . ' >' . $key . '</option>';
                    }
                    echo '</select>';
                    ?>
                    <p>Select default monospaced font name.</p>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Content Font Size</th>
            <td>
                <input type="text" name="wpptopdf[contentFontSize]"
                       value="<?php echo ($wpptopdfopts['contentFontSize']) ? $wpptopdfopts['contentFontSize'] : '12'; ?>"/>

                <p>Enter font size for text in main content.</p>
            </td>
        </tr>
    </table>
</div>
<p class="submit">
    <input type="submit" class="button-primary" name="wpptopdf[submit]" value="<?php _e('Save Changes') ?>"/>
    <input type="submit" class="button-secondary" name="wpptopdf[submit]"
           value="<?php _e('Save and Reset PDF Cache') ?>"/>
</p>
</form>
<h2>If you find this plugin useful, please rate it here <a target="_blank" title="Will open in new page!" href="http://wordpress.org/extend/plugins/wp-post-to-pdf/">http://wordpress.org/extend/plugins/wp-post-to-pdf/</a>.</h2>
</div>