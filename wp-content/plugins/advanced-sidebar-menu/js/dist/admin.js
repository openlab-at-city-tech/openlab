/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 962:
/*!***********************************************************************************************************************************************************************************************************************************!*\
  !*** ./.yarn/__virtual__/@lipemat-js-boilerplate-gutenberg-virtual-b53edc2e95/0/cache/@lipemat-js-boilerplate-gutenberg-npm-2.11.0-f95d21e270-dda1d4faf8.zip/node_modules/@lipemat/js-boilerplate-gutenberg/dist/index.module.js ***!
  \***********************************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "addMiddleware": function() { return /* binding */ m; },
/* harmony export */   "autoload": function() { return /* binding */ z; },
/* harmony export */   "autoloadBlocks": function() { return /* binding */ U; },
/* harmony export */   "autoloadPlugins": function() { return /* binding */ X; },
/* harmony export */   "clearApplicationPassword": function() { return /* binding */ W; },
/* harmony export */   "clearNonce": function() { return /* binding */ P; },
/* harmony export */   "createMethods": function() { return /* binding */ G; },
/* harmony export */   "defaultFetchHandler": function() { return /* binding */ B; },
/* harmony export */   "doRequest": function() { return /* binding */ A; },
/* harmony export */   "doRequestWithPagination": function() { return /* binding */ C; },
/* harmony export */   "enableApplicationPassword": function() { return /* binding */ N; },
/* harmony export */   "getAuthorizationUrl": function() { return /* binding */ D; },
/* harmony export */   "hasApplicationPassword": function() { return /* binding */ M; },
/* harmony export */   "hasExternalNonce": function() { return /* binding */ g; },
/* harmony export */   "removeMiddleware": function() { return /* binding */ w; },
/* harmony export */   "restoreNonce": function() { return /* binding */ E; },
/* harmony export */   "restoreRootURL": function() { return /* binding */ R; },
/* harmony export */   "setNonce": function() { return /* binding */ b; },
/* harmony export */   "setRootURL": function() { return /* binding */ I; },
/* harmony export */   "usePostMeta": function() { return /* binding */ F; },
/* harmony export */   "useTerms": function() { return /* binding */ Y; },
/* harmony export */   "wpapi": function() { return /* binding */ L; }
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/api-fetch */ 179);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/url */ 368);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/blocks */ 378);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/plugins */ 398);
/* harmony import */ var _wordpress_plugins__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ 284);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! react */ 363);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_6__);
function p(){return(p=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e}).apply(this,arguments)}var l,v,h=[];function m(e){return h.push(e),h.length-1}function w(e){return delete h[e],h}function g(){return void 0!==v}function b(e){E(),v=m(function(e){function t(e,n){var r=e.headers,o=void 0===r?{}:r;for(var i in o)"x-wp-nonce"===i.toLowerCase()&&delete o[i];return n(p({},e,{headers:p({},o,{"X-WP-Nonce":t.nonce})}))}return t.nonce=e,t}(e))}function P(){void 0!==v&&(w(v),v=void 0),void 0===l&&(l=m(function(e,t){if(void 0!==e.headers)for(var n in e.headers)"x-wp-nonce"===n.toLowerCase()&&delete e.headers[n];return t(e,t)}))}function E(){void 0!==v&&w(v),void 0!==l&&w(l),v=void 0,l=void 0}var y=function(e,t){return void 0===t&&(t=!0),Promise.resolve(function(e,t){return void 0===t&&(t=!0),t?204===e.status?null:e.json?e.json():Promise.reject(e):e}(e,t)).catch(function(e){return T(e,t)})};function T(e,n){if(void 0===n&&(n=!0),!n)throw e;return function(e){var n={code:"invalid_json",message:(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("The response is not a valid JSON response.")};if(!e||!e.json)throw n;return e.json().catch(function(){throw n})}(e).then(function(e){var n={code:"unknown_error",message:(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("An unknown error occurred.")};throw"rest_cookie_invalid_nonce"===e.code&&g()&&(e.code="external_rest_cookie_invalid_nonce"),e||n})}var k,_=["url","path","data","parse"],j={Accept:"application/json, */*;q=0.1"},x={credentials:"include"},O=function(e){if(e.status>=200&&e.status<300)return e;throw e},B=function(e){var n=function e(t,n){return function(r){return void 0===n[t]?r:(0,n[t])(r,t===n.length-1?function(e){return e}:e(t+1,n))}}(0,h.filter(Boolean))(p({},x,e)),r=n.url,o=n.path,i=n.data,u=n.parse,s=void 0===u||u,c=function(e,t){if(null==e)return{};var n,r,o={},i=Object.keys(e);for(r=0;r<i.length;r++)t.indexOf(n=i[r])>=0||(o[n]=e[n]);return o}(n,_),a=n.body,d=n.headers;return d=p({},j,d),i&&(a=JSON.stringify(i),d["Content-Type"]="application/json"),window.fetch(r||o,p({},c,{body:a,headers:d})).then(function(e){return Promise.resolve(e).then(O).catch(function(e){return T(e,s)}).then(function(e){return y(e,s)})},function(){throw{code:"fetch_error",message:(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("You are probably offline.")}})},C=function(e,t,n){return Promise.resolve(A(e,t,n,!1)).then(function(e){return Promise.resolve(y(e)).then(function(t){return{items:t,totalPages:parseInt(e.headers.get("X-WP-TotalPages")||"1"),totalItems:parseInt(e.headers.get("X-WP-Total")||"0")}})})},A=function(t,r,o,i){void 0===i&&(i=!0);try{return Promise.resolve(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()(void 0===o||"GET"===r?{method:r,parse:i,path:(0,_wordpress_url__WEBPACK_IMPORTED_MODULE_2__.addQueryArgs)(t,o)}:{data:o,method:r,parse:i,path:t}))}catch(e){return Promise.reject(e)}};function G(e){return{create:function(t){return A(e,"POST",t)},delete:function(t){return A(e+="/"+t,"DELETE",{force:!0})},get:function(t){return A(e,"GET",t)},getById:function(t,n){return A(e+="/"+t,"GET",n)},getWithPagination:function(t){return C(e,"GET",t)},trash:function(t){return A(e+="/"+t,"DELETE")},update:function(t){return A(e+="/"+t.id,"PATCH",t)}}}function L(t){var n={};return["categories","comments","blocks","media","menus","menu-locations","menu-items","statuses","pages","posts","tags","taxonomies","types","search"].map(function(e){return n[e]=function(){return G("/wp/v2/"+e)}}),n.menuLocations=function(){return G("/wp/v2/menu-locations")},n.menuItems=function(){return G("/wp/v2/menu-items")},n.users=function(){var e=G("/wp/v2/users");return e.delete=function(e,t){return void 0===t&&(t=!1),A("/wp/v2/users/"+e,"DELETE",{force:!0,reassign:t})},e},n.applicationPasswords=function(){return{create:function(e,t){return A("/wp/v2/users/"+e+"/application-passwords","POST",t)},delete:function(e,t){return A("/wp/v2/users/"+e+"/application-passwords/"+t,"DELETE")},get:function(e){return A("/wp/v2/users/"+e+"/application-passwords","GET")},getById:function(e,t){return A("/wp/v2/users/"+e+"/application-passwords/"+t,"GET")},introspect:function(e){return A("/wp/v2/users/"+e+"/application-passwords/introspect","GET")},update:function(e,t,n){return A("/wp/v2/users/"+e+"/application-passwords/"+t,"PUT",n)}}},n.settings=function(){return{get:function(){return A("/wp/v2/settings","GET")},update:function(e){return A("/wp/v2/settings","POST",e)}}},void 0!==t&&Object.keys(t).map(function(e){return n[e]=t[e]}),_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default().setFetchHandler(B),n}function R(){w(k)}function I(t,n){k&&w(k),k=m(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default().createRootURLMiddleware(t.replace(/\/$/,"")+"/")),window.location.hostname&&new URL(t).hostname===window.location.hostname?E():void 0!==n?b(n):g()||P()}var S,D=function(r){return Promise.resolve(function(o,i){try{var u=Promise.resolve(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()({path:"/",method:"GET"})).then(function(e){return e.authentication["application-passwords"]?(0,_wordpress_url__WEBPACK_IMPORTED_MODULE_2__.addQueryArgs)(e.authentication["application-passwords"].endpoints.authorization,r):{code:"application_passwords_disabled",message:(0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)("Application passwords are not enabled on this site."),data:null}})}catch(e){return e}return u&&u.then?u.then(void 0,function(e){return e}):u}())};function M(){return void 0!==S}function W(){void 0!==S&&(w(S),S=void 0)}function N(e,t){W(),S=m(function(n,r){var o=n.headers;return r(p({},n,{headers:p({},void 0===o?{}:o,{Authorization:"Basic "+btoa(e+":"+t)})}),r)})}var U=function(e,t){z({afterReload:q,beforeReload:J,getContext:e,pluginModule:t,register:_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__.registerBlockType,unregister:_wordpress_blocks__WEBPACK_IMPORTED_MODULE_3__.unregisterBlockType,type:"block"})},X=function(e,t){z({afterReload:function(){},beforeReload:function(){},getContext:e,pluginModule:t,register:_wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__.registerPlugin,unregister:_wordpress_plugins__WEBPACK_IMPORTED_MODULE_4__.unregisterPlugin,type:"plugin"})},z=function(e){var t=e.afterReload,n=e.beforeReload,r=e.getContext,o=e.pluginModule,i=e.register,u=e.unregister,s=e.type,c={},a=function(){n();var e=r();if(e){var o=[];return e.keys().forEach(function(t){var n=e(t);n.exclude||n!==c[t]&&(c[n.name+"-"+s]&&u(n.name),i(n.name,n.settings),o.push(n.name),c[n.name+"-"+s]=n)}),t(o),e}},d=a();o.hot&&null!=d&&d.id&&o.hot.accept(d.id,a)},H=null,J=function(){H=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.select)("core/block-editor").getSelectedBlockClientId(),(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.dispatch)("core/block-editor").clearSelectedBlock()},q=function(e){void 0===e&&(e=[]),(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.select)("core/block-editor").getBlocks().forEach(function(t){var n=t.clientId;e.includes(t.name)&&(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.dispatch)("core/block-editor").selectBlock(n)}),H?(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.dispatch)("core/block-editor").selectBlock(H):(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.dispatch)("core/block-editor").clearSelectedBlock(),H=null};function F(e){var t=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useDispatch)("core/editor").editPost,n=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useSelect)(function(e){return{previous:e("core/editor").getCurrentPostAttribute("meta"),current:e("core/editor").getEditedPostAttribute("meta")}}),r=e?n.current[e]:n.current,o=e?n.previous[e]:n.previous,i=(0,react__WEBPACK_IMPORTED_MODULE_6__.useCallback)(function(n){var r;e&&t({meta:(r={},r[e]=n,r)})},[t,e]),u=(0,react__WEBPACK_IMPORTED_MODULE_6__.useCallback)(function(e,n){var r;t({meta:(r={},r[e]=n,r)})},[t]);return e?[r,i,o]:[r,u,o]}function Y(e){var t=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useDispatch)("core/editor").editPost,n=(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useSelect)(function(t){var n=t("core").getTaxonomy(e);return n?{taxonomy:n,current:t("core/editor").getEditedPostAttribute(n.rest_base),previous:t("core/editor").getCurrentPostAttribute(n.rest_base)}:{current:[],previous:[]}}),r=(0,react__WEBPACK_IMPORTED_MODULE_6__.useCallback)(function(e){try{var r;return Promise.resolve(n.taxonomy?t(((r={})[n.taxonomy.rest_base]=e,r)):void 0)}catch(e){return Promise.reject(e)}},[n,t]);return[n.current,r,n.previous]}
//# sourceMappingURL=index.module.js.map


/***/ }),

/***/ 85:
/*!******************************************!*\
  !*** ./src/components/ErrorBoundary.tsx ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ 363);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../globals/config */ 434);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/url */ 368);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! dompurify */ 688);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(dompurify__WEBPACK_IMPORTED_MODULE_3__);




/**
 * Wrap any component in me, which may throw errors, and I will
 * prevent the entire UI from disappearing.
 *
 * Custom version special to Advanced Sidebar Menu with links to
 * support as well as debugging information.
 *
 * @link https://reactjs.org/docs/error-boundaries.html#introducing-error-boundaries
 */

class ErrorBoundary extends react__WEBPACK_IMPORTED_MODULE_0__.Component {
  constructor(props) {
    super(props);
    this.state = {
      hasError: false,
      error: null
    };
  }

  static getDerivedStateFromError() {
    // Update state, so the next render will show the fallback UI.
    return {
      hasError: true
    };
  }
  /**
   * Log information about the error when it happens.
   *
   * @notice Will log "Error: A cross-origin error was thrown. React doesn't have
   *         access to the actual error object in development" in React development
   *         mode but provides full error info in React production.
   */


  componentDidCatch(error, info) {
    console.log('%cError caught by the Advanced Sidebar ErrorBoundary!', 'color:orange; font-size: large; font-weight: bold');
    console.log(this.props);
    console.log(error);
    console.log(info);
    this.setState({
      error
    });
  }

  render() {
    if (this.state.hasError) {
      var _this$state$error, _this$state$error2;

      if (!_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.siteInfo.scriptDebug) {
        return /*#__PURE__*/React.createElement("div", {
          className: 'components-panel__body is-opened'
        }, /*#__PURE__*/React.createElement("h4", {
          style: {
            color: 'red'
          }
        }, "Something went wrong!"), /*#__PURE__*/React.createElement("p", null, "Please ", /*#__PURE__*/React.createElement("a", {
          href: (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_2__.addQueryArgs)((0,dompurify__WEBPACK_IMPORTED_MODULE_3__.sanitize)(window.location.href), {
            'script-debug': true
          })
        }, "enable script debug"), " to retrieve error information."));
      }

      return /*#__PURE__*/React.createElement("div", {
        className: 'components-panel__body is-opened'
      }, /*#__PURE__*/React.createElement("h4", {
        style: {
          color: 'red'
        }
      }, "Something went wrong!"), /*#__PURE__*/React.createElement("p", null, "Please ", /*#__PURE__*/React.createElement("a", {
        target: "_blank",
        href: _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.support,
        rel: "noreferrer"
      }, "create a support request"), " with the following:"), /*#__PURE__*/React.createElement("ol", null, /*#__PURE__*/React.createElement("li", null, "The ", /*#__PURE__*/React.createElement("a", {
        href: 'https://onpointplugins.com/how-to-retrieve-console-logs-from-your-browser/',
        target: '_blank',
        rel: "noreferrer"
      }, "logs from your browser console.")), /*#__PURE__*/React.createElement("li", null, "The following information.")), /*#__PURE__*/React.createElement("div", {
        style: {
          border: '2px groove',
          padding: '10px',
          width: '100%',
          overflowWrap: 'break-word'
        }
      }, /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, /*#__PURE__*/React.createElement("em", null, "Message")), " ", /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement("code", null, (_this$state$error = this.state.error) === null || _this$state$error === void 0 ? void 0 : _this$state$error.message)), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, /*#__PURE__*/React.createElement("em", null, "Block")), " ", /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement("code", null, this.props.block)), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, /*#__PURE__*/React.createElement("em", null, "Attributes")), " ", /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement("code", null, JSON.stringify(this.props.attributes))), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, /*#__PURE__*/React.createElement("em", null, "Site Info")), " ", /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement("code", null, JSON.stringify(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.siteInfo))), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("strong", null, /*#__PURE__*/React.createElement("em", null, "Stack")), " ", /*#__PURE__*/React.createElement("br", null), /*#__PURE__*/React.createElement("code", null, (_this$state$error2 = this.state.error) === null || _this$state$error2 === void 0 ? void 0 : _this$state$error2.stack))), /*#__PURE__*/React.createElement("p", null, "\xA0"), /*#__PURE__*/React.createElement("p", null, "\xA0"));
    }

    return this.props.children;
  }

}

/* harmony default export */ __webpack_exports__["default"] = (ErrorBoundary);

/***/ }),

/***/ 434:
/*!*******************************!*\
  !*** ./src/globals/config.ts ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "CONFIG": function() { return /* binding */ CONFIG; }
/* harmony export */ });
const CONFIG = window.ADVANCED_SIDEBAR_MENU || {};

/***/ }),

/***/ 409:
/*!************************************!*\
  !*** ./src/gutenberg/SideLoad.tsx ***!
  \************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ 537);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/data */ 284);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ 916);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);



let firstClientId = '';
/**
 * The customizer area does not include a `PluginArea` component,
 * so our slot fills do not load.
 *
 * We can use filters, but the Fills double up each time
 * another block is added to the page.
 *
 * Track the clientId of the first block we add the Fill to
 * and only return the Fill for that block. The rest of the blocks
 * inherit the Fill from the first block via their Slots.
 */

const SideLoad = _ref => {
  let {
    clientId,
    children
  } = _ref;

  if (!(0,lodash__WEBPACK_IMPORTED_MODULE_2__.isEmpty)(firstClientId) && clientId !== firstClientId) {
    // Make sure block still exists.
    if (-1 !== (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_1__.select)('core/block-editor').getBlockIndex(firstClientId)) {
      return null;
    }
  }

  firstClientId = clientId;
  return children ?? null;
};

/* harmony default export */ __webpack_exports__["default"] = ((0,_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.withFilters)('advanced-sidebar-menu.blocks.side-load')(SideLoad));

/***/ }),

/***/ 415:
/*!******************************************!*\
  !*** ./src/gutenberg/blocks/Display.tsx ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/components */ 537);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../globals/config */ 434);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ 916);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var react_string_replace__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react-string-replace */ 589);
/* harmony import */ var react_string_replace__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(react_string_replace__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../components/ErrorBoundary */ 85);






const checkboxes = {
  /* translators: Selected taxonomy single label */
  include_parent: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Display the highest level parent %s', 'advanced-sidebar-menu'),

  /* translators: Selected taxonomy single label */
  include_childless_parent: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Display menu when there is only the parent %s', 'advanced-sidebar-menu'),

  /* translators: Selected taxonomy plural label */
  display_all: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Always display child %s', 'advanced-sidebar-menu')
};
/**
 * Display Options shared between widgets.
 *
 * 1. Display the highest level parent page.
 * 2. Display menu when there is only the parent page.
 * 3. Always display child pages.
 * 5. Display levels of child pages.
 *
 */

const Display = _ref => {
  var _type$labels3;

  let {
    attributes,
    setAttributes,
    type,
    name,
    clientId,
    children
  } = _ref;
  const showLevels = _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.pages.id === name && _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.isPro || attributes.display_all;
  const fillProps = {
    type,
    attributes,
    name,
    setAttributes,
    clientId
  };
  return /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Display', 'advanced-sidebar-menu')
  }, Object.keys(checkboxes).map(item => {
    var _type$labels;

    let label = (type === null || type === void 0 ? void 0 : (_type$labels = type.labels) === null || _type$labels === void 0 ? void 0 : _type$labels.singular_name.toLowerCase()) ?? '';

    if ('display_all' === item) {
      var _type$labels2;

      label = (type === null || type === void 0 ? void 0 : (_type$labels2 = type.labels) === null || _type$labels2 === void 0 ? void 0 : _type$labels2.name.toLowerCase()) ?? '';
    }

    return /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.CheckboxControl, {
      key: item //eslint-disable-next-line @wordpress/valid-sprintf
      ,
      label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.sprintf)(checkboxes[item], label),
      checked: !!attributes[item],
      onChange: value => {
        setAttributes({
          [item]: !!value
        });
      }
    });
  }), showLevels && /*#__PURE__*/React.createElement("div", {
    className: 'components-base-control'
  },
  /* translators: {select html input}, {post type plural label} */
  react_string_replace__WEBPACK_IMPORTED_MODULE_4___default()((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Display %1$s levels of child %2$s', 'advanced-sidebar-menu').replace('%2$s', (type === null || type === void 0 ? void 0 : (_type$labels3 = type.labels) === null || _type$labels3 === void 0 ? void 0 : _type$labels3.name.toLowerCase()) ?? ''), '%1$s', () => /*#__PURE__*/React.createElement("select", {
    key: 'levels',
    value: attributes.levels,
    onChange: ev => setAttributes({
      levels: parseInt(ev.target.value)
    })
  }, /*#__PURE__*/React.createElement("option", {
    value: "100"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('- All -', 'advanced-sidebar-menu')), (0,lodash__WEBPACK_IMPORTED_MODULE_3__.range)(1, 10).map(n => /*#__PURE__*/React.createElement("option", {
    key: n,
    value: n
  }, n))))), children, /*#__PURE__*/React.createElement(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_5__["default"], {
    attributes: attributes,
    block: name
  }, _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.pages.id === name && /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.Slot, {
    name: "advanced-sidebar-menu/pages/display",
    fillProps: fillProps
  }), _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.categories.id === name && /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_0__.Slot, {
    name: "advanced-sidebar-menu/categories/display",
    fillProps: fillProps
  })));
};

/* harmony default export */ __webpack_exports__["default"] = (Display);

/***/ }),

/***/ 522:
/*!********************************************!*\
  !*** ./src/gutenberg/blocks/InfoPanel.tsx ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../globals/config */ 434);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ 537);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/block-editor */ 78);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/html-entities */ 906);
/* harmony import */ var _wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _info_panel_pcss__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./info-panel.pcss */ 393);







const InfoPanel = _ref => {
  let {} = _ref;
  return /*#__PURE__*/React.createElement(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_2__.InspectorControls, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Advanced Sidebar Menu PRO', 'advanced-sidebar-menu'),
    className: _info_panel_pcss__WEBPACK_IMPORTED_MODULE_5__["default"].wrap
  }, /*#__PURE__*/React.createElement("ul", null, _globals_config__WEBPACK_IMPORTED_MODULE_0__.CONFIG.features.map(feature => /*#__PURE__*/React.createElement("li", {
    key: feature
  }, (0,_wordpress_html_entities__WEBPACK_IMPORTED_MODULE_3__.decodeEntities)(feature))), /*#__PURE__*/React.createElement("li", null, /*#__PURE__*/React.createElement("a", {
    href: "https://onpointplugins.com/product/advanced-sidebar-menu-pro/?utm_source=block-more&utm_campaign=gopro&utm_medium=wp-dash",
    target: "_blank",
    style: {
      textDecoration: 'none'
    },
    rel: "noreferrer"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('So much more…', 'advanced-sidebar-menu')))), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
    className: _info_panel_pcss__WEBPACK_IMPORTED_MODULE_5__["default"].button,
    href: 'https://onpointplugins.com/product/advanced-sidebar-menu-pro/?trigger_buy_now=1&utm_source=block-upgrade&utm_campaign=gopro&utm_medium=wp-dash',
    target: '_blank',
    rel: 'noreferrer',
    isPrimary: true
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Upgrade', 'advanced-sidebar-menu'))));
};

/* harmony default export */ __webpack_exports__["default"] = ((0,_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.withFilters)('advanced-sidebar-menu.blocks.info-panel')(InfoPanel));

/***/ }),

/***/ 289:
/*!******************************************!*\
  !*** ./src/gutenberg/blocks/Preview.tsx ***!
  \******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "sanitizeClientId": function() { return /* binding */ sanitizeClientId; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ 363);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../globals/config */ 434);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/server-side-render */ 882);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ 537);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ 78);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! dompurify */ 688);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(dompurify__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @wordpress/hooks */ 31);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/data */ 284);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../helpers */ 928);
/* harmony import */ var _preview_pcss__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./preview.pcss */ 926);
/* provided dependency */ var $ = __webpack_require__(/*! jquery */ 311);
function _extends() { _extends = Object.assign ? Object.assign.bind() : function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }













/**
 * Sanitize a client id for use as an HTML id.
 *
 * Must not start with a `-` or a digit.
 *
 */
const sanitizeClientId = clientId => {
  return clientId.replace(/^([\d-])/, '_$1');
};
/**
 * If we are in the widgets' area, the block is wrapped in
 * a "sidebar" block. We retrieve the id to pass along with
 * the request to use the `widget_args` within the preview.
 *
 */

const getSidebarId = clientId => {
  if (!(0,_helpers__WEBPACK_IMPORTED_MODULE_9__.isScreen)(['widgets'])) {
    return '';
  }

  const rootId = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_8__.select)('core/block-editor').getBlockRootClientId(clientId);

  if (rootId) {
    const ParentBlock = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_8__.select)('core/block-editor').getBlocksByClientId([rootId]);

    if (ParentBlock[0] && 'core/widget-area' === ParentBlock[0].name) {
      var _ParentBlock$, _ParentBlock$$attribu;

      return (_ParentBlock$ = ParentBlock[0]) === null || _ParentBlock$ === void 0 ? void 0 : (_ParentBlock$$attribu = _ParentBlock$.attributes) === null || _ParentBlock$$attribu === void 0 ? void 0 : _ParentBlock$$attribu.id;
    }
  }

  return '';
};
/**
 * @notice Must use static constants, or the ServerSide requests
 *         will fire anytime something on the page is changed
 *         because the component re-renders.
 */


const Page = () => /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Placeholder, {
  className: _preview_pcss__WEBPACK_IMPORTED_MODULE_10__["default"].placeholder,
  icon: 'welcome-widgets-menus',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Advanced Sidebar - Pages', 'advanced-sidebar-menu'),
  instructions: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('No preview available', 'advanced-sidebar-menu')
});

const Category = () => /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Placeholder, {
  className: _preview_pcss__WEBPACK_IMPORTED_MODULE_10__["default"].placeholder,
  icon: 'welcome-widgets-menus',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Advanced Sidebar - Categories', 'advanced-sidebar-menu'),
  instructions: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('No preview available', 'advanced-sidebar-menu')
});

const Navigation = () => /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Placeholder, {
  className: _preview_pcss__WEBPACK_IMPORTED_MODULE_10__["default"].placeholder,
  icon: 'welcome-widgets-menus',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('Advanced Sidebar - Navigation', 'advanced-sidebar-menu'),
  instructions: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_7__.__)('No preview available', 'advanced-sidebar-menu')
});
/**
 * @notice The styles will not display for the preview
 *         in the block inserter sidebar when Webpack
 *         is enabled because the iframe has a late init.
 *
 */


const placeholder = block => {
  var _CONFIG$blocks$naviga;

  switch (block) {
    case _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.pages.id:
      return Page;

    case _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.categories.id:
      return Category;

    case (_CONFIG$blocks$naviga = _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.blocks.navigation) === null || _CONFIG$blocks$naviga === void 0 ? void 0 : _CONFIG$blocks$naviga.id:
      return Navigation;
  }

  return () => /*#__PURE__*/React.createElement(React.Fragment, null);
};
/**
 * Same as the `DefaultLoadingResponsePlaceholder` except we trigger
 * an action when the loading component is unmounted to allow
 * components to hook into when ServerSideRender has finished loading.
 *
 * @notice Using a constant to prevent reload on every content change.
 *
 */


const TriggerWhenLoadingFinished = _ref => {
  var _children$props, _children$props$child;

  let {
    children,
    attributes = {
      clientId: ''
    }
  } = _ref;
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    // Call action when the loading component unmounts because loading is finished.
    return () => {
      // Give the preview a chance to load on WP 5.8.
      setTimeout(() => {
        $('[data-preview="' + `${attributes.clientId}` + '"]').find('a').on('click', ev => ev.preventDefault());
        (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_6__.doAction)('advanced-sidebar-menu.blocks.preview.loading-finished', {
          values: attributes,
          clientId: attributes.clientId
        });
      }, 100);
    };
  });
  /**
   * ServerSideRender returns a <RawHTML /> filled with an error object when fetch fails.
   *
   * We throw an error, so our `ErrorBoundary` will catch it, otherwise we end up
   * with a "React objects may not be used as children" error, which means nothing.
   */

  if (children !== null && children !== void 0 && (_children$props = children.props) !== null && _children$props !== void 0 && (_children$props$child = _children$props.children) !== null && _children$props$child !== void 0 && _children$props$child.errorMsg) {
    var _children$props2, _children$props2$chil;

    throw new Error((children === null || children === void 0 ? void 0 : (_children$props2 = children.props) === null || _children$props2 === void 0 ? void 0 : (_children$props2$chil = _children$props2.children) === null || _children$props2$chil === void 0 ? void 0 : _children$props2$chil.errorMsg) ?? 'Failed');
  }

  return /*#__PURE__*/React.createElement("div", {
    className: _preview_pcss__WEBPACK_IMPORTED_MODULE_10__["default"].spinWrap
  }, /*#__PURE__*/React.createElement("div", {
    className: _preview_pcss__WEBPACK_IMPORTED_MODULE_10__["default"].spin
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Spinner, null)), /*#__PURE__*/React.createElement("div", {
    className: _preview_pcss__WEBPACK_IMPORTED_MODULE_10__["default"].spinContent
  }, children));
};

const Preview = _ref2 => {
  let {
    attributes,
    block,
    clientId
  } = _ref2;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.useBlockProps)();

  if ('' !== _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.error) {
    return /*#__PURE__*/React.createElement("div", {
      className: _preview_pcss__WEBPACK_IMPORTED_MODULE_10__["default"].error,
      dangerouslySetInnerHTML: {
        __html: (0,dompurify__WEBPACK_IMPORTED_MODULE_5__.sanitize)(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.error)
      }
    });
  }

  const sanitizedClientId = sanitizeClientId(clientId); // Prevent styles from doubling up as they are already added via render in PHP.

  delete blockProps.style;
  return /*#__PURE__*/React.createElement("div", _extends({}, blockProps, {
    "data-preview": sanitizedClientId
  }), /*#__PURE__*/React.createElement((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_2___default()), {
    EmptyResponsePlaceholder: placeholder(block),
    LoadingResponsePlaceholder: TriggerWhenLoadingFinished,
    attributes: { ...attributes,
      // Send custom attribute to determine server side renders.
      isServerSideRenderRequest: true,
      clientId: sanitizedClientId,
      sidebarId: getSidebarId(clientId)
    },
    block: block,
    httpMethod: 'POST'
  }));
};

/* harmony default export */ __webpack_exports__["default"] = (Preview);

/***/ }),

/***/ 812:
/*!**************************************************!*\
  !*** ./src/gutenberg/blocks/categories/Edit.tsx ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ 284);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../globals/config */ 434);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! dompurify */ 688);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(dompurify__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/block-editor */ 78);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _Preview__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../Preview */ 289);
/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./block */ 333);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../../../components/ErrorBoundary */ 85);
/* harmony import */ var _Display__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../Display */ 415);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @wordpress/components */ 537);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _InfoPanel__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../InfoPanel */ 522);
/* harmony import */ var _SideLoad__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../SideLoad */ 409);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../helpers */ 928);
/* harmony import */ var _pages_edit_pcss__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../pages/edit.pcss */ 959);















const Edit = _ref => {
  var _taxonomy$labels, _taxonomy$labels2, _taxonomy$labels3, _taxonomy$labels4, _taxonomy$labels5;

  let {
    attributes,
    setAttributes,
    clientId,
    name
  } = _ref;
  const taxonomy = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.useSelect)(select => {
    const type = select('core').getTaxonomy(attributes.taxonomy ?? 'category');
    return type ?? select('core').getTaxonomy('category');
  }, [attributes.taxonomy]); // We have a version conflict or license error.

  if ('' !== _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.error) {
    return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.InspectorControls, null, /*#__PURE__*/React.createElement("div", {
      className: _pages_edit_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].error,
      dangerouslySetInnerHTML: {
        __html: (0,dompurify__WEBPACK_IMPORTED_MODULE_2__.sanitize)(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.error)
      }
    })), /*#__PURE__*/React.createElement(_Preview__WEBPACK_IMPORTED_MODULE_4__["default"], {
      attributes: attributes,
      block: _block__WEBPACK_IMPORTED_MODULE_5__.block.id,
      clientId: clientId
    }));
  }

  const fillProps = {
    type: taxonomy,
    attributes,
    name,
    setAttributes,
    clientId
  };
  const EXCLUDE_HELP = /*#__PURE__*/React.createElement("span", {
    dangerouslySetInnerHTML: {
      //phpcs:ignore
      __html: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.sprintf)(
      /* translators: {taxonomy plural label}, {taxonomy single label}, {<a>}, {</a>} */
      (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('%1$s may also be excluded from all menus via the Advanced Sidebar settings when %3$sediting a %2$s%4$s.', 'advanced-sidebar-menu'), (taxonomy === null || taxonomy === void 0 ? void 0 : (_taxonomy$labels = taxonomy.labels) === null || _taxonomy$labels === void 0 ? void 0 : _taxonomy$labels.name) ?? '', (taxonomy === null || taxonomy === void 0 ? void 0 : (_taxonomy$labels2 = taxonomy.labels) === null || _taxonomy$labels2 === void 0 ? void 0 : _taxonomy$labels2.singular_name.toLowerCase()) ?? '', '<a href="https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-pro-widget-docs/#categories" target="_blank">', '</a>')
    }
  });
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.InspectorControls, null, /*#__PURE__*/React.createElement(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_6__["default"], {
    attributes: attributes,
    block: name
  }, (0,_helpers__WEBPACK_IMPORTED_MODULE_12__.isScreen)(['widgets', 'site-editor', 'customize']) && /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.PanelBody, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.TextControl, {
    value: attributes.title ?? '',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('Title', 'advanced-sidebar-menu'),
    onChange: title => setAttributes({
      title
    })
  })), /*#__PURE__*/React.createElement(_Display__WEBPACK_IMPORTED_MODULE_7__["default"], {
    attributes: attributes,
    clientId: clientId,
    name: name,
    setAttributes: setAttributes,
    type: taxonomy
  }, (0,_helpers__WEBPACK_IMPORTED_MODULE_12__.isScreen)(['post']) && /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.CheckboxControl
  /* translators: Selected taxonomy plural label */
  , {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('Display %s on single posts', 'advanced-sidebar-menu'), (taxonomy === null || taxonomy === void 0 ? void 0 : (_taxonomy$labels3 = taxonomy.labels) === null || _taxonomy$labels3 === void 0 ? void 0 : _taxonomy$labels3.name.toLowerCase()) ?? ''),
    checked: !!attributes.single,
    onChange: value => {
      setAttributes({
        single: !!value
      });
    }
  }), (0,_helpers__WEBPACK_IMPORTED_MODULE_12__.isScreen)(['widgets', 'customize']) && attributes.single && /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.SelectControl, {
    /* translators: Selected taxonomy single label */
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('Display each single post\'s %s', 'advanced-sidebar-menu'), (taxonomy === null || taxonomy === void 0 ? void 0 : (_taxonomy$labels4 = taxonomy.labels) === null || _taxonomy$labels4 === void 0 ? void 0 : _taxonomy$labels4.name.toLowerCase()) ?? ''),
    value: attributes.new_widget,
    options: Object.entries(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.categories.displayEach).map(_ref2 => {
      let [value, label] = _ref2;
      return {
        value,
        label
      };
    })
    /* eslint-disable-next-line camelcase */
    ,
    onChange: new_widget => setAttributes({
      new_widget
    })
  })), /*#__PURE__*/React.createElement("div", {
    className: 'components-panel__body is-opened'
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.Slot, {
    name: "advanced-sidebar-menu/categories/general",
    fillProps: fillProps
  }), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.TextControl
  /* translators: Selected post type plural label */
  , {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('%s to exclude (ids, comma separated)', 'advanced-sidebar-menu'), (taxonomy === null || taxonomy === void 0 ? void 0 : (_taxonomy$labels5 = taxonomy.labels) === null || _taxonomy$labels5 === void 0 ? void 0 : _taxonomy$labels5.name) ?? ''),
    value: attributes.exclude,
    help: _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.isPro ? EXCLUDE_HELP : '',
    onChange: value => {
      setAttributes({
        exclude: value
      });
    }
  }), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
    href: _globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.docs.category,
    target: "_blank",
    rel: "noopener noreferrer"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('block documentation', 'advanced-sidebar-menu')))), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.Slot, {
    name: "advanced-sidebar-menu/categories/inspector",
    fillProps: fillProps
  }))), /*#__PURE__*/React.createElement(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_3__.BlockControls, null, /*#__PURE__*/React.createElement(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_6__["default"], {
    attributes: attributes,
    block: name
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_8__.Slot, {
    name: "advanced-sidebar-menu/categories/block-controls",
    fillProps: fillProps
  }))), /*#__PURE__*/React.createElement(_InfoPanel__WEBPACK_IMPORTED_MODULE_10__["default"], {
    clientId: clientId
  }), /*#__PURE__*/React.createElement(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_6__["default"], {
    attributes: attributes,
    block: name
  }, /*#__PURE__*/React.createElement(_Preview__WEBPACK_IMPORTED_MODULE_4__["default"], {
    attributes: attributes,
    block: _block__WEBPACK_IMPORTED_MODULE_5__.block.id,
    clientId: clientId
  })), /*#__PURE__*/React.createElement(_SideLoad__WEBPACK_IMPORTED_MODULE_11__["default"], {
    clientId: clientId
  }));
};

/* harmony default export */ __webpack_exports__["default"] = (Edit);

/***/ }),

/***/ 333:
/*!***************************************************!*\
  !*** ./src/gutenberg/blocks/categories/block.tsx ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "block": function() { return /* binding */ block; },
/* harmony export */   "name": function() { return /* binding */ name; },
/* harmony export */   "settings": function() { return /* binding */ settings; }
/* harmony export */ });
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../globals/config */ 434);
/* harmony import */ var _Edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Edit */ 812);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../helpers */ 928);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




/**
 * Attributes specific to the widget as well as shared
 * widget attributes.
 *
 * @see \Advanced_Sidebar_Menu\Blocks\Block_Abstract::get_all_attributes
 * @see \Advanced_Sidebar_Menu\Blocks\Pages::get_attributes
 */

/**
 * Attributes used for the example preview.
 * Combines some PRO and basic attributes.
 * The PRO attributes will only be sent if PRO is active.
 */
const EXAMPLE = {
  'display-posts': 'all',
  'display-posts/limit': 2,
  apply_current_page_parent_styles_to_parent: true,
  apply_current_page_styles_to_parent: true,
  block_style: true,
  border: true,
  border_color: '#333',
  bullet_style: 'none',
  child_page_bg_color: '#666',
  child_page_color: '#fff',
  parent_page_bg_color: '#282828',
  parent_page_color: '#0cc4c6',
  parent_page_font_weight: 'normal',
  display_all: true,
  grandchild_page_bg_color: '#989898',
  grandchild_page_color: '#282828',
  grandchild_page_font_weight: 'bold',
  include_childless_parent: true,
  include_parent: true,
  levels: '2'
};
const block = _globals_config__WEBPACK_IMPORTED_MODULE_0__.CONFIG.blocks.categories;
const name = block.id;
const settings = {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Advanced Sidebar - Categories', 'advanced-sidebar-menu'),
  icon: 'welcome-widgets-menus',
  category: 'widgets',
  example: {
    attributes: EXAMPLE
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/legacy-widget'],
      isMatch: _ref => {
        let {
          idBase,
          instance
        } = _ref;

        if (!(instance !== null && instance !== void 0 && instance.raw)) {
          // Can't transform if raw instance is not shown in REST API.
          return false;
        }

        return 'advanced_sidebar_menu_category' === idBase;
      },
      transform: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.transformLegacyWidget)(name)
    }]
  },
  // `attributes` are registered server side because we use ServerSideRender.
  // `supports` are registered server side for easy overrides.
  edit: props => /*#__PURE__*/React.createElement(_Edit__WEBPACK_IMPORTED_MODULE_1__["default"], props),
  save: () => null,
  apiVersion: 2
};

/***/ }),

/***/ 348:
/*!*********************************************!*\
  !*** ./src/gutenberg/blocks/pages/Edit.tsx ***!
  \*********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/block-editor */ 78);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ 537);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _block__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./block */ 480);
/* harmony import */ var _Preview__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../Preview */ 289);
/* harmony import */ var _Display__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../Display */ 415);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/data */ 284);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _InfoPanel__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../InfoPanel */ 522);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../../../globals/config */ 434);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! dompurify */ 688);
/* harmony import */ var dompurify__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(dompurify__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../../../components/ErrorBoundary */ 85);
/* harmony import */ var _SideLoad__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../../SideLoad */ 409);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../../helpers */ 928);
/* harmony import */ var _edit_pcss__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./edit.pcss */ 959);















/**
 * Pages block content in the editor.
 */
const Edit = _ref => {
  var _postType$labels, _postType$labels2, _postType$labels3;

  let {
    attributes,
    setAttributes,
    clientId,
    name
  } = _ref;
  const postType = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_5__.useSelect)(select => {
    const type = select('core').getPostType(attributes.post_type ?? 'page');
    return type ?? select('core').getPostType('page');
  }, [attributes.post_type]); // We have a version conflict or license error.

  if ('' !== _globals_config__WEBPACK_IMPORTED_MODULE_7__.CONFIG.error) {
    return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement("div", {
      className: _edit_pcss__WEBPACK_IMPORTED_MODULE_13__["default"].error,
      dangerouslySetInnerHTML: {
        __html: (0,dompurify__WEBPACK_IMPORTED_MODULE_8__.sanitize)(_globals_config__WEBPACK_IMPORTED_MODULE_7__.CONFIG.error)
      }
    })), /*#__PURE__*/React.createElement(_Preview__WEBPACK_IMPORTED_MODULE_3__["default"], {
      attributes: attributes,
      block: _block__WEBPACK_IMPORTED_MODULE_2__.block.id,
      clientId: clientId
    }));
  }

  const fillProps = {
    type: postType,
    attributes,
    name,
    setAttributes,
    clientId
  };
  const EXCLUDE_HELP = /*#__PURE__*/React.createElement("span", {
    dangerouslySetInnerHTML: {
      //phpcs:ignore
      __html: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.sprintf)(
      /* translators: {post type plural label}, {post type single label}, {<a>}, {</a>} */
      (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('%1$s may also be excluded from all menus via the Advanced Sidebar settings when %3$sediting a %2$s%4$s.', 'advanced-sidebar-menu'), (postType === null || postType === void 0 ? void 0 : (_postType$labels = postType.labels) === null || _postType$labels === void 0 ? void 0 : _postType$labels.name) ?? '', (postType === null || postType === void 0 ? void 0 : (_postType$labels2 = postType.labels) === null || _postType$labels2 === void 0 ? void 0 : _postType$labels2.singular_name.toLowerCase()) ?? '', '<a href="https://onpointplugins.com/advanced-sidebar-menu/advanced-sidebar-menu-pro-widget-docs/#pages" target="_blank">', '</a>')
    }
  });
  return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__.InspectorControls, null, /*#__PURE__*/React.createElement(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_10__["default"], {
    attributes: attributes,
    block: name
  }, (0,_helpers__WEBPACK_IMPORTED_MODULE_12__.isScreen)(['widgets', 'site-editor', 'customize']) && /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
    value: attributes.title ?? '',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('Title', 'advanced-sidebar-menu'),
    onChange: title => setAttributes({
      title
    })
  })), /*#__PURE__*/React.createElement(_Display__WEBPACK_IMPORTED_MODULE_4__["default"], {
    attributes: attributes,
    clientId: clientId,
    name: name,
    setAttributes: setAttributes,
    type: postType
  }), /*#__PURE__*/React.createElement("div", {
    className: 'components-panel__body is-opened'
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Slot, {
    name: "advanced-sidebar-menu/pages/general",
    fillProps: fillProps
  }), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.SelectControl, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('Order by', 'advanced-sidebar-menu'),
    value: attributes.order_by,
    labelPosition: 'side',
    options: Object.entries(_globals_config__WEBPACK_IMPORTED_MODULE_7__.CONFIG.pages.orderBy).map(_ref2 => {
      let [value, label] = _ref2;
      return {
        value,
        label
      };
    }),
    onChange: value => {
      setAttributes({
        order_by: value
      });
    }
  }), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl
  /* translators: Selected post type plural label */
  , {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.sprintf)((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('%s to exclude (ids, comma separated)', 'advanced-sidebar-menu'), (postType === null || postType === void 0 ? void 0 : (_postType$labels3 = postType.labels) === null || _postType$labels3 === void 0 ? void 0 : _postType$labels3.name) ?? ''),
    value: attributes.exclude,
    help: _globals_config__WEBPACK_IMPORTED_MODULE_7__.CONFIG.isPro ? EXCLUDE_HELP : '',
    onChange: value => {
      setAttributes({
        exclude: value
      });
    }
  }), /*#__PURE__*/React.createElement("p", null, /*#__PURE__*/React.createElement("a", {
    href: _globals_config__WEBPACK_IMPORTED_MODULE_7__.CONFIG.docs.page,
    target: "_blank",
    rel: "noopener noreferrer"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_9__.__)('block documentation', 'advanced-sidebar-menu')))), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Slot, {
    name: "advanced-sidebar-menu/pages/inspector",
    fillProps: fillProps
  }))), /*#__PURE__*/React.createElement(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_0__.BlockControls, null, /*#__PURE__*/React.createElement(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_10__["default"], {
    attributes: attributes,
    block: name
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Slot, {
    name: "advanced-sidebar-menu/pages/block-controls",
    fillProps: fillProps
  }))), /*#__PURE__*/React.createElement(_InfoPanel__WEBPACK_IMPORTED_MODULE_6__["default"], {
    clientId: clientId
  }), /*#__PURE__*/React.createElement(_components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_10__["default"], {
    attributes: attributes,
    block: name
  }, /*#__PURE__*/React.createElement(_Preview__WEBPACK_IMPORTED_MODULE_3__["default"], {
    attributes: attributes,
    block: _block__WEBPACK_IMPORTED_MODULE_2__.block.id,
    clientId: clientId
  })), /*#__PURE__*/React.createElement(_SideLoad__WEBPACK_IMPORTED_MODULE_11__["default"], {
    clientId: clientId
  }));
};

/* harmony default export */ __webpack_exports__["default"] = (Edit);

/***/ }),

/***/ 480:
/*!**********************************************!*\
  !*** ./src/gutenberg/blocks/pages/block.tsx ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "block": function() { return /* binding */ block; },
/* harmony export */   "name": function() { return /* binding */ name; },
/* harmony export */   "settings": function() { return /* binding */ settings; }
/* harmony export */ });
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../globals/config */ 434);
/* harmony import */ var _Edit__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Edit */ 348);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../helpers */ 928);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ 3);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




/**
 * Attributes specific to the widget as well as shared
 * widget attributes.
 *
 * @see \Advanced_Sidebar_Menu\Blocks\Block_Abstract::get_all_attributes
 * @see \Advanced_Sidebar_Menu\Blocks\Pages::get_attributes
 */

/**
 * Attributes used for the example preview.
 * Combines some PRO and basic attributes.
 * The PRO attributes will only be sent if PRO is active.
 */
const EXAMPLE = {
  include_parent: true,
  include_childless_parent: true,
  display_all: true,
  levels: '2',
  apply_current_page_styles_to_parent: true,
  apply_current_page_parent_styles_to_parent: true,
  block_style: true,
  border: true,
  border_color: '#333',
  bullet_style: 'none',
  parent_page_color: '#fff',
  parent_page_bg_color: '#666',
  child_page_color: '#fff',
  child_page_bg_color: '#666',
  grandchild_page_color: '#282828',
  grandchild_page_bg_color: '#989898',
  grandchild_page_font_weight: 'bold',
  current_page_color: '#0cc4c6',
  current_page_bg_color: '#282828',
  current_page_font_weight: 'normal',
  current_page_parent_bg_color: '#333'
};
const block = _globals_config__WEBPACK_IMPORTED_MODULE_0__.CONFIG.blocks.pages;
const name = block.id;
const settings = {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Advanced Sidebar - Pages', 'advanced-sidebar-menu'),
  icon: 'welcome-widgets-menus',
  category: 'widgets',
  example: {
    attributes: EXAMPLE
  },
  transforms: {
    from: [{
      type: 'block',
      blocks: ['core/legacy-widget'],
      isMatch: _ref => {
        let {
          idBase,
          instance
        } = _ref;

        if (!(instance !== null && instance !== void 0 && instance.raw)) {
          // Can't transform if raw instance is not shown in REST API.
          return false;
        }

        return 'advanced_sidebar_menu' === idBase;
      },
      transform: (0,_helpers__WEBPACK_IMPORTED_MODULE_2__.transformLegacyWidget)(name)
    }]
  },
  // `attributes` are registered server side because we use ServerSideRender.
  // `supports` are registered server side for easy overrides.
  edit: props => /*#__PURE__*/React.createElement(_Edit__WEBPACK_IMPORTED_MODULE_1__["default"], props),
  save: () => null,
  apiVersion: 2
};

/***/ }),

/***/ 928:
/*!**********************************!*\
  !*** ./src/gutenberg/helpers.ts ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "isScreen": function() { return /* binding */ isScreen; },
/* harmony export */   "transformLegacyWidget": function() { return /* binding */ transformLegacyWidget; }
/* harmony export */ });
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ 378);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _globals_config__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../globals/config */ 434);



/**
 * Are we on one of the provided screens?
 */
const isScreen = screens => {
  return screens.includes(_globals_config__WEBPACK_IMPORTED_MODULE_1__.CONFIG.currentScreen);
};
/**
 * Transform a legacy widget to the matching block.
 *
 */

const transformLegacyWidget = name => _ref => {
  let {
    instance
  } = _ref;
  return (0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.createBlock)(name, translateLegacyWidget(instance.raw));
};
/**
 * Translate the widget's "checked" to the boolean
 * version used in the block.
 *
 */

const translateLegacyWidget = settings => {
  Object.entries(settings).forEach(_ref2 => {
    let [key, value] = _ref2;

    if ('checked' === value) {
      settings[key] = true;
    }

    if ('object' === typeof value) {
      translateLegacyWidget(settings[key]);
    } // Old widgets used to use "0" for some defaults.


    if ('0' === value) {
      delete settings[key];
    }
  });
  return settings;
};

/***/ }),

/***/ 10:
/*!********************************!*\
  !*** ./src/gutenberg/index.ts ***!
  \********************************/
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lipemat_js_boilerplate_gutenberg__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @lipemat/js-boilerplate-gutenberg */ 962);
/* harmony import */ var _blocks_Preview__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./blocks/Preview */ 289);
/* harmony import */ var _helpers__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./helpers */ 928);
/* harmony import */ var _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/ErrorBoundary */ 85);
/* module decorator */ module = __webpack_require__.hmd(module);




/**
 * Use our custom autoloader to automatically require,
 * register and add HMR support to Gutenberg related items.
 *
 * Will load from specified directory recursively.
 */

/* harmony default export */ __webpack_exports__["default"] = (() => {
  // Load all blocks
  (0,_lipemat_js_boilerplate_gutenberg__WEBPACK_IMPORTED_MODULE_3__.autoloadBlocks)(() => __webpack_require__(267), module); // Expose helpers and Preview component to window, so we can use them in PRO.

  window.ADVANCED_SIDEBAR_MENU.ErrorBoundary = _components_ErrorBoundary__WEBPACK_IMPORTED_MODULE_2__["default"];
  window.ADVANCED_SIDEBAR_MENU.Preview = _blocks_Preview__WEBPACK_IMPORTED_MODULE_0__["default"];
  window.ADVANCED_SIDEBAR_MENU.transformLegacyWidget = _helpers__WEBPACK_IMPORTED_MODULE_1__.transformLegacyWidget;
});

/***/ }),

/***/ 393:
/*!**********************************************!*\
  !*** ./src/gutenberg/blocks/info-panel.pcss ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin
/* harmony default export */ __webpack_exports__["default"] = ({"wrap":"info-panel__wrap__YT","button":"info-panel__button__PN"});

/***/ }),

/***/ 959:
/*!**********************************************!*\
  !*** ./src/gutenberg/blocks/pages/edit.pcss ***!
  \**********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin
/* harmony default export */ __webpack_exports__["default"] = ({"error":"edit__error___h"});

/***/ }),

/***/ 926:
/*!*******************************************!*\
  !*** ./src/gutenberg/blocks/preview.pcss ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin
/* harmony default export */ __webpack_exports__["default"] = ({"placeholder":"preview__placeholder__QP","error":"preview__error__xF","spin-wrap":"preview__spin-wrap__Jr","spinWrap":"preview__spin-wrap__Jr","spin":"preview__spin__A4","spin-content":"preview__spin-content__Yg","spinContent":"preview__spin-content__Yg"});

/***/ }),

/***/ 688:
/*!*********************************************************************************************************!*\
  !*** ./.yarn/cache/dompurify-npm-3.0.3-7db713c9bd-02242f2fe6.zip/node_modules/dompurify/dist/purify.js ***!
  \*********************************************************************************************************/
/***/ (function(module) {

/*! @license DOMPurify 3.0.3 | (c) Cure53 and other contributors | Released under the Apache license 2.0 and Mozilla Public License 2.0 | github.com/cure53/DOMPurify/blob/3.0.3/LICENSE */

(function (global, factory) {
   true ? module.exports = factory() :
  0;
})(this, (function () { 'use strict';

  const {
    entries,
    setPrototypeOf,
    isFrozen,
    getPrototypeOf,
    getOwnPropertyDescriptor
  } = Object;
  let {
    freeze,
    seal,
    create
  } = Object; // eslint-disable-line import/no-mutable-exports

  let {
    apply,
    construct
  } = typeof Reflect !== 'undefined' && Reflect;

  if (!apply) {
    apply = function apply(fun, thisValue, args) {
      return fun.apply(thisValue, args);
    };
  }

  if (!freeze) {
    freeze = function freeze(x) {
      return x;
    };
  }

  if (!seal) {
    seal = function seal(x) {
      return x;
    };
  }

  if (!construct) {
    construct = function construct(Func, args) {
      return new Func(...args);
    };
  }

  const arrayForEach = unapply(Array.prototype.forEach);
  const arrayPop = unapply(Array.prototype.pop);
  const arrayPush = unapply(Array.prototype.push);
  const stringToLowerCase = unapply(String.prototype.toLowerCase);
  const stringToString = unapply(String.prototype.toString);
  const stringMatch = unapply(String.prototype.match);
  const stringReplace = unapply(String.prototype.replace);
  const stringIndexOf = unapply(String.prototype.indexOf);
  const stringTrim = unapply(String.prototype.trim);
  const regExpTest = unapply(RegExp.prototype.test);
  const typeErrorCreate = unconstruct(TypeError);
  function unapply(func) {
    return function (thisArg) {
      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }

      return apply(func, thisArg, args);
    };
  }
  function unconstruct(func) {
    return function () {
      for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        args[_key2] = arguments[_key2];
      }

      return construct(func, args);
    };
  }
  /* Add properties to a lookup table */

  function addToSet(set, array, transformCaseFunc) {
    var _transformCaseFunc;

    transformCaseFunc = (_transformCaseFunc = transformCaseFunc) !== null && _transformCaseFunc !== void 0 ? _transformCaseFunc : stringToLowerCase;

    if (setPrototypeOf) {
      // Make 'in' and truthy checks like Boolean(set.constructor)
      // independent of any properties defined on Object.prototype.
      // Prevent prototype setters from intercepting set as a this value.
      setPrototypeOf(set, null);
    }

    let l = array.length;

    while (l--) {
      let element = array[l];

      if (typeof element === 'string') {
        const lcElement = transformCaseFunc(element);

        if (lcElement !== element) {
          // Config presets (e.g. tags.js, attrs.js) are immutable.
          if (!isFrozen(array)) {
            array[l] = lcElement;
          }

          element = lcElement;
        }
      }

      set[element] = true;
    }

    return set;
  }
  /* Shallow clone an object */

  function clone(object) {
    const newObject = create(null);

    for (const [property, value] of entries(object)) {
      newObject[property] = value;
    }

    return newObject;
  }
  /* This method automatically checks if the prop is function
   * or getter and behaves accordingly. */

  function lookupGetter(object, prop) {
    while (object !== null) {
      const desc = getOwnPropertyDescriptor(object, prop);

      if (desc) {
        if (desc.get) {
          return unapply(desc.get);
        }

        if (typeof desc.value === 'function') {
          return unapply(desc.value);
        }
      }

      object = getPrototypeOf(object);
    }

    function fallbackValue(element) {
      console.warn('fallback value for', element);
      return null;
    }

    return fallbackValue;
  }

  const html$1 = freeze(['a', 'abbr', 'acronym', 'address', 'area', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'big', 'blink', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'content', 'data', 'datalist', 'dd', 'decorator', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt', 'element', 'em', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'main', 'map', 'mark', 'marquee', 'menu', 'menuitem', 'meter', 'nav', 'nobr', 'ol', 'optgroup', 'option', 'output', 'p', 'picture', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'section', 'select', 'shadow', 'small', 'source', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr']); // SVG

  const svg$1 = freeze(['svg', 'a', 'altglyph', 'altglyphdef', 'altglyphitem', 'animatecolor', 'animatemotion', 'animatetransform', 'circle', 'clippath', 'defs', 'desc', 'ellipse', 'filter', 'font', 'g', 'glyph', 'glyphref', 'hkern', 'image', 'line', 'lineargradient', 'marker', 'mask', 'metadata', 'mpath', 'path', 'pattern', 'polygon', 'polyline', 'radialgradient', 'rect', 'stop', 'style', 'switch', 'symbol', 'text', 'textpath', 'title', 'tref', 'tspan', 'view', 'vkern']);
  const svgFilters = freeze(['feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix', 'feDiffuseLighting', 'feDisplacementMap', 'feDistantLight', 'feDropShadow', 'feFlood', 'feFuncA', 'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feImage', 'feMerge', 'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting', 'feSpotLight', 'feTile', 'feTurbulence']); // List of SVG elements that are disallowed by default.
  // We still need to know them so that we can do namespace
  // checks properly in case one wants to add them to
  // allow-list.

  const svgDisallowed = freeze(['animate', 'color-profile', 'cursor', 'discard', 'font-face', 'font-face-format', 'font-face-name', 'font-face-src', 'font-face-uri', 'foreignobject', 'hatch', 'hatchpath', 'mesh', 'meshgradient', 'meshpatch', 'meshrow', 'missing-glyph', 'script', 'set', 'solidcolor', 'unknown', 'use']);
  const mathMl$1 = freeze(['math', 'menclose', 'merror', 'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mmultiscripts', 'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mspace', 'msqrt', 'mstyle', 'msub', 'msup', 'msubsup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover', 'mprescripts']); // Similarly to SVG, we want to know all MathML elements,
  // even those that we disallow by default.

  const mathMlDisallowed = freeze(['maction', 'maligngroup', 'malignmark', 'mlongdiv', 'mscarries', 'mscarry', 'msgroup', 'mstack', 'msline', 'msrow', 'semantics', 'annotation', 'annotation-xml', 'mprescripts', 'none']);
  const text = freeze(['#text']);

  const html = freeze(['accept', 'action', 'align', 'alt', 'autocapitalize', 'autocomplete', 'autopictureinpicture', 'autoplay', 'background', 'bgcolor', 'border', 'capture', 'cellpadding', 'cellspacing', 'checked', 'cite', 'class', 'clear', 'color', 'cols', 'colspan', 'controls', 'controlslist', 'coords', 'crossorigin', 'datetime', 'decoding', 'default', 'dir', 'disabled', 'disablepictureinpicture', 'disableremoteplayback', 'download', 'draggable', 'enctype', 'enterkeyhint', 'face', 'for', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang', 'id', 'inputmode', 'integrity', 'ismap', 'kind', 'label', 'lang', 'list', 'loading', 'loop', 'low', 'max', 'maxlength', 'media', 'method', 'min', 'minlength', 'multiple', 'muted', 'name', 'nonce', 'noshade', 'novalidate', 'nowrap', 'open', 'optimum', 'pattern', 'placeholder', 'playsinline', 'poster', 'preload', 'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'rev', 'reversed', 'role', 'rows', 'rowspan', 'spellcheck', 'scope', 'selected', 'shape', 'size', 'sizes', 'span', 'srclang', 'start', 'src', 'srcset', 'step', 'style', 'summary', 'tabindex', 'title', 'translate', 'type', 'usemap', 'valign', 'value', 'width', 'xmlns', 'slot']);
  const svg = freeze(['accent-height', 'accumulate', 'additive', 'alignment-baseline', 'ascent', 'attributename', 'attributetype', 'azimuth', 'basefrequency', 'baseline-shift', 'begin', 'bias', 'by', 'class', 'clip', 'clippathunits', 'clip-path', 'clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cx', 'cy', 'd', 'dx', 'dy', 'diffuseconstant', 'direction', 'display', 'divisor', 'dur', 'edgemode', 'elevation', 'end', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'filterunits', 'flood-color', 'flood-opacity', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'fx', 'fy', 'g1', 'g2', 'glyph-name', 'glyphref', 'gradientunits', 'gradienttransform', 'height', 'href', 'id', 'image-rendering', 'in', 'in2', 'k', 'k1', 'k2', 'k3', 'k4', 'kerning', 'keypoints', 'keysplines', 'keytimes', 'lang', 'lengthadjust', 'letter-spacing', 'kernelmatrix', 'kernelunitlength', 'lighting-color', 'local', 'marker-end', 'marker-mid', 'marker-start', 'markerheight', 'markerunits', 'markerwidth', 'maskcontentunits', 'maskunits', 'max', 'mask', 'media', 'method', 'mode', 'min', 'name', 'numoctaves', 'offset', 'operator', 'opacity', 'order', 'orient', 'orientation', 'origin', 'overflow', 'paint-order', 'path', 'pathlength', 'patterncontentunits', 'patterntransform', 'patternunits', 'points', 'preservealpha', 'preserveaspectratio', 'primitiveunits', 'r', 'rx', 'ry', 'radius', 'refx', 'refy', 'repeatcount', 'repeatdur', 'restart', 'result', 'rotate', 'scale', 'seed', 'shape-rendering', 'specularconstant', 'specularexponent', 'spreadmethod', 'startoffset', 'stddeviation', 'stitchtiles', 'stop-color', 'stop-opacity', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke', 'stroke-width', 'style', 'surfacescale', 'systemlanguage', 'tabindex', 'targetx', 'targety', 'transform', 'transform-origin', 'text-anchor', 'text-decoration', 'text-rendering', 'textlength', 'type', 'u1', 'u2', 'unicode', 'values', 'viewbox', 'visibility', 'version', 'vert-adv-y', 'vert-origin-x', 'vert-origin-y', 'width', 'word-spacing', 'wrap', 'writing-mode', 'xchannelselector', 'ychannelselector', 'x', 'x1', 'x2', 'xmlns', 'y', 'y1', 'y2', 'z', 'zoomandpan']);
  const mathMl = freeze(['accent', 'accentunder', 'align', 'bevelled', 'close', 'columnsalign', 'columnlines', 'columnspan', 'denomalign', 'depth', 'dir', 'display', 'displaystyle', 'encoding', 'fence', 'frame', 'height', 'href', 'id', 'largeop', 'length', 'linethickness', 'lspace', 'lquote', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'movablelimits', 'notation', 'numalign', 'open', 'rowalign', 'rowlines', 'rowspacing', 'rowspan', 'rspace', 'rquote', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier', 'selection', 'separator', 'separators', 'stretchy', 'subscriptshift', 'supscriptshift', 'symmetric', 'voffset', 'width', 'xmlns']);
  const xml = freeze(['xlink:href', 'xml:id', 'xlink:title', 'xml:space', 'xmlns:xlink']);

  const MUSTACHE_EXPR = seal(/\{\{[\w\W]*|[\w\W]*\}\}/gm); // Specify template detection regex for SAFE_FOR_TEMPLATES mode

  const ERB_EXPR = seal(/<%[\w\W]*|[\w\W]*%>/gm);
  const TMPLIT_EXPR = seal(/\${[\w\W]*}/gm);
  const DATA_ATTR = seal(/^data-[\-\w.\u00B7-\uFFFF]/); // eslint-disable-line no-useless-escape

  const ARIA_ATTR = seal(/^aria-[\-\w]+$/); // eslint-disable-line no-useless-escape

  const IS_ALLOWED_URI = seal(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i // eslint-disable-line no-useless-escape
  );
  const IS_SCRIPT_OR_DATA = seal(/^(?:\w+script|data):/i);
  const ATTR_WHITESPACE = seal(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g // eslint-disable-line no-control-regex
  );
  const DOCTYPE_NAME = seal(/^html$/i);

  var EXPRESSIONS = /*#__PURE__*/Object.freeze({
    __proto__: null,
    MUSTACHE_EXPR: MUSTACHE_EXPR,
    ERB_EXPR: ERB_EXPR,
    TMPLIT_EXPR: TMPLIT_EXPR,
    DATA_ATTR: DATA_ATTR,
    ARIA_ATTR: ARIA_ATTR,
    IS_ALLOWED_URI: IS_ALLOWED_URI,
    IS_SCRIPT_OR_DATA: IS_SCRIPT_OR_DATA,
    ATTR_WHITESPACE: ATTR_WHITESPACE,
    DOCTYPE_NAME: DOCTYPE_NAME
  });

  const getGlobal = () => typeof window === 'undefined' ? null : window;
  /**
   * Creates a no-op policy for internal use only.
   * Don't export this function outside this module!
   * @param {?TrustedTypePolicyFactory} trustedTypes The policy factory.
   * @param {HTMLScriptElement} purifyHostElement The Script element used to load DOMPurify (to determine policy name suffix).
   * @return {?TrustedTypePolicy} The policy created (or null, if Trusted Types
   * are not supported or creating the policy failed).
   */


  const _createTrustedTypesPolicy = function _createTrustedTypesPolicy(trustedTypes, purifyHostElement) {
    if (typeof trustedTypes !== 'object' || typeof trustedTypes.createPolicy !== 'function') {
      return null;
    } // Allow the callers to control the unique policy name
    // by adding a data-tt-policy-suffix to the script element with the DOMPurify.
    // Policy creation with duplicate names throws in Trusted Types.


    let suffix = null;
    const ATTR_NAME = 'data-tt-policy-suffix';

    if (purifyHostElement && purifyHostElement.hasAttribute(ATTR_NAME)) {
      suffix = purifyHostElement.getAttribute(ATTR_NAME);
    }

    const policyName = 'dompurify' + (suffix ? '#' + suffix : '');

    try {
      return trustedTypes.createPolicy(policyName, {
        createHTML(html) {
          return html;
        },

        createScriptURL(scriptUrl) {
          return scriptUrl;
        }

      });
    } catch (_) {
      // Policy creation failed (most likely another DOMPurify script has
      // already run). Skip creating the policy, as this will only cause errors
      // if TT are enforced.
      console.warn('TrustedTypes policy ' + policyName + ' could not be created.');
      return null;
    }
  };

  function createDOMPurify() {
    let window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : getGlobal();

    const DOMPurify = root => createDOMPurify(root);
    /**
     * Version label, exposed for easier checks
     * if DOMPurify is up to date or not
     */


    DOMPurify.version = '3.0.3';
    /**
     * Array of elements that DOMPurify removed during sanitation.
     * Empty if nothing was removed.
     */

    DOMPurify.removed = [];

    if (!window || !window.document || window.document.nodeType !== 9) {
      // Not running in a browser, provide a factory function
      // so that you can pass your own Window
      DOMPurify.isSupported = false;
      return DOMPurify;
    }

    const originalDocument = window.document;
    const currentScript = originalDocument.currentScript;
    let {
      document
    } = window;
    const {
      DocumentFragment,
      HTMLTemplateElement,
      Node,
      Element,
      NodeFilter,
      NamedNodeMap = window.NamedNodeMap || window.MozNamedAttrMap,
      HTMLFormElement,
      DOMParser,
      trustedTypes
    } = window;
    const ElementPrototype = Element.prototype;
    const cloneNode = lookupGetter(ElementPrototype, 'cloneNode');
    const getNextSibling = lookupGetter(ElementPrototype, 'nextSibling');
    const getChildNodes = lookupGetter(ElementPrototype, 'childNodes');
    const getParentNode = lookupGetter(ElementPrototype, 'parentNode'); // As per issue #47, the web-components registry is inherited by a
    // new document created via createHTMLDocument. As per the spec
    // (http://w3c.github.io/webcomponents/spec/custom/#creating-and-passing-registries)
    // a new empty registry is used when creating a template contents owner
    // document, so we use that as our parent document to ensure nothing
    // is inherited.

    if (typeof HTMLTemplateElement === 'function') {
      const template = document.createElement('template');

      if (template.content && template.content.ownerDocument) {
        document = template.content.ownerDocument;
      }
    }

    let trustedTypesPolicy;
    let emptyHTML = '';
    const {
      implementation,
      createNodeIterator,
      createDocumentFragment,
      getElementsByTagName
    } = document;
    const {
      importNode
    } = originalDocument;
    let hooks = {};
    /**
     * Expose whether this browser supports running the full DOMPurify.
     */

    DOMPurify.isSupported = typeof entries === 'function' && typeof getParentNode === 'function' && implementation && implementation.createHTMLDocument !== undefined;
    const {
      MUSTACHE_EXPR,
      ERB_EXPR,
      TMPLIT_EXPR,
      DATA_ATTR,
      ARIA_ATTR,
      IS_SCRIPT_OR_DATA,
      ATTR_WHITESPACE
    } = EXPRESSIONS;
    let {
      IS_ALLOWED_URI: IS_ALLOWED_URI$1
    } = EXPRESSIONS;
    /**
     * We consider the elements and attributes below to be safe. Ideally
     * don't add any new ones but feel free to remove unwanted ones.
     */

    /* allowed element names */

    let ALLOWED_TAGS = null;
    const DEFAULT_ALLOWED_TAGS = addToSet({}, [...html$1, ...svg$1, ...svgFilters, ...mathMl$1, ...text]);
    /* Allowed attribute names */

    let ALLOWED_ATTR = null;
    const DEFAULT_ALLOWED_ATTR = addToSet({}, [...html, ...svg, ...mathMl, ...xml]);
    /*
     * Configure how DOMPUrify should handle custom elements and their attributes as well as customized built-in elements.
     * @property {RegExp|Function|null} tagNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any custom elements)
     * @property {RegExp|Function|null} attributeNameCheck one of [null, regexPattern, predicate]. Default: `null` (disallow any attributes not on the allow list)
     * @property {boolean} allowCustomizedBuiltInElements allow custom elements derived from built-ins if they pass CUSTOM_ELEMENT_HANDLING.tagNameCheck. Default: `false`.
     */

    let CUSTOM_ELEMENT_HANDLING = Object.seal(Object.create(null, {
      tagNameCheck: {
        writable: true,
        configurable: false,
        enumerable: true,
        value: null
      },
      attributeNameCheck: {
        writable: true,
        configurable: false,
        enumerable: true,
        value: null
      },
      allowCustomizedBuiltInElements: {
        writable: true,
        configurable: false,
        enumerable: true,
        value: false
      }
    }));
    /* Explicitly forbidden tags (overrides ALLOWED_TAGS/ADD_TAGS) */

    let FORBID_TAGS = null;
    /* Explicitly forbidden attributes (overrides ALLOWED_ATTR/ADD_ATTR) */

    let FORBID_ATTR = null;
    /* Decide if ARIA attributes are okay */

    let ALLOW_ARIA_ATTR = true;
    /* Decide if custom data attributes are okay */

    let ALLOW_DATA_ATTR = true;
    /* Decide if unknown protocols are okay */

    let ALLOW_UNKNOWN_PROTOCOLS = false;
    /* Decide if self-closing tags in attributes are allowed.
     * Usually removed due to a mXSS issue in jQuery 3.0 */

    let ALLOW_SELF_CLOSE_IN_ATTR = true;
    /* Output should be safe for common template engines.
     * This means, DOMPurify removes data attributes, mustaches and ERB
     */

    let SAFE_FOR_TEMPLATES = false;
    /* Decide if document with <html>... should be returned */

    let WHOLE_DOCUMENT = false;
    /* Track whether config is already set on this instance of DOMPurify. */

    let SET_CONFIG = false;
    /* Decide if all elements (e.g. style, script) must be children of
     * document.body. By default, browsers might move them to document.head */

    let FORCE_BODY = false;
    /* Decide if a DOM `HTMLBodyElement` should be returned, instead of a html
     * string (or a TrustedHTML object if Trusted Types are supported).
     * If `WHOLE_DOCUMENT` is enabled a `HTMLHtmlElement` will be returned instead
     */

    let RETURN_DOM = false;
    /* Decide if a DOM `DocumentFragment` should be returned, instead of a html
     * string  (or a TrustedHTML object if Trusted Types are supported) */

    let RETURN_DOM_FRAGMENT = false;
    /* Try to return a Trusted Type object instead of a string, return a string in
     * case Trusted Types are not supported  */

    let RETURN_TRUSTED_TYPE = false;
    /* Output should be free from DOM clobbering attacks?
     * This sanitizes markups named with colliding, clobberable built-in DOM APIs.
     */

    let SANITIZE_DOM = true;
    /* Achieve full DOM Clobbering protection by isolating the namespace of named
     * properties and JS variables, mitigating attacks that abuse the HTML/DOM spec rules.
     *
     * HTML/DOM spec rules that enable DOM Clobbering:
     *   - Named Access on Window (§7.3.3)
     *   - DOM Tree Accessors (§3.1.5)
     *   - Form Element Parent-Child Relations (§4.10.3)
     *   - Iframe srcdoc / Nested WindowProxies (§4.8.5)
     *   - HTMLCollection (§4.2.10.2)
     *
     * Namespace isolation is implemented by prefixing `id` and `name` attributes
     * with a constant string, i.e., `user-content-`
     */

    let SANITIZE_NAMED_PROPS = false;
    const SANITIZE_NAMED_PROPS_PREFIX = 'user-content-';
    /* Keep element content when removing element? */

    let KEEP_CONTENT = true;
    /* If a `Node` is passed to sanitize(), then performs sanitization in-place instead
     * of importing it into a new Document and returning a sanitized copy */

    let IN_PLACE = false;
    /* Allow usage of profiles like html, svg and mathMl */

    let USE_PROFILES = {};
    /* Tags to ignore content of when KEEP_CONTENT is true */

    let FORBID_CONTENTS = null;
    const DEFAULT_FORBID_CONTENTS = addToSet({}, ['annotation-xml', 'audio', 'colgroup', 'desc', 'foreignobject', 'head', 'iframe', 'math', 'mi', 'mn', 'mo', 'ms', 'mtext', 'noembed', 'noframes', 'noscript', 'plaintext', 'script', 'style', 'svg', 'template', 'thead', 'title', 'video', 'xmp']);
    /* Tags that are safe for data: URIs */

    let DATA_URI_TAGS = null;
    const DEFAULT_DATA_URI_TAGS = addToSet({}, ['audio', 'video', 'img', 'source', 'image', 'track']);
    /* Attributes safe for values like "javascript:" */

    let URI_SAFE_ATTRIBUTES = null;
    const DEFAULT_URI_SAFE_ATTRIBUTES = addToSet({}, ['alt', 'class', 'for', 'id', 'label', 'name', 'pattern', 'placeholder', 'role', 'summary', 'title', 'value', 'style', 'xmlns']);
    const MATHML_NAMESPACE = 'http://www.w3.org/1998/Math/MathML';
    const SVG_NAMESPACE = 'http://www.w3.org/2000/svg';
    const HTML_NAMESPACE = 'http://www.w3.org/1999/xhtml';
    /* Document namespace */

    let NAMESPACE = HTML_NAMESPACE;
    let IS_EMPTY_INPUT = false;
    /* Allowed XHTML+XML namespaces */

    let ALLOWED_NAMESPACES = null;
    const DEFAULT_ALLOWED_NAMESPACES = addToSet({}, [MATHML_NAMESPACE, SVG_NAMESPACE, HTML_NAMESPACE], stringToString);
    /* Parsing of strict XHTML documents */

    let PARSER_MEDIA_TYPE;
    const SUPPORTED_PARSER_MEDIA_TYPES = ['application/xhtml+xml', 'text/html'];
    const DEFAULT_PARSER_MEDIA_TYPE = 'text/html';
    let transformCaseFunc;
    /* Keep a reference to config to pass to hooks */

    let CONFIG = null;
    /* Ideally, do not touch anything below this line */

    /* ______________________________________________ */

    const formElement = document.createElement('form');

    const isRegexOrFunction = function isRegexOrFunction(testValue) {
      return testValue instanceof RegExp || testValue instanceof Function;
    };
    /**
     * _parseConfig
     *
     * @param  {Object} cfg optional config literal
     */
    // eslint-disable-next-line complexity


    const _parseConfig = function _parseConfig(cfg) {
      if (CONFIG && CONFIG === cfg) {
        return;
      }
      /* Shield configuration object from tampering */


      if (!cfg || typeof cfg !== 'object') {
        cfg = {};
      }
      /* Shield configuration object from prototype pollution */


      cfg = clone(cfg);
      PARSER_MEDIA_TYPE = // eslint-disable-next-line unicorn/prefer-includes
      SUPPORTED_PARSER_MEDIA_TYPES.indexOf(cfg.PARSER_MEDIA_TYPE) === -1 ? PARSER_MEDIA_TYPE = DEFAULT_PARSER_MEDIA_TYPE : PARSER_MEDIA_TYPE = cfg.PARSER_MEDIA_TYPE; // HTML tags and attributes are not case-sensitive, converting to lowercase. Keeping XHTML as is.

      transformCaseFunc = PARSER_MEDIA_TYPE === 'application/xhtml+xml' ? stringToString : stringToLowerCase;
      /* Set configuration parameters */

      ALLOWED_TAGS = 'ALLOWED_TAGS' in cfg ? addToSet({}, cfg.ALLOWED_TAGS, transformCaseFunc) : DEFAULT_ALLOWED_TAGS;
      ALLOWED_ATTR = 'ALLOWED_ATTR' in cfg ? addToSet({}, cfg.ALLOWED_ATTR, transformCaseFunc) : DEFAULT_ALLOWED_ATTR;
      ALLOWED_NAMESPACES = 'ALLOWED_NAMESPACES' in cfg ? addToSet({}, cfg.ALLOWED_NAMESPACES, stringToString) : DEFAULT_ALLOWED_NAMESPACES;
      URI_SAFE_ATTRIBUTES = 'ADD_URI_SAFE_ATTR' in cfg ? addToSet(clone(DEFAULT_URI_SAFE_ATTRIBUTES), // eslint-disable-line indent
      cfg.ADD_URI_SAFE_ATTR, // eslint-disable-line indent
      transformCaseFunc // eslint-disable-line indent
      ) // eslint-disable-line indent
      : DEFAULT_URI_SAFE_ATTRIBUTES;
      DATA_URI_TAGS = 'ADD_DATA_URI_TAGS' in cfg ? addToSet(clone(DEFAULT_DATA_URI_TAGS), // eslint-disable-line indent
      cfg.ADD_DATA_URI_TAGS, // eslint-disable-line indent
      transformCaseFunc // eslint-disable-line indent
      ) // eslint-disable-line indent
      : DEFAULT_DATA_URI_TAGS;
      FORBID_CONTENTS = 'FORBID_CONTENTS' in cfg ? addToSet({}, cfg.FORBID_CONTENTS, transformCaseFunc) : DEFAULT_FORBID_CONTENTS;
      FORBID_TAGS = 'FORBID_TAGS' in cfg ? addToSet({}, cfg.FORBID_TAGS, transformCaseFunc) : {};
      FORBID_ATTR = 'FORBID_ATTR' in cfg ? addToSet({}, cfg.FORBID_ATTR, transformCaseFunc) : {};
      USE_PROFILES = 'USE_PROFILES' in cfg ? cfg.USE_PROFILES : false;
      ALLOW_ARIA_ATTR = cfg.ALLOW_ARIA_ATTR !== false; // Default true

      ALLOW_DATA_ATTR = cfg.ALLOW_DATA_ATTR !== false; // Default true

      ALLOW_UNKNOWN_PROTOCOLS = cfg.ALLOW_UNKNOWN_PROTOCOLS || false; // Default false

      ALLOW_SELF_CLOSE_IN_ATTR = cfg.ALLOW_SELF_CLOSE_IN_ATTR !== false; // Default true

      SAFE_FOR_TEMPLATES = cfg.SAFE_FOR_TEMPLATES || false; // Default false

      WHOLE_DOCUMENT = cfg.WHOLE_DOCUMENT || false; // Default false

      RETURN_DOM = cfg.RETURN_DOM || false; // Default false

      RETURN_DOM_FRAGMENT = cfg.RETURN_DOM_FRAGMENT || false; // Default false

      RETURN_TRUSTED_TYPE = cfg.RETURN_TRUSTED_TYPE || false; // Default false

      FORCE_BODY = cfg.FORCE_BODY || false; // Default false

      SANITIZE_DOM = cfg.SANITIZE_DOM !== false; // Default true

      SANITIZE_NAMED_PROPS = cfg.SANITIZE_NAMED_PROPS || false; // Default false

      KEEP_CONTENT = cfg.KEEP_CONTENT !== false; // Default true

      IN_PLACE = cfg.IN_PLACE || false; // Default false

      IS_ALLOWED_URI$1 = cfg.ALLOWED_URI_REGEXP || IS_ALLOWED_URI;
      NAMESPACE = cfg.NAMESPACE || HTML_NAMESPACE;
      CUSTOM_ELEMENT_HANDLING = cfg.CUSTOM_ELEMENT_HANDLING || {};

      if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck)) {
        CUSTOM_ELEMENT_HANDLING.tagNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.tagNameCheck;
      }

      if (cfg.CUSTOM_ELEMENT_HANDLING && isRegexOrFunction(cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck)) {
        CUSTOM_ELEMENT_HANDLING.attributeNameCheck = cfg.CUSTOM_ELEMENT_HANDLING.attributeNameCheck;
      }

      if (cfg.CUSTOM_ELEMENT_HANDLING && typeof cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements === 'boolean') {
        CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements = cfg.CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements;
      }

      if (SAFE_FOR_TEMPLATES) {
        ALLOW_DATA_ATTR = false;
      }

      if (RETURN_DOM_FRAGMENT) {
        RETURN_DOM = true;
      }
      /* Parse profile info */


      if (USE_PROFILES) {
        ALLOWED_TAGS = addToSet({}, [...text]);
        ALLOWED_ATTR = [];

        if (USE_PROFILES.html === true) {
          addToSet(ALLOWED_TAGS, html$1);
          addToSet(ALLOWED_ATTR, html);
        }

        if (USE_PROFILES.svg === true) {
          addToSet(ALLOWED_TAGS, svg$1);
          addToSet(ALLOWED_ATTR, svg);
          addToSet(ALLOWED_ATTR, xml);
        }

        if (USE_PROFILES.svgFilters === true) {
          addToSet(ALLOWED_TAGS, svgFilters);
          addToSet(ALLOWED_ATTR, svg);
          addToSet(ALLOWED_ATTR, xml);
        }

        if (USE_PROFILES.mathMl === true) {
          addToSet(ALLOWED_TAGS, mathMl$1);
          addToSet(ALLOWED_ATTR, mathMl);
          addToSet(ALLOWED_ATTR, xml);
        }
      }
      /* Merge configuration parameters */


      if (cfg.ADD_TAGS) {
        if (ALLOWED_TAGS === DEFAULT_ALLOWED_TAGS) {
          ALLOWED_TAGS = clone(ALLOWED_TAGS);
        }

        addToSet(ALLOWED_TAGS, cfg.ADD_TAGS, transformCaseFunc);
      }

      if (cfg.ADD_ATTR) {
        if (ALLOWED_ATTR === DEFAULT_ALLOWED_ATTR) {
          ALLOWED_ATTR = clone(ALLOWED_ATTR);
        }

        addToSet(ALLOWED_ATTR, cfg.ADD_ATTR, transformCaseFunc);
      }

      if (cfg.ADD_URI_SAFE_ATTR) {
        addToSet(URI_SAFE_ATTRIBUTES, cfg.ADD_URI_SAFE_ATTR, transformCaseFunc);
      }

      if (cfg.FORBID_CONTENTS) {
        if (FORBID_CONTENTS === DEFAULT_FORBID_CONTENTS) {
          FORBID_CONTENTS = clone(FORBID_CONTENTS);
        }

        addToSet(FORBID_CONTENTS, cfg.FORBID_CONTENTS, transformCaseFunc);
      }
      /* Add #text in case KEEP_CONTENT is set to true */


      if (KEEP_CONTENT) {
        ALLOWED_TAGS['#text'] = true;
      }
      /* Add html, head and body to ALLOWED_TAGS in case WHOLE_DOCUMENT is true */


      if (WHOLE_DOCUMENT) {
        addToSet(ALLOWED_TAGS, ['html', 'head', 'body']);
      }
      /* Add tbody to ALLOWED_TAGS in case tables are permitted, see #286, #365 */


      if (ALLOWED_TAGS.table) {
        addToSet(ALLOWED_TAGS, ['tbody']);
        delete FORBID_TAGS.tbody;
      }

      if (cfg.TRUSTED_TYPES_POLICY) {
        if (typeof cfg.TRUSTED_TYPES_POLICY.createHTML !== 'function') {
          throw typeErrorCreate('TRUSTED_TYPES_POLICY configuration option must provide a "createHTML" hook.');
        }

        if (typeof cfg.TRUSTED_TYPES_POLICY.createScriptURL !== 'function') {
          throw typeErrorCreate('TRUSTED_TYPES_POLICY configuration option must provide a "createScriptURL" hook.');
        } // Overwrite existing TrustedTypes policy.


        trustedTypesPolicy = cfg.TRUSTED_TYPES_POLICY; // Sign local variables required by `sanitize`.

        emptyHTML = trustedTypesPolicy.createHTML('');
      } else {
        // Uninitialized policy, attempt to initialize the internal dompurify policy.
        if (trustedTypesPolicy === undefined) {
          trustedTypesPolicy = _createTrustedTypesPolicy(trustedTypes, currentScript);
        } // If creating the internal policy succeeded sign internal variables.


        if (trustedTypesPolicy !== null && typeof emptyHTML === 'string') {
          emptyHTML = trustedTypesPolicy.createHTML('');
        }
      } // Prevent further manipulation of configuration.
      // Not available in IE8, Safari 5, etc.


      if (freeze) {
        freeze(cfg);
      }

      CONFIG = cfg;
    };

    const MATHML_TEXT_INTEGRATION_POINTS = addToSet({}, ['mi', 'mo', 'mn', 'ms', 'mtext']);
    const HTML_INTEGRATION_POINTS = addToSet({}, ['foreignobject', 'desc', 'title', 'annotation-xml']); // Certain elements are allowed in both SVG and HTML
    // namespace. We need to specify them explicitly
    // so that they don't get erroneously deleted from
    // HTML namespace.

    const COMMON_SVG_AND_HTML_ELEMENTS = addToSet({}, ['title', 'style', 'font', 'a', 'script']);
    /* Keep track of all possible SVG and MathML tags
     * so that we can perform the namespace checks
     * correctly. */

    const ALL_SVG_TAGS = addToSet({}, svg$1);
    addToSet(ALL_SVG_TAGS, svgFilters);
    addToSet(ALL_SVG_TAGS, svgDisallowed);
    const ALL_MATHML_TAGS = addToSet({}, mathMl$1);
    addToSet(ALL_MATHML_TAGS, mathMlDisallowed);
    /**
     *
     *
     * @param  {Element} element a DOM element whose namespace is being checked
     * @returns {boolean} Return false if the element has a
     *  namespace that a spec-compliant parser would never
     *  return. Return true otherwise.
     */

    const _checkValidNamespace = function _checkValidNamespace(element) {
      let parent = getParentNode(element); // In JSDOM, if we're inside shadow DOM, then parentNode
      // can be null. We just simulate parent in this case.

      if (!parent || !parent.tagName) {
        parent = {
          namespaceURI: NAMESPACE,
          tagName: 'template'
        };
      }

      const tagName = stringToLowerCase(element.tagName);
      const parentTagName = stringToLowerCase(parent.tagName);

      if (!ALLOWED_NAMESPACES[element.namespaceURI]) {
        return false;
      }

      if (element.namespaceURI === SVG_NAMESPACE) {
        // The only way to switch from HTML namespace to SVG
        // is via <svg>. If it happens via any other tag, then
        // it should be killed.
        if (parent.namespaceURI === HTML_NAMESPACE) {
          return tagName === 'svg';
        } // The only way to switch from MathML to SVG is via`
        // svg if parent is either <annotation-xml> or MathML
        // text integration points.


        if (parent.namespaceURI === MATHML_NAMESPACE) {
          return tagName === 'svg' && (parentTagName === 'annotation-xml' || MATHML_TEXT_INTEGRATION_POINTS[parentTagName]);
        } // We only allow elements that are defined in SVG
        // spec. All others are disallowed in SVG namespace.


        return Boolean(ALL_SVG_TAGS[tagName]);
      }

      if (element.namespaceURI === MATHML_NAMESPACE) {
        // The only way to switch from HTML namespace to MathML
        // is via <math>. If it happens via any other tag, then
        // it should be killed.
        if (parent.namespaceURI === HTML_NAMESPACE) {
          return tagName === 'math';
        } // The only way to switch from SVG to MathML is via
        // <math> and HTML integration points


        if (parent.namespaceURI === SVG_NAMESPACE) {
          return tagName === 'math' && HTML_INTEGRATION_POINTS[parentTagName];
        } // We only allow elements that are defined in MathML
        // spec. All others are disallowed in MathML namespace.


        return Boolean(ALL_MATHML_TAGS[tagName]);
      }

      if (element.namespaceURI === HTML_NAMESPACE) {
        // The only way to switch from SVG to HTML is via
        // HTML integration points, and from MathML to HTML
        // is via MathML text integration points
        if (parent.namespaceURI === SVG_NAMESPACE && !HTML_INTEGRATION_POINTS[parentTagName]) {
          return false;
        }

        if (parent.namespaceURI === MATHML_NAMESPACE && !MATHML_TEXT_INTEGRATION_POINTS[parentTagName]) {
          return false;
        } // We disallow tags that are specific for MathML
        // or SVG and should never appear in HTML namespace


        return !ALL_MATHML_TAGS[tagName] && (COMMON_SVG_AND_HTML_ELEMENTS[tagName] || !ALL_SVG_TAGS[tagName]);
      } // For XHTML and XML documents that support custom namespaces


      if (PARSER_MEDIA_TYPE === 'application/xhtml+xml' && ALLOWED_NAMESPACES[element.namespaceURI]) {
        return true;
      } // The code should never reach this place (this means
      // that the element somehow got namespace that is not
      // HTML, SVG, MathML or allowed via ALLOWED_NAMESPACES).
      // Return false just in case.


      return false;
    };
    /**
     * _forceRemove
     *
     * @param  {Node} node a DOM node
     */


    const _forceRemove = function _forceRemove(node) {
      arrayPush(DOMPurify.removed, {
        element: node
      });

      try {
        // eslint-disable-next-line unicorn/prefer-dom-node-remove
        node.parentNode.removeChild(node);
      } catch (_) {
        node.remove();
      }
    };
    /**
     * _removeAttribute
     *
     * @param  {String} name an Attribute name
     * @param  {Node} node a DOM node
     */


    const _removeAttribute = function _removeAttribute(name, node) {
      try {
        arrayPush(DOMPurify.removed, {
          attribute: node.getAttributeNode(name),
          from: node
        });
      } catch (_) {
        arrayPush(DOMPurify.removed, {
          attribute: null,
          from: node
        });
      }

      node.removeAttribute(name); // We void attribute values for unremovable "is"" attributes

      if (name === 'is' && !ALLOWED_ATTR[name]) {
        if (RETURN_DOM || RETURN_DOM_FRAGMENT) {
          try {
            _forceRemove(node);
          } catch (_) {}
        } else {
          try {
            node.setAttribute(name, '');
          } catch (_) {}
        }
      }
    };
    /**
     * _initDocument
     *
     * @param  {String} dirty a string of dirty markup
     * @return {Document} a DOM, filled with the dirty markup
     */


    const _initDocument = function _initDocument(dirty) {
      /* Create a HTML document */
      let doc;
      let leadingWhitespace;

      if (FORCE_BODY) {
        dirty = '<remove></remove>' + dirty;
      } else {
        /* If FORCE_BODY isn't used, leading whitespace needs to be preserved manually */
        const matches = stringMatch(dirty, /^[\r\n\t ]+/);
        leadingWhitespace = matches && matches[0];
      }

      if (PARSER_MEDIA_TYPE === 'application/xhtml+xml' && NAMESPACE === HTML_NAMESPACE) {
        // Root of XHTML doc must contain xmlns declaration (see https://www.w3.org/TR/xhtml1/normative.html#strict)
        dirty = '<html xmlns="http://www.w3.org/1999/xhtml"><head></head><body>' + dirty + '</body></html>';
      }

      const dirtyPayload = trustedTypesPolicy ? trustedTypesPolicy.createHTML(dirty) : dirty;
      /*
       * Use the DOMParser API by default, fallback later if needs be
       * DOMParser not work for svg when has multiple root element.
       */

      if (NAMESPACE === HTML_NAMESPACE) {
        try {
          doc = new DOMParser().parseFromString(dirtyPayload, PARSER_MEDIA_TYPE);
        } catch (_) {}
      }
      /* Use createHTMLDocument in case DOMParser is not available */


      if (!doc || !doc.documentElement) {
        doc = implementation.createDocument(NAMESPACE, 'template', null);

        try {
          doc.documentElement.innerHTML = IS_EMPTY_INPUT ? emptyHTML : dirtyPayload;
        } catch (_) {// Syntax error if dirtyPayload is invalid xml
        }
      }

      const body = doc.body || doc.documentElement;

      if (dirty && leadingWhitespace) {
        body.insertBefore(document.createTextNode(leadingWhitespace), body.childNodes[0] || null);
      }
      /* Work on whole document or just its body */


      if (NAMESPACE === HTML_NAMESPACE) {
        return getElementsByTagName.call(doc, WHOLE_DOCUMENT ? 'html' : 'body')[0];
      }

      return WHOLE_DOCUMENT ? doc.documentElement : body;
    };
    /**
     * _createIterator
     *
     * @param  {Document} root document/fragment to create iterator for
     * @return {Iterator} iterator instance
     */


    const _createIterator = function _createIterator(root) {
      return createNodeIterator.call(root.ownerDocument || root, root, // eslint-disable-next-line no-bitwise
      NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT, null, false);
    };
    /**
     * _isClobbered
     *
     * @param  {Node} elm element to check for clobbering attacks
     * @return {Boolean} true if clobbered, false if safe
     */


    const _isClobbered = function _isClobbered(elm) {
      return elm instanceof HTMLFormElement && (typeof elm.nodeName !== 'string' || typeof elm.textContent !== 'string' || typeof elm.removeChild !== 'function' || !(elm.attributes instanceof NamedNodeMap) || typeof elm.removeAttribute !== 'function' || typeof elm.setAttribute !== 'function' || typeof elm.namespaceURI !== 'string' || typeof elm.insertBefore !== 'function' || typeof elm.hasChildNodes !== 'function');
    };
    /**
     * _isNode
     *
     * @param  {Node} obj object to check whether it's a DOM node
     * @return {Boolean} true is object is a DOM node
     */


    const _isNode = function _isNode(object) {
      return typeof Node === 'object' ? object instanceof Node : object && typeof object === 'object' && typeof object.nodeType === 'number' && typeof object.nodeName === 'string';
    };
    /**
     * _executeHook
     * Execute user configurable hooks
     *
     * @param  {String} entryPoint  Name of the hook's entry point
     * @param  {Node} currentNode node to work on with the hook
     * @param  {Object} data additional hook parameters
     */


    const _executeHook = function _executeHook(entryPoint, currentNode, data) {
      if (!hooks[entryPoint]) {
        return;
      }

      arrayForEach(hooks[entryPoint], hook => {
        hook.call(DOMPurify, currentNode, data, CONFIG);
      });
    };
    /**
     * _sanitizeElements
     *
     * @protect nodeName
     * @protect textContent
     * @protect removeChild
     *
     * @param   {Node} currentNode to check for permission to exist
     * @return  {Boolean} true if node was killed, false if left alive
     */


    const _sanitizeElements = function _sanitizeElements(currentNode) {
      let content;
      /* Execute a hook if present */

      _executeHook('beforeSanitizeElements', currentNode, null);
      /* Check if element is clobbered or can clobber */


      if (_isClobbered(currentNode)) {
        _forceRemove(currentNode);

        return true;
      }
      /* Now let's check the element's type and name */


      const tagName = transformCaseFunc(currentNode.nodeName);
      /* Execute a hook if present */

      _executeHook('uponSanitizeElement', currentNode, {
        tagName,
        allowedTags: ALLOWED_TAGS
      });
      /* Detect mXSS attempts abusing namespace confusion */


      if (currentNode.hasChildNodes() && !_isNode(currentNode.firstElementChild) && (!_isNode(currentNode.content) || !_isNode(currentNode.content.firstElementChild)) && regExpTest(/<[/\w]/g, currentNode.innerHTML) && regExpTest(/<[/\w]/g, currentNode.textContent)) {
        _forceRemove(currentNode);

        return true;
      }
      /* Remove element if anything forbids its presence */


      if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
        /* Check if we have a custom element to handle */
        if (!FORBID_TAGS[tagName] && _basicCustomElementTest(tagName)) {
          if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, tagName)) return false;
          if (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(tagName)) return false;
        }
        /* Keep content except for bad-listed elements */


        if (KEEP_CONTENT && !FORBID_CONTENTS[tagName]) {
          const parentNode = getParentNode(currentNode) || currentNode.parentNode;
          const childNodes = getChildNodes(currentNode) || currentNode.childNodes;

          if (childNodes && parentNode) {
            const childCount = childNodes.length;

            for (let i = childCount - 1; i >= 0; --i) {
              parentNode.insertBefore(cloneNode(childNodes[i], true), getNextSibling(currentNode));
            }
          }
        }

        _forceRemove(currentNode);

        return true;
      }
      /* Check whether element has a valid namespace */


      if (currentNode instanceof Element && !_checkValidNamespace(currentNode)) {
        _forceRemove(currentNode);

        return true;
      }
      /* Make sure that older browsers don't get noscript mXSS */


      if ((tagName === 'noscript' || tagName === 'noembed') && regExpTest(/<\/no(script|embed)/i, currentNode.innerHTML)) {
        _forceRemove(currentNode);

        return true;
      }
      /* Sanitize element content to be template-safe */


      if (SAFE_FOR_TEMPLATES && currentNode.nodeType === 3) {
        /* Get the element's text content */
        content = currentNode.textContent;
        content = stringReplace(content, MUSTACHE_EXPR, ' ');
        content = stringReplace(content, ERB_EXPR, ' ');
        content = stringReplace(content, TMPLIT_EXPR, ' ');

        if (currentNode.textContent !== content) {
          arrayPush(DOMPurify.removed, {
            element: currentNode.cloneNode()
          });
          currentNode.textContent = content;
        }
      }
      /* Execute a hook if present */


      _executeHook('afterSanitizeElements', currentNode, null);

      return false;
    };
    /**
     * _isValidAttribute
     *
     * @param  {string} lcTag Lowercase tag name of containing element.
     * @param  {string} lcName Lowercase attribute name.
     * @param  {string} value Attribute value.
     * @return {Boolean} Returns true if `value` is valid, otherwise false.
     */
    // eslint-disable-next-line complexity


    const _isValidAttribute = function _isValidAttribute(lcTag, lcName, value) {
      /* Make sure attribute cannot clobber */
      if (SANITIZE_DOM && (lcName === 'id' || lcName === 'name') && (value in document || value in formElement)) {
        return false;
      }
      /* Allow valid data-* attributes: At least one character after "-"
          (https://html.spec.whatwg.org/multipage/dom.html#embedding-custom-non-visible-data-with-the-data-*-attributes)
          XML-compatible (https://html.spec.whatwg.org/multipage/infrastructure.html#xml-compatible and http://www.w3.org/TR/xml/#d0e804)
          We don't need to check the value; it's always URI safe. */


      if (ALLOW_DATA_ATTR && !FORBID_ATTR[lcName] && regExpTest(DATA_ATTR, lcName)) ; else if (ALLOW_ARIA_ATTR && regExpTest(ARIA_ATTR, lcName)) ; else if (!ALLOWED_ATTR[lcName] || FORBID_ATTR[lcName]) {
        if ( // First condition does a very basic check if a) it's basically a valid custom element tagname AND
        // b) if the tagName passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
        // and c) if the attribute name passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.attributeNameCheck
        _basicCustomElementTest(lcTag) && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, lcTag) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(lcTag)) && (CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.attributeNameCheck, lcName) || CUSTOM_ELEMENT_HANDLING.attributeNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.attributeNameCheck(lcName)) || // Alternative, second condition checks if it's an `is`-attribute, AND
        // the value passes whatever the user has configured for CUSTOM_ELEMENT_HANDLING.tagNameCheck
        lcName === 'is' && CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements && (CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof RegExp && regExpTest(CUSTOM_ELEMENT_HANDLING.tagNameCheck, value) || CUSTOM_ELEMENT_HANDLING.tagNameCheck instanceof Function && CUSTOM_ELEMENT_HANDLING.tagNameCheck(value))) ; else {
          return false;
        }
        /* Check value is safe. First, is attr inert? If so, is safe */

      } else if (URI_SAFE_ATTRIBUTES[lcName]) ; else if (regExpTest(IS_ALLOWED_URI$1, stringReplace(value, ATTR_WHITESPACE, ''))) ; else if ((lcName === 'src' || lcName === 'xlink:href' || lcName === 'href') && lcTag !== 'script' && stringIndexOf(value, 'data:') === 0 && DATA_URI_TAGS[lcTag]) ; else if (ALLOW_UNKNOWN_PROTOCOLS && !regExpTest(IS_SCRIPT_OR_DATA, stringReplace(value, ATTR_WHITESPACE, ''))) ; else if (value) {
        return false;
      } else ;

      return true;
    };
    /**
     * _basicCustomElementCheck
     * checks if at least one dash is included in tagName, and it's not the first char
     * for more sophisticated checking see https://github.com/sindresorhus/validate-element-name
     * @param {string} tagName name of the tag of the node to sanitize
     */


    const _basicCustomElementTest = function _basicCustomElementTest(tagName) {
      return tagName.indexOf('-') > 0;
    };
    /**
     * _sanitizeAttributes
     *
     * @protect attributes
     * @protect nodeName
     * @protect removeAttribute
     * @protect setAttribute
     *
     * @param  {Node} currentNode to sanitize
     */


    const _sanitizeAttributes = function _sanitizeAttributes(currentNode) {
      let attr;
      let value;
      let lcName;
      let l;
      /* Execute a hook if present */

      _executeHook('beforeSanitizeAttributes', currentNode, null);

      const {
        attributes
      } = currentNode;
      /* Check if we have attributes; if not we might have a text node */

      if (!attributes) {
        return;
      }

      const hookEvent = {
        attrName: '',
        attrValue: '',
        keepAttr: true,
        allowedAttributes: ALLOWED_ATTR
      };
      l = attributes.length;
      /* Go backwards over all attributes; safely remove bad ones */

      while (l--) {
        attr = attributes[l];
        const {
          name,
          namespaceURI
        } = attr;
        value = name === 'value' ? attr.value : stringTrim(attr.value);
        lcName = transformCaseFunc(name);
        /* Execute a hook if present */

        hookEvent.attrName = lcName;
        hookEvent.attrValue = value;
        hookEvent.keepAttr = true;
        hookEvent.forceKeepAttr = undefined; // Allows developers to see this is a property they can set

        _executeHook('uponSanitizeAttribute', currentNode, hookEvent);

        value = hookEvent.attrValue;
        /* Did the hooks approve of the attribute? */

        if (hookEvent.forceKeepAttr) {
          continue;
        }
        /* Remove attribute */


        _removeAttribute(name, currentNode);
        /* Did the hooks approve of the attribute? */


        if (!hookEvent.keepAttr) {
          continue;
        }
        /* Work around a security issue in jQuery 3.0 */


        if (!ALLOW_SELF_CLOSE_IN_ATTR && regExpTest(/\/>/i, value)) {
          _removeAttribute(name, currentNode);

          continue;
        }
        /* Sanitize attribute content to be template-safe */


        if (SAFE_FOR_TEMPLATES) {
          value = stringReplace(value, MUSTACHE_EXPR, ' ');
          value = stringReplace(value, ERB_EXPR, ' ');
          value = stringReplace(value, TMPLIT_EXPR, ' ');
        }
        /* Is `value` valid for this attribute? */


        const lcTag = transformCaseFunc(currentNode.nodeName);

        if (!_isValidAttribute(lcTag, lcName, value)) {
          continue;
        }
        /* Full DOM Clobbering protection via namespace isolation,
         * Prefix id and name attributes with `user-content-`
         */


        if (SANITIZE_NAMED_PROPS && (lcName === 'id' || lcName === 'name')) {
          // Remove the attribute with this value
          _removeAttribute(name, currentNode); // Prefix the value and later re-create the attribute with the sanitized value


          value = SANITIZE_NAMED_PROPS_PREFIX + value;
        }
        /* Handle attributes that require Trusted Types */


        if (trustedTypesPolicy && typeof trustedTypes === 'object' && typeof trustedTypes.getAttributeType === 'function') {
          if (namespaceURI) ; else {
            switch (trustedTypes.getAttributeType(lcTag, lcName)) {
              case 'TrustedHTML':
                {
                  value = trustedTypesPolicy.createHTML(value);
                  break;
                }

              case 'TrustedScriptURL':
                {
                  value = trustedTypesPolicy.createScriptURL(value);
                  break;
                }
            }
          }
        }
        /* Handle invalid data-* attribute set by try-catching it */


        try {
          if (namespaceURI) {
            currentNode.setAttributeNS(namespaceURI, name, value);
          } else {
            /* Fallback to setAttribute() for browser-unrecognized namespaces e.g. "x-schema". */
            currentNode.setAttribute(name, value);
          }

          arrayPop(DOMPurify.removed);
        } catch (_) {}
      }
      /* Execute a hook if present */


      _executeHook('afterSanitizeAttributes', currentNode, null);
    };
    /**
     * _sanitizeShadowDOM
     *
     * @param  {DocumentFragment} fragment to iterate over recursively
     */


    const _sanitizeShadowDOM = function _sanitizeShadowDOM(fragment) {
      let shadowNode;

      const shadowIterator = _createIterator(fragment);
      /* Execute a hook if present */


      _executeHook('beforeSanitizeShadowDOM', fragment, null);

      while (shadowNode = shadowIterator.nextNode()) {
        /* Execute a hook if present */
        _executeHook('uponSanitizeShadowNode', shadowNode, null);
        /* Sanitize tags and elements */


        if (_sanitizeElements(shadowNode)) {
          continue;
        }
        /* Deep shadow DOM detected */


        if (shadowNode.content instanceof DocumentFragment) {
          _sanitizeShadowDOM(shadowNode.content);
        }
        /* Check attributes, sanitize if necessary */


        _sanitizeAttributes(shadowNode);
      }
      /* Execute a hook if present */


      _executeHook('afterSanitizeShadowDOM', fragment, null);
    };
    /**
     * Sanitize
     * Public method providing core sanitation functionality
     *
     * @param {String|Node} dirty string or DOM node
     * @param {Object} configuration object
     */
    // eslint-disable-next-line complexity


    DOMPurify.sanitize = function (dirty) {
      let cfg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
      let body;
      let importedNode;
      let currentNode;
      let returnNode;
      /* Make sure we have a string to sanitize.
        DO NOT return early, as this will return the wrong type if
        the user has requested a DOM object rather than a string */

      IS_EMPTY_INPUT = !dirty;

      if (IS_EMPTY_INPUT) {
        dirty = '<!-->';
      }
      /* Stringify, in case dirty is an object */


      if (typeof dirty !== 'string' && !_isNode(dirty)) {
        if (typeof dirty.toString === 'function') {
          dirty = dirty.toString();

          if (typeof dirty !== 'string') {
            throw typeErrorCreate('dirty is not a string, aborting');
          }
        } else {
          throw typeErrorCreate('toString is not a function');
        }
      }
      /* Return dirty HTML if DOMPurify cannot run */


      if (!DOMPurify.isSupported) {
        return dirty;
      }
      /* Assign config vars */


      if (!SET_CONFIG) {
        _parseConfig(cfg);
      }
      /* Clean up removed elements */


      DOMPurify.removed = [];
      /* Check if dirty is correctly typed for IN_PLACE */

      if (typeof dirty === 'string') {
        IN_PLACE = false;
      }

      if (IN_PLACE) {
        /* Do some early pre-sanitization to avoid unsafe root nodes */
        if (dirty.nodeName) {
          const tagName = transformCaseFunc(dirty.nodeName);

          if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
            throw typeErrorCreate('root node is forbidden and cannot be sanitized in-place');
          }
        }
      } else if (dirty instanceof Node) {
        /* If dirty is a DOM element, append to an empty document to avoid
           elements being stripped by the parser */
        body = _initDocument('<!---->');
        importedNode = body.ownerDocument.importNode(dirty, true);

        if (importedNode.nodeType === 1 && importedNode.nodeName === 'BODY') {
          /* Node is already a body, use as is */
          body = importedNode;
        } else if (importedNode.nodeName === 'HTML') {
          body = importedNode;
        } else {
          // eslint-disable-next-line unicorn/prefer-dom-node-append
          body.appendChild(importedNode);
        }
      } else {
        /* Exit directly if we have nothing to do */
        if (!RETURN_DOM && !SAFE_FOR_TEMPLATES && !WHOLE_DOCUMENT && // eslint-disable-next-line unicorn/prefer-includes
        dirty.indexOf('<') === -1) {
          return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(dirty) : dirty;
        }
        /* Initialize the document to work on */


        body = _initDocument(dirty);
        /* Check we have a DOM node from the data */

        if (!body) {
          return RETURN_DOM ? null : RETURN_TRUSTED_TYPE ? emptyHTML : '';
        }
      }
      /* Remove first element node (ours) if FORCE_BODY is set */


      if (body && FORCE_BODY) {
        _forceRemove(body.firstChild);
      }
      /* Get node iterator */


      const nodeIterator = _createIterator(IN_PLACE ? dirty : body);
      /* Now start iterating over the created document */


      while (currentNode = nodeIterator.nextNode()) {
        /* Sanitize tags and elements */
        if (_sanitizeElements(currentNode)) {
          continue;
        }
        /* Shadow DOM detected, sanitize it */


        if (currentNode.content instanceof DocumentFragment) {
          _sanitizeShadowDOM(currentNode.content);
        }
        /* Check attributes, sanitize if necessary */


        _sanitizeAttributes(currentNode);
      }
      /* If we sanitized `dirty` in-place, return it. */


      if (IN_PLACE) {
        return dirty;
      }
      /* Return sanitized string or DOM */


      if (RETURN_DOM) {
        if (RETURN_DOM_FRAGMENT) {
          returnNode = createDocumentFragment.call(body.ownerDocument);

          while (body.firstChild) {
            // eslint-disable-next-line unicorn/prefer-dom-node-append
            returnNode.appendChild(body.firstChild);
          }
        } else {
          returnNode = body;
        }

        if (ALLOWED_ATTR.shadowroot || ALLOWED_ATTR.shadowrootmod) {
          /*
            AdoptNode() is not used because internal state is not reset
            (e.g. the past names map of a HTMLFormElement), this is safe
            in theory but we would rather not risk another attack vector.
            The state that is cloned by importNode() is explicitly defined
            by the specs.
          */
          returnNode = importNode.call(originalDocument, returnNode, true);
        }

        return returnNode;
      }

      let serializedHTML = WHOLE_DOCUMENT ? body.outerHTML : body.innerHTML;
      /* Serialize doctype if allowed */

      if (WHOLE_DOCUMENT && ALLOWED_TAGS['!doctype'] && body.ownerDocument && body.ownerDocument.doctype && body.ownerDocument.doctype.name && regExpTest(DOCTYPE_NAME, body.ownerDocument.doctype.name)) {
        serializedHTML = '<!DOCTYPE ' + body.ownerDocument.doctype.name + '>\n' + serializedHTML;
      }
      /* Sanitize final string template-safe */


      if (SAFE_FOR_TEMPLATES) {
        serializedHTML = stringReplace(serializedHTML, MUSTACHE_EXPR, ' ');
        serializedHTML = stringReplace(serializedHTML, ERB_EXPR, ' ');
        serializedHTML = stringReplace(serializedHTML, TMPLIT_EXPR, ' ');
      }

      return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(serializedHTML) : serializedHTML;
    };
    /**
     * Public method to set the configuration once
     * setConfig
     *
     * @param {Object} cfg configuration object
     */


    DOMPurify.setConfig = function (cfg) {
      _parseConfig(cfg);

      SET_CONFIG = true;
    };
    /**
     * Public method to remove the configuration
     * clearConfig
     *
     */


    DOMPurify.clearConfig = function () {
      CONFIG = null;
      SET_CONFIG = false;
    };
    /**
     * Public method to check if an attribute value is valid.
     * Uses last set config, if any. Otherwise, uses config defaults.
     * isValidAttribute
     *
     * @param  {string} tag Tag name of containing element.
     * @param  {string} attr Attribute name.
     * @param  {string} value Attribute value.
     * @return {Boolean} Returns true if `value` is valid. Otherwise, returns false.
     */


    DOMPurify.isValidAttribute = function (tag, attr, value) {
      /* Initialize shared config vars if necessary. */
      if (!CONFIG) {
        _parseConfig({});
      }

      const lcTag = transformCaseFunc(tag);
      const lcName = transformCaseFunc(attr);
      return _isValidAttribute(lcTag, lcName, value);
    };
    /**
     * AddHook
     * Public method to add DOMPurify hooks
     *
     * @param {String} entryPoint entry point for the hook to add
     * @param {Function} hookFunction function to execute
     */


    DOMPurify.addHook = function (entryPoint, hookFunction) {
      if (typeof hookFunction !== 'function') {
        return;
      }

      hooks[entryPoint] = hooks[entryPoint] || [];
      arrayPush(hooks[entryPoint], hookFunction);
    };
    /**
     * RemoveHook
     * Public method to remove a DOMPurify hook at a given entryPoint
     * (pops it from the stack of hooks if more are present)
     *
     * @param {String} entryPoint entry point for the hook to remove
     * @return {Function} removed(popped) hook
     */


    DOMPurify.removeHook = function (entryPoint) {
      if (hooks[entryPoint]) {
        return arrayPop(hooks[entryPoint]);
      }
    };
    /**
     * RemoveHooks
     * Public method to remove all DOMPurify hooks at a given entryPoint
     *
     * @param  {String} entryPoint entry point for the hooks to remove
     */


    DOMPurify.removeHooks = function (entryPoint) {
      if (hooks[entryPoint]) {
        hooks[entryPoint] = [];
      }
    };
    /**
     * RemoveAllHooks
     * Public method to remove all DOMPurify hooks
     *
     */


    DOMPurify.removeAllHooks = function () {
      hooks = {};
    };

    return DOMPurify;
  }

  var purify = createDOMPurify();

  return purify;

}));
//# sourceMappingURL=purify.js.map


/***/ }),

/***/ 589:
/*!*************************************************************************************************************************!*\
  !*** ./.yarn/cache/react-string-replace-npm-1.1.0-9af2371852-5df67fbdb4.zip/node_modules/react-string-replace/index.js ***!
  \*************************************************************************************************************************/
/***/ (function(module) {

/* eslint-disable vars-on-top, no-var, prefer-template */
var isRegExp = function (re) { 
  return re instanceof RegExp;
};
var escapeRegExp = function escapeRegExp(string) {
  var reRegExpChar = /[\\^$.*+?()[\]{}|]/g,
    reHasRegExpChar = RegExp(reRegExpChar.source);

  return (string && reHasRegExpChar.test(string))
    ? string.replace(reRegExpChar, '\\$&')
    : string;
};
var isString = function (value) {
  return typeof value === 'string';
};
var flatten = function (array) {
  var newArray = [];

  array.forEach(function (item) {
    if (Array.isArray(item)) {
      newArray = newArray.concat(item);
    } else {
      newArray.push(item);
    }
  });

  return newArray;
};

/**
 * Given a string, replace every substring that is matched by the `match` regex
 * with the result of calling `fn` on matched substring. The result will be an
 * array with all odd indexed elements containing the replacements. The primary
 * use case is similar to using String.prototype.replace except for React.
 *
 * React will happily render an array as children of a react element, which
 * makes this approach very useful for tasks like surrounding certain text
 * within a string with react elements.
 *
 * Example:
 * matchReplace(
 *   'Emphasize all phone numbers like 884-555-4443.',
 *   /([\d|-]+)/g,
 *   (number, i) => <strong key={i}>{number}</strong>
 * );
 * // => ['Emphasize all phone numbers like ', <strong>884-555-4443</strong>, '.'
 *
 * @param {string} str
 * @param {RegExp|str} match Must contain a matching group
 * @param {function} fn
 * @return {array}
 */
function replaceString(str, match, fn) {
  var curCharStart = 0;
  var curCharLen = 0;

  if (str === '') {
    return str;
  } else if (!str || !isString(str)) {
    throw new TypeError('First argument to react-string-replace#replaceString must be a string');
  }

  var re = match;

  if (!isRegExp(re)) {
    re = new RegExp('(' + escapeRegExp(re) + ')', 'gi');
  }

  var result = str.split(re);

  // Apply fn to all odd elements
  for (var i = 1, length = result.length; i < length; i += 2) {
    /** @see {@link https://github.com/iansinnott/react-string-replace/issues/74} */
    if (result[i] === undefined || result[i - 1] === undefined) {
      console.warn('reactStringReplace: Encountered undefined value during string replacement. Your RegExp may not be working the way you expect.');
      continue;
    }

    curCharLen = result[i].length;
    curCharStart += result[i - 1].length;
    result[i] = fn(result[i], i, curCharStart);
    curCharStart += curCharLen;
  }

  return result;
}

module.exports = function reactStringReplace(source, match, fn) {
  if (!Array.isArray(source)) source = [source];

  return flatten(source.map(function(x) {
    return isString(x) ? replaceString(x, match, fn) : x;
  }));
};


/***/ }),

/***/ 267:
/*!************************************************!*\
  !*** ./src/gutenberg/blocks/ sync block\.tsx$ ***!
  \************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var map = {
	"./categories/block.tsx": 333,
	"./pages/block.tsx": 480,
	"gutenberg/blocks/categories/block.tsx": 333,
	"gutenberg/blocks/pages/block.tsx": 480
};


function webpackContext(req) {
	var id = webpackContextResolve(req);
	return __webpack_require__(id);
}
function webpackContextResolve(req) {
	if(!__webpack_require__.o(map, req)) {
		var e = new Error("Cannot find module '" + req + "'");
		e.code = 'MODULE_NOT_FOUND';
		throw e;
	}
	return map[req];
}
webpackContext.keys = function webpackContextKeys() {
	return Object.keys(map);
};
webpackContext.resolve = webpackContextResolve;
module.exports = webpackContext;
webpackContext.id = 267;

/***/ }),

/***/ 363:
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ (function(module) {

"use strict";
module.exports = React;

/***/ }),

/***/ 311:
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ (function(module) {

"use strict";
module.exports = jQuery;

/***/ }),

/***/ 916:
/*!*************************!*\
  !*** external "lodash" ***!
  \*************************/
/***/ (function(module) {

"use strict";
module.exports = lodash;

/***/ }),

/***/ 179:
/*!******************************!*\
  !*** external "wp.apiFetch" ***!
  \******************************/
/***/ (function(module) {

"use strict";
module.exports = wp.apiFetch;

/***/ }),

/***/ 78:
/*!*********************************!*\
  !*** external "wp.blockEditor" ***!
  \*********************************/
/***/ (function(module) {

"use strict";
module.exports = wp.blockEditor;

/***/ }),

/***/ 378:
/*!****************************!*\
  !*** external "wp.blocks" ***!
  \****************************/
/***/ (function(module) {

"use strict";
module.exports = wp.blocks;

/***/ }),

/***/ 537:
/*!********************************!*\
  !*** external "wp.components" ***!
  \********************************/
/***/ (function(module) {

"use strict";
module.exports = wp.components;

/***/ }),

/***/ 284:
/*!**************************!*\
  !*** external "wp.data" ***!
  \**************************/
/***/ (function(module) {

"use strict";
module.exports = wp.data;

/***/ }),

/***/ 31:
/*!***************************!*\
  !*** external "wp.hooks" ***!
  \***************************/
/***/ (function(module) {

"use strict";
module.exports = wp.hooks;

/***/ }),

/***/ 906:
/*!**********************************!*\
  !*** external "wp.htmlEntities" ***!
  \**********************************/
/***/ (function(module) {

"use strict";
module.exports = wp.htmlEntities;

/***/ }),

/***/ 3:
/*!**************************!*\
  !*** external "wp.i18n" ***!
  \**************************/
/***/ (function(module) {

"use strict";
module.exports = wp.i18n;

/***/ }),

/***/ 398:
/*!*****************************!*\
  !*** external "wp.plugins" ***!
  \*****************************/
/***/ (function(module) {

"use strict";
module.exports = wp.plugins;

/***/ }),

/***/ 882:
/*!**************************************!*\
  !*** external "wp.serverSideRender" ***!
  \**************************************/
/***/ (function(module) {

"use strict";
module.exports = wp.serverSideRender;

/***/ }),

/***/ 368:
/*!*************************!*\
  !*** external "wp.url" ***!
  \*************************/
/***/ (function(module) {

"use strict";
module.exports = wp.url;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			id: moduleId,
/******/ 			loaded: false,
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/harmony module decorator */
/******/ 	!function() {
/******/ 		__webpack_require__.hmd = function(module) {
/******/ 			module = Object.create(module);
/******/ 			if (!module.children) module.children = [];
/******/ 			Object.defineProperty(module, 'exports', {
/******/ 				enumerable: true,
/******/ 				set: function() {
/******/ 					throw new Error('ES Modules may not assign module.exports or exports.*, Use ESM export syntax, instead: ' + module.id);
/******/ 				}
/******/ 			});
/******/ 			return module;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
!function() {
/*!**********************!*\
  !*** ./src/admin.js ***!
  \**********************/
console.log('Advanced Sidebar - Loaded');
/**
 * 1. Blocks can't be lazy loaded, or they will be unavailable
 *    intermittently when developing.
 * 2. Theme Customizers must wait until the page is finished loading.
 *
 * @version 1.1.0
 */

if (typeof wp.element !== 'undefined' && typeof wp.plugins !== 'undefined') {
  (__webpack_require__(/*! ./gutenberg */ 10)["default"])();
} else if (typeof wp.customize !== 'undefined') {
  wp.customize.bind('ready', () => {
    (__webpack_require__(/*! ./gutenberg */ 10)["default"])();
  });
}
}();
/******/ })()
;
//# sourceMappingURL=admin.js.map