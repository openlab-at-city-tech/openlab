<?php

namespace WeBWorK\Server;

/**
 * Response CRUD.
 */
class Response implements Util\SaveableAsWPPost, Util\Voteable {
	protected $p;
	protected $pf;

	protected $id;

	protected $is_new;

	protected $question_id;
	protected $question;

	protected $author_id;
	protected $content;
	protected $post_date;

	protected $client_url;

	protected $vote_count = null;

	public function __construct( $id = null ) {
		$this->p = new Util\WPPost( $this );
		$this->p->set_post_type( 'webwork_response' );

		$this->pf = new Util\ProblemFormatter();

		if ( $id ) {
			$this->set_id( $id );
			$this->populate();
		}
	}

	/**
	 * Whether the response exists in the database.
	 */
	public function exists() {
		return $this->id > 0;
	}

	public function set_id( $id ) {
		$this->id = (int) $id;
	}

	public function set_author_id( $author_id ) {
		$this->author_id = (int) $author_id;
	}

	public function set_content( $content ) {
		$this->content = $content;
	}

	public function set_post_date( $date ) {
		$this->post_date = $date;
	}

	public function set_question_id( $question_id ) {
		if ( $question_id ) {
			$this->question_id = (int) $question_id;
			$this->question    = new Question( $question_id );
		}
	}

	public function set_vote_count( $vote_count ) {
		$this->vote_count = (int) $vote_count;
	}

	public function set_is_new( $is_new ) {
		$this->is_new = (bool) $is_new;
	}

	public function set_client_url( $client_url ) {
		$this->client_url = $client_url;
	}

	public function set_client_name( $client_name ) {
		$this->client_name = $client_name;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_author_id() {
		return $this->author_id;
	}

	public function get_content( $format = 'mathjax' ) {
		$content = $this->content;
		if ( 'mathjax' === $format ) {
			$content = $this->pf->replace_latex_escape_characters( $content );
		}

		$content = $this->pf->strip_illegal_markup( $content );
		//$content = $this->pf->convert_linebreaks( $content );

		return $content;
	}

	public function get_post_date() {
		return $this->post_date;
	}

	public function get_question() {
		return $this->question;
	}

	public function get_question_id() {
		return $this->question_id;
	}

	public function get_author_avatar() {
		return $this->p->get_author_avatar();
	}

	public function get_author_name() {
		return $this->p->get_author_name();
	}

	public function get_author_type_label() {
		return apply_filters( 'webwork_author_type_label', '', $this->get_author_id() );
	}

	public function get_is_new() {
		return $this->is_new;
	}

	public function get_client_url() {
		return $this->client_url;
	}

	public function get_client_name() {
		return $this->client_name;
	}

	/**
	 * Get vote count.
	 *
	 * @param $force_query Whether to skip the metadata cache.
	 * @return int
	 */
	public function get_vote_count( $force_query = false ) {
		return $this->p->get_vote_count( $force_query );
	}

	/**
	 * Get attachment IDs for this item.
	 *
	 * @return array
	 */
	public function get_attachment_ids() {
		return $this->pf->get_attachment_ids( $this->get_content() );
	}

	public function save() {
		$is_new = ! $this->exists();

		$saved = $this->p->save();

		if ( $saved ) {
			update_post_meta( $this->get_id(), 'webwork_question_id', $this->get_question_id() );

			$this->get_vote_count();

			// Bust question caches. (Won't work when mocked in tests.)
			if ( $this->question instanceof Question ) {
				$this->question->get_response_count( true );
			}

			$this->populate();
		}

		if ( $is_new ) {
			$this->send_notifications( 'new' );
		}

		return (bool) $saved;
	}

	public function delete() {
		$deleted = $this->p->delete();

		if ( $deleted ) {
			// Bust question response count cache.
			if ( $this->question instanceof Question ) {
				$this->question->get_response_count( true );
			}
		}

		return $deleted;
	}

	protected function populate( $post = null ) {
		if ( $this->p->populate() ) {
			$question_id = get_post_meta( $this->get_id(), 'webwork_question_id', true );
			$this->set_question_id( $question_id );
		}
	}

	/**
	 * @todo Abstract when there are more notification types to send.
	 */
	protected function send_notifications( $type ) {
		switch ( $type ) {
			case 'new':
				$this->send_notification_to_subscribers();
				$this->send_notification_to_instructor();
				break;
		}
	}

	/**
	 * Send an email notification to question subscribers.
	 */
	protected function send_notification_to_subscribers() {
		$subscribers = $this->question->get_subscribers();

		$response_author_id = $this->get_author_id();
		$response_author    = new \WP_User( $response_author_id );
		if ( ! $response_author->exists() ) {
			return;
		}

		foreach ( $subscribers as $subscriber_id ) {
			$subscriber = new \WP_User( $subscriber_id );

			$email = new Util\Email();
			$email->set_client_name( $this->get_client_name() );
			$email->set_recipient( $subscriber->user_email );
			$email->set_subject( sprintf( __( '%1$s has replied to a question', 'webworkqa' ), $response_author->display_name ) );

			$message = sprintf(
				__(
					'%1$s has replied to a question you are subscribed to on %2$s.

To read and reply, visit %3$s.',
					'webworkqa'
				),
				$response_author->display_name,
				$this->get_client_name(),
				$this->question->get_url( $this->get_client_url() )
			);
			$email->set_message( $message );

			$email->send();
		}
	}

	/**
	 * Send an email notification to the course instructor.
	 */
	protected function send_notification_to_instructor() {
		$instructor_emails = $this->question->get_instructor_emails();
		$course            = $this->question->get_course();

		$response_author_id = $this->get_author_id();
		$response_author    = new \WP_User( $response_author_id );
		if ( ! $response_author->exists() ) {
			return;
		}

		foreach ( $instructor_emails as $instructor_email ) {
			// Don't send instructors an email about their own replies.
			if ( $response_author->user_email === $instructor_email ) {
				return;
			}

			$email = new Util\Email();
			$email->set_client_name( $this->get_client_name() );
			$email->set_recipient( $instructor_email );
			$email->set_subject( sprintf( __( '%1$s has replied to a question in the course %2$s', 'webworkqa' ), $response_author->display_name, $course ) );

			$link_url = wp_login_url( $this->question->get_url( $this->get_client_url() ) );

			$message = sprintf(
				__(
					'%1$s has replied to a question in your course %2$s.

To read and reply, visit %3$s.',
					'webworkqa'
				),
				$response_author->display_name,
				$course,
				$link_url
			);
			$email->set_message( $message );

			$email->send();
		}
	}
}
