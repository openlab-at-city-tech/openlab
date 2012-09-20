<?php


function cpt_plugin_options()
    {
        $options = get_option('cpto_options');
        
        if (isset($_POST['form_submit']))
            {
                    
                $options['level'] = $_POST['level'];
                
                $options['autosort']    = isset($_POST['autosort'])     ? $_POST['autosort']    : '';
                $options['adminsort']   = isset($_POST['adminsort'])    ? $_POST['adminsort']   : '';
                    
                echo '<div class="updated fade"><p>' . __('Settings Saved', 'cpt') . '</p></div>';

                update_option('cpto_options', $options);
                update_option('CPT_configured', 'TRUE');
                   
            }
            
            $queue_data = get_option('ce_queue');
            
                    ?>
                      <div class="wrap"> 
                        <div id="icon-settings" class="icon32"></div>
                            <h2>General Settings</h2>
                           
                           <?php cpt_info_box(); ?>
                           
                            <form id="form_data" name="form" method="post">   
                                <br />
                                <h2 class="subtitle">General</h2>                              
                                <table class="form-table">
                                    <tbody>
                            
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label>Minimum Level to use this plugin</label></th>
                                            <td>
                                                <select id="role" name="level">
                                                    <option value="0" <?php if ($options['level'] == "0") echo 'selected="selected"'?>><?php _e('Subscriber', 'cpt') ?></option>
                                                    <option value="1" <?php if ($options['level'] == "1") echo 'selected="selected"'?>><?php _e('Contributor', 'cpt') ?></option>
                                                    <option value="2" <?php if ($options['level'] == "2") echo 'selected="selected"'?>><?php _e('Author', 'cpt') ?></option>
                                                    <option value="5" <?php if ($options['level'] == "5") echo 'selected="selected"'?>><?php _e('Editor', 'cpt') ?></option>
                                                    <option value="8" <?php if ($options['level'] == "8") echo 'selected="selected"'?>><?php _e('Administrator', 'cpt') ?></option>
                                                </select>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label>Auto Sort</label></th>
                                            <td>
                                                <label for="users_can_register">
                                                <input type="checkbox" <?php if ($options['autosort'] == "1") {echo ' checked="checked"';} ?> value="1" name="autosort">
                                                <?php _e("If checked, the plug-in will automatically update the wp-queries to use the new order (<b>No code update is necessarily</b>).<br /> If you need more order customizations you will need to uncheck this and include 'menu_order' into your theme queries", 'cpt') ?>.</label>
                                                
                                                <p><a href="javascript:;" onclick="jQuery('#example1').slideToggle();;return false;">Show Examples</a></p>
                                                <div id="example1" style="display: none">
                                                
                                                <p class="example"><br /><?php _e('The following PHP code will still return the post in the set-up Order', 'cpt') ?>:</p>
                                                <pre class="example">
$args = array(
              'post_type' => 'feature'
            );

$my_query = new WP_Query($args);
while ($my_query->have_posts())
    {
        $my_query->the_post();
        (..your code..)          
    }
</pre>
<p class="example"><br /><?php _e('Or', 'cpt') ?>:</p>
                                                <pre class="example">
$posts = get_posts($args);
foreach ($posts as $post)
    {
        (..your code..)     
    }
                                                </pre>
                                                
<p class="example"><br /><?php _e('If the Auto Sort is uncheck you will need to use the "orderby" and "order" parameters', 'cpt') ?>:</p>
<pre class="example">
$args = array(
              'post_type' => 'feature',
              'orderby'   => 'menu_order',
              'order'     => 'ASC'
            );
</pre>
                                                
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label>Admin Sort</label></th>
                                            <td>
                                                <label for="users_can_register">
                                                <input type="checkbox" <?php if ($options['adminsort'] == "1") {echo ' checked="checked"';} ?> value="1" name="adminsort">
                                                <?php _e("To affect the admin interface, to see the post types per your new sort, this need to be checked", 'cpt') ?>.</label>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                                   
                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'cpt') ?>">
                               </p>
                            
                                <input type="hidden" name="form_submit" value="true" />
                                
                                
                            </form>

                    <br />
                            
                    <?php  
            echo '</div>';   
        
        
    }

?>