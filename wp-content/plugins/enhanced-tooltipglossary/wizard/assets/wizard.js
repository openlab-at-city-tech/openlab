jQuery(document).ready(function ($) {
    const steps = $('.cm-wizard-step');

    // Next step button click event
    $('.next-step').on('click' , function () {
        const currentStep = parseInt($(this).data('step'));
        steps.eq(currentStep).hide();
        steps.eq(currentStep + 1).show();
    });

    // Previous step button click event
    $('.prev-step').on('click' , function () {
        const currentStep = parseInt($(this).data('step'));
        steps.eq(currentStep).hide();
        steps.eq(currentStep - 1).show();
    });

    // Step link click event
    $('.cm-wizard-menu li').on('click' , function () {
        const currentStep = parseInt($(this).data('step'));
        steps.hide();
        steps.eq(currentStep).show();
    });

    $('.finish').on('click', function (e) {
        if ($('form').length == 0) {
            return;
        }
        let formData = $('form').serialize();
        $.ajax({
            url: wizard_data.ajaxurl , // WordPress AJAX URL
            type: 'POST' ,
            data: {
                action: 'cmtt_save_wizard_options' , // Action name for the AJAX handler
                data: formData
            } ,
            success: function (response) {
            } ,
            error: function () {
                alert("An error occurred while saving options.");
            }
        });
    });


    $('.next-step').on('click' , function () {
        // Serialize form data
        let form = $(this).closest('.cm-wizard-step').find('form');

        if (form.length == 0) {
            return;
        }

        let formData = form.serialize();

        // AJAX call to save options
        $.ajax({
            url: wizard_data.ajaxurl , // WordPress AJAX URL
            type: 'POST' ,
            data: {
                action: 'cmtt_save_wizard_options' , // Action name for the AJAX handler
                data: formData
            } ,
            success: function (response) {
            } ,
            error: function () {
                alert("An error occurred while saving options.");
            }
        });
    });

    var $body = $('body');

    $body.on('mouseenter' , '.cm_field_help' , function () {
        if ($(this).find('.cm_field_help--wrap').length > 0) {
            return;
        }
        var helpHtml = $(this).attr('data-title');
        var $helpItemWrapHeight = "style='min-height:" + $(this).parent().outerHeight() + "px'";
        var $helpItemWrap = $("<div class='cm_field_help--wrap'" + $helpItemWrapHeight + "><div class='cm_field_help--text'></div></div>");

        $(this).append($helpItemWrap);

        var $helpItemText = $(this).find('.cm_field_help--text');
        $helpItemText.html(helpHtml);
        $helpItemText.html($helpItemText.text());

        setTimeout(function () {
            $helpItemWrap.addClass('cm_field_help--active');
        } , 300);
    }).on('mouseleave' , '.cm_field_help' , function () {
        var $helpItem = $(this).find('.cm_field_help--wrap');
        setTimeout(function () {
            $helpItem.removeClass('cm_field_help--active');
        } , 600);
        setTimeout(function () {
            $helpItem.remove();
        } , 800);

    });
});