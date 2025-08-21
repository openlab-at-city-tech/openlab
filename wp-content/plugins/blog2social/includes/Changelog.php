<?php

class B2S_Changelog {

    public static function getChangelogContent() {
        $content = '';
        if (defined('B2S_PLUGIN_CHANGELOG_CONTENT') && !empty(array_filter(unserialize(B2S_PLUGIN_CHANGELOG_CONTENT)))) {
            $b2sLastVersion = get_option('b2s_plugin_version');
            if ($b2sLastVersion !== false) {
                $changelogOptions = get_option('B2S_PLUGIN_CHANGELOG');
                if ($changelogOptions === false || !isset($changelogOptions['last_shown_version'])) {
                    $changelogOptions = array(
                        'last_shown_version' => 0
                    );
                }
                $showChangelog = false;
                if (isset($changelogOptions['last_shown_version']) && (int) $changelogOptions['last_shown_version'] < (int) $b2sLastVersion) {
                    $showChangelog = true;
                    update_option('B2S_PLUGIN_CHANGELOG', array('last_shown_version' => $b2sLastVersion), false);
                }

                if ($showChangelog) {
                    $changelogContent = unserialize(B2S_PLUGIN_CHANGELOG_CONTENT);

                    $content .= '<div class="b2s-changelog-body">';
                    if (isset($changelogContent['version_info']) && !empty($changelogContent['version_info'])) {
                        $content .= '<p class="b2s-font-bold">' . $changelogContent['version_info'] . '</p>';
                        $content .= '<br>';
                    }
                    foreach (unserialize(B2S_PLUGIN_CHANGELOG_CONTENT) as $key => $value) {
                        if (!in_array($key, array('new', 'improvements', 'fixed', 'upcoming')) || !is_array($value) || empty($value)) {
                            continue;
                        }
                        if ($key == 'new') {
                            $content .= '<p class="label label-success b2s-font-size-12">' . esc_html__('New', 'blog2social') . '</p>';
                        } else if ($key == 'improvements') {
                            $content .= '<p class="label label-info b2s-font-size-12">' . esc_html__('Improvements', 'blog2social') . '</p>';
                        } else if ($key == 'fixed') {
                            $content .= '<p class="label label-warning b2s-font-size-12">' . esc_html__('Fixed', 'blog2social') . '</p>';
                        } else if ($key == 'upcoming') {
                            $content .= '<p class="label label-danger b2s-font-size-12">' . esc_html__('Upcoming Integrations', 'blog2social') . '</p>';
                        }
                        $content .= '<ul class="b2s-changelog-list">';
                        foreach ($value as $entry) {
                            $content .= '<li>' . esc_html($entry, 'blog2social') . '</li>';
                        }
                        $content .= '</ul>';
                    }
                    $content .= '<br>';
                    $content .= '<a href="' . esc_url(B2S_Tools::getSupportLink('faq_direct')) . '" target="_blank">' . esc_html__('Get all the details on the latest update here', 'blog2social') . '</a>';
                    $content .= '</div>';
                }
            }
        }
        return $content;
    }
}
