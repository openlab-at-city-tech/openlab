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
});

function b2sSortFormSubmit() {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-sort-result-area').hide();
    jQuery('.b2s-sort-result-item-area').html("").hide();
    jQuery('.b2s-sort-pagination-area').html("").hide();

    var currentType = jQuery('#b2sType').val();
    if (currentType != "undefined") {
        jQuery('.b2s-post-btn').removeClass('btn-primary').addClass('btn-link');
        jQuery('.b2s-post-' + currentType).removeClass('btn-link').addClass('btn-primary');
    }

    var data = {
        'action': 'b2s_sort_data',
        'b2sSortPostTitle': jQuery('#b2sSortPostTitle').val(),
        'b2sSortPostAuthor': jQuery('#b2sSortPostAuthor').val(),
        'b2sUserAuthId': jQuery('#b2sUserAuthId').val(),
        'b2sPostBlogId': jQuery('#b2sPostBlogId').val(),
        'b2sType': jQuery('#b2sType').val(),
        'b2sShowByDate': jQuery('#b2sShowByDate').val(),
        'b2sPagination': jQuery('#b2sPagination').val(),
        'b2sShowPagination': jQuery('#b2sShowPagination').length > 0 ? jQuery('#b2sShowPagination').val() : 1,
        'b2sUserLang': jQuery('#b2sUserLang').val(),
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
                jQuery('.b2s-sort-pagination-area').html(data.pagination).show();

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

jQuery(document).on('click', '.deleteCcDraftBtn', function () {
    jQuery('#b2s-delete-confirm-post-id').val(jQuery(this).attr('data-blog-post-id'));
    jQuery('.b2s-delete-cc-draft-modal').modal('show');
    jQuery('.b2s-delete-cc-draft-confirm-btn').prop('disabeld', false);

});

jQuery(document).on('click', '.b2s-delete-cc-draft-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-delete-cc-draft-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_cc_draft_post',
            'postId': jQuery('#b2s-delete-confirm-post-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-cc-draft-modal').modal('hide');
            if (data.result == true) {
                jQuery('.b2s-list-cc-draft[data-blog-post-id="' + data.postId + '"').remove();
                /*var count = parseInt(jQuery('.b2s-approve-count[data-post-id="' + data.blogPostId + '"]').html());
                 var newCount = count - data.postCount;
                 jQuery('.b2s-approve-count[data-post-id="' + data.blogPostId + '"]').html(newCount);
                 if (newCount >= 1) {
                 jQuery.each(data.postId, function (i, id) {
                 jQuery('.b2s-post-approve-area-li[data-post-id="' + id + '"]').remove();
                 });
                 } else {
                 jQuery('.b2s-post-approve-area-li[data-post-id="' + data.postId[0] + '"]').closest('ul').closest('li').remove();
                 }*/
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
    jQuery('#b2sSortPostPublishDate').prop('selectedIndex', 0);
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('click', '#b2s-sort-submit-btn', function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    return false;
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

jQuery(document).on('keypress', '#b2sSortPostTitle', function (event) {
    if (event.keyCode == 13) {  //Hide Enter
        return false;
    }
});