<?php

/**
 * BUDDYPRESS CUBEPOINTS FUNCTIONS
 *
 * @version 0.1.9.8
 * @since 1.0
 * @package BuddyPress CubePoints
 * @subpackage Main
 * @license GPL v2.0
 * @link http://wordpress.org/extend/plugins/cubepoints-buddypress-integration/
 *
 * ========================================================================================================
 */
 
/**
 * bp_cp_bbpress2_new_topic_add_cppoints()
 *
 * Hide compose sent to box for member under certain point value
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function bp_cp_bbpress2_new_topic_add_cppoints() {
	$current_user = wp_get_current_user(); 
	cp_points('bp_cp_bbpress2_new_topic', $current_user->ID, get_option('bp_cp_bbpress2_new_topic'), "");
}

/**
 * bp_cp_bbpress2_new_reply_add_cppoints()
 *
 * Hide compose sent to box for member under certain point value
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function bp_cp_bbpress2_new_reply_add_cppoints() {
	$current_user = wp_get_current_user();
	cp_points('bp_cp_bbpress2_new_reply', $current_user->ID, get_option('bp_cp_bbpress2_new_reply'), "");
}

/**
 * my_bp_hide_updates_cb()
 *
 * Hide compose sent to box for member under certain point value
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_hide_updates_cb() {
	global $bp; 
	$cp_spammercheck = cp_getPoints($bp->loggedin_user->id);
	$cp_update_n_reply_spamcheck = get_option('bp_update_n_reply_spamcheck_cp_bp');
	if ($cp_spammercheck < $cp_update_n_reply_spamcheck) { echo '<style type="text/css">#whats-new-form{display:none;}.acomment-reply{display:none;}.activity-comments{display:none;}</style>'; }
}
 
/**
 * my_bp_hide_compose_message()
 *
 * Hide compose sent to box for member under certain point value
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_hide_compose_message() {
	global $bp; 
	$cp_spammercheck = cp_getPoints($bp->loggedin_user->id);
	$cp_messagespamcheck = get_option('bp_messagespamcheck_cp_bp');
	
	if ($cp_spammercheck < $cp_messagespamcheck) {
		
		echo '<style type="text/css">
			#send_message_form {display:none;}
	   	      </style>
	   	      <script type="text/javascript">
	   	      alert ("'._e('You don\'t have enough points to send a message.','cp_buddypress').'");
	   	      </script>
		';
		$naughtygoback = get_home_url();
		header( 'refresh: 1; url='.$naughtygoback);

	}
}

/**
 * my_bp_hide_send_message()
 *
 * Hide send message to member under certain point value
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_hide_send_message() {

	global $bp; 
	$cp_spammercheck = cp_getPoints($bp->loggedin_user->id);
	$cp_messagespamcheck = get_option('bp_messagespamcheck_cp_bp');
	
	if ($cp_spammercheck < $cp_messagespamcheck) {
		
		echo '<style type="text/css">
			#send-private-message {display:none;}
	   	      </style>
		';
	}
}
 
/**
 * my_bp_hide_group_create_button()
 *
 * Hide group creation button for member under certain point value
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_hide_group_create_button() {
	global $bp; 
	$cp_spammercheck = cp_getPoints($bp->loggedin_user->id);
	$cp_groupspamcheck = get_option('bp_groupcreatespamcheck_cp_bp');
	
	if ($cp_spammercheck < $cp_groupspamcheck) {
		
		$naughtygoback = get_home_url();
		
		echo '<style type="text/css">
			#send_message_form {display:none;}
	   	      </style>
	   	      <script type="text/javascript">
	   	      alert ("'._e('You don\'t have enough points to create a group.','cp_buddypress').'");
	   	      window.location = "'.$naughtygoback.'"
	   	      </script>
		';

	}
}

/**
 * my_bp_admin_bar_points()
 *
 * Display points earned in the admin bar
 * 
 *  @version 1.9.8.4
 *  @since 1.0
 */
function my_bp_admin_bar_points() {
 if ( is_user_logged_in() ) {
	global $bp;
	$user = wp_get_current_user();

	if(cp_module_activated('donate')){ // Donate Module is Active
	    
		if(function_exists('cp_lottery_show_logs')){			
		$cp_lottery1_user_entries = get_user_meta($user->ID, 'lottery1_entries', true);
		$cp_lottery2_user_entries = get_user_meta($user->ID, 'lottery2_entries', true);
		$cp_lottery3_user_entries = get_user_meta($user->ID, 'lottery3_entries', true);
		$cp_lottery4_user_entries = get_user_meta($user->ID, 'lottery4_entries', true);
		$cp_lottery5_user_entries = get_user_meta($user->ID, 'lottery5_entries', true);
		$cp_bet1_pts = get_user_meta($user->ID, 'gamble1_bet_pts', true);
		$cp_bet2_pts = get_user_meta($user->ID, 'gamble2_bet_pts', true);
		$cp_bet3_pts = get_user_meta($user->ID, 'gamble3_bet_pts', true);
		$cp_bet4_pts = get_user_meta($user->ID, 'gamble4_bet_pts', true);
		$cp_bet5_pts = get_user_meta($user->ID, 'gamble5_bet_pts', true);
		$all_lottery_n_bet_active = get_option('cp_lottery1_onoff') + get_option('cp_lottery2_onoff') + get_option('cp_lottery3_onoff') + get_option('cp_lottery4_onoff') + get_option('cp_lottery5_onoff') + get_option('cp_gamble1_onoff') + get_option('cp_gamble2_onoff') + get_option('cp_gamble3_onoff') + get_option('cp_gamble4_onoff') + get_option('cp_gamble5_onoff');
		  
			  
		  if($all_lottery_n_bet_active > 0){
		    echo '<li>';
		    ?><a href="#" title="<?php _e('Donate', 'cp'); ?>" onclick="Javascript:cp_module_donate();" class="thickbox cp_donateLink">
		    <?php echo cp_displayPoints();
		  } else {
		    echo '<li class="no-arrow">';
		    ?><a href="#" title="<?php _e('Donate', 'cp'); ?>" onclick="Javascript:cp_module_donate();" class="thickbox cp_donateLink">
		    <?php echo cp_displayPoints().'</a>';
		  }
		  

		} else {
			
		  echo '<li class="no-arrow">'; ?>
		  <a href="<?php bloginfo('url'); echo '/'.BP_MEMBERS_SLUG.'/'; echo bp_loggedin_user_username(); echo '/'.$bp->cubepoint->slug.'/'; ?>">
		  <?php echo cp_displayPoints().'</a>';
		  
		}
		
		if(function_exists('cp_lottery_show_logs')) { // see if they have my plugin
		 if($all_lottery_n_bet_active > 0 ){ // Start Menu Items
		  $totalbetnlottos2enter = 0;
		  if($cp_lottery1_user_entries < 1 && get_option('cp_lottery1_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery2_user_entries < 1 && get_option('cp_lottery2_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery3_user_entries < 1 && get_option('cp_lottery3_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery4_user_entries < 1 && get_option('cp_lottery4_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery5_user_entries < 1 && get_option('cp_lottery5_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet1_pts < 1 && get_option('cp_gamble1_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet2_pts < 1 && get_option('cp_gamble2_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet3_pts < 1 && get_option('cp_gamble3_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet4_pts < 1 && get_option('cp_gamble4_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet5_pts < 1 && get_option('cp_gamble5_onoff') == 1){ $totalbetnlottos2enter++; }
		 	 
		   if($totalbetnlottos2enter > 0){echo '&nbsp;<span id="cblotobetson">'.$totalbetnlottos2enter.'</span>&nbsp;</a>';} else { echo '</a>'; }
		   echo '<ul>';
		  		
		   if(get_option('cp_lottery1_onoff') == 1){
			if($cp_lottery1_user_entries < 1){ $title = get_option('bp_lottery1_open_cp_bp'); }
			if($cp_lottery1_user_entries > 0){ $title = get_option('bp_lottery1_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery1_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery2_onoff') == 1){
			if($cp_lottery2_user_entries < 1){ $title = get_option('bp_lottery2_open_cp_bp'); }
			if($cp_lottery2_user_entries > 0){ $title = get_option('bp_lottery2_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery2_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery3_onoff') == 1){
			if($cp_lottery3_user_entries < 1){ $title = get_option('bp_lottery3_open_cp_bp'); }
			if($cp_lottery3_user_entries > 0){ $title = get_option('bp_lottery3_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery3_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery4_onoff') == 1){
			if($cp_lottery4_user_entries < 1){ $title = get_option('bp_lottery4_open_cp_bp'); }
			if($cp_lottery4_user_entries > 0){ $title = get_option('bp_lottery4_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery4_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery5_onoff') == 1){
			if($cp_lottery5_user_entries < 1){ $title = get_option('bp_lottery5_open_cp_bp'); }
			if($cp_lottery5_user_entries > 0){ $title = get_option('bp_lottery5_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery5_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble1_onoff') == 1){
		   	   if($cp_bet1_pts < 1){ $title = get_option('bp_bet1_open_cp_bp'); }
		   	   if($cp_bet1_pts > 0){ $title = get_option('bp_bet1_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet1_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble2_onoff') == 1){
		   	   if($cp_bet2_pts < 1){ $title = get_option('bp_bet2_open_cp_bp'); }
		   	   if($cp_bet2_pts > 0){ $title = get_option('bp_bet2_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet2_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble3_onoff') == 1){
		   	   if($cp_bet3_pts < 1){ $title = get_option('bp_bet3_open_cp_bp'); }
		   	   if($cp_bet3_pts > 0){ $title = get_option('bp_bet3_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet3_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble4_onoff') == 1){
		   	   if($cp_bet4_pts < 1){ $title = get_option('bp_bet4_open_cp_bp'); }
		   	   if($cp_bet4_pts > 0){ $title = get_option('bp_bet4_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet4_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble5_onoff') == 1){
		   	   if($cp_bet5_pts < 1){ $title = get_option('bp_bet5_open_cp_bp'); }
		   	   if($cp_bet5_pts > 0){ $title = get_option('bp_bet5_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet5_url_cp_bp').'">'.$title.'</a></li>';
		   }
			
		 } // if any giveaways / bet are active
	      echo '</ul>';
	   } // End check if they have giveaway plugin  	
	     echo '</li>';
		
	} else { // Donate Module is NOT active
		
		if(function_exists('cp_lottery_show_logs')){	
		$cp_lottery1_user_entries = get_user_meta($user->ID, 'lottery1_entries', true);
		$cp_lottery2_user_entries = get_user_meta($user->ID, 'lottery2_entries', true);
		$cp_lottery3_user_entries = get_user_meta($user->ID, 'lottery3_entries', true);
		$cp_lottery4_user_entries = get_user_meta($user->ID, 'lottery4_entries', true);
		$cp_lottery5_user_entries = get_user_meta($user->ID, 'lottery5_entries', true);
		$cp_bet1_pts = get_user_meta($user->ID, 'gamble1_bet_pts', true);
		$cp_bet2_pts = get_user_meta($user->ID, 'gamble2_bet_pts', true);
		$cp_bet3_pts = get_user_meta($user->ID, 'gamble3_bet_pts', true);
		$cp_bet4_pts = get_user_meta($user->ID, 'gamble4_bet_pts', true);
		$cp_bet5_pts = get_user_meta($user->ID, 'gamble5_bet_pts', true);
		$all_lottery_n_bet_active = get_option('cp_lottery1_onoff') + get_option('cp_lottery2_onoff') + get_option('cp_lottery3_onoff') + get_option('cp_lottery4_onoff') + get_option('cp_lottery5_onoff') + get_option('cp_gamble1_onoff') + get_option('cp_gamble2_onoff') + get_option('cp_gamble3_onoff') + get_option('cp_gamble4_onoff') + get_option('cp_gamble5_onoff');
			  
		  if($all_lottery_n_bet_active > 0){
		    echo '<li>'; ?><a href="<?php bloginfo('url'); echo '/'.BP_MEMBERS_SLUG.'/'; echo bp_loggedin_user_username(); echo '/'.$bp->cubepoint->slug.'/'; ?>"><?php echo cp_displayPoints();
		  } else {
		    echo '<li class="no-arrow">'; ?><a href="<?php bloginfo('url'); echo '/'.BP_MEMBERS_SLUG.'/'; echo bp_loggedin_user_username(); echo '/'.$bp->cubepoint->slug.'/'; ?>"><?php echo cp_displayPoints().'</a>';
		  }
		  

		} else {
			
		  echo '<li class="no-arrow">'; ?><a href="<?php bloginfo('url'); echo '/'.BP_MEMBERS_SLUG.'/'; echo bp_loggedin_user_username(); echo '/'.$bp->cubepoint->slug.'/'; ?>"><?php echo cp_displayPoints().'</a>';
		  
		}
		
		if(function_exists('cp_lottery_show_logs')) { // see if they have my plugin
		 if($all_lottery_n_bet_active > 0 ){ // Start Menu Items
		  $totalbetnlottos2enter = 0;
		  if($cp_lottery1_user_entries < 1 && get_option('cp_lottery1_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery2_user_entries < 1 && get_option('cp_lottery2_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery3_user_entries < 1 && get_option('cp_lottery3_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery4_user_entries < 1 && get_option('cp_lottery4_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_lottery5_user_entries < 1 && get_option('cp_lottery5_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet1_pts < 1 && get_option('cp_gamble1_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet2_pts < 1 && get_option('cp_gamble2_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet3_pts < 1 && get_option('cp_gamble3_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet4_pts < 1 && get_option('cp_gamble4_onoff') == 1){ $totalbetnlottos2enter++; }
		  if($cp_bet5_pts < 1 && get_option('cp_gamble5_onoff') == 1){ $totalbetnlottos2enter++; }
		 	 
		   if($totalbetnlottos2enter > 0){echo '&nbsp;<span id="cblotobetson">'.$totalbetnlottos2enter.'</span>&nbsp;</a>';} else { echo '</a>'; }
		   echo '<ul>';
		  		
		   if(get_option('cp_lottery1_onoff') == 1){
			if($cp_lottery1_user_entries < 1){ $title = get_option('bp_lottery1_open_cp_bp'); }
			if($cp_lottery1_user_entries > 0){ $title = get_option('bp_lottery1_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery1_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery2_onoff') == 1){
			if($cp_lottery2_user_entries < 1){ $title = get_option('bp_lottery2_open_cp_bp'); }
			if($cp_lottery2_user_entries > 0){ $title = get_option('bp_lottery2_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery2_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery3_onoff') == 1){
			if($cp_lottery3_user_entries < 1){ $title = get_option('bp_lottery3_open_cp_bp'); }
			if($cp_lottery3_user_entries > 0){ $title = get_option('bp_lottery3_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery3_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery4_onoff') == 1){
			if($cp_lottery4_user_entries < 1){ $title = get_option('bp_lottery4_open_cp_bp'); }
			if($cp_lottery4_user_entries > 0){ $title = get_option('bp_lottery4_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery4_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_lottery5_onoff') == 1){
			if($cp_lottery5_user_entries < 1){ $title = get_option('bp_lottery5_open_cp_bp'); }
			if($cp_lottery5_user_entries > 0){ $title = get_option('bp_lottery5_entered_cp_bp'); }
			echo '<li><a href="'.get_option('bp_lottery5_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble1_onoff') == 1){
		   	   if($cp_bet1_pts < 1){ $title = get_option('bp_bet1_open_cp_bp'); }
		   	   if($cp_bet1_pts > 0){ $title = get_option('bp_bet1_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet1_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble2_onoff') == 1){
		   	   if($cp_bet2_pts < 1){ $title = get_option('bp_bet2_open_cp_bp'); }
		   	   if($cp_bet2_pts > 0){ $title = get_option('bp_bet2_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet2_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble3_onoff') == 1){
		   	   if($cp_bet3_pts < 1){ $title = get_option('bp_bet3_open_cp_bp'); }
		   	   if($cp_bet3_pts > 0){ $title = get_option('bp_bet3_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet3_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble4_onoff') == 1){
		   	   if($cp_bet4_pts < 1){ $title = get_option('bp_bet4_open_cp_bp'); }
		   	   if($cp_bet4_pts > 0){ $title = get_option('bp_bet4_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet4_url_cp_bp').'">'.$title.'</a></li>';
		   }
		   if(get_option('cp_gamble5_onoff') == 1){
		   	   if($cp_bet5_pts < 1){ $title = get_option('bp_bet5_open_cp_bp'); }
		   	   if($cp_bet5_pts > 0){ $title = get_option('bp_bet5_entered_cp_bp'); }
		   	   echo '<li><a href="'.get_option('bp_bet5_url_cp_bp').'">'.$title.'</a></li>';
		   }
			
		 } // if any giveaways / bet are active
	    echo '</ul>';
	   } // End check if they have giveaway plugin  
	     echo '</li>';
	} // End if donate module is active
	if(function_exists('cp_lottery_show_logs')){ echo '<style>#cblotobetson {background-color:#fff;-moz-border-radius:3px;-webkit-border-radius:3px;-khtml-border-radius:3px;border-radius:3px;color:#000;margin-left:2px;padding:0 6px 0 6px;}</style>'; }
 }	
}

/**
 * my_bp_create_group_add_cppoints()
 *
 * Add Points for creating a group
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_create_group_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_group_create', $bp->loggedin_user->id, get_option('bp_create_group_add_cp_bp'), "");
	}

}

/**
 * my_bp_delete_group_add_cppoints()
 *
 * Remove points for deleting a group
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_delete_group_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}
	
	if( $bp->loggedin_user->is_super_admin ){
		return false;
	}

	if ($is_spammer == false) {
		cp_points('cp_bp_group_delete', $bp->loggedin_user->id, get_option('bp_delete_group_add_cp_bp'), "");
	}

}

/**
 * my_bp_update_post_add_cppoints()
 *
 * Add Points for a update
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_update_post_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
		    
			$is_spammer = true;
			
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_update', $bp->loggedin_user->id, get_option('bp_update_post_add_cp_bp'), "");
	}

}

/**
 * my_bp_join_group_add_cppoints()
 *
 * Points for Joining a group
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_join_group_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_group_joined', $bp->loggedin_user->id, get_option('bp_join_group_add_cp_bp'), "");
	}

}

/**
 * my_bp_leave_group_add_cppoints()
 *
 * Points for Leaving a group
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_leave_group_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_group_left', $bp->loggedin_user->id, get_option('bp_leave_group_add_cp_bp'), "");
	}

}


/**
 * my_bp_update_comment_add_cppoints()
 *
 * Add Points for a comment or reply
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_update_comment_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_reply', $bp->loggedin_user->id, get_option('bp_update_comment_add_cp_bp'), "");
	}

}

/**
 * my_bp_update_group_add_cppoints()
 *
 * Add Points for a GROUP comment or reply
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_update_group_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_group_reply', $bp->loggedin_user->id, get_option('bp_update_group_add_cp_bp'), "");
	}

}

/**
 * my_bp_delete_comment_add_cppoints()
 *
 * Remove points for comment deletion
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_delete_comment_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if( $bp->loggedin_user->is_super_admin ){
		return false;
	}

	if ($is_spammer == false) {
		cp_points('cp_bp_update_removed', $bp->loggedin_user->id, get_option('bp_delete_comment_add_cp_bp'), "");
	}
}

/**
 * my_bp_friend_add_cppoints()
 *
 * Add Points for a completed Friend Request
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_friend_add_cppoints($friendship_id, $inviter_id, $invitee_id) {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach( $bpcpspamlist as $spammer_id ) {

		if($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}
	}

	if ($is_spammer == false) {
		cp_points('cp_bp_new_friend', $inviter_id, get_option('bp_friend_add_cp_bp'), "");
		cp_points('cp_bp_new_friend', $invitee_id, get_option('bp_friend_add_cp_bp'), "");		
	}

}

/**
 * my_bp_friend_delete_add_cppoints()
 *
 * Remove points for Canceled Friendship 
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_friend_delete_add_cppoints($friendship_id, $initiator_userid, $friend_userid) {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if($is_spammer == false) {
		cp_points('cp_bp_lost_friend', $initiator_userid, get_option('bp_friend_delete_add_cp_bp'), "");
		cp_points('cp_bp_lost_friend', $friend_userid, get_option('bp_friend_delete_add_cp_bp'), "");		
	}
}


/**
 * my_bp_forum_new_topic_add_cppoints()
 *
 * Add Points New Group Forum Topic (See FAQ in readme.txt for more info)
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_forum_new_topic_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_new_group_forum_topic', $bp->loggedin_user->id, get_option('bp_forum_new_topic_add_cp_bp'), "");		
	}

}

/**
 * my_bp_forum_new_post_add_cppoints()
 *
 * Add Points New Group Forum Post
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_forum_new_post_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_new_group_forum_post', $bp->loggedin_user->id, get_option('bp_forum_new_post_add_cp_bp'), "");		
	}

}

/**
 * my_bp_forum_edit_topic_add_cppoints()
 *
 * POINTS FIX for New Forum Topic Edit
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_forum_edit_topic_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_new_group_forum_post_edit', $bp->loggedin_user->id, -get_option('bp_forum_new_post_add_cp_bp'), "");
	}

}

/**
 * my_bp_forum_edit_post_add_cppoints()
 *
 * POINTS FIX for Forum Post Editit
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_forum_edit_post_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_new_group_forum_post_edit', $bp->loggedin_user->id, -get_option('bp_forum_new_post_add_cp_bp'), "");
	}

}

/**
 * my_bp_avatar_add_cppoints()
 *
 * Add Points Avatar Upload
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_avatar_add_cppoints() {

	global $bp, $wpdb;
	define('CUBEPTS3', $wpdb->prefix . 'cp');
	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );
	$avatar_results = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS3." WHERE type='cp_bp_avatar_uploaded' AND uid = ".$bp->loggedin_user->id);

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}
	}
	
	if($is_spammer == false) {

		if($avatar_results < 1) {
			cp_points('cp_bp_avatar_uploaded', $bp->loggedin_user->id, get_option('bp_avatar_add_cp_bp'), "");			
		}

	}
}

/**
 * my_bp_group_avatar_add_cppoints()
 *
 * Add Points Group Avatar Upload
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_group_avatar_add_cppoints() {

	global $bp, $wpdb;

	define('BBCPDB', $wpdb->prefix . 'bp_activity');
	define('CUBEPTS3', $wpdb->prefix . 'cp');

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );
	$bp_cp_xgroups_created = $wpdb->get_var("SELECT COUNT(*) FROM ".BBCPDB." WHERE type='created_group' AND user_id = ".$bp->loggedin_user->id);
	$group_avatar_results = $wpdb->get_var("SELECT COUNT(*) FROM ".CUBEPTS3." WHERE type='cp_bp_group_avatar_uploaded' AND uid = ".$bp->loggedin_user->id);

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}
	
	if ($is_spammer == false) {

		if($bp_cp_xgroups_created > $group_avatar_results) {
			cp_points('cp_bp_group_avatar_uploaded', $bp->loggedin_user->id, get_option('bp_avatar_add_cp_bp'), "");			
		}

	}

}

/**
 * my_bp_pm_cppoints()
 *
 * Add Point Message Sent
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_pm_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_message_sent', $bp->loggedin_user->id, get_option('bp_pm_cp_bp'), "");
	}

}

/**
 * my_bp_bplink_add_cppoints()
 *
 * Add Points for BuddyPress Link Creation
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_bplink_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}
	}

	if ($is_spammer == false) {
		cp_points('cp_bp_link_added', $bp->loggedin_user->id, get_option('bp_bplink_add_cp_bp'), "");
	}

}

/**
 * my_bp_bplinkvote_add_cppoints()
 *
 * Add Points for BuddyPress Link Vote
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_bplinkvote_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_link_voted', $bp->loggedin_user->id, get_option('bp_bplink_vote_add_cp_bp'), "");
	}

}

/**
 * my_bp_bplinkcomment_add_cppoints()
 *
 * Add Points for BuddyPress Link Comment/Update
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_bplinkcomment_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_link_comment', $bp->loggedin_user->id, get_option('bp_bplink_comment_add_cp_bp'), "");
	}
}

/**
 * my_bp_bplink_delete_add_cppoints()
 *
 * Add Points for BuddyPress Link Delete
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_bplink_delete_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_link_delete', $bp->loggedin_user->id, get_option('bp_bplink_delete_add_cp_bp'), "");
	}

}

/**
 * my_bp_gift_given_add_cppoints()
 *
 * Add Points for BuddyPress Gifts
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_gift_given_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_gift_given', $bp->loggedin_user->id, get_option('bp_gift_given_cp_bp'), "");		
	}

}

/**
 * my_bp_gallery_upload_add_cppoints()
 *
 * Add Points for BP Gallery Upload
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_gallery_upload_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_galery_upload', $bp->loggedin_user->id, get_option('bp_gallery_upload_cp_bp'), "");
	}

}

/**
 * my_bp_gallery_delete_add_cppoints()
 *
 * Remove Points for BP Gallery Delete
 * 
 *  @version 1.9.8
 *  @since 1.0
 */
function my_bp_gallery_delete_add_cppoints() {

	global $bp;

	$bpcpspamlist = explode(',' , get_option( 'bp_spammer_cp_bp' ) );

	foreach ( $bpcpspamlist as $spammer_id ) {

		if ($bp->loggedin_user->id == $spammer_id ) {	
			$is_spammer = true;
			break;
		}
		else {
			$is_spammer = false;
		}

	}

	if ($is_spammer == false) {
		cp_points('cp_bp_galery_delete', $bp->loggedin_user->id, get_option('bp_gallery_delete_cp_bp'), "");
	}

}

/**
 * cubepoints_bp_profile() 
 *
 * Adds CubePoints to Profile Page
 * 
 *  @version 1.9.8.2
 *  @since 1.0
 */
function cubepoints_bp_profile() {

	global $bp;

	if(function_exists('cp_displayPoints')){ ?>
		<span class="cubepoints_buddypress"><?php echo cp_displayPoints($bp->displayed_user->id); ?></span>
	<?php }

	if(cp_module_activated('donate')){
		if (is_user_logged_in()) { ?>
			<span class="cupepoints_buddypress_donate"><a href="#" title="<?php _e('Donate', 'cp_buddypress'); ?>" onclick="Javascript:cp_module_donate();" class="thickbox cp_donateLink"><?php _e('Donate Points', 'cp_buddypress'); ?></a></span>
	<?php } }

	if(cp_module_activated('ranks')){ ?>
		<span class="cupepoints_buddypress_rank"><?php _e('Rank', 'cp_buddypress'); ?> - <?php echo cp_module_ranks_getRank($bp->displayed_user->id); ?></span>
	<?php }
	
}

?>