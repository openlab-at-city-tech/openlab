'use strict';

/**
 * Used to handle :
 *    Click of Non-Installed Plugins
 *    Show Upsell Popup
 *    Unbind Drag & Drop

 */

let SbElementorHandler = window.SbElementorHandler || (function (_document, window, $) {

    const smashBalloonPlugins = sbHandler.smashPlugins;

    let app = {

        init: function () {
            app.events();
        },

        events: function () {

            $(window).on('elementor/frontend/init', function () {
                app.disableInactiveSmashWidgets();
                setTimeout(function () {
                    elementor.panel.$el.on('click', function () {
                        app.disableInactiveSmashWidgets();
                    });
                }, 300)
            });

        },

        disableInactiveSmashWidgets: function () {
            setTimeout(function () {
                for (const pluginName in smashBalloonPlugins) {
                    let plugin = smashBalloonPlugins[pluginName],
                        pluginWidget = elementor.panel.$el.find("#elementor-panel-category-smash-balloon").find('.sb-elem-inactive.sb-elem-' + pluginName).parents('.elementor-element-wrapper').find('.elementor-element');

                    pluginWidget.attr('draggable', false);
                    pluginWidget.on('click', function () {
                        app.createUpsellPopup(pluginName);
                    });
                }
            }, 500)
        },

        createUpsellPopup: function (pluginName) {
            let plugin = smashBalloonPlugins[pluginName],
                spinnerIcon = '<svg  x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/></path></svg>',
                upsellPopupOutput =
                    '<div class="sb-source-ctn sb-fs-boss sb-center-boss">\
                        <div class="sb-source-popup sb-popup-inside sb-install-plugin-modal">\
                            <div class="sb-popup-cls">\
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">\
                                    <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#141B38"/>\
                                </svg>\
                            </div>\
                            <div class="sb-install-plugin-body sb-fs">\
                                <div class="sb-install-plugin-header">\
                                    <div class="sb-plugin-image">' + plugin["icon"] + '\
			                    <svg class="sb-plugin-cta-logo" width="26" height="33" viewBox="0 0 26 33" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M25.5608 15.2708C25.5608 6.86286 20.0495 0.046814 13.2486 0.046814C6.44763 0.046814 0.933838 6.86286 0.933838 15.2708C0.933838 23.3312 5.98416 29.9079 12.3795 30.4596L11.6995 32.6132L15.9639 32.2505L14.4677 30.4218C20.6943 29.6661 25.5608 23.1725 25.5608 15.2708Z" fill="#FE544F"/><path fill-rule="evenodd" clip-rule="evenodd" d="M16.1843 5.39911L16.7768 11.5131L22.9165 11.6894L18.4752 15.8189L21.983 20.8926L16.0735 19.7817L14.282 25.6913L11.5618 20.3993L6.0693 22.916L8.18218 17.2428L2.82544 14.5409L8.55968 12.6968L6.97737 7.04787L12.4024 10.1407L16.1843 5.39911Z" fill="white"/></svg>\
			                    </div>\
			                    <div class="sb-plugin-name">\
			                    	<strong>Requires</strong>\
			                        <h3>\
			                            ' + pluginName + '\
			                            <span>Free</span>\
			                        </h3>\
			                    </div>\
			                </div>\
			                <div class="sb-install-plugin-content">\
			                    <p>' + plugin["description"] + '</p>\
			                    <button class="sb-install-plugin-btn sb-btn-orange sb-plugin-btn" data-plugin="' + plugin['download_plugin'] + '">\
			                        <span class="sb-install-plugin-spinner" style="display:none">' + spinnerIcon + '</span>\
			                        Install\
			                    </button>\
			                    <button class="sb-install-refresh-btn sb-btn-blue sb-plugin-btn" style="display:none">\
			                        Refresh The Page\
			                    </button>\
			                </div>\
			            </div>\
			        </div>\
			    </div>';
            if ($(window.parent.document.body).find('.sb-center-boss').length === 0) {
                $(window.parent.document.body).append(upsellPopupOutput);
            }
            $(window.parent.document.body).find('.sb-install-plugin-btn').on('click', function () {
                let downloadPlugin = $(this).attr('data-plugin');
                $(this).find('.sb-install-plugin-spinner').show();
                app.installPlugin(downloadPlugin);
            });

            $(window.parent.document.body).find('.sb-install-refresh-btn').on('click', function () {
                window.parent.location.reload();
            });

            $(window.parent.document.body).find('.sb-popup-cls').on('click', function () {
                app.closeUpsellPopup();
            });
        },
        closeUpsellPopup: function () {
            $(window.parent.document.body).find('.sb-center-boss').remove();
        },

        installPlugin: function (downloadPlugin) {
            let data = new FormData();
            data.append('action', 'sbi_install_addon');
            data.append('nonce', sbHandler.nonce);
            data.append('plugin', downloadPlugin);
            data.append('type', 'plugin');
            fetch(sbHandler.ajax_handler, {
                method: "POST",
                credentials: 'same-origin',
                body: data
            })
                .then(response => response.json())
                .then(data => {
                    $(window.parent.document.body).find('.sb-install-plugin-btn').hide();
                    if (data.success == true) {
                        $(window.parent.document.body).find('.sb-install-refresh-btn').show();

                    }

                });
        }

    };


    return app;

}(document, window, jQuery));

SbElementorHandler.init();