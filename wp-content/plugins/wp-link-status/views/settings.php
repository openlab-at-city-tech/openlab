<?php

// Load views class
require_once dirname(__FILE__).'/views.php';

/**
 * Settings class
 *
 * @package WP Link Status
 * @subpackage Views
 */
class WPLNST_Views_Settings extends WPLNST_Views {



	/**
	 * Show scan edit form
	 */
	public static function view($args) {

		// Vars
		extract($args);

		?><form method="post" id="wplnst-form" action="<?php echo esc_url($action); ?>">

			<input type="hidden" name="settings_nonce" value="<?php echo esc_attr($nonce); ?>" />

			<h2 id="wplnst-tabs-nav" class="nav-tab-wrapper">
				<a id="wplnst-crawling-tab" href="#top#wplnst-crawling" class="nav-tab"><?php _e('Crawling', 'wplnst'); ?></a>
				<a id="wplnst-timing-tab" href="#top#wplnst-timing" class="nav-tab"><?php _e('Timing', 'wplnst'); ?></a>
				<a id="wplnst-advanced-tab" href="#top#wplnst-advanced" class="nav-tab"><?php _e('Advanced', 'wplnst'); ?></a>
			</h2>

			<div id="wplnst-tabs">

				<div id="wplnst-crawling" class="wplnst-tab wplnst-tab-default">

					<table class="form-table">
						<tr>
							<th><label for="tx-max-threads"><?php _e('Number of crawler threads', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-max-threads" id="tx-max-threads" value="<?php echo esc_attr(wplnst_get_nsetting('max_threads')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('One thread means an HTTP request to your site only used for crawling purposes.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-max-scans"><?php _e('Max crawlers running', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-max-scans" id="tx-max-scans" value="<?php echo esc_attr(wplnst_get_nsetting('max_scans')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('Number of crawlers allowed to run simultaneously, each one with its own threads.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-max-pack"><?php _e('Max pack items', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-max-pack" id="tx-max-pack" value="<?php echo esc_attr(wplnst_get_nsetting('max_pack')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('Total objects (posts, comments or blogroll) processed in one single thread.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-max-scans"><?php _e('Max URL request attempts', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-max-requests" id="tx-max-requests" value="<?php echo esc_attr(wplnst_get_nsetting('max_requests')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('Number of HTTP requests attempts before set an URL as wrong.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-max-redirs"><?php _e('Max redirections allowed', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-max-redirs" id="tx-max-redirs" value="<?php echo esc_attr(wplnst_get_nsetting('max_redirs')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('Total redirections steps allowed to follow from original URL.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-max-download"><?php _e('Max download size', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-max-download" id="tx-max-download" value="<?php echo esc_attr(wplnst_get_nsetting('max_download')); ?>" maxlength="5" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('KB', 'wplnst'); ?> &nbsp; <?php echo sprintf(__('(minimum value of %s KB and max value of %s KB).', 'wplnst'), number_format_i18n(wplnst_get_nsetting('max_download', 'min')), number_format_i18n(wplnst_get_nsetting('max_download', 'max'))); ?></td>
						</tr>
						<tr>
							<th><label for="tx-user-agent"><?php _e('Default User Agent', 'wplnst'); ?></label></th>
							<td colspan="3" class="wplnst-col-input"><input type="text" name="tx-user-agent" id="tx-user-agent" value="<?php echo esc_attr(wplnst_get_tsetting('user_agent')); ?>" maxlength="255" class="regular-text" style="width: 75%;" /></td>
						</tr>
					</table>

				</div>

				<div id="wplnst-timing" class="wplnst-tab">

					<table class="form-table">
						<tr>
							<th><label for="tx-connect-timeout"><?php _e('URL Connection timeout', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-connect-timeout" id="tx-connect-timeout" value="<?php echo esc_attr(wplnst_get_nsetting('connect_timeout')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?>. <?php _e('Max time allowed to establish a connection with the URL host.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-request-timeout"><?php _e('URL Request timeout', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-request-timeout" id="tx-request-timeout" value="<?php echo esc_attr(wplnst_get_nsetting('request_timeout')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?>. <?php _e('Max time allowed to retrieve headers and body from one URL.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-extra-timeout"><?php _e('URL Extra timeout check', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-extra-timeout" id="tx-extra-timeout" value="<?php echo esc_attr(wplnst_get_nsetting('extra_timeout')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?> <?php printf(__('(mininum value of %d seconds)', 'wplnst'), wplnst_get_nsetting('extra_timeout', 'min')); ?>. <?php _e('A little grace period to avoid timeouts conflicts.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-crawler-alive"><?php _e('Check crawler alive each', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-crawler-alive" id="tx-crawler-alive" value="<?php echo esc_attr(wplnst_get_nsetting('crawler_alive')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?> <?php printf(__('(mininum value of %d seconds)', 'wplnst'), wplnst_get_nsetting('crawler_alive', 'min')); ?>. <?php _e('Checks if a crawler is interrupted and if so restart it.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-total-objects"><?php _e('Total objects check each', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-total-objects" id="tx-total-objects" value="<?php echo esc_attr(wplnst_get_nsetting('total_objects')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?> <?php printf(__('(mininum value of %d seconds)', 'wplnst'), wplnst_get_nsetting('total_objects', 'min')); ?>. <?php _e('Total of objects (posts, comments or blogroll) to check links.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-summary-status"><?php _e('Summary of status each', 'wplnst'); ?></label></th>
							<td class="wplnst-col-input"><input type="text" name="tx-summary-status" id="tx-summary-status" value="<?php echo esc_attr(wplnst_get_nsetting('summary_status')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?> <?php printf(__('(mininum value of %d seconds)', 'wplnst'), wplnst_get_nsetting('summary_status', 'min')); ?>. <?php _e('Calculate status code totals to display data in real time.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-summary-phases"><?php _e('Summary of URLs each', 'wplnst'); ?></label></th>
							<td><input type="text" name="tx-summary-phases" id="tx-summary-phases" value="<?php echo esc_attr(wplnst_get_nsetting('summary_phases')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?> <?php printf(__('(mininum value of %d seconds)', 'wplnst'), wplnst_get_nsetting('summary_phases', 'min')); ?>. <?php _e('Current number of URLs processed or waiting to be checked.', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><label for="tx-summary-objects"><?php _e('Summary of objects each', 'wplnst'); ?></label></th>
							<td><input type="text" name="tx-summary-objects" id="tx-summary-objects" value="<?php echo esc_attr(wplnst_get_nsetting('summary_objects')); ?>" maxlength="3" class="small-text" /></td>
							<td class="wplnst-col-info"><?php _e('seconds', 'wplnst'); ?> <?php printf(__('(mininum value of %d seconds)', 'wplnst'), wplnst_get_nsetting('summary_objects', 'min')); ?>. <?php _e('Summary of objects (posts, comments or blogroll) with processed URLs.', 'wplnst'); ?></td>
						</tr>
					</table>

				</div>


				<div id="wplnst-advanced" class="wplnst-tab">

					<table class="form-table">
						<tr>
							<th><label for="tx-recursion-limit"><?php _e('Recursion limit', 'wplnst'); ?></label></th>
							<td colspan="2"><input type="text" name="tx-recursion-limit" id="tx-recursion-limit" value="<?php echo esc_attr(wplnst_get_nsetting('recursion_limit')); ?>" maxlength="5" class="small-text" /> <?php _e('function calls', 'wplnst'); ?></td>
						</tr>
						<tr>
							<th><?php _e('Data results pagination', 'wplnst'); ?></th>
							<td colspan="2" class="wplnst-list"><input type="checkbox" <?php echo wplnst_get_bsetting('mysql_calc_rows')? 'checked' : ''; ?> name="ck-mysql-calc-rows" id="ck-mysql-calc-rows" value="on" /><label for="ck-mysql-calc-rows">&nbsp;<?php _e('Use <code>SQL_CALC_FOUND_ROWS</code> to calculate total rows.', 'wplnst'); ?></label></td>
						</tr>
						<tr>
							<th><?php _e('Data on uninstall', 'wplnst'); ?></th>
							<td colspan="2" class="wplnst-list"><input type="checkbox" <?php echo wplnst_get_bsetting('uninstall_data')? 'checked' : ''; ?> name="ck-uninstall-data" id="ck-uninstall-data" value="on" /><label for="ck-uninstall-data">&nbsp;<?php _e('Options and exclusive MySQL tables will be removed when uninstall this plugin.', 'wplnst'); ?></label></td>
						</tr>
					</table>

				</div>


				<p><input type="submit" value="<?php _e('Save settings', 'wplnst'); ?>" class="button button-primary" /></p>

			</div>

		</form><?php
	}



}