
//Load the "Share new post" area ///////////////////////////////////////////////////////////////////
var b2sComposeLoaded = false;
var b2sComposeInitialUrl = '';

function showTextError(){
 jQuery('.b2s-compose-textarea-wrap').addClass('b2s-curation-text-error');
}

function hideTextError(){
 jQuery('.b2s-compose-textarea-wrap').removeClass('b2s-curation-text-error');
}

jQuery(document).on('click', '#b2s-compose-expand-btn', function () {

    //Tab Navigation
    jQuery('.b2s-wp-posts-section').hide();
    jQuery('#b2s-wordpress-expand-btn').removeClass('share-post-tab-active');
    
    jQuery('#b2s-compose-expand-btn').addClass('share-post-tab-active');
    jQuery('#b2s-compose-expand-area').show();

    b2sSetComposeCollapsed(false);
    b2sLoadShipSettings(function () {
        if (jQuery('#b2sSelSchedDate').val() !== '') {
            var $settingsPanel = jQuery('#b2s-compose-settings-panel');
            if (!$settingsPanel.is(':visible')) {
                jQuery('#b2s-compose-settings-toggle').trigger('click');
            }

            var $shipType = jQuery('#b2s-post-curation-ship-type');
            $shipType.val('1').trigger('change');

            var schedDate = parseInt(jQuery('#b2sSelSchedDate').val(), 10);
            if (!isNaN(schedDate)) {
                jQuery('#b2s-post-curation-ship-date-date').datepicker('update', new Date(schedDate));
            }
        }
        b2sSetupNetworkGroupPreview();
        if (b2sComposeInitialUrl) {
            jQuery('#b2s-compose-main-textarea').val(b2sComposeInitialUrl).trigger('input');
            b2sComposeInitialUrl = '';
        }
    });
});

function b2sSetComposeCollapsed(collapsed) {
    if (collapsed) {
        //jQuery('#b2s-compose-collapsed-bar').show();
        jQuery('#b2s-compose-expand-area').hide();
        jQuery('.b2s-wp-posts-section').addClass('b2s-is-compact');
        jQuery('.b2s-wp-posts-section').hide();
    } else {
        //jQuery('#b2s-compose-collapsed-bar').hide();
        jQuery('#b2s-compose-expand-area').show();
        jQuery('.b2s-wp-posts-section').removeClass('b2s-is-compact');
    }
}

jQuery(document).on('click', '#b2s-wordpress-expand-btn', function () {

    jQuery('#b2s-compose-expand-btn').removeClass('share-post-tab-active');
    jQuery('#b2s-compose-expand-area').hide();

    jQuery('#b2s-wordpress-expand-btn').addClass('share-post-tab-active');
    jQuery('.b2s-wp-posts-section').show();
});



function b2sLoadShipSettings(onReady) {
    if (jQuery('.b2s-curation-settings-area').children().length > 0) {
        b2sSetupNetworkGroupPreview();
        if (typeof onReady === 'function') { onReady(); }
        return;
    }
    showLoadingArea();
    jQuery.ajax({
        url:      window.ajaxurl || ajaxurl,
        type:     'GET',
        dataType: 'json',
        cache:    false,
        data: {
            'action':            'b2s_get_curation_ship_details',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            hideLoadingArea();
            // Another request may have already populated the area
            if (jQuery('.b2s-curation-settings-area').children().length > 0) {
                b2sSetupNetworkGroupPreview();
                if (typeof onReady === 'function') { onReady(); }
                return;
            }
            if (data.result === true) {
                b2sCurationNoAuth = false;
                jQuery('.b2s-curation-settings-area').html(data.settings).show();
                var $twitterArea = jQuery('.b2s-curation-settings-area').find('.b2s-curation-twitter-area');
                if ($twitterArea.length) {
                    $twitterArea.insertBefore('.b2s-preview-network-info-link');
                }
                var $sel = jQuery('#b2s-post-curation-profile-select');
                if ($sel.find('[value="0"]').length) {
                    $sel.find('[value="0"]').prop('selected', true).trigger('change');
                } else {
                    $sel.trigger('change');
                }
                b2sSetupNetworkGroupPreview();
                jQuery('#b2s-btn-curation-share').prop('disabled', false);
                jQuery('#b2s-btn-curation-customize').prop('disabled', false);
                if (typeof onReady === 'function') { onReady(); }
            } else if (data.error === 'NO_AUTH') {
                b2sCurationNoAuth = true;
                jQuery('.b2s-curation-settings-area').show();
                jQuery('#b2s-btn-curation-share').prop('disabled', true);
                jQuery('#b2s-btn-curation-customize').prop('disabled', true);
                jQuery('#b2s-curation-no-auth-preview').show();
                jQuery('#b2s-curation-no-auth-info').show();
                jQuery('#b2s-curation-network-group-wrap').hide();
                if (typeof onReady === 'function') { onReady(); }
            }
        },
        error: function () {
            hideLoadingArea();
            jQuery('.b2s-server-connection-fail').show();
        }
    });
}

function b2sSetupNetworkGroupPreview() {
    var $origSelect = jQuery('#b2s-post-curation-profile-select');
    if ($origSelect.length === 0) { return; }
    jQuery('#b2s-ship-network-col').hide();
    var currentVal     = $origSelect.val();
    var $previewSelect = jQuery('#b2s-curation-preview-profile-select');
    $previewSelect.empty();
    $origSelect.find('option').each(function () {
        $previewSelect.append(jQuery(this).clone());
    });
    $previewSelect.val(currentVal);
    if (!b2sCurationNoAuth) {
        jQuery('#b2s-curation-network-group-wrap').show();
        jQuery('#b2s-curation-no-auth-preview').hide();
    }
}

//Tool:Assistini / Rewrite compose text ///////////////////////////////////////////////////////////////////
jQuery(document).on('click', '.b2s-post-item-ass-create-btn', function () {
    var $btn      = jQuery(this);
    var $resetBtn = jQuery('.b2s-post-item-ass-reset-btn');
    var $textarea = jQuery('#b2s-compose-main-textarea');
    var $loader   = jQuery('.b2s-curation-ass-loader');

    jQuery('#b2s-curation-ass-original-message').val(btoa(encodeURIComponent($textarea.val())));

    jQuery.ajax({
        url: window.ajaxurl || ajaxurl,
        type: 'POST',
        cache: false,
        data: {
            'action':             'b2s_ass_generate_text_sm',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val(),
            'post_lang':          jQuery('#b2s-curation-user-lang').val(),
            'post_id':            0,
            'network_id':         0,
            'network_type':       0,
            'post_network_name':  'generic',
            'input_text':         $textarea.val(),
            'curation': 1
        },
        beforeSend: function () {
            $textarea.prop('disabled', true);
            $btn.prop('disabled', true);
            $resetBtn.prop('disabled', true);
            $loader.show();
        },
        complete: function () {
            $textarea.prop('disabled', false);
            $btn.prop('disabled', false);
            $resetBtn.prop('disabled', false);
            $loader.hide();
        },
        success: function (data) {
            var response = JSON.parse(data);
            if (response.error) {
                b2sCurationShowAssError(response.error);
            } else if (response.result === true && response.ass_text) {
                $textarea.val(response.ass_text).trigger('input');
                jQuery('#sidebar_ship_ass_words_open').text(response.ass_words_open);
                jQuery('#sidebar_ship_ass_words_total').text(response.ass_words_total);
            } else {
                b2sCurationShowAssError('default');
            }

        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
        }
    });
});

jQuery(document).on('click', '.b2s-post-item-ass-reset-btn', function () {
    var original = jQuery('#b2s-curation-ass-original-message').val();
    if (!original) { return; }
    jQuery('#b2s-compose-main-textarea').val(decodeURIComponent(atob(original))).trigger('input');
});

function b2sCurationShowAssError(code) {
    if (code === 'nonce') {
        jQuery('.b2s-nonce-check-fail').show();
        return;
    }
    if (jQuery('.b2sAssErrorModal-' + code).length < 1) {
        code = 'default';
    }
    jQuery('.b2sAssErrorModal-body').hide();
    jQuery('.b2sAssErrorModal-' + code).show();
    jQuery('#b2sAssErrorModal').modal('show');
}

//Update the Preview ///////////////////////////////////////////////////////////////////

jQuery(document).on('keyup input', '#b2s-compose-main-textarea', function () {
    var text     = jQuery(this).val();
    if (text.length) { hideTextError(); }
    var $preview = jQuery('#b2s-curation-preview-body-text');
    if (text.length) {
        var escaped = jQuery('<div>').text(text).html()
            .replace(/\n/g, '<br>')
            .replace(/(https?:\/\/[^\s<>"'\]]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
        if (jQuery('#b2s-curation-post-format').val() === '1') {
            var curImg = jQuery('.b2s-post-item-details-url-image').attr('src');
            if (curImg && !curImg.endsWith('blog2social/assets/images/no-image.png')) {
                escaped += '<br><br><img class="b2s-curation-preview-image" src="' + curImg + '" style="width:100%;">';
            }
        }
        $preview.html(escaped);
    } else {
        $preview.html('');
    }
});

jQuery(document).on('keyup input', '#b2s-compose-main-title', function () {
    jQuery('.b2-preview-post-title').text(jQuery(this).val());
});


//Upload image ///////////////////////////////////////////////////////////////////
var b2sCurationNoAuth          = false;
var b2sCurationLastUrl         = '';
var b2sCurationScrapeTriggered = false;
var b2sCurationInputDebounce   = null;
var b2sCurationActiveScrapeUrl = '';
var b2sCurationManualImage = {
    active: false, attachmentUrl: null, attachmentId: 0,
    prevSrc: null, prevImageInputVal: null
};

function b2sActivateManualImage(attachmentUrl) {
    b2sCurationManualImage.active            = true;
    b2sCurationManualImage.attachmentUrl     = attachmentUrl;
    b2sCurationManualImage.prevSrc           = jQuery('.b2s-curation-link-preview-image').attr('src') || '';
    b2sCurationManualImage.prevImageInputVal = jQuery('#b2s-post-curation-image-url').val() || '';

    b2sShowBadge('image');
    jQuery('.b2s-curation-link-preview-image').attr('src', attachmentUrl).show();
    jQuery('.b2s-curation-link-preview-title, .b2s-curation-link-preview-description').hide();
    jQuery('.b2s-curation-link-preview').show();
    jQuery('#b2s-post-curation-image-url').val(attachmentUrl);
    jQuery('#b2s-curation-post-format').val('1');
    jQuery('.b2s-image-url-hidden-field').val(attachmentUrl);
    jQuery('.b2s-image-id-hidden-field').val(b2sCurationManualImage.attachmentId);
    jQuery('#b2s-preview-image-remove-btn').show();
    jQuery('#b2s-curation-ai-image-tag-btn').show();
}

function b2sDeactivateManualImage() {
    if (!b2sCurationManualImage.active) { return; }
    var prevSrc      = b2sCurationManualImage.prevSrc;
    var prevInputVal = b2sCurationManualImage.prevImageInputVal;
    var wasScraped   = b2sCurationScrapeTriggered;

    b2sCurationManualImage.active            = false;
    b2sCurationManualImage.attachmentUrl     = null;
    b2sCurationManualImage.attachmentId      = 0;
    b2sCurationManualImage.prevSrc           = null;
    b2sCurationManualImage.prevImageInputVal = null;

    jQuery('#b2s-preview-image-remove-btn').hide();
    jQuery('#b2s-curation-ai-image-tag-btn').hide().removeClass('ai-tag-active b2s-ai-generated-tag-btn-inactive');
    jQuery('#b2s-curation-ai-image-tag-hidden').val('0');
    jQuery('#b2s-post-curation-image-url').val("");

    if (b2sCurationLastUrl) {
        if (wasScraped) {
            var noImgSrc = jQuery('#b2sDefaultNoImage').val() || '';
            if (prevSrc && prevSrc !== noImgSrc) {
                jQuery('.b2s-curation-link-preview-image').attr('src', prevSrc).show();
            } else {
                jQuery('.b2s-curation-link-preview-image').hide();
            }
            jQuery('#b2s-post-curation-image-url').val(prevInputVal);
            jQuery('.b2s-curation-link-preview-title, .b2s-curation-link-preview-description').show();
            jQuery('.b2s-curation-link-preview').show();
            jQuery('#b2s-curation-post-format').val('0');
            jQuery('.b2s-image-url-hidden-field').val('');
            jQuery('.b2s-image-id-hidden-field').val('');
            b2sShowBadge('link');
        } else {
            jQuery('.b2s-image-url-hidden-field').val('');
            jQuery('.b2s-image-id-hidden-field').val('');
            b2sCurationScrapeTriggered = false;
            b2sTriggerLinkMode(b2sCurationLastUrl, jQuery('#b2s-compose-main-textarea').val());
        }
    } else {
        jQuery('.b2s-image-url-hidden-field').val('');
        jQuery('.b2s-image-id-hidden-field').val('');
        jQuery('.b2s-curation-link-preview').hide();
        b2sTriggerTextMode(jQuery('#b2s-compose-main-textarea').val());
    }
}

function b2sOpenImagePicker(uploadMode) {
    if (typeof wp === 'undefined' || !wp.media) { return; }
    var headline = jQuery('#b2s_wp_media_headline').val()
        || (uploadMode ? 'Upload image' : 'Select image');
    var btnText  = jQuery('#b2s_wp_media_btn').val() || 'Use this image';
    var picker   = wp.media({
        title:   headline,
        button:  { text: btnText },
        library: { type: 'image' },
        multiple: false
    });
    if (uploadMode) {
        picker.on('open', function () {
            try { picker.frame.content.mode('upload'); } catch (e) {}
        });
    }
    picker.on('select', function () {
        var att = picker.state().get('selection').first().toJSON();
        if (att && att.url) {
            b2sCurationManualImage.attachmentId = att.id || 0;
            b2sActivateManualImage(att.url);
        }
    });
    picker.open();
}


jQuery(document).on('click', '.b2s-compose-direct-upload-btn', function () {
    b2sOpenImagePicker(true);
});


jQuery(document).on('click', '#b2s-preview-image-remove-btn', function () {
    b2sDeactivateManualImage();
});

jQuery(document).on('click', '#b2s-curation-ai-image-tag-btn', function () {
    var $btn = jQuery(this);
    var $inp = jQuery('#b2s-curation-ai-image-tag-hidden');
    var active = $inp.val() === '1';
    $inp.val(active ? '0' : '1');
    $btn.toggleClass('ai-tag-active b2s-ai-generated-tag-btn-inactive');
});

jQuery(document).on('click', '#b2s-curation-ai-text-tag-btn', function () {
    var $btn = jQuery(this);
    var $inp = jQuery('#b2s-curation-ai-text-tag-hidden');
    var active = $inp.val() === '1';
    $inp.val(active ? '0' : '1');
    $btn.toggleClass('ai-tag-active b2s-ai-generated-tag-btn-inactive');
});


//Check for Post Type and Show Badge ///////////////////////////////////////////////////////////////////
jQuery(document).on('input paste', '#b2s-compose-main-textarea', function () {

    var $el = jQuery(this);
    clearTimeout(b2sCurationInputDebounce);
    b2sCurationInputDebounce = setTimeout(function () {
        b2sProcessInput($el.val());
    }, 600);

});


function b2sShowBadge(type) {
    jQuery('.b2s-badge-link-detected, .b2s-badge-image-detected, .b2s-badge-text-detected').hide();
    if (!type) {
        jQuery('#b2s-compose-link-status').hide();
        return;
    }
    jQuery('#b2s-compose-link-status').show();
    if (type === 'link')  { jQuery('.b2s-badge-link-detected').show(); }
    if (type === 'image') { jQuery('.b2s-badge-image-detected').show(); }
    if (type === 'text')  { jQuery('.b2s-badge-text-detected').show(); }
}

function b2sProcessInput(text) {
    var b2sCurationUrlRegex    = /https?:\/\/[^\s"'<>\]]+/gi;
    var urls = text.match(b2sCurationUrlRegex);
    if (urls && urls.length > 0) {
        var url = urls[0];
        if (url !== b2sCurationLastUrl) {
            b2sCurationLastUrl = url;
            b2sCurationScrapeTriggered = false;
            if (!b2sCurationManualImage.active) {
                b2sTriggerLinkMode(url, text);
            }
        }
    } else {
        if (b2sCurationLastUrl) {
            b2sCurationLastUrl = '';
            b2sCurationScrapeTriggered = false;
            if (!b2sCurationManualImage.active) {
                jQuery('.b2s-curation-link-preview').hide();
            }
            if (b2sCurationManualImage.active) {
                b2sShowBadge('image');
            } else {
                b2sShowBadge(null);
                if (typeof activateText === 'function') { activateText(); }
            }
        } else if (!b2sCurationManualImage.active) {
            b2sTriggerTextMode(text);
        }
    }
}

function b2sTriggerLinkMode(url, fullText) {
    b2sShowBadge('link');
    jQuery('#b2s-compose-og-status').hide();
    jQuery('#b2s-curation-post-format').val('0');
    if (!b2sCurationScrapeTriggered) {
        b2sCurationScrapeTriggered = true;
        jQuery('#b2s-curation-input-url').val(url);
        if (typeof activateLink === 'function') { activateLink(); }
        showPreviewLoadingArea();
        scrapeDetails(url);
    }
}

function b2sTriggerTextMode(text) {
    //Remove AI Tags
    jQuery('#b2s-curation-ai-image-tag-btn').hide().removeClass('ai-tag-active b2s-ai-generated-tag-btn-inactive');
    jQuery('#b2s-curation-ai-image-tag-hidden').val('0');
    
    jQuery('.b2s-curation-link-preview').hide();
    jQuery('#b2s-curation-post-format').val('2');
    if (text.trim().length === 0) {
        b2sShowBadge(null);
        return;
    }
    b2sShowBadge('text');
    jQuery('#b2s-compose-og-status').hide();
    if (typeof activateText === 'function') { activateText(); }
}

////// Extra Settings ///////////////////////////////////////////////////////////////////////

/* Settings panel toggle */
jQuery(document).on('click', '#b2s-compose-settings-toggle', function () {
    var $panel = jQuery('#b2s-compose-settings-panel');
    var $btn   = jQuery(this);
    if ($panel.is(':visible')) {
        $panel.slideUp(180);
        $btn.removeClass('is-open');
    } else {
        $panel.slideDown(180);
        $btn.addClass('is-open');
    }
});

// open/close toggle
jQuery(document).on('click', '#b2s-curation-preview-profile-select', function () {
    jQuery("#b2s-curation-network-group-wrap").toggleClass('is-open');
});

// close when clicking outside
jQuery(document).on('click', function (e) {
    const $wrap = jQuery("#b2s-curation-network-group-wrap");
    const $select = jQuery("#b2s-curation-preview-profile-select");

    if (
        !$wrap.is(e.target) &&
        $wrap.has(e.target).length === 0 &&
        !$select.is(e.target) &&
        $select.has(e.target).length === 0
    ) {
        $wrap.removeClass('is-open');
    }
});

//Link Scraping /////////////////////////////////////////////////////////////

function scrapeDetails(url) {
    b2sCurationActiveScrapeUrl = url;
    jQuery('.b2s-curation-settings-area').hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('.b2s-no-permission').hide();
    jQuery('#b2s-curation-no-review-info').hide();
    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-btn-curation-share').prop('disabled', true);
    jQuery('#b2s-btn-curation-customize').prop('disabled', true);

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        async: true,
        cache: true,
        data: {
            'url': url,
            'action': 'b2s_scrape_url',
            'loadSettings': false,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            hidePreviewLoadingArea();
            jQuery('#b2s-btn-curation-customize').prop('disabled', true);
            jQuery('#b2s-btn-curation-share').prop('disabled', true);
            return false;
        },
        success: function (data) {
            if (url !== b2sCurationActiveScrapeUrl) { return; }
            hidePreviewLoadingArea();
            if (data.result == true) {
                jQuery('#b2s-curation-preview').show();
                jQuery('.b2s-curation-settings-area').show();

                var ogdata = JSON.parse(data.ogdata);
                var noImg  = jQuery('#b2sDefaultNoImage').val() || '';

                //No link preview available, switch to text mode
                if(ogdata== false){
                    b2sTriggerTextMode(jQuery('#b2s-compose-main-textarea').val());
                    jQuery('.b2s-curation-link-preview').hide();
                    jQuery('#b2s-btn-curation-customize').prop('disabled', false);
                    jQuery('#b2s-btn-curation-share').prop('disabled', false);
                    jQuery('#b2s-post-curation-url').val('');
                    jQuery('#b2s-post-curation-image-url').val('');
                    return;
                }else{
                    jQuery('.b2s-curation-link-preview').show();
                }

                if (!ogdata.og_image || ogdata.og_image === 'undefined') { ogdata.og_image = noImg; }
                if (!ogdata.image_alt_text || ogdata.image_alt_text === 'undefined') { ogdata.image_alt_text = ''; }
                if (!ogdata.og_title || ogdata.og_title === 'undefined') { ogdata.og_title = ''; }
                if (!ogdata.default_description || ogdata.default_description === 'undefined') { ogdata.default_description = ''; }

                jQuery('.b2s-curation-link-preview-image').attr('alt', ogdata.image_alt_text);
                jQuery('.b2s-curation-link-preview-title').html(ogdata.og_title);
                jQuery('.b2s-curation-link-preview-description').html(ogdata.default_description);

                var noImg = jQuery('#b2sDefaultNoImage').val() || '';
                if (ogdata.og_image && ogdata.og_image !== noImg) {
                    jQuery('.b2s-curation-link-preview-image').attr('src', ogdata.og_image).show();
                    jQuery('#b2s-post-curation-image-url').val(ogdata.og_image);
                    jQuery('#b2s-curation-ai-image-tag-btn').show();
                } else {
                    jQuery('.b2s-curation-link-preview-image').hide();
                    jQuery('#b2s-post-curation-image-url').val('');
                    jQuery('#b2s-curation-ai-image-tag-btn').hide().removeClass('ai-tag-active b2s-ai-generated-tag-btn-inactive');
                    jQuery('#b2s-curation-ai-image-tag-hidden').val('0');
                }
                if (ogdata.og_title || ogdata.default_description) {
                    jQuery('.b2s-curation-link-preview-title, .b2s-curation-link-preview-description').show();
                }
                jQuery('.b2s-curation-link-preview').show();

                // Pre-fill title compose input with OG title if user hasn't typed one
                if (ogdata.og_title && jQuery('#b2s-compose-main-title').val() === '') {
                    jQuery('#b2s-compose-main-title').val(ogdata.og_title);
                    jQuery('.b2-preview-post-title').text(ogdata.og_title);
                }

                jQuery('#b2s-btn-curation-customize').prop('disabled', false);
                jQuery('#b2s-btn-curation-share').prop('disabled', false);

                var urlParam = new URL(window.location.href);
                var postId = urlParam.searchParams.get('postId');
                if (postId) { jQuery('#b2s-draft-id').val(postId); }
                var titleParam = urlParam.searchParams.get('title');
                if (titleParam && jQuery('#b2s-compose-main-title').val() === '') {
                    jQuery('#b2s-compose-main-title').val(titleParam);
                    jQuery('.b2-preview-post-title').text(titleParam);
                }
                loadDraftShipData();
            } else {
                if (data.error === 'nonce') { jQuery('.b2s-nonce-check-fail').show(); }
                
                if (data.error === 'NO_PREVIEW') {
                    jQuery('.b2s-curation-settings-area').hide();
                    jQuery('#b2s-curation-no-review-info').show();
                }
                if (data.error === 'NO_AUTH') {
                    jQuery('.b2s-curation-settings-area').hide();
                    jQuery('#b2s-curation-no-auth-info').show();
                }
                jQuery('#b2s-btn-curation-customize').prop('disabled', true);
                jQuery('#b2s-btn-curation-share').prop('disabled', true);
            }
        }
    });
    return false;
}

function loadDraftShipData() {
    var urlParam       = new URL(window.location.href);
    var ship_type      = urlParam.searchParams.get('ship_type');
    var ship_date      = urlParam.searchParams.get('ship_date');
    var profile_select = urlParam.searchParams.get('profile_select');
    var twitter_select = urlParam.searchParams.get('twitter_select');
    if (ship_type && ship_type > 0) {
        jQuery('#b2s-post-curation-ship-type').val(ship_type).trigger('change');
        if (ship_date) {
            jQuery('#b2s-post-curation-ship-date').val(ship_date);
            var spaceIdx = ship_date.indexOf(' ');
            if (spaceIdx > -1) {
                jQuery('#b2s-post-curation-ship-date-date').datepicker('update', ship_date.substring(0, spaceIdx));
                jQuery('#b2s-post-curation-ship-date-time').val(ship_date.substring(spaceIdx + 1));
            }
        }
    }
    if (profile_select) {
        jQuery('#b2s-post-curation-profile-select').val(profile_select).trigger('change');
        if (twitter_select && twitter_select > 0) {
            jQuery('#b2s-post-curation-twitter-select').val(twitter_select).trigger('change');
        }
    }
}

// Syncing form fields from outside the form div //////////////////////////////////////////////////////////////


jQuery(document).on('change', '#b2s-curation-preview-profile-select', function () {

    //Sync original select because, the selector in preview is outside the form
    var $origSelect = jQuery('#b2s-post-curation-profile-select');
    $origSelect.val(jQuery(this).val());

    var tos = false;
    if (jQuery('#b2s-post-curation-profile-data' + jQuery(this).val()).val() == "") {
        jQuery('#b2s-curation-no-auth-info').show();
        jQuery('.b2s-no-permission').hide();
        tos = true;
    } else {
        jQuery('#b2s-curation-no-auth-info').hide();
        jQuery('.b2s-no-permission').hide();
        //TOS Twitter Check
        var len = jQuery('#b2s-post-curation-twitter-select').children('option[data-mandant-id="' + jQuery(this).val() + '"]').length;
        if (len >= 1) {
            jQuery('.b2s-curation-twitter-area').show();
            jQuery('#b2s-post-curation-twitter-select').prop('disabled', false);
            jQuery('#b2s-post-curation-twitter-select').show();
            jQuery('#b2s-post-curation-twitter-select option').attr("disabled", "disabled");
            jQuery('#b2s-post-curation-twitter-select option[data-mandant-id="' + jQuery(this).val() + '"]').attr("disabled", false);
            jQuery('#b2s-post-curation-twitter-select option[data-mandant-id="' + jQuery(this).val() + '"]:first').attr("selected", "selected");
        } else {
            tos = true;
        }

    }
    //TOS Twitter 032018
    if (tos) {
        jQuery('.b2s-curation-twitter-area').hide();
        jQuery('#b2s-post-curation-twitter-select').prop('disabled', 'disabled');
        jQuery('#b2s-post-curation-twitter-select').hide();
    }

   
});



//Shipping /////////////////////////////////////////////////////////////

jQuery(document).on('click', '#b2s-btn-curation-share', function () {

    var form = jQuery('#b2s-curation-post-form');

    if (!form.valid()) {
        return false;
    }

    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('.b2s-no-permission').hide();
    jQuery('#b2s-curation-saved-draft-info').hide();
    jQuery("#b2s-instant-sharing-optional").hide();
    jQuery('.b2s-post-curation-action').val('b2s_curation_share');

    //Licence Condition
    if (typeof jQuery('#current_licence_open_daily_post_quota') != "undefined" && typeof jQuery('#current_licence_open_sched_post_quota') != "undefined") {
        var dailyLimit = jQuery('#current_licence_open_daily_post_quota').val();
        var schedLimit = jQuery('#current_licence_open_sched_post_quota').html();

        jQuery('.licence-condition-daily-modal-title').hide();
        jQuery('.licence-condition-sched-modal-title').hide();

        //direct share
        if (jQuery('#b2s-post-curation-ship-type').val() == 0 && dailyLimit <= 0) {
            jQuery('.licence-condition-daily-modal-title').show();
            jQuery('.b2s-licence-condition-modal').modal('show');
            return false;
        }
        //sched share
        if (jQuery('#b2s-post-curation-ship-type').val() == 1 && schedLimit <= 0) {
            jQuery('.licence-condition-sched-modal-title').show();
            jQuery('.b2s-licence-condition-modal').modal('show');
            return false;
        }
    }
    var b2sComposeTxt = jQuery('#b2s-compose-main-textarea').val();
    jQuery('[name="comment"]').val(b2sComposeTxt);
    jQuery('#b2s-error-image-empty').hide();
    hideTextError();
    var noContent = false;
    if (jQuery('#b2s-curation-post-format').val() == '1') {
        if ((jQuery('.b2s-image-url-hidden-field').val() || '').length === 0) {
            jQuery('#b2s-error-image-empty').show();
            noContent = true;
        }
    }
    if (b2sComposeTxt.length === 0) {
        showTextError();
        noContent = true;
    }
    if (noContent) {
        return false;
    }

    jQuery('.b2s-curation-post-list').html("");
    jQuery('.b2s-curation-post-list-area').hide();
    showLoadingArea();
    showLoadingArea();
    jQuery('.b2s-curation-settings-area').hide();

    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat=' + jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            hideLoadingArea();
            hideLoadingArea();
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            hideLoadingArea();
            if (data.result == true) {
                hideLoadingArea();
                jQuery('.b2s-curation-settings-area').show();
                jQuery('.b2s-curation-post-list-area').show();
                jQuery('.b2s-curation-post-list').html(data.content);

                //Licence Condition
                jQuery('#current_licence_open_sched_post_quota').html(data.currentOpenSchedLimit);
                jQuery('#current_licence_open_daily_post_quota').val(data.currentOpenDailyLimit);

                if (data.currenOpenDailyLimit <= 0) {
                    jQuery('.b2s-current-licence-open-daily-post-quota-sidebar-info').show();
                }

                //Network Condition
                jQuery('#current_network_open_sched_post_quota').html(data.currentNetwork45OpenSchedLimit);
                jQuery('#current_network_open_daily_post_quota').val(data.currentNetwork45OpenDailyLimit);

                if (data.currentNetwork45OpenDailyLimit <= 0) {
                    jQuery('.b2s-current-network-open-daily-post-quota-sidebar-info').show();
                }

            } else {
                hideLoadingArea();
                jQuery('.b2s-curation-post-list-area').hide();
                jQuery('.b2s-curation-settings-area').show();
                if (jQuery('#b2s-curation-post-format').val() == '0') {
                } else if (jQuery('#b2s-curation-post-format').val() == '1') {
                } else {
                }

                if (data.error == 'NO_AUTH') {
                    jQuery('#b2s-curation-no-auth-info').show();
                    jQuery('.b2s-no-permission').hide();
                } else if (data.error == 'permission_author') {
                    jQuery('.b2s-no-permission-author').show();
                } else if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('#b2s-curation-no-data-info').show();
                }
            }
            wp.heartbeat.connectNow();
        }
    });
    return false;
});


//Curation Save Draft Button ////////////////////////////////////////////////////////////////

jQuery(document).on('click', '#b2s-btn-compose-save-draft', function () {

    var form = jQuery('#b2s-curation-post-form');

    if (!form.valid()) {
        return false;
    }

    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('.b2s-no-permission').hide();
    jQuery('#b2s-curation-saved-draft-info').hide();
    var b2sComposeTxt = jQuery('#b2s-compose-main-textarea').val();
    jQuery('[name="comment"]').val(b2sComposeTxt);
    jQuery('#b2s-error-image-empty').hide();
    hideTextError();
    var noContent = false;
    if (jQuery('#b2s-curation-post-format').val() == '1') {
        if ((jQuery('.b2s-image-url-hidden-field').val() || '').length === 0) {
            jQuery('#b2s-error-image-empty').show();
            noContent = true;
        }
    }
    if (b2sComposeTxt.length === 0) {
        showTextError();
        noContent = true;
    }
    if (noContent) {
        return false;
    }
    jQuery('.b2s-post-curation-action').val('b2s_curation_draft');
    showLoadingArea();
    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat=' + jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            hideLoadingArea();
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            hideLoadingArea();
            if (data.result == true) {
                if (typeof data.postId != 'undefined') {
                    jQuery('#b2s-draft-id').val(data.postId);
                }
                jQuery('#b2s-curation-saved-draft-info').show();
                setTimeout(function () {
                    jQuery('#b2s-curation-saved-draft-info').fadeOut('slow');
                }, 5000);
            } else {
                if (data.error == 'permission_author') {
                    jQuery('.b2s-no-permission-author').show();
                } else if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('#b2s-curation-no-data-info').show();
                }
            }
        }
    });
    return false;
});


//Curation Customize Button ////////////////////////////////////////////////////////////////

jQuery(document).on('click', '#b2s-btn-curation-customize', function () {

    var form = jQuery('#b2s-curation-post-form');

    if (!form.valid()) {
        return false;
    }

    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('.b2s-no-permission').hide();
    jQuery('#b2s-curation-saved-draft-info').hide();
    var b2sComposeTxt = jQuery('#b2s-compose-main-textarea').val();
    jQuery('[name="comment"]').val(b2sComposeTxt);
    jQuery('#b2s-error-image-empty').hide();
    hideTextError();
    var noContent = false;
    if (jQuery('#b2s-curation-post-format').val() == '1') {
        if ((jQuery('.b2s-image-url-hidden-field').val() || '').length === 0) {
            jQuery('#b2s-error-image-empty').show();
            noContent = true;
        }
    }
    if (b2sComposeTxt.length === 0) {
        showTextError();
        noContent = true;
    }
    if (noContent) {
        return false;
    }
    jQuery('.b2s-post-curation-action').val('b2s_curation_customize');
    showLoadingArea();
    jQuery('.b2s-curation-settings-area').hide();
    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat=' + jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                window.location.href = data.redirect;
                return false;
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                hideLoadingArea();
                if (data.error == 'permission_author') {
                    jQuery('.b2s-no-permission-author').show();
                } else {
                    jQuery('#b2s-curation-no-data-info').show();
                }
                jQuery('.b2s-curation-settings-area').show();
                if (jQuery('#b2s-curation-post-format').val() == '0') {
                    jQuery('.b2s-curation-link-area').show();
                } 
            }
        }
    });
    return false;
});

// Show Posts Area ///////////////////////////////////////////////////////////////////

var b2sCurationSearchTimer = null;
jQuery(document).on('input', '#b2sSortPostTitle', function () {
    clearTimeout(b2sCurationSearchTimer);
    b2sCurationSearchTimer = setTimeout(function () {
        if (typeof b2sSortFormSubmit === 'function') { b2sSortFormSubmit(); }
    }, 500);
});

jQuery(document).on('click', '.b2sPostsDetailBtn', function () {
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
                'action': 'get_posts_detail_data',
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
                    jQuery('.b2s-post-details-area[data-post-id="' + data.postId + '"]').html(data.content);
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
            jQuery('.b2s-post-details-area[data-post-id="' + postId + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-details-area[data-post-id="' + postId + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }

});



// Plan Curation ///////////////////////////////////////////////////////////////////

jQuery(document).on('shown.bs.modal', '#b2s-show-post-type-modal', function () {
    if (typeof b2s_is_calendar !== 'undefined') {
        var $panel = jQuery('#b2s-compose-settings-panel');
        if (!$panel.is(':visible')) {
            $panel.slideDown(180);
            jQuery('#b2s-compose-settings-toggle').addClass('is-open');
        }
    }
    b2sLoadShipSettings(function () {
        if (typeof b2s_is_calendar !== 'undefined' && jQuery('#b2sSelSchedDate').val() !== '') {
            var $shipType = jQuery('#b2s-post-curation-ship-type');
            if ($shipType.length) { $shipType.val('1').trigger('change'); }
        }
    });
});

jQuery(document).on('change', '#b2s-post-curation-ship-type', function () {
    if (jQuery(this).val() == 1) {
        if (jQuery(this).attr('data-user-version') == 0) {
            jQuery('#b2sPreFeatureScheduleModal').modal('show');
            jQuery(this).val('0');
            return false;
        }
    }

    if (jQuery(this).val() == 1) {
        jQuery('.b2s-post-curation-ship-date-area').show();
        jQuery('#b2s-post-curation-ship-date').prop('disabled', false);
        jQuery('#b2s-post-curation-ship-date-date').prop('disabled', false);
        jQuery('#b2s-post-curation-ship-date-time').prop('disabled', false);

        var today = new Date();
        if (jQuery('#b2sSelSchedDate').val() != '') {
            today.setTime(jQuery('#b2sSelSchedDate').val());
        }
        today.setTime(Math.ceil(today.getTime() / 900000) * 900000);

        var language = jQuery('#b2s-post-curation-ship-date').attr('data-language');
        var dateFormat = (language == 'de') ? 'dd.mm.yyyy' : 'yyyy-mm-dd';
        var showMeridian = (language != 'de');
        var maxDate = new Date(jQuery('#b2sMaxSchedDate').val());

        var setDateStr = (language == 'de')
            ? padDate(today.getDate()) + '.' + padDate(today.getMonth() + 1) + '.' + today.getFullYear()
            : today.getFullYear() + '-' + padDate(today.getMonth() + 1) + '-' + padDate(today.getDate());
        var setTimeStr = (language == 'de')
            ? padDate(today.getHours()) + ':' + padDate(today.getMinutes())
            : formatAMPM(today);

        jQuery('#b2s-post-curation-ship-date-date').datepicker({
            format: dateFormat,
            language: language,
            maxViewMode: 2,
            todayHighlight: true,
            startDate: today,
            endDate: maxDate,
            calendarWeeks: true,
            autoclose: true
        }).datepicker('update', today);

        jQuery('#b2s-post-curation-ship-date-time').timepicker({
            minuteStep: 15,
            appendWidgetTo: 'body',
            showSeconds: false,
            showMeridian: showMeridian,
            defaultTime: today,
            snapToStep: true
        });

        jQuery('#b2s-post-curation-ship-date-date').off('changeDate.curation').on('changeDate.curation', function () {
            updateCurationShipDate();
        });
        jQuery('#b2s-post-curation-ship-date-time').off('changeTime.timepicker').on('changeTime.timepicker', function () {
            updateCurationShipDate();
        });

        jQuery('#b2s-post-curation-ship-date-date').val(setDateStr);
        jQuery('#b2s-post-curation-ship-date').val(setDateStr + ' ' + setTimeStr);
    } else {
        jQuery('.b2s-post-curation-ship-date-area').hide();
        jQuery('#b2s-post-curation-ship-date').prop('disabled', true);
        jQuery('#b2s-post-curation-ship-date-date').prop('disabled', true);
        jQuery('#b2s-post-curation-ship-date-time').prop('disabled', true);
    }
});

function padDate(n) {
    return ('0' + n).slice(-2);
}

function formatAMPM(date) {
    var hours   = date.getHours();
    var minutes = date.getMinutes();
    var ampm    = hours >= 12 ? 'pm' : 'am';
    hours       = hours % 12;
    hours       = hours ? hours : 12;
    minutes     = minutes < 10 ? '0' + minutes : minutes;
    return hours + ':' + minutes + ' ' + ampm;
}

function updateCurationShipDate() {
    var dateVal = jQuery('#b2s-post-curation-ship-date-date').val();
    var timeVal = jQuery('#b2s-post-curation-ship-date-time').val();
    if (dateVal && timeVal) {
        jQuery('#b2s-post-curation-ship-date').val(dateVal + ' ' + timeVal);
    }
}

/* ---- Mini calendar modal ---- */
(function () {
    var calYear  = new Date().getFullYear();
    var calMonth = new Date().getMonth();
    var calEvents = [];

    var MONTHS = {
        de: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
        en: ['January','February','March','April','May','June','July','August','September','October','November','December']
    };
    var DAYS = {
        de: ['Mo','Di','Mi','Do','Fr','Sa','So'],
        en: ['Mo','Tu','We','Th','Fr','Sa','Su']
    };

    function lang()  { return (jQuery('.b2s-calendar-modal-body').data('language') || 'en').substring(0, 2); }
    function pad(n)  { return n < 10 ? '0' + n : '' + n; }
    function months(){ return MONTHS[lang()] || MONTHS.en; }
    function days()  { return DAYS[lang()]   || DAYS.en;   }

    function loadMonth(y, m) {
        var start   = y + '-' + pad(m + 1) + '-01';
        var lastDay = new Date(y, m + 1, 0).getDate();
        var end     = y + '-' + pad(m + 1) + '-' + pad(lastDay);

        jQuery('#b2s-cal-loading').show();
        jQuery('#b2s-cal-grid').empty();
        jQuery('#b2s-cal-day-detail').hide();
        jQuery('#b2s-cal-title').text(months()[m] + ' ' + y);

        jQuery.ajax({
            url: window.ajaxurl || ajaxurl,
            type: 'GET',
            dataType: 'json',
            data: { action: 'b2s_get_calendar_events', start: start, end: end, b2s_security_nonce: jQuery('#b2s_security_nonce').val() },
            success:  function (data) { calEvents = Array.isArray(data) ? data : []; },
            error:    function ()     { calEvents = []; },
            complete: function ()     { jQuery('#b2s-cal-loading').hide(); renderGrid(y, m); }
        });
    }

    function renderGrid(y, m) {
        var byDay = {};
        calEvents.forEach(function (ev) {
            if (!ev.start) { return; }
            var d = parseInt(ev.start.substring(8, 10), 10);
            if (!byDay[d]) { byDay[d] = []; }
            byDay[d].push(ev);
        });

        var firstWeekday = (new Date(y, m, 1).getDay() + 6) % 7;
        var daysInMonth  = new Date(y, m + 1, 0).getDate();
        var today        = new Date();
        var isThisMonth  = (today.getFullYear() === y && today.getMonth() === m);

        var $tbl  = jQuery('<table class="b2s-cal-table"></table>');
        var $head = jQuery('<thead><tr></tr></thead>');
        days().forEach(function (dn) { $head.find('tr').append('<th class="b2s-cal-th">' + dn + '</th>'); });
        $tbl.append($head);

        var $body   = jQuery('<tbody></tbody>');
        var dayNum  = 1;
        var rows    = Math.ceil((firstWeekday + daysInMonth) / 7);

        for (var r = 0; r < rows; r++) {
            var $tr = jQuery('<tr></tr>');
            for (var c = 0; c < 7; c++) {
                var idx = r * 7 + c;
                if (idx < firstWeekday || dayNum > daysInMonth) {
                    $tr.append('<td class="b2s-cal-td b2s-cal-empty"></td>');
                } else {
                    var d   = dayNum;
                    var evs = byDay[d] || [];
                    var cls = 'b2s-cal-td' +
                              (isThisMonth && d === today.getDate() ? ' b2s-cal-today' : '') +
                              (evs.length ? ' b2s-cal-has-events' : '');
                    var $td = jQuery('<td class="' + cls + '" data-day="' + d + '"></td>');
                    $td.append('<div class="b2s-cal-day-num">' + d + '</div>');
                    if (evs.length) {
                        var $dots = jQuery('<div class="b2s-cal-dots"></div>');
                        for (var i = 0; i < Math.min(evs.length, 3); i++) {
                            $dots.append('<span class="b2s-cal-dot" style="background:' + (evs[i].color || '#6366f1') + '"></span>');
                        }
                        if (evs.length > 3) { $dots.append('<span class="b2s-cal-dot-more">+' + (evs.length - 3) + '</span>'); }
                        $td.append($dots);
                    }
                    $tr.append($td);
                    dayNum++;
                }
            }
            $body.append($tr);
        }
        $tbl.append($body);
        jQuery('#b2s-cal-grid').append($tbl);
    }

    jQuery(document).on('click', '#b2s-cal-grid .b2s-cal-has-events', function () {
        var d   = parseInt(jQuery(this).data('day'), 10);
        var evs = calEvents.filter(function (ev) {
            return ev.start && parseInt(ev.start.substring(8, 10), 10) === d;
        });
        evs.sort(function (a, b) { return (a.start || '').localeCompare(b.start || ''); });

        jQuery('#b2s-cal-day-detail-title').text(pad(d) + '. ' + months()[calMonth] + ' ' + calYear);
        var $ul = jQuery('#b2s-cal-day-detail-list').empty();
        evs.forEach(function (ev) {
            var time    = ev.start ? ev.start.substring(11, 16) : '';
            var title   = jQuery('<span>').text(ev.title   || '').html();
            var profile = jQuery('<span>').text(ev.profile || ev.network_name || '').html();
            var dot     = '<span class="b2s-cal-dot" style="background:' + (ev.color || '#6366f1') + ';vertical-align:middle;"></span>';
            $ul.append('<li class="b2s-cal-day-item">' + dot + ' <strong>' + time + '</strong> ' + title + ' <small class="text-muted">— ' + profile + '</small></li>');
        });
        jQuery('#b2s-cal-day-detail').show();
    });

    jQuery(document).on('click', '#b2s-cal-prev', function () {
        if (--calMonth < 0)  { calMonth = 11; calYear--; }
        loadMonth(calYear, calMonth);
    });
    jQuery(document).on('click', '#b2s-cal-next', function () {
        if (++calMonth > 11) { calMonth = 0;  calYear++; }
        loadMonth(calYear, calMonth);
    });

    jQuery(document).on('click', '.b2s-show-calendar-btn', function () {
        calYear  = new Date().getFullYear();
        calMonth = new Date().getMonth();
        jQuery('#b2s-calendar-modal').modal('show');
        loadMonth(calYear, calMonth);
    });
}());

//Emoji Picker ///////////////////////////////////////////////////////////////////
var emojiTranslation = JSON.parse(jQuery('#b2sEmojiTranslation').val());
var picker = new EmojiButton({
    position: 'auto',
    autoHide: false,
    i18n: {
        search: emojiTranslation['search'],
        categories: {
            recents: emojiTranslation['recents'],
            smileys: emojiTranslation['smileys'],
            animals: emojiTranslation['animals'],
            food: emojiTranslation['food'],
            activities: emojiTranslation['activities'],
            travel: emojiTranslation['travel'],
            objects: emojiTranslation['objects'],
            symbols: emojiTranslation['symbols'],
            flags: emojiTranslation['flags']
        },
        notFound: emojiTranslation['notFound']
    }
});
picker.on('emoji', function (emoji) {
    var text  = jQuery('#b2s-compose-main-textarea').val();
    var start = jQuery('#b2s-compose-main-textarea').attr('selectionStart');
    var end   = jQuery('#b2s-compose-main-textarea').attr('selectionEnd');
    if (typeof start === 'undefined' || typeof end === 'undefined') {
        start = text.length;
        end = text.length;
    }
    var newText = text.slice(0, start) + emoji + text.slice(end);
    jQuery('#b2s-compose-main-textarea').val(newText);
    jQuery('#b2s-compose-main-textarea').focus();
    jQuery('#b2s-compose-main-textarea').prop('selectionStart', parseInt(start) + emoji.length);
    jQuery('#b2s-compose-main-textarea').prop('selectionEnd', parseInt(start) + emoji.length);
    jQuery('#b2s-compose-main-textarea').trigger('input');
});

jQuery(document).on('click', '.b2s-post-item-details-item-message-emoji-btn', function () {
    if (picker.pickerVisible) {
        picker.hidePicker();
    } else {
        currentEmojiNetworkAuthId = jQuery(this).attr('data-network-auth-id');
        currentEmojiNetworkCount = jQuery(this).attr('data-network-count');
        picker.showPicker(jQuery(this));
    }
});

//Call Curation Draft ///////////////////////////////////////////////////////////////////


//Load Draft Button ///////////////////////////////////////////////////////////////////

jQuery(document).on('click', '#b2s-btn-load-draft', function () {
    var $modal   = jQuery('#b2s-load-draft-modal');
    var $loading = jQuery('#b2s-load-draft-loading');
    var $empty   = jQuery('#b2s-load-draft-empty');
    var $list    = jQuery('#b2s-load-draft-list');
    $loading.show();
    $empty.hide();
    $list.hide().empty();
    $modal.modal('show');
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'b2s_get_curation_drafts',
            b2s_security_nonce: jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            $loading.hide();
            if (!data.result || !data.drafts || data.drafts.length === 0) {
                $empty.show();
                return;
            }
            $list.html(data.list_html).show();
        },
        error: function () {
            $loading.hide();
            $empty.show();
        }
    });
});

jQuery(document).on('click', '.b2s-load-draft-item', function (e) {
    if (jQuery(e.target).closest('.b2s-delete-draft-btn').length) { return; }

    var d = jQuery(this).data('draft');

    if (!d) { return; }
    jQuery('#b2s-load-draft-modal').modal('hide');

    var parts = [];
    if (d.message) {
        parts.push(d.message);
    }
    if (d.url && (!d.message || d.message.indexOf(d.url) === -1)) {
        parts.push(d.url);
    }
    if (d.tags && d.tags.length) {
        var tagStr = jQuery.map(d.tags, function (t) {
            return (t.charAt(0) === '#') ? t : '#' + t;
        }).join(' ');
        if (tagStr) { parts.push(tagStr); }
    }

    var text = parts.join('\n\n').trim();
    b2sSetComposeCollapsed(false);
    jQuery('#b2s-compose-main-title').val(d.title || '').trigger('input');

    if (d.post_format == 1 && d.image_url) {
        b2sCurationManualImage.attachmentId = d.image_id || 0;
        b2sActivateManualImage(d.image_url);
        if (d.url) {
            b2sShowBadge('link');
            jQuery('#b2s-curation-post-format').val('0');
        }
    } else {
        // Text draft: clear URL state FIRST so b2sDeactivateManualImage doesn't restore link preview
        b2sCurationLastUrl = '';
        b2sCurationScrapeTriggered = false;
        b2sDeactivateManualImage();
        jQuery('.b2s-curation-link-preview').hide();
        jQuery('.b2s-curation-link-preview-image').hide().attr('src', '');
        jQuery('.b2s-curation-link-preview-title').text('');
        jQuery('.b2s-curation-link-preview-description').text('');
        jQuery('.b2s-image-url-hidden-field').val('');
        jQuery('.b2s-image-id-hidden-field').val('');
        jQuery('#b2s-curation-post-format').val('2');
        b2sShowBadge('text');
     
    }

    var $textarea = jQuery('#b2s-compose-main-textarea');
    $textarea.val(text).trigger('input');

    // Restore apply_post_templates toggle (always in DOM)
    jQuery('#b2s-apply-post-templates-toggle').prop('checked', !!d.apply_post_templates);

    // Restore ship settings (ship_type, profile, schedule) — these live inside the
    // dynamically-loaded settings area, so ensure it is populated first.
    var hasSettings = (d.ship_type > 0 || d.profile_select > 0 || d.apply_post_templates);
    b2sLoadShipSettings(function () {
        if (d.ship_type > 0) {
            jQuery('#b2s-post-curation-ship-type').val(d.ship_type).trigger('change');
            if (d.ship_date) {
                jQuery('#b2s-post-curation-ship-date').val(d.ship_date);
                var spaceIdx = d.ship_date.indexOf(' ');
                if (spaceIdx > -1) {
                    var today = new Date();

                    var schedVal = jQuery('#b2sSelSchedDate').val();
                    if (schedVal != '') {
                        today = new Date(parseInt(schedVal, 10));
                    }

                    // round to next 15 minutes
                    today.setTime(Math.ceil(today.getTime() / 900000) * 900000);

                    var language = jQuery('#b2s-post-curation-ship-date').attr('data-language');
                    var isDE = (language == 'de');

                    var showMeridian = !isDE;

                    var maxDate = new Date(parseInt(jQuery('#b2sMaxSchedDate').val(), 10));

                    function pad(n) {
                        return (n < 10 ? '0' : '') + n;
                    }

                    // DATE formatting
                    var setDateStr = isDE
                        ? pad(today.getDate()) + '.' + pad(today.getMonth() + 1) + '.' + today.getFullYear()
                        : today.getFullYear() + '-' + pad(today.getMonth() + 1) + '-' + pad(today.getDate());

                    // TIME formatting
                    var setTimeStr = showMeridian
                        ? formatAMPM(today)
                        : pad(today.getHours()) + ':' + pad(today.getMinutes());

                    jQuery('#b2s-post-curation-ship-date-date').val(setDateStr);
                    jQuery('#b2s-post-curation-ship-date-time').val(setTimeStr);
                }
            }
        }
        if (d.profile_select > 0) {
            jQuery('#b2s-post-curation-profile-select').val(d.profile_select).trigger('change');
            jQuery('#b2s-curation-preview-profile-select').val(d.profile_select).trigger('change');
        }
        if (d.twitter_select > 0) {
            jQuery('#b2s-post-curation-twitter-select').val(d.twitter_select).trigger('change');
        }
        if (hasSettings) {
            jQuery('#b2s-compose-settings-panel').show();
            jQuery('#b2s-compose-settings-toggle').addClass('is-open');
        }
        if(d.image_is_ai_generated) {
            jQuery('#b2s-curation-ai-image-tag-btn').trigger('click');
        }
        if(d.text_is_ai_generated) {
            jQuery('#b2s-curation-ai-text-tag-btn').trigger('click');
        }
    });
});

jQuery(document).on('click', '.b2s-delete-draft-btn', function (e) {
    e.stopPropagation();
    var draftId = jQuery(this).data('draft-id');
    if (!draftId) { return; }
    var $item = jQuery(this).closest('.b2s-load-draft-item');
    $item.css('opacity', '0.5').find('button').prop('disabled', true);

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'b2s_delete_user_draft',
            b2s_security_nonce: jQuery('#b2s_security_nonce').val(),
            draftId: draftId
        },
        success: function (data) {
            if (data && data.result) {
                $item.fadeOut(200, function () {
                    jQuery(this).remove();
                    if (jQuery('#b2s-load-draft-list li').length === 0) {
                        jQuery('#b2s-load-draft-list').hide();
                        jQuery('#b2s-load-draft-empty').show();
                    }
                });
            } else {
                $item.css('opacity', '1').find('button').prop('disabled', false);
            }
        },
        error: function () {
            $item.css('opacity', '1').find('button').prop('disabled', false);
        }
    });
});

//When coming from navbar of the other pages open the create content
jQuery(window).on('load', function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get('page') === 'blog2social-post' && params.get('opencontent') === '1' ){
        b2sComposeInitialUrl = params.get('url') || '';
        jQuery('#b2s-compose-expand-btn').trigger('click');
    }
});

//Badge Modals
jQuery(document).on('click', '.b2s-badge-link-detected,.b2s-badge-image-detected', function () {
    jQuery('#b2s-posttype-info-modal').modal('show');
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
            jQuery('.b2sFavoriteStar[data-post-id="' + postId + '"]').removeClass('b2sFavoriteStarLoading');
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2sFavoriteStar[data-post-id="' + postId + '"]').data('is-favorite', newStatus);
                if (newStatus == 1) {
                    jQuery('.b2sFavoriteStar[data-post-id="' + postId + '"]').removeClass('glyphicon-star-empty');
                    jQuery('.b2sFavoriteStar[data-post-id="' + postId + '"]').addClass('glyphicon-star');
                } else {
                    jQuery('.b2sFavoriteStar[data-post-id="' + postId + '"]').removeClass('glyphicon-star');
                    jQuery('.b2sFavoriteStar[data-post-id="' + postId + '"]').addClass('glyphicon-star-empty');
                }
                if (jQuery('#b2sType').val() == 'favorites') {
                    jQuery('.b2s-favorite-list-entry[data-post-id="' + postId + '"]').remove();
                    if (jQuery('.b2s-favorite-list-entry').length == 0) {
                        jQuery('.b2s-sort-result-item-area').html('<li class="list-group-item"><div class="media"><div class="media-body"></div>' + jQuery('#b2sNoFavoritesText').val() + '</div></li>');
                        jQuery('.b2s-sort-pagination-area').hide();
                    }
                }
            }
            jQuery('.b2sFavoriteStar[data-post-id="' + postId + '"]').removeClass('b2sFavoriteStarLoading');
            return true;
        }
    });

});

jQuery(document).on('click', '.b2s-preview-network-info-link', function (e) {
    e.preventDefault();
    // Add your code here to handle the click event
    jQuery('#b2sInfoNetworkModal').modal('show');
});

jQuery(document).on('click', '.b2sTwitterInfoModalBtn', function () {
    jQuery('#b2sTwitterInfoModal').modal('show');
});

function showLoadingArea(){
    jQuery('.b2s-all-loading-area').show();
    jQuery('#b2s-compose-expand-area').hide();
}
function hideLoadingArea(){
    jQuery('.b2s-all-loading-area').hide();
    jQuery('#b2s-compose-expand-area').show();
}

function showPreviewLoadingArea(){
    jQuery('.b2s-preview-loading-area').show();
    jQuery('.b2s-compose-preview-panel').hide();
 
}
function hidePreviewLoadingArea(){
    jQuery('.b2s-preview-loading-area').hide();
    jQuery('.b2s-compose-preview-panel').show();
}