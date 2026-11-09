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
    detectProActivation();
});

/**
 * Detect TranslatePress Pro activation that happens on the WordPress "Add Plugins" page.
 *
 * The onboarding "Install TranslatePress Pro" links open the core plugins screen in a new tab.
 * Once the user clicks one of them we poll this step over the wire (htmx-style) and, as soon as the
 * Pro version is detected, swap in only the refreshed step markup instead of reloading the page.
 *
 * The next poll is scheduled only after the previous request resolves, so a slow response simply
 * pushes the following poll further out (requests never stack on top of each other).
 */
function detectProActivation() {
    var FLAG           = 'trpAwaitingProActivation';
    var COUNT          = 'trpProPollCount';
    var INTERVAL       = 4000; // ms between polls once we are waiting for activation
    var MAX_POLLS      = 90;   // safety cap (~6 minutes) so we never poll forever
    var POLLABLE_STEPS = ['languages', 'autotranslation', 'addons'];
    // Steps whose markup is too JS-heavy to swap in place (Select2, dynamic rows, ...): once Pro is
    // detected we reload the whole page once instead of swapping just the inner content.
    var RELOAD_STEPS   = ['languages'];
    var CONTENT        = '.trp-onboarding-content';

    var step = new URLSearchParams(window.location.search).get('step');
    var isPollableStep = POLLABLE_STEPS.indexOf(step) !== -1;

    var store;
    try {
        store = window.sessionStorage;
        store.getItem(FLAG); // throws in private-mode edge cases
    } catch (e) {
        return; // no session storage -> skip auto-refresh entirely
    }

    var timer = null;

    function isProMarkup(node) {
        return !!node && node.getAttribute('data-trp-is-pro') === '1';
    }

    function stopPolling() {
        store.removeItem(FLAG);
        store.removeItem(COUNT);
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
    }

    function scheduleNext() {
        if (!isPollableStep || store.getItem(FLAG) !== '1') {
            return;
        }
        var count = parseInt(store.getItem(COUNT) || '0', 10);
        if (count >= MAX_POLLS) {
            stopPolling();
            return;
        }
        timer = setTimeout(function () {
            store.setItem(COUNT, String(count + 1));
            checkForProActivation();
        }, INTERVAL);
    }

    // Fetch the current step and, once Pro is active, refresh only its inner markup.
    function checkForProActivation() {
        fetch(window.location.href, {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (response) { return response.text(); })
        .then(function (html) {
            var fresh = new DOMParser().parseFromString(html, 'text/html').querySelector(CONTENT);
            if (!fresh) {
                scheduleNext(); // unexpected response (e.g. expired session) -> keep waiting
                return;
            }
            if (isProMarkup(fresh)) {
                stopPolling();
                if (RELOAD_STEPS.indexOf(step) !== -1) {
                    window.location.reload(); // JS-heavy step: reload once to get a clean Pro render
                    return;
                }
                var current = document.querySelector(CONTENT);
                if (current) {
                    current.setAttribute('data-trp-is-pro', '1');
                    current.innerHTML = fresh.innerHTML; // refresh only the inner step content
                    initializeSelect2();                 // re-init any widgets in the new markup
                }
            } else {
                scheduleNext(); // still on the free version
            }
        })
        .catch(function () { scheduleNext(); });
    }

    // Start waiting for activation as soon as the user heads off to install Pro.
    // Delegated so the handler survives content swaps.
    jQuery(document).on('click', '.trp-install-pro-link', function () {
        store.setItem(FLAG, '1');
        store.setItem(COUNT, '0'); // restart the budget for each fresh install attempt
        scheduleNext();
    });

    // Resume (or finish) waiting after a real page load.
    if (store.getItem(FLAG) === '1') {
        if (isProMarkup(document.querySelector(CONTENT))) {
            stopPolling(); // already Pro: nothing to wait for
        } else {
            scheduleNext();
        }
    }
}

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