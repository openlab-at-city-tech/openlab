var joomunited_url = 'https://www.joomunited.com/';

jQuery(document).ready(function($){
    // Define jutranslation_ajax_action if doesn't exist
    if (typeof jutranslation.ajax_action === 'undefined') {
        jutranslation.ajax_action='';
    }

    //Load the available version from Joomunited
    $.each(julanguages, function(){
        var julanguage = this;
        $.ajax({
            url : joomunited_url + "index.php?option=com_jutranslation&task=translations.getTranslations",
            type : 'POST',
            data : julanguage,
            success : function(data){
                data = JSON.parse(data);

                $('#jutranslations-languages tbody tr[data-slug="'+ julanguage.extension + '"]').each(function(){
                    var lang = $(this).data('lang');
                    var slug = $(this).data('slug');

                    //Is this language one of Joomunited has translated
                    var found = false;

                    //For each translation found add the version and an install btn
                    $.each(data.data,function(){
                        if(this.language === lang && this.extension === slug){
                            found = true;
                            //Add new version availability information
                            var html;
                            switch (versionCompare(julanguage.versions[this.language], this.extension_version, julanguage.revisions[this.language], this.revision)) {
                                case 1:
                                    html = 'You already have the newer version<br/>The latest version on Joomunited is ' + this.extension_version + ' rev'+this.revision + ' <a class="jutranslation-install" href="#" data-extension="' + this.extension + '" data-id="' + this.id + '">Install this version</a>';
                                    break;
                                case 0:
                                    html = 'You already have the latest version <a class="jutranslation-install" href="#" data-extension="' + this.extension + '" data-id="' + this.id + '">Reinstall</a>';
                                    break;
                                case -1:
                                    if (julanguage.versions[this.language]) {
                                        install_update = 'Update';
                                    } else {
                                        install_update = 'Install';
                                    }

                                    html = 'There is a new version of language file available v' + this.extension_version + ' rev'+this.revision + ' <a class="jutranslation-install" href="#" data-extension="' + this.extension + '" data-id="' + this.id + '">' + install_update + '</a>';
                            }
                            $('#jutranslations-languages tr[data-lang="'+ this.language + '"][data-slug="'+ julanguage.extension + '"] td .latest_version').html(html);

                            //Initialize all installation button
                            $('.jutranslation-install').unbind().bind('click',function(e){
                                e.preventDefault();

                                var $clicked_button = $(this);
                                var $parent_line = $clicked_button.parents('tr');
                                var slug = $clicked_button.parents('tr').data('slug');

                                //Get from Joomunited the translation
                                $.ajax({
                                    url : joomunited_url + "index.php?option=com_jutranslation&task=translations.download",
                                    type : 'POST',
                                    data : {
                                        extension : $clicked_button.data("extension"),
                                        translation : $clicked_button.data("id")
                                    },
                                    success : function(result){
                                        result = JSON.parse(result);

                                        //Do not show language columns if it's en-US
                                        var show_reference = true;
                                        if(result.datas.language==='en-US'){
                                            show_reference = false;
                                        }

                                        var table_option = {
                                            language : result.datas.language,
                                            confirm_button_action : function(e, $_content, close){
                                                e.preventDefault();

                                                var caller = this;

                                                //Get the strings
                                                var strings = {};
                                                var modified = 0;
                                                $.each($_content.find('.do_table_line'),function(){
                                                    //Get only strings if they need to be translated and have actually been translated
                                                    switch (1===1){
                                                        case $(this).find('.do_table_language').is('[readonly]'):
                                                            strings[$(this).find('.do_table_constant').data('value')] = $(this).find('.do_table_language').val();
                                                            break;
                                                        case $(this).find('.do_table_language').prop('defaultValue') !== $(this).find('.do_table_language').val():
                                                            strings[$(this).find('.do_table_constant').data('value')] = $(this).find('.do_table_language').val();
                                                            modified = 1;
                                                            break;
                                                    }
                                                });

                                                //Initialize ajax request datas
                                                var ajax_data = {
                                                    action : jutranslation.ajax_action,
                                                    slug: slug,
                                                    strings : JSON.stringify(strings),
                                                    language : result.datas.language,
                                                    extension_version : result.datas.version,
                                                    revision : result.datas.revision,
                                                    modified : modified,
                                                    wp_nonce : jutranslation.token
                                                };
                                                ajax_data[jutranslation.token] = 1;
                                                $.ajax({
                                                    type: 'POST',
                                                    url: jutranslation.base_url + 'task=jutranslation.saveStrings',
                                                    data: ajax_data,
                                                    success: function (result2) {
                                                        result2 = JSON.parse(result2);
                                                        if (result2.status === 'success') {
                                                            //Close the edition
                                                            if(close) {
                                                                caller.cancel_button_action(e);
                                                            }
                                                            toast('Modifications saved');

                                                            $parent_line.find('.current_version').html(result.datas.version + ' rev' + result.datas.revision);
                                                            $parent_line.attr('data-version', result.datas.version);

                                                            if(modified){
                                                                $parent_line.find('.jutranslation-share').show().addClass('just-added');
                                                            }else{
                                                                $parent_line.find('.jutranslation-share').hide();
                                                            }

                                                            $parent_line.find('.latest_version').html('The language file has been updated');
                                                            $parent_line.find('.jutranslation-edition').removeClass('disabled');
                                                        } else {
                                                            alert('Error while updating language file');
                                                        }
                                                    }
                                                });
                                            },
                                            columns : {
                                                reference : {
                                                    show : show_reference
                                                },
                                                language : {
                                                    editable : true,
                                                    title : 'Language ' + result.datas.language,
                                                    allow_copy : false
                                                },
                                                override : {
                                                    show : false
                                                }
                                            },
                                            copy_destination : 'do_table_language',
                                            after_init : function($_content){
                                                //Do not allow to edit already translated strings
                                                $_content.find('.do_table_language').filter(function () {
                                                    return this.value.length > 0
                                                }).attr('readonly','readonly').parents('tr').find('[class^="do_table_copy_"]').hide();

                                                //Set an introduction message
                                                if($_content.find('.do_table_language').not('[readonly="readonly"]').length){
                                                    $_content.prepend('<h3>Some strings are not currently translated to your language, you can translate it now. If you don\'t translate the english translation will be used instead</h3>');
                                                }
                                            }
                                        };

                                        //Generate the table
                                        doTable(result.datas.strings, table_option);
                                    }
                                });
                            });
                        }
                    });

                    //Check if a translation has been found on joomunited
                    var $line = $('#jutranslations-languages tr[data-lang="'+ lang + '"][data-slug="'+ julanguage.extension + '"]');
                    if(found===false){
                        $line.find('td .latest_version').html('No translation found');
                    }else{
                        if(!$line.attr('data-installed')){
                            $line.find('td .jutranslation-edition').addClass('disabled').attr('title','Please install the latest available version first');
                        }
                    }
                });
            }
        });
    });
    // Edit an installed version
    $('.jutranslation-edition').click(function(e){
        e.preventDefault();

        var $clicked_button = $(this);

        // Do nothing is disabled
        if ($clicked_button.hasClass('disabled')) {
            return;
        }

        var language = $clicked_button.parents('tr').data('lang');
        var slug = $clicked_button.parents('tr').data('slug');

        getStrings(language, slug, function(datas){

            var table_option = {
                language : language,
                slug : slug,
                confirm_button_action : function(e, $_content, close){
                    e.preventDefault();

                    var caller = this;

                    //Get the strings
                    var values = {};
                    var modified = 0;

                    $.each($_content.find('.do_table_line'),function(){
                        var value = $(this).find('.do_table_language').val();
                        if(value !== ''){
                            values[$(this).find('.do_table_constant').data('value')] = value;
                        }

                        //Check if something has been modified
                        if ($(this).find('.do_table_language').prop('defaultValue') !== $(this).find('.do_table_language').val()) {
                            modified = 1;
                        }
                    });

                    if (modified === 1) {
                        //Initialize ajax request datas
                        var ajax_data = {
                            action : jutranslation.ajax_action,
                            strings: JSON.stringify(values),
                            language: language,
                            slug: slug,
                            destination: 'edition',
                            modified: '1',
                            wp_nonce : jutranslation.token
                        };
                        ajax_data[jutranslation.token] = 1;

                        $.ajax({
                            type: 'POST',
                            url: jutranslation.base_url + 'task=jutranslation.saveStrings',
                            data: ajax_data,
                            success: function (result) {
                                result = JSON.parse(result);
                                if (result.status === 'success') {
                                    //Close the edition
                                    if(close) {
                                        caller.cancel_button_action(e);
                                    }
                                    toast('Modifications saved');

                                    $clicked_button.parents('tr').find('.jutranslation-share').show().addClass('just-added');
                                } else {
                                    alert('Error while updating language file');
                                }
                            }
                        });
                    }else{
                        //Do nothing and close the edition
                        if(close) {
                            caller.cancel_button_action(e);
                        }
                        toast('Modifications saved');
                    }
                },
                columns : {
                    language : {
                        editable : true,
                        title : 'Language ' + language,
                        allow_copy : false
                    },
                    override : {
                        show : false
                    }
                },
                copy_destination : 'do_table_language',
                after_init : function($_content){
                    //Add a message for translators before allowing to do anything
                    var message = '<p>This feature is for translators only, the content may be overwritten during the next language update. ' +
                        'If you only want to edit language strings for your own purpose please use the override feature.</p>' +
                        '<p><a href="#" id="jutranslation_show_edition" class="btn btn-lg btn-success button button-primary">OK got it, let me translate and share</a>' +
                        '<a href="#" id="jutranslation_cancel_edition" class="btn btn-lg btn-success button button-primary">Cancel</a></p>';

                    $_content.hide().after('<div class="control-group edition-message">' + message + '</div>');

                    //Click on ok edit button
                    $('#jutranslation_show_edition').click(function(){
                        $('#jutranslation .control-group.edition-message').remove();
                        $('#jutranslation .control-group.edition').show();
                    });

                    //Click on cancel edition button
                    $('#jutranslation_cancel_edition').click(function(){
                        $('#jutranslation .control-group.edition-message').remove();
                        $('#jutranslation .control-group.edition').remove();
                        $('#jutranslation .control-group').show();
                    });
                }
            };

            //Generate the table
            doTable(datas, table_option);
        });
    });

    $('.jutranslation-override').click(function(e){
        e.preventDefault();

        var $clicked_button = $(this);

        var language = $clicked_button.parents('tr').data('lang');
        var slug = $clicked_button.parents('tr').data('slug');

        //Do not show language columns if it's en-US
        var show_language = true;
        if(language==='en-US'){
            show_language = false;
        }

        getStrings(language, slug, function(datas) {
            var table_option = {
                language : language,
                confirm_button_action : function(e, $_content, close){
                    e.preventDefault();

                    var caller = this;

                    //Get the strings
                    var overrides = {};
                    $.each($_content.find('.do_table_line'),function(){
                        var value = $(this).find('.do_table_override').val();
                        if(value !== ''){
                            overrides[$(this).find('.do_table_constant').data('value')] = value;
                        }
                    });

                    //Initialize ajax request datas
                    var ajax_data = {
                        action : jutranslation.ajax_action,
                        strings : JSON.stringify(overrides),
                        language : this.language,
                        slug: slug,
                        destination : 'override',
                        wp_nonce : jutranslation.token
                    };
                    ajax_data[jutranslation.token] = 1;

                    $.ajax({
                        type : 'POST',
                        url : jutranslation.base_url + 'task=jutranslation.saveStrings',
                        data : ajax_data,
                        success : function(result){
                            result = JSON.parse(result);
                            if(result.status === 'success'){
                                $clicked_button.parents('tr').find('.jutranslation-override-count').html(Object.keys(overrides).length);

                                if(close) {
                                    caller.cancel_button_action(e);
                                }
                                toast('Modifications saved');
                            }else{
                                alert('Error while updating language file');
                            }
                        }
                    });
                },
                columns : {
                    language : {
                        title : 'Language ' + language,
                        show : show_language
                    }
                }
            };

            //Generate the table
            doTable(datas, table_option);
        });
    });

    //Share a translation to Joomunited
    $('.jutranslation-share').click(function(e){
        e.preventDefault();
        if(typeof SqueezeBox !== 'undefined' ){
            //Open Joomla lightbox
            SqueezeBox.open(jutranslation.base_url + 'action=&task=jutranslation.showViewForm&wp_nonce='+jutranslation.token+'&language=' +  $(this).closest('tr').attr('data-lang'), {handler: 'iframe'});
        }else{
            //Open Wordpress lightbox
            tb_show('Share with Joomunited',jutranslation.base_url + 'action=' + jutranslation.ajax_action + '&task=jutranslation.showViewForm&wp_nonce='+jutranslation.token+'&slug=' +  $(this).closest('tr').attr('data-slug') + '&language=' +  $(this).closest('tr').attr('data-lang') + '&TB_iframe=true');
        }
    });

    function doTable(datas, options) {
        // DOM element containing the generated content
        var $_content;

        var default_options = {
            language : null,
            slug : null,
            confirm_button_label: 'Save and close',
            save_button_label: 'Save',
            cancel_button_label: 'Cancel',
            cancel_button_action: function (e) {
                if(e!==undefined){
                    e.preventDefault();
                }

                $('#jutranslation .control-group.edition').remove();
                $('#jutranslation .control-group').show();
            },
            confirm_button_action : function (e) {
                if(e!==undefined){
                    e.preventDefault();
                }

                options.cancel_button_action(e);
            },
            after_init : function() {},
            columns : {
                constant : {
                    show : true,
                    title : 'Constant',
                    allow_copy : false
                },
                reference : {
                    show : true,
                    editable : false,
                    title : 'Reference en-US',
                    allow_copy : true

                },
                language : {
                    show : true,
                    editable : false,
                    title : 'Language',
                    allow_copy : true
                },
                override : {
                    show : true,
                    editable : true,
                    title : 'Override',
                    allow_copy : false
                }
            },
            copy_destination : 'do_table_override'
        };

        // Initialize options variable if not already
        if(options === undefined){
            var options = {};
        }

        // Merge the default options to the passed ones
        options = $.extend(true, {}, default_options, options);

        //Add a button to save
        var content = '';
        content += '<p class="do_table_buttons">';
        content += '    <a href="#" class="do_table_confirm btn btn-primary button button-primary">' + options.confirm_button_label + '</a>';
        content += '    <a href="#" class="do_table_save btn btn-primary button button-primary">' + options.save_button_label + '</a>';
        content += '    <a href="#" class="do_table_cancel btn btn-primary button">' + options.cancel_button_label + '</a>';
        content += '</p>';

        //Create the table of strings
        content += '<table>';

        //Add columns names
        content +=  '<tr class="do_table_heading">' +
            '   <td' + ( options.columns.constant.show?'':' class="hidden"' ) + '>' + options.columns.constant.title + '</td>' +
            '   <td' + ( options.columns.reference.show?'':' class="hidden"' ) + '>' + options.columns.reference.title + '</td>' +
            '   <td' + ( options.columns.language.show?'':' class="hidden"' ) + '>' + options.columns.language.title + '</td>' +
            '   <td' + ( options.columns.override.show?'':' class="hidden"' ) + '>' + options.columns.override.title + '</td>' +
            '</tr>';

        //Add filter inputs
        content +=  '<tr class="do_table_filters">' +
            '   <td' + ( options.columns.constant.show?'':' class="hidden"' ) + '><input class="do_table_filter_constant" type="text" placeholder="Filter by constant"/></td>' +
            '   <td' + ( options.columns.reference.show?'':' class="hidden"' ) + '><input class="do_table_filter_reference" type="text" placeholder="Filter by reference"/><br/>Show only empty <input type="checkbox" class="do_table_filter_empty_reference"/><br/>Show only not empty <input type="checkbox" class="do_table_filter_not_empty_reference"/></td>' +
            '   <td' + ( options.columns.language.show?'':' class="hidden"' ) + '><input class="do_table_filter_language" type="text" placeholder="Filter by language"/><br/>Show only empty <input type="checkbox" class="do_table_filter_empty_language"/><br/>Show only not empty <input type="checkbox" class="do_table_filter_not_empty_language"/></td>' +
            '   <td' + ( options.columns.override.show?'':' class="hidden"' ) + '><input class="do_table_filter_override" type="text" placeholder="Filter by override"/><br/>Show only empty <input type="checkbox" class="do_table_filter_empty_override"/><br/>Show only not empty <input type="checkbox" class="do_table_filter_not_empty_override"/></td>' +
            '</tr>';

        $.each(datas, function(key,value){
            content +=  '' +
                '<tr class="do_table_line">' +
                '   <td' + ( options.columns.constant.show?'':' class="hidden"' ) + '>' +
                '       <span class="do_table_constant" data-value="' + htmlEntities(value.key) + '">' + htmlEntities(value.key) + '</span>' +
                '   </td>' +
                '   <td' + ( options.columns.reference.show?'':' class="hidden"' ) + '>' +
                '       <input type="text" class="do_table_reference" value="' + htmlEntities(value.reference) + '" ' + ( options.columns.reference.editable?'':'readonly="readonly"' ) + ' />' + (options.columns.reference.allow_copy?'<a title="copy" href="#" class="do_table_copy_reference"><i class="icon-arrow-right-4 dashicons dashicons-arrow-right-alt2"></i></a>':'') +
                '   </td>' +
                '   <td' + ( options.columns.language.show?'':' class="hidden"' ) + '>' +
                '       <input type="text" class="do_table_language" value="' + htmlEntities(value.language) + '" ' + ( options.columns.language.editable?'':'readonly="readonly"' ) + ' >' + (options.columns.language.allow_copy?'<a title="copy" href="#" class="do_table_copy_language"><i class="icon-arrow-right-4 dashicons dashicons-arrow-right-alt2"></i></a>':'') +
                '   </td>' +
                '   <td' + ( options.columns.override.show?'':' class="hidden"' ) + '>' +
                '       <input type="text" class="do_table_override" value="' + htmlEntities(value.override) + '" ' + ( options.columns.override.editable?'':'readonly="readonly"' ) + ' >' + (options.columns.override.allow_copy?'<a title="copy" href="#" class="do_table_copy_override"><i class="icon-arrow-right-4 dashicons dashicons-arrow-right-alt2"></i></a>':'') +
                '   </td>' +
                '</tr>';
        });
        content += '</table>';

        $('#jutranslation .control-group').after('<div class="control-group edition">' + content + '</div>');
        $_content = $('#jutranslation .control-group.edition');

        // Enable filtering
        $_content.find('[class^="do_table_filter_"]').on("change keyup paste", function(){
            //Texts filters
            var filter_constant = $_content.find('.do_table_filter_constant').val();
            var filter_reference = $_content.find('.do_table_filter_reference').val();
            var filter_language = $_content.find('.do_table_filter_language').val();
            var filter_override = $_content.find('.do_table_filter_override').val();

            // Do not allow to check both filters
            switch (1==1) {
                case $(this).hasClass('do_table_filter_empty_reference') && $(this).is(':checked'):
                    $_content.find('.do_table_filter_not_empty_reference').attr('checked',null);
                    break;
                case $(this).hasClass('do_table_filter_empty_language') && $(this).is(':checked'):
                    $_content.find('.do_table_filter_not_empty_language').attr('checked',null);
                    break;
                case $(this).hasClass('do_table_filter_empty_override') && $(this).is(':checked'):
                    $_content.find('.do_table_filter_not_empty_override').attr('checked',null);
                    break;
                case $(this).hasClass('do_table_filter_not_empty_reference') && $(this).is(':checked'):
                    $_content.find('.do_table_filter_empty_reference').attr('checked',null);
                    break;
                case $(this).hasClass('do_table_filter_not_empty_language') && $(this).is(':checked'):
                    $_content.find('.do_table_filter_empty_language').attr('checked',null);
                    break;
                case $(this).hasClass('do_table_filter_not_empty_override') && $(this).is(':checked'):
                    $_content.find('.do_table_filter_empty_override').attr('checked',null);
                    break;

            }
            if($(this).is('[class^="do_table_filter_empty"]')){

            } else if ($(this).is('[class^="do_table_filter_not_empty"]')) {

            }
            $_content.find('.do_table_filter_not_empty_reference');

            //Empty filters
            var filter_empty_reference = $_content.find('.do_table_filter_empty_reference').is(":checked");
            var filter_empty_language = $_content.find('.do_table_filter_empty_language').is(":checked");
            var filter_empty_override = $_content.find('.do_table_filter_empty_override').is(":checked");

            var filter_not_empty_reference = $_content.find('.do_table_filter_not_empty_reference').is(":checked");
            var filter_not_empty_language = $_content.find('.do_table_filter_not_empty_language').is(":checked");
            var filter_not_empty_override = $_content.find('.do_table_filter_not_empty_override').is(":checked");

            // Show all rows
            $_content.find('tr.do_table_line').show();

            // Filtering is active
            if(filter_constant !== '' || filter_reference !== '' || filter_language !== '' || filter_override != '' || filter_empty_reference!==false || filter_empty_language !== false || filter_empty_override !== false || filter_not_empty_reference!==false || filter_not_empty_language !== false || filter_not_empty_override !== false){
                // Show only rows matching search
                $_content.find('.do_table_line').filter(function(){
                    //Filter by empty reference
                    if(filter_empty_reference && $(this).find('.do_table_reference').val() !== ''){
                        return true;
                    }

                    //Filter by empty language
                    if(filter_empty_language && $(this).find('.do_table_language').val() !== ''){
                        return true;
                    }

                    //Filter by empty override
                    if(filter_empty_override && $(this).find('.do_table_override').val() !== ''){
                        return true;
                    }

                    //Filter by not empty reference
                    if(filter_not_empty_reference && $(this).find('.do_table_reference').val() === ''){
                        return true;
                    }

                    //Filter by empty language
                    if(filter_not_empty_language && $(this).find('.do_table_language').val() === ''){
                        return true;
                    }

                    //Filter by empty override
                    if(filter_not_empty_override && $(this).find('.do_table_override').val() === ''){
                        return true;
                    }

                    //Filter by constant
                    if(filter_constant !== ''){
                        var regex = new RegExp(escapeRegExp(filter_constant), 'i');
                        if(!regex.test($(this).find('.do_table_constant').data('value'))){
                            return true;
                        }
                    }

                    //Filter by reference
                    if(filter_reference !== ''){
                        var regex = new RegExp(escapeRegExp(filter_reference), 'i');
                        if(!regex.test($(this).find('.do_table_reference').val())){
                            return true;
                        }
                    }

                    //Filter by current language
                    if(filter_language !== ''){
                        var regex = new RegExp(escapeRegExp(filter_language), 'i');
                        if(!regex.test($(this).find('.do_table_language').val())){
                            return true;
                        }
                    }

                    //Filter by override
                    if(filter_override !== ''){
                        var regex = new RegExp(escapeRegExp(filter_override), 'i');
                        if(!regex.test($(this).find('.do_table_override').val())){
                            return true;
                        }
                    }

                    return false;
                }).hide();
                return;
            }
        });

        // Enable copy from one input to another
        $_content.find('[class^="do_table_copy_"]').click(function(e){
            e.preventDefault();

            //Get the value from clicked element
            if($(this).hasClass('do_table_copy_reference')){
                var value = $(this).parents('tr').find('.do_table_reference').val();
            }else if($(this).hasClass('do_table_copy_language')){
                var value = $(this).parents('tr').find('.do_table_language').val();
            }else if($(this).hasClass('do_table_copy_override')){
                var value = $(this).parents('tr').find('.do_table_override').val();
            }

            //Copy value to destination input
            $(this).parents('tr').find('.'+options.copy_destination).val(value);
        });

        //Save and close
        $_content.find('.do_table_confirm').click(function(e){
            options.confirm_button_action(e, $_content, true);
        });

        //Confirm the current action
        $_content.find('.do_table_save').click(function(e){
            options.confirm_button_action(e, $_content, false);
        });

        //Cancel the current action
        $_content.find('.do_table_cancel').click(function(e, $_content){
            options.cancel_button_action(e);
        });

        $('#jutranslation .control-group').not('.edition').hide();

        $_content.find('.do_table_line input').focus(function(){
            var $this = $(this);
            var $this_line = $this.parents('tr');

            //Do nothing if already hidden
            if($(this).is(":hidden")){
                return;
            }

            //Remove all old textarea
            $this_line.find('textarea').remove();

            $this_line.find('input').each(function(){
                // Get the input value
                var val = $(this).val();

                // Add a textarea after the inpu
                $('<textarea style="width:' + $(this).css('width') + '" ' + ($(this).is('[readonly="readonly"]')?'readonly="readonly"':'') + '>' + htmlEntities(val) + '</textarea>').insertAfter(this);

                // Hide the input
                $(this).hide();
            });

            // Remove textarea on focus out
            $this.siblings('textarea').focus().focusout(function(){
                $this.val(($(this).val()));
                $this_line.find('input').show();
                $this_line.find('textarea').hide(); //Hide instead of remove to avoid html5fallback.js error
            });
        });

        options.after_init($_content);
    }

    function getStrings(language, slug, done_function) {
        var strings = {};
        $.ajax({
            type: 'POST',
            url: jutranslation.base_url + 'task=jutranslation.getTranslation',
            data: {
                action : jutranslation.ajax_action,
                language: language,
                slug: slug,
                wp_nonce : jutranslation.token
            },
            success: function (result) {
                result = JSON.parse(result);

                if (result.status === 'success') {
                    done_function(result.datas.strings);
                } else {
                    alert('Error while loading strings');
                }
            }
        });
    }

    /**
     * Escape a string to allow using it literally in a regex
     * @param string
     * @return string escaped
     */
    function escapeRegExp(string) {
        return string.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    }

    /**
     * Secure a string to use it in html content
     * @param string to espace
     * @return {string} escaped string
     */
    function htmlEntities(string){
        return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    /**
     * Compare two version numbers and their revisions
     * @param version1
     * @param version2
     * @param revision_version1
     * @param revision_version2
     * @return int 1 if version1>version2, -1 if version1<version2 , 0 if version1==version2
     */
    function versionCompare(version1, version2, revision_version1, revision_version2){
        if(version1==='') {
            version1 = '0';
        }

        if(version2==='') {
            version2 = '0';
        }

        version1 = version1.split('.');
        version2 = version2.split('.');

        if(typeof revision_version1 === 'undefined'){
            revision_version1 = 0;
        }

        if(typeof revision_version2 === 'undefined'){
            revision_version2 = 0;
        }

        for(var ij=0; ij < version1.length; ij++ ) {
            if(typeof version2[ij] === 'undefined'){
                //Version 1 has the longest version
                for(var ik=ij; ik < version1.length; ik++){
                    //Check if it has another thing as 0 trailing
                    if(parseInt(version[1]) > 0){
                        //Latest version is upper
                        return 1;
                    }
                }
                //End only with 0 version numbers, it's equal
                if(revision_version1 > revision_version2){
                    return 1;
                } else if(revision_version1 < revision_version2){
                    return -1;
                }
                return 0;
            } else {
                if(parseInt(version1[ij]) > parseInt(version2[ij])) {
                    return 1;
                } else if(parseInt(version1[ij]) < parseInt(version2[ij])) {
                    return -1;
                }
            }
        }

        //Version 2 has the longest version or is equal
        for(var ik=ij; ik < version2.length; ik++){
            //Check if it has another thing as 0 trailing
            if(parseInt(version2[ik]) > 0){
                //Latest version is upper
                return -1;
            }
        }

        //End only with 0 version numbers, it's equal
        if(revision_version1 > revision_version2){
            return 1;
        } else if(revision_version1 < revision_version2){
            return -1;
        }
        return 0;
    }

    function toast(text) {
        $('#jutranslation_toast').remove();
        $('body').append('<div id="jutranslation_toast">' + text + '</div>');
        setTimeout(function() {
            $('#jutranslation_toast').remove();
        }, 2000);
    }
});
