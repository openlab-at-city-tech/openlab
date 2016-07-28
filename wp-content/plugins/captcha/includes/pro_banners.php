<?php
/**
 * Display banners on settings page
 * @package Captcha by BestWebSoft
 * @since 4.1.5
 */

/**
 * Show ads for PRO
 * @param     string     $func        function to call
 * @return    void
 */
if ( ! function_exists( 'cptch_pro_block' ) ) {
	function cptch_pro_block( $func, $show_cross = true, $display_always = false ) {
		global $cptch_plugin_info, $wp_version, $cptch_options;
		if ( $display_always || ! bws_hide_premium_options_check( $cptch_options ) ) { ?>
			<div class="bws_pro_version_bloc cptch_pro_block <?php echo $func;?>" title="<?php _e( 'This options is available in Pro version of plugin', 'captcha' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'captcha' ); ?>"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<?php call_user_func( $func ); ?>
				</div>
				<div class="bws_pro_version_tooltip">
					<div class="bws_info"><?php _e( 'Unlock premium options by upgrading to Pro version', 'captcha' ); ?></div>
					<a class="bws_button" href="http://bestwebsoft.com/products/captcha/?k=9701bbd97e61e52baa79c58c3caacf6d&pn=75&v=<?php echo $cptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Captcha Pro by BestWebSoft Plugin"><?php _e( 'Learn More', 'captcha' ); ?></a>
				</div>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'cptch_basic_banner' ) ) {
	function cptch_basic_banner() { ?>
		<table class="form-table bws_pro_version">
			<tr valign="top">
				<th scope="row"><?php _e( 'Enable CAPTCHA for', 'captcha' ); ?>:</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span><?php _e( 'Enable CAPTCHA for', 'captcha' ); ?></span></legend>
						<label><input disabled='disabled' type="checkbox" /> Contact Form 7</label><br />
						<label><input disabled='disabled' type="checkbox" name="cptchpr_subscriber" value="1" /> Subscriber by BestWebSoft</label><br />
						<label><input disabled='disabled' type="checkbox" /> <?php _e( 'Buddypress Registration form', 'captcha' ); ?></label><br />
						<label><input disabled='disabled' type="checkbox" /> <?php _e( 'Buddypress Comments form', 'captcha' ); ?></label><br />
						<label><input disabled='disabled' type="checkbox" /> <?php _e( 'Buddypress "Create a Group" form', 'captcha' ); ?></label><br />
						<label><input disabled='disabled' type="checkbox" /> <?php _e( 'WooCommerce login form', 'captcha' ); ?></label><br />
						<label><input disabled='disabled' type="checkbox" /> <?php _e( 'WooCommerce Register form', 'captcha' ); ?></label><br />
						<label><input disabled='disabled' type="checkbox" /> <?php _e( 'WooCommerce Lost Password form', 'captcha' ); ?></label><br />
						<label><input disabled='disabled' type="checkbox" /> <?php _e( 'WooCommerce Checkout Billing form', 'captcha' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" colspan="2">
					* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'captcha' ); ?>
				</th>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'cptch_advanced_banner' ) ) {
	function cptch_advanced_banner() { ?>
		<table class="form-table bws_pro_version">
			<tr valign="top">
				<th scope="row"><?php _e( 'Use several packages at the same time', 'captcha' ); ?></th>
				<td>
					<input disabled='disabled' type="checkbox" /><br/>
					<span class="bws_info"><?php _e( 'If this option is enabled, CAPTCHA will be use pictures from different packages at the same time', 'captcha' ); ?>.</span>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'cptch_whitelist_banner' ) ) {
	function cptch_whitelist_banner() { ?>
		<table class="form-table bws_pro_version">
			<tr>
				<td valign="top"><?php _e( 'Reason', 'captcha' ); ?>
					<input disabled type="text" style="margin: 10px 0;"/><br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats", 'captcha' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></span><br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for IPs: a comma", 'captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'captcha' ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return', 'captcha' ); ?></span><br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for reasons: a comma", 'captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'captcha' ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return', 'captcha' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}

/**
 * @since 4.2.0
 */
if ( ! function_exists( 'cptch_packages_banner' ) ) {
	function cptch_packages_banner() {
		$date = date_i18n( get_option( 'date_format' ), strtotime( '1.06.2016' ) );
		$src  = plugins_url( 'images/package/', dirname(__FILE__) ); ?>
		<div class="upload-plugin cptch_install_package_wrap">
			<div class="bws_form wp-upload-form">
				<p>
					<label><input type="file" disabled="disabled"></label>
				</p>
				<p><?php _e( 'If the package already exists', 'captcha' ); ?></p>
				<p>
					<label><input disabled="disabled" checked="checked" type="radio" /><?php _e( 'Skip it', 'captcha' ); ?><label><br>
					<label><input disabled="disabled" type="radio" /><?php _e( 'Update the existed one', 'captcha' ); ?><label><br>
					<label><input disabled="disabled" type="radio" /><?php _e( 'Save it as new', 'captcha' ); ?><label>
				</p>
				<p>
					<label><input disabled="disabled" class="button-primary" value="<?php _e( 'Install Now', 'captcha' ); ?>" type="submit" /></label>
					<a class="cptch_add_ons_link" href="http://bestwebsoft.com/products/captcha/addons/" target="_blank"><?php _e( 'Browse packages', 'captcha' ); ?></a>
				</p>
			</div>
		</div>
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<input id="cb-select-all-1" type="checkbox" disabled="disabled">
					</td>
					<th scope="col" id="name" class="manage-column column-name column-primary sortable desc">
						<a href="#"><span><?php _e( 'Package', 'captcha' ); ?></span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="add_time" class="manage-column column-add_time sortable desc">
						<a href="#"><span><?php _e( 'Date', 'captcha' ); ?></span><span class="sorting-indicator"></span></a>
					</th>
				</tr>
			</thead>
			<tbody id="the-list">
				<tr>
					<th scope="row" class="check-column"></th>
					<td class="name column-name has-row-actions column-primary">
						<div class="has-media-icon">
							<span class="media-icon image-icon"><img src="<?php echo $src; ?>arabic_bw/0.png"></span>
							Arabic ( black numbers - white background )
						</div>
					</td>
					<td class="add_time column-add_time"><?php echo $date; ?></td>
				</tr>
				<tr>
					<th scope="row" class="check-column"></th>
					<td class="name column-name has-row-actions column-primary">
						<div class="has-media-icon">
							<span class="media-icon image-icon"><img src="<?php echo $src; ?>arabic_wb/0.png"></span>
							Arabic ( white numbers - black background )
						</div>
					</td>
					<td class="add_time column-add_time"><?php echo $date; ?></td>
				</tr>

				<tr>
					<th scope="row" class="check-column"></th>
					<td class="name column-name has-row-actions column-primary">
						<div class="has-media-icon">
							<span class="media-icon image-icon"><img src="<?php echo $src; ?>dots_bw/1.png"></span>
							Dots ( black dots - white background )
						</div>
					</td>
					<td class="add_time column-add_time"><?php echo $date; ?></td>
				</tr>
				<tr>
					<th scope="row" class="check-column"></th>
					<td class="name column-name has-row-actions column-primary">
						<div class="has-media-icon">
							<span class="media-icon image-icon"><img src="<?php echo $src; ?>dots_wb/1.png"></span>
							Dots ( white dots - black background )
						</div>
					</td>
					<td class="add_time column-add_time"><?php echo $date; ?></td>
				</tr>

				<tr>
					<th scope="row" class="check-column"></th>
					<td class="name column-name has-row-actions column-primary">
						<div class="has-media-icon">
							<span class="media-icon image-icon"><img src="<?php echo $src; ?>roman_bw/1.png"></span>
							Roman ( black numbers - white background )
						</div>
					</td>
					<td class="add_time column-add_time"><?php echo $date; ?></td>
				</tr>
				<tr>
					<th scope="row" class="check-column"></th>
					<td class="name column-name has-row-actions column-primary">
						<div class="has-media-icon">
							<span class="media-icon image-icon"><img src="<?php echo $src; ?>roman_wb/1.png"></span>
							Roman ( white numbers - black background )
						</div>
					</td>
					<td class="add_time column-add_time"><?php echo $date; ?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<input id="cb-select-all-1" type="checkbox" disabled="disabled">
					</td>
					<th scope="col" id="name" class="manage-column column-name column-primary sortable desc">
						<a href="#"><span><?php _e( 'Package', 'captcha' ); ?></span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col" id="add_time" class="manage-column column-add_time sortable desc">
						<a href="#"><span><?php _e( 'Date', 'captcha' ); ?></span><span class="sorting-indicator"></span></a>
					</th>
				</tr>
			</tfoot>
		</table>
	<?php }
}