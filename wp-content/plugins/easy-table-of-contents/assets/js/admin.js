jQuery(document).ready(function ($) {

    var ez_toc_color_picker = $('.ez-toc-color-picker');

    if (ez_toc_color_picker.length) {
        ez_toc_color_picker.wpColorPicker();
    }

    var ezTocSettingsWidth = document.getElementById('ez-toc-settings[width]');
    var ezTocSettingsCustomWidth = document.getElementById('ez-toc-settings[width_custom]');

    if(ezTocSettingsCustomWidth) {
        if(ezTocSettingsWidth.value != 'custom')
            ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "none";

        ezTocSettingsWidth.addEventListener('change', function () {
            if (document.getElementById('ez-toc-settings[width]').value == 'custom') {
                ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "revert";
            } else {
                ezTocSettingsCustomWidth.parentNode.parentNode.style.display = "none";
            }
        });
    }

    $("#reset-options-to-default-button").click(function() {
        let text = "Do you want reset settings to default options?";
        if (confirm(text) == true) {
            $.post(ajaxurl, { action: 'eztoc_reset_options_to_default', eztoc_security_nonce: cn_toc_admin_data.eztoc_security_nonce },
                    function (data) {
                        alert('Default Options Reset Now!');
                        window.location.reload();
                    }
            );
        }

    });

    $("#subscribe-newsletter-form").on('submit', function (e) {
        e.preventDefault();
        var $form = $("#subscribe-newsletter-form");
        var name = $form.find('input[name="name"]').val();
        var email = $form.find('input[name="email"]').val();
        var website = $form.find('input[name="company"]').val();
        $.post(ajaxurl, {action: 'eztoc_subscribe_newsletter', name: name, email: email, website: website, eztoc_security_nonce: cn_toc_admin_data.eztoc_security_nonce},
                function (data) {
                    if(data === 'security_nonce_not_verified' ){
                        alert('Security nonce not verified');
                        return false;
                    } 
                    
                }
        );
    });

    let position = $('#eztoc-general').find("select[name='ez-toc-settings[position]']");
    let customParaNumber = $('#eztoc-general').find("input[name='ez-toc-settings[custom_para_number]']");
    let customImgNumber = $('#eztoc-general').find("input[name='ez-toc-settings[custom_img_number]']");
    let blockQCheckB = $('#eztoc-general').find("input[name='ez-toc-settings[blockqoute_checkbox]']");
    if($(position).val() == 'aftercustompara'){
        $(customParaNumber).parents('tr').show();
    }else{
        $(customParaNumber).parents('tr').hide();
    }
    if($(position).val() == 'afterpara' || $(position).val() == 'aftercustompara'){
        $(blockQCheckB).parents('tr').show();
    }else{
        $(blockQCheckB).parents('tr').hide();
    }
    if($(position).val() == 'aftercustomimg'){
        $(customImgNumber).parents('tr').show();
    }else{
        $(customImgNumber).parents('tr').hide();
    }
    $(document).on("change", "select[name='ez-toc-settings[position]']", function() {
        if($(this).val() == 'aftercustompara'){
            $(customParaNumber).parents('tr').show(500);    
        }else{
            $(customParaNumber).parents('tr').hide(500);
        }    
        if($(this).val() == 'afterpara' || $(this).val() == 'aftercustompara'){
            $(blockQCheckB).parents('tr').show(500);
        }else{
            $(blockQCheckB).parents('tr').hide(500);
        }
        if($(this).val() == 'aftercustomimg'){
            $(customImgNumber).parents('tr').show(500);    
        }else{
            $(customImgNumber).parents('tr').hide(500);
        }
    });
    let check_method = $('#eztoc-general').find("select[name='ez-toc-settings[toc_loading]']");
    let smoothCheck = $('#eztoc-general').find("input[name='ez-toc-settings[smooth_scroll]']");
    let anchsJump = $('#eztoc-general').find("input[name='ez-toc-settings[avoid_anch_jump]']");
    let js_where = $('#eztoc-advanced').find("select[name='ez-toc-settings[load_js_in]']");
    if($(check_method).val() == 'js'){
        $(smoothCheck).parents('tr').show();
        $(anchsJump).parents('tr').show();
        $(js_where).parents('tr').show();
    }else{
        $(smoothCheck).parents('tr').hide();
        $(anchsJump).parents('tr').hide();
        $(js_where).parents('tr').hide();
    }
    $(document).on("change", "select[name='ez-toc-settings[toc_loading]']", function() {
        if($(this).val() == 'js'){
            $(smoothCheck).parents('tr').show(500);    
            $(anchsJump).parents('tr').show(500);    
            $(js_where).parents('tr').show(500);    
        }else{
            $(smoothCheck).parents('tr').hide(500);
            $(anchsJump).parents('tr').hide(500);
            $(js_where).parents('tr').hide(500);
        }
    });

    let stickyHighlight = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky_highlight_heading]']");
    let stickyHighlightBg = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky_highlight_bg_colour]']");
    let stickyHighlightTitle = $('#eztoc-sticky').find("input[name='ez-toc-settings[sticky_highlight_title_colour]']");
    if($(stickyHighlight).prop('checked') == true){
        $(stickyHighlightBg).parents('tr').show();
        $(stickyHighlightTitle).parents('tr').show();
    }else{
        $(stickyHighlightBg).parents('tr').hide();
        $(stickyHighlightTitle).parents('tr').hide();
    }
    $(document).on("change", "input[name='ez-toc-settings[sticky_highlight_heading]']", function() {
        if($(this).prop('checked') == true){
            $(stickyHighlightBg).parents('tr').show(500);    
            $(stickyHighlightTitle).parents('tr').show(500);    
        }else{
            $(stickyHighlightBg).parents('tr').hide(500);
            $(stickyHighlightTitle).parents('tr').hide(500);
        }
    });
    

});

/**
 * DisableScrolling Function
 * @since 2.0.33
 */
function disableScrolling() {
    var x=window.scrollX;
    var y=window.scrollY;
    window.onscroll=function(){window.scrollTo(x, y);};
}
/**
 * EnableScrolling Function
 * @since 2.0.33
 */
function enableScrolling(){
    ezTocSettingsTabsFixed();
}

/**
 * unsecuredCopyToClipboard Function
 * Clipboard JS
 * @since 2.0.33
 */
const unsecuredCopyToClipboard = (text) => {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy')
    } catch (err) {
        console.error('Unable to copy to clipboard', err)
    }
    document.body.removeChild(textArea)
};
/**
 * ez_toc_clipboard Function
 * Clipboard JS
 * @since 2.0.33
 */
function ez_toc_clipboard(id, tooltipId, $this, event) {
    event.preventDefault();
    disableScrolling();
    var copyText = $this.parentNode.parentNode.querySelectorAll("#" + id)[0];
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    unsecuredCopyToClipboard(copyText.value);

    var tooltip = $this.querySelectorAll('span.' + tooltipId)[0];
    tooltip.innerHTML = "Copied: " + copyText.value;
}
/**
 * ez_toc_outFunc Function
 * Clipboard JS
 * @since 2.0.33
 */
function ez_toc_outFunc(tooltipId, $this, event) {
    event.preventDefault();
    var tooltip = $this.querySelectorAll('span.' + tooltipId)[0];
    tooltip.innerHTML = "Copy to clipboard";
    enableScrolling();
}

/**
 * ezTocSettingsTabsFixed Function
 * Apply Fixed CSS & JS for General Settings Tabs
 * @since 2.0.38
 */
function ezTocSettingsTabsFixed() {
    var ezTocProSettingsContainer = '<span class="general-pro-settings-container"> | <a href="#eztoc-prosettings" id="eztoc-link-prosettings">Pro Settings</a></span>';

    var ezTocGeneralTabs = document.querySelector("#general #eztoc-tabs");
    var ezTocGeneralForm = document.querySelector("#general form");

    if(ezTocGeneralTabs !== null) {
        window.onscroll = function () {
            var y = window.scrollY;

            var ez_toc_pro_settings_link_paid = document.getElementsByClassName('ez-toc-pro-settings-link-paid');
            var ezTocElementProSettingsContainer = document.getElementsByClassName("general-pro-settings-container");

            var ezTocGeneralTabsLinkGeneral = document.querySelector("#general #eztoc-tabs #eztoc-link-general");
            var ezTocGeneralTabsLinkAppearance = document.querySelector("#general #eztoc-tabs #eztoc-link-appearance");
            var ezTocGeneralTabsLinkAdvanced = document.querySelector("#general #eztoc-tabs #eztoc-link-advanced");
            var ezTocGeneralTabsLinkShortcode = document.querySelector("#general #eztoc-tabs #eztoc-link-shortcode");
            var ezTocGeneralTabsLinkSticky = document.querySelector("#general #eztoc-tabs #eztoc-link-sticky");
            var ezTocGeneralTabsLinkCompatibility = document.querySelector("#general #eztoc-tabs #eztoc-link-compatibility");
            var ezTocGeneralTabsLinkIeSettings = document.querySelector("#general #eztoc-tabs #eztoc-link-iesettings");
            var ezTocGeneralTabsLinkProSettings = document.querySelector("#general #eztoc-tabs #eztoc-link-prosettings");

            var minusOffsetTop = 100;

            var ezTocGeneralContainerGeneral = document.querySelector("#general div#eztoc-general").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerAppearance = document.querySelector("#general div#eztoc-appearance").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerAdvanced = document.querySelector("#general div#eztoc-advanced").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerShortcode = document.querySelector("#general div#eztoc-shortcode").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerCompatibility = document.querySelector("#general div#eztoc-compatibility").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerIeSettings = document.querySelector("#general div#eztoc-iesettings").offsetTop - minusOffsetTop;
            var ezTocGeneralContainerSticky = document.querySelector("#eztoc-sticky").offsetTop - minusOffsetTop;
           
            if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0) {
                var ezTocGeneralContainerProSettings = document.querySelector("#general div#eztoc-prosettings").offsetTop - minusOffsetTop - 150;
            } else {
                ezTocGeneralContainerCompatibility -= 150;
                ezTocGeneralContainerIeSettings -= 150;
            }
            ezTocGeneralTabsLinkGeneral.classList.add('active');
            ezTocGeneralTabsLinkAppearance.classList.remove('active');
            ezTocGeneralTabsLinkAdvanced.classList.remove('active');
            ezTocGeneralTabsLinkShortcode.classList.remove('active');
            ezTocGeneralTabsLinkCompatibility.classList.remove('active');
            ezTocGeneralTabsLinkIeSettings.classList.remove('active');
            ezTocGeneralTabsLinkSticky.classList.remove('active');
            if (ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                ezTocGeneralTabsLinkProSettings.classList.remove('active');

            if (y >= 100) {
                ezTocGeneralTabs.classList.remove('stay');
                ezTocGeneralTabs.classList.add('moving');
                ezTocGeneralForm.classList.add('moving');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length == 0)
                    ezTocGeneralTabs.innerHTML += ezTocProSettingsContainer;
            } else {
                ezTocGeneralTabs.classList.remove('moving');
                ezTocGeneralTabs.classList.add('stay');
                ezTocGeneralForm.classList.remove('moving');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0)
                    document.querySelector(".general-pro-settings-container").remove();
            }

            if (y >= ezTocGeneralContainerGeneral) {
                ezTocGeneralTabsLinkGeneral.classList.add('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerAppearance) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.add('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
               if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerAdvanced) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.add('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerShortcode) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.add('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerSticky) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.add('active');
                
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerCompatibility) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.add('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (y >= ezTocGeneralContainerIeSettings) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.add('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.remove('active');
            }
            if (ezTocElementProSettingsContainer.length > 0 && y >= ezTocGeneralContainerProSettings) {
                ezTocGeneralTabsLinkGeneral.classList.remove('active');
                ezTocGeneralTabsLinkAppearance.classList.remove('active');
                ezTocGeneralTabsLinkAdvanced.classList.remove('active');
                ezTocGeneralTabsLinkShortcode.classList.remove('active');
                ezTocGeneralTabsLinkCompatibility.classList.remove('active');
                ezTocGeneralTabsLinkIeSettings.classList.remove('active');
                ezTocGeneralTabsLinkSticky.classList.remove('active');
                if(ez_toc_pro_settings_link_paid !== null && ez_toc_pro_settings_link_paid.length > 0 && ezTocElementProSettingsContainer.length > 0 && ezTocGeneralTabsLinkProSettings !== null)
                    ezTocGeneralTabsLinkProSettings.classList.add('active');
            }
        };
    } else {
        window.onscroll = function () {}
    }
}
ezTocSettingsTabsFixed();

