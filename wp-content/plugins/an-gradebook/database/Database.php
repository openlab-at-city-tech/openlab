<?php
class ANGB_DATABASE{
	const an_gradebook_db_version = 3.2;	
	public function __construct(){	
		register_activation_hook(__FILE__,array($this,'database_init'));	
		register_activation_hook(__FILE__,array($this,'database_alter'));
		add_action('plugins_loaded', array($this,'an_gradebook_upgrade_db'));	
	}	
	public function an_gradebook_upgrade_db(){
		if(!get_site_option( 'an_gradebook_db_version' )){
			$this->database_init();
		}
		if(self::an_gradebook_db_version > get_site_option( 'an_gradebook_db_version' )){
		    $this->database_alter();
		}
	}
	public function database_alter(){
		//Any alterations to the table after they have been created in a previous version should take place here.  This works
		//by looping through the necessary db alterations based on the current version of the db. To add an alteration use the following  
		//template code block:
		//if(get_site_option( 'an_gradebook_db_version' )==[current_db_version]){ 
		//    do stuff to tables 
		//    update_option( "an_gradebook_db_version", self::an_gradebook_db_version);
		// }
		//where the constant an_gradebook_db_version should be changed to a larger number.				
		global $wpdb;		
		if(get_site_option( 'an_gradebook_db_version' )==2){
			$sql = "ALTER TABLE an_gradebooks CHANGE name name MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
				CHANGE school school TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
				CHANGE semester semester TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
			$wpdb->query($sql);		
			update_option( "an_gradebook_db_version", 3 );				
		}		
		if(get_site_option( 'an_gradebook_db_version' )==3){
			$sql = "ALTER TABLE an_assignment CHANGE assign_points_earned assign_points_earned decimal(7,2) NOT NULL";
			$wpdb->query($sql);		
			update_option( "an_gradebook_db_version", 3.1 );				
		}
		if(get_site_option( 'an_gradebook_db_version' )==3.1){
			$gradebooks = $wpdb ->get_results("SELECT * FROM an_gradebooks", ARRAY_A); 			
			$assignments = $wpdb -> get_results("SELECT * FROM an_assignments", ARRAY_A); 
			$cells = $wpdb -> get_results("SELECT * FROM an_assignment", ARRAY_A);
			foreach ($gradebooks as $gradebook){
				$gbid = $gradebook['id'];
				$assignments_temp = array_filter($assignments,function($assignment) use($gbid){
					return $assignment['gbid'] == $gbid;
				});
				usort($assignments_temp, build_sorter('assign_order'));
				$i = 1;				
				foreach($assignments_temp as &$assignment){
					$amid = $assignment['id'];					
					$wpdb->update('an_assignments', array( 'assign_order' => $i), array('id' => $amid));   
					$cells_temp = array_filter($cells,function($cell) use($amid){
						return $cell['amid'] == $amid;
					});
					usort($cells_temp, build_sorter('assign_order'));		
					foreach($cells_temp as &$cell){
						$cid = $cell['id'];
						$wpdb->update('an_assignment', array( 'assign_order' => $i), array('id' => $cid));   						
					}
					$i++;
				}				
			}
			update_option( "an_gradebook_db_version", 3.14 );				
		}
		if(get_site_option( 'an_gradebook_db_version' )==3.14){ 
			$sql = 'ALTER TABLE an_assignments ADD assign_visibility VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "Students"';					
			$wpdb->query($sql);				
		    update_option( "an_gradebook_db_version", 3.141);
		}	
		if(get_site_option( 'an_gradebook_db_version' )==3.141){ 
			$sql1 = "SELECT uid FROM an_gradebook";					
			$sql2 = "SELECT ID FROM wp_users";			
			$result1 = $wpdb->get_col($sql1);				
			$result2 = $wpdb->get_col($sql2);	
			$uids_to_delete_from_gradebook = array_diff($result1, $result2);
			$sql1 = "DELETE FROM an_gradebook WHERE uid IN (".implode(',', $uids_to_delete_from_gradebook).")";
			$sql2 = "DELETE FROM an_assignment WHERE uid IN (".implode(',', $uids_to_delete_from_gradebook).")";			
			$wpdb->query($sql1);
			$wpdb->query($sql2);			
		    update_option( "an_gradebook_db_version", 3.1415);
		}	
		if(get_site_option( 'an_gradebook_db_version' )==3.1415){ 
			$sql = "RENAME TABLE an_gradebooks TO an_gradebook_courses, an_gradebook TO an_gradebook_students, an_assignments TO an_gradebook_assignments, an_assignment TO an_gradebook_cells";																
			$wpdb->query($sql);	
	  		$db_name = 'an_gradebook_users';
			if($wpdb->get_var('SHOW TABLES LIKE "'.$db_name.'"') != $db_name){
				$sql = 'CREATE TABLE ' . $db_name . ' (
				id int(11) NOT NULL AUTO_INCREMENT,
				uid int(11) NOT NULL,
				gbid int(11) NOT NULL,
				role VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "student",
				PRIMARY KEY  (id)  )';
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}	
			
			$sql = 'INSERT INTO an_gradebook_users (uid, gbid) SELECT uid, gbid FROM an_gradebook_students';
			$wpdb->query($sql);			
			$sql = 'SELECT gbid FROM an_gradebook_courses';
			$gbids = $wpdb -> get_col('SELECT id FROM an_gradebook_courses');
			$sql_sub = '';
			foreach($gbids as $gbid){
				$sql_sub = $sql_sub. "(1,$gbid,'instructor'),";
			}
			$sql_sub = rtrim($sql_sub, ',');
			$sql = 'INSERT INTO an_gradebook_users (uid, gbid, role) VALUES '. $sql_sub;
			$wpdb -> query($sql);
			$sql = 'DROP TABLE an_gradebook_students';
			$wpdb -> query($sql);		
			update_option( "an_gradebook_settings", array(
				'administrator'=>true,
				'editor'=>false,
				'contributor'=>false,
				'author'=>false,
				'subscriber'=>false
			));	
		}	
		if(get_site_option( 'an_gradebook_db_version' )==3.1415){ 
			update_option( "an_gradebook_settings", array(
				'administrator'=>true,
				'editor'=>false,
				'contributor'=>false,
				'author'=>false,
				'subscriber'=>false
			));			
		    update_option( "an_gradebook_db_version", self::an_gradebook_db_version);
		}									
	}
	public function database_init() {
		global $wpdb;
	  	$db_name = 'an_gradebook_courses';
		if($wpdb->get_var('SHOW TABLES LIKE "'.$db_name.'"') != $db_name){
			$sql = 'CREATE TABLE ' . $db_name . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			name MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			school TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			semester TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			year int(11) NOT NULL,
			PRIMARY KEY  (id) )';
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	  	$db_name = 'an_gradebook_users';
		if($wpdb->get_var('SHOW TABLES LIKE "'.$db_name.'"') != $db_name){
			$sql = 'CREATE TABLE ' . $db_name . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			uid int(11) NOT NULL,
			gbid int(11) NOT NULL,
			role VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "student",
			PRIMARY KEY  (id)  )';
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}		
		//$db_name2 should be changed to $table_name but we'll stick with this for now
		$db_name2 = 'an_gradebook_assignments';
		//The column headings that should be in the an_assignments table are stored in $table_columns
		$table_columns = array('id','gbid','assign_order','assign_name','assign_category', 'assign_visibility','assign_date','assign_due');
		$table_columns_specs = array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'gbid' => 'int(11) NOT NULL',
			'assign_order' => 'int(11) NOT NULL',
			'assign_name' => 'mediumtext NOT NULL',
			'assign_category' => 'mediumtext NOT NULL',			
			'assign_visibility' => 'VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "Students"', 							
			'assign_date' => 'DATE NOT NULL DEFAULT "0000-00-00"',
			'assign_due' => 'DATE NOT NULL DEFAULT "0000-00-00"');
		if($wpdb->get_var('SHOW TABLES LIKE "'.$db_name2.'"') != $db_name2){
			$sql = 'CREATE TABLE ' . $db_name2 . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			gbid int(11) NOT NULL,
			assign_order int(11) NOT NULL,		
			assign_name mediumtext NOT NULL,
			assign_category mediumtext NOT NULL,			
			assign_visibility VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT "Students",
			assign_date DATE NOT NULL DEFAULT "0000-00-00",
			assign_due DATE NOT NULL DEFAULT "0000-00-00",			
			PRIMARY KEY  (id) )';
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		} else {
			//Otherwise, check if there is something to upgrade in an_gradebook_assignments table		
			//anfixme: this needs to move to the database_alter
			$an_assignments_columns = $wpdb->get_col( "SELECT column_name FROM information_schema.columns
				WHERE table_name = 'an_gradebook_assignments' ORDER BY ordinal_position" );
			$missing_columns = array_diff($table_columns, $an_assignments_columns);
			if(count($missing_columns)){
				//add missing columns
				$sql = 'ALTER TABLE an_gradebook_assignments ';
				foreach ($missing_columns as $missing_column){
					$sql = $sql. 'ADD '. $missing_column .' '. $table_columns_specs[$missing_column] .', ';
				}
				$sql = rtrim(trim($sql), ',');
				$wpdb->query($sql);	
			}				
		}
 		$db_name3 = 'an_gradebook_cells';
		if($wpdb->get_var('SHOW TABLES LIKE "'.$db_name3.'"') != $db_name3){
			$sql = 'CREATE TABLE ' . $db_name3 . ' (
			id int(11) NOT NULL AUTO_INCREMENT,
			uid int(11) NOT NULL,
			gbid int(11) NOT NULL,
    	    amid int(11) NOT NULL,
	        assign_order int(11) NOT NULL,
	        assign_points_earned decimal(7,2) NOT NULL,
			PRIMARY KEY  (id) )';
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}	
		update_option( "an_gradebook_settings", array(
			'administrator'=>true,
			'editor'=>false,
			'contributor'=>false,
			'author'=>false,
			'subscriber'=>false
		));	
		update_option( "an_gradebook_db_version", self::an_gradebook_db_version );							
	}	
}
?>