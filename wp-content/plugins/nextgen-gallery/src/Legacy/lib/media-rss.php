<?php
/**
 * Class to produce Media RSS nodes
 *
 * @author       Vincent Prat
 * @copyright    Copyright 2008-2011
 */
class nggMediaRss {

	public static function add_mrss_alternate_link() {
		echo "<link id='MediaRSS' rel='alternate' type='application/rss+xml' title='NextGEN Gallery RSS Feed' href='" . esc_url( self::get_mrss_url() ) . "' />\n";
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

		$title       = self::decode_stored( stripslashes( get_option( 'blogname' ) ) );
		$description = self::decode_stored( stripslashes( get_option( 'blogdescription' ) ) );
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

		$title       = self::decode_stored( stripslashes( \Imagely\NGG\Display\I18N::translate( $gallery->title ?? '' ) ) );
		$description = self::decode_stored( stripslashes( \Imagely\NGG\Display\I18N::translate( $gallery->galdesc ?? '' ) ) );
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

		$title       = self::decode_stored( stripslashes( \Imagely\NGG\Display\I18N::translate( $album->name ?? '' ) ) );
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
	 * Wrap a value in a CDATA section that no stored value can terminate early.
	 *
	 * "]]>" is the only sequence able to close a CDATA section, so a value carrying
	 * one would end the section and have its remainder parsed as markup — which is
	 * element-breakout, and an XHTML-namespaced <script> breaking out of an element
	 * executes in both Chromium and Firefox. Splitting the sequence across two
	 * adjacent sections keeps the text byte-identical to the reader while leaving no
	 * terminator in the output.
	 *
	 * @param string $value Raw value.
	 * @return string CDATA-wrapped value.
	 */
	private static function cdata( $value ) {
		return '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', (string) $value ) . ']]>';
	}

	/**
	 * Decode a stored text column back to the characters the user actually typed.
	 *
	 * These columns reach this file already entity-encoded. For most of them that is
	 * the model-layer filter: Gallery::validation() (title), Image::validation()
	 * (alttext, description) and Album::validation() (name, albumdesc) all run
	 * Sanitizer::strip_html( $v, true ), and HTMLPurifier serialises a bare "&" back
	 * out as "&amp;". blogname and blogdescription arrive encoded from WordPress
	 * itself. One exception: Gallery::validation() does not filter galdesc, so that
	 * column is whatever was written to it — it is decoded here for consistency with
	 * how the feed used to render it, not because a filter encoded it.
	 *
	 * Nothing downstream decodes — I18N::translate() only runs the WPML filters — so
	 * publishing these columns verbatim shows every reader "&amp;" where the gallery
	 * is named "Mum & Dad". Before this file wrapped its nodes in CDATA, raw string
	 * concatenation meant the XML parser resolved those entities on the reader's
	 * behalf; CDATA makes them literal, so the decode is what preserves that.
	 *
	 * That symptom class is what #493 reported on the SimpleLightbox caption path
	 * (fixed by PR #494, whose test plan enumerates the characters that must render as
	 * text); the image render paths grew their own decoders for it, in
	 * I18N::ngg_sanitize_text_alt_title_desc(). This feed never did.
	 *
	 * This is safe only because every caller hands the result to cdata(): decoding can
	 * produce a literal "]]>", and cdata() splits that across two sections, so the
	 * decoded value cannot terminate its own section or become markup. Decode first,
	 * wrap second — never decode a value that is not about to be CDATA-wrapped.
	 *
	 * Flags are explicit because the implicit default was ENT_COMPAT before PHP 8.1,
	 * which leaves &#039; undecoded on the PHP 7.4 this plugin still supports.
	 *
	 * @param string $value Stored, entity-encoded value.
	 * @return string Decoded value.
	 */
	private static function decode_stored( $value ) {
		return html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * Escape a value for use inside a single-quoted XML attribute.
	 *
	 * htmlspecialchars()'s default flags only became ENT_QUOTES in PHP 8.1, and this
	 * plugin supports PHP 7.4, where the default ENT_COMPAT leaves the single quote
	 * that closes these attributes unescaped. The flag is therefore passed explicitly.
	 *
	 * @param string $value Raw value.
	 * @return string Attribute-safe value.
	 */
	private static function attr( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES );
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
		return $indent . '<title>' . self::cdata( $title ) . "</title>\n";
	}

	/**
	 * Get the XML <description> node
	 */
	public static function get_description_mrss_node( $description, $indent = "\t\t" ) {
		return $indent . '<description>' . self::cdata( $description ) . "</description>\n";
	}

	/**
	 * Get the XML <link> node
	 */
	public static function get_link_mrss_node( $link, $indent = "\t\t" ) {
		return $indent . '<link>' . self::cdata( $link ) . "</link>\n";
	}

	/**
	 * Get the XML <atom:link self> node
	 */
	public static function get_self_node( $link, $indent = "\t\t" ) {
		return $indent . "<atom:link rel='self' href='" . self::attr( $link ) . "' type='application/rss+xml' />\n";
	}

	/**
	 * Get the XML <atom:link previous> node
	 */
	public static function get_previous_link_mrss_node( $link, $indent = "\t\t" ) {
		return $indent . "<atom:link rel='previous' href='" . self::attr( $link ) . "' />\n";
	}

	/**
	 * Get the XML <atom:link next> node
	 */
	public static function get_next_link_mrss_node( $link, $indent = "\t\t" ) {
		return $indent . "<atom:link rel='next' href='" . self::attr( $link ) . "' />\n";
	}

	/**
	 * Get the XML <item> node corresponding to one single image
	 *
	 * @param $image The image object
	 */
	public static function get_image_mrss_node( $image, $indent = "\t\t" ) {
		$settings = \Imagely\NGG\Settings\Settings::get_instance();
		$storage  = \Imagely\NGG\DataStorage\Manager::get_instance();

		// Handle both object and array formats
		if ( is_array( $image ) ) {
			$image = (object) $image;
		}

		// Ensure we have a valid image object with required properties
		if ( ! is_object( $image ) || ! isset( $image->pid ) ) {
			return ''; // Skip invalid images
		}

		$image_id = $image->pid;
		$tags = wp_get_object_terms( $image_id, 'ngg_tag', 'fields=names' );
		if (is_array( $tags )) {
			$tags = implode( ', ', $tags );
		}

		// Decoded here and CDATA-wrapped below. The decode on its own was the original
		// defect: it revived an entity-encoded "]]&gt;" into a live CDATA terminator,
		// which is how a value that had passed Sanitizer::strip_html() still broke out.
		// cdata() now splits any "]]>" the decode produces, so the pair is inert while
		// the feed still shows "&" rather than "&amp;" — see decode_stored().
		$title     = isset( $image->alttext ) ? self::decode_stored( stripslashes( $image->alttext ) ) : '';
		$desc      = isset( $image->description ) ? self::decode_stored( stripslashes( $image->description ) ) : '';
		$image_url = $storage->get_image_url( $image );
		$thumb_url = $storage->get_image_url( $image, 'thumb' );

		// Get thumbnail dimensions from meta_data or use defaults
		$thumbwidth  = 80;
		$thumbheight = 80;
		$thumb_dims = $storage->get_image_dimensions( $image, 'thumb' );
		if ( $thumb_dims && isset( $thumb_dims['width'] ) && isset( $thumb_dims['height'] ) ) {
			$thumbwidth  = (int) $thumb_dims['width'];
			$thumbheight = (int) $thumb_dims['height'];
		} elseif ( isset( $image->meta_data ) && is_array( $image->meta_data ) ) {
			if ( isset( $image->meta_data['thumbnail']['width'] ) && isset( $image->meta_data['thumbnail']['height'] ) ) {
				$thumbwidth  = (int) $image->meta_data['thumbnail']['width'];
				$thumbheight = (int) $image->meta_data['thumbnail']['height'];
			}
		}

		$translated_title = \Imagely\NGG\Display\I18N::translate( $title, 'pic_' . $image_id . '_alttext' );
		$translated_desc  = \Imagely\NGG\Display\I18N::translate( $desc, 'pic_' . $image_id . '_description' );

		$out  = $indent . "<item>\n";
		$out .= $indent . "\t<title>" . self::cdata( $translated_title ) . "</title>\n";
		$out .= $indent . "\t<description>" . self::cdata( $translated_desc ) . "</description>\n";
		$out .= $indent . "\t<link>" . self::cdata( \Imagely\NGG\Util\Router::esc_url( $image_url ) ) . "</link>\n";
		$out .= $indent . "\t<guid>image-id:" . (int) $image_id . "</guid>\n";
		// Not wrapped in attr(): Router::esc_url() already returns an attribute-safe,
		// entity-encoded URL, and escaping it again turns every "&" in a query string
		// into "&amp;#038;" — which reaches the consumer as a literal "&#038;" and
		// breaks dynamic-thumbnail and CDN image URLs.
		$out .= $indent . "\t<media:content url='" . \Imagely\NGG\Util\Router::esc_url( $image_url ) . "' medium='image' />\n";
		$out .= $indent . "\t<media:title>" . self::cdata( $translated_title ) . "</media:title>\n";
		$out .= $indent . "\t<media:description>" . self::cdata( $translated_desc ) . "</media:description>\n";
		$out .= $indent . "\t<media:thumbnail url='" . \Imagely\NGG\Util\Router::esc_url( $thumb_url ) . "' width='" . $thumbwidth . "' height='" . $thumbheight . "' />\n";
		$out .= $indent . "\t<media:keywords>" . self::cdata( \Imagely\NGG\Display\I18N::translate( $tags ) ) . "</media:keywords>\n";
		$out .= $indent . "\t<media:copyright>" . self::cdata( 'Copyright (c) ' . self::decode_stored( get_option( 'blogname' ) ) . ' (' . site_url() . ')' ) . "</media:copyright>\n";
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
