<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Demo KB data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Demo_Data {

	public function __construct() {
		add_filter( 'eckb_analytics_get_search_demo_data', array( $this, 'provide_search_demo_data' ), 10, 2 );
		add_filter( 'eckb_analytics_get_rating_demo_data', array( $this, 'provide_rating_demo_data' ), 10, 2 );
	}

	/**
	 * Provide demo search data to Advanced Search add-on via filter
	 *
	 * @param null $data
	 * @param int $kb_id
	 * @return array|null
	 */
	public function provide_search_demo_data( $data, $kb_id ) {

		// Only provide demo data if this KB is using demo data
		if ( ! self::is_demo_data( $kb_id ) ) {
			return $data;
		}

		$demo_popular = self::get_demo_popular_searches();
		$demo_no_results = self::get_demo_no_results_searches();
		$demo_stats = self::get_demo_search_statistics();

		$most_popular_searches = array();
		foreach ( $demo_popular as $search ) {
			$most_popular_searches[] = array( esc_html( $search['term'] ), $search['times'] );
		}

		$no_results_searches = array();
		foreach ( $demo_no_results as $search ) {
			$no_results_searches[] = array( esc_html( $search['term'] ), $search['times'] );
		}

		return array(
			'most_popular_searches' => $most_popular_searches,
			'no_results_searches' => $no_results_searches,
			'stats_data' => $demo_stats,
		);
	}

	/**
	 * Provide demo rating data to Article Rating add-on via filter
	 *
	 * @param null $data
	 * @param int $kb_id
	 * @return array|null
	 */
	public function provide_rating_demo_data( $data, $kb_id ) {

		// Only provide demo data if this KB is using demo data
		if ( ! self::is_demo_data( $kb_id ) ) {
			return $data;
		}

		$demo_best_rated = self::get_demo_best_rated_articles();
		$demo_most_rated = self::get_demo_most_rated_articles();
		$demo_worst_rated = self::get_demo_worst_rated_articles();
		$demo_least_rated = self::get_demo_least_rated_articles();

		$best_rated_articles_data = array();
		foreach ( $demo_best_rated as $article ) {
			$best_rated_articles_data[] = array( esc_html( $article['title'] ), $article['avg_rating'] );
		}

		$most_rated_articles_data = array();
		foreach ( $demo_most_rated as $article ) {
			$most_rated_articles_data[] = array( esc_html( $article['title'] ), $article['times'] );
		}

		$worst_rated_articles_data = array();
		foreach ( $demo_worst_rated as $article ) {
			$worst_rated_articles_data[] = array( esc_html( $article['title'] ), $article['avg_rating'] );
		}

		$least_rated_articles_data = array();
		foreach ( $demo_least_rated as $article ) {
			$least_rated_articles_data[] = array( esc_html( $article['title'] ), $article['times'] );
		}

		return array(
			'best_rated_articles_data' => $best_rated_articles_data,
			'most_rated_articles_data' => $most_rated_articles_data,
			'worst_rated_articles_data' => $worst_rated_articles_data,
			'least_rated_articles_data' => $least_rated_articles_data,
			'number_of_rated_articles' => 1547,
		);
	}

	public static function create_sample_categories_and_articles( $new_kb_id, $kb_main_page_layout ) {

		$articles_seq_meta = [];
		$categories_seq_meta = [];

		$tab_category_id = null;
		if ( $kb_main_page_layout == EPKB_Layout::TABS_LAYOUT ) {

			$tab_category_name_1 = self::get_tab_top_categories()[0];
			$tab_category_name_2 = self::get_tab_top_categories()[1];
			$tab_category_name_3 = self::get_tab_top_categories()[2];

			$tab_category_id = self::create_sample_category( $new_kb_id, $tab_category_name_1 );
			if ( empty( $tab_category_id ) ) {
				return;
			}
			$tab_category_id_2 = self::create_sample_category( $new_kb_id, $tab_category_name_2 );
			if ( empty( $tab_category_id_2 ) ) {
				return;
			}
			$tab_category_id_3 = self::create_sample_category( $new_kb_id, $tab_category_name_3 );
			if ( empty( $tab_category_id_3 ) ) {
				return;
			}
			$categories_seq_meta[$tab_category_id] = [];
			$categories_seq_meta[$tab_category_id_2] = [];
			$categories_seq_meta[$tab_category_id_3] = [];
			$articles_seq_meta[$tab_category_id] = [ '0' => $tab_category_name_1, '1' => self::get_category_description( $tab_category_name_1 ) ];
			$articles_seq_meta[$tab_category_id_2] = [ '0' => $tab_category_name_2, '1' => self::get_category_description( $tab_category_name_2 )];
			$articles_seq_meta[$tab_category_id_3] = [ '0' => $tab_category_name_3, '1' => self::get_category_description( $tab_category_name_3 )];
		}

		$category_name = self::get_non_tab_top_categories()[0];
		$article_titles = [
			esc_html__('Introduction to Our Sales Process', 'echo-knowledge-base' ),
			esc_html__('Creating Effective Marketing Campaigns', 'echo-knowledge-base' ),
			esc_html__('Using the CRM Software', 'echo-knowledge-base' ),
		//	esc_html__('Brand Guidelines and Usage', 'echo-knowledge-base' ),
		];
		$category_id_1 = self::create_category_and_articles( $new_kb_id, $category_name, $tab_category_id, $article_titles, $articles_seq_meta, $categories_seq_meta );
		if ( empty( $category_id_1 ) ) {
			return;
		}

		$category_name = self::get_non_tab_top_categories()[1];
		$article_titles = [
			esc_html__('Inventory Management Best Practices', 'echo-knowledge-base' ),
			esc_html__('Understanding the Supply Chain', 'echo-knowledge-base' ),
		//	esc_html__('Brand Guidelines and Usage', 'echo-knowledge-base' ),
		];
		$category_id_2 = self::create_category_and_articles( $new_kb_id, $category_name, $tab_category_id, $article_titles, $articles_seq_meta, $categories_seq_meta );
		if ( empty( $category_id_2 ) ) {
			return;
		}
		// sub-category
		$category_name = esc_html__( 'Safety Protocols', 'echo-knowledge-base' );
		$article_titles = [
			esc_html__('Safety Protocols in the Workplace', 'echo-knowledge-base' ),
		//	esc_html__('Basic Safety checks', 'echo-knowledge-base' ),
		];
		if ( empty( $tab_category_id ) ) {
			$sub_category_id = self::create_category_and_articles( $new_kb_id, $category_name, $category_id_2, $article_titles, $articles_seq_meta, $categories_seq_meta );
		} else {
			$sub_category_id = self::create_category_and_articles( $new_kb_id, $category_name, $category_id_2, $article_titles, $articles_seq_meta, $categories_seq_meta[$tab_category_id] );
		}
		if ( empty( $sub_category_id ) ) {
			return;
		}

		$category_name = self::get_non_tab_top_categories()[2];
		$article_titles = [
			esc_html__('Onboarding Checklist for New Hires', 'echo-knowledge-base' ),
			esc_html__('Understanding Your Benefits Package', 'echo-knowledge-base' ),
		//	esc_html__('Leave Policies and How to Apply', 'echo-knowledge-base' ),
		];
		$category_id_3 = self::create_category_and_articles( $new_kb_id, $category_name, $tab_category_id, $article_titles, $articles_seq_meta, $categories_seq_meta );
		if ( empty( $category_id_3 ) ) {
			return;
		}

		// sub-category
		$category_name = esc_html__( 'Performance Reviews', 'echo-knowledge-base' );
		$article_titles = [
			esc_html__('Performance Review Guidelines', 'echo-knowledge-base' ),
		//	esc_html__('Performance Review Forms and Templates', 'echo-knowledge-base' ),
		];
		if ( empty( $tab_category_id ) ) {
			$sub_category_id = self::create_category_and_articles( $new_kb_id, $category_name, $category_id_3, $article_titles, $articles_seq_meta, $categories_seq_meta );
		} else {
			$sub_category_id = self::create_category_and_articles( $new_kb_id, $category_name, $category_id_3, $article_titles, $articles_seq_meta, $categories_seq_meta[$tab_category_id] );
		}
		if ( empty( $sub_category_id ) ) {
			return;
		}

		$category_name = self::get_non_tab_top_categories()[3];
		$article_titles = [
			esc_html__('Submitting Expense Reports', 'echo-knowledge-base' ),
			esc_html__('Travel Expense Guidelines', 'echo-knowledge-base' ),
			esc_html__('Year-End Tax Information for Employees', 'echo-knowledge-base' ),
		//	esc_html__('Understanding the Company Budget Process', 'echo-knowledge-base' ),
		];
		$category_id_4 = self::create_category_and_articles( $new_kb_id, $category_name, $tab_category_id, $article_titles, $articles_seq_meta, $categories_seq_meta );
		if ( empty( $category_id_4 ) ) {
			return;
		}

		$category_name = self::get_non_tab_top_categories()[4];
		$article_titles = [
			esc_html__('Getting Started with Your Work Computer', 'echo-knowledge-base' ),
			esc_html__('How to Request IT Support', 'echo-knowledge-base' ),
		//	esc_html__('Accessing Company Software Remotely', 'echo-knowledge-base' ),
		];
		$category_id_5 = self::create_category_and_articles( $new_kb_id, $category_name, $tab_category_id, $article_titles, $articles_seq_meta, $categories_seq_meta );
		if ( empty( $category_id_5 ) ) {
			return;
		}

		// sub-category
		$category_name = esc_html__( 'Security Protocols', 'echo-knowledge-base' );
		$article_titles = [
			esc_html__('Security Protocols for Safe Computing', 'echo-knowledge-base' ),
		//	esc_html__('Password Management Best Practices', 'echo-knowledge-base' ),
		];
		if ( empty( $tab_category_id ) ) {
			$sub_category_id = self::create_category_and_articles( $new_kb_id, $category_name, $category_id_5, $article_titles, $articles_seq_meta, $categories_seq_meta );
		} else {
			$sub_category_id = self::create_category_and_articles( $new_kb_id, $category_name, $category_id_5, $article_titles, $articles_seq_meta, $categories_seq_meta[$tab_category_id] );
		}
		if ( empty( $sub_category_id ) ) {
			return;
		}

		$category_name = self::get_non_tab_top_categories()[5];
		$article_titles = [
			esc_html__('Identifying Training Opportunities', 'echo-knowledge-base' ),
			esc_html__('Mentorship Programs Overview', 'echo-knowledge-base' ),
			esc_html__('Setting Career Goals', 'echo-knowledge-base' ),
	//		esc_html__('Skills Development Resources', 'echo-knowledge-base' ),
		];
		$category_id_6 = self::create_category_and_articles( $new_kb_id, $category_name, $tab_category_id, $article_titles, $articles_seq_meta, $categories_seq_meta );
		if ( empty( $category_id_6 ) ) {
			return;
		}

		EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, $articles_seq_meta );
		EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, $categories_seq_meta );

		// save new icons
		$new_categories_icons = array();
		$new_categories_icons[$category_id_1] = array(
			'type' => 'image',
			'name' => 'epkbfa-book',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/pink-kb-icon-laptop-100.png',
			'color' => '#000000'    // FUTURE
		);
		$new_categories_icons[$category_id_2] = array(
			'type' => 'image',
			'name' => 'ep_font_icon_gears',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/pink-kb-icon-bar-chart-100.png',
			'color' => '#000000'    // FUTURE
		);
		$new_categories_icons[$category_id_3] = array(
			'type' => 'image',
			'name' => 'epkbfa-cube',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/pink-kb-icon-notepad-100.png',
			'color' => '#000000'    // FUTURE
		);
		$new_categories_icons[$category_id_4] = array(
			'type' => 'image',
			'name' => 'epkbfa-book',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/pink-kb-icon-lightbulb-100.png',
			'color' => '#000000'    // FUTURE
		);
		$new_categories_icons[$category_id_5] = array(
			'type' => 'image',
			'name' => 'ep_font_icon_gears',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/pink-kb-icon-briefcase-100.png',
			'color' => '#000000'    // FUTURE
		);
		$new_categories_icons[$category_id_6] = array(
			'type' => 'image',
			'name' => 'epkbfa-cube',
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/pink-kb-icon-handshake-100.png',
			'color' => '#000000'    // FUTURE
		);
		$result = EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_Icons::CATEGORIES_ICONS, $new_categories_icons );
		if ( is_wp_error( $result ) ) {
			return;
		}
	}

	private static function get_tab_top_categories() {
		return [ esc_html__( 'Department Resources', 'echo-knowledge-base' ),
				esc_html__( 'Employee Handbook', 'echo-knowledge-base' ),
				esc_html__( 'How-To Center', 'echo-knowledge-base' ) ];
	}

	private static function get_non_tab_top_categories() {
		return [ esc_html__( 'Sales and Marketing', 'echo-knowledge-base' ),
				esc_html__( 'Operations and Logistics', 'echo-knowledge-base' ),
				esc_html__( 'Human Resources', 'echo-knowledge-base' ),
				esc_html__( 'Finance and Expenses', 'echo-knowledge-base' ),
				esc_html__( 'IT Support', 'echo-knowledge-base' ),
				esc_html__( 'Professional Development', 'echo-knowledge-base' ) ];
	}

	private static function get_category_description( $category_name ) {
		switch ( $category_name ) {
			case esc_html__( 'Sales and Marketing', 'echo-knowledge-base' ):
				return esc_html__( 'Innovative strategies for promoting products and effectively reaching new customers.', 'echo-knowledge-base' );
			case esc_html__( 'Operations and Logistics', 'echo-knowledge-base' ):
				return esc_html__( 'Streamline processes for efficient, agile, and scalable business operations.', 'echo-knowledge-base' );
			case esc_html__( 'Human Resources', 'echo-knowledge-base' ):
				return esc_html__( 'Policies, procedures, and support for effective workforce management.', 'echo-knowledge-base' );
			case esc_html__( 'Finance and Expenses', 'echo-knowledge-base' ):
				return esc_html__( 'Efficiently manage finances, track expenditure accurately, and optimize budgets.', 'echo-knowledge-base' );
			case esc_html__( 'IT Support', 'echo-knowledge-base' ):
				return esc_html__( 'Comprehensive technical assistance and forward‑thinking solutions for resilient digital infrastructure.', 'echo-knowledge-base' );
			case esc_html__( 'Professional Development', 'echo-knowledge-base' ):
				return esc_html__( 'Enhance skills, explore career growth opportunities, and foster professional development.', 'echo-knowledge-base' );
			case esc_html__( 'Department Resources', 'echo-knowledge-base' ):
				return esc_html__( 'Resources and tools for each department to enhance productivity and efficiency.', 'echo-knowledge-base' );
			case esc_html__( 'Employee Handbook', 'echo-knowledge-base' ):
				return esc_html__( 'Guidelines, policies, and procedures to ensure a safe and productive work environment.', 'echo-knowledge-base' );
			case esc_html__( 'How-To Center', 'echo-knowledge-base' ):
				return esc_html__( 'Step-by-step guides and tutorials to help you navigate the company\'s tools and resources.', 'echo-knowledge-base' );
			case esc_html__( 'Performance Reviews', 'echo-knowledge-base' ):
				return esc_html__( 'Evaluate and improve employee performance systematically.', 'echo-knowledge-base' );
			case esc_html__( 'Safety Protocols', 'echo-knowledge-base' ):
				return esc_html__( 'Safeguarding data and ensuring system security measures.', 'echo-knowledge-base' );
			case esc_html__( 'Security Protocols', 'echo-knowledge-base' ):
				return esc_html__( 'Guidelines for ensuring workplace safety and security.', 'echo-knowledge-base' );
			default:
				return esc_html__( 'Category description', 'echo-knowledge-base' );

		}
	}

	public static function reassign_categories_to_articles_based_on_layout( $kb_id, $kb_main_page_layout ) {

		$articles_seq_meta = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
		if ( empty( $articles_seq_meta ) ) {
			return [];
		}

		$categories_seq_meta = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( empty( $categories_seq_meta ) ) {
			return [];
		}

		// get top categories
		$top_categories = [];
		foreach( $categories_seq_meta as $category_id => $sub_categories ) {
			if ( ! empty( $articles_seq_meta[$category_id][0] ) ) {
				$top_categories[$category_id] = $articles_seq_meta[$category_id][0];
			}
		}

		// top categories do not match DEMO data;check that sub-categories match demo data; are they tab or non-tab top categories?
		if ( array_diff( array_values( $top_categories ), self::get_tab_top_categories() ) ) {

			// we have non-Tab categories; are they non-tab DEMO categories or user data?
			if ( array_diff( array_values( $top_categories ), self::get_non_tab_top_categories() ) ) {
				return []; // unknown user top categories

			// we have non-tab top categories so add tab top categories
			} else if ( $kb_main_page_layout == EPKB_Layout::TABS_LAYOUT ) {

				// add tab top categories
				$tab_category_name_1 = self::get_tab_top_categories()[0];
				$tab_category_name_2 = self::get_tab_top_categories()[1];
				$tab_category_name_3 = self::get_tab_top_categories()[2];

				$tab_category_id = self::create_sample_category( $kb_id, $tab_category_name_1, null, true );
				if ( empty( $tab_category_id ) ) {
					return [];
				}
				$tab_category_id_2 = self::create_sample_category( $kb_id, $tab_category_name_2, null, true );
				if ( empty( $tab_category_id_2 ) ) {
					return [];
				}
				$tab_category_id_3 = self::create_sample_category( $kb_id, $tab_category_name_3, null, true );
				if ( empty( $tab_category_id_3 ) ) {
					return [];
				}

				// assign sub-categories to the top categories
				foreach( $categories_seq_meta as $category_id => $sub_categories ) {
					wp_update_term( $category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), array( 'parent' => $tab_category_id ) );
				}

				// add tab top categories to categories and articles sequences
				$categories_seq_meta_tmp = $categories_seq_meta;
				$categories_seq_meta = [];
				$categories_seq_meta[$tab_category_id] = $categories_seq_meta_tmp;
				$categories_seq_meta[$tab_category_id_2] = [];
				$categories_seq_meta[$tab_category_id_3] = [];
				$articles_seq_meta[$tab_category_id] = [ '0' => $tab_category_name_1, '1' => self::get_category_description( $tab_category_name_1 ) ];
				$articles_seq_meta[$tab_category_id_2] = [ '0' => $tab_category_name_2, '1' => self::get_category_description( $tab_category_name_2 ) ];
				$articles_seq_meta[$tab_category_id_3] = [ '0' => $tab_category_name_3, '1' => self::get_category_description( $tab_category_name_3 ) ];

			} else {
				return []; // non-tab layout already has non-tab top categories
			}

		// we found matching DEMO top Tab categories and Tab layout
		} else if ( $kb_main_page_layout == EPKB_Layout::TABS_LAYOUT ) {
			return []; // Tab layout already has tab top categories

		// we found matching DEMO top Tab categories but non-Tab layout
		} else {

			// remove DEMO top tabs from categories
			$top_categories_ids = array_keys( $top_categories );
			if ( empty( $categories_seq_meta[$top_categories_ids[0]] ) ) {
				return [];
			}

			foreach( $top_categories as $top_category_id => $top_category_name ) {
				wp_delete_term( $top_category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), array( 'parent' => 0 ) );
			}

			// remove DEMO top tabs categories in categories and articles sequences
			foreach( $articles_seq_meta as $category_id => $value ) {
				if ( $category_id == $top_categories_ids[0] || $category_id == $top_categories_ids[1] || $category_id == $top_categories_ids[2] ) {
					unset( $articles_seq_meta[$category_id] );
				}
			}
			$categories_seq_meta_temp = [];
			foreach( $categories_seq_meta[$top_categories_ids[0]] as $sub_category_id => $sub_sub_categories ) {
				$categories_seq_meta_temp[$sub_category_id] = $sub_sub_categories;
			}
			$categories_seq_meta = $categories_seq_meta_temp;
		}

		return [ 'articles_seq_meta' => $articles_seq_meta, 'categories_seq_meta' => $categories_seq_meta ];
	}

	private static function create_category_and_articles( $new_kb_id, $category_name, $parent_category_id, $article_titles, &$articles_seq_meta, &$categories_seq_meta ) {

		$category_id = self::create_sample_category( $new_kb_id, $category_name, $parent_category_id );
		if ( empty( $category_id ) ) {
			return false;
		}

		$first_article = true;
		foreach ( $article_titles as $article_title ) {
			$article_id = self::create_sample_article( $new_kb_id, $category_id, $article_title );
			if ( empty( $article_id ) || is_wp_error( $article_id ) ) {
				return false;
			}

			if ( $first_article ) {
				$articles_seq_meta[$category_id] = [ '0' => $category_name, '1' => self::get_category_description( $category_name ) ];
			}
			$articles_seq_meta[$category_id] += [$article_id => $article_title];
			$first_article = false;
		}

		if ( $parent_category_id ) {
			if ( ! isset( $categories_seq_meta[$parent_category_id] ) ) {
				$categories_seq_meta[$parent_category_id] = [];
			}
			$categories_seq_meta[$parent_category_id] += [$category_id => []];
		} else {
			$categories_seq_meta[$category_id] = [];
		}

		return $category_id;
	}

	private static function create_sample_category( $kb_id, $category_name, $parent_id=null, $check_if_exists=false ) {

		if ( $check_if_exists ) {
			$term = get_term_by( 'name', $category_name, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) );
			if ( $term ) {
				return $term->term_id;
			}
		}

		$args = empty( $parent_id ) ? array( 'description' => self::get_category_description( $category_name ) )
								  : array( 'parent' => $parent_id, 'description' => self::get_category_description( $category_name ) );

		// insert category
		$term_id_array = wp_insert_term( $category_name, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), $args );
		if ( is_wp_error( $term_id_array ) || ! isset( $term_id_array['term_id'] ) ) {
			return null;
		}

		return $term_id_array['term_id'];
	}

	private static function create_sample_article( $new_kb_id, $kb_term_id, $post_title ) {

		$post_excerpt = esc_html__( 'This is a demo article excerpt.', 'echo-knowledge-base' );

		$my_post = array(
			'post_title'    => $post_title,
			'post_type'     => EPKB_KB_Handler::get_post_type( $new_kb_id ),
			'post_content'  => self::get_sample_post_content(),
			'post_excerpt'  => $post_excerpt,
			'post_status'   => 'publish',
			// current user or 'post_author'   => 1,
		);

		// create article under category
		$post_id = wp_insert_post( $my_post );
		if ( is_wp_error( $post_id ) || empty( $post_id ) ) {
			return null;
		}

		$result = wp_set_object_terms( $post_id, $kb_term_id, EPKB_KB_Handler::get_category_taxonomy_name( $new_kb_id ) );
		if ( is_wp_error( $result ) ) {
			return null;
		}

		return $post_id;
	}

	public static function get_sample_post_content() {

		$demo_img = Echo_Knowledge_Base::$plugin_url.'img/guy-on-laptop.jpg';
		$youtube_vid = '<iframe width="560" height="315" src="https://www.youtube.com/embed/gOLT-IDT3UY?si=amjYFxs-Cf_CHqFM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';

		return
			"<h2 style='padding-top: 20px;'>" . esc_html__( 'Welcome to Echo Knowledge Base!', 'echo-knowledge-base' ) . "</h2>" .
			"<p>" . esc_html__( "We're thrilled that you've chosen our plugin to enhance your Knowledge Base. We're here to assist you in making your Knowledge Base exceptional. If you need any help or have questions, just let us know!", 'echo-knowledge-base' ) . "</p>" .

			"<h3 style='padding-top: 20px;'>" . esc_html__( 'Create Articles Just Like You Create Posts', 'echo-knowledge-base' ) . "</h3>" .
			"<p>" . esc_html__( 'Add instructional videos or product demos. Example of embedded video:', 'echo-knowledge-base' ) . "</p>" .
				$youtube_vid .

			"<p>" . esc_html__( 'Enhance your articles with visuals. Example of an image:', 'echo-knowledge-base' ) . "</p>" .
			"<img src='" . esc_url( $demo_img ) . "' " . "alt='" . esc_attr__( 'Sample Image', 'echo-knowledge-base' ) . "' width='500'>" .

			"<p>" . esc_html__( 'Effortlessly embed PDFs and other media using your page builder or blocks.', 'echo-knowledge-base' ) . "</p>" .

			"<h3 style='padding-top: 20px;'>" . esc_html__( 'Main Features', 'echo-knowledge-base' ) . "</h3>" .
			"<p>" . esc_html__( 'Our plugin includes the following features to make your Knowledge Base stand out:', 'echo-knowledge-base' ) . "</p>" .
			"<ul>" .
			    "<li>" . esc_html__('Fast search bar with listed results', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Five levels of hierarchical documentation', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Article view counter with Popular and Recent Articles', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Frequently Asked Questions (FAQ) Module and shortcode.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Customizable Category Archive Page', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('AI Content Writing', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Organize articles and categories alphabetically, chronologically, or in any custom order with drag and drop.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Optimized for the best SEO results to boost online visibility', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Analytics to track your Knowledge Base usage', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Supports RTL languages, WCAG accessibility standards, and works with WPML and Polylang', 'echo-knowledge-base' ) . "</li>" .
			"</ul>" .

			"<h3 style='padding-top: 20px;'>" . esc_html__( 'PRO Features', 'echo-knowledge-base' ) . "</h3>" .
			"<p>" . esc_html__( 'Expand your Knowledge Base using our cost-effective add-ons:', 'echo-knowledge-base' ) . "</p>" .
			"<ul>" .
			    "<li>" . esc_html__('Control access and permissions based on groups, WordPress users and roles, and custom roles.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Manage public and private articles and control who can read, write, and edit articles.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Create unlimited Knowledge Bases with separate articles, categories, tags, and more.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Choose from Sidebar and Grid layouts.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Import and export articles and categories using CSV and XML', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Add article voting, a feedback form, and learn from analytics.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('More widgets and shortcodes for categories, popular articles, and the search bar.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Replace articles with links to PDFs, external docs, video links, and more.', 'echo-knowledge-base' ) . "</li>" .
			    "<li>" . esc_html__('Utilize Advanced search analytics to identify popular and empty searches.', 'echo-knowledge-base' ) . "</li>" .
			"</ul>" .

			"<h3 style='padding-top: 20px;'>" . esc_html__( 'Need Help or Looking for a Feature?', 'echo-knowledge-base' ) . "</h3>" .
			"<p>" .
			    sprintf(
			        esc_html__( "Please don't hesitate to contact us %shere%s.", 'echo-knowledge-base' ),
			        '<a href="https://www.echoknowledgebase.com/pre-sale-question/" target="_blank">',
			        '</a>'
			    ) .
			"</p>" .
			"<p>" . esc_html__( "We're always eager to help and are open to suggestions for new features.", 'echo-knowledge-base' ) . "</p>";
	}

	public static function create_sample_faqs( $new_kb_id ) {

		$faq_group = wp_create_term( esc_html__( 'Frequently Asked Questions', 'echo-knowledge-base' ), EPKB_FAQs_CPT_Setup::FAQ_CATEGORY );
		if ( is_wp_error( $faq_group ) || empty( $faq_group['term_id'] ) ) {
			return;
		}

		// update FAQ Group id
		$faq_group_id = $faq_group['term_id'];

		// update FAQ Group status
		/* $result = update_term_meta( $faq_group_id, 'faq_group_status', 'publish' );
		if ( is_wp_error( $result ) ) {
			return;
		} */

		$faq_id1 = self::create_one_faq( esc_html__( 'What are the steps to submit a purchase order?', 'echo-knowledge-base' ),
			'<p>' . esc_html__( 'Here\'s the process to submit a purchase order:', 'echo-knowledge-base' ) . '<br>
					    <ol>
					        <li>' . esc_html__( 'Fill out the purchase order form.', 'echo-knowledge-base' ) . '</li>
					        <li>' . esc_html__( 'Obtain the necessary approvals from your manager or department head.', 'echo-knowledge-base' ) . '</li>
					        <li>' . esc_html__( 'Submit the approved purchase order to the procurement team.', 'echo-knowledge-base' ) . '</li>
					    </ol>
			        </p>' );
		$faq_id2 = self::create_one_faq( esc_html__( 'Where can I find templates for customer presentations?', 'echo-knowledge-base' ),
					"<p>" . esc_html__( 'We have a library of customer presentation templates within the Sales & Marketing section of our knowledge base.', 'echo-knowledge-base' ) . "
				    </p>" );
		$faq_id3 = self::create_one_faq( esc_html__( 'What is the process for requesting time off?', 'echo-knowledge-base' ),
					"<ol>
				        <li>" . esc_html__( 'Access our Time Off Request form.', 'echo-knowledge-base' ) . "</li>
				        <li>" . esc_html__( "Fill out the form, including your desired dates and any relevant notes.", 'echo-knowledge-base' ) . "</li>
				        <li>" . esc_html__( 'Submit the form to your manager for approval.', 'echo-knowledge-base' ) . "</li>
                    </ol>");

		if ( empty( $faq_id1 ) || is_wp_error( $faq_id1 ) ||
				empty( $faq_id2 ) || is_wp_error( $faq_id2 ) ||
				empty( $faq_id3 ) || is_wp_error( $faq_id3 ) ) {
			return;
		}

		// include new FAQs
		foreach ( [$faq_id1, $faq_id2, $faq_id3] as $faq_id ) {
			wp_set_object_terms( $faq_id, $faq_group_id, EPKB_FAQs_CPT_Setup::FAQ_CATEGORY, true );
		}

		// update FAQs sequence
		$result = update_term_meta( $faq_group_id, 'faqs_order_sequence', [$faq_id1, $faq_id2, $faq_id3] );
		if ( is_wp_error( $result ) ) {
			return;
		}

		$result = EPKB_Utilities::save_kb_option( $new_kb_id, EPKB_ML_FAQs::FAQ_GROUP_IDS, [$faq_group_id] );
		if ( is_wp_error( $result ) ) {
			return;
		}
	}

	private static function create_one_faq( $faq_question, $faq_answer ) {
		$faq_args = array(
			'post_title'        => $faq_question,
			'post_type'         => EPKB_FAQs_CPT_Setup::FAQS_POST_TYPE,
			'post_content'      => $faq_answer,
			'post_status'       => 'publish',
			'comment_status'    => 'closed'
		);
		$faq_id = wp_insert_post( $faq_args, true );
		if ( empty( $faq_id ) || is_wp_error( $faq_id ) ) {
			return null;
		}

		return $faq_id;
	}

	/**
	 * Check if the current KB has demo data or user data
	 *
	 * @param int $kb_id
	 * @return bool True if demo data, false if user data
	 */
	public static function is_demo_data( $kb_id ) {

		$articles_seq_meta = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
		if ( empty( $articles_seq_meta ) ) {
			return true; // Show demo data if no articles exist
		}

		$categories_seq_meta = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( empty( $categories_seq_meta ) ) {
			return true; // Show demo data if no categories exist
		}

		// Get top categories
		$top_categories = [];
		foreach( $categories_seq_meta as $category_id => $sub_categories ) {
			if ( ! empty( $articles_seq_meta[$category_id][0] ) ) {
				$top_categories[$category_id] = $articles_seq_meta[$category_id][0];
			}
		}

		// Check if current top categories match demo tab categories
		if ( ! array_diff( array_values( $top_categories ), self::get_tab_top_categories() ) ) {
			return true; // We have demo tab categories
		}

		// Check if current top categories match demo non-tab categories
		if ( ! array_diff( array_values( $top_categories ), self::get_non_tab_top_categories() ) ) {
			return true; // We have demo non-tab categories
		}

		return false; // User has custom data
	}

	/**
	 * Get demo weekly views data for analytics
	 *
	 * @param int $weeks_back Number of weeks to generate data for (default: 12)
	 * @return array Array of weekly data with 'week_label' and 'total_views'
	 */
	public static function get_demo_weekly_views_data( $weeks_back = 12 ) {

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}
		$weekly_data = array();

		// Base views with gradual growth trend
		$base_views = 450;

		for ( $i = $weeks_back - 1; $i >= 0; $i-- ) {
			$week_date = clone $now;
			$week_date->modify( "-{$i} weeks" );

			// Add growth trend (5% per week) with random variation
			$growth_factor = 1 + ( ( $weeks_back - $i ) * 0.05 );
			$random_variation = wp_rand( 85, 115 ) / 100; // ±15% variation
			$total_views = round( $base_views * $growth_factor * $random_variation );

			$weekly_data[] = array(
				'week_label'  => $week_date->format( 'M j, Y' ),
				'total_views' => $total_views,
			);
		}

		return $weekly_data;
	}

	/**
	 * Get demo weekly searches data for analytics
	 *
	 * @param int $weeks_back Number of weeks to generate data for (default: 12)
	 * @return array Array of weekly data with 'week_label' and 'total_searches'
	 */
	public static function get_demo_weekly_searches_data( $weeks_back = 12 ) {

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}
		$weekly_data = array();

		// Base searches with gradual growth trend
		$base_searches = 180;

		for ( $i = $weeks_back - 1; $i >= 0; $i-- ) {
			$week_date = clone $now;
			$week_date->modify( "-{$i} weeks" );

			// Add growth trend (4% per week) with random variation
			$growth_factor = 1 + ( ( $weeks_back - $i ) * 0.04 );
			$random_variation = wp_rand( 80, 120 ) / 100; // ±20% variation
			$total_searches = round( $base_searches * $growth_factor * $random_variation );

			$weekly_data[] = array(
				'week_label'     => $week_date->format( 'M j, Y' ),
				'total_searches' => $total_searches,
			);
		}

		return $weekly_data;
	}

	/**
	 * Get demo weekly ratings data for analytics
	 *
	 * @param int $weeks_back Number of weeks to generate data for (default: 12)
	 * @return array Array of weekly data with 'week_label', 'positive_ratings', and 'negative_ratings'
	 */
	public static function get_demo_weekly_ratings_data( $weeks_back = 12 ) {

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}
		$weekly_data = array();

		// Base ratings with improvement trend (more positive over time)
		$base_positive = 35;
		$base_negative = 15;

		for ( $i = $weeks_back - 1; $i >= 0; $i-- ) {
			$week_date = clone $now;
			$week_date->modify( "-{$i} weeks" );

			// Positive ratings grow faster, negative ratings decline
			$positive_growth = 1 + ( ( $weeks_back - $i ) * 0.06 );
			$negative_decline = 1 - ( ( $weeks_back - $i ) * 0.02 );

			$positive_variation = wp_rand( 85, 115 ) / 100;
			$negative_variation = wp_rand( 85, 115 ) / 100;

			$positive_ratings = round( $base_positive * $positive_growth * $positive_variation );
			$negative_ratings = max( 5, round( $base_negative * $negative_decline * $negative_variation ) );

			$weekly_data[] = array(
				'week_label'       => $week_date->format( 'M j, Y' ),
				'positive_ratings' => $positive_ratings,
				'negative_ratings' => $negative_ratings,
			);
		}

		return $weekly_data;
	}

	/**
	 * Get demo period comparison data (this period vs previous period)
	 *
	 * @param string $period 'week', 'month', or 'year'
	 * @return array Array with 'current_period', 'previous_period', 'change_percent', 'is_positive'
	 */
	public static function get_demo_period_comparison_data( $period = 'month' ) {

		$now = EPKB_Utilities::create_datetime();
		if ( $now === null ) {
			return array();
		}

		// Define labels based on period
		if ( $period === 'week' ) {
			$current_label = 'This Week';
			$previous_label = 'Last Week';
			$current_views = wp_rand( 520, 580 );
			$growth_percent = wp_rand( 8, 15 );
		} elseif ( $period === 'month' ) {
			$current_label = 'This Month';
			$previous_label = 'Last Month';
			$current_views = wp_rand( 2200, 2500 );
			$growth_percent = wp_rand( 12, 20 );
		} else {
			$current_label = 'This Year';
			$previous_label = 'Last Year';
			$current_views = wp_rand( 24000, 28000 );
			$growth_percent = wp_rand( 25, 35 );
		}

		$previous_views = round( $current_views / ( 1 + ( $growth_percent / 100 ) ) );

		return array(
			'current_period'    => array(
				'label' => $current_label,
				'views' => $current_views,
			),
			'previous_period'   => array(
				'label' => $previous_label,
				'views' => $previous_views,
			),
			'change_percent'    => $growth_percent,
			'is_positive'       => true,
		);
	}

	/**
	 * Get demo growth rate data for all periods
	 *
	 * @return array Array with 'weekly', 'monthly', 'yearly' comparison data
	 */
	public static function get_demo_growth_rate_data() {

		return array(
			'weekly'  => self::get_demo_period_comparison_data( 'week' ),
			'monthly' => self::get_demo_period_comparison_data( 'month' ),
			'yearly'  => self::get_demo_period_comparison_data( 'year' ),
		);
	}

	/**
	 * Get demo day-of-week pattern data
	 *
	 * @return array Array with day-of-week averages
	 */
	public static function get_demo_day_of_week_data() {

		// Typical business pattern: higher on weekdays, lower on weekends
		return array(
			array( 'label' => 'Sunday', 'avg_views' => 45.3 ),
			array( 'label' => 'Monday', 'avg_views' => 82.7 ),
			array( 'label' => 'Tuesday', 'avg_views' => 91.2 ),
			array( 'label' => 'Wednesday', 'avg_views' => 95.8 ),
			array( 'label' => 'Thursday', 'avg_views' => 88.4 ),
			array( 'label' => 'Friday', 'avg_views' => 76.1 ),
			array( 'label' => 'Saturday', 'avg_views' => 38.6 ),
		);
	}

	/**
	 * Get demo article engagement distribution data
	 *
	 * @return array Array with distribution segments
	 */
	public static function get_demo_engagement_distribution_data() {

		// Realistic distribution showing healthy content with some low performers
		return array(
			array( 'label' => '0 Views', 'count' => 8 ),
			array( 'label' => '1-10 Views', 'count' => 15 ),
			array( 'label' => '11-50 Views', 'count' => 22 ),
			array( 'label' => '51-100 Views', 'count' => 18 ),
			array( 'label' => '101-500 Views', 'count' => 12 ),
			array( 'label' => '500+ Views', 'count' => 5 ),
		);
	}

	/**
	 * Get demo most viewed articles with categories
	 *
	 * @param int $limit Number of articles to return (default: 100)
	 * @return array Array of articles with 'title', 'views', 'category', 'url'
	 */
	public static function get_demo_most_viewed_articles( $limit = 100 ) {

		$demo_articles = array(
			array( 'title' => 'Introduction to Our Sales Process', 'category' => 'Sales and Marketing', 'views' => 1247 ),
			array( 'title' => 'Creating Effective Marketing Campaigns', 'category' => 'Sales and Marketing', 'views' => 1092 ),
			array( 'title' => 'Using the CRM Software', 'category' => 'Sales and Marketing', 'views' => 987 ),
			array( 'title' => 'Inventory Management Best Practices', 'category' => 'Operations and Logistics', 'views' => 876 ),
			array( 'title' => 'Understanding the Supply Chain', 'category' => 'Operations and Logistics', 'views' => 754 ),
			array( 'title' => 'Safety Protocols in the Workplace', 'category' => 'Operations and Logistics', 'views' => 698 ),
			array( 'title' => 'Onboarding Checklist for New Hires', 'category' => 'Human Resources', 'views' => 643 ),
			array( 'title' => 'Understanding Your Benefits Package', 'category' => 'Human Resources', 'views' => 587 ),
			array( 'title' => 'Performance Review Guidelines', 'category' => 'Human Resources', 'views' => 521 ),
			array( 'title' => 'Submitting Expense Reports', 'category' => 'Finance and Expenses', 'views' => 498 ),
			array( 'title' => 'Travel Expense Guidelines', 'category' => 'Finance and Expenses', 'views' => 467 ),
			array( 'title' => 'Year-End Tax Information for Employees', 'category' => 'Finance and Expenses', 'views' => 412 ),
			array( 'title' => 'Getting Started with Your Work Computer', 'category' => 'IT Support', 'views' => 389 ),
			array( 'title' => 'How to Request IT Support', 'category' => 'IT Support', 'views' => 356 ),
			array( 'title' => 'Security Protocols for Safe Computing', 'category' => 'IT Support', 'views' => 324 ),
			array( 'title' => 'Identifying Training Opportunities', 'category' => 'Professional Development', 'views' => 298 ),
			array( 'title' => 'Mentorship Programs Overview', 'category' => 'Professional Development', 'views' => 276 ),
			array( 'title' => 'Setting Career Goals', 'category' => 'Professional Development', 'views' => 245 ),
		);

		return array_slice( $demo_articles, 0, $limit );
	}

	/**
	 * Get demo articles with zero engagement
	 *
	 * @return array Array of articles with 'title' and 'views' (always 0)
	 */
	public static function get_demo_zero_engagement_articles() {

		return array(
			array( 'title' => 'Advanced Budget Forecasting Techniques', 'views' => 0 ),
			array( 'title' => 'Quarterly Strategic Planning Process', 'views' => 0 ),
			array( 'title' => 'Internal Communication Protocol Updates', 'views' => 0 ),
		);
	}

	/**
	 * Get demo outlier articles (high and low performers)
	 *
	 * @return array Array with 'high_performers', 'low_performers', 'mean', 'std_dev'
	 */
	public static function get_demo_outlier_articles() {

		return array(
			'high_performers' => array(
				array( 'title' => 'Introduction to Our Sales Process', 'views' => 1247, 'z_score' => 3.45 ),
				array( 'title' => 'Creating Effective Marketing Campaigns', 'views' => 1092, 'z_score' => 2.87 ),
				array( 'title' => 'Using the CRM Software', 'views' => 987, 'z_score' => 2.51 ),
			),
			'low_performers' => array(
				array( 'title' => 'Annual Compliance Training Requirements', 'views' => 42, 'z_score' => -1.85 ),
				array( 'title' => 'Office Furniture Replacement Procedures', 'views' => 38, 'z_score' => -1.92 ),
				array( 'title' => 'Conference Room Booking Guidelines', 'views' => 31, 'z_score' => -2.03 ),
			),
			'mean'            => 456.3,
			'std_dev'         => 287.5,
		);
	}

	/**
	 * Get demo most improved articles
	 *
	 * @return array Array of articles with improvement data
	 */
	public static function get_demo_most_improved_articles() {

		return array(
			array(
				'title'           => 'Security Protocols for Safe Computing',
				'current_views'   => 324,
				'previous_views'  => 156,
				'absolute_change' => 168,
				'percent_change'  => 107.7,
			),
			array(
				'title'           => 'Understanding Your Benefits Package',
				'current_views'   => 587,
				'previous_views'  => 341,
				'absolute_change' => 246,
				'percent_change'  => 72.1,
			),
			array(
				'title'           => 'How to Request IT Support',
				'current_views'   => 356,
				'previous_views'  => 218,
				'absolute_change' => 138,
				'percent_change'  => 63.3,
			),
			array(
				'title'           => 'Travel Expense Guidelines',
				'current_views'   => 467,
				'previous_views'  => 312,
				'absolute_change' => 155,
				'percent_change'  => 49.7,
			),
			array(
				'title'           => 'Mentorship Programs Overview',
				'current_views'   => 276,
				'previous_views'  => 189,
				'absolute_change' => 87,
				'percent_change'  => 46.0,
			),
		);
	}

	/**
	 * Get demo category analytics data
	 *
	 * @return array Array of categories with article count and total views
	 */
	public static function get_demo_category_analytics() {

		return array(
			array( 'category' => 'Sales and Marketing', 'article_count' => 3, 'total_views' => 3326 ),
			array( 'category' => 'Operations and Logistics', 'article_count' => 3, 'total_views' => 2328 ),
			array( 'category' => 'Human Resources', 'article_count' => 3, 'total_views' => 1751 ),
			array( 'category' => 'Finance and Expenses', 'article_count' => 3, 'total_views' => 1377 ),
			array( 'category' => 'IT Support', 'article_count' => 3, 'total_views' => 1069 ),
			array( 'category' => 'Professional Development', 'article_count' => 3, 'total_views' => 819 ),
		);
	}

	/**
	 * Get demo tag analytics data
	 *
	 * @return array Array of tags with article count and total views
	 */
	public static function get_demo_tag_analytics() {

		return array(
			array( 'tag' => 'Getting Started', 'article_count' => 5, 'total_views' => 3847 ),
			array( 'tag' => 'Best Practices', 'article_count' => 4, 'total_views' => 2892 ),
			array( 'tag' => 'Guidelines', 'article_count' => 6, 'total_views' => 2567 ),
			array( 'tag' => 'Policies', 'article_count' => 4, 'total_views' => 1923 ),
			array( 'tag' => 'Training', 'article_count' => 3, 'total_views' => 1456 ),
			array( 'tag' => 'Procedures', 'article_count' => 5, 'total_views' => 1234 ),
			array( 'tag' => 'Quick Reference', 'article_count' => 3, 'total_views' => 987 ),
			array( 'tag' => 'Advanced', 'article_count' => 2, 'total_views' => 542 ),
		);
	}

	/**
	 * Get demo best rated articles data
	 *
	 * @return array Array of articles with ratings
	 */
	public static function get_demo_best_rated_articles() {
		return array(
			array( 'title' => 'Introduction to Our Sales Process', 'avg_rating' => 4.8 ),
			array( 'title' => 'Creating Effective Marketing Campaigns', 'avg_rating' => 4.7 ),
			array( 'title' => 'Using the CRM Software', 'avg_rating' => 4.6 ),
			array( 'title' => 'Inventory Management Best Practices', 'avg_rating' => 4.5 ),
			array( 'title' => 'Understanding the Supply Chain', 'avg_rating' => 4.5 ),
			array( 'title' => 'Onboarding Checklist for New Hires', 'avg_rating' => 4.4 ),
			array( 'title' => 'Understanding Your Benefits Package', 'avg_rating' => 4.3 ),
			array( 'title' => 'Getting Started with Your Work Computer', 'avg_rating' => 4.2 ),
			array( 'title' => 'How to Request IT Support', 'avg_rating' => 4.1 ),
			array( 'title' => 'Setting Career Goals', 'avg_rating' => 4.0 ),
		);
	}

	/**
	 * Get demo most rated articles data
	 *
	 * @return array Array of articles with rating counts
	 */
	public static function get_demo_most_rated_articles() {
		return array(
			array( 'title' => 'Introduction to Our Sales Process', 'times' => 234 ),
			array( 'title' => 'Creating Effective Marketing Campaigns', 'times' => 198 ),
			array( 'title' => 'Using the CRM Software', 'times' => 187 ),
			array( 'title' => 'Inventory Management Best Practices', 'times' => 156 ),
			array( 'title' => 'Understanding the Supply Chain', 'times' => 143 ),
			array( 'title' => 'Onboarding Checklist for New Hires', 'times' => 132 ),
			array( 'title' => 'Understanding Your Benefits Package', 'times' => 121 ),
			array( 'title' => 'Safety Protocols in the Workplace', 'times' => 109 ),
			array( 'title' => 'Performance Review Guidelines', 'times' => 98 ),
			array( 'title' => 'Submitting Expense Reports', 'times' => 87 ),
		);
	}

	/**
	 * Get demo worst rated articles data
	 *
	 * @return array Array of articles with low ratings
	 */
	public static function get_demo_worst_rated_articles() {
		return array(
			array( 'title' => 'Advanced Budget Forecasting Techniques', 'avg_rating' => 2.1 ),
			array( 'title' => 'Quarterly Strategic Planning Process', 'avg_rating' => 2.3 ),
			array( 'title' => 'Internal Communication Protocol Updates', 'avg_rating' => 2.5 ),
			array( 'title' => 'Office Furniture Replacement Procedures', 'avg_rating' => 2.7 ),
			array( 'title' => 'Conference Room Booking Guidelines', 'avg_rating' => 2.8 ),
		);
	}

	/**
	 * Get demo least rated articles data
	 *
	 * @return array Array of articles with fewest ratings
	 */
	public static function get_demo_least_rated_articles() {
		return array(
			array( 'title' => 'Advanced Budget Forecasting Techniques', 'times' => 12 ),
			array( 'title' => 'Quarterly Strategic Planning Process', 'times' => 15 ),
			array( 'title' => 'Internal Communication Protocol Updates', 'times' => 18 ),
			array( 'title' => 'Office Furniture Replacement Procedures', 'times' => 21 ),
			array( 'title' => 'Conference Room Booking Guidelines', 'times' => 24 ),
			array( 'title' => 'Annual Compliance Training Requirements', 'times' => 27 ),
		);
	}

	/**
	 * Check if a specific article is a demo article
	 *
	 * @param int $article_id Article ID to check
	 * @return bool True if article is a demo article, false otherwise
	 */
	public static function is_demo_article( $article_id ) {

		// Get the article
		$article = get_post( $article_id );
		if ( ! $article ) {
			return false;
		}

		// Get KB ID from article post type
		$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $article->post_type );
		if ( empty( $kb_id ) ) {
			return false;
		}

		// First check if this KB has demo data
		if ( ! self::is_demo_data( $kb_id ) ) {
			return false;
		}

		// Get list of demo article titles
		$demo_article_titles = self::get_demo_article_titles();

		// Check if article title matches any demo article title
		return in_array( $article->post_title, $demo_article_titles );
	}

	/**
	 * Get all demo article titles
	 *
	 * @return array Array of demo article title strings
	 */
	private static function get_demo_article_titles() {
		return array(
			esc_html__('Introduction to Our Sales Process', 'echo-knowledge-base' ),
			esc_html__('Creating Effective Marketing Campaigns', 'echo-knowledge-base' ),
			esc_html__('Using the CRM Software', 'echo-knowledge-base' ),
			esc_html__('Inventory Management Best Practices', 'echo-knowledge-base' ),
			esc_html__('Understanding the Supply Chain', 'echo-knowledge-base' ),
			esc_html__('Safety Protocols in the Workplace', 'echo-knowledge-base' ),
			esc_html__('Onboarding Checklist for New Hires', 'echo-knowledge-base' ),
			esc_html__('Understanding Your Benefits Package', 'echo-knowledge-base' ),
			esc_html__('Performance Review Guidelines', 'echo-knowledge-base' ),
			esc_html__('Submitting Expense Reports', 'echo-knowledge-base' ),
			esc_html__('Travel Expense Guidelines', 'echo-knowledge-base' ),
			esc_html__('Year-End Tax Information for Employees', 'echo-knowledge-base' ),
			esc_html__('Getting Started with Your Work Computer', 'echo-knowledge-base' ),
			esc_html__('How to Request IT Support', 'echo-knowledge-base' ),
			esc_html__('Security Protocols for Safe Computing', 'echo-knowledge-base' ),
			esc_html__('Identifying Training Opportunities', 'echo-knowledge-base' ),
			esc_html__('Mentorship Programs Overview', 'echo-knowledge-base' ),
			esc_html__('Setting Career Goals', 'echo-knowledge-base' ),
		);
	}

	/**
	 * Get demo Tags Usage analytics data
	 *
	 * @return array Demo tags usage data
	 */
	public static function get_demo_tags_usage_data() {
		return array(
			'version' => '1.0',
			'score' => 85,
			'kb_category_count' => 1,
			'tag_count' => 5,
			'tags' => array( 'Getting Started', 'Best Practices', 'Guidelines', 'Training', 'Procedures' ),
			'current_tags' => array(),
			'suggested_tags' => array( 'Employee Resources', 'Onboarding', 'Workplace' ),
			'recommended_tags' => array(
				array(
					'priority' => 'medium',
					'type' => 'ai_suggestions',
					'message' => __( 'AI-powered tag suggestions based on content analysis:', 'echo-knowledge-base' ),
					'suggested_tags' => array( 'Employee Resources', 'Onboarding', 'Workplace' )
				)
			),
			'tag_analysis' => array(
				'count_score' => array( 'score' => 100, 'issues' => array() ),
				'format_score' => array( 'score' => 100, 'issues' => array() ),
				'duplicate_score' => array( 'score' => 100, 'issues' => array(), 'duplicates' => array() ),
				'category_score' => array( 'score' => 100, 'issues' => array() ),
				'relevance_score' => array(
					'score' => 85,
					'issues' => array(),
					'irrelevant_tags' => array(),
					'weak_tags' => array(),
					'strong_matches' => 4,
					'weak_matches' => 1
				),
				'ai_suggestions' => array(
					'score' => 100,
					'issues' => array(),
					'suggestions' => array( 'Employee Resources', 'Onboarding', 'Workplace' ),
					'ai_feature_unavailable' => false,
					'ai_error' => null
				)
			),
			'analyzed_at' => current_time( 'mysql' ),
			'is_demo' => true
		);
	}

	/**
	 * Get demo Readability analytics data
	 *
	 * @return array Demo readability data
	 */
	public static function get_demo_readability_data() {
		return array(
			'version' => '1.0',
			'score' => 90,
			'status' => 'analyzed',
			'issues' => array(
				array(
					'issue_type' => 'Complexity',
					'problematic_text' => 'The comprehensive onboarding process encompasses a multitude of procedural steps and documentation requirements.',
					'explanation' => 'This sentence is overly complex and could be simplified. Suggested rewrite: "The onboarding process includes several steps and required documents."'
				),
				array(
					'issue_type' => 'Jargon',
					'problematic_text' => 'Utilize the synergistic framework for optimal stakeholder engagement.',
					'explanation' => 'This sentence contains business jargon that may confuse readers. Suggested rewrite: "Use the framework to engage with stakeholders effectively."'
				)
			),
			'ai_feature_unavailable' => false,
			'analyzed_at' => current_time( 'mysql' ),
			'is_demo' => true
		);
	}

	/**
	 * Get demo popular search terms
	 *
	 * @return array Array of search terms with counts
	 */
	public static function get_demo_popular_searches() {
		return array(
			array( 'term' => 'sales process', 'times' => 342 ),
			array( 'term' => 'CRM software', 'times' => 298 ),
			array( 'term' => 'benefits', 'times' => 276 ),
			array( 'term' => 'expense report', 'times' => 254 ),
			array( 'term' => 'onboarding', 'times' => 231 ),
			array( 'term' => 'IT support', 'times' => 198 ),
			array( 'term' => 'performance review', 'times' => 176 ),
			array( 'term' => 'inventory management', 'times' => 165 ),
			array( 'term' => 'security protocols', 'times' => 143 ),
			array( 'term' => 'marketing campaign', 'times' => 132 ),
		);
	}

	/**
	 * Get demo no results search terms
	 *
	 * @return array Array of search terms with no results
	 */
	public static function get_demo_no_results_searches() {
		return array(
			array( 'term' => 'remote work policy', 'times' => 45 ),
			array( 'term' => 'parking permits', 'times' => 38 ),
			array( 'term' => 'team building activities', 'times' => 32 ),
			array( 'term' => 'holiday schedule', 'times' => 27 ),
			array( 'term' => 'company swag', 'times' => 21 ),
			array( 'term' => 'gym membership', 'times' => 18 ),
			array( 'term' => 'pet policy', 'times' => 15 ),
			array( 'term' => 'bike storage', 'times' => 12 ),
		);
	}

	/**
	 * Get demo search statistics
	 *
	 * @return array Array with search statistics
	 */
	public static function get_demo_search_statistics() {
		return array(
			'total_searches' => array( 'Total Searches', 2543 ),
			'total_no_results_searches' => array( 'Total No Results Searches', 208 ),
		);
	}

	/**
	 * Get demo data for specific AI Search Results section (core sections only)
	 * Pro sections are handled by AI Features PRO
	 *
	 * @param string $section_id Section identifier
	 * @param array $data Request data with 'query' and 'kb_id'
	 * @return array Section data with 'has_content', 'html', 'data'
	 */
	public static function get_ai_search_results_section_demo_data( $section_id, $data ) {

		$query = isset( $data['query'] ) ? $data['query'] : '';
		$query_display = trim( (string) $query );
		if ( '' === $query_display ) {
			$query_display = __( 'your knowledge base', 'echo-knowledge-base' );
		}

		switch ( $section_id ) {
			case 'ai-answer':
				$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_ai_answer_name' );

				$html  = '<div class="epkb-ai-sr-ai-answer-text">';
				$html .= '<p>' . sprintf( esc_html__( 'Based on your search for "%s", here\'s what your team should focus on:', 'echo-knowledge-base' ), esc_html( $query_display ) ) . '</p>';
				$html .= '<p>' . esc_html__( 'Use this quick summary to keep the onboarding process on track:', 'echo-knowledge-base' ) . '</p>';
				$html .= '<ul>';
				$html .= '<li><strong>' . esc_html__( 'Confirm employee details', 'echo-knowledge-base' ) . ':</strong> ' . esc_html__( 'Verify personal information, tax forms, and direct deposit preferences.', 'echo-knowledge-base' ) . '</li>';
				$html .= '<li><strong>' . esc_html__( 'Assign onboarding tasks', 'echo-knowledge-base' ) . ':</strong> ' . esc_html__( 'Share the new hire checklist and track completion dates.', 'echo-knowledge-base' ) . '</li>';
				$html .= '<li><strong>' . esc_html__( 'Communicate key policies', 'echo-knowledge-base' ) . ':</strong> ' . esc_html__( 'Highlight remote work rules, security standards, and time-off guidelines.', 'echo-knowledge-base' ) . '</li>';
				$html .= '<li><strong>' . esc_html__( 'Plan first-week touchpoints', 'echo-knowledge-base' ) . ':</strong> ' . esc_html__( 'Schedule introductions with HR, IT, and the hiring manager.', 'echo-knowledge-base' ) . '</li>';
				$html .= '</ul>';
				$html .= '<p>' . esc_html__( 'Share these steps with the employee and loop in HR support if questions come up.', 'echo-knowledge-base' ) . '</p>';
				$html .= '</div>';

				// Create database record even for demo data so chat_id is available for record-feedback and submit-contact-support
				$demo_answer = wp_strip_all_tags( $html );
				$session_id = EPKB_AI_Security::get_or_create_session();
				$language = EPKB_Language_Utilities::detect_current_language();
				$mode = EPKB_AI_Utilities::is_ai_search_advanced_enabled() ? 'advanced_search' : 'search';

				$conversation = new EPKB_AI_Conversation_Model( array(
					'user_id'    => get_current_user_id(),
					'mode'       => $mode,
					'chat_id'    => 'search_' . EPKB_AI_Utilities::generate_uuid_v4(),
					'session_id' => $session_id,
					'widget_id'  => 1,
					'language'   => $language['locale'],
					'ip'         => EPKB_AI_Utilities::get_hashed_ip()
				) );

				$conversation->add_message( 'user', $query );
				$conversation->add_message( 'assistant', $demo_answer, array( 'demo_mode' => true ) );

				$repository = new EPKB_AI_Messages_DB();
				$repository->save_conversation( $conversation );

				return array(
					'has_content' => true,
					'html' => EPKB_AI_Search_Results_Handler::get_section_wrapper( $html, 'ai-answer', $section_name ),
					'data' => array(
						'query' => $query,
						'chat_id' => $conversation->get_chat_id(),
						'source' => 'demo'
					)
				);

			case 'matching-articles':
				$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_matching_articles_name' );

				$articles = array(
					array(
						'id' => 101,
						'title' => __( 'Complete the New Hire Checklist', 'echo-knowledge-base' ),
						'url' => '#',
						'excerpt' => __( 'Step-by-step instructions and deadlines for onboarding paperwork.', 'echo-knowledge-base' )
					),
					array(
						'id' => 102,
						'title' => __( 'Set Up Payroll and Benefits Access', 'echo-knowledge-base' ),
						'url' => '#',
						'excerpt' => __( 'How to enter direct deposit, tax elections, and benefits selections.', 'echo-knowledge-base' )
					),
					array(
						'id' => 103,
						'title' => __( 'Prepare Orientation Day Resources', 'echo-knowledge-base' ),
						'url' => '#',
						'excerpt' => __( 'Checklist for equipment requests, system access, and welcome materials.', 'echo-knowledge-base' )
					)
				);

				$html = '<ul class="epkb-ai-sr-articles-list">';
				foreach ( $articles as $article ) {
					$html .= '<li class="epkb-ai-sr-article-item">';
					$html .= '<a href="' . esc_url( $article['url'] ) . '" class="epkb-ai-sr-article-link" data-kb-article-id="' . esc_attr( $article['id'] ) . '">';
					$html .= '<h4 class="epkb-ai-sr-article-title">' . esc_html( $article['title'] ) . '</h4>';
					$html .= '<p class="epkb-ai-sr-article-excerpt">' . esc_html( $article['excerpt'] ) . '</p>';
					$html .= '</a>';
					$html .= '</li>';
				}
				$html .= '</ul>';

				return array(
					'has_content' => true,
					'html' => EPKB_AI_Search_Results_Handler::get_section_wrapper( $html, 'matching-articles', $section_name ),
					'data' => array(
						'articles' => $articles,
						'count' => count( $articles )
					)
				);

			case 'contact-us':
				$button_text = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_contact_support_button_text', 'Contact Support' );
				$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_contact_us_name', 'Contact Us' );

				$html  = '<div class="epkb-ai-sr-contact-box">';
				$html .= '<p class="epkb-ai-sr-contact-message">' . esc_html__( 'Couldn\'t find what you were looking for?', 'echo-knowledge-base' ) . '</p>';
				$html .= '<p class="epkb-ai-sr-contact-description">' . esc_html__( 'Our support team is here to help. Reach out and we\'ll respond as soon as possible.', 'echo-knowledge-base' ) . '</p>';

				// Hidden form fields
				$html .= '<div class="epkb-ai-sr-contact-form" style="display: none;">';
				$html .= '<div class="epkb-ai-sr-contact-field">';
				$html .= '<label for="epkb-ai-sr-contact-name">' . esc_html__( 'Name', 'echo-knowledge-base' ) . '</label>';
				$html .= '<input type="text" id="epkb-ai-sr-contact-name" class="epkb-ai-sr-contact-input" required />';
				$html .= '</div>';
				$html .= '<div class="epkb-ai-sr-contact-field">';
				$html .= '<label for="epkb-ai-sr-contact-email">' . esc_html__( 'Email', 'echo-knowledge-base' ) . '</label>';
				$html .= '<input type="email" id="epkb-ai-sr-contact-email" class="epkb-ai-sr-contact-input" required />';
				$html .= '</div>';
				$html .= '</div>';

				$html .= '<button class="epkb-ai-sr-contact-button">' . esc_html( $button_text ) . '</button>';
				$html .= '</div>';

				return array(
					'has_content' => true,
					'html' => EPKB_AI_Search_Results_Handler::get_section_wrapper( $html, 'contact-us', $section_name ),
					'data' => array(
						'query' => $query
					)
				);

			case 'feedback':
				$section_name = EPKB_AI_Config_Specs::get_ai_config_value( 'ai_search_results_feedback_name' );

				$html  = '<div class="epkb-ai-sr-feedback-widget">';
				$html .= '<p class="epkb-ai-sr-feedback-question">' . esc_html__( 'Was this answer helpful?', 'echo-knowledge-base' ) . '</p>';
				$html .= '<div class="epkb-ai-sr-feedback-buttons">';
				$html .= '<button class="epkb-ai-sr-feedback-btn epkb-ai-sr-feedback-btn--up" data-vote="up"><span class="epkbfa epkbfa-thumbs-up"></span> ' . esc_html__( 'Yes', 'echo-knowledge-base' ) . '</button>';
				$html .= '<button class="epkb-ai-sr-feedback-btn epkb-ai-sr-feedback-btn--down" data-vote="down"><span class="epkbfa epkbfa-thumbs-down"></span> ' . esc_html__( 'No', 'echo-knowledge-base' ) . '</button>';
				$html .= '</div>';
				$html .= '</div>';

				return array(
					'has_content' => true,
					'html' => EPKB_AI_Search_Results_Handler::get_section_wrapper( $html, 'feedback', $section_name ),
					'data' => array()
				);

			default:
				// For any unknown sections, return empty content
				return array( 'has_content' => false, 'html' => '', 'data' => array() );
		}
	}
}
