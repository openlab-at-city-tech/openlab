(function ($) {

    let Calendar = function ($container, options) {
        let obj = this;
        jQuery.extend(obj.options, options);

        // Special locale for moment
        moment.locale('bookly', {
            months: obj.options.l10n.datePicker.monthNames,
            monthsShort: obj.options.l10n.datePicker.monthNamesShort,
            weekdays: obj.options.l10n.datePicker.dayNames,
            weekdaysShort: obj.options.l10n.datePicker.dayNamesShort,
            meridiem: function (hours, minutes, isLower) {
                return hours < 12
                    ? obj.options.l10n.datePicker.meridiem[isLower ? 'am' : 'AM']
                    : obj.options.l10n.datePicker.meridiem[isLower ? 'pm' : 'PM'];
            },
        });
        let existsAppointmentForm = typeof BooklyAppointmentDialog !== 'undefined'
        // Settings for Event Calendar
        let settings = {
            view: 'timeGridWeek',
            views: {
                dayGridMonth: {
                    dayHeaderFormat: function (date) {
                        return moment(date).locale('bookly').format('ddd');
                    },
                    displayEventEnd: true,
                    dayMaxEvents: obj.options.l10n.monthDayMaxEvents === '1'
                },
                timeGridDay: {
                    dayHeaderFormat: function (date) {
                        return moment(date).locale('bookly').format('dddd');
                    },
                    pointer: true
                },
                timeGridWeek: {pointer: true},
                resourceTimeGridDay: {pointer: true}
            },
            nowIndicator: true,
            hiddenDays: obj.options.l10n.hiddenDays,
            slotDuration: obj.options.l10n.slotDuration,
            slotMinTime: obj.options.l10n.slotMinTime,
            slotMaxTime: obj.options.l10n.slotMaxTime,
            scrollTime: obj.options.l10n.scrollTime,
            moreLinkContent: function (arg) {
                return obj.options.l10n.more.replace('%d', arg.num)
            },
            flexibleSlotTimeLimits: true,
            eventStartEditable: false,
            eventDurationEditable: false,
            allDaySlot: false,

            slotLabelFormat: function (date) {
                return moment(date).locale('bookly').format(obj.options.l10n.mjsTimeFormat);
            },
            eventTimeFormat: function (date) {
                return moment(date).locale('bookly').format(obj.options.l10n.mjsTimeFormat);
            },
            dayHeaderFormat: function (date) {
                return moment(date).locale('bookly').format('ddd, D');
            },
            listDayFormat: function (date) {
                return moment(date).locale('bookly').format('dddd');
            },
            firstDay: obj.options.l10n.datePicker.firstDay,
            locale: obj.options.l10n.locale.replace('_', '-'),
            buttonText: {
                today: obj.options.l10n.today,
                dayGridMonth: obj.options.l10n.month,
                timeGridWeek: obj.options.l10n.week,
                timeGridDay: obj.options.l10n.day,
                resourceTimeGridDay: obj.options.l10n.day,
                listWeek: obj.options.l10n.list
            },
            noEventsContent: obj.options.l10n.noEvents,
            eventSources: [{
                url: ajaxurl,
                method: 'POST',
                extraParams: function () {
                    return {
                        action: 'bookly_get_staff_appointments',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                        staff_ids: obj.options.getStaffMemberIds(),
                        location_ids: obj.options.getLocationIds(),
                        service_ids: obj.options.getServiceIds()
                    };
                }
            }],
            eventBackgroundColor: '#ccc',
            eventMouseEnter: function (arg) {
                if (arg.event.display === 'background') {
                    return '';
                }
                let $event = $(arg.el);
                if (arg.event.display === 'auto' && arg.view.type !== 'listWeek') {
                    if (!$event.find('.bookly-ec-popover').length) {
                        let $popover = $('<div class="bookly-popover bs-popover-top bookly-ec-popover">')
                        let $arrow = $('<div class="arrow" style="left:8px;">');
                        let $body = $('<div class="popover-body">');
                        let $buttons = existsAppointmentForm ? popoverButtons(arg) : '';
                        $body.append(arg.event.extendedProps.tooltip).append($buttons).css({minWidth: '200px'});
                        $popover.append($arrow).append($body);
                        $event.append($popover);
                    }
                    fixPopoverPosition($event.find('.bookly-ec-popover'));
                }
            },
            eventContent: function (arg) {
                if (arg.event.display === 'background') {
                    return '';
                }
                let event = arg.event;
                let props = event.extendedProps;
                let nodes = [];
                let $time = $('<div class="ec-event-time"/>');
                let $title = $('<div class="ec-event-title"/>');

                $time.append(props.header_text || arg.timeText);
                nodes.push($time.get(0));
                if (arg.view.type === 'listWeek') {
                    let dot = $('<div class="ec-event-dot"></div>').css('border-color', event.backgroundColor);
                    nodes.push($('<div/>').append(dot).get(0));
                }
                $title.append(props.desc || '');
                nodes.push($title.get(0));

                switch (props.overall_status) {
                    case 'pending':
                        $time.addClass('text-muted');
                        $title.addClass('text-muted');
                        break;
                    case 'rejected':
                    case 'cancelled':
                        $time.addClass('text-muted').wrapInner('<s>');
                        $title.addClass('text-muted');
                        break;
                }

                if (arg.view.type === 'listWeek' && existsAppointmentForm) {
                    $title.append(popoverButtons(arg));
                }

                return {domNodes: nodes};
            },
            eventClick: function (arg) {
                if (arg.event.display === 'background') {
                    return;
                }
                arg.jsEvent.stopPropagation();
                if (existsAppointmentForm) {
                    let visible_staff_id;
                    if (arg.view.type === 'resourceTimeGridDay') {
                        visible_staff_id = 0;
                    } else {
                        visible_staff_id = obj.options.getCurrentStaffId();
                    }
                    BooklyAppointmentDialog.showDialog(
                        arg.event.id,
                        null,
                        null,
                        function (event) {
                            if (event == 'refresh') {
                                calendar.refetchEvents();
                            } else {
                                if (event.start === null) {
                                    // Task
                                    calendar.removeEventById(event.id);
                                } else {
                                    if (visible_staff_id == event.resourceId || visible_staff_id == 0) {
                                        // Update event in calendar.
                                        calendar.removeEventById(event.id);
                                        calendar.addEvent(event);
                                    } else {
                                        // Switch to the event owner tab.
                                        jQuery('li > a[data-staff_id=' + event.resourceId + ']').click();
                                    }
                                }
                            }

                            if (locationChanged) {
                                calendar.refetchEvents();
                                locationChanged = false;
                            }
                        }
                    );
                }
            },
            dateClick: function (arg) {
                let staff_id, visible_staff_id;
                if (arg.view.type === 'resourceTimeGridDay') {
                    staff_id = arg.resource.id;
                    visible_staff_id = 0;
                } else {
                    staff_id = visible_staff_id = obj.options.getCurrentStaffId();
                }
                addAppointmentDialog(arg.date, staff_id, visible_staff_id);
            },
            noEventsClick: function (arg) {
                let staffId = obj.options.getCurrentStaffId();
                addAppointmentDialog(arg.view.activeStart, staffId, staffId);
            },
            loading: function (isLoading) {
                if (isLoading) {
                    if (existsAppointmentForm) {
                        BooklyL10nAppDialog.refreshed = true;
                    }
                    if (dateSetFromDatePicker) {
                        dateSetFromDatePicker = false;
                    } else {
                        calendar.setOption('highlightedDates', []);
                    }
                    $('.bookly-ec-loading').show();
                } else {
                    $('.bookly-ec-loading').hide();
                    obj.options.refresh();
                }
            },
            viewDidMount: function (view) {
                calendar.setOption('highlightedDates', []);
                obj.options.viewChanged(view);
            },
            theme: function (theme) {
                theme.button = 'btn btn-default';
                theme.buttonGroup = 'btn-group';
                theme.active = 'active';
                return theme;
            }
        };

        function popoverButtons(arg) {
            const $buttons = arg.view.type === 'listWeek' ? $('<div class="mt-2 d-flex"></div>') : $('<div class="mt-2 d-flex justify-content-end border-top pt-2"></div>');
            let props = arg.event.extendedProps;
            $buttons.append($('<button class="btn btn-success btn-sm mr-1">').append('<i class="far fa-fw fa-edit">'));
            if (obj.options.l10n.recurring_appointments.active == '1' && props.series_id) {
                $buttons.append(
                    $('<a class="btn btn-default btn-sm mr-1">').append('<i class="fas fa-fw fa-link">')
                    .attr('title', obj.options.l10n.recurring_appointments.title)
                    .on('click', function (e) {
                        e.stopPropagation();
                        BooklySeriesDialog.showDialog({
                            series_id: props.series_id,
                            done: function () {
                                calendar.refetchEvents();
                            }
                        });
                    })
                );
            }
            if (obj.options.l10n.waiting_list.active == '1' && props.waitlisted > 0) {
                $buttons.append(
                    $('<a class="btn btn-default btn-sm mr-1">').append('<i class="far fa-fw fa-list-alt">')
                    .attr('title', obj.options.l10n.waiting_list.title)
                );
            }
            if (obj.options.l10n.packages.active == '1' && props.package_id > 0) {
                $buttons.append(
                    $('<a class="btn btn-default btn-sm mr-1">').append('<i class="far fa-fw fa-calendar-alt">')
                    .attr('title', obj.options.l10n.packages.title)
                    .on('click', function (e) {
                        e.stopPropagation();
                        if (obj.options.l10n.packages.active == '1' && props.package_id) {
                            $(document.body).trigger('bookly_packages.schedule_dialog', [props.package_id, function () {
                                calendar.refetchEvents();
                            }]);
                        }
                    })
                );
            }
            $buttons.append(
                $('<a class="btn btn-danger btn-sm text-white">').append('<i class="far fa-fw fa-trash-alt">')
                .attr('title', obj.options.l10n.delete)
                .on('click', function (e) {
                    e.stopPropagation();
                    // Localize contains only string values
                    if (obj.options.l10n.recurring_appointments.active == '1' && props.series_id) {
                        $(document.body).trigger('recurring_appointments.delete_dialog', [calendar, arg.event]);
                    } else {
                        new BooklyConfirmDeletingAppointment({
                                action: 'bookly_delete_appointment',
                                appointment_id: arg.event.id,
                                csrf_token: BooklyL10nGlobal.csrf_token
                            },
                            function (response) {
                                calendar.removeEventById(arg.event.id);
                            }
                        );
                    }
                })
            );

            return $buttons;
        }

        function fixPopoverPosition($popover) {
            let $event = $popover.closest('.ec-event'),
                offset = $event.offset(),
                top = Math.max($popover.outerHeight() + 40, Math.max($event.closest('.ec-body').offset().top, offset.top) - $(document).scrollTop());

            $popover.css('top', (top - $popover.outerHeight() - 4) + 'px')
            $popover.css('left', (offset.left + 2) + 'px')
        }

        function addAppointmentDialog(date, staffId, visibleStaffId) {
            if (existsAppointmentForm) {
                BooklyAppointmentDialog.showDialog(
                    null,
                    parseInt(staffId),
                    moment(date),
                    function (event) {
                        if (event == 'refresh') {
                            calendar.refetchEvents();
                        } else {
                            if (visibleStaffId == event.resourceId || visibleStaffId == 0) {
                                if (event.start !== null) {
                                    if (event.id) {
                                        // Create event in calendar.
                                        calendar.addEvent(event);
                                    } else {
                                        calendar.refetchEvents();
                                    }
                                }
                            } else {
                                // Switch to the event owner tab.
                                jQuery('li[data-staff_id=' + event.resourceId + ']').click();
                            }
                        }

                        if (locationChanged) {
                            calendar.refetchEvents();
                            locationChanged = false;
                        }
                    }
                );
            }
        }

        let dateSetFromDatePicker = false;

        let calendar = new window.EventCalendar($container.get(0), $.extend(true, {}, settings, obj.options.calendar));

        $('.ec-toolbar .ec-title', $container).on('click', function () {
            let picker = $(this).data('daterangepicker');
            picker.setStartDate(calendar.getOption('date'));
            picker.setEndDate(calendar.getOption('date'));
        });
        // Init date picker for fast navigation in Event Calendar.
        $('.ec-toolbar .ec-title', $container).daterangepicker({
            parentEl: '.bookly-js-calendar',
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            locale: obj.options.l10n.datePicker
        }, function (start) {
            dateSetFromDatePicker = true;
            if (calendar.view.type !== 'timeGridDay' && calendar.view.type !== 'resourceTimeGridDay') {
                calendar.setOption('highlightedDates', [start.toDate()]);
            }
            calendar.setOption('date', start.toDate());
        });

        // Export calendar
        this.ec = calendar;
        if (obj.options.l10n.monthDayMaxEvents == '1') {
            let theme = this.ec.getOption('theme');
            theme.month += ' ec-minimalistic';
            this.ec.setOption('theme', theme);
        }
    };

    var locationChanged = false;
    $('body').on('change', '#bookly-appointment-location', function () {
        locationChanged = true;
    });

    Calendar.prototype.options = {
        calendar: {},
        getCurrentStaffId: function () { return -1; },
        getStaffMemberIds: function () { return [this.getCurrentStaffId()]; },
        getServiceIds: function () { return ['all']; },
        getLocationIds: function () { return ['all']; },
        refresh: function () {},
        viewChanged: function () {},
        l10n: {}
    };

    window.BooklyCalendar = Calendar;
})(jQuery);