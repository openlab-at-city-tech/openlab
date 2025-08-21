<?php if (!defined('ABSPATH')) { exit; }
$email = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == "playground.wordpress.net")?"":get_option('admin_email'); 
$popup_data = FOLDER_UPDATE_POPUP_CONTENT; // get Data from update class,
 
 
?>
<style>
    
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap');

    @font-face {
        font-family: 'Lato';
        src: url('<?php echo  esc_url($popup_data['font_url']); ?>');
    }
 
    #wpwrap {
        background-color: #fff;
        background-position: bottom center;
        background-size: cover;
        background-image: url('<?php echo esc_url($popup_data['background_image']);?>');
        position: relative;
    } 
    #wpcontent{
        background-color: transparent !important;
    }
    #wpbody-content {
        padding-bottom: 100px; 
    } 
    #wpbody {
        height: 100%;
        position: unset;
    } 
    .premio-update-popup-wrap { 
        display: flex;
        height: 100%;
        width: 100%;
        align-items: center;
        justify-content: center;  
        box-sizing: border-box;
    }
    .premio-update-popup-wrap * { 
        box-sizing: border-box;
    }

    .premio-update-popup-content {
        margin-top: 100px; 
        width: 626px;
        box-shadow: 0px 15.511px 46.534px 0px rgba(0, 0, 0, 0.16);
        border-radius: 16px;
        padding: 36px;
        background-color: #FFF;
        position: relative;
        z-index: 999;
        overflow: hidden;  
        display: flex;
        flex-wrap: wrap;
        gap: 48px;
        margin-left: 20px;
        margin-right: 20px;
    }

    .premio-update-heading {
        color: #092030;
        font-family: "Poppins", sans-serif;
        font-size: 24px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        letter-spacing: 0.24px;
        margin: 0;
    }

    .premio-update-user-trust {
        display: flex;
        padding: 6px 12px;
        align-items: center;
        gap: 10px;
        border-radius: 100px;
        background: #FFEDF6;
        width: fit-content;
    }
    .premio-update-user-trust img {
        height: 24px;
    }

    .premio-update-user-trust span {
        color: #092030;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .premio-update-popup-content h1 {
        color: #092030;
        font-family: Lato, sans-serif;
        font-size: 32px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
    }

    .premio-content-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .premio-content-list .premio-content-list-items {
        display: flex;
        gap: 8px;
        align-items: center;
        color: #092030;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 150%;
    }
 
    .premio-content-list .premio-content-list-items img {
        height: 20px;
        width: 20px;
        
        display: inherit;
    }
   
    /* // X-Small devices (portrait phones, less than 576px) */


    .premio-update-froms-input {
        margin-top: 24px;
        position: relative;
        height: 45px;
    }

    .premio-update-froms-input input {
        height: 100%;
        padding: 8px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #C6D7E3;
        padding-left: 40px;
        font-size: 13px;
        height: 45px;
    }

    .premio-update-froms-input input:hover,
    .premio-update-froms-input input:focus {
        border-color: rgba(206, 19, 109, 0.60);
        outline: none;
        box-shadow: none;
    }

    .premio-update-froms-input .mail-icon {
        position: absolute;
        left: 10px;
        top: 11px;
        z-index: 1;
    }
    .premio-update-froms-input .mail-icon img { 
        height: 24px;
        width: 24px;
    }

    .eac-input-wrap {
        width: 100%; 
    }

    .update-popup-btn {
        padding: 12px 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 8px;
        background: #CE136D;
        color: #fff;
        border: none;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }
   
    .update-popup-btn:hover, .update-popup-btn:disabled {
        background-color: #A81059;
        color: #fff !important;
    }
    .close-popup {
        background-color: #EAEFF2;
        color: #49687E;
    }
    .close-popup:hover, .close-popup:disabled {
        background-color: #D3DDE2;
        color: #49687E !important;
    }

    .update-popup-btn .icon {
        padding-top: 4px;
        display: inline-block;
    }

  

    .premio-update-popup-footer {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .premio-update-popup-icon-box {
        display: flex;
        align-items: self-start;
        gap: 8px;
        width: 33%;
        position: relative;
    }

    .premio-update-popup-icon-box .content h4 {
        color:  #49687E;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
        margin: 0;
        padding-bottom: 4px;
    }

    .premio-update-popup-icon-box .content span {
        color: #49687E;
        font-family: Lato, sans-serif;
        font-size: 12px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .premio-update-popup-icon-box .absulate-border {
        position: absolute;
        left: -14px;
        top: 0;
        content: '';
        height: 20px;
        width: 2px;
        background-color: #49687E;
        opacity: 0.16;
    }
 

    .premio-update-popup-bg-shape {
        border-radius: 448.684px;
        background: linear-gradient(180deg, rgba(175, 0, 52, 0.24) 0%, rgba(250, 22, 107, 0.24) 100%);
        filter: blur(150px);
        display: inline-block;
        width: 214.632px;
        height: 448.684px;
        transform: rotate(109.229deg);
        flex-shrink: 0;
        position: absolute;
        right: -110px;
        top: 0;
        z-index: -1;
    }
 
    .premio-update-popup-bottom-shape-one {
        position: absolute;
        bottom: 0;
        right: 5%;
    }

    .premio-update-popup-right-shape-one {
        position: absolute;
        bottom: 30%;
        right: 0;
    }

    .popup-update-btn-wrap {
        display: flex;
        gap: 24px;
        margin-top: 24px;
    }

   

    #suggestion {
        margin: 0;
        padding: 0;
        font-size: 14px;
        color: #970029;
    }

    #suggestion i {
        color: #2596be;
        font-weight: bold;
        cursor: pointer;
    }

    .eac-sugg {
        color: #c1c1c1;
        margin-left: 20px;
    }

    #pre-loader{
        height: 17px;
        width: 16px;
    }
    /* responsive */
    @media (max-width: 575.98px) { 
        #wpcontent { 
            padding-left: 0 !important;
        }
        .premio-update-user-trust span {
            font-size: 12px;
        }
        .premio-update-popup-content h1 {
            font-size: 28px;
        }
        .premio-content-list .premio-content-list-items { 
            align-items: start; 
        }
        .premio-content-list .premio-content-list-items img{

            margin-top: 5px;
        }
        
       
        .premio-update-popup-icon-box .absulate-border {
            display: none;
        }
        .premio-update-popup-footer {
            gap: 12px;
            flex-direction: column;  
            justify-content: center;
        }
        
        .premio-update-popup-icon-box {
           width: auto;
        }
    }
   
    /* responsive */
</style>

<div class="premio-update-popup-wrap" >  
    <img src="<?php echo esc_url($popup_data['shape_bottom']);?>" class="premio-update-popup-bottom-shape-one" alt="shape bottom">
    <img src="<?php echo esc_url($popup_data['shape_bottom_right']); ?>" class="premio-update-popup-right-shape-one" alt="shape right"> 
    <div class="premio-update-popup-content">
    <span class="premio-update-popup-bg-shape"></span>
        <?php if(isset($popup_data['plugin_logo'])): ?>
            <img class="plugin-logo" src="<?php echo esc_url($popup_data['plugin_logo']);?>" alt="user trust">
        <?php else: ?>
        <h3 class="premio-update-heading"><?php echo esc_html($popup_data['plugin_name']) ?></h3>
        <?php endif; ?>
       <div class="remio-update-popup-content">
            <div class="premio-update-user-trust">
                <img src="<?php echo esc_url($popup_data['trust_user_img']);?>" alt="user trust">
   
                <span><?php echo esc_html($popup_data['trust_user']) ?><span>
            </div>

            <h1><?php esc_html_e("We Only Email When It's Worth It", 'folders') ?></h1>

            <div class="premio-content-list">
                <div class="premio-content-list-items"> 
                    <img src="<?php echo esc_url($popup_data['check_circle']);?>" alt="check circle">
                    <span><?php esc_html_e('Plugin updates and features for improved performance', 'folders') ?> </span>
                </div>
                <div class="premio-content-list-items">
                    <img src="<?php echo esc_url($popup_data['check_circle']);?>" alt="check circle">
                    <span><?php esc_html_e('Important security updates for site safety', 'folders') ?></span>
                     
                </div>
                <div class="premio-content-list-items">
                    <img src="<?php echo esc_url($popup_data['check_circle']);?>" alt="check circle">
                    <span><?php esc_html_e('Limited-time offers for WordPress users seeking growth', 'folders') ?> </span>
                </div>
            </div>

            <div class="premio-update-froms-input">
                <span class="mail-icon">
                    <img src="<?php echo esc_url($popup_data['mail_icon']) ?>" alt="Mail icon">
                </span>
                <input type="email" value="<?php echo esc_attr($email) ?>" name="folder_update_email" id="folder_update_email" autocomplete="off" placeholder="<?php esc_html_e('Your Email Address', 'folders') ?>" required>
                
            </div>
            <p id="suggestion"></p>
            <div class="popup-update-btn-wrap">
                <button href="javascript:;" class="update-popup-btn submit-popup yes"> <?php esc_html_e('Count Me In', 'folders')?>
                    <span class="icon">
                        <img class="arrow-right" src="<?php echo esc_url($popup_data['arrow_right']) ?>" alt="arrow right">
                        <img id="pre-loader" style="display: none;" src="<?php echo esc_url($popup_data['pre_loader']) ?>" alt="pre loader">
               
                    </span>
                     
                </button>
                <button href="javascript:;" class="update-popup-btn close-popup no"> <?php esc_html_e('Maybe Later', 'folders')?> </button>
            </div>
          
       </div>
        

       <div class="premio-update-popup-footer">
            <!-- Icon Box -->
            <div class="premio-update-popup-icon-box">
                <div class="icon">
                    <img src="<?php echo esc_url($popup_data['user_icon']) ?>" alt="user icon">
                </div>
                <div class="content">
                    <h4><?php echo esc_html($popup_data['website_owners']) ?></h4>
                    <span><?php esc_html_e('Website Owners', 'folders') ?></span>
                </div>
            </div>
            <!-- Icon Box -->
            <!-- Icon Box -->
            <div class="premio-update-popup-icon-box">
                
                <span class="absulate-border"></span>
                <div class="icon">
                    <img src="<?php echo esc_url($popup_data['slash_icon']) ?>" alt="slash icon">
                </div>
                <div class="content">
                    <h4><?php esc_html_e('No Spam Ever', 'folders') ?></h4>
                    <span><?php esc_html_e('Unsubscribe Anytime', 'folders') ?></span>
                </div>
            </div>
            <!-- Icon Box -->
            <!-- Icon Box -->
            <div class="premio-update-popup-icon-box">
                
                <span class="absulate-border"></span>
                <div class="icon">
                    <img src="<?php echo esc_url($popup_data['star_icon']) ?>" alt="star icon">
                </div>
                <div class="content">
                    <h4><?php echo esc_html($popup_data['rating']) ?></h4>
                    <span><?php echo esc_html($popup_data['review']) ?></span>
                </div>
            </div>
            <!-- Icon Box -->
       </div>
       <input type="hidden" id="folder_update_nonce" value="<?php echo wp_create_nonce("folder_update_nonce") ?>">

    </div>
</div>

<script>
	jQuery(document).ready(function($) {
		var mailcheck_flg = false;
		$(document).on("click", ".update-popup-btn", function () {
			//$('#folder_update_email').trigger( 'blur' );
            // after clicking button button should be disable
            $('.update-popup-btn').attr("disabled", true); 
            
            $('#suggestion').html('');
            var email = $('#folder_update_email').val(); 
            var preLoader = $('#pre-loader');
            var btnIcon = $('.submit-popup .icon .arrow-right');
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 

            var updateStatus = 0;
			if ($(this).hasClass("yes")) {
				updateStatus = 1;
			}
            
       
			msemailcheck();
  
            // Check email address is empty or not its email
            if(email == '' && updateStatus == 1){
                $('#suggestion').html('Please enter your email address.');
                // empty this after 5 sec
                setTimeout(function(){ $('#suggestion').html(''); }, 5000); 
                $('.update-popup-btn').attr("disabled", false); 
                return;
            }else if (!emailRegex.test(email) && updateStatus == 1) {
                $('#suggestion').html('Please enter a valid email address.');
                // empty this after 5 sec
                setTimeout(function(){ $('#suggestion').html(''); }, 5000); 
                $('.update-popup-btn').attr("disabled", false); 
                return;
            }
     
            btnIcon.hide();     // hide arrow icon
            preLoader.show();   // show loader

            if ( $('#suggestion').html() != '' && mailcheck_flg == false ) {
				mailcheck_flg = true;
                preLoader.hide();
                btnIcon.show(); 
                $('.update-popup-btn').attr("disabled", false); 
				return false;
			}  
				 
			$.ajax({
				url: ajaxurl,
				type: 'post',
                data: {
                    action: "folder_update_status",
                    status: updateStatus,
                    email: email,
                    nonce: jQuery("#folder_update_nonce").val()
                },
                type: 'post',
				cache: false,
				success: function () {
					mailcheck_flg = false;
                    preLoader.hide();
					window.location.reload();
				}
			})
		});
		
		var checkdomains = ["yahoo.com" ,"hotmail.com" ,"gmail.com" ,"me.com" ,"aol.com" ,"mac.com" ,"live.com" ,"comcast.net" ,"googlemail.com" ,"msn.com" ,"hotmail.co.uk" ,"facebook.com" ,"verizon.net" ,"sbcglobal.net" ,"att.net" ,"gmx.com" ,"outlook.com" ,"icloud.com" ,"protonmail.com"];
        var topLevelDomains = ["com", "net", "org", "me", "io"];        
        jQuery(document).on('blur','#folder_update_email', function(event) {            
            msemailcheck();
        });
		
		function msemailcheck() {
        
			jQuery('#folder_update_email').mailcheck({
                domains: checkdomains,                 // Optional array like ['gmail.com', 'yahoo.com']
                topLevelDomains: topLevelDomains,     // Optional array like ['com', 'net', 'org']
                suggested: function(element, suggestion) {
                   
                    jQuery('#suggestion').html("Did you mean <b><i>" + suggestion.full + "</i></b>?");
                },
                empty: function(element) { 
                    jQuery('#suggestion').html('');
                }
            });
		}

        $("#folder_update_email").emailautocomplete({            
			domains: ["yahoo.com" ,"hotmail.com" ,"gmail.com" ,"me.com" ,"aol.com" ,"mac.com" ,"live.com" ,"comcast.net" ,"googlemail.com" ,"msn.com" ,"hotmail.co.uk" ,"facebook.com" ,"verizon.net" ,"sbcglobal.net" ,"att.net" ,"gmx.com" ,"outlook.com" ,"icloud.com" ,"protonmail.com"]
        });

        $(document).on("click", "#suggestion i", function (){
            $("#folder_update_email").val($(this).text());
			setTimeout(function(){ jQuery('#suggestion').html(''); }, 1000);
        });
	});
</script>
<?php ?>





