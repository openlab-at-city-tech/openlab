<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class TTO_plugin_options
        {
            
            public function plugin_options()
                {
                    $options = TTO_functions::get_settings();
                    
                    if ( isset($_POST['to_form_submit']) &&  isset ( $_POST['to_form_nonce'] )  &&  wp_verify_nonce( sanitize_text_field ( wp_unslash ( $_POST['to_form_nonce'] ) ),'to_form_submit') )
                        {
                            $options['show_reorder_interfaces']         =   isset ( $_POST['show_reorder_interfaces'] ) ?   preg_replace( '/[^a-z]/', '', array_map( 'sanitize_text_field', wp_unslash ( $_POST['show_reorder_interfaces'] ) ) )   :   "";
                            $options['capability']                      =   isset ( $_POST['capability'] )              ?   preg_replace( '/[^a-zA-Z0-9_\-]/', '', sanitize_text_field ( wp_unslash ( $_POST['capability'] ) ) )       :   "";
                            
                            $options['autosort']                        =   isset($_POST['autosort'])     ? intval($_POST['autosort'])    : '';
                            $options['adminsort']                       =   isset($_POST['adminsort'])    ? intval($_POST['adminsort'])   : '';
                                
                            ?><div class="updated fade"><p><?php esc_html_e('Settings Saved', 'taxonomy-terms-order') ?></p></div><?php

                            update_option('tto_options', $options);   
                        }
                                    
                                ?>
                                  <div class="wrap"> 
                                    
                                        <h2><?php esc_html_e( "General Settings", 'taxonomy-terms-order' ) ?></h2>
                                        
                                        <?php TTO_functions::info_box() ?>
                                       
                                        <form id="form_data" name="form" method="post">   
                                            <br />
                                            <h2 class="subtitle"><?php esc_html_e( "General", 'taxonomy-terms-order' ) ?></h2>                              
                                            <table class="form-table">
                                                <tbody>
                                                    
                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php esc_html_e('Show / Hide re-order interface', 'taxonomy-terms-order') ?></label></th>
                                                        <td>
                                                            <p><?php esc_html_e("Display the Taxonomy Order interface for the specified post types.", 'taxonomy-terms-order') ?></p>
                                                            <br />
                                                            <div class="pt-list">
                                                            <?php
                                                                
                                                                $post_types = get_post_types( array(), 'objects' );
                                                                foreach( $post_types as $post_type ) 
                                                                    {
                                                                            
                                                                        //check if there are any taxonomy for this post type
                                                                        $post_type_taxonomies = get_object_taxonomies( $post_type->name );
                                                                        
                                                                        foreach ($post_type_taxonomies as $key => $taxonomy_name)
                                                                            {
                                                                                $taxonomy_info = get_taxonomy($taxonomy_name);  
                                                                                if (empty($taxonomy_info->hierarchical) ||  $taxonomy_info->hierarchical !== TRUE) 
                                                                                    unset($post_type_taxonomies[$key]);
                                                                            }
                                                                            
                                                                        if (count($post_type_taxonomies) == 0)
                                                                            continue;                
                                                                            
                                                                            
                                                                        ?>
                                                                        <p class="pt-item"><label>
                                                                            <select name="show_reorder_interfaces[<?php echo esc_attr ( $post_type->name ) ?>]">
                                                                                <option value="show" <?php if(isset($options['show_reorder_interfaces'][$post_type->name]) && $options['show_reorder_interfaces'][$post_type->name] == 'show') {echo ' selected="selected"';} ?>><?php esc_html_e( "Show", 'taxonomy-terms-order' ) ?></option>
                                                                                <option value="hide" <?php if(isset($options['show_reorder_interfaces'][$post_type->name]) && $options['show_reorder_interfaces'][$post_type->name] == 'hide') {echo ' selected="selected"';} ?>><?php esc_html_e( "Hide", 'taxonomy-terms-order' ) ?></option>
                                                                            </select> &nbsp;&nbsp;<?php echo esc_html ( $post_type->label ); ?>
                                                                        </label><br />&nbsp;</p>
                                                                        <?php
                                                                    }
                                                 
                                                            
                                                            ?>
                                                            </div>
                                                        </td>
                                                         
                                                    </tr>

                                                    
                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php esc_html_e( "Minimum Level to use this plugin", 'taxonomy-terms-order' ) ?></label></th>
                                                        <td>
                                                            <select id="role" name="capability">
                                                                <option value="read" <?php if (isset($options['capability']) && $options['capability'] == "read") echo 'selected="selected"'?>><?php esc_html_e('Subscriber', 'taxonomy-terms-order') ?></option>
                                                                <option value="edit_posts" <?php if (isset($options['capability']) && $options['capability'] == "edit_posts") echo 'selected="selected"'?>><?php esc_html_e('Contributor', 'taxonomy-terms-order') ?></option>
                                                                <option value="publish_posts" <?php if (isset($options['capability']) && $options['capability'] == "publish_posts") echo 'selected="selected"'?>><?php esc_html_e('Author', 'taxonomy-terms-order') ?></option>
                                                                <option value="publish_pages" <?php if (isset($options['capability']) && $options['capability'] == "publish_pages") echo 'selected="selected"'?>><?php esc_html_e('Editor', 'taxonomy-terms-order') ?></option>
                                                                <option value="manage_options" <?php if (!isset($options['capability']) || empty($options['capability']) || (isset($options['capability']) && $options['capability'] == "manage_options")) echo 'selected="selected"'?>><?php esc_html_e('Administrator', 'taxonomy-terms-order') ?></option>
                                                                <?php do_action('tto/admin/plugin_options/capability') ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    
                                                    
                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php esc_html_e( "Auto Sort", 'taxonomy-terms-order' ) ?></label></th>
                                                        <td>
                                                            <select id="autosort" name="autosort">
                                                                <option value="0" <?php if ($options['autosort'] == "0") echo 'selected="selected"'?>><?php esc_html_e('OFF', 'taxonomy-terms-order') ?></option>
                                                                <option value="1" <?php if ($options['autosort'] == "1") echo 'selected="selected"'?>><?php esc_html_e('ON', 'taxonomy-terms-order') ?></option>
                                                            </select>
                                                            <label for="autosort"> *(<?php esc_html_e( "global setting", 'taxonomy-terms-order' ) ?>) <?php esc_html_e( "Additional description and details at ", 'taxonomy-terms-order' ) ?><a target="_blank" href="https://www.nsp-code.com/taxonomy-terms-order-and-auto-sort-admin-sort-description-an-usage/"><?php esc_html_e( "Auto Sort Description", 'taxonomy-terms-order' ) ?></a></label>
                                                        </td>
                                                    </tr>
                                                    
                                                    <tr valign="top">
                                                        <th scope="row" style="text-align: right;"><label><?php esc_html_e( "Admin Sort", 'taxonomy-terms-order' ) ?></label></th>
                                                        <td>
                                                            <select id="adminsort" name="adminsort">
                                                                <option value="0" <?php if ($options['adminsort'] == "0") echo 'selected="selected"'?>><?php esc_html_e('OFF', 'taxonomy-terms-order') ?></option>
                                                                <option value="1" <?php if ($options['adminsort'] == "1") echo 'selected="selected"'?>><?php esc_html_e('ON', 'taxonomy-terms-order') ?></option>
                                                            </select>
                                                            <label for="adminsort"><?php esc_html_e("This will change the order of terms within the admin interface", 'taxonomy-terms-order') ?>. <?php esc_html_e( "Additional description and details at ", 'taxonomy-terms-order' ) ?><a target="_blank" href="https://www.nsp-code.com/taxonomy-terms-order-and-auto-sort-admin-sort-description-an-usage/"><?php esc_html_e( "Auto Sort Description", 'taxonomy-terms-order' ) ?></a></label>
                                                        </td>
                                                    </tr>
                                                    
                                                    
                                                    
                                                </tbody>
                                            </table>
                                            

                                            <p class="submit">
                                                <input type="submit" name="Submit" class="button-primary" value="<?php esc_html_e('Save Settings', 'taxonomy-terms-order') ?>">
                                           </p>
                                        
                                            <?php wp_nonce_field('to_form_submit','to_form_nonce'); ?>
                                            <input type="hidden" name="to_form_submit" value="true" />
                                            
                                        </form>
                                                                    
                                <?php  
                        echo '</div>';   
                    
                    
                }    
            
        }
 

?>