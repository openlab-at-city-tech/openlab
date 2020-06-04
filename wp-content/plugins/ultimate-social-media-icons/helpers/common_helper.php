<?php
if(!function_exists('sfsi_get_displayed_std_desktop_icons')){

    function sfsi_get_displayed_std_desktop_icons($option1=false){

        $option1 =  false !== $option1 && is_array($option1) ? $option1 : unserialize(get_option('sfsi_section1_options',false));

        $arrDisplay = array();

        if(false !== $option1 && is_array($option1) ){

            foreach ($option1 as $key => $value) {

                if(strpos($key, '_display') !== false){

                    $arrDisplay[$key] = isset($option1[$key]) ? sanitize_text_field($option1[$key]) : '';

                }       
            }
        }
        
        return $arrDisplay;

    }
}

if(!function_exists('sfsi_get_displayed_custom_desktop_icons')){

    function sfsi_get_displayed_custom_desktop_icons($option1=false){
        
        $option1 = false != $option1 && is_array($option1) ? $option1 : unserialize(get_option('sfsi_section1_options',false));

        $arrDisplay = array();

        if(!empty($option1) && is_array($option1) && isset($option1['sfsi_custom_files']) 
            && !empty($option1['sfsi_custom_files']) ) :
            
            $arrdbDisplay = unserialize($option1['sfsi_custom_files']);
            
            if(is_array($arrdbDisplay)):

                $arrDisplay = $arrdbDisplay;

            endif;

        endif;

        return $arrDisplay;
    }

}

if(!function_exists('sfsi_icon_get_icon_image')){
    function sfsi_icon_get_icon_image($icon_name,$iconImgName=false){

        $icon = false;

        $option3 = unserialize(get_option('sfsi_section3_options',false));

        if(isset($option3['sfsi_actvite_theme']) && !empty($option3['sfsi_actvite_theme'])){

            $active_theme = $option3['sfsi_actvite_theme'];

            $icons_baseUrl  = SFSI_PLUGURL."images/icons_theme/".$active_theme."/";
            $visit_iconsUrl = SFSI_PLUGURL."images/visit_icons/";  

            if(isset($icon_name) && !empty($icon_name)):

                if($active_theme == 'custom_support')
                {
                    switch (strtolower($icon_name)) {

                        case 'facebook':
                            $custom_icon_name = "fb";
                            break;

                        case 'pinterest':
                            $custom_icon_name = "pintrest";
                            break;
                        
                        default:
                            $custom_icon_name = $icon_name;
                            break;
                    }

                    $key = $custom_icon_name."_skin"; 

                    $skin = get_option($key,false);

                    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";

                    if($skin)
                    {
                        $skin_url = parse_url($skin);
                        if($skin_url['scheme']==='http' && $scheme==='https'){
                            $icon = str_replace('http','https',$skin);
                        }else if($skin_url['scheme']==='https' && $scheme==='http'){
                            $icon = str_replace('https','http',$skin);
                        }else{
                            $icon = $skin;
                        }
                    }
                    else
                    {
                        $active_theme = 'default';
                        $icons_baseUrl = SFSI_PLUGURL."images/icons_theme/default/";

                        $iconImgName = false != $iconImgName ? $iconImgName: $icon_name; 
                        $icon = $icons_baseUrl.$active_theme."_".$iconImgName.".png";
                    }
                }
                else
                {
                    $iconImgName = false != $iconImgName ? $iconImgName: $icon_name;
                    $icon = $icons_baseUrl.$active_theme."_".$iconImgName.".png";
                }

            endif;      

        }

        return $icon;
    }
}

if(!function_exists('sfsi_icon_generate_other_icon_effect_admin_html')){

    function sfsi_icon_generate_other_icon_effect_admin_html($iconName,$arrActiveDesktopIcons,$customIconIndex=-1,$customIconImgUrl=null,$customIconSrNo=null){ 

        $iconImgVal         = false;
        $activeIconImgUrl   = false;
     
        $classForRevertLink = 'hide';
        $defaultIconImgUrl  = false;

        $displayIconClass   = "hide";

        $arruploadDir   = wp_upload_dir();

        if( isset($iconName) && !empty($iconName)){ 

            if("custom" == $iconName && $customIconIndex >-1){

                if(null !== $customIconImgUrl){

                    $activeIconImgUrl  = $customIconImgUrl;                
                    $defaultIconImgUrl = $customIconImgUrl;

                    // Check if icon is selected under Question 1
                    if(in_array($customIconImgUrl, $arrActiveDesktopIcons)){
                        $displayIconClass = "show";
                    }

                    $iconNameStr = $iconName.$customIconSrNo;

                }

            }

            else{

                $dbKey = "sfsi_".$iconName."_display";

                if(isset($arrActiveDesktopIcons[$dbKey]) && !empty($arrActiveDesktopIcons[$dbKey]) 
                    && "yes" == $arrActiveDesktopIcons[$dbKey]){
                    $displayIconClass = "show";
                }

                $activeIconImgUrl   = sfsi_icon_get_icon_image($iconName); 

                $iconNameStr = $iconName;
            }
            if(false != $iconImgVal && !filter_var($iconImgVal, FILTER_VALIDATE_URL)){

                $iconImgVal = SFSI_UPLOAD_DIR_BASEURL.$iconImgVal;
            } 

            $attrCustomIconSrNo  = null !== $customIconSrNo ? 'data-customiconsrno="'.$customIconSrNo.'"': null;
            $attrCustomIconIndex = -1 != $customIconIndex ? 'data-customiconindex="'.$customIconIndex.'"': null;

            $attrIconName = 'data-iconname="'.$iconName.'"';

            ?>
            <div <?php echo $attrCustomIconIndex;?><?php echo $attrIconName; ?> class="col-md-3 bottommargin20 <?php echo $displayIconClass; ?>">

                <label <?php echo $attrCustomIconSrNo; ?> class="mouseover_other_icon_label"><?php echo ucfirst($iconNameStr); ?> </label>

                <img data-defaultImg="<?php echo $defaultIconImgUrl; ?>" class="mouseover_other_icon_img" src="<?php echo $activeIconImgUrl; ?>" alt="error">

                <input <?php echo $attrCustomIconIndex; ?><?php echo $attrIconName; ?> type="hidden" value="<?php echo $iconImgVal; ?>" name="mouseover_other_icon_<?php echo $iconName; ?>">

                <a <?php echo $attrCustomIconIndex; ?><?php echo $attrIconName; ?> id="btn_mouseover_other_icon_<?php echo $iconName; ?>" class="mouseover_other_icon_change_link mouseover_other_icon" href="javascript:void(0)" >Change</a>

                <a <?php echo $attrCustomIconIndex; ?><?php echo $attrIconName; ?> class="<?php echo $classForRevertLink; ?> mouseover_other_icon_revert_link mouseover_other_icon" href="javascript:void(0)">Revert</a>

            </div>

            <?php 
        
        }

    } 
}