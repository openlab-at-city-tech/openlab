<?php if (!defined('ABSPATH')) {
    die('No direct access.');
}
/**
 * The shortcode module
 */
?>
<metaslider-shortcode inline-template>
    <div class="flex flex-col">
        <div class="mt-0">
            <pre id="shortcode" ref="shortcode" dir="ltr" class="text-yellow text-lg tipsy-tooltip bg-yellow-50 shadow p-4" original-title="<?php esc_attr_e('Click to copy shortcode.', 'ml-slider'); ?>"><div @click.prevent="copyShortcode($event)" class="text-orange cursor-pointer whitespace-normal lowercase">[metaslider id="{{ current.id }}"]</div></pre>
        </div>
    </div>
</metaslider-shortcode>
