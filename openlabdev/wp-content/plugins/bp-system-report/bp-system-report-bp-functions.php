<?php 


function bp_system_report_pseudo_cron() {
	$cool = new BP_System_Report( time() ); 
	$cool->record();
}
add_action( 'bp_system_report_pseudo_cron_hook', 'bp_system_report_pseudo_cron' );


function bp_system_report_admin_add() {
	if (is_super_admin()) {
		$plugin_page = add_menu_page( __('System Report','bp-system-report'), __('System Report','bp-system-report'), 'manage_options', __FILE__, 'bp_system_report_admin_screen' );
		add_action('admin_print_styles-' . $plugin_page, 'bp_system_report_css');
	}
}
add_action( 'admin_menu', 'bp_system_report_admin_add', 70 );


function bp_system_report_css() {
	wp_enqueue_style( 'bp-system-report-css' );
}


function bp_system_report_schedule() {	
	
	//print "<pre>";
$crons = _get_cron_array();
	echo "<div style='background: #fff;'>";
	
	echo "Now: " . time() . " Scheduled: ";
	$sched = wp_next_scheduled( 'bp_system_report_pseudo_cron_hook' );
	$until = (int)$sched - time();
	echo " Until: " . $until;
	echo "</div>";
	
//	echo wp_get_schedule( 'bp_system_report_pseudo_cron_hook' );
}
//add_action( 'wp_head', 'bp_system_report_schedule' );














function bp_system_report_admin_screen() {
	global $wpdb;
	
	if ( !$report_dates = get_option( 'bp_system_report_log' ) )
		$report_dates = array();
	
	$report_dates = array_reverse($report_dates);
	
	if ( !$a = $_POST['bpsr_a'] ) {
		$a = time();
		$a_data = new BP_System_Report( $a );
	} else {
		$a_key = 'bp_system_report_' . $a;
		if ( !$a_data = get_option( $a_key ) )
			$a_data = "Error";
	}
	
	if ( !$b = $_POST['bpsr_b'] ) {
		$b = $report_dates[0];
	}
	
	$b_key = 'bp_system_report_' . $b;
	if ( !$b_data = get_option( $b_key ) )
		$b_data = "Error";
	
	/* 
	print "<pre>";
	print_r($a_data);
	print_r($b_data);
	print "</pre>"; */
	
	?>

	<div class="wrap">
	    <h2><?php _e( 'System Report', 'bp-group-management' ) ?></h2>
	
		<form action="admin.php?page=bp-system-report/bp-system-report-bp-functions.php" method="post">
			Compare
			<select name="bpsr_b">
				<?php foreach( $report_dates as $date ) : ?>
					<option value="<?php echo $date ?>" <?php echo ($b == $date) ? 'selected="selected"' : '' ?>><?php echo bp_system_report_format_date( $date ); ?></option> 
				<?php endforeach; ?>
			</select>
			with
			<select name="bpsr_a">
					<option value="">Now</option>
				<?php foreach( $report_dates as $date ) : ?>
					<option value="<?php echo $date ?>" <?php echo ($a == $date) ? 'selected="selected"' : '' ?>><?php echo bp_system_report_format_date( $date ); ?></option> 
				<?php endforeach; ?>
			</select>
			
			<input name="Submit" type="submit" value="<?php esc_attr_e('Go'); ?>" />
			
		</form>
		
		<table id="bp-sr-table" cellspacing=0>
		
		<thead>
			<tr>
				<th scope="col"></th>
				<th scope="col"></th>
				<th scope="col"><?php echo bp_system_report_format_date( $b ); ?></th>
				<th scope="col"><?php echo (!$_POST['bpsr_a']) ? "Now" :  bp_system_report_format_date( $a ) ?></th>
				<th scope="col"><?php _e( 'Change', 'bp-system-report') ?></th>
			</tr>
			
			<tr class="bp-sr-type-label">
			
				<th scope="row" colspan=5>Members</th>
			
			</tr>
			
			<tr>
				<th scope="row"></th>
				<th scope="row">Total</th>
				
				<td><?php echo $b_data->members['total']; ?></td>
				<td><?php echo $a_data->members['total']; ?></td>
				<td><?php bp_system_report_compare( $a_data->members['total'], $b_data->members['total'] ) ?></td>
			</tr>
		
			<tr>
				<th scope="row"></th>
				<th scope="row"># active</th>
				
				<td><?php echo $b_data->members['total_active']; ?></td>
				<td><?php echo $a_data->members['total_active']; ?></td>
				<td><?php bp_system_report_compare( $a_data->members['total_active'], $b_data->members['total_active'] ) ?></td>
			</tr>
		
			<tr>
				<th scope="row"></th>
				<th scope="row">% active</th>
				
				<td><?php echo $b_data->members['percent_active']; ?></td>
				<td><?php echo $a_data->members['percent_active']; ?></td>
				<td><?php bp_system_report_compare( $a_data->members['percent_active'], $b_data->members['percent_active'] ) ?></td>
			</tr>

			<tr>
				<th scope="row"></th>
				<th scope="row">Total friendships</th>
				
				<td><?php echo $b_data->members['friendships']; ?></td>
				<td><?php echo $a_data->members['friendships']; ?></td>
				<td><?php bp_system_report_compare( $a_data->members['friendships'], $b_data->members['friendships'] ) ?></td>
			</tr>
	
			<tr>
				<th scope="row"></th>
				<th scope="row">Avg friendships per member</th>
				
				<td><?php echo round( $b_data->members['average_friendships'], 2 ); ?></td>
				<td><?php echo round( $a_data->members['average_friendships'], 2 ); ?></td>
				<td><?php bp_system_report_compare( $a_data->members['average_friendships'], $b_data->members['average_friendships'] ) ?></td>
			</tr>
			
			<tr class="bp-sr-type-label">
			
				<th scope="row" colspan=5>Groups</th>
			
			</tr>
			
			<tr>
				<th scope="row">all groups</th>
				<th scope="row">Total</th>
				
				<td><?php echo $b_data->groups['total']; ?></td>
				<td><?php echo $a_data->groups['total']; ?></td>
				<td><?php bp_system_report_compare( $a_data->groups['total'], $b_data->groups['total'] ) ?></td>
			</tr>
		
			<tr>
				<th scope="row">all groups</th>
				<th scope="row"># active</th>
				
				
				<?php 	$a_types = $a_data->groups['active'];
						
						$a_active = 0;
						foreach( $a_types as $t ) {
							$a_active += (int)$t;
						}
						
						$b_types = $b_data->groups['active'];
						
						$b_active = 0;
						foreach( $b_types as $t ) {
							$b_active += (int)$t;
						}
				?>
				
				<td><?php echo $b_active; ?></td>
				<td><?php echo $a_active; ?></td>
				<td><?php bp_system_report_compare( $a_active, $b_active ) ?></td>
			</tr>
		
			<tr>
				<th scope="row">all groups</th>
				<th scope="row">% active</th>
				
				<?php	
						$a_p = bp_system_report_percentage( $a_active/$a_data->groups['total'] );
						$b_p = bp_system_report_percentage( $b_active/$b_data->groups['total'] );
						
				?>
				<td><?php echo $b_p; ?></td>
				<td><?php echo $a_p; ?></td>
				<td><?php bp_system_report_compare( $a_p, $b_p ) ?></td>
			</tr>

					
			<tr>
				<th scope="row"><?php _e( "all groups", 'bp-system-report' ) ?></th>
				<th scope="row">total group memberships</th>
				
				
				<?php 	$a_types = $a_data->groups['members'];
						
						$a_members = 0;
						foreach( $a_types as $t ) {
							$a_members += (int)$t;
						}
						
						$b_types = $b_data->groups['members'];
						
						$b_members = 0;
						foreach( $b_types as $t ) {
							$b_members += (int)$t;
						}
				?>
				
				<td><?php echo $b_members ?></td>
				<td><?php echo $a_members ?></td>
				<td><?php bp_system_report_compare( $a_members, $b_members ) ?></td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e( "all groups", 'bp-system-report' ) ?></th>
				<th scope="row">average group membership</th>
				
				
				<?php	
						$a_p = $a_members/$a_data->groups['total'];
						$b_p = $b_members/$b_data->groups['total'];
						
				?>
				<td><?php echo round( $b_p, 2 ); ?></td>
				<td><?php echo round( $a_p, 2 ); ?></td>
				<td><?php bp_system_report_compare( $a_p, $b_p ) ?></td>
			</tr>
		
		
			<?php $type_array = array( 'public', 'private', 'hidden' ); ?>
			
			<?php foreach( $type_array as $type ) : ?>
			<tr class="padder"></tr>
		
			<tr>
				<th scope="row"><?php _e( "$type groups", 'bp-system-report' ) ?></th>
				<th scope="row">Total</th>
				
				<td><?php echo $b_data->groups['types'][$type]; ?></td>
				<td><?php echo $a_data->groups['types'][$type]; ?></td>
				<td><?php bp_system_report_compare( $a_data->groups['types'][$type], $b_data->groups['types'][$type] ) ?></td>
			</tr>
		
			<tr>
				<th scope="row"><?php _e( "$type groups", 'bp-system-report' ) ?></th>
				<th scope="row">as % of total groups</th>
				
				<td><?php echo bp_system_report_percentage( $b_data->groups['types'][$type]/$b_data->groups['total'] ); ?></td>
				<td><?php echo bp_system_report_percentage( $a_data->groups['types'][$type]/$a_data->groups['total'] ); ?></td>
				<td><?php bp_system_report_compare( bp_system_report_percentage( $a_data->groups['types'][$type]/$a_data->groups['total'] ), bp_system_report_percentage( $b_data->groups['types'][$type]/$b_data->groups['total'] ) ) ?></td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e( "$type groups", 'bp-system-report' ) ?></th>
				<th scope="row"># active</th>
				
				<td><?php echo $b_data->groups['active'][$type] ?></td>
				<td><?php echo $a_data->groups['active'][$type] ?></td>
				<td><?php bp_system_report_compare( $a_data->groups['active'][$type], $b_data->groups['active'][$type] ); ?></td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e( "$type groups", 'bp-system-report' ) ?></th>
				<th scope="row">% active</th>
				
				<td><?php echo bp_system_report_percentage( $b_data->groups['active'][$type], $b_data->groups['types'][$type] ); ?></td>
				<td><?php echo bp_system_report_percentage( $a_data->groups['active'][$type] / $a_data->groups['types'][$type] ); ?></td>
				<td><?php bp_system_report_compare( bp_system_report_percentage( $a_data->groups['active'][$type] / $a_data->groups['types'][$type] ), bp_system_report_percentage( $b_data->groups['active'][$type] / $b_data->groups['types'][$type] ) ) ?></td>
			</tr>
		
			<tr>
				<th scope="row"><?php _e( "$type groups", 'bp-system-report' ) ?></th>
				<th scope="row">total group memberships</th>
				
				<td><?php echo $b_data->groups['members'][$type]; ?></td>
				<td><?php echo $a_data->groups['members'][$type]; ?></td>
				<td><?php bp_system_report_compare( $a_data->groups['members'][$type], $b_data->groups['members'][$type] ) ?></td>
			</tr>
			
			<tr>
				<th scope="row"><?php _e( "$type groups", 'bp-system-report' ) ?></th>
				<th scope="row">average group membership</th>
				
				<td><?php echo round( $b_data->groups['members'][$type] / $b_data->groups['types'][$type], 2 ) ?></td>
				<td><?php echo round ( $a_data->groups['members'][$type] / $a_data->groups['types'][$type], 2 ) ?></td>
				<td><?php bp_system_report_compare( $a_data->groups['members'][$type] / $a_data->groups['types'][$type], $b_data->groups['members'][$type] / $b_data->groups['types'][$type] ) ?></td>
			</tr>
			<?php endforeach; ?>	
		
			
				
		
	
			<tr class="bp-sr-type-label">
			
				<th scope="row" colspan=5>Blogs</th>
			
			</tr>
			
			<tr>
				<th scope="row"></th>
				<th scope="row">Total</th>
				
				<td><?php echo $b_data->blogs['total']; ?></td>
				<td><?php echo $a_data->blogs['total']; ?></td>
				<td><?php bp_system_report_compare( $a_data->blogs['total'], $b_data->blogs['total'] ) ?></td>
			</tr>
		
			<tr>
				<th scope="row"></th>
				<th scope="row"># active</th>
				
				<td><?php echo $b_data->blogs['total_active']; ?></td>
				<td><?php echo $a_data->blogs['total_active']; ?></td>
				<td><?php bp_system_report_compare( $a_data->blogs['total_active'], $b_data->blogs['total_active'] ) ?></td>
			</tr>
		
			<tr>
				<th scope="row"></th>
				<th scope="row">% active</th>
				
				<td><?php echo $b_data->blogs['percent_active']; ?></td>
				<td><?php echo $a_data->blogs['percent_active']; ?></td>
				<td><?php bp_system_report_compare( $a_data->blogs['percent_active'], $b_data->blogs['percent_active'] ) ?></td>
			</tr>
			
		</thead>
		
		
		
		
		</table>
		
		
		
		
		<pre>
		<?php $cool = new BP_System_Report; 
			/*if ( $cool->record() )
				echo "Dope"; */
		
		?>
		<?php /* print_r($cool); */   ?>
		</pre>
	</div>
	<?php
}

class BP_System_Report {
	var $members;
	var $groups;
	var $blogs;
	
	var $date;

	function bp_system_report( $date ) {
		if ( !$report_dates = get_option( 'bp_system_report_log' ) )
			$last_report = time();
		else
			$last_report = array_pop( $report_dates );
		
				
		/* Members */
		$members_array = bp_core_get_users( array( 'per_page' => 10000 ) );
		$m = array();
		$members = $members_array['users'];
		
		$m['total'] = count($members);
		
		$active_counter = 0;
		$friends_counter = 0;
		foreach( $members as $member ) {
	
			/* Active since last report */
			$last = strtotime($member->last_activity);
			$now = time();
			$since = $now - $last_report;

			if ( $now - $last < $since )
				$active_counter++;

			$friends_counter += (int)friends_get_total_friend_count( $member->id );
		}
		
		$m['total_active'] = $active_counter;
		$m['friendships'] = $friends_counter;
		$m['average_friendships'] = $friends_counter/$m['total'];
		$m['percent_active'] = bp_system_report_percentage($counter/$m['total']);
				
		$this->members = $m;
		
		
		/* Groups */
		$groups_array = groups_get_groups( array( 'per_page' => 10000 ) );
		$m = array();
		$groups = $groups_array['groups'];
		
		$m['total'] = count($groups);
		
		$counter = 0;
		
		$type_counter = array('public' => 0, 'hidden' => 0, 'private' => 0);
		$active_counter = array('public' => 0, 'hidden' => 0, 'private' => 0);
		$member_counter = array('public' => 0, 'hidden' => 0, 'private' => 0);
		
		foreach( $groups as $group ) {
			$member_counter[$group->status] += $group->total_member_count;
			$type_counter[$group->status]++;

			/* Active since last report */
			$last = strtotime($group->last_activity);
			$now = time();
			$since = $now - $last_report;

			if ( $now - $last < $since )
				$active_counter[$group->status]++;

		}
		$m['members'] = $member_counter;
		$m['types'] = $type_counter;
		$m['active'] = $active_counter;
	//	$m['percent_active'] = bp_system_report_percentage($counter/$m['total']);
				
		$this->groups = $m;
		
		
		/* Blogs */
		$blogs_array = bp_blogs_get_blogs( array( 'per_page' => 10000 ) );
		$m = array();
		$blogs = $blogs_array['blogs'];
		
		$m['total'] = count($blogs);
		
		/* Active in last week */
		$counter = 0;
		
		foreach( $blogs as $blog ) {
			$last = strtotime($blog->last_activity);
			$now = time();
			if ( $now - $last < 604800 )
				$counter++;
		}
		$m['total_active'] = $counter;
		$m['percent_active'] = bp_system_report_percentage($counter/$m['total']);
				
		$this->blogs = $m;
		
		$this->date = time();
	}
	
	function record() {
		if ( !get_option( 'bp_system_report_log' ) )
			$log = array();
		else
			$log = get_option( 'bp_system_report_log' );
			
		$time = time();
		$log[] = $time;
		
		if ( !update_option( 'bp_system_report_log', $log ) )
			return false;
		
		$name = 'bp_system_report_' . $time;
		
		if ( !add_option( $name, $this, '', 'no' ) )
			return false;
		
		return true;
	}
}

function bp_system_report_compare( $a, $b ) {
	
	if ( strpos( $a, '%' ) ) {
		$diff = (int)$a - (int)$b;
		
		if ( $diff > 0 )
			$diff = '+' . $diff;
			
		if ( $diff == 0 )
			$diff = '-';
		else
			$diff .= '%';
		
		$pct_diff = '';
	} else {
		$diff = round( $a - $b, 2 );
		if ( $diff > 0 )
			$diff = '+' . $diff;
		
		if ( $b == $a ) {
			$pct_diff = ' / -';
		} else if ( $b == 0 ) {
			$pct_diff = ' / -';
		} else {
			$pct_diff = ' / ' . bp_system_report_percentage( $diff/$b );
		}
		
	}

	echo $diff . $pct_diff;
}

function bp_system_report_percentage( $n ) {
	$n = $n * 100;
	$n .= '%';
	return round($n, 2) . '%';
}

function bp_system_report_format_date( $timestamp ) {
	$today = strftime( "%e %h %G" );
	
	$thedate = strftime( "%e %h %G", $timestamp );
	
	if ( $today == $thedate )
		$date = __( 'Today', 'bp-system-report' );
	else
		$date = $thedate;
	
	return $date . ' ' . strftime( "%R", $timestamp );
}

?>