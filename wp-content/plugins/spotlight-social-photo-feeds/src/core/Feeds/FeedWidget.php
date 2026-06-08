<?php

namespace RebelCode\Spotlight\Instagram\Feeds;

use RebelCode\Spotlight\Instagram\Actions\RenderShortcode;
use RebelCode\Spotlight\Instagram\Wp\PostType;
use WP_Widget;

/**
 * A widget that renders a feed in the same way as the shortcode.
 *
 * @since 0.1
 */
class FeedWidget extends WP_Widget
{
    /**
     * @since 0.1
     *
     * @var PostType
     */
    public static $cpt;

    /**
     * @since 0.1
     *
     * @var RenderShortcode
     */
    public static $shortcode;

    /**
     * Constructor.
     *
     * @since 0.1
     */
    public function __construct()
    {
        parent::__construct('sli-feed', 'Spotlight Instagram Feed Widget', [
            'description' => 'Show an Instagram feed in a widget',
        ]);
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function widget($args, $instance)
    {
        $instance = wp_parse_args($instance, $this->getDefaults(static::$cpt->query()));

        if (empty($instance['feed'])) {
            return;
        }

        $feedId = $instance['feed'];
        $render = static::$shortcode;

        echo $args['before_widget'];
        echo $args['before_title'] . ($instance['title'] ?? '') . $args['after_title'];

        echo $render(['feed' => $feedId]);

        echo $args['after_widget'];
    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function form($instance)
    {
        $feeds = static::$cpt->query();
        $instance = wp_parse_args($instance, $this->getDefaults($feeds));

        $title = $instance['title'] ?? '';
        $titleHtmlId = $this->get_field_id('title');
        $titleHtmlName = $this->get_field_name('title');

        $feed = $instance['feed'] ?? '';
        $feedHtmlId = $this->get_field_id('feed');
        $feedHtmlName = $this->get_field_name('feed');

        ?>
        <p>
            <label for="<?= $titleHtmlId ?>" style="margin-right: 5px;">Title:</label>
            <input type="text"
                   id="<?= $titleHtmlId ?>"
                   name="<?= $titleHtmlName ?>"
                   value="<?= esc_attr($title) ?>"
                   class="widefat" />
        </p>

        <p>
            <label for="<?= $feedHtmlId ?>" style="margin-right: 5px;">
                Choose the feed to show:
            </label>
            <select id="<?= $feedHtmlId ?>" name="<?= $feedHtmlName ?>" class="widefat">
                <?php foreach ($feeds as $post): ?>
                    <option value="<?= $post->ID ?>" <?= selected($feed, $post->ID, false) ?>>
                        <?= $post->post_title ?>
                    </option>
                <?php endforeach ?>
            </select>
        </p>
        <?php

    }

    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function update($instance, $prev)
    {
        return $instance;
    }

    /**
     * Retrieves the default values for the widget.
     *
     * @since 0.1
     *
     * @param array $feeds The list of feeds in the database.
     *
     * @return array An associative array of default values.
     */
    public function getDefaults(array $feeds = [])
    {
        return [
            'title' => 'Instagram',
            'feed' => count($feeds) > 0 ? $feeds[0]->ID : '',
        ];
    }
}
