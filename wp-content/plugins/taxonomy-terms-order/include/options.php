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
                            
                            <?php tto_info_box() ?>
                           
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