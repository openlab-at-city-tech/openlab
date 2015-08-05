<?php
if ( !defined('P3_PATH') )
	die( 'Forbidden ');
?>
<script type="text/javascript">
	// Set up the tabs
	jQuery( document ).ready( function( $) {
		$( "#toggle-glossary" ).click( function() {
			$( "#glossary-terms" ).toggle();
			if ( "<?php _e( 'Hide Glossary', 'p3-profiler' ); ?>" == $( "#toggle-glossary" ).html() ) {
				$( "#toggle-glossary" ).html( "<?php _e( 'Show Glossary', 'p3-profiler' ); ?>" );
			} else {
				$( "#toggle-glossary" ).html( "<?php _e( 'Hide Glossary', 'p3-profiler' ); ?>" );
			}
		});
		$( "#glossary-terms td.term" ).click( function() {
			var definition = $( "div.definition", $( this ) ).html();
			$( "#p3-glossary-term-display" ).html( definition );
			$( "#p3-glossary-table td.term.hover" ).removeClass( "hover" );
			$( this ).addClass( "hover" );
		});
		$( "#p3-glossary-table td.term:first" ).click();
		$( "#p3-hide-glossary" ).click( function() {
			if ( "<?php _e( 'Hide', 'p3-profiler' ); ?>" == $( this ).html() ) {
				$( "#p3-glossary-table tbody" ).hide();
				$( "#p3-glossary-table tfoot" ).hide();
				$( this ).html( "<?php _e( 'Show', 'p3-profiler' ); ?>" );
			} else {
				$( "#p3-glossary-table tbody" ).show();
				$( "#p3-glossary-table tfoot" ).show();
				$( this ).html( "<?php _e( 'Hide', 'p3-profiler' ); ?>" );
			}
		});
		

		// Debug log
		$( "#p3-hide-debug-log" ).click( function() {
			if ( "<?php _e( 'Hide', 'p3-profiler' ); ?>" == $( this ).html() ) {
				$( "#p3-debug-log-table thead" ).hide();
				$( "#p3-debug-log-table tbody" ).hide();
				$( "#p3-debug-log-table tfoot" ).hide();
				$( this ).html( "<?php _e( 'Show', 'p3-profiler' ); ?>" );
			} else {
				$( "#p3-debug-log-table thead" ).show();
				$( "#p3-debug-log-table tbody" ).show();
				$( "#p3-debug-log-table tfoot" ).show();
				$( this ).html( "<?php _e( 'Hide', 'p3-profiler' ); ?>" );
			}
		});
		$( "#p3-debug-log-container table tbody tr:even ").addClass( "even" );


		// Automatically create the table of contents
		var links = [];
		var i = 1;
		$( "h2.p3-help-question:not(:first )" ).each( function() {
			if ( $( this ).attr( "data-question-id" ) !== undefined ) {
				$( this ).before( '<a name="' + $( this ).attr( "data-question-id" ) + '">&nbsp;</a>' );
				links.push( '<li><a href="#' + $( this ).attr( "data-question-id" ) + '">' + $( this ).html() + '</a></li>' );
			} else {
				$( this ).before( '<a name="q' + i + '">&nbsp;</a>' );
				links.push( '<li><a href="#q' + i + '">' + $( this ).html() + '</a></li>' );
				i++;
			}
		});
		$( "div.p3-question blockquote:not(:first )" ).each( function() {
			$( this ).after( '<a href="#top"><?php _e( 'Back to top', 'p3-profiler' ); ?></a>' );
		});
		$( "#p3-help-toc" ).html( "<ul>" + links.join( "\n" ) + "</ul>" );
		
		$( "div.p3-question" ).corner( "round 8px" )
	});
</script>

<div class="p3-question">
	<a name="top">&nbsp;</a>
	<h2 class="p3-help-question q-content"><?php _e( 'Contents', 'p3-profiler' ); ?></h2>
	<blockquote>
		<div id="p3-help-toc"></div>
	</blockquote>
</div>


<div class="p3-question">
	<h2 class="p3-help-question q-plugin-do"><?php _e( 'What does the P3 plugin do?', 'p3-profiler' ); ?></h2>
	<blockquote class="q-plugin-do-data">
		<?php _e( "This plugin does just what its name says, it creates a profile of your WordPress site's plugins' performance by measuring their impact on your site's load time.
<br /><br />
Often times, WordPress sites load slowly because of poorly-configured plugins or because there are so many of them. This plugin can help you narrow down the cause of your site's slowness.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-how-use"><?php _e( 'How do I use this?', 'p3-profiler' ); ?></h2>
	<blockquote class="q-how-use-data">
		<?php _e( "Simply click \"Start Scan\" to run an automated scan of your site. The scanner generates some traffic on your site and monitors your site's performance on the server, then shows you the results. With this information, you can decide what action to take.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-results"><?php _e( 'What do I do with these results?', 'p3-profiler' ); ?></h2>
	<blockquote class="q-results-data">
		<?php printf( __("If your site loads in an acceptable time (usually &lt; 0.5 seconds), you might consider other explanation for sluggish loading. For example, loading large images, large videos, or a lot of content can cause slowness. Tools like <a href=\"%1\$s\" target=\"_blank\">%2\$s</a>, <a href=\"%3\$s\" target=\"_blank\">%4\$s</a>, <a href=\"%5\$s\" target=\"_blank\">%6\$s</a>, or <a href=\"%7\$s\" target=\"_blank\">%8\$s</a> or <a href=\"%9\$s\" target=\"_blank\">%10\$s</a> can show you a connection breakdown of your site's content.", 'p3-profiler' ),
		'http://www.webpagetest.org/',                                         __( 'webpagetest.org', 'p3-profiler' ),
		'http://getfirebug.com/',                                              __( 'Firebug', 'p3-profiler' ),
		'http://tools.pingdom.com/',                                           __( 'Pingdom tools', 'p3-profiler' ),
		'http://developer.apple.com/technologies/safari/developer-tools.html', __( 'Safari Developer Tools', 'p3-profiler' ),
		'http://code.google.com/chrome/devtools/docs/overview.html',           __( 'Chrome Developer Tools', 'p3-profiler' )
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-no-visits" data-question-id="q-circumvent-cache"><?php _e( 'How do I fix "No visits recorded..." ?', 'p3-profiler' ); ?></h2>
	<blockquote class="q-no-visits-data">
		<?php _e( 'This error message means that after being disabled, the profiler did not record any traffic on your site.  There are several common causes for this:', 'p3-profiler' ); ?>
		<ul>
			<li>
				<strong><?php _e( 'Cause:', 'p3-profiler' ); ?></strong>
				<?php _e( "Your site is using a caching plugin.  The pages that are being scanned aren't actually loading on the server because they're cached in your browser or on the server before WordPress can generate them.  The P3 plugin doesn't load and doesn't record any traffic.", 'p3-profiler' ); ?>
				<br />
				<strong><?php _e( 'Solution:', 'p3-profiler' ); ?></strong>
				<?php _e( 'Enable the "Attempt to circumvent browser cache" option in the advanced settings.', 'p3-profiler' ); ?>
			</li>
			<li>
				<strong><?php _e( 'Cause:', 'p3-profiler' ); ?></strong>
				<?php _e( "The IP address you've entered in the advanced settings dialog doesn't match the IP address you're scanning from.", 'p3-profiler' ); ?>
				<br />
				<strong><?php _e( 'Solution:', 'p3-profiler' ); ?></strong>
				<?php _e( "Check the IP address you've entered and try again.", 'p3-profiler' ); ?>
			</li>
			<li>
				<strong><?php _e( 'Cause:', 'p3-profiler' ); ?></strong>
				<?php _e( "You've selected a manual scan, but haven't generated any traffic.", 'p3-profiler' ); ?>
				<br />
				<strong><?php _e( 'Solution:', 'p3-profiler' ); ?></strong>
				<?php _e( 'Try the automated scan.', 'p3-profiler' ); ?>
			</li>
		</ul>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-few-visits"><?php _e( 'Why did P3 only record 2 or 3 visits during the scan?', 'p3-profiler' ); ?></h2>
	<blockquote class="q-few-visits-data">
		<?php _e( "If your site is using a caching plugin, some pages might be cached in your browser or on the server and are loading before before WordPress can generate them.  When this happens, the P3 plugin doesn't load and doesn't record any traffic.  Please enable the \"Attempt to circumvent browser cache\" option in the advanced settings.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-p3-work"><?php _e( "How does this work?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-p3-work-data">
		<?php printf( __("When you activate the plugin by clicking \"Start Scan,\" it detects visits from your IP address, and actively monitors all <a href=\"%s\" target=\"_blank\">php user defined function calls</a> while the server generates your WordPress pages. It then records the information in a report file you can view later. When the scan is complete, or you click \"Stop Scan,\" the plugin becomes dormant again.", 'p3-profiler'),
			'http://php.net/functions'
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-site-load"><?php _e( "How does my site load the plugin?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-site-load-data">
		<?php printf( __("This plugin automatically creates a <a href=\"%s\" target=\"_blank\">must-use</a> plugin to load before other plugins. If that doesn't work, it runs like a regular plugin.", 'p3-profiler' ),
			'http://codex.wordpress.org/Must_Use_Plugins'
		); ?>
		<br /><br />
		<?php _e( 'You are currently using:', 'p3-profiler' ); ?>
		<?php
		// must-use plugin file
		$mu_file = WPMU_PLUGIN_DIR . '/p3-profiler.php';
		?>
		<?php /* must-use plugin file is there and not-empty */ ?>
		<?php if ( file_exists( $mu_file ) && filesize( $mu_file ) > 0 ){ ?>
			<a href="http://codex.wordpress.org/Must_Use_Plugins" target="_blank"><?php _e( 'must-use plugin', 'p3-profiler' ); ?></a>
			- <code><?php echo realpath( $mu_file ); ?></code>
		<?php /* default, using this plugin file */ ?>
		<?php } else { ?>
			<a href="http://codex.wordpress.org/Plugins" target="_blank"><?php _e( 'plugin', 'p3-profiler' ); ?></a>
			- <code><?php echo realpath( P3_PATH . '/p3-profiler.php' ); ?></code>
		<?php } ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-accurate"><?php _e( "How accurate are these results?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-accurate-data">
		<?php printf( __( "The results have an inherent margin of error because of the nature of the tool and its multi-layered design. The plugin changes the environment to measure it, and that makes it impossible to get completely accurate results.
<br /><br />
It gets really close, though! The \"margin of error\" on the Advanced Metrics page displays the discrepancy between the measured results (the time for your site's PHP code to completely run) and the expected results (sum of the plugins, core, theme, profile load times) to show you the plugin's accuracy.
<br /><br />
If you want more accurate results, you'll need to resort to a different profiler like <a href=\"%1\$s\" target=\"_blank\">%2\$s</a>, but this will not break down results by plugin.", 'p3-profiler' ),
			'http://xdebug.org/', __( 'xdebug', 'p3-profiler' )
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-plugins-slow"><?php _e( "Why are some plugins slow?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-plugins-slow-data">
		<?php _e( "WordPress is a complex ecosystem of plugins and themes, and it lives on a complex ecosystem of software on your web server.
<br /><br />
If a plugin runs slowly just once, it's probably an anomaly, a transient hiccup, and you can safely ignore it.
<br /><br />
If a plugin shows slowness once on a reguarly basis (e.g. every time you run a scan, once a day, once an hour), a scheduled task might be causing it. Plugins that backup your site, monitor your site for changes, contact outside sources (e.g. RSS feeds), warm up caches, etc. can exhibit this kind of behavior.
<br /><br />
If a plugin shows as fast-slow-fast-slow-fast-slow, it could be caused as the plugin loads its main code, then a follow-up piece of code, like a piece of generated JavaScript.
<br /><br />
If a plugin consistently shows slowness, you might want to contact the plugin author or try deactivating the plugin temporarily to see if it makes a difference on your site.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-different"><?php _e( "How are these results different from YSlow / PageSpeed / Webpagetest.org / Pingdom Tools?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-different-data">
		<?php printf( __("This plugin measures how your site was generated on the server. Tools like <a href=\"%1\$s\" target=\"_blank\">%2\$s</a>, <a href=\"%3\$s\" target=\"_blank\">%4\$s</a>, <a href=\"%5\$s\" target=\"_blank\">%6\$s</a>, and <a href=\"%7\$s\" target=\"_blank\">%8\$s</a> measure how your site looks to the browser.", 'p3-profiler'),
			'http://developer.yahoo.com/yslow/',         __( 'YSlow', 'p3-profiler' ),
			'https://developers.google.com/pagespeed/',  __( 'PageSpeed', 'p3-profiler' ),
			'http://www.webpagetest.org/',               __( 'Webpagetest.org', 'p3-profiler' ),
			'http://tools.pingdom.com/fpt/',             __( 'Pingdom Tools', 'p3-profiler' )
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-interfere"><?php _e( "What can interfere with testing?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-interfere-data">
		<?php _e( "Opcode optimizers can interfere with PHP backtraces. Leaving opcode optimizers turned on will result in timing that more accurately reflects your site's real performance, but the function calls to plugins may be \"optimized\" out of the backtraces and some plugins (especially those with only one hook) might not show up. Disabling opcode caches results in slower times, but shows all plugins.
<br /><br />
By default, this plugin attempts to disable any detected opcode optimizers when it runs. You can change this setting by clicking \"Advanced Settings\" under \"Start Scan.\"
<br /><br />
Caching plugins that have an option to disable caches for logged in users will not give you the same performance profile that an anonymous users experience. To get around this, you should select a manual scan, then run an incognito browser window, or run another browser, and browse your site as a logged out user. When you're finished, click \"I'm done,\" and your scan should show the performance of an anonymous user.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-opcode" data-question-id="q-opcode-optimizer"><?php _e( "Is my site using an opcode optimizer?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-opcode-data">
		<?php $detected = 0; if ( extension_loaded( 'xcache' ) ) { $detected++; ?>
			<?php _e( "Your site is using XCache.  Although XCache reports that no opcode optimization won't be implemented until version 2.0, this has been known to cause problems with P3.", 'p3-profiler' ); ?>
			<br />
		<?php } ?>	
		<?php if ( extension_loaded( 'apc' ) ) { $detected++; ?>
			<?php _e( "Your site is using APC.  This has not been known to cause problems with P3.", 'p3-profiler' ); ?>
			<br />
		<?php } ?>
		<?php if ( extension_loaded( 'eaccelerator' ) && ini_get( 'eaccelerator.optimizer' ) ) { $detected++; ?>
			<?php _e( "Your site is using eaccelerator with optimization enabled.  This has been known to cause problems with P3.", 'p3-profiler' ); ?>
			<?php if ( 'apache2handler' == strtolower( php_sapi_name() ) ) { ?>
				<?php _e( "To temporarily disable the optimizer you can add <code>php_flag eaccelerator.optimizer Off</code> to your site's .htaccess file.", 'p3-profiler' ); ?>
			<?php } elseif ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) { ?>
				<?php printf( __( "To temporarily disable the optimizer you can add <code>eaccelerator.optimizer = 0</code> to your site's <a href=\"%1\$s\" target=\"_blank\">%2\$s file</a>.", 'p3-profiler' ),
					'http://php.net/manual/en/configuration.file.per-user.php',
					ini_get( 'user_ini.filename' )
				); ?>
			<?php } else { ?>
				<?php _e( "To temporarily disable the optimizer you can ask your hosting provider.", 'p3-profiler' ); ?>
			<?php } ?>
			<br />
		<?php } ?>
		<?php if ( extension_loaded( 'Zend Optimizer+' ) && ini_get( 'zend_optimizerplus.optimization_level' ) > 0 ) { $detected++; ?>
			<?php _e( 'Your site is using Zend Optimizer+.  This has not been known to cause problems with P3.', 'p3-profiler' ); ?>
			<br />
		<?php } ?>
		<?php if ( extension_loaded( 'IonCube Loader' ) ) { $detected++; ?>
			<?php _e( 'Your site is using the IonCube loader.  This has not been known to cause problems with P3.', 'p3-profiler' ); ?>
			<br />
		<?php } ?>
		<?php if ( extension_loaded( 'wincache' ) ) { $detected++; ?>
			<?php _e( 'Your site is using wincache.  This has not been known to cause problems with P3.', 'p3-profiler' ); ?>
			<br />
		<?php } ?>
		<?php if ( extension_loaded( 'Zend Guard Loader' ) ) { $detected++; ?>
			<?php _e( 'Your site is using the Zend Guard loader.  This has not been known to cause problems with P3.', 'p3-profiler' ); ?>
			<br />
		<?php } ?>
		<?php if ( extension_loaded( 'Zend Optimizer' ) ) { $detected++; ?>
			<?php _e( 'Your site is using the Zend Optimizer.  This extension has not been tested with P3.  Please report any problems.', 'p3-profiler' ); ?>
			<br />
		<?php } ?>
		<?php if ( !$detected ) { ?>
			<?php _e( 'P3 has not detected any opcode optimizers on your site.  Although none were detected, an opcode optimizer may still be present.  Contact your server administrator with any questions.', 'p3-profiler' ); ?>
		<?php } ?>
	</blockquote>
</div>


<div class="p3-question">
	<h2 class="p3-help-question q-space"><?php _e( "How much room do these profiles take up on my server?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-space-data">
		<?php
		$total_size = 0;
		$dir        = opendir( P3_PROFILES_PATH );
		while ( false !== ( $file = readdir( $dir ) ) ) {
			if ( '.' != $file && '..' != $file && '.json' == substr( $file, -5 ) ) {
				$total_size += filesize( P3_PROFILES_PATH . "/$file" );
			}
		}
		closedir( $dir );

		?>
		<?php printf( __( "The scans are stored in <code>%1\$s</code> and take up %2\$s of disk space.  Each time you run a scan, this storage requirement goes up, and each time you delete a scan, it goes down.", 'p3-profiler' ),
			realpath( P3_PROFILES_PATH ),
			self::readable_size( $total_size )
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-running"><?php _e( "Is this plugin always running?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-running-data">
		<?php _e( "The short answer is no.
<br /><br />
The more detailed answer is the loader is always running, but checks very early in the page loading process to see if you've enabled profiling mode and if the user's IP address matches the IP address the plugin is monitoring. For multisite installations, it also matches the site URL. If all these match, the plugin becomes active and profiles. Otherwise, your site loads as normal with no other code overhead.
<br /><br />
Deactivating the plugin ensures it's not running at all, and does not delete your scans. However, uninstalling the plugin does delete your scans.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-specific"><?php _e( "How can I test specific pages on my site?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-specfic-data">
		<?php _e( "When you start a scan, choose \"Manual Scan\" and then you can visit specific links on your site that you want to profile. If you want to profile the admin section, just click the \"X\" in the top right of the scan window and you'll be returned to your admin section. You can browse as normal, then come back to the profile page and click \"Stop Scan\" when you're ready to view the results.
<br /><br />
To scan your site as an anonymous user, select \"Manual Mode\" as above, but instead of clicking your site in the scan window, open a different browser (or an incognito window) and browse your site as a logged out user. When you're done, close that browser and return to your admin. Click \"I'm done\" and view your scan results.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-still-slow"><?php _e( "My plugins don't seem to cause site slowness.  Why is my site still slow?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-still-slow-data">
		<?php printf( __( "Your site can be slow for a number of reasons. Your site could have a lot of traffic, other sites on your server could have a lot of traffic, you could be referencing content from other sites that are slow, your Internet connection could be slow, your server could be out of RAM, your site could be very image heavy, your site could require a lot of HTTP requests, etc. In short, a lot of factors can cause slowness on your site
<br /><br />
Your next stop should be to use <a href=\"%1\$s\" target=\"_blank\">%2\$s</a>, <a href=\"%3\$s\" target=\"_blank\">%4\$s</a>, <a href=\"%5\$s\" target=\"_blank\">%6\$s</a>, <a href=\"%7\$s\" target=\"_blank\">%8\$s</a>, and your browser's development tools like <a href=\"%9\$s\" target=\"_blank\">%10\$s</a> for Firefox, <a href=\"%11\$s\" target=\"_blank\">%12\$s</a> for Chrome, or <a href=\"%13\$s\" target=\"_blank\">%14\$s</a> for Safari.
<br /><br />
After you've tuned your site up as much as possible, if you're still not happy with its performance, you should consult your site/server administrator or hosting support.", 'p3-profiler'),
			'http://tools.pingdom.com/',                                           __( 'Pingdom Tools', 'p3-profiler' ),
			'http://webpagetest.org/',                                             __( 'Webpage Test', 'p3-profiler' ),
			'http://developer.yahoo.com/yslow/',                                   __( 'YSlow', 'p3-profiler' ),
			'https://developers.google.com/pagespeed/',                            __( 'Google PageSpeed', 'p3-profiler' ),
			'http://getfirebug.com/',                                              __( 'Firebug', 'p3-profiler' ),
			'http://code.google.com/chrome/devtools/docs/overview.html',           __( 'Chrome Developer Tools', 'p3-profiler' ),
			'http://developer.apple.com/technologies/safari/developer-tools.html', __( 'Safari Developer Tools', 'p3-profiler' )
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-debug" data-question-id="q-debug-log"><?php _e( "Where can I view the debug log?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-debug-data">
		<?php printf( __( "Debug mode will record 100 visits to your site, then turn off automatically.  You can view the log below.  The entries are shown in reverse order with the latest visits appearing at the top of the list.  You can also <a href=\"%1\$s\" class=\"button-secondary\">Clear the log</a> or <a href=\"%2\$s\" class=\"button-secondary\">Download the log</a> as a CSV.", 'p3-profiler' ),
			wp_nonce_url( esc_url_raw( add_query_arg( array( 'p3_action' => 'clear-debug-log' ) ) ), 'p3-clear-debug-log' ),
			wp_nonce_url( esc_url_raw( add_query_arg( array( 'p3_action' => 'download-debug-log' ) ) ), 'p3-download-debug-log' )
		); ?>
		<br /><br />
		<div id="p3-debug-log-container">
			<div class="ui-widget-header" id="p3-debug-log-header" style="padding: 8px;">
				<strong><?php _e( 'Debug Log', 'p3-profiler' ); ?></strong>
				<div style="position: relative; top: 0px; right: 80px; float: right;">
					<a href="javascript:;" id="p3-hide-debug-log"><?php _e( 'Hide', 'p3-profiler' ); ?></a>
				</div>
			</div>
			<div>
				<table class="p3-results-table" id="p3-debug-log-table" cellpadding="0" cellspacing="0" border="0">
					<thead>
						<tr>
							<td><strong><?php _ex( '#', 'Symbol meaning number', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Profiling Enabled', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Recording IP', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Scan Name', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Recording', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Disable Optimizers', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'URL', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Visitor IP', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Time', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _ex( 'PID', 'Abbreviation for process id', 'p3-profiler' ); ?></strong></td>
						</tr>
					</thead>
					<tbody>
						<?php $log = get_option( 'p3-profiler_debug_log' ); $c = count( $log ); foreach ( $log as $entry ) : ?>
							<tr>
								<td><?php echo $c--; ?></td>
								<td><?php echo $entry['profiling_enabled'] ? 'true' : 'false'; ?></td>
								<td><?php echo $entry['recording_ip']; ?></td>
								<td>
								<?php if ( file_exists(P3_PROFILES_PATH . '/' . $entry['scan_name'] . '.json' ) ) : ?>
									<a href="<?php echo esc_url( add_query_arg( array(
										'p3_action'    => 'view-scan',
										'current-scan' => null,
										'name'         => $entry['scan_name'] . '.json'
									) ) ); ?>"><?php echo $entry['scan_name']; ?></a>
								<?php else : ?>
									<?php echo $entry['scan_name']; ?>
								<?php endif; ?>
								</td>
								<td><?php echo $entry['recording'] ? 'true' : 'false'; ?></td>
								<td><?php echo $entry['disable_optimizers'] ? 'true' : 'false'; ?></td>
								<td><?php echo htmlentities( $entry['url'] ); ?></td><?php // URL intentionally not clickable to avoid accidental replay attacks ?>
								<td><?php echo $entry['visitor_ip']; ?></td>
								<td><?php echo human_time_diff( $entry['time'] ) . ' ' . __('ago'); ?></td>
								<td><?php echo $entry['pid']; ?></td>
							</tr>
						<?php endforeach ; ?>
					</tbody>
					<tfoot>
						<tr>
							<td><strong><?php _ex( '#', 'Symbol meaning number', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Profiling Enabled', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Recording IP', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Scan Name', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Recording', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Disable Optimizers', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'URL', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Visitor IP', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _e( 'Time', 'p3-profiler' ); ?></strong></td>
							<td><strong><?php _ex( 'PID', 'Abbreviation for process id', 'p3-profiler' ); ?></strong></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-usort"><?php _e( "What if I get a warning about usort()?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-usort-data">
		<?php printf( _x( "Warning messages like this: <code>Warning: usort() [function.usort]: Array was modified by the user comparison function</code> are due to a known php bug.  See <a href=\"%s\" target=\"_blank\">php bug #50688</a> for more information.  This warning does not affect the functionality of your site and it is not visible to your users.", 'Warning message is taken verbatim from PHP output', 'p3-profiler' ),
			'https://bugs.php.net/bug.php?id=50688'
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-memory"><?php _e( "Does this plugin increase memory usage on my site?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-memory-data">
		<?php printf( __( "When you run a performance scan on your site, the memory requirements go up during the scan.  Accordingly, P3 sets your <a href=\"%1\$s\" target=\"_blank\">%2\$s</a> to 256 MB and <a href=\"%3\$s\" target=\"_blank\">%4\$s</a> to 90 seconds during a performance scan.  These changes are not permanent and are only in effect when a performance scan is actively running.", 'p3-profiler' ),
			'http://www.php.net/manual/en/ini.core.php#ini.memory-limit',  __( 'memory limit', 'p3-profiler' ),
			'http://php.net/set_time_limit',                               __( 'time limit', 'p3-profiler' )
		); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-jetpack"><?php _e( "Why do some plugins show artificially high results?", 'p3-profiler' ); ?></h2>
	<blockquote class="q-specfic-data">
		<?php _e( "P3 scans your site as a logged in user.  Some plugins enable more functionality when you are logged in.  When P3 detects a plugin which could be a false positive, such as Jetpack, you'll see a notice.  The authors of these plugins have put a focus on performance and you should feel safe leaving them enabled on your site if you need the functionality they provide.", 'p3-profiler' ); ?>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-glossary" style="border-bottom-width: 0px !important;"><?php _e( 'Glossary', 'p3-profiler' ); ?></h2>
	<blockquote class="q-glossary-data">
		<div>
			<div id="p3-glossary-container">
				<div class="ui-widget-header" id="p3-glossary-header" style="padding: 8px;">
					<strong><?php _e( 'Glossary', 'p3-profiler' ); ?></strong>
					<div style="position: relative; top: 0px; right: 80px; float: right;">
						<a href="javascript:;" id="p3-hide-glossary"><?php _e( 'Hide', 'p3-profiler' ); ?></a>
					</div>
				</div>
				<div>
					<table class="p3-results-table" id="p3-glossary-table" cellpadding="0" cellspacing="0" border="0">
						<tbody>
							<tr>
								<td colspan="2" style="border-left-width: 1px !important;">
									<div id="glossary">
										<table width="100%" cellpadding="0" cellspacing="0" border="0" id="glossary-terms">
											<tr>
												<td width="200" class="term"><strong><?php _e( 'Total Load Time', 'p3-profiler' ); ?></strong>
													<div id="total-load-time-definition" style="display: none;" class="definition">
														<?php _e( 'The length of time the site took to load. This is an observed measurement (start timing when the page was requested, stop timing when the page was delivered to the browser, calculate the difference). Lower is better.', 'p3-profiler' ); ?>
													</div>
												</td>
												<td width="400" rowspan="12" id="p3-glossary-term-display">&nbsp;</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Site Load Time', 'p3-profiler' ); ?></strong>
													<div id="site-load-time-definition" style="display: none;" class="definition">
														<?php _e( "The calculated total load time minus the profile overhead. This is closer to your site's real-life load time. Lower is better.", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Profile Overhead', 'p3-profiler' ); ?></strong>
													<div id="profile-overhead-definition" style="display: none;" class="definition">
														<?php _e( "The load time spent profiling code. Because the profiler slows down your load time, it is important to know how much impact the profiler has. However, it doesn't impact your site's real-life load time.", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Plugin Load Time', 'p3-profiler' ); ?></strong>
													<div id="plugin-load-time-definition" style="display: none;" class="definition">
														<?php _e( "The load time caused by plugins. Because of WordPress' construction, we can trace a function call from a plugin through a theme through the core. The profiler prioritizes plugin calls first, theme calls second, and core calls last. Lower is better.", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Theme Load Time', 'p3-profiler' ); ?></strong>
													<div id="theme-load-time-definition" style="display: none;" class="definition">
														<?php _e( "The load time spent applying the theme. Because of WordPress' construction, we can trace a function call from a plugin through a theme through the core. The profiler prioritizes plugin calls first, theme calls second, and core calls last. Lower is better.", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Core Load Time' , 'p3-profiler' ); ?></strong>
													<div id="core-load-time-definition" style="display: none;" class="definition">
														<?php _e( "The load time caused by the WordPress core. Because of WordPress' construction, we can trace a function call from a plugin through a theme through the core. The profiler prioritizes plugin calls first, theme calls second, and core calls last. This will probably be constant.", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Margin of Error', 'p3-profiler' ); ?></strong>
													<div id="drift-definition" style="display: none;" class="definition">
														<?php _e( "This is the difference between the observed runtime (what actually happened) and expected runtime (adding the plugin runtime, theme runtime, core runtime, and profiler overhead).
<br /><br />
There are several reasons this margin of error can exist. Most likely, the profiler is missing microseconds while adding the runtime it observed. Using a network clock to set the time (NTP) can also cause minute timing changes.
<br /><br />
Ideally, this number should be zero, but there's nothing you can do to change it. It will give you an idea of how accurate the other results are.", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Observed', 'p3-profiler' ); ?></strong>
													<div id="observed-definition" style="display: none;" class="definition">
														<?php _e( "The time the site took to load. This is an observed measurement (start timing when the page was requested, stop timing when the page was delivered to the browser, calculate the difference).", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Expected', 'p3-profiler' ); ?></strong>
													<div id="expected-definition" style="display: none;" class="definition">
														<?php _e( 'The expected site load time calculated by adding plugin load time, core load time, theme load time, and profiler overhead.', 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Plugin Function Calls' , 'p3-profiler' ); ?></strong>
													<div id="plugin-funciton-calls-definition" style="display: none;" class="definition">
														<?php _e( "The number of PHP function calls generated by a plugin. Fewer is better.", 'p3-profiler' ); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'Memory Usage', 'p3-profiler' ); ?></strong>
													<div id="memory-usage-definition" style="display: none;" class="definition">
														<?php printf( __( "The amount of RAM usage observed. This is reported by <a href=\"%s\" target=\"_blank\">memory_get_peak_usage()</a>.  Lower is better.", 'p3-profiler' ),
															'http://php.net/memory_get_peak_usage'
														); ?>
													</div>
												</td>
											</tr>
											<tr>
												<td class="term"><strong><?php _e( 'MySQL Queries', 'p3-profiler' ); ?></strong>
													<div id="mysql-queries-definition" style="display: none;" class="definition">
														<?php printf( __( "The number of queries sent to the database. This is reported by the WordPress function <a href=\"%s\" target=\"_blank\">get_num_queries()</a>.  Fewer is better.", 'p3-profiler' ),
															'http://codex.wordpress.org/Function_Reference/get_num_queries'
														); ?>
													</div>
												</td>
											</tr>
										</table>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</blockquote>
</div>

<div class="p3-question">
	<h2 class="p3-help-question q-license"><?php _e( "License", 'p3-profiler' ); ?></h2>
	<blockquote class="q-license-data">
		<?php printf( __( 'P3 (Plugin Performance Profiler) is Copyright &copy; %1$s - %2$s <a href="%3$s" target="_blank">GoDaddy.com</a>.  All rights reserved.', 'p3-profiler' ), 2011, date( 'Y' ), 'http://www.godaddy.com/' ); ?>
		<br /><br />
		<?php printf( __( "This program is offered under the terms of the GNU General Public License Version 2 as published by the Free Software Foundation.
<br /><br />
This program offered WITHOUT WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License Version 2 for the specific terms.
<br /><br />
A copy of the GNU General Public License has been provided with this program.  Alternatively, you may find a copy of the license here: <a href=\"%s\" target=\"_blank\">%s</a>.", 'p3-profiler' ),
			'http://www.gnu.org/licenses/gpl-2.0.html',
			'http://www.gnu.org/licenses/gpl-2.0.html'
		); ?>
	</blockquote>
</div>
