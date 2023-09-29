<?php
namespace Oxaim\Libs;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\Oxaim\Libs\Notice' ) ) :

	class Notice {

		/**
		 * scripts version
		 *
		 * @var string
		 */
		protected $script_version = '2.2.0';

		/**
		 * Unique ID to identify each notice
		 *
		 * @var string
		 */
		protected $notice_id;

		/**
		 * Plugin text-domain
		 *
		 * @var string
		 */
		protected $text_domain;

	
		/**
		 * Unique ID
		 *
		 * @var string
		 */
		protected $unique_id;

	
		/**
		 * Notice div container's class
		 *
		 * @var string
		 */
		protected $class;

	
		/**
		 * Single button's data
		 *
		 * @var array
		 */
		protected $button;

	
		/**
		 * Size class
		 *
		 * @var array
		 */
		protected $size;

	
		/**
		 * List of all buttons with it's config data
		 *
		 * @var array
		 */
		protected $buttons;

		/**
		 * Notice title
		 *
		 * @var string
		 */
		protected $title;

	
		/**
		 * Notice message
		 *
		 * @var string
		 */
		protected $message;

		/**
		 * Left logo
		 *
		 * @var string
		 */
		protected $logo;
		/**
		 * Container gutter
		 *
		 * @var string
		 */
		protected $gutter;

		/**
		 * Left logo style
		 *
		 * @var string
		 */
		protected $logo_style;


		/**
		 * Left logo style
		 *
		 * @var string
		 */
		protected $dismissible;

		protected $expired_time;


	
		/**
		 * html markup for notice
		 *
		 * @var string
		 */
		protected $html;

	
	
		/**
		 * style css for notice
		 *
		 * @var string
		 */
		protected $style_css;

	
	
		/**
		 * set if the notice will be shown to admins only
		 */
		protected $admin_only = true;

	
	
		/**
		 * get_version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->script_version;
		}
	

		/**
		 * get_script_location
		 *
		 * @return string
		 */
		public function get_script_location() {
			return __FILE__;
		}

		// config
	
		/**
		 * Configures all setter variables
		 *
		 * @param  string $prefix
		 * @return void
		 */
		public function config( $text_domain = '', $unique_id = '' ) {
			$this->text_domain = $text_domain;

			$this->unique_id = $unique_id;

			$this->notice_id = $text_domain . '-' . $unique_id;

			$this->dismissible = false; // false, user, global

			$this->expired_time = 1;

			$this->html = '';

			$this->title = '';
		
			$this->message = '';
		
			$this->class = '';

			$this->gutter = true;

			$this->logo = '';

			$this->logo_style = '';

			$this->size = array();

			$this->button = array(
				'default_class' => 'button',
				'class'         => 'button-secondary ', // button-primary button-secondary button-small button-large button-link
				'text'          => 'Button',
				'url'           => '#',
				'icon'          => '',
			);

			$this->buttons = array();

			return $this;
		}

		// setters begin
	
		/**
		 * Adds classes to the container
		 *
		 * @param  string $classname
		 * @return void
		 */
		public function set_class( $classname = '' ) {
			$this->class .= $classname;

			return $this;
		}
	
		public function set_type( $type = '' ) {
			$this->class .= ' notice-' . $type;

			return $this;
		}

		public function set_style_css( $style_css = '' ) {
			$this->style_css = $style_css;

			return $this;
		}

		public function set_button( $button = array() ) {
			$button          = array_merge( $this->button, $button );
			$this->buttons[] = $button;

			return $this;
		}

		public function set_id( $id ) {
			$this->notice_id = $id;
			return $this;
		}

		public function set_admin_only( $admin_only ) {
			$this->$admin_only = $admin_only;
			return $this;
		}

		public function set_title( $title = '' ) {
			$this->title .= $title;

			return $this;
		}

		public function set_message( $message = '' ) {
			$this->message .= $message;

			return $this;
		}

		public function set_gutter( $gutter = true ) {
			$this->gutter .= $gutter;
			$this->class  .= ( $gutter === true ? '' : ' no-gutter' );

			return $this;
		}

		public function set_logo( $logo = '', $logo_style = '' ) {
			$this->logo = $logo;

			$this->logo_style = $logo_style;

			return $this;
		}

		public function set_html( $html = '' ) {
			$this->html .= $html;

			return $this;
		}

		// setters ends


		// group getter
		public function get_data() {
			return array(
				'message' => $this->message,
				'title'   => $this->title,
				'buttons' => $this->buttons,
				'class'   => $this->class,
				'html'    => $this->html,
			);
		}




		public function call() {
			// check if current user is admin
			if (  ! current_user_can( 'manage_options' ) ) {
				return false;
			}
			add_action( 'admin_notices', array( $this, 'get_notice' ) );
		}
	
		public function get_notice() {
			// dismissible conditions
			if ( 'user' === $this->dismissible ) {
				$expired = get_user_meta( get_current_user_id(), $this->notice_id, true );
			} elseif ( 'global' === $this->dismissible ) {
				$expired = get_transient( $this->notice_id );
			} else {
				$expired = '';
			}

			global $oxaim_lib_notice_list;

			if ( ! isset( $oxaim_lib_notice_list[ $this->notice_id ] ) ) {
				$oxaim_lib_notice_list[ $this->notice_id ] = __FILE__;

				// is transient expired?
				if ( false === $expired || empty( $expired ) ) {
					$this->generate_html();
				}
			}
		}

		public function set_dismiss( $scope = 'global', $time = ( 3600 * 24 * 7 ) ) {
			$this->dismissible  = $scope;
			$this->expired_time = $time;
		
			return $this;
		}

		public function generate_html() {

			?>
		<div 
			id="<?php echo esc_attr( $this->notice_id ); ?>" 
			class="notice wpmet-notice notice-<?php echo esc_attr( $this->notice_id . ' ' . $this->class ); ?> <?php echo ( false === $this->dismissible ? '' : 'is-dismissible' ); ?>"

			expired_time="<?php echo esc_attr( $this->expired_time ); ?>"
			dismissible="<?php echo esc_attr( $this->dismissible ); ?>"
		>
			<?php if ( ! empty( $this->logo ) ) : ?>
				<img class="notice-logo" style="<?php echo esc_attr( $this->logo_style ); ?>" src="<?php echo esc_url( $this->logo ); ?>" />
			<?php endif; ?>

			<div class="notice-right-container <?php echo ( empty( $this->logo ) ? 'notice-container-full-width' : '' ); ?>">

				<style>
					<?php echo esc_html( $this->style_css); ?>
				</style>

				<?php if ( empty( $this->html ) ) : ?>
					<?php echo ( empty( $this->title ) ? '' : sprintf( '<div class="notice-main-title notice-vert-space">%s</div>', esc_html($this->title) ) ); ?>

					<div class="notice-message notice-vert-space">
					<?php echo wp_kses( $this->message, $this->get_kses_array() ); ?>
					</div>

					<?php if ( ! empty( $this->buttons ) ) : ?>
						<div class="button-container notice-vert-space">
							<?php foreach ( $this->buttons as $button ) : ?>
								<a id="<?php echo ( ! isset( $button['id'] ) ? '' : esc_attr($button['id']) ); ?>" href="<?php echo esc_url( $button['url'] ); ?>" class="wpmet-notice-button <?php echo esc_attr( $button['class'] ); ?>">
									<?php if ( ! empty( $button['icon'] ) ) : ?>
										<i class="notice-icon <?php echo esc_attr( $button['icon'] ); ?>"></i>
									<?php endif; ?>
									<?php echo esc_html( $button['text'] ); ?>
								</a>
								&nbsp;
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

				<?php else : ?>
					<?php echo wp_kses( $this->html, $this->get_kses_array() ); ?>
				<?php endif; ?>

			</div>

			<?php if ( false !== $this->dismissible ) : ?>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">x</span>
				</button>
			<?php endif; ?>

			<div style="clear:both"></div>

		</div>
			<?php
		}

		public static function init() {
			add_action( 'wp_ajax_wpmet-notices', array( __CLASS__, 'dismiss_ajax_call' ) );
			add_action( 'admin_head', array( __CLASS__, 'enqueue_scripts' ) );
		}

		public static function dismiss_ajax_call() {
			if( empty( $_POST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wpmet-notices' ) ){
				return false;
			}

			// check if current user is admin
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			$notice_id    = ( isset( $_POST['notice_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['notice_id'] ) ) : '';
			$dismissible  = ( isset( $_POST['dismissible'] ) ) ? sanitize_text_field( wp_unslash( $_POST['dismissible'] ) ) : '';
			$expired_time = ( isset( $_POST['expired_time'] ) ) ? sanitize_text_field( wp_unslash( $_POST['expired_time'] ) ) : '';

			if ( ! empty( $notice_id ) ) {
				if ( 'user' === $dismissible ) {
					update_user_meta( get_current_user_id(), $notice_id, true );
				} else {
					set_transient( $notice_id, true, $expired_time );
				}

				wp_send_json_success();
			}

			wp_send_json_error();
		}
	
		public static function enqueue_scripts() {
			// check if current user is admin
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			echo "
			<script>
                jQuery(document).ready(function ($) {
                    $( '.wpmet-notice.is-dismissible' ).on( 'click', '.notice-dismiss', function() {

                        _this 		        = $( this ).parents('.wpmet-notice').eq(0);
                        var notice_id 	    = _this.attr( 'id' ) || '';
                        var expired_time 	= _this.attr( 'expired_time' ) || '';
                        var dismissible 	= _this.attr( 'dismissible' ) || '';
                        var x               = $( this ).attr('class');

                        // console.log({
                        //     _this, x, notice_id, expired_time, dismissible
                        // });
                        // return;

                        _this.hide();

                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action 	        : 'wpmet-notices',
                                notice_id       : notice_id,
                                dismissible 	: dismissible,
                                expired_time 	: expired_time,
								nonce 			: '" . esc_js( wp_create_nonce( 'wpmet-notices' ) ) . "'
                            },
                        });
                    });
                });
            </script>
            <style>
                .wpmet-notice{
                    margin-bottom: 15px;
                    padding: 0!important;
                    display: flex;
                    flex-direction: row;
                    justify-content: flex-start;
                    align-items: center;
                }

                .wpmet-notice .notice-right-container{
                    margin: .7rem .8rem .8rem;
                }

                .notice-container-full-width{
                    width:100%!important;
                }
                
                .wpmet-notice.no-gutter{
                    padding: 0!important;
                    border-width: 0!important;
                }
                .wpmet-notice.no-gutter .notice-right-container{
                    padding: 0!important;
                    margin: 0!important;
                }

                .notice-right-container .notice-vert-space{
                    margin-bottom: .8rem;
                }

                .notice-right-container .notice-vert-space:last-child,
                .notice-right-container .notice-vert-space:only-child{
                    margin-bottom: 0;
                }

                .wpmet-notice .notice-logo{
                    padding: 3px;
                    max-width: 110px;
                    max-height: 110px;
                }
                
                .wpmet-notice-button {
                    text-decoration:none;
                }
                
                .wpmet-notice-button > i{
                    margin-right: 3px;
                }
                
                .wpmet-notice-button .notice-icon{
                    display:inline-block;
                }

                .wpmet-notice-button .notice-icon:before{
                    vertical-align: middle!important;
                    margin-top: -1px;
                }

                .wpmet-notice .notice-main-title{
                    color: #1d2327;
                    font-size: 1.2rem;
                }
             
            </style>
		";
		}
	

		private static $instance;
	
		/**
		 * Method: instance -> Return Notice module class instance
		 *
		 * @param string|null $text_domain
		 * @param string|null $unique_id
		 * @return mixed
		 */
		public static function instance( $text_domain = null, $unique_id = null ) {
			if ( $text_domain == null ) {
				return false;
			}

			self::$instance = new self();

			return self::$instance->config( $text_domain, ( is_null( $unique_id ) ? uniqid() : $unique_id ) );
		}

		public function get_kses_array(){
			return array(
				'a'                             => array(
					'class'  => array(),
					'href'   => array(),
					'rel'    => array(),
					'title'  => array(),
					'target' => array(),
					'style'  => array(),
				),
				'b'                             => array(
					'class' => array(),
					'style' => array(),
				),
				'div'                           => array(
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'strong'                        => array(
					'class' => array(),
					'style' => array(),
				),
				'h1'                            => array(
					'class' => array(),
					'style' => array(),
				),
				'h2'                            => array(
					'class' => array(),
					'style' => array(),
				),
				'h3'                            => array(
					'class' => array(),
					'style' => array(),
				),
				'h4'                            => array(
					'class' => array(),
					'style' => array(),
				),
				'i'                             => array(
					'class' => array(),
					'style' => array(),
				),
				'img'                           => array(
					'alt'		=> array(),
					'class'		=> array(),
					'height'	=> array(),
					'src'		=> array(),
					'width'		=> array(),
					'style'		=> array(),
					'title'		=> array(),
					'srcset'	=> array(),
					'loading'	=> array(),
					'sizes'		=> array(),
				),
				'figure'                        => array(
					'class' => array(),
					'style' => array(),
				),
				'li'                            => array(
					'class' => array(),
					'style' => array(),
				),
				'ol'                            => array(
					'class' => array(),
					'style' => array(),
				),
				'p'                             => array(
					'class' => array(),
					'style' => array(),
				),
				'span'                          => array(
					'class' => array(),
					'title' => array(),
					'style' => array(),
				),
				'iframe'                        => array(
					'width'       => array(),
					'height'      => array(),
					'scrolling'   => array(),
					'frameborder' => array(),
					'allow'       => array(),
					'src'         => array(),
					'class' => array(),
					'style' => array(),
				),
				'strike'                        => array(),
				'br'                            => array(),
				'table'                         => array(),
				'ul'                            => array(
					'class' => array(),
					'style' => array(),
				),
				'svg'                           => array(
					'class'           => true,
					'aria-hidden'     => true,
					'aria-labelledby' => true,
					'role'            => true,
					'xmlns'           => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true, // <= Must be lower case!
					'preserveaspectratio' => true,
				)
			);
		}	
	}

endif;
