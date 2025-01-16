<?php


class B2S_Onboarding_Item {

    private $onboardingState;

    public function __construct(){

    }

    public function startOnboarding(){
        $options = new B2S_Options(B2S_PLUGIN_BLOG_USER_ID, "B2S_PLUGIN_ONBOARDING");
        $this->onboardingState = $options->_getOption('onboarding_active');
        if($this->onboardingState == 0){
            $options->_setOption('onboarding_active', 1);
            return 1;
        }
        return $this->onboardingState;
    }

    public function getOnboardingHtml($presetStep){

        if((int) $presetStep > 0){
            $step = $presetStep;
        } else {
            $step = $this->getStep();
        }
        $btn1_type = 'btn-primary';
        $btn2_type = 'btn-primary';
        $btn3_type = 'btn-primary';
        $style1 = 'style="display:none;"';
        $style2 = 'style="display:none;"';

        $line1 = "stepwizard-step-blue";
        $line2 = "stepwizard-step-blue";

        $class1 = $step > 1 ? 'b2s-onboarding-step-opacity' : '';

        if($step == 1){
            $btn1_type = 'btn-success';
            $style1 = '';
        } else if($step == 2){
            $btn1_type = 'btn-success';
            $btn2_type = 'btn-success';
            $style1 = '';
            $style2 = '';
            $line1 = "stepwizard-step-green";
        } else if($step == 3){
            $btn1_type = 'btn-success';
            $btn2_type = 'btn-success';
            $btn3_type = 'btn-success';
            $style3 = '';
            $line1 = "stepwizard-step-green";
            $line2 = "stepwizard-step-green";
        }
        

        $content = '';

        $content .= '<div class="text-center">';

        $content .= '<div class="b2s-onboarding-grey-background">';

        $content .= '<div class="stepwizard">';
        $content .= '<div class="stepwizard-row setup-panel align-items-center">';

        $content .= '<div class="stepwizard-step '.$line1.' col-sm-4">';
        $content .= '<a href="#step-1" type="button" id="b2s-onboarding-btn-step-1" class="btn '.$btn1_type.' btn-circle">1</a>';
        $content .= '<p class="mr-5 b2s-onboarding-step-text"><small>'.esc_html__("Connect your social media with Blog2Social", "blog2social").'</small></p>';
        $content .= '<div id="b2s-onboarding-step-1-img-container" class="'.$class1.'" '.$style1.'>';
        $content .= '<img class="b2s-onboarding-step-img" src="'.esc_url(plugins_url('/assets/images/b2s/b2s_onboarding_step1.png', B2S_PLUGIN_FILE)).'" alt="a decorative image of two people drawing lines between boxes">';
        $content .= '<br>';
        $content .= '<a href="admin.php?page=blog2social-network" class="btn text-center b2s-onboarding-button-filled b2s-start-onboarding">'. esc_html__("Connect", "blog2social").'</a>';
        $content .= '</div>';
        $content .= '</div>';

        $content .= '<div class="stepwizard-step '.$line2.' col-sm-4">';
        $content .= '<a href="#step-2" type="button" id="b2s-onboarding-btn-step-2" class="btn '.$btn2_type.' btn-circle">2</a>';
        $content .= '<p class="mr-5 b2s-onboarding-step-text"><small>'.esc_html__("Share your first post", "blog2social").'</small></p>';
        $content .= '<div id="b2s-onboarding-step-2-img-container" '.$style2.'>';
        $content .= '<img class="b2s-onboarding-step-img" src="'.esc_url(plugins_url('/assets/images/b2s/b2s_onboarding_step2.png', B2S_PLUGIN_FILE)).'" alt="a decorative image of two people drawing lines between boxes">';
        $content .= '<br>';
        $content .= '<a href="admin.php?page=blog2social-post" class="btn text-center b2s-onboarding-button-filled b2s-start-onboarding">'. esc_html__("Share posts", "blog2social").'</a>';
        $content .= '</div>';
        $content .= '</div>';


        $content .= '<div class="stepwizard-step col-sm-4">';
        $content .= '<a href="#step-3" type="button" id="b2s-onboarding-btn-step-3" class="btn '.$btn3_type.' btn-circle">3</a>';
        $content .= '<p class="mr-5"><small>'.esc_html__("Try Blog2Social Premium", "blog2social").'</small></p>';
        $content .= '</div>';

        $content .= '</div>';
        $content .= '</div>';


        $content .= '</div>';

        return $content;
    }


    private function getStep(){

        //what happens if someone does premium before posting?

        if(defined("B2S_PLUGIN_TRAIL_END") || B2S_PLUGIN_USER_VERSION > 0){
            return 3;
        } else {
            $hasMadePost = B2S_Tools::hasUserMadePost(B2S_PLUGIN_BLOG_USER_ID);
            
            if($hasMadePost){
                return 3;
            } else {
                $hasConnectedNetwork = B2S_Tools::hasUserConnectedNetwork(B2S_PLUGIN_BLOG_USER_ID);
                if($hasConnectedNetwork){
                    return 2;
                } 
            }
        }

        return 1;
    }
    


}