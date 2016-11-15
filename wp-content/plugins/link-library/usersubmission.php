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

	$valid   = true;
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
		if ( ( $options['addlinkakismet'] || $genoptions['addlinkakismet'] ) && ll_akismet_is_available() ) {
			$c = array();

			if ( !empty( $captureddata['ll_submittername'] ) ) {
				$c['comment_author'] = $captureddata['ll_submittername'];
			}

			if ( !empty( $captureddata['ll_submitteremail'] ) ) {
				$c['comment_author_email'] = $captureddata['ll_submitteremail'];
			}

			if ( !empty( $captureddata['link_url'] ) ) {
				$c['comment_author_url'] = $captureddata['link_url'];
			}

			if ( !empty( $captureddata['link_description'] ) && ( 'required' == $options['showaddlinkdesc'] || 'show' == $options['showaddlinkdesc'] ) ) {
				$c['comment_content'] = $captureddata['link_description'];

			} elseif ( !empty( $captureddata['link_notes'] ) && ( 'required' == $options['showaddlinknotes'] || 'show' == $options['showaddlinknotes'] ) ) {
				$c['comment_content'] = $captureddata['link_notes'];
			} elseif ( !empty( $captureddata['link_textfield'] ) && ( 'required' == $options['showuserlargedescription'] || 'show' == $options['showuserlargedescription'] ) ) {
				$c['comment_content'] = $captureddata['link_textfield'];
			}

			$c['blog'] = get_option( 'home' );
			$c['blog_lang'] = get_locale();
			$c['blog_charset'] = get_option( 'blog_charset' );
			$c['user_ip'] = $_SERVER['REMOTE_ADDR'];
			$c['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$c['referrer'] = $_SERVER['HTTP_REFERER'];

			$c['comment_type'] = 'link-library';

			$ignore = array( 'HTTP_COOKIE', 'HTTP_COOKIE2', 'PHP_AUTH_PW' );

			foreach ( $_SERVER as $key => $value ) {
				if ( ! in_array( $key, (array) $ignore ) )
					$c["$key"] = $value;
			}

			if ( ll_akismet_comment_check( $c ) ) {
				$valid = false;
				$message = 22;
			} else {
				$valid = true;
			};
		} elseif ( ( $options['addlinkakismet'] || $genoptions['addlinkakismet'] ) && !ll_akismet_is_available() ) {
			echo 'Akismet has been selected but is not available';
			die();
		}

		if ( $options['showcaptcha'] && $valid ) {
			$message = apply_filters( 'link_library_verify_captcha', '' );
			if ( $message > 0 ) {
				$valid = false;
			} else {
				$valid = true;
			}
		}

		if ( $options['showcustomcaptcha'] == 'show' && $valid ) {
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

		if ( $valid && $options['onereciprocaldomain'] && ( 'required' == $options['showaddlinkreciprocal'] || ( 'show' == $options['showaddlinkreciprocal'] && !empty( $captureddata['ll_reciprocal'] ) ) ) ) {
			$parsed_new_reciprocal = parse_url( esc_url( $captureddata['ll_reciprocal'] ) );
			$reciprocal_domain = $parsed_new_reciprocal['host'];

			$reciprocal_query = "SELECT link_reciprocal from " . $my_link_library_plugin->db_prefix() . "links_extrainfo le  ";

			$reciprocal_links = $wpdb->get_results( $reciprocal_query );

			foreach( $reciprocal_links as $recip_link ) {
				$parse_data = parse_url( $recip_link->link_reciprocal );
				if ( $reciprocal_domain == $parse_data['host'] ) {
					$valid = false;
					$message = 23;
					break;
				}
			}
		}

		if ( $valid ) {
			$existinglinkquery = "SELECT * from " . $my_link_library_plugin->db_prefix() . "links l where l.link_name = '" . $captureddata['link_name'] . "' ";

			if ( ( $options['addlinknoaddress'] == false ) || ( $options['addlinknoaddress'] == true && $captureddata['link_url'] != "" ) ) {
				$existinglinkquery .= " or l.link_url = 'http://" . $captureddata['link_url'] . "'";
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
						$current_user = wp_get_current_user();

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
						'link_target'      => $options['linktarget'],
						'link_updated'     => current_time( 'mysql' )
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

						$link_category_name = '';

						if ( $captureddata['link_category'] == 'new' && $captureddata['link_user_category'] != '' ) {
							$link_category_name = $captureddata['link_user_category'];
						} else if ( !empty( $captureddata['link_category'] ) ) {
							$find_cat_name_query = "SELECT t.name FROM " . $my_link_library_plugin->db_prefix() . "terms t, " . $my_link_library_plugin->db_prefix() . "term_taxonomy tt ";
							$find_cat_name_query .= "WHERE t.term_id = '" . $captureddata['link_category'] . "' AND t.term_id = tt.term_id AND tt.taxonomy = 'link_category'";
							$link_category_name = $wpdb->get_var( $find_cat_name_query );
						}

						$headers = "MIME-Version: 1.0\r\n";
						$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

						$emailmessage = __( 'A user submitted a new link to your Wordpress Link database.', 'link-library' ) . "<br /><br />";
						$emailmessage .= __( 'Link Name', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_name'] ) ) . "<br />";
						$emailmessage .= __( 'Link Address', 'link-library' ) . ": <a href='" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "'>" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "</a><br />";

						if ( 'show' == $options['showaddlinkrss'] || 'required' == $options['showaddlinkrss'] ) {
							$emailmessage .= __( 'Link RSS', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_rss'] ) ) . "<br />";
						}

						if ( 'show' == $options['showaddlinkdesc'] || 'required' == $options['showaddlinkdesc'] ) {
							$emailmessage .= __( 'Link Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_description'] ) ) . "<br />";
						}

						if ( 'show' == $options['showuserlargedescription'] || 'required' == $options['showuserlargedescription'] ) {
							$emailmessage .= __( 'Link Large Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_textfield'] ) ) . "<br />";
						}

						if ( 'show' == $options['showaddlinknotes'] || 'required' == $options['showaddlinknotes'] ) {
							$emailmessage .= __( 'Link Notes', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_notes'] ) ) . "<br />";
						}

						$emailmessage .= __( 'Link Category', 'link-library' ) . ": " . $link_category_name . " ( " . $captureddata['link_category'] . " )<br /><br />";

						if ( 'show' == $options['showaddlinkreciprocal'] || 'required' == $options['showaddlinkreciprocal'] ) {
							$emailmessage .= __( 'Reciprocal Link', 'link-library' ) . ": " . $captureddata['ll_reciprocal'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinksecondurl'] || 'required' == $options['showaddlinksecondurl'] ) {
							$emailmessage .= __( 'Link Secondary Address', 'link-library' ) . ": " . $captureddata['ll_secondwebaddr'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinktelephone'] || 'required' == $options['showaddlinktelephone'] ) {
							$emailmessage .= __( 'Link Telephone', 'link-library' ) . ": " . $captureddata['ll_telephone'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinkemail'] || 'required' == $options['showaddlinkemail'] ) {
							$emailmessage .= __( 'Link E-mail', 'link-library' ) . ": " . $captureddata['ll_email'] . "<br /><br />";
						}

						if ( 'show' == $options['showlinksubmittername'] || 'required' == $options['showlinksubmittername'] ) {
							$emailmessage .= __( 'Link Submitter Name', 'link-library' ) . ": " . $captureddata['ll_submittername'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinksubmitteremail'] || 'required' == $options['showaddlinksubmitteremail'] ) {
							$emailmessage .= __( 'Link Submitter E-mail', 'link-library' ) . ": " . $captureddata['ll_submitteremail'] . "<br /><br />";
						}

						if ( 'show' == $options['showlinksubmittercomment'] || 'required' == $options['showlinksubmittercomment'] ) {
							$emailmessage .= __( 'Link Comment', 'link-library' ) . ": " . $captureddata['ll_submittercomment'] . "<br /><br />";
						}

						if ( $options['showuserlinks'] == false ) {
							$emailmessage .= '<a href="' . esc_url( add_query_arg( 's', 'LinkLibrary%3AAwaitingModeration%3ARemoveTextToApprove', admin_url( 'link-manager.php' ) ) ) . '">Moderate new links</a>';
						} elseif ( $options['showuserlinks'] == true ) {
							$emailmessage .= '<a href="' . admin_url( 'link-manager.php' ) . '">View links</a>';
						}

						$emailmessage .= "<br /><br />" . __( 'Message generated by', 'link-library' ) . " <a href='http://ylefebvre.ca/wordpress-plugins/link-library/'>Link Library</a> for Wordpress";

						if ( ! isset( $emailtitle ) || $emailtitle == '' ) {
							$emailtitle = stripslashes( $genoptions['moderationnotificationtitle'] );
							$emailtitle = str_replace( '%linkname%', esc_html( stripslashes( $captureddata['link_name'] ) ), $emailtitle );
						} else {
							$emailtitle = htmlspecialchars_decode( get_option( 'blogname' ), ENT_QUOTES ) . " - " . __( 'New link added', 'link-library' ) . ": " . htmlspecialchars( $captureddata['link_name'] );
						}

						wp_mail( $adminmail, $emailtitle, $emailmessage, $headers );
					}

					if ( $options['emailsubmitter'] && !empty( $captureddata['ll_submitteremail'] ) && is_email( $captureddata['ll_submitteremail'] ) ) {
						$submitteremailheaders = "MIME-Version: 1.0\r\n";
						$submitteremailheaders .= "Content-type: text/html; charset=iso-8859-1\r\n";

						$submitteremailtitle = __( 'Link Submission Confirmation', 'link-library' );

						$submitteremailmessage = '<p>' . __( 'Thank you for your link submission on ', 'link-library' );
						$submitteremailmessage .= esc_html( get_bloginfo( 'name' ) ) . '</p>';

						if ( $options['showuserlinks'] == false ) {
							$submitteremailmessage .= '<p>' . __( 'Your link will appear once approved by the site administrator.', 'link-library' ) . '</p>';
						} elseif ( $options['showuserlinks'] == true ) {
							$submitteremailmessage .= '<p>' . __( 'Your link will immediately be added to the site.', 'link-library' ) . '</p>';
						}

						$submitteremailmessage .= __( 'Link Name', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_name'] ) ) . "<br />";
						$submitteremailmessage .= __( 'Link Address', 'link-library' ) . ": <a href='" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "'>" . esc_html( stripslashes( $captureddata['link_url'] ) ) . "</a><br />";

						if ( 'show' == $options['showaddlinkrss'] || 'required' == $options['showaddlinkrss'] ) {
							$submitteremailmessage .= __( 'Link RSS', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_rss'] ) ) . "<br />";
						}

						if ( 'show' == $options['showaddlinkdesc'] || 'required' == $options['showaddlinkdesc'] ) {
							$submitteremailmessage .= __( 'Link Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_description'] ) ) . "<br />";
						}

						if ( 'show' == $options['showuserlargedescription'] || 'required' == $options['showuserlargedescription'] ) {
							$submitteremailmessage .= __( 'Link Large Description', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_textfield'] ) ) . "<br />";
						}

						if ( 'show' == $options['showaddlinknotes'] || 'required' == $options['showaddlinknotes'] ) {
							$submitteremailmessage .= __( 'Link Notes', 'link-library' ) . ": " . esc_html( stripslashes( $captureddata['link_notes'] ) ) . "<br />";
						}

						$submitteremailmessage .= __( 'Link Category', 'link-library' ) . ": " . $link_category_name . " ( " . $captureddata['link_category'] . " )<br /><br />";

						if ( 'show' == $options['showaddlinkreciprocal'] || 'required' == $options['showaddlinkreciprocal'] ) {
							$submitteremailmessage .= __( 'Reciprocal Link', 'link-library' ) . ": " . $captureddata['ll_reciprocal'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinksecondurl'] || 'required' == $options['showaddlinksecondurl'] ) {
							$submitteremailmessage .= __( 'Link Secondary Address', 'link-library' ) . ": " . $captureddata['ll_secondwebaddr'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinktelephone'] || 'required' == $options['showaddlinktelephone'] ) {
							$submitteremailmessage .= __( 'Link Telephone', 'link-library' ) . ": " . $captureddata['ll_telephone'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinkemail'] || 'required' == $options['showaddlinkemail'] ) {
							$submitteremailmessage .= __( 'Link E-mail', 'link-library' ) . ": " . $captureddata['ll_email'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinksubmittername'] || 'required' == $options['showaddlinksubmittername'] ) {
							$submitteremailmessage .= __( 'Link Submitter Name', 'link-library' ) . ": " . $captureddata['ll_submittername'] . "<br /><br />";
						}

						if ( 'show' == $options['showaddlinksubmitteremail'] || 'required' == $options['showaddlinksubmitteremail'] ) {
							$submitteremailmessage .= __( 'Link Submitter E-mail', 'link-library' ) . ": " . $captureddata['ll_submitteremail'] . "<br /><br />";
						}

						if ( 'show' == $options['showlinksubmittercomment'] || 'required' == $options['showlinksubmittercomment'] ) {
							$submitteremailmessage .= __( 'Link Comment', 'link-library' ) . ": " . $captureddata['ll_submittercomment'] . "<br /><br />";
						}

						wp_mail( $captureddata['ll_submitteremail'], $submitteremailtitle, $submitteremailmessage, $submitteremailheaders );
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

	if ( isset( $_POST['thankyouurl'] ) && $_POST['thankyouurl'] != '' && $requiredcheck && $valid ) {
		$redirectaddress = $_POST['thankyouurl'];
	} else {
		if ( isset ( $_POST['pageid'] ) && is_numeric( $_POST['pageid'] ) ) {
			$redirectaddress = get_permalink( $_POST['pageid'] );
		}
	}

	$redirectaddress = esc_url_raw( add_query_arg( 'addlinkmessage', $message, $redirectaddress ) );

	if ( $valid == false && ( $options['showcaptcha'] == true || $options['showcustomcaptcha'] == 'show' || $options['onereciprocaldomain'] ) ) {
		if ( isset( $_POST['link_name'] ) && $_POST['link_name'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkname', rawurlencode( $captureddata['link_name'] ), $redirectaddress );
		}

		if ( isset( $_POST['link_url'] ) && $_POST['link_url'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkurl', rawurlencode( $captureddata['link_url'] ), $redirectaddress );
		}

		if ( isset( $_POST['link_category'] ) && $_POST['link_category'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkcat', rawurlencode( $captureddata['link_category'] ), $redirectaddress );
		}

		if ( isset( $_POST['link_user_category'] ) && $_POST['link_user_category'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkusercat', rawurlencode( $captureddata['link_user_category'] ), $redirectaddress );
		}

		if ( isset( $_POST['link_description'] ) && $_POST['link_description'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkdesc', rawurlencode( $captureddata['link_description'] ), $redirectaddress );
		}

		if ( isset( $_POST['link_textfield'] ) && $_POST['link_textfield'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinktextfield', rawurlencode( $captureddata['link_textfield'] ), $redirectaddress );
		}

		if ( isset( $_POST['link_rss'] ) && $_POST['link_rss'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkrss', rawurlencode( $captureddata['link_rss'] ), $redirectaddress );
		}

		if ( isset( $_POST['link_notes'] ) && $_POST['link_notes'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinknotes', rawurlencode( $captureddata['link_notes'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_secondwebaddr'] ) && $_POST['ll_secondwebaddr'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinksecondurl', rawurlencode( $captureddata['ll_secondwebaddr'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_telephone'] ) && $_POST['ll_telephone'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinktelephone', rawurlencode( $captureddata['ll_telephone'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_email'] ) && $_POST['ll_email'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkemail', rawurlencode( $captureddata['ll_email'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_reciprocal'] ) && $_POST['ll_reciprocal'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkreciprocal', rawurlencode( $captureddata['ll_reciprocal'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_submittername'] ) && $_POST['ll_submittername'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinksubmitname', rawurlencode( $captureddata['ll_submittername'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_submitteremail'] ) && $_POST['ll_submitteremail'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinksubmitemail', rawurlencode( $captureddata['ll_submitteremail'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_submittercomment'] ) && $_POST['ll_submittercomment'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinksubmitcomment', rawurlencode( $captureddata['ll_submittercomment'] ), $redirectaddress );
		}

		if ( isset( $_POST['ll_customcaptchaanswer'] ) && $_POST['ll_customcaptchaanswer'] != '' ) {
			$redirectaddress = add_query_arg( 'addlinkcustomcaptcha', rawurlencode( $captureddata['ll_customcaptchaanswer'] ), $redirectaddress );
		}

		$redirectaddress = esc_url_raw( $redirectaddress );
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

function ll_akismet_is_available() {
	if ( is_callable( array( 'Akismet', 'get_api_key' ) ) ) { // Akismet v3.0+
		return (bool) Akismet::get_api_key();
	}

	if ( function_exists( 'akismet_get_key' ) ) {
		return (bool) akismet_get_key();
	}

	return false;
}

function ll_akismet_comment_check( $comment ) {
	global $akismet_api_host, $akismet_api_port;

	$spam = false;
	$query_string = http_build_query( $comment );

	if ( is_callable( array( 'Akismet', 'http_post' ) ) ) { // Akismet v3.0+
		$response = Akismet::http_post( $query_string, 'comment-check' );
	} else {
		$response = akismet_http_post( $query_string, $akismet_api_host,
			'/1.1/comment-check', $akismet_api_port );
	}

	if ( 'true' == $response[1] ) {
		$spam = true;
	}

	if ( class_exists( WPCF7_Submission ) && $submission = WPCF7_Submission::get_instance() ) {
		$submission->akismet = array( 'comment' => $comment, 'spam' => $spam );
	}

	return apply_filters( 'll_akismet_comment_check', $spam, $comment );
}