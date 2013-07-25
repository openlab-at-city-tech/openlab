<?php


    function TOPluginInterface()
        {
            global $wpdb, $wp_locale;
            
            $taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : '';
            $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
                                            
            $post_type_data = get_post_type_object($post_type);
            
            if (!taxonomy_exists($taxonomy))
                $taxonomy = '';

            ?>
            <div class="wrap">
                <div class="icon32" id="icon-edit"><br></div>
                <h2><?php _e( "Taxonomy Order", 'to' ) ?></h2>

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
                <div id="ajax-response"></div>
                
                <noscript>
                    <div class="error message">
                        <p><?php _e( "This plugin can't work without javascript, because it's use drag and drop and AJAX.", 'to' ) ?></p>
                    </div>
                </noscript>

                <div class="clear"></div>
                
                <form action="edit.php" method="get" id="to_form">
                    <input type="hidden" name="page" value="to-interface-<?php echo $post_type ?>" />
                    <?php
                
                     if ($post_type != 'post')
                        echo '<input type="hidden" name="post_type" value="'. $post_type .'" />';

                    //output all available taxonomies for this post type
                    
                    $post_type_taxonomies = get_object_taxonomies($post_type);
                
                    foreach ($post_type_taxonomies as $key => $taxonomy_name)
                        {
                            $taxonomy_info = get_taxonomy($taxonomy_name);  
                            if ($taxonomy_info->hierarchical !== TRUE) 
                                unset($post_type_taxonomies[$key]);
                        }
                        
                    //use the first taxonomy if emtpy taxonomy
                    if ($taxonomy == '' || !taxonomy_exists($taxonomy))
                        {
                            reset($post_type_taxonomies);   
                            $taxonomy = current($post_type_taxonomies);
                        }
                                            
                    if (count($post_type_taxonomies) > 1)
                        {
                
                            ?>
                            
                            <h2 class="subtitle"><?php echo ucfirst($post_type_data->labels->name) ?> <?php _e( "Taxonomies", 'to' ) ?></h2>
                            <table cellspacing="0" class="wp-list-taxonomy">
                                <thead>
                                <tr>
                                    <th style="" class="column-cb check-column" id="cb" scope="col">&nbsp;</th><th style="" class="" id="author" scope="col"><?php _e( "Taxonomy Title", 'to' ) ?></th><th style="" class="manage-column" id="categories" scope="col"><?php _e( "Total  Posts", 'to' ) ?></th>    </tr>
                                </thead>

   
                                <tbody id="the-list">
                                <?php
                                    
                                    $alternate = FALSE;
                                    foreach ($post_type_taxonomies as $post_type_taxonomy)
                                        {
                                            $taxonomy_info = get_taxonomy($post_type_taxonomy);

                                            $alternate = $alternate === TRUE ? FALSE :TRUE;
                                            
                                            $taxonomy_terms = get_terms($key);
                                                             
                                            ?>
                                                <tr valign="top" class="<?php if ($alternate === TRUE) {echo 'alternate ';} ?>" id="taxonomy-<?php echo $taxonomy  ?>">
                                                        <th class="check-column" scope="row"><input type="radio" onclick="to_change_taxonomy(this)" value="<?php echo $post_type_taxonomy ?>" <?php if ($post_type_taxonomy == $taxonomy) {echo 'checked="checked"';} ?> name="taxonomy">&nbsp;</th>
                                                        <td class="categories column-categories"><b><?php echo $taxonomy_info->label ?></b> (<?php echo  $taxonomy_info->labels->singular_name; ?>)</td>
                                                        <td class="categories column-categories"><?php echo count($taxonomy_terms) ?></td>
                                                </tr>
                                            
                                            <?php
                                        }
                                ?>
                                </tbody>
                            </table>
                            <br /><br /> 
                            <?php
                        }
                            ?>

                <div id="order-terms">
                    
      
                    
                    <div id="post-body">                    
                        
                            <ul class="sortable" id="tto_sortable">
                                <?php 
                                    listTerms($taxonomy); 
                                ?>
                            </ul>
                            
                            <div class="clear"></div>
                    </div>
                    
                    <div class="alignleft actions">
                        <p class="submit">
                            <a href="javascript:;" class="save-order button-primary"><?php _e( "Update", 'to' ) ?></a>
                        </p>
                    </div>
                    
                </div> 

                </form>
                
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        
                        var NestedSortableSerializedData;
                        jQuery("ul.sortable").sortable({
                                'tolerance':'intersect',
                                'cursor':'pointer',
                                'items':'> li',
                                'axi': 'y',
                                'placeholder':'placeholder',
                                'nested': 'ul'
                            });
                    });
                    
                    
                    jQuery(".save-order").bind( "click", function() {
                                
                                var mySortable = new Array();
                                jQuery(".sortable").each(  function(){
                                    
                                    var serialized = jQuery(this).sortable("serialize");
                                    
                                    var parent_tag = jQuery(this).parent().get(0).tagName;
                                    parent_tag = parent_tag.toLowerCase()
                                    if (parent_tag == 'li')
                                        {
                                            // 
                                            var tag_id = jQuery(this).parent().attr('id');
                                            mySortable[tag_id] = serialized;
                                        }
                                        else
                                        {
                                            //
                                            mySortable[0] = serialized;
                                        }
                                });
                                
                                //serialize the array
                                var serialize_data = serialize(mySortable);
                                                                                            
                                jQuery.post( ajaxurl, { action:'update-taxonomy-order', order: serialize_data, taxonomy : '<?php echo  $taxonomy ?>' }, function() {
                                    jQuery("#ajax-response").html('<div class="message updated fade"><p><?php _e( "Items Order Updates", 'to' ) ?></p></div>');
                                    jQuery("#ajax-response div").delay(3000).hide("slow");
                                });
                            });
                </script>
                
            </div>
            <?php 
            
            
        }
    
    
    function listTerms($taxonomy) 
            {

                // Query pages.
                $args = array(
                            'orderby'       =>  'term_order',
                            'depth'         =>  0,
                            'child_of'      => 0,
                            'hide_empty'    =>  0
                );
                $taxonomy_terms = get_terms($taxonomy, $args);

                $output = '';
                if (count($taxonomy_terms) > 0)
                    {
                        $output = TOwalkTree($taxonomy_terms, $args['depth'], $args);    
                    }

                echo $output; 
                
            }
        
        function TOwalkTree($taxonomy_terms, $depth, $r) 
            {
                $walker = new TO_Terms_Walker; 
                $args = array($taxonomy_terms, $depth, $r);
                return call_user_func_array(array(&$walker, 'walk'), $args);
            }

?>