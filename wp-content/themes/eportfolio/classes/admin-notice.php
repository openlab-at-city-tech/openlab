<?php
if ( !class_exists('ePortfolio_Dashboard_Notice') ):

    class ePortfolio_Dashboard_Notice
    {
        function __construct()
        {	
            global $pagenow;

        	if( $this->eportfolio_show_hide_notice() ){

	            add_action( 'admin_notices',array( $this,'eportfolio_admin_notice' ) );
                
	        }
	        add_action( 'wp_ajax_eportfolio_notice_dismiss', array( $this, 'eportfolio_notice_dismiss' ) );
			add_action( 'switch_theme', array( $this, 'eportfolio_notice_clear_cache' ) );
        
            if( isset( $_GET['page'] ) && $_GET['page'] == 'eportfolio-about' ){

                add_action('in_admin_header', array( $this,'eportfolio_hide_all_admin_notice' ),1000 );

            }
        }

        public function eportfolio_hide_all_admin_notice(){

            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');

        }
        
        public static function eportfolio_show_hide_notice( $status = false ){

            if( $status ){

                if( (class_exists( 'Booster_Extension_Class' ) ) || get_option('eportfolio_admin_notice') ){

                    return false;

                }else{

                    return true;

                }

            }

            // Check If current Page 
            if ( isset( $_GET['page'] ) && $_GET['page'] == 'eportfolio-about'  ) {
                return false;
            }

        	// Hide if dismiss notice
        	if( get_option('eportfolio_admin_notice') ){
				return false;
			}
        	// Hide if all plugin active
        	if ( class_exists( 'Demo_Import_Kit_Class' ) && class_exists( 'Themeinwp_Import_Companion' ) && class_exists( 'WPCF7_ContactForm' ) ) {
				return false;
			}
			// Hide On TGMPA pages
			if ( ! empty( $_GET['tgmpa-nonce'] ) ) {
				return false;
			}
			// Hide if user can't access
        	if ( current_user_can( 'manage_options' ) ) {
				return true;
			}
			
        }

        // Define Global Value
        public static function eportfolio_admin_notice(){

            $theme_info      = wp_get_theme();
            $theme_name            = $theme_info->__get( 'Name' );
            ?>
           <div class="updated notice is-dismissible twp-eportfolio-notice">

                <h3><?php esc_html_e('Quick Setup','eportfolio'); ?></h3>

                <p><strong><?php printf( __( '%1$s is now installed and ready to use. Are you looking for a better experience to set up your site?', 'eportfolio' ), esc_html( $theme_name ) ); ?></strong></p>

                <small><?php esc_html_e("We've prepared a unique onboarding process through our",'eportfolio'); ?> <a href="<?php echo esc_url( admin_url().'themes.php?page='.get_template().'-about') ?>"><?php esc_html_e('Getting started','eportfolio'); ?></a> <?php esc_html_e("page. It helps you get started and configure your upcoming website with ease. Let's make it shine!",'eportfolio'); ?></small>

                <p>
                    <a target="_blank" class="button button-primary button-primary-upgrade" href="<?php echo esc_url( 'https://www.themeinwp.com/theme/eportfolio-pro/' ); ?>">
                        <span class="dashicons dashicons-thumbs-up"></span>
                        <span><?php esc_html_e('Upgrade to Pro','eportfolio'); ?></span>
                    </a>
                    <a class="button button-secondary twp-install-active" href="javascript:void(0)">
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <span><?php esc_html_e('Install and activate recommended plugins','eportfolio'); ?></span>
                    </a>

                    <span class="quick-loader-wrapper"><span class="quick-loader"></span></span>

                    <a target="_blank" class="button button-secondary" href="<?php echo esc_url( 'https://demo.themeinwp.com/eportfolio/' ); ?>">
                        <span class="dashicons dashicons-welcome-view-site"></span>
                        <span><?php esc_html_e('View Demo','eportfolio'); ?></span>
                    </a>

                    <a target="_blank" class="button button-primary" href="<?php echo esc_url('https://wordpress.org/support/theme/eportfolio/reviews/?filter=5'); ?>">
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                        <span class="dashicons dashicons-star-filled"></span>
                        <span><?php esc_html_e('Leave a review', 'eportfolio'); ?></span>
                    </a>

                    <a class="btn-dismiss twp-custom-setup" href="javascript:void(0)"><?php esc_html_e('Dismiss this notice.','eportfolio'); ?></a>

                </p>

            </div>

        <?php
        }

        public function eportfolio_notice_dismiss(){

        	if ( isset( $_POST[ '_wpnonce' ] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ '_wpnonce' ] ) ), 'eportfolio_ajax_nonce' ) ) {

	        	update_option('eportfolio_admin_notice','hide');

	        }

            die();

        }

        public function eportfolio_notice_clear_cache(){

        	update_option('eportfolio_admin_notice','');

        }

    }
    new ePortfolio_Dashboard_Notice();
endif;