<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Woothemes Custom Navigation Setup
-- Woothemes Custom Navigation Setup
-- Woothemes Custom Navigation Menu Item
-- Woothemes Custom Navigation Scripts
- Woothemes Custom Navigation Interface
- Woothemes Custom Navigation Functions
-- woo_custom_navigation_output()
-- woo_custom_navigation_sub_items()
-- woo_child_is_current()
-- woo_get_pages()
-- woo_get_categories()
-- woo_custom_navigation_default_sub_items()
- Recursive Get Child Items Function
- Woothemes Custom Navigation Menu Widget

-----------------------------------------------------------------------------------*/


/*-----------------------------------------------------------------------------------*/
/* Woothemes Custom Navigation Menu Setup
/* Setup of the Menu
/* Add Menu Item to the theme
/* Scripts - JS and CSS
/*-----------------------------------------------------------------------------------*/


function woo_custom_navigation_setup() {
	
	$nav_version = '1.0.21';
	//Custom Navigation Menu Setup
	
	//Check for Upgrades
	if (get_option( 'woo_settings_custom_nav_version') <> '') {
		$nav_version_in_db = get_option( 'woo_settings_custom_nav_version' );
	}
	else {
		$nav_version_in_db = '0';
	}
	
	//Override for menu descriptions
	update_option( 'woo_settings_custom_nav_advanced_options','yes' );
	
	if (isset($_GET['page'])) {
		$page_var = $_GET['page'];
	}
	else {
		$page_var = '';
	}
	
	if ($page_var == 'custom_navigation') 
	{
	
		//CREATE Custom Menu tables
		global $wpdb;
		$table_name = $wpdb->prefix . "woo_custom_nav_records";
		$charset_collate = '';
		if ( ! empty($wpdb->charset) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";		
		}
		if ( ! empty($wpdb->collate) ) {
			$charset_collate .= " COLLATE $wpdb->collate";		
		}
		
		if(($wpdb->get_var( "show tables like '$table_name'") != $table_name) || ($nav_version_in_db <> $nav_version)) 
		{
				
			$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			position bigint(11) NOT NULL,
			post_id bigint(11) NOT NULL,
			parent_id bigint(11) NOT NULL,
			custom_title text NOT NULL,
			custom_link text NOT NULL,
			custom_description text NOT NULL,
			menu_icon text NOT NULL,
			link_type varchar(55) NOT NULL default 'custom',
			menu_id bigint(11) NOT NULL,
			custom_anchor_title text NOT NULL,
			new_window bigint(11) NOT NULL default 0,
			UNIQUE KEY id (id)
			) ".$charset_collate.";";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
			
			update_option( 'woo_settings_custom_nav_version',$nav_version);
			
		}
		
		$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";
		
		if(($wpdb->get_var( "show tables like '$table_name_menus'") != $table_name_menus) || ($nav_version_in_db <> $nav_version)) 
		{
			$data_insert = false;
			//CHECK if tables exist
			if ($wpdb->get_var( "show tables like '$table_name_menus'") != $table_name_menus) {
				$data_insert = true;
			}
			$sql = "CREATE TABLE " . $table_name_menus . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			menu_name text NOT NULL,
			UNIQUE KEY id (id)
			) ".$charset_collate.";";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
			
			//ADD data to tables
			if ($data_insert) {
				
				//POPULATE with first menu
				$insert = "INSERT INTO ".$table_name_menus." (menu_name) "."VALUES ( 'Woo Menu 1')";
  				$results = $wpdb->query( $insert );
  			
  				//POPULATE with first menu content
  				//Pages
  				$table_name = $wpdb->prefix . "woo_custom_nav_records";
  				
  				//GET all current pages
  				$pages_args = array(
			    	'child_of' => 0,
					'sort_order' => 'ASC',
					'sort_column' => 'post_title',
					'hierarchical' => 1,
					'exclude' => '',
					'include' => '',
					'meta_key' => '',
					'meta_value' => '',
					'authors' => '',
					'parent' => 0,
					'exclude_tree' => '',
					'number' => '',
					'offset' => 0 );
				
				$pages_array = get_pages($pages_args);
				$counter = 1;
				
				//INSERT Loop
				foreach ($pages_array as $post) 
				{
						//CHECK if is top level element
					if ($post->post_parent == 0) 
					{
						//CHECK for existing page records
						$table_name_parent = $wpdb->prefix . "woo_custom_nav_records";
						$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name_parent." WHERE post_id='".$post->post_parent."' AND link_type='page' AND menu_id='1'" );
						
						if ($woo_result > 0 && isset($woo_result[0]->id)) {
								$parent_id = $woo_result[0]->id;
						}
						else {
							$parent_id = 0;
						}
						
						//INSERT page		
						//Convert string to UTF-8
						$str_converted = stripslashes($post->post_title);
						//$insert_title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );						
						//$insert = "INSERT INTO ".$table_name." (position,post_id,parent_id,custom_title,custom_link,custom_description,menu_icon,link_type,menu_id,custom_anchor_title) "."VALUES ( '".$counter."','".$post->ID."','".$parent_id."','".$insert_title."','".get_permalink($post->ID)."','','','page','1','".$insert_title."')";
	  					//$results = $wpdb->query( $insert );
	  					$results = $wpdb->insert( $table_name, array( 'position' => $counter, 'post_id' => $post->ID, 'parent_id' => $parent_id, 'custom_title' => $str_converted, 'custom_link' => get_permalink($post->ID), 'custom_description' => '', 'menu_icon' => '', 'link_type' => 'page', 'menu_id' => '1', 'custom_anchor_title' => $str_converted ));
	  					
	  					$counter++;
	 
		 				//$counter = get_children_menu_elements($post->ID, $counter, $post->ID, 'pages',1,$table_name); 
		 				$counter = get_children_menu_elements($post->ID, $counter, $post->post_parent, 'pages',1,$table_name);
 					}
 					//Do nothing
 					else
 					{
 					
 					}
 				}  			
  				
  				//GET all current categories
  				$category_args = array(
					'type'                     => 'post',
					'child_of'                 => 0,
					'orderby'                  => 'name',
					'order'                    => 'ASC',
					'hide_empty'               => false,
					'include_last_update_time' => false,
					'hierarchical'             => 0,
					'parent'             		=> 0,
					'depth'						=> 1,
					'exclude'                  => '',
					'include'                  => '',
					'number'                   => '',
					'pad_counts'               => false );
				
				
				$categories_array = get_categories($category_args);
		
  				//POPULATE with second menu
				$insert = "INSERT INTO ".$table_name_menus." (menu_name) "."VALUES ( 'Woo Menu 2')";
  				$results = $wpdb->query( $insert );

				//POPULATE with second menu content
  				//GET all current pages
					
				$counter = 1;
				
  				//GET all current categories
					
				//INSERT Loop
				foreach ($categories_array as $cat_item) {
					
					//CHECK if is top level element
					if ($cat_item->parent == 0)
					{
						//CHECK for existing category records
						$table_name_parent = $wpdb->prefix . "woo_custom_nav_records";
						$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name_parent." WHERE post_id='".$cat_item->parent."' AND link_type='category' AND menu_id='2'" );
						
						if ($woo_result > 0 && isset($woo_result[0]->id)) {
							$parent_id = $woo_result[0]->id;
						}
						else {
							$parent_id = 0;
						}
						
						//INSERT category
						//Convert string to UTF-8
						$str_converted = stripslashes($cat_item->cat_name);
						//$insert_title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
						//$insert = "INSERT INTO ".$table_name." (position,post_id,parent_id,custom_title,custom_link,custom_description,menu_icon,link_type,menu_id) "."VALUES ( '".$counter."','".$cat_item->cat_ID."','".$parent_id."','".$insert_title."','".get_category_link($cat_item->cat_ID)."','','','category','2')";
	  					//$results = $wpdb->query( $insert );
	  					$results = $wpdb->insert( $table_name, array( 'position' => $counter, 'post_id' => $cat_item->cat_ID, 'parent_id' => $parent_id, 'custom_title' => $str_converted, 'custom_link' => get_category_link($cat_item->cat_ID), 'custom_description' => '', 'menu_icon' => '', 'link_type' => 'category', 'menu_id' => '2', 'custom_anchor_title' => $str_converted ));
		 
	  					$counter++;
	  					
	  					$counter = get_children_menu_elements($cat_item->cat_ID, $counter, $cat_item->parent, 'categories',2,$table_name); 
 					}
 					//Do nothing
 					else {
 					
 					}
 				}
			}
			
			
			

		}
		
		
	   	
	}

}

function woo_custom_nav_reset() {
	
	global $wpdb;
	
	$table_name = $wpdb->prefix . "woo_custom_nav_records";	 	 
	//DROP existing tables
	$wpdb->query( "DROP TABLE ".$table_name);
	
	$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";	 	 
	//DELETE existing menus
	$wpdb->query( "DROP TABLE ".$table_name_menus);
	
	woo_custom_navigation_setup();
		
	return true;
	
}

function woo_custom_navigation_menu() {

	//Woothemes Custom Navigation Menu	
	$woopage = add_submenu_page( 'woothemes', 'Custom Navigation', 'Custom Navigation', 8, 'custom_navigation', 'woo_custom_navigation' );
	
	add_action( "admin_print_scripts-$woopage", 'woo_custom_nav_scripts' );
	  	
}

function woo_custom_nav_scripts() {

	//STYLES AND JAVASCRIPT
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-draggable' );
	wp_enqueue_script( 'jquery-ui-droppable' );
	wp_enqueue_script( 'jquery-ui-sortable' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_register_script( 'woo-nav-dynamic', get_template_directory_uri() . '/functions/js/custom_menu_dynamic_items.js', array( 'jquery-ui-dialog' ));
	wp_enqueue_script( 'woo-nav-dynamic' );
	wp_register_script( 'woo-nav-initial', get_template_directory_uri() . '/functions/js/custom_menu_initial_items.js', array( 'jquery-ui-dialog' ));
	wp_enqueue_script( 'woo-nav-initial' );
	wp_register_script( 'woo-nav-autocomplete', get_template_directory_uri() . '/functions/js/jquery.autocomplete.js', array( 'jquery' ));
	wp_enqueue_script( 'woo-nav-autocomplete' );
	//Default Style
	echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/functions/css/custom_menu.css" type="text/css" media="all" />';
	
}



/*-----------------------------------------------------------------------------------*/
/* Woothemes Custom Navigation Menu Interface
/* woo_custom_navigation() is the main function for the Custom Navigation
/* See functions in admin-functions.php
/*-----------------------------------------------------------------------------------*/

function woo_custom_navigation() {
	global $wpdb;
	?>

	<div class="wrap">
	<div id="no-js"><h3>You do not have JavaScript enabled in your browser. Please enabled it to access the Custom Menu functionality.</h3></div>
			
	    <?php
	    $messagesdiv = '';
	    $menu_id_in_edit = 0;
	    
	    //Get the theme name
	    $themename =  get_option( 'woo_themename' );
	    
	    //Default Menu to show
	    $table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";	 	 
		$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name_menus." ORDER BY id ASC LIMIT 1" );
 		if ($woo_result > 0 && isset($woo_result[0]->id)) {
			$menu_selected_id = $woo_result[0]->id;
		}
		else {
			$menu_selected_id = 1;
		}
		
		
		//CHECK which menu is selected and if menu is in edit already
		if (isset($_POST['switch_menu'])) {
			//echo $_POST['menu_select'];
			$menu_selected_id = $_POST['menu_select'];
		}
		elseif (isset($_POST['menu_id_in_edit'])){
			$menu_selected_id = $_POST['menu_id_in_edit'];
		}
		else {
		
		}
	    
	    
	    if (isset($_POST['set_woo_menu']))
	    {
	    	update_option( 'woo_custom_nav_menu', $_POST['enable_woo_menu']);
	    	$messagesdiv = '<div id="message" class="updated fade below-h2"><p>'.$themename.'\'s Custom Menu has been updated!</p></div>';
	    } 
	  
	    	    
	    //CHECK for existing woo custom menu
	 	$table_name = $wpdb->prefix . "woo_custom_nav_records";	 	
	 	$custom_nav_exists = $wpdb->query( "SELECT id FROM ".$table_name." WHERE menu_id='".$menu_selected_id."'" );
	    
	    if (isset($_POST['licount'])) {
	    	$postCounter = $_POST['licount'];
	    }
	    else {
	    	$postCounter = 0;
	    }
		
		if (isset($_POST['add_menu'])) {
			
			$table_name_custom_menu = $wpdb->prefix . "woo_custom_nav_menus";
	 		$insert_menu_name = $_POST['add_menu_name'];
	 		
	 		//CHECK for existing woo custom menu
	 		$existing_records = $wpdb->query( "SELECT id FROM ".$table_name_custom_menu." WHERE menu_name='".$insert_menu_name."'" );
	 		
	 		if ($insert_menu_name <> '') {
	 			if ($existing_records > 0) 
	 			{
	 				$messagesdiv = '<div id="message" class="error fade below-h2"><p>'.$insert_menu_name.' Menu has already created - please try another name</p></div>';
	 				//GET reset menu id
 					$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";	 	 
					$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name_menus." ORDER BY id ASC LIMIT 1" );
 					if ($woo_result > 0) {
						$menu_selected_id = $woo_result[0]->id;
						$menu_id_in_edit = $menu_selected_id;
					}
					else {
						$menu_selected_id = 0;
						$menu_selected_id = 0;
					}
	 			}
	 			else 
	 			{
	 				$wpdb->insert( $table_name_custom_menu, array( 'menu_name' => $insert_menu_name ));
	 				$menu_selected_id = $wpdb->insert_id;
	 				$menu_id_in_edit = $menu_selected_id;
	 				$messagesdiv = '<div id="message" class="updated fade below-h2"><p>'.$insert_menu_name.' Menu has been created!</p></div>';	
	 				
	 				$custom_nav_exists = $wpdb->query( "SELECT id FROM ".$table_name." WHERE menu_id='".$menu_selected_id."'" );			
					$postCounter = 0;
	 			}
	 		}
	 		else 
	 		{
	 			$messagesdiv = '<div id="message" class="error fade below-h2"><p>Please enter a valid Menu name</p></div>';
	 		}
	 		
			
		}
		
		if ($postCounter > 0) 
		{
			
			if (isset($_POST['switch_menu'])) {
				
			}
			elseif (isset($_POST['add_menu'])) {	
			
			}
			elseif (isset($_POST['reset_woo_menu'])) {	
	    		$success = woo_custom_nav_reset();
	    		if ($success) {
	    			//DISPLAY SUCCESS MESSAGE IF Menu Reset Correctly
 					$messagesdiv = '<div id="message" class="updated fade below-h2"><p>'.$themename.'\'s Custom Menu has been RESET!</p></div>';
 					//GET reset menu id
 					$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";	 	 
					$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name_menus." ORDER BY id ASC LIMIT 1" );
 					if ($woo_result > 0 && isset($woo_result[0]->id)) {
						$menu_selected_id = $woo_result[0]->id;
					}
					else {
						$menu_selected_id = 0;
					}
	    		}
	    		else {
	    			//DISPLAY SUCCESS MESSAGE IF Menu Reset Correctly
 					$messagesdiv = '<div id="message" class="error fade below-h2"><p>'.$themename.'\'s Custom Menu could not be RESET. Please try again.</p></div>';
	    		}
	    	}
			else {
				
				$menu_id_in_edit = $_POST['menu_id_in_edit'];
				//After POST delete existing records in prep for Insert
				$wpdb->query( "DELETE FROM ".$table_name." WHERE menu_id='".$menu_id_in_edit."'" );
				
				//Loop through all POST variables
 				for ($k = 1;$k<= $postCounter; $k++) {
 					
 					if (isset($_POST['dbid'.$k])) { $db_id = $_POST['dbid'.$k]; } else { $db_id = 0; }
 					if (isset($_POST['postmenu'.$k])) { $post_id = $_POST['postmenu'.$k]; } else { $post_id = 0; }
 					if (isset($_POST['parent'.$k])) { $parent_id = $_POST['parent'.$k]; } else { $parent_id = 0; }
 					if (isset($_POST['title'.$k])) { $custom_title = stripslashes($_POST['title'.$k]); } else { $custom_title = ''; }
 					if (isset($_POST['linkurl'.$k])) { $custom_linkurl = $_POST['linkurl'.$k]; } else { $custom_linkurl = ''; }
 					if (isset($_POST['description'.$k])) { $custom_description = stripslashes($_POST['description'.$k]); } else { $custom_description = ''; }
 					if (isset($_POST['icon'.$k])) { $icon = $_POST['icon'.$k]; } else { $icon = 0; }
 					if (isset($_POST['position'.$k])) { $position = $_POST['position'.$k]; } else { $position = 0; }
 					if (isset($_POST['linktype'.$k])) { $linktype = $_POST['linktype'.$k]; } else { $linktype = 'custom'; }
 					if (isset($_POST['anchortitle'.$k])) { $custom_anchor_title = stripslashes($_POST['anchortitle'.$k]); } else { $custom_anchor_title = $custom_title; }
 					if (isset($_POST['newwindow'.$k])) { $new_window = $_POST['newwindow'.$k]; } else { $new_window = 0; }
 					
 					if ($linktype == '')
 					{
 					
 					}
 					else
 					{
 						//If top level menu item
	 					if ($parent_id == 0)
	 					{
	 						//INSERT menu item record
	 						$wpdb->insert( $table_name, array( 'position' => $position, 'post_id' => $post_id, 'parent_id' => $parent_id, 'custom_title' => $custom_title, 'custom_link' => $custom_linkurl, 'custom_description' => $custom_description, 'menu_icon' => $icon, 'link_type' => $linktype, 'menu_id' => $menu_id_in_edit, 'custom_anchor_title' => $custom_anchor_title, 'new_window' => $new_window )); 	
	 					}
	 					//If not top level menu item
	 					else 
	 					{
	 						//INSERT menu item record
	 						$wpdb->insert( $table_name, array( 'position' => $position, 'post_id' => $post_id, 'parent_id' => '8000', 'custom_title' => $custom_title, 'custom_link' => $custom_linkurl, 'custom_description' => $custom_description, 'menu_icon' => $icon, 'link_type' => $linktype, 'menu_id' => $menu_id_in_edit, 'custom_anchor_title' => $custom_anchor_title, 'new_window' => $new_window  )); 	
	 						$lastid = $wpdb->insert_id;
	 						
	 						//GET the correct parent record
	 						$parentrecords = $wpdb->get_results( "SELECT id FROM ".$table_name." WHERE position='".($parent_id)."' AND menu_id='".$menu_id_in_edit."'" );
	 						
		 					if ($parentrecords > 0)
		 					{
		 						foreach ($parentrecords as $parentrecord)
		 						{
		 							$parent_id_update = $parentrecord->id;
		 						}
		 					}
		 					//UPDATE menu item record with correct parent
		 					$wpdb->update( $table_name, array( 'parent_id' => $parent_id_update ), array( 'id' => $lastid, 'menu_id' => $menu_id_in_edit ));
	 					}
 					}
 				}
 				//DISPLAY SUCCESS MESSAGE IF POST CORRECT
 				$messagesdiv = '<div id="message" class="updated fade below-h2"><p>'.$themename.'\'s Custom Menu has been updated!</p></div>';
	 				
 			}
		}
		else {
			if (isset($_POST['reset_woo_menu'])) {	
	    		$success = woo_custom_nav_reset();
	    		if ($success) {
	    			//DISPLAY SUCCESS MESSAGE IF Menu Reset Correctly
 					$messagesdiv = '<div id="message" class="updated fade below-h2"><p>'.$themename.'\'s Custom Menu has been RESET!</p></div>';
 					//GET reset menu id
 					$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";	 	 
					$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name_menus." ORDER BY id ASC LIMIT 1" );
 					if ($woo_result > 0 && isset($woo_result[0]->id)) {
						$menu_selected_id = $woo_result[0]->id;
					}
					else {
						$menu_selected_id = 0;
					}
	    		}
	    		else {
	    			//DISPLAY SUCCESS MESSAGE IF Menu Reset Correctly
 					$messagesdiv = '<div id="message" class="error fade below-h2"><p>'.$themename.'\'s Custom Menu could not be RESET. Please try again.</p></div>';
	    		}
	    	}
		}
 		
 		//DISPLAY Custom Navigation
 		?>
		<div id="pages-left">
			<div class="inside">
			<h2 class="maintitle"><img class="logo" src="<?php echo get_template_directory_uri(); ?>/functions/images/logo.png" alt="Woothemes" />Custom Navigation</h2>
			<?php
				
				//CHECK if custom menu has been enabled
				$enabled_menu = get_option( 'woo_custom_nav_menu' );
			    $checked = strtolower($enabled_menu);
	
				if ($checked == 'true') {
				} else {
					echo '<div id="message-enabled" class="error fade below-h2"><p><strong>The Custom Menu has not been Enabled yet. Please enable it in order to use it --------></strong></p></div>';
				}
				//Notify users that they can use 3.0 Menus instead
				if ( function_exists( 'wp_nav_menu') ) {
					echo '<div id="message-enabled" class="updated fade below-h2"><p><strong>You have WordPress 3.0.x installed! </strong></p><p>We suggest that you use the <a href="'.admin_url().'nav-menus.php">WordPress Menu Management system</a> instead of the Custom Navigation.</p></div>';
				}
				
			?>
			<?php echo $messagesdiv; ?>
			<form onsubmit="updatepostdata()" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"  enctype="multipart/form-data">
			
			<input type="hidden" name="licount" id="licount" value="0" />
			<input type="hidden" name="menu_id_in_edit" id="menu_id_in_edit" value="<?php echo $menu_selected_id; ?>" />
			
			<div class="sidebar-name">
			
				<div class="sidebar-name-arrow">
					<br/>
				</div>
				<?php 		
				
				//CHECK for existing woo custom menu	    		
	 			$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";
	 			if ($menu_id_in_edit > 0)
	 			{
	 				$custom_menu_name = $wpdb->get_results( "SELECT menu_name FROM ".$table_name_menus." WHERE id='".$menu_id_in_edit."'" );
	 			}
	 			else {
	 				$custom_menu_name = $wpdb->get_results( "SELECT menu_name FROM ".$table_name_menus." WHERE id='".$menu_selected_id."'" );
	 			}
	 			
	 			//Menu title 
	 			if (isset($custom_menu_name[0]->menu_name)) {
	 				$menu_title = $custom_menu_name[0]->menu_name;
	 			}
	 			else {
	 				$menu_title = '';
	 			}
	    		?>
				<h3><?php echo $menu_title; ?></h3>
				
			</div>
			
			<div id="nav-container">
				<ul id="custom-nav">
				
					<?php
						//DISPLAY existing menu
						if (($custom_nav_exists) > 0 || ($custom_menu_name > 0)) 
						{
							//SET output type
							$output_type = "backend";
							//Outputs menu	
							if (isset($custom_menu_name[0]->menu_name)) {
								$menu_name = $custom_menu_name[0]->menu_name;
							}
							else {
								$menu_name = '';
							}
							
							if ($menu_id_in_edit > 0)
							{
								//MAIN OUTPUT FUNCTION
								woo_custom_navigation_output( 'type='.$output_type.'&name='.$menu_name.'&id='.$menu_id_in_edit);
							}
							else 
							{
								//MAIN OUTPUT FUNCTION
								woo_custom_navigation_output( 'type='.$output_type.'&name='.$menu_name.'&id='.$menu_selected_id);
							}
							
						}
						//DISPLAY default menu
						else 
						{	
							//Outputs default Pages
							$intCounter = woo_get_pages(1,'menu' );			
							//Outputs default Categories
							$intCounter = woo_get_categories($intCounter,'menu' );
						}				
					?>
				
				</ul>
			</div><!-- /#nav-container -->
			
			<p class="submit">
			
			<script type="text/javascript">
				updatepostdata();       		
			</script>
			
			<input id="save_bottom" name="save_bottom" type="submit" value="Save All Changes" /></p>
			</div><!-- /.inside -->
		</div>
		
		<div id="menu-right">
		
			<h2 class="heading">Options</h2>
			
			<div class="widgets-holder-wrap">
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"></div>
					<h3>Setup Custom Menu</h3>
				</div>
				<div class="widget-holder">
						
					<?php
			    	
			    	//SETUP Woo Custom Menu
			    	
					$enabled_menu = get_option( 'woo_custom_nav_menu' );
					   
			    	$checked = strtolower($enabled_menu);
    	
			    	?>
			    	
			    	<span >
			    		<label>Enable</label><input type="radio" name="enable_woo_menu" value="true" <?php if ($checked=='true') { echo 'checked="checked"'; } ?> />
			    		<label>Disable</label><input type="radio" name="enable_woo_menu" value="false" <?php if ($checked=='true') { } else { echo 'checked="checked"'; } ?> />
					</span><!-- /.checkboxes -->				
						
					<input id="set_woo_menu" type="submit" value="Set Menu" name="set_woo_menu" class="button" /><br />
					
					<span>
						<label>Reset Menu to Default</label>
						<input id="reset_woo_menu" type="submit" value="Reset" name="reset_woo_menu" class="button" onclick="return confirm( 'Are you sure you want to RESET the Custom Navigation Menu to its Default Settings?' );" />
					</span>
					
					<div class="fix"></div>
				</div>
			</div><!-- /.widgets-holder-wrap -->
			
			<div class="widgets-holder-wrap">
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"></div>
					<h3>Menu Selector</h3>
				</div>
				<div class="widget-holder">
						
					<?php
					
					//Get Menu Items for SELECT OPTIONS 	
					$table_name_custom_menus = $wpdb->prefix . "woo_custom_nav_menus";
	 				$custom_menu_records = $wpdb->get_results( "SELECT id,menu_name FROM ".$table_name_custom_menus);
	 			
	    			?>
				
					<select id="menu_select" name="menu_select">
						<?php 
						
						//DISPLAY SELECT OPTIONS
						foreach ($custom_menu_records as $custom_menu_record)
						{
							if (($menu_id_in_edit == $custom_menu_record->id) || ($menu_selected_id == $custom_menu_record->id)) {
								$selected_option = 'selected="selected"';
							}
							else {
								$selected_option = '';
							}
							?>
							<option value="<?php echo $custom_menu_record->id; ?>" <?php echo $selected_option; ?>><?php echo $custom_menu_record->menu_name; ?></option>
							<?php
							
						}
						?>
					</select>
					
					<input id="switch_menu" type="submit" value="Switch" name="switch_menu" class="button" />
					<input id="add_menu_name" name="add_menu_name" type="text" value=""  />
					<input id="add_menu" type="submit" value="Add Menu" name="add_menu" class="button" />
						
					<div class="fix"></div>
				</div>
			</div><!-- /.widgets-holder-wrap -->
			<?php $advanced_option_descriptions = get_option( 'woo_settings_custom_nav_advanced_options' ); ?>
			<div class="widgets-holder-wrap" style="display:none;">
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"></div>
					<h3>Top Level Menu Descriptions</h3>
				</div>
				<div class="widget-holder">	
					<span>Display Descriptions in Top Level Menu?</span>
			
					<?php
			    	
			    	//UPDATE and DISPLAY Menu Description Option
			    	if (isset($_POST['menu-descriptions']))
			    	{
			    		
						if (isset($_POST['switch_menu'])) {
							
						}
						else {
							$menu_options_to_edit = $_POST['menu_id_in_edit'];
			    			update_option( 'woo_settings_custom_nav_'.$menu_options_to_edit.'_descriptions',$_POST['menu-descriptions']);	
						}
			    		
			    	}
			    	
			    	if ($menu_id_in_edit > 0)
					{
						$checkedraw = get_option( 'woo_settings_custom_nav_'.$menu_id_in_edit.'_descriptions' );
					}
					else {
						$checkedraw = get_option( 'woo_settings_custom_nav_'.$menu_selected_id.'_descriptions' );
					}
			    
			    	$checked = strtolower($checkedraw);
			    	
			    	if ($advanced_option_descriptions == 'no')
			    	{
			    		$checked = 'no';
			    	}
			    	
			    	?>
			    	
			    	<span class="checkboxes">
			    		<label>Yes</label><input type="radio" name="menu-descriptions" value="yes" <?php if ($checked=='yes') { echo 'checked="checked"'; } ?> />
			    		<label>No</label><input type="radio" name="menu-descriptions" value="no" <?php if ($checked=='yes') { } else { echo 'checked="checked"'; } ?> />
					</span><!-- /.checkboxes -->
			    	</form>
					<div class="fix"></div>
				</div>
			</div><!-- /.widgets-holder-wrap -->
			
			<div class="widgets-holder-wrap">
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"></div>
					<h3>Add an Existing Page</h3>
				</div>
				<div class="widget-holder">
					
					<?php
					
					$pages_args = array(
		    		'child_of' => 0,
					'sort_order' => 'ASC',
					'sort_column' => 'post_title',
					'hierarchical' => 1,
					'exclude' => '',
					'include' => '',
					'meta_key' => '',
					'meta_value' => '',
					'authors' => '',
					'parent' => -1,
					'exclude_tree' => '',
					'number' => '',
					'offset' => 0 );
	
					//GET all pages		
					$pages_array = get_pages($pages_args);
					$page_name = '';
					//CHECK if pages exist
					if ($pages_array)
					{
						foreach ($pages_array as $post)
						{
							//Convert string to UTF-8
							$str_converted = woo_encoding_convert($post->post_title);
							//Add page name to 
							$page_name .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').'|';
						}
					}
					else
					{
						$page_name = "No pages available";
					}
						
					?>
					
					<script>
  						jQuery(document).ready(function($) {

							//GET PHP pages
    						var dataposts = "<?php echo $page_name; ?>".split( "|" );
						
							//Set autocomplete
							$( "#page-search").autocomplete(dataposts);
						
							//Handle autocomplete result
							$( "#page-search").result(function(event, data, formatted) {
    							$( '#existing-pages').css( 'display','block' );
    							$( "#existing-pages dt:contains( '" + data + "')").css( "display", "block" );
    						
    							$( '#show-pages').hide();
    							$( '#hide-pages').show();
    						
							});
							$( '#existing-pages').css( 'display','none' );
 						});
  					</script>


					<input type="text" onfocus="jQuery( '#page-search').attr( 'value','' );" id="page-search" value="Search Pages" /> 
					
					<a id="show-pages" style="cursor:pointer;" onclick="jQuery( '#existing-pages').css( 'display','block' );jQuery( '#page-search').attr( 'value','' );jQuery( '#existing-pages dt').css( 'display','block' );jQuery( '#show-pages').hide();jQuery( '#hide-pages').show();">View All</a> 
					<a id="hide-pages" style="cursor:pointer;" onclick="jQuery( '#existing-pages').css( 'display','none' );jQuery( '#page-search').attr( 'value','Search Pages' );jQuery( '#existing-pages dt').css( 'display','none' );jQuery( '#show-pages').show();jQuery( '#hide-pages').hide();">Hide All</a>
					
					<script type="text/javascript">
					
						jQuery( '#hide-pages').hide();
					
					</script>
					
					<ul id="existing-pages" class="list">
						<?php
							$intCounter = 0;
							//Get default Pages
							$intCounter = woo_get_pages($intCounter,'default' );
						?>
					</ul>
					
					<div class="fix"></div>
					
				</div>
			</div><!-- /.widgets-holder-wrap -->
			
			<div class="widgets-holder-wrap">
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"></div>
					<h3>Add an Existing Category</h3>
				</div>
				<div class="widget-holder">
					
					<?php
					
					//Custom GET categories query
					$categories = $wpdb->get_results( "SELECT term_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'category' ORDER BY term_id ASC" );
					$cat_name = '';
					//CHECK for results
					if ($categories)
					{
						foreach($categories as $category) 
						{ 
							$cat_id = $category->term_id;
				
							$cat_args=array(
							 	'orderby' => 'name',
							  	'include' => $cat_id,
							  	'hierarchical' => 1,
						  		'order' => 'ASC'
				  			);
				  			
				  			$category_names=get_categories($cat_args);
							
							if (isset($category_names[0]->name))
							{
								//Convert string to UTF-8
								$str_converted = woo_encoding_convert($category_names[0]->name);
								//Add category name to data string
								$cat_name .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').'|';
							}
				  		}
				  	}
				  	else
					{
						$cat_name = "No categories available";
					}
				  
					?>

					<script>
  						jQuery(document).ready(function($) {

							//GET PHP categories
    						var datacats = "<?php echo $cat_name; ?>".split( "|" );
							
							//Set autocomplete
							$( "#cat-search").autocomplete(datacats);
						
							//Handle autocomplete result
							$( "#cat-search").result(function(event, data, formatted) {
    							$( '#existing-categories').css( 'display','block' );
    							$( "#existing-categories dt:contains( '" + data + "')").css( "display", "block" );
    						   						
    							$( '#show-cats').hide();
    							$( '#hide-cats').show();
    						
							});
							$( '#existing-categories').css( 'display','none' );
					
 						});
  					</script>


					<input type="text" onfocus="jQuery( '#cat-search').attr( 'value','' );" id="cat-search" value="Search Categories" /> 
					
					<a id="show-cats" style="cursor:pointer;" onclick="jQuery( '#existing-categories').css( 'display','block' );jQuery( '#cat-search').attr( 'value','' );jQuery( '#existing-categories dt').css( 'display','block' );jQuery( '#show-cats').hide();jQuery( '#hide-cats').show();">View All</a> 
					<a id="hide-cats" style="cursor:pointer;" onclick="jQuery( '#existing-categories').css( 'display','none' );jQuery( '#cat-search').attr( 'value','Search Categories' );jQuery( '#existing-categories dt').css( 'display','none' );jQuery( '#show-cats').show();jQuery( '#hide-cats').hide();">Hide All</a>
					
					<script type="text/javascript">
					
						jQuery( '#hide-cats').hide();
					
					</script>
					
					<ul id="existing-categories" class="list">
            			<?php
						 	//Get default Categories
            				$intCounter = woo_get_categories($intCounter,'default' ); 				
						?>
       				</ul>
       				
       				<div class="fix"></div>
					
				</div>
			</div><!-- /.widgets-holder-wrap -->
			
			<div class="widgets-holder-wrap">
				<div class="sidebar-name">
					<div class="sidebar-name-arrow"></div>
					<h3>Add a Custom Url</h3>
				</div>
				<div class="widget-holder">
					<input id="custom_menu_item_url" type="text" value="http://"  />
					<label>URL</label><br />
           			<?php $templatedir = get_template_directory_uri(); ?>
            		<input type="hidden" id="templatedir" value="<?php echo $templatedir; ?>" />
            		<input id="custom_menu_item_name" type="text" value="Menu Item" onfocus="jQuery( '#custom_menu_item_name').attr( 'value','' );"  />
            		<label>Menu Text</label><br />
           			<input id="custom_menu_item_description" type="text" value="A description" <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> onfocus="jQuery( '#custom_menu_item_description').attr( 'value','' );" />
           			<label <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> >Description</label>
           			<a class="addtomenu" onclick="appendToList( '<?php echo $templatedir; ?>','Custom','','','','0','' );jQuery( '#custom_menu_item_name').attr( 'value','Menu Item' );jQuery( '#custom_menu_item_description').attr( 'value','A description' );">Add to menu</a>
					<div class="fix"></div>
				</div>
			</div><!-- /.widgets-holder-wrap -->
			
       </div>
    </div>
    
    <script type="text/javascript">
		document.getElementById( 'pages-left').style.display='block';
		document.getElementById( 'menu-right').style.display='block';
		document.getElementById( 'no-js').style.display='none';
	</script>
	
	<div id="dialog-confirm" title="Edit Menu Item">
		</label><input id="edittitle" type="text" name="edittitle" value="" /><label class="editlabel" for="edittitle">Menu Title</label><br />
		<input id="editlink" type="text" name="editlink" value="" /><label class="editlabel" for="editlink">URL</label><br />
		<input id="editanchortitle" type="text" name="editanchortitle" value="" /><label class="editlabel" for="editanchortitle" >Link Title</label><br />
		<select id="editnewwindow" name="editnewwindow">
			<option value="1" >Yes</option>
			<option value="0" >No</option>
		</select><label class="editlabel" for="editnewwindow" >Open Link in a New Window</label>
		<input id="editdescription" type="text" name="editdescription" value="" <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> /><label class="editlabel" for="editdescription" <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?> >Description</label><br />
	</div>

<?php

}




/*-----------------------------------------------------------------------------------*/
/* WooThemes Custom Navigation Functions */
/* woo_custom_navigation_output() displays the menu in the back/frontend
/* woo_custom_navigation_sub_items() is a recursive sub menu item function
/* woo_get_pages()
/* woo_get_categories()
/* woo_custom_navigation_default_sub_items() is a recursive sub menu item function
/*-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Main Output Function
/* args list
/* type - frontend or backend
/* name - name of your menu
/* id - id of menu in db
/* desc - 1 = show descriptions, 2 = dont show descriptions
/* before_title - html before title is outputted in <a> tag
/* after_title - html after title is outputted in <a> tag
/*-----------------------------------------------------------------------------------*/
function woo_custom_navigation_output($args = array()) {
		
		//DEFAULT ARGS
		$type = 'frontend';
		$name = 'Woo Menu 1'; 
		$id = 0;
		$desc = 2;
		$before_title = '';
		$after_title = '';
		$depth = 0;
		
		if (isset($args)) {
		
			if ( !is_array($args) ) 
			parse_str( $args, $args );
	
			extract($args);
		}
		
		global $wpdb;
		$woo_custom_nav_menu_id = 0;
		$table_name = $wpdb->prefix . "woo_custom_nav_records";
		
		//Override for menu descriptions
		$advanced_option_descriptions = get_option( 'woo_settings_custom_nav_advanced_options' );
		if ($advanced_option_descriptions == 'no')
		{
			$desc = 2;
		}
		
		//GET Menu Items
		//FRONTEND
		if ($type == "frontend") 
		{
			$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";
			if ($id > 0) {
				$woo_custom_nav_menu_id = $id;
			}
			else {
				$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name_menus." WHERE menu_name='".$name."'" );
				$woo_custom_nav_menu_id = $woo_result[0]->id;
			}
			
			$woo_custom_nav_menu = $wpdb->get_results( "SELECT id,post_id,parent_id,position,custom_title,custom_link,custom_description,menu_icon,link_type,custom_anchor_title,new_window FROM ".$table_name." WHERE parent_id = '0' AND menu_id='".$woo_custom_nav_menu_id."' ORDER BY position ASC" );
		}
		//BACKEND
		else {
			$woo_custom_nav_menu = $wpdb->get_results( "SELECT id,post_id,parent_id,position,custom_title,custom_link,custom_description,menu_icon,link_type,custom_anchor_title,new_window FROM ".$table_name." WHERE parent_id = '0' AND menu_id='".$id."' ORDER BY position ASC" );
		}
		$queried_id = 0;
		$type_settings = 'custom';
		global $wp_query;
	    if (is_page()) {
	    	$queried_id = $wp_query->post->ID;
	    	$type_settings = 'page';
	    }
	    elseif (is_category()) {
	    	$queried_id = $wp_query->query_vars['cat'];
	    	$type_settings = 'category';
	    }
	    else {
	    }
	    //DISPLAY Loop
		foreach ($woo_custom_nav_menu as $woo_custom_nav_menu_items) {
			
			//PREPARE Menu Data
			//Page Menu Item
			if ($woo_custom_nav_menu_items->link_type == 'page')
			{
				if ($woo_custom_nav_menu_items->custom_link == '') {
					$link = get_permalink($woo_custom_nav_menu_items->post_id);
				}
				else {
					$link = $woo_custom_nav_menu_items->custom_link;
				}

				if ($woo_custom_nav_menu_items->custom_title == '') {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert(get_the_title($woo_custom_nav_menu_items->post_id));
					$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				}
				else {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($woo_custom_nav_menu_items->custom_title);
					$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				}

				if ($woo_custom_nav_menu_items->custom_description == '') {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert(get_post_meta($woo_custom_nav_menu_items->post_id, 'page-description', true));
					$description = htmlspecialchars(trim($str_converted), ENT_QUOTES, 'UTF-8' );
				}
				else {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($woo_custom_nav_menu_items->custom_description);
					$description = htmlspecialchars(trim($str_converted), ENT_QUOTES, 'UTF-8' );
				}
				$target = '';
			}
			//Category Menu Item
			elseif ($woo_custom_nav_menu_items->link_type == 'category') 
			{
				
				if ($woo_custom_nav_menu_items->custom_link == '') {
					$link = get_category_link($woo_custom_nav_menu_items->post_id);
				}
				else {
					$link = $woo_custom_nav_menu_items->custom_link;
				}
				
				if ($woo_custom_nav_menu_items->custom_title == '') {
					$title_raw = get_categories( 'include='.$woo_custom_nav_menu_items->post_id);
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($title_raw[0]->cat_name);
					$title =  htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );	
				}
				else {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($woo_custom_nav_menu_items->custom_title);
					$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				}
				
				if ($woo_custom_nav_menu_items->custom_description == '') {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert(category_description($woo_custom_nav_menu_items->post_id));
					$description = htmlspecialchars(strip_tags(trim($str_converted)), ENT_QUOTES, 'UTF-8' );
				}
				else {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($woo_custom_nav_menu_items->custom_description);
					$description = htmlspecialchars(trim($str_converted), ENT_QUOTES, 'UTF-8' );
				}
				$target = '';
				
			}
			//Custom Menu Item
			else 
			{
				$link = $woo_custom_nav_menu_items->custom_link;
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($woo_custom_nav_menu_items->custom_title);
				$title =  htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($woo_custom_nav_menu_items->custom_description);
				$description = htmlspecialchars(trim($str_converted), ENT_QUOTES, 'UTF-8' );
				$target = 'target="_blank"';
			}
			
			//SET anchor title
			if (isset($woo_custom_nav_menu_items->custom_anchor_title)) {
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($woo_custom_nav_menu_items->custom_anchor_title);
				$anchor_title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
			}
			else {
				$anchor_title = $title;
			}
			
			//SET URL protocol
			if (isset($_SERVER['HTTPS'])) {
				if ($_SERVER['HTTPS'] == 'on') {
					$protocol =  'https';
				}
				else {
					$protocol =  'http';
				}
			}
			else {
				$protocol =  'http';
			}
			$full_web_address = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			if (($queried_id == $woo_custom_nav_menu_items->post_id) && ($queried_id != 0) && ($type_settings == $woo_custom_nav_menu_items->link_type) ) {
				$li_class = 'class="current_page_item"';
			}
			else if (($woo_custom_nav_menu_items->custom_link == $full_web_address) && ($queried_id == 0) && ($type_settings == $woo_custom_nav_menu_items->link_type) ) {
				$li_class = 'class="current_page_item"';
			}
			else if (woo_child_is_current($woo_custom_nav_menu_items->id, $woo_custom_nav_menu_id, $table_name, $queried_id, $type_settings, $full_web_address)) {
                $li_class = 'class="current_page_parent"';
            }
			else {
				$li_class = '';
			}
			
			if (isset($woo_custom_nav_menu_items->new_window)) {
				if ($woo_custom_nav_menu_items->new_window > 0) {
					$target = 'target="_blank"';
				}
				else {
					$target = '';
				}
			}
			
			//List Items
			?><?php 
				
					//FRONTEND Link
					if ($type == "frontend")
					{	        
						?><li <?php echo $li_class; ?>><a title="<?php echo $anchor_title; ?>" href="<?php echo $link; ?>" <?php echo $target; ?>><?php echo stripslashes($before_title).$title.stripslashes($after_title); ?><?php 
						
							if ( $advanced_option_descriptions == 'no' ) 
							{ 
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if (($desc == 1) || ($desc == 0) )
								{
									?><span class="nav-description"><?php echo $description; ?></span><?php
								} 
								elseif ($desc == 2)
								{ }
								else
								{ }
							} 
							else 
							{
								// 2 widget override do NOT display descriptions
								// 1 widget override display descriptions
								// 0 widget override not set
								if ($desc == 1)
								{
									?><span class="nav-description"><?php echo $description; ?></span><?php
								} 
								elseif (($desc == 2) || ($desc == 0))
								{ }
								else 
								{ }
							}
							
						?></a><?php 
					}
					//BACKEND draggable and droppable elements
					elseif ($type == "backend")
					{
						?>
						<li id="menu-<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $woo_custom_nav_menu_items->position; ?>" <?php echo $li_class; ?>>
						<dl>
							<dt>
								<span class="title"><?php echo $title; ?></span>
								<span class="controls">
								<span class="type"><?php echo $woo_custom_nav_menu_items->link_type; ?></span>
								<a id="edit<?php echo $woo_custom_nav_menu_items->position; ?>" onclick="edititem(<?php echo $woo_custom_nav_menu_items->position; ?>)" value="<?php echo $woo_custom_nav_menu_items->position; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-edit.png" /></a> 
								<a id="remove<?php echo $woo_custom_nav_menu_items->position; ?>" onclick="removeitem(<?php echo $woo_custom_nav_menu_items->position; ?>)" value="<?php echo $woo_custom_nav_menu_items->position; ?>"><img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-close.png" /></a>
								<a id="view<?php echo $woo_custom_nav_menu_items->position; ?>" target="_blank" href="<?php echo $link; ?>"><img alt="View Page" title="View Page" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-viewpage.png" /></a>
								</span>
							</dt>
						</dl>
						
						<a><span class=""></span></a>
						<input type="hidden" name="dbid<?php echo $woo_custom_nav_menu_items->position; ?>" id="dbid<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $woo_custom_nav_menu_items->id; ?>" />
						<input type="hidden" name="postmenu<?php echo $woo_custom_nav_menu_items->position; ?>" id="postmenu<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $woo_custom_nav_menu_items->post_id; ?>" />
						<input type="hidden" name="parent<?php echo $woo_custom_nav_menu_items->position; ?>" id="parent<?php echo $woo_custom_nav_menu_items->position; ?>" value="0" />
						<input type="hidden" name="title<?php echo $woo_custom_nav_menu_items->position; ?>" id="title<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $title; ?>" />
						<input type="hidden" name="linkurl<?php echo $woo_custom_nav_menu_items->position; ?>" id="linkurl<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $link; ?>" />
						<input type="hidden" name="description<?php echo $woo_custom_nav_menu_items->position; ?>" id="description<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $description; ?>" />
						<input type="hidden" name="icon<?php echo $woo_custom_nav_menu_items->position; ?>" id="icon<?php echo $woo_custom_nav_menu_items->position; ?>" value="0" />
						<input type="hidden" name="position<?php echo $woo_custom_nav_menu_items->position; ?>" id="position<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $woo_custom_nav_menu_items->position; ?>" />
						<input type="hidden" name="linktype<?php echo $woo_custom_nav_menu_items->position; ?>" id="linktype<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $woo_custom_nav_menu_items->link_type; ?>" />
						<input type="hidden" name="anchortitle<?php echo $woo_custom_nav_menu_items->position; ?>" id="anchortitle<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $anchor_title; ?>" />
						<input type="hidden" name="newwindow<?php echo $woo_custom_nav_menu_items->position; ?>" id="newwindow<?php echo $woo_custom_nav_menu_items->position; ?>" value="<?php echo $woo_custom_nav_menu_items->new_window; ?>" />
						
						<?php 
					}
					
					//DISPLAY menu sub items 
					if ($woo_custom_nav_menu_items->parent_id == 0) 
					{
						//FRONTEND
						if ($type == 'frontend') 
						{
							//Recursive function
							if ( ($depth == 0) || ($depth > 1) ) {
								$intj = woo_custom_navigation_sub_items($woo_custom_nav_menu_items->id,$woo_custom_nav_menu_items->link_type,$table_name,$type,$woo_custom_nav_menu_id, $depth, 1);
							}
						}
						//BACKEND
						else 
						{
							//Recursive function
							$intj = woo_custom_navigation_sub_items($woo_custom_nav_menu_items->id,$woo_custom_nav_menu_items->link_type,$table_name,$type,$id);
						}
					}
					else 
					{
						
					}
			?></li>
			<?php 
		}
}

//RECURSIVE Sub Menu Items
function woo_custom_navigation_sub_items($post_id,$type,$table_name,$output_type,$menu_id = 0,$depth = 0,$depth_counter = 0) {
	
	$depth_counter = $depth_counter + 1;
	$parent_id = 0;
	global $wpdb;
	
	//GET sub menu items
	$woo_custom_nav_menu = $wpdb->get_results( "SELECT id,post_id,parent_id,position,custom_title,custom_link,custom_description,menu_icon,link_type,custom_anchor_title,new_window FROM ".$table_name." WHERE parent_id = '".$post_id."' AND menu_id='".$menu_id."' ORDER BY position ASC" );
	
	if (empty($woo_custom_nav_menu))
	{
	
	}
	else
	{
		?><ul <?php if ($output_type == "backend") { ?> id="sub-custom-nav" <?php } ?> >
		<?php
    	$queried_id = 0;
    	$type_settings = 'custom';
		global $wp_query;
        if (is_page()) {
	    	$queried_id = $wp_query->post->ID;
	    	$type_settings = 'page';
	    }
	    elseif (is_category()) {
	    	$queried_id = $wp_query->query_vars['cat'];
	    	$type_settings = 'category';
	    }
	    else {

	    }
	    //DISPLAY Loop
		foreach ($woo_custom_nav_menu as $sub_item) 
		{
			//Figure out where the menu item sits
			$counter=$sub_item->position;
			
			//Prepare Menu Data
			//Category Menu Item
			if ($sub_item->link_type == 'category') 
			{
				
				$parent_id = $sub_item->parent_id;
				$post_id = $sub_item->post_id;
				
				if ($sub_item->custom_link == '') {
					$link = get_category_link($sub_item->post_id);
				}
				else {
					$link = $sub_item->custom_link;
				}
				
				if ($sub_item->custom_title == '') {
					$title_raw = get_categories( 'include='.$sub_item->post_id);
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($title_raw[0]->cat_name);
					$title =  htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				}
				else {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($sub_item->custom_title);
					$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				}
				
				if ($sub_item->custom_description == '') {
					$description = strip_tags(trim(category_description($sub_item->post_id)));
				}
				else {
					$description = trim($sub_item->custom_description);
				}
				$target = '';
			}
			//Page Menu Item
			elseif ($sub_item->link_type == 'page')
			{
				
				$parent_id = $sub_item->parent_id;
				$post_id = $sub_item->post_id;
				
				if ($sub_item->custom_link == '') {
					$link = get_permalink($sub_item->post_id);
				}
				else {
					$link = $sub_item->custom_link;
				}

				if ($sub_item->custom_title == '') {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert(get_the_title($sub_item->post_id));
					$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				}
				else {
					//Convert string to UTF-8
					$str_converted = woo_encoding_convert($sub_item->custom_title);
					$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				}
				
				if ($sub_item->custom_description == '') {
					$description = trim(get_post_meta($sub_item->post_id, 'page-description', true));
				}
				else {
					$description = trim($sub_item->custom_description);
				}
				$target = '';
				
			}
			//Custom Menu Item
			else
			{
				$link = $sub_item->custom_link;
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($sub_item->custom_title);
				$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				$parent_id = $sub_item->parent_id;
				$post_id = $sub_item->post_id;
				$description = trim($sub_item->custom_description);
				$target = 'target="_blank"';
			}
			
			//SET URL protocol
			if (isset($_SERVER['HTTPS'])) {
				if ($_SERVER['HTTPS'] == 'on') {
					$protocol =  'https';
				}
				else {
					$protocol =  'http';
				}
			}
			else {
				$protocol =  'http';
			}
			$full_web_address = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			
			if (($queried_id == $sub_item->post_id) && ($queried_id != 0) && ($type_settings == $sub_item->link_type)) {
				$li_class = 'class="current_page_item"';
			}
			else if (($sub_item->custom_link == $full_web_address) && ($queried_id == 0) && ($type_settings == $sub_item->link_type) ) {
				$li_class = 'class="current_page_item"';
			}
			else if (woo_child_is_current($sub_item->id, $menu_id, $table_name, $queried_id, $type_settings, $full_web_address)) {
                $li_class = 'class="current_page_parent"';
		    }
			else {
				$li_class = '';
			}
			
			//SET anchor title
			if (isset($sub_item->custom_anchor_title)) {
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($sub_item->custom_anchor_title);
				$anchor_title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
			}
			else {
				$anchor_title = $title;
			}
			
			if (isset($sub_item->new_window)) {
				if ($sub_item->new_window > 0) {
					$target = 'target="_blank"';
				}
				else {
					$target = '';
				}
			}
			
			//List Items
			?><?php 
						//FRONTEND
						if ($output_type == "frontend")
						{
							?><li <?php echo $li_class; ?>><a title="<?php echo $anchor_title; ?>" href="<?php echo $link; ?>" <?php echo $target; ?>><?php echo $title; ?></a><?php 
						}
						//BACKEND
						elseif ($output_type == "backend")
						{
							?>
							<li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>" <?php echo $li_class; ?>>
							<dl>
							<dt>
								<span class="title"><?php echo $title; ?></span>
								<span class="controls">
								<span class="type"><?php echo $sub_item->link_type; ?></span>
								<a id="edit<?php echo $counter; ?>" onclick="edititem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-edit.png" /></a> 
								<a id="remove<?php echo $counter; ?>" onclick="removeitem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>"><img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-close.png" /></a>
								<a id="view<?php echo $counter; ?>" target="_blank" href="<?php echo $link; ?>"><img alt="View Page" title="View Page" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-viewpage.png" /></a>
								</span>
							</dt>
							</dl>
							<a class="hide" href="<?php echo $link; ?>"><?php echo $title; ?></a>
							<input type="hidden" name="dbid<?php echo $counter; ?>" id="dbid<?php echo $counter; ?>" value="<?php echo $sub_item->id; ?>" />
							<input type="hidden" name="postmenu<?php echo $counter; ?>" id="postmenu<?php echo $counter; ?>" value="<?php echo $post_id; ?>" />
							<input type="hidden" name="parent<?php echo $counter; ?>" id="parent<?php echo $counter; ?>" value="<?php echo $parent_id; ?>" />
							<input type="hidden" name="title<?php echo $counter; ?>" id="title<?php echo $counter; ?>" value="<?php echo $title; ?>" />
							<input type="hidden" name="linkurl<?php echo $counter; ?>" id="linkurl<?php echo $counter; ?>" value="<?php echo $link; ?>" />
							<input type="hidden" name="description<?php echo $counter; ?>" id="description<?php echo $counter; ?>" value="<?php echo $description; ?>" />
							<input type="hidden" name="icon<?php echo $counter; ?>" id="icon<?php echo $counter; ?>" value="0" />
							<input type="hidden" name="position<?php echo $counter; ?>" id="position<?php echo $counter; ?>" value="<?php echo $counter; ?>" />
							<input type="hidden" name="linktype<?php echo $counter; ?>" id="linktype<?php echo $counter; ?>" value="<?php echo $sub_item->link_type; ?>" />
							<input type="hidden" name="anchortitle<?php echo $counter; ?>" id="anchortitle<?php echo $counter; ?>" value="<?php echo $anchor_title; ?>" />
							<input type="hidden" name="newwindow<?php echo $counter; ?>" id="newwindow<?php echo $counter; ?>" value="<?php echo $sub_item->new_window; ?>" />
							<?php 
						}
						
						//Do recursion
						if ( ($depth_counter < $depth) || ($depth == 0) ) {
							woo_custom_navigation_sub_items($sub_item->id,$sub_item->link_type,$table_name,$output_type,$menu_id, $depth, $depth_counter);
						} 
			?></li>
			<?php 
	
		} 
	
	?></ul>
	<?php 
	
	} 
	
	return $parent_id;
 
}

//Checks if any of parent menu items children are the current page
function woo_child_is_current($parent_id, $menu_id, $table_name, $queried_id, $type_settings, $full_web_address) {
	    
    $success = false;
	    
    //Get all child elements
    global $wpdb;
	    
    //GET sub menu items
    $woo_parent_children = $wpdb->get_results( "SELECT id,post_id,parent_id,position,custom_title,custom_link,custom_description,menu_icon,link_type,custom_anchor_title,new_window FROM ".$table_name." WHERE parent_id = '".$parent_id."' AND menu_id='".$menu_id."' ORDER BY position ASC" );
	    
    //If more than 0 child elements
    if (empty($woo_parent_children))
    {
	    
    }
    else
    {
       //Children Loop
        foreach ($woo_parent_children as $woo_parent_child) 
        {
            //Check if meets criteria
            if (($queried_id == $woo_parent_child->post_id) && ($queried_id != 0) && ($type_settings == $woo_parent_child->link_type) ) {
                $success = true;
            }
            else if (($woo_parent_child->custom_link == $full_web_address) && ($queried_id == 0) && ($type_settings == $woo_parent_child->link_type) ) {
                $success = true;
            }    
        }
    }
	            
    return $success;
	    
}

//Outputs All Pages and Sub Items
function woo_get_pages($counter,$type) {

	$pages_args = array(
		    'child_of' => 0,
			'sort_order' => 'ASC',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'parent' => -1,
			'exclude_tree' => '',
			'number' => '',
			'offset' => 0 );
	
	//GET all pages		
	$pages_array = get_pages($pages_args);
	
	$intCounter = $counter;
	$parentli = $intCounter;
	
	if ($pages_array)
	{
		//DISPLAY Loop
		foreach ($pages_array as $post)
		{
	
			if ($post->post_parent == 0)
			{
				//Custom Menu
				if ($type == 'menu')
				{
					$description = trim(get_post_meta($post->ID, 'page-description', true));
					?>
					
					<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
				
						<dl>
						<dt>
						<span class="title"><?php echo $post->post_title; ?></span>
						<span class="controls">
							<span class="type">page</span>
							<a id="edit<?php echo $intCounter; ?>" onclick="edititem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-edit.png" /></a> 
							<a id="remove<?php echo $intCounter; ?>" onclick="removeitem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>">
								<img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-close.png" />
							</a>
							<a target="_blank" href="<?php echo get_permalink($post->ID); ?>">
								<img alt="View Page" title="View Page" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-viewpage.png" />
							</a>
						</span>
						
						</dt>
						</dl>
						<a class="hide" href="<?php echo get_permalink($post->ID); ?>"><span class="title"><?php echo $post->post_title; ?></span>
		    	    	</a>
		    	    	<input type="hidden" name="postmenu<?php echo $intCounter; ?>" id="postmenu<?php echo $intCounter; ?>" value="<?php echo $post->ID; ?>" />
						<input type="hidden" name="parent<?php echo $intCounter; ?>" id="parent<?php echo $intCounter; ?>" value="0" />
						<?php $str_converted = woo_encoding_convert($post->post_title);?>
						<input type="hidden" name="title<?php echo $intCounter; ?>" id="title<?php echo $intCounter; ?>" value="<?php echo htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' ); ?>" />
						<input type="hidden" name="linkurl<?php echo $intCounter; ?>" id="linkurl<?php echo $intCounter; ?>" value="<?php echo get_permalink($post->ID); ?>" />
						<input type="hidden" name="description<?php echo $intCounter; ?>" id="description<?php echo $intCounter; ?>" value="<?php echo $description; ?>" />
						<input type="hidden" name="icon<?php echo $intCounter; ?>" id="icon<?php echo $intCounter; ?>" value="0" />
						<input type="hidden" name="position<?php echo $intCounter; ?>" id="position<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>" />
						<input type="hidden" name="linktype<?php echo $intCounter; ?>" id="linktype<?php echo $intCounter; ?>" value="page" />
						<?php $str_converted = woo_encoding_convert($post->post_title);?>
						<input type="hidden" name="anchortitle<?php echo $intCounter; ?>" id="anchortitle<?php echo $intCounter; ?>" value="<?php echo htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' ); ?>" />
						<input type="hidden" name="newwindow<?php echo $intCounter; ?>" id="newwindow<?php echo $intCounter; ?>" value="0" />
						
						<?php $parentli = $post->ID; ?>
						<?php $intCounter++; ?>			                
						<?php
						
							//Recursive function
							$intCounter = woo_custom_navigation_default_sub_items($post->ID, $intCounter, $parentli, 'pages', 'menu' );
						
						?>
					
					</li>
					
					<?php 
					
				}
				//Sidebar Menu
				elseif ($type == 'default')
				{
					?>
					 
					 <li>
				        <dl>
				        <dt>
				        <?php
				        	//Convert string to UTF-8
							$str_converted = woo_encoding_convert($post->post_title);
				        	$post_text = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				        	$post_url = get_permalink($post->ID);
				        	$post_id = $post->ID;
				        	$post_parent_id = $post->post_parent;
				        	//Convert string to UTF-8
							$str_converted = woo_encoding_convert(get_post_meta($post_id, 'page-description', true));
							$description = htmlspecialchars(trim($str_converted), ENT_QUOTES, 'UTF-8' );
							
				        ?>
				        <?php $templatedir = get_template_directory_uri(); ?>
				        
				        <span class="title"><?php echo $post->post_title; ?></span> <a onclick="appendToList( '<?php echo $templatedir; ?>','Page','<?php echo $post_text; ?>','<?php echo $post_url; ?>','<?php echo $post_id; ?>','<?php echo $post_parent_id ?>','<?php echo $description; ?>')" name="<?php echo $post_text; ?>" value="<?php echo get_permalink($post->ID); ?>"><img alt="Add to Custom Menu" title="Add to Custom Menu" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-add.png" /></a></dt>
				        </dl>
				        <?php $parentli = $post->ID; ?>
						<?php $intCounter++; ?>			    
				        <?php
						
							//Recursive function
							$intCounter = woo_custom_navigation_default_sub_items($post_id, $intCounter, $parentli, 'pages', 'default' );
						
						 ?>
					        
					</li>
	
					<?php 
				
				}
				else
				{
				
				}	
			} 
		} 
	}
	else 
	{
		echo 'Not Found';
	}

	return $intCounter;
}

//Outputs All Categories and Sub Items
function woo_get_categories($counter, $type) {

	$category_args = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => false,
			'include_last_update_time' => false,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'pad_counts'               => false );
	
	
	
	$intCounter = $counter;	
	
	//GET all categories	
	$categories_array = get_categories($category_args);
	
	if ($categories_array)
	{
		//DISPLAY Loop
		foreach ($categories_array as $cat_item)
		{

			if ($cat_item->parent == 0)
			{
				//Custom Menu
				if ($type == 'menu')
				{
					?>
	    
			    	<li id="menu-<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>">
			    		<dl>
			            <dt>
			            	<span class="title"><?php echo $cat_item->cat_name; ?></span>
							<span class="controls">
							<span class="type">category</span>
							<a id="edit<?php echo $intCounter; ?>" onclick="edititem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-edit.png" /></a> 
							<a id="remove<?php echo $intCounter; ?>" onclick="removeitem(<?php echo $intCounter; ?>)" value="<?php echo $intCounter; ?>">
								<img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-close.png" />
							</a>
							<a target="_blank" href="<?php echo get_category_link($cat_item->cat_ID); ?>">
								<img alt="View Page" title="View Page" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-viewpage.png" />
							</a>
							</span>
					
			            </dt>
			            </dl>
			            <a class="hide" href="<?php echo get_category_link($cat_item->cat_ID); ?>"><span class="title"><?php echo $cat_item->cat_name; ?></span>
			            <?php 
			            $use_cats_raw = get_option( 'woo_settings_custom_nav_descriptions' );
			   			$use_cats = strtolower($use_cats_raw);
			   			if ($use_cats == 'yes') { ?>
			            <br/> <span><?php echo trim($cat_item->category_description); ?></span>
			            <?php } ?>
			                    	</a>
			            <input type="hidden" name="postmenu<?php echo $intCounter; ?>" id="postmenu<?php echo $intCounter; ?>" value="<?php echo $cat_item->cat_ID; ?>" />
			            <input type="hidden" name="parent<?php echo $intCounter; ?>" id="parent<?php echo $intCounter; ?>" value="0" />
			            <?php $str_converted = woo_encoding_convert($cat_item->cat_name);?>
			            <input type="hidden" name="title<?php echo $intCounter; ?>" id="title<?php echo $intCounter; ?>" value="<?php echo htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' ); ?>" />
						<input type="hidden" name="linkurl<?php echo $intCounter; ?>" id="linkurl<?php echo $intCounter; ?>" value="<?php echo get_category_link($cat_item->cat_ID); ?>" />
						<?php $str_converted = woo_encoding_convert($cat_item->category_description);?>
						<input type="hidden" name="description<?php echo $intCounter; ?>" id="description<?php echo $intCounter; ?>" value="<?php echo htmlspecialchars(trim($str_converted), ENT_QUOTES, 'UTF-8' ); ?>" />
						<input type="hidden" name="icon<?php echo $intCounter; ?>" id="icon<?php echo $intCounter; ?>" value="0" />
						<input type="hidden" name="position<?php echo $intCounter; ?>" id="position<?php echo $intCounter; ?>" value="<?php echo $intCounter; ?>" />
						<input type="hidden" name="linktype<?php echo $intCounter; ?>" id="linktype<?php echo $intCounter; ?>" value="category" />
						<?php $str_converted = woo_encoding_convert($cat_item->cat_name);?>
						<input type="hidden" name="anchortitle<?php echo $intCounter; ?>" id="anchortitle<?php echo $intCounter; ?>" value="<?php echo htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' ); ?>" />
						<input type="hidden" name="newwindow<?php echo $intCounter; ?>" id="newwindow<?php echo $intCounter; ?>" value="0" />
						
			            <?php $parentli = $cat_item->cat_ID; ?>
			            <?php $intCounter++; ?>			                
			           	<?php
						
							//Recursive function
							$intCounter = woo_custom_navigation_default_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories','menu' );
							
						?>
			            
			    	</li>
			    	
			    	<?php 
			    }
			    //Sidebar Menu
			    elseif ($type == 'default')
			    {
			    	?>
			    	<li>
						<dl>
						<dt>
						<?php
						//Convert string to UTF-8
						$str_converted = woo_encoding_convert($cat_item->cat_name);
	        			$post_text = htmlspecialchars(addslashes($str_converted), ENT_QUOTES, 'UTF-8' );
	        			$post_url = get_category_link($cat_item->cat_ID);
	        			$post_id = $cat_item->cat_ID;
	        			$post_parent_id = $cat_item->parent;
	        			//Convert string to UTF-8
						$str_converted = woo_encoding_convert($cat_item->description);
	        			$description = htmlspecialchars(addslashes(strip_tags(trim($str_converted))), ENT_QUOTES, 'UTF-8' );
	        			?>
	        			<?php $templatedir = get_template_directory_uri(); ?>
						<span class="title"><?php echo $cat_item->cat_name; ?></span> <a onclick="appendToList( '<?php echo $templatedir; ?>','Category','<?php echo $post_text; ?>','<?php echo $post_url; ?>','<?php echo $post_id; ?>','<?php echo $post_parent_id ?>','<?php echo $description; ?>')" name="<?php echo $post_text; ?>" value="<?php echo $post_url;  ?>"><img alt="Add to Custom Menu" title="Add to Custom Menu"  src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-add.png" /></a> </dt>
						</dl>
						<?php $parentli = $cat_item->cat_ID; ?>
			            <?php $intCounter++; ?>		
						<?php 
							//Recursive function
							$intCounter = woo_custom_navigation_default_sub_items($cat_item->cat_ID, $intCounter, $parentli, 'categories','default' );
						?>
						
					</li>
					
					<?php 
			    }	
			} 
		}
	}
	else 
	{
		echo 'Not Found';
	}
	
	return $intCounter;
}

//RECURSIVE Sub Menu Items of default categories and pages
function woo_custom_navigation_default_sub_items($childof, $intCounter, $parentli, $type, $output_type) {

	$counter = $intCounter;
	
	//Custom Menu
	if ($output_type == 'menu') 
	{
		$sub_args = array(
		'child_of' => $childof,
		'hide_empty' => false,
		'parent' => $childof);
	}
	//Sidebar Menu
	elseif ($output_type == 'default') 
	{
		$sub_args = array(
		'child_of' => $childof,
		'hide_empty' => false,
		'parent' => $childof);
	}
	else 
	{
		
	}
	
	//Get Sub Category Items			
	if ($type == 'categories')
	{
		$sub_array = get_categories($sub_args);	
	}
	//Get Sub Page Items
	elseif ($type == 'pages')
	{
		$sub_array = get_pages($sub_args);
	}
	
	
	if ($sub_array)
	{
		?>
		
		<ul id="sub-custom-nav-<?php echo $type ?>">
		
		<?php
		//DISPLAY Loop
		foreach ($sub_array as $sub_item)
		{
			//Prepare Menu Data
			//Category Menu Item
			if ($type == 'categories') 
			{
				$link = get_category_link($sub_item->cat_ID);
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($sub_item->cat_name);
				//$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				$title = htmlspecialchars(addslashes($str_converted), ENT_QUOTES, 'UTF-8' );
				$parent_id = $sub_item->cat_ID;
				$itemid = $sub_item->cat_ID;
				$linktype = 'category';
				$appendtype = 'Category';
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($sub_item->description);
				//$description = htmlspecialchars(strip_tags(trim($str_converted)), ENT_QUOTES, 'UTF-8' );	
				$description = htmlspecialchars(addslashes(strip_tags(trim($str_converted))), ENT_QUOTES, 'UTF-8' );			
			}
			//Page Menu Item
			elseif ($type == 'pages')
			{
				$link = get_permalink($sub_item->ID);
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert($sub_item->post_title);
				//$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				$title = htmlspecialchars(addslashes($str_converted), ENT_QUOTES, 'UTF-8' );
				$parent_id = $sub_item->ID;
				$linktype = 'page';
				$itemid = $sub_item->ID;
				$appendtype = 'Page';
				//Convert string to UTF-8
				$str_converted = woo_encoding_convert(get_post_meta($itemid, 'page-description', true));
				//$description = htmlspecialchars(trim($str_converted), ENT_QUOTES, 'UTF-8' );
				$description = htmlspecialchars(addslashes(strip_tags(trim($str_converted))), ENT_QUOTES, 'UTF-8' );
			}
			//Custom Menu Item
			else 
			{
				$title = '';
				$linktype = 'custom';
				$appendtype= 'Custom';
			}
			
			//Custom Menu
			if ($output_type == 'menu')
			{
				?>
				<li id="menu-<?php echo $counter; ?>" value="<?php echo $counter; ?>">
					<dl>
					<dt>
						<span class="title"><?php echo $title; ?></span>
							<span class="controls">
							<span class="type"><?php echo $linktype; ?></span>
							<a id="edit<?php echo $counter; ?>" onclick="edititem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>"><img class="edit" alt="Edit Menu Item" title="Edit Menu Item" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-edit.png" /></a> 
								<a id="remove<?php echo $counter; ?>" onclick="removeitem(<?php echo $counter; ?>)" value="<?php echo $counter; ?>">
									<img class="remove" alt="Remove from Custom Menu" title="Remove from Custom Menu" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-close.png" />
								</a>
								<a target="_blank" href="<?php echo $link; ?>">
									<img alt="View Page" title="View Page" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-viewpage.png" />
								</a>
						</span>
			
					</dt>
					</dl>
					<a class="hide" href="<?php echo $link; ?>"><?php echo $title; ?></a>
					<input type="hidden" name="dbid<?php echo $counter; ?>" id="dbid<?php echo $counter; ?>" value="<?php echo $sub_item->id; ?>" />
					<input type="hidden" name="postmenu<?php echo $counter; ?>" id="postmenu<?php echo $counter; ?>" value="<?php echo $parent_id; ?>" />
					<input type="hidden" name="parent<?php echo $counter; ?>" id="parent<?php echo $counter; ?>" value="<?php echo $parentli; ?>" />
					<input type="hidden" name="title<?php echo $counter; ?>" id="title<?php echo $counter; ?>" value="<?php echo $title; ?>" />
					<input type="hidden" name="linkurl<?php echo $counter; ?>" id="linkurl<?php echo $counter; ?>" value="<?php echo $link; ?>" />
					<input type="hidden" name="description<?php echo $counter; ?>" id="description<?php echo $counter; ?>" value="<?php echo $description; ?>" />
					<input type="hidden" name="icon<?php echo $counter; ?>" id="icon<?php echo $counter; ?>" value="0" />
					<input type="hidden" name="position<?php echo $counter; ?>" id="position<?php echo $counter; ?>" value="<?php echo $counter; ?>" />
					<input type="hidden" name="linktype<?php echo $counter; ?>" id="linktype<?php echo $counter; ?>" value="<?php echo $linktype; ?>" />
					<input type="hidden" name="anchortitle<?php echo $counter; ?>" id="anchortitle<?php echo $counter; ?>" value="<?php echo $title; ?>" />
					<input type="hidden" name="newwindow<?php echo $counter; ?>" id="newwindow<?php echo $counter; ?>" value="0" />
					
					<?php $counter++; ?>
					<?php 
						
						//Do recursion
						$counter = woo_custom_navigation_default_sub_items($parent_id, $counter, $parent_id, $type, 'menu' ); 
						
					?>
					
				</li>
				<?php 
			}
			//Sidebar Menu
			elseif ($output_type == 'default')
			{
					
				?>
				<li>
					<dl>
					<dt>
					
					<?php $templatedir = get_template_directory_uri(); ?>
					<span class="title"><?php echo $title; ?></span> <a onclick="appendToList( '<?php echo $templatedir; ?>','<?php echo $appendtype; ?>','<?php echo $title; ?>','<?php echo $link; ?>','<?php echo $itemid; ?>','<?php echo $parent_id ?>','<?php echo $description; ?>')" name="<?php echo $title; ?>" value="<?php echo $link; ?>"><img alt="Add to Custom Menu" title="Add to Custom Menu" src="<?php echo get_template_directory_uri(); ?>/functions/images/ico-add.png" /></a> </dt>
					</dl>
					<?php 
					
						//Do recursion
						$counter = woo_custom_navigation_default_sub_items($itemid, $counter, $parent_id, $type, 'default' );
						
					?>
				</li>
					  
				<?php 
			}
			
		}
		?>
		
		</ul>
		
	<?php 
	}
	
	return $counter;

}

/*-----------------------------------------------------------------------------------*/
/* Recursive get children */
/*-----------------------------------------------------------------------------------*/

function get_children_menu_elements($childof, $intCounter, $parentli, $type, $menu_id, $table_name) {

	$counter = $intCounter;
	
	global $wpdb;
	
	
	
	//Get Sub Category Items			
	if ($type == 'categories')
	{
		$sub_args = array(
			'child_of' => $childof,
			'hide_empty'  => false,
			'parent' => $childof);
		$sub_array = get_categories($sub_args);	
	}
	//Get Sub Page Items
	elseif ($type == 'pages')
	{
		$sub_args = array(
			'child_of' => $childof,
			'parent' => $childof);
	
		$sub_array = get_pages($sub_args);
		
	}
	else {
	
	}
	
	if ($sub_array)
	{
		//DISPLAY Loop
		foreach ($sub_array as $sub_item)
		{
			if (isset($sub_item->parent)) {
				$sub_item_parent = $sub_item->parent;
			}
			elseif (isset($sub_item->post_parent)) {
				$sub_item_parent = $sub_item->post_parent; 
			}
			else {
			}
			//Is child
			if ($sub_item_parent == $childof)
			{
				//Prepare Menu Data
				//Category Menu Item
				if ($type == 'categories') 
				{
					$link = get_category_link($sub_item->cat_ID);
					//Convert string to UTF-8
					//$str_converted = woo_encoding_convert($sub_item->cat_name);
					//$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
					$title = $sub_item->cat_name;
					$parent_id = $sub_item->category_parent;
					$itemid = $sub_item->cat_ID;
					$linktype = 'category';
					$appendtype= 'Category';
				}
				//Page Menu Item
				elseif ($type == 'pages')
				{
					$link = get_permalink($sub_item->ID);
					//Convert string to UTF-8
					//$str_converted = woo_encoding_convert($sub_item->post_title);
					//$title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
					$title = $sub_item->post_title;
					$parent_id = $sub_item->post_parent;
					$linktype = 'page';
					$itemid = $sub_item->ID;
					$appendtype= 'Page';
				}
				//Custom Menu Item
				else 
				{
					$title = '';
					$linktype = 'custom';
					$appendtype= 'Custom';
				}
				
				//CHECK for existing parent records
				//echo $parent_id;
				$woo_result = $wpdb->get_results( "SELECT id FROM ".$table_name." WHERE post_id='".$parent_id."' AND link_type='".$linktype."' AND menu_id='".$menu_id."'" );
				if ($woo_result > 0 && isset($woo_result[0]->id)) {
					$parent_id = $woo_result[0]->id;
				}
				else {
					//$parent_id = 0;
				}
				
				//INSERT item
				//Convert string to UTF-8
				$str_converted = stripslashes($title);
				//$insert_title = htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8' );
				//$insert = "INSERT INTO ".$table_name." (position,post_id,parent_id,custom_title,custom_link,custom_description,menu_icon,link_type,menu_id,custom_anchor_title) "."VALUES ( '".$counter."','".$itemid."','".$parent_id."','".$title."','".$link."','','','".$linktype."','".$menu_id."','".$title."')";
	  			//$results = $wpdb->query( $insert );
	  			$results = $wpdb->insert( $table_name, array( 'position' => $counter, 'post_id' => $itemid, 'parent_id' => $parent_id, 'custom_title' => $str_converted, 'custom_link' => $link, 'custom_description' => '', 'menu_icon' => '', 'link_type' => $linktype, 'menu_id' => $menu_id, 'custom_anchor_title' => $str_converted ));
	 
	  			$counter++;
	  			$counter = get_children_menu_elements($itemid, $counter, $parent_id, $type, $menu_id, $table_name);
			}	
			//Do nothing
			else {
			
			}
		}
	}
	return $counter;
}

/*---------------------------------------------------------------------------------*/
/* Woothemes Custom Navigation Menu Widget */
/*---------------------------------------------------------------------------------*/

class Woo_NavWidget extends WP_Widget {

	function Woo_NavWidget() {
		$widget_ops = array( 'description' => 'Use this widget to add one of your Woo Custom Navigation Menus as a widget.' );
		parent::WP_Widget(false, __( 'Woo - Custom Nav Menu', 'woothemes' ),$widget_ops);      
	}

	function widget($args, $instance) {  
		$navmenu = $instance['navmenu'];
		$navtitle = $instance['navtitle'];
		$navdeveloper = strtolower($instance['navdeveloper']);
		if (isset($instance['navdiv'])) { $navdiv = strtolower($instance['navdiv']); } else { $navdiv = 'no';}
		if (isset($instance['navul'])) { $navul = strtolower($instance['navul']); } else { $navul = 'no';}
		if (isset($instance['navwidgetdescription'])) { $navwidgetdescription = strtolower($instance['navwidgetdescription']); } else { $navwidgetdescription = '2';}
				
		$menuexists = false;
		
		global $wpdb;
		
		//GET menu name
		if ($navmenu > 0)
		{
			$table_name_menus = $wpdb->prefix . "woo_custom_nav_menus";
			$woo_result = $wpdb->get_results( "SELECT menu_name FROM ".$table_name_menus." WHERE id='".$navmenu."'" );
			$woo_custom_nav_menu_name = $woo_result[0]->menu_name;
			$menuexists = true;
		}
		//Do nothing
		else 
		{
			$menuexists = false;
		}
		?>
		
		<?php 
			//DEVELOPER settings enabled
			if ($navdeveloper == 'yes') 
			{ 
				//DISPLAY Custom DIV
				if ($navdiv == 'yes') 
				{ 
					?>
					<div class="widget block">
					<?php 
				}
				//Do NOT display DIV
				else 
				{
					
				} 
				
			} 
			//DISPLAY default DIV
			else 
			{
				?>
				<div class="widget block">
				<?php 
			}
		?>
		
			<h3><?php echo $navtitle; ?></h3>
			<?php 
			
			if ($menuexists) 
			{
				?>
        		<?php 
        		
        		//DEVELOPER settings enabled
				if ($navdeveloper == 'yes') 
				{ 
					//DISPLAY Custom UL
					if ($navul == 'yes') 
					{ 
						?>
						<ul class="custom-nav">
						<?php 
					}
					//Do NOT display UL
					else 
					{
						
					} 
					
				} 
				//DISPLAY default UL
				else 
				{
					?>
					<ul class="custom-nav">
					<?php 
				}
        		
        		?>
				
						<?php
							//DISPLAY custom navigation menu
							if (get_option( 'woo_custom_nav_menu') == 'true') {
        						woo_custom_navigation_output( 'name='.$woo_custom_nav_menu_name.'&desc='.$navwidgetdescription);
        					}				
						?>
				
				<?php 
				
					//DEVELOPER settings enabled
					if ($navdeveloper == 'yes') 
					{ 
						//DISPLAY Custom UL
						if ($navul == 'yes') 
						{ 
							?>
							</ul>
							<?php 
						}
						//Do NOT display UL
						else 
						{
							
						} 
						
					} 
					//DISPLAY default UL
					else 
					{
						?>
						</ul>
						<?php 
					}
					
				?>
			<?php
			}
			else
			{
				echo "You have not setup the custom navigation widget correctly, please check your settings in the backend.";
			}
			?>
		<?php 
			//DEVELOPER settings enabled
			if ($navdeveloper == 'yes') 
			{ 
				//DISPLAY Custom DIV
				if ($navdiv == 'yes') 
				{ 
					?>
					</div>
					<?php 
				}
				//Do NOT display DIV
				else 
				{
					
				} 
				
			} 
			//DISPLAY default DIV
			else 
			{
				?>
				</div>
				<?php 
			}
		?><!-- /#nav-container -->
			
			<?php
	}

	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	function form($instance) {        
		$navmenu = esc_attr($instance['navmenu']);
		$navtitle = esc_attr($instance['navtitle']);
		$navdeveloper = esc_attr($instance['navdeveloper']);
		if (isset($instance['navdiv'])) { $navdiv = esc_attr($instance['navdiv']); } else { $navdiv = 'no';}
		if (isset($instance['navul'])) { $navul = esc_attr($instance['navul']); } else { $navul = 'no';}
		if (isset($instance['navwidgetdescription'])) { $navwidgetdescription = esc_attr($instance['navwidgetdescription']); } else { $navwidgetdescription = '2';}
				
		global $wpdb;
				
		//GET Menu Items for SELECT OPTIONS 	
		$table_name_custom_menus = $wpdb->prefix . "woo_custom_nav_menus";
		$custom_menu_records = $wpdb->get_results( "SELECT id,menu_name FROM ".$table_name_custom_menus);
		
		//CHECK if menus exist
		if ($custom_menu_records > 0)
		{
		
			?>
			
			 <p>
	            <label for="<?php echo $this->get_field_id( 'navmenu' ); ?>"><?php _e( 'Select Menu:', 'woothemes' ); ?></label>
				
				<select id="<?php echo $this->get_field_id( 'navmenu' ); ?>" name="<?php echo $this->get_field_name( 'navmenu' ); ?>">
					<?php 
					
					//DISPLAY SELECT OPTIONS
					foreach ($custom_menu_records as $custom_menu_record)
					{
						if ($navmenu == $custom_menu_record->id) {
							$selected_option = 'selected="selected"';
						}
						else {
							$selected_option = '';
						}
						?>
						<option value="<?php echo $custom_menu_record->id; ?>" <?php echo $selected_option; ?>><?php echo $custom_menu_record->menu_name; ?></option>
						<?php
						
					}
					?>
				</select>
	
			</p>
			
			<p>
				
		        <label for="<?php echo $this->get_field_id( 'navtitle' ); ?>"><?php _e( 'Title:', 'woothemes' ); ?></label>
		    	<input type="text" name="<?php echo $this->get_field_name( 'navtitle' ); ?>" value="<?php echo $navtitle; ?>" class="widefat" id="<?php echo $this->get_field_id( 'navtitle' ); ?>" />
		    </p>
		    
	    	<p>
			<?php
			    $checked = strtolower($navdeveloper);
			?>
			
			<label for="<?php echo $this->get_field_id( 'navdeveloper' ); ?>"><?php _e( 'Advanced Options:', 'woothemes' ); ?></label><br />    	
			<span class="checkboxes">
			   	<label>Yes</label><input type="radio" id="<?php echo $this->get_field_name( 'navdeveloper' ); ?>" name="<?php echo $this->get_field_name( 'navdeveloper' ); ?>" value="yes" <?php if ($checked=='yes') { echo 'checked="checked"'; } ?> />
			    <label>No</label><input type="radio" id="<?php echo $this->get_field_name( 'navdeveloper' ); ?>" name="<?php echo $this->get_field_name( 'navdeveloper' ); ?>" value="no" <?php if ($checked=='yes') { } else { echo 'checked="checked"'; } ?> />
			</span><!-- /.checkboxes -->
			
			</p>
		    
		    <?php 
		    
		    //DEVELOPER settings
		    if ($checked == 'yes')
		    {
		    	?>
		    	
		    	<p>
				<?php
				    $checked = strtolower($navdiv);
				?>
				
				<label for="<?php echo $this->get_field_id( 'navdiv' ); ?>"><?php _e( 'Wrap in container DIV:', 'woothemes' ); ?></label><br />	
				<span class="checkboxes">
				   	<label>Yes</label><input type="radio" id="<?php echo $this->get_field_name( 'navdiv' ); ?>" name="<?php echo $this->get_field_name( 'navdiv' ); ?>" value="yes" <?php if ($checked=='yes') { echo 'checked="checked"'; } ?> />
				    <label>No</label><input type="radio" id="<?php echo $this->get_field_name( 'navdiv' ); ?>" name="<?php echo $this->get_field_name( 'navdiv' ); ?>" value="no" <?php if ($checked=='yes') { } else { echo 'checked="checked"'; } ?> />
				</span><!-- /.checkboxes -->
			
			</p>
			
			<p>
				<?php
				    $checked = strtolower($navul);
				?>
				
				<label for="<?php echo $this->get_field_id( 'navul' ); ?>"><?php _e( 'Wrap in container UL:', 'woothemes' ); ?></label><br />    	
				<span class="checkboxes">
				   	<label>Yes</label><input type="radio" id="<?php echo $this->get_field_name( 'navul' ); ?>" name="<?php echo $this->get_field_name( 'navul' ); ?>" value="yes" <?php if ($checked=='yes') { echo 'checked="checked"'; } ?> />
				    <label>No</label><input type="radio" id="<?php echo $this->get_field_name( 'navul' ); ?>" name="<?php echo $this->get_field_name( 'navul' ); ?>" value="no" <?php if ($checked=='yes') { } else { echo 'checked="checked"'; } ?> />
				</span><!-- /.checkboxes -->
			
			</p>

			<?php $advanced_option_descriptions = get_option( 'woo_settings_custom_nav_advanced_options' ); ?>
			<p <?php if ($advanced_option_descriptions == 'no') { ?>style="display:none;"<?php } ?>>
			
	           <?php
				    $checked = strtolower($navwidgetdescription);
				?>
				
				<label for="<?php echo $this->get_field_id( 'navwidgetdescription' ); ?>"><?php _e( 'Show Top Level Descriptions:', 'woothemes' ); ?></label><br />    	
				<span class="checkboxes">
				   	<label>Yes</label><input type="radio" id="<?php echo $this->get_field_name( 'navwidgetdescription' ); ?>" name="<?php echo $this->get_field_name( 'navwidgetdescription' ); ?>" value="1" <?php if ($checked=='1') { echo 'checked="checked"'; } ?> />
				    <label>No</label><input type="radio" id="<?php echo $this->get_field_name( 'navwidgetdescription' ); ?>" name="<?php echo $this->get_field_name( 'navwidgetdescription' ); ?>" value="2" <?php if ($checked=='1') { } else { echo 'checked="checked"'; } ?> />
				</span><!-- /.checkboxes -->
	        </p>
		    	<?php 
		    }
		    //Do nothing
		    else 
		    {
		    	
		    }
			
		}
		//Error message for menus not existing
		else 
		{
			?>
			<p>
		    	<label><?php _e( 'The Custom Menu has not been configured correctly.  Please check your theme settings before adding this widget.', 'woothemes' ); ?></label>
			</p>
			<?php
		}
	}
	
} 

//CHECK if Custom Nav Menu is Enabled
if (get_option( 'woo_custom_nav_menu') == 'true') 
{
	register_widget( 'Woo_NavWidget' );
}

?>