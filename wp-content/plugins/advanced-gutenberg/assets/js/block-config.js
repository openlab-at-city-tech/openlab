jQuery(window).on('load', function () {
    // Loading UI
    jQuery('.block-config-modal-wrapper').show();
    setTimeout(function () {
        jQuery('#advgb-loading-screen').hide();
    }, 1000);
});

jQuery(document).ready(function ($) {
    // Setup minicolors input
    $('.minicolors-input').minicolors();

    // Add block name for top header
    $('.block-config-modal-title').text(parent.window.blockLabel + $('.block-config-modal-title').text());

    $('.block-config-save').unbind('click').click(function () {
        var dataSubmit = {};
        var blockType = $('.block-type-input').val();
        var nonceVal = $('#advgb_block_config_nonce').val();
        dataSubmit[blockType] = {};

        $('.block-config-input').each(function () {
            var settingName = $(this).attr('name');
            var settingValue = $(this).val().trim();

            if ($(this).attr('type') === 'checkbox') {
                if (!this.checked) settingValue = 0;
            }

            if (settingValue !== "") {
                dataSubmit[blockType][settingName] = settingValue;
            }
        });

        $.ajax({
            url: parent.window.ajaxurl,
            type: 'POST',
            data: {
                action: 'advgb_block_config_save',
                nonce: nonceVal,
                blockType: blockType,
                settings: dataSubmit
            },
            beforeSend: function () {
                $('#advgb-loading-screen')
                    .append('<div id="advgb-config-saved-text">Saving... Do not close this window!</div>')
                    .show();
            },
            success: function () {
                $('#advgb-config-saved-text').text('Saved successfully!');
                setTimeout(function () {
                    $('#advgb-loading-screen').hide();
                    $('#advgb-config-saved-text').remove();
                }, 2000);
            },
            error: function ( jqxhr, textStatus, error ) {
                alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                $('#advgb-loading-screen').hide();
                $('#advgb-config-saved-text').remove();
            }
        });
    });
});