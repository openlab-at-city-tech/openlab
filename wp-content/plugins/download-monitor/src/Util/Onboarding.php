<?php

namespace Never5\DownloadMonitor\Util;

class Onboarding {

	/**
	 * Setup onboarding
	 */
	public function setup() {

		// add page
		add_action( 'admin_menu', array( $this, 'add_admin_page' ) );

		// add notice
		if ( false === get_option( 'dlm_hide_notice-onboarding' ) && ( ! isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && 'dlm_onboarding' != $_GET['page'] ) ) ) {
			add_action( 'admin_notices', array( $this, 'add_notice' ) );

			/*
			// notice JS -.-
			add_action( 'in_admin_footer', function () {
				?>
				<script type="text/javascript">
					jQuery( function ( $ ) {
						$( '.wpcm-notice' ).on( 'click', '.notice-dismiss', function ( event ) {
							$.get( '<?php echo Ajax\Manager::get_ajax_url( 'dismiss_notice' ); ?>', {
								id: $( this ).closest( '.wpcm-notice' ).data( 'id' ),
								nonce: '<?php echo wp_create_nonce( 'wpcm_ajax_nonce_dismiss_notice' ) ?>'
							}, function () {
							} );
						} );
					} );
				</script>
				<?php
			} );
			*/
		}

	}

	/**
	 * Add admin page
	 */
	public function add_admin_page() {
		// add page
		$menu_hook = \add_submenu_page( null, 'DLM_ONBOARDING', 'DLM_ONBOARDING', 'edit_posts', 'dlm_onboarding', array(
			$this,
			'page'
		) );

		// load onboarding assets
		add_action( 'load-' . $menu_hook, array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue onboarding assets
	 */
	public function enqueue_assets() {

		wp_enqueue_script(
			'dlm_onboarding',
			plugins_url( '/assets/js/onboarding' . ( ( ! SCRIPT_DEBUG ) ? '.min' : '' ) . '.js', download_monitor()->get_plugin_file() ),
			array( 'jquery' ),
			DLM_VERSION
		);

		wp_localize_script( 'dlm_onboarding', 'dlm_onboarding', array(
			'ajax_url_create_page' => \DLM_Ajax_Manager::get_ajax_url( 'create_page' ),
			'lbl_creating'         => __( 'Creating', 'download-monitor' ) . '...',
			'lbl_created'          => __( 'Page Created', 'download-monitor' ),
			'lbl_create_page'      => __( 'Create Page', 'download-monitor' ),
		) );

	}

	/**
	 * Onboarding notice
	 */
	public function add_notice() {
		?>
        <div class="notice notice-warning is-dismissible dlm-notice dlm-onboarding-notice" data-id="onboarding" data-nonce="<?php echo esc_attr( wp_create_nonce( 'dlm_hide_notice-onboarding' ) ); ?>" id="onboarding">
            <p><?php printf( esc_html__( 'Download Monitor is almost ready for use, %sclick here%s to finish the installation process.', 'download-monitor' ), '<a href="' . esc_url( admin_url( 'edit.php?post_type=dlm_download&page=dlm_onboarding' ) ) . '">', '</a>' ); ?></p>
        </div>
		<?php
	}

	/**
	 * Onboarding page
	 */
	public function page() {

		// been there, done that
		update_option( 'dlm_hide_notice-onboarding', 1 );

		/** @var \DLM_Settings_Helper $settings */
		$settings = download_monitor()->service( "settings" );

		// the actual page
		?>
        <div class="wrap dlm-onboarding">

            <div class="dlm-onboarding-section dlm-onboarding-section-one-col">
                <h2><?php echo esc_html__( "Welcome to ", 'download-monitor' ); ?> Download Monitor</h2>
                <p>
					<?php echo esc_html__( "Thank you for installing Download Monitor! We'd like to help you setup the plugin correctly so you can start sharing your files as quickly as possible.", 'download-monitor' ); ?>
					<?php echo esc_html__( "With Download Monitor you can manage, track and offer downloads to your users using your WordPress website.", 'download-monitor' ); ?>
					<?php echo esc_html__( "On top of that, Download Monitor allows you to sell your downloads, turning your WordPress website into fully featured e-commerce website out of the box.", 'download-monitor' ); ?>
                </p>
                <p>
					<?php echo esc_html__( "You decide if you want to offer you downloads for free or want to start selling them (or both!). Whatever you decide, you chose the right plugin for the job!", 'download-monitor' ); ?>
                </p>
            </div>

            <div class="dlm-onboarding-section dlm-onboarding-section-one-col">
                <h2><?php echo esc_html__( "Let's Create Your Pages", 'download-monitor' ); ?></h2>
                <p>
					<?php echo esc_html__( 'In order to function properly, Download Monitor needs to create some pages in your WordPress website.', 'download-monitor' ); ?>
					<?php echo esc_html__( "We can create these pages for you here. If you click the 'Create Page' button we will create that page and add the required shortcode to it. We'll also make sure the newly created page is set in your settings page.", 'download-monitor' ); ?>
                </p>
                <p>
					<?php echo esc_html__( "If you don't plan on selling downloads, you do not have to create the cart and checkout page. We recommend always creating the No Access page.", 'download-monitor' ); ?>
                </p>
                <table cellpadding="0" cellspacing="0" border="0" class="dlm-onboarding-pages">
                    <tr>
                        <th><?php echo esc_html__( 'No Access', 'download-monitor' ); ?></th>
                        <td><?php echo esc_html__( "The page your visitors see when they are not allowed to download a file.", 'download-monitor' ); ?></td>
                        <td>
							<?php
							/**
							 * Check if no access page is already set in settings
							 */
							$page_no_access = $settings->get_option( 'no_access_page' );

							if ( $page_no_access != 0 ) :
								?>
                                <a href="javascript:;"
                                   class="button button-primary button-hero dlm-page-exists"><?php echo esc_html__( 'Page Created', 'download-monitor' ); ?></a>
								<?php
							else:
								?>
                                <a href="javascript:;"
                                   class="button button-primary button-hero dlm-create-page"
                                   data-page="no-access"><?php echo esc_html__( 'Create Page', 'download-monitor' ); ?></a>
								<?php
							endif;
							?>

                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__( 'Cart', 'download-monitor' ); ?></th>
                        <td><?php echo esc_html__( 'Your shop cart page if you decide to sell downloads.', 'download-monitor' ); ?></td>
                        <td>
							<?php
							/**
							 * Check if no access page is already set in settings
							 */
							$page_cart = $settings->get_option( 'page_cart' );

							if ( $page_cart != 0 ) :
								?>
                                <a href="javascript:;"
                                   class="button button-primary button-hero dlm-page-exists"><?php echo esc_html__( 'Page Created', 'download-monitor' ); ?></a>
								<?php
							else:
								?>
                                <a href="javascript:;"
                                   class="button button-primary button-hero dlm-create-page"
                                   data-page="cart"><?php echo esc_html__( 'Create Page', 'download-monitor' ); ?></a>
								<?php
							endif;
							?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__( 'Checkout', 'download-monitor' ); ?></th>
                        <td><?php echo esc_html__( 'Your shop checkout page if you decide to sell downloads.', 'download-monitor' ); ?></td>
                        <td>
		                    <?php
		                    /**
		                     * Check if no access page is already set in settings
		                     */
		                    $page_checkout = $settings->get_option( 'page_checkout' );

		                    if ( $page_checkout != 0 ) :
			                    ?>
                                <a href="javascript:;"
                                   class="button button-primary button-hero dlm-page-exists"><?php echo esc_html__( 'Page Created', 'download-monitor' ); ?></a>
			                    <?php
		                    else:
			                    ?>
                                <a href="javascript:;"
                                   class="button button-primary button-hero dlm-create-page"
                                   data-page="checkout"><?php echo esc_html__( 'Create Page', 'download-monitor' ); ?></a>
			                    <?php
		                    endif;
		                    ?>
                        </td>
                    </tr>

                </table>
            </div>

            <div class="dlm-onboarding-section dlm-onboarding-section-one-col">
                <h2><?php echo esc_html__( 'Extensions', 'download-monitor' ); ?></h2>
                <p>
					<?php echo esc_html__( 'Power up your Download Monitor website with our official extensions. Our extensions allow you to add specific functionality to your Download Monitor powered website and come with our premium support and updates.', 'download-monitor' ); ?>
					<?php echo esc_html__( "Here's a quick sample of what we offer.", 'download-monitor' ); ?>
                </p>
            </div>

            <div class="dlm-onboarding-section dlm-onboarding-section-three-col">
				<?php
				$extension_loader = new ExtensionLoader();
				$response         = json_decode( $extension_loader->fetch() );
				if ( ! empty( $response->extensions ) ) :
					$i = 0;
					foreach ( $response->extensions as $extension ) :
						?>
                        <div class="dlm-onboarding-col">
                            <img src="<?php echo esc_attr( $extension->image ); ?>"
                                 alt="<?php echo esc_attr( $extension->name ); ?>"/>
                            <h3><?php echo esc_html( $extension->name ); ?></h3>
                            <p><?php echo esc_html( $extension->desc ); ?></p>
                        </div>
						<?php
						$i ++;
						if ( $i > 2 ) {
							break;
						}
					endforeach;
				endif;
				?>
            </div>

            <div class="dlm-onboarding-section dlm-onboarding-section-one-col dlm-onboarding-section-cta">
                <p>
                    <a href="https://www.download-monitor.com/extensions/?utm_source=plugin&utm_medium=link&utm_campaign=onboarding"
                       class="button button-primary button-hero"
                       target="_blank"><?php echo esc_html__( 'View More Extensions', 'download-monitor' ); ?></a>
                </p>
            </div>

            <div class="dlm-onboarding-section dlm-onboarding-section-one-col">
                <h2><?php echo esc_html__( "What's Next?", 'download-monitor' ); ?></h2>

                <p>
					<?php printf( esc_html__( "Now that your Download Monitor installation is done, it's time to setup your downloads. You can %sread more about creating your first Download here%s.", 'download-monitor' ), '<a href="https://www.download-monitor.com/kb/creating-your-first-download/?utm_source=plugin&utm_medium=link&utm_campaign=onboarding" target="_blank">', '</a>' ); ?>
					<?php echo esc_html__( "If you need any help in setting up your downloads or having any other question about Download Monitor, we'd be happy to help you via our support forums.", 'download-monitor' ); ?>
                    <a href="https://wordpress.org/support/plugin/download-monitor/"
                       target="_blank"><?php echo esc_html__( "Click here to visit our Support Forum.", 'download-monitor' ); ?></a>
                </p>
            </div>

            <div class="dlm-onboarding-section dlm-onboarding-section-one-col dlm-onboarding-section-cta">
                <p>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=dlm_download' ) ); ?>"
                       class="button button-primary button-hero"><?php echo esc_html__( 'Create Your First Download', 'download-monitor' ); ?></a>
                </p>
            </div>

        </div>
		<?php
	}
}