<?php

// Load views class
require_once dirname(__FILE__).'/views.php';

/**
 * Scans Edit class
 *
 * @package WP Link Status
 * @subpackage Views
 */
class WPLNST_Views_Scans_Edit extends WPLNST_Views {



	/**
	 * Show scan edit form
	 */
	public static function view($args) {

		// Vars
		extract($args);

		// Initialize
		$is_ready = true;
		$editable = ('wait' == $scan->status);

		// Check errors
		$link_types_error = $post_types_error = $post_status_error = $link_status_error = false;
		if (!empty($notice_ready) && is_array($notice_ready)) {

			// Inside form errors
			$link_types_error  = isset($notice_ready['link_types']);
			$post_types_error  = isset($notice_ready['post_types']);
			$post_status_error = isset($notice_ready['post_status']);
			$link_status_error = isset($notice_ready['link_status']);

			// Determine if ready
			$is_ready = !($link_types_error || $post_types_error || $post_status_error || $link_status_error);

			// Show notice errors
			echo '<div class="error notice">';
			foreach ($notice_ready as $notice_key => $notice_title) {
				echo '<p>'.$notice_title.'</p>';
			}
			echo '</div>';
		} ?>

		<?php if ($is_ready && $scan->id > 0) :

			// Prepare URLs
			$results_url = esc_url(WPLNST_Core_Plugin::get_url_scans_results($scan->id));
			$start_url 	 = esc_url(WPLNST_Core_Plugin::get_url_scans_crawler($scan->id, 'on', $scan->hash));
			$stop_url 	 = esc_url(WPLNST_Core_Plugin::get_url_scans_crawler($scan->id, 'off', $scan->hash)); ?>

			<?php if ('stop' == $scan->status) : ?>

				<div class="notice"><p><?php printf(__('The crawler for this scan is <strong>stopped</strong> but you can see the <a href="%s">crawling results</a> collected data.', 'wplnst'), $results_url); if ($more_scans) : echo ' '; printf(__('Or you can <a href="%s">start again the crawler</a>.', 'wplnst'), $start_url); endif; ?></p></div>

			<?php elseif ('play' == $scan->status) : ?>

				<?php if (empty($_POST['scan_run']) && empty($_GET['started'])) : ?>

					<div class="notice"><p><?php printf(__('The crawler for this scan is <strong>running</strong> and you can see its <a href="%s">crawling results data</a>.', 'wplnst'), $results_url); echo ' '; printf(__('If needed you can <a href="%s">stop the crawler</a>.', 'wplnst'), $stop_url); ?></p></div>

				<?php endif; ?>

			<?php elseif ('end' == $scan->status) : ?>

				<div class="notice"><p><?php printf(__('The crawler is completed and you can see all its <a href="%s">crawling results</a>.', 'wplnst'), $results_url); ?></p></div>

			<?php endif; ?>

		<?php endif; ?>

		<form method="post" id="wplnst-form" action="<?php echo esc_url($action); ?>">

			<input type="hidden" name="scan_id" id="wplnst-scan-id" value="<?php echo esc_attr($scan->id); ?>" />
			<input type="hidden" name="scan_run" id="wplnst-scan-run" value="" />
			<input type="hidden" name="scan_edit_nonce" value="<?php echo esc_attr($nonce); ?>" />

			<input type="hidden" name="scan_custom_fields" id="wplnst-scan-custom-fields" value='<?php echo self::esc_attr_elist($scan->custom_fields); ?>' />
			<input type="hidden" name="scan_anchor_filters" id="wplnst-scan-anchor-filters" value='<?php echo self::esc_attr_elist($scan->anchor_filters); ?>' />
			<input type="hidden" name="scan_include_urls" id="wplnst-scan-include-urls" value='<?php echo self::esc_attr_elist($scan->include_urls); ?>' />
			<input type="hidden" name="scan_exclude_urls" id="wplnst-scan-exclude-urls" value='<?php echo self::esc_attr_elist($scan->exclude_urls); ?>' />
			<input type="hidden" name="scan_html_attributes" id="wplnst-scan-html-attributes" value='<?php echo self::esc_attr_elist($scan->html_attributes); ?>' />

			<h2 id="wplnst-tabs-nav" class="nav-tab-wrapper">
				<a id="wplnst-general-tab" href="#top#wplnst-general" class="nav-tab"<?php if ($link_types_error) echo ' style="color: red;"'; ?>><?php _e('General', 'wplnst'); ?></a>
				<a id="wplnst-content-tab" href="#top#wplnst-content" class="nav-tab"<?php if ($post_types_error || $post_status_error) echo ' style="color: red;"'; ?>><?php _e('Content options', 'wplnst'); ?></a>
				<a id="wplnst-filters-tab" href="#top#wplnst-filters" class="nav-tab"><?php _e('Content filters', 'wplnst'); ?></a>
				<a id="wplnst-status-tab" href="#top#wplnst-status" class="nav-tab"<?php if ($link_status_error) echo ' style="color: red;"'; ?>><?php _e('Links status', 'wplnst'); ?></a>
				<?php if (false) : ?><a id="wplnst-scheduled-tab" href="#top#wplnst-scheduled" class="nav-tab"><?php _e('Schedule', 'wplnst'); ?></a><?php endif; ?>
				<a id="wplnst-advanced-tab" href="#top#wplnst-advanced" class="nav-tab"><?php _e('Advanced', 'wplnst'); ?></a>
			</h2>

			<div id="wplnst-tabs" class="wplnst-tabs-scan-edit">

				<div id="wplnst-general" class="wplnst-tab wplnst-tab-default">

					<table class="form-table">

						<tr>
							<th><label for="tx-name"><?php _e('Scan name', 'wplnst'); ?></label></th>
							<td><input type="text" name="tx-name" id="tx-name" value="<?php echo esc_attr($scan->name); ?>" class="regular-text" maxlength="255" /> (<?php _e('optional', 'wplnst'); ?>)</td>
						</tr>


						<?php if (!empty($link_types)) : ?>

							<?php if ($editable) : ?>

								<tr>
									<th<?php if ($link_types_error) echo ' style="color: red;"'; ?>><?php _e('Link types', 'wplnst'); ?></th>
									<td class="wplnst-list"><?php foreach ($link_types as $key => $name) : ?><input <?php self::checked($key, $scan->link_types); ?> type="checkbox" name="ck-link-type[<?php echo $key; ?>]" id="ck-link-type-<?php echo $key; ?>" value="on" /><label for="ck-link-type-<?php echo $key; ?>"><?php echo $name; ?></label> &nbsp; <?php endforeach; ?></td>
								</tr>

							<?php else : ?>

								<tr>
									<th><?php _e('Link types', 'wplnst'); ?></th>
									<td class="wplnst-value-list"><?php if (!empty($scan->link_types_names) && is_array($scan->link_types_names)) echo implode(', ', array_map('esc_html', $scan->link_types_names)); ?></td>
								</tr>

							<?php endif; ?>

						<?php endif; ?>


						<?php if (!empty($destination_types)) : ?>

							<?php if ($editable) : ?>

								<tr>
									<th><label for="wplnst-destination-type"><?php _e('Destination type', 'wplnst'); ?></label></th>
									<td><select id="wplnst-destination-type" name="sl-destination-type"><?php self::options($destination_types, $scan->destination_type); ?></select></td>
								</tr>

							<?php else : ?>

								<tr>
									<th><?php _e('Destination type', 'wplnst'); ?></th>
									<td class="wplnst-value"><?php echo esc_html($scan->destination_type_name); ?></td>
								</tr>

							<?php endif; ?>

						<?php endif; ?>


						<?php if ($editable) : ?>

							<tr>
								<th><label for="wplnst-time-scope"><?php _e('Time scope', 'wplnst'); ?></label></th>
								<td><select id="wplnst-time-scope" name="sl-time-scope"><?php self::options($time_scopes, $scan->time_scope); ?></select></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Time scope', 'wplnst'); ?></th>
								<td class="wplnst-value"><?php echo esc_html($scan->time_scope_name); ?></td>
							</tr>

						<?php endif; ?>


						<?php if ($editable) : ?>

							<tr>
								<th><label for="wplnst-crawl-order"><?php _e('Crawl order', 'wplnst'); ?></label></th>
								<td><select id="wplnst-crawl-order" name="sl-crawl-order"><?php self::options($crawl_order, $scan->crawl_order); ?></select></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Crawl order', 'wplnst'); ?></th>
								<td class="wplnst-value"><?php echo esc_html($scan->crawl_order_name); ?></td>
							</tr>

						<?php endif; ?>


						<?php if ($editable) : ?>

							<tr>
								<th><?php _e('Redirection status', 'wplnst'); ?></th>
								<td class="wplnst-list"><input <?php self::checked($scan->redir_status, true); ?> type="checkbox" id="ck-redir-status" name="ck-redir-status" value="on" /><label for="ck-redir-status"><?php _e('Check status of destination URLs', 'wplnst'); ?></label></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Redirection status', 'wplnst'); ?></th>
								<td class="wplnst-value"><?php echo $scan->redir_status? __('<strong>Yes</strong>, check status of destination URLs', 'wplnst') : __('No check status of destination URLs', 'wplnst'); ?></td>
							</tr>

						<?php endif; ?>


						<?php if ($editable) : ?>

							<tr>
								<th><?php _e('Malformed URLs', 'wplnst'); ?></th>
								<td class="wplnst-list"><input <?php self::checked($scan->malformed, true); ?> type="checkbox" id="ck-malformed-links" name="ck-malformed-links" value="on" /><label for="ck-malformed-links"><?php _e('Track malformed links', 'wplnst'); ?></label></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Malformed URLs', 'wplnst'); ?></th>
								<td class="wplnst-value"><?php echo $scan->malformed? __('<strong>Yes</strong>, track malformed links', 'wplnst') : __('No tracking of malformed links', 'wplnst'); ?></td>
							</tr>

						<?php endif; ?>


						<?php if ('end' != $scan->status) : ?>

							<tr>
								<th><?php _e('Notifications', 'wplnst'); ?></th>
								<td class="wplnst-list"><p><?php _e('Send an e-mail when the scan is completed:', 'wplnst'); ?></p>
								<p><input <?php self::checked($scan->notify_default, true); ?> type="checkbox" id="ck-notify-default" name="ck-notify-default" value="on" /><label for="ck-notify-default"><?php printf(__('Send to the current blog address <strong>%s</strong>', 'wplnst'), get_option('admin_email'));; ?></label></p>
								<p><input <?php self::checked($scan->notify_address, true); ?> type="checkbox" id="ck-notify-address" name="ck-notify-address" value="on" /><label for="ck-notify-address"><?php _e('Send to these e-mail addresses:', 'wplnst'); ?></label><br /><input type="text" name="tx-notify-address-email" id="tx-notify-address-email" value="<?php echo esc_attr($scan->notify_address_email); ?>" class="regular-text" maxlength="255" /></p></td>
							</tr>

						<?php endif; ?>


					</table>

				</div>


				<div id="wplnst-content" class="wplnst-tab">

					<table class="form-table">


						<?php if (!empty($post_types)) : ?>

							<?php if ($editable) : ?>

								<tr>
									<th<?php if ($post_types_error) echo ' style="color: red;"'; ?>><?php _e('Post types', 'wplnst'); ?></th>
									<td class="wplnst-list"><?php foreach ($post_types as $key => $name) : ?><input <?php self::checked($key, $scan->post_types); ?> type="checkbox" name="ck-post-type[<?php echo $key; ?>]" id="ck-post-type-<?php echo $key; ?>" value="on" /><label for="ck-post-type-<?php echo $key; ?>"><?php echo $name; ?> (<code><?php echo $key; ?></code>)</label><br /><?php endforeach; ?></td>
								</tr>

							<?php else : ?>

								<tr>
									<th><?php _e('Post types', 'wplnst'); ?></th>
									<td class="wplnst-value-list"><?php if (empty($scan->post_types_names_strict) || !is_array($scan->post_types_names_strict)) : echo '-'; else : echo implode("<br />", $scan->post_types_names_strict); endif; ?></td>
								</tr>

							<?php endif; ?>

						<?php endif; ?>


						<?php if (!empty($post_status)) : ?>

							<?php if ($editable) : ?>

								<tr>
									<th<?php if ($post_status_error) echo ' style="color: red;"'; ?>><?php _e('Post status', 'wplnst'); ?></th>
									<td class="wplnst-list"><?php foreach ($post_status as $key) : ?><input <?php self::checked($key, $scan->post_status); ?> type="checkbox" name="ck-post-status[<?php echo $key; ?>]" id="ck-post-status-<?php echo $key; ?>" value="on" /><label for="ck-post-status-<?php echo $key; ?>"><?php echo ucfirst($key); ?></label> &nbsp; <?php endforeach; ?></td>
								</tr>

							<?php else : ?>

								<tr>
									<th><?php _e('Post status', 'wplnst'); ?></th>
									<td class="wplnst-value-list"><?php if (empty($scan->post_status_names) || !is_array($scan->post_status_names)) : echo '-'; else : echo implode(', ', array_map('esc_html', $scan->post_status_names)); endif; ?></td>
								</tr>

							<?php endif; ?>

						<?php endif; ?>

						<?php if ($editable) : ?>

							<tr>
								<th><label for="wplnst-cf-new"><?php _e('Custom fields', 'wplnst'); ?></label></th>
								<td><table id="wplnst-elist-custom-fields" class="wplnst-elist" data-editable="true"></table>
									<input type="text" id="wplnst-cf-new" value="" class="regular-text" placeholder="<?php _e('Custom field key', 'wplnst'); ?>" />&nbsp;<select id="wplnst-cf-new-type"><?php self::options($custom_fields, false); ?></select>&nbsp;<input class="button-secondary" type="button" id="wplnst-cf-new-add" value="<?php _e('Add', 'wplnst'); ?>" /></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Custom fields', 'wplnst'); ?></th>
								<td><?php if (empty($scan->custom_fields)) : ?>-<?php else : ?><table id="wplnst-elist-custom-fields" class="wplnst-elist wplnst-elist-readonly" data-editable="false"></table><?php endif; ?></td>
							</tr>

						<?php endif; ?>

						<?php if ($editable) : ?>

							<tr>
								<th<?php if ($post_types_error) echo ' style="color: red;"'; ?>><?php _e('Comment links', 'wplnst'); ?></th>
								<td class="wplnst-list"><?php foreach ($comment_types as $key => $name) : ?><input <?php self::checked($key, $scan->comment_types); ?> type="checkbox" name="ck-comment-type[<?php echo $key; ?>]" id="ck-comment-type-<?php echo $key; ?>" value="on" /><label for="ck-comment-type-<?php echo $key; ?>"><?php echo $name; ?></label> &nbsp; <?php endforeach; ?></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Comment links', 'wplnst'); ?></th>
								<td class="wplnst-value-list"><?php if (!empty($scan->comment_types_names) && is_array($scan->comment_types_names)) echo implode(', ', array_map('esc_html', $scan->comment_types_names)); ?></td>
							</tr>

						<?php endif; ?>

						<?php if ($editable) : ?>

							<tr>
								<th<?php if ($post_types_error) echo ' style="color: red;"'; ?>><?php _e('Also check links in', 'wplnst'); ?></th>
								<td class="wplnst-list"><input <?php self::checked($scan->check_blogroll, true); ?> type="checkbox" name="ck-blogroll" id="ck-blogroll" value="on" /><label for="ck-blogroll"><?php _e('Blogroll links', 'wplnst'); ?></label></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Also check links in', 'wplnst'); ?></th>
								<td class="wplnst-value-list"><?php echo $scan->check_blogroll? __('Blogroll links', 'wplnst') :  '-'; ?></td>
							</tr>

						<?php endif; ?>
					</table>

				</div>


				<div id="wplnst-filters" class="wplnst-tab">

					<table class="form-table">

						<?php if ($editable) : ?>

							<tr>
								<th style="width: 120px;"><?php _e('Anchor filters', 'wplnst'); ?></th>
								<td><table id="wplnst-elist-anchor-filters" class="wplnst-elist" cellspacing="0" cellpadding="0" border="0" data-editable="true" data-label="<?php _e('Anchor text', 'wplnst'); ?>"></table>
									<?php _e('Anchor text', 'wplnst'); ?> <select id="wplnst-af-new-type"><?php self::options($anchor_filters, false); ?></select>&nbsp;
									<input id="wplnst-af-new" type="text" class="regular-text" value="" placeholder="<?php _e('Anchor text filter', 'wplnst'); ?>" />&nbsp;
									<input class="button-secondary" type="button" id="wplnst-af-new-add" value="<?php _e('Add', 'wplnst'); ?>" /></td>
							</tr>

						<?php else : ?>

							<tr>
								<th style="width: 120px;"><?php _e('Anchor filters', 'wplnst'); ?></th>
								<td><?php if (empty($scan->anchor_filters)) : ?>-<?php else : ?><table id="wplnst-elist-anchor-filters" class="wplnst-elist wplnst-elist-readonly" cellspacing="0" cellpadding="0" border="0" data-label="<?php _e('Anchor text', 'wplnst'); ?>" data-editable="false"></table><?php endif; ?></td>
							</tr>

						<?php endif; ?>

					</table>

					<table class="form-table">

						<?php if ($editable) : ?>

							<tr>
								<th style="width: 120px;"><?php _e('Include URLs', 'wplnst'); ?></th>
								<td><table id="wplnst-elist-include-urls" class="wplnst-elist" cellspacing="0" cellpadding="0" border="0" data-editable="true"></table>
									<input id="wplnst-ius-new" type="text" class="regular-text" value="" />&nbsp;
									<select id="wplnst-ius-new-type"><?php self::options($url_filters, false); ?></select>&nbsp;
									<input class="button-secondary" type="button" id="wplnst-ius-new-add" value="<?php _e('Add', 'wplnst'); ?>" /></td>
							</tr>

						<?php else : ?>

							<tr>
								<th style="width: 120px;"><?php _e('Include URLs', 'wplnst'); ?></th>
								<td><?php if (empty($scan->include_urls)) : ?>-<?php else : ?><table id="wplnst-elist-include-urls" class="wplnst-elist wplnst-elist-readonly" cellspacing="0" cellpadding="0" border="0" data-editable="false"></table><?php endif; ?></td>
							</tr>

						<?php endif; ?>

						<?php if ($editable) : ?>

							<tr>
								<th style="width: 120px;"><?php _e('Exclude URLs', 'wplnst'); ?></th>
								<td><table id="wplnst-elist-exclude-urls" class="wplnst-elist" cellspacing="0" cellpadding="0" border="0" data-editable="true"></table>
									<input id="wplnst-eus-new" type="text" class="regular-text" value="" />&nbsp;
									<select id="wplnst-eus-new-type"><?php self::options($url_filters, false); ?></select>&nbsp;
									<input class="button-secondary" type="button" id="wplnst-eus-new-add" value="<?php _e('Add', 'wplnst'); ?>" /></td>
							</tr>

						<?php else : ?>

							<tr>
								<th style="width: 120px;"><?php _e('Exclude URLs', 'wplnst'); ?></th>
								<td><?php if (empty($scan->exclude_urls)) : ?>-<?php else : ?><table id="wplnst-elist-exclude-urls" class="wplnst-elist wplnst-elist-readonly" cellspacing="0" cellpadding="0" border="0" data-editable="false"></table><?php endif; ?></td>
							</tr>

						<?php endif; ?>

					</table>

					<table class="form-table">

						<?php if ($editable) : ?>

							<tr>
								<th style="width: 120px;"><?php _e('HTML attributes', 'wplnst'); ?></th>
								<td><table id="wplnst-elist-html-attributes" class="wplnst-elist" cellspacing="0" cellpadding="0" border="0" data-editable="true"></table>
									<select id="wplnst-hes-new" style="width: 55px;"><option value="a">a</option><option value="img">img</option></select>&nbsp;
									<select id="wplnst-hes-new-have"><?php self::options($html_attributes_having, false); ?></select>&nbsp;
									<input id="wplnst-hes-new-att" type="text" class="regular-text" value="" placeholder="<?php _e('Attribute name', 'wplnst'); ?>" style="width: 135px;" />&nbsp;
									<select id="wplnst-hes-new-op"><?php self::options($html_attributes_operators, false); ?></select>&nbsp;
									<input id="wplnst-hes-new-val" type="text" class="regular-text" value="" placeholder="<?php _e('Attribute value', 'wplnst'); ?>" style="width: 135px;" />&nbsp;
									<input id="wplnst-hes-new-add" class="button-secondary" type="button" value="<?php _e('Add', 'wplnst'); ?>" /></td>
							</tr>

						<?php else : ?>

							<tr>
								<th style="width: 120px;"><?php _e('HTML attributes', 'wplnst'); ?></th>
								<td><?php if (empty($scan->html_attributes)) : ?>-<?php else : ?><table id="wplnst-elist-html-attributes" class="wplnst-elist wplnst-elist-readonly" cellspacing="0" cellpadding="0" border="0" data-editable="false"></table><?php endif; ?></td>
							</tr>

						<?php endif; ?>

					</table>

					<table class="form-table">

						<?php $label = __('Accelerate crawling process integrating filters in main database query', 'wplnst'); ?>

						<?php if ($editable) : ?>

							<tr><td><input <?php self::checked($scan->filtered_query, true); ?> type="checkbox" id="wplnst-filtered-query" name="ck-filtered-query" value="on" /><label for="wplnst-filtered-query">&nbsp;<?php echo $label; ?></label></td></tr>

						<?php elseif (!empty($scan->anchor_filters) || !empty($scan->include_urls) || !empty($scan->exclude_urls) || !empty($scan->html_attributes)) : ?>

							<tr><td><?php echo $label; ?>: <strong><?php echo $scan->filtered_query? __('Yes', 'wplnst') : __('No', 'wplnst'); ?></strong></td></tr>

						<?php endif; ?>

					</table>

				</div>


				<div id="wplnst-status" class="wplnst-tab">

					<table class="form-table">

						<?php if ($editable) : ?>

							<tr>
								<th<?php if ($link_status_error) echo ' style="color: red;"'; ?>><?php _e('Track links by level', 'wplnst'); ?></th>
								<td class="wplnst-list"><?php foreach ($status_levels as $key => $value) : ?><input <?php self::checked($key, $scan->status_levels); ?> type="checkbox" name="ck-status-level[<?php echo $key; ?>]" id="ck-status-level-<?php echo $key; ?>" class="wplnst-status-level" value="on" /><label for="ck-status-level-<?php echo $key; ?>"><strong><?php echo $key; ?>00s</strong> <?php echo $value; ?></label><br /><?php endforeach; ?></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Track links by level', 'wplnst'); ?></th>
								<td class="wplnst-value-list"><?php if (empty($scan->status_levels_names) || !is_array($scan->status_levels_names)) : echo '-'; else : echo implode("<br />", array_map('esc_html', $scan->status_levels_names)); endif; ?></td>
							</tr>

						<?php endif; ?>

						<?php if ($editable) : ?>

							<tr>
								<th<?php if ($link_status_error) echo ' style="color: red;"'; ?>><?php _e('Track links by code', 'wplnst'); ?></th>
								<td class="wplnst-list"><table cellpadding="0" cellspacing="0" style="margin: 2px 0 0; padding: 0;">
									<?php foreach ($status_codes as $level => $codes) : ?>
										<?php $codes_keys = array_keys($codes); $num = 0; $inc = count($codes) / 2; $inc = ($inc != floor($inc))? floor($inc) : $inc - 1; foreach ($codes as $code => $name) : $num++; $inc++; ?>
											<tr style="margin: 0; padding: 0 0 25px;">
												<td style="margin: 0; padding: 0 25px 5px 0;"><input <?php self::checked($code, $scan->status_codes); ?> type="checkbox" name="ck-status-code[<?php echo $code; ?>]" id="ck-status-code-<?php echo $code; ?>" value="on" class="wplnst-code-level wplnst-code-level-<?php echo $level; ?>" /><label for="ck-status-code-<?php echo $code; ?>"><strong><?php echo $code; ?></strong> <?php echo $name; ?></label></td>
												<td style="margin: 0; padding: 0;"><?php if ($inc < count($codes)) : $code_r = $codes_keys[$inc]; ?><input <?php self::checked($code_r, $scan->status_codes); ?> type="checkbox" name="ck-status-code[<?php echo $code_r; ?>]" id="ck-status-code-<?php echo $code_r; ?>" value="on" class="wplnst-code-level wplnst-code-level-<?php echo $level; ?>" /><label for="ck-status-code-<?php echo $code_r; ?>"><strong><?php echo $code_r; ?></strong> <?php echo $codes[$code_r]; ?></label><?php endif; ?></td>
											</tr>
											<?php if ($num >= count($codes) / 2) break; ?>
										<?php endforeach; ?>
										<tr style="margin: 0; padding: 0;">
											<td colspan="2" style="margin: 0; padding: 0;">&nbsp;</td>
										</tr>
									<?php endforeach; ?>
								</table></td>
							</tr>

						<?php else : ?>

							<tr>
								<th><?php _e('Track links by code', 'wplnst'); ?></th>
								<td class="wplnst-value-list"><?php if (empty($scan->status_codes_names) || !is_array($scan->status_codes_names)) : echo '-'; else : echo implode("<br />", array_map('esc_html', $scan->status_codes_names)); endif; ?></td>
							</tr>

						<?php endif; ?>

					</table>

				</div>


				<?php if (false) : ?><div id="wplnst-scheduled" class="wplnst-tab">

				</div><?php endif; ?>


				<div id="wplnst-advanced" class="wplnst-tab">

					<?php if ('end' == $scan->status) : ?>

						<table class="form-table">
							<tr>
								<th><?php _e('Number of threads', 'wplnst'); ?></th>
								<td class="wplnst-value"><?php echo empty($scan->threads->max)? '-' : esc_html($scan->threads->max); ?></td>
							</tr>
							<tr>
								<th><?php _e('Connection timeout', 'wplnst'); ?></th>
								<td class="wplnst-value"><?php echo empty($scan->threads->connect_timeout)? '-' : esc_html($scan->threads->connect_timeout).' '.__('seconds', 'wplnst'); ?></td>
							</tr>
							<tr>
								<th><?php _e('Request timeout', 'wplnst'); ?></th>
								<td class="wplnst-value"><?php echo empty($scan->threads->request_timeout)? '-' : esc_html($scan->threads->request_timeout).' '.__('seconds', 'wplnst'); ?></td>
							</tr>
						</table>

					<?php else : ?>

						<p><?php _e('All these values are optional, leave them empty to use plugin defaults.', 'wplnst'); ?></p>

						<table class="form-table">
							<tr>
								<th><label for="tx-threads"><?php _e('Number of threads', 'wplnst'); ?></label></th>
								<td><input type="text" name="tx-threads" id="tx-threads" value="<?php echo empty($scan->threads->max)? '' : esc_attr($scan->threads->max); ?>" class="small-text" /> <?php printf(__('(optional, default %d threads)', 'wplnst'), $default_max_threads); ?></td>
							</tr>
							<tr>
								<th><label for="tx-connect-timeout"><?php _e('Connection timeout', 'wplnst'); ?></label></th>
								<td><input type="text" name="tx-connect-timeout" id="tx-connect-timeout" value="<?php echo empty($scan->threads->connect_timeout)? '' : esc_attr($scan->threads->connect_timeout); ?>" class="small-text" /> <?php _e('seconds', 'wplnst'); ?> <?php printf(__('(optional, default %d seconds)', 'wplnst'), $default_connect_timeout); ?></td>
							</tr>
							<tr>
								<th><label for="tx-request-timeout"><?php _e('Request timeout', 'wplnst'); ?></label></th>
								<td><input type="text" name="tx-request-timeout" id="tx-request-timeout" value="<?php echo empty($scan->threads->request_timeout)? '' : esc_attr($scan->threads->request_timeout); ?>" class="small-text" /> <?php _e('seconds', 'wplnst'); ?> <?php printf(__('(optional, default %d seconds)', 'wplnst'), $default_request_timeout); ?></td>
							</tr>
						</table>

					<?php endif; ?>

				</div>


				<p><input type="submit" value="<?php _e('Save scan changes', 'wplnst'); ?>" class="button button-primary" />
				<?php if ($is_ready && ('wait' == $scan->status)) : ?> &nbsp; <input id="wplnst-save-and-run" type="button" value="<?php _e('Save and run crawler', 'wplnst'); ?>" class="button" /><?php endif; ?>
				<?php if ($scan->id > 0) : ?> &nbsp;&nbsp; <a href="<?php echo esc_url(WPLNST_Core_Plugin::get_url_scans_delete($scan->id, $scan->hash)); ?>" class="wplnst-scan-delete-isolated wplnst-trash-editor" data-confirm-delete="<?php echo esc_attr(WPLNST_Admin::get_text('scan_delete_confirm')); ?>"><?php _e('Delete this scan', 'wplnst'); ?></a><?php endif; ?></p>

			</div>

		</form><?php
	}



}