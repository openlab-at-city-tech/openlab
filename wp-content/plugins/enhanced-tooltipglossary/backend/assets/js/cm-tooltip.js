
(function ($) {

    $(document).ready(function () {

        $('#tabs').tabs();

        $('.cmtt_field_help_container').each(function () {
            var newElement,
                    element = $(this);

            newElement = $('<div class="cmtt_field_help"></div>');
            newElement.attr('title', element.html());

            if (element.siblings('th').length)
            {
                element.siblings('th').append(newElement);
            }
            else
            {
                element.siblings('*').append(newElement);
            }
            element.remove();
        });

        $('.cmtt_field_help').tooltip({
            show: {
                effect: "slideDown",
                delay: 100
            },
            position: {
                my: "left top",
                at: "right top"
            },
            content: function () {
                var element = $(this);
                return element.attr('title');
            },
            close: function (event, ui) {
                ui.tooltip.hover(
                        function () {
                            $(this).stop(true).fadeTo(400, 1);
                        },
                        function () {
                            $(this).fadeOut("400", function () {
                                $(this).remove();
                            });
                        });
            }
        });

    });

})(jQuery);