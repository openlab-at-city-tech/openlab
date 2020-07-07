<?php

function sfsi_social_media_metabox( $post ) { ?>
    <style>
    .sfsi_new_prmium_follw p {
        width: 90%;
        color: #1a1d20 !important;
        font-size: 17px !important;
        font-family: helveticaregular !important;
    }    
    .sfsi_new_prmium_follw {
        width: 97%;        
        margin-top: 8px;
        display: inline-block;
        background: #f3faf6;
        border: 1px solid #12a252;
        padding: 0px 25px 0px 15px;
        height: 63px;        
        clear: both;
        position: relative;
    }
    .sfsi_new_prmium_sharing p a {
        color: #12a252 !important;
        border-bottom: 1px solid #12a252;
        text-decoration: none;
    }
    .sfsi_new_prmium_follw p b {
        font-weight: bold;
        color: #1a1d20 !important;
    }
    .sfsi_hidenotice{
        cursor: pointer;
        float: right;
        position: absolute;
        right: 10px;
        top: 21px;
        color: grey;
        font-size: 13px;        
    }
    .sfsi-post-tooltip {
        /* display: block!important; */
        position: fixed;
        /*top: 100px;*/
        /*left: 100px;*/
        background: white;
        min-width: 100px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        z-index: 99999;
    }
    .sfsi-port-meta-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }     
    </style>

    <script type="text/javascript">

        // window.addEventListener('sfsi_functions_loaded',function(){ //cause this is not fired in post page and only fired in our plugin page
        jQuery(document).ready(()=>{
            jQuery('.sfsi_hidenotice').on('click',function(){
                var data = {
                    action:"update_sharing_settings",
                    sfsi_custom_social_hide:"yes",
                    nonce: '<?php echo wp_create_nonce('update_sharing_settings') ?>'
                };
                jQuery.post(ajaxurl, data, function(response) {
                    if(response){
                        alert('Settings updated');
                        jQuery('#sfsi-social-media').remove();                        
                    }
                });                                
            });
            const sfsi_post_meta_wrapper = jQuery(".sfsi-post-meta-wrapper");
            const sfsi_post_meta_tooltip = jQuery(".sfsi-post-tooltip");
            sfsi_post_meta_wrapper.on('mouseenter',(mouseEvent)=>{
                var sfsi_post_meta_wrapper_cur = jQuery(mouseEvent.target).parents(".sfsi-post-meta-wrapper");
                console.log(sfsi_post_meta_wrapper_cur[0]);
                var boundary_dimentions = sfsi_post_meta_wrapper_cur[0].getBoundingClientRect();
                console.log(boundary_dimentions,boundary_dimentions.width,boundary_dimentions.left);
                if (mouseEvent)
                  {
                    //FireFox
                    ypos = mouseEvent.screenY;
                  }
                  else
                  {
                    //IE
                    ypos = window.event.screenY;
                  }
                // console.log(xpos,ypos);
                sfsi_post_meta_tooltip.css("top",ypos-120)
                sfsi_post_meta_tooltip.show();
                jQuery(".sfsi-port-meta-backdrop").show();
                sfsi_post_meta_tooltip.css(
                    "left",
                    (
                        boundary_dimentions.left+
                        (
                            boundary_dimentions.width/2
                        )-
                        (
                            sfsi_post_meta_tooltip.width()/2
                        )
                    )
                );
            });
            sfsi_post_meta_wrapper.on('mouseleave',()=>{
                console.log("hidden");
                jQuery(".sfsi-port-meta-backdrop").hide();
                sfsi_post_meta_tooltip.hide();
            });
            // createPopper(sfsi_post_meta_wrapper, sfsi_post_meta_tooltip, {
            //   // options
            // });
        });
    </script>
<div class="sfsi-post-meta-wrapper">
    <div class="social_data_container_first"  >

        <!--********************************** Image for Social Networks (Facebook, LinkedIn & Twitter) STARTS ***********************************************-->

        <div class="sfsi_custom_social_data_container">

            <div class="imgTopTxt"><?php _e('<strong>Picture </strong>(For social media sharing)',SFSI_DOMAIN); ?>
            </div>

            <div class="imgContainer imgpicker">

                <img src="<?php echo esc_url(SFSI_PLUGURL."images/no-image.jpg"); ?>" />
            </div>
            <div class="imgUploadBtn"><input readonly disable type="button" class="button sfsi-post-meta-btn"
                    value="Add Picture" /></div>
        </div>

        <!--********************************** Image for Social Networks (Facebook, LinkedIn & Twitter) CLOSES ***********************************************-->


        <div class="sfsi_custom_social_titlePlusDescription">

            <div class="sfsi_titlePlusDescription">

                <!--********************************** TITLE for Social Networks (Facebook, LinkedIn & Twitter) STARTS ***********************************************-->

                <div class="sfsi_custom_social_data_title">

                    <div class="imgTopTxt">
                        <?php _e('<strong>Title </strong>(leave blank to use the post title)',SFSI_DOMAIN); ?></div>

                    <div class="social_title"><textarea readonly class="sfsi_textarea"
                            maxlength="95"></textarea>
                    </div>

                    <div class="social_description">
                        <div style="padding-right: 15px;">
                        <?php _e('This title will be used when shared on Facebook, Linkedin and WhatsApp. Leave it blank to use the post title. [Developers: this is used by the open graph meta tag «og:title»]',SFSI_DOMAIN); ?>
                        </div>
                    </div>

                    <div class="remaining_char_box" class="sfsi-remaining_char_title">
                        <?php _e('<span id="sfsi_title_remaining_char">95</span> Characters Remaining',SFSI_DOMAIN);?>
                    </div>
                </div>

                <!--********************************** TITLE for Social Networks (Facebook, LinkedIn & Twitter) CLOSES ***********************************************-->

                <!--********************************** DESCRIPTION for Social Networks (Facebook , LinkedIn & Twitter) STARTS ***********************************************-->

                <div class="sfsi_custom_social_data_description">

                    <div class="imgTopTxt">
                        <?php _e('<strong>Description </strong>(leave blank to use the post exerpt)',SFSI_DOMAIN); ?>
                    </div>

                    <div class="social_description_container"><textarea  readonly 
                            class="sfsi_textarea"
                            maxlength="297"></textarea>
                    </div>
                    <div style="display: flex;justify-content: space-between;">
                        <div class="social_description_hint">
                            <?php _e('This description will be used when shared on Facebook, Linkedin, Twitter and WhatsApp (if you use ‘Twitter cards’). Leave it blank to use the post excerpt. [Developers: this is used by the open graph meta tag «og:description»]',SFSI_DOMAIN); ?>
                        </div>

                        <div class="remaining_char_box">
                            <?php _e('<span id="sfsi_desc_remaining_char">297</span> Characters Remaining',SFSI_DOMAIN);?>
                        </div>
                    </div>
                    <?php //sfsi_social_image_issues_support_link(); ?>

                </div>

                <!--********************************** DESCRIPTION for Social Networks (Facebook, LinkedIn & Twitter) CLOSES ***********************************************-->

            </div>
        </div>
    </div>
    <div class="social_data_container_second">

        <!--********************************** Image for PINTEREST STARTS ***********************************************-->

        <div class="sfsi_custom_social_data_container">

            <div class="imgTopTxt"><?php _e('<strong>Picture </strong>(For social media sharing)',SFSI_DOMAIN); ?></div>

            <div class="imgContainer imgpicker">

                <img src="<?php echo esc_url(SFSI_PLUGURL."images/no-image.jpg"); ?>" />

                <?php
                    
                    $uploadBtnTitle = 'Add Picture';
                    ?>

            </div>

            <div class="imgUploadBtn"><input  readonly disable type="button" disable class="button sfsi-post-meta-btn"
                    value="<?php _e($uploadBtnTitle,SFSI_DOMAIN); ?>" /></div>
        </div>

        <!--********************************** Image for PINTEREST CLOSES ***********************************************-->

        <div class="sfsi_custom_social_titlePlusDescription">

            <div class="sfsi_titlePlusDescription">

                <!--********************************** DESCRIPTION for PINTEREST STARTS ***********************************************-->
                <div class="sfsi_custom_social_data_title">
                    <div class="imgTopTxt">
                        <?php _e('<strong>Pinterest description </strong>(leave blank to use the post title)',SFSI_DOMAIN); ?>
                    </div>

                    <div class="social_title"><textarea readonly class="sfsi_textarea"
                            ></textarea></div>

                    <div class="social_description">
                        <div style="padding-right: 15px;">
                            <?php _e('This description will be used when this post is shared on Pinterest. Leave it blank to use the post title.',SFSI_DOMAIN); ?>
                        </div>
                    </div>
                </div>
                <!--********************************** DESCRIPTION for PINTEREST CLOSES ***********************************************-->

                <!--********************************** TITLE for Twitter STARTS ***********************************************-->

                <div class="sfsi_custom_social_data_description">

                    <div class="imgTopTxt"><?php _e('<strong>Tweet </strong>',SFSI_DOMAIN); ?></div>

                    <div class="social_description_container"><textarea  readonly
                            class="sfsi_textarea"name
                            maxlength="106"></textarea>
                    </div>

                    <div style="display: flex;justify-content: space-between;">
                        <div class="social_description_hint">
                            <?php _e('This will be used as tweet-text (the link which get shared will be automatically the added at the end). If you don’t enter anything here the tweet text will be used which you defined globally under question 6 on the plugin’s settings page. ',SFSI_DOMAIN); ?>
                        </div>

                        <div class="remaining_char_box" id="remaining_twiter_char_description">
                            <?php _e('<span id="sfsi_twitter_desc_remaining_char">106</span> Characters Remaining',SFSI_DOMAIN);?>
                        </div>
                    </div>
                    <?php //sfsi_social_image_issues_support_link(); ?>

                </div>

                <!--********************************** TITLE for Twitter CLOSES ***********************************************-->
            </div>
        </div>
    </div>
    <div class="sfsi-port-meta-backdrop" style='display:none'></div>
    <div style='display:none; background: rgb(221, 221, 221);' class="sfsi-post-tooltip">
    <span style="font-family: helvetica-light;    font-size: 17px;">Available in premium – </span>
        <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_post_or_page&utm_campaign=usm_sharing_texts_and_images_section&utm_medium=banner"  class="font-italic text-success" style="color: #28a745!important;font-family: helvetica-light;font-size: 17px;">click to learn more</a>
    </div>  
</div>

<?php }

 
function sfsi_icons_add_meta_boxes() {
    $screen            = get_current_screen(); 
    $option5           = unserialize(get_option('sfsi_section5_options',false));
    $hideSectionVal    = (isset($option5['sfsi_custom_social_hide'])) ? $option5['sfsi_custom_social_hide']: 'no'; 

    if($hideSectionVal=='no'){
        if(isset($screen->post_type) && ('page'==$screen->post_type || 'post'==$screen->post_type)){
            add_meta_box( 'sfsi-social-media', 'Ultimate Social Media – Define which pictures & texts will get shared', 'sfsi_social_media_metabox', $screen->post_type, 'normal', 'low' );
        }        
    }
}
add_action( 'add_meta_boxes', 'sfsi_icons_add_meta_boxes' );
?>