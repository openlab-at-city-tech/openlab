<?php
class Mappress_WPML {
	static function register() {
		if (Mappress::$options->wpml)
			add_action('wpml_pro_translation_completed', array(__CLASS__, 'copy_maps'), 1, 3);
	}

	// WPML Duplicate
	static function copy_maps($new_post_id, $fields, $job) {

		$src_postid = $job->original_doc_id;
		$postid = $new_post_id;

		$lang =  apply_filters( 'wpml_post_language_details', NULL, $postid);
		$lang = $lang['language_code'];

		$post = get_post($postid);
		$post = (array)$post;

		$updated = false;

		// Trash any existing maps in target post
		$mapids = Mappress_Map::get_list('post', $postid, 'ids');
		foreach($mapids as $mapid)
			Mappress_Map::mutate($mapid, array('status' => 'trashed'));

		// Copy maps
		$maps = Mappress_Map::get_list('post', $src_postid);
		$converted = array();
		foreach($maps as $map) {
			$src_mapid = $map->mapid;
			$map->mapid = null;
			$map->otype = 'post';
			$map->oid = $postid;
			$map->save();
			$converted[$src_mapid] = $map->mapid;
		}

		$post_content = $post['post_content'];

		// Replace shortcodes
		preg_match_all( '/' . get_shortcode_regex() . '/', $post_content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			if ( 'mappress' !== $match[2] )
				continue;

			$atts = shortcode_parse_atts($match[3]);
			$src_mapid = (isset($atts['mapid'])) ? $atts['mapid'] : null;
			if (!$src_mapid || !array_key_exists($src_mapid, $converted))
				continue;

			// Set new mapid
			$atts['mapid'] = $converted[$src_mapid];

			// Generate new shortcode
			$new_shortcode = '[mappress ';
			foreach($atts as $att => $value)
				$new_shortcode .= "$att=\"$value\" ";
			$new_shortcode .= "]";

			// Replace
			$post_content = str_replace($match[0], $new_shortcode, $post_content);
			$updated = true;
		}

		// Replace Blocks
		$blocks = parse_blocks($post_content);
		foreach($blocks as $block) {
			if ($block['blockName'] != 'mappress/map')
				continue;
			$mapid = isset($block['attrs']['mapid']) ? $block['attrs']['mapid'] : null;
			if (isset($converted[$mapid])) {
				// Replace post content
				$old_string = serialize_block($block);
				$block['attrs']['mapid'] = $converted[$mapid];
				$new_string = serialize_block($block);
				$post_content = str_replace($old_string, $new_string, $post_content);
			}
			$updated = true;
		}

		if ($updated) {
			$post['ID'] = $postid;
			$post['post_content'] = $post_content;
			wp_insert_post($post);
		}
	}
}
?>