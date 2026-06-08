<?php

namespace RebelCode\Spotlight\Instagram\Feeds;

use Dhii\Output\TemplateInterface;

/**
 * The template that renders a feed.
 *
 * @since 0.1
 */
class FeedTemplate implements TemplateInterface
{
    /**
     * @inheritDoc
     *
     * @since 0.1
     */
    public function render($ctx = null)
    {
        if (!is_array($ctx)) {
            return '';
        }

        $varName = hash("crc32b", SL_INSTA_VERSION . json_encode($ctx));

        return static::renderFeed($varName, $ctx);
    }

    /**
     * Renders a feed.
     *
     * @since 0.4
     *
     * @param string $varName The JS variable name in `SliFrontCtx`.
     * @param array  $ctx The render context.
     *
     * @return string The rendered feed.
     */
    public static function renderFeed(string $varName, array $ctx)
    {
        $feedOptions = $ctx['feed'] ?? [];
        $accounts = $ctx['accounts'] ?? [];
        $media = $ctx['media'] ?? [];
        $analytics = $ctx['analytics'] ?? false;
        $instanceId = $ctx['instance'] ?? get_the_ID();

        // Convert into JSON, which is also valid JS syntax
        $feedJson = json_encode($feedOptions);
        $accountsJson = json_encode($accounts);
        $mediaJson = json_encode($media);

        // Prepare the HTML class
        $className = 'spotlight-instagram-feed';
        if (array_key_exists('className', $feedOptions) && !empty($feedOptions['className'])) {
            $className .= ' ' . $feedOptions['className'];
        }

        // Output the required HTML and JS
        ob_start();
        ?>
        <div
            class="<?= esc_attr($className) ?>"
            data-feed-var="<?= esc_attr($varName) ?>"
            data-analytics="<?= esc_attr((int) $analytics) ?>"
            data-instance="<?= esc_attr($instanceId) ?>"
        >
        </div>
        <input type="hidden" id="sli__f__<?= esc_attr($varName) ?>" data-json='<?= esc_attr($feedJson) ?>' />
        <input type="hidden" id="sli__a__<?= esc_attr($varName) ?>" data-json='<?= esc_attr($accountsJson) ?>' />
        <input type="hidden" id="sli__m__<?= esc_attr($varName) ?>" data-json='<?= esc_attr($mediaJson) ?>' />
        <?php

        // Trigger the action that will enqueue the required JS bundles
        do_action('spotlight/instagram/enqueue_front_app');

        return ob_get_clean();
    }
}
