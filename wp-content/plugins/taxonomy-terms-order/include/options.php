<?php


function to_plugin_options()
    {
        $options = get_option('tto_options');
        
        if (isset($_POST['form_submit']))
            {
                    
                $options['capability'] = sanitize_key($_POST['capability']);
                
                $options['autosort']    = isset($_POST['autosort'])     ? intval($_POST['autosort'])    : '';
                $options['adminsort']   = isset($_POST['adminsort'])    ? intval($_POST['adminsort'])   : '';
                    
                ?><div class="updated fade"><p><?php _e('Settings Saved', 'tto') ?></p></div><?php

                update_option('tto_options', $options);   
            }
                        
                    ?>
                      <div class="wrap"> 
                        <div id="icon-settings" class="icon32"></div>
                            <h2><?php _e( "General Settings", 'tto' ) ?></h2>
                            
                            <?php tto_info_box() ?>
                           
                            <form id="form_data" name="form" method="post">   
                                <br />
                                <h2 class="subtitle"><?php _e( "General", 'tto' ) ?></h2>                              
                                <table class="form-table">
                                    <tbody>
                            
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Minimum Level to use this plugin", 'tto' ) ?></label></th>
                                            <td>
                                                <select id="role" name="capability">
                                                    <option value="read" <?php if (isset($options['capability']) && $options['capability'] == "read") echo 'selected="selected"'?>><?php _e('Subscriber', 'cpt') ?></option>
                                                    <option value="edit_posts" <?php if (isset($options['capability']) && $options['capability'] == "edit_posts") echo 'selected="selected"'?>><?php _e('Contributor', 'cpt') ?></option>
                                                    <option value="publish_posts" <?php if (isset($options['capability']) && $options['capability'] == "publish_posts") echo 'selected="selected"'?>><?php _e('Author', 'cpt') ?></option>
                                                    <option value="publish_pages" <?php if (isset($options['capability']) && $options['capability'] == "publish_pages") echo 'selected="selected"'?>><?php _e('Editor', 'cpt') ?></option>
                                                    <option value="install_plugins" <?php if (!isset($options['capability']) || empty($options['capability']) || (isset($options['capability']) && $options['capability'] == "install_plugins")) echo 'selected="selected"'?>><?php _e('Administrator', 'cpt') ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Auto Sort", 'tto' ) ?></label></th>
                                            <td>
                                                <select id="role" name="autosort">
                                                    <option value="0" <?php if ($options['autosort'] == "0") echo 'selected="selected"'?>><?php _e('OFF', 'tto') ?></option>
                                                    <option value="1" <?php if ($options['autosort'] == "1") echo 'selected="selected"'?>><?php _e('ON', 'tto') ?></option>
                                                </select> *(<?php _e( "global setting", 'tto' ) ?>) <?php _e( "Additional description and details at ", 'tto' ) ?><a target="_blank" href="http://www.nsp-code.com/taxonomy-terms-order-and-auto-sort-admin-sort-description-an-usage/"><?php _e( "Auto Sort Description", 'tto' ) ?></a>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Admin Sort", 'tto' ) ?></label></th>
                                            <td>
                                                <input id="adminsort" type="checkbox" <?php if ($options['adminsort'] == "1") {echo ' checked="checked"';} ?> value="1" name="adminsort">
                                                <label for="adminsort"><?php _e("This will change the order of terms within the admin interface", 'tto') ?>. <?php _e( "Additional description and details at ", 'tto' ) ?><a target="_blank" href="http://www.nsp-code.com/taxonomy-terms-order-and-auto-sort-admin-sort-description-an-usage/"><?php _e( "Auto Sort Description", 'tto' ) ?></a></label>
                                            </td>
                                        </tr>
                                        
                                        
                                        
                                    </tbody>
                                </table>
                                

                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'tto') ?>">
                               </p>
                            
                                <input type="hidden" name="form_submit" value="true" />
                                
                            </form>
                                                        
                    <?php  
            echo '</div>';   
        
        
    }

?>