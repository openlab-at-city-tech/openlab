jQuery.noConflict();

jQuery(document).on('shown.bs.modal', '.modal', function () {
    jQuery(this).removeAttr('aria-hidden');
}).on('hidden.bs.modal', '.modal', function () {
    jQuery(this).attr('aria-hidden', 'true');
});

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
        
        jQuery('.b2s-edit-template-btn').hide();
        //FB Profiles+Groups are not supported
        jQuery('.btn-facebook[data-network-type="0"]').hide();
        jQuery('.btn-facebook[data-network-type="2"]').hide();
        jQuery('.b2s-network-video-not-supported').show();
        jQuery('.b2s-network-item-auth-list-li[data-network-id="1"][data-network-type="0"]').find('div').find('button').each(function () {
            jQuery(this).addClass('b2s-disabled');
        });
        jQuery('.b2s-network-item-auth-list-li[data-network-id="1"][data-network-type="2"]').find('div').find('button').each(function () {
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

        //FB Profiles+Groups are not supported
        jQuery('.btn-facebook[data-network-type="0"]').show();
        jQuery('.btn-facebook[data-network-type="2"]').show();
        jQuery('.b2s-network-video-not-supported').hide();
        jQuery('.b2s-network-item-auth-list-li[data-network-id="1"][data-network-type="0"]').find('div').find('a').each(function () {
            jQuery(this).removeClass('b2s-disabled');
        });
        jQuery('.b2s-network-item-auth-list-li[data-network-id="1"][data-network-type="2"]').find('div').find('a').each(function () {
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
            html += '<span class="b2s-sched-manager-premium-area pull-right hidden-xs"  style="width: 240px;"><span class="label label-success"><a href="#" class="btn-label-premium b2sBestTimesInfoModal">SMART</a></span></span>';
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

    
    jQuery('b2s-edit-template-user-upgrade-required').hide();
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

                if(data.content == 'b2s_upgrade_required') {
                    jQuery('.b2s-loading-area').hide();
                    jQuery('.b2s-edit-template-user-upgrade-required').show();
                    return;
                }

                jQuery('#b2s-edit-template').modal('show');
                jQuery('.b2s-edit-template-content').html(data.content);
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-edit-template-content').show();
                jQuery('.b2s-edit-template-save-btn').show();
                if (jQuery('#b2sUserVersion').val() < 1 && networkId != 1 && networkId != 3 && networkId != 19) {
                    jQuery('.b2s-edit-template-save-btn').addClass('b2s-btn-disabled');
                } else {
                    jQuery('.b2s-edit-template-save-btn').removeClass('b2s-btn-disabled');
                }
                jQuery('.b2s-edit-template-post-content').trigger('keyup');
                jQuery('.b2s-edit-template-comment').trigger('keyup');
                initAiTemplateSettings(jQuery('.b2s-edit-template-content'));
                if (window.b2sOpenTemplateInAiMode) {
                    window.b2sOpenTemplateInAiMode = false;
                    setEditTemplateMode('ai');
                } else {
                    setEditTemplateMode('standard');
                }
                jQuery('.b2s-edit-template-share-as-story-wrapper').each(function () {
                    var wrapperType = jQuery(this).data('network-type');
                    toggleFbPageShareAsStory(networkId, wrapperType);
                });
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

function toggleFbPageShareAsStory(networkId, networkType) {
        if (networkId !== 1 || networkType != 1) {
            return;
        }
        var wrapper = jQuery('.b2s-edit-template-share-as-story-wrapper[data-network-type=' + networkType + ']');
        if (!wrapper.length) {
            return;
        }
        var formatValue = jQuery('.b2s-edit-template-post-format[data-network-type=' + networkType + ']').val();
        if (formatValue== 1) {
            wrapper.show();
        } else {
            wrapper.hide();
        }
};

var b2sAiSettingsChanged = false;
var b2sStandardSettingsChanged = false;
var b2sAiSettingsPendingMode = null;
var b2sAiSettingsLoading = false;

function getCurrentEditTemplateMode() {
    var activeMode = jQuery('.b2s-edit-template-mode-btn.active').attr('data-mode');
    return (activeMode === 'ai') ? 'ai' : 'standard';
}

function getActiveTemplateType() {
    if (jQuery('.b2s-template-profile').closest('li.active').length > 0) {
        return 0;
    }
    if (jQuery('.b2s-template-page').closest('li.active').length > 0) {
        return 1;
    }
    if (jQuery('.b2s-template-group').closest('li.active').length > 0) {
        return 2;
    }
    return 0;
}

function setEditTemplateMode(mode) {
    var targetMode = (mode === 'ai') ? 'ai' : 'standard';
    jQuery('.b2s-edit-template-mode-btn').removeClass('active');
    jQuery('.b2s-edit-template-mode-btn[data-mode="' + targetMode + '"]').addClass('active');

    if (targetMode === 'ai') {
        jQuery('.b2s-edit-template-standard-content').hide();
        jQuery('.b2s-edit-template-ai-content').show();
        if (!jQuery('.b2s-ai-ass-connected-indicator').is(':visible')) {
            jQuery('.b2s-edit-template-ai-content-connect-gate').show();
        }
        jQuery('.b2s-edit-template-save-btn').hide();
        jQuery('.b2s-edit-template-no-cache-area').hide();
        jQuery('.b2s-edit-template-content').addClass('b2s-ai-mode-active');
        var $tabsNav = jQuery('.b2s-tabs-nav-container');
        if ($tabsNav.length) {
            var $config = jQuery('.tab-pane.active .b2s-ai-template-config');
            if ($config.length) {
                $config.before($tabsNav.detach());
            }
        }
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                'action': 'b2s_get_ass_settings',
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            success: function (data) {
                b2sAiSettingsLoading = true;
                if (data.result == true) {
                    if (data.settings.post_template != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-1').prop('checked', data.settings.post_template);
                        if (data.settings.post_template == true) {
                            jQuery('#b2s-global-ai-settings-checkbox-3').prop('disabled', true).prop('checked', true);
                            var text = jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text().replace(/\s?\(.*?\)/g, '');
                            var additionText = jQuery('#b2s-global-ai-settings-checkbox-3-conditional-text').text();
                            jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text(text + ' ' + additionText);
                        }
                    }
                    if (data.settings.deactivate_emojis != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-2').prop('checked', data.settings.deactivate_emojis);
                    }
                    if (data.settings.generate_hashtags != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-3').prop('checked', data.settings.generate_hashtags);
                    }
                    if (data.settings.displayed_content != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-4').prop('checked', data.settings.displayed_content).trigger('change');
                    }
                }
                b2sAiSettingsLoading = false;
            }
        });
    } else {
        jQuery('.b2s-edit-template-standard-content').show();
        jQuery('.b2s-edit-template-ai-content').hide();
        jQuery('.b2s-edit-template-ai-content-connect-gate').hide();
        jQuery('.b2s-edit-template-save-btn').show();
        jQuery('.b2s-edit-template-no-cache-area').show();
        jQuery('.b2s-edit-template-content').removeClass('b2s-ai-mode-active');
        var $tabsNav = jQuery('.b2s-tabs-nav-container');
        var $tabContent = jQuery('.tab-content.clearfix');
        if ($tabsNav.length && $tabContent.length) {
            $tabContent.before($tabsNav.detach());
        }
    }

    initAiTemplateSettings(jQuery('.b2s-edit-template-content'));
}

jQuery(document).on('change', '#b2s-global-ai-settings-checkbox-1', function () {
    var checked = jQuery(this).prop('checked');
    if (checked) {
        jQuery('#b2s-global-ai-settings-checkbox-3').prop('disabled', true).prop('checked', true);
        var text = jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text().replace(/\s?\(.*?\)/g, '');
        var additionText = jQuery('#b2s-global-ai-settings-checkbox-3-conditional-text').text();
        jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text(text + ' ' + additionText);
    } else {
        jQuery('#b2s-global-ai-settings-checkbox-3').prop('disabled', false);
        var text = jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text().replace(/\s?\(.*?\)/g, '');
        jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text(text);
    }
});

jQuery(document).on('change', '.b2s-global-ai-settings-checkbox-4', function () {
    if (this.checked) {
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-original-content').hide();
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-displayed-content').show();
        jQuery('.b2s-global-ai-settings-checkbox-4-original-content-checked').hide();
        jQuery('.b2s-global-ai-settings-checkbox-4-displayed-content-checked').show();
    } else {
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-displayed-content').hide();
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-original-content').show();
        jQuery('.b2s-global-ai-settings-checkbox-4-displayed-content-checked').hide();
        jQuery('.b2s-global-ai-settings-checkbox-4-original-content-checked').show();
    }
});

jQuery(document).on('change input', '.ai-template-form-element', function () {
    if (!b2sAiSettingsLoading) {
        b2sAiSettingsChanged = true;
    }
});

jQuery(document).on('change input', '.standard-template-form-element', function () {
    b2sStandardSettingsChanged = true;
});

jQuery(document).on('click', '.b2s-edit-template-mode-btn', function () {
    var targetMode = jQuery(this).attr('data-mode');
    if (targetMode === 'standard' && b2sAiSettingsChanged) {
        b2sAiSettingsPendingMode = targetMode;
        jQuery('#b2sAiSettingsUnsavedModal').modal('show');
        return false;
    }
    if (targetMode === 'ai' && b2sStandardSettingsChanged) {
        b2sAiSettingsPendingMode = targetMode;
        jQuery('#b2sAiSettingsUnsavedModal').modal('show');
        return false;
    }
    setEditTemplateMode(targetMode);
    return false;
});

jQuery(document).on('click', '.b2s-ai-unsaved-save-btn', function () {
    jQuery('#b2sAiSettingsUnsavedModal').modal('hide');
    if (b2sAiSettingsPendingMode === 'standard') {
        jQuery('.b2s-edit-template-save-ai-btn').trigger('click');
    } else {
        jQuery('.b2s-edit-template-save-btn').trigger('click');
    }
    if (b2sAiSettingsPendingMode) {
        setEditTemplateMode(b2sAiSettingsPendingMode);
        b2sAiSettingsPendingMode = null;
    }
});

jQuery(document).on('click', '.b2s-ai-unsaved-skip-btn', function () {
    jQuery('#b2sAiSettingsUnsavedModal').modal('hide');
    b2sAiSettingsChanged = false;
    b2sStandardSettingsChanged = false;
    if (b2sAiSettingsPendingMode) {
        setEditTemplateMode(b2sAiSettingsPendingMode);
        b2sAiSettingsPendingMode = null;
    }
});


jQuery('#b2s-edit-template').on('show.bs.modal', function () {
    b2sAiSettingsChanged = false;
    b2sStandardSettingsChanged = false;
});

jQuery('#b2sAiSettingsUnsavedModal').on('hidden.bs.modal', function () {
    if (jQuery('.modal.in').length) {
        jQuery('body').addClass('modal-open');
    }
});

jQuery('#b2sProFeatureEditTemplateModal').on('hidden.bs.modal', function () {
    if (jQuery('.modal.in').length) {
        jQuery('body').addClass('modal-open');
    }
});

jQuery(document).on('shown.bs.tab', '.b2s-template-profile, .b2s-template-page, .b2s-template-group', function () {
    if (getCurrentEditTemplateMode() === 'ai') {
        var $tabsNav = jQuery('.b2s-tabs-nav-container');
        if ($tabsNav.length) {
            var $config = jQuery('.tab-pane.active .b2s-ai-template-config');
            if ($config.length) {
                $config.before($tabsNav.detach());
            }
        }
    }
});

jQuery(document).on('click', '.b2s-edit-template-save-ai-btn', function () {
    b2sAiSettingsChanged = false;
    // Save Global AI Settings (same action as b2s-ass-settings-save-btn)
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_ass_settings_save',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val(),
            'setting_post_template': jQuery('#b2s-global-ai-settings-checkbox-1').prop('checked'),
            'setting_deactivate_emojis': jQuery('#b2s-global-ai-settings-checkbox-2').prop('checked'),
            'setting_generate_hashtags': jQuery('#b2s-global-ai-settings-checkbox-3').prop('checked'),
            'setting_displayed_content': jQuery('#b2s-global-ai-settings-checkbox-4').prop('checked')
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
        }
    });

    // Free users can only save global AI settings above
    if (parseInt(jQuery('#b2sUserVersion').val(), 10) < 1) {
        jQuery('#b2s-edit-template').modal('hide');
        return false;
    }

    var networkId = parseInt(jQuery('#b2s-edit-template-network-id').val(), 10);
    var typeId = getActiveTemplateType();
    var aiInstructionField = jQuery('.b2s-ai-template-ai-instruction[data-network-type="' + typeId + '"]');
    var generateHashtagsField = jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + typeId + '"]');
    var hashtagsCountField = jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + typeId + '"]');
    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + typeId + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + typeId + '"]');
    var emojiField = jQuery('.b2s-ai-template-emojis[data-network-type="' + typeId + '"]');

    if (!aiInstructionField.length || !networkId) {
        return false;
    }

    var generateHashtags = generateHashtagsField.length ? generateHashtagsField.val() : 'none';

    var payload = {
        enabled: jQuery('.b2s-ai-template-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_goal_enabled: jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        tone_language_enabled: jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        hashtags_keywords_enabled: jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_length_output_enabled: jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        answer_in_language: jQuery('.b2s-ai-template-answer-language[data-network-type="' + typeId + '"]').val(),
        network_name: jQuery('.b2s-ai-template-network-name[data-network-type="' + typeId + '"]').val(),
        ai_instruction: aiInstructionField.val(),
        post_goal: jQuery('.b2s-ai-template-post-goal[data-network-type="' + typeId + '"]').val(),
        cta_type: jQuery('.b2s-ai-template-cta-type[data-network-type="' + typeId + '"]').val(),
        point_of_view: jQuery('.b2s-ai-template-point-of-view[data-network-type="' + typeId + '"]').val(),
        tone: jQuery('.b2s-ai-template-tone[data-network-type="' + typeId + '"]').val(),
        content_focus: jQuery('.b2s-ai-template-content-focus[data-network-type="' + typeId + '"]').val(),
        text_form: jQuery('.b2s-ai-template-text-form[data-network-type="' + typeId + '"]').val(),
        form_of_address: jQuery('.b2s-ai-template-form-of-address[data-network-type="' + typeId + '"]').val(),
        emojis: emojiField.length ? emojiField.val() : 'off',
        writing_style: jQuery('.b2s-ai-template-writing-style[data-network-type="' + typeId + '"]').val(),
        generate_hashtags: generateHashtags,
        hashtags_count: (generateHashtags === 'from_ai' && hashtagsCountField.length) ? hashtagsCountField.val() : 0,
        use_keywords: (enforceKeywordsField.length && enforceKeywordsField.is(':checked') && useKeywordsField.length) ? useKeywordsField.val() : '',
        keyword_strength: jQuery('.b2s-ai-template-keyword-strength[data-network-type="' + typeId + '"]').val(),
        text_length: jQuery('.b2s-ai-template-text-length[data-network-type="' + typeId + '"]').val(),
        text_depth: jQuery('.b2s-ai-template-text-depth[data-network-type="' + typeId + '"]').val()
    };

    jQuery('.b2s-edit-template-save-success').hide();
    jQuery('.b2s-edit-template-save-failed').hide();

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        cache: false,
        data: {
            action: 'b2s_save_ai_post_template',
            networkId: networkId,
            typeId: typeId,
            payload: payload,
            b2s_security_nonce: jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-edit-template-save-failed').show();
            return false;
        },
        success: function (data) {
            if (data.result === true) {
                jQuery('.b2s-edit-template-save-success').show();
                setTimeout(function () {
                    jQuery('.b2s-edit-template-save-success').fadeOut();
                }, 3000);
                jQuery('#b2s-edit-template').modal('hide');
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                if (data.error == 'permission') {
                    jQuery('#b2s-edit-template').modal('hide');
                    jQuery('.b2s-no-permission-author').show();
                    return false;
                }
                jQuery('.b2s-edit-template-save-failed').show();
            }
        }
    });

    return false;
});

function getAiTemplatePreviewPostId() {
    var selectors = ['#post_ID', '#b2s_post_id', '#b2s-post-id', '#b2sPostId'];
    for (var i = 0; i < selectors.length; i++) {
        var value = parseInt(jQuery(selectors[i]).val(), 10);
        if (!isNaN(value) && value > 0) {
            return value;
        }
    }
    return 0;
}

function getAiPreviewText(typeId, key) {
    return jQuery('.b2s-ai-template-preview[data-network-type="' + typeId + '"]').data(key) || key;
}

function renderAiTemplatePreviewResult(typeId, data, isError) {
    var previewElm = jQuery('.b2s-ai-template-preview[data-network-type="' + typeId + '"]');
    if (!previewElm.length) {
        return;
    }

    previewElm.find('.b2s-ai-preview-loader').hide();
    previewElm.show();

    if (isError) {
        previewElm.find('.b2s-ai-preview-result').hide();
        previewElm.find('.b2s-ai-preview-message').text(data).show();
        return;
    }

    previewElm.find('.b2s-ai-preview-message').hide();
    previewElm.find('.b2s-ai-template-preview-title').text(previewElm.data('text-generated') || 'Generated text');
    var assText = (data.ass_text || '').replace(/\r\n|\r|\n/g, '<br>');
    previewElm.find('.b2s-ai-template-preview-text').html(assText);
    var wordsOpen = typeof data.ass_words_open !== 'undefined' ? data.ass_words_open : 0;
    var wordsTotal = typeof data.ass_words_total !== 'undefined' ? data.ass_words_total : 0;
    previewElm.find('.b2s-ai-template-preview-meta').text((previewElm.data('text-left') || 'Left') + ': ' + wordsOpen + ' / ' + wordsTotal);
    if (data.ass_hashtags) {
        previewElm.find('.b2s-ai-template-preview-hashtags').html('<strong>' + (previewElm.data('text-hashtags') || 'Hashtags') + ':</strong> ' + data.ass_hashtags).show();
    } else {
        previewElm.find('.b2s-ai-template-preview-hashtags').hide();
    }
    previewElm.find('.b2s-ai-preview-result').show();
}

jQuery(document).on('click', '.b2s-ai-template-preview-btn', function () {
    var previewBtn = jQuery(this);
    var networkId = parseInt(jQuery('#b2s-edit-template-network-id').val(), 10);
    var typeId = getActiveTemplateType();
    var postId = getAiTemplatePreviewPostId();
    var postTextElm = jQuery('.b2s-edit-template-preview-content[data-network-type="' + typeId + '"]').first();
    var postText = postTextElm.length ? jQuery.trim(postTextElm.text()) : '';
    var generateHashtagsField = jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + typeId + '"]');
    var hashtagsCountField = jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + typeId + '"]');
    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + typeId + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + typeId + '"]');
    var emojiField = jQuery('.b2s-ai-template-emojis[data-network-type="' + typeId + '"]');
    var generateHashtags = generateHashtagsField.length ? generateHashtagsField.val() : 'none';

    var aiTemplatePayload = {
        enabled: jQuery('.b2s-ai-template-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_goal_enabled: jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        tone_language_enabled: jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        hashtags_keywords_enabled: jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_length_output_enabled: jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        answer_in_language: jQuery('.b2s-ai-template-answer-language[data-network-type="' + typeId + '"]').val(),
        network_name: jQuery('.b2s-ai-template-network-name[data-network-type="' + typeId + '"]').val(),
        ai_instruction: jQuery('.b2s-ai-template-ai-instruction[data-network-type="' + typeId + '"]').val(),
        post_goal: jQuery('.b2s-ai-template-post-goal[data-network-type="' + typeId + '"]').val(),
        cta_type: jQuery('.b2s-ai-template-cta-type[data-network-type="' + typeId + '"]').val(),
        point_of_view: jQuery('.b2s-ai-template-point-of-view[data-network-type="' + typeId + '"]').val(),
        tone: jQuery('.b2s-ai-template-tone[data-network-type="' + typeId + '"]').val(),
        content_focus: jQuery('.b2s-ai-template-content-focus[data-network-type="' + typeId + '"]').val(),
        text_form: jQuery('.b2s-ai-template-text-form[data-network-type="' + typeId + '"]').val(),
        form_of_address: jQuery('.b2s-ai-template-form-of-address[data-network-type="' + typeId + '"]').val(),
        emojis: emojiField.length ? emojiField.val() : 'off',
        writing_style: jQuery('.b2s-ai-template-writing-style[data-network-type="' + typeId + '"]').val(),
        generate_hashtags: generateHashtags,
        hashtags_count: (generateHashtags === 'from_ai' && hashtagsCountField.length) ? hashtagsCountField.val() : 0,
        use_keywords: (enforceKeywordsField.length && enforceKeywordsField.is(':checked') && useKeywordsField.length) ? useKeywordsField.val() : '',
        keyword_strength: jQuery('.b2s-ai-template-keyword-strength[data-network-type="' + typeId + '"]').val(),
        text_length: jQuery('.b2s-ai-template-text-length[data-network-type="' + typeId + '"]').val(),
        text_depth: jQuery('.b2s-ai-template-text-depth[data-network-type="' + typeId + '"]').val()
    };

    if (!networkId) {
        renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-no-network'), true);
        return false;
    }

    if (!postId) {
        renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-no-context'), true);
        return false;
    }

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        cache: false,
        async: true,
        data: {
            action: 'b2s_ass_generate_text_sm',
            b2s_security_nonce: jQuery('#b2s_security_nonce').val(),
            post_format: jQuery('.b2s-edit-template-post-format[data-network-type="' + typeId + '"]').val(),
            post_network_name: jQuery('.b2s-ai-template-network-name[data-network-type="' + typeId + '"]').val(),
            post_lang: jQuery('#b2sUserLang').val() || 'en',
            post_id: postId,
            network_id: networkId,
            network_type: typeId,
            network_kind: typeId,
            sel_sched_date: jQuery('#selSchedDate').val() || '',
            b2s_post_type: jQuery('#b2sPostType').val() || '',
            relay_count: jQuery('#b2sRelayCount').val() || 0,
            is_video_mode: jQuery('#b2sIsVideo').val() || 0,
            post_url: jQuery('#b2sDefault_url').val() || jQuery('#b2s_post_url').val() || '',
            input_text: postText,
            ai_template_settings: aiTemplatePayload
        },
        beforeSend: function () {
            previewBtn.prop('disabled', true);
            var previewElm = jQuery('.b2s-ai-template-preview[data-network-type="' + typeId + '"]');
            previewElm.find('.b2s-ai-preview-result').hide();
            previewElm.find('.b2s-ai-preview-message').hide();
            previewElm.find('.b2s-ai-preview-loader-text').text(previewElm.data('text-generating') || 'Generating preview text...');
            previewElm.find('.b2s-ai-preview-loader').show();
            previewElm.show();
        },
        success: function (response) {
            previewBtn.prop('disabled', false);
            var data = response;
            if (typeof response === 'string') {
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-parse-error'), true);
                    return;
                }
            }

            if (data && data.error === 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
                renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-security-fail'), true);
                return;
            }

            if (data && data.result === true && data.ass_text) {
                renderAiTemplatePreviewResult(typeId, data, false);
                return;
            }

            renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-gen-fail'), true);
        },
        error: function () {
            previewBtn.prop('disabled', false);
            renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-request-fail'), true);
        }
    });

    return false;
});

function updateAiTemplateState(networkType) {
    var enabled = jQuery('.b2s-ai-template-enabled[data-network-type="' + networkType + '"]').is(':checked');
    jQuery('.b2s-ai-template-settings[data-network-type="' + networkType + '"]').show().toggleClass('b2s-ai-template-settings-disabled', !enabled);
    jQuery('.b2s-ai-template-disabled-info[data-network-type="' + networkType + '"]').toggle(!enabled);

    var contentGoalEnabled = jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var contentGoalFields = jQuery('.b2s-ai-template-content-goal-fields[data-network-type="' + networkType + '"]');
    if (contentGoalFields.length) {
        contentGoalFields.toggleClass('b2s-ai-template-group-fields-disabled', !contentGoalEnabled);
        contentGoalFields.find('input, select, textarea, button').prop('disabled', !contentGoalEnabled);
    }

    var toneLanguageEnabled = jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var toneLanguageFields = jQuery('.b2s-ai-template-tone-language-fields[data-network-type="' + networkType + '"]');
    if (toneLanguageFields.length) {
        toneLanguageFields.toggleClass('b2s-ai-template-group-fields-disabled', !toneLanguageEnabled);
        toneLanguageFields.find('input, select, textarea, button').prop('disabled', !toneLanguageEnabled);
    }

    var hashtagsKeywordsEnabled = jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var hashtagsKeywordsFields = jQuery('.b2s-ai-template-hashtags-keywords-fields[data-network-type="' + networkType + '"]');
    if (hashtagsKeywordsFields.length) {
        hashtagsKeywordsFields.toggleClass('b2s-ai-template-group-fields-disabled', !hashtagsKeywordsEnabled);
        hashtagsKeywordsFields.find('input, select, textarea, button').prop('disabled', !hashtagsKeywordsEnabled);
    }

    var contentLengthOutputEnabled = jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var contentLengthOutputFields = jQuery('.b2s-ai-template-content-length-output-fields[data-network-type="' + networkType + '"]');
    if (contentLengthOutputFields.length) {
        contentLengthOutputFields.toggleClass('b2s-ai-template-group-fields-disabled', !contentLengthOutputEnabled);
        contentLengthOutputFields.find('input, select, textarea, button').prop('disabled', !contentLengthOutputEnabled);
    }

    var generateHashtagsField = jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + networkType + '"]');
    var hashtagsCountField = jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + networkType + '"]');
    if (hashtagsCountField.length) {
        hashtagsCountField.prop('disabled', !(generateHashtagsField.length && generateHashtagsField.val() === 'from_ai' && hashtagsKeywordsEnabled));
        if (generateHashtagsField.length && generateHashtagsField.val() === 'none') {
            hashtagsCountField.val(0);
        }
    }

    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + networkType + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + networkType + '"]');
    if (useKeywordsField.length) {
        useKeywordsField.toggle(enforceKeywordsField.length && enforceKeywordsField.is(':checked'));
    }
}

function initAiTemplateSettings(context) {
    var scope = context && context.length ? context : jQuery(document);
    scope.find('.b2s-ai-template-enabled').each(function () {
        var networkType = jQuery(this).attr('data-network-type');
        updateAiTemplateState(networkType);
    });

    scope.find('.b2s-ai-template-connect-gate:visible').each(function () {
        resetAiInlineAssistiniAuth(jQuery(this));
    });
}

function loadDefaultAiTemplate(networkType) {
    var settingsWrapper = jQuery('.b2s-ai-template-settings[data-network-type="' + networkType + '"]');
    if (!settingsWrapper.length) {
        return;
    }

    var defaultConfig = settingsWrapper.attr('data-ai-defaults');
    if (!defaultConfig) {
        return;
    }

    var defaults = null;
    try {
        defaults = JSON.parse(defaultConfig);
    } catch (error) {
        return;
    }

    if (!defaults || typeof defaults !== 'object') {
        return;
    }

    jQuery('.b2s-ai-template-enabled[data-network-type="' + networkType + '"]').prop('checked', parseInt(defaults.enabled, 10) === 1);
    jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.content_goal_enabled === 'undefined' ? true : parseInt(defaults.content_goal_enabled, 10) === 1);
    jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.tone_language_enabled === 'undefined' ? true : parseInt(defaults.tone_language_enabled, 10) === 1);
    jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.hashtags_keywords_enabled === 'undefined' ? true : parseInt(defaults.hashtags_keywords_enabled, 10) === 1);
    jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.content_length_output_enabled === 'undefined' ? true : parseInt(defaults.content_length_output_enabled, 10) === 1);
    jQuery('.b2s-ai-template-answer-language[data-network-type="' + networkType + '"]').val(defaults.answer_in_language || 'en');
    jQuery('.b2s-ai-template-network-name[data-network-type="' + networkType + '"]').val(defaults.network_name || '');
    jQuery('.b2s-ai-template-ai-instruction[data-network-type="' + networkType + '"]').val(defaults.ai_instruction || '');
    jQuery('.b2s-ai-template-post-goal[data-network-type="' + networkType + '"]').val(defaults.post_goal || 'traffic');
    jQuery('.b2s-ai-template-cta-type[data-network-type="' + networkType + '"]').val(defaults.cta_type || 'none');
    jQuery('.b2s-ai-template-tone[data-network-type="' + networkType + '"]').val(defaults.tone || 'neutral');
    jQuery('.b2s-ai-template-content-focus[data-network-type="' + networkType + '"]').val(defaults.content_focus || 50);
    jQuery('.b2s-ai-template-point-of-view[data-network-type="' + networkType + '"]').val(defaults.point_of_view || 'neutral');
    jQuery('.b2s-ai-template-text-form[data-network-type="' + networkType + '"]').val(defaults.text_form || 'default');
    jQuery('.b2s-ai-template-form-of-address[data-network-type="' + networkType + '"]').val(defaults.form_of_address || 'neutral');
    jQuery('.b2s-ai-template-emojis[data-network-type="' + networkType + '"]').val(defaults.emojis || 'auto');
    jQuery('.b2s-ai-template-writing-style[data-network-type="' + networkType + '"]').val(defaults.writing_style || 'default');
    jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + networkType + '"]').val(defaults.generate_hashtags || 'from_ai');
    jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + networkType + '"]').val(typeof defaults.hashtags_count !== 'undefined' ? defaults.hashtags_count : 1);
    jQuery('.b2s-ai-template-keyword-strength[data-network-type="' + networkType + '"]').val(defaults.keyword_strength || 50);
    jQuery('.b2s-ai-template-text-length[data-network-type="' + networkType + '"]').val(defaults.text_length || 'medium');
    jQuery('.b2s-ai-template-text-depth[data-network-type="' + networkType + '"]').val(defaults.text_depth || 50);

    var keywords = defaults.use_keywords || '';
    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + networkType + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + networkType + '"]');
    if (enforceKeywordsField.length) {
        enforceKeywordsField.prop('checked', keywords.length > 0);
    }
    if (useKeywordsField.length) {
        useKeywordsField.val(keywords);
    }

    updateAiTemplateState(networkType);
    jQuery('.b2s-ai-template-preview[data-network-type="' + networkType + '"]').hide().html('');
}

function resetAiInlineAssistiniAuth(gate) {
    if (!gate || !gate.length) {
        return;
    }
    gate.find('.b2s-ai-ass-auth-step-1-content').show();
    gate.find('.b2s-ai-ass-auth-step-3-content').hide();
    gate.find('.b2s-ai-ass-step-circle').removeClass('b2s-ass-color btn-danger').addClass('btn-default');
    gate.find('.b2s-ai-ass-step-circle[data-step="1"]').addClass('b2s-ass-color btn-danger').removeClass('btn-default');
}

function openInlineAssistiniAuth(url, title) {
    var location = window.location.protocol + '//' + window.location.hostname;
    var targetUrl = encodeURI(url + '&location=' + location);
    window.open(targetUrl, title, "width=650,height=800,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
}

jQuery(document).on('click', '.b2s-ai-ass-auth-step1-btn', function () {
    var gate = jQuery(this).closest('.b2s-ai-template-connect-gate');
    var emailOwn = gate.find('.b2s-ai-ass-auth-email-option[value="0"]');
    var add = '';
    if (emailOwn.is(':checked')) {
        add = '&email=' + emailOwn.attr('data-auth-email');
    }

    gate.find('.b2s-ai-ass-step-circle[data-step="1"], .b2s-ai-ass-step-circle[data-step="2"]').addClass('b2s-ass-color btn-danger').removeClass('btn-default');
    openInlineAssistiniAuth(jQuery(this).attr('data-url') + add, jQuery(this).attr('data-auth-title'));
    return false;
});

jQuery(document).on('click', '.b2s-ai-ass-auth-step3-btn', function () {
    var gate = jQuery(this).closest('.b2s-ai-template-connect-gate');
    gate.hide();
    jQuery('.b2s-ai-template-config').show();
    jQuery('.b2s-ai-ass-connected-indicator').show();
    jQuery('#b2s-global-ai-settings-not-connected-overlay').remove();
    jQuery('.b2s-ai-template-not-connected-overlay').remove();
    return false;
});

jQuery(document).on('change', '.b2s-ai-template-enabled, .b2s-ai-template-content-goal-enabled, .b2s-ai-template-tone-language-enabled, .b2s-ai-template-hashtags-keywords-enabled, .b2s-ai-template-content-length-output-enabled, .b2s-ai-template-generate-hashtags, .b2s-ai-template-enforce-keywords', function () {
    var networkType = jQuery(this).attr('data-network-type');
    updateAiTemplateState(networkType);
});

jQuery(document).on('change', '.b2s-ai-template-emojis', function () {
    
    if (jQuery(this).val() !== 'none') {
        jQuery('#b2s-global-ai-settings-checkbox-2').prop('checked', false);
    }
});

jQuery(document).on('change', '.b2s-ai-template-hashtags-count', function () {
    if (parseInt(jQuery(this).val(), 10) > 0) {
        jQuery('#b2s-global-ai-settings-checkbox-3').prop('checked', true);
    }
});

jQuery(document).on('click', '.b2s-edit-template-load-default-ai', function () {
    loadDefaultAiTemplate(jQuery(this).attr('data-network-type'));
    return false;
});

jQuery(window).on("load", function () {
    if (jQuery('#b2sUserVersion').val() >= 1) {
     
        jQuery(document).on('click', '.b2s-edit-template-link-post', function () {
            var networkId = jQuery(this).data('network-id');
            var networkType = jQuery(this).attr('data-network-type');
            jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-text-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
            if(networkId == 4){
                jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('3');

            }else
            {
                jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('0');
            }
            jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            jQuery('.b2s-edit-template-text-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.tumblr-link-post-notice').show();
            toggleFbPageShareAsStory(networkId, networkType);

            //Tumblr special Preview Post again
            if(networkId == 4)
            {
                var post = generateExamplePost(jQuery('.b2s-edit-template-post-content').val().replace(/\n/g, "<br>"), jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
                jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
            }
           
        });

        jQuery(document).on('click', '.b2s-edit-template-image-post', function () {
                initAiTemplateSettings(jQuery('.b2s-edit-template-content'));
                setEditTemplateMode('standard');
            var networkId = jQuery(this).data('network-id');
            var networkType = jQuery(this).attr('data-network-type');
            jQuery('.tumblr-link-post-notice').hide();
            jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-text-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
            jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('1');
            jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-text-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            toggleFbPageShareAsStory(networkId, networkType);

            //Tumblr special Preview Post again
            if(networkId == 4)
            {
                var post = generateExamplePost(jQuery('.b2s-edit-template-post-content').val().replace(/\n/g, "<br>"), jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
                jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
            }

            
        });

        //Tumblr Text
        jQuery(document).on('click', '.b2s-edit-template-text-post', function () {
            var networkId = jQuery(this).data('network-id');
            var networkType = jQuery(this).attr('data-network-type');
            jQuery('.tumblr-link-post-notice').hide();
            jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
            jQuery('.b2s-edit-template-text-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
            jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('0');
            jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
            jQuery('.b2s-edit-template-text-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
            toggleFbPageShareAsStory(networkId, networkType);

            //Tumblr special Preview Post again
            if(networkId == 4)
            {
                var post = generateExamplePost(jQuery('.b2s-edit-template-post-content').val().replace(/\n/g, "<br>"), jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
                jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
            }

        });

   
        jQuery(document).on('click', '.b2s-tiktok-promotion-radio', function () {
            
            var options = jQuery('.b2s-tiktok-promotion-options[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]');
            if (jQuery(this).attr("value") == 1) {
                options.show()
            } else {
                options.hide()
            }
        });


    jQuery(document).on('change', '.b2s-tiktok-status_privacy', function () {
     
        var networkAuthId = jQuery(this).attr("data-network-auth-id");
        var mode = jQuery(this).val();

        if(mode == "SELF_ONLY"){
    
            jQuery('.b2s-tiktok-promotion-radio[data-network-id="'+networkAuthId+'"]').attr("disabled", true);
            jQuery('.b2s-tiktok-promotion-radio[data-network-id="'+networkAuthId+'"]').first().prop("checked", false);
            jQuery('.b2s-tiktok-promotion-radio[data-network-id="'+networkAuthId+'"]').last().prop("checked", true);
            jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sTiktokPromotionThirdParty\\]').attr("disabled", true);

        } else {

            jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sTiktokPromotionThirdParty\\]').attr("disabled", false);
        }
    });

   jQuery(document).on('change', '.b2s-tiktok-form-select', function () {

        var networkAuthId = jQuery(this).attr("data-network-auth-id");
        var value= jQuery(this).val();

        if (value === "0") {
        
            jQuery('.tiktok-share-settings[data-network-auth-id="'+networkAuthId+'"]').hide();

        } else {
        
             jQuery('.tiktok-share-settings[data-network-auth-id="'+networkAuthId+'"]').show();
        }
    });


        jQuery(document).on('click', '.tiktok-promotional-toggle', function () {
        
            jQuery(this).toggleClass('off'); 
    
            var networkAuthId = jQuery(this).attr("data-network-auth-id");
        
            var options = jQuery('.b2s-tiktok-promotion-options[data-network-auth-id="'+jQuery(this).attr('data-network-auth-id')+'"]');
            var self_only_option = jQuery('.b2s-tiktok-status_privacy[data-network-auth-id="'+networkAuthId +'"] > option[value="SELF_ONLY"]');

            var submitButton = jQuery('.b2s-submit-btn');
            var submitButtonScroll = jQuery('.b2s-submit-btn-scroll');
            
            //Toggle on
            if(!jQuery(this).hasClass('off')){
                options.show();

                var promotionChecked= jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sTiktokPromotionOwnBrand\\]').is(':checked');
                var brandedChecked= jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sTiktokPromotionThirdParty\\]').is(':checked');

                if(brandedChecked){
                    self_only_option.attr("disabled", true);
                    self_only_option.text(jQuery(".b2s-tiktok-self-only-disabled-text").val());
                    jQuery('.tiktok-music-confirmation[data-network-auth-id="'+networkAuthId +'"]').hide();
                    jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+networkAuthId +'"]').show();
                    jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sPaidPartnership\\]').show();
                }

                if(promotionChecked && !brandedChecked){
                    jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sPromotional\\]').show();
                    jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sPaidPartnership\\]').hide();
                    jQuery('.tiktok-music-confirmation[data-network-auth-id="'+networkAuthId +'"]').show();
                    jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+networkAuthId +'"]').hide();
                    
                }

                if(!promotionChecked && !brandedChecked){
                    jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sPromotional\\]').hide();
                    jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sPaidPartnership\\]').hide();
                    jQuery('.tiktok-music-confirmation[data-network-auth-id="'+networkAuthId +'"]').show();
                    jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+networkAuthId +'"]').hide();
                
                    submitButton.prop('disabled', true)
                    submitButtonScroll.prop('disabled', true)

                    submitButtonScroll.hover(
                        function () {
                            var text = jQuery('.b2s-tiktok-no-promotion-selected').val();
                            jQuery(this).attr('title', text);
                        });

                    submitButton.hover(
                        function () {
                            var text = jQuery('.b2s-tiktok-no-promotion-selected').val();
                            jQuery(this).attr('title', text);
                        });
                
                
                } else {
                    
                    submitButton.prop('disabled', false)
                    submitButtonScroll.prop('disabled', false)
                    submitButton.removeAttr('title');
                    submitButtonScroll.removeAttr('title');
                }

            } else {

                jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sPromotional\\]').hide();
                jQuery('#b2s\\['+networkAuthId +'\\]\\[b2sPaidPartnership\\]').hide();

                jQuery('.tiktok-music-confirmation[data-network-auth-id="'+networkAuthId +'"]').show();
                jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+networkAuthId +'"]').hide();
            
                options.hide()
                self_only_option.attr("disabled", false);
                self_only_option.text(jQuery(".b2s-tiktok-self-only-text").val());

                submitButton.prop('disabled', false)
                submitButtonScroll.prop('disabled', false)
                submitButton.removeAttr('title');
                submitButtonScroll.removeAttr('title');  
                submitButton.unbind('mouseenter mouseleave');
                submitButtonScroll.unbind('mouseenter mouseleave');
            }
        });

        jQuery(document).on('change', '.b2s-tiktok-promotion-option', function () {
        
            if (jQuery(this).is(':checked')) {
                jQuery(this).val("on");   // change value when checked
            } else {
                jQuery(this).val("off");  // change value when unchecked
            }

            var networkAuthId = jQuery(this).attr("data-network-auth-id");
            var promotionChecked= jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sTiktokPromotionOwnBrand\\]').is(':checked');
            var brandedChecked= jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sTiktokPromotionThirdParty\\]').is(':checked');
            var self_only_option = jQuery('.b2s-tiktok-status_privacy[data-network-auth-id="'+networkAuthId+'"] > option[value="SELF_ONLY"]');

            if(brandedChecked){
                jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sPaidPartnership\\]').show();
                jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sPromotional\\]').hide();

                self_only_option.attr("disabled", true);
                self_only_option.text(jQuery(".b2s-tiktok-self-only-disabled-text").val());

                jQuery('.tiktok-music-confirmation[data-network-auth-id="'+networkAuthId+'"]').hide();
                jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+networkAuthId+'"]').show();
        
            }else
            {
                self_only_option.attr("disabled", false);
                self_only_option.text(jQuery(".b2s-tiktok-self-only-text").val());
            }
            
            if(promotionChecked && !brandedChecked){
                
                jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sPromotional\\]').show();
                jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sPaidPartnership\\]').hide();
                jQuery('.tiktok-music-confirmation[data-network-auth-id="'+networkAuthId+'"]').show();
                jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+networkAuthId+'"]').hide();
                return;
            }
            if(!promotionChecked && !brandedChecked){
                jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sPromotional\\]').hide();
                jQuery('#b2s\\['+networkAuthId+'\\]\\[b2sPaidPartnership\\]').hide();
                jQuery('.tiktok-music-confirmation[data-network-auth-id="'+networkAuthId+'"]').show();
                jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+networkAuthId+'"]').hide();
                return;
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

        jQuery(document).on('mousedown mouseup keydown keyup', '.b2s-edit-template-comment', function () {
            var tb = jQuery(this).get(0);
            jQuery('.b2s-edit-template-comment-selection-start[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionStart);
            jQuery('.b2s-edit-template-comment-selection-end[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionEnd);
            
            // Update comment preview with shortcode replacement
            var comment = generateExamplePost(jQuery(this).val(), jQuery('.b2s-edit-template-range-comment[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range-comment[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
            comment = comment.replace(/\n/g, "<br>");
            jQuery('.b2s-edit-template-preview-comment[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(comment);
            
            // Show or hide comment wrapper based on whether there's text
            var wrapper = jQuery('.b2s-edit-template-preview-comment-wrapper[data-network-type="' + jQuery(this).attr('data-network-type') + '"]');
            if (jQuery(this).val().trim() !== '') {
                wrapper.show();
            } else {
                wrapper.hide();
            }
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
        jQuery(document).on('change', '.b2s-edit-template-range-comment', function () {
            jQuery('.b2s-edit-template-comment').trigger('keyup');
        });
        jQuery(document).on('change', '.b2s-edit-template-excerpt-range-comment', function () {
            jQuery('.b2s-edit-template-comment').trigger('keyup');
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

        jQuery(document).on('click', '.b2s-edit-template-comment-post-item', function () {
            var networkType = jQuery(this).attr('data-network-type');
            var text = jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val();
            var start = jQuery('.b2s-edit-template-comment-selection-start[data-network-type="' + networkType + '"]').val();
            var end = jQuery('.b2s-edit-template-comment-selection-end[data-network-type="' + networkType + '"]').val();

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
            jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val(newText);
            jQuery('.b2s-edit-template-comment').focus();
            jQuery('.b2s-edit-template-comment').trigger('keyup');
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

        jQuery(document).on('click', '.b2s-edit-template-comment-clear-btn', function () {
            var networkType = jQuery(this).attr('data-network-type');
            jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val("");
            jQuery('.b2s-edit-template-comment').focus();
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

        jQuery(document).on('keyup', '.b2s-edit-template-range-comment', function () {
            if (isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val()) < 1) {
                jQuery(this).val("1");
            }
            if (jQuery(this).attr('max') > 0 && parseInt(jQuery(this).val()) > jQuery(this).attr('max')) {
                jQuery(this).val(jQuery(this).attr('max'));
            }
            event.preventDefault();
            return false;
        });

        jQuery(document).on('keyup', '.b2s-edit-template-excerpt-range-comment', function () {
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
                        initAiTemplateSettings(jQuery('.b2s-template-tab-' + networkType));
                        setEditTemplateMode(getCurrentEditTemplateMode());
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
    b2sStandardSettingsChanged = false;
    if (jQuery('#b2sUserVersion').val() < 1 && jQuery('#b2s-edit-template-network-id').val() != 1 && jQuery('#b2s-edit-template-network-id').val() != 3 && jQuery('#b2s-edit-template-network-id').val() != 19) {
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
        
        if (jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 45){
            if (jQuery('.b2s-twitter-thread[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['twitterThreads'] = true;
            }else{
                template_data[networkType]['twitterThreads'] = false;
            }
        }

        if (typeof jQuery('.b2s-edit-template-enable-link') != "undefined") {
            if (jQuery('.b2s-edit-template-enable-link[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['addLink'] = true;
            } else {
                template_data[networkType]['addLink'] = false;
            }
        }
        if (jQuery('.b2s-edit-template-share-as-story[data-network-type="' + networkType + '"]').length) {
            if (jQuery('.b2s-edit-template-share-as-story[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['share_as_story'] = 1;
            } else {
                template_data[networkType]['share_as_story'] = 0;
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
        
        // Save default comment for networks that support comments
        if (typeof jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]') != "undefined") {
            var defaultCommentValue = jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val();
            if (defaultCommentValue !== undefined) {
                template_data[networkType]['comment'] = defaultCommentValue;
            }
            
            // Save comment character limits
            var commentRangeMax = jQuery('.b2s-edit-template-range-comment[data-network-type="' + networkType + '"]').val();
            var commentExcerptRangeMax = jQuery('.b2s-edit-template-excerpt-range-comment[data-network-type="' + networkType + '"]').val();
            if (commentRangeMax !== undefined && commentExcerptRangeMax !== undefined) {
                template_data[networkType]['comment_range_max'] = commentRangeMax;
                template_data[networkType]['comment_excerpt_range_max'] = commentExcerptRangeMax;
            }
        }
    });

    //TikTok Share Settings
    if(jQuery('#b2s-edit-template-network-id').val() == 36) {
       
        if(jQuery(".b2s-edit-template-share-settings[data-network-id='"+jQuery('#b2s-edit-template-network-id').val()+"']").hasClass("btn-primary")){
        
            var discloseToggleOff = jQuery('[name="b2s[36][b2s-tiktok-disclose-toggle]"]').hasClass("off");
            var allowComment= jQuery("#b2s\\[36\\]\\[b2sTiktokAllowComment\\]").prop('checked');
            var isOwndBrand= jQuery("#b2s\\[36\\]\\[b2sTiktokPromotionOwnBrand\\]").prop('checked');
            var isPromotion= jQuery("#b2s\\[36\\]\\[b2sTiktokPromotionThirdParty\\]").prop('checked');
            var privacyStatus= jQuery('#b2s\\[36\\]\\[status_privacy\\]').val();

            if(discloseToggleOff){
               isOwndBrand = false;
               isPromotion = false;
            }
    
            var shareSettings= {
                "allow_comment": allowComment,
                "promotion_option_organic": isOwndBrand,
                "promotion_option_branded": isPromotion,
                "status_privacy": privacyStatus
            }
    
            template_data[0]['share_settings'] = shareSettings;
        }   
    }   

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
jQuery('.b2s-info-share-as-story-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

jQuery(document).on('click', '.b2s-network-add-mandant-btn', function () {
    jQuery('#b2s-network-add-mandant').modal('show');
});
jQuery(document).on('click', '.b2sBestTimesInfoModal', function () {
    jQuery('#b2sBestTimesInfoModal').modal('show');
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
jQuery(document).on('click', '.b2s-info-share-as-story-modal-btn', function () {
    jQuery('.b2s-info-share-as-story-modal').modal('show');
});

//START Network Auth Settings
jQuery(document).on('click', '.b2s-network-auth-settings-btn', function () {

    jQuery('.b2-share-settings').hide();
    jQuery('#b2s-edit-network-auth-settings').modal('show');

    var networkAuthId = jQuery(this).attr('data-network-auth-id');

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

                    if( result.shareSettings != false) {

                        jQuery('.b2-share-settings-content').html(result.shareSettings);
                        jQuery('.b2-share-settings').show();
                      
                        if(jQuery('#b2s\\['+networkAuthId+'\\]\\[b2s-tiktok-toggle-on\\]').html() == '1'){

                            jQuery('.toggle[name="b2s['+networkAuthId+'][b2s-tiktok-disclose-toggle]"]').click();
                        }

                    }else
                    {
                        jQuery('.b2-share-settings').hide();
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
        //TikTok Share Settings must be enabled from Pro for Autoposter, Resharer and Preview
        if (jQuery('#b2sUserVersion').val() >= 2 && jQuery(this).attr('data-network-id') == 36) {
            jQuery('.b2s-network-auth-settings-content').hide();
            jQuery('.b2s-loading-area').show();
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
                        if( result.shareSettings != false) {
                            jQuery('.b2-share-settings-content').html(result.shareSettings);
                            jQuery('.b2-share-settings').show();
                            if(jQuery('#b2s\\['+networkAuthId+'\\]\\[b2s-tiktok-toggle-on\\]').html() == '1'){
                                jQuery('.toggle[name="b2s['+networkAuthId+'][b2s-tiktok-disclose-toggle]"]').click();
                            }
                        }else{
                            jQuery('.b2-share-settings').hide();
                        }
                    }
                    jQuery('.b2s-loading-area').hide();
                }
            });
        };
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

jQuery(document).on('click', '.b2s-share-settings-save-btn', function () {

    jQuery('.b2s-network-auth-settings-content').hide();
    jQuery('.b2s-loading-area').show();
    //jQuery('.b2s-url-parameter-error').hide();

    var originNetworkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkId = jQuery(this).attr('data-network-id');

    //TikTok Share Settings
    if(networkId  == 36 ) {
       
      var discloseToggleOff = jQuery('div.toggle[data-network-auth-id="'+originNetworkAuthId+'"]').hasClass("off");

        var allowComment = jQuery('#b2s\\['+originNetworkAuthId+'\\]\\[b2sTiktokAllowComment\\]').prop('checked');
        var allowStitch  = jQuery('#b2s\\['+originNetworkAuthId+'\\]\\[b2sTiktokAllowStitch\\]').prop('checked');
        var allowDuet    = jQuery('#b2s\\['+originNetworkAuthId+'\\]\\[b2sTiktokAllowDuet\\]').prop('checked');

        var isOwndBrand  = jQuery('#b2s\\['+originNetworkAuthId+'\\]\\[b2sTiktokPromotionOwnBrand\\]').prop('checked');
        var isPromotion  = jQuery('#b2s\\['+originNetworkAuthId+'\\]\\[b2sTiktokPromotionThirdParty\\]').prop('checked');

        var privacyStatus = jQuery('#b2s\\['+originNetworkAuthId+'\\]\\[status_privacy\\]').val();
        var shareAsDraft  = jQuery('#b2s\\['+originNetworkAuthId+'\\]\\[tiktok_share_mode\\]').val() === "0";

        if (discloseToggleOff) {
            isOwndBrand = false;
            isPromotion = false;
        }
   
        if(discloseToggleOff){
            isOwndBrand = false;
            isPromotion = false;
        }

        var shareSettings= {
            "allow_comment": allowComment,
            "promotion_option_organic": isOwndBrand,
            "promotion_option_branded": isPromotion,
            "status_privacy": privacyStatus,
            "share_as_draft": shareAsDraft,
            "allow_stitch": allowStitch,
            "allow_duet": allowDuet
        }

    }   

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_share_settings',
            'shareSettings': JSON.stringify(shareSettings), 
            'originNetworkAuthId': originNetworkAuthId,
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
                
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    //jQuery('.b2s-url-parameter-error[data-error-reason="save"]').show();
                }
            }
        }
    });
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


jQuery(document).on('click', '.b2s-network-add-app-info-btn', function () {
    jQuery('#b2sNetworkAddAppInfoModal').modal('show');
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
    // Strip WP block delimiter comments
    template = template.replace(/<!--[\s\S]*?-->/g, '');
    // Escape user-supplied HTML to prevent XSS. The template arrives with \n already
    // replaced by <br> at the call site, so we preserve those with a placeholder.
    template = template
        .replace(/&/g, '&amp;')
        .replace(/<br>/gi, '\x01')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\x01/g, '<br>');
    if (jQuery('#b2s_use_post').val() == 'true') {
        var content = '';
        var exerpt = '';
        var title = '';
        var url = '';
        var author = '';
        var keywords = '';
        if (typeof jQuery('#b2s_post_content').val() != 'undefined' && jQuery('#b2s_post_content').val() != '') {
            content = b2sGetExcerpt(jQuery('#b2s_post_content').val(), content_range);
        }
        if (typeof jQuery('#b2s_post_excerpt').val() != 'undefined' && jQuery('#b2s_post_excerpt').val() != '') {
            exerpt = b2sGetExcerpt(jQuery('#b2s_post_excerpt').val(), exerpt_range);
        }
        if (typeof jQuery('#b2s_post_title').val() != 'undefined' && jQuery('#b2s_post_title').val() != '') {
            title = jQuery('#b2s_post_title').val();
        }
        if (typeof jQuery('#b2s_post_url').val() != 'undefined' && jQuery('#b2s_post_url').val() != '') {
            url = jQuery('#b2s_post_url').val();
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
        template = template.replace(/{URL}/g, url);
        template = template.replace(/{AUTHOR}/g, author);
        template = template.replace(/{KEYWORDS}/g, keywords);

    }
    if (typeof jQuery('.b2s-edit-template-limit').val() != 'undefined' && jQuery('.b2s-edit-template-limit').val() > 0) {
        var limit = parseInt(jQuery('.b2s-edit-template-limit').val(), 10);
        if (template.length > limit || jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 45) {
            if ((jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 45) && jQuery('.b2s-edit-template-post-format').val() == 0) {
                template = b2sGetExcerpt(template, limit - 24);
            } else {
                template = b2sGetExcerpt(template, limit);
            }
        }
    }

    //tumblr special preview case
    if(jQuery('#b2s-edit-template-network-id').val() == 4){
        var postFormat= jQuery('.b2s-edit-template-post-format').val();

        if(postFormat == 3){
            template= stripTags(template);
            template= template.replace(/(\r\n|\n|\r)/gm, '');
            template = template.substring(0, 125);
            template = template + "...";
        }

        if(postFormat ==1){
            template = title;
        }else
        {
            jQuery('.b2s-edit-template-text-preview-tumblr-title').html(title);
        }
        
        jQuery('.b2s-edit-template-text-preview-tumblr-hashtags').html(keywords);

    }
    return template;
}

function stripTags(html) {
  const tmp = document.createElement('div');
  tmp.innerHTML = html;
  return tmp.textContent || tmp.innerText || '';
}

// Mirrors B2S_Util::getExcerpt($text, 0, $limit):
// Normalises HTML first (converts <br>/<p> to newlines, strips remaining tags)
// so that sentence boundaries like "werden." are never masked by trailing HTML.
// Prefers ending at a sentence boundary (. ? !); falls back to last word boundary.
function b2sGetExcerpt(text, limit) {
    // Normalise HTML: block separators become newlines, other tags are stripped
    var clean = text
        .replace(/<br\s*\/?>/gi, '\n')
        .replace(/<\/p>/gi, '\n')
        .replace(/<[^>]+>/g, '')
        .replace(/\n{2,}/g, '\n\n')
        .replace(/[ \t]+/g, ' ')
        .trim();
    if (clean.length <= limit) {
        return clean;
    }
    var parts = clean.split(/([ \t\n\r]+)/);
    var length = 0;
    var lastTaken = 0;
    var partCount = 0;
    for (var i = 0; i < parts.length; i++) {
        length += parts[i].length;
        if (length > limit) {
            break;
        }
        partCount++;
        var lastChar = parts[i].slice(-1);
        if (lastChar === '.' || lastChar === '?' || lastChar === '!') {
            lastTaken = partCount;
        }
    }
    if (lastTaken > 0) {
        return parts.slice(0, lastTaken).join('').trim().replace(/\n/g, '<br>');
    }
    // Fallback: cut at last word boundary within limit
    var sub = clean.substring(0, limit);
    var lastSpace = sub.lastIndexOf(' ');
    return (lastSpace > 0 ? sub.substring(0, lastSpace) : sub).trim().replace(/\n/g, '<br>');
}

jQuery(document).on('click', '.b2s-stop-onboarding', function() {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_user_onboarding',
            'onboarding': 2,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            location.reload();
        }
    });
});

jQuery(document).on('change', '.b2s-toastee-toggle', function () {
    var onboardingPaused = jQuery("#b2s-toastee-paused").val()

    if (jQuery(this).is(':checked')) {
        if(onboardingPaused == 1){
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_save_user_onboarding_paused',
                    'onboarding_paused': 0,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    jQuery("#b2s-toastee-paused").val(0)
                }
            });
        }
        jQuery('.b2s-onboarding-toastee-body').show();
    } else {
        if(onboardingPaused == 0){
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_save_user_onboarding_paused',
                    'onboarding_paused': 1,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    jQuery("#b2s-toastee-paused").val(1)

                }
            });
        }
        jQuery('.b2s-onboarding-toastee-body').hide();

    }
});
