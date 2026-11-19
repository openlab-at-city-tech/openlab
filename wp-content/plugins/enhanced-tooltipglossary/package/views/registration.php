<style>
    .activate-plugin {
        padding: 32px 20px 48px;
    }

    .activate-plugin--header {
        margin: 0 auto;
        text-align: center;
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        color: #000000;
    }

    .activate-plugin--title {
        display: inline-block;
        font-weight: 600;
        font-size: 40px;
        line-height: 48px;
        margin-bottom: 48px;
        position: relative;
        padding-left: 60px;
    }

  

    .activate-plugin--subtitle {
        font-weight: 500;
        font-size: 26px;
        line-height: 32px;
        margin-bottom: 42px;
    }

    .activate-plugin--container {
        max-width: 900px;
        margin: 0 auto;
    }

    .activate-plugin--hero {
        padding: 68px;
        border-radius: 8px;
        background-color: #64B484;
        background-image: url("<?php echo plugin_dir_url(__FILE__); ?>/img/circle.png");
        background-repeat: no-repeat;
        background-position: 125% 120px;
        margin-bottom: 56px;
    }

    .activate-plugin--benefits > li {
        position: relative;
        color: #FFFFFF;
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        font-size: 25px;
        line-height: 30px;
        padding-left: 32px;
    }

    .activate-plugin--benefits > li::before {
        position: absolute;
        content: "";
        background-image: url("<?php echo plugin_dir_url(__FILE__); ?>/img/Vector.png");
        background-size: 20px 20px;
        background-repeat: no-repeat;
        background-position: 0 0;
        width: 20px;
        height: 20px;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
    }

    .activate-plugin--benefits > li + li {
        margin-top: 32px;
    }

    .activate-plugin--form-wrapper {
        margin-bottom: 0px;
        display: flex;
        justify-content: space-between;
    }

    .activate-plugin--form {
        width: 50%;
    }

    .activate-plugin--form-div {
        display: flex;
        flex-direction: column;
        margin-bottom: 16px;
    }

    .activate-plugin--form-label {
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        font-weight: 500;
        font-size: 20px;
        line-height: 144.9%;
        color: #000000;
        margin-bottom: 20px;
    }

    .activate-plugin--form-input {
        padding: 20px 22px;
        background: #F5F5F5;
        border: 1px solid #D8D8D8;
        border-radius: 6px;
    }

    .activate-plugin--form-note {
        margin-top: 8px;
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        font-size: 13px;
        line-height: 24px;
        color: rgba(0, 0, 0, 0.5);
    }

    .activate-plugin--form-btn {
        width: 100%;
        margin-top: 24px;
        padding: 14px;
        text-align: center;
        background: #64B484;
        border-radius: 6px;
        outline: none;
        border: 2px solid #64B484;
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        font-weight: 600;
        font-size: 22px;
        line-height: 27px;
        color: #FFFFFF;
    }

    .activate-plugin--form-btn:focus {
        border: 2px solid blue;
    }

    .activate-plugin--form-btn:hover {
        cursor: pointer;
        background: #168040;
        border: 2px solid #168040;
    }

    .skip-plugin--form-btn {
        width: 100%;
        margin-top: 24px;
        padding: 14px;
        text-align: center;
        background: #929187;
        border-radius: 6px;
        outline: none;
        border: 2px solid #d0d8d3;
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        font-weight: 600;
        font-size: 22px;
        line-height: 27px;
        color: #FFFFFF;
    }

    .skip-plugin--form-btn:focus {
        border: 2px solid blue;
    }

    .skip-plugin--form-btn:hover {
        cursor: pointer;
        background: #5a594a;
        border: 2px solid #d0d8d3;
    }


    .activate-plugin--areticle-title {
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        font-size: 26px;
        line-height: 31px;
        color: #000000;
        margin-bottom: 32px;
    }

    .activate-plugin--strong {
        font-weight: 700;
    }

    .activate-plugin--permissions-text {
        font-family: "Inter", Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        font-size: 18px;
        line-height: 22px;
        color: #000000;
    }

    .ctivate-plugin--link {
        color: #64B484;
        text-decoration: none;
    }

    .ctivate-plugin--link:hover,
    .ctivate-plugin--link:focus {
        cursor: pointer;
        color: #168040;
    }

    .activate-plugin--permissions-main-text {
        margin-bottom: 24px;
        position: relative;
        padding-left: 40px;
    }

    .activate-plugin--permissions-note-text {
        margin-top: 40px;
    }

    .activate-plugin--permissions-text--act::before,
    .activate-plugin--permissions-text--sec::before {
        position: absolute;
        content: "";
        background-size: 24px 24px;
        background-repeat: no-repeat;
        background-position: 0 0;
        width: 24px;
        height: 24px;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
    }

    .activate-plugin--permissions-text--act::before {
        background-image: url("<?php echo plugin_dir_url(__FILE__); ?>/img/v1.png");
    }

    .activate-plugin--permissions-text--sec::before {
        background-image: url("<?php echo plugin_dir_url(__FILE__); ?>/img/v2.png");
    }

    @media (max-width: 992px) {
        .activate-plugin--title {
            padding-top: 72px;
            padding-left: 0;
        }

       

        .activate-plugin--hero {
            padding: 20px;
        }

        .activate-plugin--form-wrapper {
            display: flex;
            flex-direction: column;
        }

        .activate-plugin--form {
            order: 2;
            width: 100%;
        }

        .activate-plugin--form-img {
            order: 1;
            align-self: center;
            max-width: 300px;
            width: 100%;
        }

        .activate-plugin--title {
            line-height: 1.2;
            font-size: calc(28px + (40 - 28) * ((100vw - 992px) / (992 - 320)));
        }

        .activate-plugin--subtitle {
            line-height: 1.2;
            font-size: calc(22px + (26 - 22) * ((100vw - 992px) / (992 - 320)));
        }

        .activate-plugin--benefits > li {
            line-height: 1.2;
            font-size: calc(18px + (25 - 18) * ((100vw - 992px) / (992 - 320)));
        }

        .activate-plugin--form-label {
            line-height: 1.2;
            font-size: calc(16px + (20 - 16) * ((100vw - 992px) / (992 - 320)));
        }

        .activate-plugin--form-note {
            line-height: 1.2;
            font-size: calc(10px + (13 - 10) * ((100vw - 992px) / (992 - 320)));
        }

        .activate-plugin--form-btn {
            line-height: 1.2;
            font-size: calc(16px + (22 - 16) * ((100vw - 992px) / (992 - 320)));
        }

        .activate-plugin--areticle-title {
            line-height: 1.2;
            font-size: calc(22px + (26 - 22) * ((100vw - 992px) / (992 - 320)));
        }

        .activate-plugin--permissions-text {
            line-height: 1.2;
            font-size: calc(14px + (18 - 14) * ((100vw - 992px) / (992 - 320)));
        }
    }
</style>
<script>
    jQuery('document').ready(function () {

<?php if (!$isSkipped): ?>
            jQuery('#cminds_settings_container').hide();
<?php endif; ?>

        jQuery('#cminds-activation-box').on('click', '#cminds-activate', function (e) {
            jQuery('#cminds-activation-box').hide();
            jQuery('#cminds_settings_container').show();

            var formElem = jQuery('#cminds_register_form');
            var formData = new FormData(formElem[0]);
            formData.append('action', 'cm-submit-registration-email');

            jQuery.ajax({
                type: "POST",
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                },
                complete: function () {
                    // Do not show the dialog box, deactivate the plugin.
                }
            });

            e.preventDefault();
            return false;
        });

        jQuery('#cminds-activation-box').on('click', '#cminds-skip', function (e) {
            jQuery('#cminds-activation').hide();
            jQuery('#cminds-activation-skipped').show();
            jQuery('#cminds_settings_container').show();
            jQuery('#cminds-activation-box').css('margin-bottom', 0);

            jQuery.ajax({
                type: "POST",
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    'action': 'cm-submit-registration-skip',
                    'id': '<?php echo esc_attr($currentPlugin->getOption('plugin-abbrev')); ?>'
                },
                beforeSend: function () {
                },
                complete: function () {
                    // Do not show the dialog box, deactivate the plugin.
                }
            });
            e.preventDefault();
            return false;
        });

        jQuery('#wpcontent').on('click', '#cminds-unskip', function (e) {
            jQuery('#cminds_settings_container').hide();
            jQuery('#cminds-activation').show();
            jQuery('#cminds-activation-skipped').hide();
            e.preventDefault();
            return false;
        });
    });
</script>

<div id="cminds-activation-box" style="margin-bottom: <?php echo $isSkipped ? '0' : '50px'; ?>">

    <div id="cminds-activation" style="display: <?php echo $isSkipped ? 'none' : 'block'; ?>">

        <main class="activate-plugin">
            <div class="activate-plugin--header">
                <h1 class="activate-plugin--title">Hooray! Your Plugin is activated and ready to go!</h1>
                <h2 class="activate-plugin--subtitle">Register your software and get the following benefits:</h2>
            </div>
            <div class="activate-plugin--hero activate-plugin--container">
                <ul class="activate-plugin--benefits">
                    <li>Get ongoing <strong>new feature</strong> updates</li>
                    <li>Get information about new version releases</li>
                    <li>Get ongoing <strong>security</strong> notifications</li>
                    <li>Earn discounts on premium WordPress plugins</li>

                </ul>
            </div>
            <div class="activate-plugin--form-wrapper activate-plugin--container">
                <form action="" class="activate-plugin--form" id="cminds_register_form">
                    <?php
                    wp_nonce_field('cminds_register_free', 'cminds_nonce');
                    echo $currentPlugin->getRegistrationFields();
                    ?>
                    <div class="activate-plugin--form-div">
                        <label class="activate-plugin--form-label activate-plugin--form-label-required">Your email</label>
                        <input class="activate-plugin--form-input" type="email" name="email" placeholder="david@cminds.com" value="<?php echo get_option('admin_email'); ?>">
                        <p class="activate-plugin--form-note">Please NOTE: Use your personal most active email in order to get real time notification from us</p>
                    </div>
                    <div class="activate-plugin--form-div">
                        <label class="activate-plugin--form-label activate-plugin--form-label-required">Your site adress</label>
                        <input class="activate-plugin--form-input" name="URL" type="text" placeholder="cminds.com" value="<?php echo site_url(); ?>">
                    </div>
                    <button class="activate-plugin--form-btn" type="submit" id="cminds-activate">Register Now</button>
                    <button class="skip-plugin--form-btn" tabindex="1" type="submit" id="cminds-skip">Skip</button>
                </form>
                <div class="activate-plugin--form-img">
                    <img src="<?php echo plugin_dir_url(__FILE__); ?>img/assets.png" srcset="" alt="" width="381" height="463">
                </div>
            </div>

            <div class="activate-plugin--permissions activate-plugin--container">
                <h3 class="activate-plugin--areticle-title">What permissions are being granted?</h3>
                <div class="activate-plugin--permissions-main-text activate-plugin--permissions-text--act">
                    <p class="activate-plugin--permissions-text"><strong class="activate-plugin--strong">Receive Notices:</strong> Updates, announcements, and relevant marketing messages. NO SPAM!
                        You can unsubscribe at any time.</p>
                </div>
                <div class="activate-plugin--permissions-main-text">
                    <p class="activate-plugin--permissions-text activate-plugin--permissions-text--sec"><strong class="activate-plugin--strong">Data Management:</strong> Creative Minds is GDPR-compliant. You can always remove your data from our database. Your information will never be shared.</p>
                </div>
                <div class="activate-plugin--permissions-note-text">
                    <p class="activate-plugin--permissions-text "><strong class="activate-plugin--strong">Note:</strong> We take privacy and transparency very seriously. To learn more about what data we collect how we use it visit our <a href="https://www.cminds.com/privacy/" class="ctivate-plugin--link">Privacy Policy</a> and <a href="https://www.cminds.com/cm-pro-plugins-terms-and-conditions/" class="ctivate-plugin--link">Terms o Service.</a></p>
                </div>
            </div>
        </main>

    </div>

    <!--Only visible after skipped-->
    <div id="cminds-activation-skipped" class="notice" style="display: <?php echo $isSkipped ? 'block' : 'none'; ?>">
        <p>
            You're just one step away: <a href="javascript:void(0)" id="cminds-unskip">Complete <?php echo esc_attr($currentPlugin->getOption('plugin-name')); ?> registration</a> and receive additional benefits
        </p>
    </div>
</div>