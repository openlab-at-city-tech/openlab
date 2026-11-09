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
                    }
                    foreach (unserialize(B2S_PLUGIN_CHANGELOG_CONTENT) as $key => $value) {
                        if (!in_array($key, array('new', 'improvements', 'fixed', 'upcoming','fixes & tweaks')) || !is_array($value) || empty($value)) {
                            continue;
                        }
                       
                        if ($key == 'new') {
                            $content .= '<h3 class="news-modal-heading">' . esc_html__('New', 'blog2social') . '</h3>';
                        } else if ($key == 'improvements') {
                            $content .= '<h3 class="news-modal-heading">' . esc_html__('Improvements', 'blog2social') . '</h3>';
                        } else if ($key == 'fixed') {
                            $content .= '<h3 class="news-modal-heading">' . esc_html__('Fixed', 'blog2social') . '</h3>';
                        } else if ($key == 'upcoming') {
                            $content .= '<h3 class="news-modal-heading">' . esc_html__('Upcoming Integrations', 'blog2social') . '</h3>';
                        } else if ($key == 'fixes & tweaks') {
                            $content .= '<h3 class="news-modal-heading">' . esc_html__('Fixes & Tweaks', 'blog2social') . '</h3>';
                        }
                        $content .= '<ul class="">';

                        // New structure: single array with 'headings' and 'texts' keys
                        if (isset($value['texts']) && is_array($value['texts'])) {
                            $texts = $value['texts'];
                            $headings = isset($value['headings']) && is_array($value['headings']) ? $value['headings'] : array();
                            $headingbadges = isset($value['headingbadges']) && is_array($value['headingbadges']) ? $value['headingbadges'] : array();
                            $i = 0;
                            foreach ($texts as $text) {
                                $content .= "<div class='b2s-changelog-item'>"; 
                                $heading = isset($headings[$i]) ? $headings[$i] : '';
                                $headingbadge = isset($headingbadges[$i]) ? $headingbadges[$i] : '';
                               
                                if (!empty($headingbadge)) {
                                    $content .= '<span class="label label-warning b2s-font-size-12">' . $headingbadge . "".'</span>';
                                }
                                if (!empty($heading)) {
                                    $content .= '<strong>'.'  ' . $heading . '</strong><br>';
                                }
                                if (!empty($text)) {
                                    $content .= '<p>' . $text . '</p>';
                                }
                                $content .= '</div><hr class="news-modal-hr">';
                          
                                $i++;
                            }
                            
                        } else {
                            // Old structure: flat list of string entries
                            foreach ($value as $entry) {
                                if (is_string($entry)) {
                                    $content .= '<li>' . $entry . '</li>';
                                }
                            }
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
