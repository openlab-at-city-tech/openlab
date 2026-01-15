<?php

class B2S_View_Modal_Network {

    private $content = array();

    public function __construct() {
        $this->setContentArray();
    }

    private function setContentArray() {

        $this->content = array(
            3 => array(
                "title" => __('Empower your company’s voice with LinkedIn', 'blog2social'),
                "subline" => __('Post strategically on pages & in groups with Blog2Social PRO', 'blog2social'),
                "listitems" => array(
                    __('Share posts on company pages and groups', 'blog2social'),
                    __('Customize texts, images, and links', 'blog2social'),
                    __('Schedule your content or publish instantly', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social PRO', 'blog2social'),
            ),
            6 => array(
                "title" => __('Showcase your content visually on Pinterest', 'blog2social'),
                "subline" => __('Plan and share Pins starting with Blog2Social SMART', 'blog2social'),
                "listitems" => array(
                    __('Create Pins with images, descriptions, and links', 'blog2social'),
                    __('Assign content to specific boards', 'blog2social'),
                    __('Schedule content or publish immediately', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            11 => array(
                "title" => __('Publish articles strategically in Medium publications', 'blog2social'),
                "subline" => __('Blog2Social PRO delivers your posts directly to Medium', 'blog2social'),
                "listitems" => array(
                    __('Post content directly to your publication', 'blog2social'),
                    __('SEO-optimized articles with links and formatting', 'blog2social'),
                    __('Schedule posts or publish instantly', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social PRO', 'blog2social'),
            ),
            12 => array(
                "title" => __('Connect with Instagram', 'blog2social'),
                "subline" => __('Available starting with the Blog2Social SMART plan', 'blog2social'),
                "bottomText" => sprintf(
                        // translators: %s are linktags
                        __('You can share video and story posts with the %1$sVideo Add-on%2$s', 'blog2social'),
                        '<a href="' . esc_url(B2S_Tools::getSupportLink('addon_video')) . '">',
                        '</a>'
                ),
                "listitems" => array(
                    __('Publish image and carousel posts', 'blog2social'),
                    __('Reshare older posts', 'blog2social'),
                    __('Schedule your posts days, weeks, or even months in advance', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            17 => array(
                "title" => __('Reach communities on VK.com with precision', 'blog2social'),
                "subline" => __('Use the PRO features of Blog2Social for VK', 'blog2social'),
                "listitems" => array(
                    __('Post content to VK profiles, pages, and groups', 'blog2social'),
                    __('Publish images, links, and texts', 'blog2social'),
                    __('Schedule in advance or share instantly — completely flexible', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social PRO', 'blog2social'),
            ),
            18 => array(
                "title" => __('Gain more visibility with Google Posts', 'blog2social'),
                "subline" => __('Post and schedule with Blog2Social PRO', 'blog2social'),
                "listitems" => array(
                    __('Share posts directly on your business profile', 'blog2social'),
                    __('Publish images, links, and texts', 'blog2social'),
                    __('Increase your visibility in Google Search', 'blog2social'),
                    __('Schedule your posts in advance or publish instantly', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social PRO', 'blog2social'),
            ),
            19 => array(
                "title" => __('Professional networking with XING', 'blog2social'),
                "subline" => __('From Blog2Social: PRO posting and scheduling', 'blog2social'),
                "listitems" => array(
                    __('Share posts on your XING profile', 'blog2social'),
                    __('Publish texts, links, and images', 'blog2social'),
                    __('Schedule posts in advance or share them instantly', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social PRO', 'blog2social'),
            ),
            24 => array(
                "title" => __('Send updates directly to your Telegram channel', 'blog2social'),
                "subline" => __('Keep your community up to date with Blog2Social BUSINESS', 'blog2social'),
                "listitems" => array(
                    __('Post text updates, images, and links', 'blog2social'),
                    __('Automatically provide channels with content', 'blog2social'),
                    __('Publish instantly or schedule posts', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social BUSINESS', 'blog2social'),
            ),
            25 => array(
                "title" => __('Keep your Blogger blog regularly updated', 'blog2social'),
                "subline" => __('Automatically publish your content on Blogger with Blog2Social SMART', 'blog2social'),
                "listitems" => array(
                    __('Publish content directly to your Blogger blog', 'blog2social'),
                    __('With formatting, images, and links', 'blog2social'),
                    __('Post instantly or schedule your posts', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            26 => array(
                "title" => __('Share your creative projects on Ravelry', 'blog2social'),
                "subline" => __('Automatically save interesting content with Blog2Social SMART', 'blog2social'),
                "listitems" => array(
                    __('Save links as articles in Instapaper', 'blog2social'),
                    __('Add custom descriptions', 'blog2social'),
                    __('Save posts instantly or schedule them', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            27 => array(
                "title" => __('Save content as reading tips in Instapaper', 'blog2social'),
                "subline" => __('With Blog2Social SMART, automatically save interesting content', 'blog2social'),
                "listitems" => array(
                    __('Save posts instantly or schedule them', 'blog2social'),
                    __('Add custom descriptions', 'blog2social'),
                    __('Save links as articles in Instapaper', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            32 => array(
                "title" => __('Publish and schedule videos on YouTube', 'blog2social'),
                // translators: %s are linktags
                "subline" => sprintf(__('YouTube is available via the %1$sVideo Add-on%2$s', 'blog2social'),
                        '<a href="' . esc_url(B2S_Tools::getSupportLink('addon_video')) . '">',
                        '</a>'
                ),
                "listitems" => array(
                    __('Upload videos with title, description & tags', 'blog2social'),
                    __('Set thumbnails and visibility', 'blog2social'),
                    __('Publish instantly or schedule for later', 'blog2social'),
                ),
                "buttonText" => __('Activate Video Add-on', 'blog2social'),
            ),
            35 => array(
                "title" => __('Share professional videos on Vimeo', 'blog2social'),
                // translators: %s are linktags
                "subline" => sprintf(__('Schedule your videos with the %1$sVideo Add-on%2$s from Blog2Social', 'blog2social'),
                        '<a href="' . esc_url(B2S_Tools::getSupportLink('addon_video')) . '">',
                        '</a>'
                ),
                "listitems" => array(
                    __('Upload directly from the Blog2Social dashboard', 'blog2social'),
                    __('Manage title, description & thumbnail', 'blog2social'),
                    __('Publish instantly or schedule for later', 'blog2social'),
                ),
                "buttonText" => __('Activate Video Add-on', 'blog2social'),
            ),
            36 => array(
                "title" => __('Bring your content to TikTok – structured and smart', 'blog2social'),
                "subline" => __('Available starting with the Blog2Social Pro plan', 'blog2social'),
                "bottomText" => sprintf(
                        // translators: %s are linktags
                        __('Plan and automatically publish your content long-term + Share videos, stories & more formats with the %1$sVideo Add-on%2$s', 'blog2social'),
                        '<a href="' . esc_url(B2S_Tools::getSupportLink('addon_video')) . '">',
                        '</a>'
                ),
                "listitems" => array(
                    __('Share images directly on TikTok', 'blog2social'),
                    __('Add your own titles and hashtags for each post', 'blog2social'),
                    __('Plan and automatically publish your content long-term', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social PRO', 'blog2social'),
            ),
            38 => array(
                "title" => __('Communicate decentrally with Mastodon', 'blog2social'),
                "subline" => __('Blog2Social PRO makes your account Mastodon-ready', 'blog2social'),
                "listitems" => array(
                    __('Post toots directly from the dashboard', 'blog2social'),
                    __('Publish texts, links, and images', 'blog2social'),
                    __('Schedule content or send instantly', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social PRO', 'blog2social'),
            ),
            39 => array(
                "title" => __('Send updates directly to your Discord channel', 'blog2social'),
                "subline" => __('Connect your channel and schedule posts with Blog2Social SMART', 'blog2social'),
                "listitems" => array(
                    __('Share content automatically in channels', 'blog2social'),
                    __('Post text, images, and links', 'blog2social'),
                    __('Schedule in advance or share instantly — easily', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            42 => array(
                "title" => __('Automate team communication with HumHub', 'blog2social'),
                // translators: %s are linktags
                "subline" => sprintf(__('This network is available via the %1$sHumhub Add-on%2$s', 'blog2social'),
                        '<a href="' . esc_url(B2S_Tools::getSupportLink('addon_network_integration')) . '">',
                        '</a>'
                ),
                "listitems" => array(
                    __('Send posts directly to your HumHub space', 'blog2social'),
                    __('Publish texts, links, and images', 'blog2social'),
                    __('Post instantly or schedule for late', 'blog2social'),
                ),
                "buttonText" => __('Upgrade Blog2Social', 'blog2social'),
            ),
            43 => array(
                "title" => __('Starte jetzt auf Bluesky durch', 'blog2social'),
                "subline" => __('Post and schedule with Blog2Social SMART', 'blog2social'),
                "listitems" => array(
                    __('Share short posts on the decentralized network', 'blog2social'),
                    __('Publish texts, links, and images', 'blog2social'),
                    __('Schedule your posts in advance or publish instantly', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            44 => array(
                "title" => __('Share your content on Threads', 'blog2social'),
                "subline" => __('Post and schedule with Blog2Social SMART', 'blog2social'),
                "listitems" => array(
                    __('Share your content on Meta’s platform for conversations', 'blog2social'),
                    __('Publish texts and images', 'blog2social'),
                    __('Post instantly or schedule in advance', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
            45 => array(
                "title" => __('Automate your posts for X (formerly Twitter)', 'blog2social'),
                "subline" => __('Post and schedule with the X Add-on for Blog2Social', 'blog2social'),
                "bottomText" => sprintf(
                        // translators: %s are linktags
                        __('The %1$sX Add-on%2$s is seperately available', 'blog2social'),
                        '<a href="' . esc_url(B2S_Tools::getSupportLink('addon_network_integration')) . '">',
                        '</a>'
                ),
                "listitems" => array(
                    __('Schedule your posts in advance or publish instantly', 'blog2social'),
                    __('Create connected threads for longer posts', 'blog2social'),
                    __('Publish posts with up to 280 characters', 'blog2social'),
                    __('Share text, images & links', 'blog2social'),
                ),
                "buttonText" => __('Unlock X Add-on', 'blog2social'),
            ),
            46 => array(
                "title" => __('Keep your communities informed with Band', 'blog2social'),
                "subline" => __('With Blog2Social SMART, you can also automate your Band posts', 'blog2social'),
                "listitems" => array(
                    __('Share posts in groups or chats', 'blog2social'),
                    __('Customize text, images, and links', 'blog2social'),
                    __('Publish instantly or schedule for later', 'blog2social'),
                ),
                "buttonText" => __('Upgrade to Blog2Social SMART', 'blog2social'),
            ),
        );
    }

    public function getHtml() {

        $content  = '';
        $content .= '<div class="modal fade in modal-advertising-network-modal-network" role="dialog" style="display: none;">';
        $content .= '    <div class="modal-dialog modal-advertising-network">';
        $content .= '        <div class="modal-content">';
        $content .= '            <div class="modal-advertising-network-header">';
        $content .= '                <button type="button" class="close" data-dismiss="modal">×</button>';
        $content .= '            </div>';
        $content .= '            <div class="modal-body modal-advertising-network-body">';
        $content .= $this->getTitleHtml();
        $content .= $this->getSublineHtml();
        $content .= wp_kses(
            $this->getListHtml(),
            array(
                'ul' => array(
                    'class' => true,
                    'data-network-id' => true,
                ),
                'li' => array(),
            )
        );

        $content .= $this->getBottomText();
        $content .= $this->getButton();
        $content .= '            </div>'; 
        $content .= '        </div>';    
        $content .= '    </div>';         
        $content .= '</div>';          

        return $content;
    }


    private function getTitleHtml() {
        $content = '';

        foreach ($this->content as $networkId => $value) {

            $title    = isset($value['title']) ? esc_html($value['title']) : '';
            $imgUrl   = esc_url(plugins_url('/assets/images/portale/' . $networkId . '_flat.png', B2S_PLUGIN_FILE));
            $network  = esc_attr($networkId);

            $content .= '<h4 style="display: none;" class="modal-advertising-network-title" data-network-id="' . $network . '">';
            $content .= '    <img src="' . $imgUrl . '" class="modal-advertising-network-logo">';
            $content .=      $title;
            $content .= '</h4>';
        }

        return $content;
    }

    private function getSublineHtml() {
        $content = '';

        foreach ($this->content as $networkId => $value) {

            $subline = isset($value['subline'])
                ? wp_kses($value['subline'], array(
                        'strong' => array(),
                        'a'      => array('href' => array())
                ))
                : '';

            $network = esc_attr($networkId);

            $content .= '<p style="display: none;" class="modal-advertising-network-subline" data-network-id="' . $network . '">';
            $content .=     $subline;
            $content .= '</p>';
        }

        return $content;
    }

    private function getListHtml() {

        $listhtml = '';

        foreach ($this->content as $networkId => $value) {

            $listItems = isset($value['listitems']) ? $value['listitems'] : array();

            if (!is_array($listItems) || empty($listItems)) {
                return '';
            }

            $listhtml .= '<ul class="modal-advertising-network-list" data-network-id="' . esc_attr($networkId) . '">';

            foreach ($listItems as $item) {
                $listhtml .= '<li> ' . esc_html($item) . '</li>';
            }

            $listhtml .= '</ul>';
        }

        return $listhtml;
    }

    private function getBottomText() {

        $content = '';

        foreach ($this->content as $networkId => $value) {

            $bottomText = isset($value['bottomText']) ? $value['bottomText'] : '';

            if (!empty($bottomText)) {

                $network  = esc_attr($networkId);
                $iconUrl  = esc_url(plugins_url('/assets/images/advertising-modal/info-icon-blue-16px.png', B2S_PLUGIN_FILE));
                $text     = wp_kses($bottomText, ['a' => ['href' => []]]);

                $content .= '<p class="info-p modal-advertising-network-bottomtext" data-network-id="' . $network . '">';
                $content .= '    <img src="' . $iconUrl . '" class="info-icon" style="margin-right:3px;">';
                $content .=      $text;
                $content .= '</p>';
            }
        }

        return $content;

    }
 
    private function getButton() {
        $content = '';

        foreach ($this->content as $networkId => $value) {

            $url        = isset($value['buttonUrl']) 
                            ? esc_url($value['buttonUrl']) 
                            : esc_url(B2S_Tools::getSupportLink('upgrade_version'));

            $buttonText = isset($value['buttonText']) ? esc_html($value['buttonText']) : '';
            $network    = esc_attr($networkId);

            $content .= '<a data-network-id="' . $network . '" href="' . $url . '" class="btn modal-advertising-network-upgrade-btn b2s-bg-green b2s-color-white" style="margin-top: 1rem;">';
            $content .=      $buttonText;
            $content .= '</a>';
        }

        return $content;
    }


}
