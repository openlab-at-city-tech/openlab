jQuery(document).ready(function($){

    $( ".settings-tabs-loading").fadeOut();
    $( ".settings-tabs").fadeIn();

    accordion = $( ".settings-tabs .accordion").accordion({
        heightStyle:'content',
        active: 99,
        header: "> div > h3",
        collapsible: true,
    });

    $( ".settings-tabs .accordion[sortable='true']").sortable({
        axis: "y",
        handle: "h3",
        stop: function( event, ui ) {
            // IE doesn't register the blur when sorting
            // so trigger focusout handlers to remove .ui-state-focus
            ui.item.children( "h3" ).triggerHandler( "focusout" );

            // Refresh accordion to handle new order
            $( this ).accordion( "refresh" );
        }
    })



    $(".settings-tabs .sortable" ).sortable({ handle: ".sort" });

	$(document).on('click','.settings-tabs .tab-nav',function(){

		$(this).parent().parent().children('.tab-navs').children('.tab-nav').removeClass('active');

        $(this).addClass('active');

        id = $(this).attr('data-id');
        $('input[name="tab"]').val(id);


		//console.log('Hello click');
        //console.log(id);

        $(this).parent().parent().children('.tab-content').removeClass('active');
        $(this).parent().parent().children('.tab-content#'+id).addClass('active');

        $(this).parent().parent().children('.settings-tabs-right-panel').children('.right-panel-content').removeClass('active');
        $(this).parent().parent().children('.settings-tabs-right-panel').children('.right-panel-content-'+id).addClass('active');



    })



    // $(document).on('click','.settings-tabs .media-upload',function(){
    //
    //     dataId = $(this).attr('data-id');
    //
    //
    //
    //     var send_attachment_bkp = wp.media.editor.send.attachment;
    //
    //     wp.media.editor.send.attachment = function(props, attachment) {
    //         $("#media_preview_"+dataId).attr("src", attachment.url);
    //         $("#media_input_"+dataId).val(attachment.id);
    //         wp.media.editor.send.attachment = send_attachment_bkp;
    //     }
    //     wp.media.editor.open($(this));
    //     return false;
    // });
    //
    // $("#media_clear_<?php echo $id; ?>").click(function() {
    //     $("#media_input_<?php echo $id; ?>").val("");
    //     $("#media_preview_<?php echo $id; ?>").attr("src","");
    // })



    $(document).on('click','.settings-tabs .field-media-wrapper .media-upload',function(e){
        var side_uploader;
        this_ = $(this);
        //alert(target_input);
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (side_uploader) {
            side_uploader.open();
            return;
        }
        //Extend the wp.media object
        side_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        side_uploader.on('select', function() {
            attachment = side_uploader.state().get('selection').first().toJSON();

            attachmentId = attachment.id;
            src_url = attachment.url;
            //console.log(attachment);

            $(this_).prev().val(attachmentId);

            $(this_).parent().children('.media-preview-wrap').children('img').attr('src',src_url);

        });

        //Open the uploader dialog
        side_uploader.open();

    })



    $(document).on('click','.settings-tabs .field-media-url-wrapper .media-upload',function(e){
        var side_uploader;
        this_ = $(this);
        //alert(target_input);
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (side_uploader) {
            side_uploader.open();
            return;
        }
        //Extend the wp.media object
        side_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        side_uploader.on('select', function() {
            attachment = side_uploader.state().get('selection').first().toJSON();

            attachmentId = attachment.id;
            src_url = attachment.url;
            //console.log(attachment);

            $(this_).prev().val(src_url);

            $(this_).parent().children('.media-preview-wrap').children('img').attr('src',src_url);

        });

        //Open the uploader dialog
        side_uploader.open();

    })



    jQuery(document).on('click', '.settings-tabs .input-text-multi-wrapper .add-item',function(){

        dataName = $(this).attr('data-name');
        dataSort = $(this).attr('data-sort');
        dataClone = $(this).attr('data-clone');
        dataPlaceholder = $(this).attr('data-placeholder');

        html = '<div class="item">';
        html += '<input  type="text" name="'+dataName+'" placeholder="'+dataPlaceholder+'" />';

        if(dataClone){
            html += ' <span class="button clone"><i class="far fa-clone"></i></span>';
        }

        if(dataSort){
            html += ' <span class="button sort" ><i class="fas fa-arrows-alt"></i></span>';
        }




        html += ' <span class="button remove" onclick="jQuery(this).parent().remove()"><i class="fas fa-times"></i></span>';
        html += '</div>';


        jQuery(this).parent().children('.field-list').append(html);


       // $(".sortable" ).sortable({ handle: ".sort" });


    })



    jQuery(document).on("click", ".settings-tabs .field-repeatable-wrapper .collapsible .header .title-text", function() {
        if(jQuery(this).parent().parent().hasClass("active")){
            jQuery(this).parent().parent().removeClass("active");
        }else{
            jQuery(this).parent().parent().addClass("active");
        }
    })



    jQuery(document).on("click", ".settings-tabs .field-repeatable-wrapper .clone", function() {



    })


















    $(document).on('click', '.settings-tabs .expandable .expand', function(){
        if($(this).parent().parent().hasClass('active'))
        {
            $(this).parent().parent().removeClass('active');
        }
        else
        {
            $(this).parent().parent().addClass('active');
        }


    })





 		

});