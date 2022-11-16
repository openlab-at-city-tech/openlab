jQuery(function($) {
    var $form = $('#bookly-short-code-form'),
        $select_location = $('#bookly-select-location', $form),
        $select_category = $('#bookly-select-category', $form),
        $select_service = $('#bookly-select-service', $form),
        $select_employee = $('#bookly-select-employee', $form),
        $hide_locations = $('#bookly-hide-locations', $form),
        $hide_categories = $('#bookly-hide-categories', $form),
        $hide_services = $('#bookly-hide-services', $form),
        $hide_staff = $('#bookly-hide-employee', $form),
        $hide_service_duration = $('#bookly-hide-service-duration', $form),
        $hide_number_of_persons = $('#bookly-hide-number-of-persons', $form),
        $hide_quantity = $('#bookly-hide-quantity', $form),
        $hide_date = $('#bookly-hide-date', $form),
        $hide_week_days = $('#bookly-hide-week-days', $form),
        $hide_time_range = $('#bookly-hide-time-range', $form),
        $add_button = $('#add-bookly-form'),
        $insert = $('button.bookly-js-insert-shortcode', $form)
    ;

    $add_button.on('click', function() {
        window.parent.tb_show(BooklyFormShortCodeL10n.title, this.href);
        window.setTimeout(function() {
            $('#TB_window').css({
                'overflow-x': 'auto',
                'overflow-y': 'hidden'
            });
        }, 100);
    });

    // insert data into select
    function setSelect($select, data, value) {
        // reset select
        $('option:not([value=""])', $select).remove();
        // and fill the new data
        var docFragment = document.createDocumentFragment();

        function valuesToArray(obj) {
            return Object.keys(obj).map(function(key) {
                return obj[key];
            });
        }

        function compare(a, b) {
            if (parseInt(a.pos) < parseInt(b.pos))
                return -1;
            if (parseInt(a.pos) > parseInt(b.pos))
                return 1;
            return 0;
        }

        // sort select by position
        data = valuesToArray(data).sort(compare);

        $.each(data, function(key, object) {
            var option = document.createElement('option');
            option.value = object.id;
            option.text = object.name;
            docFragment.appendChild(option);
        });
        $select.append(docFragment);
        // set default value of select
        $select.val(value);
    }

    function setSelects(location_id, category_id, service_id, staff_id) {
        var _location_id = (BooklyL10nGlobal.custom_location_settings == '1' && location_id) ? location_id : 0;
        var _staff = {}, _services = {}, _categories = {}, _nop = {}, _max_capacity = null, _min_capacity = null;
        $.each(BooklyL10nGlobal.casest.staff, function(id, staff_member) {
            if (!location_id || BooklyL10nGlobal.casest.locations[location_id].staff.hasOwnProperty(id)) {
                if (!service_id) {
                    if (!category_id) {
                        _staff[id] = staff_member;
                    } else {
                        $.each(staff_member.services, function(s_id) {
                            if (BooklyL10nGlobal.casest.services[s_id].category_id == category_id) {
                                _staff[id] = staff_member;
                                return false;
                            }
                        });
                    }
                } else if (staff_member.services.hasOwnProperty(service_id)) {
                    // var _location_id = staff_member.services[service_id].locations.hasOwnProperty(location_id) ? location_id : 0;
                    if (staff_member.services[service_id].locations.hasOwnProperty(_location_id)) {
                        if (staff_member.services[service_id].locations[_location_id].price != null) {
                            _min_capacity = _min_capacity ? Math.min(_min_capacity, staff_member.services[service_id].locations[_location_id].min_capacity) : staff_member.services[service_id].locations[_location_id].min_capacity;
                            _max_capacity = _max_capacity ? Math.max(_max_capacity, staff_member.services[service_id].locations[_location_id].max_capacity) : staff_member.services[service_id].locations[_location_id].max_capacity;
                            _staff[id] = {
                                id: id,
                                name: staff_member.name + ' (' + staff_member.services[service_id].locations[_location_id].price + ')',
                                pos: staff_member.pos
                            };
                        } else {
                            _staff[id] = {
                                id: id,
                                name: staff_member.name,
                                pos: staff_member.pos
                            };
                        }
                    }
                }
            }
        });
        if (!location_id) {
            _categories = BooklyL10nGlobal.casest.categories;
            $.each(BooklyL10nGlobal.casest.services, function(id, service) {
                if (!category_id || service.category_id == category_id) {
                    if (!staff_id || BooklyL10nGlobal.casest.staff[staff_id].services.hasOwnProperty(id)) {
                        _services[id] = service;
                    }
                }
            });
        } else {
            var category_ids = [],
                service_ids = [];
            $.each(BooklyL10nGlobal.casest.staff, function(st_id) {
                $.each(BooklyL10nGlobal.casest.staff[st_id].services, function(s_id) {
                    if (BooklyL10nGlobal.casest.staff[st_id].services[s_id].locations.hasOwnProperty(_location_id)) {
                        category_ids.push(BooklyL10nGlobal.casest.services[s_id].category_id);
                        service_ids.push(s_id);
                    }
                });
            });
            $.each(BooklyL10nGlobal.casest.categories, function(id, category) {
                if ($.inArray(parseInt(id), category_ids) > -1) {
                    _categories[id] = category;
                }
            });
            $.each(BooklyL10nGlobal.casest.services, function(id, service) {
                if ($.inArray(id, service_ids) > -1) {
                    if (!category_id || service.category_id == category_id) {
                        if (!staff_id || BooklyL10nGlobal.casest.staff[staff_id].services.hasOwnProperty(id)) {
                            _services[id] = service;
                        }
                    }
                }
            });
        }

        setSelect($select_category, _categories, category_id);
        setSelect($select_service, _services, service_id);
        setSelect($select_employee, _staff, staff_id);
    }

    // Location select change
    $select_location.on('change', function() {
        var location_id = this.value,
            category_id = $select_category.val() || '',
            service_id = $select_service.val() || '',
            staff_id = $select_employee.val() || ''
        ;

        // Validate selected values.
        if (location_id != '') {
            if (staff_id != '' && !BooklyL10nGlobal.casest.locations[location_id].staff.hasOwnProperty(staff_id)) {
                staff_id = '';
            }
            if (service_id != '') {
                var valid = false;
                $.each(BooklyL10nGlobal.casest.locations[location_id].staff, function(id) {
                    if (BooklyL10nGlobal.casest.staff[id].services.hasOwnProperty(service_id)) {
                        valid = true;
                        return false;
                    }
                });
                if (!valid) {
                    service_id = '';
                }
            }
            if (category_id != '') {
                var valid = false;
                $.each(BooklyL10nGlobal.casest.locations[location_id].staff, function(id) {
                    $.each(BooklyL10nGlobal.casest.staff[id].services, function(s_id) {
                        if (BooklyL10nGlobal.casest.services[s_id].category_id == category_id) {
                            valid = true;
                            return false;
                        }
                    });
                    if (valid) {
                        return false;
                    }
                });
                if (!valid) {
                    category_id = '';
                }
            }
        }
        setSelects(location_id, category_id, service_id, staff_id);
    });

    // Category select change
    $select_category.on('change', function() {
        var location_id = $select_location.val() || '',
            category_id = this.value,
            service_id = $select_service.val() || '',
            staff_id = $select_employee.val() || ''
        ;

        // Validate selected values.
        if (category_id != '') {
            if (service_id != '') {
                if (BooklyL10nGlobal.casest.services[service_id].category_id != category_id) {
                    service_id = '';
                }
            }
            if (staff_id != '') {
                var valid = false;
                $.each(BooklyL10nGlobal.casest.staff[staff_id].services, function(id) {
                    if (BooklyL10nGlobal.casest.services[id].category_id == category_id) {
                        valid = true;
                        return false;
                    }
                });
                if (!valid) {
                    staff_id = '';
                }
            }
        }
        setSelects(location_id, category_id, service_id, staff_id);
    });

    // Service select change
    $select_service.on('change', function() {
        var location_id = $select_location.val() || '',
            category_id = '',
            service_id = this.value,
            staff_id = $select_employee.val() || ''
        ;

        // Validate selected values.
        if (service_id != '') {
            if (staff_id != '' && !BooklyL10nGlobal.casest.staff[staff_id].services.hasOwnProperty(service_id)) {
                staff_id = '';
            }
        }
        setSelects(location_id, category_id, service_id, staff_id);
        if (service_id) {
            $select_category.val(BooklyL10nGlobal.casest.services[service_id].category_id);
        }
    });

    window.getBooklyShortCode = function() {
        var shortCode = '[bookly-form',
            hide = [];
        if ($select_location.val()) {
            shortCode += ' location_id="' + $select_location.val() + '"';
        }
        if ($select_category.val()) {
            shortCode += ' category_id="' + $select_category.val() + '"';
        }
        if ($hide_locations.is(':checked')) {
            hide.push('locations');
        }
        if ($hide_categories.is(':checked')) {
            hide.push('categories');
        }
        if ($select_service.val()) {
            shortCode += ' service_id="' + $select_service.val() + '"';
        }
        if ($hide_services.is(':checked')) {
            hide.push('services');
        }
        if ($hide_service_duration.is(':checked')) {
            hide.push('service_duration');
        }
        if ($select_employee.val()) {
            shortCode += ' staff_member_id="' + $select_employee.val() + '"';
        }
        if ($hide_number_of_persons.is(':not(:checked)')) {
            shortCode += ' show_number_of_persons="1"';
        }
        if ($hide_quantity.is(':checked')) {
            hide.push('quantity');
        }
        if ($hide_staff.is(':checked')) {
            hide.push('staff_members');
        }
        if ($hide_date.is(':checked')) {
            hide.push('date')
        }
        if ($hide_week_days.is(':checked')) {
            hide.push('week_days')
        }
        if ($hide_time_range.is(':checked')) {
            hide.push('time_range');
        }
        if (hide.length > 0) {
            shortCode += ' hide="' + hide.join() + '"';
        }
        shortCode += ']';

        return shortCode;
    };

    // Staff select change
    $select_employee.on('change', function() {
        var location_id = $select_location.val() || '',
            category_id = $select_category.val() || '',
            service_id = $select_service.val() || '',
            staff_id = this.value
        ;

        setSelects(location_id, category_id, service_id, staff_id);
    });

    // Set up draft selects.
    setSelect($select_location, BooklyL10nGlobal.casest.locations);
    setSelect($select_category, BooklyL10nGlobal.casest.categories);
    setSelect($select_service, BooklyL10nGlobal.casest.services);
    setSelect($select_employee, BooklyL10nGlobal.casest.staff);

    $insert
        .on('click', function(e) {
            e.preventDefault();

            window.send_to_editor(window.getBooklyShortCode());

            $select_location.val('');
            $select_category.val('');
            $select_service.val('');
            $select_employee.val('');
            $hide_locations.prop('checked', false);
            $hide_categories.prop('checked', false);
            $hide_services.prop('checked', false);
            $hide_service_duration.prop('checked', false);
            $hide_staff.prop('checked', false);
            $hide_date.prop('checked', false);
            $hide_week_days.prop('checked', false);
            $hide_time_range.prop('checked', false);
            $hide_number_of_persons.prop('checked', true);

            window.parent.tb_remove();
            return false;
        });
});