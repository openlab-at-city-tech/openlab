<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\RecommendedPlugins;

/**
 * Renders the "Recommended plugins" admin page, using core's plugin card markup so that core's "updates" script can
 * install plugins in place, the same way the Add Plugins screen does.
 */
class RecommendedPluginsPage
{
    /** @var RecommendedPlugins */
    protected $plugins;

    public function __construct(RecommendedPlugins $plugins)
    {
        $this->plugins = $plugins;
    }

    public function __invoke(): string
    {
        $cards = '';
        foreach ($this->plugins->getAll() as $plugin) {
            $cards .= $this->renderCard($plugin);
        }

        return sprintf(
            '<div class="wrap sli-recommended-plugins">
                <h1>%s</h1>
                <p>%s</p>
                <div id="plugin-filter" class="wp-list-table widefat plugin-install">
                    <div id="the-list">%s</div>
                </div>
            </div>',
            esc_html__('Recommended plugins', 'sl-insta'),
            esc_html__('Free plugins from the team behind Spotlight.', 'sl-insta'),
            $cards
        );
    }

    protected function renderCard(array $plugin): string
    {
        $slug = $plugin['slug'];
        $detailsUrl = $this->plugins->getDetailsUrl($slug);

        $detailsLink = sprintf(
            '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s">%s</a>',
            esc_url($detailsUrl),
            /* translators: %s: plugin name. */
            esc_attr(sprintf(__('More information about %s', 'sl-insta'), $plugin['name'])),
            esc_html__('More Details', 'sl-insta')
        );

        return sprintf(
            '<div class="plugin-card plugin-card-%1$s">
                <div class="plugin-card-top">
                    <div class="name column-name">
                        <h3>
                            <a href="%2$s" class="thickbox open-plugin-details-modal">
                                %3$s
                                <img src="%4$s" class="plugin-icon" alt="" />
                            </a>
                        </h3>
                    </div>
                    <div class="action-links">
                        <ul class="plugin-action-buttons"><li>%5$s</li><li>%6$s</li></ul>
                    </div>
                    <div class="desc column-description">
                        <p>%7$s</p>
                        <p class="authors"><cite>%8$s</cite></p>
                    </div>
                </div>
            </div>',
            esc_attr(sanitize_html_class($slug)),
            esc_url($detailsUrl),
            esc_html($plugin['name']),
            esc_url($plugin['icon']),
            $this->renderButton($plugin),
            $detailsLink,
            esc_html($plugin['description']),
            esc_html__('By RebelCode', 'sl-insta')
        );
    }

    protected function renderButton(array $plugin): string
    {
        $slug = $plugin['slug'];
        $status = $this->plugins->getStatus($slug);

        if ($status === RecommendedPlugins::NOT_INSTALLED) {
            return sprintf(
                '<a class="install-now button" data-slug="%s" data-name="%s" href="%s" aria-label="%s" role="button">%s</a>',
                esc_attr($slug),
                esc_attr($plugin['name']),
                esc_url($this->plugins->getInstallUrl($slug)),
                /* translators: %s: plugin name. */
                esc_attr(sprintf(__('Install %s now', 'sl-insta'), $plugin['name'])),
                esc_html__('Install Now', 'sl-insta')
            );
        }

        $file = $this->plugins->getFile($slug);

        if ($status === RecommendedPlugins::INSTALLED && current_user_can('activate_plugin', $file)) {
            return sprintf(
                '<a class="button activate-now button-primary" href="%s" aria-label="%s" role="button">%s</a>',
                esc_url($this->plugins->getActivateUrl($file)),
                /* translators: %s: plugin name. */
                esc_attr(sprintf(__('Activate %s', 'sl-insta'), $plugin['name'])),
                esc_html__('Activate', 'sl-insta')
            );
        }

        $label = $status === RecommendedPlugins::ACTIVE
            ? __('Active', 'sl-insta')
            : __('Installed', 'sl-insta');

        return sprintf(
            '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
            esc_html($label)
        );
    }
}
