<?php

namespace LottaFramework\Extensions;

class Breadcrumbs {

	/**
	 * Home string
	 *
	 * @var null|string
	 */
	protected $_home_str = null;

	/**
	 * Separator
	 *
	 * @var string
	 */
	protected $_sep = ' > ';

	/**
	 * Link format
	 *
	 * @var string
	 */
	protected $_link = '<a href="%1$s">%2$s</a>';

	/**
	 * Item format
	 *
	 * @var string
	 */
	protected $_item = '<span>%1$s</span>';

	/**
	 * Set home string
	 *
	 * @param $str
	 */
	public function setHomeString( $str ) {
		$this->_home_str = $str;
	}

	/**
	 * Set breadcrumbs separator
	 *
	 * @param $sep
	 */
	public function setSep( $sep ) {
		$this->_sep = $sep;
	}

	/**
	 * Change link format
	 *
	 * @param $format
	 */
	public function setLinkFormat( $format ) {
		$this->_link = $format;
	}

	/**
	 * Change item format
	 *
	 * @param $format
	 */
	public function setItemFormat( $format ) {
		$this->_item = $format;
	}

	/**
	 * Generates the breadcrumbs.
	 *
	 * @return array
	 */
	public function generate() {
		$result = [];

		/* Start the breadcrumb with a link to home */
		$result[ $this->_home_str === null ? get_bloginfo( 'name' ) : $this->_home_str ] = home_url( '/' );

		if ( is_category() || is_single() ) {
			/* show categories and posts */
			if ( is_category() ) {
				$result[ single_term_title( '', false ) ] = false;
			} elseif ( is_single() ) {
				$cats = get_the_category( get_the_ID() );
				$cat  = array_shift( $cats );
				if ( $cat ) {
					$result[ $cat->name ] = get_category_link( $cat->term_id );
				}
			}
		} elseif ( is_archive() || is_single() ) {
			if ( is_day() ) {
				$result[ get_the_date() ] = false;
			} elseif ( is_month() ) {
				$result[ get_the_date( 'F Y' ) ] = false;
			} elseif ( is_year() ) {
				$result[ get_the_date( 'Y' ) ] = false;
			} else {
				$result[ get_the_archive_title() ] = false;
			}
		}

		/* If the current page is a single post or a static page, show its title with the separator */
		if ( is_single() || is_page() ) {
			$result[ the_title( '', '', false ) ] = false;
		}

		/* if you have a static page assigned to be you posts list page. It will find the title of the static page and display it. i.e Home >> Blog */
		if ( is_home() ) {
			global $post;
			$page_for_posts_id = get_option( 'page_for_posts' );
			if ( $page_for_posts_id ) {
				$post = get_page( $page_for_posts_id );
				setup_postdata( $post );
				$result[ the_title( '', '', false ) ] = false;
				rewind_posts();
			}
		}

		return $result;
	}

	/**
	 * Get breadcrumb html string
	 *
	 * @return null|string
	 */
	public function get() {

		if ( is_front_page() ) {
			return null;
		}

		$items = $this->generate();
		$html  = [];

		foreach ( $items as $title => $link ) {
			if ( $link !== false ) {
				$html[] = sprintf( $this->_link, $link, $title );
			} else {
				$html[] = sprintf( $this->_item, $title );
			}
		}

		return implode( $this->_sep, $html );
	}

	/**
	 * Output breadcrumb
	 */
	public function render( $before = '', $after = '' ) {
		$content = $this->get();
		if ( $content ) {
			echo $before . $content . $after;
		}
	}
}
