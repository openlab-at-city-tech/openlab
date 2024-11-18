<?php
/**
 * Notices for js errors 
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Error_Handler {

	public static function add_assets() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		// CSS
		wp_register_style( 'epkb-js-error-handlers', Echo_Knowledge_Base::$plugin_url . 'css/error-handlers' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		wp_print_styles( array( 'epkb-js-error-handlers' ) );

		// JS
		wp_register_script( 'epkb-js-error-handlers', Echo_Knowledge_Base::$plugin_url . 'js/error-handlers' . $suffix . '.js', array(), Echo_Knowledge_Base::$version );
		wp_print_scripts( array( 'epkb-js-error-handlers' ) );
	}

	/**
	 * Show JS errors caught by JS error handler
	 */
	public static function add_error_popup() {
		$support_html_escaped = EPKB_Utilities::contact_us_for_support();
		echo '
			<div style="display:none;" class="epkb-js-error-notice">
				<div class="epkb-js-error-close">&times;</div>
				<div class="epkb-js-error-title">' . esc_html__( 'We found a JavaScript error on this page caused by a plugin', 'echo-knowledge-base' ) . '</div>
				<div class="epkb-js-error-body">
					<div class="epkb-js-error-msg"></div>' .
					' ' . esc_html__( 'in', 'echo-knowledge-base' ) . ' <div class="epkb-js-error-url"></div>' . '
				</div>
				<div>' . $support_html_escaped /** phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ . '</div>
				<div class="epkb-js-error-about">' . esc_html__( 'Check browser console for more information', 'echo-knowledge-base' ) . '</div>
			</div>';
	}

	public static function get_ns_error_text() {
	    ob_start(); ?>
        <p><?php esc_html_e( 'Please check to see if you enabled browser ad blocker? You can add this URL address to ad blocker exceptions in the browser.', 'echo-knowledge-base' ); ?></p>
        <p><?php esc_html_e( 'Also try to clear your browser cache, then log out and back in.', 'echo-knowledge-base' ); ?></p>        <?php
        return ob_get_clean();
	}

    public static function get_csr_error_text() {
        return sprintf( '%s <a href="%s" target="_blank">%s</a>',
	        esc_html__( 'We detected CSP error. See the reference article about CSP', 'echo-knowledge-base' ), 'https://www.echoknowledgebase.com/documentation/content-security-policy/',
	        esc_html__( 'here', 'echo-knowledge-base' )
        );
    }

    public static function timeout1_error() {
	    return esc_html__( 'The front-end Editor is taking long to load. Please wait a bit longer.', 'echo-knowledge-base' );
    }

	public static function timeout2_error() {
		ob_start(); ?>
		<ul>
			<li><?php esc_html_e( 'Please check if you have any errors reported in admin > Tools > Site Health.', 'echo-knowledge-base' ); ?></li>
			<li><?php esc_html_e( 'Try a different browser.', 'echo-knowledge-base' ); ?></li>
		</ul><?php
		return ob_get_clean();
	}

	public static function other_error_found() {
		return ''; //esc_html__( 'We found an issue with your website configuration.', 'echo-knowledge-base' );
	}

	public static function js_not_loaded() {

		if ( ! EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ) { ?>
			<h4><?php esc_html_e( 'This page is not loading the KB front-end Editor.', 'echo-knowledge-base' ); ?></h4>
			<p><?php esc_html_e( 'Instead use the KB visual Editor on the back-end. To do this you need to switch on the back end Editor.', 'echo-knowledge-base' ); ?></p>
			<a class="epkb-editor-safe-mode-link" href="<?php echo esc_url( admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration&action=enable_editor_backend_mode&_wpnonce_epkb_ajax_action=' ) ) .
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                wp_create_nonce( '_wpnonce_epkb_ajax_action' ); ?>#settings__editor"><?php esc_html_e( 'Click here to go to the Configuration Page and turn on Editor back-end mode.', 'echo-knowledge-base' ); ?></a>
			<p><?php
				//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo EPKB_Utilities::contact_us_for_support();   ?>
			</p>    <?php
			return;
		}  ?>

		<h4><?php esc_html_e( 'This page is not loading the KB frontend Editor.', 'echo-knowledge-base' ); ?></h4>
		<p><?php esc_html_e( 'The front-end Editor is taking longer to load. Please wait a bit longer.', 'echo-knowledge-base' ); ?></p>
		<ul>
			<li><?php esc_html_e( 'If the Editor does not load, please check if this is a caching or filtering issue.', 'echo-knowledge-base' ); ?></li>
			<li><?php esc_html_e( 'Do NOT load your builder when loading the Editor.', 'echo-knowledge-base' ); ?></li>
			<li><?php
				echo esc_html__( 'If you have Cloudflare, read the article', 'echo-knowledge-base' ) . ' ';
				printf( '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>', 'https://www.echoknowledgebase.com/documentation/other-2/#cloudflare-rocket-loader', esc_html__( 'here', 'echo-knowledge-base' ) ); ?>
			</li>
		</ul>
		<p><?php
			//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo EPKB_Utilities::contact_us_for_support();   ?>
		</p>    <?php
	}

	// this script inline as should work if something wrong will go with enqueue
	public static function no_js_inline_script() { ?>
		<script>
			setTimeout(function(){
				let error_block = document.getElementById( "epkb-editor-error-no-js-message" );

				if ( error_block == null ) {
					return;
				}

				error_block.style.display = "flex";
			}, 3000);
		</script><?php
	}

	// this script inline as should work if something wrong will go with enqueue
	public static function no_js_inline_styles() { ?>
		<style>
			#epkb-editor-error-no-js-message {
				width: 100%;
				max-width: 700px;
				margin: auto;
				background: #F3F6FF;
				padding: 50px;
				font-family: sans-serif;
				text-align: center;
				justify-content: center;
				margin-top: 20vh;
			}

			#epkb-editor-error-no-js-message h4 {
				margin-top: 0;
				font-weight: 600;
			}

			#epkb-editor-error-no-js-message ul {
				text-align: left;
				margin-bottom: 50px;
				margin-top: 50px;
			}

			#epkb-editor-error-no-js-message .epkb-editor-safe-mode-link {
				display: inline-block;
				background: #08A000;
				color: white;
				padding: 15px 50px;
				border-radius: 5px;
				text-decoration: none;
				margin-top: 11px;
			}

			#epkb-editor-error-no-js-message .epkb-editor-safe-mode-link:hover {
				opacity: 0.9;
			}
		</style><?php
	}
}
