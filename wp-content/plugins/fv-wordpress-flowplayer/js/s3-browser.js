jQuery( function($) {
    
    function fv_flowplayer_media_browser_add_tab(tabId, tabText, tabOnClickCallback) {
      if (!jQuery('#' + tabId).length) {
        // add Vimeo browser tab
        var
          $router = jQuery('.media-router:visible'),
          $item = $router.find('.media-menu-item:last').clone();

        $item
          .attr('id', tabId)
          .text(tabText)
          .on('click', tabOnClickCallback);

        $router.append($item);
      }
    };

    function fv_flowplayer_s3_browser_load_assets(bucket,path) {
      var
        $this = jQuery(this),
        $media_frame_content = jQuery('.media-frame-content:visible'),
        $overlay_div = jQuery('#fv-player-shortcode-editor-preview-spinner').clone().css({
          'height' : '100%'
        }),
        ajax_data = {
          action: "load_s3_assets",
        };

      $this.addClass('active').siblings().removeClass('active')
      $media_frame_content.html($overlay_div);

      if (typeof bucket === 'string' && bucket) {
        ajax_data['bucket'] = bucket;
      }
      if (typeof path === 'string' && path) {
        ajax_data['path'] = path;
      }

      jQuery.post(ajaxurl, ajax_data, function(ret) {
        var
          html = '<div class="files-div"><div class="filemanager">',
          last_selected_bucket = null;

        if (ret.buckets){
          html += '<div class="bucket-dropdown">';

          // prepare dropdown HTML
          var
            select_html = '<strong>S3 Bucket:</strong> &nbsp; <select name="bucket-dropdown" id="bucket-dropdown">',
            one_bucket_enabled = false;

          for (var i in ret.buckets) {
            select_html += '<option value="' + ret.buckets[i].id + '"' + (ret.active_bucket_id == ret.buckets[i].id ? ' selected="selected"' : '') + '>' + ret.buckets[i].name + '</option>'

            if (ret.buckets[i].id > -1) {
              one_bucket_enabled = true;
            }
          }

          select_html += '</select>';

          // check if we have at least a single enabled bucket
          // and if not, replace the whole select HTML with a warning message
          if (!one_bucket_enabled) {
            select_html = '<strong>You have no S3 buckets configured <a href="options-general.php?page=fvplayer#postbox-container-tab_hosting">in settings</a> or none of them has complete settings (region, key ID and secret key).</strong>';
          }

          html += select_html + '</div>';
        }

        if (ret.err) {
          html += '<div class="errors"><strong>' + ret.err + '</strong></div><hr /><br />';
        }

        html += '<div class="search">' +
          '<input type="search" placeholder="Find a file.." />' +
          '</div>' +
          '\t\t<div class="breadcrumbs"></div>\n' +
          '\n' +
          '\t\t<ul class="data"></ul>\n' +
          '\n' +
          '\t\t<div class="nothingfound">\n' +
          '\t\t\t<div class="nofiles"></div>\n' +
          '\t\t\t<span>No files here.</span>\n' +
          '\t\t</div>\n' +
          '\n' +
          '\t</div>\n' +
          '\t</div>';

        $media_frame_content.html(html);

        jQuery('#bucket-dropdown').on('change', function() {
          if (this.value >= 0) {
            fv_flowplayer_s3_browser_load_assets(this.value);
          } else {
            var $err_div = jQuery('.filemanager .errors');

            if (!$err_div.length) {
              $err_div = jQuery('<div class="errors"></div>');
              $err_div.insertBefore(jQuery('.filemanager .search'));
              $err_div.after('<hr /><br />');
            }

            $err_div.html('<strong>Bucket is missing settings. Please make sure you assigned region, key ID and secret key to this bucket.</strong>');
            return false;
          }
        });

        fv_flowplayer_s3_browse( ret.items );
      } );

      return false;
    };

  $( document ).on( "mediaBrowserOpen", function(event) {
    fv_flowplayer_media_browser_add_tab('fv_flowplayer_s3_browser_media_tab', 'Amazon S3', fv_flowplayer_s3_browser_load_assets);
  });
  
  $( document ).on( "click", ".folders, .breadcrumbs a", function(event) {
    fv_flowplayer_s3_browser_load_assets( jQuery('#bucket-dropdown').val(), jQuery(this).attr('href') );
    return false;
  });
});




fv_flowplayer_s3_browse = function(data, ajax_search_callback) {

  var filemanager = jQuery('.filemanager'),
    breadcrumbs = jQuery('.breadcrumbs'),
    fileList = filemanager.find('.data');

  var response = [data],
    currentPath = '',
    breadcrumbsUrls = [];

  var folders = [],
    files = [];

  jQuery(window).off('fv-player-browser-open-folder');
  jQuery(window).on('fv-player-browser-open-folder', function(e, path){
    currentPath = data.path;
    breadcrumbsUrls.push(data.path);
    render(data.items);
  }).trigger('fv-player-browser-open-folder', [ '' ] );

  // Hiding and showing the search box
  filemanager.find('.search').click(function(){

    var search = jQuery(this);

    search.find('span').hide();
    search.find('input[type=search]').show().focus();

  }).hide(); // not implemented for S3


  // Listening for keyboard input on the search field.
  // We are using the "input" event which detects cut and paste
  // in addition to keyboard input.

  filemanager.find('input').on('input', function(e){

    // do nothing if we should use AJAX to perform the search
    // ... in such case, we'll use the Enter key to search
    if (typeof(ajax_search_callback) !== 'undefined') {
      return;
    }

    folders = [];
    files = [];

    var value = this.value.trim();

    if(value.length) {

      filemanager.addClass('searching');

      // Update the hash on every key stroke
      jQuery(window).trigger('fv-player-browser-open-folder', [ 'search=' + value.trim() ]);
    }

    else {

      filemanager.removeClass('searching');
      jQuery(window).trigger('fv-player-browser-open-folder', [ currentPath ] );
    }

  }).on('keyup', function(e){

    // Clicking 'ESC' button triggers focusout and cancels the search

    var search = jQuery(this);

    if(e.keyCode == 27) {

      search.trigger('focusout');

    } else if (e.keyCode == 13) {
      // Clicking 'Enter' button triggers AJAX search callback
      if (typeof(ajax_search_callback) !== 'undefined') {
        ajax_search_callback();
      }
    }

  }).focusout(function(e){

    // Cancel the search

    var search = jQuery(this);

    if(!search.val().trim().length) {
      jQuery(window).trigger('fv-player-browser-open-folder', [ currentPath ] );

      search.hide();
      search.parent().find('span').show();
    }

  });


  // Splits a file path and turns it into clickable breadcrumbs
  function generateBreadcrumbs(nextDir){
    var path = nextDir.split('/').slice(0);
    for(var i=1;i<path.length;i++){
      path[i] = path[i-1]+ '/' +path[i];
    }
    return path;
  }

  // Render the HTML for the file manager
  function render(data) {

    var scannedFolders = [],
      scannedFiles = [];

    if(Array.isArray(data)) {

      data.forEach(function (d) {

        if (d.type === 'folder') {
          scannedFolders.push(d);
        }
        else {
          scannedFiles.push(d);
        }

      });

    } else if(typeof data === 'object') {
      scannedFolders = data.folders;
      scannedFiles = data.files;

    }

    // Empty the old result and make the new one
    fileList.empty().hide();
    
    if(!scannedFolders.length && !scannedFiles.length) {
      filemanager.find('.nothingfound').show();
    }
    else {
      filemanager.find('.nothingfound').hide();
    }

    if(scannedFolders.length) {

      scannedFolders.forEach(function(f) {
        var name = escapeHTML(f.name).replace(/\/$/,'');
        fileList.append( jQuery('<li class="folders"><a href="'+f.path+'" title="'+name+'" class="folders"><span class="icon folder"></span><span class="name">'+name+'</span></a></li>'));
      });

    }

    if(scannedFiles.length) {
      
      function fileGetBase( link ) {
        link = link.replace(/\.[a-z0-9]+$/,'');
        return link;
      }

      function fileUrlIntoShortcodeEditor() {
        var
          $this            = jQuery(this),
          $url_input       = jQuery('.fv_flowplayer_target'),
          $popup_close_btn = jQuery('.media-modal-close:visible');

        var find = [ fileGetBase($this.attr('href')) ];
        if( window.fv_player_shortcode_editor_qualities ) {
          Object.keys(fv_player_shortcode_editor_qualities).forEach( function(prefix) {
            var re = new RegExp(prefix+'$');
            if( find[0].match(re) ) {
              find.push( find[0].replace(re,'') );
            }
          });
        }
        
        var splash = false;
        for( var i in find ) {
          for( var j in scannedFiles ) {
            var f = scannedFiles[j];
            if( f.link.match(/\.(jpg|jpeg|png|gif)$/) && fileGetBase(f.link) == find[i] && f.link != $this.attr('href') ) {
              splash = f.link;
            }
          }
        }

        $url_input
          .val($this.attr('href'))
          .removeClass('fv_flowplayer_target' )
          .trigger('keyup')   // this changes the HLS key field visibility in FV Player Pro
          .trigger('change'); // this check the video duration etc.

        if( splash && $url_input.attr('id').match(/^fv_wp_flowplayer_field_src/) ) {
          var splash_input = $url_input.parents('table').find('#fv_wp_flowplayer_field_splash');
          if( splash_input.val() == '' ) {
            splash_input.val(splash);
          }
        }

        $popup_close_btn.click();

        return false;
      }

      scannedFiles.forEach(function(f) {

        var fileSize = typeof(f.size) == "number" ? bytesToSize(f.size) : f.size, // just show the size for placeholders
          name = escapeHTML(f.name),
          fileType = name.split('.'),
          icon = '<span class="icon file"></span>',
          fileType = fileType[fileType.length-1],
          icon = '<span class="icon file f-'+fileType+'">.'+fileType+'</span>',
          link = f.link ? 'href="'+ f.link+'"' : '',
          $href = jQuery('<a '+link+' title="'+ name +'" class="files">'+icon+'<span class="name">'+ name +'</span> <span class="details">'+fileSize+'</span></a>'),
          file = jQuery('<li class="files"></li>');

        if( f.link ) {
          $href.on('click', fileUrlIntoShortcodeEditor);
        } else { // click on placeholder
          $href.on('click', function() {
            return false;
          });
        }

        file.append($href);
        file.appendTo(fileList);
      });

    }


    // Generate the breadcrumbs
    var url = '';
    if(filemanager.hasClass('searching')){
      url = '<span>Search results: </span>';
      fileList.removeClass('animated');
    }
    else {
      fileList.addClass('animated');

      var right_arrow =  '<span class="arrow_sign">→</span> ';
      breadcrumbsUrls.forEach(function (u, i) {
        var name = u.replace(/\/$/,'').split('/');
        if( name.length > 1 ) {
          name.forEach(function (n, k) {
            var path = '';
            for( var j=0; j<k+1; j++ ) {
              path += name[j]+'/';
            }
            url += '<a href="'+path+'"><span class="folderName">'+n+'</span></a>';
            if( k < name.length-1 ) url += right_arrow;
          });
        }

      });

    }

    breadcrumbs.text('').append(url);

    fileList.fadeIn();
  }


  // This function escapes special html characters in names

  function escapeHTML(text) {
    return text.replace(/\&/g,'&amp;').replace(/\</g,'&lt;').replace(/\>/g,'&gt;');
  }


  // Convert file sizes from bytes to human readable units

  function bytesToSize(bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Bytes';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
  }

//	});
};