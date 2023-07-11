jQuery.noConflict();

var activeTab = null;
var timeoutPlus;
var timeoutMinus;

jQuery(window).on("load", function () {
    init();
    if (jQuery('.b2s-network-mandant-select').val() == 0 || jQuery('.b2s-network-mandant-select').val() == -1) {
        jQuery('.b2s-network-mandant-btn-delete').hide();
    } else {
        jQuery('.b2s-network-mandant-btn-delete').show();
    }
    jQuery('.b2s-network-details-container-list').hide();
    jQuery('.b2s-network-details-container-list[data-mandant-id="' + jQuery('.b2s-network-mandant-select').val() + '"]').show();

    jQuery('.b2s-network-item-auth-list[data-network-count="true"]').each(function () {
        jQuery('.b2s-network-auth-count-current[data-network-id="' + jQuery(this).attr("data-network-id") + '"]').text(jQuery(this).children('li').length - 1);
    });
    jQuery('.b2s-network-tab[data-type="isSocial"]').trigger('click');

});

function init() {
    var showMeridian = true;
    if (jQuery('#b2sUserTimeFormat').val() == 0) {
        showMeridian = false;
    }
    jQuery('.b2s-settings-sched-item-input-time').timepicker({
        minuteStep: 15,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current'
    }).on('changeTime.timepicker', function (e) {
        var networkAuthId = jQuery(e.target).attr('data-network-auth-id');
        jQuery('.b2s-settings-sched-item-input-time[data-network-auth-id="' + networkAuthId + '"]').val(e.time.value);
        saveTimeSettings();
    });
}

jQuery('.b2s-network-tab').on('shown.bs.tab', function (event) {
    activeTab = jQuery(event.target).attr('data-type');
    if (activeTab == 'isVideo') {
        jQuery('.isVideoInfo').show();
    } else {
        jQuery('.isVideoInfo').hide();
    }
    showContentByCurrentTab();

});

function showContentByCurrentTab() {

    if (activeTab == 'isVideo') {
        jQuery('.b2s-sched-manager-title').hide();
        jQuery('.b2s-get-settings-sched-time-default').hide();
        jQuery('.b2s-sched-manager-premium-area').hide();
        jQuery('.b2s-edit-template-btn').hide();
        jQuery('.b2s-sched-manager-time-area').hide();
        jQuery('.b2s-sched-manager-day-area').hide();

        //FB Profiles are not supported
        jQuery('.btn-facebook[data-network-type="0"]').hide();
        jQuery('.b2s-network-video-not-supported').show();
        jQuery('.b2s-network-item-auth-list-li[data-network-id="1"][data-network-type="0"]').find('div').find('button').each(function () {
            jQuery(this).addClass('b2s-disabled');
        });

        if (jQuery('#b2sBlogHasUsedVideoAddon').val() == 0 && jQuery('#b2sUserVersion').val() == 0) {
            jQuery('.b2s-network-auth-area').css('opacity', '0.2');
            jQuery('.b2s-post').css('opacity', '0.2');

        }

    } else {
        jQuery('.b2s-sched-manager-title').show();
        jQuery('.b2s-get-settings-sched-time-default').show();
        jQuery('.b2s-sched-manager-premium-area').show();
        jQuery('.b2s-edit-template-btn').show();
        jQuery('.b2s-sched-manager-time-area').show();
        jQuery('.b2s-sched-manager-day-area').show();

        jQuery('.btn-facebook[data-network-type="0"]').show();
        jQuery('.b2s-network-video-not-supported').hide();
        jQuery('.b2s-network-item-auth-list-li[data-network-id="1"][data-network-type="0"]').find('div').find('a').each(function () {
            jQuery(this).removeClass('b2s-disabled');
        });

        jQuery('.b2s-network-auth-area').css('opacity', '');
        jQuery('.b2s-post').css('opacity', '');

    }

    var selectedMandantId = jQuery('.b2s-network-mandant-select option:selected').val();
    jQuery('.b2s-network-details-container-list[data-mandant-id="' + selectedMandantId + '"]').find('li').each(function () {
        var current = jQuery(this);
        if (current.hasClass('isSocial') || current.hasClass('isVideo')) {
            if (!current.hasClass(activeTab)) {
                current.hide();
            } else {
                current.show();
            }
        }
    });
}


jQuery(document).on('mousedown mouseup', '.b2s-sched-manager-item-input-day-btn-plus', function (e) {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var current = parseInt(jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val());
    if (e.type == "mousedown") {
        timeoutPlus = setInterval(function () {
            if (current < 99) {
                jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(current++);
            } else {
                jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(99);
            }
        }, 100);
    } else {
        clearInterval(timeoutPlus);
        saveTimeSettings();
    }
    return false;
});

jQuery(document).on('mousedown mouseup', '.b2s-sched-manager-item-input-day-btn-minus', function (e) {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var current = parseInt(jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val());
    if (e.type == "mousedown") {
        timeoutMinus = setInterval(function () {
            if (current > 0) {
                jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(current--);
            } else {
                jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(0);
            }
        }, 100);
    } else {
        clearInterval(timeoutMinus);
        saveTimeSettings();
    }
    return false;
});


jQuery(document).on('click', '.b2s-sched-manager-item-input-day-btn-minus', function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var current = parseInt(jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val());
    if (current > 0) {
        jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(current - 1);
        saveTimeSettings();
    } else {
        jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(0);
    }
});
jQuery(document).on('click', '.b2s-sched-manager-item-input-day-btn-plus', function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var current = parseInt(jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val());
    if (current < 99) {
        jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(current + 1);
        //TODO is show ALL other same items update
        saveTimeSettings();
    } else {
        jQuery('.b2s-sched-manager-item-input-day[data-network-auth-id="' + networkAuthId + '"]').val(99);
    }

});



jQuery(document).on('change', '.b2s-network-mandant-select', function () {
    jQuery('.b2s-network-auth-info').hide();
    if (jQuery(this).val() == 0 || jQuery(this).val() == -1) {
        jQuery('.b2s-network-mandant-btn-delete').hide();
    } else {
        jQuery('.b2s-network-mandant-btn-delete').show();
    }
    jQuery('.b2s-network-details-container-list').hide();
    jQuery('.b2s-network-details-container-list[data-mandant-id="' + jQuery(this).val() + '"]').show();
    showContentByCurrentTab();

});

jQuery(document).on('click', '.b2s-network-mandant-btn-delete', function () {
    jQuery('.b2s-network-auth-info').hide();
    jQuery('#b2s-network-delete-mandant').modal('show');
});

jQuery(document).on('click', '.b2s-btn-network-delete-mandant-confirm', function () {
    jQuery('.b2s-network-auth-area').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('#b2s-network-delete-mandant').modal('hide');
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_mandant',
            'mandantId': jQuery('.b2s-network-mandant-select').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-network-auth-info').hide();
            jQuery('.b2s-network-auth-area').show();
            jQuery('.b2s-loading-area').hide();
            if (data.result == true && data.mandantId >= 1) {
                jQuery('.b2s-network-details-container-list[data-mandant-id="' + data.mandantId + '"]').remove();
                jQuery(".b2s-network-mandant-select option[value='" + data.mandantId + "']").remove();
                jQuery(".b2s-network-mandant-select option[value='-1']").prop('selected', true);
                jQuery(".b2s-network-details-container-list[data-mandant-id='0']").show();
                jQuery('.b2s-network-remove-success').show();
                jQuery('.b2s-network-mandant-btn-delete').hide();
                showContentByCurrentTab();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-network-remove-fail').show();
            }
        }
    });
    return false;
});

jQuery(document).on('change', '#b2s-modify-board-and-group-network-selected', function () {
    if (jQuery(this).attr('data-network-id') == 8) {
        var name = jQuery(this.options[this.selectedIndex]).closest('optgroup').prop('label');
        jQuery('#b2s-modify-board-and-group-name').val(name);
    }
    return true;
});

jQuery(document).on('click', '#b2s-move-user-auth-to-profile', function () {
    jQuery('.b2s-network-auth-settings-content').hide();
    jQuery('.b2s-move-connection-error').hide();
    jQuery('.b2s-assign-error').hide();
    jQuery('.b2s-loading-area').show();

    var authId = jQuery('#b2sUserAuthId').val();
    var mandantId = jQuery('#b2s-move-connection-select').val();
    var oldMandantId = jQuery('#b2sOldMandantId').val();
    var networkId = jQuery('#b2sNetworkId').val();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_move_user_auth_to_profile',
            'networkAuthId': authId,
            'mandantId': mandantId,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-loading-area').hide();
                jQuery('#b2s-edit-network-auth-settings').modal('hide');
                //change show all entry
                jQuery('.b2s-network-details-container-list[data-mandant-id=-1] .b2s-network-item-auth-list-li[data-network-auth-id=' + authId + ']').attr('data-network-mandant-id', mandantId);
                jQuery('.b2s-network-details-container-list[data-mandant-id=-1] .b2s-network-item-auth-list-li[data-network-auth-id=' + authId + '] .b2s-network-item-team-btn-manage').attr('data-network-mandant-id', mandantId);
                jQuery('.b2s-network-details-container-list[data-mandant-id=-1] .b2s-network-item-auth-list-li[data-network-auth-id=' + authId + '] .b2s-network-mandant-name').html('(' + jQuery('#b2s-move-connection-select option:selected').text() + ')');

                //sort entry in show all
                var allArray = [];
                var first = true;
                jQuery('.b2s-network-details-container-list[data-mandant-id=-1] .b2s-network-item-auth-list[data-network-id=' + networkId + '] li').each(function (index, element) {
                    if (first !== true) {
                        allArray.push(element);
                    } else {
                        first = element;
                    }
                });

                for (i = 0; i < allArray.length; i++) {
                    for (j = 0; j < (allArray.length - 1); j++) {
                        if ((jQuery(allArray[j + 1]).attr('data-network-mandant-id') < jQuery(allArray[j]).attr('data-network-mandant-id')) || (jQuery(allArray[j + 1]).attr('data-network-mandant-id') == jQuery(allArray[j]).attr('data-network-mandant-id') && jQuery(allArray[j + 1]).attr('data-network-type') < jQuery(allArray[j]).attr('data-network-type')) || (jQuery(allArray[j + 1]).attr('data-network-mandant-id') == jQuery(allArray[j]).attr('data-network-mandant-id') && jQuery(allArray[j + 1]).attr('data-network-type') == jQuery(allArray[j]).attr('data-network-type') && jQuery(allArray[j + 1]).attr('data-network-auth-id') < jQuery(allArray[j]).attr('data-network-auth-id'))) {
                            var temp = allArray[j + 1];
                            allArray[j + 1] = allArray[j];
                            allArray[j] = temp;
                        }
                    }
                }
                jQuery('.b2s-network-details-container-list[data-mandant-id=-1] .b2s-network-item-auth-list[data-network-id=' + networkId + ']').html('');
                jQuery('.b2s-network-details-container-list[data-mandant-id=-1] .b2s-network-item-auth-list[data-network-id=' + networkId + ']').append(first);
                for (i = 0; i < allArray.length; i++) {
                    jQuery('.b2s-network-details-container-list[data-mandant-id=-1] .b2s-network-item-auth-list[data-network-id=' + networkId + ']').append(allArray[i]);
                }


                //copy and remove entry in profile
                jQuery('.b2s-network-details-container-list[data-mandant-id=' + oldMandantId + '] .b2s-network-item-auth-list-li[data-network-auth-id=' + authId + ']').attr('data-network-mandant-id', mandantId);
                jQuery('.b2s-network-details-container-list[data-mandant-id=' + oldMandantId + '] .b2s-network-item-auth-list-li[data-network-auth-id=' + authId + '] .b2s-network-item-team-btn-manage').attr('data-network-mandant-id', mandantId);
                var temp_list_entry = jQuery('.b2s-network-details-container-list[data-mandant-id=' + oldMandantId + '] .b2s-network-item-auth-list-li[data-network-auth-id=' + authId + ']');
                jQuery('.b2s-network-details-container-list[data-mandant-id=' + oldMandantId + '] .b2s-network-item-auth-list-li[data-network-auth-id=' + authId + ']').remove();
                jQuery('.b2s-network-details-container-list[data-mandant-id=' + mandantId + '] .b2s-network-item-auth-list[data-network-id=' + networkId + ']').append(temp_list_entry);

                //sort entry in new profile
                var mandantArray = [];
                var mandantFirst = true;
                jQuery('.b2s-network-details-container-list[data-mandant-id=' + mandantId + '] .b2s-network-item-auth-list[data-network-id=' + networkId + '] li').each(function (index, element) {
                    if (mandantFirst !== true) {
                        mandantArray.push(element);
                    } else {
                        mandantFirst = element;
                    }
                });

                for (i = 0; i < mandantArray.length; i++) {
                    for (j = 0; j < (mandantArray.length - 1); j++) {
                        if ((jQuery(mandantArray[j + 1]).attr('data-network-mandant-id') < jQuery(mandantArray[j]).attr('data-network-mandant-id')) || (jQuery(mandantArray[j + 1]).attr('data-network-mandant-id') == jQuery(mandantArray[j]).attr('data-network-mandant-id') && jQuery(mandantArray[j + 1]).attr('data-network-type') < jQuery(mandantArray[j]).attr('data-network-type')) || (jQuery(mandantArray[j + 1]).attr('data-network-mandant-id') == jQuery(mandantArray[j]).attr('data-network-mandant-id') && jQuery(mandantArray[j + 1]).attr('data-network-type') == jQuery(mandantArray[j]).attr('data-network-type') && jQuery(mandantArray[j + 1]).attr('data-network-auth-id') < jQuery(mandantArray[j]).attr('data-network-auth-id'))) {
                            var temp = mandantArray[j + 1];
                            mandantArray[j + 1] = mandantArray[j];
                            mandantArray[j] = temp;
                        }
                    }
                }
                jQuery('.b2s-network-details-container-list[data-mandant-id=' + mandantId + '] .b2s-network-item-auth-list[data-network-id=' + networkId + ']').html('');
                jQuery('.b2s-network-details-container-list[data-mandant-id=' + mandantId + '] .b2s-network-item-auth-list[data-network-id=' + networkId + ']').append(mandantFirst);

                for (i = 0; i < mandantArray.length; i++) {
                    jQuery('.b2s-network-details-container-list[data-mandant-id=' + mandantId + '] .b2s-network-item-auth-list[data-network-id=' + networkId + ']').append(mandantArray[i]);
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-network-auth-settings-content').show();
                jQuery('.b2s-move-connection-error').show();
            }
            return false;
        }
    });
    return false;
});

jQuery(document).on('click', '#b2s-assign-network-user-auth', function () {
    if (jQuery('#b2s-select-assign-user').val() <= 0) {
        return false;
    }
    jQuery('.b2s-network-auth-settings-content').hide();
    jQuery('.b2s-move-connection-error').hide();
    jQuery('.b2s-connection-assign').hide();
    jQuery('.b2s-assignment-area').hide();
    jQuery('.b2s-assign-error').hide();
    jQuery('.b2s-loading-area').show();

    var authId = jQuery('#b2sUserAuthId').val();
    var assignBlogUserId = jQuery('#b2s-select-assign-user').val();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_assign_network_user_auth',
            'networkAuthId': authId,
            'assignBlogUserId': assignBlogUserId,
            'optionBestTimes': jQuery('#b2s-network-assign-option-best-times').is(':checked'),
            'optionPostingTemplate': jQuery('#b2s-network-assign-option-posting-template').is(':checked'),
            'optionUrlParameter': jQuery('#b2s-network-assign-option-url-parameter').is(':checked'),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-assign-error[data-error-reason="default"]').show();
            jQuery('.b2s-network-auth-settings-content').show();
            jQuery('.b2s-connection-assign').show();
            jQuery('.b2s-assignment-area').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-loading-area').hide();
                jQuery('#b2s-approved-user-list').append(data.newListEntry);
                jQuery('#b2s-approved-user-list').show();
                jQuery('#b2s-select-assign-user option[value="' + assignBlogUserId + '"]').remove();
                if (jQuery('#b2s-select-assign-user').html() == '') {
                    jQuery('#b2s-select-assign-user').attr('disabled', true);
                    jQuery('.b2s-network-assign-option').hide();
                    jQuery('#b2s-assign-info').hide();
                    jQuery('#b2s-no-assign-user').show();
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-assign-error[data-error-reason="' + data.error_reason + '"]').show();
            }
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-connection-assign').show();
            jQuery('.b2s-network-auth-settings-content').show();
            jQuery('.b2s-assignment-area').show();
            return false;
        }
    });
    return false;
});

jQuery(document).on('click', '.b2s-modify-board-and-group-network-btn', function () {
    jQuery('.b2s-modify-board-and-group-network-loading-area').show();
    jQuery('.b2s-network-auth-info').hide();
    jQuery('#b2s-modify-board-and-group-network-modal').modal('show');
    jQuery('#b2s-modify-board-and-group-network-modal-title').html(jQuery(this).attr('data-modal-title'));
    jQuery('#b2s-modify-board-and-group-network-auth-id').val(jQuery(this).attr('data-network-auth-id'));
    jQuery('#b2s-modify-board-and-group-network-id').val(jQuery(this).attr('data-network-id'));
    jQuery('#b2s-modify-board-and-group-network-type').val(jQuery(this).attr('data-network-type'));
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2s-modify-board-and-group-network-save-btn').hide();
    jQuery('#b2s-modify-board-and-group-network-no-data').hide();
    jQuery('.b2s-modify-board-and-group-network-data').html("");
    jQuery('.b2s-modify-board-and-group-network-data').show();
    jQuery('#b2s-modify-board-and-group-network-save-success').hide();
    jQuery('#b2s-modify-board-and-group-network-save-error').hide();

    var networkId = jQuery(this).attr('data-network-id');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_network_board_and_group',
            'networkAuthId': jQuery(this).attr('data-network-auth-id'),
            'networkType': jQuery(this).attr('data-network-type'),
            'networkId': networkId,
            'lang': jQuery('#b2sUserLang').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-modify-board-and-group-network-loading-area').hide();
            if (data.result == true) {
                jQuery('.b2s-modify-board-and-group-network-data').html(data.content);
                jQuery('.b2s-modify-board-and-group-network-save-btn').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('#b2s-modify-board-and-group-network-no-data').show();
            }
        }
    });
    return false;

});


jQuery(document).on('click', '.b2s-modify-board-and-group-network-save-btn', function () {

    jQuery('.b2s-modify-board-and-group-network-save-btn').hide();
    jQuery('.b2s-modify-board-and-group-network-data').hide();
    jQuery('.b2s-modify-board-and-group-network-loading-area').show();

    var networkAuthId = jQuery('#b2s-modify-board-and-group-network-auth-id').val();
    var networkType = jQuery('#b2s-modify-board-and-group-network-type').val();
    var networkId = jQuery('#b2s-modify-board-and-group-network-id').val();
    var name = jQuery('#b2s-modify-board-and-group-name').val();

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_network_board_and_group',
            'networkAuthId': networkAuthId,
            'networkType': networkType,
            'networkId': networkId,
            'boardAndGroup': jQuery('#b2s-modify-board-and-group-network-selected').val(),
            'boardAndGroupName': name,
            'lang': jQuery('#b2sUserLang').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-modify-board-and-group-network-loading-area').hide();
            if (data.result == true) {
                jQuery('#b2s-modify-board-and-group-network-save-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('#b2s-modify-board-and-group-network-save-error').show();
            }
        }
    });
    return false;
});


jQuery(document).on('click', '#b2s-delete-network-sched-post', function () {
    if (!jQuery(this).is(":checked")) {
        jQuery('.b2s-btn-network-delete-auth-confirm-btn').prop('disabled', true);
        jQuery('#b2s-delete-network-sched-post').val('0');
    } else {
        jQuery('.b2s-btn-network-delete-auth-confirm-btn').removeAttr('disabled');
        jQuery('#b2s-delete-network-sched-post').val('1');
    }
});

jQuery(document).on('click', '.b2s-network-item-auth-list-btn-delete', function () {
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-network-auth-settings-content').hide();
    jQuery('.b2s-network-auth-info').hide();
    jQuery('#b2s-network-delete-auth').modal('show');
    jQuery('#b2s-delete-network-auth-id').val(jQuery(this).attr('data-network-auth-id'));
    jQuery('#b2s-delete-network-id').val(jQuery(this).attr('data-network-id'));
    jQuery('#b2s-delete-network-type').val(jQuery(this).attr('data-network-type'));
    jQuery('#b2s-delete-assign-network-auth-id').val(jQuery(this).attr('data-assign-network-auth-id'));
    jQuery('#b2s-delete-blog-user-id').val(jQuery(this).attr('data-blog-user-id'));
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('#b2s-delete-all-assign-text').hide();
    jQuery('.b2s-btn-network-delete-auth-show-post-text').hide();
    jQuery('.b2s-btn-network-delete-auth-show-post-btn').hide();
    jQuery('.b2s-btn-network-delete-auth-confirm-text').hide();
    jQuery('.b2s-btn-network-delete-auth-confirm-btn').hide();
    jQuery('.b2s-btn-network-delete-sched-text').hide();
    jQuery('.b2s-btn-network-delete-assign-text').hide();
    jQuery('.b2s-btn-network-delete-assign-sched-text').hide();
    var countSchedId = jQuery(this).attr('data-network-auth-id');
    if (typeof jQuery(this).attr('data-assign-network-auth-id') != 'undefined' && jQuery(this).attr('data-assign-network-auth-id') > 0) {
        countSchedId = jQuery(this).attr('data-assign-network-auth-id');
    }

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_sched_posts_by_user_auth',
            'networkAuthId': countSchedId,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-loading-area').hide();
            if (data.result == true) {
                jQuery('.b2s-btn-network-delete-auth-confirm-btn').removeAttr('disabled');
                if (data.count >= 1) {
                    jQuery('.b2s-btn-network-delete-auth-show-post-text').show();
                    jQuery('.b2s-btn-network-delete-sched-text').show();
                    jQuery('.b2s-btn-network-delete-auth-show-post-btn').show();
                    jQuery('#b2s-btn-network-delete-auth-show-post-count').text(data.count);
                    jQuery('.b2s-btn-network-delete-auth-confirm-btn').prop('disabled', true);
                }
                if (data.assignListCount >= 1) {
                    jQuery('.b2s-btn-network-delete-assign-text').show();
                    jQuery('#b2s-delete-assignment').val('all');
                    jQuery('.b2s-btn-network-delete-auth-confirm-btn').prop('disabled', true);
                    jQuery('.b2s-btn-network-delete-auth-show-post-text').show();
                } else {
                    jQuery('#b2s-delete-assignment').val('');
                }
                if (data.assignCount >= 1) {
                    jQuery('.b2s-btn-network-delete-assign-sched-text').show();
                    jQuery('.b2s-btn-network-delete-auth-confirm-btn').prop('disabled', true);
                    jQuery('.b2s-btn-network-delete-auth-show-post-text').show();
                }
                jQuery('#b2s-delete-assign-list').val(data.assignList);
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
            jQuery('.b2s-btn-network-delete-auth-confirm-text').show();
            jQuery('.b2s-btn-network-delete-auth-confirm-btn').show();
            jQuery('#b2s-delete-network-sched-post').prop('checked', false);
            jQuery('#b2s-delete-network-sched-post').val('0');
        }
    });
    return false;
});

jQuery(document).on('click', '.b2s-btn-network-delete-auth-show-post-btn', function () {
    window.location.href = jQuery('#b2s-redirect-url-sched-post').val() + "&b2sUserAuthId=" + jQuery('#b2s-delete-network-auth-id').val();
    return false;
});

jQuery(document).on('click', '.b2s-btn-network-delete-auth-confirm-btn', function () {
    jQuery('.b2s-network-auth-area').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('#b2s-edit-network-auth-settings').modal('hide');
    jQuery('#b2s-network-delete-auth').modal('hide');
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_auth',
            'networkAuthId': jQuery('#b2s-delete-network-auth-id').val(),
            'networkId': jQuery('#b2s-delete-network-id').val(),
            'networkType': jQuery('#b2s-delete-network-type').val(),
            'deleteSchedPost': jQuery('#b2s-delete-network-sched-post').val(),
            'assignNetworkAuthId': jQuery('#b2s-delete-assign-network-auth-id').val(),
            'blogUserId': jQuery('#b2s-delete-blog-user-id').val(),
            'deleteAssignment': jQuery('#b2s-delete-assignment').val(),
            'assignList': jQuery('#b2s-delete-assign-list').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-network-auth-info').hide();
            jQuery('.b2s-network-auth-area').show();
            jQuery('.b2s-loading-area').hide();
            if (data.result == true && data.networkAuthId >= 1 && data.networkId >= 1) {
                jQuery('.b2s-network-item-auth-list-btn-delete[data-network-auth-id="' + data.networkAuthId + '"]').parent('div').parent('li').remove();
                var networkCount = jQuery('.b2s-network-auth-count-current[data-network-count-trigger="true"][data-network-id="' + data.networkId + '"]').text();
                if (networkCount != "0") {
                    var newCount = parseInt(networkCount) - 1;
                    jQuery('.b2s-network-auth-count-current[data-network-id="' + data.networkId + '"]').text(newCount);
                }
                jQuery('.b2s-network-remove-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-network-remove-fail').show();
            }
        }
    });
    return false;
});

jQuery(document).on('click', '.b2s-network-add-mandant-btn-save', function () {
    if (!jQuery('.b2s-network-add-mandant-input').val()) {
        jQuery('.b2s-network-add-mandant-input').addClass('error');
    } else {
        jQuery('.b2s-network-add-mandant-btn-loading').show();
        jQuery('.b2s-network-add-mandant-btn-save').hide();
        jQuery('.b2s-network-add-mandant-input').removeClass('error');
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_save_user_mandant',
                'mandant': jQuery('.b2s-network-add-mandant-input').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery('.b2s-network-auth-info').hide();
                jQuery('.b2s-network-add-mandant-btn-loading').hide();
                jQuery('.b2s-network-add-mandant-btn-save').show();
                if (data.result == true) {
                    jQuery('.b2s-network-mandant-select optgroup[id="b2s-network-select-more-client"]').append('<option value="' + data.mandantId + '">' + data.mandantName + '</option>');
                    jQuery('.b2s-network-details-container').append(data.content);
                    jQuery('.b2s-network-mandant-select option[value="' + data.mandantId + '"]').prop('selected', true);
                    jQuery('.b2s-network-details-container-list').hide();
                    jQuery('.b2s-network-details-container-list[data-mandant-id="' + data.mandantId + '"]').show();
                    jQuery('.b2s-network-add-mandant-success').show();
                    showContentByCurrentTab();
                } else {
                    jQuery('.b2s-network-add-mandant-error').show();
                }
                jQuery('#b2s-network-add-mandant').modal('hide');

                jQuery('.b2s-network-item-auth-list[data-network-count="true"]').each(function () {
                    jQuery('.b2s-network-auth-count-current[data-network-id="' + jQuery(this).attr("data-network-id") + '"]').text((jQuery(this).children('li').length - 1));
                });

            }
        });
    }
});

jQuery(document).on('change', '.b2s-network-add-mandant-input', function () {
    if (jQuery(this).val() != "") {
        jQuery(this).removeClass('error');
    }
});

window.addEventListener('message', function (e) {
    if (e.origin == jQuery('#b2sServerUrl').val()) {
        var data = JSON.parse(e.data);
        loginSuccess(data.networkId, data.networkType, data.displayName, data.networkAuthId, data.mandandId);
    }
});

function loginSuccess(networkId, networkType, displayName, networkAuthId, mandandId) {
    jQuery('.b2s-network-auth-info').hide();
    jQuery('.b2s-network-auth-success').show();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_network_save_auth_to_settings',
            'mandandId': mandandId,
            'networkAuthId': networkAuthId,
            'networkId': networkId,
            'networkType': networkType,
            'displayName': displayName,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {}
    });

    var typName = JSON.parse(jQuery('#b2sNetworkTypeName').val());
    var typOverrideName = JSON.parse(jQuery('#b2sNetworkTypeNameOverride').val());
    var days = jQuery('#b2sDaysName').val();
    var networkTypeName = typName[networkType];
    if (typeof (typOverrideName[networkId]) != 'undefined') {
        if (typeof (typOverrideName[networkId][networkType]) != 'undefined') {
            networkTypeName = typOverrideName[networkId][networkType];
        }
    }
    //NEW
    if (jQuery('.b2s-network-item-auth-list-li[data-network-auth-id="' + networkAuthId + '"]').length == 0) {
        var html = "<li class='b2s-network-item-auth-list-li b2s-label-success-border-left' data-network-auth-id='" + networkAuthId + "' data-network-mandant-id='" + mandandId + "' data-network-id='" + networkId + "' data-network-type='" + networkType + "'>";
        html += '<div class="pull-left"><span class="b2s-network-item-auth-type">' + networkTypeName + '</span>: ';
        html += '<span class="b2s-network-item-auth-user-name">' + displayName + '</span>';
        if (mandandId >= 0) {
            var mandantName = jQuery(".b2s-network-mandant-select option:selected").text();
            if (mandandId <= 0) {
                mandantName = jQuery(".b2s-network-mandant-select option[value='0']").text();
            }
            html += ' <span class="b2s-network-mandant-name">(' + mandantName + ')</span>';
        }

        var today = new Date();
        if (today.getMinutes() >= 30) {
            today.setHours(today.getHours() + 1);
            today.setMinutes(0);
        } else {
            today.setMinutes(30);
        }
        var time = formatAMPM(today);
        if (jQuery('#b2sUserLang').val() == 'de') {
            time = padDate(today.getHours()) + ':' + padDate(today.getMinutes());
        }

        html += '</div>';
        html += '<div class="pull-right">';
        html += '<a class="b2s-network-item-auth-list-btn-delete b2s-add-padding-network-delete pull-right" data-network-type="' + networkType + '" data-network-id="' + networkId + '" data-network-auth-id="' + networkAuthId + '" href="#"><span class="glyphicon  glyphicon-trash glyphicon-grey"></span></a>';

        if (jQuery('#b2sUserVersion').val() == '0') {
            html += '<span class="b2s-sched-manager-premium-area pull-right hidden-xs"  style="width: 240px;"><span class="label label-success"><a href="#" class="btn-label-premium b2sInfoSchedTimesModalBtn">SMART</a></span></span>';
        } else {
            html += '<span class="b2s-sched-manager-time-area pull-right b2s-sched-manager-add-padding hidden-xs" style="margin-right:30px !important;">';
            html += '<input class="form-control b2s-box-sched-time-input b2s-settings-sched-item-input-time" type="text" value="' + time + '" readonly data-network-auth-id="' + networkAuthId + '" data-network-mandant-id="' + mandandId + '" data-network-id="' + networkId + '" data-network-type="' + networkType + '" name="b2s-user-sched-data[time][' + networkAuthId + ']">';
            html += '</span>';
            html += '<span class="b2s-sched-manager-day-area pull-right hidden-xs" style=""><span class="b2s-sched-manager-item-input-day-btn-minus" data-network-auth-id="' + networkAuthId + '">-</span> <span class="b2s-text-middle">+</span>';
            html += '<input type="text" class="b2s-sched-manager-item-input-day" data-network-auth-id="' + networkAuthId + '" data-network-mandant-id="' + mandandId + '" data-network-id="' + networkId + '" data-network-type="' + networkType + '" name="b2s-user-sched-data[delay_day][' + networkAuthId + ']" value="0" readonly> <span class="b2s-text-middle">' + days + '</span> <span class="b2s-sched-manager-item-input-day-btn-plus" data-network-auth-id="' + networkAuthId + '">+</span></span>';
        }
        html += '</div>';
        html += '<div class="clearfix"></div>';
        html += '</li>';

        jQuery(html).insertAfter('.b2s-network-item-auth-list-li[data-network-mandant-id="' + mandandId + '"][data-network-id="' + networkId + '"][data-view="all"]:first');
        jQuery(html).insertAfter('.b2s-network-item-auth-list-li[data-network-mandant-id="' + mandandId + '"][data-network-id="' + networkId + '"][data-view="selected"]:first');
        jQuery('.b2s-settings-sched-item-input-time[data-network-auth-id="' + networkAuthId + '"]').val(time);
        var networkCount = jQuery('.b2s-network-auth-count-current[data-network-count-trigger="true"][data-network-id="' + networkId + '"]').text();
        var newCount = parseInt(networkCount) + 1;
        jQuery('.b2s-network-auth-count-current[data-network-id="' + networkId + '"]').text(newCount);
        init();
        if (jQuery('#b2sUserVersion').val() >= 1) {
            saveTimeSettings();
        }

        //Update
    } else {
        jQuery('.b2s-network-auth-update-btn[data-network-auth-id="' + networkAuthId + '"').show();
        if (jQuery('.b2s-network-item-auth-list-li[data-network-auth-id="' + networkAuthId + '"][data-network-mandant-id="' + mandandId + '"][data-network-id="' + networkId + '"][data-network-type="' + networkType + '"]').length > 0) {
            var html = '<span class="b2s-network-item-auth-type">' + networkTypeName + '</span>: ';
            html += '<span class="b2s-network-item-auth-user-name">' + displayName + '</span>';
            if (mandandId >= 0) {
                var mandantName = jQuery(".b2s-network-mandant-select option:selected").text();
                if (mandandId <= 0) {
                    mandantName = jQuery(".b2s-network-mandant-select option[value='0']").text();
                }
                html += ' <span class="b2s-network-mandant-name">(' + mandantName + ')</span>';
            }
            jQuery('.b2s-network-item-auth-list-li[data-network-auth-id="' + networkAuthId + '"][data-network-mandant-id="' + mandandId + '"][data-network-id="' + networkId + '"][data-network-type="' + networkType + '"] div:first').html(html);
            jQuery('.b2s-network-item-auth-list-li[data-network-auth-id="' + networkAuthId + '"][data-network-mandant-id="' + mandandId + '"][data-network-id="' + networkId + '"][data-network-type="' + networkType + '"]').removeClass('b2s-label-danger-border-left').addClass('b2s-label-success-border-left');
        }
    }
    //Update other Auth with same networkId, networkType and displayName (only optional)
    jQuery('.b2s-network-item-auth-list-li[data-network-id="' + networkId + '"][data-network-type="' + networkType + '"]').each(function () {
        if (jQuery(this).find('.b2s-network-item-auth-user-name').html() == displayName) {
            jQuery(this).removeClass('b2s-label-danger-border-left').addClass('b2s-label-success-border-left');
            jQuery(this).find('.b2s-network-auth-list-info[data-b2s-auth-info="isInterrupted"]').hide();
        }
    });
}


jQuery(document).on('click', '.b2s-get-settings-sched-time-default', function () {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_settings_sched_time_default',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery.each(data.times, function (network_id, time) {
                    time.forEach(function (network_type_time, count) {
                        if (network_type_time != "") {
                            jQuery('.b2s-settings-sched-item-input-time[data-network-id="' + network_id + '"][data-network-type="' + count + '"]').val(network_type_time);
                            count++;
                        }
                    });
                });
                saveTimeSettings();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
    return false;
});

function saveTimeSettings() {
    jQuery('.b2s-settings-user-error').hide();
    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery('#b2sSaveTimeSettings').serialize() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            return false;
        },
        success: function (data) {
            if (data.result !== true) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
}

function wop(url, name) {
    jQuery('.b2s-network-auth-info').hide();
    jQuery('.b2s-network-auth-success').hide();
    var location = encodeURI(window.location.protocol + '//' + window.location.hostname);
    window.open(url + '&location=' + location, name, "width=650,height=900,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
}

function showFilter(typ) {
    if (typ == 'show') {
        jQuery('.filterShow').hide();
        jQuery('.form-inline').show();
        jQuery('.filterHide').show();
    } else {
        jQuery('.filterShow').show();
        jQuery('.form-inline').hide();
        jQuery('.filterHide').hide();
    }
}


function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

function padDate(n) {
    return ("0" + n).slice(-2);
}

jQuery(document).on('click', '.b2s-edit-template-btn', function () {
    jQuery('.b2s-edit-template-content').hide();
    jQuery('.b2s-edit-template-save-btn').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('#b2s-edit-template').modal('show');
    jQuery('#b2s-edit-template-network-id').val(jQuery(this).attr('data-network-id'));
    var networkId = jQuery(this).attr('data-network-id');
    jQuery('.b2s-edit-template-network-img').hide();
    jQuery('#b2s-edit-template-network-img-' + networkId).show();

    jQuery.ajax({
        url: ajaxurl,
        type: "GET",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_edit_template',
            'networkId': networkId,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-edit-template-content').html(data.content);
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-edit-template-content').show();
                jQuery('.b2s-edit-template-save-btn').show();
                if (jQuery('#b2sUserVersion').val() < 1 && networkId != 1) {
                    jQuery('.b2s-edit-template-save-btn').addClass('b2s-btn-disabled');
                } else {
                    jQuery('.b2s-edit-template-save-btn').removeClass('b2s-btn-disabled');
                }
                jQuery('.b2s-edit-template-post-content').trigger('keyup');
                if (networkId == 12) {
                    Coloris({
                        el: '.b2s-edit-template-colorpicker',
                        theme: 'polaroid',
                        swatches: [
                            '#ffffff',
                            '#000000',
                            '#ff0000',
                            '#00ff00',
                            '#0000ff',
                            '#ffff00',
                            '#c3073f',
                            '#5cdb95',
                            '#659dbd',
                            '#f9db7a',
                            '#e46de0'
                        ]
                    });
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
});

jQuery(window).on("load", function () {
    if (jQuery('#b2sUserVersion').val() >= 1) {
        jQuery(document).on('click', '.b2s-edit-template-link-post', function () {
            jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
            jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('0');
            jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            if (jQuery('#b2s-edit-template-network-id').val() == 1 || jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 24) {
                jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            }
        });

        jQuery(document).on('click', '.b2s-edit-template-image-post', function () {
            jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
            jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('1');
            jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            if (jQuery('#b2s-edit-template-network-id').val() == 1 || jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 24) {
                jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            }
        });


        document.addEventListener('dragstart', function (event) {
            event.dataTransfer.setData('Text', event.target.innerHTML);
        });

        document.addEventListener('drop', function (event) {
            setTimeout(function () {
                jQuery('.b2s-edit-template-post-content').trigger('keyup');
            }, 0);
        });

        jQuery(document).on('mousedown mouseup keydown keyup', '.b2s-edit-template-post-content', function () {
            var tb = jQuery(this).get(0);
            jQuery('.b2s-edit-template-content-selection-start[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionStart);
            jQuery('.b2s-edit-template-content-selection-end[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionEnd);
        });

        jQuery(document).on('keyup', '.b2s-edit-template-post-content', function () {
            var post = generateExamplePost(jQuery(this).val().replace(/\n/g, "<br>"), jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
            jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
            if (typeof jQuery('#b2s_post_title').val() != 'undefined' && jQuery('#b2s_post_title').val() != '') {
                jQuery('.b2s-edit-template-preview-title[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(jQuery('#b2s_post_title').val());
            }
        });

        jQuery(document).on('change', '.b2s-edit-template-range', function () {
            jQuery('.b2s-edit-template-post-content').trigger('keyup');
        });
        jQuery(document).on('change', '.b2s-edit-template-excerpt-range', function () {
            jQuery('.b2s-edit-template-post-content').trigger('keyup');
        });

        jQuery(document).on('keydown', '.b2s-edit-template-post-content', function () {
            var tb = jQuery(this).get(0);
            var start = tb.selectionStart;
            var end = tb.selectionEnd;
            var reg = new RegExp("({.+?})", "g");
            var amatch = null;
            while ((amatch = reg.exec(jQuery(this).val())) != null) {
                var thisMatchStart = amatch.index;
                var thisMatchEnd = amatch.index + amatch[0].length;
                //case: keydown in pattern
                if (start > thisMatchStart && end <= thisMatchEnd && (event.keyCode == 8 || event.keyCode == 46)) {
                    jQuery(this).val(jQuery(this).val().substr(0, thisMatchStart) + jQuery(this).val().substr(thisMatchEnd));
                    event.preventDefault();
                    return false;

                }
                //case: before pattern
                if (start <= thisMatchStart && end > thisMatchStart && (event.keyCode > 40 || event.keyCode < 16 || event.keyCode == 32)) {
                    event.preventDefault();
                    return false;
                    //case: after pattern
                } else if (start > thisMatchStart && start < thisMatchEnd && (event.keyCode > 40 || event.keyCode < 16 || event.keyCode == 32)) {
                    event.preventDefault();
                    return false;
                }
            }
        });

        jQuery(document).on('click', '.b2s-edit-template-content-post-item', function () {
            var networkType = jQuery(this).attr('data-network-type');
            var text = jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val();
            var start = jQuery('.b2s-edit-template-content-selection-start[data-network-type="' + networkType + '"]').val();
            var end = jQuery('.b2s-edit-template-content-selection-end[data-network-type="' + networkType + '"]').val();

            var reg = new RegExp("({.+?})", "g");
            var amatch = null;
            while ((amatch = reg.exec(text)) != null) {
                var thisMatchStart = amatch.index;
                var thisMatchEnd = amatch.index + amatch[0].length;
                //case: keydown in pattern
                if (start > thisMatchStart && end < thisMatchEnd) {
                    event.preventDefault();
                    return false;
                }
            }
            var newText = text.slice(0, start) + jQuery(this).html() + text.slice(end);
            jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val(newText);
            jQuery('.b2s-edit-template-post-content').focus();
            jQuery('.b2s-edit-template-post-content').trigger('keyup');
            event.preventDefault();
            return false;
        });

        jQuery(document).on('click', '.b2s-edit-template-content-clear-btn', function () {
            var networkType = jQuery(this).attr('data-network-type');
            jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val("");
            jQuery('.b2s-edit-template-post-content').focus();
            jQuery('.b2s-edit-template-post-content').trigger('keyup');
            event.preventDefault();
            return false;
        });


        jQuery(document).on('keyup', '.b2s-edit-template-range', function () {
            if (isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val()) < 1) {
                jQuery(this).val("1");
            }
            if (jQuery(this).attr('max') > 0 && parseInt(jQuery(this).val()) > jQuery(this).attr('max')) {
                jQuery(this).val(jQuery(this).attr('max'));
            }
            event.preventDefault();
            return false;
        });

        jQuery(document).on('keyup', '.b2s-edit-template-excerpt-range', function () {
            if (isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val()) < 1) {
                jQuery(this).val("1");
            }
            if (jQuery(this).attr('max') > 0 && parseInt(jQuery(this).val()) > jQuery(this).attr('max')) {
                jQuery(this).val(jQuery(this).attr('max'));
            }
            event.preventDefault();
            return false;
        });



        jQuery(document).on('click', '.b2s-edit-template-load-default', function () {
            jQuery('.b2s-edit-template-content').hide();
            jQuery('.b2s-edit-template-save-btn').hide();
            jQuery('.b2s-edit-template-save-success').hide();
            jQuery('.b2s-edit-template-save-failed').hide();
            jQuery('.b2s-loading-area').show();
            var networkType = jQuery(this).attr('data-network-type');

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_load_default_post_template',
                    'networkId': jQuery('#b2s-edit-template-network-id').val(),
                    'networkType': networkType,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-loading-area').hide();
                    jQuery('.b2s-edit-template-content').show();
                    jQuery('.b2s-edit-template-save-btn').show();
                    jQuery('.b2s-edit-template-load-default-failed').show();
                    return false;
                },
                success: function (data) {
                    jQuery('.b2s-loading-area').hide();
                    jQuery('.b2s-edit-template-content').show();
                    jQuery('.b2s-edit-template-save-btn').show();
                    if (data.result == true) {
                        jQuery('.b2s-template-tab-' + networkType).html(data.html);
                    } else {
                        if (data.error == 'nonce') {
                            jQuery('.b2s-nonce-check-fail').show();
                        }
                        jQuery('.b2s-edit-template-load-default-failed').show();
                    }
                }
            });
        });
    }
});

jQuery(document).on('click', '.b2s-edit-template-save-btn', function () {
    if (jQuery('#b2sUserVersion').val() < 1 && jQuery('#b2s-edit-template-network-id').val() != 1) {
        return false;
    }

    if (jQuery('#b2s-edit-template-network-id').val() == 12) {
        var matches = jQuery('.b2s-edit-template-post-content').val().match(/#/g);
        if (matches != null && matches.length > 30) {
            jQuery('.b2s-edit-template-post-content').addClass('error');
            jQuery('.b2s-edit-template-hashtag-warning').show();
            return false;
        } else {
            jQuery('.b2s-edit-template-post-content').removeClass('error');
            jQuery('.b2s-edit-template-hashtag-warning').hide();
        }
    }

    jQuery('.b2s-edit-template-content').hide();
    jQuery('.b2s-edit-template-save-btn').hide();
    jQuery('.b2s-edit-template-save-success').hide();
    jQuery('.b2s-edit-template-save-failed').hide();
    jQuery('.b2s-loading-area').show();

    template_data = {};

    jQuery('.b2s-edit-template-post-content').each(function (i, obj) {
        var networkType = jQuery(obj).attr('data-network-type');
        template_data[networkType] = {};
        if (jQuery('.b2s-edit-template-multi-kind[data-network-type="' + networkType + '"]').val() == 1) {
            template_data[networkType]['multi_kind'] = 1;
            template_data[networkType]['type_kind'] = {};
            jQuery('.b2s-edit-template-range[data-network-type="' + networkType + '"]').each(function (index) {
                var type_kind = jQuery(this).data('network-type-kind');
                template_data[networkType]['type_kind'][type_kind] = {};
                template_data[networkType]['type_kind'][type_kind]['range_max'] = jQuery(this).val();
                template_data[networkType]['type_kind'][type_kind]['excerpt_range_max'] = jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + networkType + '"][data-network-type-kind="' + type_kind + '"]').val();
            });
        } else {
            template_data[networkType]['multi_kind'] = 0;
            template_data[networkType]['range_max'] = jQuery('.b2s-edit-template-range[data-network-type="' + networkType + '"]').val();
            template_data[networkType]['excerpt_range_max'] = jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + networkType + '"]').val();
        }

        template_data[networkType]['format'] = jQuery('.b2s-edit-template-post-format[data-network-type="' + networkType + '"]').val();
        template_data[networkType]['content'] = jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val();
        if (jQuery('#b2s-edit-template-network-id').val() == 24 || jQuery('#b2s-edit-template-network-id').val() == 12 || jQuery('#b2s-edit-template-network-id').val() == 1 || jQuery('#b2s-edit-template-network-id').val() == 2) {
            if (jQuery('.b2s-edit-template-enable-link[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['addLink'] = true;
            } else {
                template_data[networkType]['addLink'] = false;
            }
        }
        if (jQuery('#b2s-edit-template-network-id').val() == 12) {
            if (jQuery('.b2s-edit-template-shuffle-hashtags[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['shuffleHashtags'] = true;
            } else {
                template_data[networkType]['shuffleHashtags'] = false;
            }
            template_data[networkType]['frameColor'] = jQuery('#b2s-edit-template-colorpicker').val();
        }
    });

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_post_template',
            'template_data': template_data,
            'networkId': jQuery('#b2s-edit-template-network-id').val(),
            'link_no_cache': (jQuery("#link-no-cache").is(':checked') ? '1' : '0'),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-edit-template-content').show();
            jQuery('.b2s-edit-template-save-btn').show();
            jQuery('.b2s-edit-template-save-failed').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-edit-template-content').show();
            jQuery('.b2s-edit-template-save-btn').show();
            if (data.result == true) {
                jQuery('.b2s-edit-template-save-success').show();
                setTimeout(function () {
                    jQuery('.b2s-edit-template-save-success').fadeOut();
                }, 3000);
                jQuery('#b2s-edit-template').modal('hide');
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-edit-template-save-failed').show();
            }
        }
    });
});


jQuery('#b2sInfoNoCache').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2sInfoFormat').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

jQuery(document).on('click', '.b2sInfoFormatBtn', function () {
    jQuery('#b2sInfoFormat').modal('show');
    var id = jQuery(this).attr('data-network-id');
    jQuery('.b2sInfoFormatText').hide();
    jQuery('.b2sInfoFormatText[data-network-id="' + id + '"]').show();
});

jQuery('#b2sInfoContent').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2sInfoCharacterLimit').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

jQuery(document).on('click', '.b2s-network-add-mandant-btn', function () {
    jQuery('#b2s-network-add-mandant').modal('show');
});
jQuery(document).on('click', '.b2sInfoSchedTimesModalBtn', function () {
    jQuery('#b2sInfoSchedTimesModal').modal('show');
});
jQuery(document).on('click', '.b2sInfoNetwork18Btn', function () {
    jQuery('#b2sInfoNetwork18').modal('show');
});
jQuery(document).on('click', '.b2sInfoNoCacheBtn', function () {
    jQuery('#b2sInfoNoCache').modal('show');
});
jQuery(document).on('click', '.b2sInfoContentBtn', function () {
    jQuery('#b2sInfoContent').modal('show');
});
jQuery(document).on('click', '.b2sInfoCharacterLimitBtn', function () {
    jQuery('#b2sInfoCharacterLimit').modal('show');
});
jQuery(document).on('click', '.b2s-network-addon-info-btn', function () {
    jQuery('#b2sNetworkAddonInfo').modal('show');
});

//START Network Auth Settings
jQuery(document).on('click', '.b2s-network-auth-settings-btn', function () {
    jQuery('#b2s-edit-network-auth-settings').modal('show');
    if (jQuery('#b2sUserVersion').val() >= 3) {

        jQuery('.b2s-network-auth-settings-content').hide();
        jQuery('.b2s-loading-area').show();

        jQuery('.b2s-move-connection-error').hide();
        jQuery('.b2s-connection-assign').hide();
        jQuery('.b2s-assignment-area').hide();
        jQuery('.b2s-connection-owner').hide();
        jQuery('.b2s-assign-error').hide();
        jQuery('#b2s-no-assign-user').hide();
        jQuery('#b2s-assign-info').show();
        jQuery('.b2s-url-parameter-error').hide();
        jQuery('.b2s-url-parameter-content').show();

        jQuery('#b2sUserAuthId').val(jQuery(this).attr('data-network-auth-id'));
        jQuery('#b2sOldMandantId').val(jQuery(this).attr('data-network-mandant-id'));
        jQuery('#b2sNetworkId').val(jQuery(this).attr('data-network-id'));
        jQuery('#b2sNetworkType').val(jQuery(this).attr('data-network-type'));

        //Move connection to network mandant
        if (jQuery('#b2s-move-connection-select').length) {
            jQuery('#b2s-move-connection-select').html(jQuery('.b2s-network-mandant-select').html());
            jQuery("#b2s-move-connection-select option[value='-1']").remove();
            jQuery("#b2s-move-connection-select option[value='" + jQuery(this).attr('data-network-mandant-id') + "']").remove();
            if (jQuery(this).attr('data-network-mandant-id') <= 0) {
                if (jQuery('#b2s-network-select-more-client').length) {
                    var temp_options = jQuery('#b2s-move-connection-select #b2s-network-select-more-client').html();
                    jQuery("#b2s-move-connection-select optiongroup").remove();
                    jQuery("#b2s-move-connection-select").html(temp_options);
                } else {
                    jQuery('#b2s-move-connection-input').hide();
                    jQuery('#b2s-move-connection-error').show();
                }
            } else {
                if (!jQuery('#b2s-move-connection-select #b2s-network-select-more-client option').length) {
                    var temp_options = jQuery("#b2s-move-connection-select option[value='0']");
                    jQuery("#b2s-move-connection-select optiongroup").remove();
                    jQuery("#b2s-move-connection-select").html(temp_options);
                }
            }
        }

        jQuery.ajax({
            url: ajaxurl,
            type: "GET",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_get_network_auth_settings',
                'networkAuthId': jQuery(this).attr('data-network-auth-id'),
                'owner': jQuery(this).attr('data-connection-owner'),
                'networkId': jQuery(this).attr('data-network-id'),
                'networkType': jQuery('#b2sNetworkType').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-loading-area').hide();
                    jQuery('.b2s-network-auth-settings-content').show();
                    var result = JSON.parse(data.data);

                    //URL Parameter
                    if (typeof result.urlParameter != 'undefined' && result.urlParameter.length > 0) {
                        jQuery('.b2s-url-parameter-content').html(result.urlParameter);
                        if (jQuery('.b2s-url-parameter-entry').length <= 0) {
                            jQuery('.b2s-col-name').hide();
                        }
                    } else {
                        jQuery('.b2s-url-parameter-error[data-error-reason="default"]').show();
                        jQuery('.b2s-url-parameter-content').hide();
                    }

                    //User Assign
                    if (typeof result.ownerName != 'undefined') { //Case: network is assigned by other user
                        jQuery('#b2s-connection-owner-name').text(result.ownerName);
                        jQuery('.b2s-connection-owner').show();
                        jQuery('.b2s-assignment-area').show();
                    } else if (typeof result.userSelect != 'undefined' && typeof result.assignList != 'undefined') { //Case: user is network owner
                        jQuery('#b2s-connection-assign-select').html(result.userSelect);
                        jQuery('.b2s-network-assign-list').html(result.assignList);
                        if (jQuery('#b2s-approved-user-list li').length <= 1) {
                            jQuery('#b2s-approved-user-list').hide();
                        } else {
                            jQuery('#b2s-approved-user-list').show();
                        }
                        if (jQuery('#b2s-select-assign-user').html() == '') {
                            jQuery('#b2s-select-assign-user').attr('disabled', true);
                            jQuery('#b2s-assign-network-user-auth').attr('disabled', true);
                            jQuery('.b2s-network-assign-option').hide();
                            jQuery('#b2s-assign-info').hide();
                            jQuery('#b2s-no-assign-user').show();
                        } else {
                            jQuery('#b2s-select-assign-user').removeAttr('disabled');
                            jQuery('#b2s-assign-network-user-auth').removeAttr('disabled');
                            jQuery('.b2s-network-assign-option').show();
                            jQuery('#b2s-no-assign-user').hide();
                            jQuery('#b2s-assign-info').show();
                        }
                        jQuery('.b2s-connection-assign').show();
                        jQuery('.b2s-assignment-area').show();
                        jQuery('.b2s-network-assign-list').show();
                        jQuery('#b2s-connection-assign-select').show();
                    } else {
                        jQuery('.b2s-assign-error[data-error-reason="default"]').show();
                        jQuery('.b2s-connection-assign').show();
                        jQuery('.b2s-assignment-area').show();
                        jQuery('#b2s-assign-network-user-auth').hide();
                        jQuery('#b2s-assign-info').hide();
                        jQuery('.b2s-network-assign-option').hide();
                        jQuery('.b2s-network-assign-list').hide();
                        jQuery('#b2s-connection-assign-select').hide();
                    }

                } else {
                    jQuery('#b2s-edit-network-auth-settings').modal('hide');
                    if (result.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                        return false;
                    }
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                }
            }
        });
    } else {
        jQuery('.b2s-loading-area').hide();
    }
});

//START URL Parameter
jQuery(document).on('click', '.b2s-url-parameter-add-btn', function () {
    jQuery('.b2s-col-name').show();
    var html = '<li class="b2s-url-parameter-entry row">';
    html += '<div class="col-md-5"><input class="form-control b2s-link-parameter-name" value=""></div>';
    html += '<div class="col-md-5"><input class="form-control b2s-link-parameter-value" value=""></div>';
    html += '<div class="col-md-1"><span aria-hidden="true" class="b2s-url-parameter-remove-btn text-danger">&times;</span></div>';
    html += '</li>';
    jQuery('.b2s-url-parameter-list').append(html);
    if (jQuery('.b2s-url-parameter-entry').length >= 10) {
        jQuery('.b2s-url-parameter-add-btn').hide();
    }
});

jQuery(document).on('click', '.b2s-url-parameter-remove-btn', function () {
    jQuery(this).closest('li').remove();
    if (jQuery('.b2s-url-parameter-entry').length <= 0) {
        jQuery('.b2s-col-name').hide();
    }
    jQuery('.b2s-url-parameter-add-btn').show();
});

jQuery(document).on('click', '.b2s-url-parameter-save-btn', function () {
    jQuery('.b2s-network-auth-settings-content').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-url-parameter-error').hide();

    var urlParameter = {};
    jQuery('.b2s-url-parameter-entry').each(function () {
        if (jQuery(this).find('.b2s-link-parameter-name').val().length != 0 && jQuery(this).find('.b2s-link-parameter-value').val().length != 0) {
            urlParameter[jQuery(this).find('.b2s-link-parameter-name').val()] = jQuery(this).find('.b2s-link-parameter-value').val();
        }
    });

    var originNetworkAuthId = jQuery(this).attr('data-network-auth-id');

    var networks = [];
    if (jQuery('.b2s-url-parameter-for-all').is(':checked')) { //get all network connections
        jQuery('.b2s-network-details-container-list[data-mandant-id="-1"] .b2s-network-item-auth-list-li').each(function () {
            if (typeof jQuery(this).data('network-auth-id') != 'undefined') {
                networks.push({
                    'networkAuthId': jQuery(this).data('network-auth-id'),
                    'networkId': jQuery(this).data('network-id'),
                    'networkType': jQuery(this).data('network-type'),
                    'displayName': jQuery(this).find('.b2s-network-item-auth-user-name').text()
                });
            }
        });
    } else if (jQuery('.b2s-url-parameter-for-all-network').is(':checked')) { //get all network connections for specific network
        jQuery('.b2s-network-details-container-list[data-mandant-id="-1"] .b2s-network-item-auth-list-li[data-network-id="' + jQuery(this).data('network-id') + '"]').each(function () {
            if (typeof jQuery(this).data('network-auth-id') != 'undefined') {
                networks.push({
                    'networkAuthId': jQuery(this).data('network-auth-id'),
                    'networkId': jQuery(this).data('network-id'),
                    'networkType': jQuery(this).data('network-type'),
                    'displayName': jQuery(this).find('.b2s-network-item-auth-user-name').text()
                });
            }
        });
    } else { //only current network connection
        networks.push({
            'networkAuthId': originNetworkAuthId,
            'networkId': jQuery(this).data('network-id'),
            'networkType': jQuery('.b2s-network-details-container-list[data-mandant-id="-1"] .b2s-network-item-auth-list-li[data-network-auth-id="' + originNetworkAuthId + '"]').data('network-type'),
            'displayName': jQuery('.b2s-network-details-container-list[data-mandant-id="-1"] .b2s-network-item-auth-list-li[data-network-auth-id="' + originNetworkAuthId + '"]').find('.b2s-network-item-auth-user-name').text()
        });
    }

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_url_parameter',
            'originNetworkAuthId': originNetworkAuthId,
            'networks': networks,
            'networkId': jQuery(this).attr('data-network-id'),
            'urlParameter': JSON.stringify(urlParameter),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-network-auth-settings-content').show();
            if (data.result == true) {
                if (data.html.length > 0) {
                    jQuery('.b2s-url-parameter-content').html(data.html);
                    if (jQuery('.b2s-url-parameter-entry').length <= 0) {
                        jQuery('.b2s-col-name').hide();
                    }
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-url-parameter-error[data-error-reason="save"]').show();
                }
            }
        }
    });
});
//END URL Parameter
//END Network Auth Settings

jQuery(document).on('click', '.b2s-network-add-page-info-btn', function () {
    jQuery('#b2sNetworkAddPageInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddPageInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=page', 'Blog2Social Network');
        return false;
    });
    return false;
});

jQuery(document).on('click', '.b2s-network-add-group-info-btn', function () {
    jQuery('#b2sNetworkAddGroupInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddGroupInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=group', 'Blog2Social Network');
        return false;
    });
    return false;
});

jQuery(document).on('click', '.b2s-network-add-instagram-info-btn', function () {
    jQuery('#b2sNetworkAddInstagramInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddInstagramInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=profile', 'Blog2Social Network');
        return false;
    });
    return false;
});

jQuery(document).on('click', '.b2s-network-add-instagram-business-info-btn', function () {
    jQuery('#b2sNetworkAddInstagramBusinessInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddInstagramBusinessInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=page', 'Blog2Social Network');
        return false;
    });
    return false;
});

function generateExamplePost(template, content_range, exerpt_range) {
    if (jQuery('#b2s_use_post').val() == 'true') {
        var content = '';
        var exerpt = '';
        var title = '';
        var author = '';
        var keywords = '';
        if (typeof jQuery('#b2s_post_content').val() != 'undefined' && jQuery('#b2s_post_content').val() != '') {
            content = jQuery('#b2s_post_content').val().substring(0, content_range);
            content = content.substring(0, content.lastIndexOf(' '));
        }
        if (typeof jQuery('#b2s_post_excerpt').val() != 'undefined' && jQuery('#b2s_post_excerpt').val() != '') {
            exerpt = jQuery('#b2s_post_excerpt').val().substring(0, exerpt_range);
            exerpt = exerpt.substring(0, exerpt.lastIndexOf(' '));
        }
        if (typeof jQuery('#b2s_post_title').val() != 'undefined' && jQuery('#b2s_post_title').val() != '') {
            title = jQuery('#b2s_post_title').val();
        }
        if (typeof jQuery('#b2s_post_author').val() != 'undefined' && jQuery('#b2s_post_author').val() != '') {
            author = jQuery('#b2s_post_author').val();
        }
        if (typeof jQuery('#b2s_post_keywords').val() != 'undefined' && jQuery('#b2s_post_keywords').val() != '') {
            keywords = jQuery('#b2s_post_keywords').val();
        }
        template = template.replace(/{CONTENT}/g, content);
        template = template.replace(/{EXCERPT}/g, exerpt);
        template = template.replace(/{TITLE}/g, title);
        template = template.replace(/{AUTHOR}/g, author);
        template = template.replace(/{KEYWORDS}/g, keywords);
    }
    if (typeof jQuery('.b2s-edit-template-limit').val() != 'undefined' && jQuery('.b2s-edit-template-limit').val() > 0) {
        if (template.length > jQuery('.b2s-edit-template-limit').val() || jQuery('#b2s-edit-template-network-id').val() == 2) {
            if (jQuery('#b2s-edit-template-network-id').val() == 2 && jQuery('.b2s-edit-template-post-format').val() == 0) {
                template = template.substring(0, (jQuery('.b2s-edit-template-limit').val() - 24));
            } else {
                template = template.substring(0, jQuery('.b2s-edit-template-limit').val());
            }
            template = template.substring(0, template.lastIndexOf(' '));
        }
    }
    return template;
}