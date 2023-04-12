<?php

namespace WeBWorK\Server\Util;

/**
 * Email.
 *
 * Wraps wp_mail().
 *
 * @since 1.0.0
 */
class Email {
	protected $recipient;
	protected $subject;
	protected $message;
	protected $client_name;

	public function set_recipient( $recipient ) {
		$this->recipient = $recipient;
	}

	public function set_subject( $subject ) {
		$this->subject = $subject;
	}

	public function set_message( $message ) {
		$this->message = $message;
	}

	public function set_client_name( $client_name ) {
		$this->client_name = $client_name;
	}

	public function send() {
		$client_name = get_option( 'blogname' );
		if ( ! empty( $this->client_name ) ) {
			$client_name = $this->client_name;
		}

		$subject = sprintf( '[%s] %s', $client_name, $this->subject );

		$sent = wp_mail( $this->recipient, $subject, $this->message );
		return $sent;
	}
}
