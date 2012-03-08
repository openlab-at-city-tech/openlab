<?php
class AECJS {
		public static function output_js( $handler, $dependencies = array(), $in_footer = false, $name, $type ) {
			global $aecomments;
			//Output JS or enqueue depending on if a file exists or not
			$js_uri = AECJS::get_js_url( $name, $type );
			if ( is_wp_error( $js_uri ) ) {
				AECJS::get_js( $type, true ); //echo out
			} else {
				wp_enqueue_script( $handler, $js_uri, $dependencies, $aecomments->get_version(), $in_footer );
			}
		} //end output_interface_css
		public static function update_js_file( $name, $type ) {
			$path = AECJS::create_js_file( $name, $type );
			if ( is_wp_error( $path ) ) {
				return $path;
			}
			AECFile::write( $path, AECJS::get_js( $type ) );
		} //end update_js_file
		
		public static function create_js_file( $name, $type ) {
			$path = AECFile::get_writable_file( array( 'name' => $name, 'extension' => 'js' ) );
			if ( is_wp_error( $path ) ) {
				//File doesn't exist, try to create it
				$args = array( 'name' => $name, 'extension' => 'js', 'create_new' => true );
				$path = AECFile::create_writable_file( $args );
				
				//now try to write to it
				
				if ( !is_wp_error( $path ) ) {
					AECFile::write( $path, AECJS::get_js( $type ) );
				}
			}
			//Check to see if there's still an error
			if ( is_wp_error( $path ) ) {
				//Generate wp_error and pass back CSS as a string
				$error = new WP_Error( 'aec_js_file', 'Could not create or read JS file' );
				return $error;
			} 
			return $path;
			
		} //end create_css_file
		public static function get_js_url( $name, $type ) {
			global $aecomments;
			$path = AECJS::create_js_file( $name, $type );
			
			if ( !is_wp_error( $path ) ) {
				$js_url = AECFile::get_url_from_file( $path );
				return $js_url;
			} else {
				return $path;
			}
		} //end get_js_url
		public static function update_js( $name = '', $content = '') {
			global $aecomments;
			$path = AECFile::get_writable_file( array( 'name' => $name , 'extension' => 'js' ) );
			if ( is_wp_error( $path ) || empty( $content ) ) {
				$error = new WP_Error( 'aec_save_js', 'Could not save JS' );
				return $error;
			}
			return AECFile::write( $path, $content );
		}
		public static function get_js( $type, $echo = false ) {
			global $aecomments;
			$min = $aecomments->get_admin_option( 'compressed_scripts' ) == 'true' ? ".min" : '';
			$load_footer = ($aecomments->get_admin_option( 'scripts_in_footer' ) == "true" ? true: false);
			
			ob_start();
			switch ( $type ) {
				case "icons": /* Allows editing of icon items */
					AECUtility::js_localize( 'wpajaxeditcomments',AECDependencies::get_js_vars(), true );
					include( $aecomments->get_plugin_dir( "/js/wp-ajax-edit-comments$min.js" ) );
					break;
				case "admin": /* Admin panel scripts */
					//Upgrade script
					AECUtility::js_localize('aec_check_upgrades', array('checking' => __('Checking...', 'ajaxEdit'),'checkupgrades' => __( 'Check for Upgrades','ajaxEdit' ) ), true );
					//include( $aecomments->get_plugin_dir( 'js/check-upgrades.js' ) );
					//Admin panel sortables and tabs
					include( $aecomments->get_plugin_dir( '/js/admin-panel.js' ) );
					//Admin tab config
					include( $aecomments->get_plugin_dir( '/js/tab-config.js' ) );
					//Support
					AECUtility::js_localize('aecsupport', array('show' => __('show', 'ajaxEdit'),'hide' => __('hide','ajaxEdit')), true );
					include( $aecomments->get_plugin_dir( '/js/support.js' ) );
					break;
				case "frontend": /* After the Deadline and Expand popup */
					$atdlang = "true";
					$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_posts' ) == "true" ? true : false);
					if (!$afterthedeadline) {
						$atdlang = "false";
					}	
					$aec_frontend = 	$aecomments->get_admin_option( 'use_wpload' ) == 'true' ? $aecomments->get_plugin_url( '/views/comment-popup.php' ) : site_url( '/?aec_page=comment-popup.php' );
					AECUtility::js_localize('aec_frontend', array('atdlang' => $atdlang, 'atd' => $aecomments->get_admin_option( 'after_deadline_posts' ),'expand' => $aecomments->get_admin_option( 'expand_posts' ),'url' => $aec_frontend, 'title' => __('Comment Box', 'ajaxEdit')), true );
					include( $aecomments->get_plugin_dir( 'js/jquery.atd.textarea.js' ) );
					include( $aecomments->get_plugin_dir( 'js/frontend.js' ) );
					break;
				case "popups":
					$atdlang = "true";
					$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_posts' ) == "true" ? true : false);
					if (!$afterthedeadline) {
						$atdlang = "false";
					}	
					AECUtility::js_localize('wpajaxeditcommentedit',AECDependencies::get_js_vars(), true);
					AECUtility::js_localize('aec_popup', array('atdlang' => $atdlang, 'atd' => $aecomments->get_admin_option( 'after_deadline_posts' ),'expand' => $aecomments->get_admin_option( 'expand_posts' ), 'title' => __('Comment Box', 'ajaxEdit')), true );
					//Include the various interfaces
					include( $aecomments->get_plugin_dir( "/js/comment-editor{$min}.js" ) );
					include( $aecomments->get_plugin_dir( "/js/blacklist-comment{$min}.js" ) );
					include( $aecomments->get_plugin_dir( "/js/comment-popup{$min}.js" ) );
					include( $aecomments->get_plugin_dir( "/js/email{$min}.js" ) );
					include( $aecomments->get_plugin_dir( "/js/move-comment{$min}.js" ) );
					include( $aecomments->get_plugin_dir( "/js/request-deletion{$min}.js" ) );
					
					$afterthedeadline = ($aecomments->get_admin_option( 'after_deadline_popups' ) == "true"  ? true : false);
					if ($afterthedeadline) {
						include( $aecomments->get_plugin_dir( '/js/jquery.atd.textarea.js' ) );
					}
					include( $aecomments->get_plugin_dir( '/js/jquery.tools.min.js' ) );
					include( $aecomments->get_plugin_dir( '/js/tab-config.js' ) );
					break;
			} //end switch
			$content = ob_get_clean();
			//Return content
			if ( $echo ) {	
				echo "<!--Ajax Edit Comments Scripts-->\n";
				echo "<script type='text/javascript'>\n";
				echo $content;
				echo "\n</script>\n";
			} else {
				return $content;
			}	
		} //end get_interface_css
		
} //end AECJS
