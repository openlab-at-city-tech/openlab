<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display New Features page
 *
 * @copyright   Copyright (C) 2019, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Add_Ons_Features {

	/**
	 * Filter last features array to add latest
	 * @return array
	 */
	private static function features_list() {
		$features = array();

		$features['2024.05.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Article Icons On/Off', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Toggle to display or hide article icons.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2024/05/no-article-icons-example.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2024.04.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'New Designs for Layouts', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Showcase of designs for Basic, Tabs, Categories, Classic and Drill Down layouts shown in Setup Wizard.', 'echo-knowledge-base') . '</p>',
			'image'             => Echo_Knowledge_Base::$plugin_url . 'img/' . 'setup-wizard/step-4/cat-art-module-basic-creative.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2024.03.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'New Category Archive Pages', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'A new Category Archive Page design featuring optional sidebars, search functionality, various designs, and more.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2024/03/new-category-archive-page.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/category-archive-page/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);
		$features['2024.01.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'FAQs and Presets', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Display Frequently Asked Questions using either the FAQ Module or the FAQ shortcode.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2024/02/FAQ-Example.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/faqs-shortcode/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);
		$features['2023.11.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Resource Links Feature', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Show resource links, such as links to a contact form, forum, and others on your KB Main Page.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2023/12/resource-links-feature.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/resource-links/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);
		$features['2023.10.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Popular Articles Feature', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Show popular articles on your KB Main Page, together with recently added and updated articles.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2023/12/popular-articles.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/articles-list/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);
		$features['2023.08.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Modular Main Page', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Explore new layouts that allow for customizable placement of the search box, FAQs, and new and updated articles.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2023/08/featured-screenshot-module-layout.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/modular-layout/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);
		$features['2023.07.15'] = array(
			'plugin'            => esc_html__( 'Widgets', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Popular Articles Widget/Shortcode', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'The widget and shortcode displays a list of the most popular articles based on article views.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2023/07/Popular-Articles.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/popular-articles-shortcode/',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);
		$features['2023.06.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Article Views Counter', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Track the number of times articles are viewed and display a view counter on article pages and in analytics. '.
			                                           ' Show visitors the most popular articles using widgets and shortcodes (Widgets add-on required). Analyze the least popular articles for improvement or replacement..', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2023/06/featured-screenshots-article-views.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/article-views-counter/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2023.03.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'AI Assisted Writing', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'AI assistance is available to help you write your articles.', 'echo-knowledge-base') . '</p>',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/ai-help-sidebar/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2023.02.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Draft Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Temporary hide categories on the KB Main Page or sidebars. It is not intended to prevent access to KB content.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2023/01/featured-screenshots-draft-categories-.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/hiding-categories/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2023.01.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'FAQs - Shortcode', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Use this shortcode in a page to show articles in FAQ block style.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2023/01/featured-screenshots-FAQs-shortcode-.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/faqs-shortcode',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.10.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Improve CSS loading speed', 'echo-knowledge-base'),
			'description'       => '
                                    <p>' .esc_html__( 'Output CSS specific elements', 'echo-knowledge-base' ) . '</p>
                                    <p>' .esc_html__( 'Reduce CSS file sizes', 'echo-knowledge-base' ) . '</p>
                                    <p>' .esc_html__( 'Refactor CSS output', 'echo-knowledge-base' ) . '</p>
                                    <p>' .esc_html__( 'Improve Mobile CSS', 'echo-knowledge-base' ) . '</p>
                                    
                                    ',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2022/10/Featured-improved-css-.jpg',
			//'learn_more_url'    => 'https://wordpress.org/plugins/help-dialog/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.08.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Basic Settings UI', 'echo-knowledge-base'),
			'description'       => '<p>' .esc_html__( 'We are introducing basic settings to allow users to quickly configure a subset of KB features and colors, accessible on the left sidebar on this page. '.
			                                          'The full Visual Editor is still available under the tab called "Full Editor."', 'echo-knowledge-base' ) . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2022/08/kb-new-settings-ui.jpg',
			//'learn_more_url'    => 'https://wordpress.org/plugins/help-dialog/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.06.01'] = array(
			'plugin'            => esc_html__( 'Help Dialog', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Help dialog Plugin', 'echo-knowledge-base'),
			'description'       => '<p>' .esc_html__( 'Offer better customer support and generate more leads and sales. Help Dialog shows FAQs and keywords search as well as article previews and contact form.', 'echo-knowledge-base' ) . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2022/06/featured-help-dialog.jpg',
			'learn_more_url'    => 'https://wordpress.org/plugins/help-dialog/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.05.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'New Backend Mode for the Editor', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'KB Editor is now available on the backend as well.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/11/visual-editor-configuration.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.04.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'New Navigation Sidebar for Articles', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'You can either use the frontend Editor or Setup Wizard to set up a Navigation sidebar for articles.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/11/setup-wizard-step-3.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/article-sidebars/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.02.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Convert Posts and CPTs into Articles', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Convert blog and other posts as well as Custom Post Types into KB Articles.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2022/03/Featured-convert-posts-1.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2022.01.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Articles Index Directory - Shortcode', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Use this shortcode in a page to list all articles alphabetically in three columns, grouped by first letter.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2022/02/article-index-directory-feature.png',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/shortcode-articles-index-directory/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.12.03'] = array(
			'plugin'            => esc_html__( 'Articles Import and Export', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Migrate, Copy, Import and Export KB Content.', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Import and export articles and their content, comments, categories, tags, and attachments. Migrate and copy articles between KBs. Edit articles outside of WordPress.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2020/08/KB-Import-Export-Banner.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2021.11.02'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Private Articles', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( "KB can now host private articles as well. This is not a substitute for full access control with Access Manager.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/11/Featured-Private-Articles.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.10.02'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Control TOC Scroll Speed', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( "Configure TOC scroll speed as immediate or slow scroll.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/11/Featured-TOC-Scroll.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.09.03'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Access Control to Admin Screens', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( "Choose what Authors and Editors can change and if they can view analytics.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/09/featured-screenshots-new-access.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.09.02'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'On/Off Option for Author, Date, Category', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( "On the Category Archive Page, the author, date and category can be turned on or off.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/09/category-archive-page-features.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.08.02'] = array(
			'plugin'            => esc_html__( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Filter HTML/CSS Keywords', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( "Words such as 'family', 'data', and 'option' could be hidden in the article's HTML/CSS. Exclude them from the user search unless they are inside the article content.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/08/Featured-Code-Improvements.jpg',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		/* TODO $features['2021.03.01'] = array(
			'plugin'            => esc_html__( 'Article Features BETA', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Article Rating and Email Notifications', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Current features: article rating with analytics, and email notifications (beta) for articles created and updated.', 'echo-knowledge-base') . '</p>' .
			                       ( EPKB_Utilities::is_elegant_layouts_enabled() || EPKB_Utilities::is_article_rating_enabled() || EPKB_Utilities::is_link_editor_enabled() ?
			                       '<p>' . esc_html__( 'If you do not have the new Article Features add-on in your bundle, you can get it for free.', 'echo-knowledge-base') .
			                       ' <a href="https://www.echoknowledgebase.com/documentation/bundle-users-get-article-features-for-free" target="_blank">' . esc_html__( 'Upgrade here', 'echo-knowledge-base') . '</a></p>' : '' ),
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/01/featured-screenshots-print-button.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-features/',
			'button_name'       => esc_html__( 'Learn More', 'echo-knowledge-base'),
			'plugin-type'       => 'add-on',
			'type'              => 'new-addon'
		); */

		$features['2021.05.02'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Typography', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Font size, family, and weight can now be configured for article title, article names, category names, TOC, breadcrumbs, back navigation, and search title.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-Typography-1.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/typography-font-family-size-weight/',
			'button_name'       => esc_html__( 'Learn More', 'echo-knowledge-base'),
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.05.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Custom Icons for Sub-Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Categories Focused Layout can now have custom icons for its sub-categories.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-Custom-icons-Category-focused-1.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.04.02'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Support for RTL', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'RTL (Right To Left) is a locale property indicating that text is written from right to left. This Knowledge Base fully supports RTL for both admin screens and frontend pages.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-RTL-1.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.04.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Support for WCAG accessibility', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'KB has improved web accessibility so that people with visual impairments using screen readers can use it effectively.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/05/Featured-WCAG-1.jpg',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.03.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Simple Search Analytics', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Show a basic count of searches with articles found and with no results.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/03/new-features-basic-search-analytics.jpg',
			'learn_more_url'    => admin_url('edit.php?post_type=epkb_post_type_1&page=epkb-plugin-analytics'),
			'button_name'       => esc_html__( 'Try Now!', 'echo-knowledge-base'),
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2021.02.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Print Article', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Display a print button so that users can easily print the article and save it as a PDF file. The printed article excludes the redundant site header and footer.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/01/featured-screenshots-print-button.jpg',
			'learn_more_url'    => ( epkb_get_instance()->kb_config_obj->get_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'print_button_enable', null ) == 'on' ) ? EPKB_Need_Help_Features::get_settings_link( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'settings', 'labels', '', 'print_button' ) : 'https://www.echoknowledgebase.com/documentation/print-button/',
			'button_name'       => esc_html__( 'Try Now!', 'echo-knowledge-base'),
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

	   $features['2021.01.02'] = array(
		   'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
		   'title'             => esc_html__( 'Design Article Header', 'echo-knowledge-base'),
		   'description'       => '<p>' . esc_html__( 'Change the order of elements in the article header. Move them up, down, left, or right. This applies to the article title, author, dates, print button, and breadcrumbs.', 'echo-knowledge-base') . '</p>',
		   'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2021/01/header-design.jpg',
		   'learn_more_url'    => EPKB_Editor_Utilities::get_one_editor_url( 'article_page', 'article_content' ),
		   'button_name'       => esc_html__( 'Try Now!', 'echo-knowledge-base'),
		   'plugin-type'       => 'core',
		   'type'              => 'new-feature'
	   );

		$features['2020.11.01'] = array(
			'plugin'            => esc_html__( 'Knowledge Base Visual Editor', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Edit KB Pages', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Change the style, colors, and features using the front-end Editor.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/11/front-end-editor.jpg',
			'learn_more_url'    => EPKB_Editor_Utilities::get_one_editor_url( 'main_page' ),
			'plugin-type'       => 'core',
			'type'              => 'new-feature',
			'button_name'       => esc_html__( 'Try Now', 'echo-knowledge-base'),
		);

		$features['2020.08.09'] = array(
			'plugin'            => esc_html__( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Advanced Search Shortcode for One or More KBs', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Add Advanced Search box to any page like the Contact Us form. Search across multiple KBs.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/08/featured-screenshots-asea-shortcode.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/shortcode-for-one-or-more-kbs/',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2020.07.02'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Article Previous/Next Buttons', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Allow your users to navigate to the next article or previous articles using the previous/next buttons.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/06/new-feature-article-navigation-btns.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/meta-data-authors-dates/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.06.01'] = array(
			'plugin'            => esc_html__( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Sub Category Filter', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'New sub-category filter option to narrow down your search.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/06/new-feature-sub-category-filter.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/demo-11/',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.04.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Article Sidebars', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'New article sidebars with the ability to add your own Widgets, TOC and custom code.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/04/new-feature-wizards-sidebars.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/sidebar-overview/',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2020.03.01'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Wizards', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Use Knowledge Base Wizard for an easy way to set up your KB and to choose from predefined Templates and colors.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/02/new-feature-wizards.jpg',
			'learn_more_url'    => 'https://www.youtube.com/watch?v=5uI9q2ipZxU&utm_medium=newfeatures&utm_content=home&utm_campaign=wizards',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.02.18'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Image Icons for Themes', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Add Image icons to top categories in your theme. You can upload images or custom icons.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/02/image-icons-for-themes.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=image-icons',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2020.01.ac'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Categories Focused Layout', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'New layout that focuses on showing categories in a sidebar on both Category Archive and Article pages.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/01/category-focused-layout.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		/*	$features['2020.01.df'] = array(
			   'plugin'            => 'KB Core',
			   'title'             => 'New Option for Date Formats',
			   'description'       => '<p>On Article pages, choose the format for the Last Updated and Created On dates.</p>',
			   'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-features-article-category-sequence.jpg',
			   'learn_more_url'    => '',
			   'plugin-type'       => 'core',
			   'type'              => 'new-feature'
		   ); */

		$features['2019.12.ac'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             =>__(  'New Option to Show Articles Above Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'On the Main Page (or Sidebar if you have the Elegant Layout add-on) the article can now be configured to appear above their peer categories and sub-categories.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/03/new-features-article-category-sequence-2.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.12.lv'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Three Additional Levels of Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'You can now organize your categories and articles up to six levels deep, allowing you to have more complex documentation hierarchy.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-three-new-levels-3.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.12.oo'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Table of Content on Article Pages', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Articles can now display table of content (TOC) on either side. The TOC has a list of headings and subheading. Users can easily see the article structure and can navigate to any section of the article.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-TOC-1.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.11.au'] = array(
			'plugin'            => esc_html__( 'KB Core', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Articles Can Now Display Author and Creation Date', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Configure article to display author and create date.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/new-feature-core-new-meta-1.jpg',
			'learn_more_url'    => '',
			'plugin-type'       => 'core',
			'type'              => 'new-feature'
		);

		$features['2019.11.rf'] = array(
			'plugin'            => esc_html__( 'Article Rating and Feedback', 'echo-knowledge-base'),
			'title'             => esc_html__( 'User Can Rate Articles and Submit Feedback', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'This new add-on allows users to rate articles. They can also opt to fill out a form to submit details about their vote. The admin can access the analytics to see the most and least rated articles.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=new-plugin',
			'plugin-type'       => 'add-on',
			'type'              => 'new-addon'
		);

		$features['2019.11.hc'] = array(
			'plugin'            => esc_html__( 'Advanced Search', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Search Results Include Category for Each Article', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Search category filter now shows category hierarchy each found article is in.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/AS'.'EA-feature-results-category.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=category-hierarchy',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		$features['2019.10.am'] = array(
			'plugin'            => esc_html__( 'KB Groups for Access Manager', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Search Easily for Users to Add to KB Groups', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'The KB Groups add-on allows ordering of users into different groups and roles. The new search bar lets the administrator quickly find a specific user to make changes.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/search-users-1-1024x601.jpg',
			'learn_more_url'    => 'https://www.echoknowledgebase.com/documentation/2-3-wp-users/?utm_source=plugin&utm_medium=newfeatures&utm_content=home&utm_campaign=user-search',
			'plugin-type'       => 'add-on',
			'type'              => 'new-feature'
		);

		return $features;
	}

	/**
	 * Call when the user saw new features
	 */
	private static function update_last_seen_version() {

		$features_list = self::features_list();
		krsort($features_list);
		$last_feature_date = key( $features_list );

		$result = EPKB_Utilities::save_wp_option( 'epkb_last_seen_version', $last_feature_date );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not update last seen features', $result );
			return false;
		}

		return true;
	}

	/**
	 * Get box that contains list of new features for a certain year
	 *
	 * @param $year_key
	 * @return false|string
	 */
	public static function get_new_features_box_by_year( $year_key ) {

		$features_list = self::get_new_features_list();

		ob_start();

		if ( isset( $features_list[$year_key] ) ) {     ?>
			<div class="epkb-features-container">       <?php
				self::display_new_features_details( $features_list[$year_key] );    ?>
			</div>      <?php
		}

		self::update_last_seen_version();  // clears menu count of versions not seen

		return ob_get_clean();
	}

	/**
	 * Display all new features
	 * add-ons
	 * $history = array('2019.1') = array([history_item],[history_item]...)
	 * @param $features
	 */
	private static function display_new_features_details( $features ) {
		foreach ( $features as $date => $feature ) {
			self::new_feature( $date, $feature );
		}
	}

	/**
	 * Get list of all new features
	 *
	 * @return array
	 */
	private static function get_new_features_list() {

		// get new features in last release
		$features = self::features_list();
		$features = empty($features) || ! is_array($features) ? array() : $features;

		$features_list = array();
		foreach ( $features as $date => $feature ) {
			$season = explode('.', $date);
			$key =  esc_html__( 'Year' ) . ' ' . $season[0];
			$features_list[$key][$date] = $feature;
		}

		return $features_list;
	}

	private static function group_by_month( $month ) {
		global $wp_locale;

		//Group 3 Month
		$month = ($month % 3 == 2) ? $month - 1 : ( ($month % 3 == 0) ? $month - 2 : $month );

		$monthName1 = ucfirst($wp_locale->get_month_abbrev($wp_locale->get_month($month)));
		$monthName2 = ucfirst($wp_locale->get_month_abbrev($wp_locale->get_month($month + 2)));

		return $monthName1 . ' - ' . $monthName2 . ' ' . date('Y');
	}

	/**
	 * Display list of CREL add-on features
	 */
	public static function display_crel_features_details() {
		$features = self::crel_features_list();
		$features = empty($features) || ! is_array($features) ? array() : $features;
		foreach ( $features as $date => $feature ) {
			self::new_feature( $date, $feature );
		}
	}

	/**
	 * Display feature information with image.
	 *
	 * @param $date
	 * @param array $args
	 */
	private static function new_feature( $date, $args = array () ) {
		global $wp_locale; 
		
		$season = explode('.', $date);
		$monthName = '';
		if ( ! empty($season[0]) && ! empty($season[1]) ) {
			$monthName = ucfirst($wp_locale->get_month_abbrev($wp_locale->get_month($season[1])));
			$date = $monthName . ' ' . $season[0];
		}   ?>

		<div class="epkb-features__new-feature" class="add_on_product">

			<div class="epkb-fnf__header">				<?php
				switch ( $args['type']) {
					case 'new-addon':
						echo '<span class="epkb-fnf__header__new-add-on"> <i class="epkbfa epkbfa-plug" aria-hidden="true"></i> ' . esc_html__( 'New Add-on', 'echo-knowledge-base') . '</span>';
						break;
					case 'new-feature':
						echo '<span class="epkb-fnf__header__new-feature">' . esc_html( $monthName ) . '</span>';
						break;
					case 'new-plugin':
						echo '<span class="epkb-fnf__header__new-add-on"> <i class="epkbfa epkbfa-plug " aria-hidden="true"></i>' . esc_html__( 'New Plugin', 'echo-knowledge-base') . '</span>';
						break;
					case 'widget':
						echo '<span class="epkb-fnf__header__widget"> <i class="epkbfa epkbfa-puzzle-piece " aria-hidden="true"></i>' . esc_html__( 'Widget', 'echo-knowledge-base') . '</span>';
						break;
				} ?>
				<h3 class="epkb-fnf__header__title"><?php esc_html_e( $args['title']); ?></h3>
			</div>		<?php
			if ( isset( $args['image']) ) { ?>
				<div class="featured_img epkb-fnf__img">
					<img src="<?php echo empty( $args['image']) ? '' : esc_url( $args['image'] ); ?>">
				</div>			<?php
			}
			if ( isset( $args['video']) ) { ?>
				<div class="epkb-fnf__video">
					<iframe width="560" height="170" src="<?php echo esc_url( $args['video'] ); ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
				</div>			<?php
			}
			if ( $args['plugin-type'] != 'elementor' ) { ?>
	            <div class="epkb-fnf__meta">					<?php
		            switch ( $args['plugin-type'] ) {
			            case 'add-on':
				            echo '<div class="epkb-fnf__meta__addon">' . esc_html__( 'Add-on', 'echo-knowledge-base') . '</div>';
				            break;
			            case 'core':
				            echo '<div class="epkb-fnf__meta__core">' . esc_html__( 'Core', 'echo-knowledge-base') . '</div>';
				            break;
			            case 'plugin':
				            echo '<div class="epkb-fnf__meta__addon">' . esc_html__( 'Plugin', 'echo-knowledge-base') . '</div>';
				            break;
			            case 'elementor':
				            echo '<div class="epkb-fnf__meta__addon">' . esc_html__( 'Elementor', 'echo-knowledge-base') . '</div>';
				            break;
		            }    ?>
					<div class="epkb-fnf__meta__plugin"><?php echo esc_html( $args['plugin'] ); ?></div>
					<div class="epkb-fnf__meta__date"><?php echo esc_html( $date ); ?></div>
				</div>			<?php
			}   ?>
			<div class="epkb-fnf__body">
				<p>
					<?php echo wp_kses_post( $args['description']); ?>
				</p>
			</div>			<?php
			if ( ! empty( $args['learn_more_url'] ) ) {
			   $button_name = empty( $args['button_name']) ? esc_html__( 'Learn More', 'echo-knowledge-base' ) : $args['button_name'];    ?>
				<div class="epkb-fnf__button-container">
					<a class="epkb-primary-btn" href="<?php echo esc_url( $args['learn_more_url'] ); ?>" target="_blank"><?php echo esc_html( $button_name ); ?></a>
				</div>			<?php
			}       ?>

		</div>    <?php
	}

	/**
	 * Filter crel features array to add latest
	 * @return array
	 */
	private static function crel_features_list() {
		$features = array();

		$features['2020.12.15'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Image Guide', 'creative-addons-for-elementor'),
			'description'       => '<p>' . esc_html__( "Add hotspots to screenshots and images, and connect each hotspot to a note.", 'creative-addons-for-elementor') . '</p>',
			'video'             => 'https://www.youtube.com/embed/SZEP_zxBvy4',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/image-guide/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.12.16'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Text and Image', 'creative-addons-for-elementor'),
			'description'       => '<p>' . esc_html__( 'Easy way to add text and image combo with one widget.', 'creative-addons-for-elementor') . '</p>',
			'video'             => 'https://www.youtube.com/embed/0Lpi-M2i32U',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/text-image/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.15'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Notification Box', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( "Provide important information using a prominent style to instantly catch reader's attention.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/Notification-box-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/notification-box/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.16'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Advanced Heading', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Create custom headings with lots of options. Add a link or a badge to take your documentation to the next level.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/advanced-heading-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/advanced-heading/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.17'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Step-by-step / How To', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Create amazing step-by-step documentation consistently and quickly with our powerful Steps widget.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/steps-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/steps/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2021.05.20'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Code Block', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( "Embed source code examples in your article. The user can copy and expand the code. Show code examples in CSS, HTML, JS, PHP, C# and more.", 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2021/06/Code-block-top-image-5.png',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/code-block/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);
		
		$features['2020.10.18'] = array(
		   'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
		   'title'             => esc_html__( 'Advanced Lists', 'echo-knowledge-base'),
		   'description'       => '<p>' . esc_html__( 'Make multi-level lists easily. Show levels as numbers, letters or in other formats.', 'echo-knowledge-base') . '</p>',
		   'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/advanced-list-features.jpg',
		   'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/advanced-lists/',
		   'plugin-type'       => 'elementor',
		   'type'              => 'widget'
		);

		$features['2020.10.19'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'KB Search', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Add a Search Box to any page to search documentation stored in Echo Knowledge Base.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/kb-search-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base-search/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.20'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'KB Categories', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Display your Knowledge base Categories in stunning layouts with our powerful Elementor widget.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/more-kb-widgets-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base-categories/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.21'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'KB Recent Articles', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Display your Knowledge Base Articles in stunning layouts with our powerful Elementor widget.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/more-kb-widgets-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base-recent-articles/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		$features['2020.10.22'] = array(
			'plugin'            => esc_html__( 'Widget', 'echo-knowledge-base'),
			'title'             => esc_html__( 'Knowledge Base Widget', 'echo-knowledge-base'),
			'description'       => '<p>' . esc_html__( 'Display your Knowledge base on any page.', 'echo-knowledge-base') . '</p>',
			'image'             => 'https://www.creative-addons.com/wp-content/uploads/2020/10/kb-main-page-features.jpg',
			'learn_more_url'    => 'https://www.creative-addons.com/elementor-widgets/knowledge-base/',
			'plugin-type'       => 'elementor',
			'type'              => 'widget'
		);

		return $features;
	}
}

