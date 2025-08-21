jQuery(function ($) {

    let $calendar = $('.bookly-js-calendar'),
        $staffPills = $('.bookly-js-staff-pills ul'),
        $staffLinks = $('li > a', $staffPills),
        $staffFilter = $('#bookly-js-staff-filter'),
        $servicesFilter = $('#bookly-js-services-filter'),
        $locationsFilter = $('#bookly-js-locations-filter'),
        $gcSyncButton = $('#bookly-google-calendar-sync'),
        $ocSyncButton = $('#bookly-outlook-calendar-sync'),
        staffMembers = [],
        staffIds = getCookie('bookly_cal_st_ids'),
        serviceIds = getCookie('bookly_cal_service_ids'),
        locationIds = getCookie('bookly_cal_location_ids'),
        tabId = getCookie('bookly_cal_tab_id'),
        lastView = getCookie('bookly_cal_view') || 'resourceTimeGridDay',
        headerToolbar = {
            start: 'prev,next today',
            center: 'title',
            end: ''
        },
        calendarTimer = null,
        resizeTimer = null;

    /**
     * Init tabs.
     */
    // Scrollable pills
    $('.bookly-js-staff-pills').booklyNavScrollable();

    $staffLinks.on('click', function (e) {
        e.preventDefault();
        $staffLinks.removeClass('active');
        $(this).addClass('active');
        let staff_id = $(this).data('staff_id');
        setCookie('bookly_cal_tab_id', staff_id);
        calendar.ec.setOption('resources', getStaffMembers());
        calendar.ec.refetchEvents();
    });

    function getStaffMembers() {
        let _staffMembers = [];
        if ($staffFilter.length > 0) {
            let _staff_id = $staffLinks.filter('.active').data('staff_id');
            if (_staff_id === 0) {
                $staffFilter.booklyDropdown('getSelectedExt').forEach(function (item) {
                    _staffMembers.push({id: item.value, title: encodeHTML(item.name.trim())});
                });
            } else {
                _staffMembers.push({
                    id: _staff_id,
                    title: encodeHTML($staffFilter.find('[data-value=' + _staff_id + ']').text().trim())
                });
            }
        } else {
            let $staffId = $('#bookly-js-staff-id');
            _staffMembers.push({id: $staffId.val(), title: $staffId.data('name')});
        }

        return _staffMembers;
    }

    $staffLinks.filter('[data-staff_id=' + tabId + ']').addClass('active');
    if ($staffLinks.filter('.active').length === 0) {
        $staffLinks.eq(0).addClass('active').parent().show();
    }

    /**
     * Init staff filter.
     */
    $staffFilter.booklyDropdown({
        onChange: function (values, selected, all) {
            let ids = [];
            staffMembers = [];
            this.booklyDropdown('getSelectedExt').forEach(function (item) {
                ids.push(item.value);
                staffMembers.push({id: item.value, title: encodeHTML(item.name.trim())});
            });
            calendar.ec.setOption('resources', staffMembers);
            setCookie('bookly_cal_st_ids', ids);
            if (all) {
                $staffLinks.filter('[data-staff_id!=0]').parent().toggle(selected);
            } else {
                values.forEach(function (value) {
                    $staffLinks.filter('[data-staff_id=' + value + ']').parent().toggle(selected);
                });
            }
            if ($staffLinks.filter(':visible.active').length === 0) {
                $staffLinks.filter(':visible:first').triggerHandler('click');
            } else if ($staffLinks.filter('.active').data('staff_id') === 0) {
                calendar.ec.refetchEvents();
            }
        }
    });
    if (staffIds === null) {
        $staffFilter.booklyDropdown('selectAll');
    } else if (staffIds !== '') {
        $staffFilter.booklyDropdown('setSelected', staffIds.split(','));
    } else {
        $staffFilter.booklyDropdown('toggle');
    }
    // Populate staffMembers.
    $staffFilter.booklyDropdown('getSelectedExt').forEach(function (item) {
        $staffLinks.filter('[data-staff_id=' + item.value + ']').parent().show();
    });
    staffMembers = getStaffMembers();

    /**
     * Init services filter.
     */
    $servicesFilter.booklyDropdown({
        onChange: function (values, selected, all) {
            serviceIds = this.booklyDropdown('getSelected');
            setCookie('bookly_cal_service_ids', serviceIds);
            calendar.ec.refetchEvents();
        }
    });
    if (serviceIds === null) {
        $servicesFilter.booklyDropdown('selectAll');
    } else if (serviceIds !== '') {
        $servicesFilter.booklyDropdown('setSelected', serviceIds.split(','));
    } else {
        $servicesFilter.booklyDropdown('toggle');
    }
    // Populate serviceIds.
    serviceIds = $servicesFilter.booklyDropdown('getSelected');

    /**
     * Init locations filter.
     */
    $locationsFilter.booklyDropdown({
        onChange: function (values, selected, all) {
            locationIds = this.booklyDropdown('getSelected');
            setCookie('bookly_cal_location_ids', locationIds);
            calendar.ec.refetchEvents();
        }
    });
    if (locationIds === null || locationIds === 'all') {
        $locationsFilter.booklyDropdown('selectAll');
    } else if (locationIds !== '') {
        $locationsFilter.booklyDropdown('setSelected', locationIds.split(','));
    } else {
        $locationsFilter.booklyDropdown('toggle');
    }
    // Populate locationIds.
    locationIds = $locationsFilter.booklyDropdown('getSelected');

    /**
     * Init calendar refresh buttons.
     */
    function refreshBooklyCalendar() {
        let $refresh = $('input[name="bookly_calendar_refresh_rate"]:checked');
        clearTimeout(calendarTimer);
        if ($refresh.val() > 0) {
            calendarTimer = setInterval(function () {
                calendar.ec.refetchEvents();
            }, $refresh.val() * 1000);
        }
    }

    function setCalendarHeight(height) {
        if (BooklyL10n.scrollable_calendar !== '0') {
            calendar.ec.setOption('height', height);
        }
    }

    function encodeHTML(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    $('#bookly-calendar-refresh').on('click', function () {
        calendar.ec.refetchEvents();
    });

    $('#bookly-calendar-fullscreen').on('click', function () {
        if (document.activeElement) {
            document.activeElement.blur();
        }
        let $card = $(this).closest('.card');
        $card.toggleClass('bookly-fullscreen');
        setCalendarHeight(heightEC(calendar.ec.getOption('view')));
        if ($card.hasClass('bookly-fullscreen')) {
            $("body").css("overflow", "hidden");
            $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
        } else {
            $("body").css("overflow", "auto");
            $(this).find('i').removeClass('fa-compress').addClass('fa-expand');
        }
    });

    $('input[name="bookly_calendar_refresh_rate"]').change(function () {
        $.post(
            ajaxurl,
            {action: 'bookly_update_calendar_refresh_rate', csrf_token: BooklyL10nGlobal.csrf_token, rate: this.value},
            function (response) {},
            'json'
        );
        if (this.value > 0) {
            $(this).closest('.btn-group').find('button').addClass('btn-success').removeClass('btn-default');
        } else {
            $(this).closest('.btn-group').find('button').addClass('btn-default').removeClass('btn-success');
        }
        refreshBooklyCalendar();
    });

    refreshBooklyCalendar();

    // View buttons
    if (['dayGridMonth', 'timeGridWeek', 'resourceTimeGridDay', 'listWeek'].includes(lastView)) {
        headerToolbar.end = 'dayGridMonth,timeGridWeek,resourceTimeGridDay,grid listWeek';
    } else {
        headerToolbar.end = 'resourceTimelineMonth,resourceTimelineWeek,resourceTimelineDay,timeline listWeek';
    }

    /**
     * Init Calendar.
     */
    let calendar = new BooklyCalendar($calendar, {
        calendar: {
            // General Display.
            headerToolbar: headerToolbar,
            // Views.
            view: lastView === 'listWeekTimeline' ? 'listWeek' : lastView,
            views: {
                resourceTimeGridDay: {
                    resources: staffMembers,
                    filterResourcesWithEvents: BooklyL10n.filterResourcesWithEvents,
                    titleFormat: {year: 'numeric', month: 'short', day: 'numeric', weekday: 'short'}
                },
                resourceTimelineDay: {
                    resources: staffMembers,
                    titleFormat: {year: 'numeric', month: 'short', day: 'numeric', weekday: 'short'}
                },
                resourceTimelineMonth: {
                    slotDuration: {days: 1},
                    resources: staffMembers,
                },
                resourceTimelineWeek: {
                    resources: staffMembers,
                },
            }
        },
        getCurrentStaffId: function () {
            return $staffLinks.filter('.active').data('staff_id');
        },
        getStaffMemberIds: function () {
            let ids = [],
                staffId = this.getCurrentStaffId()
            ;

            if (staffId == 0) {
                staffMembers.forEach(function (staff) {
                    ids.push(staff.id);
                });
            } else {
                ids.push(staffId);
            }

            return ids;
        },
        getLocationIds: function () {
            return locationIds;
        },
        getServiceIds: function () {
            return serviceIds;
        },
        refresh: refreshBooklyCalendar,
        viewChanged: function (view) {
            if (view.type === 'listWeek' && calendar.ec.getOption('headerToolbar').end.includes('resourceTimelineMonth')) {
                setCookie('bookly_cal_view', 'listWeekTimeline');
            } else {
                setCookie('bookly_cal_view', view.type);
            }
            resizeTimer = setTimeout(function () {
                setCalendarHeight(heightEC(view.type));
            }, 0);
        },
        l10n: BooklyL10n
    });

    function heightEC(view_type) {
        let calendar_tools_height = 81,
            calendar_top = $calendar.offset().top + calendar_tools_height,
            calendar_height = $(window).height() - calendar_top,
            day_head_height = 31,
            weeks_rows = 5,
            day_height = calendar_height / weeks_rows,
            slot_height = 20.4,
            day_slots_count = Math.floor((day_height - day_head_height) / slot_height);
        if (day_slots_count < 3) {
            day_slots_count = 3;
        }
        let height = ((day_slots_count * slot_height + day_head_height) * weeks_rows);
        if (view_type != 'dayGridMonth') {
            if (['resourceTimelineMonth', 'resourceTimelineWeek', 'resourceTimelineDay'].includes(view_type)) {
                height = 'auto';
            } else if ($('.ec-content', $calendar).height() > height) {
                height = Math.max(height, 300);
            } else {
                height = 'auto';
            }
        }

        return height === 'auto' ? 'auto' : (calendar_tools_height + height) + 'px';
    }

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            setCalendarHeight(heightEC(calendar.ec.getOption('view')));
        }, 500);
    });

    /**
     * Set cookie.
     *
     * @param key
     * @param value
     */
    function setCookie(key, value) {
        var expires = new Date();
        expires.setFullYear(expires.getFullYear() + 3);
        document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
    }

    /**
     * Get cookie.
     *
     * @param key
     * @return {*}
     */
    function getCookie(key) {
        var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    }

    /**
     * Sync with Google Calendar.
     */
    $gcSyncButton.on('click', function () {
        var ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {action: 'bookly_advanced_google_calendar_sync', csrf_token: BooklyL10nGlobal.csrf_token},
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    calendar.ec.refetchEvents();
                }
                booklyAlert(response.data.alert);
                ladda.stop();
            },
            error: function (XHR) {
                booklyAlert({error: ['Status code: ' + XHR.status]});
                ladda.stop();
            }
        });
    });

    /**
     * Sync with Outlook Calendar.
     */
    $ocSyncButton.on('click', function () {
        var ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {action: 'bookly_outlook_calendar_sync', csrf_token: BooklyL10nGlobal.csrf_token},
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    calendar.ec.refetchEvents();
                }
                booklyAlert(response.data.alert);
                ladda.stop();
            },
            error: function (XHR) {
                booklyAlert({error: ['Server status: ' + XHR.status]});
                ladda.stop();
            }
        });
    });
});