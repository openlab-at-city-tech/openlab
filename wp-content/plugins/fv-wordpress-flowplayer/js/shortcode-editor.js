('use strict');
var FVFP_sStoreRTMP = 0;   
var FVFP_sWidgetId;

var fv_wp_flowplayer_content;
var fv_wp_flowplayer_hTinyMCE;
var fv_wp_flowplayer_oEditor;
var fv_wp_fp_shortcode_remains;
var fv_player_playlist_item_template;
var fv_player_playlist_video_template;
var fv_player_playlist_subtitles_template;
var fv_player_playlist_subtitles_box_template;
var fv_wp_fp_shortcode;
var fv_player_preview_single = -1;
var fv_player_preview_window;

var fv_player_editor_button_clicked = 0;

var fv_player_shortcode_preview_unsupported = false;

var fv_player_editor_matcher = {
  default: {
    // matches URL of the video
    matcher: /\.(mp4|webm|m3u8)$/i,
    // AJAX will return these fields which can be auto-updated via JS
    update_fields: ['duration', 'last_video_meta_check'],
  }
};

jQuery(document).ready(function($){
  
  var ua = window.navigator.userAgent;
  fv_player_shortcode_preview_unsupported = ua.match(/edge/i) || ua.match(/safari/i) && !ua.match(/chrome/i) ;
  
  if( jQuery().fv_player_box ) {     
    $(document).on( 'click', '.fv-wordpress-flowplayer-button, .fv-player-editor-button, .fv-player-edit', function(e) {
      fv_player_editor_button_clicked = this;
      e.preventDefault();
      $.fv_player_box( {
        top: "100px",
        initialWidth: 1100,
        initialHeight: 50,
        width:"1100px",
        height:"100px",
        href: "#fv-player-shortcode-editor",
        inline: true,
        title: 'Add FV Player',
        onComplete : fv_wp_flowplayer_edit,
        onClosed : fv_wp_flowplayer_on_close,
        onOpen: function(){
          jQuery("#fv_player_box").addClass("fv-flowplayer-shortcode-editor");
          jQuery("#cboxOverlay").addClass("fv-flowplayer-shortcode-editor");
        }
      } );
      FVFP_sWidgetId = $(this).data().number;
    });

    $(document).on( 'click', '.fv-player-export', function(e) {
      var $element = jQuery(this);

      e.preventDefault();
      $.fv_player_box( {
        top: "100px",
        initialWidth: 1100,
        initialHeight: 50,
        width:"1100px",
        height:"100px",
        href: "#fv-player-shortcode-editor",
        inline: true,
        title: 'Export FV Player',
        onComplete : function() { fv_player_export($element); },
        onClosed : fv_wp_flowplayer_big_loader_close,
        onOpen: function(){
          jQuery("#fv_player_box").addClass("fv-flowplayer-shortcode-editor");
          jQuery("#cboxOverlay").addClass("fv-flowplayer-shortcode-editor");
        }
      } );

      return false;
    });

    $(document).on( 'click', '.fv-player-import', function(e) {
      var $element = jQuery(this);

      e.preventDefault();
      $.fv_player_box( {
        top: "100px",
        initialWidth: 1100,
        initialHeight: 50,
        width:"1100px",
        height:"100px",
        href: "#fv-player-shortcode-editor",
        inline: true,
        title: 'Import FV Player(s)',
        onComplete : fv_player_import,
        onClosed : fv_wp_flowplayer_big_loader_close,
        onOpen: function(){
          jQuery("#fv_player_box").addClass("fv-flowplayer-shortcode-editor");
          jQuery("#cboxOverlay").addClass("fv-flowplayer-shortcode-editor");
        }
      } );

      return false;
    });

    $(document).on( 'click', '.fv-player-remove', function(e) {
      var $element = jQuery(this);

      $element
        .addClass('fv-player-remove-confirm')
        .removeClass('fv-player-remove')
        .html('Are you sure?');

      return false;
    });

    $(document).on( 'click', '.fv-player-remove-confirm', function(e) {
      var
        $element = jQuery(this),
        $element_td = $element.parent();

      $element_td.find('a, span').hide();
      $element.after('<div class="fv-player-shortcode-editor-small-spinner">&nbsp;</div>');

      jQuery.post(ajaxurl, {
        action: "fv_player_db_remove",
        nonce: $element.data('nonce'),
        playerID: $element.data('player_id')
      }, function(rows_affected){
        if (!isNaN(parseFloat(rows_affected)) && isFinite(rows_affected)) {
          // remove the deleted player's row
          $element.closest('tr').hide('slow', function() {
            jQuery(this).remove();
          });
        } else {
          $element.next('div.fv-player-shortcode-editor-small-spinner').css({
            'background': 'none',
            'width': 'auto'
          }).html('Error');
          
          alert(rows_affected);

          $element_td.find('span, a:not(.fv-player-remove-confirm)').show();
        }
      }).error(function() {
        $element.next('div.fv-player-shortcode-editor-small-spinner').css({
          'background': 'none',
          'width': 'auto'
        }).html('Error');

        $element_td.find('span, a:not(.fv-player-remove-confirm)').show();
      });

      return false;
    });

    $(document).on( 'click', '.fv-player-clone', function(e) {
      var $element = jQuery(this);

      $element
        .hide()
        .after('<div class="fv-player-shortcode-editor-small-spinner">&nbsp;</div>');

      jQuery.post(ajaxurl, {
        action: "fv_player_db_clone",
        nonce: $element.data('nonce'),
        playerID: $element.data('player_id')
      }, function(playerID){
        if (playerID != '0' && !isNaN(parseFloat(playerID)) && isFinite(playerID)) {
        // add the inserted player's row
        jQuery.get(
          document.location.href.substr(0, document.location.href.indexOf('?page=fv_player')) + '?page=fv_player&id=' + playerID,
          function (response) {
            jQuery('#the-list tr:first').before(jQuery(response).find('#the-list tr:first'));
            $element.next('div.fv-player-shortcode-editor-small-spinner').remove();
            $element.show();
          }).error(function() {
            $element.next('div.fv-player-shortcode-editor-small-spinner').remove();
            $element.show();
          });
        } else {
          $element.next('div.fv-player-shortcode-editor-small-spinner').css({
            'background': 'none',
            'width': 'auto'
          }).html('Error');
        }
      }).error(function() {
        $element.next('div.fv-player-shortcode-editor-small-spinner').css({
          'background': 'none',
          'width': 'auto'
        }).html('Error');
      });

      return false;
    });
    
  }
  /* 
   * NAV TABS 
   */
  $('.fv-player-tabs-header a').click( function(e) {
    e.preventDefault();
    $('.fv-player-tabs-header a').removeClass('nav-tab-active');
    $(this).addClass('nav-tab-active')
    $('.fv-player-tabs > .fv-player-tab').hide();
    $('.' + $(this).data('tab')).show();

    fv_wp_flowplayer_dialog_resize();
  });
  
  /* 
   * Select playlist item 
   * keywords: select item
   */
  $(document).on('click','.fv-player-tab-playlist tr td', function(e) {
    var new_index = $(this).parents('tr').index();
    
    fv_player_preview_single = new_index;
    
    fv_flowplayer_editor_item_show(new_index);
  });

  $(document).on('input','.fv_wp_flowplayer_field_width', function(e) {
    $('.fv_wp_flowplayer_field_width').val(e.target.value);
  })
  $(document).on('input','.fv_wp_flowplayer_field_height', function(e) {
    $('.fv_wp_flowplayer_field_height').val(e.target.value);
  })
  /*
   * Playlist view thumbnail toggle
   */
  $('#fv-player-list-thumb-toggle > a').click(function(e){
    e.preventDefault();
    var button = $(e.currentTarget);
    if(button.hasClass('disabled')) return;
    $('#fv-player-list-thumb-toggle > a').removeClass('active');
    if(button.attr('id') === 'fv-player-list-list-view'){      
      $('.fv-player-tab-playlist').addClass('hide-thumbnails');
    }else{     
      $('.fv-player-tab-playlist').removeClass('hide-thumbnails');
    }
    button.addClass('active')
  })
  
  /* 
   * Remove playlist item 
   * keywords: delete playlist items remove playlist items
   */
  $(document).on('click','.fv-player-tab-playlist tr .fvp_item_remove', function(e) {
    e.stopPropagation();
    var
      $parent = $(e.target).parents('[data-index]'),
      index = $parent.attr('data-index'),
      id = $('.fv-player-tab-video-files table[data-index=' + index + ']').attr('data-id_video'),
      $deleted_videos_element = $('#deleted_videos');

    if (id && $deleted_videos_element.val()) {
      $deleted_videos_element.val($deleted_videos_element.val() + ',' + id);
    } else {
      $deleted_videos_element.val(id);
    }

    $parent.remove();
    jQuery('.fv-player-tab-video-files table[data-index=' + index + ']').remove();
    jQuery('.fv-player-tab-subtitles table[data-index=' + index + ']').remove();
    if(!jQuery('.fv-player-tab-subtitles table[data-index]').length){
      fv_flowplayer_playlist_add();
      jQuery('.fv-player-tab-playlist tr td').click();
    }
    
    fv_wp_flowplayer_submit('refresh-button');
  });
  
  /*
   *  Sort playlist  
   */
  $('.fv-player-tab-playlist table tbody').sortable({
    start: function( event, ui ) {
      FVFP_sStoreRTMP = jQuery('#fv-flowplayer-playlist table:first .fv_wp_flowplayer_field_rtmp').val();
    },
    update: function( event, ui ) {    
      var items = []; 
      $('.fv-player-tab-playlist table tbody tr').each(function(){
        var
          index = $(this).data('index'),
          $items = jQuery('.fv-player-tab-video-files table[data-index=' + index + ']'),
          $subs = jQuery('.fv-player-tab-subtitles table[data-index=' + index + ']');

        items.push({
          items : $items.clone(),
          subs : $subs.clone(),
        });

        $items.remove();
        $subs.remove();
      });

      for(var  i in items){
        if(!items.hasOwnProperty(i))continue;
        jQuery('.fv-player-tab-video-files').append(items[i].items);
        jQuery('.fv-player-tab-subtitles').append(items[i].subs);
      }
     
      jQuery('#fv-flowplayer-playlist table:first .fv_wp_flowplayer_field_rtmp').val( FVFP_sStoreRTMP );
      
      fv_wp_flowplayer_submit('refresh-button');      
    },
    axis: 'y',
    //handle: '.fvp_item_sort',
    containment: ".fv-player-tab-playlist"
  });
  
  /*
   * Uploader 
   */
  var fv_flowplayer_uploader;
  var fv_flowplayer_uploader_button;

  $(document).on( 'click', '#fv-player-shortcode-editor .button.add_media', function(e) {
      e.preventDefault();
      
      fv_flowplayer_uploader_button = jQuery(this);
      jQuery('.fv_flowplayer_target').removeClass('fv_flowplayer_target' );
      fv_flowplayer_uploader_button.siblings('input[type=text]').addClass('fv_flowplayer_target' );
                       
      //If the uploader object has already been created, reopen the dialog
      if (fv_flowplayer_uploader) {
          fv_flowplayer_uploader.open();
          return;
      }

      //Extend the wp.media object
      fv_flowplayer_uploader = wp.media.frames.file_frame = wp.media({
          title: 'Add Video',
          button: {
              text: 'Choose'
          },
          multiple: false
      });
      
      fv_flowplayer_uploader.on('open', function() {
        $( document ).trigger( "mediaBrowserOpen" );
        jQuery('.media-router .media-menu-item').eq(0).click();
        jQuery('.media-frame-title h1').text(fv_flowplayer_uploader_button.text());
      });      

      //When a file is selected, grab the URL and set it as the text field's value
      fv_flowplayer_uploader.on('select', function() {
          attachment = fv_flowplayer_uploader.state().get('selection').first().toJSON();

          $('.fv_flowplayer_target').val(attachment.url);
          $('.fv_flowplayer_target').removeClass('fv_flowplayer_target' );
        
          if( attachment.type == 'video' ) {
            if( typeof(attachment.width) != "undefined" && attachment.width > 0 ) {
              $('#fv_wp_flowplayer_field_width').val(attachment.width);
            }
            if( typeof(attachment.height) != "undefined" && attachment.height > 0 ) {
              $('#fv_wp_flowplayer_field_height').val(attachment.height);
            }
            if( typeof(attachment.fileLength) != "undefined" ) {
              $('#fv_wp_flowplayer_file_info').show();
              $('#fv_wp_flowplayer_file_duration').html(attachment.fileLength);
            }
            if( typeof(attachment.filesizeHumanReadable) != "undefined" ) {
              $('#fv_wp_flowplayer_file_info').show();
              $('#fv_wp_flowplayer_file_size').html(attachment.filesizeHumanReadable);
            }
            
          } else if( attachment.type == 'image' && typeof(fv_flowplayer_set_post_thumbnail_id) != "undefined" ) {
            if( jQuery('#remove-post-thumbnail').length > 0 ){
              return;
            }
            jQuery.post(ajaxurl, {
                action:"set-post-thumbnail",
                post_id: fv_flowplayer_set_post_thumbnail_id,
                thumbnail_id: attachment.id,
                 _ajax_nonce: fv_flowplayer_set_post_thumbnail_nonce,
                cookie: encodeURIComponent(document.cookie)
              }, function(str){
                var win = window.dialogArguments || opener || parent || top;
                if ( str == '0' ) {
                  alert( setPostThumbnailL10n.error );
                } else {
                  jQuery('#postimagediv .inside').html(str);
                  jQuery('#postimagediv .inside #plupload-upload-ui').hide();
                }
              } );
            
          }
          
          fv_wp_flowplayer_submit('refresh-button');
      });

      //Open the uploader dialog
      fv_flowplayer_uploader.open();

  });
  
  fv_player_playlist_item_template = jQuery('.fv-player-tab-playlist table tbody tr').parent().html();
  fv_player_playlist_video_template = jQuery('.fv-player-tab-video-files table.fv-player-playlist-item').parent().html();
  fv_player_playlist_subtitles_template = jQuery('.fv-fp-subtitle').parent().html();
  fv_player_playlist_subtitles_box_template = jQuery('.fv-player-tab-subtitles').html();

  var $document = jQuery(document);

  /*
   * Preview
   */
  $document.on('input', '.fv-player-tabs [name][data-live-update!=false]' ,function(){
    if( !fv_player_shortcode_preview_unsupported && jQuery('.fv-player-tab-playlist tr').length < 10 ){
      jQuery('#fv-player-shortcode-editor-preview-iframe-refresh').show();
    }
  });
  
  var fv_player_shortcode_click_element = null;
  $document.mousedown(function(e) {
      fv_player_shortcode_click_element = jQuery(e.target);
  });

  $document.mouseup(function(e) {
      fv_player_shortcode_click_element = null;
  });

  $document.on('blur', '.fv-player-tabs [name][data-live-update!=false]' ,function(){
    if( fv_player_shortcode_click_element && fv_player_shortcode_click_element.hasClass('button-primary') ) {
      return;
    }
    
    fv_wp_flowplayer_submit('refresh-button');
  });

  $document.on('keypress', '.fv-player-tabs [name][data-live-update!=false]' ,function(e){
    if(e.key === 'Enter') {
      fv_wp_flowplayer_submit(true);
    }
  });
  
  jQuery('#fv-player-shortcode-editor-preview-iframe-refresh').click(function(){
    jQuery('#fv-player-shortcode-editor-preview-iframe-refresh').hide();
    
    fv_wp_flowplayer_submit(true);
  });
  
  /*
   * End of playlist Actions   
   */
 
  jQuery('#fv_wp_flowplayer_field_end_actions').change(function(){
    var value = jQuery(this).val();
    jQuery('.fv_player_actions_end-toggle').hide().find('[name]').val('');
    switch(value){
      case 'redirect': 
        jQuery('#fv_wp_flowplayer_field_' + value).parents('tr').show(); 
        break; 
      case 'popup':
        jQuery('#fv_wp_flowplayer_field_' + value).parents('tr').show();
        jQuery('#fv_wp_flowplayer_field_' + value + '_id').parents('tr').show();
        break;
      case 'email_list':
        jQuery('#fv_wp_flowplayer_field_' + value).parents('tr').show();
        break;
      default:        
        fv_wp_flowplayer_submit('refresh-button');
        break;
    }
  });
  
  /*
   * Preview iframe dialog resize
   */
  $document.on('fvp-preview-complete',function(e,width,height){
    fv_player_shortcode_preview = false;
    jQuery('#fv-player-shortcode-editor-preview').attr('class','preview-show');
    setTimeout(function(){
      fv_wp_flowplayer_dialog_resize();
    },0);
  });
  
  /*
   * Video share option
   */
 
  jQuery('#fv_wp_flowplayer_field_share').change(function(){
    var value = jQuery(this).val();
    
    switch(value){
      case 'Custom': 
        jQuery("#fv_wp_flowplayer_field_share_custom").show();
        break;
      default:        
        jQuery("#fv_wp_flowplayer_field_share_custom").hide();
        break;
    }
  });

});



/*
 * Initializes shortcode, removes playlist items, hides elements
 */
function fv_wp_flowplayer_init() {
  // if error / message overlay is visible, hide it
  fv_wp_flowplayer_big_loader_close();

  // remove Insert as New button, or it'll all get renamed to Update
  // when working with original shortcode
  // jQuery('.fv_player_insert_as_new').remove();

  // remove hidden meta data inputs
  jQuery('input[name="fv_wp_flowplayer_field_duration"], input[name="fv_wp_flowplayer_field_last_video_meta_check"], input[name="fv_wp_flowplayer_field_auto_splash"], input[name="fv_wp_flowplayer_field_auto_caption"]').remove();

  // stop and remove any pending AJAX requests to retrieve video meta data
  // as well as any auto-update timers
  jQuery('input[name="fv_wp_flowplayer_field_src"]').each(function() {
    var
      $this = jQuery(this),
      ajaxData = $this.data('fv_player_video_data_ajax'),
      //refreshTask = $this.data('fv_player_video_auto_refresh_task'),
      retryData = $this.data('fv_player_video_data_ajax_retry_count');

    if (typeof(ajaxData) != 'undefined') {
      ajaxData.abort();
      $this.removeData('fv_player_video_data_ajax');
    }

    if (typeof(retryData) != 'undefined') {
      $this.removeData('fv_player_video_data_ajax_retry_count');
    }

    /*if (typeof(refreshTask) != 'undefined') {
      clearInterval(refreshTask);
      $this.removeData('fv_player_video_auto_refresh_task');
    }*/
  });

  fv_wp_flowplayer_dialog_resize_height_record = 0;
  fv_player_shortcode_preview = false;
  fv_player_shortcode_editor_last_url = false;

  jQuery('#fv_wp_flowplayer_field_player_name').show();

  jQuery('#player_id_top_text').html('');

  var field = jQuery(fv_player_editor_button_clicked).parents('.fv-player-editor-wrapper, .fv-player-gutenberg').find('.fv-player-editor-field');
  if( field.length ) {
    fv_wp_flowplayer_content = jQuery(field).val();

  } else if( jQuery('#widget-widget_fvplayer-'+FVFP_sWidgetId+'-text').length ){
    fv_wp_flowplayer_content = jQuery('#widget-widget_fvplayer-'+FVFP_sWidgetId+'-text').val();
  } else if( typeof(FCKeditorAPI) == 'undefined' && jQuery('#content:not([aria-hidden=true])').length){
    fv_wp_flowplayer_content = jQuery('#content:not([aria-hidden=true])').val();
  } else if( typeof tinymce !== 'undefined' && typeof tinymce.majorVersion !== 'undefined' && typeof tinymce.activeEditor !== 'undefined' && tinymce.majorVersion >= 4 ){
    fv_wp_flowplayer_hTinyMCE = tinymce.activeEditor;
  } else if( typeof tinyMCE !== 'undefined' ) {
    fv_wp_flowplayer_hTinyMCE = tinyMCE.getInstanceById('content');
  } else if(typeof(FCKeditorAPI) !== 'undefined' ){
    fv_wp_flowplayer_oEditor = FCKeditorAPI.GetInstance('content');
  }
  
  jQuery('#fv_wp_flowplayer_file_info').hide();
  jQuery(".fv_wp_flowplayer_field_src2_wrapper").hide();
  jQuery("#fv_wp_flowplayer_field_src2_uploader").hide();
  jQuery(".fv_wp_flowplayer_field_src1_wrapper").hide();
  jQuery("#fv_wp_flowplayer_field_src1_uploader").hide();
  jQuery("#add_format_wrapper").show();
  jQuery(".add_rtmp_wrapper").show(); 
  jQuery(".fv_wp_flowplayer_field_rtmp_wrapper").hide();
  jQuery('#fv-player-shortcode-editor-preview').attr('class','preview-no');
  
  jQuery('.fv-player-tab-video-files table').each( function(i,e) {
    if( i == 0 ) return;
    jQuery(e).remove();
  } );
  
  jQuery('.fv-player-tab-playlist table tbody tr').each( function(i,e) {
    if( i == 0 ) return;
    jQuery(e).remove();
  } );
  
  jQuery('.fv-player-tab-subtitles').html(fv_player_playlist_subtitles_box_template);
  
  jQuery('.fv_wp_flowplayer_field_subtitles_lang').val(0);

  /**
   * TABS 
   */ 
  jQuery('#fv-player-shortcode-editor a[data-tab=fv-player-tab-playlist]').hide();
  jQuery('#fv-player-shortcode-editor a[data-tab=fv-player-tab-video-files]').trigger('click');
  jQuery('.nav-tab').show;
  
  //hide empy tabs hide tabs
  jQuery('#fv-player-shortcode-editor-editor').attr('class','is-singular');
  jQuery('.fv-player-tab-playlist').hide();
  jQuery('.fv-player-playlist-item-title').html('');
  jQuery('.fv-player-tab-video-files table').show();
  
  jQuery('.playlist_edit').html(jQuery('.playlist_edit').data('create')).removeClass('button-primary').addClass('button');

  fv_player_refresh_tabs();
  
  jQuery('#fv-player-shortcode-editor-preview-target').html('');
  
  if( typeof(fv_player_shortcode_editor_ajax) != "undefined" ) {
    fv_player_shortcode_editor_ajax.abort();
  }
}

/*
 * Sends new shortcode to editor
 */
function fv_wp_flowplayer_insert( shortcode ) {
  if (typeof(jQuery(fv_player_editor_button_clicked).data('player_id')) == 'undefined' && typeof(jQuery(fv_player_editor_button_clicked).data('add_new')) == 'undefined') {
    var field = jQuery(fv_player_editor_button_clicked).parents('.fv-player-editor-wrapper').find('.fv-player-editor-field');
    var gutenberg = jQuery(fv_player_editor_button_clicked).parents('.fv-player-gutenberg').find('.fv-player-editor-field');
    
    if( gutenberg.length ) {
      var nativeInputValueSetter = Object.getOwnPropertyDescriptor(window.HTMLTextAreaElement.prototype, "value").set;
      nativeInputValueSetter.call(gutenberg[0], shortcode);
      var ev2 = new Event('change', { bubbles: true});
      gutenberg[0].dispatchEvent(ev2,shortcode);
      
    } else if (field.length) {
      field.val(shortcode);
      field.trigger('fv_flowplayer_shortcode_insert', [shortcode]);

    } else if (typeof(FCKeditorAPI) == 'undefined' && jQuery('#content:not([aria-hidden=true])').length) {
      fv_wp_flowplayer_content = fv_wp_flowplayer_content.replace(/#fvp_placeholder#/, shortcode);
      fv_wp_flowplayer_set_html(fv_wp_flowplayer_content);
    } else if (fv_wp_flowplayer_content.match(fv_wp_flowplayer_re_edit)) {
      fv_wp_flowplayer_content = fv_wp_flowplayer_content.replace(fv_wp_flowplayer_re_edit, shortcode)
      fv_wp_flowplayer_set_html(fv_wp_flowplayer_content);
    }
    else {
      if (fv_wp_flowplayer_content != '') {
        fv_wp_flowplayer_content = fv_wp_flowplayer_content.replace(fv_wp_flowplayer_re_insert, shortcode)
        fv_wp_flowplayer_set_html(fv_wp_flowplayer_content);
      } else {
        fv_wp_flowplayer_content = shortcode;
        send_to_editor(shortcode);
      }
    }
  }
}

/*
 * Removes playlist item 
 * keywords: remove palylist item
 */
function fv_wp_flowplayer_playlist_remove(link) {
  FVFP_sStoreRTMP = jQuery('#fv-flowplayer-playlist table:first .fv_wp_flowplayer_field_rtmp').val();
	jQuery(link).parents('table').remove();
  jQuery('#fv-flowplayer-playlist table:first .fv_wp_flowplayer_field_rtmp').val( FVFP_sStoreRTMP );
	return false;
}

/*
 * Adds playlist item
 * keywords: add playlist item
 */
function fv_flowplayer_playlist_add( sInput, sCaption, sSubtitles, sSplashText ) {
  jQuery('.fv-player-tab-playlist table tbody').append(fv_player_playlist_item_template);
  var ids = jQuery('.fv-player-tab-playlist [data-index]').map(function() {
    return parseInt(jQuery(this).attr('data-index'), 10);
  }).get();  
  var newIndex = Math.max(Math.max.apply(Math, ids) + 1,0);
  var current = jQuery('.fv-player-tab-playlist table tbody tr').last();
  current.attr('data-index', newIndex);
  current.find('.fvp_item_video-filename').html( 'Video ' + (newIndex + 1) );
  
  jQuery('.fv-player-tab-video-files').append(fv_player_playlist_video_template);
  var new_item = jQuery('.fv-player-tab-video-files table:last');
  new_item.hide().attr('data-index', newIndex);

  jQuery('.fv-player-tab-subtitles').append(fv_player_playlist_subtitles_box_template);
  var new_item_subtitles = jQuery('.fv-player-tab-subtitles table:last');
  new_item_subtitles.hide().attr('data-index', newIndex);
  
  //jQuery('.fv-player-tab-video-files table').hover( function() { jQuery(this).find('.fv_wp_flowplayer_playlist_remove').show(); }, function() { jQuery(this).find('.fv_wp_flowplayer_playlist_remove').hide(); } );

  if( typeof(sInput) == 'object' ) {
    var objVid = sInput;
    
    new_item.attr('data-id_video', objVid.id);    
    
    new_item.find('[name=fv_wp_flowplayer_field_src]').val(objVid.src);
    if( objVid.src1 ) {
      new_item.find('[name=fv_wp_flowplayer_field_src1]').val(objVid.src1);
      new_item.find(".fv_wp_flowplayer_field_src1_wrapper").css( 'display', 'table-row' );
    }
    if( objVid.src2 ) {
      new_item.find('[name=fv_wp_flowplayer_field_src2]').val(objVid.src2);
      new_item.find(".fv_wp_flowplayer_field_src2_wrapper").css( 'display', 'table-row' );
      new_item.find('#fv_wp_flowplayer_add_format_wrapper').show();
    }
    new_item.find('[name=fv_wp_flowplayer_field_mobile]').val(objVid.mobile);
    
    if( objVid.rtmp || objVid.rtmp_path ) {
      new_item.find('[name=fv_wp_flowplayer_field_rtmp]').val(objVid.rtmp);
      new_item.find('[name=fv_wp_flowplayer_field_rtmp_path]').val(objVid.rtmp_path);
      new_item.find(".fv_wp_flowplayer_field_rtmp_wrapper").show();
      new_item.find(".add_rtmp_wrapper").hide();
    }  
    
    new_item.find('[name=fv_wp_flowplayer_field_caption]').val(objVid.caption);
    new_item.find('[name=fv_wp_flowplayer_field_splash]').val(objVid.splash);
    new_item.find('[name=fv_wp_flowplayer_field_splash_text]').val(objVid.splash_text);
    
    new_item.find('[name=fv_wp_flowplayer_field_start]').val(objVid.start);
    new_item.find('[name=fv_wp_flowplayer_field_end]').val(objVid.end);
    
    jQuery(objVid.meta).each( function(k,v) {
      if( v.meta_key == 'audio' ) new_item.find('[name=fv_wp_flowplayer_field_audio]').prop('checked',v.meta_value).attr('data-id',v.id);
    });    
    
    if (typeof sSubtitles === 'object' && sSubtitles.length && sSubtitles[0].lang) {
      // DB-based subtitles value
      var firstDone = false;

      for (var i in sSubtitles) {
        // add as many new subtitles as we have
        if (firstDone) {
          fv_flowplayer_language_add(sSubtitles[i].file, sSubtitles[i].lang, newIndex, sSubtitles[i].id);
        } else {
          var
            subElement = jQuery('[name=fv_wp_flowplayer_field_subtitles_lang]',new_item_subtitles),
            $parent = subElement.parent();

          subElement.val(sSubtitles[i].lang);

          var subIndex = subElement.get(0).selectedIndex;
          jQuery(subElement.get(0).options[subIndex]).attr('selected', 'selected');

          $parent.attr('data-id_subtitles', sSubtitles[i].id);
          $parent.hover( function() { jQuery(this).find('.fv-fp-subtitle-remove').show(); }, function() { jQuery(this).find('.fv-fp-subtitle-remove').hide(); } );
          $parent.find('.fv-fp-subtitle-remove').click(fv_flowplayer_remove_subtitles);

          jQuery('[name=fv_wp_flowplayer_field_subtitles]',new_item_subtitles).val(sSubtitles[i].file);
          firstDone = true;
        }
      }
    }   

  } else if( sInput ) {
    var aInput = sInput.split(',');
    var count = 0;
    for( var i in aInput ) {
      if( aInput[i].match(/^rtmp:/) ) {
        new_item.find('.fv_wp_flowplayer_field_rtmp_path').val(aInput[i].replace(/^rtmp:/,''));
      } else if( aInput[i].match(/\.(jpg|png|gif|jpe|jpeg)(?:\?.*?)?$/) ) {
        new_item.find('.fv_wp_flowplayer_field_splash').val(aInput[i]);      
      } else {
        if( count == 0 ) {
          new_item.find('#fv_wp_flowplayer_field_src').val(aInput[i]);
        } else {
          new_item.find('#fv_wp_flowplayer_field_src_'+count).val(aInput[i]);
        }
        count++;
      }
    }
    if( sCaption ) {
      jQuery('[name=fv_wp_flowplayer_field_caption]',new_item).val(sCaption);
    }
    if( sSubtitles ) {
      jQuery('[name=fv_wp_flowplayer_field_subtitles]', new_item_subtitles).val(sSubtitles);
    }
    if( sSplashText ) {
      jQuery('[name=fv_wp_flowplayer_field_splash_text]', new_item).val(sSplashText);
    }
  }
  
  fv_wp_flowplayer_dialog_resize(); 
  return new_item;
}

/*
 * Displays playlist editor
 * keywords: show playlist 
 */
function fv_flowplayer_playlist_show() {

  jQuery('#fv-player-shortcode-editor-editor').attr('class','is-playlist-active');
  jQuery('.fv-player-tabs-header .nav-tab').attr('style',false);
  jQuery('a[data-tab=fv-player-tab-playlist]').click();
  
  fv_player_preview_single = -1;
  
  //fills playlist edistor table from individual video tables
  var video_files = jQuery('.fv-player-tab-video-files table');
  video_files.each( function() {
    var current = jQuery(this);

    var currentUrl = current.find('#fv_wp_flowplayer_field_src').val();
    if(!currentUrl.length){
      currentUrl = 'Video ' + (jQuery(this).index() + 1);
    }
    var playlist_row = jQuery('.fv-player-tab-playlist table tbody tr').eq( video_files.index(current) );

    current.attr('data-index', current.index() );
    playlist_row.attr('data-index', current.index() );
    
    var video_preview = current.find('#fv_wp_flowplayer_field_splash').val();
    playlist_row.find('.fvp_item_video-thumbnail').html( video_preview.length ? '<img src="' + video_preview + '" />':'');
    
    var video_name = decodeURIComponent(currentUrl).split("/").pop();
    video_name = video_name.replace(/\+/g,' ');
    video_name = video_name.replace(/watch\?v=/,'YouTube: ');
    
    playlist_row.find('.fvp_item_video-filename').html( video_name );

    var playlist_row_div = playlist_row.find('.fvp_item_caption div');
    if (!playlist_row_div.hasClass('fv-player-shortcode-editor-small-spinner')) {
      playlist_row_div.html(current.find('#fv_wp_flowplayer_field_caption').val());
    }
  });
  //initial indexing
  jQuery('.fv-player-tab.fv-player-tab-subtitles table').each(function(){
    jQuery(this).attr('data-index', jQuery(this).index() );
  })
  
  if(!jQuery('.fvp_item_video-thumbnail>img').length){
    jQuery('#fv-player-list-list-view').click();
    jQuery('#fv-player-list-thumb-view').addClass('disabled');
    jQuery('#fv-player-list-thumb-view').attr('title',jQuery('#fv-player-list-thumb-view').data('title'));
  }else{
    jQuery('#fv-player-list-thumb-view').click();
    jQuery('#fv-player-list-thumb-view').removeClass('disabled');
    jQuery('#fv-player-list-thumb-view').removeAttr('title');
  }
  
  jQuery('.fv-player-tab-playlist').show();
  fv_wp_flowplayer_dialog_resize();
  fv_player_refresh_tabs();
  fv_wp_flowplayer_submit(true);

  return false;
}

function fv_flowplayer_editor_item_show( new_index ) {
  jQuery('.fv-player-tabs-header .nav-tab').attr('style',false);    
 
  var $ = jQuery;
  
  $(document).trigger('fv_flowplayer_shortcode_item_switch', [ new_index ] );
 
  $('a[data-tab=fv-player-tab-video-files]').click();    
  
  $('.fv-player-tab-video-files table').hide();
  var video_tab = $('.fv-player-tab-video-files table').eq(new_index).show();
  
  $('.fv-player-tab-subtitles table').hide();
  var subtitles_tab = $('.fv-player-tab-subtitles table').eq(new_index).show();
  

  if($('.fv-player-tab-playlist [data-index]').length > 1){
    $('.fv-player-playlist-item-title').html('Playlist item no. ' + ++new_index);
    $('.playlist_edit').html($('.playlist_edit').data('edit')).removeClass('button').addClass('button-primary');
    jQuery('#fv-player-shortcode-editor-editor').attr('class','is-playlist');
  }else{
    $('.playlist_edit').html($('.playlist_edit').data('create')).removeClass('button-primary').addClass('button');
    jQuery('#fv-player-shortcode-editor-editor').attr('class','is-singular');
  }
  
  if($('.fv_wp_flowplayer_field_rtmp_path',video_tab).val().length === 0 && $('.fv_wp_flowplayer_field_rtmp',video_tab).val().length === 0){
    $('.fv_wp_flowplayer_field_rtmp_wrapper',video_tab).hide();
    $('.add_rtmp_wrapper',video_tab).show();
  }else{
    $('.fv_wp_flowplayer_field_rtmp_wrapper',video_tab).show();
    $('.add_rtmp_wrapper',video_tab).hide();
  }
  if(new_index > 1){
    $('.fv_wp_flowplayer_field_rtmp',video_tab).val($('.fv_wp_flowplayer_field_rtmp',$('.fv-player-tab-video-files table').eq(0)).val());
    $('.fv_wp_flowplayer_field_rtmp',video_tab).attr('readonly',true);
  }
  
  $('.fv_wp_flowplayer_field_subtitles_lang, .fv_flowplayer_language_add_link').attr('style',false);

  fv_player_refresh_tabs();

  // hide chapters and transcript when not the first video in playlist
  $('.fv-player-tab-subtitles table:gt(0)').each(function() {
    var $e = $(this);
    $e.find('.fv_wp_flowplayer_field_transcript').parents('tr:first').hide();
    $e.find('#fv_wp_flowplayer_field_chapters').parents('tr:first').hide();
  });

  fv_wp_flowplayer_submit(true);  
}

function fv_flowplayer_remove_subtitles() {
  if(jQuery(this).parents('.fv-fp-subtitles').find('.fv-fp-subtitle').length > 1){
    var
      $parent = jQuery(this).parents('.fv-fp-subtitle'),
      id = $parent.attr('data-id_subtitles')

    if (id) {
      fv_wp_delete_video_meta_record(id);
    }

    $parent.remove();
  }else{
    var
      $parent = jQuery(this).parents('.fv-fp-subtitle'),
      id = $parent.attr('data-id_subtitles')

    if (id) {
      fv_wp_delete_video_meta_record(id);
    }

    $parent.find('[name]').val('');
    $parent.removeAttr('data-id_subtitles');
  }
  fv_wp_flowplayer_dialog_resize();

  return false;
}

/*
 * Adds another language to subtitle menu
 */
function fv_flowplayer_language_add( sInput, sLang, iTabIndex, sId ) {
  if(!iTabIndex){
    var current = jQuery('.fv-player-tab-subtitles table:visible');
    iTabIndex = current.length && current.data('index') ? current.data('index') : 0;
  }
  var oTab = jQuery('.fv-fp-subtitles').eq(iTabIndex);
  oTab.append( fv_player_playlist_subtitles_template ); 

  var subElement = jQuery('.fv-fp-subtitle:last' , oTab);
  subElement.hover( function() { jQuery(this).find('.fv-fp-subtitle-remove').show(); }, function() { jQuery(this).find('.fv-fp-subtitle-remove').hide(); } );

  if (typeof(sId) !== 'undefined') {
    subElement.attr('data-id_subtitles', sId);
  }
  
  if( sInput ) {
    jQuery('.fv-fp-subtitle:last input.fv_wp_flowplayer_field_subtitles' , oTab ).val(sInput);
  }
  
  if ( sLang ) {
    if( sLang == 'iw' ) sLang = 'he';
    if( sLang == 'in' ) sLang = 'id';
    if( sLang == 'jw' ) sLang = 'jv';
    if( sLang == 'mo' ) sLang = 'ro';
    if( sLang == 'sh' ) sLang = 'sr';
    
    jQuery('.fv-fp-subtitle:last select.fv_wp_flowplayer_field_subtitles_lang' , oTab).val(sLang);
    var
      sel = jQuery('.fv-fp-subtitle:last select.fv_wp_flowplayer_field_subtitles_lang' , oTab).get(0),
      index = sel.selectedIndex,
      $selectedOption = jQuery(sel.options[index]);

    $selectedOption.attr('selected', 'selected');
  }
  
  jQuery('.fv-fp-subtitle:last .fv-fp-subtitle-remove' , oTab).click(fv_flowplayer_remove_subtitles);
  
  fv_wp_flowplayer_dialog_resize();
  return false;
}

function fv_wp_flowplayer_map_names_to_editor_fields(name) {
  var fieldMap = {
    'liststyle': 'playlist',
    'preroll': 'video_ads',
    'postroll': 'video_ads_post'
  };

  return 'fv_wp_flowplayer_field_' + (fieldMap[name] ? fieldMap[name] : name);
}

function fv_wp_flowplayer_map_db_values_to_field_values(name, value) {
  switch (name) {
    case 'playlist_advance':
      return ((value == 'true' || value == 'on') ? 'on' : (value == 'default' || value == '') ? 'default' : 'off');
      break;

    default: return value;
  }
}

/*
 * removes previous values from editor
 * fills new values from shortcode
 */
function fv_wp_flowplayer_edit() {

  var dialog = jQuery('#fv_player_box.fv-flowplayer-shortcode-editor');
  dialog.removeAttr('tabindex');
  
  fv_wp_flowplayer_init();

  // remove any DB data IDs that may be left in the form
  jQuery('#fv-player-shortcode-editor [data-id]').removeData('id').removeAttr('data-id');
  jQuery('#fv-player-shortcode-editor [data-id_video]').removeData('id_video').removeAttr('data-id_video');
  jQuery('#fv-player-shortcode-editor [data-id_subtitles]').removeData('id_subtitles').removeAttr('data-subtitles');

  // fire up editor reset event, so plugins can clear up their data IDs as well
  var $doc = jQuery(document);
  $doc.trigger('fv_flowplayer_player_editor_reset');

  jQuery("#fv-player-shortcode-editor input:not(.extra-field)").each( function() { jQuery(this).val( '' ); jQuery(this).attr( 'checked', false ) } );
  jQuery("#fv-player-shortcode-editor textarea").each( function() { jQuery(this).val( '' ) } );
  jQuery('#fv-player-shortcode-editor select').prop('selectedIndex',0);
  jQuery("[name=fv_wp_flowplayer_field_caption]").each( function() { jQuery(this).val( '' ) } );
  jQuery("[name=fv_wp_flowplayer_field_splash_text]").each( function() { jQuery(this).val( '' ) } );
  jQuery(".fv_player_field_insert-button").attr( 'value', 'Insert' );
  
  var field = jQuery(fv_player_editor_button_clicked).parents('.fv-player-editor-wrapper, .fv-player-gutenberg').find('.fv-player-editor-field');
  if( field.length || jQuery('#widget-widget_fvplayer-'+FVFP_sWidgetId+'-text').length){
    //  this is a horrible hack as it adds the hidden marker to the otherwise clean text field value just to make sure the shortcode varible below is parsed properly. But it allows some extra text to be entered into the text widget, so for now - ok
    if(fv_wp_flowplayer_content.match(/\[/) ) {
      fv_wp_flowplayer_content = '[<'+fvwpflowplayer_helper_tag+' rel="FCKFVWPFlowplayerPlaceholder">&shy;</'+fvwpflowplayer_helper_tag+'>'+fv_wp_flowplayer_content.replace('[','')+'';
    } else {
      fv_wp_flowplayer_content =   '<'+fvwpflowplayer_helper_tag+' rel="FCKFVWPFlowplayerPlaceholder">&shy;</'+fvwpflowplayer_helper_tag+'>'+fv_wp_flowplayer_content+'';
    }
    
  }else if( typeof(FCKeditorAPI) == 'undefined' && jQuery('#content:not([aria-hidden=true])').length ){    
    var bFound = false;
    var position = jQuery('#content:not([aria-hidden=true])').prop('selectionStart');
    for(var start = position; start--; start >= 0){
      if( fv_wp_flowplayer_content[start] == '['){
        bFound = true; break;
      }else if(fv_wp_flowplayer_content[start] == ']'){
        break
      }
    }
    var shortcode = [];
   
    if(bFound){    
      var temp = fv_wp_flowplayer_content.slice(start);
      temp = temp.match(/^\[fvplayer[^\[\]]*]?/);
      if(temp){
        shortcode = temp;
        fv_wp_flowplayer_content = fv_wp_flowplayer_content.slice(0, start) + '#fvp_placeholder#' + fv_wp_flowplayer_content.slice(start).replace(/^\[[^\[\]]*]?/, '');
      }else{
        fv_wp_flowplayer_content = fv_wp_flowplayer_content.slice(0, position) + '#fvp_placeholder#' + fv_wp_flowplayer_content.slice(position);
      }
    }else{
      fv_wp_flowplayer_content = fv_wp_flowplayer_content.slice(0, position) + '#fvp_placeholder#' + fv_wp_flowplayer_content.slice(position);
    }   
  } else if (typeof(jQuery(fv_player_editor_button_clicked).data('player_id')) != 'undefined') {
    // create an artificial shortcode from which we can extract the actual player ID later below
    fv_wp_flowplayer_content = '[fvplayer id="' + jQuery(fv_player_editor_button_clicked).data('player_id') + '"]';
    shortcode = [fv_wp_flowplayer_content];
  } else if (typeof(jQuery(fv_player_editor_button_clicked).data('add_new')) != 'undefined') {
  // create empty shortcode for Add New button on the list page
    fv_wp_flowplayer_content = '';
    shortcode = '';
  } else	if( fv_wp_flowplayer_hTinyMCE == undefined || tinyMCE.activeEditor.isHidden() ) {
    fv_wp_flowplayer_content = fv_wp_flowplayer_oEditor.GetHTML();    
    if (fv_wp_flowplayer_content.match( fv_wp_flowplayer_re_insert ) == null) {
      fv_wp_flowplayer_oEditor.InsertHtml('<'+fvwpflowplayer_helper_tag+' rel="FCKFVWPFlowplayerPlaceholder">&shy;</'+fvwpflowplayer_helper_tag+'>');
      fv_wp_flowplayer_content = fv_wp_flowplayer_oEditor.GetHTML();    
    }           
	}
	else {
    fv_wp_flowplayer_content = fv_wp_flowplayer_hTinyMCE.getContent();
    fv_wp_flowplayer_hTinyMCE.settings.validate = false;
    if (fv_wp_flowplayer_content.match( fv_wp_flowplayer_re_insert ) == null) {   
      var tags = ['b','span','div'];
      for( var i in tags ){
        fv_wp_flowplayer_hTinyMCE.execCommand('mceInsertContent', false,'<'+tags[i]+' data-mce-bogus="1" rel="FCKFVWPFlowplayerPlaceholder"></'+tags[i]+'>');
        fv_wp_flowplayer_content = fv_wp_flowplayer_hTinyMCE.getContent();
        
        fv_wp_flowplayer_re_edit = new RegExp( '\\[f[^\\]]*?<'+tags[i]+'[^>]*?rel="FCKFVWPFlowplayerPlaceholder"[^>]*?>.*?</'+tags[i]+'>.*?[^\]\\]', "mi" );
        fv_wp_flowplayer_re_insert = new RegExp( '<'+tags[i]+'[^>]*?rel="FCKFVWPFlowplayerPlaceholder"[^>]*?>.*?</'+tags[i]+'>', "gi" );
        
        if( fv_wp_flowplayer_content.match(fv_wp_flowplayer_re_insert) ){
          break;
        }
        
      }
      
    }
    fv_wp_flowplayer_hTinyMCE.settings.validate = true;		
	}

  var content = fv_wp_flowplayer_content.replace(/\n/g, '\uffff');          
  if(typeof(shortcode) == 'undefined'){
    var shortcode = content.match( fv_wp_flowplayer_re_edit );  
  }

  // remove visual editor placeholders etc.
  if (shortcode && shortcode[0]) {
    shortcode = shortcode[0]
      .replace(/^\[|]+$/gm, '')
      .replace(fv_wp_flowplayer_re_insert, '')
      .replace(/\\'/g, '&#039;');
  }

  if( shortcode != null && typeof(shortcode) != 'undefined' && typeof(shortcode[0]) != 'undefined') {
    // check for new, DB-based player shortcode
    var result = /fvplayer id="([\d,]+)"/g.exec(shortcode);
    if (result !== null) {
      var
        shortcode_parse_fix = shortcode
                                .replace(/(popup|ad)='[^']*?'/g, '')
                                .replace(/(popup|ad)="(.*?[^\\\\/])"/g, '');

      fv_wp_fp_shortcode_remains = shortcode_parse_fix.replace( /^\S+\s*?/, '' );

      fv_flowplayer_conf.db_extra_shortcode_params = {};
      var preserve = [ 'playlist_start', 'autoplay', 'sort', 'logo', 'width', 'height', 'controlbar', 'embed', 'ab', 'share', 'liststyle', 'playlist_hide', 'playlist_advance', 'ad', 'ad_height', 'ad_width', 'vast', 'midroll' ];
      for( var i in preserve ) {
        var value = fv_wp_flowplayer_shortcode_parse_arg( shortcode_parse_fix, preserve[i] );
        if (value && value[1]) {
          fv_flowplayer_conf.db_extra_shortcode_params[preserve[i]] = value[1];
        }
      }

      fv_flowplayer_conf.new_shortcode_active = true;
      // DB-based player, create a "wait" overlay
      fv_wp_flowplayer_big_loader_show();

      // store player ID into fv_player_conf, so we can keep sending it
      // in WP heartbeat
      fv_flowplayer_conf.current_player_db_id = result[1];

      if (fv_flowplayer_conf.fv_flowplayer_edit_lock_removal) {
        delete fv_flowplayer_conf.fv_flowplayer_edit_lock_removal[result[1]];
      }

      // check if we don't have multiple-playlists shortcode,
      // in which case we need to stop and show an error message
      if (shortcode.indexOf(',') > -1) {
        fv_wp_flowplayer_big_loader_show('Shortcode editor is not available for multiple players shortcode tag.');
        return;
      }

      // now load playlist data
      // load video data via an AJAX call
      fv_player_shortcode_editor_ajax = jQuery.post(ajaxurl, {
        action : 'fv_player_db_load',
        nonce : fv_player_editor_conf.db_load_nonce, 
        playerID :  result[1]
      }, function(response) {
        var vids = response['videos'];

        if (response) {
          if( typeof(response) != "object" ) {
            fv_wp_flowplayer_big_loader_show('Error: '+response);
            return;
          }
          
          var
            $id_player_element = jQuery('#id_player'),
            $deleted_videos_element = jQuery('#deleted_videos'),
            $deleted_video_meta_element = jQuery('#deleted_video_meta'),
            $deleted_player_meta_element = jQuery('#deleted_player_meta');

          // remove everything with index 0 and the initial video placeholder,
          // otherwise our indexing & previews wouldn't work correctly
          jQuery('[data-index="0"]').remove();
          jQuery('.fv-player-tab-playlist table tbody tr').remove();
          jQuery('.fv-player-tab-video-files table').remove();

          jQuery('#player_id_top_text').html('ID: ' + result[1]);

          if (!$id_player_element.length) {
            // add player ID as a hidden field
            jQuery('#fv-player-shortcode-editor').append('<input type="hidden" name="id_player" id="id_player" value="' + result[1] + '" />');

            // add removed video IDs as a hidden field
            jQuery('#fv-player-shortcode-editor').append('<input type="hidden" name="deleted_videos" id="deleted_videos" />');

            // add removed video meta IDs as a hidden field
            jQuery('#fv-player-shortcode-editor').append('<input type="hidden" name="deleted_video_meta" id="deleted_video_meta" />');

            // add removed player meta IDs as a hidden field
            jQuery('#fv-player-shortcode-editor').append('<input type="hidden" name="deleted_player_meta" id="deleted_player_meta" />');
          } else {
            $id_player_element.val(result[1]);
            $deleted_videos_element.val('');
            $deleted_video_meta_element.val('');
            $deleted_player_meta_element.val('');
          }

          // fire the player load event to cater for any plugins listening
          var $doc = jQuery(document);
          $doc.trigger('fv_flowplayer_player_meta_load', [response]);

          // used several times below, so it's in a function
          function set_player_field(key, value, id, video_table_index) {
            var
              real_key = fv_wp_flowplayer_map_names_to_editor_fields(key),
              real_val = fv_wp_flowplayer_map_db_values_to_field_values(key, value),
              // try ID first
              $element = jQuery((typeof(video_table_index) != 'undefined' ? '.fv-player-tab table[data-id_video=' + video_table_index + '] ' : '') + '#' + real_key);

            // special processing for end video actions
            if (real_key == 'fv_wp_flowplayer_field_end_action_value') {
              var end_of_playlist_action = jQuery('#fv_wp_flowplayer_field_end_actions').val();

              // to actually show the value, we need to trigger a change event on the end_actions dropdown itself
              jQuery('#fv_wp_flowplayer_field_end_actions').trigger('change');

              switch (end_of_playlist_action) {
                case 'redirect':
                  jQuery('#fv_wp_flowplayer_field_redirect').val(value);
                  break;

                case 'popup':
                  jQuery('#fv_wp_flowplayer_field_popup_id').val(value);
                  break;

                case 'email_list':
                  jQuery('#fv_wp_flowplayer_field_email_list').val(value);
                  break;
              }

              return;
            } else if (['fv_wp_flowplayer_field_email_list', 'fv_wp_flowplayer_field_popup_id', 'fv_wp_flowplayer_field_redirect'].indexOf(real_key) > -1) {
              // ignore the original fields, if we still use old DB values
              return;
            }

            if (!$element.length) {
              // no element with this ID found, we need to go for a name
              $element = jQuery((typeof(video_table_index) != 'undefined' ? '.fv-player-tab table[data-id_video=' + video_table_index + '] ' : '') + '[name="' + real_key + '"]');
            }

            // player and video IDs wouldn't have corresponding fields
            if ($element.length) {
              // dropdowns could have capitalized values
              if ($element.get(0).nodeName == 'SELECT') {
                if ($element.find('option[value="' + real_val + '"]').length) {
                  $element.val(real_val);
                } else {
                  // try capitalized
                  var caps = real_val.charAt(0).toUpperCase() + real_val.slice(1);
                  $element.find('option').each(function() {
                    if (this.text == caps) {
                      jQuery(this).attr('selected', 'selected');
                    }
                  });
                }
              } else if ($element.get(0).nodeName == 'INPUT' && $element.get(0).type.toLowerCase() == 'checkbox') {
                if (real_val === '1' || real_val === 'on' || real_val === 'true') {
                  $element.attr('checked', 'checked');
                } else {
                  $element.removeAttr('checked');
                }
              } else {
                $element.val(real_val);
              }

              // if an ID exists, this is a meta field
              // and the data id needs to be added to it as well
              if (typeof(id) != 'undefined') {
                $element.attr('data-id', id);
              }
            }
          }

          for (var key in response) {
            // put the field value where it belongs
            if (key !== 'videos') {
              // in case of meta data, proceed with each player meta one by one
              if (key == 'meta') {
                for (var i in response[key]) {
                  set_player_field(response[key][i]['meta_key'], response[key][i]['meta_value'], response[key][i]['id']);
                }
              } else {
                set_player_field(key, response[key]);
              }
            }
          }

          // add videos from the DB
          for (var x in vids) {
            var
              subs = [],
              transcript = null,
              chapters = null,
              video_meta = [];

            // add all subtitles, chapters and transcripts
            if (vids[x].meta && vids[x].meta.length) {
              for (var m in vids[x].meta) {
                // subtitles
                if (vids[x].meta[m].meta_key.indexOf('subtitles') > -1) {
                  subs.push({
                    lang: vids[x].meta[m].meta_key.replace('subtitles_', ''),
                    file: vids[x].meta[m].meta_value,
                    id: vids[x].meta[m].id
                  });
                }

                // chapters
                if (vids[x].meta[m].meta_key.indexOf('chapters') > -1) {
                  chapters = {
                    id: vids[x].meta[m].id,
                    value: vids[x].meta[m].meta_value
                  };
                }

                // transcript
                if (vids[x].meta[m].meta_key.indexOf('transcript') > -1) {
                  transcript = {
                    id: vids[x].meta[m].id,
                    value: vids[x].meta[m].meta_value
                  };
                }

                // general video meta
                if (vids[x].meta[m].meta_key.indexOf('live') > -1 || ['duration', 'last_video_meta_check', 'auto_splash', 'auto_caption'].indexOf(vids[x].meta[m].meta_key) > -1) {
                  video_meta.push(vids[x].meta[m]);
                }
              }
            }

            $video_data_tab = fv_flowplayer_playlist_add(vids[x], false, subs);
            $subtitles_tab = $video_data_tab.parents('.fv-player-tabs:first').find('.fv-player-tab-subtitles table:eq(' + $video_data_tab.data('index') + ')');

            // add chapters and transcript
            if (chapters){
              $subtitles_tab.find('#fv_wp_flowplayer_field_chapters').val(chapters.value).attr('data-id', chapters.id);
            }

            if (transcript) {
              $subtitles_tab.find('.fv_wp_flowplayer_field_transcript').val(transcript.value).attr('data-id', transcript.id);
            }

            if (video_meta.length) {
              for (var i in video_meta) {
                // video duration hidden input
                if (['duration', 'last_video_meta_check', 'auto_splash', 'auto_caption'].indexOf(video_meta[i].meta_key) > -1) {
                  $video_data_tab.find('#fv_wp_flowplayer_field_src').after('<input type="hidden" name="fv_wp_flowplayer_field_' + video_meta[i].meta_key + '" id="fv_wp_flowplayer_field_' + video_meta[i].meta_key + '" value="' + video_meta[i].meta_value + '" data-id="' + video_meta[i].id + '" />');
                } else {
                  // predefined meta input with field already existing in the dialog
                  set_player_field(video_meta[i].meta_key, video_meta[i].meta_value, video_meta[i].id, video_meta[i].id_video);
                }
              }
            }

            // fire up meta load event for this video, so plugins can process it and react
            $doc.trigger('fv_flowplayer_video_meta_load', [x, vids[x].meta, $video_data_tab]);
          }

          // show playlist instead of the "add new video" form
          // if we have more than 1 video
          if( fv_flowplayer_conf.current_video_to_edit ) {
            fv_flowplayer_editor_item_show(fv_flowplayer_conf.current_video_to_edit);
          } else if (vids.length > 1) {
            fv_flowplayer_playlist_show();
          } else {
            fv_flowplayer_editor_item_show(0);
          }

          // copy the Insert button, place it after the first original one
          // and rename it to Insert as New
          /*var
            $insert_button = jQuery('.fv_player_field_insert-button:not(.fv_player_insert_as_new)').filter(function() {
              return jQuery(this).parents('.fv-player-playlist-item').length === 0;
            }),
            $insert_as_new_button = jQuery('.fv_player_insert_as_new');

          if (!$insert_as_new_button.length) {
            jQuery($insert_button[0].outerHTML)
              .addClass('fv_player_insert_as_new')
              .val('Insert as New')
              .off('click')
              .on('click', function () {
                // remove update and deleted hidden fields + DB IDs so we insert a new record
                // with our data instead of updating them
                jQuery('#id_player, #deleted_videos, #deleted_video_meta, #deleted_player_meta').remove();
                jQuery('#fv-player-shortcode-editor [data-id]').removeData('id').removeAttr('data-id');
                jQuery('#fv-player-shortcode-editor [data-id_video]').removeData('id_video').removeAttr('data-id_video');
                jQuery('#fv-player-shortcode-editor [data-id_subtitles]').removeData('id_subtitles').removeAttr('data-subtitles');

                fv_wp_flowplayer_submit(null, true);
                return true;
              })
              .css('margin-left', '5px')
              .insertAfter($insert_button);
          } else {
            $insert_as_new_button.val('Insert as New');
          }*/

          // rename insert to update if we're actually editing
          jQuery('.fv_player_field_insert-button').val('Update');
        }

        fv_wp_flowplayer_big_loader_close();
      }).error(function(xhr) {
        if (xhr.status == 404) {
          fv_wp_flowplayer_big_loader_show('The requested player could not be found. Please try again.');
        } else {
          fv_wp_flowplayer_big_loader_show('An unexpected error has occurred. Please try again.');
        }
      });
    } else {
      fv_flowplayer_conf.new_shortcode_active = false;

      // ordinary text shortcode in the editor
      var shortcode_parse_fix = shortcode.replace(/(popup|ad)='[^']*?'/g, '');
      shortcode_parse_fix = shortcode_parse_fix.replace(/(popup|ad)="(.*?[^\\\\/])"/g, '');
      fv_wp_fp_shortcode_remains = shortcode_parse_fix.replace( /^\S+\s*?/, '' );

      var srcurl = fv_wp_flowplayer_shortcode_parse_arg( shortcode_parse_fix, 'src' );
      var srcurl1 = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'src1' );
      var srcurl2 = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'src2' );

      var srcrtmp = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'rtmp' );
      var srcrtmp_path = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'rtmp_path' );

      var iwidth = fv_wp_flowplayer_shortcode_parse_arg( shortcode_parse_fix, 'width' );
      var iheight = fv_wp_flowplayer_shortcode_parse_arg( shortcode_parse_fix, 'height' );

      var sad_skip = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'ad_skip' );
      var salign = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'align' );
      var scontrolbar = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'controlbar' );
      var sautoplay = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'autoplay' );
      var sliststyle = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'liststyle' );
      var sembed = fv_wp_flowplayer_shortcode_parse_arg( shortcode_parse_fix, 'embed' );
      var sloop = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'loop' );
      var slive = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'live' );
      var sshare = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'share', false, fv_wp_flowplayer_share_parse_arg );
      var sspeed = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'speed' );
      var ssplash = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'splash' );
      var ssplashend = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'splashend' );
      var ssticky = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'sticky' );

      var splaylist_advance = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'playlist_advance' );

      var ssubtitles = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'subtitles' );
      var aSubtitlesLangs = shortcode.match(/subtitles_[a-z][a-z]+/g);
      for( var i in aSubtitlesLangs ){  //  move
        fv_wp_flowplayer_shortcode_parse_arg( shortcode, aSubtitlesLangs[i], false, fv_wp_flowplayer_subtitle_parse_arg );
      }
      if(!aSubtitlesLangs){ //  move
        fv_flowplayer_language_add(false, false );
      }

      var smobile = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'mobile' );
      var sredirect = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'redirect' );

      var sCaptions = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'caption' );
      var sSplashText = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'splash_text' );
      var sPlaylist = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'playlist' );

      var sad = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'ad', true );
      var iadwidth = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'ad_width' );
      var iadheight = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'ad_height' );


      if( srcrtmp != null && srcrtmp[1] != null ) {
        jQuery(".fv_wp_flowplayer_field_rtmp").val( srcrtmp[1] );
        jQuery(".fv_wp_flowplayer_field_rtmp_wrapper").show();
        jQuery(".add_rtmp_wrapper").hide();
      }
      if( srcrtmp_path != null && srcrtmp_path[1] != null ) {
        jQuery(".fv_wp_flowplayer_field_rtmp_path").val( srcrtmp_path[1] );
        jQuery(".fv_wp_flowplayer_field_rtmp_wrapper").show();
        jQuery(".add_rtmp_wrapper").hide();
      }
      var playlist_row = jQuery('.fv-player-tab-playlist tbody tr:first')

      if( srcurl != null && srcurl[1] != null )
        document.getElementById("fv_wp_flowplayer_field_src").value = srcurl[1];
      if( srcurl1 != null && srcurl1[1] != null ) {
        document.getElementById("fv_wp_flowplayer_field_src1").value = srcurl1[1];
        jQuery(".fv_wp_flowplayer_field_src1_wrapper").css( 'display', 'table-row' );
        //document.getElementById("fv_wp_flowplayer_field_src1_uploader").style.display = 'table-row';
        if( srcurl2 != null && srcurl2[1] != null ) {
          document.getElementById("fv_wp_flowplayer_field_src2").value = srcurl2[1];
          jQuery(".fv_wp_flowplayer_field_src2_wrapper").css( 'display', 'table-row' );
          //document.getElementById("fv_wp_flowplayer_field_src2_uploader").style.display = 'table-row';
          document.getElementById("add_format_wrapper").style.display = 'none';
        }
      }

      if( srcurl != null && srcurl[1] != null ) {
        document.getElementById("fv_wp_flowplayer_field_src").value = srcurl[1];
        playlist_row.find('.fvp_item_video-filename').html( srcurl[1] );
      }

      jQuery('.fv_wp_flowplayer_field_width').val(iwidth[1] || '');
      jQuery('.fv_wp_flowplayer_field_height').val(iheight[1] || '');


      if( sautoplay != null && sautoplay[1] != null ) {
        if (sautoplay[1] == 'true')
          document.getElementById("fv_wp_flowplayer_field_autoplay").selectedIndex = 1;
        if (sautoplay[1] == 'false')
          document.getElementById("fv_wp_flowplayer_field_autoplay").selectedIndex = 2;
      }
      if( sliststyle != null && sliststyle[1] != null ) {
        var objPlaylistStyle = document.getElementById("fv_wp_flowplayer_field_playlist");
        if (sliststyle[1] == 'tabs') objPlaylistStyle.selectedIndex = 1;
        if (sliststyle[1] == 'prevnext') objPlaylistStyle.selectedIndex = 2;
        if (sliststyle[1] == 'vertical') objPlaylistStyle.selectedIndex = 3;
        if (sliststyle[1] == 'horizontal') objPlaylistStyle.selectedIndex = 4;
        if (sliststyle[1] == 'text') objPlaylistStyle.selectedIndex = 5;
        if (sliststyle[1] == 'slider') objPlaylistStyle.selectedIndex = 6;
      }
      if( sembed != null && sembed[1] != null ) {
        if (sembed[1] == 'true')
          document.getElementById("fv_wp_flowplayer_field_embed").selectedIndex = 1;
        if (sembed[1] == 'false')
          document.getElementById("fv_wp_flowplayer_field_embed").selectedIndex = 2;
      }
      if( smobile != null && smobile[1] != null )
        document.getElementById("fv_wp_flowplayer_field_mobile").value = smobile[1];

      if( ssplash != null && ssplash[1] != null ) {
        document.getElementById("fv_wp_flowplayer_field_splash").value = ssplash[1];
        playlist_row.find('.fvp_item_splash').html( '<img width="120" src="'+ssplash[1]+'" />' );
      }

      var aSubtitles = false;
      if( ssubtitles != null && ssubtitles[1] != null ) {
        aSubtitles = ssubtitles[1].split(';');
        jQuery(".fv_wp_flowplayer_field_subtitles").eq(0).val( aSubtitles[0] );
        aSubtitles.shift();  //  the first item is no longer needed for playlist parsing which will follow
      }

      if( ssticky != null && ssticky[1] != null ) {
        if (ssticky[1] == 'true')
          document.getElementById("fv_wp_flowplayer_field_sticky").selectedIndex = 1;
        if (ssticky[1] == 'false')
          document.getElementById("fv_wp_flowplayer_field_sticky").selectedIndex = 2;
      }

      if( sad != null && sad[1] != null ) {
        sad = sad[1].replace(/&#039;/g,'\'').replace(/&quot;/g,'"').replace(/&lt;/g,'<').replace(/&gt;/g,'>');
        sad = sad.replace(/&amp;/g,'&');
        document.getElementById("fv_wp_flowplayer_field_ad").value = sad;
      }

      if( iadheight != null && iadheight[1] != null )
        document.getElementById("fv_wp_flowplayer_field_ad_height").value = iadheight[1];
      if( iadwidth != null && iadwidth[1] != null )
        document.getElementById("fv_wp_flowplayer_field_ad_width").value = iadwidth[1];
      if( sad_skip != null && sad_skip[1] != null && sad_skip[1] == 'yes' )
        document.getElementById("fv_wp_flowplayer_field_ad_skip").checked = 1;

      if( sspeed != null && sspeed[1] != null ) {
        if (sspeed[1] == 'buttons')
          document.getElementById("fv_wp_flowplayer_field_speed").selectedIndex = 1;
        if (sspeed[1] == 'no')
          document.getElementById("fv_wp_flowplayer_field_speed").selectedIndex = 2;
      }
      /*
      if( ssplashend != null && ssplashend[1] != null && ssplashend[1] == 'show' )
        document.getElementById("fv_wp_flowplayer_field_splashend").checked = 1;
      if( sloop != null && sloop[1] != null && sloop[1] == 'true' )
        document.getElementById("fv_wp_flowplayer_field_loop").checked = 1;
      if( sredirect != null && sredirect[1] != null )
        document.getElementById("fv_wp_flowplayer_field_redirect").value = sredirect[1];
      */

      if( sSplashText != null && sSplashText[1] != null ) {
        document.getElementById("fv_wp_flowplayer_field_splash_text").value = sSplashText[1];
      }


      /*
       * Video end dropdown
       */
      document.getElementById("fv_wp_flowplayer_field_popup").parentNode.style.display = 'none'
      var spopup = fv_wp_flowplayer_shortcode_parse_arg( shortcode, 'popup', true );

      if( sredirect != null && sredirect[1] != null ){
        document.getElementById("fv_wp_flowplayer_field_end_actions").selectedIndex = 1;
        document.getElementById("fv_wp_flowplayer_field_redirect").value = sredirect[1];
        jQuery('#fv_wp_flowplayer_field_redirect').parents('tr').show();
      }else if( sloop != null && sloop[1] != null && sloop[1] == 'true' ){
        document.getElementById("fv_wp_flowplayer_field_end_actions").selectedIndex = 2;
      }else if( spopup != null && spopup[1] != null ) {
        document.getElementById("fv_wp_flowplayer_field_end_actions").selectedIndex = 3;

        spopup = spopup[1].replace(/&#039;/g,'\'').replace(/&quot;/g,'"').replace(/&lt;/g,'<').replace(/&gt;/g,'>');
        spopup = spopup.replace(/&amp;/g,'&');

        jQuery("#fv_wp_flowplayer_field_popup_id").parents('tr').show();

        if (spopup === null || !isNaN(parseInt(spopup)) || spopup === 'no' || spopup === 'random' || spopup === 'email_list') {
          jQuery("#fv_wp_flowplayer_field_popup_id").val(spopup)
        } else if( spopup.match(/email-[0-9]*/)){
          jQuery("#fv_wp_flowplayer_field_popup_id").parent().parent().hide();
          jQuery("#fv_wp_flowplayer_field_email_list").parent().parent().show();
          jQuery("#fv_wp_flowplayer_field_end_actions").val('email_list');
          jQuery("#fv_wp_flowplayer_field_email_list").val(spopup.match(/email-([0-9]*)/)[1]);
        }else {
          jQuery("#fv_wp_flowplayer_field_popup").val(spopup).parent().show();
        }

      }else if( ssplashend != null && ssplashend[1] != null && ssplashend[1] == 'show' ){
        document.getElementById('fv_wp_flowplayer_field_end_actions').selectedIndex = 4
      }

      if( splaylist_advance != null && splaylist_advance[1] != null ) {
        if (splaylist_advance[1] == 'true')
          document.getElementById("fv_wp_flowplayer_field_playlist_advance").selectedIndex = 1;
        if (splaylist_advance[1] == 'false')
          document.getElementById("fv_wp_flowplayer_field_playlist_advance").selectedIndex = 2;
      }


      if( salign != null && salign[1] != null ) {
        if (salign[1] == 'left')
          document.getElementById("fv_wp_flowplayer_field_align").selectedIndex = 1;
        if (salign[1] == 'right')
          document.getElementById("fv_wp_flowplayer_field_align").selectedIndex = 2;
      }

      if( scontrolbar != null && scontrolbar[1] != null ) {
        if (scontrolbar[1] == 'yes' || scontrolbar[1] == 'show' )
          document.getElementById("fv_wp_flowplayer_field_controlbar").selectedIndex = 1;
        if (scontrolbar[1] == 'no' || scontrolbar[1] == 'hide' )
          document.getElementById("fv_wp_flowplayer_field_controlbar").selectedIndex = 2;
      }

      var aCaptions = false;
      if( sCaptions ) {
        aCaptions = fv_flowplayer_shortcode_editor_cleanup(sCaptions);

        var caption = aCaptions.shift();
        jQuery('[name=fv_wp_flowplayer_field_caption]',jQuery('.fv-player-playlist-item').eq(0)).val( caption );
        playlist_row.find('.fvp_item_caption div').html( caption );
      }

      var aSplashText = false;
      if( sSplashText ) {
        aSplashText = fv_flowplayer_shortcode_editor_cleanup(sSplashText);

        var splash_text = aSplashText.shift();
        jQuery('[name=fv_wp_flowplayer_field_splash_text]',jQuery('.fv-player-playlist-item').eq(0)).val( splash_text );
      }

      if( sPlaylist ) {
        // check for all-numeric playlist items separated by commas
        // which outlines video IDs from a database
        aPlaylist = sPlaylist[1].split(';');
        for (var i in aPlaylist) {
          fv_flowplayer_playlist_add(aPlaylist[i], aCaptions[i], aSubtitles[i], aSplashText[i]);
        }
      }

      if( jQuery('.fv-fp-subtitles .fv-fp-subtitle:first input.fv_wp_flowplayer_field_subtitles').val() == '' ) {
        jQuery('.fv-fp-subtitles .fv-fp-subtitle:first').remove();
      }

      jQuery(document).trigger('fv_flowplayer_shortcode_parse', [ shortcode_parse_fix, fv_wp_fp_shortcode_remains ] );

      jQuery(".fv_player_field_insert-button").attr( 'value', 'Update' );

      jQuery('.fv_wp_flowplayer_playlist_head').hover(
        function() { jQuery(this).find('.fv_wp_flowplayer_playlist_remove').show(); }, function() { jQuery(this).find('.fv_wp_flowplayer_playlist_remove').hide(); } );

      //???
      jQuery('#cboxContent').css('background','white');
      
      if (slive != null && slive[1] != null && slive[1] == 'true') {
        jQuery("input[name=fv_wp_flowplayer_field_live]").each(function () {
          this.checked = 1;
        });
      }


      if(sPlaylist){
        fv_flowplayer_playlist_show();
      } else {
        fv_flowplayer_editor_item_show(0);
      }
      
      //initial preview
      fv_player_refresh_tabs();

      fv_wp_flowplayer_submit(true);
    }
	} else {
    jQuery(document).trigger('fv_flowplayer_shortcode_new');
    fv_wp_fp_shortcode_remains = '';

    // rename insert to save for new playlists if we come from list view
    if (typeof(jQuery(fv_player_editor_button_clicked).data('add_new')) != 'undefined') {
      jQuery('.fv_player_field_insert-button:not(.fv_player_insert_as_new)').val('Save');
    }
  }
}



function fv_wp_delete_player_meta_record(id) {
  var $element = jQuery('#deleted_player_meta');

  if ($element.val()) {
    $element.val($element.val() + ',' + id);
  } else  {
    $element.val(id);
  }
}



function fv_wp_delete_video_meta_record(id) {
  var $element = jQuery('#deleted_video_meta');

  if ($element.val()) {
    $element.val($element.val() + ',' + id);
  } else  {
    $element.val(id);
  }
}



function fv_wp_flowplayer_dialog_resize() {
  var iContentHeight = jQuery('#fv-player-shortcode-editor').height();
  if( iContentHeight < 50 ) iContentHeight = 50;
  if( iContentHeight > jQuery(window).height() - 160 ) iContentHeight = jQuery(window).height() - 160;
  
  iContentHeight = iContentHeight + 50; 
  
  if( typeof(fv_wp_flowplayer_dialog_resize_height_record) == 'undefined' || fv_wp_flowplayer_dialog_resize_height_record <= iContentHeight ) {
    fv_wp_flowplayer_dialog_resize_height_record = iContentHeight;
    jQuery('#fv-player-shortcode-editor').fv_player_box.resize({width:1100, height:iContentHeight})
  }
}


function fv_wp_flowplayer_on_close() {
  //fv_player_editor_button_clicked = false;  //  todo: is it not too early?

  fv_wp_flowplayer_init();

  if (typeof(jQuery(fv_player_editor_button_clicked).data('player_id')) == 'undefined' && typeof(jQuery(fv_player_editor_button_clicked).data('add_new')) == 'undefined') {
    fv_wp_flowplayer_set_html( fv_wp_flowplayer_content.replace( fv_wp_flowplayer_re_insert, '' ) );
    
  } else {
    var
      $buttonClicked = jQuery(fv_player_editor_button_clicked),
      playerID = $buttonClicked.data('player_id'),
      playerRow = jQuery('#the-list span[data-player_id="' + playerID + '"]');

    // check if we didn't use Insert as New button
    if (typeof($buttonClicked.data('insert_as_new_id')) != 'undefined') {
      jQuery.get(
        document.location.href.substr(0, document.location.href.indexOf('?page=fv_player')) + '?page=fv_player&id=' + $buttonClicked.data('insert_as_new_id'),
        function (response) {
          if (typeof(jQuery(fv_player_editor_button_clicked).data('player_id')) == 'undefined' || typeof(jQuery(fv_player_editor_button_clicked).data('add_new')) != 'undefined') {
            jQuery('#the-list tr:first').before(jQuery(response).find('#the-list tr:first'));
          }
        });
    } else {
      if (typeof($buttonClicked.data('insert_id')) != 'undefined') {
        // reload our player's row
        playerRow.append('&nbsp; <div class="fv-player-shortcode-editor-small-spinner">&nbsp;</div>');
        jQuery.get(
          document.location.href.substr(0, document.location.href.indexOf('?page=fv_player')) + '?page=fv_player&id=' + $buttonClicked.data('insert_id'),
          function (response) {
            jQuery('#the-list span[data-player_id="' + playerID + '"]').closest('tr').replaceWith(jQuery(response).find('#the-list tr'));
          });
      }
    }
  }

  if (fv_flowplayer_conf.current_player_db_id){
    if (!fv_flowplayer_conf.fv_flowplayer_edit_lock_removal) {
      fv_flowplayer_conf.fv_flowplayer_edit_lock_removal = {};
    }

    fv_flowplayer_conf.fv_flowplayer_edit_lock_removal[fv_flowplayer_conf.current_player_db_id] = 1;
    delete fv_flowplayer_conf.current_player_db_id;
  }
  
  if( fv_flowplayer_conf.current_video_to_edit ) {
    delete fv_flowplayer_conf.current_video_to_edit;
  }

  // fv_player_editor_button_clicked = false; // TODO: this was commented out on top of this function, do we need this at all?
}   


function fv_wp_flowplayer_set_html( html ) {
  var field = jQuery(fv_player_editor_button_clicked).parents('.fv-player-editor-wrapper').find('.fv-player-editor-field');
  var gutenberg = jQuery(fv_player_editor_button_clicked).parents('.fv-player-gutenberg').find('.fv-player-editor-field');
  
  if( gutenberg.length ) {
    var nativeInputValueSetter = Object.getOwnPropertyDescriptor(window.HTMLTextAreaElement.prototype, "value").set;
    nativeInputValueSetter.call(gutenberg[0], html);
    var ev2 = new Event('change', { bubbles: true});
    gutenberg[0].dispatchEvent(ev2,html);
    
  } else if( field.length ) {
    field.val(html);
    field.trigger('fv_flowplayer_shortcode_insert', [ html ] );

  } else if( jQuery('#widget-widget_fvplayer-'+FVFP_sWidgetId+'-text').length ){
    jQuery('#widget-widget_fvplayer-'+FVFP_sWidgetId+'-text').val(html);      
    jQuery('#widget-widget_fvplayer-'+FVFP_sWidgetId+'-text').trigger('fv_flowplayer_shortcode_insert', [ html ] );
  }else if( typeof(FCKeditorAPI) == 'undefined' && jQuery('#content:not([aria-hidden=true])').length ){
    jQuery('#content:not([aria-hidden=true])').val(html); 
  }else if( fv_wp_flowplayer_hTinyMCE == undefined || tinyMCE.activeEditor.isHidden() ) {
    fv_wp_flowplayer_oEditor.SetHTML( html );      
  }
  else {		
    fv_wp_flowplayer_hTinyMCE.setContent( html );
  }
}



function fv_wp_flowplayer_get_correct_dropdown_value(optionsHaveNoValue, $valueLessOptions, dropdown_element) {
  // at least one option is value-less
  if ($valueLessOptions.length) {
    if (optionsHaveNoValue) {
      // all options are value-less - the first one is always default and should be sent as ''
      return (dropdown_element.selectedIndex === 0 ? '' : dropdown_element.value);
    } else {
      // some options are value-less
      if ($valueLessOptions.length > 1) {
        // multiple value-less options, while some other options do have a value - this should never be
        console.log('ERROR - Unhandled exception occurred while trying to get player values: more than 1 value-less options found');
        return false;
      } else {
        // single option is value-less (
        return (dropdown_element.selectedIndex === 0 ? '' : dropdown_element.value);
      }
    }
  } else {
    // normal dropdown - all options have a value, return this.value (option's own value)
    return dropdown_element.value;
  }
}



function fv_wp_flowplayer_build_ajax_data() {
  var
      $editor                = jQuery('#fv-player-shortcode-editor')
      $tabs                  = $editor.find('.fv-player-tab'),
      regex                  = /((fv_wp_flowplayer_field_|fv_wp_flowplayer_hlskey|fv_player_field_ppv_)[^ ]*)/g,
      data                   = {'video_meta' : {}, 'player_meta' : {}},
      end_of_playlist_action = jQuery('#fv_wp_flowplayer_field_end_actions').val(),
      single_video_showing   = jQuery('input[name="fv_wp_flowplayer_field_src"]:visible').length,
      single_video_id        = (single_video_showing ? jQuery('input[name="fv_wp_flowplayer_field_src"]:visible').closest('table').data('index') : -1);

  // special processing for end video actions
  if (end_of_playlist_action && end_of_playlist_action != 'Nothing') {
    switch (end_of_playlist_action) {
      case 'redirect':
        data['fv_wp_flowplayer_field_end_action_value'] = jQuery('#fv_wp_flowplayer_field_redirect').val();
        break;

      case 'popup':
        data['fv_wp_flowplayer_field_end_action_value'] = jQuery('#fv_wp_flowplayer_field_popup_id').val();
        break;

      case 'email_list':
        data['fv_wp_flowplayer_field_end_action_value'] = jQuery('#fv_wp_flowplayer_field_email_list').val();
        break;
    }
  }

  // add playlist name
  data['fv_wp_flowplayer_field_player_name'] = jQuery('#fv_wp_flowplayer_field_player_name').val();

  // trigger meta data save events, so we get meta data from different
  // plugins included as we post
  jQuery(document).trigger('fv_flowplayer_player_meta_save', [data]);

  $tabs.each(function() {
    var
      $tab = jQuery(this),
      is_videos_tab = $tab.hasClass('fv-player-tab-video-files'),
      is_subtitles_tab = $tab.hasClass('fv-player-tab-subtitles'),
      $tables = ((is_videos_tab || is_subtitles_tab) ? $tab.find('table') : $tab.find('input, select, textarea')),
      save_index = -1;

    // prepare video and subtitles data, which are duplicated through their input names
    if (is_videos_tab) {
      data['videos'] = {};
    } else if (is_subtitles_tab) {
      data['video_meta']['subtitles'] = {};
      data['video_meta']['transcript'] = {};
      data['video_meta']['chapters'] = {};
    }

    // iterate over all tables in tabs
    $tables.each(function() {
      // only videos and subtitles tabs have tables, so we only need to search for their inputs when working with those
      var
        $inputs = ((is_videos_tab || is_subtitles_tab) ? jQuery(this).find('input, select, textarea') : jQuery(this)),
        table_index = jQuery(this).data('index');

      save_index++;

      $inputs.each(function() {
        var
          $this               = jQuery(this),
          $parent_tr          = $this.closest('tr'),
          optionsHaveNoValue = false, // will become true for dropdown options without values
          $valueLessOptions   = null,
          isDropdown          = this.nodeName == 'SELECT';

        // exceptions for selectively hidden fields
        if ($parent_tr.hasClass('fv_player_interface_hide') && $parent_tr.css('display') == 'none') {
          //return;
        }

        // check for a select without any option values, in which case we'll use their text
        if (isDropdown) {
          $valueLessOptions = $this.find('option:not([value])');
          if ($valueLessOptions.length == this.length) {
            optionsHaveNoValue = true;
          }
        }

        while ((m = regex.exec(this.name)) !== null) {
          // This is necessary to avoid infinite loops with zero-width matches
          if (m.index === regex.lastIndex) {
            regex.lastIndex++;
          }

          // videos tab
          if (is_videos_tab) {
            if (!data['videos'][save_index]) {
              data['videos'][save_index] = {
                id: jQuery('.fv-player-playlist-item[data-index=' + table_index + ']').data('id_video')
              };
            }

            // let plugins update video meta, if applicable
            jQuery(document).trigger('fv_flowplayer_video_meta_save', [data, save_index, this]);

            // check for a meta field
            if (fv_wp_flowplayer_check_for_video_meta_field(m[1])) {
              // prepare HLS data, if not prepared yet
              if (!data['video_meta']['video']) {
                data['video_meta']['video'] = {};
              }

              if (!data['video_meta']['video'][save_index]) {
                data['video_meta']['video'][save_index] = {};
              }

              fv_flowplayer_insertUpdateOrDeleteVideoMeta({
                data: data,
                meta_section: 'video',
                meta_key: fv_wp_flowplayer_get_player_meta_field_name(m[1]),
                meta_index: save_index,
                element: this
              });
            } else {
              // ordinary video field
              // check dropdown for its value based on values in it
              if (isDropdown) {
                var opt_value = fv_wp_flowplayer_get_correct_dropdown_value(optionsHaveNoValue, $valueLessOptions, this);
                // if there were any problems, just return an empty object
                if (opt_value === false) {
                  return {};
                } else {
                  data['videos'][save_index][m[1]] = opt_value;
                }
              } else {
                data['videos'][save_index][m[1]] = this.value;
              }
            }
          }

          // subtitles tab, subtitles inputs
          else if (is_subtitles_tab && $this.hasClass('fv_wp_flowplayer_field_subtitles')) {
            if (!data['video_meta']['subtitles'][save_index]) {
              data['video_meta']['subtitles'][save_index] = [];
            }

            // jQuery-select the SELECT element when we get an INPUT, since we need to pair them
            if (this.nodeName == 'INPUT') {
              data['video_meta']['subtitles'][save_index].push({
                code : $this.siblings('select:first').val(),
                file : this.value,
                id: $this.parent().data('id_subtitles')
              });
            }
          }

          // subtitles tab, chapters input
          else if (is_subtitles_tab && $this.attr('id') == 'fv_wp_flowplayer_field_chapters') {
            if (!data['video_meta']['chapters'][save_index]) {
              data['video_meta']['chapters'][save_index] = {};
            }

            fv_flowplayer_insertUpdateOrDeleteVideoMeta({
              data: data,
              meta_section: 'chapters',
              meta_key: 'file',
              meta_index: save_index,
              element: $this
            });
          }

          // subtitles tab, transcript input
          else if (is_subtitles_tab && $this.hasClass('fv_wp_flowplayer_field_transcript')) {
            if (!data['video_meta']['transcript'][save_index]) {
              data['video_meta']['transcript'][save_index] = {};
            }

            fv_flowplayer_insertUpdateOrDeleteVideoMeta({
              data: data,
              meta_section: 'transcript',
              meta_key: 'file',
              meta_index: save_index,
              element: $this
            });
          }

          // all other tabs
          else {
            if (this.nodeName == 'INPUT' && this.type.toLowerCase() == 'checkbox') {
              // some player attributes are meta data
              if (fv_wp_flowplayer_check_for_player_meta_field(m[1])) {
                // meta data input
                fv_flowplayer_insertUpdateOrDeletePlayerMeta({
                  data: data,
                  meta_section: 'player',
                  meta_key: fv_wp_flowplayer_get_player_meta_field_name(m[1]),
                  element: this,
                  handle_delete: false
                });
              } else {
                // ordinary player attribute
                data[m[1]] = (this.type.toLowerCase() == 'checkbox' ? this.checked ? 'true' : '' : this.value);
              }
            } else {
              // check dropdown for its value based on values in it
              if (isDropdown) {
                var opt_value = fv_wp_flowplayer_get_correct_dropdown_value(optionsHaveNoValue, $valueLessOptions, this);
                // if there were any problems, just return an empty object
                if (opt_value === false) {
                  return {};
                } else {
                  if (fv_wp_flowplayer_check_for_player_meta_field(m[1])) {
                    // meta data input
                    fv_flowplayer_insertUpdateOrDeletePlayerMeta({
                      data: data,
                      meta_section: 'player',
                      meta_key: fv_wp_flowplayer_get_player_meta_field_name(m[1]),
                      element: this,
                      handle_delete: false
                    });
                  } else {
                    // ordinary player attribute
                    data[m[1]] = opt_value.toLowerCase();
                  }
                }
              } else {
                if (fv_wp_flowplayer_check_for_player_meta_field(m[1])) {
                  // meta data input
                  fv_flowplayer_insertUpdateOrDeletePlayerMeta({
                    data: data,
                    meta_section: 'player',
                    meta_key: fv_wp_flowplayer_get_player_meta_field_name(m[1]),
                    element: this,
                    handle_delete: false
                  });
                } else {
                  // ordinary player attribute
                  data[m[1]] = this.value;
                }
              }
            }
          }
        }
      });
    });
  });

  // remove any empty videos, i.e. without a source
  // this is used when loading data from DB to avoid previewing an empty video that's in editor by default
  if (data['videos']) {
    var
      data_videos_new = {},
      x = 0;

    for (var i in data['videos']) {
      if (data['videos'][i]['src'] || data['videos'][i]['src1'] || !data['videos'][i]['src2']) {
        // if we should show preview of a single video only, add that video here,
        // otherwise add all videos here
        if (!single_video_showing || x == single_video_id) {
          data_videos_new[x++] =  data['videos'][i];
        } else {
          x++;
        }
      }
    }

    data['videos'] = data_videos_new;
  }

  // add player ID and deleted elements for a DB update
  var $updateElement = jQuery('#id_player');
  if ($updateElement.length) {
    data['update'] = $updateElement.val();
    data['deleted_videos'] = jQuery('#deleted_videos').val();
    data['deleted_video_meta'] = jQuery('#deleted_video_meta').val();
    data['deleted_player_meta'] = jQuery('#deleted_player_meta').val();
  }

  return data;
}



function fv_wp_flowplayer_check_for_player_meta_field(fieldName) {
  return [].indexOf(fieldName) > -1;
}



function fv_wp_flowplayer_check_for_video_meta_field(fieldName) {
  return [
    'fv_wp_flowplayer_field_duration',
    'fv_wp_flowplayer_field_last_video_meta_check',
    'fv_wp_flowplayer_field_live',
    'fv_wp_flowplayer_field_auto_splash',
    'fv_wp_flowplayer_field_auto_caption',
    'fv_wp_flowplayer_field_audio'
  ].indexOf(fieldName) > -1;
}



function fv_wp_flowplayer_get_player_meta_field_name(origFieldName) {
  if (origFieldName.indexOf('fv_wp_flowplayer_field_') > -1) {
    return origFieldName.replace('fv_wp_flowplayer_field_', '');
  }

  return origFieldName;
}



function fv_wp_flowplayer_calculatePreviewDimensions(divPreview) {
  var width = parseInt(jQuery('#fv_wp_flowplayer_field_width').val()) || 460;
  var height = parseInt(jQuery('#fv_wp_flowplayer_field_height').val()) || 300;
  if (divPreview.length && divPreview.width() < width) {
    height = Math.round(height * (divPreview.width() / width));
    width = divPreview.width();
  }

  return {
    width: width,
    height: height
  };
}



function fv_wp_flowplayer_show_preview(has_src, data, is_post) {
  var $previewDiv = jQuery('#fv-player-shortcode-editor-preview');

  jQuery('#fv-player-shortcode-editor-preview-iframe-refresh').hide();
  //jQuery('#fv-player-tabs-debug').html(fv_wp_fp_shortcode);
  if (!has_src) {
    $previewDiv.attr('class', 'preview-no');
    fv_player_shortcode_preview = false;
    //console.log('fv_player_shortcode_preview = false');
    fv_wp_flowplayer_dialog_resize();
    return;
  }

  $previewDiv.attr('class','preview-loading');
  var url = fv_Player_site_base + '?fv_player_embed='+fv_player_editor_conf.preview_nonce+'&fv_player_preview=';

  if (typeof(is_post) !== 'undefined') {
    url += 'POST';
  } else {
    url += encodeURIComponent(b64EncodeUnicode(data));
  }

  if(fv_player_shortcode_preview_unsupported){
    jQuery('#fv-player-shortcode-editor-preview-new-tab > a').html('Open preview in a new window');
    if( jQuery('#fv-player-shortcode-editor-preview div.incompatibility').length == 0 ) jQuery('#fv-player-shortcode-editor-preview-new-tab').after('<div class="notice notice-warning incompatibility"><p>For live preview of the video player please use the latest Firefox, Chromium or Opera.</p></div>');
  }

  // TODO: this opens preview in a new window for a nicer preview but won't work with new DB-based shortcode generation,
  //       as we're sending all the inputs as data and that would be too big for a GET request
  //       ... find a way to circumvent this
  /*if(fv_player_preview_single === -1 && jQuery('.fv-player-tab-video-files table').length > 9 || fv_player_shortcode_preview_unsupported){
    $previewDiv.attr('class','preview-new-tab');
    fv_player_shortcode_preview = false;
    //console.log('fv_player_shortcode_preview = false');
    jQuery('#fv-player-shortcode-editor-preview-new-tab > a').unbind('click').on('click',function(e){
      fv_wp_flowplayer_submit(true);
      url = fv_Player_site_base + '?fv_player_embed=1&fv_player_preview=' + encodeURIComponent(b64EncodeUnicode(data))
      fv_player_open_preview_window( url, width, height + Math.ceil( (jQuery('.fv-player-tab-video-files table').length / 3)) * 155 );
      return false;
    });

    return;
  }*/

  //console.log('Iframe refresh with '+fv_wp_fp_shortcode);
  if( (typeof(is_post) !== 'undefined') || typeof(fv_player_shortcode_editor_last_url) == 'undefined' || url !== fv_player_shortcode_editor_last_url ){
    fv_player_shortcode_editor_last_url = url;
    var $previewTarget = jQuery('#fv-player-shortcode-editor-preview-target');
    $previewTarget.html('');

    if (typeof(is_post) != 'undefined') {
      fv_player_shortcode_editor_ajax = jQuery.post(url, { 'fv_player_preview_json' : JSON.stringify(data) }, function (response) {
        $previewTarget.html(jQuery('#wrapper', response));
        jQuery(document).trigger('fvp-preview-complete');
      });
    } else {
      fv_player_shortcode_editor_ajax = jQuery.get(url, function (response) {
        $previewTarget.html(jQuery('#wrapper', response));
        jQuery(document).trigger('fvp-preview-complete');
      });
    }
  }else{
    jQuery(document).trigger('fvp-preview-complete');
  }
}



function fv_wp_flowplayer_big_loader_show(message) {
  var overlayDiv = jQuery('#fv-player-shortcode-editor-loading-spinner');
  if( overlayDiv.length == 0 ) {
    overlayDiv = jQuery('<div id="fv-player-shortcode-editor-loading-spinner"></div>');
    overlayDiv.insertBefore('#fv-player-shortcode-editor');
  }
  if( typeof(message) != 'undefined' ) {
    overlayDiv.html( '<p>&nbsp;</p><p align="center">'+message+'</p>' ).addClass('loaded');
  } else {
    overlayDiv.html('').removeClass('loaded');
  }
  fv_wp_flowplayer_dialog_resize();
  return overlayDiv;
}



function fv_wp_flowplayer_big_loader_close() {
  jQuery('#fv-player-shortcode-editor-loading-spinner').remove();
}



function fv_wp_flowplayer_copy_to_clipboard() {  

  fv_player_clipboard(jQuery('#fv_player_copy_to_clipboard').val(), function() {
    jQuery('#fv_player_copied_to_clipboard_message')
      .html('Text Copied To Clipboard!')
      .removeClass('notice-error')
      .addClass('notice-success')
      .css('visibility', 'visible');

    setTimeout(function() {
      jQuery('#fv_player_copied_to_clipboard_message').css('visibility', 'hidden');
    }, 3000);
  }, function() {
    jQuery('#fv_player_copied_to_clipboard_message')
      .html('<strong>Error copying text into clipboard!</strong><br />Please copy the content of the above text area manually by using CTRL+C (or CMD+C on MAC).')
      .removeClass('notice-success')
      .addClass('notice-error')
      .css({
        'visibility': 'visible',
        'width': '80%'
      });
  });
}



function fv_player_export(element) {
  fv_wp_flowplayer_big_loader_show();

  jQuery.post(ajaxurl, {
    action: 'fv_player_db_export',
    playerID : element.data('player_id'),
    nonce : element.data('nonce'),
    cookie: encodeURIComponent(document.cookie),
  }, function(json_export_data) {
    json_export_data = jQuery('<div/>').text(json_export_data).html();
    fv_wp_flowplayer_big_loader_show('<textarea name="fv_player_copy_to_clipboard" id="fv_player_copy_to_clipboard" cols="150" rows="15">'+json_export_data+'</textarea>\
      <br />\
      <br />\
      <input type="button" name="fv_player_copy_to_clipboard_btn" id="fv_player_copy_to_clipboard_btn" value="Copy To Clipboard" class="button button-primary button-large" onClick="fv_wp_flowplayer_copy_to_clipboard()" /> &nbsp; \
      <input type="button" name="close_error_overlay" id="close_error_overlay" value="Close" class="button button-large" onClick="jQuery(\'.fv-wordpress-flowplayer-button\').fv_player_box.close()" />\
      <div class="notice notice-success" id="fv_player_copied_to_clipboard_message">&nbsp;</div>');
    jQuery('#fv_player_copy_to_clipboard').select();
  }).error(function() {
    fv_wp_flowplayer_big_loader_show('An unexpected error has occurred. Please try again.\
      <br />\
      <br />\
      <input type="button" name="close_error_overlay" id="close_error_overlay" value="Close" class="button button-primary button-large" onClick="jQuery(\'.fv-wordpress-flowplayer-button\').fv_player_box.close()" />');
  });
}



function fv_player_import(failed, failed_data, message) {
  
  if( !message ) message = 'Please try again.';
  
  fv_wp_flowplayer_big_loader_show('<textarea name="fv_player_import_data" id="fv_player_import_data" cols="150" rows="15" placeholder="Paste your FV Player Export JSON data here">' + (typeof(failed_data) != 'undefined' ? failed_data : '') + '</textarea>\
    <br />\
    <br />\
    <input type="button" name="fv_player_import_btn" id="fv_player_import_btn" value="Import Player" class="button button-primary button-large" onClick="fv_wp_flowplayer_import_routine()" /> &nbsp; \
    <input type="button" name="close_import_overlay" id="close_import_overlay" value="Close" class="button button-large" onClick="jQuery(\'.fv-wordpress-flowplayer-button\').fv_player_box.close()" />\
    <div class="notice notice-success" id="fv_player_imported_message">' + (typeof(failed) != 'undefined' ? '<strong>Error importing data!</strong><br />'+message : '&nbsp;') + '</div>');

  if (typeof(failed) != 'undefined') {
    jQuery('#fv_player_imported_message')
      .removeClass('notice-success')
      .addClass('notice-error')
      .css({
        'visibility': 'visible',
        'width': '80%'
      });
  }
}



function fv_wp_flowplayer_import_routine() {
  var data = jQuery('#fv_player_import_data').val();

  if (!data) {
    jQuery('#fv_player_imported_message')
      .html('No data to import!')
      .removeClass('notice-success')
      .addClass('notice-error')
      .css({
        'visibility': 'visible',
        'width': '80%'
      });

    setTimeout(function() {
      jQuery('#fv_player_imported_message').css('visibility', 'hidden');
    }, 5000);

    return;
  } else {
    jQuery('#fv_player_imported_message').css('visibility', 'hidden');
  }
  
  fv_wp_flowplayer_big_loader_show();

  jQuery.post(ajaxurl, {
    action: 'fv_player_db_import',
    nonce: fv_player_editor_conf.db_import_nonce,
    data: data,
    cookie: encodeURIComponent(document.cookie),
  }, function(playerID) {
    if (playerID != '0' && !isNaN(parseFloat(playerID)) && isFinite(playerID)) {
      // add the inserted player's row
      jQuery.get(
        document.location.href.substr(0, document.location.href.indexOf('?page=fv_player')) + '?page=fv_player&id=' + playerID,
        function (response) {
          jQuery('#the-list tr:first').before(jQuery(response).find('#the-list tr:first'));
          jQuery('.fv-wordpress-flowplayer-button').fv_player_box.close();
      }).error(function() {
        jQuery('.fv-wordpress-flowplayer-button').fv_player_box.close();
      });
    } else {
      fv_player_import(true, data, playerID);
    }
  }).error(function() {
    fv_player_import(true, data);
  });
}



function fv_wp_flowplayer_submit( preview, insert_as_new ) {
  if( preview && typeof(fv_player_shortcode_preview) != "undefined" && fv_player_shortcode_preview ){
    //console.log('fv_wp_flowplayer_submit skip...',fv_player_shortcode_preview);
    return;
  }
  
  if( preview == 'refresh-button' ) {
    jQuery('#fv-player-shortcode-editor-preview-iframe-refresh').show();
    return;
  }  
  
  fv_player_shortcode_preview = true;
  //console.log('fv_player_shortcode_preview = true');
  
  fv_wp_fp_shortcode = '';
  var shorttag = 'fvplayer';
  var divPreview = jQuery('#fv-player-shortcode-editor-preview');
	
	if(
    !preview &&
    jQuery(".fv_wp_flowplayer_field_rtmp").attr('placeholder') == '' &&
		jQuery(".fv_wp_flowplayer_field_rtmp_wrapper").is(":visible") &&
		(
			( jQuery(".fv_wp_flowplayer_field_rtmp").val() != '' && jQuery(".fv_wp_flowplayer_field_rtmp_path").val() == '' ) ||
			( jQuery(".fv_wp_flowplayer_field_rtmp").val() == '' && jQuery(".fv_wp_flowplayer_field_rtmp_path").val() != '' )
		)
	) {
		alert('Please enter both server and path for your RTMP video.');
		return false;
	} else if( 
          !preview &&
          document.getElementById("fv_wp_flowplayer_field_src").value == '' 
          && jQuery(".fv_wp_flowplayer_field_rtmp").val() == '' 
          && jQuery(".fv_wp_flowplayer_field_rtmp_path").val() == '') {
		alert('Please enter the file name of your video file.');
		return false;
	} else {
    fv_wp_fp_shortcode = '[' + shorttag;
  }

  var
    previewWidth = null,
    previewHeight = null;

  // if we're using the new DB-related shortcode, let's handle it here
  if (1) {
	  var ajax_data = fv_wp_flowplayer_build_ajax_data();

	  if (preview) {
	    // don't use DB preview if we're working with a standard shortcode
	    if (fv_flowplayer_conf.new_shortcode_active) {
        var previewDimensions = fv_wp_flowplayer_calculatePreviewDimensions(divPreview);
        previewWidth = previewDimensions.width;
        previewHeight = previewDimensions.height;
        ajax_data['fv_wp_flowplayer_field_width'] = previewWidth;
        ajax_data['fv_wp_flowplayer_field_height'] = previewHeight;
        fv_wp_flowplayer_show_preview(true, ajax_data, true);
        return;
      }
    } else {
	    // show saving loader
      fv_wp_flowplayer_big_loader_show();

      // remove this ID from removals
      if (fv_flowplayer_conf.fv_flowplayer_edit_lock_removal) {
        delete fv_flowplayer_conf.fv_flowplayer_edit_lock_removal[fv_flowplayer_conf.current_player_db_id];
      }

      // unmark DB player ID as being currently edited
      if (fv_flowplayer_conf.current_player_db_id) {
        delete fv_flowplayer_conf.current_player_db_id;
      }

      // save data
      jQuery.post(ajaxurl, {
        action: 'fv_player_db_save',
        data: JSON.stringify(ajax_data),
        nonce: fv_player_editor_conf.preview_nonce,
        cookie: encodeURIComponent(document.cookie),
      }, function(playerID) {
        if (playerID == parseInt(playerID)) {
          // we have extra parameters to keep
          if (fv_flowplayer_conf.db_extra_shortcode_params) {
            var
              params = jQuery.map(fv_flowplayer_conf.db_extra_shortcode_params, function (value, index) {
                return index + '="' + value + '"';
              }),
              to_append = '';

            if (params.length) {
              to_append = ' ' + params.join(' ');
            }

            fv_wp_flowplayer_insert('[fvplayer id="' + playerID + '"' + to_append + ']');
          } else {
            // simple DB shortcode, no extra presentation parameters
            fv_wp_flowplayer_insert('[fvplayer id="' + playerID + '"]');
          }

          // if we're inserting player as new and we come from the list view,
          // we need to store this player's ID in the original Edit link's data,
          // so we can add it to the displayed table
          if ((insert_as_new && jQuery(fv_player_editor_button_clicked).data('player_id')) || jQuery(fv_player_editor_button_clicked).data('add_new')) {
            jQuery(fv_player_editor_button_clicked).data('insert_as_new_id', playerID);
          } else {
            jQuery(fv_player_editor_button_clicked).data('insert_id', playerID);
          }

          jQuery(".fv-wordpress-flowplayer-button").fv_player_box.close();
        } else {
          json_export_data = jQuery('<div/>').text(JSON.stringify(ajax_data)).html();
          fv_wp_flowplayer_big_loader_show('An unexpected error has occurred. Please copy the player raw data below and <a href="https://foliovision.com/support/fv-wordpress-flowplayer/bug-reports#new-post" target="_blank">submit a support ticket to Foliovision</a>:\
            <br />\
            <br />\
            <textarea name="fv_player_copy_to_clipboard" id="fv_player_copy_to_clipboard" cols="150" rows="15">'+json_export_data+'</textarea>\
            <br />\
            <br />\
            <input type="button" name="fv_player_copy_to_clipboard_btn" id="fv_player_copy_to_clipboard_btn" value="Copy To Clipboard" class="button button-primary button-large" onClick="fv_wp_flowplayer_copy_to_clipboard()" />\
            <input type="button" name="close_error_overlay" id="close_error_overlay" value="Close" class="button button-large" onClick="fv_wp_flowplayer_big_loader_close()" />\
            <div class="notice notice-success" id="fv_player_copied_to_clipboard_message">&nbsp;</div>');
          jQuery('#fv_player_copy_to_clipboard').select();
        }
      }).error(function() {
        fv_wp_flowplayer_big_loader_show('An unexpected error has occurred. Please try again.\
          <br />\
          <br />\
          <input type="button" name="close_error_overlay" id="close_error_overlay" value="Close" class="button button-primary button-large" onClick="fv_wp_flowplayer_big_loader_close()" /></p>');
      });

      return;
    }
  }

  if( fv_player_preview_single == -1 ) {
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_src','src');
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_src1','src1');
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_src2','src2');
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_rtmp','rtmp');
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_rtmp_path','rtmp_path');
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_live', 'live', false, true );	        
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_mobile','mobile');  
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_splash','splash');
  } else {
    var item = jQuery('.fv-player-tab-video-files table').eq(fv_player_preview_single);    
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_src')[0],'src');
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_src1')[0],'src1');
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_src2')[0],'src2');
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_rtmp')[0],'rtmp');
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_rtmp_path')[0],'rtmp_path');
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_live')[0], 'live', false, true );	        
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_mobile')[0],'mobile');  
    fv_wp_flowplayer_shortcode_write_arg(item.find('#fv_wp_flowplayer_field_splash')[0],'splash');
    
    //  todo: how to handle RTMP server here?
  }
  
  var width , height;
  if(!preview){
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_width','width','int');
    fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_height','height','int');
  }else{
    if (previewWidth === null) {
      var previewDimensions = fv_wp_flowplayer_calculatePreviewDimensions(divPreview);
      width = previewDimensions.width;
      height = previewDimensions.height;
    }
    fv_wp_fp_shortcode += ' width="' + width + '" '    
    fv_wp_fp_shortcode += ' height="' + height + '" '
  }
  
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_align', 'align', false, false, ['left', 'right'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_autoplay', 'autoplay', false, false, ['true', 'false'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_playlist', 'liststyle', false, false, ['tabs', 'prevnext', 'vertical','horizontal','text','slider'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_controlbar', 'controlbar', false, false, ['yes', 'no'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_embed', 'embed', false, false, ['true', 'false'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_speed', 'speed', false, false, ['buttons', 'no'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_playlist_advance', 'playlist_advance', false, false, ['true', 'false'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_sticky', 'sticky', false, false, ['true', 'false'] );
  fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_share', 'share', false, false, ['yes', 'no', jQuery('#fv_wp_flowplayer_field_share_title').val().replace(/;/,'').replace(/(\S)$/,'$1;')+jQuery('#fv_wp_flowplayer_field_share_url').val().replace(/;/,'')] );

  
  /*
   * End of playlist dropdown
   * legacy:
   * fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_loop', 'loop', false, true );
   * fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_redirect','redirect');
   * fv_wp_flowplayer_shortcode_write_arg( 'fv_wp_flowplayer_field_splashend', 'splashend', false, true, ['show'] );
   */
  switch(jQuery('#fv_wp_flowplayer_field_end_actions').val()){
    case 'loop': fv_wp_fp_shortcode += ' loop="true"'; break;
    case 'splashend': fv_wp_fp_shortcode += ' splashend="show"'; break;
    case 'redirect': fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_redirect','redirect'); break;
    case 'popup': 
      if( jQuery('[name=fv_wp_flowplayer_field_popup]').val() !== ''){
        fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_popup','popup','html');
      }else{
        fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_popup_id', 'popup', false, false, ['no','random','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16'] );
      }
    break;
    case 'email_list':
      var value = jQuery('#fv_wp_flowplayer_field_email_list').val();
      if(value)
        fv_wp_fp_shortcode += ' popup="email-' + value + '"';
    break;

  }
  
  
  jQuery('.fv_wp_flowplayer_field_subtitles').each( function() {
    var lang = jQuery(this).siblings('.fv_wp_flowplayer_field_subtitles_lang').val();
    if( lang ) fv_wp_flowplayer_shortcode_write_arg( jQuery(this)[0], 'subtitles_' + lang );  //  non language specific subtitles are handled on playlist level. what?
  });   
  
  fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_ad','ad','html');
  //  
  
  fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_ad_height','ad_height','int');
  fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_ad_skip','ad_skip', false, true, ['yes']);
	fv_wp_flowplayer_shortcode_write_arg('fv_wp_flowplayer_field_ad_width','ad_width','int');

	if( fv_player_preview_single == -1 && jQuery('.fv-player-tab-video-files table').length > 0 ) {
		var aPlaylistItems = new Array();
    var aPlaylistCaptions = new Array();
    var aSplashText = new Array();
    
    var aPlaylistSubtitles = new Array();
		jQuery('.fv-player-tab-video-files table').each(function(i,e) {
      aPlaylistCaptions.push(jQuery('[name=fv_wp_flowplayer_field_caption]',this).attr('value').trim().replace(/\;/gi,'\\;').replace(/"/gi,'&amp;quot;') );
      
      aSplashText.push(jQuery('[name=fv_wp_flowplayer_field_splash_text]', this).attr('value').trim().replace(/\;/gi,'\\;').replace(/"/gi,'&amp;quot;') );
      
      var video_subtitles = jQuery('.fv-player-tab-subtitles table').eq(i);
      video_subtitles.find('[name=fv_wp_flowplayer_field_subtitles]').each( function() {
        if( jQuery(this).prev('.fv_wp_flowplayer_field_subtitles_lang').val() ) {
          aPlaylistSubtitles.push('');
          return;
        }
        aPlaylistSubtitles.push( jQuery(this).attr('value').trim().replace(/\;/gi,'\\;').replace(/"/gi,'&amp;quot;') );
      });
      
		  if( i == 0 ) return;  
      var aPlaylistItem = new Array();      

      jQuery(this).find('input').each( function() {
        if( jQuery(this).attr('name').match(/fv_wp_flowplayer_field_caption/) ) return;
        if( jQuery(this).attr('name').match(/fv_wp_flowplayer_field_splash_text/) ) return;
        if( jQuery(this).hasClass('fv_wp_flowplayer_field_rtmp') || jQuery(this).hasClass('fv_wp_flowplayer_field_width') || jQuery(this).hasClass('fv_wp_flowplayer_field_height') ) return;
        if( jQuery(this).hasClass('extra-field') ) return;
        if( jQuery(this).attr('value').trim().length > 0 ) { 
          var value = jQuery(this).attr('value').trim()
          if( jQuery(this).hasClass('fv_wp_flowplayer_field_rtmp_path') ) value = "rtmp:"+value;
          aPlaylistItem.push(value);
        }
      } );			
      if( aPlaylistItem.length > 0 ) {
        aPlaylistItems.push(aPlaylistItem.join(','));
      }
    }
		);
		var sPlaylistItems = aPlaylistItems.join(';');
    var sPlaylistCaptions = aPlaylistCaptions.join(';');
    var sPlaylistSubtitles = aPlaylistSubtitles.join(';');
    var sSplashText = aSplashText.join(';');
		if( sPlaylistItems.length > 0 ) {
			fv_wp_fp_shortcode += ' playlist="'+sPlaylistItems+'"';
		}

    var bPlaylistCaptionExists = false;
    for( var i in aPlaylistCaptions ){
      if( typeof(aPlaylistCaptions[i]) == "string" && aPlaylistCaptions[i].trim().length > 0 ) {
        bPlaylistCaptionExists = true;
      }
    }
		if( bPlaylistCaptionExists && sPlaylistCaptions.length > 0 ) {
			fv_wp_fp_shortcode += ' caption="'+sPlaylistCaptions+'"';
		}
    
    if( sPlaylistSubtitles.replace(/[; ]/g,'').length > 0 && sPlaylistSubtitles.length > 0 ) {
			fv_wp_fp_shortcode += ' subtitles="'+sPlaylistSubtitles+'"';
		}
    
    var bPlaylistSplashTextExists = false;
    for( var i in aSplashText ){
      if( typeof(aSplashText[i]) == "string" && aSplashText[i].trim().length > 0 ) {
        bPlaylistSplashTextExists = true;
      }
    }
		if( bPlaylistSplashTextExists && aSplashText.length > 0 ) {
			fv_wp_fp_shortcode += ' splash_text="'+sSplashText+'"';
		}    
	}

  jQuery(document).trigger('fv_flowplayer_shortcode_create');
	
	if( fv_wp_fp_shortcode_remains && fv_wp_fp_shortcode_remains.trim().length > 0 ) {
  	fv_wp_fp_shortcode += ' ' + fv_wp_fp_shortcode_remains.trim();
  }
  
	fv_wp_fp_shortcode += ']';
	
  //Preview
  if(preview){
    fv_wp_flowplayer_show_preview(fv_wp_fp_shortcode.match(/src=/), fv_wp_fp_shortcode);
    return;
  }

  jQuery(".fv-wordpress-flowplayer-button").fv_player_box.close();

  fv_wp_flowplayer_insert(fv_wp_fp_shortcode);
}

function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
}


function fv_player_open_preview_window(url, width, height){
  height = Math.min(window.screen.availHeight * 0.80, height + 25);
  width = Math.min(window.screen.availWidth * 0.66, width + 100);
  
  if(fv_player_preview_window == null || fv_player_preview_window.self == null || fv_player_preview_window.closed ){
    fv_player_preview_window = window.open(url,'window','toolbar=no, menubar=no, resizable=yes width=' + width + ' height=' + height);
  }else{
    fv_player_preview_window.location.assign(url);
    fv_player_preview_window.focus();
  }
  
}


function fv_player_refresh_tabs(){
  var visibleTabs = 0;
  jQuery('#fv-player-shortcode-editor-editor a[data-tab]').removeClass('fv_player_interface_hide');
  jQuery('#fv-player-shortcode-editor-editor .fv-player-tabs > .fv-player-tab').each(function(){   
    var bHideTab = true
    jQuery(this).find('tr:not(.fv_player_actions_end-toggle):not(.submit-button-wrapper)').each(function(){
      if(jQuery(this).css('display') === 'table-row'){
        bHideTab = false;
        return false;
      }
    });
    var tab;
    var data = jQuery(this).attr('class').match(/fv-player-tab-[^ ]*/);
      if(data[0]){
      tab =  jQuery('#fv-player-shortcode-editor-editor a[data-tab=' + data[0] + ']');
      }
    if(bHideTab){
      tab.addClass('fv_player_interface_hide')
    }else{
      tab.removeClass('fv_player_interface_hide');
      if(tab.css('display')!=='none')
        visibleTabs++
      
    }
  });
  
  if(visibleTabs<=1){
    jQuery('#fv-player-shortcode-editor-editor .nav-tab').addClass('fv_player_interface_hide');
  }
  
  if(jQuery('#fv-player-shortcode-editor-editor').hasClass('is-playlist-active')){		
    jQuery('label[for=fv_wp_flowplayer_field_end_actions]').html(jQuery('label[for=fv_wp_flowplayer_field_end_actions]').data('playlist-label'))		
  }else{		
    jQuery('label[for=fv_wp_flowplayer_field_end_actions]').html(jQuery('label[for=fv_wp_flowplayer_field_end_actions]').data('single-label'))		
  }		
   
  
}

function fv_wp_flowplayer_add_format() {
  if ( jQuery("#fv_wp_flowplayer_field_src").val() != '' ) {
    if ( jQuery(".fv_wp_flowplayer_field_src1_wrapper").is(":visible") ) {      
      if ( jQuery("#fv_wp_flowplayer_field_src1").val() != '' ) {
        jQuery(".fv_wp_flowplayer_field_src2_wrapper").show();
        jQuery("#fv_wp_flowplayer_field_src2_uploader").show();
        jQuery("#add_format_wrapper").hide();
      }
      else {
        alert('Please enter the file name of your second video file.');
      }
    }
    else {
      jQuery(".fv_wp_flowplayer_field_src1_wrapper").show();
      jQuery("#fv_wp_flowplayer_field_src1_uploader").show();
    }
    fv_wp_flowplayer_dialog_resize();
  }
  else {
    alert('Please enter the file name of your video file.');
  }
}

function fv_wp_flowplayer_add_rtmp(el) {
	jQuery(el).parents('.fv-player-playlist-item').find(".fv_wp_flowplayer_field_rtmp_wrapper").show();
  jQuery(el).parents('.fv-player-playlist-item').find(".add_rtmp_wrapper").hide();
	fv_wp_flowplayer_dialog_resize();
}

function fv_wp_flowplayer_shortcode_parse_arg( sShortcode, sArg, bHTML, sCallback ) {

  var rDoubleQ = new RegExp(sArg+"=\"","g");
  var rSingleQ = new RegExp(sArg+"='","g");
  var rNoQ = new RegExp(sArg+"=[^\"']","g");
  
  var rMatch = false;
  if( sShortcode.match(rDoubleQ) ) {
    //rMatch = new RegExp(sArg+'="(.*?[^\\\\/])"',"g");
    rMatch = new RegExp('[ "\']' + sArg + '="(.*?[^\\\\])"', "g");
  } else if (sShortcode.match(rSingleQ)) {
    rMatch = new RegExp('[ "\']' + sArg + "='([^']*?)'", "g");
  } else if (sShortcode.match(rNoQ)) {
    rMatch = new RegExp('[ "\']' + sArg + "=([^\\]\\s,]+)", "g");
  }

  if( !rMatch ){
    return false;
  }
  
  var aOutput = rMatch.exec(sShortcode);
  fv_wp_fp_shortcode_remains = fv_wp_fp_shortcode_remains.replace( rMatch, '' );
 
  if( bHTML ) {
    aOutput[1] = aOutput[1].replace(/\\"/g, '"').replace(/\\(\[|])/g, '$1');
  }
  
  if( aOutput && sCallback ) {
    sCallback(aOutput);
  } else {
   return aOutput;
  }
}


function fv_wp_flowplayer_subtitle_parse_arg( args ) {
  var input = ('fv_wp_flowplayer_subtitle_parse_arg',args);
  var aLang = input[0].match(/subtitles_([a-z][a-z])/);
  fv_flowplayer_language_add( input[1], aLang[1] );
}


function fv_wp_flowplayer_share_parse_arg( args ) {
  if (args[1] == 'yes' ) {
    document.getElementById("fv_wp_flowplayer_field_share").selectedIndex = 1;
  } else if (args[1] == 'no' ) {
    document.getElementById("fv_wp_flowplayer_field_share").selectedIndex = 2;
  } else {
    document.getElementById("fv_wp_flowplayer_field_share").selectedIndex = 3;
    args = args[1].split(';');
    if( typeof(args[0]) == "string" ) jQuery('#fv_wp_flowplayer_field_share_url').val(args[0]);
    if( typeof(args[1]) == "string" ) jQuery('#fv_wp_flowplayer_field_share_title').val(args[1]);
    jQuery("#fv_wp_flowplayer_field_share_custom").show();
  }
}


function fv_wp_flowplayer_shortcode_write_args( sField, sArg, sKind, bCheckbox, aValues ) {
  jQuery('[id='+sField+']').each( function(k,v) {
    k = (k==0) ? '' : k;
    fv_wp_flowplayer_shortcode_write_arg(jQuery(this)[0],sArg+k, sKind, bCheckbox, aValues);
  });
}

function fv_wp_flowplayer_shortcode_write_arg( sField, sArg, sKind, bCheckbox, aValues ) {
  var element;
  if ( typeof(sField) == "string" ) {
    element = document.getElementById(sField);
  } else {
    element = sField;
  }
  if( typeof(element) == "undefined") {
    return false;
  }
  
  var sValue = false;
  if( bCheckbox ) {
    if( element.checked ){
      if( aValues ) {
        sValue = aValues[0];
      } else {
        sValue = 'true';
      }
    }
  } else if( aValues ){
    if( typeof(aValues[element.selectedIndex -1 ]) == "undefined" ) {
      return false;
    }
    sValue = aValues[element.selectedIndex -1 ];
  } else if( element.value != '' ) {
    sValue = element.value.trim();
    var sOutput = false;
    
    if( sKind == "int" ) {
      if( sValue % 1 !=0 ){
        return false;
      }
    } else if( sKind == 'html' ){
      sValue = sValue.replace(/&/g,'&amp;');
      //sValue = sValue.replace(/'/g,'\\\'');
      //sValue = sValue.replace(/"/g,'&quot;');
      sValue = sValue.replace(/</g,'&lt;');
      sValue = sValue.replace(/>/g,'&gt;');
    }
  } else {
    return false;
  }
    
  if( !sValue ){
    return false;
  }

  if( sValue.match(/"/) || sKind == 'html' ){
    sOutput = '"'+sValue.replace(/"/g, '\\"').replace(/(\[|])/g, '\\$1')+'"';
  } else {
    sOutput = '"'+sValue+'"';
  }
  
  if( sOutput ){
    fv_wp_fp_shortcode += ' '+sArg+'='+sOutput; 
  }
  return sValue;
};


function fv_flowplayer_shortcode_editor_cleanup(sInput) {
  sInput[1] = sInput[1].replace(/\\;/gi, '<!--FV Flowplayer Caption Separator-->').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
  aInput = sInput[1].split(';');
  for( var i in aInput ){
    aInput[i] = aInput[i].replace(/\\"/gi, '"');
    aInput[i] = aInput[i].replace(/\\<!--FV Flowplayer Caption Separator-->/gi, ';');
    aInput[i] = aInput[i].replace(/<!--FV Flowplayer Caption Separator-->/gi, ';');
  }
  return aInput;
};

jQuery(document).on('fv_flowplayer_shortcode_insert', function(e) {
  jQuery(e.target).siblings('.button.fv-wordpress-flowplayer-button').val('Edit');
});

/**
 * Automatically handles new, updated or removed player meta data
 * via JS.
 *
 * @param options Object with the following elements:
 *                element -> the actual element on page (input, select...) to check and get meta value from
 *                data -> existing player data, including player meta data
 *                meta_section -> section for the meta data, for example "player" for common metas, "ppv" for pay per view plugin etc.
 *                meta_key -> rhe actual key to check for and potentially add/update/remove
 *                handle_delete -> if true, value-less ('') elements will be considered indication that the meta key should be deleted
 *                delete_callback -> if set, this function is called when a meta key is deleted
 *                edit_callback -> if set, this function is called when a meta key is updated
 *                insert_callback -> if set, this function is called when a meta key is added
 */
function fv_flowplayer_insertUpdateOrDeletePlayerMeta(options) {
  var
    $element = jQuery(options.element),
    $deleted_meta_element = jQuery('#deleted_player_meta'),
    optionsHaveNoValue = false, // will become true for dropdown options without values
    $valueLessOptions = null,
    isDropdown = $element.get(0).nodeName == 'SELECT',
    value = ($element.get(0).type.toLowerCase() == 'checkbox' ? $element.get(0).checked ? 'true' : '' : $element.val());

  // don't do anything if we've not found the actual element
  if (!$element.length) {
    return;
  }

  // check for a select without any option values, in which case we'll use their text
  if (isDropdown) {
    $valueLessOptions = $element.find('option:not([value])');
    if ($valueLessOptions.length == $element.get(0).length) {
      optionsHaveNoValue = true;
    }

    var opt_value = fv_wp_flowplayer_get_correct_dropdown_value(optionsHaveNoValue, $valueLessOptions, $element.get(0));
    // if there were any problems, just set value to ''
    if (opt_value === false) {
      value = '';
    } else {
      value = opt_value.toLowerCase();
    }
  }

  // check whether to update or delete this meta
  if ($element.data('id')) {
    // only delete this meta if delete was not prevented via options
    // and if there was no value specified, otherwise update
    if ((!options.handle_delete || options.handle_delete !== false) && !value) {
      if ($deleted_meta_element.val()) {
        $deleted_meta_element.val($deleted_meta_element.val() + ',' + $element.data('id'));
      } else {
        $deleted_meta_element.val($element.data('id'));
      }

      $element
        .removeData('id')
        .removeAttr('data-id');

      // execute delete callback, if present
      if (options.delete_callback && typeof(options.delete_callback) == 'function') {
        options.delete_callback();
      }
    } else {
      if (typeof(options.data) != 'undefined' && typeof(options.data['player_meta'][options.meta_section]) == 'undefined') {
        options.data['player_meta'][options.meta_section] = {};
      }

      // update if we have an ID
      if (typeof(options.data) != 'undefined') {
        options.data['player_meta'][options.meta_section][options.meta_key] = {
          'id': $element.data('id'),
          'value': value
        }
      }

      // execute update callback, if present
      if (options.edit_callback && typeof(options.edit_callback) == 'function') {
        options.edit_callback();
      }
    }
  } else if (value) {
    if (typeof(options.data) != 'undefined' && typeof(options.data['player_meta'][options.meta_section]) == 'undefined') {
      options.data['player_meta'][options.meta_section] = {};
    }

    // insert new data if no meta ID
    if (typeof(options.data) != 'undefined') {
      options.data['player_meta'][options.meta_section][options.meta_key] = {
        'value': value
      }
    }

    // execute insert callback, if present
    if (options.insert_callback && typeof(options.insert_callback) == 'function') {
      options.insert_callback();
    }
  }
};

/**
 * Automatically handles new, updated or removed video meta data
 * via JS.
 *
 * @param options Object with the following elements:
 *                element -> the actual element on page (input, select...) to check and get meta value from
 *                data -> existing player data, including video meta data
 *                meta_section -> section for the meta data, for example "player" for common metas, "ppv" for pay per view plugin etc.
 *                meta_key -> rhe actual key to check for and potentially add/update/remove
 *                handle_delete -> if true, value-less ('') elements will be considered indication that the meta key should be deleted
 *                delete_callback -> if set, this function is called when a meta key is deleted
 *                edit_callback -> if set, this function is called when a meta key is updated
 *                insert_callback -> if set, this function is called when a meta key is added
 */
function fv_flowplayer_insertUpdateOrDeleteVideoMeta(options) {
  var
    $element = jQuery(options.element),
    $deleted_meta_element = jQuery('#deleted_video_meta'),
    optionsHaveNoValue = false, // will become true for dropdown options without values
    $valueLessOptions = null,
    isDropdown = $element.get(0).nodeName == 'SELECT',
    value = ($element.get(0).type.toLowerCase() == 'checkbox' ? $element.get(0).checked ? 'true' : '' : $element.val());

  // don't do anything if we've not found the actual element
  if (!$element.length) {
    return;
  }

  // check for a select without any option values, in which case we'll use their text
  if (isDropdown) {
    $valueLessOptions = $element.find('option:not([value])');
    if ($valueLessOptions.length == $element.get(0).length) {
      optionsHaveNoValue = true;
    }

    var opt_value = fv_wp_flowplayer_get_correct_dropdown_value(optionsHaveNoValue, $valueLessOptions, $element.get(0));
    // if there were any problems, just set value to ''
    if (opt_value === false) {
      value = '';
    } else {
      value = opt_value.toLowerCase();
    }
  }

  // check whether to update or delete this meta
  if ($element.data('id')) {
    // only delete this meta if delete was not prevented via options
    // and if there was no value specified, otherwise update
    if ((!options.handle_delete || options.handle_delete !== false) && !$element.val()) {
      if ($deleted_meta_element.val()) {
        $deleted_meta_element.val($deleted_meta_element.val() + ',' + $element.data('id'));
      } else {
        $deleted_meta_element.val($element.data('id'));
      }

      $element
        .removeData('id')
        .removeAttr('data-id');

      // execute delete callback, if present
      if (options.delete_callback && typeof(options.delete_callback) == 'function') {
        options.delete_callback();
      }
    } else {
      if (typeof(options.data) != 'undefined' && typeof(options.data['video_meta'][options.meta_section]) == 'undefined') {
        options.data['video_meta'][options.meta_section] = {};
      }

      if (typeof(options.data) != 'undefined') {
        // update if we have an ID
        options.data['video_meta'][options.meta_section][options.meta_index][options.meta_key] = {
          'id': $element.data('id'),
          'value': value
        }

        // execute update callback, if present
        if (options.edit_callback && typeof(options.edit_callback) == 'function') {
          options.edit_callback();
        }
      }
    }
  } else if (value) {
    if (typeof(options.data) != 'undefined' && typeof(options.data['video_meta'][options.meta_section]) == 'undefined') {
      options.data['video_meta'][options.meta_section] = {};
    }

    // insert new data if no meta ID
    if (typeof(options.data) != 'undefined') {
      options.data['video_meta'][options.meta_section][options.meta_index][options.meta_key] = {
        'value': value
      }

      // execute insert callback, if present
      if (options.insert_callback && typeof(options.insert_callback) == 'function') {
        options.insert_callback();
      }
    }
  }
};

// extending DB player edit lock's timer
jQuery( document ).on( 'heartbeat-send', function ( event, data ) {
  if (fv_flowplayer_conf.current_player_db_id) {
    data.fv_flowplayer_edit_lock_id = fv_flowplayer_conf.current_player_db_id;
  }
  
  if (fv_flowplayer_conf.fv_flowplayer_edit_lock_removal) {
    data.fv_flowplayer_edit_lock_removal = fv_flowplayer_conf.fv_flowplayer_edit_lock_removal;
  }
});

// remove edit locks in the config if it was removed on the server
jQuery( document ).on( 'heartbeat-tick', function ( event, data ) {
  if ( data.fv_flowplayer_edit_locks_removed ) {
    fv_flowplayer_conf.fv_flowplayer_edit_lock_removal = {};
  }
});




jQuery( function($) {
  $(document).ready( 'fv_wp_flowplayer_init' );

  var $body = jQuery('body');
  $body.on('focus', '#fv_player_copy_to_clipboard', function() {
    this.select();
  });

  $body.on('change', '#fv_wp_flowplayer_field_src', function() {
    var
      $element = jQuery(this),
      $parent_table = $element.closest('table'),
      $playlist_row = jQuery('.fv-player-tab-playlist table tr[data-index="' + $parent_table.data('index') + '"] td.fvp_item_caption'),
      value = $element.val(),
      update_fields = null,
      $caption_element = $parent_table.find('#fv_wp_flowplayer_field_caption'),
      $splash_element = $parent_table.find('#fv_wp_flowplayer_field_splash'),
      $auto_splash_element = $element.siblings('#fv_wp_flowplayer_field_auto_splash'),
      $auto_caption_element = $element.siblings('#fv_wp_flowplayer_field_auto_caption');

    // cancel any previous AJAX call
    if (typeof($element.data('fv_player_video_data_ajax')) != 'undefined') {
      $element.data('fv_player_video_data_ajax').abort();
      $element.removeData('fv_player_video_data_ajax');
    }

    // cancel any previous auto-refresh task
    /*if (typeof($element.data('fv_player_video_auto_refresh_task')) != 'undefined') {
      clearInterval($element.data('fv_player_video_auto_refresh_task'));
      $element.removeData('fv_player_video_auto_refresh_task');
    }*/

    // set jQuery data related to certain meta data that we may have for current video
    if (!$auto_splash_element.length && $splash_element.val() ) {
      // splash for this video was manually updated
      $splash_element.data('fv_player_user_updated', 1);
      console.log('splash for this video was manually updated');
    }

    if (!$auto_caption_element.length && $caption_element.val() ) {
      // caption for this video was manually updated
      $caption_element.data('fv_player_user_updated', 1);
      console.log('caption for this video was manually updated');
    }

    // try to check if we have a suitable matcher
    for (var vtype in fv_player_editor_matcher) {
      if (fv_player_editor_matcher[vtype].matcher.exec(value) !== null) {
        update_fields = (fv_player_editor_matcher[vtype].update_fields ? fv_player_editor_matcher[vtype].update_fields : []);
        break;
      }
    }

    // only make an AJAX call if we found a matcher
    if (update_fields !== null) {
      if (update_fields.length) {
        // add spinners (loading indicators) to all inputs where data are being loaded
        var selector = '#fv_wp_flowplayer_field_src';
        if( update_fields.indexOf('caption') > 0 ) selector += ', #fv_wp_flowplayer_field_splash';
        if( update_fields.indexOf('splash') > 0 ) selector += ', #fv_wp_flowplayer_field_caption';
        
        $parent_table
          .find(selector)
          .filter(function () {
            var
              $e = jQuery(this),
              updated_manually = $e.val() && typeof($e.data('fv_player_user_updated')) != 'undefined';
              
            console.log(this.id+' has been updated? '+updated_manually,$e.val());

            if (this.id == 'fv_wp_flowplayer_field_caption' && !updated_manually) {
              // add spinners (loading indicators) to the playlist table
              if ($playlist_row.length) {
                $playlist_row.html('<div class="fv-player-shortcode-editor-small-spinner">&nbsp;</div>');
              }
            }

            return !updated_manually;
          })
          .parent()
          .append('<div class="fv-player-shortcode-editor-small-spinner"></div>');

        var ajax_call = function () {
          $element.data('fv_player_video_data_ajax', jQuery.post(ajaxurl, {
              action: 'fv_wp_flowplayer_retrieve_video_data',
              video_url: $element.val(),
              cookie: encodeURIComponent(document.cookie),
            }, function (json_data) {

              // check if we still have this element on page
              if ($element.closest("body").length > 0 && update_fields.length) {

                // update all fields that should be updated
                for (var i in update_fields) {
                  switch (update_fields[i]) {
                    case 'caption':
                      if (json_data.name) {
                        if (!$caption_element.val() || typeof($caption_element.data('fv_player_user_updated')) == 'undefined') {
                          $caption_element.val(json_data.name);

                          // update caption in playlist table
                          if ($playlist_row.length) {
                            $playlist_row.html('<div>' + json_data.name + '</div>');
                          }
                        }
                      }
                      break;

                    case 'splash':
                      if (json_data.thumbnail) {
                        if (!$splash_element.val() || typeof($splash_element.data('fv_player_user_updated')) == 'undefined') {
                          $splash_element.val(json_data.thumbnail);
                        }
                      }
                      break;

                    case 'auto_splash':
                      if (!$element.siblings('#fv_wp_flowplayer_field_auto_splash').length) {
                        $element.after('<input type="hidden" name="fv_wp_flowplayer_field_auto_splash" id="fv_wp_flowplayer_field_auto_splash" />');
                      }

                      $element.siblings('#fv_wp_flowplayer_field_auto_splash').val(1);

                      fv_flowplayer_insertUpdateOrDeleteVideoMeta({
                        element: jQuery('#fv_wp_flowplayer_field_auto_splash'),
                        meta_section: 'video',
                        meta_key: 'auto_splash',
                        handle_delete: true
                      });
                      break;

                    case 'auto_caption':
                      if (!$element.siblings('#fv_wp_flowplayer_field_auto_caption').length) {
                        $element.after('<input type="hidden" name="fv_wp_flowplayer_field_auto_caption" id="fv_wp_flowplayer_field_auto_caption" />');
                      }

                      $element.siblings('#fv_wp_flowplayer_field_auto_caption').val(1);

                      fv_flowplayer_insertUpdateOrDeleteVideoMeta({
                        element: jQuery('#fv_wp_flowplayer_field_auto_caption'),
                        meta_section: 'video',
                        meta_key: 'auto_caption',
                        handle_delete: true
                      });
                      break;

                    case 'duration':
                      if (json_data.duration) {
                        if (!$element.siblings('#fv_wp_flowplayer_field_duration').length) {
                          $element.after('<input type="hidden" name="fv_wp_flowplayer_field_duration" id="fv_wp_flowplayer_field_duration" />');
                        }

                        var $duration_element = $element.siblings('#fv_wp_flowplayer_field_duration');
                        $duration_element.val(json_data.duration);

                        fv_flowplayer_insertUpdateOrDeleteVideoMeta({
                          element: $duration_element,
                          meta_section: 'video',
                          meta_key: 'duration',
                          handle_delete: true
                        });
                      } else {
                        var $duration_element = $element.siblings('#fv_wp_flowplayer_field_duration');

                        if ($duration_element.length) {
                          $duration_element.val('');

                          fv_flowplayer_insertUpdateOrDeleteVideoMeta({
                            element: $duration_element,
                            meta_section: 'video',
                            meta_key: 'duration',
                            handle_delete: true
                          });
                        }
                      }
                      break;

                    case 'last_video_meta_check':
                      if (json_data.ts) {
                        if (!$element.siblings('#fv_wp_flowplayer_field_last_video_meta_check').length) {
                          $element.after('<input type="hidden" name="fv_wp_flowplayer_field_last_video_meta_check" id="fv_wp_flowplayer_field_last_video_meta_check" />');
                        }

                        $element.siblings('#fv_wp_flowplayer_field_last_video_meta_check').val(json_data.ts);

                        fv_flowplayer_insertUpdateOrDeleteVideoMeta({
                          element: $element.siblings('#fv_wp_flowplayer_field_last_video_meta_check'),
                          meta_section: 'video',
                          meta_key: 'last_video_meta_check',
                          handle_delete: true
                        });
                      } else {
                        var $last_video_meta_check_element = $element.siblings('#fv_wp_flowplayer_field_last_video_meta_check');

                        if ($last_video_meta_check_element.length) {
                          $last_video_meta_check_element.val('');

                          fv_flowplayer_insertUpdateOrDeleteVideoMeta({
                            element: $last_video_meta_check_element,
                            meta_section: 'video',
                            meta_key: 'last_video_meta_check',
                            handle_delete: true
                          });
                        }
                      }
                      break;
                  }
                }
              }

              $element.removeData('fv_player_video_data_ajax');
              $element.removeData('fv_player_video_data_ajax_retry_count');

              // remove spinners
              jQuery('.fv-player-shortcode-editor-small-spinner').remove();
            }).error(function () {
              // remove element AJAX data
              $element.removeData('fv_player_video_data_ajax');

              // check if we should still retry
              var retry_count = $element.data('fv_player_video_data_ajax_retry_count');
              if (typeof(retry_count) == 'undefined' || retry_count < 2) {
                ajax_call();
                $element.data('fv_player_video_data_ajax_retry_count', (typeof(retry_count) == 'undefined' ? 1 : retry_count + 1));
              } else {
                // maximum retries reached
                $element.removeData('fv_player_video_data_ajax_retry_count');

                // check if we still have this element on page
                if ($element.closest("body").length > 0) {
                  // get this element's table
                  var
                    $parent_table = $element.closest('table'),
                    $playlist_row = jQuery('.fv-player-tab-playlist table tr[data-index="' + $parent_table.data('index') + '"] td.fvp_item_caption');

                  $playlist_row.html($caption_element.val());
                }
              }

              // remove spinners
              jQuery('.fv-player-shortcode-editor-small-spinner').remove();
            })
          );
        };

        ajax_call();
      }
    }

  });
  
  jQuery('.fv-player-editor-wrapper').each( function() { fv_show_video( jQuery(this) ) });  //  show last add more button only     

  $(document).on( 'fv_flowplayer_shortcode_insert', '.fv-player-editor-field', function() {
    fv_load_video_preview( jQuery(this).parents('.fv-player-editor-wrapper'));
  } );

  function fv_show_video( wrapper ) {
    if( wrapper.find('.fv-player-editor-field').val() ) {
      wrapper.find('.edit-video').show();
      wrapper.find('.add-video').hide();
    }
    else {
      wrapper.find('.edit-video').hide();
      wrapper.find('.add-video').show();
      wrapper.find('.fv-player-editor-preview').html('');
    }

    jQuery('[data-key='+wrapper.data('key')+'] .fv-player-editor-more').last().show();  //  show last add more button only
  }

  function fv_remove_video( id ) {
    $( '#widget-widget_fvplayer-'+id+'-text' ).val("");
    fv_show_video(id);
    $('#fv_edit_video-'+id+' .video-preview').html('');
  }

  function fv_load_video_preview( wrapper ) {
    var shortcode = $(wrapper).find('.fv-player-editor-field').val();
    console.log('fv_load_video_preview',shortcode);
    if( shortcode && shortcode.length === 0 ) {
      return false;
    }

    shortcode     = shortcode.replace( /(width=[\'"])\d*([\'"])/, "$1320$2" );  // 320
    shortcode     = shortcode.replace( /(height=[\'"])\d*([\'"])/, "$1240$2" ); // 240

    var url = fv_Player_site_base + '?fv_player_embed='+fv_player_editor_conf.preview_nonce+'&fv_player_preview=' + b64EncodeUnicode(shortcode);
    $.get(url, function(response) {
      wrapper.find('.fv-player-editor-preview').html( jQuery('#wrapper',response ) );
      $(document).trigger('fvp-preview-complete');
    } );

    fv_show_video(wrapper);
  }

  $(document).on('click','.fv-player-editor-remove', function(e) {console.log('.fv-player-editor-remove');
    var wrapper = $(this).parents('.fv-player-editor-wrapper');
    if( $('[data-key='+wrapper.data('key')+']').length == 1 ) { //  if there is only single video
      wrapper.find('.fv-player-editor-field').val('');
      fv_show_video(wrapper);
    } else {
      wrapper.remove();
      jQuery('.fv-player-editor-wrapper').each( function() { fv_show_video( jQuery(this) ) });  //  show last add more button only
    }
    return false;
  });

  $(document).on('click','.fv-player-editor-more', function(e) {
    var wrapper = $(this).parents('.fv-player-editor-wrapper');
    var new_wrapper = wrapper.clone();
    new_wrapper.find('.fv-player-editor-field').val('');
    fv_show_video(new_wrapper);
    new_wrapper.insertAfter( $('[data-key='+wrapper.data('key')+']:last') );  //  insert after last of the kind
    $(this).hide();

    return false;
  });
  
  $(document).on( 'click', '.fv-player-shortcode-copy', function(e) {
    var button = $(this);
    fv_player_clipboard( $(this).parents('tr').find('.fv-player-shortcode-input').val(), function() {
      button.html('Ok!');
      setTimeout( function() {
        button.html('Copy');
      }, 1000 );
    }, function() {
      button.html('Error');
    } );
    return false;
  });

});




jQuery(document).on('click','.fv_player_splash_list_preview', function() {
  fv_flowplayer_conf.current_video_to_edit = jQuery(this).parents('.thumbs').find('.fv_player_splash_list_preview').index(this);
  jQuery(this).parents('tr').find('.fv-player-edit').click();
});
jQuery(document).on('click','.column-shortcode input', function() {
  jQuery(this).select();
});


// mark each manually updated text field as such
jQuery(document).on('keydown', '#fv_wp_flowplayer_field_splash, #fv_wp_flowplayer_field_caption', function() {
  // remove spinner from playlist table row, if present
  var $element = jQuery(this);

  // if this element already has data set, don't do any of the selections below
  if (typeof($element.data('fv_player_user_updated')) != 'undefined') {
    return;
  }

  var
    $parent_row = $element.closest('tr'),
    $parent_table = $element.closest('table'),
    $playlist_row = jQuery('.fv-player-tab-playlist table tr[data-index="' + $parent_table.data('index') + '"] td.fvp_item_caption'),
    $playlist_row_spinner_div = $playlist_row.find('div.fv-player-shortcode-editor-small-spinner');

  if (this.id == 'fv_wp_flowplayer_field_caption' && $playlist_row_spinner_div.length > 0) {
    $playlist_row_spinner_div.removeClass('fv-player-shortcode-editor-small-spinner');
  }

  if( this.id == 'fv_wp_flowplayer_field_splash' ) {
    var $input = $parent_table.find('#fv_wp_flowplayer_field_auto_splash');
    var $meta_key = 'auto_splash';
  } else {
    var $input = $parent_table.find('#fv_wp_flowplayer_field_auto_caption');
    var $meta_key = 'auto_caption';
  }
  
  if( typeof($element.data('fv_player_user_updated')) == 'undefined' && $input.length > 0 ) {
    $input.val('');

    fv_flowplayer_insertUpdateOrDeleteVideoMeta({
      element: $input,
      meta_section: 'video',
      meta_key: $meta_key,
      handle_delete: true
    });
  }

  // remove spinner
  $parent_row.find('.fv-player-shortcode-editor-small-spinner').remove();

  console.log(this.id+' has been updated manually!');
  $element.data('fv_player_user_updated', 1);
});

jQuery(document).on('keyup', '[name=fv_wp_flowplayer_field_src], [name=fv_wp_flowplayer_field_rtmp_path]', fv_player_editor_show_stream_fields );
jQuery(document).on('fv_flowplayer_shortcode_item_switch fv_flowplayer_shortcode_new', fv_player_editor_show_stream_fields );

function fv_player_editor_show_stream_fields(e,index) {
  // on keyup
  var src = jQuery(this).val(),
    item = jQuery(this).parents('table');
  
  // on fv_flowplayer_shortcode_item_switch
  if( typeof(index) != "undefined" ) {
    item = jQuery('.fv-player-playlist-item[data-index='+index+']');
    src = item.find('[name=fv_wp_flowplayer_field_src]').val();
  }
  
  // on fv_flowplayer_shortcode_new
  if( item.length == 0 ) item = jQuery('.fv-player-playlist-item[data-index=0]');
  
  var rtmp = item.find('[name=fv_wp_flowplayer_field_rtmp_path]').val();
  var show = rtmp || src.match(/\.m3u8/) || src.match(/rtmp:/) || src.match(/\.mpd/);
  
  item.find('[name=fv_wp_flowplayer_field_live]').closest('tr').toggle(!!show);
  item.find('[name=fv_wp_flowplayer_field_audio]').closest('tr').toggle(!!show);
  
}
