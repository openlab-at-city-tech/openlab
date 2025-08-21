<?php
/**
 * Block Filters
 *
 * @package fse_freelancer_portfolio
 * @since 1.0
 */

function fse_freelancer_portfolio_block_wrapper( $fse_freelancer_portfolio_block_content, $fse_freelancer_portfolio_block ) {

	if ( 'core/button' === $fse_freelancer_portfolio_block['blockName'] ) {
		
		if( isset( $fse_freelancer_portfolio_block['attrs']['className'] ) && strpos( $fse_freelancer_portfolio_block['attrs']['className'], 'has-arrow' ) ) {
			$fse_freelancer_portfolio_block_content = str_replace( '</a>', fse_freelancer_portfolio_get_svg( array( 'icon' => esc_attr( 'caret-circle-right' ) ) ) . '</a>', $fse_freelancer_portfolio_block_content );
			return $fse_freelancer_portfolio_block_content;
		}
	}

	if( ! is_single() ) {
	
		if ( 'core/post-terms'  === $fse_freelancer_portfolio_block['blockName'] ) {
			if( 'post_tag' === $fse_freelancer_portfolio_block['attrs']['term'] ) {
				$fse_freelancer_portfolio_block_content = str_replace( '<div class="taxonomy-post_tag wp-block-post-terms">', '<div class="taxonomy-post_tag wp-block-post-terms flex">' . fse_freelancer_portfolio_get_svg( array( 'icon' => esc_attr( 'tags' ) ) ), $fse_freelancer_portfolio_block_content );
			}

			if( 'category' ===  $fse_freelancer_portfolio_block['attrs']['term'] ) {
				$fse_freelancer_portfolio_block_content = str_replace( '<div class="taxonomy-category wp-block-post-terms">', '<div class="taxonomy-category wp-block-post-terms flex">' . fse_freelancer_portfolio_get_svg( array( 'icon' => esc_attr( 'category' ) ) ), $fse_freelancer_portfolio_block_content );
			}
			return $fse_freelancer_portfolio_block_content;
		}
		if ( 'core/post-date' === $fse_freelancer_portfolio_block['blockName'] ) {
			$fse_freelancer_portfolio_block_content = str_replace( '<div class="wp-block-post-date">', '<div class="wp-block-post-date flex">' . fse_freelancer_portfolio_get_svg( array( 'icon' => esc_attr( 'calendar' ) ) ), $fse_freelancer_portfolio_block_content );
			return $fse_freelancer_portfolio_block_content;
		}
		if ( 'core/post-author' === $fse_freelancer_portfolio_block['blockName'] ) {
			$fse_freelancer_portfolio_block_content = str_replace( '<div class="wp-block-post-author">', '<div class="wp-block-post-author flex">' . fse_freelancer_portfolio_get_svg( array( 'icon' => esc_attr( 'user' ) ) ), $fse_freelancer_portfolio_block_content );
			return $fse_freelancer_portfolio_block_content;
		}
	}
	if( is_single() ){

		// Add chevron icon to the navigations
		if ( 'core/post-navigation-link' === $fse_freelancer_portfolio_block['blockName'] ) {
			if( isset( $fse_freelancer_portfolio_block['attrs']['type'] ) && 'previous' === $fse_freelancer_portfolio_block['attrs']['type'] ) {
				$fse_freelancer_portfolio_block_content = str_replace( '<span class="post-navigation-link__label">', '<span class="post-navigation-link__label">' . fse_freelancer_portfolio_get_svg( array( 'icon' => esc_attr( 'prev' ) ) ), $fse_freelancer_portfolio_block_content );
			}
			else {
				$fse_freelancer_portfolio_block_content = str_replace( '<span class="post-navigation-link__label">Next Post', '<span class="post-navigation-link__label">Next Post' . fse_freelancer_portfolio_get_svg( array( 'icon' => esc_attr( 'next' ) ) ), $fse_freelancer_portfolio_block_content );
			}
			return $fse_freelancer_portfolio_block_content;
		}
		if ( 'core/post-date' === $fse_freelancer_portfolio_block['blockName'] ) {
            $fse_freelancer_portfolio_block_content = str_replace( '<div class="wp-block-post-date">', '<div class="wp-block-post-date flex">' . fse_freelancer_portfolio_get_svg( array( 'icon' => 'calendar' ) ), $fse_freelancer_portfolio_block_content );
            return $fse_freelancer_portfolio_block_content;
        }
		if ( 'core/post-author' === $fse_freelancer_portfolio_block['blockName'] ) {
            $fse_freelancer_portfolio_block_content = str_replace( '<div class="wp-block-post-author">', '<div class="wp-block-post-author flex">' . fse_freelancer_portfolio_get_svg( array( 'icon' => 'user' ) ), $fse_freelancer_portfolio_block_content );
            return $fse_freelancer_portfolio_block_content;
        }

	}
    return $fse_freelancer_portfolio_block_content;
}
	
add_filter( 'render_block', 'fse_freelancer_portfolio_block_wrapper', 10, 2 );
