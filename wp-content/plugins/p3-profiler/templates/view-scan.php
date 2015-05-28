<?php
if ( !defined('P3_PATH') )
	die( 'Forbidden ');
$url_stats = array();
$domain    = '';
if ( !empty( self::$profile ) ) {
	$url_stats = self::$profile->get_stats_by_url();
	$domain    = @parse_url( self::$profile->report_url, PHP_URL_HOST );
}
$pie_chart_id                 = 'pie_'       . substr( md5( uniqid() ), -8 );
$runtime_chart_id             = 'runtime_'   . substr( md5( uniqid() ), -8 );
$query_chart_id               = 'query_'     . substr( md5( uniqid() ), -8 );
$component_breakdown_chart_id = 'breakdown_' . substr( md5( uniqid() ), -8 );
$component_runtime_chart_id   = 'runtime2_'  . substr( md5( uniqid() ), -8 );
?>
<script type="text/javascript">

	/**************************************************************/
	/**  Init                                                    **/
	/**************************************************************/

	// Raw json data ( used in the charts for tooltip data
	var _data = [];
	<?php if ( !empty( self::$scan ) && file_exists( self::$scan ) ) { ?>
		<?php foreach ( file( self::$scan, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ) { ?>
			_data.push(<?php echo $line; ?>);
		<?php } ?>
	<?php } ?>

	// Set up the tabs
	jQuery( document ).ready( function( $) {
		$( "#results-table tr:even" ).addClass( "even" );
		$( "#p3-email-sending-dialog" ).dialog({
			'autoOpen' : false,
			'closeOnEscape' : false,
			'draggable' : false,
			'resizable' : false,
			'modal' : true,
			'width' : 325,
			'height' : 120,
			'dialogClass' : 'noTitle'
		});
		$( "#p3-detailed-series-toggle" ).dialog({
			'autoOpen' : false,
			'closeOnEscape' : true,
			'draggable' : false,
			'resizable' : false,
			'modal' : true,
			'width' : 400,
			'height' : 'auto',
			'title' : "<?php _e( 'Toggle Series', 'p3-profiler' ); ?>",
			'buttons' :
			[
				{
					text: '<?php _e( 'OK', 'p3-profiler' ); ?>',
					'class' : 'button-secondary',
					click: function() {
						$(this).dialog( "close" );
					}
				}
			]
		});
		$( "#p3-email-results-dialog" ).dialog({
			'autoOpen' : false,
			'closeOnEscape' : true,
			'draggable' : false,
			'resizable' : false,
			'modal' : true,
			'width' : 500,
			'height' : 560,
			'title' : "<?php _e( 'Email Report', 'p3-profiler' ); ?>",
			'buttons' :
			[
				{
					text: '<?php _e( 'Send', 'p3-profiler' ); ?>',
					'class' : 'button-secondary email-send',
					click: function() {
						data = {
							'p3_to'      : jQuery( '#p3-email-results-to' ).val(),
							'p3_from'    : jQuery( '#p3-email-results-from' ).val(),
							'p3_subject' : jQuery( '#p3-email-results-subject' ).val(),
							'p3_results' : jQuery( "#p3-email-results-results" ).val(),
							'p3_message' : jQuery( "#p3-email-results-message" ).val(),
							'action'      : 'p3_send_results',
							'p3_nonce'   : '<?php echo wp_create_nonce( 'p3_ajax_send_results' ); ?>'
						}
						
						// Open the "loading" dialog
						$( "#p3-email-sending-success" ).hide();
						$( "#p3-email-sending-error" ).hide();
						$( "#p3-email-sending-loading" ).show();
						$( "#p3-email-sending-close" ).hide();
						$( "#p3-email-sending-dialog" ).dialog( "open" );

						// Send the data
						jQuery.post( ajaxurl, data, function( response ) {
							response = response.trim();
							if ( "1" == response.substring( 0, 1 ) ) {
								$( "#p3-email-success-recipient" ).html( jQuery( '#p3-email-results-to' ).val() );
								$( "#p3-email-sending-success" ).show();
								$( "#p3-email-sending-error" ).hide();
								$( "#p3-email-sending-loading" ).hide();
								$( "#p3-email-sending-close" ).show();
							} else {
								if ( "-1" == response.substring( 0, 2 ) ) {
									$( "#p3-email-error" ).html( "nonce error" );
								} else if ( "0" == response.charAt( 0 ) ) {
									$( "#p3-email-error" ).html( response.substr( 2 ) );
								} else {
									$( "#p3-email-error" ).html( "unknown error" );
								}
								$( "#p3-email-sending-success" ).hide();
								$( "#p3-email-sending-error" ).show();
								$( "#p3-email-sending-loading" ).hide();
								$( "#p3-email-sending-close" ).show();
							}
						});
					}
				},
				{
					text: '<?php _e( 'Cancel', 'p3-profiler' ) ?>',
					'class': 'p3-cancel-button',
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});
		$( "#p3-email-sending-close-submit" ).click( function() {
			$( this ).prop( "checked", true );
			$( this ).button( "refresh" );
			$( "#p3-email-sending-dialog" ).dialog( "close" );
			$( "#p3-email-results-dialog" ).dialog( "close" );
		});
		$( "#p3-email-results" ).click( function() {
			$( "#p3-email-results-dialog" ).dialog( "open" );
		});
		$( "#p3-email-sending-close" ).buttonset();
	});



	/**************************************************************/
	/**  Hover function for charts                               **/
	/**************************************************************/
	var previousPoint = null;
	function showTooltip( x, y, contents ) {
		jQuery( '<div id="p3-tooltip">' + contents + '</div>' ).css(
			{
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#fee',
				opacity: 0.80
			}
		).appendTo( "body" ).fadeIn( 200 );
	}



	/**************************************************************/
	/**  Plugin pie chart                                        **/
	/**************************************************************/
	var data_<?php echo $pie_chart_id; ?> = [
		<?php if ( !empty( self::$profile ) ){ ?>
			<?php foreach ( self::$profile->plugin_times as $k => $v ) { ?>
				{
					label: "<?php echo esc_js( $k ); ?>",
					data: <?php echo $v; ?>
				},
			<?php } ?>
		<?php } else { ?>
			{ label: '<?php _e( 'No plugins', 'p3-profiler' ); ?>', data: 1}
		<?php } ?>
	];
	jQuery( document ).ready( function( $) {
		$.plot( $(
			"#p3-holder_<?php echo $pie_chart_id; ?>" ),
			data_<?php echo $pie_chart_id; ?>,
		{
				series: {
					pie: { 
						show: true,
						combine: {
							threshold: .03 // 3% or less
						}
					}
				},
				grid: {
					hoverable: true,
					clickable: true
				},
				legend: {
					container: $( "#p3-legend_<?php echo $pie_chart_id; ?>" )
				}
		});

		$( "#p3-holder_<?php echo $pie_chart_id; ?>" ).bind( "plothover", function ( event, pos, item ) {
			if ( item ) {
				$( "#p3-tooltip" ).remove();
				showTooltip( pos.pageX, pos.pageY,
					item.series.label + "<br />" + Math.round( item.series.percent ) + "%<br />" +
					Math.round( item.datapoint[1][0][1] * Math.pow( 10, 4 ) ) / Math.pow( 10, 4 ) + " <?php _e( 'seconds', 'p3-profiler' ); ?>"
				);
			} else {
				$( "#p3-tooltip" ).remove();
			}
		});
	});



	/**************************************************************/
	/**  Runtime line chart data                                 **/
	/**************************************************************/
	var chart_<?php echo $runtime_chart_id; ?> = null;
	var data_<?php echo $runtime_chart_id; ?> = [
		{
			label: "<?php _e( 'WP Core time', 'p3-profiler' ); ?>",
			data: [
			<?php foreach ( array_values( $url_stats ) as $k => $v ) { ?>
				[
					<?php echo $k + 1; ?>,
					<?php echo $v['core']; ?>
				],
			<?php } ?>
			]
		},
		{
			label: "<?php _e( 'Theme time', 'p3-profiler' ); ?>",
			data: [
			<?php foreach ( array_values( $url_stats ) as $k => $v ) { ?>
				[
					<?php echo $k + 1; ?>,
					<?php echo $v['theme']; ?>
				],
			<?php } ?>
			]
		},
		{
			label: "<?php _e( 'Plugin time', 'p3-profiler' ); ?>",
			data: [
			<?php foreach ( array_values( $url_stats ) as $k => $v ) { ?>
				[
					<?php echo $k + 1; ?>,
					<?php echo $v['plugins']; ?>
				],
			<?php } ?>
			]
		}
	];
	jQuery( document ).ready( function( $) {
		chart_<?php echo $runtime_chart_id; ?> = $.plot( $(
			"#p3-holder_<?php echo $runtime_chart_id; ?>" ),
			data_<?php echo $runtime_chart_id; ?>,
		{
				series: {
					lines: { show: true },
					points: { show: true },
				},
				grid: {
					hoverable: true,
					clickable: true
				},
				legend : {
					container: $( "#p3-legend_<?php echo $runtime_chart_id; ?>" )
				},
				zoom: {
					interactive: true
				},
				pan: {
					interactive: true
				},
				xaxis: {
					show: false
				}
		});

		// zoom buttons
		$( '<div class="button" style="float: left; position: relative; left: 490px; top: -290px;">-</div>' )
			.appendTo( $( "#p3-holder_<?php echo $runtime_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $runtime_chart_id; ?>.zoomOut();
		});
		$( '<div class="button" style="float: left; position: relative; left: 490px; top: -290px;">+</div>' )
			.appendTo( $( "#p3-holder_<?php echo $runtime_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $runtime_chart_id; ?>.zoom();
		});

		$( "#p3-holder_<?php echo $runtime_chart_id; ?>" ).bind( "plothover", function ( event, pos, item ) {
			if ( item ) {
				if ( previousPoint != item.dataIndex ) {
					previousPoint = item.dataIndex;

					$( "#p3-tooltip" ).remove();
					var x = item.datapoint[0].toFixed( 2 ),
						y = item.datapoint[1].toFixed( 2 );

					url = _data[item["dataIndex"]]["url"];

					// Get rid of the domain
					url = url.replace(/http[s]?:\/\/<?php echo $domain; ?>(:\d+)?/, "" );

					showTooltip( item.pageX, item.pageY,
								item.series.label + "<br />" +
								url + "<br />" +
								y + " <?php _e( 'seconds', 'p3-profiler' ); ?>" );
				}
			} else {
				$( "#p3-tooltip" ).remove();
				previousPoint = null;            
			}
		});
	});
	


	/**************************************************************/
	/**  Query line chart data                                   **/
	/**************************************************************/
	var chart_<?php echo $query_chart_id; ?> = null;
	var data_<?php echo $query_chart_id; ?> = [
		{
			label: "<?php _e( '# of Queries', 'p3-profiler' ); ?>",
			data: [
			<?php if ( !empty( self::$profile ) ){ ?>
				<?php foreach ( array_values( $url_stats ) as $k => $v ) { ?>
					[
						<?php echo $k + 1; ?>,
						<?php echo $v['queries']; ?>
					],
				<?php } ?>
			<?php } ?>
			]
		}
	];
	jQuery( document ).ready( function( $) {
		chart_<?php echo $query_chart_id; ?> = $.plot( $(
			"#p3-holder_<?php echo $query_chart_id; ?>" ),
			data_<?php echo $query_chart_id; ?>,
		{
				series: {
					lines: { show: true },
					points: { show: true }
				},
				grid: {
					hoverable: true,
					clickable: true
				},
				legend : {
					container: $( "#p3-legend_<?php echo $query_chart_id; ?>" )
				},
				zoom: {
					interactive: true
				},
				pan: {
					interactive: true
				},
				xaxis: {
					show: false
				}
		});

		// zoom buttons
		$( '<div class="button" style="float: left; position: relative; left: 490px; top: -290px;">-</div>' )
			.appendTo( $( "#p3-holder_<?php echo $query_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $query_chart_id; ?>.zoomOut();
		});
		$( '<div class="button" style="float: left; position: relative; left: 490px; top: -290px;">+</div>' )
			.appendTo( $( "#p3-holder_<?php echo $query_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $query_chart_id; ?>.zoom();
		});

		$( "#p3-holder_<?php echo $query_chart_id; ?>" ).bind( "plothover", function ( event, pos, item ) {
			if ( item ) {
				if ( previousPoint != item.dataIndex ) {
					previousPoint = item.dataIndex;

					$( "#p3-tooltip" ).remove();
					var x = item.datapoint[0].toFixed( 2 ),
						y = item.datapoint[1]; //.toFixed( 2 );

					url = _data[item["dataIndex"]]["url"];

					// Get rid of the domain
					url = url.replace(/http[s]?:\/\/<?php echo $domain; ?>(:\d+)?/, "" );

					qword = ( y == 1 ) ? "<?php _e( 'query', 'p3-profiler' ); ?>" : "<?php _e( 'queries', 'p3-profiler' ); ?>";
					showTooltip( item.pageX, item.pageY,
								item.series.label + "<br />" +
								url + "<br />" +
								y + " " + qword );
				}
			} else {
				$( "#p3-tooltip" ).remove();
				previousPoint = null;            
			}
		});
	});


	/**************************************************************/
	/**  Compnent bar chart data                                 **/
	/**************************************************************/
	var chart_<?php echo $component_breakdown_chart_id; ?> = null;
	var data_<?php echo $component_breakdown_chart_id; ?> = [
		{
			label: '<?php _e( 'Site Load Time', 'p3-profiler' ); ?>',
			bars: {show: false},
			points: {show: false},
			lines: {show: true, lineWidth: 3},
			shadowSize: 0,
			data: [
				<?php for ( $i = -999 ; $i < 999 + 2; $i++ ) { ?>
					[
						<?php echo $i; ?>,
						<?php echo self::$profile->averages['site']; ?>
					],
				<?php } ?>
			]
		},
		{
			label: '<?php _e( 'WP Core Time', 'p3-profiler' ); ?>',
			data: [[0, <?php echo self::$profile->averages['core']; ?>]]
		},
		{
			label: '<?php _e( 'Theme', 'p3-profiler' ); ?>',
			data: [[1, <?php echo self::$profile->averages['theme']; ?>]]
		},
		<?php $i = 2; $other = 0; ?>
		<?php foreach ( self::$profile->plugin_times as $k => $v ) { ?>
			{
				label: '<?php echo esc_js( $k ); ?>',
				data: [[
					<?php echo $i++; ?>,
					<?php echo $v; ?>
				]],
			},
		<?php } ?>
	];

	jQuery( document ).ready( function( $) {
		chart_<?php echo $component_breakdown_chart_id; ?> = $.plot( $(
			"#p3-holder_<?php echo $component_breakdown_chart_id; ?>" ),
			data_<?php echo $component_breakdown_chart_id; ?>,
		{
				series: {
					bars: {
						show: true,
						barWidth: 0.9,
						align: 'center'
					},
					stack: false,
					lines: {
						show: false,
						steps: false,
					}
				},
				grid: {
					hoverable: true,
					clickable: true,
				},
				xaxis: {
					show: false,
					ticks: [
						[0, '<?php _e( 'Site Load Time', 'p3-profiler' ); ?>'],
						[1, '<?php _e( 'WP Core Time', 'p3-profiler' ); ?>'],
						[2, '<?php _e( 'Theme', 'p3-profiler' ); ?>'],
						<?php $i = 3; ?>
						<?php foreach ( self::$profile->plugin_times as $k => $v ) { ?>
							[
								<?php echo $i++ ?>,
								'<?php echo esc_js( $k ); ?>'
							],
						<?php } ?>
					],
					min: 0,
					max: <?php echo $i; ?>,
				},
				legend : {
					container: $( "#p3-legend_<?php echo $component_breakdown_chart_id; ?>" )
				},
				zoom: {
					interactive: true
				},
				pan: {
					interactive: true
				}
		});

		$( "#p3-holder_<?php echo $component_breakdown_chart_id; ?>" ).bind( "plothover", function ( event, pos, item ) {
			if ( item ) {
				$( "#p3-tooltip" ).remove();
				showTooltip( pos.pageX, pos.pageY,
					item.series.label + "<br />" + Math.round( item.datapoint[1] * Math.pow( 10, 4 ) ) / Math.pow( 10, 4 ) + " <?php _e('seconds', 'p3-profiler' ); ?>"
				);
			} else {
				$( "#p3-tooltip" ).remove();
			}
		});

		// zoom buttons
		$( '<div class="button" style="float: left; position: relative; left: 490px; top: -290px;">-</div>' )
			.appendTo( $( "#p3-holder_<?php echo $component_breakdown_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $component_breakdown_chart_id; ?>.zoomOut();
		});
		$( '<div class="button" style="float: left; position: relative; left: 490px; top: -290px;">+</div>' )
			.appendTo( $( "#p3-holder_<?php echo $component_breakdown_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $component_breakdown_chart_id; ?>.zoom();
		});
	});

	/**************************************************************/
	/**  Runtime by component line chart data                    **/
	/**************************************************************/
	var chart_<?php echo $component_runtime_chart_id; ?> = null;
	var data_<?php echo $component_runtime_chart_id; ?> = [
		{
			label: "<?php _e( 'WP Core Time', 'p3-profiler' ); ?>",
			data: [
			<?php if ( !empty( self::$profile ) ){ ?>
				<?php foreach ( array_values( $url_stats ) as $k => $v ) { ?>
					[
						<?php echo $k + 1; ?>,
						<?php echo $v['core']; ?>
					],
				<?php } ?>
			<?php } ?>
			]
		},
		{
			label: "<?php _e( 'Theme', 'p3-profiler' ); ?>",
			data: [
			<?php if ( !empty( self::$profile ) ){ ?>
				<?php foreach ( array_values( $url_stats ) as $k => $v ) { ?>
					[
						<?php echo $k + 1; ?>,
						<?php echo $v['theme']; ?>
					],
				<?php } ?>
			<?php } ?>
			]
		},
		<?php if ( !empty( self::$profile ) && !empty( self::$profile->detected_plugins ) ) { ?>
			<?php foreach ( self::$profile->detected_plugins as $plugin ) { ?>
				{
					label: "<?php echo esc_js( $plugin ); ?>",
					data: [
					<?php foreach ( array_values( $url_stats ) as $k => $v ) { ?>
						[
							<?php echo $k + 1; ?>,
							<?php if ( array_key_exists( $plugin, $v['breakdown'] ) ) : ?>
								<?php echo $v['breakdown'][$plugin]; ?>
							<?php else : ?>
								0
							<?php endif; ?>
						],
					<?php } ?>
					]
				},
			<?php } ?>
		<?php } ?>
	];
	
	var detailed_timeline_options = {};

	jQuery( document ).ready( function ( $ ) {
		<?php if ( !empty( self::$profile ) && !empty( self::$profile->detected_plugins ) ) { ?>
			jQuery( "#p3-detailed-series-toggle" ).append( '<div><label><input type="checkbox" checked="checked" class="p3-detailed-series-toggle" data-key="<?php esc_attr_e( 'WP Core Time', 'p3-profiler' ); ?>" /><?php _e( 'WP Core Time', 'p3-profiler' ); ?></label></div>' );
			jQuery( "#p3-detailed-series-toggle" ).append( '<div><label><input type="checkbox" checked="checked" class="p3-detailed-series-toggle" data-key="<?php esc_attr_e( 'Theme', 'p3-profiler' ); ?>" /><?php _e( 'Theme', 'p3-profiler' ); ?></label></div>' );
			<?php foreach ( self::$profile->detected_plugins as $plugin ) { ?>
				jQuery( "#p3-detailed-series-toggle" ).append( '<div><label><input type="checkbox" checked="checked" class="p3-detailed-series-toggle" data-key="<?php echo esc_html( $plugin ); ?>" /><?php echo esc_html( $plugin ); ?></label></div>' );
			<?php } ?>
		<?php } ?>
		jQuery( "input.p3-detailed-series-toggle" ).click( function() {
			data = [];
			keys = [];
			jQuery( "input.p3-detailed-series-toggle:checked" ).each(function() {
				keys.push( $( this ).attr( "data-key" ) );
			});
			for ( i = 0 ; i < keys.length ; i++ ) {
				tmp = [];
				for ( j = 0 ; j < data_<?php echo $component_runtime_chart_id; ?>.length ; j++ ) {
					if ( keys[i] == data_<?php echo $component_runtime_chart_id; ?>[j]['label'] ) {
						for ( k = 0 ; k < data_<?php echo $component_runtime_chart_id; ?>[j]['data'].length ; k++ ) {
							tmp.push( data_<?php echo $component_runtime_chart_id; ?>[j]['data'][k] );
						}
					}
				}
				data.push( {
					data: tmp,
					label: keys[i]
				} );
			}
			if ( data.length == 0 ) {
				data = [
					{
						data: [],
						label: '<?php _e( 'No data', 'p3-profiler' ); ?>'
					}
				]
			}
			chart_<?php echo $component_runtime_chart_id; ?> = $.plot(
				$( "#p3-holder_<?php echo $component_runtime_chart_id; ?>" ),
				data,
				detailed_timeline_options
			);
		});
	});
	jQuery( document ).ready( function( $ ) {
		detailed_timeline_options = {
			series: {
				lines: { show: true },
				points: { show: true }
			},
			grid: {
				hoverable: true,
				clickable: true
			},
			legend : {
				container: jQuery( "#p3-legend_<?php echo $component_runtime_chart_id; ?>" )
			},
			zoom: {
				interactive: true
			},
			pan: {
				interactive: true
			},
			xaxis: {
				show: false
			}
		}
		chart_<?php echo $component_runtime_chart_id; ?> = $.plot(
			$( "#p3-holder_<?php echo $component_runtime_chart_id; ?>" ),
			data_<?php echo $component_runtime_chart_id; ?>,
			detailed_timeline_options
		);

		$( "#p3-holder_<?php echo $component_runtime_chart_id; ?>" ).bind( "plothover", function ( event, pos, item ) {
			if ( item ) {
				if ( previousPoint != item.dataIndex ) {
					previousPoint = item.dataIndex;

					$( "#p3-tooltip" ).remove();
					var x = item.datapoint[0].toFixed( 2 ),
						y = item.datapoint[1]; //.toFixed( 2 );

					url = _data[item["dataIndex"]]["url"];

					// Get rid of the domain
					url = url.replace(/http[s]?:\/\/<?php echo $domain; ?>(:\d+)?/, "" );

					showTooltip( item.pageX, item.pageY,
								item.series.label + "<br />" +
								url + "<br />" +
								y + " <?php _e( 'seconds', 'p3-profiler' ); ?>" );
				}
			} else {
				$( "#p3-tooltip" ).remove();
				previousPoint = null;            
			}
		});
		
		// zoom buttons
		$( '<div class="button" style="float: left; position: relative; left: 460px; top: -290px;">-</div>' )
			.appendTo( $( "#p3-holder_<?php echo $component_runtime_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $component_runtime_chart_id; ?>.zoomOut();
		});
		$( '<div class="button" style="float: left; position: relative; left: 460px; top: -290px;">+</div>' )
			.appendTo( $( "#p3-holder_<?php echo $component_runtime_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			chart_<?php echo $component_runtime_chart_id; ?>.zoom();
		});
		$( '<div class="button" style="float: left; position: relative; left: 460px; top: -290px; padding-left: 6px; padding-right: 6px;"><input type="checkbox" checked="checked" style="padding: 0; margin: 0; width: 15px;" /></div>' )
			.appendTo( $( "#p3-holder_<?php echo $component_runtime_chart_id; ?>" ).parent() ).click( function ( e ) {
			e.preventDefault();
			$( "#p3-detailed-series-toggle" ).dialog( "open" );
		});

	});
	
	jQuery( document ).ready( function( $ ) {
		$( "#p3-tabs" ).tabs();
	});

</script>
<div id="p3-tabs">
	<ul>
		<li><a href="#p3-tabs-1"><?php _e( 'Runtime By Plugin', 'p3-profiler' ); ?></a></li>
		<li><a href="#p3-tabs-5"><?php _e( 'Detailed Breakdown', 'p3-profiler' ); ?></a></li>
		<li><a href="#p3-tabs-2"><?php _e( 'Simple Timeline', 'p3-profiler' ); ?></a></li>
		<li><a href="#p3-tabs-6"><?php _e( 'Detailed Timeline', 'p3-profiler' ); ?></a></li>
		<li><a href="#p3-tabs-3"><?php _e( 'Query Timeline', 'p3-profiler' ); ?></a></li>
		<li><a href="#p3-tabs-4"><?php _e( 'Advanced Metrics', 'p3-profiler' ); ?></a></li>
	</ul>

	<!-- Plugin bar chart -->
	<div id="p3-tabs-5">
		<h2 class="detailed-title"><?php _e( 'Detailed Breakdown', 'p3-profiler' ); ?></h2>
		<div class="p3-plugin-graph">
			<table>
				<tr>
					<td rowspan="2">
						<div class="p3-y-axis-label">
							<em class="p3-em"><?php _e( 'Seconds', 'p3-profiler' ); ?></em>
						</div>
					</td>
					<td rowspan="2">
						<div class="p3-graph-holder detailed-graph" id="p3-holder_<?php echo $component_breakdown_chart_id; ?>"></div>
					</td>
					<td>
						<h3 class="breakdown-legend"><?php _ex( 'Legend', 'How to interpret the chart or graph', 'p3-profiler' ); ?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<div class="p3-custom-legend" id="p3-legend_<?php echo $component_breakdown_chart_id; ?>"></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">
						<div class="p3-x-axis-label" style="top: -10px;">
							<em class="p3-em"><?php _e( 'Component', 'p3-profiler' ); ?></em>
						</div>
					</td>
				</tr>
			</table>
		</div>		
	</div>
	
	<!-- Plugin pie chart div -->
	<div id="p3-tabs-1">
		<h2 class="runtime-title"><?php _e( 'Runtime by Plugin', 'p3-profiler' ); ?></h2>
		<div class="p3-plugin-graph" style="width: 570px;">
			<table>
				<tr>
					<td rowspan="2">
						<div style="width: 370px;" class="p3-graph-holder runtime-graph" id="p3-holder_<?php echo $pie_chart_id; ?>"></div>
					</td>
					<td>
						<h3 class="runtime-legend"><?php _ex( 'Legend', 'How to interpret the chart or graph', 'p3-profiler' ); ?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<div class="p3-custom-legend" id="p3-legend_<?php echo $pie_chart_id;?>"></div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Runtime line chart div -->
	<div id="p3-tabs-2">
		<h2 class="summary-title"><?php _e( 'Summary Timeline', 'p3-profiler' ); ?></h2>
		<div class="p3-plugin-graph">
			<table>
				<tr>
					<td rowspan="2">
						<div class="p3-y-axis-label">
							<em class="p3-em"><?php _e( 'Seconds', 'p3-profiler' ); ?></em>
						</div>
					</td>
					<td rowspan="2">
						<div class="p3-graph-holder summary-graph" id="p3-holder_<?php echo $runtime_chart_id; ?>"></div>
					</td>
					<td>
						<h3 class="summary-legend"><?php _ex( 'Legend', 'How to interpret the chart or graph', 'p3-profiler' ); ?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<div class="p3-custom-legend" id="p3-legend_<?php echo $runtime_chart_id; ?>"></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">
						<div class="p3-x-axis-label">
							<!-- <em class="p3-em">Visit</em> -->
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Query line chart div -->
	<div id="p3-tabs-3">
		<h2 class="query-title"><?php _e( 'Query Timeline', 'p3-profiler' ); ?></h2>
		<div class="p3-plugin-graph">
			<table>
				<tr>
					<td rowspan="2">
						<div class="p3-y-axis-label">
							<em class="p3-em"><?php _e( 'Queries', 'p3-profiler' ) ;?></em>
						</div>
					</td>
					<td rowspan="2">
						<div class="p3-graph-holder query-graph" id="p3-holder_<?php echo $query_chart_id; ?>"></div>
					</td>
					<td>
						<h3 class="query-legend"><?php _ex( 'Legend', 'How to interpret the chart or graph', 'p3-profiler' ); ?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<div class="p3-custom-legend" id="p3-legend_<?php echo $query_chart_id; ?>"></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">
						<div class="p3-x-axis-label">
							<!-- <em class="p3-em">Visit</em> -->
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- Component runtime chart div -->
	<div id="p3-tabs-6">
		<h2 class="detailed-timeline-title"><?php _e( 'Detailed Timeline', 'p3-profiler' ); ?></h2>
		<div class="p3-plugin-graph">
			<table>
				<tr>
					<td rowspan="2">
						<div class="p3-y-axis-label">
							<em class="p3-em"><?php _e( 'Seconds', 'p3-profiler' ); ?></em>
						</div>
					</td>
					<td rowspan="2">
						<div class="p3-graph-holder detailed-timeline-graph" id="p3-holder_<?php echo $component_runtime_chart_id; ?>"></div>
					</td>
					<td>
						<h3 class="detailed-timeline-legend"><?php _ex( 'Legend', 'How to interpret the chart or graph', 'p3-profiler' ); ?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<div class="p3-custom-legend" id="p3-legend_<?php echo $component_runtime_chart_id; ?>"></div>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2">
						<div class="p3-x-axis-label">
							<!-- <em class="p3-em">Visit</em> -->
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<!-- Advanced data -->
	<div id="p3-tabs-4">
		<div id="p3-metrics-container">
			<div class="ui-widget-header" id="p3-metrics-header" style="padding: 8px;">
				<strong><?php _e( 'Advanced Metrics', 'p3-profiler' ); ?></strong>
			</div>
			<div>
				<table class="p3-results-table" id="p3-results-table" cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr class="advanced">
							<td class="qtip-tip tlt-label" title="<?php esc_attr_e( "The time the site took to load. This is an observed measurement (start timing when the page was requested, stop timing when the page was delivered to the browser, calculate the difference). Lower is better.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Total Load Time:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="tlt-data">
								<?php printf( '%.4f', self::$profile->averages['total'] ); ?><?php _e( 'seconds', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr>
							<td class="qtip-tip slt-label" title="<?php esc_attr_e( "The calculated total load time minus the profile overhead. This is closer to your site's real-life load time. Lower is better.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Site Load Time:', 'p3-profiler' ); ?></small></em></strong>
							</td>
							<td class="slt-data">
								<?php printf( '%.4f', self::$profile->averages['site'] ); ?><?php _e( 'seconds', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr class="advanced">
							<td class="qtip-tip poh-label" title="<?php esc_attr_e( "The load time spent profiling code. Because the profiler slows down your load time, it is important to know how much impact the profiler has. However, it doesn't impact your site's	real-life load time.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Profile Overhead:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="poh-data">
								<?php printf( '%.4f', self::$profile->averages['profile'] ); ?><?php _e( 'seconds', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr>
							<td class="qtip-tip plt-label" title="<?php esc_attr_e( "The load time caused by plugins. Because of WordPress' construction, we can trace a function call  from a plugin through a theme through the core. The profiler prioritizes plugin calls first, theme calls second, and core calls last. Lower is better.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Plugin Load Time:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="plt-data">
								<?php printf( '%.4f', self::$profile->averages['plugins'] ); ?><?php _e( 'seconds', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr>
							<td class="qtip-tip theme-label" title="<?php esc_attr_e( "The load time spent applying the theme. Because of WordPress' construction, we can trace a function call from a plugin through a theme through the core. The profiler prioritizes plugin calls first, theme calls second, and core calls last. Lower is better.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Theme Load Time:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="theme-data">
								<?php printf( '%.4f', self::$profile->averages['theme'] ); ?><?php _e( 'seconds', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr>
							<td class="qtip-tip clt-label" title="<?php esc_attr_e( "The load time caused by the WordPress core. Because of WordPress' construction, we can trace a function call from a plugin through a theme through the core. The profiler prioritizes plugin calls first, theme calls second, and core calls last. This will probably be constant.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Core Load Time:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="clt-data">
								<?php printf( '%.4f', self::$profile->averages['core'] ); ?><?php _e( 'seconds', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr class="advanced">
							<td class="qtip-tip moe-label" title="<?php esc_attr_e( "This is the difference between the observed runtime (what actually happened) and expected runtime (adding the plugin runtime, theme runtime, core runtime, and profiler overhead). There are several reasons this margin of error can exist. Most likely, the profiler is missing microseconds while adding the runtime it observed. Using a network clock to set the time (NTP) can also cause minute timing changes. Ideally, this number should be zero, but there's nothing you can do to change it. It will give you an idea of how accurate the other results are.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Margin of Error:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="moe-data">
								<?php printf( '%.4f', self::$profile->averages['drift'] ); ?><?php _e( 'seconds', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
								<br />
								<em class="p3-em">
									(<span class="qtip-tip moe-observed" title="<?php esc_attr_e( "How long the site took to load. This is an observed measurement (start timing when the page was requested, stop timing when the page was delivered to the browser, calculate the difference).", 'p3-profiler' ); ?>"><?php printf( '%.4f', self::$profile->averages['observed'] ); ?> <?php _e( 'observed', 'p3-profiler' ); ?></span>,
									<span class="qtip-tip moe-expected" title="<?php esc_attr_e( "The expected site load time calculated by adding plugin load time, core load time, theme load time, and profiler overhead.", 'p3-profiler' ); ?>"><?php printf( '%.4f', self::$profile->averages['expected'] ); ?> <?php _e( 'expected', 'p3-profiler' ); ?></span>)
								</em>
							</td>
						</tr>
						<tr class="advanced">
							<td class="qtip-tip visits-label" title="<?php esc_attr_e( "The number of visits registered during a profiling session.  More visits produce a more accurate summary.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Visits:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="visits-data">
								<?php echo number_format( self::$profile->visits ); ?>
							</td>
						</tr>
						<tr class="advanced">
							<td class="qtip-tip num-ticks-label" title="<?php esc_attr_e( "The number of PHP ticks recorded during the profiling session.  A tick is loosely correlated to a PHP statement or function call.  Fewer is better.", 'p3-profiler' ); ?>">
								<strong><?php _e ( 'Number of PHP ticks:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="num-ticks-data">
								<?php echo number_format( self::$profile->averages['plugin_calls'] ); ?> <?php _e( 'calls', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr>
							<td class="qtip-tip mu-label" title="<?php esc_attr_e( "The amount of RAM usage observed.  This is reported by memory_get_peak_usage(). Lower is better.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'Memory Usage:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="mu-data">
								<?php echo number_format( self::$profile->averages['memory'] / 1024 / 1024, 2 ); ?> <?php _ex( 'MB', 'Abbreviation for megabytes', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
						<tr>
							<td class="qtip-tip mq-label" title="<?php esc_attr_e( "The count of queries sent to the database.  This is reported by the WordPress function get_num_queries(). Lower is better.", 'p3-profiler' ); ?>">
								<strong><?php _e( 'MySQL Queries:', 'p3-profiler' ); ?></strong>
							</td>
							<td class="mq-data">
								<?php echo round( self::$profile->averages['queries'] ); ?> <?php _e( 'queries', 'p3-profiler' ); ?> <em class="p3-em"><?php _ex( 'avg.', "Abbreviation for 'average'", 'p3-profiler' ); ?></em>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Email these results -->
	<div id="p3-email-results-container">
		<div class="button" id="p3-email-results">
			<div>
			<img src="<?php echo plugins_url(); ?>/p3-profiler/css/icon_mail.gif" height="22" width="22" align="center"
				alt="<?php esc_attr_e( 'Email these results', 'p3-profiler' ); ?>" title="<?php esc_attr_e( 'Email these results', 'p3-profiler' ); ?>" />
			<a href="javascript:;"><?php _e ( 'Email these results', 'p3-profiler' ); ?></a>
			</div>
		</div>
	</div>
	
	<!-- Email results dialog -->
	<div id="p3-email-results-dialog" class="p3-dialog">
		<div>
			<span id="p3-email-from-label"><?php _e( 'From:', 'p3-profiler' ); ?></span><br />
			<input type="text" id="p3-email-results-from" style="width:95%;" size="35"
				value="<?php $user = wp_get_current_user(); echo $user->user_email; ?>" title="<?php esc_attr_e( 'Enter the e-mail address to send from', 'p3-profiler' ); ?>" />
		</div>
		<br />
		<div>
			<span id="p3-email-recipient-label"><?php _e( 'Recipient:', 'p3-profiler' ); ?></span><br />
			<input type="text" id="p3-email-results-to" style="width:95%;" size="35"
				value="<?php $user = wp_get_current_user(); echo $user->user_email; ?>"
				title="<?php esc_attr_e( 'Enter the e-mail address where you would like to send these results', 'p3-profiler' ); ?>" />
		</div>
		<br />
		<div>
			<span id="p3-email-subject-label"><?php _e( 'Subject:', 'p3-profiler' ); ?></span><br />
			<input type="text" id="p3-email-results-subject" style="width:95%;" size="35"
				value="<?php echo esc_attr( sprintf( __( 'Performance Profile Results for %s', 'p3-profiler' ), get_bloginfo( 'name' ) ) ); ?>" title="<?php esc_attr_e( 'Enter the e-mail subject', 'p3-profiler' ); ?>" />
		</div>
		<br />
		<div>
			<span id="p3-email-message-label"><?php _e( 'Message:', 'p3-profiler' ); ?> <em class="p3-em"><?php _e( '(optional)', 'p3-profiler' ); ?></em><br /></span>
			<textarea id="p3-email-results-message" style="width: 95%; height: 100px;"><?php esc_html_e("Hello,

I profiled my WordPress site's performance using the Profile Plugin and I wanted
to share the results with you.  Please take a look at the information below:", 'p3-profiler' ); ?></textarea>
		</div>
		<br />
		<div>
			<span id="p3-email-results-label"><?php _e( 'Results:', 'p3-profiler' ); ?> <em class="p3-em"><?php _e( '(system generated, do not edit)', 'p3-profiler' ); ?></em></span><br />
			<textarea disabled="disabled" id="p3-email-results-results" style="width: 95%; height: 120px;"><?php 
			$plugin_list = '';
			foreach ( self::$profile->plugin_times as $k => $v) {
				$plugin_list .= $k . ' - ' . sprintf('%.4f sec', $v) . ' - ' . sprintf( '%.2f%%', $v * 100 / array_sum( self::$profile->plugin_times ) ) . "\n";
			}
printf( __( "WordPress Plugin Profile Report
===========================================
Report date: %1\$s
Theme name: %2\$s
Pages browsed: %3\$s
Avg. load time: %4\$s sec
Number of plugins: %5\$s
Plugin impact: %6\$s of load time
Avg. plugin time: %7\$s sec
Avg. core time: %8\$s sec
Avg. theme time: %9\$s sec
Avg. mem usage: %10\$s MB
Avg. ticks: %11\$s
Avg. db queries : %12\$s
Margin of error : %13\$s sec

Plugin list:
===========================================
%14\$s
", 'p3-profiler' ),
date_i18n( get_option( 'date_format' ), self::$profile->report_date ),
self::$profile->theme_name,
self::$profile->visits,
sprintf( '%.4f', self::$profile->averages['site'] ),
count( self::$profile->detected_plugins ),
sprintf( '%.2f%%', self::$profile->averages['plugin_impact'] ),
sprintf( '%.4f', self::$profile->averages['plugins'] ),
sprintf( '%.4f', self::$profile->averages['core'] ),
sprintf( '%.4f', self::$profile->averages['theme'] ),
number_format( self::$profile->averages['memory'] / 1024 / 1024, 2 ),
number_format( self::$profile->averages['plugin_calls'] ),
sprintf( '%.2f', self::$profile->averages['queries'] ),
sprintf( '%.4f', self::$profile->averages['drift'] ),
$plugin_list
		); ?></textarea>
		</div>
		<input type="hidden" id="p3-email-results-scan" value="<?php echo basename( self::$scan ); ?>" />
	</div>
	
	<!-- Email sending dialog -->
	<div id="p3-email-sending-dialog" class="p3-dialog">
		<div id="p3-email-sending-loading">
			<img src="<?php echo get_site_url() . '/wp-admin/images/loading.gif' ?>" height="16" width="16" title="<?php esc_attr_e( 'Loading', 'p3-profiler' ); ?>" alt="<?php esc_attr_e( 'Loading', 'p3-profiler' ); ?>" />
		</div>
		<div id="p3-email-sending-error">
			<?php _e( 'There was a problem sending the e-mail:', 'p3-profiler' ); ?> <span id="p3-email-error"></span>
		</div>
		<div id="p3-email-sending-success">
			<?php _e( 'Your report was sent successfully to', 'p3-profiler' ); ?> <span id="p3-email-success-recipient"></span>
		</div>
		<div id="p3-email-sending-close">
			<input type="checkbox" id="p3-email-sending-close-submit" checked="checked" /><label for="p3-email-sending-close-submit" class="p3-email-sending-close"><?php _e( 'Done', 'p3-profiler' ); ?></label>
		</div>
	</div>

	<!-- Enable / disable series dialog -->
	<div id="p3-detailed-series-toggle" class="p3-dialog">
		
	</div>
</div>

<?php do_action( 'p3_runtime_by_plugin_notifications', self::$profile ); ?>
