<?php
/**
 * Header class class
 *
 * @package Sydney
 */

if ( !class_exists( 'Sydney_Header' ) ) :
	Class Sydney_Header {

		/**
		 * Instance
		 */		
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( get_option( 'sydney-update-header' ) ) {
				add_action( 'sydney_header', array( $this, 'header_markup' ) );
				add_action( 'sydney_header', array( $this, 'header_mobile_markup' ) );
			} else {
				add_action( 'sydney_header', array( $this, 'header_legacy' ) );
			}

			//add_action( 'sydney_header', array( $this, 'header_image' ) );
		}

		/**
		 * Core header image
		 */
		public function header_image() {
			echo '<div class="header-image">';
				the_header_image_tag();
			echo '</div>';
		}

		/**
		 * Desktop header markup
		 */
		public function header_markup() {
			$layout = get_theme_mod( 'header_layout_desktop', 'header_layout_2' );
			?>

			<?php call_user_func( array( $this, $layout ) ); ?>
			<?php
		}

		/**
		 * Mobile header markup
		 */		
		public function header_mobile_markup() {
			$layout = get_theme_mod( 'header_layout_mobile', 'header_mobile_layout_1' );
			$offcanvas_header 				= get_theme_mod( 'offcanvas_header', 'branding' );
			$offcanvas_header_custom_text 	= get_theme_mod( 'offcanvas_header_custom_text', esc_html__( 'Menu', 'sydney' ) );
			?>

			<div class="sydney-offcanvas-menu">
				<div class="mobile-header-item">
					<div class="row valign">
						<div class="col-xs-8">
							<?php 
							if ( 'branding' === $offcanvas_header ) {
								$this->logo();
							} elseif ( 'custom' === $offcanvas_header ) {
								echo '<div class="offcanvas-header-custom-text">' . esc_html( $offcanvas_header_custom_text ) . '</div>';
							}
							?>
						</div>
						<div class="col-xs-4 align-right">
							<a class="mobile-menu-close" href="#"><i class="sydney-svg-icon icon-cancel"><span class="screen-reader-text"><?php echo esc_html__( 'Close menu', 'sydney' ); ?></span><?php sydney_get_svg_icon( 'icon-cancel', true ); ?></i></a>
						</div>
					</div>
				</div>
				<div class="mobile-header-item">
					<?php if ( has_nav_menu( 'mobile' ) ) {
						$this->mobile_menu();
					} else {
						$this->menu();
					} ?>
				</div>
				<div class="mobile-header-item offcanvas-items">
					<?php $this->render_components( 'offcanvas' ); ?>
				</div>				
			</div>
			
			<?php call_user_func( array( $this, $layout ) ); ?>
			<?php
		}
		
		/**
		 * Desktop: header layout 1
		 */
		public function header_layout_1() {
			$layout 	= get_theme_mod( 'header_layout_desktop', 'header_layout_2' );
			$container 	= get_theme_mod( 'header_container', 'container' );
			?>
				<header id="masthead" class="main-header <?php echo esc_attr( $layout ); ?> <?php echo esc_attr( $this->sticky() ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="site-header-inner">
							<div class="row valign">
								<div class="col-md-5">
									<?php $this->menu(); ?>
								</div>
								<div class="col-md-2">
									<?php $this->logo( $context = 'main' ); ?>
								</div>
								<div class="col-md-5 header-elements">
									<?php $this->render_components( 'l1' ); ?>
								</div>							
							</div>
						</div>
					</div>
					<?php $this->search_form(); ?>
				</header>
			<?php
		}

		/**
		 * Desktop: header layout 2
		 */
		public function header_layout_2() {
			$layout 		= get_theme_mod( 'header_layout_desktop', 'header_layout_2' );
			$container 		= get_theme_mod( 'header_container', 'container' );
			$menu_position 	= get_theme_mod( 'main_header_menu_position', 'right' );
			?>
				<header id="masthead" class="main-header <?php echo esc_attr( $layout ); ?> <?php echo esc_attr( $this->sticky() ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="site-header-inner">
							<div class="row valign">
								<div class="header-col">
									<?php $this->logo( $context = 'main' ); ?>
								</div>
								<div class="header-col menu-col menu-<?php echo esc_attr( $menu_position ); ?>">
									<?php $this->menu(); ?>
								</div>							
								<div class="header-col header-elements">
									<?php $this->render_components( 'l1' ); ?>
								</div>							
							</div>
						</div>
					</div>
					<?php $this->search_form(); ?>
				</header>
			<?php
		}
		
		/**
		 * Desktop: header layout 3
		 */
		public function header_layout_3() {
			$layout 	= get_theme_mod( 'header_layout_desktop', 'header_layout_2' );
			$container 	= get_theme_mod( 'header_container', 'container' );

			global $post;
			$transparent_menu = '';
			if ( isset( $post ) ) {
				$transparent_menu = get_post_meta( $post->ID, '_sydney_transparent_menu', true );
			}
			$transparent_header = sydney_get_display_conditions( 'transparent_header', false );

			?>

			<?php if ( $transparent_menu || $transparent_header ) : ?>
				<div class="header-wrapper">
				<?php endif; ?>
				<header id="masthead" class="main-header <?php echo esc_attr( $layout ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="top-header-row">
							<div class="row valign">
								<div class="col-md-4 header-elements header-elements-left">
									<?php $this->render_components( 'l3left' ); ?>
								</div>
								<div class="col-md-4">
									<?php $this->logo( $context = 'main' ); ?>
								</div>							
								<div class="col-md-4 header-elements">
									<?php $this->render_components( 'l3right' ); ?>
								</div>							
							</div>
						</div>	
					</div>	
					<?php $this->search_form(); ?>
				</header>
				<div class="bottom-header-row bottom-<?php echo esc_attr( $layout ); ?> <?php echo esc_attr( $this->sticky() ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="bottom-header-inner">
							<div class="row valign">
								<div class="col-md-12">
								<?php $this->menu(); ?>
								</div>
							</div>
						</div>
					</div>	
				</div>	
				<?php if ( $transparent_menu || $transparent_header ) : ?>
				</div>
				<?php endif; ?>							
			<?php
		}
		
		/**
		 * Desktop: header layout 4
		 */
		public function header_layout_4() {
			$layout 	= get_theme_mod( 'header_layout_desktop', 'header_layout_2' );
			$container 	= get_theme_mod( 'header_container', 'container' );

			global $post;
			$transparent_menu = '';
			if ( isset( $post ) ) {
				$transparent_menu = get_post_meta( $post->ID, '_sydney_transparent_menu', true );
			}			
			$transparent_header = sydney_get_display_conditions( 'transparent_header', false );

			?>
				<?php if ( $transparent_menu || $transparent_header ) : ?>
				<div class="header-wrapper">
				<?php endif; ?>			
				<header id="masthead" class="main-header <?php echo esc_attr( $layout ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="top-header-row">
							<div class="row valign">
								<div class="col-md-4">
									<?php $this->logo( $context = 'main' ); ?>
								</div>
								<div class="col-md-8 header-elements">
									<?php $this->render_components( 'l4top' ); ?>
								</div>							
						
							</div>
						</div>	
					</div>	
					<?php $this->search_form(); ?>
				</header>
				<div class="bottom-header-row bottom-<?php echo esc_attr( $layout ); ?> <?php echo esc_attr( $this->sticky() ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="bottom-header-inner">
							<div class="row valign">
								<div class="col-md-8">
									<?php $this->menu(); ?>
								</div>
								<div class="col-md-4 header-elements">
									<?php $this->render_components( 'l4bottom' ); ?>
								</div>									
							</div>
						</div>
					</div>	
				</div>		
				<?php if ( $transparent_menu || $transparent_header ) : ?>
				</div>
				<?php endif; ?>						
			<?php
		}	
		
		/**
		 * Desktop: header layout 5
		 */
		public function header_layout_5() {
			$layout 	= get_theme_mod( 'header_layout_desktop', 'header_layout_2' );
			$container 	= get_theme_mod( 'header_container', 'container' );

			global $post;
			$transparent_menu = '';
			if ( isset( $post ) ) {
				$transparent_menu = get_post_meta( $post->ID, '_sydney_transparent_menu', true );
			}		
			$transparent_header = sydney_get_display_conditions( 'transparent_header', false );		
			?>
				<?php if ( $transparent_menu || $transparent_header ) : ?>
				<div class="header-wrapper">
				<?php endif; ?>					
				<header id="masthead" class="main-header <?php echo esc_attr( $layout ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="top-header-row">
							<div class="row valign">
								<div class="col-md-4 header-elements header-elements-left">
									<?php $this->render_components( 'l5topleft' ); ?>
								</div>
								<div class="col-md-4">
									<?php $this->logo( $context = 'main' ); ?>
								</div>							
								<div class="col-md-4 header-elements">
									<?php $this->render_components( 'l5topright' ); ?>
								</div>							
							</div>
						</div>	
					</div>		
					<?php $this->search_form(); ?>
				</header>
				<div class="bottom-header-row bottom-<?php echo esc_attr( $layout ); ?> <?php echo esc_attr( $this->sticky() ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="bottom-header-inner">
							<div class="row valign">
								<div class="col-md-8">
									<?php $this->menu(); ?>
								</div>
								<div class="col-md-4 header-elements">
									<?php $this->render_components( 'l5bottom' ); ?>
								</div>									
							</div>
						</div>
					</div>	
				</div>	
				<?php if ( $transparent_menu || $transparent_header ) : ?>
				</div>
				<?php endif; ?>							
			<?php
		}			


		/**
		 * Mobile: layout 1
		 */		
		public function header_mobile_layout_1() {
			$container = get_theme_mod( 'header_container', 'container-fluid' );
			?>
				<header id="masthead-mobile" class="main-header mobile-header <?php echo esc_attr( $this->sticky( 'mobile' ) ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="row valign">
							<div class="col-sm-4 col-grow-mobile">
								<?php $this->logo(); ?>
							</div>
							<div class="col-sm-8 col-grow-mobile header-elements valign align-right">
								<?php $this->render_components( 'mobile' ); ?>
								<?php $this->trigger(); ?>
							</div>						
						</div>
					</div>
					<?php $this->search_form(); ?>
				</header>
			<?php
		}	

		/**
		 * Mobile: layout 2
		 */		
		public function header_mobile_layout_2() {
			$container = get_theme_mod( 'header_container', 'container-fluid' );
			?>
				<header id="masthead-mobile" class="main-header mobile-header <?php echo esc_attr( $this->sticky( 'mobile' ) ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="row valign">
							<div class="col-sm-4 col-xs-4 header-elements valign">
								<?php $this->render_components( 'mobile' ); ?>
							</div>							
							<div class="col-sm-4 col-xs-4 align-center">
								<?php $this->logo(); ?>
							</div>
							<div class="col-sm-4 col-xs-4 align-right">
								<?php $this->trigger(); ?>
							</div>						
						</div>
					</div>
					<?php $this->search_form(); ?>
				</header>
			<?php
		}	

		/**
		 * Mobile: layout 3
		 */		
		public function header_mobile_layout_3() {
			$container = get_theme_mod( 'header_container', 'container-fluid' );
			?>
				<header id="masthead-mobile" class="main-header mobile-header <?php echo esc_attr( $this->sticky( 'mobile' ) ); ?>">
					<div class="<?php echo esc_attr( $container ); ?>">
						<div class="row valign">
							<div class="col-sm-4 col-xs-4">
								<?php $this->trigger(); ?>
							</div>														
							<div class="col-sm-4 col-xs-4 align-center">
								<?php $this->logo(); ?>
							</div>
							<div class="col-sm-4 col-xs-4 header-elements valign align-right">
								<?php $this->render_components( 'mobile' ); ?>
							</div>						
						</div>
					</div>
					<?php $this->search_form(); ?>
				</header>
			<?php
		}			
				
		/**
		 * Render header components
		 */
		public function render_components( $location ) {
			$defaults 	= sydney_get_default_header_components();
			$components = get_theme_mod( 'header_components_' . $location, $defaults[$location] );

			foreach ( $components as $component ) {
				call_user_func( array( $this, $component ) );
			}
		}

		/**
		 * Social icons
		 */
		public function social() {
			echo '<div class="header-item header-social">';
				sydney_social_profile( 'social_profiles_header' );
			echo '</div>';
		}

		/**
		 * Main navigation
		 */
		public function menu() {
			if ( function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled( 'primary' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'primary') ); ?>
			<?php else: ?>	
			<nav id="mainnav" class="mainnav">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
					)
				);
				?>
			</nav>
			<?php endif;
		}

		/**
		 * Mobile navigation
		 */
		public function mobile_menu() {
			?>
			<nav id="mainnav" class="mainnav">
				<?php			
				wp_nav_menu(
					array(
						'theme_location' => 'mobile',
						'menu_id'        => 'primary-menu',
						'walker'         => apply_filters( 'sydney_primary_wp_nav_menu_walker', '' )
					)
				);
				?>
			</nav>
			<?php
		}		

		/**
		 * Button
		 */
		public function button() {
			$text 	= get_theme_mod( 'header_button_text', esc_html__( 'Click me', 'sydney' ) );
			$url	= get_theme_mod( 'header_button_link', '#' );
			$newtab = get_theme_mod( 'header_button_newtab', 0 );
			$open	= '';
			if ( $newtab ) {
				$open = 'target="_blank"';
			}

			?>
				<a <?php echo esc_html( $open ); ?> class="button roll-button header-item" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $text ); ?></a>
			<?php
		}

		/**
		 * Contact info
		 */
		public function contact_info() {
			$email 	= get_theme_mod( 'header_contact_mail', esc_html__( 'office@example.org', 'sydney' ) );
			$phone	= get_theme_mod( 'header_contact_phone', esc_html__( '111222333', 'sydney' ) );

			?>
				<div class="header-item header-contact">
					<?php if ( $email ) : ?>
						<a class="header-contact-email" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><i class="sydney-svg-icon"><?php sydney_get_svg_icon( 'icon-mail', true ); ?></i><span><?php echo esc_html( antispambot( $email ) ); ?></span></a>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<a class="header-contact-phone" href="tel:<?php echo esc_attr( $phone ); ?>"><i class="sydney-svg-icon"><?php sydney_get_svg_icon( 'icon-phone', true ); ?></i><span><?php echo esc_html( $phone ); ?></span></a>
					<?php endif; ?>					
				</div>
			<?php
		}		

		/**
		 * Woocommerce icons
		 */
		function woocommerce_icons() {

			if ( !class_exists( 'WooCommerce' ) ) {
				return;
			}
			
			echo sydney_woocommerce_header_cart(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}		

		/**
		 * Search icon
		 */
		public function search() {
			?>
				<a href="#" class="header-search header-item">
					<span class="screen-reader-text"><?php echo esc_html__( 'Search', 'sydney' ); ?></span>
					<i class="sydney-svg-icon icon-search active"><?php sydney_get_svg_icon( 'icon-search', true ); ?></i>
					<i class="sydney-svg-icon icon-cancel"><?php sydney_get_svg_icon( 'icon-cancel', true ); ?></i>
				</a>
			<?php
		}

		/**
		 * Search form
		 */
		public function search_form() {
			?>
			<div class="header-search-form">
			<?php
				if ( class_exists( 'DGWT_WC_Ajax_Search' ) ) {
					echo do_shortcode('[wcas-search-form]');
				} else {
					get_search_form();
				}
			?>
			</div>
			<?php
		}

		/**
		 * Site branding
		*/		
		public function logo( $context = false ) {
			$logo_site_title	= get_theme_mod('logo_site_title', 0);
			?>
			<div class="site-branding">

				<?php if ( get_theme_mod('site_logo') ) : ?>
					<?php
						$logo_id 	= attachment_url_to_postid( get_theme_mod( 'site_logo' ) );
						$logo_attrs = wp_get_attachment_image_src( $logo_id, 'large' );
					?>						
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name'); ?>"><img width="<?php echo esc_attr( $logo_attrs[1] ); ?>" height="<?php echo esc_attr( $logo_attrs[2] ); ?>" class="site-logo" src="<?php echo esc_url(get_theme_mod('site_logo')); ?>" alt="<?php bloginfo('name'); ?>" <?php sydney_do_schema( 'logo' ); ?> /></a>
				
					<?php if ( $logo_site_title ) : //display site title and description with the logo
						echo '<div class="site-branding-text">';
						$this->site_title_desc();
						echo '</div>';
					elseif ( is_home() && 'main' == $context ) : ?>
						<h1 class="screen-reader-text"><?php bloginfo( 'name' ); ?></h1>
					<?php endif; ?>
				<?php else : ?>
					<?php $this->site_title_desc(); ?>
				<?php endif; ?>	
			</div><!-- .site-branding -->
			<?php
		}

		/**
		 * Site title and description
		 */
		public function site_title_desc() {
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$sydney_description = get_bloginfo( 'description', 'display' );
			if ( $sydney_description || is_customize_preview() ) :
				?>
				<p class="site-description"><?php echo $sydney_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
			<?php endif;
		}		

		/**
		 * Mobile menu trigger
		 */
		public function trigger() { ?>
			<?php if ( function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled( 'primary' ) ) : ?>
				<?php wp_nav_menu( array( 'theme_location' => 'primary') ); ?>
			<?php else: ?>	
				<?php $icon = get_theme_mod( 'mobile_menu_icon', 'mobile-icon2' ); ?>
				<a href="#" class="menu-toggle">
					<span class="screen-reader-text"><?php echo esc_html__( 'Toggle menu', 'sydney' ); ?></span>
					<i class="sydney-svg-icon"><?php sydney_get_svg_icon( $icon, true ); ?></i>
				</a>
			<?php endif;
		}

		/**
		 * Sticky mode
		 */
		public function sticky( $device = 'desktop' ) {
			$enabled 	= get_theme_mod( 'enable_sticky_header', 1 );

			if ( $device == 'mobile' ) {
				$enabled = get_theme_mod( 'enable_sticky_header_mobile', 0 );
			}

			$type 		= get_theme_mod( 'sticky_header_type', 'always' );
			$sticky		= '';

			if ( $enabled ) {
				$sticky = 'sticky-header sticky-' . esc_html( $type );
			}

			return $sticky;
		}

		/**
		 * Legacy header
		 */
		public function header_legacy() {
			?>
			<header id="masthead" class="site-header" role="banner" <?php sydney_do_schema( 'header' ); ?>>
				<div class="header-wrap">
					<div class="<?php echo esc_attr( sydney_menu_container() ); ?>">
						<div class="row">
							<div class="col-md-4 col-sm-8 col-xs-12">
							<?php if ( get_theme_mod('site_logo') ) : ?>
								<?php
									$logo_id 	= attachment_url_to_postid( get_theme_mod( 'site_logo' ) );
									$logo_attrs = wp_get_attachment_image_src( $logo_id );
								?>						
								<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name'); ?>"><img width="<?php echo esc_attr( $logo_attrs[1] ); ?>" height="<?php echo esc_attr( $logo_attrs[2] ); ?>" class="site-logo" src="<?php echo esc_url(get_theme_mod('site_logo')); ?>" alt="<?php bloginfo('name'); ?>" <?php sydney_do_schema( 'logo' ); ?> /></a>
								<?php if ( is_home() && !is_front_page() ) : ?>
									<h1 class="site-title screen-reader-text"><?php bloginfo( 'name' ); ?></h1>
								<?php endif; ?>
							<?php else : ?>
								<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
								<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>	        
							<?php endif; ?>
							</div>
							<div class="col-md-8 col-sm-4 col-xs-12">
								<div class="btn-menu" <?php echo wp_kses_post( apply_filters( 'sydney_nav_toggle_data_attrs', '' ) ); ?>><span class="screen-reader-text"><?php echo esc_html__( 'Toggle menu', 'sydney' ); ?></span><i class="sydney-svg-icon"><?php sydney_get_svg_icon( 'icon-menu', true ); ?></i></div>
								<nav id="mainnav" class="mainnav" role="navigation" <?php sydney_do_schema( 'nav' ); ?> <?php echo wp_kses_post( apply_filters( 'sydney_nav_data_attrs', '' ) ); ?>>
									<?php wp_nav_menu( array( 'theme_location' => 'primary', 'fallback_cb' => 'sydney_menu_fallback' ) ); ?>
								</nav><!-- #site-navigation -->
							</div>
						</div>
					</div>
				</div>
			</header><!-- #masthead -->
			<?php
		}
	}

	/**
	 * Initialize class
	 */
	Sydney_Header::get_instance();

endif;