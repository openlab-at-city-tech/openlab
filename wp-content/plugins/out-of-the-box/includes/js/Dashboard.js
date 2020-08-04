(function ($) {
  'use strict';
  $.widget("cp.OutoftheBoxDashboard", {
    options: {
      $container_totals: $('#outofthebox-totals'),
      $container_events_chart: $('#outofthebox-events-chart'),
      $container_top_downloads: $('#top-downloads'),
      $container_top_users: $('#top-users'),
      $container_full_log: $('#full-log')
    },

    _create: function () {
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

      $('body').addClass('outofthebox');

      this._initChartDatePicker();

      this.options.$container_totals.one('inview', function (event, isInView) {
        self.loadTotalsData(self.options.$container_totals, null, null);
      });

      this.options.$container_events_chart.one('inview', function (event, isInView) {
        self.loadChartData();
      });

      this.options.$container_top_downloads.one('inview', function (event, isInView) {
        self.loadTopDownloads();
      });

      this.options.$container_top_users.one('inview', function (event, isInView) {
        self.loadTopUsers();
      });

      this.options.$container_full_log.one('inview', function (event, isInView) {
        self.loadFullLog();
      });

      this._initEvents();
    },

    _initEvents: function () {
      var self = this;

      $(self.element).on('click', 'a.open-entry-details', function (e) {
        var id = $(this).data('entry-id');
        var account_id = $(this).data('account-id');
        self.viewEntryDetails(id, account_id);
      });
      $('body').on('click', '#outofthebox-modal-action a.open-entry-details', function (e) {
        var id = $(this).data('entry-id');
        var account_id = $(this).data('account-id');
        self.viewEntryDetails(id, account_id);
      });

      $(self.element).on('click', 'a.open-user-details', function (e) {
        var id = $(this).data('user-id');
        self.viewUserDetails(id);
      });
      $('body').on('click', '#outofthebox-modal-action a.open-user-details', function (e) {
        var id = $(this).data('user-id');
        self.viewUserDetails(id);
      });
      $(self.element).on('click', '#clear_statistics', function (e) {
        self.clearStatistics();
      });

    },

    clearStatistics: function () {
      var self = this;
      var $button = $("#clear_statistics");
      $button.addClass('disabled');
      $button.find('.oftb-spinner').fadeIn();

      $.ajax({
        method: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-reset-statistics',
          _ajax_nonce: self.options.admin_nonce
        },
        complete: function () {
          $button.removeClass('disabled');
          $button.find('.oftb-spinner').fadeOut();
          location.reload(true);
        },
        dataType: 'json'
      });

    },

    _initChartDatePicker: function () {
      var self = this;

      var $chart_datepicker_from = self.element.find(".chart_datepicker_from");
      var $chart_datepicker_to = self.element.find(".chart_datepicker_to");

      /* Select From DatePicker */
      self.chart_datepicker_from = $chart_datepicker_from.datepicker({
        altField: ".chart_datepicker_from",
        autoSize: true,
        dateFormat: "dd-mm-yy",
        changeMonth: true
      }).on("change", function () {
        self.chart_datepicker_to.datepicker("option", "minDate", moment($chart_datepicker_from.datepicker("getDate")).format('DD-MM-YYYY'));
        clearTimeout(self.updateChartTimer);
        self.updateChartTimer = setTimeout(function () {
          self.loadChartData();
          self.fullLog.ajax.reload();
        }, 1000);
      });
      $chart_datepicker_from.datepicker("setDate", moment().subtract(1, 'months').toDate());

      /* Select To DatePicker */
      self.chart_datepicker_to = $chart_datepicker_to.datepicker({
        altField: ".chart_datepicker_to",
        autoSize: true,
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        maxDate: "+0d"
      }).on("change", function () {
        self.chart_datepicker_from.datepicker("option", "maxDate", moment($chart_datepicker_to.datepicker("getDate")).format('DD-MM-YYYY'));

        clearTimeout(self.updateChartTimer);
        self.updateChartTimer = setTimeout(function () {
          self.loadChartData();
          self.fullLog.ajax.reload();
        }, 1000);
      });
      $chart_datepicker_to.datepicker("setDate", moment().toDate());
    },

    loadChartData: function () {
      var self = this;

      self.options.$container_events_chart.prev().fadeIn();

      $.ajax({
        method: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-event-stats',
          type: 'activities',
          periodstart: moment(self.chart_datepicker_from.datepicker("getDate")).format('YYYY-MM-DD'),
          periodend: moment(self.chart_datepicker_to.datepicker("getDate")).format('YYYY-MM-DD'),
          _ajax_nonce: self.options.admin_nonce
        },

        success: function (dataset) {
          self._setChart(dataset);
        },
        complete: function (dataset) {
          self.options.$container_events_chart.prev().fadeOut();
        },
        dataType: 'json'
      });

    },
    _setChart: function (dataset) {
      var self = this;

      new Chart(self.options.$container_events_chart, {
        type: 'line',
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            position: 'bottom'
          },
          tooltips: {
            mode: 'x'
          },
          scales: {
            xAxes: [{
                type: 'time',
                time: {
                  parser: 'DD-MM-YYYY',
                  unit: 'day',
                  tooltipFormat: 'LL'
                },
                scaleLabel: {
                  display: false
                },
                ticks: {
                  beginAtZero: false
                }
              }],
            yAxes: [{
                scaleLabel: {
                  display: false
                },
                ticks: {
                  beginAtZero: true
                }
              }]
          }
        },

        data: dataset
      });
    },

    loadTotalsData: function ($container_totals, detail, id) {
      var self = this;

      $container_totals.find('.outofthebox-counter span').css({'background-color': '', 'color': ''});

      $.ajax({
        method: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-event-stats',
          type: 'totals',
          detail: detail,
          id: id,
          _ajax_nonce: self.options.admin_nonce
        },

        success: function (dataset) {
          self._setTotalsData($container_totals, dataset);
        },
        complete: function (dataset) {
          $container_totals.find('.loading').fadeOut();
        },
        dataType: 'json'
      });

    },

    _setTotalsData: function ($container_totals, dataset) {
      var self = this;

      $.each($container_totals.find('.outofthebox-counter'), function () {
        var event_type = $(this).data('type');

        if (typeof dataset[event_type] === 'undefined') {
          $(this).find('span').append('0');
          return true;
        }
        var total = parseInt(dataset[event_type].total);

        $(this).find('span').css({'background-color': dataset[event_type].colors.normal, 'color': 'white'}).append(total);
      });
    },

    loadTopDownloads: function () {
      var self = this;

      self.options.$container_top_downloads.DataTable({
        dom: '<"top">rt<"bottom">p<"clear">',
        "language": {
          "loadingRecords": '',
          "processing": '<div class="loading"><div class="loader-beat"></div></div>'
        },
        searching: false,
        autoWidth: true,
        scrollY: '300px',
        paging: false,
        deferRender: true,
        responsive: {
          details: {
            type: 'column',
            target: -1
          }
        },
        "columns": [
          {"data": "icon"},
          {"data": "entry_name"},
          {"data": "total"}
        ],
        columnDefs: [
          {
            name: "icon",
            targets: 0,
            width: "20px",
            className: 'all',
            responsivePriority: 4,
            searchable: false,
            orderable: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return '<a href="#' + full.entry_id + '"><img src="' + data + '" width="24px" height="24px"/></a>';
            }
          },
          {
            name: "entry_name",
            targets: 1,
            className: 'all',
            responsivePriority: 1,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return '<a href="#' + full.entry_id + '" title="' + full.parent_path + '/' + full.entry_name + '" class="open-entry-details" data-entry-id="' + full.entry_id + '">' + full.entry_name + '</a>';
            }
          },
          {
            name: "total",
            targets: 2,
            width: "30px",
            className: 'dt-right',
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return data;
            }
          }
        ],
        "order": [[2, "desc"]],
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url": self.options.ajax_url,
          "data": {
            action: 'outofthebox-event-stats',
            type: 'topdownloads',
            _ajax_nonce: self.options.admin_nonce
          }
        }
      });
    },
    loadTopUsers: function () {
      var self = this;

      self.options.$container_top_users.DataTable({
        dom: '<"top">rt<"bottom">p<"clear">',
        "language": {
          "loadingRecords": '',
          "processing": '<div class="loading"><div class="loader-beat"></div></div>'
        },
        searching: false,
        autoWidth: true,
        scrollY: '300px',
        paging: false,
        deferRender: true,
        responsive: {
          details: {
            type: 'column',
            target: -1
          }
        },
        "columns": [
          {"data": "icon"},
          {"data": "user_display_name"},
          {"data": "user_name"},
          {"data": "total"}
        ],
        columnDefs: [
          {
            name: "icon",
            targets: 0,
            width: "20px",
            className: 'all',
            responsivePriority: 4,
            searchable: false,
            orderable: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return '<a href="#' + full.user_id + '" class="open-user-details" data-user-id="' + full.user_id + '" ><img src="' + data + '" width="24px" height="24px"/></a>';
            }
          },
          {
            name: "user_display_name",
            targets: 1,
            className: 'all',
            responsivePriority: 1,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              if (full.user_id === "0") {
                return data;
              }

              return '<a href="#' + full.user_id + '" class="open-user-details" data-user-id="' + full.user_id + '">' + data + '</a>';
            }
          },
          {
            name: "user_name",
            targets: 2,
            className: 'dt-left',
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              if (full.user_id === "0") {
                return data;
              }

              return '<a href="#' + full.user_id + '" class="open-user-details"  data-user-id="' + full.user_id + '">' + data + '</a>';
            }
          },
          {
            name: "total",
            targets: 3,
            width: "30px",
            className: 'dt-right',
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return data;
            }
          }
        ],
        "order": [[3, "desc"]],
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url": self.options.ajax_url,
          "data": {
            action: 'outofthebox-event-stats',
            type: 'topusers',
            _ajax_nonce: self.options.admin_nonce
          }
        }
      });
    },
    loadFullLog: function () {
      var self = this;

      self.fullLog = self.options.$container_full_log.DataTable({
        dom: 'fBrtip<"clear">',
        "language": {
          "loadingRecords": '<div class="loading"><div class="loader-beat"></div></div>',
          "processing": '<div class="loading"><div class="loader-beat"></div></div>'
        },
        autoWidth: true,
        deferRender: true,
        scrollY: 500,
        scroller: true,
        scrollCollapse: true,
        pageLength: 50,
        searchDelay: 600,
        buttons: [
          {
            text: '<i class="fas fa-file-export fa-lg"></i>',
            className: 'simple-button blue',
            action: function (e, dt, node, config) {
              $.ajax({
                "url": self.options.ajax_url,
                "data": {
                  'action': 'outofthebox-event-stats',
                  'type': 'full-log',
                  'export': true,
                  'periodstart': moment(self.chart_datepicker_from.datepicker("getDate")).format('YYYY-MM-DD'),
                  'periodend': moment(self.chart_datepicker_to.datepicker("getDate")).format('YYYY-MM-DD'),
                  '_ajax_nonce': self.options.admin_nonce
                },
                "success": function (res, status, xhr) {
                  var csvData = new Blob([res], {type: 'text/csv;charset=utf-8;'});
                  var csvURL = window.URL.createObjectURL(csvData);
                  var tempLink = document.createElement('a');
                  tempLink.href = csvURL;
                  tempLink.setAttribute('download', 'data.csv');
                  tempLink.click();
                }
              });
            }
          },
          {
            extend: 'copy',
            text: '<i class="fas fa-copy fa-lg"></i>',
            className: 'simple-button blue'
          },
          {
            extend: 'print',
            text: '<i class="fas fa-print fa-lg"></i>',
            className: 'simple-button blue',
            title: ''
          },
          {
            extend: 'colvis',
            text: '<i class="fas fa-filter fa-lg"></i>',
            className: 'simple-button blue',
            background: false,
            columns: [1, 2, 3, 4, 5, 6, 7, 8]
          }
        ],
        "columns": [
          {"data": "icon"},
          {"data": "description"},
          {"data": "datetime"},
          {"data": "type"},
          {"data": "user"},
          {"data": "entry_name"},
          {"data": "parent_path"},
          {"data": "location"},
          {"data": "extra"}
        ],
        columnDefs: [

          {
            name: "icon",
            targets: 0,
            orderable: false,
            width: "24px",
            className: 'dt-left all',
            searchable: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return "<i class='fas " + data + " fa-lg'></i>";
            }
          },
          {
            name: "description",
            targets: 1,
            className: 'dt-left',
            searchable: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return data;
            }
          },

          {
            name: "datetime",
            targets: 2,
            width: "150px",
            className: 'dt-left all',
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return data;
            }
          },
          {
            name: "type",
            targets: 3,
            className: 'all',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return data;
            }
          },
          {
            name: "user",
            targets: 4,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return '<a href="#' + full.user_id + '" class="open-user-details"  data-user-id="' + full.user_id + '">' + data + '</a>';
            }
          },
          {
            name: "entry_name",
            targets: 5,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return  data;
            }
          },
          {
            name: "parent_path",
            targets: 6,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return  data;
            }
          },
          {
            name: "location",
            targets: 7,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              if (data === '') {
                return '';
              }

              return  '<a href="' + full.location_full + '" target="_blank"><i class="fas fa-external-link-alt"></i> ' + data + '</a>';
            }
          },
          {
            name: "extra",
            targets: 8,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return data;

            }
          }
        ],
        "order": [[2, "desc"]],
        "processing": true,
        "serverSide": true,
        "ajax": {
          "url": self.options.ajax_url,
          data: function (d) {
            d.action = 'outofthebox-event-stats';
            d.type = 'full-log';
            d.periodstart = moment(self.chart_datepicker_from.datepicker("getDate")).format('YYYY-MM-DD');
            d.periodend = moment(self.chart_datepicker_to.datepicker("getDate")).format('YYYY-MM-DD');
            d._ajax_nonce = self.options.admin_nonce;
          }
        }
      });
    },

    loadDetailedLog: function ($table, detail, id) {
      var self = this;

      $table.DataTable({
        dom: 'fBrtip<"clear">',
        "language": {
          "loadingRecords": '<div class="loading"><div class="loader-beat"></div></div>',
          "processing": '<div class="loading"><div class="loader-beat"></div></div>'
        },
        autoWidth: true,
        deferRender: true,
        scrollY: 300,
        scroller: true,
        pageLength: 50,
        searchDelay: 600,
        responsive: true,
        buttons: [
          {
            text: '<i class="fas fa-file-export fa-lg"></i>',
            className: 'simple-button blue',
            action: function (e, dt, node, config) {
              $.ajax({
                "url": self.options.ajax_url,
                "data": {
                  'action': 'outofthebox-event-stats',
                  'type': 'full-log',
                  'detail': detail,
                  'id': id,
                  'export': true,
                  '_ajax_nonce': self.options.admin_nonce
                },
                "success": function (res, status, xhr) {
                  var csvData = new Blob([res], {type: 'text/csv;charset=utf-8;'});
                  var csvURL = window.URL.createObjectURL(csvData);
                  var tempLink = document.createElement('a');
                  tempLink.href = csvURL;
                  tempLink.setAttribute('download', 'data.csv');
                  tempLink.click();
                }
              });
            }
          },
          {
            extend: 'copy',
            text: '<i class="fas fa-copy fa-lg"></i>',
            className: 'simple-button blue'
          },
          {
            extend: 'print',
            text: '<i class="fas fa-print fa-lg"></i>',
            className: 'simple-button blue',
            title: ''
          },
          {
            extend: 'colvis',
            text: '<i class="fas fa-filter fa-lg"></i>',
            className: 'simple-button blue',
            background: false,
            columns: [1, 2, 3, 4, 5, 6, 7, 8]
          }
        ],
        "columns": [
          {"data": "icon"},
          {"data": "description"},
          {"data": "datetime"},
          {"data": "type"},
          {"data": "user"},
          {"data": "entry_name"},
          {"data": "parent_path"},
          {"data": "location"},
          {"data": "extra"}
        ],
        columnDefs: [

          {
            name: "icon",
            targets: 0,
            orderable: false,
            width: "24px",
            className: 'dt-left all',
            searchable: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return "<i class='fas " + data + " fa-lg'></i>";
            }
          },
          {
            name: "description",
            targets: 1,
            className: 'dt-left',
            searchable: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return data;
            }
          },

          {
            name: "datetime",
            targets: 2,
            width: "150px",
            className: 'dt-left all',
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return data;
            }
          },
          {
            name: "type",
            targets: 3,
            className: 'all',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return data;
            }
          },
          {
            name: "user",
            targets: 4,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return '<a href="#' + full.user_id + '" class="open-user-details"  data-user-id="' + full.user_id + '">' + data + '</a>';
            }
          },
          {
            name: "entry_name",
            targets: 5,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return  data;
            }
          },
          {
            name: "parent_path",
            targets: 6,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              return  data;
            }
          },
          {
            name: "location",
            targets: 7,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }
              if (data === '') {
                return '';
              }

              return  '<a href="' + full.location_full + '" target="_blank"><i class="fas fa-external-link-alt"></i>' + data + '</a>';
            }
          },
          {
            name: "extra",
            targets: 8,
            className: 'dt-left',
            visible: false,
            render: function (data, type, full, meta) {
              if (type !== "display") {
                return data;
              }

              return data;

            }
          }
        ],
        "order": [[2, "desc"]],
        "processing": true,
        "serverSide": true,
        "ajax": self.options.ajax_url + '?action=outofthebox-event-stats&type=full-log&detail=' + detail + '&id=' + id + '&_ajax_nonce=' + self.options.admin_nonce
      });
    },

    viewEntryDetails: function (id, account_id) {
      var self = this;

      self.openModal('entry', id, account_id);
    },

    viewUserDetails: function (id) {
      var self = this;

      self.openModal('user', id, null);

    },

    openModal: function (detail, id, account_id) {

      var self = this;

      $('#outofthebox-modal-action').remove();

      /* Build the Delete Dialog */
      var modalbuttons = '';
      modalbuttons += '<button class="simple-button blue outofthebox-modal-cancel-btn secondary" data-action="cancel" type="button" onclick="modal_action.close();" title="' + self.options.str_close_title + '" >' + self.options.str_close_title + '</button>';
      var modalheader = $('<a tabindex="0" class="close-button" title="' + self.options.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
      var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" style="display:none"></div>');
      var modalfooter = $('<div class="outofthebox-modal-footer" style="display:none"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
      var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal outofthebox-modal80 ' + self.options.content_skin + '"><div class="modal-dialog"><div class="modal-content"><div class="loading"><div class="loader-beat"></div></div></div></div></div>');

      $('body').append(modaldialog);

      var $detail_template = $('.event-details-template').clone().appendTo(modalbody).show();
      $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

      switch (detail) {
        case 'user':
          var $detail_container = $('.modal-content  .event-details-user-template').show();
          break;
        case 'entry':
          var $detail_container = $('.modal-content  .event-details-entry-template').show();
          break;
      }

      /* Load Totals */
      var $totals_container = $('.modal-content .event-details-totals-template');
      $totals_container.find('span').html('<div class="loading"><div class="loader-beat"></div></div>');
      self.loadTotalsData($totals_container, detail, id);

      $.ajax({type: "POST",
        url: self.options.ajax_url,
        data: {
          action: 'outofthebox-event-stats',
          account_id: account_id,
          type: 'get-detail',
          detail: detail,
          id: id,
          _ajax_nonce: self.options.admin_nonce
        },
        success: function (response) {
          switch (detail) {

            case 'user':
              $detail_template.find('.event-details-name').text(response.user.user_name);
              $detail_container.find('.event-details-email').text(response.user.user_email);
              $detail_container.find('.event-details-roles').text(response.user.user_roles);
              $detail_container.find('.event-details-entry-img').css('background-image', 'url(' + response.user.avatar + ')');
              $detail_container.find('.event-visit-profile').attr('href', response.user.user_link);
              break;
            case 'entry':
              $detail_template.find('.event-details-name').text(response.entry.entry_name);
              $detail_container.find('.event-details-description').text(response.entry.entry_description);
              $detail_container.find('.event-details-entry-img').css('background-image', 'url(' + response.entry.entry_thumbnails + ')');

              if (response.entry.entry_link === false) {
                $detail_container.find('.event-download-entry').fadeOut();
              } else {
                $detail_container.find('.event-download-entry').attr('href', response.entry.entry_link + '&_ajax_nonce=' + self.options.admin_nonce);
              }
              break;
          }

          $('.outofthebox-modal-body').fadeIn();
          $('.outofthebox-modal-footer').fadeIn();
          $('.modal-content .loading:first').fadeOut();

        },
        complete: function () {
          $detail_container.find('.loading').fadeOut();
        },
        dataType: 'json'
      });

      /* Load Log*/
      self.loadDetailedLog($('#outofthebox-modal-action .modal-content table'), detail, id);

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

    }
  });
})(jQuery);

(function ($) {
  $(".OutoftheBoxDashboard").OutoftheBoxDashboard(OutoftheBox_Dashboard_vars);
})(jQuery);