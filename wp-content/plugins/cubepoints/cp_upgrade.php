<?php
/**
 * CubePoints Upgrade Script
 */

if(get_option('cp_db_version')<1.3){
	if(is_admin()){
		cp_install();
		update_option('cp_db_version', 1.3);
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->base_prefix."cubepoints'") == $wpdb->base_prefix."cubepoints" ){
			$rows = $wpdb->get_results('DESCRIBE '.$wpdb->base_prefix."cubepoints");
			$cols = array();
			foreach($rows as $row){
				$cols[]=$row->Field;
			}
			if(!in_array('source',$cols)){
				//Nothing to import, old database has wrong database structure
			}
			else{
				$results=$wpdb->get_results("SELECT * FROM ".$wpdb->base_prefix."cubepoints");
				$count = 0;
				$count1 = 0;
				$left = array();
				foreach($results as $result){
					$count1++;
					if($result->type=='comment'||$result->type=='admin'||$result->type=='post'||$result->type=='reg'||$result->type=='login'||$result->type=='donate'||$result->type=='login'){
						if($result->type=='login'){
							$result->type = 'dailypoints';
						}
						$wpdb->query("INSERT INTO `".CP_DB."` (`id`, `uid`, `type`, `data`, `points`, `timestamp`) VALUES (NULL, '".$result->uid."', '".$result->type."', ".$result->source.", '".$result->points."', ".$result->timestamp.");");
						// Not removing entries from old database
						//$wpdb->query("DELETE FROM ".$wpdb->base_prefix."cubepoints WHERE id=".$result->id);
						$count++;
					}
					else{
						$left[]=$result->type;
					}
				}
				echo '<div class="updated"><p><strong>'.__('CubePoints Updated').': </strong>'.__('Your database has been updated and  ', 'cp').' '.$count.' '.__('out of', 'cp').' '.$count1.' '.__('log items were imported', 'cp').'.</p></div>';
				$left = array_unique($left);
				if(count($left)>0){
					echo '<div class="error"><p><strong>'.__('The following log types were not imported', 'cp').':</strong> '.implode($left,', ').'</p></div>';
				}
			}
		}
		else{
			//Nothing to import, old database not found
		}
	}
}

?>