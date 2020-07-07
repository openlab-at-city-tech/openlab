<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
    <style>
        .folder-hidden {
            overflow: hidden;
        }

        .folder-popup-overlay .folder-internal-message {
            margin: 3px 0 3px 22px;
            display: none;
        }

        .folder-reason-input {
            margin: 3px 0 3px 22px;
            display: none;
        }

        .folder-reason-input input[type="text"] {
            width: 100%;
            display: block;
        }

        .folder-popup-overlay {
            background: rgba(0, 0, 0, .8);
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 1000;
            overflow: auto;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in-out :
        }

        .folder-popup-overlay.folder-active {
            opacity: 1;
            visibility: visible;
        }

        .folder-serveypanel {
            width: 600px;
            background: #fff;
            margin: 65px auto 0;
        }

        .folder-popup-header {
            background: #f1f1f1;
            padding: 20px;
            border-bottom: 1px solid #ccc;
        }

        .folder-popup-header h2 {
            margin: 0;
        }

        .folder-popup-body {
            padding: 10px 20px;
        }

        .folder-popup-footer {
            background: #f9f3f3;
            padding: 10px 20px;
            border-top: 1px solid #ccc;
        }

        .folder-popup-footer:after {
            content: "";
            display: table;
            clear: both;
        }

        .action-btns {
            float: right;
        }

        .folder-anonymous {
            display: none;
        }

        .attention, .error-message {
            color: red;
            font-weight: 600;
            display: none;
        }

        .folder-spinner {
            display: none;
        }

        .folder-spinner img {
            margin-top: 3px;
        }

        .folder-hidden-input {
            padding: 10px 0 0;
            display: none;
        }

        .folder-hidden-input input[type='text'] {
            padding: 0 10px;
            width: 100%;
            height: 26px;
            line-height: 26px;
        }

        .folder-hidden-input textarea {
            padding: 10px;
            width: 100%;
            height: 100px;
            margin: 0 0 10px 0;
        }

        span.folder-error-message {
            color: #dd0000;
            font-weight: 600;
        }

        .form-control textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }


        .folder-help-btn {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 1001
        }

        .folder-help-btn a {
            display: block;
            border: 3px solid #FFF;
            width: 50px;
            height: 50px;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
            position: relative
        }

        .folder-help-btn a img {
            width: 100%;
            height: auto;
            display: block;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%
        }

        .folder-help-form {
            position: fixed;
            right: 85px;
            border: 1px solid #e9edf0;
            bottom: 25px;
            background: #fff;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            width: 320px;
            z-index: 1001;
            direction: ltr;
            opacity: 0;
            transition: .4s;
            -webkit-transition: .4s;
            -moz-transition: .4s;
            display: none;
        }

        .folder-help-form.active {
            opacity: 1;
            pointer-events: inherit;
            display: block;
        }

        .folder-help-header {
            background: #f4f4f4;
            border-bottom: solid 1px #e9edf0;
            padding: 5px 20px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px 10px 0 0;
            font-size: 16px;
            text-align: right
        }

        .folder-help-header b {
            float: left
        }

        .folder-help-content {
            margin-bottom: 10px;
            padding: 20px 20px 10px
        }

        .folder-help-form p {
            margin: 0 0 1em
        }

        .folder-form-field {
            margin-bottom: 10px
        }

        .folder-form-field input, .folder-form-field textarea {
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
            padding: 5px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #c5c5c5
        }

        .folder-form-field textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
        }

        .folder-help-button {
            border: none;
            padding: 8px 0;
            width: 100%;
            background: #ff6624;
            color: #fff;
            border-radius: 18px
        }

        .folder-help-form .error-message {
            font-weight: 400;
            font-size: 14px;
            display: block;
        }

        .folder-help-form input.input-error, .folder-help-form textarea.input-error {
            border-color: #dc3232
        }

        .folder-help-btn span.tooltiptext {
            position: absolute;
            background: #000;
            font-size: 12px;
            color: #fff;
            top: -35px;
            width: 140%;
            text-align: center;
            left: -20%;
            border-radius: 5px;
            direction: ltr
        }

        p.error-p, p.success-p {
            margin: 0;
            font-size: 14px;
            text-align: center
        }

        .folder-help-btn span.tooltiptext:after {
            bottom: -20px;
            content: "";
            transform: translateX(-50%);
            height: 10px;
            width: 0px;
            border-width: 10px 5px 0;
            border-style: solid;
            border-color: #000 transparent transparent;
            left: 50%;
            position: absolute
        }
        .folder-help-btn {
            display: none;
        }

        p.success-p {
            color: green
        }

        p.error-p {
            color: #dc3232
        }

        html[dir=rtl] .folder-help-btn {
            left: 20px;
            right: auto
        }

        html[dir=rtl] .folder-help-form {
            left: 85px;
            right: auto
        }
        .folder-popup-body h3 {
            line-height: 24px;
        }
        .folder-popup-overlay .form-control input {
            width: 100%;
            margin: 0 0 15px 0;
        }
    </style>

    <div class="folder-popup-overlay">
        <div class="folder-serveypanel">
            <form action="#" method="post" id="folder-deactivate-form">
                <div class="folder-popup-header">
                    <h2><?php esc_html_e('Quick feedback about Folders', WCP_FOLDER); ?> 🙏</h2>
                </div>
                <div class="folder-popup-body">
                    <h3><?php esc_html_e('Your feedback will help us improve the product, please tell us why did you decide to deactivate Folders :)', WCP_FOLDER); ?></h3>
                    <div class="form-control">
                        <input type="email" value="<?php echo get_option( 'admin_email' ) ?>" placeholder="<?php echo _e("Email address", WCP_FOLDER) ?>" id="folder-deactivate-email_id">
                    </div>
                    <div class="form-control">
                        <label></label>
                        <textarea placeholder="<?php esc_html_e("Your comment", WCP_FOLDER) ?>" id="folder-comment"></textarea>
                    </div>
                    <?php esc_html_e("Having any problem with the Folders plugins?", WCP_FOLDER) ?><a class='folder-deactivate-button' href='javascript:;'><?php esc_html_e("Click here", WCP_FOLDER) ?></a><?php esc_html_e(" to contact our support now", WCP_FOLDER) ?>
                </div>
                <div class="folder-popup-footer">
                    <label class="folder-anonymous"><input type="checkbox"/><?php esc_html_e('Anonymous feedback', WCP_FOLDER); ?>
                    </label>
                    <input type="button" class="button button-secondary button-skip folder-popup-skip-feedback" value="Skip &amp; Deactivate">

                    <div class="action-btns">
                    <span class="folder-spinner">
                        <img src="<?php echo esc_url(admin_url('/images/spinner.gif')); ?>" alt="">
                    </span>
                        <input type="submit" class="button button-secondary button-deactivate folder-popup-allow-deactivate" value="Submit &amp; Deactivate" disabled="disabled">
                        <a href="#" class="button button-primary folder-popup-button-close"><?php esc_attr_e('Cancel', WCP_FOLDER); ?></a>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        (function ($) {

            $(function () {

                $(document).on("click", ".folder-deactivate-button", function(e){
                    e.stopPropagation();
                    jQuery(".folder-popup-button-close").trigger("click");
                    jQuery(".folder-help-btn").toggle();
                    jQuery(".folder-help-form").toggleClass("active");
                    jQuery("#user_email").focus();
                });

                /* Diffrent folder slug for Free/Pro */
                var folderPluginSlug = 'folders';
                // Code to fire when the DOM is ready.

                $(document).on('click', 'tr[data-slug="' + folderPluginSlug + '"] .deactivate', function (e) {
                    e.preventDefault();

                    $('.folder-popup-overlay').addClass('folder-active');
                    $('body').addClass('folder-hidden');
                });
                $(document).on('click', '.folder-popup-button-close', function () {
                    close_popup();
                });
                $(document).on('click', ".folder-serveypanel,tr[data-slug='" + folderPluginSlug + "'] .deactivate", function (e) {
                    e.stopPropagation();
                });

                $(document).click(function () {
                    close_popup();
                });
                $(document).on("keyup", "#folder-comment", function(){
                    if($.trim($(this).val()) == "") {
                        $(".folder-popup-allow-deactivate").attr("disabled", true);
                    } else {
                        $(".folder-popup-allow-deactivate").attr("disabled", false);
                    }
                });
                $(document).on('submit', '#folder-deactivate-form', function (event) {
                    event.preventDefault();

                    var _reason = jQuery('#folder-comment').val();
                    var _email_id = jQuery('#folder-deactivate-email_id').val();

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'folder_plugin_deactivate',
                            reason: _reason,
                            email_id: _email_id,
                            nonce: '<?php echo esc_attr(wp_create_nonce('wcp_folder_deactivate_nonce')) ?>'
                        },
                        beforeSend: function () {
                            $(".folder-spinner").show();
                            $(".folder-popup-allow-deactivate").attr("disabled", "disabled");
                        }
                    }).done(function (res) {
                        $(".folder-spinner").hide();
                        $(".folder-popup-allow-deactivate").removeAttr("disabled");
                        window.location.href = $("tr[data-slug='" + folderPluginSlug + "'] .deactivate a").attr('href');
                    });
                });

                $('.folder-popup-skip-feedback').on('click', function (e) {
                    window.location.href = $("tr[data-slug='" + folderPluginSlug + "'] .deactivate a").attr('href');
                });

                function close_popup() {
                    $('.folder-popup-overlay').removeClass('folder-active');
                    $('#folder-deactivate-form').trigger("reset");
                    $(".folder-popup-allow-deactivate").attr('disabled', 'disabled');
                    $(".folder-reason-input").hide();
                    $('body').removeClass('folder-hidden');
                    $('.message.error-message').hide();
                }
            });

        })(jQuery); // This invokes the function above and allows us to use '$' in place of 'jQuery' in our code.
    </script>
<?php include_once dirname(__FILE__)."/help.php" ?>