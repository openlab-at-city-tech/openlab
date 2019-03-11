<?php
//AEC Script and Style Dependencies
class AECDependencies {
		//Checks for script addition on single or page posts
		//called from wp_print_scripts action
		public static function add_post_scripts() {
			global $aecomments;
			if (
				is_single() ||
				(
					is_page() &&
					$aecomments->get_admin_option( 'show_pages' ) == 'true'
				) ||
				(
					is_admin() &&
					$aecomments->get_user_option( 'admin_editing' ) == 'false'
				) ||
				(
					$aecomments->get_admin_option( 'scripts_on_archive' ) == 'true' &&
					is_post_type_archive($aecomments->get_admin_option( 'allowed_archives' ))
				)
			) {
				AECDependencies::add_scripts();
			}
		} //end add_post_scripts
		
		
		//Adds CSS for the admin panel interface
		//public static class.dependencies
		public static function add_admin_panel_css() {
			global $aecomments;
			//todo - consolidate these as well if possible
			wp_enqueue_style('aecadminpanel', $aecomments->get_plugin_url().'/css/admin-panel.css', array(), $aecomments->get_version());
			wp_enqueue_style('aecadminpaneltabber', $aecomments->get_plugin_url().'/css/tabber.css', array('aecadminpanel'), $aecomments->get_version());
		} //end add_admin_panel_css
		
		/* Private - Adds JavaScript in the admin panel if admin has enabled the option */
		//public static class.dependencies
		public static function add_admin_scripts() {

			//Admin scripts here
			AECJS::output_js( 'aec_admin', array( 'jquery-ui-sortable' ), true );
		} //end add_admin_scripts
		
		
		/**
		* Adds a link to the stylesheet to the header
		*/
		//public static class.dependencies
		public static function add_css(){
			global $aecomments;
			
			if ( is_single() ||
				is_page() ||
				is_admin() ||
				(
					$aecomments->get_admin_option( 'scripts_on_archive' ) == 'true' &&
					is_post_type_archive($aecomments->get_admin_option( 'allowed_archives' ))
				)
			) {
				if (is_page() && $aecomments->get_admin_option( 'show_pages' ) != 'true') 
					return;

				//Output CSS or enqueue depending on if a file exists or not
				AECCSS::get_main_css( true ); //echo out
				
				//Output clearfix - Saves a page request so it echoes out the entire CSS in the source
				ob_start();
				/* From http://blue-anvil.com/archives/experiments-with-floats-whats-the-best-method-of-clearance */
				if ($aecomments->get_admin_option( 'clear_after' ) == "true") { 
					echo "<!--[if IE]>\n";
					echo "<style type='text/css'>";
					include( $aecomments->get_plugin_dir( '/css/clearfix.css' ) );
					echo "</style>";
					echo "\n<![endif]-->\n";
				} /* clear after */
				ob_get_flush();
				
				AECDependencies::queue_colorbox_style();
			}
		} //end add_css
		
		//Adds the appropriate scripts to WordPress
		//public static class.dependencies
		public static function add_scripts(){
			
			global $post, $aecomments;
			$post_id = is_object( $post ) ? $post->ID : 0;
			
			if ( is_object( $post ) && 'closed' == $post->comment_status && !is_user_logged_in()) {
				return;
			}
			$min = '';
			if ($aecomments->get_admin_option( 'compressed_scripts' ) == 'true') {
				$min = ".min";
			}
			$load_footer = ($aecomments->get_admin_option( 'scripts_in_footer' ) == "true" ? true: false);
			if ( !is_admin() ) AECDependencies::load_frontend(false);
			AECDependencies::queue_colorbox_script( $min, $load_footer );
			//Output icon JS
			AECJS::output_js( 'wp_ajax_edit_comments_script', array("jquery"), $load_footer );
		} //end add_scripts
		
		public static function ajax_url() {
			if ( is_admin() ) return;
			?>
<script type='text/javascript'>
/*From Ajax Edit Comments*/
if ( typeof( ajaxurl ) == 'undefined' ) { var ajaxurl = '<?php echo esc_js( admin_url( 'admin-ajax.php', is_ssl() ? 'admin' : 'http' ) ); ?>'; }
</script>
            <?php
		} //end ajax_url
		
		//public static class.dependencies
		//Echoes out various JavaScript vars needed for the scripts
		public static function get_js_vars() {
			global $aecomments;
			
			//todo - add esc_js where appropriate
			return array(
				'AEC_BlogUrl' => admin_url( 'admin-ajax.php' ),
				'AEC_CanScroll' => AECCore::can_scroll(),
				'AEC_Minutes' => __('minutes', 'ajaxEdit'),
				'AEC_Minute' => __('minute', 'ajaxEdit'),
				'AEC_And' => __('and', 'ajaxEdit'),
				'AEC_Seconds' => __('seconds', 'ajaxEdit'),
				'AEC_Second' => __('second', 'ajaxEdit'),
				'AEC_Moderation' => __('Mark for Moderation?', 'ajaxEdit'), 
				'AEC_Approve' => __('Approve Comment?', 'ajaxEdit'),
				'AEC_Spam' => __('Mark as Spam?', 'ajaxEdit'), 
				'AEC_Delete' => __('Delete this comment?', 'ajaxEdit'), 
				'AEC_Anon' => __('Anonymous', 'ajaxEdit'), 
				'AEC_Loading' => __('Loading...', 'ajaxEdit'),
				'AEC_Ready' => __('Ready', 'ajaxEdit'),
				'AEC_Sending' => __('Sending...', 'ajaxEdit'),
				'AEC_Sent' => __('Message Sent', 'ajaxEdit'),
				'AEC_LoadSuccessful' => __('Comment Loaded Successfully', 'ajaxEdit'), 
				'AEC_Saving' => __('Saving...', 'ajaxEdit'), 
				'AEC_Blacklisting' => __('Blacklisting...', 'ajaxEdit'), 
				'AEC_Saved' => __('Comment Successfully Saved', 'ajaxEdit'), 
				'AEC_Delink' => __('De-link Successful', 'ajaxEdit'),
				'AEC_MoreOptions' => __('More Options', 'ajaxEdit'),
				'AEC_LessOptions' => __('Less Options', 'ajaxEdit'),
				'AEC_UseRTL' => $aecomments->get_admin_option( 'use_rtl' ),
				'AEC_RequestDeletionSuccess' => __('Request has been sent successfully', 'ajaxEdit'),
				'AEC_RequestError' => __('Error sending request', 'ajaxEdit'),
				'AEC_approving' => __('Approving...', 'ajaxEdit'),
				'AEC_delinking' => __('De-linking...', 'ajaxEdit'),
				'AEC_moderating' => __('Moderating...', 'ajaxEdit'),
				'AEC_spamming' => __('Spamming...', 'ajaxEdit'),
				'AEC_deleting' => __('Deleting...', 'ajaxEdit'),
				'AEC_restoring' => __('Restoring...', 'ajaxEdit'),
				'AEC_restored' => __('Comment Restored.', 'ajaxEdit'),
				'AEC_undoing' => __('Undoing...', 'ajaxEdit'),
				'AEC_undosuccess' => __('Undo Successful', 'ajaxEdit'),
				'AEC_permdelete' => __('Comment Deleted Permanently', 'ajaxEdit'),
				'AEC_fieldsrequired' => __('Input Fields are Required', 'ajaxEdit'),
				'AEC_emailaddresserror' => __('E-mail Address is Invalid', 'ajaxEdit'),
				'AEC_AftertheDeadline' => ($aecomments->get_admin_option( 'after_deadline_popups' ) == "true" ? 'true': 'false'),
				'AEC_AftertheDeadline_lang' => $aecomments->get_admin_option( 'atdlang' ),
				'AEC_Expand' => ($aecomments->get_admin_option( 'expand_popups' ) == "true" ? 'true': 'false'),
				'AEC_Yes' => __('Yes', 'ajaxEdit'),
				'AEC_No' => __('No', 'ajaxEdit'),
				'AEC_Sure' => __('Are you sure?', 'ajaxEdit'),
				'AEC_colorbox_width' => absint( $aecomments->get_admin_option( 'colorbox_width' ) ),
				'AEC_colorbox_height' => absint( $aecomments->get_admin_option( 'colorbox_height' ) )
			);
		} //end get_js_vars
		
		//Loads the after-the-deadline scripts
		//Skips the page detection tests if $skiptest = false
		//public static class.dependencies
		public static function load_frontend($skiptest = true) {
			global $aecomments, $post;
			if (is_admin()) { return; }
			
			$load_footer = ($aecomments->get_admin_option( 'scripts_in_footer' ) == "true" ? true: false);
			$min = '';
			if ($aecomments->get_admin_option( 'compressed_scripts' ) == 'true') {
				$min = ".min";
			}

			if ($skiptest == true) {
				if (!is_page() && 
					!is_single() &&
					(
						$aecomments->get_admin_option( 'scripts_on_archive' ) != 'true' ||
						!is_post_type_archive($aecomments->get_admin_option( 'allowed_archives' ))
					)
				)
					return;
				if ('closed' == $post->comment_status && !is_user_logged_in()) 
					return;
			}
			$expand = ($aecomments->get_admin_option( 'expand_posts' ) == 'true' ? true: false);
			$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_posts' ) == "true" ? true : false);
			
			if (!$expand && !$afterthedeadline)
				return;
			$deps = array( 'jquery' );
			if ( $expand ) {
				if ( AECDependencies::queue_colorbox_script( $min, $load_footer ) ) {
					$deps[] = "colorbox";
				}
			}
			AECJS::output_js( 'aec_frontend', $deps, true );
		} //end load_frontend
		
		//Loads the after-the-deadline scripts
		//Skips the page detection tests if $test = false
		//public static class.dependencies
		public static function load_frontend_css() {
			global $aecomments, $post;
			if (is_admin()) { return; }
			
			if (!is_page() && 
				!is_single() &&
				(
					$aecomments->get_admin_option( 'scripts_on_archive' ) != 'true' ||
					!is_post_type_archive($aecomments->get_admin_option( 'allowed_archives' ))
				)
			)
					return;
			if ('closed' == $post->comment_status && !is_user_logged_in()) 
					return;
			
			$expand = ($aecomments->get_admin_option( 'expand_posts' ) == 'true' ? true: false);
			if ( !$expand ) 
				return;
			
			wp_enqueue_style( 'aec_frontend', $aecomments->get_plugin_url( 'css/frontend.css' ), array(), $aecomments->get_version(), 'all' );
			wp_enqueue_style( 'aec_atd', $aecomments->get_plugin_url( 'css/atd/atd.css' ), array(), $aecomments->get_version(), 'all' );
			
			if ($expand) {
				//wp_deregister_style('colorbox');
				AECDependencies::queue_colorbox_style();
			}
			
		} //end load_frontend_css
		public static function queue_colorbox_script( $min = '', $load_footer = false) {
			global $aecomments;
			if ( !is_admin() && $aecomments->get_admin_option( 'enable_colorbox' ) == 'false' ) return false;
			wp_enqueue_script($aecomments->get_colorbox_param( 'script_handler' ), $aecomments->get_plugin_url() . "/js/jquery.colorbox{$min}.js", array("jquery"),$aecomments->get_version(),$load_footer);
			return true;
		} //end queue_colorbox_script
		public static function queue_colorbox_style() {
			global $aecomments;
			if ( !is_admin() && $aecomments->get_admin_option( 'enable_colorbox' ) == 'false' ) return false;
			wp_enqueue_style($aecomments->get_colorbox_param( 'style_handler' ), $aecomments->get_plugin_url( '/css/colorbox/colorbox.css' ),array(),$aecomments->get_version(),'screen');		
			return true;
		} //end queue_colorbox_style
}
?>