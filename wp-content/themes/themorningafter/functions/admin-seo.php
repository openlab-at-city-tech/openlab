<?php
/*-----------------------------------------------------------------------------------*/
/* SEO - woothemes_seo_page */
/*-----------------------------------------------------------------------------------*/

function woothemes_seo_page(){

    $themename =  get_option( 'woo_themename' );      
    $manualurl =  get_option( 'woo_manual' ); 
	$shortname =  'seo_woo'; 
	
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
	
	$inner_pages = array(	'a' => 'Page title; Blog title',
							'b' => 'Page title;',
							'c' => 'Blog title; Page title;',
							'd' => 'Page title; Blog description',
							'e' => 'Blog title; Page title; Blog description'
						);
	
	$seo_options = array();
	
	$seo_options[] = array( "name" => "General Settings",
					"icon" => "general",
					"type" => "heading" );
					
	$seo_options[] = array( "name" => "Please Read",
					"type" => "info",
					"std" => "Welcome to the WooSEO feature. <br /><small>Here we help you take control of your search engine readiness with some in-built theme options. Our themes do however support some of WordPress.org's most commonly used SEO plugins - <strong>All-in-One SEO Pack</strong>, <strong>Headspace 2</strong> and <strong>WordPress SEO By Yoast</strong>. Use the checkbox below to use 3rd party plugin data.</small>" );

	$seo_options[] = array( "name" => "Use 3rd Party Plugin Data",
					"desc" => "Meta data added to <strong>custom fields in posts and pages</strong> will be extracted and used where applicable. This typically does not include Homepages and Archives, and only Singular templates.",	
					"id" => $shortname."_use_third_party_data",
					"std" => "false",
					"type" => "checkbox" ); 
					
	$seo_options[] = array( "name" => "Hide SEO custom fields",
					"desc" => "Check this box to hide the input fields created in the post and page edit screens.",	
					"id" => $shortname."_hide_fields",
					"std" => "false",
					"type" => "checkbox" ); 
				
	$seo_options[] = array( "name" => "Page Title",
					"icon" => "misc",
					"type" => "heading" );
					
	$seo_options[] = array( "name" => "Separator",
					"desc" => "Define a new separator character for your page titles.",
					"id" => $shortname."_seperator",
					"std" => "|",
					"type" => "text" );
					
	$seo_options[] = array( "name" => "Blog Title",
					"desc" => "NOTE: This is the same setting as per the SETTINGS > GENERAL tab in the WordPress backend.",
					"id" => "blogname",
					"std" => "",
					"type" => "text" );
					
	$seo_options[] = array( "name" => "Blog Description",
					"desc" => "NOTE: This is the same setting as per the SETTINGS > GENERAL tab in the WordPress backend.",
					"id" => "blogdescription",
					"std" => "",
					"type" => "text" );
					
	$seo_options[] = array( "name" => "Enable woo_title()",
					"desc" => "woo_title() makes use of WordPress's built in wp_title() function to control the output for your page titles. It's also recommended for use with plugins.",
					"id" => $shortname."_wp_title",
					"std" => "false",
					"class" => "collapsed",
					"type" => "checkbox" ); 
					
	$seo_options[] = array( "name" => "Disable Custom Titles",
					"desc" => "If you prefer to have uniform titles across you theme. Alternatively they will be generated from custom fields and/or plugin data.",
					"id" => $shortname."_wp_custom_field_title",
					"std" => "false",
					"class" => "hidden",
					"type" => "checkbox" ); 
					
	$seo_options[] = array( "name" => "Paged Variable",
					"desc" => "The name variable that will appear then paging through archives.",
					"id" => $shortname."_paged_var",
					"std" => "Page",
					"class" => "hidden",
					"type" => "text" );
					
	$seo_options[] = array( "name" => "Paged Variable Position",
					"desc" => "Change the position where the paged variable will appear.",
					"id" => $shortname."_paged_var_pos",
					"std" => "before",
					"class" => "hidden",
					"options" => array(	'before' => 'Before',
										'after' => 'After'),
					"type" => "select2" );
																
	$seo_options[] = array( "name" => "Homepage Title Layout",
					"desc" => "Define the order the title, description and meta data appears in.",
					"id" => $shortname."_home_layout",
					"std" => "",
					"class" => "hidden",
					"options" => array(	'a' => 'Blog title; blog description',
										'b' => 'Blog title',
										'c' => 'Blog description'),
					"type" => "select2" );
					
	$seo_options[] = array( "name" => "Single Title Layout",
					"desc" => "Define the order the title, description and meta data appears in.",
					"id" => $shortname."_single_layout",
					"std" => "",
					"class" => "hidden",
					"options" => $inner_pages,
					"type" => "select2" );
					
	$seo_options[] = array( "name" => "Page Title Layout",
					"desc" => "Define the order the title, description and meta data appears in.",
					"id" => $shortname."_page_layout",
					"std" => "",
					"class" => "hidden",
					"options" => $inner_pages,
					"type" => "select2" );
					
	$seo_options[] = array( "name" => "Archive Title Layout",
					"desc" => "Define the order the title, description and meta data appears in.",
					"id" => $shortname."_archive_layout",
					"std" => "",
					"class" => "hidden",
					"options" => $inner_pages,
					"type" => "select2" );
					
	$seo_options[] = array( "name" => "Indexing Meta",
					"icon" => "misc",
					"type" => "heading" );
					
	/*$seo_options[] = array( "name" => "Add Indexing Meta",
					"desc" => "Add links to the header telling the search engine what the start, next, previous and home urls are.",
					"id" => $shortname."_meta_basics",
					"std" => "false",
					"type" => "checkbox" ); */
	
	$seo_options[] = array( "name" => "Archive Indexing",
					"desc" => "Select which archives to index on your site. Aids in removing duplicate content from being indexed, preventing content dilution.",
					"id" => $shortname."_meta_indexing",
					"std" => "category",
					"type" => "multicheck",
					"options" => array(	'category' => 'Category Archives',
										'tag' => 'Tag Archives',
										'author' => 'Author Pages',
										'search' => 'Search Results',
										'date' => 'Date Archives')); 
										
	$seo_options[] = array( "name" => "Set meta for Posts & Pages to 'follow' by default",
					"desc" => "By default the woo_meta(); adds a 'nofollow' meta to post and pages, meaning search engines will not index pages leading away from these pages.",
					"id" => $shortname."_meta_single_follow",
					"std" => "",
					"type" => "checkbox" );					
	

	$seo_options[] = array( "name" => "Description Meta",
					"icon" => "misc",
					"type" => "heading" );
					
	$seo_options[] = array( "name" => "Homepage Description",
					"desc" => "Choose where to populate your Homepage meta description from.",
					"id" => $shortname."_meta_home_desc",
					"std" => "a",
					"options" => array(	"a" => "Off",
										"b" => "From WP Site Description",
										"c" => "From Custom Homepage Description"),
					"type" => "radio" );
										
	$seo_options[] = array( "name" => "Custom Homepage Description",
					"desc" => "Add a custom meta description to your homepage.",
					"id" => $shortname."_meta_home_desc_custom",
					"std" => "",
					"type" => "textarea" );
	
	$seo_options[] = array( "name" => "Single Page/Post Description",
					"desc" => "Add your post/page description from custom fields. <strong>* See below</strong>",
					"id" => $shortname."_meta_single_desc",
					"std" => "a",
					"options" => array(	"a" => "Off *",
										"b" => "From Customs Field and/or Plugins",
										"c" => "Automatically from Post/Page Content",
										),
					"type" => "radio" );
					
	$seo_options[] = array( "name" => "Global Post/Page Descriptions",
					"desc" => "Add a custom meta description to your posts and pages. This will only show if no other data is available from the selection above. Will still be added even if setting above is set to \"Off\".",
					"id" => $shortname."_meta_single_desc_sitewide",
					"std" => "",
					"class" => "collapsed",
					"type" => "checkbox" );		
					
	$seo_options[] = array( "name" => "Add Global Description",
					"desc" => "Add your global decription.",
					"id" => $shortname."_meta_single_desc_custom",
					"std" => "",
					"class" => "hidden",
					"type" => "textarea" );
										
	$seo_options[] = array( "name" => "Keyword Meta",
					"icon" => "misc",
					"type" => "heading" );
					
	$seo_options[] = array( "name" => "Homepage Keywords",
					"desc" => "Choose where to populate your Homepage meta description from.",
					"id" => $shortname."_meta_home_key",
					"std" => "a",
					"options" => array(	"a" => "Off",
										"c" => "From Custom Homepage Keywords"),
					"type" => "radio" );
										
	$seo_options[] = array( "name" => "Custom Homepage Keywords",
					"desc" => "Add a (comma separated) list of keywords to your homepage.",
					"id" => $shortname."_meta_home_key_custom",
					"std" => "",
					"type" => "textarea" );
	
	$seo_options[] = array( "name" => "Single Page/Post Keywords",
					"desc" => "Add your post/page keywords from custom field. <strong>* See below</strong>",
					"id" => $shortname."_meta_single_key",
					"std" => "a",
					"options" => array(	"a" => "Off *",
										"b" => "From Custom Fields and/or Plugins",
										"c" => "Automatically from Post Tags &amp; Categories"),
					"type" => "radio" );
					
	$seo_options[] = array( "name" => "Custom Post/Page Keywords",
					"desc" => "Add custom meta keywords to your posts and pages. This will only show if no other data is available from the options above. Even if the option above is set to <strong>'Off'</strong>, this will still be added to your site.",
					"id" => $shortname."_meta_single_key_sitewide",
					"std" => "",
					"class" => "collapsed",
					"type" => "checkbox" );		
					
	$seo_options[] = array( "name" => "Custom Post/Page Keywords",
					"desc" => "Add a (comma separated) list of keywords to your posts and pages.",
					"id" => $shortname."_meta_single_key_custom",
					"std" => "",
					"class" => "hidden",
					"type" => "textarea" );
					
					
	update_option( 'woo_seo_template',$seo_options);
					
    
	?>

    <div class="wrap" id="woo_container">
    <?php
    
    	if(
    		class_exists( 'All_in_One_SEO_Pack') || 
    		class_exists( 'Headspace_Plugin') || 
    		class_exists( 'WPSEO_Admin' ) || 
    		class_exists( 'WPSEO_Frontend' )
    	  ) { 
				
			echo "<div id='woo-seo-notice' class='woo-notice'><p><strong>3rd Party SEO Plugin(s) Detected</strong> - Some WooTheme SEO functionality has been disabled.</p></div>";
						
		}
    
    ?>  
    <?php
    
    	if ( get_option( 'blog_public') == 0 ) { 
				
			echo "<div id='woo-seo-notice-privacy' class='woo-notice'><p><strong>This site is set to Private</strong> - SEO is disabled, change settings <a href='". admin_url( 'options-privacy.php' ) . "'>here</a>.</p></div>";
						
		}
    
    ?>  
    <div id="woo-popup-save" class="woo-save-popup"><div class="woo-save-save">Options Updated</div></div>
    <div id="woo-popup-reset" class="woo-save-popup"><div class="woo-save-reset">Options Reset</div></div>
        <form action="" enctype="multipart/form-data" id="wooform">
        <?php
	    	// Add nonce for added security.
	    	if ( function_exists( 'wp_nonce_field' ) ) { wp_nonce_field( 'wooframework-seo-options-update' ); } // End IF Statement
	    	
	    	$woo_nonce = '';
	    	
	    	if ( function_exists( 'wp_create_nonce' ) ) { $woo_nonce = wp_create_nonce( 'wooframework-seo-options-update' ); } // End IF Statement
	    	
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
            <?php $return = woothemes_machine($seo_options); ?>
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
            <img style="display:none" src="<?php echo get_template_directory_uri(); ?>/functions/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
            <input type="submit" value="Save All Changes" class="button submit-button" />        
            </form>
            
            <form action="<?php echo esc_html( $_SERVER['REQUEST_URI'] ) ?>" method="post" style="display:inline" id="wooform-reset">
            <?php
		    	// Add nonce for added security.
		    	if ( function_exists( 'wp_nonce_field' ) ) { wp_nonce_field( 'wooframework-seo-options-reset' ); } // End IF Statement
		    	
		    	$woo_nonce = '';
		    	
		    	if ( function_exists( 'wp_create_nonce' ) ) { $woo_nonce = wp_create_nonce( 'wooframework-seo-options-reset' ); } // End IF Statement
		    	
		    	if ( $woo_nonce == '' ) {} else {
		    	
		    ?>
		    	<input type="hidden" name="_ajax_nonce" value="<?php echo $woo_nonce; ?>" />
		    <?php
		    	
		    	} // End IF Statement
		    ?>
            <span class="submit-footer-reset">
            <input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button" onclick="return confirm( 'Click OK to reset. Any settings will be lost!' );" />
            <input type="hidden" name="woo_save" value="reset" /> 
            </span>
        	</form>

            
            </div>

    
    
    <div style="clear:both;"></div>    
    </div><!--wrap-->

<?php } ?>