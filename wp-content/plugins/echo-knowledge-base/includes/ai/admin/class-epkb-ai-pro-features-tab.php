<?php defined( 'ABSPATH' ) || exit();

/**
 * Display AI PRO Features tab showcasing premium features
 */
class EPKB_AI_PRO_Features_Tab {

	/**
	 * Get the configuration for the PRO Features tab
	 *
	 * @return array
	 * @noinspection PhpUnused
	 */
	public static function get_tab_config() {
		return array(
			'tab_id' => 'pro-features',
			'title' => __( 'PRO Features', 'echo-knowledge-base' ),
			'type' => 'pro_showcase',
			'header' => array(
				'title' => __( 'Welcome to Echo Knowledge Base Pro', 'echo-knowledge-base' ),
				'subtitle' => array(
					__( 'We\'re grateful you\'re using Echo Knowledge Base and hope it\'s been valuable for your team.', 'echo-knowledge-base' ),
					__( 'Upgrading to Pro unlocks powerful features that enhance your knowledge base experience.', 'echo-knowledge-base' ),
					__( 'Your support also helps us continue developing and improving the plugin for our amazing community.', 'echo-knowledge-base' )
				),
				'status' => self::get_pro_status()
			),
			'discount_coupon' => self::get_discount_coupon(),
			'features' => self::get_pro_features(),
			'cta' => array(
				//'title' => __( 'Ready to Unlock All PRO Features?', 'echo-knowledge-base' ),
				'subtitle' => __( 'Join thousands of businesses using AI to provide instant, accurate support', 'echo-knowledge-base' ),
				'button_text' => __( 'Get PRO Now', 'echo-knowledge-base' ),
				'button_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/ai-features/',
				'button_secondary_text' => __( 'View Pricing', 'echo-knowledge-base' ),
				'button_secondary_url' => 'https://www.echoknowledgebase.com/ai-features-pricing/#pricing',
				'discount_text' => __( '🎉 Save 20% with annual billing', 'echo-knowledge-base' ),
				'guarantee' => __( '30-day money-back guarantee', 'echo-knowledge-base' )
			)
		);
	}

	/**
	 * Get the number of installed Echo KB add-ons
	 *
	 * @return int
	 */
	private static function get_addon_count() {
		$addon_count = 0;
		
		// Check for each known Echo KB add-on
		$addons = array(
			'Echo_Advanced_Search',
			'Echo_Elegant_Layouts',
			'Echo_Article_Rating_And_Feedback',
			'Echo_KB_Articles_Setup',
			'Echo_Knowledge_Base_CPT',
			'Echo_Widgets_KB',
			'Echo_Access_Manager',
			'Echo_Links_Editor',
			'Echo_KB_Export_Import',
			'Echo_Advanced_Config',
			'EPKB_AI_FEATURES_VERSION'
		);
		
		foreach ( $addons as $addon_class ) {
			if ( class_exists( $addon_class ) || defined( $addon_class ) ) {
				$addon_count++;
			}
		}
		
		return $addon_count;
	}

	/**
	 * Get discount coupon based on number of add-ons
	 *
	 * @return array
	 */
	private static function get_discount_coupon() {
		$addon_count = self::get_addon_count();
		$current_date = date('Y-m-d');
		$expiry_date = date('Y-m-d', strtotime('September 15'));
		
		if ( $addon_count == 0 ) {
			return array(
				'discount_percentage' => 15,
				'coupon_code' => 'AI_PRO_BASIC_15',
				'title' => __( '🎉 Limited Time: 15% OFF for New Users!', 'echo-knowledge-base' ),
				'subtitle' => __( 'Start your AI journey with our special promotional discount', 'echo-knowledge-base' ),
				'badge_text' => __( 'NEW USER DISCOUNT', 'echo-knowledge-base' ),
				'addon_count' => $addon_count
			);
		} elseif ( $addon_count == 1 ) {
			return array(
				'discount_percentage' => 20,
				'coupon_code' => 'AI_PRO_ADV_20',
				'title' => __( '🎉 Exclusive: 20% OFF for Valued Customers!', 'echo-knowledge-base' ),
				'subtitle' => __( 'Thank you for being our customer! Enjoy this special discount', 'echo-knowledge-base' ),
				'badge_text' => __( 'CUSTOMER APPRECIATION', 'echo-knowledge-base' ),
				'addon_count' => $addon_count
			);
		} elseif ( $addon_count >= 2 && $addon_count <= 3 ) {
			return array(
				'discount_percentage' => 30,
				'coupon_code' => 'AI_PRO_VIP_30',
				'title' => __( '🎉 VIP Offer: 30% OFF for Premium Members!', 'echo-knowledge-base' ),
				'subtitle' => __( 'As a premium member, you deserve our best discount', 'echo-knowledge-base' ),
				'badge_text' => __( 'VIP MEMBER DISCOUNT', 'echo-knowledge-base' ),
				'addon_count' => $addon_count
			);
		} else {
			return array(
				'discount_percentage' => 40,
				'coupon_code' => 'AI_PRO_ELITE_40',
				'title' => __( '🎉 Elite Special: 40% OFF for Power Users!', 'echo-knowledge-base' ),
				'subtitle' => __( 'Our most exclusive discount for our most valued partners', 'echo-knowledge-base' ),
				'badge_text' => __( 'ELITE PARTNER DISCOUNT', 'echo-knowledge-base' ),
				'addon_count' => $addon_count
			);
		}
	}

	/**
	 * Get list of PRO features to showcase
	 *
	 * @return array
	 */
	private static function get_pro_features() {
		return array(
			array(
				'id'          => 'advanced-training',
			'title'       => __( 'Advanced Training Data Sources', 'echo-knowledge-base' ),
			'description' => __( 'Train your AI using posts, pages, custom post types, and private notes. Build a comprehensive knowledge base that understands all your content.', 'echo-knowledge-base' ),
			'icon'        => 'epkbfa epkbfa-database',
			'icon_color'  => '#4A90E2',
			'benefits'    => array(
				__( 'WordPress posts and pages (Home page, product pages etc.).', 'echo-knowledge-base' ),
				__( 'Custom post types (e.g. Products, Events, Courses).', 'echo-knowledge-base' ),
				__( 'Internal notes created for reference, available to AI without being published publicly.', 'echo-knowledge-base' ),
               ),

				'image' => esc_url( Echo_Knowledge_Base::$plugin_url . 'img/ad/ai-pro-features-training-data.png' )
			),
			array(
				'id' => 'content-gaps-analysis',
				'title' => __( 'Content Gaps Analysis', 'echo-knowledge-base' ),
				'description' => __( 'AI identifies missing information and unanswered questions in your articles. Discover what topics need more coverage and what questions your users might ask that aren\'t addressed.', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-puzzle-piece',
				'icon_color' => '#F39C12',
				'benefits' => array(
					__( 'Identifies unanswered user questions', 'echo-knowledge-base' ),
					__( 'Detects missing or underdeveloped topics', 'echo-knowledge-base' ),
					__( 'Provides actionable recommendations for improvement', 'echo-knowledge-base' )
				),
				'image' => 'https://www.echoknowledgebase.com/wp-content/uploads/2025/10/Content-Gaps-Analysis.jpg'
			),
			array(
				'id' => 'tag-suggestions',
				'title' => __( 'AI-Powered Tag Suggestions', 'echo-knowledge-base' ),
				'description' => __( 'AI analyzes your articles and intelligently suggests both broad and specific tags, helping you organize content more effectively and improve discoverability.', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-tags',
				'icon_color' => '#9B59B6',
				'benefits' => array(
					__( 'Automatic tag analysis for each article', 'echo-knowledge-base' ),
					__( 'Broad category tags for general topics', 'echo-knowledge-base' ),
					__( 'Specific tags for detailed content areas', 'echo-knowledge-base' )
				),
				'image' => 'https://www.echoknowledgebase.com/wp-content/uploads/2025/10/AI-Powered-Tag-Suggestions.jpg'
			),
			array(
				'id' => 'ai-advanced-search',
				'title' => __( 'AI Advanced Search', 'echo-knowledge-base' ),
				'description' => __( 'Display comprehensive AI search results in an organized, multi-section layout that can be embedded anywhere via shortcode.', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-search',
				'icon_color' => '#16A085',
				'benefits' => array(
					__( 'AI-generated answer with relevant source citations', 'echo-knowledge-base' ),
					__( 'Recommended articles organized in column layouts', 'echo-knowledge-base' ),
					__( 'Related topics and categories for deeper exploration', 'echo-knowledge-base' ),
					__( 'Flexible shortcode to embed the search results page anywhere', 'echo-knowledge-base' )
				),
				'image' => 'https://www.echoknowledgebase.com/wp-content/uploads/2025/10/Feature-Advanced-Search-Results.png' 
			),
			array(
				'id' => 'email-notifications',
				'title' => __( 'Smart Email Notifications', 'echo-knowledge-base' ),
				'description' => __( 'Stay informed with automated daily summaries of AI Chat and Search activity, delivered straight to your inbox.', 'echo-knowledge-base' ),
				'icon' => 'epkbfa epkbfa-envelope',
				'icon_color' => '#E74C3C',
				'benefits' => array(
					__( 'Daily email reports at your chosen time', 'echo-knowledge-base' ),
					__( 'Customizable recipient and subject line', 'echo-knowledge-base' ),
					__( 'Includes AI Chat and Search query titles', 'echo-knowledge-base' )
				),
				'image' => esc_url( Echo_Knowledge_Base::$plugin_url . 'img/ad/ai-pro-features-email-notifications.png' )
				//'badge' => __( 'upcoming feature', 'echo-knowledge-base' ),
				//'badge_type' => 'coming-soon'
			),
			// array(
			// 	'id' => 'articles-analysis',
			// 	'title' => __( 'AI-Powered Article Analysis', 'echo-knowledge-base' ),
			// 	'description' => __( 'Get intelligent insights about your content. AI analyzes article quality, readability, and suggests improvements to maximize user engagement.', 'echo-knowledge-base' ),
			// 	'icon' => 'epkbfa epkbfa-chart-line',
			// 	'icon_color' => '#27AE60',
			// 	'benefits' => array(
			// 		__( 'Content quality scoring', 'echo-knowledge-base' ),
			// 		__( 'Readability analysis', 'echo-knowledge-base' ),
			// 		__( 'SEO optimization tips', 'echo-knowledge-base' ),
			// 	),
			// 	'badge' => __( 'upcoming feature', 'echo-knowledge-base' ),
			// 	'badge_type' => 'coming-soon'
			// ),
			// array(
			// 	'id' => 'glossary-terms',
			// 	'title' => __( 'AI-Generated Glossary Terms', 'echo-knowledge-base' ),
			// 	'description' => __( 'Automatically generate and manage glossary terms with AI. Intelligently prioritize technical terms, acronyms, and industry-specific language in search results and content ordering.', 'echo-knowledge-base' ),
			// 	'icon' => 'epkbfa epkbfa-book',
			// 	'icon_color' => '#9B59B6',
			// 	'benefits' => array(
			// 		__( 'Auto-generate glossary definitions', 'echo-knowledge-base' ),
			// 		__( 'Smart term prioritization', 'echo-knowledge-base' ),
			// 		__( 'Context-aware term ordering', 'echo-knowledge-base' )
			// 	),
			// 	'badge' => __( 'upcoming feature', 'echo-knowledge-base' ),
			// 	'badge_type' => 'coming-soon'
			// ),
			// array(
			// 	'id' => 'enhanced-search',
			// 	'title' => __( 'Enhanced AI Search Results', 'echo-knowledge-base' ),
			// 	'description' => __( 'Deliver rich, comprehensive search results with visual aids. Include diagrams, related articles, glossary terms, and more to provide complete answers.', 'echo-knowledge-base' ),
			// 	'icon' => 'epkbfa epkbfa-search-plus',
			// 	'icon_color' => '#F39C12',
			// 	'benefits' => array(
			// 		__( 'Visual diagrams and charts', 'echo-knowledge-base' ),
			// 		__( 'Related articles suggestions', 'echo-knowledge-base' ),
			// 		__( 'Integrated glossary terms', 'echo-knowledge-base' )
			// 	),
			// 	'badge' => __( 'upcoming feature', 'echo-knowledge-base' ),
			// 	'badge_type' => 'coming-soon'
			// ),
			// array(
			// 	'id' => 'advanced-features',
			// 	'title' => __( 'Advanced AI Capabilities', 'echo-knowledge-base' ),
			// 	'description' => __( 'Unlock powerful features including PDF search, human agent handoff, and intelligent auto-suggestions for a complete support experience.', 'echo-knowledge-base' ),
			// 	'icon' => 'epkbfa epkbfa-rocket',
			// 	'icon_color' => '#E67E22',
			// 	'benefits' => array(
			// 		__( 'PDF document search', 'echo-knowledge-base' ),
			// 		__( 'Human agent handoff', 'echo-knowledge-base' ),
			// 		__( 'Smart auto-suggestions', 'echo-knowledge-base' ),
			// 		__( 'Multi-language support', 'echo-knowledge-base' )
			// 	),
			// 	'badge' => __( 'upcoming feature', 'echo-knowledge-base' ),
			// 	'badge_type' => 'coming-soon'
			// )
		);
	}

	/**
	 * Get PRO status information
	 *
	 * @return array
	 */
	private static function get_pro_status() {
		$has_pro = defined( 'EPKB_AI_FEATURES_VERSION' );
		
		if ( ! $has_pro ) {
			return array(
				'has_pro' => false,
				'status_text' => '',
				'status_class' => '',
				'features_available' => 0,
				'features_total' => 0
			);
		}
		
		return array(
			'has_pro' => true,
			'status_text' => __( 'PRO Active', 'echo-knowledge-base' ),
			'status_class' => 'status-active',
			'features_available' => 4,
			'features_total' => 4
		);
	}

}