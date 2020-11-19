"use strict";

(function ($) {
  var vczAPIListUserMeetings = {
    init: function init() {
      this.cacheDOM();
      this.defaultActions();
    },
    cacheDOM: function cacheDOM() {
      this.$wrapper = $('.vczapi-user-meeting-list');

      if (this.$wrapper === undefined || this.$wrapper.length < 1) {
        return false;
      }
    },
    defaultActions: function defaultActions() {
      this.$wrapper.DataTable({
        responsive: true
      });
    }
  };
  var vczAPIMeetingFilter = {
    init: function init() {
      this.cacheDOM();
      this.evntHandlers();
    },
    cacheDOM: function cacheDOM() {
      this.$taxonomyOrder = $('.vczapi-taxonomy-ordering');
      this.$orderType = $('.vczapi-ordering');
    },
    evntHandlers: function evntHandlers() {
      this.$taxonomyOrder.on('change', this.taxOrdering.bind(this));
      this.$orderType.on('change', this.upcomingLatest.bind(this));
    },
    taxOrdering: function taxOrdering(e) {
      $(e.currentTarget).closest('form').submit();
    },
    upcomingLatest: function upcomingLatest(e) {
      $(e.currentTarget).closest('form').submit();
    }
  };
  $(function () {
    vczAPIMeetingFilter.init();
    vczAPIListUserMeetings.init();
  });
})(jQuery);