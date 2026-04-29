<?php

namespace ElementsKit_Lite\Config;

defined('ABSPATH') || exit;
class Widget_List extends \ElementsKit_Lite\Core\Config_List
{

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
					'widget-category' => 'general', // general
				),
				'accordion' => array(
					'slug'            => 'accordion',
					'title'           => 'Accordion',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'button' => array(
					'slug'            => 'button',
					'title'           => 'Button',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'heading' => array(
					'slug'            => 'heading',
					'title'           => 'Heading',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'blog-posts' => array(
					'slug'            => 'blog-posts',
					'title'           => 'Blog Posts',
					'package'         => 'free',
					'widget-category' => 'post', // posts
				),
				'icon-box' => array(
					'slug'            => 'icon-box',
					'title'           => 'Icon Box',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'image-box' => array(
					'slug'            => 'image-box',
					'title'           => 'Image Box',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'countdown-timer' => array(
					'slug'            => 'countdown-timer',
					'title'           => 'Countdown Timer',
					'package'         => 'free',
					'widget-category' => 'marketing', // marketing
				),
				'client-logo' => array(
					'slug'            => 'client-logo',
					'title'           => 'Client Logo',
					'package'         => 'free',
					'widget-category' => 'marketing', // marketing
				),
				'faq' => array(
					'slug'            => 'faq',
					'title'           => 'FAQ',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'funfact' => array(
					'slug'            => 'funfact',
					'title'           => 'Funfact',
					'package'         => 'free',
					'widget-category' => 'creative', // creative
				),
				'image-comparison' => array(
					'slug'            => 'image-comparison',
					'title'           => 'Image Comparison',
					'package'         => 'free',
					'widget-category' => 'creative', // creative
				),
				'lottie' => array(
					'slug'            => 'lottie',
					'title'           => 'Lottie',
					'package'         => 'free',
					'widget-category' => 'creative', // creative
				),
				'testimonial' => array(
					'slug'            => 'testimonial',
					'title'           => 'Testimonial',
					'package'         => 'free',
					'widget-category' => 'marketing', // marketing
				),
				'pricing' => array(
					'slug'            => 'pricing',
					'title'           => 'Pricing Table',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'team' => array(
					'slug'            => 'team',
					'title'           => 'Team',
					'package'         => 'free',
					'widget-category' => 'advanced', // advanced
				),
				'social' => array(
					'slug'            => 'social',
					'title'           => 'Social Icons',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'progressbar' => array(
					'slug'            => 'progressbar',
					'title'           => 'Progress Bar',
					'package'         => 'free',
					'widget-category' => 'creative', // creative
				),
				'category-list' => array(
					'slug'            => 'category-list',
					'title'           => 'Category List',
					'package'         => 'free',
					'widget-category' => 'post', // posts
				),
				'page-list' => array(
					'slug'            => 'page-list',
					'title'           => 'Page List',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'post-grid' => array(
					'slug'            => 'post-grid',
					'title'           => 'Post Grid',
					'package'         => 'free',
					'widget-category' => 'post', // posts
				),
				'post-list' => array(
					'slug'            => 'post-list',
					'title'           => 'Post List',
					'package'         => 'free',
					'widget-category' => 'post', // posts
				),
				'post-tab' => array(
					'slug'            => 'post-tab',
					'title'           => 'Post Tab',
					'package'         => 'free',
					'widget-category' => 'post', // posts
				),
				'nav-menu' => array(
					'slug'            => 'nav-menu',
					'title'           => 'Nav Menu',
					'package'         => 'free',
					'widget-category' => 'header-footer', // header footer
				),
				'mail-chimp' => array(
					'slug'            => 'mail-chimp',
					'title'           => 'MailChimp',
					'package'         => 'free',
					'widget-category' => 'form', // form
				),
				'header-info' => array(
					'slug'            => 'header-info',
					'title'           => 'Header Info',
					'package'         => 'free',
					'widget-category' => 'header-footer', // header footer
				),
				'piechart' => array(
					'slug'            => 'piechart',
					'title'           => 'Pie Chart',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'header-search' => array(
					'slug'            => 'header-search',
					'title'           => 'Header Search',
					'package'         => 'free',
					'widget-category' => 'header-footer', // header footer
				),
				'header-offcanvas' => array(
					'slug'            => 'header-offcanvas',
					'title'           => 'Header Offcanvas',
					'package'         => 'free',
					'widget-category' => 'header-footer', // header footer
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
					'widget-category' => 'form', // form
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
					'widget-category' => 'creative', // creative
				),
				'social-share' => array(
					'slug'            => 'social-share',
					'title'           => 'Social Share',
					'package'         => 'free',
					'widget-category' => 'marketing', // marketing
				),
				'dual-button' => array(
					'slug'            => 'dual-button',
					'title'           => 'Dual Button',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'we-forms' => array(
					'slug'            => 'we-forms',
					'title'           => 'weForms',
					'package'         => 'free',
					'widget-category' => 'form', // form
				),
				'wp-forms' => array(
					'slug'            => 'wp-forms',
					'title'           => 'WPForms',
					'package'         => 'free',
					'widget-category' => 'form', // form
				),
				'ninja-forms' => array(
					'slug'            => 'ninja-forms',
					'title'           => 'Ninja Forms',
					'package'         => 'free',
					'widget-category' => 'form', // form
				),
				'tablepress' => array(
					'slug'            => 'tablepress',
					'title'           => 'TablePress',
					'package'         => 'free',
					'widget-category' => 'general', // general
				),
				'fluent-forms'         => array(
					'slug'            => 'fluent-forms',
					'title'           => 'Fluent Forms',
					'package'         => 'free',
					'widget-category' => 'form', // form
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
					'widget-category' => 'advanced', // advanced
					'icon'            => 'ekit ekit-accordion',
				),
				'advanced-tab' => array(
					'slug'            => 'advanced-tab',
					'title'           => 'Advanced Tab',
					'package'         => 'pro-disabled',
					'widget-category' => 'advanced', // advanced
					'icon'            => 'ekit ekit-tab',
				),
				'hotspot' => array(
					'slug'            => 'hotspot',
					'title'           => 'Hotspot',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'eicon-image-hotspot',
				),
				'motion-text' => array(
					'slug'            => 'motion-text',
					'title'           => 'Motion Text',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'eicon-animation-text',
				),
				'twitter-feed' => array(
					'slug'            => 'twitter-feed',
					'title'           => 'Twitter Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // social media feeds
					'icon'            => 'eicon-twitter-feed',
				),
				'instagram-feed'       => array(
					'slug'            => 'instagram-feed',
					'title'           => 'Instagram Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // social media feeds
					'icon'            => 'ekit ekit-instagram',
				),
				'gallery'              => array(
					'slug'            => 'gallery',
					'title'           => 'Gallery',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'            => 'ekit ekit-image-gallery',
				),
				'chart'                => array(
					'slug'            => 'chart',
					'title'           => 'Chart',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'            => 'eicon-shape',
				),
				'woo-category-list'    => array(
					'slug'            => 'woo-category-list',
					'title'           => 'Woo Category List',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // woocommerce
					'icon'            => 'eicon-product-categories',
				),
				'woo-mini-cart'        => array(
					'slug'            => 'woo-mini-cart',
					'title'           => 'Woo Mini Cart',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // woocommerce
					'icon'            => 'eicon-product-add-to-cart',
				),
				'woo-product-carousel' => array(
					'slug'            => 'woo-product-carousel',
					'title'           => 'Woo Product Carousel',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // woocommerce
					'icon'            => 'eicon-carousel',
				),
				'woo-product-list'     => array(
					'slug'            => 'woo-product-list',
					'title'           => 'Woo Product List',
					'package'         => 'pro-disabled',
					'widget-category' => 'woocommerce', // woocommerce
					'icon'            => 'eicon-editor-list-ul',
				),
				'table'                => array(
					'slug'            => 'table',
					'title'           => 'Data Table',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'            => 'eicon-table',
				),
				'timeline'             => array(
					'slug'            => 'timeline',
					'title'           => 'Timeline',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'ekit ekit-horizontal-timeline',
				),
				'creative-button'      => array(
					'slug'            => 'creative-button',
					'title'           => 'Creative Button',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'eicon-button',
				),
				'vertical-menu'        => array(
					'slug'            => 'vertical-menu',
					'title'           => 'Vertical Menu',
					'package'         => 'pro-disabled',
					'widget-category' => 'header-footer', // header footer
					'icon'            => 'eicon-nav-menu',
				),
				'advanced-toggle'      => array(
					'slug'            => 'advanced-toggle',
					'title'           => 'Advanced Toggle',
					'package'         => 'pro-disabled',
					'widget-category' => 'advanced', // advanced
					'icon'            => 'eicon-toggle',
				),
				'video-gallery'        => array(
					'slug'            => 'video-gallery',
					'title'           => 'Video Gallery',
					'package'         => 'pro-disabled',
					'widget-category' => 'advanced', // advanced
					'icon'            => 'eicon-youtube',
				),
				'zoom'                 => array(
					'slug'            => 'zoom',
					'title'           => 'Zoom',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'            => 'eicon-button',
				),
				'behance-feed'         => array(
					'slug'            => 'behance-feed',
					'title'           => 'Behance Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // social media feeds
					'icon'            => 'ekit ekit-behance',
				),
				'breadcrumb' => array(
					'slug'            => 'breadcrumb',
					'title'           => 'Breadcrumb',
					'package'         => 'pro-disabled',
					'widget-category' => 'header-footer', // header footer
					'icon'            => 'eicon-button',
				),
				'dribble-feed' => array(
					'slug'            => 'dribble-feed',
					'title'           => 'Dribbble Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // social media feeds
					'icon'            => 'ekit ekit-dribbble',
				),
				'facebook-feed' => array(
					'slug'            => 'facebook-feed',
					'title'           => 'Facebook Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // social media feeds
					'icon'            => 'eicon-fb-feed',
				),
				'facebook-review' => array(
					'slug'            => 'facebook-review',
					'title'           => 'Facebook Review',
					'package'         => 'pro-disabled',
					'widget-category' => 'review-testimonials', // review testimonials
					'icon'            => 'eicon-button'
				),
				'yelp' => array(
					'slug'            => 'yelp',
					'title'           => 'Yelp',
					'package'         => 'pro-disabled',
					'widget-category' => 'review-testimonials', // review testimonials
					'icon'            => 'eicon-favorite',
				),
				'pinterest-feed' => array(
					'slug'            => 'pinterest-feed',
					'title'           => 'Pinterest Feed',
					'package'         => 'pro-disabled',
					'widget-category' => 'social-media-feeds', // social media feeds
					'icon'            => 'ekit ekit-pinterest',
				),
				'popup-modal' => array(
					'slug'            => 'popup-modal',
					'title'           => 'Popup Modal',
					'package'         => 'pro-disabled',
					'widget-category' => 'marketing', // marketing
					'icon'            => 'eicon-button',
				),
				'google-map' => array(
					'slug'            => 'google-map',
					'title'           => 'Google Maps',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'            => 'eicon-google-maps',
				),
				'unfold' => array(
					'slug'            => 'unfold',
					'title'           => 'Unfold',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'eicon-button',
				),
				'image-swap' => array(
					'slug'            => 'image-swap',
					'title'           => 'Image Swap',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'ekit ekit-image-swap',
				),
				'whatsapp' => array(
					'slug'            => 'whatsapp',
					'title'           => 'WhatsApp',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'            => 'ekit ekit-whatsapp',
				),
				'advanced-slider' => array(
					'slug'            => 'advanced-slider',
					'title'           => 'Advanced Slider',
					'package'         => 'pro-disabled',
					'widget-category' => 'advanced', // advanced
					'icon'            => 'ekit ekit-advanced-slider',
				),
				'image-hover-effect' => array(
					'slug'            => 'image-hover-effect',
					'title'           => 'Image Hover Effect',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'			  => 'ekit ekit-image-hover-effect',
				),
				'fancy-animated-text' => array(
					'slug'            => 'fancy-animated-text',
					'title'           => 'Fancy Animated Text',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'ekit ekit-fancy-heading',
				),
				'price-menu' => array(
					'slug'            => 'price-menu',
					'title'           => 'Price Menu',
					'package'         => 'pro-disabled',
					'widget-category' => 'marketing', // marketing
					'icon'            => 'ekit ekit-price-menu',
				),
				'stylish-list' => array(
					'slug'            => 'stylish-list',
					'title'           => 'Stylish List',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'ekit ekit-stylish-list',
				),
				'team-slider' => array(
					'slug'            => 'team-slider',
					'title'           => 'Team Slider',
					'package'         => 'pro-disabled',
					'widget-category' => 'advanced', // advanced
					'icon'            => 'ekit ekit-team-carousel-slider',
				),
				'audio-player' => array(
					'slug'            => 'audio-player',
					'title'           => 'Audio Player',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // General
					'icon'            => 'ekit ekit-audio-player',
				),
				'flip-box' => array(
					'slug'    => 'flip-box',
					'title'   => 'Flip Box',
					'package' => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'    => 'ekit ekit-flip-box',
				),
				'image-morphing' => array(
					'slug'            => 'image-morphing',
					'title'           => 'Image Morphing',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'ekit ekit-image-morphing',
				),
				'content-ticker' => array(
					'slug'            => 'content-ticker',
					'title'           => 'Content Ticker',
					'package'         => 'pro-disabled',
					'widget-category' => 'marketing', // marketing
					'icon'            => 'ekit ekit-content-ticker',
				),
				'coupon-code' => array(
					'slug'            => 'coupon-code',
					'title'           => 'Coupon Code',
					'package'         => 'pro-disabled',
					'widget-category' => 'marketing', // marketing
					'icon'            => 'ekit ekit-coupon-code',
				),
				'comparison-table' => array(
					'slug'            => 'comparison-table',
					'title'           => 'Comparison Table',
					'package'         => 'pro-disabled',
					'widget-category' => 'marketing', // marketing
					'icon'            => 'ekit ekit-flip-box',
				),
				'protected-content' => array(
					'slug'            => 'protected-content',
					'title'           => 'Protected Content',
					'package'         => 'pro-disabled',
					'widget-category' => 'advanced', // advanced
					'icon'            => 'ekit ekit-protected-content-v3',
				),
				'interactive-links' => array(
					'slug'            => 'interactive-links',
					'title'           => 'Interactive Links',
					'package'         => 'pro-disabled',
					'widget-category' => 'creative', // creative
					'icon'            => 'ekit ekit-interactive-link',
				),
				'circle-menu' => array(
					'slug'            => 'circle-menu',
					'title'           => 'Circle Menu',
					'package'         => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'            => 'ekit ekit-coupon-code',
				),
				'advanced-search' => array(
					'slug'    => 'advanced-search',
					'title'   => 'Advanced Search',
					'package' => 'pro-disabled',
					'widget-category' => 'header-footer', // header footer
					'icon'    => 'ekit ekit-search',
				),
				'login-form' => [
					'slug'    => 'login-form',
					'title'   => 'Login Form',
					'package' => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'    => 'ekit ekit-login',
				],
				'register-form' => [
					'slug'    => 'register-form',
					'title'   => 'Register Form',
					'package' => 'pro-disabled',
					'widget-category' => 'general', // general
					'icon'    => 'ekit ekit-register',
				],
				'copyright' => [
					'slug'    => 'copyright',
					'title'   => 'Copyright',
					'package' => 'pro-disabled',
					'widget-category' => 'header-footer', // header footer
					'icon'    => 'ekit ekit-copyright',
				],
			)
		);
	}
}
