<?php
/**
 * The template for displaying Breadcrumb for KB Article or KB Category Archive page.
 *
 * This template can be overridden by copying it to yourtheme/kb_templates/feature-breadcrumb.php.
 *
 * HOWEVER, on occasion Echo Plugins will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author        Echo Plugins
 *
 */
/** @var WP_Post | WP_Term $article */
/** @var EPKB_KB_Config_DB $kb_config */

$article_rec = empty( $article ) || !$article instanceof WP_Post ? null : $article;
$term_rec = empty( $article ) || !$article instanceof WP_Term ? null : $article;
if ( empty( $article_rec ) && empty( $term_rec ) ) {
	return;
}

// setup KB Main Page link
$kb_main_page_url = '';
if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
	$current_lang = apply_filters( 'wpml_current_language', NULL );

	foreach ( $kb_config['kb_main_pages'] as $post_id => $post_title ) {
		if ( empty( $post_id ) ) {
			continue;
		}

		$args = array('element_id' => $post_id, 'element_type' => 'page');
		$kb_main_page_lang = apply_filters( 'wpml_element_language_code', null, $args );
		if ( $kb_main_page_lang == $current_lang ) {
			$post_url = get_permalink( $post_id );
			$kb_main_page_url = is_wp_error( $post_url ) ? '' : $post_url;
			break;
		}
	}

} else {
	$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
}

// setup breadcrumb links
$breadcrumb = array( $kb_config['breadcrumb_home_text'] => $kb_main_page_url );

// breadcrumb for the article
if ( $article_rec ) {

	$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $kb_config, $article_rec->ID );

	foreach ( $breadcrumb_tree as $category_id => $category_name ) {
		$term_link = EPKB_Utilities::get_term_url( $category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_config['id'] ) );
		$breadcrumb += array($category_name => $term_link);
	}

	$breadcrumb += array( $article_rec->post_title => '#' );
}

// breadcrumb for the category
if ( $term_rec ) {

	$breadcrumb_tree = EPKB_Templates_Various::get_term_breadcrumb( $kb_config, $term_rec->term_id );

	foreach ( $breadcrumb_tree as $category_id ) {
		$term = get_term( $category_id, $term_rec->taxonomy );
		if ( empty( $term ) || is_wp_error( $term ) || !property_exists( $term, 'name' ) ) {
			continue;
		}

		$term_link = EPKB_Utilities::get_term_url( $category_id, $term_rec->taxonomy );
		$breadcrumb += array( $term->name => $term_link );
	}

	$breadcrumb += array( $term_rec->name => '#' );
}


//Saved Setting values
$breadcrumb_style1_escaped = EPKB_Utilities::get_inline_style( '
							padding-top::    breadcrumb_padding_top, 
							padding-right:: breadcrumb_padding_right,
							padding-bottom:: breadcrumb_padding_bottom, 
							padding-left:: breadcrumb_padding_left,
							margin-top::    breadcrumb_margin_top, 
							margin-right:: breadcrumb_margin_right,
							margin-bottom:: ' . ( isset( $kb_config['use_old_margin_bottom'] ) && $kb_config['use_old_margin_bottom'] ? 'breadcrumb_margin_bottom_old' : 'breadcrumb_margin_bottom' ) . ', ' . '
							margin-left:: breadcrumb_margin_left,
							typography::breadcrumb_typography', $kb_config );
$breadcrumb_style2_escaped = EPKB_Utilities::get_inline_style( 'color:: breadcrumb_text_color', $kb_config );
$breadcrumb_style3_escaped = EPKB_Utilities::get_inline_style( 'typography::breadcrumb_typography', $kb_config ); ?>

	<div class="eckb-breadcrumb" <?php echo $breadcrumb_style1_escaped; ?>>    <?php    //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( !empty( $kb_config['breadcrumb_description_text'] ) || !empty( $_REQUEST['epkb-editor-page-loaded'] ) ) { ?>
			<div class="eckb-breadcrumb-label">
				<?php echo esc_html( $kb_config['breadcrumb_description_text'] ); ?>
			</div>    <?php
		} ?>
		<nav class="eckb-breadcrumb-outline" aria-label="<?php esc_html_e( 'Breadcrumb', 'echo-knowledge-base' ); ?>">
			<ul class="eckb-breadcrumb-nav">       <?php
				$ix = 0;
				foreach ( $breadcrumb as $text => $link ) {

					echo '<li ' . $breadcrumb_style3_escaped . '>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '	<span class="eckb-breadcrumb-link">';

					$ix++;
					$text = empty( $text ) && $ix == 1 ? esc_html__( 'KB Home', 'echo-knowledge-base' ) : $text;
					$text = empty( $text ) && $ix > 1 ? esc_html__( 'Link ', 'echo-knowledge-base' ) . ( $ix - 1 ) : $text;

					// output URL if not the last crumb
					if ( $ix < sizeof( $breadcrumb ) ) {
						if ( empty( $link ) ) {
							echo '<span ' .  $breadcrumb_style2_escaped . ' >' . esc_html( $text ) . '</span>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo '<a tabindex="0" href="' . esc_url( $link ) . '"><span ' . $breadcrumb_style2_escaped . ' >' . esc_html( $text ) . '</span></a>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						echo '<span class="eckb-breadcrumb-link-icon ' . esc_html( $kb_config['breadcrumb_icon_separator'] ) . '" aria-hidden="true"></span>';
					} else {
						echo '<span aria-current="page"' . $breadcrumb_style2_escaped . ' >' . esc_html( $text ) . '</span>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

					echo '	</span>';
					echo '</li>';

				} ?>
			</ul>
		</nav>

	</div>          <?php
