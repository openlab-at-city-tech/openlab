(function ($) {
  'use strict';
  $.widget("cp.OutoftheBox", {
    options: {
      listtoken: null,
      searchQuery: null
    },

    _create: function () {
      /* Remove no JS message */
      this.element.removeClass('jsdisabled');
      this.element.show();
      this.options.topContainer = this.element.parent();
      this.options.loadingContainer = this.element.find('.loading');

      /* Set the max width for the element */
      this.element.css('width', '100%');

      /* Set the shortcode ID */
      this.options.listtoken = this.element.attr('data-token');
      this.options.account_id = this.element.attr('data-account-id');

      /* Local Cache */
      this.cache = {};

      /* Upload values */
      this.uploaded_files = [];
      this.uploaded_files_storage = {};
      this.number_of_uploaded_files = {
        'Max': this.element.find('input[name="maxnumberofuploads"]').val(),
        'Counter': 0
      };

      /* Mobile? */
      this.options.mobile = false;
      if (/Android|webOS|iPhone|iPod|iPad|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        var userAgent = navigator.userAgent.toLowerCase();
        if ((userAgent.search("android") > -1) && (userAgent.search("mobile") > -1)) {
          this.options.mobile = true;
        } else if ((userAgent.search("android") > -1) && !(userAgent.search("mobile") > -1)) {
          this.options.mobile = false;
        } else {
          this.options.mobile = true;
        }
      } else if (this.options.is_mobile === '1') {
        /* Check if user is using a mobile device (including tables) detected by WordPress, alters css*/
        this.options.mobile = true;
      }

      if (this.options.mobile) {
        $('html').addClass('oftb-mobile');
      }

      this.recaptcha_passed = false;

      /* Ignite! */
      this._initiate();

    },

    _destroy: function () {
      return this._super();
    },

    _setOption: function (key, value) {
      this._super(key, value);
    },

    _initiate: function () {
      var self = this;


      self.options.topContainer.one('inview', function (event, isInView) {

        self._initResizeHandler();
        self._refreshView();
        self._initCache();

        if (self.options.recaptcha !== '') {
          self._initReCaptcha();
        }

        if (self.options.topContainer.hasClass('files') || self.options.topContainer.hasClass('gallery') || self.options.topContainer.hasClass('search')) {
          self._initFilebrowser();
        }

        if (self.element.find('.fileuploadform').length > 0) {
          self._initUploadBox();
        }

        if (self.options.topContainer.hasClass('video') || self.options.topContainer.hasClass('audio')) {
          self._initMediaPlayer();
        }
      });

      if (self.options.topContainer.hasClass('initiate')) {
        self.options.topContainer.trigger('inview');
      }

      window.setTimeout(function () {
        self.initated = true;
      }, 2000);
    },

    _initFilebrowser: function () {
      this.renderNavMenu();

      var data = {};
      /* Check if Deep link */
      var url = new URL(window.location);
      var search_params = new URLSearchParams(url.search);
      var deeplink = search_params.get('wpcp_link');

      if (deeplink !== null) {
        var hash_params = JSON.parse(decodeURIComponent(window.atob(deeplink)));

        if (hash_params.org_path === this.element.attr('data-org-path')) {
          this.options.account_id = hash_params.account_id;
          this.element.attr('data-path', hash_params.last_path);
          this.option.focus_id = hash_params.focus_id;
        }
      }

      /* Do first Request*/
      this._getFileList(data);
    },

    _initMediaPlayer: function () {
      var self = this;

      var event = new Event('init_media_player');
      self.element[0].dispatchEvent(event);
    },

    _initUploadBox: function () {
      var self = this;

      var is_standalone = self.options.topContainer.hasClass('upload');
      var upload_box = self.element.find('.fileuploadform');
      var upload_form = upload_box.closest('form');
      var autoUpload = true;

      /* Drag & Drop functionality for the Upload Box */
      this._initDragDrop();

      /* Remove Folder upload button if isn't supported by browser */
      if (self._helperIsIE() !== false && self._helperIsIE() < 10) {
        $('.upload-multiple-files').parent().remove();
      }

      /* Set Cookie for Guest uploads */
      if (is_standalone && document.cookie.indexOf("OftB-ID=") == -1) {
        var date = new Date();
        date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toUTCString();
        var id = Math.random().toString(36).substr(2, 16);
        document.cookie = "OftB-ID=" + id + expires + "; path=" + self.options.cookie_path + "; domain=" + self.options.cookie_domain + ";";
      }

      /* Add custom Upload Button on forms to start upload before submitting form*/
      if (upload_form.length > 0) {
        var $form_submit_btn = upload_form.find('input[type="submit"]:visible, input[type="button"].gform_next_button:visible, button[id^="gform_submit_button"]:visible, button[class*="cf7md-submit-btn"]:visible');
        var $form_upload_btn = upload_form.find('#wpcp_form_submit');

        if ($form_upload_btn.length === 0) {
          $form_upload_btn = $form_submit_btn.clone().attr('id', 'wpcp_form_submit').prop('onclick', null).prop('onkeypress', null).off().addClass('wpcf7-submit');
        }

        $form_upload_btn.click(function (e) {

          if ((upload_box.closest('.gfield_contains_required').length) && (self.number_of_uploaded_files.Counter === 0)) {
            upload_box.css('border-color', 'red');
            return false;
          }

          $(this).addClass("firing").prop("disabled", true).fadeTo(400, 0.3);

          if (upload_box.find(".template-upload").length > 0) {
            upload_box.css('border-color', '');
            upload_box.trigger('outofthebox-startupload');
            $(this).val(self.options.str_processing);
            return false;
          }

          if (upload_form.find(".template-upload").length === 0 && upload_form.hasClass('submitting') === false) {

            upload_form.addClass("submitting")

            $(this).remove();
            $form_submit_btn.prop("disabled", false).show().fadeTo(400, 0.3).trigger('click').prop("disabled", true);

            $('html, body').animate({
              scrollTop: $form_submit_btn.offset().top
            }, 1500);
          }
        });
      }

      /* Disable Auto Upload in case the Upload Box is part of a Gravity Form or Contact Form */
      if (upload_form.length > 0 && upload_form.find('#gravityflow_update_button').length === 0 && !self.options.topContainer.hasClass('auto_upload')) {
        autoUpload = false;

        /* Do the upload before the form is submitted */
        if (upload_form.find('#wpcp_form_submit').length === 0) {
          $form_upload_btn.insertBefore($form_submit_btn);
          $form_submit_btn.prop("disabled", true).hide();

          // Trigger Contact Form 7 'required field' events
          upload_box.trigger('outofthebox-removeupload');
        }
      }

      /* If the Upload Box is part of a Gravity Form or Contact Form, render the rows of the already uploaded content */
      if (upload_form.length > 0 && self.element.find('input[name="fileupload-filelist_' + self.options.listtoken + '"]').val().length > 0) {
        self.uploaded_files_storage = JSON.parse(self.element.find('input[name="fileupload-filelist_' + self.options.listtoken + '"]').val());

        $.each(self.uploaded_files_storage, function (index, file) {
          self._uploadRenderRow(file);
          self._uploadRenderRowOnFinish(file, true);
          self.number_of_uploaded_files.Counter++;
        });
      }

      /* Update form input fields to store upload files */
      self.element.find('.fileupload-filelist').change(function () {
        self.element.parent().next('.fileupload-input-filelist').val($(this).val());
      });
      self.element.find('.fileupload-filelist').trigger('change');

      self.element.find('.fileupload-list').click(function () {
        self.element.find(".upload-input-button:first").trigger("click");
      });

      /* The following browsers support XHR and(CHUNKED) file uploads, 
       * which allows advanced usage of the file upload
       * 
       * ###Desktop browsers###
       * - Google Chrome
       * - Apple Safari 5.0+ (6.0+)
       * - Mozilla Firefox 4.0+ (4.0+)
       * - Opera 12.0+ (12.0+)
       * Microsoft Internet Explorer 10.0+ (10.0+)
       * 
       * ###Mobile browsers###
       * Apple Safari Mobile on iOS 6.0+  (6.0+)
       * Google Chrome on iOS 6.0+ (6.0+)
       * Google Chrome on Android 4.0+  (4.0+)
       * Default Browser on Android 3.2+  (NOT SUPPORTED)
       * Opera Mobile 12.0+ (NOT SUPPORTED)
       */
      var support_xhr = $.support.xhrFileUpload;
      var support_chunked = false;

      var multipart_val = (support_xhr) ? false : true;
      var method_val = 'POST';
      var singlefileuploads_val = true;
      var maxchunksize_val = (support_chunked) ? 20 * 320 * 1000 : 0; //Multiple of 320kb, the recommended fragment size is between 5-10 MB.

      /* Update max file upload for direct uploads */
      //if (support_xhr && self.element.find('input[name="maxfilesize"]').attr('data-limit') === '0') {
      self.element.find('input[name="maxfilesize"]').val('');
      //  self.element.find('.fileupload-container').find('.max-file-size').text(self.options.str_uploading_no_limit);
      //}

      /* Initiate the Blueimp File Upload plugin*/
      upload_box.fileupload({
        url: self.options.ajax_url,
        type: method_val,
        maxChunkSize: maxchunksize_val,
        singleFileUploads: singlefileuploads_val,
        multipart: multipart_val,
        dataType: 'json',
        autoUpload: autoUpload,
        maxFileSize: (support_xhr && self.element.find('input[name="maxfilesize"]').attr('data-limit') === '0') ? 'undefined' : self.options.post_max_size,
        acceptFileTypes: new RegExp(self.element.find('input[name="acceptfiletypes"]').val(), "i"),
        dropZone: self.element,
        messages: {
          maxNumberOfFiles: self.options.maxNumberOfFiles,
          acceptFileTypes: self.options.acceptFileTypes,
          maxFileSize: self.options.maxFileSize,
          minFileSize: self.options.minFileSize
        },
        limitConcurrentUploads: 3,
        disableImageLoad: true,
        disableImageResize: true,
        disableImagePreview: true,
        disableAudioPreview: true,
        disableVideoPreview: true,
        uploadTemplateId: null,
        downloadTemplateId: null,
        add: function (e, data) {

          $.each(data.files, function (index, file) {
            self.number_of_uploaded_files.Counter++;
            file.hash = file.name.hashCode() + '_' + Math.floor(Math.random() * 1000000);
            file.listtoken = self.options.listtoken;

            if ("webkitRelativePath" in file) {
              file.path = file.webkitRelativePath.replace(file.name, '');
            } else {
              file.path = '';
            }

            /* Files larger than 300MB cannot be uploaded directly to Dropbox :( */
            file.directupload = support_xhr && (file.size < 314572800);

            file = self._uploadValidateFile(file);
            var row = self._uploadRenderRow(file);

            if (file.error !== false) {
              upload_box.trigger('outofthebox-removeupload', [data.files[index]]);
              data.files.splice(index, 1);
            }

            row.find('.cancel-upload').on('click', function (e) {
              e.preventDefault();
              e.stopPropagation();

              data.files.splice(index, 1);
              self.number_of_uploaded_files.Counter--;
              self._uploadDeleteRow(file, 0);

              upload_box.trigger('outofthebox-removeupload', [data.files[index]]);
            });

          });

          upload_box.trigger('outofthebox-addupload', [data]);

          if (data.autoUpload || (data.autoUpload !== false &&
                  $(this).fileupload('option', 'autoUpload'))) {
            if (data.files.length > 0) {
              data.process().done(function () {
                self._uploadDoRequest(data);
              });
            }
          } else {
            $(this).on('outofthebox-startupload', function () {
              if (data.files.length > 0) {
                data.process().done(function () {
                  self._uploadDoRequest(data);
                });
              }
            });
          }
        },
        submit: function (e, data) {
          /*  Enable navigation prompt */
          window.onbeforeunload = function () {
            return true;
          };

          self.element.attr('uploading', 'true');

          var filehash;
          var file;

          $.each(data.files, function (index, entry) {
            file = entry;
            self._uploadRenderRowOnStart(file);
            filehash = file.hash;
          });

          if (autoUpload === false && self.element.visible(true) === false) {
            $('html, body').stop().animate({
              scrollTop: self.element.offset().top - 200
            }, 1000);
          }


          /* Do Direct Upload */
          if (file.directupload) {
            var $this = $(this);

            self.element.find('.fileuploadform').fileupload('option', 'multipart', false);

            $.ajax({type: "POST",
              url: OutoftheBox_vars.ajax_url,
              data: {
                action: 'outofthebox-upload-file',
                account_id: self.options.account_id,
                type: 'get-direct-url',
                filename: file.name,
                file_size: file.size,
                file_path: file.path,
                mimetype: file.type,
                orgin: (!window.location.origin) ? window.location.protocol + "//"
                        + window.location.hostname
                        + (window.location.port ? ':' + window.location.port : '') : window.location.origin,
                lastpath: self.element.attr('data-path'),
                listtoken: self.options.listtoken,
                _ajax_nonce: self.options.upload_nonce,
              },
              error: function () {
                file.error = self.options.str_error;
                self._uploadRenderRowOnFinish(file);
              },
              success: function (response) {
                if (typeof response.result === 'undefined' || typeof response.url === 'undefined') {
                  file.error = self.options.str_error;
                  self._uploadRenderRowOnFinish(file);
                } else {
                  data.url = response.url;
                  file.fileid = response.id;
                  file.convert = response.convert;
                  data.jqXHR = $this.fileupload('send', data);
                }
              },
              dataType: 'json'
            });
            return false;

            /* Do Upload via Server */
          } else {
            self.element.find('.fileuploadform').fileupload('option', 'multipart', true);

            data.formData = {
              action: 'outofthebox-upload-file',
              account_id: self.options.account_id,
              type: 'do-upload',
              hash: filehash,
              file_path: file.path,
              lastpath: self.element.attr('data-path'),
              listtoken: self.options.listtoken,
              _ajax_nonce: self.options.upload_nonce
            };
          }

        }
      }).on('fileuploadsubmit', function (e, data) {

      }).on('fileuploadprogress', function (e, data) {

        var file = data.files[0];
        if (file.directupload) {
          /* Upload Progress for direct upload */
          var progress = parseInt(data.loaded / data.total * 100, 10);
          self._uploadRenderRowOnProgress(file, {percentage: progress, progress: 'uploading_to_cloud'});
        } else {
          /* Upload Progress for upload via server*/
          var progress = parseInt(data.loaded / data.total * 100, 10) / 2;

          self._uploadRenderRowOnProgress(file, {percentage: progress, progress: 'uploading_to_server'});

          if (progress >= 50) {
            self._uploadRenderRowOnProgress(file, {percentage: 50, progress: 'uploading_to_cloud'});

            setTimeout(function () {
              self._uploadGetProgress(file);
            }, 2000);
          }
        }

      }).on('fileuploadstopped', function () {

      }).on('fileuploaddone', function (e, data) {
        sendGooglePageView('Upload file');
      }).on('fileuploadalways', function (e, data) {

        var file = data.files[0];
        if (data.result === null) {
          file.error = self.options.str_error;
          self._uploadRenderRowOnFinish(file);
        }

        if (file.directupload) {
          /* Final Event after upload for Direct upload */
          if (file.convert) {
            self._uploadDoConvert(file);
          } else {
            self._uploadRenderRowOnFinish(file);
          }
        } else {
          /* Final Event after upload for upload via Server*/
          if (typeof data.result !== 'undefined') {
            if (typeof data.result.status !== 'undefined') {
              if (data.result.status.progress === 'finished' || data.result.status.progress === 'failed') {
                self._uploadRenderRowOnFinish(data.result.file);
              }
            } else {
              data.result.file.error = self.options.str_error;
              self._uploadRenderRowOnFinish(data.result.file);
            }
          } else {
            file.error = self.options.str_error;
            self._uploadRenderRowOnFinish(file);
          }
        }

      }).on('fileuploaddrop', function (e, data) {
        var uploadcontainer = $(this);
        $('html, body').animate({
          scrollTop: uploadcontainer.offset().top
        }, 1500);
      }).on('outofthebox-upload-finished', function (e) {

        self.element.attr('uploading', '');

        if ($('.OutoftheBox[uploading=true]').length > 0) {
          return;
        }

        if (upload_form.length > 0 && upload_form.find('#gravityflow_update_button').length === 0 && !self.options.topContainer.hasClass('auto_upload')) {
          $form_upload_btn.trigger('click');
        }

      });
    },

    _getFileList: function (data) {
      var request = this._buildFileListRequest();

      this.element.find('.no_results').remove();
      this.options.loadingContainer.removeClass('initialize upload error').fadeIn(400);

      this.element.find('.nav-refresh i').addClass('fa-spin');
      request(data, this.renderBrowserContent, this);
    },

    _buildFileListRequest: function (data) {

      var self = this;

      return  this._pipeline({
        url: self.options.ajax_url,
        type: "POST",
        dataType: "json",
        data: function (d) {

          d.listtoken = self.options.listtoken;
          d.account_id = self.options.account_id;
          d.lastpath = self.element.attr('data-path');
          d.sort = self.element.attr('data-sort');
          d.deeplink = self.element.attr('data-deeplink');
          d.filelayout = self.element.attr('data-layout');
          d.action = 'outofthebox-get-filelist';
          d._ajax_nonce = self.options.refresh_nonce;
          d.mobile = self.options.mobile;

          if (self.element.attr('data-list') === 'gallery') {
            d.action = 'outofthebox-get-gallery';
            d._ajax_nonce = self.options.gallery_nonce;
          }

          d.query = self.searchQuery;

          return d;
        }
      });
    },

    /**
     * Initiate the Search Box functionality
     */
    _initSearchBox: function () {
      var self = this;
      var $nav_search_box = this.element.find('.nav-search');

      /* Search qtip popup */
      $nav_search_box.qtip({
        prerender: false,
        id: 'search-' + self.options.listtoken,
        content: {
          text: $nav_search_box.next('.search-div'),
          button: $nav_search_box.next('.search-div').find('.search-remove')
        },
        position: {
          my: 'top right',
          at: 'bottom center',
          target: $nav_search_box,
          viewport: $(window),
          adjust: {
            scroll: false
          }
        },
        style: {
          classes: 'OutoftheBox search ' + self.options.content_skin
        },
        show: {
          effect: function () {
            $(this).fadeTo(90, 1, function () {
              $('input', this).focus();
            });
          }
        },
        hide: {
          fixed: true,
          delay: 1500,
          event: 'unfocus'
        }
      });

      /* Search Key Up event */
      self.element.find('.search-input').on("keyup", function (event) {

        self.searchQuery = $(this).val();

        if ($(this).val().length > 0) {
          self.options.loadingContainer.addClass('search');
          self.element.find('.nav-search').addClass('inuse');

          clearTimeout(self.updateTimer);
          self.updateTimer = setTimeout(function () {
            self.element.find('.loading, .ajax-filelist').show();
            self._getFileList({});
          }, 1000);

        } else {
          self.element.find('.nav-search').removeClass('inuse');
          if (self.element.hasClass('searchlist')) {
            self.element.find('.loading, .ajax-filelist').hide();
            self.element.find('.ajax-filelist').html('');
          }
        }
      });

      /* Search submit button event [Search Mode] */
      self.element.find('.submit-search').click(function () {

        self.searchQuery = $(this).val();

        if ($(this).val().length > 0) {

          clearTimeout(self.updateTimer);
          self.updateTimer = setTimeout(function () {
            self.element.find('.loading, .ajax-filelist').show();
            self._getFileList({});
          }, 1000);

        } else {
          self.element.find('.loading, .ajax-filelist').hide();
          self.element.find('.ajax-filelist').html('');
        }
      });

      self.element.find('.search-remove').click(function () {
        if ($(this).parent().find('.search-input').val() !== '') {
          self.clearSearchBox();
        }
      });

    },

    clearSearchBox: function () {
      $('[data-qtip-id="search-' + this.options.listtoken + '"] .search-input').val('').trigger('keyup');
    },

    /* Initiate the Settings menu functionality */
    _initGearMenu: function () {
      var self = this;
      var $gearmenu = this.element.find('.nav-gear');

      $gearmenu.qtip({
        prerender: false,
        id: 'nav-' + self.options.listtoken,
        content: {
          text: $gearmenu.next('.gear-menu')
        },
        position: {
          my: 'top right',
          at: 'bottom center',
          target: $gearmenu,
          viewport: $(window),
          adjust: {
            scroll: false
          }
        },
        style: {
          classes: 'OutoftheBox ' + self.options.content_skin
        },
        show: {
          event: 'click, mouseenter',
          solo: true
        },
        hide: {
          event: 'mouseleave unfocus',
          fixed: true,
          delay: 200
        },
        events: {
          show: function (event, api) {
            var selectedboxes = self._helperReadArrCheckBoxes("[data-token='" + self.options.listtoken + "'] input[name='selected-files[]']");

            if (selectedboxes.length === 0) {
              api.elements.content.find(".selected-files-to-zip").parent().hide();
              api.elements.content.find(".all-files-to-zip").parent().show();
              api.elements.content.find(".selected-files-delete").parent().hide();
              api.elements.content.find(".selected-files-move").parent().hide();
            } else {
              api.elements.content.find(".selected-files-to-zip").parent().show();
              api.elements.content.find(".all-files-to-zip").parent().hide();
              api.elements.content.find(".selected-files-delete").parent().show();
              api.elements.content.find(".selected-files-move").parent().show();
            }

            var visibleelements = api.elements.content.find('ul > li').not('.gear-menu-no-options').filter(function () {
              return $(this).css('display') !== 'none';
            });

            if (visibleelements.length > 0) {
              api.elements.content.find('.gear-menu-no-options').hide();
            } else {
              api.elements.content.find('.gear-menu-no-options').show();
            }

          }
        }
      });


      /* Layout button is the Switch between table and grid mode */
      this.element.find('.nav-layout').unbind('click').click(function () {

        if (self.element.attr('data-layout') === 'list') {
          self.element.attr('data-layout', 'grid');
        } else {
          self.element.attr('data-layout', 'list');
        }

        self._getFileList({});
      });

      /* Zip button*/
      this.element.find('.select-all-files').unbind('click').click(function () {
        self.element.find(".selected-files:checkbox").prop("checked", $(this).prop("checked"));
        if ($(this).prop("checked") === true) {
          self.element.find('.entry:not(".newfolder")').addClass('isselected');
        } else {
          self.element.find('.entry:not(".newfolder")').removeClass('isselected');
        }
      });

      this.element.find('.all-files-to-zip, .selected-files-to-zip').unbind('click').click(function (event) {

        var entries = [];

        if ($(event.target).hasClass('all-files-to-zip')) {
          self.element.find('.select-all-files').trigger('click');
          entries = self._helperReadArrCheckBoxes("[data-token='" + self.options.listtoken + "'] input[name='selected-files[]']");
        }

        if ($(event.target).hasClass('selected-files-to-zip')) {
          entries = self._helperReadArrCheckBoxes("[data-token='" + self.options.listtoken + "'] input[name='selected-files[]']");
        }

        var data = {
          action: 'outofthebox-create-zip',
          account_id: self.options.account_id,
          listtoken: self.options.listtoken,
          lastpath: self.element.attr('data-path'),
          _ajax_nonce: self.options.createzip_nonce,
          files: entries
        };

        var $processor_icon = $('<div class="processor_icon"><i class="fas fa-cog fa-spin fa-1x fa-fw"></i></div>').css({'margin-right': '5px', 'display': 'inline-grid'});
        self.element.find(".layout-grid input:checked[name='selected-files[]']").closest('.entry').find(".entry-name-view").prepend($processor_icon);
        self.element.find(".layout-list input:checked[name='selected-files[]']").closest('.entry').find(".entry_name").prepend($processor_icon);

        self.element.find('.processor_icon').delay(5000).fadeOut('slow', function () {
          $(this).remove();
        });

        if ($(event.target).hasClass('all-files-to-zip')) {
          self.element.find('.select-all-files').trigger('click');
        }

        $('.qtip').qtip('hide');
        $(this).attr('href', self.options.ajax_url + "?" + $.param(data));

        return;
      });

      /* Add scroll event to nav-upload */
      self.element.find('.nav-upload').click(function () {
        $('.qtip').qtip('hide');

        var uploadcontainer = self.element.find('.fileupload-container');

        $('html, body').animate({
          scrollTop: uploadcontainer.offset().top
        }, 1500);
        for (var i = 0; i < 3; i++) {
          uploadcontainer.find('.fileupload-buttonbar').fadeTo('slow', 0.5).fadeTo('slow', 1.0);
        }
      });

      /* Move multiple files at once */
      self.element.find('.selected-files-move').click(function () {
        $('.qtip').qtip('hide');

        var entries = self.element.find("input[name='selected-files[]']:checked");

        if (entries.length === 0) {
          return false;
        }

        self._actionMoveEntries(entries);
      });

      /* Delete multiple files at once */
      self.element.find('.selected-files-delete').click(function () {
        $('.qtip').qtip('hide');

        var entries = self.element.find("input[name='selected-files[]']:checked");

        if (entries.length === 0) {
          return false;
        }

        self._actionDeleteEntries(entries);
      });

      /* Direct Link Folder */
      self.element.find('.entry_action_deeplink_folder').unbind('click').click(function (e) {
        self._actionCreateDeepLink($(this));
      });

      /* Social Share Folder */
      self.element.find('.entry_action_shortlink_folder').unbind('click').click(function (e) {
        self._actionShareEntry($(this));
      });
    },

    _initAccountSelector: function () {
      var self = this;

      self.element.find('.nav-account-selector').click(function () {
        self._actionSelectAccount();
      });

    },

    /**
     * Render the Content after receving the File List
     */
    renderBrowserContent: function (self, json) {
      if (json === false) {
        self.element.find('.nav-title').html(self.options.str_no_filelist);
        self.options.loadingContainer.addClass('error');
      } else {
        self.options.loadingContainer.fadeIn(200);


        self.element.find('.ajax-filelist').html(json.html);
        self.element.find('.image-collage').hide();
        self.element.find('.nav-title').html(json.breadcrumb);
        self.element.find('.current-folder-raw').text(json.rawpath);

        if (json.lastpath !== null) {
          self.element.attr('data-path', json.lastpath);
        }

      }

      self.element.find('.breadcrumb').one('inview', function (event, isInView) {
        self.renderBreadCrumb();
      });

      self.element.find('.nav-refresh i').removeClass('fa-spin');
      self.unveilImages();

      if (self.element.hasClass('gridgallery')) {
        self.renderContentForGallery();
        self._initLazyLoading();
      } else {
        self.renderContentForBrowser();
      }

      /** Unveil Handlers for containers with overflow */
      self.element.find('.ajax-filelist').scroll(function () {
        clearTimeout(window.scrollUnveilTimer);
        window.scrollTimer = setTimeout(function () {
          self.unveilImages();
        }, 500);
      });
      $('#TB_ajaxContent').scroll(function () {
        clearTimeout(window.scrollUnveilTimer);
        window.scrollTimer = setTimeout(function () {
          self.unveilImages();
        }, 500);
      });

      self.unveilImages();

      /* Hover Events */
      self.element.find('.entry').unbind('hover').hover(
              function () {
                $(this).addClass('hasfocus');
              },
              function () {
                $(this).removeClass('hasfocus');
              }
      ).on('mouseover', function () {
        $(this).addClass('hasfocus');
      }).unbind('click').click(function () {
        /* CheckBox Event */
        //$(this).find('.entry_checkbox input[type="checkbox"]').trigger('click');
      }).on("contextmenu", function (e) {
        /* Disable right clicks */
        return false;
      });

      /* Folder Click events */
      self.element.find('.folder, .image-folder').unbind('click').click(function (e) {

        if ($(this).hasClass('isdragged') || $(this).hasClass('newfolder')) {
          return false;
        }

        e.stopPropagation();
        self.clearSearchBox();

        var data = {
          OutoftheBoxpath: $(this).closest('.folder, .image-folder').attr('data-url'),
        };

        self._getFileList(data);

        if ($(window).scrollTop() > self.element.find('.nav-header').offset().top) {
          $('html, body').stop().animate({
            scrollTop: self.element.offset().top - 200
          }, 1000);
        }

      });

      /* Create New Folder Event */
      self.element.find('.newfolder').unbind('click').click(function (e) {
        if (typeof self.searchQuery != 'undefined' && self.searchQuery !== '') {
          return false;
        }
        self._actionCreateEntry($(this));
      });

      /* CheckBox Events */
      self.element.find('.entry_checkbox').unbind('click').click(function (e) {
        e.stopPropagation();
        return true;
      });

      self.element.find('.entry_checkbox :checkbox').click(function (e) {
        if ($(this).prop('checked')) {
          $(this).closest('.entry').addClass('isselected');
        } else {
          $(this).closest('.entry').removeClass('isselected');
        }
      });


      self._initEditMenu();
      self._initLightbox();
      self.renderThumbnailsPopup();
      self._initMove();
      self._initLinkEvent();
      self._initScrollToTop();

      if (typeof grecaptcha !== 'undefined' && self.options.recaptcha !== '' && self.recaptcha_passed === false) {
        self._disableDownloadLinks();
      }

      self.options.loadingContainer.fadeOut(300);

      if (self.option.focus_id !== null) {
        var entry = self.element.find('.entry[data-id="' + self.option.focus_id + '"]');
        entry.addClass('hasfocus');

        if (entry.find('a.ilightbox-group').length > 0) {
          entry.find('a.ilightbox-group').trigger('click');
        }

        self.option.focus_id = null;
      }

      self.element.trigger('content-loaded', self);

    },

    renderContentForBrowser: function () {
      var self = this;

      switch (this.element.attr('data-layout')) {
        case 'list':
          self.element.removeClass('oftb-grid').addClass('oftb-list');
          $(".qtip[data-qtip-id='nav-" + self.options.listtoken + "']").find('.fa-th-large').closest('li').show();
          self.element.find('.fa-th-large').closest('li').show();
          $(".qtip[data-qtip-id='nav-" + self.options.listtoken + "']").find('.fa-th-list').closest('li').hide();
          self.element.find('.fa-th-list').closest('li').hide();
          break;

        case 'grid':
          self.element.removeClass('oftb-list').addClass('oftb-grid');
          $(".qtip[data-qtip-id='nav-" + self.options.listtoken + "']").find('.fa-th-large').closest('li').hide();
          self.element.find('.fa-th-large').closest('li').hide();
          $(".qtip[data-qtip-id='nav-" + self.options.listtoken + "']").find('.fa-th-list').closest('li').show();
          self.element.find('.fa-th-list').closest('li').show();

          /* Update files to fit in container */
          var $layoutgrid = self.element.find('.files.layout-grid');
          self.fitEntriesInContainer($layoutgrid.find('.folders-container '), 250);
          self.fitEntriesInContainer($layoutgrid.find('.files-container '), 250);

          $layoutgrid.fadeTo(0, 0).delay(100).fadeTo(200, 1);


          break;
      }
    },

    renderContentForGallery: function () {
      var self = this;

      var image_container = self.element.find('.image-container');
      var image_collage = self.element.find(".image-collage");

      image_container.hover(
              function () {
                $(this).find('.image-rollover').stop().animate({opacity: 1}, 400);
              },
              function () {
                $(this).find('.image-rollover').stop().animate({opacity: 0}, 400);
              }).find('.image-rollover').css("opacity", "0");

      image_collage.outerWidth(self.element.find('.ajax-filelist').width() - 1, true);

      var targetheight = self.element.attr('data-targetheight');
      image_collage.removeWhitespace().collagePlus({
        'targetHeight': targetheight,
        'fadeSpeed': "slow",
        'allowPartialLastRow': true
      });

      self.element.find(".image-container.hidden").fadeOut(0);
      image_collage.fadeTo(200, 1);

      image_container.each(function () {
        var folder_thumb = $(this).find(".folder-thumb");

        $(this).find(".image-folder-img").width($(this).width()).height($(this).height());

        if (folder_thumb.length > 0) {
          folder_thumb.width($(this).width()).height($(this).height());
          $(this).find(".image-folder-img").hide();
        }
      });

      self.renderImageFolders();
    },

    fitEntriesInContainer: function ($grid_container, targetwidth) {
      var self = this;

      var filelistwidth = $grid_container.innerWidth() - 1;
      var itemsonrow = Math.round(filelistwidth / targetwidth);
      var calculatedwidth = Math.floor(filelistwidth / itemsonrow);

      $grid_container.removeWhitespace();

      $grid_container.find('.entry_block').each(function () {
        var padding = parseInt($(this).css('padding-left')) + parseInt($(this).css('padding-right'));
        $(this).parent().outerWidth(calculatedwidth - padding, true);
      });
    },

    renderImageFolders: function () {
      var self = this;

      self.element.find('.image-folder').unbind('mousemove').mousemove(function (e) {

        var thumbnails = $(this).find('.folder-thumb');
        var relX = e.offsetX / e.currentTarget.offsetWidth;
        var show_n = Math.ceil(relX * thumbnails.length) - 1;

        thumbnails.filter(':gt(0)').stop(true).fadeOut().filter(':eq(' + show_n + ')').stop(true).fadeIn();
      });

      self.element.find('.image-folder').unbind('mouseleave').mouseleave(function () {
        $(this).find('.folder-thumb:gt(0)').stop(true).fadeOut();
      });

    },

    /* Load more images */
    _initLazyLoading: function () {
      var self = this;
      var last_visible_image = self.element.find(".image-container.entry:not(.hidden):last()");
      var load_per_time = self.element.attr('data-loadimages');

      last_visible_image.one('inview', function (event, isInView) {
        var images = self.element.find(".image-container:hidden:lt(" + load_per_time + ")");

        if (images.length === 0) {
          return;
        }

        images.fadeIn(500).removeClass('hidden').find('img').removeClass('hidden');
        self.unveilImages();

        self._initLazyLoading();
      });
    },

    _initScrollToTop: function () {
      var self = this;

      this.element.find('.scroll-to-top').unbind('click').click(function () {
        $('html, body').animate({
          scrollTop: self.element.offset().top
        }, 1500);
      });

      $(window).off('scroll', null, self._positionScrollToTop).on('scroll', null, {}, self._positionScrollToTop);
    },

    _initReCaptcha: function () {
      var self = this;

      if (typeof grecaptcha === 'undefined') {
        setTimeout(self._initReCaptcha.bind(self), 1000);
        return false;
      }

      grecaptcha.ready(function () {
        grecaptcha.execute(self.options.recaptcha, {action: 'wpcloudplugins'}).then(function (token) {

          $.ajax({type: "POST",
            url: self.options.ajax_url,
            data: {
              action: 'outofthebox-check-recaptcha',
              listtoken: self.options.listtoken,
              response: token,
              _ajax_nonce: self.options.recaptcha_nonce
            },
            success: function (response) {

              if (typeof response.verified === 'undefined' || response.verified !== true) {
                return false;
              }
              self.recaptcha_passed = true;
              self._enableDownloadLinks();
            },
            dataType: 'json'
          });
        })
      });
    },

    _disableDownloadLinks: function () {
      var self = this;

      var download_links = self.element.find('a.entry_action_download:not(.recaptcha), a.entry_action_export:not(.recaptcha), a.entry_link:not(.recaptcha), a.entry_action_view:not(.recaptcha), a.entry_action_external_view:not(.recaptcha)');

      if (download_links.length === 0) {
        return;
      }

      $.each(download_links, function () {
        var href = $(this).attr('href');
        var download = $(this).attr('download');
        $(this).attr('href', 'javascript:void(0)').attr('href-action', href);
        $(this).removeAttr('download').attr('download-action', download);
        $(this).addClass('recaptcha');
      });

    },

    _enableDownloadLinks: function () {
      var self = this;

      var download_links = $('a.entry_action_download.recaptcha, a.entry_action_export.recaptcha, a.entry_link.recaptcha, a.entry_action_view.recaptcha, a.entry_action_external_view.recaptcha');

      if (download_links.length === 0) {
        return;
      }

      $.each(download_links, function () {
        $(this).attr('href', $(this).attr('href-action')).attr('download', $(this).attr('download-action'));
        $(this).removeClass('recaptcha');
      });
    },

    _positionScrollToTop: function (event) {
      clearTimeout(window.scrollTimer);

      window.scrollTimer = setTimeout(function () {

        $('.ajax-filelist').each(function () {
          var $container = $(this);
          var $scroll_to_top_container = $container.next('.scroll-to-top');

          var heightContainer = $container.height();
          var positionContainer = $container.offset();
          var bottomContainer = positionContainer.top + heightContainer;
          var topWindow = $(window).scrollTop();
          var bottomWindow = topWindow + $(window).height();

          if (topWindow > positionContainer.top && heightContainer > $(window).height()) {
            $scroll_to_top_container.show().fadeIn(40);

            var positionbutton = heightContainer;
            if (bottomContainer > bottomWindow) {
              positionbutton = bottomWindow - positionContainer.top - 30;
            }
            $scroll_to_top_container.stop().animate({top: Math.round(positionbutton - 50)});
          } else {
            $scroll_to_top_container.fadeOut(400);
          }
        });
      }, 50);
    },

    /**
     * Initiate the UI Moveable / Draggable function
     * to allow the user to move files and folders
     * @returns {Boolean}
     */
    _initMove: function () {
      var self = this;
      if (this.element.find('.moveable').length === 0) {
        return false;
      }

      this.element.find('.moveable').draggable({
        stack: ".moveable",
        cursor: 'move',
        cursorAt: {top: 10, left: 10},
        containment: self.element,
        helper: "clone",
        distance: 10,
        delay: 50,
        start: function (event, ui) {
          $(this).addClass('isdragged');
        },
        stop: function (event, ui) {
          setTimeout(function () {
            $(this).removeClass('isdragged');
          }, 300);
        }
      });
      this.element.find('.folder, .image-folder').droppable({
        accept: self.element.find('.moveable'),
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        tolerance: "pointer",
        drop: function (event, ui) {
          //ui.draggable.fadeOut();
          self._actionMoveEntry(ui.draggable, $(this));
        }
      });
    },

    /* Button Events for linking folders */
    _initLinkEvent: function () {
      var self = this;

      self.element.find('.entry_linkto').unbind('click').click(function (e) {

        var folder_text = $(this).parent().attr('data-name');
        var folder_path = decodeURIComponent($(this).parent().attr('data-url'));
        var account_id = self.element.attr('data-account-id');
        var user_id = $('.outofthebox .thickbox_opener').find('[data-user-id]').attr('data-user-id');
        var $thickbox_opener = $('.thickbox_opener');

        if ($thickbox_opener.hasClass('private-folders-auto')) {
          $thickbox_opener.find('.private-folders-auto-current').val(folder_path);
          $thickbox_opener.find('.private-folders-auto-input-account').val(account_id);
          $thickbox_opener.find('.private-folders-auto-input-id').val(folder_path);
          $thickbox_opener.find('.private-folders-auto-input-name').val(folder_path);
          $thickbox_opener.find('.private-folders-auto-button').removeClass('disabled').find('.oftb-spinner').fadeOut()
          tb_remove();
          e.stopPropagation();
          return true;
        }

        if ($thickbox_opener.hasClass('woocommerce_upload_location')) {
          $('#woocommerce_outofthebox-woocommerce_upload_location_id').val(folder_path);
          $('#woocommerce_outofthebox-woocommerce_upload_location').val(folder_text);
          tb_remove();
          e.stopPropagation();
          return true;
        }

        $.ajax({type: "POST",
          url: self.options.ajax_url,
          data: {
            action: 'outofthebox-linkusertofolder',
            account_id: account_id,
            id: folder_path,
            text: folder_path,
            userid: user_id,
            _ajax_nonce: self.options.createlink_nonce
          },
          beforeSend: function () {
            tb_remove();
            $('.outofthebox .thickbox_opener').find('.oftb-spinner').show();
          },
          complete: function () {
            $('.oftb-spinner').hide();
          },
          success: function (response) {
            if (response === '1') {
              $('.outofthebox .thickbox_opener').parent().find('.column-private_folder').text(folder_path);
              $('.outofthebox .thickbox_opener .unlinkbutton').removeClass('hidden');
              $('.outofthebox .thickbox_opener .linkbutton').addClass('hidden');
              $('.outofthebox .thickbox_opener').removeClass("thickbox_opener");
            } else {
              location.reload(true);
            }
          },
          dataType: 'text'
        });

        e.stopPropagation();
        return true;
      });

      self.element.find('.entry_woocommerce_link').unbind('click').click(function (e) {

        var file_id = $(this).closest('.entry').attr('data-url');
        var file_name = $(this).closest('.entry').attr('data-name');
        var account_id = self.element.attr('data-account-id');

        tb_remove();
        window.wc_outofthebox.afterFileSelected(file_id, file_name, account_id);
        e.stopPropagation();
        return true;
      });
    },

    /* Bind event which shows the edit menu */
    _initEditMenu: function () {
      var self = this;
      self.element.find(' .entry .entry_edit_menu').each(function () {

        $(this).click(function (e) {
          e.stopPropagation();
        });

        $(this).qtip({
          content: {
            text: $(this).next('.oftb-dropdown-menu')
          },
          position: {
            my: 'top center',
            at: 'bottom center',
            target: $(this),
            scroll: false,
            viewport: self.element
          },
          show: {
            event: 'click',
            solo: true
          },
          hide: {
            event: 'mouseleave unfocus',
            delay: 200,
            fixed: true
          },
          events: {
            show: function (event, api) {
              api.elements.target.closest('.entry').addClass('hasfocus').addClass('popupopen');
            },
            hide: function (event, api) {
              api.elements.target.closest('.entry').removeClass('hasfocus').removeClass('popupopen');
            }
          },
          style: {
            classes: 'OutoftheBox ' + self.options.content_skin
          }
        });
      });

      /* Preview Event */
      self.element.find('.entry_action_view').unbind('click').click(function () {
        self._actionPreviewEntry($(this));
      });

      /* Download Event */
      self.element.find('.entry_action_download').unbind('click').click(function (e) {
        self._actionDownloadEntry($(this));
      });

      /* Social Share Event */
      self.element.find('.entry_action_shortlink').unbind('click').click(function (e) {
        self._actionShareEntry($(this));
      });

      /* Delete Event*/
      self.element.find('.entry_action_delete').unbind('click').click(function (e) {
        var datapath = $(this).closest("ul").attr('data-path');
        self.element.find(".entry[data-url='" + datapath + "'] .selected-files:checkbox").prop("checked", true);
        var entries = self.element.find("input[name='selected-files[]']:checked");
        self._actionDeleteEntries(entries);
      });

      /* Move Event*/
      self.element.find('.entry_action_move').unbind('click').click(function (e) {
        var datapath = $(this).closest("ul").attr('data-path');
        self.element.find(".entry[data-url='" + datapath + "'] .selected-files:checkbox").prop("checked", true);
        var entries = self.element.find("input[name='selected-files[]']:checked");
        self._actionMoveEntries(entries);
      });

      /* Rename Event */
      self.element.find('.entry_action_rename').unbind('click').click(function (e) {
        self._actionRenameEntry($(this));
      });

      /* DeepLink Event */
      self.element.find('.entry_action_deeplink').unbind('click').click(function (e) {
        self._actionCreateDeepLink($(this));
      });
    },

    /* Make the BreadCrumb responsive */
    renderBreadCrumb: function () {
      var self = this;
      var $breadcrumb_element = self.element.find('.breadcrumb');

      $breadcrumb_element.asBreadcrumbs('destroy');

      $breadcrumb_element.asBreadcrumbs({
        namespace: "oftb",
        toggleIconClass: "fas fa-caret-down",
        dropdownMenuClass: "oftb-dropdown-menu",

        dropdownItem: function dropdownItem(classes, label, href) {
          if (!href) {
            return ('<li class="' + classes.dropdownItemClass + ' ' + classes.dropdownItemDisableClass + '"><a href="#"><i class="fas fa-folder fa-lg"></i>  ' + label + '</a></li>');
          }
          return ('<li class="' + classes.dropdownItemClass + '"><a href="' + href + '"><i class="fas fa-folder fa-lg"></i>  ' + label + '</a></li>');
        }
      });

      $breadcrumb_element.find('.oftb-dropdown-menu li').click(function () {
        $breadcrumb_element.find('li a.folder[href="' + $(this).find('a').attr("href") + '"]').trigger('click');
        $breadcrumb_element.find('.oftb-dropdown').removeClass('open');
      });

      $breadcrumb_element.find('.oftb-toggle').click(function () {
        $breadcrumb_element.find('.oftb-dropdown').addClass('open');
      });

      $(document).mouseup(function (e) {
        var container = $breadcrumb_element.find('.oftb-dropdown-menu');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
          container.parent().removeClass('open');
        }
      });
    },

    /* Bind event which shows popup with thumbnail on hover in file list */
    renderThumbnailsPopup: function () {
      var self = this;
      self.element.find('.entry[data-tooltip] .entry_name, .entry[data-tooltip] .entry_lastedit').each(function () {
        $(this).qtip({
          suppress: true,
          content: {
            text: function (event, api) {
              var descriptionbox = $(this).parent().find('.description_textbox').clone();
              descriptionbox.find("img.preloading").removeClass('hidden').unveil(200, null, function () {
                $(this).load(function () {
                  $(this).removeClass('preloading').removeAttr('data-src');
                  $(this).prev('.preloading').remove();
                });
              }).trigger('unveil');

              return descriptionbox;
            }
          },
          position: {
            target: 'mouse',
            adjust: {x: 5, y: 5, scroll: false},
            viewport: self.element
          },
          show: {
            delay: 500,
            solo: true
          },
          hide: {
            event: 'click mouseleave unfocus'
          },
          style: {
            classes: 'OutoftheBox description ' + self.options.content_skin
          }
        });
      });
    },

    /* Unveil Images */
    unveilImages: function () {
      var self = this;

      self.element.find('img.preloading').one('error', function () {
        if (typeof $(this).attr('data-src-backup') !== typeof undefined && $(this).attr('data-src-backup') !== false) {
          this.src = $(this).attr('data-src-backup');
        } else {
          this.src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
        }

        $(this).removeAttr('data-src');
        $(this).prev('.preloading').remove();
      });

      self.element.find('img.preloading').not('.hidden').unveil(200, null, function () {
        $(this).load(function () {
          $(this).removeClass('preloading').removeAttr('data-src');
          $(this).prev('.preloading').remove();
        });
      });

      setTimeout(function () {
        //self.renderContentForGallery()
      }, 200);

    },

    /**
     * Renders the General Menu in the Navigation Bar
     */
    renderNavMenu: function () {
      var self = this;

      /* Fire up the search functionality*/
      this._initSearchBox();
      this._initGearMenu();
      this._initAccountSelector();

      /* Refresh button does a hard refresh for the current folder*/
      this.element.find('.nav-refresh').unbind('click');
      this.element.find('.nav-refresh').click(function () {
        self.clearSearchBox();
        self.options.forceRefresh = true;
        self._getFileList({});
      });

      /* Event for nav-home button */
      this.element.find('.nav-home').unbind('click');
      this.element.find('.nav-home').click(function () {
        self.clearSearchBox();
        self.element.attr('data-path', self.element.attr('data-org-path'));
        self._getFileList({'OutoftheBoxpath': self.element.attr('data-org-path')});
      });

      /* Sortable column Names */
      self.element.find('.sortable').click(function () {

        var newclass = 'asc';
        if ($(this).hasClass('asc')) {
          newclass = 'desc';
        }

        self.element.find('.sortable').removeClass('asc').removeClass('desc');
        $(this).addClass(newclass);
        var sortstr = $(this).attr('data-sortname') + ':' + newclass;
        self.element.attr('data-sort', sortstr);

        self._getFileList({});
      });
    },

    /**
     * Open the Lightbox to preview an entry
     * @param {Object} entry_for_focus
     * @param {Array} rows // array of objects for gallery mode
     * @returns {undefined}
     */
    _initLightbox: function () {
      var self = this;
      var lightboxnav = (self.element.attr('data-lightboxnav') === '1');

      var options = {

        skin: this.options.lightbox_skin,
        path: this.options.lightbox_path,
        maxScale: 1,
        minScale: 0.05,

        slideshow: {
          pauseOnHover: true,
          pauseTime: self.element.attr('data-pausetime'),
          startPaused: ((self.element.attr('data-list') === 'gallery') && (self.element.attr('data-slideshow') === '1')) ? false : true
        },
        controls: {
          slideshow: (self.element.attr('data-list') === 'gallery' && lightboxnav) ? true : false,
          arrows: (lightboxnav) ? true : false,
          thumbnail: (self.options.mobile ? false : true)
        },
        caption: {
          start: (self.options.lightbox_showcaption === 'mouseenter') ? true : false,
          show: self.options.lightbox_showcaption,
          hide: (self.options.lightbox_showcaption === 'mouseenter') ? 'mouseleave' : self.options.lightbox_showcaption,
        },
        keepAspectRatio: true,
        callback: {
          onBeforeLoad: function (api, position) {
            $('.ilightbox-holder').addClass('OutoftheBox');
            $('.ilightbox-holder .oftb-hidepopout').remove();


            if (self.element.attr('data-popout') === '0') {
              $('.ilightbox-holder').find('.oftb-embedded').after('<div class="oftb-hidepopout">&nbsp;</div>');
            }

            var element = $('.ilightbox-holder').find('iframe').addClass('oftb-embedded');
            self._helperIframeFix(element);
          },
          onBeforeChange: function () {
            /* Stop all HTML 5 players */
            var players = $('.ilightbox-holder video, .ilightbox-holder audio');
            $.each(players, function (i, element) {
              if (element.paused === false) {
                element.pause();
              }
            });
          },
          onAfterChange: function (api) {
            /* Auto Play new players*/
            var players = api.currentElement.find('video, audio');
            $.each(players, function (i, element) {
              if (element.paused) {
                element.play();
              }
            });
          },
          onRender: function (api, position) {
            /* Auto-size HTML 5 player */
            var $video_html5_players = $('.ilightbox-holder').find('video, audio');
            $.each($video_html5_players, function (i, video_html5_player) {

              var $video_html5_player = $(this);

              video_html5_player.addEventListener('playing', function () {
                var container_width = api.currentElement.find('.ilightbox-container').width() - 1;
                var container_height = api.currentElement.find('.ilightbox-container').height() - 1;

                $video_html5_player.width(container_width);

                $video_html5_player.parent().width(container_width)

                if ($video_html5_player.height() > api.currentElement.find('.ilightbox-container').height() - 2) {
                  $video_html5_player.height(container_height);
                }
              }, false);
              $video_html5_player.find('source').attr('src', $video_html5_player.find('source').attr('data-src'));
            });

          },
          onShow: function (api) {
            if (api.currentElement.find('.empty_iframe').length === 0) {
              api.currentElement.find('.oftb-embedded').after(self.options.str_iframe_loggedin);
            }

            /* Bugfix for PDF files that open very narrow */
            if (api.currentElement.find('iframe').length > 0) {
              setTimeout(function () {
                api.currentElement.find('.oftb-embedded').width(api.currentElement.find('.ilightbox-container').width() - 1);
              }, 500);
              api.currentElement.find('iframe').on('load', function () {
                api.currentElement.find('.empty_iframe').remove();
              });
            }

            api.currentElement.find('.empty_iframe').hide();
            if (api.currentElement.find('img').length === 0) {
              setTimeout(function () {
                api.currentElement.find('.empty_iframe').fadeIn();
                api.currentElement.find('.empty_iframe_link').attr('href', api.currentElement.find('iframe').attr('src'))
              }, 5000);
            }

            /* Auto Play new players*/
            var players = api.currentElement.find('video, audio');
            $.each(players, function (i, element) {
              if (element.paused) {
                element.play();
              }
            });

            /* Lazy Load thumbnails */
            var iL = this;

            $(".ilightbox-thumbnail img.preloading").unveil(null, null, function () {
              $(this).load(function () {
                $(this).removeClass('preloading').removeAttr('data-src');
                $(this).prev('.preloading').remove();
                $(this).parent().data({
                  naturalWidth: this.width,
                  naturalHeight: this.height
                });

                iL.positionThumbnails(null, null, null);

                $(".ilightbox-thumbnail img.preloading").unveil();

              });
            });

            $('.ilightbox-container .oftb-hidepopout').on("contextmenu", function (e) {
              return false;
            });

            $('.ilightbox-container .ilightbox-wrapper, .ilightbox-container img, .ilightbox-container video, .ilightbox-container audio').on("contextmenu", function (e) {
              return (self.options.lightbox_rightclick === 'Yes');
            });

            if (self.options.mobile) {
              $('.ilightbox-container img').panzoom({disablePan: true, minScale: 1, contain: 'invert'});
              $('.ilightbox-container img').on('panzoomzoom', function (e, panzoom, scale) {
                if (scale == 1) {
                  panzoom.options.disablePan = true;
                } else {
                  panzoom.options.disablePan = false;
                }
              });
            }

            /* Log preview event if needed */
            var $img = api.currentElement.find('img');
            if ($img.length > 0 && $img.data('logged') !== 1 && ($img.attr('src').indexOf('action=outofthebox-') === -1)) {
              var entry_id = $('a[href="' + $img.attr('src') + '"]').closest('[data-id]').data('id');
              $img.data('logged', 1);
              self._logEvent('log_preview_event', entry_id);
            }
          }
        },
        errors: {
          loadImage: self.options.str_imgError_title,
          loadContents: self.options.str_xhrError_title
        },
        text: {
          next: self.options.str_next_title,
          previous: self.options.str_previous_title,
          slideShow: self.options.str_startslideshow
        }
      };

      if (lightboxnav) {

        if (!$.isEmptyObject(this.lightBox)) {
          self.lightBox.destroy();
        }

        var elements = self.element.find('.ilightbox-group');
        self.lightBox = elements.iLightBox(options);

      } else {

        if (!$.isEmptyObject(this.lightBox)) {
          $.each(this.lightBox, function () {
            this.destroy();
          });
        }

        self.lightBox = [];

        self.element.find('.ilightbox-group').each(function () {
          self.lightBox.push($(this).iLightBox(options));
        });
      }

    },

    /**
     * Create a direct URL to the entry
     * @param {Object} entry
     * @returns {String}
     */
    _actionCreateDeepLink: function (entry) {

      var self = this;

      $('.qtip').qtip('hide');

      var datapath = entry.closest("ul").attr('data-path');
      var is_folder, entry_id;

      if (entry.hasClass('entry_action_deeplink_folder')) {
        var datapath = self.element.attr('data-path');
        is_folder = true;
      } else {
        var entry_element = self.element.find(".entry[data-url='" + datapath + "']");
        var entry_name = entry_element.attr('data-name');
        entry_id = entry_element.attr('data-id');
        is_folder = entry_element.hasClass('folder') || entry_element.hasClass('image-folder');
      }

      /* Generate Direct link */
      var hash_params = {
        'account_id': self.element.attr('data-account-id'),
        'last_path': (is_folder) ? datapath : self.element.attr('data-path'),
        'org_path': self.element.attr('data-org-path'),
        'focus_id': entry_id
      };

      var hash = window.btoa(encodeURIComponent(JSON.stringify(hash_params)));

      var url = new URL(window.location);
      var search_params = new URLSearchParams(url.search);
      search_params.set('wpcp_link', hash);
      url.search = search_params.toString();

      /* Modal */
      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Delete Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_close_title + '" >' + self.options.str_close_title + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" ><input type="text" class="direct-link-url" value="' + url.toString() + '" style="width: 98%;" readonly/><div class="outofthebox-shared-social"></div></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      $.extend(jsSocials.shares, {
        directlink: {
          label: "Copy",
          logo: "fas fa-clipboard",
          shareIn: "self",
          shareUrl: function () {
            return '#';
          },
          countUrl: ""
        }
      });

      $(".outofthebox-shared-social").jsSocials({
        url: url.toString(),
        text: entry_name + ' | ',
        showLabel: false,
        showCount: "inside",
        shareIn: "popup",
        shares: ["directlink", "email", "twitter", "facebook", "googleplus", "linkedin", "pinterest", "whatsapp"]
      });

      var clipboard = new ClipboardJS('.jssocials-share-directlink', {
        text: function (trigger) {
          return $('.direct-link-url').val();
        }
      });

      clipboard.on('success', function (e) {
        $('.jssocials-share-directlink i').removeClass('fa-copy').addClass('fa-clipboard-check');
        e.clearSelection();
      });

      clipboard.on('error', function (e) {
        if (self.options.mobile) {
          $('.direct-link-url').select();
        } else {
          window.prompt('Copy to clipboard: Ctrl+C, Enter', $('.direct-link-url').val());
        }
      });

      /* Open the Dialog and load the images inside it */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        dialogOpenClass: 'animated slideInDown',
        dialogCloseClass: 'animated slideOutUp',
        escapeClose: true
      });
      document.addEventListener('keydown', function (ev) {
        modal_action.keydown(ev);
      }, false);
      modal_action.open();
      window.modal_action = modal_action;

      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');

      return false;
    },

    _logEvent: function (type, id) {
      var self = this;

      if (self.options.log_events === "0") {
        return false;
      }

      $.ajax({type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-event-stats',
          account_id: self.options.account_id,
          type: type,
          id: id,
          _ajax_nonce: self.options.log_nonce
        }
      });
    },

    _actionPreviewEntry: function (entry) {
      var self = this;
      $('.qtip').qtip('hide');
      var datapath = entry.closest("ul").attr('data-path');
      var link = self.element.find(".entry[data-url='" + datapath + "']").find(".entry_link")[0].click();
    },
    /**
     * Download an entry
     * @param {Object} entry
     * @param {string} mimetype
     */
    _actionDownloadEntry: function (entry) {
      var self = this;

      var dataname = entry.attr('data-filename');

      sendGooglePageView('Download', dataname);

      var dataid = entry.closest("ul").attr('data-path');
      if (typeof dataid === 'undefined') {
        dataid = entry.closest(".entry").attr('data-url');
      }

      var $processor_icon = $('<div><i class="fas fa-cog fa-spin fa-1x fa-fw"></i></div>').css({'margin-right': '5px', 'display': 'inline-grid'}).delay(5000).fadeOut('slow', function () {
        $(this).remove();
      });
      self.element.find(".layout-grid .entry[data-url='" + dataid + "'] .entry-name-view").prepend($processor_icon);
      self.element.find(".layout-list .entry[data-url='" + dataid + "'] .entry_name").prepend($processor_icon);

      // Delay a few milliseconds for Tracking event
      setTimeout(function () {
        $('.qtip').qtip('hide');
        return true;
      }, 300);

    },

    _actionShareEntry: function (entry) {
      var self = this;

      $('.qtip').qtip('hide');

      var account_id = self.element.attr('data-account-id');
      var datapath = entry.closest("ul").attr('data-path');
      var dataurl = self.element.find(".entry[data-url='" + datapath + "']").attr('data-url');
      var dataname = self.element.find(".entry[data-url='" + datapath + "']").attr('data-name');

      if (entry.hasClass('entry_action_shortlink_folder')) {
        var dataurl = self.element.attr('data-path');
      }

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Delete Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_close_title + '" >' + self.options.str_close_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="confirm" type="button" title="' + self.options.str_create_shared_link + '" >' + self.options.str_create_shared_link + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" ></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      $.ajax({type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-create-link',
          account_id: account_id,
          listtoken: self.options.listtoken,
          OutoftheBoxpath: dataurl,
          _ajax_nonce: self.options.createlink_nonce
        },
        complete: function () {
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').remove();
        },
        success: function (response) {
          if (response !== null) {
            if (response.link !== null) {
              $('.outofthebox-modal-body').append('<input type="text" class="shared-link-url" value="' + response.link + '" style="width: 98%;" readonly/><div class="outofthebox-shared-social"></div>');
              sendGooglePageView('Create shared link');

              $.extend(jsSocials.shares, {
                directlink: {
                  label: "Copy",
                  logo: "fas fa-clipboard",
                  shareIn: "self",
                  shareUrl: function () {
                    return '#';
                  },
                  countUrl: ""
                }
              });

              $(".outofthebox-shared-social").jsSocials({
                url: response.link,
                text: dataname + ' | ',
                showLabel: false,
                showCount: "inside",
                shareIn: "popup",
                shares: ["directlink", "email", "twitter", "facebook", "googleplus", "linkedin", "pinterest", "whatsapp"]
              });


              var clipboard = new ClipboardJS('.jssocials-share-directlink', {
                text: function (trigger) {
                  return $('.shared-link-url').val();
                }
              });

              clipboard.on('success', function (e) {
                $('.jssocials-share-directlink i').removeClass('fa-copy').addClass('fa-clipboard-check');
                e.clearSelection();
              });

              clipboard.on('error', function (e) {
                if (self.options.mobile) {
                  $('.shared-link-url').select();
                } else {
                  window.prompt('Copy to clipboard: Ctrl+C, Enter', $('.shared-link-url').val());
                }
              });

            } else {
              $('.outofthebox-modal-body').find('.shared-link-url').val(response.error);
            }
          }
        },
        dataType: 'json'
      });


      /* Open the Dialog and load the images inside it */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        dialogOpenClass: 'animated slideInDown',
        dialogCloseClass: 'animated slideOutUp',
        escapeClose: true
      });
      document.addEventListener('keydown', function (ev) {
        modal_action.keydown(ev);
      }, false);
      modal_action.open();
      window.modal_action = modal_action;

      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');

      return false;
    },
    /**
     * Open a Dialog for creating a new Entry with a certain mimetype
     * @param {String} template_name
     * @param {String} mimetype
     */
    _actionCreateEntry: function (entry) {
      var self = this;

      $('.qtip').qtip('hide');

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();
      /* Build the Rename Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_cancel_title + '" >' + self.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="rename" type="button" title="' + self.options.str_addfolder_title + '" >' + self.options.str_addfolder_title + '</button>';
      var addfolder_input = '<input type="text" id="outofthebox-modal-addfolder-input" name="outofthebox-modal-addfolder-input" value="" style="width:100%"/>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" >' + self.options.str_addfolder + ' <br/>' + addfolder_input + '</div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');

      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);
      /* Set the button actions */

      $('#outofthebox-modal-action #outofthebox-modal-addfolder-input').unbind('keyup');
      $('#outofthebox-modal-action #outofthebox-modal-addfolder-input').on("keyup", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').trigger('click');
        }
      });
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').unbind('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {

        var newinput = $('#outofthebox-modal-addfolder-input').val();
        /* Check if there are illegal characters in the new name*/
        if (/[<>:"/\\|?*]/g.test($('#outofthebox-modal-addfolder-input').val())) {
          $('#outofthebox-modal-action .outofthebox-modal-error').remove();
          $('#outofthebox-modal-addfolder-input').after('<div class="outofthebox-modal-error">' + self.options.str_rename_failed + '</div>');
          $('#outofthebox-modal-action .outofthebox-modal-error').fadeIn();
        } else {

          var data = {
            action: 'outofthebox-add-folder',
            newfolder: encodeURIComponent(newinput),
            _ajax_nonce: self.options.addfolder_nonce
          };
          self._actionDoModifyEntry(data);

          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');
        }

      });
      /* Open the dialog */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        dialogOpenClass: 'animated slideInDown',
        dialogCloseClass: 'animated slideOutUp',
        escapeClose: true
      });
      document.addEventListener('keydown', function (ev) {
        modal_action.keydown(ev);
      }, false);
      modal_action.open();
      window.modal_action = modal_action;
      return false;
    },

    /**
     * Create a Dialog for renaming an entry
     * @param {Object} entry
     */
    _actionRenameEntry: function (entry) {

      var self = this;
      $('.qtip').qtip('hide');

      var datapath = entry.closest("ul").attr('data-path');
      var dataname = self.element.find(".entry[data-url='" + datapath + "']").attr('data-name');
      var dataurl = self.element.find(".entry[data-url='" + datapath + "']").attr('data-url');

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Rename Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_cancel_title + '" >' + self.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="rename" type="button" title="' + self.options.str_rename_title + '" >' + self.options.str_rename_title + '</button>';
      var renameinput = '<input id="outofthebox-modal-rename-input" name="outofthebox-modal-rename-input" type="text" value="' + dataname + '" style="width:100%"/>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" >' + self.options.str_rename + '<br/>' + renameinput + '</div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');

      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);
      /* Set the button actions */

      $('#outofthebox-modal-action #outofthebox-modal-rename-input').unbind('keyup');
      $('#outofthebox-modal-action #outofthebox-modal-rename-input').on("keyup", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').trigger('click');
        }
      });
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').unbind('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {

        var new_filename = $('#outofthebox-modal-rename-input').val();
        /* Check if there are illegal characters in the new name*/
        if (/[<>:"/\\|?*]/g.test($('#outofthebox-modal-rename-input').val())) {
          $('#outofthebox-modal-action .outofthebox-modal-error').remove();
          $('#outofthebox-modal-rename-input').after('<div class="outofthebox-modal-error">' + self.options.str_rename_failed + '</div>');
          $('#outofthebox-modal-action .outofthebox-modal-error').fadeIn();
        } else {

          var data = {
            action: 'outofthebox-rename-entry',
            OutoftheBoxpath: dataurl,
            newname: encodeURIComponent(new_filename),
            _ajax_nonce: self.options.rename_nonce
          };
          self._actionDoModifyEntry(data);

          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');
        }

      });
      /* Open the dialog */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        dialogOpenClass: 'animated slideInDown',
        dialogCloseClass: 'animated slideOutUp',
        escapeClose: true
      });
      document.addEventListener('keydown', function (ev) {
        modal_action.keydown(ev);
      }, false);
      modal_action.open();
      window.modal_action = modal_action;
      return false;
    },
    /**
     * Create a request to move the selected enties
     * @param {UI element} entry
     * @param {UI element} to_folder
     */
    _actionMoveEntry: function (entry, to_folder) {

      var data = {
        action: 'outofthebox-move-entries',
        entries: [entry.attr('data-url')],
        copy: false,
        target: to_folder.attr('data-url'),
        listtoken: this.options.listtoken,
        _ajax_nonce: this.options.move_nonce
      };

      this._actionDoModifyEntry(data);

    },

    /**
     * Open a Dialog to move selected entries
     * @param {Object} entries
     */
    _actionMoveEntries: function (entries) {

      /* Close any open modal windows */
      $('.qtip').qtip('hide');
      $('#outofthebox-modal-action').remove();

      /* Build the data request variable and make a list of the selected entries */
      var self = this, list_of_files = '', files = [];
      $.each(entries, function () {
        files.push($(this).val());
      });

      /* Build the Move Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_cancel_title + '" >' + self.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="confirm" type="button" title="' + self.options.str_move_title + '" >' + self.options.str_move_title + ' (' + files.length + ')</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" ><div id="outofthebox-modal-folder-selector"></div></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      /* Copy current Out-of-the-Box element into modal, and turn it into a File Browser */
      self.options.topContainer.clone().appendTo("#outofthebox-modal-folder-selector").removeClass('gallery').addClass('files');
      $("#outofthebox-modal-folder-selector").find(".ajax-filelist").html('');
      $("#outofthebox-modal-folder-selector .OutoftheBox").attr('data-list', 'files').attr('data-layout', 'list').OutoftheBox(OutoftheBox_vars);

      /* Set the button actions */
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').unbind('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {

        var data = {
          action: 'outofthebox-move-entries',
          entries: files,
          copy: false,
          target: $("#outofthebox-modal-folder-selector .OutoftheBox").attr('data-path'),
          _ajax_nonce: self.options.move_nonce
        };

        self._actionDoModifyEntry(data);

        $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
        $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');
      });

      /* Open the Dialog and load the images inside it */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        dialogOpenClass: 'animated slideInDown',
        dialogCloseClass: 'animated slideOutUp',
        escapeClose: true
      });
      document.addEventListener('keydown', function (ev) {
        modal_action.keydown(ev);
      }, false);
      modal_action.open();
      window.modal_action = modal_action;

      return false;
    },

    /**
     * Open a Dialog to delete selected entries
     * @param {Object} entries
     */
    _actionDeleteEntries: function (entries) {

      /* Close any open modal windows */
      $('.qtip').qtip('hide');
      $('#outofthebox-modal-action').remove();

      /* Build the data request variable and make a list of the selected entries */
      var self = this, list_of_files = '', files = [];
      $.each(entries, function () {
        var $entry = $(this).closest('.entry');
        var $img = $entry.find('img:first()');

        var icon_tag = $('<div class="outofthebox-modal-file-icon">');
        if ($img.length > 0) {
          $img.clone().appendTo(icon_tag);
        }
        list_of_files += '<li>' + icon_tag.html() + '<span>' + $entry.attr('data-name') + '</span></li>';
        files.push($(this).val());
      });

      /* Build the Delete Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_cancel_title + '" >' + self.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="confirm" type="button" title="' + self.options.str_delete_title + '" >' + self.options.str_delete_title + ' (' + files.length + ') </button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" >' + self.options.str_delete + '</br></br><ul class="files">' + list_of_files + '</ul></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      /* Set the button actions */
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').unbind('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {

        var data = {
          action: 'outofthebox-delete-entries',
          entries: files,
          _ajax_nonce: self.options.delete_nonce
        };
        self._actionDoModifyEntry(data);

        $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
        $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');
      });

      /* Open the Dialog and load the images inside it */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        dialogOpenClass: 'animated slideInDown',
        dialogCloseClass: 'animated slideOutUp',
        escapeClose: true
      });
      document.addEventListener('keydown', function (ev) {
        modal_action.keydown(ev);
      }, false);
      modal_action.open();
      window.modal_action = modal_action;

      return false;
    },

    /**
     * Initiate the Account Selector functionality
     */
    _actionSelectAccount: function () {
      /* Close any open modal windows */
      $('.qtip').qtip('hide');
      $('#outofthebox-modal-action').remove();

      /* Build the data request variable and make a list of the selected entries */
      var self = this;

      /* Build the Account Selector Dialog */
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body modal-account-selector" tabindex="0" style="text-align: center;"><h1>' + self.options.str_account_title + '</h1><div class="nav-account-selector-content">' + self.element.find('.nav-account-selector-content').html() + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody);

      /* Set the button actions */
      $('#outofthebox-modal-action .nav-account-selector').unbind('click');
      $('#outofthebox-modal-action .nav-account-selector').click(function () {
        self.element.find('.nav-account-selector:first').html($(this).html());

        self.element.attr('data-id', '');
        self.element.attr('data-path', '');
        self.element.attr('data-account-id', $(this).attr('data-account-id'));
        self.options.account_id = $(this).attr('data-account-id');
        self._getFileList({});

        modal_action.close();
      });

      /* Open the Dialog and load the images inside it */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        dialogOpenClass: 'animated slideInDown',
        dialogCloseClass: 'animated slideOutUp',
        escapeClose: true
      });
      document.addEventListener('keydown', function (ev) {
        modal_action.keydown(ev);
      }, false);
      modal_action.open();
      window.modal_action = modal_action;

      return false;
    },

    _actionDoModifyEntry: function (request) {
      var self = this;
      var lastpath = self.element.attr('data-path');

      request.listtoken = this.options.listtoken;
      request.lastpath = lastpath;
      request.account_id = self.element.attr('data-account-id');

      $.ajax({
        type: "POST",
        url: self.options.ajax_url,
        data: request,
        beforeSend: function () {
          self.options.loadingContainer.fadeIn(400);
        },
        success: function (json) {

          if (typeof json !== 'undefined') {
            if (typeof json.lastpath !== 'undefined' && (json.lastpath !== null)) {
              self.element.attr('data-path', json.lastpath);
            }
          }

        },
        complete: function () {

          if (typeof modal_action !== 'undefined') {
            modal_action.close();
          }

          self.options.forceRefresh = true;
          self._getFileList({});
        },
        dataType: 'json'
      });
    },

    /* ***** Helper functions for File Upload ***** */
    /* Validate File for Upload */
    _uploadValidateFile: function (file, position) {
      var self = this;

      var minFileSize = self.element.find('input[name="minfilesize"]').val();
      var maxFileSize = self.element.find('input[name="maxfilesize"]').val();
      var acceptFileType = new RegExp(self.element.find('input[name="acceptfiletypes"]').val(), "i");

      file.error = false;
      if (file.name.length && !acceptFileType.test(file.name)) {
        file.error = self.options.acceptFileTypes;
      }

      if (minFileSize !== '' && file.size <= minFileSize) {
        file.error = self.options.minFileSize;
      }

      if (maxFileSize !== '' && file.size > 0 && file.size > maxFileSize) {
        file.error = self.options.maxFileSize;
      }

      if (self.number_of_uploaded_files.Max > 0 && (self.number_of_uploaded_files.Counter > self.number_of_uploaded_files.Max)) {
        var max_reached = true;
        /* Allow upload of the same file */
        $.each(self.uploaded_files_storage, function () {
          if (this.name === file.name) {
            max_reached = false;
            self.number_of_uploaded_files.Counter--; // Don't count this as an extra file
          }
        });

        if (max_reached) {
          file.error = self.options.maxNumberOfFiles;
        }
      }

      return file;
    },

    /* Get Progress for uploading files to cloud*/
    _uploadGetProgress: function (file) {
      var self = this;

      $.ajax({type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-upload-file',
          account_id: self.element.attr('data-account-id'),
          type: 'get-status',
          listtoken: self.options.listtoken,
          hash: file.hash,
          _ajax_nonce: self.options.upload_nonce
        },
        dataType: 'json',
        success: function (response) {
          if (response !== null) {
            if (typeof response.status !== 'undefined') {
              if (response.status.progress === 'starting' || response.status.progress === 'uploading') {
                setTimeout(function () {
                  self._uploadGetProgress(response.file);
                }, 1500);
              }
              self._uploadRenderRowOnProgress(response.file, {percentage: 50 + (response.status.percentage / 2), progress: response.status.progress});
            } else {
              file.error = self.options.str_error;
              self._uploadRenderRowOnFinish(file);
            }
          }
        },
        error: function (response) {
          file.error = self.options.str_error;
          self._uploadRenderRowOnFinish(file);
        }
      });

    },

    /* Render file in upload list */
    _uploadRenderRow: function (file) {
      var self = this;

      var row = self.element.find('.template-row').clone().removeClass('template-row');
      var cancel_button = $('<a class="cancel-upload"><i class="fas fa-ban" aria-hidden="true"></i> ' + self.options.str_delete_title + '</a>');

      row.attr('data-file', file.name).attr('data-id', file.hash);
      row.find('.file-name').text(file.path.replace(file.name, '') + file.name);
      if (file.size !== 'undefined' && file.size > 0) {
        row.find('.file-size').text(self._helperFormatBytes(file.size, 1));
      }
      row.find('.upload-thumbnail img').attr('src', self._uploadGetThumbnail(file));

      row.addClass('template-upload');
      row.find('.upload-status').removeClass().addClass('upload-status queue').append(cancel_button);
      row.find('.upload-status-icon').removeClass().addClass('upload-status-icon fas fa-circle').hide();

      self.element.find('.fileupload-list .files').append(row);
      self.element.find('div.fileupload-drag-drop').fadeOut();

      if (typeof file.error !== 'undefined' && file.error !== false) {
        self._uploadRenderRowOnFinish(file, 'invalid_file');
      }

      return row;

    },
    _uploadRenderRowOnStart: function (file) {
      var self = this;

      var row = self.element.find(".fileupload-list [data-id='" + file.hash + "']");

      row.find('.upload-status').removeClass().addClass('upload-status succes').text(self.options.str_uploading_local);
      row.find('.upload-status-icon').removeClass().addClass('upload-status-icon fas fa-circle-notch fa-spin').fadeIn();
      row.find('.upload-progress').slideDown();
    },

    /* Render the progress of uploading cloud files */
    _uploadRenderRowOnProgress: function (file, status) {
      var self = this;

      var row = self.element.find(".fileupload-list [data-id='" + file.hash + "']");
      var progress_bar = row.find('.ui-progressbar');
      var progress_bar_value = progress_bar.find('.ui-progressbar-value');

      progress_bar_value.fadeIn().animate({
        width: (status.percentage / 100) * progress_bar.width()
      }, 50);

      if (status.progress === 'uploading_to_cloud') {
        row.find('.upload-status').text(self.options.str_uploading_cloud);
      }
    },

    _uploadRenderRowOnFinish: function (file, haserror) {
      var self = this;

      var row = self.element.find(".fileupload-list [data-id='" + file.hash + "']");

      row.addClass('template-download').removeClass('template-upload');
      row.find('.file-name').text(file.path.replace(file.name, '') + file.name);
      row.find('.upload-thumbnail img').attr('src', self._uploadGetThumbnail(file));
      row.find('.upload-progress').slideUp();

      if (typeof file.error !== 'undefined' && file.error !== false) {
        row.find('.upload-error').html('<i class="fas fa-exclamation-circle"></i> <strong>' + self.options.str_error + ":</strong> " + file.error).slideUp().delay(500).slideDown();
      } else {
        row.find('.upload-status').removeClass().addClass('upload-status succes').text(self.options.str_success);
        row.find('.upload-status-icon').removeClass().addClass('upload-status-icon fas fa-check-circle');

        self.uploaded_files.push(file.fileid);
      }

      if (typeof haserror === 'undefined' && self.element.find('.template-upload').length < 1) {
        clearTimeout(self.uploadPostProcessTimer);
        self.uploadPostProcessTimer = setTimeout(function () {
          self._uploadDoPostProcess();
        }, 500);
      }

      if (row.closest('.gform_wrapper').length > 0 || row.closest('.wpcf7').length > 0 || (self.element.hasClass('upload') === true)) {
        /* Keep the upload listed in Forms */
      } else {
        self._uploadDeleteRow(file, 5000);
      }
    },

    _uploadDeleteRow: function (file, delayms) {
      var self = this;

      var row = self.element.find(".fileupload-list [data-id='" + file.hash + "']");

      row.delay(delayms).animate({"opacity": "0"}, "slow", function () {
        $(this).remove();

        if (self.element.find('.template-upload').length < 1) {
          self.element.find('div.fileupload-drag-drop').fadeIn();
        }
      });

      if (typeof file.error !== 'undefined' && file.error !== false) {
        self.number_of_uploaded_files.Counter--;
      }
    },

    _uploadDoRequest: function (data) {
      var self = this;

      if ($.active === 0) {
        data.submit();
      } else {
        window.setTimeout(function () {
          self._uploadDoRequest(data)
        }, 200);
      }
    },

    /* Upload Notification function to send notifications if needed after upload */
    _uploadDoPostProcess: function () {
      var self = this;

      $.ajax({type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-upload-file',
          account_id: self.element.attr('data-account-id'),
          type: 'upload-postprocess',
          listtoken: self.options.listtoken,
          files: self.uploaded_files,
          lastpath: self.element.attr('data-path'),
          _ajax_nonce: self.options.upload_nonce
        },
        success: function (response) {
          if (response !== null) {
            self.uploaded_files = [];

            $.each(response.files, function (fileid, file) {
              self.uploaded_files_storage[fileid] = {
                "hash": fileid,
                "name": file.name,
                "type": file.type,
                "path": file.completepath,
                "size": file.filesize,
                "link": file.link,
                "account_id": file.account_id,
                "folderurl": file.folderurl,
              };
            });

            self.element.find('.fileupload-filelist').val(JSON.stringify(self.uploaded_files_storage)).trigger('change');
          }
        },
        complete: function (response) {

          if (self.element.hasClass('upload') === false) {
            self.options.clearLocalCache = true;

            clearTimeout(self.updateTimer);
            self._getFileList({});
          }

          if (self.element.find('.fileupload-list').find('.template-upload').length < 1) {
            /* Remove navigation prompt */
            window.onbeforeunload = null;

            self.element.find('.fileuploadform').trigger('outofthebox-upload-finished');
          }
        },
        dataType: 'json'
      });
    },

    _uploadGetThumbnail: function (file) {
      var self = this;

      var thumbnailUrl = self.options.icons_set + '128x128/';
      if (typeof file.thumbnail === 'undefined' || file.thumbnail === null || file.thumbnail === '') {
        var icon;

        if (typeof file.type === 'undefined' || file.type === null) {
          icon = 'unknown';
        } else if (file.type.indexOf("word") >= 0) {
          icon = 'application-msword';
        } else if (file.type.indexOf("excel") >= 0 || file.type.indexOf("spreadsheet") >= 0) {
          icon = 'application-vnd.ms-excel';
        } else if (file.type.indexOf("powerpoint") >= 0 || file.type.indexOf("presentation") >= 0) {
          icon = 'application-vnd.ms-powerpoint';
        } else if (file.type.indexOf("access") >= 0 || file.type.indexOf("mdb") >= 0) {
          icon = 'application-vnd.ms-access';
        } else if (file.type.indexOf("image") >= 0) {
          icon = 'image-x-generic';
        } else if (file.type.indexOf("audio") >= 0) {
          icon = 'audio-x-generic';
        } else if (file.type.indexOf("video") >= 0) {
          icon = 'video-x-generic';
        } else if (file.type.indexOf("pdf") >= 0) {
          icon = 'application-pdf';
        } else if (file.type.indexOf("zip") >= 0 ||
                file.type.indexOf("archive") >= 0 ||
                file.type.indexOf("tar") >= 0 ||
                file.type.indexOf("compressed") >= 0
                ) {
          icon = 'application-zip';
        } else if (file.type.indexOf("html") >= 0) {
          icon = 'text-xml';
        } else if (file.type.indexOf("application/exe") >= 0 ||
                file.type.indexOf("application/x-msdownload") >= 0 ||
                file.type.indexOf("application/x-exe") >= 0 ||
                file.type.indexOf("application/x-winexe") >= 0 ||
                file.type.indexOf("application/msdos-windows") >= 0 ||
                file.type.indexOf("application/x-executable") >= 0
                ) {
          icon = 'application-x-executable';
        } else if (file.type.indexOf("text") >= 0) {
          icon = 'text-x-generic';
        } else {
          icon = 'unknown';
        }
        return thumbnailUrl + icon + '.png';
      } else {
        return file.thumbnail;
      }
    },

    _initDragDrop: function () {
      var self = this;
      $(document).bind('dragover', function (e) {
        var dropZone = self.element,
                timeout = window.dropZoneTimeout;
        if (!timeout) {
          dropZone.addClass('in');
        } else {
          clearTimeout(timeout);
        }
        var found = false, node = e.target;
        do {
          if ($(node).is(dropZone)) {
            found = true;
            break;
          }
          node = node.parentNode;
        } while (node !== null);
        if (found) {
          $(node).addClass('hover');
        } else {
          dropZone.removeClass('hover');
        }
        window.dropZoneTimeout = setTimeout(function () {
          window.dropZoneTimeout = null;
          dropZone.removeClass('in hover');
        }, 100);
      });
      $(document).bind('drop dragover', function (e) {
        e.preventDefault();
      });
    },

    _initResizeHandler: function () {
      var self = this;
      self._orgininal_width = self.element.width();

      $(window).resize(function () {

        if (self._orgininal_width === self.element.width()) {
          return;
        }

        self._orgininal_width = self.element.width();

        self._refreshView();
      });
    },

    _refreshView: function () {
      var self = this;

      // set a timer to re-apply the plugin
      if (typeof self.resizeTimer !== 'undefined') {
        clearTimeout(self.resizeTimer);
      }

      self.element.find('.image-collage').fadeTo(100, 0);
      self.element.find('.layout-grid').fadeTo(100, 0);

      self.resizeTimer = setTimeout(function () {
        if (self.options.topContainer.hasClass('files') || self.options.topContainer.hasClass('search')) {
          self.renderContentForBrowser();
        }

        if (self.options.topContainer.hasClass('gallery')) {
          self.renderContentForGallery();
        }
      }, 100);
    },

    /**
     * Pipelining function to cache ajax requests
     */
    _pipeline: function (opts) {
      var self = this;
      var conf = $.extend({
        url: self.options.ajax_url,
        data: null,
        method: 'POST'
      }, opts);

      return function (request, drawCallback, settings) {

        var d = conf.data(request);
        $.extend(request, d);
        var storage_key = 'CloudPlugin_' + (request.listtoken + request._ajax_nonce + (typeof request.account_id === 'undefined' ? '' : request.account_id) + request.filelayout + request.OutoftheBoxpath + request.lastpath + request.sort + request.query).hashCode();

        if (self.options.clearLocalCache) {
          self._cacheRemove('all');
          self.options.clearLocalCache = false;
        }

        // API request that the cache be cleared
        if (self.options.forceRefresh) {
          self._cacheRemove('all');
          request.hardrefresh = true;
          self.options.forceRefresh = false;
        }

        if (self._cacheGet(storage_key) !== null) {
          var json = self._cacheGet(storage_key);

          if (json === Object(json)) {
            json.draw = request.draw; // Update the echo for each response
            drawCallback(self, json);
            return true;
          } else {
            self._cacheRemove(storage_key);
          }

        }

        if (typeof settings.jqXHR !== 'undefined' && settings.jqXHR !== null) {
          settings.jqXHR.abort();
        }

        settings.jqXHR = $.ajax({
          type: conf.method,
          url: conf.url,
          data: request,
          dataType: "json",
          cache: false,
          beforeSend: function () {

          },
          success: function (json) {

            if (json === Object(json)) {
              self.element.trigger('ajax-success', [json, request, settings.jqXHR]);
              self._cacheSet(storage_key, json);
              drawCallback(self, json);
            } else {
              self.element.trigger('ajax-error', [json, request, settings.jqXHR]);
              drawCallback(self, false);
              return false;
            }

          },
          error: function (json) {
            self.element.trigger('ajax-error', [json, request, settings.jqXHR]);
            drawCallback(self, false);
            return false;

          }
        });

      };
    },

    _initCache: function () {
      var self = this;

      self._isCacheStorageAvailable = self._cacheStorageAvailable();
      setInterval(function () {
        self._cacheRemove('all');
      }, 1000 * 60 * 15);
    },

    _cacheStorageAvailable: function () {

      try {
        var storage = window['sessionStorage'],
                x = '__storage_test__';
        storage.setItem(x, x);
        storage.removeItem(x);
        return true;
      } catch (e) {
        return e instanceof DOMException && (
                // everything except Firefox
                e.code === 22 ||
                // Firefox
                e.code === 1014 ||
                // test name field too, because code might not be present
                // everything except Firefox
                e.name === 'QuotaExceededError' ||
                // Firefox
                e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
                // acknowledge QuotaExceededError only if there's something already stored
                storage.length !== 0;
      }
    },

    _cacheGet: function (key) {
      if (typeof this.cache.expires === 'undefined') {
        var expires = new Date();
        expires.setMinutes(expires.getMinutes() + 15);
        this.cache.expires = expires;
      }

      if (this.cache.expires.getTime() < new Date().getTime()) {
        this._cacheRemove(key);
      }

      if (this._isCacheStorageAvailable) {
        return JSON.parse(sessionStorage.getItem(key));
      } else {

        if (typeof this.cache[key] === 'undefined') {
          return null;
        }

        return this.cache[key];
      }

    },
    _cacheSet: function (key, value) {
      if (this._isCacheStorageAvailable) {
        try {
          sessionStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
          this._cacheRemove('all');
          return false;
        }
      } else {
        if (typeof this.cache[key] === 'undefined') {
          this.cache[key] = {};
        }

        this.cache[key] = value;
      }
    },
    _cacheRemove: function (key) {
      if (this._isCacheStorageAvailable) {

        if (key === 'all') {
          var i = sessionStorage.length;
          while (i--) {
            var key = sessionStorage.key(i);
            if (/CloudPlugin/.test(key)) {
              sessionStorage.removeItem(key);
            }
          }
        } else {
          sessionStorage.removeItem(key);
        }

      } else {

        if (key === 'all') {
          delete this.cache;
        } else {
          delete this.cache[key];
        }

      }
    },

    _helperDownloadUrlInline: function (url) {
      var hiddenIFrameID = 'hiddenDownloader';
      var iframe = document.getElementById(hiddenIFrameID);
      if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
      }
      iframe.src = url;
    },
    _helperFormatBytes: function (bytes, decimals) {
      if (bytes == 0)
        return '—';
      var k = 1000; // or 1024 for binary
      var dm = decimals + 1 || 3;
      var sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      var i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    },
    _helperIframeFix: function ($element) {
      /* Safari bug fix for embedded iframes*/
      if (/iPhone|iPod|iPad/.test(navigator.userAgent)) {
        $element.each(function () {
          if ($(this).closest('#safari_fix').length === 0) {
            $(this).wrap(function () {
              return $('<div id="safari_fix"/>').css({
                'width': "100%",
                'height': "100%",
                'overflow': 'auto',
                'z-index': '2',
                '-webkit-overflow-scrolling': 'touch'
              });
            });
          }
        });
      }
    },
    _helperCachedScript: function (url, options) {

      // Allow user to set any option except for dataType, cache, and url
      options = jQuery.extend(options || {}, {
        dataType: "script",
        cache: true,
        url: url
      });

      // Use $.ajax() since it is more flexible than $.getScript
      // Return the jqXHR object so we can chain callbacks
      return jQuery.ajax(options);
    },
    _helperReadArrCheckBoxes: function (element) {
      var values = $(element + ":checked").map(function () {
        return this.value;
      }).get();

      return values;
    },
    _helperIsIE: function () {
      var myNav = navigator.userAgent.toLowerCase();
      return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
    }
  });

  var ajaxQueue = $({});
  $.ajaxQueue = function (ajaxOpts) {
    var oldComplete = ajaxOpts.complete;
    ajaxQueue.queue(function (next) {
      ajaxOpts.complete = function () {
        if (oldComplete)
          oldComplete.apply(this, arguments);
        next();
      };
      $.ajax(ajaxOpts);
    });
  };

})(jQuery);

(function ($) {
  $(".OutoftheBox").OutoftheBox(OutoftheBox_vars);
})(jQuery)

var wpcp_playlists = {};

function sendGooglePageView(action, value) {
  if (OutoftheBox_vars.google_analytics === "1") {
    if (typeof ga !== "undefined" && ga !== null) {
      ga('send', 'event', 'Out-of-the-Box', action, value);
    }
    if (typeof _gaq !== "undefined" && _gaq !== null) {
      _gaq.push(['_trackEvent', 'Out-of-the-Box', action, value]);
    }

    if (typeof gtag !== "undefined" && gtag !== null) {
      gtag('event', action, {
        'event_category': 'Out-of-the-Box',
        'event_label': value,
        'value': value
      });
    }
  }
}