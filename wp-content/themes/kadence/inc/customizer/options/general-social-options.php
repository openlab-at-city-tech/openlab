<?php
/**
 * Header Builder Options
 *
 * @package Kadence
 */

namespace Kadence;

use Kadence\Theme_Customizer;
use function Kadence\kadence;
ob_start(); ?>
<div class="kadence-compontent-description">
<h2><?php echo esc_html__( 'Social Network Links', 'kadence' ); ?></h2>
</div>
<?php
$compontent_description = ob_get_clean();
$settings = array(
	'social_settings' => array(
		'control_type' => 'kadence_blank_control',
		'section'      => 'general_social',
		'settings'     => false,
		'priority'     => 1,
		'description'  => $compontent_description,
	),
	'facebook_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'facebook_link' ),
		'label'        => esc_html__( 'Facebook', 'kadence' ),
	),
	'twitter_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'twitter_link' ),
		'label'        => esc_html__( 'X formerly Twitter', 'kadence' ),
	),
	'threads_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'threads_link' ),
		'label'        => esc_html__( 'Threads', 'kadence' ),
	),
	'instagram_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'instagram_link' ),
		'label'        => esc_html__( 'Instagram', 'kadence' ),
	),
	'youtube_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'youtube_link' ),
		'label'        => esc_html__( 'YouTube', 'kadence' ),
	),
	'vimeo_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'vimeo_link' ),
		'label'        => esc_html__( 'Vimeo', 'kadence' ),
	),
	'facebook_group_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'facebook_group_link' ),
		'label'        => esc_html__( 'Facebook Group', 'kadence' ),
	),
	'pinterest_link' => array(
		'control_type' => 'kadence_text_control',
		'sanitize'     => 'esc_url_raw',
		'section'      => 'general_social',
		'default'      => kadence()->default( 'pinterest_link' ),
		'label'        => esc_html__( 'Pinterest', 'kadence' ),
	),
	'linkedin_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'linkedin_link' ),
		'label'        => esc_html__( 'Linkedin', 'kadence' ),
	),
	'dribbble_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'dribbble_link' ),
		'label'        => esc_html__( 'Dribbble', 'kadence' ),
	),
	'behance_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'behance_link' ),
		'label'        => esc_html__( 'Behance', 'kadence' ),
	),
	'patreon_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'patreon_link' ),
		'label'        => esc_html__( 'Patreon', 'kadence' ),
	),
	'reddit_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'reddit_link' ),
		'label'        => esc_html__( 'Reddit', 'kadence' ),
	),
	'medium_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'medium_link' ),
		'label'        => esc_html__( 'medium', 'kadence' ),
	),
	'wordpress_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'wordpress_link' ),
		'label'        => esc_html__( 'WordPress', 'kadence' ),
	),
	'github_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'github_link' ),
		'label'        => esc_html__( 'GitHub', 'kadence' ),
	),
	'vk_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'vk_link' ),
		'label'        => esc_html__( 'VK', 'kadence' ),
	),
	'xing_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'xing_link' ),
		'label'        => esc_html__( 'Xing', 'kadence' ),
	),
	'rss_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'rss_link' ),
		'label'        => esc_html__( 'RSS', 'kadence' ),
	),
	'google_reviews_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'google_reviews_link' ),
		'label'        => esc_html__( 'Google Reviews', 'kadence' ),
	),
	'yelp_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'yelp_link' ),
		'label'        => esc_html__( 'Yelp', 'kadence' ),
	),
	'trip_advisor_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'trip_advisor_link' ),
		'label'        => esc_html__( 'Trip Advisor', 'kadence' ),
	),
	'imdb_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'imdb_link' ),
		'label'        => esc_html__( 'IMDB', 'kadence' ),
	),
	'whatsapp_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'whatsapp_link' ),
		'label'        => esc_html__( 'WhatsApp', 'kadence' ),
	),
	'telegram_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'telegram_link' ),
		'label'        => esc_html__( 'Telegram', 'kadence' ),
	),
	'soundcloud_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'soundcloud_link' ),
		'label'        => esc_html__( 'SoundCloud', 'kadence' ),
	),
	'tumblr_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'tumblr_link' ),
		'label'        => esc_html__( 'Tumblr', 'kadence' ),
	),
	'tiktok_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'tiktok_link' ),
		'label'        => esc_html__( 'Tiktok', 'kadence' ),
	),
	'discord_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'discord_link' ),
		'label'        => esc_html__( 'Discord', 'kadence' ),
	),
	'spotify_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'spotify_link' ),
		'label'        => esc_html__( 'Spotify', 'kadence' ),
	),
	'apple_podcasts_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'apple_podcasts_link' ),
		'label'        => esc_html__( 'Apple Podcast', 'kadence' ),
	),
	'flickr_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'flickr_link' ),
		'label'        => esc_html__( 'Flickr', 'kadence' ),
	),
	'500px_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( '500px_link' ),
		'label'        => esc_html__( '500PX', 'kadence' ),
	),
	'bandcamp_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'bandcamp_link' ),
		'label'        => esc_html__( 'Bandcamp', 'kadence' ),
	),
	'anchor_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'anchor_link' ),
		'label'        => esc_html__( 'Anchor', 'kadence' ),
	),
	'email_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'sanitize_text_field',
		'default'      => kadence()->default( 'email_link' ),
		'label'        => esc_html__( 'Email', 'kadence' ),
	),
	'phone_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'sanitize_text_field',
		'default'      => kadence()->default( 'phone_link' ),
		'label'        => esc_html__( 'Phone', 'kadence' ),
	),
	'custom1_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'custom1_link' ),
		'label'        => esc_html__( 'Custom 1', 'kadence' ),
	),
	'custom2_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'custom2_link' ),
		'label'        => esc_html__( 'Custom 2', 'kadence' ),
	),
	'custom3_link' => array(
		'control_type' => 'kadence_text_control',
		'section'      => 'general_social',
		'sanitize'     => 'esc_url_raw',
		'default'      => kadence()->default( 'custom3_link' ),
		'label'        => esc_html__( 'Custom 3', 'kadence' ),
	),
);

Theme_Customizer::add_settings( $settings );

