<?php
namespace Wpmet\Libs;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\Wpmet\Libs\Stories' ) ) :

	class Stories {

		protected $script_version = '1.1.1';

		protected $key = 'wpmet_stories';
		protected $data;
		protected $title;
		protected $plugin_link = array();
		protected $last_check; 
		protected $check_interval = ( 3600 * 6 );

		protected $plugin_screens;

		protected $text_domain;
		protected $filter_string;
		protected $api_url;

		private $stories;

		/**
		 * Get version of this script
		 *
		 * @return string Version name
		 */
		public function get_version() {
			return $this->script_version;
		}

		/**
		 * Get current directory path
		 *
		 * @return string
		 */
		public function get_script_location() {
			return __FILE__;
		}

		public function set_plugin( $link_title, $weblink = 'https://wpmet.com/' ) {
			$this->plugin_link[] = array( $link_title, $weblink );

			return $this;
		}

		public function call() {
			add_action( 'wp_dashboard_setup', array( $this, 'show_story_widget' ), 111 );
		}

		private function in_whitelist( $conf, $list ) {

			$match = $conf->data->whitelist;

			if ( empty( $match ) ) {

				return true;
			};

			$match_arr = explode( ',', $match );

			foreach ( $list as $word ) {
				if ( in_array( $word, $match_arr ) ) {

					return true;
				}
			}

			return false;
		}

		private function in_blacklist( $conf, $list ) {

			$match = $conf->data->blacklist;

			if ( empty( $match ) ) {

				return false;
			};

			$match_arr = explode( ',', $match );

			foreach ( $match_arr as $idx => $item ) {

				$match_arr[ $idx ] = trim( $item );
			}

			foreach ( $list as $word ) {
				if ( in_array( $word, $match_arr ) ) {

					return true;
				}
			}

			return false;
		}

		public function set_title( $title = '' ) {
			$this->title = $title;

			return $this;
		}

		public function is_test( $is_test = false ) {

			if ( $is_test === true ) {
				$this->check_interval = 1;
			}

			return $this;
		}

		public function set_text_domain( $text_domain ) {

			$this->text_domain = $text_domain;

			return $this;
		}

		public function set_filter( $filter_string ) {

			$this->filter_string = $filter_string;

			return $this;
		}

		public function set_api_url( $url ) {

			$this->api_url = $url;

			return $this;
		}

		public function set_plugin_screens( $screen ) {

			$this->plugin_screens[] = $screen;

			return $this;
		}

		private function set_stories( $story ) {
			$filter = array( $this->text_domain );
			foreach ( get_option( 'active_plugins' ) as $plugin ) {
				$temp = pathinfo( $plugin );
				if ( ! empty( $temp ) ) {
					$filter[] = trim( $temp['filename'] );
				}
			}

			if ( isset( $this->stories[ $story->id ] ) ) {
				return;
			}
			
			// if start and endtime is set, check current time is inside the timeframe
			if ( ( ! empty( $story->start ) && ! empty( $story->end ) ) && ( intval( $story->start ) > time() || intval( $story->end ) < time() ) ) {
				return;
			}

			if ( empty( array_intersect( $filter, $story->plugins ) ) ) {
				return;
			}

			$this->stories[ $story->id ] = array(
				'id'          => $story->id,
				'title'       => $story->title,
				'description' => $story->description,
				'type'        => $story->type,
				'priority'    => $story->priority,
				'story_link'  => $story->data->story_link,
				'story_image' => $story->data->story_image,
			);
		}

		private function get_stories() {
			$this->data = get_option( $this->text_domain . '__stories_data' );
			$this->data = $this->data == '' ? array() : $this->data;

			$this->last_check = get_option( $this->text_domain . '__stories_last_check' );

			$this->last_check = empty( $this->last_check ) ? 0 : $this->last_check;

			if ( ( $this->check_interval + $this->last_check ) < time() ) {
				$response = wp_remote_get(
					$this->api_url . 'cache/stories.json?nocache=' . time(),
					array(
						'timeout'     => 10,
						'httpversion' => '1.1',
					)
				);

				if ( ! is_wp_error( $response ) && isset( $response['body'] ) && $response['body'] != '' ) {
					
					$response = json_decode( $response['body'] );
					
					if ( ! empty( $response ) ) {
						$this->data = $response;
	
						update_option( $this->text_domain . '__stories_last_check', time() );
						update_option( $this->text_domain . '__stories_data', $this->data );
					}

					return;
				}
			}
		}
		
		public function show_story_widget() {
			$this->get_stories();

			if ( ! empty( $this->data->error ) ) {

				return;
			}

			if ( empty( $this->data ) ) {

				return;
			}

			$list = array();

			if ( ! empty( $this->filter_string ) ) {

				$list = explode( ',', $this->filter_string );

				foreach ( $list as $idx => $item ) {
					$list[ $idx ] = trim( $item );
				}
				$list = array_filter( $list );
			}

			foreach ( $this->data as $story ) {
				
				if ( ! empty( $list ) && $this->in_blacklist( $story, $list ) ) {
					
					continue;
				}
				
				$this->set_stories( $story );
			}

			if ( empty( $this->stories ) ) {
				return;
			}

			$this->title = ( isset( $this->title ) && ! empty( $this->title ) ? $this->title . ' ' : '' ) . 'Stories';

			wp_add_dashboard_widget( 'wpmet-stories', __( 'Wpmet Stories', 'elementskit-lite' ), array( $this, 'show' ) );

			// Move our widget to top.
			global $wp_meta_boxes;

			$dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
			$ours      = array(
				'wpmet-stories' => $dashboard['wpmet-stories'],
			);

			$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( $ours, $dashboard );
		}

		public function show() {
			usort(
				$this->stories,
				function ( $a, $b ) {
					if ( $a['priority'] == $b['priority'] ) {
						return 0;
					}
					return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
				}
			);
			include_once 'views/template.php';
		}

		/**
		 * Crosscheck if Story library will be shown at current WP admin page or not
		 *
		 * @param string $b_screen
		 * @param string $screen_id
		 * 
		 * @return boolean
		 */
		public function is_correct_screen_to_show( $b_screen, $screen_id ) {

			if ( in_array( $b_screen, array( $screen_id, 'all_page' ) ) ) {

				return true;
			}

			if ( $b_screen == 'plugin_page' ) {

				return in_array( $screen_id, $this->plugin_screens );
			}

			return false;
		}

		/**
		 * Define singleton instance
		 *
		 * @var [type]
		 */
		private static $instance;

		public static function instance( $text_domain = '' ) {
		
			if ( ! self::$instance ) {
				self::$instance = new static();            
			}
	
			return self::$instance->set_text_domain( $text_domain );
		}
	}

endif;
