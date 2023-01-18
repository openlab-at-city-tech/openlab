(function ($) {

    let DaysOff = function($container, options) {
        let obj = this,
            d = new Date();
        jQuery.extend(obj.options, options);
        $('.bookly-js-holidays').jCal({
            day: new Date(d.getFullYear(), 0, 1),
            days: 1,
            showMonths: 12,
            scrollSpeed: 350,
            action: 'bookly_update_staff_holidays',
            csrf_token: BooklyL10nGlobal.csrf_token,
            staff_id: obj.options.staff_id,
            events: obj.options.l10n.holidays,
            dayOffset: parseInt(obj.options.l10n.firstDay),
            loadingImg: obj.options.l10n.loading_img,
            dow: obj.options.l10n.days,
            ml: obj.options.l10n.months,
            we_are_not_working: obj.options.l10n.we_are_not_working,
            repeat: obj.options.l10n.repeat,
            close: obj.options.l10n.close
        });

        $('.bookly-js-jCalBtn', $container).on('click', function (e) {
            e.preventDefault();
            let trigger = $(this).data('trigger');
            $('.bookly-js-holidays', $container).find($(trigger)).trigger('click');
        });
    }

    DaysOff.prototype.options = {
        staff_id: -1,
        l10n: {}
    };

    window.BooklyStaffDaysOff = DaysOff;
})(jQuery);