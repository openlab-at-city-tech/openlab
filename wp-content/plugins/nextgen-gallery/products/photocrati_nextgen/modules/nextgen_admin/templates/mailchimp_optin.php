<?php
/**
 * @var array $i18n
 * @var string $dismiss_url
 */
?>
<style>
    #ngg_mailchimp_optin_parent {

    }
    #ngg_mailchimp_optin_form {

    }
    #ngg_mailchimp_optin_wrapper {

    }
    .ngg_mailchimp_optin_fields_wrapper {
        display: flex;
        flex-direction: row;
    }
    .ngg_mailchimp_optin_fields_wrapper * {
        margin-right: 10px;
    }
    #ngg_mailchimp_optin_response_wrapper {
    }
    #ngg_mailchimp_optin_submit {
        background: #9dbd1b;
        border: none;
        padding: 10px;
        color: #fff;
    }
    #ngg_mailchimp_optin_submit:hover {
        cursor: pointer;
    }
    #ngg_mailchimp_optin_response {
    }
</style>
<div id="ngg_mailchimp_optin_parent">
    <form action="https://imagely.us2.list-manage.com/subscribe/post-json?u=72d9b6ebbed08185011c3d642&id=25fbdfefaf&c=?"
          method="POST"
          id="ngg_mailchimp_optin_form"
          name="mc-embedded-subscribe-form"
          class="">
        <div id="ngg_mailchimp_optin_wrapper">
            <h2>
                <?php print $i18n['headline']; ?>
            </h2>
            <p>
                <?php print $i18n['message']; ?>
            </p>
            <div class="ngg_mailchimp_optin_fields_wrapper">
                <input type="email"
                       value=""
                       name="EMAIL"
                       class=""
                       id=""
                       placeholder="<?php print esc_attr($i18n['email_placeholder']); ?>"
                       required>
                <input type="text"
                       value=""
                       name="FNAME"
                       class=""
                       id=""
                       placeholder="<?php print esc_attr($i18n['name_placeholder']); ?>"
                       required>
                <div style="position: absolute; left: -5000px;"
                     aria-hidden="true">
                    <input type="text"
                           name="b_72d9b6ebbed08185011c3d642_25fbdfefaf"
                           tabindex="-1"
                           value="">
                    <input type="hidden"
                           id=""
                           name="group[7113][72057594037927936]"
                           value="1"
                           checked>
                </div>
                <input type="submit"
                       value="<?php print esc_attr($i18n['submit']); ?>"
                       name="subscribe"
                       id="ngg_mailchimp_optin_submit"
                       class="">
            </div>
            <div id="ngg_mailchimp_optin_response_wrapper" class="hidden">
                <p id="ngg_mailchimp_optin_response"></p>
            </div>
        </div>
    </form>
</div>
<script>
    const form             = document.getElementById('ngg_mailchimp_optin_form');
    const submit           = document.getElementById('ngg_mailchimp_optin_submit');
    const response_wrapper = document.getElementById('ngg_mailchimp_optin_response_wrapper');
    const response_message = document.getElementById('ngg_mailchimp_optin_response');
    const notice           = document.querySelectorAll('[data-notification-name="mailchimp_opt_in"]')[0];

    form.addEventListener('submit', function(event) {

        event.preventDefault();

        response_wrapper.classList.add('hidden');
        submit.disabled = true;

        const handleError = function(error) {
            response_wrapper.classList.remove('hidden');
            response_message.innerHTML = error.msg;
        };

        // Use jQuery.ajax() because fetch() cannot use application/json when making cross domain requests
        jQuery.ajax({
            url: form.action,
            crossDomain: true,
            type: form.method,
            data: jQuery(form).serialize(),
            dataType: 'jsonp',
            contentType: "application/json; charset=utf-8",

            error: function(error) {
                submit.disabled = false;
                handleError('<?php print $i18n['connect_error']; ?>');
            },

            success: function(result) {
                if (result.result === "success") {
                    fetch('<?php print $dismiss_url; ?>', {
                        method: 'post',
                        cache: 'no-cache'
                    }).then(function(result) { return result.json(); }).then(function(data) {
                        if (data.success) {
                            response_wrapper.classList.remove('hidden');
                            response_message.innerHTML = '<?php print $i18n['confirmation']; ?>';
                            setTimeout(function() {
                                response_wrapper.classList.add('hidden');
                                notice.classList.add('hidden');
                            }, 2000);
                        }
                    });
                } else {
                    handleError(result);
                }
            }
        });
    });
</script>
