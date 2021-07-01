<?php
/**
 * @var string $alternate_random_method_field
 * @var string $cache_confirmation
 * @var string $cache_label
 * @var string $disable_fontawesome_field
 * @var string $disable_ngg_tags_page_field
 * @var string $galleries_in_feeds
 * @var string $galleries_in_feeds_help
 * @var string $galleries_in_feeds_label
 * @var string $galleries_in_feeds_no
 * @var string $galleries_in_feeds_yes
 * @var string $maximum_entity_count_field
 * @var string $mediarss_activated
 * @var string $mediarss_activated_help
 * @var string $mediarss_activated_label
 * @var string $mediarss_activated_no
 * @var string $mediarss_activated_yes
 * @var string $random_widget_cache_ttl_field
 * @var string $slug_field
 * @var string $update_legacy_featured_images_field
 */
?>
<table>

    <tr>
        <td class="column1">
            <label  for="mediarss_activated" 
                    title="<?php echo esc_attr($mediarss_activated_help); ?>"
                    class="tooltip">
                <?php esc_html_e($mediarss_activated_label)?>
            </label>
        </td>
        <td>
            <label for="mediarss_activated">
                <?php esc_html_e($mediarss_activated_yes) ?>
            </label>
            <input
                id='mediarss_activated'
                type="radio"
                name="misc_settings[useMediaRSS]"
                value="1"
                <?php checked(TRUE, $mediarss_activated ? TRUE : FALSE)?>
                />
            &nbsp;
            <label for="mediarss_activated_no">
                <?php esc_html_e($mediarss_activated_no) ?>
            </label>
            <input
                id='mediarss_activated_no'
                type="radio"
                name="misc_settings[useMediaRSS]"
                value="0"
                <?php checked(FALSE, $mediarss_activated ? TRUE : FALSE)?>
                />
        </td>
    </tr>

    <tr>
        <td class="column1">
            <label  for="galleries_in_feeds"  
                    title="<?php echo esc_attr($galleries_in_feeds_help); ?>"
                    class="tooltip">
                <?php esc_html_e($galleries_in_feeds_label)?>
            </label>
        </td>
        <td>
            <label for="galleries_in_feeds">
                <?php esc_html_e($galleries_in_feeds_yes) ?>
            </label>
            <input
                id='galleries_in_feeds'
                type="radio"
                name="misc_settings[galleries_in_feeds]"
                value="1"
                <?php checked(TRUE, $galleries_in_feeds ? TRUE : FALSE)?>
                />
            &nbsp;
            <label for="galleries_in_feeds_no">
                <?php esc_html_e($galleries_in_feeds_no) ?>
            </label>
            <input
                id='galleries_in_feeds_no'
                type="radio"
                name="misc_settings[galleries_in_feeds]"
                value="0"
                <?php checked(FALSE, $galleries_in_feeds ? TRUE : FALSE)?>
                />
        </td>
    </tr>

    <tr>
        <td class='column1'>
            <?php echo $cache_label; ?>
        </td>
        <td>
            <input type='submit'
                   name="action_proxy"
                   class="button delete button-primary"
                   data-proxy-value="cache"
                   data-confirm="<?php echo $cache_confirmation; ?>"
                   value='<?php echo $cache_label; ?>'
                />
        </td>
    </tr>

    <?php print $update_legacy_featured_images_field; ?>

    <?php print $slug_field; ?>

    <?php print $maximum_entity_count_field; ?>

    <?php print $random_widget_cache_ttl_field; ?>

    <?php print $alternate_random_method_field; ?>

    <?php print $disable_fontawesome_field; ?>

    <?php print $disable_ngg_tags_page_field; ?>
</table>