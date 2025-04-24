<?php

namespace WeBWorK\Server;

/**
 * Question CRUD.
 */
class Question implements Util\SaveableAsWPPost, Util\Voteable {
	protected $p;
	protected $pf;

	protected $id = 0;

	protected $problem_id;
	protected $problem_set;
	protected $course;
	protected $problem_text;
	protected $client_url;
	protected $remote_class_url;
	protected $remote_problem_url;
	protected $emailable_url;
	protected $random_seed;
	protected $notify_addresses;
	protected $student_name;
	protected $remote_user_problem_url;

	protected $author_id;
	protected $content;
	protected $tried;
	protected $is_anonymous;
	protected $post_date;

	public function __construct( $id = null ) {
		$this->p = new Util\WPPost( $this );
		$this->p->set_post_type( 'webwork_question' );

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

	public function set_tried( $tried ) {
		$this->tried = $tried;
	}

	public function set_is_anonymous( $is_anonymous ) {
		$this->is_anonymous = (bool) $is_anonymous;
	}

	public function set_post_date( $date ) {
		$this->post_date = $date;
	}

	public function set_problem_id( $problem_id ) {
		$this->problem_id = $problem_id;
	}

	public function set_problem_set( $problem_set ) {
		$this->problem_set = $problem_set;
	}

	public function set_course( $course ) {
		$this->course = $course;
	}

	public function set_problem_text( $problem_text ) {
		$this->problem_text = $problem_text;
	}

	public function set_vote_count( $vote_count ) {
		$this->vote_count = (int) $vote_count;
	}

	public function set_client_url( $client_url ) {
		$this->client_url = $client_url;
	}

	public function set_client_name( $client_name ) {
		$this->client_name = $client_name;
	}

	public function set_emailable_url( $emailable_url ) {
		$this->emailable_url = $emailable_url;
	}

	public function set_random_seed( $random_seed ) {
		$this->random_seed = $random_seed;
	}

	public function set_notify_addresses( $notify_addresses ) {
		$this->notify_addresses = $notify_addresses;
	}

	public function set_student_name( $student_name ) {
		$this->student_name = $student_name;
	}

	public function set_remote_class_url( $remote_class_url ) {
		$this->remote_class_url = $remote_class_url;
	}

	public function set_remote_problem_url( $remote_problem_url ) {
		$this->remote_problem_url = $remote_problem_url;
	}

	public function set_remote_user_problem_url( $remote_user_problem_url ) {
		$this->remote_user_problem_url = $remote_user_problem_url;
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

	public function get_tried( $format = 'mathjax' ) {
		$tried = $this->tried;
		if ( 'mathjax' === $format ) {
			$tried = $this->pf->replace_latex_escape_characters( $tried );
		}

		$tried = $this->pf->strip_illegal_markup( $tried );
		//$content = $this->pf->convert_linebreaks( $content );

		return $tried;
	}

	public function get_is_anonymous() {
		return (bool) $this->is_anonymous;
	}

	public function get_problem_text( $format = 'mathjax' ) {
		$problem_text = $this->problem_text;
		if ( 'mathjax' === $format ) {
			$problem_text = $this->pf->replace_latex_escape_characters( $problem_text );
		}

		$problem_text = $this->pf->strip_illegal_markup( $problem_text, 'extended' );
		$problem_text = $this->pf->remove_empty_divs( $problem_text );

		return $problem_text;
	}

	public function get_post_date() {
		return $this->post_date;
	}

	public function get_problem_id() {
		return (string) $this->problem_id;
	}

	public function get_problem_set() {
		return (string) $this->problem_set;
	}

	public function get_course() {
		return (string) $this->course;
	}

	public function get_author_avatar() {
		return $this->p->get_author_avatar();
	}

	public function get_author_name() {
		return $this->p->get_author_name();
	}

	public function get_client_url() {
		return $this->client_url;
	}

	public function get_client_name() {
		return $this->client_name;
	}

	public function get_emailable_url() {
		return $this->emailable_url;
	}

	public function get_random_seed() {
		return $this->random_seed;
	}

	public function get_notify_addresses() {
		if ( ! is_array( $this->notify_addresses ) ) {
			$nas = explode( ';', $this->notify_addresses );
			$nas = array_map( 'trim', $nas );

			$this->notify_addresses = $nas;
		}

		return $this->notify_addresses;
	}

	public function get_student_name() {
		return $this->student_name;
	}

	public function get_remote_class_url() {
		return $this->remote_class_url;
	}

	public function get_remote_problem_url() {
		return $this->remote_problem_url;
	}

	public function get_remote_user_problem_url() {
		return $this->remote_user_problem_url;
	}

	/**
	 * @todo Do something better than this.
	 */
	public function get_url( $client_url ) {
		return trailingslashit( $client_url ) . '#:problemId=' . $this->get_problem_id() . ':questionId=' . $this->get_id();
	}

	/**
	 * Get whether the question has an answer.
	 *
	 * @since 1.0.0
	 *
	 * @param $force_query Whether to skip metadata cache. Default false.
	 * @return bool
	 */
	public function get_has_answer( $force_query = false ) {
		$question_id = $this->get_id();
		$has_answer  = get_post_meta( $question_id, 'webwork_has_answer', true );
		if ( $force_query || '' === $has_answer ) {
			$response_query = new Response\Query(
				array(
					'question_id__in' => array( $question_id ),
					'is_answer'       => true,
				)
			);
			$responses      = $response_query->get();

			$has_answer = ! empty( $responses );
			update_post_meta( $question_id, 'webwork_has_answer', (int) $has_answer );
		}

		return (bool) $has_answer;
	}

	/**
	 * Get response count.
	 *
	 * @since 1.0.0
	 *
	 * @param $force_query Whether to skip metadata cache. Default false.
	 * @return int
	 */
	public function get_response_count( $force_query = false ) {
		$question_id = $this->get_id();
		$count       = get_post_meta( $question_id, 'webwork_response_count', true );
		if ( $force_query || '' === $count ) {
			$response_query = new Response\Query(
				array(
					'question_id__in' => array( $question_id ),
				)
			);
			$responses      = $response_query->get();

			$count = count( $responses );
			update_post_meta( $question_id, 'webwork_response_count', $count );
		}

		return intval( $count );
	}

	/**
	 * Get vote count.
	 *
	 * @param int $force_query Whether to skip the metadata cache.
	 * @return int
	 */
	public function get_vote_count( $force_query = false ) {
		return $this->p->get_vote_count( $force_query );
	}

	/**
	 * Get instructor emails.
	 *
	 * The primary source for email addresses is the 'notifyAddresses' property sent
	 * from WeBWorK. The fallback is a course-instructor map that must be provided via
	 * filter. The two are mutually exclusive, to ensure that no duplicate emails are sent.
	 *
	 * @return array
	 */
	public function get_instructor_emails() {
		$emails = $this->get_notify_addresses();

		if ( ! $emails ) {
			$course = $this->get_course();

			$instructor_map = apply_filters( 'webwork_section_instructor_map', array() );
			if ( isset( $instructor_map[ $course ] ) ) {
				$emails = array( $instructor_map[ $course ] );
			}
		}

		return $emails;
	}

	/**
	 * Get attachment IDs for this item.
	 *
	 * @return array
	 */
	public function get_attachment_ids() {
		$content_ids = $this->pf->get_attachment_ids( $this->get_content() );
		$tried_ids   = $this->pf->get_attachment_ids( $this->get_tried() );
		return array_unique( array_merge( $content_ids, $tried_ids ) );
	}

	public function save() {
		$is_new = ! $this->exists();

		$saved = $this->p->save();

		if ( $saved ) {
			$this->set_id( $saved );

			$post_id = $this->get_id();

			wp_set_object_terms( $post_id, array( $this->get_problem_id() ), 'webwork_problem_id' );
			wp_set_object_terms( $post_id, array( $this->get_problem_set() ), 'webwork_problem_set' );
			wp_set_object_terms( $post_id, array( $this->get_course() ), 'webwork_course' );

			$tried = $this->get_tried();
			$tried = $this->pf->convert_delims( $tried );
			$tried = $this->pf->swap_latex_escape_characters( $tried );
			update_post_meta( $this->get_id(), 'webwork_tried', $tried );

			$problem_text = $this->get_problem_text();
			$problem_text = $this->pf->convert_delims( $problem_text );
			$problem_text = $this->pf->swap_latex_escape_characters( $problem_text );
			update_post_meta( $this->get_id(), 'webwork_problem_text', $problem_text );

			update_post_meta( $this->get_id(), 'webwork_remote_class_url', $this->get_remote_class_url() );
			update_post_meta( $this->get_id(), 'webwork_remote_problem_url', $this->get_remote_problem_url() );
			update_post_meta( $this->get_id(), 'webwork_remote_user_problem_url', $this->get_remote_user_problem_url() );

			update_post_meta( $this->get_id(), 'webwork_is_anonymous', (int) $this->get_is_anonymous() );

			update_post_meta( $this->get_id(), 'webwork_emailable_url', $this->get_emailable_url() );
			update_post_meta( $this->get_id(), 'webwork_random_seed', $this->get_random_seed() );
			update_post_meta( $this->get_id(), 'webwork_notify_addresses', $this->get_notify_addresses() );
			update_post_meta( $this->get_id(), 'webwork_student_name', $this->get_student_name() );

			// Refresh vote count.
			$this->get_vote_count( true );

			if ( $is_new ) {
				$this->set_subscription( $this->get_author_id(), true );
			}

			$this->populate();
		}

		if ( $is_new ) {
			$this->send_notifications();
		}

		return (bool) $saved;
	}

	public function delete() {
		return $this->p->delete();
	}

	protected function populate() {
		if ( $this->p->populate() ) {
			$post_id = $this->get_id();

			$problem_sets = get_the_terms( $post_id, 'webwork_problem_set' );
			if ( $problem_sets ) {
				$problem_set = reset( $problem_sets )->name;
			} else {
				$problem_set = '';
			}
			$this->set_problem_set( $problem_set );

			$problem_ids = get_the_terms( $post_id, 'webwork_problem_id' );
			if ( $problem_ids ) {
				$problem_id = reset( $problem_ids )->name;
			} else {
				$problem_id = '';
			}
			$this->set_problem_id( $problem_id );

			$courses = get_the_terms( $post_id, 'webwork_course' );
			if ( $courses ) {
				$course = reset( $courses )->name;
			} else {
				$course = '';
			}
			$this->set_course( $course );

			$tried = get_post_meta( $this->get_id(), 'webwork_tried', true );
			$this->set_tried( $tried );

			$problem_text = get_post_meta( $this->get_id(), 'webwork_problem_text', true );
			$this->set_problem_text( $problem_text );

			$is_anonymous = get_post_meta( $this->get_id(), 'webwork_is_anonymous', true );
			$this->set_is_anonymous( $is_anonymous );

			$remote_class_url = get_post_meta( $this->get_id(), 'webwork_remote_class_url', true );
			$this->set_remote_class_url( $remote_class_url );

			$remote_problem_url = get_post_meta( $this->get_id(), 'webwork_remote_problem_url', true );
			$this->set_remote_problem_url( $remote_problem_url );

			$remote_user_problem_url = get_post_meta( $this->get_id(), 'webwork_remote_user_problem_url', true );
			$this->set_remote_user_problem_url( $remote_user_problem_url );

			$emailable_url = get_post_meta( $this->get_id(), 'webwork_emailable_url', true );
			$this->set_emailable_url( $emailable_url );

			$random_seed = get_post_meta( $this->get_id(), 'webwork_random_seed', true );
			$this->set_random_seed( $random_seed );

			$notify_addresses = get_post_meta( $this->get_id(), 'webwork_notify_addresses', true );
			$this->set_notify_addresses( $notify_addresses );

			$student_name = get_post_meta( $this->get_id(), 'webwork_student_name', true );
			$this->set_student_name( $student_name );
		}
	}

	/**
	 * Send notifications.
	 */
	protected function send_notifications() {
		$this->send_notification_to_instructor();
	}

	/**
	 * Send an email notification to the course instructor.
	 */
	protected function send_notification_to_instructor() {
		$instructor_emails = $this->get_instructor_emails();
		$course            = $this->get_course();

		$question_author_id = $this->get_author_id();
		$question_author    = new \WP_User( $question_author_id );
		if ( ! $question_author->exists() ) {
			return;
		}

		foreach ( $instructor_emails as $instructor_email ) {
			// Don't send instructors an email about their own questions.
			if ( $question_author->user_email === $instructor_email ) {
				return;
			}

			$email = new Util\Email();
			$email->set_client_name( $this->get_client_name() );
			$email->set_recipient( $instructor_email );
			$email->set_subject( sprintf( __( '%1$s has posted a question in the course %2$s', 'webworkqa' ), $question_author->display_name, $course ) );

			$link_url = wp_login_url( $this->get_url( $this->get_client_url() ) );

			$remote_text = '';
			$remote_url  = $this->get_remote_user_problem_url();

			if ( $remote_url ) {
				$message = sprintf(
					__(
						'%1$s has posted a question in your course %2$s.

To read and reply to the question, visit %3$s.

To view the student\'s work on this question in WeBWorK, visit %4$s.',
						'webworkqa'
					),
					$question_author->display_name,
					$course,
					$link_url,
					$remote_url
				);
			} else {
				$message = sprintf(
					__(
						'%1$s has posted a question in your course %2$s.

To read and reply, visit %3$s.',
						'webworkqa'
					),
					$question_author->display_name,
					$course,
					$link_url
				);
			}
			$email->set_message( $message );

			$email->send();
		}
	}

	public function set_subscription( $user_id, $status ) {
		$tax_slug = 'user_' . $user_id;
		$item_id  = $this->get_id();

		$subscribed = wp_get_object_terms( $item_id, 'webwork_subscribed_by' );

		$is_subscribed = false;
		foreach ( $subscribed as $sub ) {
			if ( $tax_slug === $sub->slug ) {
				$is_subscribed = true;
				break;
			}
		}

		if ( ( $status && $is_subscribed ) || ( ! $status && ! $is_subscribed ) ) {
			return false;
		}

		$term = get_term_by( 'slug', $tax_slug, 'webwork_subscribed_by' );
		if ( ! $term || is_wp_error( $term ) ) {
			$created = wp_insert_term(
				$tax_slug,
				'webwork_subscribed_by',
				array(
					'slug' => $tax_slug,
				)
			);

			if ( is_wp_error( $term ) ) {
				return false;
			}

			$term = get_term( $created['term_id'], 'webwork_subscribed_by' );
		}

		if ( $status ) {
			$retval = wp_set_object_terms( $item_id, array( $term->term_id ), 'webwork_subscribed_by', true );
		} else {
			$retval = wp_remove_object_terms( $item_id, array( $term->term_id ), 'webwork_subscribed_by' );
		}

		return ! is_wp_error( $retval );
	}

	public function get_subscribers() {
		$terms = wp_get_object_terms( $this->get_id(), 'webwork_subscribed_by' );

		$user_ids = array();
		foreach ( $terms as $term ) {
			$user_ids[] = intval( substr( $term->slug, 5 ) );
		}

		return $user_ids;
	}

	public function has_external_assets() {
		$d = new \DOMDocument();
		libxml_use_internal_errors( true );
		$d->loadHTML( $this->get_problem_text() );
		libxml_clear_errors();

		$imgs                = $d->getElementsByTagName( 'img' );
		$has_external_assets = false;
		foreach ( $imgs as $img ) {
			$src = $img->getAttribute( 'src' );
			if ( ! $src ) {
				continue;
			}

			$src_domain  = parse_url( $src, PHP_URL_HOST );
			$home_domain = parse_url( home_url(), PHP_URL_HOST );
			if ( $src_domain !== $home_domain ) {
				$has_external_assets = true;
				break;
			}
		}

		return $has_external_assets;
	}

	/**
	 * Parse question content for external items (like images), pull them to WP, and change internal refs.
	 */
	public function fetch_external_assets() {
		$this->set_problem_text( $this->fetch_external_assets_for_text( $this->get_problem_text() ) );
	}

	public function fetch_external_assets_for_text( $text ) {
		if ( empty( $text ) ) {
			return $text;
		}

		$d = new \DOMDocument();
		$d->loadHTML( $text );

		$imgs    = $d->getElementsByTagName( 'img' );
		$fetched = array();
		foreach ( $imgs as $img ) {
			$src = $img->getAttribute( 'src' );
			if ( ! $src ) {
				continue;
			}

			$src_domain  = parse_url( $src, PHP_URL_HOST );
			$home_domain = parse_url( home_url(), PHP_URL_HOST );
			if ( $src_domain === $home_domain ) {
				continue;
			}

			// Only fetch if we haven't already done it.
			if ( ! isset( $fetched[ $src ] ) ) {
				if ( ! function_exists( 'download_url' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}

				$tmp = download_url( $src );
				if ( is_wp_error( $tmp ) ) {
					continue;
				}

				$file_array = array(
					'tmp_name' => $tmp,
					'name'     => basename( $src ),
				);

				$overrides = array(
					'test_form' => false,
					'test_size' => false,
				);

				$sideload = wp_handle_sideload( $file_array, $overrides );

				if ( ! is_wp_error( $sideload ) && ! empty( $sideload['url'] ) ) {
					$fetched[ $src ] = $sideload['url'];
				}
			}

			if ( ! isset( $fetched[ $src ] ) ) {
				continue;
			}

			$new_url = $fetched[ $src ];
			$img->setAttribute( 'src', $new_url );

			// If the parent node is a link pointing to the image URL, change that too.
			if ( ! empty( $img->parentNode->tagName ) ) {
				if ( 'a' !== $img->parentNode->tagName ) {
					continue;
				}

				$href = $img->parentNode->getAttribute( 'href' );
				if ( $href !== $src ) {
					continue;
				}

				$img->parentNode->setAttribute( 'href', $new_url );
			}
		}

		return $d->saveHTML();
	}
}
