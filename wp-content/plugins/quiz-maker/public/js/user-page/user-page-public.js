(function ($) {
    'use strict';    
    $(document).ready(function () {
        
        // for details
        $.fn.aysModal = function(action){
            var $this = $(this);
            switch(action){
                case 'hide':
                    $(this).find('.ays-modal-content').css('animation-name', 'zoomOut');
                    setTimeout(function(){
                        $(document).find('html, body').removeClass('modal-open');
                        $(document).find('.ays-modal-backdrop').remove();
                        $this.hide();
                    }, 250);
                    break;
                case 'show':
                default:
                    $this.show();
                    $(this).find('.ays-modal-content').css('animation-name', 'zoomIn');
                    $(document).find('.modal-backdrop').remove();
                    $(document.body).append('<div class="ays-modal-backdrop"></div>');
                    $(document).find('html, body').addClass('modal-open');
                    break;
            }
        }
        
        $(document).on("keydown", function(e){
            if(e.keyCode === 27){
                $(document).find('.ays-modal').aysModal('hide');
                return false;
            }
        });
        
        $(document).find('div#ays-results-modal').appendTo($(document.body));
        // Modal close
        $(document).find('.ays-close').on('click', function () {
            $(document).find('.ays-modal').aysModal('hide');
        });
        
        // User reports info Details .Open results more information popup window
        $(document).on('click', '.ays-quiz-user-sqore-pages-details', function(e){
            e.preventDefault();
            $(document).find('div.ays-quiz-preloader').css('display', 'flex');
            $(document).find('#ays-results-modal').aysModal('show');
            var this_element = $(this);
            var result_id = $(this).attr('data-id');
            var action = 'user_reports_info_popup_ajax';
            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: {
                    result: result_id,
                    action: action
                },
                success: function(response){
                    if(response.status === true){
                        $('div#ays-results-body').html(response.rows);
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                    }else{
                        swal.fire({
                            type: 'info',
                            html: "<h2>Can't load resource.</h2><br><h4>Maybe the data has been deleted.</h4>",
                        }).then(function(res){
                            $(document).find('div.ays-quiz-preloader').css('display', 'none');
                            $(document).find('.ays-modal').aysModal('hide');
                        });
                    }
                },
                error: function(){
                    swal.fire({
                        type: 'info',
                        html: "<h2>Can't load resource.</h2><br><h6>Maybe the data has been deleted.</h46>"
                    }).then(function(res){
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                        $(document).find('.ays-modal').aysModal('hide');
                    });
                }
            });
        });

        // Export result to pdf
        $(document).on('click','.ays-quiz-export-pdf', function (e) {
            var $this = $(this);
            var action = 'user_export_result_pdf';
            var result_id = $this.data('result');
            $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'flex');
            $this.attr('disabled');

            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: {
                    action: action,
                    result: result_id,
                },
                success: function (response) {
                    if (response.status) {
                        $this.parent().find('#downloadFileF').attr({
                            'href': response.fileUrl,
                            'download': response.fileName,
                        })[0].click();
                        window.URL.revokeObjectURL(response.fileUrl);
                    }else{
                        swal.fire({
                            type: 'info',
                            html: "<h2>Can't load resource.</h2><br><h4>Maybe the data has been deleted.</h4>",
                        })
                    }
                    $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'none');
                    $this.removeClass('disabled');
                }
            });
            e.preventDefault();
        });

    });
    
})(jQuery);
