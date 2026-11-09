<?php declare(strict_types=1);

/**
 * Banner markup, included by Banner::render(), which prints banner.css just before it. Escape
 * everything here, at output.
 *
 * Wide where there is room, such as the top of a page, and stacked where it is narrow, such as a
 * settings sidebar: the stylesheet sizes it to its container, not the screen.
 *
 * "inline" stops wp-admin's common.js from moving the notice under the page heading, so it is left
 * off only on a notice hook, where the banner should move like any other notice. The dismiss
 * control is a link so the dismissal is stored server side, without a script; without
 * "is-dismissible", core's makeNoticesDismissible() adds no second, non-persisting button.
 *
 * @var \TEC\Common\Nexcess\Banners\BannerFields $fields
 * @var string $dismiss_url
 * @var bool $inline
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="notice nx-banner<?php echo $inline ? ' inline' : ''; ?>">
	<div class="nx-banner__inner">
		<p class="nx-banner__text">
			<?php echo esc_html( $fields->heading ); ?>
			<span class="nx-banner__accent"><?php echo esc_html( $fields->body ); ?></span>
		</p>
		<?php if ( $fields->ctaLabel !== '' && $fields->ctaUrl !== '' ) : ?>
			<a class="nx-banner__cta" href="<?php echo esc_url( $fields->ctaUrl ); ?>" target="_blank" rel="noopener noreferrer">
				<?php echo esc_html( $fields->ctaLabel ); ?>
				<?php // WordPress core's own string, so it is translated without a text domain of ours. ?>
				<span class="screen-reader-text"><?php echo esc_html__( '(opens in a new tab)' ); ?></span>
			</a>
		<?php endif; ?>
	</div>
	<a class="nx-banner__dismiss" href="<?php echo esc_url( $dismiss_url ); ?>">
		<span class="screen-reader-text"><?php echo esc_html( $fields->dismissLabel ); ?></span>
	</a>
</div>
