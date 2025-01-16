<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Kenta
 */

use LottaFramework\Facades\CZ;
use LottaFramework\Icons\IconsManager;
use LottaFramework\Utils;

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 *
 * @return array
 */
function kenta_body_classes( $classes ) {

	$classes[] = 'kenta-body overflow-x-hidden kenta-form-' . CZ::get( 'kenta_content_form_style' );

	if ( is_page() ) {
		$classes[] = 'kenta-pages';
	}

	if ( is_single() ) {
		$classes[] = 'kenta-single_post';
	}

	if ( kenta_is_woo_shop() ) {
		$classes[] = 'kenta-store';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}

add_filter( 'body_class', 'kenta_body_classes' );

/**
 * Sets the post excerpt length to n words.
 *
 * function tied to the excerpt_length filter hook.
 *
 * @uses filter excerpt_length
 */
function kenta_excerpt_length( $length ) {

	if ( is_admin() || ! kenta_app()->has( 'store.excerpt_length' ) || absint( kenta_app()['store.excerpt_length'] ) <= 0 ) {
		return $length;
	}

	return absint( kenta_app()['store.excerpt_length'] );
}

add_filter( 'excerpt_length', 'kenta_excerpt_length' );

if ( ! function_exists( 'kenta_get_the_archive_title' ) ) {
	/**
	 * Override blogs page title
	 */
	function kenta_get_the_archive_title( $title ) {
		if ( is_home() ) {
			return CZ::get( 'kenta_blogs_archive_header_title' );
		}

		return $title;
	}
}
add_filter( 'get_the_archive_title', 'kenta_get_the_archive_title' );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a option from customizer
 *
 * @return string option from customizer prepended with an ellipsis.
 */
function kenta_excerpt_more( $link ) {
	if ( is_admin() || ! kenta_app()->has( 'store.excerpt_more_text' ) || kenta_app()['store.excerpt_more_text'] === '' ) {
		return $link;
	}

	return kenta_app()['store.excerpt_more_text'];
}

add_filter( 'excerpt_more', 'kenta_excerpt_more' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function kenta_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}

add_action( 'wp_head', 'kenta_pingback_header' );

/**
 * Add selective dynamic css output container
 */
function kenta_add_selective_css_container() {
	?>
    <style id="kenta-preloader-selective-css"></style>
    <style id="kenta-global-selective-css"></style>
    <style id="kenta-woo-selective-css"></style>
    <style id="kenta-header-selective-css"></style>
    <style id="kenta-footer-selective-css"></style>
    <style id="kenta-transparent-selective-css"></style>
	<?php
}

add_action( 'wp_head', 'kenta_add_selective_css_container' );

if ( ! function_exists( 'kenta_add_preloader' ) ) {
	/**
	 * Add global preloader
	 */
	function kenta_add_preloader() {
		if ( CZ::checked( 'kenta_global_preloader' ) ) {
			$preset = CZ::get( 'kenta_preloader_preset' );

			?>
            <div class="kenta-preloader-wrap kenta-preloader-<?php echo esc_attr( $preset ); ?>">
				<?php echo wp_kses_post( kenta_get_preloader( $preset )['html'] ) ?>
            </div>
			<?php
		}
	}
}
add_action( 'kenta_action_before', 'kenta_add_preloader' );

/**
 * Add primary sidebar
 *
 * @param $layout
 */
function kenta_add_primary_sidebar( $layout ) {
	// Include primary sidebar.
	if ( $layout === 'left-sidebar' || $layout === 'right-sidebar' ) {
		get_sidebar();
	}
}

add_action( 'kenta_action_sidebar', 'kenta_add_primary_sidebar' );

/**
 * Site header open
 */
function kenta_add_header_open() {
	$transparent = kenta_is_transparent_header();
	$device      = CZ::get( 'kenta_enable_transparent_header_device' );
	?>
    <header class="<?php Utils::the_clsx( [
		'kenta-site-header text-accent'    => true,
		'kenta-transparent-header'         => $transparent,
		'kenta-header-with-admin-bar'      => is_admin_bar_showing(),
		'kenta-transparent-header-desktop' => $transparent && ( $device === 'all' || $device === 'desktop' ),
		'kenta-transparent-header-mobile'  => $transparent && ( $device === 'all' || $device === 'mobile' ),
	] ); ?>">
	<?php
}

add_action( 'kenta_action_before_header', 'kenta_add_header_open' );
/**
 * Site header closed
 */
function kenta_add_header_close() {
	?>
    </header>
	<?php
}

add_action( 'kenta_action_after_header', 'kenta_add_header_close' );

/**
 * Header render
 */
function kenta_header_render() {
	if ( kenta_get_current_post_meta( 'disable-site-header' ) !== 'yes' ) {

		do_action( 'kenta_before_header_row_render', 'modal' );
		if ( Kenta_Header_Builder::shouldRenderRow( 'modal' ) ) {
			Kenta_Header_Builder::render( 'modal' );
		}
		do_action( 'kenta_after_header_row_render', 'modal' );

		do_action( 'kenta_before_header_row_render', 'top_bar' );
		if ( Kenta_Header_Builder::shouldRenderRow( 'top_bar' ) ) {
			Kenta_Header_Builder::render( 'top_bar' );
		}
		do_action( 'kenta_after_header_row_render', 'top_bar' );

		do_action( 'kenta_before_header_row_render', 'primary_navbar' );
		if ( Kenta_Header_Builder::shouldRenderRow( 'primary_navbar' ) ) {
			Kenta_Header_Builder::render( 'primary_navbar' );
		}
		do_action( 'kenta_after_header_row_render', 'primary_navbar' );

		do_action( 'kenta_before_header_row_render', 'bottom_row' );
		if ( Kenta_Header_Builder::shouldRenderRow( 'bottom_row' ) ) {
			Kenta_Header_Builder::render( 'bottom_row' );
		}
		do_action( 'kenta_after_header_row_render', 'bottom_row' );
	}
}

add_action( 'kenta_action_header', 'kenta_header_render' );

function kenta_header_row_start( $id ) {
	$attrs = [
		'class'    => 'kenta-header-row kenta-header-row-' . $id,
		'data-row' => $id,
	];

	if ( is_customize_preview() ) {
		$attrs['data-shortcut']          = 'border';
		$attrs['data-shortcut-location'] = 'kenta_header:' . $id;
	}

	echo '<div ' . Utils::render_attribute_string( $attrs ) . '>';
}

add_action( 'kenta_start_header_row', 'kenta_header_row_start', 10 );

function kenta_header_row_container_start( $id ) {
	echo '<div class="kenta-max-w-wide has-global-padding container mx-auto text-xs flex flex-wrap items-stretch">';
}

add_action( 'kenta_start_header_row', 'kenta_header_row_container_start', 20 );

function kenta_header_row_close() {
	echo '</div>';
}

// header row
add_action( 'kenta_after_header_row', 'kenta_header_row_close', 10 );
// container
add_action( 'kenta_after_header_row', 'kenta_header_row_close', 20 );

/**
 * Show single post header
 */
function kenta_show_single_post_header( $layout ) {
	if ( CZ::get( 'kenta_post_featured_image_position' ) === 'behind' ) {
		if ( have_posts() ) {
			the_post();
			kenta_show_article_header( 'kenta_single_post', 'post' );
			rewind_posts();
		}
	}
}

add_action( 'kenta_action_before_single_post_container', 'kenta_show_single_post_header', 10, 2 );

/**
 * Show page header
 */
function kenta_show_page_header( $layout ) {
	$header = ! ( is_front_page() && ! is_home() ) || CZ::checked( 'kenta_show_frontpage_header' );
	if ( $header && CZ::get( 'kenta_page_featured_image_position' ) === 'behind' ) {
		if ( have_posts() ) {
			the_post();
			kenta_show_article_header( 'kenta_pages', 'page' );
			rewind_posts();
		}
	}
}

add_action( 'kenta_action_before_page_container', 'kenta_show_page_header', 10, 2 );

/**
 * Show posts pagination
 */
function kenta_show_posts_pagination() {
	global $wp_query;
	$pages = $wp_query->max_num_pages;

	global $paged;
	$paged = empty( $paged ) ? 1 : $paged;

	// Don't print empty markup in archives if there's only one page or pagination is disabled.
	if ( ! CZ::checked( 'kenta_archive_pagination_section' ) ||
	     ( $pages < 2 && ( is_home() || is_archive() || is_search() ) ) ) {
		return;
	}

	$type                 = CZ::get( 'kenta_pagination_type' );
	$show_disabled_button = CZ::checked( 'kenta_pagination_disabled_button' );

	$css = apply_filters( 'kenta_pagination_css', [
		'kenta-pagination'    => true,
		'kenta-scroll-reveal' => CZ::checked( 'kenta_pagination_scroll_reveal' )
	], $type );

	$pagination_attrs = [
		'class'                     => Utils::clsx( $css ),
		'data-pagination-type'      => $type,
		'data-pagination-max-pages' => $pages,
	];

	if ( is_customize_preview() ) {
		$pagination_attrs['data-shortcut']          = 'border';
		$pagination_attrs['data-shortcut-location'] = 'kenta_archive:kenta_archive_pagination_section';
	}

	$btn_class          = 'kenta-btn';
	$current_btn_class  = $btn_class . ' kenta-btn-active';
	$disabled_btn_class = $btn_class . ' kenta-btn-disabled';

	$show_previous_button = function ( $disabled = false ) use ( $paged, $btn_class, $disabled_btn_class ) {
		$prev_type = CZ::get( 'kenta_pagination_prev_next_type' );

		if ( $disabled ) {
			echo '<span class="' . esc_attr( $disabled_btn_class . ' kenta-prev-btn kenta-prev-btn-' . $prev_type ) . '">';
		} else {
			echo '<a href="' . esc_url( get_pagenum_link( $paged - 1, true ) ) .
			     '" class="' . esc_attr( $btn_class . ' kenta-prev-btn kenta-prev-btn-' . $prev_type ) . '">';
		}

		if ( $prev_type === 'text' ) {
			echo '<span>' . esc_html( CZ::get( 'kenta_pagination_prev_text' ) ) . '</span>';
		} else {
			IconsManager::print( CZ::get( 'kenta_pagination_prev_icon' ) );
		}

		echo $disabled ? '</span>' : '</a>';
	};

	$show_next_button = function ( $disabled = false ) use ( $paged, $btn_class, $disabled_btn_class ) {
		$next_type = CZ::get( 'kenta_pagination_prev_next_type' );

		if ( $disabled ) {
			echo '<span class="' . esc_attr( $disabled_btn_class . ' kenta-next-btn kenta-next-btn-' . $next_type ) . '">';
		} else {
			echo '<a href="' . esc_url( get_pagenum_link( $paged + 1, true ) ) .
			     '" class="' . esc_attr( $btn_class . ' kenta-next-btn kenta-next-btn-' . $next_type ) . '">';
		}

		echo '<span>';
		if ( $next_type === 'text' ) {
			esc_html_e( CZ::get( 'kenta_pagination_next_text' ) );
		} else {
			IconsManager::print( CZ::get( 'kenta_pagination_next_icon' ) );
		}
		echo '</span>';

		echo $disabled ? '</span>' : '</a>';
	};

	echo '<nav ' . Utils::render_attribute_string( $pagination_attrs ) . '>';

	if ( 'prev-next' === $type ) {

		// Show previous button
		if ( $paged > 1 ) {
			$show_previous_button();
		} elseif ( $show_disabled_button ) {
			$show_previous_button( true );
		}

		// Show next button
		if ( $paged < $pages ) {
			$show_next_button();
		} elseif ( $show_disabled_button ) {
			$show_next_button( true );
		}

	} elseif ( 'numbered' === $type ) {
		$range     = 2;
		$showitems = ( $range * 2 ) + 1;

		// Show previous button
		if ( CZ::checked( 'kenta_pagination_prev_next_button' ) ) {
			if ( $paged > 1 ) {
				$show_previous_button();
			} elseif ( $show_disabled_button ) {
				$show_previous_button( true );
			}
		}

		// Show numeric buttons
		for ( $i = 1; $i <= $pages; $i ++ ) {
			if ( 1 !== $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
				if ( $paged === $i ) {
					echo '<span class="' . esc_attr( $current_btn_class ) . '">' . $i . '</span>';
				} else {
					echo '<a class="' . esc_attr( $btn_class ) . '" href="' . esc_url( get_pagenum_link( $i, true ) ) . '">' . $i . '</a>';
				}
			}
		}

		// Show next button
		if ( CZ::checked( 'kenta_pagination_prev_next_button' ) ) {
			if ( $paged < $pages ) {
				$show_next_button();
			} elseif ( $show_disabled_button ) {
				$show_next_button( true );
			}
		}
	}

	do_action( 'kenta_show_pagination', $type, $pages, $paged );

	echo '</nav>';
}

add_action( 'kenta_action_posts_pagination', 'kenta_show_posts_pagination' );

/**
 * Show page content
 */
function kenta_show_page_content( $layout ) {
	$is_behind = CZ::get( 'kenta_page_featured_image_position' ) === 'behind';

	kenta_show_article( 'kenta_pages', 'page', ! $is_behind );
}

add_action( 'kenta_action_page', 'kenta_show_page_content', 10, 2 );

/**
 * Show single post content
 */
function kenta_show_single_post_content( $layout ) {
	$is_behind = CZ::get( 'kenta_post_featured_image_position' ) === 'behind';

	kenta_show_article( 'kenta_single_post', 'post', ! $is_behind );
}

add_action( 'kenta_action_single_post', 'kenta_show_single_post_content', 10, 2 );

/**
 * Show share box
 */
function kenta_add_post_share_box() {
	if ( is_page() && ! is_front_page() && CZ::checked( 'kenta_page_share_box' ) ) {
		kenta_show_share_box( 'page', 'kenta_pages:kenta_page_share_box' );
	}

	if ( is_single() && CZ::checked( 'kenta_post_share_box' ) ) {
		kenta_show_share_box( 'post', 'kenta_single_post:kenta_post_share_box' );
	}
}

add_action( 'kenta_action_after_single_post', 'kenta_add_post_share_box', 10 );
add_action( 'kenta_action_after_page', 'kenta_add_post_share_box', 10 );

function kenta_add_post_author_bio() {
	if ( ! CZ::checked( 'kenta_post_author_bio' ) ) {
		return;
	}

	$user_id = null;
	$obj     = get_queried_object();
	if ( is_author() ) {
		$user_id = $obj->data->ID;
	} elseif ( is_single() ) {
		$user_id = $obj->post_author;
	}

	if ( ! $user_id ) {
		return;
	}

	$attrs = [
		'class' => 'kenta-max-w-content has-global-padding mx-auto',
	];

	if ( is_customize_preview() ) {
		$attrs['data-shortcut']          = 'border';
		$attrs['data-shortcut-location'] = 'kenta_single_post:kenta_post_author_bio';
	}

	$author_posts_url = get_author_posts_url( get_the_author_meta( 'ID', $user_id ) );
	?>
    <div <?php Utils::print_attribute_string( $attrs ); ?>>
        <div class="kenta-about-author-bio-box">
			<?php
			if ( CZ::checked( 'kenta_post_author_bio_avatar' ) ) {
				/**
				 * Filter the author bio avatar size.
				 *
				 * @param int $size the avatar height and width size in pixels
				 *
				 */
				$author_bio_avatar_size = apply_filters( 'kenta_filter_author_bio_avatar_size', absint( CZ::get( 'kenta_post_author_bio_avatar_size' ) ) );

				$author_bio_avatar_link = CZ::checked( 'kenta_post_author_bio_avatar_link' );
				if ( $author_bio_avatar_link ) {
					echo '<a href="' . esc_url( $author_posts_url ) . '" class="kenta-author-bio-avatar-link">';
				}

				echo get_avatar( get_the_author_meta( 'user_email', $user_id ), absint( $author_bio_avatar_size ), '', '', [
					'class' => 'kenta-author-bio-avatar'
				] );

				if ( $author_bio_avatar_link ) {
					echo '</a>';
				}
			}
			?>

			<?php
			$name_prefix = CZ::get( 'kenta_post_author_bio_name_prefix' );

			echo '<h5 class="kenta-author-bio-name">';
			echo esc_html( $name_prefix ) . ( ! empty( $name_prefix ) ? ' ' : '' );
			echo '<span class="kenta-author-bio-display-name">';
			echo esc_html( get_the_author_meta( 'display_name', $user_id ) );
			echo '</span>';
			echo '</h5>';
			?>

			<?php if ( CZ::checked( 'kenta_post_author_bio_all_articles_link' ) ): ?>
                <a class="kenta-author-bio-all-articles" href="<?php echo esc_url( $author_posts_url ); ?>"
                   rel="author">
					<?php echo CZ::get( 'kenta_post_author_bio_all_articles_text' ) ?>
                </a>
			<?php endif; ?>

            <p class="kenta-author-bio">
				<?php the_author_meta( 'description', $user_id ); ?>
            </p>
        </div>
    </div>
	<?php
}

add_action( 'kenta_action_after_single_post', 'kenta_add_post_author_bio', 5 );

/**
 * Show posts navigation
 */
function kenta_add_post_navigation() {
	if ( ! CZ::checked( 'kenta_post_navigation' ) ) {
		return;
	}

	$attrs = [
		'class' => 'kenta-max-w-content has-global-padding mx-auto',
	];

	if ( is_customize_preview() ) {
		$attrs['data-shortcut']          = 'border';
		$attrs['data-shortcut-location'] = 'kenta_single_post:kenta_post_navigation';
	}

	echo '<div ' . Utils::render_attribute_string( $attrs ) . '>';
	echo '<div class="kenta-post-navigation">';

	$fallback_image = CZ::hasImage( 'kenta_post_featured_image_fallback' )
		? '<img class="wp-post-image" ' . Utils::render_attribute_string( CZ::imgAttrs( 'kenta_post_featured_image_fallback' ) ) . ' />'
		: '';

	$prev_post = get_previous_post();

	$prev_thumbnail = $fallback_image;
	$next_thumbnail = $fallback_image;

	if ( has_post_thumbnail( $prev_post ? $prev_post->ID : null ) ) {
		$prev_thumbnail = get_the_post_thumbnail( $prev_post ? $prev_post->ID : null, 'medium' );
	}

	$prev_thumbnail = '<div class="prev-post-thumbnail post-thumbnail">' .
	                  $prev_thumbnail .
	                  IconsManager::render( CZ::get( 'kenta_post_navigation_prev_icon' ) ) .
	                  '</div>';

	$next_post = get_next_post();
	if ( has_post_thumbnail( $next_post ? $next_post->ID : null ) ) {
		$next_thumbnail = get_the_post_thumbnail( $next_post ? $next_post->ID : null, 'medium' );
	}

	$next_thumbnail = '<div class="next-post-thumbnail post-thumbnail">' .
	                  $next_thumbnail .
	                  IconsManager::render( CZ::get( 'kenta_post_navigation_next_icon' ) ) .
	                  '</div>';

	the_post_navigation( [
		'prev_text'          => $prev_thumbnail . '<div class="item-wrap pl-gutter lg:pr-2"><span class="item-label">' . esc_html__( 'Previous Post', 'kenta' ) . '</span><span class="item-title">%title</span></div>',
		'next_text'          => $next_thumbnail . '<div class="item-wrap pr-gutter lg:pl-2"><span class="item-label">' . esc_html__( 'Next Post', 'kenta' ) . '</span><span class="item-title">%title</span></div>',
		'screen_reader_text' => '<span class="nav-subtitle screen-reader-text">' . esc_html__( 'Page', 'kenta' ) . '</span>',
	] );

	echo '</div>';
	echo '</div>';
}

add_action( 'kenta_action_after_single_post', 'kenta_add_post_navigation', 10 );

/**
 * Show post comments
 */
function kenta_show_post_comments() {
	// If comments are open, or we have at least one comment, load up the comment template.
	if ( ( comments_open() || get_comments_number() ) ) {
		comments_template();
	}
}

add_action( 'kenta_action_after_page', 'kenta_show_post_comments', 30 );
add_action( 'kenta_action_after_single_post', 'kenta_show_post_comments', 30 );

/**
 * Footer open
 */
function kenta_footer_open() {
	?>
    <footer class="kenta-footer-area text-accent">
	<?php
}

add_action( 'kenta_action_before_footer', 'kenta_footer_open' );


/**
 * Footer render
 */
function kenta_footer_render() {
	if ( kenta_get_current_post_meta( 'disable-site-footer' ) !== 'yes' ) {
		$rows = [ 'top', 'middle', 'bottom' ];

		foreach ( $rows as $row ) {
			if ( Kenta_Footer_Builder::shouldRenderRow( $row ) ) {
				Kenta_Footer_Builder::render( $row, function ( $css, $args ) {
					$css[] = 'flex';

					return $css;
				} );
			}
		}
	}
}

add_action( 'kenta_action_footer', 'kenta_footer_render' );

/**
 * Close footer
 */
function kenta_footer_close() {
	?>
    </footer>
	<?php
}

add_action( 'kenta_action_after_footer', 'kenta_footer_close' );

/**
 * Header & footer elements hook
 */
if ( ! function_exists( 'kenta_render_breadcrumbs' ) ) {
	function kenta_render_breadcrumbs() {
		\LottaFramework\Facades\Breadcrumbs::render();
	}
}
add_action( 'kenta_render_breadcrumbs', 'kenta_render_breadcrumbs' );

if ( ! function_exists( 'kenta_custom_theme_layout' ) ) {
	/**
	 * Modify the theme's JSON data by updating the theme's layout
	 * based on the Customizer value
	 *
	 * @param object $theme_json The original theme JSON data.
	 *
	 * @return object The modified theme JSON data.
	 *
	 * @since 2.0.0
	 */
	function kenta_custom_theme_layout( $theme_json ) {
		// Site wide size
		$wide_size    = '1140px';
		$option_type  = kenta_current_option_type();
		$content_size = CZ::get( 'kenta_' . $option_type . '_container_max_width' );
		if ( ! $content_size // without custom width
		     || Utils::str_ends_with( $content_size, 'ch' ) ) { // drop support for 'ch' unit
			$content_size = '720px';
		}

		$new_data = array(
			'version'  => 2,
			'settings' => array(
				'layout' => array(
					'contentSize' => $content_size,
					'wideSize'    => $wide_size,
				),
			)
		);

		return $theme_json->update_with( $new_data );
	}
}
add_filter( 'wp_theme_json_data_theme', 'kenta_custom_theme_layout' );
