<?php
/**
 * Create a functionality for using quiz in iframes and in amp pages.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 */

/**
 * Create a functionality for using quiz in iframes and in amp pages.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_iFrame {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
     * The public class object of the plugin.
     *
	 * @since    1.0.0
	 * @access   private
	 * @var      object $public_obj The public class object.
	 */
	private $public_obj;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

        $this->public_obj = new Quiz_Maker_Public( $this->plugin_name, $this->version );
	}

	/**
	 * @return bool
	 */
	public static function isEmbed() {
		if ( isset( $_SERVER["HTTP_SEC_FETCH_SITE"] ) && $_SERVER["HTTP_SEC_FETCH_SITE"] !== "same-origin" && isset( $_SERVER["HTTP_SEC_FETCH_DEST"] ) && $_SERVER["HTTP_SEC_FETCH_DEST"] !== "iframe" ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isAMP() {
        if( Quiz_Maker_Data::isServerSideRenderRequest() ) {
            return false;
        }

		if ( isset( $_REQUEST['ays-amp'] ) && absint( $_REQUEST['ays-amp'] ) === 1 ) {
			return true;
		}elseif ( function_exists( 'ampforwp_is_amp_endpoint' ) ) {
            return ampforwp_is_amp_endpoint();
		} elseif ( function_exists( 'amp_is_request' ) ) {
			return amp_is_request();
		} elseif ( function_exists( 'is_amp_endpoint' ) ) {
			return is_amp_endpoint();
		}

		return false;
	}

	public function enqueue_styles() {
		$this->public_obj->enqueue_styles();
		ob_start();
		?>
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
              integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
              crossorigin="anonymous"
              referrerpolicy="no-referrer"/>
		<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		$this->public_obj->enqueue_scripts();
	}

	public function ays_quiz_translate_content($content) {
        $in = str_replace("\n", "-ays-quiz-break-line-", $content);
        $out = preg_replace_callback("/\[:(.*?)\[:]/", function($part){
            $language_slug = explode('-', get_bloginfo("language"))[0];
            preg_match("/\[\:".$language_slug."\](.*?)\[\:/is", $part[0], $out);
            return (is_array($out) && isset($out[1])) ? $out[1] : $part[0];
        }, $in);
        $out = str_replace("-ays-quiz-break-line-", "\n", $out);
        return $out;
    }

    public function ays_quiz_payment_scripts($id, $public_url, $plugin_name, $plugin_version) {
    	$quiz = Quiz_Maker_Data::get_quiz_by_id($id);
        
        if (is_null($quiz)) {
            $content = "";
            return $content;
        }
        if (intval($quiz['published']) === 0) {
            $content = "";
            return $content;
        }

        $content = array();
        
        $is_elementor_exists = Quiz_Maker_Data::ays_quiz_is_elementor();
        $is_editor_exists = Quiz_Maker_Data::ays_quiz_is_editor();

        
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
        $enable_copy_protection = (isset($options['enable_copy_protection']) && $options['enable_copy_protection'] == "on") ? true : false;
        $quiz_integrations = (get_option( 'ays_quiz_integrations' ) == null || get_option( 'ays_quiz_integrations' ) == '') ? array() : json_decode( get_option( 'ays_quiz_integrations' ), true );
        $payment_terms = isset($quiz_integrations['payment_terms']) ? $quiz_integrations['payment_terms'] : "lifetime";
        $paypal_client_id = isset($quiz_integrations['paypal_client_id']) && $quiz_integrations['paypal_client_id'] != '' ? $quiz_integrations['paypal_client_id'] : null;
        $quiz_paypal = (isset($options['enable_paypal']) && $options['enable_paypal'] == "on") ? true : false;
        $quiz_paypal_message = (isset($options['paypal_message']) && $options['paypal_message'] != "") ? $options['paypal_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
        $quiz_paypal_message = stripslashes( wpautop( $quiz_paypal_message ) );
        $paypal_subscribtion_duration = isset( $quiz_integrations['subscribtion_duration'] ) && $quiz_integrations['subscribtion_duration'] != '' ? absint( $quiz_integrations['subscribtion_duration'] ) : '';
        $paypal_subscribtion_duration_by = isset( $quiz_integrations['subscribtion_duration_by'] ) && $quiz_integrations['subscribtion_duration_by'] != '' ? $quiz_integrations['subscribtion_duration_by'] : 'day';

        // Stripe
        $stripe_res = (Quiz_Maker_Settings_Actions::ays_get_setting('stripe') === false) ? json_encode(array()) : Quiz_Maker_Settings_Actions::ays_get_setting('stripe');
        $stripe = json_decode($stripe_res, true);
        $stripe_secret_key = isset($stripe['secret_key']) ? $stripe['secret_key'] : '';
        $stripe_api_key = isset($stripe['api_key']) ? $stripe['api_key'] : '';
        $stripe_payment_terms = isset($stripe['payment_terms']) ? $stripe['payment_terms'] : 'lifetime';

        // Stripe parameters
        $options['enable_stripe'] = !isset( $options['enable_stripe'] ) ? 'off' : $options['enable_stripe'];
        $enable_stripe = ( isset($options['enable_stripe']) && $options['enable_stripe'] == 'on' ) ? true : false;
        $stripe_amount = (isset($options['stripe_amount'])) ? $options['stripe_amount'] : '';
        $stripe_currency = (isset($options['stripe_currency'])) ? $options['stripe_currency'] : '';
        $stripe_message = (isset($options['stripe_message'])) ? $options['stripe_message'] : __('You need to pay to pass this quiz.', $this->plugin_name);
        $stripe_message = stripslashes( wpautop( $stripe_message ) );

        // Paypal And Stripe Paymant type
        $payment_type = (isset($options['payment_type']) && sanitize_text_field( $options['payment_type'] ) != '') ? sanitize_text_field( esc_attr( $options['payment_type']) ) : 'prepay';

        $paypal_connection = Quiz_Maker_Data::get_payment_connection( 'paypal', $payment_type, $payment_terms, $id, array(
            'subsctiptionDuration' => $paypal_subscribtion_duration,
            'subsctiptionDurationBy' => $paypal_subscribtion_duration_by,
        ));

        if($quiz_paypal && $paypal_connection === true){
            if($paypal_client_id == null || $paypal_client_id == ''){
            }else{
                $link = "https://www.paypal.com/sdk/js?client-id=".$quiz_integrations['paypal_client_id']."&currency=".$options['paypal_currency'];
                $content[] = "<script src='". $link ."' id='quiz-maker-paypal-js' data-namespace='aysQuizPayPal'></script>";
            }
        }

        $stripe_connection = Quiz_Maker_Data::get_payment_connection( 'stripe', $payment_type, $stripe_payment_terms, $id, array());

        if($enable_stripe && $stripe_connection === true){
            if($stripe_secret_key == '' || $stripe_api_key == ''){
            }else{
                $enqueue_stripe_scripts = true;
                if( !is_user_logged_in() && $stripe_payment_terms == "lifetime" ){
                    $enqueue_stripe_scripts = false;
                }

                if( $is_elementor_exists ){
                    $enqueue_stripe_scripts = false;
                }

                if( $enqueue_stripe_scripts ){

                    $link = "https://js.stripe.com/v3/";
                	$content[] = "<script src='". $link ."' id='quiz-maker-stripe-js'></script>";

                    $link = $public_url . "js/stripe_client.js?ver=". $this->version;
                	$content[] = "<script src='". $link ."' id='quiz-maker-stripe-client-js'></script>";
                }
            }
        }

        if($enable_copy_protection){
            if ( ! $is_elementor_exists && ! $is_editor_exists ) {
            	$link = $public_url . "js/quiz_copy_protection.min.js?ver=". $this->version;
            	$content[] = "<script src='". $link ."' id='quiz-maker-quiz_copy_protection-js'></script>";
            }
        }

        $content = implode("", $content);
        return $content;
    }

	public function iframe_shortcode() {
        self::headers();

        $id = ( isset( $_REQUEST['quiz'] ) && $_REQUEST['quiz'] != '' ) ? absint( intval( $_REQUEST['quiz'] ) ) : null;

        $attr = array(
            'id' => $id
        );

		$attr['chain'] = (isset($attr['chain'])) ? absint(intval($attr['chain'])) : null;
		$attr['report'] = (isset($attr['report'])) ? $attr['report'] : null;

		if ( is_null( $id ) ) {
			$quiz_content = "<p class='wrong_shortcode_text' style='color:red;'>" . __( 'Wrong shortcode initialized', $this->plugin_name ) . "</p>";
			echo str_replace( array( "\r\n", "\n", "\r" ), "\n", $quiz_content );
			wp_die();
		}

		$this->enqueue_scripts();

		include_once( AYS_QUIZ_PUBLIC_PATH . '/partials/quiz-maker-iframe-template.php' );

		wp_die();
	}

	/**
	 * @return void
	 */
    public static function headers(){
    	if ( isset( $_SERVER["HTTP_SEC_FETCH_SITE"] ) && $_SERVER["HTTP_SEC_FETCH_SITE"] !== "same-origin" && isset( $_SERVER["HTTP_SEC_FETCH_DEST"] ) && $_SERVER["HTTP_SEC_FETCH_DEST"] !== "iframe" ) {
		    header( 'HTTP/1.0 403 Forbidden', true, 403 );
		    http_response_code( 403 );
		    die();
	    }

	    header( 'Access-Control-Allow-Origin: *' );
	    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS' );
	    header( 'Access-Control-Max-Age: 1000' );
	    header( 'Access-Control-Allow-Headers: Content-Type' );
	    header( 'Content-Type: text/html; charset=utf-8' );
	    header( "Content-Security-Policy: frame-ancestors * " );
	    header( 'X-Frame-Options: ALLOW-FROM *' );
    }

	/**
	 * @return void
	 */
    public static function headers_for_ajax(){
	    header('Access-Control-Allow-Origin: *');
	    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	    header('Access-Control-Max-Age: 1000');
	    header('Access-Control-Allow-Headers: Content-Type');
	    header( "Content-Security-Policy: frame-ancestors * " );
	    header( 'X-Frame-Options: ALLOW-FROM *' );
    }

    public static function get_iframe_for_amp( $id, $attr, $full_page = false ) {
	    $url_query = http_build_query( array_merge( array(
		    'action' => 'ays_quiz_iframe_shortcode',
		    'quiz' => $id,
		    'ays-amp' => '1',
	    ), $attr ) );

        $same_origin = ' allow-same-origin ';
        if( function_exists( 'ampforwp_is_amp_endpoint' ) ){
	        $same_origin = '';
        }

        $url = admin_url( 'admin-ajax.php' );
	    if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === "on" ) {
		    //$url = str_replace( 'https', 'http', $url );
	    }

        $url .= '?' . $url_query;
        $url = self::get_tiny_url($url);
        
	    $options = array();
	    if ( !is_null($id) && intval($id) > 0 ) {
	        $quiz = Quiz_Maker_Data::get_quiz_by_id($id);
	        $options = ( isset( $quiz['options'] ) && json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
	    }

	    // Quiz min-height
        $quiz_height = (isset($options['height']) && $options['height'] != '' && intval( $options['height'] ) > 0) ? absint( sanitize_text_field($options['height']) ) : 400;
	    $quiz_width = (isset($options['width']) && $options['width'] != '' && intval( $options['width'] ) > 0) ? absint( sanitize_text_field($options['width']) ) : 400;

        if ($quiz_height != "" && $quiz_height > 0) {
        	$quiz_height += 100;
        }

	    $quiz_width += 34;

	    $content = '<div id="aysQuizAMPIframeWrap' . $id . '">
			<style>
				div[id^="aysQuizAMPIframeWrap"] amp-iframe {
					width: 100%;
					max-width: 100% !important;
					margin: 0 auto !important;
					overflow: hidden;
				}
				
				div[id^="aysQuizAMPIframeWrap"] iframe:not( amp-iframe iframe ) {
					width: '. ( $full_page ? '100%' : $quiz_width . 'px' ) .';
					height: '. ( $full_page ? '100%' : $quiz_height . 'px' ) .';
					max-width: 100% !important;
					margin: 0 auto !important;
					overflow: hidden;
				}
				
				div[id^="aysQuizAMPIframeWrap"] amp-iframe iframe body {
					background-color: #fff;
				}
			</style>
			<script async custom-element="amp-iframe" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
            <iframe
              width="'. ( $full_page ? '100%' : $quiz_width ) .'"
           	  height="'. ( $full_page ? '100%' : $quiz_height ) .'"
              frameborder="0"
              scrolling="yes"
              layout="responsive"
              sandbox="allow-downloads '. $same_origin .' allow-forms allow-modals allow-orientation-lock allow-pointer-lock allow-popups allow-popups-to-escape-sandbox allow-presentation allow-scripts allow-top-navigation-by-user-activation"
              id="aysQuizAMPIframe' . $id . '"
              src="' . $url . '">
            </iframe>
            </div>';

        return $content;
    }

    public static function get_iframe( $id, $attr ) {
	    $url_query = http_build_query( array_merge( array(
		    // 'action' => 'ays_quiz_iframe_shortcode',
		    'quiz' => $id,
        ), $attr ) );

	    $url = admin_url( 'admin-ajax.php' );
	    if( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === "on" ) {
		    //$url = str_replace( 'https', 'http', $url );
	    }

	    $url = home_url( 'ays-quiz/' );
	    $url .= $id . '/?' . $url_query;

	    $options = array();
	    if ( !is_null($id) && intval($id) > 0 ) {
	        $quiz = Quiz_Maker_Data::get_quiz_by_id($id);
	        $options = ( isset( $quiz['options'] ) && json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
	    }

	    // Quiz min-height
        $quiz_height = (isset($options['height']) && $options['height'] != '' && intval( $options['height'] ) > 0) ? absint( sanitize_text_field($options['height']) ) : 400;

        if ($quiz_height != "" && $quiz_height > 0) {
        	$quiz_height += 100;
        }

	    $content = '
            <iframe
              width="400"
              height="'. $quiz_height .'"
              frameborder="0"
              scrolling="yes"
              layout="responsive"
              sandbox="allow-downloads allow-same-origin allow-forms allow-modals allow-orientation-lock allow-pointer-lock allow-popups allow-popups-to-escape-sandbox allow-presentation allow-scripts allow-top-navigation-by-user-activation"
              resizable
              id="aysQuizIframe'. $id .'"
              src="' . $url . '"
              style="width:100%; max-width: 100%; margin: 0 auto; overflow: hidden;">
              <div overflow tabindex="0" role="button" aria-label="Quiz content"></div>
            </iframe>';

        $content .= '
        <script>

			window.addEventListener("message", receiveMessage, false);

			function receiveMessage(event) {
			  if (event.data === "getParentUrl") {
			    event.source.postMessage(window.location.href, event.origin);
			  }
			}

		</script>';

        return $content;
    }

    public static function get_tiny_url($url) {
		$api_url = 'https://tinyurl.com/api-create.php?url=' . $url;
		$response = wp_remote_get( $api_url );
		return wp_remote_retrieve_body( $response );
	}

    public function add_rewrite_endpoint(){
	    add_rewrite_endpoint( 'ays-quiz', EP_ROOT );
	    flush_rewrite_rules();
    }

    public function add_template_redirect(){
	    if ( $id = get_query_var( 'ays-quiz' ) ) {
            $id = absint( $id );
		    $attr = array(
			    'id' => $id,
		    );

		    $attr['chain'] = (isset($_GET['chain'])) ? absint(intval($_GET['chain'])) : null;
		    $attr['report'] = (isset($_GET['report'])) ? $_GET['report'] : null;
		    $attr['training'] = (isset($_GET['training'])) ? $_GET['training'] : null;
		    $attr['category_selective'] = (isset($_GET['category_selective'])) ? $_GET['category_selective'] : null;

            if ( is_null( $id ) ) {
                $quiz_content = "<p class='wrong_shortcode_text' style='color:red;'>" . __( 'Wrong shortcode initialized', $this->plugin_name ) . "</p>";
                echo str_replace( array( "\r\n", "\n", "\r" ), "\n", $quiz_content );
                die();
            }

		    $chain_id = (isset($attr['chain'])) ? absint(intval($attr['chain'])) : null;
		    $chain_result_btn = (isset($attr['report'])) ? $attr['report'] : null;
		    $is_training = isset($attr['training']) && sanitize_text_field($attr['training']) === 'true';
		    $category_selective = isset($attr['category_selective']) && sanitize_text_field($attr['category_selective']) === 'true';

		    $this->public_obj->set_prop('chain_id', $chain_id );
		    $this->public_obj->set_prop( 'chain_result_btn', $chain_result_btn );
		    $this->public_obj->set_prop( 'is_training', $is_training );
		    $this->public_obj->set_prop( 'category_selective', $category_selective );

		    $this->enqueue_scripts();

            if( self::isAMP() ){
                echo $this->get_iframe_for_amp( $id, $attr, true );
            } elseif( self::isEmbed() ) {
	            add_filter( 'show_admin_bar' , array( $this, 'disable_admin_bar' ) );
	            include_once( AYS_QUIZ_PUBLIC_PATH . '/partials/quiz-maker-iframe-template.php' );
            } else {
	            include_once( AYS_QUIZ_PUBLIC_PATH . '/partials/quiz-maker-iframe-template.php' );
            }
            die();
	    }
    }

	public function add_request_check( $vars = array() ){

		if ( isset( $vars['ays-quiz'] ) && empty( $vars['ays-quiz'] ) ) {
			$vars['ays-quiz'] = 'default';
		}
		return $vars;
	}

	public function disable_admin_bar() {
		return false;
	}
}