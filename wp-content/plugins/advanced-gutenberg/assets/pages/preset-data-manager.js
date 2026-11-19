/******/ (() => { // webpackBootstrap
/*!*************************************************************!*\
  !*** ./assets/pages/block-controls/preset-data-manager.jsx ***!
  \*************************************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _toConsumableArray(r) { return _arrayWithoutHoles(r) || _iterableToArray(r) || _unsupportedIterableToArray(r) || _nonIterableSpread(); }
function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _iterableToArray(r) { if ("undefined" != typeof Symbol && null != r[Symbol.iterator] || null != r["@@iterator"]) return Array.from(r); }
function _arrayWithoutHoles(r) { if (Array.isArray(r)) return _arrayLikeToArray(r); }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
(function () {
  'use strict';

  window.AdvGBPresetData = {
    presets: typeof advgb_block_controls_vars !== 'undefined' && advgb_block_controls_vars.presets ? advgb_block_controls_vars.presets : [],
    subscribers: [],
    // Subscribe to preset data changes
    subscribe: function subscribe(callback) {
      this.subscribers.push(callback);
    },
    // Unsubscribe from preset data changes
    unsubscribe: function unsubscribe(callback) {
      this.subscribers = this.subscribers.filter(function (sub) {
        return sub !== callback;
      });
    },
    // Notify all subscribers of data changes
    notifySubscribers: function notifySubscribers() {
      var _this = this;
      this.subscribers.forEach(function (callback) {
        try {
          callback(_this.presets);
        } catch (error) {
          console.error('Error in preset data subscriber:', error);
        }
      });
    },
    // Update all presets data
    updatePresets: function updatePresets(newPresets) {
      this.presets = Array.isArray(newPresets) ? newPresets : [];

      // Update the original global variable for backward compatibility
      if (typeof advgb_block_controls_vars !== 'undefined') {
        advgb_block_controls_vars.presets = this.presets;
      }
      this.notifySubscribers();
    },
    // Add a new preset
    addPreset: function addPreset(preset) {
      if (preset && preset.id) {
        this.presets.push(preset);
        this.updateGlobalVar();
        this.notifySubscribers();
      }
    },
    // Update an existing preset
    updatePreset: function updatePreset(presetId, updatedPreset) {
      var index = this.presets.findIndex(function (p) {
        return p.id === presetId;
      });
      if (index !== -1) {
        this.presets[index] = _objectSpread(_objectSpread({}, this.presets[index]), updatedPreset);
        this.updateGlobalVar();
        this.notifySubscribers();
      }
    },
    // Remove a preset
    removePreset: function removePreset(presetId) {
      this.presets = this.presets.filter(function (p) {
        return p.id !== presetId;
      });
      this.updateGlobalVar();
      this.notifySubscribers();
    },
    // Get preset by ID
    getPreset: function getPreset(presetId) {
      return this.presets.find(function (p) {
        return p.id === presetId;
      });
    },
    // Get all presets
    getAllPresets: function getAllPresets() {
      return _toConsumableArray(this.presets);
    },
    // Update global variable for backward compatibility
    updateGlobalVar: function updateGlobalVar() {
      if (typeof advgb_block_controls_vars !== 'undefined') {
        advgb_block_controls_vars.presets = this.presets;
      }
    }
  };

  // Initialize with existing data
  if (typeof advgb_block_controls_vars !== 'undefined' && advgb_block_controls_vars.presets) {
    window.AdvGBPresetData.presets = Array.isArray(advgb_block_controls_vars.presets) ? advgb_block_controls_vars.presets : [];
  }
})();
/******/ })()
;
//# sourceMappingURL=preset-data-manager.js.map