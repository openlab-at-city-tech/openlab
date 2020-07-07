<?php
/**
 * Create the admin menu for this plugin
 * @param no-param
 * @return no-return
 */
function WtiLikePostAdminMenu() {
	add_options_page('WTI Like Post', __('WTI Like Post', 'wti-like-post'), 'activate_plugins', 'WtiLikePostAdminMenu', 'WtiLikePostAdminContent');
}

add_action('admin_menu', 'WtiLikePostAdminMenu');

/**
 * Pluing settings page
 * @param no-param
 * @return no-return
 */
function WtiLikePostAdminContent() {
	// Creating the admin configuration interface
	global $wpdb, $wti_like_post_db_version;
     
	$excluded_sections = get_option('wti_like_post_excluded_sections');
	$excluded_categories = get_option('wti_like_post_excluded_categories');
	
	if (empty($excluded_sections)) {
		$excluded_sections = array();
	}
	
	if (empty($excluded_categories)) {
		$excluded_categories = array();
	}
?>
<div class="wrap">
     <h2><?php echo __('WTI Like Post', 'wti-like-post') . ' ' . $wti_like_post_db_version;?></h2>
     <br class="clear" />
	
	<div class="metabox-holder has-right-sidebar" id="poststuff">
		<div class="inner-sidebar" id="side-info-column">
			<div class="meta-box-sortables ui-sortable" id="side-sortables">
				<div id="WtiLikePostOptions" class="postbox">
					<div title="Click to toggle" class="handlediv"><br /></div>
					<h3 class="hndle"><?php echo __('Support / Manual / Upgradation', 'wti-like-post'); ?></h3>
					<div class="inside">
						<p style="margin:15px 0px;"><?php echo __('For any suggestion / query / issue / requirement, please feel free to drop an email to', 'wti-like-post'); ?> <a href="mailto:support@webtechideas.com?subject=WTI Like Post">support@webtechideas.com</a>.</p>
						<p style="margin:15px 0px;"><?php echo __('Get the', 'wti-like-post'); ?> <a href="https://www.webtechideas.in/wti-like-post-plugin/3/" target="_blank"><?php echo __('Lite Manual here', 'wti-like-post'); ?></a>.</p>
						<p style="margin:15px 0px;"><?php echo __('Get the', 'wti-like-post'); ?> <a href="https://www.webtechideas.in/product/wti-like-post-pro/" target="_blank"><?php echo __('PRO Version here', 'wti-like-post'); ?></a> <?php echo __('for more advanced features', 'wti-like-post'); ?>.</p>
						<p style="margin:15px 0px;"><?php echo __('Get the', 'wti-like-post'); ?> <a href="https://www.webtechideas.in/product/wti-like-post-pro/" target="_blank"><?php echo __('PRO Manual here', 'wti-like-post'); ?></a> <?php echo __('for a complete list of features', 'wti-like-post'); ?>.</p>
					</div>
				</div>
				
				<div id="WtiLikePostOptions" class="postbox">
					<div title="Click to toggle" class="handlediv"><br /></div>
					<h3 class="hndle"><?php echo __('Review / Donation', 'wti-like-post'); ?></h3>
					<div class="inside">
						<p style="margin:15px 0px;">
							<?php echo __('Please feel free to add your reviews on', 'wti-like-post'); ?> <a href="http://wordpress.org/support/view/plugin-reviews/wti-like-post" target="_blank"><?php echo __('Wordpress', 'wti-like-post');?></a>.</p>
						</p>
						<p style="margin:15px 0px;">
							<?php echo __('There has been a lot of effort put behind the development of this plugin. Please consider donating towards this plugin development.', 'wti-like-post');?>
							<form method="post" action="https://www.paypal.com/cgi-bin/webscr" target="_blank">
								<?php _e('Amount', 'wti-like-post'); ?> $ <input type="text" value="" title="Other donate" size="5" name="amount"><br />
								<input type="hidden" value="_xclick" name="cmd" />
								<input type="hidden" value="support@webtechideas.com" name="business" />
								<input type="hidden" value="WTI Like Post" name="item_name" />
								<input type="hidden" value="USD" name="currency_code" />
								<input type="hidden" value="0" name="no_shipping" />
								<input type="hidden" value="1" name="no_note" />
								<input type="hidden" value="3FWGC6LFTMTUG" name="mrb" />
								<input type="hidden" value="IC_Sample" name="bn" />
								<input type="hidden" value="https://www.webtechideas.in/thanks/" name="return" />
								<input type="image" alt="Make payments with payPal - it's fast, free and secure!" name="submit" src="https://www.paypal.com/en_US/i/btn/x-click-but11.gif" />
							</form>
						</p>
					</div>
				</div>
			</div>
		</div>

		<div id="post-body">
			<div id="post-body-content">
				<div id="WtiLikePostOptions" class="postbox">
					<h3><?php echo __('Configuration', 'wti-like-post'); ?></h3>
					<div class="inside">
						<form method="post" action="options.php" id="wtilp_admin_settings">
							<?php settings_fields('wti_like_post_options'); ?>
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><label for="drop_settings_table_no"><?php _e('Remove plugin settings and table on plugin un-install', 'wti-like-post'); ?></label></th>
									<td>
										<input type="radio" name="wti_like_post_drop_settings_table" id="drop_table_yes" value="1" <?php if (1 == get_option('wti_like_post_drop_settings_table')) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_drop_settings_table" id="drop_table_no" value="0" <?php if ((0 == get_option('wti_like_post_drop_settings_table')) || ('' == get_option('wti_like_post_drop_settings_table'))) { echo 'checked'; } ?> /> <?php echo __('No', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select whether the plugin settings and table will be removed when you uninstall the plugin. Setting this to NO is helpful if you are planning to reuse this in future with old data or upgrade to PRO version.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Voting Period', 'wti-like-post'); ?></label></th>
									<td>
										<?php
										$voting_period = get_option('wti_like_post_voting_period');
										?>
										<select name="wti_like_post_voting_period" id="wti_like_post_voting_period">
											<option value="0"><?php echo __('Always can vote', 'wti-like-post'); ?></option>
											<option value="once" <?php if ("once" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Only once', 'wti-like-post'); ?></option>
											<option value="1" <?php if ("1" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One day', 'wti-like-post'); ?></option>
											<option value="2" <?php if ("2" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Two days', 'wti-like-post'); ?></option>
											<option value="3" <?php if ("3" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Three days', 'wti-like-post'); ?></option>
											<option value="7" <?php if ("7" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One week', 'wti-like-post'); ?></option>
											<option value="14" <?php if ("14" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Two weeks', 'wti-like-post'); ?></option>
											<option value="21" <?php if ("21" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Three weeks', 'wti-like-post'); ?></option>
											<option value="1m" <?php if ("1m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One month', 'wti-like-post'); ?></option>
											<option value="2m" <?php if ("2m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Two months', 'wti-like-post'); ?></option>
											<option value="3m" <?php if ("3m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Three months', 'wti-like-post'); ?></option>
											<option value="6m" <?php if ("6m" == $voting_period) echo "selected='selected'"; ?>><?php echo __('Six Months', 'wti-like-post'); ?></option>
											<option value="1y" <?php if ("1y" == $voting_period) echo "selected='selected'"; ?>><?php echo __('One Year', 'wti-like-post'); ?></option>
										</select>
										<span class="description"><?php _e('Select the voting period after which user can vote again.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Voting Style', 'wti-like-post'); ?></label></th>
									<td>
										<?php
										$voting_style = get_option('wti_like_post_voting_style');
										?>
										<select name="wti_like_post_voting_style" id="wti_like_post_voting_style">
											<option value="style1" <?php if ("style1" == $voting_style) echo "selected='selected'"; ?>><?php echo __('Style1', 'wti-like-post'); ?></option>
											<option value="style2" <?php if ("style2" == $voting_style) echo "selected='selected'"; ?>><?php echo __('Style2', 'wti-like-post'); ?></option>
											<option value="style3" <?php if ("style3" == $voting_style) echo "selected='selected'"; ?>><?php echo __('Style3', 'wti-like-post'); ?></option>
										</select>
										<span class="description"><?php _e('Select the voting style from 3 available options with 3 different sets of images.', 'wti-like-post'); ?></span>
									</td>
								</tr>			
								<tr valign="top">
									<th scope="row"><label><?php _e('Login required to vote', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wti_like_post_login_required" id="login_yes" value="1" <?php if (1 == get_option('wti_like_post_login_required')) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_login_required" id="login_no" value="0" <?php if ((0 == get_option('wti_like_post_login_required')) || ('' == get_option('wti_like_post_login_required'))) { echo 'checked'; } ?> /> <?php echo __('No', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select whether only logged in users can vote or not.', 'wti-like-post');?></span>
									</td>
								</tr>			
								<tr valign="top">
									<th scope="row"><label><?php _e('Login required message', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wti_like_post_login_message" id="wti_like_post_login_message" value="<?php echo esc_html(get_option('wti_like_post_login_message')); ?>" />
										<span class="description"><?php _e('Message to show in case login required and user is not logged in.', 'wti-like-post');?></span>
									</td>
								</tr>			
								<tr valign="top">
									<th scope="row"><label><?php _e('Thank you message', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wti_like_post_thank_message" id="wti_like_post_thank_message" value="<?php echo esc_html(get_option('wti_like_post_thank_message')); ?>" />
										<span class="description"><?php _e('Message to show after successful voting.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Already voted message', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wti_like_post_voted_message" id="wti_like_post_voted_message" value="<?php echo esc_html(get_option('wti_like_post_voted_message')); ?>" />
										<span class="description"><?php _e('Message to show if user has already voted.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Show on pages', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wti_like_post_show_on_pages" id="show_pages_yes" value="1" <?php if (('1' == get_option('wti_like_post_show_on_pages'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_show_on_pages" id="show_pages_no" value="0" <?php if ('0' == get_option('wti_like_post_show_on_pages') || ('' == get_option('wti_like_post_show_on_pages'))) { echo 'checked'; } ?> /> <?php echo __('No', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select yes if you want to show the like option on pages as well.', 'wti-like-post')?></span>
									</td>
								</tr>	
								<tr valign="top">
									<th scope="row"><label><?php _e('Exclude on selected sections', 'wti-like-post'); ?></label></th>
									<td>
										<input type="checkbox" name="wti_like_post_excluded_sections[]" id="wti_like_post_excluded_home" value="home" <?php if (in_array('home', $excluded_sections)) { echo 'checked'; } ?> /> <?php echo __('Home', 'wti-like-post'); ?>
										<input type="checkbox" name="wti_like_post_excluded_sections[]" id="wti_like_post_excluded_archive" value="archive" <?php if (in_array('archive', $excluded_sections)) { echo 'checked'; } ?> /> <?php echo __('Archive', 'wti-like-post'); ?>
										<span class="description"><?php _e('Check the sections where you do not want to avail the like/dislike options. This has higher priority than the "Exclude post/page IDs" setting.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Exclude selected categories', 'wti-like-post'); ?></label></th>
									<td>	
										<select name='wti_like_post_excluded_categories[]' id='wti_like_post_excluded_categories' multiple="multiple" size="4" style="height:auto !important;">
											<?php 
											$categories=  get_categories();
											
											foreach ($categories as $category) {
												$selected = (in_array($category->cat_ID, $excluded_categories)) ? 'selected="selected"' : '';
												$option  = '<option value="' . $category->cat_ID . '" ' . $selected . '>';
												$option .= $category->cat_name;
												$option .= ' (' . $category->category_count . ')';
												$option .= '</option>';
												echo $option;
											}
											?>
										</select>
										<span class="description"><?php _e('Select categories where you do not want to show the like option. It has higher priority than "Exclude post/page IDs" setting.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Allow post IDs', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wti_like_post_allowed_posts" id="wti_like_post_allowed_posts" value="<?php echo esc_html(get_option('wti_like_post_allowed_posts')); ?>" />
										<span class="description"><?php _e('Suppose you have a post which belongs to more than one categories and you have excluded one of those categories. So the like/dislike will not be available for that post. Enter comma separated those post ids where you want to show the like/dislike option irrespective of that post category being excluded.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Exclude post/page IDs', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="text" size="40" name="wti_like_post_excluded_posts" id="wti_like_post_excluded_posts" value="<?php echo esc_html(get_option('wti_like_post_excluded_posts')); ?>" />
										<span class="description"><?php _e('Enter comma separated post/page ids where you do not want to show the like option. If Show on pages setting is set to Yes but you have added the page id here, then like option will not be shown for the same page.', 'wti-like-post');?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Show excluded posts/pages on widget', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wti_like_post_show_on_widget" id="show_widget_yes" value="1" <?php if (('1' == get_option('wti_like_post_show_on_widget')) || ('' == get_option('wti_like_post_show_on_widget'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_show_on_widget" id="show_widget_no" value="0" <?php if ('0' == get_option('wti_like_post_show_on_widget')) { echo 'checked'; } ?> /> <?php echo __('No', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select yes if you want to show the excluded posts/pages on widget.', 'wti-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Position Setting', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wti_like_post_position" id="position_top" value="top" <?php if (('top' == get_option('wti_like_post_position')) || ('' == get_option('wti_like_post_position'))) { echo 'checked'; } ?> /> <?php echo __('Top of Content', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_position" id="position_bottom" value="bottom" <?php if ('bottom' == get_option('wti_like_post_position')) { echo 'checked'; } ?> /> <?php echo __('Bottom of Content', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select the position where you want to show the like options.', 'wti-like-post')?></span>
									</td>
								</tr>			
								<tr valign="top">
									<th scope="row"><label><?php _e('Alignment Setting', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wti_like_post_alignment" id="alignment_left" value="left" <?php if (('left' == get_option('wti_like_post_alignment')) || ('' == get_option('wti_like_post_alignment'))) { echo 'checked'; } ?> /> <?php echo __('Left', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_alignment" id="alignment_right" value="right" <?php if ('right' == get_option('wti_like_post_alignment')) { echo 'checked'; } ?> /> <?php echo __('Right', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select the alignment whether to show on left or on right.', 'wti-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Title text for like/unlike images', 'wti-like-post'); ?></label></th>
									<td>
										<input type="text" name="wti_like_post_title_text" id="wti_like_post_title_text" value="<?php echo esc_html(get_option('wti_like_post_title_text')); ?>" />
										<span class="description"><?php echo __('Enter both texts separated by "/" to show when user puts mouse over like/unlike images.', 'wti-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"><label><?php _e('Show dislike option', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wti_like_post_show_dislike" id="show_dislike_yes" value="1" <?php if (('1' == get_option('wti_like_post_show_dislike')) || ('' == get_option('wti_like_post_show_dislike'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_show_dislike" id="show_dislike_no" value="0" <?php if ('0' == get_option('wti_like_post_show_dislike')) { echo 'checked'; } ?> /> <?php echo __('No', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select the option whether to show or hide the dislike option.', 'wti-like-post')?></span>
									</td>
								</tr>	
								<tr valign="top">
									<th scope="row"><label><?php _e('Show +/- symbols', 'wti-like-post'); ?></label></th>
									<td>	
										<input type="radio" name="wti_like_post_show_symbols" id="show_symbol_yes" value="1" <?php if (('1' == get_option('wti_like_post_show_symbols')) || ('' == get_option('wti_like_post_show_symbols'))) { echo 'checked'; } ?> /> <?php echo __('Yes', 'wti-like-post'); ?>
										<input type="radio" name="wti_like_post_show_symbols" id="show_symbol_no" value="0" <?php if ('0' == get_option('wti_like_post_show_symbols')) { echo 'checked'; } ?> /> <?php echo __('No', 'wti-like-post'); ?>
										<span class="description"><?php _e('Select the option whether to show or hide the plus or minus symbols before like/unlike count.', 'wti-like-post')?></span>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row"></th>
									<td>
										<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', 'wti-like-post'); ?>" />
										<input class="button-secondary" type="submit" name="Reset" value="<?php _e('Reset Options', 'wti-like-post'); ?>" onclick="return confirmReset()" />
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>		
		</div>
	
		<script>
		function confirmReset()
		{
			// Check whether user agrees to reset the settings to default or not
			var check = confirm("<?php _e('Are you sure to reset the options to default settings?', 'wti-like-post')?>");
			
			if (check) {
				// Reset the settings
				document.getElementById('wti_like_post_voting_period').value = 'once';
				document.getElementById('wti_like_post_voting_style').value = 'style1';
				document.getElementById('login_yes').checked = false;
				document.getElementById('login_no').checked = true;
				document.getElementById('wti_like_post_login_message').value = "<?php echo __('Please login to vote.', 'wti-like-post'); ?>";
				document.getElementById('wti_like_post_thank_message').value = "<?php echo __('Thanks for your vote.', 'wti-like-post'); ?>";
				document.getElementById('wti_like_post_voted_message').value = "<?php echo __('You have already voted.', 'wti-like-post'); ?>";
				document.getElementById('show_pages_yes').checked = false;
				document.getElementById('show_pages_no').checked = true;
				document.getElementById('wti_like_post_allowed_posts').value = '';
				document.getElementById('wti_like_post_excluded_posts').value = '';
				document.getElementById('wti_like_post_excluded_categories').selectedIndex = -1;
				document.getElementById('wti_like_post_excluded_home').value = '';
				document.getElementById('wti_like_post_excluded_archive').value = '';
				document.getElementById('show_widget_yes').checked = true;
				document.getElementById('show_widget_no').checked = false;
				document.getElementById('position_top').checked = false;
				document.getElementById('position_bottom').checked = true;
				document.getElementById('alignment_left').checked = true;
				document.getElementById('alignment_right').checked = false;
				document.getElementById('show_symbol_yes').checked = true;
				document.getElementById('show_symbol_no').checked = false;
				document.getElementById('show_dislike_yes').checked = true;
				document.getElementById('show_dislike_no').checked = false;
				document.getElementById('wti_like_post_title_text').value = "<?php echo __('Like/Unlike', 'wti-like-post'); ?>";
				
				return true;
			}
			
			return false;
		}
		
		function processAll()
		{
			var cfm = confirm('<?php echo __('Are you sure to reset all the counts present in the database?', 'wti-like-post')?>');
			
			if (cfm) {
				return true;
			} else {
				return false;
			}
		}
		
		function processSelected()
		{
			var cfm = confirm('<?php echo __('Are you sure to reset selected counts present in the database?', 'wti-like-post')?>');
			
			if (cfm) {
				return true;
			} else {
				return false;
			}
		}
		</script>
		
		<?php
		if (isset($_POST['resetall'])) {
			if (wp_verify_nonce( $_POST['_wpnonce'], 'wti_like_post_lite_reset_counts_nonce' )) {
				$status = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}wti_like_post");
				if ($status) {
					echo '<div class="updated" id="message"><p>';
					echo __('All counts have been reset successfully.', 'wti-like-post');
					echo '</p></div>';
				} else {
					echo '<div class="error" id="error"><p>';
					echo __('All counts could not be reset.', 'wti-like-post');
					echo '</p></div>';
				}
			} else {
				echo '<div class="error" id="error"><p>';
				echo __('Invalid access to reset all counts.', 'wti-like-post');
				echo '</p></div>';
			}
		}

		if (isset($_POST['resetselected'])) {
			if (wp_verify_nonce( $_POST['_wpnonce'], 'wti_like_post_lite_reset_counts_nonce' )) {
				if (count($_POST['post_ids']) > 0) {
					// Filter proper values
					$all_ids = array_map(function($value) {
						return (int) $value;
					}, $_POST['post_ids']);

					$post_ids = implode(",", array_filter($all_ids));

					$status = $wpdb->query(
						"DELETE FROM {$wpdb->prefix}wti_like_post WHERE post_id IN ($post_ids)"
					);

					if ($status) {
						echo '<div class="updated" id="message"><p>';

						if ($status > 1) {
							echo $status . ' ' . __('counts were reset successfully.', 'wti-like-post');
						} else {
							echo $status . ' ' . __('count was reset successfully.', 'wti-like-post');
						}
						
						echo '</p></div>';
					} else {
						echo '<div class="error" id="error"><p>';
						echo __('Selected counts could not be reset.', 'wti-like-post');
						echo '</p></div>';
					}
				} else {
					echo '<div class="error" id="error"><p>';
					echo __('Please select posts to reset count.', 'wti-like-post');
					echo '</p></div>';
				}
			} else {
				echo '<div class="error" id="error"><p>';
				echo __('Invalid access to reset selected counts.', 'wti-like-post');
				echo '</p></div>';
			}
		}
		?>
		<div class="clearfix"></div>
		<div class="ui-sortable meta-box-sortables" style="clear:left">
			<h2><?php _e('Most Liked Posts', 'wti-like-post');?></h2>
			<?php
			// Getting the most liked posts
			$query = "SELECT COUNT(post_id) AS total
					FROM `{$wpdb->prefix}wti_like_post` L JOIN {$wpdb->prefix}posts P
					ON L.post_id = P.ID WHERE value > 0";
			$post_count = $wpdb->get_var($query);
	   
			if ($post_count > 0) {
	
				// Pagination script
				$limit = get_option('posts_per_page');
				
				if ( isset( $_GET['paged'] ) ) {
					$current = max( 1, $_GET['paged'] );
				} else {
					$current = 1;
				}
				
				$total_pages = ceil($post_count / $limit);
				$start = $current * $limit - $limit;
				
				$query = $wpdb->prepare(
					"SELECT post_id, SUM(value) AS like_count, post_title
					FROM `{$wpdb->prefix}wti_like_post` L JOIN {$wpdb->prefix}posts P 
					ON L.post_id = P.ID WHERE value > 0 GROUP BY post_id
					ORDER BY like_count DESC, post_title LIMIT %d, %d",
					$start, $limit
				);
				
				$result = $wpdb->get_results($query);
				?>
				<form method="post" action="<?php echo admin_url('options-general.php?page=WtiLikePostAdminMenu'); ?>" name="most_liked_posts_form" id="most_liked_posts_form">
					<div style="float:left">
						<?php
						wp_nonce_field('wti_like_post_lite_reset_counts_nonce');
						?>
						<input class="button-secondary" type="submit" name="resetall" id="resetall" onclick="return processAll()" value="<?php echo __('Reset All Counts', 'wti-like-post')?>" />
						<input class="button-secondary" type="submit" name="resetselected" id="resetselected" onclick="return processSelected()" value="<?php echo __('Reset Selected Counts', 'wti-like-post')?>" />
					</div>
					<div style="float:right">
						<div class="tablenav top">
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo $post_count?> <?php echo __('items', 'wti-like-post'); ?></span>
								<?php
								echo paginate_links(
									array(
										'current' 	=> $current,
										'prev_text'	=> '&laquo; ' . __('Prev', 'wti-like-post'),
										'next_text'    	=> __('Next', 'wti-like-post') . ' &raquo;',
										'base' 		=> @add_query_arg('paged','%#%'),
										'format'  	=> '?page=WtiLikePostAdminMenu',
										'total'   	=> $total_pages
									)
								);
								?>
							</div>
						</div>
					</div>
					<?php
					echo '<table cellspacing="0" class="wp-list-table widefat fixed likes">';
					echo '<thead><tr><th class="manage-column column-cb check-column" id="cb" scope="col">';
					echo '<input type="checkbox" id="checkall">';
					echo '</th><th>';
					echo __('Post Title', 'wti-like-post');
					echo '</th><th>';
					echo __('Like Count', 'wti-like-post');
					echo '</th><tr></thead>';
					echo '<tbody class="list:likes" id="the-list">';
					
					foreach ($result as $post) {
						$post_title = esc_html($post->post_title);
						$permalink = get_permalink($post->post_id);
						$like_count = $post->like_count;
						
						echo '<tr>';
						echo '<th class="check-column" scope="row" align="center"><input type="checkbox" value="' . $post->post_id . '" class="administrator" id="post_id_' . $post->post_id . '" name="post_ids[]"></th>';
						echo '<td><a href="' . $permalink . '" title="' . $post_title . '" target="_blank">' . $post_title . '</a></td>';
						echo '<td>' . $like_count . '</td>';
						echo '</tr>';
					}
		 		
					echo '</tbody></table>';
				?>
				</form>
				<?php
			} else {
				echo '<p>';
				echo __('No posts liked yet.', 'wti-like-post');
				echo '</p>';
			}
			?>
		</div>
     </div>
</div>
<?php
}

// For adding metabox for posts/pages
add_action('admin_menu', 'WtiLikePostAddMetaBox');

/**
 * Metabox for for like post
 * @param no-param
 * @return no-return
 */
function WtiLikePostAddMetaBox() {
	// Add the meta box for posts/pages
     add_meta_box('wti-like-post-meta-box', __('WTI Like Post Exclude Option', 'wti-like-post'), 'WtiLikePostShowMetaBox', 'post', 'side', 'high');
     add_meta_box('wti-like-post-meta-box', __('WTI Like Post Exclude Option', 'wti-like-post'), 'WtiLikePostShowMetaBox', 'page', 'side', 'high');
}

/**
 * Callback function to show fields in meta box
 * @param no-param
 * @return string
 */
function WtiLikePostShowMetaBox() {
	global $post;

	// Use nonce for verification
	echo '<input type="hidden" name="wti_like_post_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

     // Get whether current post is excluded or not
	$excluded_posts = explode(',', esc_html(get_option('wti_like_post_excluded_posts')));

	if (in_array($post->ID, $excluded_posts)) {
		$checked = 'checked="checked"';
	} else {
		$checked = '';
	}

	echo '<p>';    
	echo '<label for="wti_exclude_post"><input type="checkbox" name="wti_exclude_post" id="wti_exclude_post" value="1" ', $checked, ' /> ';
	echo __('Check to disable like/unlike functionality', 'wti-like-post');
	echo '</label>';
	echo '</p>';
}

add_action('save_post', 'WtiLikePostSaveData');

/**
 * Save data from meta box
 * @param no-param
 * @return string
 */
function WtiLikePostSaveData($post_id) {
     // Verify nonce
     if ( empty( $_POST['wti_like_post_meta_box_nonce'] ) ||
	     !wp_verify_nonce( $_POST['wti_like_post_meta_box_nonce'], basename(__FILE__) ) ) {
          return $post_id;
     }
    
     // Check autosave
     if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
          return $post_id;
     }
    
     // Check permissions
     if ('page' == $_POST['post_type']) {
          if (!current_user_can('edit_page', $post_id)) {
               return $post_id;
          }
     } elseif (!current_user_can('edit_post', $post_id)) {
          return $post_id;
     }

	// Initialise the excluded posts array
	$excluded_posts = array();
	$exc_posts = esc_html(get_option('wti_like_post_excluded_posts'));

	// Check whether this post/page is to be excluded
	$exclude_post = isset( $_POST['wti_exclude_post'] ) ? $_POST['wti_exclude_post'] : 0;
	
	// Get old excluded posts/pages
	if (strlen($exc_posts) > 0) {
		$excluded_posts = explode(',', $exc_posts);
	}
	
	if ($exclude_post == 1 && !in_array($post_id, $excluded_posts)) {
		// Add this post/page id to the excluded list
		$excluded_posts[] = (int) $post_id;
		
		if (!empty($excluded_posts)) {
			// Since there are already excluded posts/pages, add this as a comma separated value
			update_option('wti_like_post_excluded_posts', implode(',', $excluded_posts));
		} else {
			// Since there is no old excluded post/page, add this directly
			update_option('wti_like_post_excluded_posts', $post_id);
		}
	} else if (!$exclude_post) {
		// Check whether this id is already in the excluded list or not
		$key = array_search($post_id, $excluded_posts);
		
		if ($key !== false) {
			// Since this is already in the list, so exluded this
			unset($excluded_posts[$key]);
			
			// Update the excluded posts list
			update_option('wti_like_post_excluded_posts', implode(',', $excluded_posts));
		}
	}
}

/**
 * Additional links on plugins page
 * 
 * @param array
 * @param string
 * @return array
 */
function WtiLikePostSetPluginMeta( $links, $file ) {
	if ( strpos( $file, 'wti-like-post/wti_like_post.php' ) !== false ) {
		$new_links = array(
						'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=support@webtechideas.com&item_name=WTI%20Like%20Post&return=https://www.webtechideas.in/thanks/" target="_blank">' . __( 'Donate', 'wti-like-post' ) . '</a>',
						'<a href="https://www.webtechideas.in/product/wti-like-post-pro/" target="_blank">' . __( 'PRO Version', 'wti-like-post' ) . '</a>',
						'<a href="http://support.webtechideas.com/forums/forum/wti-like-post-pro/" target="_blank">' . __( 'PRO Support Forum', 'wti-like-post' ) . '</a>',
					);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}

add_filter( 'plugin_row_meta', 'WtiLikePostSetPluginMeta', 10, 2 );

/**
 * Display a notice that can be dismissed
 *
 * @param none
 * @return void
 */
add_action('admin_notices', 'WtiAdminNotice');

function WtiAdminNotice() {
	global $pagenow, $wti_like_post_db_version;
	
 	if ( isset( $_GET['hide_wti_like_post_notify_author'] ) && true == $_GET['hide_wti_like_post_notify_author'] ) {
 		if ( current_user_can( 'activate_plugins' ) && wp_verify_nonce( $_GET['_wpnonce'], 'wti_like_post_lite_notify_author_nonce' ) ) {
			// Hide the notification
			update_option( 'wti_like_post_lite_notify_author', 0 );
		} else {
			echo '<div class="error"><p>Invalid access to hide author notification.</p></div>';
		}
	} else if ( isset( $_GET['send_wti_like_post_notify_author'] ) && true == $_GET['send_wti_like_post_notify_author'] ) {
 		if ( current_user_can( 'activate_plugins' ) && wp_verify_nonce( $_GET['_wpnonce'], 'wti_like_post_lite_notify_author_nonce' ) ) {
			// Check that the author has to be notified
			$notify_author = get_option( 'wti_like_post_lite_notify_author', 1 );
			
			if ( $notify_author ) {
				// Not yet notified, so notify the author now
				$message = 'WTI Like Post Lite ' . $wti_like_post_db_version . ' is used on <a href="' . get_option( 'siteurl' ) . '">' . get_option( 'blogname' ) . '</a>.';
				$headers = array('Content-Type: text/html; charset=UTF-8');
				
				$sent = wp_mail( 'support@webtechideas.com', 'WTI Like Post Lite ' . $wti_like_post_db_version . ' Used', $message, $headers );
				
				if ( $sent ) {
					update_option('wti_like_post_lite_notify_author', 0);
					echo '<div class="updated"><p>Thanks for registering.</p></div>';
				}
			}
		} else {
			echo '<div class="error"><p>Invalid access to send author notification.</p></div>';
		}
	}

	if ( $pagenow == 'plugins.php' || ( isset( $_GET['page'] ) && ( $_GET['page'] == 'WtiLikePostAdminMenu'
		|| $_GET['page'] == 'wtilp-most-liked-posts' || $_GET['page'] == 'wtilp-features-support' ) ) ) {
		
		// Check that the author has to be notified
		$notify_author = get_option( 'wti_like_post_lite_notify_author', 1 );
		
		if ( $notify_author ) {
			echo '<div class="updated"><p>';
			
			echo 'Please consider <strong><a href="' . esc_url( wp_nonce_url( add_query_arg( 'send_wti_like_post_notify_author', 'true' ), 'wti_like_post_lite_notify_author_nonce' ) ) . '">registering your use of WTI Like Post</a></strong> ' .
				'to inform <a href="https://www.webtechideas.in" target="_blank">WebTechIdeas (plugin author)</a> that you are using it. This sends only your site name and URL so that they ' .
				'know where their plugin is being used, no other data is sent. <a href="' . esc_url( wp_nonce_url( add_query_arg( 'hide_wti_like_post_notify_author', 'true' ), 'wti_like_post_lite_notify_author_nonce' ) ) . '">Hide this message.</a>';
			
			echo '</p></div>';
		}
	}
}

/**
 * Add the javascript for admin of the plugin
 * @param no-param
 * @return string
 */
function WtiLikePostEnqueueAdminScripts() {
	wp_register_script( 'wti_like_post_admin_script', plugins_url( 'js/wti_like_post_admin.js', __FILE__ ), array('jquery') );
	wp_localize_script( 'wti_like_post_admin_script', 'wtilp', array(
												    'ajax_url' => admin_url( 'admin-ajax.php' ),
												)
				    );
  
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'wti_like_post_admin_script' );
}