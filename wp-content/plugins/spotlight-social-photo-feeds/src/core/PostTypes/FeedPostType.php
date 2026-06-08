<?php

namespace RebelCode\Spotlight\Instagram\PostTypes;

use RebelCode\Spotlight\Instagram\Feeds\Feed;
use RebelCode\Spotlight\Instagram\Utils\Arrays;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Post;
use wpdb;

/**
 * The post type for feeds.
 *
 * This class extends the {@link PostType} class only as a formality. The primary purpose of this class is to house
 * the meta key constants and functionality for dealing with posts of the feed custom post type.
 *
 * @since 0.1
 */
class FeedPostType extends PostType
{
    /**
     * The meta key for a feed's options.
     *
     * @since 0.1
     */
    const OPTIONS = '_sli_options';

    /**
     * Converts a WordPress post into a feed instance.
     *
     * @since 0.1
     *
     * @param WP_Post $post
     *
     * @return Feed
     */
    public static function fromWpPost(WP_Post $post)
    {
        $options = $post->{static::OPTIONS};
        $options = (empty($options) || !is_array($options)) ? [] : $options;

        return new Feed($post->ID, $post->post_title, $options);
    }

    /**
     * Converts a feed instance into a WordPress post array.
     *
     * @since 0.1
     *
     * @param Feed $feed
     *
     * @return array
     */
    public static function toWpPost(Feed $feed)
    {
        return [
            'ID' => $feed->getId(),
            'post_title' => $feed->getName(),
            'post_status' => 'publish',
            'meta_input' => [
                static::OPTIONS => $feed->getOptions(),
            ],
        ];
    }

    /**
     * Finds usages of the shortcode for a specific feed.
     *
     * @since 0.1
     *
     * @param Feed $feed The feed instance.
     * @param wpdb $wpdb The WordPress database driver.
     *
     * @return array A list of associative sub-arrays, each containing information about posts whose contents include
     *               an occurrence of the shortcode with the given feed's ID. Each sub-array will have the below keys:
     *               'id' => The ID of the post
     *               'name' => The title of the post
     *               'type' => The post type
     *               'link' => The URL to the post's edit page
     */
    public static function getShortcodeUsages(Feed $feed, wpdb $wpdb)
    {
        $postsTable = $wpdb->prefix . 'posts';

        $query = $wpdb->prepare(
            "SELECT ID, post_title, post_type
                    FROM $postsTable
                    WHERE (post_type = 'post' OR post_type = 'page') AND post_status != 'trash' AND
                          post_content REGEXP '\\\\[instagram[[:blank:]]+feed=[\\'\"]%d[\\'\"]'",
            $feed->getId()
        );

        $results = $wpdb->get_results($query);

        return Arrays::map($results, function ($row) {
            return [
                'id' => $row->ID,
                'name' => $row->post_title,
                'type' => get_post_type_object($row->post_type)->labels->singular_name,
                'link' => get_permalink($row->ID),
            ];
        });
    }

    /**
     * Work in progress.
     *
     * @since 0.1
     *
     * @param Feed $feed
     */
    public static function getWidgetUsages(Feed $feed)
    {
        $id = $feed->getId();

        $sidebars = get_option('sidebars_widgets', []);
        $sliWidgets = get_option('widget_sli-feed', []);
        $widgetEditLink = admin_url('customize.php?autofocus[panel]=widgets');

        unset($sidebars['wp_inactive_widgets']);
        unset($sidebars['array_version']);

        foreach ($sidebars as $sidebar => $widgetList) {
            foreach ($widgetList as $widgetId) {
                if (strpos($widgetId, 'sli-feed-') !== 0) {
                    continue;
                }

                $actualId = substr($widgetId, 9);

                if (!array_key_exists($actualId, $sliWidgets)) {
                    continue;
                }

                if ($sliWidgets[$actualId]['feed'] == $id) {
                    $usages[] = [
                        'id' => $actualId,
                        'name' => $sliWidgets[$actualId]['title'],
                        'type' => __('WordPress widget', 'sl-insta'),
                        'link' => $widgetEditLink,
                    ];
                }
            }
        }
    }

    /**
     * Finds usages of the WordPress block for a specific feed.
     *
     * @since 0.1
     *
     * @param Feed $feed The feed instance.
     * @param wpdb $wpdb The WordPress database driver.
     *
     * @return array A list of associative sub-arrays, each containing information about posts whose contents include
     *               an occurrence of the wp block with the given feed's ID. Each sub-array will have the below keys:
     *               'id' => The ID of the post
     *               'name' => The title of the post
     *               'type' => The post type
     *               'link' => The URL to the post's edit page
     */
    public static function getWpBlockUsages(Feed $feed, wpdb $wpdb)
    {
        $postsTable = $wpdb->prefix . 'posts';
        $query = $wpdb->prepare(
            "SELECT ID, post_title, post_type
                    FROM $postsTable
                    WHERE (post_type = 'post' OR post_type = 'page') AND post_status != 'trash' AND
                          post_content REGEXP '<!-- wp:spotlight/instagram \\\\{\"feedId\":\"?%d\"?'",
            $feed->getId()
        );

        $results = $wpdb->get_results($query);

        return Arrays::map($results, function ($row) {
            return [
                'id' => $row->ID,
                'name' => $row->post_title,
                'type' => get_post_type_object($row->post_type)->labels->singular_name,
                'link' => get_permalink($row->ID),
            ];
        });
    }

    /**
     * Finds usages of the Spotlight Elementor widget for a specific feed.
     *
     * @since 0.4
     *
     * @param Feed $feed The feed instance.
     * @param wpdb $wpdb The WordPress database driver.
     *
     * @return array A list of associative sub-arrays, each containing information about posts whose Elementor page data
     *               includes a Spotlight widget that uses the given feed. Each sub-array will have the below keys:
     *               'id' => The ID of the post
     *               'name' => The title of the post
     *               'type' => The post type
     *               'link' => The URL to the post's edit page
     */
    public static function getElementorWidgetUsages(Feed $feed, wpdb $wpdb)
    {
        $postsTable = $wpdb->posts;
        $metaTable = $wpdb->postmeta;

        $query = $wpdb->prepare(
            "SELECT ID, post_title, post_type
             FROM $postsTable
             WHERE ID IN (
                    SELECT post_id
                    FROM $metaTable
                    WHERE meta_key = '_elementor_data' AND
                          meta_value LIKE '%%sl-insta-feed%%' AND
                          meta_value LIKE '%%\"feed\":\"%d\"%%'
                ) AND (post_type = 'post' OR post_type = 'page') AND post_status != 'trash'
            ",
            $feed->getId()
        );

        $results = $wpdb->get_results($query);

        return Arrays::map($results, function ($row) {
            return [
                'id' => $row->ID,
                'name' => $row->post_title,
                'type' => __('Elementor widget', 'sl-insta'),
                'link' => get_permalink($row->ID),
            ];
        });
    }
}
