/*! elementor - v3.22.0 - 26-06-2024 */
"use strict";
(self["webpackChunkelementor"] = self["webpackChunkelementor"] || []).push([["progress"],{

/***/ "../assets/dev/js/frontend/handlers/progress.js":
/*!******************************************************!*\
  !*** ../assets/dev/js/frontend/handlers/progress.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports["default"] = void 0;
class Progress extends elementorModules.frontend.handlers.Base {
  getDefaultSettings() {
    return {
      selectors: {
        progressNumber: '.elementor-progress-bar'
      }
    };
  }
  getDefaultElements() {
    const selectors = this.getSettings('selectors');
    return {
      $progressNumber: this.$element.find(selectors.progressNumber)
    };
  }
  onInit() {
    super.onInit();
    const observer = this.createObserver();
    observer.observe(this.elements.$progressNumber[0]);
  }
  createObserver() {
    const options = {
      root: null,
      threshold: 0,
      rootMargin: '0px'
    };
    return new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const $progressbar = this.elements.$progressNumber;
          $progressbar.css('width', $progressbar.data('max') + '%');
        }
      });
    }, options);
  }
}
exports["default"] = Progress;

/***/ })

}]);
//# sourceMappingURL=progress.6d15c16f0f5c4792940f.bundle.js.map