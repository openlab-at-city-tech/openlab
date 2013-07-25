<?php
class AECJS {
		public static function output_js( $handler, $dependencies = array(), $in_footer = false ) {
			AECJS::get_js( $handler, $dependencies, $in_footer ); //echo out
		} //end output_js
		
		public static function get_js( $handler, $dependencies, $in_footer ) {
			global $aecomments;
			$min = $aecomments->get_admin_option( 'compressed_scripts' ) == 'true' ? ".min" : '';
			$load_footer = ($aecomments->get_admin_option( 'scripts_in_footer' ) == "true" ? true: false);

			switch ( $handler ) {
				case "wp_ajax_edit_comments_script": /* Allows editing of icon items */
					wp_enqueue_script( $handler, $aecomments->get_plugin_url( "/js/wp-ajax-edit-comments$min.js" ), $dependencies, $aecomments->get_version(), $in_footer );
					wp_localize_script( $handler, 'wpajaxeditcomments', AECDependencies::get_js_vars() );
					break;
				case "aec_admin": /* Admin panel scripts */
					//Admin panel sortables and tabs
					wp_enqueue_script( 'aec_admin_init', $aecomments->get_plugin_url( '/js/admin-panel.js' ), $dependencies, $aecomments->get_version(), $in_footer );
					wp_enqueue_script( 'aec_admin_tabs', $aecomments->get_plugin_url( '/js/tab-config.js' ), array( 'aec_admin_init' ), $aecomments->get_version(), $in_footer );
					break;
				case "aec_frontend": /* After the Deadline and Expand popup */
					$atdlang = "true";
					$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_posts' ) == "true" ? true : false);
					if (!$afterthedeadline) {
						$atdlang = "false";
					}	
					$aec_frontend = 	site_url( '/?aec_page=comment-popup.php' );
					wp_enqueue_script( 'aec_atd', $aecomments->get_plugin_url( 'js/jquery.atd.textarea.js' ), $dependencies, $aecomments->get_version(), $in_footer );
					wp_enqueue_script( 'aec_frontend', $aecomments->get_plugin_url( 'js/frontend.js' ), array( 'aec_atd' ), $aecomments->get_version(), $in_footer );
					
					wp_localize_script( 'aec_atd', 'aec_frontend', array('atdlang' => $atdlang, 'atd' => $aecomments->get_admin_option( 'after_deadline_posts' ),'expand' => $aecomments->get_admin_option( 'expand_posts' ),'url' => $aec_frontend, 'title' => __('Comment Box', 'ajaxEdit') ) );
					break;
			} //end switch
		} //end get_us
		
		public static function register_popups_js( $handler ) {
			global $aecomments;
			$min = $aecomments->get_admin_option( 'compressed_scripts' ) == 'true' ? ".min" : '';
			$atdlang = "true";
			$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_posts' ) == "true" ? true : false);
			if (!$afterthedeadline) {
				$atdlang = "false";
			}
			wp_register_script( 'jquery-tools', $aecomments->get_plugin_url( '/js/jquery.tools.min.js' ) , array('jquery'), $aecomments->get_version(), true);
			wp_register_script( 'jquery-tools-tabs', $aecomments->get_plugin_url( '/js/tab-config.js' ), array( 'jquery-tools' ), $aecomments->get_version() );
			
			$localize_vars = AECDependencies::get_js_vars();			
			$deps = array( 'jquery' );
			
			$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_popups' ) == "true"  ? true : false);
			if ($afterthedeadline) {
				wp_register_script( 'aec_atd', $aecomments->get_plugin_url( 'js/jquery.atd.textarea.js' ), 'jquery', $aecomments->get_version() );
				$deps[] = 'aec_atd';
			}
			
			wp_register_script( 'aec_popups', $aecomments->get_plugin_url( "/js/{$handler}{$min}.js" ), $deps, $aecomments->get_version() );
			wp_localize_script( 'aec_popups', 'wpajaxeditcommentedit', $localize_vars );
			wp_localize_script( 'aec_popups', 'aec_popup', array('atdlang' => $atdlang, 'atd' => $aecomments->get_admin_option( 'after_deadline_posts' ),'expand' => $aecomments->get_admin_option( 'expand_posts' ), 'title' => __('Comment Box', 'ajaxEdit') ) );
			
			//Include the various interfaces
			/*include( $aecomments->get_plugin_dir( "/js/comment-editor{$min}.js" ) );
			include( $aecomments->get_plugin_dir( "/js/blacklist-comment{$min}.js" ) );
			include( $aecomments->get_plugin_dir( "/js/comment-popup{$min}.js" ) );
			include( $aecomments->get_plugin_dir( "/js/email{$min}.js" ) );
			include( $aecomments->get_plugin_dir( "/js/move-comment{$min}.js" ) );
			include( $aecomments->get_plugin_dir( "/js/request-deletion{$min}.js" ) );*/
			
			
		} //end get_js_popups
		
} //end AECJS
