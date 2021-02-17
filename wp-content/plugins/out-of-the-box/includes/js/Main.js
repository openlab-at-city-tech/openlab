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
      this.element.on("contextmenu", function (e) {
        /* Disable right clicks */
        return false;
      });

      this.options.main = this;
      this.options.topContainer = this.element.parent();
      this.options.loadingContainer = this.element.find('.loading');

      /* Set the max width for the element */
      this.element.css('width', '100%');

      /* Set the shortcode ID */
      this.options.listtoken = this.element.attr('data-token');
      this.options.account_id = this.element.attr('data-account-id');
      this.options.instance_action = this.element.attr('data-type');

      /* Set initual search term */
      this.searchQuery = this.element.attr('data-query');

      /* Local Cache */
      this.cache = {};

      /* Mobile? */
      this.options.userAgent = navigator.userAgent || navigator.vendor || window.opera;
      this.options.supportTouch = (!!('ontouchstart' in window) && (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(this.options.userAgent))) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
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

      /* RTL */
      this.is_rtl = window.getComputedStyle(document.body, null).getPropertyValue('direction') === 'rtl';

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
        self._initLazyLoad();

        if (self.options.recaptcha !== '') {
          self._initReCaptcha(self);
        }

        if (self.options.topContainer.hasClass('files') || self.options.topContainer.hasClass('gallery') || self.options.topContainer.hasClass('search')) {
          self._initFilebrowser();
        }

        if (self.element.find('.fileupload-box').length > 0) {
          self._initUploadBox();
        }

        if (self.options.topContainer.hasClass('video') || self.options.topContainer.hasClass('audio')) {
          self._initMediaPlayer();
        }
      });

      /* Check if Deep link */
      var url = new URL(window.location);
      var search_params = new URLSearchParams(url.search);
      var deeplink = search_params.get('wpcp_link');

      if (deeplink !== null) {
        var hash_params = JSON.parse(decodeURIComponent(window.atob(deeplink)));
        if (hash_params.source === this.element.attr('data-source')) {
          self.options.topContainer.addClass('initiate')
        }
      }

      /* Initiate if needed even when not in view */
      if (self.options.topContainer.hasClass('initiate')) {
        self.options.topContainer.trigger('inview');
      }

      window.setTimeout(function () {
        self.initated = true;
      }, 2000);

      self.element.trigger('outofthebox-loaded', self);
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

        if (hash_params.source === this.element.attr('data-source')) {
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
      self.element.find('.fileupload-box').OutoftheBoxUploadbox(self.options);
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

      return this._pipeline({
        url: self.options.ajax_url,
        type: "POST",
        dataType: "json",
        data: function (d) {

          d.listtoken = self.options.listtoken;
          d.account_id = self.options.account_id;
          d.lastpath = self.element.attr('data-path');
          d.sort = self.element.attr('data-sort');
          d.deeplink = self.element.attr('data-deeplink');
          d.action = 'outofthebox-get-filelist';
          d._ajax_nonce = self.options.refresh_nonce;
          d.mobile = self.options.mobile;

          if (self.element.attr('data-list') === 'gallery') {
            d.action = 'outofthebox-get-gallery';
            d.type = 'gallery';
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
      var nav_search_box = self.element[0].querySelector('.nav-search');

      if (typeof nav_search_box === 'undefined' || nav_search_box === null) {
        self._initSearchBoxActions(self.element);
        return;
      }

      nav_search_box.click(function (e) {
        e.stopPropagation();
      });

      nav_search_box.nextElementSibling.classList.remove('tippy-content-holder');

      tippy(nav_search_box, {
        trigger: 'click',
        content: nav_search_box.nextElementSibling,
        allowHTML: true,
        placement: (self.is_rtl) ? 'bottom-start' : 'bottom-end',
        appendTo: self.element[0],
        moveTransition: 'transform 0.2s ease-out',
        interactive: true,
        interactiveDebounce: 500,
        theme: 'wpcloudplugins-' + self.options.content_skin,
        onShown: function (instance) {
          // Focus on element
          $('input', instance.popper).focus();
        },
        onCreate: function (instance) {
          // Search Box functionality
          self._initSearchBoxActions($(instance.popper));
        }
      });

    },
    _initSearchBoxActions: function (element) {
      var self = this;

      /* Search Key Up event */
      element.find('.search-input').on("keyup", function (event) {

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
          } else {
            self._getFileList({});
          }
        }
      });

      /* Search submit button event [Search Mode] */
      element.find('.submit-search').click(function () {

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

      element.find('.search-remove').click(function () {
        self.clearSearchBox();
      });

      /* Preset value in search box*/
      element.find('.search-input').val(self.searchQuery);

    },

    clearSearchBox: function (noreset) {
      var self = this;

      self.searchQuery = '';
      self.element.find('.search-input').val('')
      if (typeof noreset === 'undefined') {
        self.element.find('.search-input').trigger('keyup');
      }

    },

    /* Initiate the Settings menu functionality */
    _initNavActionMenu: function () {
      var self = this;
      var actionmenu = self.element[0].querySelector('.nav-gear');

      if (typeof actionmenu === 'undefined' || actionmenu === null) {
        return;
      }

      $(actionmenu).click(function (e) {
        e.stopPropagation();
      });

      actionmenu.nextElementSibling.classList.remove('tippy-content-holder');

      tippy(actionmenu, {
        trigger: 'click',
        content: actionmenu.nextElementSibling,
        maxWidth: 350,
        allowHTML: true,
        placement: (self.is_rtl) ? 'bottom-start' : 'bottom-end',
        appendTo: self.element[0],
        moveTransition: 'transform 0.2s ease-out',
        interactive: true,
        theme: 'wpcloudplugins-' + self.options.content_skin,
        onShow: function (instance) {

          /* Hide menu on mouseleave. Default on click */
          //$(instance.popper).on('mouseleave', function (e) {
          //  instance.hideWithInteractivity(e);
          //});

          self._initNavActionMenuEvents(instance);

        }
      });

    },

    _initNavActionMenuEvents: function (instance) {
      var self = this;
      var $entry = $('<div>').attr('data-id', self.element.attr('data-id'));

      /* Submenu actions */
      $(instance.popper).find('ul').off('click').click(function () {
        $(this).toggleClass("menu-opened")
      });

      /* Layout button is the Switch between table and grid mode */
      $(instance.popper).find('.nav-layout').off('click').click(function () {

        instance.hide();

        if (self.element.attr('data-layout') === 'list') {
          self.element.attr('data-layout', 'grid');
        } else {
          self.element.attr('data-layout', 'list');
        }

        self._getFileList({});
      });

      switch (self.element.attr('data-layout')) {
        case 'list':
          $(instance.popper).find('.nav-layout-grid').closest('li').show();
          $(instance.popper).find('.nav-layout-list').closest('li').hide();
          break;

        case 'grid':
          $(instance.popper).find('.nav-layout-grid').closest('li').hide();
          $(instance.popper).find('.nav-layout-list').closest('li').show();
          break;
      }

      /* Zip button*/
      var selectedboxes = self._helperReadArrCheckBoxes("[data-token='" + self.options.listtoken + "'] input[name='selected-files[]']");

      if (selectedboxes.length === 0) {
        $(instance.popper).find(".selected-files-to-zip").parent().hide();
        $(instance.popper).find(".all-files-to-zip").parent().show();
        $(instance.popper).find(".selected-files-delete").parent().hide();
        $(instance.popper).find(".selected-files-move").parent().hide();
      } else {
        $(instance.popper).find(".selected-files-to-zip").parent().show();
        $(instance.popper).find(".all-files-to-zip").parent().hide();
        $(instance.popper).find(".selected-files-delete").parent().show();
        $(instance.popper).find(".selected-files-move").parent().show();
      }

      var visibleelements = $(instance.popper).find('ul > li').not('.gear-menu-no-options').filter(function () {
        return $(this).css('display') !== 'none';
      });

      if (visibleelements.length > 0) {
        $(instance.popper).find('.gear-menu-no-options').hide();
      } else {
        $(instance.popper).find('.gear-menu-no-options').show();
      }

      $(instance.popper).find('.all-files-to-zip, .selected-files-to-zip').off('click').click(function (event) {

        instance.hide();

        var entries = [];

        if ($(event.target).hasClass('all-files-to-zip')) {
          self.element.find('.select-all').trigger('click');
          entries = self._helperReadArrCheckBoxes("[data-token='" + self.options.listtoken + "'] input[name='selected-files[]']");
        }

        if ($(event.target).hasClass('selected-files-to-zip')) {
          entries = self._helperReadArrCheckBoxes("[data-token='" + self.options.listtoken + "'] input[name='selected-files[]']");
        }

        self._actionDownloadZip(entries, event)

        return;
      });

      /* Add scroll event to nav-upload */
      $(instance.popper).find('.nav-upload').click(function () {
        instance.hide();

        var uploadcontainer = self.element.find('.fileupload-box');

        $('html, body').animate({
          scrollTop: uploadcontainer.offset().top
        }, 1500);
        for (var i = 0; i < 3; i++) {
          uploadcontainer.find('.fileupload-buttonbar').fadeTo('slow', 0.5).fadeTo('slow', 1.0);
        }
      });

      /* Create New Document Event */
      $(instance.popper).find('.newentry').off('click').click(function (e) {
        instance.hide();
        if (typeof self.searchQuery != 'undefined' && self.searchQuery !== '') {
          return false;
        }
        self._actionCreateEntry($entry, $(this).data('mimetype'), e);
      });

      /* Create New Folder Event */
      $(instance.popper).find('.newfolder').off('click').click(function (e) {
        instance.hide();
        if (typeof self.searchQuery != 'undefined' && self.searchQuery !== '') {
          return false;
        }
        self._actionCreateEntry($entry, $(this).data('mimetype'), e);
      });

      /* Select & Deselect all */
      if (self.element.data('selected-all') === true) {
        $(instance.popper).find('.select-all').parent().hide().next().show();
      } else {
        $(instance.popper).find('.deselect-all').parent().hide().prev().show();
      }


      $(instance.popper).find('.select-all').click(function (e) {
        instance.hide();

        self.element.find(".selected-files:checkbox").prop("checked", true);
        self.element.find('.entry:not(".newfolder"):not(".pf")').addClass('isselected');
        self.element.data('selected-all', true)
        $(this).parent().hide().next().show();
      });

      $(instance.popper).find('.deselect-all').click(function (e) {
        instance.hide();

        self.element.find(".selected-files:checkbox").prop("checked", false);
        self.element.find('.entry:not(".newfolder"):not(".pf")').removeClass('isselected');
        self.element.data('selected-all', false)
        $(this).parent().hide().prev().show();
      });

      /* Move multiple files at once */
      $(instance.popper).find('.selected-files-move').click(function (e) {
        instance.hide();

        var entries = self.element.find("input[name='selected-files[]']:checked");

        if (entries.length === 0) {
          return false;
        }

        self._actionMoveEntries(entries);
      });

      /* Delete multiple files at once */
      $(instance.popper).find('.selected-files-delete').click(function (e) {


        var entries = self.element.find("input[name='selected-files[]']:checked");

        if (entries.length === 0) {
          return false;
        }

        self._actionDeleteEntries(entries, e);
      });

      /* Direct Link Folder */
      $(instance.popper).find('.entry_action_deeplink_folder').off('click').click(function (e) {
        instance.hide();
        self._actionCreateDeepLink($entry, e);
      });

      /* Social Share Folder */
      $(instance.popper).find('.entry_action_shortlink_folder').off('click').click(function (e) {
        instance.hide();
        self._actionShareEntry($entry, e);
      });
    },

    /* Initiate the Sortings menu functionality */
    _initSortMenu: function () {
      var self = this;

      var sortmenu = self.element[0].querySelector('.nav-sort');

      if (typeof sortmenu === 'undefined' || sortmenu === null) {
        return;
      }

      $(sortmenu).click(function (e) {
        e.stopPropagation();
      });

      sortmenu.nextElementSibling.classList.remove('tippy-content-holder');

      tippy(sortmenu, {
        trigger: 'click',
        content: sortmenu.nextElementSibling,
        maxWidth: 350,
        allowHTML: true,
        placement: (self.is_rtl) ? 'bottom-start' : 'bottom-end',
        appendTo: self.element[0],
        moveTransition: 'transform 0.2s ease-out',
        interactive: true,
        theme: 'wpcloudplugins-' + self.options.content_skin,
        onShow: function (instance) {
          self._initSortEvents(instance);
        }
      });

    },

    _initSortEvents: function (instance) {

      var self = this;

      $(instance.popper).find('.nav-sorting-field').off('click').click(function () {
        $(instance.popper).find('.nav-sorting-field').removeClass('sort-selected');
        $(this).addClass('sort-selected');

        var sort_str = $(instance.popper).find('.nav-sorting-field.sort-selected').data('field') + ":" + $(instance.popper).find('.nav-sorting-order.sort-selected').data('order');
        self.element.attr('data-sort', sort_str);

        self._getFileList({});
        tippy.hideAll()
      });

      $(instance.popper).find('.nav-sorting-order').off('click').click(function () {
        $(instance.popper).find('.nav-sorting-order').removeClass('sort-selected');
        $(this).addClass('sort-selected');

        var sort_str = $(instance.popper).find('.nav-sorting-field.sort-selected').data('field') + ":" + $(instance.popper).find('.nav-sorting-order.sort-selected').data('order');
        self.element.attr('data-sort', sort_str);

        self._getFileList({});
        tippy.hideAll()
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

        self.element.removeClass('-is-virtual-folder');
        if (json.virtual === true) {
          self.element.addClass('-is-virtual-folder');
        }

      }

      self.element.find('.wpcp-breadcrumb').one('inview', function (event, isInView) {
        self.renderBreadCrumb();
      });

      self.element.find('.nav-refresh i').removeClass('fa-spin');

      if (self.element.attr('data-layout') === 'grid') {
        self.element.find('.entry_thumbnail img.hidden').removeClass('hidden');
      }

      self.lazyload.update();

      if (self.element.hasClass('gridgallery')) {
        self.renderContentForGallery();
        self._initLoadMore();
      } else {
        self.renderContentForBrowser();
      }

      self.lazyload.update();

      /* Hover Events */
      self.element.find('.entry').off('hover').hover(
        function () {
          $(this).addClass('hasfocus');
        },
        function () {
          $(this).removeClass('hasfocus');
        }
      ).on('mouseover', function () {
        $(this).addClass('hasfocus');
      }).off('click').click(function () {
        /* CheckBox Event */
        //$(this).find('.entry_checkbox input[type="checkbox"]').trigger('click');
      }).on("contextmenu", function (e) {
        /* Disable right clicks */
        $(this).find('.entry-action-menu-button').trigger('click');
        return false;
      });

      /* Folder Click events */
      self.element.find('.folder, .image-folder').off('click').click(function (e) {

        if ($(this).hasClass('isdragged') || $(this).hasClass('newfolder')) {
          return false;
        }

        e.stopPropagation();
        self.clearSearchBox(false);

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
      self.element.find('.newfolder').off('click').click(function (e) {
        if (typeof self.searchQuery != 'undefined' && self.searchQuery !== '') {
          return false;
        }
        self._actionCreateEntry($(this), $(this).data('mimetype'), e);
      });

      /* Entry Click Events*/
      self.element.find('.entry.file').off('click').click(function (e) {
        if (self.options.instance_action === 'embedded' || self.options.instance_action === 'links') {
          $(this).find('.entry_checkbox :checkbox').trigger('click');
          e.stopPropagation();
        } else if (self.options.instance_action === 'shortcode') {

        } else {
          var link = $(this).find(".entry_link");
          if (self.options.supportTouch && link.hasClass('ilightbox-group')) {
            link.trigger('itap');
          } else {
            link[0].click()
          }
        }
      });

      self.element.find('.entry').each(function () {
        self._initEntryActions($(this));
      })

      self.element.data('selected-all', false);

      self.element.find('.entry_checkbox').off('click').click(function (e) {
        e.stopPropagation();
        return true;
      });

      self.element.find('.entry_checkbox :checkbox').click(function (e) {
        if ($(this).prop('checked')) {
          $(this).closest('.entry').addClass('isselected');
        } else {
          $(this).closest('.entry').removeClass('isselected');
        }
        e.stopPropagation();
      });


      self._initActionMenu();
      self._initDescriptionBox();
      self._initLightbox();
      if (self.element.attr('data-layout') === 'list') {
        self._initThumbnailsPopup();
      }
      self._initMove();
      self._initLinkEvent();
      self._initScrollToTop();

      if (typeof grecaptcha !== 'undefined' && self.options.recaptcha !== '' && self.recaptcha_passed === false) {
        self._disableDownloadLinks();
      }

      self.options.loadingContainer.fadeOut(300);

      if (typeof self.option.focus_id !== 'undefined' && self.option.focus_id !== null) {
        var entry = self.element.find('.entry[data-id="' + self.option.focus_id + '"]');
        entry.addClass('hasfocus');

        if (entry.find('a.ilightbox-group').length > 0) {
          entry.find('a.ilightbox-group').trigger('click');
        }

        $('html, body').animate({
          scrollTop: entry.offset().top
        }, 1500);

        self.option.focus_id = null;
      }

      self.element.trigger('content-loaded', self);

    },

    renderContentForBrowser: function () {
      var self = this;

      var $layout = self.element.find('.files');
      switch (this.element.attr('data-layout')) {
        case 'list':
          self.element.removeClass('oftb-grid').addClass('oftb-list');
          break;

        case 'grid':
          self.element.removeClass('oftb-list').addClass('oftb-grid');

          /* Update files to fit in container */
          self.fitEntriesInContainer($layout.find('.folders-container '), 250);
          self.fitEntriesInContainer($layout.find('.files-container '), 250);
          break;
      }

      $layout.fadeTo(0, 0).delay(100).fadeTo(200, 1);
    },

    renderContentForGallery: function () {
      var self = this;

      var image_container = self.element.find('.image-container');
      var image_collage = self.element.find(".image-collage");

      image_container.hover(
        function () {
          $(this).find('.image-rollover').stop().animate({
            opacity: 1
          }, 400);
        },
        function () {
          $(this).find('.image-rollover').stop().animate({
            opacity: 0
          }, 400);
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

      self.element.find('.image-folder').off('mousemove').mousemove(function (e) {

        var thumbnails = $(this).find('.folder-thumb');
        var relX = e.offsetX / e.currentTarget.offsetWidth;
        var show_n = Math.ceil(relX * thumbnails.length) - 1;

        thumbnails.filter(':gt(0)').stop(true).fadeOut().filter(':eq(' + show_n + ')').stop(true).fadeIn();
      });

      self.element.find('.image-folder').off('mouseleave').mouseleave(function () {
        $(this).find('.folder-thumb:gt(0)').stop(true).fadeOut();
      });

    },

    /* Load more images */
    _initLoadMore: function () {
      var self = this;
      var last_visible_image = self.element.find(".image-container.entry:not(.hidden):last()");
      var load_per_time = self.element.attr('data-loadimages');

      last_visible_image.one('inview', function (event, isInView) {
        var images = self.element.find(".image-container:hidden:lt(" + load_per_time + ")");

        if (images.length === 0) {
          return;
        }

        images.fadeIn(500).removeClass('hidden').find('img').removeClass('hidden');

        self._initLoadMore();
      });
    },

    _initScrollToTop: function () {
      var self = this;

      this.element.find('.scroll-to-top').off('click').click(function () {
        $('html, body').animate({
          scrollTop: self.element.offset().top
        }, 1500);
      });

      $(window).off('scroll', null, self._positionScrollToTop).on('scroll', null, {}, self._positionScrollToTop);
    },

    _initReCaptcha: function (self) {

      if (typeof grecaptcha === 'undefined') {
        setTimeout(function () {
          self._initReCaptcha(self);
        }, 1000);
        return false;
      }

      grecaptcha.ready(function () {
        grecaptcha.execute(self.options.recaptcha, {
          action: 'wpcloudplugins'
        }).then(function (token) {

          $.ajax({
            type: "POST",
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
            var positionright = '50%';

            if (bottomContainer > bottomWindow) {
              positionbutton = bottomWindow - positionContainer.top - 30;
              positionright = 0;
            }

            $scroll_to_top_container.stop().animate({
              top: Math.round(positionbutton - 50),
              right: positionright
            });
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
        zIndex: 999999,
        cursor: 'move',
        cursorAt: {
          top: 10,
          left: 10
        },
        containment: self.element,
        helper: function () {
          var selected = self.element.find('.moveable .entry_checkbox input:checked').closest('.moveable');
          if (selected.length === 0) {
            selected = $(this);
          }
          var container = $('<div/>').attr('id', 'dragging_container');
          container.append(selected.clone());
          return container;
        },
        distance: 10,
        delay: 50,
        start: function (event, ui) {
          $(this).addClass('isdragged');
        },
        stop: function (event, ui) {
          var $element = $(this);
          setTimeout(function () {
            $element.removeClass('isdragged');
          }, 100);
        }
      });
      this.element.find('.folder, .image-folder').droppable({
        accept: self.element.find('.moveable'),
        activeClass: "ui-state-hover",
        hoverClass: "ui-state-active",
        tolerance: "pointer",
        drop: function (event, ui) {
          self._actionMoveEntry(ui.helper.children(), $(this));
        }
      });
    },

    /* Button Events for linking folders */
    _initLinkEvent: function () {
      var self = this;

      self.element.find('.entry_linkto').off('click').click(function (e) {

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

        $.ajax({
          type: "POST",
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

      self.element.find('.entry_woocommerce_link').off('click').click(function (e) {

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
    _initEntryActions: function ($entry) {
      var self = this;

      /* Preview Event */
      $entry.find('.entry_action_view').off('click').click(function (e) {
        tippy.hideAll()
        self._actionPreviewEntry($entry, e);
      });

      /* Download Event */
      $entry.find('.entry_action_download').off('click').click(function (e) {
        tippy.hideAll()
        e.stopPropagation();

        if ($entry.hasClass('folder')) {
          self._actionDownloadZip([$entry.attr('data-id')], e)
        } else {
          return self._actionDownloadEntry($entry, e);
        }

      });

      /* Save As Event */
      $entry.find('.entry_action_export').off('click').click(function (e) {
        tippy.hideAll()
        e.stopPropagation();
        return self._actionExportEntry($entry, e);
      });

      /* Social Share Event */
      $entry.find('.entry_action_shortlink').off('click').click(function (e) {
        e.stopPropagation();
        tippy.hideAll()
        self._actionShareEntry($entry, e);
      });

      /* Delete Event*/
      $entry.find('.entry_action_delete').off('click').click(function (e) {
        tippy.hideAll()
        $entry.find(".selected-files:checkbox").prop("checked", true);
        var entries = self.element.find("input[name='selected-files[]']:checked");
        self._actionDeleteEntries(entries, e);
      });

      /* Move Event*/
      $entry.find('.entry_action_move').off('click').click(function (e) {
        tippy.hideAll()
        $entry.find(".selected-files:checkbox").prop("checked", true);
        var entries = self.element.find("input[name='selected-files[]']:checked");
        self._actionMoveEntries(entries, e);
      });

      /* Copy Event*/
      $entry.find('.entry_action_copy').off('click').click(function (e) {
        tippy.hideAll()
        self._actionCopyEntry($entry, e);
      });

      /* Rename Event */
      $entry.find('.entry_action_rename').off('click').click(function (e) {
        tippy.hideAll()
        self._actionRenameEntry($entry, e);
      });

      /* Description Box Event */
      $entry.find('.entry_action_description').off('click').click(function (e) {
        tippy.hideAll()
        self._actionEditDescriptionEntry($entry, e);
      });

      /* DeepLink Event */
      $entry.find('.entry_action_deeplink').off('click').click(function (e) {
        e.stopPropagation();
        tippy.hideAll()
        self._actionCreateDeepLink($entry, e);
      });
    },

    /* Bind event which shows the action menu */
    _initActionMenu: function () {
      var self = this;
      var menus = self.element[0].querySelectorAll('.entry .entry-action-menu-button')

      $(menus).click(function (e) {
        e.stopPropagation();
      });

      tippy(menus, {
        trigger: 'click',
        content: function (reference) {
          return $(reference).find('.tippy-content-holder').clone(true).get(0);
        },
        maxWidth: 350,
        allowHTML: true,
        placement: (self.is_rtl) ? 'top-start' : 'top-end',
        appendTo: self.element[0],
        moveTransition: 'transform 0.2s ease-out',
        interactive: true,
        theme: 'wpcloudplugins-' + self.options.content_skin,
        onShow: function (instance) {

          $(instance.popper).find('.tippy-content-holder').removeClass('tippy-content-holder');

          var $entry = $(instance.reference).closest('.entry');

          $entry.addClass('hasfocus').addClass('popupopen');

          /* Hide menu on mouseleave. Default on click */
          //$(instance.popper).on('mouseleave', function (e) {
          //  instance.hideWithInteractivity(e);
          //});

        },
        onHide: function (instance) {
          $(instance.reference).closest('.entry').removeClass('hasfocus').removeClass('popupopen');
        },

      });

    },

    _initDescriptionBox: function () {
      var self = this;

      var description_tooltips = self.element[0].querySelectorAll('.entry .entry-description-button');

      $(description_tooltips).click(function (e) {
        e.stopPropagation();
      });

      tippy(description_tooltips, {
        trigger: 'mouseenter focus click',
        content: function (reference) {
          return reference.querySelector('.tippy-content-holder').innerHTML
        },
        maxWidth: 350,
        allowHTML: true,
        placement: 'bottom',
        delay: [100, null],
        offset: [0, -10],
        interactiveBorder: 15,
        appendTo: self.element[0],
        moveTransition: 'transform 0.2s ease-out',
        interactive: true,
        theme: 'wpcloudplugins-' + self.options.content_skin,
        onShow: function (instance) {
          $(instance.reference).closest('.entry').addClass('popupopen');
        },
        onHide: function (instance) {
          $(instance.reference).closest('.entry').removeClass('popupopen');
        },
      });
    },

    /* Make the BreadCrumb responsive */
    renderBreadCrumb: function () {
      var self = this;
      var $breadcrumb_element = self.element.find('.wpcp-breadcrumb');

      $breadcrumb_element.asBreadcrumbs('destroy');

      $breadcrumb_element.asBreadcrumbs({
        namespace: "wpcp",
        toggleIconClass: "fas fa-caret-down",
        dropdownMenuClass: "tippy-content",

        dropdownItem: function dropdownItem(classes, label, href) {
          if (!href) {
            return ('<li class="' + classes.dropdownItemClass + ' ' + classes.dropdownItemDisableClass + '"><a href="#"><i class="fas fa-folder fa-lg"></i>  ' + label + '</a></li>');
          }
          return ('<li class="' + classes.dropdownItemClass + '"><a href="' + href + '"><i class="fas fa-folder fa-lg"></i>  ' + label + '</a></li>');
        }
      });

      $breadcrumb_element.find('.tippy-content li').click(function () {
        $breadcrumb_element.find('li a.folder[href="' + $(this).find('a').attr("href") + '"]').trigger('click');
        $breadcrumb_element.find('.wpcp-dropdown').removeClass('dropdown-open');
      });

      $breadcrumb_element.find('.wpcp-toggle').click(function () {
        $breadcrumb_element.find('.wpcp-dropdown').addClass('dropdown-open');
      });

      $(document).mouseup(function (e) {
        var container = $breadcrumb_element.find('.tippy-content');

        if (!container.is(e.target) && container.has(e.target).length === 0) {
          container.parent().removeClass('open');
        }
      });
    },

    /* Bind event which shows popup with thumbnail on hover in file list */
    _initThumbnailsPopup: function () {
      var self = this;
      var thumbnail_tooltips = self.element[0].querySelectorAll('.entry[data-tooltip]');

      tippy(thumbnail_tooltips, {
        content: function (reference) {
          return reference.querySelector('.entry_thumbnail-view-center')
        },
        maxWidth: (self.options.mobile) ? 128 : 256,
        allowHTML: true,
        placement: 'auto',
        delay: [250, null],
        offset: [0, 10],
        appendTo: self.element[0],
        moveTransition: 'transform 0.2s ease-out',
        interactive: true,
        theme: 'wpcloudplugins-' + self.options.content_skin,
        onHide: function (instance) {
          $(instance.reference).removeClass('hasfocus');
        },
      });
    },

    /* Lazy Loading Images */
    _initLazyLoad: function () {
      var self = this;

      self.lazyload = new LazyLoad({
        data_src: (window.devicePixelRatio > 1) ? "src-retina" : "src",
        elements_selector: "img.preloading",
        threshold: 300,
        unobserve_entered: true,
        use_native: true,
        class_applied: "wpcp-lazy-applied",
        class_loading: "wpcp-lazy-loading",
        class_loaded: "wpcp-lazy-loaded",
        class_error: "wpcp-lazy-error",

        callback_loaded: function (element) {
          $(element).removeClass('preloading').removeAttr('data-src');
          $(element).prev('.preloading').remove();
        },

        callback_load: function (element) { // Callback when site is loading old verison of LazyLoad
          $(element).removeClass('preloading').removeAttr('data-src');
          $(element).prev('.preloading').remove();
        },

        callback_error: function (element) {
          if (typeof $(element).attr('data-src-backup') !== typeof undefined && $(element).attr('data-src-backup') !== false) {
            element.src = $(element).attr('data-src-backup');
          } else {
            element.src = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=";
          }

          $(element).removeAttr('data-src');
          $(element).prev('.preloading').remove();
        }
      });

    },

    /**
     * Renders the General Menu in the Navigation Bar
     */
    renderNavMenu: function () {
      var self = this;

      /* Fire up the search functionality*/
      this._initSearchBox();
      this._initNavActionMenu();
      this._initSortMenu();
      this._initAccountSelector();

      /* Refresh button does a hard refresh for the current folder*/
      this.element.find('.nav-refresh').off('click');
      this.element.find('.nav-refresh').click(function () {
        self.clearSearchBox();
        self.options.forceRefresh = true;
        self._getFileList({});
      });

      /* Event for nav-home button */
      this.element.find('.nav-home').off('click');
      this.element.find('.nav-home').click(function () {
        self.clearSearchBox();
        self.element.attr('data-path', self.element.attr('data-org-path'));
        self._getFileList({
          'OutoftheBoxpath': self.element.attr('data-org-path')
        });
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
          pauseOnHover: false,
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
            self._renderLightboxCaption(api);
            $('.ilightbox-holder').addClass('OutoftheBox');
            $('.ilightbox-holder .oftb-hidepopout').remove();


            if (self.element.attr('data-popout') === '0') {
              $('.ilightbox-holder').find('.oftb-embedded').after('<div class="oftb-hidepopout">&nbsp;</div>');
            }

            var element = $('.ilightbox-holder').find('iframe').addClass('oftb-embedded');
            self._helperIframeFix(element);
          },
          onBeforeChange: function (api) {
            self._renderLightboxCaption(api);
            /* Stop all HTML 5 players */
            var players = $('.ilightbox-holder video, .ilightbox-holder audio');
            $.each(players, function (i, element) {
              if (element.paused === false) {
                element.pause();
              }
            });
          },
          onAfterChange: function (api) {
            self._renderLightboxCaption(api);

            /* Auto Play new players*/
            var players = api.currentElement.find('video, audio');
            $.each(players, function (i, element) {
              if (element.paused) {
                element.play();
              }
            });
          },
          onRender: function (api, position) {
            self._renderLightboxCaption(api);

            var $video_html5_players = $('.ilightbox-holder').find('video, audio');
            $.each($video_html5_players, function (i, video_html5_player) {
              var $video_html5_player = $(this);
              $(this).find('source').attr('src', $video_html5_player.find('source').attr('data-src'));
            });
          },
          onShow: function (api) {
            self._renderLightboxCaption(api);

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

            new LazyLoad({
              //container: $(".ilightbox-thumbnail").get(0),
              data_src: (window.devicePixelRatio > 1) ? "src-retina" : "src",
              unobserve_entered: true,
              use_native: true,
              elements_selector: ".ilightbox-thumbnail img.preloading",
              class_applied: "wpcp-lazy-applied",
              class_loading: "wpcp-lazy-loading",
              class_loaded: "wpcp-lazy-loaded",
              class_error: "wpcp-lazy-error",

              callback_loaded: function (element) {
                $(element).removeClass('preloading preloading-lightbox-thumbnail');
                $(element).prev('.preloading').remove();

                $(element).parent().data({
                  naturalWidth: element.width,
                  naturalHeight: element.height
                });

                iL.positionThumbnails(null, null, null);
              },

              callback_load: function (element) { // Callback when site is loading old verison of LazyLoad
                $(element).removeClass('preloading preloading-lightbox-thumbnail');
                $(element).prev('.preloading').remove();

                $(element).parent().data({
                  naturalWidth: element.width,
                  naturalHeight: element.height
                });

                iL.positionThumbnails(null, null, null);
              },

            });

            $('.ilightbox-container .oftb-hidepopout').on("contextmenu", function (e) {
              return false;
            });

            $('.ilightbox-container .ilightbox-wrapper, .ilightbox-container img, .ilightbox-container video, .ilightbox-container audio').on("contextmenu", function (e) {
              return (self.options.lightbox_rightclick === 'Yes');
            });

            /* Pinch, Pan & Zoom */
            $('.ilightbox-container img').each(function () {
              var $image = $(this);
              if ($image.closest('.panzoom-container').length === 0) {
                $image.wrap('<div class="panzoom-container"></div>');

                var panzoom = Panzoom(this, {
                  cursor: 'auto',
                  minScale: 1,
                  panOnlyWhenZoomed: true,
                  contain: 'outside'
                })
                $(this).parent().get(0).addEventListener('wheel', function (event) {
                  //if (!event.shiftKey) return
                  event.stopPropagation()
                  panzoom.zoomWithWheel(event);
                });
              }
            })

            /* Log preview event if needed */
            var $source = api.currentElement.find('[src]')
            if ($source.length > 0 && $source.data('logged') !== 1) {
              var entry_id = $('a[href="' + $source.attr('src') + '"]').closest('.entry').data('id');
              $source.data('logged', 1);
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

    _renderLightboxCaption: function (api) {
      var self = this;

      if (api.currentElement.find('.entry-info').length > 0) {
        return;
      }

      if (self.options.topContainer.hasClass('files') || self.options.lightbox_showcaption === 'mouseenter') {
        $('.ilightbox-container').addClass('caption-visible');
      }

      var caption_div = api.currentElement.find('.ilightbox-caption');
      if (caption_div.length === 0) {
        caption_div = $('<div class="ilightbox-caption panzoom-exclude"/>');
        caption_div.appendTo(api.currentElement.children().first())
      }

      /* Find Entry by ID */
      var entry_id = api.currentElement.find('span[data-id]').data('id');
      if (typeof entry_id !== 'undefined') {
        var menu = self.element.find('.entry[data-id="' + entry_id + '"]').find('.entry-info');
      } else {
        /* Find Entry by source */
        var source = api.currentElement.find('[src]').attr('src')
        var menu = self.element.find('a[href="' + source + '"]').closest('.entry').find('.entry-info');
      }

      menu.clone(true).appendTo(caption_div);

      if (api.currentElement.find('.entry-action-menu-button').length) {
        tippy(api.currentElement.find('.entry-action-menu-button').get(0), {
          trigger: 'click',
          content: function (reference) {
            return $(reference).find('.tippy-content-holder').clone(true).get(0);
          },
          maxWidth: 350,
          allowHTML: true,
          placement: (self.is_rtl) ? 'bottom-start' : 'bottom-end',
          appendTo: api.currentElement.get(0),
          moveTransition: 'transform 0.2s ease-out',
          interactive: true,
          theme: 'wpcloudplugins-' + self.options.content_skin,
          onShow: function (instance) {
            $(instance.popper).find('.tippy-content-holder').removeClass('tippy-content-holder');

          },
          onHide: function (instance) {},
        });
      }

      if (api.currentElement.find('.entry-description-button').length) {
        tippy(api.currentElement.find('.entry-description-button').get(0), {
          trigger: 'mouseenter focus click',
          content: function (reference) {
            return reference.querySelector('.tippy-content-holder').innerHTML
          },
          maxWidth: 350,
          allowHTML: true,
          placement: (self.is_rtl) ? 'bottom-start' : 'bottom-end',
          delay: [100, null],

          moveTransition: 'transform 0.2s ease-out',
          interactive: true,
          theme: 'wpcloudplugins-' + self.options.content_skin,
          onShow: function (instance) {
            $(instance.reference).closest('.entry').addClass('hasfocus');
          },
          onHide: function (instance) {
            $(instance.reference).closest('.entry').removeClass('hasfocus');
          },
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

      var datapath, is_folder, entry_id, entry_name, entryimg;

      if (entry.hasClass('entry') === false) { // Nav menu -> Direct link button
        // Nav menu -> Direct link button
        datapath = self.element.attr('data-path');
        entry_name = self.element.find('.wpcp-breadcrumb li:last').text();
        is_folder = true;
      } else {
        entry_name = entry.attr('data-name');
        entry_id = entry.attr('data-id');
        entryimg = entry.find('img').attr('src');
        is_folder = entry.hasClass('folder') || entry.hasClass('image-folder');
      }

      /* Generate Direct link */
      var hash_params = {
        'source': self.element.attr('data-source'),
        'account_id': self.element.attr('data-account-id'),
        'last_path': (is_folder) ? '' : self.element.attr('data-path'),
        'focus_id': entry_id
      };

      var hash = window.btoa(encodeURIComponent(JSON.stringify(hash_params)));

      var url = new URL(window.location);
      var search_params = new URLSearchParams(url.search);
      search_params.set('wpcp_link', hash);
      url.search = search_params.toString();

      sendGooglePageView('Create deeplink', entry_name);

      /* Modal */
      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Delete Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_close_title + '" >' + self.options.str_close_title + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" ><input type="text" class="direct-link-url" value="' + url.toString() + '" style="width: 98%;" readonly/><div class="shareon outofthebox-shared-social"></div></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      var share_container = $('#outofthebox-modal-action .outofthebox-shared-social');
      share_container.attr('data-url', url);
      share_container.attr('data-title', entry_name + ' | ');

      $.each(self.options.share_buttons, function (index, platform) {
        share_container.append('<a class="' + platform + '" title="' + self.options.str_shareon + ' ' + platform.charAt(0).toUpperCase() + platform.slice(1) + '" data-media="' + entryimg + '" data-imageurl="' + entryimg + '" data-image="' + entryimg + '"></a>');
      });

      shareon();

      var email = $('.shareon .email').attr('href', 'mailto:?subject=' + entry_name + ' | ' + '&body=' + url);

      var clipboard = new ClipboardJS('.shareon .clipboard', {
        text: function (trigger) {
          return $('.direct-link-url').val();
        }
      });

      clipboard.on('success', function (e) {
        $('.shareon .clipboard').addClass('clipboard-check');
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

      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');

      return false;
    },

    _logEvent: function (type, id, data) {
      var self = this;

      if (self.options.log_events === "0") {
        return false;
      }

      if (typeof data === 'undefined') {
        data = {};
      }

      $.ajax({
        type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-event-log',
          account_id: self.options.account_id,
          listtoken: self.options.listtoken,
          type: type,
          id: id,
          data: data,
          _ajax_nonce: self.options.log_nonce
        }
      });
    },

    /**
     * Preview an entry
     */
    _actionPreviewEntry: function (entry, mouseevent) {
      var self = this;

      var link = entry.find(".entry_link");
      if (self.options.supportTouch && link.hasClass('ilightbox-group')) {
        link.trigger('itap');
      } else {
        link[0].click()
      }
    },

    /**
     * Download an entry
     * @param {Object} entry
     * @param {string} mimetype
     */
    _actionDownloadEntry: function (entry, mouseevent) {
      var self = this;

      sendGooglePageView('Download', entry.attr('data-filename'));

      var $processor_icon = $('<div><i class="fas fa-cog fa-spin fa-1x fa-fw"></i></div>').css({
        'margin-right': '5px',
        'display': 'inline-grid'
      }).delay(5000).fadeOut('slow', function () {
        $(this).remove();
      });

      entry.find('.entry-info-name span').prepend($processor_icon);

      // Delay a few milliseconds for Tracking event
      setTimeout(function () {

        return true;
      }, 300);

    },

    /**
     * Download files as ZIP
     */
    _actionDownloadZip: function (entries, mouseevent) {
      var self = this;

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Dialog */
      var modalbuttons = '';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" ><div class="zip-loading-bar label-center" data-preset="circle" data-value="0"></div><h3 class="zip-status">' + self.options.str_processing + '</h3></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      self.zip_download_bar = new ldBar(modalbody.find('.zip-loading-bar').get(0), {
        "preset": "circle"
      });

      self._zipDownload(entries);

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

      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');

      return false;
    },

    _zipDownload: function (entries, request_id) {
      var self = this;

      if (typeof request_id === 'undefined') {
        request_id = new Date().getTime();
      }

      var data = {
        action: 'outofthebox-create-zip',
        type: 'do-zip',
        request_id: request_id,
        account_id: self.options.account_id,
        listtoken: self.options.listtoken,
        lastFolder: self.element.attr('data-id'),
        _ajax_nonce: self.options.createzip_nonce,
        files: entries
      }

      self._helperDownloadUrlInline(self.options.ajax_url + "?" + $.param(data))
      setTimeout(function () {
        self._watchZipProgres(request_id);
      }, 2000);

    },

    _watchZipProgres: function (request_id) {
      var self = this;

      self._getZipProgress(request_id).then(function (response) {

        if ($.isEmptyObject(response) || typeof response.status === 'undefined' || response.status.progress === 'failed') {
          $('#outofthebox-modal-action').remove();
          return;
        }

        $('.zip-status').html(response.status.progress_str);
        self.zip_download_bar.set(response.status.percentage);

        if (response.status.progress === 'finished') {
          $('#outofthebox-modal-action').remove();
          return
        }

        setTimeout(function () {
          self._watchZipProgres(request_id);
        }, 2000);

      })
    },

    _getZipProgress: function (request_id) {
      var self = this;

      return $.ajax({
        type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-create-zip',
          type: 'get-progress',
          request_id: request_id,
          account_id: self.options.account_id,
          listtoken: self.options.listtoken,
          lastFolder: self.element.attr('data-id'),
          _ajax_nonce: self.options.createzip_nonce,
        },
        dataType: 'json',
      });
    },

    /**
     * Share an Entry
     */
    _actionShareEntry: function (entry, mouseevent) {
      var self = this;

      var account_id = self.element.attr('data-account-id');
      var datapath = entry.closest("ul").attr('data-path');
      var dataurl = entry.attr('data-url');
      var dataname = entry.attr('data-name');
      var entryimg = entry.find('img').attr('src');

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

      $.ajax({
        type: "POST",
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

              response.link = response.link.replace('?dl=1', '');

              $('.outofthebox-modal-body').append('<input type="text" class="shared-link-url" value="' + response.link + '" style="width: 98%;" readonly/><div class="shareon outofthebox-shared-social"></div>');
              sendGooglePageView('Create shared link', dataname);

              var share_container = $('#outofthebox-modal-action .outofthebox-shared-social');
              share_container.attr('data-url', response.link);
              share_container.attr('data-title', dataname + ' | ');

              $.each(self.options.share_buttons, function (index, platform) {
                share_container.append('<a class="' + platform + '" title="' + self.options.str_shareon + ' ' + platform.charAt(0).toUpperCase() + platform.slice(1) + '" data-media="' + entryimg + '" data-imageurl="' + entryimg + '" data-image="' + entryimg + '"></a>');
              });

              shareon();

              var email = $('.shareon .email').attr('href', 'mailto:?subject=' + dataname + ' | ' + '&body=' + response.link);

              var clipboard = new ClipboardJS('.shareon .clipboard', {
                text: function (trigger) {
                  return $('.shared-link-url').val();
                }
              });

              clipboard.on('success', function (e) {
                $('.shareon .clipboard').addClass('clipboard-check');
                e.clearSelection();
              });

              clipboard.on('error', function () {
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

      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');

      return false;
    },
    /**
     * Open a Dialog for creating a new Entry with a certain mimetype
     * @param {String} template_name
     * @param {String} mimetype
     */
    _actionCreateEntry: function (entry, mimetype, mouseevent) {
      var self = this;



      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();
      /* Build the Rename Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_cancel_title + '" >' + self.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="rename" type="button" title="' + self.options.str_addnew_title + '" >' + self.options.str_addnew_title + '</button>';
      var create_entry_input = '<input type="text" id="outofthebox-modal-create-entry-input" name="outofthebox-modal-create-entry-input" value="" style="width:100%"/>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" >' + self.options.str_addnew_name + ': <br/>' + create_entry_input + '</div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');

      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);
      /* Set the button actions */

      $('#outofthebox-modal-action #outofthebox-modal-create-entry-input').off('keyup');
      $('#outofthebox-modal-action #outofthebox-modal-create-entry-input').on("keyup", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').trigger('click');
        }
      });
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {

        var filename = $('#outofthebox-modal-create-entry-input').val();
        /* Check if there are illegal characters in the new name*/
        if (/[<>:"/\\|?*]/g.test($('#outofthebox-modal-create-entry-input').val())) {
          $('#outofthebox-modal-action .outofthebox-modal-error').remove();
          $('#outofthebox-modal-create-entry-input').after('<div class="outofthebox-modal-error">' + self.options.str_rename_failed + '</div>');
          $('#outofthebox-modal-action .outofthebox-modal-error').fadeIn();
        } else {

          var data = {
            action: 'outofthebox-create-entry',
            mimetype: mimetype,
            name: encodeURIComponent(filename),
            _ajax_nonce: self.options.createentry_nonce
          };
          self._actionDoModifyEntry(data);

          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');
        }

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

    /**
     * Create a Dialog for copying an entry
     */
    _actionCopyEntry: function (entry, mouseevent) {

      var self = this;

      var dataid = entry.attr('data-id');
      var dataname = entry.attr('data-name');
      var dataurl = entry.attr('data-url');

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Rename Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_cancel_title + '" >' + self.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="rename" type="button" title="' + self.options.str_copy_title + '" >' + self.options.str_copy_title + '</button>';
      var copynameinput = '<input id="outofthebox-modal-rename-input" name="outofthebox-modal-rename-input" type="text" value="' + dataname + '" style="width:100%"/>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" >' + self.options.str_copy + '<br/>' + copynameinput + '</div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');

      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);
      /* Set the button actions */

      $('#outofthebox-modal-action #outofthebox-modal-rename-input').off('keyup');
      $('#outofthebox-modal-action #outofthebox-modal-rename-input').on("keyup", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').trigger('click');
        }
      });
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {

        var filename = $('#outofthebox-modal-rename-input').val();
        /* Check if there are illegal characters in the new name*/
        if (/[<>:"/\\|?*]/g.test($('#outofthebox-modal-rename-input').val())) {
          $('#outofthebox-modal-action .outofthebox-modal-error').remove();
          $('#outofthebox-modal-rename-input').after('<div class="outofthebox-modal-error">' + self.options.str_rename_failed + '</div>');
          $('#outofthebox-modal-action .outofthebox-modal-error').fadeIn();
        } else {

          var data = {
            action: 'outofthebox-copy-entry',
            OutoftheBoxpath: dataurl,
            newname: encodeURIComponent(filename),
            _ajax_nonce: self.options.copy_nonce
          };
          self._actionDoModifyEntry(data);

          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');
        }

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

    /**
     * Create a Dialog for renaming an entry
     * @param {Object} entry
     */
    _actionRenameEntry: function (entry, mouseevent) {

      var self = this;

      var datapath = entry.closest("ul").attr('data-path');
      var dataname = entry.attr('data-name');
      var dataurl = entry.attr('data-url');

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

      $('#outofthebox-modal-action #outofthebox-modal-rename-input').off('keyup');
      $('#outofthebox-modal-action #outofthebox-modal-rename-input').on("keyup", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
          $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').trigger('click');
        }
      });
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
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

    /**
     * Create a Dialog for changing the description of an entry
     * @param {Object} entry
     */
    _actionEditDescriptionEntry: function (entry, mouseevent) {

      var self = this;


      var dataid = entry.attr('data-id');
      var account_id = self.element.attr('data-account-id');
      var description = entry.find('.description-text').html();

      if (typeof description === 'undefined') {
        description = '';
      }

      /* Close any open modal windows */
      $('#outofthebox-modal-action').remove();

      /* Build the Rename Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="button outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_cancel_title + '" >' + self.options.str_cancel_title + '</button>';
      modalbuttons += '<button class="button outofthebox-modal-confirm-btn" data-action="editdescription" type="button" title="' + self.options.str_save_title + '" >' + self.options.str_save_title + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" ><textarea id="outofthebox-modal-description-input" name="outofthebox-modal-description-input" style="width:100%" rows="8" placeholder="' + self.options.str_add_description + '"></textarea></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"></div></div></div>');

      $('body').append(modaldialog);
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      /* Fill Textarea */
      $('#outofthebox-modal-description-input').val(description.replace(/<br\s?\/?>/g, "\r"));

      /* Set the button actions */
      $('#outofthebox-modal-action #outofthebox-modal-description-input').off('keyup');
      $('#outofthebox-modal-action #outofthebox-modal-description-input').on("keyup", function (event) {
        if (event.which == 13 || event.keyCode == 13) {
          $('#outofthebox-modal-action .outofthebox-modal-description-btn').trigger('click');
        }
      });
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').click(function () {

        var new_description = $('#outofthebox-modal-description-input').val();

        var data = {
          action: 'outofthebox-edit-description-entry',
          account_id: account_id,
          id: dataid,
          newdescription: new_description,
          listtoken: self.options.listtoken,
          _ajax_nonce: self.options.description_nonce
        };
        self._actionDoModifyEntry(data);

        $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').prop('disabled', true);
        $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').html('<i class="fas fa-cog fa-spin fa-fw"></i><span> ' + self.options.str_processing + '</span>');


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


    /**
     * Create a request to move the selected enties
     * @param {UI element} entry
     * @param {UI element} to_folder
     */
    _actionMoveEntry: function (entries, to_folder, mouseevent) {

      var self = this;

      var files = [];

      $.each(entries, function () {
        files.push($(this).attr('data-id'));
      });

      var data = {
        action: 'outofthebox-move-entries',
        entries: files,
        copy: false,
        target: to_folder.attr('data-url'),
        _ajax_nonce: self.options.move_nonce
      };

      self._actionDoModifyEntry(data);

    },

    /**
     * Open a Dialog to move selected entries
     * @param {Object} entries
     */
    _actionMoveEntries: function (entries, mouseevent) {

      /* Close any open modal windows */

      $('#outofthebox-modal-action').remove();

      /* Build the data request variable and make a list of the selected entries */
      var self = this,
        list_of_files = '',
        files = [];
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
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
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

    /**
     * Open a Dialog to delete selected entries
     * @param {Object} entries
     */
    _actionDeleteEntries: function (entries, mouseevent) {

      /* Close any open modal windows */

      $('#outofthebox-modal-action').remove();

      /* Build the data request variable and make a list of the selected entries */
      var self = this,
        list_of_files = '',
        files = [];
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
      $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').off('click');
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

    /**
     * Initiate the Account Selector functionality
     */
    _actionSelectAccount: function () {
      /* Close any open modal windows */

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
      $('#outofthebox-modal-action .nav-account-selector').off('click');
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
      self.element.find('.files').fadeTo(100, 0);

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
        var storage_key = 'CloudPlugin_' + (request.listtoken + request._ajax_nonce + (typeof request.account_id === 'undefined' ? '' : request.account_id) + request.OutoftheBoxpath + request.lastpath + request.sort + request.query).hashCode();

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
    _helperReturnBytes: function (size) {
      if (size == '')
        return 0;

      var unit = size.charAt(size.length - 1);

      if (('B' === unit || 'b' === unit) && (isNaN(size.charAt(size.length - 2)))) {
        unit = size.charAt(size.length - 2);
      }

      switch (unit) {
        case 'M':
        case 'm':
          return parseInt(size) * 1048576
        case 'k':
        case 'k':
          return parseInt(size) * 1024
        case 'G':
        case 'g':
          return parseInt(size) * 1073741824
        default:
          return parseInt(size)
      }
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
        return $(this).closest('.entry').attr('data-id');
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