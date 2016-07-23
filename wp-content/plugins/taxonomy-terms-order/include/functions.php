<?php
    
    /**
    * @desc 
    * 
    * Return UserLevel
    * 
    */
    function tto_userdata_get_user_level($return_as_numeric = FALSE)
        {
            global $userdata;
            
            $user_level = '';
            for ($i=10; $i >= 0;$i--)
                {
                    if (current_user_can('level_' . $i) === TRUE)
                        {
                            $user_level = $i;
                            if ($return_as_numeric === FALSE)
                                $user_level = 'level_'.$i; 
                            break;
                        }    
                }        
            return ($user_level);
        } 
        
    function tto_info_box()
        {
            ?>
                <div id="cpt_info_box">
                     <div id="p_right"> 
                        
                        <div id="p_socialize">
                            
                            <div class="p_s_item s_f">
                                <div id="fb-root"></div>
                                <script>(function(d, s, id) {
                                  var js, fjs = d.getElementsByTagName(s)[0];
                                  if (d.getElementById(id)) return;
                                  js = d.createElement(s); js.id = id;
                                  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
                                  fjs.parentNode.insertBefore(js, fjs);
                                }(document, 'script', 'facebook-jssdk'));</script>
                                
                                <div class="fb-like" data-href="https://www.facebook.com/Nsp-Code-190329887674484/" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>
                                
                            </div>
                            
                            <div class="p_s_item s_t">
                                <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.nsp-code.com" data-text="Define custom order for your post types through an easy to use javascript AJAX drag and drop interface. No theme code updates are necessarily, this plugin will take care of query update." data-count="none">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
                            </div>
                            
                            <div class="p_s_item s_gp">
                                <!-- Place this tag in your head or just before your close body tag -->
                                <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

                                <!-- Place this tag where you want the +1 button to render -->
                                <div class="g-plusone" data-size="small" data-annotation="none" data-href="http://nsp-code.com/"></div>
                            </div>
                            
                            <div class="clear"></div>
                        </div>
                        
                        <div id="donate_form">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                                <input type="hidden" name="cmd" value="_donations">
                                <input type="hidden" name="business" value="electronice_delphi@yahoo.com">
                                <input type="hidden" name="lc" value="RO">
                                <input type="hidden" name="item_name" value="Nsp-Code">
                                <input type="hidden" name="item_number" value="taxonomy-terms-order">
                                <input type="hidden" name="no_note" value="0">
                                <input type="hidden" name="cn" value="Add special instructions to the seller:">
                                <input type="hidden" name="no_shipping" value="2">
                                <input type="hidden" name="cancel_return" value="http://nsp-code.com/donate.php">
                                <input type="hidden" name="currency_code" value="USD">
                                <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
                                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                            </form>

                        </div>
                    </div>
                    
                    <p><?php _e( "Did you find this plugin useful? Please support our work with a donation or write an article about this plugin in your blog with a link to our site", 'taxonomy-terms-order' ) ?> <br /><strong>http://www.nsp-code.com/</strong>.</p>
                    <h4><?php _e( "Did you know there is available an advanced version of this plug-in?", 'taxonomy-terms-order' ) ?> <a target="_blank" href="http://www.nsp-code.com/premium-plugins/wordpress-plugins/advanced-taxonomy-terms-order/"><?php _e( "Read more", 'taxonomy-terms-order' ) ?></a></h4>
                    <p><?php _e('Check our', 'taxonomy-terms-order') ?> <a target="_blank" href="https://wordpress.org/plugins/post-terms-order/">Post Terms Order</a> <?php _e('plugin which allow to custom sort categories and custom taxonomies terms per post basis', 'taxonomy-terms-order') ?> </p>
                    <p><?php _e( "Check our", 'taxonomy-terms-order' ) ?> <a target="_blank" href="https://wordpress.org/plugins/post-types-order/">Post Types Order</a> <?php _e( "plugin which allow to custom sort all posts, pages, custom post types", 'taxonomy-terms-order' ) ?> </p>
                    
                    <div class="clear"></div>
                </div>
            
            <?php   
        }

?>