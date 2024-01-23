(function ($, rules, _) {

    $(document).ready(function () {
        var styleSheet = $('#academia-custom-css').length ? $('#academia-custom-css')[0].sheet : undefined;

        _.each(rules['color-rules'], function (current) {
            wp.customize(current.id, function (value) {

                value.bind(function (newval) {
                    var myObj = {};
                    myObj[current.rule] = newval;
                    vein.inject(
                        current.selector.split(','),
                        myObj,
                        {'stylesheet': styleSheet}
                    );
                });
            });
        });

    });
})(jQuery, academia_css_rules, _, vein);