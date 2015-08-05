<?php

$button_current_checked = '';
$button_history_checked = '';
$button_help_checked    = '';
if ( 'current-scan' == self::$action || !empty( $_REQUEST['current_scan'] ) ) {
	$button_current_checked = 'checked="checked"';
} elseif ( 'help' == self::$action ) {
	$button_help_checked = 'checked="checked"';
} else {
	$button_history_checked = 'checked="checked"';
}

?>
<script type="text/javascript">
	jQuery( document ).ready( function( $) {
		$( "#button-current-scan" ).click( function() {
			location.href = "<?php echo esc_url_raw( add_query_arg( array( 'p3_action' => 'current-scan', 'name' => null, 'current_scan' => null ) ) ); ?>";
		});
		$( "#button-history-scans" ).click( function() {
			location.href = "<?php echo esc_url_raw( add_query_arg( array( 'p3_action' => 'list-scans', 'name' => null, 'current_scan' => null ) ) ); ?>";
		});
		$( "#button-help" ).click( function() {
			location.href = "<?php echo esc_url_raw( add_query_arg( array( 'p3_action' => 'help', 'name' => null, 'current_scan' => null ) ) ); ?>";
		})
		$( ".p3-button" ).button();
		$( "#p3-navbar" ).buttonset();
		$( "#p3-navbar" ).corner( "round 8px" );
		$( ".p3-big-button" ).buttonset();
		$( "#p3-results-table tr:even" ).addClass( "even" );
		$( "td div.row-actions-visible" ).hide();
		$( "table.wp-list-table td" ).mouseover( function() {
			$( "div.row-actions-visible", $( this ) ).show();
		}).mouseout( function() {
			$( "div.row-actions-visible", $( this ) ).hide();
		});
		$( ".qtip-tip" ).each( function() {
			$( this ).qtip({
				content: $( this ).attr( "title" ),
				position: {
					my: 'top center',
					at: 'bottom center'
				},
				style: {
					classes: 'ui-tooltip-blue ui-tooltip-shadow'
				}
			});
		});

		// Callouts
		$( "div#p3-reminder-wrapper" )
			.corner( "round 8px" )
			.parent()
			.css( "padding", "4px" )
			.corner( "round 10px" );
	});
</script>
<div class="wrap">

	<!-- Header icon / title -->
	<div id="icon-plugins" class="icon32"><br/></div>
	<h2 class="plugin-name"><?php _e( 'P3 - Plugin Performance Profiler', 'p3-profiler' ); ?></h2>

	<!-- Header navbar -->
	<div class="ui-widget-header" id="p3-navbar">
		<div>
			<input type="radio" name="p3-nav" id="button-current-scan" <?php echo $button_current_checked; ?> />
			<label for="button-current-scan" class="current-tab"><?php _e( 'Current', 'p3-profiler' ); ?></label>
			<input type="radio" name="p3-nav" id="button-history-scans" <?php echo $button_history_checked; ?> />
			<label for="button-history-scans" class="history-tab"><?php _e( 'History', 'p3-profiler' ); ?></label>
			<input type="radio" name="p3-nav" id="button-help" <?php echo $button_help_checked; ?> /><label for="button-help" class="help-tab"><?php _e( 'Help', 'p3-profiler' ); ?></label>
		</div>

		<div id="p3-scan-label">
			<?php if ( !empty( self::$profile ) ) : ?>
				<?php _e( 'Scan name:', 'p3-profiler' ); ?> <?php echo self::$profile->profile_name; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Start / stop button and callouts -->
	<?php require_once P3_PATH . '/templates/callouts.php'; ?>

	<!-- View scan or show a list of scans -->
	<?php if ( ( 'current-scan' == self::$action && !empty( self::$scan ) ) || 'view-scan' == self::$action ) { ?>
		<?php require_once P3_PATH . '/templates/view-scan.php'; ?>
	<?php } elseif ( 'help' == self::$action ) { ?>
		<?php require_once P3_PATH . '/templates/help.php'; ?>
	<?php } else { ?>
		<?php require_once P3_PATH . '/templates/list-scans.php'; ?>
	<?php } ?>

</div>

<div id="p3-footer-wrapper">
	<div id="p3-reminder">
		<div id="p3-reminder-wrapper">
			<?php _e( 'Do you like this plugin?', 'p3-profiler' ); ?>
			<ul>
				<li><a href="http://twitter.com/home?status=<?php echo rawurlencode( sprintf( __( 'I just optimized my WordPress site with %1$s %2$s', 'p3-profiler' ), '#p3plugin', 'http://wordpress.org/extend/plugins/p3-profiler/') ); ?>" target="_blank"><?php _e( 'Tweet about it', 'p3-profiler' ); ?></a></li>
				<li><a href="http://wordpress.org/extend/plugins/p3-profiler/" target="_blank"><?php _e( 'Rate it on the repository', 'p3-profiler' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div id="p3-copyright">
		<img src="<?php echo plugins_url() . '/p3-profiler/css/logo.png'; ?>" alt="<?php esc_attr_e( 'Logo', 'p3-profiler' ); ?>" title="<?php esc_attr_e( 'Logo', 'p3-profiler' ); ?>" />
		<br />
		<?php printf( __( 'P3 (Plugin Performance Profiler) is Copyright &copy; %1$s - %2$s <a href="%3$s" target="_blank">GoDaddy.com</a>.  All rights reserved.', 'p3-profiler' ), 2011, date( 'Y' ), 'http://www.godaddy.com/' ); ?>
	</div>
</div>

