<?php
if ( ! function_exists( 'add_action' ) ) {
	exit();
}

global $current_user;

$s2_admin = ! empty( $_POST['s2_admin'] ) ? sanitize_key( $_POST['s2_admin'] ) : '';

// was anything POSTed?
if ( 'mail' === $s2_admin ) {
	if ( isset( $_REQUEST['_wpnonce'] ) && ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'subscribe2-write_subscribers' . S2VERSION ) ) {
		die( '<p>' . esc_html__( 'Security error! Your request cannot be completed.', 'subscribe2' ) . '</p>' );
	}

	$subject = html_entity_decode( stripslashes( wp_kses( $this->substitute( $_POST['subject'] ), '' ) ), ENT_QUOTES );
	$body    = wpautop( $this->substitute( stripslashes( $_POST['content'] ) ), true );
	if ( '' !== $current_user->display_name || '' !== $current_user->user_email ) {
		$this->myname  = html_entity_decode( $current_user->display_name, ENT_QUOTES );
		$this->myemail = $current_user->user_email;
	}
	if ( isset( $_POST['send'] ) ) {
		if ( 'confirmed' === $_POST['what'] ) {
			$recipients = $this->get_public();
		} elseif ( 'unconfirmed' === $_POST['what'] ) {
			$recipients = $this->get_public( 0 );
		} elseif ( 'public' === $_POST['what'] ) {
			$confirmed   = $this->get_public();
			$unconfirmed = $this->get_public( 0 );
			$recipients  = array_merge( (array) $confirmed, (array) $unconfirmed );
		} elseif ( is_numeric( $_POST['what'] ) ) {
			$category   = intval( $_POST['what'] );
			$recipients = $this->get_registered( "cats=$category" );
		} elseif ( 'all_users' === $_POST['what'] ) {
			$recipients = $this->get_all_registered();
		} elseif ( 'all' === $_POST['what'] ) {
			$confirmed   = $this->get_public();
			$unconfirmed = $this->get_public( 0 );
			$registered  = $this->get_all_registered();
			$recipients  = array_merge( (array) $confirmed, (array) $unconfirmed, (array) $registered );
		} else {
			$recipients = $this->get_registered();
		}
	} elseif ( isset( $_POST['preview'] ) ) {
		global $user_email;
		$recipients[] = $user_email;
	}

	$uploads = array();
	if ( ! empty( $_FILES ) ) {
		foreach ( $_FILES['file']['name'] as $key => $value ) {
			if ( 0 === $_FILES['file']['error'][ $key ] ) {
				$file = array(
					'name'     => $_FILES['file']['name'][ $key ],
					'type'     => $_FILES['file']['type'][ $key ],
					'tmp_name' => $_FILES['file']['tmp_name'][ $key ],
					'error'    => $_FILES['file']['error'][ $key ],
					'size'     => $_FILES['file']['size'][ $key ],
				);

				$uploads[] = wp_handle_upload(
					$file,
					array(
						'test_form' => false,
					)
				);
			}
		}
	}
	$attachments = array();
	if ( ! empty( $uploads ) ) {
		foreach ( $uploads as $upload ) {
			if ( ! isset( $upload['error'] ) ) {
				$attachments[] = $upload['file'];
			} else {
				$upload_error = $upload['error'];
			}
		}
	}

	if ( empty( $body ) ) {
		$error_message = __( 'Your email was empty', 'subscribe2' );
		$success       = false;
	} elseif ( isset( $upload_error ) ) {
		$error_message = $upload_error;
		$success       = false;
	} else {
		$success       = $this->mail( $recipients, $subject, $body, 'html', $attachments );
		$error_message = __( 'Check your settings and check with your hosting provider', 'subscribe2' );
	}

	if ( $success ) {
		if ( isset( $_POST['preview'] ) ) {
			$message = '<p class="s2_message">' . __( 'Preview message sent!', 'subscribe2' ) . '</p>';
		} elseif ( isset( $_POST['send'] ) ) {
			$message = '<p class="s2_message">' . __( 'Message sent!', 'subscribe2' ) . '</p>';
		}
	} else {
		global $phpmailer;
		$message = '<p class="s2_error">' . __( 'Message failed!', 'subscribe2' ) . '</p>' . $error_message . $phpmailer->ErrorInfo;
	}
	echo '<div id="message" class="updated"><strong><p>' . wp_kses_post( $message ) . '</p></strong></div>' . "\r\n";
}

// show our form
echo '<div class="wrap">';
echo '<h1>' . esc_html__( 'Send an email to subscribers', 'subscribe2' ) . '</h1>' . "\r\n";
echo '<form method="post" enctype="multipart/form-data">' . "\r\n";

wp_nonce_field( 'subscribe2-write_subscribers' . S2VERSION );

$body = ! empty( $_POST['content'] ) ? esc_textarea( $_POST['content'] ) : '';
if ( isset( $_POST['subject'] ) ) {
	$subject = stripslashes( esc_html( $_POST['subject'] ) );
} else {
	$subject = __( 'A message from', 'subscribe2' ) . ' ' . html_entity_decode( get_option( 'blogname' ), ENT_QUOTES );
}

echo '<p>' . esc_html__( 'Subject', 'subscribe2' ) . ': <input type="text" size="69" name="subject" value="' . esc_attr( $subject ) . '" /> <br><br>';
echo '<textarea rows="12" cols="75" name="content">' . $body . '</textarea>';
echo "<br><div id=\"upload_files\"><input type=\"file\" name=\"file[]\"></div>\r\n";
echo '<input type="button" class="button-secondary" name="addmore" value="' . esc_attr( __( 'Add More Files', 'subscribe2' ) ) . "\" onClick=\"add_file_upload();\" />\r\n";
echo "<br><br>\r\n";
echo esc_html__( 'Recipients:', 'subscribe2' ) . ' ';
$this->display_subscriber_dropdown( apply_filters( 's2_subscriber_dropdown_default', 'registered' ), false );
echo '<input type="hidden" name="s2_admin" value="mail" />';
echo '<p class="submit"><input type="submit" class="button-secondary" name="preview" value="' . esc_attr( __( 'Preview', 'subscribe2' ) ) . '" />&nbsp;<input type="submit" class="button-primary" name="send" value="' . esc_attr( __( 'Send', 'subscribe2' ) ) . '" /></p>';
echo '</form></div>' . "\r\n";
echo '<div style="clear: both;"><p>&nbsp;</p></div>';
?>
<script type="text/javascript">
//<![CDATA[
function add_file_upload() {
	var div = document.getElementById( 'upload_files' );
	var field = div.getElementsByTagName( 'input' )[0];
	div.appendChild( document.createElement( 'br' ) );
	div.appendChild( field.cloneNode( false ) );
}
//]]>
</script>
<?php
require ABSPATH . 'wp-admin/admin-footer.php';
// just to be sure
die;
