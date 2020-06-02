<?php

/**
* nggRewrite - Rewrite Rules for NextGEN Gallery
*
* sorry wp-guys I didn't understand this at all.
* I tried it a couple of hours : this is the only pooooor result
*
* @package NextGEN Gallery
* @author Alex Rabe
*
*/
class nggRewrite {

	/**
	 * Default slug name
	 *
	 * @since 1.8.0
	 * @var string
	 */
	var $slug = 'nggallery';

	/**
	 * Contain the main rewrite structure
	 *
	 * @since 1.8.3
	 * @var array
	 */
    var $ngg_rules = '';

	/**
	* Constructor
	*/
	function __construct()
	{
		// read the option setting
		$this->options = get_option('ngg_options');

		// get later from the options
        $this->slug = $this->options['permalinkSlug'];

		/*WARNING: Do nothook rewrite rule regentation on the init hook for anything other than dev. */
		//add_action('init',array(&$this, 'flush'));

		add_filter('query_vars', array(&$this, 'add_queryvars') );
		add_filter('wp_title' , array(&$this, 'rewrite_title') );

        //DD32 recommend : http://groups.google.com/group/wp-hackers/browse_thread/thread/50ac0d07e30765e9
        //add_filter('rewrite_rules_array', array($this, 'RewriteRules'));

		if ($this->options['usePermalinks'])
			add_action('generate_rewrite_rules', array(&$this, 'RewriteRules'));

        // setup the main rewrite structure for the plugin
        $this->ngg_rules = array(
            '/page-([0-9]+)/' => '&nggpage=[matches]',
    		'/image/([^/]+)/' => '&pid=[matches]',
    		'/image/([^/]+)/page-([0-9]+)/' => '&pid=[matches]&nggpage=[matches]',
    		'/slideshow/' => '&show=slide',
    		'/images/' => '&show=gallery',
    		'/tags/([^/]+)/' => '&gallerytag=[matches]',
    		'/tags/([^/]+)/page-([0-9]+)/' => '&gallerytag=[matches]&nggpage=[matches]',
    		'/([^/]+)/' => '&album=[matches]',
    		'/([^/]+)/page-([0-9]+)/' => '&album=[matches]&nggpage=[matches]',
    		'/([^/]+)/([^/]+)/' => '&album=[matches]&gallery=[matches]',
    		'/([^/]+)/([^/]+)/slideshow/' => '&album=[matches]&gallery=[matches]&show=slide',
    		'/([^/]+)/([^/]+)/images/' => '&album=[matches]&gallery=[matches]&show=gallery',
    		'/([^/]+)/([^/]+)/page-([0-9]+)/' => '&album=[matches]&gallery=[matches]&nggpage=[matches]',
    		'/([^/]+)/([^/]+)/page-([0-9]+)/slideshow/' => '&album=[matches]&gallery=[matches]&nggpage=[matches]&show=slide',
    		'/([^/]+)/([^/]+)/page-([0-9]+)/images/' => '&album=[matches]&gallery=[matches]&nggpage=[matches]&show=gallery',
    		'/([^/]+)/([^/]+)/image/([^/]+)/' => '&album=[matches]&gallery=[matches]&pid=[matches]'
        );


	} // end of initialization

	/**
	* Get the permalink to a picture/album/gallery given its ID/name/...
	*/
	function get_permalink( $args ) {
		global $wp_rewrite, $wp_query;

        // taken from is_frontpage plugin, required for static homepage
        $show_on_front = get_option('show_on_front');
        $page_on_front = get_option('page_on_front');

		//TODO: Watch out for ticket http://trac.wordpress.org/ticket/6627
		if ($wp_rewrite->using_permalinks() && $this->options['usePermalinks'] ) {
			$post = &get_post(get_the_ID());

			// If the album is not set before get it from the wp_query ($_GET)
            if ( !isset ($args['album'] ) )
                $album = get_query_var('album');
			if ( !empty( $album ) )
				$args ['album'] = $album;

			$gallery = get_query_var('gallery');
			if ( !empty( $gallery ) )
				$args ['gallery'] = $gallery;

			$gallerytag = get_query_var('gallerytag');
			if ( !empty( $gallerytag ) )
				$args ['gallerytag'] = $gallerytag;

			/** urlconstructor =  post url | slug | tags | [nav] | [show]
				tags : 	album, gallery 	-> /album-([0-9]+)/gallery-([0-9]+)/
						pid 			-> /image/([0-9]+)/
						gallerytag		-> /tags/([^/]+)/
				nav	 : 	nggpage			-> /page-([0-9]+)/
				show : 	show=slide		-> /slideshow/
						show=gallery	-> /images/
			**/

			// 1. Post / Page url + main slug
            $url = trailingslashit ( get_permalink ($post->ID) ) . $this->slug;
            //TODO: For static home pages generate the link to the selected page, still doesn't work
            if (($show_on_front == 'page') && ($page_on_front == get_the_ID()))
                $url = trailingslashit ( $post->guid ) . $this->slug;

			// 2. Album, pid or tags
			if (isset ($args['album']) && ($args['gallery'] == false) )
				$url .= '/' . $args['album'];
			elseif  (isset ($args['album']) && isset ($args['gallery']) )
				$url .= '/' . $args['album'] . '/' . $args['gallery'];

			if  (isset ($args['gallerytag']))
				$url .= '/tags/' . $args['gallerytag'];

			if  (isset ($args['pid']))
				$url .= '/image/' . $args['pid'];

			// 3. Navigation
			if  (isset ($args['nggpage']) && ($args['nggpage']) )
				$url .= '/page-' . $args['nggpage'];
            elseif (isset ($args['nggpage']) && ($args['nggpage'] === false) && ( count($args) == 1 ) )
                $url = trailingslashit ( get_permalink ($post->ID) ); // special case instead of showing page-1, we show the clean url

			// 4. Show images or Slideshow
			if  (isset ($args['show']))
				$url .= ( $args['show'] == 'slide' ) ? '/slideshow' : '/images';

			return apply_filters('ngg_get_permalink', $url, $args);

		} else {
			// we need to add the page/post id at the start_page otherwise we don't know which gallery is clicked
			if (is_home())
				$args['pageid'] = get_the_ID();

			if (($show_on_front == 'page') && ($page_on_front == get_the_ID()))
				$args['page_id'] = get_the_ID();

			if ( !is_singular() )
				$query = htmlspecialchars( add_query_arg($args, get_permalink( get_the_ID() )) );
			else
				$query = htmlspecialchars( add_query_arg( $args ) );

            return apply_filters('ngg_get_permalink', $query, $args);
		}
	}

	/**
	* The permalinks needs to be flushed after activation
	*/
	function flush() {
		global $wp_rewrite, $ngg;

        // reload slug, maybe it changed during the flush routine
        $this->slug = $ngg->options['permalinkSlug'];

		if ($ngg->options['usePermalinks'])
			add_action('generate_rewrite_rules', array(&$this, 'RewriteRules'));

		$wp_rewrite->flush_rules();
	}

	/**
	* add some more vars to the big wp_query
	*/
	function add_queryvars( $query_vars ){

		$query_vars[] = 'pid';
		$query_vars[] = 'pageid';
		$query_vars[] = 'nggpage';
		$query_vars[] = 'gallery';
		$query_vars[] = 'album';
		$query_vars[] = 'gallerytag';
		$query_vars[] = 'show';
        $query_vars[] = 'callback';

		return $query_vars;
	}

	/**
	* rewrite the blog title if the gallery is used
	*/
	function rewrite_title($title) {

		$new_title = '';
		// the separataor
		$sep = ' &laquo; ';

		// $_GET from wp_query
		$pid     = get_query_var('pid');
		$pageid  = get_query_var('pageid');
		$nggpage = get_query_var('nggpage');
		$gallery = get_query_var('gallery');
		$album   = get_query_var('album');
		$tag  	 = get_query_var('gallerytag');
		$show    = get_query_var('show');

		//TODO: I could parse for the Picture name , gallery etc, but this increase the queries
		//TODO: Class nggdb need to cache the query for the nggfunctions.php

		if ( $show == 'slide' )
			$new_title .= __('Slideshow', 'nggallery') . $sep ;
		elseif ( $show == 'show' )
			$new_title .= __('Gallery', 'nggallery') . $sep ;

		if ( !empty($pid) )
			$new_title .= __('Picture', 'nggallery') . ' ' . esc_attr($pid) . $sep ;

		if ( !empty($album) )
			$new_title .= __('Album', 'nggallery') . ' ' . esc_attr($album) . $sep ;

		if ( !empty($gallery) )
			$new_title .= __('Gallery', 'nggallery') . ' ' . esc_attr($gallery) . $sep ;

		if ( !empty($nggpage) )
			$new_title .= __('Page', 'nggallery') . ' ' . esc_attr($nggpage) . $sep ;

		//esc_attr should avoid XSS like http://domain/?gallerytag=%3C/title%3E%3Cscript%3Ealert(document.cookie)%3C/script%3E
		if ( !empty($tag) )
			$new_title .= esc_attr($tag) . $sep;

		//prepend the data
		$title = $new_title . $title;

		return $title;
	}

	/**
	 * Canonical support for a better SEO (Dupilcat content), not longer nedded for Wp 2.9
	 * See : http://googlewebmastercentral.blogspot.com/2009/02/specify-your-canonical.html
	 *
	 * @deprecated
	 * @return void
	 */
	function add_canonical_meta()
    {
        // create the meta link
        $meta  = "\n<link rel='canonical' href='" . get_permalink() ."' />";
        // add a filter for SEO plugins, so they can remove it
        echo apply_filters('ngg_add_canonical_meta', $meta);

        return;
    }

	/**
	* The actual rewrite rules
	*/
	function RewriteRules($wp_rewrite) {
        global $ngg;

		$rewrite_rules = array (
            // XML request
            $this->slug . '/slideshow/([0-9]+)/?$' => 'index.php?imagerotator=true&gid=$matches[1]'
		);

        $rewrite_rules = array_merge($rewrite_rules, $this->generate_rewrite_rules() );
		$wp_rewrite->rules = array_merge($rewrite_rules, $wp_rewrite->rules);
	}

	/**
	 * Mainly a copy of the same function in wp-includes\rewrite.php
     * Adding the NGG tags to each post & page. Never found easier and proper way to handle this with other functions.
	 *
	 * @return array the permalink structure
	 */
	function generate_rewrite_rules() {
        global $wp_rewrite;

        $rewrite_rules = array();
        $permalink_structure =  $wp_rewrite->permalink_structure;

        //get everything up to the first rewrite tag
		$front = substr($permalink_structure, 0, strpos($permalink_structure, '%'));
		//build an array of the tags (note that said array ends up being in $tokens[0])
		preg_match_all('/%.+?%/', $permalink_structure, $tokens);

		$num_tokens = count($tokens[0]);

		$this->index = $wp_rewrite->index; //probably 'index.php'

		//build a list from the rewritecode and queryreplace arrays, that will look something like
		//tagname=$matches[i] where i is the current $i
		for ( $i = 0; $i < $num_tokens; ++$i ) {
			if ( 0 < $i )
				$queries[$i] = $queries[$i - 1] . '&';
			else
				$queries[$i] = '';

			$query_token = str_replace($wp_rewrite->rewritecode, $wp_rewrite->queryreplace, $tokens[0][$i]) . $wp_rewrite->preg_index($i+1);
			$queries[$i] .= $query_token;
		}

		//get the structure, minus any cruft (stuff that isn't tags) at the front
		$structure = $permalink_structure;
		if ( $front != '/' )
			$structure = str_replace($front, '', $structure);

		//create a list of dirs to walk over, making rewrite rules for each level
		//so for example, a $structure of /%year%/%month%/%postname% would create
		//rewrite rules for /%year%/, /%year%/%month%/ and /%year%/%month%/%postname%
		$structure = trim($structure, '/');

		//strip slashes from the front of $front
		$struct = preg_replace('|^/+|', '', $front);

		//get the struct for this dir, and trim slashes off the front
		$struct .= $structure . '/'; //accumulate. see comment near explode('/', $structure) above
		$struct = ltrim($struct, '/');

		//replace tags with regexes
		$match = str_replace($wp_rewrite->rewritecode, $wp_rewrite->rewritereplace, $struct);

		//make a list of tags, and store how many there are in $num_toks
		$num_toks = preg_match_all('/%.+?%/', $struct, $toks);

		//get the 'tagname=$matches[i]'
		$query = ( isset($queries) && is_array($queries) ) ? $queries[$num_toks - 1] : '';

        if ( $num_toks ) {
            // In the case we build for each and every page ( based on a simple %pagename% rule ) the rewrite rules,
            // we need to add them first, then the post rules
            if ( $wp_rewrite->use_verbose_page_rules )
                $rewrite_rules = array_merge ( $this->page_rewrite_rules(), $this->add_rewrite_rules( $match, $query, $num_toks ) );
            else
                $rewrite_rules = array_merge ( $this->add_rewrite_rules( $match, $query, $num_toks ), $this->page_rewrite_rules() );
        }

        return $rewrite_rules;
	}

	/**
	 * Retrieve all of the rewrite rules for pages.
	 *
	 * If the 'use_verbose_page_rules' property is false, then there will only
	 * be a single rewrite rule for pages for those matching '%pagename%'. With
	 * the property set to true, the attachments and the pages will be added for
	 * each individual attachment URI and page URI, respectively.
	 *
	 * @since 1.8.3
	 * @access public
	 * @return array
	 */
	function page_rewrite_rules() {
        global $wp_rewrite;

		$rewrite_rules = array();

		if ( ! $wp_rewrite->use_verbose_page_rules ) {

            $rewrite_rules = $this->add_rewrite_rules( "(.+?)/", 'pagename=$matches[1]', 1 );
    		return $rewrite_rules;
		}

		$page_uris = $wp_rewrite->page_uri_index();
		$uris = $page_uris[0];

		if ( is_array( $uris ) ) {

			foreach ( $uris as $uri => $pagename ) {
                $rewrite_rules = array_merge($rewrite_rules, $this->add_rewrite_rules( "($uri)/", 'pagename=$matches[1]', 1 ) );
			}

		}

		return $rewrite_rules;
	}

    /**
     * Build the final structure of the rewrite rules based on match/query
     *
     * @since 1.8.3
     * @param string $match
     * @param string $query
     * @param int $num_toks
     * @return array
     */
    function add_rewrite_rules( $match, $query, $num_toks ) {
        global $wp_rewrite;

        $rewrite_rules = array();

        foreach ( $this->ngg_rules as $regex => $new_query) {

            // first add your nextgen slug
            $final_match = $match . $this->slug;

            //add regex parameter
            $final_match .= $regex;
            // check how often we found matches fields
            $count = substr_count($new_query, '[matches]');
            // we need to know how many tags before
            $offset = $num_toks;
            // build the query and count up the matches : tagname=$matches[x]
            for ( $i = 0; $i < $count; $i++ ) {
                $new_query = preg_replace('/\[matches\]/', '$matches[' . ++$offset . ']', $new_query, 1);
            }
            $final_query = $query . $new_query;

            //close the match and finalise the query
            $final_match .= '?$';
            $final_query = $this->index . '?' . $final_query;

            $rewrite_rules = array_merge($rewrite_rules, array($final_match => $final_query));

        }

		return $rewrite_rules;
    }

}  // of nggRewrite CLASS
