jQuery(function ($) {
    window.BooklyNotificationDialog = function () {
        let $notificationList = $('#bookly-js-notification-list'),
            $btnNewNotification = $('#bookly-js-new-notification'),
            $modalNotification = $('#bookly-js-notification-modal'),
            containers = {
                settings: $('#bookly-js-settings-container', $modalNotification),
                statuses: $('.bookly-js-statuses-container', $modalNotification),
                services: $('.bookly-js-services-container', $modalNotification),
                recipient: $('.bookly-js-recipient-container', $modalNotification),
                message: $('#bookly-js-message-container', $modalNotification),
                attach: $('.bookly-js-attach-container', $modalNotification),
                codes: $('.bookly-js-codes-container', $modalNotification)
            },
            $offsets = $('.bookly-js-offset', containers.settings),
            $notificationType = $('select[name=\'notification[type]\']', containers.settings),
            $labelSend = $('.bookly-js-offset-exists', containers.settings),
            $offsetBidirectional = $('.bookly-js-offset-bidirectional', containers.settings),
            $offsetBefore = $('.bookly-js-offset-before', containers.settings),
            $btnSaveNotification = $('.bookly-js-save', $modalNotification),
            $helpType = $('.bookly-js-help-block', containers.settings),
            $codes = $('table.bookly-js-codes', $modalNotification),
            $status = $("select[name='notification[settings][status]']", containers.settings),
            $defaultStatuses,
            useTinyMCE = BooklyNotificationDialogL10n.gateway == 'email' && typeof (tinyMCE) !== 'undefined',
            notification = {
                $body: $('#bookly-js-notification-body', containers.message),
                $subject: $("#bookly-js-notification-subject", containers.message)
            },
            useAceEditor = ['email', 'voice', 'sms'].includes(BooklyNotificationDialogL10n.gateway),
            aceEditor = useAceEditor ? $('#bookly-ace-editor').booklyAceEditor() : null,
            whatsAppSettings = {}
        ;

        function setNotificationText(text) {
            if (BooklyNotificationDialogL10n.gateway !== 'whatsapp') {
                notification.$body.val(text);
            }
            if (useTinyMCE) {
                tinyMCE.activeEditor.setContent(text);
            }
            useAceEditor && aceEditor.booklyAceEditor('setValue', text);
        }

        function format(option) {
            return option.id && option.element.dataset.icon ? '<i class="fa-fw ' + option.element.dataset.icon + '"></i> ' + option.text : option.text;
        }

        $modalNotification
        .on('show.bs.modal.first', function () {
            $notificationType.trigger('change');
            $modalNotification.unbind('show.bs.modal.first');
            if (useTinyMCE) {
                tinymce.init(tinyMCEPreInit);
            }
            containers.message.siblings('a[data-toggle=bookly-collapse]').html(BooklyNotificationDialogL10n.title.container);
            $('.bookly-js-services', containers.settings).booklyDropdown();
            $('.modal-title', $modalNotification).html(BooklyNotificationDialogL10n.title.edit);
        });

        if (useTinyMCE) {
            $('a[data-toggle="bookly-tab"]').on('shown.bs.tab', function (e) {
                if ($(e.target).data('ace') !== undefined) {
                    tinyMCE.triggerSave();
                    aceEditor.booklyAceEditor('setValue', $('[name=notification\\[message\\]]').val());
                    aceEditor.booklyAceEditor('focus');
                } else {
                    tinyMCE.activeEditor.setContent(wpautop(aceEditor.booklyAceEditor('getValue')));
                    tinyMCE.activeEditor.focus();
                }
            });
        }

        /**
         * Notification
         */
        $notificationType
        .on('change', function () {
            if ($(':selected', $notificationType).length == 0) {
                // Un supported notification type (without Pro)
                $notificationType.val('new_booking');
            }
            var $modalBody        = $(this).closest('.modal-body'),
                $attach           = $modalBody.find('.bookly-js-attach'),
                $selected         = $(':selected', $notificationType),
                set               = $selected.data('set').split(' '),
                recipients        = $selected.data('recipients'),
                showAttach        = $selected.data('attach') || [],
                hideServices      = true,
                hideStatuses      = true,
                notification_type = $selected.val()
            ;

            $helpType.hide();
            $offsets.hide();

            switch (notification_type) {
                case 'appointment_reminder':
                case 'ca_status_changed':
                case 'ca_status_changed_recurring':
                    hideStatuses = false;
                    hideServices = false;
                    break;
                case 'customer_birthday':
                case 'customer_new_wp_user':
                case 'last_appointment':
                    break;
                case 'new_booking':
                case 'new_booking_recurring':
                    hideStatuses = false;
                    hideServices = false;
                    break;
                case 'new_booking_combined':
                    $helpType.filter('.' + notification_type).show();
                    break;
                case 'new_package':
                case 'package_deleted':
                    break;
                case 'staff_day_agenda':
                    $("input[name='notification[settings][option]'][value=3]", containers.settings).prop('checked', true);
                    break;
                case 'staff_waiting_list':
                    break;
            }

            containers.statuses.toggle(!hideStatuses);
            containers.services.toggle(!hideServices);

            switch (set[0]) {
                case 'bidirectional':
                    $labelSend.show();
                    $('.bookly-js-offsets', $offsetBidirectional).each(function () {
                        $(this).toggle($(this).hasClass('bookly-js-' + set[1]));
                    });
                    if (set[1] !== 'full') {
                        $('.bookly-js-' + set[1] + ' input:radio', $offsetBidirectional).prop('checked', true);
                    }
                    $offsetBidirectional.show();
                    break;
                case 'before':
                    $offsetBefore.show();
                    $labelSend.show();
                    break;
            }

            // Hide/un hide recipient
            $.each(['customer', 'staff', 'admin', 'custom'], function (index, value) {
                $("[name$='[to_" + value + "]']:checkbox", containers.recipient).closest('.custom-control').toggle(recipients.indexOf(value) != -1);
            });

            // Hide/un hide attach
            $attach.hide();
            $.each(showAttach, function (index, value) {
                $('.bookly-js-' + value, containers.attach).show();
            });
            $codes.hide();
            $codes.filter('.bookly-js-codes-' + notification_type).show();
            useAceEditor && aceEditor.booklyAceEditor('setCodes', BooklyNotificationDialogL10n.codes[notification_type]);
        })
            .booklySelect2({
                minimumResultsForSearch: -1,
                width: '100%',
                theme: 'bootstrap4',
                dropdownParent: '#bookly-tbs',
                allowClear: false,
                templateResult: format,
                templateSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
        });

        $('.bookly-js-services', $modalNotification).booklyDropdown({});

        $btnNewNotification.off()
        .on('click', function () {
            showNotificationDialog();
        });

        $btnSaveNotification.off()
        .on('click', function () {
            if (useTinyMCE && $('a[data-toggle="bookly-tab"][data-tinymce].active').length) {
                tinyMCE.triggerSave();
            } else if (useAceEditor) {
                $('[name=notification\\[message\\]]').val(aceEditor.booklyAceEditor('getValue'));
            }
            var data = booklySerialize.form($modalNotification),
                ladda = Ladda.create(this);
            ladda.start();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: booklySerialize.buildRequestData('bookly_save_notification', data),
                dataType: 'json',
                success: function (response) {
                    ladda.stop();
                    if (response.success) {
                        $notificationList.DataTable().ajax.reload();
                        $modalNotification.booklyModal('hide');
                    }
                }
            });
        });

        $notificationList
        .on('click', '[data-action=edit]', function () {
            var row  = $notificationList.DataTable().row($(this).closest('td')),
                data = row.data();
            showNotificationDialog(data.id);
        });

        function showNotificationDialog(id) {
            $('.bookly-js-loading:first-child', $modalNotification).addClass('bookly-loading').removeClass('bookly-collapse');
            $('.bookly-js-loading:last-child', $modalNotification).addClass('bookly-collapse');

            if (BooklyNotificationDialogL10n.gateway === 'whatsapp'
                && !BooklyNotificationDialogL10n.hasOwnProperty('templates')
            ) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: booklySerialize.buildRequestData('bookly_get_whatsapp_templates'),
                    dataType: 'json',
                    async: false,
                    success: function(response) {
                        if (response.success) {
                            BooklyNotificationDialogL10n.templates = response.data.list;
                        } else {
                            BooklyNotificationDialogL10n.templates = [];
                            booklyAlert({error: [response.data.message]});
                        }
                        renderTemplatesList();
                    }
                });
            }

            if (id === undefined) {
                setNotificationData(BooklyNotificationDialogL10n.defaultNotification);
                $modalNotification.booklyModal('show');
            } else {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_get_notification_data',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                        id: id
                    },
                    dataType: 'json',
                    success: function (response) {
                        setNotificationData(response.data);
                        $modalNotification.booklyModal('show');
                    }
                });
            }
        }

        function setNotificationData(data) {
            if (BooklyNotificationDialogL10n.gateway === 'whatsapp') {
                if (data.settings.hasOwnProperty('whatsapp')) {
                    whatsAppSettings = data.settings.whatsapp;
                }
                $("select[name='notification[message]']", containers.message)
                    .val(data.message)
                    .trigger('change');
            }

            // Notification settings
            $("input[name='notification[id]']", containers.settings).val(data.id);
            $("input[name='notification[name]']", containers.settings).val(data.name);
            $("input[name='notification[active]'][value=" + data.active + "]", containers.settings).prop('checked', true);
            if ($defaultStatuses) {
                $status.html($defaultStatuses);
            } else {
                $defaultStatuses = $status.html();
            }
            if (data.settings.status !== null) {
                if ($status.find('option[value="' + data.settings.status + '"]').length > 0) {
                    $status.val(data.settings.status);
                } else {
                    var custom_status = data.settings.status.charAt(0).toUpperCase() + data.settings.status.slice(1);

                    $status.append($("<option></option>", {value: data.settings.status, text: custom_status.replace(/\-/g, ' ')})).val(data.settings.status);
                }
            }

            $("input[name='notification[settings][services][any]'][value='" + data.settings.services.any + "']", containers.settings).prop('checked', true);
            $('.bookly-js-services', containers.settings).booklyDropdown('setSelected', data.settings.services.ids);

            $("input[name='notification[settings][option]'][value=" + data.settings.option + "]", containers.settings).prop('checked', true);
            $("select[name='notification[settings][offset_hours]']", containers.settings).val(data.settings.offset_hours);
            $("select[name='notification[settings][perform]']", containers.settings).val(data.settings.perform);
            $("select[name='notification[settings][at_hour]']", containers.settings).val(data.settings.at_hour);
            $("select[name='notification[settings][offset_bidirectional_hours]']", containers.settings).val(data.settings.offset_bidirectional_hours);
            $("select[name='notification[settings][offset_before_hours]']", containers.settings).val(data.settings.offset_before_hours);
            $("select[name='notification[settings][before_at_hour]']", containers.settings).val(data.settings.before_at_hour);

            // Recipients
            $("input[name='notification[to_staff]']", containers.settings).prop('checked', data.to_staff == '1');
            $("input[name='notification[to_customer]']", containers.settings).prop('checked', data.to_customer == '1');
            $("input[name='notification[to_admin]']", containers.settings).prop('checked', data.to_admin == '1');
            $("input[name='notification[to_custom]']", containers.settings).prop('checked', data.to_custom == '1');
            $("input[name='notification[to_custom]']", containers.settings)
            .on('change', function () {
                $('.bookly-js-custom-recipients', containers.settings).toggle(this.checked)
            }).trigger('change');
            $("[name='notification[custom_recipients]']", containers.settings).val(data.custom_recipients);

            // Message
            $("input[name='notification[subject]']", containers.message).val(data.subject);
            $("input[name='notification[attach_ics]']", containers.message).prop('checked', data.attach_ics == '1');
            $("input[name='notification[attach_invoice]']", containers.message).prop('checked', data.attach_invoice == '1');

            setNotificationText(data.message);

            if (data.hasOwnProperty('id')) {
                $('.modal-title', $modalNotification).html(BooklyNotificationDialogL10n.title.edit);
                containers.settings.booklyCollapse('hide');
                containers.message.booklyCollapse('show');
                $('.bookly-js-save > span.ladda-label', $modalNotification).text(BooklyNotificationDialogL10n.title.save);
            } else {
                $('.modal-title', $modalNotification).html(BooklyNotificationDialogL10n.title.new);
                containers.settings.booklyCollapse('show');
                $('.bookly-js-save > span.ladda-label', $modalNotification).text(BooklyNotificationDialogL10n.title.create);
            }

            $notificationType.val(data.type).trigger('change');

            $('.bookly-js-loading', $modalNotification).toggleClass('bookly-collapse');

            $('a[href="#bookly-wp-editor-pane"]').click();
        }

        if (BooklyNotificationDialogL10n.gateway === 'whatsapp') {
            let $whatsapTemplates = $('#bookly-js-templates', containers.message);
            containers['variables'] = {
                header: $('#bookly-js-notification-subject-variables',containers.message),
                body: $('#bookly-js-notification-body-variables',containers.message),
            }

            function renderTemplatesList() {
                $whatsapTemplates[0].appendChild(new Option());
                for (var key in BooklyNotificationDialogL10n.templates) {
                    let tpl = BooklyNotificationDialogL10n.templates[key],
                        status = BooklyNotificationDialogL10n.statuses.hasOwnProperty(tpl.status) ? BooklyNotificationDialogL10n.statuses[tpl.status] : (tpl.status.charAt(0) + tpl.status.substring(1).toLowerCase().replaceAll('_', ' '));

                    $whatsapTemplates[0].appendChild(new Option(tpl.name + ' (' + tpl.language + ') - ' + status, key));
                }
            }

            /**
             * @param str
             * @returns {string[]}
             */
            function extractVariables(str) {
                const regex = /{{\d+}}/gm;
                let m, variables = [];
                while ((m = regex.exec(str)) !== null) {
                    m.forEach(function(match) {
                        if (variables.indexOf(match) === -1) {
                            variables.push(match);
                        }
                    });
                }
                try {
                    let collator = new Intl.Collator(undefined, {numeric: true, sensitivity: 'base'});
                    variables.sort(collator.compare);
                } catch (e) {}

                return variables;
            }

            function renderVariables(target, text) {
                let $list = $('.bookly-js-variables-list', containers.variables[target]),
                    variables = extractVariables(text);
                containers.variables[target].toggle(variables.length > 0);
                $list.html('');
                variables.forEach(function(key, position) {
                    let $input = $('<input>', {class: 'form-control', type: 'text', name: 'notification[settings][whatsapp][' + target + '][]'});
                    if (whatsAppSettings.hasOwnProperty(target)) {
                        $input.val(whatsAppSettings[target][position]);
                    }
                    $list.append( $('<div>', {class: 'row'})
                        .append($('<div>', {class: 'col'})
                            .append($('<div>', {class: 'input-group mb-1'})
                                .append($('<div>', {class: 'input-group-prepend'})
                                    .append($('<span>', {class: 'input-group-text', text: key}))
                                )
                                .append($input)
                            )
                        )
                    )
                })
            }

            $whatsapTemplates
                .on('change', function() {
                    let exists = BooklyNotificationDialogL10n.templates.hasOwnProperty(this.value),
                        tpl;
                    if (exists) {
                        tpl = BooklyNotificationDialogL10n.templates[this.value];
                    }
                    notification.$body.val(exists ? tpl.body.text : '');
                    notification.$subject.val(exists && tpl.header ? tpl.header.text : '');
                    renderVariables('header', exists && tpl.header ? tpl.header.text : '');
                    renderVariables('body', exists ? tpl.body.text : '');
                    $('input[name=\'notification[settings][whatsapp][template]\']', containers.message).val(exists ? tpl.name : '');
                    $('input[name=\'notification[settings][whatsapp][language]\']', containers.message).val(exists ? tpl.language : '');
                });
        }

        $(document)
        // Required because Bootstrap blocks all focusin calls from elements outside the dialog
        .on('focusin', function (e) {
            if ($(e.target).closest(".ui-autocomplete-input").length) {
                e.stopImmediatePropagation();
            }
            if ($(e.target).closest("#link-selector").length) {
                e.stopImmediatePropagation();
            }
        });

        // source: https://github.com/andymantell/node-wpautop
        function _autop_newline_preservation_helper(matches) {
            return matches[0].replace("\n", "<WPPreserveNewline />");
        }

        function wpautop(pee, br) {
            if (typeof (br) === 'undefined') {
                br = true;
            }

            var pre_tags = {};
            if (pee.trim() === '') {
                return '';
            }

            pee = pee + "\n"; // just to make things a little easier, pad the end
            if (pee.indexOf('<pre') > -1) {
                var pee_parts = pee.split('</pre>');
                var last_pee = pee_parts.pop();
                pee = '';
                pee_parts.forEach(function (pee_part, index) {
                    var start = pee_part.indexOf('<pre');

                    // Malformed html?
                    if (start === -1) {
                        pee += pee_part;
                        return;
                    }

                    var name = "<pre wp-pre-tag-" + index + "></pre>";
                    pre_tags[name] = pee_part.substr(start) + '</pre>';
                    pee += pee_part.substr(0, start) + name;

                });

                pee += last_pee;
            }

            pee = pee.replace(/<br \/>\s*<br \/>/, "\n\n");

            // Space things out a little
            var allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
            pee = pee.replace(new RegExp('(<' + allblocks + '[^>]*>)', 'gmi'), "\n$1");
            pee = pee.replace(new RegExp('(</' + allblocks + '>)', 'gmi'), "$1\n\n");
            pee = pee.replace(/\r\n|\r/, "\n"); // cross-platform newlines

            if (pee.indexOf('<option') > -1) {
                // no P/BR around option
                pee = pee.replace(/\s*<option'/gmi, '<option');
                pee = pee.replace(/<\/option>\s*/gmi, '</option>');
            }

            if (pee.indexOf('</object>') > -1) {
                // no P/BR around param and embed
                pee = pee.replace(/(<object[^>]*>)\s*/gmi, '$1');
                pee = pee.replace(/\s*<\/object>/gmi, '</object>');
                pee = pee.replace(/\s*(<\/?(?:param|embed)[^>]*>)\s*/gmi, '$1');
            }

            if (pee.indexOf('<source') > -1 || pee.indexOf('<track') > -1) {
                // no P/BR around source and track
                pee = pee.replace(/([<\[](?:audio|video)[^>\]]*[>\]])\s*/gmi, '$1');
                pee = pee.replace(/\s*([<\[]\/(?:audio|video)[>\]])/gmi, '$1');
                pee = pee.replace(/\s*(<(?:source|track)[^>]*>)\s*/gmi, '$1');
            }

            pee = pee.replace(/\n\n+/gmi, "\n\n"); // take care of duplicates

            // make paragraphs, including one at the end
            var pees = pee.split(/\n\s*\n/);
            pee = '';
            pees.forEach(function (tinkle) {
                pee += '<p>' + tinkle.replace(/^\s+|\s+$/g, '') + "</p>\n";
            });

            pee = pee.replace(/<p>\s*<\/p>/gmi, ''); // under certain strange conditions it could create a P of entirely whitespace
            pee = pee.replace(/<p>([^<]+)<\/(div|address|form)>/gmi, "<p>$1</p></$2>");
            pee = pee.replace(new RegExp('<p>\s*(</?' + allblocks + '[^>]*>)\s*</p>', 'gmi'), "$1", pee); // don't pee all over a tag
            pee = pee.replace(/<p>(<li.+?)<\/p>/gmi, "$1"); // problem with nested lists
            pee = pee.replace(/<p><blockquote([^>]*)>/gmi, "<blockquote$1><p>");
            pee = pee.replace(/<\/blockquote><\/p>/gmi, '</p></blockquote>');
            pee = pee.replace(new RegExp('<p>\s*(</?' + allblocks + '[^>]*>)', 'gmi'), "$1");
            pee = pee.replace(new RegExp('(</?' + allblocks + '[^>]*>)\s*</p>', 'gmi'), "$1");

            if (br) {
                pee = pee.replace(/<(script|style)(?:.|\n)*?<\/\\1>/gmi, _autop_newline_preservation_helper); // /s modifier from php PCRE regexp replaced with (?:.|\n)
                pee = pee.replace(/(<br \/>)?\s*\n/gmi, "<br />\n"); // optionally make line breaks
                pee = pee.replace('<WPPreserveNewline />', "\n");
            }

            pee = pee.replace(new RegExp('(</?' + allblocks + '[^>]*>)\s*<br />', 'gmi'), "$1");
            pee = pee.replace(/<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)/gmi, '$1');
            pee = pee.replace(/\n<\/p>$/gmi, '</p>');

            if (Object.keys(pre_tags).length) {
                pee = pee.replace(new RegExp(Object.keys(pre_tags).join('|'), "gi"), function (matched) {
                    return pre_tags[matched];
                });
            }

            return pee;
        }
    }
});