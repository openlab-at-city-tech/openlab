<?php
/**
 * Display banners on settings page
 * @package Captcha by mysimplewp
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
			
		<?php }
	}
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
	function cptch_packages_banner() { ?>
				
	<?php }
}

/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_additional_options' ) ) {
	function cptch_additional_options() {
		$src = plugins_url( 'images/package/', dirname( __FILE__ ) ); ?>
		
	<?php }
}

/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_option_tab' ) ) {
	function cptch_option_tab() {
		$src = plugins_url( 'images/package/', dirname( __FILE__ ) ); ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Enable', 'captcha' );?></th>
				<td><fieldset><input type="checkbox" disabled="disabled" /></fieldset></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Hide from registered users', 'captcha' );?></th>
				<td><fieldset><input type="checkbox" disabled="disabled" /></fieldset></td>
			</tr>
			<tr>
				<th scope="row"><?php _e( 'Use general settings', 'captcha' );?></th>
				<td><fieldset><input type="checkbox" disabled="disabled" /></fieldset></td>
			</tr>
			<tr class="cptch_form_option_used_packages">
				<th scope="row"><?php _e( 'Use image packages', 'captcha' );?></th>
				<td>
					<fieldset>
						<div class="cptch_tabs_package_list cptch_pro_pack_tab">
							<ul class="cptch_tabs_package_list_items">
								<li>
									<span><input type="checkbox" disabled="disabled" /></span>
									<span><img src="<?php echo $src; ?>arabic_bt/0.png"></span>
									<span>Arabic ( black numbers - transparent background )</span>
								</li>
								<li>
									<span><input type="checkbox" disabled="disabled" /></span>
									<span><img src="<?php echo $src; ?>arabic_bw/0.png"></span>
									<span>Arabic ( black numbers - white background )</span>
								</li>
								<li>
									<span><input type="checkbox" disabled="disabled" /></span>
									<span><img src="<?php echo $src; ?>arabic_wb/0.png"></span>
									<span>Arabic ( white numbers - black background )</span>
								</li>
							</ul>
						</div>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="cptch_form_wp_comments_enable_time_limit"><?php _e( 'Enable time limit', 'captcha' ); ?></label></th>
				<td>
					<fieldset>
						<input type="checkbox" disabled="disabled" />
						&nbsp;<?php _e( 'for', 'captcha' ); ?>&nbsp;
						<input value="120" type="number" disabled="disabled" />
						&nbsp;<?php _e( 'seconds', 'captcha' ); ?></span>
					</fieldset>
				</td>
			</tr>
		</table>
	<?php }
}

/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_use_several_packages' ) ) {
	function cptch_use_several_packages() { ?>
		<table class="form-table">
			<tr>
				<th scope="row"><?php _e( 'Use several image packages at the same time', 'captcha' );?></th>
				<td><fieldset><input type="checkbox" disabled="disabled" /></fieldset></td>
			</tr>
		</table>
	<?php }
}