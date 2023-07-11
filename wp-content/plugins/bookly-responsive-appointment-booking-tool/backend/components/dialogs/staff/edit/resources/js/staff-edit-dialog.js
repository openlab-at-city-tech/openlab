jQuery(function ($) {
    'use strict';

    let $staffList = $('#bookly-staff-list'),
        $modal = $('#bookly-staff-edit-modal'),
        $modalBody = $('.modal-body', $modal),
        $modalTitle = $('.modal-title', $modal),
        $modalFooter = $('.modal-footer ', $modal),
        $saveBtn = $('.bookly-js-save', $modalFooter),
        $archiveBtn = $('.bookly-js-staff-archive', $modalFooter),
        $validateErrors = $('.bookly-js-errors', $modalFooter),
        $deleteCascadeModal = $('.bookly-js-delete-cascade-confirm'),
        $staffCount = $('.bookly-js-staff-count'),
        currentTab = 'bookly-' + BooklyStaffEditDialogL10n.currentTab + '-tab',
        tabs = {
            daysOff: null,
            schedule: null,
            specialDays: null
        },
        staff_id,
        holidays
    ;

    $modal
    .on('keydown', ':input:not(textarea)', function (event) {
        if (event.key == 'Enter') {
            event.preventDefault();
        }
    })
    .on('show.bs.modal', function () {
        for (let tab in tabs) {
            tabs[tab] = null;
        }
    });

    $staffList
    .on('click', '[data-action="edit"]', function () {
        let data = $staffList.DataTable().row($(this).closest('td')).data();
        staff_id = data.id;
        editStaff(staff_id);
    });

    $('#bookly-js-new-staff')
    .on('click', function () {
        if (BooklyStaffEditDialogL10n.proRequired == '1') {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_get_staff_count',
                    csrf_token: BooklyL10nGlobal.csrf_token
                },
                dataType: 'json',
                success: function (response) {
                    if (response.data.count > 0) {
                        requiredBooklyPro();
                    } else {
                        staff_id = 0;
                        editStaff(staff_id);
                    }
                }
            })
        } else {
            staff_id = 0;
            editStaff(staff_id);
        }
    });

    /**
     * Edit staff member.
     */
    function editStaff(staff_id) {
        $modalTitle.html(staff_id ? BooklyStaffEditDialogL10n.editStaff : BooklyStaffEditDialogL10n.createStaff);
        $('#bookly-staff-delete', $modalFooter).toggle(staff_id != 0);

        $modalFooter.hide();
        $validateErrors.html('');
        $saveBtn.prop('disabled', false);
        $modalBody.html('<div class="bookly-loading"></div>');
        $modal.booklyModal();
        $.get(ajaxurl, {action: 'bookly_get_staff_data', id: staff_id, csrf_token: BooklyL10nGlobal.csrf_token}, function (response) {
            $modalBody.html(response.data.html.edit);
            booklyAlert(response.data.alert);
            $modalFooter.show();
            holidays = response.data.holidays;
            let $details_container = $('#bookly-details-container', $modalBody),
                $advanced_container = $('#bookly-advanced-container', $modalBody),
                $services_container = $('#bookly-services-container', $modalBody),
                $schedule_container = $('#bookly-schedule-container', $modalBody),
                $holidays_container = $('#bookly-holidays-container', $modalBody),
                $special_days_container = $('#bookly-special-days-container', $modalBody)
            ;
            $details_container.append(response.data.html.details);
            $advanced_container.append(response.data.html.advanced);
            $services_container.append(response.data.html.services);
            $schedule_container.append(response.data.html.schedule);
            $holidays_container.append(response.data.html.holidays);
            $special_days_container.append(response.data.html.special_days);

            $('.bookly-js-modal-footer', $modalBody).hide();

            new BooklyStaffDetails($details_container, {
                intlTelInput: BooklyStaffEditDialogL10n.intlTelInput,
                l10n: BooklyStaffEditDialogL10n
            });

            if (staff_id) {
                $('.bookly-js-staff-tabs').booklyNavScrollable();
            }

            $archiveBtn.toggle(staff_id ? response.data.staff.visibility !== 'archive' : false);
            if (currentTab) {
                $('#' + currentTab, $modalBody).click();
            }
        });
    }

    /**
     * Delete staff member.
     */
    function deleteStaff(_data, ladda) {
        ladda.start();
        $deleteCascadeModal.booklyModal('show');
        // Delete
        $('.bookly-js-delete', $deleteCascadeModal).off().on('click', function () {
            ladda = Ladda.create(this);
            ladda.start();
            let data = {
                action: 'bookly_remove_staff',
                'staff_ids[]': staff_id,
                csrf_token: BooklyL10nGlobal.csrf_token
            };
            $.post(ajaxurl, data, function () {
                $staffList.DataTable().ajax.reload();
                $deleteCascadeModal.booklyModal('hide');
                $modal.booklyModal('hide');
                ladda.stop();
            });
        });
        // Edit
        $('.bookly-js-edit', $deleteCascadeModal).off().on('click', function () {
            ladda = Ladda.create(this);
            ladda.start();
            window.location.href = BooklyStaffEditDialogL10n.appointmentsUrl + '#staff=' + staff_id;
        });
    };

    $modalFooter
    .on('click', '#bookly-staff-delete', function (e) {
        e.preventDefault();
        deleteStaff({}, Ladda.create(this));
    });

    $modalBody
    // Delete staff avatar
    .on('click', '.bookly-thumb-delete', function () {
        var $thumb = $(this).parents('.bookly-js-image');
        $thumb.attr('style', '');
        $modalBody.find('[name=attachment_id]').val('').trigger('change');
    })

    // Open details tab
    .on('click', '#bookly-details-tab', function () {
        $('.tab-pane > div').hide();
        $('#bookly-details-container', $modalBody).show();
    })

    // Open services tab
    .on('click', '#bookly-services-tab', function () {
        $('.tab-pane > div').hide();
        let $container = $('#bookly-services-container', $modalBody);
        new BooklyStaffServices($container, {
            get_staff_services: {
                action: 'bookly_get_staff_services',
                staff_id: staff_id,
            },
            onLoad: function () {
                $('.bookly-js-modal-footer', $container).hide();
                $('#bookly-services-save', $container).addClass('bookly-js-save');
                $(document.body).trigger('staff.validation', ['staff-services', false, '']);
            },
            l10n: BooklyStaffEditDialogL10n.services,
        });
        $('#bookly-services-save', $container).addClass('bookly-js-save');
        $container.show();
    })

    // Open special days tab
    .on('click', '#bookly-special-days-tab', function () {
        $('.tab-pane > div').hide();
        let $container = $('#bookly-special-days-container', $modalBody);
        if (tabs.specialDays !== 'initialized') {
            tabs.specialDays = 'initialized';
            new BooklyStaffSpecialDays($container, {
                staff_id: staff_id,
                l10n: SpecialDaysL10n,
                onLoad: function () {
                    $('.bookly-js-modal-footer', $container).hide();
                    $('#bookly-js-special-days-save-days', $container).addClass('bookly-js-save');
                }
            });
            $('#bookly-js-special-days-save-days', $container).addClass('bookly-js-save');
        }

        $container.show();
    })

    // Open schedule tab
    .on('click', '#bookly-schedule-tab', function () {
        $('.tab-pane > div').hide();
        let $container = $('#bookly-schedule-container', $modalBody);

        if (tabs.schedule !== 'initialized') {
            tabs.schedule = 'initialized'
            new BooklyStaffSchedule($container, {
                get_staff_schedule: {
                    action: 'bookly_get_staff_schedule',
                    staff_id: staff_id
                },
                onLoad: function () {
                    $('.bookly-js-modal-footer', $container).hide();
                    $('#bookly-schedule-save', $container).addClass('bookly-js-save');
                },
                l10n: BooklyL10n
            });
            $('#bookly-schedule-save', $modalBody).addClass('bookly-js-save');
        }
        $container.show();
    })

    // Open holiday tab
    .on('click', '#bookly-holidays-tab', function () {
        $('.tab-pane > div').hide();
        let $container = $('#bookly-holidays-container', $modalBody);
        if (tabs.daysOff !== 'initialized') {
            tabs.daysOff = 'initialized';
            new BooklyStaffDaysOff($container, {
                staff_id: staff_id,
                l10n: jQuery.extend(BooklyStaffEditDialogL10n.holidays, {holidays: holidays})
            });
        }
        $container.show();
    })
    .on('click', '> .nav-tabs [data-toggle=bookly-tab]', function () {
        currentTab = $(this).attr('id');
    });

    let waitResposes = 0,
        ladda,
        success;

    $saveBtn
    .on('click', function (e) {
        e.preventDefault();
        ladda = Ladda.create(this);
        ladda.start();

        let $buttons = $('.bookly-js-modal-footer', $modalBody);
        waitResposes = 0;
        success = true;
        $buttons
        .each(function () {
            let $button = $('.bookly-js-save', this);
            if ($button.length > 0) {
                waitResposes++;
                $button.trigger('click');
            }
        });
    });

    $(document.body)
    .on('staff.saving', {},
        function (event, result) {
            if (waitResposes > 0) {
                if (result.hasOwnProperty('error')) {
                    success = false;
                }
                waitResposes--;
            }
            if (waitResposes <= 0) {
                $staffList.DataTable().ajax.reload(function () {
                    $staffList.DataTable().responsive.recalc();
                });
                ladda ? ladda.stop() : null;
                $modal.booklyModal('hide');
                booklyAlert({success: [BooklyStaffEditDialogL10n.settingsSaved]})
            }
        })
    .on('staff.validation', {},
        function (event, tab, has_error, info) {
            let id = 'tab-' + tab + '-validation',
                $container = $validateErrors.find('#' + id);
            if (has_error) {
                ladda ? ladda.stop() : null;
                if ($container.length === 0) {
                    $validateErrors.append($('<div/>').attr('id', id).html(Array.isArray(info) ? info.join('<br>') : info));
                }
            } else {
                $container.remove();
            }

            $saveBtn.prop('disabled', $('>', $validateErrors).length !== 0);
        });

    $(document.body).on('bookly.staff.edit', {},
        function (event, staff_id) {
            editStaff(staff_id)
        }
    );
});