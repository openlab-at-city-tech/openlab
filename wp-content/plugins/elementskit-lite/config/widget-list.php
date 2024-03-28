<?php

namespace ElementsKit_Lite\Config;

defined( 'ABSPATH' ) || exit;
class Widget_List extends \ElementsKit_Lite\Core\Config_List {

	protected $type = 'widget';
	
	protected function set_required_list() {
		$this->required_list = array();
	}

	protected function set_optional_list() {

		$this->optional_list = apply_filters(
			'elementskit/widgets/list',
			array(
				'image-accordion' => array(
					'slug'            => 'image-accordion',
					'title'           => 'Image Accordion',
					'package'         => 'free', // free, pro, free
					//'path' => 'path to the widget directory',
					//'base_class_name' => 'main class name',
					//'title' => 'widget title',
					//'live' => 'live demo url'
					'widget-category' => 'general', // General
				),
				'accordion' => array(
					'slug'            => 'accordion',
					'title'           => 'Accordion',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'button' => array(
					'slug'            => 'button',
					'title'           => 'Button',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'heading' => array(
					'slug'            => 'heading',
					'title'           => 'Heading',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'blog-posts' => array(
					'slug'            => 'blog-posts',
					'title'           => 'Blog Posts',
					'package'         => 'free',
					'widget-category' => 'wp-posts', // Post Widgets
				),
				'icon-box' => array(
					'slug'            => 'icon-box',
					'title'           => 'Icon Box',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'image-box' => array(
					'slug'            => 'image-box',
					'title'           => 'Image Box',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'countdown-timer' => array(
					'slug'            => 'countdown-timer',
					'title'           => 'Countdown Timer',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'client-logo' => array(
					'slug'            => 'client-logo',
					'title'           => 'Client Logo',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'faq' => array(
					'slug'            => 'faq',
					'title'           => 'FAQ',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'funfact' => array(
					'slug'            => 'funfact',
					'title'           => 'Funfact',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'image-comparison' => array(
					'slug'            => 'image-comparison',
					'title'           => 'Image Comparison',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'lottie' => array(
					'slug'            => 'lottie',
					'title'           => 'Lottie',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'testimonial' => array(
					'slug'            => 'testimonial',
					'title'           => 'Testimonial',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'pricing' => array(
					'slug'            => 'pricing',
					'title'           => 'Pricing Table',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'team' => array(
					'slug'            => 'team',
					'title'           => 'Team',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'social' => array(
					'slug'            => 'social',
					'title'           => 'Social Icons',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'progressbar' => array(
					'slug'            => 'progressbar',
					'title'           => 'Progress Bar',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'category-list' => array(
					'slug'            => 'category-list',
					'title'           => 'Category List',
					'package'         => 'free',
					'widget-category' => 'wp-posts', // Post Widgets
				),
				'page-list' => array(
					'slug'            => 'page-list',
					'title'           => 'Page List',
					'package'         => 'free',
					'widget-category' => 'header-footer', // ElementsKit Header Footer
				),
				'post-grid' => array(
					'slug'            => 'post-grid',
					'title'           => 'Post Grid',
					'package'         => 'free',
					'widget-category' => 'wp-posts', // Post Widgets
				),
				'post-list' => array(
					'slug'            => 'post-list',
					'title'           => 'Post List',
					'package'         => 'free',
					'widget-category' => 'wp-posts', // Post Widgets
				),
				'post-tab' => array(
					'slug'            => 'post-tab',
					'title'           => 'Post Tab',
					'package'         => 'free',
					'widget-category' => 'wp-posts', // Post Widgets
				),
				'nav-menu' => array(
					'slug'            => 'nav-menu',
					'title'           => 'ElementsKit Nav Menu',
					'package'         => 'free',
					'widget-category' => 'header-footer', // ElementsKit Header Footer
				),
				'mail-chimp' => array(
					'slug'            => 'mail-chimp',
					'title'           => 'MailChimp',
					'package'         => 'free',
					'widget-category' => 'form-widgets', // Form Widgets
				),
				'header-info' => array(
					'slug'            => 'header-info',
					'title'           => 'Header Info',
					'package'         => 'free',
					'widget-category' => 'header-footer', // ElementsKit Header Footer
				),
				'piechart' => array(
					'slug'            => 'piechart',
					'title'           => 'Pie Chart',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'header-search' => array(
					'slug'            => 'header-search',
					'title'           => 'Header Search',
					'package'         => 'free',
					'widget-category' => 'header-footer', // ElementsKit Header Footer
				),
				'header-offcanvas' => array(
					'slug'            => 'header-offcanvas',
					'title'           => 'Header Offcanvas',
					'package'         => 'free',
					'widget-category' => 'header-footer', // ElementsKit Header Footer
				),
				'tab' => array(
					'slug'            => 'tab',
					'title'           => 'Tab',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'contact-form7' => array(
					'slug'            => 'contact-form7',
					'title'           => 'Contact Form7',
					'package'         => 'free',
					'widget-category' => 'form-widgets', // Form Widgets
				),
				'video' => array(
					'slug'            => 'video',
					'title'           => 'Video',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'business-hours' => array(
					'slug'            => 'business-hours',
					'title'           => 'Business Hours',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'drop-caps' => array(
					'slug'            => 'drop-caps',
					'title'           => 'Drop Caps',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'social-share' => array(
					'slug'            => 'social-share',
					'title'           => 'Social Share',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'dual-button' => array(
					'slug'            => 'dual-button',
					'title'           => 'Dual Button',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'caldera-forms' => array(
					'slug'            => 'caldera-forms',
					'title'           => 'Caldera Forms',
					'package'         => 'free',
					'widget-category' => 'form-widgets', // Form Widgets
				),
				'we-forms' => array(
					'slug'            => 'we-forms',
					'title'           => 'weForms',
					'package'         => 'free',
					'widget-category' => 'form-widgets', // Form Widgets
				),
				'wp-forms' => array(
					'slug'            => 'wp-forms',
					'title'           => 'WPForms',
					'package'         => 'free',
					'widget-category' => 'form-widgets', // Form Widgets
				),
	
				'ninja-forms' => array(
					'slug'            => 'ninja-forms',
					'title'           => 'Ninja Forms',
					'package'         => 'free',
					'widget-category' => 'form-widgets', // Form Widgets
				),
				'tablepress' => array(
					'slug'            => 'tablepress',
					'title'           => 'TablePress',
					'package'         => 'free',
					'widget-category' => 'general', // General
				),
				'fluent-forms'         => array(
					'slug'            => 'fluent-forms',
					'title'           => 'Fluent Forms',
					'package'         => 'free',
					'widget-category' => 'form-widgets', // Form Widgets
				),
				'back-to-top' => array(
					'slug'            => 'back-to-top',
					'title'           => 'Back To Top',
					'package'         => 'free',
					'widget-category' => 'general', //general
				),
				'advanced-accordion' => array(
					'slug'            => 'advanced-accordion',
					'title'           => 'Advanced Accordion',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'advanced-tab' => array(
					'slug'            => 'advanced-tab',
					'title'           => 'Advanced Tab',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'hotspot' => array(
					'slug'            => 'hotspot',
					'title'           => 'Hotspot',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'motion-text' => array(
					'slug'            => 'motion-text',
					'title'           => 'Motion Text',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'twitter-feed' => array(
					'slug'            => 'twitter-feed',
					'title'           => 'Twitter Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // Social Media Feeds Widgets
				),
	
				'instagram-feed'       => array(
					'slug'            => 'instagram-feed',
					'title'           => 'Instagram Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // Social Media Feeds Widgets
				),
				'gallery'              => array(
					'slug'            => 'gallery',
					'title'           => 'Gallery',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'chart'                => array(
					'slug'            => 'chart',
					'title'           => 'Chart',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'woo-category-list'    => array(
					'slug'            => 'woo-category-list',
					'title'           => 'Woo Category List',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // Woocommerce Widgets
				),
				'woo-mini-cart'        => array(
					'slug'            => 'woo-mini-cart',
					'title'           => 'Woo Mini Cart',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // Woocommerce Widgets
				),
				'woo-product-carousel' => array(
					'slug'            => 'woo-product-carousel',
					'title'           => 'Woo Product Carousel',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // Woocommerce Widgets
				),
				'woo-product-list'     => array(
					'slug'            => 'woo-product-list',
					'title'           => 'Woo Product List',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // Woocommerce Widgets
				),
				'table'                => array(
					'slug'            => 'table',
					'title'           => 'Table',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'timeline'             => array(
					'slug'            => 'timeline',
					'title'           => 'Timeline',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'creative-button'      => array(
					'slug'            => 'creative-button',
					'title'           => 'Creative Button',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'vertical-menu'        => array(
					'slug'            => 'vertical-menu',
					'title'           => 'Vertical Menu',
					'package'         => 'pro-disabled',
					'widget-category' => 'header-footer', // ElementsKit Header Footer
				),
				'advanced-toggle'      => array(
					'slug'            => 'advanced-toggle',
					'title'           => 'Advanced Toggle',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'video-gallery'        => array(
					'slug'            => 'video-gallery',
					'title'           => 'Video Gallery',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'zoom'                 => array(
					'slug'            => 'zoom',
					'title'           => 'Zoom',
					'package'         => 'pro-disabled',
					'widget-category' => 'meeting-widgets', // Meeting Widgets
				),
				'behance-feed'         => array(
					'slug'            => 'behance-feed',
					'title'           => 'Behance Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // Social Media Feeds Widgets
				),
				'breadcrumb' => array(
					'slug'            => 'breadcrumb',
					'title'           => 'Breadcrumb',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'dribble-feed' => array(
					'slug'            => 'dribble-feed',
					'title'           => 'Dribbble Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // Social Media Feeds Widgets
				),
				'facebook-feed' => array(
					'slug'            => 'facebook-feed',
					'title'           => 'Facebook Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // Social Media Feeds Widgets
				),
				'facebook-review' => array(
					'slug'            => 'facebook-review',
					'title'           => 'Facebook Review',
					'package'         => 'pro-disabled',
					'widget-category' => 'review-widgets', // Review Widgets
				),
				'yelp' => array(
					'slug'            => 'yelp',
					'title'           => 'Yelp',
					'package'         => 'pro-disabled',
					'widget-category' => 'review-widgets', // Review Widgets
				),
				'pinterest-feed' => array(
					'slug'            => 'pinterest-feed',
					'title'           => 'Pinterest Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // Social Media Feeds Widgets
				),
				'popup-modal' => array(
					'slug'            => 'popup-modal',
					'title'           => 'Popup Modal',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'google-map' => array(
					'slug'            => 'google-map',
					'title'           => 'Google Maps',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'unfold' => array(
					'slug'            => 'unfold',
					'title'           => 'Unfold',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'image-swap' => array(
					'slug'            => 'image-swap',
					'title'           => 'Image Swap',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'whatsapp' => array(
					'slug'            => 'whatsapp',
					'title'           => 'WhatsApp',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'advanced-slider' => array(
					'slug'            => 'advanced-slider',
					'title'           => 'Advanced Slider',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'image-hover-effect' => array(
					'slug'            => 'image-hover-effect',
					'title'           => 'Image Hover Effect',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'fancy-animated-text' => array(
					'slug'            => 'fancy-animated-text',
					'title'           => 'Fancy Animated Text',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'price-menu' => array(
					'slug'            => 'price-menu',
					'title'           => 'Price Menu',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'stylish-list' => array(
					'slug'            => 'stylish-list',
					'title'           => 'Stylish List',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'team-slider' => array(
					'slug'            => 'team-slider',
					'title'           => 'Team Slider',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'audio-player' => array(
					'slug'            => 'audio-player',
					'title'           => 'Audio Player',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'flip-box' => array(
					'slug'    => 'flip-box',
					'title'   => 'Flip Box',
					'package' => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'image-morphing' => array(
					'slug'            => 'image-morphing',
					'title'           => 'Image Morphing',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'content-ticker' => array(
					'slug'            => 'content-ticker',
					'title'           => 'Content Ticker',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'coupon-code' => array(
					'slug'            => 'coupon-code',
					'title'           => 'Coupon Code',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'comparison-table' => array(
					'slug'            => 'comparison-table',
					'title'           => 'Comparison Table',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'protected-content' => array(
					'slug'            => 'protected-content',
					'title'           => 'Protected Content',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
				'interactive-links' => array(
					'slug'            => 'interactive-links',
					'title'           => 'Interactive Links',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
				),
			)
		);
	}
}
