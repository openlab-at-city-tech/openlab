jQuery(function ($) {
    var $sort = $('#bookly-shop-sort'),
        $shop = $('#bookly-shop'),
        $loading = $('#bookly-shop-loading'),
        $template = $('#bookly-shop-template')
    ;
    $('.bookly-js-select').booklySelect2({
        width: '100%',
        theme: 'bootstrap4',
        dropdownParent: '#bookly-tbs',
        allowClear: true,
        minimumResultsForSearch: -1
    });
    $sort.on('change', function () {
        $loading.show();
        $shop.hide();
        $.ajax({
            url     : ajaxurl,
            type    : 'GET',
            data    : {
                action    : 'bookly_get_shop_data',
                csrf_token: BooklyL10nGlobal.csrf_token,
                sort      : $sort.val()
            },
            dataType: 'json',
            success : function (response) {
                if (response.data.shop.length) {
                    $shop.html('');
                    $.each(response.data.shop, function (id, plugin) {
                        var rating = '';
                        for (var i = 0; i < 5; i++) {
                            if (plugin.rating - i > 0.5) {
                                rating += '<i class="fas fa-fw fa-star"></i>';
                            } else if (plugin.rating - i > 0) {
                                rating += '<i class="fas fa-fw fa-star-half-alt"></i>';
                            } else {
                                rating += '<i class="far fa-fw fa-star"></i>';
                            }
                        }
                        $shop.append(
                            $template.clone().show().html()
                                .replace(/{{rating}}/g, rating)
                                .replace(/{{(.+?)}}/g, function (match) {
                                    return plugin[match.substring(2, match.length - 2)];
                                })
                        );
                    });

                }
                $shop.show();
                $loading.hide();
            }
        });
    }).trigger('change');
});