jQuery(function($) {
    'use strict';
    let $servicesList = $('#services-list'),
        $serviceDialog = $('#bookly-edit-service-modal'),
        $containers = $('.bookly-js-service-containers .tab-pane > div'),
        $serviceLoading = $('.bookly-js-service-containers > .bookly-js-loading', $serviceDialog),
        $serviceTabs = $('.bookly-js-service-tabs', $serviceDialog),
        $wrapContainer = $('.bookly-js-service-containers', $serviceDialog),
        containers = {
            general: $('#bookly-services-general-container', $serviceDialog),
            advanced: $('#bookly-services-advanced-container', $serviceDialog),
            time: $('#bookly-services-time-container', $serviceDialog),
            extras: $('#bookly-services-extras-container', $serviceDialog),
            discounts: $('#bookly-discounts-container', $serviceDialog),
            special_days: $('#bookly-services-special-days-container', $serviceDialog),
            schedule: $('#bookly-services-schedule-container', $serviceDialog),
            wc: $('#bookly-services-wc-container', $serviceDialog),
            additional: $('#bookly-service-additional-html', $serviceDialog),
        },
        $saveButton = $('#bookly-save', $serviceDialog),
        $updateStaffModal = $('#bookly-update-service-settings'),
        $serviceType = $('[name="type"]', $serviceDialog),
        $serviceId = $('[name="id"]', $serviceDialog),
        updateStaffChoice = null
    ;

    $serviceDialog
        .on('keydown', ':input:not(textarea)', function(event) {
            if (event.key == "Enter") {
                event.preventDefault();
            }
        })
        .on('show.bs.modal', function() {
            $('.bookly-js-service-error', $serviceDialog).html('');
            $saveButton.prop('disabled', false);
        });

    function editService(service_id) {
        $containers.html('');
        $serviceTabs.hide();
        $serviceLoading.show();
        $serviceDialog.booklyModal('show');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_get_service_data',
                csrf_token: BooklyL10nGlobal.csrf_token,
                id: service_id
            },
            dataType: 'json',
            success: function(response) {
                Object.keys(response.data.html).forEach(function(tab) {
                    containers[tab].html(response.data.html[tab]);
                });

                $serviceId.val(service_id);
                $serviceType.val(response.data.type);

                /**
                 * Init general tab
                 */
                let $colorPicker = $('.bookly-js-color-picker', containers.general),
                    $visibility = $('input[name="visibility"]', containers.general),
                    $providers = $('.bookly-js-providers', containers.general),
                    $staffPreference = $('[name=staff_preference]', containers.general),
                    $prefStaffOrder = $('.bookly-js-preferred-staff-order', containers.general),
                    $prefStaffList = $('.bookly-js-preferred-staff-list', containers.general),
                    $prefPeriod = $('.bookly-js-preferred-period', containers.general),
                    $prefRandom = $('.bookly-js-preferred-random-staff', containers.general),
                    $gatewaysList = $('#bookly-js-gateways-list', containers.general),
                    staff_data = {}
                ;
                // Color picker.
                $colorPicker.wpColorPicker();
                $gatewaysList.booklyDropdown();

                if ($serviceType.val() === 'package') {
                    $('#package_service').on('change', function() {
                        $saveButton.prop('disabled', $(this).val() === '0');
                        $(this).next('.alert').toggleClass('bookly-show', $(this).val() === '0');
                    }).trigger('change');
                }

                // Service image.
                containers.general.off()
                    .on('click', '.bookly-thumb label', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var frame = wp.media({
                            library: {type: 'image'},
                            multiple: false
                        });
                        frame
                            .on('select', function() {
                                var selection = frame.state().get('selection').toJSON(),
                                    img_src;
                                if (selection.length) {
                                    if (selection[0].sizes['thumbnail'] !== undefined) {
                                        img_src = selection[0].sizes['thumbnail'].url;
                                    } else {
                                        img_src = selection[0].url;
                                    }
                                    containers.general.find('[name=attachment_id]').val(selection[0].id).trigger('change');
                                    $('#bookly-js-service-image').find('.bookly-js-image').css({'background-image': 'url(' + img_src + ')', 'background-size': 'cover'});
                                    $('.bookly-thumb-delete').show();
                                    $('.bookly-thumb').addClass('bookly-thumb-with-image');
                                    $(this).hide();
                                }
                            });

                        frame.open();
                        $(document).off('focusin.modal');
                    })
                    // Delete service image
                    .on('click', '.bookly-thumb-delete', function() {
                        var $thumb = $(this).parents('.bookly-js-image');
                        $thumb.attr('style', '');
                        containers.general.find('[name=attachment_id]').val('').trigger('change');
                        $('.bookly-thumb').removeClass('bookly-thumb-with-image');
                        $('.bookly-thumb-delete').hide();
                    })
                    .on('change', '[name="gateways"]', function () {
                        if (this.value == 'default') {
                            $gatewaysList.closest('.form-group').hide();
                        } else {
                            $gatewaysList.closest('.form-group').show();
                        }
                    });

                if ($('[name="gateways"][value="default"]', containers.general).prop('checked')) {
                    $gatewaysList.closest('.form-group').hide();
                }

                // Visibility.
                $visibility.off().on('change', function() {
                    $('.bookly-js-groups-list', containers.general).toggle($('input[name="visibility"]:checked', containers.general).val() === 'group');
                });
                // Providers.
                $providers.booklyDropdown();
                // Providers preference.
                $.each(response.data.staff, function(index, category) {
                    $.each(category.items, function(index, staff) {
                        staff_data[staff.id] = encodeHTML(staff.full_name);
                    });
                });
                $staffPreference.on('change', function() {
                    if (this.value === 'order' && $prefStaffList.html() === '') {
                        let staffIds = $staffPreference.data('default'),
                            $draggable = $('<div class="col-12">').append('<i class="fas fa-fw fa-bars text-muted bookly-cursor-move bookly-js-draghandle"/>').append('<input type="hidden" name="positions[]"/>');
                        $draggable.find('i').attr('title', BooklyL10n.reorder);
                        staffIds.forEach(function(staffId) {
                            $prefStaffList.append($draggable.clone().find('input').val(staffId).end().append('&nbsp;' + staff_data[staffId]));
                        });
                        Object.keys(BooklyServiceEditDialogL10n.staff).forEach(function(staffId) {
                            if (staffIds.indexOf(staffId) === -1) {
                                $prefStaffList.append($draggable.clone().find('input').val(staffId).end().append('&nbsp;' + staff_data[staffId]));
                            }
                        });
                    }
                    $prefStaffOrder.toggle(this.value === 'order');
                    $prefPeriod.toggle(this.value === 'least_occupied_for_period' || this.value === 'most_occupied_for_period');
                    $prefRandom.toggle(this.value !== 'order');
                }).trigger('change');
                // Preferred providers order.
                if ($prefStaffList.length) {
                    Sortable.create($prefStaffList[0], {
                        handle: '.bookly-js-draghandle',
                        onEnd: function() {
                            let data = {
                                service_id: service_id,
                                staff: []
                            }
                            $prefStaffList.find('input').each(function() {
                                data.staff.push(this.value);
                            });
                            $.ajax({
                                type: 'POST',
                                url: ajaxurl,
                                data: booklySerialize.buildRequestData('bookly_pro_update_service_staff_preference_orders', data)
                            });
                        }
                    });
                }

                /**
                 * Init time tab
                 */
                let $duration = $('.bookly-js-duration', containers.time),
                    $unitsBlock = $('.bookly-js-units-block', containers.time),
                    $unitDuration = $('.bookly-js-unit-duration', containers.time)
                ;
                // Duration (and unit duration).
                $duration.off().on('change', function() {
                    if (this.value === 'custom') {
                        $serviceDialog.find('.bookly-js-price-label').hide();
                        $serviceDialog.find('.bookly-js-unit-price-label').show();
                        $unitsBlock.css('display', 'flex');
                    } else {
                        $serviceDialog.find('.bookly-js-price-label').show();
                        $serviceDialog.find('.bookly-js-unit-price-label').hide();
                        $unitDuration.val(this.value);
                        $unitsBlock.hide();
                    }
                    $unitDuration.trigger('change');
                }).trigger('change');
                $duration.add($unitDuration);
                $unitDuration.on('change', function() {
                    $('.bookly-js-start-time-info', $serviceDialog).toggle(this.value >= 86400);
                    if (this.value < 86400) {
                        $('.bookly-js-service-slot-length', $serviceDialog).css('display', 'flex');
                    } else {
                        $('.bookly-js-service-slot-length', $serviceDialog).hide();
                    }
                });

                /**
                 * Init other settings.
                 */
                $('.bookly-js-simple-dropdown', $serviceDialog).booklyDropdown();

                // Fields that are repeated at staff level.
                $serviceDialog.find('.bookly-js-question').each(function() {
                    $(this).data('last_value', this.value);
                });

                $serviceDialog.find('.bookly-js-service').hide();
                $serviceDialog.find('.bookly-js-service-' + response.data.type).css('display', '');

                // Switch to 'General' tab if active is not visible
                if ($('.bookly-js-service-tabs a.active').closest('li').css('display') == 'none') {
                    $('#bookly-services-general-tab').click();
                }

                $(document.body).trigger('service.initForm', [$wrapContainer, service_id, response.data]);

                $serviceTabs.show();
                $serviceLoading.hide();

                /**
                 * Save service
                 */
                $saveButton.off().on('click', function(e) {
                    e.preventDefault();
                    var showModal = false;
                    if (updateStaffChoice === null) {
                        $serviceDialog.find('.bookly-js-question').each(function() {
                            if ($(this).data('last_value') !== this.value && ($(this).attr('name') != 'price' || $serviceType.val() == 'simple' || $serviceType.val() == 'package')) {
                                showModal = true;
                            }
                        });
                    }
                    if (showModal) {
                        $updateStaffModal.data('panel', containers.general).booklyModal('show');
                    } else {
                        submitServiceFrom($serviceDialog, updateStaffChoice);
                    }
                });

                /**
                 * Update staff services modal
                 */
                $updateStaffModal.off().on('click', '.bookly-yes', function() {
                    $updateStaffModal.booklyModal('hide');
                    if ($('#bookly-remember-my-choice').prop('checked')) {
                        updateStaffChoice = true;
                    }
                    submitServiceFrom($serviceDialog, 1);
                }).on('click', '.bookly-no', function() {
                    if ($('#bookly-remember-my-choice').prop('checked')) {
                        updateStaffChoice = false;
                    }
                    submitServiceFrom($serviceDialog, 0);
                });

                /**
                 * Local functions
                 */
                function encodeHTML(s) {
                    return s === null ? '' : s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                }

                function submitServiceFrom($panel, update_staff) {
                    $('input[name=update_staff]', $panel).val(update_staff ? 1 : 0);
                    $('input[name=package_service_changed]', $panel).val($panel.find('[name=package_service]').data('last_value') != $panel.find('[name=package_service]').val() ? 1 : 0);
                    var ladda = rangeTools.ladda($('#bookly-save', $panel).get(0)),
                        data = booklySerialize.form($('form', $panel));
                    $(document.body).trigger('service.submitForm', [$panel, data]);
                    $.post(ajaxurl, booklySerialize.buildRequestData('bookly_update_service', data), function(response) {
                        if (response.success) {
                            booklyAlert(response.data.alert);
                            if (response.data.new_extras_list) {
                                BooklyExtrasL10n.list = response.data.new_extras_list
                            }
                            $servicesList.DataTable().ajax.reload(null, false);
                            $serviceDialog.booklyModal('hide');
                        }
                    }, 'json').always(function() {
                        ladda.stop();
                    });
                }
            }
        });
    }

    $servicesList.on('click', '[data-action="edit"]', function() {
        let $tr = $(this).closest('tr'),
            data = $servicesList.DataTable().row($tr.hasClass('child') ? $tr.prev() : $tr).data();
        editService(data.id);
    });

    $(document.body).on('service.edit', {}, function(event, service_id) {
        editService(service_id);
    });

    $('.nav-scrollable').booklyNavScrollable();
});