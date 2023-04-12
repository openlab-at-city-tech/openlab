<?php

namespace WeBWorK\Server\Response;

/**
 * Response query.
 *
 * @since 1.0.0
 */
class Query {
	protected $r;
	protected $results = null;

	public function __construct( $args ) {
		$this->r = array_merge(
			array(
				'question_id__in' => null,
				'orderby'         => 'votes',
				'response_id__in' => null,
				'is_answer'       => null,
			),
			$args
		);
	}

	/**
	 * Get responses.
	 *
	 * @since 1.0.0
	 *
	 * @return array|int
	 */
	public function get() {
		if ( isset( $this->results ) && is_array( $this->results ) ) {
			return $this->results;
		}

		$args = array(
			'post_type'              => 'webwork_response',
			'update_post_term_cache' => false,
			'meta_query'             => array(),
			'posts_per_page'         => -1,
			'orderby'                => 'post_date',
			'order'                  => 'ASC',
		);

		if ( null !== $this->r['question_id__in'] ) {
			if ( array() === $this->r['question_id__in'] ) {
				$question_id__in = array( 0 );
			} else {
				$question_id__in = array_map( 'intval', $this->r['question_id__in'] );
			}

			$args['meta_query']['question_id__in'] = array(
				'key'     => 'webwork_question_id',
				'value'   => $question_id__in,
				'compare' => 'IN',
			);
		}

		if ( null !== $this->r['is_answer'] ) {
			if ( $this->r['is_answer'] ) {
				$args['meta_query']['is_answer'] = array(
					'key'   => 'webwork_question_answer',
					'value' => '1',
				);
			} else {
				// SOS
				$is_answer_args              = $this->r;
				$is_answer_args['is_answer'] = true;
				$is_answer_args['orderby']   = 'post_date';
				$is_answer_query             = new Query( $is_answer_args );
				$is_answers                  = $is_answer_query->get();

				$not_in = array();
				foreach ( $is_answers as $is_answer ) {
					$not_in[] = $is_answer->get_id();
				}

				$args['post__not_in'] = $not_in;
			}
		}

		if ( 'votes' === $this->r['orderby'] ) {
			$args['meta_query']['votes_orderby'] = array(
				'key'     => 'webwork_vote_count',
				'compare' => 'EXISTS',
				'type'    => 'SIGNED',
			);

			$args['orderby'] = array(
				'votes_orderby' => 'DESC',
				'post_date'     => 'ASC',
			);
		}

		if ( null !== $this->r['response_id__in'] ) {
			$args['post__in'] = wp_parse_id_list( $this->r['response_id__in'] );
		}

		$response_query = new \WP_Query( $args );
		$_responses     = $response_query->posts;

		$responses = array();
		foreach ( $_responses as $_response ) {
			$responses[ $_response->ID ] = new \WeBWorK\Server\Response( $_response->ID );
		}

		$this->results = $responses;
		return $this->results;
	}

	public function get_for_endpoint() {
		$responses = $this->get();

		$formatted = array();
		foreach ( $responses as $r ) {
			$question = $r->get_question();

			// TODO: This is an insufficent amount of info to hide on the front-end
			if ( $question && $question->get_is_anonymous() && $r->get_author_id() === $question->get_author_id() ) {
				$author_name      = webwork_user_is_admin() ? $r->get_author_name() : '';
				$author_avatar    = get_avatar_url( 0, array( 'size' => 80 ) );
				$author_id        = 0;
				$obfuscate_author = true;
			} else {
				$author_name      = $r->get_author_name();
				$author_avatar    = $r->get_author_avatar();
				$author_id        = $r->get_author_id();
				$obfuscate_author = false;
			}

			$response_id               = $r->get_id();
			$formatted[ $response_id ] = array(
				'authorAvatar'    => $author_avatar,
				'authorId'        => $author_id,
				'authorName'      => $author_name,
				'authorUserType'  => $r->get_author_type_label(),
				'content'         => $r->get_content(),
				'postDate'        => $r->get_post_date(),
				'questionId'      => $r->get_question_id(),
				'responseId'      => $response_id,
				'obfuscateAuthor' => $obfuscate_author,
			);
		}

		return $formatted;
	}
}
