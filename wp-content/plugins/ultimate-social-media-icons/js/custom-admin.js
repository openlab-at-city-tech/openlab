function sfsi_update_index() {
    var s = 1;
    SFSI("ul.icn_listing li.custom").each(function () {
        SFSI(this).children("span.custom-txt").html("Custom " + s), s++;
    }), cntt = 1, SFSI("div.cm_lnk").each(function () {
        SFSI(this).find("h2.custom").find("span.sfsiCtxt").html("Custom " + cntt + ":"),
            cntt++;
    }), cntt = 1, SFSI("div.custom_m").find("div.custom_section").each(function () {
        SFSI(this).find("label").html("Custom " + cntt + ":"), cntt++;
    });
}

function sfsicollapse(s) {
    var i = !0,
        e = SFSI(s).closest("div.ui-accordion-content").prev("h3.ui-accordion-header"),
        t = SFSI(s).closest("div.ui-accordion-content").first();
    e.toggleClass("ui-corner-all", i).toggleClass("accordion-header-active ui-state-active ui-corner-top", !i).attr("aria-selected", (!i).toString()),
        e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", i).toggleClass("ui-icon-triangle-1-s", !i),
        t.toggleClass("accordion-content-active", !i), i ? t.slideUp() : t.slideDown();
}

function sfsi_delete_CusIcon(s, i) {
    beForeLoad();
    var e = {
        action: "deleteIcons",
        icon_name: i.attr("name"),
        nonce: SFSI(i).parents('.custom').find('input[name="nonce"]').val()
    };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: e,
        dataType: "json",
        success: function (e) {
            if ("success" == e.res) {
                showErrorSuc("success", "Saved !", 1);
                var t = e.last_index + 1;
                SFSI("#total_cusotm_icons").val(e.total_up), SFSI(s).closest(".custom").remove(),
                    SFSI("li.custom:last-child").addClass("bdr_btm_non"), SFSI(".custom-links").find("div." + i.attr("name")).remove(),
                    SFSI(".custom_m").find("div." + i.attr("name")).remove(), SFSI(".share_icon_order").children("li." + i.attr("name")).remove(),
                    SFSI("ul.sfsi_sample_icons").children("li." + i.attr("name")).remove();

                if (e.total_up == 0) {
                    SFSI(".notice_custom_icons_premium").hide();
                }
                var n = e.total_up + 1;
                4 == e.total_up && SFSI(".icn_listing").append('<li id="c' + t + '" class="custom bdr_btm_non"><div class="radio_section tb_4_ck"><span class="checkbox" dynamic_ele="yes" style=" 0px 0px;"></span><input name="sfsiICON_' + t + '_display"  type="checkbox" value="yes" class="styled" style="display:none;"  isNew="yes" /></div> <span class="custom-img"><img src="' + SFSI("#plugin_url").val() + 'images/custom.png" id="CImg_' + t + '"  alt="error" /> </span> <span class="custom custom-txt">Custom' + n + ' </span> <div class="right_info"> <p><span>It depends:</span> Upload a custom icon if you have other accounts/websites you want to link to.</p><div class="inputWrapper"></div></li>');
            } else showErrorSuc("error", "Unkown error , please try again", 1);
            return sfsi_update_index(), update_Sec5Iconorder(), sfsi_update_step1(), sfsi_update_step5(),
                afterLoad(), "suc";
        }
    });
}

function update_Sec5Iconorder() {
    SFSI("ul.share_icon_order").children("li").each(function () {
        SFSI(this).attr("data-index", SFSI(this).index() + 1);
    });
}

function sfsi_section_Display(s, i) {
    "hide" == i ? (SFSI("." + s + " :input").prop("disabled", !0), SFSI("." + s + " :button").prop("disabled", !0),
        SFSI("." + s).hide()) : (SFSI("." + s + " :input").removeAttr("disabled", !0), SFSI("." + s + " :button").removeAttr("disabled", !0),
        SFSI("." + s).show());
}

function sfsi_depened_sections() {
    if ("sfsi" == SFSI("input[name='sfsi_rss_icons']:checked").val()) {
        for (i = 0; 16 > i; i++) {
            var s = i + 1,
                e = 74 * i;
            SFSI(".row_" + s + "_2").css("background-position", "-588px -" + e + "px");
        }
        var t = SFSI(".icon_img").attr("src");
        if (t) {
            if (t.indexOf("subscribe") != -1) {
                var n = t.replace("subscribe.png", "sf_arow_icn.png");
            } else {
                var n = t.replace("email.png", "sf_arow_icn.png");
            }
            SFSI(".icon_img").attr("src", n);
        }
    } else {
        if ("email" == SFSI("input[name='sfsi_rss_icons']:checked").val()) {
            for (SFSI(".row_1_2").css("background-position", "-58px 0"), i = 0; 16 > i; i++) {
                var s = i + 1,
                    e = 74 * i;
                SFSI(".row_" + s + "_2").css("background-position", "-58px -" + e + "px");
            }
            var t = SFSI(".icon_img").attr("src");
            if (t) {
                if (t.indexOf("sf_arow_icn") != -1) {
                    var n = t.replace("sf_arow_icn.png", "email.png");
                } else {
                    var n = t.replace("subscribe.png", "email.png");
                }
                SFSI(".icon_img").attr("src", n);
            }
        } else {
            for (SFSI(".row_1_2").css("background-position", "-649px 0"), i = 0; 16 > i; i++) {
                var s = i + 1,
                    e = 74 * i;
                SFSI(".row_" + s + "_2").css("background-position", "-649px -" + e + "px");
            }
            var t = SFSI(".icon_img").attr("src");
            if (t) {
                if (t.indexOf("email") != -1) {
                    var n = t.replace("email.png", "subscribe.png");
                } else {
                    var n = t.replace("sf_arow_icn.png", "subscribe.png");
                }
                SFSI(".icon_img").attr("src", n);
            }
        }
    }
    SFSI("input[name='sfsi_rss_display']").prop("checked") ? sfsi_section_Display("rss_section", "show") : sfsi_section_Display("rss_section", "hide"),
        SFSI("input[name='sfsi_email_display']").prop("checked") ? sfsi_section_Display("email_section", "show") : sfsi_section_Display("email_section", "hide"),
        SFSI("input[name='sfsi_facebook_display']").prop("checked") ? sfsi_section_Display("facebook_section", "show") : sfsi_section_Display("facebook_section", "hide"),
        SFSI("input[name='sfsi_twitter_display']").prop("checked") ? sfsi_section_Display("twitter_section", "show") : sfsi_section_Display("twitter_section", "hide"),
        SFSI("input[name='sfsi_youtube_display']").prop("checked") ? sfsi_section_Display("youtube_section", "show") : sfsi_section_Display("youtube_section", "hide"),
        SFSI("input[name='sfsi_pinterest_display']").prop("checked") ? sfsi_section_Display("pinterest_section", "show") : sfsi_section_Display("pinterest_section", "hide"),
        SFSI("input[name='sfsi_telegram_display']").prop("checked") ? sfsi_section_Display("telegram_section", "show") : sfsi_section_Display("telegram_section", "hide"),
        SFSI("input[name='sfsi_vk_display']").prop("checked") ? sfsi_section_Display("vk_section", "show") : sfsi_section_Display("vk_section", "hide"),
        SFSI("input[name='sfsi_ok_display']").prop("checked") ? sfsi_section_Display("ok_section", "show") : sfsi_section_Display("ok_section", "hide"),
        SFSI("input[name='sfsi_wechat_display']").prop("checked") ? sfsi_section_Display("wechat_section", "show") : sfsi_section_Display("wechat_section", "hide"),
        SFSI("input[name='sfsi_weibo_display']").prop("checked") ? sfsi_section_Display("weibo_section", "show") : sfsi_section_Display("weibo_section", "hide"),
        SFSI("input[name='sfsi_instagram_display']").prop("checked") ? sfsi_section_Display("instagram_section", "show") : sfsi_section_Display("instagram_section", "hide"),
        SFSI("input[name='sfsi_linkedin_display']").prop("checked") ? sfsi_section_Display("linkedin_section", "show") : sfsi_section_Display("linkedin_section", "hide"),
        SFSI("input[element-type='cusotm-icon']").prop("checked") ? sfsi_section_Display("custom_section", "show") : sfsi_section_Display("custom_section", "hide");
}

function CustomIConSectionsUpdate() {
    sfsi_section_Display("counter".ele, show);
}

// Upload Custom Skin {Monad}
function sfsi_customskin_upload(s, ref, nonce) {
    var ttl = jQuery(ref).attr("title");
    var i = s,
        e = {
            action: "UploadSkins",
            custom_imgurl: i,
            nonce: nonce
        };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: e,
        success: function (msg) {
            if (msg.res = "success") {
                var arr = s.split('=');
                jQuery(ref).prev('.imgskin').attr('src', arr[1]);
                jQuery(ref).prev('.imgskin').css("display", "block");
                jQuery(ref).text("Update");
                jQuery(ref).next('.dlt_btn').css("display", "block");
            }
        }
    });
}

// Delete Custom Skin {Monad}
function deleteskin_icon(s) {
    var iconname = jQuery(s).attr("title");
    var nonce = jQuery(s).attr("data-nonce");
    var i = iconname,
        e = {
            action: "DeleteSkin",
            iconname: i,
            nonce: nonce
        };
    // console.log('delete sin icon', i, iconname, nonce);
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: e,
        dataType: "json",
        success: function (msg) {
            // console.log(s, e, msg);

            if (msg.res === "success") {
                SFSI(s).prev("a").text("Upload");
                SFSI(s).prev("a").prev("img").attr("src", '');
                SFSI(s).prev("a").prev("img").css("display", "none");
                SFSI(s).css("display", "none");
            } else {
                alert("Whoops! something went wrong.")
            }
        }
    });
}

// Save Custom Skin {Monad}
function SFSI_done(nonce) {
    e = {
        action: "Iamdone",
        nonce: nonce
    };

    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: e,
        success: function (msg) {
            if (msg.res === "success") {


                jQuery("li.cstomskins_upload").children(".icns_tab_3").html(msg);
                SFSI("input[name='sfsi_rss_display']").prop("checked") ? sfsi_section_Display("rss_section", "show") : sfsi_section_Display("rss_section", "hide"), SFSI("input[name='sfsi_email_display']").prop("checked") ? sfsi_section_Display("email_section", "show") : sfsi_section_Display("email_section", "hide"), SFSI("input[name='sfsi_facebook_display']").prop("checked") ? sfsi_section_Display("facebook_section", "show") : sfsi_section_Display("facebook_section", "hide"), SFSI("input[name='sfsi_twitter_display']").prop("checked") ? sfsi_section_Display("twitter_section", "show") : sfsi_section_Display("twitter_section", "hide"), SFSI("input[name='sfsi_youtube_display']").prop("checked") ? sfsi_section_Display("youtube_section", "show") : sfsi_section_Display("youtube_section", "hide"), SFSI("input[name='sfsi_pinterest_display']").prop("checked") ? sfsi_section_Display("pinterest_section", "show") : sfsi_section_Display("pinterest_section", "hide"), SFSI("input[name='sfsi_instagram_display']").prop("checked") ? sfsi_section_Display("instagram_section", "show") : sfsi_section_Display("instagram_section", "hide"), SFSI("input[name='sfsi_linkedin_display']").prop("checked") ? sfsi_section_Display("linkedin_section", "show") : sfsi_section_Display("linkedin_section", "hide"), SFSI("input[element-type='cusotm-icon']").prop("checked") ? sfsi_section_Display("custom_section", "show") : sfsi_section_Display("custom_section", "hide");
                SFSI(".cstmskins-overlay").hide("slow");
                sfsi_update_step3() && sfsicollapse(this);
            }
        }
    });
}

// Upload Custom Icons {Monad}
function sfsi_newcustomicon_upload(s, nonce, nonce2) {
    var i = s,
        e = {
            action: "UploadIcons",
            custom_imgurl: i,
            nonce: nonce
        };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: e,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s.res == 'success') {
                afterIconSuccess(s, nonce2);
            } else {
                SFSI(".upload-overlay").hide("slow");
                SFSI(".uperror").html(s.res);
                showErrorSuc("Error", "Some Error Occured During Upload Custom Icon", 1)
            }
        }
    });
}

function sfsi_update_step1() {
    var nonce = SFSI("#sfsi_save1").attr("data-nonce");
    global_error = 0, beForeLoad(), sfsi_depened_sections();
    var s = !1,
        i = SFSI("input[name='sfsi_rss_display']:checked").val(),
        e = SFSI("input[name='sfsi_email_display']:checked").val(),
        t = SFSI("input[name='sfsi_facebook_display']:checked").val(),
        n = SFSI("input[name='sfsi_twitter_display']:checked").val(),
        r = SFSI("input[name='sfsi_youtube_display']:checked").val(),
        c = SFSI("input[name='sfsi_pinterest_display']:checked").val(),
        p = SFSI("input[name='sfsi_linkedin_display']:checked").val(),
        tg = SFSI("input[name='sfsi_telegram_display']:checked").val(),
        vk = SFSI("input[name='sfsi_vk_display']:checked").val(),
        ok = SFSI("input[name='sfsi_ok_display']:checked").val(),
        wc = SFSI("input[name='sfsi_wechat_display']:checked").val(),
        wb = SFSI("input[name='sfsi_weibo_display']:checked").val(),
        _ = SFSI("input[name='sfsi_instagram_display']:checked").val(),
        l = SFSI("input[name='sfsi_custom1_display']:checked").val(),
        S = SFSI("input[name='sfsi_custom2_display']:checked").val(),
        u = SFSI("input[name='sfsi_custom3_display']:checked").val(),
        f = SFSI("input[name='sfsi_custom4_display']:checked").val(),
        d = SFSI("input[name='sfsi_custom5_display']:checked").val(),
        I = {
            action: "updateSrcn1",
            sfsi_rss_display: i,
            sfsi_email_display: e,
            sfsi_facebook_display: t,
            sfsi_twitter_display: n,
            sfsi_youtube_display: r,
            sfsi_pinterest_display: c,
            sfsi_linkedin_display: p,
            sfsi_telegram_display: tg,
            sfsi_vk_display: vk,
            sfsi_ok_display: ok,
            sfsi_wechat_display: wc,
            sfsi_weibo_display: wb,
            sfsi_instagram_display: _,
            sfsi_custom1_display: l,
            sfsi_custom2_display: S,
            sfsi_custom3_display: u,
            sfsi_custom4_display: f,
            sfsi_custom5_display: d,
            nonce: nonce
        };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: I,
        async: !0,
        dataType: "json",
        success: function (i) {
            if (i == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 1);
                s = !1;
                afterLoad();
            } else {
                "success" == i ? (showErrorSuc("success", "Saved !", 1), sfsicollapse("#sfsi_save1"),
                    sfsi_make_popBox()) : (global_error = 1, showErrorSuc("error", "Unkown error , please try again", 1),
                    s = !1), afterLoad();
            }
        }
    });
}

function sfsi_update_step2() {

    var nonce = SFSI("#sfsi_save2").attr("data-nonce");
    var s = sfsi_validationStep2();
    if (!s) return global_error = 1, !1;
    beForeLoad();
    var i = 1 == SFSI("input[name='sfsi_rss_url']").prop("disabled") ? "" : SFSI("input[name='sfsi_rss_url']").val(),
        e = 1 == SFSI("input[name='sfsi_rss_icons']").prop("disabled") ? "" : SFSI("input[name='sfsi_rss_icons']:checked").val(),

        t = 1 == SFSI("input[name='sfsi_facebookPage_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_facebookPage_option']:checked").val(),
        n = 1 == SFSI("input[name='sfsi_facebookLike_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_facebookLike_option']:checked").val(),
        o = 1 == SFSI("input[name='sfsi_facebookShare_option']").prop("disabled") ? "" : SFSI("input[name='sfsi_facebookShare_option']:checked").val(),
        a = SFSI("input[name='sfsi_facebookPage_url']").val(),
        r = 1 == SFSI("input[name='sfsi_twitter_followme']").prop("disabled") ? "" : SFSI("input[name='sfsi_twitter_followme']:checked").val(),
        c = 1 == SFSI("input[name='sfsi_twitter_followUserName']").prop("disabled") ? "" : SFSI("input[name='sfsi_twitter_followUserName']").val(),
        p = 1 == SFSI("input[name='sfsi_twitter_aboutPage']").prop("disabled") ? "" : SFSI("input[name='sfsi_twitter_aboutPage']:checked").val(),
        _ = 1 == SFSI("input[name='sfsi_twitter_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_twitter_page']:checked").val(),
        l = SFSI("input[name='sfsi_twitter_pageURL']").val(),
        S = SFSI("textarea[name='sfsi_twitter_aboutPageText']").val(),
        m = 1 == SFSI("input[name='sfsi_youtube_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_youtube_page']:checked").val(),
        F = 1 == SFSI("input[name='sfsi_youtube_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_youtube_pageUrl']").val(),
        h = 1 == SFSI("input[name='sfsi_youtube_follow']").prop("disabled") ? "" : SFSI("input[name='sfsi_youtube_follow']:checked").val(),
        cls = SFSI("input[name='sfsi_youtubeusernameorid']:checked").val(),
        v = SFSI("input[name='sfsi_ytube_user']").val(),
        vchid = SFSI("input[name='sfsi_ytube_chnlid']").val(),
        g = 1 == SFSI("input[name='sfsi_pinterest_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_pinterest_page']:checked").val(),
        k = 1 == SFSI("input[name='sfsi_pinterest_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_pinterest_pageUrl']").val(),
        y = 1 == SFSI("input[name='sfsi_pinterest_pingBlog']").prop("disabled") ? "" : SFSI("input[name='sfsi_pinterest_pingBlog']:checked").val(),
        b = 1 == SFSI("input[name='sfsi_instagram_pageUrl']").prop("disabled") ? "" : SFSI("input[name='sfsi_instagram_pageUrl']").val(),
        w = 1 == SFSI("input[name='sfsi_linkedin_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedin_page']:checked").val(),
        x = 1 == SFSI("input[name='sfsi_linkedin_pageURL']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedin_pageURL']").val(),
        C = 1 == SFSI("input[name='sfsi_linkedin_follow']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedin_follow']:checked").val(),
        D = SFSI("input[name='sfsi_linkedin_followCompany']").val(),
        U = 1 == SFSI("input[name='sfsi_linkedin_SharePage']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedin_SharePage']:checked").val(),
        O = SFSI("input[name='sfsi_linkedin_recommendBusines']:checked").val(),
        T = SFSI("input[name='sfsi_linkedin_recommendProductId']").val(),
        j = SFSI("input[name='sfsi_linkedin_recommendCompany']").val(),
        tp = 1 == SFSI("input[name='sfsi_telegram_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_telegram_page']:checked").val(),
        tpu = SFSI("input[name='sfsi_telegram_pageURL']").val(),
        tm = SFSI("input[name='sfsi_telegram_message']").val(),
        tmn = SFSI("input[name='sfsi_telegram_username']").val(),
        wp = 1 == SFSI("input[name='sfsi_weibo_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_weibo_page']:checked").val(),
        wpu = SFSI("input[name='sfsi_weibo_pageURL']").val(),
        vp = 1 == SFSI("input[name='sfsi_vk_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_vk_page']:checked").val(),
        vpu = SFSI("input[name='sfsi_vk_pageURL']").val(),
        op = 1 == SFSI("input[name='sfsi_ok_page']").prop("disabled") ? "" : SFSI("input[name='sfsi_ok_page']:checked").val(),
        opu = SFSI("input[name='sfsi_ok_pageURL']").val(),
        P = {};
    SFSI("input[name='sfsi_CustomIcon_links[]']").each(function () {
        P[SFSI(this).attr("file-id")] = this.value;
    });
    var M = {
        action: "updateSrcn2",
        sfsi_rss_url: i,
        sfsi_rss_icons: e,
        sfsi_facebookPage_option: t,
        sfsi_facebookLike_option: n,
        sfsi_facebookShare_option: o,
        sfsi_facebookPage_url: a,
        sfsi_twitter_followme: r,
        sfsi_twitter_followUserName: c,
        sfsi_twitter_aboutPage: p,
        sfsi_twitter_page: _,
        sfsi_twitter_pageURL: l,
        sfsi_twitter_aboutPageText: S,
        sfsi_youtube_page: m,
        sfsi_youtube_pageUrl: F,
        sfsi_youtube_follow: h,
        sfsi_youtubeusernameorid: cls,
        sfsi_ytube_user: v,
        sfsi_ytube_chnlid: vchid,
        sfsi_pinterest_page: g,
        sfsi_pinterest_pageUrl: k,
        sfsi_instagram_pageUrl: b,
        sfsi_pinterest_pingBlog: y,
        sfsi_linkedin_page: w,
        sfsi_linkedin_pageURL: x,
        sfsi_linkedin_follow: C,
        sfsi_linkedin_followCompany: D,
        sfsi_linkedin_SharePage: U,
        sfsi_linkedin_recommendBusines: O,
        sfsi_linkedin_recommendCompany: j,
        sfsi_linkedin_recommendProductId: T,
        sfsi_custom_links: P,
        sfsi_telegram_page: tp,
        sfsi_telegram_pageURL: tpu,
        sfsi_telegram_message: tm,
        sfsi_telegram_username: tmn,
        sfsi_weibo_page: wp,
        sfsi_weibo_pageURL: wpu,
        sfsi_vk_page: vp,
        sfsi_vk_pageURL: vpu,
        sfsi_ok_page: op,
        sfsi_ok_pageURL: opu,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: M,
        async: !0,
        dataType: "json",
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 2);
                return_value = !1;
                afterLoad();
            } else {
                "success" == s ? (showErrorSuc("success", "Saved !", 2), sfsicollapse("#sfsi_save2"),
                    sfsi_depened_sections()) : (global_error = 1, showErrorSuc("error", "Unkown error , please try again", 2),
                    return_value = !1), afterLoad();
            }
        }
    });
}

function sfsi_update_step3() {
    var nonce = SFSI("#sfsi_save3").attr("data-nonce");
    var s = sfsi_validationStep3();
    if (!s) return global_error = 1, !1;
    beForeLoad();
    var i = SFSI("input[name='sfsi_actvite_theme']:checked").val(),
        e = SFSI("input[name='sfsi_mouseOver']:checked").val(),
        t = SFSI("input[name='sfsi_shuffle_icons']:checked").val(),
        n = SFSI("input[name='sfsi_shuffle_Firstload']:checked").val(),
        o = SFSI("input[name='sfsi_same_icons_mouseOver_effect']:checked").val(),
        a = SFSI("input[name='sfsi_shuffle_interval']:checked").val(),
        r = SFSI("input[name='sfsi_shuffle_intervalTime']").val(),
        c = SFSI("input[name='sfsi_specialIcon_animation']:checked").val(),
        p = SFSI("input[name='sfsi_specialIcon_MouseOver']:checked").val(),
        _ = SFSI("input[name='sfsi_specialIcon_Firstload']:checked").val(),
        l = SFSI("#sfsi_specialIcon_Firstload_Icons option:selected").val(),
        S = SFSI("input[name='sfsi_specialIcon_interval']:checked").val(),
        u = SFSI("input[name='sfsi_specialIcon_intervalTime']").val(),
        f = SFSI("#sfsi_specialIcon_intervalIcons option:selected").val();

    var mouseover_effect_type = 'same_icons'; //SFSI("input[name='sfsi_mouseOver_effect_type']:checked").val();

    d = {
        action: "updateSrcn3",
        sfsi_actvite_theme: i,
        sfsi_mouseOver: e,
        sfsi_shuffle_icons: t,
        sfsi_shuffle_Firstload: n,
        sfsi_mouseOver_effect: o,
        sfsi_mouseover_effect_type: mouseover_effect_type,
        sfsi_shuffle_interval: a,
        sfsi_shuffle_intervalTime: r,
        sfsi_specialIcon_animation: c,
        sfsi_specialIcon_MouseOver: p,
        sfsi_specialIcon_Firstload: _,
        sfsi_specialIcon_Firstload_Icons: l,
        sfsi_specialIcon_interval: S,
        sfsi_specialIcon_intervalTime: u,
        sfsi_specialIcon_intervalIcons: f,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: d,
        async: !0,
        dataType: "json",
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 3);
                return_value = !1;
                afterLoad();
            } else {
                "success" == s ? (showErrorSuc("success", "Saved !", 3), sfsicollapse("#sfsi_save3")) : (showErrorSuc("error", "Unkown error , please try again", 3),
                    return_value = !1), afterLoad();
            }
        }
    });
}

function sfsi_show_counts() {
    "yes" == SFSI("input[name='sfsi_display_counts']:checked").val() ? (SFSI(".count_sections").slideDown(),
        sfsi_showPreviewCounts()) : (SFSI(".count_sections").slideUp(), sfsi_showPreviewCounts());
}

function sfsi_showPreviewCounts() {
    var s = 0;
    1 == SFSI("input[name='sfsi_rss_countsDisplay']").prop("checked") ? (SFSI("#sfsi_rss_countsDisplay").css("opacity", 1), s = 1) : SFSI("#sfsi_rss_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_email_countsDisplay']").prop("checked") ? (SFSI("#sfsi_email_countsDisplay").css("opacity", 1),
            s = 1) : SFSI("#sfsi_email_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_facebook_countsDisplay']").prop("checked") ? (SFSI("#sfsi_facebook_countsDisplay").css("opacity", 1),
            s = 1) : SFSI("#sfsi_facebook_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_twitter_countsDisplay']").prop("checked") ? (SFSI("#sfsi_twitter_countsDisplay").css("opacity", 1),
            s = 1) : SFSI("#sfsi_twitter_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_linkedIn_countsDisplay']").prop("checked") ? (SFSI("#sfsi_linkedIn_countsDisplay").css("opacity", 1), s = 1) : SFSI("#sfsi_linkedIn_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_youtube_countsDisplay']").prop("checked") ? (SFSI("#sfsi_youtube_countsDisplay").css("opacity", 1),
            s = 1) : SFSI("#sfsi_youtube_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_pinterest_countsDisplay']").prop("checked") ? (SFSI("#sfsi_pinterest_countsDisplay").css("opacity", 1),
            s = 1) : SFSI("#sfsi_pinterest_countsDisplay").css("opacity", 0), 1 == SFSI("input[name='sfsi_instagram_countsDisplay']").prop("checked") ? (SFSI("#sfsi_instagram_countsDisplay").css("opacity", 1),
            s = 1) : SFSI("#sfsi_instagram_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_telegram_countsDisplay']").prop("checked") ? (SFSI("#sfsi_telegram_countsDisplay").css("opacity", 0), s = 1) : SFSI("#sfsi_telegram_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_vk_countsDisplay']").prop("checked") ? (SFSI("#sfsi_vk_countsDisplay").css("opacity", 0), s = 1) : SFSI("#sfsi_vk_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_ok_countsDisplay']").prop("checked") ? (SFSI("#sfsi_ok_countsDisplay").css("opacity", 0), s = 1) : SFSI("#sfsi_ok_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_weibo_countsDisplay']").prop("checked") ? (SFSI("#sfsi_weibo_countsDisplay").css("opacity", 0), s = 1) : SFSI("#sfsi_weibo_countsDisplay").css("opacity", 0),
        1 == SFSI("input[name='sfsi_wechat_countsDisplay']").prop("checked") ? (SFSI("#sfsi_wechat_countsDisplay").css("opacity", 0), s = 1) : SFSI("#sfsi_wechat_countsDisplay").css("opacity", 0),

        0 == s || "no" == SFSI("input[name='sfsi_display_counts']:checked").val() ? SFSI(".sfsi_Cdisplay").hide() : SFSI(".sfsi_Cdisplay").show();
}

function sfsi_show_OnpostsDisplay() {
    //"yes" == SFSI("input[name='sfsi_show_Onposts']:checked").val() ? SFSI(".PostsSettings_section").slideDown() :SFSI(".PostsSettings_section").slideUp();
}

function sfsi_update_step4() {
    var nonce = SFSI("#sfsi_save4").attr("data-nonce");
    var s = !1,
        i = sfsi_validationStep4();
    if (!i) return global_error = 1, !1;
    beForeLoad();
    var e = SFSI("input[name='sfsi_display_counts']:checked").val(),
        t = 1 == SFSI("input[name='sfsi_email_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_email_countsDisplay']:checked").val(),
        n = 1 == SFSI("input[name='sfsi_email_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_email_countsFrom']:checked").val(),
        o = SFSI("input[name='sfsi_email_manualCounts']").val(),
        r = 1 == SFSI("input[name='sfsi_rss_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_rss_countsDisplay']:checked").val(),
        c = SFSI("input[name='sfsi_rss_manualCounts']").val(),
        p = 1 == SFSI("input[name='sfsi_facebook_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_facebook_countsDisplay']:checked").val(),
        _ = 1 == SFSI("input[name='sfsi_facebook_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_facebook_countsFrom']:checked").val(),
        mp = SFSI("input[name='sfsi_facebook_mypageCounts']").val(),
        l = SFSI("input[name='sfsi_facebook_manualCounts']").val(),
        S = 1 == SFSI("input[name='sfsi_twitter_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_twitter_countsDisplay']:checked").val(),
        u = 1 == SFSI("input[name='sfsi_twitter_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_twitter_countsFrom']:checked").val(),
        f = SFSI("input[name='sfsi_twitter_manualCounts']").val(),
        d = SFSI("input[name='tw_consumer_key']").val(),
        I = SFSI("input[name='tw_consumer_secret']").val(),
        m = SFSI("input[name='tw_oauth_access_token']").val(),
        F = SFSI("input[name='tw_oauth_access_token_secret']").val(),
        k = 1 == SFSI("input[name='sfsi_linkedIn_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedIn_countsFrom']:checked").val(),
        y = SFSI("input[name='sfsi_linkedIn_manualCounts']").val(),
        b = SFSI("input[name='ln_company']").val(),
        w = SFSI("input[name='ln_api_key']").val(),
        x = SFSI("input[name='ln_secret_key']").val(),
        C = SFSI("input[name='ln_oAuth_user_token']").val(),
        D = 1 == SFSI("input[name='sfsi_linkedIn_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedIn_countsDisplay']:checked").val(),
        k = 1 == SFSI("input[name='sfsi_linkedIn_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedIn_countsFrom']:checked").val(),
        y = SFSI("input[name='sfsi_linkedIn_manualCounts']").val(),
        U = 1 == SFSI("input[name='sfsi_youtube_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_youtube_countsDisplay']:checked").val(),
        O = 1 == SFSI("input[name='sfsi_youtube_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_youtube_countsFrom']:checked").val(),
        T = SFSI("input[name='sfsi_youtube_manualCounts']").val(),
        j = SFSI("input[name='sfsi_youtube_user']").val(),
        P = 1 == SFSI("input[name='sfsi_pinterest_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_pinterest_countsDisplay']:checked").val(),
        M = 1 == SFSI("input[name='sfsi_pinterest_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_pinterest_countsFrom']:checked").val(),
        L = SFSI("input[name='sfsi_pinterest_manualCounts']").val(),
        B = SFSI("input[name='sfsi_pinterest_user']").val(),
        E = SFSI("input[name='sfsi_pinterest_board']").val(),
        z = 1 == SFSI("input[name='sfsi_instagram_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_instagram_countsDisplay']:checked").val(),
        A = 1 == SFSI("input[name='sfsi_instagram_countsFrom']").prop("disabled") ? "" : SFSI("input[name='sfsi_instagram_countsFrom']:checked").val(),
        N = SFSI("input[name='sfsi_instagram_manualCounts']").val(),
        H = SFSI("input[name='sfsi_instagram_User']").val(),
        ha = SFSI("input[name='sfsi_instagram_clientid']").val(),
        ia = SFSI("input[name='sfsi_instagram_appurl']").val(),
        ja = SFSI("input[name='sfsi_instagram_token']").val(),

        tc = 1 == SFSI("input[name='sfsi_telegram_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_telegram_countsDisplay']:checked").val(),
        tm = SFSI("input[name='sfsi_telegram_manualCounts']").val(),

        vc = 1 == SFSI("input[name='sfsi_vk_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_vk_countsDisplay']:checked").val(),
        vm = SFSI("input[name='sfsi_vk_manualCounts']").val(),


        oc = 1 == SFSI("input[name='sfsi_ok_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_ok_countsDisplay']:checked").val(),
        om = SFSI("input[name='sfsi_ok_manualCounts']").val(),


        wc = 1 == SFSI("input[name='sfsi_weibo_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_weibo_countsDisplay']:checked").val(),
        wm = SFSI("input[name='sfsi_weibo_manualCounts']").val(),

        wcc = 1 == SFSI("input[name='sfsi_wechat_countsDisplay']").prop("disabled") ? "" : SFSI("input[name='sfsi_wechat_countsDisplay']:checked").val(),
        wcm = SFSI("input[name='sfsi_wechat_manualCounts']").val(),

        resp = 1 == SFSI("input[name='sfsi_responsive_share_count']").prop("disabled") ? "" : SFSI("input[name='sfsi_responsive_share_count']:checked").val(),
        original = 1 == SFSI("input[name='sfsi_original_counts']").prop("disabled") ? "" : SFSI("input[name='sfsi_original_counts']:checked").val(),
        round = 1 == SFSI("input[name='sfsi_round_counts']").prop("disabled") ? "" : SFSI("input[name='sfsi_round_counts']:checked").val()

    console.log(resp, original, round);

    $ = {
        action: "updateSrcn4",
        sfsi_display_counts: e,
        sfsi_email_countsDisplay: t,
        sfsi_email_countsFrom: n,
        sfsi_email_manualCounts: o,
        sfsi_rss_countsDisplay: r,
        sfsi_rss_manualCounts: c,
        sfsi_facebook_countsDisplay: p,
        sfsi_facebook_countsFrom: _,
        sfsi_facebook_mypageCounts: mp,
        sfsi_facebook_manualCounts: l,
        sfsi_twitter_countsDisplay: S,
        sfsi_twitter_countsFrom: u,
        sfsi_twitter_manualCounts: f,
        tw_consumer_key: d,
        tw_consumer_secret: I,
        tw_oauth_access_token: m,
        tw_oauth_access_token_secret: F,
        sfsi_linkedIn_countsDisplay: D,
        sfsi_linkedIn_countsFrom: k,
        sfsi_linkedIn_manualCounts: y,
        ln_company: b,
        ln_api_key: w,
        ln_secret_key: x,
        ln_oAuth_user_token: C,
        sfsi_youtube_countsDisplay: U,
        sfsi_youtube_countsFrom: O,
        sfsi_youtube_manualCounts: T,
        sfsi_youtube_user: j,
        sfsi_youtube_channelId: SFSI("input[name='sfsi_youtube_channelId']").val(),
        sfsi_pinterest_countsDisplay: P,
        sfsi_pinterest_countsFrom: M,
        sfsi_pinterest_manualCounts: L,
        sfsi_pinterest_user: B,
        sfsi_pinterest_board: E,
        sfsi_instagram_countsDisplay: z,
        sfsi_instagram_countsFrom: A,
        sfsi_instagram_manualCounts: N,
        sfsi_instagram_User: H,
        sfsi_instagram_clientid: ha,
        sfsi_instagram_appurl: ia,
        sfsi_instagram_token: ja,
        sfsi_telegram_countsDisplay: tc,
        sfsi_telegram_manualCounts: tm,
        sfsi_vk_countsDisplay: vc,
        sfsi_vk_manualCounts: vm,
        sfsi_ok_countsDisplay: oc,
        sfsi_ok_manualCounts: om,
        sfsi_weibo_countsDisplay: wc,
        sfsi_weibo_manualCounts: wm,
        sfsi_wechat_countsDisplay: wcc,
        sfsi_wechat_manualCounts: wcm,
        sfsi_responsive_share_count: resp,
        sfsi_original_counts: original,
        sfsi_round_counts: round,
        nonce: nonce
    };
    console.log($);
    return SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: $,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 4);
                global_error = 1;
                afterLoad();
            } else {
                "success" == s.res ? (showErrorSuc("success", "Saved !", 4), sfsicollapse("#sfsi_save4"),
                    sfsi_showPreviewCounts()) : (showErrorSuc("error", "Unkown error , please try again", 4),
                    global_error = 1), afterLoad();
            }
        }
    }), s;
}

function sfsi_update_step5() {
    var nonce = SFSI("#sfsi_save5").attr("data-nonce");
    sfsi_update_step3();

    var s = sfsi_validationStep5();

    if (!s) return global_error = 1, !1;

    beForeLoad();

    var i = SFSI("input[name='sfsi_icons_size']").val(),
        e = SFSI("input[name='sfsi_icons_perRow']").val(),
        t = SFSI("input[name='sfsi_icons_spacing']").val(),
        n = SFSI("#sfsi_icons_Alignment").val(),
        vw = SFSI("#sfsi_icons_Alignment_via_widget").val(),
        vs = SFSI("#sfsi_icons_Alignment_via_shortcode").val(),

        o = SFSI("input[name='sfsi_icons_ClickPageOpen']:checked").val(),

        se = SFSI("input[name='sfsi_icons_suppress_errors']:checked").val(),
        c = SFSI("input[name='sfsi_icons_stick']:checked").val(),
        p = SFSI("#sfsi_rssIcon_order").attr("data-index"),
        _ = SFSI("#sfsi_emailIcon_order").attr("data-index"),
        S = SFSI("#sfsi_facebookIcon_order").attr("data-index"),
        u = SFSI("#sfsi_twitterIcon_order").attr("data-index"),
        f = SFSI("#sfsi_youtubeIcon_order").attr("data-index"),
        d = SFSI("#sfsi_pinterestIcon_order").attr("data-index"),
        I = SFSI("#sfsi_instagramIcon_order").attr("data-index"),
        F = SFSI("#sfsi_linkedinIcon_order").attr("data-index"),
        tgi = SFSI("#sfsi_telegramIcon_order").attr("data-index"),
        vki = SFSI("#sfsi_vkIcon_order").attr("data-index"),
        oki = SFSI("#sfsi_okIcon_order").attr("data-index"),
        wbi = SFSI("#sfsi_weiboIcon_order").attr("data-index"),
        wci = SFSI("#sfsi_wechatIcon_order").attr("data-index"),

        h = new Array();

    SFSI(".custom_iconOrder").each(function () {
        h.push({
            order: SFSI(this).attr("data-index"),
            ele: SFSI(this).attr("element-id")
        });
    });

    var v = 1 == SFSI("input[name='sfsi_rss_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_rss_MouseOverText']").val(),
        g = 1 == SFSI("input[name='sfsi_email_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_email_MouseOverText']").val(),
        k = 1 == SFSI("input[name='sfsi_twitter_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_twitter_MouseOverText']").val(),
        y = 1 == SFSI("input[name='sfsi_facebook_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_facebook_MouseOverText']").val(),
        w = 1 == SFSI("input[name='sfsi_linkedIn_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_linkedIn_MouseOverText']").val(),
        x = 1 == SFSI("input[name='sfsi_youtube_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_youtube_MouseOverText']").val(),
        C = 1 == SFSI("input[name='sfsi_pinterest_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_pinterest_MouseOverText']").val(),
        D = 1 == SFSI("input[name='sfsi_instagram_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_instagram_MouseOverText']").val(),
        tg = 1 == SFSI("input[name='sfsi_telegram_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_telegram_MouseOverText']").val(),
        vk = 1 == SFSI("input[name='sfsi_vk_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_vk_MouseOverText']").val(),
        ok = 1 == SFSI("input[name='sfsi_ok_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_ok_MouseOverText']").val(),
        wb = 1 == SFSI("input[name='sfsi_weibo_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_weibo_MouseOverText']").val(),
        wc = 1 == SFSI("input[name='sfsi_wechat_MouseOverText']").prop("disabled") ? "" : SFSI("input[name='sfsi_wechat_MouseOverText']").val(),

        O = {};
    SFSI("input[name='sfsi_custom_MouseOverTexts[]']").each(function () {
        O[SFSI(this).attr("file-id")] = this.value;
    });

    var sfsi_custom_social_hide = SFSI("input[name='sfsi_custom_social_hide']").val();

    var T = {
        action: "updateSrcn5",
        sfsi_icons_size: i,
        sfsi_icons_Alignment: n,
        sfsi_icons_Alignment_via_widget: vw,
        sfsi_icons_Alignment_via_shortcode: vs,
        sfsi_icons_perRow: e,
        sfsi_icons_spacing: t,
        sfsi_icons_ClickPageOpen: o,
        sfsi_icons_suppress_errors: se,
        sfsi_icons_stick: c,
        sfsi_rss_MouseOverText: v,
        sfsi_email_MouseOverText: g,
        sfsi_twitter_MouseOverText: k,
        sfsi_facebook_MouseOverText: y,
        sfsi_youtube_MouseOverText: x,
        sfsi_linkedIn_MouseOverText: w,
        sfsi_pinterest_MouseOverText: C,
        sfsi_instagram_MouseOverText: D,
        sfsi_telegram_MouseOverText: tg,
        sfsi_vk_MouseOverText: vk,
        sfsi_ok_MouseOverText: ok,
        sfsi_weibo_MouseOverText: wb,
        sfsi_wechat_MouseOverText: wc,
        sfsi_custom_MouseOverTexts: O,
        sfsi_rssIcon_order: p,
        sfsi_emailIcon_order: _,
        sfsi_facebookIcon_order: S,
        sfsi_twitterIcon_order: u,
        sfsi_youtubeIcon_order: f,
        sfsi_pinterestIcon_order: d,
        sfsi_instagramIcon_order: I,
        sfsi_linkedinIcon_order: F,
        sfsi_telegramIcon_order: tgi,
        sfsi_vkIcon_order: vki,
        sfsi_okIcon_order: oki,
        sfsi_weiboIcon_order: wbi,
        sfsi_wechatIcon_order: wci,

        sfsi_custom_orders: h,
        sfsi_custom_social_hide: sfsi_custom_social_hide,
        nonce: nonce
    };
    // console.log(T);
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: T,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 5);
                global_error = 1;
                afterLoad();
            } else {
                "success" == s ? (showErrorSuc("success", "Saved !", 5), sfsicollapse("#sfsi_save5")) : (global_error = 1,
                    showErrorSuc("error", "Unkown error , please try again", 5)), afterLoad();
            }
        }
    });
}

function sfsi_update_step6() {
    var nonce = SFSI("#sfsi_save9").attr("data-nonce2");
    beForeLoad();
    var s = SFSI("input[name='sfsi_show_Onposts']:checked").val(),
        i = SFSI("input[name='sfsi_textBefor_icons']").val(),
        e = SFSI("#sfsi_icons_alignment").val(),
        rsub = SFSI("input[name='sfsi_rectsub']:checked").val(),
        rfb = SFSI("input[name='sfsi_rectfb']:checked").val(),
        rpin = SFSI("input[name='sfsi_rectpinit']:checked").val(),
        rshr = SFSI("input[name='sfsi_rectshr']:checked").val(),
        rtwr = SFSI("input[name='sfsi_recttwtr']:checked").val(),
        rfbshare = SFSI("input[name='sfsi_rectfbshare']:checked").val(),
        a = SFSI("input[name='sfsi_display_button_type']:checked").val();
    endpost = SFSI("input[name='sfsi_responsive_icons_end_post']:checked").val();

    var responsive_icons = {
        "default_icons": {},
        "settings": {}
    };
    SFSI('.sfsi_responsive_default_icon_container input[type="checkbox"]').each(function (index, obj) {
        var data_obj = {};
        data_obj.active = ('checked' == SFSI(obj).attr('checked')) ? 'yes' : 'no';
        var iconname = SFSI(obj).attr('data-icon');
        var next_section = SFSI(obj).parent().parent();
        data_obj.text = next_section.find('input[name="sfsi_responsive_' + iconname + '_input"]').val();
        data_obj.url = next_section.find('input[name="sfsi_responsive_' + iconname + '_url_input"]').val();
        responsive_icons.default_icons[iconname] = data_obj;
    });
    SFSI('.sfsi_responsive_custom_icon_container input[type="checkbox"]').each(function (index, obj) {
        if (SFSI(obj).attr('id') != "sfsi_responsive_custom_new_display") {
            var data_obj = {};
            data_obj.active = 'checked' == SFSI(obj).attr('checked') ? 'yes' : 'no';
            var icon_index = SFSI(obj).attr('data-custom-index');
            var next_section = SFSI(obj).parent().parent();
            data_obj['added'] = SFSI('input[name="sfsi_responsive_custom_' + index + '_added"]').val();
            data_obj.icon = next_section.find('img').attr('src');
            data_obj["bg-color"] = next_section.find('.sfsi_bg-color-picker').val();

            data_obj.text = next_section.find('input[name="sfsi_responsive_custom_' + icon_index + '_input"]').val();
            data_obj.url = next_section.find('input[name="sfsi_responsive_custom_' + icon_index + '_url_input"]').val();
            responsive_icons.custom_icons[index] = data_obj;
        }
    });
    responsive_icons.settings.icon_size = SFSI('select[name="sfsi_responsive_icons_settings_icon_size"]').val();
    responsive_icons.settings.icon_width_type = SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val();
    responsive_icons.settings.icon_width_size = SFSI('input[name="sfsi_responsive_icons_sttings_icon_width_size"]').val();
    responsive_icons.settings.edge_type = SFSI('select[name="sfsi_responsive_icons_settings_edge_type"]').val();
    responsive_icons.settings.edge_radius = SFSI('select[name="sfsi_responsive_icons_settings_edge_radius"]').val();
    responsive_icons.settings.style = SFSI('select[name="sfsi_responsive_icons_settings_style"]').val();
    responsive_icons.settings.margin = SFSI('input[name="sfsi_responsive_icons_settings_margin"]').val();
    responsive_icons.settings.text_align = SFSI('select[name="sfsi_responsive_icons_settings_text_align"]').val();
    responsive_icons.settings.show_count = SFSI('input[name="sfsi_responsive_icon_show_count"]:checked').val();
    responsive_icons.settings.counter_color = SFSI('input[name="sfsi_responsive_counter_color"]').val();
    responsive_icons.settings.counter_bg_color = SFSI('input[name="sfsi_responsive_counter_bg_color"]').val();
    responsive_icons.settings.share_count_text = SFSI('input[name="sfsi_responsive_counter_share_count_text"]').val();
    responsive_icons.settings.margin_above = SFSI('input[name="sfsi_responsive_icons_settings_margin_above"]').val();
    responsive_icons.settings.margin_below = SFSI('input[name="sfsi_responsive_icons_settings_margin_below"]').val();

    n = {
        action: "updateSrcn6",
        sfsi_show_Onposts: s,
        sfsi_icons_alignment: e,
        sfsi_textBefor_icons: i,
        sfsi_rectsub: rsub,
        sfsi_rectfb: rfb,
        sfsi_rectpinit: rpin,
        sfsi_rectshr: rshr,
        sfsi_recttwtr: rtwr,
        sfsi_rectfbshare: rfbshare,
        sfsi_responsive_icons: responsive_icons,
        sfsi_display_button_type: a,
        sfsi_responsive_icons_end_post: endpost,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: n,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
                global_error = 1;
                afterLoad();
            } else {
                "success" == s ? (showErrorSuc("success", "Saved !", 6), sfsicollapse("#sfsi_save6")) : (global_error = 1,
                    showErrorSuc("error", "Unkown error , please try again", 6)), afterLoad();
            }
        }
    });
}

function sfsi_update_step7() {
    var nonce = SFSI("#sfsi_save7").attr("data-nonce");
    var s = sfsi_validationStep7();
    if (!s) return global_error = 1, !1;
    beForeLoad();
    var i = SFSI("input[name='sfsi_popup_text']").val(),
        e = SFSI("#sfsi_popup_font option:selected").val(),
        t = SFSI("#sfsi_popup_fontStyle option:selected").val(),
        color = SFSI("input[name='sfsi_popup_fontColor']").val(),
        n = SFSI("input[name='sfsi_popup_fontSize']").val(),
        o = SFSI("input[name='sfsi_popup_background_color']").val(),
        a = SFSI("input[name='sfsi_popup_border_color']").val(),
        r = SFSI("input[name='sfsi_popup_border_thickness']").val(),
        c = SFSI("input[name='sfsi_popup_border_shadow']:checked").val(),
        p = SFSI("input[name='sfsi_Show_popupOn']:checked").val(),
        _ = [];
    SFSI("#sfsi_Show_popupOn_PageIDs :selected").each(function (s, i) {
        _[s] = SFSI(i).val();
    });
    var l = SFSI("input[name='sfsi_Shown_pop']:checked").val(),
        S = SFSI("input[name='sfsi_Shown_popupOnceTime']").val(),
        u = SFSI("#sfsi_Shown_popuplimitPerUserTime").val(),
        f = {
            action: "updateSrcn7",
            sfsi_popup_text: i,
            sfsi_popup_font: e,
            sfsi_popup_fontColor: color,
            /*sfsi_popup_fontStyle: t,*/
            sfsi_popup_fontSize: n,
            sfsi_popup_background_color: o,
            sfsi_popup_border_color: a,
            sfsi_popup_border_thickness: r,
            sfsi_popup_border_shadow: c,
            sfsi_Show_popupOn: p,
            sfsi_Show_popupOn_PageIDs: _,
            sfsi_Shown_pop: l,
            sfsi_Shown_popupOnceTime: S,
            sfsi_Shown_popuplimitPerUserTime: u,
            nonce: nonce
        };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: f,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 7);
                afterLoad();
            } else {
                "success" == s ? (showErrorSuc("success", "Saved !", 7), sfsicollapse("#sfsi_save7")) : showErrorSuc("error", "Unkown error , please try again", 7),
                    afterLoad();
            }
        }
    });
}

function sfsi_update_step8() {
    var nonce = SFSI("#sfsi_save8").attr("data-nonce");
    beForeLoad();
    var ie = SFSI("input[name='sfsi_form_adjustment']:checked").val(),
        je = SFSI("input[name='sfsi_form_height']").val(),
        ke = SFSI("input[name='sfsi_form_width']").val(),
        le = SFSI("input[name='sfsi_form_border']:checked").val(),
        me = SFSI("input[name='sfsi_form_border_thickness']").val(),
        ne = SFSI("input[name='sfsi_form_border_color']").val(),
        oe = SFSI("input[name='sfsi_form_background']").val(),

        ae = SFSI("input[name='sfsi_form_heading_text']").val(),
        be = SFSI("#sfsi_form_heading_font option:selected").val(),
        ce = SFSI("#sfsi_form_heading_fontstyle option:selected").val(),
        de = SFSI("input[name='sfsi_form_heading_fontcolor']").val(),
        ee = SFSI("input[name='sfsi_form_heading_fontsize']").val(),
        fe = SFSI("#sfsi_form_heading_fontalign option:selected").val(),

        ue = SFSI("input[name='sfsi_form_field_text']").val(),
        ve = SFSI("#sfsi_form_field_font option:selected").val(),
        we = SFSI("#sfsi_form_field_fontstyle option:selected").val(),
        xe = SFSI("input[name='sfsi_form_field_fontcolor']").val(),
        ye = SFSI("input[name='sfsi_form_field_fontsize']").val(),
        ze = SFSI("#sfsi_form_field_fontalign option:selected").val(),

        i = SFSI("input[name='sfsi_form_button_text']").val(),
        j = SFSI("#sfsi_form_button_font option:selected").val(),
        k = SFSI("#sfsi_form_button_fontstyle option:selected").val(),
        l = SFSI("input[name='sfsi_form_button_fontcolor']").val(),
        m = SFSI("input[name='sfsi_form_button_fontsize']").val(),
        n = SFSI("#sfsi_form_button_fontalign option:selected").val(),
        o = SFSI("input[name='sfsi_form_button_background']").val();

    var f = {
        action: "updateSrcn8",
        sfsi_form_adjustment: ie,
        sfsi_form_height: je,
        sfsi_form_width: ke,
        sfsi_form_border: le,
        sfsi_form_border_thickness: me,
        sfsi_form_border_color: ne,
        sfsi_form_background: oe,

        sfsi_form_heading_text: ae,
        sfsi_form_heading_font: be,
        sfsi_form_heading_fontstyle: ce,
        sfsi_form_heading_fontcolor: de,
        sfsi_form_heading_fontsize: ee,
        sfsi_form_heading_fontalign: fe,

        sfsi_form_field_text: ue,
        sfsi_form_field_font: ve,
        sfsi_form_field_fontstyle: we,
        sfsi_form_field_fontcolor: xe,
        sfsi_form_field_fontsize: ye,
        sfsi_form_field_fontalign: ze,

        sfsi_form_button_text: i,
        sfsi_form_button_font: j,
        sfsi_form_button_fontstyle: k,
        sfsi_form_button_fontcolor: l,
        sfsi_form_button_fontsize: m,
        sfsi_form_button_fontalign: n,
        sfsi_form_button_background: o,

        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: f,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 7);
                afterLoad();
            } else {
                "success" == s ? (showErrorSuc("success", "Saved !", 8), sfsicollapse("#sfsi_save8"), create_suscriber_form()) : showErrorSuc("error", "Unkown error , please try again", 8),
                    afterLoad();
            }
        }
    });
}

// Queestion 3
function sfsi_update_step9() {
    sfsi_update_step6();
    var nonce = SFSI("#sfsi_save9").attr("data-nonce");
    beForeLoad();

    var i_float = SFSI("input[name='sfsi_icons_float']:checked").val(),
        i_floatP = SFSI("input[name='sfsi_icons_floatPosition']:checked").val(),
        i_floatMt = SFSI("input[name='sfsi_icons_floatMargin_top']").val(),
        i_floatMb = SFSI("input[name='sfsi_icons_floatMargin_bottom']").val(),
        i_floatMl = SFSI("input[name='sfsi_icons_floatMargin_left']").val(),
        i_floatMr = SFSI("input[name='sfsi_icons_floatMargin_right']").val(),
        i_disableFloat = SFSI("input[name='sfsi_disable_floaticons']:checked").val(),

        show_via_widget = SFSI("input[name='sfsi_show_via_widget']").val(),
        show_via__shortcode = SFSI("input[name='sfsi_show_via_shortcode']:checked").length == 0 ? "no" : "yes",
        sfsi_show_via_afterposts = SFSI("input[name='sfsi_show_via_afterposts']").val();

    var f = {

        action: "updateSrcn9",

        sfsi_icons_float: i_float,
        sfsi_icons_floatPosition: i_floatP,
        sfsi_icons_floatMargin_top: i_floatMt,
        sfsi_icons_floatMargin_bottom: i_floatMb,
        sfsi_icons_floatMargin_left: i_floatMl,
        sfsi_icons_floatMargin_right: i_floatMr,
        sfsi_disable_floaticons: i_disableFloat,

        sfsi_show_via_widget: show_via_widget,
        sfsi_show_via_shortcode: show_via__shortcode,
        sfsi_show_via_afterposts: sfsi_show_via_afterposts,
        nonce: nonce
    };
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: f,
        dataType: "json",
        async: !0,
        success: function (s) {
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 9);
                afterLoad();
            } else {
                "success" == s ? (showErrorSuc("success", "Saved !", 9), sfsicollapse("#sfsi_save9")) : showErrorSuc("error", "Unkown error , please try again", 9),
                    afterLoad();
            }
        }
    });
}

function sfsi_validationStep2() {
    //var class_name= SFSI(element).hasAttr('sfsi_validate');
    SFSI('input').removeClass('inputError'); // remove previous error 
    if (sfsi_validator(SFSI('input[name="sfsi_rss_display"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_rss_url"]'), 'url')) {
            showErrorSuc("error", "Error : Invalid Rss url ", 2);
            SFSI('input[name="sfsi_rss_url"]').addClass('inputError');

            return false;
        }
    }
    /* validate facebook */
    if (sfsi_validator(SFSI('input[name="sfsi_facebookPage_option"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_facebookPage_option"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_facebookPage_url"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid Facebook page url ", 2);
            SFSI('input[name="sfsi_facebookPage_url"]').addClass('inputError');

            return false;
        }
    }
    /* validate twitter user name */
    if (sfsi_validator(SFSI('input[name="sfsi_twitter_followme"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_followme"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_twitter_followUserName"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid Twitter UserName ", 2);
            SFSI('input[name="sfsi_twitter_followUserName"]').addClass('inputError');
            return false;
        }
    }
    // /* validate twitter about page */
    // if (sfsi_validator(SFSI('input[name="sfsi_twitter_aboutPage"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_aboutPage"]'), 'checked')) {
    //     if (!sfsi_validator(SFSI('#sfsi_twitter_aboutPageText'), 'blank')) {
    //         showErrorSuc("error", "Error : Tweet about my page is blank ", 2);
    //         SFSI('#sfsi_twitter_aboutPageText').addClass('inputError');
    //         return false;
    //     }
    // }
    /* twitter validation */
    if (sfsi_validator(SFSI('input[name="sfsi_twitter_page"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_page"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_twitter_pageURL"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid twitter page Url ", 2);
            SFSI('input[name="sfsi_twitter_pageURL"]').addClass('inputError');
            return false;
        }
    }

    /* youtube validation */
    if (sfsi_validator(SFSI('input[name="sfsi_youtube_page"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_youtube_page"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_youtube_pageUrl"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid youtube Url ", 2);
            SFSI('input[name="sfsi_youtube_pageUrl"]').addClass('inputError');
            return false;
        }
    }
    /* youtube validation */
    if (sfsi_validator(SFSI('input[name="sfsi_youtube_page"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_youtube_follow"]'), 'checked')) {
        cls = SFSI("input[name='sfsi_youtubeusernameorid']:checked").val();
        if (cls == 'name' && !sfsi_validator(SFSI('input[name="sfsi_ytube_user"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid youtube user name", 2);
            SFSI('input[name="sfsi_ytube_user"]').addClass('inputError');
            return false;
        }

        if (cls == 'id' && !sfsi_validator(SFSI('input[name="sfsi_ytube_chnlid"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid youtube Channel ID ", 2);
            SFSI('input[name="sfsi_ytube_user"]').addClass('inputError');
            return false;
        }
    }
    /* pinterest validation */
    if (sfsi_validator(SFSI('input[name="sfsi_pinterest_page"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_pinterest_page"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_pinterest_pageUrl"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid pinterest page url ", 2);
            SFSI('input[name="sfsi_pinterest_pageUrl"]').addClass('inputError');
            return false;
        }
    }
    /* instagram validation */
    if (sfsi_validator(SFSI('input[name="sfsi_instagram_display"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_instagram_pageUrl"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid Instagram url ", 2);
            SFSI('input[name="sfsi_instagram_pageUrl"]').addClass('inputError');
            return false;
        }
    }
    /* telegram validation */
    if (sfsi_validator(SFSI('input[name="sfsi_telegram_display"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_telegram_username"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid telegram username ", 2);
            SFSI('input[name="sfsi_telegram_username"]').addClass('inputError');
            return false;
        }
    }
    /* telegram validation */
    if (sfsi_validator(SFSI('input[name="sfsi_telegram_display"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_telegram_message"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid Message ", 2);
            SFSI('input[name="sfsi_telegram_message"]').addClass('inputError');
            return false;
        }
    }
    /* vk validation */
    if (sfsi_validator(SFSI('input[name="sfsi_vk_display"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_vk_pageURL"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid vk url ", 2);
            SFSI('input[name="sfsi_vk_pageURL"]').addClass('inputError');
            return false;
        }
    }
    /* ok validation */
    if (sfsi_validator(SFSI('input[name="sfsi_ok_display"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_ok_pageURL"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid ok url ", 2);
            SFSI('input[name="sfsi_ok_pageURL"]').addClass('inputError');
            return false;
        }
    }
    /* weibo validation */
    if (sfsi_validator(SFSI('input[name="sfsi_weibo_display"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_weibo_pageURL"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid weibo url ", 2);
            SFSI('input[name="sfsi_weibo_pageURL"]').addClass('inputError');
            return false;
        }
    }
    /* LinkedIn validation */
    if (sfsi_validator(SFSI('input[name="sfsi_linkedin_page"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_linkedin_page"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_linkedin_pageURL"]'), 'blank')) {
            showErrorSuc("error", "Error : Invalid LinkedIn page url ", 2);
            SFSI('input[name="sfsi_linkedin_pageURL"]').addClass('inputError');
            return false;
        }
    }
    if (sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendBusines"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendBusines"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendProductId"]'), 'blank') || !sfsi_validator(SFSI('input[name="sfsi_linkedin_recommendCompany"]'), 'blank')) {
            showErrorSuc("error", "Error : Please Enter Product Id and Company for LinkedIn Recommendation ", 2);
            SFSI('input[name="sfsi_linkedin_recommendProductId"]').addClass('inputError');
            SFSI('input[name="sfsi_linkedin_recommendCompany"]').addClass('inputError');
            return false;
        }
    }
    /* validate custom links */
    var er = 0;
    SFSI("input[name='sfsi_CustomIcon_links[]']").each(function () {

        //if(!sfsi_validator(SFSI(this),'blank') || !sfsi_validator(SFSI(SFSI(this)),'url') )
        if (!sfsi_validator(SFSI(this), 'blank')) {
            showErrorSuc("error", "Error : Please Enter a valid Custom link ", 2);
            SFSI(this).addClass('inputError');
            er = 1;
        }
    });
    if (!er) return true;
    else return false;
}

function sfsi_validationStep3() {
    SFSI('input').removeClass('inputError'); // remove previous error  
    /* validate shuffle effect  */
    if (sfsi_validator(SFSI('input[name="sfsi_shuffle_icons"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_shuffle_icons"]'), 'checked')) {
        if ((!sfsi_validator(SFSI('input[name="sfsi_shuffle_Firstload"]'), 'activte') || !sfsi_validator(SFSI('input[name="sfsi_shuffle_Firstload"]'), 'checked')) && (!sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'), 'activte') || !sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'), 'checked'))) {
            showErrorSuc("error", "Error : Please Chose a Shuffle option ", 3);
            SFSI('input[name="sfsi_shuffle_Firstload"]').addClass('inputError');
            SFSI('input[name="sfsi_shuffle_interval"]').addClass('inputError');
            return false;
        }
    }
    if (!sfsi_validator(SFSI('input[name="sfsi_shuffle_icons"]'), 'checked') && (sfsi_validator(SFSI('input[name="sfsi_shuffle_Firstload"]'), 'checked') || sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'), 'checked'))) {
        showErrorSuc("error", "Error : Please check \"Shuffle them automatically\" option also ", 3);
        SFSI('input[name="sfsi_shuffle_Firstload"]').addClass('inputError');
        SFSI('input[name="sfsi_shuffle_interval"]').addClass('inputError');
        return false;
    }

    /* validate twitter user name */
    if (sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_shuffle_interval"]'), 'checked')) {

        if (!sfsi_validator(SFSI('input[name="sfsi_shuffle_intervalTime"]'), 'blank') || !sfsi_validator(SFSI('input[name="sfsi_shuffle_intervalTime"]'), 'int')) {
            showErrorSuc("error", "Error : Invalid shuffle time interval", 3);
            SFSI('input[name="sfsi_shuffle_intervalTime"]').addClass('inputError');
            return false;
        }
    }
    return true;
}

function sfsi_validationStep4() {
    //var class_name= SFSI(element).hasAttr('sfsi_validate');
    /* validate email */
    if (sfsi_validator(SFSI('input[name="sfsi_email_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_email_countsDisplay"]'), 'checked')) {
        if (SFSI('input[name="sfsi_email_countsFrom"]:checked').val() == 'manual') {
            if (!sfsi_validator(SFSI('input[name="sfsi_email_manualCounts"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter manual counts for Email icon ", 4);
                SFSI('input[name="sfsi_email_manualCounts"]').addClass('inputError');
                return false;
            }
        }
    }
    /* validate RSS count */
    if (sfsi_validator(SFSI('input[name="sfsi_rss_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_rss_countsDisplay"]'), 'checked')) {
        if (!sfsi_validator(SFSI('input[name="sfsi_rss_manualCounts"]'), 'blank')) {
            showErrorSuc("error", "Error : Please Enter manual counts for Rss icon ", 4);
            SFSI('input[name="sfsi_rss_countsDisplay"]').addClass('inputError');
            return false;
        }
    }
    /* validate facebook */
    if (sfsi_validator(SFSI('input[name="sfsi_facebook_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_facebook_countsDisplay"]'), 'checked')) {
        /*if(SFSI('input[name="sfsi_facebook_countsFrom"]:checked').val()=='likes' )
        {   
          if(!sfsi_validator(SFSI('input[name="sfsi_facebook_PageLink"]'),'blank'))
            {   showErrorSuc("error","Error : Please Enter facebook page Url ",4);
                SFSI('input[name="sfsi_facebook_PageLink"]').addClass('inputError');
                return false;
            }      
        } */
        if (SFSI('input[name="sfsi_facebook_countsFrom"]:checked').val() == 'manual') {
            if (!sfsi_validator(SFSI('input[name="sfsi_facebook_manualCounts"]'), 'blank') && !sfsi_validator(SFSI('input[name="sfsi_facebook_manualCounts"]'), 'url')) {
                showErrorSuc("error", "Error : Please Enter a valid facebook manual counts ", 4);
                SFSI('input[name="sfsi_facebook_manualCounts"]').addClass('inputError');
                return false;
            }
        }
    }

    /* validate twitter */
    if (sfsi_validator(SFSI('input[name="sfsi_twitter_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_twitter_countsDisplay"]'), 'checked')) {
        if (SFSI('input[name="sfsi_twitter_countsFrom"]:checked').val() == 'source') {
            if (!sfsi_validator(SFSI('input[name="tw_consumer_key"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a valid consumer key", 4);
                SFSI('input[name="tw_consumer_key"]').addClass('inputError');
                return false;
            }
            if (!sfsi_validator(SFSI('input[name="tw_consumer_secret"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a valid consume secret ", 4);
                SFSI('input[name="tw_consumer_secret"]').addClass('inputError');
                return false;
            }
            if (!sfsi_validator(SFSI('input[name="tw_oauth_access_token"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a valid oauth access token", 4);
                SFSI('input[name="tw_oauth_access_token"]').addClass('inputError');
                return false;
            }
            if (!sfsi_validator(SFSI('input[name="tw_oauth_access_token_secret"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a oAuth access token secret", 4);
                SFSI('input[name="tw_oauth_access_token_secret"]').addClass('inputError');
                return false;
            }
        }
        if (SFSI('input[name="sfsi_linkedIn_countsFrom"]:checked').val() == 'manual') {

            if (!sfsi_validator(SFSI('input[name="sfsi_twitter_manualCounts"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter Twitter manual counts ", 4);
                SFSI('input[name="sfsi_twitter_manualCounts"]').addClass('inputError');
                return false;
            }
        }
    }
    /* validate LinkedIn */
    if (sfsi_validator(SFSI('input[name="sfsi_linkedIn_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_linkedIn_countsDisplay"]'), 'checked')) {
        if (SFSI('input[name="sfsi_linkedIn_countsFrom"]:checked').val() == 'follower') {
            if (!sfsi_validator(SFSI('input[name="ln_company"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a valid company name", 4);
                SFSI('input[name="ln_company"]').addClass('inputError');
                return false;
            }
            if (!sfsi_validator(SFSI('input[name="ln_api_key"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a valid API key ", 4);
                SFSI('input[name="ln_api_key"]').addClass('inputError');
                return false;
            }
            if (!sfsi_validator(SFSI('input[name="ln_secret_key"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a valid secret ", 4);
                SFSI('input[name="ln_secret_key"]').addClass('inputError');
                return false;
            }
            if (!sfsi_validator(SFSI('input[name="ln_oAuth_user_token"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a oAuth Access Token", 4);
                SFSI('input[name="ln_oAuth_user_token"]').addClass('inputError');
                return false;
            }
        }
        if (SFSI('input[name="sfsi_linkedIn_countsFrom"]:checked').val() == 'manual') {
            if (!sfsi_validator(SFSI('input[name="sfsi_linkedIn_manualCounts"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter LinkedIn manual counts ", 4);
                SFSI('input[name="sfsi_linkedIn_manualCounts"]').addClass('inputError');
                return false;
            }
        }
    }
    /* validate youtube */
    if (sfsi_validator(SFSI('input[name="sfsi_youtube_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_youtube_countsDisplay"]'), 'checked')) {
        if (SFSI('input[name="sfsi_youtube_countsFrom"]:checked').val() == 'subscriber') {
            if (
                !sfsi_validator(SFSI('input[name="sfsi_youtube_user"]'), 'blank') &&
                !sfsi_validator(SFSI('input[name="sfsi_youtube_channelId"]'), 'blank')
            ) {
                showErrorSuc("error", "Error : Please Enter a youtube user name or channel id", 4);
                SFSI('input[name="sfsi_youtube_user"]').addClass('inputError');
                SFSI('input[name="sfsi_youtube_channelId"]').addClass('inputError');
                return false;
            }
        }
        if (SFSI('input[name="sfsi_youtube_countsFrom"]:checked').val() == 'manual') {
            if (!sfsi_validator(SFSI('input[name="sfsi_youtube_manualCounts"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter youtube manual counts ", 4);
                SFSI('input[name="sfsi_youtube_manualCounts"]').addClass('inputError');
                return false;
            }
        }
    }
    /* validate pinterest */
    if (sfsi_validator(SFSI('input[name="sfsi_pinterest_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_pinterest_countsDisplay"]'), 'checked')) {
        if (SFSI('input[name="sfsi_pinterest_countsFrom"]:checked').val() == 'manual') {
            if (!sfsi_validator(SFSI('input[name="sfsi_pinterest_manualCounts"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter Pinterest manual counts ", 4);
                SFSI('input[name="sfsi_pinterest_manualCounts"]').addClass('inputError');
                return false;
            }
        }
    }
    /* validate instagram */
    if (sfsi_validator(SFSI('input[name="sfsi_instagram_countsDisplay"]'), 'activte') && sfsi_validator(SFSI('input[name="sfsi_instagram_countsDisplay"]'), 'checked')) {
        if (SFSI('input[name="sfsi_instagram_countsFrom"]:checked').val() == 'manual') {
            if (!sfsi_validator(SFSI('input[name="sfsi_instagram_manualCounts"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter Instagram manual counts ", 4);
                SFSI('input[name="sfsi_instagram_manualCounts"]').addClass('inputError');
                return false;
            }
        }
        if (SFSI('input[name="sfsi_instagram_countsFrom"]:checked').val() == 'followers') {
            if (!sfsi_validator(SFSI('input[name="sfsi_instagram_User"]'), 'blank')) {
                showErrorSuc("error", "Error : Please Enter a instagram user name", 4);
                SFSI('input[name="sfsi_instagram_User"]').addClass('inputError');
                return false;
            }
        }
    }
    return true;
}

function sfsi_validationStep5() {
    //var class_name= SFSI(element).hasAttr('sfsi_validate');
    /* validate size   */
    if (!sfsi_validator(SFSI('input[name="sfsi_icons_size"]'), 'int')) {
        showErrorSuc("error", "Error : Please enter a numeric value only ", 5);
        SFSI('input[name="sfsi_icons_size"]').addClass('inputError');
        return false;
    }
    if (parseInt(SFSI('input[name="sfsi_icons_size"]').val()) > 100) {
        showErrorSuc("error", "Error : Icons Size allow  100px maximum ", 5);
        SFSI('input[name="sfsi_icons_size"]').addClass('inputError');
        return false;
    }
    if (parseInt(SFSI('input[name="sfsi_icons_size"]').val()) <= 0) {
        showErrorSuc("error", "Error : Icons Size should be more than 0 ", 5);
        SFSI('input[name="sfsi_icons_size"]').addClass('inputError');
        return false;
    }
    /* validate spacing   */
    if (!sfsi_validator(SFSI('input[name="sfsi_icons_spacing"]'), 'int')) {
        showErrorSuc("error", "Error : Please enter a numeric value only ", 5);
        SFSI('input[name="sfsi_icons_spacing"]').addClass('inputError');
        return false;
    }
    if (parseInt(SFSI('input[name="sfsi_icons_spacing"]').val()) < 0) {
        showErrorSuc("error", "Error : Icons Spacing should be 0 or more", 5);
        SFSI('input[name="sfsi_icons_spacing"]').addClass('inputError');
        return false;
    }
    /* icons per row  spacing   */
    if (!sfsi_validator(SFSI('input[name="sfsi_icons_perRow"]'), 'int')) {
        showErrorSuc("error", "Error : Please enter a numeric value only ", 5);
        SFSI('input[name="sfsi_icons_perRow"]').addClass('inputError');
        return false;
    }
    if (parseInt(SFSI('input[name="sfsi_icons_perRow"]').val()) <= 0) {
        showErrorSuc("error", "Error : Icons Per row should be more than 0", 5);
        SFSI('input[name="sfsi_icons_perRow"]').addClass('inputError');
        return false;
    }
    /* validate icons effects   */
    // if(SFSI('input[name="sfsi_icons_float"]:checked').val()=="yes" && SFSI('input[name="sfsi_icons_stick"]:checked').val()=="yes")
    // {   
    // 	showErrorSuc("error","Error : Only one allow from Sticking & floating ",5);
    // 	SFSI('input[name="sfsi_icons_float"][value="no"]').prop("checked", true);
    // 	return false;
    // }
    return true;
}

function sfsi_validationStep7() {
    //var class_name= SFSI(element).hasAttr('sfsi_validate');
    /* validate border thikness   */
    if (!sfsi_validator(SFSI('input[name="sfsi_popup_border_thickness"]'), 'int')) {
        showErrorSuc("error", "Error : Please enter a numeric value only ", 7);
        SFSI('input[name="sfsi_popup_border_thickness"]').addClass('inputError');
        return false;
    }
    /* validate fotn size   */
    if (!sfsi_validator(SFSI('input[name="sfsi_popup_fontSize"]'), 'int')) {
        showErrorSuc("error", "Error : Please enter a numeric value only ", 7);
        SFSI('input[name="sfsi_popup_fontSize"]').addClass('inputError');
        return false;
    }
    /* validate pop up shown    */
    if (SFSI('input[name="sfsi_Shown_pop"]:checked').val() == 'once') {

        if (!sfsi_validator(SFSI('input[name="sfsi_Shown_popupOnceTime"]'), 'blank') && !sfsi_validator(SFSI('input[name="sfsi_Shown_popupOnceTime"]'), 'url')) {
            showErrorSuc("error", "Error : Please Enter a valid pop up shown time ", 7);
            SFSI('input[name="sfsi_Shown_popupOnceTime"]').addClass('inputError');
            return false;
        }
    }
    /* validate page ids   */
    if (SFSI('input[name="sfsi_Show_popupOn"]:checked').val() == 'selectedpage') {
        if (!sfsi_validator(SFSI('input[name="sfsi_Show_popupOn"]'), 'blank')) {
            showErrorSuc("error", "Error : Please Enter page ids with comma  ", 7);
            SFSI('input[name="sfsi_Show_popupOn"]').addClass('inputError');
            return false;
        }
    }
    /* validate spacing   */
    if (!sfsi_validator(SFSI('input[name="sfsi_icons_spacing"]'), 'int')) {
        showErrorSuc("error", "Error : Please enter a numeric value only ", 7);
        SFSI('input[name="sfsi_icons_spacing"]').addClass('inputError');
        return false;
    }
    /* icons per row  spacing   */
    if (!sfsi_validator(SFSI('input[name="sfsi_icons_perRow"]'), 'int')) {
        showErrorSuc("error", "Error : Please enter a numeric value only ", 7);
        SFSI('input[name="sfsi_icons_perRow"]').addClass('inputError');
        return false;
    }
    return true;
}

function sfsi_validator(element, valType) {
    var Vurl = new RegExp("^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\@\?\'\\\+&amp;%\$#\=~_\-]+))*$");
    //var Vurl = /http:\/\/[A-Za-z0-9\.-]{3,}\.[A-Za-z]{3}/;

    switch (valType) {
        case "blank":
            if (!element.val().trim()) return false;
            else return true;
            break;
        case "url":
            if (!Vurl.test(element.val().trim())) return false;
            else return true;
            break;
        case "checked":
            if (!element.attr('checked') === true) return false;
            else return true;
            break;
        case "activte":
            if (!element.attr('disabled')) return true;
            else return false;
            break;
        case "int":
            if (!isNaN(element.val())) return true;
            else return false;
            break;

    }
}

function afterIconSuccess(s, nonce) {
    if (s.res = "success") {
        var i = s.key + 1,
            e = s.element,
            t = e + 1;
        SFSI("#total_cusotm_icons").val(s.element);
        SFSI(".upload-overlay").hide("slow");
        SFSI(".uperror").html("");
        showErrorSuc("success", "Custom Icon updated successfully", 1);
        d = new Date();

        var ele = SFSI(".notice_custom_icons_premium");

        SFSI("li.custom:last-child").removeClass("bdr_btm_non");
        SFSI("li.custom:last-child").children("span.custom-img").children("img").attr("src", s.img_path + "?" + d.getTime());
        SFSI("input[name=sfsiICON_" + s.key + "]").removeAttr("ele-type");
        SFSI("input[name=sfsiICON_" + s.key + "]").removeAttr("isnew");
        icons_name = SFSI("li.custom:last-child").find("input.styled").attr("name");
        var n = icons_name.split("_");
        s.key = s.key, s.img_path += "?" + d.getTime(), 5 > e && SFSI(".icn_listing").append('<li id="c' + i + '" class="custom bdr_btm_non"><div class="radio_section tb_4_ck"><span class="checkbox" dynamic_ele="yes" style=" 0px 0px;"></span><input name="sfsiICON_' + i + '"  type="checkbox" value="yes" class="styled" style="display:none;" element-type="cusotm-icon" isNew="yes" /></div> <span class="custom-img"><img src="' + SFSI("#plugin_url").val() + 'images/custom.png" id="CImg_' + i + '" alt="error"  /> </span> <span class="custom custom-txt">Custom' + t + ' </span> <div class="right_info"> <p><span>It depends:</span> Upload a custom icon if you have other accounts/websites you want to link to.</p><div class="inputWrapper"></div></li>'),
            SFSI(".custom_section").show(),
            SFSI('<div class="row  sfsiICON_' + s.key + ' cm_lnk"> <h2 class="custom"> <span class="customstep2-img"> <img   src="' + s.img_path + "?" + d.getTime() + '" style="border-radius:48%" alt="error" /> </span> <span class="sfsiCtxt">Custom ' + e + '</span> </h2> <div class="inr_cont "><p>Where do you want this icon to link to?</p> <p class="radio_section fb_url custom_section  sfsiICON_' + s.key + '" ><label>Link :</label><input file-id="' + s.key + '" name="sfsi_CustomIcon_links[]" type="text" value="" placeholder="http://" class="add" /></p></div></div>').insertBefore('.notice_custom_icons_premium');
        //SFSI(".custom-links").append(' <div class="row  sfsiICON_' + s.key + ' cm_lnk"> <h2 class="custom"> <span class="customstep2-img"> <img   src="' + s.img_path + "?" + d.getTime() + '" style="border-radius:48%" /> </span> <span class="sfsiCtxt">Custom ' + e + '</span> </h2> <div class="inr_cont "><p>Where do you want this icon to link to?</p> <p class="radio_section fb_url custom_section  sfsiICON_' + s.key + '" ><label>Link :</label><input file-id="' + s.key + '" name="sfsi_CustomIcon_links[]" type="text" value="" placeholder="http://" class="add" /></p></div></div>');
        SFSI(".notice_custom_icons_premium").show();
        SFSI("#c" + s.key).append('<input type="hidden" name="nonce" value="' + nonce + '">');
        var o = SFSI("div.custom_m").find("div.mouseover_field").length;
        SFSI("div.custom_m").append(0 == o % 2 ? '<div class="clear"> </div> <div class="mouseover_field custom_section sfsiICON_' + s.key + '"><label>Custom ' + e + ':</label><input name="sfsi_custom_MouseOverTexts[]" value="" type="text" file-id="' + s.key + '" /></div>' : '<div class="cHover " ><div class="mouseover_field custom_section sfsiICON_' + s.key + '"><label>Custom ' + e + ':</label><input name="sfsi_custom_MouseOverTexts[]" value="" type="text" file-id="' + s.key + '" /></div>'),
            SFSI("ul.share_icon_order").append('<li class="custom_iconOrder sfsiICON_' + s.key + '" data-index="" element-id="' + s.key + '" id=""><a href="#" title="Custom Icon" ><img src="' + s.img_path + '" alt="Linked In" class="sfcm"/></a></li>'),
            SFSI("ul.sfsi_sample_icons").append('<li class="sfsiICON_' + s.key + '" element-id="' + s.key + '" ><div><img src="' + s.img_path + '" alt="Linked In" class="sfcm"/><span class="sfsi_Cdisplay">12k</span></div></li>'),
            sfsi_update_index(), update_Sec5Iconorder(), sfsi_update_step1(), sfsi_update_step2(),
            sfsi_update_step5(), SFSI(".upload-overlay").css("pointer-events", "auto"), sfsi_showPreviewCounts(),
            afterLoad();
    }
}

function beforeIconSubmit(s) {
    if (SFSI(".uperror").html("Uploading....."), window.File && window.FileReader && window.FileList && window.Blob) {
        SFSI(s).val() || SFSI(".uperror").html("File is empty");
        var i = s.files[0].size,
            e = s.files[0].type;
        switch (e) {
            case "image/png":
            case "image/gif":
            case "image/jpeg":
            case "image/pjpeg":
                break;

            default:
                return SFSI(".uperror").html("Unsupported file"), !1;
        }
        return i > 1048576 ? (SFSI(".uperror").html("Image should be less than 1 MB"), !1) : !0;
    }
    return !0;
}

function bytesToSize(s) {
    var i = ["Bytes", "KB", "MB", "GB", "TB"];
    if (0 == s) return "0 Bytes";
    var e = parseInt(Math.floor(Math.log(s) / Math.log(1024)));
    return Math.round(s / Math.pow(1024, e), 2) + " " + i[e];
}

function showErrorSuc(s, i, e) {
    if ("error" == s) var t = "errorMsg";
    else var t = "sucMsg";
    return SFSI(".tab" + e + ">." + t).html(i), SFSI(".tab" + e + ">." + t).show(),
        SFSI(".tab" + e + ">." + t).effect("highlight", {}, 5e3), setTimeout(function () {
            SFSI("." + t).slideUp("slow");
        }, 5e3), !1;
}

function beForeLoad() {
    SFSI(".loader-img").show(), SFSI(".save_button >a").html("Saving..."), SFSI(".save_button >a").css("pointer-events", "none");
}

function afterLoad() {
    SFSI("input").removeClass("inputError"), SFSI(".save_button >a").html("Save"), SFSI(".tab10>div.save_button >a").html("Save All Settings"),
        SFSI(".save_button >a").css("pointer-events", "auto"), SFSI(".save_button >a").removeAttr("onclick"),
        SFSI(".loader-img").hide();
}

function sfsi_make_popBox() {
    var s = 0;
    SFSI(".sfsi_sample_icons >li").each(function () {
            "none" != SFSI(this).css("display") && (s = 1);
        }),
        0 == s ? SFSI(".sfsi_Popinner").hide() : SFSI(".sfsi_Popinner").show(),
        "" != SFSI('input[name="sfsi_popup_text"]').val() ? (SFSI(".sfsi_Popinner >h2").html(SFSI('input[name="sfsi_popup_text"]').val()),
            SFSI(".sfsi_Popinner >h2").show()) : SFSI(".sfsi_Popinner >h2").hide(), SFSI(".sfsi_Popinner").css({
            "border-color": SFSI('input[name="sfsi_popup_border_color"]').val(),
            "border-width": SFSI('input[name="sfsi_popup_border_thickness"]').val(),
            "border-style": "solid"
        }),
        SFSI(".sfsi_Popinner").css("background-color", SFSI('input[name="sfsi_popup_background_color"]').val()),
        SFSI(".sfsi_Popinner h2").css("font-family", SFSI("#sfsi_popup_font").val()), SFSI(".sfsi_Popinner h2").css("font-style", SFSI("#sfsi_popup_fontStyle").val()),
        SFSI(".sfsi_Popinner >h2").css("font-size", parseInt(SFSI('input[name="sfsi_popup_fontSize"]').val())),
        SFSI(".sfsi_Popinner >h2").css("color", SFSI('input[name="sfsi_popup_fontColor"]').val() + " !important"),
        "yes" == SFSI('input[name="sfsi_popup_border_shadow"]:checked').val() ? SFSI(".sfsi_Popinner").css("box-shadow", "12px 30px 18px #CCCCCC") : SFSI(".sfsi_Popinner").css("box-shadow", "none");
}

function sfsi_stick_widget(s) {
    0 == initTop.length && (SFSI(".sfsi_widget").each(function (s) {
            initTop[s] = SFSI(this).position().top;
        })
        //  console.log(initTop)
    );
    var i = SFSI(window).scrollTop(),
        e = [],
        t = [];
    SFSI(".sfsi_widget").each(function (s) {
        e[s] = SFSI(this).position().top, t[s] = SFSI(this);
    });
    var n = !1;
    for (var o in e) {
        var a = parseInt(o) + 1;
        e[o] < i && e[a] > i && a < e.length ? (SFSI(t[o]).css({
            position: "fixed",
            top: s
        }), SFSI(t[a]).css({
            position: "",
            top: initTop[a]
        }), n = !0) : SFSI(t[o]).css({
            position: "",
            top: initTop[o]
        });
    }
    if (!n) {
        var r = e.length - 1,
            c = -1;
        e.length > 1 && (c = e.length - 2), initTop[r] < i ? (SFSI(t[r]).css({
            position: "fixed",
            top: s
        }), c >= 0 && SFSI(t[c]).css({
            position: "",
            top: initTop[c]
        })) : (SFSI(t[r]).css({
            position: "",
            top: initTop[r]
        }), c >= 0 && e[c] < i);
    }
}

function sfsi_setCookie(s, i, e) {
    var t = new Date();
    t.setTime(t.getTime() + 1e3 * 60 * 60 * 24 * e);
    var n = "expires=" + t.toGMTString();
    document.cookie = s + "=" + i + "; " + n;
}

function sfsfi_getCookie(s) {
    for (var i = s + "=", e = document.cookie.split(";"), t = 0; t < e.length; t++) {
        var n = e[t].trim();
        if (0 == n.indexOf(i)) return n.substring(i.length, n.length);
    }
    return "";
}

function sfsi_hideFooter() {}

window.onerror = function () {},
    SFSI = jQuery,
    SFSI(window).on('load', function () {
        SFSI("#sfpageLoad").fadeOut(2e3);
    });

//changes done {Monad}
function selectText(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select();
    } else if (window.getSelection()) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    }
}

function create_suscriber_form() {
    //Popbox customization
    "no" == SFSI('input[name="sfsi_form_adjustment"]:checked').val() ? SFSI(".sfsi_subscribe_Popinner").css({
        "width": parseInt(SFSI('input[name="sfsi_form_width"]').val()),
        "height": parseInt(SFSI('input[name="sfsi_form_height"]').val())
    }) : SFSI(".sfsi_subscribe_Popinner").css({
        "width": '',
        "height": ''
    });

    "yes" == SFSI('input[name="sfsi_form_adjustment"]:checked').val() ? SFSI(".sfsi_html > .sfsi_subscribe_Popinner").css({
        "width": "100%"
    }) : '';

    "yes" == SFSI('input[name="sfsi_form_border"]:checked').val() ? SFSI(".sfsi_subscribe_Popinner").css({
        "border": SFSI('input[name="sfsi_form_border_thickness"]').val() + "px solid " + SFSI('input[name="sfsi_form_border_color"]').val()
    }) : SFSI(".sfsi_subscribe_Popinner").css("border", "none");

    SFSI('input[name="sfsi_form_background"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").css("background-color", SFSI('input[name="sfsi_form_background"]').val())) : '';

    //Heading customization
    SFSI('input[name="sfsi_form_heading_text"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner > form > h5").html(SFSI('input[name="sfsi_form_heading_text"]').val())) : SFSI(".sfsi_subscribe_Popinner > form > h5").html('');

    SFSI('#sfsi_form_heading_font').val() != "" ? (SFSI(".sfsi_subscribe_Popinner > form > h5").css("font-family", SFSI("#sfsi_form_heading_font").val())) : '';

    if (SFSI('#sfsi_form_heading_fontstyle').val() != 'bold') {
        SFSI('#sfsi_form_heading_fontstyle').val() != "" ? (SFSI(".sfsi_subscribe_Popinner > form > h5").css("font-style", SFSI("#sfsi_form_heading_fontstyle").val())) : '';
        SFSI(".sfsi_subscribe_Popinner > form > h5").css("font-weight", '');
    } else {
        SFSI('#sfsi_form_heading_fontstyle').val() != "" ? (SFSI(".sfsi_subscribe_Popinner > form > h5").css("font-weight", "bold")) : '';
        SFSI(".sfsi_subscribe_Popinner > form > h5").css("font-style", '');
    }

    SFSI('input[name="sfsi_form_heading_fontcolor"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner > form > h5").css("color", SFSI('input[name="sfsi_form_heading_fontcolor"]').val())) : '';

    SFSI('input[name="sfsi_form_heading_fontsize"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner > form > h5").css({
        "font-size": parseInt(SFSI('input[name="sfsi_form_heading_fontsize"]').val())
    })) : '';

    SFSI('#sfsi_form_heading_fontalign').val() != "" ? (SFSI(".sfsi_subscribe_Popinner > form > h5").css("text-align", SFSI("#sfsi_form_heading_fontalign").val())) : '';

    //Field customization
    SFSI('input[name="sfsi_form_field_text"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').attr("placeholder", SFSI('input[name="sfsi_form_field_text"]').val())) : SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').attr("placeholder", '');

    SFSI('input[name="sfsi_form_field_text"]').val() != "" ? (SFSI(".sfsi_left_container > .sfsi_subscribe_Popinner").find('input[name="email"]').val(SFSI('input[name="sfsi_form_field_text"]').val())) : SFSI(".sfsi_left_container > .sfsi_subscribe_Popinner").find('input[name="email"]').val('');

    SFSI('input[name="sfsi_form_field_text"]').val() != "" ? (SFSI(".like_pop_box > .sfsi_subscribe_Popinner").find('input[name="email"]').val(SFSI('input[name="sfsi_form_field_text"]').val())) : SFSI(".like_pop_box > .sfsi_subscribe_Popinner").find('input[name="email"]').val('');

    SFSI('#sfsi_form_field_font').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css("font-family", SFSI("#sfsi_form_field_font").val())) : '';

    if (SFSI('#sfsi_form_field_fontstyle').val() != "bold") {
        SFSI('#sfsi_form_field_fontstyle').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css("font-style", SFSI("#sfsi_form_field_fontstyle").val())) : '';
        SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css("font-weight", '');
    } else {
        SFSI('#sfsi_form_field_fontstyle').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css("font-weight", 'bold')) : '';
        SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css("font-style", '');
    }

    SFSI('input[name="sfsi_form_field_fontcolor"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css("color", SFSI('input[name="sfsi_form_field_fontcolor"]').val())) : '';

    SFSI('input[name="sfsi_form_field_fontsize"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css({
        "font-size": parseInt(SFSI('input[name="sfsi_form_field_fontsize"]').val())
    })) : '';

    SFSI('#sfsi_form_field_fontalign').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="email"]').css("text-align", SFSI("#sfsi_form_field_fontalign").val())) : '';

    //Button customization
    SFSI('input[name="sfsi_form_button_text"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').attr("value", SFSI('input[name="sfsi_form_button_text"]').val())) : '';

    SFSI('#sfsi_form_button_font').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("font-family", SFSI("#sfsi_form_button_font").val())) : '';

    if (SFSI('#sfsi_form_button_fontstyle').val() != "bold") {
        SFSI('#sfsi_form_button_fontstyle').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("font-style", SFSI("#sfsi_form_button_fontstyle").val())) : '';
        SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("font-weight", '');
    } else {
        SFSI('#sfsi_form_button_fontstyle').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("font-weight", 'bold')) : '';
        SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("font-style", '');
    }

    SFSI('input[name="sfsi_form_button_fontcolor"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("color", SFSI('input[name="sfsi_form_button_fontcolor"]').val())) : '';

    SFSI('input[name="sfsi_form_button_fontsize"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css({
        "font-size": parseInt(SFSI('input[name="sfsi_form_button_fontsize"]').val())
    })) : '';

    SFSI('#sfsi_form_button_fontalign').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("text-align", SFSI("#sfsi_form_button_fontalign").val())) : '';

    SFSI('input[name="sfsi_form_button_background"]').val() != "" ? (SFSI(".sfsi_subscribe_Popinner").find('input[name="subscribe"]').css("background-color", SFSI('input[name="sfsi_form_button_background"]').val())) : '';

    var innerHTML = SFSI(".sfsi_html > .sfsi_subscribe_Popinner").html();
    var styleCss = SFSI(".sfsi_html > .sfsi_subscribe_Popinner").attr("style");
    innerHTML = '<div style="' + styleCss + '">' + innerHTML + '</div>';
    SFSI(".sfsi_subscription_html > xmp").html(innerHTML);

    /*var data = {
		action:"getForm",
		heading: SFSI('input[name="sfsi_form_heading_text"]').val(),
		placeholder:SFSI('input[name="sfsi_form_field_text"]').val(),
		button:SFSI('input[name="sfsi_form_button_text"]').val()
	};
	SFSI.ajax({
        url:sfsi_icon_ajax_object.ajax_url,
        type:"post",
        data:data,
        success:function(s) {
			SFSI(".sfsi_subscription_html").html(s);
		}
    });*/
}

var global_error = 0;
if (typeof SFSI != 'undefined') {

    function sfsi_dismiss_notice(btnClass, ajaxAction) {

        var btnClass = "." + btnClass;

        SFSI(document).on("click", btnClass, function () {

            SFSI.ajax({
                url: sfsi_icon_ajax_object.ajax_url,
                type: "post",
                data: {
                    action: ajaxAction
                },
                success: function (e) {
                    if (false != e) {
                        SFSI(btnClass).parent().remove();
                    }
                }
            });
        });
    }
}

SFSI(document).ready(function (s) {
    jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
        if (jQuery(a_container).css('display') !== "none") {
            sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
        }
    })
    sfsi_resize_icons_container();

    var arrDismiss = [

        {
            "btnClass": "sfsi-notice-dismiss",
            "action": "sfsi_dismiss_lang_notice"
        },

        {
            "btnClass": "sfsi-AddThis-notice-dismiss",
            "action": "sfsi_dismiss_addThis_icon_notice"
        },

        {
            "btnClass": "sfsi_error_reporting_notice-dismiss",
            "action": "sfsi_dismiss_error_reporting_notice"
        }
    ];

    SFSI.each(arrDismiss, function (key, valueObj) {
        sfsi_dismiss_notice(valueObj.btnClass, valueObj.action);
    });

    //changes done {Monad}
    SFSI(".tab_3_icns").on("click", ".cstomskins_upload", function () {
        SFSI(".cstmskins-overlay").show("slow", function () {
            e = 0;
        });
    });
    /*SFSI("#custmskin_clspop").live("click", function() {*/
    SFSI(document).on("click", '#custmskin_clspop', function () {
        SFSI_done();
        SFSI(".cstmskins-overlay").hide("slow");
    });

    create_suscriber_form();
    SFSI('input[name="sfsi_form_heading_text"], input[name="sfsi_form_border_thickness"], input[name="sfsi_form_height"], input[name="sfsi_form_width"], input[name="sfsi_form_heading_fontsize"], input[name="sfsi_form_field_text"], input[name="sfsi_form_field_fontsize"], input[name="sfsi_form_button_text"], input[name="sfsi_form_button_fontsize"]').on("keyup", create_suscriber_form);

    SFSI('input[name="sfsi_form_border_color"], input[name="sfsi_form_background"] ,input[name="sfsi_form_heading_fontcolor"], input[name="sfsi_form_field_fontcolor"] ,input[name="sfsi_form_button_fontcolor"],input[name="sfsi_form_button_background"]').on("focus", create_suscriber_form);

    SFSI("#sfsi_form_heading_font, #sfsi_form_heading_fontstyle, #sfsi_form_heading_fontalign, #sfsi_form_field_font, #sfsi_form_field_fontstyle, #sfsi_form_field_fontalign, #sfsi_form_button_font, #sfsi_form_button_fontstyle, #sfsi_form_button_fontalign").on("change", create_suscriber_form);

    /*SFSI(".radio").live("click", function() {*/
    SFSI(document).on("click", '.radio', function () {

        var s = SFSI(this).parent().find("input:radio:first");
        var inputName = s.attr("name");
        // console.log(inputName);

        var inputChecked = s.attr("checked");

        switch (inputName) {
            case 'sfsi_form_adjustment':
                if (s.val() == 'no')
                    s.parents(".row_tab").next(".row_tab").show("fast");
                else
                    s.parents(".row_tab").next(".row_tab").hide("fast");
                create_suscriber_form()
                break;
            case 'sfsi_form_border':
                if (s.val() == 'yes')
                    s.parents(".row_tab").next(".row_tab").show("fast");
                else
                    s.parents(".row_tab").next(".row_tab").hide("fast");
                create_suscriber_form()
                break;
            case 'sfsi_icons_suppress_errors':

                SFSI('input[name="sfsi_icons_suppress_errors"]').removeAttr('checked');

                if (s.val() == 'yes')
                    SFSI('input[name="sfsi_icons_suppress_errors"][value="yes"]').attr('checked', 'true');
                else
                    SFSI('input[name="sfsi_icons_suppress_errors"][value="no"]').attr('checked', 'true');
                break;
            case 'sfsi_responsive_icons_end_post':
                if ("yes" == s.val()) {
                    jQuery('.sfsi_responsive_icon_option_li.sfsi_responsive_show').show();
                } else {
                    jQuery('.sfsi_responsive_icon_option_li.sfsi_responsive_show').hide();
                }
        }
    });

    SFSI('#sfsi_form_border_color').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                create_suscriber_form()
            },
            clear: function () {
                create_suscriber_form()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_form_background').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                create_suscriber_form()
            },
            clear: function () {
                create_suscriber_form()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_form_heading_fontcolor').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                create_suscriber_form()
            },
            clear: function () {
                create_suscriber_form()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_form_button_fontcolor').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                create_suscriber_form()
            },
            clear: function () {
                create_suscriber_form()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_form_button_background').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                create_suscriber_form()
            },
            clear: function () {
                create_suscriber_form()
            },
            hide: true,
            palettes: true
        });
    //changes done {Monad}

    function i() {
        SFSI(".uperror").html(""), afterLoad();
        var s = SFSI('input[name="' + SFSI("#upload_id").val() + '"]');
        s.removeAttr("checked");
        var i = SFSI(s).parent().find("span:first");
        return SFSI(i).css("background-position", "0px 0px"), SFSI(".upload-overlay").hide("slow"),
            !1;
    }
    SFSI("#accordion").accordion({
            collapsible: !0,
            active: !1,
            heightStyle: "content",
            event: "click",
            beforeActivate: function (s, i) {
                if (i.newHeader[0]) var e = i.newHeader,
                    t = e.next(".ui-accordion-content");
                else var e = i.oldHeader,
                    t = e.next(".ui-accordion-content");
                var n = "true" == e.attr("aria-selected");
                return e.toggleClass("ui-corner-all", n).toggleClass("accordion-header-active ui-state-active ui-corner-top", !n).attr("aria-selected", (!n).toString()),
                    e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", n).toggleClass("ui-icon-triangle-1-s", !n),
                    t.toggleClass("accordion-content-active", !n), n ? t.slideUp() : t.slideDown(), !1;
            }
        }),
        SFSI("#accordion1").accordion({
            collapsible: !0,
            active: !1,
            heightStyle: "content",
            event: "click",
            beforeActivate: function (s, i) {
                if (i.newHeader[0]) var e = i.newHeader,
                    t = e.next(".ui-accordion-content");
                else var e = i.oldHeader,
                    t = e.next(".ui-accordion-content");
                var n = "true" == e.attr("aria-selected");
                return e.toggleClass("ui-corner-all", n).toggleClass("accordion-header-active ui-state-active ui-corner-top", !n).attr("aria-selected", (!n).toString()),
                    e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", n).toggleClass("ui-icon-triangle-1-s", !n),
                    t.toggleClass("accordion-content-active", !n), n ? t.slideUp() : t.slideDown(), !1;
            }
        }),

        SFSI("#accordion2").accordion({
            collapsible: !0,
            active: !1,
            heightStyle: "content",
            event: "click",
            beforeActivate: function (s, i) {
                if (i.newHeader[0]) var e = i.newHeader,
                    t = e.next(".ui-accordion-content");
                else var e = i.oldHeader,
                    t = e.next(".ui-accordion-content");
                var n = "true" == e.attr("aria-selected");
                return e.toggleClass("ui-corner-all", n).toggleClass("accordion-header-active ui-state-active ui-corner-top", !n).attr("aria-selected", (!n).toString()),
                    e.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", n).toggleClass("ui-icon-triangle-1-s", !n),
                    t.toggleClass("accordion-content-active", !n), n ? t.slideUp() : t.slideDown(), !1;
            }
        }),
        SFSI(".closeSec").on("click", function () {
            var s = !0,
                i = SFSI(this).closest("div.ui-accordion-content").prev("h3.ui-accordion-header").first(),
                e = SFSI(this).closest("div.ui-accordion-content").first();
            i.toggleClass("ui-corner-all", s).toggleClass("accordion-header-active ui-state-active ui-corner-top", !s).attr("aria-selected", (!s).toString()),
                i.children(".ui-icon").toggleClass("ui-icon-triangle-1-e", s).toggleClass("ui-icon-triangle-1-s", !s),
                e.toggleClass("accordion-content-active", !s), s ? e.slideUp() : e.slideDown();
        }),
        SFSI(document).click(function (s) {
            var i = SFSI(".sfsi_FrntInner_chg"),
                e = SFSI(".sfsi_wDiv"),
                t = SFSI("#at15s");
            i.is(s.target) || 0 !== i.has(s.target).length || e.is(s.target) || 0 !== e.has(s.target).length || t.is(s.target) || 0 !== t.has(s.target).length || i.fadeOut();
        }),
        SFSI('#sfsi_popup_background_color').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                sfsi_make_popBox()
            },
            clear: function () {
                sfsi_make_popBox()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_popup_border_color').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                sfsi_make_popBox()
            },
            clear: function () {
                sfsi_make_popBox()
            },
            hide: true,
            palettes: true
        }),
        SFSI('#sfsi_popup_fontColor').wpColorPicker({
            defaultColor: false,
            change: function (event, ui) {
                sfsi_make_popBox()
            },
            clear: function () {
                sfsi_make_popBox()
            },
            hide: true,
            palettes: true
        }),
        SFSI("div#sfsiid_linkedin").find(".icon4").find("a").find("img").mouseover(function () {
            SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/linkedIn_hover.svg");
        }),
        SFSI("div#sfsiid_linkedin").find(".icon4").find("a").find("img").mouseleave(function () {
            SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/linkedIn.svg");
        }),
        SFSI("div#sfsiid_youtube").find(".icon1").find("a").find("img").mouseover(function () {
            SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/youtube_hover.svg");
        }),
        SFSI("div#sfsiid_youtube").find(".icon1").find("a").find("img").mouseleave(function () {
            SFSI(this).attr("src", sfsi_icon_ajax_object.plugin_url + "images/visit_icons/youtube.svg");
        }),
        SFSI("div#sfsiid_facebook").find(".icon1").find("a").find("img").mouseover(function () {
            SFSI(this).css("opacity", "0.9");
        }),
        SFSI("div#sfsiid_facebook").find(".icon1").find("a").find("img").mouseleave(function () {
            SFSI(this).css("opacity", "1");
        }),
        SFSI("div#sfsiid_twitter").find(".cstmicon1").find("a").find("img").mouseover(function () {
            SFSI(this).css("opacity", "0.9");
        }),
        SFSI("div#sfsiid_twitter").find(".cstmicon1").find("a").find("img").mouseleave(function () {
            SFSI(this).css("opacity", "1");
        }),
        SFSI("#sfsi_save1").on("click", function () {
            // console.log('save1',sfsi_update_step1());
            sfsi_update_step1() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save2").on("click", function () {
            sfsi_update_step2() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save3").on("click", function () {
            sfsi_update_step3() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save4").on("click", function () {
            sfsi_update_step4() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save5").on("click", function () {
            sfsi_update_step5() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save6").on("click", function () {
            sfsi_update_step6() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save7").on("click", function () {
            sfsi_update_step7() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save8").on("click", function () {
            sfsi_update_step8() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save9").on("click", function () {
            sfsi_update_step9() && sfsicollapse(this);
        }),
        SFSI("#sfsi_save_export").on("click", function () {
            sfsi_save_export();
        }),
        SFSI("#sfsi_installDate").on("click", function () {
            sfsi_installDate_save();
        }),
        SFSI("#sfsi_currentDate").on("click", function () {
            sfsi_currentDate_save();
        }),
        SFSI("#sfsi_showNextBannerDate").on("click", function () {
            sfsi_showNextBannerDate_save();
        }),
        SFSI("#sfsi_cycleDate").on("click", function () {
            sfsi_cycleDate_save();
        }),
        SFSI("#sfsi_loyaltyDate").on("click", function () {
            sfsi_loyaltyDate_save();
        }),
        SFSI("#sfsi_banner_global_firsttime_offer").on("click", function () {
            sfsi_banner_global_firsttime_offer_save();
        }),
        SFSI("#sfsi_banner_global_pinterest").on("click", function () {
            sfsi_banner_global_pinterest_save();
        }),
        SFSI("#sfsi_banner_global_social").on("click", function () {
            sfsi_banner_global_social_save();
        }),
        SFSI("#sfsi_banner_global_load_faster").on("click", function () {
            sfsi_banner_global_load_faster_save();
        }),
        SFSI("#sfsi_banner_global_shares").on("click", function () {
            sfsi_banner_global_shares_save();
        }),
        SFSI("#sfsi_banner_global_gdpr").on("click", function () {
            sfsi_banner_global_gdpr_save();
        }),
        SFSI("#sfsi_banner_global_http").on("click", function () {
            sfsi_banner_global_http_save();
        }),
        SFSI("#sfsi_banner_global_upgrade").on("click", function () {
            sfsi_banner_global_upgrade_save();
        }),
        SFSI("#save_all_settings").on("click", function () {
            return SFSI("#save_all_settings").text("Saving.."), SFSI(".save_button >a").css("pointer-events", "none"),
                sfsi_update_step1(), sfsi_update_step8(), 1 == global_error ? (showErrorSuc("error", 'Some Selection error in "Which icons do you want to show on your site?" tab.', 8),
                    global_error = 0, !1) : (sfsi_update_step2(), 1 == global_error ? (showErrorSuc("error", 'Some Selection error in "What do you want the icons to do?" tab.', 8),
                    global_error = 0, !1) : (sfsi_update_step3(), 1 == global_error ? (showErrorSuc("error", 'Some Selection error in "What design & animation do you want to give your icons?" tab.', 8),
                    global_error = 0, !1) : (sfsi_update_step4(), 1 == global_error ? (showErrorSuc("error", 'Some Selection error in "Do you want to display "counts" next to your icons?" tab.', 8),
                    global_error = 0, !1) : (sfsi_update_step5(), 1 == global_error ? (showErrorSuc("error", 'Some Selection error in "Any other wishes for your main icons?" tab.', 8),
                    global_error = 0, !1) : (sfsi_update_step6(), 1 == global_error ? (showErrorSuc("error", 'Some Selection error in "Do you want to display icons at the end of every post?" tab.', 8),
                    global_error = 0, !1) : (sfsi_update_step7(), 1 == global_error ? (showErrorSuc("error", 'Some Selection error in "Do you want to display a pop-up, asking people to subscribe?" tab.', 8),
                    /*global_error = 0, !1) :void (0 == global_error && showErrorSuc("success", 'Saved! Now go to the <a href="widgets.php">widget</a> area and place the widget into your sidebar (if not done already)', 8))))))));*/
                    global_error = 0, !1) : void(0 == global_error && showErrorSuc("success", '', 8))))))));
        }),
        /*SFSI(".fileUPInput").live("change", function() {*/
        SFSI(document).on("change", '.fileUPInput', function () {
            beForeLoad(), beforeIconSubmit(this) && (SFSI(".upload-overlay").css("pointer-events", "none"),
                SFSI("#customIconFrm").ajaxForm({
                    dataType: "json",
                    success: afterIconSuccess,
                    resetForm: !0
                }).submit());
        }),
        SFSI(".pop-up").on("click", function () {
            ("fbex-s2" == SFSI(this).attr("data-id") || "linkex-s2" == SFSI(this).attr("data-id")) && (SFSI("." + SFSI(this).attr("data-id")).hide(),
                SFSI("." + SFSI(this).attr("data-id")).css("opacity", "1"), SFSI("." + SFSI(this).attr("data-id")).css("z-index", "1000")),
            SFSI("." + SFSI(this).attr("data-id")).show("slow");
        }),
        /*SFSI("#close_popup").live("click", function() {*/
        SFSI(document).on("click", '#close_popup', function () {
            SFSI(".read-overlay").hide("slow");
        });

    var e = 0;
    SFSI(".icn_listing").on("click", ".checkbox", function () {
            if (1 == e) return !1;
            "yes" == SFSI(this).attr("dynamic_ele") && (s = SFSI(this).parent().find("input:checkbox:first"),
                    s.is(":checked") ? SFSI(s).attr("checked", !1) : SFSI(s).attr("checked", !0)), s = SFSI(this).parent().find("input:checkbox:first"),
                "yes" == SFSI(s).attr("isNew") && ("0px 0px" == SFSI(this).css("background-position") ? (SFSI(s).attr("checked", !0),
                    SFSI(this).css("background-position", "0px -36px")) : (SFSI(s).removeAttr("checked", !0),
                    SFSI(this).css("background-position", "0px 0px")));
            var s = SFSI(this).parent().find("input:checkbox:first");
            if (s.is(":checked") && "cusotm-icon" == s.attr("element-type")) SFSI(".fileUPInput").attr("name", "custom_icons[]"),
                SFSI(".upload-overlay").show("slow", function () {
                    e = 0;
                }), SFSI("#upload_id").val(s.attr("name"));
            else if (!s.is(":checked") && "cusotm-icon" == s.attr("element-type")) return s.attr("ele-type") ? (SFSI(this).attr("checked", !0),
                SFSI(this).css("background-position", "0px -36px"), e = 0, !1) : confirm("Are you sure want to delete this Icon..?? ") ? "suc" == sfsi_delete_CusIcon(this, s) ? (s.attr("checked", !1),
                SFSI(this).css("background-position", "0px 0px"), e = 0, !1) : (e = 0, !1) : (s.attr("checked", !0),
                SFSI(this).css("background-position", "0px -36px"), e = 0, !1);
        }),
        SFSI(".icn_listing").on("click", ".checkbox", function () {
            checked = SFSI(this).parent().find("input:checkbox:first"), "sfsi_email_display" != checked.attr("name") || checked.is(":checked") || SFSI(".demail-1").show("slow");
        }),
        SFSI("#deac_email2").on("click", function () {
            SFSI(".demail-1").hide("slow"), SFSI(".demail-2").show("slow");
        }),
        SFSI("#deac_email3").on("click", function () {
            SFSI(".demail-2").hide("slow"), SFSI(".demail-3").show("slow");
        }),
        SFSI(".hideemailpop").on("click", function () {
            SFSI('input[name="sfsi_email_display"]').attr("checked", !0), SFSI('input[name="sfsi_email_display"]').parent().find("span:first").css("background-position", "0px -36px"),
                SFSI(".demail-1").hide("slow"), SFSI(".demail-2").hide("slow"), SFSI(".demail-3").hide("slow");
        }),
        SFSI(".hidePop").on("click", function () {
            SFSI(".demail-1").hide("slow"), SFSI(".demail-2").hide("slow"), SFSI(".demail-3").hide("slow");
        }),
        SFSI(".activate_footer").on("click", function () {
            var nonce = SFSI(this).attr("data-nonce");
            SFSI(this).text("activating....");
            var s = {
                action: "activateFooter",
                nonce: nonce
            };
            SFSI.ajax({
                url: sfsi_icon_ajax_object.ajax_url,
                type: "post",
                data: s,
                dataType: "json",
                success: function (s) {
                    if (s.res == "wrong_nonce") {
                        SFSI(".activate_footer").css("font-size", "18px");
                        SFSI(".activate_footer").text("Unauthorised Request, Try again after refreshing page");
                    } else {
                        "success" == s.res && (SFSI(".demail-1").hide("slow"), SFSI(".demail-2").hide("slow"),
                            SFSI(".demail-3").hide("slow"), SFSI(".activate_footer").text("Ok, activate link"));
                    }
                }
            });
        }),
        SFSI(".sfsi_removeFooter").on("click", function () {
            var nonce = SFSI(this).attr("data-nonce");
            SFSI(this).text("working....");
            var s = {
                action: "removeFooter",
                nonce: nonce
            };
            SFSI.ajax({
                url: sfsi_icon_ajax_object.ajax_url,
                type: "post",
                data: s,
                dataType: "json",
                success: function (s) {
                    if (s.res == "wrong_nonce") {
                        SFSI(".sfsi_removeFooter").text("Unauthorised Request, Try again after refreshing page");
                    } else {
                        "success" == s.res && (SFSI(".sfsi_removeFooter").fadeOut("slow"), SFSI(".sfsi_footerLnk").fadeOut("slow"));
                    }
                }
            });
        }),
        /*SFSI(".radio").live("click", function() {*/
        SFSI(document).on("click", '.radio', function () {
            var s = SFSI(this).parent().find("input:radio:first");
            "sfsi_display_counts" == s.attr("name") && sfsi_show_counts();
        }),
        SFSI("#close_Uploadpopup").on("click", i), /*SFSI(".radio").live("click", function() {*/ SFSI(document).on("click", '.radio', function () {
            var s = SFSI(this).parent().find("input:radio:first");
            "sfsi_show_Onposts" == s.attr("name") && sfsi_show_OnpostsDisplay();
        }),
        sfsi_show_OnpostsDisplay(), sfsi_depened_sections(), sfsi_show_counts(), sfsi_showPreviewCounts(),
        SFSI(".share_icon_order").sortable({
            update: function () {
                SFSI(".share_icon_order li").each(function () {
                    SFSI(this).attr("data-index", SFSI(this).index() + 1);
                });
            },
            revert: !0
        }),

        //*------------------------------- Sharing text & pcitures checkbox for showing section in Page, Post STARTS -------------------------------------//

        SFSI(document).on("click", '.checkbox', function () {

            var s = SFSI(this).parent().find("input:checkbox:first");
            var backgroundPos = jQuery(this).css('background-position').split(" ");
            var xPos = backgroundPos[0],
                yPos = backgroundPos[1];

            var inputName = s.attr('name');
            var inputChecked = s.attr("checked");

            switch (inputName) {

                case "sfsi_custom_social_hide":

                    var val = (yPos == "0px") ? "no" : "yes";
                    SFSI('input[name="sfsi_custom_social_hide"]').val(val);

                    break;

                case "sfsi_show_via_widget":
                case "sfsi_show_via_widget":
                case "sfsi_show_via_afterposts":
                case "sfsi_custom_social_hide":

                    var val = (yPos == "0px") ? "no" : "yes";
                    SFSI('input[name="' + s.attr('name') + '"]').val(val);

                    break;

                case 'sfsi_mouseOver':

                    var elem = SFSI('input[name="' + inputName + '"]');

                    var togglelem = SFSI('.mouse-over-effects');

                    if (inputChecked) {
                        togglelem.removeClass('hide').addClass('show');
                    } else {
                        togglelem.removeClass('show').addClass('hide');
                    }

                    break;
                case 'sfsi_responsive_facebook_display':
                    if (inputChecked) {
                        SFSI('.sfsi_responsive_icon_facebook_container').parents('a').show();
                        var icon = inputName.replace('sfsi_responsive_', '').replace('_display', '');
                        if (SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                            window.sfsi_fittext_shouldDisplay = true;
                            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                                if (jQuery(a_container).css('display') !== "none") {
                                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                                }
                            })
                        }
                    } else {

                        SFSI('.sfsi_responsive_icon_facebook_container').parents('a').hide();
                        if (SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                            window.sfsi_fittext_shouldDisplay = true;
                            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                                if (jQuery(a_container).css('display') !== "none") {
                                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                                }
                            })
                        }
                    }
                    break;
                case 'sfsi_responsive_Twitter_display':
                    if (inputChecked) {
                        SFSI('.sfsi_responsive_icon_twitter_container').parents('a').show();
                        var icon = inputName.replace('sfsi_responsive_', '').replace('_display', '');
                        if (SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                            window.sfsi_fittext_shouldDisplay = true;
                            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                                if (jQuery(a_container).css('display') !== "none") {
                                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                                }
                            })
                        }
                    } else {
                        SFSI('.sfsi_responsive_icon_twitter_container').parents('a').hide();
                        if (SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() !== "Fully responsive") {
                            window.sfsi_fittext_shouldDisplay = true;
                            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                                if (jQuery(a_container).css('display') !== "none") {
                                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                                }
                            })
                        }
                    }
                    break;
                case 'sfsi_responsive_Follow_display':
                    if (inputChecked) {
                        SFSI('.sfsi_responsive_icon_follow_container').parents('a').show();
                        var icon = inputName.replace('sfsi_responsive_', '').replace('_display', '');
                    } else {
                        SFSI('.sfsi_responsive_icon_follow_container').parents('a').hide();
                    }
                    window.sfsi_fittext_shouldDisplay = true;
                    jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                        if (jQuery(a_container).css('display') !== "none") {
                            sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                        }
                    })
                    break;
            }

        });

    //*------------------------------- Sharing text & pcitures checkbox for showing section in Page, Post CLOSES -------------------------------------//

    SFSI(document).on("click", '.radio', function () {

        var s = SFSI(this).parent().find("input:radio:first");

        switch (s.attr("name")) {

            case 'sfsi_mouseOver_effect_type':

                var _val = s.val();
                var _name = s.attr("name");

                if ('same_icons' == _val) {
                    SFSI('.same_icons_effects').removeClass('hide').addClass('show');
                    SFSI('.other_icons_effects_options').removeClass('show').addClass('hide');
                } else if ('other_icons' == _val) {
                    SFSI('.same_icons_effects').removeClass('show').addClass('hide');
                    SFSI('.other_icons_effects_options').removeClass('hide').addClass('show');
                }

                break;
        }

    });

    SFSI(document).on("click", '.radio', function () {

            var s = SFSI(this).parent().find("input:radio:first");
            "sfsi_email_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_email_countsDisplay"]').prop("checked", !0),
                    SFSI('input[name="sfsi_email_countsDisplay"]').parent().find("span.checkbox").attr("style", "0px -36px;"),
                    "manual" == SFSI("input[name='sfsi_email_countsFrom']:checked").val() ? SFSI("input[name='sfsi_email_manualCounts']").slideDown() : SFSI("input[name='sfsi_email_manualCounts']").slideUp()),

                "sfsi_facebook_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_facebook_countsDisplay"]').prop("checked", !0),
                    SFSI('input[name="sfsi_facebook_countsDisplay"]').parent().find("span.checkbox").attr("style", "0px -36px;"),
                    "mypage" == SFSI("input[name='sfsi_facebook_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_facebook_mypageCounts']").slideDown(), SFSI(".sfsi_fbpgidwpr").slideDown()) : (SFSI("input[name='sfsi_facebook_mypageCounts']").slideUp(), SFSI(".sfsi_fbpgidwpr").slideUp()),

                    "manual" == SFSI("input[name='sfsi_facebook_countsFrom']:checked").val() ? SFSI("input[name='sfsi_facebook_manualCounts']").slideDown() : SFSI("input[name='sfsi_facebook_manualCounts']").slideUp()),

                "sfsi_facebook_countsFrom" == s.attr("name") && (("mypage" == SFSI("input[name='sfsi_facebook_countsFrom']:checked").val() || "likes" == SFSI("input[name='sfsi_facebook_countsFrom']:checked").val()) ? (SFSI(".sfsi_facebook_pagedeasc").slideDown()) : (SFSI(".sfsi_facebook_pagedeasc").slideUp())),

                "sfsi_twitter_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_twitter_countsDisplay"]').prop("checked", !0),
                    SFSI('input[name="sfsi_twitter_countsDisplay"]').parent().find("span.checkbox").attr("style", "0px -36px;"),
                    "manual" == SFSI("input[name='sfsi_twitter_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_twitter_manualCounts']").slideDown(),
                        SFSI(".tw_follow_options").slideUp()) : (SFSI("input[name='sfsi_twitter_manualCounts']").slideUp(),
                        SFSI(".tw_follow_options").slideDown())),


                "sfsi_linkedIn_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_linkedIn_countsDisplay"]').prop("checked", !0),
                    SFSI('input[name="sfsi_linkedIn_countsDisplay"]').parent().find("span.checkbox").attr("style", "0px -36px;"),
                    "manual" == SFSI("input[name='sfsi_linkedIn_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_linkedIn_manualCounts']").slideDown(),
                        SFSI(".linkedIn_options").slideUp()) : (SFSI("input[name='sfsi_linkedIn_manualCounts']").slideUp(),
                        SFSI(".linkedIn_options").slideDown())),

                "sfsi_youtube_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_youtube_countsDisplay"]').prop("checked", !0),
                    SFSI('input[name="sfsi_youtube_countsDisplay"]').parent().find("span.checkbox").attr("style", "0px -36px;"),
                    "manual" == SFSI("input[name='sfsi_youtube_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_youtube_manualCounts']").slideDown(),
                        SFSI(".youtube_options").slideUp()) : (SFSI("input[name='sfsi_youtube_manualCounts']").slideUp(),
                        SFSI(".youtube_options").slideDown())), "sfsi_pinterest_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_pinterest_countsDisplay"]').prop("checked", !0),
                    SFSI('input[name="sfsi_pinterest_countsDisplay"]').parent().find("span.checkbox").attr("style", "0px -36px;"),
                    "manual" == SFSI("input[name='sfsi_pinterest_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_pinterest_manualCounts']").slideDown(),
                        SFSI(".pin_options").slideUp()) : SFSI("input[name='sfsi_pinterest_manualCounts']").slideUp()),

                "sfsi_instagram_countsFrom" == s.attr("name") && (SFSI('input[name="sfsi_instagram_countsDisplay"]').prop("checked", !0),
                    SFSI('input[name="sfsi_instagram_countsDisplay"]').parent().find("span.checkbox").attr("style", "0px -36px;"),
                    "manual" == SFSI("input[name='sfsi_instagram_countsFrom']:checked").val() ? (SFSI("input[name='sfsi_instagram_manualCounts']").slideDown(),
                        SFSI(".instagram_userLi").slideUp()) : (SFSI("input[name='sfsi_instagram_manualCounts']").slideUp(),
                        SFSI(".instagram_userLi").slideDown()));

        }),

        sfsi_make_popBox(),

        SFSI('input[name="sfsi_popup_text"] ,input[name="sfsi_popup_background_color"],input[name="sfsi_popup_border_color"],input[name="sfsi_popup_border_thickness"],input[name="sfsi_popup_fontSize"],input[name="sfsi_popup_fontColor"]').on("keyup", sfsi_make_popBox),
        SFSI('input[name="sfsi_popup_text"] ,input[name="sfsi_popup_background_color"],input[name="sfsi_popup_border_color"],input[name="sfsi_popup_border_thickness"],input[name="sfsi_popup_fontSize"],input[name="sfsi_popup_fontColor"]').on("focus", sfsi_make_popBox),

        SFSI("#sfsi_popup_font ,#sfsi_popup_fontStyle").on("change", sfsi_make_popBox),

        /*SFSI(".radio").live("click", function(){*/
        SFSI(document).on("click", '.radio', function () {

            var s = SFSI(this).parent().find("input:radio:first");

            if ("sfsi_icons_floatPosition" == s.attr("name")) {
                SFSI('input[name="sfsi_icons_floatPosition"]').removeAttr("checked");
                s.attr("checked", true);
            }

            if ("sfsi_disable_floaticons" == s.attr("name")) {
                SFSI('input[name="sfsi_disable_floaticons"]').removeAttr("checked");
                s.attr("checked", true);
            }

            "sfsi_popup_border_shadow" == s.attr("name") && sfsi_make_popBox();
        }), /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? SFSI("img.sfsi_wicon").on("click", function (s) {
            s.stopPropagation();
            var i = SFSI("#sfsi_floater_sec").val();
            SFSI("div.sfsi_wicons").css("z-index", "0"), SFSI(this).parent().parent().parent().siblings("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide(),
                SFSI(this).parent().parent().parent().parent().siblings("li").length > 0 && (SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_tool_tip_2").css("z-index", "0"),
                    SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide()),
                SFSI(this).parent().parent().parent().css("z-index", "1000000"), SFSI(this).parent().parent().css({
                    "z-index": "999"
                }), SFSI(this).attr("data-effect") && "fade_in" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                    opacity: 1,
                    "z-index": 10
                }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 10
                    }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "combo" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"),
                    SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 10
                    })), ("top-left" == i || "top-right" == i) && SFSI(this).parent().parent().parent().parent("#sfsi_floater").length > 0 && "sfsi_floater" == SFSI(this).parent().parent().parent().parent().attr("id") ? (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").addClass("sfsi_plc_btm"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").addClass("top_big_arow"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 10
                    }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show()) : (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").removeClass("sfsi_plc_btm"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 1e3
                    }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show());
        }) : SFSI("img.sfsi_wicon").on("mouseenter", function () {
            var s = SFSI("#sfsi_floater_sec").val();
            SFSI("div.sfsi_wicons").css("z-index", "0"), SFSI(this).parent().parent().parent().siblings("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide(),
                SFSI(this).parent().parent().parent().parent().siblings("li").length > 0 && (SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_tool_tip_2").css("z-index", "0"),
                    SFSI(this).parent().parent().parent().parent().siblings("li").find("div.sfsi_wicons").find(".inerCnt").find("div.sfsi_tool_tip_2").hide()),
                SFSI(this).parent().parent().parent().css("z-index", "1000000"), SFSI(this).parent().parent().css({
                    "z-index": "999"
                }), SFSI(this).attr("data-effect") && "fade_in" == SFSI(this).attr("data-effect") && (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                    opacity: 1,
                    "z-index": 10
                }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "scale" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 10
                    }), SFSI(this).parent().css("opacity", "1")), SFSI(this).attr("data-effect") && "combo" == SFSI(this).attr("data-effect") && (SFSI(this).parent().addClass("scale"),
                    SFSI(this).parent().css("opacity", "1"), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 10
                    })), ("top-left" == s || "top-right" == s) && SFSI(this).parent().parent().parent().parent("#sfsi_floater").length > 0 && "sfsi_floater" == SFSI(this).parent().parent().parent().parent().attr("id") ? (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").addClass("sfsi_plc_btm"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").addClass("top_big_arow"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 10
                    }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show()) : (SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").find("span.bot_arow").removeClass("top_big_arow"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").removeClass("sfsi_plc_btm"),
                    SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").css({
                        opacity: 1,
                        "z-index": 10
                    }), SFSI(this).parentsUntil("div").siblings("div.sfsi_tool_tip_2").show());
        }), SFSI("div.sfsi_wicons").on("mouseleave", function () {
            SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && "fade_in" == SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && SFSI(this).children("div.inerCnt").find("a.sficn").css("opacity", "0.6"),
                SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && "scale" == SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && SFSI(this).children("div.inerCnt").find("a.sficn").removeClass("scale"),
                SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && "combo" == SFSI(this).children("div.inerCnt").children("a.sficn").attr("data-effect") && (SFSI(this).children("div.inerCnt").find("a.sficn").css("opacity", "0.6"),
                    SFSI(this).children("div.inerCnt").find("a.sficn").removeClass("scale")), SFSI(this).children(".inerCnt").find("div.sfsi_tool_tip_2").hide();
        }), SFSI("body").on("click", function () {
            SFSI(".inerCnt").find("div.sfsi_tool_tip_2").hide();
        }), SFSI(".adminTooltip >a").on("hover", function () {
            SFSI(this).offset().top, SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").css("opacity", "1"),
                SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").show();
        }), SFSI(".adminTooltip").on("mouseleave", function () {
            "none" != SFSI(".gpls_tool_bdr").css("display") && 0 != SFSI(".gpls_tool_bdr").css("opacity") ? SFSI(".pop_up_box ").on("click", function () {
                SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").css("opacity", "0"), SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").hide();
            }) : (SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").css("opacity", "0"),
                SFSI(this).parent("div").find("div.sfsi_tool_tip_2_inr").hide());
        }), SFSI(".expand-area").on("click", function () {
            "Read more" == SFSI(this).text() ? (SFSI(this).siblings("p").children("label").fadeIn("slow"),
                SFSI(this).text("Collapse")) : (SFSI(this).siblings("p").children("label").fadeOut("slow"),
                SFSI(this).text("Read more"));
        }), /*SFSI(".radio").live("click", function() {*/ SFSI(document).on("click", '.radio', function () {

            var s = SFSI(this).parent().find("input:radio:first");

            "sfsi_icons_float" == s.attr("name") && "yes" == s.val() && (SFSI(".float_options").slideDown("slow"),

                    SFSI('input[name="sfsi_icons_stick"][value="no"]').attr("checked", !0), SFSI('input[name="sfsi_icons_stick"][value="yes"]').removeAttr("checked"),
                    SFSI('input[name="sfsi_icons_stick"][value="no"]').parent().find("span").attr("style", "0px -41px;"),
                    SFSI('input[name="sfsi_icons_stick"][value="yes"]').parent().find("span").attr("style", "0px -0px;")),

                //("sfsi_icons_stick" == s.attr("name") && "yes" == s.val() || "sfsi_icons_float" == s.attr("name") && "no" == s.val()) && (SFSI(".float_options").slideUp("slow"),
                ("sfsi_icons_stick" == s.attr("name") && "yes" == s.val()) && (SFSI(".float_options").slideUp("slow"),

                    SFSI('input[name="sfsi_icons_float"][value="no"]').prop("checked", !0), SFSI('input[name="sfsi_icons_float"][value="yes"]').prop("checked", !1),
                    SFSI('input[name="sfsi_icons_float"][value="no"]').parent().find("span.radio").attr("style", "0px -41px;"),
                    SFSI('input[name="sfsi_icons_float"][value="yes"]').parent().find("span.radio").attr("style", "0px -0px;"));

        }),

        SFSI(".sfsi_wDiv").length > 0 && setTimeout(function () {
            var s = parseInt(SFSI(".sfsi_wDiv").height()) + 0 + "px";
            SFSI(".sfsi_holders").each(function () {
                SFSI(this).css("height", s);
            });
        }, 200),
        /*SFSI(".checkbox").live("click", function() {*/
        SFSI(document).on("click", '.checkbox', function () {
            var s = SFSI(this).parent().find("input:checkbox:first");
            ("sfsi_shuffle_Firstload" == s.attr("name") && "checked" == s.attr("checked") || "sfsi_shuffle_interval" == s.attr("name") && "checked" == s.attr("checked")) && (SFSI('input[name="sfsi_shuffle_icons"]').parent().find("span").css("background-position", "0px -36px"),
                SFSI('input[name="sfsi_shuffle_icons"]').attr("checked", "checked")), "sfsi_shuffle_icons" == s.attr("name") && "checked" != s.attr("checked") && (SFSI('input[name="sfsi_shuffle_Firstload"]').removeAttr("checked"),
                SFSI('input[name="sfsi_shuffle_Firstload"]').parent().find("span").css("background-position", "0px 0px"),
                SFSI('input[name="sfsi_shuffle_interval"]').removeAttr("checked"), SFSI('input[name="sfsi_shuffle_interval"]').parent().find("span").css("background-position", "0px 0px"));
        });

    SFSI("body").on("click", "#sfsi_getMeFullAccess", function () {
        var email = SFSI(this).parents("form").find("input[type='email']").val();
        var feedid = SFSI(this).parents("form").find("input[name='feed_id']").val();
        var error = false;
        var regEx = /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;

        if (email === '') {
            error = true;
        }

        if (!regEx.test(email)) {
            error = true;
        }

        if (!error) {

            SFSI(this).css("pointer-events", "none");
            // console.log("feedid",feedid);
            if (feedid == "" || undefined == feedid) {
                var nonce = SFSI(this).attr('data-nonce-fetch-feed-id');
                e = {
                    action: "sfsi_get_feed_id",
                    nonce: nonce,
                };
                SFSI.ajax({
                    url: sfsi_icon_ajax_object.ajax_url,
                    type: "post",
                    data: e,
                    dataType: "json",
                    async: !0,
                    success: function (s) {
                        if (s.res == "wrong_nonce") {
                            alert("Error: Unauthorised Request, Try again after refreshing page.");
                        } else {
                            if ("success" == s.res) {
                                var feedid = s.feed_id;
                                if (feedid == "" || null == feedid) {
                                    alert("Error: Claiming didn't work. Please try again later.");
                                    SFSI(".sfsi_getMeFullAccess_class").css("pointer-events", "initial");


                                } else {
                                    jQuery('#calimingOptimizationForm input[name="feed_id"]').val(feedid);
                                    // console.log("feedid",feedid,SFSI("#calimingOptimizationForm input[name='feed_id']"),SFSI('#calimingOptimizationForm input[name="feedid"]').val());
                                    SFSI('#calimingOptimizationForm').submit();
                                    SFSI(".sfsi_getMeFullAccess_class").css("pointer-events", "initial");


                                }
                            } else {
                                if ("failed" == s.res) {
                                    alert("Error: " + s.message + ".");
                                    SFSI(".sfsi_getMeFullAccess_class").css("pointer-events", "initial");



                                } else {
                                    alert("Error: Please try again.");
                                    SFSI(".sfsi_getMeFullAccess_class").css("pointer-events", "initial");


                                }
                            }
                        }
                    }
                });
            } else {
                SFSI(this).parents("form").submit();
            }
        } else {
            alert("Error: Please provide your email address.");
        }
    });

    SFSI('form#calimingOptimizationForm').on('keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    /*SFSI(".checkbox").live("click", function()
	{
        var s = SFSI(this).parent().find("input:checkbox:first");
        "float_on_page" == s.attr("name") && "yes" == s.val() && ( 
        SFSI('input[name="sfsi_icons_stick"][value="no"]').attr("checked", !0), SFSI('input[name="sfsi_icons_stick"][value="yes"]').removeAttr("checked"), 
        SFSI('input[name="sfsi_icons_stick"][value="no"]').parent().find("span").attr("style", "0px -41px;"), 
        SFSI('input[name="sfsi_icons_stick"][value="yes"]').parent().find("span").attr("style", "0px -0px;"));
    });
	SFSI(".radio").live("click", function()
	{
        var s = SFSI(this).parent().find("input:radio:first");
		var a = SFSI(".cstmfltonpgstck");
		("sfsi_icons_stick" == s.attr("name") && "yes" == s.val()) && (
        SFSI('input[name="float_on_page"][value="no"]').prop("checked", !0), SFSI('input[name="float_on_page"][value="yes"]').prop("checked", !1), 
        SFSI('input[name="float_on_page"][value="no"]').parent().find("span.checkbox").attr("style", "0px -41px;"), 
        SFSI('input[name="float_on_page"][value="yes"]').parent().find("span.checkbox").attr("style", "0px -0px;"),
		jQuery(a).children(".checkbox").css("background-position", "0px 0px" ), toggleflotpage(a));
    });*/
    window.sfsi_initialization_checkbox_count = 0;
    window.sfsi_initialization_checkbox = setInterval(function () {
        // console.log(jQuery('.radio_section.tb_4_ck>span.checkbox').length,jQuery('.radio_section.tb_4_ck>input.styled').length);
        if (jQuery('.radio_section.tb_4_ck>span.checkbox').length < jQuery('.radio_section.tb_4_ck>input.styled').length) {
            window.sfsi_initialization_checkbox_count++;
            // console.log('not initialized',window.sfsi_initialization_checkbox_count);
            if (window.sfsi_initialization_checkbox_count > 12) {
                // alert('Some script from diffrent plugin is interfearing with "Ultimate Social Icons" js files and checkbox couldn\'t be initialized. ');
                // window.clearInterval(window.sfsi_initialization_checkbox);
            }
        } else {
            // console.log('all initialized',window.sfsi_initialization_checkbox_count);
            window.clearInterval(window.sfsi_initialization_checkbox);
        }
    }, 1000);
    sfsi_responsive_icon_intraction_handler();

});

//for utube channel name and id
function showhideutube(ref) {
    var chnlslctn = SFSI(ref).children("input").val();
    if (chnlslctn == "name") {
        SFSI(ref).parent(".enough_waffling").next(".cstmutbtxtwpr").children(".cstmutbchnlnmewpr").slideDown();
        SFSI(ref).parent(".enough_waffling").next(".cstmutbtxtwpr").children(".cstmutbchnlidwpr").slideUp();
    } else {
        SFSI(ref).parent(".enough_waffling").next(".cstmutbtxtwpr").children(".cstmutbchnlidwpr").slideDown();
        SFSI(ref).parent(".enough_waffling").next(".cstmutbtxtwpr").children(".cstmutbchnlnmewpr").slideUp();
    }
}
 
function checkforinfoslction(ref) {
    var pos = jQuery(ref).children(".checkbox").css("background-position");

    var rightInfoClass = jQuery(ref).next().attr('class');

    var rightInfoPElem = jQuery(ref).next("." + rightInfoClass).children("p").first();

    var elemName = 'label';

    if (pos == "0px 0px") {
        rightInfoPElem.children(elemName).hide();
    } else {
        rightInfoPElem.children(elemName).show();
    }
}

function checkforinfoslction_checkbox(ref) {

console.log(ref)
    var pos = jQuery(ref).children(".checkbox").css("background-position");

    var elem = jQuery(ref).parent().children('.sfsi_right_info').find('.kckslctn');

    if (pos == "0px 0px") {
        elem.hide();
    } else {
        elem.show();
        jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
            }
        })
        sfsi_resize_icons_container();
        setTimeout(function () {
            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                if (jQuery(a_container).css('display') !== "none") {
                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                }
            })
            sfsi_resize_icons_container();
        }, 10);
    }
}

function sfsi_toggleflotpage_que3(ref) {
    var pos = jQuery(ref).children(".checkbox").css("background-position");
    if (pos == "0px 0px") {
        jQuery(ref).next(".sfsi_right_info").hide();

    } else {
        jQuery(ref).next(".sfsi_right_info").show();
    }
}

var initTop = new Array();

SFSI('.sfsi_navigate_to_question7').on("click", function () {

    var elem = SFSI('#ui-id-6');

    if (elem.hasClass('accordion-content-active')) {

        // Cloase tab of Question 3
        elem.find('.sfsiColbtn').trigger('click');

        // Open tab of Question 7
        if (!SFSI('#ui-id-14').hasClass('accordion-content-active')) {
            SFSI('#ui-id-13').trigger('click');
        }

        var pos = SFSI("#ui-id-13").offset();
        var scrollToPos = pos.top - SFSI(window).height() * 0.99 + 30;
        SFSI('html,body').animate({
            scrollTop: scrollToPos
        }, 500);
    }
});

SFSI("body").on("click", ".sfsi_tokenGenerateButton a", function () {
    var clienId = SFSI("input[name='sfsi_instagram_clientid']").val();
    var redirectUrl = SFSI("input[name='sfsi_instagram_appurl']").val();

    var scope = "basic";
    var instaUrl = "https://www.instagram.com/oauth/authorize/?client_id=<id>&redirect_uri=<url>&response_type=token&scope=" + scope;

    if (clienId !== '' && redirectUrl !== '') {
        instaUrl = instaUrl.replace('<id>', clienId);
        instaUrl = instaUrl.replace('<url>', redirectUrl);

        window.open(instaUrl, '_blank');
    } else {
        alert("Please enter client id and redirect url first");
    }

});
SFSI(document).ready(function () {

    SFSI('#sfsi_jivo_offline_chat .tab-link').click(function () {
        var cur = SFSI(this);
        if (!cur.hasClass('active')) {
            var target = cur.find('a').attr('href');
            cur.parent().children().removeClass('active');
            cur.addClass('active');
            SFSI('#sfsi_jivo_offline_chat .tabs').children().hide();
            SFSI(target).show();
        }
    });
    SFSI('#sfsi_jivo_offline_chat #sfsi_sales form').submit(function (event) {
        event & event.preventDefault();
        // console.log(event);
        var target = SFSI(this).parents('.tab-content');
        var message = SFSI(this).find('textarea[name="question"]').val();
        var email = SFSI(this).find('input[name="email"]').val();
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        var nonce = SFSI(this).find('input[name="nonce"]').val();

        if ("" === email || false === re.test(String(email).toLowerCase())) {
            // console.log(SFSI(this).find('input[name="email"]'));
            SFSI(this).find('input[name="email"]').css('background-color', 'red');
            SFSI(this).find('input[name="email"]').on('keyup', function () {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                var email = SFSI(this).val();
                // console.log(email,re.test(String(email).toLowerCase()) );
                if ("" !== email && true === re.test(String(email).toLowerCase())) {
                    SFSI(this).css('background-color', '#fff');
                }
            })
            return false;

        }
        SFSI.ajax({
            url: sfsi_icon_ajax_object.ajax_url,
            type: "post",
            data: {
                action: "sfsiOfflineChatMessage",
                message: message,
                email: email,
                'nonce': nonce
            }
        }).done(function () {
            target.find('.before_message_sent').hide();
            target.find('.after_message_sent').show();
        });
    })
});

function sfsi_close_offline_chat(e) {
    e && e.preventDefault();

    SFSI('#sfsi_jivo_offline_chat').hide();
    SFSI('#sfsi_dummy_chat_icon').show();
}

function sfsi_open_quick_checkout(e) {
    e && e.preventDefault();
    // console.log(jQuery('.sfsi_quick-pay-box'));
    jQuery('.sfsi_quick-pay-box').show();
}

function sfsi_close_quickpay(e) {
    e && e.preventDefault();
    jQuery('.sfsi_quickpay-overlay').hide();
}

function sfsi_quickpay_container_click(event) {
    if (jQuery(event.target).hasClass('sellcodes-quick-purchase')) {
        jQuery(jQuery(event.target).find('p.sc-button img')[0]).click();
    }
}



// <------------------------* Responsive icon *----------------------->

function sfsi_responsive_icon_intraction_handler() {
    window.sfsi_fittext_shouldDisplay = true;
    SFSI('select[name="sfsi_responsive_icons_settings_edge_type"]').on('change', function () {
        $target_div = (SFSI(this).parent());
        if (SFSI(this).val() === "Round") {
            // console.log('Round', 'Round', SFSI(this).val());

            $target_div.parent().children().css('display', 'inline-block');
            $target_div.parent().next().css("display", "inline-block");
            var radius = jQuery('select[name="sfsi_responsive_icons_settings_edge_radius"]').val() + 'px'
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container,.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').css('border-radius', radius);

        } else {
            // console.log('sharp', 'sharp', SFSI(this).val(), $target_div.parent().children(), $target_div.parent().children().hide());

            $target_div.parent().children().hide();
            $target_div.show();
            $target_div.parent().next().hide();
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container,.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').css('border-radius', 'unset');

        }
    });
    SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').on('change', function () {
        $target_div = (SFSI(this).parent());
        if (SFSI(this).val() === "Fixed icon width") {
            $target_div.parent().children().css('display', 'inline-block');
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').css('width', 'auto').css('display', 'flex');
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container a').css('flex-basis', 'unset');
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').css('width', jQuery('input[name="sfsi_responsive_icons_sttings_icon_width_size"]').val());

            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container_box_fully_container').removeClass('sfsi_icons_container_box_fully_container').addClass('sfsi_icons_container_box_fixed_container');
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container_box_fixed_container').removeClass('sfsi_icons_container_box_fully_container').addClass('sfsi_icons_container_box_fixed_container');
            window.sfsi_fittext_shouldDisplay = true;
            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                if (jQuery(a_container).css('display') !== "none") {
                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                }
            })
        } else {
            $target_div.parent().children().hide();
            $target_div.show();
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').css('width', '100%').css('display', 'flex');
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container a').css('flex-basis', '100%');
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').css('width', '100%');

            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container_box_fixed_container').removeClass('sfsi_icons_container_box_fixed_container').addClass('sfsi_icons_container_box_fully_container');
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container_box_fully_container').removeClass('sfsi_icons_container_box_fixed_container').addClass('sfsi_icons_container_box_fully_container');
            window.sfsi_fittext_shouldDisplay = true;
            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                if (jQuery(a_container).css('display') !== "none") {
                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                }
            })
        }
        jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').removeClass('sfsi_fixed_count_container').removeClass('sfsi_responsive_count_container').addClass('sfsi_' + (jQuery(this).val() == "Fully responsive" ? 'responsive' : 'fixed') + '_count_container')
        jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container>a').removeClass('sfsi_responsive_fluid').removeClass('sfsi_responsive_fixed_width').addClass('sfsi_responsive_' + (jQuery(this).val() == "Fully responsive" ? 'fluid' : 'fixed_width'))
        sfsi_resize_icons_container();

    })
    jQuery(document).on('keyup', 'input[name="sfsi_responsive_icons_sttings_icon_width_size"]', function () {
        if (SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() === "Fixed icon width") {
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').css('width', jQuery(this).val() + 'px');
            window.sfsi_fittext_shouldDisplay = true;
            jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
                if (jQuery(a_container).css('display') !== "none") {
                    sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
                }
            })
        }
        sfsi_resize_icons_container();
    });
    jQuery(document).on('change', 'input[name="sfsi_responsive_icons_sttings_icon_width_size"]', function () {
        if (SFSI('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() === "Fixed icon width") {
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').css('width', jQuery(this).val() + 'px');
        }
    });
    jQuery(document).on('keyup', 'input[name="sfsi_responsive_icons_settings_margin"]', function () {
        jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container a,.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').css('margin-right', jQuery(this).val() + 'px');
    });
    jQuery(document).on('change', 'input[name="sfsi_responsive_icons_settings_margin"]', function () {
        jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container a,.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').css('margin-right', jQuery(this).val() + 'px');
        // jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').css('width',(jQuery('.sfsi_responsive_icons').width()-(jQuery('.sfsi_responsive_icons_count').width()+jQuery(this).val()))+'px');

    });
    jQuery(document).on('change', 'select[name="sfsi_responsive_icons_settings_text_align"]', function () {
        if (jQuery(this).val() === "Centered") {
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container a').css('text-align', 'center');
        } else {
            jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container a').css('text-align', 'left');
        }
    });
    jQuery('.sfsi_responsive_default_icon_container input.sfsi_responsive_input').on('keyup', function () {
        jQuery(this).parent().find('.sfsi_responsive_icon_item_container').find('span').text(jQuery(this).val());
        var iconName = jQuery(this).attr('name');
        var icon = iconName.replace('sfsi_responsive_', '').replace('_input', '');
        jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_' + (icon.toLowerCase()) + '_container span').text(jQuery(this).val());
        window.sfsi_fittext_shouldDisplay = true;
        jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
            }
        })
        sfsi_resize_icons_container();
    })
    jQuery('.sfsi_responsive_custom_icon_container input.sfsi_responsive_input').on('keyup', function () {
        jQuery(this).parent().find('.sfsi_responsive_icon_item_container').find('span').text(jQuery(this).val());
        var iconName = jQuery(this).attr('name');
        var icon = iconName.replace('sfsi_responsive_custom_', '').replace('_input', '');
        jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_' + icon + '_container span').text(jQuery(this).val())
        window.sfsi_fittext_shouldDisplay = true;
        jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
            }
        })
        sfsi_resize_icons_container();

    })

    jQuery('.sfsi_responsive_default_url_toggler').click(function (event) {

        event.preventDefault();
        sfsi_responsive_open_url(event);
    });
    jQuery('.sfsi_responsive_default_url_toggler').click(function (event) {
        event.preventDefault();
        sfsi_responsive_open_url(event);
    })
    jQuery('.sfsi_responsive_custom_url_hide, .sfsi_responsive_default_url_hide').click(function (event) {
        event.preventDefault();
        /* console.log(event,jQuery(event.target)); */
        jQuery(event.target).parent().parent().find('.sfsi_responsive_custom_url_hide').hide();
        jQuery(event.target).parent().parent().find('.sfsi_responsive_url_input').hide();
        jQuery(event.target).parent().parent().find('.sfsi_responsive_default_url_hide').hide();
        jQuery(event.target).parent().parent().find('.sfsi_responsive_default_url_toggler').show();
    });
    jQuery('select[name="sfsi_responsive_icons_settings_icon_size"]').change(function (event) {
        jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container,.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').removeClass('sfsi_small_button').removeClass('sfsi_medium_button').removeClass('sfsi_large_button').addClass('sfsi_' + (jQuery(this).val().toLowerCase()) + '_button');
        jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').removeClass('sfsi_small_button_container').removeClass('sfsi_medium_button_container').removeClass('sfsi_large_button_container').addClass('sfsi_' + (jQuery(this).val().toLowerCase()) + '_button_container')
    })
    jQuery(document).on('change', 'select[name="sfsi_responsive_icons_settings_edge_radius"]', function (event) {
        var radius = jQuery(this).val() + 'px'
        jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container,.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').css('border-radius', radius);

    });
    jQuery(document).on('change', 'select[name="sfsi_responsive_icons_settings_style"]', function (event) {
        if ('Flat' === jQuery(this).val()) {
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').removeClass('sfsi_responsive_icon_gradient');
        } else {
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').addClass('sfsi_responsive_icon_gradient');
        }
    });
    jQuery(document).on('mouseenter', '.sfsi_responsive_icon_preview .sfsi_icons_container a', function () {
        jQuery(this).css('opacity', 0.8);
    })
    jQuery(document).on('mouseleave', '.sfsi_responsive_icon_preview .sfsi_icons_container a', function () {
        jQuery(this).css('opacity', 1);
    })
    window.sfsi_fittext_shouldDisplay = true;
    jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
        if (jQuery(a_container).css('display') !== "none") {
            sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
        }
    })
    sfsi_resize_icons_container();
    jQuery('.ui-accordion-header.ui-state-default.ui-accordion-icons').click(function (data) {
        window.sfsi_fittext_shouldDisplay = true;
        jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
            if (jQuery(a_container).css('display') !== "none") {
                sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
            }
        })
        sfsi_resize_icons_container();
    });
    jQuery('select[name="sfsi_responsive_icons_settings_text_align"]').change(function (event) {
        var data = jQuery(event.target).val();
        if (data == "Centered") {
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').removeClass('sfsi_left-align_icon').addClass('sfsi_centered_icon');
        } else {
            jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container').removeClass('sfsi_centered_icon').addClass('sfsi_left-align_icon');
        }
    });
    jQuery('a.sfsi_responsive_custom_delete_btn').click(function (event) {
        event.preventDefault();
        var icon_num = jQuery(this).attr('data-id');
        //reset the current block;
        // var last_block = jQuery('.sfsi_responsive_custom_icon_4_container').clone();
        var cur_block = jQuery('.sfsi_responsive_custom_icon_' + icon_num + '_container');
        cur_block.find('.sfsi_responsive_custom_delete_btn').hide();
        cur_block.find('input[name="sfsi_responsive_custom_' + icon_num + '_added"]').val('no');
        cur_block.find('.sfsi_responsive_custom_' + icon_num + '_added').attr('value', 'no');
        cur_block.find('.radio_section.tb_4_ck .checkbox').click();
        cur_block.hide();


        if (icon_num > 0) {
            var prev_block = jQuery('.sfsi_responsive_custom_icon_' + (icon_num - 1) + '_container');
            prev_block.find('.sfsi_responsive_custom_delete_btn').show();
        }
        // jQuery('.sfsi_responsive_custom_icon_container').each(function(index,custom_icon){
        // 	var target= jQuery(custom_icon);
        // 	target.find('.sfsi_responsive_custom_delete_btn');
        // 	var custom_id = target.find('.sfsi_responsive_custom_delete_btn').attr('data-id');
        // 	if(custom_id>icon_num){
        // 		target.removeClass('sfsi_responsive_custom_icon_'+custom_id+'_container').addClass('sfsi_responsive_custom_icon_'+(custom_id-1)+'_container');
        // 		target.find('input[name="sfsi_responsive_custom_'+custom_id+'_added"]').attr('name',"sfsi_responsive_custom_"+(custom_id-1)+"_added");
        // 		target.find('#sfsi_responsive_'+custom_id+'_display').removeClass('sfsi_responsive_custom_'+custom_id+'_display').addClass('sfsi_responsive_custom_'+(custom_id-1)+'_display').attr('id','sfsi_responsive_'+(custom_id-1)+'_display').attr('name','sfsi_responsive_custom_'+(custom_id-1)+'_display').attr('data-custom-index',(custom_id-1));
        // 		target.find('.sfsi_responsive_icon_item_container').removeClass('sfsi_responsive_icon_custom_'+custom_id+'_container').addClass('sfsi_responsive_icon_custom_'+(custom_id-1)+'_container');
        // 		target.find('.sfsi_responsive_input').attr('name','sfsi_responsive_custom_'+(custom_id-1)+'_input');
        // 		target.find('.sfsi_responsive_url_input').attr('name','sfsi_responsive_custom_'+(custom_id-1)+'_url_input');
        // 		target.find('.sfsi_bg-color-picker').attr('name','sfsi_responsive_icon_'+(custom_id-1)+'_bg_color');
        // 		target.find('.sfsi_logo_upload sfsi_logo_custom_'+custom_id+'_upload').removeClass('sfsi_logo_upload sfsi_logo_custom_'+custom_id+'_upload').addClass('sfsi_logo_upload sfsi_logo_custom_'+(custom_id-1)+'_upload');
        // 		target.find('input[type="sfsi_responsive_icons_custom_'+custom_id+'_icon"]').attr('name','input[type="sfsi_responsive_icons_custom_'+(custom_id-1)+'_icon"]');				
        // 		target.find('.sfsi_responsive_custom_delete_btn').attr('data-id',''+(custom_id-1));				
        // 	}
        // });
        // // sfsi_backend_section_beforeafter_set_fixed_width();
        //    // jQuery(window).on('resize',sfsi_backend_section_beforeafter_set_fixed_width);
        // var new_block=jQuery('.sfsi_responsive_custom_icon_container').clone();
        // jQuery('.sfsi_responsive_custom_icon_container').remove();
        // jQuery('.sfsi_responsive_default_icon_container').parent().append(last_block).append();
        // jQuery('.sfsi_responsive_default_icon_container').parent().append(new_block);
        // return false;
    })
}

function sfsi_responsive_icon_counter_tgl(hide, show, ref = null) {
    if (null !== hide && '' !== hide) {
        jQuery('.' + hide).hide();
    }
    if (null !== show && '' !== show) {
        jQuery('.' + show).show();
    }
}


function sfsi_responsive_open_url(event) {
    jQuery(event.target).parent().find('.sfsi_responsive_custom_url_hide').show();
    jQuery(event.target).parent().find('.sfsi_responsive_default_url_hide').show();
    jQuery(event.target).parent().find('.sfsi_responsive_url_input').show();
    jQuery(event.target).hide();
}

function sfsi_responsive_icon_hide_responsive_options() {
    jQuery('.sfsi_PostsSettings_section').show();
    jQuery('.sfsi_choose_post_types_section').show();
    jQuery('.sfsi_not_responsive').show();
    // jQuery('.sfsi_icn_listing8.sfsi_closerli').hide();
}

function sfsi_responsive_icon_show_responsive_options() {
    jQuery('.sfsi_PostsSettings_section').hide();
    // jQuery('.sfsi_PostsSettings_section').show();
    jQuery('.sfsi_choose_post_types_section').hide();
    jQuery('.sfsi_not_responsive').hide();
    window.sfsi_fittext_shouldDisplay = true;
    jQuery('.sfsi_responsive_icon_preview a').each(function (index, a_container) {
        if (jQuery(a_container).css('display') !== "none") {
            sfsi_fitText(jQuery(a_container).find('.sfsi_responsive_icon_item_container'));
        }
    })
    sfsi_resize_icons_container();
}

function sfsi_scroll_to_div(option_id, scroll_selector) {
    jQuery('#' + option_id + '.ui-accordion-header[aria-selected="false"]').click() //opened the option
    //scroll to it.
    if (scroll_selector && scroll_selector !== '') {
        scroll_selector = scroll_selector;
    } else {
        scroll_selector = '#' + option_id + '.ui-accordion-header';
    }
    jQuery('html, body').stop().animate({
        scrollTop: jQuery(scroll_selector).offset().top
    }, 1000);
}

function sfsi_fitText(container) {
    /* 	console.log(container,container.parent().parent(),container.parent().parent().hasClass('sfsi_icons_container_box_fixed_container')); */
    if (container.parent().parent().hasClass('sfsi_icons_container_box_fixed_container')) {
        /* console.log(window.sfsi_fittext_shouldDisplay); */
        if (window.sfsi_fittext_shouldDisplay === true) {
            if (jQuery('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() == "Fully responsive") {
                var all_icon_width = jQuery('.sfsi_responsive_icons .sfsi_icons_container').width();
                console.log(all_icon_width, 'width of icons');
                var total_active_icons = jQuery('.sfsi_responsive_icons .sfsi_icons_container a').filter(function (i, icon) {
                    return jQuery(icon).css('display') && (jQuery(icon).css('display').toLowerCase() !== "none");
                }).length;

                var distance_between_icon = jQuery('input[name="sfsi_responsive_icons_settings_margin"]').val()
                var container_width = ((all_icon_width - distance_between_icon) / total_active_icons) - 5;
                container_width = (container_width - distance_between_icon);
            } else {
                var container_width = container.width();
                console.log(container_width, 'width of icons');

            }
            // var container_img_width = container.find('img').width();
            var container_img_width = 70;
            // var span=container.find('span').clone();
            var span = container.find('span');
            // var span_original_width = container.find('span').width();
            var span_original_width = container_width - (container_img_width)
            span
                // .css('display','inline-block')
                .css('white-space', 'nowrap')
            // .css('width','auto')
            ;
            var span_flatted_width = span.width();
            if (span_flatted_width == 0) {
                span_flatted_width = span_original_width;
            }
            span
                // .css('display','inline-block')
                .css('white-space', 'unset')
            // .css('width','auto')
            ;
            var shouldDisplay = ((undefined === window.sfsi_fittext_shouldDisplay) ? true : window.sfsi_fittext_shouldDisplay = true);
            var fontSize = parseInt(span.css('font-size'));

            if (6 > fontSize) {
                fontSize = 20;
            }

            var computed_fontSize = (Math.floor((fontSize * span_original_width) / span_flatted_width));

            if (computed_fontSize < 8) {
                shouldDisplay = false;
                window.sfsi_fittext_shouldDisplay = false;
                computed_fontSize = 20;
            }
            span.css('font-size', Math.min(computed_fontSize, 20));
            span
                // .css('display','inline-block')
                .css('white-space', 'nowrap')
            // .css('width','auto')
            ;
            if (shouldDisplay) {
                span.show();
            } else {
                span.hide();
                jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icon_item_container  span').hide();
            }
        }
    } else {
        var span = container.find('span');
        /* 	console.log(span); */
        span.css('font-size', 'initial');
        span.show();
    }

}

function sfsi_fixedWidth_fitText(container) {
    return;
    /* console.log(sfsi_fittext_shouldDisplay); */
    if (window.sfsi_fittext_shouldDisplay === true) {
        if (jQuery('select[name="sfsi_responsive_icons_settings_icon_width_type"]').val() == "Fixed icon width") {
            var all_icon_width = jQuery('.sfsi_responsive_icons .sfsi_icons_container').width();
            var total_active_icons = jQuery('.sfsi_responsive_icons .sfsi_icons_container a').filter(function (i, icon) {
                return jQuery(icon).css('display') && (jQuery(icon).css('display').toLowerCase() !== "none");
            }).length;
            var distance_between_icon = jQuery('input[name="sfsi_responsive_icons_settings_margin"]').val()
            var container_width = ((all_icon_width - distance_between_icon) / total_active_icons) - 5;
            container_width = (container_width - distance_between_icon);
        } else {
            var container_width = container.width();
        }
        // var container_img_width = container.find('img').width();
        var container_img_width = 70;
        // var span=container.find('span').clone();
        var span = container.find('span');
        // var span_original_width = container.find('span').width();
        var span_original_width = container_width - (container_img_width)
        span
            // .css('display','inline-block')
            .css('white-space', 'nowrap')
        // .css('width','auto')
        ;
        var span_flatted_width = span.width();
        if (span_flatted_width == 0) {
            span_flatted_width = span_original_width;
        }
        span
            // .css('display','inline-block')
            .css('white-space', 'unset')
        // .css('width','auto')
        ;
        var shouldDisplay = undefined === window.sfsi_fittext_shouldDisplay ? true : window.sfsi_fittext_shouldDisplay = true;;
        var fontSize = parseInt(span.css('font-size'));

        if (6 > fontSize) {
            fontSize = 15;
        }

        var computed_fontSize = (Math.floor((fontSize * span_original_width) / span_flatted_width));

        if (computed_fontSize < 8) {
            shouldDisplay = false;
            window.sfsi_fittext_shouldDisplay = false;
            computed_fontSize = 15;
        }
        span.css('font-size', Math.min(computed_fontSize, 15));
        span
            // .css('display','inline-block')
            .css('white-space', 'nowrap')
        // .css('width','auto')
        ;
        // var heightOfResIcons = jQuery('.sfsi_responsive_icon_item_container').height();

        //   if(heightOfResIcons < 17){
        //     span.show();
        //   }else{
        //     span.hide();
        //   }

        if (shouldDisplay) {
            span.show();
        } else {
            span.hide();
        }
    }
}

function sfsi_resize_icons_container() {
    // resize icon container based on the size of count
    sfsi_cloned_icon_list = jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').clone();
    sfsi_cloned_icon_list.removeClass('.sfsi_responsive_icon_preview .sfsi_responsive_with_counter_icons').addClass('sfsi_responsive_cloned_list');
    sfsi_cloned_icon_list.css('width', '100%');
    jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').parent().append(sfsi_cloned_icon_list);

    // sfsi_cloned_icon_list.css({
    //       position: "absolute",
    //       left: "-10000px"
    //   }).appendTo("body");
    actual_width = sfsi_cloned_icon_list.width();
    count_width = jQuery('.sfsi_responsive_icon_preview .sfsi_responsive_icons_count').width();
    jQuery('.sfsi_responsive_cloned_list').remove();
    sfsi_inline_style = jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').attr('style');
    // remove_width 
    sfsi_inline_style = sfsi_inline_style.replace(/width:auto($|!important|)(;|$)/g, '').replace(/width:\s*(-|)\d*\s*(px|%)\s*($|!important|)(;|$)/g, '');
    if (!(jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').hasClass('sfsi_responsive_without_counter_icons') && jQuery('.sfsi_icons_container').hasClass('sfsi_icons_container_box_fixed_container'))) {
        sfsi_inline_style += "width:" + (actual_width - count_width - 1) + 'px!important;'
    } else {
        sfsi_inline_style += "width:auto!important;";
    }
    jQuery('.sfsi_responsive_icon_preview .sfsi_icons_container').attr('style', sfsi_inline_style);

}

function sfsi_togglbtmsection(show, hide, ref) {
    // console.log(show,hide);
    jQuery(ref).parent("ul").children("li.clckbltglcls").each(function (index, element) {
        jQuery(this).children(".radio").css("background-position", "0px 0px");
        jQuery(this).children(".styled").attr("checked", "false");
    });
    jQuery(ref).children(".radio").css("background-position", "0px -41px");
    jQuery(ref).children(".styled").attr("checked", "true");
    // console.log(show,hide);

    jQuery("." + show).show();
    jQuery("." + show).children(".radiodisplaysection").show();
    jQuery("." + hide).hide();
    jQuery("." + hide).children(".radiodisplaysection").hide();
}
jQuery(document).ready(function () {
    var sfsi_functions_loaded = new CustomEvent('sfsi_functions_loaded', {
        detail: {
            "abc": "def"
        }
    });
    window.dispatchEvent(sfsi_functions_loaded);

});

function sfsi_show_responsive() {
    var icon_type = jQuery('input[name="sfsi_display_button_type"]:checked').val();
    var responsive_show = jQuery('input[name="sfsi_responsive_icons_end_post"]:checked').val();
    setTimeout(function () {
        // console.log(icon_type, responsive_show,icon_type=="responsive_button" && responsive_show=="yes");
        if (icon_type == "responsive_button" && responsive_show == "yes") {
            jQuery('.sfsi_responsive_icon_option_li.sfsi_responsive_show').show();
        } else {
            jQuery('.sfsi_responsive_icon_option_li.sfsi_responsive_show').hide();
        }
    }, 100);
}


function sfsi_save_export() {
    var nonce = SFSI("#sfsi_save_export").attr("data-nonce");
    console.log(nonce);
    var data = {
        action: "sfsi_save_export",
        nonce: nonce
    };
    console.log(data);
    SFSI.ajax({
        url: sfsi_icon_ajax_object.ajax_url,
        type: "post",
        data: data,
        success: function (s) {
            console.log(s);
            if (s == "wrong_nonce") {
                showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
                global_error = 1;
            } else {
                var date = new Date();
                var timestamp = date.getTime();
                var blob = new Blob([JSON.stringify(s, null, 2)], {
                    type: 'application/json'
                });
                var url = URL.createObjectURL(blob);
                let link = document.createElement("a");
                link.href = url;
                link.download = "sfsi_export_options" + timestamp + ".json"
                link.innerText = "Open the array URL";
                document.body.appendChild(link);
                link.click();
                (showErrorSuc("Settings Exported !", "Settings Exported !", 10));
            }
        }
    });

}

function sfsi_installDate_save() {
    var nonce = SFSI("#sfsi_installDate").attr("data-nonce");
    console.log(nonce);
    var sfsi_installDate = SFSI("input[name='sfsi_installDate']").val();
    var data = {
        action: "sfsi_installDate",
        sfsi_installDate: sfsi_installDate,
        nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}

function sfsi_currentDate_save(){
    var nonce = SFSI("#sfsi_currentDate").attr("data-nonce");
    console.log(nonce);
    var sfsi_currentDate = SFSI("input[name='sfsi_currentDate']").val();
	var data = {
        action: "sfsi_currentDate",
        sfsi_currentDate:sfsi_currentDate,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}

function sfsi_showNextBannerDate_save(){
    var nonce = SFSI("#sfsi_showNextBannerDate").attr("data-nonce");
    console.log(nonce);
    var sfsi_showNextBannerDate = SFSI("input[name='sfsi_showNextBannerDate']").val();
	var data = {
        action: "sfsi_showNextBannerDate",
        sfsi_showNextBannerDate:sfsi_showNextBannerDate,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}

function sfsi_cycleDate_save(){
    var nonce = SFSI("#sfsi_cycleDate").attr("data-nonce");
    console.log(nonce);
    var sfsi_cycleDate = SFSI("input[name='sfsi_cycleDate']").val();
	var data = {
        action: "sfsi_cycleDate",
        sfsi_cycleDate:sfsi_cycleDate,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}

function sfsi_loyaltyDate_save(){
    var nonce = SFSI("#sfsi_loyaltyDate").attr("data-nonce");
    console.log(nonce);
    var sfsi_loyaltyDate = SFSI("input[name='sfsi_loyaltyDate']").val();
	var data = {
        action: "sfsi_loyaltyDate",
        sfsi_loyaltyDate:sfsi_loyaltyDate,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_firsttime_offer_save(){
    var nonce = SFSI("#sfsi_banner_global_firsttime_offer").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_firsttime_offer = SFSI("input[name='sfsi_banner_global_firsttime_offer']").val();
	var data = {
        action: "sfsi_banner_global_firsttime_offer",
        sfsi_banner_global_firsttime_offer:sfsi_banner_global_firsttime_offer,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_pinterest_save(){
    var nonce = SFSI("#sfsi_banner_global_pinterest").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_pinterest = SFSI("input[name='sfsi_banner_global_pinterest']").val();
	var data = {
        action: "sfsi_banner_global_pinterest",
        sfsi_banner_global_pinterest:sfsi_banner_global_pinterest,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_social_save(){
    var nonce = SFSI("#sfsi_banner_global_social").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_social = SFSI("input[name='sfsi_banner_global_social']").val();
	var data = {
        action: "sfsi_banner_global_social",
        sfsi_banner_global_social:sfsi_banner_global_social,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_load_faster_save(){
    var nonce = SFSI("#sfsi_banner_global_load_faster").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_load_faster = SFSI("input[name='sfsi_banner_global_load_faster']").val();
	var data = {
        action: "sfsi_banner_global_load_faster",
        sfsi_banner_global_load_faster:sfsi_banner_global_load_faster,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_shares_save(){
    var nonce = SFSI("#sfsi_banner_global_shares").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_shares = SFSI("input[name='sfsi_banner_global_shares']").val();
	var data = {
        action: "sfsi_banner_global_shares",
        sfsi_banner_global_shares:sfsi_banner_global_shares,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_gdpr_save(){
    var nonce = SFSI("#sfsi_banner_global_gdpr").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_gdpr = SFSI("input[name='sfsi_banner_global_gdpr']").val();
	var data = {
        action: "sfsi_banner_global_gdpr",
        sfsi_banner_global_gdpr:sfsi_banner_global_gdpr,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_http_save(){
    var nonce = SFSI("#sfsi_banner_global_http").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_http = SFSI("input[name='sfsi_banner_global_http']").val();
	var data = {
        action: "sfsi_banner_global_http",
        sfsi_banner_global_http:sfsi_banner_global_http,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}


function sfsi_banner_global_upgrade_save(){
    var nonce = SFSI("#sfsi_banner_global_upgrade").attr("data-nonce");
    console.log(nonce);
    var sfsi_banner_global_upgrade = SFSI("input[name='sfsi_banner_global_upgrade']").val();
	var data = {
        action: "sfsi_banner_global_upgrade",
        sfsi_banner_global_upgrade:sfsi_banner_global_upgrade,
		nonce: nonce
    };
    console.log(data);
	SFSI.ajax({
		url: sfsi_icon_ajax_object.ajax_url,
		type: "post",
		data: data,
		success: function (s) {
			console.log(s);
			if (s == "wrong_nonce") {
				showErrorSuc("error", "Unauthorised Request, Try again after refreshing page", 6);
				global_error = 1;
			} else {
				console.log(s);
			}
		}
	});
}

