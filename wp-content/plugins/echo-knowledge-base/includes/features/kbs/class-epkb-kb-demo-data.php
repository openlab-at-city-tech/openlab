<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Demo KB data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Demo_Data {

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
				return esc_html__( 'Strategies for promoting products and reaching customers.', 'echo-knowledge-base' );
			case esc_html__( 'Operations and Logistics', 'echo-knowledge-base' ):
				return esc_html__( 'Streamline processes for efficient business operations.', 'echo-knowledge-base' );
			case esc_html__( 'Human Resources', 'echo-knowledge-base' ):
				return esc_html__( 'Policies, procedures, and support for effective workforce management.', 'echo-knowledge-base' );
			case esc_html__( 'Finance and Expenses', 'echo-knowledge-base' ):
				return esc_html__( 'Efficiently manage finances, track expenditure accurately, and optimize budgets.', 'echo-knowledge-base' );
			case esc_html__( 'IT Support', 'echo-knowledge-base' ):
				return esc_html__( 'Technical assistance and solutions for digital infrastructure.', 'echo-knowledge-base' );
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
			return;
		}

		$categories_seq_meta = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( empty( $categories_seq_meta ) ) {
			return;
		}

		// get top categories
		$top_categories = [];
		foreach( $categories_seq_meta as $category_id => $sub_categories ) {
			if ( ! empty( $articles_seq_meta[$category_id][0] ) ) {
				$top_categories[$category_id] = $articles_seq_meta[$category_id][0];
			}
		}

		// check that sub-categories match demo data; are they tab or non-tab top categories?
		if ( array_diff( array_values( $top_categories ), self::get_tab_top_categories() ) ) {

			// we have non-Tab categories; are they non-tab categories or user data?
			if ( array_diff( array_values( $top_categories ), self::get_non_tab_top_categories() ) ) {
				return; // unknown top categories

			// we have non-tab top categories so add tab top categories
			} else if ( $kb_main_page_layout == EPKB_Layout::TABS_LAYOUT ) {

				// add tab top categories
				$tab_category_name_1 = self::get_tab_top_categories()[0];
				$tab_category_name_2 = self::get_tab_top_categories()[1];
				$tab_category_name_3 = self::get_tab_top_categories()[2];

				$tab_category_id = self::create_sample_category( $kb_id, $tab_category_name_1, null, true );
				if ( empty( $tab_category_id ) ) {
					return;
				}
				$tab_category_id_2 = self::create_sample_category( $kb_id, $tab_category_name_2, null, true );
				if ( empty( $tab_category_id_2 ) ) {
					return;
				}
				$tab_category_id_3 = self::create_sample_category( $kb_id, $tab_category_name_3, null, true );
				if ( empty( $tab_category_id_3 ) ) {
					return;
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
				return; // non-tab layout already has non-tab top categories
			}

		// we found top Tab categories and Tab layout
		} else if ( $kb_main_page_layout == EPKB_Layout::TABS_LAYOUT ) {
			return; // Tab layout already has tab top categories

		// we found top Tab categories but non-Tab layout
		} else {

			// remove top tabs from categories
			$top_categories_ids = array_keys( $top_categories );
			if ( empty( $categories_seq_meta[$top_categories_ids[0]] ) ) {
				return;
			}

			foreach( $top_categories as $top_category_id => $top_category_name ) {
				wp_delete_term( $top_category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), array( 'parent' => 0 ) );
			}

			// remove top tabs categories in categories and articles sequences
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

	    // Save the categories and articles sequences
	    EPKB_Utilities::save_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, $articles_seq_meta );
	    EPKB_Utilities::save_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, $categories_seq_meta );
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

	public static function get_sample_post_content() {  // TODO translate

		$demo_img = Echo_Knowledge_Base::$plugin_url.'img/guy-on-laptop.jpg';
		$youtube_vid = '<iframe width="560" height="315" src="https://www.youtube.com/embed/gOLT-IDT3UY?si=amjYFxs-Cf_CHqFM" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';

		return "
		    <h2 style='padding-top: 20px;'>Welcome to Echo Knowledge Base!</h2>
		    <p>We're thrilled that you've chosen our plugin to enhance your Knowledge Base. We\'re here to assist you in making your Knowledge Base exceptional. If you need any help or have questions, just let us know!</p>
		
		    <h3 style='padding-top: 20px;'>Create Articles Just Like You Create Posts</h3>
		    <p>Add instructional videos or product demos. Example of embedded video:</p>
		    $youtube_vid
		    
		    
		    
		    <p>Enhance your articles with visuals. Example of an image:</p>
		    <img src=\"$demo_img\" alt=\"Sample Image\" width=\"500\">
		
		    <p>Effortlessly embed PDFs and other media using your page builder or blocks.</p>
		
		    <h3 style='padding-top: 20px;'>Main Features</h3>
		    <p>Our plugin includes the following features to make your Knowledge Base stand out:</p>
		    <ul>
		        <li>Fast search bar with listed results</li>
		        <li>Five levels of hierarchical documentation</li>
		        <li>Article view counter with Popular and Recent Articles</li>
		        <li>Frequently Asked Questions (FAQ) Module and shortcode.</li>
		        <li>Customizable Category Archive Page</li>
		        <li>AI Content Writing</li>
		        <li>Organize articles and categories alphabetically, chronologically, or in any custom order with drag and drop.</li>
		        <li>Optimized for the best SEO results to boost online visibility</li>
		        <li>Analytics to track your Knowledge Base usage</li>
		        <li>Supports RTL languages, WCAG accessibility standards, and works with WPML and Polylang</li>
		    </ul>
		
		    <h3 style='padding-top: 20px;'>PRO Features</h3>
		    <p>Expand your Knowledge Base using our cost-effective add-ons:</p>
		    <ul>
		        <li>Control access and permissions based on groups, WordPress users and roles, and custom roles.</li>
		        <li>Manage public and private articles and control who can read, write, and edit articles.</li>
		        <li>Create unlimited Knowledge Bases with separate articles, categories, tags, and more.</li>
		        <li>Choose from Sidebar and Grid layouts.</li>
		        <li>Import and export articles and categories using CSV and XML</li>
		        <li>Add article voting, a feedback form, and learn from analytics.</li>
		        <li>More widgets and shortcodes for categories, popular articles, and the search bar.</li>
		        <li>Replace articles with links to PDFs, external docs, video links, and more.</li>
		        <li>Utilize Advanced search analytics to identify popular and empty searches.</li>
		    </ul>
		
		    <h3 style='padding-top: 20px;'>Need Help or Looking for a Feature?</h3>
		    <p>Please don't hesitate to contact us <a href=\"https://www.echoknowledgebase.com/pre-sale-question/\" target='_blank'>here</a>.</p>
		    <p>We're always eager to help and are open to suggestions for new features.</p>
		";
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

		$faq_id1 = self::create_one_faq( 'What are the steps to submit a purchase order?',
			'<p>' . esc_html__( 'Here\'s the process to submit a purchase order:', 'echo-knowledge-base' ) . '<br>
					    <ol>
					        <li>' . esc_html__( 'Fill out the purchase order form.', 'echo-knowledge-base' ) . '</li>
					        <li>' . esc_html__( 'Obtain the necessary approvals from your manager or department head.', 'echo-knowledge-base' ) . '</li>
					        <li>' . esc_html__( 'Submit the approved purchase order to the procurement team.', 'echo-knowledge-base' ) . '</li>
					    </ol>
			        </p>' );
		$faq_id2 = self::create_one_faq( 'Where can I find templates for customer presentations?',
					"<p>" . esc_html__( 'We have a library of customer presentation templates within the Sales & Marketing section of our knowledge base.', 'echo-knowledge-base' ) . "
				    </p>" );
		$faq_id3 = self::create_one_faq( 'What is the process for requesting time off?',
					"<ol>
				        <li>" . esc_html__( 'Access our Time Off Request form.', 'echo-knowledge-base' ) . "</li>
				        <li>" . esc_html__( "Fill out the form, including your desired dates and any relevant notes.", 'echo-knowledge-base' ) . "</li>
				        <li>" . esc_html__( 'Submit the form to your manager for approval.', 'echo-knowledge-base' ) . "</li>
                    </ol>");

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
}
