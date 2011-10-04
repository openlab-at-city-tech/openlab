<?php
	/* REQUIRE THE CORE CLASS */
	require_once ( 'vigilance-admin.php' );
	/*
		Class Definition
	*/
	if (!class_exists( 'Vigilance' )) {
		class Vigilance extends JestroCore {

			/* PHP4 Constructor */
			function Vigilance () {

				/* SET UP THEME SPECIFIC VARIABLES */
				$this->themename = "Vigilance";
				$this->themeurl = "http://thethemefoundry.com/vigilance/";
				$this->shortname = "V";
				/*
					OPTION TYPES:
					- checkbox: name, id, desc, std, type
					- radio: name, id, desc, std, type, options
					- text: name, id, desc, std, type
					- colorpicker: name, id, desc, std, type
					- select: name, id, desc, std, type, options
					- textarea: name, id, desc, std, type, options
				*/
				$this->options = array(

					array(
						"name" => __( 'Navigation <span>control your top navigation menu</span>', 'vigilance' ),
						"type" => "subhead"),

					array(
						"name" => __( 'Hide all pages', 'vigilance' ),
						"id" => $this->shortname."_hide_pages",
						"desc" => __( 'Check this box to hide all pages.', 'vigilance' ),
						"std" => '',
						"type" => "checkbox"),

					array(
						"name" => __( 'Hide all categories', 'vigilance' ),
						"id" => $this->shortname."_hide_cats",
						"desc" => __( 'Check this box to hide all categories.', 'vigilance' ),
						"std" => '',
						"type" => "checkbox"),

					array(
						"name" => __( 'Color Scheme <span>customize your color scheme</span>', 'vigilance' ),
						"type" => "subhead"),

					array(
						"name" => __( 'Customize colors', 'vigilance' ),
						"id" => $this->shortname."_background_css",
						"desc" => __( 'If enabled your theme will use the layouts and colors you choose below.', 'vigilance' ),
						"std" => "disabled",
						"type" => "select",
						"options" => array(
							"disabled" => __( 'Disabled', 'vigilance' ),
							"enabled"	 =>	 __( 'Enabled', 'vigilance' ) ) ),

					array(
						"name" => __( 'Border color', 'vigilance' ),
						"id" => $this->shortname."_border_color",
						"desc" => __( 'Use hex values and be sure to include the leading #.', 'vigilance' ),
						"std" => "#9a927f",
						"type" => "colorpicker"),

					array(
						"name" => __( 'Link color', 'vigilance' ),
						"id" => $this->shortname."_link_color",
						"desc" => __( 'Use hex values and be sure to include the leading #.', 'vigilance' ),
						"std" => "#772124",
						"type" => "colorpicker"),

					array(
						"name" => __( 'Link hover color', 'vigilance' ),
						"id" => $this->shortname."_hover_color",
						"desc" => __( 'Use hex values and be sure to include the leading #.', 'vigilance' ),
						"std" => "#58181b",
						"type" => "colorpicker"),

					array(
						"name" => __( 'Disable hover background images', 'vigilance' ),
						"id" => $this->shortname."_image_hover",
						"desc" => __( 'Check this box if you use custom link colors and do not want the default red showing when a user hovers over the comments bubble or the sidebar menu items.', 'vigilance' ),
						"std" => "false",
						"type" => "checkbox"),

					array(
						"name" => __( 'Top Banner Image <span>control your top banner image state</span>', 'vigilance' ),
						"type" => "subhead"),

					array(
						"name" => __( 'Banner state', 'vigilance' ),
						"id" => $this->shortname."_banner_state",
						"desc" => __( sprintf( __( 'Add your images to the top banner rotation by uploading them to the %s directory.' ), '<code>' . STYLESHEETPATH . '/images/top-banner/</code>' ), 'vigilance' ),
						"std" => "hide",
						"type" => "select",
						"options" => array(
							"rotate" => __( 'Rotating images', 'vigilance' ),
							"static" => __( 'Static image', 'vigilance' ),
							"specific" => __( 'Page or post specific', 'vigilance' ),
							"custom" => __( 'Custom code', 'vigilance' ),
							"hide" => __( 'Do not show an image', 'vigilance' ) ) ),

					array(
						"name" => __( 'Banner height', 'vigilance' ),
						"id" => $this->shortname."_banner_height",
						"desc" => __( 'The height of your image. The width is fixed at 596px.', 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Banner <code>&lt;alt&gt;</code> tag', 'vigilance' ),
						"id" => $this->shortname."_banner_alt",
						"desc" => __( 'Specify the <code>&lt;alt&gt;</code> tag for your banner image(s). Will default to your blog title if left blank.', 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Static image name', 'vigilance' ),
						"id" => $this->shortname."_banner_url",
						"desc" => __( sprintf( __( 'Set the <em>Banner State</em> to "Static Image" and upload your image to the %s directory.' ), '<code>' . STYLESHEETPATH . '/images/top-banner/</code>' ), 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Home image name', 'vigilance' ),
						"id" => $this->shortname."_banner_home",
						"desc" => __( sprintf( __( 'To replace your home top banner with a specific image upload it to the %s directory.' ), '<code>' . STYLESHEETPATH . '/images/top-banner/</code>' ), 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Custom code', 'vigilance' ),
						"id" => $this->shortname."_banner_custom",
						"desc" => __( 'Replace your top banner with custom code. The <em>Banner State</em> must be set to "Custom code" for this to work.', 'vigilance' ),
						"std" => '',
						"type" => "textarea",
						"options" => array(
							"rows" => "5",
							"cols" => "40") ),

					array(
						"name" => __( 'Sidebar Image <span>control your sidebar image state</span>', 'vigilance' ),
						"type" => "subhead"),

					array(
						"name" => __( 'Image state', 'vigilance' ),
						"desc" => __( sprintf( __( 'Add your images to the sidebar rotation by uploading them to the %s directory.' ), '<code>' . STYLESHEETPATH . '/images/sidebar/</code>' ), 'vigilance' ),
						"id" => $this->shortname."_sideimg_state",
						"std" => "hide",
						"type" => "select",
						"options" => array(
							"rotate" => __( 'Rotating images', 'vigilance' ),
							"static" => __( 'Static image', 'vigilance' ),
							"custom" => __( 'Custom code', 'vigilance' ),
							"specific" => __( 'Page or post specific', 'vigilance' ),
							"hide" => __( 'Do not show an image', 'vigilance' ) ) ),

					array(
						"name" => __( 'Image height', 'vigilance' ),
						"id" => $this->shortname."_sideimg_height",
						"desc" => __( 'The height of your image. The width is fixed at 300px.', 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Image <code>&lt;alt&gt;</code> tag', 'vigilance' ),
						"id" => $this->shortname."_sideimg_alt",
						"desc" => __( sprintf( __( 'The %s tag for your sidebar image(s). Will default to your blog title if left blank.' ), '<code>&lt;alt&gt;</code>' ), 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Static image', 'vigilance' ),
						"id" => $this->shortname."_sideimg_url",
						"desc" => __( sprintf( __( 'Set the <em>Image State</em> to "Static Image" and upload your image to the %s directory.' ), '<code>' . STYLESHEETPATH . '/images/sidebar/</code>'), 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Image link', 'vigilance' ),
						"id" => $this->shortname."_sideimg_link",
						"desc" => __( 'Define a hyperlink for your sidebar image. If left empty the anchor tags will not be included.', 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Custom code', 'vigilance' ),
						"id" => $this->shortname."_sideimg_custom",
						"desc" => __( 'Replace your sidebar image with custom code. The <em>Image State</em> must be set to "Custom code" for this to work.', 'vigilance' ),
						"std" => '',
						"type" => "textarea",
						"options" => array(
							"rows" => "5",
							"cols" => "40") ),

					array(
						"name" => __( 'Sidebar Feed Box <span>share feeds and updates with your readers</span>', 'vigilance' ),
						"type" => "subhead"),

					array(
						"name" => __( 'Feed box state', 'vigilance' ),
						"desc" => __( 'Enable or disable the feed box located in the sidebar.', 'vigilance' ),
						"id" => $this->shortname."_feed_state",
						"std" => "enabled",
						"type" => "select",
						"options" => array(
							"disabled" => __( 'Disabled', 'vigilance' ),
							"enabled" => __( 'Enabled', 'vigilance' ))),

					array(
						"name" => __( 'Feed box title text', 'vigilance' ),
						"id" => $this->shortname."_feed_title",
						"desc" => __( 'Header for your feed box.', 'vigilance' ),
						"std" => __( 'Get Free Updates', 'vigilance' ),
						"type" => "text"),

					array(
						"name" => __( 'Feed box intro text', 'vigilance' ),
						"id" => $this->shortname."_feed_intro",
						"desc" => __( 'Enter your feed intro text here.', 'vigilance' ),
						"std" => __( 'Get the latest and the greatest news delivered for free to your reader or your inbox:', 'vigilance' ),
						"type" => "textarea",
						"options" => array(
							"rows" => "5",
							"cols" => "40") ),

					array(
						"name" => __( '<a href="http://feedburner.google.com/">Feedburner</a> email updates link', 'vigilance' ),
						"id" => $this->shortname."_feed_email",
						"desc" => __( 'Enter your feed email link here. Do not paste the provided link code, extract and paste the URL only.', 'vigilance' ),
						"std" => "http://feedburner.google.com/fb/a/mailverify?uri=YOURFEEDID&loc=en_US",
						"type" => "textarea",
						"options" => array(
							"rows" => "2",
							"cols" => "80") ),

					array(
						"name" => __( 'Footer <span>add a copyright notice and tracking codes</span>', 'vigilance' ),
						"type" => "subhead"),

					array(
						"name" => __( 'Copyright notice', 'vigilance' ),
						"id" => $this->shortname."_copyright_name",
						"desc" => __( 'Your name or the name of your business.', 'vigilance' ),
						"std" => '',
						"type" => "text"),

					array(
						"name" => __( 'Stats code', 'vigilance' ),
						"id" => $this->shortname."_stats_code",
						"desc" => __( sprintf( __( 'If you would like to use Google Analytics or any other tracking script in your footer just paste it here. The script will be inserted before the closing %s tag.' ), '<code>&#60;/body&#62;</code>' ), 'vigilance' ),
						"std" => '',
						"type" => "textarea",
						"options" => array(
							"rows" => "5",
							"cols" => "40") ),
				);
				parent::JestroCore();

				add_filter( 'wp_head', array( &$this, 'addCssStyles' ) );
			}

			/*
				ALL OF THE FUNCTIONS BELOW
				ARE BASED ON THE OPTIONS ABOVE
				EVERY OPTION SHOULD HAVE A FUNCTION

				THESE FUNCTIONS CURRENTLY JUST
				RETURN THE OPTION, BUT COULD BE
				REWRITTEN TO RETURN DIFFERENT DATA
			*/

			/* LOGO FUNCTIONS */
			function logoState () {
				return get_option($this->shortname.'_logo' );
			}
			function logoName () {
				return get_option($this->shortname.'_logo_img' );
			}
			function logoAlt () {
				return get_option($this->shortname.'_logo_img_alt' );
			}
			function logoTagline () {
				return get_option($this->shortname.'_tagline' );
			}

			/* NAVIGATION FUNCTIONS */
			function excludedPages () {
				return get_option($this->shortname.'_pages_to_exclude' );
			}
			function excludedCategories () {
				return get_option($this->shortname.'_categories_to_exclude' );
			}
			function hidePages () {
				return get_option($this->shortname.'_hide_pages' );
			}
			function hideCategories () {
				return get_option($this->shortname.'_hide_cats' );
			}
			function hideHome () {
				return get_option($this->shortname.'_hide_home' );
			}

			/* FOOTER FUNCTIONS */
			function copyrightName() {
				return wp_filter_post_kses(get_option($this->shortname.'_copyright_name' ));
			}
			function statsCode() {
				return stripslashes(get_option($this->shortname.'_stats_code' ));
			}

			/* ALERTBOX FUNCTIONS */
			function alertboxState() {
				return get_option($this->shortname.'_alertbox_state' );
			}
			function alertboxTitle() {
				return stripslashes(wp_filter_post_kses(get_option($this->shortname.'_alertbox_title' )));
			}
			function alertboxContent() {
				return stripslashes(wp_filter_post_kses(wpautop(get_option($this->shortname.'_alertbox_content' ))));
			}

			/* BANNER FUNCTIONS */
			function bannerState() {
				return get_option($this->shortname.'_banner_state' );
			}
			function BannerHeight() {
				return wp_filter_post_kses(get_option($this->shortname.'_banner_height' ));
			}
			function bannerAlt() {
				return get_option($this->shortname.'_banner_alt' );
			}
			function bannerUrl() {
				return wp_filter_post_kses(get_option($this->shortname.'_banner_url' ));
			}
			function bannerHome() {
				return stripslashes(wp_filter_post_kses(get_option($this->shortname.'_banner_home' )));
			}
			function bannerCustom() {
				return stripslashes(get_option($this->shortname.'_banner_custom' ));
			}

			/* FEED FUNCTIONS */
			function feedState() {
				return get_option($this->shortname.'_feed_state' );
			}
			function feedTitle() {
				return stripslashes(wp_filter_post_kses(get_option($this->shortname.'_feed_title' )));
			}
			function feedIntro() {
				return stripslashes(wp_filter_post_kses(get_option($this->shortname.'_feed_intro' )));
			}
			function feedEmail() {
				return htmlspecialchars(wp_filter_post_kses(get_option($this->shortname.'_feed_email')), UTF-8);
			}

			/* TWITTER FUNCTIONS */
			function twitter() {
				return htmlspecialchars(wp_filter_post_kses(get_option($this->shortname.'_twitter')), UTF-8);
			}
			function twitterToggle() {
				return get_option($this->shortname.'_twitter_toggle' );
			}

			/* SIDE IMAGE FUNCTIONS */
			function sideimgState() {
				return get_option($this->shortname.'_sideimg_state' );
			}
			function sideimgHeight() {
				return wp_filter_post_kses(get_option($this->shortname.'_sideimg_height' ));
			}
			function sideimgAlt() {
				return stripslashes(wp_filter_post_kses(get_option($this->shortname.'_sideimg_alt' )));
			}
			function sideimgUrl() {
				return wp_filter_post_kses(get_option($this->shortname.'_sideimg_url' ));
			}
			function sideimgLink() {
				return wp_filter_post_kses(get_option($this->shortname.'_sideimg_link' ));
			}
			function sideimgCustom() {
				return	stripslashes(get_option($this->shortname.'_sideimg_custom' ));
			}

			/* CSS FUNCTIONS */
			function backgroundCss() {
				return get_option($this->shortname.'_background_css' );
			}
			function backgroundColor() {
				return "#a39c8a";
			}
			function borderColor() {
				return get_option($this->shortname.'_border_color' );
			}
			function linkColor() {
				return get_option($this->shortname.'_link_color' );
			}
			function hoverColor() {
				return get_option($this->shortname.'_hover_color' );
			}
			function imageHover() {
				return get_option($this->shortname.'_image_hover' );
			}
			function addCssStyles() {
				if ( $this->backgroundCss() == 'enabled' || $this->imageHover() == 'true' ) :
				?>
				<style type="text/css">
					<?php if ($this->backgroundCss() == 'enabled' ) : ?>
					/*Background
					------------------------------------------------------------ */
					body { background-color: <?php echo $this->backgroundColor(); ?>; }
					#wrapper {
						background: #fff;
						padding: 0 20px 10px 20px;
						border-left: 4px solid <?php echo $this->borderColor(); ?>;
						border-right: 4px solid <?php echo $this->borderColor(); ?>;
					}
					/*Links
					------------------------------------------------------------ */
					#content a:link, #content a:visited { color: <?php echo $this->linkColor(); ?>; }
					#sidebar a:link, #sidebar a:visited { color: <?php echo $this->linkColor(); ?>; }
					h1#title a:hover, div#title a:hover { color: <?php echo $this->linkColor(); ?>; }
					#nav li.current_page_item a, #nav li.current_page_parent a, #nav li.current_page_ancestor a, #nav li.current-cat a, #nav li a:hover {
						color: <?php echo $this->linkColor(); ?>;
						border-top: 4px solid <?php echo $this->linkColor(); ?>;
					}
					.post-header h1 a:hover, .post-header h2 a:hover { color: <?php echo $this->linkColor(); ?>; }
					.comments a:hover { color: <?php echo $this->linkColor(); ?>; }
					.meta a:hover { color: <?php echo $this->linkColor(); ?>; }
					.highlight-box { background: <?php echo $this->linkColor(); ?>;	}
					.post-footer a:hover { color: <?php echo $this->linkColor(); ?>; }
					#footer a:hover { color: <?php echo $this->linkColor(); ?>; }
					/*Hover
					------------------------------------------------------------ */
					#content .entry a:hover { color: <?php echo $this->hoverColor(); ?>; }
					#wrapper #sidebar a:hover { color: <?php echo $this->hoverColor(); ?>; }
					/*Reset Specific Link Colors
					------------------------------------------------------------ */
					#content .post-header h1 a:link, #content .post-header h1 a:visited, #content .post-header h2 a:link, #content .post-header h2 a:visited { color: #444; }
					#content .post-header h1 a:hover, #content .post-header h2 a:hover { color: <?php echo $this->linkColor(); ?>; }
					#content .comments a { color: #757575; }
					#content .comments a:hover { color: <?php echo $this->linkColor(); ?>; }
					#content .meta a:link, #content .meta a:visited { color: #666; }
					#content .meta a:hover { color: <?php echo $this->linkColor(); ?>; }
					#content .post-footer a:link, #content .post-footer a:visited { color: #333; }
					#content .c-permalink a:link, #content .c-permalink a:visited { color: #ccc; }
					#content .reply a:link, #reply .c-permalink a:visited { color: #aaa; }
					#content .reply a:hover { color: <?php echo $this->linkColor(); ?>; }
					#footer a:link, #footer a:visited { color: #666; }
					#footer a:hover { color: <?php echo $this->linkColor(); ?>; }
				<?php endif; ?>
				<?php if ($this->imageHover() == 'true' ) : ?>
					/*Hide hover colors on comment images and sidebar menu images
					------------------------------------------------------------ */
					.comments a:hover { background-position: 0 4px; }
					ul li.widget ul li a:hover { background-position: 0 6px; }
				<?php endif; ?>

				</style>
				<?php
				endif;
			}
		}
	}
	/* SETTING EVERYTHING IN MOTION */
	if (class_exists( 'Vigilance' )) {
		$vigilance = new Vigilance();
	}

?>