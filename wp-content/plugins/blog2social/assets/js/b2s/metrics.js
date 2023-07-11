jQuery.noConflict();

var filterDates = [];

if (typeof wp.heartbeat !== "undefined") {
    jQuery(document).on('heartbeat-send', function (e, data) {
        data['b2s_heartbeat'] = 'b2s_listener';
        data['b2s_heartbeat_action'] = 'b2s_metrics';
    });
    wp.heartbeat.connectNow();
}

jQuery(window).on("load", function () {
    if(jQuery('#b2sOptionMetricsStarted').val() == '1' && jQuery('#b2sOptionMetricsFeedback').val() == '0') {
        jQuery('.b2s-metrics-feedback-modal').modal('show');
    }   
    if(jQuery('#b2sOptionMetricsStarted').val() == '0') {
        jQuery('.b2s-metrics-starting-modal').modal('show');
    }
    
    var today = new Date();
    var startDate = new Date();
    startDate.setTime(startDate.getTime() - ((24*60*60*1000) * 30));//today -30 days
    
    var dateFormat = 'DD.MM.YYYY';
    if(jQuery('#b2sUserLang').val() == 'en') {
        dateFormat = 'YYYY-MM-DD';
    }
    
    jQuery('#b2s-metrics-date-picker').daterangepicker({
        ranges: {
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()]
        },
        "showCustomRangeLabel": false,
        "alwaysShowCalendars": true,
        "startDate": startDate,
        "endDate": today,
        "maxDate": today,
        "opens": "left",
        "locale": {
            format: dateFormat
        }
    }, function(start, end, label) {
        filterDates = [start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')];
        loadInsights();
    });
    
    loadInsights();
});

function loadInsights() {
    jQuery('.b2s-metrics-area').hide();
    jQuery('.b2s-loading-area').show();
    var filterNetwork = jQuery('.b2s-calendar-filter-network-btn:checked').val();
    jQuery.ajax({
        url: ajaxurl,
        type: "GET",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_load_insights',
            'filter_network': filterNetwork,
            'filter_dates': filterDates,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-metrics-area').show();
            jQuery('.b2s-loading-area').hide();
            if(data.result == true) {
                jQuery('.b2s-sort-result-item-area').html(data.data.posts);
                
                jQuery('.b2s-posts-total-data').html(data.data.general.postCountTotal);
                jQuery('.b2s-impressions-total-data').html(data.data.general.impressionsTotal);
                jQuery('.b2s-engagements-total-data').html(data.data.general.engagementsTotal);
                
                jQuery('.b2s-posts-gain-data').html(data.data.general.postCountToday);
                jQuery('.b2s-impressions-gain-data').html(data.data.general.impressionsToday);
                jQuery('.b2s-engagements-gain-data').html(data.data.general.engagementsToday);
                
                jQuery('#b2s-posts-status').removeClass('glyphicon-arrow-up').removeClass('glyphicon-arrow-down').removeClass('glyphicon-minus');
                jQuery('#b2s-impressions-status').removeClass('glyphicon-arrow-up').removeClass('glyphicon-arrow-down').removeClass('glyphicon-minus');
                jQuery('#b2s-engagements-status').removeClass('glyphicon-arrow-up').removeClass('glyphicon-arrow-down').removeClass('glyphicon-minus');
                
                if(data.data.general.postCountToday == data.data.general.impressionsCompare) {
                    jQuery('#b2s-posts-status').addClass('glyphicon-minus');
                } else if(data.data.general.postCountToday > data.data.general.impressionsCompare) {
                    jQuery('#b2s-posts-status').addClass('glyphicon-arrow-up');
                } else if(data.data.general.postCountToday < data.data.general.impressionsCompare) {
                    jQuery('#b2s-posts-status').addClass('glyphicon-arrow-down');
                }
                
                if(data.data.general.impressionsToday == data.data.general.impressionsCompare) {
                    jQuery('#b2s-impressions-status').addClass('glyphicon-minus');
                } else if(data.data.general.impressionsToday > data.data.general.impressionsCompare) {
                    jQuery('#b2s-impressions-status').addClass('glyphicon-arrow-up');
                } else if(data.data.general.impressionsToday < data.data.general.impressionsCompare) {
                    jQuery('#b2s-impressions-status').addClass('glyphicon-arrow-down');
                }
                
                if(data.data.general.engagementsToday == data.data.general.engagementsCompare) {
                    jQuery('#b2s-engagements-status').addClass('glyphicon-minus');
                } else if(data.data.general.engagementsToday > data.data.general.engagementsCompare) {
                    jQuery('#b2s-engagements-status').addClass('glyphicon-arrow-up');
                } else if(data.data.general.engagementsToday < data.data.general.engagementsCompare) {
                    jQuery('#b2s-engagements-status').addClass('glyphicon-arrow-down');
                }
            }
        }
    });
}

jQuery(document).on('change', '.b2s-calendar-filter-network-btn', function() {
    loadInsights();
});

jQuery(document).on('click', '.b2s-sort-posts', function() {
    jQuery('.b2s-sort-posts').removeClass('btn-primary').addClass('btn-default');
    jQuery(this).addClass('btn-primary').removeClass('btn-default');
    var sortType = jQuery(this).data('sort-type');
    jQuery(".b2s-sort-result-item-area li").sort(sort_posts).appendTo('.b2s-sort-result-item-area');
    function sort_posts(a, b) {
        return (jQuery(b).data(sortType)) < (jQuery(a).data(sortType)) ? -1 : 1;
    }
});

jQuery(document).on('change', '.b2s-filter-active', function() {
    var activeType = jQuery(this).val();
    jQuery(".b2s-sort-result-item-area li").each(function(element) {
        if(activeType == "0") {
            jQuery(this).show();
        }
        if(activeType == "1") {
            if(jQuery(this).data('active') == "1") {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        }
        if(activeType == "2") {
            if(jQuery(this).data('active') == "0") {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        }
    });
});

jQuery(document).on('click', '.b2sGetB2SPostsByWpPost', function () {
    var postId = jQuery(this).attr('data-post-id');
    var showByDate = jQuery(this).attr('data-search-date');
    var filterNetwork = jQuery('.b2s-calendar-filter-network-btn:checked').val();
    if (jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').html() == '') {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_publish_post_data',
                'postId': postId,
                'type': 'metrics',
                'showByDate': showByDate,
                'sharedByUser': jQuery('#b2sSortPostSharedBy').val(),
                'sharedOnNetwork': filterNetwork,
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
            }
        });
    } else {
        if (jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').is(':visible')) {
            jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').hide();
        } else {
            jQuery('.b2s-post-publish-area[data-post-id="' + postId + '"]').show();
        }
    }
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

jQuery(document).on('click', '.checkbox-all', function () {
    if (jQuery('.checkbox-all').is(":checked")) {
        jQuery('.checkboxes[data-blog-post-id="' + jQuery(this).attr('data-blog-post-id') + '"]').prop("checked", true);
    } else {
        jQuery('.checkboxes[data-blog-post-id="' + jQuery('.checkbox-all').attr('data-blog-post-id') + '"]').prop("checked", false);
    }
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

jQuery(document).on('click', '.b2s-metrics-starting-confirm-btn', function () {
    jQuery('.b2s-metrics-starting-modal').modal('hide');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_metrics_starting_confirm',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == false) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                }
            }
            return true;
        }
    });
});

jQuery(document).on('click', '.b2s-metrics-info-btn', function () {
    jQuery('.b2s-metrics-info-modal').modal('show');
});

jQuery(document).on('click', '.b2s-metrics-info-close-btn', function () {
    jQuery('.b2s-metrics-info-modal').modal('hide');
});

jQuery(document).on('click', '.b2s-metrics-legend-info-modal-btn', function () {
    jQuery('.b2s-metrics-legend-info-modal').modal('show');
});

jQuery(document).on('click', '.b2s-metrics-feedback-btn', function () {
    jQuery('.b2s-metrics-feedback-modal').modal('show');
    jQuery('#b2s-metrics-feedback-checkbox').hide();
});

