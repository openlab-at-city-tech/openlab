jQuery(function ($) {
    let $modal = $('#bookly-js-update-plugins-modal'),
        $list = $('.bookly-js-plugins-list', $modal),
        spinner = '<span class="spinner" style="float: none; margin: -2px 0 0 2px"></span>',
        icon = '<img src="{src}" style="vertical-align:middle; height:24px; margin-right: 8px; border-radius: 3px; padding-bottom:3px">',
        reloadPage = false,
        checkedSlugs = 0;

    $(document)
        .on('click', '[data-update-bookly-plugin]', function(e) {
            e.preventDefault();
            let slug = $(this).data('update-bookly-plugin'),
                $container = $(this).closest('.bookly-js-plugin-update-info');

            $(this).next('.spinner').addClass('is-active');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_update_plugin',
                    slug: slug,
                    csrf_token: BooklyPluginsPageL10n.csrfToken,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('span', $container).html(BooklyPluginsPageL10n.updated.replace('%s', response.data.title));
                        if (slug === 'bookly-addon-pro') {
                            processUpdatesForPro();
                        }
                        reloadPage = true;
                    }
                },
                error: function(XHR, exception) {

                },
            });
        })
        .on('wp-plugin-update-success', {},
            function(event, arg) {
                if (arg.slug === 'bookly-responsive-appointment-booking-tool' || arg.slug === 'bookly-addon-pro') {
                    checkedSlugs = 0;
                    if (arg.slug === 'bookly-addon-pro') {
                        processUpdatesForPro();
                    } else {
                        checkUpdate(arg.slug, [], function() {});
                    }
                }
            }
        )
        .on('click', '[id^="deactivate-bookly-"]', function(e) {
            if (BooklyPluginsPageL10n.deleteData == '1') {
                if (!confirm(BooklyPluginsPageL10n.deletingInfo)) {
                    e.preventDefault();
                }
            }
        });

    function processUpdatesForPro() {
        if (BooklyPluginsPageL10n.addons.length > 0) {
            $modal.show();
            $list.html(
                BooklyPluginsPageL10n.wait
                    .replace('{checked}', '<span class="bookly-js-plugins-checked">0</span>')
                    .replace('{total}', BooklyPluginsPageL10n.addons.length)
            );
        }
        let promises = [],
            i = 0;
        for (; i < BooklyPluginsPageL10n.addons.length;) {
            promises.push(new Promise(function(resolve, reject) {
                checkUpdate('bookly-addon-pro', BooklyPluginsPageL10n.addons.slice(i, i + 5), resolve)
            }));
            i += 5;
        }
        if (i < BooklyPluginsPageL10n.addons.length) {
            promises.push(new Promise(function(resolve, reject) {
                checkUpdate('bookly-addon-pro', BooklyPluginsPageL10n.addons.slice(i), resolve);
            }));
        }
        Promise.all(promises).then(function(results) {
            let exists = false;
            for (var key in results) {
                if (results[key].exist_updates) {
                    exists = true;
                }
            }
            if (!exists) {
                $list.append('<p>' + BooklyPluginsPageL10n.noUpdatesAvailable + '</p>');
                setTimeout(function() {
                    $modal.fadeOut()
                }, 3000);
            }
        });
    }

    function checkUpdate(slug, slugs, resolve) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_check_update',
                csrf_token: BooklyPluginsPageL10n.csrfToken,
                slug: slug,
                slugs: slugs
            },
            dataType: 'json',
            success: function(response) {
                if (slugs.length > 0) {
                    checkedSlugs += slugs.length;
                    $('.bookly-js-plugins-checked', $modal).html(checkedSlugs);
                }
                if (response.success) {
                    for (var key in response.data.update) {
                        let info = ''
                        if (response.data.update[key].icon) {
                            info = icon.replace('{src}', response.data.update[key].icon);
                        }
                        info += '<span>' + response.data.update[key].details + spinner;
                        if (response.data.update[key].support) {
                            info += '<br>' + response.data.update[key].support;
                        }
                        info += '</span>';
                        $list.append('<div class="bookly-js-plugin-update-info">' + info + '</div>');
                    }
                    $modal.show();
                    return resolve({exist_updates: response.data.update.length > 0});
                }
                return resolve({exist_updates: false});
            },
            error: function(XHR, exception) {

            },
        });
    }

    $list
        .on('click', '[data-bookly-plugin]', function(e) {
            e.preventDefault();
            let $spinner = $(this).next('span.spinner'),
                $container = $(this).closest('.bookly-js-plugin-update-info');
            $spinner.addClass('is-active');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_pro_re_check_support',
                    csrf_token: BooklyPluginsPageL10n.csrfToken,
                    plugin: $(this).data('bookly-plugin')
                },
                dataType: 'json',
                success: function(response) {
                    if (response.valid) {
                        $('.update-message', $container).fadeOut();
                    } else {
                        $spinner.removeClass('is-active');
                        alert(response.message);
                    }
                },
                error: function(XHR, exception) {
                    $spinner.removeClass('is-active');
                },
            });
        });


    window.onclick = function(event) {
        if (event.target == $modal[0]) {
            $modal.hide();
            if (reloadPage) {
                window.location.href = window.location.href;
            }
        }
    }

    $('.bookly-js-plugin').each(function() {
        let $plugin_tr = $(this).prev();
        $plugin_tr.addClass('update');

        $('[data-bookly-plugin]', $(this)).on('click', function(e) {
            e.preventDefault();
            let $spinner = $(this).siblings('.spinner');
            $spinner.addClass('is-active');
            let $update_link = $('a[href*="puc_check_for_updates=1&puc_slug=bookly-addon-"]', $plugin_tr),
                data = {
                    action: 'bookly_pro_re_check_support',
                    csrf_token: BooklyPluginsPageL10n.csrfToken,
                    plugin: $(this).data('bookly-plugin')
                };

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.valid) {
                        window.location.href = $update_link.attr('href');
                    } else {
                        $spinner.removeClass('is-active');
                        alert(response.message);
                    }
                },
                error: function(XHR, exception) {
                    $spinner.removeClass('is-active');
                },
            });
        })
    })
});
