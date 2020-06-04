<style>
.sfsi-top-header .sfsi-sad-image,.sfsi-top-header h2{vertical-align:middle;display:inline-block}.banner_support_forum{margin:50px auto;text-align:center;width:80%}.banner_support_forum .banner-1 img{width:100%}.sfsi-deactivation-popup{width:100%;height:100%;display:none;position:fixed;top:0;left:0;background:rgba(0,0,0,.75)}.sfsi-deactivation-popup-content{max-width:540px;width:90%;position:absolute;top:50%;left:50%;-webkit-transform:translate(-50%,-50%);transform:translate(-50%,-50%);box-shadow:0 2px 6px rgba(0,0,0,1);border-radius:3px;background:#fff}.sfsi-deactivation-popup-close{padding:0 5px 3px;display:inline-block;position:absolute;top:25px;right:25px;transition:ease .25s all;-webkit-transform:translate(50%,-50%);transform:translate(50%,-50%);border-radius:100%;background:#FEFEFE;font-size:20px;text-align:center;line-height:100%;color:#acacac;border:1px solid #acacac;text-decoration:none}.sfsi-deactivation-popup-close:active,.sfsi-deactivation-popup-close:focus,.sfsi-deactivation-popup-close:hover{color:#acacac;border:1px solid #acacac}.sfsi-deactivation-popup-close:focus{box-shadow:none!important}.sfsi-top-header{text-align:center;padding:30px 30px 0}.sfsi-top-header h2{font-size:22.92px;margin-right:12px}.sfsi-popup-content{padding:0 30px}.sfsi-text{text-align:center;padding-bottom:14px}.sfsi-text .sfsi-please{font-size:16.92px;font-weight:900;display:inline;text-decoration:none!important;border-bottom:1px solid #000!important}.sfsi-text .sfsi-please-other-text{font-size:14.92px;display:inline}.sfsi-go-to-support{text-align:center}.sfsi-go-to-support .go-to-support-forum{display:inline-block;background:#26B654;padding:14px 35px;font-size:19.34px;color:#fff;text-decoration:none;font-weight:700}.sfsi-info-text{text-align:center;margin-left:auto;width:70%;margin-right:auto;padding-top:10px;padding-bottom:23px}.sfsi-info-text p{font-size:9.31px!important}.sfsi-popup-footer{border-top:2px solid #f2f2f2}.sfsi-popup-footer .sfsi-deactivation-reason-link{text-align:center;padding:28px}.sfsi-popup-footer .sfsi-deactivation-reason-link a{text-decoration:none!important;font-size:12.73px;color:#000;border-bottom:1px Solid #000}.sfsi-radio-button-content{padding:23px 30px}.sfsi-deactivate-radio-text{font-size:16.92px!important;display:inline-block;margin:10px 0;padding-top:5px;cursor:pointer}.sfsi-reason-1-section .sfsi-popup-content .sfsi-text .sfsi-please-other-text img,.sfsi-reason-2-section .sfsi-popup-content .sfsi-text .sfsi-please-other-text img{vertical-align:middle!important;width:16px!important;margin-bottom:3px!important}.sfsi-deactivate-radio-text .sfsi-bold-text{font-weight:700!important}.sfsi-reason-1-section .sfsi-popup-content .sfsi-text,.sfsi-reason-3-section .sfsi-popup-content .sfsi-text{text-align:left;padding-top:10px;padding-bottom:14px;padding-left:23px}.sfsi-reason-1-section .sfsi-popup-content,.sfsi-reason-2-section .sfsi-popup-content,.sfsi-reason-3-section .sfsi-popup-content{padding:0!important}.sfsi-reason-1-section .sfsi-popup-content .sfsi-text .sfsi-please{border-bottom:none!important}.sfsi-premium-version-text{color:#5489D0!important;text-decoration:none;font-weight:700}.sfsi-just-deactivate-it{text-align:center}.sfsi-just-deactivate-it a{text-decoration:none!important;font-size:13.73px;color:#000;border-bottom:1px solid #000}.show{display:block}.hide{display:none}.marginbottom{margin-bottom:15px}

</style>

<div class="sfsi-deactivation-popup" data-popup="popup-1">

    <div class="sfsi-deactivation-popup-content">

        <div class="sfsi-popup-header sfsi-top-header">
            <h2>Oh! You don\'t like our plugin?</h2>
            <div class="sfsi-sad-image">
                <img src="<?php echo SFSI_PLUGURL; ?>images/sad_image.png" alt="error">
            </div>
        </div>

        <div class="sfsi-popup-content">
            <div class="sfsi-text">
                <p class="sfsi-please">PLEASE</p>
                <p class="sfsi-please-other-text">let us know in the Support Forum what was the issue, <br>so that we can fix it. We‘ll respond quickly!</p>
            </div>
            <div class="sfsi-go-to-support">
                <a target="_blank" href="<?php echo SFSI_SUPPORT_FORM; ?>" class="go-to-support-forum">Go to Support Forum></a>
            </div>
            <div class="sfsi-info-text">
                <p>If you're not a Wordpress user yet, please sign up – it's quick! Once logged in you‘ll see a section at the bottom where you can ask your question.</p>
            </div>
        </div>
        <div class="sfsi-popup-footer">
            <div class="sfsi-deactivation-reason-link">
                <a href="javascript:void(0)">No, don't worry, there was a different reason, I want to de-activate it</a>
            </div>
        </div>
        
        <a href="javascript:void(0)" class="sfsi-deactivation-popup-close" data-popup-close="popup-1">&times;</a>

    </div>

</div>

<div class="sfsi-deactivation-popup" data-popup="popup-2">

    <div class="sfsi-deactivation-popup-content">

        <!--        Radio buttons START-->
        <div class="sfsi-radio-button-content">

            <form>
                
                <div class="sfsi-reason-container">

                    <input type="radio" class="sfsi-deactivate-radio" name="reason" value="0">
                    <p class="sfsi-deactivate-radio-text">The plugin <span class="sfsi-bold-text">doesn't work for me</span></p>
                
                    <div class="sfsi-reason-section hide">
                        <div class="sfsi-popup-content">
                            <div class="sfsi-text">
                                <p class="sfsi-please">PLEASE</p>
                                <p class="sfsi-please-other-text">let us know in the Support Forum what didn't work, <br>so that we can fix it. We‘ll respond quickly! <img src="<?php echo SFSI_PLUGURL; ?>images/smile.png" alt="error"></p>
                            </div>
                            <div class="sfsi-go-to-support">
                                <a target="_blank" href="<?php echo SFSI_SUPPORT_FORM; ?>" class="go-to-support-forum">Go to Support Forum ></a>
                            </div>
                            <div class="sfsi-info-text">
                                <p>If you‘re not a Wordpress user yet, please sign up – it‘s quick! Once logged in you‘ll see a section at the bottom where you can ask your question. </p>
                            </div>
                        </div>
                    </div>
                
                </div>

                <div class="sfsi-reason-container">

                    <input type="radio" class="sfsi-deactivate-radio" name="reason" value="1">
                    <p class="sfsi-deactivate-radio-text">I got the <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium" class="sfsi-premium-version-text">premium version</a>!</p>
                
                    <div class="sfsi-reason-section hide">
                        <div class="sfsi-popup-content">
                            <div class="sfsi-text">
                                <p class="sfsi-please-other-text">Thumbs up to that one! <img src="<?php echo SFSI_PLUGURL; ?>images/smile.png" alt="error"></p>
                            </div>
                            <div class="sfsi-go-to-support">
                                <a href="javascript:void(0)" class="sfsi-deactive-plugin go-to-support-forum">De-activate plugin now</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="sfsi-reason-container">

                    <input type="radio" class="sfsi-deactivate-radio" name="reason" value="2">
                    <p class="sfsi-deactivate-radio-text"><span class="sfsi-bold-text">Other</span></p>
                        
                        <div class="sfsi-reason-section hide">
                            <div class="sfsi-popup-content marginbottom">
                                <div class="sfsi-text">
                                    <p class="sfsi-please-other-text">Cool - if it was a reason we colud learn from, please let us know in the Support Forum.</p>
                                </div>
                                <div class="sfsi-go-to-support">
                                    <a target="_blank" href="<?php echo SFSI_SUPPORT_FORM; ?>" class="go-to-support-forum">Go to Support Forum ></a>
                                </div>
                            </div>
                        </div>
                </div>

                <div class="sfsi-reason-container">

                    <div class="sfsi-just-deactivate-it">
                        <a href="javascript:void(0)" class="sfsi-deactive-plugin">Just de-activate it</a>
                    </div>

                </div>

            </form>

        </div>
        <!--  Radio buttons END-->

        <a href="javascript:void(0)" class="sfsi-deactivation-popup-close" data-popup-close="popup-2">&times;</a>

    </div>
</div>