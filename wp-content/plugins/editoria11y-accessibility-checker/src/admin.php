<?php // phpcs:ignore
/**
 * Admin settings page.
 *
 *  @package         Editoria11y
 */

/**
 * Allowed HTML for filters.
 */
function ed11y_allowed_html() {
	$allowed_html = array(
		'em'     => array(),
		'strong' => array(),
		'code'   => array(),
		'br'     => array(),
		'p'      => array(),
	);
	return $allowed_html;
}

/**
 * Sets up the plugin settings page and registers the plugin settings.
 *
 * @link   http://codex.wordpress.org/Function_Reference/add_options_page
 */
function ed11y_admin_menu() {
	$settings = add_options_page(
		esc_html__( 'Editoria11y Settings', 'editoria11y' ),
		esc_html__( 'Editoria11y', 'editoria11y' ),
		'manage_options',
		'ed11y',
		'ed11y_plugin_settings_render_page'
	);
	if ( ! $settings ) {
		return;
	}
	// Provided hook_suffix that's returned to add scripts only on settings page.
	add_action( 'load-' . $settings, 'ed11y_styles_scripts' );
}
add_action( 'admin_menu', 'ed11y_admin_menu' );

/**
 * Enqueue custom styles & scripts for plugin usage.
 */
function ed11y_styles_scripts() {
	// Load plugin admin style.
	wp_enqueue_style( 'editoria11y-wp-css', trailingslashit( ED11Y_ASSETS ) . 'css/editoria11y-wp-admin.css', null, Editoria11y::ED11Y_VERSION );
}

/**
 * Register settings.
 *
 * @link   http://codex.wordpress.org/Function_Reference/register_setting
 */
function ed11y_register_settings() {

	register_setting(
		'ed11y_settings',
		'ed11y_plugin_settings',
		'ed11y_plugin_settings_validate'
	);
}
add_action( 'admin_init', 'ed11y_register_settings' );

/**
 * Register the setting sections and fields.
 *
 * @link   http://codex.wordpress.org/Function_Reference/add_settings_section
 * @link   http://codex.wordpress.org/Function_Reference/add_settings_field
 */
function ed11y_setting_sections_fields() {

	/* =============== Sections */

	// Add General section.
	add_settings_section(
		'ed11y_basic',
		__( 'Basic configuration', 'editoria11y' ),
		'__return_false',
		'ed11y'
	);

	// Add dataviz content section.
	add_settings_section(
		'ed11y_test_settings',
		__( 'Customize test selectors', 'editoria11y' ),
		'__return_false',
		'ed11y'
	);

	// Add compatibility section.
	add_settings_section(
		'ed11y_compatibility_settings',
		__( 'Compatibility with themes and other plugins', 'editoria11y' ),
		'__return_false',
		'ed11y'
	);

	/* ================= Fields */

	/* == basic config == */

	// Add themepicker field.
	add_settings_field(
		'ed11y_theme',
		esc_html__( 'Theme for tooltips', 'editoria11y' ),
		'ed11y_theme_field',
		'ed11y',
		'ed11y_basic',
		array( 'label_for' => 'ed11y_theme' )
	);

	// Add live check field.
	add_settings_field(
		'ed11y_livecheck',
		esc_html__( 'Highlight issues while editing content', 'editoria11y' ),
		'ed11y_livecheck_field',
		'ed11y',
		'ed11y_basic',
		array( 'label_for' => 'ed11y_livecheck' )
	);

	// Add reports permission field.
	add_settings_field(
		'ed11y_report_restrict',
		esc_html__( 'Only admins can view reports', 'editoria11y' ),
		'ed11y_report_restrict_field',
		'ed11y',
		'ed11y_basic',
		array( 'label_for' => 'ed11y_report_restrict_field' )
	);

	/* == Customize selectors == */

	// Add 'Check Roots' input setting field.
	add_settings_field(
		'ed11y_checkRoots',
		esc_html__( 'Check this part of the page', 'editoria11y' ),
		'ed11y_check_roots_field',
		'ed11y',
		'ed11y_test_settings',
		array( 'label_for' => 'ed11y_checkRoots' )
	);

	// Add container ignore field.
	add_settings_field(
		'ed11y_ignore_elements',
		esc_html__( 'Do not flag these elements', 'editoria11y' ),
		'ed11y_ignore_elements_field',
		'ed11y',
		'ed11y_test_settings',
		array( 'label_for' => 'ed11y_ignore_elements' )
	);

	// Document types field.
	add_settings_field(
		'ed11y_documentContent',
		esc_html__( 'Document types flagged as needing manual review', 'editoria11y' ),
		'ed11y_document_content_field',
		'ed11y',
		'ed11y_test_settings',
		array( 'label_for' => 'ed11y_documentContent' )
	);

	// Add datavizContent field.
	add_settings_field(
		'ed11y_datavizContent',
		esc_html__( 'Embeds flagged as needing manual review', 'editoria11y' ),
		'ed11y_dataviz_content_field',
		'ed11y',
		'ed11y_test_settings',
		array( 'label_for' => 'ed11y_datavizContent' )
	);

	// Add Video content field.
	add_settings_field(
		'ed11y_videoContent',
		esc_html__( 'Videos flagged as needing a manual check for captions', 'editoria11y' ),
		'ed11y_video_content_field',
		'ed11y',
		'ed11y_test_settings',
		array( 'label_for' => 'ed11y_videoContent' )
	);

	// Audio content field.
	add_settings_field(
		'ed11y_audioContent',
		esc_html__( 'Audio flagged as needing a manual check for transcripts', 'editoria11y' ),
		'ed11y_audio_content_field',
		'ed11y',
		'ed11y_test_settings',
		array( 'label_for' => 'ed11y_audioContent' )
	);

	// Add link text ignore field.
	add_settings_field(
		'ed11y_checkvisibility',
		esc_html__( 'Check if elements are visible when using panel navigation buttons', 'editoria11y' ),
		'ed11y_checkvisibility_field',
		'ed11y',
		'ed11y_compatibility_settings',
		array( 'label_for' => 'ed11y_checkvisibility' )
	);

	// Add link text ignore field.
	add_settings_field(
		'ed11y_link_ignore_strings',
		esc_html__( 'Ignore these strings in links', 'editoria11y' ),
		'ed11y_link_ignore_strings_field',
		'ed11y',
		'ed11y_compatibility_settings',
		array( 'label_for' => 'ed11y_link_ignore_strings' )
	);

	// Don't run ed11y if these elements exist.
	add_settings_field(
		'ed11y_custom_tests',
		esc_html__( 'Pause tests for this number of custom result insertions', 'editoria11y' ),
		'ed11y_custom_tests_field',
		'ed11y',
		'ed11y_compatibility_settings',
		array( 'label_for' => 'ed11y_custom_tests' )
	);

	// Don't run ed11y if these elements exist.
	add_settings_field(
		'ed11y_no_run',
		esc_html__( 'Turn off Editoria11y if these elements exist', 'editoria11y' ),
		'ed11y_no_run_field',
		'ed11y',
		'ed11y_compatibility_settings',
		array( 'label_for' => 'ed11y_no_run' )
	);
}
add_action( 'admin_init', 'ed11y_setting_sections_fields' );

/**
 * Target field
 */
function ed11y_theme_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_theme' );
	?>

	<select name="ed11y_plugin_settings[ed11y_theme]" id="ed11y-theme" name="ed11y_theme" class="form-select">
		<option <?php echo 'sleekTheme' === $settings ? 'selected="true"' : ''; ?>value="sleekTheme">Sleek</option>
		<option <?php echo 'lightTheme' === $settings ? 'selected="true"' : ''; ?>value="lightTheme">Classic</option>
		<option <?php echo 'darkTheme' === $settings ? 'selected="true"' : ''; ?>value="darkTheme">Dark</option>
	</select>

	<?php
}

/**
 * Livecheck field
 */
function ed11y_livecheck_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_livecheck' );
	?>

	<select name="ed11y_plugin_settings[ed11y_livecheck]" id="ed11y-livecheck" name="ed11y_livecheck" class="form-select" aria-describedby="livecheck_description">
		<option <?php echo 'all' === $settings ? 'selected="true"' : ''; ?>value="all">All issues</option>
		<option <?php echo 'errors' === $settings ? 'selected="true"' : ''; ?>value="errors">Only definite errors</option>
		<option <?php echo 'none' === $settings ? 'selected="true"' : ''; ?>value="none">None</option>
	</select>
	<p id="livecheck_description">
		Editoria11y's full tips with details and dismissal options appear when viewing published or preview pages.
	</p>
	<p>
		Simplified alerts can also be injected into the block editor, as highlights around the affected block. Adjust this to reduce or remove those highlights.
	</p>
	<?php
}

/**
 * Dashboard access for editors field
 */
function ed11y_report_restrict_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_report_restrict' );
	?>
	<input type="checkbox" aria-describedby="ed11y_report_restrict_description" name="ed11y_plugin_settings[ed11y_report_restrict]" id="ed11y_report_restrict_field" value="1"<?php checked( '1', $settings ); ?> />
	<p id="ed11y_report_restrict_description">By default both admins and editors can view reports.</p>
	<?php
}


/**
 * Target field
 */
function ed11y_check_roots_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_checkRoots' );
	$default  = ed11y_get_default_options( 'ed11y_checkRoots' );
	?>
	<input autocomplete="off" 
		name="ed11y_plugin_settings[ed11y_checkRoots]" 
		type="text" 
		id="ed11y_checkRoots" 
		placeholder="<?php echo esc_attr( $default ); ?>"
		value="<?php echo esc_attr( $settings ); ?>" 
		aria-describedby="target_description" />
	<p id="target_description">
		<?php
			echo wp_kses(
				__(
					'Editoria11y works best when it only checks content editors can...edit.
			If it is flagging issues in your header or footer, put CSS selectors here for the elements 
			that contain your editable content, e.g. <code>#content, footer</code>',
					'editoria11y'
				),
				ed11y_allowed_html()
			);
		?>
	</p>
	<p>
		<?php
			echo wp_kses( __( 'The default is <code>main</code> or <code>body</code>, depending on theme.', 'editoria11y' ), ed11y_allowed_html() );
		?>
		</p>
	</p>
	<?php
}


/**
 * Container ignore field
 */
function ed11y_ignore_elements_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_ignore_elements' );
	$default  = ed11y_get_default_options( 'ed11y_ignore_elements' );
	?>
	<textarea autocomplete="off" 
	class="regular-text" id="ed11y_ignore_elements" 
	aria-describedby="exclusions_description" 
	name="ed11y_plugin_settings[ed11y_ignore_elements]"
	rows="3" cols="45"><?php echo esc_attr( $settings ); ?></textarea>
	<p id="exclusions_description">
		<?php
			echo wp_kses(
				__(
					'If Editoria11y is flagging things editors cannot fix, e.g., theme-generated "read more" links or social media widgets,
			provide CSS selectors for elements you would like it to ignore. Be specific, e.g. <code>.read-more a, .wp-block-post-excerpt__more-link, #comments h3</code>'
				),
				ed11y_allowed_html()
			);
		?>
	</p>
	<p>
		If you are new at this, start by <a href="https://developer.chrome.com/docs/devtools/open/">opening 
			your browser's developer tools</a>, inspecting the element you do not want flagged, 
			and looking for unique-looking <a href="https://developer.mozilla.org/en-US/docs/Learn/Getting_started_with_the_web/CSS_basics#different_types_of_selectors">CSS selectors</a>.
	</p>
	<p>Default: <code><?php echo esc_attr( $default ); ?></code></p>
	<?php
}

/**
 * Video field
 */
function ed11y_video_content_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_videoContent' );
	$default  = ed11y_get_default_options( 'ed11y_videoContent' );
	?>
	<textarea id="ed11y_videoContent" 
		name="ed11y_plugin_settings[ed11y_videoContent]" 
		cols="45" rows="3"><?php echo esc_html( $settings ); ?></textarea>
		<p>Default: <code><?php echo esc_attr( $default ); ?></code></p>
	<?php
}

/**
 * Audio field
 */
function ed11y_audio_content_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_audioContent' );
	$default  = ed11y_get_default_options( 'ed11y_audioContent' );
	?>
	<textarea id="ed11y_audioContent" name="ed11y_plugin_settings[ed11y_audioContent]" 
	cols="45" rows="3"><?php echo esc_html( $settings ); ?></textarea>
	<p>Default: <code><?php echo esc_attr( $default ); ?></code></p>
	<?php
}

/**
 * Document field
 */
function ed11y_document_content_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_documentContent' );
	$default  = ed11y_get_default_options( 'ed11y_documentContent' );
	?>
	<textarea id="ed11y_documentContent" name="ed11y_plugin_settings[ed11y_documentContent]" 
	cols="45" rows="3"
	><?php echo esc_html( $settings ); ?></textarea>
	<p>By default, Editoria11y will flag links to these document types: <code><?php echo esc_attr( $default ); ?></code></p>
	<p>If you would like to override this to flag more or fewer document types, copy and paste that list into this field and adjust to your liking. 
		If you do not want any document links flagged, set this field to <code>false</code></p>
	<?php
}

/**
 * Field for datavizContent.
 */
function ed11y_dataviz_content_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_datavizContent' );
	$default  = ed11y_get_default_options( 'ed11y_datavizContent' );
	?>
	<textarea id="ed11y_datavizContent" name="ed11y_plugin_settings[ed11y_datavizContent]" 
	cols="45" rows="3"><?php echo esc_html( $settings ); ?></textarea>
	<p>Default: <code><?php echo esc_attr( $default ); ?></code></p>
	<?php
}

/**
 * Disable visible check
 */
function ed11y_checkvisibility_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_checkvisibility', false );
	?>
	<select name="ed11y_plugin_settings[ed11y_checkvisibility]" id="ed11y-checkvisibility" name="ed11y_checkvisibility" class="form-select" aria-describedby="checkvisibility_description">
		<option <?php echo '' === $settings ? 'selected="true"' : ''; ?>value="">Theme default</option>
		<option <?php echo 'true' === $settings ? 'selected="true"' : ''; ?>value="true">Check for visibility</option>
		<option <?php echo 'false' === $settings ? 'selected="true"' : ''; ?>value="false">Disable visibility checking</option>
	</select>

	<p id="checkvisibility-description">Set if your theme throws "this element may be hidden" alerts 
		when using the next/previous buttons on the main panel. 
		See the main library documentation for <a href="https://editoria11y.princeton.edu/configuration/#js-events">JS events</a> and <a href="https://editoria11y.princeton.edu/configuration/#hidden-content">developer tips for revealing hidden content on demand</a>.</p>
		<p><em>And please tell us if this happens with a common theme so we can add it to the defaults!</em></p>
	<?php
}

/**
 * Link span ignore field
 */
function ed11y_link_ignore_strings_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_link_ignore_strings' );
	$default  = ed11y_get_default_options( 'ed11y_link_ignore_strings' );
	?>
	   
	<input autocomplete="off" class="regular-text" 
	id="ed11y_link_ignore_strings" 
	aria-describedby="link_description" 
	type="text" 
	name="ed11y_plugin_settings[ed11y_link_ignore_strings]" 
	placeholder="<?php echo esc_attr( $default ); ?>" 
	value="<?php echo esc_attr( $settings ); ?>"/>
	<p id="link_span_description">
		<?php
			echo wp_kses( __( 'Some themes inject hidden text for screen readers to explain external link icons. Provide a RegEx to exclude this theme-injected text from tests, e.g.:<br> <code>(Link opens in new window)|(External link)</code>', 'editoria11y' ), ed11y_allowed_html() );
		?>
	</p>
	<?php
}

/**
 * Turn off Editoria11y if these elements are detected
 */
function ed11y_custom_tests_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_custom_tests' );
	$default  = ed11y_get_default_options( 'ed11y_custom_tests' );
	?>
	<input autocomplete="off" 
	class="regular-text" id="ed11y_custom_tests" 
	aria-describedby="ed11y_custom_tests_description" 
	type="number" min="0" max="99" name="ed11y_plugin_settings[ed11y_custom_tests]" 
	placeholder="<?php echo esc_attr( $default ); ?>" 
	value="<?php echo esc_attr( $settings ); ?>" pattern="[^<>\\\x27;|@&]+"/>
	<p id="ed11y_custom_tests_description">
		<?php
			echo wp_kses( __( 'Themes and modules can add custom tests to the checker. When custom tests finish, the send an "ed11yResume" event. Editoria11y will wait until it receives this number of resume notifications before showing results.', 'editoria11y' ), ed11y_allowed_html( 'a' ) );
		?>
	</p>

	<?php
}

/**
 * Turn off Editoria11y if these elements are detected
 */
function ed11y_no_run_field() {
	$settings = ed11y_get_plugin_settings( 'ed11y_no_run' );
	$default  = ed11y_get_default_options( 'ed11y_no_run' );
	?>
	<input autocomplete="off" 
	class="regular-text" id="ed11y_no_run" 
	aria-describedby="ed11y_no_run_description" 
	type="text" name="ed11y_plugin_settings[ed11y_no_run]" 
	placeholder="<?php echo esc_attr( $default ); ?>" 
	value="<?php echo esc_attr( $settings ); ?>" pattern="[^<>\\\x27;|@&]+"/>
	<p id="ed11y_no_run_description">
		<?php
			echo wp_kses( __( 'Used to disable checks on particular page, or when content editing tools are active.', 'editoria11y' ), ed11y_allowed_html() );
		?>
	</p>

	<?php
}

/**
 * Render the plugin settings page.
 */
function ed11y_plugin_settings_render_page() {
	?>

	<div class="wrap">
		<h1><?php esc_html_e( 'Editoria11y Settings', 'editoria11y' ); ?></h1>

		<div id="poststuff">
			<div id="post-body" class="editoria11y-settings metabox-holder columns-2">
				<div id="post-body-content">

				<div class="announcement-component">
					<!-- stuff above the form -->
				</div>

				<form method="post" action="options.php" autocomplete="off" class="ed11y-form-admin">
					<?php settings_fields( 'ed11y_settings' ); ?>
					<?php do_settings_sections( 'ed11y' ); ?>
					<?php submit_button( esc_html__( 'Save Settings', 'editoria11y' ), 'primary large' ); ?>
				</form>
			</div><!-- .post-body-content -->

			<div id="postbox-container-1" class="postbox-container">
				<div class="postbox">
					<div class="inside">
					<h2 class="postbox-heading">
						Getting started
					</h2>
					<p>Editoria11y should work out of the box in most themes (view a 
						<a href="https://jjameson.mycpanel.princeton.edu/editoria11y/">demo of the authoring experience</a>). 
						<ol>
							<li>If authors do not see the checker toggle, check your <a href="https://developer.mozilla.org/en-US/docs/Tools/Browser_Console" class="ext" data-extlink="">browser console</a> for errors, and make sure the theme is not hiding <code>ed11y-element-panel</code>.</li>
							<li>If the checker toggle is <strong>present</strong> but not finding much: make sure your content areas are listed in "Check content in these containers". It is not uncommon for themes to insert editable content outside the <code>main</code> element.</li></ol>
					</p>

						<h2 class="postbox-heading">Getting help</h3>
						<ul>
							<li><a href="https://wordpress.org/plugins/editoria11y-accessibility-checker/">Editoria11y WordPress Plugin Documentation</a></li>
							<li>
								<a href="https://editoria11y.princeton.edu">Editoria11y Library Documentation</a>
							</li>
							<li>
								<a href="https://github.com/itmaybejj/editoria11y-wp/issues">Issues &amp; feature requests</a><br><br>
								<span style="font-size: .9em;">Version: <?php echo ( esc_html( Editoria11y::ED11Y_VERSION ) ); ?></span>
							</li>
						</ul>

					</div>
				</div>
			</div><!-- .postbox-container -->

			</div><!-- .editoria11y-settings -->
			<br class="clear">
		</div>
	</div>
	<?php
}

/**
 * Validates/sanitizes the plugins settings after they've been submitted.
 *
 * @param string $settings To validate.
 */
function ed11y_plugin_settings_validate( $settings ) {

	/* Deep cleaning to help with error handling and security */
	$remove        = array(
		'&lt;'     => '',
		'&apos;'   => '',
		'&amp;'    => '',
		'&percnt;' => '',
		'&#96;'    => '',
		'`'        => '',
	);
	$remove_extra  = array(
		'&gt;' => '',
		'>'    => '',
	);
	$target_remove = array_merge( $remove, $remove_extra );

	$settings['ed11y_checkRoots'] = strtr(
		sanitize_text_field( $settings['ed11y_checkRoots'] ),
		$target_remove
	);

	/* Exclusions */
	$settings['ed11y_ignore_elements'] = strtr(
		sanitize_text_field( $settings['ed11y_ignore_elements'] ),
		$remove
	);

	$settings['ed11y_link_ignore_strings'] = strtr(
		sanitize_text_field( $settings['ed11y_link_ignore_strings'] ),
		$remove
	);

	$settings['ed11y_custom_tests'] = strtr(
		sanitize_text_field( $settings['ed11y_custom_tests'] ),
		$target_remove
	);

	/* Don't run Editoria11y */
	$settings['ed11y_no_run'] = strtr(
		sanitize_text_field( $settings['ed11y_no_run'] ),
		$target_remove
	);

	// Allowed characters: , . : empty space.
	$special_chars = '/[^.,:a-zA-Z0-9 ]/';

	$settings['ed11y_livecheck'] = preg_replace(
		$special_chars,
		'',
		sanitize_text_field( $settings['ed11y_livecheck'] )
	);
	$settings['ed11y_theme']     = preg_replace(
		$special_chars,
		'',
		sanitize_text_field( $settings['ed11y_theme'] )
	);

	/* Video */
	$settings['ed11y_videoContent'] = preg_replace(
		$special_chars,
		'',
		sanitize_text_field( $settings['ed11y_videoContent'] )
	);

	/* Audio */
	$settings['ed11y_audioContent'] = preg_replace(
		$special_chars,
		'',
		sanitize_text_field( $settings['ed11y_audioContent'] )
	);

	/* Document */
	$settings['ed11y_documentContent'] = preg_replace(
		$special_chars,
		'',
		sanitize_text_field( $settings['ed11y_documentContent'] )
	);

	/* Data Visualizations */
	$settings['ed11y_datavizContent'] = preg_replace(
		$special_chars,
		'',
		sanitize_text_field( $settings['ed11y_datavizContent'] )
	);

	// Reset cache.
	delete_site_transient( 'editoria11y_settings' );

	return $settings;
}

/**
 * Render the plugin settings page.
 */
function editoria11y_dashboard() {
	wp_enqueue_script( 'editoria11y-js', trailingslashit( ED11Y_ASSETS ) . 'lib/editoria11y.min.js', array( 'wp-api' ), true, Editoria11y::ED11Y_VERSION, false );
	wp_enqueue_script( 'editoria11y-js-dash', trailingslashit( ED11Y_ASSETS ) . 'js/editoria11y-dashboard.js', array( 'wp-api' ), true, Editoria11y::ED11Y_VERSION, false );
	wp_enqueue_style( 'editoria11y-css', trailingslashit( ED11Y_ASSETS ) . 'css/editoria11y-dashboard.css', null, Editoria11y::ED11Y_VERSION );
	$nonce = wp_create_nonce( 'ed1ref' );
	echo '<div id="ed1">
			<h1>Editoria11y accessibility checker</h1>
			<div id="ed1-page-wrapper"></div>
			<div id="ed1-results-wrapper"></div>
			<div id="ed1-dismissals-wrapper"></div>
		</div>
		<script id="editoria11y-nonce" type="application/json">
			' . wp_json_encode( $nonce ) . '
		</script>';
}

add_action( 'admin_menu', 'ed11y_dashboard_menu' );
/**
 * Add Editoria11y dashboard to admin sidebar menu.
 */
function ed11y_dashboard_menu() {
	$setting    = ed11y_get_plugin_settings( 'ed11y_report_restrict' );
	$capability = '1' === $setting ? 'manage_options' : 'edit_others_posts';
	add_menu_page( esc_html__( 'Editoria11y', 'editoria11y' ), esc_html__( 'Editoria11y', 'editoria11y' ), $capability, ED11Y_SRC . 'admin.php', 'editoria11y_dashboard', 'dashicons-chart-bar', 90 );
}

/**
 * Returns array of nicenames for test result keys.
 */
function ed11y_test_nice_names() {
	$tests                                = array();
	$tests['headingLevelSkipped']         = __( 'Manual check: was a heading level skipped?', 'editoria11y' );
	$tests['headingEmpty']                = __( 'Heading tag without any text', 'editoria11y' );
	$tests['headingIsLong']               = __( 'Manual check: long heading', 'editoria11y' );
	$tests['blockQuoteIsShort']           = __( 'Manual check: is this a blockquote?', 'editoria11y' );
	$tests['altMissing']                  = __( 'Image has no alternative text attribute', 'editoria11y' );
	$tests['altNull']                     = __( 'Manual check: image has no alt text', 'editoria11y' );
	$tests['altURL']                      = __( "Image's text alternative is a URL", 'editoria11y' );
	$tests['alURLLinked']                 = __( "Linked image's text alternative is a URL", 'editoria11y' );
	$tests['altImageOf']                  = __( 'Manual check: possibly redundant text in alt', 'editoria11y' );
	$tests['altImageOfLinked']            = __( 'Manual check: possibly redundant text in linked image', 'editoria11y' );
	$tests['altDeadspace']                = __( "Image's text alternative is unpronounceable", 'editoria11y' );
	$tests['altDeadspaceLinked']          = __( "Linked Image's text alternative is unpronounceable", 'editoria11y' );
	$tests['altEmptyLinked']              = __( 'Linked Image has no alt text', 'editoria11y' );
	$tests['altLong']                     = __( 'Manual check: very long alternative text', 'editoria11y' );
	$tests['altLongLinked']               = __( 'Manual check: very long alternative text in linked image', 'editoria11y' );
	$tests['altPartOfLinkWithText']       = __( 'Manual check: link contains both text and an image', 'editoria11y' );
	$tests['linkNoText']                  = __( 'Link with no accessible text', 'editoria11y' );
	$tests['linkTextIsURL']               = __( 'Manual check: is this link text a URL?', 'editoria11y' );
	$tests['linkTextIsGeneric']           = __( 'Manual check: is this link meaningful and concise?', 'editoria11y' );
	$tests['linkDocument']                = __( 'Manual check: is the linked document accessible?', 'editoria11y' );
	$tests['linkNewWindow']               = __( 'Manual check: is opening a new window expected?', 'editoria11y' );
	$tests['tableNoHeaderCells']          = __( 'Table has no header cells', 'editoria11y' );
	$tests['tableContainsContentHeading'] = __( 'Content heading inside a table', 'editoria11y' );
	$tests['tableEmptyHeaderCell']        = __( 'Empty table header cell', 'editoria11y' );
	$tests['textPossibleList']            = __( 'Manual check: should this have list formatting?', 'editoria11y' );
	$tests['textPossibleHeading']         = __( 'Manual check: should this be a heading?', 'editoria11y' );
	$tests['textUppercase']               = __( 'Manual check: is this uppercase text needed?', 'editoria11y' );
	$tests['embedVideo']                  = __( 'Manual check: is this video accurately captioned?', 'editoria11y' );
	$tests['embedAudio']                  = __( 'Manual check: is an accurate transcript provided?', 'editoria11y' );
	$tests['embedVisualization']          = __( 'Manual check: is this visualization accessible?', 'editoria11y' );
	$tests['embedTwitter']                = __( 'Manual check: is this embed a keyboard trap?', 'editoria11y' );
	$tests['embedCustom']                 = __( 'Manual check: is this embedded content accessible?', 'editoria11y' );
	return $tests;
}

/**
 * Returns a CSV download of site results.
 */
function ed11y_export_results_csv() {
	if ( isset( $_GET['ed11y_export_results_csv'] ) && isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'ed1ref' ) ) { // phpcs:ignore
		$setting    = ed11y_get_plugin_settings( 'ed11y_report_restrict' );
		$capability = '1' === $setting ? 'manage_options' : 'edit_others_posts';
		if ( ! current_user_can( $capability ) ) {
			return;
		}
		$test_name = ed11y_test_nice_names();

		header( 'Content-type: text/csv' );
		header( 'Content-Disposition: attachment; filename="wp-posts.csv"' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$file = fopen( 'php://output', 'w' );

		global $wpdb;
		$utable = $wpdb->prefix . 'ed11y_urls';
		$rtable = $wpdb->prefix . 'ed11y_results';

		/*
		Complex counts and joins required a direct DB call.
		Variables are all validated or sanitized.
		*/
		// phpcs:disable
		$data = $wpdb->get_results(
			"SELECT
				{$utable}.pid,
				{$utable}.page_url,
				{$utable}.page_title,
				{$utable}.entity_type,
				{$utable}.page_total,
				SUM({$rtable}.result_count) AS count,
				{$rtable}.result_key,
			MAX({$rtable}.created) as created
			FROM {$rtable}
			INNER JOIN {$utable} ON {$rtable}.pid={$utable}.pid
			GROUP BY {$utable}.pid,
				{$utable}.page_url,
				{$utable}.page_title,
				{$utable}.entity_type,
				{$utable}.page_total,
				{$rtable}.created
			ORDER BY count DESC;"
		);
		// phpcs:enable

		fputcsv( $file, array( 'Count', 'Issue', 'URL', 'Page', 'Type', 'Detected on' ) );

		foreach ( $data as $result ) {
			fputcsv( $file, array( $result->count, $test_name[ $result->result_key ], $result->page_url, $result->page_title, $result->entity_type, $result->created ) );
		}

		exit();

	}
}

add_action( 'admin_init', 'ed11y_export_results_csv' );
