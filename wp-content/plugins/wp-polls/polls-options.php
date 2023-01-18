<?php
### Check Whether User Can Manage Polls
if( ! current_user_can( 'manage_polls' ) ) {
	die( 'Access Denied' );
}


### Variables Variables Variables
$base_name = plugin_basename( 'wp-polls/polls-options.php' );
$base_page = 'admin.php?page=' . $base_name;
$id = isset( $_GET['id'] ) ? (int) sanitize_key( $_GET['id'] ) : 0;


### Get Poll Bar Images
$pollbar_path = WP_PLUGIN_DIR . '/wp-polls/images';
$poll_bars = array();
if( $handle = @opendir( $pollbar_path ) ) {
	while( false !== ( $filename = readdir( $handle ) ) ) {
		if( substr( $filename, 0, 1 ) !== '.' && substr( $filename, 0, 2 ) !== '..' ) {
			if( is_dir( $pollbar_path.'/'.$filename ) ) {
				$poll_bars[$filename] = getimagesize( $pollbar_path . '/' . $filename . '/pollbg.gif' );
			}
		}
	}
	closedir( $handle );
}

### If Form Is Submitted
if( isset($_POST['Submit']) && $_POST['Submit'] ) {
	check_admin_referer('wp-polls_options');
	$poll_bar_style             = isset( $_POST['poll_bar_style'] ) && in_array( $_POST['poll_bar_style'], array_merge( array_keys( $poll_bars ), array( 'use_css' ) ), true ) ? $_POST['poll_bar_style'] : 'default';
	$poll_bar_background        = isset( $_POST['poll_bar_bg'] ) ? substr( strip_tags( trim( $_POST['poll_bar_bg'] ) ), 0, 6 ) : '000000';
	$poll_bar_border            = isset( $_POST['poll_bar_border'] ) ? substr( strip_tags( trim( $_POST['poll_bar_border'] ) ), 0, 6 ) : '000000';
	$poll_bar_height            = isset( $_POST['poll_bar_height'] ) ? (int) sanitize_key( $_POST['poll_bar_height'] ) : 10;
	$poll_bar                   = array(
		'style'         => $poll_bar_style,
		'background'    => $poll_bar_background,
		'border'        => $poll_bar_border,
		'height'        => $poll_bar_height
	);
	$poll_ajax_style            = array(
		'loading'   => isset( $_POST['poll_ajax_style_loading'] ) ? (int) sanitize_key( $_POST['poll_ajax_style_loading'] ) : 1,
		'fading'    => isset( $_POST['poll_ajax_style_fading'] ) ? (int) sanitize_key( $_POST['poll_ajax_style_fading'] ) : 1
	);
	$poll_ans_sortby            = isset( $_POST['poll_ans_sortby'] ) && in_array( $_POST['poll_ans_sortby'], array( 'polla_votes', 'polla_aid', 'polla_answers', 'RAND()' ), true ) ? $_POST['poll_ans_sortby'] : 'polla_aid';
	$poll_ans_sortorder         = isset( $_POST['poll_ans_sortorder'] ) && in_array( $_POST['poll_ans_sortorder'], array( 'asc', 'desc' ), true ) ? $_POST['poll_ans_sortorder'] : 'asc';
	$poll_ans_result_sortby     = isset( $_POST['poll_ans_result_sortby'] ) && in_array( $_POST['poll_ans_result_sortby'], array( 'polla_votes', 'polla_aid', 'polla_answers', 'RAND()' ), true ) ? $_POST['poll_ans_result_sortby'] : 'polla_votes';
	$poll_ans_result_sortorder  = isset( $_POST['poll_ans_result_sortorder'] ) && in_array( $_POST['poll_ans_result_sortorder'], array( 'asc', 'desc' ), true ) ? $_POST['poll_ans_result_sortorder'] : 'desc';
	$poll_archive_perpage       = isset( $_POST['poll_archive_perpage'] ) ? (int) sanitize_key( $_POST['poll_archive_perpage'] ) : 0;
	$poll_archive_displaypoll   = isset( $_POST['poll_archive_displaypoll'] ) ? (int) sanitize_key( $_POST['poll_archive_displaypoll'] ) : 0;
	$poll_archive_url           = isset( $_POST['poll_archive_url'] ) ? esc_url_raw( strip_tags( trim( $_POST['poll_archive_url'] ) ) ) : '';
	$poll_currentpoll           = isset( $_POST['poll_currentpoll'] ) ? (int) sanitize_key( $_POST['poll_currentpoll'] ) : 0;
	$poll_close                 = isset( $_POST['poll_close'] ) ? (int) sanitize_key( $_POST['poll_close'] ) : 0;
	$poll_logging_method        = isset( $_POST['poll_logging_method'] ) ? (int) sanitize_key( $_POST['poll_logging_method'] ) : 0;
	$poll_cookielog_expiry      = isset( $_POST['poll_cookielog_expiry'] ) ? (int) sanitize_key ($_POST['poll_cookielog_expiry'] ) : 0;
	$poll_allowtovote           = isset( $_POST['poll_allowtovote'] ) ? (int) sanitize_key( $_POST['poll_allowtovote'] ) : 0;
	
	// New options we just need a single poll_options
	$poll_options = array();
	$poll_options['ip_header'] = ! empty( $_POST['poll_ip_header'] )  ? sanitize_text_field( $_POST['poll_ip_header'] ) : '';

	$update_poll_queries = array();
	$update_poll_text = array();
	$update_poll_queries[] = update_option('poll_bar', $poll_bar);
	$update_poll_queries[] = update_option('poll_ajax_style', $poll_ajax_style);
	$update_poll_queries[] = update_option('poll_ans_sortby', $poll_ans_sortby);
	$update_poll_queries[] = update_option('poll_ans_sortorder', $poll_ans_sortorder);
	$update_poll_queries[] = update_option('poll_ans_result_sortby', $poll_ans_result_sortby);
	$update_poll_queries[] = update_option('poll_ans_result_sortorder', $poll_ans_result_sortorder);
	$update_poll_queries[] = update_option('poll_archive_perpage', $poll_archive_perpage);
	$update_poll_queries[] = update_option('poll_archive_displaypoll', $poll_archive_displaypoll);
	$update_poll_queries[] = update_option('poll_archive_url', $poll_archive_url);
	$update_poll_queries[] = update_option('poll_currentpoll', $poll_currentpoll);
	$update_poll_queries[] = update_option('poll_close', $poll_close);
	$update_poll_queries[] = update_option('poll_logging_method', $poll_logging_method);
	$update_poll_queries[] = update_option('poll_cookielog_expiry', $poll_cookielog_expiry);
	$update_poll_queries[] = update_option('poll_allowtovote', $poll_allowtovote);
	$update_poll_queries[] = update_option( 'poll_options', $poll_options );
	$update_poll_text[] = __('Poll Bar Style', 'wp-polls');
	$update_poll_text[] = __('Poll AJAX Style', 'wp-polls');
	$update_poll_text[] = __('Sort Poll Answers By Option', 'wp-polls');
	$update_poll_text[] = __('Sort Order Of Poll Answers Option', 'wp-polls');
	$update_poll_text[] = __('Sort Poll Results By Option', 'wp-polls');
	$update_poll_text[] = __('Sort Order Of Poll Results Option', 'wp-polls');
	$update_poll_text[] = __('Number Of Polls Per Page To Display In Poll Archive Option', 'wp-polls');
	$update_poll_text[] = __('Type Of Polls To Display In Poll Archive Option', 'wp-polls');
	$update_poll_text[] = __('Poll Archive URL Option', 'wp-polls');
	$update_poll_text[] = __('Show Poll Achive Link Option', 'wp-polls');
	$update_poll_text[] = __('Current Active Poll Option', 'wp-polls');
	$update_poll_text[] = __('Poll Close Option', 'wp-polls');
	$update_poll_text[] = __('Logging Method', 'wp-polls');
	$update_poll_text[] = __('Cookie And Log Expiry Option', 'wp-polls');
	$update_poll_text[] = __('Allow To Vote Option', 'wp-polls');
	$update_poll_text[] = __( 'Poll Options', 'wp-polls' );
	$i=0;
	$text = '';
	foreach($update_poll_queries as $update_poll_query) {
		if($update_poll_query) {
			$text .= '<p style="color: green;">'.$update_poll_text[$i].' '.__('Updated', 'wp-polls').'</p>';
		}
		$i++;
	}
	if(empty($text)) {
		$text = '<p style="color: red;">'.__('No Poll Option Updated', 'wp-polls').'</p>';
	}
	cron_polls_place();
}

$poll_options = get_option( 'poll_options' );
if ( empty( $poll_options ) ) {
	$poll_options['ip_header'] = '';
}
?>
<script type="text/javascript">
/* <![CDATA[*/
	function set_pollbar_height(height) {
			jQuery("#poll_bar_height").val(height);
	}
	function update_pollbar(where) {
		pollbar_background = "#" + jQuery("#poll_bar_bg").val();
		pollbar_border = "#" + jQuery("#poll_bar_border").val();
		pollbar_height = jQuery("#poll_bar_height").val() + "px";
		if(where  == "background") {
			jQuery("#wp-polls-pollbar-bg").css("background-color", pollbar_background);
		} else if(where == "border") {
			jQuery("#wp-polls-pollbar-border").css("background-color", pollbar_border);
		} else if(where == "style") {
			pollbar_style = jQuery("input[name='poll_bar_style']:checked").val();
			if(pollbar_style == "use_css") {
				jQuery("#wp-polls-pollbar").css("background-image", "none");
			} else {
				jQuery("#wp-polls-pollbar").css("background-image", "url('<?php echo plugins_url('wp-polls/images/'); ?>" + pollbar_style + "/pollbg.gif')");
			}
		}
		jQuery("#wp-polls-pollbar").css({"background-color":pollbar_background, "border":"1px solid " + pollbar_border, "height":pollbar_height});
	}
/* ]]> */
</script>
<?php if(!empty($text)) { echo '<!-- Last Action --><div id="message" class="updated fade"><p>'.$text.'</p></div>'; } ?>
<form id="poll_options_form" method="post" action="<?php echo admin_url('admin.php?page='.plugin_basename(__FILE__)); ?>">
<?php wp_nonce_field('wp-polls_options'); ?>
<div class="wrap">
	<h2><?php _e('Poll Options', 'wp-polls'); ?></h2>
	<!-- Poll Bar Style -->
	<h3><?php _e('Poll Bar Style', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr>
			<th scope="row" valign="top"><?php _e('Poll Bar Style', 'wp-polls'); ?></th>
			<td colspan="2">
				<?php
					$pollbar = get_option('poll_bar');
					$pollbar_url = plugins_url('wp-polls/images');
					if( count( $poll_bars ) > 0 ) {
						foreach( $poll_bars as $filename => $pollbar_info ) {
							echo '<p>'."\n";
							if($pollbar['style'] == $filename) {
								echo '<input type="radio" id="poll_bar_style-'.$filename.'" name="poll_bar_style" value="'.$filename.'" checked="checked" onclick="set_pollbar_height('.$pollbar_info[1].'); update_pollbar(\'style\');" />';
							} else {
								echo '<input type="radio" id="poll_bar_style-'.$filename.'" name="poll_bar_style" value="'.$filename.'" onclick="set_pollbar_height('.$pollbar_info[1].'); update_pollbar(\'style\');" />';
							}
							echo '<label for="poll_bar_style-'.$filename.'">&nbsp;&nbsp;&nbsp;';
							echo '<img src="'.$pollbar_url.'/'.$filename.'/pollbg.gif" height="'.$pollbar_info[1].'" width="100" alt="pollbg.gif" />';
							echo '&nbsp;&nbsp;&nbsp;('.$filename.')</label>';
							echo '</p>'."\n";
						}
					}
				?>
				<input type="radio" id="poll_bar_style-use_css" name="poll_bar_style" value="use_css"<?php checked('use_css', $pollbar['style']); ?> onclick="update_pollbar('style');" /><label for="poll_bar_style-use_css"> <?php _e('Use CSS Style', 'wp-polls'); ?></label>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Poll Bar Background', 'wp-polls'); ?></th>
			<td width="10%" dir="ltr">#<input type="text" id="poll_bar_bg" name="poll_bar_bg" value="<?php echo esc_attr( $pollbar['background'] ); ?>" size="6" maxlength="6" onblur="update_pollbar('background');" /></td>
			<td><div id="wp-polls-pollbar-bg" style="background-color: #<?php echo $pollbar['background']; ?>;"></div></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Poll Bar Border', 'wp-polls'); ?></th>
			<td width="10%" dir="ltr">#<input type="text" id="poll_bar_border" name="poll_bar_border" value="<?php echo esc_attr( $pollbar['border'] ); ?>" size="6" maxlength="6" onblur="update_pollbar('border');" /></td>
			<td><div id="wp-polls-pollbar-border" style="background-color: #<?php echo $pollbar['border']; ?>;"></div></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Poll Bar Height', 'wp-polls'); ?></th>
			<td colspan="2" dir="ltr"><input type="text" id="poll_bar_height" name="poll_bar_height" value="<?php echo $pollbar['height']; ?>" size="2" maxlength="2" onblur="update_pollbar('height');" />px</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Your poll bar will look like this', 'wp-polls'); ?></th>
			<td colspan="2">
				<?php
					if($pollbar['style'] == 'use_css') {
						echo '<div id="wp-polls-pollbar" style="width: 100px; height: '.$pollbar['height'].'px; background-color: #'.$pollbar['background'].'; border: 1px solid #'.$pollbar['border'].'"></div>'."\n";
					} else {
						echo '<div id="wp-polls-pollbar" style="width: 100px; height: '.$pollbar['height'].'px; background-color: #'.$pollbar['background'].'; border: 1px solid #'.$pollbar['border'].'; background-image: url(\''.plugins_url('wp-polls/images/'.$pollbar['style'].'/pollbg.gif').'\');"></div>'."\n";
					}
				?>
			</td>
		</tr>
	</table>

	<!-- Polls AJAX Style -->
	<?php $poll_ajax_style = get_option('poll_ajax_style'); ?>
	<h3><?php _e('Polls AJAX Style', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr>
			<th scope="row" valign="top"><?php _e('Show Loading Image With Text', 'wp-polls'); ?></th>
			<td>
				<select name="poll_ajax_style_loading" size="1">
					<option value="0"<?php selected('0', $poll_ajax_style['loading']); ?>><?php _e('No', 'wp-polls'); ?></option>
					<option value="1"<?php selected('1', $poll_ajax_style['loading']); ?>><?php _e('Yes', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Show Fading In And Fading Out Of Poll', 'wp-polls'); ?></th>
			<td>
				<select name="poll_ajax_style_fading" size="1">
					<option value="0"<?php selected('0', $poll_ajax_style['fading']); ?>><?php _e('No', 'wp-polls'); ?></option>
					<option value="1"<?php selected('1', $poll_ajax_style['fading']); ?>><?php _e('Yes', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
	</table>

	<!-- Sorting Of Poll Answers -->
	<h3><?php _e('Sorting Of Poll Answers', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr>
			<th scope="row" valign="top"><?php _e('Sort Poll Answers By:', 'wp-polls'); ?></th>
			<td>
				<select name="poll_ans_sortby" size="1">
					<option value="polla_votes"<?php selected('polla_votes', get_option('poll_ans_sortby')); ?>><?php _e('Votes Cast', 'wp-polls'); ?></option>
					<option value="polla_aid"<?php selected('polla_aid', get_option('poll_ans_sortby')); ?>><?php _e('Exact Order', 'wp-polls'); ?></option>
					<option value="polla_answers"<?php selected('polla_answers', get_option('poll_ans_sortby')); ?>><?php _e('Alphabetical Order', 'wp-polls'); ?></option>
					<option value="RAND()"<?php selected('RAND()', get_option('poll_ans_sortby')); ?>><?php _e('Random Order', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Sort Order Of Poll Answers:', 'wp-polls'); ?></th>
			<td>
				<select name="poll_ans_sortorder" size="1">
					<option value="asc"<?php selected('asc', get_option('poll_ans_sortorder')); ?>><?php _e('Ascending', 'wp-polls'); ?></option>
					<option value="desc"<?php selected('desc', get_option('poll_ans_sortorder')); ?>><?php _e('Descending', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
	</table>

	<!-- Sorting Of Poll Results -->
	<h3><?php _e('Sorting Of Poll Results', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr>
			<th scope="row" valign="top"><?php _e('Sort Poll Results By:', 'wp-polls'); ?></th>
			<td>
				<select name="poll_ans_result_sortby" size="1">
					<option value="polla_votes"<?php selected('polla_votes', get_option('poll_ans_result_sortby')); ?>><?php _e('Votes Cast', 'wp-polls'); ?></option>
					<option value="polla_aid"<?php selected('polla_aid', get_option('poll_ans_result_sortby')); ?>><?php _e('Exact Order', 'wp-polls'); ?></option>
					<option value="polla_answers"<?php selected('polla_answers', get_option('poll_ans_result_sortby')); ?>><?php _e('Alphabetical Order', 'wp-polls'); ?></option>
					<option value="RAND()"<?php selected('RAND()', get_option('poll_ans_result_sortby')); ?>><?php _e('Random Order', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Sort Order Of Poll Results:', 'wp-polls'); ?></th>
			<td>
				<select name="poll_ans_result_sortorder" size="1">
					<option value="asc"<?php selected('asc', get_option('poll_ans_result_sortorder')); ?>><?php _e('Ascending', 'wp-polls'); ?></option>
					<option value="desc"<?php selected('desc', get_option('poll_ans_result_sortorder')); ?>><?php _e('Descending', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
	</table>

	<!-- Allow To Vote -->
	<h3><?php _e('Allow To Vote', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr>
			<th scope="row" valign="top"><?php _e('Who Is Allowed To Vote?', 'wp-polls'); ?></th>
			<td>
				<select name="poll_allowtovote" size="1">
					<option value="0"<?php selected('0', get_option('poll_allowtovote')); ?>><?php _e('Guests Only', 'wp-polls'); ?></option>
					<option value="1"<?php selected('1', get_option('poll_allowtovote')); ?>><?php _e('Registered Users Only', 'wp-polls'); ?></option>
					<option value="2"<?php selected('2', get_option('poll_allowtovote')); ?>><?php _e('Registered Users And Guests', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
	</table>

	<!-- Logging Method -->
	<h3><?php _e('Logging Method', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr valign="top">
			<th scope="row" valign="top"><?php _e('Poll Logging Method:', 'wp-polls'); ?></th>
			<td>
				<select name="poll_logging_method" size="1">
					<option value="0"<?php selected('0', get_option('poll_logging_method')); ?>><?php _e('Do Not Log', 'wp-polls'); ?></option>
					<option value="1"<?php selected('1', get_option('poll_logging_method')); ?>><?php _e('Logged By Cookie', 'wp-polls'); ?></option>
					<option value="2"<?php selected('2', get_option('poll_logging_method')); ?>><?php _e('Logged By IP', 'wp-polls'); ?></option>
					<option value="3"<?php selected('3', get_option('poll_logging_method')); ?>><?php _e('Logged By Cookie And IP', 'wp-polls'); ?></option>
					<option value="4"<?php selected('4', get_option('poll_logging_method')); ?>><?php _e('Logged By Username', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Expiry Time For Cookie And Log:', 'wp-polls'); ?></th>
			<td><input type="text" name="poll_cookielog_expiry" value="<?php echo (int) esc_attr( get_option( 'poll_cookielog_expiry' ) ); ?>" size="10" /> <?php _e('seconds (0 to disable)', 'wp-polls'); ?></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e( 'Header That Contains The IP:', 'wp-polls' ); ?></th>
			<td><input type="text" name="poll_ip_header" value="<?php echo esc_attr( $poll_options['ip_header'] ); ?>" size="30" /> <?php _e( 'You can leave it blank to use the default', 'wp-polls' ); ?><br /><?php _e( 'Example: REMOTE_ADDR', 'wp-polls' ); ?></td>
		</tr>
	</table>

	<!-- Poll Archive -->
	<h3><?php _e('Poll Archive', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr>
			<th scope="row" valign="top"><?php _e('Number Of Polls Per Page:', 'wp-polls'); ?></th>
			<td><input type="text" name="poll_archive_perpage" value="<?php echo (int) esc_attr( get_option( 'poll_archive_perpage' ) ); ?>" size="2" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Type Of Polls To Display In Poll Archive:', 'wp-polls'); ?></th>
			<td>
				<select name="poll_archive_displaypoll" size="1">
					<option value="1"<?php selected('1', get_option('poll_archive_displaypoll')); ?>><?php _e('Closed Polls Only', 'wp-polls'); ?></option>
					<option value="2"<?php selected('2', get_option('poll_archive_displaypoll')); ?>><?php _e('Opened Polls Only', 'wp-polls'); ?></option>
					<option value="3"<?php selected('3', get_option('poll_archive_displaypoll')); ?>><?php _e('Closed And Opened Polls', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Poll Archive URL:', 'wp-polls'); ?></th>
			<td><input type="text" name="poll_archive_url" value="<?php echo esc_url( get_option( 'poll_archive_url' ) ); ?>" size="50" dir="ltr" /></td>
		</tr>
		<tr>
			<th scope="row" valign="top"><?php _e('Note', 'wp-polls'); ?></th>
			<td><em><?php _e('Only polls\' results will be shown in the Poll Archive regardless of whether the poll is closed or opened.', 'wp-polls'); ?></em></td>
		</tr>
	</table>

	<!-- Current Active Poll -->
	<h3><?php _e('Current Active Poll', 'wp-polls'); ?></h3>
	<table class="form-table">
		 <tr>
			<th scope="row" valign="top"><?php _e('Current Active Poll', 'wp-polls'); ?>:</th>
			<td>
				<select name="poll_currentpoll" size="1">
					<option value="-1"<?php selected(-1, get_option('poll_currentpoll')); ?>><?php _e('Do NOT Display Poll (Disable)', 'wp-polls'); ?></option>
					<option value="-2"<?php selected(-2, get_option('poll_currentpoll')); ?>><?php _e('Display Random Poll', 'wp-polls'); ?></option>
					<option value="0"<?php selected(0, get_option('poll_currentpoll')); ?>><?php _e('Display Latest Poll', 'wp-polls'); ?></option>
					<optgroup>&nbsp;</optgroup>
					<?php
						$polls = $wpdb->get_results("SELECT pollq_id, pollq_question FROM $wpdb->pollsq ORDER BY pollq_id DESC");
						if($polls) {
							foreach($polls as $poll) {
								$poll_question = removeslashes($poll->pollq_question);
								$poll_id = (int) $poll->pollq_id;
								if($poll_id === (int) get_option( 'poll_currentpoll' ) ) {
									echo '<option value="' . $poll_id . '" selected="selected">' . esc_attr( $poll_question ) . '</option>';
								} else {
									echo '<option value="' . $poll_id . '">' . esc_attr( $poll_question ) . '</option>';
								}
							}
						}
					?>
				</select>
			</td>
		</tr>
		 <tr>
			<th scope="row" valign="top"><?php _e('When Poll Is Closed', 'wp-polls'); ?>:</th>
			<td>
				<select name="poll_close" size="1">
					<option value="1"<?php selected(1, get_option('poll_close')); ?>><?php _e('Display Poll\'s Results', 'wp-polls'); ?></option>
					<option value="3"<?php selected(3, get_option('poll_close')); ?>><?php _e('Display Disabled Poll\'s Voting Form', 'wp-polls'); ?></option>
					<option value="2"<?php selected(2, get_option('poll_close')); ?>><?php _e('Do Not Display Poll In Post/Sidebar', 'wp-polls'); ?></option>
				</select>
			</td>
		</tr>
	</table>

	<!-- Submit Button -->
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', 'wp-polls'); ?>" />
	</p>
</div>
</form>
