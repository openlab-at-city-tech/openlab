<?php
/**
 * Sydney campaign notice
 *
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class to display the theme review notice after certain period.
 *
 */
class Sydney_Campaign_Notice {

    /**
     * End date target.
     *
     * @var string
     */
    private $end_date_target = '2024-12-01';
    
	/**
	 * Constructor
	 */
	public function __construct() {
		if( defined( 'SYDNEY_AWL_ACTIVE' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_inline_style' ) );
		add_action( 'admin_notices', array( $this, 'review_notice_markup' ), 0 );
		add_action( 'admin_init', array( $this, 'dismiss_notice_handler' ), 0 );
	}

	/**
	 * Enqueue admin scripts
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'sydney-notices', get_template_directory_uri() . '/inc/notices/sydney-notices.min.css', array(), '202411', 'all' );
	}

    /**
     * Add inline style.
     * 
     * @return void
     */
    public function add_inline_style() {
        $css = "
            .toplevel_page_sydney-dashboard #wpbody-content>.updated.sydney-campaign-notice, 
            .toplevel_page_sydney-dashboard #wpbody-content>.notice.sydney-campaign-notice {
                display: block !important;
                margin: -1px -1px 0px -20px;
            }

            .sydney-campaign-notice {
                position: relative !important;
                background-color: #000;
                padding: 30px 30px 0px !important;
                border-left: 0;
            }

            @media(min-width: 1270px) {
                .sydney-campaign-notice {
                    padding: 45px 61px 40px !important;
					margin-left: -20px;
                }
            }

            .sydney-campaign-notice h3 {
                color: #FFF;
                font-size: 42px;
                font-weight: 700;
                line-height: 1.1;
                margin-bottom: 40px;
            }

            @media(min-width: 576px) {
                .sydney-campaign-notice h3 {
                    min-width: 455px;
                    max-width: 25%;
                    line-height: 0.8;
                }   
            }

            .sydney-campaign-notice h3 span {
                position: relative;
                top: 12px;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                color: #FFDB12;
            }

            .sydney-campaign-notice-thumbnail {
                max-width: 100%;
                height: auto;
                margin-top: 30px;
            }

            @media(min-width: 1270px) {
                .sydney-campaign-notice-thumbnail {
                    position: absolute;
                    right: 40px;
                    bottom: 0;
                    max-width: 553px;
                    margin-top: 0;
                }
            }

            @media(min-width: 1300px) {
                .sydney-campaign-notice-thumbnail {
                    max-width: 663px;
                }
            }

            .sydney-campaign-notice-percent {
                position: relative;
                max-width: 118px;
                top: -2px;
            }

            .sydney-campaign-notice .sydney-btn {
                font-size: 19px;
                padding: 19px 41px;
                border-radius: 7px;
            }

            .sydney-campaign-notice .notice-dismiss,
            .sydney-campaign-notice .notice-dismiss:before {
                color: #FFF;
            }

            .sydney-campaign-notice .notice-dismiss:active:before, 
            .sydney-campaign-notice .notice-dismiss:focus:before, 
            .sydney-campaign-notice .notice-dismiss:hover:before {
                color: #757575;
            }
        ";

        wp_add_inline_style( 'sydney-notices', $css );
    }

    /**
	 * Is notice dismissed?.
	 * 
     * @return bool
	 */
	public function is_dismissed() {
		$user_id = get_current_user_id();

        return get_user_meta( $user_id, 'sydney_disable_bf2024_notice', true ) ? true : false;
	}

    /**
	 * Has end date passed.
	 * 
	 * @return bool
	 */
	public function has_end_date_passed() {
		if ( empty( $this->end_date_target ) ) {
			return false;
		}

		$end_date = strtotime( $this->end_date_target );
		$current_date = time();

		return $current_date > $end_date;
	}

	/**
	 * Show HTML markup if conditions meet.
     * 
     * @return void
	 */
	public function review_notice_markup() {
		$dismissed = $this->is_dismissed();
        $has_end_date_passed = $this->has_end_date_passed();

		if ( $dismissed || $has_end_date_passed ) {
			return;
		}

        // Display Conditions
		global $hook_suffix;

        if( ! in_array( $hook_suffix, array( 'toplevel_page_sydney-dashboard' ), true ) ) {
			return;
		}
		
		?>
        
        <div class="sydney-notice sydney-campaign-notice" style="position:relative;">
			<h3><?php echo wp_kses_post( sprintf(
                /* Translators: 1. Image url. */
                __( 'Sydney Black Friday: Up to <span><img src="%1$s" class="sydney-campaign-notice-percent" alt="Up to 40 Percent Off!" /> Off!</span>', 'sydney' ),
                get_template_directory_uri() . '/images/admin/40-percent.png'
            ) ); ?></h3>

            <a href="https://athemes.com/black-friday/?utm_source=theme_notice&utm_medium=button&utm_campaign=Sydney#sydney-pro" class="sydney-btn sydney-btn-primary" target="_blank"><?php esc_html_e( 'Give Me This Deal', 'sydney' ); ?></a>

            <img src="<?php echo esc_url( get_template_directory_uri() . '/images/admin/people-trust.png' ); ?>" alt="<?php echo esc_attr__( 'Ready to join 130,000+ WordPress creators who\'ve found their perfect match?', 'sydney' ); ?>" class="sydney-campaign-notice-thumbnail" />

			<a class="notice-dismiss" href="?page=sydney-dashboard&nag_sydney_disable_campaign_notice=0" style="text-decoration:none;"></a>             
		</div>

		<?php
	}

	/**
	 * Disable review notice permanently
	 */
	public function dismiss_notice_handler() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, Universal.Operators.StrictComparisons.LooseEqual
		if ( isset( $_GET['nag_sydney_disable_campaign_notice'] ) && '0' == $_GET['nag_sydney_disable_campaign_notice'] ) {
			add_user_meta( get_current_user_id(), 'sydney_disable_bf2024_notice', 'true', true );
		}
	}
}

new Sydney_Campaign_Notice();