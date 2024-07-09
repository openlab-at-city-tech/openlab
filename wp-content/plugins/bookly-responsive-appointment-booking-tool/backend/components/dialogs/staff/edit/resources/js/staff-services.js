(function ($) {

    var Services = function($container, options) {
        var obj  = this;
        jQuery.extend(obj.options, options);

        // Load services form
        if (options.reload) {
            $container.html('<div class="bookly-loading"></div>');
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: $.extend({csrf_token: BooklyL10nGlobal.csrf_token}, obj.options.get_staff_services),
                dataType: 'json',
                xhrFields: {withCredentials: true},
                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                success: function (response) {
                    $container.html('');
                    $container.append(response.data.html);
                    $container.removeData('init');
                    obj.options.onLoad();
                    init($container, obj);
                }
            });
        } else {
            init($container, obj);
        }
    };

    function init($container, obj) {
        $(document.body).trigger('special_hours.tab_init', [$container, obj.options]);
        let $services_form = $('form', $container);
        var autoTickCheckboxes = function () {
            // Handle 'select category' checkbox.
            $('.bookly-js-category-checkbox').each(function () {
                $(this).prop(
                    'checked',
                    $('.bookly-js-category-services .bookly-js-service-checkbox[data-category-id="' + $(this).data('category-id') + '"]:not(:checked)').length == 0
                );
            });
            // Handle 'select all services' checkbox.
            $('#bookly-check-all-entities').prop(
                'checked',
                $('.bookly-js-service-checkbox:not(:checked)').length == 0
            );
        };
        var checkCapacityError = function ($form_group) {
            obj.options.validation(false, '');
            if (parseInt($form_group.find('.bookly-js-capacity-min').val()) > parseInt($form_group.find('.bookly-js-capacity-max').val())) {
                $form_group.find('input').addClass('is-invalid');
                $services_form.find('.bookly-js-services-error').html(obj.options.l10n.capacity_error);
                $services_form.find('#bookly-services-save').prop('disabled', true);
                obj.options.validation(true, obj.options.l10n.capacity_error);
            } else if (!(parseInt($form_group.find('.bookly-js-capacity-min').val()) > 0)) {
                $form_group.find('input').addClass('is-invalid');
                $services_form.find('.bookly-js-services-error').html(obj.options.l10n.capacity_error);
                $services_form.find('#bookly-services-save').prop('disabled', true);
                obj.options.validation(true, '');
            } else {
                $form_group.find('input').removeClass('is-invalid');
                $services_form.find('.bookly-js-services-error').html('');
                $services_form.find('#bookly-services-save').prop('disabled', false);
            }
        };

        $services_form
            // Select all services related to chosen category
            .on('click', '.bookly-js-category-checkbox', function () {
                $('.bookly-js-category-services [data-category-id="' + $(this).data('category-id') + '"]').prop('checked', $(this).is(':checked')).change();
                autoTickCheckboxes();
            })
            // Check and uncheck all services
            .on('click', '#bookly-check-all-entities', function () {
                $('.bookly-js-service-checkbox', $services_form).prop('checked', $(this).is(':checked')).change();
                $('.bookly-js-category-checkbox').prop('checked', $(this).is(':checked'));
            })
            // Select service
            .on('click', '.bookly-js-service-checkbox', function () {
                autoTickCheckboxes();
            })
            // Save services
            .on('click', '#bookly-services-save', function (e) {
                e.preventDefault();
                let ladda = Ladda.create(this);
                ladda.start();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: booklySerialize.buildRequestDataFromForm('bookly_update_staff_services', $services_form),
                    dataType: 'json',
                    xhrFields: {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success: function (response) {
                        ladda.stop();
                        if (response.success) {
                            obj.options.saving({success: [obj.options.l10n.saved]});
                        }
                    }
                });
            })
            // After reset auto tick group checkboxes.
            .on('click', '#bookly-services-reset', function () {
                setTimeout(function () {
                    autoTickCheckboxes();
                    $('.bookly-js-capacity-form-group', $services_form).each(function () {
                        checkCapacityError($(this));
                    });
                    $('.bookly-js-service-checkbox', $services_form).trigger('change');
                }, 0);
            })
            // Change location
            .on('change', '#staff_location_id', function () {
                let get_staff_services = {
                    action: obj.options.get_staff_services.action,
                    staff_id: obj.options.get_staff_services.staff_id,
                };
                if (this.value != '') {
                    get_staff_services.location_id = this.value;
                }
                new BooklyStaffServices($container, {
                    reload: true,
                    get_staff_services: get_staff_services,
                    l10n: obj.options.l10n,
                });
            })
            // Change default/custom settings for location
            .on('change', '#custom_location_settings', function () {
                if ($(this).val() == 1) {
                    $('#bookly-staff-services', $services_form).show();
                } else {
                    $('#bookly-staff-services', $services_form).hide();
                }
            });

        $('.bookly-js-service-checkbox').on('change', function () {
            let $this = $(this),
                $service = $this.closest('li'),
                $inputs = $service.find('input:not(:checkbox)'),
                $modal = $('#bookly-packages-tip');

            $inputs.attr('disabled', !$this.is(':checked'));

            // Handle package-service connections
            if ($(this).is(':checked') && $service.data('service-type') == 'package') {
                let $checkboxes = $('li[data-service-type="simple"][data-service-id="' + $service.data('sub-service') + '"] .bookly-js-service-checkbox:not(:checked)', $services_form);
                if ($checkboxes.length) {
                    $checkboxes.prop('checked', true).trigger('change');
                    if (obj.options.l10n.hideTip !== '1') {
                        $modal.booklyModal();
                    }
                }
                $('.bookly-js-capacity-min', $service).val($('li[data-service-type="simple"][data-service-id="' + $service.data('sub-service') + '"] .bookly-js-capacity-min', $services_form).val());
                $('.bookly-js-capacity-max', $service).val($('li[data-service-type="simple"][data-service-id="' + $service.data('sub-service') + '"] .bookly-js-capacity-max', $services_form).val());
            }
            if (!$(this).is(':checked') && $service.data('service-type') == 'simple') {
                let $checkboxes = $('li[data-service-type="package"][data-sub-service="' + $service.data('service-id') + '"] .bookly-js-service-checkbox:checked', $services_form);
                if ($checkboxes.length) {
                    $checkboxes.prop('checked', false).trigger('change');
                    if (obj.options.l10n.hideTip !== '1') {
                        $modal.booklyModal();
                    }
                }
            }
        });

        $('#bookly-packages-tip').on('hide.bs.modal', function () {
            if ($(this).find('.bookly-js-dont-show-packages-tip:checked').length) {
                obj.options.l10n.hideTip = '1';
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'bookly_packages_hide_staff_services_tip',
                        csrf_token: BooklyL10nGlobal.csrf_token
                    },
                    dataType: 'json',
                });
            }
        });

        $('.bookly-js-capacity').on('keyup change', function () {
            var $service = $(this).closest('li');
            if ($service.data('service-type') == 'simple') {
                if ($(this).hasClass('bookly-js-capacity-min')) {
                    $('li[data-service-type="package"][data-sub-service="' + $service.data('service-id') + '"] .bookly-js-capacity-min', $services_form).val($(this).val());
                } else {
                    $('li[data-service-type="package"][data-sub-service="' + $service.data('service-id') + '"] .bookly-js-capacity-max', $services_form).val($(this).val());
                }
            }
            checkCapacityError($(this).closest('.form-group'));
        });
        $('#custom_location_settings', $services_form).trigger('change');
        autoTickCheckboxes();
    }

    Services.prototype.options = {
        reload: false,
        get_staff_services: {
            action  : 'bookly_get_staff_services',
            staff_id: -1,
        },
        booklyAlert: window.booklyAlert,
        saving: function (alerts) {
            $(document.body).trigger('staff.saving', [alerts]);
        },
        validation: function (has_error, info) {
            $(document.body).trigger('staff.validation', ['staff-services', has_error, info]);
        },
        onLoad: function () {},
        l10n: {}
    };

    window.BooklyStaffServices = Services;
})(jQuery);