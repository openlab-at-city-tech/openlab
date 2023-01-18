import $ from 'jquery';
import {opt, laddaStart, scrollTo, booklyAjax} from './shared.js';
import stepExtras from './extras_step.js';
import stepTime from './time_step.js';
import stepCart from './cart_step.js';
import Chain from './components/Chain.svelte';

/**
 * Service step.
 */
export default function stepService(params) {
    if (opt[params.form_id].skip_steps.service) {
        if (!opt[params.form_id].skip_steps.extras && opt[params.form_id].step_extras == 'before_step_time') {
            stepExtras(params)
        } else {
            stepTime(params);
        }
        return;
    }
    var data = {
            action: 'bookly_render_service',
        },
        $container = opt[params.form_id].$container;
    if (opt[params.form_id].use_client_time_zone) {
        data.time_zone = opt[params.form_id].timeZone;
        data.time_zone_offset = opt[params.form_id].timeZoneOffset;
    }
    $.extend(data, params);
    booklyAjax({
        data
    }).then(response => {
        BooklyL10n.csrf_token = response.csrf_token;
        $container.html(response.html);
        scrollTo($container, params.form_id);

        var $chain = $('.bookly-js-chain', $container),
            $date_from = $('.bookly-js-date-from', $container),
            $week_days = $('.bookly-js-week-days', $container),
            $select_time_from = $('.bookly-js-select-time-from', $container),
            $select_time_to = $('.bookly-js-select-time-to', $container),
            $next_step = $('.bookly-js-next-step', $container),
            $mobile_next_step = $('.bookly-js-mobile-next-step', $container),
            $mobile_prev_step = $('.bookly-js-mobile-prev-step', $container),
            locations = response.locations,
            categories = response.categories,
            services = response.services,
            staff = response.staff,
            chain = response.chain,
            required = response.required,
            defaults = opt[params.form_id].defaults,
            servicesPerLocation = response.services_per_location || false,
            serviceNameWithDuration = response.service_name_with_duration,
            staffNameWithPrice = response.staff_name_with_price,
            collaborativeHideStaff = response.collaborative_hide_staff,
            showRatings = response.show_ratings,
            showCategoryInfo = response.show_category_info,
            showServiceInfo = response.show_service_info,
            showStaffInfo = response.show_staff_info,
            maxQuantity = response.max_quantity || 1,
            multiple = response.multi_service || false,
            l10n = response.l10n,
            customJS = response.custom_js
        ;

        // Set up selects.
        if (serviceNameWithDuration) {
            $.each(services, function(id, service) {
                service.name = service.name + ' ( ' + service.duration + ' )';
            });
        }

        let c = new Chain({
            target: $chain.get(0),
            props: {
                items: chain,
                data: {
                    locations,
                    categories,
                    services,
                    staff,
                    defaults,
                    required,
                    servicesPerLocation,
                    staffNameWithPrice,
                    collaborativeHideStaff,
                    showRatings,
                    showCategoryInfo,
                    showServiceInfo,
                    showStaffInfo,
                    maxQuantity,
                    date_from_element: $date_from,
                    hasLocationSelect: !opt[params.form_id].form_attributes.hide_locations,
                    hasCategorySelect: !opt[params.form_id].form_attributes.hide_categories,
                    hasServiceSelect: !(opt[params.form_id].form_attributes.hide_services && defaults.service_id),
                    hasStaffSelect: !opt[params.form_id].form_attributes.hide_staff_members,
                    hasDurationSelect: !opt[params.form_id].form_attributes.hide_service_duration,
                    hasNopSelect: opt[params.form_id].form_attributes.show_number_of_persons,
                    hasQuantitySelect: !opt[params.form_id].form_attributes.hide_quantity,
                    l10n
                },
                multiple
            }
        });

        // Init Pickadate.
        $date_from.data('date_min', response.date_min || true);
        $date_from.pickadate({
            formatSubmit: 'yyyy-mm-dd',
            format: opt[params.form_id].date_format,
            min: response.date_min || true,
            max: response.date_max || true,
            clear: false,
            close: false,
            today: BooklyL10n.today,
            monthsFull: BooklyL10n.months,
            monthsShort: BooklyL10n.monthsShort,
            weekdaysFull: BooklyL10n.days,
            weekdaysShort: BooklyL10n.daysShort,
            labelMonthNext: BooklyL10n.nextMonth,
            labelMonthPrev: BooklyL10n.prevMonth,
            firstDay: opt[params.form_id].firstDay,
            onSet: function(timestamp) {
                if ($.isNumeric(timestamp.select)) {
                    // Checks appropriate day of the week
                    var date = new Date(timestamp.select);
                    $('.bookly-js-week-days input:checkbox[value="' + (date.getDay() + 1) + '"]:not(:checked)', $container).attr('checked', true).trigger('change');
                }
            },
            onClose: function() {
                $date_from.data('updated', true);
                // Hide for skip tab navigations by days of the month when the calendar is closed
                $('#' + $date_from.attr('aria-owns')).hide();
            },
        }).focusin(function() {
            // Restore calendar visibility, changed on onClose
            $('#' + $date_from.attr('aria-owns')).show();
        });

        $('.bookly-js-go-to-cart', $container).on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            laddaStart(this);
            stepCart({form_id: params.form_id, from_step: 'service'});
        });

        if (opt[params.form_id].form_attributes.hide_date) {
            $('.bookly-js-available-date', $container).hide();
        }
        if (opt[params.form_id].form_attributes.hide_week_days) {
            $('.bookly-js-week-days', $container).hide();
        }
        if (opt[params.form_id].form_attributes.hide_time_range) {
            $('.bookly-js-time-range', $container).hide();
        }

        // time from
        $select_time_from.on('change', function() {
            var start_time = $(this).val(),
                end_time = $select_time_to.val(),
                $last_time_entry = $('option:last', $select_time_from);

            $select_time_to.empty();

            // case when we click on the not last time entry
            if ($select_time_from[0].selectedIndex < $last_time_entry.index()) {
                // clone and append all next "time_from" time entries to "time_to" list
                $('option', this).each(function() {
                    if ($(this).val() > start_time) {
                        $select_time_to.append($(this).clone());
                    }
                });
                // case when we click on the last time entry
            } else {
                $select_time_to.append($last_time_entry.clone()).val($last_time_entry.val());
            }

            var first_value = $('option:first', $select_time_to).val();
            $select_time_to.val(end_time >= first_value ? end_time : first_value);
        });

        let stepServiceValidator = function() {
            let valid = true,
                $scroll_to = null;

            $(c.validate()).each(function(_, status) {
                if (!status.valid) {
                    valid = false;
                    let $el = $(status.el);
                    if ($el.is(':visible')) {
                        $scroll_to = $el;
                        return false;
                    }
                }
            });

            $date_from.removeClass('bookly-error');
            // date validation
            if (!$date_from.val()) {
                valid = false;
                $date_from.addClass('bookly-error');
                if ($scroll_to === null) {
                    $scroll_to = $date_from;
                }
            }

            // week days
            if ($week_days.length && !$(':checked', $week_days).length) {
                valid = false;
                $week_days.addClass('bookly-error');
                if ($scroll_to === null) {
                    $scroll_to = $week_days;
                }
            } else {
                $week_days.removeClass('bookly-error');
            }

            if ($scroll_to !== null) {
                scrollTo($scroll_to, params.form_id);
            }

            return valid;
        };

        // "Next" click
        $next_step.on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();

            if (stepServiceValidator()) {

                laddaStart(this);

                // Execute custom JavaScript
                if (customJS) {
                    try {
                        $.globalEval(customJS.next_button);
                    } catch (e) {
                        // Do nothing
                    }
                }

                // Prepare chain data.
                let chain = [],
                    has_extras = 0,
                    time_requirements = 0,
                    recurrence_enabled = 1,
                    _time_requirements = {'required': 2, 'optional': 1, 'off': 0};
                $.each(c.getValues(), function(_, values) {
                    let _service = services[values.serviceId];

                    chain.push({
                        location_id: values.locationId,
                        service_id: values.serviceId,
                        staff_ids: values.staffIds,
                        units: values.duration,
                        number_of_persons: values.nop,
                        quantity: values.quantity
                    });
                    time_requirements = Math.max(
                        time_requirements,
                        _time_requirements[_service.hasOwnProperty('time_requirements')
                            ? _service.time_requirements
                            : 'required']
                    );
                    recurrence_enabled = Math.min(recurrence_enabled, _service.recurrence_enabled);
                    has_extras += _service.has_extras;
                });

                // Prepare days.
                var days = [];
                $('.bookly-js-week-days input:checked', $container).each(function() {
                    days.push(this.value);
                });
                booklyAjax({
                    type: 'POST',
                    data: {
                        action: 'bookly_session_save',
                        form_id: params.form_id,
                        chain: chain,
                        date_from: $date_from.pickadate('picker').get('select', 'yyyy-mm-dd'),
                        days: days,
                        time_from: opt[params.form_id].form_attributes.hide_time_range ? null : $select_time_from.val(),
                        time_to: opt[params.form_id].form_attributes.hide_time_range ? null : $select_time_to.val(),
                        no_extras: has_extras == 0
                    }
                }).then(response => {
                    opt[params.form_id].no_time = time_requirements == 0;
                    opt[params.form_id].no_extras = has_extras == 0;
                    opt[params.form_id].recurrence_enabled = recurrence_enabled == 1;
                    if (opt[params.form_id].skip_steps.extras) {
                        stepTime({form_id: params.form_id});
                    } else {
                        if (has_extras == 0 || opt[params.form_id].step_extras == 'after_step_time') {
                            stepTime({form_id: params.form_id});
                        } else {
                            stepExtras({form_id: params.form_id});
                        }
                    }
                });
            }
        });

        $mobile_next_step.on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (stepServiceValidator()) {
                if (opt[params.form_id].skip_steps.service_part2) {
                    laddaStart(this);
                    $next_step.trigger('click');
                } else {
                    $('.bookly-js-mobile-step-1', $container).hide();
                    $('.bookly-js-mobile-step-2', $container).css('display', 'block');
                    scrollTo($container, params.form_id);
                }
            }

            return false;
        });

        if (opt[params.form_id].skip_steps.service_part1) {
            // Skip scrolling
            // Timeout to let form set default values
            setTimeout(function() {
                opt[params.form_id].scroll = false;
                $mobile_next_step.trigger('click');
            }, 0);
            $mobile_prev_step.remove();
        } else {
            $mobile_prev_step.on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                $('.bookly-js-mobile-step-1', $container).show();
                $('.bookly-js-mobile-step-2', $container).hide();
                return false;
            });
        }
    });
}