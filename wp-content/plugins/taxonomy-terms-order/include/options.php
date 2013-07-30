<?php


function to_plugin_options()
    {
        $options = get_option('tto_options');
        
        if (isset($_POST['form_submit']))
            {
                    
                $options['level'] = $_POST['level'];
                
                $options['autosort']    = isset($_POST['autosort'])     ? $_POST['autosort']    : '';
                $options['adminsort']   = isset($_POST['adminsort'])    ? $_POST['adminsort']   : '';
                    
                ?><div class="updated fade"><p><?php _e('Settings Saved', 'to') ?></p></div><?php

                update_option('tto_options', $options);   
            }
                        
                    ?>
                      <div class="wrap"> 
                        <div id="icon-settings" class="icon32"></div>
                            <h2><?php _e( "General Settings", 'to' ) ?></h2>
                            
                            <div id="cpt_info_box">
                                
                                 <div id="p_right"> 
                        
                                    <div id="p_socialize">
                                        <div class="p_s_item s_gp">
                                            <!-- Place this tag in your head or just before your close body tag -->
                                            <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

                                            <!-- Place this tag where you want the +1 button to render -->
                                            <div class="g-plusone" data-size="small" data-annotation="none" data-href="http://nsp-code.com/"></div>
                                        </div>
                                        <div class="p_s_item s_t">
                                            <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.nsp-code.com" data-text="Define custom order for your taxonomies terms through an easy to use javascript AJAX drag and drop interface. No theme code updates are necessarily, this plugin will take care of query update." data-count="none">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
                                        </div> 
                                        
                                        <div class="p_s_item s_f">
                                            <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.nsp-code.com%2F&amp;send=false&amp;layout=button_count&amp;width=75&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:75px; height:21px;" allowTransparency="true"></iframe>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    
                                    <div id="donate_form">
                                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                                        <input type="hidden" name="cmd" value="_s-xclick">
                                        <input type="hidden" name="hosted_button_id" value="CU22TFDKJMLAE">
                                        <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                        <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
                                        </form>
                                    </div>
                                </div>
                                <p><?php _e( "Did you find this plugin useful? Please support our work with a donation or write an article about this plugin in your blog with a link to our site", 'to' ) ?> <br /><strong>http://www.nsp-code.com/</strong>.</p>
                                <h4><?php _e( "Did you know there is available a more advanced version of this plug-in?", 'to' ) ?> <a target="_blank" href="http://www.nsp-code.com/premium-plugins/wordpress-plugins/advanced-taxonomy-terms-order/"><?php _e( "Read more", 'to' ) ?></a></h4>
                                <p><?php _e( "Check our", 'to' ) ?> <a target="_blank" href="http://wordpress.org/extend/plugins/post-types-order/">Post Types Order</a> <?php _e( "plugin which allow to custom sort all posts, pages, custom post types", 'to' ) ?> </p>
                            </div>
                           
                            <form id="form_data" name="form" method="post">   
                                <br />
                                <h2 class="subtitle"><?php _e( "General", 'to' ) ?></h2>                              
                                <table class="form-table">
                                    <tbody>
                            
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Minimum Level to use this plugin", 'to' ) ?></label></th>
                                            <td>
                                                <select id="role" name="level">
                                                    <option value="0" <?php if ($options['level'] == "0") echo 'selected="selected"'?>><?php _e('Subscriber', 'tto') ?></option>
                                                    <option value="1" <?php if ($options['level'] == "1") echo 'selected="selected"'?>><?php _e('Contributor', 'tto') ?></option>
                                                    <option value="2" <?php if ($options['level'] == "2") echo 'selected="selected"'?>><?php _e('Author', 'tto') ?></option>
                                                    <option value="5" <?php if ($options['level'] == "5") echo 'selected="selected"'?>><?php _e('Editor', 'tto') ?></option>
                                                    <option value="8" <?php if ($options['level'] == "8") echo 'selected="selected"'?>><?php _e('Administrator', 'tto') ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Auto Sort", 'to' ) ?></label></th>
                                            <td>
                                                <select id="role" name="autosort">
                                                    <option value="0" <?php if ($options['autosort'] == "0") echo 'selected="selected"'?>><?php _e('OFF', 'tto') ?></option>
                                                    <option value="1" <?php if ($options['autosort'] == "1") echo 'selected="selected"'?>><?php _e('ON', 'tto') ?></option>
                                                </select> *(<?php _e( "global setting", 'to' ) ?>)
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Admin Sort", 'to' ) ?></label></th>
                                            <td>
                                                <label for="users_can_register">
                                                <input type="checkbox" <?php if ($options['adminsort'] == "1") {echo ' checked="checked"';} ?> value="1" name="adminsort">
                                                <?php _e("This will change the order of terms within the admin interface", 'to') ?>.</label>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"></th>
                                            <td>
                                                <br /><br /><br />
                                <p><b><u><?php _e( "Autosort OFF", 'to' ) ?></u></b></p>                                                
                                <p class="example"><?php _e('No query will be changed, the terms will appear in the original order. To retrieve the terms in the required order you must use the term_order on the orderby parameter', 'to') ?>:</p>
                                <pre class="example">
$argv = array(
                'orderby'       =>  'term_order',
                'hide_empty'    => false
                );
get_terms('category', $argv);
</pre>
                                <p><?php _e( "See more info on the get_terms usage", 'to' ) ?> <a href="http://codex.wordpress.org/Function_Reference/get_terms" target="_blank"><?php _e( "here", 'to' ) ?></a></p>

                                <p><b><u><?php _e( "Autosort ON", 'to' ) ?></u></b></p> 
                                <p class="example"><?php _e('The queries will be updated, all terms will appear in the order you manually defined. This is recommended if you don\'t want to change any theme code to apply the terms order', 'to') ?></p>
                                                   
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                

                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'to') ?>">
                               </p>
                            
                                <input type="hidden" name="form_submit" value="true" />
                                
                            </form>
                                                        
                    <?php  
            echo '</div>';   
        
        
    }

?>