/*
 * Script used in Settings Page
 */

jQuery( function() {

    /**
     * Change the language selector and slugs
     */
    function TRP_Settings_Language_Selector() {
        var _this = this;
        var duplicate_url_error_message;
        var iso_codes;
        var error_handler;

        /**
         * Initialize select to become select2
         */
        this.initialize_select2 = function () {
            jQuery('.trp-select2').each(function () {
                var select_element = jQuery(this);
                select_element.select2(/*arguments*/);
            });
        };

        this.get_default_url_slug = function( new_language ){
            var return_slug = iso_codes[new_language];
            var url_slugs = _this.get_existing_url_slugs();
            url_slugs.push( return_slug );
            if ( has_duplicates ( url_slugs ) ){
                return_slug = new_language;
            }
            return return_slug.toLowerCase();
        };

        this.error_check = function( new_language ){
            error_handler.show_hide_warning( new_language, true );

            if ( error_handler.has_error === true ){
                return true;
            }

            error_handler.languages.push( new_language );

            return false;
        };

        this.add_language = function(){
            var selected_language = jQuery( '#trp-select-language' );
            var new_language = selected_language.val();

            if ( new_language == "" ){
                return;
            }

            if ( jQuery( "#trp-languages-table .trp-language" ).length >= 2 && jQuery( '.trp-language-selector-limited' ).length ){
                jQuery(".trp-upsell-multiple-languages").show('fast');
                return;
            }

            if ( _this.error_check( new_language ) === true ){
                return;
            }

            selected_language.val( '' ).trigger( 'change' );

            var new_option = jQuery( '.trp-language' ).first().clone();

            _this.supports_formality( new_language, new_option );

            error_handler.add_language_change_listener( new_option.find('.trp-translation-language') );

            new_option = jQuery( new_option );

            new_option.find('.trp-translation-language').on( 'change', _this.change_language );

            new_option.find( '.trp-hidden-default-language' ).remove();
            new_option.find( '.select2-container' ).remove();
            var select = new_option.find( 'select.trp-translation-language' );
            select.removeAttr( 'disabled' );
            select.find( 'option' ).each(function(index, el){
                el.text = el.text.replace('Default: ', '');
            })

            select.val( new_language );
            select.select2();

            var checkbox = new_option.find( 'input.trp-translation-published' );
            checkbox.removeAttr( 'disabled' );
            checkbox.val( new_language );

            var url_slug = new_option.find( 'input.trp-language-slug' );
            url_slug.val( _this.get_default_url_slug( new_language ) );
            url_slug.attr('name', 'trp_settings[url-slugs][' + new_language + ']' );

            var language_code = new_option.find( 'input.trp-language-code' );
            language_code.val( new_language);

            var remove = new_option.find( '.trp-remove-language__container' ).toggle();

            new_option = jQuery( '#trp-sortable-languages' ).append( new_option );
            new_option.find( '.trp-remove-language__container' ).last().click( _this.remove_language );

            jQuery( '#trp-new-language.trp-add-new-language-row' ).remove();
        };

        this.change_language = function( event ){
            var new_language_element          = jQuery(event.target).closest( '.trp-language' );
            var new_language_code             = jQuery(event.target).next().find('.select2-selection__rendered').attr('title');

            _this.supports_formality( new_language_code, new_language_element );
        }

        this.remove_language = function( element ){
            var message = jQuery( '.trp-remove-language' ).attr( 'data-confirm-message' );
            var confirmed = confirm( message );

            if ( confirmed ) {
                let language_to_remove = jQuery( element.target ).closest('.trp-language');
                let language_to_remove_code = language_to_remove.find('.trp-language-code').val();

                // remove language from array
                error_handler.languages.splice( error_handler.languages.indexOf(language_to_remove_code), 1 ) ;

                language_to_remove.remove();

                error_handler.show_hide_warning( language_to_remove_code );
            }

        };

        this.update_default_language = function(){
            var selected_language = jQuery( '#trp-default-language').val();
            jQuery( '.trp-hidden-default-language' ).val( selected_language );
            jQuery( '.trp-translation-published[disabled]' ).val( selected_language );
            jQuery( '.trp-translation-language[disabled]').val( selected_language ).trigger( 'change' );
        };

        function has_duplicates(array) {
            var valuesSoFar = Object.create(null);
            for (var i = 0; i < array.length; ++i) {
                var value = array[i];
                if (value in valuesSoFar) {
                    return true;
                }
                valuesSoFar[value] = true;
            }
            return false;
        }

        this.get_existing_url_slugs = function(){
            var url_slugs = [];
            jQuery( '.trp-language-slug' ).each( function (){
                url_slugs.push( jQuery( this ).val().toLowerCase() );
            } );
            return url_slugs;
        };

        this.check_unique_url_slugs = function (event){
            var url_slugs = _this.get_existing_url_slugs();
            if ( has_duplicates(url_slugs)){
                alert( duplicate_url_error_message );
                event.preventDefault();
            }
        };

        this.update_url_slug_and_status = function ( event ) {
            var select = jQuery( event.target );
            var new_language = select.val();
            var row = jQuery( select ).parents( '.trp-language' ) ;
            row.find( '.trp-language-slug' ).attr( 'name', 'trp_settings[url-slugs][' + new_language + ']').val( '' ).val( _this.get_default_url_slug( new_language ) );
            row.find( '.trp-language-code' ).val( '' ).val( new_language );
            row.find( '.trp-translation-published' ).val( new_language );
        };

        this.supports_formality = function( new_language_code, new_language_element ){
            var languages_that_support_formality = trp_url_slugs_info['languages_that_support_formality'];
            var formality_match                  = new_language_code.match( /formal|informal/ ) !== null ? new_language_code.match( /formal|informal/ )[0] : false; // check if the language is innately formal/informal e.g. de_DE_formal
            var formality_select_field           = new_language_element.find( '.trp-translation-language-formality' );
            var stripped_formal_language         = error_handler.strip_formal_language( new_language_code );

            if ( formality_select_field.length === 0 ){
                return;
            }

            formality_select_field.removeClass( 'trp-formality-disabled' ); // when a language is added,  the fields are cloned - which means that the select field could have the .trp-formality-disabled class even if the language supports formality

            if ( stripped_formal_language && languages_that_support_formality[ stripped_formal_language ] === 'true' ){
                select_change( formality_match );
                return;
            }

            if ( !languages_that_support_formality[new_language_code] || languages_that_support_formality[new_language_code] === 'false' ){
                formality_select_field.addClass( 'trp-formality-disabled' );
            }

            select_change( 'default' );

            function select_change( option_value ) {
                formality_select_field.find( 'option' ).each( function () {

                    if ( jQuery( this ).attr( 'value' ) === option_value ){
                        jQuery( this ).attr( 'selected', 'selected' );
                        return;
                    }

                    jQuery( this ).removeAttr( 'selected' );

                } );
            }

        }

        var initialize = new TRP_Advanced_Settings_Tabs();
        initialize.init();

        this.initialize = function () {
            this.initialize_select2();

            error_handler = new TRP_Error_handler();
            duplicate_url_error_message = trp_url_slugs_info['error_message_duplicate_slugs'];
            iso_codes = trp_url_slugs_info['iso_codes'];

            jQuery( '#trp-sortable-languages' ).sortable({ handle: '.trp-sortable-handle' });
            jQuery( '#trp-add-language' ).click( _this.add_language );
            jQuery('.trp-remove-language__container:not(.trp-adst-remove-element)').click(_this.remove_language);
            jQuery( '#trp-default-language' ).on( 'change', _this.update_default_language );
            jQuery( "form[action='options.php']").on ( 'submit', _this.check_unique_url_slugs );
            jQuery( '#trp-languages-table' ).on( 'change', '.trp-translation-language', _this.update_url_slug_and_status );
            jQuery('.trp-language .trp-select2').not( '#trp-default-language' ).on( 'change', _this.change_language );
            jQuery( '.trp-select2' ).on( 'select2:open', function(){
                document.querySelector( '.select2-search__field' ).focus();
            });
        };

        this.initialize();
    }

    /*
     * Manage adding and removing items from an option of tpe list from Advanced Settings page
     */
    function TRP_Advanced_Settings_List( table ){

        var _this = this

        this.addEventHandlers = function( table ){
            var add_list_entry = table.querySelector( '.trp-add-list-entry' );

            // add event listener on ADD button
            add_list_entry.querySelector('.trp-adst-button-add-new-item').addEventListener("click", _this.add_item );

            var removeButtons = table.querySelectorAll( '.trp-adst-remove-element' );
            for( var i = 0 ; i < removeButtons.length ; i++ ) {
                removeButtons[i].addEventListener("click", _this.remove_item)
            }
        }

        this.remove_item = function( event ){
            if ( confirm( document.querySelector('.trp-adst-remove-element-text').getAttribute( 'data-confirm-message' ) ) ){
                jQuery( event.target ).closest( '.trp-list-entry' ).remove()
            }
        }

        this.add_item = function () {
            var add_list_entry = table.querySelector( '.trp-add-list-entry' );
            var clone = add_list_entry.cloneNode(true)

            // Remove the trp-add-list-entry class from the second element after it was cloned
            add_list_entry.classList.remove('trp-add-list-entry');

            // Show Add button, hide Remove button
            add_list_entry.querySelector( '.trp-adst-button-add-new-item' ).style.display = 'none'
            add_list_entry.querySelector( '.trp-adst-remove-element' ).parentNode.style.display = 'block'

            // Design change to add the cloned element at the bottom of list
            // Done becasue the select box element cannot be cloned with its selected state
            var itemInserted =  add_list_entry.parentNode.insertBefore(clone, add_list_entry.nextSibling);

            // Set name attributes
            var dataNames = add_list_entry.querySelectorAll( '[data-name]' )
            for( var i = 0 ; i < dataNames.length ; i++ ) {
                dataNames[i].setAttribute( 'name', dataNames[i].getAttribute('data-name') );
            }

            var removeButtons = table.querySelectorAll( '.trp-adst-remove-element' );
            for( var i = 0 ; i < removeButtons.length ; i++ ) {
                removeButtons[i].addEventListener("click", _this.remove_item)
            }

            // Reset values of textareas with new items
            var dataValues = clone.querySelectorAll( '[data-name]' )
            for( var i = 0 ; i < dataValues.length ; i++ ) {
                dataValues[i].value = ''
            }

            //Restore checkbox(es) values after cloning and clearing; alternative than excluding from reset
            var restoreCheckboxes = clone.querySelectorAll ( 'input[type=checkbox]' )
            for( var i = 0 ; i < restoreCheckboxes.length ; i++ ) {
                restoreCheckboxes[i].value = 'yes'
            }

            // Add click listener on new row's Add button
            var addButton = itemInserted.querySelector('.trp-adst-button-add-new-item');
            addButton.addEventListener("click", _this.add_item );
        }

        _this.addEventHandlers( table )
    }
    var trpSettingsLanguages = new TRP_Settings_Language_Selector();

    jQuery('#trp-default-language').on("select2:selecting", function(e) {
        jQuery(".trp-settings-warning").show('fast');
    });

    /*
     * Automatic Translation Page
     */


    /** Backwards compatibility for when Pro version was not updated -- Tp Settings redesign */
    function toggleDeeplElements__compat(show = true) {
        const optionsContainer = document.querySelector('.trp-settings-options__wrapper .trp-to-hide');

        // Wrap the given text in a span first due to it not having a tag
        function wrapTextNode(text, className) {
            document.querySelectorAll('.trp-settings-options__wrapper .trp-to-hide').forEach(el => {
                el.childNodes.forEach(node => {
                    if (node.nodeType === 3 && node.nodeValue.includes(text)) {
                        // Check if the text is already wrapped by seeing if its parent is a <span> with the class
                        if (node.parentNode && node.parentNode.classList?.contains(className)) {
                            return; // Already wrapped, so do nothing
                        }

                        // Create a <span> and wrap the text
                        let span = document.createElement("span");
                        span.textContent = node.nodeValue;
                        span.classList.add(className);
                        node.replaceWith(span);
                    }
                });
            });
        }

        wrapTextNode("DeepL API Type", "trp-deepl-api-type-text");

        const deeplApiTypeText  = optionsContainer.querySelector('.trp-deepl-api-type-text');

        if ( !optionsContainer || !deeplApiTypeText ) return;

        const labelProType      = document.querySelector('label[for="trp-deepl-api-type-pro"]');
        const labelFreeType     = document.querySelector('label[for="trp-deepl-api-type-free"]');
        const deeplApiKeyText   = document.querySelector('h4');
        const deeplApiKeyInput  = document.querySelector('#trp-deepl-key');
        const apiKeyDescription = deeplApiKeyInput.nextElementSibling;
        const deeplDescription = labelFreeType?.nextElementSibling; // Safe check
        function toggleElement(el, show) {
            if (el)
                el.style.display = show ? "" : "none";
        }

        // Toggle elements
        toggleElement(deeplApiTypeText, show);
        toggleElement(labelProType, show);
        toggleElement(labelFreeType, show);
        toggleElement(deeplApiKeyText, show);
        toggleElement(deeplDescription, show);
        toggleElement(deeplApiKeyInput, show);
        toggleElement(apiKeyDescription, show);
    }

    // Hide API Fields Based on Selected Translation Engine
    jQuery('#trp-translation-engines').on('change', function (){
        jQuery('.trp-engine').hide();

        toggleDeeplElements__compat(false);

        // backwards compatibility for when Paid version not updated. Deepl missing .trp-engine and #deepl selectors in html
        jQuery("#trp-deepl-api-type-pro").closest('tr').hide();
        jQuery("#trp-deepl-key").closest('tr').hide();

        jQuery( '#trp-test-api-key' ).show(); // initiate the default so we can hide for deepl_upsell
        if (jQuery(this).val() == 'deepl_upsell'){
            jQuery( '#trp-test-api-key' ).hide()
        }

        if ( jQuery("#" + this.value).length > 0 ){
            jQuery("#" + this.value).show();
            TRP_check_visible_engine_for_upsale_mtapi( this.value );

        }else{
            // backwards compatibility for when Paid version not updated. Deepl missing .trp-engine and #deepl selectors in html
            jQuery("#trp-deepl-api-type-pro").closest('tr').show();
            jQuery("#trp-deepl-key").closest('tr').show();

            toggleDeeplElements__compat(true);
        }
    })



    // Used for the main machine translation toggle to show/hide all options below it
    function TRP_show_hide_machine_translation_options(){
        if( jQuery( '#trp-machine-translation-enabled' ).val() != 'yes' )
            jQuery( '.trp-machine-translation-options tbody tr:not(:first-child)').hide()
        else
            jQuery( '.trp-machine-translation-options tbody tr:not(:first-child)').show()

        if( jQuery( '#trp-machine-translation-enabled' ).val() == 'yes' )
            jQuery('.trp-translation-engine:checked').trigger('change')
    }

    TRP_show_hide_machine_translation_options()
    jQuery('#trp-machine-translation-enabled').on( 'change', function(){
        TRP_show_hide_machine_translation_options()
    })

    function TRP_check_visible_engine_for_upsale_mtapi( selected_engine ) {

        jQuery('#tpai-upsale').hide();
        jQuery( '.trp-upsale-fill' ).hide();

        if (selected_engine == 'mtapi' && jQuery('.trp-upsale-fill').attr('id') === 'TranslatePress') {
            jQuery('#tpai-upsale').show();
            jQuery( '.trp-upsale-fill' ).show();
        } else {
            jQuery('#tpai-upsale').hide();
            jQuery( '.trp-upsale-fill' ).hide();

        }
    }

    function TRP_test_API_key(){
        const testPopupOverlay = document.querySelector( '.trp-test-api-key-popup-overlay');
        const testPopup        = document.querySelector( '.trp-test-api-key-popup' );

        if ( !testPopup )
            return;

        const testApiBtn = document.querySelector( '#trp-test-api-key button' );

        const closePopup = document.querySelector( '.trp-test-api-key-close-btn' );

        closePopup.addEventListener( "click", function () {
            testPopupOverlay.style.visibility = "hidden";
            testPopup.style.visibility = "hidden";
        });

        testPopupOverlay.addEventListener( "click", function () {
            if ( testPopup.style.visibility === 'hidden' )
                return;

            testPopupOverlay.style.visibility = "hidden";
            testPopup.style.visibility = "hidden";
        });

        testPopup.addEventListener( "click", function (event) {
            event.stopPropagation();
        });


        if ( typeof testApiBtn !== 'undefined' && testApiBtn ) {
            testApiBtn.addEventListener("click", function () {
                testPopupOverlay.style.visibility = 'visible';

                jQuery('.trp-loading-spinner').show();

                jQuery.ajax({
                    url: trp_url_slugs_info['admin-ajax'],
                    type: 'POST',
                    data: {
                        action: 'test_api_key',
                        security: document.querySelector('#trp_test_api_nonce_field').value
                    },
                    success: function (response) {
                        if (response.success) {
                            testPopup.style.visibility = 'visible';

                            populatePopup(response.data);
                        } else {
                            console.error("Error:", response.data.message);
                        }
                    },
                    error: function () {
                        console.error("AJAX request failed");
                    },
                    complete: function () {
                        jQuery('.trp-loading-spinner').hide();
                    }
                });
            });
        }

        function populatePopup( response ){
            const popupResponse = testPopup.querySelector('.trp-test-api-key-response .trp-settings-container');
            const popupResponseBody = testPopup.querySelector('.trp-test-api-key-response-body .trp-settings-container');
            const popupResponseFull = testPopup.querySelector('.trp-test-api-key-response-full .trp-settings-container');

            popupResponse.textContent     = JSON.stringify( response.response.response );
            popupResponseBody.textContent = response.response.body;
            popupResponseFull.textContent = response.raw_response;
        }
    }
    TRP_test_API_key();

    // check quota for site on TP AI
    function TRP_TP_AI_Recheck(){
        jQuery( '#trp-refresh-tpai-text-recheck').hide()
        jQuery( '#trp-refresh-tpai-text-rechecking').show()
        jQuery.ajax({
            url: trp_url_slugs_info['admin-ajax'],
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'trp_ai_recheck_quota',
                nonce: trp_url_slugs_info['trp-tpai-recheck-nonce']
            },
            success: function (response) {
                if( response.hasOwnProperty('quota') ){
                    jQuery( '#trp-ai-quota-number').text( Number(response.quota).toLocaleString('en-US') )
                    if ( !response.quota ){
                        jQuery( '#trp-refresh-tpai-text-recheck').finish().delay(1700).fadeIn("fast")
                    }else{
                        jQuery( '#trp-refresh-tpai').finish().delay(1100).fadeOut("slow")
                    }
                }
                jQuery( '#trp-refresh-tpai-text-done').finish().fadeIn("fast").delay(1000).fadeOut("slow")
                jQuery( '#trp-refresh-tpai-text-recheck').hide()
                jQuery( '#trp-refresh-tpai-text-rechecking').hide()

                trp_ai_recheck_in_progress = false
            },
            error: function (response) {
                jQuery( '#trp-refresh-tpai-text-done').finish().fadeIn("fast").delay(1000).fadeOut("slow");
                jQuery( '#trp-refresh-tpai-text-rechecking').hide()
                jQuery( '#trp-refresh-tpai-text-recheck').finish().delay(1700).fadeIn("fast")
                trp_ai_recheck_in_progress = false
            }
        })
    }

    // Enable target field only if checkbox is checked
    function trp_enable_disable_conditional_field( targetField, checkboxField ){
        const checkbox = document.querySelector( checkboxField );
        const target = document.querySelector( targetField );

        if ( !checkbox )
            return;

        function toggleInput() {
            if (target.matches("input, select, textarea, button")) {
                target.disabled = !checkbox.checked;
            } else {
                const inputs = target.querySelectorAll("input, select, textarea, button");
                inputs.forEach(input => {
                    input.disabled = !checkbox.checked;
                })
            }
        }

        checkbox.addEventListener("change", toggleInput);
        toggleInput();
    }
    trp_enable_disable_conditional_field( '#trp-machine-translation-limit','#trp-machine-translation-limit-toggle' );
    trp_enable_disable_conditional_field( 'select[name="trp_advanced_settings[enable_hreflang_xdefault]"]','#enable_hreflang_xdefault-checkbox' );
    trp_enable_disable_conditional_field( 'div.trp-input-array-rows__wrapper','#language_date_format' );


    trp_ai_recheck_in_progress = false
    jQuery("#trp-refresh-tpai").on('click', function(){
        if ( !trp_ai_recheck_in_progress ){
            trp_ai_recheck_in_progress = true
            TRP_TP_AI_Recheck()
        }
    })

    /*
    * END Automatic Translation Page
    */

    // Options of type List adding, from Advanced Settings page
    var trpListOptions = document.querySelectorAll( '.trp-adst-list-option' );
    for ( var i = 0 ; i < trpListOptions.length ; i++ ){
        new TRP_Advanced_Settings_List( trpListOptions[i] );
    }

});

//Advanced Settings Tabs
function TRP_Advanced_Settings_Tabs() {
    function init() {
        if (!window.location.search.includes('trp_advanced_page')) return;

        jQuery('.trp-settings-container').hide();

        // Backwards compatibility for old Pro versions. We did not have trp-settings-containers then
        const aldSettingsContainer = document.querySelector('.advanced_settings_class.ald_settings');

        let navItems = document.querySelectorAll(".trp_advanced_tab_content_table_item");
        let containers = !aldSettingsContainer ?
            document.querySelectorAll(".trp-settings-container") :
            [...document.querySelectorAll(".trp-settings-container"), aldSettingsContainer];

        let settingsReferer = document.querySelector("#trp_advanced_settings_referer"); // Hidden input field

        function getURLParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }

        function updateURLParameter(param, value) {
            let url = new URL(window.location.href);
            url.searchParams.set(param, value);
            window.history.replaceState(null, "", url.toString());
        }

        function showTargetContainer(targetClass) {
            containers.forEach(container => container.style.display = "none");

            let targetContainers = targetClass === 'ald_settings' && aldSettingsContainer ?
                document.querySelectorAll('.advanced_settings_class.ald_settings') :
                document.querySelectorAll(`.trp-settings-container-${targetClass}`);

            if (targetContainers.length > 0) {
                targetContainers.forEach(container => container.style.display = "block");
            }

            document.querySelectorAll(".trp_advanced_tab_content_table_item").forEach(el => {
                el.classList.remove("trp-nav-active");
            });

            let activeNavItem = document.querySelector(`.trp_advanced_tab_content_table_item a.${targetClass}`);
            if (activeNavItem) {
                activeNavItem.closest(".trp_advanced_tab_content_table_item").classList.add("trp-nav-active");
            }
        }

        navItems.forEach(item => {
            item.addEventListener("click", function (event) {
                event.preventDefault();

                let targetClass = this.querySelector("a").classList[0];

                updateURLParameter("tab", targetClass);
                if (settingsReferer) settingsReferer.value = targetClass;
                showTargetContainer(targetClass);
            });
        });

        // On page load, check for &tab= in URL or in the hidden input
        let activeTab = getURLParameter("tab") || (settingsReferer ? settingsReferer.value : null);

        if (activeTab) {
            showTargetContainer(activeTab);
        } else {
            let firstNavItem = navItems[0]?.querySelector("a");
            if (firstNavItem) {
                let firstTargetClass = firstNavItem.classList[0];
                updateURLParameter("tab", firstTargetClass);
                if (settingsReferer) settingsReferer.value = firstTargetClass;
                showTargetContainer(firstTargetClass);
            }
        }
    }

    return {
        init: init
    }
}

function TRP_Error_handler() {

    this.has_error = false;
    this.languages = [];
    let _this = this;
    let $error_container;
    let error_type;

    this.init = function () {
        $error_container = jQuery('.trp-add-language-error-container');
        this.set_language_list();
        this.init_event_listeners();
    }

    this.set_language_list = function () {
        let language_nodes = document.querySelectorAll('.trp-language .trp-language-code');

        for (let i = 0; i < language_nodes.length; i++) {
            this.languages[i] = language_nodes[i].value;
        }

    }

    // If the language is formal / informal, returns it but stripped of the _informal or _formal parts
    // Returns false otherwise
    this.strip_formal_language = function (new_language_code) {
        let formality_map = {
            _informal: '',
            _formal: ''
        };

        if (new_language_code.includes('formal') || new_language_code.includes('informal')) {
            new_language_code = new_language_code.replace(/_formal|_informal/, function (matched) {
                return formality_map[matched];
            });

            return new_language_code;
        }

        return false;
    }

    this.has_formal_variant = function (new_language_code, languages_array) {

        for (let language of languages_array) {
            let stripped_formal_language = this.strip_formal_language(language); // false if is not a formal language

            if (stripped_formal_language && stripped_formal_language === new_language_code) {
                return true;
            }
        }

        return false;
    }

    this.set_error_type = function (new_language_code, is_new_language_added) {
        let languages_array = is_new_language_added ? [].concat(this.languages, new_language_code) : this.languages;

        if (languages_array.length !== new Set(languages_array).size) {
            error_type = "duplicates";
            return true;
        }

        for (let language_code of languages_array) {
            let stripped_formal_language = this.strip_formal_language(language_code);

            if (stripped_formal_language !== false && languages_array.includes(stripped_formal_language) || this.has_formal_variant(language_code, languages_array)) {
                error_type = "formality";
                return true;
            }
        }

        return false;
    }

    this.change_warning_text = function () {
        let error_container_text;

        switch (error_type) {
            case 'formality':
                error_container_text = trp_url_slugs_info['error_message_formality'];
                break;

            case 'duplicates':
                error_container_text = trp_url_slugs_info['error_message_duplicate_languages'];
                break;
        }

        $error_container.html(error_container_text);
    }

    // Displays the warning message with the relevant text in case there is an error
    // Or hides the warning message in case it was resolved
    this.show_hide_warning = function (new_language_code, is_new_language_added = false) {
        this.has_error = this.set_error_type(new_language_code, is_new_language_added);

        if (this.has_error !== false) {
            this.change_warning_text();
            $error_container.show('fast');
        }

        if (this.has_error === false && $error_container.is(':visible')) {
            $error_container.hide('fast');
        }

    }

    this.init_event_listeners = function () {
        let language_nodes = document.querySelectorAll('.trp-language .trp-select2');
        this.add_language_change_listener(language_nodes);
    }

    this.add_language_change_listener = function (nodes) {
        let $nodes = jQuery(nodes);

        $nodes.on('change', language_change);

        function language_change(event) {
            // .trp-language-code is changed after the language changes so there is a small window in which we can get the old value
            let old_language_code = jQuery(event.target).closest('.trp-language').find('.trp-language-code').val();
            let new_language_code = jQuery(event.target).next().find('.select2-selection__rendered').attr('title');

            _this.languages[_this.languages.indexOf(old_language_code)] = new_language_code;

            _this.show_hide_warning(new_language_code);
        }
    }

    this.init();
}

// TRP Email Course
jQuery(document).ready(function (e) {
    jQuery('.trp-email-course input[type="submit"]').on('click', function (e) {

        e.preventDefault()

        jQuery( '.trp-email-course .trp-email-course__error' ).removeClass( 'visible' )

        jQuery('.trp-email-course input[type="submit"]').addClass( 'disabled' )

        var email = jQuery( '.trp-email-course input[name="trp_email_course_email"]').val()

        if ( !trp_validateEmail( email ) ){
            jQuery( '.trp-email-course .trp-email-course__error' ).addClass( 'visible' )
            jQuery( '.trp-email-course input[name="trp_email_course_email"]' ).focus()

            return
        }

        if( email != '' ){

            jQuery( '.trp-email-course input[type="submit"]' ).val( 'Working...' )

            var data = new FormData()
                data.append( 'email', email )

            var version = jQuery('.trp-email-course input[name="trp_installed_plugin_version"]').val()
            if ( version != '' )
                data.append( 'version', version )

            jQuery.ajax({
                url: 'https://translatepress.com/wp-json/trp-api/emailCourseSubscribe',
                type: 'POST',
                processData: false,
                contentType: false,
                data: data,
                success: function (response) {

                    if( response.message ){

                        jQuery( '.trp-email-course .trp-email-course__message').text( response.message ).addClass( 'visible' ).addClass( 'success' )
                        jQuery( '.trp-email-course .trp-email-course__form' ).hide()
                        jQuery( '.trp-email-course__footer' ).css( 'visibility', 'hidden' )

                        trp_dimiss_email_course()

                    }

                },
                error: function (response) {

                    jQuery('.trp-email-course input[type="submit"]').val('Sign me up!')

                }
            })

        }
    })
})

function trp_validateEmail(email) {

    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());

}