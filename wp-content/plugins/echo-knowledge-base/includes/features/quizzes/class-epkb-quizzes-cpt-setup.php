<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Quiz custom post type and meta.
 */
class EPKB_Quizzes_CPT_Setup {

	const QUIZ_POST_TYPE = 'echo_kb_quiz';

	public function __construct() {
		add_action( 'init', array( $this, 'register_quiz_post_type' ), 10 );
	}

	/**
	 * Register hidden quiz post type and its meta schema.
	 */
	public function register_quiz_post_type() {

		if ( ! EPKB_Quizzes_Utilities::is_feature_enabled() ) {
			return;
		}

		$labels = array(
			'name'               => esc_html__( 'Knowledge Base Quizzes', 'echo-knowledge-base' ),
			'singular_name'      => esc_html__( 'Knowledge Base Quiz', 'echo-knowledge-base' ),
			'menu_name'          => esc_html__( 'Quizzes', 'echo-knowledge-base' ),
			'name_admin_bar'     => esc_html__( 'Quiz', 'echo-knowledge-base' ),
			'add_new'            => esc_html__( 'Add New', 'echo-knowledge-base' ),
			'add_new_item'       => esc_html__( 'Add New Quiz', 'echo-knowledge-base' ),
			'new_item'           => esc_html__( 'New Quiz', 'echo-knowledge-base' ),
			'edit_item'          => esc_html__( 'Edit Quiz', 'echo-knowledge-base' ),
			'all_items'          => esc_html__( 'All Quizzes', 'echo-knowledge-base' ),
			'search_items'       => esc_html__( 'Search Quizzes', 'echo-knowledge-base' ),
			'not_found'          => esc_html__( 'No quizzes found', 'echo-knowledge-base' ),
			'not_found_in_trash' => esc_html__( 'No quizzes found in Trash', 'echo-knowledge-base' ),
		);

		register_post_type( self::QUIZ_POST_TYPE, array(
			'labels'              => $labels,
			'description'         => esc_html__( 'Learner quizzes linked to KB articles.', 'echo-knowledge-base' ),
			'public'              => false,
			'exclude_from_search' => true,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'publicly_queryable'  => false,
			'query_var'           => false,
			'rewrite'             => false,
			'has_archive'         => false,
			'hierarchical'        => false,
			'show_in_rest'        => false,
			'supports'            => array( 'title', 'editor', 'custom-fields' ),
		) );

		register_post_meta( self::QUIZ_POST_TYPE, EPKB_Quizzes_Utilities::META_SOURCE_ARTICLE_ID, array(
			'type'              => 'integer',
			'single'            => true,
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'show_in_rest'      => true,
		) );

		register_post_meta( self::QUIZ_POST_TYPE, EPKB_Quizzes_Utilities::META_QUESTIONS, array(
			'type'         => 'array',
			'single'       => true,
			'default'      => array(),
			'show_in_rest' => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'id'             => array( 'type' => 'string' ),
							'type'           => array( 'type' => 'string' ),
							'question'       => array( 'type' => 'string' ),
							'choices'        => array(
								'type'  => 'array',
								'items' => array( 'type' => 'string' ),
							),
							'correct_choice' => array( 'type' => 'integer' ),
							'explanation'    => array( 'type' => 'string' ),
						),
					),
				),
			),
		) );

		register_post_meta( self::QUIZ_POST_TYPE, EPKB_Quizzes_Utilities::META_GENERATION_META, array(
			'type'         => 'object',
			'single'       => true,
			'default'      => array(),
			'show_in_rest' => array(
				'schema' => array(
					'type'       => 'object',
					'properties' => array(
						'source_checksum'    => array( 'type' => 'string' ),
						'generated_at'       => array( 'type' => 'string' ),
						'question_count'     => array( 'type' => 'integer' ),
						'question_count_mode'=> array( 'type' => 'string' ),
					),
				),
			),
		) );
	}
}
