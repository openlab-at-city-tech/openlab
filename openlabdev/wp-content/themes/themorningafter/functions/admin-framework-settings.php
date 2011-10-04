<?php
/*-----------------------------------------------------------------------------------*/
/* Framework Settings page - woothemes_framework_settings_page */
/*-----------------------------------------------------------------------------------*/

function woothemes_framework_settings_page(){

    $themename =  get_option( 'woo_themename' );      
    $manualurl =  get_option( 'woo_manual' ); 
	$shortname =  'framework_woo'; 

    //Framework Version in Backend Head
    $woo_framework_version = get_option( 'woo_framework_version' );
    
    //Version in Backend Head
    $theme_data = get_theme_data( get_template_directory() . '/style.css' );
    $local_version = $theme_data['Version'];
    
    //GET themes update RSS feed and do magic
	include_once(ABSPATH . WPINC . '/feed.php' );

	$pos = strpos($manualurl, 'documentation' );
	$theme_slug = str_replace( "/", "", substr($manualurl, ($pos + 13))); //13 for the word documentation
	
    //add filter to make the rss read cache clear every 4 hours
    add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 14400;' ) );
	
	$framework_options = array();
	
	$framework_options[] = array( 	"name" => "Framework Settings",
									"icon" => "general",
									"type" => "heading" );
									
	$framework_options[] = array( 	"name" => "Super User (username)",
									"desc" => "Enter your <strong>username</strong> to hide the Framework Settings and Update Framework from other users. Can be reset from the <a href='".home_url()."/wp-admin/options.php'>WP options page</a> under <em>framework_woo_super_user</em>.",
									"id" => $shortname."_super_user", 
									"std" => "",
									"class" => "text",
									"type" => "text" );		

	$framework_options[] = array( 	"name" => "Disable SEO Menu Item",
									"desc" => "Disable the <strong>SEO</strong> menu item in the theme menu.",
									"id" => $shortname."_seo_disable",
									"std" => "",
									"type" => "checkbox" );	

	$framework_options[] = array( 	"name" => "Disable Sidebar Manager Menu Item",
									"desc" => "Disable the <strong>Sidebar Manager</strong> menu item in the theme menu.",
									"id" => $shortname."_sbm_disable",
									"std" => "",
									"type" => "checkbox" );	

	$framework_options[] = array( 	"name" => "Disable Buy Themes Menu Item",
									"desc" => "Disable the <strong>Buy Themes</strong> menu item in the theme menu.",
									"id" => $shortname."_buy_themes_disable",
									"std" => "",
									"type" => "checkbox" );	
								
	$framework_options[] = array( 	"name" => "Enable Custom Navigation",
									"desc" => "Enable the old <strong>Custom Navigation</strong> menu item. Try to use <a href='".home_url()."/wp-admin/nav-menus.php'>WP Menus</a> instead, as this function is outdated.",
									"id" => $shortname."_woonav",
									"std" => "",
									"type" => "checkbox" );	

	$framework_options[] = array( 	"name" => "Theme Update Notification",
									"desc" => "This will enable notices on your theme options page that there is an update available for your theme.",
									"id" => $shortname."_theme_version_checker",
									"std" => "",
									"type" => "checkbox" );	

	$framework_options[] = array( 	"name" => "Disable Shortcodes Stylesheet",
									"desc" => "This disables the output of shortcodes.css in the HEAD section of your site.",
									"id" => $shortname."_disable_shortcodes",
									"std" => "",
									"type" => "checkbox" );	
									
	$framework_options[] = array( 	"name" => "Remove Generator Meta Tags",
									"desc" => "This disables the output of generator meta tags in the HEAD section of your site.",
									"id" => $shortname."_disable_generator",
									"std" => "",
									"type" => "checkbox" );	

										
	$framework_options[] = array( 	"name" => "Image Placeholder",
									"desc" => "Set a default image placeholder for your thumbnails. Use this if you want a default image to be shown if you haven't added a custom image to your post.",
									"id" => $shortname."_default_image",
									"std" => "",
									"type" => "upload" );

	$framework_options[] = array( 	"name" => "Branding",
									"icon" => "misc",
									"type" => "heading" );
	
	$framework_options[] = array( 	"name" => "Options panel header",
									"desc" => "Change the header image for the WooThemes Backend.",
									"id" => $shortname."_backend_header_image",
									"std" => "",
									"type" => "upload" );
									
	$framework_options[] = array( 	"name" => "Options panel icon",
									"desc" => "Change the icon image for the WordPress backend sidebar.",
									"id" => $shortname."_backend_icon",
									"std" => "",
									"type" => "upload" );
									
	$framework_options[] = array( 	"name" => "WordPress login logo",
									"desc" => "Change the logo image for the WordPress login page.",
									"id" => $shortname."_custom_login_logo",
									"std" => "",
									"type" => "upload" );

	$framework_options[] = array( 	"name" => "Import / Export",
									"icon" => "misc",
									"type" => "heading" );
									
	$framework_options[] = array( 	"name" => "Import Options",
									"desc" => "Import the options from another installation of this theme.",
									"id" => $shortname."_import_options",
									"std" => "",
									"type" => "textarea" );
																		
								//Create, Encrypt and Update the Saved Settings
                                global $wpdb;
								delete_option( 'framework_woo_export_options' );
                                $options = get_option( 'woo_template' );
                                $query_inner = '';
                                $count = 0;
								foreach( $options as $option ) {
									
									if(isset($option['id'])){ 
										$count++;
										$option_id = $option['id'];
										
										if($count > 1){ $query_inner .= ' OR '; }
										$query_inner .= "option_name = '$option_id'";
										
										if ( is_array( $option['type'] ) ) {
											foreach ( $option['type'] as $o ) {
												if($count > 1){ $query_inner .= ' OR '; }
												if ( isset( $o['id'] ) ) {
													$option_id = $o['id'];
													$query_inner .= "option_name = '$option_id'";
												}
											}
										}
										
									}
									
								}
								
								// Add Sidebar Manager data to the WooFramework exporter.
								$query_inner .= " OR option_name = 'sbm_woo_sbm_options'";
								
								// Allow child themes/plugins to add their own data to the exporter.
								$query_inner = apply_filters( 'wooframework_export_query_inner', $query_inner );
								
								$query = "SELECT * FROM $wpdb->options WHERE $query_inner";
								                                
                                $results = $wpdb->get_results($query);
                                
                                foreach ($results as $result){
                                
                                     $output[$result->option_name] = $result->option_value;
                                
                                } // End FOREACH Loop
                                
                                $output = serialize($output);										
													
	$framework_options[] = array( 	"name" => "Export Options",
									"desc" => "Export the options to another installation of this theme, or to keep a backup of your options.",
									"id" => $shortname."_export_options",
									"std" => base64_encode($output),
									"type" => "textarea" );
									
/*
	$framework_options[] = array( 	"name" => "Font Stacks (Beta)",
									"icon" => "typography",
									"type" => "heading" );		
	
	$framework_options[] = array( 	"name" => "Font Stack Builder",
									"desc" => "Use the font stack builder to add your own custom font stacks to your theme.
									To create a new stack, fill in the name and a CSS ready font stack.
									Once you have added a stack you can select it from the font menu on any of the 
									Typography settings in your theme options.",
									"id" => $shortname."_font_stack", 
									"std" => "Added Font Stacks",
									"type" => "string_builder" );
*/
	
	global $wp_version;
	
	if ( $wp_version >= '3.1' ) {
	
	$framework_options[] = array( 	"name" => "Admin Bar",
									"icon" => "header",
									"type" => "heading" );
									
	$framework_options[] = array( 	"name" => "Disable WordPress Admin Bar",
									"desc" => "Disable the WordPress Admin Bar.",
									"id" => $shortname."_admin_bar_disable",
									"std" => "",
									"type" => "checkbox" );
									
	$framework_options[] = array( 	"name" => "Enable the WooFramework Admin Bar enhancements",
									"desc" => "Enable several WooFramework-specific enhancements to the WordPress Admin Bar, such as custom navigation items for 'Theme Options'.",
									"id" => $shortname."_admin_bar_enhancements",
									"std" => "",
									"type" => "checkbox" );
									
	}
    
    update_option( 'woo_framework_template', $framework_options );
    
	?>

    <div class="wrap" id="woo_container">
    <div id="woo-popup-save" class="woo-save-popup"><div class="woo-save-save">Options Updated</div></div>
    <div id="woo-popup-reset" class="woo-save-popup"><div class="woo-save-reset">Options Reset</div></div>
        <form action="" enctype="multipart/form-data" id="wooform" method="post">
        <?php
	    	// Add nonce for added security.
	    	if ( function_exists( 'wp_nonce_field' ) ) { wp_nonce_field( 'wooframework-framework-options-update' ); } // End IF Statement
	    	
	    	$woo_nonce = '';
	    	
	    	if ( function_exists( 'wp_create_nonce' ) ) { $woo_nonce = wp_create_nonce( 'wooframework-framework-options-update' ); } // End IF Statement
	    	
	    	if ( $woo_nonce == '' ) {} else {
	    	
	    ?>
	    	<input type="hidden" name="_ajax_nonce" value="<?php echo $woo_nonce; ?>" />
	    <?php
	    	
	    	} // End IF Statement
	    ?>
            <div id="header">
                <div class="logo">
                <?php if(get_option( 'framework_woo_backend_header_image')) { ?>
                <img alt="" src="<?php echo get_option( 'framework_woo_backend_header_image' ); ?>"/>
                <?php } else { ?>
                <img alt="WooThemes" src="<?php echo get_template_directory_uri(); ?>/functions/images/logo.png"/>
                <?php } ?>
                </div>
                <div class="theme-info">
                    <span class="theme"><?php echo $themename; ?> <?php echo $local_version; ?></span>
                    <span class="framework">Framework <?php echo $woo_framework_version; ?></span>
                </div>
                <div class="clear"></div>
            </div>
            <div id="support-links">
                <ul>
                    <li class="changelog"><a title="Theme Changelog" href="<?php echo $manualurl; ?>#Changelog">View Changelog</a></li>
                    <li class="docs"><a title="Theme Documentation" href="<?php echo $manualurl; ?>">View Themedocs</a></li>
                    <li class="forum"><a href="http://forum.woothemes.com" target="_blank">Visit Forum</a></li>
                    <li class="right"><img style="display:none" src="<?php echo get_template_directory_uri(); ?>/functions/images/loading-top.gif" class="ajax-loading-img ajax-loading-img-top" alt="Working..." /><a href="#" id="expand_options">[+]</a> <input type="submit" value="Save All Changes" class="button submit-button" /></li>
                </ul>
            </div>
            <?php $return = woothemes_machine($framework_options); ?>
            <div id="main">
                <div id="woo-nav">
                    <ul>
                        <?php echo $return[1]; ?> 
                    </ul>		
                </div>
                <div id="content">
   				<?php echo $return[0]; ?> 
                </div>
                <div class="clear"></div>
                
            </div>
            <div class="save_bar_top">
            <input type="hidden" name="woo_save" value="save" />
            <img style="display:none" src="<?php echo get_template_directory_uri(); ?>/functions/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
            <input type="submit" value="Save All Changes" class="button submit-button" />        
            </form>
            
            <form action="<?php echo esc_html( $_SERVER['REQUEST_URI'] ) ?>" method="post" style="display:inline" id="wooform-reset">
            <?php
		    	// Add nonce for added security.
		    	if ( function_exists( 'wp_nonce_field' ) ) { wp_nonce_field( 'wooframework-framework-options-reset' ); } // End IF Statement
		    	
		    	$woo_nonce = '';
		    	
		    	if ( function_exists( 'wp_create_nonce' ) ) { $woo_nonce = wp_create_nonce( 'wooframework-framework-options-reset' ); } // End IF Statement
		    	
		    	if ( $woo_nonce == '' ) {} else {
		    	
		    ?>
		    	<input type="hidden" name="_ajax_nonce" value="<?php echo $woo_nonce; ?>" />
		    <?php
		    	
		    	} // End IF Statement
		    ?>
            <span class="submit-footer-reset">
<!--             <input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button" onclick="return confirm( 'Click OK to reset. Any settings will be lost!' );" /> -->
            <input type="hidden" name="woo_save" value="reset" /> 
            </span>
        	</form>
            
            
            </div>    
    
    <div style="clear:both;"></div>    
    </div><!--wrap-->

<?php } ?>