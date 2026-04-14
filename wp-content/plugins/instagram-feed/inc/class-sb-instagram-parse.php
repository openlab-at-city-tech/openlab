<?php

if (!defined('ABSPATH')) {
	die('-1');
}

/**
 * Class SB_Instagram_Parse
 *
 * The structure of the data coming from the Instagram API is different
 * for the old API vs the new graph API. This class is used to parse
 * whatever structure the data has as well as use this to generate
 * parts of the html used for image sources.
 *
 * @since 2.0/5.0
 */
class SB_Instagram_Parse
{
	/**
	 * @param $post array
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_account_type($post)
	{
		if (isset($post['media_type'])) {
			return 'business';
		} else {
			return 'personal';
		}
	}

	/**
	 * @param $post array
	 *
	 * @return false|int
	 *
	 * @since 2.0/5.0
	 */
	public static function get_timestamp($post)
	{
		$timestamp = 0;
		if (isset($post['created_time'])) {
			$timestamp = $post['created_time'];
		} elseif (isset($post['timestamp'])) {
			// some date formatting functions have trouble with the "T", "+", and extra zeroes added by Instagram.
			$remove_plus = trim(str_replace(array('T', '+', ' 0000'), ' ', $post['timestamp']));
			$timestamp = strtotime($remove_plus);
		}

		return $timestamp;
	}

	/**
	 * Uses the existing data for the individual instagram post to
	 * set the best image sources for each resolution size. Due to
	 * random bugs or just how the API works, different post types
	 * need special treatment.
	 *
	 * @param array $post
	 * @param array $resized_images
	 *
	 * @return array
	 *
	 * @since 2.0/5.0
	 * @since 2.1.3/5.2.3 added 'd' element as a default backup from the API
	 */
	public static function get_media_src_set($post, $resized_images = array())
	{
		$full_size = self::get_media_url($post);
		$media_urls = array(
			'd' => self::get_media_url($post),
			'150' => '',
			'320' => '',
			'640' => ''
		);
		$account_type = isset($post['images']) ? 'personal' : 'business';

		if ($account_type === 'personal') {
			$media_urls['150'] = $post['images']['thumbnail']['url'];
			$media_urls['320'] = $post['images']['low_resolution']['url'];
			$media_urls['640'] = $post['images']['standard_resolution']['url'];
		} else {
			$post_id = self::get_post_id($post);

			$media_urls['640'] = $full_size;
			$media_urls['150'] = $full_size;
			$media_urls['320'] = $full_size;

			// use resized images if exists
			if (
				isset($resized_images[$post_id]['id'])
				&& $resized_images[$post_id]['id'] !== 'pending'
				&& $resized_images[$post_id]['id'] !== 'video'
				&& $resized_images[$post_id]['id'] !== 'error'
			) {
				$extension = isset($resized_images[$post_id]['extension']) ? $resized_images[$post_id]['extension'] : '.jpg';
				if (isset($resized_images[$post_id]['sizes']['full'])) {
					$media_urls['640'] = sbi_get_resized_uploads_url() . $resized_images[$post_id]['id'] . 'full' . $extension;
				}
				if (isset($resized_images[$post_id]['sizes']['low'])) {
					$media_urls['320'] = sbi_get_resized_uploads_url() . $resized_images[$post_id]['id'] . 'low' . $extension;
				}
			}
		}

		return $media_urls;
	}

	/**
	 * Get the media URL for the post.
	 *
	 * @param array  $post Post data.
	 * @param string $resolution Resolution.
	 *
	 * @return string
	 * @since 2.0/5.0
	 */
	public static function get_media_url($post, $resolution = 'lightbox')
	{
		$account_type = isset($post['images']) ? 'personal' : 'business';
		$media_type = isset($post['media_type']) ? $post['media_type'] : 'none';

		if ($account_type === 'personal') {
			return $post['images']['standard_resolution']['url'];
		}

		if (
			$media_type === 'CAROUSEL_ALBUM'
			|| $media_type === 'VIDEO'
			|| $media_type === 'OEMBED'
		) {
			if (isset($post['thumbnail_url'])) {
				return $post['thumbnail_url'];
			} elseif ($media_type === 'CAROUSEL_ALBUM' && isset($post['media_url'])) {
				return $post['media_url'];
			} elseif (isset($post['children'])) {
				$i = 0;
				$full_size = '';
				foreach ($post['children']['data'] as $carousel_item) {
					if ($carousel_item['media_type'] === 'IMAGE' && empty($full_size)) {
						if (isset($carousel_item['media_url'])) {
							$full_size = $carousel_item['media_url'];
						}
					} elseif ($carousel_item['media_type'] === 'VIDEO' && empty($full_size)) {
						if (isset($carousel_item['thumbnail_url'])) {
							$full_size = $carousel_item['thumbnail_url'];
						} else {
							$full_size = self::fetch_carousel_item_media($carousel_item, $post);
						}
					}

					$i++;
				}
				return $full_size;
			} else {
				return self::fetch_single_media($post);
			}
		} else {
			if (isset($post['media_url'])) {
				return $post['media_url'];
			}

			return self::fetch_single_media($post);
		}
	}

	/**
	 * Fetches media URL using SB_Instagram_Single (oEmbed or Media API).
	 *
	 * @param array|string $post_or_permalink Post data array or permalink string.
	 *
	 * @return string Media URL, thumbnail URL, or placeholder.
	 *
	 * @since 6.10.0
	 */
	public static function fetch_single_media($post_or_permalink)
	{
		if (!class_exists('SB_Instagram_Single')) {
			return trailingslashit(SBI_PLUGIN_URL) . 'img/thumb-placeholder.png';
		}

		// Extract permalink and post_data
		$permalink = is_array($post_or_permalink) ? self::fix_permalink(self::get_permalink($post_or_permalink)) : self::fix_permalink($post_or_permalink);
		$post_data = is_array($post_or_permalink) ? $post_or_permalink : array();

		$single = new SB_Instagram_Single($permalink, $post_data);
		$single->init();
		$fetched = $single->get_post();

		// Return media_url (skip .mp4 files) or thumbnail_url
		if (isset($fetched['media_url']) && !empty($fetched['media_url']) && strpos($fetched['media_url'], '.mp4') === false) {
			return $fetched['media_url'];
		}
		if (isset($fetched['thumbnail_url']) && !empty($fetched['thumbnail_url'])) {
			return $fetched['thumbnail_url'];
		}

		return trailingslashit(SBI_PLUGIN_URL) . 'img/thumb-placeholder.png';
	}

	/**
	 * Fetches carousel item media with inherited parent context for Media API.
	 *
	 * Carousel children don't have username fields, so we inherit from parent
	 * to enable Media API for user's own carousel posts.
	 *
	 * @param array $carousel_item Carousel child item data.
	 * @param array $parent_post Parent carousel post data.
	 *
	 * @return string Media URL, thumbnail URL, or placeholder.
	 *
	 * @since 6.10.0
	 */
	protected static function fetch_carousel_item_media($carousel_item, $parent_post)
	{
		// Inherit parent context for Media API to work
		$carousel_item_with_context = $carousel_item;

		// Add username from parent post (enables Media API)
		if (!empty($parent_post['username'])) {
			$carousel_item_with_context['username'] = $parent_post['username'];
		}

		// Add parent ID for tracking
		if (!empty($parent_post['id'])) {
			$carousel_item_with_context['parent_id'] = $parent_post['id'];
		}

		// Fetch via Single class (oEmbed or Media API)
		return self::fetch_single_media($carousel_item_with_context);
	}

	/**
	 * There seems to be occasional bugs with the Instagram API
	 * and permalinks. This corrects it.
	 *
	 * @param string $permalink
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function fix_permalink($permalink)
	{
		if (substr_count($permalink, '/') > 5) {
			$permalink_array = explode('/', $permalink);
			$perm_id = $permalink_array[count($permalink_array) - 2];
			$permalink = 'https://www.instagram.com/p/' . $perm_id . '/';
		}
		return $permalink;
	}

	/**
	 * @param $post array
	 *
	 * @return mixed
	 *
	 * @since 2.0/5.0
	 */
	public static function get_permalink($post)
	{
		if (isset($post['permalink'])) {
			return $post['permalink'];
		}

		return $post['link'];
	}

	/**
	 * @param $post array
	 *
	 * @return mixed
	 *
	 * @since 2.0/5.0
	 */
	public static function get_post_id($post)
	{
		return $post['id'];
	}

	/**
	 * A default can be set in the case that the user doesn't use captions
	 * for posts as this is also used as the alt text for the image.
	 *
	 * @param $post
	 * @param string $default
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_caption($post, $default = '')
	{
		$caption = $default;
		if (!empty($post['caption']) && !is_array($post['caption'])) {
			$caption = $post['caption'];
		} elseif (!empty($post['caption']['text'])) {
			$caption = $post['caption']['text'];
		}

		$video_title = self::get_video_title($post);

		if (!empty($video_title)) {
			$caption = $video_title . '. ' . $caption;
		}

		return $caption;
	}

	/**
	 * New in IG Graph API 10.0. A title for IGTV posts
	 *
	 * @param array $post
	 *
	 * @return string
	 *
	 * @since 2.9/5.12
	 */
	public static function get_video_title($post)
	{
		if (isset($post['video_title'])) {
			return $post['video_title'];
		}

		return '';
	}

	/**
	 * @param array $header_data
	 * @param array $settings
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 * @since 2.2/5.3 added support for a custom avatar in settings
	 */
	public static function get_avatar($header_data, $settings = array('favor_local' => false), $is_header_attr = false)
	{
		if ($is_header_attr) {
			return self::get_avatar_url($header_data);
		}

		if (!empty($settings['customavatar'])) {
			return $settings['customavatar'];
		}

		if (!empty($header_data['local_avatar_url'])) {
			return $header_data['local_avatar_url'];
		}

		if (!empty($header_data['local_avatar']) && is_string($header_data['local_avatar'])) {
			return $header_data['local_avatar'];
		}

		if (SB_Instagram_GDPR_Integrations::doing_gdpr($settings) && !$is_header_attr) {
			return trailingslashit(SBI_PLUGIN_URL) . 'img/thumb-placeholder.png';
		}

		if (isset($header_data['profile_picture'])) {
			return $header_data['profile_picture'];
		}

		if (isset($header_data['profile_picture_url'])) {
			return $header_data['profile_picture_url'];
		}

		if (isset($header_data['user']) && is_array($header_data['user'])) {
			return $header_data['user']['profile_picture'];
		}

		if (isset($header_data['data'])) {
			return $header_data['data']['profile_picture'];
		}

		return '';
	}

	/**
	 * Get the avatar URL from the API response
	 *
	 * @param array $account_info
	 *
	 * @return string
	 *
	 * @since 6.0
	 */
	public static function get_avatar_url($account_info)
	{
		if (isset($account_info['profile_picture'])) {
			return $account_info['profile_picture'];
		} elseif (isset($account_info['profile_picture_url'])) {
			return $account_info['profile_picture_url'];
		} elseif (isset($account_info['user'])) {
			return $account_info['user']['profile_picture'];
		} elseif (isset($account_info['data'])) {
			return $account_info['data']['profile_picture'];
		}

		return '';
	}

	/**
	 * The full name attached to the user account
	 *
	 * @param array $header_data
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_name($header_data)
	{
		if (isset($header_data['name'])) {
			return $header_data['name'];
		} elseif (isset($header_data['data']['full_name'])) {
			return $header_data['data']['full_name'];
		}
		return self::get_username($header_data);
	}

	/**
	 * @param array $header_data
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_username($header_data)
	{
		if (isset($header_data['username'])) {
			return $header_data['username'];
		} elseif (isset($header_data['user'])) {
			return $header_data['user']['username'];
		} elseif (isset($header_data['data'])) {
			return $header_data['data']['username'];
		}
		return '';
	}

	/**
	 * Account bio/description used in header
	 *
	 * @param $header_data
	 *
	 * @return string
	 *
	 * @since 2.0.1/5.0
	 * @since 2.2/5.3 added support for a custom bio in settings
	 */
	public static function get_bio($header_data, $settings = array())
	{
		$customizer = $settings['customizer'];
		if ($customizer) {
			return '{{$parent.getHeaderBio()}}';
		} else {
			if (!empty($settings['custombio'])) {
				return $settings['custombio'];
			} elseif (isset($header_data['data']['bio'])) {
				return $header_data['data']['bio'];
			} elseif (isset($header_data['bio'])) {
				return $header_data['bio'];
			} elseif (isset($header_data['biography'])) {
				return $header_data['biography'];
			}
			return '';
		}
	}

	/**
	 * New in IG Graph API 10.0
	 *
	 * @param array $post
	 *
	 * @return string
	 *
	 * @since 2.9/5.12
	 */
	public static function get_media_product_type($post)
	{
		if (isset($post['media_product_type'])) {
			return strtolower($post['media_product_type']);
		}

		// get media_type and permalink and search for reel in permalink.
		$media_type = self::get_media_type($post);
		$permalink = self::get_permalink($post);
		if ($media_type === 'video' && strpos($permalink, 'https://www.instagram.com/reel/') !== false) {
			return 'reels';
		}

		return 'feed';
	}

	/**
	 * @param $post array
	 *
	 * @return string
	 *
	 * @since 2.0/5.0
	 */
	public static function get_media_type($post)
	{
		if (isset($post['type'])) {
			return $post['type'];
		}

		return strtolower(str_replace('_ALBUM', '', $post['media_type']));
	}
}
