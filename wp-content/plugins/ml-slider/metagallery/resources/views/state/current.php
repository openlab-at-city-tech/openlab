<?php
/**
 * The current gallery state
 */

if (!defined('ABSPATH')) {
    die('No direct access.');
}
?>

<div
    x-id="current"
    x-title="Current Gallery"
    x-data="CurrentGallery(<?php echo \esc_attr(\wp_json_encode($data['gallery'][0])); ?>)"
    @metagallery-save.window="save()"
    x-init="init()"></div>
