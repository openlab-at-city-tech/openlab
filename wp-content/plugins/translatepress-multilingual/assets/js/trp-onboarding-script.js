/**
 * TranslatePress Onboarding JavaScript
 * Handles interactive functionality for the onboarding process
 */

jQuery(document).ready(function() {
    'use strict';

    currentOnboardingPage();
    initializeSelect2();
    addLanguage();
    removeLanguage();
    setSlug();
});

function initializeSelect2() {
    jQuery('.trp-select2').each(function() {
        // Destroy existing Select2 instance if it exists
        if (jQuery(this).hasClass('select2-hidden-accessible')) {
            jQuery(this).select2('destroy');
        }

        jQuery(this).select2();
    });
}
function addLanguage() {
    jQuery('#trp-add-language').on('click', function(e) {
        e.preventDefault();

        var maxLanguages = parseInt(trp_onboarding_vars.trp_secondary_languages, 10);
        var $addButton = jQuery(this);
        var currentLanguages = jQuery('.trp-additional-language').length;
        var currentErrors = jQuery('.trp-extra-languages-error').length;

        if(currentLanguages == 1 && currentErrors == 1){
            return;
        }

        // Remove any previous message
        jQuery('.trp-extra-languages-error').remove();

        if (currentLanguages >= maxLanguages) {
            var templateContentError = jQuery(jQuery('#trp-languages-error').html()).hide();
            $addButton.before(templateContentError);
            templateContentError.slideDown(200);
            return;
        }

        var templateContent = jQuery(jQuery('#trp-add-language-template').html()).hide();
        $addButton.before(templateContent);
        templateContent.slideDown(200, function() {
            var newSelect = jQuery('.trp-select2').last();
            initializeSelect2();
            //newSelect.select2('open');
        });

        setSlug();
    });
}

function removeLanguage() {
    jQuery(document).ready(function() {
        jQuery(document).on('click', '.trp-remove-language', function(e) {
            e.preventDefault();
            const $container = jQuery(this).parent();
            $container.slideUp(200, function() {
                $container.remove(); // Remove after animation completes
                // Remove any previous message
                jQuery('.trp-extra-languages-error').slideUp(200);
            });
        });
    });
}

function setSlug() {
    jQuery(document).ready(function() {
        jQuery('.trp-select2').on('select2:select', function (e) {
            var selectedValue = e.params.data.id; // e.g. 'fr_FR'
            var baseSlug = selectedValue.split('_')[0]; // e.g. 'fr'
            var fallbackSlug = selectedValue.toLowerCase(); // e.g. 'fr_fr'

            var $slugInput = jQuery(this)
                .closest('.trp-language-wrap')
                .find('.trp-slug-field input');

            // Collect slugs from all other inputs, excluding the current one
            var existingSlugs = [];
            jQuery('.trp-slug-field input').each(function () {
                if (this !== $slugInput[0]) {
                    existingSlugs.push(jQuery(this).val());
                }
            });

            // Use fallback if base slug already exists elsewhere
            var finalSlug = existingSlugs.includes(baseSlug) ? fallbackSlug : baseSlug;

            $slugInput.attr('name', 'url_slugs['+selectedValue+']');
            $slugInput.val(finalSlug);
        });
    });
}

function currentOnboardingPage(){
    jQuery(document).ready(function() {
        var current_url = jQuery(location).attr('href');

        jQuery('.trp-nav-onboarding-dot').each( function () {
            if (jQuery(this).attr('href') === current_url) {
                jQuery(this).parent().prevAll().children('a').css('background', '#72BCFA');

                jQuery(this).css('background', '#2271B1');
             }
        });

        var params = new URLSearchParams(window.location.search);
        var step = params.get("step");

        if ( step == 'finish' ){
            jQuery('.trp-nav-onboarding-dot').css( 'background', '#2271B1' );
        }
    });
}