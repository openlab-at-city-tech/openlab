<?php
/*-----------------------------------------------------------------------------------*/
/*	Social Widget Class
/*-----------------------------------------------------------------------------------*/

class MKS_Social_Widget extends WP_Widget {

	public $defaults;

	function __construct() {
		$widget_ops = array( 'classname' => 'mks_social_widget', 'description' => __( 'Display your social icons with this widget', 'meks-smart-social-widget' ) );
		$control_ops = array( 'id_base' => 'mks_social_widget' );
		parent::__construct( 'mks_social_widget', __( 'Meks Social Widget', 'meks-smart-social-widget' ), $widget_ops, $control_ops );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_filter( 'use_widgets_block_editor', '__return_false' );

		$this->defaults = array(
			'title' => __( 'Follow Me', 'meks-smart-social-widget' ),
			'content' => '',
			'style' => 'square',
			'size' => 48,
			'font_size' => 16,
			'target' => '_blank',
			'social' => array()
		);

		//Allow themes or plugins to modify default parameters
		$this->defaults = apply_filters( 'mks_social_widget_modify_defaults', $this->defaults );

	}

	function enqueue_scripts() {
		wp_register_style( 'meks-social-widget', MKS_SOCIAL_WIDGET_URL.'css/style.css', false, MKS_SOCIAL_WIDGET_VER );
		wp_enqueue_style( 'meks-social-widget' );
	}

	function enqueue_admin_scripts() {
		wp_enqueue_script( 'meks-social-widget-js', MKS_SOCIAL_WIDGET_URL.'js/main.js', array( 'jquery', 'jquery-ui-sortable' ), MKS_SOCIAL_WIDGET_VER );
		wp_enqueue_style( 'mks-social-widget-css', MKS_SOCIAL_WIDGET_URL . 'css/admin.css', false, MKS_SOCIAL_WIDGET_VER );
	}

	function widget( $args, $instance ) {

		extract( $args );

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;

		if ( !empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
?>

		<?php if ( !empty( $instance['content'] ) ) : ?>
			<?php echo wpautop( wp_kses_post( $instance['content'] ) ); ?> 
		<?php endif; ?>

		<?php if ( !empty( $instance['social'] ) ): ?>
			<?php
				$size_style = 'style="width: '.esc_attr( $instance['size'] ).'px; height: '.esc_attr( $instance['size'] ).'px; font-size: '.esc_attr( $instance['font_size'] ).'px;line-height:'.esc_attr( $instance['size']+ceil( $instance['font_size']/3.5 ) ).'px;"';

				$target = $instance['target'] == '_blank' ? 'target="'.esc_attr( $instance['target'] ).'" rel="noopener"' : 'target="'.esc_attr( $instance['target'] ).'"' ;
			?>
			<ul class="mks_social_widget_ul">
			<?php foreach ( $instance['social'] as $item ) : ?>
				<li><a href="<?php echo $item['url']; ?>" title="<?php echo esc_attr( $this->get_social_title( $item['icon'] ) ); ?>" class="socicon-<?php echo esc_attr( $item['icon'] ); ?> <?php echo esc_attr( 'soc_'.$instance['style'] ); ?>" <?php echo $target; ?> <?php echo $size_style; ?>><span><?php echo $item['icon']; ?></span></a></li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>


		<?php
		echo $after_widget;
	}



	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['content'] = $new_instance['content'];
		$instance['style'] = $new_instance['style'];
		$instance['size'] = absint( $new_instance['size'] );
		$instance['font_size'] = absint( $new_instance['font_size'] );
		$instance['target'] = $new_instance['target'];
		$instance['social'] = array();
		if ( !empty( $new_instance['social_icon'] ) ) {
			$protocols = wp_allowed_protocols();
			$protocols[] = 'skype'; //allow skype call protocol
			for ( $i=0; $i < ( count( $new_instance['social_icon'] ) - 1 ); $i++ ) {
				$temp = array( 'icon' => $new_instance['social_icon'][$i], 'url' => esc_url( $new_instance['social_url'][$i], $protocols ) );
				$instance['social'][] = $temp;
			}
		}
		return $instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->defaults );

		$social_links = $this->get_social();
?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'meks-smart-social-widget' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Introduction text (optional)', 'meks-smart-social-widget' ); ?>:</label>
			<textarea id="<?php echo $this->get_field_id( 'content' ); ?>" rows="5" name="<?php echo $this->get_field_name( 'content' ); ?>" class="widefat"><?php echo $instance['content']; ?></textarea>
		</p>

		<p>
			<span class="mks-option-label mks-option-fl"><?php _e( 'Icon shape', 'meks-smart-social-widget' ); ?>:</span><br/>
			<div class="mks-option-radio-wrapper">

			<label class="mks-option-radio"><input type="radio" name="<?php echo $this->get_field_name( 'style' ); ?>" value="square" <?php checked( $instance['style'], 'square' ); ?>/><?php _e( 'Square', 'meks-smart-social-widget' ); ?></label><br/>
			<label class="mks-option-radio"><input type="radio" name="<?php echo $this->get_field_name( 'style' ); ?>" value="circle" <?php checked( $instance['style'], 'circle' ); ?>/><?php _e( 'Circle', 'meks-smart-social-widget' ); ?></label><br/>
			<label class="mks-option-radio"><input type="radio" name="<?php echo $this->get_field_name( 'style' ); ?>" value="rounded" <?php checked( $instance['style'], 'rounded' ); ?>/><?php _e( 'Rounded corners', 'meks-smart-social-widget' ); ?></label>
			</div>
		</p>

		<p>
			<label class="mks-option-label" for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Icon size', 'meks-smart-social-widget' ); ?>: </label>
			<input id="<?php echo $this->get_field_id( 'size' ); ?>" type="text" name="<?php echo $this->get_field_name( 'size' ); ?>" value="<?php echo absint( $instance['size'] ); ?>" class="small-text" /> px
		</p>


		<p>
			<label class="mks-option-label" for="<?php echo $this->get_field_id( 'font_size' ); ?>"><?php _e( 'Icon font size', 'meks-smart-social-widget' ); ?>: </label>
			<input id="<?php echo $this->get_field_id( 'font_size' ); ?>" type="text" name="<?php echo $this->get_field_name( 'font_size' ); ?>" value="<?php echo absint( $instance['font_size'] ); ?>" class="small-text" /> px
		</p>

		<p>
			<label class="mks-option-label" for="<?php echo $this->get_field_id( 'target' ); ?>"><?php _e( 'Open links in', 'meks-smart-social-widget' ); ?>: </label>
			<select id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>">
				<option value="_blank" <?php selected( '_blank', $instance['target'] ); ?>><?php _e( 'New Window', 'meks-smart-social-widget' ); ?></option>
				<option value="_self" <?php selected( '_self', $instance['target'] ); ?>><?php _e( 'Same Window', 'meks-smart-social-widget' ); ?></option>
			</select>
		</p>

		<h4 class="mks-icons-title"><?php _e( 'Icons', 'meks-smart-social-widget' ); ?>:</h4>

		<ul class="mks_social_container mks-social-sortable">
		  <?php foreach ( $instance['social'] as $link ) : ?>
			  <li>
			  	<?php $this->draw_social( $this, $social_links, $link ); ?>
			  </li>
			<?php endforeach; ?>
		</ul>


		<p>
	  	<a href="#" class="mks_add_social button"><?php _e( 'Add Icon', 'meks-smart-social-widget' ); ?></a>
	  </p>

	  <div class="mks_social_clone" style="display:none">
			<?php $this->draw_social( $this, $social_links ); ?>
	  </div>



	<?php
	}

	function draw_social( $widget, $social_links, $selected = array( 'icon' => '', 'url' => '' ) ) { ?>

				<label class="mks-sw-icon"><?php _e( 'Icon', 'meks-smart-social-widget' ); ?> :</label>
				<select type="text" name="<?php echo $widget->get_field_name( 'social_icon' ); ?>[]" value="<?php echo $selected['icon']; ?>" style="width: 82%">
					<?php foreach ( $social_links as $key => $link ) : ?>
						<option value="<?php echo $key; ?>" <?php selected( $key, $selected['icon'] ); ?>><?php echo $link; ?></option>
					<?php endforeach; ?>
				</select>

				<label class="mks-sw-icon"><?php _e( 'Url', 'meks-smart-social-widget' ); ?> :</label>
				<input type="text" name="<?php echo $widget->get_field_name( 'social_url' ); ?>[]" value="<?php echo $selected['url']; ?>" style="width: 82%">


				<span class="mks-remove-social dashicons dashicons-no-alt"></span>

	<?php }


	protected function get_social_title( $social_name ) {
		$items = $this->get_social();
		return $items[$social_name];
	}

	function get_social() {
		$new_icons = array(
			'500px' => '500px',
			'8tracks' => '8tracks',
			'airbnb' => 'Airbnb',
			'alliance' => 'Alliance',
			'amazon' => 'Amazon',
			'amplement' => 'amplement',
			'android' => 'Android',
			'angellist' => 'AngelList',
			'apple' => 'Apple',
			'appnet' => 'appnet',
			'baidu' => 'baidu',
			'bandcamp' => 'bandcamp',
			'battlenet' => 'BATTLE.NET',
			'mixer' => 'Mixer',
			'bebee' => 'beBee',
			'bebo' => 'bebo',
			'behance' => 'Behance',
			'blizzard' => 'Blizzard',
			'blogger' => 'Blogger',
			'buffer' => 'Buffer',
			'chrome' => 'Chrome',
			'coderwall' => 'coderwall',
			'curse' => 'Curse',
			'dailymotion' => 'Dailymotion',
			'deezer' => 'DEEZER',
			'delicious' => 'Delicious',
			'deviantart' => 'deviantART',
			'diablo' => 'Diablo',
			'digg' => 'Digg',
			'discord' => 'Discord',
			'disqus' => 'DISQUS',
			'douban' => 'douban',
			'draugiem' => 'draugiem.lv',
			'dribbble' => 'dribbble',
			'drupal' => 'Drupal',
			'ebay' => 'ebay',
			'ello' => 'Ello',
			'endomondo' => 'endomondo',
			'envato' => 'Envato',
			'etsy' => 'Etsy',
			'facebook' => 'Facebook',
			'feedburner' => 'FeedBurner',
			'filmweb' => 'FILMWEB',
			'firefox' => 'Firefox',
			'flattr' => 'Flattr',
			'flickr' => 'flickr',
			'formulr' => 'FORMULR',
			'forrst' => 'Forrst',
			'foursquare' => 'foursquare',
			'friendfeed' => 'FriendFeed',
			'github' => 'GitHub',
			'goodreads' => 'goodreads',
			'google' => 'Google',
			'googlegroups' => 'Google Groups',
			'googlephotos' => 'Google Photos',
			'googlescholar' => 'Google Scholar',
			'grooveshark' => 'grooveshark',
			'hackerrank' => 'HackerRank',
			'hearthstone' => 'Hearthstone',
			'hellocoton' => 'Hellocoton',
			'heroes' => 'Hereos of the Storm',
			'hitbox' => 'hitbox',
			'horde' => 'Horde',
			'houzz' => 'houzz',
			'icq' => 'icq',
			'identica' => 'Identica',
			'imdb' => 'IMDb',
			'instagram' => 'Instagram',
			'issuu' => 'issuu',
			'istock' => 'iStock',
			'itunes' => 'iTunes',
			'keybase' => 'Keybase',
			'lanyrd' => 'Lanyrd',
			'lastfm' => 'last.fm',
			'line' => 'LINE',
			'linkedin' => 'Linkedin',
			'livejournal' => 'LiveJournal',
			'lyft' => 'lyft',
			'macos' => 'macOS',
			'mail' => 'Mail',
			'medium' => 'Medium',
			'meetup' => 'Meetup',
			'mixcloud' => 'Mixcloud',
			'modelmayhem' => 'Model Mayhem',
			'mumble' => 'mumble',
			'myspace' => 'Myspace',
			'newsvine' => 'NewsVine',
			'nintendo' => 'Nintendo Network',
			'npm' => 'npm',
			'odnoklassniki' => 'Odnoklassniki',
			'openid' => 'OpenID',
			'opera' => 'Opera',
			'outlook' => 'Outlook',
			'overwatch' => 'Overwatch',
			'patreon' => 'Patreon',
			'paypal' => 'Paypal',
			'periscope' => 'Periscope',
			'persona' => 'Mozilla Persona',
			'pinterest' => 'Pinterest',
			'play' => 'Google Play',
			'player' => 'Player.me',
			'playstation' => 'PlayStation',
			'pocket' => 'Pocket',
			'qq' => 'QQ',
			'quora' => 'Quora',
			'raidcall' => 'RaidCall',
			'ravelry' => 'Ravelry',
			'reddit' => 'reddit',
			'renren' => 'renren',
			'researchgate' => 'ResearchGate',
			'residentadvisor' => 'Resident Advisor',
			'reverbnation' => 'REVERBNATION',
			'rss' => 'RSS',
			'sharethis' => 'ShareThis',
			'skype' => 'skype',
			'slideshare' => 'SlideShare',
			'smugmug' => 'SmugMug',
			'snapchat' => 'Snapchat',
			'songkick' => 'songkick',
			'soundcloud' => 'soundcloud',
			'spotify' => 'Spotify',
			'stackexchange' => 'StackExchange',
			'stackoverflow' => 'stackoverflow',
			'starcraft' => 'Starcraft',
			'stayfriends' => 'StayFriends',
			'steam' => 'steam',
			'storehouse' => 'Storehouse',
			'strava' => 'STRAVA',
			'streamjar' => 'StreamJar',
			'stumbleupon' => 'StumbleUpon',
			'swarm' => 'Swarm',
			'teamspeak' => 'TeamSpeak',
			'teamviewer' => 'TeamViewer',
			'technorati' => 'Technorati',
			'telegram' => 'Telegram',
			'tiktok' => 'TikTok',
			'tripadvisor' => 'tripadvisor',
			'tripit' => 'Tripit',
			'triplej' => 'triplej',
			'tumblr' => 'tumblr',
			'twitch' => 'Twitch',
			'twitter' => 'X (ex Twitter)',
			'uber' => 'UBER',
			'ventrilo' => 'Ventrilo',
			'viadeo' => 'Viadeo',
			'viber' => 'Viber',
			'viewbug' => 'viewbug',
			'vimeo' => 'vimeo',
			'vine' => 'Vine',
			'vkontakte' => 'VKontakte',
			'warcraft' => 'Warcraft',
			'wechat' => 'WeChat',
			'weibo' => 'Sina Weibo',
			'whatsapp' => 'WhatsApp',
			'wikipedia' => 'Wikipedia',
			'windows' => 'Windows',
			'wordpress' => 'WordPress',
			'wykop' => 'wykop',
			'xbox' => 'XBOX',
			'xing' => 'Xing',
			'yahoo' => 'Yahoo!',
			'yammer' => 'Yammer',
			'yandex' => 'Yandex',
			'yelp' => 'yelp',
			'younow' => 'Younow',
			'youtube' => 'YouTube',
			'zapier' => 'Zapier',
			'zerply' => 'Zerply',
			'zomato' => 'zomato',
			'zynga' => 'zynga',
			'spreadshirt' => 'spreadshirt',
			'trello' => 'Trello',
			'gamejolt' => 'Game Jolt',
			'tunein' => 'tunein',
			'bloglovin' => 'BLOGLOVIN\'',
			'gamewisp' => 'GameWisp',
			'messenger' => 'Facebook Messenger',
			'pandora' => 'Pandora',
			'microsoft' => 'Microsoft',
			'mobcrush' => 'mobcrush',
			'sketchfab' => 'Sketchfab',
			'yt-gaming' => 'Youtube Gaming',
			'fyuse' => 'FYUSE',
			'bitbucket' => 'Bitbucket',
			'augment' => 'AUGMENT',
			'toneden' => 'ToneDen',
			'niconico' => 'niconico',
			'zillow' => 'Zillow',
			'googlemaps' => 'Google Maps',
			'booking' => 'Booking',
			'fundable' => 'Fundable',
			'upwork' => 'Upwork',
			'ghost' => 'ghost',
			'loomly' => 'loomly',
			'trulia' => 'Trulia',
			'ask' => 'Ask',
			'gust' => 'Gust',
			'toptal' => 'Toptal',
			'squarespace' => 'Squarespace',
			'bonanza' => 'bonanza',
			'doodle' => 'Doodle',
			'bing' => 'Bing',
			'seedrs' => 'Seedrs',
			'freelancer' => 'Freelancer',
			'shopify' => 'Shopify',
			'googlecalendar' => 'Google Calendar',
			'redfin' => 'Redfin',
			'wix' => 'Wix',
			'craigslist' => 'Craigslist',
			'alibaba' => 'Alibaba',
			'zoom' => 'Zoom',
			'homes' => 'Homes',
			'appstore' => 'Appstore',
			'guru' => 'Guru',
			'aliexpress' => 'Aliexpress',
			'gotomeeting' => 'GoToMeeting',
			'fiverr' => 'Fiverr',
			'logmein' => 'LogMeIn',
			'openaigym' => 'OpenAI Gym',
			'slack' => 'Slack',
			'codepen' => 'Codepen',
			'angieslist' => 'Angie\'s List',
			'homeadvisor' => 'HomeAdvisor',
			'unsplash' => 'Unsplash',
			'mastodon' => 'Mastodon',
			'natgeo' => 'Natgeo',
			'qobuz' => 'Qobuz',
			'tidal' => 'Tidal',
			'realtor' => 'Realtor',
			'calendly' => 'Calendly',
			'homify' => 'homify',
			'crunchbase' => 'crunchbase',
			'livemaster' => 'livemaster',
			'udemy' => 'udemy',
			'nextdoor' => 'Nextdoor',
			'origin' => 'Origin',
			'codered' => 'CodeRED',
			'portfolio' => 'Adobe Portfolio',
			'instructables' => 'instructables',
			'gitlab' => 'GitLab',
			'mailru' => 'Mail.ru',
			'bookbub' => 'BookBub',
			'kobo' => 'Kobo',
			'smashwords' => 'Smashwords',
			'hackernews' => 'Hacker News',
			'hackerone' => 'hackerone',
			'beatport' => 'beatport',
			'napster' => 'napster',
			'spip' => 'SPIP',
			'wickr' => 'Wickr',
			'blackberry' => 'BlackBerry',
			'myanimelist' => 'MyAnimeList',
			'pixiv' => 'pixiv',
			'gamefor' => 'GameFor',
			'traxsource' => 'Traxsource',
			'indiedb' => 'indie DB',
			'moddb' => 'Mod DB',
			'internet' => 'Internet',
		);

		$old_icons = array(
			'aim' => 'Aim',
			'evernote' => 'Evernote',
			'cargo' => 'Cargo',
			'icloud' => 'iCloud',
			'picasa' => 'Picasa',
			'posterous' => 'Posterous',
			'tencent' => 'TenCent',
			'me2day' => 'Me2Day'
		);

		$icons = array_merge( $new_icons, $old_icons );

		ksort( $icons );

		return $icons;
	}
}

?>
