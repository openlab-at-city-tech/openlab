<?php
/**
 * Template part for displaying front page introduction.
 *
 * @package Flawless Blog
 */

// Banner Section.
$banner_section = get_theme_mod( 'flawless_blog_banner_section_enable', false );

if ( false === $banner_section ) {
	return;
}

$background_image     = get_theme_mod( 'flawless_blog_banner_image', '' );
$banner_title         = get_theme_mod( 'flawless_blog_banner_title', __( 'Welcome to my blog', 'flawless-blog' ) );
$banner_description   = get_theme_mod( 'flawless_blog_banner_description', '' );
$read_more_button     = get_theme_mod( 'flawless_blog_banner_read_more_button_label', __( 'Read More', 'flawless-blog' ) );
$read_more_button_url = get_theme_mod( 'flawless_blog_banner_read_more_button_url', '' );
?>
<div id="flawless_blog_banner_section" class="frontpage banner-section style-1">
	<div class="theme-wrapper">
		<div class="banner-intro" style="background-image: url(<?php echo esc_url( $background_image ); ?>);">
			<div class="banner-intro-content">
				<?php if ( !empty( $banner_title ) ) { ?>
					<div class="banner-intro-head">
						<h2><?php echo esc_html( $banner_title ); ?></h2>	
					</div>
				<?php } ?>
				<?php if ( !empty( $banner_description ) ) { ?>
					<div class="banner-intro-txt">
						<p>
							<?php echo esc_html( $banner_description ); ?>
							<?php if ( !empty( $read_more_button ) ) { ?>
								<a class="banner-link" href="<?php echo esc_url( $read_more_button_url ); ?>">
									<em><?php echo esc_html( $read_more_button ); ?></em>
									<i class="fas fa-external-link-alt"></i>
								</a>
							<?php } ?>
						</p>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
