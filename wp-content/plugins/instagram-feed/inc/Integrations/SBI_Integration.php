<?php

namespace InstagramFeed\Integrations;

use InstagramFeed\Builder\SBI_Db;
use InstagramFeed\Builder\SBI_Feed_Builder;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class SBI_Integration
 *
 * @since 6.2.9
 */
class SBI_Integration
{
	/**
	 * Widget CTA
	 *
	 * @param string $type - dropdown or button.
	 * @return string
	 * @since 6.1
	 */
	public static function get_widget_cta($type = 'dropdown')
	{
		$widget_cta_html = '';
		$feeds_list = SBI_Db::elementor_feeds_query();
		ob_start();
		self::get_widget_cta_html($feeds_list, $type);
		$widget_cta_html .= ob_get_contents();
		ob_get_clean();
		return $widget_cta_html;
	}

	/**
	 * CTA HTML
	 *
	 * @param [type] $feeds_list - list of feeds.
	 * @param string $type - dropdown or button.
	 * @return void
	 */
	public static function get_widget_cta_html($feeds_list, $type = 'dropdown')
	{
		$info = self::get_widget_info();
		$feeds_exist = is_array($feeds_list) && sizeof($feeds_list) > 0;

		$cta_heading = $feeds_exist ? $info['cta_header2'] : $info['cta_header'];
		$cta_description = sbi_is_pro_version() ? $info['cta_description_pro'] : $info['cta_description_free'];
		?>
		<div class="sbi-feed-block-cta">
			<div class="sbi-feed-block-cta-img-ctn">
				<div class="sbi-feed-block-cta-img">
					<span><?php echo $info['plugins'][$info['plugin']]['icon']; ?></span>
					<svg class="sbi-feed-block-cta-logo" width="31" height="39" viewBox="0 0 31 39" fill="none"
						 xmlns="http://www.w3.org/2000/svg">
						<path d="M1.62525 18.4447C1.62525 26.7883 6.60827 33.9305 13.3915 35.171L12.9954 36.4252L12.5937 37.6973L13.923 37.5843L18.4105 37.2026L20.0997 37.0589L19.0261 35.7468L18.4015 34.9834C24.7525 33.3286 29.3269 26.4321 29.3269 18.4447C29.3269 9.29016 23.2952 1.53113 15.4774 1.53113C7.65975 1.53113 1.62525 9.2899 1.62525 18.4447Z"
							  fill="#FE544F" stroke="white" stroke-width="1.78661"></path>
						<path fill-rule="evenodd" clip-rule="evenodd"
							  d="M18.5669 8.05676L19.1904 14.4905L25.6512 14.6761L20.9776 19.0216L24.6689 24.3606L18.4503 23.1916L16.5651 29.4104L13.7026 23.8415L7.92284 26.4899L10.1462 20.5199L4.50931 17.6767L10.5435 15.7361L8.8784 9.79176L14.5871 13.0464L18.5669 8.05676Z"
							  fill="white"></path>
					</svg>
				</div>
			</div>
			<h3 class="sbi-feed-block-cta-heading"><?php echo esc_html($cta_heading); ?></h3>

			<?php if ($feeds_exist) : ?>
				<div class="sbi-feed-block-cta-selector">

					<?php if ($type == 'dropdown') : ?>
						<select class="sbi-feed-block-cta-feedselector">
							<option>
								<?php echo sprintf(__('Select %s Feed', 'instagram-feed'), esc_html(ucfirst($info['plugin']))) ?>
							</option>
							<?php foreach ($feeds_list as $feed_id => $feed_name) : ?>
								<option value="<?php echo esc_attr($feed_id) ?>">
									<?php echo esc_html($feed_name) ?>
								</option>
							<?php endforeach ?>
						</select>
					<?php elseif ($type == 'button') : ?>
						<a href="<?php echo esc_url(admin_url('admin.php?page=sbi-feed-builder')) ?>"
						   rel="noopener noreferrer" class="sbi-feed-block-cta-btn">
							<?php echo esc_html__('Create Instagram Feed', 'instagram-feed'); ?>
						</a>
					<?php endif; ?>


					<span>
						<?php
						echo esc_html__('Or create a Feed for', 'instagram-feed');
						unset($info['plugins'][$info['plugin']]);
						?>
						<?php foreach ($info['plugins'] as $name => $plugin) :
							$link = $plugin['active'] ? $plugin['dashboard_link'] : $plugin['link']; ?>
							<a href="<?php echo esc_url($link) ?>" target="_blank" rel="noopener"
							   class="sbi-feed-block-link">
								<?php echo esc_html($name); ?>
							</a>
						<?php endforeach ?>
					</span>
				</div>

			<?php else : ?>
				<a href="<?php echo esc_url(admin_url('admin.php?page=sbi-feed-builder')) ?>" target="_blank"
				   rel="noopener noreferrer" class="sbi-feed-block-cta-btn">
					<?php echo sprintf(__('Create %s Feed', 'instagram-feed'), esc_html(ucfirst($info['plugin']))) ?>
				</a>
			<?php endif; ?>

			<div class="sbi-feed-block-cta-desc">
				<strong><?php echo esc_html__('Did you Know?', 'instagram-feed') ?></strong>
				<span><?php echo $cta_description; ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * Get Widget/Module/Block Info
	 *
	 * @return array
	 * @since 6.2.9
	 * @access public
	 */
	public static function get_widget_info()
	{
		return [
			'plugin' => 'instagram',
			'cta_header' => __('Get started with your first feed from Instagram', 'instagram-feed'),
			'cta_header2' => __('Select an Instagram feed to embed', 'instagram-feed'),
			'cta_description_free' => sprintf(
				esc_html__('You can display feeds of Instagram photos, videos, albums, events and more using the %s.', 'instagram-feed'),
				'<a class="sbi-feed-block-link" href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=elementor&utm_medium=widget&utm_content=proversion" target="_blank">' . esc_html__('Pro version', 'instagram-feed') . '</a>'
			),
			'cta_description_pro' => sprintf(
				esc_html__('You can also add Facebook, Twitter, and YouTube posts into your feed using our %s.', 'instagram-feed'),
				'<a class="sbi-feed-block-link" href="https://smashballoon.com/social-wall/demo/?utm_campaign=instagram-free&utm_source=elementor&utm_medium=widget&utm_content=socialwall" target="_blank">' . esc_html__('Social Wall plugin', 'instagram-feed') . '</a>'
			),
			'plugins' => SBI_Feed_Builder::get_smashballoon_plugins_info()
		];
	}
}
