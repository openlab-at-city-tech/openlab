<?php
//case:36
class B2S_Settings_Share{
    
    private $networkId=0;
    private $networkAuthId=0;
    private $currentShareSettings=array();
    private $allowNetworkShareSettings = array(36);
    private $prepostDetails=null;

    public function __construct($networkAuthId, $networkId, $currentShareSettings) {
        
        $this->networkAuthId = $networkAuthId;
        $this->networkId = $networkId;
        $this->currentShareSettings = $currentShareSettings;
    }


    public function getShareSettingsHtml($preview=false, $video=false){
        
        if(!in_array($this->networkId, $this->allowNetworkShareSettings)){
            return false;
        }

        $content='<div>';

        if($this->networkId == 36){

            $privacyValue = isset($this->currentShareSettings[$this->networkAuthId]['status_privacy'])? $this->currentShareSettings[$this->networkAuthId]['status_privacy'] : ($preview ? '' : 'PUBLIC_TO_EVERYONE');
            $allowComment = isset($this->currentShareSettings[$this->networkAuthId]['allow_comment']) && $this->currentShareSettings[$this->networkAuthId]['allow_comment'] === true ? $this->currentShareSettings[$this->networkAuthId]['allow_comment'] : false;
            $allowStitch = isset($this->currentShareSettings[$this->networkAuthId]['allow_stitch']) && $this->currentShareSettings[$this->networkAuthId]['allow_stitch'] === true ? $this->currentShareSettings[$this->networkAuthId]['allow_stitch'] : false;
            $allowDuet = isset($this->currentShareSettings[$this->networkAuthId]['allow_duet']) && $this->currentShareSettings[$this->networkAuthId]['allow_duet'] === true ? $this->currentShareSettings[$this->networkAuthId]['allow_duet'] : false;
            $promotionOwnBrand = isset($this->currentShareSettings[$this->networkAuthId]['promotion_option_organic']) && $this->currentShareSettings[$this->networkAuthId]['promotion_option_organic'] === true ? $this->currentShareSettings[$this->networkAuthId]['promotion_option_organic'] : false;
            $promotionThirdParty = isset($this->currentShareSettings[$this->networkAuthId]['promotion_option_branded']) && $this->currentShareSettings[$this->networkAuthId]['promotion_option_branded'] === true? $this->currentShareSettings[$this->networkAuthId]['promotion_option_branded'] : false;
            $shareAsDraft = $shareAsDraft = (isset($this->currentShareSettings[$this->networkAuthId]['share_as_draft']) || $preview)? (isset($this->currentShareSettings[$this->networkAuthId]['share_as_draft'])? $this->currentShareSettings[$this->networkAuthId]['share_as_draft']: false ) : true;
            $toggleOn = ($promotionOwnBrand || $promotionThirdParty)? true : false;
            
            $prepostDetails = json_decode(B2S_Tools::getPrePostDetails($this->networkAuthId), true);
            $this->prepostDetails = $prepostDetails;
            if(isset($prepostDetails['prepostsettings']['data']) && !empty($prepostDetails['prepostsettings']['data'])){
                
                $data= $prepostDetails['prepostsettings']['data'];
                $commentDisabled= isset($data['comment_disabled'])? $data['comment_disabled'] : false;
                $duetDisabled= isset($data['duet_disabled'])? $data['duet_disabled'] : false;
                $privacyLevelOptions = isset($data['privacy_level_options'])? $data['privacy_level_options'] : array();
                $stitchDisabled= isset($data['stitch_disabled'])? $data['stitch_disabled'] : false;
            }
           
            if(!$preview){
                
                $content .= '<select style="max-width: 100%!important;" class="form-control b2s-select-area b2s-tiktok-form-select" id="b2s[' . esc_attr($this->networkAuthId) . '][tiktok_share_mode]" data-network-auth-id="' . esc_attr($this->networkAuthId) . '" name="b2s[' . esc_attr($this->networkAuthId) . '][tiktok_share_mode]">';
                $content .= '<option value="0" '. ($shareAsDraft   ? 'selected' : '' ) . '>'.esc_html__("share as draft", "blog2social").'</option>';
                $content .= '<option value="1" '.(!$shareAsDraft  ? 'selected' : '' ). '>'.esc_html__("share directly", "blog2social").'</option>';
                $content .= '</select>';
            }

            $content .= '<div class="tiktok-share-settings" data-network-auth-id="' . esc_attr($this->networkAuthId) . '" '. ($shareAsDraft ? 'style="display:none;"' : '' ) . '>';


            $privacySettings = array(
                            "PUBLIC_TO_EVERYONE" => array("value" => "PUBLIC_TO_EVERYONE", "label" => esc_html__("Public to everyone", "blog2social")),
                            "FOLLOWER_OF_CREATOR" => array("value" => "FOLLOWER_OF_CREATOR", "label" => esc_html__("Followers of creator", "blog2social")),
                            "MUTUAL_FOLLOW_FRIENDS" => array("value" => "MUTUAL_FOLLOW_FRIENDS", "label" => esc_html__("Mutual follow friends", "blog2social")),
                            "SELF_ONLY" => array("value" => "SELF_ONLY", "label" => esc_html__("Self only", "blog2social")),
                        );

            $content .= '<label>'.esc_html__("Who can view the post?", "blog2social").'</label>';
            $content .= '<select style="max-width: 100%!important;" class="form-control b2s-select-area b2s-tiktok-status_privacy" data-network-auth-id="' . esc_attr($this->networkAuthId) . '" id="b2s[' . esc_attr($this->networkAuthId) . '][status_privacy]" name="b2s[' . esc_attr($this->networkAuthId) . '][status_privacy]">';

            if($preview){
                $content .= '<option selected value=""></option>';
            }

            if(empty($privacyLevelOptions)){
                $privacyLevelOptions = array("SELF_ONLY");
            }

            foreach($privacySettings as $option){
                if(isset($option['value']) && isset($option["label"])){
                    if(in_array($option['value'], $privacyLevelOptions) ){ 
                        $content .= '<option value="'.esc_attr($option['value']).'" '. ($option['value'] == $privacyValue ? " selected " :""). ' >' .$option["label"]. '</option>';
                    }
                }
            }

            $content.= '</select>';

            $content .= '<label>'.esc_html__("Allow users to", "blog2social").'</label>';
            $content.= '<div class="b2s-tiktok-allow-options b2s-tiktok-menu">';
            $content.= '<input '. ($commentDisabled? " disabled " : "").'   '.($allowComment && !$commentDisabled ? "checked" : "").'      type="checkbox" name="b2s[' . esc_attr($this->networkAuthId) . '][allow_comment]" id="b2s[' . esc_attr($this->networkAuthId) . '][b2sTiktokAllowComment]" data-network-count="-1" data-network-auth-id="' . esc_attr($this->networkAuthId) . '" >';
            $content .= '<label> ' . esc_html__('Comment', 'blog2social') . '</label>';   
               
            if(!$preview  || $video){
                $content .= ' <input '. ($duetDisabled? " disabled " : "").'  '.($allowDuet  && !$duetDisabled? "checked" : "").' type="checkbox" name="b2s[' . esc_attr($this->networkAuthId) . '][allow_duet]" id="b2s[' . esc_attr($this->networkAuthId) . '][b2sTiktokAllowDuet]" data-network-count="-1" data-network-auth-id="' . esc_attr($this->networkAuthId) . '" data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
                $content .= '<label> ' . esc_html__('Duet', "blog2social") . '</label>';
                $content .= ' <input '. ($stitchDisabled? " disabled " : "").' '.($allowStitch && !$stitchDisabled ? "checked" : "").'  type="checkbox" name="b2s[' . esc_attr($this->networkAuthId) . '][allow_stitch]" id="b2s[' . esc_attr($this->networkAuthId) . '][b2sTiktokAllowStitch]" data-network-count="-1" data-network-auth-id="' . esc_attr($this->networkAuthId) . '" data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
                $content .= '<label> ' . esc_html__('Stitch', "blog2social") . '</label>';
            }

            $content .= '</div>';

            $content .= '<label>'.esc_html__("Disclose video/photo content",  "blog2social").'</label>';
            $content .= '<div class="b2s-tiktok-promotion b2s-tiktok-menu">';
            $content .= '<div>
                        <div class="toggle tiktok-promotional-toggle btn btn-xs btn-primary off" data-toggle="toggle" style="width: 90px; height: 22px; float:left;"  name="b2s[' . esc_attr($this->networkAuthId) . '][b2s-tiktok-disclose-toggle]"  data-network-auth-id="' . esc_attr($this->networkAuthId) . '">
                        <input data-size="mini" data-toggle="toggle" data-width="90" data-height="22" data-onstyle="primary" data-on="ON" data-off="OFF" checked=""  name="b2s[' . esc_attr($this->networkAuthId) . '][b2s-tiktok-disclose-input]" class="" data-area-type="manuell" value="1" type="checkbox">
                        <div class="toggle-group">
                        <label class="btn btn-primary btn-xs toggle-on" style="line-height: 14px;">ON</label>
                        <label class="btn btn-default btn-xs active toggle-off" style="line-height: 14px;">OFF</label>
                        <span class="toggle-handle btn btn-default btn-xs"></span>
                        </div>
                        </div>
                        </div>
                    <div style="clear: both;"></div>
                    <div hidden id="b2s[' . esc_attr($this->networkAuthId) . '][b2s-tiktok-toggle-on]">'.($toggleOn ? '1' : '0').'</div>';

            $content .= '<div class="b2s-tiktok-disclose-info"  data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
            $content .= '<label> ' . esc_html__("Turn on to disclose that this video/photo promotes goods or services in exchange for something of value. Your video/photo could promote yourself, a third party or both.", 'blog2social') . '</label>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '<div class="clearfix"></div><div class="alert alert-info b2s-tiktok-promotional-note"   id="b2s[' . esc_attr($this->networkAuthId) . '][b2sPromotional]" style="display:none;">' . esc_html__("Your photo/video will be labeled as 'Promotional content'.", "blog2social") .'</div>';
            $content.= '<div class="clearfix"></div><div class="alert alert-info b2s-tiktok-paid-partnership-note"  id="b2s[' . esc_attr($this->networkAuthId) . '][b2sPaidPartnership]" style="display:none;">' . esc_html__("Your photo/video will be labeled as 'Paid partnership'.", "blog2social") .'</div>';

            $content .= '<div class="b2s-tiktok-branded-private-notice" style="display:none;" data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
            $content .= '<label> ' . esc_html__("Branded content can't be self-only", 'blog2social') . '</label>';
            $content .= '</div>';

            $content .= '<div class="b2s-tiktok-promotion-options b2s-tiktok-menu b2s-margin-bottom-10" style="display:none;" data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
            $content .= '<div>';

            $content .= '<div class="b2s-margin-bottom-10">';
            $content .= '<input '.($promotionOwnBrand ? "checked" : "").'  type="checkbox" value="'.($promotionOwnBrand ? "on" : "off").'"  class="b2s-tiktok-promotion-option" name="b2s[' . esc_attr($this->networkAuthId) . '][promotion_option_organic]" id="b2s[' . esc_attr($this->networkAuthId) . '][b2sTiktokPromotionOwnBrand]" data-network-count="-1" data-network-auth-id="' . esc_attr($this->networkAuthId) . '" >';
            $content .= '<label> ' . esc_html__('Your brand', 'blog2social') . '</label>';
            $content .= '<br><label class="b2s-own-promotional-content"> ' . esc_html__("You are promoting yourself or your own business. This video will be classified as Brand Organic.", 'blog2social') . '</label>';              
            $content .= '</div>';

            $content .= '<div class="b2s-margin-bottom-10">';
            $content .= ' <input '.($promotionThirdParty ? "checked" : "").'  type="checkbox" value="'.($promotionThirdParty ? "on" : "off").'"  class="b2s-tiktok-promotion-option" name="b2s[' . esc_attr($this->networkAuthId) . '][promotion_option_branded]" id="b2s[' . esc_attr($this->networkAuthId) . '][b2sTiktokPromotionThirdParty]" data-network-count="-1" data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
            $content .= '<label> ' . esc_html__('Branded Content', 'blog2social') . '</label>';
            $content .= '<br><label class="b2s-both-promotional-content"> ' . esc_html__("You are promoting another brand or a third party. This video will be classified as Branded Content.", 'blog2social') . '</label>';
            $content .= '<br>';
            $content .= '</div>';
            $content .= '</div>';
            $content .= '</div>';

            $content .= '<div class="b2s-tiktok-menu">';
        
            $content  .= '<div class="tiktok-music-confirmation"  data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
            // translators: %s is a link
            $content  .= sprintf(__('By posting, you agree to <a href="%s" target="_blank">TikTok\'s Music Usage Confirmation.</a>', "blog2social"), esc_url(B2S_Tools::getSupportLink('tiktok_music_confirmation')));
            $content  .= '</div>';
            
            $content  .= '<div class="tiktok-music-brand-confirmation" style="display:none;" data-network-auth-id="' . esc_attr($this->networkAuthId) . '">';
            // translators: %1$s, %2$s is a link
            $content .= sprintf(__('By posting, you agree to <a href="%1$s" target="_blank">Tiktok\'s Branded Content Policy</a> and <a href="%2$s" target="_blank">Music Usage Confirmation.</a>', "blog2social"), esc_url(B2S_Tools::getSupportLink('tiktok_branded_confirmation')),esc_url(B2S_Tools::getSupportLink('tiktok_music_confirmation')));
            $content .= '</div>';     

            $content .= '<input type="hidden" class="b2s-tiktok-self-only-disabled-text" value="'.esc_html__("Self only (Branded content videos cannot be set to private)", "blog2social").'">';
            $content  .= '<input type="hidden" class="b2s-tiktok-self-only-text" value="'.esc_html__("Self only", "blog2social").'">';
            $content .= '<input type="hidden" class="b2s-tiktok-no-promotion-selected" value="'.esc_html__("You need to indicate if your TikTok content promotes yourself, a third party, or both.", "blog2social").'">';
            $content  .= '</div>';
        
            $content .= '</div>';
        }

        if(!$preview){
            $content .= '<button class="btn btn-sm btn-primary pull-right b2s-share-settings-save-btn" data-network-auth-id="'.$this->networkAuthId.'" data-network-id="'.$this->networkId.'">speichern</button>';
        }

        $content.='</div>';

        if(!$preview){
            $content.='<br>';
            $content.='<br>';
            $content.='<hr>';            
            
        }
                        
        return $content;
    }

    public function getPrepostDetails() {
        return $this->prepostDetails;
    }

}