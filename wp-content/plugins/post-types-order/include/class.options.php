<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class CptoOptionsInterface
        {
            
            var $CPTO;
            
            
            function __construct()
                {
                    
                    global $CPTO;
                    
                    $this->CPTO =   $CPTO;
                    
                }
            
            function check_options_update()
                {
                    
                    $options    =   $this->CPTO->functions->get_options();
                    
                    if (isset($_POST['form_submit'])    &&  wp_verify_nonce($_POST['cpto_form_nonce'],'cpto_form_submit'))
                        {
                            
                            $options['show_reorder_interfaces'] = (array) $_POST['show_reorder_interfaces'];
                            $options['show_reorder_interfaces'] =   array_map( 'sanitize_key', $options['show_reorder_interfaces'] );
                                
                            $options['capability']              = sanitize_key($_POST['capability']);
                            
                            $options['autosort']                = isset($_POST['autosort'])     ? intval($_POST['autosort'])    : '';
                            $options['adminsort']               = isset($_POST['adminsort'])    ? intval($_POST['adminsort'])   : '';
                            $options['use_query_ASC_DESC']      = isset($_POST['use_query_ASC_DESC'])    ? intval($_POST['use_query_ASC_DESC'])   : '';
                            $options['archive_drag_drop']       = isset($_POST['archive_drag_drop'])    ? intval($_POST['archive_drag_drop'])   : '';
                            
                            $options['navigation_sort_apply']   = isset($_POST['navigation_sort_apply'])    ? intval($_POST['navigation_sort_apply'])   : '';
                                                
                            update_option('cpto_options', $options);
                            update_option('CPT_configured', 'TRUE');
                            
                            add_action( 'admin_notices',        array( $this,   'admin_save_notice') );
                               
                        }   
                    
                }
                
                
            function admin_save_notice()
                {

                    echo "<div class='updated'><p>". esc_html__('Settings Saved', 'post-types-order')  ."</p></div>";
                        
                }
            
            
            function plugin_options_interface()
                {
                    $options    =   $this->CPTO->functions->get_options();
            
                    ?>
                        <div id="cpto" class="wrap"> 
                            <h2><?php esc_html_e('General Settings', 'post-types-order') ?></h2>
                           
                            <?php $this->CPTO->functions->cpt_info_box(); ?>
                           
                            <form id="form_data" name="form" method="post">   
                                <br />
                                <h2 class="subtitle"><?php esc_html_e('General', 'post-types-order') ?></h2>                              
                                <table class="form-table">
                                    <tbody>
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php esc_html_e('Show / Hide re-order interface', 'post-types-order') ?></label></th>
                                            <td>
                                                <?php
                                                
                                                    $post_types = get_post_types();
                                                    foreach( $post_types as $post_type_name ) 
                                                        {
                                                            //ignore list
                                                            $ignore_post_types  =   array(
                                                                                            'reply',
                                                                                            'topic',
                                                                                            'report',
                                                                                            'status'  
                                                                                            );
                                                            
                                                            if( in_array($post_type_name, $ignore_post_types) )
                                                                continue;
                                                            
                                                            if( is_post_type_hierarchical($post_type_name) )
                                                                continue;
                                                                
                                                            $post_type_data = get_post_type_object( $post_type_name );
                                                            if($post_type_data->show_ui === FALSE)
                                                                continue;
                                                ?>
                                                <p><label>
                                                    <select name="show_reorder_interfaces[<?php echo esc_attr($post_type_name) ?>]">
                                                        <option value="show" <?php if(isset($options['show_reorder_interfaces'][$post_type_name]) && $options['show_reorder_interfaces'][$post_type_name] == 'show') {echo ' selected="selected"';} ?>><?php esc_html_e( "Show", 'post-types-order' ) ?></option>
                                                        <option value="hide" <?php if(isset($options['show_reorder_interfaces'][$post_type_name]) && $options['show_reorder_interfaces'][$post_type_name] == 'hide') {echo ' selected="selected"';} ?>><?php esc_html_e( "Hide", 'post-types-order' ) ?></option>
                                                    </select> &nbsp;&nbsp;<?php echo esc_html ( $post_type_data->labels->singular_name ); ?>
                                                </label><br />&nbsp;</p>
                                                <?php  } ?>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php esc_html_e('Minimum Level to use this plugin', 'post-types-order') ?></label></th>
                                            <td>
                                                <select id="role" name="capability">
                                                    <option value="read" <?php if (isset($options['capability']) && $options['capability'] == "read") echo 'selected="selected"'?>><?php esc_html_e('Subscriber', 'post-types-order') ?></option>
                                                    <option value="edit_posts" <?php if (isset($options['capability']) && $options['capability'] == "edit_posts") echo 'selected="selected"'?>><?php esc_html_e('Contributor', 'post-types-order') ?></option>
                                                    <option value="publish_posts" <?php if (isset($options['capability']) && $options['capability'] == "publish_posts") echo 'selected="selected"'?>><?php esc_html_e('Author', 'post-types-order') ?></option>
                                                    <option value="publish_pages" <?php if (isset($options['capability']) && $options['capability'] == "publish_pages") echo 'selected="selected"'?>><?php esc_html_e('Editor', 'post-types-order') ?></option>
                                                    <option value="manage_options" <?php if (!isset($options['capability']) || empty($options['capability']) || (isset($options['capability']) && $options['capability'] == "manage_options")) echo 'selected="selected"'?>><?php esc_html_e('Administrator', 'post-types-order') ?></option>
                                                    <?php do_action('pto/admin/plugin_options/capability') ?>
                                                </select>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label for="autosort"><?php esc_html_e('Auto Sort', 'post-types-order') ?></label></th>
                                            <td>
                                                <p><input type="checkbox" <?php checked( '1', $options['autosort'] ); ?> id="autosort" value="1" name="autosort"> <?php esc_html_e("If checked, the plug-in automatically update the WordPress queries to use the new order", 'post-types-order'); ?> ( <b><?php esc_html_e("No code update is necessarily", 'post-types-order'); ?></b> )</p>
                                                <p class="description"><?php esc_html_e("If only certain queries need to use the custom sort, keep this unchecked and include 'orderby' => 'menu_order' into query parameters", 'post-types-order') ?>.
                                                <br />
                                                <a href="http://www.nsp-code.com/sample-code-on-how-to-apply-the-sort-for-post-types-order-plugin/" target="_blank"><?php esc_html_e('Additional Description and Examples', 'post-types-order') ?></a></p>
                                                
                                            </td>
                                        </tr>
                                        
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label for="adminsort"><?php esc_html_e('Admin Sort', 'post-types-order') ?></label></th>
                                            <td>
                                                <p>
                                                <input type="checkbox" <?php checked( '1', $options['adminsort'] ); ?> id="adminsort" value="1" name="adminsort">
                                                <?php esc_html_e("To affect the admin interface, to see the post types per your new sort, this need to be checked", 'post-types-order') ?>.</p>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label for="archive_drag_drop"><?php esc_html_e('Use query ASC / DESC parameter ', 'post-types-order') ?></label></th>
                                            <td>
                                                <p>
                                                <input type="checkbox" <?php checked( '1', $options['use_query_ASC_DESC'] ); ?> id="use_query_ASC_DESC" value="1" name="use_query_ASC_DESC">
                                                <?php esc_html_e("If the query include an Ascending or Descending order paramether, use that. If query order is set to DESC the order will be reversed.", 'post-types-order') ?>.</p>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label for="archive_drag_drop"><?php esc_html_e('Archive Drag&Drop ', 'post-types-order') ?></label></th>
                                            <td>
                                                <p>
                                                <input type="checkbox" <?php checked( '1', $options['archive_drag_drop'] ); ?> id="archive_drag_drop" value="1" name="archive_drag_drop">
                                                <?php esc_html_e("Allow sortable drag & drop functionality within default WordPress post type archive. Admin Sort need to be active.", 'post-types-order') ?>.</p>
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label for="navigation_sort_apply"><?php esc_html_e('Next / Previous Apply', 'post-types-order') ?></label></th>
                                            <td>
                                                <p>
                                                <input type="checkbox" <?php checked( '1', $options['navigation_sort_apply'] ); ?> id="navigation_sort_apply" value="1" name="navigation_sort_apply">
                                                <?php esc_html_e("Apply the sort on Next / Previous site-wide navigation.", 'post-types-order') ?> <?php esc_html_e('This can also be controlled through', 'post-types-order') ?> <a href="http://www.nsp-code.com/apply-custom-sorting-for-next-previous-site-wide-navigation/" target="_blank"><?php esc_html_e('code', 'post-types-order') ?></a></p>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                               
                                <p class="submit">
                                <input type="submit" name="Submit" class="button-primary" value="<?php esc_html_e('Save Settings', 'post-types-order') ?>">
                                </p>

                                <?php wp_nonce_field('cpto_form_submit','cpto_form_nonce'); ?>
                                <input type="hidden" name="form_submit" value="true" />
                                
                                
                           </form>

                        </div>        
                    <?php          
                    
                }
        }

?>