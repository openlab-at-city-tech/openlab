jQuery.noConflict();

jQuery("#b2s-license-user-select").chosen({
    no_results_text: jQuery('#b2s-no-user-found').val(),
    search_contains: true
});

jQuery('.chosen-search input').attr('placeholder', 'Search for blog user');

jQuery("#b2s-license-user-select").change(function () {
    jQuery('#b2s-license-user').val(jQuery('#b2s-license-user-select').val());
    return false;
});

var current_chosen_search = "";
var current_chosen_search_count = 0;
jQuery('.chosen-search input').on('keyup', function () {
    if (this.value != current_chosen_search) {
        current_chosen_search = this.value;
        if (current_chosen_search.length >= 3) {
            current_chosen_search_count++;
            var temp_count = current_chosen_search_count;
            jQuery('#b2s-license-user-select').empty();
            jQuery.ajax({
                url: ajaxurl,
                type: "GET",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_search_user',
                    'search_user': current_chosen_search,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                        return false;
                    }
                    if (current_chosen_search_count == temp_count) {
                        if (data.result == true) {
                            jQuery('#b2s-license-user-select').empty();
                            if (data.options != "") {
                                var newOptions = jQuery(data.options);
                                jQuery('#b2s-license-user-select').append(newOptions);
                                jQuery('#b2s-license-user-select').trigger("chosen:updated");
                                jQuery('.chosen-search input').val(current_chosen_search);
                                jQuery('#b2s-license-user').val(jQuery("#b2s-license-user-select option:first").val());
                            }
                        }
                    }
                }
            });
        }
    }
    return false;
});
