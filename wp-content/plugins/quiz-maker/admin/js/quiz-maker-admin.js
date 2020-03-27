(function ($) {
    'use strict';
    $(document).ready(function () {
        // Notifications dismiss button
        $(document).on('click', '.notice-dismiss', function (e) {
            let linkModified = location.href.split('?')[1].split('&');
            for(let i = 0; i < linkModified.length; i++){
                if(linkModified[i].split("=")[0] == "status"){
                    linkModified.splice(i, 1);
                }
            }
            linkModified = linkModified.join('&');
            window.history.replaceState({}, document.title, '?'+linkModified);
        });
        // Quiz toast close button
        jQuery('.quiz_toast__close').click(function(e){
            e.preventDefault();
            var parent = $(this).parent('.quiz_toast');
            parent.fadeOut("slow", function() { $(this).remove(); } );
        });
        
        let toggle_ddmenu = $(document).find('.toggle_ddmenu');
        toggle_ddmenu.on('click', function () {
            let ddmenu = $(this).next();
            let state = ddmenu.attr('data-expanded');
            switch (state) {
                case 'true':
                    $(this).find('.ays_fa').css({
                        transform: 'rotate(0deg)'
                    });
                    ddmenu.attr('data-expanded', 'false');
                    break;
                case 'false':
                    $(this).find('.ays_fa').css({
                        transform: 'rotate(90deg)'
                    });
                    ddmenu.attr('data-expanded', 'true');
                    break;
            }
        });
        
        $('[data-toggle="popover"]').popover();
        $('[data-toggle="tooltip"]').tooltip();
        
        // Disabling submit when press enter button on inputing
        $(document).on("input", 'input', function(e){
            if(e.keyCode == 13){
                if($(document).find("#ays-question-form").length !== 0 ||
                   $(document).find("#ays-quiz-category-form").length !== 0 ||
                   $(document).find("#ays-quiz-settings-form").length !== 0){
                    return false;
                }
            }
        });
        
        $(document).on("keydown", function(e){
            if(e.target.nodeName == "TEXTAREA"){
                return true;
            }
            if(e.keyCode == 13){
                if($(document).find("#ays-question-form").length !== 0 ||
                   $(document).find("#ays-quiz-category-form").length !== 0 ||
                   $(document).find("#ays-quiz-settings-form").length !== 0){
                    return false;
                }
            }
            if(e.keyCode === 27){
                $(document).find('.ays-modal').aysModal('hide');
                return false;
            }
        });
        
        
        // Dashboard page
        // start
        let heart_interval = setInterval(function () {
            $('div.ays-quiz-maker-wrapper h1 i.ays_fa').toggleClass('pulse');
            $(document).find('.ays_heart_beat i.ays_fa').toggleClass('ays_pulse');
        }, 1000);


        let appearanceTime = 200, 
            appearanceEffects = ['fadeInLeft', 'fadeInRight'];        
        $(document).find('div.ays-quiz-card').each(function (index) {
            let card = $(this);
            setTimeout(function () {
                card.addClass('ays-quiz-card-show' + ' ' + appearanceEffects[index % 2]);
            }, appearanceTime);
            appearanceTime += 200;
        });
        
        // end
        
        
        $(document).find('#ays-quiz-title').on('input', function(e){
            $(document).find('.ays_quiz_title_in_top').html($(this).val());
        });
        
        if($(document).find('.ays-top-menu').width() <= $(document).find('div.ays-top-tab-wrapper').width()){
            $(document).find('.ays_menu_left').css('display', 'flex');
            $(document).find('.ays_menu_right').css('display', 'flex');
        }
        $(window).resize(function(){
            if($(document).find('.ays-top-menu').width() < $(document).find('div.ays-top-tab-wrapper').width()){
                $(document).find('.ays_menu_left').css('display', 'flex');
                $(document).find('.ays_menu_right').css('display', 'flex');
            }else{
                $(document).find('.ays_menu_left').css('display', 'none');
                $(document).find('.ays_menu_right').css('display', 'none');
                $(document).find('div.ays-top-tab-wrapper').css('transform', 'translate(0px)');
            }
        });
        let menuItemWidths0 = [];
        let menuItemWidths = [];
        $(document).find('.ays-top-tab-wrapper .nav-tab').each(function(){
            let $this = $(this);
            menuItemWidths0.push($this.outerWidth());
        });

        for(let i = 0; i < menuItemWidths0.length; i+=2){
            menuItemWidths.push(menuItemWidths0[i]+menuItemWidths0[i+1]);
        }
        let menuItemWidth = 0;
        for(let i = 0; i < menuItemWidths.length; i++){
            menuItemWidth += menuItemWidths[i];
        }
        menuItemWidth = menuItemWidth / menuItemWidths.length;

        $(document).on('click', '.ays_menu_left', function(){
            let scroll = parseInt($(this).attr('data-scroll'));
            scroll -= menuItemWidth;
            if(scroll < 0){
                scroll = 0;
            }
            $(document).find('div.ays-top-tab-wrapper').css('transform', 'translate(-'+scroll+'px)');
            $(this).attr('data-scroll', scroll);
            $(document).find('.ays_menu_right').attr('data-scroll', scroll);
        });
        $(document).on('click', '.ays_menu_right', function(){
            let scroll = parseInt($(this).attr('data-scroll'));
            let howTranslate = $(document).find('div.ays-top-tab-wrapper').width() - $(document).find('.ays-top-menu').width();
            howTranslate += 7;
            if(scroll == -1){
                scroll = menuItemWidth;
            }
            scroll += menuItemWidth;
            if(scroll > howTranslate){
                scroll = howTranslate;
            }
            $(document).find('div.ays-top-tab-wrapper').css('transform', 'translate(-'+scroll+'px)');
            $(this).attr('data-scroll', scroll);
            $(document).find('.ays_menu_left').attr('data-scroll', scroll);
        });
        

//        if($(document).find('.checkbox_carousel_body').width() >= $(document).find('div.checkbox_carousel').width()){
//            $(document).find('.cb_carousel_arrows').css('display', 'block');
//            $(document).find('div.checkbox_carousel').each(function(){
//                $(this).attr('data-scroll-width', this.scrollWidth);
//            });
//        }
        $(document).on('click', '.cb_carousel_left', function(){
            let howTranslate = ($(document).find('div.checkbox_carousel').width() * 25) / 100;
            let currentTranslate = parseInt($(this).parents('.checkbox_carousel').find('.checkbox_carousel_body').css('transform').split(',')[4]);
            if(currentTranslate == NaN){
                currentTranslate = 0;
            }
            if(currentTranslate < 0){
                howTranslate = howTranslate + currentTranslate;
            }else{
                howTranslate = howTranslate + -currentTranslate;
            }
            if(howTranslate > 0){
                howTranslate = 0;
            }
            $(this).parents('.checkbox_carousel').find('.checkbox_carousel_body').css('transform', 'translateX('+howTranslate+'px)');
        });
        
        $(document).on('click', '.cb_carousel_right', function(){
            let howTranslate = ($(document).find('div.checkbox_carousel').width() * 25) / 100;
            let currentTranslate = parseInt($(this).parents('.checkbox_carousel').find('.checkbox_carousel_body').css('transform').split(',')[4]);
            if(currentTranslate < 0){
                howTranslate = howTranslate + -currentTranslate;
            }else{
                howTranslate = howTranslate + currentTranslate;
            }
            
            if(parseInt($(this).parents('.checkbox_carousel')[0].scrollWidth) - parseInt($(this).parents('.checkbox_carousel').width()) < Math.abs(howTranslate)){
                howTranslate = parseInt($(this).parents('.checkbox_carousel').data('scrollWidth') - parseInt($(this).parents('.checkbox_carousel').width()) + 2);
            }
            $(this).parents('.checkbox_carousel').find('.checkbox_carousel_body').css('transform', 'translateX(-'+howTranslate+'px)');
        });
        
//        $(document).find('#ays_enable_paypal').on('change', function(){
//            if($(this).prop('checked') == true){
//                if($(document).find('#ays_enable_logged_users').attr('checked') != 'checked'){
//                    $(document).find('#ays_enable_logged_users').trigger('click');
////                    $(document).find('#ays_enable_logged_users').attr('checked', 'checked');
//                    if($(document).find('#ays_logged_in_message').val() == ''){
//                        $(document).find('#ays_logged_in_message').html('You need to log in to pass this quiz.');
//                    }
//                }
//                $(document).find('#ays_enable_logged_users').attr('disabled', 'disabled');
//            }else{
//                $(document).find('#ays_enable_logged_users').removeAttr('disabled');
//            }
//        });
//        if($(document).find('#ays_enable_paypal').prop('checked') == true){
//            if($(document).find('#ays_enable_logged_users').attr('checked') != 'checked'){
//                $(document).find('#ays_enable_logged_users').trigger('click');
//                $(document).find('#ays_enable_logged_users').attr('checked', 'checked');
//                if($(document).find('#ays_logged_in_message').val() == ''){
//                    $(document).find('#ays_logged_in_message').html('You need to log in to pass this quiz.');
//                }
//            }
//            setTimeout(function(){
//                $(document).find('#ays_enable_logged_users').attr('disabled', 'disabled');
//            }, 1);
//        }else{
//            $(document).find('#ays_enable_logged_users').removeAttr('disabled');
//        }
        

        $(document).on('change', '.ays_toggle_checkbox', function (e) {
            let state = $(this).prop('checked');
            let parent = $(this).parents('.ays_toggle_parent');
            
            if($(this).hasClass('ays_toggle_slide')){
                switch (state) {
                    case true:
                        parent.find('.ays_toggle_target').slideDown(250);
                        break;
                    case false:
                        parent.find('.ays_toggle_target').slideUp(250);
                        break;
                }
            }else{
                switch (state) {
                    case true:
                        parent.find('.ays_toggle_target').show(250);
                        break;
                    case false:
                        parent.find('.ays_toggle_target').hide(250);
                        break;
                }
            }
        });
        
        $(document).on('change', '.ays_toggle_select', function (e) {
            let state = $(this).val();
            let toggle = $(this).data('hide');
            let parent = $(this).parents('.ays_toggle_parent');
            
            if($(this).hasClass('ays_toggle_slide')){
                if (toggle == state) {
                    parent.find('.ays_toggle_target').slideUp(250);
                }else{
                    parent.find('.ays_toggle_target').slideDown(250);
                }
            }else{
                if (toggle == state) {
                    parent.find('.ays_toggle_target').hide(250);
                }else{
                    parent.find('.ays_toggle_target').show(250);
                }
            }
        });
                    
        
        $(document).find('.checkbox_carousel.form_fields input[type="checkbox"]').on('change', function(e){
            if($(this).prop('checked') == true){
                $(document).find('#'+$(this).attr('id')+'_required').removeAttr('disabled');
            }else{
                $(document).find('#'+$(this).attr('id')+'_required').attr('disabled', 'disabled');
                $(document).find('#'+$(this).attr('id')+'_required').removeAttr('checked');
            }
        });

        $(document).find('.checkbox_carousel.form_fields input[type="checkbox"]').each(function(e){
            if($(this).prop('checked') == true){
                $(document).find('#'+$(this).attr('id')+'_required').removeAttr('disabled');
            }else{
                $(document).find('#'+$(this).attr('id')+'_required').attr('disabled', 'disabled');
                $(document).find('#'+$(this).attr('id')+'_required').removeAttr('checked');
            }
        });


        var ays_results = $(document).find('.ays_result_read, .ays_quiz_results_unreads');
        for (var i in ays_results) {
            if (typeof ays_results.eq(i).val() != 'undefined') {
                if (ays_results.eq(i).val() == 0) {
                    ays_results.eq(i).parents('tr').addClass('ays_read_result');
                }
            }
        }
        var ays_quiz_results = $(document).find('.ays-show-results');
        for (var i in ays_quiz_results) {
            ays_quiz_results.eq(i).parents('tr').addClass('ays_quiz_read_result');
        }

        $(document).find('#ays-category').select2({
            placeholder: 'Select category'
        });
        $(document).find('#ays_user_roles').select2({
            placeholder: 'Select role'
        });

        
        $(document).find('.interval_wproduct').select2({
            placeholder: 'Select a product',
            allowClear: true,
            templateResult: ays_formatState
        });
        
        function ays_formatState (ays_state) {
            if(!ays_state.id) {
                return ays_state.text;
            }
            var baseUrl = $(ays_state.element).data('nkar');
            if(baseUrl != ''){
                var ays_state = $(
                    '<span><img src=' + baseUrl + ' class=\'ays_prod_image\' /> ' + ays_state.text + '</span>'
                );
            }else{
                var ays_state = $(
                    '<span>' + ays_state.text + '</span>'
                );
            }
            return ays_state;
        }

        $(document).find('b[role="presentation"]').removeClass('ays_fa ays_fa_chevron_down');
        
        $(document).find('.ays-field .select2-container').on("click", function () {
            if ($(this).hasClass('select2-container--open')) {
                $(this).find('b[role="presentation"]').removeClass('ays_fa ays_fa_chevron_down');
                $(this).find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_up');
            } else {
                $(this).find('b[role="presentation"]').removeClass('ays_fa ays_fa_chevron_up');
                $(this).find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');
            }
        });

        // Initialize sortable
        $(document).find('table.ays-answers-table tbody').sortable({
            handle: '.ays_fa_arrows',
            cursor: 'move',
			opacity: 0.8,
			placeholder: 'clone',
            update: function (event, ui) {
                let className = ui.item.attr('class').split(' ')[0];
                $('table.ays-answers-table tbody').find('tr.'+className).each(function (index) {
                    let newValue = index + 1,
                        classEven = (((index + 1) % 2) === 0) ? 'even' : '';
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    $(this).addClass(classEven);
                    $(this).find('.ays-correct-answer').val(newValue);
                });
            }
        });

        $(document).find('table.ays-questions-table tbody').sortable({
            handle: 'td.ays-sort',
            cursor: 'move',
			opacity: 0.8,
			placeholder: 'clone',
            update: function (event, ui) {
                let className = ui.item.attr('class').split(' ')[0];
                let sorting_ids = [];
                $(document).find('tr.' + className).each(function (index) {
                    let classEven = (((index + 1) % 2) === 0) ? 'even' : '';
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    sorting_ids.push($(this).data('id'));
                    $(this).addClass(classEven);
                });
                $(document).find('input#ays_already_added_questions').val(sorting_ids);
            }
        });

        $(document).find('table.ays-intervals-table tbody').sortable({
            handle: 'td.ays-sort',
            cursor: 'move',
			opacity: 0.8,
			placeholder: 'clone',
            update: function (event, ui) {
                let className = ui.item.attr('class').split(' ')[0];
                $(document).find('tr.' + className).each(function (index) {
                    let classEven = (((index + 1) % 2) === 0) ? 'even' : '';
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    $(this).addClass(classEven);
                });
            }
        });

        $('.interval_max').on('input', function () {
            var this_max = $(this);
            var next_min_input = $(this).parents().eq(1).next().find('.interval_min');
            if (next_min_input) next_min_input.val(parseInt(this_max.val()) + 1);
        });
        
        $('.interval_max,.interval_min').on('change', function () {
            var this_value = parseInt($(this).val());
            var prev_min_input = parseInt($(this).parents().eq(1).prev().find('.interval_min').val());
            var prev_max_input = parseInt($(this).parents().eq(1).prev().find('.interval_max').val());

            if (this_value <= prev_min_input || this_value <= prev_max_input) {
                alert('Your value must be bigger than ' + prev_min_input + ' or ' + prev_max_input);
            }
        });
        
        $('.ays-add-interval').on('click', function () {
            let intervals_table = $('.ays-intervals-table'),
                row_count = intervals_table.children('tbody').children('tr').length,
                className = ((row_count % 2) === 0) ? "" : "even",
                isWoo = intervals_table.hasClass('with-woo-product'),
                wooSelect = isWoo ? intervals_table.find(".interval_wproduct").eq(0).parent().clone(true).prop("outerHTML") : "";
            let wooOptions = "";
            
            if(isWoo){
                for(let i in quiz_wc_products){
                    wooOptions += "<option data-nkar='" + quiz_wc_products[i].image + "' value='" + quiz_wc_products[i].ID + "'>" + quiz_wc_products[i].post_title + "</option>\n";
                }
                wooSelect = "<td>" +
                    "<select  name='interval_wproduct[]' class='interval_wproduct'>" +
                        "<option></option>" +
                        wooOptions +
                    "</select>" +
                "</td>";
            }
            intervals_table.append("<tr class=\"ays-interval-row ui-state-default " + className + " \">\n" +
                "   <td class=\"ays-sort\"><i class=\"ays_fa ays_fa_arrows\" aria-hidden=\"true\"></i></td>\n" +
                "   <td><input type=\"number\" name=\"interval_min[]\" class=\"interval_min\"></td>\n" +
                "   <td><input type=\"number\" name=\"interval_max[]\" class=\"interval_max\"></td>\n" +
                "   <td><textarea name=\"interval_text[]\" class=\"interval_text\"></textarea></td>\n" +
                wooSelect +
                "   <td class=\"ays-interval-image-td\">\n" +
                "       <label class='ays-label' for='ays-answer'><a href=\"javascript:void(0)\" class=\"add-answer-image add-interval-image\" style=display:block;>Add</a></label>\n" +
                "       <div class=\"ays-answer-image-container ays-interval-image-container\" style=display:none;>\n" +
                "           <span class=\"ays-remove-answer-img\"></span>\n" +
                "           <img src=\"\" class=\"ays-answer-img\" style=\"width: 100%;\"/>\n" +
                "           <input type=\"hidden\" name=\"interval_image[]\" class=\"ays-answer-image\" value=\"\"/>\n" +
                "       </div>\n" +
                "   </td>\n" +
                "   <td>\n" +
                "       <a href=\"javascript:void(0)\" class=\"ays-delete-interval\">\n" +
                "           <i class=\"ays_fa ays_fa_minus_square\" aria-hidden=\"true\"></i>\n" +
                "       </a>\n" +
                "   </td>\n" +
                "</tr>");
            intervals_table.find('.interval_wproduct').select2({
                placeholder: 'Select a product',
                allowClear: true,
                templateResult: ays_formatState
            });
        });

        $(document).on('click', '.ays-remove-answer-img', function () {
            $(this).parent().fadeOut();
            var ays_remove_answer_img = $(this);
            if(ays_remove_answer_img.parent().hasClass('ays-interval-image-container')){
                setTimeout(function(){
                    ays_remove_answer_img.parents().eq(1).find('.add-interval-image').fadeIn();
                    ays_remove_answer_img.parent().find('img.ays-answer-img').attr('src', '');
                    ays_remove_answer_img.parent().find('input.ays-answer-image').val('');
                },300);
            }
        });

        $(document).on('click', '.ays-delete-interval', function () {
            $(this).parent('td').parent('tr.ays-interval-row').remove();

            $(document).find('tr.ays-interval-row').each(function () {
                if ($(this).hasClass('even')) {
                    $(this).removeClass('even');
                }
                let className = ((index % 2) === 0) ? 'even' : '';
                index++;
            });
        });
        
        // Modal close
        $(document).find('.ays-close').on('click', function () {
            $(document).find('.ays-modal').aysModal('hide');
        });
            

        // Quiz questions table
        $(document).find('.ays-delete-question').live('click', function () {
            let id = $(this).parents('.ays-question-row').data('id');
            let index = $.inArray(id, window.aysQuestSelected);

            if ( index !== -1 ) {
                window.aysQuestSelected.splice( index, 1 );
            }
        });
        
        $(document).on('click', '.ays-delete-question', function () {
            let index = 1,
                id_container = $(document).find('input#ays_already_added_questions'),
                existing_ids = id_container.val().split(',');
            let q = $(this);
            q.parents("tr").css({
                'animation-name': 'slideOutLeft',
                'animation-duration': '.3s'
            });
            let indexOfAddTable = $.inArray($(this).data('id'), window.aysQuestSelected);
            if(indexOfAddTable !== -1){
                window.aysQuestSelected.splice( indexOfAddTable, 1 );
                qatable.draw();
            }

            if ($.inArray($(this).data('id').toString(), existing_ids) !== -1) {
                let position = $.inArray($(this).data('id').toString(), existing_ids);
                existing_ids.splice(position, 1);
                id_container.val(existing_ids.join(','));
            }

            $(document).find('input[type="checkbox"]#ays_select_' + $(this).data('id')).prop('checked', false);
            
            setTimeout(function(){            
                q.parent('td').parent('tr.ays-question-row').remove();
                let accordion = $(document).find('table.ays-questions-table tbody');
                let questions_count = accordion.find('tr.ays-question-row').length;
                $(document).find('.questions_count_number').text(questions_count);
            
                if($(document).find('tr.ays-question-row').length == 0){
                   let quizEmptytd = '<tr class="ays-question-row ui-state-default">'+
                    '    <td colspan="5" class="empty_quiz_td">'+
                    '        <div>'+
                    '            <i class="ays_fa ays_fa_info" aria-hidden="true" style="margin-right:10px"></i>'+
                    '            <span style="font-size: 13px; font-style: italic;">'+
                    '               There are no questions yet.'+
                    '            </span>'+
                    '            <a class="create_question_link" href="admin.php?page=quiz-maker-questions&action=add" target="_blank">Create question</a>'+
                    '        </div>'+
                    '        <div class="ays_add_question_from_table">'+
                    '            <a href="javascript:void(0)" class="ays-add-question">'+
                    '                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                    '                Add questions'+
                    '            </a>'+
                    '        </div>'+
                    '    </td>'+
                    '</tr>';
                    $(document).find('#ays-questions-table tbody').append(quizEmptytd);
                }
                $(document).find('tr.ays-question-row').each(function () {
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    let className = ((index % 2) === 0) ? 'even' : '';
                    index++;
                    $(this).addClass(className);
                });
            }, 300);
        });

        $(document).find('input[type="checkbox"].ays-select-all').on('change', function () {
            let state = $(this).prop('checked'),
                table = $('table.ays-add-questions-table'),
                id_container = $(document).find('input#ays_already_added_questions'),
                existing_ids = id_container.val().split(',');
            if (state === false) {
                table.find('input[type="checkbox"].ays-select-single').each(function () {
                    if ($.inArray($(this).val().toString(), existing_ids) !== -1) {
                        let position = $.inArray($(this).val().toString(), existing_ids);
                        existing_ids.splice(position, 1);
                        id_container.val(existing_ids.join(','));
                        //$(document).find('tr.ays-question-row[data-id="' + $(this).val() + '"]').remove();
                    }
                });
            }
            table.find('input[type="checkbox"].ays-select-all').prop('checked', state);
            table.find('input[type="checkbox"].ays-select-single').each(function () {
                $(this).prop('checked', state);
            });
        });

        $(document).find('input[type="checkbox"].ays-select-single').on('change', function () {
            if (!$(this).prop('checked')) {
                let index = 1,
                    id_container = $(document).find('input#ays_already_added_questions'),
                    existing_ids = id_container.val().split(','),
                    question = $(this).val();
                if ($.inArray(question.toString(), existing_ids) !== -1) {
                    let position = $.inArray(question.toString(), existing_ids);
                    existing_ids.splice(position, 1);
                    id_container.val(existing_ids.join(','));
                }
                $(document).find('input[type="checkbox"].ays-select-all').prop('checked', false);
            }
        });

        let flags = [];
        $(document).find('input[type="checkbox"].ays-select-single').each(function () {
            if (!$(this).prop('checked'))
                flags.push(false);
            else
                flags.push(true);

        });

        if (flags.every(checkTrue)) {
            $(document).find('input[type="checkbox"].ays-select-all').prop('checked', true);
        }
        
        
        
        $(document).on('click', 'a.add-quiz-bg-music', function (e) {
            openMusicMediaUploader(e, $(this));
        });        
        $(document).on('click', 'a.add-quiz-image', function (e) {
            openQuizMediaUploader(e, $(this));
        });
        $(document).on('click', '.ays-remove-quiz-img', function () {
            $(this).parent().find('img#ays-quiz-img').attr('src', '');
            $('input#ays-quiz-image').val('');
            $(this).parent().fadeOut();
            $(document).find('.ays-field a.add-quiz-image').text('Add Image');
            let ays_quiz_theme = $(document).find('input[name="ays_quiz_theme"]:checked').val();
            switch (ays_quiz_theme) {
                case 'elegant_dark':
                case 'elegant_light':
                case 'rect_light':
                case 'rect_dark':
                case 'classic_dark':
                case 'classic_light':
                    $(document).find('#ays-quiz-live-image').css({'display': 'none'});
                    break;
                case 'modern_light':
                case 'modern_dark':
                    $(document).find('.ays-quiz-live-container').css({'background-image':'none'});
                    $(document).find('#ays-quiz-live-image').css({'display': 'none'});
                    break;
            }
        });
        $(document).on('click', 'a.add-quiz-bg-image', function (e) {
            openQuizMediaUploader(e, $(this));
        });
        $(document).on('click', '.ays-edit-quiz-bg-img', function (e) {
            openQuizMediaUploader(e, $(this));
        });
        
        
        let pagination = $('.ays-question-pagination');
        let pageCount = 20;
        if (pagination.length > 0) {
            createPagination(pagination, pageCount, 1);
        }

        // Tabulation
        $(document).find('.nav-tab-wrapper a.nav-tab').on('click', function (e) {
            let elemenetID = $(this).attr('href');
            let active_tab = $(this).attr('data-tab');
            $(document).find('.nav-tab-wrapper a.nav-tab').each(function () {
                if ($(this).hasClass('nav-tab-active')) {
                    $(this).removeClass('nav-tab-active');
                }
            });
            $(this).addClass('nav-tab-active');
            $(document).find('.ays-quiz-tab-content').each(function () {
                $(this).css('display', 'none');
            });
            $(document).find("[name='ays_quiz_tab']").val(active_tab);
            $(document).find("[name='ays_question_tab']").val(active_tab);
            $('.ays-quiz-tab-content' + elemenetID).css('display', 'block');
            e.preventDefault();
        });       
        
        
        
        
        $('.open-lightbox').on('click', function (e) {
            e.preventDefault();
            var image = $(this).attr('href');
            $('html').addClass('no-scroll');
            $('.ays-quiz-row ').append('<div class="lightbox-opened"><img src="' + image + '"></div>');
        });

        $('body').on('click', '.lightbox-opened', function () {
            $('html').removeClass('no-scroll');
            $('.lightbox-opened').remove();
        });

        
        $('#ays_users_roles').select2();        
        $('#ays_add_postcat_for_quiz').select2();
        
        
        $(document).find('#ays_enable_restriction_pass').on('click', function () {
            if ($(this).prop('checked')) {
                if ($(document).find('#ays_enable_logged_users').prop('checked')){
                    $(document).find('#ays_enable_logged_users').prop('disabled', true);
                }else{
                    $(document).find('#ays_enable_logged_users').trigger('click');
                    $(document).find('#ays_enable_logged_users').prop('checked', true);
                    $(document).find('#ays_enable_logged_users').prop('disabled', true);
                }
            } else {
                $(document).find('#ays_enable_logged_users').prop('disabled', false);
            }
        });
        
        if($(document).find('#ays_logged_in_message').val() == ""){
            $(document).find('#ays_logged_in_message').html('You need to log in to pass this quiz.');
        }


        $('#ays_enable_mail_user, #ays_enable_certificate').on('change', function () {
            if ($(this).prop('checked')) {
                if ($('#ays_information_form').val() === 'disable') {
                    $('#ays_information_form').find('option[value="after"]').prop('selected', true).trigger('change');
                    $('#ays_form_email').prop('checked', true);
                    $('#ays_form_name').prop('checked', true);
                }
            }
        });
        

        $('#quiz_stat_select').select2();
        $('#ays_smtp_secures').select2();
        $('#ays_paypal_currency').select2();
        $('.tablenav.top').find('.clear').before($('#filter-div'));



        $(document).find('a[href="#tab3"]').on('click',function () {
            if($(document).find('.ays_active_theme_image').length === 0){
                $(document).find('#answers_view_select').css('display','none');
            }
        });
        
        $(document).find('.ays-results-order-filter').live('click', function(e){
            e.preventDefault();
            let orderby = $(document).find('select[name="orderby"]').val();
            let link = location.href;
            if( orderby != '' ){
                orderby = "&orderby="+orderby;
                document.location.href = link+orderby;
            }else{
                document.location.href = link;
            }
        });
        
        setTimeout(function(){
            $(document).find('g title:contains("Chart created using amCharts library")').parent().remove();
        }, 1000);
        
        
        setTimeout(function(){
            if($(document).find('#ays_custom_css').length > 0){
                if(wp.codeEditor){
                    wp.codeEditor.initialize($(document).find('#ays_custom_css'), cm_settings);
                }
            }
        }, 500);

        $(document).find('a[href="#tab2"]').on('click', function (e) {        
            setTimeout(function(){
                if($(document).find('#ays_custom_css').length > 0){
                    var ays_custom_css = $(document).find('#ays_custom_css').html();
                    if(wp.codeEditor){
                        $(document).find('#ays_custom_css').next('.CodeMirror').remove();
                        wp.codeEditor.initialize($(document).find('#ays_custom_css'), cm_settings);
                        $(document).find('#ays_custom_css').html(ays_custom_css);
                    }
                }
            }, 500);
        });
        
        // Schedule of the Quiz
//        $('#active_date_check').change(function () {
//            $('.active_date').toggleClass('d-none')
//        })
        
//        $(document).on('click', '#import_toggle_button', function(e){
//            $(document).find('.upload-import-file-wrap').toggleClass('show-upload-view');
//        });
//        
//        $(document).on('change', '#import_file', function(e){
//            let pattern = /(.csv|.xls|.json)$/g;
//            if(pattern.test($(this).val())){
//                $(this).parents('form').find('input[name="import-file-submit"]').removeAttr('disabled')
//            }
//        });
        
        $('#ays_slack_client').on('input', function () {
            let clientId = $(this).val();
            if (clientId == '') {
                $("#slackOAuth2").addClass('disabled btn-outline-secondary');
                $("#slackOAuth2").removeClass('btn-secondary');
                return false;
            }
            let scopes = "channels%3Ahistory%20" +
                "channels%3Aread%20" +
                "channels%3Awrite%20" +
                "groups%3Aread%20" +
                "groups%3Awrite%20" +
                "mpim%3Aread%20" +
                "mpim%3Awrite%20" +
                "im%3Awrite%20" +
                "im%3Aread%20" +
                "chat%3Awrite%3Abot%20" +
                "chat%3Awrite%3Auser";
            let url = "https://slack.com/oauth/authorize?client_id=" + clientId + "&scope=" + scopes + "&state=" + clientId;
            $("#slackOAuth2").attr('data-src', url);//.toggleClass('disabled btn-outline-secondary btn-secondary');
            $("#slackOAuth2").removeClass('disabled btn-outline-secondary');
            $("#slackOAuth2").addClass('btn-secondary');
        });
        $("#slackOAuth2").on('click', function () {
            let url = $(this).attr('data-src');
            if (!url) {
                return false;
            }
            location.replace(url)
        });
        $('#ays_slack_secret').on('input', function(e) {
            if($(this).val() == ''){
                $("#slackOAuthGetToken").addClass('disabled btn-outline-secondary');
                $("#slackOAuthGetToken").removeClass('btn-secondary');
                return false;
            }
            
            $("#slackOAuthGetToken").removeClass('disabled btn-outline-secondary');
            $("#slackOAuthGetToken").addClass('btn-secondary');
        });
        $("#slackInstructionsPopOver").popover({
            content: $("#slackInstructions").html(),
            html: true,
//            trigger: "focus"
        });
        
        if ($('#ays-attribute-type').val() == 'select') {
            $('.ays_attr_options').show(250);
        }
        $('#ays-attribute-type').on('change', function () {
            if ($(this).val() === 'select') {
                $('.ays_attr_options').show(250);
            }
            else {
                $('.ays_attr_options').hide(250);
            }
        });
        
        $(document).find('.user-filter-apply').live('click', function(e){
            e.preventDefault();
            let catFilter = $(document).find('select[name="filterbyuser"]').val();
            let link = location.href;
            let linkFisrtPart = link.split('?')[0];
            let linkModified = link.split('?')[1].split('&');
            for(let i = 0; i < linkModified.length; i++){
                if(linkModified[i].split("=")[0] == "wpuser"){
                    linkModified.splice(i, 1);
                }
            }
            link = linkFisrtPart + "?" + linkModified.join('&');
            
            if( catFilter != '' ){
                catFilter = "&wpuser="+catFilter;
                document.location.href = link+catFilter;
            }else{
                document.location.href = link;
            }
        });
        
        $(document).find('#ays-deactive, #ays-active').datetimepicker({
            controlType: 'select',
            oneLine: true,
            dateFormat: "yy-mm-dd",
            timeFormat: "HH:mm:ss"
        });
        
        
        // Quizzes form submit
        // Checking the issues
        $(document).find('#ays-quiz-category-form').on('submit', function(e){
            
            if($(document).find('#ays-quiz-title').val() == ''){
                $(document).find('#ays-quiz-title').val('Quiz').trigger('input');
            }
            var $this = $(this)[0];
            if($(document).find('#ays-quiz-title').val() != ""){
                $this.submit();
            }else{
                e.preventDefault();
                $this.submit();
            }
        });
        
        // Delete confirmation
        $(document).on('click', '.ays_confirm_del', function(e){            
            e.preventDefault();
            var message = $(this).data('message');
            var confirm = window.confirm('Are you sure you want to delete '+message+'?');
            if(confirm === true){
                window.location.replace($(this).attr('href'));
            }
        });
    });
        
})(jQuery);
