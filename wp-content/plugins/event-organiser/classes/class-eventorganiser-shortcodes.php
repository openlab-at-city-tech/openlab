<?php
/**
 * Class used to create the event calendar shortcode
 *
 * @uses EO_Calendar Widget class to generate calendar html
 * @ignore
 */
class EventOrganiser_Shortcodes {
	static $add_script;
	static $calendars =array();
	static $widget_calendars =array();
	static $map = array();
	static $event;
 
	static function init() {
		add_shortcode('eo_calendar', array(__CLASS__, 'handle_calendar_shortcode'));
		add_shortcode('eo_fullcalendar', array( __CLASS__, 'handle_fullcalendar_shortcode'));
		add_shortcode('eo_venue_map', array(__CLASS__, 'handle_venuemap_shortcode'));
		add_shortcode('eo_events', array(__CLASS__, 'handle_eventlist_shortcode'));
		add_shortcode('eo_subscribe', array(__CLASS__, 'handle_subscription_shortcode'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}
 
	static function handle_calendar_shortcode($atts = array()) {
		$atts = (array) $atts;
		/* Shortcodes don't accept hyphens, so convert taxonomy names */
		$taxs = array('category','tag','venue');
		foreach ($taxs as $tax){
			if(isset($atts['event_'.$tax])){
				$atts['event-'.$tax]=	$atts['event_'.$tax];
				unset($atts['event_'.$tax]);
			}
		}

		if( isset( $atts['show_long'] ) ){
			$atts['show-long'] = $atts['show_long'];
			unset( $atts['show_long'] ); 			
		}
		
		if( isset( $atts['link_to_single'] ) ){
			$atts['link-to-single'] = $atts['link_to_single'];
			unset( $atts['link_to_single'] ); 			
		}

		/* Parse defaults */
		$atts = wp_parse_args($atts,array(
			'showpastevents' => 1,
			'show-long'      => 0,
			'link-to-single' => 0,
		));
	
		self::$add_script = true;

		$id     = count(self::$widget_calendars);
		$cal_id = 'eo_shortcode_calendar_'.$id;
		self::$widget_calendars[$cal_id] = $atts;

		$tz = eo_get_blog_timezone();
		$date =  isset($_GET['eo_month']) ? $_GET['eo_month'].'-01' : 'now';
		$month = new DateTime($date,$tz);
		$month = date_create($month->format('Y-m-1'),$tz);
		
		$html = '<div class="widget_calendar eo-calendar eo-calendar-shortcode eo_widget_calendar" id="'.$cal_id.'">';
		
		$html .= '<div id="'.$cal_id.'_content" class="eo-widget-cal-wrap" data-eo-widget-cal-id="'.$cal_id.'">';
		$html .= EO_Calendar_Widget::generate_output( $month, $atts );
		$html .='</div>';
		
		$html .= '</div>';

		return $html;
	}

	static function handle_subscription_shortcode( $atts, $content = null ) {
		$atts = shortcode_atts( array(
			'title' => 'Subscribe to calendar',
			'type' => 'google',
			'class' => '',
			'id' => '',
			'style' => '',
			'category' => false,
			'venue' => false,
		), $atts, 'eo_subscribe' );

		if ( $atts['category'] ) {
			$url = eo_get_event_category_feed( $atts['category'] );

		} elseif ( $atts['venue'] ) {
			$url = eo_get_event_venue_feed( $atts['venue'] );

		} else {
			$url = eo_get_events_feed();
		}

		$class = $atts['class'] ? 'class="' . esc_attr( $atts['class'] ) . '"' : false;
		$title = $atts['title'] ? 'title="' . esc_attr( $atts['title'] ) . '"' : false;
		$style = $atts['style'] ? 'style="' . esc_attr( $atts['style'] ) . '"' : false;
		$id    = $atts['id']    ? 'id="' . esc_attr( $atts['id'] ) . '"' : false;

		if ( strtolower( $atts['type'] ) == 'webcal' ) {
			$url = preg_replace( '/^http(s?):/i', 'webcal:', $url );
		} elseif ( strtolower( $atts['type'] ) == 'ical' ) {
			//Do nothing
		} else {
			//Google doesn't support https:// protocols for the cid value
			//@see https://github.com/stephenharris/Event-Organiser/issues/328
			//@link  http://stackoverflow.com/a/21218052/932391
			$url = preg_replace( '/^https:/i', 'http:', $url );
			$url = add_query_arg( 'cid', urlencode( $url ), 'https://www.google.com/calendar/render' );
		}

		$html = '<a href="'.$url.'" target="_blank" '.$class.' '.$title.' '.$id.' '.$style.'>'.$content.'</a>';
		return $html;
	}

	static function handle_fullcalendar_shortcode( $atts = array() ) {

		global $wp_locale;

		/* Handle Boolean attributes - this will be passed as strings, we want them as boolean */
		$bool_atts = array(
			'tooltip' => 'true', 'weekends' => 'true', 'alldayslot' => 'true', 'users_events' => 'false',
			'theme' => 'false', 'isrtl' => $wp_locale->is_rtl() ? 'true' : 'false', 'responsive' => 'true',
			'compact' => false,
		);

		$atts = wp_parse_args( $atts, $bool_atts );

		foreach( $bool_atts as $att => $value ){
			$atts[$att] = ( strtolower( $atts[$att] ) == 'true' ? true : false );
		}
		
		//Backwards compatability, key used to be true/false. Now can be bottom/top
		if( isset( $atts['key'] ) ){
			if( 'true' == strtolower( $atts['key'] ) ){
				$atts['key'] = 'bottom';
			}elseif( !in_array( strtolower( $atts['key'] ), array( 'bottom', 'top' ) ) ){
				$atts['key'] = false;
			}
		}

		if( isset($atts['venue']) && !isset( $atts['event_venue'] ) ){
			$atts['event_venue'] = $atts['venue'];
			unset( $atts['venue'] );
		}
		if( isset($atts['category']) && !isset( $atts['event_category'] ) ){
			$atts['event_category'] = $atts['category'];
			unset( $atts['category'] );
		}
		
		$date_attributes = array( 
			'timeformat', 'axisformat', 'titleformatday', 'titleformatweek', 'titleformatmonth',
			'columnformatmonth', 'columnformatweek', 'columnformatday',
		);
		
		foreach( $date_attributes as $attribute ){
			if( isset( $atts[$attribute] ) ){
				$atts[$attribute] = self::_cleanup_format( $atts[$attribute] );
			}
		}

		$taxonomies = get_object_taxonomies( 'event' );
		foreach( $taxonomies as $tax ){
			//Shortcode attributes can't contain hyphens
			$shortcode_attr = str_replace( '-', '_', $tax );
			if( isset( $atts[$shortcode_attr] ) ){
				$atts[$tax] = $atts[$shortcode_attr];
				unset( $atts[$shortcode_attr] ); 
			}
		}
		
		if( isset( $atts['showdays'] ) ){
			$atts['showdays'] = explode( ',', $atts['showdays'] );
		}
				
		return eo_get_event_fullcalendar( $atts );
	}
	
	/**
	 * Prior to 3.0.0, formats could accept operators to deal with ranges.
	 * Specifically {...} switches to formatting the 2nd date and ((...)) only displays 
	 * the enclosed format if the current date is different from the alternate date in 
	 * the same regards.E.g.  M j(( Y)){ '—'(( M)) j Y} produces the following dates: 
	 * Dec 30 2013 — Jan 5 2014, Jan 6 — 12 2014
	 * 
	 * This was removed in 3.0.0, fullCalendar.js will now automatically split the date where
	 * appropriate. This function removes {...} and all enclosed content and replaces ((...))
	 * by the content contained within to help prevent an users upgrading from the old version.
	 * 
	 * @ignore
	 */
	static function _cleanup_format( $format ){
		$format = preg_replace( '/({.*})/', '', $format );
		$format = preg_replace_callback( '/\(\((.*)\)\)/', array( __CLASS__ ,'_replace_open_bracket' ), $format );			
		return $format;
	}
	
	static function _replace_open_bracket( $matches ){
		return $matches[1];
	}

	static function handle_venuemap_shortcode($atts = array()) {
		global $post;

		$atts = (array) $atts;

		if( !empty( $atts['event_venue'] ) ){
			$atts['venue'] = $atts['event_venue'];
		}

		//If venue is not set get from the venue being quiered or the post being viewed
		if( empty($atts['venue']) ){
			if( eo_is_venue() ){
				$atts['venue'] = esc_attr( get_query_var( 'term' ) );
			}else{
				$atts['venue'] = eo_get_venue_slug( get_the_ID() );
			}
		}

		$venue_slugs = explode( ',', $atts['venue'] );

		$args = shortcode_atts( array(
			'zoom' => 15, 'zoomcontrol' => 'true', 'minzoom' => 0, 'maxzoom' => null,
			'scrollwheel' => 'true', 'rotatecontrol' => 'true', 'pancontrol' => 'true',
			'overviewmapcontrol' => 'true', 'streetviewcontrol' => 'true',
			'maptypecontrol' => 'true', 'draggable' => 'true', 'maptypeid' => 'ROADMAP',
			'width' => '100%','height' => '200px','class' => '', 'tooltip' => 'false',
			), $atts );

		//Cast options as boolean:
		$bool_options = array( 
			'tooltip', 'scrollwheel', 'zoomcontrol', 'rotatecontrol', 'pancontrol',
			'overviewmapcontrol', 'streetviewcontrol', 'draggable', 'maptypecontrol',
		);
		foreach( $bool_options as $option  ){
			$args[$option] = ( $args[$option] == 'false' ? false : true );
		}

		return eo_get_venue_map( $venue_slugs, $args );
	}



	static function handle_eventlist_shortcode($atts=array(),$content=null) {
		$atts = (array) $atts;
		$taxs = array('category','tag','venue');
		foreach ($taxs as $tax){
			if(isset($atts['event_'.$tax])){
				$atts['event-'.$tax]=	$atts['event_'.$tax];
				unset($atts['event_'.$tax]);
			}
		}

		if((isset($atts['venue']) &&$atts['venue']=='%this%') ||( isset($atts['event-venue']) && $atts['event-venue']=='%this%' )){
			if( eo_get_venue_slug() ){
				$atts['event-venue']=  eo_get_venue_slug();
			}else{
				unset($atts['venue']);
				unset($atts['event-venue']);
			}
		}
		
		if( isset( $atts['users_events'] ) && strtolower( $atts['users_events'] ) == 'true' ){
			$atts['bookee_id'] = get_current_user_id();
		}

		$args = array(
			'class'=>'eo-events eo-events-shortcode',
			'template'=>$content,
			'no_events'=> isset( $atts['no_events'] ) ? $atts['no_events'] : '',
			'type'=>'shortcode',
		);
		

		return eventorganiser_list_events( $atts,$args, 0);
	}


	static function read_template($template){
		$patterns = array(
			'/%(event_title)%/',
			'/%(start)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(end)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(end)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(end)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(end)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(schedule_start)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(schedule_last)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(schedule_end)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(event_range)({(?P<date>[^{}]*)})?({(?P<time>[^{}]*)})?%/',
			'/%(event_venue)%/',
			'/%(event_venue_url)%/',
			'/%(event_cats)%/',
			'/%(event_tags)%/',
			'/%(event_venue_address)%/',
			'/%(event_venue_postcode)%/',
			'/%(event_venue_city)%/',
			'/%(event_venue_country)%/',
			'/%(event_venue_state)%/',
			'/%(event_venue_city)%/',
			'/%(event_organiser)%/',
			'/%(event_thumbnail)(?:{([^{}]+)})?(?:{([^{}]+)})?%/',
			'/%(event_url)%/',
			'/%(event_custom_field){([^{}]+)}%/',
			'/%(event_venue_map)({[^{}]+})?%/',
			'/%(event_excerpt)(?:{(\d+)})?%/',
			'/%(cat_color)%/',
			'/%(event_title_attr)%/',
			'/%(event_duration){([^{}]+)}%/',
			'/%(event_content)%/',
		);
		$template = preg_replace_callback($patterns, array(__CLASS__,'parse_template'), $template);
		return $template;
	}
	
	static function parse_template($matches){
		global $post;
		$replacement='';

		switch($matches[1]):
			case 'event_title':
				$replacement = get_the_title();
				break;

			case 'start':
			case 'end':
			case 'schedule_start':
			case 'schedule_last':
			case 'schedule_end':
				$defaults = array(
					'date' => get_option('date_format'),
					'time' => get_option('time_format'),
				);
				$formats = array_merge( $defaults, $matches );
				$format = eo_get_event_datetime_format( get_the_ID(), $formats['date'], $formats['time'] );

				switch( $matches[1] ):
					case 'start':
						$replacement = eo_get_the_start( $format );
					break;
					case 'end':
						$replacement = eo_get_the_end( $format );
					break;
					case 'schedule_start':
						$replacement = eo_get_schedule_start( $format );
					break;
					case 'schedule_last':
					case 'schedule_end':
						$replacement = eo_get_schedule_end( $format );
					break;
				endswitch;
				break;

			case 'event_duration':
				$start = eo_get_the_start( DATETIMEOBJ );
				$end   = clone eo_get_the_end( DATETIMEOBJ );
				if ( eo_is_all_day() ) {
					$end->modify( '+1 minute' );
				}
				if ( function_exists( 'date_diff' ) ) {
					$duration = date_diff( $start, $end );
					$replacement = $duration->format( $matches[2] );
				} else {
					$replacement = eo_date_interval( $start,$end, $matches[2] );
				}
				break;

			case 'event_range':
				$defaults = array(
					'date' => get_option('date_format'),
					'time' => get_option('time_format'),
				);
				$formats = array_merge( $defaults, $matches );
				$replacement = eo_format_event_occurrence( get_the_ID(), eo_get_the_occurrence_id(), $formats['date'], $formats['time'] );
				break;

				$start = eo_get_the_start( DATETIMEOBJ );
				$end   = clone eo_get_the_end( DATETIMEOBJ );
				if ( eo_is_all_day() ) {
					$end->modify( '+1 minute' );
				}
				if ( function_exists( 'date_diff' ) ) {
					$duration = date_diff( $start, $end );
					$replacement = $duration->format( $matches[2] );
				} else {
					$replacement = eo_date_interval( $start,$end, $matches[2] );
				}
				break;

			case 'event_organiser':
				$event = get_post();
				$event_organiser = get_user_by( 'id', (int) $event->post_author );
				$replacement = $event_organiser->display_name;
				break;
			case 'event_tags':
				$replacement = get_the_term_list( get_the_ID(), 'event-tag', '', ', ','');
				break;

			case 'event_cats':
				$replacement = get_the_term_list( get_the_ID(), 'event-category', '', ', ','');
				break;

			case 'event_venue':
				$replacement =eo_get_venue_name();
				break;

			case 'event_venue_map':
				if(eo_get_venue()){
					$class = (isset($matches[2]) ? self::eo_clean_input($matches[2]) : '');
					$class = (!empty($class) ?  'class='.$class : '');
					$replacement =  eo_get_venue_map( eo_get_venue(), compact('class') );
				}
				break;

			case 'event_venue_url':
				$venue_link =eo_get_venue_link();
				$replacement = ( !is_wp_error($venue_link) ? $venue_link : '');
				break;
			case 'event_venue_address':
				$address = eo_get_venue_address();
				$replacement =$address['address'];
				break;
			case 'event_venue_postcode':
				$address = eo_get_venue_address();
				$replacement =$address['postcode'];
				break;
			case 'event_venue_city':
				$address = eo_get_venue_address();
				$replacement =$address['city'];
				break;
			case 'event_venue_country':
				$address = eo_get_venue_address();
				$replacement =$address['country'];
				break;
			case 'event_venue_state':
                                $address = eo_get_venue_address();
                                $replacement =$address['state'];
                                break;
			case 'event_venue_city':
                                $address = eo_get_venue_address();
                                $replacement =$address['city'];
                                break;
			case 'event_thumbnail':
				$size = (isset($matches[2]) ? self::eo_clean_input($matches[2]) : '');
				$size = (!empty($size) ?  $size : 'thumbnail');
				$attr = (isset($matches[3]) ? self::eo_clean_input($matches[3]) : '');

				//Decode HTML entities as shortcode encodes them
				$attr = html_entity_decode($attr);
				$replacement = get_the_post_thumbnail(get_the_ID(),$size, $attr);
				break;
			case 'event_url':
				$replacement = eo_get_permalink();
				break;
			case 'event_custom_field':
				$field = $matches[2];
				$meta = get_post_meta(get_the_ID(), $field);
				$replacement =  implode($meta);
				break;
			case 'event_excerpt':
				$length = ( isset( $matches[2] ) ? intval( $matches[2] ) : 55 );
				//Using get_the_excerpt adds a link....
				if ( post_password_required( $post ) ) {
					$output = __( 'There is no excerpt because this is a protected post.', 'eventorganiser' );
				} else {
					$output = $post->post_excerpt;
				}
				$replacement = eventorganiser_trim_excerpt( $output, $length );
				break;
			case 'event_content':
				$replacement = get_the_content();
				break;
			case 'cat_color':
				$replacement =  eo_get_event_color();
				break;
			case 'event_title_attr':
				$replacement = get_the_title();
				break;

		endswitch;
		return $replacement;
	}

	static function eo_clean_input($input){
		$input = trim($input,"{}"); //remove { }
		$input = str_replace(array("'",'"',"&#8221;","&#8216;", "&#8217;"),'',$input); //remove quotations
		return $input;
	}

	static function print_script() {

		if ( ! self::$add_script ) {
			return;
		}

		$load_users = $load_venues = $load_categories = $load_tags = false;

		if ( self::$calendars ) {
			foreach( self::$calendars as $calendar ) {
				if ( self::calendarHeadersContainCaseInsensitive( 'organiser', $calendar ) ) {
					$load_users = true;
				}
				if ( self::calendarHeadersContainCaseInsensitive( 'venue', $calendar ) ) {
					$load_venues = true;
				}
				if ( self::calendarHeadersContainCaseInsensitive( 'category', $calendar ) ) {
					$load_categories = true;
				}
				if ( self::calendarHeadersContainCaseInsensitive( 'tag', $calendar ) ) {
					$load_tags = true;
				}
			}
		}

		$fullcal = ( empty( self::$calendars ) ? array() : array(
			'firstDay' => intval( get_option( 'start_of_week' ) ),
		));

		if ( $load_venues ) {
			$fullcal['venues'] = get_terms( 'event-venue', array( 'hide_empty' => 0 ) );
		}

		if ( $load_categories ) {
			$fullcal['categories'] = get_terms( 'event-category', array( 'hide_empty' => 0 ) );
		}

		if ( $load_tags ) {
			$fullcal['tags'] = get_terms( 'event-tag', array( 'hide_empty' => 1 ) );
		}

		if ( $load_users ) {
			$fullcal['users'] = wp_list_pluck( get_users(), 'display_name', 'ID' );
		}

		eo_localize_script( 'eo_front', array(
			'ajaxurl'          => admin_url( 'admin-ajax.php' ),
			'calendars'        => self::$calendars,
			'widget_calendars' => self::$widget_calendars,
			'fullcal'          => $fullcal,
			'map'              => self::$map,
		));

		if ( ! empty( self::$calendars ) || ! empty( self::$map ) || ! empty( self::$widget_calendars ) ) {

			wp_enqueue_script( 'eo_qtip2' );
			wp_enqueue_script( 'eo_front' );

			eo_enqueue_style( 'eo_front' );
			eo_enqueue_style( 'eo_calendar-style' );

		}

		if ( ! empty( self::$map ) ) {
			wp_enqueue_script( 'eo_GoogleMap' );
			wp_enqueue_script( 'eo-open-source-map' );
			wp_enqueue_style( 'eo-open-source-map' );
		}
	}

	private static function calendarHeadersContainCaseInsensitive( $key, $calendar ) {
		foreach( array( 'headerleft', 'headerright', 'headercenter' ) as $header ) {
			$headers = array_map( 'strtolower',  preg_split( "/(,|\s)/", $calendar[$header] ) );
			if ( in_array( strtolower( $key ), $headers ) ) {
				return true;
			}
		}
		return false;
  }
}

EventOrganiser_Shortcodes::init();

/**
 * @ignore
 */
function eventorganiser_category_key($args=array(),$id=1){
		$args['taxonomy'] ='event-category';

		$html ='<div class="eo-fullcalendar-key" id="eo_fullcalendar_key'.$id.'">';
		$terms = get_terms( 'event-category', $args );
		$html.= "<ul class='eo_fullcalendar_key'>";
		foreach ($terms as $term):
			$slug = esc_attr($term->slug);
			$color = esc_attr($term->color);
			$class = "class='eo_fullcalendar_key_cat eo_fullcalendar_key_cat_{$slug}'";
			$html.= "<li {$class}><span class='eo_fullcalendar_key_colour' style='background:{$color}'>&nbsp;</span>".esc_attr($term->name)."</li>";			
		endforeach;
		$html.='</ul></div>';

		return $html;
	}
?>
