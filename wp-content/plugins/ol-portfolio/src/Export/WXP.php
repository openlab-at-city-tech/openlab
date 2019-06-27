<?php
/**
 * Generates the WXR export file.
 */

namespace OpenLab\Portfolio\Export;

/**
 * Based on export_wxp().
 *
 * @see https://developer.wordpress.org/reference/functions/export_wp/
 */
class WXP {

	/**
	 * WXP version.
	 */
	const VERSION = '1.2';

	/**
	 * Exported WXP filename.
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * Initialize Export.
	 *
	 * @param string $export_dir
	 */
	public function __construct( $filename ) {
		$this->filename = $filename;
	}

	/**
	 * Create WXP export file.
	 *
	 * @return void
	 */
	public function create() {
		$header = $this->add_header();

		if ( ! $header ) {
			return false;
		}

		// Continue exporting.
		$this->add_authors();
		$this->add_categories();
		$this->add_tags();
		$this->add_terms();
		$this->add_menus();
		$this->add_content();

		return true;
	}

	/**
	 * Creat export file with required header.
	 *
	 * @return bool
	 */
	protected function add_header() {
		$wxr_version = static::VERSION;

		$header  = '<?xml version="1.0" encoding="' . get_bloginfo('charset') . "\" ?>\n";
		$header .= "<!-- This is a WordPress eXtended RSS file generated as an export of your site. -->\n";
		$header .= "<!-- It contains information about your site's posts, pages, comments, categories, and other content. -->\n";
		$header .= "<!-- You may use this file to transfer that content from one site to another. -->\n";
		$header .= "<!-- This file is not intended to serve as a complete backup of your site. -->\n\n";
		$header .= "<!-- To import this information into a WordPress site follow these steps: -->\n";
		$header .= "<!-- 1. Log in to that site as an administrator. -->\n";
		$header .= "<!-- 2. Go to Tools: Import in the WordPress admin panel. -->\n";
		$header .= "<!-- 3. Install the \"WordPress\" importer from the list. -->\n";
		$header .= "<!-- 4. Activate & Run Importer. -->\n";
		$header .= "<!-- 5. Upload this file using the form provided on that page. -->\n";
		$header .= "<!-- 6. You will first be asked to map the authors in this export file to users -->\n";
		$header .= "<!--    on the site. For each author, you may choose to map to an -->\n";
		$header .= "<!--    existing user on the site or to create a new user. -->\n";
		$header .= "<!-- 7. WordPress will then import each of the posts, pages, comments, categories, etc. -->\n";
		$header .= "<!--    contained in this file into your site. -->\n\n";
		$header .= "<!-- generator=\"WordPress/" . get_bloginfo_rss('version') . "\" created=\"". date('Y-m-d H:i') . "\" -->\n";
		$header .= "<rss version=\"2.0\" xmlns:excerpt=\"http://wordpress.org/export/$wxr_version/excerpt/\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:wp=\"http://wordpress.org/export/$wxr_version/\">\n";
		$header .= "<channel>\n";
		$header .= "\t<title>" . get_bloginfo_rss( 'name' ) ."</title>\n";
		$header .= "\t<link>" . get_bloginfo_rss( 'url' ) ."</link>\n";
		$header .= "\t<description>" . get_bloginfo_rss( 'description' ) ."</description>\n";
		$header .= "\t<pubDate>" . date( 'D, d M Y H:i:s +0000' ) ."</pubDate>\n";
		$header .= "\t<language>" . get_bloginfo_rss( 'language' ) ."</language>\n";
		$header .= "\t<wp:wxr_version>" . $wxr_version ."</wp:wxr_version>\n";
		$header .= "\t<wp:site_id>" . get_current_blog_id() ."</wp:site_id>\n";
		$header .= "\t<wp:base_blog_url>" . get_bloginfo_rss( 'url' ) ."</wp:base_blog_url>\n";

		if ( ! file_put_contents( $this->filename, $header, FILE_APPEND ) ) {
			return false;
		}

		unset( $header );
		return true;
	}

	/**
	 * Add authors to export file.
	 *
	 * @return bool.
	 */
	protected function add_authors() {
		global $wpdb;

		$authors = [];
		$results = $wpdb->get_results( "SELECT DISTINCT post_author FROM $wpdb->posts WHERE post_status != 'auto-draft'" );
		foreach ( (array) $results as $result ) {
			$authors[] = get_userdata( $result->post_author );
		}

		$authors = array_filter( $authors );

		$xml = '';
		foreach ( $authors as $author ) {
			$xml .= "\t<wp:author>";
			$xml .= '<wp:author_id>' . $author->ID . '</wp:author_id>';
			$xml .= '<wp:author_login>' . $author->user_login . '</wp:author_login>';
			$xml .= '<wp:author_email>' . $author->user_email . '</wp:author_email>';
			$xml .= '<wp:author_display_name>' . $this->cdata( $author->display_name ) . '</wp:author_display_name>';
			$xml .= '<wp:author_first_name>' . $this->cdata( $author->user_firstname ) . '</wp:author_first_name>';
			$xml .= '<wp:author_last_name>' . $this->cdata( $author->user_lastname ) . '</wp:author_last_name>';
			$xml .= "</wp:author>\n";
		}

		if ( ! file_put_contents( $this->filename, $xml, FILE_APPEND ) ) {
			return false;
		}

		unset( $xml );
		return true;
	}

	/**
	 * Add categorie to export file.
	 *
	 * @return bool
	 */
	protected function add_categories() {
		$cats = [];
		$categories = (array) get_categories( [ 'get' => 'all' ] );

		// Put categories in order with no child going before its parent.
		while ( $cat = array_shift( $categories ) ) {
			if ( $cat->parent == 0 || isset( $cats[ $cat->parent ] ) ) {
				$cats[ $cat->term_id ] = $cat;
			} else {
				$categories[] = $cat;
			}
		}

		$xml = '';
		foreach ( $cats as $c ) {
			$parent_slug = $c->parent ? $cats[$c->parent]->slug : '';

			$xml .= "\t<wp:category>";
			$xml .= "<wp:term_id>{$c->term_id}</wp:term_id>";
			$xml .= "<wp:category_nicename>{$c->slug}</wp:category_nicename>";
			$xml .= "<wp:category_parent>{$parent_slug}</wp:category_parent>";
			$xml .= $this->cat_name( $c );
			$xml .= $this->category_description( $c );
			$xml .= "</wp:category>\n";
		}

		if ( ! file_put_contents( $this->filename, $xml, FILE_APPEND ) ) {
			return false;
		}

		unset( $xml );
		return true;
	}

	/**
	 * Addd tags to export file.
	 *
	 * @return bool
	 */
	protected function add_tags() {
		$tags = (array) get_tags( [ 'get' => 'all' ] );

		$xml = '';
		foreach ( $tags as $t ) {
			$xml .= "\t<wp:tag>";
			$xml .= "<wp:term_id>{$t->term_id}</wp:term_id>";
			$xml .= "<wp:tag_slug>{$t->slug}</wp:tag_slug>";
			$xml .= $this->tag_name( $t );
			$xml .= $this->tag_description( $t );
			$xml .= "</wp:tag>\n";
		}

		if ( ! file_put_contents( $this->filename, $xml, FILE_APPEND ) ) {
			return false;
		}

		unset( $xml );
		return true;
	}

	/**
	 * Add terms to export file.
	 *
	 * @return bool
	 */
	protected function add_terms() {
		$terms = [];
		$custom_taxonomies = get_taxonomies( [ '_builtin' => false ] );
		$custom_terms = (array) get_terms( $custom_taxonomies, [ 'get' => 'all' ] );

		// put terms in order with no child going before its parent
		while ( $t = array_shift( $custom_terms ) ) {
			if ( $t->parent == 0 || isset( $terms[ $t->parent ] ) )
				$terms[ $t->term_id ] = $t;
			else
				$custom_terms[] = $t;
		}

		$xml = '';
		foreach ( $terms as $t ) {
			$parent_slug =  $t->parent ? $terms[$t->parent]->slug : '';

			$xml .= "\t<wp:term>";
			$xml .= "<wp:term_id>{$t->term_id}</wp:term_id>";
			$xml .= "<wp:term_taxonomy>{$t->taxonomy}</wp:term_taxonomy>";
			$xml .= "<wp:term_slug>{$t->slug}</wp:term_slug>";
			$xml .= "<wp:term_parent>{$parent_slug}</wp:term_parent>";
			$xml .= $this->term_name( $t );
			$xml .= $this->term_description( $t );
			$xml .= "</wp:term>\n";
		}

		if ( ! file_put_contents( $this->filename, $xml, FILE_APPEND ) ) {
			return false;
		}

		unset( $xml );
		return true;
	}

	/**
	 * Add menus to export file.
	 *
	 * @return bool
	 */
	protected function add_menus() {
		$nav_menus = wp_get_nav_menus();

		if ( empty( $nav_menus ) || ! is_array( $nav_menus ) ) {
			return false;
		}

		$xml = '';
		foreach ( $nav_menus as $menu ) {
			$xml .= "\t<wp:term>";
			$xml .= "<wp:term_id>{$menu->term_id}</wp:term_id>";
			$xml .= "<wp:term_taxonomy>nav_menu</wp:term_taxonomy>";
			$xml .= "<wp:term_slug>{$menu->slug}</wp:term_slug>";
			$xml .= $this->term_name( $menu );
			$xml .= "</wp:term>\n";
		}

		if ( ! file_put_contents( $this->filename, $xml, FILE_APPEND ) ) {
			return false;
		}

		unset( $xml );
		return true;
	}

	/**
	 * Add content to export file and wrap it up.
	 *
	 * @return bool
	 */
	protected function add_content() {
		global $wpdb;

		$post_ids = $this->get_post_ids();
		if ( empty( $post_ids ) ) {
			return fasle;
		}

		// Fetch 20 posts at a time rather than loading the entire table into memory.
		while ( $next_posts = array_splice( $post_ids, 0, 20 ) ) {
			$where = 'WHERE ID IN (' . join( ',', $next_posts ) . ')';
			$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->posts} $where" );

			$xml = '';
			foreach ( $posts as $post ) {
				$is_sticky = is_sticky( $post->ID ) ? 1 : 0;

				$xml .= "\t<item>\n";
				$xml .= "\t\t<title>" . apply_filters( 'the_title_rss', $post->post_title ) ."</title>\n";
				$xml .= "\t\t<link>" . esc_url( apply_filters( 'the_permalink_rss', get_permalink( $post ) ) ) ."</link>\n";
				$xml .= "\t\t<pubDate>" . mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true, $post ), false ) ."</pubDate>\n";
				$xml .= "\t\t<dc:creator>" . $this->cdata( get_the_author_meta( 'login', $post->post_author ) ) ."</dc:creator>\n";
				$xml .= "\t\t<guid isPermaLink=\"false\">" . esc_url( get_the_guid( $post->ID ) ) ."</guid>\n";
				$xml .= "\t\t<description></description>\n";
				$xml .= "\t\t<content:encoded>" . $this->cdata( apply_filters( 'the_content_export', $post->post_content ) ) . "</content:encoded>\n";
				$xml .= "\t\t<excerpt:encoded>" . $this->cdata( apply_filters( 'the_excerpt_export', $post->post_excerpt ) ) . "</excerpt:encoded>\n";
				$xml .= "\t\t<wp:post_id>" . $post->ID . "</wp:post_id>\n";
				$xml .= "\t\t<wp:post_date>" . $post->post_date . "</wp:post_date>\n";
				$xml .= "\t\t<wp:post_date_gmt>" . $post->post_date_gmt . "</wp:post_date_gmt>\n";
				$xml .= "\t\t<wp:comment_status>" . $post->comment_status . "</wp:comment_status>\n";
				$xml .= "\t\t<wp:ping_status>" . $post->ping_status . "</wp:ping_status>\n";
				$xml .= "\t\t<wp:post_name>" . $post->post_name . "</wp:post_name>\n";
				$xml .= "\t\t<wp:status>" . $post->post_status . "</wp:status>\n";
				$xml .= "\t\t<wp:post_parent>" . $post->post_parent . "</wp:post_parent>\n";
				$xml .= "\t\t<wp:menu_order>" . $post->menu_order . "</wp:menu_order>\n";
				$xml .= "\t\t<wp:post_type>" . $post->post_type . "</wp:post_type>\n";
				$xml .= "\t\t<wp:post_password>" . $post->post_password . "</wp:post_password>\n";
				$xml .= "\t\t<wp:is_sticky>" . $is_sticky . "</wp:is_sticky>\n";

				if ( $post->post_type == 'attachment' ) {
					$xml .= "\t\t<wp:attachment_url>" . wp_get_attachment_url( $post->ID ) . "</wp:attachment_url>\n";
				}

				$xml .= $this->post_taxonomy( $post );

				$postmeta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id = %d", $post->ID ) );
				foreach ( $postmeta as $meta ) {
					if ( apply_filters( 'ol_portfolio_export_skip_postmeta', false, $meta->meta_key, $meta ) ) {
						continue;
					}

					$xml .= "\t\t<wp:postmeta>\n\t\t\t<wp:meta_key>" . $meta->meta_key ."</wp:meta_key>\n\t\t\t<wp:meta_value>" .$this->cdata( $meta->meta_value ) ."</wp:meta_value>\n\t\t</wp:postmeta>\n";
				}

				$comments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID ) );
				foreach ( $comments as $c ) {
					$xml .= "\t\t<wp:comment>\n";
					$xml .= "\t\t\t<wp:comment_id>" . $c->comment_ID . "</wp:comment_id>\n";
					$xml .= "\t\t\t<wp:comment_author>" . $this->cdata( $c->comment_author ) . "</wp:comment_author>\n";
					$xml .= "\t\t\t<wp:comment_author_email>" . $c->comment_author_email . "</wp:comment_author_email>\n";
					$xml .= "\t\t\t<wp:comment_author_url>" . esc_url_raw( $c->comment_author_url ) . "</wp:comment_author_url>\n";
					$xml .= "\t\t\t<wp:comment_author_IP>" . $c->comment_author_IP . "</wp:comment_author_IP>\n";
					$xml .= "\t\t\t<wp:comment_date>" . $c->comment_date . "</wp:comment_date>\n";
					$xml .= "\t\t\t<wp:comment_date_gmt>" . $c->comment_date_gmt . "</wp:comment_date_gmt>\n";
					$xml .= "\t\t\t<wp:comment_content>" . $this->cdata( $c->comment_content ) . "</wp:comment_content>\n";
					$xml .= "\t\t\t<wp:comment_approved>" . $c->comment_approved . "</wp:comment_approved>\n";
					$xml .= "\t\t\t<wp:comment_type>" . $c->comment_type . "</wp:comment_type>\n";
					$xml .= "\t\t\t<wp:comment_parent>" . $c->comment_parent . "</wp:comment_parent>\n";
					$xml .= "\t\t\t<wp:comment_user_id>" . $c->user_id . "</wp:comment_user_id>\n";
					$c_meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->commentmeta WHERE comment_id = %d", $c->comment_ID ) );
					foreach ( $c_meta as $meta ) {
						$xml .= "\t\t\t<wp:commentmeta>\n\t\t\t\t<wp:meta_key>" . $meta->meta_key ."</wp:meta_key>\n\t\t\t\t<wp:meta_value>" .$this->cdata( $meta->meta_value ) ."</wp:meta_value>\n\t\t\t</wp:commentmeta>\n";
					}
					$xml .= "\t\t</wp:comment>\n";
				}
				$xml .= "\t</item>\n";
			}

			file_put_contents( $this->filename, $xml, FILE_APPEND );
		}

		unset( $xml );
		file_put_contents( $this->filename, "</channel>\n</rss>", FILE_APPEND );
	}

	/**
	 * Pre-fetch post IDs.
	 *
	 * @return array $post_ids
	 */
	protected function get_post_ids() {
		global $wpdb;

		$post_types = get_post_types( array( 'can_export' => true ) );
		$esses      = array_fill( 0, count( $post_types ), '%s' );
		$where      = $wpdb->prepare( "{$wpdb->posts}.post_type IN (" . implode( ',', $esses ) . ')', $post_types );
		$where .= " AND {$wpdb->posts}.post_status != 'auto-draft'";

		// Grab a snapshot of post IDs, just in case it changes during the export.
		$post_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE $where" );

		return $post_ids;
	}

	/**
	 * Output list of taxonomy terms, in XML tag format, associated with a post.
	 *
	 * @param object $post
	 * @return string
	 */
	protected function post_taxonomy( $post ) {
		$taxonomies = get_object_taxonomies( $post->post_type );
		if ( empty( $taxonomies ) ) {
			return '';
		}

		$terms = wp_get_object_terms( $post->ID, $taxonomies );

		$wxr_post_tags = '';

		foreach ( (array) $terms as $term ) {
			$wxr_post_tags .= "\t\t<category domain=\"{$term->taxonomy}\" nicename=\"{$term->slug}\">" . $this->cdata( $term->name ) . "</category>\n";
		}

		return $wxr_post_tags;
	}

	/**
	 * Wrap given string in XML CDATA tag.
	 *
	 * @param string $str String to wrap in XML CDATA tag.
	 * @return string
	*/
	protected function cdata( $str ) {
		if ( ! seems_utf8( $str ) ) {
			$str = utf8_encode( $str );
		}

		$str = '<![CDATA[' . str_replace( ']]>', ']]]]><![CDATA[>', $str ) . ']]>';

		return $str;
	}

	/**
	 * Output a cat_name XML tag from a given category object.
	 *
	 * @param object $category Category Object
	 * @return string
	 */
	protected function cat_name( $category ) {
		if ( empty( $category->name ) )
			return '';

		return '<wp:cat_name>' . $this->cdata( $category->name ) . '</wp:cat_name>';
	}

	/**
	 * Output a category_description XML tag from a given category object.
	 *
	 * @param object $category Category Object
	 * @return string
	 */
	protected function category_description( $category ) {
		if ( empty( $category->description ) )
			return '';

		return '<wp:category_description>' . $this->cdata( $category->description ) . '</wp:category_description>';
	}

	/**
	 * Output a tag_name XML tag from a given tag object.
	 *
	 * @param object $tag Tag Object
	 * @return string
	 */
	protected function tag_name( $tag ) {
		if ( empty( $tag->name ) )
			return '';

		return '<wp:tag_name>' . $this->cdata( $tag->name ) . '</wp:tag_name>';
	}

	/**
	 * Output a tag_description XML tag from a given tag object.
	 *
	 * @param object $tag Tag Object
	 * @return string
	 */
	protected function tag_description( $tag ) {
		if ( empty( $tag->description ) )
			return '';

		return '<wp:tag_description>' . $this->cdata( $tag->description ) . '</wp:tag_description>';
	}

	/**
	 * Output a term_name XML tag from a given term object.
	 *
	 * @param object $term Term Object
	 * @return string
	 */
	protected function term_name( $term ) {
		if ( empty( $term->name ) )
			return '';

		return '<wp:term_name>' . $this->cdata( $term->name ) . '</wp:term_name>';
	}

	/**
	 * Output a term_description XML tag from a given term object.
	 *
	 * @param object $term Term Object
	 * @return string
	 */
	protected function term_description( $term ) {
		if ( empty( $term->description ) )
			return '';

		return '<wp:term_description>' . $this->cdata( $term->description ) . '</wp:term_description>';
	}
}
