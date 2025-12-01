<?php

class B2S_View_Modal_General {

    private $title = '';
    private $subline = '';
    private $smallSubline = '';
    private $listItems = array();
    private $midtext = '';
    private $bottomText = '';
    private $backgroundImage = '';
    private $buttonText = '';
    private $redirectUrl = '';
    private $textColor = '';
    private $titleColor = '';
    private $buttonColor = '';
    private $buttonTextColor = '';
    private $backgroundColor = '';
    private $defaultTextColor = 'b2s-color-white';
    private $defaulttitleColor = 'b2s-color-green';
    private $defaultbuttonColor = 'b2s-bg-green';
    private $defaultbuttonTextColor = 'b2s-color-white';
    private $defaultbackgroundColor = 'b2s-bg-color-dark-grey';
    private $buttonExtraMarginTop = 0;
    private $buttonExtraPaddingBottom = 0;
    private $numberListItems = false;
    private $name = '';
    private $networkId = 0;
    private $content = array();

    public function __construct($name = '', $networkId = 0) {

        $this->name = $name;
        $this->networkId = $networkId;
        $this->setContentArray();
       
    }

    private function setContentArray() {

        $this->content = array(
            "b2sPreFeatureBestTimesModal" => array(
                0 => array(
                    "title" => esc_html__('Best Times Manager', 'blog2social'),
                    "subline" => esc_html__('Plan your social media posts at the perfect time – automatically.', 'blog2social'),
                    "midtext" => esc_html__('The Best Times Manager automatically publishes your posts when your community is most active. This way, you achieve more reach and engagement – completely effortlessly.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-best-times-2.png',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Upgrade Blog2Social', 'blog2social'),
                    "buttonExtraPaddingBottom" => 5,
                    "listitems" => array(
                        esc_html__('Automatic selection of the optimal publishing times', 'blog2social'),
                        esc_html__('More visibility and engagement for your posts', 'blog2social'),
                        esc_html__('Ideal for repeat scheduling & time savings', 'blog2social'),
                    ),
                )
            ),
            "b2sPreFeatureAutoPosterModal" => array(
                0 => array(
                    "title" => esc_html__('Post Automatically', 'blog2social'),
                    "subline" => esc_html__('Save time – Blog2Social posts automatically for you', 'blog2social'),
                    "midtext" => esc_html__('Save time and publish your content automatically – no manual sharing needed. With an upgrade to Blog2Social, your posts practically run themselves:', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-autopost.png',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Activate now', 'blog2social'),
                    "buttonExtraPaddingBottom" => 5,
                    "listitems" => array(
                        esc_html__('Automatically publish and share your posts', 'blog2social'),
                        esc_html__('Use the best posting times for each network', 'blog2social'),
                        esc_html__('Re-share older posts', 'blog2social'),
                    ),
                )
            ),
            "b2sPreFeatureReshareModal" => array(
                0 => array(
                    "title" => esc_html__('Re-Share Post', 'blog2social'),
                    "subline" => esc_html__('Do you want to post your blog article again?', 'blog2social'),
                    "midtext" => esc_html__('With Blog2Social SMART, PRO, or BUSINESS, you can share your posts multiple times – automatically, scheduled, and at the best time for each network.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-resharer.png',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Activate now', 'blog2social'),
                    "buttonExtraPaddingBottom" => 10.5,
                    "listitems" => array(
                        esc_html__('Post content multiple times to increase your reach', 'blog2social'),
                        esc_html__('Best Time Manager: automatically publish at the optimal time', 'blog2social'),
                        esc_html__('Revive old posts and reach new audiences', 'blog2social'),
                    ),
                )
            ),
            "b2sPreFeatureScheduleModal" => array(
                0 => array(
                    "title" => esc_html__('Scheduling', 'blog2social'),
                    "subline" => esc_html__('You want to plan your posts?', 'blog2social'),
                    "midtext" => esc_html__('With an upgrade to Blog2Social, you can easily schedule your social media posts – once, multiple times, or on a recurring basis.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-calendar.png',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Activate now', 'blog2social'),
                    "buttonExtraPaddingBottom" => 8,
                    "listitems" => array(
                        esc_html__('Schedule posts for specific dates in the calendar', 'blog2social'),
                        esc_html__('Automatically share recurring or evergreen posts', 'blog2social'),
                        esc_html__('Use the Best Time Manager for maximum reach', 'blog2social'),
                    ),
                )
            ),
            "b2sProFeatureNetworkGroupsModal" => array(
                0 => array(
                    "title" => esc_html__('Network Groupings', 'blog2social'),
                    "subline" => esc_html__('Your networks. Your structure. Your workflow.', 'blog2social'),
                    "midtext" => esc_html__('With network groupings, you can easily save specific social media combinations and reuse them anytime – ideal for different campaigns or post formats.', 'blog2social'),
                    "bottomText" => esc_html__('Keep an overview even with many projects or clients', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-networks-1.png',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Upgrade Blog2Social', 'blog2social'),
                    "buttonExtraPaddingBottom" => 5,
                    "listitems" => array(
                        esc_html__('Combine multiple profiles, pages, and communities into one grouping', 'blog2social'),
                        esc_html__('Set up once, reuse anytime', 'blog2social'),
                        esc_html__('Start upgrade & use groupings', 'blog2social'),
                    ),
                )
            ),
            "b2sProFeatureEditTemplateModal" => array(
                0 => array(
                    "title" => esc_html__('Post Templates', 'blog2social'),
                    "subline" => esc_html__('Perfect social media posts with one click.', 'blog2social'),
                    "midtext" => esc_html__('Create customized post templates for every network, profile, and format. Save time and boost your reach – with your own templates.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-templates.png',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Unlock now', 'blog2social'),
                    "buttonExtraPaddingBottom" => 5,
                    "listitems" => array(
                        esc_html__('Consistent look for your brand', 'blog2social'),
                        esc_html__('Templates for profiles, pages & groups', 'blog2social'),
                        esc_html__('More reach through better targeting', 'blog2social'),
                    ),
                )
            ),
            "b2sPreFeaturePostFormatModal" => array(
                0 => array(
                    "title" => esc_html__('Post formats', 'blog2social'),
                    "subline" => esc_html__('Control how your content appears on social media', 'blog2social'),
                    "midtext" => esc_html__('With an upgrade to Blog2Social SMART, PRO or BUSINESS, you can choose the perfect format for every network:', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-postformat_480.png',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Upgrade now', 'blog2social'),
                    "buttonExtraPaddingBottom" => 10.5,
                    "listitems" => array(
                        esc_html__('Link post for more website clicks', 'blog2social'),
                        esc_html__('Image Post for maximum visibility in the feed', 'blog2social'),
                        esc_html__('Custom presentation based on each network’s best practices', 'blog2social'),
                    ),
                )
            ),
            //Backup Modal if no network-advertising-Modal is added for network id in network-advertising-modals
            "b2sPreFeatureNetworksModal" => array(//
                0 => array(
                    "title" => esc_html__('Network Connections', 'blog2social'),
                    "subline" => esc_html__('Unlock more networks and profiles – with SMART, PRO, or BUSINESS:', 'blog2social'),
                    "bottomText" => esc_html__('Upgrade to unlock this network or try it for free – risk-free!', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-network-connect.png',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Unlock now', 'blog2social'),
                    "buttonExtraMarginTop" => 7,
                    "buttonExtraPaddingBottom" => 5,
                    "listitems" => array(
                        esc_html__('Post across all relevant channels', 'blog2social'),
                        esc_html__('Plan your content ahead and automate publishing', 'blog2social'),
                        esc_html__('Use custom post formats and images for each network', 'blog2social'),
                    ),
                )
            ),
            "b2sPreFeatureEditAndDeleteModal" => array(
                0 => array(
                    "title" => esc_html__('Post Management', 'blog2social'),
                    "subline" => esc_html__('Would you like to delete a published post or edit a scheduled one?', 'blog2social'),
                    "bottomText" => esc_html__('Unfortunately, this isn’t possible in the Free version – but with SMART, PRO, or BUSINESS you have full control over your content:', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-delete-post.png',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__(' Upgrade now', 'blog2social'),
                    "buttonExtraMarginTop" => 7,
                    "buttonExtraPaddingBottom" => 5,
                    "listitems" => array(
                        esc_html__('Keep an overview of your posts and schedules in the calendar', 'blog2social'),
                        esc_html__('Automatically post and schedule your content', 'blog2social'),
                        esc_html__('Flexibly delete, reschedule, or repost your content', 'blog2social'),
                    ),
                )
            ),
            //Backup Modal if no network-advertising-Modal is added for network id in network-advertising-modals
            "b2sProFeatureNetworksModal" => array(
                0 => array(
                    "title" => esc_html__('Network Profiles', 'blog2social'),
                    "subline" => esc_html__('Connect your Groups and Pages and reach more.', 'blog2social'),
                    "midtext" => esc_html__('Unlock pro features to showcase your brand effectively and scale your social media activities.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-network-connect.png',
                    "backgroundColor" => 'b2s-color-white',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Unlock now', 'blog2social'),
                    "buttonExtraMarginTop" => 10,
                    "listitems" => array(
                        esc_html__('Connect pages and groups on Facebook, LinkedIn, Xing, and VK.', 'blog2social'),
                        esc_html__('Schedule and automatically repost your content.', 'blog2social'),
                        esc_html__('Manage all your social media channels from one central hub.', 'blog2social'),
                    ),
                )
            ),
            "b2sProFeatureUserAppsModal" => array(
                0 => array(
                    "title" => esc_html__('App Limit Reached', 'blog2social'),
                    "subline" => esc_html__('Expand your Blog2Social – more apps, more reach, more flexibility', 'blog2social'),
                    //"smallsubline"=>esc_html__('You’re already using your own Pinterest app interface.', 'blog2social'),
                    "midtext" => esc_html__('Expand the number of apps you can connect: Smart(1), Pro(3), Business(5).', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-app-limit.png',
                    "backgroundColor" => 'b2s-color-white',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('addon_apps'),
                    "buttonText" => esc_html__('Upgrade Blog2Social', 'blog2social'),
                    "buttonExtraMarginTop" => 10,
                    "listitems" => array(
                        esc_html__('Connect multiple Pinterest accounts to your apps with Blog2Social.', 'blog2social'),
                        esc_html__('Schedule your content in advance & automatically.', 'blog2social'),
                        esc_html__('Use custom image and post formats.', 'blog2social'),
                    ),
                )
            ),
            "b2sProFeatureMultiImageModal" => array(
                0 => array(
                    "title" => esc_html__('Pro Feature – Multi-Image Posting', 'blog2social'),
                    "subline" => esc_html__('Tell your story with more images', 'blog2social'),
                    "midtext" => esc_html__('With Blog2Social Premium PRO, you can post multiple images in a single post – perfect for stories, products, or collages.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-multi-images.png',
                    "backgroundColor" => 'b2s-color-white',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Upgrade Blog2Social', 'blog2social'),
                    "buttonExtraMarginTop" => 10,
                    "buttonExtraPaddingBottom" => 5,
                    "listitems" => array(
                        esc_html__('Multiple images per post', 'blog2social'),
                        esc_html__('Auto-posting and scheduling included', 'blog2social'),
                        esc_html__('Reporting & Best Time Manager', 'blog2social'),
                    ),
                )
            ),
            "b2sBusinessFeatureNetworksModal" => array(
                0 => array(
                    "title" => esc_html__('Network Profiles', 'blog2social'),
                    "subline" => esc_html__('Connect your Groups and Pages and reach more.', 'blog2social'),
                    "midtext" => esc_html__('Unlock Business features to showcase your brand effectively and scale your social media activities.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-network-connect.png',
                    "backgroundColor" => 'b2s-color-white',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('upgrade_version'),
                    "buttonText" => esc_html__('Unlock now', 'blog2social'),
                    "buttonExtraMarginTop" => 10,
                    "listitems" => array(
                        esc_html__('Connect pages and groups on Facebook, LinkedIn, Xing, and VK.', 'blog2social'),
                        esc_html__('Schedule and automatically repost your content.', 'blog2social'),
                        esc_html__('Manage all your social media channels from one central hub.', 'blog2social'),
                    ),
                )
            ),
            "b2sBestTimesInfoModal" => array(
                0 => array(
                    "title" => esc_html__('Best Times Manager', 'blog2social'),
                    "subline" => esc_html__('Automatically Use the Best Times – for Greater Reach with Your Posts', 'blog2social'),
                    "midtext" => esc_html__('With the Best Time Manager, Blog2Social automatically schedules your posts at the best times — individually for each social network. This way, you reach your followers when they are online and most likely to see your posts.', 'blog2social'),
                    "bottomText" => esc_html__('Tip: With customized posting schedules for each network, you can create your optimal social media rhythm — effortlessly.', 'blog2social'),
                    "backgroundImage" => '/assets/images/advertising-modal/bg-info-best-times_720.png',
                    "textColor" => 'b2s-color-dark-grey',
                    "backgroundColor" => 'b2s-bg-color-white',
                    "buttonColor" => 'b2s-bg-color-white',
                    "redirectUrl" => B2S_Tools::getSupportLink('besttimes_faq'),
                    "buttonText" => esc_html__('Learn more: Set up and apply individual best times', 'blog2social'),
                    "buttonTextColor" => 'b2s-color-green',
                    "buttonExtraPaddingBottom" => 13,
                    "listitems" => array(
                        esc_html__('In the preview editor, open the “Load Best Times” tab to automatically apply the recommended times.', 'blog2social'),
                        esc_html__('Or click “Load My Time Settings” to use your own preferred posting times.', 'blog2social'),
                        esc_html__('You can edit all times in the preview editor and save them as your default settings.', 'blog2social'),
                    ),
                    "numberListItems" => true
                )
            ),
        );
    }

    private function setName($name) {
        $this->name = $name;
    }

    private function setContent() {

        if (isset($this->name) && !empty($this->name)) {

            if (isset($this->content[$this->name][$this->networkId]) && is_array($this->content[$this->name][$this->networkId])) {

                $this->title = isset($this->content[$this->name][$this->networkId]['title']) ? $this->content[$this->name][$this->networkId]['title'] : '';
                $this->subline = isset($this->content[$this->name][$this->networkId]['subline']) ? $this->content[$this->name][$this->networkId]['subline'] : '';
                $this->smallSubline = isset($this->content[$this->name][$this->networkId]['smallsubline']) ? $this->content[$this->name][$this->networkId]['smallsubline'] : '';
                $this->midtext = isset($this->content[$this->name][$this->networkId]['midtext']) ? $this->content[$this->name][$this->networkId]['midtext'] : '';
                $this->bottomText = isset($this->content[$this->name][$this->networkId]['bottomText']) ? $this->content[$this->name][$this->networkId]['bottomText'] : '';
                $this->backgroundImage = isset($this->content[$this->name][$this->networkId]['backgroundImage']) ? $this->content[$this->name][$this->networkId]['backgroundImage'] : '';
                $this->textColor = isset($this->content[$this->name][$this->networkId]['textColor']) ? $this->content[$this->name][$this->networkId]['textColor'] : $this->defaultTextColor;
                $this->buttonColor = isset($this->content[$this->name][$this->networkId]['buttonColor']) ? $this->content[$this->name][$this->networkId]['buttonColor'] : $this->defaultbuttonColor;
                $this->buttonTextColor = isset($this->content[$this->name][$this->networkId]['buttonTextColor']) ? $this->content[$this->name][$this->networkId]['buttonTextColor'] : $this->defaultbuttonTextColor;
                $this->titleColor = isset($this->content[$this->name][$this->networkId]['titleColor']) ? $this->content[$this->name][$this->networkId]['titleColor'] : $this->defaulttitleColor;
                $this->listItems = isset($this->content[$this->name][$this->networkId]['listitems']) ? $this->content[$this->name][$this->networkId]['listitems'] : array();
                $this->buttonText = isset($this->content[$this->name][$this->networkId]['buttonText']) ? $this->content[$this->name][$this->networkId]['buttonText'] : '';
                $this->redirectUrl = isset($this->content[$this->name][$this->networkId]['redirectUrl']) ? $this->content[$this->name][$this->networkId]['redirectUrl'] : '';
                $this->buttonExtraMarginTop = isset($this->content[$this->name][$this->networkId]['buttonExtraMarginTop']) ? $this->content[$this->name][$this->networkId]['buttonExtraMarginTop'] : 0;
                $this->buttonExtraPaddingBottom = isset($this->content[$this->name][$this->networkId]['buttonExtraPaddingBottom']) ? $this->content[$this->name][$this->networkId]['buttonExtraPaddingBottom'] : 0;
                $this->backgroundColor = isset($this->content[$this->name][$this->networkId]['backgroundColor']) ? $this->content[$this->name][$this->networkId]['backgroundColor'] : $this->defaultbackgroundColor;
                $this->numberListItems = isset($this->content[$this->name][$this->networkId]['numberListItems']) ? $this->content[$this->name][$this->networkId]['numberListItems'] : false;
            }
        }
    }

    private function hasEntry($name="", $networkId = 0) {
        return isset($this->content[$name][$networkId]);
    }

    public function getModalsHtml($modalNames=array()){

        $content = '';

        foreach ($modalNames as $modalName) {

            if($this->hasEntry($modalName)){
                $this->setName($modalName);
                $this->setContent();
                $content .= $this->getHtml();
            }
        }

        return $content;
    }

    private function getHtml() {

        $content  = '';
        $content .= '<div class="modal fade in" id="' . esc_attr($this->name) . '" role="dialog" style="display: none; padding-left: 15px;">';
        $content .= '<div class="modal-dialog">';
        $content .= '<div class="modal-advertising-content ' . esc_attr($this->textColor) . ' ' . esc_attr($this->backgroundColor) . '" style="border-radius: 10px; background-image: url(\'' . esc_url(plugins_url($this->backgroundImage, B2S_PLUGIN_FILE)) . '\');">';
        $content .= '<div class="modal-advertising-header">';
        $content .= '<button type="button" class="close ' . esc_attr($this->textColor) . '" data-dismiss="modal">×</button>';
        $content .= '</div>';
        $content .= '<div class="modal-advertising-body">';
        $content .= '<h4 class="' . esc_attr($this->titleColor) . '">' . esc_html($this->title) . '</h4>';
        $content .= '<h3 style="margin-top: 15px;"><strong>' . esc_html($this->subline) . '</strong></h3>';

        if (!empty($this->smallSubline)) {
            $content .= '<strong><p class="modal-advertising-smallsubline">' . esc_html($this->smallSubline) . '</p></strong>';
        }

        $content .= '<hr class="hr-advertising-left">';
        $content .= '<p class="modal-advertising-midtext">' . esc_html($this->midtext) . '</p>';
        $content .= wp_kses(
            $this->getListHtml(),
            [
                'ul' => ['class' => true],
                'li' => [],
            ]
        );
        $content .= '<p>' . esc_html($this->bottomText) . '</p>';
        $buttonStyle = '';

        if ($this->buttonExtraMarginTop != 0) {
            $buttonStyle = 'style="margin-bottom: ' . esc_attr($this->buttonExtraMarginTop) . 'rem!important;"';
        }

        $content .= '<div style="margin-top: 20px;">';
        $content .= '<a ' . $buttonStyle .
                    ' href="' . esc_url($this->redirectUrl) . '" class="b2s-advertise-upgrade-btn btn ' .
                    esc_attr($this->buttonTextColor) . ' ' . esc_attr($this->buttonColor) . '">' .
                    esc_html($this->buttonText) . '</a>';
        $content .= '</div>';

        if ($this->buttonExtraPaddingBottom != 0) {
            $content .= '<div style="padding-bottom: ' . esc_attr($this->buttonExtraPaddingBottom) . 'rem!important;"></div>';
        }
        $content .= '</div>'; 
        $content .= '</div>';     
        $content .= '</div>';         
        $content .= '</div>';    

        return $content;
    }


    private function getListHtml() {

        if (!is_array($this->listItems) || empty($this->listItems)) {
            return '';
        }
        if ($this->numberListItems) {

            $listNum = 1;
            if (!is_array($this->listItems) || empty($this->listItems)) {
                return '';
            }

            $listhtml = '';
            $listhtml .= '<ul class="">';

            foreach ($this->listItems as $item) {
                $listhtml .= '<li> ' . $listNum . '. ' . esc_html($item) . '</li>';
                $listNum++;
            }

            $listhtml .= '</ul>';

            return $listhtml;
        } else {

            $listhtml = '';
            $listhtml .= '<ul class="list-unstyled">';

            foreach ($this->listItems as $item) {


                $listhtml .= '<li> ' . esc_html($item) . '</li>';
            }

            $listhtml .= '</ul>';

            return $listhtml;
        }
    }
}
