<?php

/**
 * 
 */
class YotuViews{

	public $sections = array();
	
	public function __construct()
	{
		global $yotuwp;

		$sections = array();

		$templates = array(
			'grid' => __('Grid', 'yotuwp-easy-youtube-embed'),
			'list' => __('List', 'yotuwp-easy-youtube-embed'),
			'mix'  => __('Mix', 'yotuwp-easy-youtube-embed'),
		);

		$templates = apply_filters( 'yotuwp_templates', $templates );
		//Setting general
		$sections['settings'] = array(
			'icon' 		=> 'dashicons-admin-generic',
			'key' 		=> 'settings',
			'title' 	=> __('General', 'yotuwp-easy-youtube-embed'),
			'priority' 	=> 10,
			'fields' => array(
				array(
					'name'			=> 'template',
					'type' 			=> 'select',
					'label'			=> __('Videos Layout Template', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'grid',
					'priority' 		=> 10,
					'description'	=> __('Layout for display videos.', 'yotuwp-easy-youtube-embed'),
					'options' 		=> $templates,
					'extbtn'		=> '<a href="https://www.yotuwp.com/pro/" target="_blank" class="extra-btn"><span class="dashicons dashicons-arrow-right-alt"></span>Get More Layouts</a>'
				),
				array(
					'name'			=> 'column',
					'type'			=> 'select',
					'label'			=> __('Columns', 'yotuwp-easy-youtube-embed'),
					'default'		=> '3',
					'priority' 		=> 20,
					'description'	=> __('The number columns of videos on Grid and Mix layout mode.', 'yotuwp-easy-youtube-embed'),
					'options' => array(
						'1' => '1 column',
						'2' => '2 columns',
						'3' => '3 columns',
						'4' => '4 columns',
						'5' => '5 columns',
						'6' => '6 columns'
					)
				),
				array(
					'name'			=> 'per_page',
					'type'			=> 'text',
					'label'			=> __('Videos per page', 'yotuwp-easy-youtube-embed'),
					'default'		=> '12',
					'priority' 		=> 30,
					'description'	=> __('The limit number videos per page.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'pagination',
					'type'			=> 'toggle',
					'label'			=> __('Pagination?', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 40,
					'description'	=> __('The pagination for reaching more videos on list.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'pagitype',
					'type'			=> 'select',
					'label'			=> __('Pagination type', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'pager',
					'priority' 		=> 50,
					'description'	=> __('The type display and loading of pagination. Pager display next/preve button and current page. Load more displays one button on bottom. Default: pager', 'yotuwp-easy-youtube-embed'),
					'options' => array(
						'pager' => 'Pager',
						'loadmore' => 'Load More'
					)
				),
				array(
					'name'			=> 'title',
					'type'			=> 'toggle',
					'label'			=> __('Videos Title?', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 60,
					'description'	=> __('Display video title on listing.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'description',
					'type'			=> 'toggle',
					'label'			=> __('Videos Description?', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 70,
					'description'	=> __('Display video description on listing.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'thumbratio',
					'type'			=> 'select',
					'label'			=> __('Video Thumbnail Ratio', 'yotuwp-easy-youtube-embed'),
					'default'		=> '43',
					'priority' 		=> 59,
					'description'	=> __('Change video thumnail ratio to remove top and bottom bar.', 'yotuwp-easy-youtube-embed'),
					'options' => array(
						'43'  => 'Normal - 4:3',
						'169' => 'HD - 16:9'
					)
				)
				
			)
		);

		//Player settings
		$sections['player'] = array(
			'icon' 		=> 'dashicons-video-alt3',
			'key' 		=> 'player',
			'title' 	=> __('Player', 'yotuwp-easy-youtube-embed'),
			'priority' 	=> 20,
			'fields' => array(
				array(
					'name'			=> 'mode',
					'type' 			=> 'select',
					'label'			=> __('Player Mode', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'large',
					'priority' 		=> 10,
					'description'	=> __('Layout for video player.', 'yotuwp-easy-youtube-embed'),
					'options' 		=> array(
						'large' => __('Large', 'yotuwp-easy-youtube-embed'),
						'popup' => __('Popup', 'yotuwp-easy-youtube-embed')
					)
				),
				array(
					'name'			=> 'width',
					'type'			=> 'text',
					'priority' 		=> 20,
					'label'			=> __('Player width', 'yotuwp-easy-youtube-embed'),
					'default'		=> '600',
					'description'	=> __('The default width of player. Set 0 to use full container width player. Default : 600(px)', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'scrolling',
					'type'			=> 'text',
					'priority' 		=> 30,
					'label'			=> __('Scrolling Offset', 'yotuwp-easy-youtube-embed'),
					'default'		=> '100',
					'description'	=> __('The distance betwen top browser with player when play a video. Set 0 for auto center player in screen. Default : 100(px)', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'playing',
					'type'			=> 'toggle',
					'label'			=> __('Playing Title', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'off',
					'priority' 		=> 40,
					'description'	=> __('Show title playing video on top of player. Default disabled.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'playing_description',
					'type'			=> 'toggle',
					'label'			=> __('Playing Description', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'off',
					'priority' 		=> 50,
					'description'	=> __('Show description playing video at bottom of player. Default disabled.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'controls',
					'type'			=> 'toggle',
					'label'			=> __('Controls', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 60,
					'description'	=> __('This parameter indicates whether the video player controls are displayed', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'autoplay',
					'type'			=> 'toggle',
					'label'			=> __('Auto play', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 70,
					'description'	=> __('This parameter specifies whether the initial video will automatically start to play when the player loads.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'autonext',
					'type'			=> 'toggle',
					'label'			=> __('Auto Next Video', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'off',
					'priority' 		=> 71,
					'description'	=> __('Enable play next video in list automatically after previous one end.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'rel',
					'type'			=> 'toggle',
					'label'			=> __('Related Videos', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 80,
					'description'	=> __('This parameter indicates whether the player should show related videos when playback of the initial video ends.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'loop',
					'type'			=> 'toggle',
					'label'			=> __('Loop', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 90,
					'description'	=> __('In the case of a single video player, enable this for the player to play the initial video again and again.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'modestbranding',
					'type'			=> 'toggle',
					'label'			=> __('Branding logo', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 100,
					'description'	=> __('Display your brand logo from displaying in the control bar. This option will remove YouTube logo as well.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'showinfo',
					'type'			=> 'toggle',
					'label'			=> __('Show info', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 110,
					'description'	=> __('Enable information like the video title and uploader before the video starts playing.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'thumbnails',
					'type'			=> 'toggle',
					'label'			=> __('Modal Thumbnails', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'on',
					'priority' 		=> 120,
					'description'	=> __('Display list of videos on Modal popup player.', 'yotuwp-easy-youtube-embed'),
				),

				array(
					'name'			=> 'cc_load_policy',
					'type'			=> 'select',
					'label'			=> __('Force Closed Captions', 'yotuwp-easy-youtube-embed'),
					'default'		=> '0',
					'priority' 		=> 130,
					'description'	=> __('Enable closed captions for video. .', 'yotuwp-easy-youtube-embed'),
					'options' 		=> array(
						'0' => __('No', 'yotuwp-easy-youtube-embed'),
						'1' => __('Yes', 'yotuwp-easy-youtube-embed')
					)
				),
				array(
					'name'			=> 'iv_load_policy',
					'type'			=> 'select',
					'label'			=> __('Show annotations', 'yotuwp-easy-youtube-embed'),
					'default'		=> '1',
					'priority' 		=> 135,
					'description'	=> __('Choose whether to show annotations or not', 'yotuwp-easy-youtube-embed'),
					'options' 		=> array(
						'3' => __('No', 'yotuwp-easy-youtube-embed'),
						'1' => __('Yes', 'yotuwp-easy-youtube-embed')
					)
				),
				
				array(
					'name'			=> 'hl',
					'type'			=> 'text',
					'priority' 		=> 140,
					'label'			=> __('Player Language', 'yotuwp-easy-youtube-embed'),
					'default'		=> '',
					'description'	=> sprintf(__('The language in interface of player. Default is English. The option value is an ISO 639-1 two-letter language code or a fully specified locale. You can get your language code from %s', 'yotuwp-easy-youtube-embed'), '<a href="http://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">this page</a>'),
				),

				array(
					'name'			=> 'cc_lang_pref',
					'type'			=> 'text',
					'priority' 		=> 145,
					'label'			=> __('Preferred captions language', 'yotuwp-easy-youtube-embed'),
					'default'		=> '',
					'description'	=> sprintf(__('Change preferred language for captions. The option value is an ISO 639-1 two-letter language code or a fully specified locale. You can get your language code from %s', 'yotuwp-easy-youtube-embed'), '<a href="http://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">this page</a>'),
				),
			)
		);

		$sections['styling'] = array(
			'icon' 		=> 'dashicons-admin-customizer',
			'key' 		=> 'styling',
			'title' 	=> __('Styling', 'yotuwp-easy-youtube-embed'),
			'priority' 	=> 30,
			'fields' => array(
				array(
					'name'			=> 'pager_layout',
					'type'			=> 'radios',
					'label'			=> __('Pager Layout', 'yotuwp-easy-youtube-embed'),
					'default'		=> 'default',
					'description'	=> __('The layout for pager. Select one of them to use.', 'yotuwp-easy-youtube-embed'),
					'priority' 		=> 20,
					'options' 		=> array(
						'default' 	=> array(
							'title' => __('Default', 'yotuwp-easy-youtube-embed'),
							'img' 	=> 'images/fields/pager_layout/default.png' 
						),
						'center_no_text' => array(
							'title' => __('Center No Text', 'yotuwp-easy-youtube-embed'),
							'img' 	=> 'images/fields/pager_layout/center_no_text.png' 
						),
						'bothside' 	=> array(
							'title' => __('Both Side', 'yotuwp-easy-youtube-embed'),
							'img' 	=> 'images/fields/pager_layout/bothside.png'
						),
						'bothside_no_text' => array(
							'title' => __('Both Side No Text', 'yotuwp-easy-youtube-embed'),
							'img' 	=> 'images/fields/pager_layout/bothside_no_text.png'
						),
					),
					
				),
				
				array(
					'name'			=> 'button',
					'type'			=> 'buttons',
					'label'			=> __('Button Style', 'yotuwp-easy-youtube-embed'),
					'default'		=> '10',
					'priority' 		=> 20,
					'class' 		=> 'noful',
					'description'	=> __('The styling for all buttons. Select one of them to using.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'button_color',
					'type'			=> 'color',
					'label'			=> __('Button Text Color', 'yotuwp-easy-youtube-embed'),
					'default'		=> '',
					'priority' 		=> 30,
					'description'	=> __('The color of text on button.', 'yotuwp-easy-youtube-embed'),
					'css' 			=> '.yotu-button-prs|color'
				),
				array(
					'name'			=> 'button_bg_color',
					'type'			=> 'color',
					'label'			=> __('Button Background Color', 'yotuwp-easy-youtube-embed'),
					'default'		=> '',
					'priority' 		=> 40,
					'css'			=> '.yotu-button-prs|background-color',
					'description'	=> __('The button background color.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'button_color_hover',
					'type'			=> 'color',
					'label'			=> __('Button Color Hover', 'yotuwp-easy-youtube-embed'),
					'default'		=> '',
					'priority' 		=> 50,
					'css'			=> '.yotu-button-prs:hover,.yotu-button-prs:focus|color',
					'description'	=> __('The color of text button on hover.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'button_bg_color_hover',
					'type'			=> 'color',
					'label'			=> __('Button Background Color Hover', 'yotuwp-easy-youtube-embed'),
					'default'		=> '',
					'priority' 		=> 60,
					'css'			=> '.yotu-button-prs:hover,.yotu-button-prs:focus|background-color',
					'description'	=> __('The background color of button on hover.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'gallery_bg',
					'type'			=> 'color',
					'label'			=> __('Gallery Background Color', 'yotuwp-easy-youtube-embed'),
					'default'		=> '',
					'priority' 		=> 70,
					'css'			=> 'body .yotuwp.yotu-playlist|background-color',
					'description'	=> __('The background color of gallery.', 'yotuwp-easy-youtube-embed'),
				),
				array(
					'name'			=> 'video_style',
					'type'			=> 'radios',
					'label'			=> __( 'Video Thumbnail', 'yotuwp-easy-youtube-embed' ),
					'default'		=> '',
					'priority' 		=> 11,
					'preset' 		=> true,
					'description'	=> __( 'Style for a video on list', 'yotuwp-easy-youtube-embed' ),
					'options'		=> array(
						'' 	=> array(
							'title' => __( 'Default', 'yotuwp-easy-youtube-embed' ),
							'img'   => ''
						),
						'gplus' 	=> array(
							'title' => __( 'Yooglus - Grid Only', 'yotuwp-easy-youtube-embed' ),
							'img'   => 'images/fields/video_style/yplus.jpg'
						),
						'abnb' 	=> array(
							'title' => __( 'Yabnb - Grid Only', 'yotuwp-easy-youtube-embed' ),
							'img'   => 'images/fields/video_style/yabnb.jpg'
						)
					),
					'extbtn'		=> '<a href="https://www.yotuwp.com/pro/" target="_blank" class="extra-btn"><span class="dashicons dashicons-arrow-right-alt"></span>Get More Styles</a>'
				)
			)
		);

		//effect 
		$sections['effects'] = array(
			'icon' 		=> 'dashicons-visibility',
			'key' 		=> 'effects',
			'title' 	=> __('Effects', 'yotuwp-pro'),
			'priority' 	=> 30,
			'pro'		=> true,
			'fields' 	=> array(
				array(
					'name'			=> 'video_box',
					'label'			=> __( 'Video Box Effect', 'yotuwp-pro' ),
					'type'			=> 'effects',
					'priority' 		=> 10,
					'default'		=> '',
					'description'	=> 'The hover effect on each video thumnails on gallery.',
				)
			)
		);

		$sections['premium'] = array(
			'icon' 		=> 'dashicons-awards',
			'key' 		=> 'premium',
			'title' 	=> __('Intro', 'yotuwp-pro'),
			'priority' 	=> 70,
			'pro'		=> true,
			'fields' 	=> array(
				array(
					'name'			=> 'intro',
					'label'			=> '',
					'type'			=> 'intro',
					'priority' 		=> 10,
					'default'		=> '',
					'description'	=> '',
				)
			)
		);

		$sections = apply_filters('yotu_settings', $sections, array());

		$this->sections = $sections;

	}
	
	public function settings( $data ) {
		global $yotuwp;

		$data = apply_filters('yotuwp_before_render_settings', $data);

		foreach ( $this->sections as $tab => $section ) {
			foreach( $section['fields'] as $ind => $field ) {
				$field_name = $field['name'];
				if( 
					isset( $data[ $tab ]) &&
					isset( $data[ $tab ][ $field_name ])
				) {
					$this->sections[ $tab ]['fields'][ $ind ]['value'] = $data[ $tab ][ $field_name ];
				}
			}
		}

		if ( !isset( $data['is_panel']) ) {
			unset($this->sections['premium']);
		}
		if ( !isset( $data['styling']) ) {
			unset($this->sections['styling']);
		}

		//API settings

		if ( isset( $data['api']) ) {
			$api = $data['api'];
			$allow_tracking = isset($api['tracking'])? $api['tracking'] : false;

			if ( get_option( 'yotuwp_allow_tracking', false )) {
				$allow_tracking = true;
			}
			
			$this->sections['api'] = array(
				'icon' 		=> 'dashicons-admin-network',
				'key' 		=> 'api',
				'title' 	=> __('API', 'yotuwp-easy-youtube-embed'),
				'priority' 	=> 60,
				'fields' 	=> array(
					array(
						'name'			=> 'api_key',
						'type'			=> 'text',
						'priority' 		=> 10,
						'label'			=> __('Youtube API Key', 'yotuwp-easy-youtube-embed'),
						'default'		=> '',
						'description'	=> sprintf(__('Follow %s to get your own YouTube API key', 'yotuwp-easy-youtube-embed'), '<a href="https://www.yotuwp.com/how-to-get-youtube-api-key/" target="_blank">this guide</a>'),
						'value'			=> isset($api['api_key'])? $api['api_key'] : '',
					),

					array(
						'name'			=> 'tracking',
						'type'			=> 'toggle',
						'label'			=> __('Enable Tracking?', 'yotuwp-easy-youtube-embed'),
						'default'		=> 'off',
						'priority' 		=> 60,
						'value'			=> ($allow_tracking)? 'on' : 'off',
						'description'	=> __('Allow <strong>YotuWP - YouTube Gallery</strong> to track plugin usage? Become a contributor by opting in to our anonymous data tracking. We guarantee no sensitive data is collected.', 'yotuwp-easy-youtube-embed'),
					),
				)
			);
		}

		//Cache settings
		if (isset( $data['cache'])) {
			$cache = $data['cache'];
			$this->sections['cache'] = array(
				'icon' => 'dashicons-dashboard',
				'key' => 'cache',
				'title' => __('Cache', 'yotuwp-easy-youtube-embed'),
				'priority' 	=> 50,
				'fields' => array(
					array(
						'name'			=> 'enable',
						'type'			=> 'toggle',
						'label'			=> __('Enable?', 'yotuwp-easy-youtube-embed'),
						'default'		=> 'off',
						'priority' 		=> 10,
						'description'	=> __('The cache to reduce time for loading videos. Give best experience to your readers.', 'yotuwp-easy-youtube-embed'),
						'value'			=> isset( $cache['enable']) ? $cache['enable'] : 'off'
					),
					array(
						'name'			=> 'timeout',
						'type'			=> 'select',
						'label'			=> __('Timeout', 'yotuwp-easy-youtube-embed'),
						'default'		=> 'weekly',
						'priority' 		=> 20,
						'description'	=> __('The time your cache removed after created to ensure your videos are fresh.', 'yotuwp-easy-youtube-embed'),
						'value'			=> $cache['timeout'],
						'options' => array(
							"weekly" 				=> "Once a Week",
							"everyminute" 			=> "Once Every 1 Minute",
							"everyfiveminute" 		=> "Once Every 5 Minutes",
							"everyfifteenminute" 	=> "Once Every 15 Minutes",
							"twiceanhour" 			=> "Twice an Hour",
							"onceanhour" 			=> "Once an Hour",
							"everytwohours" 		=> "Once Every 2 Hours",
							"everythreehours" 		=> "Once Every 3 Hours",
							"everyfourhours" 		=> "Once Every 4 Hours",
							"everyfivehours" 		=> "Once Every 5 Hours",
							"everysixhours" 		=> "Once Every 6 Hours",
							"everysevenhours" 		=> "Once Every 7 Hours",
							"everyeighthours" 		=> "Once Every 8 Hours",
							"everyninehours" 		=> "Once Every 9 Hours",
							"everytenhours" 		=> "Once Every 10 Hours",
							"onceaday" 				=> "Once a Day",
							"everythreedays" 		=> "Once Every 3 Days",
							"everytendays" 			=> "Once Every 10 Days",
							"montly" 				=> "Once a Month",
							"yearly" 				=> "Once a Year",
							"hourly" 				=> "Once Hourly",
							"twicedaily" 			=> "Twice Daily",
							"daily" 				=> "Once Daily"

						)
					),

					array(
						'name'			=> 'clearcache',
						'type'			=> 'button',
						'label'			=> __('Clear cache data', 'yotuwp-easy-youtube-embed'),
						'btn-label'		=> __('Delete Cache', 'yotuwp-easy-youtube-embed'),
						'priority' 		=> 30,
						'func' 		=> 'delete-cache',
						'description'	=> __('Delete all videos cache to get latest update from your channel/playlist.', 'yotuwp-easy-youtube-embed'),
					)
				)
			);
		}

		$this->render_tabs( $this->sections, ( isset( $data['is_panel'] )? true : false ) );
	}

	public static function sidebar() {
	?>
	<div class="yotu-sidebar">
		<div class="yotu-sidebar-box">
			<h2><?php _e('10x Audience Engagement', 'yotuwp-easy-youtube-embed');?></h2>
			<p>
				YotuWP has Pro version which comes with several additional benefits.
			</p>
			<ul>
				<li>Carousel Layout</li>
				<li>Masonry Layout</li>
				<li>Multiple video thumbnails styling</li>
				<li>Meta video data: View, Like, Comments, Published Date</li>
				<li>12+ Hover icons</li>
				<li>Custom text for buttons Next, Prev, Load more</li>
				<li>Color styling for title, description, meta data</li>
				<li>And more…</li>
			</ul>
			<a class="yotuwp-button-sidebar" href="https://www.yotuwp.com/pro/?utm_source=clientsite&utm_medium=pro&utm_campaign=sidebar" target="_blank"><?php _e('More details', 'yotuwp-easy-youtube-embed');?></a>
		</div>
		<div class="yotu-sidebar-box">
			<h2><?php _e('Document', 'yotuwp-easy-youtube-embed');?></h2>
			<p>
				 <?php _e('YotuWP Document', 'yotuwp-easy-youtube-embed');?> <a href="https://www.yotuwp.com/document/?utm_source=clientsite&utm_medium=docs&utm_campaign=doc" target="_blank"><?php _e('Read more', 'yotuwp-easy-youtube-embed');?></a>
			</p>
			<p>
				<?php _e('You do not know how to get YouTube API key', 'yotuwp-easy-youtube-embed');?> > <a href="https://www.yotuwp.com/how-to-get-youtube-api-key/?utm_source=clientsite&utm_medium=docs&utm_campaign=api" target="_blank"><?php _e('Read this article', 'yotuwp-easy-youtube-embed');?></a>
			</p>
		</div>
		<div class="yotu-sidebar-box">
			<h2>Shortcode Generator</h2>
			<p>I just created new feature called <a target="_blank" href="admin.php?page=yotuwp-shortcode">Shortcode Generator</a>. That feature allow you create the YotuWP shortcode for using on widgets, product description, page builder or other place which support shortcode running. <a target="_blank" href="admin.php?page=yotuwp-shortcode">Check it now!</a></p>
		</div>
		
		<div class="yotu-sidebar-box">
			<h2><?php _e('Support', 'yotuwp-easy-youtube-embed');?></h2>
			<p>
				 <?php _e('For futher question and suggestion, please open theard on', 'yotuwp-easy-youtube-embed');?> <a href="https://wordpress.org/support/plugin/yotuwp-easy-youtube-embed" target="_blank"><?php _e('WordPress.org forum', 'yotuwp-easy-youtube-embed');?></a>
			</p>
			<p>
				<?php _e('Or send us message from ', 'yotuwp-easy-youtube-embed');?> <a href="https://www.yotuwp.com/contact/?utm_source=clientsite&utm_medium=contact&utm_campaign=contact" target="_blank"><?php _e('contact form', 'yotuwp-easy-youtube-embed');?></a>
			</p>
		</div>
	</div>
	<?php
	}

	public function popup( $yotuwp, $is_panel = true) {
	?>

	<div class="yotu_insert_popup" data-type="playlist">
		<?php 
		if (is_array( $yotuwp->api) && $yotuwp->api['api_key'] !=''):?>
			<h4><?php _e('Step #1: Select type videos you want to inserts', 'yotuwp-easy-youtube-embed');?></h4>
			<ul class="yotu-tabs yotu-tabs-insert">
				<li>
					<a href="#" data-tab="playlist" data-yotu="insert"><?php _e('Playlist/List', 'yotuwp-easy-youtube-embed');?></a>
				</li>
				<li>
					<a href="#" data-tab="channel" data-yotu="insert"><?php _e('Channel', 'yotuwp-easy-youtube-embed');?></a>
				</li>
				<li>
					<a href="#" data-tab="username" data-yotu="insert"><?php _e('Username', 'yotuwp-easy-youtube-embed');?></a>
				</li>
				<li>
					<a href="#" data-tab="single" data-yotu="insert"><?php _e('Single Video', 'yotuwp-easy-youtube-embed');?></a>
				</li>
				<li>	
					<a href="#" data-tab="videos" data-yotu="insert"><?php _e('Multi Videos', 'yotuwp-easy-youtube-embed');?></a>
				</li>
				<li>	
					<a href="#" data-tab="keyword" data-yotu="insert"><?php _e('By Keyword', 'yotuwp-easy-youtube-embed');?></a>
				</li>
			</ul>
			<div class="yotu-tabs-content yotu-insert-popup">
				<div class="yotu-tab-content" id="yotu-tab-playlist" data-type="playlist">
					<p><?php _e('Please enter playlist/list URL for getting info. Then press Verify button to checking data.', 'yotuwp-easy-youtube-embed');?><br><em>Example: https://www.youtube.com/playlist?list=PLmU8B4gZ41idW0H82OGG8nvlkceNPqpvq</em></p>
					<div class="yotu-input-url">
						<input type="text" name="yotu-input-url" class="yotu-input-value"/>
						<a href="#" class="yotu-button yotu-search-action"><?php _e('Verify', 'yotuwp-easy-youtube-embed');?></a>
					</div>
				</div>
				<div class="yotu-tab-content" id="yotu-tab-channel" data-type="channel">
					<p><?php _e('Please enter channel URL for getting info. Then press Verify button to checking data.', 'yotuwp-easy-youtube-embed');?><br><em>Example: https://www.youtube.com/channel/UCANLZYMidaCbLQFWXBC95Jg</em></p>
					<div class="yotu-input-url">
						<input type="text" name="yotu-input-url" class="yotu-input-value"/>
						<a href="#" class="yotu-button yotu-search-action"><?php _e('Verify', 'yotuwp-easy-youtube-embed');?></a>
					</div>
				</div>
				<div class="yotu-tab-content" id="yotu-tab-username" data-type="username">
					<p><?php _e('Please enter username you want to get videos. Then press Verify button to checking data.', 'yotuwp-easy-youtube-embed');?><br>
						<em>Example: <br />OneDirectionVEVO</em>
					</p>
					<div class="yotu-input-url">
						<textarea type="text" rows="3" cols="50" name="yotu-input-url" class="yotu-input-value"></textarea>
						<a href="#" class="yotu-button yotu-search-action"><?php _e('Verify', 'yotuwp-easy-youtube-embed');?></a>
					</div>
				</div>
				<div class="yotu-tab-content" id="yotu-tab-single" data-type="videos">
					<p><?php _e('Enter your video URL into text box below. Each video filled into each line. Then press Verify button to checking data.', 'yotuwp-easy-youtube-embed');?><br>
						<em>Example: <br />https://www.youtube.com/watch?v=JLf9q36UsBk</em>
					</p>
					<div class="yotu-input-url">
						<input type="text" rows="3" cols="50" name="yotu-input-url" class="yotu-input-value"/>
						<a href="#" class="yotu-button yotu-search-action"><?php _e('Verify', 'yotuwp-easy-youtube-embed');?></a>
					</div>
				</div>
				<div class="yotu-tab-content" id="yotu-tab-videos" data-type="videos">
					<p><?php _e('Enter your videos URL into text box below. Each video filled into each line. Then press Verify button to checking data.', 'yotuwp-easy-youtube-embed');?><br>
						<em>Example: <br />https://www.youtube.com/watch?v=JLf9q36UsBk<br />https://www.youtube.com/watch?v=wyK7YuwUWsU<br />https://www.youtube.com/watch?v=dwdtzwua2pY</em>
					</p>
					<div class="yotu-input-url">
						<textarea type="text" rows="3" cols="50" name="yotu-input-url" class="yotu-input-value"></textarea>
						<a href="#" class="yotu-button yotu-search-action"><?php _e('Verify', 'yotuwp-easy-youtube-embed');?></a>
					</div>
				</div>
				<div class="yotu-tab-content" id="yotu-tab-keyword" data-type="keyword">
					<p><?php _e('Enter your keyword into text box below to listing all videos with that keyword.', 'yotuwp-easy-youtube-embed');?><br>
						<em>Example: <br />TED videos</em>
					</p>
					<div class="yotu-input-url">
						<input type="text" rows="3" cols="50" name="yotu-input-url" class="yotu-input-value"/>
						<a href="#" class="yotu-button yotu-search-action"><?php _e('Verify', 'yotuwp-easy-youtube-embed');?></a>
					</div>
				</div>
			</div>
			<div class="yotu-info-res"></div>
			<div class="yotu-step">
				<h4><?php _e('Step #2: Layout Settings', 'yotuwp-easy-youtube-embed');?></h4>
				<div class="yotu-field">
					<label><?php _e('Use default options', 'yotuwp-easy-youtube-embed');?></label>
					<label class="yotu-switch">
					  <input type="checkbox" checked="checked" id="yotu-settings-handler"/>
					  <span class="yotu-slider yotu-round"></span>
					</label>
				</div>
			</div>
			<div class="yotu-layout yotu-hidden">
				<p>
					<?php _e('Do you need help?', 'yotuwp-easy-youtube-embed');?> <a href="https://www.yotuwp.com/document/" target="_blank"><?php _e('Check out document here', 'yotuwp-easy-youtube-embed');?></a>
				</p>
				<?php
				$data = array(
					'settings' => $yotuwp->options,
					'player' => $yotuwp->player
				);
				$this->settings( $data );
				?>
			</div>
			<?php if( $is_panel):?>
				<div class="yotu-actions">
					<a href="#" class="yotu-button yotu-button-primary"><?php _e('Insert Shortcode', 'yotuwp-easy-youtube-embed');?></a>    
				</div>
			<?php else:?>
				<div class="yotu-step">
					<h4><?php _e('Step #3: Copy your shortcode', 'yotuwp-easy-youtube-embed');?></h4>
					<p><?php _e('Click on the input the select shortcode text then paste into your place you want to display gallery.', 'yotuwp-easy-youtube-embed');?></p>
					<div class="yotu-shortcode-gen yotu-input-url">
						<input type="text" name="shortcode" id="shortcode_val" value="" class="yotu-input-value" />   
					</div>
				</div>
			<?php endif;?>
		<?php else :?>
			<h4 style="color: #f00;">
				<?php printf( __( 'Please enter your Youtube API key from <a href="%s#api">setting page</a> to use this feature.', 'yotuwp-easy-youtube-embed' ), menu_page_url('yotuwp', false) );?>
			</h4>
			<p><?php _e('You can follow guide to get API Key and setup it.', 'yotuwp-easy-youtube-embed');?> <a href="https://www.yotuwp.com/how-to-get-youtube-api-key/" target="_blank"><?php _e('Check out document here', 'yotuwp-easy-youtube-embed');?> >></a></p>
		<?php endif;?>
	</div>
	<?php
	}

	public function display( $template, $data, $settings) {
		global $yotuwp;

		$is_single = false;

		ob_start();

		do_action_ref_array( 'yotu_before_display', array(&$template, &$data, &$settings ));

		if (!isset( $yotuwp->api['api_key']) || empty( $yotuwp->api['api_key'])) {
			$html = __('YotuWP warning: API Key was removed, please contact to your admin about this issues.', 'yotuwp-easy-youtube-embed');
		} else if(is_array( $data)) {
			$html = (isset($data['error']) && $data['error'] == 1) ? $data['msg'] : __('YotuWP: An issue happend when getting the videos, please check your connection and refresh page again .', 'yotuwp-easy-youtube-embed');
		}
		else {
				
			$playerId = uniqid();

			if( $settings['player']['mode'] == 'popup')
				$playerId = 'modal';

			$player      = $settings['player'];
			$width       = '';
			$width_class = '';

			if(isset( $player['width']) && $player['width'] > 0)
				$width = 'width:'.$player['width'] . 'px';
			else
				$width_class = 'yotu-player-full';

			$classeses 		= apply_filters('yotuwp_top_classes', array('yotu-playlist yotuwp'), $settings);
			$classeses[]    = 'yotu-limit-min'. (( $data->totalPage == 1)? ' yotu-limit-max' : '');
			$classeses[]    = $width_class;

			if ( $is_single ) $classeses[] = 'yotu-playlist-single';

			if ( isset( $settings['thumbratio'])) $classeses[] = ' yotu-thumb-'.$settings['thumbratio'];
			if ( isset( $settings['template'])) $classeses[] = ' yotu-template-'.$settings['template'];

			$attrs = array('data-yotu="'. $playerId .'"');
			$attrs[] = 'data-page="1"';
			$attrs[] = 'id="yotuwp-'.$settings['gallery_id'].'"';
			$attrs[] = 'data-total="'. $data->totalPage .'"';
			$attrs[] = 'data-settings="'. base64_encode(json_encode( $settings )) .'"';
			$attrs[] = 'data-player="'. $settings['player']['mode'] .'"';
			$attrs[] = 'data-showdesc="'. $settings['description'] .'"';

			?>
			<div class="<?php echo implode( ' ', $classeses );?>" <?php echo implode( ' ', $attrs );?>>
				<div>
					<?php if ( $player['mode'] =='large'):?>
					<div class="yotu-wrapper-player" style="<?php echo $width;?>">
						<?php if ( $player['playing'] ):?>
							<div class="yotu-playing">
								<?php if (count( $data->items) > 0 ):
									echo yotuwp_video_title($data->items[0]);
								endif;?>
							</div>
						<?php endif;?>
						<div class="yotu-player">
							<div class="yotu-video-placeholder" id="yotu-player-<?php echo $playerId;?>"></div>
						</div>
						<div class="yotu-playing-status"></div>
						<?php if( $player['playing_description']):?>
						<div class="yotu-playing-description">
							<?php if (count( $data->items) >1 ):
								echo yotuwp_video_description($data->items[0]);
							endif;?>
						</div>
						<?php endif;?>
					</div>

					<?php
					endif;
					
					if (
						isset( $settings['pagination'] ) && 
						$settings['pagination'] == 'on' && 
						$settings['pagitype'] == 'pager'
					) {
						$pagination_pos = 'top';
						include( $yotuwp->path . YTDS . 'templates' . YTDS . 'pagination.php');
					}

					echo $yotuwp->template( $template, $data, $settings);

					if (
						isset( $settings['pagination']) && 
						$settings['pagination'] == 'on'
					) {
						$pagination_pos = 'bottom';
						include( $yotuwp->path . YTDS . 'templates' . YTDS . 'pagination.php');
					}
					?>
				</div>
			</div>
			<?php
			
			$html = ob_get_contents();
			ob_end_clean();
		}

		return $html;
	}


	public function admin_page()
	{
		global $yotuwp;
		?>
		<div class="yotu-wrap wrap yotuwp-settings">
			<h1></h1>
			<div class="yotu-body">
				
				<?php if (isset( $_GET['install'])):?>
					<div id="message" class="updated notice notice-success is-dismissible megabounce-msg">
						<p><?php _e('Thank you for activation YotuWP! Set your API key to start using. <a href="https://www.yotuwp.com/document/?utm_source=clientsite&amp;utm_medium=docs&amp;utm_campaign=doc" target="_blank">Read more</a>', 'yotuwp-easy-youtube-embed');?></p>
					</div>
				<?php endif?>
				
				<div class="yotu-body-form">
					<form method="post" action="options.php">
						<input type="hidden" id="yotu-settings-last_tab" class="yotu-param" name="yotu-settings[last_tab]" value="<?php echo $yotuwp->options['last_tab'];?>">
						<input type="hidden" id="yotu-settings-last_update" class="yotu-param" name="yotu-settings[last_update]" value="<?php echo time();?>">
						<div class="yotu-settings-title">
							<div class="yotu-logo">
								<img src="<?php echo $yotuwp->url . 'assets/images/yotu-small.png';?>" height="80"/>
								<div><?php _e('Version', 'yotuwp-easy-youtube-embed'); echo ' '. $yotuwp->version;?></div>
							</div>
							<span><?php _e('YotuWP Settings', 'yotuwp-easy-youtube-embed');?></span>
							<?php submit_button(); ?>
						</div>
						<?php settings_errors(); ?>
						
						<?php
						//unset($yotuwp->options['premium']);
						$data = array(
							'settings' => $yotuwp->options,
							'player'   => $yotuwp->player,
							'cache'    => $yotuwp->cache_cfg,
							'styling'  => $yotuwp->styling,
							'api'      => $yotuwp->api,
							'effects'      => $yotuwp->effects,
							'is_panel' => true
						);

						$this->settings( $data );

						?>
					</form>
				</div>
				
				<?php $this->sidebar();?>
			</div>
			
		</div>
		<?php
		
	}

	public function slugify( $text) {
		$text = preg_replace('~[^\pL\d]+~u', '-', $text);
		$text = @iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = preg_replace('~[^-\w]+~', '', $text);
		$text = trim( $text, '-');
		$text = preg_replace('~-+~', '-', $text);
		$text = strtolower( $text);

		if (empty( $text)) {
			return 'n-a';
		}

		return $text;
	}

	public function render_tabs( $sections, $is_panel = false) {
		global $yotuwp;

		include( $yotuwp->path . YTDS . 'inc' . YTDS  .  'fields.php');
		//include pro field
		if (defined('YOTUPRO_VERSION') && file_exists( $yotuwp->pro_path . YTDS . 'inc' . YTDS  .  'fields.php')) {
			include( $yotuwp->pro_path . YTDS . 'inc' . YTDS  .  'fields.php');
			$field_control = new YotuProFields();
		} else $field_control = new YotuFields();
		

		

		$tabs_control = array();
		$tabs_content = array();
		
		uasort( $sections, function( $a, $b ) {
			if( !isset( $a['priority'] ) ) return false;
			if( !isset( $b['priority'] ) ) return true;
			return $a['priority'] - $b['priority'];
		});

		foreach ( $sections as $tab => $section) {

			//if(!isset($section['title'])) print_r($section);
			
			$group_id = $section['key'];

			$tabs_control[] = '<li><a href="#" data-tab="'.$group_id.'">'.( isset( $section['icon'] )? '<span class="dashicons '. $section['icon'] .'"></span>' : '' ) . $section['title'].'</a></li>';
			$tabs_content[] = '<div class="yotu-tab-content" style="display:block;" id="yotu-tab-'.$group_id.'">';

			
			uasort($section['fields'], function($a, $b) {
				if( !isset( $a['priority'] ) ) return false;
				if( !isset( $b['priority'] ) ) return true;
				return $a['priority'] - $b['priority'];
			});

			foreach ( $section['fields'] as $field) {
				$field['group'] = $group_id;
				$tabs_content[] = $field_control->render_field( $field);
			}

			$tabs_content[] = '</div>';
		}

		$tabs_control = apply_filters( 'yotuwp_tabs_control', $tabs_control, $is_panel );
		$tabs_content = apply_filters( 'yotuwp_tabs_content', $tabs_content, $is_panel );

		?>
		<div class="yotu-tabs">
			<ul><?php echo implode( '',$tabs_control );?></ul>
		</div>
		
		<div class="yotu-tabs-content"><?php echo implode( '', $tabs_content );?></div>
		<?php if ( $is_panel ):
		?>
		<div class="yotu-submit">
            <?php
            // This prints out all hidden setting fields
            settings_fields( 'yotu' );
            do_settings_sections( 'yotu-settings' );
            submit_button(); ?>

        </div>
		<?php
		endif;

	}

	public function shortcode_gen(){
		global $yotuwp, $current_user ;
		
		$user_id = $current_user->ID;
		if ( !get_user_meta( $user_id, 'yotuwp_scgen_ignore_notice' ) ) {
			update_user_meta( $user_id, 'yotuwp_scgen_ignore_notice', false);
		}
		?>
		<div class="yotu-wrap wrap">
			<div class="yotu-logo">
				<img src="<?php echo $yotuwp->url . 'assets/images/yotu-small.png';?>" height="80"/>
				<div><?php _e('Version', 'yotuwp-easy-youtube-embed'); echo ' '. $yotuwp->version;?></div>
			</div>
			<div class="yotu-body shortcode_gen">
				<h1><?php _e('YotuWP Shortcode Generate', 'yotuwp-easy-youtube-embed');?></h1>
				<p><?php _e('This feature helps you generate YotuWP shortcode to adding to any page builder, product description or widget.', 'yotuwp-easy-youtube-embed');?></p>
				<?php $this->popup( $yotuwp, false); ?>
				<?php $this->sidebar();?>
			</div>
			
		</div>
		<?php
	}
}

