jQuery.noConflict();

if (typeof wp.heartbeat !== "undefined") {
    jQuery(document).on('heartbeat-send', function (e, data) {
        data['b2s_heartbeat'] = 'b2s_listener';
    });
    wp.heartbeat.connectNow();
}
jQuery(window).on("load", function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();

    jQuery('#b2s-sched-calendar-area').hide();

    if (jQuery('#b2sType').val() == "draft-post") {
        var dateFormat = "yyyy-mm-dd";
        var language = "en";
        if (jQuery('#b2sUserLang').val() == "de") {
            dateFormat = "dd.mm.yyyy";
            language = "de";
        }
        jQuery("#b2sSortSharedAtDateStart").datepicker({
            format: dateFormat,
            language: language,
            maxViewMode: 2,
            todayHighlight: true,
            calendarWeeks: true,
            autoclose: true
        });

        jQuery("#b2sSortSharedAtDateEnd").datepicker({
            format: dateFormat,
            language: language,
            maxViewMode: 2,
            todayHighlight: true,
            calendarWeeks: true,
            autoclose: true
        });
    }
});


function wopApprove(blogPostId, postId, url, name) {
    var location = encodeURI(window.location.protocol + '//' + window.location.hostname);
    var win = window.open(url + '&location=' + location, name, "width=650,height=900,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
    if (postId > 0) {
        function checkIfWinClosed(intervalID) {
            if (win.closed) {
                clearInterval(intervalID);
                //Show Modal
                jQuery('.b2s-publish-approve-modal').modal('show');
                jQuery('#b2s-approve-post-id').val(postId);
                jQuery('#b2s-approve-blog-post-id').val(blogPostId);
            }
        }
        var interval = setInterval(function () {
            checkIfWinClosed(interval);
        }, 500);
    }
}


jQuery(document).on('click', '.b2s-sched-calendar-btn', function () {
    if (jQuery('#b2s-sched-calendar-area').is(":visible")) {
        jQuery('#b2s-sched-calendar-btn-text').text(jQuery(this).attr('data-show-calendar-btn-title'));
        jQuery('#b2s-sched-calendar-area').hide();
    } else {
        jQuery('#b2s-sched-calendar-btn-text').text(jQuery(this).attr('data-hide-calendar-btn-title'));
        jQuery('#b2s-sched-calendar-area').show();
    }
});

//Overlay second modal
jQuery('#b2s-network-select-image').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
//Overlay second modal
jQuery('#b2s-post-ship-item-post-format-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2s-info-change-meta-tag-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2sImageZoomModal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

jQuery(document).on('click', '.b2sDetailsPublishPostBtn', function () {
    var postId = jQuery(this).attr('data-post-id');
    var showByDate = jQuery(this).attr('data-search-date');
    if (!jQuery(this).find('i').hasClass('isload')) {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_publish_post_data',
                'postId': postId,
                'type': jQuery('#b2sType').val(),
                'showByDate': showByDate,
                'sharedByUser': jQuery('#b2sSortPostSharedBy').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-post-publish-area[data-post-id="' + data.postId + '"]').html(data.content);
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
                wp.heartbeat.connectNow();
            }
        });
        jQuery(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up').addClass('isload').addClass('isShow');
    } else {
        if (jQuery(this).find('i').hasClass('isShow')) {
            jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }
});

jQuery(document).on('click', '.b2sDetailsApprovePostBtn', function () {
    var postId = jQuery(this).attr('data-post-id');
    var showByDate = jQuery(this).attr('data-search-date');
    if (!jQuery(this).find('i').hasClass('isload')) {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_approve_post_data',
                'postId': postId,
                'showByDate': showByDate,
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-post-approve-area[data-post-id="' + data.postId + '"]').html(data.content);
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            }
        });
        jQuery(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up').addClass('isload').addClass('isShow');
    } else {
        if (jQuery(this).find('i').hasClass('isShow')) {
            jQuery('.b2s-post-approve-area[data-post-id="' + postId + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-approve-area[data-post-id="' + postId + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }
});


jQuery(document).on('click', '#b2s-sort-submit-btn', function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    return false;
});


jQuery(document).on('keypress', '#b2sSortPostTitle', function (event) {
    if (event.keyCode == 13) {  //Hide Enter
        return false;
    }
});

jQuery(document).on('click', '.b2s-pagination-btn', function () {
    jQuery('#b2sPagination').val(jQuery(this).attr('data-page'));
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('change', '.b2s-select', function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('click', '#b2s-sort-reset-btn', function () {
    jQuery('#b2sPagination').val("1");
    jQuery('#b2sSortPostTitle').val("");
    jQuery('#b2sSortPostAuthor').prop('selectedIndex', 0);
    jQuery('#b2sSortPostCat').prop('selectedIndex', 0);
    jQuery('#b2sSortPostType').prop('selectedIndex', 0);
    jQuery('#b2sSortPostSchedDate').prop('selectedIndex', 0);
    jQuery('#b2sShowByDate').val("");
    jQuery('#b2sUserAuthId').val("");
    jQuery('#b2sPostBlogId').val("");
    jQuery('#b2sShowByNetwork').val("0");
    jQuery('#b2sSortPostStatus').prop('selectedIndex', 0);
    jQuery('#b2sSortPostShareStatus').prop('selectedIndex', 0);
    jQuery('#b2sSortPostPublishDate').prop('selectedIndex', 0);
    b2sSortFormSubmit();
    return false;
});


function b2sSortFormSubmit(sched_dates) {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-sort-result-area').hide();
    jQuery('.b2s-sort-result-item-area').html("").hide();
    jQuery('.b2s-sort-pagination-content').html("");
    jQuery('.b2s-sort-pagination-area').hide();

    var currentType = jQuery('#b2sType').val();
    if (currentType != "undefined") {
        jQuery('.b2s-post-btn').removeClass('btn-primary').addClass('btn-link');
        jQuery('.b2s-post-' + currentType).removeClass('btn-link').addClass('btn-primary');
    }

    var data = {
        'action': 'b2s_sort_data',
        'b2sSortPostTitle': jQuery('#b2sSortPostTitle').val(),
        'b2sSortPostAuthor': jQuery('#b2sSortPostAuthor').val(),
        'b2sSortPostCat': jQuery('#b2sSortPostCat').val(),
        'b2sSortPostType': jQuery('#b2sSortPostType').val(),
        'b2sSortPostSchedDate': jQuery('#b2sSortPostSchedDate').val(),
        'b2sUserAuthId': jQuery('#b2sUserAuthId').val(),
        'b2sPostBlogId': jQuery('#b2sPostBlogId').val(),
        'b2sType': jQuery('#b2sType').val(),
        'b2sShowByDate': jQuery('#b2sShowByDate').val(),
        'b2sShowByNetwork': jQuery('#b2sShowByNetwork').val(),
        'b2sPagination': jQuery('#b2sPagination').val(),
        'b2sShowPagination': jQuery('#b2sShowPagination').length > 0 ? jQuery('#b2sShowPagination').val() : 1,
        'b2sSortPostStatus': jQuery('#b2sSortPostStatus').val(),
        'b2sSortPostShareStatus': jQuery('#b2sSortPostShareStatus').val(),
        'b2sSortPostPublishDate': jQuery('#b2sSortPostPublishDate').val(),
        'b2sUserLang': jQuery('#b2sUserLang').val(),
        'b2sPostsPerPage': jQuery('#b2sPostsPerPage').val(),
        'b2sSortPostSharedBy': jQuery('#b2sSortPostSharedBy').val(),
        'b2sSortSharedToNetwork': jQuery('#b2sSortSharedToNetwork').val(),
        'b2sSortSharedAtDateStart': jQuery('#b2sSortSharedAtDateStart').val(),
        'b2sSortSharedAtDateEnd': jQuery('#b2sSortSharedAtDateEnd').val(),
        'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
    };

    if (jQuery('#b2sPostsPerPage').length > 0) {
        data['b2sPostsPerPage'] = jQuery('#b2sPostsPerPage').val();
    }

    var legacyMode = true;
    if (jQuery('#isLegacyMode').val() !== undefined) {
        if (jQuery('#isLegacyMode').val() == "1") {
            legacyMode = false; // loading is sync (stack)
        }
    }


    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        async: legacyMode,
        cache: false,
        data: data,
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (typeof data === 'undefined' || data === null) {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            }
            if (data.result == true) {
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-sort-result-area').show();
                jQuery('.b2s-sort-result-item-area').html(data.content).show();
                if(data.pagination != '') {
                    jQuery('.b2s-sort-pagination-content').html(data.pagination);
                    jQuery('.b2s-sort-pagination-area').show();
                }

                //extern - Routing from dashboard
                if (jQuery('#b2sPostBlogId').val() !== undefined) {
                    if (jQuery('#b2sPostBlogId').val() != "") {
                        jQuery('.b2sDetailsSchedPostBtn[data-post-id="' + jQuery('#b2sPostBlogId').val() + '"]').trigger('click');
                    }
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                }
                return false;
            }
        }
    });
}

jQuery(document).on('click', '#b2s-delete-modal-btn', function () {
    jQuery('.b2s-delete-all-modal').modal('show');

});

jQuery(document).on('click', '.b2s-publish-delete-all-confirm-btn', function () {
    jQuery('.b2s-delete-all-modal').modal('hide');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            action: 'b2s_delete_all_posts_older_than',
            b2s_security_nonce: jQuery('#b2s_security_nonce').val(),
            timeframe: jQuery('#b2s-delete-all-posts-select').val()
        },
        error: function (response) {
            return false;
        },
        success: function (data) {

            var url = window.location.href;
            if(url[url.length -1] == "#"){
                url = url.slice(0, url.length - 1)
            } 

            var origin = "&origin=publish_post";
            if(data.result){
                var deletePostStatus = "&deletePostStatus=success";
            } else {
                var deletePostStatus = "&deletePostStatus=failure";
            }
            var deletedPostsNumber = "&deletedPostsNumber="+data.count;

            url = url +origin + deletePostStatus + deletedPostsNumber;
            window.location.assign(url);
            
            return true;
        }
    });


});


jQuery(document).on('click', '.b2sDetailsSchedPostBtn', function () {
    var postId = jQuery(this).attr('data-post-id');
    var showByDate = jQuery(this).attr('data-search-date');
    var showByNetwork = jQuery(this).attr('data-search-network');
    var userAuthId = jQuery('#b2sUserAuthId').val();
    if (!jQuery(this).find('i').hasClass('isload')) {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_sched_post_data',
                'postId': postId,
                'showByDate': showByDate,
                'showByNetwork': showByNetwork,
                'userAuthId': userAuthId,
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-post-sched-area[data-post-id="' + data.postId + '"]').html(data.content);
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            }
        });
        jQuery(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up').addClass('isload').addClass('isShow');
    } else {
        if (jQuery(this).find('i').hasClass('isShow')) {
            jQuery('.b2s-post-sched-area[data-post-id="' + postId + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-sched-area[data-post-id="' + postId + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }

});
jQuery(document).on('click', '.b2sDetailsPublishPostTriggerLink', function () {
    jQuery(this).parent().prev().find('button').trigger('click');
    return false;
});

jQuery(document).on('click', '.b2sDetailsApprovePostTriggerLink', function () {
    jQuery(this).parent().prev().find('button').trigger('click');
    return false;
});

jQuery(document).on('click', '.b2sDetailsSchedPostTriggerLink', function () {
    if (jQuery('#b2s-redirect-url-sched-post').val() == undefined || jQuery('#b2s-redirect-url-sched-post').val() === null) {
        //self page blog2social-sched
        jQuery(this).parent().prev().find('button').trigger('click');
    } else {
        //extern - routing from dashboard
        if (jQuery(this).children('span').attr('data-post-id').length > 0) {
            window.location.href = jQuery('#b2s-redirect-url-sched-post').val() + "&b2sPostBlogId=" + jQuery(this).children('span').attr('data-post-id');
        }
    }
    return false;
});
jQuery(document).on('click', '.checkbox-all', function () {
    if (jQuery('.checkbox-all').is(":checked")) {
        jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]').prop("checked", true);
    } else {
        jQuery('.checkboxes[data-blog-post-id="' + jQuery('.checkbox-all').attr('data-blog-post-id') + '"]').prop("checked", false);
    }
});
jQuery(document).on('click', '.checkbox-post-sched-all-btn', function () {
    var checkboxes = jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]:checked');
    if (checkboxes.length > 0) {
        var items = [];
        jQuery(checkboxes).each(function (i, selected) {
            items[i] = jQuery(selected).val();
        });
        jQuery('#b2s-delete-confirm-post-id').val(items.join());
        jQuery('#b2s-delete-confirm-post-count').html(items.length);
        jQuery('.b2s-delete-sched-modal').modal('show');
        jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', false);
    }
});

jQuery(document).on('click', '.b2s-post-sched-area-drop-btn', function () {
    jQuery('#b2s-delete-confirm-post-id').val(jQuery(this).attr('data-post-id'));
    jQuery('#b2s-delete-confirm-post-count').html('1');
    jQuery('.b2s-delete-sched-modal').modal('show');
    jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', false);
});
jQuery(document).on('click', '.b2s-sched-delete-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_sched_post',
            'postId': jQuery('#b2s-delete-confirm-post-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-sched-modal').modal('hide');
            if (data.result == true) {
                var count = parseInt(jQuery('.b2s-sched-count[data-post-id="' + data.blogPostId + '"]').html());
                var newCount = count - data.postCount;
                jQuery('.b2s-sched-count[data-post-id="' + data.blogPostId + '"]').html(newCount);
                if (newCount >= 1) {
                    jQuery.each(data.postId, function (i, id) {
                        jQuery('.b2s-post-sched-area-li[data-post-id="' + id + '"]').remove();
                    });
                } else {
                    jQuery('.b2s-post-sched-area-li[data-post-id="' + data.postId[0] + '"]').closest('ul').closest('li').remove();
                }
                jQuery('.b2s-post-remove-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-post-remove-fail').show();
            }
            wp.heartbeat.connectNow();
            return true;
        }
    });
});

jQuery(document).on('click', '.checkbox-post-publish-all-btn', function () {
    var checkboxes = jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]:checked');
    if (checkboxes.length > 0) {
        var items = [];
        jQuery(checkboxes).each(function (i, selected) {
            items[i] = jQuery(selected).val();
        });
        jQuery('#b2s-delete-confirm-post-id').val(items.join());
        jQuery('#b2s-delete-confirm-post-count').html(items.length);
        jQuery('.b2s-delete-publish-modal').modal('show');
        jQuery('.b2s-publish-delete-confirm-btn').prop('disabeld', false);
    }
});


jQuery(document).on('click', '.checkbox-post-approve-all-btn', function () {
    var checkboxes = jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]:checked');
    if (checkboxes.length > 0) {
        var items = [];
        jQuery(checkboxes).each(function (i, selected) {
            items[i] = jQuery(selected).val();
        });
        jQuery('#b2s-delete-confirm-post-id').val(items.join());
        jQuery('#b2s-delete-confirm-post-count').html(items.length);
        jQuery('.b2s-delete-approve-modal').modal('show');
        jQuery('.b2s-approve-delete-confirm-btn').prop('disabeld', false);
    }
});

jQuery(document).on('click', '.b2s-approve-publish-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-server-connection-fail').hide();

    var postId = jQuery('#b2s-approve-post-id').val();
    var blogPostId = jQuery('#b2s-approve-blog-post-id').val();
    if (postId > 0) {
        var count = parseInt(jQuery('.b2s-approve-count[data-post-id="' + blogPostId + '"]').html());
        var newCount = count - 1;
        jQuery('.b2s-approve-count[data-post-id="' + blogPostId + '"]').html(newCount);
        if (newCount >= 1) {
            jQuery('.b2s-post-approve-area-li[data-post-id="' + postId + '"]').remove();
        } else {
            jQuery('.b2s-post-approve-area-li[data-post-id="' + postId + '"]').closest('ul').closest('li').remove();
        }
        jQuery('.b2s-publish-approve-modal').modal('hide');
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            cache: false,
            async: false,
            data: {
                'action': 'b2s_update_approve_post',
                'post_id': postId,
                'publish_link': "",
                'publish_error_code': "",
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            success: function (data) {
            }
        });
    }
});


jQuery(document).on('click', '.b2s-approve-delete-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-approve-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_approve_post',
            'postId': jQuery('#b2s-delete-confirm-post-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-approve-modal').modal('hide');
            if (data.result == true) {
                var count = parseInt(jQuery('.b2s-approve-count[data-post-id="' + data.blogPostId + '"]').html());
                var newCount = count - data.postCount;
                jQuery('.b2s-approve-count[data-post-id="' + data.blogPostId + '"]').html(newCount);
                if (newCount >= 1) {
                    jQuery.each(data.postId, function (i, id) {
                        jQuery('.b2s-post-approve-area-li[data-post-id="' + id + '"]').remove();
                    });
                } else {
                    jQuery('.b2s-post-approve-area-li[data-post-id="' + data.postId[0] + '"]').closest('ul').closest('li').remove();
                }
                jQuery('.b2s-post-remove-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-post-remove-fail').show();
            }
            wp.heartbeat.connectNow();
            return true;
        }
    });
});

//Modal Edit Post close
jQuery(document).on('click', '.b2s-modal-close-edit-post', function (e) {
    jQuery(jQuery(this).attr('data-modal-name')).remove();
    return false;
});

jQuery(document).on('click', '.b2s-post-approve-area-drop-btn', function () {
    jQuery('#b2s-delete-confirm-post-id').val(jQuery(this).attr('data-post-id'));
    jQuery('#b2s-delete-confirm-post-count').html('1');
    jQuery('.b2s-delete-approve-modal').modal('show');
    jQuery('.b2s-approve-delete-confirm-btn').prop('disabeld', false);
});

jQuery(document).on('click', '.b2s-post-publish-area-drop-btn', function () {
    jQuery('#b2s-delete-confirm-post-id').val(jQuery(this).attr('data-post-id'));
    jQuery('#b2s-delete-confirm-post-count').html('1');
    jQuery('.b2s-delete-publish-modal').modal('show');
    jQuery('.b2s-publish-delete-confirm-btn').prop('disabeld', false);
});

jQuery(document).on('click', '.b2s-publish-delete-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-publish-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_publish_post',
            'postId': jQuery('#b2s-delete-confirm-post-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-publish-modal').modal('hide');
            if (data.result == true) {
                var count = parseInt(jQuery('.b2s-publish-count[data-post-id="' + data.blogPostId + '"]').html());
                var newCount = count - data.postCount;
                jQuery('.b2s-publish-count[data-post-id="' + data.blogPostId + '"]').html(newCount);
                if (newCount >= 1) {
                    jQuery.each(data.postId, function (i, id) {
                        jQuery('.b2s-post-publish-area-li[data-post-id="' + id + '"]').remove();
                    });
                } else {
                    jQuery('.b2s-post-publish-area-li[data-post-id="' + data.postId[0] + '"]').closest('ul').closest('li').remove();
                }
                jQuery('.b2s-post-remove-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-post-remove-fail').show();
            }
            wp.heartbeat.connectNow();
            return true;
        }
    });
});


jQuery(document).on('click', '.b2s-post-edit-sched-btn', function () {
    showEditSchedPost(jQuery(this).attr('data-b2s-id'), jQuery(this).attr('data-post-id'), jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-network-type'), jQuery(this).attr('data-network-id'), jQuery(this).attr('data-relay-primary-post-id'));

});

//Customize 
function showEditSchedPost(b2s_id, post_id, network_auth_id, network_type, network_id, relay_primary_post_id) {
    if (jQuery('#b2s-edit-event-modal-' + b2s_id).length == 1)
    {
        jQuery('#b2s-edit-event-modal-' + b2s_id).remove();
    }
    jQuery("#b2sPostId").val(post_id);
    var $modal = jQuery("<div>");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_post_edit_modal',
            'id': b2s_id,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                $modal = $modal.html(data);
            }
        }
    });
    b2s_current_post_id = post_id;
    jQuery("body").append($modal);
    jQuery(".b2s-edit-post-delete").hide();
    jQuery('#b2sUserTimeZone').val(jQuery('#user_timezone').val());
    jQuery('#b2s-edit-event-modal-' + b2s_id).modal('show');
    var post_format = jQuery('#b2sCurrentPostFormat').val();
    activatePortal(network_auth_id);
    initSceditor(network_auth_id);
    if (jQuery('.b2s-post-ship-item-post-format-text[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').length > 0) {
        var postFormatText = b2s_post_formats;
        var isSetPostFormat = false;
        var postFormatType = jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').attr('data-post-format-type');
        //is set post format => override current condidtions by user settings for this post
        if (post_format !== null) {
            jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val(post_format);
            jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + network_auth_id + '"]').html(postFormatText[postFormatType][post_format]);
            jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val(post_format);
            //edit modal select post format
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').removeClass('b2s-settings-checked');
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"][data-post-format="' + post_format + '"]').addClass('b2s-settings-checked');
        } else {
            jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + network_auth_id + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val()]);
            jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val());
        }

        //if linkpost then show btn meta tags
        var isMetaChecked = false;
        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
        if (typeof network_id != 'undefined' && jQuery.inArray(network_id.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
            isMetaChecked = true;
        }
        if ((network_id == "2" || network_id == "24") && jQuery('#isCardMetaChecked').val() == "1") {
            isMetaChecked = true;
        }
        if (isMetaChecked && jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val() == "0") {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", false);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", false);
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').show();
            //jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').trigger("click");
            var dataMetaType = jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').attr("data-meta-type");
            if (dataMetaType == "og") {
                jQuery('.b2sChangeOgMeta[data-network-auth-id="' + network_auth_id + '"]').val("1");
            } else {
                jQuery('.b2sChangeCardMeta[data-network-auth-id="' + network_auth_id + '"]').val("1");
            }
        } else {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').hide();
        }

        //Content Curation
        if (jQuery('.b2s-post-ship-item-post-format[data-network-auth-id="' + network_auth_id + '"]').attr('data-post-wp-type') == 'ex') {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-load-info-meta-tag-modal[data-network-auth-id="' + network_auth_id + '"]').attr("style", "display:none !important");
            if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val() == 0) {
                jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + network_auth_id + '"]').hide();
                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + network_auth_id + '"]').hide();
            } else {
                jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + network_auth_id + '"]').show();
                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + network_auth_id + '"]').show();
            }
        }
    }
    var textLimit = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + network_auth_id + '"]').attr('data-network-text-limit');
    if (textLimit != "0") {
        networkLimitAll(network_auth_id, network_id, textLimit);
    } else {
        networkCount(network_auth_id);
    }
    var today = new Date();
    var dateFormat = "yyyy-mm-dd";
    var language = "en";
    var showMeridian = true;
    if (jQuery('#b2sUserLang').val() == "de") {
        dateFormat = "dd.mm.yyyy";
        language = "de";
    }
    if (jQuery('#b2sUserTimeFormat').val() == 0) {
        showMeridian = false;
    }

    jQuery(".b2s-post-item-details-release-input-date").datepicker({
        format: dateFormat,
        language: language,
        maxViewMode: 2,
        todayHighlight: true,
        startDate: today,
        calendarWeeks: true,
        autoclose: true
    });
    jQuery('.b2s-post-item-details-release-input-time').timepicker({
        minuteStep: 15,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current',
        snapToStep: true
    });
    jQuery(".b2s-post-item-details-release-input-date").datepicker().on('changeDate', function (e) {
        checkSchedDateTime(network_auth_id);
    });
    jQuery('.b2s-post-item-details-release-input-time').timepicker().on('changeTime.timepicker', function (e) {
        checkSchedDateTime(network_auth_id);
    });
    init();

    //is relay post?
    if (relay_primary_post_id > 0) {
        jQuery('#b2s-edit-event-modal-' + b2s_id).find("input, textarea, button").each(function () {
            if (!jQuery(this).hasClass('b2s-input-hidden') && !jQuery(this).hasClass('b2s-modal-close') && !jQuery(this).hasClass('b2s-post-item-details-relay-input-delay') && !jQuery(this).hasClass('b2s-edit-post-delete') && !jQuery(this).hasClass('b2s-edit-post-save-this')) {
                jQuery(this).prop("disabled", true);
            }
        });
    }

    if (!b2s_has_premium)
    {
        jQuery('#b2s-edit-event-modal-' + b2s_id).find("input, textarea, button").each(function () {
            if (!jQuery(this).hasClass('b2s-modal-close')) {
                jQuery(this).prop("disabled", true);
            }
        });
    }
}

jQuery(document).on('click', '.b2s-select-image-modal-open', function () {
    jQuery('.b2s-network-select-image-content').html("");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_image_modal',
            'id': jQuery(this).data('post-id'),
            'image_url': jQuery(this).data('image-url'),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                jQuery(".b2s-network-select-image-content").html(data);
            }
        }
    });
    var authId = jQuery(this).data('network-auth-id');
    jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', authId);
    jQuery('.b2s-upload-image').attr('data-network-auth-id', authId);
    var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + authId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + authId + '"]').html();
    jQuery('.b2s-selected-network-for-image-info').html(content);
    jQuery('#b2sInsertImageType').val("0");
    jQuery('.networkImage').each(function () {
        var width = this.naturalWidth;
        var height = this.naturalHeight;
        jQuery(this).parents('.b2s-image-item').find('.b2s-image-item-caption-resolution').html(width + 'x' + height);
    });
    jQuery('#b2s-network-select-image').modal('show');
    return false;
});

jQuery(document).on("click", ".b2s-edit-post-save-this", function (e) {
    e.preventDefault();
    jQuery('#save_method').val("apply-this");
    var id = jQuery(this).data("b2s-id");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery(this).closest("form").serialize() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            }
            jQuery('#b2s-edit-event-modal-' + id).modal('hide');
            jQuery('#b2s-edit-event-modal-' + id).remove();
            jQuery('body').removeClass('modal-open');
            jQuery('body').removeAttr('style');
            if (data.date != "") {
                jQuery('.b2s-post-sched-area-sched-time[data-post-id="' + id + '"]').html(data.date);
            }
            jQuery('.b2s-post-edit-success').show();
            wp.heartbeat.connectNow();
        }
    });
});
jQuery(document).on("click", ".release_locks", function () {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_calendar_release_locks',
            'post_id': jQuery('#post_id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            }
            wp.heartbeat.connectNow();
        }
    });
});



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

function padDate(n) {
    return ("0" + n).slice(-2);
}


function checkSchedDateTime() {
    var dateElement = '#b2s-change-date';
    var timeElement = '#b2s-change-time';
    var dateStr = jQuery(dateElement).val();
    var minStr = jQuery(timeElement).val();
    var timeZone = parseInt(jQuery('#user_timezone').val()) * (-1);

    if (jQuery('#b2sUserLang').val() == 'de') {
        dateStr = dateStr.substring(6, 10) + '-' + dateStr.substring(3, 5) + '-' + dateStr.substring(0, 2);
    } else {
        var minParts = minStr.split(' ');
        var minParts2 = minParts[0].split(':');
        if (minParts[1] == 'PM') {
            minParts2[0] = parseInt(minParts2[0]) + 12;
        }
        minStr = minParts2[0] + ':' + minParts2[1];
    }

    var minParts3 = minStr.split(':');
    if (minParts3[0] < 10) {
        minParts3[0] = '0' + minParts3[0];
    }
    var dateParts = dateStr.split('-');

    //utc current time
    var now = new Date();
    //offset between utc und user
    var offset = (parseInt(now.getTimezoneOffset() / 60)) * (-1);
    //enter hour to user time
    var hour = parseInt(minParts3[0]) + timeZone + offset;
    //calculate datetime in utc
    var enter = new Date(dateParts[0], dateParts[1] - 1, dateParts[2], hour, minParts3[1]);
    //compare enter date time with allowed user time
    if (enter.getTime() < now.getTime()) {
        //enter set on next 15 minutes and calculate on user timezone
        enter.setTime(now.getTime() + (900000 - (now.getTime() % 900000)) - (3600000 * (timeZone + offset)));
        jQuery(dateElement).datepicker('update', enter);
        jQuery(timeElement).timepicker('setTime', enter);
    }
}


window.addEventListener('message', function (e) {
    if (e.origin == jQuery('#b2sServerUrl').val()) {
        var data = JSON.parse(e.data);
        if (data.action == 'approve') {
            var count = parseInt(jQuery('.b2s-approve-count[data-post-id="' + data.blog_post_id + '"]').html());
            var newCount = count - 1;
            jQuery('.b2s-approve-count[data-post-id="' + data.blog_post_id + '"]').html(newCount);
            if (newCount >= 1) {
                jQuery('.b2s-post-approve-area-li[data-post-id="' + data.post_id + '"]').remove();
            } else {
                jQuery('.b2s-post-approve-area-li[data-post-id="' + data.post_id + '"]').closest('ul').closest('li').remove();
            }
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                cache: false,
                async: false,
                data: {
                    'action': 'b2s_update_approve_post',
                    'post_id': data.post_id,
                    'publish_link': data.publish_link,
                    'publish_error_code': data.publish_error_code,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                success: function (data) {

                }
            });
        }
    }
});

jQuery(document).on('click', '.deleteDraftBtn', function () {
    jQuery('#b2s-delete-confirm-draft-id').val(jQuery(this).attr('data-b2s-draft-id'));
    jQuery('.b2s-delete-draft-modal').modal('show');
});

jQuery(document).on('click', '.b2s-draft-delete-confirm-btn', function () {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_draft',
            'draftId': jQuery('#b2s-delete-confirm-draft-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-draft-modal').modal('hide');
            if (data.result == true) {
                jQuery('.b2s-draft-list-entry[data-b2s-draft-id="' + jQuery('#b2s-delete-confirm-draft-id').val() + '"]').remove();
                jQuery('.b2s-post-remove-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-post-remove-fail').show();
            }
            return true;
        }
    });
});

jQuery(document).on('click', '.b2sFavoriteStar', function () {
    jQuery(this).addClass('b2sFavoriteStarLoading');
    var postId = jQuery(this).data('post-id');
    var newStatus = (jQuery(this).data('is-favorite') == "1" ? 0 : 1);
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_change_favorite_status',
            'postId': postId,
            'setStatus': newStatus,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2sFavoriteStar[data-post-id="'+postId+'"]').removeClass('b2sFavoriteStarLoading');
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2sFavoriteStar[data-post-id="'+postId+'"]').data('is-favorite', newStatus);
                if(newStatus == 1){
                    jQuery('.b2sFavoriteStar[data-post-id="'+postId+'"]').removeClass('glyphicon-star-empty');
                    jQuery('.b2sFavoriteStar[data-post-id="'+postId+'"]').addClass('glyphicon-star');
                } else {
                    jQuery('.b2sFavoriteStar[data-post-id="'+postId+'"]').removeClass('glyphicon-star');
                    jQuery('.b2sFavoriteStar[data-post-id="'+postId+'"]').addClass('glyphicon-star-empty');
                }
                if(jQuery('#b2sType').val() == 'favorites') {
                    jQuery('.b2s-favorite-list-entry[data-post-id="'+postId+'"]').remove();
                    if(jQuery('.b2s-favorite-list-entry').length == 0) {
                        jQuery('.b2s-sort-result-item-area').html('<li class="list-group-item"><div class="media"><div class="media-body"></div>'+jQuery('#b2sNoFavoritesText').val()+'</div></li>');
                        jQuery('.b2s-sort-pagination-area').hide();
                    }
                }
            }
            jQuery('.b2sFavoriteStar[data-post-id="'+postId+'"]').removeClass('b2sFavoriteStarLoading');
            return true;
        }
    });
    
});

jQuery(document).on('click', '.b2s-post-per-page', function() {
    jQuery('#b2sPostsPerPage').val(jQuery(this).data('post-per-page'));
    jQuery('.b2s-post-per-page').addClass('btn-default').removeClass('btn-primary');
    jQuery(this).removeClass('btn-default').addClass('btn-primary');
    jQuery('#b2s-sort-submit-btn').trigger('click');
});

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
                var tomorrow = new Date();
                if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                    tomorrow.setTime(jQuery('#b2sBlogPostSchedDate').val());
                }
                tomorrow.setDate(tomorrow.getDate() + 1);
                var tomorrowMonth = ("0" + (tomorrow.getMonth() + 1)).slice(-2);
                var tomorrowDate = ("0" + tomorrow.getDate()).slice(-2);
                var dateTomorrow = tomorrow.getFullYear() + "-" + tomorrowMonth + "-" + tomorrowDate;
                var today = new Date();
                if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                    today.setTime(jQuery('#b2sBlogPostSchedDate').val());
                }

                var todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
                var todayDate = ("0" + today.getDate()).slice(-2);
                var dateToday = today.getFullYear() + "-" + todayMonth + "-" + todayDate;
                var lang = jQuery('#b2sUserLang').val();
                if (lang == "de") {
                    dateTomorrow = tomorrowDate + "." + tomorrowMonth + "." + tomorrow.getFullYear();
                    dateToday = todayDate + "." + todayMonth + "." + today.getFullYear();
                }

                jQuery.each(data.times, function (network_id, time) {
                    if (jQuery('.b2s-post-item[data-network-id="' + network_id + '"]').is(":visible")) {
                        time.forEach(function (network_type_time, count) {
                            if (network_type_time != "") {


                                var hours = network_type_time.substring(0, 2);
                                if (lang == "en") {
                                    var timeparts = network_type_time.split(' ');
                                    hours = (timeparts[1] == 'AM') ? hours : (parseInt(hours) + 12);
                                }
                                if (hours < today.getHours()) {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateTomorrow);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateTomorrow);
                                } else {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateToday);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateToday);
                                }
                                jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').val(network_type_time);
                                jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').timepicker('setTime', network_type_time);

                                count++;
                            }
                        });
                    }
                });
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
    return false;
});



jQuery(document).on('click', '.b2s-get-settings-sched-time-user', function () {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_settings_sched_time_user',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            var tomorrow = new Date();
            if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                tomorrow.setTime(jQuery('#b2sBlogPostSchedDate').val());
            }

            tomorrow.setDate(tomorrow.getDate() + 1);
            var tomorrowMonth = ("0" + (tomorrow.getMonth() + 1)).slice(-2);
            var tomorrowDate = ("0" + tomorrow.getDate()).slice(-2);
            var dateTomorrow = tomorrow.getFullYear() + "-" + tomorrowMonth + "-" + tomorrowDate;
            var today = new Date();
            if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                today.setTime(jQuery('#b2sBlogPostSchedDate').val());
            }

            var todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
            var todayDate = ("0" + today.getDate()).slice(-2);
            var dateToday = today.getFullYear() + "-" + todayMonth + "-" + todayDate;
            var lang = jQuery('#b2sUserLang').val();
            if (lang == "de") {
                dateTomorrow = tomorrowDate + "." + tomorrowMonth + "." + tomorrow.getFullYear();
                dateToday = todayDate + "." + todayMonth + "." + today.getFullYear();
            }
            if (data.result == true) {
                //V5.1.0 seeding
                if (data.type == 'new') {
                    //new
                    jQuery.each(data.times, function (network_auth_id, time) {
                        if (jQuery('.b2s-post-item[data-network-auth-id="' + network_auth_id + '"]').is(":visible")) {
                            //is not set special dates
                            if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + network_auth_id + '"]').val() == '0') {
                                jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + network_auth_id + '"]').val('1').trigger("change");
                            }
                            var hours = time.substring(0, 2);
                            var timeparts = time.split(' ');
                            if (typeof timeparts[1] != 'undefined') {
                                hours = (timeparts[1] == 'AM') ? hours : (parseInt(hours) + 12);
                            }

                            var isDelay = false;
                            var delayDay = data.delay_day[network_auth_id];
                            if (delayDay != undefined) {
                                if (delayDay > 0) {
                                    var delay = new Date();
                                    if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                                        delay.setTime(jQuery('#b2sBlogPostSchedDate').val());
                                    }
                                    delay.setDate(delay.getDate() + parseInt(delayDay));
                                    var delayMonth = ("0" + (delay.getMonth() + 1)).slice(-2);
                                    var delayDate = ("0" + delay.getDate()).slice(-2);
                                    var dateDelay = delay.getFullYear() + "-" + delayMonth + "-" + delayDate;
                                    if (lang == 'de') {
                                        dateDelay = delayDate + '.' + delayMonth + "." + delay.getFullYear();
                                    }
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(dateDelay);
                                    isDelay = true;

                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').datepicker('update', dateDelay);
                                }
                            }
                            if (!isDelay) {
                                if (hours < today.getHours()) {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(dateTomorrow);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').datepicker('update', dateTomorrow);
                                } else {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(dateToday);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').datepicker('update', dateToday);
                                }
                            }
                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(time);
                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').timepicker('setTime', new Date(today.getFullYear(), today.getMonth(), today.getDate(), hours, time.slice(3, 5)));
                        }
                    });
                } else {
                    //old
                    jQuery.each(data.times, function (network_id, time) {
                        if (jQuery('.b2s-post-item[data-network-id="' + network_id + '"]').is(":visible")) {
                            time.forEach(function (network_type_time, count) {
                                if (network_type_time != "") {
                                    var networkAuthId = jQuery(this).attr('data-network-auth-id');
                                    //is not set special dates
                                    if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"]').val() != '1') {
                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"]').val('1').trigger("change");
                                    }
                                    var hours = network_type_time.substring(0, 2);
                                    if (lang == "en") {
                                        var timeparts = network_type_time.split(' ');
                                        hours = (timeparts[1] == 'AM') ? hours : (parseInt(hours) + 12);
                                    }
                                    if (hours < today.getHours()) {
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateTomorrow);
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateTomorrow);
                                    } else {
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateToday);
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateToday);
                                    }
                                    jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(network_type_time);
                                    jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').timepicker('setTime', network_type_time);
                                    count++;
                                }
                            });
                        }
                    });
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                //default load best Times
                //jQuery('.b2s-get-settings-sched-time-default').trigger('click');
                //set current time
                jQuery('.b2s-post-item:visible').each(function () {
                    var networkAuthId = jQuery(this).attr('data-network-auth-id');
                    if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').is(':not(:disabled)')) {
                        //is not set special dates
                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() != '1') {
                            jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val('1').trigger("change");
                        }
                    }
                });
            }
        }
    });
    return false;
});

jQuery(document).on('click', '.b2s-add-multi-image', function() {
    jQuery('.b2s-network-select-image-content').html("");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_image_modal',
            'id': jQuery('#post_id').val(),
            'image_url': jQuery(this).data('image-url'),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                jQuery(".b2s-network-select-image-content").html(data);
            }
        }
    });
    var authId = jQuery(this).data('network-auth-id');
    jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', authId);
    jQuery('.b2s-upload-image').attr('data-network-auth-id', authId);
    var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + authId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + authId + '"]').html();
    jQuery('.b2s-selected-network-for-image-info').html(content);
    jQuery('#b2sInsertImageType').val("0");
    jQuery('.networkImage').each(function () {
        var width = this.naturalWidth;
        var height = this.naturalHeight;
        jQuery(this).parents('.b2s-image-item').find('.b2s-image-item-caption-resolution').html(width + 'x' + height);
    });
    jQuery('#b2s-network-select-image').modal('show');
});

jQuery(document).on('click', '.b2s-repost-multi', function () {
    var checkboxes = jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]:checked');
    if (checkboxes.length > 0) {
        var authIds = [];
        jQuery(checkboxes).each(function (i, selected) {
            authIds[i] = jQuery(selected).attr('data-network-auth-id');
        });
        jQuery(this).attr('href', jQuery(this).attr('href') + '&multi_network_auth_id=' + authIds.join(','))
    } else {
        var checkboxes = jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]');
        if (checkboxes.length > 0) {
            var authIds = [];
            jQuery(checkboxes).each(function (i, selected) {
                authIds[i] = jQuery(selected).attr('data-network-auth-id');
            });
            jQuery(this).attr('href', jQuery(this).attr('href') + '&multi_network_auth_id=' + authIds.join(','))
        }
    }
});