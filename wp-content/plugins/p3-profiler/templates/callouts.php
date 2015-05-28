<?php
if ( !defined('P3_PATH') )
	die( 'Forbidden ');

$opts = get_option( 'p3-profiler_options' );
?>
<script type="text/javascript">

	/*****************************************************************/
	/**  AUTO SCANNER HELPER OBJECT                                 **/
	/*****************************************************************/
	// This will load all of the pages in the list, then turn off
	// the profile mode and view the results when complete.
	var P3_Scan = {

		// List of pages to scan
		pages: <?php echo json_encode( self::list_of_pages() ); ?>,

		// Current page
		current_page: 0,

		// Pause flag
		paused: false,

		// Create a random string
		random: function(length) {
			var ret = "";
			var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			for ( var i = 0 ; i < length ; i++ ) {
				ret += alphabet.charAt( Math.floor( Math.random() * alphabet.length ) );
			}
			return ret;
		},

		// Start
		start: function() {
			
			// If cache busting is disabled, remove P3_NOCACHE from the pages
			if ( jQuery( '#p3-cache-buster' ).prop( 'checked' ) ) {
				for ( i = 0 ; i < P3_Scan.pages.length ; i++ ) {
					if ( P3_Scan.pages[i].indexOf('?') > -1 ) {
						P3_Scan.pages[i] += '&P3_NOCACHE=' + P3_Scan.random(8);
					} else {
						P3_Scan.pages[i] += '?P3_NOCACHE=' + P3_Scan.random(8);
					}
				}
			}

			// Form data
			data = {
				'p3_ip' : jQuery( '#p3-advanced-ip' ).val(),
				'p3_disable_opcode_cache' : jQuery( '#p3-disable-opcode-cache' ).prop( 'checked' ),
				'p3_cache_buster' : jQuery( '#p3-cache-buster' ).prop( 'checked' ),
				'p3_debug' : jQuery( '#p3-debug' ).prop( 'checked' ),
				'p3_scan_name' : jQuery( "#p3-scan-name" ).val(),
				'action' : 'p3_start_scan',
				'p3_nonce' : jQuery( "#p3_nonce" ).val()
			}

			// Turn on the profiler
			jQuery.post( ajaxurl, data, function( response ) {

				// Start scanning pages
				jQuery( "#p3-scan-frame" ).attr( "onload", "P3_Scan.next_page();" );
				jQuery( "#p3-scan-frame" ).attr( "src", P3_Scan.pages[0] );
				P3_Scan.current_page = 0;
				P3_Scan.update_display();
			});
		},
		
		// Pause
		pause: function() {
			
			// Turn off the profiler
			data = {
				'action' : 'p3_stop_scan',
				'p3_nonce' : '<?php echo wp_create_nonce( 'p3_ajax_stop_scan' ); ?>'
			}
			jQuery.post( ajaxurl, data, function( response ) {

				// Hide the cancel button
				jQuery( "#p3-cancel-scan-buttonset" ).hide();
				jQuery( "#p3-resume-scan-buttonset" ).show();
				jQuery( "#p3-view-results-buttonset" ).hide();
				
				// Show the view results button
				jQuery( "#p3-view-incomplete-results-submit" ).attr( "data-scan-name", response );
				
				// Pause
				P3_Scan.paused = true;
				
				// Update the caption
				jQuery( "#p3-scanning-caption" ).html( "<?php _e( 'Scanning is paused.', 'p3-profiler' ); ?>" ).css( "color", "black" );
			});
		},

		// Resume
		resume: function() {
			
			data = {
				'p3_ip' : jQuery( '#p3-advanced-ip' ).val(),
				'p3_disable_opcode_cache' : jQuery( '#p3-disable-opcode-cache' ).prop( 'checked' ),
				'p3_cache_buster' : jQuery( '#p3-cache-buster' ).prop( 'checked' ),
				'p3_debug' : jQuery( '#p3-debug' ).prop( 'checked' ),
				'p3_scan_name' : jQuery( "#p3-scan-name" ).val(),
				'action' : 'p3_start_scan',
				'p3_nonce' : jQuery( "#p3_nonce" ).val()
			}

			// Turn on the profiler
			jQuery.post( ajaxurl, data, function( response ) {

					// Show the cancel button
					P3_Scan.paused = false;
					jQuery( "#p3-cancel-scan-buttonset" ).show();
					jQuery( "#p3-resume-scan-buttonset" ).hide();
					jQuery( "#p3-view-results-buttonset" ).hide();
					P3_Scan.update_display();
					P3_Scan.next_page();
			});
		},

		// Stop
		stop: function() {
			
			// Turn off the profiler
			data = {
				'action' : 'p3_stop_scan',
				'p3_nonce' : '<?php echo wp_create_nonce( 'p3_ajax_stop_scan' ); ?>'
			}
			jQuery.post( ajaxurl, data, function( response ) {
				
				// Hide the cancel button
				jQuery( "#p3-cancel-scan-buttonset" ).hide();
				jQuery( "#p3-resume-scan-buttonset" ).hide();
				jQuery( "#p3-view-results-buttonset" ).show();
				
				// Show the view results button
				jQuery( "#p3-view-results-submit" ).attr( "data-scan-name", response );
				
				// Update the caption
				jQuery( "#p3-scanning-caption" ).html( "<?php _e( 'Scanning is complete.', 'p3-profiler' ); ?>" ).css( "color", "black" );
			});
		},

		// Update the display
		update_display : function() {
			jQuery( "#p3-scanning-caption" ).html( '<?php _e( 'Scanning', 'p3-profiler' ); ?> ' + P3_Scan.pages[P3_Scan.current_page] ).css( "color", "" );
			jQuery( "#p3-progress" ).progressbar( "value", ( P3_Scan.current_page / ( P3_Scan.pages.length - 1 ) ) * 100 );
		},

		// Look at the next page
		next_page : function() {

			// Paused?
			if ( P3_Scan.paused ) {
				return true;
			}

			// Is it time to stop?
			if ( P3_Scan.current_page >= P3_Scan.pages.length - 1 ) {
				P3_Scan.stop();
				return true;
			}

			// Next page
			jQuery( "#p3-scan-frame" ).attr( "src", P3_Scan.pages[++P3_Scan.current_page] );

			// Update the display
			P3_Scan.update_display();
		}
	};

	// Sync save settings
	function p3_sync_advanced_settings() {
		if ( jQuery( "#p3-use-current-ip" ).prop( "checked" ) ) {
			jQuery( "#p3-advanced-ip" ).val( "<?php echo esc_js( p3_profiler_get_ip() ); ?>" );
			jQuery( "#p3-advanced-ip" ).prop( "disabled", true );
		} else {
			<?php $ip = $opts['ip_address']; if ( empty( $ip ) ) { $ip = p3_profiler_get_ip(); } ?>
			jQuery( "#p3-advanced-ip" ).val( "<?php echo esc_js( $ip ); ?>" );
			jQuery( "#p3-advanced-ip" ).prop( "disabled", false );
		}
	}

	// Onload functionality
	jQuery( document ).ready( function( $) {

		/*****************************************************************/
		/**  DIALOGS                                                    **/
		/*****************************************************************/

		// IP settings
		$( "#p3-ip-dialog" ).dialog({
			'autoOpen' : false,
			'closeOnEscape' : true,
			'draggable' : false,
			'resizable' : false,
			'modal' : true,
			'width' : 450,
			'height' : 450,
			'title' : "<?php _e( 'Advanced Settings', 'p3-profiler' ); ?>",
			'buttons' :
			[
				{
					text: '<?php _e( 'OK', 'p3-profiler' ); ?>',
					'class' : 'button-secondary',
					click: function() {
						
						// Save settings
						data = {
							'action' : 'p3_save_settings',
							'p3_disable_opcode_cache' : $( '#p3-disable-opcode-cache' ).prop( 'checked' ),
							'p3_use_current_ip' : $( '#p3-use-current-ip' ).prop( 'checked' ),
							'p3_ip_address' : $( '#p3-advanced-ip' ).val(),
							'p3_cache_buster' : $( '#p3-cache-buster' ).prop( 'checked' ),
							'p3_debug' : $( '#p3-debug' ).prop( 'checked' ),
							'p3_nonce' : '<?php echo wp_create_nonce( 'p3_save_settings' ); ?>'
						};
						$.post( ajaxurl, data, function( response ) {
							$( "#p3-ip-dialog" ).dialog( "close" );
						});
					}
				},
				{
					text: '<?php _e( 'Cancel', 'p3-profiler'); ?>',
					'class': 'p3-cancel-button',
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});

		// Iframe scanner
		$( "#p3-scanner-dialog" ).dialog({
			'autoOpen' : false,
			'closeOnEscape' : true,
			'draggable' : false,
			'resizable' : false,
			'modal' : true,
			'width': 800,
			'height' : 600,
			'title' : "<?php _e( 'Performance Scan', 'p3-profiler' ); ?>",
			'dialogClass' : 'noPadding'
		});

		// Auto scan or manual scan 
		$( "#p3-scan-name-dialog" ).dialog({
			'autoOpen' : false,
			'closeOnEscape' : true,
			'draggable' : false,
			'resizable' : false,
			'modal' : true,
			'width' : 500,
			'height' : 175,
			'title' : '<?php _e( 'Scan Name', 'p3-profiler' ); ?>'
			// 'dialogClass' : 'noTitle'
		});

		// Progress dialog
		$( "#p3-progress-dialog" ).dialog({
			'autoOpen' : false,
			'closeOnEscape' : false,
			'draggable' : false,
			'resizable' : false,
			'modal' : true,
			'width' : 450,
			'height' : 120,
			'dialogClass' : 'noTitle'
		});



		/*****************************************************************/
		/**  LINKS                                                      **/
		/*****************************************************************/
		
		// Advanced settings link
		$( "#p3-advanced-settings" ).click( function() {
			$( "#p3-ip-dialog" ).dialog( "open" );
		});



		/*****************************************************************/
		/**  BUTTONS                                                    **/
		/*****************************************************************/
		
		// Start scan button
		$( "#p3-start-scan-submit" ).click( function() {
			
			// Stay checked to keep the styling
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );

			url = $( "#p3-scan-frame" ).attr( "data-defaultsrc" );
			if ( url.indexOf('?') >= 0 || url.indexOf('&') >= 0 ) {
				url += '&P3_HIDE_ADMIN_BAR=1';
			} else if ( url.charAt(url.length - 1) != '/' ) {
				url += '/?P3_HIDE_ADMIN_BAR=1';
			} else {
				url += '?P3_HIDE_ADMIN_BAR=1';
			}

			$( "#p3-scan-frame" ).attr( "src", url );
			$( "#p3-scanner-dialog" ).dialog( "open" );
			$( "#p3-scan-name-dialog" ).dialog( "open" );
		});
		
		// Stop scan button
		$( "#p3-stop-scan-submit" ).click( function() {

			// Stay checked to keep the styling
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );

			// Turn off the profiler
			data = {
				'action' : 'p3_stop_scan',
				'p3_nonce' : '<?php echo wp_create_nonce( 'p3_ajax_stop_scan' ); ?>'
			}
			jQuery.post( ajaxurl, data, function( response ) {
				location.reload();
			});
		});

		// Auto scan button
		$( "#p3-auto-scan-submit" ).click( function() {
			
			// Stay checked to keep the styling
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );

			// Close the "auto or manual" dialog
			$( "#p3-scan-name-dialog" ).dialog( "close" );

			// Open the progress bar dialog
			$( "#p3-progress-dialog" ).dialog( "open" );

			// Initialize the progress bar to 0%
			$( "#p3-progress" ).progressbar({
				'value': 0
			});

			P3_Scan.start();
		});

		// Manual scan button
		$( "#p3-manual-scan-submit" ).click( function() {
			
			// Stay checked to keep the styling
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );
			
			// Form data
			data = {
				'p3_ip' : jQuery( '#p3-advanced-ip' ).val(),
				'p3_disable_opcode_cache' : jQuery( '#p3-disable-opcode-cache' ).prop( 'checked' ),
				'p3_cache_buster' : jQuery( '#p3-cache-buster' ).prop( 'checked' ),
				'p3_debug' : jQuery( '#p3-debug' ).prop( 'checked' ),
				'p3_scan_name' : jQuery( "#p3-scan-name" ).val(),
				'action' : 'p3_start_scan',
				'p3_nonce' : jQuery( "#p3_nonce" ).val()
			}

			// Turn on the profiler
			jQuery.post( ajaxurl, data, function( response ) {
			});

			$( "#p3-scan-name-dialog" ).dialog( "close" );
			$( "#p3-scan-caption" ).hide();
			$( "#p3-manual-scan-caption" ).show();
		});
		
		// Manual scan "I'm done" button
		$( "#p3-manual-scan-done-submit" ).click( function() {
			data = {
				'action' : 'p3_stop_scan',
				'p3_nonce' : '<?php echo wp_create_nonce( 'p3_ajax_stop_scan' ); ?>'
			}
			jQuery.post( ajaxurl, data, function( response ) {
				location.href = "<?php echo esc_url_raw( add_query_arg( array( 'p3_action' => 'view-scan', 'current_scan' => '1', 'name' => null ) ) ); ?>&name=" + response;
			})
			$( "#p3-scanner-dialog" ).dialog( "close" );
		});
		
		// Manual scan cancel link
		$( "#p3-manual-scan-cancel" ).click( function() {
			P3_Scan.pause();
			$( "#p3-scanner-dialog" ).dialog( "close" );
		});

		// Cancel scan button
		$( "#p3-cancel-scan-submit" ).click( function() {
			
			// Stay checked to keep the styling
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );

			P3_Scan.pause();
		});
		
		// Resume
		$( "#p3-resume-scan-submit" ).click( function() {
			
			// Stay checked to keep the styling
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );

			P3_Scan.resume();
		});
		
		// View results button
		$( "#p3-view-results-submit" ).click( function() {

			// Stay checked to keep the styling
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );

			// Close the dialogs
			jQuery( "#p3-scanner-dialog" ).dialog( "close" );
			jQuery( "#p3-progress-dialog" ).dialog( "close" );

			// View the scan
			location.href = "<?php echo esc_url_raw( add_query_arg( array( 'p3_action' => 'view-scan', 'current_scan' => '1', 'name' => null ) ) ); ?>&name=" + $( this ).attr( "data-scan-name" );
		});
		$( "#p3-view-incomplete-results-submit" ).click( function() {
			$( "#p3-view-results-submit" ).attr( "data-scan-name", $( "#p3-view-incomplete-results-submit" ).attr( "data-scan-name" ) );
			$( "#p3-view-results-submit" ).trigger( "click" );
		});


		/*****************************************************************/
		/**  OTHER                                                      **/
		/*****************************************************************/
		// Enable / disable buttons based on scan name input
		$( "#p3-scan-name" ).live( "keyup", function() {
			if ( $( this ).val().match(/^[a-zA-Z0-9_\.-]+$/) ) {
				$( "#p3-auto-scan-submit" ).button( "enable" )
				$( "#p3-manual-scan-submit" ).button( "enable" );
			} else {
				$( "#p3-auto-scan-submit" ).button( "disable" );
				$( "#p3-manual-scan-submit" ).button( "disable" );
			}
		});
		
		// Enable / disable the IP text based on the "use current ip" checkbox
		$( "#p3-use-current-ip").live( "click", p3_sync_advanced_settings );
		p3_sync_advanced_settings();

		// Callouts
		$( "div.p3-callout-inner-wrapper" )
		.corner( "round 8px" )
		.parent()
		.css( "padding", "4px" )
		.corner( "round 10px" );

		// Start / stop buttons
		$( "#p3-scan-form-wrapper" ).corner( "round 8px" );
		
		// Continue scan
		$( "a.p3-continue-scan" ).click( function() {
			$( "#p3-start-scan-submit" ).trigger( "click" );
			$( "#p3-scan-name" ).val( $( this ).attr( "data-name" ).replace(/\.json$/, '' ) );
		});
	});
</script>
<table id="p3-quick-report" cellpadding="0" cellspacing="0">
	<tr>

		<td>
			<div class="ui-widget-header" id="p3-scan-form-wrapper">
				<?php if ( false !== ( $info = self::scan_enabled() ) ) { ?>
					<!-- Stop scan button -->

					<strong>IP:</strong> <?php echo htmlentities( $info['ip'] ); ?>
					<div class="p3-big-button"><input type="checkbox" checked="checked" id="p3-stop-scan-submit" />
					<label for="p3-stop-scan-submit"><?php _e( 'Stop Scan', 'p3-profiler' ); ?></label></div>
					<?php echo htmlentities( $info['name'] ); ?>

				<?php } else { ?>

					<!-- Start scan button -->
					<?php echo wp_nonce_field( 'p3_ajax_start_scan', 'p3_nonce' ); ?>
					<strong><?php _e( 'My IP:', 'p3-profiler' ); ?></strong><?php echo htmlentities( p3_profiler_get_ip() ); ?>
					<div class="p3-big-button"><input type="checkbox" checked="checked" id="p3-start-scan-submit" />
					<label for="p3-start-scan-submit" class="scan-btn"><?php _e( 'Start Scan', 'p3-profiler' ); ?></label></div>
					<a href="javascript:;" id="p3-advanced-settings"><?php _e( 'Advanced Settings', 'p3-profiler' ); ?></a>

				<?php } ?>
			</div>
		</td>

		<!-- First callout cell -->
		<td class="p3-callout">
			<div class="p3-callout-outer-wrapper qtip-tip total-plugins-tip" title="<?php esc_attr_e( 'Total number of active plugins, including must-use plugins, on your site.', 'p3-profiler' ); ?>">
				<div class="p3-callout-inner-wrapper">
					<div class="p3-callout-caption total-plugins-caption"><?php _e( 'Total Plugins:', 'p3-profiler' ); ?></div>
					<div class="p3-callout-data total-plugins-data">
						<?php
						// Get the total number of plugins
						$active_plugins = count( get_mu_plugins() );
						foreach ( get_plugins() as $plugin => $junk ) {
							if ( is_plugin_active( $plugin ) ) {
								$active_plugins++;
							}
						}
						echo $active_plugins;
						?>
					</div>
					<div class="p3-callout-caption total-plugins-info">(<?php _e( 'currently active', 'p3-profiler' ); ?>)</div>
				</div>
			</div>
		</td>

		<!-- Second callout cell -->
		<td class="p3-callout">
			<div class="p3-callout-outer-wrapper qtip-tip load-time-tip" title="<?php esc_attr_e( 'Total number of seconds dedicated to plugin code per visit on your site.', 'p3-profiler' ); ?>"
				<?php if ( !empty( self::$scan ) ) { ?>title="<?php esc_attr_e( 'From', 'p3-profiler' ); ?> <?php echo basename( self::$scan ); ?><?php } ?>">
				<div class="p3-callout-inner-wrapper">
					<div class="p3-callout-caption load-time-caption"><?php _e( 'Plugin Load Time', 'p3-profiler' ); ?></div>
					<div class="p3-callout-data load-time-data">
						<?php if ( null === self::$profile ) { ?>
							<span class="p3-faded-grey"><?php _e( 'n/a', 'p3-profiler' ); ?></span>
						<?php } else { ?>
							<?php printf( '%.3f', self::$profile->averages['plugins'] ); ?>
						<?php } ?>
					</div>
					<div class="p3-callout-caption load-time-info">(<?php _e( 'sec. per visit', 'p3-profiler' ); ?>)</div>
				</div>
			</div>
		</td>

		<!-- Third callout cell -->
		<td class="p3-callout">
			<div class="p3-callout-outer-wrapper qtip-tip impact-tip" title="<?php esc_attr_e( 'Percent of load time on your site dedicated to plugin code', 'p3-profiler' ); ?>"
				<?php if ( !empty( self::$scan ) ) { ?>title="<?php esc_attr_e( 'From', 'p3-profiler' ); ?> <?php echo basename( self::$scan ); ?><?php } ?>">
				<div class="p3-callout-inner-wrapper">
					<div class="p3-callout-caption impact-caption"><?php _e( 'Plugin Impact', 'p3-profiler' ); ?></div>
					<div class="p3-callout-data impact-data">
						<?php if ( null === self::$profile ) { ?>
							<span class="p3-faded-grey"><?php _e( 'n/a', 'p3-profiler' ); ?></span>
						<?php } else { ?>
							<?php printf( '%.1f%%', self::$profile->averages['plugin_impact'] ); ?>
						<?php } ?>
					</div>
					<div class="p3-callout-caption impact-info">(<?php _e( 'of page load time', 'p3-profiler' ); ?>)</div>
				</div>
			</div>
		</td>

		<!-- Fourth callout cell -->
		<td class="p3-callout">
			<div class="p3-callout-outer-wrapper qtip-tip mysql-tip" title="<?php esc_attr_e( 'Total number of database queries per visit', 'p3-profiler' ); ?>"
				<?php if ( !empty( self::$scan ) ) { ?>title="<?php esc_attr_e( 'From', 'p3-profiler' ); ?> <?php echo basename( self::$scan ); ?><?php } ?>">
				<div class="p3-callout-inner-wrapper">
					<div class="p3-callout-caption mysql-caption"><?php _e( 'MySQL Queries', 'p3-profiler' ); ?></div>
					<div class="p3-callout-data mysql-data">
						<?php if ( null === self::$profile ) { ?>
							<span class="p3-faded-grey"><?php _e( 'n/a', 'p3-profiler' ); ?></span>
						<?php } else { ?>
							<?php echo round( self::$profile->averages['queries'] ); ?>
						<?php } ?>
					</div>
					<div class="p3-callout-caption mysql-info"><?php _e( 'per visit', 'p3-profiler' ); ?></div>
				</div>
			</div>
		</td>

	</tr>
</table>

<!-- Dialog for IP settings -->
<div id="p3-ip-dialog" class="p3-dialog">
	<div class="ip-text">
		<?php _e( 'IP address or pattern:', 'p3-profiler' ); ?><br /><br />
		<input type="checkbox" id="p3-use-current-ip" <?php if ( true == $opts['use_current_ip'] ) : ?>checked="checked"<?php endif; ?> />
		<label for="p3-use-current-ip"><?php _e( 'Use my IP address', 'p3-profiler' ); ?></label>
		<br />
		<input type="text" id="p3-advanced-ip" style="width:90%;" size="35" value="" title="<?php esc_attr_e( 'Enter IP address or regular expression pattern', 'p3-profiler' ); ?>" />
		<br />
		<em class="p3-em ip-example-text"><?php _e( 'Example: 1.2.3.4 or ( 1.2.3.4|4.5.6.7 )', 'p3-profiler' ); ?></em>
	</div>
	<br />
	<div class="opcode-text">
		<input type="checkbox" id="p3-disable-opcode-cache" <?php if ( true == $opts['disable_opcode_cache'] ) : ?>checked="checked"<?php endif; ?> />
		<label for="p3-disable-opcode-cache"><?php _e( 'Attempt to disable opcode optimizers', 'p3-profiler' ); ?> <em>(<?php _e( 'recommended', 'p3-profiler' ); ?>)</em></label>
		<br />
		<em class="p3-em"><?php _e( 'This can increase accuracy in plugin detection, but decrease accuracy in timing', 'p3-profiler' ); ?></em>
	</div>
	<br />
	<div class="cache-text">
		<input type="checkbox" id="p3-cache-buster" <?php if ( true == $opts['cache_buster'] ) : ?>checked="checked"<?php endif; ?> />
		<label for="p3-cache-buster"><?php _e( 'Attempt to circumvent browser cache', 'p3-profiler' ); ?></label>
		<br />
		<em class="p3-em"><?php printf( __('This may help fix a "No visits recorded" error message.  See the <a href="%s" class="cache-help">help</a> page for details.', 'p3-profiler' ),
			esc_url( add_query_arg( array( 'p3_action' => 'help', 'current_scan' => null ) ) ) . '#q-debug-log'
		); ?> </em>
	</div>
	<br />
	<div class="debug-text">
		<input type="checkbox" id="p3-debug" <?php if ( true == $opts['debug'] ) : ?>checked="checked"<?php endif; ?> />
		<label for="p3-debug"><?php _e( 'Debug mode', 'p3-profiler' ); ?></label>
		<br />
		<em class="p3-em"><?php printf( __('This will log the last 100 visits.  Check the <a href="%s" class="debug-help">help</a> page to view log messages.', 'p3-profiler' ),
			esc_url( add_query_arg( array( 'p3_action' => 'help', 'current_scan' => null ) ) ) . '#q-debug-log'
		); ?></em>
	</div>
</div>

<!-- Dialog for iframe scanner -->
<div id="p3-scanner-dialog" class="p3-dialog">
	<iframe id="p3-scan-frame" frameborder="0"
		data-defaultsrc="<?php echo ( true === force_ssl_admin() ?  str_replace( 'http://', 'https://', home_url() ) :  home_url() ); ?>">
	</iframe>
	<div id="p3-scan-caption">
		<?php _e( 'The scanner will analyze the speed and resource usage of all active plugins on your website. It may take several minutes, and this window must remain open for the scan to finish successfully.', 'p3-profiler' ); ?>
	</div>
	<div id="p3-manual-scan-caption" style="display: none;">
		<table>
			<tr>
				<td>
					<?php _e( 'Click the links and pages of your site, and the scanner will analyze the speed and resource usage of all of your active plugins.', 'p3-profiler' ); ?>
				</td>
				<td width="220">
					<a href="javascript:;" id="p3-manual-scan-cancel"><?php _e( 'Cancel', 'p3-profiler' ); ?></a>
					&nbsp;&nbsp;&nbsp;
					<span class="p3-big-button">
						<input type="checkbox" id="p3-manual-scan-done-submit" checked="checked" />
						<label for="p3-manual-scan-done-submit"><?php _e( "I'm Done", 'p3-profiler' ); ?></label>
					</span>
				</td>
			</tr>
		</table>
	</div>
</div>

<!-- Dialog for choose manual or auto scan  -->
<div id="p3-scan-name-dialog" class="p3-dialog">
	<div style="padding-top: 10px;" class="scan-title"><?php _e( 'Scan name:', 'p3-profiler' ); ?>
		<input type="text" name="p3_scan_name" id="p3-scan-name" title="<?php esc_attr_e( 'Enter scan name here', 'p3-profiler' ); ?>"
			value="scan_<?php echo date( 'Y-m-d' ); ?>_<?php echo substr( md5( uniqid() ), -8 );?>" size="35" maxlength="100" />
	</div>
	<div style="padding-top: 10px;"><em class="p3-em scan-description"><?php _e( 'Enter the name of a previous scan to continue scanning', 'p3-profiler' ); ?></em></div>
	<br />
	<div class="p3-big-button">
		<input type="checkbox" id="p3-auto-scan-submit" checked="checked" /><label for="p3-auto-scan-submit" class="auto-scan"><?php _e( 'Auto Scan' , 'p3-profiler' ); ?></label>
		<input type="checkbox" id="p3-manual-scan-submit" checked="checked" /><label for="p3-manual-scan-submit" class="manual-scan"><?php _e( 'Manual Scan', 'p3-profiler' ); ?></label>
	</div>
</div>

<!-- Dialog for progress bar -->
<div id="p3-progress-dialog" class="p3-dialog">
	<div id="p3-scanning-caption">
		<?php _e( 'Scanning ...', 'p3-profiler' ); ?>
	</div>
	<div id="p3-progress"></div>
	
	<!-- Cancel button -->
	<div class="p3-big-button" id="p3-cancel-scan-buttonset">
		<input type="checkbox" id="p3-cancel-scan-submit" checked="checked" /><label for="p3-cancel-scan-submit" class="stop-scan"><?php _e( 'Stop Scan', 'p3-profiler' ); ?></label>
	</div>

	<!-- View / resume buttons -->
	<div class="p3-big-button" id="p3-resume-scan-buttonset" style="display: none;">
		<input type="checkbox" id="p3-resume-scan-submit" checked="checked" /><label for="p3-resume-scan-submit" class="resume-scan"><?php _e( 'Resume', 'p3-profiler' ); ?></label>
		<input type="checkbox" id="p3-view-incomplete-results-submit" checked="checked" data-scan-name="" />
		<label for="p3-view-incomplete-results-submit" class="incomplete-results"><?php _e( 'View Results', 'p3-profiler' ); ?></label>
	</div>
	
	<!-- View results button -->
	<div class="p3-big-button" id="p3-view-results-buttonset" style="display: none;">
		<input type="checkbox" id="p3-view-results-submit" checked="checked" data-scan-name="" />
		<label for="p3-view-results-submit" class="view-results"><?php _e( 'View Results', 'p3-profiler' ); ?></label>
	</div>	
</div>