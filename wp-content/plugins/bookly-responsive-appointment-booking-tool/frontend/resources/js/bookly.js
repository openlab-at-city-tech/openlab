const booklyJsVersion="26.0";
/*!*/
var bookly = (function ($) {
	'use strict';

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function getDefaultExportFromCjs (x) {
		return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
	}

	var fails$1;
	var hasRequiredFails$1;

	function requireFails$1 () {
		if (hasRequiredFails$1) return fails$1;
		hasRequiredFails$1 = 1;
		fails$1 = function (exec) {
		  try {
		    return !!exec();
		  } catch (error) {
		    return true;
		  }
		};
		return fails$1;
	}

	var functionBindNative$1;
	var hasRequiredFunctionBindNative$1;

	function requireFunctionBindNative$1 () {
		if (hasRequiredFunctionBindNative$1) return functionBindNative$1;
		hasRequiredFunctionBindNative$1 = 1;
		var fails = /*@__PURE__*/ requireFails$1();

		functionBindNative$1 = !fails(function () {
		  // eslint-disable-next-line es/no-function-prototype-bind -- safe
		  var test = (function () { /* empty */ }).bind();
		  // eslint-disable-next-line no-prototype-builtins -- safe
		  return typeof test != 'function' || test.hasOwnProperty('prototype');
		});
		return functionBindNative$1;
	}

	var functionUncurryThis$1;
	var hasRequiredFunctionUncurryThis$1;

	function requireFunctionUncurryThis$1 () {
		if (hasRequiredFunctionUncurryThis$1) return functionUncurryThis$1;
		hasRequiredFunctionUncurryThis$1 = 1;
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative$1();

		var FunctionPrototype = Function.prototype;
		var call = FunctionPrototype.call;
		// eslint-disable-next-line es/no-function-prototype-bind -- safe
		var uncurryThisWithBind = NATIVE_BIND && FunctionPrototype.bind.bind(call, call);

		functionUncurryThis$1 = NATIVE_BIND ? uncurryThisWithBind : function (fn) {
		  return function () {
		    return call.apply(fn, arguments);
		  };
		};
		return functionUncurryThis$1;
	}

	var objectIsPrototypeOf$1;
	var hasRequiredObjectIsPrototypeOf$1;

	function requireObjectIsPrototypeOf$1 () {
		if (hasRequiredObjectIsPrototypeOf$1) return objectIsPrototypeOf$1;
		hasRequiredObjectIsPrototypeOf$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();

		objectIsPrototypeOf$1 = uncurryThis({}.isPrototypeOf);
		return objectIsPrototypeOf$1;
	}

	var es_array_includes$1 = {};

	var globalThis_1$1;
	var hasRequiredGlobalThis$7;

	function requireGlobalThis$7 () {
		if (hasRequiredGlobalThis$7) return globalThis_1$1;
		hasRequiredGlobalThis$7 = 1;
		var check = function (it) {
		  return it && it.Math === Math && it;
		};

		// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
		globalThis_1$1 =
		  // eslint-disable-next-line es/no-global-this -- safe
		  check(typeof globalThis == 'object' && globalThis) ||
		  check(typeof window == 'object' && window) ||
		  // eslint-disable-next-line no-restricted-globals -- safe
		  check(typeof self == 'object' && self) ||
		  check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
		  check(typeof globalThis_1$1 == 'object' && globalThis_1$1) ||
		  // eslint-disable-next-line no-new-func -- fallback
		  (function () { return this; })() || Function('return this')();
		return globalThis_1$1;
	}

	var functionApply$1;
	var hasRequiredFunctionApply$1;

	function requireFunctionApply$1 () {
		if (hasRequiredFunctionApply$1) return functionApply$1;
		hasRequiredFunctionApply$1 = 1;
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative$1();

		var FunctionPrototype = Function.prototype;
		var apply = FunctionPrototype.apply;
		var call = FunctionPrototype.call;

		// eslint-disable-next-line es/no-function-prototype-bind, es/no-reflect -- safe
		functionApply$1 = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND ? call.bind(apply) : function () {
		  return call.apply(apply, arguments);
		});
		return functionApply$1;
	}

	var classofRaw$1;
	var hasRequiredClassofRaw$1;

	function requireClassofRaw$1 () {
		if (hasRequiredClassofRaw$1) return classofRaw$1;
		hasRequiredClassofRaw$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();

		var toString = uncurryThis({}.toString);
		var stringSlice = uncurryThis(''.slice);

		classofRaw$1 = function (it) {
		  return stringSlice(toString(it), 8, -1);
		};
		return classofRaw$1;
	}

	var functionUncurryThisClause$1;
	var hasRequiredFunctionUncurryThisClause$1;

	function requireFunctionUncurryThisClause$1 () {
		if (hasRequiredFunctionUncurryThisClause$1) return functionUncurryThisClause$1;
		hasRequiredFunctionUncurryThisClause$1 = 1;
		var classofRaw = /*@__PURE__*/ requireClassofRaw$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();

		functionUncurryThisClause$1 = function (fn) {
		  // Nashorn bug:
		  //   https://github.com/zloirock/core-js/issues/1128
		  //   https://github.com/zloirock/core-js/issues/1130
		  if (classofRaw(fn) === 'Function') return uncurryThis(fn);
		};
		return functionUncurryThisClause$1;
	}

	var isCallable$1;
	var hasRequiredIsCallable$1;

	function requireIsCallable$1 () {
		if (hasRequiredIsCallable$1) return isCallable$1;
		hasRequiredIsCallable$1 = 1;
		// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
		var documentAll = typeof document == 'object' && document.all;

		// `IsCallable` abstract operation
		// https://tc39.es/ecma262/#sec-iscallable
		// eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
		isCallable$1 = typeof documentAll == 'undefined' && documentAll !== undefined ? function (argument) {
		  return typeof argument == 'function' || argument === documentAll;
		} : function (argument) {
		  return typeof argument == 'function';
		};
		return isCallable$1;
	}

	var objectGetOwnPropertyDescriptor$1 = {};

	var descriptors$1;
	var hasRequiredDescriptors$1;

	function requireDescriptors$1 () {
		if (hasRequiredDescriptors$1) return descriptors$1;
		hasRequiredDescriptors$1 = 1;
		var fails = /*@__PURE__*/ requireFails$1();

		// Detect IE8's incomplete defineProperty implementation
		descriptors$1 = !fails(function () {
		  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
		  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] !== 7;
		});
		return descriptors$1;
	}

	var functionCall$1;
	var hasRequiredFunctionCall$1;

	function requireFunctionCall$1 () {
		if (hasRequiredFunctionCall$1) return functionCall$1;
		hasRequiredFunctionCall$1 = 1;
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative$1();

		var call = Function.prototype.call;
		// eslint-disable-next-line es/no-function-prototype-bind -- safe
		functionCall$1 = NATIVE_BIND ? call.bind(call) : function () {
		  return call.apply(call, arguments);
		};
		return functionCall$1;
	}

	var objectPropertyIsEnumerable$1 = {};

	var hasRequiredObjectPropertyIsEnumerable$1;

	function requireObjectPropertyIsEnumerable$1 () {
		if (hasRequiredObjectPropertyIsEnumerable$1) return objectPropertyIsEnumerable$1;
		hasRequiredObjectPropertyIsEnumerable$1 = 1;
		var $propertyIsEnumerable = {}.propertyIsEnumerable;
		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

		// Nashorn ~ JDK8 bug
		var NASHORN_BUG = getOwnPropertyDescriptor && !$propertyIsEnumerable.call({ 1: 2 }, 1);

		// `Object.prototype.propertyIsEnumerable` method implementation
		// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
		objectPropertyIsEnumerable$1.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
		  var descriptor = getOwnPropertyDescriptor(this, V);
		  return !!descriptor && descriptor.enumerable;
		} : $propertyIsEnumerable;
		return objectPropertyIsEnumerable$1;
	}

	var createPropertyDescriptor$1;
	var hasRequiredCreatePropertyDescriptor$1;

	function requireCreatePropertyDescriptor$1 () {
		if (hasRequiredCreatePropertyDescriptor$1) return createPropertyDescriptor$1;
		hasRequiredCreatePropertyDescriptor$1 = 1;
		createPropertyDescriptor$1 = function (bitmap, value) {
		  return {
		    enumerable: !(bitmap & 1),
		    configurable: !(bitmap & 2),
		    writable: !(bitmap & 4),
		    value: value
		  };
		};
		return createPropertyDescriptor$1;
	}

	var indexedObject$1;
	var hasRequiredIndexedObject$1;

	function requireIndexedObject$1 () {
		if (hasRequiredIndexedObject$1) return indexedObject$1;
		hasRequiredIndexedObject$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var classof = /*@__PURE__*/ requireClassofRaw$1();

		var $Object = Object;
		var split = uncurryThis(''.split);

		// fallback for non-array-like ES3 and non-enumerable old V8 strings
		indexedObject$1 = fails(function () {
		  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
		  // eslint-disable-next-line no-prototype-builtins -- safe
		  return !$Object('z').propertyIsEnumerable(0);
		}) ? function (it) {
		  return classof(it) === 'String' ? split(it, '') : $Object(it);
		} : $Object;
		return indexedObject$1;
	}

	var isNullOrUndefined$1;
	var hasRequiredIsNullOrUndefined$1;

	function requireIsNullOrUndefined$1 () {
		if (hasRequiredIsNullOrUndefined$1) return isNullOrUndefined$1;
		hasRequiredIsNullOrUndefined$1 = 1;
		// we can't use just `it == null` since of `document.all` special case
		// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
		isNullOrUndefined$1 = function (it) {
		  return it === null || it === undefined;
		};
		return isNullOrUndefined$1;
	}

	var requireObjectCoercible$1;
	var hasRequiredRequireObjectCoercible$1;

	function requireRequireObjectCoercible$1 () {
		if (hasRequiredRequireObjectCoercible$1) return requireObjectCoercible$1;
		hasRequiredRequireObjectCoercible$1 = 1;
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined$1();

		var $TypeError = TypeError;

		// `RequireObjectCoercible` abstract operation
		// https://tc39.es/ecma262/#sec-requireobjectcoercible
		requireObjectCoercible$1 = function (it) {
		  if (isNullOrUndefined(it)) throw new $TypeError("Can't call method on " + it);
		  return it;
		};
		return requireObjectCoercible$1;
	}

	var toIndexedObject$1;
	var hasRequiredToIndexedObject$1;

	function requireToIndexedObject$1 () {
		if (hasRequiredToIndexedObject$1) return toIndexedObject$1;
		hasRequiredToIndexedObject$1 = 1;
		// toObject with fallback for non-array-like ES3 strings
		var IndexedObject = /*@__PURE__*/ requireIndexedObject$1();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();

		toIndexedObject$1 = function (it) {
		  return IndexedObject(requireObjectCoercible(it));
		};
		return toIndexedObject$1;
	}

	var isObject$1;
	var hasRequiredIsObject$1;

	function requireIsObject$1 () {
		if (hasRequiredIsObject$1) return isObject$1;
		hasRequiredIsObject$1 = 1;
		var isCallable = /*@__PURE__*/ requireIsCallable$1();

		isObject$1 = function (it) {
		  return typeof it == 'object' ? it !== null : isCallable(it);
		};
		return isObject$1;
	}

	var path$1;
	var hasRequiredPath$1;

	function requirePath$1 () {
		if (hasRequiredPath$1) return path$1;
		hasRequiredPath$1 = 1;
		path$1 = {};
		return path$1;
	}

	var getBuiltIn$1;
	var hasRequiredGetBuiltIn$1;

	function requireGetBuiltIn$1 () {
		if (hasRequiredGetBuiltIn$1) return getBuiltIn$1;
		hasRequiredGetBuiltIn$1 = 1;
		var path = /*@__PURE__*/ requirePath$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();

		var aFunction = function (variable) {
		  return isCallable(variable) ? variable : undefined;
		};

		getBuiltIn$1 = function (namespace, method) {
		  return arguments.length < 2 ? aFunction(path[namespace]) || aFunction(globalThis[namespace])
		    : path[namespace] && path[namespace][method] || globalThis[namespace] && globalThis[namespace][method];
		};
		return getBuiltIn$1;
	}

	var environmentUserAgent$1;
	var hasRequiredEnvironmentUserAgent$1;

	function requireEnvironmentUserAgent$1 () {
		if (hasRequiredEnvironmentUserAgent$1) return environmentUserAgent$1;
		hasRequiredEnvironmentUserAgent$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();

		var navigator = globalThis.navigator;
		var userAgent = navigator && navigator.userAgent;

		environmentUserAgent$1 = userAgent ? String(userAgent) : '';
		return environmentUserAgent$1;
	}

	var environmentV8Version$1;
	var hasRequiredEnvironmentV8Version$1;

	function requireEnvironmentV8Version$1 () {
		if (hasRequiredEnvironmentV8Version$1) return environmentV8Version$1;
		hasRequiredEnvironmentV8Version$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		var process = globalThis.process;
		var Deno = globalThis.Deno;
		var versions = process && process.versions || Deno && Deno.version;
		var v8 = versions && versions.v8;
		var match, version;

		if (v8) {
		  match = v8.split('.');
		  // in old Chrome, versions of V8 isn't V8 = Chrome / 10
		  // but their correct versions are not interesting for us
		  version = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
		}

		// BrowserFS NodeJS `process` polyfill incorrectly set `.v8` to `0.0`
		// so check `userAgent` even if `.v8` exists, but 0
		if (!version && userAgent) {
		  match = userAgent.match(/Edge\/(\d+)/);
		  if (!match || match[1] >= 74) {
		    match = userAgent.match(/Chrome\/(\d+)/);
		    if (match) version = +match[1];
		  }
		}

		environmentV8Version$1 = version;
		return environmentV8Version$1;
	}

	var symbolConstructorDetection$1;
	var hasRequiredSymbolConstructorDetection$1;

	function requireSymbolConstructorDetection$1 () {
		if (hasRequiredSymbolConstructorDetection$1) return symbolConstructorDetection$1;
		hasRequiredSymbolConstructorDetection$1 = 1;
		/* eslint-disable es/no-symbol -- required for testing */
		var V8_VERSION = /*@__PURE__*/ requireEnvironmentV8Version$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();

		var $String = globalThis.String;

		// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
		symbolConstructorDetection$1 = !!Object.getOwnPropertySymbols && !fails(function () {
		  var symbol = Symbol('symbol detection');
		  // Chrome 38 Symbol has incorrect toString conversion
		  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
		  // nb: Do not call `String` directly to avoid this being optimized out to `symbol+''` which will,
		  // of course, fail.
		  return !$String(symbol) || !(Object(symbol) instanceof Symbol) ||
		    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
		    !Symbol.sham && V8_VERSION && V8_VERSION < 41;
		});
		return symbolConstructorDetection$1;
	}

	var useSymbolAsUid$1;
	var hasRequiredUseSymbolAsUid$1;

	function requireUseSymbolAsUid$1 () {
		if (hasRequiredUseSymbolAsUid$1) return useSymbolAsUid$1;
		hasRequiredUseSymbolAsUid$1 = 1;
		/* eslint-disable es/no-symbol -- required for testing */
		var NATIVE_SYMBOL = /*@__PURE__*/ requireSymbolConstructorDetection$1();

		useSymbolAsUid$1 = NATIVE_SYMBOL &&
		  !Symbol.sham &&
		  typeof Symbol.iterator == 'symbol';
		return useSymbolAsUid$1;
	}

	var isSymbol$1;
	var hasRequiredIsSymbol$1;

	function requireIsSymbol$1 () {
		if (hasRequiredIsSymbol$1) return isSymbol$1;
		hasRequiredIsSymbol$1 = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var USE_SYMBOL_AS_UID = /*@__PURE__*/ requireUseSymbolAsUid$1();

		var $Object = Object;

		isSymbol$1 = USE_SYMBOL_AS_UID ? function (it) {
		  return typeof it == 'symbol';
		} : function (it) {
		  var $Symbol = getBuiltIn('Symbol');
		  return isCallable($Symbol) && isPrototypeOf($Symbol.prototype, $Object(it));
		};
		return isSymbol$1;
	}

	var tryToString$1;
	var hasRequiredTryToString$1;

	function requireTryToString$1 () {
		if (hasRequiredTryToString$1) return tryToString$1;
		hasRequiredTryToString$1 = 1;
		var $String = String;

		tryToString$1 = function (argument) {
		  try {
		    return $String(argument);
		  } catch (error) {
		    return 'Object';
		  }
		};
		return tryToString$1;
	}

	var aCallable$1;
	var hasRequiredACallable$1;

	function requireACallable$1 () {
		if (hasRequiredACallable$1) return aCallable$1;
		hasRequiredACallable$1 = 1;
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var tryToString = /*@__PURE__*/ requireTryToString$1();

		var $TypeError = TypeError;

		// `Assert: IsCallable(argument) is true`
		aCallable$1 = function (argument) {
		  if (isCallable(argument)) return argument;
		  throw new $TypeError(tryToString(argument) + ' is not a function');
		};
		return aCallable$1;
	}

	var getMethod$1;
	var hasRequiredGetMethod$1;

	function requireGetMethod$1 () {
		if (hasRequiredGetMethod$1) return getMethod$1;
		hasRequiredGetMethod$1 = 1;
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined$1();

		// `GetMethod` abstract operation
		// https://tc39.es/ecma262/#sec-getmethod
		getMethod$1 = function (V, P) {
		  var func = V[P];
		  return isNullOrUndefined(func) ? undefined : aCallable(func);
		};
		return getMethod$1;
	}

	var ordinaryToPrimitive$1;
	var hasRequiredOrdinaryToPrimitive$1;

	function requireOrdinaryToPrimitive$1 () {
		if (hasRequiredOrdinaryToPrimitive$1) return ordinaryToPrimitive$1;
		hasRequiredOrdinaryToPrimitive$1 = 1;
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();

		var $TypeError = TypeError;

		// `OrdinaryToPrimitive` abstract operation
		// https://tc39.es/ecma262/#sec-ordinarytoprimitive
		ordinaryToPrimitive$1 = function (input, pref) {
		  var fn, val;
		  if (pref === 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
		  if (isCallable(fn = input.valueOf) && !isObject(val = call(fn, input))) return val;
		  if (pref !== 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
		  throw new $TypeError("Can't convert object to primitive value");
		};
		return ordinaryToPrimitive$1;
	}

	var sharedStore$1 = {exports: {}};

	var isPure$1;
	var hasRequiredIsPure$1;

	function requireIsPure$1 () {
		if (hasRequiredIsPure$1) return isPure$1;
		hasRequiredIsPure$1 = 1;
		isPure$1 = true;
		return isPure$1;
	}

	var defineGlobalProperty$1;
	var hasRequiredDefineGlobalProperty$1;

	function requireDefineGlobalProperty$1 () {
		if (hasRequiredDefineGlobalProperty$1) return defineGlobalProperty$1;
		hasRequiredDefineGlobalProperty$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();

		// eslint-disable-next-line es/no-object-defineproperty -- safe
		var defineProperty = Object.defineProperty;

		defineGlobalProperty$1 = function (key, value) {
		  try {
		    defineProperty(globalThis, key, { value: value, configurable: true, writable: true });
		  } catch (error) {
		    globalThis[key] = value;
		  } return value;
		};
		return defineGlobalProperty$1;
	}

	var hasRequiredSharedStore$1;

	function requireSharedStore$1 () {
		if (hasRequiredSharedStore$1) return sharedStore$1.exports;
		hasRequiredSharedStore$1 = 1;
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var defineGlobalProperty = /*@__PURE__*/ requireDefineGlobalProperty$1();

		var SHARED = '__core-js_shared__';
		var store = sharedStore$1.exports = globalThis[SHARED] || defineGlobalProperty(SHARED, {});

		(store.versions || (store.versions = [])).push({
		  version: '3.44.0',
		  mode: IS_PURE ? 'pure' : 'global',
		  copyright: '© 2014-2025 Denis Pushkarev (zloirock.ru)',
		  license: 'https://github.com/zloirock/core-js/blob/v3.44.0/LICENSE',
		  source: 'https://github.com/zloirock/core-js'
		});
		return sharedStore$1.exports;
	}

	var shared$1;
	var hasRequiredShared$1;

	function requireShared$1 () {
		if (hasRequiredShared$1) return shared$1;
		hasRequiredShared$1 = 1;
		var store = /*@__PURE__*/ requireSharedStore$1();

		shared$1 = function (key, value) {
		  return store[key] || (store[key] = value || {});
		};
		return shared$1;
	}

	var toObject$1;
	var hasRequiredToObject$1;

	function requireToObject$1 () {
		if (hasRequiredToObject$1) return toObject$1;
		hasRequiredToObject$1 = 1;
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();

		var $Object = Object;

		// `ToObject` abstract operation
		// https://tc39.es/ecma262/#sec-toobject
		toObject$1 = function (argument) {
		  return $Object(requireObjectCoercible(argument));
		};
		return toObject$1;
	}

	var hasOwnProperty_1$1;
	var hasRequiredHasOwnProperty$1;

	function requireHasOwnProperty$1 () {
		if (hasRequiredHasOwnProperty$1) return hasOwnProperty_1$1;
		hasRequiredHasOwnProperty$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var toObject = /*@__PURE__*/ requireToObject$1();

		var hasOwnProperty = uncurryThis({}.hasOwnProperty);

		// `HasOwnProperty` abstract operation
		// https://tc39.es/ecma262/#sec-hasownproperty
		// eslint-disable-next-line es/no-object-hasown -- safe
		hasOwnProperty_1$1 = Object.hasOwn || function hasOwn(it, key) {
		  return hasOwnProperty(toObject(it), key);
		};
		return hasOwnProperty_1$1;
	}

	var uid$1;
	var hasRequiredUid$1;

	function requireUid$1 () {
		if (hasRequiredUid$1) return uid$1;
		hasRequiredUid$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();

		var id = 0;
		var postfix = Math.random();
		var toString = uncurryThis(1.1.toString);

		uid$1 = function (key) {
		  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString(++id + postfix, 36);
		};
		return uid$1;
	}

	var wellKnownSymbol$1;
	var hasRequiredWellKnownSymbol$1;

	function requireWellKnownSymbol$1 () {
		if (hasRequiredWellKnownSymbol$1) return wellKnownSymbol$1;
		hasRequiredWellKnownSymbol$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var shared = /*@__PURE__*/ requireShared$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var uid = /*@__PURE__*/ requireUid$1();
		var NATIVE_SYMBOL = /*@__PURE__*/ requireSymbolConstructorDetection$1();
		var USE_SYMBOL_AS_UID = /*@__PURE__*/ requireUseSymbolAsUid$1();

		var Symbol = globalThis.Symbol;
		var WellKnownSymbolsStore = shared('wks');
		var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol['for'] || Symbol : Symbol && Symbol.withoutSetter || uid;

		wellKnownSymbol$1 = function (name) {
		  if (!hasOwn(WellKnownSymbolsStore, name)) {
		    WellKnownSymbolsStore[name] = NATIVE_SYMBOL && hasOwn(Symbol, name)
		      ? Symbol[name]
		      : createWellKnownSymbol('Symbol.' + name);
		  } return WellKnownSymbolsStore[name];
		};
		return wellKnownSymbol$1;
	}

	var toPrimitive$1;
	var hasRequiredToPrimitive$1;

	function requireToPrimitive$1 () {
		if (hasRequiredToPrimitive$1) return toPrimitive$1;
		hasRequiredToPrimitive$1 = 1;
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var isSymbol = /*@__PURE__*/ requireIsSymbol$1();
		var getMethod = /*@__PURE__*/ requireGetMethod$1();
		var ordinaryToPrimitive = /*@__PURE__*/ requireOrdinaryToPrimitive$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var $TypeError = TypeError;
		var TO_PRIMITIVE = wellKnownSymbol('toPrimitive');

		// `ToPrimitive` abstract operation
		// https://tc39.es/ecma262/#sec-toprimitive
		toPrimitive$1 = function (input, pref) {
		  if (!isObject(input) || isSymbol(input)) return input;
		  var exoticToPrim = getMethod(input, TO_PRIMITIVE);
		  var result;
		  if (exoticToPrim) {
		    if (pref === undefined) pref = 'default';
		    result = call(exoticToPrim, input, pref);
		    if (!isObject(result) || isSymbol(result)) return result;
		    throw new $TypeError("Can't convert object to primitive value");
		  }
		  if (pref === undefined) pref = 'number';
		  return ordinaryToPrimitive(input, pref);
		};
		return toPrimitive$1;
	}

	var toPropertyKey$1;
	var hasRequiredToPropertyKey$1;

	function requireToPropertyKey$1 () {
		if (hasRequiredToPropertyKey$1) return toPropertyKey$1;
		hasRequiredToPropertyKey$1 = 1;
		var toPrimitive = /*@__PURE__*/ requireToPrimitive$1();
		var isSymbol = /*@__PURE__*/ requireIsSymbol$1();

		// `ToPropertyKey` abstract operation
		// https://tc39.es/ecma262/#sec-topropertykey
		toPropertyKey$1 = function (argument) {
		  var key = toPrimitive(argument, 'string');
		  return isSymbol(key) ? key : key + '';
		};
		return toPropertyKey$1;
	}

	var documentCreateElement$1;
	var hasRequiredDocumentCreateElement$1;

	function requireDocumentCreateElement$1 () {
		if (hasRequiredDocumentCreateElement$1) return documentCreateElement$1;
		hasRequiredDocumentCreateElement$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var isObject = /*@__PURE__*/ requireIsObject$1();

		var document = globalThis.document;
		// typeof document.createElement is 'object' in old IE
		var EXISTS = isObject(document) && isObject(document.createElement);

		documentCreateElement$1 = function (it) {
		  return EXISTS ? document.createElement(it) : {};
		};
		return documentCreateElement$1;
	}

	var ie8DomDefine$1;
	var hasRequiredIe8DomDefine$1;

	function requireIe8DomDefine$1 () {
		if (hasRequiredIe8DomDefine$1) return ie8DomDefine$1;
		hasRequiredIe8DomDefine$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var createElement = /*@__PURE__*/ requireDocumentCreateElement$1();

		// Thanks to IE8 for its funny defineProperty
		ie8DomDefine$1 = !DESCRIPTORS && !fails(function () {
		  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
		  return Object.defineProperty(createElement('div'), 'a', {
		    get: function () { return 7; }
		  }).a !== 7;
		});
		return ie8DomDefine$1;
	}

	var hasRequiredObjectGetOwnPropertyDescriptor$1;

	function requireObjectGetOwnPropertyDescriptor$1 () {
		if (hasRequiredObjectGetOwnPropertyDescriptor$1) return objectGetOwnPropertyDescriptor$1;
		hasRequiredObjectGetOwnPropertyDescriptor$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var propertyIsEnumerableModule = /*@__PURE__*/ requireObjectPropertyIsEnumerable$1();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor$1();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var toPropertyKey = /*@__PURE__*/ requireToPropertyKey$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var IE8_DOM_DEFINE = /*@__PURE__*/ requireIe8DomDefine$1();

		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

		// `Object.getOwnPropertyDescriptor` method
		// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
		objectGetOwnPropertyDescriptor$1.f = DESCRIPTORS ? $getOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
		  O = toIndexedObject(O);
		  P = toPropertyKey(P);
		  if (IE8_DOM_DEFINE) try {
		    return $getOwnPropertyDescriptor(O, P);
		  } catch (error) { /* empty */ }
		  if (hasOwn(O, P)) return createPropertyDescriptor(!call(propertyIsEnumerableModule.f, O, P), O[P]);
		};
		return objectGetOwnPropertyDescriptor$1;
	}

	var isForced_1$1;
	var hasRequiredIsForced$1;

	function requireIsForced$1 () {
		if (hasRequiredIsForced$1) return isForced_1$1;
		hasRequiredIsForced$1 = 1;
		var fails = /*@__PURE__*/ requireFails$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();

		var replacement = /#|\.prototype\./;

		var isForced = function (feature, detection) {
		  var value = data[normalize(feature)];
		  return value === POLYFILL ? true
		    : value === NATIVE ? false
		    : isCallable(detection) ? fails(detection)
		    : !!detection;
		};

		var normalize = isForced.normalize = function (string) {
		  return String(string).replace(replacement, '.').toLowerCase();
		};

		var data = isForced.data = {};
		var NATIVE = isForced.NATIVE = 'N';
		var POLYFILL = isForced.POLYFILL = 'P';

		isForced_1$1 = isForced;
		return isForced_1$1;
	}

	var functionBindContext$1;
	var hasRequiredFunctionBindContext$1;

	function requireFunctionBindContext$1 () {
		if (hasRequiredFunctionBindContext$1) return functionBindContext$1;
		hasRequiredFunctionBindContext$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThisClause$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative$1();

		var bind = uncurryThis(uncurryThis.bind);

		// optional / simple context binding
		functionBindContext$1 = function (fn, that) {
		  aCallable(fn);
		  return that === undefined ? fn : NATIVE_BIND ? bind(fn, that) : function (/* ...args */) {
		    return fn.apply(that, arguments);
		  };
		};
		return functionBindContext$1;
	}

	var objectDefineProperty$1 = {};

	var v8PrototypeDefineBug$1;
	var hasRequiredV8PrototypeDefineBug$1;

	function requireV8PrototypeDefineBug$1 () {
		if (hasRequiredV8PrototypeDefineBug$1) return v8PrototypeDefineBug$1;
		hasRequiredV8PrototypeDefineBug$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var fails = /*@__PURE__*/ requireFails$1();

		// V8 ~ Chrome 36-
		// https://bugs.chromium.org/p/v8/issues/detail?id=3334
		v8PrototypeDefineBug$1 = DESCRIPTORS && fails(function () {
		  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
		  return Object.defineProperty(function () { /* empty */ }, 'prototype', {
		    value: 42,
		    writable: false
		  }).prototype !== 42;
		});
		return v8PrototypeDefineBug$1;
	}

	var anObject$1;
	var hasRequiredAnObject$1;

	function requireAnObject$1 () {
		if (hasRequiredAnObject$1) return anObject$1;
		hasRequiredAnObject$1 = 1;
		var isObject = /*@__PURE__*/ requireIsObject$1();

		var $String = String;
		var $TypeError = TypeError;

		// `Assert: Type(argument) is Object`
		anObject$1 = function (argument) {
		  if (isObject(argument)) return argument;
		  throw new $TypeError($String(argument) + ' is not an object');
		};
		return anObject$1;
	}

	var hasRequiredObjectDefineProperty$1;

	function requireObjectDefineProperty$1 () {
		if (hasRequiredObjectDefineProperty$1) return objectDefineProperty$1;
		hasRequiredObjectDefineProperty$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var IE8_DOM_DEFINE = /*@__PURE__*/ requireIe8DomDefine$1();
		var V8_PROTOTYPE_DEFINE_BUG = /*@__PURE__*/ requireV8PrototypeDefineBug$1();
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var toPropertyKey = /*@__PURE__*/ requireToPropertyKey$1();

		var $TypeError = TypeError;
		// eslint-disable-next-line es/no-object-defineproperty -- safe
		var $defineProperty = Object.defineProperty;
		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
		var ENUMERABLE = 'enumerable';
		var CONFIGURABLE = 'configurable';
		var WRITABLE = 'writable';

		// `Object.defineProperty` method
		// https://tc39.es/ecma262/#sec-object.defineproperty
		objectDefineProperty$1.f = DESCRIPTORS ? V8_PROTOTYPE_DEFINE_BUG ? function defineProperty(O, P, Attributes) {
		  anObject(O);
		  P = toPropertyKey(P);
		  anObject(Attributes);
		  if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
		    var current = $getOwnPropertyDescriptor(O, P);
		    if (current && current[WRITABLE]) {
		      O[P] = Attributes.value;
		      Attributes = {
		        configurable: CONFIGURABLE in Attributes ? Attributes[CONFIGURABLE] : current[CONFIGURABLE],
		        enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
		        writable: false
		      };
		    }
		  } return $defineProperty(O, P, Attributes);
		} : $defineProperty : function defineProperty(O, P, Attributes) {
		  anObject(O);
		  P = toPropertyKey(P);
		  anObject(Attributes);
		  if (IE8_DOM_DEFINE) try {
		    return $defineProperty(O, P, Attributes);
		  } catch (error) { /* empty */ }
		  if ('get' in Attributes || 'set' in Attributes) throw new $TypeError('Accessors not supported');
		  if ('value' in Attributes) O[P] = Attributes.value;
		  return O;
		};
		return objectDefineProperty$1;
	}

	var createNonEnumerableProperty$1;
	var hasRequiredCreateNonEnumerableProperty$1;

	function requireCreateNonEnumerableProperty$1 () {
		if (hasRequiredCreateNonEnumerableProperty$1) return createNonEnumerableProperty$1;
		hasRequiredCreateNonEnumerableProperty$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var definePropertyModule = /*@__PURE__*/ requireObjectDefineProperty$1();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor$1();

		createNonEnumerableProperty$1 = DESCRIPTORS ? function (object, key, value) {
		  return definePropertyModule.f(object, key, createPropertyDescriptor(1, value));
		} : function (object, key, value) {
		  object[key] = value;
		  return object;
		};
		return createNonEnumerableProperty$1;
	}

	var _export$1;
	var hasRequired_export$1;

	function require_export$1 () {
		if (hasRequired_export$1) return _export$1;
		hasRequired_export$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var apply = /*@__PURE__*/ requireFunctionApply$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThisClause$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var getOwnPropertyDescriptor = /*@__PURE__*/ requireObjectGetOwnPropertyDescriptor$1().f;
		var isForced = /*@__PURE__*/ requireIsForced$1();
		var path = /*@__PURE__*/ requirePath$1();
		var bind = /*@__PURE__*/ requireFunctionBindContext$1();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();

		var wrapConstructor = function (NativeConstructor) {
		  var Wrapper = function (a, b, c) {
		    if (this instanceof Wrapper) {
		      switch (arguments.length) {
		        case 0: return new NativeConstructor();
		        case 1: return new NativeConstructor(a);
		        case 2: return new NativeConstructor(a, b);
		      } return new NativeConstructor(a, b, c);
		    } return apply(NativeConstructor, this, arguments);
		  };
		  Wrapper.prototype = NativeConstructor.prototype;
		  return Wrapper;
		};

		/*
		  options.target         - name of the target object
		  options.global         - target is the global object
		  options.stat           - export as static methods of target
		  options.proto          - export as prototype methods of target
		  options.real           - real prototype method for the `pure` version
		  options.forced         - export even if the native feature is available
		  options.bind           - bind methods to the target, required for the `pure` version
		  options.wrap           - wrap constructors to preventing global pollution, required for the `pure` version
		  options.unsafe         - use the simple assignment of property instead of delete + defineProperty
		  options.sham           - add a flag to not completely full polyfills
		  options.enumerable     - export as enumerable property
		  options.dontCallGetSet - prevent calling a getter on target
		  options.name           - the .name of the function if it does not match the key
		*/
		_export$1 = function (options, source) {
		  var TARGET = options.target;
		  var GLOBAL = options.global;
		  var STATIC = options.stat;
		  var PROTO = options.proto;

		  var nativeSource = GLOBAL ? globalThis : STATIC ? globalThis[TARGET] : globalThis[TARGET] && globalThis[TARGET].prototype;

		  var target = GLOBAL ? path : path[TARGET] || createNonEnumerableProperty(path, TARGET, {})[TARGET];
		  var targetPrototype = target.prototype;

		  var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
		  var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

		  for (key in source) {
		    FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
		    // contains in native
		    USE_NATIVE = !FORCED && nativeSource && hasOwn(nativeSource, key);

		    targetProperty = target[key];

		    if (USE_NATIVE) if (options.dontCallGetSet) {
		      descriptor = getOwnPropertyDescriptor(nativeSource, key);
		      nativeProperty = descriptor && descriptor.value;
		    } else nativeProperty = nativeSource[key];

		    // export native or implementation
		    sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

		    if (!FORCED && !PROTO && typeof targetProperty == typeof sourceProperty) continue;

		    // bind methods to global for calling from export context
		    if (options.bind && USE_NATIVE) resultProperty = bind(sourceProperty, globalThis);
		    // wrap global constructors for prevent changes in this version
		    else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor(sourceProperty);
		    // make static versions for prototype methods
		    else if (PROTO && isCallable(sourceProperty)) resultProperty = uncurryThis(sourceProperty);
		    // default case
		    else resultProperty = sourceProperty;

		    // add a flag to not completely full polyfills
		    if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
		      createNonEnumerableProperty(resultProperty, 'sham', true);
		    }

		    createNonEnumerableProperty(target, key, resultProperty);

		    if (PROTO) {
		      VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
		      if (!hasOwn(path, VIRTUAL_PROTOTYPE)) {
		        createNonEnumerableProperty(path, VIRTUAL_PROTOTYPE, {});
		      }
		      // export virtual prototype methods
		      createNonEnumerableProperty(path[VIRTUAL_PROTOTYPE], key, sourceProperty);
		      // export real prototype methods
		      if (options.real && targetPrototype && (FORCED || !targetPrototype[key])) {
		        createNonEnumerableProperty(targetPrototype, key, sourceProperty);
		      }
		    }
		  }
		};
		return _export$1;
	}

	var mathTrunc$1;
	var hasRequiredMathTrunc$1;

	function requireMathTrunc$1 () {
		if (hasRequiredMathTrunc$1) return mathTrunc$1;
		hasRequiredMathTrunc$1 = 1;
		var ceil = Math.ceil;
		var floor = Math.floor;

		// `Math.trunc` method
		// https://tc39.es/ecma262/#sec-math.trunc
		// eslint-disable-next-line es/no-math-trunc -- safe
		mathTrunc$1 = Math.trunc || function trunc(x) {
		  var n = +x;
		  return (n > 0 ? floor : ceil)(n);
		};
		return mathTrunc$1;
	}

	var toIntegerOrInfinity$1;
	var hasRequiredToIntegerOrInfinity$1;

	function requireToIntegerOrInfinity$1 () {
		if (hasRequiredToIntegerOrInfinity$1) return toIntegerOrInfinity$1;
		hasRequiredToIntegerOrInfinity$1 = 1;
		var trunc = /*@__PURE__*/ requireMathTrunc$1();

		// `ToIntegerOrInfinity` abstract operation
		// https://tc39.es/ecma262/#sec-tointegerorinfinity
		toIntegerOrInfinity$1 = function (argument) {
		  var number = +argument;
		  // eslint-disable-next-line no-self-compare -- NaN check
		  return number !== number || number === 0 ? 0 : trunc(number);
		};
		return toIntegerOrInfinity$1;
	}

	var toAbsoluteIndex$1;
	var hasRequiredToAbsoluteIndex$1;

	function requireToAbsoluteIndex$1 () {
		if (hasRequiredToAbsoluteIndex$1) return toAbsoluteIndex$1;
		hasRequiredToAbsoluteIndex$1 = 1;
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity$1();

		var max = Math.max;
		var min = Math.min;

		// Helper for a popular repeating case of the spec:
		// Let integer be ? ToInteger(index).
		// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
		toAbsoluteIndex$1 = function (index, length) {
		  var integer = toIntegerOrInfinity(index);
		  return integer < 0 ? max(integer + length, 0) : min(integer, length);
		};
		return toAbsoluteIndex$1;
	}

	var toLength$1;
	var hasRequiredToLength$1;

	function requireToLength$1 () {
		if (hasRequiredToLength$1) return toLength$1;
		hasRequiredToLength$1 = 1;
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity$1();

		var min = Math.min;

		// `ToLength` abstract operation
		// https://tc39.es/ecma262/#sec-tolength
		toLength$1 = function (argument) {
		  var len = toIntegerOrInfinity(argument);
		  return len > 0 ? min(len, 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
		};
		return toLength$1;
	}

	var lengthOfArrayLike$1;
	var hasRequiredLengthOfArrayLike$1;

	function requireLengthOfArrayLike$1 () {
		if (hasRequiredLengthOfArrayLike$1) return lengthOfArrayLike$1;
		hasRequiredLengthOfArrayLike$1 = 1;
		var toLength = /*@__PURE__*/ requireToLength$1();

		// `LengthOfArrayLike` abstract operation
		// https://tc39.es/ecma262/#sec-lengthofarraylike
		lengthOfArrayLike$1 = function (obj) {
		  return toLength(obj.length);
		};
		return lengthOfArrayLike$1;
	}

	var arrayIncludes$1;
	var hasRequiredArrayIncludes$1;

	function requireArrayIncludes$1 () {
		if (hasRequiredArrayIncludes$1) return arrayIncludes$1;
		hasRequiredArrayIncludes$1 = 1;
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var toAbsoluteIndex = /*@__PURE__*/ requireToAbsoluteIndex$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();

		// `Array.prototype.{ indexOf, includes }` methods implementation
		var createMethod = function (IS_INCLUDES) {
		  return function ($this, el, fromIndex) {
		    var O = toIndexedObject($this);
		    var length = lengthOfArrayLike(O);
		    if (length === 0) return !IS_INCLUDES && -1;
		    var index = toAbsoluteIndex(fromIndex, length);
		    var value;
		    // Array#includes uses SameValueZero equality algorithm
		    // eslint-disable-next-line no-self-compare -- NaN check
		    if (IS_INCLUDES && el !== el) while (length > index) {
		      value = O[index++];
		      // eslint-disable-next-line no-self-compare -- NaN check
		      if (value !== value) return true;
		    // Array#indexOf ignores holes, Array#includes - not
		    } else for (;length > index; index++) {
		      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
		    } return !IS_INCLUDES && -1;
		  };
		};

		arrayIncludes$1 = {
		  // `Array.prototype.includes` method
		  // https://tc39.es/ecma262/#sec-array.prototype.includes
		  includes: createMethod(true),
		  // `Array.prototype.indexOf` method
		  // https://tc39.es/ecma262/#sec-array.prototype.indexof
		  indexOf: createMethod(false)
		};
		return arrayIncludes$1;
	}

	var addToUnscopables$1;
	var hasRequiredAddToUnscopables$1;

	function requireAddToUnscopables$1 () {
		if (hasRequiredAddToUnscopables$1) return addToUnscopables$1;
		hasRequiredAddToUnscopables$1 = 1;
		addToUnscopables$1 = function () { /* empty */ };
		return addToUnscopables$1;
	}

	var hasRequiredEs_array_includes$1;

	function requireEs_array_includes$1 () {
		if (hasRequiredEs_array_includes$1) return es_array_includes$1;
		hasRequiredEs_array_includes$1 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $includes = /*@__PURE__*/ requireArrayIncludes$1().includes;
		var fails = /*@__PURE__*/ requireFails$1();
		var addToUnscopables = /*@__PURE__*/ requireAddToUnscopables$1();

		// FF99+ bug
		var BROKEN_ON_SPARSE = fails(function () {
		  // eslint-disable-next-line es/no-array-prototype-includes -- detection
		  return !Array(1).includes();
		});

		// `Array.prototype.includes` method
		// https://tc39.es/ecma262/#sec-array.prototype.includes
		$({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
		  includes: function includes(el /* , fromIndex = 0 */) {
		    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});

		// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
		addToUnscopables('includes');
		return es_array_includes$1;
	}

	var getBuiltInPrototypeMethod$1;
	var hasRequiredGetBuiltInPrototypeMethod$1;

	function requireGetBuiltInPrototypeMethod$1 () {
		if (hasRequiredGetBuiltInPrototypeMethod$1) return getBuiltInPrototypeMethod$1;
		hasRequiredGetBuiltInPrototypeMethod$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var path = /*@__PURE__*/ requirePath$1();

		getBuiltInPrototypeMethod$1 = function (CONSTRUCTOR, METHOD) {
		  var Namespace = path[CONSTRUCTOR + 'Prototype'];
		  var pureMethod = Namespace && Namespace[METHOD];
		  if (pureMethod) return pureMethod;
		  var NativeConstructor = globalThis[CONSTRUCTOR];
		  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
		  return NativePrototype && NativePrototype[METHOD];
		};
		return getBuiltInPrototypeMethod$1;
	}

	var includes$9;
	var hasRequiredIncludes$9;

	function requireIncludes$9 () {
		if (hasRequiredIncludes$9) return includes$9;
		hasRequiredIncludes$9 = 1;
		requireEs_array_includes$1();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		includes$9 = getBuiltInPrototypeMethod('Array', 'includes');
		return includes$9;
	}

	var es_string_includes$1 = {};

	var isRegexp$1;
	var hasRequiredIsRegexp$1;

	function requireIsRegexp$1 () {
		if (hasRequiredIsRegexp$1) return isRegexp$1;
		hasRequiredIsRegexp$1 = 1;
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var classof = /*@__PURE__*/ requireClassofRaw$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var MATCH = wellKnownSymbol('match');

		// `IsRegExp` abstract operation
		// https://tc39.es/ecma262/#sec-isregexp
		isRegexp$1 = function (it) {
		  var isRegExp;
		  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : classof(it) === 'RegExp');
		};
		return isRegexp$1;
	}

	var notARegexp$1;
	var hasRequiredNotARegexp$1;

	function requireNotARegexp$1 () {
		if (hasRequiredNotARegexp$1) return notARegexp$1;
		hasRequiredNotARegexp$1 = 1;
		var isRegExp = /*@__PURE__*/ requireIsRegexp$1();

		var $TypeError = TypeError;

		notARegexp$1 = function (it) {
		  if (isRegExp(it)) {
		    throw new $TypeError("The method doesn't accept regular expressions");
		  } return it;
		};
		return notARegexp$1;
	}

	var toStringTagSupport$1;
	var hasRequiredToStringTagSupport$1;

	function requireToStringTagSupport$1 () {
		if (hasRequiredToStringTagSupport$1) return toStringTagSupport$1;
		hasRequiredToStringTagSupport$1 = 1;
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var TO_STRING_TAG = wellKnownSymbol('toStringTag');
		var test = {};

		test[TO_STRING_TAG] = 'z';

		toStringTagSupport$1 = String(test) === '[object z]';
		return toStringTagSupport$1;
	}

	var classof$1;
	var hasRequiredClassof$1;

	function requireClassof$1 () {
		if (hasRequiredClassof$1) return classof$1;
		hasRequiredClassof$1 = 1;
		var TO_STRING_TAG_SUPPORT = /*@__PURE__*/ requireToStringTagSupport$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var classofRaw = /*@__PURE__*/ requireClassofRaw$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var TO_STRING_TAG = wellKnownSymbol('toStringTag');
		var $Object = Object;

		// ES3 wrong here
		var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) === 'Arguments';

		// fallback for IE11 Script Access Denied error
		var tryGet = function (it, key) {
		  try {
		    return it[key];
		  } catch (error) { /* empty */ }
		};

		// getting tag from ES6+ `Object.prototype.toString`
		classof$1 = TO_STRING_TAG_SUPPORT ? classofRaw : function (it) {
		  var O, tag, result;
		  return it === undefined ? 'Undefined' : it === null ? 'Null'
		    // @@toStringTag case
		    : typeof (tag = tryGet(O = $Object(it), TO_STRING_TAG)) == 'string' ? tag
		    // builtinTag case
		    : CORRECT_ARGUMENTS ? classofRaw(O)
		    // ES3 arguments fallback
		    : (result = classofRaw(O)) === 'Object' && isCallable(O.callee) ? 'Arguments' : result;
		};
		return classof$1;
	}

	var toString$1;
	var hasRequiredToString$1;

	function requireToString$1 () {
		if (hasRequiredToString$1) return toString$1;
		hasRequiredToString$1 = 1;
		var classof = /*@__PURE__*/ requireClassof$1();

		var $String = String;

		toString$1 = function (argument) {
		  if (classof(argument) === 'Symbol') throw new TypeError('Cannot convert a Symbol value to a string');
		  return $String(argument);
		};
		return toString$1;
	}

	var correctIsRegexpLogic$1;
	var hasRequiredCorrectIsRegexpLogic$1;

	function requireCorrectIsRegexpLogic$1 () {
		if (hasRequiredCorrectIsRegexpLogic$1) return correctIsRegexpLogic$1;
		hasRequiredCorrectIsRegexpLogic$1 = 1;
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var MATCH = wellKnownSymbol('match');

		correctIsRegexpLogic$1 = function (METHOD_NAME) {
		  var regexp = /./;
		  try {
		    '/./'[METHOD_NAME](regexp);
		  } catch (error1) {
		    try {
		      regexp[MATCH] = false;
		      return '/./'[METHOD_NAME](regexp);
		    } catch (error2) { /* empty */ }
		  } return false;
		};
		return correctIsRegexpLogic$1;
	}

	var hasRequiredEs_string_includes$1;

	function requireEs_string_includes$1 () {
		if (hasRequiredEs_string_includes$1) return es_string_includes$1;
		hasRequiredEs_string_includes$1 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var notARegExp = /*@__PURE__*/ requireNotARegexp$1();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();
		var toString = /*@__PURE__*/ requireToString$1();
		var correctIsRegExpLogic = /*@__PURE__*/ requireCorrectIsRegexpLogic$1();

		var stringIndexOf = uncurryThis(''.indexOf);

		// `String.prototype.includes` method
		// https://tc39.es/ecma262/#sec-string.prototype.includes
		$({ target: 'String', proto: true, forced: !correctIsRegExpLogic('includes') }, {
		  includes: function includes(searchString /* , position = 0 */) {
		    return !!~stringIndexOf(
		      toString(requireObjectCoercible(this)),
		      toString(notARegExp(searchString)),
		      arguments.length > 1 ? arguments[1] : undefined
		    );
		  }
		});
		return es_string_includes$1;
	}

	var includes$8;
	var hasRequiredIncludes$8;

	function requireIncludes$8 () {
		if (hasRequiredIncludes$8) return includes$8;
		hasRequiredIncludes$8 = 1;
		requireEs_string_includes$1();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		includes$8 = getBuiltInPrototypeMethod('String', 'includes');
		return includes$8;
	}

	var includes$7;
	var hasRequiredIncludes$7;

	function requireIncludes$7 () {
		if (hasRequiredIncludes$7) return includes$7;
		hasRequiredIncludes$7 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var arrayMethod = /*@__PURE__*/ requireIncludes$9();
		var stringMethod = /*@__PURE__*/ requireIncludes$8();

		var ArrayPrototype = Array.prototype;
		var StringPrototype = String.prototype;

		includes$7 = function (it) {
		  var own = it.includes;
		  if (it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.includes)) return arrayMethod;
		  if (typeof it == 'string' || it === StringPrototype || (isPrototypeOf(StringPrototype, it) && own === StringPrototype.includes)) {
		    return stringMethod;
		  } return own;
		};
		return includes$7;
	}

	var includes$6;
	var hasRequiredIncludes$6;

	function requireIncludes$6 () {
		if (hasRequiredIncludes$6) return includes$6;
		hasRequiredIncludes$6 = 1;
		var parent = /*@__PURE__*/ requireIncludes$7();

		includes$6 = parent;
		return includes$6;
	}

	var includes$5;
	var hasRequiredIncludes$5;

	function requireIncludes$5 () {
		if (hasRequiredIncludes$5) return includes$5;
		hasRequiredIncludes$5 = 1;
		includes$5 = /*@__PURE__*/ requireIncludes$6();
		return includes$5;
	}

	var includesExports$1 = requireIncludes$5();
	var _includesInstanceProperty$1 = /*@__PURE__*/getDefaultExportFromCjs(includesExports$1);

	var es_aggregateError = {};

	var es_aggregateError_constructor = {};

	var sharedKey$1;
	var hasRequiredSharedKey$1;

	function requireSharedKey$1 () {
		if (hasRequiredSharedKey$1) return sharedKey$1;
		hasRequiredSharedKey$1 = 1;
		var shared = /*@__PURE__*/ requireShared$1();
		var uid = /*@__PURE__*/ requireUid$1();

		var keys = shared('keys');

		sharedKey$1 = function (key) {
		  return keys[key] || (keys[key] = uid(key));
		};
		return sharedKey$1;
	}

	var correctPrototypeGetter$1;
	var hasRequiredCorrectPrototypeGetter$1;

	function requireCorrectPrototypeGetter$1 () {
		if (hasRequiredCorrectPrototypeGetter$1) return correctPrototypeGetter$1;
		hasRequiredCorrectPrototypeGetter$1 = 1;
		var fails = /*@__PURE__*/ requireFails$1();

		correctPrototypeGetter$1 = !fails(function () {
		  function F() { /* empty */ }
		  F.prototype.constructor = null;
		  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
		  return Object.getPrototypeOf(new F()) !== F.prototype;
		});
		return correctPrototypeGetter$1;
	}

	var objectGetPrototypeOf$1;
	var hasRequiredObjectGetPrototypeOf$1;

	function requireObjectGetPrototypeOf$1 () {
		if (hasRequiredObjectGetPrototypeOf$1) return objectGetPrototypeOf$1;
		hasRequiredObjectGetPrototypeOf$1 = 1;
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var sharedKey = /*@__PURE__*/ requireSharedKey$1();
		var CORRECT_PROTOTYPE_GETTER = /*@__PURE__*/ requireCorrectPrototypeGetter$1();

		var IE_PROTO = sharedKey('IE_PROTO');
		var $Object = Object;
		var ObjectPrototype = $Object.prototype;

		// `Object.getPrototypeOf` method
		// https://tc39.es/ecma262/#sec-object.getprototypeof
		// eslint-disable-next-line es/no-object-getprototypeof -- safe
		objectGetPrototypeOf$1 = CORRECT_PROTOTYPE_GETTER ? $Object.getPrototypeOf : function (O) {
		  var object = toObject(O);
		  if (hasOwn(object, IE_PROTO)) return object[IE_PROTO];
		  var constructor = object.constructor;
		  if (isCallable(constructor) && object instanceof constructor) {
		    return constructor.prototype;
		  } return object instanceof $Object ? ObjectPrototype : null;
		};
		return objectGetPrototypeOf$1;
	}

	var functionUncurryThisAccessor$1;
	var hasRequiredFunctionUncurryThisAccessor$1;

	function requireFunctionUncurryThisAccessor$1 () {
		if (hasRequiredFunctionUncurryThisAccessor$1) return functionUncurryThisAccessor$1;
		hasRequiredFunctionUncurryThisAccessor$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();

		functionUncurryThisAccessor$1 = function (object, key, method) {
		  try {
		    // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		    return uncurryThis(aCallable(Object.getOwnPropertyDescriptor(object, key)[method]));
		  } catch (error) { /* empty */ }
		};
		return functionUncurryThisAccessor$1;
	}

	var isPossiblePrototype$1;
	var hasRequiredIsPossiblePrototype$1;

	function requireIsPossiblePrototype$1 () {
		if (hasRequiredIsPossiblePrototype$1) return isPossiblePrototype$1;
		hasRequiredIsPossiblePrototype$1 = 1;
		var isObject = /*@__PURE__*/ requireIsObject$1();

		isPossiblePrototype$1 = function (argument) {
		  return isObject(argument) || argument === null;
		};
		return isPossiblePrototype$1;
	}

	var aPossiblePrototype$1;
	var hasRequiredAPossiblePrototype$1;

	function requireAPossiblePrototype$1 () {
		if (hasRequiredAPossiblePrototype$1) return aPossiblePrototype$1;
		hasRequiredAPossiblePrototype$1 = 1;
		var isPossiblePrototype = /*@__PURE__*/ requireIsPossiblePrototype$1();

		var $String = String;
		var $TypeError = TypeError;

		aPossiblePrototype$1 = function (argument) {
		  if (isPossiblePrototype(argument)) return argument;
		  throw new $TypeError("Can't set " + $String(argument) + ' as a prototype');
		};
		return aPossiblePrototype$1;
	}

	var objectSetPrototypeOf$1;
	var hasRequiredObjectSetPrototypeOf$1;

	function requireObjectSetPrototypeOf$1 () {
		if (hasRequiredObjectSetPrototypeOf$1) return objectSetPrototypeOf$1;
		hasRequiredObjectSetPrototypeOf$1 = 1;
		/* eslint-disable no-proto -- safe */
		var uncurryThisAccessor = /*@__PURE__*/ requireFunctionUncurryThisAccessor$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();
		var aPossiblePrototype = /*@__PURE__*/ requireAPossiblePrototype$1();

		// `Object.setPrototypeOf` method
		// https://tc39.es/ecma262/#sec-object.setprototypeof
		// Works with __proto__ only. Old v8 can't work with null proto objects.
		// eslint-disable-next-line es/no-object-setprototypeof -- safe
		objectSetPrototypeOf$1 = Object.setPrototypeOf || ('__proto__' in {} ? function () {
		  var CORRECT_SETTER = false;
		  var test = {};
		  var setter;
		  try {
		    setter = uncurryThisAccessor(Object.prototype, '__proto__', 'set');
		    setter(test, []);
		    CORRECT_SETTER = test instanceof Array;
		  } catch (error) { /* empty */ }
		  return function setPrototypeOf(O, proto) {
		    requireObjectCoercible(O);
		    aPossiblePrototype(proto);
		    if (!isObject(O)) return O;
		    if (CORRECT_SETTER) setter(O, proto);
		    else O.__proto__ = proto;
		    return O;
		  };
		}() : undefined);
		return objectSetPrototypeOf$1;
	}

	var objectGetOwnPropertyNames$1 = {};

	var hiddenKeys$1;
	var hasRequiredHiddenKeys$1;

	function requireHiddenKeys$1 () {
		if (hasRequiredHiddenKeys$1) return hiddenKeys$1;
		hasRequiredHiddenKeys$1 = 1;
		hiddenKeys$1 = {};
		return hiddenKeys$1;
	}

	var objectKeysInternal$1;
	var hasRequiredObjectKeysInternal$1;

	function requireObjectKeysInternal$1 () {
		if (hasRequiredObjectKeysInternal$1) return objectKeysInternal$1;
		hasRequiredObjectKeysInternal$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var indexOf = /*@__PURE__*/ requireArrayIncludes$1().indexOf;
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys$1();

		var push = uncurryThis([].push);

		objectKeysInternal$1 = function (object, names) {
		  var O = toIndexedObject(object);
		  var i = 0;
		  var result = [];
		  var key;
		  for (key in O) !hasOwn(hiddenKeys, key) && hasOwn(O, key) && push(result, key);
		  // Don't enum bug & hidden keys
		  while (names.length > i) if (hasOwn(O, key = names[i++])) {
		    ~indexOf(result, key) || push(result, key);
		  }
		  return result;
		};
		return objectKeysInternal$1;
	}

	var enumBugKeys$1;
	var hasRequiredEnumBugKeys$1;

	function requireEnumBugKeys$1 () {
		if (hasRequiredEnumBugKeys$1) return enumBugKeys$1;
		hasRequiredEnumBugKeys$1 = 1;
		// IE8- don't enum bug keys
		enumBugKeys$1 = [
		  'constructor',
		  'hasOwnProperty',
		  'isPrototypeOf',
		  'propertyIsEnumerable',
		  'toLocaleString',
		  'toString',
		  'valueOf'
		];
		return enumBugKeys$1;
	}

	var hasRequiredObjectGetOwnPropertyNames$1;

	function requireObjectGetOwnPropertyNames$1 () {
		if (hasRequiredObjectGetOwnPropertyNames$1) return objectGetOwnPropertyNames$1;
		hasRequiredObjectGetOwnPropertyNames$1 = 1;
		var internalObjectKeys = /*@__PURE__*/ requireObjectKeysInternal$1();
		var enumBugKeys = /*@__PURE__*/ requireEnumBugKeys$1();

		var hiddenKeys = enumBugKeys.concat('length', 'prototype');

		// `Object.getOwnPropertyNames` method
		// https://tc39.es/ecma262/#sec-object.getownpropertynames
		// eslint-disable-next-line es/no-object-getownpropertynames -- safe
		objectGetOwnPropertyNames$1.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
		  return internalObjectKeys(O, hiddenKeys);
		};
		return objectGetOwnPropertyNames$1;
	}

	var objectGetOwnPropertySymbols = {};

	var hasRequiredObjectGetOwnPropertySymbols;

	function requireObjectGetOwnPropertySymbols () {
		if (hasRequiredObjectGetOwnPropertySymbols) return objectGetOwnPropertySymbols;
		hasRequiredObjectGetOwnPropertySymbols = 1;
		// eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
		objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;
		return objectGetOwnPropertySymbols;
	}

	var ownKeys;
	var hasRequiredOwnKeys;

	function requireOwnKeys () {
		if (hasRequiredOwnKeys) return ownKeys;
		hasRequiredOwnKeys = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var getOwnPropertyNamesModule = /*@__PURE__*/ requireObjectGetOwnPropertyNames$1();
		var getOwnPropertySymbolsModule = /*@__PURE__*/ requireObjectGetOwnPropertySymbols();
		var anObject = /*@__PURE__*/ requireAnObject$1();

		var concat = uncurryThis([].concat);

		// all object keys, includes non-enumerable and symbols
		ownKeys = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
		  var keys = getOwnPropertyNamesModule.f(anObject(it));
		  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
		  return getOwnPropertySymbols ? concat(keys, getOwnPropertySymbols(it)) : keys;
		};
		return ownKeys;
	}

	var copyConstructorProperties;
	var hasRequiredCopyConstructorProperties;

	function requireCopyConstructorProperties () {
		if (hasRequiredCopyConstructorProperties) return copyConstructorProperties;
		hasRequiredCopyConstructorProperties = 1;
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var ownKeys = /*@__PURE__*/ requireOwnKeys();
		var getOwnPropertyDescriptorModule = /*@__PURE__*/ requireObjectGetOwnPropertyDescriptor$1();
		var definePropertyModule = /*@__PURE__*/ requireObjectDefineProperty$1();

		copyConstructorProperties = function (target, source, exceptions) {
		  var keys = ownKeys(source);
		  var defineProperty = definePropertyModule.f;
		  var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
		  for (var i = 0; i < keys.length; i++) {
		    var key = keys[i];
		    if (!hasOwn(target, key) && !(exceptions && hasOwn(exceptions, key))) {
		      defineProperty(target, key, getOwnPropertyDescriptor(source, key));
		    }
		  }
		};
		return copyConstructorProperties;
	}

	var objectDefineProperties$1 = {};

	var objectKeys$1;
	var hasRequiredObjectKeys$1;

	function requireObjectKeys$1 () {
		if (hasRequiredObjectKeys$1) return objectKeys$1;
		hasRequiredObjectKeys$1 = 1;
		var internalObjectKeys = /*@__PURE__*/ requireObjectKeysInternal$1();
		var enumBugKeys = /*@__PURE__*/ requireEnumBugKeys$1();

		// `Object.keys` method
		// https://tc39.es/ecma262/#sec-object.keys
		// eslint-disable-next-line es/no-object-keys -- safe
		objectKeys$1 = Object.keys || function keys(O) {
		  return internalObjectKeys(O, enumBugKeys);
		};
		return objectKeys$1;
	}

	var hasRequiredObjectDefineProperties$1;

	function requireObjectDefineProperties$1 () {
		if (hasRequiredObjectDefineProperties$1) return objectDefineProperties$1;
		hasRequiredObjectDefineProperties$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var V8_PROTOTYPE_DEFINE_BUG = /*@__PURE__*/ requireV8PrototypeDefineBug$1();
		var definePropertyModule = /*@__PURE__*/ requireObjectDefineProperty$1();
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var objectKeys = /*@__PURE__*/ requireObjectKeys$1();

		// `Object.defineProperties` method
		// https://tc39.es/ecma262/#sec-object.defineproperties
		// eslint-disable-next-line es/no-object-defineproperties -- safe
		objectDefineProperties$1.f = DESCRIPTORS && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
		  anObject(O);
		  var props = toIndexedObject(Properties);
		  var keys = objectKeys(Properties);
		  var length = keys.length;
		  var index = 0;
		  var key;
		  while (length > index) definePropertyModule.f(O, key = keys[index++], props[key]);
		  return O;
		};
		return objectDefineProperties$1;
	}

	var html$1;
	var hasRequiredHtml$1;

	function requireHtml$1 () {
		if (hasRequiredHtml$1) return html$1;
		hasRequiredHtml$1 = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();

		html$1 = getBuiltIn('document', 'documentElement');
		return html$1;
	}

	var objectCreate$1;
	var hasRequiredObjectCreate$1;

	function requireObjectCreate$1 () {
		if (hasRequiredObjectCreate$1) return objectCreate$1;
		hasRequiredObjectCreate$1 = 1;
		/* global ActiveXObject -- old IE, WSH */
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var definePropertiesModule = /*@__PURE__*/ requireObjectDefineProperties$1();
		var enumBugKeys = /*@__PURE__*/ requireEnumBugKeys$1();
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys$1();
		var html = /*@__PURE__*/ requireHtml$1();
		var documentCreateElement = /*@__PURE__*/ requireDocumentCreateElement$1();
		var sharedKey = /*@__PURE__*/ requireSharedKey$1();

		var GT = '>';
		var LT = '<';
		var PROTOTYPE = 'prototype';
		var SCRIPT = 'script';
		var IE_PROTO = sharedKey('IE_PROTO');

		var EmptyConstructor = function () { /* empty */ };

		var scriptTag = function (content) {
		  return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
		};

		// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
		var NullProtoObjectViaActiveX = function (activeXDocument) {
		  activeXDocument.write(scriptTag(''));
		  activeXDocument.close();
		  var temp = activeXDocument.parentWindow.Object;
		  // eslint-disable-next-line no-useless-assignment -- avoid memory leak
		  activeXDocument = null;
		  return temp;
		};

		// Create object with fake `null` prototype: use iframe Object with cleared prototype
		var NullProtoObjectViaIFrame = function () {
		  // Thrash, waste and sodomy: IE GC bug
		  var iframe = documentCreateElement('iframe');
		  var JS = 'java' + SCRIPT + ':';
		  var iframeDocument;
		  iframe.style.display = 'none';
		  html.appendChild(iframe);
		  // https://github.com/zloirock/core-js/issues/475
		  iframe.src = String(JS);
		  iframeDocument = iframe.contentWindow.document;
		  iframeDocument.open();
		  iframeDocument.write(scriptTag('document.F=Object'));
		  iframeDocument.close();
		  return iframeDocument.F;
		};

		// Check for document.domain and active x support
		// No need to use active x approach when document.domain is not set
		// see https://github.com/es-shims/es5-shim/issues/150
		// variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
		// avoid IE GC bug
		var activeXDocument;
		var NullProtoObject = function () {
		  try {
		    activeXDocument = new ActiveXObject('htmlfile');
		  } catch (error) { /* ignore */ }
		  NullProtoObject = typeof document != 'undefined'
		    ? document.domain && activeXDocument
		      ? NullProtoObjectViaActiveX(activeXDocument) // old IE
		      : NullProtoObjectViaIFrame()
		    : NullProtoObjectViaActiveX(activeXDocument); // WSH
		  var length = enumBugKeys.length;
		  while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys[length]];
		  return NullProtoObject();
		};

		hiddenKeys[IE_PROTO] = true;

		// `Object.create` method
		// https://tc39.es/ecma262/#sec-object.create
		// eslint-disable-next-line es/no-object-create -- safe
		objectCreate$1 = Object.create || function create(O, Properties) {
		  var result;
		  if (O !== null) {
		    EmptyConstructor[PROTOTYPE] = anObject(O);
		    result = new EmptyConstructor();
		    EmptyConstructor[PROTOTYPE] = null;
		    // add "__proto__" for Object.getPrototypeOf polyfill
		    result[IE_PROTO] = O;
		  } else result = NullProtoObject();
		  return Properties === undefined ? result : definePropertiesModule.f(result, Properties);
		};
		return objectCreate$1;
	}

	var installErrorCause;
	var hasRequiredInstallErrorCause;

	function requireInstallErrorCause () {
		if (hasRequiredInstallErrorCause) return installErrorCause;
		hasRequiredInstallErrorCause = 1;
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();

		// `InstallErrorCause` abstract operation
		// https://tc39.es/ecma262/#sec-installerrorcause
		installErrorCause = function (O, options) {
		  if (isObject(options) && 'cause' in options) {
		    createNonEnumerableProperty(O, 'cause', options.cause);
		  }
		};
		return installErrorCause;
	}

	var errorStackClear;
	var hasRequiredErrorStackClear;

	function requireErrorStackClear () {
		if (hasRequiredErrorStackClear) return errorStackClear;
		hasRequiredErrorStackClear = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();

		var $Error = Error;
		var replace = uncurryThis(''.replace);

		var TEST = (function (arg) { return String(new $Error(arg).stack); })('zxcasd');
		// eslint-disable-next-line redos/no-vulnerable, sonarjs/slow-regex -- safe
		var V8_OR_CHAKRA_STACK_ENTRY = /\n\s*at [^:]*:[^\n]*/;
		var IS_V8_OR_CHAKRA_STACK = V8_OR_CHAKRA_STACK_ENTRY.test(TEST);

		errorStackClear = function (stack, dropEntries) {
		  if (IS_V8_OR_CHAKRA_STACK && typeof stack == 'string' && !$Error.prepareStackTrace) {
		    while (dropEntries--) stack = replace(stack, V8_OR_CHAKRA_STACK_ENTRY, '');
		  } return stack;
		};
		return errorStackClear;
	}

	var errorStackInstallable;
	var hasRequiredErrorStackInstallable;

	function requireErrorStackInstallable () {
		if (hasRequiredErrorStackInstallable) return errorStackInstallable;
		hasRequiredErrorStackInstallable = 1;
		var fails = /*@__PURE__*/ requireFails$1();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor$1();

		errorStackInstallable = !fails(function () {
		  var error = new Error('a');
		  if (!('stack' in error)) return true;
		  // eslint-disable-next-line es/no-object-defineproperty -- safe
		  Object.defineProperty(error, 'stack', createPropertyDescriptor(1, 7));
		  return error.stack !== 7;
		});
		return errorStackInstallable;
	}

	var errorStackInstall;
	var hasRequiredErrorStackInstall;

	function requireErrorStackInstall () {
		if (hasRequiredErrorStackInstall) return errorStackInstall;
		hasRequiredErrorStackInstall = 1;
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();
		var clearErrorStack = /*@__PURE__*/ requireErrorStackClear();
		var ERROR_STACK_INSTALLABLE = /*@__PURE__*/ requireErrorStackInstallable();

		// non-standard V8
		// eslint-disable-next-line es/no-nonstandard-error-properties -- safe
		var captureStackTrace = Error.captureStackTrace;

		errorStackInstall = function (error, C, stack, dropEntries) {
		  if (ERROR_STACK_INSTALLABLE) {
		    if (captureStackTrace) captureStackTrace(error, C);
		    else createNonEnumerableProperty(error, 'stack', clearErrorStack(stack, dropEntries));
		  }
		};
		return errorStackInstall;
	}

	var iterators$1;
	var hasRequiredIterators$1;

	function requireIterators$1 () {
		if (hasRequiredIterators$1) return iterators$1;
		hasRequiredIterators$1 = 1;
		iterators$1 = {};
		return iterators$1;
	}

	var isArrayIteratorMethod$1;
	var hasRequiredIsArrayIteratorMethod$1;

	function requireIsArrayIteratorMethod$1 () {
		if (hasRequiredIsArrayIteratorMethod$1) return isArrayIteratorMethod$1;
		hasRequiredIsArrayIteratorMethod$1 = 1;
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var Iterators = /*@__PURE__*/ requireIterators$1();

		var ITERATOR = wellKnownSymbol('iterator');
		var ArrayPrototype = Array.prototype;

		// check on default Array iterator
		isArrayIteratorMethod$1 = function (it) {
		  return it !== undefined && (Iterators.Array === it || ArrayPrototype[ITERATOR] === it);
		};
		return isArrayIteratorMethod$1;
	}

	var getIteratorMethod$1;
	var hasRequiredGetIteratorMethod$1;

	function requireGetIteratorMethod$1 () {
		if (hasRequiredGetIteratorMethod$1) return getIteratorMethod$1;
		hasRequiredGetIteratorMethod$1 = 1;
		var classof = /*@__PURE__*/ requireClassof$1();
		var getMethod = /*@__PURE__*/ requireGetMethod$1();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined$1();
		var Iterators = /*@__PURE__*/ requireIterators$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var ITERATOR = wellKnownSymbol('iterator');

		getIteratorMethod$1 = function (it) {
		  if (!isNullOrUndefined(it)) return getMethod(it, ITERATOR)
		    || getMethod(it, '@@iterator')
		    || Iterators[classof(it)];
		};
		return getIteratorMethod$1;
	}

	var getIterator$1;
	var hasRequiredGetIterator$1;

	function requireGetIterator$1 () {
		if (hasRequiredGetIterator$1) return getIterator$1;
		hasRequiredGetIterator$1 = 1;
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var tryToString = /*@__PURE__*/ requireTryToString$1();
		var getIteratorMethod = /*@__PURE__*/ requireGetIteratorMethod$1();

		var $TypeError = TypeError;

		getIterator$1 = function (argument, usingIterator) {
		  var iteratorMethod = arguments.length < 2 ? getIteratorMethod(argument) : usingIterator;
		  if (aCallable(iteratorMethod)) return anObject(call(iteratorMethod, argument));
		  throw new $TypeError(tryToString(argument) + ' is not iterable');
		};
		return getIterator$1;
	}

	var iteratorClose$1;
	var hasRequiredIteratorClose$1;

	function requireIteratorClose$1 () {
		if (hasRequiredIteratorClose$1) return iteratorClose$1;
		hasRequiredIteratorClose$1 = 1;
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var getMethod = /*@__PURE__*/ requireGetMethod$1();

		iteratorClose$1 = function (iterator, kind, value) {
		  var innerResult, innerError;
		  anObject(iterator);
		  try {
		    innerResult = getMethod(iterator, 'return');
		    if (!innerResult) {
		      if (kind === 'throw') throw value;
		      return value;
		    }
		    innerResult = call(innerResult, iterator);
		  } catch (error) {
		    innerError = true;
		    innerResult = error;
		  }
		  if (kind === 'throw') throw value;
		  if (innerError) throw innerResult;
		  anObject(innerResult);
		  return value;
		};
		return iteratorClose$1;
	}

	var iterate$1;
	var hasRequiredIterate$1;

	function requireIterate$1 () {
		if (hasRequiredIterate$1) return iterate$1;
		hasRequiredIterate$1 = 1;
		var bind = /*@__PURE__*/ requireFunctionBindContext$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var tryToString = /*@__PURE__*/ requireTryToString$1();
		var isArrayIteratorMethod = /*@__PURE__*/ requireIsArrayIteratorMethod$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var getIterator = /*@__PURE__*/ requireGetIterator$1();
		var getIteratorMethod = /*@__PURE__*/ requireGetIteratorMethod$1();
		var iteratorClose = /*@__PURE__*/ requireIteratorClose$1();

		var $TypeError = TypeError;

		var Result = function (stopped, result) {
		  this.stopped = stopped;
		  this.result = result;
		};

		var ResultPrototype = Result.prototype;

		iterate$1 = function (iterable, unboundFunction, options) {
		  var that = options && options.that;
		  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
		  var IS_RECORD = !!(options && options.IS_RECORD);
		  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
		  var INTERRUPTED = !!(options && options.INTERRUPTED);
		  var fn = bind(unboundFunction, that);
		  var iterator, iterFn, index, length, result, next, step;

		  var stop = function (condition) {
		    if (iterator) iteratorClose(iterator, 'normal');
		    return new Result(true, condition);
		  };

		  var callFn = function (value) {
		    if (AS_ENTRIES) {
		      anObject(value);
		      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
		    } return INTERRUPTED ? fn(value, stop) : fn(value);
		  };

		  if (IS_RECORD) {
		    iterator = iterable.iterator;
		  } else if (IS_ITERATOR) {
		    iterator = iterable;
		  } else {
		    iterFn = getIteratorMethod(iterable);
		    if (!iterFn) throw new $TypeError(tryToString(iterable) + ' is not iterable');
		    // optimisation for array iterators
		    if (isArrayIteratorMethod(iterFn)) {
		      for (index = 0, length = lengthOfArrayLike(iterable); length > index; index++) {
		        result = callFn(iterable[index]);
		        if (result && isPrototypeOf(ResultPrototype, result)) return result;
		      } return new Result(false);
		    }
		    iterator = getIterator(iterable, iterFn);
		  }

		  next = IS_RECORD ? iterable.next : iterator.next;
		  while (!(step = call(next, iterator)).done) {
		    try {
		      result = callFn(step.value);
		    } catch (error) {
		      iteratorClose(iterator, 'throw', error);
		    }
		    if (typeof result == 'object' && result && isPrototypeOf(ResultPrototype, result)) return result;
		  } return new Result(false);
		};
		return iterate$1;
	}

	var normalizeStringArgument;
	var hasRequiredNormalizeStringArgument;

	function requireNormalizeStringArgument () {
		if (hasRequiredNormalizeStringArgument) return normalizeStringArgument;
		hasRequiredNormalizeStringArgument = 1;
		var toString = /*@__PURE__*/ requireToString$1();

		normalizeStringArgument = function (argument, $default) {
		  return argument === undefined ? arguments.length < 2 ? '' : $default : toString(argument);
		};
		return normalizeStringArgument;
	}

	var hasRequiredEs_aggregateError_constructor;

	function requireEs_aggregateError_constructor () {
		if (hasRequiredEs_aggregateError_constructor) return es_aggregateError_constructor;
		hasRequiredEs_aggregateError_constructor = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var getPrototypeOf = /*@__PURE__*/ requireObjectGetPrototypeOf$1();
		var setPrototypeOf = /*@__PURE__*/ requireObjectSetPrototypeOf$1();
		var copyConstructorProperties = /*@__PURE__*/ requireCopyConstructorProperties();
		var create = /*@__PURE__*/ requireObjectCreate$1();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor$1();
		var installErrorCause = /*@__PURE__*/ requireInstallErrorCause();
		var installErrorStack = /*@__PURE__*/ requireErrorStackInstall();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var normalizeStringArgument = /*@__PURE__*/ requireNormalizeStringArgument();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var TO_STRING_TAG = wellKnownSymbol('toStringTag');
		var $Error = Error;
		var push = [].push;

		var $AggregateError = function AggregateError(errors, message /* , options */) {
		  var isInstance = isPrototypeOf(AggregateErrorPrototype, this);
		  var that;
		  if (setPrototypeOf) {
		    that = setPrototypeOf(new $Error(), isInstance ? getPrototypeOf(this) : AggregateErrorPrototype);
		  } else {
		    that = isInstance ? this : create(AggregateErrorPrototype);
		    createNonEnumerableProperty(that, TO_STRING_TAG, 'Error');
		  }
		  if (message !== undefined) createNonEnumerableProperty(that, 'message', normalizeStringArgument(message));
		  installErrorStack(that, $AggregateError, that.stack, 1);
		  if (arguments.length > 2) installErrorCause(that, arguments[2]);
		  var errorsArray = [];
		  iterate(errors, push, { that: errorsArray });
		  createNonEnumerableProperty(that, 'errors', errorsArray);
		  return that;
		};

		if (setPrototypeOf) setPrototypeOf($AggregateError, $Error);
		else copyConstructorProperties($AggregateError, $Error, { name: true });

		var AggregateErrorPrototype = $AggregateError.prototype = create($Error.prototype, {
		  constructor: createPropertyDescriptor(1, $AggregateError),
		  message: createPropertyDescriptor(1, ''),
		  name: createPropertyDescriptor(1, 'AggregateError')
		});

		// `AggregateError` constructor
		// https://tc39.es/ecma262/#sec-aggregate-error-constructor
		$({ global: true, constructor: true, arity: 2 }, {
		  AggregateError: $AggregateError
		});
		return es_aggregateError_constructor;
	}

	var hasRequiredEs_aggregateError;

	function requireEs_aggregateError () {
		if (hasRequiredEs_aggregateError) return es_aggregateError;
		hasRequiredEs_aggregateError = 1;
		// TODO: Remove this module from `core-js@4` since it's replaced to module below
		requireEs_aggregateError_constructor();
		return es_aggregateError;
	}

	var weakMapBasicDetection$1;
	var hasRequiredWeakMapBasicDetection$1;

	function requireWeakMapBasicDetection$1 () {
		if (hasRequiredWeakMapBasicDetection$1) return weakMapBasicDetection$1;
		hasRequiredWeakMapBasicDetection$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();

		var WeakMap = globalThis.WeakMap;

		weakMapBasicDetection$1 = isCallable(WeakMap) && /native code/.test(String(WeakMap));
		return weakMapBasicDetection$1;
	}

	var internalState$1;
	var hasRequiredInternalState$1;

	function requireInternalState$1 () {
		if (hasRequiredInternalState$1) return internalState$1;
		hasRequiredInternalState$1 = 1;
		var NATIVE_WEAK_MAP = /*@__PURE__*/ requireWeakMapBasicDetection$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var shared = /*@__PURE__*/ requireSharedStore$1();
		var sharedKey = /*@__PURE__*/ requireSharedKey$1();
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys$1();

		var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
		var TypeError = globalThis.TypeError;
		var WeakMap = globalThis.WeakMap;
		var set, get, has;

		var enforce = function (it) {
		  return has(it) ? get(it) : set(it, {});
		};

		var getterFor = function (TYPE) {
		  return function (it) {
		    var state;
		    if (!isObject(it) || (state = get(it)).type !== TYPE) {
		      throw new TypeError('Incompatible receiver, ' + TYPE + ' required');
		    } return state;
		  };
		};

		if (NATIVE_WEAK_MAP || shared.state) {
		  var store = shared.state || (shared.state = new WeakMap());
		  /* eslint-disable no-self-assign -- prototype methods protection */
		  store.get = store.get;
		  store.has = store.has;
		  store.set = store.set;
		  /* eslint-enable no-self-assign -- prototype methods protection */
		  set = function (it, metadata) {
		    if (store.has(it)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
		    metadata.facade = it;
		    store.set(it, metadata);
		    return metadata;
		  };
		  get = function (it) {
		    return store.get(it) || {};
		  };
		  has = function (it) {
		    return store.has(it);
		  };
		} else {
		  var STATE = sharedKey('state');
		  hiddenKeys[STATE] = true;
		  set = function (it, metadata) {
		    if (hasOwn(it, STATE)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
		    metadata.facade = it;
		    createNonEnumerableProperty(it, STATE, metadata);
		    return metadata;
		  };
		  get = function (it) {
		    return hasOwn(it, STATE) ? it[STATE] : {};
		  };
		  has = function (it) {
		    return hasOwn(it, STATE);
		  };
		}

		internalState$1 = {
		  set: set,
		  get: get,
		  has: has,
		  enforce: enforce,
		  getterFor: getterFor
		};
		return internalState$1;
	}

	var functionName$1;
	var hasRequiredFunctionName$1;

	function requireFunctionName$1 () {
		if (hasRequiredFunctionName$1) return functionName$1;
		hasRequiredFunctionName$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();

		var FunctionPrototype = Function.prototype;
		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var getDescriptor = DESCRIPTORS && Object.getOwnPropertyDescriptor;

		var EXISTS = hasOwn(FunctionPrototype, 'name');
		// additional protection from minified / mangled / dropped function names
		var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
		var CONFIGURABLE = EXISTS && (!DESCRIPTORS || (DESCRIPTORS && getDescriptor(FunctionPrototype, 'name').configurable));

		functionName$1 = {
		  EXISTS: EXISTS,
		  PROPER: PROPER,
		  CONFIGURABLE: CONFIGURABLE
		};
		return functionName$1;
	}

	var defineBuiltIn$1;
	var hasRequiredDefineBuiltIn$1;

	function requireDefineBuiltIn$1 () {
		if (hasRequiredDefineBuiltIn$1) return defineBuiltIn$1;
		hasRequiredDefineBuiltIn$1 = 1;
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();

		defineBuiltIn$1 = function (target, key, value, options) {
		  if (options && options.enumerable) target[key] = value;
		  else createNonEnumerableProperty(target, key, value);
		  return target;
		};
		return defineBuiltIn$1;
	}

	var iteratorsCore$1;
	var hasRequiredIteratorsCore$1;

	function requireIteratorsCore$1 () {
		if (hasRequiredIteratorsCore$1) return iteratorsCore$1;
		hasRequiredIteratorsCore$1 = 1;
		var fails = /*@__PURE__*/ requireFails$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var create = /*@__PURE__*/ requireObjectCreate$1();
		var getPrototypeOf = /*@__PURE__*/ requireObjectGetPrototypeOf$1();
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();

		var ITERATOR = wellKnownSymbol('iterator');
		var BUGGY_SAFARI_ITERATORS = false;

		// `%IteratorPrototype%` object
		// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
		var IteratorPrototype, PrototypeOfArrayIteratorPrototype, arrayIterator;

		/* eslint-disable es/no-array-prototype-keys -- safe */
		if ([].keys) {
		  arrayIterator = [].keys();
		  // Safari 8 has buggy iterators w/o `next`
		  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS = true;
		  else {
		    PrototypeOfArrayIteratorPrototype = getPrototypeOf(getPrototypeOf(arrayIterator));
		    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype = PrototypeOfArrayIteratorPrototype;
		  }
		}

		var NEW_ITERATOR_PROTOTYPE = !isObject(IteratorPrototype) || fails(function () {
		  var test = {};
		  // FF44- legacy iterators case
		  return IteratorPrototype[ITERATOR].call(test) !== test;
		});

		if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype = {};
		else if (IS_PURE) IteratorPrototype = create(IteratorPrototype);

		// `%IteratorPrototype%[@@iterator]()` method
		// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
		if (!isCallable(IteratorPrototype[ITERATOR])) {
		  defineBuiltIn(IteratorPrototype, ITERATOR, function () {
		    return this;
		  });
		}

		iteratorsCore$1 = {
		  IteratorPrototype: IteratorPrototype,
		  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS
		};
		return iteratorsCore$1;
	}

	var objectToString$1;
	var hasRequiredObjectToString$1;

	function requireObjectToString$1 () {
		if (hasRequiredObjectToString$1) return objectToString$1;
		hasRequiredObjectToString$1 = 1;
		var TO_STRING_TAG_SUPPORT = /*@__PURE__*/ requireToStringTagSupport$1();
		var classof = /*@__PURE__*/ requireClassof$1();

		// `Object.prototype.toString` method implementation
		// https://tc39.es/ecma262/#sec-object.prototype.tostring
		objectToString$1 = TO_STRING_TAG_SUPPORT ? {}.toString : function toString() {
		  return '[object ' + classof(this) + ']';
		};
		return objectToString$1;
	}

	var setToStringTag$1;
	var hasRequiredSetToStringTag$1;

	function requireSetToStringTag$1 () {
		if (hasRequiredSetToStringTag$1) return setToStringTag$1;
		hasRequiredSetToStringTag$1 = 1;
		var TO_STRING_TAG_SUPPORT = /*@__PURE__*/ requireToStringTagSupport$1();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty$1().f;
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var toString = /*@__PURE__*/ requireObjectToString$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var TO_STRING_TAG = wellKnownSymbol('toStringTag');

		setToStringTag$1 = function (it, TAG, STATIC, SET_METHOD) {
		  var target = STATIC ? it : it && it.prototype;
		  if (target) {
		    if (!hasOwn(target, TO_STRING_TAG)) {
		      defineProperty(target, TO_STRING_TAG, { configurable: true, value: TAG });
		    }
		    if (SET_METHOD && !TO_STRING_TAG_SUPPORT) {
		      createNonEnumerableProperty(target, 'toString', toString);
		    }
		  }
		};
		return setToStringTag$1;
	}

	var iteratorCreateConstructor$1;
	var hasRequiredIteratorCreateConstructor$1;

	function requireIteratorCreateConstructor$1 () {
		if (hasRequiredIteratorCreateConstructor$1) return iteratorCreateConstructor$1;
		hasRequiredIteratorCreateConstructor$1 = 1;
		var IteratorPrototype = /*@__PURE__*/ requireIteratorsCore$1().IteratorPrototype;
		var create = /*@__PURE__*/ requireObjectCreate$1();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor$1();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag$1();
		var Iterators = /*@__PURE__*/ requireIterators$1();

		var returnThis = function () { return this; };

		iteratorCreateConstructor$1 = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
		  var TO_STRING_TAG = NAME + ' Iterator';
		  IteratorConstructor.prototype = create(IteratorPrototype, { next: createPropertyDescriptor(+!ENUMERABLE_NEXT, next) });
		  setToStringTag(IteratorConstructor, TO_STRING_TAG, false, true);
		  Iterators[TO_STRING_TAG] = returnThis;
		  return IteratorConstructor;
		};
		return iteratorCreateConstructor$1;
	}

	var iteratorDefine$1;
	var hasRequiredIteratorDefine$1;

	function requireIteratorDefine$1 () {
		if (hasRequiredIteratorDefine$1) return iteratorDefine$1;
		hasRequiredIteratorDefine$1 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var FunctionName = /*@__PURE__*/ requireFunctionName$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var createIteratorConstructor = /*@__PURE__*/ requireIteratorCreateConstructor$1();
		var getPrototypeOf = /*@__PURE__*/ requireObjectGetPrototypeOf$1();
		var setPrototypeOf = /*@__PURE__*/ requireObjectSetPrototypeOf$1();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag$1();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var Iterators = /*@__PURE__*/ requireIterators$1();
		var IteratorsCore = /*@__PURE__*/ requireIteratorsCore$1();

		var PROPER_FUNCTION_NAME = FunctionName.PROPER;
		var CONFIGURABLE_FUNCTION_NAME = FunctionName.CONFIGURABLE;
		var IteratorPrototype = IteratorsCore.IteratorPrototype;
		var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
		var ITERATOR = wellKnownSymbol('iterator');
		var KEYS = 'keys';
		var VALUES = 'values';
		var ENTRIES = 'entries';

		var returnThis = function () { return this; };

		iteratorDefine$1 = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
		  createIteratorConstructor(IteratorConstructor, NAME, next);

		  var getIterationMethod = function (KIND) {
		    if (KIND === DEFAULT && defaultIterator) return defaultIterator;
		    if (!BUGGY_SAFARI_ITERATORS && KIND && KIND in IterablePrototype) return IterablePrototype[KIND];

		    switch (KIND) {
		      case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
		      case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
		      case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
		    }

		    return function () { return new IteratorConstructor(this); };
		  };

		  var TO_STRING_TAG = NAME + ' Iterator';
		  var INCORRECT_VALUES_NAME = false;
		  var IterablePrototype = Iterable.prototype;
		  var nativeIterator = IterablePrototype[ITERATOR]
		    || IterablePrototype['@@iterator']
		    || DEFAULT && IterablePrototype[DEFAULT];
		  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
		  var anyNativeIterator = NAME === 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
		  var CurrentIteratorPrototype, methods, KEY;

		  // fix native
		  if (anyNativeIterator) {
		    CurrentIteratorPrototype = getPrototypeOf(anyNativeIterator.call(new Iterable()));
		    if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
		      if (!IS_PURE && getPrototypeOf(CurrentIteratorPrototype) !== IteratorPrototype) {
		        if (setPrototypeOf) {
		          setPrototypeOf(CurrentIteratorPrototype, IteratorPrototype);
		        } else if (!isCallable(CurrentIteratorPrototype[ITERATOR])) {
		          defineBuiltIn(CurrentIteratorPrototype, ITERATOR, returnThis);
		        }
		      }
		      // Set @@toStringTag to native iterators
		      setToStringTag(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
		      if (IS_PURE) Iterators[TO_STRING_TAG] = returnThis;
		    }
		  }

		  // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
		  if (PROPER_FUNCTION_NAME && DEFAULT === VALUES && nativeIterator && nativeIterator.name !== VALUES) {
		    if (!IS_PURE && CONFIGURABLE_FUNCTION_NAME) {
		      createNonEnumerableProperty(IterablePrototype, 'name', VALUES);
		    } else {
		      INCORRECT_VALUES_NAME = true;
		      defaultIterator = function values() { return call(nativeIterator, this); };
		    }
		  }

		  // export additional methods
		  if (DEFAULT) {
		    methods = {
		      values: getIterationMethod(VALUES),
		      keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
		      entries: getIterationMethod(ENTRIES)
		    };
		    if (FORCED) for (KEY in methods) {
		      if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
		        defineBuiltIn(IterablePrototype, KEY, methods[KEY]);
		      }
		    } else $({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
		  }

		  // define iterator
		  if ((!IS_PURE || FORCED) && IterablePrototype[ITERATOR] !== defaultIterator) {
		    defineBuiltIn(IterablePrototype, ITERATOR, defaultIterator, { name: DEFAULT });
		  }
		  Iterators[NAME] = defaultIterator;

		  return methods;
		};
		return iteratorDefine$1;
	}

	var createIterResultObject$1;
	var hasRequiredCreateIterResultObject$1;

	function requireCreateIterResultObject$1 () {
		if (hasRequiredCreateIterResultObject$1) return createIterResultObject$1;
		hasRequiredCreateIterResultObject$1 = 1;
		// `CreateIterResultObject` abstract operation
		// https://tc39.es/ecma262/#sec-createiterresultobject
		createIterResultObject$1 = function (value, done) {
		  return { value: value, done: done };
		};
		return createIterResultObject$1;
	}

	var es_array_iterator$1;
	var hasRequiredEs_array_iterator$1;

	function requireEs_array_iterator$1 () {
		if (hasRequiredEs_array_iterator$1) return es_array_iterator$1;
		hasRequiredEs_array_iterator$1 = 1;
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var addToUnscopables = /*@__PURE__*/ requireAddToUnscopables$1();
		var Iterators = /*@__PURE__*/ requireIterators$1();
		var InternalStateModule = /*@__PURE__*/ requireInternalState$1();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty$1().f;
		var defineIterator = /*@__PURE__*/ requireIteratorDefine$1();
		var createIterResultObject = /*@__PURE__*/ requireCreateIterResultObject$1();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();

		var ARRAY_ITERATOR = 'Array Iterator';
		var setInternalState = InternalStateModule.set;
		var getInternalState = InternalStateModule.getterFor(ARRAY_ITERATOR);

		// `Array.prototype.entries` method
		// https://tc39.es/ecma262/#sec-array.prototype.entries
		// `Array.prototype.keys` method
		// https://tc39.es/ecma262/#sec-array.prototype.keys
		// `Array.prototype.values` method
		// https://tc39.es/ecma262/#sec-array.prototype.values
		// `Array.prototype[@@iterator]` method
		// https://tc39.es/ecma262/#sec-array.prototype-@@iterator
		// `CreateArrayIterator` internal method
		// https://tc39.es/ecma262/#sec-createarrayiterator
		es_array_iterator$1 = defineIterator(Array, 'Array', function (iterated, kind) {
		  setInternalState(this, {
		    type: ARRAY_ITERATOR,
		    target: toIndexedObject(iterated), // target
		    index: 0,                          // next index
		    kind: kind                         // kind
		  });
		// `%ArrayIteratorPrototype%.next` method
		// https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
		}, function () {
		  var state = getInternalState(this);
		  var target = state.target;
		  var index = state.index++;
		  if (!target || index >= target.length) {
		    state.target = null;
		    return createIterResultObject(undefined, true);
		  }
		  switch (state.kind) {
		    case 'keys': return createIterResultObject(index, false);
		    case 'values': return createIterResultObject(target[index], false);
		  } return createIterResultObject([index, target[index]], false);
		}, 'values');

		// argumentsList[@@iterator] is %ArrayProto_values%
		// https://tc39.es/ecma262/#sec-createunmappedargumentsobject
		// https://tc39.es/ecma262/#sec-createmappedargumentsobject
		var values = Iterators.Arguments = Iterators.Array;

		// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
		addToUnscopables('keys');
		addToUnscopables('values');
		addToUnscopables('entries');

		// V8 ~ Chrome 45- bug
		if (!IS_PURE && DESCRIPTORS && values.name !== 'values') try {
		  defineProperty(values, 'name', { value: 'values' });
		} catch (error) { /* empty */ }
		return es_array_iterator$1;
	}

	var es_promise = {};

	var es_promise_constructor = {};

	var environment;
	var hasRequiredEnvironment;

	function requireEnvironment () {
		if (hasRequiredEnvironment) return environment;
		hasRequiredEnvironment = 1;
		/* global Bun, Deno -- detection */
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();
		var classof = /*@__PURE__*/ requireClassofRaw$1();

		var userAgentStartsWith = function (string) {
		  return userAgent.slice(0, string.length) === string;
		};

		environment = (function () {
		  if (userAgentStartsWith('Bun/')) return 'BUN';
		  if (userAgentStartsWith('Cloudflare-Workers')) return 'CLOUDFLARE';
		  if (userAgentStartsWith('Deno/')) return 'DENO';
		  if (userAgentStartsWith('Node.js/')) return 'NODE';
		  if (globalThis.Bun && typeof Bun.version == 'string') return 'BUN';
		  if (globalThis.Deno && typeof Deno.version == 'object') return 'DENO';
		  if (classof(globalThis.process) === 'process') return 'NODE';
		  if (globalThis.window && globalThis.document) return 'BROWSER';
		  return 'REST';
		})();
		return environment;
	}

	var environmentIsNode;
	var hasRequiredEnvironmentIsNode;

	function requireEnvironmentIsNode () {
		if (hasRequiredEnvironmentIsNode) return environmentIsNode;
		hasRequiredEnvironmentIsNode = 1;
		var ENVIRONMENT = /*@__PURE__*/ requireEnvironment();

		environmentIsNode = ENVIRONMENT === 'NODE';
		return environmentIsNode;
	}

	var defineBuiltInAccessor$1;
	var hasRequiredDefineBuiltInAccessor$1;

	function requireDefineBuiltInAccessor$1 () {
		if (hasRequiredDefineBuiltInAccessor$1) return defineBuiltInAccessor$1;
		hasRequiredDefineBuiltInAccessor$1 = 1;
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty$1();

		defineBuiltInAccessor$1 = function (target, name, descriptor) {
		  return defineProperty.f(target, name, descriptor);
		};
		return defineBuiltInAccessor$1;
	}

	var setSpecies$1;
	var hasRequiredSetSpecies$1;

	function requireSetSpecies$1 () {
		if (hasRequiredSetSpecies$1) return setSpecies$1;
		hasRequiredSetSpecies$1 = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var defineBuiltInAccessor = /*@__PURE__*/ requireDefineBuiltInAccessor$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();

		var SPECIES = wellKnownSymbol('species');

		setSpecies$1 = function (CONSTRUCTOR_NAME) {
		  var Constructor = getBuiltIn(CONSTRUCTOR_NAME);

		  if (DESCRIPTORS && Constructor && !Constructor[SPECIES]) {
		    defineBuiltInAccessor(Constructor, SPECIES, {
		      configurable: true,
		      get: function () { return this; }
		    });
		  }
		};
		return setSpecies$1;
	}

	var anInstance$1;
	var hasRequiredAnInstance$1;

	function requireAnInstance$1 () {
		if (hasRequiredAnInstance$1) return anInstance$1;
		hasRequiredAnInstance$1 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();

		var $TypeError = TypeError;

		anInstance$1 = function (it, Prototype) {
		  if (isPrototypeOf(Prototype, it)) return it;
		  throw new $TypeError('Incorrect invocation');
		};
		return anInstance$1;
	}

	var inspectSource$1;
	var hasRequiredInspectSource$1;

	function requireInspectSource$1 () {
		if (hasRequiredInspectSource$1) return inspectSource$1;
		hasRequiredInspectSource$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var store = /*@__PURE__*/ requireSharedStore$1();

		var functionToString = uncurryThis(Function.toString);

		// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
		if (!isCallable(store.inspectSource)) {
		  store.inspectSource = function (it) {
		    return functionToString(it);
		  };
		}

		inspectSource$1 = store.inspectSource;
		return inspectSource$1;
	}

	var isConstructor$1;
	var hasRequiredIsConstructor$1;

	function requireIsConstructor$1 () {
		if (hasRequiredIsConstructor$1) return isConstructor$1;
		hasRequiredIsConstructor$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var classof = /*@__PURE__*/ requireClassof$1();
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var inspectSource = /*@__PURE__*/ requireInspectSource$1();

		var noop = function () { /* empty */ };
		var construct = getBuiltIn('Reflect', 'construct');
		var constructorRegExp = /^\s*(?:class|function)\b/;
		var exec = uncurryThis(constructorRegExp.exec);
		var INCORRECT_TO_STRING = !constructorRegExp.test(noop);

		var isConstructorModern = function isConstructor(argument) {
		  if (!isCallable(argument)) return false;
		  try {
		    construct(noop, [], argument);
		    return true;
		  } catch (error) {
		    return false;
		  }
		};

		var isConstructorLegacy = function isConstructor(argument) {
		  if (!isCallable(argument)) return false;
		  switch (classof(argument)) {
		    case 'AsyncFunction':
		    case 'GeneratorFunction':
		    case 'AsyncGeneratorFunction': return false;
		  }
		  try {
		    // we can't check .prototype since constructors produced by .bind haven't it
		    // `Function#toString` throws on some built-it function in some legacy engines
		    // (for example, `DOMQuad` and similar in FF41-)
		    return INCORRECT_TO_STRING || !!exec(constructorRegExp, inspectSource(argument));
		  } catch (error) {
		    return true;
		  }
		};

		isConstructorLegacy.sham = true;

		// `IsConstructor` abstract operation
		// https://tc39.es/ecma262/#sec-isconstructor
		isConstructor$1 = !construct || fails(function () {
		  var called;
		  return isConstructorModern(isConstructorModern.call)
		    || !isConstructorModern(Object)
		    || !isConstructorModern(function () { called = true; })
		    || called;
		}) ? isConstructorLegacy : isConstructorModern;
		return isConstructor$1;
	}

	var aConstructor;
	var hasRequiredAConstructor;

	function requireAConstructor () {
		if (hasRequiredAConstructor) return aConstructor;
		hasRequiredAConstructor = 1;
		var isConstructor = /*@__PURE__*/ requireIsConstructor$1();
		var tryToString = /*@__PURE__*/ requireTryToString$1();

		var $TypeError = TypeError;

		// `Assert: IsConstructor(argument) is true`
		aConstructor = function (argument) {
		  if (isConstructor(argument)) return argument;
		  throw new $TypeError(tryToString(argument) + ' is not a constructor');
		};
		return aConstructor;
	}

	var speciesConstructor;
	var hasRequiredSpeciesConstructor;

	function requireSpeciesConstructor () {
		if (hasRequiredSpeciesConstructor) return speciesConstructor;
		hasRequiredSpeciesConstructor = 1;
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var aConstructor = /*@__PURE__*/ requireAConstructor();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var SPECIES = wellKnownSymbol('species');

		// `SpeciesConstructor` abstract operation
		// https://tc39.es/ecma262/#sec-speciesconstructor
		speciesConstructor = function (O, defaultConstructor) {
		  var C = anObject(O).constructor;
		  var S;
		  return C === undefined || isNullOrUndefined(S = anObject(C)[SPECIES]) ? defaultConstructor : aConstructor(S);
		};
		return speciesConstructor;
	}

	var arraySlice$1;
	var hasRequiredArraySlice$1;

	function requireArraySlice$1 () {
		if (hasRequiredArraySlice$1) return arraySlice$1;
		hasRequiredArraySlice$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();

		arraySlice$1 = uncurryThis([].slice);
		return arraySlice$1;
	}

	var validateArgumentsLength;
	var hasRequiredValidateArgumentsLength;

	function requireValidateArgumentsLength () {
		if (hasRequiredValidateArgumentsLength) return validateArgumentsLength;
		hasRequiredValidateArgumentsLength = 1;
		var $TypeError = TypeError;

		validateArgumentsLength = function (passed, required) {
		  if (passed < required) throw new $TypeError('Not enough arguments');
		  return passed;
		};
		return validateArgumentsLength;
	}

	var environmentIsIos;
	var hasRequiredEnvironmentIsIos;

	function requireEnvironmentIsIos () {
		if (hasRequiredEnvironmentIsIos) return environmentIsIos;
		hasRequiredEnvironmentIsIos = 1;
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		// eslint-disable-next-line redos/no-vulnerable -- safe
		environmentIsIos = /(?:ipad|iphone|ipod).*applewebkit/i.test(userAgent);
		return environmentIsIos;
	}

	var task;
	var hasRequiredTask;

	function requireTask () {
		if (hasRequiredTask) return task;
		hasRequiredTask = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var apply = /*@__PURE__*/ requireFunctionApply$1();
		var bind = /*@__PURE__*/ requireFunctionBindContext$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var html = /*@__PURE__*/ requireHtml$1();
		var arraySlice = /*@__PURE__*/ requireArraySlice$1();
		var createElement = /*@__PURE__*/ requireDocumentCreateElement$1();
		var validateArgumentsLength = /*@__PURE__*/ requireValidateArgumentsLength();
		var IS_IOS = /*@__PURE__*/ requireEnvironmentIsIos();
		var IS_NODE = /*@__PURE__*/ requireEnvironmentIsNode();

		var set = globalThis.setImmediate;
		var clear = globalThis.clearImmediate;
		var process = globalThis.process;
		var Dispatch = globalThis.Dispatch;
		var Function = globalThis.Function;
		var MessageChannel = globalThis.MessageChannel;
		var String = globalThis.String;
		var counter = 0;
		var queue = {};
		var ONREADYSTATECHANGE = 'onreadystatechange';
		var $location, defer, channel, port;

		fails(function () {
		  // Deno throws a ReferenceError on `location` access without `--location` flag
		  $location = globalThis.location;
		});

		var run = function (id) {
		  if (hasOwn(queue, id)) {
		    var fn = queue[id];
		    delete queue[id];
		    fn();
		  }
		};

		var runner = function (id) {
		  return function () {
		    run(id);
		  };
		};

		var eventListener = function (event) {
		  run(event.data);
		};

		var globalPostMessageDefer = function (id) {
		  // old engines have not location.origin
		  globalThis.postMessage(String(id), $location.protocol + '//' + $location.host);
		};

		// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
		if (!set || !clear) {
		  set = function setImmediate(handler) {
		    validateArgumentsLength(arguments.length, 1);
		    var fn = isCallable(handler) ? handler : Function(handler);
		    var args = arraySlice(arguments, 1);
		    queue[++counter] = function () {
		      apply(fn, undefined, args);
		    };
		    defer(counter);
		    return counter;
		  };
		  clear = function clearImmediate(id) {
		    delete queue[id];
		  };
		  // Node.js 0.8-
		  if (IS_NODE) {
		    defer = function (id) {
		      process.nextTick(runner(id));
		    };
		  // Sphere (JS game engine) Dispatch API
		  } else if (Dispatch && Dispatch.now) {
		    defer = function (id) {
		      Dispatch.now(runner(id));
		    };
		  // Browsers with MessageChannel, includes WebWorkers
		  // except iOS - https://github.com/zloirock/core-js/issues/624
		  } else if (MessageChannel && !IS_IOS) {
		    channel = new MessageChannel();
		    port = channel.port2;
		    channel.port1.onmessage = eventListener;
		    defer = bind(port.postMessage, port);
		  // Browsers with postMessage, skip WebWorkers
		  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
		  } else if (
		    globalThis.addEventListener &&
		    isCallable(globalThis.postMessage) &&
		    !globalThis.importScripts &&
		    $location && $location.protocol !== 'file:' &&
		    !fails(globalPostMessageDefer)
		  ) {
		    defer = globalPostMessageDefer;
		    globalThis.addEventListener('message', eventListener, false);
		  // IE8-
		  } else if (ONREADYSTATECHANGE in createElement('script')) {
		    defer = function (id) {
		      html.appendChild(createElement('script'))[ONREADYSTATECHANGE] = function () {
		        html.removeChild(this);
		        run(id);
		      };
		    };
		  // Rest old browsers
		  } else {
		    defer = function (id) {
		      setTimeout(runner(id), 0);
		    };
		  }
		}

		task = {
		  set: set,
		  clear: clear
		};
		return task;
	}

	var safeGetBuiltIn;
	var hasRequiredSafeGetBuiltIn;

	function requireSafeGetBuiltIn () {
		if (hasRequiredSafeGetBuiltIn) return safeGetBuiltIn;
		hasRequiredSafeGetBuiltIn = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();

		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

		// Avoid NodeJS experimental warning
		safeGetBuiltIn = function (name) {
		  if (!DESCRIPTORS) return globalThis[name];
		  var descriptor = getOwnPropertyDescriptor(globalThis, name);
		  return descriptor && descriptor.value;
		};
		return safeGetBuiltIn;
	}

	var queue;
	var hasRequiredQueue;

	function requireQueue () {
		if (hasRequiredQueue) return queue;
		hasRequiredQueue = 1;
		var Queue = function () {
		  this.head = null;
		  this.tail = null;
		};

		Queue.prototype = {
		  add: function (item) {
		    var entry = { item: item, next: null };
		    var tail = this.tail;
		    if (tail) tail.next = entry;
		    else this.head = entry;
		    this.tail = entry;
		  },
		  get: function () {
		    var entry = this.head;
		    if (entry) {
		      var next = this.head = entry.next;
		      if (next === null) this.tail = null;
		      return entry.item;
		    }
		  }
		};

		queue = Queue;
		return queue;
	}

	var environmentIsIosPebble;
	var hasRequiredEnvironmentIsIosPebble;

	function requireEnvironmentIsIosPebble () {
		if (hasRequiredEnvironmentIsIosPebble) return environmentIsIosPebble;
		hasRequiredEnvironmentIsIosPebble = 1;
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		environmentIsIosPebble = /ipad|iphone|ipod/i.test(userAgent) && typeof Pebble != 'undefined';
		return environmentIsIosPebble;
	}

	var environmentIsWebosWebkit;
	var hasRequiredEnvironmentIsWebosWebkit;

	function requireEnvironmentIsWebosWebkit () {
		if (hasRequiredEnvironmentIsWebosWebkit) return environmentIsWebosWebkit;
		hasRequiredEnvironmentIsWebosWebkit = 1;
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		environmentIsWebosWebkit = /web0s(?!.*chrome)/i.test(userAgent);
		return environmentIsWebosWebkit;
	}

	var microtask_1;
	var hasRequiredMicrotask;

	function requireMicrotask () {
		if (hasRequiredMicrotask) return microtask_1;
		hasRequiredMicrotask = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var safeGetBuiltIn = /*@__PURE__*/ requireSafeGetBuiltIn();
		var bind = /*@__PURE__*/ requireFunctionBindContext$1();
		var macrotask = /*@__PURE__*/ requireTask().set;
		var Queue = /*@__PURE__*/ requireQueue();
		var IS_IOS = /*@__PURE__*/ requireEnvironmentIsIos();
		var IS_IOS_PEBBLE = /*@__PURE__*/ requireEnvironmentIsIosPebble();
		var IS_WEBOS_WEBKIT = /*@__PURE__*/ requireEnvironmentIsWebosWebkit();
		var IS_NODE = /*@__PURE__*/ requireEnvironmentIsNode();

		var MutationObserver = globalThis.MutationObserver || globalThis.WebKitMutationObserver;
		var document = globalThis.document;
		var process = globalThis.process;
		var Promise = globalThis.Promise;
		var microtask = safeGetBuiltIn('queueMicrotask');
		var notify, toggle, node, promise, then;

		// modern engines have queueMicrotask method
		if (!microtask) {
		  var queue = new Queue();

		  var flush = function () {
		    var parent, fn;
		    if (IS_NODE && (parent = process.domain)) parent.exit();
		    while (fn = queue.get()) try {
		      fn();
		    } catch (error) {
		      if (queue.head) notify();
		      throw error;
		    }
		    if (parent) parent.enter();
		  };

		  // browsers with MutationObserver, except iOS - https://github.com/zloirock/core-js/issues/339
		  // also except WebOS Webkit https://github.com/zloirock/core-js/issues/898
		  if (!IS_IOS && !IS_NODE && !IS_WEBOS_WEBKIT && MutationObserver && document) {
		    toggle = true;
		    node = document.createTextNode('');
		    new MutationObserver(flush).observe(node, { characterData: true });
		    notify = function () {
		      node.data = toggle = !toggle;
		    };
		  // environments with maybe non-completely correct, but existent Promise
		  } else if (!IS_IOS_PEBBLE && Promise && Promise.resolve) {
		    // Promise.resolve without an argument throws an error in LG WebOS 2
		    promise = Promise.resolve(undefined);
		    // workaround of WebKit ~ iOS Safari 10.1 bug
		    promise.constructor = Promise;
		    then = bind(promise.then, promise);
		    notify = function () {
		      then(flush);
		    };
		  // Node.js without promises
		  } else if (IS_NODE) {
		    notify = function () {
		      process.nextTick(flush);
		    };
		  // for other environments - macrotask based on:
		  // - setImmediate
		  // - MessageChannel
		  // - window.postMessage
		  // - onreadystatechange
		  // - setTimeout
		  } else {
		    // `webpack` dev server bug on IE global methods - use bind(fn, global)
		    macrotask = bind(macrotask, globalThis);
		    notify = function () {
		      macrotask(flush);
		    };
		  }

		  microtask = function (fn) {
		    if (!queue.head) notify();
		    queue.add(fn);
		  };
		}

		microtask_1 = microtask;
		return microtask_1;
	}

	var hostReportErrors;
	var hasRequiredHostReportErrors;

	function requireHostReportErrors () {
		if (hasRequiredHostReportErrors) return hostReportErrors;
		hasRequiredHostReportErrors = 1;
		hostReportErrors = function (a, b) {
		  try {
		    // eslint-disable-next-line no-console -- safe
		    arguments.length === 1 ? console.error(a) : console.error(a, b);
		  } catch (error) { /* empty */ }
		};
		return hostReportErrors;
	}

	var perform;
	var hasRequiredPerform;

	function requirePerform () {
		if (hasRequiredPerform) return perform;
		hasRequiredPerform = 1;
		perform = function (exec) {
		  try {
		    return { error: false, value: exec() };
		  } catch (error) {
		    return { error: true, value: error };
		  }
		};
		return perform;
	}

	var promiseNativeConstructor;
	var hasRequiredPromiseNativeConstructor;

	function requirePromiseNativeConstructor () {
		if (hasRequiredPromiseNativeConstructor) return promiseNativeConstructor;
		hasRequiredPromiseNativeConstructor = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();

		promiseNativeConstructor = globalThis.Promise;
		return promiseNativeConstructor;
	}

	var promiseConstructorDetection;
	var hasRequiredPromiseConstructorDetection;

	function requirePromiseConstructorDetection () {
		if (hasRequiredPromiseConstructorDetection) return promiseConstructorDetection;
		hasRequiredPromiseConstructorDetection = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var NativePromiseConstructor = /*@__PURE__*/ requirePromiseNativeConstructor();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var isForced = /*@__PURE__*/ requireIsForced$1();
		var inspectSource = /*@__PURE__*/ requireInspectSource$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var ENVIRONMENT = /*@__PURE__*/ requireEnvironment();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var V8_VERSION = /*@__PURE__*/ requireEnvironmentV8Version$1();

		var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;
		var SPECIES = wellKnownSymbol('species');
		var SUBCLASSING = false;
		var NATIVE_PROMISE_REJECTION_EVENT = isCallable(globalThis.PromiseRejectionEvent);

		var FORCED_PROMISE_CONSTRUCTOR = isForced('Promise', function () {
		  var PROMISE_CONSTRUCTOR_SOURCE = inspectSource(NativePromiseConstructor);
		  var GLOBAL_CORE_JS_PROMISE = PROMISE_CONSTRUCTOR_SOURCE !== String(NativePromiseConstructor);
		  // V8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
		  // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
		  // We can't detect it synchronously, so just check versions
		  if (!GLOBAL_CORE_JS_PROMISE && V8_VERSION === 66) return true;
		  // We need Promise#{ catch, finally } in the pure version for preventing prototype pollution
		  if (IS_PURE && !(NativePromisePrototype['catch'] && NativePromisePrototype['finally'])) return true;
		  // We can't use @@species feature detection in V8 since it causes
		  // deoptimization and performance degradation
		  // https://github.com/zloirock/core-js/issues/679
		  if (!V8_VERSION || V8_VERSION < 51 || !/native code/.test(PROMISE_CONSTRUCTOR_SOURCE)) {
		    // Detect correctness of subclassing with @@species support
		    var promise = new NativePromiseConstructor(function (resolve) { resolve(1); });
		    var FakePromise = function (exec) {
		      exec(function () { /* empty */ }, function () { /* empty */ });
		    };
		    var constructor = promise.constructor = {};
		    constructor[SPECIES] = FakePromise;
		    SUBCLASSING = promise.then(function () { /* empty */ }) instanceof FakePromise;
		    if (!SUBCLASSING) return true;
		  // Unhandled rejections tracking support, NodeJS Promise without it fails @@species test
		  } return !GLOBAL_CORE_JS_PROMISE && (ENVIRONMENT === 'BROWSER' || ENVIRONMENT === 'DENO') && !NATIVE_PROMISE_REJECTION_EVENT;
		});

		promiseConstructorDetection = {
		  CONSTRUCTOR: FORCED_PROMISE_CONSTRUCTOR,
		  REJECTION_EVENT: NATIVE_PROMISE_REJECTION_EVENT,
		  SUBCLASSING: SUBCLASSING
		};
		return promiseConstructorDetection;
	}

	var newPromiseCapability = {};

	var hasRequiredNewPromiseCapability;

	function requireNewPromiseCapability () {
		if (hasRequiredNewPromiseCapability) return newPromiseCapability;
		hasRequiredNewPromiseCapability = 1;
		var aCallable = /*@__PURE__*/ requireACallable$1();

		var $TypeError = TypeError;

		var PromiseCapability = function (C) {
		  var resolve, reject;
		  this.promise = new C(function ($$resolve, $$reject) {
		    if (resolve !== undefined || reject !== undefined) throw new $TypeError('Bad Promise constructor');
		    resolve = $$resolve;
		    reject = $$reject;
		  });
		  this.resolve = aCallable(resolve);
		  this.reject = aCallable(reject);
		};

		// `NewPromiseCapability` abstract operation
		// https://tc39.es/ecma262/#sec-newpromisecapability
		newPromiseCapability.f = function (C) {
		  return new PromiseCapability(C);
		};
		return newPromiseCapability;
	}

	var hasRequiredEs_promise_constructor;

	function requireEs_promise_constructor () {
		if (hasRequiredEs_promise_constructor) return es_promise_constructor;
		hasRequiredEs_promise_constructor = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var IS_NODE = /*@__PURE__*/ requireEnvironmentIsNode();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var path = /*@__PURE__*/ requirePath$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn$1();
		var setPrototypeOf = /*@__PURE__*/ requireObjectSetPrototypeOf$1();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag$1();
		var setSpecies = /*@__PURE__*/ requireSetSpecies$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var anInstance = /*@__PURE__*/ requireAnInstance$1();
		var speciesConstructor = /*@__PURE__*/ requireSpeciesConstructor();
		var task = /*@__PURE__*/ requireTask().set;
		var microtask = /*@__PURE__*/ requireMicrotask();
		var hostReportErrors = /*@__PURE__*/ requireHostReportErrors();
		var perform = /*@__PURE__*/ requirePerform();
		var Queue = /*@__PURE__*/ requireQueue();
		var InternalStateModule = /*@__PURE__*/ requireInternalState$1();
		var NativePromiseConstructor = /*@__PURE__*/ requirePromiseNativeConstructor();
		var PromiseConstructorDetection = /*@__PURE__*/ requirePromiseConstructorDetection();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();

		var PROMISE = 'Promise';
		var FORCED_PROMISE_CONSTRUCTOR = PromiseConstructorDetection.CONSTRUCTOR;
		var NATIVE_PROMISE_REJECTION_EVENT = PromiseConstructorDetection.REJECTION_EVENT;
		var NATIVE_PROMISE_SUBCLASSING = PromiseConstructorDetection.SUBCLASSING;
		var getInternalPromiseState = InternalStateModule.getterFor(PROMISE);
		var setInternalState = InternalStateModule.set;
		var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;
		var PromiseConstructor = NativePromiseConstructor;
		var PromisePrototype = NativePromisePrototype;
		var TypeError = globalThis.TypeError;
		var document = globalThis.document;
		var process = globalThis.process;
		var newPromiseCapability = newPromiseCapabilityModule.f;
		var newGenericPromiseCapability = newPromiseCapability;

		var DISPATCH_EVENT = !!(document && document.createEvent && globalThis.dispatchEvent);
		var UNHANDLED_REJECTION = 'unhandledrejection';
		var REJECTION_HANDLED = 'rejectionhandled';
		var PENDING = 0;
		var FULFILLED = 1;
		var REJECTED = 2;
		var HANDLED = 1;
		var UNHANDLED = 2;

		var Internal, OwnPromiseCapability, PromiseWrapper, nativeThen;

		// helpers
		var isThenable = function (it) {
		  var then;
		  return isObject(it) && isCallable(then = it.then) ? then : false;
		};

		var callReaction = function (reaction, state) {
		  var value = state.value;
		  var ok = state.state === FULFILLED;
		  var handler = ok ? reaction.ok : reaction.fail;
		  var resolve = reaction.resolve;
		  var reject = reaction.reject;
		  var domain = reaction.domain;
		  var result, then, exited;
		  try {
		    if (handler) {
		      if (!ok) {
		        if (state.rejection === UNHANDLED) onHandleUnhandled(state);
		        state.rejection = HANDLED;
		      }
		      if (handler === true) result = value;
		      else {
		        if (domain) domain.enter();
		        result = handler(value); // can throw
		        if (domain) {
		          domain.exit();
		          exited = true;
		        }
		      }
		      if (result === reaction.promise) {
		        reject(new TypeError('Promise-chain cycle'));
		      } else if (then = isThenable(result)) {
		        call(then, result, resolve, reject);
		      } else resolve(result);
		    } else reject(value);
		  } catch (error) {
		    if (domain && !exited) domain.exit();
		    reject(error);
		  }
		};

		var notify = function (state, isReject) {
		  if (state.notified) return;
		  state.notified = true;
		  microtask(function () {
		    var reactions = state.reactions;
		    var reaction;
		    while (reaction = reactions.get()) {
		      callReaction(reaction, state);
		    }
		    state.notified = false;
		    if (isReject && !state.rejection) onUnhandled(state);
		  });
		};

		var dispatchEvent = function (name, promise, reason) {
		  var event, handler;
		  if (DISPATCH_EVENT) {
		    event = document.createEvent('Event');
		    event.promise = promise;
		    event.reason = reason;
		    event.initEvent(name, false, true);
		    globalThis.dispatchEvent(event);
		  } else event = { promise: promise, reason: reason };
		  if (!NATIVE_PROMISE_REJECTION_EVENT && (handler = globalThis['on' + name])) handler(event);
		  else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
		};

		var onUnhandled = function (state) {
		  call(task, globalThis, function () {
		    var promise = state.facade;
		    var value = state.value;
		    var IS_UNHANDLED = isUnhandled(state);
		    var result;
		    if (IS_UNHANDLED) {
		      result = perform(function () {
		        if (IS_NODE) {
		          process.emit('unhandledRejection', value, promise);
		        } else dispatchEvent(UNHANDLED_REJECTION, promise, value);
		      });
		      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
		      state.rejection = IS_NODE || isUnhandled(state) ? UNHANDLED : HANDLED;
		      if (result.error) throw result.value;
		    }
		  });
		};

		var isUnhandled = function (state) {
		  return state.rejection !== HANDLED && !state.parent;
		};

		var onHandleUnhandled = function (state) {
		  call(task, globalThis, function () {
		    var promise = state.facade;
		    if (IS_NODE) {
		      process.emit('rejectionHandled', promise);
		    } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
		  });
		};

		var bind = function (fn, state, unwrap) {
		  return function (value) {
		    fn(state, value, unwrap);
		  };
		};

		var internalReject = function (state, value, unwrap) {
		  if (state.done) return;
		  state.done = true;
		  if (unwrap) state = unwrap;
		  state.value = value;
		  state.state = REJECTED;
		  notify(state, true);
		};

		var internalResolve = function (state, value, unwrap) {
		  if (state.done) return;
		  state.done = true;
		  if (unwrap) state = unwrap;
		  try {
		    if (state.facade === value) throw new TypeError("Promise can't be resolved itself");
		    var then = isThenable(value);
		    if (then) {
		      microtask(function () {
		        var wrapper = { done: false };
		        try {
		          call(then, value,
		            bind(internalResolve, wrapper, state),
		            bind(internalReject, wrapper, state)
		          );
		        } catch (error) {
		          internalReject(wrapper, error, state);
		        }
		      });
		    } else {
		      state.value = value;
		      state.state = FULFILLED;
		      notify(state, false);
		    }
		  } catch (error) {
		    internalReject({ done: false }, error, state);
		  }
		};

		// constructor polyfill
		if (FORCED_PROMISE_CONSTRUCTOR) {
		  // 25.4.3.1 Promise(executor)
		  PromiseConstructor = function Promise(executor) {
		    anInstance(this, PromisePrototype);
		    aCallable(executor);
		    call(Internal, this);
		    var state = getInternalPromiseState(this);
		    try {
		      executor(bind(internalResolve, state), bind(internalReject, state));
		    } catch (error) {
		      internalReject(state, error);
		    }
		  };

		  PromisePrototype = PromiseConstructor.prototype;

		  // eslint-disable-next-line no-unused-vars -- required for `.length`
		  Internal = function Promise(executor) {
		    setInternalState(this, {
		      type: PROMISE,
		      done: false,
		      notified: false,
		      parent: false,
		      reactions: new Queue(),
		      rejection: false,
		      state: PENDING,
		      value: null
		    });
		  };

		  // `Promise.prototype.then` method
		  // https://tc39.es/ecma262/#sec-promise.prototype.then
		  Internal.prototype = defineBuiltIn(PromisePrototype, 'then', function then(onFulfilled, onRejected) {
		    var state = getInternalPromiseState(this);
		    var reaction = newPromiseCapability(speciesConstructor(this, PromiseConstructor));
		    state.parent = true;
		    reaction.ok = isCallable(onFulfilled) ? onFulfilled : true;
		    reaction.fail = isCallable(onRejected) && onRejected;
		    reaction.domain = IS_NODE ? process.domain : undefined;
		    if (state.state === PENDING) state.reactions.add(reaction);
		    else microtask(function () {
		      callReaction(reaction, state);
		    });
		    return reaction.promise;
		  });

		  OwnPromiseCapability = function () {
		    var promise = new Internal();
		    var state = getInternalPromiseState(promise);
		    this.promise = promise;
		    this.resolve = bind(internalResolve, state);
		    this.reject = bind(internalReject, state);
		  };

		  newPromiseCapabilityModule.f = newPromiseCapability = function (C) {
		    return C === PromiseConstructor || C === PromiseWrapper
		      ? new OwnPromiseCapability(C)
		      : newGenericPromiseCapability(C);
		  };

		  if (!IS_PURE && isCallable(NativePromiseConstructor) && NativePromisePrototype !== Object.prototype) {
		    nativeThen = NativePromisePrototype.then;

		    if (!NATIVE_PROMISE_SUBCLASSING) {
		      // make `Promise#then` return a polyfilled `Promise` for native promise-based APIs
		      defineBuiltIn(NativePromisePrototype, 'then', function then(onFulfilled, onRejected) {
		        var that = this;
		        return new PromiseConstructor(function (resolve, reject) {
		          call(nativeThen, that, resolve, reject);
		        }).then(onFulfilled, onRejected);
		      // https://github.com/zloirock/core-js/issues/640
		      }, { unsafe: true });
		    }

		    // make `.constructor === Promise` work for native promise-based APIs
		    try {
		      delete NativePromisePrototype.constructor;
		    } catch (error) { /* empty */ }

		    // make `instanceof Promise` work for native promise-based APIs
		    if (setPrototypeOf) {
		      setPrototypeOf(NativePromisePrototype, PromisePrototype);
		    }
		  }
		}

		// `Promise` constructor
		// https://tc39.es/ecma262/#sec-promise-executor
		$({ global: true, constructor: true, wrap: true, forced: FORCED_PROMISE_CONSTRUCTOR }, {
		  Promise: PromiseConstructor
		});

		PromiseWrapper = path.Promise;

		setToStringTag(PromiseConstructor, PROMISE, false, true);
		setSpecies(PROMISE);
		return es_promise_constructor;
	}

	var es_promise_all = {};

	var checkCorrectnessOfIteration;
	var hasRequiredCheckCorrectnessOfIteration;

	function requireCheckCorrectnessOfIteration () {
		if (hasRequiredCheckCorrectnessOfIteration) return checkCorrectnessOfIteration;
		hasRequiredCheckCorrectnessOfIteration = 1;
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var ITERATOR = wellKnownSymbol('iterator');
		var SAFE_CLOSING = false;

		try {
		  var called = 0;
		  var iteratorWithReturn = {
		    next: function () {
		      return { done: !!called++ };
		    },
		    'return': function () {
		      SAFE_CLOSING = true;
		    }
		  };
		  iteratorWithReturn[ITERATOR] = function () {
		    return this;
		  };
		  // eslint-disable-next-line es/no-array-from, no-throw-literal -- required for testing
		  Array.from(iteratorWithReturn, function () { throw 2; });
		} catch (error) { /* empty */ }

		checkCorrectnessOfIteration = function (exec, SKIP_CLOSING) {
		  try {
		    if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
		  } catch (error) { return false; } // workaround of old WebKit + `eval` bug
		  var ITERATION_SUPPORT = false;
		  try {
		    var object = {};
		    object[ITERATOR] = function () {
		      return {
		        next: function () {
		          return { done: ITERATION_SUPPORT = true };
		        }
		      };
		    };
		    exec(object);
		  } catch (error) { /* empty */ }
		  return ITERATION_SUPPORT;
		};
		return checkCorrectnessOfIteration;
	}

	var promiseStaticsIncorrectIteration;
	var hasRequiredPromiseStaticsIncorrectIteration;

	function requirePromiseStaticsIncorrectIteration () {
		if (hasRequiredPromiseStaticsIncorrectIteration) return promiseStaticsIncorrectIteration;
		hasRequiredPromiseStaticsIncorrectIteration = 1;
		var NativePromiseConstructor = /*@__PURE__*/ requirePromiseNativeConstructor();
		var checkCorrectnessOfIteration = /*@__PURE__*/ requireCheckCorrectnessOfIteration();
		var FORCED_PROMISE_CONSTRUCTOR = /*@__PURE__*/ requirePromiseConstructorDetection().CONSTRUCTOR;

		promiseStaticsIncorrectIteration = FORCED_PROMISE_CONSTRUCTOR || !checkCorrectnessOfIteration(function (iterable) {
		  NativePromiseConstructor.all(iterable).then(undefined, function () { /* empty */ });
		});
		return promiseStaticsIncorrectIteration;
	}

	var hasRequiredEs_promise_all;

	function requireEs_promise_all () {
		if (hasRequiredEs_promise_all) return es_promise_all;
		hasRequiredEs_promise_all = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();
		var perform = /*@__PURE__*/ requirePerform();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var PROMISE_STATICS_INCORRECT_ITERATION = /*@__PURE__*/ requirePromiseStaticsIncorrectIteration();

		// `Promise.all` method
		// https://tc39.es/ecma262/#sec-promise.all
		$({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
		  all: function all(iterable) {
		    var C = this;
		    var capability = newPromiseCapabilityModule.f(C);
		    var resolve = capability.resolve;
		    var reject = capability.reject;
		    var result = perform(function () {
		      var $promiseResolve = aCallable(C.resolve);
		      var values = [];
		      var counter = 0;
		      var remaining = 1;
		      iterate(iterable, function (promise) {
		        var index = counter++;
		        var alreadyCalled = false;
		        remaining++;
		        call($promiseResolve, C, promise).then(function (value) {
		          if (alreadyCalled) return;
		          alreadyCalled = true;
		          values[index] = value;
		          --remaining || resolve(values);
		        }, reject);
		      });
		      --remaining || resolve(values);
		    });
		    if (result.error) reject(result.value);
		    return capability.promise;
		  }
		});
		return es_promise_all;
	}

	var es_promise_catch = {};

	var hasRequiredEs_promise_catch;

	function requireEs_promise_catch () {
		if (hasRequiredEs_promise_catch) return es_promise_catch;
		hasRequiredEs_promise_catch = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var FORCED_PROMISE_CONSTRUCTOR = /*@__PURE__*/ requirePromiseConstructorDetection().CONSTRUCTOR;
		var NativePromiseConstructor = /*@__PURE__*/ requirePromiseNativeConstructor();
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn$1();

		var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;

		// `Promise.prototype.catch` method
		// https://tc39.es/ecma262/#sec-promise.prototype.catch
		$({ target: 'Promise', proto: true, forced: FORCED_PROMISE_CONSTRUCTOR, real: true }, {
		  'catch': function (onRejected) {
		    return this.then(undefined, onRejected);
		  }
		});

		// makes sure that native promise-based APIs `Promise#catch` properly works with patched `Promise#then`
		if (!IS_PURE && isCallable(NativePromiseConstructor)) {
		  var method = getBuiltIn('Promise').prototype['catch'];
		  if (NativePromisePrototype['catch'] !== method) {
		    defineBuiltIn(NativePromisePrototype, 'catch', method, { unsafe: true });
		  }
		}
		return es_promise_catch;
	}

	var es_promise_race = {};

	var hasRequiredEs_promise_race;

	function requireEs_promise_race () {
		if (hasRequiredEs_promise_race) return es_promise_race;
		hasRequiredEs_promise_race = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();
		var perform = /*@__PURE__*/ requirePerform();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var PROMISE_STATICS_INCORRECT_ITERATION = /*@__PURE__*/ requirePromiseStaticsIncorrectIteration();

		// `Promise.race` method
		// https://tc39.es/ecma262/#sec-promise.race
		$({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
		  race: function race(iterable) {
		    var C = this;
		    var capability = newPromiseCapabilityModule.f(C);
		    var reject = capability.reject;
		    var result = perform(function () {
		      var $promiseResolve = aCallable(C.resolve);
		      iterate(iterable, function (promise) {
		        call($promiseResolve, C, promise).then(capability.resolve, reject);
		      });
		    });
		    if (result.error) reject(result.value);
		    return capability.promise;
		  }
		});
		return es_promise_race;
	}

	var es_promise_reject = {};

	var hasRequiredEs_promise_reject;

	function requireEs_promise_reject () {
		if (hasRequiredEs_promise_reject) return es_promise_reject;
		hasRequiredEs_promise_reject = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();
		var FORCED_PROMISE_CONSTRUCTOR = /*@__PURE__*/ requirePromiseConstructorDetection().CONSTRUCTOR;

		// `Promise.reject` method
		// https://tc39.es/ecma262/#sec-promise.reject
		$({ target: 'Promise', stat: true, forced: FORCED_PROMISE_CONSTRUCTOR }, {
		  reject: function reject(r) {
		    var capability = newPromiseCapabilityModule.f(this);
		    var capabilityReject = capability.reject;
		    capabilityReject(r);
		    return capability.promise;
		  }
		});
		return es_promise_reject;
	}

	var es_promise_resolve = {};

	var promiseResolve;
	var hasRequiredPromiseResolve;

	function requirePromiseResolve () {
		if (hasRequiredPromiseResolve) return promiseResolve;
		hasRequiredPromiseResolve = 1;
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var newPromiseCapability = /*@__PURE__*/ requireNewPromiseCapability();

		promiseResolve = function (C, x) {
		  anObject(C);
		  if (isObject(x) && x.constructor === C) return x;
		  var promiseCapability = newPromiseCapability.f(C);
		  var resolve = promiseCapability.resolve;
		  resolve(x);
		  return promiseCapability.promise;
		};
		return promiseResolve;
	}

	var hasRequiredEs_promise_resolve;

	function requireEs_promise_resolve () {
		if (hasRequiredEs_promise_resolve) return es_promise_resolve;
		hasRequiredEs_promise_resolve = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var NativePromiseConstructor = /*@__PURE__*/ requirePromiseNativeConstructor();
		var FORCED_PROMISE_CONSTRUCTOR = /*@__PURE__*/ requirePromiseConstructorDetection().CONSTRUCTOR;
		var promiseResolve = /*@__PURE__*/ requirePromiseResolve();

		var PromiseConstructorWrapper = getBuiltIn('Promise');
		var CHECK_WRAPPER = IS_PURE && !FORCED_PROMISE_CONSTRUCTOR;

		// `Promise.resolve` method
		// https://tc39.es/ecma262/#sec-promise.resolve
		$({ target: 'Promise', stat: true, forced: IS_PURE || FORCED_PROMISE_CONSTRUCTOR }, {
		  resolve: function resolve(x) {
		    return promiseResolve(CHECK_WRAPPER && this === PromiseConstructorWrapper ? NativePromiseConstructor : this, x);
		  }
		});
		return es_promise_resolve;
	}

	var hasRequiredEs_promise;

	function requireEs_promise () {
		if (hasRequiredEs_promise) return es_promise;
		hasRequiredEs_promise = 1;
		// TODO: Remove this module from `core-js@4` since it's split to modules listed below
		requireEs_promise_constructor();
		requireEs_promise_all();
		requireEs_promise_catch();
		requireEs_promise_race();
		requireEs_promise_reject();
		requireEs_promise_resolve();
		return es_promise;
	}

	var es_promise_allSettled = {};

	var hasRequiredEs_promise_allSettled;

	function requireEs_promise_allSettled () {
		if (hasRequiredEs_promise_allSettled) return es_promise_allSettled;
		hasRequiredEs_promise_allSettled = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();
		var perform = /*@__PURE__*/ requirePerform();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var PROMISE_STATICS_INCORRECT_ITERATION = /*@__PURE__*/ requirePromiseStaticsIncorrectIteration();

		// `Promise.allSettled` method
		// https://tc39.es/ecma262/#sec-promise.allsettled
		$({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
		  allSettled: function allSettled(iterable) {
		    var C = this;
		    var capability = newPromiseCapabilityModule.f(C);
		    var resolve = capability.resolve;
		    var reject = capability.reject;
		    var result = perform(function () {
		      var promiseResolve = aCallable(C.resolve);
		      var values = [];
		      var counter = 0;
		      var remaining = 1;
		      iterate(iterable, function (promise) {
		        var index = counter++;
		        var alreadyCalled = false;
		        remaining++;
		        call(promiseResolve, C, promise).then(function (value) {
		          if (alreadyCalled) return;
		          alreadyCalled = true;
		          values[index] = { status: 'fulfilled', value: value };
		          --remaining || resolve(values);
		        }, function (error) {
		          if (alreadyCalled) return;
		          alreadyCalled = true;
		          values[index] = { status: 'rejected', reason: error };
		          --remaining || resolve(values);
		        });
		      });
		      --remaining || resolve(values);
		    });
		    if (result.error) reject(result.value);
		    return capability.promise;
		  }
		});
		return es_promise_allSettled;
	}

	var es_promise_any = {};

	var hasRequiredEs_promise_any;

	function requireEs_promise_any () {
		if (hasRequiredEs_promise_any) return es_promise_any;
		hasRequiredEs_promise_any = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();
		var perform = /*@__PURE__*/ requirePerform();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var PROMISE_STATICS_INCORRECT_ITERATION = /*@__PURE__*/ requirePromiseStaticsIncorrectIteration();

		var PROMISE_ANY_ERROR = 'No one promise resolved';

		// `Promise.any` method
		// https://tc39.es/ecma262/#sec-promise.any
		$({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
		  any: function any(iterable) {
		    var C = this;
		    var AggregateError = getBuiltIn('AggregateError');
		    var capability = newPromiseCapabilityModule.f(C);
		    var resolve = capability.resolve;
		    var reject = capability.reject;
		    var result = perform(function () {
		      var promiseResolve = aCallable(C.resolve);
		      var errors = [];
		      var counter = 0;
		      var remaining = 1;
		      var alreadyResolved = false;
		      iterate(iterable, function (promise) {
		        var index = counter++;
		        var alreadyRejected = false;
		        remaining++;
		        call(promiseResolve, C, promise).then(function (value) {
		          if (alreadyRejected || alreadyResolved) return;
		          alreadyResolved = true;
		          resolve(value);
		        }, function (error) {
		          if (alreadyRejected || alreadyResolved) return;
		          alreadyRejected = true;
		          errors[index] = error;
		          --remaining || reject(new AggregateError(errors, PROMISE_ANY_ERROR));
		        });
		      });
		      --remaining || reject(new AggregateError(errors, PROMISE_ANY_ERROR));
		    });
		    if (result.error) reject(result.value);
		    return capability.promise;
		  }
		});
		return es_promise_any;
	}

	var es_promise_try = {};

	var hasRequiredEs_promise_try;

	function requireEs_promise_try () {
		if (hasRequiredEs_promise_try) return es_promise_try;
		hasRequiredEs_promise_try = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var apply = /*@__PURE__*/ requireFunctionApply$1();
		var slice = /*@__PURE__*/ requireArraySlice$1();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var perform = /*@__PURE__*/ requirePerform();

		var Promise = globalThis.Promise;

		var ACCEPT_ARGUMENTS = false;
		// Avoiding the use of polyfills of the previous iteration of this proposal
		// that does not accept arguments of the callback
		var FORCED = !Promise || !Promise['try'] || perform(function () {
		  Promise['try'](function (argument) {
		    ACCEPT_ARGUMENTS = argument === 8;
		  }, 8);
		}).error || !ACCEPT_ARGUMENTS;

		// `Promise.try` method
		// https://tc39.es/ecma262/#sec-promise.try
		$({ target: 'Promise', stat: true, forced: FORCED }, {
		  'try': function (callbackfn /* , ...args */) {
		    var args = arguments.length > 1 ? slice(arguments, 1) : [];
		    var promiseCapability = newPromiseCapabilityModule.f(this);
		    var result = perform(function () {
		      return apply(aCallable(callbackfn), undefined, args);
		    });
		    (result.error ? promiseCapability.reject : promiseCapability.resolve)(result.value);
		    return promiseCapability.promise;
		  }
		});
		return es_promise_try;
	}

	var es_promise_withResolvers = {};

	var hasRequiredEs_promise_withResolvers;

	function requireEs_promise_withResolvers () {
		if (hasRequiredEs_promise_withResolvers) return es_promise_withResolvers;
		hasRequiredEs_promise_withResolvers = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var newPromiseCapabilityModule = /*@__PURE__*/ requireNewPromiseCapability();

		// `Promise.withResolvers` method
		// https://tc39.es/ecma262/#sec-promise.withResolvers
		$({ target: 'Promise', stat: true }, {
		  withResolvers: function withResolvers() {
		    var promiseCapability = newPromiseCapabilityModule.f(this);
		    return {
		      promise: promiseCapability.promise,
		      resolve: promiseCapability.resolve,
		      reject: promiseCapability.reject
		    };
		  }
		});
		return es_promise_withResolvers;
	}

	var es_promise_finally = {};

	var hasRequiredEs_promise_finally;

	function requireEs_promise_finally () {
		if (hasRequiredEs_promise_finally) return es_promise_finally;
		hasRequiredEs_promise_finally = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var NativePromiseConstructor = /*@__PURE__*/ requirePromiseNativeConstructor();
		var fails = /*@__PURE__*/ requireFails$1();
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var speciesConstructor = /*@__PURE__*/ requireSpeciesConstructor();
		var promiseResolve = /*@__PURE__*/ requirePromiseResolve();
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn$1();

		var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;

		// Safari bug https://bugs.webkit.org/show_bug.cgi?id=200829
		var NON_GENERIC = !!NativePromiseConstructor && fails(function () {
		  // eslint-disable-next-line unicorn/no-thenable -- required for testing
		  NativePromisePrototype['finally'].call({ then: function () { /* empty */ } }, function () { /* empty */ });
		});

		// `Promise.prototype.finally` method
		// https://tc39.es/ecma262/#sec-promise.prototype.finally
		$({ target: 'Promise', proto: true, real: true, forced: NON_GENERIC }, {
		  'finally': function (onFinally) {
		    var C = speciesConstructor(this, getBuiltIn('Promise'));
		    var isFunction = isCallable(onFinally);
		    return this.then(
		      isFunction ? function (x) {
		        return promiseResolve(C, onFinally()).then(function () { return x; });
		      } : onFinally,
		      isFunction ? function (e) {
		        return promiseResolve(C, onFinally()).then(function () { throw e; });
		      } : onFinally
		    );
		  }
		});

		// makes sure that native promise-based APIs `Promise#finally` properly works with patched `Promise#then`
		if (!IS_PURE && isCallable(NativePromiseConstructor)) {
		  var method = getBuiltIn('Promise').prototype['finally'];
		  if (NativePromisePrototype['finally'] !== method) {
		    defineBuiltIn(NativePromisePrototype, 'finally', method, { unsafe: true });
		  }
		}
		return es_promise_finally;
	}

	var es_string_iterator$1 = {};

	var stringMultibyte$1;
	var hasRequiredStringMultibyte$1;

	function requireStringMultibyte$1 () {
		if (hasRequiredStringMultibyte$1) return stringMultibyte$1;
		hasRequiredStringMultibyte$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity$1();
		var toString = /*@__PURE__*/ requireToString$1();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();

		var charAt = uncurryThis(''.charAt);
		var charCodeAt = uncurryThis(''.charCodeAt);
		var stringSlice = uncurryThis(''.slice);

		var createMethod = function (CONVERT_TO_STRING) {
		  return function ($this, pos) {
		    var S = toString(requireObjectCoercible($this));
		    var position = toIntegerOrInfinity(pos);
		    var size = S.length;
		    var first, second;
		    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
		    first = charCodeAt(S, position);
		    return first < 0xD800 || first > 0xDBFF || position + 1 === size
		      || (second = charCodeAt(S, position + 1)) < 0xDC00 || second > 0xDFFF
		        ? CONVERT_TO_STRING
		          ? charAt(S, position)
		          : first
		        : CONVERT_TO_STRING
		          ? stringSlice(S, position, position + 2)
		          : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
		  };
		};

		stringMultibyte$1 = {
		  // `String.prototype.codePointAt` method
		  // https://tc39.es/ecma262/#sec-string.prototype.codepointat
		  codeAt: createMethod(false),
		  // `String.prototype.at` method
		  // https://github.com/mathiasbynens/String.prototype.at
		  charAt: createMethod(true)
		};
		return stringMultibyte$1;
	}

	var hasRequiredEs_string_iterator$1;

	function requireEs_string_iterator$1 () {
		if (hasRequiredEs_string_iterator$1) return es_string_iterator$1;
		hasRequiredEs_string_iterator$1 = 1;
		var charAt = /*@__PURE__*/ requireStringMultibyte$1().charAt;
		var toString = /*@__PURE__*/ requireToString$1();
		var InternalStateModule = /*@__PURE__*/ requireInternalState$1();
		var defineIterator = /*@__PURE__*/ requireIteratorDefine$1();
		var createIterResultObject = /*@__PURE__*/ requireCreateIterResultObject$1();

		var STRING_ITERATOR = 'String Iterator';
		var setInternalState = InternalStateModule.set;
		var getInternalState = InternalStateModule.getterFor(STRING_ITERATOR);

		// `String.prototype[@@iterator]` method
		// https://tc39.es/ecma262/#sec-string.prototype-@@iterator
		defineIterator(String, 'String', function (iterated) {
		  setInternalState(this, {
		    type: STRING_ITERATOR,
		    string: toString(iterated),
		    index: 0
		  });
		// `%StringIteratorPrototype%.next` method
		// https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
		}, function next() {
		  var state = getInternalState(this);
		  var string = state.string;
		  var index = state.index;
		  var point;
		  if (index >= string.length) return createIterResultObject(undefined, true);
		  point = charAt(string, index);
		  state.index += point.length;
		  return createIterResultObject(point, false);
		});
		return es_string_iterator$1;
	}

	var promise$3;
	var hasRequiredPromise$2;

	function requirePromise$2 () {
		if (hasRequiredPromise$2) return promise$3;
		hasRequiredPromise$2 = 1;
		requireEs_aggregateError();
		requireEs_array_iterator$1();
		requireEs_promise();
		requireEs_promise_allSettled();
		requireEs_promise_any();
		requireEs_promise_try();
		requireEs_promise_withResolvers();
		requireEs_promise_finally();
		requireEs_string_iterator$1();
		var path = /*@__PURE__*/ requirePath$1();

		promise$3 = path.Promise;
		return promise$3;
	}

	var web_domCollections_iterator$1 = {};

	var domIterables$1;
	var hasRequiredDomIterables$1;

	function requireDomIterables$1 () {
		if (hasRequiredDomIterables$1) return domIterables$1;
		hasRequiredDomIterables$1 = 1;
		// iterable DOM collections
		// flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
		domIterables$1 = {
		  CSSRuleList: 0,
		  CSSStyleDeclaration: 0,
		  CSSValueList: 0,
		  ClientRectList: 0,
		  DOMRectList: 0,
		  DOMStringList: 0,
		  DOMTokenList: 1,
		  DataTransferItemList: 0,
		  FileList: 0,
		  HTMLAllCollection: 0,
		  HTMLCollection: 0,
		  HTMLFormElement: 0,
		  HTMLSelectElement: 0,
		  MediaList: 0,
		  MimeTypeArray: 0,
		  NamedNodeMap: 0,
		  NodeList: 1,
		  PaintRequestList: 0,
		  Plugin: 0,
		  PluginArray: 0,
		  SVGLengthList: 0,
		  SVGNumberList: 0,
		  SVGPathSegList: 0,
		  SVGPointList: 0,
		  SVGStringList: 0,
		  SVGTransformList: 0,
		  SourceBufferList: 0,
		  StyleSheetList: 0,
		  TextTrackCueList: 0,
		  TextTrackList: 0,
		  TouchList: 0
		};
		return domIterables$1;
	}

	var hasRequiredWeb_domCollections_iterator$1;

	function requireWeb_domCollections_iterator$1 () {
		if (hasRequiredWeb_domCollections_iterator$1) return web_domCollections_iterator$1;
		hasRequiredWeb_domCollections_iterator$1 = 1;
		requireEs_array_iterator$1();
		var DOMIterables = /*@__PURE__*/ requireDomIterables$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag$1();
		var Iterators = /*@__PURE__*/ requireIterators$1();

		for (var COLLECTION_NAME in DOMIterables) {
		  setToStringTag(globalThis[COLLECTION_NAME], COLLECTION_NAME);
		  Iterators[COLLECTION_NAME] = Iterators.Array;
		}
		return web_domCollections_iterator$1;
	}

	var promise$2;
	var hasRequiredPromise$1;

	function requirePromise$1 () {
		if (hasRequiredPromise$1) return promise$2;
		hasRequiredPromise$1 = 1;
		var parent = /*@__PURE__*/ requirePromise$2();
		requireWeb_domCollections_iterator$1();

		promise$2 = parent;
		return promise$2;
	}

	var promise$1;
	var hasRequiredPromise;

	function requirePromise () {
		if (hasRequiredPromise) return promise$1;
		hasRequiredPromise = 1;
		promise$1 = /*@__PURE__*/ requirePromise$1();
		return promise$1;
	}

	var promiseExports = requirePromise();
	var _Promise = /*@__PURE__*/getDefaultExportFromCjs(promiseExports);

	var es_parseFloat = {};

	var whitespaces$1;
	var hasRequiredWhitespaces$1;

	function requireWhitespaces$1 () {
		if (hasRequiredWhitespaces$1) return whitespaces$1;
		hasRequiredWhitespaces$1 = 1;
		// a string of all valid unicode whitespaces
		whitespaces$1 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
		  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';
		return whitespaces$1;
	}

	var stringTrim$1;
	var hasRequiredStringTrim$1;

	function requireStringTrim$1 () {
		if (hasRequiredStringTrim$1) return stringTrim$1;
		hasRequiredStringTrim$1 = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();
		var toString = /*@__PURE__*/ requireToString$1();
		var whitespaces = /*@__PURE__*/ requireWhitespaces$1();

		var replace = uncurryThis(''.replace);
		var ltrim = RegExp('^[' + whitespaces + ']+');
		var rtrim = RegExp('(^|[^' + whitespaces + '])[' + whitespaces + ']+$');

		// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
		var createMethod = function (TYPE) {
		  return function ($this) {
		    var string = toString(requireObjectCoercible($this));
		    if (TYPE & 1) string = replace(string, ltrim, '');
		    if (TYPE & 2) string = replace(string, rtrim, '$1');
		    return string;
		  };
		};

		stringTrim$1 = {
		  // `String.prototype.{ trimLeft, trimStart }` methods
		  // https://tc39.es/ecma262/#sec-string.prototype.trimstart
		  start: createMethod(1),
		  // `String.prototype.{ trimRight, trimEnd }` methods
		  // https://tc39.es/ecma262/#sec-string.prototype.trimend
		  end: createMethod(2),
		  // `String.prototype.trim` method
		  // https://tc39.es/ecma262/#sec-string.prototype.trim
		  trim: createMethod(3)
		};
		return stringTrim$1;
	}

	var numberParseFloat;
	var hasRequiredNumberParseFloat;

	function requireNumberParseFloat () {
		if (hasRequiredNumberParseFloat) return numberParseFloat;
		hasRequiredNumberParseFloat = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var fails = /*@__PURE__*/ requireFails$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var toString = /*@__PURE__*/ requireToString$1();
		var trim = /*@__PURE__*/ requireStringTrim$1().trim;
		var whitespaces = /*@__PURE__*/ requireWhitespaces$1();

		var charAt = uncurryThis(''.charAt);
		var $parseFloat = globalThis.parseFloat;
		var Symbol = globalThis.Symbol;
		var ITERATOR = Symbol && Symbol.iterator;
		var FORCED = 1 / $parseFloat(whitespaces + '-0') !== -Infinity
		  // MS Edge 18- broken with boxed symbols
		  || (ITERATOR && !fails(function () { $parseFloat(Object(ITERATOR)); }));

		// `parseFloat` method
		// https://tc39.es/ecma262/#sec-parsefloat-string
		numberParseFloat = FORCED ? function parseFloat(string) {
		  var trimmedString = trim(toString(string));
		  var result = $parseFloat(trimmedString);
		  return result === 0 && charAt(trimmedString, 0) === '-' ? -0 : result;
		} : $parseFloat;
		return numberParseFloat;
	}

	var hasRequiredEs_parseFloat;

	function requireEs_parseFloat () {
		if (hasRequiredEs_parseFloat) return es_parseFloat;
		hasRequiredEs_parseFloat = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $parseFloat = /*@__PURE__*/ requireNumberParseFloat();

		// `parseFloat` method
		// https://tc39.es/ecma262/#sec-parsefloat-string
		$({ global: true, forced: parseFloat !== $parseFloat }, {
		  parseFloat: $parseFloat
		});
		return es_parseFloat;
	}

	var _parseFloat$3;
	var hasRequired_parseFloat$2;

	function require_parseFloat$2 () {
		if (hasRequired_parseFloat$2) return _parseFloat$3;
		hasRequired_parseFloat$2 = 1;
		requireEs_parseFloat();
		var path = /*@__PURE__*/ requirePath$1();

		_parseFloat$3 = path.parseFloat;
		return _parseFloat$3;
	}

	var _parseFloat$2;
	var hasRequired_parseFloat$1;

	function require_parseFloat$1 () {
		if (hasRequired_parseFloat$1) return _parseFloat$2;
		hasRequired_parseFloat$1 = 1;
		var parent = /*@__PURE__*/ require_parseFloat$2();

		_parseFloat$2 = parent;
		return _parseFloat$2;
	}

	var _parseFloat$1;
	var hasRequired_parseFloat;

	function require_parseFloat () {
		if (hasRequired_parseFloat) return _parseFloat$1;
		hasRequired_parseFloat = 1;
		_parseFloat$1 = /*@__PURE__*/ require_parseFloat$1();
		return _parseFloat$1;
	}

	var _parseFloatExports = require_parseFloat();
	var _parseFloat = /*@__PURE__*/getDefaultExportFromCjs(_parseFloatExports);

	var es_parseInt$1 = {};

	var numberParseInt$1;
	var hasRequiredNumberParseInt$1;

	function requireNumberParseInt$1 () {
		if (hasRequiredNumberParseInt$1) return numberParseInt$1;
		hasRequiredNumberParseInt$1 = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var fails = /*@__PURE__*/ requireFails$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var toString = /*@__PURE__*/ requireToString$1();
		var trim = /*@__PURE__*/ requireStringTrim$1().trim;
		var whitespaces = /*@__PURE__*/ requireWhitespaces$1();

		var $parseInt = globalThis.parseInt;
		var Symbol = globalThis.Symbol;
		var ITERATOR = Symbol && Symbol.iterator;
		var hex = /^[+-]?0x/i;
		var exec = uncurryThis(hex.exec);
		var FORCED = $parseInt(whitespaces + '08') !== 8 || $parseInt(whitespaces + '0x16') !== 22
		  // MS Edge 18- broken with boxed symbols
		  || (ITERATOR && !fails(function () { $parseInt(Object(ITERATOR)); }));

		// `parseInt` method
		// https://tc39.es/ecma262/#sec-parseint-string-radix
		numberParseInt$1 = FORCED ? function parseInt(string, radix) {
		  var S = trim(toString(string));
		  return $parseInt(S, (radix >>> 0) || (exec(hex, S) ? 16 : 10));
		} : $parseInt;
		return numberParseInt$1;
	}

	var hasRequiredEs_parseInt$1;

	function requireEs_parseInt$1 () {
		if (hasRequiredEs_parseInt$1) return es_parseInt$1;
		hasRequiredEs_parseInt$1 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $parseInt = /*@__PURE__*/ requireNumberParseInt$1();

		// `parseInt` method
		// https://tc39.es/ecma262/#sec-parseint-string-radix
		$({ global: true, forced: parseInt !== $parseInt }, {
		  parseInt: $parseInt
		});
		return es_parseInt$1;
	}

	var _parseInt$7;
	var hasRequired_parseInt$5;

	function require_parseInt$5 () {
		if (hasRequired_parseInt$5) return _parseInt$7;
		hasRequired_parseInt$5 = 1;
		requireEs_parseInt$1();
		var path = /*@__PURE__*/ requirePath$1();

		_parseInt$7 = path.parseInt;
		return _parseInt$7;
	}

	var _parseInt$6;
	var hasRequired_parseInt$4;

	function require_parseInt$4 () {
		if (hasRequired_parseInt$4) return _parseInt$6;
		hasRequired_parseInt$4 = 1;
		var parent = /*@__PURE__*/ require_parseInt$5();

		_parseInt$6 = parent;
		return _parseInt$6;
	}

	var _parseInt$5;
	var hasRequired_parseInt$3;

	function require_parseInt$3 () {
		if (hasRequired_parseInt$3) return _parseInt$5;
		hasRequired_parseInt$3 = 1;
		_parseInt$5 = /*@__PURE__*/ require_parseInt$4();
		return _parseInt$5;
	}

	var _parseIntExports$1 = require_parseInt$3();
	var _parseInt$4 = /*@__PURE__*/getDefaultExportFromCjs(_parseIntExports$1);

	var es_array_slice$1 = {};

	var isArray$1;
	var hasRequiredIsArray$1;

	function requireIsArray$1 () {
		if (hasRequiredIsArray$1) return isArray$1;
		hasRequiredIsArray$1 = 1;
		var classof = /*@__PURE__*/ requireClassofRaw$1();

		// `IsArray` abstract operation
		// https://tc39.es/ecma262/#sec-isarray
		// eslint-disable-next-line es/no-array-isarray -- safe
		isArray$1 = Array.isArray || function isArray(argument) {
		  return classof(argument) === 'Array';
		};
		return isArray$1;
	}

	var createProperty$1;
	var hasRequiredCreateProperty$1;

	function requireCreateProperty$1 () {
		if (hasRequiredCreateProperty$1) return createProperty$1;
		hasRequiredCreateProperty$1 = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var definePropertyModule = /*@__PURE__*/ requireObjectDefineProperty$1();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor$1();

		createProperty$1 = function (object, key, value) {
		  if (DESCRIPTORS) definePropertyModule.f(object, key, createPropertyDescriptor(0, value));
		  else object[key] = value;
		};
		return createProperty$1;
	}

	var arrayMethodHasSpeciesSupport$1;
	var hasRequiredArrayMethodHasSpeciesSupport$1;

	function requireArrayMethodHasSpeciesSupport$1 () {
		if (hasRequiredArrayMethodHasSpeciesSupport$1) return arrayMethodHasSpeciesSupport$1;
		hasRequiredArrayMethodHasSpeciesSupport$1 = 1;
		var fails = /*@__PURE__*/ requireFails$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var V8_VERSION = /*@__PURE__*/ requireEnvironmentV8Version$1();

		var SPECIES = wellKnownSymbol('species');

		arrayMethodHasSpeciesSupport$1 = function (METHOD_NAME) {
		  // We can't use this feature detection in V8 since it causes
		  // deoptimization and serious performance degradation
		  // https://github.com/zloirock/core-js/issues/677
		  return V8_VERSION >= 51 || !fails(function () {
		    var array = [];
		    var constructor = array.constructor = {};
		    constructor[SPECIES] = function () {
		      return { foo: 1 };
		    };
		    return array[METHOD_NAME](Boolean).foo !== 1;
		  });
		};
		return arrayMethodHasSpeciesSupport$1;
	}

	var hasRequiredEs_array_slice$1;

	function requireEs_array_slice$1 () {
		if (hasRequiredEs_array_slice$1) return es_array_slice$1;
		hasRequiredEs_array_slice$1 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var isArray = /*@__PURE__*/ requireIsArray$1();
		var isConstructor = /*@__PURE__*/ requireIsConstructor$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var toAbsoluteIndex = /*@__PURE__*/ requireToAbsoluteIndex$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var createProperty = /*@__PURE__*/ requireCreateProperty$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var arrayMethodHasSpeciesSupport = /*@__PURE__*/ requireArrayMethodHasSpeciesSupport$1();
		var nativeSlice = /*@__PURE__*/ requireArraySlice$1();

		var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('slice');

		var SPECIES = wellKnownSymbol('species');
		var $Array = Array;
		var max = Math.max;

		// `Array.prototype.slice` method
		// https://tc39.es/ecma262/#sec-array.prototype.slice
		// fallback for not array-like ES3 strings and DOM objects
		$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
		  slice: function slice(start, end) {
		    var O = toIndexedObject(this);
		    var length = lengthOfArrayLike(O);
		    var k = toAbsoluteIndex(start, length);
		    var fin = toAbsoluteIndex(end === undefined ? length : end, length);
		    // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
		    var Constructor, result, n;
		    if (isArray(O)) {
		      Constructor = O.constructor;
		      // cross-realm fallback
		      if (isConstructor(Constructor) && (Constructor === $Array || isArray(Constructor.prototype))) {
		        Constructor = undefined;
		      } else if (isObject(Constructor)) {
		        Constructor = Constructor[SPECIES];
		        if (Constructor === null) Constructor = undefined;
		      }
		      if (Constructor === $Array || Constructor === undefined) {
		        return nativeSlice(O, k, fin);
		      }
		    }
		    result = new (Constructor === undefined ? $Array : Constructor)(max(fin - k, 0));
		    for (n = 0; k < fin; k++, n++) if (k in O) createProperty(result, n, O[k]);
		    result.length = n;
		    return result;
		  }
		});
		return es_array_slice$1;
	}

	var slice$7;
	var hasRequiredSlice$7;

	function requireSlice$7 () {
		if (hasRequiredSlice$7) return slice$7;
		hasRequiredSlice$7 = 1;
		requireEs_array_slice$1();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		slice$7 = getBuiltInPrototypeMethod('Array', 'slice');
		return slice$7;
	}

	var slice$6;
	var hasRequiredSlice$6;

	function requireSlice$6 () {
		if (hasRequiredSlice$6) return slice$6;
		hasRequiredSlice$6 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireSlice$7();

		var ArrayPrototype = Array.prototype;

		slice$6 = function (it) {
		  var own = it.slice;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.slice) ? method : own;
		};
		return slice$6;
	}

	var slice$5;
	var hasRequiredSlice$5;

	function requireSlice$5 () {
		if (hasRequiredSlice$5) return slice$5;
		hasRequiredSlice$5 = 1;
		var parent = /*@__PURE__*/ requireSlice$6();

		slice$5 = parent;
		return slice$5;
	}

	var slice$4;
	var hasRequiredSlice$4;

	function requireSlice$4 () {
		if (hasRequiredSlice$4) return slice$4;
		hasRequiredSlice$4 = 1;
		slice$4 = /*@__PURE__*/ requireSlice$5();
		return slice$4;
	}

	var sliceExports$1 = requireSlice$4();
	var _sliceInstanceProperty$1 = /*@__PURE__*/getDefaultExportFromCjs(sliceExports$1);

	var web_timers = {};

	var web_setInterval = {};

	var schedulersFix;
	var hasRequiredSchedulersFix;

	function requireSchedulersFix () {
		if (hasRequiredSchedulersFix) return schedulersFix;
		hasRequiredSchedulersFix = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var apply = /*@__PURE__*/ requireFunctionApply$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var ENVIRONMENT = /*@__PURE__*/ requireEnvironment();
		var USER_AGENT = /*@__PURE__*/ requireEnvironmentUserAgent$1();
		var arraySlice = /*@__PURE__*/ requireArraySlice$1();
		var validateArgumentsLength = /*@__PURE__*/ requireValidateArgumentsLength();

		var Function = globalThis.Function;
		// dirty IE9- and Bun 0.3.0- checks
		var WRAP = /MSIE .\./.test(USER_AGENT) || ENVIRONMENT === 'BUN' && (function () {
		  var version = globalThis.Bun.version.split('.');
		  return version.length < 3 || version[0] === '0' && (version[1] < 3 || version[1] === '3' && version[2] === '0');
		})();

		// IE9- / Bun 0.3.0- setTimeout / setInterval / setImmediate additional parameters fix
		// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#timers
		// https://github.com/oven-sh/bun/issues/1633
		schedulersFix = function (scheduler, hasTimeArg) {
		  var firstParamIndex = hasTimeArg ? 2 : 1;
		  return WRAP ? function (handler, timeout /* , ...arguments */) {
		    var boundArgs = validateArgumentsLength(arguments.length, 1) > firstParamIndex;
		    var fn = isCallable(handler) ? handler : Function(handler);
		    var params = boundArgs ? arraySlice(arguments, firstParamIndex) : [];
		    var callback = boundArgs ? function () {
		      apply(fn, this, params);
		    } : fn;
		    return hasTimeArg ? scheduler(callback, timeout) : scheduler(callback);
		  } : scheduler;
		};
		return schedulersFix;
	}

	var hasRequiredWeb_setInterval;

	function requireWeb_setInterval () {
		if (hasRequiredWeb_setInterval) return web_setInterval;
		hasRequiredWeb_setInterval = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var schedulersFix = /*@__PURE__*/ requireSchedulersFix();

		var setInterval = schedulersFix(globalThis.setInterval, true);

		// Bun / IE9- setInterval additional parameters fix
		// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-setinterval
		$({ global: true, bind: true, forced: globalThis.setInterval !== setInterval }, {
		  setInterval: setInterval
		});
		return web_setInterval;
	}

	var web_setTimeout = {};

	var hasRequiredWeb_setTimeout;

	function requireWeb_setTimeout () {
		if (hasRequiredWeb_setTimeout) return web_setTimeout;
		hasRequiredWeb_setTimeout = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var schedulersFix = /*@__PURE__*/ requireSchedulersFix();

		var setTimeout = schedulersFix(globalThis.setTimeout, true);

		// Bun / IE9- setTimeout additional parameters fix
		// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-settimeout
		$({ global: true, bind: true, forced: globalThis.setTimeout !== setTimeout }, {
		  setTimeout: setTimeout
		});
		return web_setTimeout;
	}

	var hasRequiredWeb_timers;

	function requireWeb_timers () {
		if (hasRequiredWeb_timers) return web_timers;
		hasRequiredWeb_timers = 1;
		// TODO: Remove this module from `core-js@4` since it's split to modules listed below
		requireWeb_setInterval();
		requireWeb_setTimeout();
		return web_timers;
	}

	var setTimeout$2;
	var hasRequiredSetTimeout$1;

	function requireSetTimeout$1 () {
		if (hasRequiredSetTimeout$1) return setTimeout$2;
		hasRequiredSetTimeout$1 = 1;
		requireWeb_timers();
		var path = /*@__PURE__*/ requirePath$1();

		setTimeout$2 = path.setTimeout;
		return setTimeout$2;
	}

	var setTimeout$1;
	var hasRequiredSetTimeout;

	function requireSetTimeout () {
		if (hasRequiredSetTimeout) return setTimeout$1;
		hasRequiredSetTimeout = 1;
		setTimeout$1 = /*@__PURE__*/ requireSetTimeout$1();
		return setTimeout$1;
	}

	var setTimeoutExports = requireSetTimeout();
	var _setTimeout = /*@__PURE__*/getDefaultExportFromCjs(setTimeoutExports);

	var opt = {};

	/**
	 * Start Ladda on given button.
	 */
	function laddaStart(elem) {
	  var ladda = Ladda.create(elem);
	  ladda.start();
	  return ladda;
	}

	/**
	 * Scroll to element if it is not visible.
	 *
	 * @param $elem
	 * @param formId
	 */
	function scrollTo($elem, formId) {
	  if (opt[formId].scroll) {
	    if ($elem.length) {
	      var elemTop = $elem.offset().top;
	      var scrollTop = $(window).scrollTop();
	      if (elemTop < $(window).scrollTop() || elemTop > scrollTop + window.innerHeight) {
	        $('html,body').animate({
	          scrollTop: elemTop - 50
	        }, 500);
	      }
	    }
	  } else {
	    opt[formId].scroll = true;
	  }
	}
	function requestCancellable() {
	  const request = {
	    xhr: null,
	    booklyAjax: () => {},
	    cancel: () => {}
	  };
	  request.booklyAjax = options => {
	    return new _Promise((resolve, reject) => {
	      request.cancel = () => {
	        if (request.xhr != null) {
	          request.xhr.abort();
	          request.xhr = null;
	        }
	      };
	      request.xhr = ajax(options, resolve, reject);
	    });
	  };
	  return request;
	}
	function booklyAjax(options) {
	  return new _Promise((resolve, reject) => {
	    ajax(options, resolve, reject);
	  });
	}
	function formatDate$1(date, format) {
	  return moment(date).locale('bookly-daterange').format(format || BooklyL10nGlobal.datePicker.format);
	}
	class Format {
	  #w;
	  constructor(w) {
	    this.#w = w;
	  }
	  price(amount) {
	    let result = this.#w.format_price.format;
	    amount = _parseFloat(amount);
	    result = result.replace('{sign}', amount < 0 ? '-' : '');
	    result = result.replace('{price}', this._formatNumber(Math.abs(amount), this.#w.format_price.decimals, this.#w.format_price.decimal_separator, this.#w.format_price.thousands_separator));
	    return result;
	  }
	  _formatNumber(n, c, d, t) {
	    var _context;
	    n = Math.abs(Number(n) || 0).toFixed(c);
	    c = isNaN(c = Math.abs(c)) ? 2 : c;
	    d = d === undefined ? '.' : d;
	    t = t === undefined ? ',' : t.replace(/&nbsp;/g, '\u00A0');
	    let s = n < 0 ? '-' : '',
	      i = String(_parseInt$4(n)),
	      j = i.length > 3 ? i.length % 3 : 0;
	    return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + _sliceInstanceProperty$1(_context = Math.abs(n - i).toFixed(c)).call(_context, 2) : '');
	  }
	}
	function ajax(options, resolve, reject) {
	  options.data.csrf_token = BooklyL10n.csrf_token;
	  return $.ajax(jQuery.extend({
	    url: BooklyL10n.ajaxurl,
	    dataType: 'json',
	    xhrFields: {
	      withCredentials: true
	    },
	    crossDomain: 'withCredentials' in new XMLHttpRequest(),
	    beforeSend(jqXHR, settings) {}
	  }, options)).always(response => {
	    if (processSessionSaveResponse(response)) {
	      if (response.success) {
	        resolve(response);
	      } else {
	        reject(response);
	      }
	    }
	  });
	}
	function processSessionSaveResponse(response) {
	  if (!response.success && response?.error === 'session_error') {
	    Ladda.stopAll();
	    _setTimeout(function () {
	      if (confirm(BooklyL10n.sessionHasExpired)) {
	        location.reload();
	      }
	    }, 100);
	    return false;
	  }
	  return true;
	}

	var es_string_padStart = {};

	var stringRepeat;
	var hasRequiredStringRepeat;

	function requireStringRepeat () {
		if (hasRequiredStringRepeat) return stringRepeat;
		hasRequiredStringRepeat = 1;
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity$1();
		var toString = /*@__PURE__*/ requireToString$1();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();

		var $RangeError = RangeError;

		// `String.prototype.repeat` method implementation
		// https://tc39.es/ecma262/#sec-string.prototype.repeat
		stringRepeat = function repeat(count) {
		  var str = toString(requireObjectCoercible(this));
		  var result = '';
		  var n = toIntegerOrInfinity(count);
		  if (n < 0 || n === Infinity) throw new $RangeError('Wrong number of repetitions');
		  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
		  return result;
		};
		return stringRepeat;
	}

	var stringPad;
	var hasRequiredStringPad;

	function requireStringPad () {
		if (hasRequiredStringPad) return stringPad;
		hasRequiredStringPad = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var toLength = /*@__PURE__*/ requireToLength$1();
		var toString = /*@__PURE__*/ requireToString$1();
		var $repeat = /*@__PURE__*/ requireStringRepeat();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();

		var repeat = uncurryThis($repeat);
		var stringSlice = uncurryThis(''.slice);
		var ceil = Math.ceil;

		// `String.prototype.{ padStart, padEnd }` methods implementation
		var createMethod = function (IS_END) {
		  return function ($this, maxLength, fillString) {
		    var S = toString(requireObjectCoercible($this));
		    var intMaxLength = toLength(maxLength);
		    var stringLength = S.length;
		    var fillStr = fillString === undefined ? ' ' : toString(fillString);
		    var fillLen, stringFiller;
		    if (intMaxLength <= stringLength || fillStr === '') return S;
		    fillLen = intMaxLength - stringLength;
		    stringFiller = repeat(fillStr, ceil(fillLen / fillStr.length));
		    if (stringFiller.length > fillLen) stringFiller = stringSlice(stringFiller, 0, fillLen);
		    return IS_END ? S + stringFiller : stringFiller + S;
		  };
		};

		stringPad = {
		  // `String.prototype.padStart` method
		  // https://tc39.es/ecma262/#sec-string.prototype.padstart
		  start: createMethod(false),
		  // `String.prototype.padEnd` method
		  // https://tc39.es/ecma262/#sec-string.prototype.padend
		  end: createMethod(true)
		};
		return stringPad;
	}

	var stringPadWebkitBug;
	var hasRequiredStringPadWebkitBug;

	function requireStringPadWebkitBug () {
		if (hasRequiredStringPadWebkitBug) return stringPadWebkitBug;
		hasRequiredStringPadWebkitBug = 1;
		// https://github.com/zloirock/core-js/issues/280
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		stringPadWebkitBug = /Version\/10(?:\.\d+){1,2}(?: [\w./]+)?(?: Mobile\/\w+)? Safari\//.test(userAgent);
		return stringPadWebkitBug;
	}

	var hasRequiredEs_string_padStart;

	function requireEs_string_padStart () {
		if (hasRequiredEs_string_padStart) return es_string_padStart;
		hasRequiredEs_string_padStart = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $padStart = /*@__PURE__*/ requireStringPad().start;
		var WEBKIT_BUG = /*@__PURE__*/ requireStringPadWebkitBug();

		// `String.prototype.padStart` method
		// https://tc39.es/ecma262/#sec-string.prototype.padstart
		$({ target: 'String', proto: true, forced: WEBKIT_BUG }, {
		  padStart: function padStart(maxLength /* , fillString = ' ' */) {
		    return $padStart(this, maxLength, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});
		return es_string_padStart;
	}

	var padStart$3;
	var hasRequiredPadStart$3;

	function requirePadStart$3 () {
		if (hasRequiredPadStart$3) return padStart$3;
		hasRequiredPadStart$3 = 1;
		requireEs_string_padStart();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		padStart$3 = getBuiltInPrototypeMethod('String', 'padStart');
		return padStart$3;
	}

	var padStart$2;
	var hasRequiredPadStart$2;

	function requirePadStart$2 () {
		if (hasRequiredPadStart$2) return padStart$2;
		hasRequiredPadStart$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requirePadStart$3();

		var StringPrototype = String.prototype;

		padStart$2 = function (it) {
		  var own = it.padStart;
		  return typeof it == 'string' || it === StringPrototype
		    || (isPrototypeOf(StringPrototype, it) && own === StringPrototype.padStart) ? method : own;
		};
		return padStart$2;
	}

	var padStart$1;
	var hasRequiredPadStart$1;

	function requirePadStart$1 () {
		if (hasRequiredPadStart$1) return padStart$1;
		hasRequiredPadStart$1 = 1;
		var parent = /*@__PURE__*/ requirePadStart$2();

		padStart$1 = parent;
		return padStart$1;
	}

	var padStart;
	var hasRequiredPadStart;

	function requirePadStart () {
		if (hasRequiredPadStart) return padStart;
		hasRequiredPadStart = 1;
		padStart = /*@__PURE__*/ requirePadStart$1();
		return padStart;
	}

	var padStartExports = requirePadStart();
	var _padStartInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(padStartExports);

	var es_array_find = {};

	var arraySpeciesConstructor$1;
	var hasRequiredArraySpeciesConstructor$1;

	function requireArraySpeciesConstructor$1 () {
		if (hasRequiredArraySpeciesConstructor$1) return arraySpeciesConstructor$1;
		hasRequiredArraySpeciesConstructor$1 = 1;
		var isArray = /*@__PURE__*/ requireIsArray$1();
		var isConstructor = /*@__PURE__*/ requireIsConstructor$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();

		var SPECIES = wellKnownSymbol('species');
		var $Array = Array;

		// a part of `ArraySpeciesCreate` abstract operation
		// https://tc39.es/ecma262/#sec-arrayspeciescreate
		arraySpeciesConstructor$1 = function (originalArray) {
		  var C;
		  if (isArray(originalArray)) {
		    C = originalArray.constructor;
		    // cross-realm fallback
		    if (isConstructor(C) && (C === $Array || isArray(C.prototype))) C = undefined;
		    else if (isObject(C)) {
		      C = C[SPECIES];
		      if (C === null) C = undefined;
		    }
		  } return C === undefined ? $Array : C;
		};
		return arraySpeciesConstructor$1;
	}

	var arraySpeciesCreate$1;
	var hasRequiredArraySpeciesCreate$1;

	function requireArraySpeciesCreate$1 () {
		if (hasRequiredArraySpeciesCreate$1) return arraySpeciesCreate$1;
		hasRequiredArraySpeciesCreate$1 = 1;
		var arraySpeciesConstructor = /*@__PURE__*/ requireArraySpeciesConstructor$1();

		// `ArraySpeciesCreate` abstract operation
		// https://tc39.es/ecma262/#sec-arrayspeciescreate
		arraySpeciesCreate$1 = function (originalArray, length) {
		  return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
		};
		return arraySpeciesCreate$1;
	}

	var arrayIteration$1;
	var hasRequiredArrayIteration$1;

	function requireArrayIteration$1 () {
		if (hasRequiredArrayIteration$1) return arrayIteration$1;
		hasRequiredArrayIteration$1 = 1;
		var bind = /*@__PURE__*/ requireFunctionBindContext$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var IndexedObject = /*@__PURE__*/ requireIndexedObject$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();
		var arraySpeciesCreate = /*@__PURE__*/ requireArraySpeciesCreate$1();

		var push = uncurryThis([].push);

		// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
		var createMethod = function (TYPE) {
		  var IS_MAP = TYPE === 1;
		  var IS_FILTER = TYPE === 2;
		  var IS_SOME = TYPE === 3;
		  var IS_EVERY = TYPE === 4;
		  var IS_FIND_INDEX = TYPE === 6;
		  var IS_FILTER_REJECT = TYPE === 7;
		  var NO_HOLES = TYPE === 5 || IS_FIND_INDEX;
		  return function ($this, callbackfn, that, specificCreate) {
		    var O = toObject($this);
		    var self = IndexedObject(O);
		    var length = lengthOfArrayLike(self);
		    var boundFunction = bind(callbackfn, that);
		    var index = 0;
		    var create = specificCreate || arraySpeciesCreate;
		    var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_REJECT ? create($this, 0) : undefined;
		    var value, result;
		    for (;length > index; index++) if (NO_HOLES || index in self) {
		      value = self[index];
		      result = boundFunction(value, index, O);
		      if (TYPE) {
		        if (IS_MAP) target[index] = result; // map
		        else if (result) switch (TYPE) {
		          case 3: return true;              // some
		          case 5: return value;             // find
		          case 6: return index;             // findIndex
		          case 2: push(target, value);      // filter
		        } else switch (TYPE) {
		          case 4: return false;             // every
		          case 7: push(target, value);      // filterReject
		        }
		      }
		    }
		    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
		  };
		};

		arrayIteration$1 = {
		  // `Array.prototype.forEach` method
		  // https://tc39.es/ecma262/#sec-array.prototype.foreach
		  forEach: createMethod(0),
		  // `Array.prototype.map` method
		  // https://tc39.es/ecma262/#sec-array.prototype.map
		  map: createMethod(1),
		  // `Array.prototype.filter` method
		  // https://tc39.es/ecma262/#sec-array.prototype.filter
		  filter: createMethod(2),
		  // `Array.prototype.some` method
		  // https://tc39.es/ecma262/#sec-array.prototype.some
		  some: createMethod(3),
		  // `Array.prototype.every` method
		  // https://tc39.es/ecma262/#sec-array.prototype.every
		  every: createMethod(4),
		  // `Array.prototype.find` method
		  // https://tc39.es/ecma262/#sec-array.prototype.find
		  find: createMethod(5),
		  // `Array.prototype.findIndex` method
		  // https://tc39.es/ecma262/#sec-array.prototype.findIndex
		  findIndex: createMethod(6),
		  // `Array.prototype.filterReject` method
		  // https://github.com/tc39/proposal-array-filtering
		  filterReject: createMethod(7)
		};
		return arrayIteration$1;
	}

	var hasRequiredEs_array_find;

	function requireEs_array_find () {
		if (hasRequiredEs_array_find) return es_array_find;
		hasRequiredEs_array_find = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $find = /*@__PURE__*/ requireArrayIteration$1().find;
		var addToUnscopables = /*@__PURE__*/ requireAddToUnscopables$1();

		var FIND = 'find';
		var SKIPS_HOLES = true;

		// Shouldn't skip holes
		// eslint-disable-next-line es/no-array-prototype-find -- testing
		if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

		// `Array.prototype.find` method
		// https://tc39.es/ecma262/#sec-array.prototype.find
		$({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
		  find: function find(callbackfn /* , that = undefined */) {
		    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});

		// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
		addToUnscopables(FIND);
		return es_array_find;
	}

	var find$3;
	var hasRequiredFind$3;

	function requireFind$3 () {
		if (hasRequiredFind$3) return find$3;
		hasRequiredFind$3 = 1;
		requireEs_array_find();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		find$3 = getBuiltInPrototypeMethod('Array', 'find');
		return find$3;
	}

	var find$2;
	var hasRequiredFind$2;

	function requireFind$2 () {
		if (hasRequiredFind$2) return find$2;
		hasRequiredFind$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireFind$3();

		var ArrayPrototype = Array.prototype;

		find$2 = function (it) {
		  var own = it.find;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.find) ? method : own;
		};
		return find$2;
	}

	var find$1;
	var hasRequiredFind$1;

	function requireFind$1 () {
		if (hasRequiredFind$1) return find$1;
		hasRequiredFind$1 = 1;
		var parent = /*@__PURE__*/ requireFind$2();

		find$1 = parent;
		return find$1;
	}

	var find;
	var hasRequiredFind;

	function requireFind () {
		if (hasRequiredFind) return find;
		hasRequiredFind = 1;
		find = /*@__PURE__*/ requireFind$1();
		return find;
	}

	var findExports = requireFind();
	var _findInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(findExports);

	var es_date_toJson = {};

	var dateToIsoString;
	var hasRequiredDateToIsoString;

	function requireDateToIsoString () {
		if (hasRequiredDateToIsoString) return dateToIsoString;
		hasRequiredDateToIsoString = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var padStart = /*@__PURE__*/ requireStringPad().start;

		var $RangeError = RangeError;
		var $isFinite = isFinite;
		var abs = Math.abs;
		var DatePrototype = Date.prototype;
		var nativeDateToISOString = DatePrototype.toISOString;
		var thisTimeValue = uncurryThis(DatePrototype.getTime);
		var getUTCDate = uncurryThis(DatePrototype.getUTCDate);
		var getUTCFullYear = uncurryThis(DatePrototype.getUTCFullYear);
		var getUTCHours = uncurryThis(DatePrototype.getUTCHours);
		var getUTCMilliseconds = uncurryThis(DatePrototype.getUTCMilliseconds);
		var getUTCMinutes = uncurryThis(DatePrototype.getUTCMinutes);
		var getUTCMonth = uncurryThis(DatePrototype.getUTCMonth);
		var getUTCSeconds = uncurryThis(DatePrototype.getUTCSeconds);

		// `Date.prototype.toISOString` method implementation
		// https://tc39.es/ecma262/#sec-date.prototype.toisostring
		// PhantomJS / old WebKit fails here:
		dateToIsoString = (fails(function () {
		  return nativeDateToISOString.call(new Date(-5e13 - 1)) !== '0385-07-25T07:06:39.999Z';
		}) || !fails(function () {
		  nativeDateToISOString.call(new Date(NaN));
		})) ? function toISOString() {
		  if (!$isFinite(thisTimeValue(this))) throw new $RangeError('Invalid time value');
		  var date = this;
		  var year = getUTCFullYear(date);
		  var milliseconds = getUTCMilliseconds(date);
		  var sign = year < 0 ? '-' : year > 9999 ? '+' : '';
		  return sign + padStart(abs(year), sign ? 6 : 4, 0) +
		    '-' + padStart(getUTCMonth(date) + 1, 2, 0) +
		    '-' + padStart(getUTCDate(date), 2, 0) +
		    'T' + padStart(getUTCHours(date), 2, 0) +
		    ':' + padStart(getUTCMinutes(date), 2, 0) +
		    ':' + padStart(getUTCSeconds(date), 2, 0) +
		    '.' + padStart(milliseconds, 3, 0) +
		    'Z';
		} : nativeDateToISOString;
		return dateToIsoString;
	}

	var hasRequiredEs_date_toJson;

	function requireEs_date_toJson () {
		if (hasRequiredEs_date_toJson) return es_date_toJson;
		hasRequiredEs_date_toJson = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var toPrimitive = /*@__PURE__*/ requireToPrimitive$1();
		var toISOString = /*@__PURE__*/ requireDateToIsoString();
		var classof = /*@__PURE__*/ requireClassofRaw$1();
		var fails = /*@__PURE__*/ requireFails$1();

		var FORCED = fails(function () {
		  return new Date(NaN).toJSON() !== null
		    || call(Date.prototype.toJSON, { toISOString: function () { return 1; } }) !== 1;
		});

		// `Date.prototype.toJSON` method
		// https://tc39.es/ecma262/#sec-date.prototype.tojson
		$({ target: 'Date', proto: true, forced: FORCED }, {
		  // eslint-disable-next-line no-unused-vars -- required for `.length`
		  toJSON: function toJSON(key) {
		    var O = toObject(this);
		    var pv = toPrimitive(O, 'number');
		    return typeof pv == 'number' && !isFinite(pv) ? null :
		      (!('toISOString' in O) && classof(O) === 'Date') ? call(toISOString, O) : O.toISOString();
		  }
		});
		return es_date_toJson;
	}

	var es_json_stringify = {};

	var getJsonReplacerFunction;
	var hasRequiredGetJsonReplacerFunction;

	function requireGetJsonReplacerFunction () {
		if (hasRequiredGetJsonReplacerFunction) return getJsonReplacerFunction;
		hasRequiredGetJsonReplacerFunction = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var isArray = /*@__PURE__*/ requireIsArray$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var classof = /*@__PURE__*/ requireClassofRaw$1();
		var toString = /*@__PURE__*/ requireToString$1();

		var push = uncurryThis([].push);

		getJsonReplacerFunction = function (replacer) {
		  if (isCallable(replacer)) return replacer;
		  if (!isArray(replacer)) return;
		  var rawLength = replacer.length;
		  var keys = [];
		  for (var i = 0; i < rawLength; i++) {
		    var element = replacer[i];
		    if (typeof element == 'string') push(keys, element);
		    else if (typeof element == 'number' || classof(element) === 'Number' || classof(element) === 'String') push(keys, toString(element));
		  }
		  var keysLength = keys.length;
		  var root = true;
		  return function (key, value) {
		    if (root) {
		      root = false;
		      return value;
		    }
		    if (isArray(this)) return value;
		    for (var j = 0; j < keysLength; j++) if (keys[j] === key) return value;
		  };
		};
		return getJsonReplacerFunction;
	}

	var hasRequiredEs_json_stringify;

	function requireEs_json_stringify () {
		if (hasRequiredEs_json_stringify) return es_json_stringify;
		hasRequiredEs_json_stringify = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var apply = /*@__PURE__*/ requireFunctionApply$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var isSymbol = /*@__PURE__*/ requireIsSymbol$1();
		var arraySlice = /*@__PURE__*/ requireArraySlice$1();
		var getReplacerFunction = /*@__PURE__*/ requireGetJsonReplacerFunction();
		var NATIVE_SYMBOL = /*@__PURE__*/ requireSymbolConstructorDetection$1();

		var $String = String;
		var $stringify = getBuiltIn('JSON', 'stringify');
		var exec = uncurryThis(/./.exec);
		var charAt = uncurryThis(''.charAt);
		var charCodeAt = uncurryThis(''.charCodeAt);
		var replace = uncurryThis(''.replace);
		var numberToString = uncurryThis(1.1.toString);

		var tester = /[\uD800-\uDFFF]/g;
		var low = /^[\uD800-\uDBFF]$/;
		var hi = /^[\uDC00-\uDFFF]$/;

		var WRONG_SYMBOLS_CONVERSION = !NATIVE_SYMBOL || fails(function () {
		  var symbol = getBuiltIn('Symbol')('stringify detection');
		  // MS Edge converts symbol values to JSON as {}
		  return $stringify([symbol]) !== '[null]'
		    // WebKit converts symbol values to JSON as null
		    || $stringify({ a: symbol }) !== '{}'
		    // V8 throws on boxed symbols
		    || $stringify(Object(symbol)) !== '{}';
		});

		// https://github.com/tc39/proposal-well-formed-stringify
		var ILL_FORMED_UNICODE = fails(function () {
		  return $stringify('\uDF06\uD834') !== '"\\udf06\\ud834"'
		    || $stringify('\uDEAD') !== '"\\udead"';
		});

		var stringifyWithSymbolsFix = function (it, replacer) {
		  var args = arraySlice(arguments);
		  var $replacer = getReplacerFunction(replacer);
		  if (!isCallable($replacer) && (it === undefined || isSymbol(it))) return; // IE8 returns string on undefined
		  args[1] = function (key, value) {
		    // some old implementations (like WebKit) could pass numbers as keys
		    if (isCallable($replacer)) value = call($replacer, this, $String(key), value);
		    if (!isSymbol(value)) return value;
		  };
		  return apply($stringify, null, args);
		};

		var fixIllFormed = function (match, offset, string) {
		  var prev = charAt(string, offset - 1);
		  var next = charAt(string, offset + 1);
		  if ((exec(low, match) && !exec(hi, next)) || (exec(hi, match) && !exec(low, prev))) {
		    return '\\u' + numberToString(charCodeAt(match, 0), 16);
		  } return match;
		};

		if ($stringify) {
		  // `JSON.stringify` method
		  // https://tc39.es/ecma262/#sec-json.stringify
		  $({ target: 'JSON', stat: true, arity: 3, forced: WRONG_SYMBOLS_CONVERSION || ILL_FORMED_UNICODE }, {
		    // eslint-disable-next-line no-unused-vars -- required for `.length`
		    stringify: function stringify(it, replacer, space) {
		      var args = arraySlice(arguments);
		      var result = apply(WRONG_SYMBOLS_CONVERSION ? stringifyWithSymbolsFix : $stringify, null, args);
		      return ILL_FORMED_UNICODE && typeof result == 'string' ? replace(result, tester, fixIllFormed) : result;
		    }
		  });
		}
		return es_json_stringify;
	}

	var stringify$2;
	var hasRequiredStringify$2;

	function requireStringify$2 () {
		if (hasRequiredStringify$2) return stringify$2;
		hasRequiredStringify$2 = 1;
		requireEs_date_toJson();
		requireEs_json_stringify();
		var path = /*@__PURE__*/ requirePath$1();
		var apply = /*@__PURE__*/ requireFunctionApply$1();

		// eslint-disable-next-line es/no-json -- safe
		if (!path.JSON) path.JSON = { stringify: JSON.stringify };

		// eslint-disable-next-line no-unused-vars -- required for `.length`
		stringify$2 = function stringify(it, replacer, space) {
		  return apply(path.JSON.stringify, null, arguments);
		};
		return stringify$2;
	}

	var stringify$1;
	var hasRequiredStringify$1;

	function requireStringify$1 () {
		if (hasRequiredStringify$1) return stringify$1;
		hasRequiredStringify$1 = 1;
		var parent = /*@__PURE__*/ requireStringify$2();

		stringify$1 = parent;
		return stringify$1;
	}

	var stringify;
	var hasRequiredStringify;

	function requireStringify () {
		if (hasRequiredStringify) return stringify;
		hasRequiredStringify = 1;
		stringify = /*@__PURE__*/ requireStringify$1();
		return stringify;
	}

	var stringifyExports = requireStringify();
	var _JSON$stringify = /*@__PURE__*/getDefaultExportFromCjs(stringifyExports);

	var es_string_repeat = {};

	var hasRequiredEs_string_repeat;

	function requireEs_string_repeat () {
		if (hasRequiredEs_string_repeat) return es_string_repeat;
		hasRequiredEs_string_repeat = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var repeat = /*@__PURE__*/ requireStringRepeat();

		// `String.prototype.repeat` method
		// https://tc39.es/ecma262/#sec-string.prototype.repeat
		$({ target: 'String', proto: true }, {
		  repeat: repeat
		});
		return es_string_repeat;
	}

	var repeat$3;
	var hasRequiredRepeat$3;

	function requireRepeat$3 () {
		if (hasRequiredRepeat$3) return repeat$3;
		hasRequiredRepeat$3 = 1;
		requireEs_string_repeat();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		repeat$3 = getBuiltInPrototypeMethod('String', 'repeat');
		return repeat$3;
	}

	var repeat$2;
	var hasRequiredRepeat$2;

	function requireRepeat$2 () {
		if (hasRequiredRepeat$2) return repeat$2;
		hasRequiredRepeat$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireRepeat$3();

		var StringPrototype = String.prototype;

		repeat$2 = function (it) {
		  var own = it.repeat;
		  return typeof it == 'string' || it === StringPrototype
		    || (isPrototypeOf(StringPrototype, it) && own === StringPrototype.repeat) ? method : own;
		};
		return repeat$2;
	}

	var repeat$1;
	var hasRequiredRepeat$1;

	function requireRepeat$1 () {
		if (hasRequiredRepeat$1) return repeat$1;
		hasRequiredRepeat$1 = 1;
		var parent = /*@__PURE__*/ requireRepeat$2();

		repeat$1 = parent;
		return repeat$1;
	}

	var repeat;
	var hasRequiredRepeat;

	function requireRepeat () {
		if (hasRequiredRepeat) return repeat;
		hasRequiredRepeat = 1;
		repeat = /*@__PURE__*/ requireRepeat$1();
		return repeat;
	}

	var repeatExports = requireRepeat();
	var _repeatInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(repeatExports);

	var es_array_forEach = {};

	var arrayMethodIsStrict;
	var hasRequiredArrayMethodIsStrict;

	function requireArrayMethodIsStrict () {
		if (hasRequiredArrayMethodIsStrict) return arrayMethodIsStrict;
		hasRequiredArrayMethodIsStrict = 1;
		var fails = /*@__PURE__*/ requireFails$1();

		arrayMethodIsStrict = function (METHOD_NAME, argument) {
		  var method = [][METHOD_NAME];
		  return !!method && fails(function () {
		    // eslint-disable-next-line no-useless-call -- required for testing
		    method.call(null, argument || function () { return 1; }, 1);
		  });
		};
		return arrayMethodIsStrict;
	}

	var arrayForEach;
	var hasRequiredArrayForEach;

	function requireArrayForEach () {
		if (hasRequiredArrayForEach) return arrayForEach;
		hasRequiredArrayForEach = 1;
		var $forEach = /*@__PURE__*/ requireArrayIteration$1().forEach;
		var arrayMethodIsStrict = /*@__PURE__*/ requireArrayMethodIsStrict();

		var STRICT_METHOD = arrayMethodIsStrict('forEach');

		// `Array.prototype.forEach` method implementation
		// https://tc39.es/ecma262/#sec-array.prototype.foreach
		arrayForEach = !STRICT_METHOD ? function forEach(callbackfn /* , thisArg */) {
		  return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		// eslint-disable-next-line es/no-array-prototype-foreach -- safe
		} : [].forEach;
		return arrayForEach;
	}

	var hasRequiredEs_array_forEach;

	function requireEs_array_forEach () {
		if (hasRequiredEs_array_forEach) return es_array_forEach;
		hasRequiredEs_array_forEach = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var forEach = /*@__PURE__*/ requireArrayForEach();

		// `Array.prototype.forEach` method
		// https://tc39.es/ecma262/#sec-array.prototype.foreach
		// eslint-disable-next-line es/no-array-prototype-foreach -- safe
		$({ target: 'Array', proto: true, forced: [].forEach !== forEach }, {
		  forEach: forEach
		});
		return es_array_forEach;
	}

	var forEach$3;
	var hasRequiredForEach$3;

	function requireForEach$3 () {
		if (hasRequiredForEach$3) return forEach$3;
		hasRequiredForEach$3 = 1;
		requireEs_array_forEach();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		forEach$3 = getBuiltInPrototypeMethod('Array', 'forEach');
		return forEach$3;
	}

	var forEach$2;
	var hasRequiredForEach$2;

	function requireForEach$2 () {
		if (hasRequiredForEach$2) return forEach$2;
		hasRequiredForEach$2 = 1;
		var parent = /*@__PURE__*/ requireForEach$3();

		forEach$2 = parent;
		return forEach$2;
	}

	var forEach$1;
	var hasRequiredForEach$1;

	function requireForEach$1 () {
		if (hasRequiredForEach$1) return forEach$1;
		hasRequiredForEach$1 = 1;
		var classof = /*@__PURE__*/ requireClassof$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireForEach$2();

		var ArrayPrototype = Array.prototype;

		var DOMIterables = {
		  DOMTokenList: true,
		  NodeList: true
		};

		forEach$1 = function (it) {
		  var own = it.forEach;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.forEach)
		    || hasOwn(DOMIterables, classof(it)) ? method : own;
		};
		return forEach$1;
	}

	var forEach;
	var hasRequiredForEach;

	function requireForEach () {
		if (hasRequiredForEach) return forEach;
		hasRequiredForEach = 1;
		forEach = /*@__PURE__*/ requireForEach$1();
		return forEach;
	}

	var forEachExports = requireForEach();
	var _forEachInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(forEachExports);

	var es_array_splice = {};

	var arraySetLength;
	var hasRequiredArraySetLength;

	function requireArraySetLength () {
		if (hasRequiredArraySetLength) return arraySetLength;
		hasRequiredArraySetLength = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var isArray = /*@__PURE__*/ requireIsArray$1();

		var $TypeError = TypeError;
		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

		// Safari < 13 does not throw an error in this case
		var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS && !function () {
		  // makes no sense without proper strict mode support
		  if (this !== undefined) return true;
		  try {
		    // eslint-disable-next-line es/no-object-defineproperty -- safe
		    Object.defineProperty([], 'length', { writable: false }).length = 1;
		  } catch (error) {
		    return error instanceof TypeError;
		  }
		}();

		arraySetLength = SILENT_ON_NON_WRITABLE_LENGTH_SET ? function (O, length) {
		  if (isArray(O) && !getOwnPropertyDescriptor(O, 'length').writable) {
		    throw new $TypeError('Cannot set read only .length');
		  } return O.length = length;
		} : function (O, length) {
		  return O.length = length;
		};
		return arraySetLength;
	}

	var doesNotExceedSafeInteger;
	var hasRequiredDoesNotExceedSafeInteger;

	function requireDoesNotExceedSafeInteger () {
		if (hasRequiredDoesNotExceedSafeInteger) return doesNotExceedSafeInteger;
		hasRequiredDoesNotExceedSafeInteger = 1;
		var $TypeError = TypeError;
		var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; // 2 ** 53 - 1 == 9007199254740991

		doesNotExceedSafeInteger = function (it) {
		  if (it > MAX_SAFE_INTEGER) throw $TypeError('Maximum allowed index exceeded');
		  return it;
		};
		return doesNotExceedSafeInteger;
	}

	var deletePropertyOrThrow;
	var hasRequiredDeletePropertyOrThrow;

	function requireDeletePropertyOrThrow () {
		if (hasRequiredDeletePropertyOrThrow) return deletePropertyOrThrow;
		hasRequiredDeletePropertyOrThrow = 1;
		var tryToString = /*@__PURE__*/ requireTryToString$1();

		var $TypeError = TypeError;

		deletePropertyOrThrow = function (O, P) {
		  if (!delete O[P]) throw new $TypeError('Cannot delete property ' + tryToString(P) + ' of ' + tryToString(O));
		};
		return deletePropertyOrThrow;
	}

	var hasRequiredEs_array_splice;

	function requireEs_array_splice () {
		if (hasRequiredEs_array_splice) return es_array_splice;
		hasRequiredEs_array_splice = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var toAbsoluteIndex = /*@__PURE__*/ requireToAbsoluteIndex$1();
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();
		var setArrayLength = /*@__PURE__*/ requireArraySetLength();
		var doesNotExceedSafeInteger = /*@__PURE__*/ requireDoesNotExceedSafeInteger();
		var arraySpeciesCreate = /*@__PURE__*/ requireArraySpeciesCreate$1();
		var createProperty = /*@__PURE__*/ requireCreateProperty$1();
		var deletePropertyOrThrow = /*@__PURE__*/ requireDeletePropertyOrThrow();
		var arrayMethodHasSpeciesSupport = /*@__PURE__*/ requireArrayMethodHasSpeciesSupport$1();

		var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('splice');

		var max = Math.max;
		var min = Math.min;

		// `Array.prototype.splice` method
		// https://tc39.es/ecma262/#sec-array.prototype.splice
		// with adding support of @@species
		$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
		  splice: function splice(start, deleteCount /* , ...items */) {
		    var O = toObject(this);
		    var len = lengthOfArrayLike(O);
		    var actualStart = toAbsoluteIndex(start, len);
		    var argumentsLength = arguments.length;
		    var insertCount, actualDeleteCount, A, k, from, to;
		    if (argumentsLength === 0) {
		      insertCount = actualDeleteCount = 0;
		    } else if (argumentsLength === 1) {
		      insertCount = 0;
		      actualDeleteCount = len - actualStart;
		    } else {
		      insertCount = argumentsLength - 2;
		      actualDeleteCount = min(max(toIntegerOrInfinity(deleteCount), 0), len - actualStart);
		    }
		    doesNotExceedSafeInteger(len + insertCount - actualDeleteCount);
		    A = arraySpeciesCreate(O, actualDeleteCount);
		    for (k = 0; k < actualDeleteCount; k++) {
		      from = actualStart + k;
		      if (from in O) createProperty(A, k, O[from]);
		    }
		    A.length = actualDeleteCount;
		    if (insertCount < actualDeleteCount) {
		      for (k = actualStart; k < len - actualDeleteCount; k++) {
		        from = k + actualDeleteCount;
		        to = k + insertCount;
		        if (from in O) O[to] = O[from];
		        else deletePropertyOrThrow(O, to);
		      }
		      for (k = len; k > len - actualDeleteCount + insertCount; k--) deletePropertyOrThrow(O, k - 1);
		    } else if (insertCount > actualDeleteCount) {
		      for (k = len - actualDeleteCount; k > actualStart; k--) {
		        from = k + actualDeleteCount - 1;
		        to = k + insertCount - 1;
		        if (from in O) O[to] = O[from];
		        else deletePropertyOrThrow(O, to);
		      }
		    }
		    for (k = 0; k < insertCount; k++) {
		      O[k + actualStart] = arguments[k + 2];
		    }
		    setArrayLength(O, len - actualDeleteCount + insertCount);
		    return A;
		  }
		});
		return es_array_splice;
	}

	var splice$3;
	var hasRequiredSplice$3;

	function requireSplice$3 () {
		if (hasRequiredSplice$3) return splice$3;
		hasRequiredSplice$3 = 1;
		requireEs_array_splice();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		splice$3 = getBuiltInPrototypeMethod('Array', 'splice');
		return splice$3;
	}

	var splice$2;
	var hasRequiredSplice$2;

	function requireSplice$2 () {
		if (hasRequiredSplice$2) return splice$2;
		hasRequiredSplice$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireSplice$3();

		var ArrayPrototype = Array.prototype;

		splice$2 = function (it) {
		  var own = it.splice;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.splice) ? method : own;
		};
		return splice$2;
	}

	var splice$1;
	var hasRequiredSplice$1;

	function requireSplice$1 () {
		if (hasRequiredSplice$1) return splice$1;
		hasRequiredSplice$1 = 1;
		var parent = /*@__PURE__*/ requireSplice$2();

		splice$1 = parent;
		return splice$1;
	}

	var splice;
	var hasRequiredSplice;

	function requireSplice () {
		if (hasRequiredSplice) return splice;
		hasRequiredSplice = 1;
		splice = /*@__PURE__*/ requireSplice$1();
		return splice;
	}

	var spliceExports = requireSplice();
	var _spliceInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(spliceExports);

	var es_array_every = {};

	var hasRequiredEs_array_every;

	function requireEs_array_every () {
		if (hasRequiredEs_array_every) return es_array_every;
		hasRequiredEs_array_every = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $every = /*@__PURE__*/ requireArrayIteration$1().every;
		var arrayMethodIsStrict = /*@__PURE__*/ requireArrayMethodIsStrict();

		var STRICT_METHOD = arrayMethodIsStrict('every');

		// `Array.prototype.every` method
		// https://tc39.es/ecma262/#sec-array.prototype.every
		$({ target: 'Array', proto: true, forced: !STRICT_METHOD }, {
		  every: function every(callbackfn /* , thisArg */) {
		    return $every(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});
		return es_array_every;
	}

	var every$3;
	var hasRequiredEvery$3;

	function requireEvery$3 () {
		if (hasRequiredEvery$3) return every$3;
		hasRequiredEvery$3 = 1;
		requireEs_array_every();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		every$3 = getBuiltInPrototypeMethod('Array', 'every');
		return every$3;
	}

	var every$2;
	var hasRequiredEvery$2;

	function requireEvery$2 () {
		if (hasRequiredEvery$2) return every$2;
		hasRequiredEvery$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireEvery$3();

		var ArrayPrototype = Array.prototype;

		every$2 = function (it) {
		  var own = it.every;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.every) ? method : own;
		};
		return every$2;
	}

	var every$1;
	var hasRequiredEvery$1;

	function requireEvery$1 () {
		if (hasRequiredEvery$1) return every$1;
		hasRequiredEvery$1 = 1;
		var parent = /*@__PURE__*/ requireEvery$2();

		every$1 = parent;
		return every$1;
	}

	var every;
	var hasRequiredEvery;

	function requireEvery () {
		if (hasRequiredEvery) return every;
		hasRequiredEvery = 1;
		every = /*@__PURE__*/ requireEvery$1();
		return every;
	}

	var everyExports = requireEvery();
	var _everyInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(everyExports);

	var es_array_concat = {};

	var hasRequiredEs_array_concat;

	function requireEs_array_concat () {
		if (hasRequiredEs_array_concat) return es_array_concat;
		hasRequiredEs_array_concat = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var isArray = /*@__PURE__*/ requireIsArray$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();
		var doesNotExceedSafeInteger = /*@__PURE__*/ requireDoesNotExceedSafeInteger();
		var createProperty = /*@__PURE__*/ requireCreateProperty$1();
		var arraySpeciesCreate = /*@__PURE__*/ requireArraySpeciesCreate$1();
		var arrayMethodHasSpeciesSupport = /*@__PURE__*/ requireArrayMethodHasSpeciesSupport$1();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol$1();
		var V8_VERSION = /*@__PURE__*/ requireEnvironmentV8Version$1();

		var IS_CONCAT_SPREADABLE = wellKnownSymbol('isConcatSpreadable');

		// We can't use this feature detection in V8 since it causes
		// deoptimization and serious performance degradation
		// https://github.com/zloirock/core-js/issues/679
		var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION >= 51 || !fails(function () {
		  var array = [];
		  array[IS_CONCAT_SPREADABLE] = false;
		  return array.concat()[0] !== array;
		});

		var isConcatSpreadable = function (O) {
		  if (!isObject(O)) return false;
		  var spreadable = O[IS_CONCAT_SPREADABLE];
		  return spreadable !== undefined ? !!spreadable : isArray(O);
		};

		var FORCED = !IS_CONCAT_SPREADABLE_SUPPORT || !arrayMethodHasSpeciesSupport('concat');

		// `Array.prototype.concat` method
		// https://tc39.es/ecma262/#sec-array.prototype.concat
		// with adding support of @@isConcatSpreadable and @@species
		$({ target: 'Array', proto: true, arity: 1, forced: FORCED }, {
		  // eslint-disable-next-line no-unused-vars -- required for `.length`
		  concat: function concat(arg) {
		    var O = toObject(this);
		    var A = arraySpeciesCreate(O, 0);
		    var n = 0;
		    var i, k, length, len, E;
		    for (i = -1, length = arguments.length; i < length; i++) {
		      E = i === -1 ? O : arguments[i];
		      if (isConcatSpreadable(E)) {
		        len = lengthOfArrayLike(E);
		        doesNotExceedSafeInteger(n + len);
		        for (k = 0; k < len; k++, n++) if (k in E) createProperty(A, n, E[k]);
		      } else {
		        doesNotExceedSafeInteger(n + 1);
		        createProperty(A, n++, E);
		      }
		    }
		    A.length = n;
		    return A;
		  }
		});
		return es_array_concat;
	}

	var concat$3;
	var hasRequiredConcat$3;

	function requireConcat$3 () {
		if (hasRequiredConcat$3) return concat$3;
		hasRequiredConcat$3 = 1;
		requireEs_array_concat();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		concat$3 = getBuiltInPrototypeMethod('Array', 'concat');
		return concat$3;
	}

	var concat$2;
	var hasRequiredConcat$2;

	function requireConcat$2 () {
		if (hasRequiredConcat$2) return concat$2;
		hasRequiredConcat$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireConcat$3();

		var ArrayPrototype = Array.prototype;

		concat$2 = function (it) {
		  var own = it.concat;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.concat) ? method : own;
		};
		return concat$2;
	}

	var concat$1;
	var hasRequiredConcat$1;

	function requireConcat$1 () {
		if (hasRequiredConcat$1) return concat$1;
		hasRequiredConcat$1 = 1;
		var parent = /*@__PURE__*/ requireConcat$2();

		concat$1 = parent;
		return concat$1;
	}

	var concat;
	var hasRequiredConcat;

	function requireConcat () {
		if (hasRequiredConcat) return concat;
		hasRequiredConcat = 1;
		concat = /*@__PURE__*/ requireConcat$1();
		return concat;
	}

	var concatExports = requireConcat();
	var _concatInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(concatExports);

	var es_array_map = {};

	var hasRequiredEs_array_map;

	function requireEs_array_map () {
		if (hasRequiredEs_array_map) return es_array_map;
		hasRequiredEs_array_map = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $map = /*@__PURE__*/ requireArrayIteration$1().map;
		var arrayMethodHasSpeciesSupport = /*@__PURE__*/ requireArrayMethodHasSpeciesSupport$1();

		var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('map');

		// `Array.prototype.map` method
		// https://tc39.es/ecma262/#sec-array.prototype.map
		// with adding support of @@species
		$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
		  map: function map(callbackfn /* , thisArg */) {
		    return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});
		return es_array_map;
	}

	var map$6;
	var hasRequiredMap$6;

	function requireMap$6 () {
		if (hasRequiredMap$6) return map$6;
		hasRequiredMap$6 = 1;
		requireEs_array_map();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		map$6 = getBuiltInPrototypeMethod('Array', 'map');
		return map$6;
	}

	var map$5;
	var hasRequiredMap$5;

	function requireMap$5 () {
		if (hasRequiredMap$5) return map$5;
		hasRequiredMap$5 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireMap$6();

		var ArrayPrototype = Array.prototype;

		map$5 = function (it) {
		  var own = it.map;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.map) ? method : own;
		};
		return map$5;
	}

	var map$4;
	var hasRequiredMap$4;

	function requireMap$4 () {
		if (hasRequiredMap$4) return map$4;
		hasRequiredMap$4 = 1;
		var parent = /*@__PURE__*/ requireMap$5();

		map$4 = parent;
		return map$4;
	}

	var map$3;
	var hasRequiredMap$3;

	function requireMap$3 () {
		if (hasRequiredMap$3) return map$3;
		hasRequiredMap$3 = 1;
		map$3 = /*@__PURE__*/ requireMap$4();
		return map$3;
	}

	var mapExports$1 = requireMap$3();
	var _mapInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(mapExports$1);

	var es_array_filter$1 = {};

	var hasRequiredEs_array_filter$1;

	function requireEs_array_filter$1 () {
		if (hasRequiredEs_array_filter$1) return es_array_filter$1;
		hasRequiredEs_array_filter$1 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $filter = /*@__PURE__*/ requireArrayIteration$1().filter;
		var arrayMethodHasSpeciesSupport = /*@__PURE__*/ requireArrayMethodHasSpeciesSupport$1();

		var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter');

		// `Array.prototype.filter` method
		// https://tc39.es/ecma262/#sec-array.prototype.filter
		// with adding support of @@species
		$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
		  filter: function filter(callbackfn /* , thisArg */) {
		    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});
		return es_array_filter$1;
	}

	var filter$7;
	var hasRequiredFilter$7;

	function requireFilter$7 () {
		if (hasRequiredFilter$7) return filter$7;
		hasRequiredFilter$7 = 1;
		requireEs_array_filter$1();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		filter$7 = getBuiltInPrototypeMethod('Array', 'filter');
		return filter$7;
	}

	var filter$6;
	var hasRequiredFilter$6;

	function requireFilter$6 () {
		if (hasRequiredFilter$6) return filter$6;
		hasRequiredFilter$6 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireFilter$7();

		var ArrayPrototype = Array.prototype;

		filter$6 = function (it) {
		  var own = it.filter;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.filter) ? method : own;
		};
		return filter$6;
	}

	var filter$5;
	var hasRequiredFilter$5;

	function requireFilter$5 () {
		if (hasRequiredFilter$5) return filter$5;
		hasRequiredFilter$5 = 1;
		var parent = /*@__PURE__*/ requireFilter$6();

		filter$5 = parent;
		return filter$5;
	}

	var filter$4;
	var hasRequiredFilter$4;

	function requireFilter$4 () {
		if (hasRequiredFilter$4) return filter$4;
		hasRequiredFilter$4 = 1;
		filter$4 = /*@__PURE__*/ requireFilter$5();
		return filter$4;
	}

	var filterExports$1 = requireFilter$4();
	var _filterInstanceProperty$1 = /*@__PURE__*/getDefaultExportFromCjs(filterExports$1);

	var es_object_keys = {};

	var hasRequiredEs_object_keys;

	function requireEs_object_keys () {
		if (hasRequiredEs_object_keys) return es_object_keys;
		hasRequiredEs_object_keys = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var nativeKeys = /*@__PURE__*/ requireObjectKeys$1();
		var fails = /*@__PURE__*/ requireFails$1();

		var FAILS_ON_PRIMITIVES = fails(function () { nativeKeys(1); });

		// `Object.keys` method
		// https://tc39.es/ecma262/#sec-object.keys
		$({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES }, {
		  keys: function keys(it) {
		    return nativeKeys(toObject(it));
		  }
		});
		return es_object_keys;
	}

	var keys$2;
	var hasRequiredKeys$2;

	function requireKeys$2 () {
		if (hasRequiredKeys$2) return keys$2;
		hasRequiredKeys$2 = 1;
		requireEs_object_keys();
		var path = /*@__PURE__*/ requirePath$1();

		keys$2 = path.Object.keys;
		return keys$2;
	}

	var keys$1;
	var hasRequiredKeys$1;

	function requireKeys$1 () {
		if (hasRequiredKeys$1) return keys$1;
		hasRequiredKeys$1 = 1;
		var parent = /*@__PURE__*/ requireKeys$2();

		keys$1 = parent;
		return keys$1;
	}

	var keys;
	var hasRequiredKeys;

	function requireKeys () {
		if (hasRequiredKeys) return keys;
		hasRequiredKeys = 1;
		keys = /*@__PURE__*/ requireKeys$1();
		return keys;
	}

	var keysExports = requireKeys();
	var _Object$keys = /*@__PURE__*/getDefaultExportFromCjs(keysExports);

	var es_string_trim = {};

	var stringTrimForced;
	var hasRequiredStringTrimForced;

	function requireStringTrimForced () {
		if (hasRequiredStringTrimForced) return stringTrimForced;
		hasRequiredStringTrimForced = 1;
		var PROPER_FUNCTION_NAME = /*@__PURE__*/ requireFunctionName$1().PROPER;
		var fails = /*@__PURE__*/ requireFails$1();
		var whitespaces = /*@__PURE__*/ requireWhitespaces$1();

		var non = '\u200B\u0085\u180E';

		// check that a method works with the correct list
		// of whitespaces and has a correct name
		stringTrimForced = function (METHOD_NAME) {
		  return fails(function () {
		    return !!whitespaces[METHOD_NAME]()
		      || non[METHOD_NAME]() !== non
		      || (PROPER_FUNCTION_NAME && whitespaces[METHOD_NAME].name !== METHOD_NAME);
		  });
		};
		return stringTrimForced;
	}

	var hasRequiredEs_string_trim;

	function requireEs_string_trim () {
		if (hasRequiredEs_string_trim) return es_string_trim;
		hasRequiredEs_string_trim = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $trim = /*@__PURE__*/ requireStringTrim$1().trim;
		var forcedStringTrimMethod = /*@__PURE__*/ requireStringTrimForced();

		// `String.prototype.trim` method
		// https://tc39.es/ecma262/#sec-string.prototype.trim
		$({ target: 'String', proto: true, forced: forcedStringTrimMethod('trim') }, {
		  trim: function trim() {
		    return $trim(this);
		  }
		});
		return es_string_trim;
	}

	var trim$3;
	var hasRequiredTrim$3;

	function requireTrim$3 () {
		if (hasRequiredTrim$3) return trim$3;
		hasRequiredTrim$3 = 1;
		requireEs_string_trim();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		trim$3 = getBuiltInPrototypeMethod('String', 'trim');
		return trim$3;
	}

	var trim$2;
	var hasRequiredTrim$2;

	function requireTrim$2 () {
		if (hasRequiredTrim$2) return trim$2;
		hasRequiredTrim$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireTrim$3();

		var StringPrototype = String.prototype;

		trim$2 = function (it) {
		  var own = it.trim;
		  return typeof it == 'string' || it === StringPrototype
		    || (isPrototypeOf(StringPrototype, it) && own === StringPrototype.trim) ? method : own;
		};
		return trim$2;
	}

	var trim$1;
	var hasRequiredTrim$1;

	function requireTrim$1 () {
		if (hasRequiredTrim$1) return trim$1;
		hasRequiredTrim$1 = 1;
		var parent = /*@__PURE__*/ requireTrim$2();

		trim$1 = parent;
		return trim$1;
	}

	var trim;
	var hasRequiredTrim;

	function requireTrim () {
		if (hasRequiredTrim) return trim;
		hasRequiredTrim = 1;
		trim = /*@__PURE__*/ requireTrim$1();
		return trim;
	}

	var trimExports = requireTrim();
	var _trimInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(trimExports);

	var es_array_indexOf = {};

	var hasRequiredEs_array_indexOf;

	function requireEs_array_indexOf () {
		if (hasRequiredEs_array_indexOf) return es_array_indexOf;
		hasRequiredEs_array_indexOf = 1;
		/* eslint-disable es/no-array-prototype-indexof -- required for testing */
		var $ = /*@__PURE__*/ require_export$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThisClause$1();
		var $indexOf = /*@__PURE__*/ requireArrayIncludes$1().indexOf;
		var arrayMethodIsStrict = /*@__PURE__*/ requireArrayMethodIsStrict();

		var nativeIndexOf = uncurryThis([].indexOf);

		var NEGATIVE_ZERO = !!nativeIndexOf && 1 / nativeIndexOf([1], 1, -0) < 0;
		var FORCED = NEGATIVE_ZERO || !arrayMethodIsStrict('indexOf');

		// `Array.prototype.indexOf` method
		// https://tc39.es/ecma262/#sec-array.prototype.indexof
		$({ target: 'Array', proto: true, forced: FORCED }, {
		  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
		    var fromIndex = arguments.length > 1 ? arguments[1] : undefined;
		    return NEGATIVE_ZERO
		      // convert -0 to +0
		      ? nativeIndexOf(this, searchElement, fromIndex) || 0
		      : $indexOf(this, searchElement, fromIndex);
		  }
		});
		return es_array_indexOf;
	}

	var indexOf$3;
	var hasRequiredIndexOf$3;

	function requireIndexOf$3 () {
		if (hasRequiredIndexOf$3) return indexOf$3;
		hasRequiredIndexOf$3 = 1;
		requireEs_array_indexOf();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		indexOf$3 = getBuiltInPrototypeMethod('Array', 'indexOf');
		return indexOf$3;
	}

	var indexOf$2;
	var hasRequiredIndexOf$2;

	function requireIndexOf$2 () {
		if (hasRequiredIndexOf$2) return indexOf$2;
		hasRequiredIndexOf$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireIndexOf$3();

		var ArrayPrototype = Array.prototype;

		indexOf$2 = function (it) {
		  var own = it.indexOf;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.indexOf) ? method : own;
		};
		return indexOf$2;
	}

	var indexOf$1;
	var hasRequiredIndexOf$1;

	function requireIndexOf$1 () {
		if (hasRequiredIndexOf$1) return indexOf$1;
		hasRequiredIndexOf$1 = 1;
		var parent = /*@__PURE__*/ requireIndexOf$2();

		indexOf$1 = parent;
		return indexOf$1;
	}

	var indexOf;
	var hasRequiredIndexOf;

	function requireIndexOf () {
		if (hasRequiredIndexOf) return indexOf;
		hasRequiredIndexOf = 1;
		indexOf = /*@__PURE__*/ requireIndexOf$1();
		return indexOf;
	}

	var indexOfExports = requireIndexOf();
	var _indexOfInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(indexOfExports);

	/**
	 * Complete step.
	 */
	function stepComplete(params) {
	  let data = $.extend({
	      action: 'bookly_render_complete'
	    }, params),
	    $container = opt[params.form_id].$container;
	  booklyAjax({
	    data
	  }).then(response => {
	    if (response.final_step_url && !data.error) {
	      document.location.href = response.final_step_url;
	    } else {
	      var _context;
	      $container.html(response.html);
	      let $qc = $('.bookly-js-qr', $container),
	        url = BooklyL10n.ajaxurl + (_indexOfInstanceProperty(_context = BooklyL10n.ajaxurl).call(_context, '?') > 0 ? '&' : '?') + 'bookly_order=' + response.bookly_order + '&csrf_token=' + BooklyL10n.csrf_token;
	      new QRCode($qc.get(0), {
	        text: response.qr,
	        width: 256,
	        height: 256,
	        useSVG: true,
	        correctLevel: 1
	      });
	      scrollTo($container, params.form_id);
	      $('.bookly-js-start-over', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          reset_form: true,
	          new_chain: true
	        });
	      });
	      $('.bookly-js-download-ics', $container).on('click', function (e) {
	        let ladda = laddaStart(this);
	        window.location = url + '&action=bookly_add_to_calendar&calendar=ics';
	        _setTimeout(() => ladda.stop(), 1500);
	      });
	      $('.bookly-js-download-invoice', $container).on('click', function (e) {
	        let ladda = laddaStart(this);
	        window.location = url + '&action=bookly_invoices_download_invoice';
	        _setTimeout(() => ladda.stop(), 1500);
	      });
	      $('.bookly-js-add-to-calendar', $container).on('click', function (e) {
	        e.preventDefault();
	        let ladda = laddaStart(this);
	        window.open(url + '&action=bookly_add_to_calendar&calendar=' + $(this).data('calendar'), '_blank');
	        _setTimeout(() => ladda.stop(), 1500);
	      });
	    }
	  });
	}

	/**
	 * Payment step.
	 */
	function stepPayment(params) {
	  var $container = opt[params.form_id].$container;
	  booklyAjax({
	    type: 'POST',
	    data: {
	      action: 'bookly_render_payment',
	      form_id: params.form_id,
	      page_url: document.URL.split('#')[0]
	    }
	  }).then(response => {
	    // If payment step is disabled.
	    if (response.disabled) {
	      save(params.form_id);
	      return;
	    }
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    if (opt[params.form_id].status.booking == 'cancelled') {
	      opt[params.form_id].status.booking = 'ok';
	    }
	    const customJS = response.custom_js;
	    let $stripe_card_field = $('#bookly-stripe-card-field', $container);

	    // Init stripe intents form
	    if ($stripe_card_field.length) {
	      if (response.stripe_publishable_key) {
	        var stripe = Stripe(response.stripe_publishable_key, {
	          betas: ['payment_intent_beta_3']
	        });
	        var elements = stripe.elements();
	        var stripe_card = elements.create('cardNumber');
	        stripe_card.mount("#bookly-form-" + params.form_id + " #bookly-stripe-card-field");
	        var stripe_expiry = elements.create('cardExpiry');
	        stripe_expiry.mount("#bookly-form-" + params.form_id + " #bookly-stripe-card-expiry-field");
	        var stripe_cvc = elements.create('cardCvc');
	        stripe_cvc.mount("#bookly-form-" + params.form_id + " #bookly-stripe-card-cvc-field");
	      } else {
	        $('.pay-card .bookly-js-next-step', $container).prop('disabled', true);
	        let $details = $stripe_card_field.closest('.bookly-js-details');
	        $('.bookly-form-group', $details).hide();
	        $('.bookly-js-card-error', $details).text('Please call Stripe() with your publishable key. You used an empty string.');
	      }
	    }
	    var $payments = $('.bookly-js-payment', $container),
	      $apply_coupon_button = $('.bookly-js-apply-coupon', $container),
	      $coupon_input = $('input.bookly-user-coupon', $container),
	      $apply_gift_card_button = $('.bookly-js-apply-gift-card', $container),
	      $gift_card_input = $('input.bookly-user-gift', $container),
	      $apply_tips_button = $('.bookly-js-apply-tips', $container),
	      $applied_tips_button = $('.bookly-js-applied-tips', $container),
	      $tips_input = $('input.bookly-user-tips', $container),
	      $tips_error = $('.bookly-js-tips-error', $container),
	      $deposit_mode = $('input[type=radio][name=bookly-full-payment]', $container),
	      $coupon_info_text = $('.bookly-info-text-coupon', $container),
	      $buttons = $('.bookly-gateway-buttons,.bookly-js-details', $container),
	      $payment_details;
	    $payments.on('click', function () {
	      $buttons.hide();
	      $('.bookly-gateway-buttons.pay-' + $(this).val(), $container).show();
	      if ($(this).data('with-details') == 1) {
	        let $parent = $(this).closest('.bookly-list');
	        $payment_details = $('.bookly-js-details', $parent);
	        $('.bookly-js-details', $parent).show();
	      } else {
	        $payment_details = null;
	      }
	    });
	    $payments.eq(0).trigger('click');
	    $deposit_mode.on('change', function () {
	      let data = {
	        action: 'bookly_deposit_payments_apply_payment_method',
	        form_id: params.form_id,
	        deposit_full: $(this).val()
	      };
	      $(this).hide();
	      $(this).prev().css('display', 'inline-block');
	      booklyAjax({
	        type: 'POST',
	        data: data
	      }).then(response => {
	        stepPayment({
	          form_id: params.form_id
	        });
	      });
	    });
	    $apply_coupon_button.on('click', function (e) {
	      var ladda = laddaStart(this);
	      $coupon_input.removeClass('bookly-error');
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_coupons_apply_coupon',
	          form_id: params.form_id,
	          coupon_code: $coupon_input.val()
	        },
	        error: function () {
	          ladda.stop();
	        }
	      }).then(response => {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(response => {
	        $coupon_input.addClass('bookly-error');
	        $coupon_info_text.html(response.text);
	        $apply_coupon_button.next('.bookly-label-error').remove();
	        let $error = $('<div>', {
	          class: 'bookly-label-error',
	          text: response?.error || 'Error'
	        });
	        $error.insertAfter($apply_coupon_button);
	        scrollTo($error, params.form_id);
	      }).finally(() => {
	        ladda.stop();
	      });
	    });
	    $apply_gift_card_button.on('click', function (e) {
	      var ladda = laddaStart(this);
	      $gift_card_input.removeClass('bookly-error');
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_pro_apply_gift_card',
	          form_id: params.form_id,
	          gift_card: $gift_card_input.val()
	        },
	        error: function () {
	          ladda.stop();
	        }
	      }).then(response => {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(response => {
	        if ($('.bookly-js-payment[value!=free]', $container).length > 0) {
	          $gift_card_input.addClass('bookly-error');
	          $apply_gift_card_button.next('.bookly-label-error').remove();
	          let $error = $('<div>', {
	            class: 'bookly-label-error',
	            text: response?.error || 'Error'
	          });
	          $error.insertAfter($apply_gift_card_button);
	          scrollTo($error, params.form_id);
	        } else {
	          stepPayment({
	            form_id: params.form_id
	          });
	        }
	      }).finally(() => {
	        ladda.stop();
	      });
	    });
	    $tips_input.on('keyup', function () {
	      $applied_tips_button.hide();
	      $apply_tips_button.css('display', 'inline-block');
	    });
	    $apply_tips_button.on('click', function (e) {
	      var ladda = laddaStart(this);
	      $tips_error.text('');
	      $tips_input.removeClass('bookly-error');
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_pro_apply_tips',
	          form_id: params.form_id,
	          tips: $tips_input.val()
	        },
	        error: function () {
	          ladda.stop();
	        }
	      }).then(response => {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(response => {
	        $tips_error.html(response.error);
	        $tips_input.addClass('bookly-error');
	        scrollTo($tips_error, params.form_id);
	        ladda.stop();
	      });
	    });
	    $('.bookly-js-next-step', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      var ladda = laddaStart(this),
	        $gateway_checked = _filterInstanceProperty$1($payments).call($payments, ':checked');

	      // Execute custom JavaScript
	      if (customJS) {
	        try {
	          $.globalEval(customJS.next_button);
	        } catch (e) {
	          // Do nothing
	        }
	      }
	      if ($gateway_checked.val() === 'card') {
	        let gateway = $gateway_checked.data('gateway');
	        if (gateway === 'authorize_net') {
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_create_payment_intent',
	              card: {
	                number: $('input[name="card_number"]', $payment_details).val(),
	                cvc: $('input[name="card_cvc"]', $payment_details).val(),
	                exp_month: $('select[name="card_exp_month"]', $payment_details).val(),
	                exp_year: $('select[name="card_exp_year"]', $payment_details).val()
	              },
	              response_url: window.location.pathname + window.location.search.split('#')[0],
	              form_id: params.form_id,
	              gateway: gateway
	            }
	          }).then(response => {
	            retrieveRequest(response.data, params.form_id);
	          }).catch(response => {
	            handleBooklyAjaxError(response, params.form_id, $gateway_checked.closest('.bookly-list'));
	            ladda.stop();
	          });
	        } else if (gateway === 'stripe') {
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_create_payment_intent',
	              form_id: params.form_id,
	              response_url: window.location.pathname + window.location.search.split('#')[0],
	              gateway: gateway
	            }
	          }).then(response => {
	            stripe.confirmCardPayment(response.data.intent_secret, {
	              payment_method: {
	                card: stripe_card
	              }
	            }).then(function (result) {
	              if (result.error) {
	                booklyAjax({
	                  type: 'POST',
	                  data: {
	                    action: 'bookly_rollback_order',
	                    form_id: params.form_id,
	                    bookly_order: response.data.bookly_order
	                  }
	                }).then(response => {
	                  ladda.stop();
	                  let $stripe_container = $gateway_checked.closest('.bookly-list');
	                  $('.bookly-label-error', $stripe_container).remove();
	                  $stripe_container.append($('<div>', {
	                    class: 'bookly-label-error',
	                    text: result.error.message || 'Error'
	                  }));
	                });
	              } else {
	                retrieveRequest(response.data, params.form_id);
	              }
	            });
	          }).catch(response => {
	            handleBooklyAjaxError(response, params.form_id, $gateway_checked.closest('.bookly-list'));
	            ladda.stop();
	          });
	        }
	      } else {
	        booklyAjax({
	          type: 'POST',
	          data: {
	            action: 'bookly_create_payment_intent',
	            form_id: params.form_id,
	            gateway: $gateway_checked.val(),
	            response_url: window.location.pathname + window.location.search.split('#')[0]
	          }
	        }).then(response => {
	          retrieveRequest(response.data, params.form_id);
	        }).catch(response => {
	          handleBooklyAjaxError(response, params.form_id, $gateway_checked.closest('.bookly-list'));
	          ladda.stop();
	        });
	      }
	    });
	    $('.bookly-js-back-step', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepDetails({
	        form_id: params.form_id
	      });
	    });
	  });
	}

	/**
	 * Save appointment.
	 */
	function save(form_id) {
	  booklyAjax({
	    type: 'POST',
	    data: {
	      action: 'bookly_save_appointment',
	      form_id: form_id
	    }
	  }).then(response => {
	    stepComplete({
	      form_id: form_id
	    });
	  }).catch(response => {
	    if (response.error == 'cart_item_not_available') {
	      handleErrorCartItemNotAvailable(response, form_id);
	    }
	  });
	}

	/**
	 * Handle error with code 3 which means one of the cart item is not available anymore.
	 *
	 * @param response
	 * @param form_id
	 */
	function handleErrorCartItemNotAvailable(response, form_id) {
	  if (!opt[form_id].skip_steps.cart) {
	    stepCart({
	      form_id: form_id
	    }, {
	      failed_key: response.failed_cart_key,
	      message: opt[form_id].errors[response.error]
	    });
	  } else {
	    stepTime({
	      form_id: form_id
	    }, opt[form_id].errors[response.error]);
	  }
	}
	function handleBooklyAjaxError(response, form_id, $gateway_selector) {
	  if (response.error == 'cart_item_not_available') {
	    handleErrorCartItemNotAvailable(response, form_id);
	  } else if (response.error) {
	    $('.bookly-label-error', $gateway_selector).remove();
	    $gateway_selector.append($('<div>', {
	      class: 'bookly-label-error',
	      text: response?.error_message || 'Error'
	    }));
	  }
	}
	function retrieveRequest(data, form_id) {
	  if (data.on_site) {
	    $.ajax({
	      type: 'GET',
	      url: data.target_url,
	      xhrFields: {
	        withCredentials: true
	      },
	      crossDomain: 'withCredentials' in new XMLHttpRequest()
	    }).always(function () {
	      stepComplete({
	        form_id
	      });
	    });
	  } else {
	    document.location.href = data.target_url;
	  }
	}

	var fails;
	var hasRequiredFails;

	function requireFails () {
		if (hasRequiredFails) return fails;
		hasRequiredFails = 1;
		fails = function (exec) {
		  try {
		    return !!exec();
		  } catch (error) {
		    return true;
		  }
		};
		return fails;
	}

	var functionBindNative;
	var hasRequiredFunctionBindNative;

	function requireFunctionBindNative () {
		if (hasRequiredFunctionBindNative) return functionBindNative;
		hasRequiredFunctionBindNative = 1;
		var fails = /*@__PURE__*/ requireFails();

		functionBindNative = !fails(function () {
		  // eslint-disable-next-line es/no-function-prototype-bind -- safe
		  var test = (function () { /* empty */ }).bind();
		  // eslint-disable-next-line no-prototype-builtins -- safe
		  return typeof test != 'function' || test.hasOwnProperty('prototype');
		});
		return functionBindNative;
	}

	var functionUncurryThis;
	var hasRequiredFunctionUncurryThis;

	function requireFunctionUncurryThis () {
		if (hasRequiredFunctionUncurryThis) return functionUncurryThis;
		hasRequiredFunctionUncurryThis = 1;
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative();

		var FunctionPrototype = Function.prototype;
		var call = FunctionPrototype.call;
		// eslint-disable-next-line es/no-function-prototype-bind -- safe
		var uncurryThisWithBind = NATIVE_BIND && FunctionPrototype.bind.bind(call, call);

		functionUncurryThis = NATIVE_BIND ? uncurryThisWithBind : function (fn) {
		  return function () {
		    return call.apply(fn, arguments);
		  };
		};
		return functionUncurryThis;
	}

	var objectIsPrototypeOf;
	var hasRequiredObjectIsPrototypeOf;

	function requireObjectIsPrototypeOf () {
		if (hasRequiredObjectIsPrototypeOf) return objectIsPrototypeOf;
		hasRequiredObjectIsPrototypeOf = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();

		objectIsPrototypeOf = uncurryThis({}.isPrototypeOf);
		return objectIsPrototypeOf;
	}

	var es_array_slice = {};

	var globalThis_1;
	var hasRequiredGlobalThis$6;

	function requireGlobalThis$6 () {
		if (hasRequiredGlobalThis$6) return globalThis_1;
		hasRequiredGlobalThis$6 = 1;
		var check = function (it) {
		  return it && it.Math === Math && it;
		};

		// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
		globalThis_1 =
		  // eslint-disable-next-line es/no-global-this -- safe
		  check(typeof globalThis == 'object' && globalThis) ||
		  check(typeof window == 'object' && window) ||
		  // eslint-disable-next-line no-restricted-globals -- safe
		  check(typeof self == 'object' && self) ||
		  check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
		  check(typeof globalThis_1 == 'object' && globalThis_1) ||
		  // eslint-disable-next-line no-new-func -- fallback
		  (function () { return this; })() || Function('return this')();
		return globalThis_1;
	}

	var functionApply;
	var hasRequiredFunctionApply;

	function requireFunctionApply () {
		if (hasRequiredFunctionApply) return functionApply;
		hasRequiredFunctionApply = 1;
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative();

		var FunctionPrototype = Function.prototype;
		var apply = FunctionPrototype.apply;
		var call = FunctionPrototype.call;

		// eslint-disable-next-line es/no-function-prototype-bind, es/no-reflect -- safe
		functionApply = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND ? call.bind(apply) : function () {
		  return call.apply(apply, arguments);
		});
		return functionApply;
	}

	var classofRaw;
	var hasRequiredClassofRaw;

	function requireClassofRaw () {
		if (hasRequiredClassofRaw) return classofRaw;
		hasRequiredClassofRaw = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();

		var toString = uncurryThis({}.toString);
		var stringSlice = uncurryThis(''.slice);

		classofRaw = function (it) {
		  return stringSlice(toString(it), 8, -1);
		};
		return classofRaw;
	}

	var functionUncurryThisClause;
	var hasRequiredFunctionUncurryThisClause;

	function requireFunctionUncurryThisClause () {
		if (hasRequiredFunctionUncurryThisClause) return functionUncurryThisClause;
		hasRequiredFunctionUncurryThisClause = 1;
		var classofRaw = /*@__PURE__*/ requireClassofRaw();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();

		functionUncurryThisClause = function (fn) {
		  // Nashorn bug:
		  //   https://github.com/zloirock/core-js/issues/1128
		  //   https://github.com/zloirock/core-js/issues/1130
		  if (classofRaw(fn) === 'Function') return uncurryThis(fn);
		};
		return functionUncurryThisClause;
	}

	var isCallable;
	var hasRequiredIsCallable;

	function requireIsCallable () {
		if (hasRequiredIsCallable) return isCallable;
		hasRequiredIsCallable = 1;
		// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
		var documentAll = typeof document == 'object' && document.all;

		// `IsCallable` abstract operation
		// https://tc39.es/ecma262/#sec-iscallable
		// eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
		isCallable = typeof documentAll == 'undefined' && documentAll !== undefined ? function (argument) {
		  return typeof argument == 'function' || argument === documentAll;
		} : function (argument) {
		  return typeof argument == 'function';
		};
		return isCallable;
	}

	var objectGetOwnPropertyDescriptor = {};

	var descriptors;
	var hasRequiredDescriptors;

	function requireDescriptors () {
		if (hasRequiredDescriptors) return descriptors;
		hasRequiredDescriptors = 1;
		var fails = /*@__PURE__*/ requireFails();

		// Detect IE8's incomplete defineProperty implementation
		descriptors = !fails(function () {
		  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
		  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] !== 7;
		});
		return descriptors;
	}

	var functionCall;
	var hasRequiredFunctionCall;

	function requireFunctionCall () {
		if (hasRequiredFunctionCall) return functionCall;
		hasRequiredFunctionCall = 1;
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative();

		var call = Function.prototype.call;
		// eslint-disable-next-line es/no-function-prototype-bind -- safe
		functionCall = NATIVE_BIND ? call.bind(call) : function () {
		  return call.apply(call, arguments);
		};
		return functionCall;
	}

	var objectPropertyIsEnumerable = {};

	var hasRequiredObjectPropertyIsEnumerable;

	function requireObjectPropertyIsEnumerable () {
		if (hasRequiredObjectPropertyIsEnumerable) return objectPropertyIsEnumerable;
		hasRequiredObjectPropertyIsEnumerable = 1;
		var $propertyIsEnumerable = {}.propertyIsEnumerable;
		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

		// Nashorn ~ JDK8 bug
		var NASHORN_BUG = getOwnPropertyDescriptor && !$propertyIsEnumerable.call({ 1: 2 }, 1);

		// `Object.prototype.propertyIsEnumerable` method implementation
		// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
		objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
		  var descriptor = getOwnPropertyDescriptor(this, V);
		  return !!descriptor && descriptor.enumerable;
		} : $propertyIsEnumerable;
		return objectPropertyIsEnumerable;
	}

	var createPropertyDescriptor;
	var hasRequiredCreatePropertyDescriptor;

	function requireCreatePropertyDescriptor () {
		if (hasRequiredCreatePropertyDescriptor) return createPropertyDescriptor;
		hasRequiredCreatePropertyDescriptor = 1;
		createPropertyDescriptor = function (bitmap, value) {
		  return {
		    enumerable: !(bitmap & 1),
		    configurable: !(bitmap & 2),
		    writable: !(bitmap & 4),
		    value: value
		  };
		};
		return createPropertyDescriptor;
	}

	var indexedObject;
	var hasRequiredIndexedObject;

	function requireIndexedObject () {
		if (hasRequiredIndexedObject) return indexedObject;
		hasRequiredIndexedObject = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var fails = /*@__PURE__*/ requireFails();
		var classof = /*@__PURE__*/ requireClassofRaw();

		var $Object = Object;
		var split = uncurryThis(''.split);

		// fallback for non-array-like ES3 and non-enumerable old V8 strings
		indexedObject = fails(function () {
		  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
		  // eslint-disable-next-line no-prototype-builtins -- safe
		  return !$Object('z').propertyIsEnumerable(0);
		}) ? function (it) {
		  return classof(it) === 'String' ? split(it, '') : $Object(it);
		} : $Object;
		return indexedObject;
	}

	var isNullOrUndefined;
	var hasRequiredIsNullOrUndefined;

	function requireIsNullOrUndefined () {
		if (hasRequiredIsNullOrUndefined) return isNullOrUndefined;
		hasRequiredIsNullOrUndefined = 1;
		// we can't use just `it == null` since of `document.all` special case
		// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
		isNullOrUndefined = function (it) {
		  return it === null || it === undefined;
		};
		return isNullOrUndefined;
	}

	var requireObjectCoercible;
	var hasRequiredRequireObjectCoercible;

	function requireRequireObjectCoercible () {
		if (hasRequiredRequireObjectCoercible) return requireObjectCoercible;
		hasRequiredRequireObjectCoercible = 1;
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined();

		var $TypeError = TypeError;

		// `RequireObjectCoercible` abstract operation
		// https://tc39.es/ecma262/#sec-requireobjectcoercible
		requireObjectCoercible = function (it) {
		  if (isNullOrUndefined(it)) throw new $TypeError("Can't call method on " + it);
		  return it;
		};
		return requireObjectCoercible;
	}

	var toIndexedObject;
	var hasRequiredToIndexedObject;

	function requireToIndexedObject () {
		if (hasRequiredToIndexedObject) return toIndexedObject;
		hasRequiredToIndexedObject = 1;
		// toObject with fallback for non-array-like ES3 strings
		var IndexedObject = /*@__PURE__*/ requireIndexedObject();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible();

		toIndexedObject = function (it) {
		  return IndexedObject(requireObjectCoercible(it));
		};
		return toIndexedObject;
	}

	var isObject;
	var hasRequiredIsObject;

	function requireIsObject () {
		if (hasRequiredIsObject) return isObject;
		hasRequiredIsObject = 1;
		var isCallable = /*@__PURE__*/ requireIsCallable();

		isObject = function (it) {
		  return typeof it == 'object' ? it !== null : isCallable(it);
		};
		return isObject;
	}

	var path;
	var hasRequiredPath;

	function requirePath () {
		if (hasRequiredPath) return path;
		hasRequiredPath = 1;
		path = {};
		return path;
	}

	var getBuiltIn;
	var hasRequiredGetBuiltIn;

	function requireGetBuiltIn () {
		if (hasRequiredGetBuiltIn) return getBuiltIn;
		hasRequiredGetBuiltIn = 1;
		var path = /*@__PURE__*/ requirePath();
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var isCallable = /*@__PURE__*/ requireIsCallable();

		var aFunction = function (variable) {
		  return isCallable(variable) ? variable : undefined;
		};

		getBuiltIn = function (namespace, method) {
		  return arguments.length < 2 ? aFunction(path[namespace]) || aFunction(globalThis[namespace])
		    : path[namespace] && path[namespace][method] || globalThis[namespace] && globalThis[namespace][method];
		};
		return getBuiltIn;
	}

	var environmentUserAgent;
	var hasRequiredEnvironmentUserAgent;

	function requireEnvironmentUserAgent () {
		if (hasRequiredEnvironmentUserAgent) return environmentUserAgent;
		hasRequiredEnvironmentUserAgent = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();

		var navigator = globalThis.navigator;
		var userAgent = navigator && navigator.userAgent;

		environmentUserAgent = userAgent ? String(userAgent) : '';
		return environmentUserAgent;
	}

	var environmentV8Version;
	var hasRequiredEnvironmentV8Version;

	function requireEnvironmentV8Version () {
		if (hasRequiredEnvironmentV8Version) return environmentV8Version;
		hasRequiredEnvironmentV8Version = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent();

		var process = globalThis.process;
		var Deno = globalThis.Deno;
		var versions = process && process.versions || Deno && Deno.version;
		var v8 = versions && versions.v8;
		var match, version;

		if (v8) {
		  match = v8.split('.');
		  // in old Chrome, versions of V8 isn't V8 = Chrome / 10
		  // but their correct versions are not interesting for us
		  version = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
		}

		// BrowserFS NodeJS `process` polyfill incorrectly set `.v8` to `0.0`
		// so check `userAgent` even if `.v8` exists, but 0
		if (!version && userAgent) {
		  match = userAgent.match(/Edge\/(\d+)/);
		  if (!match || match[1] >= 74) {
		    match = userAgent.match(/Chrome\/(\d+)/);
		    if (match) version = +match[1];
		  }
		}

		environmentV8Version = version;
		return environmentV8Version;
	}

	var symbolConstructorDetection;
	var hasRequiredSymbolConstructorDetection;

	function requireSymbolConstructorDetection () {
		if (hasRequiredSymbolConstructorDetection) return symbolConstructorDetection;
		hasRequiredSymbolConstructorDetection = 1;
		/* eslint-disable es/no-symbol -- required for testing */
		var V8_VERSION = /*@__PURE__*/ requireEnvironmentV8Version();
		var fails = /*@__PURE__*/ requireFails();
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();

		var $String = globalThis.String;

		// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
		symbolConstructorDetection = !!Object.getOwnPropertySymbols && !fails(function () {
		  var symbol = Symbol('symbol detection');
		  // Chrome 38 Symbol has incorrect toString conversion
		  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
		  // nb: Do not call `String` directly to avoid this being optimized out to `symbol+''` which will,
		  // of course, fail.
		  return !$String(symbol) || !(Object(symbol) instanceof Symbol) ||
		    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
		    !Symbol.sham && V8_VERSION && V8_VERSION < 41;
		});
		return symbolConstructorDetection;
	}

	var useSymbolAsUid;
	var hasRequiredUseSymbolAsUid;

	function requireUseSymbolAsUid () {
		if (hasRequiredUseSymbolAsUid) return useSymbolAsUid;
		hasRequiredUseSymbolAsUid = 1;
		/* eslint-disable es/no-symbol -- required for testing */
		var NATIVE_SYMBOL = /*@__PURE__*/ requireSymbolConstructorDetection();

		useSymbolAsUid = NATIVE_SYMBOL &&
		  !Symbol.sham &&
		  typeof Symbol.iterator == 'symbol';
		return useSymbolAsUid;
	}

	var isSymbol;
	var hasRequiredIsSymbol;

	function requireIsSymbol () {
		if (hasRequiredIsSymbol) return isSymbol;
		hasRequiredIsSymbol = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf();
		var USE_SYMBOL_AS_UID = /*@__PURE__*/ requireUseSymbolAsUid();

		var $Object = Object;

		isSymbol = USE_SYMBOL_AS_UID ? function (it) {
		  return typeof it == 'symbol';
		} : function (it) {
		  var $Symbol = getBuiltIn('Symbol');
		  return isCallable($Symbol) && isPrototypeOf($Symbol.prototype, $Object(it));
		};
		return isSymbol;
	}

	var tryToString;
	var hasRequiredTryToString;

	function requireTryToString () {
		if (hasRequiredTryToString) return tryToString;
		hasRequiredTryToString = 1;
		var $String = String;

		tryToString = function (argument) {
		  try {
		    return $String(argument);
		  } catch (error) {
		    return 'Object';
		  }
		};
		return tryToString;
	}

	var aCallable;
	var hasRequiredACallable;

	function requireACallable () {
		if (hasRequiredACallable) return aCallable;
		hasRequiredACallable = 1;
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var tryToString = /*@__PURE__*/ requireTryToString();

		var $TypeError = TypeError;

		// `Assert: IsCallable(argument) is true`
		aCallable = function (argument) {
		  if (isCallable(argument)) return argument;
		  throw new $TypeError(tryToString(argument) + ' is not a function');
		};
		return aCallable;
	}

	var getMethod;
	var hasRequiredGetMethod;

	function requireGetMethod () {
		if (hasRequiredGetMethod) return getMethod;
		hasRequiredGetMethod = 1;
		var aCallable = /*@__PURE__*/ requireACallable();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined();

		// `GetMethod` abstract operation
		// https://tc39.es/ecma262/#sec-getmethod
		getMethod = function (V, P) {
		  var func = V[P];
		  return isNullOrUndefined(func) ? undefined : aCallable(func);
		};
		return getMethod;
	}

	var ordinaryToPrimitive;
	var hasRequiredOrdinaryToPrimitive;

	function requireOrdinaryToPrimitive () {
		if (hasRequiredOrdinaryToPrimitive) return ordinaryToPrimitive;
		hasRequiredOrdinaryToPrimitive = 1;
		var call = /*@__PURE__*/ requireFunctionCall();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var isObject = /*@__PURE__*/ requireIsObject();

		var $TypeError = TypeError;

		// `OrdinaryToPrimitive` abstract operation
		// https://tc39.es/ecma262/#sec-ordinarytoprimitive
		ordinaryToPrimitive = function (input, pref) {
		  var fn, val;
		  if (pref === 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
		  if (isCallable(fn = input.valueOf) && !isObject(val = call(fn, input))) return val;
		  if (pref !== 'string' && isCallable(fn = input.toString) && !isObject(val = call(fn, input))) return val;
		  throw new $TypeError("Can't convert object to primitive value");
		};
		return ordinaryToPrimitive;
	}

	var sharedStore = {exports: {}};

	var isPure;
	var hasRequiredIsPure;

	function requireIsPure () {
		if (hasRequiredIsPure) return isPure;
		hasRequiredIsPure = 1;
		isPure = true;
		return isPure;
	}

	var defineGlobalProperty;
	var hasRequiredDefineGlobalProperty;

	function requireDefineGlobalProperty () {
		if (hasRequiredDefineGlobalProperty) return defineGlobalProperty;
		hasRequiredDefineGlobalProperty = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();

		// eslint-disable-next-line es/no-object-defineproperty -- safe
		var defineProperty = Object.defineProperty;

		defineGlobalProperty = function (key, value) {
		  try {
		    defineProperty(globalThis, key, { value: value, configurable: true, writable: true });
		  } catch (error) {
		    globalThis[key] = value;
		  } return value;
		};
		return defineGlobalProperty;
	}

	var hasRequiredSharedStore;

	function requireSharedStore () {
		if (hasRequiredSharedStore) return sharedStore.exports;
		hasRequiredSharedStore = 1;
		var IS_PURE = /*@__PURE__*/ requireIsPure();
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var defineGlobalProperty = /*@__PURE__*/ requireDefineGlobalProperty();

		var SHARED = '__core-js_shared__';
		var store = sharedStore.exports = globalThis[SHARED] || defineGlobalProperty(SHARED, {});

		(store.versions || (store.versions = [])).push({
		  version: '3.44.0',
		  mode: IS_PURE ? 'pure' : 'global',
		  copyright: '© 2014-2025 Denis Pushkarev (zloirock.ru)',
		  license: 'https://github.com/zloirock/core-js/blob/v3.44.0/LICENSE',
		  source: 'https://github.com/zloirock/core-js'
		});
		return sharedStore.exports;
	}

	var shared;
	var hasRequiredShared;

	function requireShared () {
		if (hasRequiredShared) return shared;
		hasRequiredShared = 1;
		var store = /*@__PURE__*/ requireSharedStore();

		shared = function (key, value) {
		  return store[key] || (store[key] = value || {});
		};
		return shared;
	}

	var toObject;
	var hasRequiredToObject;

	function requireToObject () {
		if (hasRequiredToObject) return toObject;
		hasRequiredToObject = 1;
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible();

		var $Object = Object;

		// `ToObject` abstract operation
		// https://tc39.es/ecma262/#sec-toobject
		toObject = function (argument) {
		  return $Object(requireObjectCoercible(argument));
		};
		return toObject;
	}

	var hasOwnProperty_1;
	var hasRequiredHasOwnProperty;

	function requireHasOwnProperty () {
		if (hasRequiredHasOwnProperty) return hasOwnProperty_1;
		hasRequiredHasOwnProperty = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var toObject = /*@__PURE__*/ requireToObject();

		var hasOwnProperty = uncurryThis({}.hasOwnProperty);

		// `HasOwnProperty` abstract operation
		// https://tc39.es/ecma262/#sec-hasownproperty
		// eslint-disable-next-line es/no-object-hasown -- safe
		hasOwnProperty_1 = Object.hasOwn || function hasOwn(it, key) {
		  return hasOwnProperty(toObject(it), key);
		};
		return hasOwnProperty_1;
	}

	var uid;
	var hasRequiredUid;

	function requireUid () {
		if (hasRequiredUid) return uid;
		hasRequiredUid = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();

		var id = 0;
		var postfix = Math.random();
		var toString = uncurryThis(1.1.toString);

		uid = function (key) {
		  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString(++id + postfix, 36);
		};
		return uid;
	}

	var wellKnownSymbol;
	var hasRequiredWellKnownSymbol;

	function requireWellKnownSymbol () {
		if (hasRequiredWellKnownSymbol) return wellKnownSymbol;
		hasRequiredWellKnownSymbol = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var shared = /*@__PURE__*/ requireShared();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();
		var uid = /*@__PURE__*/ requireUid();
		var NATIVE_SYMBOL = /*@__PURE__*/ requireSymbolConstructorDetection();
		var USE_SYMBOL_AS_UID = /*@__PURE__*/ requireUseSymbolAsUid();

		var Symbol = globalThis.Symbol;
		var WellKnownSymbolsStore = shared('wks');
		var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol['for'] || Symbol : Symbol && Symbol.withoutSetter || uid;

		wellKnownSymbol = function (name) {
		  if (!hasOwn(WellKnownSymbolsStore, name)) {
		    WellKnownSymbolsStore[name] = NATIVE_SYMBOL && hasOwn(Symbol, name)
		      ? Symbol[name]
		      : createWellKnownSymbol('Symbol.' + name);
		  } return WellKnownSymbolsStore[name];
		};
		return wellKnownSymbol;
	}

	var toPrimitive;
	var hasRequiredToPrimitive;

	function requireToPrimitive () {
		if (hasRequiredToPrimitive) return toPrimitive;
		hasRequiredToPrimitive = 1;
		var call = /*@__PURE__*/ requireFunctionCall();
		var isObject = /*@__PURE__*/ requireIsObject();
		var isSymbol = /*@__PURE__*/ requireIsSymbol();
		var getMethod = /*@__PURE__*/ requireGetMethod();
		var ordinaryToPrimitive = /*@__PURE__*/ requireOrdinaryToPrimitive();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var $TypeError = TypeError;
		var TO_PRIMITIVE = wellKnownSymbol('toPrimitive');

		// `ToPrimitive` abstract operation
		// https://tc39.es/ecma262/#sec-toprimitive
		toPrimitive = function (input, pref) {
		  if (!isObject(input) || isSymbol(input)) return input;
		  var exoticToPrim = getMethod(input, TO_PRIMITIVE);
		  var result;
		  if (exoticToPrim) {
		    if (pref === undefined) pref = 'default';
		    result = call(exoticToPrim, input, pref);
		    if (!isObject(result) || isSymbol(result)) return result;
		    throw new $TypeError("Can't convert object to primitive value");
		  }
		  if (pref === undefined) pref = 'number';
		  return ordinaryToPrimitive(input, pref);
		};
		return toPrimitive;
	}

	var toPropertyKey;
	var hasRequiredToPropertyKey;

	function requireToPropertyKey () {
		if (hasRequiredToPropertyKey) return toPropertyKey;
		hasRequiredToPropertyKey = 1;
		var toPrimitive = /*@__PURE__*/ requireToPrimitive();
		var isSymbol = /*@__PURE__*/ requireIsSymbol();

		// `ToPropertyKey` abstract operation
		// https://tc39.es/ecma262/#sec-topropertykey
		toPropertyKey = function (argument) {
		  var key = toPrimitive(argument, 'string');
		  return isSymbol(key) ? key : key + '';
		};
		return toPropertyKey;
	}

	var documentCreateElement;
	var hasRequiredDocumentCreateElement;

	function requireDocumentCreateElement () {
		if (hasRequiredDocumentCreateElement) return documentCreateElement;
		hasRequiredDocumentCreateElement = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var isObject = /*@__PURE__*/ requireIsObject();

		var document = globalThis.document;
		// typeof document.createElement is 'object' in old IE
		var EXISTS = isObject(document) && isObject(document.createElement);

		documentCreateElement = function (it) {
		  return EXISTS ? document.createElement(it) : {};
		};
		return documentCreateElement;
	}

	var ie8DomDefine;
	var hasRequiredIe8DomDefine;

	function requireIe8DomDefine () {
		if (hasRequiredIe8DomDefine) return ie8DomDefine;
		hasRequiredIe8DomDefine = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var fails = /*@__PURE__*/ requireFails();
		var createElement = /*@__PURE__*/ requireDocumentCreateElement();

		// Thanks to IE8 for its funny defineProperty
		ie8DomDefine = !DESCRIPTORS && !fails(function () {
		  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
		  return Object.defineProperty(createElement('div'), 'a', {
		    get: function () { return 7; }
		  }).a !== 7;
		});
		return ie8DomDefine;
	}

	var hasRequiredObjectGetOwnPropertyDescriptor;

	function requireObjectGetOwnPropertyDescriptor () {
		if (hasRequiredObjectGetOwnPropertyDescriptor) return objectGetOwnPropertyDescriptor;
		hasRequiredObjectGetOwnPropertyDescriptor = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var call = /*@__PURE__*/ requireFunctionCall();
		var propertyIsEnumerableModule = /*@__PURE__*/ requireObjectPropertyIsEnumerable();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject();
		var toPropertyKey = /*@__PURE__*/ requireToPropertyKey();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();
		var IE8_DOM_DEFINE = /*@__PURE__*/ requireIe8DomDefine();

		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

		// `Object.getOwnPropertyDescriptor` method
		// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
		objectGetOwnPropertyDescriptor.f = DESCRIPTORS ? $getOwnPropertyDescriptor : function getOwnPropertyDescriptor(O, P) {
		  O = toIndexedObject(O);
		  P = toPropertyKey(P);
		  if (IE8_DOM_DEFINE) try {
		    return $getOwnPropertyDescriptor(O, P);
		  } catch (error) { /* empty */ }
		  if (hasOwn(O, P)) return createPropertyDescriptor(!call(propertyIsEnumerableModule.f, O, P), O[P]);
		};
		return objectGetOwnPropertyDescriptor;
	}

	var isForced_1;
	var hasRequiredIsForced;

	function requireIsForced () {
		if (hasRequiredIsForced) return isForced_1;
		hasRequiredIsForced = 1;
		var fails = /*@__PURE__*/ requireFails();
		var isCallable = /*@__PURE__*/ requireIsCallable();

		var replacement = /#|\.prototype\./;

		var isForced = function (feature, detection) {
		  var value = data[normalize(feature)];
		  return value === POLYFILL ? true
		    : value === NATIVE ? false
		    : isCallable(detection) ? fails(detection)
		    : !!detection;
		};

		var normalize = isForced.normalize = function (string) {
		  return String(string).replace(replacement, '.').toLowerCase();
		};

		var data = isForced.data = {};
		var NATIVE = isForced.NATIVE = 'N';
		var POLYFILL = isForced.POLYFILL = 'P';

		isForced_1 = isForced;
		return isForced_1;
	}

	var functionBindContext;
	var hasRequiredFunctionBindContext;

	function requireFunctionBindContext () {
		if (hasRequiredFunctionBindContext) return functionBindContext;
		hasRequiredFunctionBindContext = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThisClause();
		var aCallable = /*@__PURE__*/ requireACallable();
		var NATIVE_BIND = /*@__PURE__*/ requireFunctionBindNative();

		var bind = uncurryThis(uncurryThis.bind);

		// optional / simple context binding
		functionBindContext = function (fn, that) {
		  aCallable(fn);
		  return that === undefined ? fn : NATIVE_BIND ? bind(fn, that) : function (/* ...args */) {
		    return fn.apply(that, arguments);
		  };
		};
		return functionBindContext;
	}

	var objectDefineProperty = {};

	var v8PrototypeDefineBug;
	var hasRequiredV8PrototypeDefineBug;

	function requireV8PrototypeDefineBug () {
		if (hasRequiredV8PrototypeDefineBug) return v8PrototypeDefineBug;
		hasRequiredV8PrototypeDefineBug = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var fails = /*@__PURE__*/ requireFails();

		// V8 ~ Chrome 36-
		// https://bugs.chromium.org/p/v8/issues/detail?id=3334
		v8PrototypeDefineBug = DESCRIPTORS && fails(function () {
		  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
		  return Object.defineProperty(function () { /* empty */ }, 'prototype', {
		    value: 42,
		    writable: false
		  }).prototype !== 42;
		});
		return v8PrototypeDefineBug;
	}

	var anObject;
	var hasRequiredAnObject;

	function requireAnObject () {
		if (hasRequiredAnObject) return anObject;
		hasRequiredAnObject = 1;
		var isObject = /*@__PURE__*/ requireIsObject();

		var $String = String;
		var $TypeError = TypeError;

		// `Assert: Type(argument) is Object`
		anObject = function (argument) {
		  if (isObject(argument)) return argument;
		  throw new $TypeError($String(argument) + ' is not an object');
		};
		return anObject;
	}

	var hasRequiredObjectDefineProperty;

	function requireObjectDefineProperty () {
		if (hasRequiredObjectDefineProperty) return objectDefineProperty;
		hasRequiredObjectDefineProperty = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var IE8_DOM_DEFINE = /*@__PURE__*/ requireIe8DomDefine();
		var V8_PROTOTYPE_DEFINE_BUG = /*@__PURE__*/ requireV8PrototypeDefineBug();
		var anObject = /*@__PURE__*/ requireAnObject();
		var toPropertyKey = /*@__PURE__*/ requireToPropertyKey();

		var $TypeError = TypeError;
		// eslint-disable-next-line es/no-object-defineproperty -- safe
		var $defineProperty = Object.defineProperty;
		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
		var ENUMERABLE = 'enumerable';
		var CONFIGURABLE = 'configurable';
		var WRITABLE = 'writable';

		// `Object.defineProperty` method
		// https://tc39.es/ecma262/#sec-object.defineproperty
		objectDefineProperty.f = DESCRIPTORS ? V8_PROTOTYPE_DEFINE_BUG ? function defineProperty(O, P, Attributes) {
		  anObject(O);
		  P = toPropertyKey(P);
		  anObject(Attributes);
		  if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
		    var current = $getOwnPropertyDescriptor(O, P);
		    if (current && current[WRITABLE]) {
		      O[P] = Attributes.value;
		      Attributes = {
		        configurable: CONFIGURABLE in Attributes ? Attributes[CONFIGURABLE] : current[CONFIGURABLE],
		        enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
		        writable: false
		      };
		    }
		  } return $defineProperty(O, P, Attributes);
		} : $defineProperty : function defineProperty(O, P, Attributes) {
		  anObject(O);
		  P = toPropertyKey(P);
		  anObject(Attributes);
		  if (IE8_DOM_DEFINE) try {
		    return $defineProperty(O, P, Attributes);
		  } catch (error) { /* empty */ }
		  if ('get' in Attributes || 'set' in Attributes) throw new $TypeError('Accessors not supported');
		  if ('value' in Attributes) O[P] = Attributes.value;
		  return O;
		};
		return objectDefineProperty;
	}

	var createNonEnumerableProperty;
	var hasRequiredCreateNonEnumerableProperty;

	function requireCreateNonEnumerableProperty () {
		if (hasRequiredCreateNonEnumerableProperty) return createNonEnumerableProperty;
		hasRequiredCreateNonEnumerableProperty = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var definePropertyModule = /*@__PURE__*/ requireObjectDefineProperty();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor();

		createNonEnumerableProperty = DESCRIPTORS ? function (object, key, value) {
		  return definePropertyModule.f(object, key, createPropertyDescriptor(1, value));
		} : function (object, key, value) {
		  object[key] = value;
		  return object;
		};
		return createNonEnumerableProperty;
	}

	var _export;
	var hasRequired_export;

	function require_export () {
		if (hasRequired_export) return _export;
		hasRequired_export = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var apply = /*@__PURE__*/ requireFunctionApply();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThisClause();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var getOwnPropertyDescriptor = /*@__PURE__*/ requireObjectGetOwnPropertyDescriptor().f;
		var isForced = /*@__PURE__*/ requireIsForced();
		var path = /*@__PURE__*/ requirePath();
		var bind = /*@__PURE__*/ requireFunctionBindContext();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();

		var wrapConstructor = function (NativeConstructor) {
		  var Wrapper = function (a, b, c) {
		    if (this instanceof Wrapper) {
		      switch (arguments.length) {
		        case 0: return new NativeConstructor();
		        case 1: return new NativeConstructor(a);
		        case 2: return new NativeConstructor(a, b);
		      } return new NativeConstructor(a, b, c);
		    } return apply(NativeConstructor, this, arguments);
		  };
		  Wrapper.prototype = NativeConstructor.prototype;
		  return Wrapper;
		};

		/*
		  options.target         - name of the target object
		  options.global         - target is the global object
		  options.stat           - export as static methods of target
		  options.proto          - export as prototype methods of target
		  options.real           - real prototype method for the `pure` version
		  options.forced         - export even if the native feature is available
		  options.bind           - bind methods to the target, required for the `pure` version
		  options.wrap           - wrap constructors to preventing global pollution, required for the `pure` version
		  options.unsafe         - use the simple assignment of property instead of delete + defineProperty
		  options.sham           - add a flag to not completely full polyfills
		  options.enumerable     - export as enumerable property
		  options.dontCallGetSet - prevent calling a getter on target
		  options.name           - the .name of the function if it does not match the key
		*/
		_export = function (options, source) {
		  var TARGET = options.target;
		  var GLOBAL = options.global;
		  var STATIC = options.stat;
		  var PROTO = options.proto;

		  var nativeSource = GLOBAL ? globalThis : STATIC ? globalThis[TARGET] : globalThis[TARGET] && globalThis[TARGET].prototype;

		  var target = GLOBAL ? path : path[TARGET] || createNonEnumerableProperty(path, TARGET, {})[TARGET];
		  var targetPrototype = target.prototype;

		  var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
		  var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

		  for (key in source) {
		    FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
		    // contains in native
		    USE_NATIVE = !FORCED && nativeSource && hasOwn(nativeSource, key);

		    targetProperty = target[key];

		    if (USE_NATIVE) if (options.dontCallGetSet) {
		      descriptor = getOwnPropertyDescriptor(nativeSource, key);
		      nativeProperty = descriptor && descriptor.value;
		    } else nativeProperty = nativeSource[key];

		    // export native or implementation
		    sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

		    if (!FORCED && !PROTO && typeof targetProperty == typeof sourceProperty) continue;

		    // bind methods to global for calling from export context
		    if (options.bind && USE_NATIVE) resultProperty = bind(sourceProperty, globalThis);
		    // wrap global constructors for prevent changes in this version
		    else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor(sourceProperty);
		    // make static versions for prototype methods
		    else if (PROTO && isCallable(sourceProperty)) resultProperty = uncurryThis(sourceProperty);
		    // default case
		    else resultProperty = sourceProperty;

		    // add a flag to not completely full polyfills
		    if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
		      createNonEnumerableProperty(resultProperty, 'sham', true);
		    }

		    createNonEnumerableProperty(target, key, resultProperty);

		    if (PROTO) {
		      VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
		      if (!hasOwn(path, VIRTUAL_PROTOTYPE)) {
		        createNonEnumerableProperty(path, VIRTUAL_PROTOTYPE, {});
		      }
		      // export virtual prototype methods
		      createNonEnumerableProperty(path[VIRTUAL_PROTOTYPE], key, sourceProperty);
		      // export real prototype methods
		      if (options.real && targetPrototype && (FORCED || !targetPrototype[key])) {
		        createNonEnumerableProperty(targetPrototype, key, sourceProperty);
		      }
		    }
		  }
		};
		return _export;
	}

	var isArray;
	var hasRequiredIsArray;

	function requireIsArray () {
		if (hasRequiredIsArray) return isArray;
		hasRequiredIsArray = 1;
		var classof = /*@__PURE__*/ requireClassofRaw();

		// `IsArray` abstract operation
		// https://tc39.es/ecma262/#sec-isarray
		// eslint-disable-next-line es/no-array-isarray -- safe
		isArray = Array.isArray || function isArray(argument) {
		  return classof(argument) === 'Array';
		};
		return isArray;
	}

	var toStringTagSupport;
	var hasRequiredToStringTagSupport;

	function requireToStringTagSupport () {
		if (hasRequiredToStringTagSupport) return toStringTagSupport;
		hasRequiredToStringTagSupport = 1;
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var TO_STRING_TAG = wellKnownSymbol('toStringTag');
		var test = {};

		test[TO_STRING_TAG] = 'z';

		toStringTagSupport = String(test) === '[object z]';
		return toStringTagSupport;
	}

	var classof;
	var hasRequiredClassof;

	function requireClassof () {
		if (hasRequiredClassof) return classof;
		hasRequiredClassof = 1;
		var TO_STRING_TAG_SUPPORT = /*@__PURE__*/ requireToStringTagSupport();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var classofRaw = /*@__PURE__*/ requireClassofRaw();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var TO_STRING_TAG = wellKnownSymbol('toStringTag');
		var $Object = Object;

		// ES3 wrong here
		var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) === 'Arguments';

		// fallback for IE11 Script Access Denied error
		var tryGet = function (it, key) {
		  try {
		    return it[key];
		  } catch (error) { /* empty */ }
		};

		// getting tag from ES6+ `Object.prototype.toString`
		classof = TO_STRING_TAG_SUPPORT ? classofRaw : function (it) {
		  var O, tag, result;
		  return it === undefined ? 'Undefined' : it === null ? 'Null'
		    // @@toStringTag case
		    : typeof (tag = tryGet(O = $Object(it), TO_STRING_TAG)) == 'string' ? tag
		    // builtinTag case
		    : CORRECT_ARGUMENTS ? classofRaw(O)
		    // ES3 arguments fallback
		    : (result = classofRaw(O)) === 'Object' && isCallable(O.callee) ? 'Arguments' : result;
		};
		return classof;
	}

	var inspectSource;
	var hasRequiredInspectSource;

	function requireInspectSource () {
		if (hasRequiredInspectSource) return inspectSource;
		hasRequiredInspectSource = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var store = /*@__PURE__*/ requireSharedStore();

		var functionToString = uncurryThis(Function.toString);

		// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
		if (!isCallable(store.inspectSource)) {
		  store.inspectSource = function (it) {
		    return functionToString(it);
		  };
		}

		inspectSource = store.inspectSource;
		return inspectSource;
	}

	var isConstructor;
	var hasRequiredIsConstructor;

	function requireIsConstructor () {
		if (hasRequiredIsConstructor) return isConstructor;
		hasRequiredIsConstructor = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var fails = /*@__PURE__*/ requireFails();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var classof = /*@__PURE__*/ requireClassof();
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn();
		var inspectSource = /*@__PURE__*/ requireInspectSource();

		var noop = function () { /* empty */ };
		var construct = getBuiltIn('Reflect', 'construct');
		var constructorRegExp = /^\s*(?:class|function)\b/;
		var exec = uncurryThis(constructorRegExp.exec);
		var INCORRECT_TO_STRING = !constructorRegExp.test(noop);

		var isConstructorModern = function isConstructor(argument) {
		  if (!isCallable(argument)) return false;
		  try {
		    construct(noop, [], argument);
		    return true;
		  } catch (error) {
		    return false;
		  }
		};

		var isConstructorLegacy = function isConstructor(argument) {
		  if (!isCallable(argument)) return false;
		  switch (classof(argument)) {
		    case 'AsyncFunction':
		    case 'GeneratorFunction':
		    case 'AsyncGeneratorFunction': return false;
		  }
		  try {
		    // we can't check .prototype since constructors produced by .bind haven't it
		    // `Function#toString` throws on some built-it function in some legacy engines
		    // (for example, `DOMQuad` and similar in FF41-)
		    return INCORRECT_TO_STRING || !!exec(constructorRegExp, inspectSource(argument));
		  } catch (error) {
		    return true;
		  }
		};

		isConstructorLegacy.sham = true;

		// `IsConstructor` abstract operation
		// https://tc39.es/ecma262/#sec-isconstructor
		isConstructor = !construct || fails(function () {
		  var called;
		  return isConstructorModern(isConstructorModern.call)
		    || !isConstructorModern(Object)
		    || !isConstructorModern(function () { called = true; })
		    || called;
		}) ? isConstructorLegacy : isConstructorModern;
		return isConstructor;
	}

	var mathTrunc;
	var hasRequiredMathTrunc;

	function requireMathTrunc () {
		if (hasRequiredMathTrunc) return mathTrunc;
		hasRequiredMathTrunc = 1;
		var ceil = Math.ceil;
		var floor = Math.floor;

		// `Math.trunc` method
		// https://tc39.es/ecma262/#sec-math.trunc
		// eslint-disable-next-line es/no-math-trunc -- safe
		mathTrunc = Math.trunc || function trunc(x) {
		  var n = +x;
		  return (n > 0 ? floor : ceil)(n);
		};
		return mathTrunc;
	}

	var toIntegerOrInfinity;
	var hasRequiredToIntegerOrInfinity;

	function requireToIntegerOrInfinity () {
		if (hasRequiredToIntegerOrInfinity) return toIntegerOrInfinity;
		hasRequiredToIntegerOrInfinity = 1;
		var trunc = /*@__PURE__*/ requireMathTrunc();

		// `ToIntegerOrInfinity` abstract operation
		// https://tc39.es/ecma262/#sec-tointegerorinfinity
		toIntegerOrInfinity = function (argument) {
		  var number = +argument;
		  // eslint-disable-next-line no-self-compare -- NaN check
		  return number !== number || number === 0 ? 0 : trunc(number);
		};
		return toIntegerOrInfinity;
	}

	var toAbsoluteIndex;
	var hasRequiredToAbsoluteIndex;

	function requireToAbsoluteIndex () {
		if (hasRequiredToAbsoluteIndex) return toAbsoluteIndex;
		hasRequiredToAbsoluteIndex = 1;
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity();

		var max = Math.max;
		var min = Math.min;

		// Helper for a popular repeating case of the spec:
		// Let integer be ? ToInteger(index).
		// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
		toAbsoluteIndex = function (index, length) {
		  var integer = toIntegerOrInfinity(index);
		  return integer < 0 ? max(integer + length, 0) : min(integer, length);
		};
		return toAbsoluteIndex;
	}

	var toLength;
	var hasRequiredToLength;

	function requireToLength () {
		if (hasRequiredToLength) return toLength;
		hasRequiredToLength = 1;
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity();

		var min = Math.min;

		// `ToLength` abstract operation
		// https://tc39.es/ecma262/#sec-tolength
		toLength = function (argument) {
		  var len = toIntegerOrInfinity(argument);
		  return len > 0 ? min(len, 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
		};
		return toLength;
	}

	var lengthOfArrayLike;
	var hasRequiredLengthOfArrayLike;

	function requireLengthOfArrayLike () {
		if (hasRequiredLengthOfArrayLike) return lengthOfArrayLike;
		hasRequiredLengthOfArrayLike = 1;
		var toLength = /*@__PURE__*/ requireToLength();

		// `LengthOfArrayLike` abstract operation
		// https://tc39.es/ecma262/#sec-lengthofarraylike
		lengthOfArrayLike = function (obj) {
		  return toLength(obj.length);
		};
		return lengthOfArrayLike;
	}

	var createProperty;
	var hasRequiredCreateProperty;

	function requireCreateProperty () {
		if (hasRequiredCreateProperty) return createProperty;
		hasRequiredCreateProperty = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var definePropertyModule = /*@__PURE__*/ requireObjectDefineProperty();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor();

		createProperty = function (object, key, value) {
		  if (DESCRIPTORS) definePropertyModule.f(object, key, createPropertyDescriptor(0, value));
		  else object[key] = value;
		};
		return createProperty;
	}

	var arrayMethodHasSpeciesSupport;
	var hasRequiredArrayMethodHasSpeciesSupport;

	function requireArrayMethodHasSpeciesSupport () {
		if (hasRequiredArrayMethodHasSpeciesSupport) return arrayMethodHasSpeciesSupport;
		hasRequiredArrayMethodHasSpeciesSupport = 1;
		var fails = /*@__PURE__*/ requireFails();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();
		var V8_VERSION = /*@__PURE__*/ requireEnvironmentV8Version();

		var SPECIES = wellKnownSymbol('species');

		arrayMethodHasSpeciesSupport = function (METHOD_NAME) {
		  // We can't use this feature detection in V8 since it causes
		  // deoptimization and serious performance degradation
		  // https://github.com/zloirock/core-js/issues/677
		  return V8_VERSION >= 51 || !fails(function () {
		    var array = [];
		    var constructor = array.constructor = {};
		    constructor[SPECIES] = function () {
		      return { foo: 1 };
		    };
		    return array[METHOD_NAME](Boolean).foo !== 1;
		  });
		};
		return arrayMethodHasSpeciesSupport;
	}

	var arraySlice;
	var hasRequiredArraySlice;

	function requireArraySlice () {
		if (hasRequiredArraySlice) return arraySlice;
		hasRequiredArraySlice = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();

		arraySlice = uncurryThis([].slice);
		return arraySlice;
	}

	var hasRequiredEs_array_slice;

	function requireEs_array_slice () {
		if (hasRequiredEs_array_slice) return es_array_slice;
		hasRequiredEs_array_slice = 1;
		var $ = /*@__PURE__*/ require_export();
		var isArray = /*@__PURE__*/ requireIsArray();
		var isConstructor = /*@__PURE__*/ requireIsConstructor();
		var isObject = /*@__PURE__*/ requireIsObject();
		var toAbsoluteIndex = /*@__PURE__*/ requireToAbsoluteIndex();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject();
		var createProperty = /*@__PURE__*/ requireCreateProperty();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();
		var arrayMethodHasSpeciesSupport = /*@__PURE__*/ requireArrayMethodHasSpeciesSupport();
		var nativeSlice = /*@__PURE__*/ requireArraySlice();

		var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('slice');

		var SPECIES = wellKnownSymbol('species');
		var $Array = Array;
		var max = Math.max;

		// `Array.prototype.slice` method
		// https://tc39.es/ecma262/#sec-array.prototype.slice
		// fallback for not array-like ES3 strings and DOM objects
		$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
		  slice: function slice(start, end) {
		    var O = toIndexedObject(this);
		    var length = lengthOfArrayLike(O);
		    var k = toAbsoluteIndex(start, length);
		    var fin = toAbsoluteIndex(end === undefined ? length : end, length);
		    // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
		    var Constructor, result, n;
		    if (isArray(O)) {
		      Constructor = O.constructor;
		      // cross-realm fallback
		      if (isConstructor(Constructor) && (Constructor === $Array || isArray(Constructor.prototype))) {
		        Constructor = undefined;
		      } else if (isObject(Constructor)) {
		        Constructor = Constructor[SPECIES];
		        if (Constructor === null) Constructor = undefined;
		      }
		      if (Constructor === $Array || Constructor === undefined) {
		        return nativeSlice(O, k, fin);
		      }
		    }
		    result = new (Constructor === undefined ? $Array : Constructor)(max(fin - k, 0));
		    for (n = 0; k < fin; k++, n++) if (k in O) createProperty(result, n, O[k]);
		    result.length = n;
		    return result;
		  }
		});
		return es_array_slice;
	}

	var getBuiltInPrototypeMethod;
	var hasRequiredGetBuiltInPrototypeMethod;

	function requireGetBuiltInPrototypeMethod () {
		if (hasRequiredGetBuiltInPrototypeMethod) return getBuiltInPrototypeMethod;
		hasRequiredGetBuiltInPrototypeMethod = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var path = /*@__PURE__*/ requirePath();

		getBuiltInPrototypeMethod = function (CONSTRUCTOR, METHOD) {
		  var Namespace = path[CONSTRUCTOR + 'Prototype'];
		  var pureMethod = Namespace && Namespace[METHOD];
		  if (pureMethod) return pureMethod;
		  var NativeConstructor = globalThis[CONSTRUCTOR];
		  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
		  return NativePrototype && NativePrototype[METHOD];
		};
		return getBuiltInPrototypeMethod;
	}

	var slice$3;
	var hasRequiredSlice$3;

	function requireSlice$3 () {
		if (hasRequiredSlice$3) return slice$3;
		hasRequiredSlice$3 = 1;
		requireEs_array_slice();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod();

		slice$3 = getBuiltInPrototypeMethod('Array', 'slice');
		return slice$3;
	}

	var slice$2;
	var hasRequiredSlice$2;

	function requireSlice$2 () {
		if (hasRequiredSlice$2) return slice$2;
		hasRequiredSlice$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf();
		var method = /*@__PURE__*/ requireSlice$3();

		var ArrayPrototype = Array.prototype;

		slice$2 = function (it) {
		  var own = it.slice;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.slice) ? method : own;
		};
		return slice$2;
	}

	var slice$1;
	var hasRequiredSlice$1;

	function requireSlice$1 () {
		if (hasRequiredSlice$1) return slice$1;
		hasRequiredSlice$1 = 1;
		var parent = /*@__PURE__*/ requireSlice$2();

		slice$1 = parent;
		return slice$1;
	}

	var slice;
	var hasRequiredSlice;

	function requireSlice () {
		if (hasRequiredSlice) return slice;
		hasRequiredSlice = 1;
		slice = /*@__PURE__*/ requireSlice$1();
		return slice;
	}

	var sliceExports = requireSlice();
	var _sliceInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(sliceExports);

	var es_array_filter = {};

	var arraySpeciesConstructor;
	var hasRequiredArraySpeciesConstructor;

	function requireArraySpeciesConstructor () {
		if (hasRequiredArraySpeciesConstructor) return arraySpeciesConstructor;
		hasRequiredArraySpeciesConstructor = 1;
		var isArray = /*@__PURE__*/ requireIsArray();
		var isConstructor = /*@__PURE__*/ requireIsConstructor();
		var isObject = /*@__PURE__*/ requireIsObject();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var SPECIES = wellKnownSymbol('species');
		var $Array = Array;

		// a part of `ArraySpeciesCreate` abstract operation
		// https://tc39.es/ecma262/#sec-arrayspeciescreate
		arraySpeciesConstructor = function (originalArray) {
		  var C;
		  if (isArray(originalArray)) {
		    C = originalArray.constructor;
		    // cross-realm fallback
		    if (isConstructor(C) && (C === $Array || isArray(C.prototype))) C = undefined;
		    else if (isObject(C)) {
		      C = C[SPECIES];
		      if (C === null) C = undefined;
		    }
		  } return C === undefined ? $Array : C;
		};
		return arraySpeciesConstructor;
	}

	var arraySpeciesCreate;
	var hasRequiredArraySpeciesCreate;

	function requireArraySpeciesCreate () {
		if (hasRequiredArraySpeciesCreate) return arraySpeciesCreate;
		hasRequiredArraySpeciesCreate = 1;
		var arraySpeciesConstructor = /*@__PURE__*/ requireArraySpeciesConstructor();

		// `ArraySpeciesCreate` abstract operation
		// https://tc39.es/ecma262/#sec-arrayspeciescreate
		arraySpeciesCreate = function (originalArray, length) {
		  return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
		};
		return arraySpeciesCreate;
	}

	var arrayIteration;
	var hasRequiredArrayIteration;

	function requireArrayIteration () {
		if (hasRequiredArrayIteration) return arrayIteration;
		hasRequiredArrayIteration = 1;
		var bind = /*@__PURE__*/ requireFunctionBindContext();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var IndexedObject = /*@__PURE__*/ requireIndexedObject();
		var toObject = /*@__PURE__*/ requireToObject();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike();
		var arraySpeciesCreate = /*@__PURE__*/ requireArraySpeciesCreate();

		var push = uncurryThis([].push);

		// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
		var createMethod = function (TYPE) {
		  var IS_MAP = TYPE === 1;
		  var IS_FILTER = TYPE === 2;
		  var IS_SOME = TYPE === 3;
		  var IS_EVERY = TYPE === 4;
		  var IS_FIND_INDEX = TYPE === 6;
		  var IS_FILTER_REJECT = TYPE === 7;
		  var NO_HOLES = TYPE === 5 || IS_FIND_INDEX;
		  return function ($this, callbackfn, that, specificCreate) {
		    var O = toObject($this);
		    var self = IndexedObject(O);
		    var length = lengthOfArrayLike(self);
		    var boundFunction = bind(callbackfn, that);
		    var index = 0;
		    var create = specificCreate || arraySpeciesCreate;
		    var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_REJECT ? create($this, 0) : undefined;
		    var value, result;
		    for (;length > index; index++) if (NO_HOLES || index in self) {
		      value = self[index];
		      result = boundFunction(value, index, O);
		      if (TYPE) {
		        if (IS_MAP) target[index] = result; // map
		        else if (result) switch (TYPE) {
		          case 3: return true;              // some
		          case 5: return value;             // find
		          case 6: return index;             // findIndex
		          case 2: push(target, value);      // filter
		        } else switch (TYPE) {
		          case 4: return false;             // every
		          case 7: push(target, value);      // filterReject
		        }
		      }
		    }
		    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
		  };
		};

		arrayIteration = {
		  // `Array.prototype.forEach` method
		  // https://tc39.es/ecma262/#sec-array.prototype.foreach
		  forEach: createMethod(0),
		  // `Array.prototype.map` method
		  // https://tc39.es/ecma262/#sec-array.prototype.map
		  map: createMethod(1),
		  // `Array.prototype.filter` method
		  // https://tc39.es/ecma262/#sec-array.prototype.filter
		  filter: createMethod(2),
		  // `Array.prototype.some` method
		  // https://tc39.es/ecma262/#sec-array.prototype.some
		  some: createMethod(3),
		  // `Array.prototype.every` method
		  // https://tc39.es/ecma262/#sec-array.prototype.every
		  every: createMethod(4),
		  // `Array.prototype.find` method
		  // https://tc39.es/ecma262/#sec-array.prototype.find
		  find: createMethod(5),
		  // `Array.prototype.findIndex` method
		  // https://tc39.es/ecma262/#sec-array.prototype.findIndex
		  findIndex: createMethod(6),
		  // `Array.prototype.filterReject` method
		  // https://github.com/tc39/proposal-array-filtering
		  filterReject: createMethod(7)
		};
		return arrayIteration;
	}

	var hasRequiredEs_array_filter;

	function requireEs_array_filter () {
		if (hasRequiredEs_array_filter) return es_array_filter;
		hasRequiredEs_array_filter = 1;
		var $ = /*@__PURE__*/ require_export();
		var $filter = /*@__PURE__*/ requireArrayIteration().filter;
		var arrayMethodHasSpeciesSupport = /*@__PURE__*/ requireArrayMethodHasSpeciesSupport();

		var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter');

		// `Array.prototype.filter` method
		// https://tc39.es/ecma262/#sec-array.prototype.filter
		// with adding support of @@species
		$({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
		  filter: function filter(callbackfn /* , thisArg */) {
		    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});
		return es_array_filter;
	}

	var filter$3;
	var hasRequiredFilter$3;

	function requireFilter$3 () {
		if (hasRequiredFilter$3) return filter$3;
		hasRequiredFilter$3 = 1;
		requireEs_array_filter();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod();

		filter$3 = getBuiltInPrototypeMethod('Array', 'filter');
		return filter$3;
	}

	var filter$2;
	var hasRequiredFilter$2;

	function requireFilter$2 () {
		if (hasRequiredFilter$2) return filter$2;
		hasRequiredFilter$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf();
		var method = /*@__PURE__*/ requireFilter$3();

		var ArrayPrototype = Array.prototype;

		filter$2 = function (it) {
		  var own = it.filter;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.filter) ? method : own;
		};
		return filter$2;
	}

	var filter$1;
	var hasRequiredFilter$1;

	function requireFilter$1 () {
		if (hasRequiredFilter$1) return filter$1;
		hasRequiredFilter$1 = 1;
		var parent = /*@__PURE__*/ requireFilter$2();

		filter$1 = parent;
		return filter$1;
	}

	var filter;
	var hasRequiredFilter;

	function requireFilter () {
		if (hasRequiredFilter) return filter;
		hasRequiredFilter = 1;
		filter = /*@__PURE__*/ requireFilter$1();
		return filter;
	}

	var filterExports = requireFilter();
	var _filterInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(filterExports);

	var es_parseInt = {};

	var toString;
	var hasRequiredToString;

	function requireToString () {
		if (hasRequiredToString) return toString;
		hasRequiredToString = 1;
		var classof = /*@__PURE__*/ requireClassof();

		var $String = String;

		toString = function (argument) {
		  if (classof(argument) === 'Symbol') throw new TypeError('Cannot convert a Symbol value to a string');
		  return $String(argument);
		};
		return toString;
	}

	var whitespaces;
	var hasRequiredWhitespaces;

	function requireWhitespaces () {
		if (hasRequiredWhitespaces) return whitespaces;
		hasRequiredWhitespaces = 1;
		// a string of all valid unicode whitespaces
		whitespaces = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
		  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';
		return whitespaces;
	}

	var stringTrim;
	var hasRequiredStringTrim;

	function requireStringTrim () {
		if (hasRequiredStringTrim) return stringTrim;
		hasRequiredStringTrim = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible();
		var toString = /*@__PURE__*/ requireToString();
		var whitespaces = /*@__PURE__*/ requireWhitespaces();

		var replace = uncurryThis(''.replace);
		var ltrim = RegExp('^[' + whitespaces + ']+');
		var rtrim = RegExp('(^|[^' + whitespaces + '])[' + whitespaces + ']+$');

		// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
		var createMethod = function (TYPE) {
		  return function ($this) {
		    var string = toString(requireObjectCoercible($this));
		    if (TYPE & 1) string = replace(string, ltrim, '');
		    if (TYPE & 2) string = replace(string, rtrim, '$1');
		    return string;
		  };
		};

		stringTrim = {
		  // `String.prototype.{ trimLeft, trimStart }` methods
		  // https://tc39.es/ecma262/#sec-string.prototype.trimstart
		  start: createMethod(1),
		  // `String.prototype.{ trimRight, trimEnd }` methods
		  // https://tc39.es/ecma262/#sec-string.prototype.trimend
		  end: createMethod(2),
		  // `String.prototype.trim` method
		  // https://tc39.es/ecma262/#sec-string.prototype.trim
		  trim: createMethod(3)
		};
		return stringTrim;
	}

	var numberParseInt;
	var hasRequiredNumberParseInt;

	function requireNumberParseInt () {
		if (hasRequiredNumberParseInt) return numberParseInt;
		hasRequiredNumberParseInt = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var fails = /*@__PURE__*/ requireFails();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var toString = /*@__PURE__*/ requireToString();
		var trim = /*@__PURE__*/ requireStringTrim().trim;
		var whitespaces = /*@__PURE__*/ requireWhitespaces();

		var $parseInt = globalThis.parseInt;
		var Symbol = globalThis.Symbol;
		var ITERATOR = Symbol && Symbol.iterator;
		var hex = /^[+-]?0x/i;
		var exec = uncurryThis(hex.exec);
		var FORCED = $parseInt(whitespaces + '08') !== 8 || $parseInt(whitespaces + '0x16') !== 22
		  // MS Edge 18- broken with boxed symbols
		  || (ITERATOR && !fails(function () { $parseInt(Object(ITERATOR)); }));

		// `parseInt` method
		// https://tc39.es/ecma262/#sec-parseint-string-radix
		numberParseInt = FORCED ? function parseInt(string, radix) {
		  var S = trim(toString(string));
		  return $parseInt(S, (radix >>> 0) || (exec(hex, S) ? 16 : 10));
		} : $parseInt;
		return numberParseInt;
	}

	var hasRequiredEs_parseInt;

	function requireEs_parseInt () {
		if (hasRequiredEs_parseInt) return es_parseInt;
		hasRequiredEs_parseInt = 1;
		var $ = /*@__PURE__*/ require_export();
		var $parseInt = /*@__PURE__*/ requireNumberParseInt();

		// `parseInt` method
		// https://tc39.es/ecma262/#sec-parseint-string-radix
		$({ global: true, forced: parseInt !== $parseInt }, {
		  parseInt: $parseInt
		});
		return es_parseInt;
	}

	var _parseInt$3;
	var hasRequired_parseInt$2;

	function require_parseInt$2 () {
		if (hasRequired_parseInt$2) return _parseInt$3;
		hasRequired_parseInt$2 = 1;
		requireEs_parseInt();
		var path = /*@__PURE__*/ requirePath();

		_parseInt$3 = path.parseInt;
		return _parseInt$3;
	}

	var _parseInt$2;
	var hasRequired_parseInt$1;

	function require_parseInt$1 () {
		if (hasRequired_parseInt$1) return _parseInt$2;
		hasRequired_parseInt$1 = 1;
		var parent = /*@__PURE__*/ require_parseInt$2();

		_parseInt$2 = parent;
		return _parseInt$2;
	}

	var _parseInt$1;
	var hasRequired_parseInt;

	function require_parseInt () {
		if (hasRequired_parseInt) return _parseInt$1;
		hasRequired_parseInt = 1;
		_parseInt$1 = /*@__PURE__*/ require_parseInt$1();
		return _parseInt$1;
	}

	var _parseIntExports = require_parseInt();
	var _parseInt = /*@__PURE__*/getDefaultExportFromCjs(_parseIntExports);

	var es_array_includes = {};

	var arrayIncludes;
	var hasRequiredArrayIncludes;

	function requireArrayIncludes () {
		if (hasRequiredArrayIncludes) return arrayIncludes;
		hasRequiredArrayIncludes = 1;
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject();
		var toAbsoluteIndex = /*@__PURE__*/ requireToAbsoluteIndex();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike();

		// `Array.prototype.{ indexOf, includes }` methods implementation
		var createMethod = function (IS_INCLUDES) {
		  return function ($this, el, fromIndex) {
		    var O = toIndexedObject($this);
		    var length = lengthOfArrayLike(O);
		    if (length === 0) return !IS_INCLUDES && -1;
		    var index = toAbsoluteIndex(fromIndex, length);
		    var value;
		    // Array#includes uses SameValueZero equality algorithm
		    // eslint-disable-next-line no-self-compare -- NaN check
		    if (IS_INCLUDES && el !== el) while (length > index) {
		      value = O[index++];
		      // eslint-disable-next-line no-self-compare -- NaN check
		      if (value !== value) return true;
		    // Array#indexOf ignores holes, Array#includes - not
		    } else for (;length > index; index++) {
		      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
		    } return !IS_INCLUDES && -1;
		  };
		};

		arrayIncludes = {
		  // `Array.prototype.includes` method
		  // https://tc39.es/ecma262/#sec-array.prototype.includes
		  includes: createMethod(true),
		  // `Array.prototype.indexOf` method
		  // https://tc39.es/ecma262/#sec-array.prototype.indexof
		  indexOf: createMethod(false)
		};
		return arrayIncludes;
	}

	var addToUnscopables;
	var hasRequiredAddToUnscopables;

	function requireAddToUnscopables () {
		if (hasRequiredAddToUnscopables) return addToUnscopables;
		hasRequiredAddToUnscopables = 1;
		addToUnscopables = function () { /* empty */ };
		return addToUnscopables;
	}

	var hasRequiredEs_array_includes;

	function requireEs_array_includes () {
		if (hasRequiredEs_array_includes) return es_array_includes;
		hasRequiredEs_array_includes = 1;
		var $ = /*@__PURE__*/ require_export();
		var $includes = /*@__PURE__*/ requireArrayIncludes().includes;
		var fails = /*@__PURE__*/ requireFails();
		var addToUnscopables = /*@__PURE__*/ requireAddToUnscopables();

		// FF99+ bug
		var BROKEN_ON_SPARSE = fails(function () {
		  // eslint-disable-next-line es/no-array-prototype-includes -- detection
		  return !Array(1).includes();
		});

		// `Array.prototype.includes` method
		// https://tc39.es/ecma262/#sec-array.prototype.includes
		$({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
		  includes: function includes(el /* , fromIndex = 0 */) {
		    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
		  }
		});

		// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
		addToUnscopables('includes');
		return es_array_includes;
	}

	var includes$4;
	var hasRequiredIncludes$4;

	function requireIncludes$4 () {
		if (hasRequiredIncludes$4) return includes$4;
		hasRequiredIncludes$4 = 1;
		requireEs_array_includes();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod();

		includes$4 = getBuiltInPrototypeMethod('Array', 'includes');
		return includes$4;
	}

	var es_string_includes = {};

	var isRegexp;
	var hasRequiredIsRegexp;

	function requireIsRegexp () {
		if (hasRequiredIsRegexp) return isRegexp;
		hasRequiredIsRegexp = 1;
		var isObject = /*@__PURE__*/ requireIsObject();
		var classof = /*@__PURE__*/ requireClassofRaw();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var MATCH = wellKnownSymbol('match');

		// `IsRegExp` abstract operation
		// https://tc39.es/ecma262/#sec-isregexp
		isRegexp = function (it) {
		  var isRegExp;
		  return isObject(it) && ((isRegExp = it[MATCH]) !== undefined ? !!isRegExp : classof(it) === 'RegExp');
		};
		return isRegexp;
	}

	var notARegexp;
	var hasRequiredNotARegexp;

	function requireNotARegexp () {
		if (hasRequiredNotARegexp) return notARegexp;
		hasRequiredNotARegexp = 1;
		var isRegExp = /*@__PURE__*/ requireIsRegexp();

		var $TypeError = TypeError;

		notARegexp = function (it) {
		  if (isRegExp(it)) {
		    throw new $TypeError("The method doesn't accept regular expressions");
		  } return it;
		};
		return notARegexp;
	}

	var correctIsRegexpLogic;
	var hasRequiredCorrectIsRegexpLogic;

	function requireCorrectIsRegexpLogic () {
		if (hasRequiredCorrectIsRegexpLogic) return correctIsRegexpLogic;
		hasRequiredCorrectIsRegexpLogic = 1;
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var MATCH = wellKnownSymbol('match');

		correctIsRegexpLogic = function (METHOD_NAME) {
		  var regexp = /./;
		  try {
		    '/./'[METHOD_NAME](regexp);
		  } catch (error1) {
		    try {
		      regexp[MATCH] = false;
		      return '/./'[METHOD_NAME](regexp);
		    } catch (error2) { /* empty */ }
		  } return false;
		};
		return correctIsRegexpLogic;
	}

	var hasRequiredEs_string_includes;

	function requireEs_string_includes () {
		if (hasRequiredEs_string_includes) return es_string_includes;
		hasRequiredEs_string_includes = 1;
		var $ = /*@__PURE__*/ require_export();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var notARegExp = /*@__PURE__*/ requireNotARegexp();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible();
		var toString = /*@__PURE__*/ requireToString();
		var correctIsRegExpLogic = /*@__PURE__*/ requireCorrectIsRegexpLogic();

		var stringIndexOf = uncurryThis(''.indexOf);

		// `String.prototype.includes` method
		// https://tc39.es/ecma262/#sec-string.prototype.includes
		$({ target: 'String', proto: true, forced: !correctIsRegExpLogic('includes') }, {
		  includes: function includes(searchString /* , position = 0 */) {
		    return !!~stringIndexOf(
		      toString(requireObjectCoercible(this)),
		      toString(notARegExp(searchString)),
		      arguments.length > 1 ? arguments[1] : undefined
		    );
		  }
		});
		return es_string_includes;
	}

	var includes$3;
	var hasRequiredIncludes$3;

	function requireIncludes$3 () {
		if (hasRequiredIncludes$3) return includes$3;
		hasRequiredIncludes$3 = 1;
		requireEs_string_includes();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod();

		includes$3 = getBuiltInPrototypeMethod('String', 'includes');
		return includes$3;
	}

	var includes$2;
	var hasRequiredIncludes$2;

	function requireIncludes$2 () {
		if (hasRequiredIncludes$2) return includes$2;
		hasRequiredIncludes$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf();
		var arrayMethod = /*@__PURE__*/ requireIncludes$4();
		var stringMethod = /*@__PURE__*/ requireIncludes$3();

		var ArrayPrototype = Array.prototype;
		var StringPrototype = String.prototype;

		includes$2 = function (it) {
		  var own = it.includes;
		  if (it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.includes)) return arrayMethod;
		  if (typeof it == 'string' || it === StringPrototype || (isPrototypeOf(StringPrototype, it) && own === StringPrototype.includes)) {
		    return stringMethod;
		  } return own;
		};
		return includes$2;
	}

	var includes$1;
	var hasRequiredIncludes$1;

	function requireIncludes$1 () {
		if (hasRequiredIncludes$1) return includes$1;
		hasRequiredIncludes$1 = 1;
		var parent = /*@__PURE__*/ requireIncludes$2();

		includes$1 = parent;
		return includes$1;
	}

	var includes;
	var hasRequiredIncludes;

	function requireIncludes () {
		if (hasRequiredIncludes) return includes;
		hasRequiredIncludes = 1;
		includes = /*@__PURE__*/ requireIncludes$1();
		return includes;
	}

	var includesExports = requireIncludes();
	var _includesInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(includesExports);

	var iterators;
	var hasRequiredIterators;

	function requireIterators () {
		if (hasRequiredIterators) return iterators;
		hasRequiredIterators = 1;
		iterators = {};
		return iterators;
	}

	var weakMapBasicDetection;
	var hasRequiredWeakMapBasicDetection;

	function requireWeakMapBasicDetection () {
		if (hasRequiredWeakMapBasicDetection) return weakMapBasicDetection;
		hasRequiredWeakMapBasicDetection = 1;
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var isCallable = /*@__PURE__*/ requireIsCallable();

		var WeakMap = globalThis.WeakMap;

		weakMapBasicDetection = isCallable(WeakMap) && /native code/.test(String(WeakMap));
		return weakMapBasicDetection;
	}

	var sharedKey;
	var hasRequiredSharedKey;

	function requireSharedKey () {
		if (hasRequiredSharedKey) return sharedKey;
		hasRequiredSharedKey = 1;
		var shared = /*@__PURE__*/ requireShared();
		var uid = /*@__PURE__*/ requireUid();

		var keys = shared('keys');

		sharedKey = function (key) {
		  return keys[key] || (keys[key] = uid(key));
		};
		return sharedKey;
	}

	var hiddenKeys;
	var hasRequiredHiddenKeys;

	function requireHiddenKeys () {
		if (hasRequiredHiddenKeys) return hiddenKeys;
		hasRequiredHiddenKeys = 1;
		hiddenKeys = {};
		return hiddenKeys;
	}

	var internalState;
	var hasRequiredInternalState;

	function requireInternalState () {
		if (hasRequiredInternalState) return internalState;
		hasRequiredInternalState = 1;
		var NATIVE_WEAK_MAP = /*@__PURE__*/ requireWeakMapBasicDetection();
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var isObject = /*@__PURE__*/ requireIsObject();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();
		var shared = /*@__PURE__*/ requireSharedStore();
		var sharedKey = /*@__PURE__*/ requireSharedKey();
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys();

		var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
		var TypeError = globalThis.TypeError;
		var WeakMap = globalThis.WeakMap;
		var set, get, has;

		var enforce = function (it) {
		  return has(it) ? get(it) : set(it, {});
		};

		var getterFor = function (TYPE) {
		  return function (it) {
		    var state;
		    if (!isObject(it) || (state = get(it)).type !== TYPE) {
		      throw new TypeError('Incompatible receiver, ' + TYPE + ' required');
		    } return state;
		  };
		};

		if (NATIVE_WEAK_MAP || shared.state) {
		  var store = shared.state || (shared.state = new WeakMap());
		  /* eslint-disable no-self-assign -- prototype methods protection */
		  store.get = store.get;
		  store.has = store.has;
		  store.set = store.set;
		  /* eslint-enable no-self-assign -- prototype methods protection */
		  set = function (it, metadata) {
		    if (store.has(it)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
		    metadata.facade = it;
		    store.set(it, metadata);
		    return metadata;
		  };
		  get = function (it) {
		    return store.get(it) || {};
		  };
		  has = function (it) {
		    return store.has(it);
		  };
		} else {
		  var STATE = sharedKey('state');
		  hiddenKeys[STATE] = true;
		  set = function (it, metadata) {
		    if (hasOwn(it, STATE)) throw new TypeError(OBJECT_ALREADY_INITIALIZED);
		    metadata.facade = it;
		    createNonEnumerableProperty(it, STATE, metadata);
		    return metadata;
		  };
		  get = function (it) {
		    return hasOwn(it, STATE) ? it[STATE] : {};
		  };
		  has = function (it) {
		    return hasOwn(it, STATE);
		  };
		}

		internalState = {
		  set: set,
		  get: get,
		  has: has,
		  enforce: enforce,
		  getterFor: getterFor
		};
		return internalState;
	}

	var functionName;
	var hasRequiredFunctionName;

	function requireFunctionName () {
		if (hasRequiredFunctionName) return functionName;
		hasRequiredFunctionName = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();

		var FunctionPrototype = Function.prototype;
		// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		var getDescriptor = DESCRIPTORS && Object.getOwnPropertyDescriptor;

		var EXISTS = hasOwn(FunctionPrototype, 'name');
		// additional protection from minified / mangled / dropped function names
		var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
		var CONFIGURABLE = EXISTS && (!DESCRIPTORS || (DESCRIPTORS && getDescriptor(FunctionPrototype, 'name').configurable));

		functionName = {
		  EXISTS: EXISTS,
		  PROPER: PROPER,
		  CONFIGURABLE: CONFIGURABLE
		};
		return functionName;
	}

	var objectDefineProperties = {};

	var objectKeysInternal;
	var hasRequiredObjectKeysInternal;

	function requireObjectKeysInternal () {
		if (hasRequiredObjectKeysInternal) return objectKeysInternal;
		hasRequiredObjectKeysInternal = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject();
		var indexOf = /*@__PURE__*/ requireArrayIncludes().indexOf;
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys();

		var push = uncurryThis([].push);

		objectKeysInternal = function (object, names) {
		  var O = toIndexedObject(object);
		  var i = 0;
		  var result = [];
		  var key;
		  for (key in O) !hasOwn(hiddenKeys, key) && hasOwn(O, key) && push(result, key);
		  // Don't enum bug & hidden keys
		  while (names.length > i) if (hasOwn(O, key = names[i++])) {
		    ~indexOf(result, key) || push(result, key);
		  }
		  return result;
		};
		return objectKeysInternal;
	}

	var enumBugKeys;
	var hasRequiredEnumBugKeys;

	function requireEnumBugKeys () {
		if (hasRequiredEnumBugKeys) return enumBugKeys;
		hasRequiredEnumBugKeys = 1;
		// IE8- don't enum bug keys
		enumBugKeys = [
		  'constructor',
		  'hasOwnProperty',
		  'isPrototypeOf',
		  'propertyIsEnumerable',
		  'toLocaleString',
		  'toString',
		  'valueOf'
		];
		return enumBugKeys;
	}

	var objectKeys;
	var hasRequiredObjectKeys;

	function requireObjectKeys () {
		if (hasRequiredObjectKeys) return objectKeys;
		hasRequiredObjectKeys = 1;
		var internalObjectKeys = /*@__PURE__*/ requireObjectKeysInternal();
		var enumBugKeys = /*@__PURE__*/ requireEnumBugKeys();

		// `Object.keys` method
		// https://tc39.es/ecma262/#sec-object.keys
		// eslint-disable-next-line es/no-object-keys -- safe
		objectKeys = Object.keys || function keys(O) {
		  return internalObjectKeys(O, enumBugKeys);
		};
		return objectKeys;
	}

	var hasRequiredObjectDefineProperties;

	function requireObjectDefineProperties () {
		if (hasRequiredObjectDefineProperties) return objectDefineProperties;
		hasRequiredObjectDefineProperties = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var V8_PROTOTYPE_DEFINE_BUG = /*@__PURE__*/ requireV8PrototypeDefineBug();
		var definePropertyModule = /*@__PURE__*/ requireObjectDefineProperty();
		var anObject = /*@__PURE__*/ requireAnObject();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject();
		var objectKeys = /*@__PURE__*/ requireObjectKeys();

		// `Object.defineProperties` method
		// https://tc39.es/ecma262/#sec-object.defineproperties
		// eslint-disable-next-line es/no-object-defineproperties -- safe
		objectDefineProperties.f = DESCRIPTORS && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
		  anObject(O);
		  var props = toIndexedObject(Properties);
		  var keys = objectKeys(Properties);
		  var length = keys.length;
		  var index = 0;
		  var key;
		  while (length > index) definePropertyModule.f(O, key = keys[index++], props[key]);
		  return O;
		};
		return objectDefineProperties;
	}

	var html;
	var hasRequiredHtml;

	function requireHtml () {
		if (hasRequiredHtml) return html;
		hasRequiredHtml = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn();

		html = getBuiltIn('document', 'documentElement');
		return html;
	}

	var objectCreate;
	var hasRequiredObjectCreate;

	function requireObjectCreate () {
		if (hasRequiredObjectCreate) return objectCreate;
		hasRequiredObjectCreate = 1;
		/* global ActiveXObject -- old IE, WSH */
		var anObject = /*@__PURE__*/ requireAnObject();
		var definePropertiesModule = /*@__PURE__*/ requireObjectDefineProperties();
		var enumBugKeys = /*@__PURE__*/ requireEnumBugKeys();
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys();
		var html = /*@__PURE__*/ requireHtml();
		var documentCreateElement = /*@__PURE__*/ requireDocumentCreateElement();
		var sharedKey = /*@__PURE__*/ requireSharedKey();

		var GT = '>';
		var LT = '<';
		var PROTOTYPE = 'prototype';
		var SCRIPT = 'script';
		var IE_PROTO = sharedKey('IE_PROTO');

		var EmptyConstructor = function () { /* empty */ };

		var scriptTag = function (content) {
		  return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
		};

		// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
		var NullProtoObjectViaActiveX = function (activeXDocument) {
		  activeXDocument.write(scriptTag(''));
		  activeXDocument.close();
		  var temp = activeXDocument.parentWindow.Object;
		  // eslint-disable-next-line no-useless-assignment -- avoid memory leak
		  activeXDocument = null;
		  return temp;
		};

		// Create object with fake `null` prototype: use iframe Object with cleared prototype
		var NullProtoObjectViaIFrame = function () {
		  // Thrash, waste and sodomy: IE GC bug
		  var iframe = documentCreateElement('iframe');
		  var JS = 'java' + SCRIPT + ':';
		  var iframeDocument;
		  iframe.style.display = 'none';
		  html.appendChild(iframe);
		  // https://github.com/zloirock/core-js/issues/475
		  iframe.src = String(JS);
		  iframeDocument = iframe.contentWindow.document;
		  iframeDocument.open();
		  iframeDocument.write(scriptTag('document.F=Object'));
		  iframeDocument.close();
		  return iframeDocument.F;
		};

		// Check for document.domain and active x support
		// No need to use active x approach when document.domain is not set
		// see https://github.com/es-shims/es5-shim/issues/150
		// variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
		// avoid IE GC bug
		var activeXDocument;
		var NullProtoObject = function () {
		  try {
		    activeXDocument = new ActiveXObject('htmlfile');
		  } catch (error) { /* ignore */ }
		  NullProtoObject = typeof document != 'undefined'
		    ? document.domain && activeXDocument
		      ? NullProtoObjectViaActiveX(activeXDocument) // old IE
		      : NullProtoObjectViaIFrame()
		    : NullProtoObjectViaActiveX(activeXDocument); // WSH
		  var length = enumBugKeys.length;
		  while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys[length]];
		  return NullProtoObject();
		};

		hiddenKeys[IE_PROTO] = true;

		// `Object.create` method
		// https://tc39.es/ecma262/#sec-object.create
		// eslint-disable-next-line es/no-object-create -- safe
		objectCreate = Object.create || function create(O, Properties) {
		  var result;
		  if (O !== null) {
		    EmptyConstructor[PROTOTYPE] = anObject(O);
		    result = new EmptyConstructor();
		    EmptyConstructor[PROTOTYPE] = null;
		    // add "__proto__" for Object.getPrototypeOf polyfill
		    result[IE_PROTO] = O;
		  } else result = NullProtoObject();
		  return Properties === undefined ? result : definePropertiesModule.f(result, Properties);
		};
		return objectCreate;
	}

	var correctPrototypeGetter;
	var hasRequiredCorrectPrototypeGetter;

	function requireCorrectPrototypeGetter () {
		if (hasRequiredCorrectPrototypeGetter) return correctPrototypeGetter;
		hasRequiredCorrectPrototypeGetter = 1;
		var fails = /*@__PURE__*/ requireFails();

		correctPrototypeGetter = !fails(function () {
		  function F() { /* empty */ }
		  F.prototype.constructor = null;
		  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
		  return Object.getPrototypeOf(new F()) !== F.prototype;
		});
		return correctPrototypeGetter;
	}

	var objectGetPrototypeOf;
	var hasRequiredObjectGetPrototypeOf;

	function requireObjectGetPrototypeOf () {
		if (hasRequiredObjectGetPrototypeOf) return objectGetPrototypeOf;
		hasRequiredObjectGetPrototypeOf = 1;
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var toObject = /*@__PURE__*/ requireToObject();
		var sharedKey = /*@__PURE__*/ requireSharedKey();
		var CORRECT_PROTOTYPE_GETTER = /*@__PURE__*/ requireCorrectPrototypeGetter();

		var IE_PROTO = sharedKey('IE_PROTO');
		var $Object = Object;
		var ObjectPrototype = $Object.prototype;

		// `Object.getPrototypeOf` method
		// https://tc39.es/ecma262/#sec-object.getprototypeof
		// eslint-disable-next-line es/no-object-getprototypeof -- safe
		objectGetPrototypeOf = CORRECT_PROTOTYPE_GETTER ? $Object.getPrototypeOf : function (O) {
		  var object = toObject(O);
		  if (hasOwn(object, IE_PROTO)) return object[IE_PROTO];
		  var constructor = object.constructor;
		  if (isCallable(constructor) && object instanceof constructor) {
		    return constructor.prototype;
		  } return object instanceof $Object ? ObjectPrototype : null;
		};
		return objectGetPrototypeOf;
	}

	var defineBuiltIn;
	var hasRequiredDefineBuiltIn;

	function requireDefineBuiltIn () {
		if (hasRequiredDefineBuiltIn) return defineBuiltIn;
		hasRequiredDefineBuiltIn = 1;
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty();

		defineBuiltIn = function (target, key, value, options) {
		  if (options && options.enumerable) target[key] = value;
		  else createNonEnumerableProperty(target, key, value);
		  return target;
		};
		return defineBuiltIn;
	}

	var iteratorsCore;
	var hasRequiredIteratorsCore;

	function requireIteratorsCore () {
		if (hasRequiredIteratorsCore) return iteratorsCore;
		hasRequiredIteratorsCore = 1;
		var fails = /*@__PURE__*/ requireFails();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var isObject = /*@__PURE__*/ requireIsObject();
		var create = /*@__PURE__*/ requireObjectCreate();
		var getPrototypeOf = /*@__PURE__*/ requireObjectGetPrototypeOf();
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();
		var IS_PURE = /*@__PURE__*/ requireIsPure();

		var ITERATOR = wellKnownSymbol('iterator');
		var BUGGY_SAFARI_ITERATORS = false;

		// `%IteratorPrototype%` object
		// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
		var IteratorPrototype, PrototypeOfArrayIteratorPrototype, arrayIterator;

		/* eslint-disable es/no-array-prototype-keys -- safe */
		if ([].keys) {
		  arrayIterator = [].keys();
		  // Safari 8 has buggy iterators w/o `next`
		  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS = true;
		  else {
		    PrototypeOfArrayIteratorPrototype = getPrototypeOf(getPrototypeOf(arrayIterator));
		    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype = PrototypeOfArrayIteratorPrototype;
		  }
		}

		var NEW_ITERATOR_PROTOTYPE = !isObject(IteratorPrototype) || fails(function () {
		  var test = {};
		  // FF44- legacy iterators case
		  return IteratorPrototype[ITERATOR].call(test) !== test;
		});

		if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype = {};
		else if (IS_PURE) IteratorPrototype = create(IteratorPrototype);

		// `%IteratorPrototype%[@@iterator]()` method
		// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
		if (!isCallable(IteratorPrototype[ITERATOR])) {
		  defineBuiltIn(IteratorPrototype, ITERATOR, function () {
		    return this;
		  });
		}

		iteratorsCore = {
		  IteratorPrototype: IteratorPrototype,
		  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS
		};
		return iteratorsCore;
	}

	var objectToString;
	var hasRequiredObjectToString;

	function requireObjectToString () {
		if (hasRequiredObjectToString) return objectToString;
		hasRequiredObjectToString = 1;
		var TO_STRING_TAG_SUPPORT = /*@__PURE__*/ requireToStringTagSupport();
		var classof = /*@__PURE__*/ requireClassof();

		// `Object.prototype.toString` method implementation
		// https://tc39.es/ecma262/#sec-object.prototype.tostring
		objectToString = TO_STRING_TAG_SUPPORT ? {}.toString : function toString() {
		  return '[object ' + classof(this) + ']';
		};
		return objectToString;
	}

	var setToStringTag;
	var hasRequiredSetToStringTag;

	function requireSetToStringTag () {
		if (hasRequiredSetToStringTag) return setToStringTag;
		hasRequiredSetToStringTag = 1;
		var TO_STRING_TAG_SUPPORT = /*@__PURE__*/ requireToStringTagSupport();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty().f;
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();
		var toString = /*@__PURE__*/ requireObjectToString();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var TO_STRING_TAG = wellKnownSymbol('toStringTag');

		setToStringTag = function (it, TAG, STATIC, SET_METHOD) {
		  var target = STATIC ? it : it && it.prototype;
		  if (target) {
		    if (!hasOwn(target, TO_STRING_TAG)) {
		      defineProperty(target, TO_STRING_TAG, { configurable: true, value: TAG });
		    }
		    if (SET_METHOD && !TO_STRING_TAG_SUPPORT) {
		      createNonEnumerableProperty(target, 'toString', toString);
		    }
		  }
		};
		return setToStringTag;
	}

	var iteratorCreateConstructor;
	var hasRequiredIteratorCreateConstructor;

	function requireIteratorCreateConstructor () {
		if (hasRequiredIteratorCreateConstructor) return iteratorCreateConstructor;
		hasRequiredIteratorCreateConstructor = 1;
		var IteratorPrototype = /*@__PURE__*/ requireIteratorsCore().IteratorPrototype;
		var create = /*@__PURE__*/ requireObjectCreate();
		var createPropertyDescriptor = /*@__PURE__*/ requireCreatePropertyDescriptor();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag();
		var Iterators = /*@__PURE__*/ requireIterators();

		var returnThis = function () { return this; };

		iteratorCreateConstructor = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
		  var TO_STRING_TAG = NAME + ' Iterator';
		  IteratorConstructor.prototype = create(IteratorPrototype, { next: createPropertyDescriptor(+!ENUMERABLE_NEXT, next) });
		  setToStringTag(IteratorConstructor, TO_STRING_TAG, false, true);
		  Iterators[TO_STRING_TAG] = returnThis;
		  return IteratorConstructor;
		};
		return iteratorCreateConstructor;
	}

	var functionUncurryThisAccessor;
	var hasRequiredFunctionUncurryThisAccessor;

	function requireFunctionUncurryThisAccessor () {
		if (hasRequiredFunctionUncurryThisAccessor) return functionUncurryThisAccessor;
		hasRequiredFunctionUncurryThisAccessor = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var aCallable = /*@__PURE__*/ requireACallable();

		functionUncurryThisAccessor = function (object, key, method) {
		  try {
		    // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
		    return uncurryThis(aCallable(Object.getOwnPropertyDescriptor(object, key)[method]));
		  } catch (error) { /* empty */ }
		};
		return functionUncurryThisAccessor;
	}

	var isPossiblePrototype;
	var hasRequiredIsPossiblePrototype;

	function requireIsPossiblePrototype () {
		if (hasRequiredIsPossiblePrototype) return isPossiblePrototype;
		hasRequiredIsPossiblePrototype = 1;
		var isObject = /*@__PURE__*/ requireIsObject();

		isPossiblePrototype = function (argument) {
		  return isObject(argument) || argument === null;
		};
		return isPossiblePrototype;
	}

	var aPossiblePrototype;
	var hasRequiredAPossiblePrototype;

	function requireAPossiblePrototype () {
		if (hasRequiredAPossiblePrototype) return aPossiblePrototype;
		hasRequiredAPossiblePrototype = 1;
		var isPossiblePrototype = /*@__PURE__*/ requireIsPossiblePrototype();

		var $String = String;
		var $TypeError = TypeError;

		aPossiblePrototype = function (argument) {
		  if (isPossiblePrototype(argument)) return argument;
		  throw new $TypeError("Can't set " + $String(argument) + ' as a prototype');
		};
		return aPossiblePrototype;
	}

	var objectSetPrototypeOf;
	var hasRequiredObjectSetPrototypeOf;

	function requireObjectSetPrototypeOf () {
		if (hasRequiredObjectSetPrototypeOf) return objectSetPrototypeOf;
		hasRequiredObjectSetPrototypeOf = 1;
		/* eslint-disable no-proto -- safe */
		var uncurryThisAccessor = /*@__PURE__*/ requireFunctionUncurryThisAccessor();
		var isObject = /*@__PURE__*/ requireIsObject();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible();
		var aPossiblePrototype = /*@__PURE__*/ requireAPossiblePrototype();

		// `Object.setPrototypeOf` method
		// https://tc39.es/ecma262/#sec-object.setprototypeof
		// Works with __proto__ only. Old v8 can't work with null proto objects.
		// eslint-disable-next-line es/no-object-setprototypeof -- safe
		objectSetPrototypeOf = Object.setPrototypeOf || ('__proto__' in {} ? function () {
		  var CORRECT_SETTER = false;
		  var test = {};
		  var setter;
		  try {
		    setter = uncurryThisAccessor(Object.prototype, '__proto__', 'set');
		    setter(test, []);
		    CORRECT_SETTER = test instanceof Array;
		  } catch (error) { /* empty */ }
		  return function setPrototypeOf(O, proto) {
		    requireObjectCoercible(O);
		    aPossiblePrototype(proto);
		    if (!isObject(O)) return O;
		    if (CORRECT_SETTER) setter(O, proto);
		    else O.__proto__ = proto;
		    return O;
		  };
		}() : undefined);
		return objectSetPrototypeOf;
	}

	var iteratorDefine;
	var hasRequiredIteratorDefine;

	function requireIteratorDefine () {
		if (hasRequiredIteratorDefine) return iteratorDefine;
		hasRequiredIteratorDefine = 1;
		var $ = /*@__PURE__*/ require_export();
		var call = /*@__PURE__*/ requireFunctionCall();
		var IS_PURE = /*@__PURE__*/ requireIsPure();
		var FunctionName = /*@__PURE__*/ requireFunctionName();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var createIteratorConstructor = /*@__PURE__*/ requireIteratorCreateConstructor();
		var getPrototypeOf = /*@__PURE__*/ requireObjectGetPrototypeOf();
		var setPrototypeOf = /*@__PURE__*/ requireObjectSetPrototypeOf();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty();
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();
		var Iterators = /*@__PURE__*/ requireIterators();
		var IteratorsCore = /*@__PURE__*/ requireIteratorsCore();

		var PROPER_FUNCTION_NAME = FunctionName.PROPER;
		var CONFIGURABLE_FUNCTION_NAME = FunctionName.CONFIGURABLE;
		var IteratorPrototype = IteratorsCore.IteratorPrototype;
		var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
		var ITERATOR = wellKnownSymbol('iterator');
		var KEYS = 'keys';
		var VALUES = 'values';
		var ENTRIES = 'entries';

		var returnThis = function () { return this; };

		iteratorDefine = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
		  createIteratorConstructor(IteratorConstructor, NAME, next);

		  var getIterationMethod = function (KIND) {
		    if (KIND === DEFAULT && defaultIterator) return defaultIterator;
		    if (!BUGGY_SAFARI_ITERATORS && KIND && KIND in IterablePrototype) return IterablePrototype[KIND];

		    switch (KIND) {
		      case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
		      case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
		      case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
		    }

		    return function () { return new IteratorConstructor(this); };
		  };

		  var TO_STRING_TAG = NAME + ' Iterator';
		  var INCORRECT_VALUES_NAME = false;
		  var IterablePrototype = Iterable.prototype;
		  var nativeIterator = IterablePrototype[ITERATOR]
		    || IterablePrototype['@@iterator']
		    || DEFAULT && IterablePrototype[DEFAULT];
		  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
		  var anyNativeIterator = NAME === 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
		  var CurrentIteratorPrototype, methods, KEY;

		  // fix native
		  if (anyNativeIterator) {
		    CurrentIteratorPrototype = getPrototypeOf(anyNativeIterator.call(new Iterable()));
		    if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
		      if (!IS_PURE && getPrototypeOf(CurrentIteratorPrototype) !== IteratorPrototype) {
		        if (setPrototypeOf) {
		          setPrototypeOf(CurrentIteratorPrototype, IteratorPrototype);
		        } else if (!isCallable(CurrentIteratorPrototype[ITERATOR])) {
		          defineBuiltIn(CurrentIteratorPrototype, ITERATOR, returnThis);
		        }
		      }
		      // Set @@toStringTag to native iterators
		      setToStringTag(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
		      if (IS_PURE) Iterators[TO_STRING_TAG] = returnThis;
		    }
		  }

		  // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
		  if (PROPER_FUNCTION_NAME && DEFAULT === VALUES && nativeIterator && nativeIterator.name !== VALUES) {
		    if (!IS_PURE && CONFIGURABLE_FUNCTION_NAME) {
		      createNonEnumerableProperty(IterablePrototype, 'name', VALUES);
		    } else {
		      INCORRECT_VALUES_NAME = true;
		      defaultIterator = function values() { return call(nativeIterator, this); };
		    }
		  }

		  // export additional methods
		  if (DEFAULT) {
		    methods = {
		      values: getIterationMethod(VALUES),
		      keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
		      entries: getIterationMethod(ENTRIES)
		    };
		    if (FORCED) for (KEY in methods) {
		      if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
		        defineBuiltIn(IterablePrototype, KEY, methods[KEY]);
		      }
		    } else $({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
		  }

		  // define iterator
		  if ((!IS_PURE || FORCED) && IterablePrototype[ITERATOR] !== defaultIterator) {
		    defineBuiltIn(IterablePrototype, ITERATOR, defaultIterator, { name: DEFAULT });
		  }
		  Iterators[NAME] = defaultIterator;

		  return methods;
		};
		return iteratorDefine;
	}

	var createIterResultObject;
	var hasRequiredCreateIterResultObject;

	function requireCreateIterResultObject () {
		if (hasRequiredCreateIterResultObject) return createIterResultObject;
		hasRequiredCreateIterResultObject = 1;
		// `CreateIterResultObject` abstract operation
		// https://tc39.es/ecma262/#sec-createiterresultobject
		createIterResultObject = function (value, done) {
		  return { value: value, done: done };
		};
		return createIterResultObject;
	}

	var es_array_iterator;
	var hasRequiredEs_array_iterator;

	function requireEs_array_iterator () {
		if (hasRequiredEs_array_iterator) return es_array_iterator;
		hasRequiredEs_array_iterator = 1;
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject();
		var addToUnscopables = /*@__PURE__*/ requireAddToUnscopables();
		var Iterators = /*@__PURE__*/ requireIterators();
		var InternalStateModule = /*@__PURE__*/ requireInternalState();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty().f;
		var defineIterator = /*@__PURE__*/ requireIteratorDefine();
		var createIterResultObject = /*@__PURE__*/ requireCreateIterResultObject();
		var IS_PURE = /*@__PURE__*/ requireIsPure();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();

		var ARRAY_ITERATOR = 'Array Iterator';
		var setInternalState = InternalStateModule.set;
		var getInternalState = InternalStateModule.getterFor(ARRAY_ITERATOR);

		// `Array.prototype.entries` method
		// https://tc39.es/ecma262/#sec-array.prototype.entries
		// `Array.prototype.keys` method
		// https://tc39.es/ecma262/#sec-array.prototype.keys
		// `Array.prototype.values` method
		// https://tc39.es/ecma262/#sec-array.prototype.values
		// `Array.prototype[@@iterator]` method
		// https://tc39.es/ecma262/#sec-array.prototype-@@iterator
		// `CreateArrayIterator` internal method
		// https://tc39.es/ecma262/#sec-createarrayiterator
		es_array_iterator = defineIterator(Array, 'Array', function (iterated, kind) {
		  setInternalState(this, {
		    type: ARRAY_ITERATOR,
		    target: toIndexedObject(iterated), // target
		    index: 0,                          // next index
		    kind: kind                         // kind
		  });
		// `%ArrayIteratorPrototype%.next` method
		// https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
		}, function () {
		  var state = getInternalState(this);
		  var target = state.target;
		  var index = state.index++;
		  if (!target || index >= target.length) {
		    state.target = null;
		    return createIterResultObject(undefined, true);
		  }
		  switch (state.kind) {
		    case 'keys': return createIterResultObject(index, false);
		    case 'values': return createIterResultObject(target[index], false);
		  } return createIterResultObject([index, target[index]], false);
		}, 'values');

		// argumentsList[@@iterator] is %ArrayProto_values%
		// https://tc39.es/ecma262/#sec-createunmappedargumentsobject
		// https://tc39.es/ecma262/#sec-createmappedargumentsobject
		var values = Iterators.Arguments = Iterators.Array;

		// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
		addToUnscopables('keys');
		addToUnscopables('values');
		addToUnscopables('entries');

		// V8 ~ Chrome 45- bug
		if (!IS_PURE && DESCRIPTORS && values.name !== 'values') try {
		  defineProperty(values, 'name', { value: 'values' });
		} catch (error) { /* empty */ }
		return es_array_iterator;
	}

	var es_set$1 = {};

	var es_set_constructor$1 = {};

	var internalMetadata$1 = {exports: {}};

	var objectGetOwnPropertyNames = {};

	var hasRequiredObjectGetOwnPropertyNames;

	function requireObjectGetOwnPropertyNames () {
		if (hasRequiredObjectGetOwnPropertyNames) return objectGetOwnPropertyNames;
		hasRequiredObjectGetOwnPropertyNames = 1;
		var internalObjectKeys = /*@__PURE__*/ requireObjectKeysInternal();
		var enumBugKeys = /*@__PURE__*/ requireEnumBugKeys();

		var hiddenKeys = enumBugKeys.concat('length', 'prototype');

		// `Object.getOwnPropertyNames` method
		// https://tc39.es/ecma262/#sec-object.getownpropertynames
		// eslint-disable-next-line es/no-object-getownpropertynames -- safe
		objectGetOwnPropertyNames.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
		  return internalObjectKeys(O, hiddenKeys);
		};
		return objectGetOwnPropertyNames;
	}

	var objectGetOwnPropertyNamesExternal$1 = {};

	var hasRequiredObjectGetOwnPropertyNamesExternal$1;

	function requireObjectGetOwnPropertyNamesExternal$1 () {
		if (hasRequiredObjectGetOwnPropertyNamesExternal$1) return objectGetOwnPropertyNamesExternal$1;
		hasRequiredObjectGetOwnPropertyNamesExternal$1 = 1;
		/* eslint-disable es/no-object-getownpropertynames -- safe */
		var classof = /*@__PURE__*/ requireClassofRaw();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject();
		var $getOwnPropertyNames = /*@__PURE__*/ requireObjectGetOwnPropertyNames().f;
		var arraySlice = /*@__PURE__*/ requireArraySlice();

		var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
		  ? Object.getOwnPropertyNames(window) : [];

		var getWindowNames = function (it) {
		  try {
		    return $getOwnPropertyNames(it);
		  } catch (error) {
		    return arraySlice(windowNames);
		  }
		};

		// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
		objectGetOwnPropertyNamesExternal$1.f = function getOwnPropertyNames(it) {
		  return windowNames && classof(it) === 'Window'
		    ? getWindowNames(it)
		    : $getOwnPropertyNames(toIndexedObject(it));
		};
		return objectGetOwnPropertyNamesExternal$1;
	}

	var arrayBufferNonExtensible$1;
	var hasRequiredArrayBufferNonExtensible$1;

	function requireArrayBufferNonExtensible$1 () {
		if (hasRequiredArrayBufferNonExtensible$1) return arrayBufferNonExtensible$1;
		hasRequiredArrayBufferNonExtensible$1 = 1;
		// FF26- bug: ArrayBuffers are non-extensible, but Object.isExtensible does not report it
		var fails = /*@__PURE__*/ requireFails();

		arrayBufferNonExtensible$1 = fails(function () {
		  if (typeof ArrayBuffer == 'function') {
		    var buffer = new ArrayBuffer(8);
		    // eslint-disable-next-line es/no-object-isextensible, es/no-object-defineproperty -- safe
		    if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
		  }
		});
		return arrayBufferNonExtensible$1;
	}

	var objectIsExtensible$1;
	var hasRequiredObjectIsExtensible$1;

	function requireObjectIsExtensible$1 () {
		if (hasRequiredObjectIsExtensible$1) return objectIsExtensible$1;
		hasRequiredObjectIsExtensible$1 = 1;
		var fails = /*@__PURE__*/ requireFails();
		var isObject = /*@__PURE__*/ requireIsObject();
		var classof = /*@__PURE__*/ requireClassofRaw();
		var ARRAY_BUFFER_NON_EXTENSIBLE = /*@__PURE__*/ requireArrayBufferNonExtensible$1();

		// eslint-disable-next-line es/no-object-isextensible -- safe
		var $isExtensible = Object.isExtensible;
		var FAILS_ON_PRIMITIVES = fails(function () { });

		// `Object.isExtensible` method
		// https://tc39.es/ecma262/#sec-object.isextensible
		objectIsExtensible$1 = (FAILS_ON_PRIMITIVES || ARRAY_BUFFER_NON_EXTENSIBLE) ? function isExtensible(it) {
		  if (!isObject(it)) return false;
		  if (ARRAY_BUFFER_NON_EXTENSIBLE && classof(it) === 'ArrayBuffer') return false;
		  return $isExtensible ? $isExtensible(it) : true;
		} : $isExtensible;
		return objectIsExtensible$1;
	}

	var freezing$1;
	var hasRequiredFreezing$1;

	function requireFreezing$1 () {
		if (hasRequiredFreezing$1) return freezing$1;
		hasRequiredFreezing$1 = 1;
		var fails = /*@__PURE__*/ requireFails();

		freezing$1 = !fails(function () {
		  // eslint-disable-next-line es/no-object-isextensible, es/no-object-preventextensions -- required for testing
		  return Object.isExtensible(Object.preventExtensions({}));
		});
		return freezing$1;
	}

	var hasRequiredInternalMetadata$1;

	function requireInternalMetadata$1 () {
		if (hasRequiredInternalMetadata$1) return internalMetadata$1.exports;
		hasRequiredInternalMetadata$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys();
		var isObject = /*@__PURE__*/ requireIsObject();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty().f;
		var getOwnPropertyNamesModule = /*@__PURE__*/ requireObjectGetOwnPropertyNames();
		var getOwnPropertyNamesExternalModule = /*@__PURE__*/ requireObjectGetOwnPropertyNamesExternal$1();
		var isExtensible = /*@__PURE__*/ requireObjectIsExtensible$1();
		var uid = /*@__PURE__*/ requireUid();
		var FREEZING = /*@__PURE__*/ requireFreezing$1();

		var REQUIRED = false;
		var METADATA = uid('meta');
		var id = 0;

		var setMetadata = function (it) {
		  defineProperty(it, METADATA, { value: {
		    objectID: 'O' + id++, // object ID
		    weakData: {}          // weak collections IDs
		  } });
		};

		var fastKey = function (it, create) {
		  // return a primitive with prefix
		  if (!isObject(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
		  if (!hasOwn(it, METADATA)) {
		    // can't set metadata to uncaught frozen object
		    if (!isExtensible(it)) return 'F';
		    // not necessary to add metadata
		    if (!create) return 'E';
		    // add missing metadata
		    setMetadata(it);
		  // return object ID
		  } return it[METADATA].objectID;
		};

		var getWeakData = function (it, create) {
		  if (!hasOwn(it, METADATA)) {
		    // can't set metadata to uncaught frozen object
		    if (!isExtensible(it)) return true;
		    // not necessary to add metadata
		    if (!create) return false;
		    // add missing metadata
		    setMetadata(it);
		  // return the store of weak collections IDs
		  } return it[METADATA].weakData;
		};

		// add metadata on freeze-family methods calling
		var onFreeze = function (it) {
		  if (FREEZING && REQUIRED && isExtensible(it) && !hasOwn(it, METADATA)) setMetadata(it);
		  return it;
		};

		var enable = function () {
		  meta.enable = function () { /* empty */ };
		  REQUIRED = true;
		  var getOwnPropertyNames = getOwnPropertyNamesModule.f;
		  var splice = uncurryThis([].splice);
		  var test = {};
		  test[METADATA] = 1;

		  // prevent exposing of metadata key
		  if (getOwnPropertyNames(test).length) {
		    getOwnPropertyNamesModule.f = function (it) {
		      var result = getOwnPropertyNames(it);
		      for (var i = 0, length = result.length; i < length; i++) {
		        if (result[i] === METADATA) {
		          splice(result, i, 1);
		          break;
		        }
		      } return result;
		    };

		    $({ target: 'Object', stat: true, forced: true }, {
		      getOwnPropertyNames: getOwnPropertyNamesExternalModule.f
		    });
		  }
		};

		var meta = internalMetadata$1.exports = {
		  enable: enable,
		  fastKey: fastKey,
		  getWeakData: getWeakData,
		  onFreeze: onFreeze
		};

		hiddenKeys[METADATA] = true;
		return internalMetadata$1.exports;
	}

	var isArrayIteratorMethod;
	var hasRequiredIsArrayIteratorMethod;

	function requireIsArrayIteratorMethod () {
		if (hasRequiredIsArrayIteratorMethod) return isArrayIteratorMethod;
		hasRequiredIsArrayIteratorMethod = 1;
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();
		var Iterators = /*@__PURE__*/ requireIterators();

		var ITERATOR = wellKnownSymbol('iterator');
		var ArrayPrototype = Array.prototype;

		// check on default Array iterator
		isArrayIteratorMethod = function (it) {
		  return it !== undefined && (Iterators.Array === it || ArrayPrototype[ITERATOR] === it);
		};
		return isArrayIteratorMethod;
	}

	var getIteratorMethod;
	var hasRequiredGetIteratorMethod;

	function requireGetIteratorMethod () {
		if (hasRequiredGetIteratorMethod) return getIteratorMethod;
		hasRequiredGetIteratorMethod = 1;
		var classof = /*@__PURE__*/ requireClassof();
		var getMethod = /*@__PURE__*/ requireGetMethod();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined();
		var Iterators = /*@__PURE__*/ requireIterators();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();

		var ITERATOR = wellKnownSymbol('iterator');

		getIteratorMethod = function (it) {
		  if (!isNullOrUndefined(it)) return getMethod(it, ITERATOR)
		    || getMethod(it, '@@iterator')
		    || Iterators[classof(it)];
		};
		return getIteratorMethod;
	}

	var getIterator;
	var hasRequiredGetIterator;

	function requireGetIterator () {
		if (hasRequiredGetIterator) return getIterator;
		hasRequiredGetIterator = 1;
		var call = /*@__PURE__*/ requireFunctionCall();
		var aCallable = /*@__PURE__*/ requireACallable();
		var anObject = /*@__PURE__*/ requireAnObject();
		var tryToString = /*@__PURE__*/ requireTryToString();
		var getIteratorMethod = /*@__PURE__*/ requireGetIteratorMethod();

		var $TypeError = TypeError;

		getIterator = function (argument, usingIterator) {
		  var iteratorMethod = arguments.length < 2 ? getIteratorMethod(argument) : usingIterator;
		  if (aCallable(iteratorMethod)) return anObject(call(iteratorMethod, argument));
		  throw new $TypeError(tryToString(argument) + ' is not iterable');
		};
		return getIterator;
	}

	var iteratorClose;
	var hasRequiredIteratorClose;

	function requireIteratorClose () {
		if (hasRequiredIteratorClose) return iteratorClose;
		hasRequiredIteratorClose = 1;
		var call = /*@__PURE__*/ requireFunctionCall();
		var anObject = /*@__PURE__*/ requireAnObject();
		var getMethod = /*@__PURE__*/ requireGetMethod();

		iteratorClose = function (iterator, kind, value) {
		  var innerResult, innerError;
		  anObject(iterator);
		  try {
		    innerResult = getMethod(iterator, 'return');
		    if (!innerResult) {
		      if (kind === 'throw') throw value;
		      return value;
		    }
		    innerResult = call(innerResult, iterator);
		  } catch (error) {
		    innerError = true;
		    innerResult = error;
		  }
		  if (kind === 'throw') throw value;
		  if (innerError) throw innerResult;
		  anObject(innerResult);
		  return value;
		};
		return iteratorClose;
	}

	var iterate;
	var hasRequiredIterate;

	function requireIterate () {
		if (hasRequiredIterate) return iterate;
		hasRequiredIterate = 1;
		var bind = /*@__PURE__*/ requireFunctionBindContext();
		var call = /*@__PURE__*/ requireFunctionCall();
		var anObject = /*@__PURE__*/ requireAnObject();
		var tryToString = /*@__PURE__*/ requireTryToString();
		var isArrayIteratorMethod = /*@__PURE__*/ requireIsArrayIteratorMethod();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike();
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf();
		var getIterator = /*@__PURE__*/ requireGetIterator();
		var getIteratorMethod = /*@__PURE__*/ requireGetIteratorMethod();
		var iteratorClose = /*@__PURE__*/ requireIteratorClose();

		var $TypeError = TypeError;

		var Result = function (stopped, result) {
		  this.stopped = stopped;
		  this.result = result;
		};

		var ResultPrototype = Result.prototype;

		iterate = function (iterable, unboundFunction, options) {
		  var that = options && options.that;
		  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
		  var IS_RECORD = !!(options && options.IS_RECORD);
		  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
		  var INTERRUPTED = !!(options && options.INTERRUPTED);
		  var fn = bind(unboundFunction, that);
		  var iterator, iterFn, index, length, result, next, step;

		  var stop = function (condition) {
		    if (iterator) iteratorClose(iterator, 'normal');
		    return new Result(true, condition);
		  };

		  var callFn = function (value) {
		    if (AS_ENTRIES) {
		      anObject(value);
		      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
		    } return INTERRUPTED ? fn(value, stop) : fn(value);
		  };

		  if (IS_RECORD) {
		    iterator = iterable.iterator;
		  } else if (IS_ITERATOR) {
		    iterator = iterable;
		  } else {
		    iterFn = getIteratorMethod(iterable);
		    if (!iterFn) throw new $TypeError(tryToString(iterable) + ' is not iterable');
		    // optimisation for array iterators
		    if (isArrayIteratorMethod(iterFn)) {
		      for (index = 0, length = lengthOfArrayLike(iterable); length > index; index++) {
		        result = callFn(iterable[index]);
		        if (result && isPrototypeOf(ResultPrototype, result)) return result;
		      } return new Result(false);
		    }
		    iterator = getIterator(iterable, iterFn);
		  }

		  next = IS_RECORD ? iterable.next : iterator.next;
		  while (!(step = call(next, iterator)).done) {
		    try {
		      result = callFn(step.value);
		    } catch (error) {
		      iteratorClose(iterator, 'throw', error);
		    }
		    if (typeof result == 'object' && result && isPrototypeOf(ResultPrototype, result)) return result;
		  } return new Result(false);
		};
		return iterate;
	}

	var anInstance;
	var hasRequiredAnInstance;

	function requireAnInstance () {
		if (hasRequiredAnInstance) return anInstance;
		hasRequiredAnInstance = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf();

		var $TypeError = TypeError;

		anInstance = function (it, Prototype) {
		  if (isPrototypeOf(Prototype, it)) return it;
		  throw new $TypeError('Incorrect invocation');
		};
		return anInstance;
	}

	var collection$1;
	var hasRequiredCollection$1;

	function requireCollection$1 () {
		if (hasRequiredCollection$1) return collection$1;
		hasRequiredCollection$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var InternalMetadataModule = /*@__PURE__*/ requireInternalMetadata$1();
		var fails = /*@__PURE__*/ requireFails();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty();
		var iterate = /*@__PURE__*/ requireIterate();
		var anInstance = /*@__PURE__*/ requireAnInstance();
		var isCallable = /*@__PURE__*/ requireIsCallable();
		var isObject = /*@__PURE__*/ requireIsObject();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty().f;
		var forEach = /*@__PURE__*/ requireArrayIteration().forEach;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var InternalStateModule = /*@__PURE__*/ requireInternalState();

		var setInternalState = InternalStateModule.set;
		var internalStateGetterFor = InternalStateModule.getterFor;

		collection$1 = function (CONSTRUCTOR_NAME, wrapper, common) {
		  var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
		  var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
		  var ADDER = IS_MAP ? 'set' : 'add';
		  var NativeConstructor = globalThis[CONSTRUCTOR_NAME];
		  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
		  var exported = {};
		  var Constructor;

		  if (!DESCRIPTORS || !isCallable(NativeConstructor)
		    || !(IS_WEAK || NativePrototype.forEach && !fails(function () { new NativeConstructor().entries().next(); }))
		  ) {
		    // create collection constructor
		    Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
		    InternalMetadataModule.enable();
		  } else {
		    Constructor = wrapper(function (target, iterable) {
		      setInternalState(anInstance(target, Prototype), {
		        type: CONSTRUCTOR_NAME,
		        collection: new NativeConstructor()
		      });
		      if (!isNullOrUndefined(iterable)) iterate(iterable, target[ADDER], { that: target, AS_ENTRIES: IS_MAP });
		    });

		    var Prototype = Constructor.prototype;

		    var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

		    forEach(['add', 'clear', 'delete', 'forEach', 'get', 'has', 'set', 'keys', 'values', 'entries'], function (KEY) {
		      var IS_ADDER = KEY === 'add' || KEY === 'set';
		      if (KEY in NativePrototype && !(IS_WEAK && KEY === 'clear')) {
		        createNonEnumerableProperty(Prototype, KEY, function (a, b) {
		          var collection = getInternalState(this).collection;
		          if (!IS_ADDER && IS_WEAK && !isObject(a)) return KEY === 'get' ? undefined : false;
		          var result = collection[KEY](a === 0 ? 0 : a, b);
		          return IS_ADDER ? this : result;
		        });
		      }
		    });

		    IS_WEAK || defineProperty(Prototype, 'size', {
		      configurable: true,
		      get: function () {
		        return getInternalState(this).collection.size;
		      }
		    });
		  }

		  setToStringTag(Constructor, CONSTRUCTOR_NAME, false, true);

		  exported[CONSTRUCTOR_NAME] = Constructor;
		  $({ global: true, forced: true }, exported);

		  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

		  return Constructor;
		};
		return collection$1;
	}

	var defineBuiltInAccessor;
	var hasRequiredDefineBuiltInAccessor;

	function requireDefineBuiltInAccessor () {
		if (hasRequiredDefineBuiltInAccessor) return defineBuiltInAccessor;
		hasRequiredDefineBuiltInAccessor = 1;
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty();

		defineBuiltInAccessor = function (target, name, descriptor) {
		  return defineProperty.f(target, name, descriptor);
		};
		return defineBuiltInAccessor;
	}

	var defineBuiltIns$1;
	var hasRequiredDefineBuiltIns$1;

	function requireDefineBuiltIns$1 () {
		if (hasRequiredDefineBuiltIns$1) return defineBuiltIns$1;
		hasRequiredDefineBuiltIns$1 = 1;
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn();

		defineBuiltIns$1 = function (target, src, options) {
		  for (var key in src) {
		    if (options && options.unsafe && target[key]) target[key] = src[key];
		    else defineBuiltIn(target, key, src[key], options);
		  } return target;
		};
		return defineBuiltIns$1;
	}

	var setSpecies;
	var hasRequiredSetSpecies;

	function requireSetSpecies () {
		if (hasRequiredSetSpecies) return setSpecies;
		hasRequiredSetSpecies = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn();
		var defineBuiltInAccessor = /*@__PURE__*/ requireDefineBuiltInAccessor();
		var wellKnownSymbol = /*@__PURE__*/ requireWellKnownSymbol();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();

		var SPECIES = wellKnownSymbol('species');

		setSpecies = function (CONSTRUCTOR_NAME) {
		  var Constructor = getBuiltIn(CONSTRUCTOR_NAME);

		  if (DESCRIPTORS && Constructor && !Constructor[SPECIES]) {
		    defineBuiltInAccessor(Constructor, SPECIES, {
		      configurable: true,
		      get: function () { return this; }
		    });
		  }
		};
		return setSpecies;
	}

	var collectionStrong$1;
	var hasRequiredCollectionStrong$1;

	function requireCollectionStrong$1 () {
		if (hasRequiredCollectionStrong$1) return collectionStrong$1;
		hasRequiredCollectionStrong$1 = 1;
		var create = /*@__PURE__*/ requireObjectCreate();
		var defineBuiltInAccessor = /*@__PURE__*/ requireDefineBuiltInAccessor();
		var defineBuiltIns = /*@__PURE__*/ requireDefineBuiltIns$1();
		var bind = /*@__PURE__*/ requireFunctionBindContext();
		var anInstance = /*@__PURE__*/ requireAnInstance();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined();
		var iterate = /*@__PURE__*/ requireIterate();
		var defineIterator = /*@__PURE__*/ requireIteratorDefine();
		var createIterResultObject = /*@__PURE__*/ requireCreateIterResultObject();
		var setSpecies = /*@__PURE__*/ requireSetSpecies();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors();
		var fastKey = /*@__PURE__*/ requireInternalMetadata$1().fastKey;
		var InternalStateModule = /*@__PURE__*/ requireInternalState();

		var setInternalState = InternalStateModule.set;
		var internalStateGetterFor = InternalStateModule.getterFor;

		collectionStrong$1 = {
		  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
		    var Constructor = wrapper(function (that, iterable) {
		      anInstance(that, Prototype);
		      setInternalState(that, {
		        type: CONSTRUCTOR_NAME,
		        index: create(null),
		        first: null,
		        last: null,
		        size: 0
		      });
		      if (!DESCRIPTORS) that.size = 0;
		      if (!isNullOrUndefined(iterable)) iterate(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
		    });

		    var Prototype = Constructor.prototype;

		    var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

		    var define = function (that, key, value) {
		      var state = getInternalState(that);
		      var entry = getEntry(that, key);
		      var previous, index;
		      // change existing entry
		      if (entry) {
		        entry.value = value;
		      // create new entry
		      } else {
		        state.last = entry = {
		          index: index = fastKey(key, true),
		          key: key,
		          value: value,
		          previous: previous = state.last,
		          next: null,
		          removed: false
		        };
		        if (!state.first) state.first = entry;
		        if (previous) previous.next = entry;
		        if (DESCRIPTORS) state.size++;
		        else that.size++;
		        // add to index
		        if (index !== 'F') state.index[index] = entry;
		      } return that;
		    };

		    var getEntry = function (that, key) {
		      var state = getInternalState(that);
		      // fast case
		      var index = fastKey(key);
		      var entry;
		      if (index !== 'F') return state.index[index];
		      // frozen object case
		      for (entry = state.first; entry; entry = entry.next) {
		        if (entry.key === key) return entry;
		      }
		    };

		    defineBuiltIns(Prototype, {
		      // `{ Map, Set }.prototype.clear()` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.clear
		      // https://tc39.es/ecma262/#sec-set.prototype.clear
		      clear: function clear() {
		        var that = this;
		        var state = getInternalState(that);
		        var entry = state.first;
		        while (entry) {
		          entry.removed = true;
		          if (entry.previous) entry.previous = entry.previous.next = null;
		          entry = entry.next;
		        }
		        state.first = state.last = null;
		        state.index = create(null);
		        if (DESCRIPTORS) state.size = 0;
		        else that.size = 0;
		      },
		      // `{ Map, Set }.prototype.delete(key)` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.delete
		      // https://tc39.es/ecma262/#sec-set.prototype.delete
		      'delete': function (key) {
		        var that = this;
		        var state = getInternalState(that);
		        var entry = getEntry(that, key);
		        if (entry) {
		          var next = entry.next;
		          var prev = entry.previous;
		          delete state.index[entry.index];
		          entry.removed = true;
		          if (prev) prev.next = next;
		          if (next) next.previous = prev;
		          if (state.first === entry) state.first = next;
		          if (state.last === entry) state.last = prev;
		          if (DESCRIPTORS) state.size--;
		          else that.size--;
		        } return !!entry;
		      },
		      // `{ Map, Set }.prototype.forEach(callbackfn, thisArg = undefined)` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.foreach
		      // https://tc39.es/ecma262/#sec-set.prototype.foreach
		      forEach: function forEach(callbackfn /* , that = undefined */) {
		        var state = getInternalState(this);
		        var boundFunction = bind(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		        var entry;
		        while (entry = entry ? entry.next : state.first) {
		          boundFunction(entry.value, entry.key, this);
		          // revert to the last existing entry
		          while (entry && entry.removed) entry = entry.previous;
		        }
		      },
		      // `{ Map, Set}.prototype.has(key)` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.has
		      // https://tc39.es/ecma262/#sec-set.prototype.has
		      has: function has(key) {
		        return !!getEntry(this, key);
		      }
		    });

		    defineBuiltIns(Prototype, IS_MAP ? {
		      // `Map.prototype.get(key)` method
		      // https://tc39.es/ecma262/#sec-map.prototype.get
		      get: function get(key) {
		        var entry = getEntry(this, key);
		        return entry && entry.value;
		      },
		      // `Map.prototype.set(key, value)` method
		      // https://tc39.es/ecma262/#sec-map.prototype.set
		      set: function set(key, value) {
		        return define(this, key === 0 ? 0 : key, value);
		      }
		    } : {
		      // `Set.prototype.add(value)` method
		      // https://tc39.es/ecma262/#sec-set.prototype.add
		      add: function add(value) {
		        return define(this, value = value === 0 ? 0 : value, value);
		      }
		    });
		    if (DESCRIPTORS) defineBuiltInAccessor(Prototype, 'size', {
		      configurable: true,
		      get: function () {
		        return getInternalState(this).size;
		      }
		    });
		    return Constructor;
		  },
		  setStrong: function (Constructor, CONSTRUCTOR_NAME, IS_MAP) {
		    var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
		    var getInternalCollectionState = internalStateGetterFor(CONSTRUCTOR_NAME);
		    var getInternalIteratorState = internalStateGetterFor(ITERATOR_NAME);
		    // `{ Map, Set }.prototype.{ keys, values, entries, @@iterator }()` methods
		    // https://tc39.es/ecma262/#sec-map.prototype.entries
		    // https://tc39.es/ecma262/#sec-map.prototype.keys
		    // https://tc39.es/ecma262/#sec-map.prototype.values
		    // https://tc39.es/ecma262/#sec-map.prototype-@@iterator
		    // https://tc39.es/ecma262/#sec-set.prototype.entries
		    // https://tc39.es/ecma262/#sec-set.prototype.keys
		    // https://tc39.es/ecma262/#sec-set.prototype.values
		    // https://tc39.es/ecma262/#sec-set.prototype-@@iterator
		    defineIterator(Constructor, CONSTRUCTOR_NAME, function (iterated, kind) {
		      setInternalState(this, {
		        type: ITERATOR_NAME,
		        target: iterated,
		        state: getInternalCollectionState(iterated),
		        kind: kind,
		        last: null
		      });
		    }, function () {
		      var state = getInternalIteratorState(this);
		      var kind = state.kind;
		      var entry = state.last;
		      // revert to the last existing entry
		      while (entry && entry.removed) entry = entry.previous;
		      // get next entry
		      if (!state.target || !(state.last = entry = entry ? entry.next : state.state.first)) {
		        // or finish the iteration
		        state.target = null;
		        return createIterResultObject(undefined, true);
		      }
		      // return step by kind
		      if (kind === 'keys') return createIterResultObject(entry.key, false);
		      if (kind === 'values') return createIterResultObject(entry.value, false);
		      return createIterResultObject([entry.key, entry.value], false);
		    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

		    // `{ Map, Set }.prototype[@@species]` accessors
		    // https://tc39.es/ecma262/#sec-get-map-@@species
		    // https://tc39.es/ecma262/#sec-get-set-@@species
		    setSpecies(CONSTRUCTOR_NAME);
		  }
		};
		return collectionStrong$1;
	}

	var hasRequiredEs_set_constructor$1;

	function requireEs_set_constructor$1 () {
		if (hasRequiredEs_set_constructor$1) return es_set_constructor$1;
		hasRequiredEs_set_constructor$1 = 1;
		var collection = /*@__PURE__*/ requireCollection$1();
		var collectionStrong = /*@__PURE__*/ requireCollectionStrong$1();

		// `Set` constructor
		// https://tc39.es/ecma262/#sec-set-objects
		collection('Set', function (init) {
		  return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
		}, collectionStrong);
		return es_set_constructor$1;
	}

	var hasRequiredEs_set$1;

	function requireEs_set$1 () {
		if (hasRequiredEs_set$1) return es_set$1;
		hasRequiredEs_set$1 = 1;
		// TODO: Remove this module from `core-js@4` since it's replaced to module below
		requireEs_set_constructor$1();
		return es_set$1;
	}

	var es_set_difference_v2$1 = {};

	var aSet$1;
	var hasRequiredASet$1;

	function requireASet$1 () {
		if (hasRequiredASet$1) return aSet$1;
		hasRequiredASet$1 = 1;
		var tryToString = /*@__PURE__*/ requireTryToString();

		var $TypeError = TypeError;

		// Perform ? RequireInternalSlot(M, [[SetData]])
		aSet$1 = function (it) {
		  if (typeof it == 'object' && 'size' in it && 'has' in it && 'add' in it && 'delete' in it && 'keys' in it) return it;
		  throw new $TypeError(tryToString(it) + ' is not a set');
		};
		return aSet$1;
	}

	var caller$1;
	var hasRequiredCaller$1;

	function requireCaller$1 () {
		if (hasRequiredCaller$1) return caller$1;
		hasRequiredCaller$1 = 1;
		caller$1 = function (methodName, numArgs) {
		  return numArgs === 1 ? function (object, arg) {
		    return object[methodName](arg);
		  } : function (object, arg1, arg2) {
		    return object[methodName](arg1, arg2);
		  };
		};
		return caller$1;
	}

	var setHelpers$1;
	var hasRequiredSetHelpers$1;

	function requireSetHelpers$1 () {
		if (hasRequiredSetHelpers$1) return setHelpers$1;
		hasRequiredSetHelpers$1 = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn();
		var caller = /*@__PURE__*/ requireCaller$1();

		var Set = getBuiltIn('Set');
		var SetPrototype = Set.prototype;

		setHelpers$1 = {
		  Set: Set,
		  add: caller('add', 1),
		  has: caller('has', 1),
		  remove: caller('delete', 1),
		  proto: SetPrototype
		};
		return setHelpers$1;
	}

	var iterateSimple$1;
	var hasRequiredIterateSimple$1;

	function requireIterateSimple$1 () {
		if (hasRequiredIterateSimple$1) return iterateSimple$1;
		hasRequiredIterateSimple$1 = 1;
		var call = /*@__PURE__*/ requireFunctionCall();

		iterateSimple$1 = function (record, fn, ITERATOR_INSTEAD_OF_RECORD) {
		  var iterator = ITERATOR_INSTEAD_OF_RECORD ? record : record.iterator;
		  var next = record.next;
		  var step, result;
		  while (!(step = call(next, iterator)).done) {
		    result = fn(step.value);
		    if (result !== undefined) return result;
		  }
		};
		return iterateSimple$1;
	}

	var setIterate$1;
	var hasRequiredSetIterate$1;

	function requireSetIterate$1 () {
		if (hasRequiredSetIterate$1) return setIterate$1;
		hasRequiredSetIterate$1 = 1;
		var iterateSimple = /*@__PURE__*/ requireIterateSimple$1();

		setIterate$1 = function (set, fn, interruptible) {
		  return interruptible ? iterateSimple(set.keys(), fn, true) : set.forEach(fn);
		};
		return setIterate$1;
	}

	var setClone$1;
	var hasRequiredSetClone$1;

	function requireSetClone$1 () {
		if (hasRequiredSetClone$1) return setClone$1;
		hasRequiredSetClone$1 = 1;
		var SetHelpers = /*@__PURE__*/ requireSetHelpers$1();
		var iterate = /*@__PURE__*/ requireSetIterate$1();

		var Set = SetHelpers.Set;
		var add = SetHelpers.add;

		setClone$1 = function (set) {
		  var result = new Set();
		  iterate(set, function (it) {
		    add(result, it);
		  });
		  return result;
		};
		return setClone$1;
	}

	var setSize$1;
	var hasRequiredSetSize$1;

	function requireSetSize$1 () {
		if (hasRequiredSetSize$1) return setSize$1;
		hasRequiredSetSize$1 = 1;
		setSize$1 = function (set) {
		  return set.size;
		};
		return setSize$1;
	}

	var getIteratorDirect$1;
	var hasRequiredGetIteratorDirect$1;

	function requireGetIteratorDirect$1 () {
		if (hasRequiredGetIteratorDirect$1) return getIteratorDirect$1;
		hasRequiredGetIteratorDirect$1 = 1;
		// `GetIteratorDirect(obj)` abstract operation
		// https://tc39.es/ecma262/#sec-getiteratordirect
		getIteratorDirect$1 = function (obj) {
		  return {
		    iterator: obj,
		    next: obj.next,
		    done: false
		  };
		};
		return getIteratorDirect$1;
	}

	var getSetRecord$1;
	var hasRequiredGetSetRecord$1;

	function requireGetSetRecord$1 () {
		if (hasRequiredGetSetRecord$1) return getSetRecord$1;
		hasRequiredGetSetRecord$1 = 1;
		var aCallable = /*@__PURE__*/ requireACallable();
		var anObject = /*@__PURE__*/ requireAnObject();
		var call = /*@__PURE__*/ requireFunctionCall();
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity();
		var getIteratorDirect = /*@__PURE__*/ requireGetIteratorDirect$1();

		var INVALID_SIZE = 'Invalid size';
		var $RangeError = RangeError;
		var $TypeError = TypeError;
		var max = Math.max;

		var SetRecord = function (set, intSize) {
		  this.set = set;
		  this.size = max(intSize, 0);
		  this.has = aCallable(set.has);
		  this.keys = aCallable(set.keys);
		};

		SetRecord.prototype = {
		  getIterator: function () {
		    return getIteratorDirect(anObject(call(this.keys, this.set)));
		  },
		  includes: function (it) {
		    return call(this.has, this.set, it);
		  }
		};

		// `GetSetRecord` abstract operation
		// https://tc39.es/proposal-set-methods/#sec-getsetrecord
		getSetRecord$1 = function (obj) {
		  anObject(obj);
		  var numSize = +obj.size;
		  // NOTE: If size is undefined, then numSize will be NaN
		  // eslint-disable-next-line no-self-compare -- NaN check
		  if (numSize !== numSize) throw new $TypeError(INVALID_SIZE);
		  var intSize = toIntegerOrInfinity(numSize);
		  if (intSize < 0) throw new $RangeError(INVALID_SIZE);
		  return new SetRecord(obj, intSize);
		};
		return getSetRecord$1;
	}

	var setDifference$1;
	var hasRequiredSetDifference$1;

	function requireSetDifference$1 () {
		if (hasRequiredSetDifference$1) return setDifference$1;
		hasRequiredSetDifference$1 = 1;
		var aSet = /*@__PURE__*/ requireASet$1();
		var SetHelpers = /*@__PURE__*/ requireSetHelpers$1();
		var clone = /*@__PURE__*/ requireSetClone$1();
		var size = /*@__PURE__*/ requireSetSize$1();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord$1();
		var iterateSet = /*@__PURE__*/ requireSetIterate$1();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple$1();

		var has = SetHelpers.has;
		var remove = SetHelpers.remove;

		// `Set.prototype.difference` method
		// https://tc39.es/ecma262/#sec-set.prototype.difference
		setDifference$1 = function difference(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  var result = clone(O);
		  if (size(O) <= otherRec.size) iterateSet(O, function (e) {
		    if (otherRec.includes(e)) remove(result, e);
		  });
		  else iterateSimple(otherRec.getIterator(), function (e) {
		    if (has(result, e)) remove(result, e);
		  });
		  return result;
		};
		return setDifference$1;
	}

	var setMethodAcceptSetLike$1;
	var hasRequiredSetMethodAcceptSetLike$1;

	function requireSetMethodAcceptSetLike$1 () {
		if (hasRequiredSetMethodAcceptSetLike$1) return setMethodAcceptSetLike$1;
		hasRequiredSetMethodAcceptSetLike$1 = 1;
		setMethodAcceptSetLike$1 = function () {
		  return false;
		};
		return setMethodAcceptSetLike$1;
	}

	var hasRequiredEs_set_difference_v2$1;

	function requireEs_set_difference_v2$1 () {
		if (hasRequiredEs_set_difference_v2$1) return es_set_difference_v2$1;
		hasRequiredEs_set_difference_v2$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var difference = /*@__PURE__*/ requireSetDifference$1();
		var fails = /*@__PURE__*/ requireFails();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike$1();

		var SET_LIKE_INCORRECT_BEHAVIOR = !setMethodAcceptSetLike('difference', function (result) {
		  return result.size === 0;
		});

		var FORCED = SET_LIKE_INCORRECT_BEHAVIOR || fails(function () {
		  // https://bugs.webkit.org/show_bug.cgi?id=288595
		  var setLike = {
		    size: 1,
		    has: function () { return true; },
		    keys: function () {
		      var index = 0;
		      return {
		        next: function () {
		          var done = index++ > 1;
		          if (baseSet.has(1)) baseSet.clear();
		          return { done: done, value: 2 };
		        }
		      };
		    }
		  };
		  // eslint-disable-next-line es/no-set -- testing
		  var baseSet = new Set([1, 2, 3, 4]);
		  // eslint-disable-next-line es/no-set-prototype-difference -- testing
		  return baseSet.difference(setLike).size !== 3;
		});

		// `Set.prototype.difference` method
		// https://tc39.es/ecma262/#sec-set.prototype.difference
		$({ target: 'Set', proto: true, real: true, forced: FORCED }, {
		  difference: difference
		});
		return es_set_difference_v2$1;
	}

	var es_set_intersection_v2$1 = {};

	var setIntersection$1;
	var hasRequiredSetIntersection$1;

	function requireSetIntersection$1 () {
		if (hasRequiredSetIntersection$1) return setIntersection$1;
		hasRequiredSetIntersection$1 = 1;
		var aSet = /*@__PURE__*/ requireASet$1();
		var SetHelpers = /*@__PURE__*/ requireSetHelpers$1();
		var size = /*@__PURE__*/ requireSetSize$1();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord$1();
		var iterateSet = /*@__PURE__*/ requireSetIterate$1();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple$1();

		var Set = SetHelpers.Set;
		var add = SetHelpers.add;
		var has = SetHelpers.has;

		// `Set.prototype.intersection` method
		// https://tc39.es/ecma262/#sec-set.prototype.intersection
		setIntersection$1 = function intersection(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  var result = new Set();

		  if (size(O) > otherRec.size) {
		    iterateSimple(otherRec.getIterator(), function (e) {
		      if (has(O, e)) add(result, e);
		    });
		  } else {
		    iterateSet(O, function (e) {
		      if (otherRec.includes(e)) add(result, e);
		    });
		  }

		  return result;
		};
		return setIntersection$1;
	}

	var hasRequiredEs_set_intersection_v2$1;

	function requireEs_set_intersection_v2$1 () {
		if (hasRequiredEs_set_intersection_v2$1) return es_set_intersection_v2$1;
		hasRequiredEs_set_intersection_v2$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var fails = /*@__PURE__*/ requireFails();
		var intersection = /*@__PURE__*/ requireSetIntersection$1();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike$1();

		var INCORRECT = !setMethodAcceptSetLike('intersection', function (result) {
		  return result.size === 2 && result.has(1) && result.has(2);
		}) || fails(function () {
		  // eslint-disable-next-line es/no-array-from, es/no-set, es/no-set-prototype-intersection -- testing
		  return String(Array.from(new Set([1, 2, 3]).intersection(new Set([3, 2])))) !== '3,2';
		});

		// `Set.prototype.intersection` method
		// https://tc39.es/ecma262/#sec-set.prototype.intersection
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  intersection: intersection
		});
		return es_set_intersection_v2$1;
	}

	var es_set_isDisjointFrom_v2$1 = {};

	var setIsDisjointFrom$1;
	var hasRequiredSetIsDisjointFrom$1;

	function requireSetIsDisjointFrom$1 () {
		if (hasRequiredSetIsDisjointFrom$1) return setIsDisjointFrom$1;
		hasRequiredSetIsDisjointFrom$1 = 1;
		var aSet = /*@__PURE__*/ requireASet$1();
		var has = /*@__PURE__*/ requireSetHelpers$1().has;
		var size = /*@__PURE__*/ requireSetSize$1();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord$1();
		var iterateSet = /*@__PURE__*/ requireSetIterate$1();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple$1();
		var iteratorClose = /*@__PURE__*/ requireIteratorClose();

		// `Set.prototype.isDisjointFrom` method
		// https://tc39.es/ecma262/#sec-set.prototype.isdisjointfrom
		setIsDisjointFrom$1 = function isDisjointFrom(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  if (size(O) <= otherRec.size) return iterateSet(O, function (e) {
		    if (otherRec.includes(e)) return false;
		  }, true) !== false;
		  var iterator = otherRec.getIterator();
		  return iterateSimple(iterator, function (e) {
		    if (has(O, e)) return iteratorClose(iterator, 'normal', false);
		  }) !== false;
		};
		return setIsDisjointFrom$1;
	}

	var hasRequiredEs_set_isDisjointFrom_v2$1;

	function requireEs_set_isDisjointFrom_v2$1 () {
		if (hasRequiredEs_set_isDisjointFrom_v2$1) return es_set_isDisjointFrom_v2$1;
		hasRequiredEs_set_isDisjointFrom_v2$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var isDisjointFrom = /*@__PURE__*/ requireSetIsDisjointFrom$1();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike$1();

		var INCORRECT = !setMethodAcceptSetLike('isDisjointFrom', function (result) {
		  return !result;
		});

		// `Set.prototype.isDisjointFrom` method
		// https://tc39.es/ecma262/#sec-set.prototype.isdisjointfrom
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  isDisjointFrom: isDisjointFrom
		});
		return es_set_isDisjointFrom_v2$1;
	}

	var es_set_isSubsetOf_v2$1 = {};

	var setIsSubsetOf$1;
	var hasRequiredSetIsSubsetOf$1;

	function requireSetIsSubsetOf$1 () {
		if (hasRequiredSetIsSubsetOf$1) return setIsSubsetOf$1;
		hasRequiredSetIsSubsetOf$1 = 1;
		var aSet = /*@__PURE__*/ requireASet$1();
		var size = /*@__PURE__*/ requireSetSize$1();
		var iterate = /*@__PURE__*/ requireSetIterate$1();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord$1();

		// `Set.prototype.isSubsetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issubsetof
		setIsSubsetOf$1 = function isSubsetOf(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  if (size(O) > otherRec.size) return false;
		  return iterate(O, function (e) {
		    if (!otherRec.includes(e)) return false;
		  }, true) !== false;
		};
		return setIsSubsetOf$1;
	}

	var hasRequiredEs_set_isSubsetOf_v2$1;

	function requireEs_set_isSubsetOf_v2$1 () {
		if (hasRequiredEs_set_isSubsetOf_v2$1) return es_set_isSubsetOf_v2$1;
		hasRequiredEs_set_isSubsetOf_v2$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var isSubsetOf = /*@__PURE__*/ requireSetIsSubsetOf$1();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike$1();

		var INCORRECT = !setMethodAcceptSetLike('isSubsetOf', function (result) {
		  return result;
		});

		// `Set.prototype.isSubsetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issubsetof
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  isSubsetOf: isSubsetOf
		});
		return es_set_isSubsetOf_v2$1;
	}

	var es_set_isSupersetOf_v2$1 = {};

	var setIsSupersetOf$1;
	var hasRequiredSetIsSupersetOf$1;

	function requireSetIsSupersetOf$1 () {
		if (hasRequiredSetIsSupersetOf$1) return setIsSupersetOf$1;
		hasRequiredSetIsSupersetOf$1 = 1;
		var aSet = /*@__PURE__*/ requireASet$1();
		var has = /*@__PURE__*/ requireSetHelpers$1().has;
		var size = /*@__PURE__*/ requireSetSize$1();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord$1();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple$1();
		var iteratorClose = /*@__PURE__*/ requireIteratorClose();

		// `Set.prototype.isSupersetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issupersetof
		setIsSupersetOf$1 = function isSupersetOf(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  if (size(O) < otherRec.size) return false;
		  var iterator = otherRec.getIterator();
		  return iterateSimple(iterator, function (e) {
		    if (!has(O, e)) return iteratorClose(iterator, 'normal', false);
		  }) !== false;
		};
		return setIsSupersetOf$1;
	}

	var hasRequiredEs_set_isSupersetOf_v2$1;

	function requireEs_set_isSupersetOf_v2$1 () {
		if (hasRequiredEs_set_isSupersetOf_v2$1) return es_set_isSupersetOf_v2$1;
		hasRequiredEs_set_isSupersetOf_v2$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var isSupersetOf = /*@__PURE__*/ requireSetIsSupersetOf$1();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike$1();

		var INCORRECT = !setMethodAcceptSetLike('isSupersetOf', function (result) {
		  return !result;
		});

		// `Set.prototype.isSupersetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issupersetof
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  isSupersetOf: isSupersetOf
		});
		return es_set_isSupersetOf_v2$1;
	}

	var es_set_symmetricDifference_v2$1 = {};

	var setSymmetricDifference$1;
	var hasRequiredSetSymmetricDifference$1;

	function requireSetSymmetricDifference$1 () {
		if (hasRequiredSetSymmetricDifference$1) return setSymmetricDifference$1;
		hasRequiredSetSymmetricDifference$1 = 1;
		var aSet = /*@__PURE__*/ requireASet$1();
		var SetHelpers = /*@__PURE__*/ requireSetHelpers$1();
		var clone = /*@__PURE__*/ requireSetClone$1();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord$1();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple$1();

		var add = SetHelpers.add;
		var has = SetHelpers.has;
		var remove = SetHelpers.remove;

		// `Set.prototype.symmetricDifference` method
		// https://tc39.es/ecma262/#sec-set.prototype.symmetricdifference
		setSymmetricDifference$1 = function symmetricDifference(other) {
		  var O = aSet(this);
		  var keysIter = getSetRecord(other).getIterator();
		  var result = clone(O);
		  iterateSimple(keysIter, function (e) {
		    if (has(O, e)) remove(result, e);
		    else add(result, e);
		  });
		  return result;
		};
		return setSymmetricDifference$1;
	}

	var setMethodGetKeysBeforeCloningDetection$1;
	var hasRequiredSetMethodGetKeysBeforeCloningDetection$1;

	function requireSetMethodGetKeysBeforeCloningDetection$1 () {
		if (hasRequiredSetMethodGetKeysBeforeCloningDetection$1) return setMethodGetKeysBeforeCloningDetection$1;
		hasRequiredSetMethodGetKeysBeforeCloningDetection$1 = 1;
		// Should get iterator record of a set-like object before cloning this
		// https://bugs.webkit.org/show_bug.cgi?id=289430
		setMethodGetKeysBeforeCloningDetection$1 = function (METHOD_NAME) {
		  try {
		    // eslint-disable-next-line es/no-set -- needed for test
		    var baseSet = new Set();
		    var setLike = {
		      size: 0,
		      has: function () { return true; },
		      keys: function () {
		        // eslint-disable-next-line es/no-object-defineproperty -- needed for test
		        return Object.defineProperty({}, 'next', {
		          get: function () {
		            baseSet.clear();
		            baseSet.add(4);
		            return function () {
		              return { done: true };
		            };
		          }
		        });
		      }
		    };
		    var result = baseSet[METHOD_NAME](setLike);

		    return result.size === 1 && result.values().next().value === 4;
		  } catch (error) {
		    return false;
		  }
		};
		return setMethodGetKeysBeforeCloningDetection$1;
	}

	var hasRequiredEs_set_symmetricDifference_v2$1;

	function requireEs_set_symmetricDifference_v2$1 () {
		if (hasRequiredEs_set_symmetricDifference_v2$1) return es_set_symmetricDifference_v2$1;
		hasRequiredEs_set_symmetricDifference_v2$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var symmetricDifference = /*@__PURE__*/ requireSetSymmetricDifference$1();
		var setMethodGetKeysBeforeCloning = /*@__PURE__*/ requireSetMethodGetKeysBeforeCloningDetection$1();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike$1();

		var FORCED = !setMethodAcceptSetLike('symmetricDifference') || !setMethodGetKeysBeforeCloning('symmetricDifference');

		// `Set.prototype.symmetricDifference` method
		// https://tc39.es/ecma262/#sec-set.prototype.symmetricdifference
		$({ target: 'Set', proto: true, real: true, forced: FORCED }, {
		  symmetricDifference: symmetricDifference
		});
		return es_set_symmetricDifference_v2$1;
	}

	var es_set_union_v2$1 = {};

	var setUnion$1;
	var hasRequiredSetUnion$1;

	function requireSetUnion$1 () {
		if (hasRequiredSetUnion$1) return setUnion$1;
		hasRequiredSetUnion$1 = 1;
		var aSet = /*@__PURE__*/ requireASet$1();
		var add = /*@__PURE__*/ requireSetHelpers$1().add;
		var clone = /*@__PURE__*/ requireSetClone$1();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord$1();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple$1();

		// `Set.prototype.union` method
		// https://tc39.es/ecma262/#sec-set.prototype.union
		setUnion$1 = function union(other) {
		  var O = aSet(this);
		  var keysIter = getSetRecord(other).getIterator();
		  var result = clone(O);
		  iterateSimple(keysIter, function (it) {
		    add(result, it);
		  });
		  return result;
		};
		return setUnion$1;
	}

	var hasRequiredEs_set_union_v2$1;

	function requireEs_set_union_v2$1 () {
		if (hasRequiredEs_set_union_v2$1) return es_set_union_v2$1;
		hasRequiredEs_set_union_v2$1 = 1;
		var $ = /*@__PURE__*/ require_export();
		var union = /*@__PURE__*/ requireSetUnion$1();
		var setMethodGetKeysBeforeCloning = /*@__PURE__*/ requireSetMethodGetKeysBeforeCloningDetection$1();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike$1();

		var FORCED = !setMethodAcceptSetLike('union') || !setMethodGetKeysBeforeCloning('union');

		// `Set.prototype.union` method
		// https://tc39.es/ecma262/#sec-set.prototype.union
		$({ target: 'Set', proto: true, real: true, forced: FORCED }, {
		  union: union
		});
		return es_set_union_v2$1;
	}

	var es_string_iterator = {};

	var stringMultibyte;
	var hasRequiredStringMultibyte;

	function requireStringMultibyte () {
		if (hasRequiredStringMultibyte) return stringMultibyte;
		hasRequiredStringMultibyte = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis();
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity();
		var toString = /*@__PURE__*/ requireToString();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible();

		var charAt = uncurryThis(''.charAt);
		var charCodeAt = uncurryThis(''.charCodeAt);
		var stringSlice = uncurryThis(''.slice);

		var createMethod = function (CONVERT_TO_STRING) {
		  return function ($this, pos) {
		    var S = toString(requireObjectCoercible($this));
		    var position = toIntegerOrInfinity(pos);
		    var size = S.length;
		    var first, second;
		    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
		    first = charCodeAt(S, position);
		    return first < 0xD800 || first > 0xDBFF || position + 1 === size
		      || (second = charCodeAt(S, position + 1)) < 0xDC00 || second > 0xDFFF
		        ? CONVERT_TO_STRING
		          ? charAt(S, position)
		          : first
		        : CONVERT_TO_STRING
		          ? stringSlice(S, position, position + 2)
		          : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
		  };
		};

		stringMultibyte = {
		  // `String.prototype.codePointAt` method
		  // https://tc39.es/ecma262/#sec-string.prototype.codepointat
		  codeAt: createMethod(false),
		  // `String.prototype.at` method
		  // https://github.com/mathiasbynens/String.prototype.at
		  charAt: createMethod(true)
		};
		return stringMultibyte;
	}

	var hasRequiredEs_string_iterator;

	function requireEs_string_iterator () {
		if (hasRequiredEs_string_iterator) return es_string_iterator;
		hasRequiredEs_string_iterator = 1;
		var charAt = /*@__PURE__*/ requireStringMultibyte().charAt;
		var toString = /*@__PURE__*/ requireToString();
		var InternalStateModule = /*@__PURE__*/ requireInternalState();
		var defineIterator = /*@__PURE__*/ requireIteratorDefine();
		var createIterResultObject = /*@__PURE__*/ requireCreateIterResultObject();

		var STRING_ITERATOR = 'String Iterator';
		var setInternalState = InternalStateModule.set;
		var getInternalState = InternalStateModule.getterFor(STRING_ITERATOR);

		// `String.prototype[@@iterator]` method
		// https://tc39.es/ecma262/#sec-string.prototype-@@iterator
		defineIterator(String, 'String', function (iterated) {
		  setInternalState(this, {
		    type: STRING_ITERATOR,
		    string: toString(iterated),
		    index: 0
		  });
		// `%StringIteratorPrototype%.next` method
		// https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
		}, function next() {
		  var state = getInternalState(this);
		  var string = state.string;
		  var index = state.index;
		  var point;
		  if (index >= string.length) return createIterResultObject(undefined, true);
		  point = charAt(string, index);
		  state.index += point.length;
		  return createIterResultObject(point, false);
		});
		return es_string_iterator;
	}

	var set$5;
	var hasRequiredSet$5;

	function requireSet$5 () {
		if (hasRequiredSet$5) return set$5;
		hasRequiredSet$5 = 1;
		requireEs_array_iterator();
		requireEs_set$1();
		requireEs_set_difference_v2$1();
		requireEs_set_intersection_v2$1();
		requireEs_set_isDisjointFrom_v2$1();
		requireEs_set_isSubsetOf_v2$1();
		requireEs_set_isSupersetOf_v2$1();
		requireEs_set_symmetricDifference_v2$1();
		requireEs_set_union_v2$1();
		requireEs_string_iterator();
		var path = /*@__PURE__*/ requirePath();

		set$5 = path.Set;
		return set$5;
	}

	var web_domCollections_iterator = {};

	var domIterables;
	var hasRequiredDomIterables;

	function requireDomIterables () {
		if (hasRequiredDomIterables) return domIterables;
		hasRequiredDomIterables = 1;
		// iterable DOM collections
		// flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
		domIterables = {
		  CSSRuleList: 0,
		  CSSStyleDeclaration: 0,
		  CSSValueList: 0,
		  ClientRectList: 0,
		  DOMRectList: 0,
		  DOMStringList: 0,
		  DOMTokenList: 1,
		  DataTransferItemList: 0,
		  FileList: 0,
		  HTMLAllCollection: 0,
		  HTMLCollection: 0,
		  HTMLFormElement: 0,
		  HTMLSelectElement: 0,
		  MediaList: 0,
		  MimeTypeArray: 0,
		  NamedNodeMap: 0,
		  NodeList: 1,
		  PaintRequestList: 0,
		  Plugin: 0,
		  PluginArray: 0,
		  SVGLengthList: 0,
		  SVGNumberList: 0,
		  SVGPathSegList: 0,
		  SVGPointList: 0,
		  SVGStringList: 0,
		  SVGTransformList: 0,
		  SourceBufferList: 0,
		  StyleSheetList: 0,
		  TextTrackCueList: 0,
		  TextTrackList: 0,
		  TouchList: 0
		};
		return domIterables;
	}

	var hasRequiredWeb_domCollections_iterator;

	function requireWeb_domCollections_iterator () {
		if (hasRequiredWeb_domCollections_iterator) return web_domCollections_iterator;
		hasRequiredWeb_domCollections_iterator = 1;
		requireEs_array_iterator();
		var DOMIterables = /*@__PURE__*/ requireDomIterables();
		var globalThis = /*@__PURE__*/ requireGlobalThis$6();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag();
		var Iterators = /*@__PURE__*/ requireIterators();

		for (var COLLECTION_NAME in DOMIterables) {
		  setToStringTag(globalThis[COLLECTION_NAME], COLLECTION_NAME);
		  Iterators[COLLECTION_NAME] = Iterators.Array;
		}
		return web_domCollections_iterator;
	}

	var set$4;
	var hasRequiredSet$4;

	function requireSet$4 () {
		if (hasRequiredSet$4) return set$4;
		hasRequiredSet$4 = 1;
		var parent = /*@__PURE__*/ requireSet$5();
		requireWeb_domCollections_iterator();

		set$4 = parent;
		return set$4;
	}

	var set$3;
	var hasRequiredSet$3;

	function requireSet$3 () {
		if (hasRequiredSet$3) return set$3;
		hasRequiredSet$3 = 1;
		set$3 = /*@__PURE__*/ requireSet$4();
		return set$3;
	}

	var setExports$1 = requireSet$3();
	var _Set$1 = /*@__PURE__*/getDefaultExportFromCjs(setExports$1);

	var es_object_create = {};

	var hasRequiredEs_object_create;

	function requireEs_object_create () {
		if (hasRequiredEs_object_create) return es_object_create;
		hasRequiredEs_object_create = 1;
		// TODO: Remove from `core-js@4`
		var $ = /*@__PURE__*/ require_export$1();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var create = /*@__PURE__*/ requireObjectCreate$1();

		// `Object.create` method
		// https://tc39.es/ecma262/#sec-object.create
		$({ target: 'Object', stat: true, sham: !DESCRIPTORS }, {
		  create: create
		});
		return es_object_create;
	}

	var create$2;
	var hasRequiredCreate$2;

	function requireCreate$2 () {
		if (hasRequiredCreate$2) return create$2;
		hasRequiredCreate$2 = 1;
		requireEs_object_create();
		var path = /*@__PURE__*/ requirePath$1();

		var Object = path.Object;

		create$2 = function create(P, D) {
		  return Object.create(P, D);
		};
		return create$2;
	}

	var create$1;
	var hasRequiredCreate$1;

	function requireCreate$1 () {
		if (hasRequiredCreate$1) return create$1;
		hasRequiredCreate$1 = 1;
		var parent = /*@__PURE__*/ requireCreate$2();

		create$1 = parent;
		return create$1;
	}

	var create;
	var hasRequiredCreate;

	function requireCreate () {
		if (hasRequiredCreate) return create;
		hasRequiredCreate = 1;
		create = /*@__PURE__*/ requireCreate$1();
		return create;
	}

	var createExports = requireCreate();
	var _Object$create = /*@__PURE__*/getDefaultExportFromCjs(createExports);

	var es_set = {};

	var es_set_constructor = {};

	var internalMetadata = {exports: {}};

	var objectGetOwnPropertyNamesExternal = {};

	var hasRequiredObjectGetOwnPropertyNamesExternal;

	function requireObjectGetOwnPropertyNamesExternal () {
		if (hasRequiredObjectGetOwnPropertyNamesExternal) return objectGetOwnPropertyNamesExternal;
		hasRequiredObjectGetOwnPropertyNamesExternal = 1;
		/* eslint-disable es/no-object-getownpropertynames -- safe */
		var classof = /*@__PURE__*/ requireClassofRaw$1();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var $getOwnPropertyNames = /*@__PURE__*/ requireObjectGetOwnPropertyNames$1().f;
		var arraySlice = /*@__PURE__*/ requireArraySlice$1();

		var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
		  ? Object.getOwnPropertyNames(window) : [];

		var getWindowNames = function (it) {
		  try {
		    return $getOwnPropertyNames(it);
		  } catch (error) {
		    return arraySlice(windowNames);
		  }
		};

		// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
		objectGetOwnPropertyNamesExternal.f = function getOwnPropertyNames(it) {
		  return windowNames && classof(it) === 'Window'
		    ? getWindowNames(it)
		    : $getOwnPropertyNames(toIndexedObject(it));
		};
		return objectGetOwnPropertyNamesExternal;
	}

	var arrayBufferNonExtensible;
	var hasRequiredArrayBufferNonExtensible;

	function requireArrayBufferNonExtensible () {
		if (hasRequiredArrayBufferNonExtensible) return arrayBufferNonExtensible;
		hasRequiredArrayBufferNonExtensible = 1;
		// FF26- bug: ArrayBuffers are non-extensible, but Object.isExtensible does not report it
		var fails = /*@__PURE__*/ requireFails$1();

		arrayBufferNonExtensible = fails(function () {
		  if (typeof ArrayBuffer == 'function') {
		    var buffer = new ArrayBuffer(8);
		    // eslint-disable-next-line es/no-object-isextensible, es/no-object-defineproperty -- safe
		    if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
		  }
		});
		return arrayBufferNonExtensible;
	}

	var objectIsExtensible;
	var hasRequiredObjectIsExtensible;

	function requireObjectIsExtensible () {
		if (hasRequiredObjectIsExtensible) return objectIsExtensible;
		hasRequiredObjectIsExtensible = 1;
		var fails = /*@__PURE__*/ requireFails$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var classof = /*@__PURE__*/ requireClassofRaw$1();
		var ARRAY_BUFFER_NON_EXTENSIBLE = /*@__PURE__*/ requireArrayBufferNonExtensible();

		// eslint-disable-next-line es/no-object-isextensible -- safe
		var $isExtensible = Object.isExtensible;
		var FAILS_ON_PRIMITIVES = fails(function () { });

		// `Object.isExtensible` method
		// https://tc39.es/ecma262/#sec-object.isextensible
		objectIsExtensible = (FAILS_ON_PRIMITIVES || ARRAY_BUFFER_NON_EXTENSIBLE) ? function isExtensible(it) {
		  if (!isObject(it)) return false;
		  if (ARRAY_BUFFER_NON_EXTENSIBLE && classof(it) === 'ArrayBuffer') return false;
		  return $isExtensible ? $isExtensible(it) : true;
		} : $isExtensible;
		return objectIsExtensible;
	}

	var freezing;
	var hasRequiredFreezing;

	function requireFreezing () {
		if (hasRequiredFreezing) return freezing;
		hasRequiredFreezing = 1;
		var fails = /*@__PURE__*/ requireFails$1();

		freezing = !fails(function () {
		  // eslint-disable-next-line es/no-object-isextensible, es/no-object-preventextensions -- required for testing
		  return Object.isExtensible(Object.preventExtensions({}));
		});
		return freezing;
	}

	var hasRequiredInternalMetadata;

	function requireInternalMetadata () {
		if (hasRequiredInternalMetadata) return internalMetadata.exports;
		hasRequiredInternalMetadata = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var hiddenKeys = /*@__PURE__*/ requireHiddenKeys$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty$1().f;
		var getOwnPropertyNamesModule = /*@__PURE__*/ requireObjectGetOwnPropertyNames$1();
		var getOwnPropertyNamesExternalModule = /*@__PURE__*/ requireObjectGetOwnPropertyNamesExternal();
		var isExtensible = /*@__PURE__*/ requireObjectIsExtensible();
		var uid = /*@__PURE__*/ requireUid$1();
		var FREEZING = /*@__PURE__*/ requireFreezing();

		var REQUIRED = false;
		var METADATA = uid('meta');
		var id = 0;

		var setMetadata = function (it) {
		  defineProperty(it, METADATA, { value: {
		    objectID: 'O' + id++, // object ID
		    weakData: {}          // weak collections IDs
		  } });
		};

		var fastKey = function (it, create) {
		  // return a primitive with prefix
		  if (!isObject(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
		  if (!hasOwn(it, METADATA)) {
		    // can't set metadata to uncaught frozen object
		    if (!isExtensible(it)) return 'F';
		    // not necessary to add metadata
		    if (!create) return 'E';
		    // add missing metadata
		    setMetadata(it);
		  // return object ID
		  } return it[METADATA].objectID;
		};

		var getWeakData = function (it, create) {
		  if (!hasOwn(it, METADATA)) {
		    // can't set metadata to uncaught frozen object
		    if (!isExtensible(it)) return true;
		    // not necessary to add metadata
		    if (!create) return false;
		    // add missing metadata
		    setMetadata(it);
		  // return the store of weak collections IDs
		  } return it[METADATA].weakData;
		};

		// add metadata on freeze-family methods calling
		var onFreeze = function (it) {
		  if (FREEZING && REQUIRED && isExtensible(it) && !hasOwn(it, METADATA)) setMetadata(it);
		  return it;
		};

		var enable = function () {
		  meta.enable = function () { /* empty */ };
		  REQUIRED = true;
		  var getOwnPropertyNames = getOwnPropertyNamesModule.f;
		  var splice = uncurryThis([].splice);
		  var test = {};
		  test[METADATA] = 1;

		  // prevent exposing of metadata key
		  if (getOwnPropertyNames(test).length) {
		    getOwnPropertyNamesModule.f = function (it) {
		      var result = getOwnPropertyNames(it);
		      for (var i = 0, length = result.length; i < length; i++) {
		        if (result[i] === METADATA) {
		          splice(result, i, 1);
		          break;
		        }
		      } return result;
		    };

		    $({ target: 'Object', stat: true, forced: true }, {
		      getOwnPropertyNames: getOwnPropertyNamesExternalModule.f
		    });
		  }
		};

		var meta = internalMetadata.exports = {
		  enable: enable,
		  fastKey: fastKey,
		  getWeakData: getWeakData,
		  onFreeze: onFreeze
		};

		hiddenKeys[METADATA] = true;
		return internalMetadata.exports;
	}

	var collection;
	var hasRequiredCollection;

	function requireCollection () {
		if (hasRequiredCollection) return collection;
		hasRequiredCollection = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var InternalMetadataModule = /*@__PURE__*/ requireInternalMetadata();
		var fails = /*@__PURE__*/ requireFails$1();
		var createNonEnumerableProperty = /*@__PURE__*/ requireCreateNonEnumerableProperty$1();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var anInstance = /*@__PURE__*/ requireAnInstance$1();
		var isCallable = /*@__PURE__*/ requireIsCallable$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined$1();
		var setToStringTag = /*@__PURE__*/ requireSetToStringTag$1();
		var defineProperty = /*@__PURE__*/ requireObjectDefineProperty$1().f;
		var forEach = /*@__PURE__*/ requireArrayIteration$1().forEach;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var InternalStateModule = /*@__PURE__*/ requireInternalState$1();

		var setInternalState = InternalStateModule.set;
		var internalStateGetterFor = InternalStateModule.getterFor;

		collection = function (CONSTRUCTOR_NAME, wrapper, common) {
		  var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
		  var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
		  var ADDER = IS_MAP ? 'set' : 'add';
		  var NativeConstructor = globalThis[CONSTRUCTOR_NAME];
		  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
		  var exported = {};
		  var Constructor;

		  if (!DESCRIPTORS || !isCallable(NativeConstructor)
		    || !(IS_WEAK || NativePrototype.forEach && !fails(function () { new NativeConstructor().entries().next(); }))
		  ) {
		    // create collection constructor
		    Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
		    InternalMetadataModule.enable();
		  } else {
		    Constructor = wrapper(function (target, iterable) {
		      setInternalState(anInstance(target, Prototype), {
		        type: CONSTRUCTOR_NAME,
		        collection: new NativeConstructor()
		      });
		      if (!isNullOrUndefined(iterable)) iterate(iterable, target[ADDER], { that: target, AS_ENTRIES: IS_MAP });
		    });

		    var Prototype = Constructor.prototype;

		    var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

		    forEach(['add', 'clear', 'delete', 'forEach', 'get', 'has', 'set', 'keys', 'values', 'entries'], function (KEY) {
		      var IS_ADDER = KEY === 'add' || KEY === 'set';
		      if (KEY in NativePrototype && !(IS_WEAK && KEY === 'clear')) {
		        createNonEnumerableProperty(Prototype, KEY, function (a, b) {
		          var collection = getInternalState(this).collection;
		          if (!IS_ADDER && IS_WEAK && !isObject(a)) return KEY === 'get' ? undefined : false;
		          var result = collection[KEY](a === 0 ? 0 : a, b);
		          return IS_ADDER ? this : result;
		        });
		      }
		    });

		    IS_WEAK || defineProperty(Prototype, 'size', {
		      configurable: true,
		      get: function () {
		        return getInternalState(this).collection.size;
		      }
		    });
		  }

		  setToStringTag(Constructor, CONSTRUCTOR_NAME, false, true);

		  exported[CONSTRUCTOR_NAME] = Constructor;
		  $({ global: true, forced: true }, exported);

		  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

		  return Constructor;
		};
		return collection;
	}

	var defineBuiltIns;
	var hasRequiredDefineBuiltIns;

	function requireDefineBuiltIns () {
		if (hasRequiredDefineBuiltIns) return defineBuiltIns;
		hasRequiredDefineBuiltIns = 1;
		var defineBuiltIn = /*@__PURE__*/ requireDefineBuiltIn$1();

		defineBuiltIns = function (target, src, options) {
		  for (var key in src) {
		    if (options && options.unsafe && target[key]) target[key] = src[key];
		    else defineBuiltIn(target, key, src[key], options);
		  } return target;
		};
		return defineBuiltIns;
	}

	var collectionStrong;
	var hasRequiredCollectionStrong;

	function requireCollectionStrong () {
		if (hasRequiredCollectionStrong) return collectionStrong;
		hasRequiredCollectionStrong = 1;
		var create = /*@__PURE__*/ requireObjectCreate$1();
		var defineBuiltInAccessor = /*@__PURE__*/ requireDefineBuiltInAccessor$1();
		var defineBuiltIns = /*@__PURE__*/ requireDefineBuiltIns();
		var bind = /*@__PURE__*/ requireFunctionBindContext$1();
		var anInstance = /*@__PURE__*/ requireAnInstance$1();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined$1();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var defineIterator = /*@__PURE__*/ requireIteratorDefine$1();
		var createIterResultObject = /*@__PURE__*/ requireCreateIterResultObject$1();
		var setSpecies = /*@__PURE__*/ requireSetSpecies$1();
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var fastKey = /*@__PURE__*/ requireInternalMetadata().fastKey;
		var InternalStateModule = /*@__PURE__*/ requireInternalState$1();

		var setInternalState = InternalStateModule.set;
		var internalStateGetterFor = InternalStateModule.getterFor;

		collectionStrong = {
		  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
		    var Constructor = wrapper(function (that, iterable) {
		      anInstance(that, Prototype);
		      setInternalState(that, {
		        type: CONSTRUCTOR_NAME,
		        index: create(null),
		        first: null,
		        last: null,
		        size: 0
		      });
		      if (!DESCRIPTORS) that.size = 0;
		      if (!isNullOrUndefined(iterable)) iterate(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
		    });

		    var Prototype = Constructor.prototype;

		    var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

		    var define = function (that, key, value) {
		      var state = getInternalState(that);
		      var entry = getEntry(that, key);
		      var previous, index;
		      // change existing entry
		      if (entry) {
		        entry.value = value;
		      // create new entry
		      } else {
		        state.last = entry = {
		          index: index = fastKey(key, true),
		          key: key,
		          value: value,
		          previous: previous = state.last,
		          next: null,
		          removed: false
		        };
		        if (!state.first) state.first = entry;
		        if (previous) previous.next = entry;
		        if (DESCRIPTORS) state.size++;
		        else that.size++;
		        // add to index
		        if (index !== 'F') state.index[index] = entry;
		      } return that;
		    };

		    var getEntry = function (that, key) {
		      var state = getInternalState(that);
		      // fast case
		      var index = fastKey(key);
		      var entry;
		      if (index !== 'F') return state.index[index];
		      // frozen object case
		      for (entry = state.first; entry; entry = entry.next) {
		        if (entry.key === key) return entry;
		      }
		    };

		    defineBuiltIns(Prototype, {
		      // `{ Map, Set }.prototype.clear()` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.clear
		      // https://tc39.es/ecma262/#sec-set.prototype.clear
		      clear: function clear() {
		        var that = this;
		        var state = getInternalState(that);
		        var entry = state.first;
		        while (entry) {
		          entry.removed = true;
		          if (entry.previous) entry.previous = entry.previous.next = null;
		          entry = entry.next;
		        }
		        state.first = state.last = null;
		        state.index = create(null);
		        if (DESCRIPTORS) state.size = 0;
		        else that.size = 0;
		      },
		      // `{ Map, Set }.prototype.delete(key)` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.delete
		      // https://tc39.es/ecma262/#sec-set.prototype.delete
		      'delete': function (key) {
		        var that = this;
		        var state = getInternalState(that);
		        var entry = getEntry(that, key);
		        if (entry) {
		          var next = entry.next;
		          var prev = entry.previous;
		          delete state.index[entry.index];
		          entry.removed = true;
		          if (prev) prev.next = next;
		          if (next) next.previous = prev;
		          if (state.first === entry) state.first = next;
		          if (state.last === entry) state.last = prev;
		          if (DESCRIPTORS) state.size--;
		          else that.size--;
		        } return !!entry;
		      },
		      // `{ Map, Set }.prototype.forEach(callbackfn, thisArg = undefined)` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.foreach
		      // https://tc39.es/ecma262/#sec-set.prototype.foreach
		      forEach: function forEach(callbackfn /* , that = undefined */) {
		        var state = getInternalState(this);
		        var boundFunction = bind(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
		        var entry;
		        while (entry = entry ? entry.next : state.first) {
		          boundFunction(entry.value, entry.key, this);
		          // revert to the last existing entry
		          while (entry && entry.removed) entry = entry.previous;
		        }
		      },
		      // `{ Map, Set}.prototype.has(key)` methods
		      // https://tc39.es/ecma262/#sec-map.prototype.has
		      // https://tc39.es/ecma262/#sec-set.prototype.has
		      has: function has(key) {
		        return !!getEntry(this, key);
		      }
		    });

		    defineBuiltIns(Prototype, IS_MAP ? {
		      // `Map.prototype.get(key)` method
		      // https://tc39.es/ecma262/#sec-map.prototype.get
		      get: function get(key) {
		        var entry = getEntry(this, key);
		        return entry && entry.value;
		      },
		      // `Map.prototype.set(key, value)` method
		      // https://tc39.es/ecma262/#sec-map.prototype.set
		      set: function set(key, value) {
		        return define(this, key === 0 ? 0 : key, value);
		      }
		    } : {
		      // `Set.prototype.add(value)` method
		      // https://tc39.es/ecma262/#sec-set.prototype.add
		      add: function add(value) {
		        return define(this, value = value === 0 ? 0 : value, value);
		      }
		    });
		    if (DESCRIPTORS) defineBuiltInAccessor(Prototype, 'size', {
		      configurable: true,
		      get: function () {
		        return getInternalState(this).size;
		      }
		    });
		    return Constructor;
		  },
		  setStrong: function (Constructor, CONSTRUCTOR_NAME, IS_MAP) {
		    var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
		    var getInternalCollectionState = internalStateGetterFor(CONSTRUCTOR_NAME);
		    var getInternalIteratorState = internalStateGetterFor(ITERATOR_NAME);
		    // `{ Map, Set }.prototype.{ keys, values, entries, @@iterator }()` methods
		    // https://tc39.es/ecma262/#sec-map.prototype.entries
		    // https://tc39.es/ecma262/#sec-map.prototype.keys
		    // https://tc39.es/ecma262/#sec-map.prototype.values
		    // https://tc39.es/ecma262/#sec-map.prototype-@@iterator
		    // https://tc39.es/ecma262/#sec-set.prototype.entries
		    // https://tc39.es/ecma262/#sec-set.prototype.keys
		    // https://tc39.es/ecma262/#sec-set.prototype.values
		    // https://tc39.es/ecma262/#sec-set.prototype-@@iterator
		    defineIterator(Constructor, CONSTRUCTOR_NAME, function (iterated, kind) {
		      setInternalState(this, {
		        type: ITERATOR_NAME,
		        target: iterated,
		        state: getInternalCollectionState(iterated),
		        kind: kind,
		        last: null
		      });
		    }, function () {
		      var state = getInternalIteratorState(this);
		      var kind = state.kind;
		      var entry = state.last;
		      // revert to the last existing entry
		      while (entry && entry.removed) entry = entry.previous;
		      // get next entry
		      if (!state.target || !(state.last = entry = entry ? entry.next : state.state.first)) {
		        // or finish the iteration
		        state.target = null;
		        return createIterResultObject(undefined, true);
		      }
		      // return step by kind
		      if (kind === 'keys') return createIterResultObject(entry.key, false);
		      if (kind === 'values') return createIterResultObject(entry.value, false);
		      return createIterResultObject([entry.key, entry.value], false);
		    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

		    // `{ Map, Set }.prototype[@@species]` accessors
		    // https://tc39.es/ecma262/#sec-get-map-@@species
		    // https://tc39.es/ecma262/#sec-get-set-@@species
		    setSpecies(CONSTRUCTOR_NAME);
		  }
		};
		return collectionStrong;
	}

	var hasRequiredEs_set_constructor;

	function requireEs_set_constructor () {
		if (hasRequiredEs_set_constructor) return es_set_constructor;
		hasRequiredEs_set_constructor = 1;
		var collection = /*@__PURE__*/ requireCollection();
		var collectionStrong = /*@__PURE__*/ requireCollectionStrong();

		// `Set` constructor
		// https://tc39.es/ecma262/#sec-set-objects
		collection('Set', function (init) {
		  return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
		}, collectionStrong);
		return es_set_constructor;
	}

	var hasRequiredEs_set;

	function requireEs_set () {
		if (hasRequiredEs_set) return es_set;
		hasRequiredEs_set = 1;
		// TODO: Remove this module from `core-js@4` since it's replaced to module below
		requireEs_set_constructor();
		return es_set;
	}

	var es_set_difference_v2 = {};

	var aSet;
	var hasRequiredASet;

	function requireASet () {
		if (hasRequiredASet) return aSet;
		hasRequiredASet = 1;
		var tryToString = /*@__PURE__*/ requireTryToString$1();

		var $TypeError = TypeError;

		// Perform ? RequireInternalSlot(M, [[SetData]])
		aSet = function (it) {
		  if (typeof it == 'object' && 'size' in it && 'has' in it && 'add' in it && 'delete' in it && 'keys' in it) return it;
		  throw new $TypeError(tryToString(it) + ' is not a set');
		};
		return aSet;
	}

	var caller;
	var hasRequiredCaller;

	function requireCaller () {
		if (hasRequiredCaller) return caller;
		hasRequiredCaller = 1;
		caller = function (methodName, numArgs) {
		  return numArgs === 1 ? function (object, arg) {
		    return object[methodName](arg);
		  } : function (object, arg1, arg2) {
		    return object[methodName](arg1, arg2);
		  };
		};
		return caller;
	}

	var setHelpers;
	var hasRequiredSetHelpers;

	function requireSetHelpers () {
		if (hasRequiredSetHelpers) return setHelpers;
		hasRequiredSetHelpers = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var caller = /*@__PURE__*/ requireCaller();

		var Set = getBuiltIn('Set');
		var SetPrototype = Set.prototype;

		setHelpers = {
		  Set: Set,
		  add: caller('add', 1),
		  has: caller('has', 1),
		  remove: caller('delete', 1),
		  proto: SetPrototype
		};
		return setHelpers;
	}

	var iterateSimple;
	var hasRequiredIterateSimple;

	function requireIterateSimple () {
		if (hasRequiredIterateSimple) return iterateSimple;
		hasRequiredIterateSimple = 1;
		var call = /*@__PURE__*/ requireFunctionCall$1();

		iterateSimple = function (record, fn, ITERATOR_INSTEAD_OF_RECORD) {
		  var iterator = ITERATOR_INSTEAD_OF_RECORD ? record : record.iterator;
		  var next = record.next;
		  var step, result;
		  while (!(step = call(next, iterator)).done) {
		    result = fn(step.value);
		    if (result !== undefined) return result;
		  }
		};
		return iterateSimple;
	}

	var setIterate;
	var hasRequiredSetIterate;

	function requireSetIterate () {
		if (hasRequiredSetIterate) return setIterate;
		hasRequiredSetIterate = 1;
		var iterateSimple = /*@__PURE__*/ requireIterateSimple();

		setIterate = function (set, fn, interruptible) {
		  return interruptible ? iterateSimple(set.keys(), fn, true) : set.forEach(fn);
		};
		return setIterate;
	}

	var setClone;
	var hasRequiredSetClone;

	function requireSetClone () {
		if (hasRequiredSetClone) return setClone;
		hasRequiredSetClone = 1;
		var SetHelpers = /*@__PURE__*/ requireSetHelpers();
		var iterate = /*@__PURE__*/ requireSetIterate();

		var Set = SetHelpers.Set;
		var add = SetHelpers.add;

		setClone = function (set) {
		  var result = new Set();
		  iterate(set, function (it) {
		    add(result, it);
		  });
		  return result;
		};
		return setClone;
	}

	var setSize;
	var hasRequiredSetSize;

	function requireSetSize () {
		if (hasRequiredSetSize) return setSize;
		hasRequiredSetSize = 1;
		setSize = function (set) {
		  return set.size;
		};
		return setSize;
	}

	var getIteratorDirect;
	var hasRequiredGetIteratorDirect;

	function requireGetIteratorDirect () {
		if (hasRequiredGetIteratorDirect) return getIteratorDirect;
		hasRequiredGetIteratorDirect = 1;
		// `GetIteratorDirect(obj)` abstract operation
		// https://tc39.es/ecma262/#sec-getiteratordirect
		getIteratorDirect = function (obj) {
		  return {
		    iterator: obj,
		    next: obj.next,
		    done: false
		  };
		};
		return getIteratorDirect;
	}

	var getSetRecord;
	var hasRequiredGetSetRecord;

	function requireGetSetRecord () {
		if (hasRequiredGetSetRecord) return getSetRecord;
		hasRequiredGetSetRecord = 1;
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var toIntegerOrInfinity = /*@__PURE__*/ requireToIntegerOrInfinity$1();
		var getIteratorDirect = /*@__PURE__*/ requireGetIteratorDirect();

		var INVALID_SIZE = 'Invalid size';
		var $RangeError = RangeError;
		var $TypeError = TypeError;
		var max = Math.max;

		var SetRecord = function (set, intSize) {
		  this.set = set;
		  this.size = max(intSize, 0);
		  this.has = aCallable(set.has);
		  this.keys = aCallable(set.keys);
		};

		SetRecord.prototype = {
		  getIterator: function () {
		    return getIteratorDirect(anObject(call(this.keys, this.set)));
		  },
		  includes: function (it) {
		    return call(this.has, this.set, it);
		  }
		};

		// `GetSetRecord` abstract operation
		// https://tc39.es/proposal-set-methods/#sec-getsetrecord
		getSetRecord = function (obj) {
		  anObject(obj);
		  var numSize = +obj.size;
		  // NOTE: If size is undefined, then numSize will be NaN
		  // eslint-disable-next-line no-self-compare -- NaN check
		  if (numSize !== numSize) throw new $TypeError(INVALID_SIZE);
		  var intSize = toIntegerOrInfinity(numSize);
		  if (intSize < 0) throw new $RangeError(INVALID_SIZE);
		  return new SetRecord(obj, intSize);
		};
		return getSetRecord;
	}

	var setDifference;
	var hasRequiredSetDifference;

	function requireSetDifference () {
		if (hasRequiredSetDifference) return setDifference;
		hasRequiredSetDifference = 1;
		var aSet = /*@__PURE__*/ requireASet();
		var SetHelpers = /*@__PURE__*/ requireSetHelpers();
		var clone = /*@__PURE__*/ requireSetClone();
		var size = /*@__PURE__*/ requireSetSize();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord();
		var iterateSet = /*@__PURE__*/ requireSetIterate();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple();

		var has = SetHelpers.has;
		var remove = SetHelpers.remove;

		// `Set.prototype.difference` method
		// https://tc39.es/ecma262/#sec-set.prototype.difference
		setDifference = function difference(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  var result = clone(O);
		  if (size(O) <= otherRec.size) iterateSet(O, function (e) {
		    if (otherRec.includes(e)) remove(result, e);
		  });
		  else iterateSimple(otherRec.getIterator(), function (e) {
		    if (has(result, e)) remove(result, e);
		  });
		  return result;
		};
		return setDifference;
	}

	var setMethodAcceptSetLike;
	var hasRequiredSetMethodAcceptSetLike;

	function requireSetMethodAcceptSetLike () {
		if (hasRequiredSetMethodAcceptSetLike) return setMethodAcceptSetLike;
		hasRequiredSetMethodAcceptSetLike = 1;
		setMethodAcceptSetLike = function () {
		  return false;
		};
		return setMethodAcceptSetLike;
	}

	var hasRequiredEs_set_difference_v2;

	function requireEs_set_difference_v2 () {
		if (hasRequiredEs_set_difference_v2) return es_set_difference_v2;
		hasRequiredEs_set_difference_v2 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var difference = /*@__PURE__*/ requireSetDifference();
		var fails = /*@__PURE__*/ requireFails$1();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike();

		var SET_LIKE_INCORRECT_BEHAVIOR = !setMethodAcceptSetLike('difference', function (result) {
		  return result.size === 0;
		});

		var FORCED = SET_LIKE_INCORRECT_BEHAVIOR || fails(function () {
		  // https://bugs.webkit.org/show_bug.cgi?id=288595
		  var setLike = {
		    size: 1,
		    has: function () { return true; },
		    keys: function () {
		      var index = 0;
		      return {
		        next: function () {
		          var done = index++ > 1;
		          if (baseSet.has(1)) baseSet.clear();
		          return { done: done, value: 2 };
		        }
		      };
		    }
		  };
		  // eslint-disable-next-line es/no-set -- testing
		  var baseSet = new Set([1, 2, 3, 4]);
		  // eslint-disable-next-line es/no-set-prototype-difference -- testing
		  return baseSet.difference(setLike).size !== 3;
		});

		// `Set.prototype.difference` method
		// https://tc39.es/ecma262/#sec-set.prototype.difference
		$({ target: 'Set', proto: true, real: true, forced: FORCED }, {
		  difference: difference
		});
		return es_set_difference_v2;
	}

	var es_set_intersection_v2 = {};

	var setIntersection;
	var hasRequiredSetIntersection;

	function requireSetIntersection () {
		if (hasRequiredSetIntersection) return setIntersection;
		hasRequiredSetIntersection = 1;
		var aSet = /*@__PURE__*/ requireASet();
		var SetHelpers = /*@__PURE__*/ requireSetHelpers();
		var size = /*@__PURE__*/ requireSetSize();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord();
		var iterateSet = /*@__PURE__*/ requireSetIterate();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple();

		var Set = SetHelpers.Set;
		var add = SetHelpers.add;
		var has = SetHelpers.has;

		// `Set.prototype.intersection` method
		// https://tc39.es/ecma262/#sec-set.prototype.intersection
		setIntersection = function intersection(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  var result = new Set();

		  if (size(O) > otherRec.size) {
		    iterateSimple(otherRec.getIterator(), function (e) {
		      if (has(O, e)) add(result, e);
		    });
		  } else {
		    iterateSet(O, function (e) {
		      if (otherRec.includes(e)) add(result, e);
		    });
		  }

		  return result;
		};
		return setIntersection;
	}

	var hasRequiredEs_set_intersection_v2;

	function requireEs_set_intersection_v2 () {
		if (hasRequiredEs_set_intersection_v2) return es_set_intersection_v2;
		hasRequiredEs_set_intersection_v2 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var intersection = /*@__PURE__*/ requireSetIntersection();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike();

		var INCORRECT = !setMethodAcceptSetLike('intersection', function (result) {
		  return result.size === 2 && result.has(1) && result.has(2);
		}) || fails(function () {
		  // eslint-disable-next-line es/no-array-from, es/no-set, es/no-set-prototype-intersection -- testing
		  return String(Array.from(new Set([1, 2, 3]).intersection(new Set([3, 2])))) !== '3,2';
		});

		// `Set.prototype.intersection` method
		// https://tc39.es/ecma262/#sec-set.prototype.intersection
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  intersection: intersection
		});
		return es_set_intersection_v2;
	}

	var es_set_isDisjointFrom_v2 = {};

	var setIsDisjointFrom;
	var hasRequiredSetIsDisjointFrom;

	function requireSetIsDisjointFrom () {
		if (hasRequiredSetIsDisjointFrom) return setIsDisjointFrom;
		hasRequiredSetIsDisjointFrom = 1;
		var aSet = /*@__PURE__*/ requireASet();
		var has = /*@__PURE__*/ requireSetHelpers().has;
		var size = /*@__PURE__*/ requireSetSize();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord();
		var iterateSet = /*@__PURE__*/ requireSetIterate();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple();
		var iteratorClose = /*@__PURE__*/ requireIteratorClose$1();

		// `Set.prototype.isDisjointFrom` method
		// https://tc39.es/ecma262/#sec-set.prototype.isdisjointfrom
		setIsDisjointFrom = function isDisjointFrom(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  if (size(O) <= otherRec.size) return iterateSet(O, function (e) {
		    if (otherRec.includes(e)) return false;
		  }, true) !== false;
		  var iterator = otherRec.getIterator();
		  return iterateSimple(iterator, function (e) {
		    if (has(O, e)) return iteratorClose(iterator, 'normal', false);
		  }) !== false;
		};
		return setIsDisjointFrom;
	}

	var hasRequiredEs_set_isDisjointFrom_v2;

	function requireEs_set_isDisjointFrom_v2 () {
		if (hasRequiredEs_set_isDisjointFrom_v2) return es_set_isDisjointFrom_v2;
		hasRequiredEs_set_isDisjointFrom_v2 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var isDisjointFrom = /*@__PURE__*/ requireSetIsDisjointFrom();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike();

		var INCORRECT = !setMethodAcceptSetLike('isDisjointFrom', function (result) {
		  return !result;
		});

		// `Set.prototype.isDisjointFrom` method
		// https://tc39.es/ecma262/#sec-set.prototype.isdisjointfrom
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  isDisjointFrom: isDisjointFrom
		});
		return es_set_isDisjointFrom_v2;
	}

	var es_set_isSubsetOf_v2 = {};

	var setIsSubsetOf;
	var hasRequiredSetIsSubsetOf;

	function requireSetIsSubsetOf () {
		if (hasRequiredSetIsSubsetOf) return setIsSubsetOf;
		hasRequiredSetIsSubsetOf = 1;
		var aSet = /*@__PURE__*/ requireASet();
		var size = /*@__PURE__*/ requireSetSize();
		var iterate = /*@__PURE__*/ requireSetIterate();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord();

		// `Set.prototype.isSubsetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issubsetof
		setIsSubsetOf = function isSubsetOf(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  if (size(O) > otherRec.size) return false;
		  return iterate(O, function (e) {
		    if (!otherRec.includes(e)) return false;
		  }, true) !== false;
		};
		return setIsSubsetOf;
	}

	var hasRequiredEs_set_isSubsetOf_v2;

	function requireEs_set_isSubsetOf_v2 () {
		if (hasRequiredEs_set_isSubsetOf_v2) return es_set_isSubsetOf_v2;
		hasRequiredEs_set_isSubsetOf_v2 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var isSubsetOf = /*@__PURE__*/ requireSetIsSubsetOf();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike();

		var INCORRECT = !setMethodAcceptSetLike('isSubsetOf', function (result) {
		  return result;
		});

		// `Set.prototype.isSubsetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issubsetof
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  isSubsetOf: isSubsetOf
		});
		return es_set_isSubsetOf_v2;
	}

	var es_set_isSupersetOf_v2 = {};

	var setIsSupersetOf;
	var hasRequiredSetIsSupersetOf;

	function requireSetIsSupersetOf () {
		if (hasRequiredSetIsSupersetOf) return setIsSupersetOf;
		hasRequiredSetIsSupersetOf = 1;
		var aSet = /*@__PURE__*/ requireASet();
		var has = /*@__PURE__*/ requireSetHelpers().has;
		var size = /*@__PURE__*/ requireSetSize();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple();
		var iteratorClose = /*@__PURE__*/ requireIteratorClose$1();

		// `Set.prototype.isSupersetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issupersetof
		setIsSupersetOf = function isSupersetOf(other) {
		  var O = aSet(this);
		  var otherRec = getSetRecord(other);
		  if (size(O) < otherRec.size) return false;
		  var iterator = otherRec.getIterator();
		  return iterateSimple(iterator, function (e) {
		    if (!has(O, e)) return iteratorClose(iterator, 'normal', false);
		  }) !== false;
		};
		return setIsSupersetOf;
	}

	var hasRequiredEs_set_isSupersetOf_v2;

	function requireEs_set_isSupersetOf_v2 () {
		if (hasRequiredEs_set_isSupersetOf_v2) return es_set_isSupersetOf_v2;
		hasRequiredEs_set_isSupersetOf_v2 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var isSupersetOf = /*@__PURE__*/ requireSetIsSupersetOf();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike();

		var INCORRECT = !setMethodAcceptSetLike('isSupersetOf', function (result) {
		  return !result;
		});

		// `Set.prototype.isSupersetOf` method
		// https://tc39.es/ecma262/#sec-set.prototype.issupersetof
		$({ target: 'Set', proto: true, real: true, forced: INCORRECT }, {
		  isSupersetOf: isSupersetOf
		});
		return es_set_isSupersetOf_v2;
	}

	var es_set_symmetricDifference_v2 = {};

	var setSymmetricDifference;
	var hasRequiredSetSymmetricDifference;

	function requireSetSymmetricDifference () {
		if (hasRequiredSetSymmetricDifference) return setSymmetricDifference;
		hasRequiredSetSymmetricDifference = 1;
		var aSet = /*@__PURE__*/ requireASet();
		var SetHelpers = /*@__PURE__*/ requireSetHelpers();
		var clone = /*@__PURE__*/ requireSetClone();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple();

		var add = SetHelpers.add;
		var has = SetHelpers.has;
		var remove = SetHelpers.remove;

		// `Set.prototype.symmetricDifference` method
		// https://tc39.es/ecma262/#sec-set.prototype.symmetricdifference
		setSymmetricDifference = function symmetricDifference(other) {
		  var O = aSet(this);
		  var keysIter = getSetRecord(other).getIterator();
		  var result = clone(O);
		  iterateSimple(keysIter, function (e) {
		    if (has(O, e)) remove(result, e);
		    else add(result, e);
		  });
		  return result;
		};
		return setSymmetricDifference;
	}

	var setMethodGetKeysBeforeCloningDetection;
	var hasRequiredSetMethodGetKeysBeforeCloningDetection;

	function requireSetMethodGetKeysBeforeCloningDetection () {
		if (hasRequiredSetMethodGetKeysBeforeCloningDetection) return setMethodGetKeysBeforeCloningDetection;
		hasRequiredSetMethodGetKeysBeforeCloningDetection = 1;
		// Should get iterator record of a set-like object before cloning this
		// https://bugs.webkit.org/show_bug.cgi?id=289430
		setMethodGetKeysBeforeCloningDetection = function (METHOD_NAME) {
		  try {
		    // eslint-disable-next-line es/no-set -- needed for test
		    var baseSet = new Set();
		    var setLike = {
		      size: 0,
		      has: function () { return true; },
		      keys: function () {
		        // eslint-disable-next-line es/no-object-defineproperty -- needed for test
		        return Object.defineProperty({}, 'next', {
		          get: function () {
		            baseSet.clear();
		            baseSet.add(4);
		            return function () {
		              return { done: true };
		            };
		          }
		        });
		      }
		    };
		    var result = baseSet[METHOD_NAME](setLike);

		    return result.size === 1 && result.values().next().value === 4;
		  } catch (error) {
		    return false;
		  }
		};
		return setMethodGetKeysBeforeCloningDetection;
	}

	var hasRequiredEs_set_symmetricDifference_v2;

	function requireEs_set_symmetricDifference_v2 () {
		if (hasRequiredEs_set_symmetricDifference_v2) return es_set_symmetricDifference_v2;
		hasRequiredEs_set_symmetricDifference_v2 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var symmetricDifference = /*@__PURE__*/ requireSetSymmetricDifference();
		var setMethodGetKeysBeforeCloning = /*@__PURE__*/ requireSetMethodGetKeysBeforeCloningDetection();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike();

		var FORCED = !setMethodAcceptSetLike('symmetricDifference') || !setMethodGetKeysBeforeCloning('symmetricDifference');

		// `Set.prototype.symmetricDifference` method
		// https://tc39.es/ecma262/#sec-set.prototype.symmetricdifference
		$({ target: 'Set', proto: true, real: true, forced: FORCED }, {
		  symmetricDifference: symmetricDifference
		});
		return es_set_symmetricDifference_v2;
	}

	var es_set_union_v2 = {};

	var setUnion;
	var hasRequiredSetUnion;

	function requireSetUnion () {
		if (hasRequiredSetUnion) return setUnion;
		hasRequiredSetUnion = 1;
		var aSet = /*@__PURE__*/ requireASet();
		var add = /*@__PURE__*/ requireSetHelpers().add;
		var clone = /*@__PURE__*/ requireSetClone();
		var getSetRecord = /*@__PURE__*/ requireGetSetRecord();
		var iterateSimple = /*@__PURE__*/ requireIterateSimple();

		// `Set.prototype.union` method
		// https://tc39.es/ecma262/#sec-set.prototype.union
		setUnion = function union(other) {
		  var O = aSet(this);
		  var keysIter = getSetRecord(other).getIterator();
		  var result = clone(O);
		  iterateSimple(keysIter, function (it) {
		    add(result, it);
		  });
		  return result;
		};
		return setUnion;
	}

	var hasRequiredEs_set_union_v2;

	function requireEs_set_union_v2 () {
		if (hasRequiredEs_set_union_v2) return es_set_union_v2;
		hasRequiredEs_set_union_v2 = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var union = /*@__PURE__*/ requireSetUnion();
		var setMethodGetKeysBeforeCloning = /*@__PURE__*/ requireSetMethodGetKeysBeforeCloningDetection();
		var setMethodAcceptSetLike = /*@__PURE__*/ requireSetMethodAcceptSetLike();

		var FORCED = !setMethodAcceptSetLike('union') || !setMethodGetKeysBeforeCloning('union');

		// `Set.prototype.union` method
		// https://tc39.es/ecma262/#sec-set.prototype.union
		$({ target: 'Set', proto: true, real: true, forced: FORCED }, {
		  union: union
		});
		return es_set_union_v2;
	}

	var set$2;
	var hasRequiredSet$2;

	function requireSet$2 () {
		if (hasRequiredSet$2) return set$2;
		hasRequiredSet$2 = 1;
		requireEs_array_iterator$1();
		requireEs_set();
		requireEs_set_difference_v2();
		requireEs_set_intersection_v2();
		requireEs_set_isDisjointFrom_v2();
		requireEs_set_isSubsetOf_v2();
		requireEs_set_isSupersetOf_v2();
		requireEs_set_symmetricDifference_v2();
		requireEs_set_union_v2();
		requireEs_string_iterator$1();
		var path = /*@__PURE__*/ requirePath$1();

		set$2 = path.Set;
		return set$2;
	}

	var set$1;
	var hasRequiredSet$1;

	function requireSet$1 () {
		if (hasRequiredSet$1) return set$1;
		hasRequiredSet$1 = 1;
		var parent = /*@__PURE__*/ requireSet$2();
		requireWeb_domCollections_iterator$1();

		set$1 = parent;
		return set$1;
	}

	var set;
	var hasRequiredSet;

	function requireSet () {
		if (hasRequiredSet) return set;
		hasRequiredSet = 1;
		set = /*@__PURE__*/ requireSet$1();
		return set;
	}

	var setExports = requireSet();
	var _Set = /*@__PURE__*/getDefaultExportFromCjs(setExports);

	/** @returns {void} */
	function noop() {}
	const identity = x => x;

	/**
	 * @template T
	 * @template S
	 * @param {T} tar
	 * @param {S} src
	 * @returns {T & S}
	 */
	function assign(tar, src) {
	  // @ts-ignore
	  for (const k in src) tar[k] = src[k];
	  return /** @type {T & S} */tar;
	}
	function run(fn) {
	  return fn();
	}
	function blank_object() {
	  return _Object$create(null);
	}

	/**
	 * @param {Function[]} fns
	 * @returns {void}
	 */
	function run_all(fns) {
	  _forEachInstanceProperty(fns).call(fns, run);
	}

	/**
	 * @param {any} thing
	 * @returns {thing is Function}
	 */
	function is_function(thing) {
	  return typeof thing === 'function';
	}

	/** @returns {boolean} */
	function safe_not_equal(a, b) {
	  return a != a ? b == b : a !== b || a && typeof a === 'object' || typeof a === 'function';
	}

	/** @returns {boolean} */
	function is_empty(obj) {
	  return _Object$keys(obj).length === 0;
	}
	function create_slot(definition, ctx, $$scope, fn) {
	  if (definition) {
	    const slot_ctx = get_slot_context(definition, ctx, $$scope, fn);
	    return definition[0](slot_ctx);
	  }
	}
	function get_slot_context(definition, ctx, $$scope, fn) {
	  var _context3;
	  return definition[1] && fn ? assign(_sliceInstanceProperty$1(_context3 = $$scope.ctx).call(_context3), definition[1](fn(ctx))) : $$scope.ctx;
	}
	function get_slot_changes(definition, $$scope, dirty, fn) {
	  if (definition[2] && fn) ;
	  return $$scope.dirty;
	}

	/** @returns {void} */
	function update_slot_base(slot, slot_definition, ctx, $$scope, slot_changes, get_slot_context_fn) {
	  if (slot_changes) {
	    const slot_context = get_slot_context(slot_definition, ctx, $$scope, get_slot_context_fn);
	    slot.p(slot_context, slot_changes);
	  }
	}

	/** @returns {any[] | -1} */
	function get_all_dirty_from_scope($$scope) {
	  if ($$scope.ctx.length > 32) {
	    const dirty = [];
	    const length = $$scope.ctx.length / 32;
	    for (let i = 0; i < length; i++) {
	      dirty[i] = -1;
	    }
	    return dirty;
	  }
	  return -1;
	}

	var es_date_now = {};

	var hasRequiredEs_date_now;

	function requireEs_date_now () {
		if (hasRequiredEs_date_now) return es_date_now;
		hasRequiredEs_date_now = 1;
		// TODO: Remove from `core-js@4`
		var $ = /*@__PURE__*/ require_export$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();

		var $Date = Date;
		var thisTimeValue = uncurryThis($Date.prototype.getTime);

		// `Date.now` method
		// https://tc39.es/ecma262/#sec-date.now
		$({ target: 'Date', stat: true }, {
		  now: function now() {
		    return thisTimeValue(new $Date());
		  }
		});
		return es_date_now;
	}

	var now$3;
	var hasRequiredNow$2;

	function requireNow$2 () {
		if (hasRequiredNow$2) return now$3;
		hasRequiredNow$2 = 1;
		requireEs_date_now();
		var path = /*@__PURE__*/ requirePath$1();

		now$3 = path.Date.now;
		return now$3;
	}

	var now$2;
	var hasRequiredNow$1;

	function requireNow$1 () {
		if (hasRequiredNow$1) return now$2;
		hasRequiredNow$1 = 1;
		var parent = /*@__PURE__*/ requireNow$2();

		now$2 = parent;
		return now$2;
	}

	var now$1;
	var hasRequiredNow;

	function requireNow () {
		if (hasRequiredNow) return now$1;
		hasRequiredNow = 1;
		now$1 = /*@__PURE__*/ requireNow$1();
		return now$1;
	}

	var nowExports = requireNow();
	var _Date$now = /*@__PURE__*/getDefaultExportFromCjs(nowExports);

	const is_client = typeof window !== 'undefined';

	/** @type {() => number} */
	let now = is_client ? () => window.performance.now() : () => _Date$now();
	let raf = is_client ? cb => requestAnimationFrame(cb) : noop;

	const tasks = new _Set();

	/**
	 * @param {number} now
	 * @returns {void}
	 */
	function run_tasks(now) {
	  _forEachInstanceProperty(tasks).call(tasks, task => {
	    if (!task.c(now)) {
	      tasks.delete(task);
	      task.f();
	    }
	  });
	  if (tasks.size !== 0) raf(run_tasks);
	}

	/**
	 * Creates a new task that runs on each raf frame
	 * until it returns a falsy value or is aborted
	 * @param {import('./private.js').TaskCallback} callback
	 * @returns {import('./private.js').Task}
	 */
	function loop(callback) {
	  /** @type {import('./private.js').TaskEntry} */
	  let task;
	  if (tasks.size === 0) raf(run_tasks);
	  return {
	    promise: new _Promise(fulfill => {
	      tasks.add(task = {
	        c: callback,
	        f: fulfill
	      });
	    }),
	    abort() {
	      tasks.delete(task);
	    }
	  };
	}

	var es_map = {};

	var es_map_constructor = {};

	var hasRequiredEs_map_constructor;

	function requireEs_map_constructor () {
		if (hasRequiredEs_map_constructor) return es_map_constructor;
		hasRequiredEs_map_constructor = 1;
		var collection = /*@__PURE__*/ requireCollection();
		var collectionStrong = /*@__PURE__*/ requireCollectionStrong();

		// `Map` constructor
		// https://tc39.es/ecma262/#sec-map-objects
		collection('Map', function (init) {
		  return function Map() { return init(this, arguments.length ? arguments[0] : undefined); };
		}, collectionStrong);
		return es_map_constructor;
	}

	var hasRequiredEs_map;

	function requireEs_map () {
		if (hasRequiredEs_map) return es_map;
		hasRequiredEs_map = 1;
		// TODO: Remove this module from `core-js@4` since it's replaced to module below
		requireEs_map_constructor();
		return es_map;
	}

	var es_map_groupBy = {};

	var mapHelpers;
	var hasRequiredMapHelpers;

	function requireMapHelpers () {
		if (hasRequiredMapHelpers) return mapHelpers;
		hasRequiredMapHelpers = 1;
		var getBuiltIn = /*@__PURE__*/ requireGetBuiltIn$1();
		var caller = /*@__PURE__*/ requireCaller();

		var Map = getBuiltIn('Map');

		mapHelpers = {
		  Map: Map,
		  set: caller('set', 2),
		  get: caller('get', 1),
		  has: caller('has', 1),
		  remove: caller('delete', 1),
		  proto: Map.prototype
		};
		return mapHelpers;
	}

	var hasRequiredEs_map_groupBy;

	function requireEs_map_groupBy () {
		if (hasRequiredEs_map_groupBy) return es_map_groupBy;
		hasRequiredEs_map_groupBy = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var requireObjectCoercible = /*@__PURE__*/ requireRequireObjectCoercible$1();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var MapHelpers = /*@__PURE__*/ requireMapHelpers();
		var IS_PURE = /*@__PURE__*/ requireIsPure$1();
		var fails = /*@__PURE__*/ requireFails$1();

		var Map = MapHelpers.Map;
		var has = MapHelpers.has;
		var get = MapHelpers.get;
		var set = MapHelpers.set;
		var push = uncurryThis([].push);

		// https://bugs.webkit.org/show_bug.cgi?id=271524
		var DOES_NOT_WORK_WITH_PRIMITIVES = IS_PURE || fails(function () {
		  return Map.groupBy('ab', function (it) {
		    return it;
		  }).get('a').length !== 1;
		});

		// `Map.groupBy` method
		// https://tc39.es/ecma262/#sec-map.groupby
		$({ target: 'Map', stat: true, forced: IS_PURE || DOES_NOT_WORK_WITH_PRIMITIVES }, {
		  groupBy: function groupBy(items, callbackfn) {
		    requireObjectCoercible(items);
		    aCallable(callbackfn);
		    var map = new Map();
		    var k = 0;
		    iterate(items, function (value) {
		      var key = callbackfn(value, k++);
		      if (!has(map, key)) set(map, key, [value]);
		      else push(get(map, key), value);
		    });
		    return map;
		  }
		});
		return es_map_groupBy;
	}

	var map$2;
	var hasRequiredMap$2;

	function requireMap$2 () {
		if (hasRequiredMap$2) return map$2;
		hasRequiredMap$2 = 1;
		requireEs_array_iterator$1();
		requireEs_map();
		requireEs_map_groupBy();
		requireEs_string_iterator$1();
		var path = /*@__PURE__*/ requirePath$1();

		map$2 = path.Map;
		return map$2;
	}

	var map$1;
	var hasRequiredMap$1;

	function requireMap$1 () {
		if (hasRequiredMap$1) return map$1;
		hasRequiredMap$1 = 1;
		var parent = /*@__PURE__*/ requireMap$2();
		requireWeb_domCollections_iterator$1();

		map$1 = parent;
		return map$1;
	}

	var map;
	var hasRequiredMap;

	function requireMap () {
		if (hasRequiredMap) return map;
		hasRequiredMap = 1;
		map = /*@__PURE__*/ requireMap$1();
		return map;
	}

	var mapExports = requireMap();
	var _Map = /*@__PURE__*/getDefaultExportFromCjs(mapExports);

	var es_array_sort = {};

	var arraySort;
	var hasRequiredArraySort;

	function requireArraySort () {
		if (hasRequiredArraySort) return arraySort;
		hasRequiredArraySort = 1;
		var arraySlice = /*@__PURE__*/ requireArraySlice$1();

		var floor = Math.floor;

		var sort = function (array, comparefn) {
		  var length = array.length;

		  if (length < 8) {
		    // insertion sort
		    var i = 1;
		    var element, j;

		    while (i < length) {
		      j = i;
		      element = array[i];
		      while (j && comparefn(array[j - 1], element) > 0) {
		        array[j] = array[--j];
		      }
		      if (j !== i++) array[j] = element;
		    }
		  } else {
		    // merge sort
		    var middle = floor(length / 2);
		    var left = sort(arraySlice(array, 0, middle), comparefn);
		    var right = sort(arraySlice(array, middle), comparefn);
		    var llength = left.length;
		    var rlength = right.length;
		    var lindex = 0;
		    var rindex = 0;

		    while (lindex < llength || rindex < rlength) {
		      array[lindex + rindex] = (lindex < llength && rindex < rlength)
		        ? comparefn(left[lindex], right[rindex]) <= 0 ? left[lindex++] : right[rindex++]
		        : lindex < llength ? left[lindex++] : right[rindex++];
		    }
		  }

		  return array;
		};

		arraySort = sort;
		return arraySort;
	}

	var environmentFfVersion;
	var hasRequiredEnvironmentFfVersion;

	function requireEnvironmentFfVersion () {
		if (hasRequiredEnvironmentFfVersion) return environmentFfVersion;
		hasRequiredEnvironmentFfVersion = 1;
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		var firefox = userAgent.match(/firefox\/(\d+)/i);

		environmentFfVersion = !!firefox && +firefox[1];
		return environmentFfVersion;
	}

	var environmentIsIeOrEdge;
	var hasRequiredEnvironmentIsIeOrEdge;

	function requireEnvironmentIsIeOrEdge () {
		if (hasRequiredEnvironmentIsIeOrEdge) return environmentIsIeOrEdge;
		hasRequiredEnvironmentIsIeOrEdge = 1;
		var UA = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		environmentIsIeOrEdge = /MSIE|Trident/.test(UA);
		return environmentIsIeOrEdge;
	}

	var environmentWebkitVersion;
	var hasRequiredEnvironmentWebkitVersion;

	function requireEnvironmentWebkitVersion () {
		if (hasRequiredEnvironmentWebkitVersion) return environmentWebkitVersion;
		hasRequiredEnvironmentWebkitVersion = 1;
		var userAgent = /*@__PURE__*/ requireEnvironmentUserAgent$1();

		var webkit = userAgent.match(/AppleWebKit\/(\d+)\./);

		environmentWebkitVersion = !!webkit && +webkit[1];
		return environmentWebkitVersion;
	}

	var hasRequiredEs_array_sort;

	function requireEs_array_sort () {
		if (hasRequiredEs_array_sort) return es_array_sort;
		hasRequiredEs_array_sort = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var aCallable = /*@__PURE__*/ requireACallable$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();
		var deletePropertyOrThrow = /*@__PURE__*/ requireDeletePropertyOrThrow();
		var toString = /*@__PURE__*/ requireToString$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var internalSort = /*@__PURE__*/ requireArraySort();
		var arrayMethodIsStrict = /*@__PURE__*/ requireArrayMethodIsStrict();
		var FF = /*@__PURE__*/ requireEnvironmentFfVersion();
		var IE_OR_EDGE = /*@__PURE__*/ requireEnvironmentIsIeOrEdge();
		var V8 = /*@__PURE__*/ requireEnvironmentV8Version$1();
		var WEBKIT = /*@__PURE__*/ requireEnvironmentWebkitVersion();

		var test = [];
		var nativeSort = uncurryThis(test.sort);
		var push = uncurryThis(test.push);

		// IE8-
		var FAILS_ON_UNDEFINED = fails(function () {
		  test.sort(undefined);
		});
		// V8 bug
		var FAILS_ON_NULL = fails(function () {
		  test.sort(null);
		});
		// Old WebKit
		var STRICT_METHOD = arrayMethodIsStrict('sort');

		var STABLE_SORT = !fails(function () {
		  // feature detection can be too slow, so check engines versions
		  if (V8) return V8 < 70;
		  if (FF && FF > 3) return;
		  if (IE_OR_EDGE) return true;
		  if (WEBKIT) return WEBKIT < 603;

		  var result = '';
		  var code, chr, value, index;

		  // generate an array with more 512 elements (Chakra and old V8 fails only in this case)
		  for (code = 65; code < 76; code++) {
		    chr = String.fromCharCode(code);

		    switch (code) {
		      case 66: case 69: case 70: case 72: value = 3; break;
		      case 68: case 71: value = 4; break;
		      default: value = 2;
		    }

		    for (index = 0; index < 47; index++) {
		      test.push({ k: chr + index, v: value });
		    }
		  }

		  test.sort(function (a, b) { return b.v - a.v; });

		  for (index = 0; index < test.length; index++) {
		    chr = test[index].k.charAt(0);
		    if (result.charAt(result.length - 1) !== chr) result += chr;
		  }

		  return result !== 'DGBEFHACIJK';
		});

		var FORCED = FAILS_ON_UNDEFINED || !FAILS_ON_NULL || !STRICT_METHOD || !STABLE_SORT;

		var getSortCompare = function (comparefn) {
		  return function (x, y) {
		    if (y === undefined) return -1;
		    if (x === undefined) return 1;
		    if (comparefn !== undefined) return +comparefn(x, y) || 0;
		    return toString(x) > toString(y) ? 1 : -1;
		  };
		};

		// `Array.prototype.sort` method
		// https://tc39.es/ecma262/#sec-array.prototype.sort
		$({ target: 'Array', proto: true, forced: FORCED }, {
		  sort: function sort(comparefn) {
		    if (comparefn !== undefined) aCallable(comparefn);

		    var array = toObject(this);

		    if (STABLE_SORT) return comparefn === undefined ? nativeSort(array) : nativeSort(array, comparefn);

		    var items = [];
		    var arrayLength = lengthOfArrayLike(array);
		    var itemsLength, index;

		    for (index = 0; index < arrayLength; index++) {
		      if (index in array) push(items, array[index]);
		    }

		    internalSort(items, getSortCompare(comparefn));

		    itemsLength = lengthOfArrayLike(items);
		    index = 0;

		    while (index < itemsLength) array[index] = items[index++];
		    while (index < arrayLength) deletePropertyOrThrow(array, index++);

		    return array;
		  }
		});
		return es_array_sort;
	}

	var sort$3;
	var hasRequiredSort$3;

	function requireSort$3 () {
		if (hasRequiredSort$3) return sort$3;
		hasRequiredSort$3 = 1;
		requireEs_array_sort();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		sort$3 = getBuiltInPrototypeMethod('Array', 'sort');
		return sort$3;
	}

	var sort$2;
	var hasRequiredSort$2;

	function requireSort$2 () {
		if (hasRequiredSort$2) return sort$2;
		hasRequiredSort$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireSort$3();

		var ArrayPrototype = Array.prototype;

		sort$2 = function (it) {
		  var own = it.sort;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.sort) ? method : own;
		};
		return sort$2;
	}

	var sort$1;
	var hasRequiredSort$1;

	function requireSort$1 () {
		if (hasRequiredSort$1) return sort$1;
		hasRequiredSort$1 = 1;
		var parent = /*@__PURE__*/ requireSort$2();

		sort$1 = parent;
		return sort$1;
	}

	var sort;
	var hasRequiredSort;

	function requireSort () {
		if (hasRequiredSort) return sort;
		hasRequiredSort = 1;
		sort = /*@__PURE__*/ requireSort$1();
		return sort;
	}

	var sortExports = requireSort();
	var _sortInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(sortExports);

	var es_array_from = {};

	var callWithSafeIterationClosing;
	var hasRequiredCallWithSafeIterationClosing;

	function requireCallWithSafeIterationClosing () {
		if (hasRequiredCallWithSafeIterationClosing) return callWithSafeIterationClosing;
		hasRequiredCallWithSafeIterationClosing = 1;
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var iteratorClose = /*@__PURE__*/ requireIteratorClose$1();

		// call something on iterator step with safe closing on error
		callWithSafeIterationClosing = function (iterator, fn, value, ENTRIES) {
		  try {
		    return ENTRIES ? fn(anObject(value)[0], value[1]) : fn(value);
		  } catch (error) {
		    iteratorClose(iterator, 'throw', error);
		  }
		};
		return callWithSafeIterationClosing;
	}

	var arrayFrom;
	var hasRequiredArrayFrom;

	function requireArrayFrom () {
		if (hasRequiredArrayFrom) return arrayFrom;
		hasRequiredArrayFrom = 1;
		var bind = /*@__PURE__*/ requireFunctionBindContext$1();
		var call = /*@__PURE__*/ requireFunctionCall$1();
		var toObject = /*@__PURE__*/ requireToObject$1();
		var callWithSafeIterationClosing = /*@__PURE__*/ requireCallWithSafeIterationClosing();
		var isArrayIteratorMethod = /*@__PURE__*/ requireIsArrayIteratorMethod$1();
		var isConstructor = /*@__PURE__*/ requireIsConstructor$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();
		var createProperty = /*@__PURE__*/ requireCreateProperty$1();
		var getIterator = /*@__PURE__*/ requireGetIterator$1();
		var getIteratorMethod = /*@__PURE__*/ requireGetIteratorMethod$1();

		var $Array = Array;

		// `Array.from` method implementation
		// https://tc39.es/ecma262/#sec-array.from
		arrayFrom = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
		  var O = toObject(arrayLike);
		  var IS_CONSTRUCTOR = isConstructor(this);
		  var argumentsLength = arguments.length;
		  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
		  var mapping = mapfn !== undefined;
		  if (mapping) mapfn = bind(mapfn, argumentsLength > 2 ? arguments[2] : undefined);
		  var iteratorMethod = getIteratorMethod(O);
		  var index = 0;
		  var length, result, step, iterator, next, value;
		  // if the target is not iterable or it's an array with the default iterator - use a simple case
		  if (iteratorMethod && !(this === $Array && isArrayIteratorMethod(iteratorMethod))) {
		    result = IS_CONSTRUCTOR ? new this() : [];
		    iterator = getIterator(O, iteratorMethod);
		    next = iterator.next;
		    for (;!(step = call(next, iterator)).done; index++) {
		      value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
		      createProperty(result, index, value);
		    }
		  } else {
		    length = lengthOfArrayLike(O);
		    result = IS_CONSTRUCTOR ? new this(length) : $Array(length);
		    for (;length > index; index++) {
		      value = mapping ? mapfn(O[index], index) : O[index];
		      createProperty(result, index, value);
		    }
		  }
		  result.length = index;
		  return result;
		};
		return arrayFrom;
	}

	var hasRequiredEs_array_from;

	function requireEs_array_from () {
		if (hasRequiredEs_array_from) return es_array_from;
		hasRequiredEs_array_from = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var from = /*@__PURE__*/ requireArrayFrom();
		var checkCorrectnessOfIteration = /*@__PURE__*/ requireCheckCorrectnessOfIteration();

		var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
		  // eslint-disable-next-line es/no-array-from -- required for testing
		  Array.from(iterable);
		});

		// `Array.from` method
		// https://tc39.es/ecma262/#sec-array.from
		$({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
		  from: from
		});
		return es_array_from;
	}

	var from$2;
	var hasRequiredFrom$2;

	function requireFrom$2 () {
		if (hasRequiredFrom$2) return from$2;
		hasRequiredFrom$2 = 1;
		requireEs_string_iterator$1();
		requireEs_array_from();
		var path = /*@__PURE__*/ requirePath$1();

		from$2 = path.Array.from;
		return from$2;
	}

	var from$1;
	var hasRequiredFrom$1;

	function requireFrom$1 () {
		if (hasRequiredFrom$1) return from$1;
		hasRequiredFrom$1 = 1;
		var parent = /*@__PURE__*/ requireFrom$2();

		from$1 = parent;
		return from$1;
	}

	var from;
	var hasRequiredFrom;

	function requireFrom () {
		if (hasRequiredFrom) return from;
		hasRequiredFrom = 1;
		from = /*@__PURE__*/ requireFrom$1();
		return from;
	}

	var fromExports = requireFrom();
	var _Array$from = /*@__PURE__*/getDefaultExportFromCjs(fromExports);

	var es_weakMap = {};

	var es_weakMap_constructor = {};

	var collectionWeak;
	var hasRequiredCollectionWeak;

	function requireCollectionWeak () {
		if (hasRequiredCollectionWeak) return collectionWeak;
		hasRequiredCollectionWeak = 1;
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var defineBuiltIns = /*@__PURE__*/ requireDefineBuiltIns();
		var getWeakData = /*@__PURE__*/ requireInternalMetadata().getWeakData;
		var anInstance = /*@__PURE__*/ requireAnInstance$1();
		var anObject = /*@__PURE__*/ requireAnObject$1();
		var isNullOrUndefined = /*@__PURE__*/ requireIsNullOrUndefined$1();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var iterate = /*@__PURE__*/ requireIterate$1();
		var ArrayIterationModule = /*@__PURE__*/ requireArrayIteration$1();
		var hasOwn = /*@__PURE__*/ requireHasOwnProperty$1();
		var InternalStateModule = /*@__PURE__*/ requireInternalState$1();

		var setInternalState = InternalStateModule.set;
		var internalStateGetterFor = InternalStateModule.getterFor;
		var find = ArrayIterationModule.find;
		var findIndex = ArrayIterationModule.findIndex;
		var splice = uncurryThis([].splice);
		var id = 0;

		// fallback for uncaught frozen keys
		var uncaughtFrozenStore = function (state) {
		  return state.frozen || (state.frozen = new UncaughtFrozenStore());
		};

		var UncaughtFrozenStore = function () {
		  this.entries = [];
		};

		var findUncaughtFrozen = function (store, key) {
		  return find(store.entries, function (it) {
		    return it[0] === key;
		  });
		};

		UncaughtFrozenStore.prototype = {
		  get: function (key) {
		    var entry = findUncaughtFrozen(this, key);
		    if (entry) return entry[1];
		  },
		  has: function (key) {
		    return !!findUncaughtFrozen(this, key);
		  },
		  set: function (key, value) {
		    var entry = findUncaughtFrozen(this, key);
		    if (entry) entry[1] = value;
		    else this.entries.push([key, value]);
		  },
		  'delete': function (key) {
		    var index = findIndex(this.entries, function (it) {
		      return it[0] === key;
		    });
		    if (~index) splice(this.entries, index, 1);
		    return !!~index;
		  }
		};

		collectionWeak = {
		  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
		    var Constructor = wrapper(function (that, iterable) {
		      anInstance(that, Prototype);
		      setInternalState(that, {
		        type: CONSTRUCTOR_NAME,
		        id: id++,
		        frozen: null
		      });
		      if (!isNullOrUndefined(iterable)) iterate(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
		    });

		    var Prototype = Constructor.prototype;

		    var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

		    var define = function (that, key, value) {
		      var state = getInternalState(that);
		      var data = getWeakData(anObject(key), true);
		      if (data === true) uncaughtFrozenStore(state).set(key, value);
		      else data[state.id] = value;
		      return that;
		    };

		    defineBuiltIns(Prototype, {
		      // `{ WeakMap, WeakSet }.prototype.delete(key)` methods
		      // https://tc39.es/ecma262/#sec-weakmap.prototype.delete
		      // https://tc39.es/ecma262/#sec-weakset.prototype.delete
		      'delete': function (key) {
		        var state = getInternalState(this);
		        if (!isObject(key)) return false;
		        var data = getWeakData(key);
		        if (data === true) return uncaughtFrozenStore(state)['delete'](key);
		        return data && hasOwn(data, state.id) && delete data[state.id];
		      },
		      // `{ WeakMap, WeakSet }.prototype.has(key)` methods
		      // https://tc39.es/ecma262/#sec-weakmap.prototype.has
		      // https://tc39.es/ecma262/#sec-weakset.prototype.has
		      has: function has(key) {
		        var state = getInternalState(this);
		        if (!isObject(key)) return false;
		        var data = getWeakData(key);
		        if (data === true) return uncaughtFrozenStore(state).has(key);
		        return data && hasOwn(data, state.id);
		      }
		    });

		    defineBuiltIns(Prototype, IS_MAP ? {
		      // `WeakMap.prototype.get(key)` method
		      // https://tc39.es/ecma262/#sec-weakmap.prototype.get
		      get: function get(key) {
		        var state = getInternalState(this);
		        if (isObject(key)) {
		          var data = getWeakData(key);
		          if (data === true) return uncaughtFrozenStore(state).get(key);
		          if (data) return data[state.id];
		        }
		      },
		      // `WeakMap.prototype.set(key, value)` method
		      // https://tc39.es/ecma262/#sec-weakmap.prototype.set
		      set: function set(key, value) {
		        return define(this, key, value);
		      }
		    } : {
		      // `WeakSet.prototype.add(value)` method
		      // https://tc39.es/ecma262/#sec-weakset.prototype.add
		      add: function add(value) {
		        return define(this, value, true);
		      }
		    });

		    return Constructor;
		  }
		};
		return collectionWeak;
	}

	var hasRequiredEs_weakMap_constructor;

	function requireEs_weakMap_constructor () {
		if (hasRequiredEs_weakMap_constructor) return es_weakMap_constructor;
		hasRequiredEs_weakMap_constructor = 1;
		var FREEZING = /*@__PURE__*/ requireFreezing();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var defineBuiltIns = /*@__PURE__*/ requireDefineBuiltIns();
		var InternalMetadataModule = /*@__PURE__*/ requireInternalMetadata();
		var collection = /*@__PURE__*/ requireCollection();
		var collectionWeak = /*@__PURE__*/ requireCollectionWeak();
		var isObject = /*@__PURE__*/ requireIsObject$1();
		var enforceInternalState = /*@__PURE__*/ requireInternalState$1().enforce;
		var fails = /*@__PURE__*/ requireFails$1();
		var NATIVE_WEAK_MAP = /*@__PURE__*/ requireWeakMapBasicDetection$1();

		var $Object = Object;
		// eslint-disable-next-line es/no-array-isarray -- safe
		var isArray = Array.isArray;
		// eslint-disable-next-line es/no-object-isextensible -- safe
		var isExtensible = $Object.isExtensible;
		// eslint-disable-next-line es/no-object-isfrozen -- safe
		var isFrozen = $Object.isFrozen;
		// eslint-disable-next-line es/no-object-issealed -- safe
		var isSealed = $Object.isSealed;
		// eslint-disable-next-line es/no-object-freeze -- safe
		var freeze = $Object.freeze;
		// eslint-disable-next-line es/no-object-seal -- safe
		var seal = $Object.seal;

		var IS_IE11 = !globalThis.ActiveXObject && 'ActiveXObject' in globalThis;
		var InternalWeakMap;

		var wrapper = function (init) {
		  return function WeakMap() {
		    return init(this, arguments.length ? arguments[0] : undefined);
		  };
		};

		// `WeakMap` constructor
		// https://tc39.es/ecma262/#sec-weakmap-constructor
		var $WeakMap = collection('WeakMap', wrapper, collectionWeak);
		var WeakMapPrototype = $WeakMap.prototype;
		var nativeSet = uncurryThis(WeakMapPrototype.set);

		// Chakra Edge bug: adding frozen arrays to WeakMap unfreeze them
		var hasMSEdgeFreezingBug = function () {
		  return FREEZING && fails(function () {
		    var frozenArray = freeze([]);
		    nativeSet(new $WeakMap(), frozenArray, 1);
		    return !isFrozen(frozenArray);
		  });
		};

		// IE11 WeakMap frozen keys fix
		// We can't use feature detection because it crash some old IE builds
		// https://github.com/zloirock/core-js/issues/485
		if (NATIVE_WEAK_MAP) if (IS_IE11) {
		  InternalWeakMap = collectionWeak.getConstructor(wrapper, 'WeakMap', true);
		  InternalMetadataModule.enable();
		  var nativeDelete = uncurryThis(WeakMapPrototype['delete']);
		  var nativeHas = uncurryThis(WeakMapPrototype.has);
		  var nativeGet = uncurryThis(WeakMapPrototype.get);
		  defineBuiltIns(WeakMapPrototype, {
		    'delete': function (key) {
		      if (isObject(key) && !isExtensible(key)) {
		        var state = enforceInternalState(this);
		        if (!state.frozen) state.frozen = new InternalWeakMap();
		        return nativeDelete(this, key) || state.frozen['delete'](key);
		      } return nativeDelete(this, key);
		    },
		    has: function has(key) {
		      if (isObject(key) && !isExtensible(key)) {
		        var state = enforceInternalState(this);
		        if (!state.frozen) state.frozen = new InternalWeakMap();
		        return nativeHas(this, key) || state.frozen.has(key);
		      } return nativeHas(this, key);
		    },
		    get: function get(key) {
		      if (isObject(key) && !isExtensible(key)) {
		        var state = enforceInternalState(this);
		        if (!state.frozen) state.frozen = new InternalWeakMap();
		        return nativeHas(this, key) ? nativeGet(this, key) : state.frozen.get(key);
		      } return nativeGet(this, key);
		    },
		    set: function set(key, value) {
		      if (isObject(key) && !isExtensible(key)) {
		        var state = enforceInternalState(this);
		        if (!state.frozen) state.frozen = new InternalWeakMap();
		        nativeHas(this, key) ? nativeSet(this, key, value) : state.frozen.set(key, value);
		      } else nativeSet(this, key, value);
		      return this;
		    }
		  });
		// Chakra Edge frozen keys fix
		} else if (hasMSEdgeFreezingBug()) {
		  defineBuiltIns(WeakMapPrototype, {
		    set: function set(key, value) {
		      var arrayIntegrityLevel;
		      if (isArray(key)) {
		        if (isFrozen(key)) arrayIntegrityLevel = freeze;
		        else if (isSealed(key)) arrayIntegrityLevel = seal;
		      }
		      nativeSet(this, key, value);
		      if (arrayIntegrityLevel) arrayIntegrityLevel(key);
		      return this;
		    }
		  });
		}
		return es_weakMap_constructor;
	}

	var hasRequiredEs_weakMap;

	function requireEs_weakMap () {
		if (hasRequiredEs_weakMap) return es_weakMap;
		hasRequiredEs_weakMap = 1;
		// TODO: Remove this module from `core-js@4` since it's replaced to module below
		requireEs_weakMap_constructor();
		return es_weakMap;
	}

	var weakMap$2;
	var hasRequiredWeakMap$2;

	function requireWeakMap$2 () {
		if (hasRequiredWeakMap$2) return weakMap$2;
		hasRequiredWeakMap$2 = 1;
		requireEs_array_iterator$1();
		requireEs_weakMap();
		var path = /*@__PURE__*/ requirePath$1();

		weakMap$2 = path.WeakMap;
		return weakMap$2;
	}

	var weakMap$1;
	var hasRequiredWeakMap$1;

	function requireWeakMap$1 () {
		if (hasRequiredWeakMap$1) return weakMap$1;
		hasRequiredWeakMap$1 = 1;
		var parent = /*@__PURE__*/ requireWeakMap$2();
		requireWeb_domCollections_iterator$1();

		weakMap$1 = parent;
		return weakMap$1;
	}

	var weakMap;
	var hasRequiredWeakMap;

	function requireWeakMap () {
		if (hasRequiredWeakMap) return weakMap;
		hasRequiredWeakMap = 1;
		weakMap = /*@__PURE__*/ requireWeakMap$1();
		return weakMap;
	}

	var weakMapExports = requireWeakMap();
	var _WeakMap = /*@__PURE__*/getDefaultExportFromCjs(weakMapExports);

	var esnext_globalThis = {};

	var es_globalThis = {};

	var hasRequiredEs_globalThis;

	function requireEs_globalThis () {
		if (hasRequiredEs_globalThis) return es_globalThis;
		hasRequiredEs_globalThis = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var globalThis = /*@__PURE__*/ requireGlobalThis$7();

		// `globalThis` object
		// https://tc39.es/ecma262/#sec-globalthis
		$({ global: true, forced: globalThis.globalThis !== globalThis }, {
		  globalThis: globalThis
		});
		return es_globalThis;
	}

	var hasRequiredEsnext_globalThis;

	function requireEsnext_globalThis () {
		if (hasRequiredEsnext_globalThis) return esnext_globalThis;
		hasRequiredEsnext_globalThis = 1;
		// TODO: Remove from `core-js@4`
		requireEs_globalThis();
		return esnext_globalThis;
	}

	var globalThis$6;
	var hasRequiredGlobalThis$5;

	function requireGlobalThis$5 () {
		if (hasRequiredGlobalThis$5) return globalThis$6;
		hasRequiredGlobalThis$5 = 1;
		requireEs_globalThis();

		globalThis$6 = /*@__PURE__*/ requireGlobalThis$7();
		return globalThis$6;
	}

	var globalThis$5;
	var hasRequiredGlobalThis$4;

	function requireGlobalThis$4 () {
		if (hasRequiredGlobalThis$4) return globalThis$5;
		hasRequiredGlobalThis$4 = 1;
		var parent = /*@__PURE__*/ requireGlobalThis$5();

		globalThis$5 = parent;
		return globalThis$5;
	}

	var globalThis$4;
	var hasRequiredGlobalThis$3;

	function requireGlobalThis$3 () {
		if (hasRequiredGlobalThis$3) return globalThis$4;
		hasRequiredGlobalThis$3 = 1;
		var parent = /*@__PURE__*/ requireGlobalThis$4();

		globalThis$4 = parent;
		return globalThis$4;
	}

	var globalThis$3;
	var hasRequiredGlobalThis$2;

	function requireGlobalThis$2 () {
		if (hasRequiredGlobalThis$2) return globalThis$3;
		hasRequiredGlobalThis$2 = 1;
		// TODO: remove from `core-js@4`
		requireEsnext_globalThis();

		var parent = /*@__PURE__*/ requireGlobalThis$3();

		globalThis$3 = parent;
		return globalThis$3;
	}

	var globalThis$2;
	var hasRequiredGlobalThis$1;

	function requireGlobalThis$1 () {
		if (hasRequiredGlobalThis$1) return globalThis$2;
		hasRequiredGlobalThis$1 = 1;
		globalThis$2 = /*@__PURE__*/ requireGlobalThis$2();
		return globalThis$2;
	}

	var globalThis$1;
	var hasRequiredGlobalThis;

	function requireGlobalThis () {
		if (hasRequiredGlobalThis) return globalThis$1;
		hasRequiredGlobalThis = 1;
		globalThis$1 = /*@__PURE__*/ requireGlobalThis$1();
		return globalThis$1;
	}

	var globalThisExports = requireGlobalThis();
	var _globalThis = /*@__PURE__*/getDefaultExportFromCjs(globalThisExports);

	/** @type {typeof globalThis} */
	const globals = typeof window !== 'undefined' ? window : typeof _globalThis !== 'undefined' ? _globalThis :
	// @ts-ignore Node typings have this
	global;

	// Needs to be written like this to pass the tree-shake-test
	'WeakMap' in globals ? new _WeakMap() : undefined;

	/**
	 * @param {Node} target
	 * @param {Node} node
	 * @returns {void}
	 */
	function append(target, node) {
	  target.appendChild(node);
	}

	/**
	 * @param {Node} node
	 * @returns {ShadowRoot | Document}
	 */
	function get_root_for_style(node) {
	  if (!node) return document;
	  const root = node.getRootNode ? node.getRootNode() : node.ownerDocument;
	  if (root && /** @type {ShadowRoot} */root.host) {
	    return /** @type {ShadowRoot} */root;
	  }
	  return node.ownerDocument;
	}

	/**
	 * @param {Node} node
	 * @returns {CSSStyleSheet}
	 */
	function append_empty_stylesheet(node) {
	  const style_element = element('style');
	  // For transitions to work without 'style-src: unsafe-inline' Content Security Policy,
	  // these empty tags need to be allowed with a hash as a workaround until we move to the Web Animations API.
	  // Using the hash for the empty string (for an empty tag) works in all browsers except Safari.
	  // So as a workaround for the workaround, when we append empty style tags we set their content to /* empty */.
	  // The hash 'sha256-9OlNO0DNEeaVzHL4RZwCLsBHA8WBQ8toBp/4F5XV2nc=' will then work even in Safari.
	  style_element.textContent = '/* empty */';
	  append_stylesheet(get_root_for_style(node), style_element);
	  return style_element.sheet;
	}

	/**
	 * @param {ShadowRoot | Document} node
	 * @param {HTMLStyleElement} style
	 * @returns {CSSStyleSheet}
	 */
	function append_stylesheet(node, style) {
	  append(/** @type {Document} */node.head || node, style);
	  return style.sheet;
	}

	/**
	 * @param {Node} target
	 * @param {Node} node
	 * @param {Node} [anchor]
	 * @returns {void}
	 */
	function insert(target, node, anchor) {
	  target.insertBefore(node, anchor || null);
	}

	/**
	 * @param {Node} node
	 * @returns {void}
	 */
	function detach(node) {
	  if (node.parentNode) {
	    node.parentNode.removeChild(node);
	  }
	}

	/**
	 * @returns {void} */
	function destroy_each(iterations, detaching) {
	  for (let i = 0; i < iterations.length; i += 1) {
	    if (iterations[i]) iterations[i].d(detaching);
	  }
	}

	/**
	 * @template {keyof HTMLElementTagNameMap} K
	 * @param {K} name
	 * @returns {HTMLElementTagNameMap[K]}
	 */
	function element(name) {
	  return document.createElement(name);
	}

	/**
	 * @template {keyof SVGElementTagNameMap} K
	 * @param {K} name
	 * @returns {SVGElement}
	 */
	function svg_element(name) {
	  return document.createElementNS('http://www.w3.org/2000/svg', name);
	}

	/**
	 * @param {string} data
	 * @returns {Text}
	 */
	function text(data) {
	  return document.createTextNode(data);
	}

	/**
	 * @returns {Text} */
	function space() {
	  return text(' ');
	}

	/**
	 * @returns {Text} */
	function empty() {
	  return text('');
	}

	/**
	 * @param {EventTarget} node
	 * @param {string} event
	 * @param {EventListenerOrEventListenerObject} handler
	 * @param {boolean | AddEventListenerOptions | EventListenerOptions} [options]
	 * @returns {() => void}
	 */
	function listen(node, event, handler, options) {
	  node.addEventListener(event, handler, options);
	  return () => node.removeEventListener(event, handler, options);
	}

	/**
	 * @returns {(event: any) => any} */
	function stop_propagation(fn) {
	  return function (event) {
	    event.stopPropagation();
	    // @ts-ignore
	    return fn.call(this, event);
	  };
	}

	/**
	 * @param {Element} node
	 * @param {string} attribute
	 * @param {string} [value]
	 * @returns {void}
	 */
	function attr(node, attribute, value) {
	  if (value == null) node.removeAttribute(attribute);else if (node.getAttribute(attribute) !== value) node.setAttribute(attribute, value);
	}

	/**
	 * @param {Element} element
	 * @returns {ChildNode[]}
	 */
	function children(element) {
	  return _Array$from(element.childNodes);
	}

	/**
	 * @param {Text} text
	 * @param {unknown} data
	 * @returns {void}
	 */
	function set_data(text, data) {
	  data = '' + data;
	  if (text.data === data) return;
	  text.data = /** @type {string} */data;
	}

	/**
	 * @returns {void} */
	function set_input_value(input, value) {
	  input.value = value == null ? '' : value;
	}

	/**
	 * @returns {void} */
	function select_option(select, value, mounting) {
	  for (let i = 0; i < select.options.length; i += 1) {
	    const option = select.options[i];
	    if (option.__value === value) {
	      option.selected = true;
	      return;
	    }
	  }
	  if (!mounting || value !== undefined) {
	    select.selectedIndex = -1; // no option should be selected
	  }
	}
	function select_value(select) {
	  const selected_option = select.querySelector(':checked');
	  return selected_option && selected_option.__value;
	}

	/**
	 * @returns {void} */
	function toggle_class(element, name, toggle) {
	  // The `!!` is required because an `undefined` flag means flipping the current state.
	  element.classList.toggle(name, !!toggle);
	}

	/**
	 * @template T
	 * @param {string} type
	 * @param {T} [detail]
	 * @param {{ bubbles?: boolean, cancelable?: boolean }} [options]
	 * @returns {CustomEvent<T>}
	 */
	function custom_event(type, detail) {
	  let {
	    bubbles = false,
	    cancelable = false
	  } = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
	  return new CustomEvent(type, {
	    detail,
	    bubbles,
	    cancelable
	  });
	}

	/**
	 * @typedef {Node & {
	 * 	claim_order?: number;
	 * 	hydrate_init?: true;
	 * 	actual_end_child?: NodeEx;
	 * 	childNodes: NodeListOf<NodeEx>;
	 * }} NodeEx
	 */

	/** @typedef {ChildNode & NodeEx} ChildNodeEx */

	/** @typedef {NodeEx & { claim_order: number }} NodeEx2 */

	/**
	 * @typedef {ChildNodeEx[] & {
	 * 	claim_info?: {
	 * 		last_index: number;
	 * 		total_claimed: number;
	 * 	};
	 * }} ChildNodeArray
	 */

	// we need to store the information for multiple documents because a Svelte application could also contain iframes
	// https://github.com/sveltejs/svelte/issues/3624
	/** @type {Map<Document | ShadowRoot, import('./private.d.ts').StyleInformation>} */
	const managed_styles = new _Map();
	let active = 0;

	// https://github.com/darkskyapp/string-hash/blob/master/index.js
	/**
	 * @param {string} str
	 * @returns {number}
	 */
	function hash(str) {
	  let hash = 5381;
	  let i = str.length;
	  while (i--) hash = (hash << 5) - hash ^ str.charCodeAt(i);
	  return hash >>> 0;
	}

	/**
	 * @param {Document | ShadowRoot} doc
	 * @param {Element & ElementCSSInlineStyle} node
	 * @returns {{ stylesheet: any; rules: {}; }}
	 */
	function create_style_information(doc, node) {
	  const info = {
	    stylesheet: append_empty_stylesheet(node),
	    rules: {}
	  };
	  managed_styles.set(doc, info);
	  return info;
	}

	/**
	 * @param {Element & ElementCSSInlineStyle} node
	 * @param {number} a
	 * @param {number} b
	 * @param {number} duration
	 * @param {number} delay
	 * @param {(t: number) => number} ease
	 * @param {(t: number, u: number) => string} fn
	 * @param {number} uid
	 * @returns {string}
	 */
	function create_rule(node, a, b, duration, delay, ease, fn) {
	  let uid = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : 0;
	  const step = 16.666 / duration;
	  let keyframes = '{\n';
	  for (let p = 0; p <= 1; p += step) {
	    const t = a + (b - a) * ease(p);
	    keyframes += p * 100 + `%{${fn(t, 1 - t)}}\n`;
	  }
	  const rule = keyframes + `100% {${fn(b, 1 - b)}}\n}`;
	  const name = `__svelte_${hash(rule)}_${uid}`;
	  const doc = get_root_for_style(node);
	  const {
	    stylesheet,
	    rules
	  } = managed_styles.get(doc) || create_style_information(doc, node);
	  if (!rules[name]) {
	    rules[name] = true;
	    stylesheet.insertRule(`@keyframes ${name} ${rule}`, stylesheet.cssRules.length);
	  }
	  const animation = node.style.animation || '';
	  node.style.animation = `${animation ? `${animation}, ` : ''}${name} ${duration}ms linear ${delay}ms 1 both`;
	  active += 1;
	  return name;
	}

	/**
	 * @param {Element & ElementCSSInlineStyle} node
	 * @param {string} [name]
	 * @returns {void}
	 */
	function delete_rule(node, name) {
	  const previous = (node.style.animation || '').split(', ');
	  const next = _filterInstanceProperty$1(previous).call(previous, name ? anim => _indexOfInstanceProperty(anim).call(anim, name) < 0 // remove specific animation
	  : anim => _indexOfInstanceProperty(anim).call(anim, '__svelte') === -1 // remove all Svelte animations
	  );
	  const deleted = previous.length - next.length;
	  if (deleted) {
	    node.style.animation = next.join(', ');
	    active -= deleted;
	    if (!active) clear_rules();
	  }
	}

	/** @returns {void} */
	function clear_rules() {
	  raf(() => {
	    if (active) return;
	    _forEachInstanceProperty(managed_styles).call(managed_styles, info => {
	      const {
	        ownerNode
	      } = info.stylesheet;
	      // there is no ownerNode if it runs on jsdom.
	      if (ownerNode) detach(ownerNode);
	    });
	    managed_styles.clear();
	  });
	}

	let current_component;

	/** @returns {void} */
	function set_current_component(component) {
	  current_component = component;
	}
	function get_current_component() {
	  if (!current_component) throw new Error('Function called outside component initialization');
	  return current_component;
	}

	/**
	 * Creates an event dispatcher that can be used to dispatch [component events](https://svelte.dev/docs#template-syntax-component-directives-on-eventname).
	 * Event dispatchers are functions that can take two arguments: `name` and `detail`.
	 *
	 * Component events created with `createEventDispatcher` create a
	 * [CustomEvent](https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent).
	 * These events do not [bubble](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Building_blocks/Events#Event_bubbling_and_capture).
	 * The `detail` argument corresponds to the [CustomEvent.detail](https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/detail)
	 * property and can contain any type of data.
	 *
	 * The event dispatcher can be typed to narrow the allowed event names and the type of the `detail` argument:
	 * ```ts
	 * const dispatch = createEventDispatcher<{
	 *  loaded: never; // does not take a detail argument
	 *  change: string; // takes a detail argument of type string, which is required
	 *  optional: number | null; // takes an optional detail argument of type number
	 * }>();
	 * ```
	 *
	 * https://svelte.dev/docs/svelte#createeventdispatcher
	 * @template {Record<string, any>} [EventMap=any]
	 * @returns {import('./public.js').EventDispatcher<EventMap>}
	 */
	function createEventDispatcher() {
	  const component = get_current_component();
	  return function (type, detail) {
	    let {
	      cancelable = false
	    } = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
	    const callbacks = component.$$.callbacks[type];
	    if (callbacks) {
	      var _context;
	      // TODO are there situations where events could be dispatched
	      // in a server (non-DOM) environment?
	      const event = custom_event(/** @type {string} */type, detail, {
	        cancelable
	      });
	      _forEachInstanceProperty(_context = _sliceInstanceProperty$1(callbacks).call(callbacks)).call(_context, fn => {
	        fn.call(component, event);
	      });
	      return !event.defaultPrevented;
	    }
	    return true;
	  };
	}

	// TODO figure out if we still want to support
	// shorthand events, or if we want to implement
	// a real bubbling mechanism
	/**
	 * @param component
	 * @param event
	 * @returns {void}
	 */
	function bubble(component, event) {
	  const callbacks = component.$$.callbacks[event.type];
	  if (callbacks) {
	    var _context2;
	    // @ts-ignore
	    _forEachInstanceProperty(_context2 = _sliceInstanceProperty$1(callbacks).call(callbacks)).call(_context2, fn => fn.call(this, event));
	  }
	}

	const dirty_components = [];
	const binding_callbacks = [];
	let render_callbacks = [];
	const flush_callbacks = [];
	const resolved_promise = /* @__PURE__ */_Promise.resolve();
	let update_scheduled = false;

	/** @returns {void} */
	function schedule_update() {
	  if (!update_scheduled) {
	    update_scheduled = true;
	    resolved_promise.then(flush);
	  }
	}

	/** @returns {Promise<void>} */
	function tick() {
	  schedule_update();
	  return resolved_promise;
	}

	/** @returns {void} */
	function add_render_callback(fn) {
	  render_callbacks.push(fn);
	}

	/** @returns {void} */
	function add_flush_callback(fn) {
	  flush_callbacks.push(fn);
	}

	// flush() calls callbacks in this order:
	// 1. All beforeUpdate callbacks, in order: parents before children
	// 2. All bind:this callbacks, in reverse order: children before parents.
	// 3. All afterUpdate callbacks, in order: parents before children. EXCEPT
	//    for afterUpdates called during the initial onMount, which are called in
	//    reverse order: children before parents.
	// Since callbacks might update component values, which could trigger another
	// call to flush(), the following steps guard against this:
	// 1. During beforeUpdate, any updated components will be added to the
	//    dirty_components array and will cause a reentrant call to flush(). Because
	//    the flush index is kept outside the function, the reentrant call will pick
	//    up where the earlier call left off and go through all dirty components. The
	//    current_component value is saved and restored so that the reentrant call will
	//    not interfere with the "parent" flush() call.
	// 2. bind:this callbacks cannot trigger new flush() calls.
	// 3. During afterUpdate, any updated components will NOT have their afterUpdate
	//    callback called a second time; the seen_callbacks set, outside the flush()
	//    function, guarantees this behavior.
	const seen_callbacks = new _Set();
	let flushidx = 0; // Do *not* move this inside the flush() function

	/** @returns {void} */
	function flush() {
	  // Do not reenter flush while dirty components are updated, as this can
	  // result in an infinite loop. Instead, let the inner flush handle it.
	  // Reentrancy is ok afterwards for bindings etc.
	  if (flushidx !== 0) {
	    return;
	  }
	  const saved_component = current_component;
	  do {
	    // first, call beforeUpdate functions
	    // and update components
	    try {
	      while (flushidx < dirty_components.length) {
	        const component = dirty_components[flushidx];
	        flushidx++;
	        set_current_component(component);
	        update(component.$$);
	      }
	    } catch (e) {
	      // reset dirty state to not end up in a deadlocked state and then rethrow
	      dirty_components.length = 0;
	      flushidx = 0;
	      throw e;
	    }
	    set_current_component(null);
	    dirty_components.length = 0;
	    flushidx = 0;
	    while (binding_callbacks.length) binding_callbacks.pop()();
	    // then, once components are updated, call
	    // afterUpdate functions. This may cause
	    // subsequent updates...
	    for (let i = 0; i < render_callbacks.length; i += 1) {
	      const callback = render_callbacks[i];
	      if (!seen_callbacks.has(callback)) {
	        // ...so guard against infinite loops
	        seen_callbacks.add(callback);
	        callback();
	      }
	    }
	    render_callbacks.length = 0;
	  } while (dirty_components.length);
	  while (flush_callbacks.length) {
	    flush_callbacks.pop()();
	  }
	  update_scheduled = false;
	  seen_callbacks.clear();
	  set_current_component(saved_component);
	}

	/** @returns {void} */
	function update($$) {
	  if ($$.fragment !== null) {
	    var _context;
	    $$.update();
	    run_all($$.before_update);
	    const dirty = $$.dirty;
	    $$.dirty = [-1];
	    $$.fragment && $$.fragment.p($$.ctx, dirty);
	    _forEachInstanceProperty(_context = $$.after_update).call(_context, add_render_callback);
	  }
	}

	/**
	 * Useful for example to execute remaining `afterUpdate` callbacks before executing `destroy`.
	 * @param {Function[]} fns
	 * @returns {void}
	 */
	function flush_render_callbacks(fns) {
	  const filtered = [];
	  const targets = [];
	  _forEachInstanceProperty(render_callbacks).call(render_callbacks, c => _indexOfInstanceProperty(fns).call(fns, c) === -1 ? filtered.push(c) : targets.push(c));
	  _forEachInstanceProperty(targets).call(targets, c => c());
	  render_callbacks = filtered;
	}

	/**
	 * @type {Promise<void> | null}
	 */
	let promise;

	/**
	 * @returns {Promise<void>}
	 */
	function wait() {
	  if (!promise) {
	    promise = _Promise.resolve();
	    promise.then(() => {
	      promise = null;
	    });
	  }
	  return promise;
	}

	/**
	 * @param {Element} node
	 * @param {INTRO | OUTRO | boolean} direction
	 * @param {'start' | 'end'} kind
	 * @returns {void}
	 */
	function dispatch(node, direction, kind) {
	  node.dispatchEvent(custom_event(`${direction ? 'intro' : 'outro'}${kind}`));
	}
	const outroing = new _Set();

	/**
	 * @type {Outro}
	 */
	let outros;

	/**
	 * @returns {void} */
	function group_outros() {
	  outros = {
	    r: 0,
	    c: [],
	    p: outros // parent group
	  };
	}

	/**
	 * @returns {void} */
	function check_outros() {
	  if (!outros.r) {
	    run_all(outros.c);
	  }
	  outros = outros.p;
	}

	/**
	 * @param {import('./private.js').Fragment} block
	 * @param {0 | 1} [local]
	 * @returns {void}
	 */
	function transition_in(block, local) {
	  if (block && block.i) {
	    outroing.delete(block);
	    block.i(local);
	  }
	}

	/**
	 * @param {import('./private.js').Fragment} block
	 * @param {0 | 1} local
	 * @param {0 | 1} [detach]
	 * @param {() => void} [callback]
	 * @returns {void}
	 */
	function transition_out(block, local, detach, callback) {
	  if (block && block.o) {
	    if (outroing.has(block)) return;
	    outroing.add(block);
	    outros.c.push(() => {
	      outroing.delete(block);
	      if (callback) {
	        if (detach) block.d(1);
	        callback();
	      }
	    });
	    block.o(local);
	  } else if (callback) {
	    callback();
	  }
	}

	/**
	 * @type {import('../transition/public.js').TransitionConfig}
	 */
	const null_transition = {
	  duration: 0
	};

	/**
	 * @param {Element & ElementCSSInlineStyle} node
	 * @param {TransitionFn} fn
	 * @param {any} params
	 * @returns {{ start(): void; invalidate(): void; end(): void; }}
	 */
	function create_in_transition(node, fn, params) {
	  /**
	   * @type {TransitionOptions} */
	  const options = {
	    direction: 'in'
	  };
	  let config = fn(node, params, options);
	  let running = false;
	  let animation_name;
	  let task;
	  let uid = 0;

	  /**
	   * @returns {void} */
	  function cleanup() {
	    if (animation_name) delete_rule(node, animation_name);
	  }

	  /**
	   * @returns {void} */
	  function go() {
	    const {
	      delay = 0,
	      duration = 300,
	      easing = identity,
	      tick = noop,
	      css
	    } = config || null_transition;
	    if (css) animation_name = create_rule(node, 0, 1, duration, delay, easing, css, uid++);
	    tick(0, 1);
	    const start_time = now() + delay;
	    const end_time = start_time + duration;
	    if (task) task.abort();
	    running = true;
	    add_render_callback(() => dispatch(node, true, 'start'));
	    task = loop(now => {
	      if (running) {
	        if (now >= end_time) {
	          tick(1, 0);
	          dispatch(node, true, 'end');
	          cleanup();
	          return running = false;
	        }
	        if (now >= start_time) {
	          const t = easing((now - start_time) / duration);
	          tick(t, 1 - t);
	        }
	      }
	      return running;
	    });
	  }
	  let started = false;
	  return {
	    start() {
	      if (started) return;
	      started = true;
	      delete_rule(node);
	      if (is_function(config)) {
	        config = config(options);
	        wait().then(go);
	      } else {
	        go();
	      }
	    },
	    invalidate() {
	      started = false;
	    },
	    end() {
	      if (running) {
	        cleanup();
	        running = false;
	      }
	    }
	  };
	}

	/**
	 * @param {Element & ElementCSSInlineStyle} node
	 * @param {TransitionFn} fn
	 * @param {any} params
	 * @param {boolean} intro
	 * @returns {{ run(b: 0 | 1): void; end(): void; }}
	 */
	function create_bidirectional_transition(node, fn, params, intro) {
	  /**
	   * @type {TransitionOptions} */
	  const options = {
	    direction: 'both'
	  };
	  let config = fn(node, params, options);
	  let t = intro ? 0 : 1;

	  /**
	   * @type {Program | null} */
	  let running_program = null;

	  /**
	   * @type {PendingProgram | null} */
	  let pending_program = null;
	  let animation_name = null;

	  /** @type {boolean} */
	  let original_inert_value;

	  /**
	   * @returns {void} */
	  function clear_animation() {
	    if (animation_name) delete_rule(node, animation_name);
	  }

	  /**
	   * @param {PendingProgram} program
	   * @param {number} duration
	   * @returns {Program}
	   */
	  function init(program, duration) {
	    const d = /** @type {Program['d']} */program.b - t;
	    duration *= Math.abs(d);
	    return {
	      a: t,
	      b: program.b,
	      d,
	      duration,
	      start: program.start,
	      end: program.start + duration,
	      group: program.group
	    };
	  }

	  /**
	   * @param {INTRO | OUTRO} b
	   * @returns {void}
	   */
	  function go(b) {
	    const {
	      delay = 0,
	      duration = 300,
	      easing = identity,
	      tick = noop,
	      css
	    } = config || null_transition;

	    /**
	     * @type {PendingProgram} */
	    const program = {
	      start: now() + delay,
	      b
	    };
	    if (!b) {
	      // @ts-ignore todo: improve typings
	      program.group = outros;
	      outros.r += 1;
	    }
	    if ('inert' in node) {
	      if (b) {
	        if (original_inert_value !== undefined) {
	          // aborted/reversed outro — restore previous inert value
	          node.inert = original_inert_value;
	        }
	      } else {
	        original_inert_value = /** @type {HTMLElement} */node.inert;
	        node.inert = true;
	      }
	    }
	    if (running_program || pending_program) {
	      pending_program = program;
	    } else {
	      // if this is an intro, and there's a delay, we need to do
	      // an initial tick and/or apply CSS animation immediately
	      if (css) {
	        clear_animation();
	        animation_name = create_rule(node, t, b, duration, delay, easing, css);
	      }
	      if (b) tick(0, 1);
	      running_program = init(program, duration);
	      add_render_callback(() => dispatch(node, b, 'start'));
	      loop(now => {
	        if (pending_program && now > pending_program.start) {
	          running_program = init(pending_program, duration);
	          pending_program = null;
	          dispatch(node, running_program.b, 'start');
	          if (css) {
	            clear_animation();
	            animation_name = create_rule(node, t, running_program.b, running_program.duration, 0, easing, config.css);
	          }
	        }
	        if (running_program) {
	          if (now >= running_program.end) {
	            tick(t = running_program.b, 1 - t);
	            dispatch(node, running_program.b, 'end');
	            if (!pending_program) {
	              // we're done
	              if (running_program.b) {
	                // intro — we can tidy up immediately
	                clear_animation();
	              } else {
	                // outro — needs to be coordinated
	                if (! --running_program.group.r) run_all(running_program.group.c);
	              }
	            }
	            running_program = null;
	          } else if (now >= running_program.start) {
	            const p = now - running_program.start;
	            t = running_program.a + running_program.d * easing(p / running_program.duration);
	            tick(t, 1 - t);
	          }
	        }
	        return !!(running_program || pending_program);
	      });
	    }
	  }
	  return {
	    run(b) {
	      if (is_function(config)) {
	        wait().then(() => {
	          const opts = {
	            direction: b ? 'in' : 'out'
	          };
	          // @ts-ignore
	          config = config(opts);
	          go(b);
	        });
	      } else {
	        go(b);
	      }
	    },
	    end() {
	      clear_animation();
	      running_program = pending_program = null;
	    }
	  };
	}

	/** @typedef {1} INTRO */
	/** @typedef {0} OUTRO */
	/** @typedef {{ direction: 'in' | 'out' | 'both' }} TransitionOptions */
	/** @typedef {(node: Element, params: any, options: TransitionOptions) => import('../transition/public.js').TransitionConfig} TransitionFn */

	/**
	 * @typedef {Object} Outro
	 * @property {number} r
	 * @property {Function[]} c
	 * @property {Object} p
	 */

	/**
	 * @typedef {Object} PendingProgram
	 * @property {number} start
	 * @property {INTRO|OUTRO} b
	 * @property {Outro} [group]
	 */

	/**
	 * @typedef {Object} Program
	 * @property {number} a
	 * @property {INTRO|OUTRO} b
	 * @property {1|-1} d
	 * @property {number} duration
	 * @property {number} start
	 * @property {number} end
	 * @property {Outro} [group]
	 */

	// general each functions:

	function ensure_array_like(array_like_or_iterator) {
	  return array_like_or_iterator?.length !== undefined ? array_like_or_iterator : _Array$from(array_like_or_iterator);
	}

	/** @returns {void} */
	function outro_and_destroy_block(block, lookup) {
	  transition_out(block, 1, 1, () => {
	    lookup.delete(block.key);
	  });
	}

	/** @returns {any[]} */
	function update_keyed_each(old_blocks, dirty, get_key, dynamic, ctx, list, lookup, node, destroy, create_each_block, next, get_context) {
	  let o = old_blocks.length;
	  let n = list.length;
	  let i = o;
	  const old_indexes = {};
	  while (i--) old_indexes[old_blocks[i].key] = i;
	  const new_blocks = [];
	  const new_lookup = new _Map();
	  const deltas = new _Map();
	  const updates = [];
	  i = n;
	  while (i--) {
	    const child_ctx = get_context(ctx, list, i);
	    const key = get_key(child_ctx);
	    let block = lookup.get(key);
	    if (!block) {
	      block = create_each_block(key, child_ctx);
	      block.c();
	    } else {
	      // defer updates until all the DOM shuffling is done
	      updates.push(() => block.p(child_ctx, dirty));
	    }
	    new_lookup.set(key, new_blocks[i] = block);
	    if (key in old_indexes) deltas.set(key, Math.abs(i - old_indexes[key]));
	  }
	  const will_move = new _Set();
	  const did_move = new _Set();
	  /** @returns {void} */
	  function insert(block) {
	    transition_in(block, 1);
	    block.m(node, next);
	    lookup.set(block.key, block);
	    next = block.first;
	    n--;
	  }
	  while (o && n) {
	    const new_block = new_blocks[n - 1];
	    const old_block = old_blocks[o - 1];
	    const new_key = new_block.key;
	    const old_key = old_block.key;
	    if (new_block === old_block) {
	      // do nothing
	      next = new_block.first;
	      o--;
	      n--;
	    } else if (!new_lookup.has(old_key)) {
	      // remove old block
	      destroy(old_block, lookup);
	      o--;
	    } else if (!lookup.has(new_key) || will_move.has(new_key)) {
	      insert(new_block);
	    } else if (did_move.has(old_key)) {
	      o--;
	    } else if (deltas.get(new_key) > deltas.get(old_key)) {
	      did_move.add(new_key);
	      insert(new_block);
	    } else {
	      will_move.add(old_key);
	      o--;
	    }
	  }
	  while (o--) {
	    const old_block = old_blocks[o];
	    if (!new_lookup.has(old_block.key)) destroy(old_block, lookup);
	  }
	  while (n) insert(new_blocks[n - 1]);
	  run_all(updates);
	  return new_blocks;
	}

	/** @returns {{}} */
	function get_spread_update(levels, updates) {
	  const update = {};
	  const to_null_out = {};
	  const accounted_for = {
	    $$scope: 1
	  };
	  let i = levels.length;
	  while (i--) {
	    const o = levels[i];
	    const n = updates[i];
	    if (n) {
	      for (const key in o) {
	        if (!(key in n)) to_null_out[key] = 1;
	      }
	      for (const key in n) {
	        if (!accounted_for[key]) {
	          update[key] = n[key];
	          accounted_for[key] = 1;
	        }
	      }
	      levels[i] = n;
	    } else {
	      for (const key in o) {
	        accounted_for[key] = 1;
	      }
	    }
	  }
	  for (const key in to_null_out) {
	    if (!(key in update)) update[key] = undefined;
	  }
	  return update;
	}
	function get_spread_object(spread_props) {
	  return typeof spread_props === 'object' && spread_props !== null ? spread_props : {};
	}

	const _boolean_attributes = /** @type {const} */['allowfullscreen', 'allowpaymentrequest', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'formnovalidate', 'hidden', 'inert', 'ismap', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected'];

	/**
	 * List of HTML boolean attributes (e.g. `<input disabled>`).
	 * Source: https://html.spec.whatwg.org/multipage/indices.html
	 *
	 * @type {Set<string>}
	 */
	new _Set([..._boolean_attributes]);

	/** @typedef {typeof _boolean_attributes[number]} BooleanAttributes */

	var es_array_fill = {};

	var arrayFill;
	var hasRequiredArrayFill;

	function requireArrayFill () {
		if (hasRequiredArrayFill) return arrayFill;
		hasRequiredArrayFill = 1;
		var toObject = /*@__PURE__*/ requireToObject$1();
		var toAbsoluteIndex = /*@__PURE__*/ requireToAbsoluteIndex$1();
		var lengthOfArrayLike = /*@__PURE__*/ requireLengthOfArrayLike$1();

		// `Array.prototype.fill` method implementation
		// https://tc39.es/ecma262/#sec-array.prototype.fill
		arrayFill = function fill(value /* , start = 0, end = @length */) {
		  var O = toObject(this);
		  var length = lengthOfArrayLike(O);
		  var argumentsLength = arguments.length;
		  var index = toAbsoluteIndex(argumentsLength > 1 ? arguments[1] : undefined, length);
		  var end = argumentsLength > 2 ? arguments[2] : undefined;
		  var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
		  while (endPos > index) O[index++] = value;
		  return O;
		};
		return arrayFill;
	}

	var hasRequiredEs_array_fill;

	function requireEs_array_fill () {
		if (hasRequiredEs_array_fill) return es_array_fill;
		hasRequiredEs_array_fill = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var fill = /*@__PURE__*/ requireArrayFill();
		var addToUnscopables = /*@__PURE__*/ requireAddToUnscopables$1();

		// `Array.prototype.fill` method
		// https://tc39.es/ecma262/#sec-array.prototype.fill
		$({ target: 'Array', proto: true }, {
		  fill: fill
		});

		// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
		addToUnscopables('fill');
		return es_array_fill;
	}

	var fill$3;
	var hasRequiredFill$3;

	function requireFill$3 () {
		if (hasRequiredFill$3) return fill$3;
		hasRequiredFill$3 = 1;
		requireEs_array_fill();
		var getBuiltInPrototypeMethod = /*@__PURE__*/ requireGetBuiltInPrototypeMethod$1();

		fill$3 = getBuiltInPrototypeMethod('Array', 'fill');
		return fill$3;
	}

	var fill$2;
	var hasRequiredFill$2;

	function requireFill$2 () {
		if (hasRequiredFill$2) return fill$2;
		hasRequiredFill$2 = 1;
		var isPrototypeOf = /*@__PURE__*/ requireObjectIsPrototypeOf$1();
		var method = /*@__PURE__*/ requireFill$3();

		var ArrayPrototype = Array.prototype;

		fill$2 = function (it) {
		  var own = it.fill;
		  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.fill) ? method : own;
		};
		return fill$2;
	}

	var fill$1;
	var hasRequiredFill$1;

	function requireFill$1 () {
		if (hasRequiredFill$1) return fill$1;
		hasRequiredFill$1 = 1;
		var parent = /*@__PURE__*/ requireFill$2();

		fill$1 = parent;
		return fill$1;
	}

	var fill;
	var hasRequiredFill;

	function requireFill () {
		if (hasRequiredFill) return fill;
		hasRequiredFill = 1;
		fill = /*@__PURE__*/ requireFill$1();
		return fill;
	}

	var fillExports = requireFill();
	var _fillInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(fillExports);

	/** @returns {void} */
	function bind(component, name, callback) {
	  const index = component.$$.props[name];
	  if (index !== undefined) {
	    component.$$.bound[index] = callback;
	    callback(component.$$.ctx[index]);
	  }
	}

	/** @returns {void} */
	function create_component(block) {
	  block && block.c();
	}

	/** @returns {void} */
	function mount_component(component, target, anchor) {
	  const {
	    fragment,
	    after_update
	  } = component.$$;
	  fragment && fragment.m(target, anchor);
	  // onMount happens before the initial afterUpdate
	  add_render_callback(() => {
	    var _context, _context2;
	    const new_on_destroy = _filterInstanceProperty$1(_context = _mapInstanceProperty(_context2 = component.$$.on_mount).call(_context2, run)).call(_context, is_function);
	    // if the component was destroyed immediately
	    // it will update the `$$.on_destroy` reference to `null`.
	    // the destructured on_destroy may still reference to the old array
	    if (component.$$.on_destroy) {
	      component.$$.on_destroy.push(...new_on_destroy);
	    } else {
	      // Edge case - component was destroyed immediately,
	      // most likely as a result of a binding initialising
	      run_all(new_on_destroy);
	    }
	    component.$$.on_mount = [];
	  });
	  _forEachInstanceProperty(after_update).call(after_update, add_render_callback);
	}

	/** @returns {void} */
	function destroy_component(component, detaching) {
	  const $$ = component.$$;
	  if ($$.fragment !== null) {
	    flush_render_callbacks($$.after_update);
	    run_all($$.on_destroy);
	    $$.fragment && $$.fragment.d(detaching);
	    // TODO null out other refs, including component.$$ (but need to
	    // preserve final state?)
	    $$.on_destroy = $$.fragment = null;
	    $$.ctx = [];
	  }
	}

	/** @returns {void} */
	function make_dirty(component, i) {
	  if (component.$$.dirty[0] === -1) {
	    var _context3;
	    dirty_components.push(component);
	    schedule_update();
	    _fillInstanceProperty(_context3 = component.$$.dirty).call(_context3, 0);
	  }
	  component.$$.dirty[i / 31 | 0] |= 1 << i % 31;
	}

	// TODO: Document the other params
	/**
	 * @param {SvelteComponent} component
	 * @param {import('./public.js').ComponentConstructorOptions} options
	 *
	 * @param {import('./utils.js')['not_equal']} not_equal Used to compare props and state values.
	 * @param {(target: Element | ShadowRoot) => void} [append_styles] Function that appends styles to the DOM when the component is first initialised.
	 * This will be the `add_css` function from the compiled component.
	 *
	 * @returns {void}
	 */
	function init(component, options, instance, create_fragment, not_equal, props) {
	  let append_styles = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : null;
	  let dirty = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : [-1];
	  const parent_component = current_component;
	  set_current_component(component);
	  /** @type {import('./private.js').T$$} */
	  const $$ = component.$$ = {
	    fragment: null,
	    ctx: [],
	    // state
	    props,
	    update: noop,
	    not_equal,
	    bound: blank_object(),
	    // lifecycle
	    on_mount: [],
	    on_destroy: [],
	    on_disconnect: [],
	    before_update: [],
	    after_update: [],
	    context: new _Map(options.context || (parent_component ? parent_component.$$.context : [])),
	    // everything else
	    callbacks: blank_object(),
	    dirty,
	    skip_bound: false,
	    root: options.target || parent_component.$$.root
	  };
	  append_styles && append_styles($$.root);
	  let ready = false;
	  $$.ctx = instance ? instance(component, options.props || {}, function (i, ret) {
	    const value = (arguments.length <= 2 ? 0 : arguments.length - 2) ? arguments.length <= 2 ? undefined : arguments[2] : ret;
	    if ($$.ctx && not_equal($$.ctx[i], $$.ctx[i] = value)) {
	      if (!$$.skip_bound && $$.bound[i]) $$.bound[i](value);
	      if (ready) make_dirty(component, i);
	    }
	    return ret;
	  }) : [];
	  $$.update();
	  ready = true;
	  run_all($$.before_update);
	  // `false` as a special case of no DOM component
	  $$.fragment = create_fragment ? create_fragment($$.ctx) : false;
	  if (options.target) {
	    if (options.hydrate) {
	      // TODO: what is the correct type here?
	      // @ts-expect-error
	      const nodes = children(options.target);
	      $$.fragment && $$.fragment.l(nodes);
	      _forEachInstanceProperty(nodes).call(nodes, detach);
	    } else {
	      // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
	      $$.fragment && $$.fragment.c();
	    }
	    if (options.intro) transition_in(component.$$.fragment);
	    mount_component(component, options.target, options.anchor);
	    flush();
	  }
	  set_current_component(parent_component);
	}

	/**
	 * Base class for Svelte components. Used when dev=false.
	 *
	 * @template {Record<string, any>} [Props=any]
	 * @template {Record<string, any>} [Events=any]
	 */
	class SvelteComponent {
	  /**
	   * ### PRIVATE API
	   *
	   * Do not use, may change at any time
	   *
	   * @type {any}
	   */
	  $$ = (() => undefined)();
	  /**
	   * ### PRIVATE API
	   *
	   * Do not use, may change at any time
	   *
	   * @type {any}
	   */
	  $$set = (() => undefined)();

	  /** @returns {void} */
	  $destroy() {
	    destroy_component(this, 1);
	    this.$destroy = noop;
	  }

	  /**
	   * @template {Extract<keyof Events, string>} K
	   * @param {K} type
	   * @param {((e: Events[K]) => void) | null | undefined} callback
	   * @returns {() => void}
	   */
	  $on(type, callback) {
	    if (!is_function(callback)) {
	      return noop;
	    }
	    const callbacks = this.$$.callbacks[type] || (this.$$.callbacks[type] = []);
	    callbacks.push(callback);
	    return () => {
	      const index = _indexOfInstanceProperty(callbacks).call(callbacks, callback);
	      if (index !== -1) _spliceInstanceProperty(callbacks).call(callbacks, index, 1);
	    };
	  }

	  /**
	   * @param {Partial<Props>} props
	   * @returns {void}
	   */
	  $set(props) {
	    if (this.$$set && !is_empty(props)) {
	      this.$$.skip_bound = true;
	      this.$$set(props);
	      this.$$.skip_bound = false;
	    }
	  }
	}

	/**
	 * @typedef {Object} CustomElementPropDefinition
	 * @property {string} [attribute]
	 * @property {boolean} [reflect]
	 * @property {'String'|'Boolean'|'Number'|'Array'|'Object'} [type]
	 */

	// generated during release, do not modify

	const PUBLIC_VERSION = '4';

	if (typeof window !== 'undefined')
	  // @ts-ignore
	  (window.__svelte || (window.__svelte = {
	    v: new _Set()
	  })).v.add(PUBLIC_VERSION);

	/*
	Adapted from https://github.com/mattdesl
	Distributed under MIT License https://github.com/mattdesl/eases/blob/master/LICENSE.md
	*/

	/**
	 * https://svelte.dev/docs/svelte-easing
	 * @param {number} t
	 * @returns {number}
	 */
	function cubicOut(t) {
	  const f = t - 1.0;
	  return f * f * f + 1.0;
	}

	/**
	 * Slides an element in and out.
	 *
	 * https://svelte.dev/docs/svelte-transition#slide
	 * @param {Element} node
	 * @param {import('./public').SlideParams} [params]
	 * @returns {import('./public').TransitionConfig}
	 */
	function slide(node) {
	  let {
	    delay = 0,
	    duration = 400,
	    easing = cubicOut,
	    axis = 'y'
	  } = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
	  const style = getComputedStyle(node);
	  const opacity = +style.opacity;
	  const primary_property = axis === 'y' ? 'height' : 'width';
	  const primary_property_value = _parseFloat(style[primary_property]);
	  const secondary_properties = axis === 'y' ? ['top', 'bottom'] : ['left', 'right'];
	  const capitalized_secondary_properties = _mapInstanceProperty(secondary_properties).call(secondary_properties, e => `${e[0].toUpperCase()}${_sliceInstanceProperty$1(e).call(e, 1)}`);
	  const padding_start_value = _parseFloat(style[`padding${capitalized_secondary_properties[0]}`]);
	  const padding_end_value = _parseFloat(style[`padding${capitalized_secondary_properties[1]}`]);
	  const margin_start_value = _parseFloat(style[`margin${capitalized_secondary_properties[0]}`]);
	  const margin_end_value = _parseFloat(style[`margin${capitalized_secondary_properties[1]}`]);
	  const border_width_start_value = _parseFloat(style[`border${capitalized_secondary_properties[0]}Width`]);
	  const border_width_end_value = _parseFloat(style[`border${capitalized_secondary_properties[1]}Width`]);
	  return {
	    delay,
	    duration,
	    easing,
	    css: t => 'overflow: hidden;' + `opacity: ${Math.min(t * 20, 1) * opacity};` + `${primary_property}: ${t * primary_property_value}px;` + `padding-${secondary_properties[0]}: ${t * padding_start_value}px;` + `padding-${secondary_properties[1]}: ${t * padding_end_value}px;` + `margin-${secondary_properties[0]}: ${t * margin_start_value}px;` + `margin-${secondary_properties[1]}: ${t * margin_end_value}px;` + `border-${secondary_properties[0]}-width: ${t * border_width_start_value}px;` + `border-${secondary_properties[1]}-width: ${t * border_width_end_value}px;`
	  };
	}

	/* ../../../../../../../assets/js/frontend/components/Spinner.svelte generated by Svelte v4.2.20 */
	function create_fragment$5(ctx) {
	  let div;
	  let svg;
	  let path0;
	  let path1;
	  let svg_class_value;
	  let div_style_value;
	  return {
	    c() {
	      div = element("div");
	      svg = svg_element("svg");
	      path0 = svg_element("path");
	      path1 = svg_element("path");
	      attr(path0, "d", "M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z");
	      attr(path0, "fill", "currentColor");
	      attr(path1, "d", "M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z");
	      attr(path1, "fill", "currentFill");
	      attr(svg, "aria-hidden", "true");
	      attr(svg, "class", svg_class_value = "bookly:inline bookly:text-gray-200 bookly:animate-spin fill-bookly " + (/*full_size*/ctx[1] ? 'bookly:absolute bookly:inset-0 bookly:h-full bookly:w-full' : 'bookly:w-8 bookly:h-8'));
	      attr(svg, "viewBox", "0 0 100 101");
	      attr(svg, "fill", "none");
	      attr(svg, "xmlns", "http://www.w3.org/2000/svg");
	      attr(div, "class", "bookly:flex bookly:flex-col bookly:justify-center bookly:items-center bookly:w-full bookly-loading-mark");
	      attr(div, "style", div_style_value = /*height*/ctx[0] ? 'min-height: ' + /*height*/ctx[0] + 'px;' : 'min-height: 100%;');
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, svg);
	      append(svg, path0);
	      append(svg, path1);
	    },
	    p(ctx, _ref) {
	      let [dirty] = _ref;
	      if (dirty & /*full_size*/2 && svg_class_value !== (svg_class_value = "bookly:inline bookly:text-gray-200 bookly:animate-spin fill-bookly " + (/*full_size*/ctx[1] ? 'bookly:absolute bookly:inset-0 bookly:h-full bookly:w-full' : 'bookly:w-8 bookly:h-8'))) {
	        attr(svg, "class", svg_class_value);
	      }
	      if (dirty & /*height*/1 && div_style_value !== (div_style_value = /*height*/ctx[0] ? 'min-height: ' + /*height*/ctx[0] + 'px;' : 'min-height: 100%;')) {
	        attr(div, "style", div_style_value);
	      }
	    },
	    i: noop,
	    o: noop,
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	    }
	  };
	}
	function instance$5($$self, $$props, $$invalidate) {
	  let {
	    height = null
	  } = $$props;
	  let {
	    full_size = false
	  } = $$props;
	  $$self.$$set = $$props => {
	    if ('height' in $$props) $$invalidate(0, height = $$props.height);
	    if ('full_size' in $$props) $$invalidate(1, full_size = $$props.full_size);
	  };
	  return [height, full_size];
	}
	let Spinner$1 = class Spinner extends SvelteComponent {
	  constructor(options) {
	    super();
	    init(this, options, instance$5, create_fragment$5, safe_not_equal, {
	      height: 0,
	      full_size: 1
	    });
	  }
	};

	/* ../../../../../../../assets/js/frontend/components/Button.svelte generated by Svelte v4.2.20 */
	function create_else_block_1(ctx) {
	  let button;
	  let t;
	  let span;
	  let button_class_value;
	  let button_disabled_value;
	  let current;
	  let mounted;
	  let dispose;
	  let if_block = /*loading*/ctx[3] && create_if_block_4$1();
	  const default_slot_template = /*#slots*/ctx[17].default;
	  const default_slot = create_slot(default_slot_template, ctx, /*$$scope*/ctx[16], null);
	  return {
	    c() {
	      button = element("button");
	      if (if_block) if_block.c();
	      t = space();
	      span = element("span");
	      if (default_slot) default_slot.c();
	      toggle_class(span, "bookly:opacity-0", /*loading*/ctx[3]);
	      attr(button, "type", "button");
	      attr(button, "title", /*title*/ctx[2]);
	      attr(button, "class", button_class_value = "" + (/*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly:drop-shadow-none bookly:box-border"));
	      attr(button, "style", /*styles*/ctx[4]);
	      button.disabled = button_disabled_value = /*disabled*/ctx[0] || /*loading*/ctx[3];
	      toggle_class(button, "bookly:cursor-pointer", ! /*disabled*/ctx[0]);
	      toggle_class(button, "bookly:pointer-events-none", /*disabled*/ctx[0]);
	      toggle_class(button, "bookly:opacity-50", /*disabled*/ctx[0]);
	    },
	    m(target, anchor) {
	      insert(target, button, anchor);
	      if (if_block) if_block.m(button, null);
	      append(button, t);
	      append(button, span);
	      if (default_slot) {
	        default_slot.m(span, null);
	      }
	      current = true;
	      if (!mounted) {
	        dispose = listen(button, "click", stop_propagation(/*click_handler_1*/ctx[20]));
	        mounted = true;
	      }
	    },
	    p(ctx, dirty) {
	      if (/*loading*/ctx[3]) {
	        if (if_block) {
	          if (dirty & /*loading*/8) {
	            transition_in(if_block, 1);
	          }
	        } else {
	          if_block = create_if_block_4$1();
	          if_block.c();
	          transition_in(if_block, 1);
	          if_block.m(button, t);
	        }
	      } else if (if_block) {
	        group_outros();
	        transition_out(if_block, 1, 1, () => {
	          if_block = null;
	        });
	        check_outros();
	      }
	      if (default_slot) {
	        if (default_slot.p && (!current || dirty & /*$$scope*/65536)) {
	          update_slot_base(default_slot, default_slot_template, ctx, /*$$scope*/ctx[16], !current ? get_all_dirty_from_scope(/*$$scope*/ctx[16]) : get_slot_changes(default_slot_template, /*$$scope*/ctx[16], dirty, null), null);
	        }
	      }
	      if (!current || dirty & /*loading*/8) {
	        toggle_class(span, "bookly:opacity-0", /*loading*/ctx[3]);
	      }
	      if (!current || dirty & /*title*/4) {
	        attr(button, "title", /*title*/ctx[2]);
	      }
	      if (!current || dirty & /*classes, buttonClasses*/96 && button_class_value !== (button_class_value = "" + (/*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly:drop-shadow-none bookly:box-border"))) {
	        attr(button, "class", button_class_value);
	      }
	      if (!current || dirty & /*styles*/16) {
	        attr(button, "style", /*styles*/ctx[4]);
	      }
	      if (!current || dirty & /*disabled, loading*/9 && button_disabled_value !== (button_disabled_value = /*disabled*/ctx[0] || /*loading*/ctx[3])) {
	        button.disabled = button_disabled_value;
	      }
	      if (!current || dirty & /*classes, buttonClasses, disabled*/97) {
	        toggle_class(button, "bookly:cursor-pointer", ! /*disabled*/ctx[0]);
	      }
	      if (!current || dirty & /*classes, buttonClasses, disabled*/97) {
	        toggle_class(button, "bookly:pointer-events-none", /*disabled*/ctx[0]);
	      }
	      if (!current || dirty & /*classes, buttonClasses, disabled*/97) {
	        toggle_class(button, "bookly:opacity-50", /*disabled*/ctx[0]);
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block);
	      transition_in(default_slot, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block);
	      transition_out(default_slot, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(button);
	      }
	      if (if_block) if_block.d();
	      if (default_slot) default_slot.d(detaching);
	      mounted = false;
	      dispose();
	    }
	  };
	}

	// (144:0) {#if container === 'div'}
	function create_if_block$4(ctx) {
	  let current_block_type_index;
	  let if_block;
	  let if_block_anchor;
	  let current;
	  const if_block_creators = [create_if_block_1$3, create_else_block$1];
	  const if_blocks = [];
	  function select_block_type_1(ctx, dirty) {
	    if (! /*disabled*/ctx[0]) return 0;
	    return 1;
	  }
	  current_block_type_index = select_block_type_1(ctx);
	  if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
	  return {
	    c() {
	      if_block.c();
	      if_block_anchor = empty();
	    },
	    m(target, anchor) {
	      if_blocks[current_block_type_index].m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p(ctx, dirty) {
	      let previous_block_index = current_block_type_index;
	      current_block_type_index = select_block_type_1(ctx);
	      if (current_block_type_index === previous_block_index) {
	        if_blocks[current_block_type_index].p(ctx, dirty);
	      } else {
	        group_outros();
	        transition_out(if_blocks[previous_block_index], 1, 1, () => {
	          if_blocks[previous_block_index] = null;
	        });
	        check_outros();
	        if_block = if_blocks[current_block_type_index];
	        if (!if_block) {
	          if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
	          if_block.c();
	        } else {
	          if_block.p(ctx, dirty);
	        }
	        transition_in(if_block, 1);
	        if_block.m(if_block_anchor.parentNode, if_block_anchor);
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block);
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(if_block_anchor);
	      }
	      if_blocks[current_block_type_index].d(detaching);
	    }
	  };
	}

	// (194:8) {#if loading}
	function create_if_block_4$1(ctx) {
	  let span;
	  let spinner;
	  let current;
	  spinner = new Spinner$1({
	    props: {
	      full_size: true
	    }
	  });
	  return {
	    c() {
	      span = element("span");
	      create_component(spinner.$$.fragment);
	      attr(span, "class", "bookly:absolute bookly:inset-1");
	    },
	    m(target, anchor) {
	      insert(target, span, anchor);
	      mount_component(spinner, span, null);
	      current = true;
	    },
	    i(local) {
	      if (current) return;
	      transition_in(spinner.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(spinner.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(span);
	      }
	      destroy_component(spinner);
	    }
	  };
	}

	// (165:4) {:else}
	function create_else_block$1(ctx) {
	  let div;
	  let t;
	  let span;
	  let div_class_value;
	  let current;
	  let if_block = /*loading*/ctx[3] && create_if_block_3$2();
	  const default_slot_template = /*#slots*/ctx[17].default;
	  const default_slot = create_slot(default_slot_template, ctx, /*$$scope*/ctx[16], null);
	  return {
	    c() {
	      div = element("div");
	      if (if_block) if_block.c();
	      t = space();
	      span = element("span");
	      if (default_slot) default_slot.c();
	      toggle_class(span, "bookly:opacity-0", /*loading*/ctx[3]);
	      attr(div, "title", /*title*/ctx[2]);
	      attr(div, "class", div_class_value = "" + (/*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly:drop-shadow-none bookly:box-border bookly:text-center bookly:flex bookly:items-center bookly:justify-center pointer-events-none bookly:opacity-50 bookly:pointer-events-none"));
	      attr(div, "style", /*styles*/ctx[4]);
	      attr(div, "disabled", /*disabled*/ctx[0]);
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      if (if_block) if_block.m(div, null);
	      append(div, t);
	      append(div, span);
	      if (default_slot) {
	        default_slot.m(span, null);
	      }
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (/*loading*/ctx[3]) {
	        if (if_block) {
	          if (dirty & /*loading*/8) {
	            transition_in(if_block, 1);
	          }
	        } else {
	          if_block = create_if_block_3$2();
	          if_block.c();
	          transition_in(if_block, 1);
	          if_block.m(div, t);
	        }
	      } else if (if_block) {
	        group_outros();
	        transition_out(if_block, 1, 1, () => {
	          if_block = null;
	        });
	        check_outros();
	      }
	      if (default_slot) {
	        if (default_slot.p && (!current || dirty & /*$$scope*/65536)) {
	          update_slot_base(default_slot, default_slot_template, ctx, /*$$scope*/ctx[16], !current ? get_all_dirty_from_scope(/*$$scope*/ctx[16]) : get_slot_changes(default_slot_template, /*$$scope*/ctx[16], dirty, null), null);
	        }
	      }
	      if (!current || dirty & /*loading*/8) {
	        toggle_class(span, "bookly:opacity-0", /*loading*/ctx[3]);
	      }
	      if (!current || dirty & /*title*/4) {
	        attr(div, "title", /*title*/ctx[2]);
	      }
	      if (!current || dirty & /*classes, buttonClasses*/96 && div_class_value !== (div_class_value = "" + (/*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly:drop-shadow-none bookly:box-border bookly:text-center bookly:flex bookly:items-center bookly:justify-center pointer-events-none bookly:opacity-50 bookly:pointer-events-none"))) {
	        attr(div, "class", div_class_value);
	      }
	      if (!current || dirty & /*styles*/16) {
	        attr(div, "style", /*styles*/ctx[4]);
	      }
	      if (!current || dirty & /*disabled*/1) {
	        attr(div, "disabled", /*disabled*/ctx[0]);
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block);
	      transition_in(default_slot, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block);
	      transition_out(default_slot, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (if_block) if_block.d();
	      if (default_slot) default_slot.d(detaching);
	    }
	  };
	}

	// (145:4) {#if !disabled}
	function create_if_block_1$3(ctx) {
	  let div;
	  let t;
	  let span;
	  let div_class_value;
	  let current;
	  let mounted;
	  let dispose;
	  let if_block = /*loading*/ctx[3] && create_if_block_2$3();
	  const default_slot_template = /*#slots*/ctx[17].default;
	  const default_slot = create_slot(default_slot_template, ctx, /*$$scope*/ctx[16], null);
	  return {
	    c() {
	      div = element("div");
	      if (if_block) if_block.c();
	      t = space();
	      span = element("span");
	      if (default_slot) default_slot.c();
	      toggle_class(span, "bookly:opacity-0", /*loading*/ctx[3]);
	      attr(div, "title", /*title*/ctx[2]);
	      attr(div, "class", div_class_value = "" + (/*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly:drop-shadow-none bookly:box-border bookly:text-center bookly:flex bookly:items-center bookly:justify-center bookly:focus:outline-hidden bookly:cursor-pointer"));
	      attr(div, "style", /*styles*/ctx[4]);
	      attr(div, "disabled", /*disabled*/ctx[0]);
	      attr(div, "role", "button");
	      attr(div, "tabindex", "0");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      if (if_block) if_block.m(div, null);
	      append(div, t);
	      append(div, span);
	      if (default_slot) {
	        default_slot.m(span, null);
	      }
	      current = true;
	      if (!mounted) {
	        dispose = [listen(div, "click", stop_propagation(/*click_handler*/ctx[18])), listen(div, "keypress", stop_propagation(/*keypress_handler*/ctx[19]))];
	        mounted = true;
	      }
	    },
	    p(ctx, dirty) {
	      if (/*loading*/ctx[3]) {
	        if (if_block) {
	          if (dirty & /*loading*/8) {
	            transition_in(if_block, 1);
	          }
	        } else {
	          if_block = create_if_block_2$3();
	          if_block.c();
	          transition_in(if_block, 1);
	          if_block.m(div, t);
	        }
	      } else if (if_block) {
	        group_outros();
	        transition_out(if_block, 1, 1, () => {
	          if_block = null;
	        });
	        check_outros();
	      }
	      if (default_slot) {
	        if (default_slot.p && (!current || dirty & /*$$scope*/65536)) {
	          update_slot_base(default_slot, default_slot_template, ctx, /*$$scope*/ctx[16], !current ? get_all_dirty_from_scope(/*$$scope*/ctx[16]) : get_slot_changes(default_slot_template, /*$$scope*/ctx[16], dirty, null), null);
	        }
	      }
	      if (!current || dirty & /*loading*/8) {
	        toggle_class(span, "bookly:opacity-0", /*loading*/ctx[3]);
	      }
	      if (!current || dirty & /*title*/4) {
	        attr(div, "title", /*title*/ctx[2]);
	      }
	      if (!current || dirty & /*classes, buttonClasses*/96 && div_class_value !== (div_class_value = "" + (/*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly:drop-shadow-none bookly:box-border bookly:text-center bookly:flex bookly:items-center bookly:justify-center bookly:focus:outline-hidden bookly:cursor-pointer"))) {
	        attr(div, "class", div_class_value);
	      }
	      if (!current || dirty & /*styles*/16) {
	        attr(div, "style", /*styles*/ctx[4]);
	      }
	      if (!current || dirty & /*disabled*/1) {
	        attr(div, "disabled", /*disabled*/ctx[0]);
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block);
	      transition_in(default_slot, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block);
	      transition_out(default_slot, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (if_block) if_block.d();
	      if (default_slot) default_slot.d(detaching);
	      mounted = false;
	      run_all(dispose);
	    }
	  };
	}

	// (172:12) {#if loading}
	function create_if_block_3$2(ctx) {
	  let span;
	  let spinner;
	  let current;
	  spinner = new Spinner$1({
	    props: {
	      full_size: true
	    }
	  });
	  return {
	    c() {
	      span = element("span");
	      create_component(spinner.$$.fragment);
	      attr(span, "class", "bookly:absolute bookly:inset-1");
	    },
	    m(target, anchor) {
	      insert(target, span, anchor);
	      mount_component(spinner, span, null);
	      current = true;
	    },
	    i(local) {
	      if (current) return;
	      transition_in(spinner.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(spinner.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(span);
	      }
	      destroy_component(spinner);
	    }
	  };
	}

	// (156:12) {#if loading}
	function create_if_block_2$3(ctx) {
	  let span;
	  let spinner;
	  let current;
	  spinner = new Spinner$1({
	    props: {
	      full_size: true
	    }
	  });
	  return {
	    c() {
	      span = element("span");
	      create_component(spinner.$$.fragment);
	      attr(span, "class", "bookly:absolute bookly:inset-1");
	    },
	    m(target, anchor) {
	      insert(target, span, anchor);
	      mount_component(spinner, span, null);
	      current = true;
	    },
	    i(local) {
	      if (current) return;
	      transition_in(spinner.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(spinner.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(span);
	      }
	      destroy_component(spinner);
	    }
	  };
	}
	function create_fragment$4(ctx) {
	  let current_block_type_index;
	  let if_block;
	  let if_block_anchor;
	  let current;
	  const if_block_creators = [create_if_block$4, create_else_block_1];
	  const if_blocks = [];
	  function select_block_type(ctx, dirty) {
	    if (/*container*/ctx[1] === 'div') return 0;
	    return 1;
	  }
	  current_block_type_index = select_block_type(ctx);
	  if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
	  return {
	    c() {
	      if_block.c();
	      if_block_anchor = empty();
	    },
	    m(target, anchor) {
	      if_blocks[current_block_type_index].m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p(ctx, _ref) {
	      let [dirty] = _ref;
	      let previous_block_index = current_block_type_index;
	      current_block_type_index = select_block_type(ctx);
	      if (current_block_type_index === previous_block_index) {
	        if_blocks[current_block_type_index].p(ctx, dirty);
	      } else {
	        group_outros();
	        transition_out(if_blocks[previous_block_index], 1, 1, () => {
	          if_blocks[previous_block_index] = null;
	        });
	        check_outros();
	        if_block = if_blocks[current_block_type_index];
	        if (!if_block) {
	          if_block = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
	          if_block.c();
	        } else {
	          if_block.p(ctx, dirty);
	        }
	        transition_in(if_block, 1);
	        if_block.m(if_block_anchor.parentNode, if_block_anchor);
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block);
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(if_block_anchor);
	      }
	      if_blocks[current_block_type_index].d(detaching);
	    }
	  };
	}
	function instance$4($$self, $$props, $$invalidate) {
	  let {
	    $$slots: slots = {},
	    $$scope
	  } = $$props;
	  let {
	    disabled = false
	  } = $$props;
	  let {
	    type = 'default'
	  } = $$props;
	  let {
	    container = 'button'
	  } = $$props;
	  let {
	    title = ''
	  } = $$props;
	  let {
	    rounded = true
	  } = $$props;
	  let {
	    bordered = true
	  } = $$props;
	  let {
	    paddings = true
	  } = $$props;
	  let {
	    margins = true
	  } = $$props;
	  let {
	    shadows = true
	  } = $$props;
	  let {
	    loading = false
	  } = $$props;
	  let {
	    color = false
	  } = $$props;
	  let {
	    size = 'normal'
	  } = $$props;
	  let {
	    styles = ''
	  } = $$props;
	  let {
	    class: classes = ''
	  } = $$props;
	  let hover;
	  let buttonClasses;
	  function click_handler(event) {
	    bubble.call(this, $$self, event);
	  }
	  function keypress_handler(event) {
	    bubble.call(this, $$self, event);
	  }
	  function click_handler_1(event) {
	    bubble.call(this, $$self, event);
	  }
	  $$self.$$set = $$props => {
	    if ('disabled' in $$props) $$invalidate(0, disabled = $$props.disabled);
	    if ('type' in $$props) $$invalidate(13, type = $$props.type);
	    if ('container' in $$props) $$invalidate(1, container = $$props.container);
	    if ('title' in $$props) $$invalidate(2, title = $$props.title);
	    if ('rounded' in $$props) $$invalidate(7, rounded = $$props.rounded);
	    if ('bordered' in $$props) $$invalidate(8, bordered = $$props.bordered);
	    if ('paddings' in $$props) $$invalidate(9, paddings = $$props.paddings);
	    if ('margins' in $$props) $$invalidate(10, margins = $$props.margins);
	    if ('shadows' in $$props) $$invalidate(11, shadows = $$props.shadows);
	    if ('loading' in $$props) $$invalidate(3, loading = $$props.loading);
	    if ('color' in $$props) $$invalidate(14, color = $$props.color);
	    if ('size' in $$props) $$invalidate(12, size = $$props.size);
	    if ('styles' in $$props) $$invalidate(4, styles = $$props.styles);
	    if ('class' in $$props) $$invalidate(5, classes = $$props.class);
	    if ('$$scope' in $$props) $$invalidate(16, $$scope = $$props.$$scope);
	  };
	  $$self.$$.update = () => {
	    if ($$self.$$.dirty & /*type, color, disabled, shadows, buttonClasses, loading, hover, rounded, bordered, paddings, size, margins*/65481) {
	      {
	        switch (type) {
	          case 'secondary':
	            $$invalidate(6, buttonClasses = 'bookly:text-slate-600 bookly:bg-white bookly:border-slate-600');
	            $$invalidate(15, hover = 'bookly:hover:text-slate-50 bookly:hover:bg-slate-400 bookly:hover:border-slate-400');
	            break;
	          case 'white':
	            $$invalidate(6, buttonClasses = 'bookly:text-slate-600 bookly:bg-white bookly:border-slate-600');
	            $$invalidate(15, hover = 'bookly:hover:text-slate-50 bookly:hover:bg-gray-400 bookly:hover:border-gray-400');
	            break;
	          case 'transparent':
	            $$invalidate(6, buttonClasses = (color ? color : 'bookly:text-slate-600') + ' bookly:bg-transparent bookly:border-slate-600');
	            $$invalidate(15, hover = 'bookly:hover:text-slate-50 bookly:hover:bg-gray-400 bookly:hover:border-gray-400');
	            break;
	          case 'bookly':
	            $$invalidate(6, buttonClasses = 'text-bookly bookly:not-hover:bg-white border-bookly');
	            $$invalidate(15, hover = 'bookly:hover:text-white hover:bg-bookly bookly:hover:opacity-80 hover:border-bookly');
	            break;
	          case 'bookly-active':
	            $$invalidate(6, buttonClasses = 'bg-bookly bookly:text-white border-bookly');
	            $$invalidate(15, hover = 'bookly:hover:text-slate-100 hover:bg-bookly hover:border-bookly');
	            break;
	          case 'bookly-gray':
	            $$invalidate(6, buttonClasses = 'text-bookly bookly:not-hover:bg-gray-200 border-bookly');
	            $$invalidate(15, hover = 'bookly:hover:text-white hover:bg-bookly hover:border-bookly');
	            break;
	          case 'link':
	            $$invalidate(6, buttonClasses = 'bookly:border-none bookly:rounded-none bookly:p-0 bookly:focus:border-none bookly:focus:outline-none ' + (disabled ? 'bookly:text-gray-600' : 'text-bookly'));
	            $$invalidate(15, hover = 'bookly:hover:text-gray-600');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            $$invalidate(12, size = 'link');
	            break;
	          case 'calendar':
	            $$invalidate(6, buttonClasses = '');
	            $$invalidate(15, hover = 'bookly:hover:opacity-80');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          case 'calendar-normal':
	            $$invalidate(6, buttonClasses = 'text-bookly border-bookly bookly:rounded-none bookly:m-0 ' + (disabled ? 'bookly:bg-slate-50 hover:text-bookly' : 'bookly:bg-white'));
	            $$invalidate(15, hover = 'hover:bg-bookly hover:border-bookly ' + (disabled ? 'hover:text-bookly' : 'bookly:hover:text-white'));
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          case 'calendar-active':
	            $$invalidate(6, buttonClasses = 'bg-bookly bookly:text-white border-bookly bookly:rounded-none bookly:m-0');
	            $$invalidate(15, hover = 'bookly:hover:text-slate-200');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          case 'calendar-inactive':
	            $$invalidate(6, buttonClasses = 'bookly:text-gray-400 border-bookly bookly:rounded-none bookly:m-0 ' + (disabled ? 'bookly:bg-slate-50' : 'bookly:bg-white'));
	            $$invalidate(15, hover = 'bookly:hover:text-white bookly:hover:bg-gray-400 hover:border-bookly');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          default:
	            $$invalidate(6, buttonClasses = 'bookly:text-black bookly:bg-gray-100 bookly:border-default-border');
	            $$invalidate(15, hover = 'bookly:hover:text-slate-50 bookly:hover:bg-gray-400');
	            break;
	        }
	        if (!shadows) {
	          $$invalidate(6, buttonClasses += ' bookly:shadow-none');
	        }
	        if (!disabled && !loading && shadows) {
	          $$invalidate(6, buttonClasses += ' bookly:active:shadow-md');
	        }
	        if (!disabled && !loading) {
	          $$invalidate(6, buttonClasses += ' ' + hover);
	        }
	        if (rounded) {
	          $$invalidate(6, buttonClasses += ' bookly:rounded');
	        }
	        if (bordered) {
	          $$invalidate(6, buttonClasses += ' bookly:border bookly:border-solid');
	        }
	        if (paddings) {
	          switch (size) {
	            case 'lg':
	              $$invalidate(6, buttonClasses += ' bookly:px-5 bookly:py-0');
	              break;
	            default:
	              $$invalidate(6, buttonClasses += ' bookly:px-4 bookly:py-0');
	              break;
	          }
	        }
	        if (margins) {
	          $$invalidate(6, buttonClasses += ' bookly:ms-2 bookly:my-0 bookly:me-0');
	        }
	        switch (size) {
	          case 'link':
	          case 'custom':
	            break;
	          case 'lg':
	            $$invalidate(6, buttonClasses += ' bookly:text-xl bookly:h-14');
	            break;
	          default:
	            $$invalidate(6, buttonClasses += ' bookly:text-lg bookly:h-10');
	            break;
	        }
	        if (margins) {
	          $$invalidate(6, buttonClasses += ' bookly:relative');
	        }
	      }
	    }
	  };
	  return [disabled, container, title, loading, styles, classes, buttonClasses, rounded, bordered, paddings, margins, shadows, size, type, color, hover, $$scope, slots, click_handler, keypress_handler, click_handler_1];
	}
	class Button extends SvelteComponent {
	  constructor(options) {
	    super();
	    init(this, options, instance$4, create_fragment$4, safe_not_equal, {
	      disabled: 0,
	      type: 13,
	      container: 1,
	      title: 2,
	      rounded: 7,
	      bordered: 8,
	      paddings: 9,
	      margins: 10,
	      shadows: 11,
	      loading: 3,
	      color: 14,
	      size: 12,
	      styles: 4,
	      class: 5
	    });
	  }
	}

	function get_each_context_4(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[46] = list[i];
	  child_ctx[60] = i;
	  const constants_0 = /*year*/child_ctx[1] + /*_year*/child_ctx[60] - 4;
	  child_ctx[58] = constants_0;
	  const constants_1 = new Date(/*__year*/child_ctx[58], 12, 0);
	  child_ctx[54] = constants_1;
	  const constants_2 = /*limits*/child_ctx[0] && (/*limits*/child_ctx[0].hasOwnProperty('start') && /*limits*/child_ctx[0].start.getFullYear() > /*_date*/child_ctx[54].getFullYear() || /*limits*/child_ctx[0].hasOwnProperty('end') && /*limits*/child_ctx[0].end.getFullYear() < /*_date*/child_ctx[54].getFullYear());
	  child_ctx[50] = constants_2;
	  return child_ctx;
	}
	function get_each_context_3(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[46] = list[i];
	  child_ctx[57] = i;
	  const constants_0 = new Date(/*year*/child_ctx[1], /*_month*/child_ctx[57] + 1, 0);
	  child_ctx[54] = constants_0;
	  const constants_1 = new Date(/*year*/child_ctx[1], /*_month*/child_ctx[57], 1);
	  child_ctx[55] = constants_1;
	  const constants_2 = /*limits*/child_ctx[0] && (/*limits*/child_ctx[0].hasOwnProperty('start') && /*limits*/child_ctx[0].start > /*_date*/child_ctx[54] || /*limits*/child_ctx[0].hasOwnProperty('end') && /*limits*/child_ctx[0].end < /*_end_date*/child_ctx[55]);
	  child_ctx[50] = constants_2;
	  return child_ctx;
	}
	function get_each_context$2(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[46] = list[i];
	  child_ctx[48] = i;
	  return child_ctx;
	}
	function get_each_context_1(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[46] = list[i];
	  child_ctx[52] = i;
	  const constants_0 = /*days*/child_ctx[10][/*i*/child_ctx[48] * 7 + /*j*/child_ctx[52]];
	  child_ctx[49] = constants_0;
	  const constants_1 = /*_day*/child_ctx[49].disabled;
	  child_ctx[50] = constants_1;
	  return child_ctx;
	}
	function get_each_context_2(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[46] = list[i];
	  child_ctx[48] = i;
	  return child_ctx;
	}

	// (253:0) {#if show}
	function create_if_block$3(ctx) {
	  let div3;
	  let t0;
	  let div1;
	  let div0;
	  let button0;
	  let t1;
	  let button1;
	  let t2;
	  let button2;
	  let div1_class_value;
	  let t3;
	  let div2;
	  let current_block_type_index;
	  let if_block1;
	  let div3_class_value;
	  let div3_intro;
	  let current;
	  let if_block0 = (/*loading*/ctx[3] || /*disabled*/ctx[5]) && create_if_block_3$1();
	  button0 = new Button({
	    props: {
	      class: "bookly:grow-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-left-button-mark bookly:m-0 bookly:px-4 bookly:text-xl bookly:shadow-none bookly:cursor-pointer " + /*controlButtonClasses*/ctx[19],
	      type: "calendar",
	      bordered: false,
	      rounded: false,
	      margins: false,
	      disabled: /*loading*/ctx[3] || /*limits*/ctx[0] && /*limits*/ctx[0].hasOwnProperty('start') && /*month*/ctx[2] <= /*limits*/ctx[0].start.getMonth() && /*year*/ctx[1] === /*limits*/ctx[0].start.getFullYear(),
	      container: "div",
	      $$slots: {
	        default: [create_default_slot_5]
	      },
	      $$scope: {
	        ctx
	      }
	    }
	  });
	  button0.$on("click", /*onClickLeft*/ctx[23]);
	  button0.$on("keypress", /*onClickLeft*/ctx[23]);
	  button1 = new Button({
	    props: {
	      class: "bookly:grow bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-middle-button-mark bookly:m-0 bookly:text-lg bookly:shadow-none bookly:cursor-pointer " + /*controlButtonClasses*/ctx[19],
	      type: "calendar",
	      bordered: false,
	      rounded: false,
	      margins: false,
	      container: "div",
	      $$slots: {
	        default: [create_default_slot_4]
	      },
	      $$scope: {
	        ctx
	      }
	    }
	  });
	  button1.$on("click", /*changeView*/ctx[22]);
	  button1.$on("keypress", /*changeView*/ctx[22]);
	  button2 = new Button({
	    props: {
	      class: "bookly:grow-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-right-button-mark bookly:m-0 bookly:px-4 bookly:text-xl bookly:shadow-none bookly:cursor-pointer " + /*controlButtonClasses*/ctx[19],
	      type: "calendar",
	      bordered: false,
	      rounded: false,
	      margins: false,
	      disabled: /*loading*/ctx[3] || /*limits*/ctx[0] && /*limits*/ctx[0].hasOwnProperty('end') && /*month*/ctx[2] >= /*limits*/ctx[0].end.getMonth() && /*year*/ctx[1] === /*limits*/ctx[0].end.getFullYear(),
	      container: "div",
	      $$slots: {
	        default: [create_default_slot_3]
	      },
	      $$scope: {
	        ctx
	      }
	    }
	  });
	  button2.$on("click", /*onClickRight*/ctx[24]);
	  button2.$on("keypress", /*onClickRight*/ctx[24]);
	  const if_block_creators = [create_if_block_1$2, create_if_block_2$2, create_else_block];
	  const if_blocks = [];
	  function select_block_type(ctx, dirty) {
	    if (/*view*/ctx[9] === 'calendar') return 0;
	    if (/*view*/ctx[9] === 'month') return 1;
	    return 2;
	  }
	  current_block_type_index = select_block_type(ctx);
	  if_block1 = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
	  return {
	    c() {
	      div3 = element("div");
	      if (if_block0) if_block0.c();
	      t0 = space();
	      div1 = element("div");
	      div0 = element("div");
	      create_component(button0.$$.fragment);
	      t1 = space();
	      create_component(button1.$$.fragment);
	      t2 = space();
	      create_component(button2.$$.fragment);
	      t3 = space();
	      div2 = element("div");
	      if_block1.c();
	      attr(div0, "class", "bookly:flex bookly:text-gray-400");
	      attr(div0, "role", "group");
	      attr(div1, "class", div1_class_value = "bookly:w-full bookly:border-b " + /*borderColor*/ctx[14] + " bookly:mb-0.5 bookly:pb-0.5 bookly-calendar-controls-mark" + " svelte-trnmqx");
	      attr(div2, "class", "bookly:w-full");
	      attr(div3, "class", div3_class_value = "bookly:w-full bookly:min-h-full bookly:p-0.5 bookly:relative " + /*bgColor*/ctx[12] + " " + /*borderColor*/ctx[14] + " bookly:rounded " + (/*border*/ctx[7] ? 'bookly:border bookly:p-0.5 bookly:rounded' : '') + " svelte-trnmqx");
	    },
	    m(target, anchor) {
	      insert(target, div3, anchor);
	      if (if_block0) if_block0.m(div3, null);
	      append(div3, t0);
	      append(div3, div1);
	      append(div1, div0);
	      mount_component(button0, div0, null);
	      append(div0, t1);
	      mount_component(button1, div0, null);
	      append(div0, t2);
	      mount_component(button2, div0, null);
	      append(div3, t3);
	      append(div3, div2);
	      if_blocks[current_block_type_index].m(div2, null);
	      /*div3_binding*/
	      ctx[43](div3);
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (/*loading*/ctx[3] || /*disabled*/ctx[5]) {
	        if (if_block0) {
	          if (dirty[0] & /*loading, disabled*/40) {
	            transition_in(if_block0, 1);
	          }
	        } else {
	          if_block0 = create_if_block_3$1();
	          if_block0.c();
	          transition_in(if_block0, 1);
	          if_block0.m(div3, t0);
	        }
	      } else if (if_block0) {
	        group_outros();
	        transition_out(if_block0, 1, 1, () => {
	          if_block0 = null;
	        });
	        check_outros();
	      }
	      const button0_changes = {};
	      if (dirty[0] & /*controlButtonClasses*/524288) button0_changes.class = "bookly:grow-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-left-button-mark bookly:m-0 bookly:px-4 bookly:text-xl bookly:shadow-none bookly:cursor-pointer " + /*controlButtonClasses*/ctx[19];
	      if (dirty[0] & /*loading, limits, month, year*/15) button0_changes.disabled = /*loading*/ctx[3] || /*limits*/ctx[0] && /*limits*/ctx[0].hasOwnProperty('start') && /*month*/ctx[2] <= /*limits*/ctx[0].start.getMonth() && /*year*/ctx[1] === /*limits*/ctx[0].start.getFullYear();
	      if (dirty[0] & /*rtl*/2048 | dirty[1] & /*$$scope*/1073741824) {
	        button0_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button0.$set(button0_changes);
	      const button1_changes = {};
	      if (dirty[0] & /*controlButtonClasses*/524288) button1_changes.class = "bookly:grow bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-middle-button-mark bookly:m-0 bookly:text-lg bookly:shadow-none bookly:cursor-pointer " + /*controlButtonClasses*/ctx[19];
	      if (dirty[0] & /*title*/1048576 | dirty[1] & /*$$scope*/1073741824) {
	        button1_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button1.$set(button1_changes);
	      const button2_changes = {};
	      if (dirty[0] & /*controlButtonClasses*/524288) button2_changes.class = "bookly:grow-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-right-button-mark bookly:m-0 bookly:px-4 bookly:text-xl bookly:shadow-none bookly:cursor-pointer " + /*controlButtonClasses*/ctx[19];
	      if (dirty[0] & /*loading, limits, month, year*/15) button2_changes.disabled = /*loading*/ctx[3] || /*limits*/ctx[0] && /*limits*/ctx[0].hasOwnProperty('end') && /*month*/ctx[2] >= /*limits*/ctx[0].end.getMonth() && /*year*/ctx[1] === /*limits*/ctx[0].end.getFullYear();
	      if (dirty[0] & /*rtl*/2048 | dirty[1] & /*$$scope*/1073741824) {
	        button2_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button2.$set(button2_changes);
	      if (!current || dirty[0] & /*borderColor*/16384 && div1_class_value !== (div1_class_value = "bookly:w-full bookly:border-b " + /*borderColor*/ctx[14] + " bookly:mb-0.5 bookly:pb-0.5 bookly-calendar-controls-mark" + " svelte-trnmqx")) {
	        attr(div1, "class", div1_class_value);
	      }
	      let previous_block_index = current_block_type_index;
	      current_block_type_index = select_block_type(ctx);
	      if (current_block_type_index === previous_block_index) {
	        if_blocks[current_block_type_index].p(ctx, dirty);
	      } else {
	        group_outros();
	        transition_out(if_blocks[previous_block_index], 1, 1, () => {
	          if_blocks[previous_block_index] = null;
	        });
	        check_outros();
	        if_block1 = if_blocks[current_block_type_index];
	        if (!if_block1) {
	          if_block1 = if_blocks[current_block_type_index] = if_block_creators[current_block_type_index](ctx);
	          if_block1.c();
	        } else {
	          if_block1.p(ctx, dirty);
	        }
	        transition_in(if_block1, 1);
	        if_block1.m(div2, null);
	      }
	      if (!current || dirty[0] & /*bgColor, borderColor, border*/20608 && div3_class_value !== (div3_class_value = "bookly:w-full bookly:min-h-full bookly:p-0.5 bookly:relative " + /*bgColor*/ctx[12] + " " + /*borderColor*/ctx[14] + " bookly:rounded " + (/*border*/ctx[7] ? 'bookly:border bookly:p-0.5 bookly:rounded' : '') + " svelte-trnmqx")) {
	        attr(div3, "class", div3_class_value);
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block0);
	      transition_in(button0.$$.fragment, local);
	      transition_in(button1.$$.fragment, local);
	      transition_in(button2.$$.fragment, local);
	      transition_in(if_block1);
	      if (local) {
	        if (!div3_intro) {
	          add_render_callback(() => {
	            div3_intro = create_in_transition(div3, slide, {
	              duration: 200
	            });
	            div3_intro.start();
	          });
	        }
	      }
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block0);
	      transition_out(button0.$$.fragment, local);
	      transition_out(button1.$$.fragment, local);
	      transition_out(button2.$$.fragment, local);
	      transition_out(if_block1);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div3);
	      }
	      if (if_block0) if_block0.d();
	      destroy_component(button0);
	      destroy_component(button1);
	      destroy_component(button2);
	      if_blocks[current_block_type_index].d();
	      /*div3_binding*/
	      ctx[43](null);
	    }
	  };
	}

	// (255:8) {#if loading || disabled}
	function create_if_block_3$1(ctx) {
	  let div;
	  let spinner;
	  let current;
	  spinner = new Spinner$1({});
	  return {
	    c() {
	      div = element("div");
	      create_component(spinner.$$.fragment);
	      attr(div, "class", "bookly-calendar-overlay svelte-trnmqx");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(spinner, div, null);
	      current = true;
	    },
	    i(local) {
	      if (current) return;
	      transition_in(spinner.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(spinner.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_component(spinner);
	    }
	  };
	}

	// (262:16) <Button                         class="bookly:grow-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-left-button-mark bookly:m-0 bookly:px-4 bookly:text-xl bookly:shadow-none bookly:cursor-pointer {controlButtonClasses}"                         type="calendar"                         bordered={false}                         rounded={false}                         margins={false}                         disabled={loading || (limits && limits.hasOwnProperty('start') && month <= limits.start.getMonth() && year === limits.start.getFullYear())}                         on:click={onClickLeft}                         on:keypress={onClickLeft}                         container="div"                 >
	function create_default_slot_5(ctx) {
	  let i;
	  return {
	    c() {
	      i = element("i");
	      attr(i, "class", "bi");
	      toggle_class(i, "bi-chevron-left", ! /*rtl*/ctx[11]);
	      toggle_class(i, "bi-chevron-right", /*rtl*/ctx[11]);
	    },
	    m(target, anchor) {
	      insert(target, i, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*rtl*/2048) {
	        toggle_class(i, "bi-chevron-left", ! /*rtl*/ctx[11]);
	      }
	      if (dirty[0] & /*rtl*/2048) {
	        toggle_class(i, "bi-chevron-right", /*rtl*/ctx[11]);
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(i);
	      }
	    }
	  };
	}

	// (275:16) <Button                         class="bookly:grow bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-middle-button-mark bookly:m-0 bookly:text-lg bookly:shadow-none bookly:cursor-pointer {controlButtonClasses}"                         type="calendar"                         bordered={false}                         rounded={false}                         margins={false}                         on:click={changeView}                         on:keypress={changeView}                         container="div"                 >
	function create_default_slot_4(ctx) {
	  let t;
	  return {
	    c() {
	      t = text(/*title*/ctx[20]);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*title*/1048576) set_data(t, /*title*/ctx[20]);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (287:16) <Button                         class="bookly:grow-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly-calendar-right-button-mark bookly:m-0 bookly:px-4 bookly:text-xl bookly:shadow-none bookly:cursor-pointer {controlButtonClasses}"                         type="calendar"                         bordered={false}                         rounded={false}                         margins={false}                         disabled={loading || (limits && limits.hasOwnProperty('end') && month >= limits.end.getMonth() && year === limits.end.getFullYear())}                         on:click={onClickRight}                         on:keypress={onClickRight}                         container="div"                 >
	function create_default_slot_3(ctx) {
	  let i;
	  return {
	    c() {
	      i = element("i");
	      attr(i, "class", "bi");
	      toggle_class(i, "bi-chevron-left", /*rtl*/ctx[11]);
	      toggle_class(i, "bi-chevron-right", ! /*rtl*/ctx[11]);
	    },
	    m(target, anchor) {
	      insert(target, i, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*rtl*/2048) {
	        toggle_class(i, "bi-chevron-left", /*rtl*/ctx[11]);
	      }
	      if (dirty[0] & /*rtl*/2048) {
	        toggle_class(i, "bi-chevron-right", ! /*rtl*/ctx[11]);
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(i);
	      }
	    }
	  };
	}

	// (357:12) {:else}
	function create_else_block(ctx) {
	  let div;
	  let div_transition;
	  let current;
	  let each_value_4 = ensure_array_like({
	    length: 9
	  });
	  let each_blocks = [];
	  for (let i = 0; i < each_value_4.length; i += 1) {
	    each_blocks[i] = create_each_block_4(get_each_context_4(ctx, each_value_4, i));
	  }
	  const out = i => transition_out(each_blocks[i], 1, 1, () => {
	    each_blocks[i] = null;
	  });
	  return {
	    c() {
	      div = element("div");
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        each_blocks[i].c();
	      }
	      attr(div, "class", "bookly:w-full bookly:text-center bookly:grid bookly:grid-cols-3 bookly-calendar-years-mark");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        if (each_blocks[i]) {
	          each_blocks[i].m(div, null);
	        }
	      }
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses, view*/164355) {
	        each_value_4 = ensure_array_like({
	          length: 9
	        });
	        let i;
	        for (i = 0; i < each_value_4.length; i += 1) {
	          const child_ctx = get_each_context_4(ctx, each_value_4, i);
	          if (each_blocks[i]) {
	            each_blocks[i].p(child_ctx, dirty);
	            transition_in(each_blocks[i], 1);
	          } else {
	            each_blocks[i] = create_each_block_4(child_ctx);
	            each_blocks[i].c();
	            transition_in(each_blocks[i], 1);
	            each_blocks[i].m(div, null);
	          }
	        }
	        group_outros();
	        for (i = each_value_4.length; i < each_blocks.length; i += 1) {
	          out(i);
	        }
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      for (let i = 0; i < each_value_4.length; i += 1) {
	        transition_in(each_blocks[i]);
	      }
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      each_blocks = _filterInstanceProperty(each_blocks).call(each_blocks, Boolean);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        transition_out(each_blocks[i]);
	      }
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_each(each_blocks, detaching);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (332:39) 
	function create_if_block_2$2(ctx) {
	  let div;
	  let div_transition;
	  let current;
	  let each_value_3 = ensure_array_like({
	    length: 12
	  });
	  let each_blocks = [];
	  for (let i = 0; i < each_value_3.length; i += 1) {
	    each_blocks[i] = create_each_block_3(get_each_context_3(ctx, each_value_3, i));
	  }
	  const out = i => transition_out(each_blocks[i], 1, 1, () => {
	    each_blocks[i] = null;
	  });
	  return {
	    c() {
	      div = element("div");
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        each_blocks[i].c();
	      }
	      attr(div, "class", "bookly:w-full bookly:text-center bookly:grid bookly:grid-cols-4 bookly-calendar-months-mark");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        if (each_blocks[i]) {
	          each_blocks[i].m(div, null);
	        }
	      }
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses, month, dispatch, view, datePicker*/2261527) {
	        each_value_3 = ensure_array_like({
	          length: 12
	        });
	        let i;
	        for (i = 0; i < each_value_3.length; i += 1) {
	          const child_ctx = get_each_context_3(ctx, each_value_3, i);
	          if (each_blocks[i]) {
	            each_blocks[i].p(child_ctx, dirty);
	            transition_in(each_blocks[i], 1);
	          } else {
	            each_blocks[i] = create_each_block_3(child_ctx);
	            each_blocks[i].c();
	            transition_in(each_blocks[i], 1);
	            each_blocks[i].m(div, null);
	          }
	        }
	        group_outros();
	        for (i = each_value_3.length; i < each_blocks.length; i += 1) {
	          out(i);
	        }
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      for (let i = 0; i < each_value_3.length; i += 1) {
	        transition_in(each_blocks[i]);
	      }
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      each_blocks = _filterInstanceProperty(each_blocks).call(each_blocks, Boolean);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        transition_out(each_blocks[i]);
	      }
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_each(each_blocks, detaching);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (301:12) {#if view === 'calendar'}
	function create_if_block_1$2(ctx) {
	  let div2;
	  let div0;
	  let div0_class_value;
	  let t;
	  let div1;
	  let div2_transition;
	  let current;
	  let each_value_2 = ensure_array_like({
	    length: 7
	  });
	  let each_blocks_1 = [];
	  for (let i = 0; i < each_value_2.length; i += 1) {
	    each_blocks_1[i] = create_each_block_2(get_each_context_2(ctx, each_value_2, i));
	  }
	  let each_value = ensure_array_like({
	    length: _parseInt(/*days*/ctx[10].length / 7)
	  });
	  let each_blocks = [];
	  for (let i = 0; i < each_value.length; i += 1) {
	    each_blocks[i] = create_each_block$2(get_each_context$2(ctx, each_value, i));
	  }
	  const out = i => transition_out(each_blocks[i], 1, 1, () => {
	    each_blocks[i] = null;
	  });
	  return {
	    c() {
	      div2 = element("div");
	      div0 = element("div");
	      for (let i = 0; i < each_blocks_1.length; i += 1) {
	        each_blocks_1[i].c();
	      }
	      t = space();
	      div1 = element("div");
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        each_blocks[i].c();
	      }
	      attr(div0, "class", div0_class_value = "bookly:flex bookly:flex-row fw-bold bookly:text-center bookly:text-muted bookly:w-full bookly:border-b " + /*borderColor*/ctx[14] + " bookly:mb-0.5 bookly:py-2 bookly:max-w-full" + " svelte-trnmqx");
	      attr(div1, "class", "bookly:relative bookly:rounded");
	      attr(div2, "class", "bookly:w-full bookly-calendar-dates-mark");
	    },
	    m(target, anchor) {
	      insert(target, div2, anchor);
	      append(div2, div0);
	      for (let i = 0; i < each_blocks_1.length; i += 1) {
	        if (each_blocks_1[i]) {
	          each_blocks_1[i].m(div0, null);
	        }
	      }
	      append(div2, t);
	      append(div2, div1);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        if (each_blocks[i]) {
	          each_blocks[i].m(div1, null);
	        }
	      }
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*textColor, datePicker*/8208) {
	        each_value_2 = ensure_array_like({
	          length: 7
	        });
	        let i;
	        for (i = 0; i < each_value_2.length; i += 1) {
	          const child_ctx = get_each_context_2(ctx, each_value_2, i);
	          if (each_blocks_1[i]) {
	            each_blocks_1[i].p(child_ctx, dirty);
	          } else {
	            each_blocks_1[i] = create_each_block_2(child_ctx);
	            each_blocks_1[i].c();
	            each_blocks_1[i].m(div0, null);
	          }
	        }
	        for (; i < each_blocks_1.length; i += 1) {
	          each_blocks_1[i].d(1);
	        }
	        each_blocks_1.length = each_value_2.length;
	      }
	      if (!current || dirty[0] & /*borderColor*/16384 && div0_class_value !== (div0_class_value = "bookly:flex bookly:flex-row fw-bold bookly:text-center bookly:text-muted bookly:w-full bookly:border-b " + /*borderColor*/ctx[14] + " bookly:mb-0.5 bookly:py-2 bookly:max-w-full" + " svelte-trnmqx")) {
	        attr(div0, "class", div0_class_value);
	      }
	      if (dirty[0] & /*days, disabledButtonClasses, activeButtonClasses, buttonClasses, otherMonthButtonClasses, onClickDate*/34046976) {
	        each_value = ensure_array_like({
	          length: _parseInt(/*days*/ctx[10].length / 7)
	        });
	        let i;
	        for (i = 0; i < each_value.length; i += 1) {
	          const child_ctx = get_each_context$2(ctx, each_value, i);
	          if (each_blocks[i]) {
	            each_blocks[i].p(child_ctx, dirty);
	            transition_in(each_blocks[i], 1);
	          } else {
	            each_blocks[i] = create_each_block$2(child_ctx);
	            each_blocks[i].c();
	            transition_in(each_blocks[i], 1);
	            each_blocks[i].m(div1, null);
	          }
	        }
	        group_outros();
	        for (i = each_value.length; i < each_blocks.length; i += 1) {
	          out(i);
	        }
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      for (let i = 0; i < each_value.length; i += 1) {
	        transition_in(each_blocks[i]);
	      }
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div2_transition) div2_transition = create_bidirectional_transition(div2, slide, {}, true);
	          div2_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      each_blocks = _filterInstanceProperty(each_blocks).call(each_blocks, Boolean);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        transition_out(each_blocks[i]);
	      }
	      if (local) {
	        if (!div2_transition) div2_transition = create_bidirectional_transition(div2, slide, {}, false);
	        div2_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div2);
	      }
	      destroy_each(each_blocks_1, detaching);
	      destroy_each(each_blocks, detaching);
	      if (detaching && div2_transition) div2_transition.end();
	    }
	  };
	}

	// (364:28) <Button                                     type="calendar"                                     bordered={false}                                     rounded={false}                                     paddings={false}                                     margins={false}                                     class="bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly:px-2 bookly:py-0 bookly:m-0 bookly:text-xl bookly:h-16 bookly:cursor-pointer {_disabled ? disabledButtonClasses : ''} {buttonClasses}"                                     on:click={() => {year = __year; view='month'}}                                     on:keypress={() => {year = __year; view='month'}}                                     disabled={_disabled}                                     container="div"                                     size="custom"                             >
	function create_default_slot_2(ctx) {
	  let t_value = /*__year*/ctx[58] + "";
	  let t;
	  return {
	    c() {
	      t = text(t_value);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*year*/2 && t_value !== (t_value = /*__year*/ctx[58] + "")) set_data(t, t_value);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (359:20) {#each {length: 9} as _,_year}
	function create_each_block_4(ctx) {
	  let div;
	  let button;
	  let t;
	  let current;
	  function click_handler_2() {
	    return /*click_handler_2*/ctx[41](/*__year*/ctx[58]);
	  }
	  function keypress_handler_2() {
	    return /*keypress_handler_2*/ctx[42](/*__year*/ctx[58]);
	  }
	  button = new Button({
	    props: {
	      type: "calendar",
	      bordered: false,
	      rounded: false,
	      paddings: false,
	      margins: false,
	      class: "bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly:px-2 bookly:py-0 bookly:m-0 bookly:text-xl bookly:h-16 bookly:cursor-pointer " + (/*_disabled*/ctx[50] ? /*disabledButtonClasses*/ctx[17] : '') + " " + /*buttonClasses*/ctx[15],
	      disabled: /*_disabled*/ctx[50],
	      container: "div",
	      size: "custom",
	      $$slots: {
	        default: [create_default_slot_2]
	      },
	      $$scope: {
	        ctx
	      }
	    }
	  });
	  button.$on("click", click_handler_2);
	  button.$on("keypress", keypress_handler_2);
	  return {
	    c() {
	      div = element("div");
	      create_component(button.$$.fragment);
	      t = space();
	      attr(div, "class", "col-4");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(button, div, null);
	      append(div, t);
	      current = true;
	    },
	    p(new_ctx, dirty) {
	      ctx = new_ctx;
	      const button_changes = {};
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses*/163843) button_changes.class = "bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly:px-2 bookly:py-0 bookly:m-0 bookly:text-xl bookly:h-16 bookly:cursor-pointer " + (/*_disabled*/ctx[50] ? /*disabledButtonClasses*/ctx[17] : '') + " " + /*buttonClasses*/ctx[15];
	      if (dirty[0] & /*limits, year*/3) button_changes.disabled = /*_disabled*/ctx[50];
	      if (dirty[0] & /*year*/2 | dirty[1] & /*$$scope*/1073741824) {
	        button_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button.$set(button_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(button.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(button.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_component(button);
	    }
	  };
	}

	// (339:28) <Button                                     type="calendar"                                     class="bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly:px-2 bookly:py-0 bookly:m-0 bookly:text-xl bookly:h-16 bookly:cursor-pointer {_disabled ? disabledButtonClasses : ''} {buttonClasses}"                                     bordered={false}                                     rounded={false}                                     margins={false}                                     paddings={false}                                     on:click={() => {month = _month; dispatch('month-change'); view='calendar'}}                                     on:keypress={() => {month = _month; dispatch('month-change'); view='calendar'}}                                     disabled={_disabled}                                     container="div"                                     size="custom"                             >
	function create_default_slot_1(ctx) {
	  let t_value = /*datePicker*/ctx[4].monthNamesShort[/*_month*/ctx[57]] + "";
	  let t;
	  return {
	    c() {
	      t = text(t_value);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*datePicker*/16 && t_value !== (t_value = /*datePicker*/ctx[4].monthNamesShort[/*_month*/ctx[57]] + "")) set_data(t, t_value);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (334:20) {#each {length: 12} as _,_month}
	function create_each_block_3(ctx) {
	  let div;
	  let button;
	  let t;
	  let current;
	  function click_handler_1() {
	    return /*click_handler_1*/ctx[39](/*_month*/ctx[57]);
	  }
	  function keypress_handler_1() {
	    return /*keypress_handler_1*/ctx[40](/*_month*/ctx[57]);
	  }
	  button = new Button({
	    props: {
	      type: "calendar",
	      class: "bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly:px-2 bookly:py-0 bookly:m-0 bookly:text-xl bookly:h-16 bookly:cursor-pointer " + (/*_disabled*/ctx[50] ? /*disabledButtonClasses*/ctx[17] : '') + " " + /*buttonClasses*/ctx[15],
	      bordered: false,
	      rounded: false,
	      margins: false,
	      paddings: false,
	      disabled: /*_disabled*/ctx[50],
	      container: "div",
	      size: "custom",
	      $$slots: {
	        default: [create_default_slot_1]
	      },
	      $$scope: {
	        ctx
	      }
	    }
	  });
	  button.$on("click", click_handler_1);
	  button.$on("keypress", keypress_handler_1);
	  return {
	    c() {
	      div = element("div");
	      create_component(button.$$.fragment);
	      t = space();
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(button, div, null);
	      append(div, t);
	      current = true;
	    },
	    p(new_ctx, dirty) {
	      ctx = new_ctx;
	      const button_changes = {};
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses*/163843) button_changes.class = "bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:leading-normal bookly:px-2 bookly:py-0 bookly:m-0 bookly:text-xl bookly:h-16 bookly:cursor-pointer " + (/*_disabled*/ctx[50] ? /*disabledButtonClasses*/ctx[17] : '') + " " + /*buttonClasses*/ctx[15];
	      if (dirty[0] & /*limits, year*/3) button_changes.disabled = /*_disabled*/ctx[50];
	      if (dirty[0] & /*datePicker*/16 | dirty[1] & /*$$scope*/1073741824) {
	        button_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button.$set(button_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(button.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(button.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_component(button);
	    }
	  };
	}

	// (304:24) {#each {length: 7} as _, i}
	function create_each_block_2(ctx) {
	  let div;
	  let t_value = /*datePicker*/ctx[4].dayNamesShort[(/*i*/ctx[48] + /*datePicker*/ctx[4].firstDay) % 7] + "";
	  let t;
	  let div_class_value;
	  return {
	    c() {
	      div = element("div");
	      t = text(t_value);
	      attr(div, "class", div_class_value = "bookly:flex-1 bookly:px-0 bookly:overflow-hidden bookly:text-sm " + /*textColor*/ctx[13] + " bookly:cursor-default" + " svelte-trnmqx");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, t);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*datePicker*/16 && t_value !== (t_value = /*datePicker*/ctx[4].dayNamesShort[(/*i*/ctx[48] + /*datePicker*/ctx[4].firstDay) % 7] + "")) set_data(t, t_value);
	      if (dirty[0] & /*textColor*/8192 && div_class_value !== (div_class_value = "bookly:flex-1 bookly:px-0 bookly:overflow-hidden bookly:text-sm " + /*textColor*/ctx[13] + " bookly:cursor-default" + " svelte-trnmqx")) {
	        attr(div, "class", div_class_value);
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	    }
	  };
	}

	// (314:36) <Button                                             type='calendar'                                             class="bookly:text-sm bookly:h-10 bookly:leading-4 bookly:shadow-none bookly:flex-1 bookly:py-2 bookly:px-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:cursor-pointer {_disabled ? disabledButtonClasses : ''} {_day.active ? activeButtonClasses : (_day.current ? buttonClasses : otherMonthButtonClasses)} {_day.current ? 'bookly-calendar-current-month-mark' : ''}"                                             bordered={false}                                             margins={false}                                             on:click={() => !_disabled && onClickDate(_day)}                                             on:keypress={() => !_disabled && onClickDate(_day)}                                             disabled={_disabled}                                             container="div"                                             size="custom"                                     >
	function create_default_slot(ctx) {
	  let t_value = /*_day*/ctx[49].title + "";
	  let t;
	  return {
	    c() {
	      t = text(t_value);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*days*/1024 && t_value !== (t_value = /*_day*/ctx[49].title + "")) set_data(t, t_value);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (311:32) {#each {length: 7} as _, j}
	function create_each_block_1(ctx) {
	  let button;
	  let current;
	  function click_handler() {
	    return /*click_handler*/ctx[37](/*_disabled*/ctx[50], /*_day*/ctx[49]);
	  }
	  function keypress_handler() {
	    return /*keypress_handler*/ctx[38](/*_disabled*/ctx[50], /*_day*/ctx[49]);
	  }
	  button = new Button({
	    props: {
	      type: "calendar",
	      class: "bookly:text-sm bookly:h-10 bookly:leading-4 bookly:shadow-none bookly:flex-1 bookly:py-2 bookly:px-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:cursor-pointer " + (/*_disabled*/ctx[50] ? /*disabledButtonClasses*/ctx[17] : '') + " " + (/*_day*/ctx[49].active ? /*activeButtonClasses*/ctx[16] : /*_day*/ctx[49].current ? /*buttonClasses*/ctx[15] : /*otherMonthButtonClasses*/ctx[18]) + " " + (/*_day*/ctx[49].current ? 'bookly-calendar-current-month-mark' : ''),
	      bordered: false,
	      margins: false,
	      disabled: /*_disabled*/ctx[50],
	      container: "div",
	      size: "custom",
	      $$slots: {
	        default: [create_default_slot]
	      },
	      $$scope: {
	        ctx
	      }
	    }
	  });
	  button.$on("click", click_handler);
	  button.$on("keypress", keypress_handler);
	  return {
	    c() {
	      create_component(button.$$.fragment);
	    },
	    m(target, anchor) {
	      mount_component(button, target, anchor);
	      current = true;
	    },
	    p(new_ctx, dirty) {
	      ctx = new_ctx;
	      const button_changes = {};
	      if (dirty[0] & /*days, disabledButtonClasses, activeButtonClasses, buttonClasses, otherMonthButtonClasses*/492544) button_changes.class = "bookly:text-sm bookly:h-10 bookly:leading-4 bookly:shadow-none bookly:flex-1 bookly:py-2 bookly:px-0 bookly:border-none bookly:focus:border-none bookly:focus:outline-none bookly:cursor-pointer " + (/*_disabled*/ctx[50] ? /*disabledButtonClasses*/ctx[17] : '') + " " + (/*_day*/ctx[49].active ? /*activeButtonClasses*/ctx[16] : /*_day*/ctx[49].current ? /*buttonClasses*/ctx[15] : /*otherMonthButtonClasses*/ctx[18]) + " " + (/*_day*/ctx[49].current ? 'bookly-calendar-current-month-mark' : '');
	      if (dirty[0] & /*days*/1024) button_changes.disabled = /*_disabled*/ctx[50];
	      if (dirty[0] & /*days*/1024 | dirty[1] & /*$$scope*/1073741824) {
	        button_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button.$set(button_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(button.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(button.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      destroy_component(button, detaching);
	    }
	  };
	}

	// (309:24) {#each {length: parseInt(days.length / 7)} as _, i}
	function create_each_block$2(ctx) {
	  let div;
	  let t;
	  let current;
	  let each_value_1 = ensure_array_like({
	    length: 7
	  });
	  let each_blocks = [];
	  for (let i = 0; i < each_value_1.length; i += 1) {
	    each_blocks[i] = create_each_block_1(get_each_context_1(ctx, each_value_1, i));
	  }
	  const out = i => transition_out(each_blocks[i], 1, 1, () => {
	    each_blocks[i] = null;
	  });
	  return {
	    c() {
	      div = element("div");
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        each_blocks[i].c();
	      }
	      t = space();
	      attr(div, "class", "bookly:flex bookly:w-full");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        if (each_blocks[i]) {
	          each_blocks[i].m(div, null);
	        }
	      }
	      append(div, t);
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*days, disabledButtonClasses, activeButtonClasses, buttonClasses, otherMonthButtonClasses, onClickDate*/34046976) {
	        each_value_1 = ensure_array_like({
	          length: 7
	        });
	        let i;
	        for (i = 0; i < each_value_1.length; i += 1) {
	          const child_ctx = get_each_context_1(ctx, each_value_1, i);
	          if (each_blocks[i]) {
	            each_blocks[i].p(child_ctx, dirty);
	            transition_in(each_blocks[i], 1);
	          } else {
	            each_blocks[i] = create_each_block_1(child_ctx);
	            each_blocks[i].c();
	            transition_in(each_blocks[i], 1);
	            each_blocks[i].m(div, t);
	          }
	        }
	        group_outros();
	        for (i = each_value_1.length; i < each_blocks.length; i += 1) {
	          out(i);
	        }
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      for (let i = 0; i < each_value_1.length; i += 1) {
	        transition_in(each_blocks[i]);
	      }
	      current = true;
	    },
	    o(local) {
	      each_blocks = _filterInstanceProperty(each_blocks).call(each_blocks, Boolean);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        transition_out(each_blocks[i]);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_each(each_blocks, detaching);
	    }
	  };
	}
	function create_fragment$3(ctx) {
	  let if_block_anchor;
	  let current;
	  let if_block = /*show*/ctx[6] && create_if_block$3(ctx);
	  return {
	    c() {
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	    },
	    m(target, anchor) {
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (/*show*/ctx[6]) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	          if (dirty[0] & /*show*/64) {
	            transition_in(if_block, 1);
	          }
	        } else {
	          if_block = create_if_block$3(ctx);
	          if_block.c();
	          transition_in(if_block, 1);
	          if_block.m(if_block_anchor.parentNode, if_block_anchor);
	        }
	      } else if (if_block) {
	        group_outros();
	        transition_out(if_block, 1, 1, () => {
	          if_block = null;
	        });
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block);
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(if_block_anchor);
	      }
	      if (if_block) if_block.d(detaching);
	    }
	  };
	}
	function formatDate(day) {
	  let _month = day.getMonth() + 1,
	    _day = day.getDate(),
	    _year = day.getFullYear();
	  return _year + '-' + (_month < 10 ? '0' + _month : _month) + '-' + (_day < 10 ? '0' + _day : _day);
	}
	function instance$3($$self, $$props, $$invalidate) {
	  const dispatch = createEventDispatcher();
	  let {
	    layout = 'text-accent'
	  } = $$props;
	  let {
	    date = null
	  } = $$props;
	  let {
	    startDate = null
	  } = $$props;
	  let {
	    holidays = []
	  } = $$props;
	  let {
	    datePicker
	  } = $$props;
	  let {
	    maxDays = 0
	  } = $$props;
	  let {
	    limits = {}
	  } = $$props;
	  let {
	    disabled = false
	  } = $$props;
	  let {
	    disabledWeekDays = []
	  } = $$props;
	  let {
	    loadSchedule = false
	  } = $$props;
	  let el;
	  let rtl = false;
	  let bgColor, textColor, borderColor, buttonClasses, activeButtonClasses, disabledButtonClasses, otherMonthButtonClasses, controlButtonClasses;
	  switch (layout) {
	    case 'bg-accent':
	      bgColor = 'bg-bookly';
	      textColor = 'bookly:text-white';
	      borderColor = 'border-bookly';
	      buttonClasses = 'bookly:text-white bg-bookly:not-hover bookly:hover:bg-white hover:text-bookly';
	      otherMonthButtonClasses = 'bookly:text-slate-300 bg-bookly:not-hover bookly:hover:bg-white hover:text-bookly';
	      activeButtonClasses = 'bookly:bg-white text-bookly hover:text-bookly';
	      disabledButtonClasses = '';
	      controlButtonClasses = 'bookly:text-white bg-bookly:not-hover bookly:hover:bg-white hover:text-bookly';
	      break;
	    default:
	      bgColor = 'bookly:bg-white';
	      textColor = 'bookly:text-slate-600 bookly:hover:text-slate-600';
	      borderColor = 'bookly:border-slate-100';
	      buttonClasses = 'text-bookly hover:bg-bookly bookly:hover:text-white';
	      otherMonthButtonClasses = 'bookly:text-slate-400 hover:bg-bookly bookly:hover:text-white';
	      activeButtonClasses = 'bookly:text-white bg-bookly';
	      disabledButtonClasses = 'bookly:bg-slate-100';
	      controlButtonClasses = 'bookly:text-slate-600 hover:bg-bookly bookly:hover:text-white';
	      break;
	  }
	  function forceLoadSchedule() {
	    $$invalidate(28, holidays = []);
	    $$invalidate(29, loadedMonths = []);
	    $$invalidate(3, loading = true);
	  }
	  if (maxDays) {
	    limits.end = new Date();
	    limits.end.setDate(limits.end.getDate() + _parseInt(maxDays));
	  }
	  let view = 'calendar';
	  let start = new Date();
	  let {
	    year = start.getFullYear()
	  } = $$props;
	  let {
	    month = start.getMonth()
	  } = $$props;
	  let title = '';
	  let days;
	  let {
	    loadedMonths = []
	  } = $$props;
	  let {
	    loading = true
	  } = $$props;
	  let {
	    show = true
	  } = $$props;
	  let {
	    border = false
	  } = $$props;
	  function changeView() {
	    switch (view) {
	      case 'calendar':
	        $$invalidate(9, view = 'month');
	        break;
	      case 'month':
	        $$invalidate(9, view = 'year');
	        break;
	      case 'year':
	        $$invalidate(9, view = 'calendar');
	        break;
	    }
	  }
	  function onClickLeft() {
	    switch (view) {
	      case 'calendar':
	        if (month === 0) {
	          $$invalidate(2, month = 11);
	          $$invalidate(1, year--, year);
	        } else {
	          $$invalidate(2, month--, month);
	        }
	        dispatch('month-change', 'prev');
	        break;
	      case 'month':
	        $$invalidate(1, year--, year);
	        break;
	      case 'year':
	        $$invalidate(1, year -= 9);
	        break;
	    }
	  }
	  function onClickRight() {
	    switch (view) {
	      case 'calendar':
	        if (month === 11) {
	          $$invalidate(2, month = 0);
	          $$invalidate(1, year++, year);
	        } else {
	          $$invalidate(2, month++, month);
	        }
	        dispatch('month-change', 'next');
	        break;
	      case 'month':
	        $$invalidate(1, year++, year);
	        break;
	      case 'year':
	        $$invalidate(1, year += 9);
	        break;
	    }
	  }
	  let monthWithYear;
	  let lastDate = monthWithYear;
	  function _loadSchedule() {
	    if (!_includesInstanceProperty(loadedMonths).call(loadedMonths, monthWithYear)) {
	      loadSchedule(month + 1, year).then(response => {
	        $$invalidate(29, loadedMonths = [...new _Set$1([...loadedMonths, ...(response?.data.parsed_months || [])])]);
	        $$invalidate(28, holidays = [...new _Set$1([...holidays, ...(response?.data.holidays || [])])]);
	        if (date === null) {
	          let firstDate = new Date();
	          while (_includesInstanceProperty(holidays).call(holidays, $$invalidate(26, date = formatDate(firstDate)))) {
	            firstDate.setDate(firstDate.getDate() + 1);
	          }
	          $$invalidate(26, date = formatDate(firstDate));
	          $$invalidate(2, month = firstDate.getMonth());
	          $$invalidate(1, year = firstDate.getFullYear());
	          dispatch('change');
	        }
	      }).catch(() => {
	        if (date === null) {
	          let firstDate = new Date();
	          $$invalidate(26, date = formatDate(firstDate));
	          $$invalidate(2, month = firstDate.getMonth());
	          $$invalidate(1, year = firstDate.getFullYear());
	          dispatch('change');
	        }
	      }).finally(() => $$invalidate(3, loading = false));
	    } else {
	      $$invalidate(3, loading = false);
	    }
	  }
	  function onClickDate(_day) {
	    document.activeElement && document.activeElement.blur();
	    $$invalidate(2, month = _day.date.getMonth());
	    $$invalidate(1, year = _day.date.getFullYear());
	    $$invalidate(26, date = formatDate(_day.date));
	    dispatch('change');
	  }
	  const click_handler = (_disabled, _day) => !_disabled && onClickDate(_day);
	  const keypress_handler = (_disabled, _day) => !_disabled && onClickDate(_day);
	  const click_handler_1 = _month => {
	    $$invalidate(2, month = _month);
	    dispatch('month-change');
	    $$invalidate(9, view = 'calendar');
	  };
	  const keypress_handler_1 = _month => {
	    $$invalidate(2, month = _month);
	    dispatch('month-change');
	    $$invalidate(9, view = 'calendar');
	  };
	  const click_handler_2 = __year => {
	    $$invalidate(1, year = __year);
	    $$invalidate(9, view = 'month');
	  };
	  const keypress_handler_2 = __year => {
	    $$invalidate(1, year = __year);
	    $$invalidate(9, view = 'month');
	  };
	  function div3_binding($$value) {
	    binding_callbacks[$$value ? 'unshift' : 'push'](() => {
	      el = $$value;
	      $$invalidate(8, el);
	    });
	  }
	  $$self.$$set = $$props => {
	    if ('layout' in $$props) $$invalidate(30, layout = $$props.layout);
	    if ('date' in $$props) $$invalidate(26, date = $$props.date);
	    if ('startDate' in $$props) $$invalidate(27, startDate = $$props.startDate);
	    if ('holidays' in $$props) $$invalidate(28, holidays = $$props.holidays);
	    if ('datePicker' in $$props) $$invalidate(4, datePicker = $$props.datePicker);
	    if ('maxDays' in $$props) $$invalidate(31, maxDays = $$props.maxDays);
	    if ('limits' in $$props) $$invalidate(0, limits = $$props.limits);
	    if ('disabled' in $$props) $$invalidate(5, disabled = $$props.disabled);
	    if ('disabledWeekDays' in $$props) $$invalidate(32, disabledWeekDays = $$props.disabledWeekDays);
	    if ('loadSchedule' in $$props) $$invalidate(33, loadSchedule = $$props.loadSchedule);
	    if ('year' in $$props) $$invalidate(1, year = $$props.year);
	    if ('month' in $$props) $$invalidate(2, month = $$props.month);
	    if ('loadedMonths' in $$props) $$invalidate(29, loadedMonths = $$props.loadedMonths);
	    if ('loading' in $$props) $$invalidate(3, loading = $$props.loading);
	    if ('show' in $$props) $$invalidate(6, show = $$props.show);
	    if ('border' in $$props) $$invalidate(7, border = $$props.border);
	  };
	  $$self.$$.update = () => {
	    if ($$self.$$.dirty[0] & /*el*/256) {
	      if (el) {
	        $$invalidate(11, rtl = getComputedStyle(el).direction === 'rtl');
	      }
	    }
	    if ($$self.$$.dirty[0] & /*startDate*/134217728) {
	      if (startDate === null) {
	        $$invalidate(27, startDate = new Date());
	      } else {
	        $$invalidate(1, year = startDate.getFullYear());
	        $$invalidate(2, month = startDate.getMonth());
	      }
	    }
	    if ($$self.$$.dirty[0] & /*month, year*/6) {
	      $$invalidate(35, monthWithYear = month + '-' + year);
	    }
	    if ($$self.$$.dirty[0] & /*view, year, month*/518 | $$self.$$.dirty[1] & /*loadSchedule, lastDate, monthWithYear*/52) {
	      if (loadSchedule !== false && view === 'calendar' && (year || month)) {
	        if (lastDate !== monthWithYear) {
	          $$invalidate(36, lastDate = monthWithYear);
	          $$invalidate(3, loading = true);
	        }
	      }
	    }
	    if ($$self.$$.dirty[0] & /*loading*/8 | $$self.$$.dirty[1] & /*loadSchedule*/4) {
	      if (loadSchedule !== false && loading) {
	        _loadSchedule();
	      }
	    }
	    if ($$self.$$.dirty[0] & /*year, month, datePicker, days, limits, loadedMonths, holidays, date*/872416279 | $$self.$$.dirty[1] & /*disabledWeekDays, monthWithYear*/18) {
	      {
	        let _day = new Date(year, month, 1);
	        _day.setDate(_day.getDate() - ((_day.getDay() - datePicker.firstDay) % 7 + 7) % 7);
	        let lastDay = new Date(year, month + 1, 0);
	        lastDay.setDate(lastDay.getDate() - ((lastDay.getDay() - datePicker.firstDay) % 7 + 7) % 7 + 6);
	        $$invalidate(10, days = []);
	        do {
	          let dayFormatted = formatDate(_day);
	          days.push({
	            'title': _day.getDate(),
	            'current': _day.getMonth() === month,
	            'disabled': limits && limits.hasOwnProperty('start') && _day < limits.start || limits && limits.hasOwnProperty('end') && _day > limits.end || _includesInstanceProperty(disabledWeekDays).call(disabledWeekDays, _day.getDay()) || _includesInstanceProperty(loadedMonths).call(loadedMonths, monthWithYear) && _includesInstanceProperty(holidays).call(holidays, dayFormatted),
	            'active': date === dayFormatted,
	            'date': new Date(_day.getTime())
	          });
	          _day.setDate(_day.getDate() + 1);
	        } while (lastDay >= _day);
	      }
	    }
	    if ($$self.$$.dirty[0] & /*view, datePicker, month, year*/534) {
	      if (view) {
	        switch (view) {
	          case 'calendar':
	            $$invalidate(20, title = datePicker.monthNamesShort[month] + ' ' + year);
	            break;
	          case 'month':
	          case 'year':
	            $$invalidate(20, title = year);
	            break;
	        }
	      }
	    }
	  };
	  return [limits, year, month, loading, datePicker, disabled, show, border, el, view, days, rtl, bgColor, textColor, borderColor, buttonClasses, activeButtonClasses, disabledButtonClasses, otherMonthButtonClasses, controlButtonClasses, title, dispatch, changeView, onClickLeft, onClickRight, onClickDate, date, startDate, holidays, loadedMonths, layout, maxDays, disabledWeekDays, loadSchedule, forceLoadSchedule, monthWithYear, lastDate, click_handler, keypress_handler, click_handler_1, keypress_handler_1, click_handler_2, keypress_handler_2, div3_binding];
	}
	class Calendar extends SvelteComponent {
	  constructor(options) {
	    super();
	    init(this, options, instance$3, create_fragment$3, safe_not_equal, {
	      layout: 30,
	      date: 26,
	      startDate: 27,
	      holidays: 28,
	      datePicker: 4,
	      maxDays: 31,
	      limits: 0,
	      disabled: 5,
	      disabledWeekDays: 32,
	      loadSchedule: 33,
	      forceLoadSchedule: 34,
	      year: 1,
	      month: 2,
	      loadedMonths: 29,
	      loading: 3,
	      show: 6,
	      border: 7
	    }, null, [-1, -1]);
	  }
	  get layout() {
	    return this.$$.ctx[30];
	  }
	  set layout(layout) {
	    this.$$set({
	      layout
	    });
	    flush();
	  }
	  get date() {
	    return this.$$.ctx[26];
	  }
	  set date(date) {
	    this.$$set({
	      date
	    });
	    flush();
	  }
	  get startDate() {
	    return this.$$.ctx[27];
	  }
	  set startDate(startDate) {
	    this.$$set({
	      startDate
	    });
	    flush();
	  }
	  get holidays() {
	    return this.$$.ctx[28];
	  }
	  set holidays(holidays) {
	    this.$$set({
	      holidays
	    });
	    flush();
	  }
	  get datePicker() {
	    return this.$$.ctx[4];
	  }
	  set datePicker(datePicker) {
	    this.$$set({
	      datePicker
	    });
	    flush();
	  }
	  get maxDays() {
	    return this.$$.ctx[31];
	  }
	  set maxDays(maxDays) {
	    this.$$set({
	      maxDays
	    });
	    flush();
	  }
	  get limits() {
	    return this.$$.ctx[0];
	  }
	  set limits(limits) {
	    this.$$set({
	      limits
	    });
	    flush();
	  }
	  get disabled() {
	    return this.$$.ctx[5];
	  }
	  set disabled(disabled) {
	    this.$$set({
	      disabled
	    });
	    flush();
	  }
	  get disabledWeekDays() {
	    return this.$$.ctx[32];
	  }
	  set disabledWeekDays(disabledWeekDays) {
	    this.$$set({
	      disabledWeekDays
	    });
	    flush();
	  }
	  get loadSchedule() {
	    return this.$$.ctx[33];
	  }
	  set loadSchedule(loadSchedule) {
	    this.$$set({
	      loadSchedule
	    });
	    flush();
	  }
	  get forceLoadSchedule() {
	    return this.$$.ctx[34];
	  }
	  get year() {
	    return this.$$.ctx[1];
	  }
	  set year(year) {
	    this.$$set({
	      year
	    });
	    flush();
	  }
	  get month() {
	    return this.$$.ctx[2];
	  }
	  set month(month) {
	    this.$$set({
	      month
	    });
	    flush();
	  }
	  get loadedMonths() {
	    return this.$$.ctx[29];
	  }
	  set loadedMonths(loadedMonths) {
	    this.$$set({
	      loadedMonths
	    });
	    flush();
	  }
	  get loading() {
	    return this.$$.ctx[3];
	  }
	  set loading(loading) {
	    this.$$set({
	      loading
	    });
	    flush();
	  }
	  get show() {
	    return this.$$.ctx[6];
	  }
	  set show(show) {
	    this.$$set({
	      show
	    });
	    flush();
	  }
	  get border() {
	    return this.$$.ctx[7];
	  }
	  set border(border) {
	    this.$$set({
	      border
	    });
	    flush();
	  }
	}

	/**
	 * Details step.
	 */
	function stepDetails(params) {
	  let data = $.extend({
	      action: 'bookly_render_details'
	    }, params),
	    $container = opt[params.form_id].$container;
	  booklyAjax({
	    data
	  }).then(response => {
	    var _context, _context2;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    let intlTelInput = response.intlTelInput,
	      update_details_dialog = response.update_details_dialog,
	      woocommerce = response.woocommerce,
	      customJS = response.custom_js,
	      custom_fields_conditions = response.custom_fields_conditions || [],
	      terms_error = response.l10n.terms_error;
	    if (opt[params.form_id].hasOwnProperty('google_maps') && opt[params.form_id].google_maps.enabled) {
	      booklyInitGooglePlacesAutocomplete($container);
	    }
	    $(document.body).trigger('bookly.render.step_detail', [$container]);
	    // Init.
	    let $guest_info = $('.bookly-js-guest', $container),
	      $phone_field = $('.bookly-js-user-phone-input', $container),
	      $email_field = $('.bookly-js-user-email', $container),
	      $email_confirm_field = $('.bookly-js-user-email-confirm', $container),
	      $birthday_day_field = $('.bookly-js-select-birthday-day', $container),
	      $birthday_month_field = $('.bookly-js-select-birthday-month', $container),
	      $birthday_year_field = $('.bookly-js-select-birthday-year', $container),
	      $address_country_field = $('.bookly-js-address-country', $container),
	      $address_state_field = $('.bookly-js-address-state', $container),
	      $address_postcode_field = $('.bookly-js-address-postcode', $container),
	      $address_city_field = $('.bookly-js-address-city', $container),
	      $address_street_field = $('.bookly-js-address-street', $container),
	      $address_street_number_field = $('.bookly-js-address-street_number', $container),
	      $address_additional_field = $('.bookly-js-address-additional_address', $container),
	      $address_country_error = $('.bookly-js-address-country-error', $container),
	      $address_state_error = $('.bookly-js-address-state-error', $container),
	      $address_postcode_error = $('.bookly-js-address-postcode-error', $container),
	      $address_city_error = $('.bookly-js-address-city-error', $container),
	      $address_street_error = $('.bookly-js-address-street-error', $container),
	      $address_street_number_error = $('.bookly-js-address-street_number-error', $container),
	      $address_additional_error = $('.bookly-js-address-additional_address-error', $container),
	      $birthday_day_error = $('.bookly-js-select-birthday-day-error', $container),
	      $birthday_month_error = $('.bookly-js-select-birthday-month-error', $container),
	      $birthday_year_error = $('.bookly-js-select-birthday-year-error', $container),
	      $full_name_field = $('.bookly-js-full-name', $container),
	      $first_name_field = $('.bookly-js-first-name', $container),
	      $last_name_field = $('.bookly-js-last-name', $container),
	      $notes_field = $('.bookly-js-user-notes', $container),
	      $custom_field = $('.bookly-js-custom-field', $container),
	      $info_field = $('.bookly-js-info-field', $container),
	      $phone_error = $('.bookly-js-user-phone-error', $container),
	      $email_error = $('.bookly-js-user-email-error', $container),
	      $email_confirm_error = $('.bookly-js-user-email-confirm-error', $container),
	      $name_error = $('.bookly-js-full-name-error', $container),
	      $first_name_error = $('.bookly-js-first-name-error', $container),
	      $last_name_error = $('.bookly-js-last-name-error', $container),
	      $captcha = $('.bookly-js-captcha-img', $container),
	      $custom_error = $('.bookly-custom-field-error', $container),
	      $info_error = $('.bookly-js-info-field-error', $container),
	      $modals = $('.bookly-js-modal', $container),
	      $login_modal = $('.bookly-js-login', $container),
	      $cst_modal = $('.bookly-js-cst-duplicate', $container),
	      $verification_modal = $('.bookly-js-verification-code', $container),
	      $verification_code = $('#bookly-verification-code', $container),
	      $next_btn = $('.bookly-js-next-step', $container),
	      $errors = _mapInstanceProperty(_context = $([$birthday_day_error, $birthday_month_error, $birthday_year_error, $address_country_error, $address_state_error, $address_postcode_error, $address_city_error, $address_street_error, $address_street_number_error, $address_additional_error, $name_error, $first_name_error, $last_name_error, $phone_error, $email_error, $email_confirm_error, $custom_error, $info_error])).call(_context, $.fn.toArray),
	      $fields = _mapInstanceProperty(_context2 = $([$birthday_day_field, $birthday_month_field, $birthday_year_field, $address_city_field, $address_country_field, $address_postcode_field, $address_state_field, $address_street_field, $address_street_number_field, $address_additional_field, $full_name_field, $first_name_field, $last_name_field, $phone_field, $email_field, $email_confirm_field, $custom_field, $info_field])).call(_context2, $.fn.toArray);

	    // Populate form after login.
	    var populateForm = function (response) {
	      $full_name_field.val(response.data.full_name).removeClass('bookly-error');
	      $first_name_field.val(response.data.first_name).removeClass('bookly-error');
	      $last_name_field.val(response.data.last_name).removeClass('bookly-error');
	      if (response.data.birthday) {
	        var dateParts = response.data.birthday.split('-'),
	          year = _parseInt$4(dateParts[0]),
	          month = _parseInt$4(dateParts[1]),
	          day = _parseInt$4(dateParts[2]);
	        $birthday_day_field.val(day).removeClass('bookly-error');
	        $birthday_month_field.val(month).removeClass('bookly-error');
	        $birthday_year_field.val(year).removeClass('bookly-error');
	      }
	      if (response.data.phone) {
	        $phone_field.removeClass('bookly-error');
	        if (intlTelInput.enabled) {
	          let iti = window.booklyIntlTelInput.getInstance($phone_field.get(0));
	          iti.setNumber(response.data.phone);
	        } else {
	          $phone_field.val(response.data.phone);
	        }
	      }
	      if (response.data.country) {
	        $address_country_field.val(response.data.country).removeClass('bookly-error');
	      }
	      if (response.data.state) {
	        $address_state_field.val(response.data.state).removeClass('bookly-error');
	      }
	      if (response.data.postcode) {
	        $address_postcode_field.val(response.data.postcode).removeClass('bookly-error');
	      }
	      if (response.data.city) {
	        $address_city_field.val(response.data.city).removeClass('bookly-error');
	      }
	      if (response.data.street) {
	        $address_street_field.val(response.data.street).removeClass('bookly-error');
	      }
	      if (response.data.street_number) {
	        $address_street_number_field.val(response.data.street_number).removeClass('bookly-error');
	      }
	      if (response.data.additional_address) {
	        $address_additional_field.val(response.data.additional_address).removeClass('bookly-error');
	      }
	      $email_field.val(response.data.email).removeClass('bookly-error');
	      if (response.data.info_fields) {
	        var _context3;
	        _forEachInstanceProperty(_context3 = response.data.info_fields).call(_context3, function (field) {
	          var _context4, _context6;
	          var $info_field = _findInstanceProperty($container).call($container, '.bookly-js-info-field-row[data-id="' + field.id + '"]');
	          switch ($info_field.data('type')) {
	            case 'checkboxes':
	              _forEachInstanceProperty(_context4 = field.value).call(_context4, function (value) {
	                var _context5;
	                _filterInstanceProperty$1(_context5 = _findInstanceProperty($info_field).call($info_field, '.bookly-js-info-field')).call(_context5, function () {
	                  return this.value == value;
	                }).prop('checked', true);
	              });
	              break;
	            case 'radio-buttons':
	              _filterInstanceProperty$1(_context6 = _findInstanceProperty($info_field).call($info_field, '.bookly-js-info-field')).call(_context6, function () {
	                return this.value == field.value;
	              }).prop('checked', true);
	              break;
	            default:
	              _findInstanceProperty($info_field).call($info_field, '.bookly-js-info-field').val(field.value);
	              break;
	          }
	        });
	      }
	      _filterInstanceProperty$1($errors).call($errors, ':not(.bookly-custom-field-error)').html('');
	    };
	    let checkCustomFieldConditions = function ($row) {
	      let id = $row.data('id'),
	        value = [];
	      switch ($row.data('type')) {
	        case 'drop-down':
	          value.push(_findInstanceProperty($row).call($row, 'select').val());
	          break;
	        case 'radio-buttons':
	          value.push(_findInstanceProperty($row).call($row, 'input:checked').val());
	          break;
	        case 'checkboxes':
	          _findInstanceProperty($row).call($row, 'input').each(function () {
	            if ($(this).prop('checked')) {
	              value.push($(this).val());
	            }
	          });
	          break;
	      }
	      $.each(custom_fields_conditions, function (i, condition) {
	        let $target = $('.bookly-custom-field-row[data-id="' + condition.target + '"]'),
	          target_visibility = $target.is(':visible');
	        if (_parseInt$4(condition.source) === id) {
	          let show = false;
	          $.each(value, function (i, v) {
	            var _context7, _context8;
	            if ($row.is(':visible') && (_includesInstanceProperty$1(_context7 = condition.value).call(_context7, v) && condition.equal === '1' || !_includesInstanceProperty$1(_context8 = condition.value).call(_context8, v) && condition.equal !== '1')) {
	              show = true;
	            }
	          });
	          $target.toggle(show);
	          if ($target.is(':visible') !== target_visibility) {
	            checkCustomFieldConditions($target);
	          }
	        }
	      });
	    };
	    // Conditional custom fields
	    $('.bookly-custom-field-row').on('change', 'select, input[type="checkbox"], input[type="radio"]', function () {
	      checkCustomFieldConditions($(this).closest('.bookly-custom-field-row'));
	    });
	    $('.bookly-custom-field-row').each(function () {
	      var _context9;
	      const _type = $(this).data('type');
	      if (_includesInstanceProperty$1(_context9 = ['drop-down', 'radio-buttons', 'checkboxes']).call(_context9, _type)) {
	        if (_type === 'drop-down') {
	          var _context0;
	          _findInstanceProperty(_context0 = $(this)).call(_context0, 'select').trigger('change');
	        } else {
	          var _context1;
	          _findInstanceProperty(_context1 = $(this)).call(_context1, 'input:checked').trigger('change');
	        }
	      }
	    });

	    // Custom fields date fields
	    let calendars = {};
	    $(document).on('click', function (e) {
	      var _context10;
	      let $calendar = $(e.target).closest('.bookly-js-datepicker-calendar-wrap'),
	        _id;
	      if ($calendar.length !== 0) {
	        _id = $calendar.data('id');
	      }
	      _forEachInstanceProperty(_context10 = _Object$keys(calendars)).call(_context10, id => {
	        if (id !== _id) calendars[id].show = false;
	      });
	    });
	    $('.bookly-js-cf-date', $container).each(function () {
	      let $that = $(this),
	        $parent = $that.parent(),
	        id = $that.attr('id'),
	        props = {
	          datePicker: BooklyL10nGlobal.datePicker,
	          loading: false,
	          show: false,
	          border: true,
	          limits: {},
	          layout: opt[params.form_id].datepicker_mode
	        };
	      if ($that.data('value')) {
	        props.date = $that.data('value');
	        $that.val(formatDate$1($that.data('value')));
	      }
	      let today = new Date();
	      if ($(this).data('min') !== '') {
	        let startDate = new Date($(this).data('min'));
	        props.limits.start = startDate;
	        if (startDate > today) {
	          props.month = startDate.getMonth();
	          props.year = startDate.getFullYear();
	        }
	      }
	      if ($(this).data('max') !== '') {
	        let endDate = new Date($(this).data('max'));
	        props.limits.end = new Date($(this).data('max'));
	        if (endDate < today) {
	          props.month = endDate.getMonth();
	          props.year = endDate.getFullYear();
	        }
	      }
	      calendars[id] = new Calendar({
	        target: _findInstanceProperty($parent).call($parent, '.bookly-js-datepicker-calendar').get(0),
	        props: props
	      });
	      $(this).on('focus', function (e) {
	        calendars[id].show = true;
	      });
	      calendars[id].$on('change', function () {
	        calendars[id].show = false;
	        $that.val(formatDate$1(calendars[id].date));
	      });
	      $('span', $parent).on('click', function (e) {
	        calendars[id].date = null;
	        $that.val('');
	      });
	    });
	    if (intlTelInput.enabled) {
	      window.booklyIntlTelInput($phone_field.get(0), {
	        preferredCountries: [intlTelInput.country],
	        initialCountry: intlTelInput.country,
	        geoIpLookup: function (callback) {
	          $.get('https://ipinfo.io', function () {}, 'jsonp').always(function (resp) {
	            var countryCode = resp && resp.country ? resp.country : '';
	            callback(countryCode);
	          });
	        }
	      });
	    }
	    // Init modals.
	    _findInstanceProperty($container).call($container, '.bookly-js-modal.' + params.form_id).remove();
	    $modals.addClass(params.form_id).appendTo($container).on('click', '.bookly-js-close', function (e) {
	      var _context11, _context12, _context13;
	      e.preventDefault();
	      _findInstanceProperty(_context11 = _findInstanceProperty(_context12 = _findInstanceProperty(_context13 = $(e.delegateTarget).removeClass('bookly-in')).call(_context13, 'form').trigger('reset').end()).call(_context12, 'input').removeClass('bookly-error').end()).call(_context11, '.bookly-label-error').html('');
	    });
	    // Login modal.
	    $('.bookly-js-login-show', $container).on('click', function (e) {
	      e.preventDefault();
	      $login_modal.addClass('bookly-in');
	    });
	    $('button:submit', $login_modal).on('click', function (e) {
	      e.preventDefault();
	      var ladda = Ladda.create(this);
	      ladda.start();
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_wp_user_login',
	          form_id: params.form_id,
	          log: _findInstanceProperty($login_modal).call($login_modal, '[name="log"]').val(),
	          pwd: _findInstanceProperty($login_modal).call($login_modal, '[name="pwd"]').val(),
	          rememberme: _findInstanceProperty($login_modal).call($login_modal, '[name="rememberme"]').prop('checked') ? 1 : 0
	        }
	      }).then(response => {
	        BooklyL10n.csrf_token = response.data.csrf_token;
	        $guest_info.fadeOut('slow');
	        populateForm(response);
	        $login_modal.removeClass('bookly-in');
	      }).catch(response => {
	        if (response.error == 'incorrect_username_password') {
	          _findInstanceProperty($login_modal).call($login_modal, 'input').addClass('bookly-error');
	          _findInstanceProperty($login_modal).call($login_modal, '.bookly-label-error').html(opt[params.form_id].errors[response.error]);
	        }
	      }).finally(() => {
	        ladda.stop();
	      });
	    });
	    // Customer duplicate modal.
	    $('button:submit', $cst_modal).on('click', function (e) {
	      e.preventDefault();
	      $cst_modal.removeClass('bookly-in');
	      $next_btn.trigger('click', [1]);
	    });
	    // Verification code modal.
	    $('button:submit', $verification_modal).on('click', function (e) {
	      e.preventDefault();
	      $verification_modal.removeClass('bookly-in');
	      $next_btn.trigger('click');
	    });
	    // Facebook login button.
	    if (opt[params.form_id].hasOwnProperty('facebook') && opt[params.form_id].facebook.enabled && typeof FB !== 'undefined') {
	      FB.XFBML.parse($('.bookly-js-fb-login-button', $container).parent().get(0));
	      opt[params.form_id].facebook.onStatusChange = function (response) {
	        if (response.status === 'connected') {
	          opt[params.form_id].facebook.enabled = false;
	          opt[params.form_id].facebook.onStatusChange = undefined;
	          $guest_info.fadeOut('slow', function () {
	            // Hide buttons in all Bookly forms on the page.
	            $('.bookly-js-fb-login-button').hide();
	          });
	          FB.api('/me', {
	            fields: 'id,name,first_name,last_name,email'
	          }, function (userInfo) {
	            booklyAjax({
	              type: 'POST',
	              data: $.extend(userInfo, {
	                action: 'bookly_pro_facebook_login',
	                form_id: params.form_id
	              })
	            }).then(response => {
	              populateForm(response);
	            });
	          });
	        }
	      };
	    }
	    $next_btn.on('click', function (e, force_update_customer) {
	      e.stopPropagation();
	      e.preventDefault();

	      // Terms and conditions checkbox
	      let $terms = $('.bookly-js-terms', $container),
	        $terms_error = $('.bookly-js-terms-error', $container);
	      $terms_error.html('');
	      if ($terms.length && !$terms.prop('checked')) {
	        $terms_error.html(terms_error);
	      } else {
	        var _context14, _context15;
	        var info_fields = [],
	          custom_fields = {},
	          checkbox_values,
	          captcha_ids = [],
	          ladda = laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $.globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }

	        // Customer information fields.
	        $('div.bookly-js-info-field-row', $container).each(function () {
	          var $this = $(this);
	          switch ($this.data('type')) {
	            case 'text-field':
	            case 'file':
	            case 'number':
	              info_fields.push({
	                id: $this.data('id'),
	                value: _findInstanceProperty($this).call($this, 'input.bookly-js-info-field').val()
	              });
	              break;
	            case 'textarea':
	              info_fields.push({
	                id: $this.data('id'),
	                value: _findInstanceProperty($this).call($this, 'textarea.bookly-js-info-field').val()
	              });
	              break;
	            case 'checkboxes':
	              checkbox_values = [];
	              _findInstanceProperty($this).call($this, 'input.bookly-js-info-field:checked').each(function () {
	                checkbox_values.push(this.value);
	              });
	              info_fields.push({
	                id: $this.data('id'),
	                value: checkbox_values
	              });
	              break;
	            case 'radio-buttons':
	              info_fields.push({
	                id: $this.data('id'),
	                value: _findInstanceProperty($this).call($this, 'input.bookly-js-info-field:checked').val() || null
	              });
	              break;
	            case 'drop-down':
	            case 'time':
	              info_fields.push({
	                id: $this.data('id'),
	                value: _findInstanceProperty($this).call($this, 'select.bookly-js-info-field').val()
	              });
	              break;
	            case 'date':
	              info_fields.push({
	                id: $this.data('id'),
	                value: calendars[_findInstanceProperty($this).call($this, '.bookly-js-datepicker-calendar-wrap').data('id')].date
	              });
	              break;
	          }
	        });
	        // Custom fields.
	        $('.bookly-custom-fields-container', $container).each(function () {
	          let $cf_container = $(this),
	            key = $cf_container.data('key'),
	            custom_fields_data = [];
	          $('div.bookly-custom-field-row', $cf_container).each(function () {
	            var $this = $(this);
	            if ($this.css('display') !== 'none') {
	              switch ($this.data('type')) {
	                case 'text-field':
	                case 'file':
	                case 'number':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: _findInstanceProperty($this).call($this, 'input.bookly-js-custom-field').val()
	                  });
	                  break;
	                case 'textarea':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: _findInstanceProperty($this).call($this, 'textarea.bookly-js-custom-field').val()
	                  });
	                  break;
	                case 'checkboxes':
	                  checkbox_values = [];
	                  _findInstanceProperty($this).call($this, 'input.bookly-js-custom-field:checked').each(function () {
	                    checkbox_values.push(this.value);
	                  });
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: checkbox_values
	                  });
	                  break;
	                case 'radio-buttons':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: _findInstanceProperty($this).call($this, 'input.bookly-js-custom-field:checked').val() || null
	                  });
	                  break;
	                case 'drop-down':
	                case 'time':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: _findInstanceProperty($this).call($this, 'select.bookly-js-custom-field').val()
	                  });
	                  break;
	                case 'date':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: calendars[_findInstanceProperty($this).call($this, '.bookly-js-datepicker-calendar-wrap').data('id')].date
	                  });
	                  break;
	                case 'captcha':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: _findInstanceProperty($this).call($this, 'input.bookly-js-custom-field').val()
	                  });
	                  captcha_ids.push($this.data('id'));
	                  break;
	              }
	            }
	          });
	          custom_fields[key] = {
	            custom_fields: custom_fields_data
	          };
	        });
	        var data = {
	          action: 'bookly_session_save',
	          form_id: params.form_id,
	          full_name: $full_name_field.val(),
	          first_name: $first_name_field.val(),
	          last_name: $last_name_field.val(),
	          phone: intlTelInput.enabled ? booklyGetPhoneNumber($phone_field.get(0)) : $phone_field.val(),
	          email: _trimInstanceProperty(_context14 = $email_field.val()).call(_context14),
	          email_confirm: $email_confirm_field.length === 1 ? _trimInstanceProperty(_context15 = $email_confirm_field.val()).call(_context15) : undefined,
	          birthday: {
	            day: $birthday_day_field.val(),
	            month: $birthday_month_field.val(),
	            year: $birthday_year_field.val()
	          },
	          full_address: $('.bookly-js-cst-address-autocomplete', $container).val(),
	          country: $address_country_field.val(),
	          state: $address_state_field.val(),
	          postcode: $address_postcode_field.val(),
	          city: $address_city_field.val(),
	          street: $address_street_field.val(),
	          street_number: $address_street_number_field.val(),
	          additional_address: $address_additional_field.val(),
	          address_iso: {
	            country: $address_country_field.data('short'),
	            state: $address_state_field.data('short')
	          },
	          info_fields: info_fields,
	          notes: $notes_field.val(),
	          cart: custom_fields,
	          captcha_ids: _JSON$stringify(captcha_ids),
	          force_update_customer: !update_details_dialog || force_update_customer,
	          verification_code: $verification_code.val()
	        };
	        // Error messages
	        $errors.empty();
	        $fields.removeClass('bookly-error');
	        booklyAjax({
	          type: 'POST',
	          data: data
	        }).then(response => {
	          if (woocommerce.enabled) {
	            var data = {
	              action: 'bookly_pro_add_to_woocommerce_cart',
	              form_id: params.form_id
	            };
	            booklyAjax({
	              type: 'POST',
	              data: data
	            }).then(response => {
	              window.location.href = response.data.target_url;
	            }).catch(response => {
	              ladda.stop();
	              handleErrorCartItemNotAvailable(response.data, params.form_id);
	            });
	          } else {
	            stepPayment({
	              form_id: params.form_id
	            });
	          }
	        }).catch(response => {
	          var $scroll_to = null;
	          if (response.appointments_limit_reached) {
	            stepComplete({
	              form_id: params.form_id,
	              error: 'appointments_limit_reached'
	            });
	          } else if (response.hasOwnProperty('verify')) {
	            ladda.stop();
	            _findInstanceProperty($verification_modal).call($verification_modal, '#bookly-verification-code-text').html(response.verify_text).end().addClass('bookly-in');
	            let $error = _findInstanceProperty($verification_modal).call($verification_modal, '.bookly-js-verification-code-error');
	            if (response.success === false && $verification_code.val()) {
	              _findInstanceProperty($verification_modal).call($verification_modal, '#bookly-verification-code').addClass('bookly-error');
	              $error.html(response.incorrect_code_text).show();
	            } else {
	              $error.hide();
	            }
	          } else if (response.group_skip_payment) {
	            booklyAjax({
	              type: 'POST',
	              data: {
	                action: 'bookly_save_appointment',
	                form_id: params.form_id
	              }
	            }).then(response => {
	              stepComplete({
	                form_id: params.form_id
	              });
	            });
	          } else {
	            ladda.stop();
	            var invalidClass = 'bookly-error',
	              validateFields = [{
	                name: 'full_name',
	                errorElement: $name_error,
	                formElement: $full_name_field
	              }, {
	                name: 'first_name',
	                errorElement: $first_name_error,
	                formElement: $first_name_field
	              }, {
	                name: 'last_name',
	                errorElement: $last_name_error,
	                formElement: $last_name_field
	              }, {
	                name: 'phone',
	                errorElement: $phone_error,
	                formElement: $phone_field
	              }, {
	                name: 'email',
	                errorElement: $email_error,
	                formElement: $email_field
	              }, {
	                name: 'email_confirm',
	                errorElement: $email_confirm_error,
	                formElement: $email_confirm_field
	              }, {
	                name: 'birthday_day',
	                errorElement: $birthday_day_error,
	                formElement: $birthday_day_field
	              }, {
	                name: 'birthday_month',
	                errorElement: $birthday_month_error,
	                formElement: $birthday_month_field
	              }, {
	                name: 'birthday_year',
	                errorElement: $birthday_year_error,
	                formElement: $birthday_year_field
	              }, {
	                name: 'country',
	                errorElement: $address_country_error,
	                formElement: $address_country_field
	              }, {
	                name: 'state',
	                errorElement: $address_state_error,
	                formElement: $address_state_field
	              }, {
	                name: 'postcode',
	                errorElement: $address_postcode_error,
	                formElement: $address_postcode_field
	              }, {
	                name: 'city',
	                errorElement: $address_city_error,
	                formElement: $address_city_field
	              }, {
	                name: 'street',
	                errorElement: $address_street_error,
	                formElement: $address_street_field
	              }, {
	                name: 'street_number',
	                errorElement: $address_street_number_error,
	                formElement: $address_street_number_field
	              }, {
	                name: 'additional_address',
	                errorElement: $address_additional_error,
	                formElement: $address_additional_field
	              }];
	            _forEachInstanceProperty(validateFields).call(validateFields, function (field) {
	              if (!response[field.name]) {
	                return;
	              }
	              field.errorElement.html(response[field.name]);
	              field.formElement.addClass(invalidClass);
	              if ($scroll_to === null) {
	                $scroll_to = field.formElement;
	              }
	            });
	            if (response.info_fields) {
	              $.each(response.info_fields, function (field_id, message) {
	                var $div = $('div.bookly-js-info-field-row[data-id="' + field_id + '"]', $container);
	                _findInstanceProperty($div).call($div, '.bookly-js-info-field-error').html(message);
	                _findInstanceProperty($div).call($div, '.bookly-js-info-field').addClass('bookly-error');
	                if ($scroll_to === null) {
	                  $scroll_to = _findInstanceProperty($div).call($div, '.bookly-js-info-field');
	                }
	              });
	            }
	            if (response.custom_fields) {
	              $.each(response.custom_fields, function (key, fields) {
	                $.each(fields, function (field_id, message) {
	                  var $custom_fields_collector = $('.bookly-custom-fields-container[data-key="' + key + '"]', $container);
	                  var $div = $('[data-id="' + field_id + '"]', $custom_fields_collector);
	                  _findInstanceProperty($div).call($div, '.bookly-custom-field-error').html(message);
	                  _findInstanceProperty($div).call($div, '.bookly-js-custom-field').addClass('bookly-error');
	                  if ($scroll_to === null) {
	                    $scroll_to = _findInstanceProperty($div).call($div, '.bookly-js-custom-field');
	                  }
	                });
	              });
	            }
	            if (response.customer) {
	              _findInstanceProperty($cst_modal).call($cst_modal, '.bookly-js-modal-body').html(response.customer).end().addClass('bookly-in');
	            }
	          }
	          if ($scroll_to !== null) {
	            scrollTo($scroll_to, params.form_id);
	          }
	        });
	      }
	    });
	    $('.bookly-js-back-step', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      if (!opt[params.form_id].skip_steps.cart) {
	        stepCart({
	          form_id: params.form_id
	        });
	      } else if (opt[params.form_id].no_time || opt[params.form_id].skip_steps.time) {
	        if (opt[params.form_id].no_extras || opt[params.form_id].skip_steps.extras) {
	          stepService({
	            form_id: params.form_id
	          });
	        } else {
	          stepExtras({
	            form_id: params.form_id
	          });
	        }
	      } else if (!_repeatInstanceProperty(opt[params.form_id].skip_steps) && opt[params.form_id].recurrence_enabled) {
	        stepRepeat({
	          form_id: params.form_id
	        });
	      } else if (!opt[params.form_id].skip_steps.extras && opt[params.form_id].step_extras == 'after_step_time' && !opt[params.form_id].no_extras) {
	        stepExtras({
	          form_id: params.form_id
	        });
	      } else {
	        stepTime({
	          form_id: params.form_id
	        });
	      }
	    });
	    $('.bookly-js-captcha-refresh', $container).on('click', function () {
	      $captcha.css('opacity', '0.5');
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_custom_fields_captcha_refresh',
	          form_id: params.form_id
	        }
	      }).then(response => {
	        $captcha.attr('src', response.data.captcha_url).on('load', function () {
	          $captcha.css('opacity', '1');
	        });
	      });
	    });
	  });

	  /**
	   * global function to init google places
	   */
	  function booklyInitGooglePlacesAutocomplete(bookly_forms) {
	    bookly_forms = bookly_forms || $('.bookly-form .bookly-details-step');
	    bookly_forms.each(function () {
	      initGooglePlacesAutocomplete($(this));
	    });
	  }

	  /**
	   * Addon: Google Maps Address
	   * @param {jQuery} [$container]
	   * @returns {boolean}
	   */
	  function initGooglePlacesAutocomplete($container) {
	    var autocompleteInput = _findInstanceProperty($container).call($container, '.bookly-js-cst-address-autocomplete');
	    if (!autocompleteInput.length) {
	      return false;
	    }
	    var autocomplete = new google.maps.places.Autocomplete(autocompleteInput[0], {
	        types: ['geocode']
	      }),
	      autocompleteFields = [{
	        selector: '.bookly-js-address-country',
	        val: function () {
	          return getFieldValueByType('country');
	        },
	        short: function () {
	          return getFieldValueByType('country', true);
	        }
	      }, {
	        selector: '.bookly-js-address-postcode',
	        val: function () {
	          return getFieldValueByType('postal_code');
	        }
	      }, {
	        selector: '.bookly-js-address-city',
	        val: function () {
	          return getFieldValueByType('locality') || getFieldValueByType('administrative_area_level_3') || getFieldValueByType('postal_town') || getFieldValueByType('sublocality_level_1');
	        }
	      }, {
	        selector: '.bookly-js-address-state',
	        val: function () {
	          return getFieldValueByType('administrative_area_level_1');
	        },
	        short: function () {
	          return getFieldValueByType('administrative_area_level_1', true);
	        }
	      }, {
	        selector: '.bookly-js-address-street',
	        val: function () {
	          return getFieldValueByType('route');
	        }
	      }, {
	        selector: '.bookly-js-address-street_number',
	        val: function () {
	          return getFieldValueByType('street_number') || getFieldValueByType('premise');
	        }
	      }, {
	        selector: '.bookly-js-address-additional_address',
	        val: function () {
	          return getFieldValueByType('subpremise') || getFieldValueByType('neighborhood') || getFieldValueByType('sublocality');
	        }
	      }];
	    var getFieldValueByType = function (type, useShortName) {
	      var addressComponents = autocomplete.getPlace().address_components;
	      for (var i = 0; i < addressComponents.length; i++) {
	        var addressType = addressComponents[i].types[0];
	        if (addressType === type) {
	          return useShortName ? addressComponents[i]['short_name'] : addressComponents[i]['long_name'];
	        }
	      }
	      return '';
	    };
	    autocomplete.addListener('place_changed', function () {
	      _forEachInstanceProperty(autocompleteFields).call(autocompleteFields, function (field) {
	        var element = _findInstanceProperty($container).call($container, field.selector);
	        if (element.length === 0) {
	          return;
	        }
	        element.val(field.val());
	        if (typeof field.short == 'function') {
	          element.data('short', field.short());
	        }
	      });
	    });
	  }
	}

	/**
	 * Cart step.
	 */
	function stepCart(params, error) {
	  if (opt[params.form_id].skip_steps.cart) {
	    stepDetails(params);
	  } else {
	    if (params && params.from_step) {
	      // Record previous step if it was given in params.
	      opt[params.form_id].cart_prev_step = params.from_step;
	    }
	    let data = $.extend({
	        action: 'bookly_render_cart'
	      }, params),
	      $container = opt[params.form_id].$container;
	    booklyAjax({
	      data
	    }).then(response => {
	      $container.html(response.html);
	      if (error) {
	        $('.bookly-label-error', $container).html(error.message);
	        $('tr[data-cart-key="' + error.failed_key + '"]', $container).addClass('bookly-label-error');
	      } else {
	        $('.bookly-label-error', $container).hide();
	      }
	      scrollTo($container, params.form_id);
	      const customJS = response.custom_js;
	      $('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $.globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }
	        stepDetails({
	          form_id: params.form_id
	        });
	      });
	      $('.bookly-add-item', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          new_chain: true
	        });
	      });
	      // 'BACK' button.
	      $('.bookly-js-back-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        switch (opt[params.form_id].cart_prev_step) {
	          case 'service':
	            stepService({
	              form_id: params.form_id
	            });
	            break;
	          case 'extras':
	            stepExtras({
	              form_id: params.form_id
	            });
	            break;
	          case 'time':
	            stepTime({
	              form_id: params.form_id
	            });
	            break;
	          case 'repeat':
	            stepRepeat({
	              form_id: params.form_id
	            });
	            break;
	          default:
	            stepService({
	              form_id: params.form_id
	            });
	        }
	      });
	      $('.bookly-js-actions button', $container).on('click', function () {
	        laddaStart(this);
	        let $this = $(this),
	          $cart_item = $this.closest('tr');
	        switch ($this.data('action')) {
	          case 'drop':
	            booklyAjax({
	              data: {
	                action: 'bookly_cart_drop_item',
	                form_id: params.form_id,
	                cart_key: $cart_item.data('cart-key')
	              }
	            }).then(response => {
	              let remove_cart_key = $cart_item.data('cart-key'),
	                $trs_to_remove = $('tr[data-cart-key="' + remove_cart_key + '"]', $container);
	              $cart_item.delay(300).fadeOut(200, function () {
	                if (response.data.total_waiting_list) {
	                  $('.bookly-js-waiting-list-price', $container).html(response.data.waiting_list_price);
	                  $('.bookly-js-waiting-list-deposit', $container).html(response.data.waiting_list_deposit);
	                } else {
	                  $('.bookly-js-waiting-list-price', $container).closest('tr').remove();
	                }
	                $('.bookly-js-subtotal-price', $container).html(response.data.subtotal_price);
	                $('.bookly-js-subtotal-deposit', $container).html(response.data.subtotal_deposit);
	                $('.bookly-js-pay-now-deposit', $container).html(response.data.pay_now_deposit);
	                $('.bookly-js-pay-now-tax', $container).html(response.data.pay_now_tax);
	                $('.bookly-js-total-price', $container).html(response.data.total_price);
	                $('.bookly-js-total-tax', $container).html(response.data.total_tax);
	                $trs_to_remove.remove();
	                if ($('tr[data-cart-key]').length == 0) {
	                  $('.bookly-js-back-step', $container).hide();
	                  $('.bookly-js-next-step', $container).hide();
	                }
	              });
	            });
	            break;
	          case 'edit':
	            stepService({
	              form_id: params.form_id,
	              edit_cart_item: $cart_item.data('cart-key')
	            });
	            break;
	        }
	      });
	    });
	  }
	}

	/**
	 * Repeat step.
	 */
	function stepRepeat(params, error) {
	  if (_repeatInstanceProperty(opt[params.form_id].skip_steps)) {
	    stepCart(params, error);
	  } else {
	    let data = $.extend({
	        action: 'bookly_render_repeat'
	      }, params),
	      $container = opt[params.form_id].$container;
	    booklyAjax({
	      data
	    }).then(response => {
	      var _context3, _context4;
	      $container.html(response.html);
	      scrollTo($container, params.form_id);
	      let $repeat_enabled = $('.bookly-js-repeat-appointment-enabled', $container),
	        $next_step = $('.bookly-js-next-step', $container),
	        $repeat_container = $('.bookly-js-repeat-variants-container', $container),
	        $variants = $('[class^="bookly-js-variant"]', $repeat_container),
	        $repeat_variant = $('.bookly-js-repeat-variant', $repeat_container),
	        $button_get_schedule = $('.bookly-js-get-schedule', $repeat_container),
	        $variant_weekly = $('.bookly-js-variant-weekly', $repeat_container),
	        $variant_monthly = $('.bookly-js-repeat-variant-monthly', $repeat_container),
	        $date_until = $('.bookly-js-repeat-until', $repeat_container),
	        $repeat_times = $('.bookly-js-repeat-times', $repeat_container),
	        $monthly_specific_day = $('.bookly-js-monthly-specific-day', $repeat_container),
	        $monthly_week_day = $('.bookly-js-monthly-week-day', $repeat_container),
	        $repeat_every_day = $('.bookly-js-repeat-daily-every', $repeat_container),
	        $schedule_container = $('.bookly-js-schedule-container', $container),
	        $days_error = $('.bookly-js-days-error', $repeat_container),
	        $schedule_slots = $('.bookly-js-schedule-slots', $schedule_container),
	        $intersection_info = $('.bookly-js-intersection-info', $schedule_container),
	        $info_help = $('.bookly-js-schedule-help', $schedule_container),
	        $info_wells = $('.bookly-well', $schedule_container),
	        $pagination = $('.bookly-pagination', $schedule_container),
	        $schedule_row_template = $('.bookly-schedule-row-template .bookly-schedule-row', $schedule_container),
	        pages_warning_info = response.pages_warning_info,
	        short_date_format = response.short_date_format,
	        bound_date = {
	          min: response.date_min || true,
	          max: response.date_max || true
	        },
	        schedule = [],
	        customJS = response.custom_js,
	        schedule_calendar;
	      var repeat = {
	        prepareButtonNextState: function () {
	          // Disable/Enable next button
	          var is_disabled = $next_step.prop('disabled'),
	            new_prop_disabled = schedule.length == 0;
	          for (var i = 0; i < schedule.length; i++) {
	            if (is_disabled) {
	              if (!schedule[i].deleted) {
	                new_prop_disabled = false;
	                break;
	              }
	            } else if (schedule[i].deleted) {
	              new_prop_disabled = true;
	            } else {
	              new_prop_disabled = false;
	              break;
	            }
	          }
	          $next_step.prop('disabled', new_prop_disabled);
	        },
	        addTimeSlotControl: function ($schedule_row, options, preferred_time, selected_time) {
	          var $time = '';
	          if (options.length) {
	            var prefer;
	            $time = $('<select/>');
	            $.each(options, function (index, option) {
	              var $option = $('<option/>');
	              $option.text(option.title).val(option.value);
	              if (option.disabled) {
	                $option.attr('disabled', 'disabled');
	              }
	              $time.append($option);
	              if (!prefer && !option.disabled) {
	                if (option.title == preferred_time) {
	                  // Select by time title.
	                  $time.val(option.value);
	                  prefer = true;
	                } else if (option.title == selected_time) {
	                  $time.val(option.value);
	                }
	              }
	            });
	          }
	          _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-js-schedule-time').html($time);
	          _findInstanceProperty($schedule_row).call($schedule_row, 'div.bookly-label-error').toggle(!options.length);
	        },
	        renderSchedulePage: function (page) {
	          let $row,
	            count = schedule.length,
	            rows_on_page = 5,
	            start = rows_on_page * page - rows_on_page,
	            warning_pages = [],
	            previousPage = function (e) {
	              e.preventDefault();
	              let page = _parseInt$4(_findInstanceProperty($pagination).call($pagination, '.active').data('page'));
	              if (page > 1) {
	                repeat.renderSchedulePage(page - 1);
	              }
	            },
	            nextPage = function (e) {
	              e.preventDefault();
	              let page = _parseInt$4(_findInstanceProperty($pagination).call($pagination, '.active').data('page'));
	              if (page < count / rows_on_page) {
	                repeat.renderSchedulePage(page + 1);
	              }
	            };
	          $schedule_slots.html('');
	          for (var i = start, j = 0; j < rows_on_page && i < count; i++, j++) {
	            $row = $schedule_row_template.clone();
	            $row.data('datetime', schedule[i].datetime);
	            $row.data('index', schedule[i].index);
	            $('> div:first-child', $row).html(schedule[i].index);
	            $('.bookly-schedule-date', $row).html(schedule[i].display_date);
	            if (schedule[i].all_day_service_time !== undefined) {
	              $('.bookly-js-schedule-time', $row).hide();
	              $('.bookly-js-schedule-all-day-time', $row).html(schedule[i].all_day_service_time).show();
	            } else {
	              $('.bookly-js-schedule-time', $row).html(schedule[i].display_time).show();
	              $('.bookly-js-schedule-all-day-time', $row).hide();
	            }
	            if (schedule[i].another_time) {
	              $('.bookly-schedule-intersect', $row).show();
	            }
	            if (schedule[i].deleted) {
	              _findInstanceProperty($row).call($row, '.bookly-schedule-appointment').addClass('bookly-appointment-hidden');
	            }
	            $schedule_slots.append($row);
	          }
	          if (count > rows_on_page) {
	            var $btn = $('<li/>').append($('<a>', {
	              href: '#',
	              text: '«'
	            }));
	            $btn.on('click', previousPage).keypress(function (e) {
	              e.preventDefault();
	              if (e.which == 13 || e.which == 32) {
	                previousPage(e);
	              }
	            });
	            $pagination.html($btn);
	            for (i = 0, j = 1; i < count; i += 5, j++) {
	              $btn = $('<li/>', {
	                'data-page': j
	              }).append($('<a>', {
	                href: '#',
	                text: j
	              }));
	              $pagination.append($btn);
	              $btn.on('click', function (e) {
	                e.preventDefault();
	                repeat.renderSchedulePage($(this).data('page'));
	              }).keypress(function (e) {
	                e.preventDefault();
	                if (e.which == 13 || e.which == 32) {
	                  repeat.renderSchedulePage($(this).data('page'));
	                }
	              });
	            }
	            _findInstanceProperty($pagination).call($pagination, 'li:eq(' + page + ')').addClass('active');
	            $btn = $('<li/>').append($('<a>', {
	              href: '#',
	              text: '»'
	            }));
	            $btn.on('click', nextPage).keypress(function (e) {
	              e.preventDefault();
	              if (e.which == 13 || e.which == 32) {
	                nextPage(e);
	              }
	            });
	            $pagination.append($btn).show();
	            for (i = 0; i < count; i++) {
	              if (schedule[i].another_time) {
	                page = _parseInt$4(i / rows_on_page) + 1;
	                warning_pages.push(page);
	                i = page * rows_on_page - 1;
	              }
	            }
	            if (warning_pages.length > 0) {
	              $intersection_info.html(pages_warning_info.replace('{list}', warning_pages.join(', ')));
	            }
	            $info_wells.toggle(warning_pages.length > 0);
	            $pagination.toggle(count > rows_on_page);
	          } else {
	            $pagination.hide();
	            $info_wells.hide();
	            for (i = 0; i < count; i++) {
	              if (schedule[i].another_time) {
	                $info_help.show();
	                break;
	              }
	            }
	          }
	        },
	        renderFullSchedule: function (data) {
	          schedule = data; // it has global scope
	          // Prefer time is display time selected on step time.
	          var preferred_time = null;
	          $.each(schedule, function (index, item) {
	            if (!preferred_time && !item.another_time) {
	              preferred_time = item.display_time;
	            }
	          });
	          repeat.renderSchedulePage(1);
	          $schedule_container.show();
	          $next_step.prop('disabled', schedule.length == 0);
	          $schedule_slots.on('click', 'button[data-action]', function () {
	            var $schedule_row = $(this).closest('.bookly-schedule-row');
	            var row_index = $schedule_row.data('index') - 1;
	            switch ($(this).data('action')) {
	              case 'drop':
	                schedule[row_index].deleted = true;
	                _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-appointment').addClass('bookly-appointment-hidden');
	                repeat.prepareButtonNextState();
	                break;
	              case 'restore':
	                schedule[row_index].deleted = false;
	                _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-appointment').removeClass('bookly-appointment-hidden');
	                $next_step.prop('disabled', false);
	                break;
	              case 'edit':
	                _findInstanceProperty($schedule_slots).call($schedule_slots, '.bookly-schedule-row .bookly-js-datepicker-container').each(function () {
	                  let $row = $(this).closest('.bookly-schedule-row'),
	                    index = $row.data('index') - 1;
	                  _findInstanceProperty($row).call($row, 'button[data-action="edit"]').show();
	                  _findInstanceProperty($row).call($row, 'button[data-action="save"]').hide();
	                  _findInstanceProperty($row).call($row, '.bookly-schedule-date').html(schedule[index].display_date);
	                  _findInstanceProperty($row).call($row, '.bookly-js-schedule-time').html(schedule[index].display_time);
	                });
	                let slots = JSON.parse(schedule[row_index].slots),
	                  current_date = slots[0][2].split(' ')[0],
	                  $date = $('<input/>', {
	                    type: 'text',
	                    value: formatDate$1(current_date, short_date_format)
	                  }),
	                  $edit_button = $(this),
	                  ladda_round = laddaStart(this);
	                $date.data('date', current_date);
	                _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-date').html($.merge($date, $('<div class="bookly:relative bookly:w-full bookly:z-10 bookly-js-datepicker-container" style="font-weight: normal;"><div class="bookly:absolute bookly:top-1 bookly:left-0 bookly:w-72 bookly:p-0 bookly:bg-white bookly-js-datepicker-calendar"></div></div>')));
	                $date = _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-date input');
	                if (schedule_calendar) {
	                  schedule_calendar.$destroy();
	                }
	                $(document).on('click', function (e) {
	                  if ($(e.target).closest('.bookly-schedule-date').length === 0) {
	                    schedule_calendar.show = false;
	                  }
	                });
	                schedule_calendar = new Calendar({
	                  target: _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-js-datepicker-calendar').get(0),
	                  props: {
	                    datePicker: BooklyL10nGlobal.datePicker,
	                    loading: false,
	                    show: false,
	                    border: true,
	                    date: current_date,
	                    startDate: new Date(current_date),
	                    layout: opt[params.form_id].datepicker_mode
	                  }
	                });
	                $date.on('focus', function (e) {
	                  schedule_calendar.show = true;
	                });
	                $date.on('change', function () {
	                  var exclude = [];
	                  $.each(schedule, function (index, item) {
	                    if (row_index != index && !item.deleted) {
	                      exclude.push(item.slots);
	                    }
	                  });
	                  booklyAjax({
	                    type: 'POST',
	                    data: {
	                      action: 'bookly_recurring_appointments_get_daily_customer_schedule',
	                      date: $(this).data('date'),
	                      form_id: params.form_id,
	                      exclude: exclude
	                    }
	                  }).then(response => {
	                    $edit_button.hide();
	                    ladda_round.stop();
	                    if (response.data.length) {
	                      repeat.addTimeSlotControl($schedule_row, response.data[0].options, preferred_time, schedule[row_index].display_time, response.data[0].all_day_service_time);
	                      _findInstanceProperty($schedule_row).call($schedule_row, 'button[data-action="save"]').show();
	                    } else {
	                      repeat.addTimeSlotControl($schedule_row, []);
	                      _findInstanceProperty($schedule_row).call($schedule_row, 'button[data-action="save"]').hide();
	                    }
	                  });
	                });
	                schedule_calendar.$on('change', function () {
	                  schedule_calendar.show = false;
	                  $date.data('date', schedule_calendar.date);
	                  $date.val(formatDate$1(schedule_calendar.date, short_date_format));
	                  $date.trigger('change');
	                });
	                $date.trigger('change');
	                break;
	              case 'save':
	                $(this).hide();
	                _findInstanceProperty($schedule_row).call($schedule_row, 'button[data-action="edit"]').show();
	                var $date_container = _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-date'),
	                  $time_container = _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-js-schedule-time'),
	                  $select = _findInstanceProperty($time_container).call($time_container, 'select'),
	                  option = _findInstanceProperty($select).call($select, 'option:selected');
	                schedule[row_index].slots = $select.val();
	                schedule[row_index].display_date = _findInstanceProperty($date_container).call($date_container, 'input').val();
	                schedule[row_index].display_time = option.text();
	                $date_container.html(schedule[row_index].display_date);
	                $time_container.html(schedule[row_index].display_time);
	                break;
	            }
	          });
	        },
	        isDateMatchesSelections: function (current_date) {
	          switch ($repeat_variant.val()) {
	            case 'daily':
	              if (($repeat_every_day.val() > 6 || $.inArray(current_date.format('ddd').toLowerCase(), repeat.week_days) != -1) && current_date.diff(repeat.date_from, 'days') % $repeat_every_day.val() == 0) {
	                return true;
	              }
	              break;
	            case 'weekly':
	            case 'biweekly':
	              if (($repeat_variant.val() == 'weekly' || current_date.diff(repeat.date_from.clone().startOf('isoWeek'), 'weeks') % 2 == 0) && $.inArray(current_date.format('ddd').toLowerCase(), repeat.checked_week_days) != -1) {
	                return true;
	              }
	              break;
	            case 'monthly':
	              switch ($variant_monthly.val()) {
	                case 'specific':
	                  if (current_date.format('D') == $monthly_specific_day.val()) {
	                    return true;
	                  }
	                  break;
	                case 'last':
	                  if (current_date.format('ddd').toLowerCase() == $monthly_week_day.val() && current_date.clone().endOf('month').diff(current_date, 'days') < 7) {
	                    return true;
	                  }
	                  break;
	                default:
	                  var month_diff = current_date.diff(current_date.clone().startOf('month'), 'days');
	                  if (current_date.format('ddd').toLowerCase() == $monthly_week_day.val() && month_diff >= ($variant_monthly.prop('selectedIndex') - 1) * 7 && month_diff < $variant_monthly.prop('selectedIndex') * 7) {
	                    return true;
	                  }
	              }
	              break;
	          }
	          return false;
	        },
	        updateRepeatDate: function () {
	          var _context;
	          var number_of_times = 0,
	            repeat_times = $repeat_times.val(),
	            date_from = _sliceInstanceProperty$1(_context = bound_date.min).call(_context),
	            moment_until = moment(until_calendar.date).add(5, 'years');
	          date_from[1]++;
	          repeat.date_from = moment(date_from.join(','), 'YYYY,M,D');
	          repeat.week_days = [];
	          _findInstanceProperty($monthly_week_day).call($monthly_week_day, 'option').each(function () {
	            repeat.week_days.push($(this).val());
	          });
	          repeat.checked_week_days = [];
	          $('.bookly-js-week-days input:checked', $repeat_container).each(function () {
	            repeat.checked_week_days.push(this.value);
	          });
	          var current_date = repeat.date_from.clone();
	          do {
	            if (repeat.isDateMatchesSelections(current_date)) {
	              number_of_times++;
	            }
	            current_date.add(1, 'days');
	          } while (number_of_times < repeat_times && current_date.isBefore(moment_until));
	          current_date.subtract(1, 'days');
	          until_calendar.date = current_date.format('YYYY-MM-DD');
	          until_calendar.startDate = new Date(current_date.format('YYYY-MM-DD'));
	          $date_until.val(current_date.format(BooklyL10nGlobal.datePicker.format));
	        },
	        updateRepeatTimes: function () {
	          var _context2;
	          var number_of_times = 0,
	            date_from = _sliceInstanceProperty$1(_context2 = bound_date.min).call(_context2),
	            moment_until = moment(until_calendar.date).add(1, 'days');
	          date_from[1]++;
	          repeat.date_from = moment(date_from.join(','), 'YYYY,M,D');
	          repeat.week_days = [];
	          _findInstanceProperty($monthly_week_day).call($monthly_week_day, 'option').each(function () {
	            repeat.week_days.push($(this).val());
	          });
	          repeat.checked_week_days = [];
	          $('.bookly-js-week-days input:checked', $repeat_container).each(function () {
	            repeat.checked_week_days.push(this.value);
	          });
	          var current_date = repeat.date_from.clone();
	          do {
	            if (repeat.isDateMatchesSelections(current_date)) {
	              number_of_times++;
	            }
	            current_date.add(1, 'days');
	          } while (current_date.isBefore(moment_until));
	          $repeat_times.val(number_of_times);
	        }
	      };
	      let until_calendar = new Calendar({
	        target: _findInstanceProperty(_context3 = $date_until.parent()).call(_context3, '.bookly-js-datepicker-calendar').get(0),
	        props: {
	          datePicker: BooklyL10nGlobal.datePicker,
	          loading: false,
	          show: false,
	          border: true,
	          date: $date_until.data('value'),
	          startDate: new Date($date_until.data('value')),
	          limits: {
	            start: response.date_min ? new Date(response.date_min[0], response.date_min[1], response.date_min[2]) : new Date(),
	            end: response.date_max ? new Date(response.date_max[0], response.date_max[1], response.date_max[2]) : false
	          },
	          layout: opt[params.form_id].datepicker_mode
	        }
	      });
	      $date_until.val(formatDate$1($date_until.data('value')));
	      $(document).on('click', function (e) {
	        if ($(e.target).closest('.bookly-js-repeat-until-wrap').length === 0) {
	          until_calendar.show = false;
	        }
	      });
	      $date_until.on('focus', function (e) {
	        until_calendar.show = true;
	      });
	      until_calendar.$on('change', function () {
	        until_calendar.show = false;
	        $date_until.val(formatDate$1(until_calendar.date));
	      });
	      var open_repeat_onchange = $repeat_enabled.on('change', function () {
	        $repeat_container.toggle($(this).prop('checked'));
	        if ($(this).prop('checked')) {
	          repeat.prepareButtonNextState();
	        } else {
	          $next_step.prop('disabled', false);
	        }
	      });
	      if (response.repeated) {
	        var repeat_data = response.repeat_data;
	        var repeat_params = repeat_data.params;
	        $repeat_enabled.prop('checked', true);
	        $repeat_variant.val(_repeatInstanceProperty(repeat_data));
	        until_calendar.date = repeat_data.until;
	        $date_until.val(formatDate$1(repeat_data.until));
	        switch (_repeatInstanceProperty(repeat_data)) {
	          case 'daily':
	            $repeat_every_day.val(_everyInstanceProperty(repeat_params));
	            break;
	          case 'weekly':
	          //break skipped
	          case 'biweekly':
	            $('.bookly-js-week-days input[type="checkbox"]', $repeat_container).prop('checked', false).parent().removeClass('active');
	            _forEachInstanceProperty(_context4 = repeat_params.on).call(_context4, function (val) {
	              $('.bookly-js-week-days input:checkbox[value=' + val + ']', $repeat_container).prop('checked', true);
	            });
	            break;
	          case 'monthly':
	            if (repeat_params.on === 'day') {
	              $variant_monthly.val('specific');
	              $('.bookly-js-monthly-specific-day[value=' + repeat_params.day + ']', $repeat_container).prop('checked', true);
	            } else {
	              $variant_monthly.val(repeat_params.on);
	              $monthly_week_day.val(repeat_params.weekday);
	            }
	            break;
	        }
	        repeat.renderFullSchedule(response.schedule);
	      }
	      open_repeat_onchange.trigger('change');
	      if (!response.could_be_repeated) {
	        $repeat_enabled.attr('disabled', true);
	      }
	      $repeat_variant.on('change', function () {
	        $variants.hide();
	        _findInstanceProperty($repeat_container).call($repeat_container, '.bookly-js-variant-' + this.value).show();
	        repeat.updateRepeatTimes();
	      }).trigger('change');
	      $variant_monthly.on('change', function () {
	        $monthly_week_day.toggle(this.value != 'specific');
	        $monthly_specific_day.toggle(this.value == 'specific');
	        repeat.updateRepeatTimes();
	      }).trigger('change');
	      $('.bookly-js-week-days input', $repeat_container).on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      $monthly_specific_day.val(response.date_min[2]);
	      $monthly_specific_day.on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      $monthly_week_day.on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      until_calendar.$on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      $repeat_every_day.on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      $repeat_times.on('change', function () {
	        repeat.updateRepeatDate();
	      });
	      $button_get_schedule.on('click', function () {
	        $schedule_container.hide();
	        let data = {
	            action: 'bookly_recurring_appointments_get_customer_schedule',
	            form_id: params.form_id,
	            repeat: $repeat_variant.val(),
	            until: until_calendar.date,
	            params: {}
	          },
	          ladda = laddaStart(this);
	        switch (_repeatInstanceProperty(data)) {
	          case 'daily':
	            data.params = {
	              every: $repeat_every_day.val()
	            };
	            break;
	          case 'weekly':
	          case 'biweekly':
	            data.params.on = [];
	            $('.bookly-js-week-days input[type="checkbox"]:checked', $variant_weekly).each(function () {
	              data.params.on.push(this.value);
	            });
	            if (data.params.on.length == 0) {
	              $days_error.toggle(true);
	              ladda.stop();
	              return false;
	            } else {
	              $days_error.toggle(false);
	            }
	            break;
	          case 'monthly':
	            if ($variant_monthly.val() == 'specific') {
	              data.params = {
	                on: 'day',
	                day: $monthly_specific_day.val()
	              };
	            } else {
	              data.params = {
	                on: $variant_monthly.val(),
	                weekday: $monthly_week_day.val()
	              };
	            }
	            break;
	        }
	        $schedule_slots.off('click');
	        booklyAjax({
	          type: 'POST',
	          data: data
	        }).then(response => {
	          repeat.renderFullSchedule(response.data);
	          ladda.stop();
	        });
	      });
	      $('.bookly-js-back-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        booklyAjax({
	          type: 'POST',
	          data: {
	            action: 'bookly_session_save',
	            form_id: params.form_id,
	            unrepeat: 1
	          }
	        }).then(response => {
	          if (!opt[params.form_id].skip_steps.extras && opt[params.form_id].step_extras == 'after_step_time' && !opt[params.form_id].no_extras) {
	            stepExtras({
	              form_id: params.form_id
	            });
	          } else {
	            stepTime({
	              form_id: params.form_id
	            });
	          }
	        });
	      });
	      $('.bookly-js-go-to-cart', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepCart({
	          form_id: params.form_id,
	          from_step: 'repeat'
	        });
	      });
	      $('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $.globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }
	        if ($repeat_enabled.is(':checked')) {
	          var slots_to_send = [];
	          var repeat = 0;
	          _forEachInstanceProperty(schedule).call(schedule, function (item) {
	            if (!item.deleted) {
	              var slots = JSON.parse(item.slots);
	              slots_to_send = _concatInstanceProperty(slots_to_send).call(slots_to_send, slots);
	              repeat++;
	            }
	          });
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_session_save',
	              form_id: params.form_id,
	              slots: _JSON$stringify(slots_to_send),
	              repeat: repeat
	            }
	          }).then(response => {
	            stepCart({
	              form_id: params.form_id,
	              add_to_cart: true,
	              from_step: 'repeat'
	            });
	          });
	        } else {
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_session_save',
	              form_id: params.form_id,
	              unrepeat: 1
	            }
	          }).then(response => {
	            stepCart({
	              form_id: params.form_id,
	              add_to_cart: true,
	              from_step: 'repeat'
	            });
	          });
	        }
	      });
	    });
	  }
	}

	/**
	 * Time step.
	 */
	function stepTime(params, error_message) {
	  if (opt[params.form_id].no_time || opt[params.form_id].skip_steps.time) {
	    if (!opt[params.form_id].skip_steps.extras && opt[params.form_id].step_extras == 'after_step_time' && !opt[params.form_id].no_extras) {
	      stepExtras({
	        form_id: params.form_id
	      });
	    } else if (!opt[params.form_id].skip_steps.cart) {
	      stepCart({
	        form_id: params.form_id,
	        add_to_cart: true,
	        from_step: params && params.prev_step ? params.prev_step : 'service'
	      });
	    } else {
	      stepDetails({
	        form_id: params.form_id,
	        add_to_cart: true
	      });
	    }
	    return;
	  }
	  var data = {
	      action: 'bookly_render_time'
	    },
	    $container = opt[params.form_id].$container;
	  if (opt[params.form_id].skip_steps.service && opt[params.form_id].use_client_time_zone) {
	    // If Service step is skipped then we need to send time zone offset.
	    data.time_zone = opt[params.form_id].timeZone;
	    data.time_zone_offset = opt[params.form_id].timeZoneOffset;
	  }
	  $.extend(data, params);
	  let columnizerObserver = false;
	  let lastObserverTime = 0;
	  let lastObserverWidth = 0;
	  let loadedMonths = [];

	  // Build slots html
	  function prepareSlotsHtml(slots_data, selected_date) {
	    var response = {};
	    $.each(slots_data, function (group, group_slots) {
	      var html = '<button class="bookly-day" value="' + group + '">' + group_slots.title + '</button>';
	      $.each(group_slots.slots, function (id, slot) {
	        html += '<button value="' + _JSON$stringify(slot.data).replace(/"/g, '&quot;') + '" data-group="' + group + '" class="bookly-hour' + (slot.special_hour ? ' bookly-slot-in-special-hour' : '') + (slot.status == 'waiting-list' ? ' bookly-slot-in-waiting-list' : slot.status == 'booked' ? ' booked' : '') + '"' + (slot.status == 'booked' ? ' disabled' : '') + '>' + '<span class="ladda-label bookly-time-main' + (slot.data[0][2] == selected_date ? ' bookly-bold' : '') + '">' + '<i class="bookly-hour-icon"><span></span></i>' + slot.time_text + '<span class="bookly-time-additional' + (slot.status == 'waiting-list' ? ' bookly-waiting-list' : '') + '"> ' + slot.additional_text + '</span></span>' + '</button>';
	      });
	      response[group] = html;
	    });
	    return response;
	  }
	  let requestRenderTime = requestCancellable(),
	    requestSessionSave = requestCancellable();
	  requestRenderTime.booklyAjax({
	    data
	  }).then(response => {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    var $columnizer_wrap = $('.bookly-columnizer-wrap', $container),
	      $columnizer = $('.bookly-columnizer', $columnizer_wrap),
	      $time_next_button = $('.bookly-time-next', $container),
	      $time_prev_button = $('.bookly-time-prev', $container),
	      $current_screen = null,
	      slot_height = 36,
	      column_width = response.time_slots_wide ? 205 : 127,
	      column_class = response.time_slots_wide ? 'bookly-column bookly-column-wide' : 'bookly-column',
	      columns = 0,
	      screen_index = 0,
	      has_more_slots = response.has_more_slots,
	      show_calendar = response.show_calendar,
	      is_rtl = response.is_rtl,
	      $screens,
	      slots_per_column,
	      columns_per_screen,
	      show_day_per_column = response.day_one_column,
	      slots = prepareSlotsHtml(response.slots_data, response.selected_date),
	      customJS = response.custom_js;
	    // 'BACK' button.
	    $('.bookly-js-back-step', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      if (!opt[params.form_id].skip_steps.extras && !opt[params.form_id].no_extras) {
	        if (opt[params.form_id].step_extras == 'before_step_time') {
	          stepExtras({
	            form_id: params.form_id
	          });
	        } else {
	          stepService({
	            form_id: params.form_id
	          });
	        }
	      } else {
	        stepService({
	          form_id: params.form_id
	        });
	      }
	    }).toggle(!opt[params.form_id].skip_steps.service || !opt[params.form_id].skip_steps.extras);
	    $('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'time'
	      });
	    });

	    // Time zone switcher.
	    $('.bookly-js-time-zone-switcher', $container).on('change', function (e) {
	      opt[params.form_id].timeZone = this.value;
	      opt[params.form_id].timeZoneOffset = undefined;
	      showSpinner();
	      requestRenderTime.cancel();
	      if (columnizerObserver) {
	        columnizerObserver.disconnect();
	      }
	      stepTime({
	        form_id: params.form_id,
	        time_zone: opt[params.form_id].timeZone
	      });
	    });
	    if (show_calendar) {
	      let date = response.current_date ? response.first_available_date ? response.first_available_date : response.current_date : response.selected_date ? response.selected_date.substring(0, 10) : $('.bookly-js-selected-date', $container).data('value');
	      loadedMonths.push(moment(date).month() + '-' + moment(date).year());
	      let _cal = new Calendar({
	        target: $('.bookly-js-slot-calendar', $container).get(0),
	        props: {
	          datePicker: BooklyL10nGlobal.datePicker,
	          date: date,
	          startDate: moment(date).toDate(),
	          limits: {
	            start: response.date_min ? new Date(response.date_min[0], response.date_min[1], response.date_min[2]) : new Date(),
	            end: response.date_max ? new Date(response.date_max[0], response.date_max[1], response.date_max[2]) : false
	          },
	          holidays: response.disabled_days,
	          loadedMonths: loadedMonths,
	          loading: false,
	          border: true,
	          layout: opt[params.form_id].datepicker_mode,
	          disabledWeekDays: response.has_slots ? [] : [0, 1, 2, 3, 4, 5, 6]
	        }
	      });
	      function calendarMonthChange(date, dir) {
	        _cal.loading = true;
	        requestRenderTime.cancel();
	        stepTime({
	          form_id: params.form_id,
	          selected_date: date,
	          dir: dir
	        });
	        showSpinner();
	      }
	      _cal.$on('change', function () {
	        if (moment(_cal.date).month() !== moment(date).month()) {
	          calendarMonthChange(_cal.date, null);
	        } else {
	          $columnizer.html(slots[_cal.date]).css('left', '0');
	          columns = 0;
	          screen_index = 0;
	          $current_screen = null;
	          initSlots();
	          $time_prev_button.hide();
	          $time_next_button.toggle($screens.length != 1);
	        }
	      });
	      _cal.$on('month-change', function (e) {
	        calendarMonthChange(_cal.year + '-' + (_cal.month < 9 ? '0' + (_cal.month + 1) : _cal.month + 1) + '-01', e.detail);
	      });
	      $columnizer.html(slots[date]);
	    } else {
	      // Insert all slots.
	      var slots_data = '';
	      $.each(slots, function (group, group_slots) {
	        slots_data += group_slots;
	      });
	      $columnizer.html(slots_data);
	    }
	    if (response.has_slots) {
	      if (error_message) {
	        _findInstanceProperty($container).call($container, '.bookly-label-error').html(error_message);
	      } else {
	        _findInstanceProperty($container).call($container, '.bookly-label-error').hide();
	      }

	      // Calculate number of slots per column.
	      slots_per_column = _parseInt$4($(window).height() / slot_height, 10);
	      if (slots_per_column < 4) {
	        slots_per_column = 4;
	      } else if (slots_per_column > 10) {
	        slots_per_column = 10;
	      }
	      var hammertime = $('.bookly-time-step', $container).hammer({
	        swipe_velocity: 0.1
	      });
	      hammertime.on('swipeleft', function () {
	        if ($time_next_button.is(':visible')) {
	          $time_next_button.trigger('click');
	        }
	      });
	      hammertime.on('swiperight', function () {
	        if ($time_prev_button.is(':visible')) {
	          $time_prev_button.trigger('click');
	        }
	      });
	      $time_next_button.on('click', function (e) {
	        $time_prev_button.show();
	        if ($screens.eq(screen_index + 1).length) {
	          $columnizer.animate({
	            left: (is_rtl ? '+' : '-') + (screen_index + 1) * $current_screen.width()
	          }, {
	            duration: 800
	          });
	          $current_screen = $screens.eq(++screen_index);
	          $columnizer_wrap.animate({
	            height: $current_screen.height()
	          }, {
	            duration: 800
	          });
	          if (screen_index + 1 === $screens.length && !has_more_slots) {
	            $time_next_button.hide();
	          }
	        } else if (has_more_slots) {
	          // Do ajax request when there are more slots.
	          var $button = $('> button:last', $columnizer);
	          if ($button.length === 0) {
	            $button = $('.bookly-column:hidden:last > button:last', $columnizer);
	            if ($button.length === 0) {
	              $button = $('.bookly-column:last > button:last', $columnizer);
	            }
	          }

	          // Render Next Time
	          var data = {
	              action: 'bookly_render_next_time',
	              form_id: params.form_id,
	              last_slot: $button.val()
	            },
	            ladda = laddaStart(this);
	          booklyAjax({
	            type: 'POST',
	            data: data
	          }).then(response => {
	            if (response.has_slots) {
	              // if there are available time
	              has_more_slots = response.has_more_slots;
	              var slots_data = '';
	              $.each(prepareSlotsHtml(response.slots_data, response.selected_date), function (group, group_slots) {
	                slots_data += group_slots;
	              });
	              var $html = $(slots_data);
	              // The first slot is always a day slot.
	              // Check if such day slot already exists (this can happen
	              // because of time zone offset) and then remove the first slot.
	              var $first_day = $html.eq(0);
	              if ($('button.bookly-day[value="' + $first_day.attr('value') + '"]', $container).length) {
	                $html = $html.not(':first');
	              }
	              $columnizer.append($html);
	              initSlots();
	              $time_next_button.trigger('click');
	            } else {
	              // no available time
	              $time_next_button.hide();
	            }
	            ladda.stop();
	          }).catch(response => {
	            $time_next_button.hide();
	            ladda.stop();
	          });
	        }
	      });
	      $time_prev_button.on('click', function () {
	        $time_next_button.show();
	        $current_screen = $screens.eq(--screen_index);
	        $columnizer.animate({
	          left: (is_rtl ? '+' : '-') + screen_index * $current_screen.width()
	        }, {
	          duration: 800
	        });
	        $columnizer_wrap.animate({
	          height: $current_screen.height()
	        }, {
	          duration: 800
	        });
	        if (screen_index === 0) {
	          $time_prev_button.hide();
	        }
	      });
	    }
	    scrollTo($container, params.form_id);
	    function showSpinner() {
	      $('.bookly-time-screen,.bookly-not-time-screen', $container).addClass('bookly-spin-overlay');
	      var opts = {
	        lines: 11,
	        // The number of lines to draw
	        length: 11,
	        // The length of each line
	        width: 4,
	        // The line thickness
	        radius: 5 // The radius of the inner circle
	      };
	      if ($screens) {
	        new Spinner(opts).spin($screens.eq(screen_index).get(0));
	      } else {
	        // Calendar not available month.
	        new Spinner(opts).spin($('.bookly-not-time-screen', $container).get(0));
	      }
	    }
	    function initSlots() {
	      var $buttons = $('> button', $columnizer),
	        slots_count = 0,
	        max_slots = 0,
	        $button,
	        $column,
	        $screen;
	      if (show_day_per_column) {
	        /**
	         * Create columns for 'Show each day in one column' mode.
	         */
	        while ($buttons.length > 0) {
	          // Create column.
	          if ($buttons.eq(0).hasClass('bookly-day')) {
	            slots_count = 1;
	            $column = $('<div class="' + column_class + '" />');
	            $button = $(_spliceInstanceProperty($buttons).call($buttons, 0, 1));
	            $button.addClass('bookly-js-first-child');
	            $column.append($button);
	          } else {
	            slots_count++;
	            $button = $(_spliceInstanceProperty($buttons).call($buttons, 0, 1));
	            // If it is last slot in the column.
	            if (!$buttons.length || $buttons.eq(0).hasClass('bookly-day')) {
	              $button.addClass('bookly-last-child');
	              $column.append($button);
	              $columnizer.append($column);
	            } else {
	              $column.append($button);
	            }
	          }
	          // Calculate max number of slots.
	          if (slots_count > max_slots) {
	            max_slots = slots_count;
	          }
	        }
	      } else {
	        /**
	         * Create columns for normal mode.
	         */
	        while (has_more_slots ? $buttons.length > slots_per_column : $buttons.length) {
	          $column = $('<div class="' + column_class + '" />');
	          max_slots = slots_per_column;
	          if (columns % columns_per_screen == 0 && !$buttons.eq(0).hasClass('bookly-day')) {
	            // If this is the first column of a screen and the first slot in this column is not day
	            // then put 1 slot less in this column because createScreens adds 1 more
	            // slot to such columns.
	            --max_slots;
	          }
	          for (var i = 0; i < max_slots; ++i) {
	            if (i + 1 == max_slots && $buttons.eq(0).hasClass('bookly-day')) {
	              // Skip the last slot if it is day.
	              break;
	            }
	            $button = $(_spliceInstanceProperty($buttons).call($buttons, 0, 1));
	            if (i == 0) {
	              $button.addClass('bookly-js-first-child');
	            } else if (i + 1 == max_slots) {
	              $button.addClass('bookly-last-child');
	            }
	            $column.append($button);
	          }
	          $columnizer.append($column);
	          ++columns;
	        }
	      }
	      /**
	       * Create screens.
	       */
	      var $columns = $('> .bookly-column', $columnizer);
	      while (has_more_slots ? $columns.length >= columns_per_screen : $columns.length) {
	        $screen = $('<div class="bookly-time-screen"/>');
	        for (var i = 0; i < columns_per_screen; ++i) {
	          $column = $(_spliceInstanceProperty($columns).call($columns, 0, 1));
	          if (i == 0) {
	            $column.addClass('bookly-js-first-column');
	            var $first_slot = _findInstanceProperty($column).call($column, '.bookly-js-first-child');
	            // In the first column the first slot is time.
	            if (!$first_slot.hasClass('bookly-day')) {
	              var group = $first_slot.data('group'),
	                $group_slot = $('button.bookly-day[value="' + group + '"]:last', $container);
	              // Copy group slot to the first column.
	              $column.prepend($group_slot.clone());
	            }
	          }
	          $screen.append($column);
	        }
	        $columnizer.append($screen);
	      }
	      $screens = $('.bookly-time-screen', $columnizer);
	      if ($current_screen === null) {
	        $current_screen = $screens.eq(0);
	      }
	      $('button.bookly-time-skip', $container).off('click').on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        if (!opt[params.form_id].no_extras && opt[params.form_id].step_extras === 'after_step_time') {
	          stepExtras({
	            form_id: params.form_id
	          });
	        } else {
	          if (!opt[params.form_id].skip_steps.cart) {
	            stepCart({
	              form_id: params.form_id,
	              add_to_cart: true,
	              from_step: 'time'
	            });
	          } else {
	            stepDetails({
	              form_id: params.form_id,
	              add_to_cart: true
	            });
	          }
	        }
	      });

	      // On click on a slot.
	      $('button.bookly-hour', $container).off('click').on('click', function (e) {
	        requestSessionSave.cancel();
	        e.stopPropagation();
	        e.preventDefault();
	        var $this = $(this),
	          data = {
	            action: 'bookly_session_save',
	            form_id: params.form_id,
	            slots: this.value
	          };
	        $this.attr({
	          'data-style': 'zoom-in',
	          'data-spinner-color': '#333',
	          'data-spinner-size': '40'
	        });
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $.globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }
	        requestSessionSave.booklyAjax({
	          type: 'POST',
	          data: data
	        }).then(response => {
	          if (!opt[params.form_id].skip_steps.extras && opt[params.form_id].step_extras == 'after_step_time' && !opt[params.form_id].no_extras) {
	            stepExtras({
	              form_id: params.form_id
	            });
	          } else if (!_repeatInstanceProperty(opt[params.form_id].skip_steps) && opt[params.form_id].recurrence_enabled) {
	            stepRepeat({
	              form_id: params.form_id
	            });
	          } else if (!opt[params.form_id].skip_steps.cart) {
	            stepCart({
	              form_id: params.form_id,
	              add_to_cart: true,
	              from_step: 'time'
	            });
	          } else {
	            stepDetails({
	              form_id: params.form_id,
	              add_to_cart: true
	            });
	          }
	        });
	      });

	      // Columnizer width & height.
	      $('.bookly-time-step', $container).width(columns_per_screen * column_width);
	      $columnizer_wrap.height($current_screen.height());
	    }
	    function observeResizeColumnizer() {
	      if ($('.bookly-time-step', $container).length > 0) {
	        let time = new Date().getTime();
	        if (time - lastObserverTime > 200) {
	          let formWidth = $columnizer_wrap.closest('.bookly-form').width();
	          if (formWidth !== lastObserverWidth) {
	            resizeColumnizer();
	            lastObserverWidth = formWidth;
	            lastObserverTime = time;
	          }
	        }
	      } else {
	        columnizerObserver.disconnect();
	      }
	    }
	    function resizeColumnizer() {
	      $columnizer.html(slots_data).css('left', '0px');
	      columns = 0;
	      screen_index = 0;
	      $current_screen = null;
	      if (column_width > 0) {
	        let formWidth = $columnizer_wrap.closest('.bookly-form').width();
	        if (show_calendar) {
	          let calendarWidth = $('.bookly-js-slot-calendar', $container).width();
	          if (formWidth > calendarWidth + column_width + 24) {
	            columns_per_screen = _parseInt$4((formWidth - calendarWidth - 24) / column_width, 10);
	          } else {
	            columns_per_screen = _parseInt$4(formWidth / column_width, 10);
	          }
	        } else {
	          columns_per_screen = _parseInt$4(formWidth / column_width, 10);
	        }
	      }
	      if (columns_per_screen > 10) {
	        columns_per_screen = 10;
	      }
	      columns_per_screen = Math.max(columns_per_screen, 1);
	      initSlots();
	      $time_prev_button.hide();
	      if (!has_more_slots && $screens.length === 1) {
	        $time_next_button.hide();
	      } else {
	        $time_next_button.show();
	      }
	    }
	    if (typeof ResizeObserver === "undefined" || typeof ResizeObserver === undefined) {
	      resizeColumnizer();
	    } else {
	      columnizerObserver = new ResizeObserver(observeResizeColumnizer);
	      columnizerObserver.observe($container.get(0));
	    }
	  }).catch(response => {
	    stepService({
	      form_id: params.form_id
	    });
	  });
	}

	/**
	 * Extras step.
	 */
	function stepExtras(params) {
	  var data = {
	      action: 'bookly_render_extras'
	    },
	    $container = opt[params.form_id].$container;
	  if (opt[params.form_id].skip_steps.service && opt[params.form_id].use_client_time_zone) {
	    // If Service step is skipped then we need to send time zone offset.
	    data.time_zone = opt[params.form_id].timeZone;
	    data.time_zone_offset = opt[params.form_id].timeZoneOffset;
	  }
	  $.extend(data, params);
	  booklyAjax({
	    data
	  }).then(response => {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    let $next_step = $('.bookly-js-next-step', $container),
	      $back_step = $('.bookly-js-back-step', $container),
	      $goto_cart = $('.bookly-js-go-to-cart', $container),
	      $extras_items = $('.bookly-js-extras-item', $container),
	      $extras_summary = $('.bookly-js-extras-summary span', $container),
	      customJS = response.custom_js,
	      $this,
	      $input,
	      format = new Format(response);
	    var extrasChanged = function ($extras_item, quantity) {
	      var $input = _findInstanceProperty($extras_item).call($extras_item, 'input'),
	        $total = _findInstanceProperty($extras_item).call($extras_item, '.bookly-js-extras-total-price'),
	        total_price = quantity * _parseFloat($extras_item.data('price'));
	      $total.text(format.price(total_price));
	      $input.val(quantity);
	      _findInstanceProperty($extras_item).call($extras_item, '.bookly-js-extras-thumb').toggleClass('bookly-extras-selected', quantity > 0);

	      // Updating summary
	      var amount = 0;
	      $extras_items.each(function (index, elem) {
	        var $this = $(this),
	          multiplier = $this.closest('.bookly-js-extras-container').data('multiplier');
	        amount += _parseFloat($this.data('price')) * _findInstanceProperty($this).call($this, 'input').val() * multiplier;
	      });
	      if (amount) {
	        $extras_summary.html(' + ' + format.price(amount));
	      } else {
	        $extras_summary.html('');
	      }
	    };
	    $extras_items.each(function (index, elem) {
	      var $this = $(this);
	      var $input = _findInstanceProperty($this).call($this, 'input');
	      $('.bookly-js-extras-thumb', $this).on('click', function () {
	        extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	      }).keypress(function (e) {
	        e.preventDefault();
	        if (e.which == 13 || e.which == 32) {
	          extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	        }
	      });
	      _findInstanceProperty($this).call($this, '.bookly-js-count-control').on('click', function () {
	        var count = _parseInt$4($input.val());
	        count = $(this).hasClass('bookly-js-extras-increment') ? Math.min($this.data('max_quantity'), count + 1) : Math.max($this.data('min_quantity'), count - 1);
	        extrasChanged($this, count);
	      });
	      setInputFilter($input.get(0), function (value) {
	        let valid = /^\d*$/.test(value) && (value === '' || _parseInt$4(value) <= $this.data('max_quantity') && _parseInt$4(value) >= $this.data('min_quantity'));
	        if (valid) {
	          extrasChanged($this, value === '' ? $this.data('min_quantity') : _parseInt$4(value));
	        }
	        return valid;
	      });
	      extrasChanged($this, $input.val());
	    });
	    $goto_cart.on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'extras'
	      });
	    });
	    $next_step.on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);

	      // Execute custom JavaScript
	      if (customJS) {
	        try {
	          $.globalEval(customJS.next_button);
	        } catch (e) {
	          // Do nothing
	        }
	      }
	      var extras = {};
	      $('.bookly-js-extras-container', $container).each(function () {
	        var $extras_container = $(this);
	        var chain_id = $extras_container.data('chain');
	        var chain_extras = {};
	        // Get checked extras for chain.
	        _findInstanceProperty($extras_container).call($extras_container, '.bookly-js-extras-item').each(function (index, elem) {
	          $this = $(this);
	          $input = _findInstanceProperty($this).call($this, 'input');
	          if ($input.val() > 0) {
	            chain_extras[$this.data('id')] = $input.val();
	          }
	        });
	        extras[chain_id] = _JSON$stringify(chain_extras);
	      });
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_session_save',
	          form_id: params.form_id,
	          extras: extras
	        }
	      }).then(response => {
	        if (opt[params.form_id].step_extras == 'before_step_time' && !opt[params.form_id].skip_steps.time) {
	          stepTime({
	            form_id: params.form_id,
	            prev_step: 'extras'
	          });
	        } else if (!_repeatInstanceProperty(opt[params.form_id].skip_steps) && opt[params.form_id].recurrence_enabled) {
	          stepRepeat({
	            form_id: params.form_id
	          });
	        } else if (!opt[params.form_id].skip_steps.cart) {
	          stepCart({
	            form_id: params.form_id,
	            add_to_cart: true,
	            from_step: 'time'
	          });
	        } else {
	          stepDetails({
	            form_id: params.form_id,
	            add_to_cart: true
	          });
	        }
	      });
	    });
	    $back_step.on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      if (opt[params.form_id].step_extras == 'after_step_time' && !opt[params.form_id].no_time) {
	        stepTime({
	          form_id: params.form_id,
	          prev_step: 'extras'
	        });
	      } else {
	        stepService({
	          form_id: params.form_id
	        });
	      }
	    });
	  });
	}
	function setInputFilter(textbox, inputFilter) {
	  var _context;
	  _forEachInstanceProperty(_context = ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"]).call(_context, function (event) {
	    textbox.addEventListener(event, function () {
	      if (inputFilter(this.value)) {
	        this.oldValue = this.value;
	        this.oldSelectionStart = this.selectionStart;
	        this.oldSelectionEnd = this.selectionEnd;
	      } else if (this.hasOwnProperty('oldValue')) {
	        this.value = this.oldValue;
	        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
	      } else {
	        this.value = '';
	      }
	    });
	  });
	}

	var es_object_values = {};

	var objectToArray;
	var hasRequiredObjectToArray;

	function requireObjectToArray () {
		if (hasRequiredObjectToArray) return objectToArray;
		hasRequiredObjectToArray = 1;
		var DESCRIPTORS = /*@__PURE__*/ requireDescriptors$1();
		var fails = /*@__PURE__*/ requireFails$1();
		var uncurryThis = /*@__PURE__*/ requireFunctionUncurryThis$1();
		var objectGetPrototypeOf = /*@__PURE__*/ requireObjectGetPrototypeOf$1();
		var objectKeys = /*@__PURE__*/ requireObjectKeys$1();
		var toIndexedObject = /*@__PURE__*/ requireToIndexedObject$1();
		var $propertyIsEnumerable = /*@__PURE__*/ requireObjectPropertyIsEnumerable$1().f;

		var propertyIsEnumerable = uncurryThis($propertyIsEnumerable);
		var push = uncurryThis([].push);

		// in some IE versions, `propertyIsEnumerable` returns incorrect result on integer keys
		// of `null` prototype objects
		var IE_BUG = DESCRIPTORS && fails(function () {
		  // eslint-disable-next-line es/no-object-create -- safe
		  var O = Object.create(null);
		  O[2] = 2;
		  return !propertyIsEnumerable(O, 2);
		});

		// `Object.{ entries, values }` methods implementation
		var createMethod = function (TO_ENTRIES) {
		  return function (it) {
		    var O = toIndexedObject(it);
		    var keys = objectKeys(O);
		    var IE_WORKAROUND = IE_BUG && objectGetPrototypeOf(O) === null;
		    var length = keys.length;
		    var i = 0;
		    var result = [];
		    var key;
		    while (length > i) {
		      key = keys[i++];
		      if (!DESCRIPTORS || (IE_WORKAROUND ? key in O : propertyIsEnumerable(O, key))) {
		        push(result, TO_ENTRIES ? [key, O[key]] : O[key]);
		      }
		    }
		    return result;
		  };
		};

		objectToArray = {
		  // `Object.entries` method
		  // https://tc39.es/ecma262/#sec-object.entries
		  entries: createMethod(true),
		  // `Object.values` method
		  // https://tc39.es/ecma262/#sec-object.values
		  values: createMethod(false)
		};
		return objectToArray;
	}

	var hasRequiredEs_object_values;

	function requireEs_object_values () {
		if (hasRequiredEs_object_values) return es_object_values;
		hasRequiredEs_object_values = 1;
		var $ = /*@__PURE__*/ require_export$1();
		var $values = /*@__PURE__*/ requireObjectToArray().values;

		// `Object.values` method
		// https://tc39.es/ecma262/#sec-object.values
		$({ target: 'Object', stat: true }, {
		  values: function values(O) {
		    return $values(O);
		  }
		});
		return es_object_values;
	}

	var values$2;
	var hasRequiredValues$2;

	function requireValues$2 () {
		if (hasRequiredValues$2) return values$2;
		hasRequiredValues$2 = 1;
		requireEs_object_values();
		var path = /*@__PURE__*/ requirePath$1();

		values$2 = path.Object.values;
		return values$2;
	}

	var values$1;
	var hasRequiredValues$1;

	function requireValues$1 () {
		if (hasRequiredValues$1) return values$1;
		hasRequiredValues$1 = 1;
		var parent = /*@__PURE__*/ requireValues$2();

		values$1 = parent;
		return values$1;
	}

	var values;
	var hasRequiredValues;

	function requireValues () {
		if (hasRequiredValues) return values;
		hasRequiredValues = 1;
		values = /*@__PURE__*/ requireValues$1();
		return values;
	}

	var valuesExports = requireValues();
	var _Object$values = /*@__PURE__*/getDefaultExportFromCjs(valuesExports);

	function get_each_context$1(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty$1(ctx).call(ctx);
	  child_ctx[11] = list[i];
	  return child_ctx;
	}

	// (31:8) {#if placeholder}
	function create_if_block_2$1(ctx) {
	  let option;
	  let t_value = /*placeholder*/ctx[3].name + "";
	  let t;
	  let option_value_value;
	  return {
	    c() {
	      option = element("option");
	      t = text(t_value);
	      option.__value = option_value_value = /*placeholder*/ctx[3].id;
	      set_input_value(option, option.__value);
	    },
	    m(target, anchor) {
	      insert(target, option, anchor);
	      append(option, t);
	    },
	    p(ctx, dirty) {
	      if (dirty & /*placeholder*/8 && t_value !== (t_value = /*placeholder*/ctx[3].name + "")) set_data(t, t_value);
	      if (dirty & /*placeholder*/8 && option_value_value !== (option_value_value = /*placeholder*/ctx[3].id)) {
	        option.__value = option_value_value;
	        set_input_value(option, option.__value);
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(option);
	      }
	    }
	  };
	}

	// (35:12) {#if !item.hidden}
	function create_if_block_1$1(ctx) {
	  let option;
	  let t_value = /*item*/ctx[11].name + "";
	  let t;
	  let option_value_value;
	  return {
	    c() {
	      option = element("option");
	      t = text(t_value);
	      option.__value = option_value_value = /*item*/ctx[11].id;
	      set_input_value(option, option.__value);
	    },
	    m(target, anchor) {
	      insert(target, option, anchor);
	      append(option, t);
	    },
	    p(ctx, dirty) {
	      if (dirty & /*items*/16 && t_value !== (t_value = /*item*/ctx[11].name + "")) set_data(t, t_value);
	      if (dirty & /*items*/16 && option_value_value !== (option_value_value = /*item*/ctx[11].id)) {
	        option.__value = option_value_value;
	        set_input_value(option, option.__value);
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(option);
	      }
	    }
	  };
	}

	// (34:8) {#each items as item}
	function create_each_block$1(ctx) {
	  let if_block_anchor;
	  let if_block = ! /*item*/ctx[11].hidden && create_if_block_1$1(ctx);
	  return {
	    c() {
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	    },
	    m(target, anchor) {
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	    },
	    p(ctx, dirty) {
	      if (! /*item*/ctx[11].hidden) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	        } else {
	          if_block = create_if_block_1$1(ctx);
	          if_block.c();
	          if_block.m(if_block_anchor.parentNode, if_block_anchor);
	        }
	      } else if (if_block) {
	        if_block.d(1);
	        if_block = null;
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(if_block_anchor);
	      }
	      if (if_block) if_block.d(detaching);
	    }
	  };
	}

	// (41:0) {#if error}
	function create_if_block$2(ctx) {
	  let div;
	  let t;
	  return {
	    c() {
	      div = element("div");
	      t = text(/*error*/ctx[5]);
	      attr(div, "class", "bookly-label-error");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, t);
	    },
	    p(ctx, dirty) {
	      if (dirty & /*error*/32) set_data(t, /*error*/ctx[5]);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	    }
	  };
	}
	function create_fragment$2(ctx) {
	  let label_1;
	  let t0;
	  let t1;
	  let div;
	  let select;
	  let if_block0_anchor;
	  let t2;
	  let if_block1_anchor;
	  let mounted;
	  let dispose;
	  let if_block0 = /*placeholder*/ctx[3] && create_if_block_2$1(ctx);
	  let each_value = ensure_array_like(/*items*/ctx[4]);
	  let each_blocks = [];
	  for (let i = 0; i < each_value.length; i += 1) {
	    each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));
	  }
	  let if_block1 = /*error*/ctx[5] && create_if_block$2(ctx);
	  return {
	    c() {
	      label_1 = element("label");
	      t0 = text(/*label*/ctx[2]);
	      t1 = space();
	      div = element("div");
	      select = element("select");
	      if (if_block0) if_block0.c();
	      if_block0_anchor = empty();
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        each_blocks[i].c();
	      }
	      t2 = space();
	      if (if_block1) if_block1.c();
	      if_block1_anchor = empty();
	      attr(label_1, "for", "bookly-rnd-" + /*id*/ctx[6]);
	      attr(select, "id", "bookly-rnd-" + /*id*/ctx[6]);
	      if (/*selected*/ctx[1] === void 0) add_render_callback(() => /*select_change_handler*/ctx[9].call(select));
	    },
	    m(target, anchor) {
	      insert(target, label_1, anchor);
	      append(label_1, t0);
	      /*label_1_binding*/
	      ctx[8](label_1);
	      insert(target, t1, anchor);
	      insert(target, div, anchor);
	      append(div, select);
	      if (if_block0) if_block0.m(select, null);
	      append(select, if_block0_anchor);
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        if (each_blocks[i]) {
	          each_blocks[i].m(select, null);
	        }
	      }
	      select_option(select, /*selected*/ctx[1], true);
	      insert(target, t2, anchor);
	      if (if_block1) if_block1.m(target, anchor);
	      insert(target, if_block1_anchor, anchor);
	      if (!mounted) {
	        dispose = [listen(select, "change", /*select_change_handler*/ctx[9]), listen(select, "change", /*onChange*/ctx[7])];
	        mounted = true;
	      }
	    },
	    p(ctx, _ref) {
	      let [dirty] = _ref;
	      if (dirty & /*label*/4) set_data(t0, /*label*/ctx[2]);
	      if (/*placeholder*/ctx[3]) {
	        if (if_block0) {
	          if_block0.p(ctx, dirty);
	        } else {
	          if_block0 = create_if_block_2$1(ctx);
	          if_block0.c();
	          if_block0.m(select, if_block0_anchor);
	        }
	      } else if (if_block0) {
	        if_block0.d(1);
	        if_block0 = null;
	      }
	      if (dirty & /*items*/16) {
	        each_value = ensure_array_like(/*items*/ctx[4]);
	        let i;
	        for (i = 0; i < each_value.length; i += 1) {
	          const child_ctx = get_each_context$1(ctx, each_value, i);
	          if (each_blocks[i]) {
	            each_blocks[i].p(child_ctx, dirty);
	          } else {
	            each_blocks[i] = create_each_block$1(child_ctx);
	            each_blocks[i].c();
	            each_blocks[i].m(select, null);
	          }
	        }
	        for (; i < each_blocks.length; i += 1) {
	          each_blocks[i].d(1);
	        }
	        each_blocks.length = each_value.length;
	      }
	      if (dirty & /*selected, items, placeholder*/26) {
	        select_option(select, /*selected*/ctx[1]);
	      }
	      if (/*error*/ctx[5]) {
	        if (if_block1) {
	          if_block1.p(ctx, dirty);
	        } else {
	          if_block1 = create_if_block$2(ctx);
	          if_block1.c();
	          if_block1.m(if_block1_anchor.parentNode, if_block1_anchor);
	        }
	      } else if (if_block1) {
	        if_block1.d(1);
	        if_block1 = null;
	      }
	    },
	    i: noop,
	    o: noop,
	    d(detaching) {
	      if (detaching) {
	        detach(label_1);
	        detach(t1);
	        detach(div);
	        detach(t2);
	        detach(if_block1_anchor);
	      }

	      /*label_1_binding*/
	      ctx[8](null);
	      if (if_block0) if_block0.d();
	      destroy_each(each_blocks, detaching);
	      if (if_block1) if_block1.d(detaching);
	      mounted = false;
	      run_all(dispose);
	    }
	  };
	}
	function compare(a, b) {
	  if (a.pos < b.pos) return -1;
	  if (a.pos > b.pos) return 1;
	  return 0;
	}
	function instance$2($$self, $$props, $$invalidate) {
	  let {
	    el = null
	  } = $$props;
	  let {
	    label = ''
	  } = $$props;
	  let {
	    placeholder = null
	  } = $$props;
	  let {
	    items = []
	  } = $$props;
	  let {
	    selected = ''
	  } = $$props;
	  let {
	    error = null
	  } = $$props;
	  let id = Math.random().toString(36).substr(2, 9);
	  const dispatch = createEventDispatcher();
	  function onChange() {
	    dispatch('change', selected);
	  }
	  function label_1_binding($$value) {
	    binding_callbacks[$$value ? 'unshift' : 'push'](() => {
	      el = $$value;
	      $$invalidate(0, el);
	    });
	  }
	  function select_change_handler() {
	    selected = select_value(this);
	    $$invalidate(1, selected);
	    $$invalidate(4, items);
	    $$invalidate(3, placeholder);
	  }
	  $$self.$$set = $$props => {
	    if ('el' in $$props) $$invalidate(0, el = $$props.el);
	    if ('label' in $$props) $$invalidate(2, label = $$props.label);
	    if ('placeholder' in $$props) $$invalidate(3, placeholder = $$props.placeholder);
	    if ('items' in $$props) $$invalidate(4, items = $$props.items);
	    if ('selected' in $$props) $$invalidate(1, selected = $$props.selected);
	    if ('error' in $$props) $$invalidate(5, error = $$props.error);
	  };
	  $$self.$$.update = () => {
	    if ($$self.$$.dirty & /*items*/16) {
	      // Sort items by position
	      _sortInstanceProperty(items).call(items, compare);
	    }
	  };
	  return [el, selected, label, placeholder, items, error, id, onChange, label_1_binding, select_change_handler];
	}
	class Select extends SvelteComponent {
	  constructor(options) {
	    super();
	    init(this, options, instance$2, create_fragment$2, safe_not_equal, {
	      el: 0,
	      label: 2,
	      placeholder: 3,
	      items: 4,
	      selected: 1,
	      error: 5
	    });
	  }
	}

	function create_if_block_14(ctx) {
	  let div;
	  let select;
	  let updating_el;
	  let current;
	  function select_el_binding(value) {
	    /*select_el_binding*/ctx[66](value);
	  }
	  let select_props = {
	    label: /*l10n*/ctx[16].location_label,
	    placeholder: /*locationPlaceholder*/ctx[30],
	    items: _Object$values(/*locations*/ctx[0]),
	    selected: /*locationId*/ctx[17],
	    error: /*locationError*/ctx[34]
	  };
	  if (/*locationEl*/ctx[35] !== void 0) {
	    select_props.el = /*locationEl*/ctx[35];
	  }
	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(() => bind(select, 'el', select_el_binding));
	  select.$on("change", /*onLocationChange*/ctx[40]);
	  return {
	    c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "location");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p(ctx, dirty) {
	      const select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].location_label;
	      if (dirty[0] & /*locationPlaceholder*/1073741824) select_changes.placeholder = /*locationPlaceholder*/ctx[30];
	      if (dirty[0] & /*locations*/1) select_changes.items = _Object$values(/*locations*/ctx[0]);
	      if (dirty[0] & /*locationId*/131072) select_changes.selected = /*locationId*/ctx[17];
	      if (dirty[1] & /*locationError*/8) select_changes.error = /*locationError*/ctx[34];
	      if (!updating_el && dirty[1] & /*locationEl*/16) {
	        updating_el = true;
	        select_changes.el = /*locationEl*/ctx[35];
	        add_flush_callback(() => updating_el = false);
	      }
	      select.$set(select_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_component(select);
	    }
	  };
	}

	// (489:4) {#if hasCategorySelect}
	function create_if_block_12(ctx) {
	  let div;
	  let select;
	  let t;
	  let show_if = /*showCategoryInfo*/ctx[4] && /*categoryId*/ctx[18] && /*categories*/ctx[1][/*categoryId*/ctx[18]].hasOwnProperty('info') && /*categories*/ctx[1][/*categoryId*/ctx[18]].info !== '';
	  let if_block_anchor;
	  let current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].category_label,
	      placeholder: /*categoryPlaceholder*/ctx[31],
	      items: _Object$values(/*categoryItems*/ctx[26]),
	      selected: /*categoryId*/ctx[18]
	    }
	  });
	  select.$on("change", /*onCategoryChange*/ctx[41]);
	  let if_block = show_if && create_if_block_13(ctx);
	  return {
	    c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "category");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p(ctx, dirty) {
	      const select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].category_label;
	      if (dirty[1] & /*categoryPlaceholder*/1) select_changes.placeholder = /*categoryPlaceholder*/ctx[31];
	      if (dirty[0] & /*categoryItems*/67108864) select_changes.items = _Object$values(/*categoryItems*/ctx[26]);
	      if (dirty[0] & /*categoryId*/262144) select_changes.selected = /*categoryId*/ctx[18];
	      select.$set(select_changes);
	      if (dirty[0] & /*showCategoryInfo, categoryId, categories*/262162) show_if = /*showCategoryInfo*/ctx[4] && /*categoryId*/ctx[18] && /*categories*/ctx[1][/*categoryId*/ctx[18]].hasOwnProperty('info') && /*categories*/ctx[1][/*categoryId*/ctx[18]].info !== '';
	      if (show_if) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	          if (dirty[0] & /*showCategoryInfo, categoryId, categories*/262162) {
	            transition_in(if_block, 1);
	          }
	        } else {
	          if_block = create_if_block_13(ctx);
	          if_block.c();
	          transition_in(if_block, 1);
	          if_block.m(if_block_anchor.parentNode, if_block_anchor);
	        }
	      } else if (if_block) {
	        group_outros();
	        transition_out(if_block, 1, 1, () => {
	          if_block = null;
	        });
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      transition_in(if_block);
	      current = true;
	    },
	    o(local) {
	      transition_out(select.$$.fragment, local);
	      transition_out(if_block);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	        detach(t);
	        detach(if_block_anchor);
	      }
	      destroy_component(select);
	      if (if_block) if_block.d(detaching);
	    }
	  };
	}

	// (499:8) {#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}
	function create_if_block_13(ctx) {
	  let div;
	  let raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "";
	  let div_transition;
	  let current;
	  return {
	    c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-sm bookly-category-info");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p(ctx, dirty) {
	      if ((!current || dirty[0] & /*categories, categoryId*/262146) && raw_value !== (raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "")) div.innerHTML = raw_value;
	    },
	    i(local) {
	      if (current) return;
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (505:4) {#if hasServiceSelect}
	function create_if_block_10(ctx) {
	  let div;
	  let select;
	  let updating_el;
	  let t;
	  let show_if = /*showServiceInfo*/ctx[5] && /*serviceId*/ctx[19] && /*services*/ctx[2][/*serviceId*/ctx[19]].hasOwnProperty('info') && /*services*/ctx[2][/*serviceId*/ctx[19]].info !== '';
	  let if_block_anchor;
	  let current;
	  function select_el_binding_1(value) {
	    /*select_el_binding_1*/ctx[67](value);
	  }
	  let select_props = {
	    label: /*l10n*/ctx[16].service_label,
	    placeholder: /*servicePlaceholder*/ctx[32],
	    items: _Object$values(/*serviceItems*/ctx[27]),
	    selected: /*serviceId*/ctx[19],
	    error: /*serviceError*/ctx[36]
	  };
	  if (/*serviceEl*/ctx[37] !== void 0) {
	    select_props.el = /*serviceEl*/ctx[37];
	  }
	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(() => bind(select, 'el', select_el_binding_1));
	  select.$on("change", /*onServiceChange*/ctx[42]);
	  let if_block = show_if && create_if_block_11(ctx);
	  return {
	    c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "service");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p(ctx, dirty) {
	      const select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].service_label;
	      if (dirty[1] & /*servicePlaceholder*/2) select_changes.placeholder = /*servicePlaceholder*/ctx[32];
	      if (dirty[0] & /*serviceItems*/134217728) select_changes.items = _Object$values(/*serviceItems*/ctx[27]);
	      if (dirty[0] & /*serviceId*/524288) select_changes.selected = /*serviceId*/ctx[19];
	      if (dirty[1] & /*serviceError*/32) select_changes.error = /*serviceError*/ctx[36];
	      if (!updating_el && dirty[1] & /*serviceEl*/64) {
	        updating_el = true;
	        select_changes.el = /*serviceEl*/ctx[37];
	        add_flush_callback(() => updating_el = false);
	      }
	      select.$set(select_changes);
	      if (dirty[0] & /*showServiceInfo, serviceId, services*/524324) show_if = /*showServiceInfo*/ctx[5] && /*serviceId*/ctx[19] && /*services*/ctx[2][/*serviceId*/ctx[19]].hasOwnProperty('info') && /*services*/ctx[2][/*serviceId*/ctx[19]].info !== '';
	      if (show_if) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	          if (dirty[0] & /*showServiceInfo, serviceId, services*/524324) {
	            transition_in(if_block, 1);
	          }
	        } else {
	          if_block = create_if_block_11(ctx);
	          if_block.c();
	          transition_in(if_block, 1);
	          if_block.m(if_block_anchor.parentNode, if_block_anchor);
	        }
	      } else if (if_block) {
	        group_outros();
	        transition_out(if_block, 1, 1, () => {
	          if_block = null;
	        });
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      transition_in(if_block);
	      current = true;
	    },
	    o(local) {
	      transition_out(select.$$.fragment, local);
	      transition_out(if_block);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	        detach(t);
	        detach(if_block_anchor);
	      }
	      destroy_component(select);
	      if (if_block) if_block.d(detaching);
	    }
	  };
	}

	// (517:8) {#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}
	function create_if_block_11(ctx) {
	  let div;
	  let raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "";
	  let div_transition;
	  let current;
	  return {
	    c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-sm bookly-service-info");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p(ctx, dirty) {
	      if ((!current || dirty[0] & /*services, serviceId*/524292) && raw_value !== (raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "")) div.innerHTML = raw_value;
	    },
	    i(local) {
	      if (current) return;
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (523:4) {#if hasStaffSelect}
	function create_if_block_8(ctx) {
	  let div;
	  let select;
	  let updating_el;
	  let t;
	  let show_if = /*showStaffInfo*/ctx[6] && /*staffId*/ctx[20] && /*staff*/ctx[3][/*staffId*/ctx[20]].hasOwnProperty('info') && /*staff*/ctx[3][/*staffId*/ctx[20]].info !== '';
	  let if_block_anchor;
	  let current;
	  function select_el_binding_2(value) {
	    /*select_el_binding_2*/ctx[68](value);
	  }
	  let select_props = {
	    label: /*l10n*/ctx[16].staff_label,
	    placeholder: /*staffPlaceholder*/ctx[33],
	    items: _Object$values(/*staffItems*/ctx[23]),
	    selected: /*staffId*/ctx[20],
	    error: /*staffError*/ctx[38]
	  };
	  if (/*staffEl*/ctx[39] !== void 0) {
	    select_props.el = /*staffEl*/ctx[39];
	  }
	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(() => bind(select, 'el', select_el_binding_2));
	  select.$on("change", /*onStaffChange*/ctx[43]);
	  let if_block = show_if && create_if_block_9(ctx);
	  return {
	    c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "staff");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p(ctx, dirty) {
	      const select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].staff_label;
	      if (dirty[1] & /*staffPlaceholder*/4) select_changes.placeholder = /*staffPlaceholder*/ctx[33];
	      if (dirty[0] & /*staffItems*/8388608) select_changes.items = _Object$values(/*staffItems*/ctx[23]);
	      if (dirty[0] & /*staffId*/1048576) select_changes.selected = /*staffId*/ctx[20];
	      if (dirty[1] & /*staffError*/128) select_changes.error = /*staffError*/ctx[38];
	      if (!updating_el && dirty[1] & /*staffEl*/256) {
	        updating_el = true;
	        select_changes.el = /*staffEl*/ctx[39];
	        add_flush_callback(() => updating_el = false);
	      }
	      select.$set(select_changes);
	      if (dirty[0] & /*showStaffInfo, staffId, staff*/1048648) show_if = /*showStaffInfo*/ctx[6] && /*staffId*/ctx[20] && /*staff*/ctx[3][/*staffId*/ctx[20]].hasOwnProperty('info') && /*staff*/ctx[3][/*staffId*/ctx[20]].info !== '';
	      if (show_if) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	          if (dirty[0] & /*showStaffInfo, staffId, staff*/1048648) {
	            transition_in(if_block, 1);
	          }
	        } else {
	          if_block = create_if_block_9(ctx);
	          if_block.c();
	          transition_in(if_block, 1);
	          if_block.m(if_block_anchor.parentNode, if_block_anchor);
	        }
	      } else if (if_block) {
	        group_outros();
	        transition_out(if_block, 1, 1, () => {
	          if_block = null;
	        });
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      transition_in(if_block);
	      current = true;
	    },
	    o(local) {
	      transition_out(select.$$.fragment, local);
	      transition_out(if_block);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	        detach(t);
	        detach(if_block_anchor);
	      }
	      destroy_component(select);
	      if (if_block) if_block.d(detaching);
	    }
	  };
	}

	// (535:8) {#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}
	function create_if_block_9(ctx) {
	  let div;
	  let raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "";
	  let div_transition;
	  let current;
	  return {
	    c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-sm bookly-staff-info");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p(ctx, dirty) {
	      if ((!current || dirty[0] & /*staff, staffId*/1048584) && raw_value !== (raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "")) div.innerHTML = raw_value;
	    },
	    i(local) {
	      if (current) return;
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (541:4) {#if hasDurationSelect}
	function create_if_block_7(ctx) {
	  let div;
	  let select;
	  let current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].duration_label,
	      items: _Object$values(/*durationItems*/ctx[24]),
	      selected: /*duration*/ctx[21]
	    }
	  });
	  select.$on("change", /*onDurationChange*/ctx[44]);
	  return {
	    c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "duration");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p(ctx, dirty) {
	      const select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].duration_label;
	      if (dirty[0] & /*durationItems*/16777216) select_changes.items = _Object$values(/*durationItems*/ctx[24]);
	      if (dirty[0] & /*duration*/2097152) select_changes.selected = /*duration*/ctx[21];
	      select.$set(select_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_component(select);
	    }
	  };
	}

	// (551:4) {#if hasNopSelect}
	function create_if_block_6(ctx) {
	  let div;
	  let select;
	  let current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].nop_label,
	      items: _Object$values(/*nopItems*/ctx[28]),
	      selected: /*nop*/ctx[22]
	    }
	  });
	  select.$on("change", /*onNopChange*/ctx[45]);
	  return {
	    c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "nop");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p(ctx, dirty) {
	      const select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].nop_label;
	      if (dirty[0] & /*nopItems*/268435456) select_changes.items = _Object$values(/*nopItems*/ctx[28]);
	      if (dirty[0] & /*nop*/4194304) select_changes.selected = /*nop*/ctx[22];
	      select.$set(select_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_component(select);
	    }
	  };
	}

	// (561:4) {#if hasQuantitySelect}
	function create_if_block_5(ctx) {
	  let div;
	  let select;
	  let current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].quantity_label,
	      items: _Object$values(/*quantityItems*/ctx[29]),
	      selected: /*quantity*/ctx[25]
	    }
	  });
	  select.$on("change", /*onQuantityChange*/ctx[46]);
	  return {
	    c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "quantity");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p(ctx, dirty) {
	      const select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].quantity_label;
	      if (dirty[0] & /*quantityItems*/536870912) select_changes.items = _Object$values(/*quantityItems*/ctx[29]);
	      if (dirty[0] & /*quantity*/33554432) select_changes.selected = /*quantity*/ctx[25];
	      select.$set(select_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      destroy_component(select);
	    }
	  };
	}

	// (571:4) {#if hasDropBtn}
	function create_if_block_3(ctx) {
	  let div1;
	  let label;
	  let t;
	  let div0;
	  let if_block = /*showDropBtn*/ctx[15] && create_if_block_4(ctx);
	  return {
	    c() {
	      div1 = element("div");
	      label = element("label");
	      t = space();
	      div0 = element("div");
	      if (if_block) if_block.c();
	      attr(div1, "class", "bookly-form-group bookly-chain-actions");
	    },
	    m(target, anchor) {
	      insert(target, div1, anchor);
	      append(div1, label);
	      append(div1, t);
	      append(div1, div0);
	      if (if_block) if_block.m(div0, null);
	    },
	    p(ctx, dirty) {
	      if (/*showDropBtn*/ctx[15]) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	        } else {
	          if_block = create_if_block_4(ctx);
	          if_block.c();
	          if_block.m(div0, null);
	        }
	      } else if (if_block) {
	        if_block.d(1);
	        if_block = null;
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div1);
	      }
	      if (if_block) if_block.d();
	    }
	  };
	}

	// (575:16) {#if showDropBtn}
	function create_if_block_4(ctx) {
	  let button;
	  let mounted;
	  let dispose;
	  return {
	    c() {
	      button = element("button");
	      button.innerHTML = `<i class="bookly-icon-sm bookly-icon-drop"></i>`;
	      attr(button, "class", "bookly-round");
	    },
	    m(target, anchor) {
	      insert(target, button, anchor);
	      if (!mounted) {
	        dispose = listen(button, "click", /*onDropBtnClick*/ctx[47]);
	        mounted = true;
	      }
	    },
	    p: noop,
	    d(detaching) {
	      if (detaching) {
	        detach(button);
	      }
	      mounted = false;
	      dispose();
	    }
	  };
	}

	// (582:0) {#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}
	function create_if_block_2(ctx) {
	  let div;
	  let raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "";
	  let div_transition;
	  let current;
	  return {
	    c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-md bookly-category-info");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p(ctx, dirty) {
	      if ((!current || dirty[0] & /*categories, categoryId*/262146) && raw_value !== (raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "")) div.innerHTML = raw_value;
	    },
	    i(local) {
	      if (current) return;
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (587:0) {#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}
	function create_if_block_1(ctx) {
	  let div;
	  let raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "";
	  let div_transition;
	  let current;
	  return {
	    c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-md bookly-service-info");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p(ctx, dirty) {
	      if ((!current || dirty[0] & /*services, serviceId*/524292) && raw_value !== (raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "")) div.innerHTML = raw_value;
	    },
	    i(local) {
	      if (current) return;
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (592:0) {#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}
	function create_if_block$1(ctx) {
	  let div;
	  let raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "";
	  let div_transition;
	  let current;
	  return {
	    c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-md bookly-staff-info");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p(ctx, dirty) {
	      if ((!current || dirty[0] & /*staff, staffId*/1048584) && raw_value !== (raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "")) div.innerHTML = raw_value;
	    },
	    i(local) {
	      if (current) return;
	      if (local) {
	        add_render_callback(() => {
	          if (!current) return;
	          if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	          div_transition.run(1);
	        });
	      }
	      current = true;
	    },
	    o(local) {
	      if (local) {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	        div_transition.run(0);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}
	function create_fragment$1(ctx) {
	  let div;
	  let t0;
	  let t1;
	  let t2;
	  let t3;
	  let t4;
	  let t5;
	  let t6;
	  let t7;
	  let show_if_2 = /*showCategoryInfo*/ctx[4] && /*categoryId*/ctx[18] && /*categories*/ctx[1][/*categoryId*/ctx[18]].hasOwnProperty('info') && /*categories*/ctx[1][/*categoryId*/ctx[18]].info !== '';
	  let t8;
	  let show_if_1 = /*showServiceInfo*/ctx[5] && /*serviceId*/ctx[19] && /*services*/ctx[2][/*serviceId*/ctx[19]].hasOwnProperty('info') && /*services*/ctx[2][/*serviceId*/ctx[19]].info !== '';
	  let t9;
	  let show_if = /*showStaffInfo*/ctx[6] && /*staffId*/ctx[20] && /*staff*/ctx[3][/*staffId*/ctx[20]].hasOwnProperty('info') && /*staff*/ctx[3][/*staffId*/ctx[20]].info !== '';
	  let if_block10_anchor;
	  let current;
	  let if_block0 = /*hasLocationSelect*/ctx[7] && create_if_block_14(ctx);
	  let if_block1 = /*hasCategorySelect*/ctx[8] && create_if_block_12(ctx);
	  let if_block2 = /*hasServiceSelect*/ctx[9] && create_if_block_10(ctx);
	  let if_block3 = /*hasStaffSelect*/ctx[10] && create_if_block_8(ctx);
	  let if_block4 = /*hasDurationSelect*/ctx[11] && create_if_block_7(ctx);
	  let if_block5 = /*hasNopSelect*/ctx[12] && create_if_block_6(ctx);
	  let if_block6 = /*hasQuantitySelect*/ctx[13] && create_if_block_5(ctx);
	  let if_block7 = /*hasDropBtn*/ctx[14] && create_if_block_3(ctx);
	  let if_block8 = show_if_2 && create_if_block_2(ctx);
	  let if_block9 = show_if_1 && create_if_block_1(ctx);
	  let if_block10 = show_if && create_if_block$1(ctx);
	  return {
	    c() {
	      div = element("div");
	      if (if_block0) if_block0.c();
	      t0 = space();
	      if (if_block1) if_block1.c();
	      t1 = space();
	      if (if_block2) if_block2.c();
	      t2 = space();
	      if (if_block3) if_block3.c();
	      t3 = space();
	      if (if_block4) if_block4.c();
	      t4 = space();
	      if (if_block5) if_block5.c();
	      t5 = space();
	      if (if_block6) if_block6.c();
	      t6 = space();
	      if (if_block7) if_block7.c();
	      t7 = space();
	      if (if_block8) if_block8.c();
	      t8 = space();
	      if (if_block9) if_block9.c();
	      t9 = space();
	      if (if_block10) if_block10.c();
	      if_block10_anchor = empty();
	      attr(div, "class", "bookly-table bookly-box");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      if (if_block0) if_block0.m(div, null);
	      append(div, t0);
	      if (if_block1) if_block1.m(div, null);
	      append(div, t1);
	      if (if_block2) if_block2.m(div, null);
	      append(div, t2);
	      if (if_block3) if_block3.m(div, null);
	      append(div, t3);
	      if (if_block4) if_block4.m(div, null);
	      append(div, t4);
	      if (if_block5) if_block5.m(div, null);
	      append(div, t5);
	      if (if_block6) if_block6.m(div, null);
	      append(div, t6);
	      if (if_block7) if_block7.m(div, null);
	      insert(target, t7, anchor);
	      if (if_block8) if_block8.m(target, anchor);
	      insert(target, t8, anchor);
	      if (if_block9) if_block9.m(target, anchor);
	      insert(target, t9, anchor);
	      if (if_block10) if_block10.m(target, anchor);
	      insert(target, if_block10_anchor, anchor);
	      current = true;
	    },
	    p(ctx, dirty) {
	      if (/*hasLocationSelect*/ctx[7]) {
	        if (if_block0) {
	          if_block0.p(ctx, dirty);
	          if (dirty[0] & /*hasLocationSelect*/128) {
	            transition_in(if_block0, 1);
	          }
	        } else {
	          if_block0 = create_if_block_14(ctx);
	          if_block0.c();
	          transition_in(if_block0, 1);
	          if_block0.m(div, t0);
	        }
	      } else if (if_block0) {
	        group_outros();
	        transition_out(if_block0, 1, 1, () => {
	          if_block0 = null;
	        });
	        check_outros();
	      }
	      if (/*hasCategorySelect*/ctx[8]) {
	        if (if_block1) {
	          if_block1.p(ctx, dirty);
	          if (dirty[0] & /*hasCategorySelect*/256) {
	            transition_in(if_block1, 1);
	          }
	        } else {
	          if_block1 = create_if_block_12(ctx);
	          if_block1.c();
	          transition_in(if_block1, 1);
	          if_block1.m(div, t1);
	        }
	      } else if (if_block1) {
	        group_outros();
	        transition_out(if_block1, 1, 1, () => {
	          if_block1 = null;
	        });
	        check_outros();
	      }
	      if (/*hasServiceSelect*/ctx[9]) {
	        if (if_block2) {
	          if_block2.p(ctx, dirty);
	          if (dirty[0] & /*hasServiceSelect*/512) {
	            transition_in(if_block2, 1);
	          }
	        } else {
	          if_block2 = create_if_block_10(ctx);
	          if_block2.c();
	          transition_in(if_block2, 1);
	          if_block2.m(div, t2);
	        }
	      } else if (if_block2) {
	        group_outros();
	        transition_out(if_block2, 1, 1, () => {
	          if_block2 = null;
	        });
	        check_outros();
	      }
	      if (/*hasStaffSelect*/ctx[10]) {
	        if (if_block3) {
	          if_block3.p(ctx, dirty);
	          if (dirty[0] & /*hasStaffSelect*/1024) {
	            transition_in(if_block3, 1);
	          }
	        } else {
	          if_block3 = create_if_block_8(ctx);
	          if_block3.c();
	          transition_in(if_block3, 1);
	          if_block3.m(div, t3);
	        }
	      } else if (if_block3) {
	        group_outros();
	        transition_out(if_block3, 1, 1, () => {
	          if_block3 = null;
	        });
	        check_outros();
	      }
	      if (/*hasDurationSelect*/ctx[11]) {
	        if (if_block4) {
	          if_block4.p(ctx, dirty);
	          if (dirty[0] & /*hasDurationSelect*/2048) {
	            transition_in(if_block4, 1);
	          }
	        } else {
	          if_block4 = create_if_block_7(ctx);
	          if_block4.c();
	          transition_in(if_block4, 1);
	          if_block4.m(div, t4);
	        }
	      } else if (if_block4) {
	        group_outros();
	        transition_out(if_block4, 1, 1, () => {
	          if_block4 = null;
	        });
	        check_outros();
	      }
	      if (/*hasNopSelect*/ctx[12]) {
	        if (if_block5) {
	          if_block5.p(ctx, dirty);
	          if (dirty[0] & /*hasNopSelect*/4096) {
	            transition_in(if_block5, 1);
	          }
	        } else {
	          if_block5 = create_if_block_6(ctx);
	          if_block5.c();
	          transition_in(if_block5, 1);
	          if_block5.m(div, t5);
	        }
	      } else if (if_block5) {
	        group_outros();
	        transition_out(if_block5, 1, 1, () => {
	          if_block5 = null;
	        });
	        check_outros();
	      }
	      if (/*hasQuantitySelect*/ctx[13]) {
	        if (if_block6) {
	          if_block6.p(ctx, dirty);
	          if (dirty[0] & /*hasQuantitySelect*/8192) {
	            transition_in(if_block6, 1);
	          }
	        } else {
	          if_block6 = create_if_block_5(ctx);
	          if_block6.c();
	          transition_in(if_block6, 1);
	          if_block6.m(div, t6);
	        }
	      } else if (if_block6) {
	        group_outros();
	        transition_out(if_block6, 1, 1, () => {
	          if_block6 = null;
	        });
	        check_outros();
	      }
	      if (/*hasDropBtn*/ctx[14]) {
	        if (if_block7) {
	          if_block7.p(ctx, dirty);
	        } else {
	          if_block7 = create_if_block_3(ctx);
	          if_block7.c();
	          if_block7.m(div, null);
	        }
	      } else if (if_block7) {
	        if_block7.d(1);
	        if_block7 = null;
	      }
	      if (dirty[0] & /*showCategoryInfo, categoryId, categories*/262162) show_if_2 = /*showCategoryInfo*/ctx[4] && /*categoryId*/ctx[18] && /*categories*/ctx[1][/*categoryId*/ctx[18]].hasOwnProperty('info') && /*categories*/ctx[1][/*categoryId*/ctx[18]].info !== '';
	      if (show_if_2) {
	        if (if_block8) {
	          if_block8.p(ctx, dirty);
	          if (dirty[0] & /*showCategoryInfo, categoryId, categories*/262162) {
	            transition_in(if_block8, 1);
	          }
	        } else {
	          if_block8 = create_if_block_2(ctx);
	          if_block8.c();
	          transition_in(if_block8, 1);
	          if_block8.m(t8.parentNode, t8);
	        }
	      } else if (if_block8) {
	        group_outros();
	        transition_out(if_block8, 1, 1, () => {
	          if_block8 = null;
	        });
	        check_outros();
	      }
	      if (dirty[0] & /*showServiceInfo, serviceId, services*/524324) show_if_1 = /*showServiceInfo*/ctx[5] && /*serviceId*/ctx[19] && /*services*/ctx[2][/*serviceId*/ctx[19]].hasOwnProperty('info') && /*services*/ctx[2][/*serviceId*/ctx[19]].info !== '';
	      if (show_if_1) {
	        if (if_block9) {
	          if_block9.p(ctx, dirty);
	          if (dirty[0] & /*showServiceInfo, serviceId, services*/524324) {
	            transition_in(if_block9, 1);
	          }
	        } else {
	          if_block9 = create_if_block_1(ctx);
	          if_block9.c();
	          transition_in(if_block9, 1);
	          if_block9.m(t9.parentNode, t9);
	        }
	      } else if (if_block9) {
	        group_outros();
	        transition_out(if_block9, 1, 1, () => {
	          if_block9 = null;
	        });
	        check_outros();
	      }
	      if (dirty[0] & /*showStaffInfo, staffId, staff*/1048648) show_if = /*showStaffInfo*/ctx[6] && /*staffId*/ctx[20] && /*staff*/ctx[3][/*staffId*/ctx[20]].hasOwnProperty('info') && /*staff*/ctx[3][/*staffId*/ctx[20]].info !== '';
	      if (show_if) {
	        if (if_block10) {
	          if_block10.p(ctx, dirty);
	          if (dirty[0] & /*showStaffInfo, staffId, staff*/1048648) {
	            transition_in(if_block10, 1);
	          }
	        } else {
	          if_block10 = create_if_block$1(ctx);
	          if_block10.c();
	          transition_in(if_block10, 1);
	          if_block10.m(if_block10_anchor.parentNode, if_block10_anchor);
	        }
	      } else if (if_block10) {
	        group_outros();
	        transition_out(if_block10, 1, 1, () => {
	          if_block10 = null;
	        });
	        check_outros();
	      }
	    },
	    i(local) {
	      if (current) return;
	      transition_in(if_block0);
	      transition_in(if_block1);
	      transition_in(if_block2);
	      transition_in(if_block3);
	      transition_in(if_block4);
	      transition_in(if_block5);
	      transition_in(if_block6);
	      transition_in(if_block8);
	      transition_in(if_block9);
	      transition_in(if_block10);
	      current = true;
	    },
	    o(local) {
	      transition_out(if_block0);
	      transition_out(if_block1);
	      transition_out(if_block2);
	      transition_out(if_block3);
	      transition_out(if_block4);
	      transition_out(if_block5);
	      transition_out(if_block6);
	      transition_out(if_block8);
	      transition_out(if_block9);
	      transition_out(if_block10);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	        detach(t7);
	        detach(t8);
	        detach(t9);
	        detach(if_block10_anchor);
	      }
	      if (if_block0) if_block0.d();
	      if (if_block1) if_block1.d();
	      if (if_block2) if_block2.d();
	      if (if_block3) if_block3.d();
	      if (if_block4) if_block4.d();
	      if (if_block5) if_block5.d();
	      if (if_block6) if_block6.d();
	      if (if_block7) if_block7.d();
	      if (if_block8) if_block8.d(detaching);
	      if (if_block9) if_block9.d(detaching);
	      if (if_block10) if_block10.d(detaching);
	    }
	  };
	}
	function instance$1($$self, $$props, $$invalidate) {
	  let {
	    item = {}
	  } = $$props;
	  let {
	    index = 0
	  } = $$props;
	  let {
	    locations = []
	  } = $$props;
	  let {
	    categories = []
	  } = $$props;
	  let {
	    services = []
	  } = $$props;
	  let {
	    staff = []
	  } = $$props;
	  let {
	    defaults = {}
	  } = $$props;
	  let {
	    required = {}
	  } = $$props;
	  let {
	    servicesPerLocation = false
	  } = $$props;
	  let {
	    staffNameWithPrice = false
	  } = $$props;
	  let {
	    collaborativeHideStaff = false
	  } = $$props;
	  let {
	    showRatings = false
	  } = $$props;
	  let {
	    showCategoryInfo = false
	  } = $$props;
	  let {
	    showServiceInfo = false
	  } = $$props;
	  let {
	    showStaffInfo = false
	  } = $$props;
	  let {
	    maxQuantity = 1
	  } = $$props;
	  let {
	    hasLocationSelect = false
	  } = $$props;
	  let {
	    hasCategorySelect = true
	  } = $$props;
	  let {
	    hasServiceSelect = true
	  } = $$props;
	  let {
	    hasStaffSelect = true
	  } = $$props;
	  let {
	    hasDurationSelect = false
	  } = $$props;
	  let {
	    hasNopSelect = false
	  } = $$props;
	  let {
	    hasQuantitySelect = false
	  } = $$props;
	  let {
	    hasDropBtn = false
	  } = $$props;
	  let {
	    showDropBtn = false
	  } = $$props;
	  let {
	    l10n = {}
	  } = $$props;
	  let {
	    date_from_element = null
	  } = $$props;
	  const dispatch = createEventDispatcher();
	  let locationId = 0;
	  let categoryId = 0;
	  let serviceId = 0;
	  let staffId = 0;
	  let duration = 1;
	  let nop = 1;
	  let quantity = 1;
	  let categoryItems;
	  let serviceItems;
	  let staffItems;
	  let durationItems;
	  let nopItems;
	  let quantityItems;
	  let locationPlaceholder;
	  let categoryPlaceholder;
	  let servicePlaceholder;
	  let staffPlaceholder;
	  let locationError, locationEl;
	  let serviceError, serviceEl;
	  let staffError, staffEl;
	  let lookupLocationId;
	  let categorySelected;
	  let maxCapacity;
	  let minCapacity;
	  let srvMaxCapacity;
	  let srvMinCapacity;

	  // Preselect values
	  tick().then(() => {
	    // Location
	    let selected = item.location_id || defaults.location_id;
	    if (selected) {
	      onLocationChange({
	        detail: selected
	      });
	    }
	  }).then(() => {
	    // Category
	    if (defaults.category_id) {
	      onCategoryChange({
	        detail: defaults.category_id
	      });
	    }
	  }).then(() => {
	    // Service
	    let selected = item.service_id || defaults.service_id;
	    if (selected) {
	      onServiceChange({
	        detail: selected
	      });
	    }
	  }).then(() => {
	    // Staff
	    let selected;
	    if (hasStaffSelect && item.staff_ids && item.staff_ids.length) {
	      selected = item.staff_ids.length > 1 ? 0 : item.staff_ids[0];
	    } else {
	      selected = defaults.staff_id;
	    }
	    if (selected) {
	      onStaffChange({
	        detail: selected
	      });
	    }
	  }).then(() => {
	    // Duration
	    if (item.units > 1) {
	      onDurationChange({
	        detail: item.units
	      });
	    }
	  }).then(() => {
	    // Nop
	    if (item.number_of_persons > 1) {
	      onNopChange({
	        detail: item.number_of_persons
	      });
	    }
	  }).then(() => {
	    // Quantity
	    if (item.quantity > 1) {
	      onQuantityChange({
	        detail: item.quantity
	      });
	    }
	  });
	  function onLocationChange(event) {
	    $$invalidate(17, locationId = event.detail);

	    // Validate value
	    if (!(locationId in locations)) {
	      $$invalidate(17, locationId = 0);
	    }
	    if (locationId != 0) {
	      $$invalidate(34, locationError = null);
	    }

	    // Update related values
	    if (locationId) {
	      let lookupLocationId = servicesPerLocation ? locationId : 0;
	      if (staffId) {
	        if (!(staffId in locations[locationId].staff)) {
	          $$invalidate(20, staffId = 0);
	        } else if (serviceId && !(lookupLocationId in staff[staffId].services[serviceId].locations)) {
	          $$invalidate(20, staffId = 0);
	        }
	      }
	      if (serviceId) {
	        let valid = false;
	        $.each(locations[locationId].staff, id => {
	          if (serviceId in staff[id].services && lookupLocationId in staff[id].services[serviceId].locations) {
	            valid = true;
	            return false;
	          }
	        });
	        if (!valid) {
	          $$invalidate(19, serviceId = 0);
	        }
	      }
	      if (categoryId) {
	        let valid = false;
	        $.each(locations[locationId].staff, id => {
	          $.each(staff[id].services, srvId => {
	            if (services[srvId].category_id === categoryId) {
	              valid = true;
	              return false;
	            }
	          });
	          if (valid) {
	            return false;
	          }
	        });
	        if (!valid) {
	          $$invalidate(18, categoryId = 0);
	        }
	      }
	    }
	  }
	  function onCategoryChange(event) {
	    $$invalidate(18, categoryId = event.detail);

	    // Validate value
	    if (!(categoryId in categoryItems)) {
	      $$invalidate(18, categoryId = 0);
	    }

	    // Update related values
	    if (categoryId) {
	      $$invalidate(61, categorySelected = true);
	      if (serviceId) {
	        if (services[serviceId].category_id !== categoryId) {
	          $$invalidate(19, serviceId = 0);
	        }
	      }
	      if (staffId) {
	        let valid = false;
	        $.each(staff[staffId].services, id => {
	          if (services[id].category_id === categoryId) {
	            valid = true;
	            return false;
	          }
	        });
	        if (!valid) {
	          $$invalidate(20, staffId = 0);
	        }
	      }
	    } else {
	      $$invalidate(61, categorySelected = false);
	    }
	  }
	  function onServiceChange(event) {
	    let dateMin = false;
	    $$invalidate(65, srvMinCapacity = false);
	    $$invalidate(64, srvMaxCapacity = false);
	    $$invalidate(19, serviceId = event.detail);

	    // Validate value
	    if (!(serviceId in serviceItems)) {
	      $$invalidate(19, serviceId = 0);
	    }

	    // Update related values
	    if (serviceId) {
	      $$invalidate(18, categoryId = services[serviceId].category_id);
	      if (staffId && !(serviceId in staff[staffId].services)) {
	        $$invalidate(20, staffId = 0);
	      }
	      if (date_from_element[0]) {
	        dateMin = services[serviceId].hasOwnProperty('min_time_prior_booking') ? services[serviceId].min_time_prior_booking : date_from_element.data('date_min');
	      }
	      $$invalidate(36, serviceError = null);
	    } else if (!categorySelected) {
	      $$invalidate(18, categoryId = 0);
	      if (date_from_element[0]) {
	        dateMin = date_from_element.data('date_min');
	      }
	    }
	    dispatch('changeMinDate', dateMin);
	  }
	  function onStaffChange(event) {
	    $$invalidate(20, staffId = event.detail);

	    // Validate value
	    if (!(staffId in staffItems)) {
	      $$invalidate(20, staffId = 0);
	    }
	    if (staffId != 0) {
	      $$invalidate(38, staffError = null);
	    }
	  }
	  function onDurationChange(event) {
	    $$invalidate(21, duration = event.detail);

	    // Validate value
	    if (!(duration in durationItems)) {
	      $$invalidate(21, duration = 1);
	    }
	  }
	  function onNopChange(event) {
	    $$invalidate(22, nop = event.detail);

	    // Validate value
	    if (!(nop in nopItems)) {
	      $$invalidate(22, nop = 1);
	    }
	  }
	  function onQuantityChange(event) {
	    $$invalidate(25, quantity = event.detail);

	    // Validate value
	    if (!(quantity in quantityItems)) {
	      $$invalidate(25, quantity = 1);
	    }
	  }
	  function onDropBtnClick() {
	    dispatch('dropItem', index);
	  }
	  function validate() {
	    let valid = true;
	    let el = null;
	    $$invalidate(38, staffError = $$invalidate(36, serviceError = $$invalidate(34, locationError = null)));
	    if (required.staff && !staffId && (!collaborativeHideStaff || !serviceId || services[serviceId].type !== 'collaborative')) {
	      valid = false;
	      $$invalidate(38, staffError = l10n.staff_error);
	      el = staffEl;
	    }
	    if (!serviceId) {
	      valid = false;
	      $$invalidate(36, serviceError = l10n.service_error);
	      el = serviceEl;
	    }
	    if (required.location && !locationId) {
	      valid = false;
	      $$invalidate(34, locationError = l10n.location_error);
	      el = locationEl;
	    }
	    return {
	      valid,
	      el
	    };
	  }
	  function getValues() {
	    return {
	      locationId,
	      categoryId,
	      serviceId,
	      staffIds: staffId ? [staffId] : _mapInstanceProperty($).call($, staffItems, item => item.id),
	      duration,
	      nop,
	      quantity
	    };
	  }
	  function select_el_binding(value) {
	    locationEl = value;
	    $$invalidate(35, locationEl);
	  }
	  function select_el_binding_1(value) {
	    serviceEl = value;
	    $$invalidate(37, serviceEl);
	  }
	  function select_el_binding_2(value) {
	    staffEl = value;
	    $$invalidate(39, staffEl);
	  }
	  $$self.$$set = $$props => {
	    if ('item' in $$props) $$invalidate(48, item = $$props.item);
	    if ('index' in $$props) $$invalidate(49, index = $$props.index);
	    if ('locations' in $$props) $$invalidate(0, locations = $$props.locations);
	    if ('categories' in $$props) $$invalidate(1, categories = $$props.categories);
	    if ('services' in $$props) $$invalidate(2, services = $$props.services);
	    if ('staff' in $$props) $$invalidate(3, staff = $$props.staff);
	    if ('defaults' in $$props) $$invalidate(50, defaults = $$props.defaults);
	    if ('required' in $$props) $$invalidate(51, required = $$props.required);
	    if ('servicesPerLocation' in $$props) $$invalidate(52, servicesPerLocation = $$props.servicesPerLocation);
	    if ('staffNameWithPrice' in $$props) $$invalidate(53, staffNameWithPrice = $$props.staffNameWithPrice);
	    if ('collaborativeHideStaff' in $$props) $$invalidate(54, collaborativeHideStaff = $$props.collaborativeHideStaff);
	    if ('showRatings' in $$props) $$invalidate(55, showRatings = $$props.showRatings);
	    if ('showCategoryInfo' in $$props) $$invalidate(4, showCategoryInfo = $$props.showCategoryInfo);
	    if ('showServiceInfo' in $$props) $$invalidate(5, showServiceInfo = $$props.showServiceInfo);
	    if ('showStaffInfo' in $$props) $$invalidate(6, showStaffInfo = $$props.showStaffInfo);
	    if ('maxQuantity' in $$props) $$invalidate(56, maxQuantity = $$props.maxQuantity);
	    if ('hasLocationSelect' in $$props) $$invalidate(7, hasLocationSelect = $$props.hasLocationSelect);
	    if ('hasCategorySelect' in $$props) $$invalidate(8, hasCategorySelect = $$props.hasCategorySelect);
	    if ('hasServiceSelect' in $$props) $$invalidate(9, hasServiceSelect = $$props.hasServiceSelect);
	    if ('hasStaffSelect' in $$props) $$invalidate(10, hasStaffSelect = $$props.hasStaffSelect);
	    if ('hasDurationSelect' in $$props) $$invalidate(11, hasDurationSelect = $$props.hasDurationSelect);
	    if ('hasNopSelect' in $$props) $$invalidate(12, hasNopSelect = $$props.hasNopSelect);
	    if ('hasQuantitySelect' in $$props) $$invalidate(13, hasQuantitySelect = $$props.hasQuantitySelect);
	    if ('hasDropBtn' in $$props) $$invalidate(14, hasDropBtn = $$props.hasDropBtn);
	    if ('showDropBtn' in $$props) $$invalidate(15, showDropBtn = $$props.showDropBtn);
	    if ('l10n' in $$props) $$invalidate(16, l10n = $$props.l10n);
	    if ('date_from_element' in $$props) $$invalidate(57, date_from_element = $$props.date_from_element);
	  };
	  $$self.$$.update = () => {
	    if ($$self.$$.dirty[0] & /*locationId, staff, locations, serviceId, categoryId, services, staffItems, categories, staffId, nop, hasNopSelect, duration, durationItems, l10n*/33493007 | $$self.$$.dirty[1] & /*servicesPerLocation, lookupLocationId, staffNameWithPrice, collaborativeHideStaff, showRatings, categorySelected, maxQuantity*/1675624448 | $$self.$$.dirty[2] & /*srvMinCapacity, srvMaxCapacity, minCapacity, maxCapacity*/15) {
	      {
	        $$invalidate(60, lookupLocationId = servicesPerLocation && locationId ? locationId : 0);
	        $$invalidate(26, categoryItems = {});
	        $$invalidate(27, serviceItems = {});
	        $$invalidate(23, staffItems = {});
	        $$invalidate(28, nopItems = {});

	        // Staff
	        $.each(staff, (id, staffMember) => {
	          if (!locationId || id in locations[locationId].staff) {
	            if (!serviceId) {
	              if (!categoryId) {
	                $$invalidate(23, staffItems[id] = $.extend({}, staffMember), staffItems);
	              } else {
	                $.each(staffMember.services, srvId => {
	                  if (services[srvId].category_id === categoryId) {
	                    $$invalidate(23, staffItems[id] = $.extend({}, staffMember), staffItems);
	                    return false;
	                  }
	                });
	              }
	            } else if (serviceId in staffMember.services) {
	              $.each(staffMember.services[serviceId].locations, (locId, locSrv) => {
	                if (lookupLocationId && lookupLocationId !== _parseInt$4(locId)) {
	                  return true;
	                }
	                $$invalidate(65, srvMinCapacity = srvMinCapacity ? Math.min(srvMinCapacity, locSrv.min_capacity) : locSrv.min_capacity);
	                $$invalidate(64, srvMaxCapacity = srvMaxCapacity ? Math.max(srvMaxCapacity, locSrv.max_capacity) : locSrv.max_capacity);
	                $$invalidate(23, staffItems[id] = $.extend({}, staffMember, {
	                  name: staffMember.name + (staffNameWithPrice && locSrv.price !== null && (lookupLocationId || !servicesPerLocation) ? ' (' + locSrv.price + ')' : ''),
	                  hidden: collaborativeHideStaff && services[serviceId].type === 'collaborative'
	                }), staffItems);
	                if (collaborativeHideStaff && services[serviceId].type === 'collaborative') {
	                  $$invalidate(20, staffId = 0);
	                }
	              });
	            }
	          }
	        });

	        // Add ratings to staff names
	        if (showRatings) {
	          $.each(staff, (id, staffMember) => {
	            if (staffMember.id in staffItems) {
	              if (serviceId) {
	                if (serviceId in staffMember.services && staffMember.services[serviceId].rating) {
	                  $$invalidate(23, staffItems[staffMember.id].name = '★' + staffMember.services[serviceId].rating + ' ' + staffItems[staffMember.id].name, staffItems);
	                }
	              } else if (staffMember.rating) {
	                $$invalidate(23, staffItems[staffMember.id].name = '★' + staffMember.rating + ' ' + staffItems[staffMember.id].name, staffItems);
	              }
	            }
	          });
	        }

	        // Category & service
	        if (!locationId) {
	          $$invalidate(26, categoryItems = categories);
	          $.each(services, (id, service) => {
	            if (!categoryId || !categorySelected || service.category_id === categoryId) {
	              if (!staffId || id in staff[staffId].services) {
	                $$invalidate(27, serviceItems[id] = service, serviceItems);
	              }
	            }
	          });
	        } else {
	          let categoryIds = [],
	            serviceIds = [];
	          if (servicesPerLocation) {
	            $.each(staff, stId => {
	              $.each(staff[stId].services, srvId => {
	                if (lookupLocationId in staff[stId].services[srvId].locations) {
	                  categoryIds.push(services[srvId].category_id);
	                  serviceIds.push(srvId);
	                }
	              });
	            });
	          } else {
	            $.each(locations[locationId].staff, stId => {
	              $.each(staff[stId].services, srvId => {
	                categoryIds.push(services[srvId].category_id);
	                serviceIds.push(srvId);
	              });
	            });
	          }
	          $.each(categories, (id, category) => {
	            if ($.inArray(_parseInt$4(id), categoryIds) > -1) {
	              $$invalidate(26, categoryItems[id] = category, categoryItems);
	            }
	          });
	          if (categoryId && $.inArray(categoryId, categoryIds) === -1) {
	            $$invalidate(18, categoryId = 0);
	            $$invalidate(61, categorySelected = false);
	          }
	          $.each(services, (id, service) => {
	            if ($.inArray(id, serviceIds) > -1) {
	              if (!categoryId || !categorySelected || service.category_id === categoryId) {
	                if (!staffId || id in staff[staffId].services) {
	                  $$invalidate(27, serviceItems[id] = service, serviceItems);
	                }
	              }
	            }
	          });
	        }

	        // Number of persons
	        $$invalidate(62, maxCapacity = serviceId ? staffId ? lookupLocationId in staff[staffId].services[serviceId].locations ? staff[staffId].services[serviceId].locations[lookupLocationId].max_capacity : 1 : srvMaxCapacity ? srvMaxCapacity : 1 : 1);
	        $$invalidate(63, minCapacity = serviceId ? staffId ? lookupLocationId in staff[staffId].services[serviceId].locations ? staff[staffId].services[serviceId].locations[lookupLocationId].min_capacity : 1 : srvMinCapacity ? srvMinCapacity : 1 : 1);
	        for (let i = minCapacity; i <= maxCapacity; ++i) {
	          $$invalidate(28, nopItems[i] = {
	            id: i,
	            name: i
	          }, nopItems);
	        }
	        if (nop > maxCapacity) {
	          $$invalidate(22, nop = maxCapacity);
	        }
	        if (nop < minCapacity || !hasNopSelect) {
	          $$invalidate(22, nop = minCapacity);
	        }

	        // Duration
	        $$invalidate(24, durationItems = {
	          1: {
	            id: 1,
	            name: '-'
	          }
	        });
	        if (serviceId) {
	          if (!staffId || servicesPerLocation && !locationId) {
	            if ('units' in services[serviceId]) {
	              $$invalidate(24, durationItems = services[serviceId].units);
	            }
	          } else {
	            let locId = locationId || 0;
	            let staffLocations = staff[staffId].services[serviceId].locations;
	            if (staffLocations) {
	              let staffLocation = locId in staffLocations ? staffLocations[locId] : staffLocations[0];
	              if ('units' in staffLocation) {
	                $$invalidate(24, durationItems = staffLocation.units);
	              }
	            }
	          }
	        }
	        if (!(duration in durationItems)) {
	          if (_Object$keys(durationItems).length > 0) {
	            $$invalidate(21, duration = _Object$values(durationItems)[0].id);
	          } else {
	            $$invalidate(21, duration = 1);
	          }
	        }

	        // Quantity
	        $$invalidate(29, quantityItems = {});
	        for (let q = 1; q <= maxQuantity; ++q) {
	          $$invalidate(29, quantityItems[q] = {
	            id: q,
	            name: q
	          }, quantityItems);
	        }

	        // Placeholders
	        $$invalidate(30, locationPlaceholder = {
	          id: 0,
	          name: l10n.location_option
	        });
	        $$invalidate(31, categoryPlaceholder = {
	          id: 0,
	          name: l10n.category_option
	        });
	        $$invalidate(32, servicePlaceholder = {
	          id: 0,
	          name: l10n.service_option
	        });
	        $$invalidate(33, staffPlaceholder = {
	          id: 0,
	          name: l10n.staff_option
	        });
	      }
	    }
	  };
	  return [locations, categories, services, staff, showCategoryInfo, showServiceInfo, showStaffInfo, hasLocationSelect, hasCategorySelect, hasServiceSelect, hasStaffSelect, hasDurationSelect, hasNopSelect, hasQuantitySelect, hasDropBtn, showDropBtn, l10n, locationId, categoryId, serviceId, staffId, duration, nop, staffItems, durationItems, quantity, categoryItems, serviceItems, nopItems, quantityItems, locationPlaceholder, categoryPlaceholder, servicePlaceholder, staffPlaceholder, locationError, locationEl, serviceError, serviceEl, staffError, staffEl, onLocationChange, onCategoryChange, onServiceChange, onStaffChange, onDurationChange, onNopChange, onQuantityChange, onDropBtnClick, item, index, defaults, required, servicesPerLocation, staffNameWithPrice, collaborativeHideStaff, showRatings, maxQuantity, date_from_element, validate, getValues, lookupLocationId, categorySelected, maxCapacity, minCapacity, srvMaxCapacity, srvMinCapacity, select_el_binding, select_el_binding_1, select_el_binding_2];
	}
	class ChainItem extends SvelteComponent {
	  constructor(options) {
	    super();
	    init(this, options, instance$1, create_fragment$1, safe_not_equal, {
	      item: 48,
	      index: 49,
	      locations: 0,
	      categories: 1,
	      services: 2,
	      staff: 3,
	      defaults: 50,
	      required: 51,
	      servicesPerLocation: 52,
	      staffNameWithPrice: 53,
	      collaborativeHideStaff: 54,
	      showRatings: 55,
	      showCategoryInfo: 4,
	      showServiceInfo: 5,
	      showStaffInfo: 6,
	      maxQuantity: 56,
	      hasLocationSelect: 7,
	      hasCategorySelect: 8,
	      hasServiceSelect: 9,
	      hasStaffSelect: 10,
	      hasDurationSelect: 11,
	      hasNopSelect: 12,
	      hasQuantitySelect: 13,
	      hasDropBtn: 14,
	      showDropBtn: 15,
	      l10n: 16,
	      date_from_element: 57,
	      validate: 58,
	      getValues: 59
	    }, null, [-1, -1, -1]);
	  }
	  get validate() {
	    return this.$$.ctx[58];
	  }
	  get getValues() {
	    return this.$$.ctx[59];
	  }
	}

	function get_each_context(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty$1(ctx).call(ctx);
	  child_ctx[11] = list[i];
	  child_ctx[12] = list;
	  child_ctx[13] = i;
	  return child_ctx;
	}

	// (33:0) {#each items as item, index (item)}
	function create_each_block(key_1, ctx) {
	  let first;
	  let chainitem;
	  let index = /*index*/ctx[13];
	  let current;
	  const chainitem_spread_levels = [/*data*/ctx[1], {
	    item: /*item*/ctx[11]
	  }, {
	    index: /*index*/ctx[13]
	  }, {
	    hasDropBtn: /*multiple*/ctx[2]
	  }, {
	    showDropBtn: /*index*/ctx[13] > 0
	  }];
	  const assign_chainitem = () => /*chainitem_binding*/ctx[9](chainitem, index);
	  const unassign_chainitem = () => /*chainitem_binding*/ctx[9](null, index);
	  let chainitem_props = {};
	  for (let i = 0; i < chainitem_spread_levels.length; i += 1) {
	    chainitem_props = assign(chainitem_props, chainitem_spread_levels[i]);
	  }
	  chainitem = new ChainItem({
	    props: chainitem_props
	  });
	  assign_chainitem();
	  chainitem.$on("dropItem", /*onDropItem*/ctx[6]);
	  chainitem.$on("changeMinDate", /*changeMinDate_handler*/ctx[10]);
	  return {
	    key: key_1,
	    first: null,
	    c() {
	      first = empty();
	      create_component(chainitem.$$.fragment);
	      this.first = first;
	    },
	    m(target, anchor) {
	      insert(target, first, anchor);
	      mount_component(chainitem, target, anchor);
	      current = true;
	    },
	    p(new_ctx, dirty) {
	      ctx = new_ctx;
	      if (index !== /*index*/ctx[13]) {
	        unassign_chainitem();
	        index = /*index*/ctx[13];
	        assign_chainitem();
	      }
	      const chainitem_changes = dirty & /*data, items, multiple*/7 ? get_spread_update(chainitem_spread_levels, [dirty & /*data*/2 && get_spread_object(/*data*/ctx[1]), dirty & /*items*/1 && {
	        item: /*item*/ctx[11]
	      }, dirty & /*items*/1 && {
	        index: /*index*/ctx[13]
	      }, dirty & /*multiple*/4 && {
	        hasDropBtn: /*multiple*/ctx[2]
	      }, dirty & /*items*/1 && {
	        showDropBtn: /*index*/ctx[13] > 0
	      }]) : {};
	      chainitem.$set(chainitem_changes);
	    },
	    i(local) {
	      if (current) return;
	      transition_in(chainitem.$$.fragment, local);
	      current = true;
	    },
	    o(local) {
	      transition_out(chainitem.$$.fragment, local);
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(first);
	      }
	      unassign_chainitem();
	      destroy_component(chainitem, detaching);
	    }
	  };
	}

	// (36:0) {#if multiple}
	function create_if_block(ctx) {
	  let div;
	  let button;
	  let span;
	  let t_value = /*data*/ctx[1].l10n.add_service + "";
	  let t;
	  let mounted;
	  let dispose;
	  return {
	    c() {
	      div = element("div");
	      button = element("button");
	      span = element("span");
	      t = text(t_value);
	      attr(span, "class", "ladda-label");
	      attr(button, "class", "bookly-btn ladda-button");
	      attr(button, "data-style", "zoom-in");
	      attr(button, "data-spinner-size", "40");
	      attr(div, "class", "bookly-box");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, button);
	      append(button, span);
	      append(span, t);
	      if (!mounted) {
	        dispose = listen(button, "click", /*onAddItem*/ctx[5]);
	        mounted = true;
	      }
	    },
	    p(ctx, dirty) {
	      if (dirty & /*data*/2 && t_value !== (t_value = /*data*/ctx[1].l10n.add_service + "")) set_data(t, t_value);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(div);
	      }
	      mounted = false;
	      dispose();
	    }
	  };
	}
	function create_fragment(ctx) {
	  let each_blocks = [];
	  let each_1_lookup = new _Map();
	  let t;
	  let if_block_anchor;
	  let current;
	  let each_value = ensure_array_like(/*items*/ctx[0]);
	  const get_key = ctx => /*item*/ctx[11];
	  for (let i = 0; i < each_value.length; i += 1) {
	    let child_ctx = get_each_context(ctx, each_value, i);
	    let key = get_key(child_ctx);
	    each_1_lookup.set(key, each_blocks[i] = create_each_block(key, child_ctx));
	  }
	  let if_block = /*multiple*/ctx[2] && create_if_block(ctx);
	  return {
	    c() {
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        each_blocks[i].c();
	      }
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	    },
	    m(target, anchor) {
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        if (each_blocks[i]) {
	          each_blocks[i].m(target, anchor);
	        }
	      }
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p(ctx, _ref) {
	      let [dirty] = _ref;
	      if (dirty & /*data, items, multiple, els, onDropItem, dispatch*/95) {
	        each_value = ensure_array_like(/*items*/ctx[0]);
	        group_outros();
	        each_blocks = update_keyed_each(each_blocks, dirty, get_key, 1, ctx, each_value, each_1_lookup, t.parentNode, outro_and_destroy_block, create_each_block, t, get_each_context);
	        check_outros();
	      }
	      if (/*multiple*/ctx[2]) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	        } else {
	          if_block = create_if_block(ctx);
	          if_block.c();
	          if_block.m(if_block_anchor.parentNode, if_block_anchor);
	        }
	      } else if (if_block) {
	        if_block.d(1);
	        if_block = null;
	      }
	    },
	    i(local) {
	      if (current) return;
	      for (let i = 0; i < each_value.length; i += 1) {
	        transition_in(each_blocks[i]);
	      }
	      current = true;
	    },
	    o(local) {
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        transition_out(each_blocks[i]);
	      }
	      current = false;
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	        detach(if_block_anchor);
	      }
	      for (let i = 0; i < each_blocks.length; i += 1) {
	        each_blocks[i].d(detaching);
	      }
	      if (if_block) if_block.d(detaching);
	    }
	  };
	}
	function instance($$self, $$props, $$invalidate) {
	  let {
	    items = []
	  } = $$props;
	  let {
	    data = {}
	  } = $$props;
	  let {
	    multiple = false
	  } = $$props;
	  let els = [];
	  const dispatch = createEventDispatcher();
	  function onAddItem() {
	    items.push({});
	    $$invalidate(0, items);
	  }
	  function onDropItem(event) {
	    _spliceInstanceProperty(items).call(items, event.detail, 1);
	    $$invalidate(0, items);
	    _spliceInstanceProperty(els).call(els, event.detail, 1);
	  }
	  function validate() {
	    var _context;
	    return _mapInstanceProperty(_context = _filterInstanceProperty$1(els).call(els, el => !!el)).call(_context, el => el.validate());
	  }
	  function getValues() {
	    var _context2;
	    return _mapInstanceProperty(_context2 = _filterInstanceProperty$1(els).call(els, el => !!el)).call(_context2, el => el.getValues());
	  }
	  function chainitem_binding($$value, index) {
	    binding_callbacks[$$value ? 'unshift' : 'push'](() => {
	      els[index] = $$value;
	      $$invalidate(3, els);
	    });
	  }
	  const changeMinDate_handler = e => dispatch('changeMinDate', e.detail);
	  $$self.$$set = $$props => {
	    if ('items' in $$props) $$invalidate(0, items = $$props.items);
	    if ('data' in $$props) $$invalidate(1, data = $$props.data);
	    if ('multiple' in $$props) $$invalidate(2, multiple = $$props.multiple);
	  };
	  return [items, data, multiple, els, dispatch, onAddItem, onDropItem, validate, getValues, chainitem_binding, changeMinDate_handler];
	}
	class Chain extends SvelteComponent {
	  constructor(options) {
	    super();
	    init(this, options, instance, create_fragment, safe_not_equal, {
	      items: 0,
	      data: 1,
	      multiple: 2,
	      validate: 7,
	      getValues: 8
	    });
	  }
	  get validate() {
	    return this.$$.ctx[7];
	  }
	  get getValues() {
	    return this.$$.ctx[8];
	  }
	}

	/**
	 * Service step.
	 */
	function stepService(params) {
	  if (opt[params.form_id].skip_steps.service) {
	    if (!opt[params.form_id].skip_steps.extras && opt[params.form_id].step_extras == 'before_step_time') {
	      stepExtras(params);
	    } else {
	      stepTime(params);
	    }
	    return;
	  }
	  var data = {
	      action: 'bookly_render_service'
	    },
	    $container = opt[params.form_id].$container;
	  if (opt[params.form_id].use_client_time_zone) {
	    data.time_zone = opt[params.form_id].timeZone;
	    data.time_zone_offset = opt[params.form_id].timeZoneOffset;
	  }
	  $.extend(data, params);
	  booklyAjax({
	    data
	  }).then(response => {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    var $chain = $('.bookly-js-chain', $container),
	      $date_from = $('.bookly-js-date-from', $container),
	      $week_days = $('.bookly-js-week-days', $container),
	      $select_time_from = $('.bookly-js-select-time-from', $container),
	      $select_time_to = $('.bookly-js-select-time-to', $container),
	      $next_step = $('.bookly-js-next-step', $container),
	      $mobile_next_step = $('.bookly-js-mobile-next-step', $container),
	      $mobile_prev_step = $('.bookly-js-mobile-prev-step', $container),
	      locations = response.locations,
	      categories = response.categories,
	      services = response.services,
	      staff = response.staff,
	      chain = response.chain,
	      required = response.required,
	      defaults = opt[params.form_id].defaults,
	      servicesPerLocation = response.services_per_location || false,
	      serviceNameWithDuration = response.service_name_with_duration,
	      staffNameWithPrice = response.staff_name_with_price,
	      collaborativeHideStaff = response.collaborative_hide_staff,
	      showRatings = response.show_ratings,
	      showCategoryInfo = response.show_category_info,
	      showServiceInfo = response.show_service_info,
	      showStaffInfo = response.show_staff_info,
	      maxQuantity = response.max_quantity || 1,
	      multiple = response.multi_service || false,
	      l10n = response.l10n,
	      customJS = response.custom_js;

	    // Set up selects.
	    if (serviceNameWithDuration) {
	      $.each(services, function (id, service) {
	        service.name = service.name + ' ( ' + service.duration + ' )';
	      });
	    }
	    let c = new Chain({
	      target: $chain.get(0),
	      props: {
	        items: chain,
	        data: {
	          locations,
	          categories,
	          services,
	          staff,
	          defaults,
	          required,
	          servicesPerLocation,
	          staffNameWithPrice,
	          collaborativeHideStaff,
	          showRatings,
	          showCategoryInfo,
	          showServiceInfo,
	          showStaffInfo,
	          maxQuantity,
	          date_from_element: $date_from,
	          hasLocationSelect: !opt[params.form_id].form_attributes.hide_locations,
	          hasCategorySelect: !opt[params.form_id].form_attributes.hide_categories,
	          hasServiceSelect: !(opt[params.form_id].form_attributes.hide_services && defaults.service_id),
	          hasStaffSelect: !opt[params.form_id].form_attributes.hide_staff_members,
	          hasDurationSelect: !opt[params.form_id].form_attributes.hide_service_duration,
	          hasNopSelect: opt[params.form_id].form_attributes.show_number_of_persons,
	          hasQuantitySelect: !opt[params.form_id].form_attributes.hide_quantity,
	          l10n
	        },
	        multiple
	      }
	    });
	    c.$on('changeMinDate', function (e) {
	      let _start_date = new Date(e.detail[0], e.detail[1], e.detail[2]);
	      _cal.limits = {
	        start: _start_date,
	        end: response.date_max ? new Date(response.date_max[0], response.date_max[1], response.date_max[2]) : false
	      };
	      if (!$date_from.data('changed') || _start_date > new Date($date_from.val())) {
	        var _context, _context2;
	        _cal.date = e.detail[0] + '-' + _padStartInstanceProperty(_context = String(e.detail[1] + 1)).call(_context, 2, '0') + '-' + _padStartInstanceProperty(_context2 = String(e.detail[2])).call(_context2, 2, '0');
	        $date_from.val(formatDate$1(_cal.date));
	      }
	    });
	    $date_from.data('date_min', response.date_min || true);
	    let _cal = new Calendar({
	      target: $('.bookly-js-datepicker-calendar', $container).get(0),
	      props: {
	        datePicker: BooklyL10nGlobal.datePicker,
	        date: $date_from.data('value'),
	        startDate: new Date($date_from.data('value')),
	        loading: false,
	        show: false,
	        border: true,
	        layout: opt[params.form_id].datepicker_mode,
	        limits: {
	          start: response.date_min ? new Date(response.date_min[0], response.date_min[1], response.date_min[2]) : new Date(),
	          end: response.date_max ? new Date(response.date_max[0], response.date_max[1], response.date_max[2]) : false
	        }
	      }
	    });
	    $date_from.val(formatDate$1($date_from.data('value')));
	    $(document).on('click', function (e) {
	      if ($(e.target).closest('.bookly-js-available-date').length === 0) {
	        _cal.show = false;
	      }
	    });
	    $date_from.on('focus', function (e) {
	      _cal.show = true;
	    });
	    _cal.$on('change', function () {
	      _cal.show = false;
	      $date_from.data('changed', true);
	      $date_from.val(formatDate$1(_cal.date));
	    });
	    $('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'service'
	      });
	    });
	    if (opt[params.form_id].form_attributes.hide_date) {
	      $('.bookly-js-available-date', $container).hide();
	    }
	    if (opt[params.form_id].form_attributes.hide_week_days) {
	      $('.bookly-js-week-days', $container).hide();
	    }
	    if (opt[params.form_id].form_attributes.hide_time_range) {
	      $('.bookly-js-time-range', $container).hide();
	    }

	    // time from
	    $select_time_from.on('change', function () {
	      var start_time = $(this).val(),
	        end_time = $select_time_to.val(),
	        $last_time_entry = $('option:last', $select_time_from);
	      $select_time_to.empty();

	      // case when we click on the not last time entry
	      if ($select_time_from[0].selectedIndex < $last_time_entry.index()) {
	        // clone and append all next "time_from" time entries to "time_to" list
	        $('option', this).each(function () {
	          if ($(this).val() > start_time) {
	            $select_time_to.append($(this).clone());
	          }
	        });
	        // case when we click on the last time entry
	      } else {
	        $select_time_to.append($last_time_entry.clone()).val($last_time_entry.val());
	      }
	      var first_value = $('option:first', $select_time_to).val();
	      $select_time_to.val(end_time >= first_value ? end_time : first_value);
	    });
	    let stepServiceValidator = function () {
	      let valid = true,
	        $scroll_to = null;
	      $(c.validate()).each(function (_, status) {
	        if (!status.valid) {
	          valid = false;
	          let $el = $(status.el);
	          if ($el.is(':visible')) {
	            $scroll_to = $el;
	            return false;
	          }
	        }
	      });
	      $date_from.removeClass('bookly-error');
	      // date validation
	      if (!$date_from.val()) {
	        valid = false;
	        $date_from.addClass('bookly-error');
	        if ($scroll_to === null) {
	          $scroll_to = $date_from;
	        }
	      }

	      // week days
	      if ($week_days.length && !$(':checked', $week_days).length) {
	        valid = false;
	        $week_days.addClass('bookly-error');
	        if ($scroll_to === null) {
	          $scroll_to = $week_days;
	        }
	      } else {
	        $week_days.removeClass('bookly-error');
	      }
	      if ($scroll_to !== null) {
	        scrollTo($scroll_to, params.form_id);
	      }
	      return valid;
	    };

	    // "Next" click
	    $next_step.on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      if (stepServiceValidator()) {
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $.globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }

	        // Prepare chain data.
	        let chain = [],
	          has_extras = 0,
	          time_requirements = 0,
	          recurrence_enabled = 1,
	          _time_requirements = {
	            'required': 2,
	            'optional': 1,
	            'off': 0
	          };
	        $.each(c.getValues(), function (_, values) {
	          let _service = services[values.serviceId];
	          chain.push({
	            location_id: values.locationId,
	            service_id: values.serviceId,
	            staff_ids: values.staffIds,
	            units: values.duration,
	            number_of_persons: values.nop,
	            quantity: values.quantity
	          });
	          time_requirements = Math.max(time_requirements, _time_requirements[_service.hasOwnProperty('time_requirements') ? _service.time_requirements : 'required']);
	          recurrence_enabled = Math.min(recurrence_enabled, _service.recurrence_enabled);
	          has_extras += _service.has_extras;
	        });

	        // Prepare days.
	        var days = [];
	        $('.bookly-js-week-days input:checked', $container).each(function () {
	          days.push(this.value);
	        });
	        booklyAjax({
	          type: 'POST',
	          data: {
	            action: 'bookly_session_save',
	            form_id: params.form_id,
	            chain: chain,
	            date_from: _cal.date,
	            days: days,
	            time_from: opt[params.form_id].form_attributes.hide_time_range ? null : $select_time_from.val(),
	            time_to: opt[params.form_id].form_attributes.hide_time_range ? null : $select_time_to.val(),
	            no_extras: has_extras == 0
	          }
	        }).then(response => {
	          opt[params.form_id].no_time = time_requirements == 0;
	          opt[params.form_id].no_extras = has_extras == 0;
	          opt[params.form_id].recurrence_enabled = recurrence_enabled == 1;
	          if (opt[params.form_id].skip_steps.extras) {
	            stepTime({
	              form_id: params.form_id
	            });
	          } else {
	            if (has_extras == 0 || opt[params.form_id].step_extras == 'after_step_time') {
	              stepTime({
	                form_id: params.form_id
	              });
	            } else {
	              stepExtras({
	                form_id: params.form_id
	              });
	            }
	          }
	        });
	      }
	    });
	    $mobile_next_step.on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      if (stepServiceValidator()) {
	        if (opt[params.form_id].skip_steps.service_part2) {
	          laddaStart(this);
	          $next_step.trigger('click');
	        } else {
	          $('.bookly-js-mobile-step-1', $container).hide();
	          $('.bookly-stepper li:eq(1)', $container).addClass('bookly-step-active');
	          $('.bookly-stepper li:eq(0)', $container).removeClass('bookly-step-active');
	          $('.bookly-js-mobile-step-2', $container).css('display', 'block');
	          scrollTo($container, params.form_id);
	        }
	      }
	      return false;
	    });
	    if (opt[params.form_id].skip_steps.service_part1) {
	      // Skip scrolling
	      // Timeout to let form set default values
	      _setTimeout(function () {
	        opt[params.form_id].scroll = false;
	        $mobile_next_step.trigger('click');
	        $('.bookly-stepper li:eq(0)', $container).addClass('bookly-step-active');
	        $('.bookly-stepper li:eq(1)', $container).removeClass('bookly-step-active');
	      }, 0);
	      $mobile_prev_step.remove();
	    } else {
	      $mobile_prev_step.on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        $('.bookly-js-mobile-step-1', $container).show();
	        $('.bookly-js-mobile-step-2', $container).hide();
	        $('.bookly-stepper li:eq(0)', $container).addClass('bookly-step-active');
	        $('.bookly-stepper li:eq(1)', $container).removeClass('bookly-step-active');
	        return false;
	      });
	    }
	  });
	}

	/**
	 * Main Bookly function.
	 *
	 * @param options
	 */
	function main (options) {
	  var _context;
	  let $container = $('#bookly-form-' + options.form_id);
	  if (!$container.length) {
	    return;
	  }
	  opt[options.form_id] = options;
	  opt[options.form_id].$container = $container;
	  opt[options.form_id].timeZone = typeof Intl === 'object' ? Intl.DateTimeFormat().resolvedOptions().timeZone : undefined;
	  opt[options.form_id].timeZoneOffset = new Date().getTimezoneOffset();
	  opt[options.form_id].skip_steps.service = options.skip_steps.service_part1 && options.skip_steps.service_part2;
	  if (!_includesInstanceProperty$1(_context = moment.locales()).call(_context, 'bookly-daterange')) {
	    let current_locale = moment.locale();
	    moment.defineLocale('bookly-daterange', {
	      months: BooklyL10n.months,
	      monthsShort: BooklyL10n.monthsShort,
	      weekdays: BooklyL10n.days,
	      weekdaysShort: BooklyL10n.daysShort
	    });
	    moment.locale(current_locale);
	  }

	  // initialize
	  if (options.status.booking == 'finished') {
	    opt[options.form_id].scroll = true;
	    stepComplete({
	      form_id: options.form_id
	    });
	  } else if (options.status.booking == 'cancelled') {
	    opt[options.form_id].scroll = true;
	    stepPayment({
	      form_id: options.form_id
	    });
	  } else {
	    opt[options.form_id].scroll = false;
	    stepService({
	      form_id: options.form_id,
	      new_chain: true
	    });
	  }
	  if (options.hasOwnProperty('facebook') && options.facebook.enabled) {
	    initFacebookLogin(options);
	  }

	  // init google places

	  if (options.hasOwnProperty('google_maps') && options.google_maps.enabled) {
	    var apiKey = options.google_maps.api_key,
	      src = 'https://maps.googleapis.com/maps/api/js?key=' + apiKey + '&libraries=places';
	    importScript(src, true);
	  }
	  if (options.hasOwnProperty('stripe') && options.stripe.enabled) {
	    importScript('https://js.stripe.com/v3/', true);
	  }
	}

	/**
	 * Init Facebook login.
	 */
	function initFacebookLogin(options) {
	  if (typeof FB !== 'undefined') {
	    FB.init({
	      appId: options.facebook.appId,
	      status: true,
	      version: 'v2.12'
	    });
	    FB.getLoginStatus(function (response) {
	      if (response.status === 'connected') {
	        options.facebook.enabled = false;
	        FB.api('/me', {
	          fields: 'id,name,first_name,last_name,email,link'
	        }, function (userInfo) {
	          booklyAjax({
	            type: 'POST',
	            data: $.extend(userInfo, {
	              action: 'bookly_pro_facebook_login',
	              form_id: options.form_id
	            })
	          });
	        });
	      } else {
	        FB.Event.subscribe('auth.statusChange', function (response) {
	          if (options.facebook.onStatusChange) {
	            options.facebook.onStatusChange(response);
	          }
	        });
	      }
	    });
	  }
	}
	function importScript(src, async, onLoad) {
	  var script = document.createElement("script");
	  script.type = "text\/javascript";
	  {
	    script.async = async;
	  }
	  if (onLoad instanceof Function) {
	    script.onload = onLoad;
	  }
	  document.head.appendChild(script);
	  script.src = src;
	}

	return main;

})(jQuery);
