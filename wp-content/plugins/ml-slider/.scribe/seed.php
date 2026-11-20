<?php
/**
 * Demo content for this plugin's how-to captures.
 *
 * Runs inside WordPress after the plugins are active. Create here the content the admin screens in
 * .scribe/howto.json need to look real: posts, images, and any data the plugin stores.
 *
 * The last line printed must be a JSON object of named references, for example {"gallery.basic": 12}.
 * A name can then be used in an admin page path as {{gallery.basic}}.
 */

// Create demo content here, for example:
// $post_id = wp_insert_post(['post_title' => 'Demo post', 'post_status' => 'publish']);
// $refs['post.basic'] = $post_id;

echo json_encode(new stdClass()), "\n";
