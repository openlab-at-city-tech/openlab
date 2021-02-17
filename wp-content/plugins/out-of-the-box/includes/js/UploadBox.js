(function ($) {
  'use strict';
  $.widget("cp.OutoftheBoxUploadbox", {
    options: {
      standalone: false,
      auto_upload: false,
      restrictions: {
        accepted_formats: null,
        min_file_size: null,
        max_file_size: null,
        max_uploads: null,
        quota: null,
        has_requirements: false
      },
      use_encryption: false,
      attached_browser: null,
      dropzone: self.element,
      pre_process: false,
      template_row: '.fileupload-table-row-template',
      queue_table: '.fileupload-table-body',
      debug: false
    },

    _create: function () {

      /* File Upload Settings */
      this.options.auto_upload = (this.element.hasClass('auto_upload') === true || this.element.hasClass('force_auto_upload') === true);
      this.options.standalone = this.options.main.element.hasClass('upload') === true;
      this.options.support_xhr = $.support.xhrFileUpload && (this.options.use_encryption === false);
      this.options.support_chunked = false;
      this.options.multipart_val = (this.options.support_xhr) ? false : true;
      this.options.method_val = 'POST';
      this.options.singlefileuploads_val = true;
      this.options.maxchunksize_val = (this.options.support_chunked) ? 20 * 320 * 1000 : 0; //Multiple of 320kb, the recommended fragment size is between 5-10 MB.
      this.options.max_server_connections = 5;
      if (typeof window.current_server_connections === 'undefined') {
        window.current_server_connections = 0;
      }
      this.upload_events = {};

      this.options.restrictions.accepted_formats = new RegExp(this.element.find('input[name="acceptfiletypes"]').val(), "i");
      this.options.restrictions.min_file_size = parseInt(this.element.find('input[name="minfilesize"]').val());
      this.options.restrictions.max_file_size = (this.options.support_xhr && this.element.find('input[name="maxfilesize"]').attr('data-limit') === '0') ? -1 : parseInt(this.element.find('input[name="maxfilesize"]').val());
      this.options.restrictions.max_uploads = parseInt(this.element.find('input[name="maxnumberofuploads"]').val());
      this.options.has_requirements = this.element.find('.upload-requirements-content-subtitle').children().length !== 0

      this.options.pre_process = this.element.data('preprocess') == true;
      this.options.use_encryption = (this.element.find('input[name="encryption"]').val() === '1');
      this.options.template_row = this.element.find(this.options.template_row);
      this.options.queue_table = this.element.find(this.options.queue_table);
      this.options.dropzone = this.options.main.element;

      /* Is Upload Box part of a Form? */
      this.in_form = this.element.closest('form').length > 0;
      this.form_element = this.element.closest('form');
      this.form_submit_button = this.form_element.find('input[type="submit"]:visible, input[type="button"].gform_next_button:visible, button[id^="gform_submit_button"]:visible, button[class*="cf7md-submit-btn"]:visible, button.wpforms-submit:visible, button.wpforms-page-next:visible, button.frm_button_submit:visible').not('.wpcp-upload-submit, .frm_prev_page');
      this.form_upload_button = null;
      this.block_upload_element = null;

      /* Upload values */
      this.upload_process = null;
      this.pre_process = null;
      this.pre_process_completed = false;
      this.pending_uploads = {}; //Current uploads
      this.finished_uploads = {}; //file information after upload
      this.files_storage = {}; // file information after post processing
      this.queue = {}; //files visible in dom queue
      this.queue_length = 0;
      this.queue_size = 0;

      /* Progress */
      this.progress_timer = null;
      this.upload_started;
      this.time_remaining = 0;

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

      self._log('LOADED', self);

      self._initUploadBox();
      self._initFunctions();
      self._refreshView();

      self._log('ACTIVATED', self);

      window.setTimeout(function () {
        self.initated = true;
      }, 2000);

    },

    /**
     * Initiate the Blue Imp File Upload
     */
    _initUploadBox: function () {
      var self = this;

      self.upload_box = self.element.fileupload({
        url: self.options.ajax_url,
        type: self.options.method_val,
        maxChunkSize: self.options.maxchunksize_val,
        singleFileUploads: self.options.singlefileuploads_val,
        multipart: self.options.multipart_val,
        dataType: 'json',
        autoUpload: self.options.auto_upload,
        maxFileSize: self.options.restrictions.max_file_size,
        acceptFileTypes: self.options.restrictions.accepted_formats,
        dropZone: self.options.dropzone,
        messages: {
          maxNumberOfFiles: self.options.maxNumberOfFiles,
          acceptFileTypes: self.options.acceptFileTypes,
          maxFileSize: self.options.maxFileSize,
          minFileSize: self.options.minFileSize
        },
        limitConcurrentUploads: 3,
        //sequentialUploads: true,
        disableImageLoad: true,
        disableImageResize: true,
        disableImagePreview: true,
        disableAudioPreview: true,
        disableVideoPreview: true,
        uploadTemplateId: null,
        downloadTemplateId: null,
        add: function (e, data) {
          // Add files to the Upload Queue
          self._add(data);
        },
        submit: function (e, data) {
          /*
          Callback for the submit event of each file upload.
          If this callback returns false, the file upload request is not started.
          */

          self._start(data);
          return false;
        }
      }).on('fileuploadprogress', function (e, data) {
        /* Callback for upload progress events.*/
        self._progress(data);
      }).on('fileuploadfail', function (e, data) {
        /* Callback for upload progress events.*/
        var file = data.files[0];
        // Failed Request
        self.queue[file.hash].error = self.options.str_uploading_failed;
        self.queue[file.hash].status.type = 'upload-failed';
        self._isFinished(file);
      }).on('fileuploaddone', function (e, data) {
        /* Callback for completed (success, abort or error) upload requests. This callback is the equivalent to the complete callback provided by jQuery ajax().*/
        self._done(data);
      }).on('outofthebox-upload-start', function () {
        $.each(self.queue, function (hash, file) {
          if (
            file.upload_event !== null &&
            typeof self.finished_uploads[file.hash] === 'undefined' &&
            typeof self.pending_uploads[file.hash] === 'undefined'
          ) {
            self.pending_uploads[file.hash] = file;
            file.upload_event();
          }
        })
      });


    },

    /**
     * Add new files to the Upload Box
     * @param {array} data 
     */
    _add: function (data) {
      var self = this;

      $.each(data.files, function (index, file) {

        // Set Folder path in file object
        var path = '';
        if ("relativePath" in file) {
          path = file.relativePath;
        } else if ("webkitRelativePath" in file) {
          path = file.webkitRelativePath;
        }

        file.path = path.replace(file.name, '');

        if (file.path !== '') {
          self.options.pre_process = true;
          self.pre_process_completed = false;
        }

        // Add own properties to the file object for identification
        file.hash = (file.path + file.name + file.size + ("lastModified" in file ? file.lastModified : '')).hashCode();
        file.listtoken = self.options.listtoken;
        file.description = (typeof file.description !== 'undefined') ? file.description : '';

        /* Files larger than 300MB cannot be uploaded directly to Dropbox :( */
        file.directupload = self.options.support_xhr && (file.size < 314572800);
        file.convert = false; // not implemented

        // Make sure that the file isn't already added in queue
        if (typeof self.queue[file.hash] !== 'undefined') {
          self._log('FILE ALREADY ADDED');
          if (self.queue[file.hash].status.request !== null) {
            self._log('STOP UPLOAD CURRENT FILE UPLOAD');
            self._stop(file);
          }

          self.removeFileFromQueueTable(file);
        }

        // Validate the file
        file = self._validate(file);

        // Add file to queue
        self.queue[file.hash] = file;
        self.queue[file.hash].status = {
          type: 'upload-waiting',
          progress: 0,
          progressbar: null,
          request: null
        };
        self.queue[file.hash].element = self.addFileToQueueTable(file);
        self.queue_size += file.size;
        self.queue_length++;

        if (file.error) {
          self._log('FILE NOT VALID');
          self.element.find('.fileupload-requirements-button').click();
          self.queue[file.hash].status.type = 'validation-failed';
          self.queue[file.hash].upload_event = null;
        } else {
          /* Set upload trigger */
          self.queue[file.hash].upload_event = function () {
            data.process().done(function () {
              data.submit();
            });
          }

          self.element.trigger('outofthebox-add-upload', [file, data, self]);

          self._log('FILE ADDED');
        }

        // Update Element in Queue
        self.updateFileInQueueTable(file);
      });

      /* Start the upload when auto upload is enabled */
      if (self.options.auto_upload) {
        window.setTimeout(function () {
          self._log('AUTO UPLOAD');
          self.element.trigger('outofthebox-upload-start');
        }, 500);
      }
    },

    _validate: function (file) {
      var self = this;

      file.error = false;
      // Check File Format
      if (file.name.length && !self.options.restrictions.accepted_formats.test(file.name)) {
        file.error = self.options.str_filetype_not_allowed;
      }

      // Check File sizes
      else if (self.options.restrictions.min_file_size !== '' && file.size <= self.options.restrictions.min_file_size) {
        file.error = self.options.str_min_file_size;

      } else if (self.options.restrictions.max_file_size > -1 && file.size > 0 && file.size > self.options.restrictions.max_file_size) {
        file.error = self.options.str_max_file_size;

      }

      // Check number of uploads
      else if (self.options.restrictions.max_uploads > 0 && self.queue_length > 0 && (self.queue_length >= self.options.restrictions.max_uploads)) {
        file.error = self.options.str_files_limit;

      };

      // Check Quota
      self._log('VALIDATED', file);
      return file;

    },
    _delete: function (data) {
      var self = this;
    },

    _start: function (data) {
      var self = this;

      self._log('START', data);

      /* Update the queue table */
      $.each(data.files, function (index, file) {

        /* Check if file is still present in queue or already removed */
        if ($.isEmptyObject((self.queue[file.hash]))) {
          file.error = true;
        } else {
          self.updateFileInQueueTable(file);
        }

        if (file.error !== false) {
          data.files.splice(index, 1);
        }
      });

      /* Stop if no files are present in the queue */
      if (data.files.length === 0) {
        self._refreshView();
        return;
      }

      /* Set Progress data */
      if (self.upload_process === null) {
        self.upload_started = new Date().getTime()
      }

      /* Create an async function to control upload box behavior*/
      if (self.upload_process === null) {
        self.upload_process = $.Deferred();

        $.when(self.upload_process).then(
          function () {
            self._log('ALL UPLOADS FINISHED', data);
            self.upload_process = null;

            /* Trigger Post Process */
            self._postProcess().then(function (response) {
              self._finish(data, response);
            });
          }
        );
      }

      /*  Enable/Disable navigation prompt */
      window.onbeforeunload = function () {
        return true;
      };

      /* Update UI elements */
      self.element.addClass('-is-uploading');
      if (self.in_form) {
        self.form_upload_button.val(self.options.str_uploading).html(self.options.str_uploading).prop("disabled", true).addClass("-wpcp-submit-active");
      }

      /* File upload uses single fileuploads */
      var file = data.files[0];
      self.queue[file.hash].status.type = 'upload-starting';
      self.updateFileInQueueTable(file);

      /* Preprocess the upload */
      self._preProcess().then(function () {
        // Pre process successful
        self.pre_process_completed = true;
        self._log('PREPROCESS COMPLETED');

        /* Add File to upload pending list which while trigger post process when empty */
        self.pending_uploads[file.hash] = file;

        self.element.trigger('outofthebox-start-upload', [file, data, self]);
        self._scrollToFile(file)

        if (file.directupload) {
          /* Do Direct Upload */
          self._getUploadUrl(file).then(
            function (response) {
              window.current_server_connections--;

              if ($.isEmptyObject(response) || typeof response.result === 'undefined' || typeof response.url === 'undefined') {
                self._log('UPLOAD FAILED', file);

                self.queue[file.hash].error = self.options.str_uploading_failed;
                self._isFinished(file);
                return false;
              };

              // Successful Request
              data.url = response.url;
              self.queue[file.hash].fileid = response.id;
              self.queue[file.hash].convert = response.convert;
              data.jqXHR = self.queue[file.hash].status.request = self.upload_box.fileupload('send', data);

              self._log('UPLOAD DIRECTLY TO CLOUD', data);
            },
            function (response) {
              // Failed Request
              window.current_server_connections--;
              self._log('UPLOAD FAILED', file);
              self.queue[file.hash].error = self.options.str_uploading_failed;
              self._isFinished(file);
              return false;
            }
          );
        } else {
          // Fix for servers that don't support uploads via application/octet-stream :S
          self.upload_box.fileupload('option', 'multipart', true);

          /* Do Upload via Server*/
          data.formData = {
            action: 'outofthebox-upload-file',
            account_id: self.options.account_id,
            type: 'do-upload',
            hash: file.hash,
            file_path: file.path,
            file_description: file.description,
            lastpath: self.options.main.element.attr('data-path'),
            listtoken: self.options.listtoken,
            _ajax_nonce: self.options.upload_nonce
          };
          self._log('UPLOAD TO SERVER');
          data.jqXHR = self.queue[file.hash].status.request = self.upload_box.fileupload('send', data);
        };
      }, function () {
        // Pre process failed
        self.pre_process = null;
        self.pre_process_completed = false;
        self._log('PREPROCESS FAILED');

        var file = data.files[0];
        self.queue[file.hash].error = self.options.str_uploading_failed;
        self._isFinished(file);
        return false;
      });
    },
    _preProcess: function () {

      var self = this;

      /* In case we don't need to preprocess, directly continue with the upload */
      if (self.options.pre_process === false || self.pre_process_completed) {
        return new Promise(function (resolve, reject) {
          resolve(true);
        });
      }

      if (self.pre_process !== null) {
        return self.pre_process;
      }

      /* Preprocess to e.g. create a Private Folder */
      self.pre_process = $.ajax({
        type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-upload-file',
          account_id: self.options.main.element.attr('data-account-id'),
          type: 'upload-preprocess',
          listtoken: self.options.listtoken,
          lastpath: self.options.main.element.attr('data-path'),
          _ajax_nonce: self.options.upload_nonce
        },
        dataType: 'json'
      });

      self._log('PREPROCESS STARTED', self.pre_process);
      return self.pre_process;
    },

    _getUploadUrl: function (file) {
      var self = this;

      self._log('OBTAIN DIRECT UPLOAD URL', file);

      return new Promise(function (resolve, reject) {
        (function waitForConnection() {
          if (window.current_server_connections < self.options.max_server_connections) {
            window.current_server_connections++;
            return resolve();
          }
          setTimeout(waitForConnection, 500);
        })();
      }).then(function (response) {
        return $.ajax({
          type: "POST",
          url: OutoftheBox_vars.ajax_url,
          data: {
            action: 'outofthebox-upload-file',
            account_id: self.options.main.options.account_id,
            type: 'get-direct-url',
            filename: file.name,
            file_size: file.size,
            file_path: file.path,
            file_description: file.description,
            mimetype: file.type,
            orgin: (!window.location.origin) ? window.location.protocol + "//" +
              window.location.hostname +
              (window.location.port ? ':' + window.location.port : '') : window.location.origin,
            lastpath: self.options.main.element.attr('data-path'),
            listtoken: self.options.listtoken,
            _ajax_nonce: self.options.upload_nonce,
          },
          dataType: 'json'
        });
      });
    },

    _done: function (data) {
      var self = this;

      /* File upload uses single fileuploads */
      var file = data.files[0];

      /* Terminated running progress watches */
      if (self.options.support_xhr === false && typeof self.queue[file.hash].status.server_progress !== 'undefined') {
        self.queue[file.hash].status.server_progress.abort();
      }

      /* Check if upload was successful */
      var is_uploaded = true;
      if (self.options.support_xhr && (typeof data.result === 'undefined' || data.result === null || data.result === "")) {
        is_uploaded = false;
      }

      if (self.options.support_xhr === false && (typeof data.result.file === 'undefined' || typeof data.result.file.fileid === 'undefined')) {
        is_uploaded = false;
      }

      if (is_uploaded === false) {
        self.queue[file.hash].error = self.options.str_uploading_failed;
        self.queue[file.hash].status.type = 'upload-failed';
        self._isFinished(file);
        return;
      }

      /* Add FILE ID */
      self.queue[file.hash].fileid = (self.options.support_xhr) ? self.queue[file.hash].fileid : data.result.file.fileid;
      self.queue[file.hash].convert = (self.options.support_xhr) ? self.queue[file.hash].convert : data.result.file.convert;
      file = self.queue[file.hash];

      /* Convert file if needed and finish upload */
      self._convert(file).then(function (response) {
        if ($.isEmptyObject(response) || typeof response.result === 'undefined' || response.result === 0) {
          self.queue[file.hash].error = self.options.str_uploading_convert_failed;
          self._isFinished(file);
          return false;
        };

        self.queue[file.hash].status.type = 'upload-finished';
        self.queue[file.hash].fileid = response.fileid;
        self.finished_uploads[self.queue[file.hash].fileid] = self.queue[file.hash];

        sendGooglePageView('Upload file', self.queue[file.hash].name);
        self._isFinished(file);
      });
    },

    /* Convert files if needed */
    _convert: function (file) {
      var self = this;

      /* If no Post Convert process is required */
      if (file.convert === false) {
        return new Promise(function (resolve, reject) {
          var response = {
            'result': 1,
            'fileid': file.fileid
          }
          resolve(response);
        });
      }

      self.queue[file.hash].status.type = 'upload-converting';
      self.updateFileInQueueTable(file);

      self._log('START CONVERT', file);

      return $.ajax({
        type: "POST",
        url: OutoftheBox_vars.ajax_url,
        data: {
          action: 'outofthebox-upload-file',
          account_id: self.element.attr('data-account-id'),
          type: 'upload-convert',
          listtoken: self.options.listtoken,
          fileid: file.fileid,
          convert: file.convert,
          _ajax_nonce: self.options.upload_nonce
        },
        dataType: 'json'
      });
    },

    /**
     * Resolve Upload Box Promise when no files are uploaded anymorere *
     * @param {*} file 
     */
    _isFinished: function (file) {
      var self = this;

      self._log('FILE UPLOAD FINISHED', file);

      self.updateFileInQueueTable(file);

      if (file.error !== false) {
        self.options.main._logEvent('log_failed_upload_event', self.options.main.element.attr('data-id'), {
          'name': file.name,
          'size': file.size,
          'error': file.error
        });
      }

      self.queue[file.hash].upload_event = null;
      delete self.pending_uploads[file.hash];

      if (Object.keys(self.pending_uploads).length === 0) {
        self.upload_process.resolve('upload-finished');
      }
    },

    /* Post Process to send email notifications and trigger PHP other hooks */
    _postProcess: function () {
      var self = this;

      if (Object.keys(self.finished_uploads).length === 0) {
        self._log('NO POST PROCESS (NO FILES UPLOADED)');
        return new Promise(function (resolve, reject) {
          resolve(true);
        });
      }


      self._log('POST PROCESS STARTED');

      return $.ajax({
        type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-upload-file',
          account_id: self.element.attr('data-account-id'),
          type: 'upload-postprocess',
          listtoken: self.options.listtoken,
          files: Object.keys(self.finished_uploads),
          lastpath: self.options.main.element.attr('data-path'),
          _ajax_nonce: self.options.upload_nonce
        },
        dataType: 'json'
      });
    },

    _finish: function (data, response) {
      var self = this;

      self._log('POST PROCESS FINISHED', response);

      window.onbeforeunload = null;

      clearInterval(self.progress_timer);
      self.progress_timer = null;

      /* Store API information in object */
      $.each(response.files, function (fileid, file) {
        self.files_storage[fileid] = {
          "hash": fileid,
          "name": file.name,
          "description": file.description,
          "type": file.type,
          "path": file.completepath,
          "size": file.filesize,
          "link": file.link,
          "account_id": file.account_id,
          "folderurl": file.folderurl,
        };
      });

      /* Update Form input field containing file information */
      self.element.find('.fileupload-filelist').val(JSON.stringify(self.files_storage)).trigger('change');

      /* Update UI elements */
      self.element.removeClass('-is-uploading');
      self.element.find('.-upload-finished').addClass('-upload-postprocess-finished');

      if (self.in_form) {
        self.form_upload_button.val(self.form_submit_button.val()).html(self.form_submit_button.html()).prop("disabled", false).removeClass("-wpcp-submit-active");
      }
      self._refreshView();

      /* Refresh File Browser if attached */
      if (self.options.standalone === false) {
        if (self.options.main.element.hasClass('upload') === false) {
          self.options.main.options.clearLocalCache = true;
          self.options.main.options.forceRefresh = true;

          clearTimeout(self.options.main.updateTimer);
          self.options.main._getFileList({});
        }
      }

      var finish_upload = true;

      /* Check for Errors (only required when part of a form)*/
      if (self.in_form) {
        $.each(self.queue, function (index, file) {
          if (file.error !== false) {
            finish_upload = false;
            self._scrollToFile(file)

            self._log('QUEUE CONTAINS ERRORS', self.queue);
            self._abort(data);

            self._dialogFailedUploads([file]);
          };
        });
      }

      if (finish_upload) {
        self.element.trigger('outofthebox-upload-finished', [data, self]);
      }
    },

    _abort: function (data, response) {
      var self = this;

      self._log('UPLOAD ABORT', response);

      window.onbeforeunload = null;
      self.upload_process = null;

      /* Update UI elements */
      self.element.removeClass('-is-uploading');
      self._refreshView();

      /* Refresh File Browser if attached */
      if (self.options.standalone === false) {
        if (self.options.main.element.hasClass('upload') === false) {
          self.options.main.options.clearLocalCache = true;
          self.options.main.options.forceRefresh = true;

          clearTimeout(self.options.main.updateTimer);
          self.options.main._getFileList({});
        }
      }

      self.form_upload_button.val(self.form_submit_button.val()).html(self.form_submit_button.html()).prop("disabled", false).removeClass("-wpcp-submit-active");;

    },
    _progress: function (data) {
      var self = this;
      self._log('PROGRESS', data);

      /* File upload uses single fileuploads */
      var file = data.files[0];
      var progress;

      if (self.options.support_xhr && file.directupload) {
        /* Upload Progress for direct upload */
        progress = parseInt(data.loaded / data.total * 100, 10);
        self.queue[file.hash].status.type = 'uploading-to-cloud';
        /* Global progress*/
        if (self.progress_timer === null) {
          self.progress_timer = setInterval(function () {
            self._globalProgress(self);
          }, 5000);
        }


      } else {
        /* Upload Progress for upload via Server */
        progress = parseInt(data.loaded / data.total * 100, 10) / 2;
        self.queue[file.hash].status.type = 'uploading-to-server';

        /* Upload to server has finished. Uploading to cloud */
        if (progress >= 50) {
          progress = 50;
          self.queue[file.hash].status.type = 'uploading-to-cloud';

          setTimeout(function () {
            self._watchServerProgres(file);
          }, 2000);
        }
      }

      /* Update Information in table */
      self.queue[file.hash].status.progress = (progress > 0 ? (progress - 1) : progress);
      self.updateFileInQueueTable(file);

    },

    _globalProgress: function () {
      var self = this;

      var time_spent = new Date().getTime() - self.upload_started;
      var total_size = 0;
      var total_progress = 0;
      var i = 0;

      // Get global progress based on weighted value
      $.each(self.queue, function (hash, file) {
        if (file.status.type === 'validation-failed' || file.status.type === 'upload-finished' || file.status.type === 'upload-failed') {
          return;
        }

        total_size += file.size;
        i++;
      })

      $.each(self.queue, function (hash, file) {
        if (file.status.type === 'validation-failed' || file.status.type === 'upload-finished' || file.status.type === 'upload-failed') {
          return;
        }

        total_progress += (file.size / total_size) * file.status.progressbar.value;
      })

      total_progress = Math.round(total_progress) / 100;

      // Use the time spent and the current progress to estimate the time remaining
      self.time_remaining = Math.round(((time_spent / total_progress) - time_spent) / 1000);

      // Minimum time remaining is 1 second
      self.time_remaining = Math.max(self.time_remaining, 1);

      if (self.time_remaining !== Infinity) {
        self.element.trigger('progress-update', [self.time_remaining, self]);
      }
    },

    _watchServerProgres: function (file) {
      var self = this;

      /* Global progress*/
      if (self.progress_timer === null) {
        self.progress_timer = setInterval(function () {
          self._globalProgress(self);
        }, 5000);
      }

      self._getServerProgress(file).then(function (response) {

        self._log('RECEIVED STATUS UPLOAD SERVER -> CLOUD', response);

        // Stop on server errror
        if ($.isEmptyObject(response) || typeof response.status === 'undefined' || response.status.progress === 'upload-failed') {
          return;
        }

        self.queue[file.hash].status.type = response.status.progress;
        self.queue[file.hash].status.progress = 50 + (response.status.percentage / 2) - 1;

        self.updateFileInQueueTable(file);

        if (response.status.progress === 'upload-finished') {
          return
        }

        setTimeout(function () {

          if (self.queue[file.hash].status.type === 'upload-failed' || self.queue[file.hash].status.type === 'upload-finished') {
            return
          }

          self._watchServerProgres(file);
        }, 2000);

      })
    },

    _getServerProgress: function (file) {
      var self = this;

      self._log('GET STATUS UPLOAD SERVER -> CLOUD', file);

      self.queue[file.hash].status.server_progress = $.ajax({
        type: "POST",
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
      });

      return self.queue[file.hash].status.server_progress;
    },

    _stop: function (file) {
      var self = this;

      self._log('ABORT FILE UPLOAD', file);

      self.queue[file.hash].status.request.abort();
      self.queue[file.hash].status.type = 'upload-failed';
      self.queue[file.hash].error = file.error = self.options.str_uploading_cancelled;
    },

    /**
     * Add a failed upload again to the queue
     */
    _redo: function (file) {
      var self = this;

      self._log('REDO FILE UPLOAD', file);

      self.element.fileupload('add', {
        files: [file]
      });

    },

    /**
     * Reset Upload Box
     */
    _clear: function () {
      var self = this;

      self.upload_process = null;
      self.pre_process = null;
      self.pre_process_completed = false;
      self.pending_uploads = {};
      self.finished_uploads = {};
      self.files_storage = {};
      self.queue = {};
      self.queue_length = 0;
      self.queue_size = 0;

      self.element.find('input[name="fileupload-filelist_' + self.options.listtoken + '"]').val();

      if (self.in_form) {
        self.form_element.find('.fileupload-filelist').val('');
        self.form_upload_button.remove();
        self.form_upload_button = null;
        self._addFormSubmitButton();
      }

      self.options.queue_table.find('tr:not(.fileupload-table-row-template)').remove();

      self._refreshView();

      self._log('CLEAR');
    },

    _log: function (data) {
      var self = this;
      if (self.options.debug) {
        console.log('UPLOAD BOX:' + data);
      }
    },

    /**
     * Add new File to the Upload Queue
     * @param {*} file 
     */
    addFileToQueueTable: function (file) {
      var self = this;

      // Clone queue row from template
      var row = self.options.template_row.clone().removeClass('fileupload-table-row-template').addClass('-upload-waiting');

      // Add file metadata to the row in order to identify it in the future
      row.attr('data-id', file.hash);

      // Add File name, Size and thumbnail
      row.find('.fileupload-table-text-title').text(file.path.replace(file.name, '') + file.name);
      if (file.size !== 'undefined' && file.size > 0) {
        row.find('.fileupload-table-text-subtitle').text(self.options.main._helperFormatBytes(file.size, 1));
      }
      row.find('.fileupload-table-cell-icon img').attr('src', self._getThumbnail(file));
      self.queue[file.hash].status.progressbar = new ldBar(row.find('.fileupload-loading-bar')[0], {
        "preset": "circle",
        "value": 0
      });

      // Add row to table
      self.options.queue_table.append(row);

      return row;
    },

    updateFileInQueueTable: function (file) {
      var self = this;

      var row = self.queue[file.hash].element;

      // Update progress
      row.removeClass('-upload-waiting -upload-starting -uploading-to-cloud -uploading-to-server -upload-converting -upload-failed');
      row.addClass('-' + self.queue[file.hash].status.type);

      self._log(self.queue[file.hash], self.queue[file.hash].status.progress);
      if (self.queue[file.hash].status.type === 'uploading-to-cloud' || self.queue[file.hash].status.type === 'uploading-to-server') {
        self.queue[file.hash].status.progressbar.set(self.queue[file.hash].status.progress)
      }

      // Add description if present
      row.find('.fileupload-table-text-subtitle').text(self.options.main._helperFormatBytes(file.size, 1) + ((file.description) ? ' - ' + file.description : ''));

      // Add Error information if present
      if (file.error !== false) {
        row.find('.fileupload-table-text-subtitle').text(file.error);
        row.addClass('-upload-failed');

        self.queue_size -= file.size;
        self.queue_length--;

      }

      // Refresh complete view 
      self._refreshView();

    },
    removeFileFromQueueTable: function (file) {
      var self = this;

      var row = self.queue[file.hash].element;

      row.removeClass('-upload-waiting');

      row.animate({
        "opacity": "0"
      }, "slow", function () {
        $(this).remove();
      });

      /* Update counters if validated (and not uploaded) files are removed from the queue */
      if (row.hasClass('-upload-failed') === false && row.hasClass('-upload-finished') === false) {
        self.queue_size -= file.size;
        self.queue_length--;
      }

      self.element.trigger('outofthebox-upload-removed', [file, self]);

      // Remove file from the queue
      delete self.queue[file.hash];

      self._refreshView();

    },

    /* Update the information in the Upload Box  */
    _refreshView: function () {
      var self = this;

      self.element.removeClass('-has-queue')
      if (self.queue_length > 0) {
        self.element.addClass('-has-queue')
      }

      if (self.queue_length > 0) {
        self.element.find('.fileupload-items').text(self.queue_length + ' ' + ((self.queue_length === 1) ? self.options.str_item : self.options.str_items));
        self.element.find('.fileupload-items-size').text(self.options.main._helperFormatBytes(self.queue_size, 1));
      }

      /* Enable/Disable Start Upload button */
      var start_button = self.element.find('.fileupload-start-button');
      start_button.prop('disabled', (self.upload_process !== null));

      if (self.upload_process !== null) {
        start_button.html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_uploading + '</span>');
      } else {
        start_button.html('<span> ' + self.options.str_uploading_start + '</span>');
      }

      if (self.options.auto_upload === false) {
        self.element.find('.fileupload-add-button').prop('disabled', (self.upload_process !== null));
      }

      self._updateRequiredUI();
    },

    _initFunctions: function () {
      var self = this;

      /* Add Files/Folder buttons */
      self.element.find('.upload-add-file, .fileupload-header-title').click(function (e) {

        e.preventDefault();
        e.stopPropagation();

        self.element.find(".upload-input.upload-input-files").trigger("click");
        tippy.hideAll();
      });

      self.element.find('.upload-add-folder').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.element.find(".upload-input.upload-input-folder").trigger("click");
        tippy.hideAll();
      });

      var $upload_button = self.element.find('.fileupload-add-button');

      if ($upload_button.next().find('li').length === 1) {
        // File Uploads only; Hide file / folder selector, but just fire file upload event

        $upload_button.click(function () {
          self.element.find('.upload-add-file:first').trigger('click');
        })

      } else {
        $upload_button.next().removeClass('tippy-content-holder');

        tippy($upload_button.get(0), {
          trigger: 'click',
          content: $upload_button.next().get(0),
          allowHTML: true,
          placement: 'bottom-end',
          moveTransition: 'transform 0.2s ease-out',
          interactive: true,
          interactiveDebounce: 500,
          theme: 'wpcloudplugins-' + self.options.content_skin,
          onShown: function (instance) {},
          onCreate: function (instance) {}
        });
      }

      /* Remove button */
      $(self.element).on('click', '.upload-remove', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var hash = $(this).closest('.fileupload-table-row').attr('data-id');
        var file = self.queue[hash];

        self.removeFileFromQueueTable(file);
      })

      /* Start Upload Button */
      self.element.find('.fileupload-start-button').click(function (e) {
        e.preventDefault();
        e.stopPropagation()

        self.element.trigger('outofthebox-upload-start');
      });

      /* Stop button */
      $(self.element).on('click', '.upload-stop', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var hash = $(this).closest('.fileupload-table-row').attr('data-id');
        var file = self.queue[hash];

        self._stop(file);
      })

      /* Redo button */
      $(self.element).on('click', '.upload-redo', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var hash = $(this).closest('.fileupload-table-row').attr('data-id');
        var file = self.queue[hash];

        self._redo(file);
      })

      /* Requirements  */
      if (self.options.has_requirements === false) {
        self.element.find('.fileupload-requirements-button').remove();
      } else {

        var $requirments_button = self.element.find('.fileupload-requirements-button');
        $requirments_button.next().removeClass('tippy-content-holder');

        tippy($requirments_button.get(0), {
          trigger: 'click mouseenter focus',
          content: $requirments_button.next().get(0),
          allowHTML: true,
          moveTransition: 'transform 0.2s ease-out',
          theme: 'wpcloudplugins-' + self.options.content_skin,
          onShown: function (instance) {},
          onCreate: function (instance) {}
        });
      }

      if (self.options.restrictions.max_file_size < 0) {
        self.element.find('.max-file-size').text(self.options.str_uploading_no_limit);
      }

      /* Set Cookie for Guest uploads */
      if (self.options.standalone && document.cookie.indexOf("OFTB-ID=") == -1) {
        var date = new Date();
        date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toUTCString();
        var id = Math.random().toString(36).substr(2, 16);
        document.cookie = "OFTB-ID=" + id + expires + "; path=" + self.options.cookie_path + "; domain=" + self.options.cookie_domain + ";SameSite=Strict;";
      }

      /* Global Progress information */
      // Not implemented yet due to inaccurate predictions  
      // var global_progress = self.element.find('.fileupload-progress').wpcp_countdown({
      //   date: new Date()
      // });
      // self.element.on('progress-update', function (event, value) {

      //   global_progress.wpcp_countdown("destroy");
      //   var expected_time = new Date();
      //   expected_time = new Date(expected_time.getTime() + value * 1000);

      //   global_progress = self.element.find('.fileupload-progress').wpcp_countdown({
      //     date: expected_time
      //   });
      // })

      /* Drag/Drop  */
      self._initDragDrop();

      /* Add Description button */
      $(self.element).on('click', 'button.upload-add-description', function (e) {

        var hash = $(this).closest('.fileupload-table-row').attr('data-id');
        var file = self.queue[hash];

        self._dialogAddDescription(file);
      });

      /* Additional Form functions */
      if (self.in_form) {
        self._initFormActions();
      }
    },

    _initFormActions: function () {
      var self = this;

      self.element.addClass('-is-formfield');

      /* Add own Submit button for element */
      self._addFormSubmitButton();

      /* Required fields UI */
      self._updateRequiredUI();

      /* Initiate functionality to control Custom Folder Names (via Private Folders)*/
      self._initCustomFolderNames();

      /* Disable Auto Upload in case the Upload Box is part of Form element*/
      self.options.auto_upload = self.element.hasClass('auto_upload') || self.element.hasClass('force_auto_upload') || false;

      self.element.on('outofthebox-add-upload outofthebox-upload-removed', function () {
        self._updateRequiredUI();
      });

      self.form_element.find('input, textarea').on('input change', function () {
        self._updateRequiredUI();
      });

      /* Add OnChange event to update Form input fields if needed */
      self.element.find('.fileupload-filelist').change(function () {
        self.options.main.element.parent().next('.fileupload-input-filelist').val($(this).val());
      });

      /* Add trigger to submit form itself (via custom Submit button)*/
      if (self.options.auto_upload === false) {
        self.element.on('outofthebox-upload-finished', function () {
          if ($('.OutoftheBox .-is-uploading').length > 0) {
            return; // Don't fire when there are still uploads processing on the page.
          }

          self.form_upload_button.trigger('click')
        });
      }

      /* Actions for CF7 form events */
      var wpcf7 = $(self.element).closest('.wpcf7');
      if (wpcf7.length > 0) {
        wpcf7.get(0).addEventListener('wpcf7submit', function (event) {
          self.form_element.removeClass("-is-submitting");
          //self._addFormSubmitButton();
        }, false);
      }

      /* Actions for WPForm form events */
      var wpforms = $(self.element).closest('.wpforms-form');
      if (wpforms.length > 0) {

        self.form_element.on('invalid-form wpformsAjaxSubmitError wpformsAjaxSubmitFailed', function (event) {
          self.form_element.removeClass("-is-submitting");
        });

        self.form_element.on('wpformsPageChange', function (event, page, $form) {
          self.form_element.removeClass("-is-submitting");
          self.form_upload_button.removeClass('-wpcp-submit-done');
          self.form_submit_button.addClass('-wpcp-submit-hidden');
          self._refreshView();
        });
      }

      /* Actions for Formidable Forms events */
      jQuery(document).on('frmFormComplete frmFormErrors', function (event, object, response) {
        self.form_element.removeClass("-is-submitting");
      });

      jQuery(document).on('frmFormComplete', function (event, object, response) {
        self._clear();
      });

      jQuery(document).on('frmPageChanged', function (event, object, response) {
        self.form_element.removeClass("-is-submitting");
        self.form_upload_button.removeClass('-wpcp-submit-done');
        self.form_submit_button.addClass('-wpcp-submit-hidden');
        self._refreshView();
      });

      /* Event to clear uploadbox if form is reset*/
      self.form_element.on("reset", function () {
        self._clear();
      });

      /* Prefill the Boxes if data is present in form fields*/
      self._prefillQueue();
    },

    _updateRequiredUI: function () {
      var self = this;

      if (self.in_form === false) {
        return
      }

      /* Set if upload field is required in form */
      self.options.is_required =
        (
          (self.element.closest('.gfield_contains_required').length) ||
          (self.element.closest('.wpcf7-validates-as-required').length) ||
          (self.element.closest('.frm_required_field').length) ||
          (self.element.closest('.wpforms-field').find('.wpforms-required-label').length)
        );

      if (self.options.is_required) {
        self.element.addClass('-is-required');
      }

      if (self.element.hasClass('-is-uploading')) {
        return;
      }

      self.element.removeClass('-need-files -has-files');
      if (
        self.options.is_required &&
        self.element.find('.-upload-waiting').length === 0 &&
        Object.keys(self.finished_uploads).length === 0
      ) {
        self.element.addClass('-need-files');
        self.form_upload_button.prop("disabled", true);
      } else {
        self.element.addClass('-has-files');
        self.form_upload_button.prop("disabled", false);
      }

    },

    /* Replace the current form submit button with a clone, and add own events
    Trigger the default button, or another Upload Box submit button once the upload is finished */
    _addFormSubmitButton: function () {
      var self = this;

      self.form_submit_button.addClass('wpcp-submit-original -wpcp-submit-hidden');

      self.form_upload_button = self.form_submit_button.clone()
        .attr('id', 'wpcp_submit_replacement_' + self.options.listtoken)
        .attr('onclick', null)
        .attr('onkeypress', null)
        .prop('onclick', null)
        .prop('onkeypress', null)
        .off()
        .addClass('wpcp-upload-submit wpcf7-submit')
        .removeClass('wpcp-submit-original -wpcp-submit-hidden')
        .insertBefore(self.form_submit_button)
        .click(function (e) {
          self._formSubmitEvent(e);
        })

    },

    _formSubmitEvent: function (e) {
      var self = this;

      e.stopPropagation();
      e.preventDefault();

      /* First validate form if this is available */
      if ($.isFunction(self.form_element.valid) && self.form_element.valid() === false) {
        return false;
      }

      /* Don't submit with errors in queue */
      var files_with_errors = false;
      if (Object.keys(self.queue).length > 0) {
        Object.keys(self.queue).forEach(function (hash) {
          var file = self.queue[hash];
          if (file.error !== false) {
            files_with_errors = true;
            self._scrollToFile(file)
          }
        });
      }

      if (files_with_errors) {
        self._dialogRemoveFromQueue();
        return false;
      }


      /* Don't submit when upload is required but no files are added yet*/
      self.element.removeClass('-need-files');
      if (self.options.is_required && self.element.find('.-upload-waiting').length === 0 && Object.keys(self.finished_uploads).length === 0) {
        self.element.addClass('-need-files');

        $('html, body').animate({
          scrollTop: self.element.offset().top
        }, 1500);

        return false;
      }

      /* Start Upload if there are files in the queue */
      //self.form_upload_button.val(self.form_submit_button.val()).removeClass("-wpcp-submit-active").prop("disabled", false);
      self.form_upload_button.prop("disabled", false).removeClass("-wpcp-submit-active");
      if (self.element.find('.-upload-waiting').length > 0) {
        self.element.trigger('outofthebox-upload-start');
        return false
      }

      /* Don't fire when there are still uploads processing on the page or the form is already submitted. */
      if (
        self.element.find('.-upload-waiting').length > 0 || // Are there still files queued in any of the upload boxes on the form?
        self.element.find('.-need-files').length > 0 || // Are there still files required in any of the upload boxes on the form?
        $('.OutoftheBox .-is-uploading').length > 0 // Are there still uploads being processed on the page?
      ) {
        return false;
      }

      self.form_upload_button.val(self.form_submit_button.val()).html(self.form_submit_button.html()).addClass('-wpcp-submit-done');

      /* Trigger the next button in sequence */
      if (self.form_upload_button.next('.wpcp-upload-submit').length > 0) {
        self.form_upload_button.next('.wpcp-upload-submit').trigger('click');
        return;
      }

      /* Remove files[] fields from form to prevent them being submitted */
      var files_input = [];
      self.form_element.find('.upload-input').each(function () {
        files_input.push({
          parent: $(this).parent(),
          input: $(this).clone(true)
        });

        $(this).remove();

      })

      /* Is the form already submitting? */
      if (self.element.hasClass("-is-submitting")) {
        return false;
      }

      /* Fire form submit when upload is finished*/
      self.form_element.addClass("-is-submitting");
      self.form_submit_button.removeClass('-wpcp-submit-hidden').trigger('click');

      /* Scroll to Submit button */
      $('html, body').animate({
        scrollTop: self.form_submit_button.offset().top
      }, 1500);

      $.each(files_input, function () {
        this.parent.append(this.input)
      })
    },

    /**
     * If the Upload Box is part of a Form, render the rows of already uploaded content
     *  when the input field is filled
     */
    _prefillQueue: function () {
      var self = this;

      var input_field = self.element.find('input[name="fileupload-filelist_' + self.options.listtoken + '"]');
      var form_input_field = self.options.main.element.parent().next('.fileupload-input-filelist');

      if (input_field.val().length === 0) {
        if (form_input_field.length === 0 || form_input_field.val().length === 0) {
          return;
        } else {
          input_field.val(form_input_field.val());
        }
      }

      self.finished_uploads = JSON.parse(input_field.val());

      $.each(self.finished_uploads, function (index, file) {
        self.queue[file.hash] = file;
        self.queue[file.hash].status = {
          type: 'upload-finished',
          progress: 100,
          progressbar: null,
          request: null
        };
        self.queue[file.hash].upload_event = null;
        self.queue[file.hash].size = self.options.main._helperReturnBytes(file.size);
        self.queue[file.hash].error = false;
        self.queue[file.hash].element = self.addFileToQueueTable(file);
        self.queue_size += file.size;
        self.queue_length++;

        self.element.trigger('outofthebox-add-upload', [file, [], self]);

        self.updateFileInQueueTable(file);
      });


      self._refreshView();
    },

    _initCustomFolderNames: function () {
      var self = this;

      // All the input fields that are used for custom folder names
      var inputs = self.form_element.find('input.outofthebox_private_folder_name, select.outofthebox_private_folder_name, .outofthebox_private_folder_name input:last-of-type');

      if (inputs.length === 0) {
        return;
      }

      // Add DIV on top of upload box to prevent uploads before other required input fields are set
      if (self.block_upload_element === null) {
        self.block_upload_element = $('<div>').attr('class', 'fileupload-box-block').html('<div>' + self.options.str_uploading_required_data + '</div>');
        self.block_upload_element.insertBefore(self.element.css('position', 'relative'))
      }

      //self.block_upload_element.hide();
      //if (should_block) {
      self.block_upload_element.show();
      //}

      // Add events to input fields to update the block element and add the data to a cookie
      inputs.on("change keyup", function () {
        var name = '';
        var all_filled = true;

        inputs.each(function () {
          if ($(this).val() === '') {
            all_filled = false;
          }
          name += $(this).val() + '|';
        });
        name = (name.length > 0) ? name.slice(0, -1) : name;

        if (all_filled && name.length > 2) {
          self.block_upload_element.fadeOut();
          document.cookie = 'WPCP-FORM-NAME-' + self.options.listtoken + '=' + name + '; path=/';
        } else {
          self.block_upload_element.fadeIn();
        }
      });

      inputs.trigger('keyup');
    },

    _initDragDrop: function () {
      var self = this;

      $(document).on('dragover', function (e) {
        var dropZone = self.options.dropzone,
          timeout = self.dropZoneTimeout;
        if (!timeout) {
          dropZone.addClass('in');
        } else {
          clearTimeout(timeout);
        }
        var found = false,
          node = e.target;
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
        self.dropZoneTimeout = setTimeout(function () {
          self.dropZoneTimeout = null;
          dropZone.removeClass('in hover');
        }, 100);
      });

      $(document).on('drop dragover', function (e) {
        e.preventDefault();
      });
    },

    _scrollToFile: function (file) {
      var self = this;

      /* Scroll to element in queue */
      var offset = self.options.queue_table.scrollTop();
      offset = offset + self.queue[file.hash].element.position().top;

      clearTimeout(self.tablescroller);
      self.tablescroller = setTimeout(function () {
        self.options.queue_table.animate({
          scrollTop: (offset)
        }, 800);
      }, 500);
    },

    _getThumbnail: function (file) {
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

    _dialogAddDescription: function (file) {

      var self = this;

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Description Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + this.options.main.options.str_cancel_title + '" >' + this.options.main.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="adddescription" type="button" title="' + this.options.main.options.str_save_title + '" >' + this.options.main.options.str_save_title + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + this.options.main.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" ><textarea id="outofthebox-modal-description-input" name="outofthebox-modal-description-input" style="width:100%" rows="8" placeholder="' + file.name + ' | ' + this.options.main.options.str_add_description + '"></textarea></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + this.options.main.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');

      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      /* Fill Textarea */
      $('#outofthebox-modal-description-input').val(file.description.replace(/<br\s?\/?>/g, "\r"));

      /* Set the button actions */
      $('#outofthebox-modal-action #outofthebox-modal-description-input').off('keyup');
      $('#outofthebox-modal-action #outofthebox-modal-description-input').on("keyup", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
          $('#outofthebox-modal-action .outofthebox-modal-description-btn').trigger('click');
        }
      });
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {
        file.description = $('#outofthebox-modal-description-input').val();
        self.queue[file.hash] = file;
        self.updateFileInQueueTable(file)
        modal_action.close();
      });

      /* Open the dialog */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        bodyClass: 'rmodal-open',
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

    _dialogFailedUploads: function (files, mouseevent) {

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the data */
      var self = this,
        list_of_files = '';

      $.each(files, function (index, file) {
        var $img = file.element.find('img:first()');

        var icon_tag = $('<div class="outofthebox-modal-file-icon">');
        if ($img.length > 0) {
          $img.clone().appendTo(icon_tag);
        }
        list_of_files += '<li>' + icon_tag.html() + '<span>' + file.name + '</span></li>';

      });

      /* Build the Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="confirm" type="button" title="' + self.options.str_close_title + '" >' + self.options.str_close_title + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" >' + self.options.str_uploading_failed_msg + '<br/><br/><ul class="files">' + list_of_files + '</ul></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      /* Set the button actions */
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {
        modal_action.close();
      });

      /* Open the Dialog and load the images inside it */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        bodyClass: 'rmodal-open',
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

    _dialogRemoveFromQueue: function (mouseevent) {

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      var self = this;

      /* Build the Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="confirm" type="button" title="' + self.options.str_close_title + '" >' + self.options.str_close_title + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" >' + self.options.str_uploading_failed_in_form + '</div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      /* Set the button actions */
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {
        modal_action.close();
      });

      /* Open the Dialog and load the images inside it */
      var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
        bodyClass: 'rmodal-open',
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

  });

})(jQuery);