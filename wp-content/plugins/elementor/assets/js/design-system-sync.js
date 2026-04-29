/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/*!*****************************************************************************!*\
  !*** ../modules/design-system-sync/assets/js/design-system-sync-handler.js ***!
  \*****************************************************************************/


(function () {
  'use strict';

  var SYNC_ENDPOINT = wpApiSettings.root + 'elementor/v1/design-system-sync/stylesheet';
  var SYNC_STYLESHEET_ID = 'elementor-design-system-sync-css';
  var DEBOUNCE_MS = 500;
  var syncTimeout;
  function syncDesignSystem() {
    fetch(SYNC_ENDPOINT, {
      method: 'POST',
      headers: {
        'X-WP-Nonce': wpApiSettings.nonce
      }
    }).then(function (response) {
      if (!response.ok) {
        return;
      }
      if (204 === response.status) {
        return;
      }
      return response.json();
    }).then(function (data) {
      if (!data) {
        return;
      }
      refreshGlobals();
      reloadCanvasDesignSyncStyles(data);
    });
  }
  function refreshGlobals() {
    var globals = $e.components.get('globals');
    globals === null || globals === void 0 || globals.refreshGlobalData();
    globals === null || globals === void 0 || globals.populateGlobalData();
  }
  function reloadCanvasDesignSyncStyles(_ref) {
    var url = _ref.url;
    var previewFrame = document.getElementById('elementor-preview-iframe');
    if (!(previewFrame !== null && previewFrame !== void 0 && previewFrame.contentDocument)) {
      return;
    }
    var link = previewFrame.contentDocument.getElementById(SYNC_STYLESHEET_ID);
    if (!link) {
      link = previewFrame.contentDocument.createElement('link');
      link.id = SYNC_STYLESHEET_ID;
      link.rel = 'stylesheet';
      previewFrame.contentDocument.head.appendChild(link);
    }
    link.href = url;
  }
  function onClassesUpdated(event) {
    var context = event.detail.context;
    if (context !== 'frontend') {
      return;
    }
    clearTimeout(syncTimeout);
    syncTimeout = setTimeout(syncDesignSystem, DEBOUNCE_MS);
  }
  window.addEventListener('classes:updated', onClassesUpdated);
  window.addEventListener('variables:updated', function () {
    clearTimeout(syncTimeout);
    syncTimeout = setTimeout(syncDesignSystem, DEBOUNCE_MS);
  });
})();
/******/ })()
;
//# sourceMappingURL=design-system-sync.js.map