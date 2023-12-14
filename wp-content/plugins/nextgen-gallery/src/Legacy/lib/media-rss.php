<?php
/**
 * Class to produce Media RSS nodes
 *
 * @author       Vincent Prat
 * @copyright    Copyright 2008-2011
 */
class nggMediaRss {

	public static function add_mrss_alternate_link() {
		echo "<link id='MediaRSS' rel='alternate' type='application/rss+xml' title='NextGEN Gallery RSS Feed' href='" . self::get_mrss_url() . "' />\n";
	}

	/**
	 * Get the URL of the general media RSS
	 */
	public static function get_mrss_url(): string {
		return NGGALLERY_URLPATH . 'xml/media-rss.php';
	}

	/**
	 * Get the URL of a gallery media RSS
	 */
	public static function get_gallery_mrss_url( $gid, $prev_next = false ): string {
		return self::get_mrss_url() . '?' . ( 'gid=' . $gid . ( $prev_next ? '&prev_next=true' : '' ) . '&mode=gallery' );
	}

	/**
	 * Get the URL of the media RSS for last pictures
	 */
	public static function get_last_pictures_mrss_url( $page = 0, $show = 30 ): string {
		return self::get_mrss_url() . '?' . ( 'show=' . $show . '&page=' . $page . '&mode=last_pictures' );
	}

	/**
	 * Get the XML <rss> node corresponding to the last pictures registered
	 *
	 * @param int $page The current page (defaults to 0)
	 * @param int $show The number of pictures to include in one field (default 30)
	 */
	public static function get_last_pictures_mrss( $page = 0, $show = 30 ) {
		$images = nggdb::find_last_images( $page, $show );

		$title       = stripslashes( get_option( 'blogname' ) );
		$description = stripslashes( get_option( 'blogdescription' ) );
		$link        = site_url();
		$prev_link   = ( $page > 0 ) ? self::get_last_pictures_mrss_url( $page-1, $show ) : '';
		$next_link   = count( $images )!=0 ? self::get_last_pictures_mrss_url( $page+1, $show ) : '';

		return self::get_mrss_root_node( $title, $description, $link, $prev_link, $next_link, $images );
	}

	/**
	 * Get the XML <rss> node corresponding to a gallery
	 *
	 * @param $gallery (object) The gallery to include in RSS
	 * @param $prev_gallery (object) The previous gallery to link in RSS (null if none)
	 * @param $next_gallery (object) The next gallery to link in RSS (null if none)
	 */
	public static function get_gallery_mrss( $gallery, $prev_gallery = null, $next_gallery = null ) {

		$ngg_options = nggGallery::get_option( 'ngg_options' );
		// Set sort order value, if not used (upgrade issue)
		$ngg_options['galSort']    = ( $ngg_options['galSort'] ) ? $ngg_options['galSort'] : 'pid';
		$ngg_options['galSortDir'] = ( $ngg_options['galSortDir'] == 'DESC' ) ? 'DESC' : 'ASC';

		$title       = stripslashes( \Imagely\NGG\Display\I18N::translate( $gallery->title ) );
		$description = stripslashes( \Imagely\NGG\Display\I18N::translate( $gallery->galdesc ) );
		$link        = self::get_permalink( $gallery->pageid );
		$prev_link   = ( $prev_gallery != null ) ? self::get_gallery_mrss_url( $prev_gallery->gid, true ) : '';
		$next_link   = ( $next_gallery != null ) ? self::get_gallery_mrss_url( $next_gallery->gid, true ) : '';
		$images      = nggdb::get_gallery( $gallery->gid, $ngg_options['galSort'], $ngg_options['galSortDir'] );

		return self::get_mrss_root_node( $title, $description, $link, $prev_link, $next_link, $images );
	}

	/**
	 * Get the XML <rss> node corresponding to an album
	 *
	 * @param object $album The album to include in RSS
	 */
	public static function get_album_mrss( $album ) {
		$nggdb    = new nggdb();

		$title       = stripslashes( \Imagely\NGG\Display\I18N::translate( $album->name ) );
		$description = '';
		$link        = self::get_permalink( 0 );
		$prev_link   = '';
		$next_link   = '';
		$images      = $nggdb->find_images_in_album( $album->id );

		return self::get_mrss_root_node( $title, $description, $link, $prev_link, $next_link, $images );
	}

	/**
	 * Get the XML <rss> node
	 */
	public static function get_mrss_root_node( $title, $description, $link, $prev_link, $next_link, $images ) {

		if ($prev_link != '' || $next_link != '') {
			$out = "<rss version='2.0' xmlns:media='http://search.yahoo.com/mrss/' xmlns:atom='http://www.w3.org/2005/Atom'>\n";
		} else {
			$out = "<rss version='2.0' xmlns:media='http://search.yahoo.com/mrss/'>\n";
		}

		$out .= "\t<channel>\n";

		$out .= self::get_generator_mrss_node();
		$out .= self::get_title_mrss_node( $title );
		$out .= self::get_description_mrss_node( $description );
		$out .= self::get_link_mrss_node( $link );

		if ($prev_link != '' || $next_link != '') {
			$out .= self::get_self_node( self::get_mrss_url() );
		}
		if ($prev_link!='') {
			$out .= self::get_previous_link_mrss_node( $prev_link );
		}
		if ($next_link!='') {
			$out .= self::get_next_link_mrss_node( $next_link );
		}

		foreach ($images as $image) {
			$out .= self::get_image_mrss_node( $image );
		}

		$out .= "\t</channel>\n";
		$out .= "</rss>\n";

		return $out;
	}

	/**
	 * Get the XML <generator> node
	 */
	public static function get_generator_mrss_node( $indent = "\t\t" ) {
		return $indent . "<generator><![CDATA[NextGEN Gallery [https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/]]]></generator>\n";
	}

	/**
	 * Get the XML <title> node
	 */
	public static function get_title_mrss_node( $title, $indent = "\t\t" ) {
		return $indent . "<title>" . $title . "</title>\n";
	}

	/**
	 * Get the XML <description> node
	 */
	public static function get_description_mrss_node( $description, $indent = "\t\t" ) {
		return $indent . "<description>" . $description . "</description>\n";
	}

	/**
	 * Get the XML <link> node
	 */
	public static function get_link_mrss_node( $link, $indent = "\t\t" ) {
		return $indent . "<link><![CDATA[" . htmlspecialchars( $link ) . "]]></link>\n";
	}

	/**
	 * Get the XML <atom:link self> node
	 */
	public static function get_self_node( $link, $indent = "\t\t" ) {
		return $indent . "<atom:link rel='self' href='" . htmlspecialchars( $link ) . "' type='application/rss+xml' />\n";
	}

	/**
	 * Get the XML <atom:link previous> node
	 */
	public static function get_previous_link_mrss_node( $link, $indent = "\t\t" ) {
		return $indent . "<atom:link rel='previous' href='" . htmlspecialchars( $link ) . "' />\n";
	}

	/**
	 * Get the XML <atom:link next> node
	 */
	public static function get_next_link_mrss_node( $link, $indent = "\t\t" ) {
		return $indent . "<atom:link rel='next' href='" . htmlspecialchars( $link ) . "' />\n";
	}

	/**
	 * Get the XML <item> node corresponding to one single image
	 *
	 * @param $image The image object
	 */
	public static function get_image_mrss_node( $image, $indent = "\t\t" ) {
		$settings = \Imagely\NGG\Settings\Settings::get_instance();
		$storage  = \Imagely\NGG\DataStorage\Manager::get_instance();

		$tags = wp_get_object_terms( $image->pid, 'ngg_tag', 'fields=names' );
		if (is_array( $tags )) {
			$tags = implode( ', ', $tags );
		}

		$title     = html_entity_decode( stripslashes( $image->alttext ) );
		$desc      = html_entity_decode( stripslashes( $image->description ) );
		$image_url = $storage->get_image_url( $image );
		$thumb_url = $storage->get_image_url( $image, 'thumb' );

		$thumbwidth  = 80;
		$thumbheight = 80;
		if (( $dimensions = $storage->get_thumb_dimensions( $image ) )) {
			$thumbwidth  = $dimensions['width'];
			$thumbheight = $dimensions['height'];
		}

		$out  = $indent . "<item>\n";
		$out .= $indent . "\t<title><![CDATA[" . \Imagely\NGG\Display\I18N::translate( $title, 'pic_' . $image->pid . '_alttext' ) . "]]></title>\n";
		$out .= $indent . "\t<description><![CDATA[" . \Imagely\NGG\Display\I18N::translate( $desc, 'pic_' . $image->pid . '_description' ) . "]]></description>\n";
		$out .= $indent . "\t<link><![CDATA[" . \Imagely\NGG\Util\Router::esc_url( $image_url ) . "]]></link>\n";
		$out .= $indent . "\t<guid>image-id:" . $image->pid . "</guid>\n";
		$out .= $indent . "\t<media:content url='" . \Imagely\NGG\Util\Router::esc_url( $image_url ) . "' medium='image' />\n";
		$out .= $indent . "\t<media:title><![CDATA[" . \Imagely\NGG\Display\I18N::translate( $title, 'pic_' . $image->pid . '_alttext' ) . "]]></media:title>\n";
		$out .= $indent . "\t<media:description><![CDATA[" . \Imagely\NGG\Display\I18N::translate( $desc, 'pic_' . $image->pid . '_description' ) . "]]></media:description>\n";
		$out .= $indent . "\t<media:thumbnail url='" . \Imagely\NGG\Util\Router::esc_url( $thumb_url ) . "' width='" . $thumbwidth . "' height='" . $thumbheight . "' />\n";
		$out .= $indent . "\t<media:keywords><![CDATA[" . esc_html( \Imagely\NGG\Display\I18N::translate( $tags ) ) . "]]></media:keywords>\n";
		$out .= $indent . "\t<media:copyright><![CDATA[Copyright (c) " . get_option( "blogname" ) . " (" . site_url() . ")]]></media:copyright>\n";
		$out .= $indent . "</item>\n";

		return $out;
	}

	public static function get_permalink( $page_id ) {
		if ($page_id == 0) {
			$permalink = site_url();
		} else {
			$permalink = get_permalink( $page_id );
		}

		return $permalink;
	}
}