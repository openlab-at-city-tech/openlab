<section id="" class="cm">
    <div class="left box padding">
        <div  class="postbox">
            <h3><span>General Support</span></h3>
            <div class="inside">
                <h4>Submit your Support Request</h4>
                <p>Please click on the button to visit the WordPress.org forum and to submit your support request. </p>
                <p><a href="<?php echo $this->getOption( 'plugin-support-url' ); ?>"  target="_blank" class="buttonblue">Open WordPress.org Support Forum</a>  </p>

                <hr>
                <h4>Share your Appreciation</h4>
                <p>Please consider sharing your experience by leaving a review. It helps us to continue our efforts in promoting this plugin.</p>
                <a target="_blank" href="<?php echo $this->getOption( 'plugin-review-url' ); ?>">
                    <div class="btn button">
                        <div class="dashicons dashicons-share-alt2"></div><span>Submit a review to WordPress.org</span>
                    </div>
                </a>

                <hr>
                <h4>Enable PoweredBy Link</h4>
                <p>Please help us spread a word about our plugin by leaving a discreet powered by link.</p>
                <form action="" method="post">
                    <input type="hidden" name="<?php echo $this->getPoweredByOption(); ?>" value="0"/>
                    <input type="checkbox" name="<?php echo $this->getPoweredByOption(); ?>" value="1" <?php checked( 1, $this->isPoweredByEnabled() ); ?>/>
                    <input type="submit" name="cminds_poweredby_change" value="Change PoweredBy Setting" />
                </form>

            </div>
        </div>

        <div  class="postbox">
            <h3><span>About CreativeMinds</span></h3>
            <div class="inside">
                <p>CreativeMinds offers <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/hire-us/' ); ?>"  target="_blank">Custom WordPress Plugins</a> to suit your specific requirements and make your WordPress website stand out above the rest! Our team of expert developers can add <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/hire-us/' ); ?>"  target="_blank">custom features</a> to modify our existing plugins in a way that best suits your needs, or create a totally unique plugin from scratch! <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/contact/' ); ?>"  target="_blank">Contact us</a> to hear more.</p>
                <hr/>
                <h4>Follow CreativeMinds</h4>
                Twitter: <a href="https://twitter.com/CMPLUGINS" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @CMPLUGINS</a>
                <script>!function ( d, s, id ) {
                        var js, fjs = d.getElementsByTagName( s )[0], p = /^http:/.test( d.location ) ? 'http' : 'https';
                        if ( !d.getElementById( id ) ) {
                            js = d.createElement( s );
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore( js, fjs );
                        }
                    }( document, 'script', 'twitter-wjs' );
                </script>
                <hr>


                <div id="fb-root"></div>
                <script>( function ( d, s, id ) {
                        var js, fjs = d.getElementsByTagName( s )[0];
                        if ( d.getElementById( id ) )
                            return;
                        js = d.createElement( s );
                        js.id = id;
                        js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&autoLogAppEvents=1&version=v2.12&appId=459655384109264';
                        fjs.parentNode.insertBefore( js, fjs );
                    }( document, 'script', 'facebook-jssdk' ) );</script>
                <div class="fb-page" data-href="https://www.facebook.com/cmplugins/" data-tabs="timeline" data-width="500px" data-height="300px" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true"><blockquote cite="https://www.facebook.com/cmplugins/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/cmplugins/">CM Plugins</a></blockquote></div>

                <hr>

                <style>
                    @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i&subset=cyrillic,cyrillic-ext,latin-ext');
                    #mlb2-8021402,
                    #mlb2-8021402 *,
                    #mlb2-8021402 a:hover,
                    #mlb2-8021402 a:visited,
                    #mlb2-8021402 a:focus,
                    #mlb2-8021402 a:active {
                        overflow: visible;
                        position: static;
                        background: none;
                        border: none;
                        bottom: auto;
                        clear: none;
                        cursor: default;
                        float: none;
                        letter-spacing: normal;
                        line-height: normal;
                        text-align: left;
                        text-indent: 0;
                        text-transform: none;
                        visibility: visible;
                        white-space: normal;
                        max-height: none;
                        max-width: none;
                        left: auto;
                        min-height: 0;
                        min-width: 0;
                        right: auto;
                        top: auto;
                        width: auto;
                        z-index: auto;
                        text-shadow: none;
                        box-shadow: none;
                        outline: medium none;
                    }

                    #mlb2-8021402 a:hover {
                        cursor: pointer !important;
                    }

                    #mlb2-8021402 h4 {
                        font-weight: normal;
                    }

                    #mlb2-8021402 .subscribe-form {
                        padding: 20px;
                        width: 500px !important;
                        border: 2px solid #F6F6F6 !important;
                        background: #f6f6f6 none !important;
                        border-radius: 0px !important;
                        box-sizing: border-box !important;
                    }

                    #mlb2-8021402 .ml-block-form {
                        margin-bottom: 0px;
                    }

                    #mlb2-8021402 .subscribe-form .form-section {
                        margin-bottom: 20px;
                        width: 100%;
                    }

                    #mlb2-8021402 .subscribe-form .form-section.mb10 {
                        margin-bottom: 10px;
                        float: left;
                    }

                    #mlb2-8021402 .subscribe-form .form-section.mb0 {
                        margin-bottom: 0px;
                    }

                    #mlb2-8021402 .subscribe-form .form-section h4 {
                        margin: 0px 0px 10px 0px !important;
                        padding: 0px !important;
                        color: #000000 !important;
                        font-family: 'Open Sans', sans-serif !important;
                        font-size: 28px !important;
                        line-height: 100%;
                        text-align: left !important;
                    }

                    #mlb2-8021402 .subscribe-form .form-section p,
                    #mlb2-8021402 .subscribe-form .form-section li {
                        line-height: 150%;
                        padding: 0px !important;
                        margin: 0px 0px 10px 0px;
                        color: #000000 !important;
                        font-family: 'Open Sans', sans-serif !important;
                        font-size: 14px !important;
                    }

                    #mlb2-8021402 .subscribe-form .form-section a {
                        font-size: 14px !important;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .confirmation_checkbox {
                        line-height: 150%;
                        padding: 0px !important;
                        margin: 0px 0px 15px 0px !important;
                        color: #000000 !important;
                        font-family: 'Open Sans', sans-serif !important;
                        font-size: 12px !important;
                        font-weight: normal !important;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .confirmation_checkbox input[type="checkbox"] {
                        margin-right: 5px !important;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .form-group {
                        margin-bottom: 15px;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .form-group label {
                        float: left;
                        margin-bottom: 10px;
                        width: 100%;
                        line-height: 100%;
                        color: #000000 !important;
                        font-family: 'Open Sans', sans-serif !important;
                        font-size: 14px !important;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .checkbox {
                        width: 100%;
                        margin: 0px 0px 10px 0px;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .checkbox label {
                        color: #000000 !important;
                        font-family: 'Open Sans', sans-serif !important;
                        font-size: 14px !important;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .checkbox input {
                        margin: 0px 5px 0px 0px;
                    }

                    #mlb2-8021402 .subscribe-form .form-section .checkbox input[type=checkbox] {
                        -webkit-appearance: checkbox;
                        opacity: 1;
                    }

                    #mlb2-8021402.ml-subscribe-form .form-group .form-control {
                        width: 100%;
                        font-size: 13px;
                        padding: 10px 10px;
                        height: auto;
                        font-family: Arial;
                        border-radius: 0px;
                        border: 1px solid #cccccc !important;
                        color: #000000 !important;
                        background-color: #FFFFFF !important;
                        -webkit-box-sizing: border-box;
                        -moz-box-sizing: border-box;
                        box-sizing: border-box;
                        clear: left;
                    }

                    #mlb2-8021402.ml-subscribe-form button {
                        border: none !important;
                        cursor: pointer !important;
                        width: 100% !important;
                        border-radius: 0px !important;
                        height: 40px !important;
                        background-color: #87b87f !important;
                        color: #FFFFFF !important;
                        font-family: 'Arial', sans-serif !important;
                        font-size: 16px !important;
                        text-align: center !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        position: relative!important;
                    }

                    #mlb2-8021402.ml-subscribe-form button.gradient-on {
                        background: -webkit-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.2) 100%);
                        background: -o-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.2) 100%);
                        background: -moz-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.2) 100%);
                        background: linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.2) 100%);
                    }

                    #mlb2-8021402.ml-subscribe-form button.gradient-on:hover {
                        background: -webkit-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                        background: -o-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                        background: -moz-linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                        background: linear-gradient(top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                    }

                    #mlb2-8021402.ml-subscribe-form button[disabled] {
                        cursor: not-allowed!important;
                    }

                    #mlb2-8021402.ml-subscribe-form .form-section.ml-error label {
                        color: red!important;
                    }

                    #mlb2-8021402.ml-subscribe-form .form-group.ml-error label {
                        color: red!important;
                    }

                    #mlb2-8021402.ml-subscribe-form .form-group.ml-error .form-control {
                        border-color: red!important;
                    }

                    @media (max-width: 768px) {
                        #mlb2-8021402 {
                            width: 100% !important;
                        }
                        #mlb2-8021402 form.ml-block-form,
                        #mlb2-8021402.ml-subscribe-form .subscribe-form {
                            width: 100% !important;
                        }
                    }
                </style>
                <div id="mlb2-8021402" class="ml-subscribe-form ml-subscribe-form-8021402">
                    <div class="ml-vertical-align-center">
                        <div class="subscribe-form ml-block-success" style="display:none">
                            <div class="form-section">
                                <h4>CreativeMinds Newsletter</h4>
                                <p>Thank you! You have successfully subscribed to our newsletter.</p>
                            </div>
                        </div>
                        <form class="ml-block-form" action="https://app.mailerlite.com/webforms/submit/x3a9p3" data-id="790074" data-code="x3a9p3" method="POST" target="_blank">
                            <div class="subscribe-form">
                                <div class="form-section mb10">
                                    <h4>CreativeMinds Newsletter</h4>
                                    <p>Sign up for new original content, updates and exclusive offers.</p>
                                </div>
                                <div class="form-section">
                                    <div class="form-group ml-field-email ml-validate-required ml-validate-email">
                                        <input type="email" name="fields[email]" class="form-control" placeholder="Email*" value="" autocomplete="email" x-autocompletetype="email" spellcheck="false" autocapitalize="off" autocorrect="off">
                                    </div>
                                </div>
                                <input type="hidden" name="ml-submit" value="1" />
                                <button type="submit" class="primary">
                                    Subscribe
                                </button>
                                <button disabled="disabled" style="display: none;" type="button" class="loading">
                                    <img src="https://static.mailerlite.com/images/rolling@2x.gif" width="20" height="20" style="width: 20px; height: 20px;">
                                </button>
                            </div>
                        </form>
                        <script>
                            function ml_webform_success_8021402() {
                                var $ = ml_jQuery || jQuery;

                                $( '.ml-subscribe-form-8021402 .ml-block-success' ).show();
                                $( '.ml-subscribe-form-8021402 .ml-block-form' ).hide();
                            }
                            ;
                        </script>
                    </div>
                </div>
                <script type="text/javascript" src="https://static.mailerlite.com/js/w/webforms.min.js?v3772b61f1ec61c541c401d4eadfdd02f"></script>

                <hr />
                <h4><span>Join CM Affiliate Program</span></h4>
                <p>Earn money by referring your site visitor to CreativeMinds plugins store</p>
                <p>
                    <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/referral-program/' ); ?>"  target="_blank" class="buttonblue">Affiliate Program</a>
                </p>


            </div>
        </div>

        <div class="postbox">
            <?php
            $currentPlugin = $this;
            ?>
            <h3><span>Data Management</span></h3>
            <div class="inside">
                <script>
                            jQuery( 'document' ).ready( function () {
                                jQuery( '.postbox' ).on( 'click', '#cminds-remove-registration', function ( e ) {
                                    var formElem = jQuery( '#cminds_deregister_form' );
                                    var formData = new FormData( formElem[0] );
                                    formData.append( 'action', 'cm-submit-deregistration' );

                                    jQuery.ajax( {
                                        type: "POST",
                                        url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        beforeSend: function () {
                                        },
                                        complete: function () {
                                            jQuery( '#remove-registration-message' ).html( 'Registration data has been removed from CreativeMinds servers.' );
                                        }
                                    } );

                                    e.preventDefault();
                                    return false;
                                } );

                            } );
                </script>
                <?php if ( $currentPlugin->isRegistered() ) : ?>
                    <h4>Remove your registration data</h4>
                    <p>If you would like to remove the registration data (your email and site URL) from CreativeMinds servers and erase it you can do that by clicking the button below.</p>
                    <form method="post" action="" id="cminds_deregister_form">
                        <p>
                            <?php
                            wp_nonce_field( 'cminds_register_free', 'cminds_nonce' );
                            echo $currentPlugin->getRegistrationFields();
                            ?>
                            <a href="javascript:void(0);" class="buttonblue" id="cminds-remove-registration">Remove Registration Data</a>
                        </p>
                    </form>
                    <span id="remove-registration-message"></span>
                <?php else: ?>
                    <?php if ( $currentPlugin->wasRegistered() ) : ?>
                        <h4>Data removal confirmation</h4>
                        <p>Your data was removed from our database and our mailing list.</p>
                    <?php else : ?>
                        <h4>Data removal is always possible</h4>
                        <p>If you would ever like to register the plugin and send us your information, the button allowing to remove your data will appear here.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div  class="postbox">
            <h3><span>System Information</span></h3>
            <div class="inside">
                <?php echo $this->displayServerInformationTab(); ?>
            </div>
        </div>
    </div>

    <div class="right box padding">
        <div id="pages" class="pages postbox">
            <h3>
                <span>Plugin Documentation</span>
                <?php if ( $this->getUserguideUrl() ): ?>
                    <strong class="label-title-link"> <a class="label-title-link-class"  target="_blank" href="<?php echo $this->getUserguideUrl(); ?>">View Plugin User Guide >></a></strong>
                <?php endif; ?>
            </h3>

            <div class="inside">
                <h4>Plugin User Guide</h4>
                <p>For more detailed explanations please visit the plugin <a href="<?php echo $this->addAffiliateCode( $this->getUserguideUrl() ); ?>"  target="_blank">online documentation</a>. We also have a <a href="<?php echo $this->addAffiliateCode( $this->getOption( 'plugin-store-url' ) ); ?>"  target="_blank">detailed product page</a> for this plugin which includes demos and <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/cm-plugins-video-library/' ); ?>"  target="_blank">video tutorials</a>. Please be aware that the user guide is for both the free and the pro editions and some functionality only works in the pro edition of the plugin.</p>
                <hr/>
                <h4>CSS Customizations</h4>
                <p>To easily customize the CSS using live WYSIWYG you can use <a href="https://wordpress.org/plugins/yellow-pencil-visual-theme-customizer/"><strong>Visual Theme Customizer</strong></a> plugin. </p>
                <?php
                $videos = $this->getOption( 'plugin-guide-videos' );
                $height = 280;
                $width  = $height * 1.78125;

                if ( !empty( $videos ) && is_array( $videos ) ) :
                    ?>
                    <?php foreach ( $videos as $key => $video ) : ?>
                        <hr/>
                        <h4>Installation Tutorial</h4>
                        <div class="label-video">
                            <iframe src="https://player.vimeo.com/video/<?php echo $video[ 'video_id' ]; ?>?title=0&byline=0&portrait=0" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <hr/>
                <h4>Upgrading your Plugin to the Pro Editon Tutorial</h4>
                <div class="label-video">
                    <iframe src="https://player.vimeo.com/video/134692135" width="500" height="280" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                </div>
            </div>
        </div>

               <div id="buy" class="buy postbox">
            <h3> <span>Check our NEW Keyword Hound - Best WordPress SEO Tool Ever!</span></h3>
            <div class="plugins">
                <div class="list">
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>SEOHoundIcon.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$79</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>NEW:</strong> Streamline keyword management and boost the SEO of your website with this one-of-a-kind WordPress SEO plugin!.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/seo-keyword-hound-wordpress/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div id="buy" class="buy postbox">
            <h3> <span>Buy CreativeMidns suite of all 99+ Premium WordPress Plugins</span></h3>
            <div class="plugins">
                <div class="list">
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>WPmembership.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$249</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>BEST VALUE:</strong> Get all 99+ CM products for a great discount! Offer includes one year of unlimited updates and expert support.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/cm-wordpress-plugins-yearly-membership/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="buy" class="buy postbox">
            <h3> <span>Selected CreativeMinds Plugins</span></h3>
            <div class="plugins">

                <div class="list">
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="https://wordpress.org/plugins/enhanced-tooltipglossary/" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>tooltip.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>FREE</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Tooltip Glossary</strong> - The best glossary managment tool for WordPress. Free Edition</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-download" href="https://wordpress.org/plugins/enhanced-tooltipglossary/" target="_blank" >Download</a>
                        </div>
                    </div>

                    <!-- CM Tooltip Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/tooltipglossary' ); ?>" target="_blank">
                                <img class="img" width="80" src="<?php echo plugin_dir_url( __FILE__ ); ?>tooltip.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>From $29</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Tooltip Glossary</strong> - The best glossary managment tool for WordPress</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/tooltipglossary' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!-- CM Answers Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/answers' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>answers.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$39</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Answers</strong> - Questions and Answers discussion forum.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/answers' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!-- Download Manager Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/downloadsmanager' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>downloads.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$39</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Download Manager</strong> - The ultimate tool for managing uploads and downloads.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/downloadsmanager' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!--  Pop Up Manager Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/cm-pop-up-banners-plugin-for-wordpress/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>popup.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$29</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Pop Up Manager</strong> - Easily publish your  events and products using PopUp Banners.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/cm-pop-up-banners-plugin-for-wordpress/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!--  Business Directory  Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-business-directory-plugin-for-wordpress/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>businessdir.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$39</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Business Directory</strong> - Supports the management of a business listing.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-business-directory-plugin-for-wordpress/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!--  Video Lessons  Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-video-lessons-manager-plugin-for-wordpress/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>videolessons.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$39</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Video Manager</strong> - Manage video lessons and allow users and admin to track progress.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-video-lessons-manager-plugin-for-wordpress/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>


                    <!--  FAQ  Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/faq-plugin-for-wordpress-by-creativeminds' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>faq.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$29</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM FAQ</strong> - Build powerful frequently answered question (FAQ) knowledge base.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/faq-plugin-for-wordpress-by-creativeminds' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!--  Search and Replace  Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-on-demand-search-and-replace-plugin-for-wordpress/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>searchreplace.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$29</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Search and Replace</strong> - On demand search and replace tool allows you to easily replace texts & html.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/purchase-cm-on-demand-search-and-replace-plugin-for-wordpress/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!--  Cm Map Routes Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/maps-routes-manager-plugin-for-wordpress-by-creativeminds/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>routes.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$39</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Map Route Manager</strong> - Draw map routes and generate a catalog of routes and trails with points of interest using Google maps.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/maps-routes-manager-plugin-for-wordpress-by-creativeminds/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <!--  Cm Booking Calendar Plugin -->
                    <div class="plugins-table">
                        <div class="plugins-img item">
                            <a href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/schedule-appointments-manage-bookings-plugin-wordpress/' ); ?>" target="_blank">
                                <img class="img" src="<?php echo plugin_dir_url( __FILE__ ); ?>appointments_icon.png">
                            </a>
                        </div>

                        <div class="plugins-price item">
                            <span>$39</span>
                        </div>

                        <div class="plugins-body item">
                            <p><strong>CM Booking Calendar</strong> - Customers can easily schedule appointments and pay for them directly through your website.</p>
                        </div>

                        <div class="plugins-action item">
                            <a class="button-success" href="<?php echo $this->addAffiliateCode( 'https://www.cminds.com/wordpress-plugins-library/schedule-appointments-manage-bookings-plugin-wordpress/' ); ?>" target="_blank" >More Info</a>
                        </div>
                    </div>

                    <hr/>

                    <a href="<?php echo $this->getStoreUrl(); ?>"  target="_blank" class="buttonorange">View All CreativeMinds Plugins</a>
                    <a href="<?php echo $this->getStoreUrl( array( 'category' => 'Bundle' ) ); ?>"  target="_blank" class="buttonblue">View Bundles</a>
                    <a href="<?php echo $this->getStoreUrl( array( 'category' => 'Add-On' ) ); ?>"  target="_blank" class="buttonblue">View AddOns</a>
                    <a href="<?php echo $this->getStoreUrl( array( 'category' => 'Service' ) ); ?>" target="_blank" class="buttonblue">View Services</a>
                </div>
            </div>
        </div>
    </div>
</section>