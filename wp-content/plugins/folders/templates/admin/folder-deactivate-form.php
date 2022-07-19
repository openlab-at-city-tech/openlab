<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
    <style>
        .folder-hidden{overflow:hidden}.folder-popup-overlay .folder-internal-message{margin:3px 0 3px 22px;display:none}.folder-reason-input{margin:3px 0 3px 22px;display:none}.folder-reason-input input[type=text]{width:100%;display:block}.folder-popup-overlay{background:rgba(0,0,0,.8);position:fixed;top:0;left:0;height:100%;width:100%;z-index:1000;overflow:auto;visibility:hidden;opacity:0;transition:opacity .3s ease-in-out :}.folder-popup-overlay.folder-active{opacity:1;visibility:visible}.folder-serveypanel{width:600px;background:#fff;margin:65px auto 0}.folder-popup-header{background:#f1f1f1;padding:20px;border-bottom:1px solid #ccc}.folder-popup-header h2{margin:0}.folder-popup-body{padding:10px 20px}.folder-popup-footer{background:#f9f3f3;padding:10px 20px;border-top:1px solid #ccc}.folder-popup-footer:after{content:"";display:table;clear:both}.action-btns{float:right}.folder-anonymous{display:none}.attention,.error-message{color:red;font-weight:600;display:none}.folder-spinner{display:none}.folder-spinner img{margin-top:3px}.folder-hidden-input{padding:10px 0 0;display:none}.folder-hidden-input input[type=text]{padding:0 10px;width:100%;height:26px;line-height:26px}.folder-hidden-input textarea{padding:10px;width:100%;height:100px;margin:0 0 10px 0}span.folder-error-message{color:#d00;font-weight:600}.form-control textarea{width:100%;height:100px;margin-bottom:10px}td.plugin-title {display: table-cell;}
    </style>

    <div class="folder-popup-overlay">
        <div class="folder-serveypanel">
            <form action="#" method="post" id="folder-deactivate-form">
                <div class="folder-popup-header">
                    <h2><?php esc_html_e('Quick feedback about Folders', 'folders'); ?> 🙏</h2>
                </div>
                <div class="folder-popup-body">
                    <h3><?php esc_html_e('Your feedback will help us improve the product, please tell us why did you decide to deactivate Folders :)', 'folders'); ?></h3>
                    <div class="form-control">
                        <input type="email" value="<?php echo get_option( 'admin_email' ) ?>" placeholder="<?php echo _e("Email address", 'folders'); ?>" id="folder-deactivate-email_id">
                    </div>
                    <div class="form-control">
                        <label></label>
                        <textarea placeholder="<?php esc_html_e("Your comment", 'folders'); ?>" id="folder-comment"></textarea>
                    </div>
                    <?php esc_html_e("Having any problem with the Folders plugins?", 'folders'); ?><a class='folder-deactivate-button' href='javascript:;'><?php esc_html_e("Click here", 'folders'); ?></a><?php esc_html_e(" to contact our support now", 'folders'); ?>
                </div>
                <div class="folder-popup-footer">
                    <label class="folder-anonymous"><input type="checkbox"/><?php esc_html_e('Anonymous feedback', 'folders'); ?>
                    </label>
                    <input type="button" class="button button-secondary button-skip folder-popup-skip-feedback" value="Skip &amp; Deactivate">

                    <div class="action-btns">
                    <span class="folder-spinner">
                        <img src="<?php echo esc_url(admin_url('/images/spinner.gif')); ?>" alt="">
                    </span>
                        <input type="submit" class="button button-secondary button-deactivate folder-popup-allow-deactivate" value="Submit &amp; Deactivate" disabled="disabled">
                        <a href="#" class="button button-primary folder-popup-button-close"><?php esc_attr_e('Cancel', 'folders'); ?></a>
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