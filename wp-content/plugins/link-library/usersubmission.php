<?php

function link_library_process_user_submission( $my_link_library_plugin ) {
	check_admin_referer( 'LL_ADDLINK_FORM' );

	require_once( ABSPATH . '/wp-admin/includes/taxonomy.php' );

	load_plugin_textdomain( 'link-library', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	global $wpdb;

	$settings     = ( isset( $_POST['settingsid'] ) ? $_POST['settingsid'] : 1 );
	$settingsname = 'LinkLibraryPP' . $settings;
	$options      = get_option( $settingsname );
	$options = wp_parse_args( $options, ll_reset_options( 1, 'list', 'return' ) );

	$genoptions = get_option( 'LinkLibraryGeneral' );
	$genoptions = wp_parse_args( $genoptions, ll_reset_gen_settings( 'return' ) );

	$valid   = false;
	$requiredcheck = true;
	$message = "";

	$captureddata                           = array();
	$captureddata['link_category']          = ( isset( $_POST['link_category'] ) ? $_POST['link_category'] : '' );
	$captureddata['link_user_category']     = ( isset( $_POST['link_user_category'] ) ? $_POST['link_user_category'] : '' );
	$captureddata['link_description']       = ( isset( $_POST['link_description'] ) ? $_POST['link_description'] : '' );
	$captureddata['link_textfield']         = ( isset( $_POST['link_textfield'] ) ? $_POST['link_textfield'] : '' );
	$captureddata['link_name']              = ( isset( $_POST['link_name'] ) ? $_POST['link_name'] : '' );
	$captureddata['link_url']               = ( isset( $_POST['link_url'] ) ? $_POST['link_url'] : '' );
	$captureddata['link_rss']               = ( isset( $_POST['link_rss'] ) ? $_POST['link_rss'] : '' );
	$captureddata['link_notes']             = ( isset( $_POST['link_notes'] ) ? $_POST['link_notes'] : '' );
	$captureddata['ll_secondwebaddr']       = ( isset( $_POST['ll_secondwebaddr'] ) ? $_POST['ll_secondwebaddr'] : '' );
	$captureddata['ll_telephone']           = ( isset( $_POST['ll_telephone'] ) ? $_POST['ll_telephone'] : '' );
	$captureddata['ll_email']               = ( isset( $_POST['ll_email'] ) ? $_POST['ll_email'] : '' );
	$captureddata['ll_reciprocal']          = ( isset( $_POST['ll_reciprocal'] ) ? $_POST['ll_reciprocal'] : '' );
	$captureddata['ll_submittername']       = ( isset( $_POST['ll_submittername'] ) ? $_POST['ll_submittername'] : '' );
	$captureddata['ll_submitteremail']      = ( isset( $_POST['ll_submitteremail'] ) ? $_POST['ll_submitteremail'] : '' );
	$captureddata['ll_submittercomment']    = ( isset( $_POST['ll_submittercomment'] ) ? $_POST['ll_submittercomment'] : '' );
	$captureddata['ll_customcaptchaanswer'] = ( isset( $_POST['ll_customcaptchaanswer'] ) ? $_POST['ll_customcaptchaanswer'] : '' );

	if ( 'required' == $options['showaddlinkrss'] && empty( $captureddata['link_rss'] ) ) {
		$requiredcheck = false;
		$message = 11;
	} else if ( 'required' == $options['showaddlinkdesc'] && empty( $captureddata['link_description'] ) ) {
		$requiredcheck = false;
		$message = 12;
	} else if ( 'required' == $options['showaddlinknotes'] && empty( $captureddata['link_notes'] ) ) {
		$requiredcheck = false;
		$message = 13;
	} else if ( 'required' == $options['showaddlinkreciprocal'] && empty( $captureddata['ll_reciprocal'] ) ) {
		$requiredcheck = false;
		$message = 14;
	} else if ( 'required' == $options['showaddlinksecondurl'] && empty( $captureddata['ll_secondwebaddr'] ) ) {
		$requiredcheck = false;
		$message = 15;
	} else if ( 'required' == $options['showaddlinktelephone'] && empty( $captureddata['ll_telephone'] ) ) {
		$requiredcheck = false;
		$message = 16;
	} else if ( 'required' == $options['showaddlinkemail'] && empty( $captureddata['ll_email'] ) ) {
		$requiredcheck = false;
		$message = 17;
	} else if ( 'required' == $options['showlinksubmittername'] && empty( $captureddata['ll_submittername'] ) ) {
		$requiredcheck = false;
		$message = 18;
	} else if ( 'required' == $options['showaddlinksubmitteremail'] && empty( $captureddata['ll_submitteremail'] ) ) {
		$requiredcheck = false;
		$message = 19;
	} else if ( 'required' == $options['showlinksubmittercomment'] && empty( $captureddata['ll_submittercomment'] ) ) {
		$requiredcheck = false;
		$message = 20;
	} else if ( 'required' == $options['showuserlargedescription'] && empty( $captureddata['link_textfield'] ) ) {
		$requiredcheck = false;
		$message = 21;
	}

	if ( $captureddata['link_name'] != '' && $requiredcheck ) {
		if ( $options['showcaptcha'] ) {
			$message = apply_filters( 'link_library_verify_captcha', '' );
			if ( $message > 0 ) {
				$valid = false;
			} else {
				$valid = true;
			}
		}

		if ( $options['showcustomcaptcha'] == 'show' ) {
			if ( $captureddata['ll_customcaptchaanswer'] == '' ) {
				$valid   = false;
				$message = 5;
			} else {
				if ( strtolower( $captureddata['ll_customcaptchaanswer'] ) == strtolower( $options['customcaptchaanswer'] ) ) {
					$valid = true;
				} else {
					$valid   = false;
					$message = 6;
				}
			}
		}

		if ( $valid || ( $options['showcaptcha'] == false && $options['showcustomcaptcha'] == 'hide' ) ) {
			$existinglinkquery = "SELECT * from " . $my_link_library_plugin->db_prefix() . "links l where l.link_name = '" . $captureddata['link_name'] . "' ";

			if ( ( $options['addlinknoaddress'] == false ) || ( $options['addlinknoaddress'] == true && $captureddata['link_url'] != "" ) ) {
				$existinglinkquery .= " and l.link_url = 'http://" . $captureddata['link_url'] . "'";
			}

			$existinglink = $wpdb->get_var( $existinglinkquery );

			if ( $existinglink == "" && ( ( $options['addlinknoaddress'] == false && $captureddata['link_url'] != "" ) || $options['addlinknoaddress'] == true ) ) {
				if ( $captureddata['link_category'] == 'new' && $captureddata['link_user_category'] != '' ) {
					$existingcatquery = "SELECT t.term_id FROM " . $my_link_library_plugin->db_prefix() . "terms t, " . $my_link_library_plugin->db_prefix() . "term_taxonomy tt ";
					$existingcatquery .= "WHERE t.name = '" . $captureddata['link_user_category'] . "' AND t.term_id = tt.term_id AND tt.taxonomy = 'link_category'";
					$existingcat = $wpdb->get_var( $existingcatquery );

					if ( ! $existingcat ) {
						$newlinkcatdata = array(
							"cat_name"             => $captureddata['link_user_category'],
							"category_description" => "",
							"category_nicename"    => sanitize_text_field( $captureddata['link_user_category'] )
						);
						$newlinkcat     = wp_insert_category( $newlinkcatdata );
						$newcatarray    = array( "term_id" => $newlinkcat );
						$newcattype     = array( "taxonomy" => 'link_category' );
						$wpdb->update( $my_link_library_plugin->db_prefix() . 'term_taxonomy', $newcattype, $newcatarray );
						$newlinkcat = array( $newlinkcat );
					} else {
						$newlinkcat = array( $existingcat );
					}

					$message = 8;
					$validcat = true;
				} elseif ( $captureddata['link_category'] == 'new' && $captureddata['link_user_category'] == '' ) {
					$message  = 7;
					$validcat = false;
				} else {
					$newlinkcat = array( $captureddata['link_category'] );

					$message = 8;

					$validcat = true;
				}

				if ( $validcat == true ) {
					if ( $options['showuserlinks'] == false ) {
						if ( $options['showifreciprocalvalid'] ) {
							$reciprocal_return = $my_link_library_plugin->CheckReciprocalLink( $genoptions['recipcheckaddress'], $captureddata['ll_reciprocal'] );

							if ( $reciprocal_return == 'exists_found' ) {
								$newlinkdesc       = $captureddata['link_description'];
								$newlinkvisibility = 'Y';
								unset ( $message );
							} else {
								$newlinkdesc       = '(LinkLibrary:AwaitingModeration:RemoveTextToApprove)' . $captureddata['link_description'];
								$newlinkvisibility = 'N';
							}
						} else {
							$newlinkdesc       = '(LinkLibrary:AwaitingModeration:RemoveTextToApprove)' . $captureddata['link_description'];
							$newlinkvisibility = 'N';
						}
					} else {
						$newlinkdesc       = $captureddata['link_description'];
						$newlinkvisibility = 'Y';
						unset ( $message );
					}

					$username = '';
					if ( $options['storelinksubmitter'] == true ) {
						global $current_user;

						get_currentuserinfo();

						if ( $current_user ) {
							$username = $current_user->user_login;
						}
					}

					$newlink   = array(
						"link_name"        => esc_html( stripslashes( $captureddata['link_name'] ) ),
						"link_url"         => esc_html( stripslashes( $captureddata['link_url'] ) ),
						"link_rss"         => esc_html( stripslashes( $captureddata['link_rss'] ) ),
						"link_description" => esc_html( stripslashes( $newlinkdesc ) ),
						"link_notes"       => esc_html( stripslashes( $captureddata['link_notes'] ) ),
						"link_category"    => $newlinkcat,
						"link_visible"     => $newlinkvisibility,
						'link_target'      => $options['linktarget']
					);
					$newlinkid = $my_link_library_plugin->link_library_insert_link( $newlink, false, $options['addlinknoaddress'] );

					$extradatatable = $my_link_library_plugin->db_prefix() . "links_extrainfo";
					$wpdb->insert( $extradatatable, array(
						'link_id'              => $newlinkid,
						'link_second_url'      => $captureddata['ll_secondwebaddr'],
						'link_telephone'       => $captureddata['ll_telephone'],
						'link_email'           => $captureddata['ll_email'],
						'link_reciprocal'      => $captureddata['ll_reciprocal'],
						'link_submitter'       => ( isset( $username ) ? $username : null ),
						'link_submitter_name'  => $captureddata['ll_submittername'],
						'link_submitter_email' => $captureddata['ll_submitteremail'],
						'link_textfield'       => $captureddata['link_textfield'],
						'link_no_follow'       => '',
						'link_featured'        => '',
						'link_manual_updated'  => ''
					) );

					if ( $options['emailnewlink'] ) {
						if ( $genoptions['moderatoremail'] != '' ) {
							$adminmail = $genoptions['moderatoremail'];
						} else {
							$adminmail = get_option( 'admin_email' );
						}
						$headers = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

						$emailmessage = __( 'A user submitted a new link to your Wordpress Link database.', 'link-library' ) . "<br /><br />";
						$emailmessage .= __( 'Link Name', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_name'] ) ) . "<br />";
						$emailmessage .= __( 'Link Address', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_url'] ) ) . "<br />";
						$emailmessage .= __( 'Link RSS', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_rss'] ) ) . "<br />";
						$emailmessage .= __( 'Link Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_description'] ) ) . "<br />";
						$emailmessage .= __( 'Link Large Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_textfield'] ) ) . "<br />";
						$emailmessage .= __( 'Link Notes', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_notes'] ) ) . "<br />";
						$emailmessage .= __( 'Link Category', 'link-library' ) . ": " . $captureddata['link_category'] . "<br /><br />";
						$emailmessage .= __( 'Reciprocal Link', 'link-library' ) . ": " . $captureddata['ll_reciprocal'] . "<br /><br />";
						$emailmessage .= __( 'Link Secondary Address', 'link-library' ) . ": " . $captureddata['ll_secondwebaddr'] . "<br /><br />";
						$emailmessage .= __( 'Link Telephone', 'link-library' ) . ": " . $captureddata['ll_telephone'] . "<br /><br />";
						$emailmessage .= __( 'Link E-mail', 'link-library' ) . ": " . $captureddata['ll_email'] . "<br /><br />";
						$emailmessage .= __( 'Link Submitter', 'link-library' ) . ": " . $username . "<br /><br />";
						$emailmessage .= __( 'Link Submitter Name', 'link-library' ) . ": " . $captureddata['ll_submittername'] . "<br /><br />";
						$emailmessage .= __( 'Link Submitter E-mail', 'link-library' ) . ": " . $captureddata['ll_submitteremail'] . "<br /><br />";
						$emailmessage .= __( 'Link Comment', 'link-library' ) . ": " . $captureddata['ll_submittercomment'] . "<br /><br />";

						if ( $options['showuserlinks'] == false ) {
							$emailmessage .= '<a href="' . esc_url( add_query_arg( 's', 'LinkLibrary%3AAwaitingModeration%3ARemoveTextToApprove', admin_url( 'link-manager.php' ) ) ) . '">Moderate new links</a>';
						} elseif ( $options['showuserlinks'] == true ) {
							$emailmessage .= '<a href="' . admin_url( 'link-manager.php' ) . '">View links</a>';
						}

						$emailmessage .= "<br /><br />" . __( 'Message generated by', 'link-library' ) . " <a href='http://yannickcorner.nayanna.biz/wordpress-plugins/link-library/'>Link Library</a> for Wordpress";

						if ( ! isset( $emailtitle ) || $emailtitle == '' ) {
							$emailtitle = stripslashes( $genoptions['moderationnotificationtitle'] );
							$emailtitle = str_replace( '%linkname%', esc_html( stripslashes( $captureddata['link_name'] ) ), $emailtitle );
						} else {
							$emailtitle = htmlspecialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . " - " . __( 'New link added', 'link-library' ) . ": " . htmlspecialchars( $captureddata['link_name'] );
						}

						wp_mail( $adminmail, $emailtitle, $emailmessage, $headers );
					}
				}
			} elseif ( $existinglink == "" && ( $options['addlinknoaddress'] == false && $captureddata['link_url'] == "" ) ) {
				$message = 9;
			} else {
				$message = 10;
			}
		}
	}

	$redirectaddress = '';

	if ( isset( $_POST['thankyouurl'] ) && $_POST['thankyouurl'] != '' ) {
		$redirectaddress = $_POST['thankyouurl'];
	} else {
		if ( isset ( $_POST['pageid'] ) && is_numeric( $_POST['pageid'] ) ) {
			$redirectaddress = get_permalink( $_POST['pageid'] );
		}
	}

	$redirectaddress = esc_url( add_query_arg( 'addlinkmessage', $message, $redirectaddress ) );

	if ( $valid == false && ( $options['showcaptcha'] == true || $options['showcustomcaptcha'] == 'show' ) ) {
		if ( isset( $_POST['link_name'] ) && $_POST['link_name'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkname', rawurlencode( $captureddata['link_name'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['link_url'] ) && $_POST['link_url'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkurl', rawurlencode( $captureddata['link_url'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['link_category'] ) && $_POST['link_category'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkcat', rawurlencode( $captureddata['link_category'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['link_user_category'] ) && $_POST['link_user_category'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkusercat', rawurlencode( $captureddata['link_user_category'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['link_description'] ) && $_POST['link_description'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkdesc', rawurlencode( $captureddata['link_description'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['link_textfield'] ) && $_POST['link_textfield'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinktextfield', rawurlencode( $captureddata['link_textfield'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['link_rss'] ) && $_POST['link_rss'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkrss', rawurlencode( $captureddata['link_rss'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['link_notes'] ) && $_POST['link_notes'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinknotes', rawurlencode( $captureddata['link_notes'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_secondwebaddr'] ) && $_POST['ll_secondwebaddr'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinksecondurl', rawurlencode( $captureddata['ll_secondwebaddr'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_telephone'] ) && $_POST['ll_telephone'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinktelephone', rawurlencode( $captureddata['ll_telephone'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_email'] ) && $_POST['ll_email'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkemail', rawurlencode( $captureddata['ll_email'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_reciprocal'] ) && $_POST['ll_reciprocal'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkreciprocal', rawurlencode( $captureddata['ll_reciprocal'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_submittername'] ) && $_POST['ll_submittername'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinksubmitname', rawurlencode( $captureddata['ll_submittername'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_submitteremail'] ) && $_POST['ll_submitteremail'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinksubmitemail', rawurlencode( $captureddata['ll_submitteremail'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_submittercomment'] ) && $_POST['ll_submittercomment'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinksubmitcomment', rawurlencode( $captureddata['ll_submittercomment'] ), $redirectaddress ) );
		}

		if ( isset( $_POST['ll_customcaptchaanswer'] ) && $_POST['ll_customcaptchaanswer'] != '' ) {
			$redirectaddress = esc_url( add_query_arg( 'addlinkcustomcaptcha', rawurlencode( $captureddata['ll_customcaptchaanswer'] ), $redirectaddress ) );
		}
	}

	wp_redirect( $redirectaddress );
	exit;
}

function link_library_verify_captcha() {

	$message = 0;

	if ( empty( $_REQUEST['confirm_code'] ) ) {
		$message = 1;
	} else {
		if ( isset( $_COOKIE['Captcha'] ) ) {
			list( $Hash, $Time ) = explode( '.', $_COOKIE['Captcha'] );
			if ( md5( "ORHFUKELFPTUEODKFJ" . $_REQUEST['confirm_code'] . $_SERVER['REMOTE_ADDR'] . $Time ) != $Hash ) {
				$message = 2;
			} elseif ( ( time() - 5 * 60 ) > $Time ) {
				$message = 3;
			}
		} else {
			$message = 4;
		}
	}

	return $message;
}

add_filter( 'link_library_verify_captcha', 'link_library_verify_captcha' );

