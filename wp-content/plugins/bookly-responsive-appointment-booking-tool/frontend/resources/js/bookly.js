var bookly = (function ($$1b) {
	'use strict';

	function _interopDefaultLegacy (e) { return e && typeof e === 'object' && 'default' in e ? e : { 'default': e }; }

	var $__default = /*#__PURE__*/_interopDefaultLegacy($$1b);

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function getDefaultExportFromCjs (x) {
		return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
	}

	var symbol$6 = {exports: {}};

	var symbol$5 = {exports: {}};

	var check = function (it) {
	  return it && it.Math == Math && it;
	};

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global$t =
	  // eslint-disable-next-line es/no-global-this -- safe
	  check(typeof globalThis == 'object' && globalThis) ||
	  check(typeof window == 'object' && window) ||
	  // eslint-disable-next-line no-restricted-globals -- safe
	  check(typeof self == 'object' && self) ||
	  check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
	  // eslint-disable-next-line no-new-func -- fallback
	  (function () { return this; })() || Function('return this')();

	var fails$x = function (exec) {
	  try {
	    return !!exec();
	  } catch (error) {
	    return true;
	  }
	};

	var fails$w = fails$x;

	var functionBindNative = !fails$w(function () {
	  // eslint-disable-next-line es/no-function-prototype-bind -- safe
	  var test = (function () { /* empty */ }).bind();
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return typeof test != 'function' || test.hasOwnProperty('prototype');
	});

	var NATIVE_BIND$4 = functionBindNative;

	var FunctionPrototype$3 = Function.prototype;
	var apply$6 = FunctionPrototype$3.apply;
	var call$v = FunctionPrototype$3.call;

	// eslint-disable-next-line es/no-reflect -- safe
	var functionApply = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND$4 ? call$v.bind(apply$6) : function () {
	  return call$v.apply(apply$6, arguments);
	});

	var NATIVE_BIND$3 = functionBindNative;

	var FunctionPrototype$2 = Function.prototype;
	var call$u = FunctionPrototype$2.call;
	var uncurryThisWithBind = NATIVE_BIND$3 && FunctionPrototype$2.bind.bind(call$u, call$u);

	var functionUncurryThisRaw = NATIVE_BIND$3 ? uncurryThisWithBind : function (fn) {
	  return function () {
	    return call$u.apply(fn, arguments);
	  };
	};

	var uncurryThisRaw$1 = functionUncurryThisRaw;

	var toString$e = uncurryThisRaw$1({}.toString);
	var stringSlice$2 = uncurryThisRaw$1(''.slice);

	var classofRaw$2 = function (it) {
	  return stringSlice$2(toString$e(it), 8, -1);
	};

	var classofRaw$1 = classofRaw$2;
	var uncurryThisRaw = functionUncurryThisRaw;

	var functionUncurryThis = function (fn) {
	  // Nashorn bug:
	  //   https://github.com/zloirock/core-js/issues/1128
	  //   https://github.com/zloirock/core-js/issues/1130
	  if (classofRaw$1(fn) === 'Function') return uncurryThisRaw(fn);
	};

	var documentAll$2 = typeof document == 'object' && document.all;

	// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
	var IS_HTMLDDA = typeof documentAll$2 == 'undefined' && documentAll$2 !== undefined;

	var documentAll_1 = {
	  all: documentAll$2,
	  IS_HTMLDDA: IS_HTMLDDA
	};

	var $documentAll$1 = documentAll_1;

	var documentAll$1 = $documentAll$1.all;

	// `IsCallable` abstract operation
	// https://tc39.es/ecma262/#sec-iscallable
	var isCallable$m = $documentAll$1.IS_HTMLDDA ? function (argument) {
	  return typeof argument == 'function' || argument === documentAll$1;
	} : function (argument) {
	  return typeof argument == 'function';
	};

	var objectGetOwnPropertyDescriptor = {};

	var fails$v = fails$x;

	// Detect IE8's incomplete defineProperty implementation
	var descriptors = !fails$v(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
	});

	var NATIVE_BIND$2 = functionBindNative;

	var call$t = Function.prototype.call;

	var functionCall = NATIVE_BIND$2 ? call$t.bind(call$t) : function () {
	  return call$t.apply(call$t, arguments);
	};

	var objectPropertyIsEnumerable = {};

	var $propertyIsEnumerable$2 = {}.propertyIsEnumerable;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$a = Object.getOwnPropertyDescriptor;

	// Nashorn ~ JDK8 bug
	var NASHORN_BUG = getOwnPropertyDescriptor$a && !$propertyIsEnumerable$2.call({ 1: 2 }, 1);

	// `Object.prototype.propertyIsEnumerable` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
	objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
	  var descriptor = getOwnPropertyDescriptor$a(this, V);
	  return !!descriptor && descriptor.enumerable;
	} : $propertyIsEnumerable$2;

	var createPropertyDescriptor$7 = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};

	var uncurryThis$w = functionUncurryThis;
	var fails$u = fails$x;
	var classof$d = classofRaw$2;

	var $Object$5 = Object;
	var split = uncurryThis$w(''.split);

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var indexedObject = fails$u(function () {
	  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return !$Object$5('z').propertyIsEnumerable(0);
	}) ? function (it) {
	  return classof$d(it) == 'String' ? split(it, '') : $Object$5(it);
	} : $Object$5;

	// we can't use just `it == null` since of `document.all` special case
	// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
	var isNullOrUndefined$8 = function (it) {
	  return it === null || it === undefined;
	};

	var isNullOrUndefined$7 = isNullOrUndefined$8;

	var $TypeError$j = TypeError;

	// `RequireObjectCoercible` abstract operation
	// https://tc39.es/ecma262/#sec-requireobjectcoercible
	var requireObjectCoercible$7 = function (it) {
	  if (isNullOrUndefined$7(it)) throw $TypeError$j("Can't call method on " + it);
	  return it;
	};

	// toObject with fallback for non-array-like ES3 strings
	var IndexedObject$2 = indexedObject;
	var requireObjectCoercible$6 = requireObjectCoercible$7;

	var toIndexedObject$b = function (it) {
	  return IndexedObject$2(requireObjectCoercible$6(it));
	};

	var isCallable$l = isCallable$m;
	var $documentAll = documentAll_1;

	var documentAll = $documentAll.all;

	var isObject$m = $documentAll.IS_HTMLDDA ? function (it) {
	  return typeof it == 'object' ? it !== null : isCallable$l(it) || it === documentAll;
	} : function (it) {
	  return typeof it == 'object' ? it !== null : isCallable$l(it);
	};

	var path$s = {};

	var path$r = path$s;
	var global$s = global$t;
	var isCallable$k = isCallable$m;

	var aFunction = function (variable) {
	  return isCallable$k(variable) ? variable : undefined;
	};

	var getBuiltIn$h = function (namespace, method) {
	  return arguments.length < 2 ? aFunction(path$r[namespace]) || aFunction(global$s[namespace])
	    : path$r[namespace] && path$r[namespace][method] || global$s[namespace] && global$s[namespace][method];
	};

	var uncurryThis$v = functionUncurryThis;

	var objectIsPrototypeOf = uncurryThis$v({}.isPrototypeOf);

	var getBuiltIn$g = getBuiltIn$h;

	var engineUserAgent = getBuiltIn$g('navigator', 'userAgent') || '';

	var global$r = global$t;
	var userAgent$6 = engineUserAgent;

	var process$3 = global$r.process;
	var Deno$1 = global$r.Deno;
	var versions = process$3 && process$3.versions || Deno$1 && Deno$1.version;
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
	if (!version && userAgent$6) {
	  match = userAgent$6.match(/Edge\/(\d+)/);
	  if (!match || match[1] >= 74) {
	    match = userAgent$6.match(/Chrome\/(\d+)/);
	    if (match) version = +match[1];
	  }
	}

	var engineV8Version = version;

	/* eslint-disable es/no-symbol -- required for testing */

	var V8_VERSION$3 = engineV8Version;
	var fails$t = fails$x;

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
	var symbolConstructorDetection = !!Object.getOwnPropertySymbols && !fails$t(function () {
	  var symbol = Symbol();
	  // Chrome 38 Symbol has incorrect toString conversion
	  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
	  return !String(symbol) || !(Object(symbol) instanceof Symbol) ||
	    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
	    !Symbol.sham && V8_VERSION$3 && V8_VERSION$3 < 41;
	});

	/* eslint-disable es/no-symbol -- required for testing */

	var NATIVE_SYMBOL$5 = symbolConstructorDetection;

	var useSymbolAsUid = NATIVE_SYMBOL$5
	  && !Symbol.sham
	  && typeof Symbol.iterator == 'symbol';

	var getBuiltIn$f = getBuiltIn$h;
	var isCallable$j = isCallable$m;
	var isPrototypeOf$l = objectIsPrototypeOf;
	var USE_SYMBOL_AS_UID$1 = useSymbolAsUid;

	var $Object$4 = Object;

	var isSymbol$4 = USE_SYMBOL_AS_UID$1 ? function (it) {
	  return typeof it == 'symbol';
	} : function (it) {
	  var $Symbol = getBuiltIn$f('Symbol');
	  return isCallable$j($Symbol) && isPrototypeOf$l($Symbol.prototype, $Object$4(it));
	};

	var $String$3 = String;

	var tryToString$6 = function (argument) {
	  try {
	    return $String$3(argument);
	  } catch (error) {
	    return 'Object';
	  }
	};

	var isCallable$i = isCallable$m;
	var tryToString$5 = tryToString$6;

	var $TypeError$i = TypeError;

	// `Assert: IsCallable(argument) is true`
	var aCallable$n = function (argument) {
	  if (isCallable$i(argument)) return argument;
	  throw $TypeError$i(tryToString$5(argument) + ' is not a function');
	};

	var aCallable$m = aCallable$n;
	var isNullOrUndefined$6 = isNullOrUndefined$8;

	// `GetMethod` abstract operation
	// https://tc39.es/ecma262/#sec-getmethod
	var getMethod$3 = function (V, P) {
	  var func = V[P];
	  return isNullOrUndefined$6(func) ? undefined : aCallable$m(func);
	};

	var call$s = functionCall;
	var isCallable$h = isCallable$m;
	var isObject$l = isObject$m;

	var $TypeError$h = TypeError;

	// `OrdinaryToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-ordinarytoprimitive
	var ordinaryToPrimitive$1 = function (input, pref) {
	  var fn, val;
	  if (pref === 'string' && isCallable$h(fn = input.toString) && !isObject$l(val = call$s(fn, input))) return val;
	  if (isCallable$h(fn = input.valueOf) && !isObject$l(val = call$s(fn, input))) return val;
	  if (pref !== 'string' && isCallable$h(fn = input.toString) && !isObject$l(val = call$s(fn, input))) return val;
	  throw $TypeError$h("Can't convert object to primitive value");
	};

	var shared$6 = {exports: {}};

	var isPure = true;

	var global$q = global$t;

	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var defineProperty$f = Object.defineProperty;

	var defineGlobalProperty$1 = function (key, value) {
	  try {
	    defineProperty$f(global$q, key, { value: value, configurable: true, writable: true });
	  } catch (error) {
	    global$q[key] = value;
	  } return value;
	};

	var global$p = global$t;
	var defineGlobalProperty = defineGlobalProperty$1;

	var SHARED = '__core-js_shared__';
	var store$3 = global$p[SHARED] || defineGlobalProperty(SHARED, {});

	var sharedStore = store$3;

	var store$2 = sharedStore;

	(shared$6.exports = function (key, value) {
	  return store$2[key] || (store$2[key] = value !== undefined ? value : {});
	})('versions', []).push({
	  version: '3.26.0',
	  mode: 'pure' ,
	  copyright: '© 2014-2022 Denis Pushkarev (zloirock.ru)',
	  license: 'https://github.com/zloirock/core-js/blob/v3.26.0/LICENSE',
	  source: 'https://github.com/zloirock/core-js'
	});

	var requireObjectCoercible$5 = requireObjectCoercible$7;

	var $Object$3 = Object;

	// `ToObject` abstract operation
	// https://tc39.es/ecma262/#sec-toobject
	var toObject$c = function (argument) {
	  return $Object$3(requireObjectCoercible$5(argument));
	};

	var uncurryThis$u = functionUncurryThis;
	var toObject$b = toObject$c;

	var hasOwnProperty = uncurryThis$u({}.hasOwnProperty);

	// `HasOwnProperty` abstract operation
	// https://tc39.es/ecma262/#sec-hasownproperty
	// eslint-disable-next-line es/no-object-hasown -- safe
	var hasOwnProperty_1 = Object.hasOwn || function hasOwn(it, key) {
	  return hasOwnProperty(toObject$b(it), key);
	};

	var uncurryThis$t = functionUncurryThis;

	var id$2 = 0;
	var postfix = Math.random();
	var toString$d = uncurryThis$t(1.0.toString);

	var uid$4 = function (key) {
	  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString$d(++id$2 + postfix, 36);
	};

	var global$o = global$t;
	var shared$5 = shared$6.exports;
	var hasOwn$j = hasOwnProperty_1;
	var uid$3 = uid$4;
	var NATIVE_SYMBOL$4 = symbolConstructorDetection;
	var USE_SYMBOL_AS_UID = useSymbolAsUid;

	var WellKnownSymbolsStore$1 = shared$5('wks');
	var Symbol$3 = global$o.Symbol;
	var symbolFor = Symbol$3 && Symbol$3['for'];
	var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol$3 : Symbol$3 && Symbol$3.withoutSetter || uid$3;

	var wellKnownSymbol$o = function (name) {
	  if (!hasOwn$j(WellKnownSymbolsStore$1, name) || !(NATIVE_SYMBOL$4 || typeof WellKnownSymbolsStore$1[name] == 'string')) {
	    var description = 'Symbol.' + name;
	    if (NATIVE_SYMBOL$4 && hasOwn$j(Symbol$3, name)) {
	      WellKnownSymbolsStore$1[name] = Symbol$3[name];
	    } else if (USE_SYMBOL_AS_UID && symbolFor) {
	      WellKnownSymbolsStore$1[name] = symbolFor(description);
	    } else {
	      WellKnownSymbolsStore$1[name] = createWellKnownSymbol(description);
	    }
	  } return WellKnownSymbolsStore$1[name];
	};

	var call$r = functionCall;
	var isObject$k = isObject$m;
	var isSymbol$3 = isSymbol$4;
	var getMethod$2 = getMethod$3;
	var ordinaryToPrimitive = ordinaryToPrimitive$1;
	var wellKnownSymbol$n = wellKnownSymbol$o;

	var $TypeError$g = TypeError;
	var TO_PRIMITIVE = wellKnownSymbol$n('toPrimitive');

	// `ToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-toprimitive
	var toPrimitive$1 = function (input, pref) {
	  if (!isObject$k(input) || isSymbol$3(input)) return input;
	  var exoticToPrim = getMethod$2(input, TO_PRIMITIVE);
	  var result;
	  if (exoticToPrim) {
	    if (pref === undefined) pref = 'default';
	    result = call$r(exoticToPrim, input, pref);
	    if (!isObject$k(result) || isSymbol$3(result)) return result;
	    throw $TypeError$g("Can't convert object to primitive value");
	  }
	  if (pref === undefined) pref = 'number';
	  return ordinaryToPrimitive(input, pref);
	};

	var toPrimitive = toPrimitive$1;
	var isSymbol$2 = isSymbol$4;

	// `ToPropertyKey` abstract operation
	// https://tc39.es/ecma262/#sec-topropertykey
	var toPropertyKey$4 = function (argument) {
	  var key = toPrimitive(argument, 'string');
	  return isSymbol$2(key) ? key : key + '';
	};

	var global$n = global$t;
	var isObject$j = isObject$m;

	var document$3 = global$n.document;
	// typeof document.createElement is 'object' in old IE
	var EXISTS$1 = isObject$j(document$3) && isObject$j(document$3.createElement);

	var documentCreateElement$1 = function (it) {
	  return EXISTS$1 ? document$3.createElement(it) : {};
	};

	var DESCRIPTORS$i = descriptors;
	var fails$s = fails$x;
	var createElement$1 = documentCreateElement$1;

	// Thanks to IE8 for its funny defineProperty
	var ie8DomDefine = !DESCRIPTORS$i && !fails$s(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty(createElement$1('div'), 'a', {
	    get: function () { return 7; }
	  }).a != 7;
	});

	var DESCRIPTORS$h = descriptors;
	var call$q = functionCall;
	var propertyIsEnumerableModule$2 = objectPropertyIsEnumerable;
	var createPropertyDescriptor$6 = createPropertyDescriptor$7;
	var toIndexedObject$a = toIndexedObject$b;
	var toPropertyKey$3 = toPropertyKey$4;
	var hasOwn$i = hasOwnProperty_1;
	var IE8_DOM_DEFINE$1 = ie8DomDefine;

	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor$2 = Object.getOwnPropertyDescriptor;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
	objectGetOwnPropertyDescriptor.f = DESCRIPTORS$h ? $getOwnPropertyDescriptor$2 : function getOwnPropertyDescriptor(O, P) {
	  O = toIndexedObject$a(O);
	  P = toPropertyKey$3(P);
	  if (IE8_DOM_DEFINE$1) try {
	    return $getOwnPropertyDescriptor$2(O, P);
	  } catch (error) { /* empty */ }
	  if (hasOwn$i(O, P)) return createPropertyDescriptor$6(!call$q(propertyIsEnumerableModule$2.f, O, P), O[P]);
	};

	var fails$r = fails$x;
	var isCallable$g = isCallable$m;

	var replacement = /#|\.prototype\./;

	var isForced$2 = function (feature, detection) {
	  var value = data[normalize(feature)];
	  return value == POLYFILL ? true
	    : value == NATIVE ? false
	    : isCallable$g(detection) ? fails$r(detection)
	    : !!detection;
	};

	var normalize = isForced$2.normalize = function (string) {
	  return String(string).replace(replacement, '.').toLowerCase();
	};

	var data = isForced$2.data = {};
	var NATIVE = isForced$2.NATIVE = 'N';
	var POLYFILL = isForced$2.POLYFILL = 'P';

	var isForced_1 = isForced$2;

	var uncurryThis$s = functionUncurryThis;
	var aCallable$l = aCallable$n;
	var NATIVE_BIND$1 = functionBindNative;

	var bind$q = uncurryThis$s(uncurryThis$s.bind);

	// optional / simple context binding
	var functionBindContext = function (fn, that) {
	  aCallable$l(fn);
	  return that === undefined ? fn : NATIVE_BIND$1 ? bind$q(fn, that) : function (/* ...args */) {
	    return fn.apply(that, arguments);
	  };
	};

	var objectDefineProperty = {};

	var DESCRIPTORS$g = descriptors;
	var fails$q = fails$x;

	// V8 ~ Chrome 36-
	// https://bugs.chromium.org/p/v8/issues/detail?id=3334
	var v8PrototypeDefineBug = DESCRIPTORS$g && fails$q(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty(function () { /* empty */ }, 'prototype', {
	    value: 42,
	    writable: false
	  }).prototype != 42;
	});

	var isObject$i = isObject$m;

	var $String$2 = String;
	var $TypeError$f = TypeError;

	// `Assert: Type(argument) is Object`
	var anObject$u = function (argument) {
	  if (isObject$i(argument)) return argument;
	  throw $TypeError$f($String$2(argument) + ' is not an object');
	};

	var DESCRIPTORS$f = descriptors;
	var IE8_DOM_DEFINE = ie8DomDefine;
	var V8_PROTOTYPE_DEFINE_BUG$1 = v8PrototypeDefineBug;
	var anObject$t = anObject$u;
	var toPropertyKey$2 = toPropertyKey$4;

	var $TypeError$e = TypeError;
	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var $defineProperty$1 = Object.defineProperty;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;
	var ENUMERABLE = 'enumerable';
	var CONFIGURABLE$1 = 'configurable';
	var WRITABLE = 'writable';

	// `Object.defineProperty` method
	// https://tc39.es/ecma262/#sec-object.defineproperty
	objectDefineProperty.f = DESCRIPTORS$f ? V8_PROTOTYPE_DEFINE_BUG$1 ? function defineProperty(O, P, Attributes) {
	  anObject$t(O);
	  P = toPropertyKey$2(P);
	  anObject$t(Attributes);
	  if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
	    var current = $getOwnPropertyDescriptor$1(O, P);
	    if (current && current[WRITABLE]) {
	      O[P] = Attributes.value;
	      Attributes = {
	        configurable: CONFIGURABLE$1 in Attributes ? Attributes[CONFIGURABLE$1] : current[CONFIGURABLE$1],
	        enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
	        writable: false
	      };
	    }
	  } return $defineProperty$1(O, P, Attributes);
	} : $defineProperty$1 : function defineProperty(O, P, Attributes) {
	  anObject$t(O);
	  P = toPropertyKey$2(P);
	  anObject$t(Attributes);
	  if (IE8_DOM_DEFINE) try {
	    return $defineProperty$1(O, P, Attributes);
	  } catch (error) { /* empty */ }
	  if ('get' in Attributes || 'set' in Attributes) throw $TypeError$e('Accessors not supported');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};

	var DESCRIPTORS$e = descriptors;
	var definePropertyModule$5 = objectDefineProperty;
	var createPropertyDescriptor$5 = createPropertyDescriptor$7;

	var createNonEnumerableProperty$8 = DESCRIPTORS$e ? function (object, key, value) {
	  return definePropertyModule$5.f(object, key, createPropertyDescriptor$5(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};

	var global$m = global$t;
	var apply$5 = functionApply;
	var uncurryThis$r = functionUncurryThis;
	var isCallable$f = isCallable$m;
	var getOwnPropertyDescriptor$9 = objectGetOwnPropertyDescriptor.f;
	var isForced$1 = isForced_1;
	var path$q = path$s;
	var bind$p = functionBindContext;
	var createNonEnumerableProperty$7 = createNonEnumerableProperty$8;
	var hasOwn$h = hasOwnProperty_1;

	var wrapConstructor = function (NativeConstructor) {
	  var Wrapper = function (a, b, c) {
	    if (this instanceof Wrapper) {
	      switch (arguments.length) {
	        case 0: return new NativeConstructor();
	        case 1: return new NativeConstructor(a);
	        case 2: return new NativeConstructor(a, b);
	      } return new NativeConstructor(a, b, c);
	    } return apply$5(NativeConstructor, this, arguments);
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
	var _export = function (options, source) {
	  var TARGET = options.target;
	  var GLOBAL = options.global;
	  var STATIC = options.stat;
	  var PROTO = options.proto;

	  var nativeSource = GLOBAL ? global$m : STATIC ? global$m[TARGET] : (global$m[TARGET] || {}).prototype;

	  var target = GLOBAL ? path$q : path$q[TARGET] || createNonEnumerableProperty$7(path$q, TARGET, {})[TARGET];
	  var targetPrototype = target.prototype;

	  var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
	  var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

	  for (key in source) {
	    FORCED = isForced$1(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
	    // contains in native
	    USE_NATIVE = !FORCED && nativeSource && hasOwn$h(nativeSource, key);

	    targetProperty = target[key];

	    if (USE_NATIVE) if (options.dontCallGetSet) {
	      descriptor = getOwnPropertyDescriptor$9(nativeSource, key);
	      nativeProperty = descriptor && descriptor.value;
	    } else nativeProperty = nativeSource[key];

	    // export native or implementation
	    sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

	    if (USE_NATIVE && typeof targetProperty == typeof sourceProperty) continue;

	    // bind timers to global for call from export context
	    if (options.bind && USE_NATIVE) resultProperty = bind$p(sourceProperty, global$m);
	    // wrap global constructors for prevent changs in this version
	    else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor(sourceProperty);
	    // make static versions for prototype methods
	    else if (PROTO && isCallable$f(sourceProperty)) resultProperty = uncurryThis$r(sourceProperty);
	    // default case
	    else resultProperty = sourceProperty;

	    // add a flag to not completely full polyfills
	    if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
	      createNonEnumerableProperty$7(resultProperty, 'sham', true);
	    }

	    createNonEnumerableProperty$7(target, key, resultProperty);

	    if (PROTO) {
	      VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
	      if (!hasOwn$h(path$q, VIRTUAL_PROTOTYPE)) {
	        createNonEnumerableProperty$7(path$q, VIRTUAL_PROTOTYPE, {});
	      }
	      // export virtual prototype methods
	      createNonEnumerableProperty$7(path$q[VIRTUAL_PROTOTYPE], key, sourceProperty);
	      // export real prototype methods
	      if (options.real && targetPrototype && !targetPrototype[key]) {
	        createNonEnumerableProperty$7(targetPrototype, key, sourceProperty);
	      }
	    }
	  }
	};

	var classof$c = classofRaw$2;

	// `IsArray` abstract operation
	// https://tc39.es/ecma262/#sec-isarray
	// eslint-disable-next-line es/no-array-isarray -- safe
	var isArray$e = Array.isArray || function isArray(argument) {
	  return classof$c(argument) == 'Array';
	};

	var ceil = Math.ceil;
	var floor$1 = Math.floor;

	// `Math.trunc` method
	// https://tc39.es/ecma262/#sec-math.trunc
	// eslint-disable-next-line es/no-math-trunc -- safe
	var mathTrunc = Math.trunc || function trunc(x) {
	  var n = +x;
	  return (n > 0 ? floor$1 : ceil)(n);
	};

	var trunc = mathTrunc;

	// `ToIntegerOrInfinity` abstract operation
	// https://tc39.es/ecma262/#sec-tointegerorinfinity
	var toIntegerOrInfinity$5 = function (argument) {
	  var number = +argument;
	  // eslint-disable-next-line no-self-compare -- NaN check
	  return number !== number || number === 0 ? 0 : trunc(number);
	};

	var toIntegerOrInfinity$4 = toIntegerOrInfinity$5;

	var min$3 = Math.min;

	// `ToLength` abstract operation
	// https://tc39.es/ecma262/#sec-tolength
	var toLength$2 = function (argument) {
	  return argument > 0 ? min$3(toIntegerOrInfinity$4(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
	};

	var toLength$1 = toLength$2;

	// `LengthOfArrayLike` abstract operation
	// https://tc39.es/ecma262/#sec-lengthofarraylike
	var lengthOfArrayLike$a = function (obj) {
	  return toLength$1(obj.length);
	};

	var $TypeError$d = TypeError;
	var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; // 2 ** 53 - 1 == 9007199254740991

	var doesNotExceedSafeInteger$2 = function (it) {
	  if (it > MAX_SAFE_INTEGER) throw $TypeError$d('Maximum allowed index exceeded');
	  return it;
	};

	var toPropertyKey$1 = toPropertyKey$4;
	var definePropertyModule$4 = objectDefineProperty;
	var createPropertyDescriptor$4 = createPropertyDescriptor$7;

	var createProperty$6 = function (object, key, value) {
	  var propertyKey = toPropertyKey$1(key);
	  if (propertyKey in object) definePropertyModule$4.f(object, propertyKey, createPropertyDescriptor$4(0, value));
	  else object[propertyKey] = value;
	};

	var wellKnownSymbol$m = wellKnownSymbol$o;

	var TO_STRING_TAG$4 = wellKnownSymbol$m('toStringTag');
	var test$2 = {};

	test$2[TO_STRING_TAG$4] = 'z';

	var toStringTagSupport = String(test$2) === '[object z]';

	var TO_STRING_TAG_SUPPORT$2 = toStringTagSupport;
	var isCallable$e = isCallable$m;
	var classofRaw = classofRaw$2;
	var wellKnownSymbol$l = wellKnownSymbol$o;

	var TO_STRING_TAG$3 = wellKnownSymbol$l('toStringTag');
	var $Object$2 = Object;

	// ES3 wrong here
	var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) == 'Arguments';

	// fallback for IE11 Script Access Denied error
	var tryGet = function (it, key) {
	  try {
	    return it[key];
	  } catch (error) { /* empty */ }
	};

	// getting tag from ES6+ `Object.prototype.toString`
	var classof$b = TO_STRING_TAG_SUPPORT$2 ? classofRaw : function (it) {
	  var O, tag, result;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    // @@toStringTag case
	    : typeof (tag = tryGet(O = $Object$2(it), TO_STRING_TAG$3)) == 'string' ? tag
	    // builtinTag case
	    : CORRECT_ARGUMENTS ? classofRaw(O)
	    // ES3 arguments fallback
	    : (result = classofRaw(O)) == 'Object' && isCallable$e(O.callee) ? 'Arguments' : result;
	};

	var uncurryThis$q = functionUncurryThis;
	var isCallable$d = isCallable$m;
	var store$1 = sharedStore;

	var functionToString = uncurryThis$q(Function.toString);

	// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
	if (!isCallable$d(store$1.inspectSource)) {
	  store$1.inspectSource = function (it) {
	    return functionToString(it);
	  };
	}

	var inspectSource$2 = store$1.inspectSource;

	var uncurryThis$p = functionUncurryThis;
	var fails$p = fails$x;
	var isCallable$c = isCallable$m;
	var classof$a = classof$b;
	var getBuiltIn$e = getBuiltIn$h;
	var inspectSource$1 = inspectSource$2;

	var noop$1 = function () { /* empty */ };
	var empty$1 = [];
	var construct$8 = getBuiltIn$e('Reflect', 'construct');
	var constructorRegExp = /^\s*(?:class|function)\b/;
	var exec$2 = uncurryThis$p(constructorRegExp.exec);
	var INCORRECT_TO_STRING = !constructorRegExp.exec(noop$1);

	var isConstructorModern = function isConstructor(argument) {
	  if (!isCallable$c(argument)) return false;
	  try {
	    construct$8(noop$1, empty$1, argument);
	    return true;
	  } catch (error) {
	    return false;
	  }
	};

	var isConstructorLegacy = function isConstructor(argument) {
	  if (!isCallable$c(argument)) return false;
	  switch (classof$a(argument)) {
	    case 'AsyncFunction':
	    case 'GeneratorFunction':
	    case 'AsyncGeneratorFunction': return false;
	  }
	  try {
	    // we can't check .prototype since constructors produced by .bind haven't it
	    // `Function#toString` throws on some built-it function in some legacy engines
	    // (for example, `DOMQuad` and similar in FF41-)
	    return INCORRECT_TO_STRING || !!exec$2(constructorRegExp, inspectSource$1(argument));
	  } catch (error) {
	    return true;
	  }
	};

	isConstructorLegacy.sham = true;

	// `IsConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-isconstructor
	var isConstructor$4 = !construct$8 || fails$p(function () {
	  var called;
	  return isConstructorModern(isConstructorModern.call)
	    || !isConstructorModern(Object)
	    || !isConstructorModern(function () { called = true; })
	    || called;
	}) ? isConstructorLegacy : isConstructorModern;

	var isArray$d = isArray$e;
	var isConstructor$3 = isConstructor$4;
	var isObject$h = isObject$m;
	var wellKnownSymbol$k = wellKnownSymbol$o;

	var SPECIES$5 = wellKnownSymbol$k('species');
	var $Array$3 = Array;

	// a part of `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesConstructor$1 = function (originalArray) {
	  var C;
	  if (isArray$d(originalArray)) {
	    C = originalArray.constructor;
	    // cross-realm fallback
	    if (isConstructor$3(C) && (C === $Array$3 || isArray$d(C.prototype))) C = undefined;
	    else if (isObject$h(C)) {
	      C = C[SPECIES$5];
	      if (C === null) C = undefined;
	    }
	  } return C === undefined ? $Array$3 : C;
	};

	var arraySpeciesConstructor = arraySpeciesConstructor$1;

	// `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesCreate$3 = function (originalArray, length) {
	  return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
	};

	var fails$o = fails$x;
	var wellKnownSymbol$j = wellKnownSymbol$o;
	var V8_VERSION$2 = engineV8Version;

	var SPECIES$4 = wellKnownSymbol$j('species');

	var arrayMethodHasSpeciesSupport$5 = function (METHOD_NAME) {
	  // We can't use this feature detection in V8 since it causes
	  // deoptimization and serious performance degradation
	  // https://github.com/zloirock/core-js/issues/677
	  return V8_VERSION$2 >= 51 || !fails$o(function () {
	    var array = [];
	    var constructor = array.constructor = {};
	    constructor[SPECIES$4] = function () {
	      return { foo: 1 };
	    };
	    return array[METHOD_NAME](Boolean).foo !== 1;
	  });
	};

	var $$1a = _export;
	var fails$n = fails$x;
	var isArray$c = isArray$e;
	var isObject$g = isObject$m;
	var toObject$a = toObject$c;
	var lengthOfArrayLike$9 = lengthOfArrayLike$a;
	var doesNotExceedSafeInteger$1 = doesNotExceedSafeInteger$2;
	var createProperty$5 = createProperty$6;
	var arraySpeciesCreate$2 = arraySpeciesCreate$3;
	var arrayMethodHasSpeciesSupport$4 = arrayMethodHasSpeciesSupport$5;
	var wellKnownSymbol$i = wellKnownSymbol$o;
	var V8_VERSION$1 = engineV8Version;

	var IS_CONCAT_SPREADABLE = wellKnownSymbol$i('isConcatSpreadable');

	// We can't use this feature detection in V8 since it causes
	// deoptimization and serious performance degradation
	// https://github.com/zloirock/core-js/issues/679
	var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION$1 >= 51 || !fails$n(function () {
	  var array = [];
	  array[IS_CONCAT_SPREADABLE] = false;
	  return array.concat()[0] !== array;
	});

	var SPECIES_SUPPORT = arrayMethodHasSpeciesSupport$4('concat');

	var isConcatSpreadable = function (O) {
	  if (!isObject$g(O)) return false;
	  var spreadable = O[IS_CONCAT_SPREADABLE];
	  return spreadable !== undefined ? !!spreadable : isArray$c(O);
	};

	var FORCED$6 = !IS_CONCAT_SPREADABLE_SUPPORT || !SPECIES_SUPPORT;

	// `Array.prototype.concat` method
	// https://tc39.es/ecma262/#sec-array.prototype.concat
	// with adding support of @@isConcatSpreadable and @@species
	$$1a({ target: 'Array', proto: true, arity: 1, forced: FORCED$6 }, {
	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  concat: function concat(arg) {
	    var O = toObject$a(this);
	    var A = arraySpeciesCreate$2(O, 0);
	    var n = 0;
	    var i, k, length, len, E;
	    for (i = -1, length = arguments.length; i < length; i++) {
	      E = i === -1 ? O : arguments[i];
	      if (isConcatSpreadable(E)) {
	        len = lengthOfArrayLike$9(E);
	        doesNotExceedSafeInteger$1(n + len);
	        for (k = 0; k < len; k++, n++) if (k in E) createProperty$5(A, n, E[k]);
	      } else {
	        doesNotExceedSafeInteger$1(n + 1);
	        createProperty$5(A, n++, E);
	      }
	    }
	    A.length = n;
	    return A;
	  }
	});

	var classof$9 = classof$b;

	var $String$1 = String;

	var toString$c = function (argument) {
	  if (classof$9(argument) === 'Symbol') throw TypeError('Cannot convert a Symbol value to a string');
	  return $String$1(argument);
	};

	var objectDefineProperties = {};

	var toIntegerOrInfinity$3 = toIntegerOrInfinity$5;

	var max$3 = Math.max;
	var min$2 = Math.min;

	// Helper for a popular repeating case of the spec:
	// Let integer be ? ToInteger(index).
	// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
	var toAbsoluteIndex$5 = function (index, length) {
	  var integer = toIntegerOrInfinity$3(index);
	  return integer < 0 ? max$3(integer + length, 0) : min$2(integer, length);
	};

	var toIndexedObject$9 = toIndexedObject$b;
	var toAbsoluteIndex$4 = toAbsoluteIndex$5;
	var lengthOfArrayLike$8 = lengthOfArrayLike$a;

	// `Array.prototype.{ indexOf, includes }` methods implementation
	var createMethod$4 = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIndexedObject$9($this);
	    var length = lengthOfArrayLike$8(O);
	    var index = toAbsoluteIndex$4(fromIndex, length);
	    var value;
	    // Array#includes uses SameValueZero equality algorithm
	    // eslint-disable-next-line no-self-compare -- NaN check
	    if (IS_INCLUDES && el != el) while (length > index) {
	      value = O[index++];
	      // eslint-disable-next-line no-self-compare -- NaN check
	      if (value != value) return true;
	    // Array#indexOf ignores holes, Array#includes - not
	    } else for (;length > index; index++) {
	      if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
	    } return !IS_INCLUDES && -1;
	  };
	};

	var arrayIncludes = {
	  // `Array.prototype.includes` method
	  // https://tc39.es/ecma262/#sec-array.prototype.includes
	  includes: createMethod$4(true),
	  // `Array.prototype.indexOf` method
	  // https://tc39.es/ecma262/#sec-array.prototype.indexof
	  indexOf: createMethod$4(false)
	};

	var hiddenKeys$6 = {};

	var uncurryThis$o = functionUncurryThis;
	var hasOwn$g = hasOwnProperty_1;
	var toIndexedObject$8 = toIndexedObject$b;
	var indexOf$8 = arrayIncludes.indexOf;
	var hiddenKeys$5 = hiddenKeys$6;

	var push$8 = uncurryThis$o([].push);

	var objectKeysInternal = function (object, names) {
	  var O = toIndexedObject$8(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) !hasOwn$g(hiddenKeys$5, key) && hasOwn$g(O, key) && push$8(result, key);
	  // Don't enum bug & hidden keys
	  while (names.length > i) if (hasOwn$g(O, key = names[i++])) {
	    ~indexOf$8(result, key) || push$8(result, key);
	  }
	  return result;
	};

	// IE8- don't enum bug keys
	var enumBugKeys$3 = [
	  'constructor',
	  'hasOwnProperty',
	  'isPrototypeOf',
	  'propertyIsEnumerable',
	  'toLocaleString',
	  'toString',
	  'valueOf'
	];

	var internalObjectKeys$1 = objectKeysInternal;
	var enumBugKeys$2 = enumBugKeys$3;

	// `Object.keys` method
	// https://tc39.es/ecma262/#sec-object.keys
	// eslint-disable-next-line es/no-object-keys -- safe
	var objectKeys$4 = Object.keys || function keys(O) {
	  return internalObjectKeys$1(O, enumBugKeys$2);
	};

	var DESCRIPTORS$d = descriptors;
	var V8_PROTOTYPE_DEFINE_BUG = v8PrototypeDefineBug;
	var definePropertyModule$3 = objectDefineProperty;
	var anObject$s = anObject$u;
	var toIndexedObject$7 = toIndexedObject$b;
	var objectKeys$3 = objectKeys$4;

	// `Object.defineProperties` method
	// https://tc39.es/ecma262/#sec-object.defineproperties
	// eslint-disable-next-line es/no-object-defineproperties -- safe
	objectDefineProperties.f = DESCRIPTORS$d && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject$s(O);
	  var props = toIndexedObject$7(Properties);
	  var keys = objectKeys$3(Properties);
	  var length = keys.length;
	  var index = 0;
	  var key;
	  while (length > index) definePropertyModule$3.f(O, key = keys[index++], props[key]);
	  return O;
	};

	var getBuiltIn$d = getBuiltIn$h;

	var html$2 = getBuiltIn$d('document', 'documentElement');

	var shared$4 = shared$6.exports;
	var uid$2 = uid$4;

	var keys$3 = shared$4('keys');

	var sharedKey$4 = function (key) {
	  return keys$3[key] || (keys$3[key] = uid$2(key));
	};

	/* global ActiveXObject -- old IE, WSH */

	var anObject$r = anObject$u;
	var definePropertiesModule$1 = objectDefineProperties;
	var enumBugKeys$1 = enumBugKeys$3;
	var hiddenKeys$4 = hiddenKeys$6;
	var html$1 = html$2;
	var documentCreateElement = documentCreateElement$1;
	var sharedKey$3 = sharedKey$4;

	var GT = '>';
	var LT = '<';
	var PROTOTYPE$1 = 'prototype';
	var SCRIPT = 'script';
	var IE_PROTO$1 = sharedKey$3('IE_PROTO');

	var EmptyConstructor = function () { /* empty */ };

	var scriptTag = function (content) {
	  return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
	};

	// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
	var NullProtoObjectViaActiveX = function (activeXDocument) {
	  activeXDocument.write(scriptTag(''));
	  activeXDocument.close();
	  var temp = activeXDocument.parentWindow.Object;
	  activeXDocument = null; // avoid memory leak
	  return temp;
	};

	// Create object with fake `null` prototype: use iframe Object with cleared prototype
	var NullProtoObjectViaIFrame = function () {
	  // Thrash, waste and sodomy: IE GC bug
	  var iframe = documentCreateElement('iframe');
	  var JS = 'java' + SCRIPT + ':';
	  var iframeDocument;
	  iframe.style.display = 'none';
	  html$1.appendChild(iframe);
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
	  var length = enumBugKeys$1.length;
	  while (length--) delete NullProtoObject[PROTOTYPE$1][enumBugKeys$1[length]];
	  return NullProtoObject();
	};

	hiddenKeys$4[IE_PROTO$1] = true;

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	// eslint-disable-next-line es/no-object-create -- safe
	var objectCreate = Object.create || function create(O, Properties) {
	  var result;
	  if (O !== null) {
	    EmptyConstructor[PROTOTYPE$1] = anObject$r(O);
	    result = new EmptyConstructor();
	    EmptyConstructor[PROTOTYPE$1] = null;
	    // add "__proto__" for Object.getPrototypeOf polyfill
	    result[IE_PROTO$1] = O;
	  } else result = NullProtoObject();
	  return Properties === undefined ? result : definePropertiesModule$1.f(result, Properties);
	};

	var objectGetOwnPropertyNames = {};

	var internalObjectKeys = objectKeysInternal;
	var enumBugKeys = enumBugKeys$3;

	var hiddenKeys$3 = enumBugKeys.concat('length', 'prototype');

	// `Object.getOwnPropertyNames` method
	// https://tc39.es/ecma262/#sec-object.getownpropertynames
	// eslint-disable-next-line es/no-object-getownpropertynames -- safe
	objectGetOwnPropertyNames.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
	  return internalObjectKeys(O, hiddenKeys$3);
	};

	var objectGetOwnPropertyNamesExternal = {};

	var toAbsoluteIndex$3 = toAbsoluteIndex$5;
	var lengthOfArrayLike$7 = lengthOfArrayLike$a;
	var createProperty$4 = createProperty$6;

	var $Array$2 = Array;
	var max$2 = Math.max;

	var arraySliceSimple = function (O, start, end) {
	  var length = lengthOfArrayLike$7(O);
	  var k = toAbsoluteIndex$3(start, length);
	  var fin = toAbsoluteIndex$3(end === undefined ? length : end, length);
	  var result = $Array$2(max$2(fin - k, 0));
	  for (var n = 0; k < fin; k++, n++) createProperty$4(result, n, O[k]);
	  result.length = n;
	  return result;
	};

	/* eslint-disable es/no-object-getownpropertynames -- safe */

	var classof$8 = classofRaw$2;
	var toIndexedObject$6 = toIndexedObject$b;
	var $getOwnPropertyNames$1 = objectGetOwnPropertyNames.f;
	var arraySlice$7 = arraySliceSimple;

	var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
	  ? Object.getOwnPropertyNames(window) : [];

	var getWindowNames = function (it) {
	  try {
	    return $getOwnPropertyNames$1(it);
	  } catch (error) {
	    return arraySlice$7(windowNames);
	  }
	};

	// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
	objectGetOwnPropertyNamesExternal.f = function getOwnPropertyNames(it) {
	  return windowNames && classof$8(it) == 'Window'
	    ? getWindowNames(it)
	    : $getOwnPropertyNames$1(toIndexedObject$6(it));
	};

	var objectGetOwnPropertySymbols = {};

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
	objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;

	var createNonEnumerableProperty$6 = createNonEnumerableProperty$8;

	var defineBuiltIn$6 = function (target, key, value, options) {
	  if (options && options.enumerable) target[key] = value;
	  else createNonEnumerableProperty$6(target, key, value);
	  return target;
	};

	var wellKnownSymbolWrapped = {};

	var wellKnownSymbol$h = wellKnownSymbol$o;

	wellKnownSymbolWrapped.f = wellKnownSymbol$h;

	var path$p = path$s;
	var hasOwn$f = hasOwnProperty_1;
	var wrappedWellKnownSymbolModule$1 = wellKnownSymbolWrapped;
	var defineProperty$e = objectDefineProperty.f;

	var wellKnownSymbolDefine = function (NAME) {
	  var Symbol = path$p.Symbol || (path$p.Symbol = {});
	  if (!hasOwn$f(Symbol, NAME)) defineProperty$e(Symbol, NAME, {
	    value: wrappedWellKnownSymbolModule$1.f(NAME)
	  });
	};

	var call$p = functionCall;
	var getBuiltIn$c = getBuiltIn$h;
	var wellKnownSymbol$g = wellKnownSymbol$o;
	var defineBuiltIn$5 = defineBuiltIn$6;

	var symbolDefineToPrimitive = function () {
	  var Symbol = getBuiltIn$c('Symbol');
	  var SymbolPrototype = Symbol && Symbol.prototype;
	  var valueOf = SymbolPrototype && SymbolPrototype.valueOf;
	  var TO_PRIMITIVE = wellKnownSymbol$g('toPrimitive');

	  if (SymbolPrototype && !SymbolPrototype[TO_PRIMITIVE]) {
	    // `Symbol.prototype[@@toPrimitive]` method
	    // https://tc39.es/ecma262/#sec-symbol.prototype-@@toprimitive
	    // eslint-disable-next-line no-unused-vars -- required for .length
	    defineBuiltIn$5(SymbolPrototype, TO_PRIMITIVE, function (hint) {
	      return call$p(valueOf, this);
	    }, { arity: 1 });
	  }
	};

	var TO_STRING_TAG_SUPPORT$1 = toStringTagSupport;
	var classof$7 = classof$b;

	// `Object.prototype.toString` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.tostring
	var objectToString = TO_STRING_TAG_SUPPORT$1 ? {}.toString : function toString() {
	  return '[object ' + classof$7(this) + ']';
	};

	var TO_STRING_TAG_SUPPORT = toStringTagSupport;
	var defineProperty$d = objectDefineProperty.f;
	var createNonEnumerableProperty$5 = createNonEnumerableProperty$8;
	var hasOwn$e = hasOwnProperty_1;
	var toString$b = objectToString;
	var wellKnownSymbol$f = wellKnownSymbol$o;

	var TO_STRING_TAG$2 = wellKnownSymbol$f('toStringTag');

	var setToStringTag$7 = function (it, TAG, STATIC, SET_METHOD) {
	  if (it) {
	    var target = STATIC ? it : it.prototype;
	    if (!hasOwn$e(target, TO_STRING_TAG$2)) {
	      defineProperty$d(target, TO_STRING_TAG$2, { configurable: true, value: TAG });
	    }
	    if (SET_METHOD && !TO_STRING_TAG_SUPPORT) {
	      createNonEnumerableProperty$5(target, 'toString', toString$b);
	    }
	  }
	};

	var global$l = global$t;
	var isCallable$b = isCallable$m;

	var WeakMap$1 = global$l.WeakMap;

	var weakMapBasicDetection = isCallable$b(WeakMap$1) && /native code/.test(String(WeakMap$1));

	var NATIVE_WEAK_MAP$1 = weakMapBasicDetection;
	var global$k = global$t;
	var isObject$f = isObject$m;
	var createNonEnumerableProperty$4 = createNonEnumerableProperty$8;
	var hasOwn$d = hasOwnProperty_1;
	var shared$3 = sharedStore;
	var sharedKey$2 = sharedKey$4;
	var hiddenKeys$2 = hiddenKeys$6;

	var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
	var TypeError$3 = global$k.TypeError;
	var WeakMap = global$k.WeakMap;
	var set$4, get$7, has;

	var enforce = function (it) {
	  return has(it) ? get$7(it) : set$4(it, {});
	};

	var getterFor = function (TYPE) {
	  return function (it) {
	    var state;
	    if (!isObject$f(it) || (state = get$7(it)).type !== TYPE) {
	      throw TypeError$3('Incompatible receiver, ' + TYPE + ' required');
	    } return state;
	  };
	};

	if (NATIVE_WEAK_MAP$1 || shared$3.state) {
	  var store = shared$3.state || (shared$3.state = new WeakMap());
	  /* eslint-disable no-self-assign -- prototype methods protection */
	  store.get = store.get;
	  store.has = store.has;
	  store.set = store.set;
	  /* eslint-enable no-self-assign -- prototype methods protection */
	  set$4 = function (it, metadata) {
	    if (store.has(it)) throw TypeError$3(OBJECT_ALREADY_INITIALIZED);
	    metadata.facade = it;
	    store.set(it, metadata);
	    return metadata;
	  };
	  get$7 = function (it) {
	    return store.get(it) || {};
	  };
	  has = function (it) {
	    return store.has(it);
	  };
	} else {
	  var STATE = sharedKey$2('state');
	  hiddenKeys$2[STATE] = true;
	  set$4 = function (it, metadata) {
	    if (hasOwn$d(it, STATE)) throw TypeError$3(OBJECT_ALREADY_INITIALIZED);
	    metadata.facade = it;
	    createNonEnumerableProperty$4(it, STATE, metadata);
	    return metadata;
	  };
	  get$7 = function (it) {
	    return hasOwn$d(it, STATE) ? it[STATE] : {};
	  };
	  has = function (it) {
	    return hasOwn$d(it, STATE);
	  };
	}

	var internalState = {
	  set: set$4,
	  get: get$7,
	  has: has,
	  enforce: enforce,
	  getterFor: getterFor
	};

	var bind$o = functionBindContext;
	var uncurryThis$n = functionUncurryThis;
	var IndexedObject$1 = indexedObject;
	var toObject$9 = toObject$c;
	var lengthOfArrayLike$6 = lengthOfArrayLike$a;
	var arraySpeciesCreate$1 = arraySpeciesCreate$3;

	var push$7 = uncurryThis$n([].push);

	// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
	var createMethod$3 = function (TYPE) {
	  var IS_MAP = TYPE == 1;
	  var IS_FILTER = TYPE == 2;
	  var IS_SOME = TYPE == 3;
	  var IS_EVERY = TYPE == 4;
	  var IS_FIND_INDEX = TYPE == 6;
	  var IS_FILTER_REJECT = TYPE == 7;
	  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
	  return function ($this, callbackfn, that, specificCreate) {
	    var O = toObject$9($this);
	    var self = IndexedObject$1(O);
	    var boundFunction = bind$o(callbackfn, that);
	    var length = lengthOfArrayLike$6(self);
	    var index = 0;
	    var create = specificCreate || arraySpeciesCreate$1;
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
	          case 2: push$7(target, value);      // filter
	        } else switch (TYPE) {
	          case 4: return false;             // every
	          case 7: push$7(target, value);      // filterReject
	        }
	      }
	    }
	    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
	  };
	};

	var arrayIteration = {
	  // `Array.prototype.forEach` method
	  // https://tc39.es/ecma262/#sec-array.prototype.foreach
	  forEach: createMethod$3(0),
	  // `Array.prototype.map` method
	  // https://tc39.es/ecma262/#sec-array.prototype.map
	  map: createMethod$3(1),
	  // `Array.prototype.filter` method
	  // https://tc39.es/ecma262/#sec-array.prototype.filter
	  filter: createMethod$3(2),
	  // `Array.prototype.some` method
	  // https://tc39.es/ecma262/#sec-array.prototype.some
	  some: createMethod$3(3),
	  // `Array.prototype.every` method
	  // https://tc39.es/ecma262/#sec-array.prototype.every
	  every: createMethod$3(4),
	  // `Array.prototype.find` method
	  // https://tc39.es/ecma262/#sec-array.prototype.find
	  find: createMethod$3(5),
	  // `Array.prototype.findIndex` method
	  // https://tc39.es/ecma262/#sec-array.prototype.findIndex
	  findIndex: createMethod$3(6),
	  // `Array.prototype.filterReject` method
	  // https://github.com/tc39/proposal-array-filtering
	  filterReject: createMethod$3(7)
	};

	var $$19 = _export;
	var global$j = global$t;
	var call$o = functionCall;
	var uncurryThis$m = functionUncurryThis;
	var DESCRIPTORS$c = descriptors;
	var NATIVE_SYMBOL$3 = symbolConstructorDetection;
	var fails$m = fails$x;
	var hasOwn$c = hasOwnProperty_1;
	var isPrototypeOf$k = objectIsPrototypeOf;
	var anObject$q = anObject$u;
	var toIndexedObject$5 = toIndexedObject$b;
	var toPropertyKey = toPropertyKey$4;
	var $toString = toString$c;
	var createPropertyDescriptor$3 = createPropertyDescriptor$7;
	var nativeObjectCreate = objectCreate;
	var objectKeys$2 = objectKeys$4;
	var getOwnPropertyNamesModule$2 = objectGetOwnPropertyNames;
	var getOwnPropertyNamesExternal = objectGetOwnPropertyNamesExternal;
	var getOwnPropertySymbolsModule$3 = objectGetOwnPropertySymbols;
	var getOwnPropertyDescriptorModule$3 = objectGetOwnPropertyDescriptor;
	var definePropertyModule$2 = objectDefineProperty;
	var definePropertiesModule = objectDefineProperties;
	var propertyIsEnumerableModule$1 = objectPropertyIsEnumerable;
	var defineBuiltIn$4 = defineBuiltIn$6;
	var shared$2 = shared$6.exports;
	var sharedKey$1 = sharedKey$4;
	var hiddenKeys$1 = hiddenKeys$6;
	var uid$1 = uid$4;
	var wellKnownSymbol$e = wellKnownSymbol$o;
	var wrappedWellKnownSymbolModule = wellKnownSymbolWrapped;
	var defineWellKnownSymbol$l = wellKnownSymbolDefine;
	var defineSymbolToPrimitive$1 = symbolDefineToPrimitive;
	var setToStringTag$6 = setToStringTag$7;
	var InternalStateModule$6 = internalState;
	var $forEach$1 = arrayIteration.forEach;

	var HIDDEN = sharedKey$1('hidden');
	var SYMBOL = 'Symbol';
	var PROTOTYPE = 'prototype';

	var setInternalState$6 = InternalStateModule$6.set;
	var getInternalState$2 = InternalStateModule$6.getterFor(SYMBOL);

	var ObjectPrototype$2 = Object[PROTOTYPE];
	var $Symbol = global$j.Symbol;
	var SymbolPrototype = $Symbol && $Symbol[PROTOTYPE];
	var TypeError$2 = global$j.TypeError;
	var QObject = global$j.QObject;
	var nativeGetOwnPropertyDescriptor$1 = getOwnPropertyDescriptorModule$3.f;
	var nativeDefineProperty = definePropertyModule$2.f;
	var nativeGetOwnPropertyNames = getOwnPropertyNamesExternal.f;
	var nativePropertyIsEnumerable = propertyIsEnumerableModule$1.f;
	var push$6 = uncurryThis$m([].push);

	var AllSymbols = shared$2('symbols');
	var ObjectPrototypeSymbols = shared$2('op-symbols');
	var WellKnownSymbolsStore = shared$2('wks');

	// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
	var USE_SETTER = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

	// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
	var setSymbolDescriptor = DESCRIPTORS$c && fails$m(function () {
	  return nativeObjectCreate(nativeDefineProperty({}, 'a', {
	    get: function () { return nativeDefineProperty(this, 'a', { value: 7 }).a; }
	  })).a != 7;
	}) ? function (O, P, Attributes) {
	  var ObjectPrototypeDescriptor = nativeGetOwnPropertyDescriptor$1(ObjectPrototype$2, P);
	  if (ObjectPrototypeDescriptor) delete ObjectPrototype$2[P];
	  nativeDefineProperty(O, P, Attributes);
	  if (ObjectPrototypeDescriptor && O !== ObjectPrototype$2) {
	    nativeDefineProperty(ObjectPrototype$2, P, ObjectPrototypeDescriptor);
	  }
	} : nativeDefineProperty;

	var wrap$1 = function (tag, description) {
	  var symbol = AllSymbols[tag] = nativeObjectCreate(SymbolPrototype);
	  setInternalState$6(symbol, {
	    type: SYMBOL,
	    tag: tag,
	    description: description
	  });
	  if (!DESCRIPTORS$c) symbol.description = description;
	  return symbol;
	};

	var $defineProperty = function defineProperty(O, P, Attributes) {
	  if (O === ObjectPrototype$2) $defineProperty(ObjectPrototypeSymbols, P, Attributes);
	  anObject$q(O);
	  var key = toPropertyKey(P);
	  anObject$q(Attributes);
	  if (hasOwn$c(AllSymbols, key)) {
	    if (!Attributes.enumerable) {
	      if (!hasOwn$c(O, HIDDEN)) nativeDefineProperty(O, HIDDEN, createPropertyDescriptor$3(1, {}));
	      O[HIDDEN][key] = true;
	    } else {
	      if (hasOwn$c(O, HIDDEN) && O[HIDDEN][key]) O[HIDDEN][key] = false;
	      Attributes = nativeObjectCreate(Attributes, { enumerable: createPropertyDescriptor$3(0, false) });
	    } return setSymbolDescriptor(O, key, Attributes);
	  } return nativeDefineProperty(O, key, Attributes);
	};

	var $defineProperties = function defineProperties(O, Properties) {
	  anObject$q(O);
	  var properties = toIndexedObject$5(Properties);
	  var keys = objectKeys$2(properties).concat($getOwnPropertySymbols(properties));
	  $forEach$1(keys, function (key) {
	    if (!DESCRIPTORS$c || call$o($propertyIsEnumerable$1, properties, key)) $defineProperty(O, key, properties[key]);
	  });
	  return O;
	};

	var $create = function create(O, Properties) {
	  return Properties === undefined ? nativeObjectCreate(O) : $defineProperties(nativeObjectCreate(O), Properties);
	};

	var $propertyIsEnumerable$1 = function propertyIsEnumerable(V) {
	  var P = toPropertyKey(V);
	  var enumerable = call$o(nativePropertyIsEnumerable, this, P);
	  if (this === ObjectPrototype$2 && hasOwn$c(AllSymbols, P) && !hasOwn$c(ObjectPrototypeSymbols, P)) return false;
	  return enumerable || !hasOwn$c(this, P) || !hasOwn$c(AllSymbols, P) || hasOwn$c(this, HIDDEN) && this[HIDDEN][P]
	    ? enumerable : true;
	};

	var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(O, P) {
	  var it = toIndexedObject$5(O);
	  var key = toPropertyKey(P);
	  if (it === ObjectPrototype$2 && hasOwn$c(AllSymbols, key) && !hasOwn$c(ObjectPrototypeSymbols, key)) return;
	  var descriptor = nativeGetOwnPropertyDescriptor$1(it, key);
	  if (descriptor && hasOwn$c(AllSymbols, key) && !(hasOwn$c(it, HIDDEN) && it[HIDDEN][key])) {
	    descriptor.enumerable = true;
	  }
	  return descriptor;
	};

	var $getOwnPropertyNames = function getOwnPropertyNames(O) {
	  var names = nativeGetOwnPropertyNames(toIndexedObject$5(O));
	  var result = [];
	  $forEach$1(names, function (key) {
	    if (!hasOwn$c(AllSymbols, key) && !hasOwn$c(hiddenKeys$1, key)) push$6(result, key);
	  });
	  return result;
	};

	var $getOwnPropertySymbols = function (O) {
	  var IS_OBJECT_PROTOTYPE = O === ObjectPrototype$2;
	  var names = nativeGetOwnPropertyNames(IS_OBJECT_PROTOTYPE ? ObjectPrototypeSymbols : toIndexedObject$5(O));
	  var result = [];
	  $forEach$1(names, function (key) {
	    if (hasOwn$c(AllSymbols, key) && (!IS_OBJECT_PROTOTYPE || hasOwn$c(ObjectPrototype$2, key))) {
	      push$6(result, AllSymbols[key]);
	    }
	  });
	  return result;
	};

	// `Symbol` constructor
	// https://tc39.es/ecma262/#sec-symbol-constructor
	if (!NATIVE_SYMBOL$3) {
	  $Symbol = function Symbol() {
	    if (isPrototypeOf$k(SymbolPrototype, this)) throw TypeError$2('Symbol is not a constructor');
	    var description = !arguments.length || arguments[0] === undefined ? undefined : $toString(arguments[0]);
	    var tag = uid$1(description);
	    var setter = function (value) {
	      if (this === ObjectPrototype$2) call$o(setter, ObjectPrototypeSymbols, value);
	      if (hasOwn$c(this, HIDDEN) && hasOwn$c(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
	      setSymbolDescriptor(this, tag, createPropertyDescriptor$3(1, value));
	    };
	    if (DESCRIPTORS$c && USE_SETTER) setSymbolDescriptor(ObjectPrototype$2, tag, { configurable: true, set: setter });
	    return wrap$1(tag, description);
	  };

	  SymbolPrototype = $Symbol[PROTOTYPE];

	  defineBuiltIn$4(SymbolPrototype, 'toString', function toString() {
	    return getInternalState$2(this).tag;
	  });

	  defineBuiltIn$4($Symbol, 'withoutSetter', function (description) {
	    return wrap$1(uid$1(description), description);
	  });

	  propertyIsEnumerableModule$1.f = $propertyIsEnumerable$1;
	  definePropertyModule$2.f = $defineProperty;
	  definePropertiesModule.f = $defineProperties;
	  getOwnPropertyDescriptorModule$3.f = $getOwnPropertyDescriptor;
	  getOwnPropertyNamesModule$2.f = getOwnPropertyNamesExternal.f = $getOwnPropertyNames;
	  getOwnPropertySymbolsModule$3.f = $getOwnPropertySymbols;

	  wrappedWellKnownSymbolModule.f = function (name) {
	    return wrap$1(wellKnownSymbol$e(name), name);
	  };

	  if (DESCRIPTORS$c) {
	    // https://github.com/tc39/proposal-Symbol-description
	    nativeDefineProperty(SymbolPrototype, 'description', {
	      configurable: true,
	      get: function description() {
	        return getInternalState$2(this).description;
	      }
	    });
	  }
	}

	$$19({ global: true, constructor: true, wrap: true, forced: !NATIVE_SYMBOL$3, sham: !NATIVE_SYMBOL$3 }, {
	  Symbol: $Symbol
	});

	$forEach$1(objectKeys$2(WellKnownSymbolsStore), function (name) {
	  defineWellKnownSymbol$l(name);
	});

	$$19({ target: SYMBOL, stat: true, forced: !NATIVE_SYMBOL$3 }, {
	  useSetter: function () { USE_SETTER = true; },
	  useSimple: function () { USE_SETTER = false; }
	});

	$$19({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$3, sham: !DESCRIPTORS$c }, {
	  // `Object.create` method
	  // https://tc39.es/ecma262/#sec-object.create
	  create: $create,
	  // `Object.defineProperty` method
	  // https://tc39.es/ecma262/#sec-object.defineproperty
	  defineProperty: $defineProperty,
	  // `Object.defineProperties` method
	  // https://tc39.es/ecma262/#sec-object.defineproperties
	  defineProperties: $defineProperties,
	  // `Object.getOwnPropertyDescriptor` method
	  // https://tc39.es/ecma262/#sec-object.getownpropertydescriptors
	  getOwnPropertyDescriptor: $getOwnPropertyDescriptor
	});

	$$19({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$3 }, {
	  // `Object.getOwnPropertyNames` method
	  // https://tc39.es/ecma262/#sec-object.getownpropertynames
	  getOwnPropertyNames: $getOwnPropertyNames
	});

	// `Symbol.prototype[@@toPrimitive]` method
	// https://tc39.es/ecma262/#sec-symbol.prototype-@@toprimitive
	defineSymbolToPrimitive$1();

	// `Symbol.prototype[@@toStringTag]` property
	// https://tc39.es/ecma262/#sec-symbol.prototype-@@tostringtag
	setToStringTag$6($Symbol, SYMBOL);

	hiddenKeys$1[HIDDEN] = true;

	var NATIVE_SYMBOL$2 = symbolConstructorDetection;

	/* eslint-disable es/no-symbol -- safe */
	var symbolRegistryDetection = NATIVE_SYMBOL$2 && !!Symbol['for'] && !!Symbol.keyFor;

	var $$18 = _export;
	var getBuiltIn$b = getBuiltIn$h;
	var hasOwn$b = hasOwnProperty_1;
	var toString$a = toString$c;
	var shared$1 = shared$6.exports;
	var NATIVE_SYMBOL_REGISTRY$1 = symbolRegistryDetection;

	var StringToSymbolRegistry = shared$1('string-to-symbol-registry');
	var SymbolToStringRegistry$1 = shared$1('symbol-to-string-registry');

	// `Symbol.for` method
	// https://tc39.es/ecma262/#sec-symbol.for
	$$18({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY$1 }, {
	  'for': function (key) {
	    var string = toString$a(key);
	    if (hasOwn$b(StringToSymbolRegistry, string)) return StringToSymbolRegistry[string];
	    var symbol = getBuiltIn$b('Symbol')(string);
	    StringToSymbolRegistry[string] = symbol;
	    SymbolToStringRegistry$1[symbol] = string;
	    return symbol;
	  }
	});

	var $$17 = _export;
	var hasOwn$a = hasOwnProperty_1;
	var isSymbol$1 = isSymbol$4;
	var tryToString$4 = tryToString$6;
	var shared = shared$6.exports;
	var NATIVE_SYMBOL_REGISTRY = symbolRegistryDetection;

	var SymbolToStringRegistry = shared('symbol-to-string-registry');

	// `Symbol.keyFor` method
	// https://tc39.es/ecma262/#sec-symbol.keyfor
	$$17({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY }, {
	  keyFor: function keyFor(sym) {
	    if (!isSymbol$1(sym)) throw TypeError(tryToString$4(sym) + ' is not a symbol');
	    if (hasOwn$a(SymbolToStringRegistry, sym)) return SymbolToStringRegistry[sym];
	  }
	});

	var uncurryThis$l = functionUncurryThis;

	var arraySlice$6 = uncurryThis$l([].slice);

	var $$16 = _export;
	var getBuiltIn$a = getBuiltIn$h;
	var apply$4 = functionApply;
	var call$n = functionCall;
	var uncurryThis$k = functionUncurryThis;
	var fails$l = fails$x;
	var isArray$b = isArray$e;
	var isCallable$a = isCallable$m;
	var isObject$e = isObject$m;
	var isSymbol = isSymbol$4;
	var arraySlice$5 = arraySlice$6;
	var NATIVE_SYMBOL$1 = symbolConstructorDetection;

	var $stringify = getBuiltIn$a('JSON', 'stringify');
	var exec$1 = uncurryThis$k(/./.exec);
	var charAt$3 = uncurryThis$k(''.charAt);
	var charCodeAt$1 = uncurryThis$k(''.charCodeAt);
	var replace$2 = uncurryThis$k(''.replace);
	var numberToString = uncurryThis$k(1.0.toString);

	var tester = /[\uD800-\uDFFF]/g;
	var low = /^[\uD800-\uDBFF]$/;
	var hi = /^[\uDC00-\uDFFF]$/;

	var WRONG_SYMBOLS_CONVERSION = !NATIVE_SYMBOL$1 || fails$l(function () {
	  var symbol = getBuiltIn$a('Symbol')();
	  // MS Edge converts symbol values to JSON as {}
	  return $stringify([symbol]) != '[null]'
	    // WebKit converts symbol values to JSON as null
	    || $stringify({ a: symbol }) != '{}'
	    // V8 throws on boxed symbols
	    || $stringify(Object(symbol)) != '{}';
	});

	// https://github.com/tc39/proposal-well-formed-stringify
	var ILL_FORMED_UNICODE = fails$l(function () {
	  return $stringify('\uDF06\uD834') !== '"\\udf06\\ud834"'
	    || $stringify('\uDEAD') !== '"\\udead"';
	});

	var stringifyWithSymbolsFix = function (it, replacer) {
	  var args = arraySlice$5(arguments);
	  var $replacer = replacer;
	  if (!isObject$e(replacer) && it === undefined || isSymbol(it)) return; // IE8 returns string on undefined
	  if (!isArray$b(replacer)) replacer = function (key, value) {
	    if (isCallable$a($replacer)) value = call$n($replacer, this, key, value);
	    if (!isSymbol(value)) return value;
	  };
	  args[1] = replacer;
	  return apply$4($stringify, null, args);
	};

	var fixIllFormed = function (match, offset, string) {
	  var prev = charAt$3(string, offset - 1);
	  var next = charAt$3(string, offset + 1);
	  if ((exec$1(low, match) && !exec$1(hi, next)) || (exec$1(hi, match) && !exec$1(low, prev))) {
	    return '\\u' + numberToString(charCodeAt$1(match, 0), 16);
	  } return match;
	};

	if ($stringify) {
	  // `JSON.stringify` method
	  // https://tc39.es/ecma262/#sec-json.stringify
	  $$16({ target: 'JSON', stat: true, arity: 3, forced: WRONG_SYMBOLS_CONVERSION || ILL_FORMED_UNICODE }, {
	    // eslint-disable-next-line no-unused-vars -- required for `.length`
	    stringify: function stringify(it, replacer, space) {
	      var args = arraySlice$5(arguments);
	      var result = apply$4(WRONG_SYMBOLS_CONVERSION ? stringifyWithSymbolsFix : $stringify, null, args);
	      return ILL_FORMED_UNICODE && typeof result == 'string' ? replace$2(result, tester, fixIllFormed) : result;
	    }
	  });
	}

	var $$15 = _export;
	var NATIVE_SYMBOL = symbolConstructorDetection;
	var fails$k = fails$x;
	var getOwnPropertySymbolsModule$2 = objectGetOwnPropertySymbols;
	var toObject$8 = toObject$c;

	// V8 ~ Chrome 38 and 39 `Object.getOwnPropertySymbols` fails on primitives
	// https://bugs.chromium.org/p/v8/issues/detail?id=3443
	var FORCED$5 = !NATIVE_SYMBOL || fails$k(function () { getOwnPropertySymbolsModule$2.f(1); });

	// `Object.getOwnPropertySymbols` method
	// https://tc39.es/ecma262/#sec-object.getownpropertysymbols
	$$15({ target: 'Object', stat: true, forced: FORCED$5 }, {
	  getOwnPropertySymbols: function getOwnPropertySymbols(it) {
	    var $getOwnPropertySymbols = getOwnPropertySymbolsModule$2.f;
	    return $getOwnPropertySymbols ? $getOwnPropertySymbols(toObject$8(it)) : [];
	  }
	});

	var defineWellKnownSymbol$k = wellKnownSymbolDefine;

	// `Symbol.asyncIterator` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.asynciterator
	defineWellKnownSymbol$k('asyncIterator');

	var defineWellKnownSymbol$j = wellKnownSymbolDefine;

	// `Symbol.hasInstance` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.hasinstance
	defineWellKnownSymbol$j('hasInstance');

	var defineWellKnownSymbol$i = wellKnownSymbolDefine;

	// `Symbol.isConcatSpreadable` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.isconcatspreadable
	defineWellKnownSymbol$i('isConcatSpreadable');

	var defineWellKnownSymbol$h = wellKnownSymbolDefine;

	// `Symbol.iterator` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.iterator
	defineWellKnownSymbol$h('iterator');

	var defineWellKnownSymbol$g = wellKnownSymbolDefine;

	// `Symbol.match` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.match
	defineWellKnownSymbol$g('match');

	var defineWellKnownSymbol$f = wellKnownSymbolDefine;

	// `Symbol.matchAll` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.matchall
	defineWellKnownSymbol$f('matchAll');

	var defineWellKnownSymbol$e = wellKnownSymbolDefine;

	// `Symbol.replace` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.replace
	defineWellKnownSymbol$e('replace');

	var defineWellKnownSymbol$d = wellKnownSymbolDefine;

	// `Symbol.search` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.search
	defineWellKnownSymbol$d('search');

	var defineWellKnownSymbol$c = wellKnownSymbolDefine;

	// `Symbol.species` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.species
	defineWellKnownSymbol$c('species');

	var defineWellKnownSymbol$b = wellKnownSymbolDefine;

	// `Symbol.split` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.split
	defineWellKnownSymbol$b('split');

	var defineWellKnownSymbol$a = wellKnownSymbolDefine;
	var defineSymbolToPrimitive = symbolDefineToPrimitive;

	// `Symbol.toPrimitive` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.toprimitive
	defineWellKnownSymbol$a('toPrimitive');

	// `Symbol.prototype[@@toPrimitive]` method
	// https://tc39.es/ecma262/#sec-symbol.prototype-@@toprimitive
	defineSymbolToPrimitive();

	var getBuiltIn$9 = getBuiltIn$h;
	var defineWellKnownSymbol$9 = wellKnownSymbolDefine;
	var setToStringTag$5 = setToStringTag$7;

	// `Symbol.toStringTag` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.tostringtag
	defineWellKnownSymbol$9('toStringTag');

	// `Symbol.prototype[@@toStringTag]` property
	// https://tc39.es/ecma262/#sec-symbol.prototype-@@tostringtag
	setToStringTag$5(getBuiltIn$9('Symbol'), 'Symbol');

	var defineWellKnownSymbol$8 = wellKnownSymbolDefine;

	// `Symbol.unscopables` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.unscopables
	defineWellKnownSymbol$8('unscopables');

	var global$i = global$t;
	var setToStringTag$4 = setToStringTag$7;

	// JSON[@@toStringTag] property
	// https://tc39.es/ecma262/#sec-json-@@tostringtag
	setToStringTag$4(global$i.JSON, 'JSON', true);

	var path$o = path$s;

	var symbol$4 = path$o.Symbol;

	var addToUnscopables$1 = function () { /* empty */ };

	var iterators = {};

	var DESCRIPTORS$b = descriptors;
	var hasOwn$9 = hasOwnProperty_1;

	var FunctionPrototype$1 = Function.prototype;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getDescriptor = DESCRIPTORS$b && Object.getOwnPropertyDescriptor;

	var EXISTS = hasOwn$9(FunctionPrototype$1, 'name');
	// additional protection from minified / mangled / dropped function names
	var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
	var CONFIGURABLE = EXISTS && (!DESCRIPTORS$b || (DESCRIPTORS$b && getDescriptor(FunctionPrototype$1, 'name').configurable));

	var functionName = {
	  EXISTS: EXISTS,
	  PROPER: PROPER,
	  CONFIGURABLE: CONFIGURABLE
	};

	var fails$j = fails$x;

	var correctPrototypeGetter = !fails$j(function () {
	  function F() { /* empty */ }
	  F.prototype.constructor = null;
	  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
	  return Object.getPrototypeOf(new F()) !== F.prototype;
	});

	var hasOwn$8 = hasOwnProperty_1;
	var isCallable$9 = isCallable$m;
	var toObject$7 = toObject$c;
	var sharedKey = sharedKey$4;
	var CORRECT_PROTOTYPE_GETTER$1 = correctPrototypeGetter;

	var IE_PROTO = sharedKey('IE_PROTO');
	var $Object$1 = Object;
	var ObjectPrototype$1 = $Object$1.prototype;

	// `Object.getPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.getprototypeof
	// eslint-disable-next-line es/no-object-getprototypeof -- safe
	var objectGetPrototypeOf = CORRECT_PROTOTYPE_GETTER$1 ? $Object$1.getPrototypeOf : function (O) {
	  var object = toObject$7(O);
	  if (hasOwn$8(object, IE_PROTO)) return object[IE_PROTO];
	  var constructor = object.constructor;
	  if (isCallable$9(constructor) && object instanceof constructor) {
	    return constructor.prototype;
	  } return object instanceof $Object$1 ? ObjectPrototype$1 : null;
	};

	var fails$i = fails$x;
	var isCallable$8 = isCallable$m;
	var isObject$d = isObject$m;
	var create$c = objectCreate;
	var getPrototypeOf$9 = objectGetPrototypeOf;
	var defineBuiltIn$3 = defineBuiltIn$6;
	var wellKnownSymbol$d = wellKnownSymbol$o;

	var ITERATOR$7 = wellKnownSymbol$d('iterator');
	var BUGGY_SAFARI_ITERATORS$1 = false;

	// `%IteratorPrototype%` object
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
	var IteratorPrototype$1, PrototypeOfArrayIteratorPrototype, arrayIterator;

	/* eslint-disable es/no-array-prototype-keys -- safe */
	if ([].keys) {
	  arrayIterator = [].keys();
	  // Safari 8 has buggy iterators w/o `next`
	  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS$1 = true;
	  else {
	    PrototypeOfArrayIteratorPrototype = getPrototypeOf$9(getPrototypeOf$9(arrayIterator));
	    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$1 = PrototypeOfArrayIteratorPrototype;
	  }
	}

	var NEW_ITERATOR_PROTOTYPE = !isObject$d(IteratorPrototype$1) || fails$i(function () {
	  var test = {};
	  // FF44- legacy iterators case
	  return IteratorPrototype$1[ITERATOR$7].call(test) !== test;
	});

	if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$1 = {};
	else IteratorPrototype$1 = create$c(IteratorPrototype$1);

	// `%IteratorPrototype%[@@iterator]()` method
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
	if (!isCallable$8(IteratorPrototype$1[ITERATOR$7])) {
	  defineBuiltIn$3(IteratorPrototype$1, ITERATOR$7, function () {
	    return this;
	  });
	}

	var iteratorsCore = {
	  IteratorPrototype: IteratorPrototype$1,
	  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
	};

	var IteratorPrototype = iteratorsCore.IteratorPrototype;
	var create$b = objectCreate;
	var createPropertyDescriptor$2 = createPropertyDescriptor$7;
	var setToStringTag$3 = setToStringTag$7;
	var Iterators$6 = iterators;

	var returnThis$1 = function () { return this; };

	var iteratorCreateConstructor = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
	  var TO_STRING_TAG = NAME + ' Iterator';
	  IteratorConstructor.prototype = create$b(IteratorPrototype, { next: createPropertyDescriptor$2(+!ENUMERABLE_NEXT, next) });
	  setToStringTag$3(IteratorConstructor, TO_STRING_TAG, false, true);
	  Iterators$6[TO_STRING_TAG] = returnThis$1;
	  return IteratorConstructor;
	};

	var isCallable$7 = isCallable$m;

	var $String = String;
	var $TypeError$c = TypeError;

	var aPossiblePrototype$1 = function (argument) {
	  if (typeof argument == 'object' || isCallable$7(argument)) return argument;
	  throw $TypeError$c("Can't set " + $String(argument) + ' as a prototype');
	};

	/* eslint-disable no-proto -- safe */

	var uncurryThis$j = functionUncurryThis;
	var anObject$p = anObject$u;
	var aPossiblePrototype = aPossiblePrototype$1;

	// `Object.setPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.setprototypeof
	// Works with __proto__ only. Old v8 can't work with null proto objects.
	// eslint-disable-next-line es/no-object-setprototypeof -- safe
	var objectSetPrototypeOf = Object.setPrototypeOf || ('__proto__' in {} ? function () {
	  var CORRECT_SETTER = false;
	  var test = {};
	  var setter;
	  try {
	    // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	    setter = uncurryThis$j(Object.getOwnPropertyDescriptor(Object.prototype, '__proto__').set);
	    setter(test, []);
	    CORRECT_SETTER = test instanceof Array;
	  } catch (error) { /* empty */ }
	  return function setPrototypeOf(O, proto) {
	    anObject$p(O);
	    aPossiblePrototype(proto);
	    if (CORRECT_SETTER) setter(O, proto);
	    else O.__proto__ = proto;
	    return O;
	  };
	}() : undefined);

	var $$14 = _export;
	var call$m = functionCall;
	var FunctionName = functionName;
	var createIteratorConstructor = iteratorCreateConstructor;
	var getPrototypeOf$8 = objectGetPrototypeOf;
	var setToStringTag$2 = setToStringTag$7;
	var defineBuiltIn$2 = defineBuiltIn$6;
	var wellKnownSymbol$c = wellKnownSymbol$o;
	var Iterators$5 = iterators;
	var IteratorsCore = iteratorsCore;

	var PROPER_FUNCTION_NAME$1 = FunctionName.PROPER;
	FunctionName.CONFIGURABLE;
	IteratorsCore.IteratorPrototype;
	var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
	var ITERATOR$6 = wellKnownSymbol$c('iterator');
	var KEYS = 'keys';
	var VALUES = 'values';
	var ENTRIES = 'entries';

	var returnThis = function () { return this; };

	var iteratorDefine = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
	  createIteratorConstructor(IteratorConstructor, NAME, next);

	  var getIterationMethod = function (KIND) {
	    if (KIND === DEFAULT && defaultIterator) return defaultIterator;
	    if (!BUGGY_SAFARI_ITERATORS && KIND in IterablePrototype) return IterablePrototype[KIND];
	    switch (KIND) {
	      case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
	      case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
	      case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
	    } return function () { return new IteratorConstructor(this); };
	  };

	  var TO_STRING_TAG = NAME + ' Iterator';
	  var INCORRECT_VALUES_NAME = false;
	  var IterablePrototype = Iterable.prototype;
	  var nativeIterator = IterablePrototype[ITERATOR$6]
	    || IterablePrototype['@@iterator']
	    || DEFAULT && IterablePrototype[DEFAULT];
	  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
	  var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
	  var CurrentIteratorPrototype, methods, KEY;

	  // fix native
	  if (anyNativeIterator) {
	    CurrentIteratorPrototype = getPrototypeOf$8(anyNativeIterator.call(new Iterable()));
	    if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
	      // Set @@toStringTag to native iterators
	      setToStringTag$2(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
	      Iterators$5[TO_STRING_TAG] = returnThis;
	    }
	  }

	  // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
	  if (PROPER_FUNCTION_NAME$1 && DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
	    {
	      INCORRECT_VALUES_NAME = true;
	      defaultIterator = function values() { return call$m(nativeIterator, this); };
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
	        defineBuiltIn$2(IterablePrototype, KEY, methods[KEY]);
	      }
	    } else $$14({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
	  }

	  // define iterator
	  if ((FORCED) && IterablePrototype[ITERATOR$6] !== defaultIterator) {
	    defineBuiltIn$2(IterablePrototype, ITERATOR$6, defaultIterator, { name: DEFAULT });
	  }
	  Iterators$5[NAME] = defaultIterator;

	  return methods;
	};

	// `CreateIterResultObject` abstract operation
	// https://tc39.es/ecma262/#sec-createiterresultobject
	var createIterResultObject$3 = function (value, done) {
	  return { value: value, done: done };
	};

	var toIndexedObject$4 = toIndexedObject$b;
	var addToUnscopables = addToUnscopables$1;
	var Iterators$4 = iterators;
	var InternalStateModule$5 = internalState;
	var defineProperty$c = objectDefineProperty.f;
	var defineIterator$2 = iteratorDefine;
	var createIterResultObject$2 = createIterResultObject$3;
	var IS_PURE$1 = isPure;
	var DESCRIPTORS$a = descriptors;

	var ARRAY_ITERATOR = 'Array Iterator';
	var setInternalState$5 = InternalStateModule$5.set;
	var getInternalState$1 = InternalStateModule$5.getterFor(ARRAY_ITERATOR);

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
	defineIterator$2(Array, 'Array', function (iterated, kind) {
	  setInternalState$5(this, {
	    type: ARRAY_ITERATOR,
	    target: toIndexedObject$4(iterated), // target
	    index: 0,                          // next index
	    kind: kind                         // kind
	  });
	// `%ArrayIteratorPrototype%.next` method
	// https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
	}, function () {
	  var state = getInternalState$1(this);
	  var target = state.target;
	  var kind = state.kind;
	  var index = state.index++;
	  if (!target || index >= target.length) {
	    state.target = undefined;
	    return createIterResultObject$2(undefined, true);
	  }
	  if (kind == 'keys') return createIterResultObject$2(index, false);
	  if (kind == 'values') return createIterResultObject$2(target[index], false);
	  return createIterResultObject$2([index, target[index]], false);
	}, 'values');

	// argumentsList[@@iterator] is %ArrayProto_values%
	// https://tc39.es/ecma262/#sec-createunmappedargumentsobject
	// https://tc39.es/ecma262/#sec-createmappedargumentsobject
	var values$3 = Iterators$4.Arguments = Iterators$4.Array;

	// https://tc39.es/ecma262/#sec-array.prototype-@@unscopables
	addToUnscopables();
	addToUnscopables();
	addToUnscopables();

	// V8 ~ Chrome 45- bug
	if (!IS_PURE$1 && DESCRIPTORS$a && values$3.name !== 'values') try {
	  defineProperty$c(values$3, 'name', { value: 'values' });
	} catch (error) { /* empty */ }

	// iterable DOM collections
	// flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
	var domIterables = {
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

	var DOMIterables$1 = domIterables;
	var global$h = global$t;
	var classof$6 = classof$b;
	var createNonEnumerableProperty$3 = createNonEnumerableProperty$8;
	var Iterators$3 = iterators;
	var wellKnownSymbol$b = wellKnownSymbol$o;

	var TO_STRING_TAG$1 = wellKnownSymbol$b('toStringTag');

	for (var COLLECTION_NAME in DOMIterables$1) {
	  var Collection = global$h[COLLECTION_NAME];
	  var CollectionPrototype = Collection && Collection.prototype;
	  if (CollectionPrototype && classof$6(CollectionPrototype) !== TO_STRING_TAG$1) {
	    createNonEnumerableProperty$3(CollectionPrototype, TO_STRING_TAG$1, COLLECTION_NAME);
	  }
	  Iterators$3[COLLECTION_NAME] = Iterators$3.Array;
	}

	var parent$1f = symbol$4;


	var symbol$3 = parent$1f;

	var parent$1e = symbol$3;

	var symbol$2 = parent$1e;

	var defineWellKnownSymbol$7 = wellKnownSymbolDefine;

	// `Symbol.asyncDispose` well-known symbol
	// https://github.com/tc39/proposal-using-statement
	defineWellKnownSymbol$7('asyncDispose');

	var defineWellKnownSymbol$6 = wellKnownSymbolDefine;

	// `Symbol.dispose` well-known symbol
	// https://github.com/tc39/proposal-using-statement
	defineWellKnownSymbol$6('dispose');

	var defineWellKnownSymbol$5 = wellKnownSymbolDefine;

	// `Symbol.matcher` well-known symbol
	// https://github.com/tc39/proposal-pattern-matching
	defineWellKnownSymbol$5('matcher');

	var defineWellKnownSymbol$4 = wellKnownSymbolDefine;

	// `Symbol.metadataKey` well-known symbol
	// https://github.com/tc39/proposal-decorator-metadata
	defineWellKnownSymbol$4('metadataKey');

	var defineWellKnownSymbol$3 = wellKnownSymbolDefine;

	// `Symbol.observable` well-known symbol
	// https://github.com/tc39/proposal-observable
	defineWellKnownSymbol$3('observable');

	// TODO: Remove from `core-js@4`
	var defineWellKnownSymbol$2 = wellKnownSymbolDefine;

	// `Symbol.metadata` well-known symbol
	// https://github.com/tc39/proposal-decorators
	defineWellKnownSymbol$2('metadata');

	// TODO: remove from `core-js@4`
	var defineWellKnownSymbol$1 = wellKnownSymbolDefine;

	// `Symbol.patternMatch` well-known symbol
	// https://github.com/tc39/proposal-pattern-matching
	defineWellKnownSymbol$1('patternMatch');

	// TODO: remove from `core-js@4`
	var defineWellKnownSymbol = wellKnownSymbolDefine;

	defineWellKnownSymbol('replaceAll');

	var parent$1d = symbol$2;





	// TODO: Remove from `core-js@4`




	var symbol$1 = parent$1d;

	(function (module) {
		module.exports = symbol$1;
	} (symbol$5));

	(function (module) {
		module.exports = symbol$5.exports;
	} (symbol$6));

	var _Symbol = /*@__PURE__*/getDefaultExportFromCjs(symbol$6.exports);

	var iterator$5 = {exports: {}};

	var iterator$4 = {exports: {}};

	var uncurryThis$i = functionUncurryThis;
	var toIntegerOrInfinity$2 = toIntegerOrInfinity$5;
	var toString$9 = toString$c;
	var requireObjectCoercible$4 = requireObjectCoercible$7;

	var charAt$2 = uncurryThis$i(''.charAt);
	var charCodeAt = uncurryThis$i(''.charCodeAt);
	var stringSlice$1 = uncurryThis$i(''.slice);

	var createMethod$2 = function (CONVERT_TO_STRING) {
	  return function ($this, pos) {
	    var S = toString$9(requireObjectCoercible$4($this));
	    var position = toIntegerOrInfinity$2(pos);
	    var size = S.length;
	    var first, second;
	    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
	    first = charCodeAt(S, position);
	    return first < 0xD800 || first > 0xDBFF || position + 1 === size
	      || (second = charCodeAt(S, position + 1)) < 0xDC00 || second > 0xDFFF
	        ? CONVERT_TO_STRING
	          ? charAt$2(S, position)
	          : first
	        : CONVERT_TO_STRING
	          ? stringSlice$1(S, position, position + 2)
	          : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
	  };
	};

	var stringMultibyte = {
	  // `String.prototype.codePointAt` method
	  // https://tc39.es/ecma262/#sec-string.prototype.codepointat
	  codeAt: createMethod$2(false),
	  // `String.prototype.at` method
	  // https://github.com/mathiasbynens/String.prototype.at
	  charAt: createMethod$2(true)
	};

	var charAt$1 = stringMultibyte.charAt;
	var toString$8 = toString$c;
	var InternalStateModule$4 = internalState;
	var defineIterator$1 = iteratorDefine;
	var createIterResultObject$1 = createIterResultObject$3;

	var STRING_ITERATOR = 'String Iterator';
	var setInternalState$4 = InternalStateModule$4.set;
	var getInternalState = InternalStateModule$4.getterFor(STRING_ITERATOR);

	// `String.prototype[@@iterator]` method
	// https://tc39.es/ecma262/#sec-string.prototype-@@iterator
	defineIterator$1(String, 'String', function (iterated) {
	  setInternalState$4(this, {
	    type: STRING_ITERATOR,
	    string: toString$8(iterated),
	    index: 0
	  });
	// `%StringIteratorPrototype%.next` method
	// https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
	}, function next() {
	  var state = getInternalState(this);
	  var string = state.string;
	  var index = state.index;
	  var point;
	  if (index >= string.length) return createIterResultObject$1(undefined, true);
	  point = charAt$1(string, index);
	  state.index += point.length;
	  return createIterResultObject$1(point, false);
	});

	var WrappedWellKnownSymbolModule = wellKnownSymbolWrapped;

	var iterator$3 = WrappedWellKnownSymbolModule.f('iterator');

	var parent$1c = iterator$3;


	var iterator$2 = parent$1c;

	var parent$1b = iterator$2;

	var iterator$1 = parent$1b;

	var parent$1a = iterator$1;

	var iterator = parent$1a;

	(function (module) {
		module.exports = iterator;
	} (iterator$4));

	(function (module) {
		module.exports = iterator$4.exports;
	} (iterator$5));

	var _Symbol$iterator = /*@__PURE__*/getDefaultExportFromCjs(iterator$5.exports);

	function _typeof(obj) {
	  "@babel/helpers - typeof";

	  return _typeof = "function" == typeof _Symbol && "symbol" == typeof _Symbol$iterator ? function (obj) {
	    return typeof obj;
	  } : function (obj) {
	    return obj && "function" == typeof _Symbol && obj.constructor === _Symbol && obj !== _Symbol.prototype ? "symbol" : typeof obj;
	  }, _typeof(obj);
	}

	function _classCallCheck(instance, Constructor) {
	  if (!(instance instanceof Constructor)) {
	    throw new TypeError("Cannot call a class as a function");
	  }
	}

	var defineProperty$b = {exports: {}};

	var defineProperty$a = {exports: {}};

	var defineProperty$9 = {exports: {}};

	var $$13 = _export;
	var DESCRIPTORS$9 = descriptors;
	var defineProperty$8 = objectDefineProperty.f;

	// `Object.defineProperty` method
	// https://tc39.es/ecma262/#sec-object.defineproperty
	// eslint-disable-next-line es/no-object-defineproperty -- safe
	$$13({ target: 'Object', stat: true, forced: Object.defineProperty !== defineProperty$8, sham: !DESCRIPTORS$9 }, {
	  defineProperty: defineProperty$8
	});

	var path$n = path$s;

	var Object$3 = path$n.Object;

	var defineProperty$7 = defineProperty$9.exports = function defineProperty(it, key, desc) {
	  return Object$3.defineProperty(it, key, desc);
	};

	if (Object$3.defineProperty.sham) defineProperty$7.sham = true;

	var parent$19 = defineProperty$9.exports;

	var defineProperty$6 = parent$19;

	var parent$18 = defineProperty$6;

	var defineProperty$5 = parent$18;

	var parent$17 = defineProperty$5;

	var defineProperty$4 = parent$17;

	(function (module) {
		module.exports = defineProperty$4;
	} (defineProperty$a));

	(function (module) {
		module.exports = defineProperty$a.exports;
	} (defineProperty$b));

	var _Object$defineProperty = /*@__PURE__*/getDefaultExportFromCjs(defineProperty$b.exports);

	function _defineProperties(target, props) {
	  for (var i = 0; i < props.length; i++) {
	    var descriptor = props[i];
	    descriptor.enumerable = descriptor.enumerable || false;
	    descriptor.configurable = true;
	    if ("value" in descriptor) descriptor.writable = true;
	    _Object$defineProperty(target, descriptor.key, descriptor);
	  }
	}
	function _createClass(Constructor, protoProps, staticProps) {
	  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
	  if (staticProps) _defineProperties(Constructor, staticProps);
	  _Object$defineProperty(Constructor, "prototype", {
	    writable: false
	  });
	  return Constructor;
	}

	function _classApplyDescriptorGet(receiver, descriptor) {
	  if (descriptor.get) {
	    return descriptor.get.call(receiver);
	  }
	  return descriptor.value;
	}

	function _classExtractFieldDescriptor(receiver, privateMap, action) {
	  if (!privateMap.has(receiver)) {
	    throw new TypeError("attempted to " + action + " private field on non-instance");
	  }
	  return privateMap.get(receiver);
	}

	function _classPrivateFieldGet(receiver, privateMap) {
	  var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "get");
	  return _classApplyDescriptorGet(receiver, descriptor);
	}

	function _classApplyDescriptorSet(receiver, descriptor, value) {
	  if (descriptor.set) {
	    descriptor.set.call(receiver, value);
	  } else {
	    if (!descriptor.writable) {
	      throw new TypeError("attempted to set read only private field");
	    }
	    descriptor.value = value;
	  }
	}

	function _classPrivateFieldSet(receiver, privateMap, value) {
	  var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "set");
	  _classApplyDescriptorSet(receiver, descriptor, value);
	  return value;
	}

	var promise$4 = {exports: {}};

	var getBuiltIn$8 = getBuiltIn$h;
	var uncurryThis$h = functionUncurryThis;
	var getOwnPropertyNamesModule$1 = objectGetOwnPropertyNames;
	var getOwnPropertySymbolsModule$1 = objectGetOwnPropertySymbols;
	var anObject$o = anObject$u;

	var concat$6 = uncurryThis$h([].concat);

	// all object keys, includes non-enumerable and symbols
	var ownKeys$2 = getBuiltIn$8('Reflect', 'ownKeys') || function ownKeys(it) {
	  var keys = getOwnPropertyNamesModule$1.f(anObject$o(it));
	  var getOwnPropertySymbols = getOwnPropertySymbolsModule$1.f;
	  return getOwnPropertySymbols ? concat$6(keys, getOwnPropertySymbols(it)) : keys;
	};

	var hasOwn$7 = hasOwnProperty_1;
	var ownKeys$1 = ownKeys$2;
	var getOwnPropertyDescriptorModule$2 = objectGetOwnPropertyDescriptor;
	var definePropertyModule$1 = objectDefineProperty;

	var copyConstructorProperties$1 = function (target, source, exceptions) {
	  var keys = ownKeys$1(source);
	  var defineProperty = definePropertyModule$1.f;
	  var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule$2.f;
	  for (var i = 0; i < keys.length; i++) {
	    var key = keys[i];
	    if (!hasOwn$7(target, key) && !(exceptions && hasOwn$7(exceptions, key))) {
	      defineProperty(target, key, getOwnPropertyDescriptor(source, key));
	    }
	  }
	};

	var uncurryThis$g = functionUncurryThis;

	var $Error$1 = Error;
	var replace$1 = uncurryThis$g(''.replace);

	var TEST = (function (arg) { return String($Error$1(arg).stack); })('zxcasd');
	var V8_OR_CHAKRA_STACK_ENTRY = /\n\s*at [^:]*:[^\n]*/;
	var IS_V8_OR_CHAKRA_STACK = V8_OR_CHAKRA_STACK_ENTRY.test(TEST);

	var errorStackClear = function (stack, dropEntries) {
	  if (IS_V8_OR_CHAKRA_STACK && typeof stack == 'string' && !$Error$1.prepareStackTrace) {
	    while (dropEntries--) stack = replace$1(stack, V8_OR_CHAKRA_STACK_ENTRY, '');
	  } return stack;
	};

	var isObject$c = isObject$m;
	var createNonEnumerableProperty$2 = createNonEnumerableProperty$8;

	// `InstallErrorCause` abstract operation
	// https://tc39.es/proposal-error-cause/#sec-errorobjects-install-error-cause
	var installErrorCause$1 = function (O, options) {
	  if (isObject$c(options) && 'cause' in options) {
	    createNonEnumerableProperty$2(O, 'cause', options.cause);
	  }
	};

	var wellKnownSymbol$a = wellKnownSymbol$o;
	var Iterators$2 = iterators;

	var ITERATOR$5 = wellKnownSymbol$a('iterator');
	var ArrayPrototype$d = Array.prototype;

	// check on default Array iterator
	var isArrayIteratorMethod$2 = function (it) {
	  return it !== undefined && (Iterators$2.Array === it || ArrayPrototype$d[ITERATOR$5] === it);
	};

	var classof$5 = classof$b;
	var getMethod$1 = getMethod$3;
	var isNullOrUndefined$5 = isNullOrUndefined$8;
	var Iterators$1 = iterators;
	var wellKnownSymbol$9 = wellKnownSymbol$o;

	var ITERATOR$4 = wellKnownSymbol$9('iterator');

	var getIteratorMethod$9 = function (it) {
	  if (!isNullOrUndefined$5(it)) return getMethod$1(it, ITERATOR$4)
	    || getMethod$1(it, '@@iterator')
	    || Iterators$1[classof$5(it)];
	};

	var call$l = functionCall;
	var aCallable$k = aCallable$n;
	var anObject$n = anObject$u;
	var tryToString$3 = tryToString$6;
	var getIteratorMethod$8 = getIteratorMethod$9;

	var $TypeError$b = TypeError;

	var getIterator$4 = function (argument, usingIterator) {
	  var iteratorMethod = arguments.length < 2 ? getIteratorMethod$8(argument) : usingIterator;
	  if (aCallable$k(iteratorMethod)) return anObject$n(call$l(iteratorMethod, argument));
	  throw $TypeError$b(tryToString$3(argument) + ' is not iterable');
	};

	var call$k = functionCall;
	var anObject$m = anObject$u;
	var getMethod = getMethod$3;

	var iteratorClose$2 = function (iterator, kind, value) {
	  var innerResult, innerError;
	  anObject$m(iterator);
	  try {
	    innerResult = getMethod(iterator, 'return');
	    if (!innerResult) {
	      if (kind === 'throw') throw value;
	      return value;
	    }
	    innerResult = call$k(innerResult, iterator);
	  } catch (error) {
	    innerError = true;
	    innerResult = error;
	  }
	  if (kind === 'throw') throw value;
	  if (innerError) throw innerResult;
	  anObject$m(innerResult);
	  return value;
	};

	var bind$n = functionBindContext;
	var call$j = functionCall;
	var anObject$l = anObject$u;
	var tryToString$2 = tryToString$6;
	var isArrayIteratorMethod$1 = isArrayIteratorMethod$2;
	var lengthOfArrayLike$5 = lengthOfArrayLike$a;
	var isPrototypeOf$j = objectIsPrototypeOf;
	var getIterator$3 = getIterator$4;
	var getIteratorMethod$7 = getIteratorMethod$9;
	var iteratorClose$1 = iteratorClose$2;

	var $TypeError$a = TypeError;

	var Result = function (stopped, result) {
	  this.stopped = stopped;
	  this.result = result;
	};

	var ResultPrototype = Result.prototype;

	var iterate$m = function (iterable, unboundFunction, options) {
	  var that = options && options.that;
	  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
	  var IS_RECORD = !!(options && options.IS_RECORD);
	  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
	  var INTERRUPTED = !!(options && options.INTERRUPTED);
	  var fn = bind$n(unboundFunction, that);
	  var iterator, iterFn, index, length, result, next, step;

	  var stop = function (condition) {
	    if (iterator) iteratorClose$1(iterator, 'normal', condition);
	    return new Result(true, condition);
	  };

	  var callFn = function (value) {
	    if (AS_ENTRIES) {
	      anObject$l(value);
	      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
	    } return INTERRUPTED ? fn(value, stop) : fn(value);
	  };

	  if (IS_RECORD) {
	    iterator = iterable.iterator;
	  } else if (IS_ITERATOR) {
	    iterator = iterable;
	  } else {
	    iterFn = getIteratorMethod$7(iterable);
	    if (!iterFn) throw $TypeError$a(tryToString$2(iterable) + ' is not iterable');
	    // optimisation for array iterators
	    if (isArrayIteratorMethod$1(iterFn)) {
	      for (index = 0, length = lengthOfArrayLike$5(iterable); length > index; index++) {
	        result = callFn(iterable[index]);
	        if (result && isPrototypeOf$j(ResultPrototype, result)) return result;
	      } return new Result(false);
	    }
	    iterator = getIterator$3(iterable, iterFn);
	  }

	  next = IS_RECORD ? iterable.next : iterator.next;
	  while (!(step = call$j(next, iterator)).done) {
	    try {
	      result = callFn(step.value);
	    } catch (error) {
	      iteratorClose$1(iterator, 'throw', error);
	    }
	    if (typeof result == 'object' && result && isPrototypeOf$j(ResultPrototype, result)) return result;
	  } return new Result(false);
	};

	var toString$7 = toString$c;

	var normalizeStringArgument$1 = function (argument, $default) {
	  return argument === undefined ? arguments.length < 2 ? '' : $default : toString$7(argument);
	};

	var fails$h = fails$x;
	var createPropertyDescriptor$1 = createPropertyDescriptor$7;

	var errorStackInstallable = !fails$h(function () {
	  var error = Error('a');
	  if (!('stack' in error)) return true;
	  // eslint-disable-next-line es/no-object-defineproperty -- safe
	  Object.defineProperty(error, 'stack', createPropertyDescriptor$1(1, 7));
	  return error.stack !== 7;
	});

	var $$12 = _export;
	var isPrototypeOf$i = objectIsPrototypeOf;
	var getPrototypeOf$7 = objectGetPrototypeOf;
	var setPrototypeOf$7 = objectSetPrototypeOf;
	var copyConstructorProperties = copyConstructorProperties$1;
	var create$a = objectCreate;
	var createNonEnumerableProperty$1 = createNonEnumerableProperty$8;
	var createPropertyDescriptor = createPropertyDescriptor$7;
	var clearErrorStack = errorStackClear;
	var installErrorCause = installErrorCause$1;
	var iterate$l = iterate$m;
	var normalizeStringArgument = normalizeStringArgument$1;
	var wellKnownSymbol$8 = wellKnownSymbol$o;
	var ERROR_STACK_INSTALLABLE = errorStackInstallable;

	var TO_STRING_TAG = wellKnownSymbol$8('toStringTag');
	var $Error = Error;
	var push$5 = [].push;

	var $AggregateError = function AggregateError(errors, message /* , options */) {
	  var options = arguments.length > 2 ? arguments[2] : undefined;
	  var isInstance = isPrototypeOf$i(AggregateErrorPrototype, this);
	  var that;
	  if (setPrototypeOf$7) {
	    that = setPrototypeOf$7($Error(), isInstance ? getPrototypeOf$7(this) : AggregateErrorPrototype);
	  } else {
	    that = isInstance ? this : create$a(AggregateErrorPrototype);
	    createNonEnumerableProperty$1(that, TO_STRING_TAG, 'Error');
	  }
	  if (message !== undefined) createNonEnumerableProperty$1(that, 'message', normalizeStringArgument(message));
	  if (ERROR_STACK_INSTALLABLE) createNonEnumerableProperty$1(that, 'stack', clearErrorStack(that.stack, 1));
	  installErrorCause(that, options);
	  var errorsArray = [];
	  iterate$l(errors, push$5, { that: errorsArray });
	  createNonEnumerableProperty$1(that, 'errors', errorsArray);
	  return that;
	};

	if (setPrototypeOf$7) setPrototypeOf$7($AggregateError, $Error);
	else copyConstructorProperties($AggregateError, $Error, { name: true });

	var AggregateErrorPrototype = $AggregateError.prototype = create$a($Error.prototype, {
	  constructor: createPropertyDescriptor(1, $AggregateError),
	  message: createPropertyDescriptor(1, ''),
	  name: createPropertyDescriptor(1, 'AggregateError')
	});

	// `AggregateError` constructor
	// https://tc39.es/ecma262/#sec-aggregate-error-constructor
	$$12({ global: true, constructor: true, arity: 2 }, {
	  AggregateError: $AggregateError
	});

	var classof$4 = classofRaw$2;
	var global$g = global$t;

	var engineIsNode = classof$4(global$g.process) == 'process';

	var getBuiltIn$7 = getBuiltIn$h;
	var definePropertyModule = objectDefineProperty;
	var wellKnownSymbol$7 = wellKnownSymbol$o;
	var DESCRIPTORS$8 = descriptors;

	var SPECIES$3 = wellKnownSymbol$7('species');

	var setSpecies$2 = function (CONSTRUCTOR_NAME) {
	  var Constructor = getBuiltIn$7(CONSTRUCTOR_NAME);
	  var defineProperty = definePropertyModule.f;

	  if (DESCRIPTORS$8 && Constructor && !Constructor[SPECIES$3]) {
	    defineProperty(Constructor, SPECIES$3, {
	      configurable: true,
	      get: function () { return this; }
	    });
	  }
	};

	var isPrototypeOf$h = objectIsPrototypeOf;

	var $TypeError$9 = TypeError;

	var anInstance$4 = function (it, Prototype) {
	  if (isPrototypeOf$h(Prototype, it)) return it;
	  throw $TypeError$9('Incorrect invocation');
	};

	var isConstructor$2 = isConstructor$4;
	var tryToString$1 = tryToString$6;

	var $TypeError$8 = TypeError;

	// `Assert: IsConstructor(argument) is true`
	var aConstructor$3 = function (argument) {
	  if (isConstructor$2(argument)) return argument;
	  throw $TypeError$8(tryToString$1(argument) + ' is not a constructor');
	};

	var anObject$k = anObject$u;
	var aConstructor$2 = aConstructor$3;
	var isNullOrUndefined$4 = isNullOrUndefined$8;
	var wellKnownSymbol$6 = wellKnownSymbol$o;

	var SPECIES$2 = wellKnownSymbol$6('species');

	// `SpeciesConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-speciesconstructor
	var speciesConstructor$5 = function (O, defaultConstructor) {
	  var C = anObject$k(O).constructor;
	  var S;
	  return C === undefined || isNullOrUndefined$4(S = anObject$k(C)[SPECIES$2]) ? defaultConstructor : aConstructor$2(S);
	};

	var $TypeError$7 = TypeError;

	var validateArgumentsLength$2 = function (passed, required) {
	  if (passed < required) throw $TypeError$7('Not enough arguments');
	  return passed;
	};

	var userAgent$5 = engineUserAgent;

	var engineIsIos = /(?:ipad|iphone|ipod).*applewebkit/i.test(userAgent$5);

	var global$f = global$t;
	var apply$3 = functionApply;
	var bind$m = functionBindContext;
	var isCallable$6 = isCallable$m;
	var hasOwn$6 = hasOwnProperty_1;
	var fails$g = fails$x;
	var html = html$2;
	var arraySlice$4 = arraySlice$6;
	var createElement = documentCreateElement$1;
	var validateArgumentsLength$1 = validateArgumentsLength$2;
	var IS_IOS$1 = engineIsIos;
	var IS_NODE$3 = engineIsNode;

	var set$3 = global$f.setImmediate;
	var clear = global$f.clearImmediate;
	var process$2 = global$f.process;
	var Dispatch = global$f.Dispatch;
	var Function$2 = global$f.Function;
	var MessageChannel = global$f.MessageChannel;
	var String$1 = global$f.String;
	var counter = 0;
	var queue$1 = {};
	var ONREADYSTATECHANGE = 'onreadystatechange';
	var $location, defer, channel, port;

	try {
	  // Deno throws a ReferenceError on `location` access without `--location` flag
	  $location = global$f.location;
	} catch (error) { /* empty */ }

	var run$1 = function (id) {
	  if (hasOwn$6(queue$1, id)) {
	    var fn = queue$1[id];
	    delete queue$1[id];
	    fn();
	  }
	};

	var runner = function (id) {
	  return function () {
	    run$1(id);
	  };
	};

	var listener = function (event) {
	  run$1(event.data);
	};

	var post = function (id) {
	  // old engines have not location.origin
	  global$f.postMessage(String$1(id), $location.protocol + '//' + $location.host);
	};

	// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
	if (!set$3 || !clear) {
	  set$3 = function setImmediate(handler) {
	    validateArgumentsLength$1(arguments.length, 1);
	    var fn = isCallable$6(handler) ? handler : Function$2(handler);
	    var args = arraySlice$4(arguments, 1);
	    queue$1[++counter] = function () {
	      apply$3(fn, undefined, args);
	    };
	    defer(counter);
	    return counter;
	  };
	  clear = function clearImmediate(id) {
	    delete queue$1[id];
	  };
	  // Node.js 0.8-
	  if (IS_NODE$3) {
	    defer = function (id) {
	      process$2.nextTick(runner(id));
	    };
	  // Sphere (JS game engine) Dispatch API
	  } else if (Dispatch && Dispatch.now) {
	    defer = function (id) {
	      Dispatch.now(runner(id));
	    };
	  // Browsers with MessageChannel, includes WebWorkers
	  // except iOS - https://github.com/zloirock/core-js/issues/624
	  } else if (MessageChannel && !IS_IOS$1) {
	    channel = new MessageChannel();
	    port = channel.port2;
	    channel.port1.onmessage = listener;
	    defer = bind$m(port.postMessage, port);
	  // Browsers with postMessage, skip WebWorkers
	  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
	  } else if (
	    global$f.addEventListener &&
	    isCallable$6(global$f.postMessage) &&
	    !global$f.importScripts &&
	    $location && $location.protocol !== 'file:' &&
	    !fails$g(post)
	  ) {
	    defer = post;
	    global$f.addEventListener('message', listener, false);
	  // IE8-
	  } else if (ONREADYSTATECHANGE in createElement('script')) {
	    defer = function (id) {
	      html.appendChild(createElement('script'))[ONREADYSTATECHANGE] = function () {
	        html.removeChild(this);
	        run$1(id);
	      };
	    };
	  // Rest old browsers
	  } else {
	    defer = function (id) {
	      setTimeout(runner(id), 0);
	    };
	  }
	}

	var task$1 = {
	  set: set$3,
	  clear: clear
	};

	var userAgent$4 = engineUserAgent;
	var global$e = global$t;

	var engineIsIosPebble = /ipad|iphone|ipod/i.test(userAgent$4) && global$e.Pebble !== undefined;

	var userAgent$3 = engineUserAgent;

	var engineIsWebosWebkit = /web0s(?!.*chrome)/i.test(userAgent$3);

	var global$d = global$t;
	var bind$l = functionBindContext;
	var getOwnPropertyDescriptor$8 = objectGetOwnPropertyDescriptor.f;
	var macrotask = task$1.set;
	var IS_IOS = engineIsIos;
	var IS_IOS_PEBBLE = engineIsIosPebble;
	var IS_WEBOS_WEBKIT = engineIsWebosWebkit;
	var IS_NODE$2 = engineIsNode;

	var MutationObserver = global$d.MutationObserver || global$d.WebKitMutationObserver;
	var document$2 = global$d.document;
	var process$1 = global$d.process;
	var Promise$1 = global$d.Promise;
	// Node.js 11 shows ExperimentalWarning on getting `queueMicrotask`
	var queueMicrotaskDescriptor = getOwnPropertyDescriptor$8(global$d, 'queueMicrotask');
	var queueMicrotask = queueMicrotaskDescriptor && queueMicrotaskDescriptor.value;

	var flush$1, head, last, notify$1, toggle, node, promise$3, then;

	// modern engines have queueMicrotask method
	if (!queueMicrotask) {
	  flush$1 = function () {
	    var parent, fn;
	    if (IS_NODE$2 && (parent = process$1.domain)) parent.exit();
	    while (head) {
	      fn = head.fn;
	      head = head.next;
	      try {
	        fn();
	      } catch (error) {
	        if (head) notify$1();
	        else last = undefined;
	        throw error;
	      }
	    } last = undefined;
	    if (parent) parent.enter();
	  };

	  // browsers with MutationObserver, except iOS - https://github.com/zloirock/core-js/issues/339
	  // also except WebOS Webkit https://github.com/zloirock/core-js/issues/898
	  if (!IS_IOS && !IS_NODE$2 && !IS_WEBOS_WEBKIT && MutationObserver && document$2) {
	    toggle = true;
	    node = document$2.createTextNode('');
	    new MutationObserver(flush$1).observe(node, { characterData: true });
	    notify$1 = function () {
	      node.data = toggle = !toggle;
	    };
	  // environments with maybe non-completely correct, but existent Promise
	  } else if (!IS_IOS_PEBBLE && Promise$1 && Promise$1.resolve) {
	    // Promise.resolve without an argument throws an error in LG WebOS 2
	    promise$3 = Promise$1.resolve(undefined);
	    // workaround of WebKit ~ iOS Safari 10.1 bug
	    promise$3.constructor = Promise$1;
	    then = bind$l(promise$3.then, promise$3);
	    notify$1 = function () {
	      then(flush$1);
	    };
	  // Node.js without promises
	  } else if (IS_NODE$2) {
	    notify$1 = function () {
	      process$1.nextTick(flush$1);
	    };
	  // for other environments - macrotask based on:
	  // - setImmediate
	  // - MessageChannel
	  // - window.postMessage
	  // - onreadystatechange
	  // - setTimeout
	  } else {
	    // strange IE + webpack dev server bug - use .bind(global)
	    macrotask = bind$l(macrotask, global$d);
	    notify$1 = function () {
	      macrotask(flush$1);
	    };
	  }
	}

	var microtask$1 = queueMicrotask || function (fn) {
	  var task = { fn: fn, next: undefined };
	  if (last) last.next = task;
	  if (!head) {
	    head = task;
	    notify$1();
	  } last = task;
	};

	var global$c = global$t;

	var hostReportErrors$1 = function (a, b) {
	  var console = global$c.console;
	  if (console && console.error) {
	    arguments.length == 1 ? console.error(a) : console.error(a, b);
	  }
	};

	var perform$5 = function (exec) {
	  try {
	    return { error: false, value: exec() };
	  } catch (error) {
	    return { error: true, value: error };
	  }
	};

	var Queue$1 = function () {
	  this.head = null;
	  this.tail = null;
	};

	Queue$1.prototype = {
	  add: function (item) {
	    var entry = { item: item, next: null };
	    if (this.head) this.tail.next = entry;
	    else this.head = entry;
	    this.tail = entry;
	  },
	  get: function () {
	    var entry = this.head;
	    if (entry) {
	      this.head = entry.next;
	      if (this.tail === entry) this.tail = null;
	      return entry.item;
	    }
	  }
	};

	var queue = Queue$1;

	var global$b = global$t;

	var promiseNativeConstructor = global$b.Promise;

	/* global Deno -- Deno case */

	var engineIsDeno = typeof Deno == 'object' && Deno && typeof Deno.version == 'object';

	var IS_DENO$1 = engineIsDeno;
	var IS_NODE$1 = engineIsNode;

	var engineIsBrowser = !IS_DENO$1 && !IS_NODE$1
	  && typeof window == 'object'
	  && typeof document == 'object';

	var global$a = global$t;
	var NativePromiseConstructor$5 = promiseNativeConstructor;
	var isCallable$5 = isCallable$m;
	var isForced = isForced_1;
	var inspectSource = inspectSource$2;
	var wellKnownSymbol$5 = wellKnownSymbol$o;
	var IS_BROWSER = engineIsBrowser;
	var IS_DENO = engineIsDeno;
	var V8_VERSION = engineV8Version;

	var NativePromisePrototype$2 = NativePromiseConstructor$5 && NativePromiseConstructor$5.prototype;
	var SPECIES$1 = wellKnownSymbol$5('species');
	var SUBCLASSING = false;
	var NATIVE_PROMISE_REJECTION_EVENT$1 = isCallable$5(global$a.PromiseRejectionEvent);

	var FORCED_PROMISE_CONSTRUCTOR$5 = isForced('Promise', function () {
	  var PROMISE_CONSTRUCTOR_SOURCE = inspectSource(NativePromiseConstructor$5);
	  var GLOBAL_CORE_JS_PROMISE = PROMISE_CONSTRUCTOR_SOURCE !== String(NativePromiseConstructor$5);
	  // V8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
	  // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
	  // We can't detect it synchronously, so just check versions
	  if (!GLOBAL_CORE_JS_PROMISE && V8_VERSION === 66) return true;
	  // We need Promise#{ catch, finally } in the pure version for preventing prototype pollution
	  if (!(NativePromisePrototype$2['catch'] && NativePromisePrototype$2['finally'])) return true;
	  // We can't use @@species feature detection in V8 since it causes
	  // deoptimization and performance degradation
	  // https://github.com/zloirock/core-js/issues/679
	  if (!V8_VERSION || V8_VERSION < 51 || !/native code/.test(PROMISE_CONSTRUCTOR_SOURCE)) {
	    // Detect correctness of subclassing with @@species support
	    var promise = new NativePromiseConstructor$5(function (resolve) { resolve(1); });
	    var FakePromise = function (exec) {
	      exec(function () { /* empty */ }, function () { /* empty */ });
	    };
	    var constructor = promise.constructor = {};
	    constructor[SPECIES$1] = FakePromise;
	    SUBCLASSING = promise.then(function () { /* empty */ }) instanceof FakePromise;
	    if (!SUBCLASSING) return true;
	  // Unhandled rejections tracking support, NodeJS Promise without it fails @@species test
	  } return !GLOBAL_CORE_JS_PROMISE && (IS_BROWSER || IS_DENO) && !NATIVE_PROMISE_REJECTION_EVENT$1;
	});

	var promiseConstructorDetection = {
	  CONSTRUCTOR: FORCED_PROMISE_CONSTRUCTOR$5,
	  REJECTION_EVENT: NATIVE_PROMISE_REJECTION_EVENT$1,
	  SUBCLASSING: SUBCLASSING
	};

	var newPromiseCapability$2 = {};

	var aCallable$j = aCallable$n;

	var $TypeError$6 = TypeError;

	var PromiseCapability = function (C) {
	  var resolve, reject;
	  this.promise = new C(function ($$resolve, $$reject) {
	    if (resolve !== undefined || reject !== undefined) throw $TypeError$6('Bad Promise constructor');
	    resolve = $$resolve;
	    reject = $$reject;
	  });
	  this.resolve = aCallable$j(resolve);
	  this.reject = aCallable$j(reject);
	};

	// `NewPromiseCapability` abstract operation
	// https://tc39.es/ecma262/#sec-newpromisecapability
	newPromiseCapability$2.f = function (C) {
	  return new PromiseCapability(C);
	};

	var $$11 = _export;
	var IS_NODE = engineIsNode;
	var global$9 = global$t;
	var call$i = functionCall;
	var defineBuiltIn$1 = defineBuiltIn$6;
	var setToStringTag$1 = setToStringTag$7;
	var setSpecies$1 = setSpecies$2;
	var aCallable$i = aCallable$n;
	var isCallable$4 = isCallable$m;
	var isObject$b = isObject$m;
	var anInstance$3 = anInstance$4;
	var speciesConstructor$4 = speciesConstructor$5;
	var task = task$1.set;
	var microtask = microtask$1;
	var hostReportErrors = hostReportErrors$1;
	var perform$4 = perform$5;
	var Queue = queue;
	var InternalStateModule$3 = internalState;
	var NativePromiseConstructor$4 = promiseNativeConstructor;
	var PromiseConstructorDetection = promiseConstructorDetection;
	var newPromiseCapabilityModule$5 = newPromiseCapability$2;

	var PROMISE = 'Promise';
	var FORCED_PROMISE_CONSTRUCTOR$4 = PromiseConstructorDetection.CONSTRUCTOR;
	var NATIVE_PROMISE_REJECTION_EVENT = PromiseConstructorDetection.REJECTION_EVENT;
	PromiseConstructorDetection.SUBCLASSING;
	var getInternalPromiseState = InternalStateModule$3.getterFor(PROMISE);
	var setInternalState$3 = InternalStateModule$3.set;
	var NativePromisePrototype$1 = NativePromiseConstructor$4 && NativePromiseConstructor$4.prototype;
	var PromiseConstructor = NativePromiseConstructor$4;
	var PromisePrototype = NativePromisePrototype$1;
	var TypeError$1 = global$9.TypeError;
	var document$1 = global$9.document;
	var process = global$9.process;
	var newPromiseCapability$1 = newPromiseCapabilityModule$5.f;
	var newGenericPromiseCapability = newPromiseCapability$1;

	var DISPATCH_EVENT = !!(document$1 && document$1.createEvent && global$9.dispatchEvent);
	var UNHANDLED_REJECTION = 'unhandledrejection';
	var REJECTION_HANDLED = 'rejectionhandled';
	var PENDING = 0;
	var FULFILLED = 1;
	var REJECTED = 2;
	var HANDLED = 1;
	var UNHANDLED = 2;

	var Internal, OwnPromiseCapability, PromiseWrapper;

	// helpers
	var isThenable = function (it) {
	  var then;
	  return isObject$b(it) && isCallable$4(then = it.then) ? then : false;
	};

	var callReaction = function (reaction, state) {
	  var value = state.value;
	  var ok = state.state == FULFILLED;
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
	        reject(TypeError$1('Promise-chain cycle'));
	      } else if (then = isThenable(result)) {
	        call$i(then, result, resolve, reject);
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
	    event = document$1.createEvent('Event');
	    event.promise = promise;
	    event.reason = reason;
	    event.initEvent(name, false, true);
	    global$9.dispatchEvent(event);
	  } else event = { promise: promise, reason: reason };
	  if (!NATIVE_PROMISE_REJECTION_EVENT && (handler = global$9['on' + name])) handler(event);
	  else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
	};

	var onUnhandled = function (state) {
	  call$i(task, global$9, function () {
	    var promise = state.facade;
	    var value = state.value;
	    var IS_UNHANDLED = isUnhandled(state);
	    var result;
	    if (IS_UNHANDLED) {
	      result = perform$4(function () {
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
	  call$i(task, global$9, function () {
	    var promise = state.facade;
	    if (IS_NODE) {
	      process.emit('rejectionHandled', promise);
	    } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
	  });
	};

	var bind$k = function (fn, state, unwrap) {
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
	    if (state.facade === value) throw TypeError$1("Promise can't be resolved itself");
	    var then = isThenable(value);
	    if (then) {
	      microtask(function () {
	        var wrapper = { done: false };
	        try {
	          call$i(then, value,
	            bind$k(internalResolve, wrapper, state),
	            bind$k(internalReject, wrapper, state)
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
	if (FORCED_PROMISE_CONSTRUCTOR$4) {
	  // 25.4.3.1 Promise(executor)
	  PromiseConstructor = function Promise(executor) {
	    anInstance$3(this, PromisePrototype);
	    aCallable$i(executor);
	    call$i(Internal, this);
	    var state = getInternalPromiseState(this);
	    try {
	      executor(bind$k(internalResolve, state), bind$k(internalReject, state));
	    } catch (error) {
	      internalReject(state, error);
	    }
	  };

	  PromisePrototype = PromiseConstructor.prototype;

	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  Internal = function Promise(executor) {
	    setInternalState$3(this, {
	      type: PROMISE,
	      done: false,
	      notified: false,
	      parent: false,
	      reactions: new Queue(),
	      rejection: false,
	      state: PENDING,
	      value: undefined
	    });
	  };

	  // `Promise.prototype.then` method
	  // https://tc39.es/ecma262/#sec-promise.prototype.then
	  Internal.prototype = defineBuiltIn$1(PromisePrototype, 'then', function then(onFulfilled, onRejected) {
	    var state = getInternalPromiseState(this);
	    var reaction = newPromiseCapability$1(speciesConstructor$4(this, PromiseConstructor));
	    state.parent = true;
	    reaction.ok = isCallable$4(onFulfilled) ? onFulfilled : true;
	    reaction.fail = isCallable$4(onRejected) && onRejected;
	    reaction.domain = IS_NODE ? process.domain : undefined;
	    if (state.state == PENDING) state.reactions.add(reaction);
	    else microtask(function () {
	      callReaction(reaction, state);
	    });
	    return reaction.promise;
	  });

	  OwnPromiseCapability = function () {
	    var promise = new Internal();
	    var state = getInternalPromiseState(promise);
	    this.promise = promise;
	    this.resolve = bind$k(internalResolve, state);
	    this.reject = bind$k(internalReject, state);
	  };

	  newPromiseCapabilityModule$5.f = newPromiseCapability$1 = function (C) {
	    return C === PromiseConstructor || C === PromiseWrapper
	      ? new OwnPromiseCapability(C)
	      : newGenericPromiseCapability(C);
	  };
	}

	$$11({ global: true, constructor: true, wrap: true, forced: FORCED_PROMISE_CONSTRUCTOR$4 }, {
	  Promise: PromiseConstructor
	});

	setToStringTag$1(PromiseConstructor, PROMISE, false, true);
	setSpecies$1(PROMISE);

	var wellKnownSymbol$4 = wellKnownSymbol$o;

	var ITERATOR$3 = wellKnownSymbol$4('iterator');
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
	  iteratorWithReturn[ITERATOR$3] = function () {
	    return this;
	  };
	  // eslint-disable-next-line es/no-array-from, no-throw-literal -- required for testing
	  Array.from(iteratorWithReturn, function () { throw 2; });
	} catch (error) { /* empty */ }

	var checkCorrectnessOfIteration$2 = function (exec, SKIP_CLOSING) {
	  if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
	  var ITERATION_SUPPORT = false;
	  try {
	    var object = {};
	    object[ITERATOR$3] = function () {
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

	var NativePromiseConstructor$3 = promiseNativeConstructor;
	var checkCorrectnessOfIteration$1 = checkCorrectnessOfIteration$2;
	var FORCED_PROMISE_CONSTRUCTOR$3 = promiseConstructorDetection.CONSTRUCTOR;

	var promiseStaticsIncorrectIteration = FORCED_PROMISE_CONSTRUCTOR$3 || !checkCorrectnessOfIteration$1(function (iterable) {
	  NativePromiseConstructor$3.all(iterable).then(undefined, function () { /* empty */ });
	});

	var $$10 = _export;
	var call$h = functionCall;
	var aCallable$h = aCallable$n;
	var newPromiseCapabilityModule$4 = newPromiseCapability$2;
	var perform$3 = perform$5;
	var iterate$k = iterate$m;
	var PROMISE_STATICS_INCORRECT_ITERATION$1 = promiseStaticsIncorrectIteration;

	// `Promise.all` method
	// https://tc39.es/ecma262/#sec-promise.all
	$$10({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$1 }, {
	  all: function all(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$4.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform$3(function () {
	      var $promiseResolve = aCallable$h(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate$k(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        remaining++;
	        call$h($promiseResolve, C, promise).then(function (value) {
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

	var $$$ = _export;
	var FORCED_PROMISE_CONSTRUCTOR$2 = promiseConstructorDetection.CONSTRUCTOR;
	var NativePromiseConstructor$2 = promiseNativeConstructor;

	NativePromiseConstructor$2 && NativePromiseConstructor$2.prototype;

	// `Promise.prototype.catch` method
	// https://tc39.es/ecma262/#sec-promise.prototype.catch
	$$$({ target: 'Promise', proto: true, forced: FORCED_PROMISE_CONSTRUCTOR$2, real: true }, {
	  'catch': function (onRejected) {
	    return this.then(undefined, onRejected);
	  }
	});

	var $$_ = _export;
	var call$g = functionCall;
	var aCallable$g = aCallable$n;
	var newPromiseCapabilityModule$3 = newPromiseCapability$2;
	var perform$2 = perform$5;
	var iterate$j = iterate$m;
	var PROMISE_STATICS_INCORRECT_ITERATION = promiseStaticsIncorrectIteration;

	// `Promise.race` method
	// https://tc39.es/ecma262/#sec-promise.race
	$$_({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
	  race: function race(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$3.f(C);
	    var reject = capability.reject;
	    var result = perform$2(function () {
	      var $promiseResolve = aCallable$g(C.resolve);
	      iterate$j(iterable, function (promise) {
	        call$g($promiseResolve, C, promise).then(capability.resolve, reject);
	      });
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	var $$Z = _export;
	var call$f = functionCall;
	var newPromiseCapabilityModule$2 = newPromiseCapability$2;
	var FORCED_PROMISE_CONSTRUCTOR$1 = promiseConstructorDetection.CONSTRUCTOR;

	// `Promise.reject` method
	// https://tc39.es/ecma262/#sec-promise.reject
	$$Z({ target: 'Promise', stat: true, forced: FORCED_PROMISE_CONSTRUCTOR$1 }, {
	  reject: function reject(r) {
	    var capability = newPromiseCapabilityModule$2.f(this);
	    call$f(capability.reject, undefined, r);
	    return capability.promise;
	  }
	});

	var anObject$j = anObject$u;
	var isObject$a = isObject$m;
	var newPromiseCapability = newPromiseCapability$2;

	var promiseResolve$2 = function (C, x) {
	  anObject$j(C);
	  if (isObject$a(x) && x.constructor === C) return x;
	  var promiseCapability = newPromiseCapability.f(C);
	  var resolve = promiseCapability.resolve;
	  resolve(x);
	  return promiseCapability.promise;
	};

	var $$Y = _export;
	var getBuiltIn$6 = getBuiltIn$h;
	var IS_PURE = isPure;
	var NativePromiseConstructor$1 = promiseNativeConstructor;
	var FORCED_PROMISE_CONSTRUCTOR = promiseConstructorDetection.CONSTRUCTOR;
	var promiseResolve$1 = promiseResolve$2;

	var PromiseConstructorWrapper = getBuiltIn$6('Promise');
	var CHECK_WRAPPER = !FORCED_PROMISE_CONSTRUCTOR;

	// `Promise.resolve` method
	// https://tc39.es/ecma262/#sec-promise.resolve
	$$Y({ target: 'Promise', stat: true, forced: IS_PURE  }, {
	  resolve: function resolve(x) {
	    return promiseResolve$1(CHECK_WRAPPER && this === PromiseConstructorWrapper ? NativePromiseConstructor$1 : this, x);
	  }
	});

	var $$X = _export;
	var call$e = functionCall;
	var aCallable$f = aCallable$n;
	var newPromiseCapabilityModule$1 = newPromiseCapability$2;
	var perform$1 = perform$5;
	var iterate$i = iterate$m;

	// `Promise.allSettled` method
	// https://tc39.es/ecma262/#sec-promise.allsettled
	$$X({ target: 'Promise', stat: true }, {
	  allSettled: function allSettled(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$1.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform$1(function () {
	      var promiseResolve = aCallable$f(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate$i(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        remaining++;
	        call$e(promiseResolve, C, promise).then(function (value) {
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

	var $$W = _export;
	var call$d = functionCall;
	var aCallable$e = aCallable$n;
	var getBuiltIn$5 = getBuiltIn$h;
	var newPromiseCapabilityModule = newPromiseCapability$2;
	var perform = perform$5;
	var iterate$h = iterate$m;

	var PROMISE_ANY_ERROR = 'No one promise resolved';

	// `Promise.any` method
	// https://tc39.es/ecma262/#sec-promise.any
	$$W({ target: 'Promise', stat: true }, {
	  any: function any(iterable) {
	    var C = this;
	    var AggregateError = getBuiltIn$5('AggregateError');
	    var capability = newPromiseCapabilityModule.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform(function () {
	      var promiseResolve = aCallable$e(C.resolve);
	      var errors = [];
	      var counter = 0;
	      var remaining = 1;
	      var alreadyResolved = false;
	      iterate$h(iterable, function (promise) {
	        var index = counter++;
	        var alreadyRejected = false;
	        remaining++;
	        call$d(promiseResolve, C, promise).then(function (value) {
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

	var $$V = _export;
	var NativePromiseConstructor = promiseNativeConstructor;
	var fails$f = fails$x;
	var getBuiltIn$4 = getBuiltIn$h;
	var isCallable$3 = isCallable$m;
	var speciesConstructor$3 = speciesConstructor$5;
	var promiseResolve = promiseResolve$2;

	var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;

	// Safari bug https://bugs.webkit.org/show_bug.cgi?id=200829
	var NON_GENERIC = !!NativePromiseConstructor && fails$f(function () {
	  // eslint-disable-next-line unicorn/no-thenable -- required for testing
	  NativePromisePrototype['finally'].call({ then: function () { /* empty */ } }, function () { /* empty */ });
	});

	// `Promise.prototype.finally` method
	// https://tc39.es/ecma262/#sec-promise.prototype.finally
	$$V({ target: 'Promise', proto: true, real: true, forced: NON_GENERIC }, {
	  'finally': function (onFinally) {
	    var C = speciesConstructor$3(this, getBuiltIn$4('Promise'));
	    var isFunction = isCallable$3(onFinally);
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

	var path$m = path$s;

	var promise$2 = path$m.Promise;

	var parent$16 = promise$2;


	var promise$1 = parent$16;

	(function (module) {
		module.exports = promise$1;
	} (promise$4));

	var _Promise = /*@__PURE__*/getDefaultExportFromCjs(promise$4.exports);

	var _parseFloat$3 = {exports: {}};

	// a string of all valid unicode whitespaces
	var whitespaces$4 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
	  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

	var uncurryThis$f = functionUncurryThis;
	var requireObjectCoercible$3 = requireObjectCoercible$7;
	var toString$6 = toString$c;
	var whitespaces$3 = whitespaces$4;

	var replace = uncurryThis$f(''.replace);
	var whitespace = '[' + whitespaces$3 + ']';
	var ltrim = RegExp('^' + whitespace + whitespace + '*');
	var rtrim = RegExp(whitespace + whitespace + '*$');

	// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
	var createMethod$1 = function (TYPE) {
	  return function ($this) {
	    var string = toString$6(requireObjectCoercible$3($this));
	    if (TYPE & 1) string = replace(string, ltrim, '');
	    if (TYPE & 2) string = replace(string, rtrim, '');
	    return string;
	  };
	};

	var stringTrim = {
	  // `String.prototype.{ trimLeft, trimStart }` methods
	  // https://tc39.es/ecma262/#sec-string.prototype.trimstart
	  start: createMethod$1(1),
	  // `String.prototype.{ trimRight, trimEnd }` methods
	  // https://tc39.es/ecma262/#sec-string.prototype.trimend
	  end: createMethod$1(2),
	  // `String.prototype.trim` method
	  // https://tc39.es/ecma262/#sec-string.prototype.trim
	  trim: createMethod$1(3)
	};

	var global$8 = global$t;
	var fails$e = fails$x;
	var uncurryThis$e = functionUncurryThis;
	var toString$5 = toString$c;
	var trim$5 = stringTrim.trim;
	var whitespaces$2 = whitespaces$4;

	var charAt = uncurryThis$e(''.charAt);
	var $parseFloat$1 = global$8.parseFloat;
	var Symbol$2 = global$8.Symbol;
	var ITERATOR$2 = Symbol$2 && Symbol$2.iterator;
	var FORCED$4 = 1 / $parseFloat$1(whitespaces$2 + '-0') !== -Infinity
	  // MS Edge 18- broken with boxed symbols
	  || (ITERATOR$2 && !fails$e(function () { $parseFloat$1(Object(ITERATOR$2)); }));

	// `parseFloat` method
	// https://tc39.es/ecma262/#sec-parsefloat-string
	var numberParseFloat = FORCED$4 ? function parseFloat(string) {
	  var trimmedString = trim$5(toString$5(string));
	  var result = $parseFloat$1(trimmedString);
	  return result === 0 && charAt(trimmedString, 0) == '-' ? -0 : result;
	} : $parseFloat$1;

	var $$U = _export;
	var $parseFloat = numberParseFloat;

	// `parseFloat` method
	// https://tc39.es/ecma262/#sec-parsefloat-string
	$$U({ global: true, forced: parseFloat != $parseFloat }, {
	  parseFloat: $parseFloat
	});

	var path$l = path$s;

	var _parseFloat$2 = path$l.parseFloat;

	var parent$15 = _parseFloat$2;

	var _parseFloat$1 = parent$15;

	(function (module) {
		module.exports = _parseFloat$1;
	} (_parseFloat$3));

	var _parseFloat = /*@__PURE__*/getDefaultExportFromCjs(_parseFloat$3.exports);

	var _parseInt$3 = {exports: {}};

	var global$7 = global$t;
	var fails$d = fails$x;
	var uncurryThis$d = functionUncurryThis;
	var toString$4 = toString$c;
	var trim$4 = stringTrim.trim;
	var whitespaces$1 = whitespaces$4;

	var $parseInt$1 = global$7.parseInt;
	var Symbol$1 = global$7.Symbol;
	var ITERATOR$1 = Symbol$1 && Symbol$1.iterator;
	var hex = /^[+-]?0x/i;
	var exec = uncurryThis$d(hex.exec);
	var FORCED$3 = $parseInt$1(whitespaces$1 + '08') !== 8 || $parseInt$1(whitespaces$1 + '0x16') !== 22
	  // MS Edge 18- broken with boxed symbols
	  || (ITERATOR$1 && !fails$d(function () { $parseInt$1(Object(ITERATOR$1)); }));

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	var numberParseInt = FORCED$3 ? function parseInt(string, radix) {
	  var S = trim$4(toString$4(string));
	  return $parseInt$1(S, (radix >>> 0) || (exec(hex, S) ? 16 : 10));
	} : $parseInt$1;

	var $$T = _export;
	var $parseInt = numberParseInt;

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	$$T({ global: true, forced: parseInt != $parseInt }, {
	  parseInt: $parseInt
	});

	var path$k = path$s;

	var _parseInt$2 = path$k.parseInt;

	var parent$14 = _parseInt$2;

	var _parseInt$1 = parent$14;

	(function (module) {
		module.exports = _parseInt$1;
	} (_parseInt$3));

	var _parseInt = /*@__PURE__*/getDefaultExportFromCjs(_parseInt$3.exports);

	var slice$7 = {exports: {}};

	var $$S = _export;
	var isArray$a = isArray$e;
	var isConstructor$1 = isConstructor$4;
	var isObject$9 = isObject$m;
	var toAbsoluteIndex$2 = toAbsoluteIndex$5;
	var lengthOfArrayLike$4 = lengthOfArrayLike$a;
	var toIndexedObject$3 = toIndexedObject$b;
	var createProperty$3 = createProperty$6;
	var wellKnownSymbol$3 = wellKnownSymbol$o;
	var arrayMethodHasSpeciesSupport$3 = arrayMethodHasSpeciesSupport$5;
	var nativeSlice = arraySlice$6;

	var HAS_SPECIES_SUPPORT$3 = arrayMethodHasSpeciesSupport$3('slice');

	var SPECIES = wellKnownSymbol$3('species');
	var $Array$1 = Array;
	var max$1 = Math.max;

	// `Array.prototype.slice` method
	// https://tc39.es/ecma262/#sec-array.prototype.slice
	// fallback for not array-like ES3 strings and DOM objects
	$$S({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$3 }, {
	  slice: function slice(start, end) {
	    var O = toIndexedObject$3(this);
	    var length = lengthOfArrayLike$4(O);
	    var k = toAbsoluteIndex$2(start, length);
	    var fin = toAbsoluteIndex$2(end === undefined ? length : end, length);
	    // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
	    var Constructor, result, n;
	    if (isArray$a(O)) {
	      Constructor = O.constructor;
	      // cross-realm fallback
	      if (isConstructor$1(Constructor) && (Constructor === $Array$1 || isArray$a(Constructor.prototype))) {
	        Constructor = undefined;
	      } else if (isObject$9(Constructor)) {
	        Constructor = Constructor[SPECIES];
	        if (Constructor === null) Constructor = undefined;
	      }
	      if (Constructor === $Array$1 || Constructor === undefined) {
	        return nativeSlice(O, k, fin);
	      }
	    }
	    result = new (Constructor === undefined ? $Array$1 : Constructor)(max$1(fin - k, 0));
	    for (n = 0; k < fin; k++, n++) if (k in O) createProperty$3(result, n, O[k]);
	    result.length = n;
	    return result;
	  }
	});

	var path$j = path$s;

	var entryVirtual$i = function (CONSTRUCTOR) {
	  return path$j[CONSTRUCTOR + 'Prototype'];
	};

	var entryVirtual$h = entryVirtual$i;

	var slice$6 = entryVirtual$h('Array').slice;

	var isPrototypeOf$g = objectIsPrototypeOf;
	var method$f = slice$6;

	var ArrayPrototype$c = Array.prototype;

	var slice$5 = function (it) {
	  var own = it.slice;
	  return it === ArrayPrototype$c || (isPrototypeOf$g(ArrayPrototype$c, it) && own === ArrayPrototype$c.slice) ? method$f : own;
	};

	var parent$13 = slice$5;

	var slice$4 = parent$13;

	(function (module) {
		module.exports = slice$4;
	} (slice$7));

	var _sliceInstanceProperty$1 = /*@__PURE__*/getDefaultExportFromCjs(slice$7.exports);

	var setTimeout$3 = {exports: {}};

	var global$6 = global$t;
	var apply$2 = functionApply;
	var isCallable$2 = isCallable$m;
	var userAgent$2 = engineUserAgent;
	var arraySlice$3 = arraySlice$6;
	var validateArgumentsLength = validateArgumentsLength$2;

	var MSIE = /MSIE .\./.test(userAgent$2); // <- dirty ie9- check
	var Function$1 = global$6.Function;

	var wrap = function (scheduler) {
	  return MSIE ? function (handler, timeout /* , ...arguments */) {
	    var boundArgs = validateArgumentsLength(arguments.length, 1) > 2;
	    var fn = isCallable$2(handler) ? handler : Function$1(handler);
	    var args = boundArgs ? arraySlice$3(arguments, 2) : undefined;
	    return scheduler(boundArgs ? function () {
	      apply$2(fn, this, args);
	    } : fn, timeout);
	  } : scheduler;
	};

	// ie9- setTimeout & setInterval additional parameters fix
	// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#timers
	var schedulersFix = {
	  // `setTimeout` method
	  // https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-settimeout
	  setTimeout: wrap(global$6.setTimeout),
	  // `setInterval` method
	  // https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-setinterval
	  setInterval: wrap(global$6.setInterval)
	};

	var $$R = _export;
	var global$5 = global$t;
	var setInterval = schedulersFix.setInterval;

	// ie9- setInterval additional parameters fix
	// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-setinterval
	$$R({ global: true, bind: true, forced: global$5.setInterval !== setInterval }, {
	  setInterval: setInterval
	});

	var $$Q = _export;
	var global$4 = global$t;
	var setTimeout$2 = schedulersFix.setTimeout;

	// ie9- setTimeout additional parameters fix
	// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-settimeout
	$$Q({ global: true, bind: true, forced: global$4.setTimeout !== setTimeout$2 }, {
	  setTimeout: setTimeout$2
	});

	var path$i = path$s;

	var setTimeout$1 = path$i.setTimeout;

	(function (module) {
		module.exports = setTimeout$1;
	} (setTimeout$3));

	var _setTimeout = /*@__PURE__*/getDefaultExportFromCjs(setTimeout$3.exports);

	var weakMap$2 = {exports: {}};

	var defineBuiltIn = defineBuiltIn$6;

	var defineBuiltIns$3 = function (target, src, options) {
	  for (var key in src) {
	    if (options && options.unsafe && target[key]) target[key] = src[key];
	    else defineBuiltIn(target, key, src[key], options);
	  } return target;
	};

	var internalMetadata = {exports: {}};

	// FF26- bug: ArrayBuffers are non-extensible, but Object.isExtensible does not report it
	var fails$c = fails$x;

	var arrayBufferNonExtensible = fails$c(function () {
	  if (typeof ArrayBuffer == 'function') {
	    var buffer = new ArrayBuffer(8);
	    // eslint-disable-next-line es/no-object-isextensible, es/no-object-defineproperty -- safe
	    if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
	  }
	});

	var fails$b = fails$x;
	var isObject$8 = isObject$m;
	var classof$3 = classofRaw$2;
	var ARRAY_BUFFER_NON_EXTENSIBLE = arrayBufferNonExtensible;

	// eslint-disable-next-line es/no-object-isextensible -- safe
	var $isExtensible = Object.isExtensible;
	var FAILS_ON_PRIMITIVES$3 = fails$b(function () { $isExtensible(1); });

	// `Object.isExtensible` method
	// https://tc39.es/ecma262/#sec-object.isextensible
	var objectIsExtensible = (FAILS_ON_PRIMITIVES$3 || ARRAY_BUFFER_NON_EXTENSIBLE) ? function isExtensible(it) {
	  if (!isObject$8(it)) return false;
	  if (ARRAY_BUFFER_NON_EXTENSIBLE && classof$3(it) == 'ArrayBuffer') return false;
	  return $isExtensible ? $isExtensible(it) : true;
	} : $isExtensible;

	var fails$a = fails$x;

	var freezing = !fails$a(function () {
	  // eslint-disable-next-line es/no-object-isextensible, es/no-object-preventextensions -- required for testing
	  return Object.isExtensible(Object.preventExtensions({}));
	});

	var $$P = _export;
	var uncurryThis$c = functionUncurryThis;
	var hiddenKeys = hiddenKeys$6;
	var isObject$7 = isObject$m;
	var hasOwn$5 = hasOwnProperty_1;
	var defineProperty$3 = objectDefineProperty.f;
	var getOwnPropertyNamesModule = objectGetOwnPropertyNames;
	var getOwnPropertyNamesExternalModule = objectGetOwnPropertyNamesExternal;
	var isExtensible$1 = objectIsExtensible;
	var uid = uid$4;
	var FREEZING = freezing;

	var REQUIRED = false;
	var METADATA = uid('meta');
	var id$1 = 0;

	var setMetadata = function (it) {
	  defineProperty$3(it, METADATA, { value: {
	    objectID: 'O' + id$1++, // object ID
	    weakData: {}          // weak collections IDs
	  } });
	};

	var fastKey$1 = function (it, create) {
	  // return a primitive with prefix
	  if (!isObject$7(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
	  if (!hasOwn$5(it, METADATA)) {
	    // can't set metadata to uncaught frozen object
	    if (!isExtensible$1(it)) return 'F';
	    // not necessary to add metadata
	    if (!create) return 'E';
	    // add missing metadata
	    setMetadata(it);
	  // return object ID
	  } return it[METADATA].objectID;
	};

	var getWeakData$1 = function (it, create) {
	  if (!hasOwn$5(it, METADATA)) {
	    // can't set metadata to uncaught frozen object
	    if (!isExtensible$1(it)) return true;
	    // not necessary to add metadata
	    if (!create) return false;
	    // add missing metadata
	    setMetadata(it);
	  // return the store of weak collections IDs
	  } return it[METADATA].weakData;
	};

	// add metadata on freeze-family methods calling
	var onFreeze = function (it) {
	  if (FREEZING && REQUIRED && isExtensible$1(it) && !hasOwn$5(it, METADATA)) setMetadata(it);
	  return it;
	};

	var enable = function () {
	  meta.enable = function () { /* empty */ };
	  REQUIRED = true;
	  var getOwnPropertyNames = getOwnPropertyNamesModule.f;
	  var splice = uncurryThis$c([].splice);
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

	    $$P({ target: 'Object', stat: true, forced: true }, {
	      getOwnPropertyNames: getOwnPropertyNamesExternalModule.f
	    });
	  }
	};

	var meta = internalMetadata.exports = {
	  enable: enable,
	  fastKey: fastKey$1,
	  getWeakData: getWeakData$1,
	  onFreeze: onFreeze
	};

	hiddenKeys[METADATA] = true;

	var $$O = _export;
	var global$3 = global$t;
	var InternalMetadataModule$1 = internalMetadata.exports;
	var fails$9 = fails$x;
	var createNonEnumerableProperty = createNonEnumerableProperty$8;
	var iterate$g = iterate$m;
	var anInstance$2 = anInstance$4;
	var isCallable$1 = isCallable$m;
	var isObject$6 = isObject$m;
	var setToStringTag = setToStringTag$7;
	var defineProperty$2 = objectDefineProperty.f;
	var forEach$5 = arrayIteration.forEach;
	var DESCRIPTORS$7 = descriptors;
	var InternalStateModule$2 = internalState;

	var setInternalState$2 = InternalStateModule$2.set;
	var internalStateGetterFor$2 = InternalStateModule$2.getterFor;

	var collection$3 = function (CONSTRUCTOR_NAME, wrapper, common) {
	  var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
	  var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
	  var ADDER = IS_MAP ? 'set' : 'add';
	  var NativeConstructor = global$3[CONSTRUCTOR_NAME];
	  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
	  var exported = {};
	  var Constructor;

	  if (!DESCRIPTORS$7 || !isCallable$1(NativeConstructor)
	    || !(IS_WEAK || NativePrototype.forEach && !fails$9(function () { new NativeConstructor().entries().next(); }))
	  ) {
	    // create collection constructor
	    Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
	    InternalMetadataModule$1.enable();
	  } else {
	    Constructor = wrapper(function (target, iterable) {
	      setInternalState$2(anInstance$2(target, Prototype), {
	        type: CONSTRUCTOR_NAME,
	        collection: new NativeConstructor()
	      });
	      if (iterable != undefined) iterate$g(iterable, target[ADDER], { that: target, AS_ENTRIES: IS_MAP });
	    });

	    var Prototype = Constructor.prototype;

	    var getInternalState = internalStateGetterFor$2(CONSTRUCTOR_NAME);

	    forEach$5(['add', 'clear', 'delete', 'forEach', 'get', 'has', 'set', 'keys', 'values', 'entries'], function (KEY) {
	      var IS_ADDER = KEY == 'add' || KEY == 'set';
	      if (KEY in NativePrototype && !(IS_WEAK && KEY == 'clear')) {
	        createNonEnumerableProperty(Prototype, KEY, function (a, b) {
	          var collection = getInternalState(this).collection;
	          if (!IS_ADDER && IS_WEAK && !isObject$6(a)) return KEY == 'get' ? undefined : false;
	          var result = collection[KEY](a === 0 ? 0 : a, b);
	          return IS_ADDER ? this : result;
	        });
	      }
	    });

	    IS_WEAK || defineProperty$2(Prototype, 'size', {
	      configurable: true,
	      get: function () {
	        return getInternalState(this).collection.size;
	      }
	    });
	  }

	  setToStringTag(Constructor, CONSTRUCTOR_NAME, false, true);

	  exported[CONSTRUCTOR_NAME] = Constructor;
	  $$O({ global: true, forced: true }, exported);

	  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

	  return Constructor;
	};

	var uncurryThis$b = functionUncurryThis;
	var defineBuiltIns$2 = defineBuiltIns$3;
	var getWeakData = internalMetadata.exports.getWeakData;
	var anInstance$1 = anInstance$4;
	var anObject$i = anObject$u;
	var isNullOrUndefined$3 = isNullOrUndefined$8;
	var isObject$5 = isObject$m;
	var iterate$f = iterate$m;
	var ArrayIterationModule = arrayIteration;
	var hasOwn$4 = hasOwnProperty_1;
	var InternalStateModule$1 = internalState;

	var setInternalState$1 = InternalStateModule$1.set;
	var internalStateGetterFor$1 = InternalStateModule$1.getterFor;
	var find$4 = ArrayIterationModule.find;
	var findIndex = ArrayIterationModule.findIndex;
	var splice$4 = uncurryThis$b([].splice);
	var id = 0;

	// fallback for uncaught frozen keys
	var uncaughtFrozenStore = function (store) {
	  return store.frozen || (store.frozen = new UncaughtFrozenStore());
	};

	var UncaughtFrozenStore = function () {
	  this.entries = [];
	};

	var findUncaughtFrozen = function (store, key) {
	  return find$4(store.entries, function (it) {
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
	    if (~index) splice$4(this.entries, index, 1);
	    return !!~index;
	  }
	};

	var collectionWeak$1 = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var Constructor = wrapper(function (that, iterable) {
	      anInstance$1(that, Prototype);
	      setInternalState$1(that, {
	        type: CONSTRUCTOR_NAME,
	        id: id++,
	        frozen: undefined
	      });
	      if (!isNullOrUndefined$3(iterable)) iterate$f(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
	    });

	    var Prototype = Constructor.prototype;

	    var getInternalState = internalStateGetterFor$1(CONSTRUCTOR_NAME);

	    var define = function (that, key, value) {
	      var state = getInternalState(that);
	      var data = getWeakData(anObject$i(key), true);
	      if (data === true) uncaughtFrozenStore(state).set(key, value);
	      else data[state.id] = value;
	      return that;
	    };

	    defineBuiltIns$2(Prototype, {
	      // `{ WeakMap, WeakSet }.prototype.delete(key)` methods
	      // https://tc39.es/ecma262/#sec-weakmap.prototype.delete
	      // https://tc39.es/ecma262/#sec-weakset.prototype.delete
	      'delete': function (key) {
	        var state = getInternalState(this);
	        if (!isObject$5(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state)['delete'](key);
	        return data && hasOwn$4(data, state.id) && delete data[state.id];
	      },
	      // `{ WeakMap, WeakSet }.prototype.has(key)` methods
	      // https://tc39.es/ecma262/#sec-weakmap.prototype.has
	      // https://tc39.es/ecma262/#sec-weakset.prototype.has
	      has: function has(key) {
	        var state = getInternalState(this);
	        if (!isObject$5(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state).has(key);
	        return data && hasOwn$4(data, state.id);
	      }
	    });

	    defineBuiltIns$2(Prototype, IS_MAP ? {
	      // `WeakMap.prototype.get(key)` method
	      // https://tc39.es/ecma262/#sec-weakmap.prototype.get
	      get: function get(key) {
	        var state = getInternalState(this);
	        if (isObject$5(key)) {
	          var data = getWeakData(key);
	          if (data === true) return uncaughtFrozenStore(state).get(key);
	          return data ? data[state.id] : undefined;
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

	var global$2 = global$t;
	var uncurryThis$a = functionUncurryThis;
	var defineBuiltIns$1 = defineBuiltIns$3;
	var InternalMetadataModule = internalMetadata.exports;
	var collection$2 = collection$3;
	var collectionWeak = collectionWeak$1;
	var isObject$4 = isObject$m;
	var isExtensible = objectIsExtensible;
	var enforceInternalState = internalState.enforce;
	var NATIVE_WEAK_MAP = weakMapBasicDetection;

	var IS_IE11 = !global$2.ActiveXObject && 'ActiveXObject' in global$2;
	var InternalWeakMap;

	var wrapper = function (init) {
	  return function WeakMap() {
	    return init(this, arguments.length ? arguments[0] : undefined);
	  };
	};

	// `WeakMap` constructor
	// https://tc39.es/ecma262/#sec-weakmap-constructor
	var $WeakMap = collection$2('WeakMap', wrapper, collectionWeak);

	// IE11 WeakMap frozen keys fix
	// We can't use feature detection because it crash some old IE builds
	// https://github.com/zloirock/core-js/issues/485
	if (NATIVE_WEAK_MAP && IS_IE11) {
	  InternalWeakMap = collectionWeak.getConstructor(wrapper, 'WeakMap', true);
	  InternalMetadataModule.enable();
	  var WeakMapPrototype = $WeakMap.prototype;
	  var nativeDelete = uncurryThis$a(WeakMapPrototype['delete']);
	  var nativeHas = uncurryThis$a(WeakMapPrototype.has);
	  var nativeGet = uncurryThis$a(WeakMapPrototype.get);
	  var nativeSet = uncurryThis$a(WeakMapPrototype.set);
	  defineBuiltIns$1(WeakMapPrototype, {
	    'delete': function (key) {
	      if (isObject$4(key) && !isExtensible(key)) {
	        var state = enforceInternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeDelete(this, key) || state.frozen['delete'](key);
	      } return nativeDelete(this, key);
	    },
	    has: function has(key) {
	      if (isObject$4(key) && !isExtensible(key)) {
	        var state = enforceInternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeHas(this, key) || state.frozen.has(key);
	      } return nativeHas(this, key);
	    },
	    get: function get(key) {
	      if (isObject$4(key) && !isExtensible(key)) {
	        var state = enforceInternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeHas(this, key) ? nativeGet(this, key) : state.frozen.get(key);
	      } return nativeGet(this, key);
	    },
	    set: function set(key, value) {
	      if (isObject$4(key) && !isExtensible(key)) {
	        var state = enforceInternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        nativeHas(this, key) ? nativeSet(this, key, value) : state.frozen.set(key, value);
	      } else nativeSet(this, key, value);
	      return this;
	    }
	  });
	}

	var path$h = path$s;

	var weakMap$1 = path$h.WeakMap;

	var parent$12 = weakMap$1;


	var weakMap = parent$12;

	(function (module) {
		module.exports = weakMap;
	} (weakMap$2));

	var _WeakMap = /*@__PURE__*/getDefaultExportFromCjs(weakMap$2.exports);

	function _classPrivateFieldInitSpec(obj, privateMap, value) { _checkPrivateRedeclaration(obj, privateMap); privateMap.set(obj, value); }
	function _checkPrivateRedeclaration(obj, privateCollection) { if (privateCollection.has(obj)) { throw new TypeError("Cannot initialize the same private elements twice on an object"); } }
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
	      var scrollTop = $__default["default"](window).scrollTop();
	      if (elemTop < $__default["default"](window).scrollTop() || elemTop > scrollTop + window.innerHeight) {
	        $__default["default"]('html,body').animate({
	          scrollTop: elemTop - 50
	        }, 500);
	      }
	    }
	  } else {
	    opt[formId].scroll = true;
	  }
	}
	function requestCancellable() {
	  var request = {
	    xhr: null,
	    booklyAjax: function booklyAjax() {},
	    cancel: function cancel() {}
	  };
	  request.booklyAjax = function (options) {
	    return new _Promise(function (resolve, reject) {
	      request.cancel = function () {
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
	  return new _Promise(function (resolve, reject) {
	    ajax(options, resolve, reject);
	  });
	}
	var _w = /*#__PURE__*/new _WeakMap();
	var Format = /*#__PURE__*/function () {
	  function Format(w) {
	    _classCallCheck(this, Format);
	    _classPrivateFieldInitSpec(this, _w, {
	      writable: true,
	      value: void 0
	    });
	    _classPrivateFieldSet(this, _w, w);
	  }
	  _createClass(Format, [{
	    key: "price",
	    value: function price(amount) {
	      var result = _classPrivateFieldGet(this, _w).format_price.format;
	      amount = _parseFloat(amount);
	      result = result.replace('{sign}', amount < 0 ? '-' : '');
	      result = result.replace('{price}', this._formatNumber(Math.abs(amount), _classPrivateFieldGet(this, _w).format_price.decimals, _classPrivateFieldGet(this, _w).format_price.decimal_separator, _classPrivateFieldGet(this, _w).format_price.thousands_separator));
	      return result;
	    }
	  }, {
	    key: "_formatNumber",
	    value: function _formatNumber(n, c, d, t) {
	      var _context;
	      n = Math.abs(Number(n) || 0).toFixed(c);
	      c = isNaN(c = Math.abs(c)) ? 2 : c;
	      d = d === undefined ? '.' : d;
	      t = t === undefined ? ',' : t;
	      var s = n < 0 ? '-' : '',
	        i = String(_parseInt(n)),
	        j = i.length > 3 ? i.length % 3 : 0;
	      return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + _sliceInstanceProperty$1(_context = Math.abs(n - i).toFixed(c)).call(_context, 2) : '');
	    }
	  }]);
	  return Format;
	}();
	function ajax(options, resolve, reject) {
	  options.data.csrf_token = BooklyL10n.csrf_token;
	  return $__default["default"].ajax(jQuery.extend({
	    url: BooklyL10n.ajaxurl,
	    dataType: 'json',
	    xhrFields: {
	      withCredentials: true
	    },
	    crossDomain: 'withCredentials' in new XMLHttpRequest(),
	    beforeSend: function beforeSend(jqXHR, settings) {}
	  }, options)).always(function (response) {
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
	  if (!response.success && (response === null || response === void 0 ? void 0 : response.error) === 'session_error') {
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

	var find$3 = {exports: {}};

	var $$N = _export;
	var $find = arrayIteration.find;

	var FIND = 'find';
	var SKIPS_HOLES = true;

	// Shouldn't skip holes
	if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

	// `Array.prototype.find` method
	// https://tc39.es/ecma262/#sec-array.prototype.find
	$$N({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
	  find: function find(callbackfn /* , that = undefined */) {
	    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$g = entryVirtual$i;

	var find$2 = entryVirtual$g('Array').find;

	var isPrototypeOf$f = objectIsPrototypeOf;
	var method$e = find$2;

	var ArrayPrototype$b = Array.prototype;

	var find$1 = function (it) {
	  var own = it.find;
	  return it === ArrayPrototype$b || (isPrototypeOf$f(ArrayPrototype$b, it) && own === ArrayPrototype$b.find) ? method$e : own;
	};

	var parent$11 = find$1;

	var find = parent$11;

	(function (module) {
		module.exports = find;
	} (find$3));

	var _findInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(find$3.exports);

	var stringify$2 = {exports: {}};

	var path$g = path$s;
	var apply$1 = functionApply;

	// eslint-disable-next-line es/no-json -- safe
	if (!path$g.JSON) path$g.JSON = { stringify: JSON.stringify };

	// eslint-disable-next-line no-unused-vars -- required for `.length`
	var stringify$1 = function stringify(it, replacer, space) {
	  return apply$1(path$g.JSON.stringify, null, arguments);
	};

	var parent$10 = stringify$1;

	var stringify = parent$10;

	(function (module) {
		module.exports = stringify;
	} (stringify$2));

	var _JSON$stringify = /*@__PURE__*/getDefaultExportFromCjs(stringify$2.exports);

	var repeat$4 = {exports: {}};

	var toIntegerOrInfinity$1 = toIntegerOrInfinity$5;
	var toString$3 = toString$c;
	var requireObjectCoercible$2 = requireObjectCoercible$7;

	var $RangeError = RangeError;

	// `String.prototype.repeat` method implementation
	// https://tc39.es/ecma262/#sec-string.prototype.repeat
	var stringRepeat = function repeat(count) {
	  var str = toString$3(requireObjectCoercible$2(this));
	  var result = '';
	  var n = toIntegerOrInfinity$1(count);
	  if (n < 0 || n == Infinity) throw $RangeError('Wrong number of repetitions');
	  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
	  return result;
	};

	var $$M = _export;
	var repeat$3 = stringRepeat;

	// `String.prototype.repeat` method
	// https://tc39.es/ecma262/#sec-string.prototype.repeat
	$$M({ target: 'String', proto: true }, {
	  repeat: repeat$3
	});

	var entryVirtual$f = entryVirtual$i;

	var repeat$2 = entryVirtual$f('String').repeat;

	var isPrototypeOf$e = objectIsPrototypeOf;
	var method$d = repeat$2;

	var StringPrototype$3 = String.prototype;

	var repeat$1 = function (it) {
	  var own = it.repeat;
	  return typeof it == 'string' || it === StringPrototype$3
	    || (isPrototypeOf$e(StringPrototype$3, it) && own === StringPrototype$3.repeat) ? method$d : own;
	};

	var parent$$ = repeat$1;

	var repeat = parent$$;

	(function (module) {
		module.exports = repeat;
	} (repeat$4));

	var _repeatInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(repeat$4.exports);

	var forEach$4 = {exports: {}};

	var fails$8 = fails$x;

	var arrayMethodIsStrict$4 = function (METHOD_NAME, argument) {
	  var method = [][METHOD_NAME];
	  return !!method && fails$8(function () {
	    // eslint-disable-next-line no-useless-call -- required for testing
	    method.call(null, argument || function () { return 1; }, 1);
	  });
	};

	var $forEach = arrayIteration.forEach;
	var arrayMethodIsStrict$3 = arrayMethodIsStrict$4;

	var STRICT_METHOD$3 = arrayMethodIsStrict$3('forEach');

	// `Array.prototype.forEach` method implementation
	// https://tc39.es/ecma262/#sec-array.prototype.foreach
	var arrayForEach = !STRICT_METHOD$3 ? function forEach(callbackfn /* , thisArg */) {
	  return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	// eslint-disable-next-line es/no-array-prototype-foreach -- safe
	} : [].forEach;

	var $$L = _export;
	var forEach$3 = arrayForEach;

	// `Array.prototype.forEach` method
	// https://tc39.es/ecma262/#sec-array.prototype.foreach
	// eslint-disable-next-line es/no-array-prototype-foreach -- safe
	$$L({ target: 'Array', proto: true, forced: [].forEach != forEach$3 }, {
	  forEach: forEach$3
	});

	var entryVirtual$e = entryVirtual$i;

	var forEach$2 = entryVirtual$e('Array').forEach;

	var parent$_ = forEach$2;

	var forEach$1 = parent$_;

	var classof$2 = classof$b;
	var hasOwn$3 = hasOwnProperty_1;
	var isPrototypeOf$d = objectIsPrototypeOf;
	var method$c = forEach$1;

	var ArrayPrototype$a = Array.prototype;

	var DOMIterables = {
	  DOMTokenList: true,
	  NodeList: true
	};

	var forEach = function (it) {
	  var own = it.forEach;
	  return it === ArrayPrototype$a || (isPrototypeOf$d(ArrayPrototype$a, it) && own === ArrayPrototype$a.forEach)
	    || hasOwn$3(DOMIterables, classof$2(it)) ? method$c : own;
	};

	(function (module) {
		module.exports = forEach;
	} (forEach$4));

	var _forEachInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(forEach$4.exports);

	var splice$3 = {exports: {}};

	var DESCRIPTORS$6 = descriptors;
	var isArray$9 = isArray$e;

	var $TypeError$5 = TypeError;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$7 = Object.getOwnPropertyDescriptor;

	// Safari < 13 does not throw an error in this case
	var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS$6 && !function () {
	  // makes no sense without proper strict mode support
	  if (this !== undefined) return true;
	  try {
	    // eslint-disable-next-line es/no-object-defineproperty -- safe
	    Object.defineProperty([], 'length', { writable: false }).length = 1;
	  } catch (error) {
	    return error instanceof TypeError;
	  }
	}();

	var arraySetLength = SILENT_ON_NON_WRITABLE_LENGTH_SET ? function (O, length) {
	  if (isArray$9(O) && !getOwnPropertyDescriptor$7(O, 'length').writable) {
	    throw $TypeError$5('Cannot set read only .length');
	  } return O.length = length;
	} : function (O, length) {
	  return O.length = length;
	};

	var tryToString = tryToString$6;

	var $TypeError$4 = TypeError;

	var deletePropertyOrThrow$2 = function (O, P) {
	  if (!delete O[P]) throw $TypeError$4('Cannot delete property ' + tryToString(P) + ' of ' + tryToString(O));
	};

	var $$K = _export;
	var toObject$6 = toObject$c;
	var toAbsoluteIndex$1 = toAbsoluteIndex$5;
	var toIntegerOrInfinity = toIntegerOrInfinity$5;
	var lengthOfArrayLike$3 = lengthOfArrayLike$a;
	var setArrayLength = arraySetLength;
	var doesNotExceedSafeInteger = doesNotExceedSafeInteger$2;
	var arraySpeciesCreate = arraySpeciesCreate$3;
	var createProperty$2 = createProperty$6;
	var deletePropertyOrThrow$1 = deletePropertyOrThrow$2;
	var arrayMethodHasSpeciesSupport$2 = arrayMethodHasSpeciesSupport$5;

	var HAS_SPECIES_SUPPORT$2 = arrayMethodHasSpeciesSupport$2('splice');

	var max = Math.max;
	var min$1 = Math.min;

	// `Array.prototype.splice` method
	// https://tc39.es/ecma262/#sec-array.prototype.splice
	// with adding support of @@species
	$$K({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$2 }, {
	  splice: function splice(start, deleteCount /* , ...items */) {
	    var O = toObject$6(this);
	    var len = lengthOfArrayLike$3(O);
	    var actualStart = toAbsoluteIndex$1(start, len);
	    var argumentsLength = arguments.length;
	    var insertCount, actualDeleteCount, A, k, from, to;
	    if (argumentsLength === 0) {
	      insertCount = actualDeleteCount = 0;
	    } else if (argumentsLength === 1) {
	      insertCount = 0;
	      actualDeleteCount = len - actualStart;
	    } else {
	      insertCount = argumentsLength - 2;
	      actualDeleteCount = min$1(max(toIntegerOrInfinity(deleteCount), 0), len - actualStart);
	    }
	    doesNotExceedSafeInteger(len + insertCount - actualDeleteCount);
	    A = arraySpeciesCreate(O, actualDeleteCount);
	    for (k = 0; k < actualDeleteCount; k++) {
	      from = actualStart + k;
	      if (from in O) createProperty$2(A, k, O[from]);
	    }
	    A.length = actualDeleteCount;
	    if (insertCount < actualDeleteCount) {
	      for (k = actualStart; k < len - actualDeleteCount; k++) {
	        from = k + actualDeleteCount;
	        to = k + insertCount;
	        if (from in O) O[to] = O[from];
	        else deletePropertyOrThrow$1(O, to);
	      }
	      for (k = len; k > len - actualDeleteCount + insertCount; k--) deletePropertyOrThrow$1(O, k - 1);
	    } else if (insertCount > actualDeleteCount) {
	      for (k = len - actualDeleteCount; k > actualStart; k--) {
	        from = k + actualDeleteCount - 1;
	        to = k + insertCount - 1;
	        if (from in O) O[to] = O[from];
	        else deletePropertyOrThrow$1(O, to);
	      }
	    }
	    for (k = 0; k < insertCount; k++) {
	      O[k + actualStart] = arguments[k + 2];
	    }
	    setArrayLength(O, len - actualDeleteCount + insertCount);
	    return A;
	  }
	});

	var entryVirtual$d = entryVirtual$i;

	var splice$2 = entryVirtual$d('Array').splice;

	var isPrototypeOf$c = objectIsPrototypeOf;
	var method$b = splice$2;

	var ArrayPrototype$9 = Array.prototype;

	var splice$1 = function (it) {
	  var own = it.splice;
	  return it === ArrayPrototype$9 || (isPrototypeOf$c(ArrayPrototype$9, it) && own === ArrayPrototype$9.splice) ? method$b : own;
	};

	var parent$Z = splice$1;

	var splice = parent$Z;

	(function (module) {
		module.exports = splice;
	} (splice$3));

	var _spliceInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(splice$3.exports);

	var every$3 = {exports: {}};

	var $$J = _export;
	var $every = arrayIteration.every;
	var arrayMethodIsStrict$2 = arrayMethodIsStrict$4;

	var STRICT_METHOD$2 = arrayMethodIsStrict$2('every');

	// `Array.prototype.every` method
	// https://tc39.es/ecma262/#sec-array.prototype.every
	$$J({ target: 'Array', proto: true, forced: !STRICT_METHOD$2 }, {
	  every: function every(callbackfn /* , thisArg */) {
	    return $every(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$c = entryVirtual$i;

	var every$2 = entryVirtual$c('Array').every;

	var isPrototypeOf$b = objectIsPrototypeOf;
	var method$a = every$2;

	var ArrayPrototype$8 = Array.prototype;

	var every$1 = function (it) {
	  var own = it.every;
	  return it === ArrayPrototype$8 || (isPrototypeOf$b(ArrayPrototype$8, it) && own === ArrayPrototype$8.every) ? method$a : own;
	};

	var parent$Y = every$1;

	var every = parent$Y;

	(function (module) {
		module.exports = every;
	} (every$3));

	var _everyInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(every$3.exports);

	var concat$5 = {exports: {}};

	var entryVirtual$b = entryVirtual$i;

	var concat$4 = entryVirtual$b('Array').concat;

	var isPrototypeOf$a = objectIsPrototypeOf;
	var method$9 = concat$4;

	var ArrayPrototype$7 = Array.prototype;

	var concat$3 = function (it) {
	  var own = it.concat;
	  return it === ArrayPrototype$7 || (isPrototypeOf$a(ArrayPrototype$7, it) && own === ArrayPrototype$7.concat) ? method$9 : own;
	};

	var parent$X = concat$3;

	var concat$2 = parent$X;

	(function (module) {
		module.exports = concat$2;
	} (concat$5));

	var _concatInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(concat$5.exports);

	var map$a = {exports: {}};

	var $$I = _export;
	var $map = arrayIteration.map;
	var arrayMethodHasSpeciesSupport$1 = arrayMethodHasSpeciesSupport$5;

	var HAS_SPECIES_SUPPORT$1 = arrayMethodHasSpeciesSupport$1('map');

	// `Array.prototype.map` method
	// https://tc39.es/ecma262/#sec-array.prototype.map
	// with adding support of @@species
	$$I({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$1 }, {
	  map: function map(callbackfn /* , thisArg */) {
	    return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$a = entryVirtual$i;

	var map$9 = entryVirtual$a('Array').map;

	var isPrototypeOf$9 = objectIsPrototypeOf;
	var method$8 = map$9;

	var ArrayPrototype$6 = Array.prototype;

	var map$8 = function (it) {
	  var own = it.map;
	  return it === ArrayPrototype$6 || (isPrototypeOf$9(ArrayPrototype$6, it) && own === ArrayPrototype$6.map) ? method$8 : own;
	};

	var parent$W = map$8;

	var map$7 = parent$W;

	(function (module) {
		module.exports = map$7;
	} (map$a));

	var _mapInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(map$a.exports);

	var filter$3 = {exports: {}};

	var $$H = _export;
	var $filter = arrayIteration.filter;
	var arrayMethodHasSpeciesSupport = arrayMethodHasSpeciesSupport$5;

	var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter');

	// `Array.prototype.filter` method
	// https://tc39.es/ecma262/#sec-array.prototype.filter
	// with adding support of @@species
	$$H({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
	  filter: function filter(callbackfn /* , thisArg */) {
	    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$9 = entryVirtual$i;

	var filter$2 = entryVirtual$9('Array').filter;

	var isPrototypeOf$8 = objectIsPrototypeOf;
	var method$7 = filter$2;

	var ArrayPrototype$5 = Array.prototype;

	var filter$1 = function (it) {
	  var own = it.filter;
	  return it === ArrayPrototype$5 || (isPrototypeOf$8(ArrayPrototype$5, it) && own === ArrayPrototype$5.filter) ? method$7 : own;
	};

	var parent$V = filter$1;

	var filter = parent$V;

	(function (module) {
		module.exports = filter;
	} (filter$3));

	var _filterInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(filter$3.exports);

	var includes$4 = {exports: {}};

	var $$G = _export;
	var $includes = arrayIncludes.includes;
	var fails$7 = fails$x;

	// FF99+ bug
	var BROKEN_ON_SPARSE = fails$7(function () {
	  return !Array(1).includes();
	});

	// `Array.prototype.includes` method
	// https://tc39.es/ecma262/#sec-array.prototype.includes
	$$G({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
	  includes: function includes(el /* , fromIndex = 0 */) {
	    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$8 = entryVirtual$i;

	var includes$3 = entryVirtual$8('Array').includes;

	var isObject$3 = isObject$m;
	var classof$1 = classofRaw$2;
	var wellKnownSymbol$2 = wellKnownSymbol$o;

	var MATCH$1 = wellKnownSymbol$2('match');

	// `IsRegExp` abstract operation
	// https://tc39.es/ecma262/#sec-isregexp
	var isRegexp = function (it) {
	  var isRegExp;
	  return isObject$3(it) && ((isRegExp = it[MATCH$1]) !== undefined ? !!isRegExp : classof$1(it) == 'RegExp');
	};

	var isRegExp = isRegexp;

	var $TypeError$3 = TypeError;

	var notARegexp = function (it) {
	  if (isRegExp(it)) {
	    throw $TypeError$3("The method doesn't accept regular expressions");
	  } return it;
	};

	var wellKnownSymbol$1 = wellKnownSymbol$o;

	var MATCH = wellKnownSymbol$1('match');

	var correctIsRegexpLogic = function (METHOD_NAME) {
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

	var $$F = _export;
	var uncurryThis$9 = functionUncurryThis;
	var notARegExp$1 = notARegexp;
	var requireObjectCoercible$1 = requireObjectCoercible$7;
	var toString$2 = toString$c;
	var correctIsRegExpLogic$1 = correctIsRegexpLogic;

	var stringIndexOf = uncurryThis$9(''.indexOf);

	// `String.prototype.includes` method
	// https://tc39.es/ecma262/#sec-string.prototype.includes
	$$F({ target: 'String', proto: true, forced: !correctIsRegExpLogic$1('includes') }, {
	  includes: function includes(searchString /* , position = 0 */) {
	    return !!~stringIndexOf(
	      toString$2(requireObjectCoercible$1(this)),
	      toString$2(notARegExp$1(searchString)),
	      arguments.length > 1 ? arguments[1] : undefined
	    );
	  }
	});

	var entryVirtual$7 = entryVirtual$i;

	var includes$2 = entryVirtual$7('String').includes;

	var isPrototypeOf$7 = objectIsPrototypeOf;
	var arrayMethod = includes$3;
	var stringMethod = includes$2;

	var ArrayPrototype$4 = Array.prototype;
	var StringPrototype$2 = String.prototype;

	var includes$1 = function (it) {
	  var own = it.includes;
	  if (it === ArrayPrototype$4 || (isPrototypeOf$7(ArrayPrototype$4, it) && own === ArrayPrototype$4.includes)) return arrayMethod;
	  if (typeof it == 'string' || it === StringPrototype$2 || (isPrototypeOf$7(StringPrototype$2, it) && own === StringPrototype$2.includes)) {
	    return stringMethod;
	  } return own;
	};

	var parent$U = includes$1;

	var includes = parent$U;

	(function (module) {
		module.exports = includes;
	} (includes$4));

	var _includesInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(includes$4.exports);

	var trim$3 = {exports: {}};

	var PROPER_FUNCTION_NAME = functionName.PROPER;
	var fails$6 = fails$x;
	var whitespaces = whitespaces$4;

	var non = '\u200B\u0085\u180E';

	// check that a method works with the correct list
	// of whitespaces and has a correct name
	var stringTrimForced = function (METHOD_NAME) {
	  return fails$6(function () {
	    return !!whitespaces[METHOD_NAME]()
	      || non[METHOD_NAME]() !== non
	      || (PROPER_FUNCTION_NAME && whitespaces[METHOD_NAME].name !== METHOD_NAME);
	  });
	};

	var $$E = _export;
	var $trim = stringTrim.trim;
	var forcedStringTrimMethod = stringTrimForced;

	// `String.prototype.trim` method
	// https://tc39.es/ecma262/#sec-string.prototype.trim
	$$E({ target: 'String', proto: true, forced: forcedStringTrimMethod('trim') }, {
	  trim: function trim() {
	    return $trim(this);
	  }
	});

	var entryVirtual$6 = entryVirtual$i;

	var trim$2 = entryVirtual$6('String').trim;

	var isPrototypeOf$6 = objectIsPrototypeOf;
	var method$6 = trim$2;

	var StringPrototype$1 = String.prototype;

	var trim$1 = function (it) {
	  var own = it.trim;
	  return typeof it == 'string' || it === StringPrototype$1
	    || (isPrototypeOf$6(StringPrototype$1, it) && own === StringPrototype$1.trim) ? method$6 : own;
	};

	var parent$T = trim$1;

	var trim = parent$T;

	(function (module) {
		module.exports = trim;
	} (trim$3));

	var _trimInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(trim$3.exports);

	var indexOf$7 = {exports: {}};

	/* eslint-disable es/no-array-prototype-indexof -- required for testing */
	var $$D = _export;
	var uncurryThis$8 = functionUncurryThis;
	var $indexOf = arrayIncludes.indexOf;
	var arrayMethodIsStrict$1 = arrayMethodIsStrict$4;

	var nativeIndexOf = uncurryThis$8([].indexOf);

	var NEGATIVE_ZERO = !!nativeIndexOf && 1 / nativeIndexOf([1], 1, -0) < 0;
	var STRICT_METHOD$1 = arrayMethodIsStrict$1('indexOf');

	// `Array.prototype.indexOf` method
	// https://tc39.es/ecma262/#sec-array.prototype.indexof
	$$D({ target: 'Array', proto: true, forced: NEGATIVE_ZERO || !STRICT_METHOD$1 }, {
	  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
	    var fromIndex = arguments.length > 1 ? arguments[1] : undefined;
	    return NEGATIVE_ZERO
	      // convert -0 to +0
	      ? nativeIndexOf(this, searchElement, fromIndex) || 0
	      : $indexOf(this, searchElement, fromIndex);
	  }
	});

	var entryVirtual$5 = entryVirtual$i;

	var indexOf$6 = entryVirtual$5('Array').indexOf;

	var isPrototypeOf$5 = objectIsPrototypeOf;
	var method$5 = indexOf$6;

	var ArrayPrototype$3 = Array.prototype;

	var indexOf$5 = function (it) {
	  var own = it.indexOf;
	  return it === ArrayPrototype$3 || (isPrototypeOf$5(ArrayPrototype$3, it) && own === ArrayPrototype$3.indexOf) ? method$5 : own;
	};

	var parent$S = indexOf$5;

	var indexOf$4 = parent$S;

	(function (module) {
		module.exports = indexOf$4;
	} (indexOf$7));

	var _indexOfInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(indexOf$7.exports);

	/**
	 * Complete step.
	 */
	function stepComplete(params) {
	  var data = $__default["default"].extend({
	      action: 'bookly_render_complete'
	    }, params),
	    $container = opt[params.form_id].$container;
	  booklyAjax({
	    data: data
	  }).then(function (response) {
	    if (response.final_step_url && !data.error) {
	      document.location.href = response.final_step_url;
	    } else {
	      var _context;
	      $container.html(response.html);
	      var $qc = $__default["default"]('.bookly-js-qr', $container),
	        url = BooklyL10n.ajaxurl + (_indexOfInstanceProperty(_context = BooklyL10n.ajaxurl).call(_context, '?') > 0 ? '&' : '?') + 'bookly_order=' + response.bookly_order + '&csrf_token=' + BooklyL10n.csrf_token;
	      $__default["default"]('img', $qc).on('error', function () {
	        $qc.remove();
	      }).on('load', function () {
	        $qc.removeClass('bookly-loading');
	      });
	      scrollTo($container, params.form_id);
	      $__default["default"]('.bookly-js-start-over', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          reset_form: true,
	          new_chain: true
	        });
	      });
	      $__default["default"]('.bookly-js-download-ics', $container).on('click', function (e) {
	        var ladda = laddaStart(this);
	        window.location = url + '&action=bookly_add_to_calendar&calendar=ics';
	        _setTimeout(function () {
	          return ladda.stop();
	        }, 1500);
	      });
	      $__default["default"]('.bookly-js-download-invoice', $container).on('click', function (e) {
	        var ladda = laddaStart(this);
	        window.location = url + '&action=bookly_invoices_download_invoice';
	        _setTimeout(function () {
	          return ladda.stop();
	        }, 1500);
	      });
	      $__default["default"]('.bookly-js-add-to-calendar', $container).on('click', function (e) {
	        e.preventDefault();
	        var ladda = laddaStart(this);
	        window.open(url + '&action=bookly_add_to_calendar&calendar=' + $__default["default"](this).data('calendar'), '_blank');
	        _setTimeout(function () {
	          return ladda.stop();
	        }, 1500);
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
	  }).then(function (response) {
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
	    var customJS = response.custom_js;
	    var $stripe_card_field = $__default["default"]('#bookly-stripe-card-field', $container);

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
	        $__default["default"]('.pay-card .bookly-js-next-step', $container).prop('disabled', true);
	        var $details = $stripe_card_field.closest('.bookly-js-details');
	        $__default["default"]('.bookly-form-group', $details).hide();
	        $__default["default"]('.bookly-js-card-error', $details).text('Please call Stripe() with your publishable key. You used an empty string.');
	      }
	    }
	    var $payments = $__default["default"]('.bookly-js-payment', $container),
	      $apply_coupon_button = $__default["default"]('.bookly-js-apply-coupon', $container),
	      $coupon_input = $__default["default"]('input.bookly-user-coupon', $container),
	      $apply_gift_card_button = $__default["default"]('.bookly-js-apply-gift-card', $container),
	      $gift_card_input = $__default["default"]('input.bookly-user-gift', $container),
	      $apply_tips_button = $__default["default"]('.bookly-js-apply-tips', $container),
	      $applied_tips_button = $__default["default"]('.bookly-js-applied-tips', $container),
	      $tips_input = $__default["default"]('input.bookly-user-tips', $container),
	      $tips_error = $__default["default"]('.bookly-js-tips-error', $container),
	      $deposit_mode = $__default["default"]('input[type=radio][name=bookly-full-payment]', $container),
	      $coupon_info_text = $__default["default"]('.bookly-info-text-coupon', $container),
	      $buttons = $__default["default"]('.bookly-gateway-buttons,.bookly-js-details', $container),
	      $payment_details;
	    $payments.on('click', function () {
	      $buttons.hide();
	      $__default["default"]('.bookly-gateway-buttons.pay-' + $__default["default"](this).val(), $container).show();
	      if ($__default["default"](this).data('with-details') == 1) {
	        var $parent = $__default["default"](this).closest('.bookly-list');
	        $payment_details = $__default["default"]('.bookly-js-details', $parent);
	        $__default["default"]('.bookly-js-details', $parent).show();
	      } else {
	        $payment_details = null;
	      }
	    });
	    $payments.eq(0).trigger('click');
	    $deposit_mode.on('change', function () {
	      var data = {
	        action: 'bookly_deposit_payments_apply_payment_method',
	        form_id: params.form_id,
	        deposit_full: $__default["default"](this).val()
	      };
	      $__default["default"](this).hide();
	      $__default["default"](this).prev().css('display', 'inline-block');
	      booklyAjax({
	        type: 'POST',
	        data: data
	      }).then(function (response) {
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
	        error: function error() {
	          ladda.stop();
	        }
	      }).then(function (response) {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(function (response) {
	        $coupon_input.addClass('bookly-error');
	        $coupon_info_text.html(response.text);
	        $apply_coupon_button.next('.bookly-label-error').remove();
	        var $error = $__default["default"]('<div>', {
	          class: 'bookly-label-error',
	          text: (response === null || response === void 0 ? void 0 : response.error) || 'Error'
	        });
	        $error.insertAfter($apply_coupon_button);
	        scrollTo($error, params.form_id);
	      }).finally(function () {
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
	        error: function error() {
	          ladda.stop();
	        }
	      }).then(function (response) {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(function (response) {
	        if ($__default["default"]('.bookly-js-payment[value!=free]', $container).length > 0) {
	          $gift_card_input.addClass('bookly-error');
	          $apply_gift_card_button.next('.bookly-label-error').remove();
	          var $error = $__default["default"]('<div>', {
	            class: 'bookly-label-error',
	            text: (response === null || response === void 0 ? void 0 : response.error) || 'Error'
	          });
	          $error.insertAfter($apply_gift_card_button);
	          scrollTo($error, params.form_id);
	        } else {
	          stepPayment({
	            form_id: params.form_id
	          });
	        }
	      }).finally(function () {
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
	        error: function error() {
	          ladda.stop();
	        }
	      }).then(function (response) {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(function (response) {
	        $tips_error.html(response.error);
	        $tips_input.addClass('bookly-error');
	        scrollTo($tips_error, params.form_id);
	        ladda.stop();
	      });
	    });
	    $__default["default"]('.bookly-js-next-step', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      var ladda = laddaStart(this),
	        $gateway_checked = _filterInstanceProperty($payments).call($payments, ':checked');

	      // Execute custom JavaScript
	      if (customJS) {
	        try {
	          $__default["default"].globalEval(customJS.next_button);
	        } catch (e) {
	          // Do nothing
	        }
	      }
	      if ($gateway_checked.val() === 'card') {
	        var gateway = $gateway_checked.data('gateway');
	        if (gateway === 'authorize_net') {
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_create_payment_intent',
	              card: {
	                number: $__default["default"]('input[name="card_number"]', $payment_details).val(),
	                cvc: $__default["default"]('input[name="card_cvc"]', $payment_details).val(),
	                exp_month: $__default["default"]('select[name="card_exp_month"]', $payment_details).val(),
	                exp_year: $__default["default"]('select[name="card_exp_year"]', $payment_details).val()
	              },
	              response_url: window.location.pathname + window.location.search.split('#')[0],
	              form_id: params.form_id,
	              gateway: gateway,
	              form_slug: 'booking-form'
	            }
	          }).then(function (response) {
	            retrieveRequest(response.data, params.form_id);
	          }).catch(function (response) {
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
	              gateway: gateway,
	              form_slug: 'booking-form'
	            }
	          }).then(function (response) {
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
	                    form_slug: 'booking-form',
	                    bookly_order: response.data.bookly_order
	                  }
	                }).then(function (response) {
	                  ladda.stop();
	                  var $stripe_container = $gateway_checked.closest('.bookly-list');
	                  $__default["default"]('.bookly-label-error', $stripe_container).remove();
	                  $stripe_container.append($__default["default"]('<div>', {
	                    class: 'bookly-label-error',
	                    text: result.error.message || 'Error'
	                  }));
	                });
	              } else {
	                retrieveRequest(response.data, params.form_id);
	              }
	            });
	          }).catch(function (response) {
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
	            response_url: window.location.pathname + window.location.search.split('#')[0],
	            form_slug: 'booking-form'
	          }
	        }).then(function (response) {
	          retrieveRequest(response.data, params.form_id);
	        }).catch(function (response) {
	          handleBooklyAjaxError(response, params.form_id, $gateway_checked.closest('.bookly-list'));
	          ladda.stop();
	        });
	      }
	    });
	    $__default["default"]('.bookly-js-back-step', $container).on('click', function (e) {
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
	  }).then(function (response) {
	    stepComplete({
	      form_id: form_id
	    });
	  }).catch(function (response) {
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
	    $__default["default"]('.bookly-label-error', $gateway_selector).remove();
	    $gateway_selector.append($__default["default"]('<div>', {
	      class: 'bookly-label-error',
	      text: (response === null || response === void 0 ? void 0 : response.error_message) || 'Error'
	    }));
	  }
	}
	function retrieveRequest(data, form_id) {
	  if (data.on_site) {
	    $__default["default"].ajax({
	      type: 'GET',
	      url: data.target_url,
	      xhrFields: {
	        withCredentials: true
	      },
	      crossDomain: 'withCredentials' in new XMLHttpRequest()
	    }).always(function () {
	      stepComplete({
	        form_id: form_id
	      });
	    });
	  } else {
	    document.location.href = data.target_url;
	  }
	}

	/**
	 * Details step.
	 */
	function stepDetails(params) {
	  var data = $__default["default"].extend({
	      action: 'bookly_render_details'
	    }, params),
	    $container = opt[params.form_id].$container;
	  booklyAjax({
	    data: data
	  }).then(function (response) {
	    var _context, _context2;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    var intlTelInput = response.intlTelInput,
	      update_details_dialog = response.update_details_dialog,
	      woocommerce = response.woocommerce,
	      customJS = response.custom_js,
	      custom_fields_conditions = response.custom_fields_conditions || [],
	      terms_error = response.l10n.terms_error;
	    if (opt[params.form_id].hasOwnProperty('google_maps') && opt[params.form_id].google_maps.enabled) {
	      booklyInitGooglePlacesAutocomplete($container);
	    }
	    $__default["default"](document.body).trigger('bookly.render.step_detail', [$container]);
	    // Init.
	    var phone_number = '',
	      $guest_info = $__default["default"]('.bookly-js-guest', $container),
	      $phone_field = $__default["default"]('.bookly-js-user-phone-input', $container),
	      $email_field = $__default["default"]('.bookly-js-user-email', $container),
	      $email_confirm_field = $__default["default"]('.bookly-js-user-email-confirm', $container),
	      $birthday_day_field = $__default["default"]('.bookly-js-select-birthday-day', $container),
	      $birthday_month_field = $__default["default"]('.bookly-js-select-birthday-month', $container),
	      $birthday_year_field = $__default["default"]('.bookly-js-select-birthday-year', $container),
	      $address_country_field = $__default["default"]('.bookly-js-address-country', $container),
	      $address_state_field = $__default["default"]('.bookly-js-address-state', $container),
	      $address_postcode_field = $__default["default"]('.bookly-js-address-postcode', $container),
	      $address_city_field = $__default["default"]('.bookly-js-address-city', $container),
	      $address_street_field = $__default["default"]('.bookly-js-address-street', $container),
	      $address_street_number_field = $__default["default"]('.bookly-js-address-street_number', $container),
	      $address_additional_field = $__default["default"]('.bookly-js-address-additional_address', $container),
	      $address_country_error = $__default["default"]('.bookly-js-address-country-error', $container),
	      $address_state_error = $__default["default"]('.bookly-js-address-state-error', $container),
	      $address_postcode_error = $__default["default"]('.bookly-js-address-postcode-error', $container),
	      $address_city_error = $__default["default"]('.bookly-js-address-city-error', $container),
	      $address_street_error = $__default["default"]('.bookly-js-address-street-error', $container),
	      $address_street_number_error = $__default["default"]('.bookly-js-address-street_number-error', $container),
	      $address_additional_error = $__default["default"]('.bookly-js-address-additional_address-error', $container),
	      $birthday_day_error = $__default["default"]('.bookly-js-select-birthday-day-error', $container),
	      $birthday_month_error = $__default["default"]('.bookly-js-select-birthday-month-error', $container),
	      $birthday_year_error = $__default["default"]('.bookly-js-select-birthday-year-error', $container),
	      $full_name_field = $__default["default"]('.bookly-js-full-name', $container),
	      $first_name_field = $__default["default"]('.bookly-js-first-name', $container),
	      $last_name_field = $__default["default"]('.bookly-js-last-name', $container),
	      $notes_field = $__default["default"]('.bookly-js-user-notes', $container),
	      $custom_field = $__default["default"]('.bookly-js-custom-field', $container),
	      $info_field = $__default["default"]('.bookly-js-info-field', $container),
	      $phone_error = $__default["default"]('.bookly-js-user-phone-error', $container),
	      $email_error = $__default["default"]('.bookly-js-user-email-error', $container),
	      $email_confirm_error = $__default["default"]('.bookly-js-user-email-confirm-error', $container),
	      $name_error = $__default["default"]('.bookly-js-full-name-error', $container),
	      $first_name_error = $__default["default"]('.bookly-js-first-name-error', $container),
	      $last_name_error = $__default["default"]('.bookly-js-last-name-error', $container),
	      $captcha = $__default["default"]('.bookly-js-captcha-img', $container),
	      $custom_error = $__default["default"]('.bookly-custom-field-error', $container),
	      $info_error = $__default["default"]('.bookly-js-info-field-error', $container),
	      $modals = $__default["default"]('.bookly-js-modal', $container),
	      $login_modal = $__default["default"]('.bookly-js-login', $container),
	      $cst_modal = $__default["default"]('.bookly-js-cst-duplicate', $container),
	      $verification_modal = $__default["default"]('.bookly-js-verification-code', $container),
	      $verification_code = $__default["default"]('#bookly-verification-code', $container),
	      $next_btn = $__default["default"]('.bookly-js-next-step', $container),
	      $errors = _mapInstanceProperty(_context = $__default["default"]([$birthday_day_error, $birthday_month_error, $birthday_year_error, $address_country_error, $address_state_error, $address_postcode_error, $address_city_error, $address_street_error, $address_street_number_error, $address_additional_error, $name_error, $first_name_error, $last_name_error, $phone_error, $email_error, $email_confirm_error, $custom_error, $info_error])).call(_context, $__default["default"].fn.toArray),
	      $fields = _mapInstanceProperty(_context2 = $__default["default"]([$birthday_day_field, $birthday_month_field, $birthday_year_field, $address_city_field, $address_country_field, $address_postcode_field, $address_state_field, $address_street_field, $address_street_number_field, $address_additional_field, $full_name_field, $first_name_field, $last_name_field, $phone_field, $email_field, $email_confirm_field, $custom_field, $info_field])).call(_context2, $__default["default"].fn.toArray);

	    // Populate form after login.
	    var populateForm = function populateForm(response) {
	      $full_name_field.val(response.data.full_name).removeClass('bookly-error');
	      $first_name_field.val(response.data.first_name).removeClass('bookly-error');
	      $last_name_field.val(response.data.last_name).removeClass('bookly-error');
	      if (response.data.birthday) {
	        var dateParts = response.data.birthday.split('-'),
	          year = _parseInt(dateParts[0]),
	          month = _parseInt(dateParts[1]),
	          day = _parseInt(dateParts[2]);
	        $birthday_day_field.val(day).removeClass('bookly-error');
	        $birthday_month_field.val(month).removeClass('bookly-error');
	        $birthday_year_field.val(year).removeClass('bookly-error');
	      }
	      if (response.data.phone) {
	        $phone_field.removeClass('bookly-error');
	        if (intlTelInput.enabled) {
	          $phone_field.intlTelInput('setNumber', response.data.phone);
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
	                _filterInstanceProperty(_context5 = _findInstanceProperty($info_field).call($info_field, '.bookly-js-info-field')).call(_context5, function () {
	                  return this.value == value;
	                }).prop('checked', true);
	              });
	              break;
	            case 'radio-buttons':
	              _filterInstanceProperty(_context6 = _findInstanceProperty($info_field).call($info_field, '.bookly-js-info-field')).call(_context6, function () {
	                return this.value == field.value;
	              }).prop('checked', true);
	              break;
	            default:
	              _findInstanceProperty($info_field).call($info_field, '.bookly-js-info-field').val(field.value);
	              break;
	          }
	        });
	      }
	      _filterInstanceProperty($errors).call($errors, ':not(.bookly-custom-field-error)').html('');
	    };
	    var checkCustomFieldConditions = function checkCustomFieldConditions($row) {
	      var id = $row.data('id'),
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
	            if ($__default["default"](this).prop('checked')) {
	              value.push($__default["default"](this).val());
	            }
	          });
	          break;
	      }
	      $__default["default"].each(custom_fields_conditions, function (i, condition) {
	        var $target = $__default["default"]('.bookly-custom-field-row[data-id="' + condition.target + '"]'),
	          target_visibility = $target.is(':visible');
	        if (_parseInt(condition.source) === id) {
	          var show = false;
	          $__default["default"].each(value, function (i, v) {
	            var _context7, _context8;
	            if ($row.is(':visible') && (_includesInstanceProperty(_context7 = condition.value).call(_context7, v) && condition.equal === '1' || !_includesInstanceProperty(_context8 = condition.value).call(_context8, v) && condition.equal !== '1')) {
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
	    $__default["default"]('.bookly-custom-field-row').on('change', 'select, input[type="checkbox"], input[type="radio"]', function () {
	      checkCustomFieldConditions($__default["default"](this).closest('.bookly-custom-field-row'));
	    });
	    $__default["default"]('.bookly-custom-field-row').each(function () {
	      var _context9;
	      var _type = $__default["default"](this).data('type');
	      if (_includesInstanceProperty(_context9 = ['drop-down', 'radio-buttons', 'checkboxes']).call(_context9, _type)) {
	        if (_type === 'drop-down') {
	          var _context10;
	          _findInstanceProperty(_context10 = $__default["default"](this)).call(_context10, 'select').trigger('change');
	        } else {
	          var _context11;
	          _findInstanceProperty(_context11 = $__default["default"](this)).call(_context11, 'input:checked').trigger('change');
	        }
	      }
	    });
	    // Custom fields date fields
	    $__default["default"]('.bookly-js-cf-date', $container).each(function () {
	      var _context12, _context13;
	      var $cf_date = $__default["default"](this);
	      $cf_date.pickadate({
	        formatSubmit: 'yyyy-mm-dd',
	        format: opt[params.form_id].date_format,
	        min: $__default["default"](this).data('min') !== '' ? _mapInstanceProperty(_context12 = $__default["default"](this).data('min').split('-')).call(_context12, function (value, index) {
	          if (index === 1) return value - 1;else return _parseInt(value);
	        }) : false,
	        max: $__default["default"](this).data('max') !== '' ? _mapInstanceProperty(_context13 = $__default["default"](this).data('max').split('-')).call(_context13, function (value, index) {
	          if (index === 1) return value - 1;else return _parseInt(value);
	        }) : false,
	        clear: false,
	        close: false,
	        today: BooklyL10n.today,
	        monthsFull: BooklyL10n.months,
	        weekdaysFull: BooklyL10n.days,
	        weekdaysShort: BooklyL10n.daysShort,
	        labelMonthNext: BooklyL10n.nextMonth,
	        labelMonthPrev: BooklyL10n.prevMonth,
	        firstDay: opt[params.form_id].firstDay,
	        onClose: function onClose() {
	          // Hide for skip tab navigations by days of the month when the calendar is closed
	          $__default["default"]('#' + $cf_date.attr('aria-owns')).hide();
	        }
	      }).focusin(function () {
	        // Restore calendar visibility, changed on onClose
	        $__default["default"]('#' + $cf_date.attr('aria-owns')).show();
	      });
	    });
	    if (intlTelInput.enabled) {
	      $phone_field.intlTelInput({
	        preferredCountries: [intlTelInput.country],
	        initialCountry: intlTelInput.country,
	        geoIpLookup: function geoIpLookup(callback) {
	          $__default["default"].get('https://ipinfo.io', function () {}, 'jsonp').always(function (resp) {
	            var countryCode = resp && resp.country ? resp.country : '';
	            callback(countryCode);
	          });
	        },
	        utilsScript: intlTelInput.utils
	      });
	    }
	    // Init modals.
	    _findInstanceProperty($container).call($container, '.bookly-js-modal.' + params.form_id).remove();
	    $modals.addClass(params.form_id).appendTo($container).on('click', '.bookly-js-close', function (e) {
	      var _context14, _context15, _context16;
	      e.preventDefault();
	      _findInstanceProperty(_context14 = _findInstanceProperty(_context15 = _findInstanceProperty(_context16 = $__default["default"](e.delegateTarget).removeClass('bookly-in')).call(_context16, 'form').trigger('reset').end()).call(_context15, 'input').removeClass('bookly-error').end()).call(_context14, '.bookly-label-error').html('');
	    });
	    // Login modal.
	    $__default["default"]('.bookly-js-login-show', $container).on('click', function (e) {
	      e.preventDefault();
	      $login_modal.addClass('bookly-in');
	    });
	    $__default["default"]('button:submit', $login_modal).on('click', function (e) {
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
	      }).then(function (response) {
	        BooklyL10n.csrf_token = response.data.csrf_token;
	        $guest_info.fadeOut('slow');
	        populateForm(response);
	        $login_modal.removeClass('bookly-in');
	      }).catch(function (response) {
	        if (response.error == 'incorrect_username_password') {
	          _findInstanceProperty($login_modal).call($login_modal, 'input').addClass('bookly-error');
	          _findInstanceProperty($login_modal).call($login_modal, '.bookly-label-error').html(opt[params.form_id].errors[response.error]);
	        }
	      }).finally(function () {
	        ladda.stop();
	      });
	    });
	    // Customer duplicate modal.
	    $__default["default"]('button:submit', $cst_modal).on('click', function (e) {
	      e.preventDefault();
	      $cst_modal.removeClass('bookly-in');
	      $next_btn.trigger('click', [1]);
	    });
	    // Verification code modal.
	    $__default["default"]('button:submit', $verification_modal).on('click', function (e) {
	      e.preventDefault();
	      $verification_modal.removeClass('bookly-in');
	      $next_btn.trigger('click');
	    });
	    // Facebook login button.
	    if (opt[params.form_id].hasOwnProperty('facebook') && opt[params.form_id].facebook.enabled && typeof FB !== 'undefined') {
	      FB.XFBML.parse($__default["default"]('.bookly-js-fb-login-button', $container).parent().get(0));
	      opt[params.form_id].facebook.onStatusChange = function (response) {
	        if (response.status === 'connected') {
	          opt[params.form_id].facebook.enabled = false;
	          opt[params.form_id].facebook.onStatusChange = undefined;
	          $guest_info.fadeOut('slow', function () {
	            // Hide buttons in all Bookly forms on the page.
	            $__default["default"]('.bookly-js-fb-login-button').hide();
	          });
	          FB.api('/me', {
	            fields: 'id,name,first_name,last_name,email'
	          }, function (userInfo) {
	            booklyAjax({
	              type: 'POST',
	              data: $__default["default"].extend(userInfo, {
	                action: 'bookly_pro_facebook_login',
	                form_id: params.form_id
	              })
	            }).then(function (response) {
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
	      var $terms = $__default["default"]('.bookly-js-terms', $container),
	        $terms_error = $__default["default"]('.bookly-js-terms-error', $container);
	      $terms_error.html('');
	      if ($terms.length && !$terms.prop('checked')) {
	        $terms_error.html(terms_error);
	      } else {
	        var _context17, _context18;
	        var info_fields = [],
	          custom_fields = {},
	          checkbox_values,
	          captcha_ids = [],
	          ladda = laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $__default["default"].globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }

	        // Customer information fields.
	        $__default["default"]('div.bookly-js-info-field-row', $container).each(function () {
	          var $this = $__default["default"](this);
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
	                value: _findInstanceProperty($this).call($this, 'input.bookly-js-info-field').pickadate('picker').get('select', 'yyyy-mm-dd')
	              });
	              break;
	          }
	        });
	        // Custom fields.
	        $__default["default"]('.bookly-custom-fields-container', $container).each(function () {
	          var $cf_container = $__default["default"](this),
	            key = $cf_container.data('key'),
	            custom_fields_data = [];
	          $__default["default"]('div.bookly-custom-field-row', $cf_container).each(function () {
	            var $this = $__default["default"](this);
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
	                    value: _findInstanceProperty($this).call($this, 'input.bookly-js-custom-field').pickadate('picker').get('select', 'yyyy-mm-dd')
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
	        try {
	          phone_number = intlTelInput.enabled ? $phone_field.intlTelInput('getNumber') : $phone_field.val();
	          if (phone_number == '') {
	            phone_number = $phone_field.val();
	          }
	        } catch (error) {
	          // In case when intlTelInput can't return phone number.
	          phone_number = $phone_field.val();
	        }
	        var data = {
	          action: 'bookly_session_save',
	          form_id: params.form_id,
	          full_name: $full_name_field.val(),
	          first_name: $first_name_field.val(),
	          last_name: $last_name_field.val(),
	          phone: phone_number,
	          email: _trimInstanceProperty(_context17 = $email_field.val()).call(_context17),
	          email_confirm: $email_confirm_field.length === 1 ? _trimInstanceProperty(_context18 = $email_confirm_field.val()).call(_context18) : undefined,
	          birthday: {
	            day: $birthday_day_field.val(),
	            month: $birthday_month_field.val(),
	            year: $birthday_year_field.val()
	          },
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
	        }).then(function (response) {
	          if (woocommerce.enabled) {
	            var data = {
	              action: 'bookly_pro_add_to_woocommerce_cart',
	              form_id: params.form_id
	            };
	            booklyAjax({
	              type: 'POST',
	              data: data
	            }).then(function (response) {
	              window.location.href = response.data.target_url;
	            }).catch(function (response) {
	              ladda.stop();
	              stepTime({
	                form_id: params.form_id
	              }, opt[params.form_id].errors[response.data.error]);
	            });
	          } else {
	            stepPayment({
	              form_id: params.form_id
	            });
	          }
	        }).catch(function (response) {
	          var $scroll_to = null;
	          if (response.appointments_limit_reached) {
	            stepComplete({
	              form_id: params.form_id,
	              error: 'appointments_limit_reached'
	            });
	          } else if (response.hasOwnProperty('verify')) {
	            ladda.stop();
	            _findInstanceProperty($verification_modal).call($verification_modal, '#bookly-verification-code-text').html(response.verify_text).end().addClass('bookly-in');
	          } else if (response.group_skip_payment) {
	            booklyAjax({
	              type: 'POST',
	              data: {
	                action: 'bookly_save_appointment',
	                form_id: params.form_id
	              }
	            }).then(function (response) {
	              stepComplete({
	                form_id: params.form_id,
	                error: 'group_skip_payment'
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
	              $__default["default"].each(response.info_fields, function (field_id, message) {
	                var $div = $__default["default"]('div.bookly-js-info-field-row[data-id="' + field_id + '"]', $container);
	                _findInstanceProperty($div).call($div, '.bookly-js-info-field-error').html(message);
	                _findInstanceProperty($div).call($div, '.bookly-js-info-field').addClass('bookly-error');
	                if ($scroll_to === null) {
	                  $scroll_to = _findInstanceProperty($div).call($div, '.bookly-js-info-field');
	                }
	              });
	            }
	            if (response.custom_fields) {
	              $__default["default"].each(response.custom_fields, function (key, fields) {
	                $__default["default"].each(fields, function (field_id, message) {
	                  var $custom_fields_collector = $__default["default"]('.bookly-custom-fields-container[data-key="' + key + '"]', $container);
	                  var $div = $__default["default"]('[data-id="' + field_id + '"]', $custom_fields_collector);
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
	    $__default["default"]('.bookly-js-back-step', $container).on('click', function (e) {
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
	    $__default["default"]('.bookly-js-captcha-refresh', $container).on('click', function () {
	      $captcha.css('opacity', '0.5');
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_custom_fields_captcha_refresh',
	          form_id: params.form_id
	        }
	      }).then(function (response) {
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
	    bookly_forms = bookly_forms || $__default["default"]('.bookly-form .bookly-details-step');
	    bookly_forms.each(function () {
	      initGooglePlacesAutocomplete($__default["default"](this));
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
	        val: function val() {
	          return getFieldValueByType('country');
	        },
	        short: function short() {
	          return getFieldValueByType('country', true);
	        }
	      }, {
	        selector: '.bookly-js-address-postcode',
	        val: function val() {
	          return getFieldValueByType('postal_code');
	        }
	      }, {
	        selector: '.bookly-js-address-city',
	        val: function val() {
	          return getFieldValueByType('locality') || getFieldValueByType('administrative_area_level_3') || getFieldValueByType('postal_town');
	        }
	      }, {
	        selector: '.bookly-js-address-state',
	        val: function val() {
	          return getFieldValueByType('administrative_area_level_1');
	        },
	        short: function short() {
	          return getFieldValueByType('administrative_area_level_1', true);
	        }
	      }, {
	        selector: '.bookly-js-address-street',
	        val: function val() {
	          return getFieldValueByType('route');
	        }
	      }, {
	        selector: '.bookly-js-address-street_number',
	        val: function val() {
	          return getFieldValueByType('street_number');
	        }
	      }, {
	        selector: '.bookly-js-address-additional_address',
	        val: function val() {
	          return getFieldValueByType('subpremise') || getFieldValueByType('neighborhood') || getFieldValueByType('sublocality');
	        }
	      }];
	    var getFieldValueByType = function getFieldValueByType(type, useShortName) {
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
	    var data = $__default["default"].extend({
	        action: 'bookly_render_cart'
	      }, params),
	      $container = opt[params.form_id].$container;
	    booklyAjax({
	      data: data
	    }).then(function (response) {
	      $container.html(response.html);
	      if (error) {
	        $__default["default"]('.bookly-label-error', $container).html(error.message);
	        $__default["default"]('tr[data-cart-key="' + error.failed_key + '"]', $container).addClass('bookly-label-error');
	      } else {
	        $__default["default"]('.bookly-label-error', $container).hide();
	      }
	      scrollTo($container, params.form_id);
	      var customJS = response.custom_js;
	      $__default["default"]('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $__default["default"].globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }
	        stepDetails({
	          form_id: params.form_id
	        });
	      });
	      $__default["default"]('.bookly-add-item', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          new_chain: true
	        });
	      });
	      // 'BACK' button.
	      $__default["default"]('.bookly-js-back-step', $container).on('click', function (e) {
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
	      $__default["default"]('.bookly-js-actions button', $container).on('click', function () {
	        laddaStart(this);
	        var $this = $__default["default"](this),
	          $cart_item = $this.closest('tr');
	        switch ($this.data('action')) {
	          case 'drop':
	            booklyAjax({
	              data: {
	                action: 'bookly_cart_drop_item',
	                form_id: params.form_id,
	                cart_key: $cart_item.data('cart-key')
	              }
	            }).then(function (response) {
	              var remove_cart_key = $cart_item.data('cart-key'),
	                $trs_to_remove = $__default["default"]('tr[data-cart-key="' + remove_cart_key + '"]', $container);
	              $cart_item.delay(300).fadeOut(200, function () {
	                if (response.data.total_waiting_list) {
	                  $__default["default"]('.bookly-js-waiting-list-price', $container).html(response.data.waiting_list_price);
	                  $__default["default"]('.bookly-js-waiting-list-deposit', $container).html(response.data.waiting_list_deposit);
	                } else {
	                  $__default["default"]('.bookly-js-waiting-list-price', $container).closest('tr').remove();
	                }
	                $__default["default"]('.bookly-js-subtotal-price', $container).html(response.data.subtotal_price);
	                $__default["default"]('.bookly-js-subtotal-deposit', $container).html(response.data.subtotal_deposit);
	                $__default["default"]('.bookly-js-pay-now-deposit', $container).html(response.data.pay_now_deposit);
	                $__default["default"]('.bookly-js-pay-now-tax', $container).html(response.data.pay_now_tax);
	                $__default["default"]('.bookly-js-total-price', $container).html(response.data.total_price);
	                $__default["default"]('.bookly-js-total-tax', $container).html(response.data.total_tax);
	                $trs_to_remove.remove();
	                if ($__default["default"]('tr[data-cart-key]').length == 0) {
	                  $__default["default"]('.bookly-js-back-step', $container).hide();
	                  $__default["default"]('.bookly-js-next-step', $container).hide();
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
	    var data = $__default["default"].extend({
	        action: 'bookly_render_repeat'
	      }, params),
	      $container = opt[params.form_id].$container;
	    booklyAjax({
	      data: data
	    }).then(function (response) {
	      var _context3;
	      $container.html(response.html);
	      scrollTo($container, params.form_id);
	      var $repeat_enabled = $__default["default"]('.bookly-js-repeat-appointment-enabled', $container),
	        $next_step = $__default["default"]('.bookly-js-next-step', $container),
	        $repeat_container = $__default["default"]('.bookly-js-repeat-variants-container', $container),
	        $variants = $__default["default"]('[class^="bookly-js-variant"]', $repeat_container),
	        $repeat_variant = $__default["default"]('.bookly-js-repeat-variant', $repeat_container),
	        $button_get_schedule = $__default["default"]('.bookly-js-get-schedule', $repeat_container),
	        $variant_weekly = $__default["default"]('.bookly-js-variant-weekly', $repeat_container),
	        $variant_monthly = $__default["default"]('.bookly-js-repeat-variant-monthly', $repeat_container),
	        $date_until = $__default["default"]('.bookly-js-repeat-until', $repeat_container),
	        $repeat_times = $__default["default"]('.bookly-js-repeat-times', $repeat_container),
	        $monthly_specific_day = $__default["default"]('.bookly-js-monthly-specific-day', $repeat_container),
	        $monthly_week_day = $__default["default"]('.bookly-js-monthly-week-day', $repeat_container),
	        $repeat_every_day = $__default["default"]('.bookly-js-repeat-daily-every', $repeat_container),
	        $schedule_container = $__default["default"]('.bookly-js-schedule-container', $container),
	        $days_error = $__default["default"]('.bookly-js-days-error', $repeat_container),
	        $schedule_slots = $__default["default"]('.bookly-js-schedule-slots', $schedule_container),
	        $intersection_info = $__default["default"]('.bookly-js-intersection-info', $schedule_container),
	        $info_help = $__default["default"]('.bookly-js-schedule-help', $schedule_container),
	        $info_wells = $__default["default"]('.bookly-well', $schedule_container),
	        $pagination = $__default["default"]('.bookly-pagination', $schedule_container),
	        $schedule_row_template = $__default["default"]('.bookly-schedule-row-template .bookly-schedule-row', $schedule_container),
	        pages_warning_info = response.pages_warning_info,
	        short_date_format = response.short_date_format,
	        bound_date = {
	          min: response.date_min || true,
	          max: response.date_max || true
	        },
	        schedule = [],
	        customJS = response.custom_js;
	      var repeat = {
	        prepareButtonNextState: function prepareButtonNextState() {
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
	        addTimeSlotControl: function addTimeSlotControl($schedule_row, options, preferred_time, selected_time) {
	          var $time = '';
	          if (options.length) {
	            var prefer;
	            $time = $__default["default"]('<select/>');
	            $__default["default"].each(options, function (index, option) {
	              var $option = $__default["default"]('<option/>');
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
	        renderSchedulePage: function renderSchedulePage(page) {
	          var $row,
	            count = schedule.length,
	            rows_on_page = 5,
	            start = rows_on_page * page - rows_on_page,
	            warning_pages = [],
	            previousPage = function previousPage(e) {
	              e.preventDefault();
	              var page = _parseInt(_findInstanceProperty($pagination).call($pagination, '.active').data('page'));
	              if (page > 1) {
	                repeat.renderSchedulePage(page - 1);
	              }
	            },
	            nextPage = function nextPage(e) {
	              e.preventDefault();
	              var page = _parseInt(_findInstanceProperty($pagination).call($pagination, '.active').data('page'));
	              if (page < count / rows_on_page) {
	                repeat.renderSchedulePage(page + 1);
	              }
	            };
	          $schedule_slots.html('');
	          for (var i = start, j = 0; j < rows_on_page && i < count; i++, j++) {
	            $row = $schedule_row_template.clone();
	            $row.data('datetime', schedule[i].datetime);
	            $row.data('index', schedule[i].index);
	            $__default["default"]('> div:first-child', $row).html(schedule[i].index);
	            $__default["default"]('.bookly-schedule-date', $row).html(schedule[i].display_date);
	            if (schedule[i].all_day_service_time !== undefined) {
	              $__default["default"]('.bookly-js-schedule-time', $row).hide();
	              $__default["default"]('.bookly-js-schedule-all-day-time', $row).html(schedule[i].all_day_service_time).show();
	            } else {
	              $__default["default"]('.bookly-js-schedule-time', $row).html(schedule[i].display_time).show();
	              $__default["default"]('.bookly-js-schedule-all-day-time', $row).hide();
	            }
	            if (schedule[i].another_time) {
	              $__default["default"]('.bookly-schedule-intersect', $row).show();
	            }
	            if (schedule[i].deleted) {
	              _findInstanceProperty($row).call($row, '.bookly-schedule-appointment').addClass('bookly-appointment-hidden');
	            }
	            $schedule_slots.append($row);
	          }
	          if (count > rows_on_page) {
	            var $btn = $__default["default"]('<li/>').append($__default["default"]('<a>', {
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
	              $btn = $__default["default"]('<li/>', {
	                'data-page': j
	              }).append($__default["default"]('<a>', {
	                href: '#',
	                text: j
	              }));
	              $pagination.append($btn);
	              $btn.on('click', function (e) {
	                e.preventDefault();
	                repeat.renderSchedulePage($__default["default"](this).data('page'));
	              }).keypress(function (e) {
	                e.preventDefault();
	                if (e.which == 13 || e.which == 32) {
	                  repeat.renderSchedulePage($__default["default"](this).data('page'));
	                }
	              });
	            }
	            _findInstanceProperty($pagination).call($pagination, 'li:eq(' + page + ')').addClass('active');
	            $btn = $__default["default"]('<li/>').append($__default["default"]('<a>', {
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
	                page = _parseInt(i / rows_on_page) + 1;
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
	        renderFullSchedule: function renderFullSchedule(data) {
	          schedule = data; // it has global scope
	          // Prefer time is display time selected on step time.
	          var preferred_time = null;
	          $__default["default"].each(schedule, function (index, item) {
	            if (!preferred_time && !item.another_time) {
	              preferred_time = item.display_time;
	            }
	          });
	          repeat.renderSchedulePage(1);
	          $schedule_container.show();
	          $next_step.prop('disabled', schedule.length == 0);
	          $schedule_slots.on('click', 'button[data-action]', function () {
	            var $schedule_row = $__default["default"](this).closest('.bookly-schedule-row');
	            var row_index = $schedule_row.data('index') - 1;
	            switch ($__default["default"](this).data('action')) {
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
	                var $date = $__default["default"]('<input/>', {
	                    type: 'text'
	                  }),
	                  $edit_button = $__default["default"](this),
	                  ladda_round = laddaStart(this);
	                _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-date').html($date);
	                $date.pickadate({
	                  min: bound_date.min,
	                  max: bound_date.max,
	                  formatSubmit: 'yyyy-mm-dd',
	                  format: short_date_format,
	                  clear: false,
	                  close: false,
	                  today: BooklyL10n.today,
	                  monthsFull: BooklyL10n.months,
	                  monthsShort: BooklyL10n.monthsShort,
	                  weekdaysFull: BooklyL10n.days,
	                  weekdaysShort: BooklyL10n.daysShort,
	                  labelMonthNext: BooklyL10n.nextMonth,
	                  labelMonthPrev: BooklyL10n.prevMonth,
	                  firstDay: opt[params.form_id].firstDay,
	                  onSet: function onSet() {
	                    var exclude = [];
	                    $__default["default"].each(schedule, function (index, item) {
	                      if (row_index != index && !item.deleted) {
	                        exclude.push(item.slots);
	                      }
	                    });
	                    booklyAjax({
	                      type: 'POST',
	                      data: {
	                        action: 'bookly_recurring_appointments_get_daily_customer_schedule',
	                        date: this.get('select', 'yyyy-mm-dd'),
	                        form_id: params.form_id,
	                        exclude: exclude
	                      }
	                    }).then(function (response) {
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
	                  },
	                  onClose: function onClose() {
	                    // Hide for skip tab navigations by days of the month when the calendar is closed
	                    $__default["default"]('#' + $date.attr('aria-owns')).hide();
	                  }
	                }).focusin(function () {
	                  // Restore calendar visibility, changed on onClose
	                  $__default["default"]('#' + $date.attr('aria-owns')).show();
	                });
	                var slots = JSON.parse(schedule[row_index].slots);
	                $date.pickadate('picker').set('select', new Date(slots[0][2]));
	                break;
	              case 'save':
	                $__default["default"](this).hide();
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
	        isDateMatchesSelections: function isDateMatchesSelections(current_date) {
	          switch ($repeat_variant.val()) {
	            case 'daily':
	              if (($repeat_every_day.val() > 6 || $__default["default"].inArray(current_date.format('ddd').toLowerCase(), repeat.week_days) != -1) && current_date.diff(repeat.date_from, 'days') % $repeat_every_day.val() == 0) {
	                return true;
	              }
	              break;
	            case 'weekly':
	            case 'biweekly':
	              if (($repeat_variant.val() == 'weekly' || current_date.diff(repeat.date_from.clone().startOf('isoWeek'), 'weeks') % 2 == 0) && $__default["default"].inArray(current_date.format('ddd').toLowerCase(), repeat.checked_week_days) != -1) {
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
	        updateRepeatDate: function updateRepeatDate() {
	          var _context;
	          var number_of_times = 0,
	            repeat_times = $repeat_times.val(),
	            date_from = _sliceInstanceProperty$1(_context = bound_date.min).call(_context),
	            date_until = $date_until.pickadate('picker').get('select'),
	            moment_until = moment().year(date_until.year).month(date_until.month).date(date_until.date).add(5, 'years');
	          date_from[1]++;
	          repeat.date_from = moment(date_from.join(','), 'YYYY,M,D');
	          repeat.week_days = [];
	          _findInstanceProperty($monthly_week_day).call($monthly_week_day, 'option').each(function () {
	            repeat.week_days.push($__default["default"](this).val());
	          });
	          repeat.checked_week_days = [];
	          $__default["default"]('.bookly-js-week-days input:checked', $repeat_container).each(function () {
	            repeat.checked_week_days.push(this.value);
	          });
	          var current_date = repeat.date_from.clone();
	          do {
	            if (repeat.isDateMatchesSelections(current_date)) {
	              number_of_times++;
	            }
	            current_date.add(1, 'days');
	          } while (number_of_times < repeat_times && current_date.isBefore(moment_until));
	          $date_until.val(current_date.subtract(1, 'days').format('MMMM D, YYYY'));
	          $date_until.pickadate('picker').set('select', new Date(current_date.format('YYYY'), current_date.format('M') - 1, current_date.format('D')));
	        },
	        updateRepeatTimes: function updateRepeatTimes() {
	          var _context2;
	          var number_of_times = 0,
	            date_from = _sliceInstanceProperty$1(_context2 = bound_date.min).call(_context2),
	            date_until = $date_until.pickadate('picker').get('select'),
	            moment_until = moment().year(date_until.year).month(date_until.month).date(date_until.date);
	          date_from[1]++;
	          repeat.date_from = moment(date_from.join(','), 'YYYY,M,D');
	          repeat.week_days = [];
	          _findInstanceProperty($monthly_week_day).call($monthly_week_day, 'option').each(function () {
	            repeat.week_days.push($__default["default"](this).val());
	          });
	          repeat.checked_week_days = [];
	          $__default["default"]('.bookly-js-week-days input:checked', $repeat_container).each(function () {
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
	      $date_until.pickadate({
	        formatSubmit: 'yyyy-mm-dd',
	        format: opt[params.form_id].date_format,
	        min: bound_date.min,
	        max: bound_date.max,
	        clear: false,
	        close: false,
	        today: BooklyL10n.today,
	        monthsFull: BooklyL10n.months,
	        weekdaysFull: BooklyL10n.days,
	        weekdaysShort: BooklyL10n.daysShort,
	        labelMonthNext: BooklyL10n.nextMonth,
	        labelMonthPrev: BooklyL10n.prevMonth,
	        firstDay: opt[params.form_id].firstDay,
	        onClose: function onClose() {
	          // Hide for skip tab navigations by days of the month when the calendar is closed
	          $__default["default"]('#' + $date_until.attr('aria-owns')).hide();
	        }
	      }).focusin(function () {
	        // Restore calendar visibility, changed on onClose
	        $__default["default"]('#' + $date_until.attr('aria-owns')).show();
	      });
	      var open_repeat_onchange = $repeat_enabled.on('change', function () {
	        $repeat_container.toggle($__default["default"](this).prop('checked'));
	        if ($__default["default"](this).prop('checked')) {
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
	        var until = repeat_data.until.split('-');
	        $date_until.pickadate('set').set('select', new Date(until[0], until[1] - 1, until[2]));
	        switch (_repeatInstanceProperty(repeat_data)) {
	          case 'daily':
	            $repeat_every_day.val(_everyInstanceProperty(repeat_params));
	            break;
	          case 'weekly':
	          //break skipped
	          case 'biweekly':
	            $__default["default"]('.bookly-js-week-days input[type="checkbox"]', $repeat_container).prop('checked', false).parent().removeClass('active');
	            _forEachInstanceProperty(_context3 = repeat_params.on).call(_context3, function (val) {
	              $__default["default"]('.bookly-js-week-days input:checkbox[value=' + val + ']', $repeat_container).prop('checked', true);
	            });
	            break;
	          case 'monthly':
	            if (repeat_params.on === 'day') {
	              $variant_monthly.val('specific');
	              $__default["default"]('.bookly-js-monthly-specific-day[value=' + repeat_params.day + ']', $repeat_container).prop('checked', true);
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
	      $__default["default"]('.bookly-js-week-days input', $repeat_container).on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      $monthly_specific_day.val(response.date_min[2]);
	      $monthly_specific_day.on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      $monthly_week_day.on('change', function () {
	        repeat.updateRepeatTimes();
	      });
	      $date_until.on('change', function () {
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
	        var data = {
	            action: 'bookly_recurring_appointments_get_customer_schedule',
	            form_id: params.form_id,
	            repeat: $repeat_variant.val(),
	            until: $date_until.pickadate('picker').get('select', 'yyyy-mm-dd'),
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
	            $__default["default"]('.bookly-js-week-days input[type="checkbox"]:checked', $variant_weekly).each(function () {
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
	        }).then(function (response) {
	          repeat.renderFullSchedule(response.data);
	          ladda.stop();
	        });
	      });
	      $__default["default"]('.bookly-js-back-step', $container).on('click', function (e) {
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
	        }).then(function (response) {
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
	      $__default["default"]('.bookly-js-go-to-cart', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepCart({
	          form_id: params.form_id,
	          from_step: 'repeat'
	        });
	      });
	      $__default["default"]('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $__default["default"].globalEval(customJS.next_button);
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
	          }).then(function (response) {
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
	          }).then(function (response) {
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
	  $__default["default"].extend(data, params);
	  var columnizerObserver = false;
	  var lastObserverTime = 0;
	  var lastObserverWidth = 0;

	  // Build slots html
	  function prepareSlotsHtml(slots_data, selected_date) {
	    var response = {};
	    $__default["default"].each(slots_data, function (group, group_slots) {
	      var html = '<button class="bookly-day" value="' + group + '">' + group_slots.title + '</button>';
	      $__default["default"].each(group_slots.slots, function (id, slot) {
	        html += '<button value="' + _JSON$stringify(slot.data).replace(/"/g, '&quot;') + '" data-group="' + group + '" class="bookly-hour' + (slot.special_hour ? ' bookly-slot-in-special-hour' : '') + (slot.status == 'waiting-list' ? ' bookly-slot-in-waiting-list' : slot.status == 'booked' ? ' booked' : '') + '"' + (slot.status == 'booked' ? ' disabled' : '') + '>' + '<span class="ladda-label bookly-time-main' + (slot.data[0][2] == selected_date ? ' bookly-bold' : '') + '">' + '<i class="bookly-hour-icon"><span></span></i>' + slot.time_text + '</span>' + '<span class="bookly-time-additional' + (slot.status == 'waiting-list' ? ' bookly-waiting-list' : '') + '"> ' + slot.additional_text + '</span>' + '</button>';
	      });
	      response[group] = html;
	    });
	    return response;
	  }
	  var requestRenderTime = requestCancellable(),
	    requestSessionSave = requestCancellable();
	  requestRenderTime.booklyAjax({
	    data: data
	  }).then(function (response) {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    var $columnizer_wrap = $__default["default"]('.bookly-columnizer-wrap', $container),
	      $columnizer = $__default["default"]('.bookly-columnizer', $columnizer_wrap),
	      $time_next_button = $__default["default"]('.bookly-time-next', $container),
	      $time_prev_button = $__default["default"]('.bookly-time-prev', $container),
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
	    $__default["default"]('.bookly-js-back-step', $container).on('click', function (e) {
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
	    $__default["default"]('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'time'
	      });
	    });

	    // Time zone switcher.
	    $__default["default"]('.bookly-js-time-zone-switcher', $container).on('change', function (e) {
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
	      // Init calendar.
	      var $input = $__default["default"]('.bookly-js-selected-date', $container);
	      $input.pickadate({
	        formatSubmit: 'yyyy-mm-dd',
	        format: opt[params.form_id].date_format,
	        min: response.date_min || true,
	        max: response.date_max || true,
	        weekdaysFull: BooklyL10n.days,
	        weekdaysShort: BooklyL10n.daysShort,
	        monthsFull: BooklyL10n.months,
	        labelMonthNext: BooklyL10n.nextMonth,
	        labelMonthPrev: BooklyL10n.prevMonth,
	        firstDay: opt[params.form_id].firstDay,
	        clear: false,
	        close: false,
	        today: false,
	        disable: response.disabled_days,
	        closeOnSelect: false,
	        klass: {
	          picker: 'picker picker--opened picker--focused'
	        },
	        onSet: function onSet(e) {
	          if (e.select) {
	            var date = this.get('select', 'yyyy-mm-dd');
	            if (slots[date]) {
	              // Get data from response.slots.
	              $columnizer.html(slots[date]).css('left', '0px');
	              columns = 0;
	              screen_index = 0;
	              $current_screen = null;
	              initSlots();
	              $time_prev_button.hide();
	              $time_next_button.toggle($screens.length != 1);
	            } else {
	              // Load new data from server.
	              requestRenderTime.cancel();
	              stepTime({
	                form_id: params.form_id,
	                selected_date: date
	              });
	              showSpinner();
	            }
	          }
	          this.open(); // Fix ultimate-member plugin
	        },

	        onClose: function onClose() {
	          this.open(false);
	        },
	        onRender: function onRender() {
	          var date = new Date(Date.UTC(this.get('view').year, this.get('view').month));
	          $__default["default"]('.picker__nav--next', $container).on('click', function (e) {
	            e.stopPropagation();
	            e.preventDefault();
	            date.setUTCMonth(date.getUTCMonth() + 1);
	            requestRenderTime.cancel();
	            stepTime({
	              form_id: params.form_id,
	              selected_date: date.toJSON().substr(0, 10)
	            });
	            showSpinner();
	          });
	          $__default["default"]('.picker__nav--prev', $container).on('click', function (e) {
	            e.stopPropagation();
	            e.preventDefault();
	            date.setUTCMonth(date.getUTCMonth() - 1);
	            requestRenderTime.cancel();
	            stepTime({
	              form_id: params.form_id,
	              selected_date: date.toJSON().substr(0, 10)
	            });
	            showSpinner();
	          });
	        }
	      });
	      // Insert slots for selected day.
	      var date = $input.pickadate('picker').get('select', 'yyyy-mm-dd');
	      $columnizer.html(slots[date]);
	    } else {
	      // Insert all slots.
	      var slots_data = '';
	      $__default["default"].each(slots, function (group, group_slots) {
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
	      slots_per_column = _parseInt($__default["default"](window).height() / slot_height, 10);
	      if (slots_per_column < 4) {
	        slots_per_column = 4;
	      } else if (slots_per_column > 10) {
	        slots_per_column = 10;
	      }
	      var hammertime = $__default["default"]('.bookly-time-step', $container).hammer({
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
	          var $button = $__default["default"]('> button:last', $columnizer);
	          if ($button.length === 0) {
	            $button = $__default["default"]('.bookly-column:hidden:last > button:last', $columnizer);
	            if ($button.length === 0) {
	              $button = $__default["default"]('.bookly-column:last > button:last', $columnizer);
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
	          }).then(function (response) {
	            if (response.has_slots) {
	              // if there are available time
	              has_more_slots = response.has_more_slots;
	              var slots_data = '';
	              $__default["default"].each(prepareSlotsHtml(response.slots_data, response.selected_date), function (group, group_slots) {
	                slots_data += group_slots;
	              });
	              var $html = $__default["default"](slots_data);
	              // The first slot is always a day slot.
	              // Check if such day slot already exists (this can happen
	              // because of time zone offset) and then remove the first slot.
	              var $first_day = $html.eq(0);
	              if ($__default["default"]('button.bookly-day[value="' + $first_day.attr('value') + '"]', $container).length) {
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
	          }).catch(function (response) {
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
	      $__default["default"]('.bookly-time-screen,.bookly-not-time-screen', $container).addClass('bookly-spin-overlay');
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
	        new Spinner(opts).spin($__default["default"]('.bookly-not-time-screen', $container).get(0));
	      }
	    }
	    function initSlots() {
	      var $buttons = $__default["default"]('> button', $columnizer),
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
	            $column = $__default["default"]('<div class="' + column_class + '" />');
	            $button = $__default["default"](_spliceInstanceProperty($buttons).call($buttons, 0, 1));
	            $button.addClass('bookly-js-first-child');
	            $column.append($button);
	          } else {
	            slots_count++;
	            $button = $__default["default"](_spliceInstanceProperty($buttons).call($buttons, 0, 1));
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
	          $column = $__default["default"]('<div class="' + column_class + '" />');
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
	            $button = $__default["default"](_spliceInstanceProperty($buttons).call($buttons, 0, 1));
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
	      var $columns = $__default["default"]('> .bookly-column', $columnizer);
	      while (has_more_slots ? $columns.length >= columns_per_screen : $columns.length) {
	        $screen = $__default["default"]('<div class="bookly-time-screen"/>');
	        for (var i = 0; i < columns_per_screen; ++i) {
	          $column = $__default["default"](_spliceInstanceProperty($columns).call($columns, 0, 1));
	          if (i == 0) {
	            $column.addClass('bookly-js-first-column');
	            var $first_slot = _findInstanceProperty($column).call($column, '.bookly-js-first-child');
	            // In the first column the first slot is time.
	            if (!$first_slot.hasClass('bookly-day')) {
	              var group = $first_slot.data('group'),
	                $group_slot = $__default["default"]('button.bookly-day[value="' + group + '"]:last', $container);
	              // Copy group slot to the first column.
	              $column.prepend($group_slot.clone());
	            }
	          }
	          $screen.append($column);
	        }
	        $columnizer.append($screen);
	      }
	      $screens = $__default["default"]('.bookly-time-screen', $columnizer);
	      if ($current_screen === null) {
	        $current_screen = $screens.eq(0);
	      }
	      $__default["default"]('button.bookly-time-skip', $container).off('click').on('click', function (e) {
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
	      $__default["default"]('button.bookly-hour', $container).off('click').on('click', function (e) {
	        requestSessionSave.cancel();
	        e.stopPropagation();
	        e.preventDefault();
	        var $this = $__default["default"](this),
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
	            $__default["default"].globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }
	        requestSessionSave.booklyAjax({
	          type: 'POST',
	          data: data
	        }).then(function (response) {
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
	      $__default["default"]('.bookly-time-step', $container).width(columns_per_screen * column_width);
	      $columnizer_wrap.height($current_screen.height());
	    }
	    function observeResizeColumnizer() {
	      if ($__default["default"]('.bookly-time-step', $container).length > 0) {
	        var time = new Date().getTime();
	        if (time - lastObserverTime > 200) {
	          var formWidth = $columnizer_wrap.closest('.bookly-form').width();
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
	        var formWidth = $columnizer_wrap.closest('.bookly-form').width();
	        if (show_calendar) {
	          var calendarWidth = $__default["default"]('.bookly-js-slot-calendar', $container).width();
	          if (formWidth > calendarWidth + column_width + 24) {
	            columns_per_screen = _parseInt((formWidth - calendarWidth - 24) / column_width, 10);
	          } else {
	            columns_per_screen = _parseInt(formWidth / column_width, 10);
	          }
	        } else {
	          columns_per_screen = _parseInt(formWidth / column_width, 10);
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
	    if (typeof ResizeObserver === "undefined" || (typeof ResizeObserver === "undefined" ? "undefined" : _typeof(ResizeObserver)) === undefined) {
	      resizeColumnizer();
	    } else {
	      columnizerObserver = new ResizeObserver(observeResizeColumnizer);
	      columnizerObserver.observe($container.get(0));
	    }
	  }).catch(function (response) {
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
	  $__default["default"].extend(data, params);
	  booklyAjax({
	    data: data
	  }).then(function (response) {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    var $next_step = $__default["default"]('.bookly-js-next-step', $container),
	      $back_step = $__default["default"]('.bookly-js-back-step', $container),
	      $goto_cart = $__default["default"]('.bookly-js-go-to-cart', $container),
	      $extras_items = $__default["default"]('.bookly-js-extras-item', $container),
	      $extras_summary = $__default["default"]('.bookly-js-extras-summary span', $container),
	      customJS = response.custom_js,
	      $this,
	      $input,
	      format = new Format(response);
	    var extrasChanged = function extrasChanged($extras_item, quantity) {
	      var $input = _findInstanceProperty($extras_item).call($extras_item, 'input'),
	        $total = _findInstanceProperty($extras_item).call($extras_item, '.bookly-js-extras-total-price'),
	        total_price = quantity * _parseFloat($extras_item.data('price'));
	      $total.text(format.price(total_price));
	      $input.val(quantity);
	      _findInstanceProperty($extras_item).call($extras_item, '.bookly-js-extras-thumb').toggleClass('bookly-extras-selected', quantity > 0);

	      // Updating summary
	      var amount = 0;
	      $extras_items.each(function (index, elem) {
	        var $this = $__default["default"](this),
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
	      var $this = $__default["default"](this);
	      var $input = _findInstanceProperty($this).call($this, 'input');
	      $__default["default"]('.bookly-js-extras-thumb', $this).on('click', function () {
	        extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	      }).keypress(function (e) {
	        e.preventDefault();
	        if (e.which == 13 || e.which == 32) {
	          extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	        }
	      });
	      _findInstanceProperty($this).call($this, '.bookly-js-count-control').on('click', function () {
	        var count = _parseInt($input.val());
	        count = $__default["default"](this).hasClass('bookly-js-extras-increment') ? Math.min($this.data('max_quantity'), count + 1) : Math.max($this.data('min_quantity'), count - 1);
	        extrasChanged($this, count);
	      });
	      setInputFilter($input.get(0), function (value) {
	        var valid = /^\d*$/.test(value) && (value === '' || _parseInt(value) <= $this.data('max_quantity') && _parseInt(value) >= $this.data('min_quantity'));
	        if (valid) {
	          extrasChanged($this, value === '' ? $this.data('min_quantity') : _parseInt(value));
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
	          $__default["default"].globalEval(customJS.next_button);
	        } catch (e) {
	          // Do nothing
	        }
	      }
	      var extras = {};
	      $__default["default"]('.bookly-js-extras-container', $container).each(function () {
	        var $extras_container = $__default["default"](this);
	        var chain_id = $extras_container.data('chain');
	        var chain_extras = {};
	        // Get checked extras for chain.
	        _findInstanceProperty($extras_container).call($extras_container, '.bookly-js-extras-item').each(function (index, elem) {
	          $this = $__default["default"](this);
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
	      }).then(function (response) {
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

	function _assertThisInitialized(self) {
	  if (self === void 0) {
	    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
	  }
	  return self;
	}

	var create$9 = {exports: {}};

	var create$8 = {exports: {}};

	// TODO: Remove from `core-js@4`
	var $$C = _export;
	var DESCRIPTORS$5 = descriptors;
	var create$7 = objectCreate;

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	$$C({ target: 'Object', stat: true, sham: !DESCRIPTORS$5 }, {
	  create: create$7
	});

	var path$f = path$s;

	var Object$2 = path$f.Object;

	var create$6 = function create(P, D) {
	  return Object$2.create(P, D);
	};

	var parent$R = create$6;

	var create$5 = parent$R;

	var parent$Q = create$5;

	var create$4 = parent$Q;

	var parent$P = create$4;

	var create$3 = parent$P;

	(function (module) {
		module.exports = create$3;
	} (create$8));

	(function (module) {
		module.exports = create$8.exports;
	} (create$9));

	var _Object$create$1 = /*@__PURE__*/getDefaultExportFromCjs(create$9.exports);

	var setPrototypeOf$6 = {exports: {}};

	var setPrototypeOf$5 = {exports: {}};

	var $$B = _export;
	var setPrototypeOf$4 = objectSetPrototypeOf;

	// `Object.setPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.setprototypeof
	$$B({ target: 'Object', stat: true }, {
	  setPrototypeOf: setPrototypeOf$4
	});

	var path$e = path$s;

	var setPrototypeOf$3 = path$e.Object.setPrototypeOf;

	var parent$O = setPrototypeOf$3;

	var setPrototypeOf$2 = parent$O;

	var parent$N = setPrototypeOf$2;

	var setPrototypeOf$1 = parent$N;

	var parent$M = setPrototypeOf$1;

	var setPrototypeOf = parent$M;

	(function (module) {
		module.exports = setPrototypeOf;
	} (setPrototypeOf$5));

	(function (module) {
		module.exports = setPrototypeOf$5.exports;
	} (setPrototypeOf$6));

	var _Object$setPrototypeOf = /*@__PURE__*/getDefaultExportFromCjs(setPrototypeOf$6.exports);

	var bind$j = {exports: {}};

	var bind$i = {exports: {}};

	var uncurryThis$7 = functionUncurryThis;
	var aCallable$d = aCallable$n;
	var isObject$2 = isObject$m;
	var hasOwn$2 = hasOwnProperty_1;
	var arraySlice$2 = arraySlice$6;
	var NATIVE_BIND = functionBindNative;

	var $Function = Function;
	var concat$1 = uncurryThis$7([].concat);
	var join = uncurryThis$7([].join);
	var factories = {};

	var construct$7 = function (C, argsLength, args) {
	  if (!hasOwn$2(factories, argsLength)) {
	    for (var list = [], i = 0; i < argsLength; i++) list[i] = 'a[' + i + ']';
	    factories[argsLength] = $Function('C,a', 'return new C(' + join(list, ',') + ')');
	  } return factories[argsLength](C, args);
	};

	// `Function.prototype.bind` method implementation
	// https://tc39.es/ecma262/#sec-function.prototype.bind
	var functionBind = NATIVE_BIND ? $Function.bind : function bind(that /* , ...args */) {
	  var F = aCallable$d(this);
	  var Prototype = F.prototype;
	  var partArgs = arraySlice$2(arguments, 1);
	  var boundFunction = function bound(/* args... */) {
	    var args = concat$1(partArgs, arraySlice$2(arguments));
	    return this instanceof boundFunction ? construct$7(F, args.length, args) : F.apply(that, args);
	  };
	  if (isObject$2(Prototype)) boundFunction.prototype = Prototype;
	  return boundFunction;
	};

	// TODO: Remove from `core-js@4`
	var $$A = _export;
	var bind$h = functionBind;

	// `Function.prototype.bind` method
	// https://tc39.es/ecma262/#sec-function.prototype.bind
	$$A({ target: 'Function', proto: true, forced: Function.bind !== bind$h }, {
	  bind: bind$h
	});

	var entryVirtual$4 = entryVirtual$i;

	var bind$g = entryVirtual$4('Function').bind;

	var isPrototypeOf$4 = objectIsPrototypeOf;
	var method$4 = bind$g;

	var FunctionPrototype = Function.prototype;

	var bind$f = function (it) {
	  var own = it.bind;
	  return it === FunctionPrototype || (isPrototypeOf$4(FunctionPrototype, it) && own === FunctionPrototype.bind) ? method$4 : own;
	};

	var parent$L = bind$f;

	var bind$e = parent$L;

	var parent$K = bind$e;

	var bind$d = parent$K;

	var parent$J = bind$d;

	var bind$c = parent$J;

	(function (module) {
		module.exports = bind$c;
	} (bind$i));

	(function (module) {
		module.exports = bind$i.exports;
	} (bind$j));

	var _bindInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(bind$j.exports);

	function _setPrototypeOf(o, p) {
	  var _context;
	  _setPrototypeOf = _Object$setPrototypeOf ? _bindInstanceProperty(_context = _Object$setPrototypeOf).call(_context) : function _setPrototypeOf(o, p) {
	    o.__proto__ = p;
	    return o;
	  };
	  return _setPrototypeOf(o, p);
	}

	function _inherits(subClass, superClass) {
	  if (typeof superClass !== "function" && superClass !== null) {
	    throw new TypeError("Super expression must either be null or a function");
	  }
	  subClass.prototype = _Object$create$1(superClass && superClass.prototype, {
	    constructor: {
	      value: subClass,
	      writable: true,
	      configurable: true
	    }
	  });
	  _Object$defineProperty(subClass, "prototype", {
	    writable: false
	  });
	  if (superClass) _setPrototypeOf(subClass, superClass);
	}

	function _possibleConstructorReturn(self, call) {
	  if (call && (_typeof(call) === "object" || typeof call === "function")) {
	    return call;
	  } else if (call !== void 0) {
	    throw new TypeError("Derived constructors may only return object or undefined");
	  }
	  return _assertThisInitialized(self);
	}

	var getPrototypeOf$6 = {exports: {}};

	var getPrototypeOf$5 = {exports: {}};

	var $$z = _export;
	var fails$5 = fails$x;
	var toObject$5 = toObject$c;
	var nativeGetPrototypeOf = objectGetPrototypeOf;
	var CORRECT_PROTOTYPE_GETTER = correctPrototypeGetter;

	var FAILS_ON_PRIMITIVES$2 = fails$5(function () { nativeGetPrototypeOf(1); });

	// `Object.getPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.getprototypeof
	$$z({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$2, sham: !CORRECT_PROTOTYPE_GETTER }, {
	  getPrototypeOf: function getPrototypeOf(it) {
	    return nativeGetPrototypeOf(toObject$5(it));
	  }
	});

	var path$d = path$s;

	var getPrototypeOf$4 = path$d.Object.getPrototypeOf;

	var parent$I = getPrototypeOf$4;

	var getPrototypeOf$3 = parent$I;

	var parent$H = getPrototypeOf$3;

	var getPrototypeOf$2 = parent$H;

	var parent$G = getPrototypeOf$2;

	var getPrototypeOf$1 = parent$G;

	(function (module) {
		module.exports = getPrototypeOf$1;
	} (getPrototypeOf$5));

	(function (module) {
		module.exports = getPrototypeOf$5.exports;
	} (getPrototypeOf$6));

	var _Object$getPrototypeOf = /*@__PURE__*/getDefaultExportFromCjs(getPrototypeOf$6.exports);

	function _getPrototypeOf(o) {
	  var _context;
	  _getPrototypeOf = _Object$setPrototypeOf ? _bindInstanceProperty(_context = _Object$getPrototypeOf).call(_context) : function _getPrototypeOf(o) {
	    return o.__proto__ || _Object$getPrototypeOf(o);
	  };
	  return _getPrototypeOf(o);
	}

	var isArray$8 = {exports: {}};

	var isArray$7 = {exports: {}};

	var $$y = _export;
	var isArray$6 = isArray$e;

	// `Array.isArray` method
	// https://tc39.es/ecma262/#sec-array.isarray
	$$y({ target: 'Array', stat: true }, {
	  isArray: isArray$6
	});

	var path$c = path$s;

	var isArray$5 = path$c.Array.isArray;

	var parent$F = isArray$5;

	var isArray$4 = parent$F;

	var parent$E = isArray$4;

	var isArray$3 = parent$E;

	var parent$D = isArray$3;

	var isArray$2 = parent$D;

	(function (module) {
		module.exports = isArray$2;
	} (isArray$7));

	(function (module) {
		module.exports = isArray$7.exports;
	} (isArray$8));

	var _Array$isArray = /*@__PURE__*/getDefaultExportFromCjs(isArray$8.exports);

	function _arrayWithHoles(arr) {
	  if (_Array$isArray(arr)) return arr;
	}

	var getIteratorMethod$6 = {exports: {}};

	var getIteratorMethod$5 = {exports: {}};

	var getIteratorMethod$4 = getIteratorMethod$9;

	var getIteratorMethod_1 = getIteratorMethod$4;

	var parent$C = getIteratorMethod_1;


	var getIteratorMethod$3 = parent$C;

	var parent$B = getIteratorMethod$3;

	var getIteratorMethod$2 = parent$B;

	var parent$A = getIteratorMethod$2;

	var getIteratorMethod$1 = parent$A;

	(function (module) {
		module.exports = getIteratorMethod$1;
	} (getIteratorMethod$5));

	(function (module) {
		module.exports = getIteratorMethod$5.exports;
	} (getIteratorMethod$6));

	var _getIteratorMethod = /*@__PURE__*/getDefaultExportFromCjs(getIteratorMethod$6.exports);

	function _iterableToArrayLimit(arr, i) {
	  var _i = arr == null ? null : typeof _Symbol !== "undefined" && _getIteratorMethod(arr) || arr["@@iterator"];
	  if (_i == null) return;
	  var _arr = [];
	  var _n = true;
	  var _d = false;
	  var _s, _e;
	  try {
	    for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) {
	      _arr.push(_s.value);
	      if (i && _arr.length === i) break;
	    }
	  } catch (err) {
	    _d = true;
	    _e = err;
	  } finally {
	    try {
	      if (!_n && _i["return"] != null) _i["return"]();
	    } finally {
	      if (_d) throw _e;
	    }
	  }
	  return _arr;
	}

	var slice$3 = {exports: {}};

	var slice$2 = {exports: {}};

	var parent$z = slice$4;

	var slice$1 = parent$z;

	var parent$y = slice$1;

	var slice = parent$y;

	(function (module) {
		module.exports = slice;
	} (slice$2));

	(function (module) {
		module.exports = slice$2.exports;
	} (slice$3));

	var _sliceInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(slice$3.exports);

	var from$8 = {exports: {}};

	var from$7 = {exports: {}};

	var anObject$h = anObject$u;
	var iteratorClose = iteratorClose$2;

	// call something on iterator step with safe closing on error
	var callWithSafeIterationClosing$1 = function (iterator, fn, value, ENTRIES) {
	  try {
	    return ENTRIES ? fn(anObject$h(value)[0], value[1]) : fn(value);
	  } catch (error) {
	    iteratorClose(iterator, 'throw', error);
	  }
	};

	var bind$b = functionBindContext;
	var call$c = functionCall;
	var toObject$4 = toObject$c;
	var callWithSafeIterationClosing = callWithSafeIterationClosing$1;
	var isArrayIteratorMethod = isArrayIteratorMethod$2;
	var isConstructor = isConstructor$4;
	var lengthOfArrayLike$2 = lengthOfArrayLike$a;
	var createProperty$1 = createProperty$6;
	var getIterator$2 = getIterator$4;
	var getIteratorMethod = getIteratorMethod$9;

	var $Array = Array;

	// `Array.from` method implementation
	// https://tc39.es/ecma262/#sec-array.from
	var arrayFrom = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
	  var O = toObject$4(arrayLike);
	  var IS_CONSTRUCTOR = isConstructor(this);
	  var argumentsLength = arguments.length;
	  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
	  var mapping = mapfn !== undefined;
	  if (mapping) mapfn = bind$b(mapfn, argumentsLength > 2 ? arguments[2] : undefined);
	  var iteratorMethod = getIteratorMethod(O);
	  var index = 0;
	  var length, result, step, iterator, next, value;
	  // if the target is not iterable or it's an array with the default iterator - use a simple case
	  if (iteratorMethod && !(this === $Array && isArrayIteratorMethod(iteratorMethod))) {
	    iterator = getIterator$2(O, iteratorMethod);
	    next = iterator.next;
	    result = IS_CONSTRUCTOR ? new this() : [];
	    for (;!(step = call$c(next, iterator)).done; index++) {
	      value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
	      createProperty$1(result, index, value);
	    }
	  } else {
	    length = lengthOfArrayLike$2(O);
	    result = IS_CONSTRUCTOR ? new this(length) : $Array(length);
	    for (;length > index; index++) {
	      value = mapping ? mapfn(O[index], index) : O[index];
	      createProperty$1(result, index, value);
	    }
	  }
	  result.length = index;
	  return result;
	};

	var $$x = _export;
	var from$6 = arrayFrom;
	var checkCorrectnessOfIteration = checkCorrectnessOfIteration$2;

	var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
	  // eslint-disable-next-line es/no-array-from -- required for testing
	  Array.from(iterable);
	});

	// `Array.from` method
	// https://tc39.es/ecma262/#sec-array.from
	$$x({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
	  from: from$6
	});

	var path$b = path$s;

	var from$5 = path$b.Array.from;

	var parent$x = from$5;

	var from$4 = parent$x;

	var parent$w = from$4;

	var from$3 = parent$w;

	var parent$v = from$3;

	var from$2 = parent$v;

	(function (module) {
		module.exports = from$2;
	} (from$7));

	(function (module) {
		module.exports = from$7.exports;
	} (from$8));

	var _Array$from$1 = /*@__PURE__*/getDefaultExportFromCjs(from$8.exports);

	function _arrayLikeToArray(arr, len) {
	  if (len == null || len > arr.length) len = arr.length;
	  for (var i = 0, arr2 = new Array(len); i < len; i++) {
	    arr2[i] = arr[i];
	  }
	  return arr2;
	}

	function _unsupportedIterableToArray(o, minLen) {
	  var _context;
	  if (!o) return;
	  if (typeof o === "string") return _arrayLikeToArray(o, minLen);
	  var n = _sliceInstanceProperty(_context = Object.prototype.toString.call(o)).call(_context, 8, -1);
	  if (n === "Object" && o.constructor) n = o.constructor.name;
	  if (n === "Map" || n === "Set") return _Array$from$1(o);
	  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
	}

	function _nonIterableRest() {
	  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	function _slicedToArray(arr, i) {
	  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
	}

	var map$6 = {exports: {}};

	var defineProperty$1 = objectDefineProperty.f;
	var create$2 = objectCreate;
	var defineBuiltIns = defineBuiltIns$3;
	var bind$a = functionBindContext;
	var anInstance = anInstance$4;
	var isNullOrUndefined$2 = isNullOrUndefined$8;
	var iterate$e = iterate$m;
	var defineIterator = iteratorDefine;
	var createIterResultObject = createIterResultObject$3;
	var setSpecies = setSpecies$2;
	var DESCRIPTORS$4 = descriptors;
	var fastKey = internalMetadata.exports.fastKey;
	var InternalStateModule = internalState;

	var setInternalState = InternalStateModule.set;
	var internalStateGetterFor = InternalStateModule.getterFor;

	var collectionStrong$2 = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var Constructor = wrapper(function (that, iterable) {
	      anInstance(that, Prototype);
	      setInternalState(that, {
	        type: CONSTRUCTOR_NAME,
	        index: create$2(null),
	        first: undefined,
	        last: undefined,
	        size: 0
	      });
	      if (!DESCRIPTORS$4) that.size = 0;
	      if (!isNullOrUndefined$2(iterable)) iterate$e(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
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
	          next: undefined,
	          removed: false
	        };
	        if (!state.first) state.first = entry;
	        if (previous) previous.next = entry;
	        if (DESCRIPTORS$4) state.size++;
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
	        if (entry.key == key) return entry;
	      }
	    };

	    defineBuiltIns(Prototype, {
	      // `{ Map, Set }.prototype.clear()` methods
	      // https://tc39.es/ecma262/#sec-map.prototype.clear
	      // https://tc39.es/ecma262/#sec-set.prototype.clear
	      clear: function clear() {
	        var that = this;
	        var state = getInternalState(that);
	        var data = state.index;
	        var entry = state.first;
	        while (entry) {
	          entry.removed = true;
	          if (entry.previous) entry.previous = entry.previous.next = undefined;
	          delete data[entry.index];
	          entry = entry.next;
	        }
	        state.first = state.last = undefined;
	        if (DESCRIPTORS$4) state.size = 0;
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
	          if (state.first == entry) state.first = next;
	          if (state.last == entry) state.last = prev;
	          if (DESCRIPTORS$4) state.size--;
	          else that.size--;
	        } return !!entry;
	      },
	      // `{ Map, Set }.prototype.forEach(callbackfn, thisArg = undefined)` methods
	      // https://tc39.es/ecma262/#sec-map.prototype.foreach
	      // https://tc39.es/ecma262/#sec-set.prototype.foreach
	      forEach: function forEach(callbackfn /* , that = undefined */) {
	        var state = getInternalState(this);
	        var boundFunction = bind$a(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
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
	    if (DESCRIPTORS$4) defineProperty$1(Prototype, 'size', {
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
	        last: undefined
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
	        state.target = undefined;
	        return createIterResultObject(undefined, true);
	      }
	      // return step by kind
	      if (kind == 'keys') return createIterResultObject(entry.key, false);
	      if (kind == 'values') return createIterResultObject(entry.value, false);
	      return createIterResultObject([entry.key, entry.value], false);
	    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

	    // `{ Map, Set }.prototype[@@species]` accessors
	    // https://tc39.es/ecma262/#sec-get-map-@@species
	    // https://tc39.es/ecma262/#sec-get-set-@@species
	    setSpecies(CONSTRUCTOR_NAME);
	  }
	};

	var collection$1 = collection$3;
	var collectionStrong$1 = collectionStrong$2;

	// `Map` constructor
	// https://tc39.es/ecma262/#sec-map-objects
	collection$1('Map', function (init) {
	  return function Map() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong$1);

	var path$a = path$s;

	var map$5 = path$a.Map;

	var parent$u = map$5;


	var map$4 = parent$u;

	(function (module) {
		module.exports = map$4;
	} (map$6));

	var _Map = /*@__PURE__*/getDefaultExportFromCjs(map$6.exports);

	var construct$6 = {exports: {}};

	var $$w = _export;
	var getBuiltIn$3 = getBuiltIn$h;
	var apply = functionApply;
	var bind$9 = functionBind;
	var aConstructor$1 = aConstructor$3;
	var anObject$g = anObject$u;
	var isObject$1 = isObject$m;
	var create$1 = objectCreate;
	var fails$4 = fails$x;

	var nativeConstruct = getBuiltIn$3('Reflect', 'construct');
	var ObjectPrototype = Object.prototype;
	var push$4 = [].push;

	// `Reflect.construct` method
	// https://tc39.es/ecma262/#sec-reflect.construct
	// MS Edge supports only 2 arguments and argumentsList argument is optional
	// FF Nightly sets third argument as `new.target`, but does not create `this` from it
	var NEW_TARGET_BUG = fails$4(function () {
	  function F() { /* empty */ }
	  return !(nativeConstruct(function () { /* empty */ }, [], F) instanceof F);
	});

	var ARGS_BUG = !fails$4(function () {
	  nativeConstruct(function () { /* empty */ });
	});

	var FORCED$2 = NEW_TARGET_BUG || ARGS_BUG;

	$$w({ target: 'Reflect', stat: true, forced: FORCED$2, sham: FORCED$2 }, {
	  construct: function construct(Target, args /* , newTarget */) {
	    aConstructor$1(Target);
	    anObject$g(args);
	    var newTarget = arguments.length < 3 ? Target : aConstructor$1(arguments[2]);
	    if (ARGS_BUG && !NEW_TARGET_BUG) return nativeConstruct(Target, args, newTarget);
	    if (Target == newTarget) {
	      // w/o altered newTarget, optimization for 0-4 arguments
	      switch (args.length) {
	        case 0: return new Target();
	        case 1: return new Target(args[0]);
	        case 2: return new Target(args[0], args[1]);
	        case 3: return new Target(args[0], args[1], args[2]);
	        case 4: return new Target(args[0], args[1], args[2], args[3]);
	      }
	      // w/o altered newTarget, lot of arguments case
	      var $args = [null];
	      apply(push$4, $args, args);
	      return new (apply(bind$9, Target, $args))();
	    }
	    // with altered newTarget, not support built-in constructors
	    var proto = newTarget.prototype;
	    var instance = create$1(isObject$1(proto) ? proto : ObjectPrototype);
	    var result = apply(Target, instance, args);
	    return isObject$1(result) ? result : instance;
	  }
	});

	var path$9 = path$s;

	var construct$5 = path$9.Reflect.construct;

	var parent$t = construct$5;

	var construct$4 = parent$t;

	(function (module) {
		module.exports = construct$4;
	} (construct$6));

	var _Reflect$construct = /*@__PURE__*/getDefaultExportFromCjs(construct$6.exports);

	var isArray$1 = {exports: {}};

	(function (module) {
		module.exports = isArray$4;
	} (isArray$1));

	var map$3 = {exports: {}};

	var map$2 = {exports: {}};

	var parent$s = map$4;

	var map$1 = parent$s;

	// https://tc39.github.io/proposal-setmap-offrom/
	var bind$8 = functionBindContext;
	var call$b = functionCall;
	var aCallable$c = aCallable$n;
	var aConstructor = aConstructor$3;
	var isNullOrUndefined$1 = isNullOrUndefined$8;
	var iterate$d = iterate$m;

	var push$3 = [].push;

	var collectionFrom = function from(source /* , mapFn, thisArg */) {
	  var length = arguments.length;
	  var mapFn = length > 1 ? arguments[1] : undefined;
	  var mapping, array, n, boundFunction;
	  aConstructor(this);
	  mapping = mapFn !== undefined;
	  if (mapping) aCallable$c(mapFn);
	  if (isNullOrUndefined$1(source)) return new this();
	  array = [];
	  if (mapping) {
	    n = 0;
	    boundFunction = bind$8(mapFn, length > 2 ? arguments[2] : undefined);
	    iterate$d(source, function (nextItem) {
	      call$b(push$3, array, boundFunction(nextItem, n++));
	    });
	  } else {
	    iterate$d(source, push$3, { that: array });
	  }
	  return new this(array);
	};

	var $$v = _export;
	var from$1 = collectionFrom;

	// `Map.from` method
	// https://tc39.github.io/proposal-setmap-offrom/#sec-map.from
	$$v({ target: 'Map', stat: true, forced: true }, {
	  from: from$1
	});

	var arraySlice$1 = arraySlice$6;

	// https://tc39.github.io/proposal-setmap-offrom/
	var collectionOf = function of() {
	  return new this(arraySlice$1(arguments));
	};

	var $$u = _export;
	var of = collectionOf;

	// `Map.of` method
	// https://tc39.github.io/proposal-setmap-offrom/#sec-map.of
	$$u({ target: 'Map', stat: true, forced: true }, {
	  of: of
	});

	var call$a = functionCall;
	var aCallable$b = aCallable$n;
	var anObject$f = anObject$u;

	// https://github.com/tc39/collection-methods
	var collectionDeleteAll = function deleteAll(/* ...elements */) {
	  var collection = anObject$f(this);
	  var remover = aCallable$b(collection['delete']);
	  var allDeleted = true;
	  var wasDeleted;
	  for (var k = 0, len = arguments.length; k < len; k++) {
	    wasDeleted = call$a(remover, collection, arguments[k]);
	    allDeleted = allDeleted && wasDeleted;
	  }
	  return !!allDeleted;
	};

	var $$t = _export;
	var deleteAll = collectionDeleteAll;

	// `Map.prototype.deleteAll` method
	// https://github.com/tc39/proposal-collection-methods
	$$t({ target: 'Map', proto: true, real: true, forced: true }, {
	  deleteAll: deleteAll
	});

	var call$9 = functionCall;
	var aCallable$a = aCallable$n;
	var anObject$e = anObject$u;

	// `Map.prototype.emplace` method
	// https://github.com/thumbsupep/proposal-upsert
	var mapEmplace = function emplace(key, handler) {
	  var map = anObject$e(this);
	  var get = aCallable$a(map.get);
	  var has = aCallable$a(map.has);
	  var set = aCallable$a(map.set);
	  var value, inserted;
	  if (call$9(has, map, key)) {
	    value = call$9(get, map, key);
	    if ('update' in handler) {
	      value = handler.update(value, key, map);
	      call$9(set, map, key, value);
	    } return value;
	  }
	  inserted = handler.insert(key, map);
	  call$9(set, map, key, inserted);
	  return inserted;
	};

	var $$s = _export;
	var emplace = mapEmplace;

	// `Map.prototype.emplace` method
	// https://github.com/thumbsupep/proposal-upsert
	$$s({ target: 'Map', proto: true, real: true, forced: true }, {
	  emplace: emplace
	});

	var getIterator$1 = getIterator$4;

	var getMapIterator$a = getIterator$1;

	var $$r = _export;
	var anObject$d = anObject$u;
	var bind$7 = functionBindContext;
	var getMapIterator$9 = getMapIterator$a;
	var iterate$c = iterate$m;

	// `Map.prototype.every` method
	// https://github.com/tc39/proposal-collection-methods
	$$r({ target: 'Map', proto: true, real: true, forced: true }, {
	  every: function every(callbackfn /* , thisArg */) {
	    var map = anObject$d(this);
	    var iterator = getMapIterator$9(map);
	    var boundFunction = bind$7(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    return !iterate$c(iterator, function (key, value, stop) {
	      if (!boundFunction(value, key, map)) return stop();
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).stopped;
	  }
	});

	var $$q = _export;
	var getBuiltIn$2 = getBuiltIn$h;
	var bind$6 = functionBindContext;
	var call$8 = functionCall;
	var aCallable$9 = aCallable$n;
	var anObject$c = anObject$u;
	var speciesConstructor$2 = speciesConstructor$5;
	var getMapIterator$8 = getMapIterator$a;
	var iterate$b = iterate$m;

	// `Map.prototype.filter` method
	// https://github.com/tc39/proposal-collection-methods
	$$q({ target: 'Map', proto: true, real: true, forced: true }, {
	  filter: function filter(callbackfn /* , thisArg */) {
	    var map = anObject$c(this);
	    var iterator = getMapIterator$8(map);
	    var boundFunction = bind$6(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    var newMap = new (speciesConstructor$2(map, getBuiltIn$2('Map')))();
	    var setter = aCallable$9(newMap.set);
	    iterate$b(iterator, function (key, value) {
	      if (boundFunction(value, key, map)) call$8(setter, newMap, key, value);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    return newMap;
	  }
	});

	var $$p = _export;
	var anObject$b = anObject$u;
	var bind$5 = functionBindContext;
	var getMapIterator$7 = getMapIterator$a;
	var iterate$a = iterate$m;

	// `Map.prototype.find` method
	// https://github.com/tc39/proposal-collection-methods
	$$p({ target: 'Map', proto: true, real: true, forced: true }, {
	  find: function find(callbackfn /* , thisArg */) {
	    var map = anObject$b(this);
	    var iterator = getMapIterator$7(map);
	    var boundFunction = bind$5(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    return iterate$a(iterator, function (key, value, stop) {
	      if (boundFunction(value, key, map)) return stop(value);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).result;
	  }
	});

	var $$o = _export;
	var anObject$a = anObject$u;
	var bind$4 = functionBindContext;
	var getMapIterator$6 = getMapIterator$a;
	var iterate$9 = iterate$m;

	// `Map.prototype.findKey` method
	// https://github.com/tc39/proposal-collection-methods
	$$o({ target: 'Map', proto: true, real: true, forced: true }, {
	  findKey: function findKey(callbackfn /* , thisArg */) {
	    var map = anObject$a(this);
	    var iterator = getMapIterator$6(map);
	    var boundFunction = bind$4(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    return iterate$9(iterator, function (key, value, stop) {
	      if (boundFunction(value, key, map)) return stop(key);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).result;
	  }
	});

	var $$n = _export;
	var call$7 = functionCall;
	var uncurryThis$6 = functionUncurryThis;
	var aCallable$8 = aCallable$n;
	var getIterator = getIterator$4;
	var iterate$8 = iterate$m;

	var push$2 = uncurryThis$6([].push);

	// `Map.groupBy` method
	// https://github.com/tc39/proposal-collection-methods
	$$n({ target: 'Map', stat: true, forced: true }, {
	  groupBy: function groupBy(iterable, keyDerivative) {
	    aCallable$8(keyDerivative);
	    var iterator = getIterator(iterable);
	    var newMap = new this();
	    var has = aCallable$8(newMap.has);
	    var get = aCallable$8(newMap.get);
	    var set = aCallable$8(newMap.set);
	    iterate$8(iterator, function (element) {
	      var derivedKey = keyDerivative(element);
	      if (!call$7(has, newMap, derivedKey)) call$7(set, newMap, derivedKey, [element]);
	      else push$2(call$7(get, newMap, derivedKey), element);
	    }, { IS_ITERATOR: true });
	    return newMap;
	  }
	});

	// `SameValueZero` abstract operation
	// https://tc39.es/ecma262/#sec-samevaluezero
	var sameValueZero$1 = function (x, y) {
	  // eslint-disable-next-line no-self-compare -- NaN check
	  return x === y || x != x && y != y;
	};

	var $$m = _export;
	var anObject$9 = anObject$u;
	var getMapIterator$5 = getMapIterator$a;
	var sameValueZero = sameValueZero$1;
	var iterate$7 = iterate$m;

	// `Map.prototype.includes` method
	// https://github.com/tc39/proposal-collection-methods
	$$m({ target: 'Map', proto: true, real: true, forced: true }, {
	  includes: function includes(searchElement) {
	    return iterate$7(getMapIterator$5(anObject$9(this)), function (key, value, stop) {
	      if (sameValueZero(value, searchElement)) return stop();
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).stopped;
	  }
	});

	var $$l = _export;
	var call$6 = functionCall;
	var iterate$6 = iterate$m;
	var aCallable$7 = aCallable$n;

	// `Map.keyBy` method
	// https://github.com/tc39/proposal-collection-methods
	$$l({ target: 'Map', stat: true, forced: true }, {
	  keyBy: function keyBy(iterable, keyDerivative) {
	    var newMap = new this();
	    aCallable$7(keyDerivative);
	    var setter = aCallable$7(newMap.set);
	    iterate$6(iterable, function (element) {
	      call$6(setter, newMap, keyDerivative(element), element);
	    });
	    return newMap;
	  }
	});

	var $$k = _export;
	var anObject$8 = anObject$u;
	var getMapIterator$4 = getMapIterator$a;
	var iterate$5 = iterate$m;

	// `Map.prototype.keyOf` method
	// https://github.com/tc39/proposal-collection-methods
	$$k({ target: 'Map', proto: true, real: true, forced: true }, {
	  keyOf: function keyOf(searchElement) {
	    return iterate$5(getMapIterator$4(anObject$8(this)), function (key, value, stop) {
	      if (value === searchElement) return stop(key);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).result;
	  }
	});

	var $$j = _export;
	var getBuiltIn$1 = getBuiltIn$h;
	var bind$3 = functionBindContext;
	var call$5 = functionCall;
	var aCallable$6 = aCallable$n;
	var anObject$7 = anObject$u;
	var speciesConstructor$1 = speciesConstructor$5;
	var getMapIterator$3 = getMapIterator$a;
	var iterate$4 = iterate$m;

	// `Map.prototype.mapKeys` method
	// https://github.com/tc39/proposal-collection-methods
	$$j({ target: 'Map', proto: true, real: true, forced: true }, {
	  mapKeys: function mapKeys(callbackfn /* , thisArg */) {
	    var map = anObject$7(this);
	    var iterator = getMapIterator$3(map);
	    var boundFunction = bind$3(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    var newMap = new (speciesConstructor$1(map, getBuiltIn$1('Map')))();
	    var setter = aCallable$6(newMap.set);
	    iterate$4(iterator, function (key, value) {
	      call$5(setter, newMap, boundFunction(value, key, map), value);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    return newMap;
	  }
	});

	var $$i = _export;
	var getBuiltIn = getBuiltIn$h;
	var bind$2 = functionBindContext;
	var call$4 = functionCall;
	var aCallable$5 = aCallable$n;
	var anObject$6 = anObject$u;
	var speciesConstructor = speciesConstructor$5;
	var getMapIterator$2 = getMapIterator$a;
	var iterate$3 = iterate$m;

	// `Map.prototype.mapValues` method
	// https://github.com/tc39/proposal-collection-methods
	$$i({ target: 'Map', proto: true, real: true, forced: true }, {
	  mapValues: function mapValues(callbackfn /* , thisArg */) {
	    var map = anObject$6(this);
	    var iterator = getMapIterator$2(map);
	    var boundFunction = bind$2(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    var newMap = new (speciesConstructor(map, getBuiltIn('Map')))();
	    var setter = aCallable$5(newMap.set);
	    iterate$3(iterator, function (key, value) {
	      call$4(setter, newMap, key, boundFunction(value, key, map));
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    return newMap;
	  }
	});

	var $$h = _export;
	var aCallable$4 = aCallable$n;
	var anObject$5 = anObject$u;
	var iterate$2 = iterate$m;

	// `Map.prototype.merge` method
	// https://github.com/tc39/proposal-collection-methods
	$$h({ target: 'Map', proto: true, real: true, arity: 1, forced: true }, {
	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  merge: function merge(iterable /* ...iterables */) {
	    var map = anObject$5(this);
	    var setter = aCallable$4(map.set);
	    var argumentsLength = arguments.length;
	    var i = 0;
	    while (i < argumentsLength) {
	      iterate$2(arguments[i++], setter, { that: map, AS_ENTRIES: true });
	    }
	    return map;
	  }
	});

	var $$g = _export;
	var anObject$4 = anObject$u;
	var aCallable$3 = aCallable$n;
	var getMapIterator$1 = getMapIterator$a;
	var iterate$1 = iterate$m;

	var $TypeError$2 = TypeError;

	// `Map.prototype.reduce` method
	// https://github.com/tc39/proposal-collection-methods
	$$g({ target: 'Map', proto: true, real: true, forced: true }, {
	  reduce: function reduce(callbackfn /* , initialValue */) {
	    var map = anObject$4(this);
	    var iterator = getMapIterator$1(map);
	    var noInitial = arguments.length < 2;
	    var accumulator = noInitial ? undefined : arguments[1];
	    aCallable$3(callbackfn);
	    iterate$1(iterator, function (key, value) {
	      if (noInitial) {
	        noInitial = false;
	        accumulator = value;
	      } else {
	        accumulator = callbackfn(accumulator, value, key, map);
	      }
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    if (noInitial) throw $TypeError$2('Reduce of empty map with no initial value');
	    return accumulator;
	  }
	});

	var $$f = _export;
	var anObject$3 = anObject$u;
	var bind$1 = functionBindContext;
	var getMapIterator = getMapIterator$a;
	var iterate = iterate$m;

	// `Set.prototype.some` method
	// https://github.com/tc39/proposal-collection-methods
	$$f({ target: 'Map', proto: true, real: true, forced: true }, {
	  some: function some(callbackfn /* , thisArg */) {
	    var map = anObject$3(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = bind$1(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	    return iterate(iterator, function (key, value, stop) {
	      if (boundFunction(value, key, map)) return stop();
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).stopped;
	  }
	});

	var $$e = _export;
	var call$3 = functionCall;
	var anObject$2 = anObject$u;
	var aCallable$2 = aCallable$n;

	var $TypeError$1 = TypeError;

	// `Set.prototype.update` method
	// https://github.com/tc39/proposal-collection-methods
	$$e({ target: 'Map', proto: true, real: true, forced: true }, {
	  update: function update(key, callback /* , thunk */) {
	    var map = anObject$2(this);
	    var get = aCallable$2(map.get);
	    var has = aCallable$2(map.has);
	    var set = aCallable$2(map.set);
	    var length = arguments.length;
	    aCallable$2(callback);
	    var isPresentInMap = call$3(has, map, key);
	    if (!isPresentInMap && length < 3) {
	      throw $TypeError$1('Updating absent value');
	    }
	    var value = isPresentInMap ? call$3(get, map, key) : aCallable$2(length > 2 ? arguments[2] : undefined)(key, map);
	    call$3(set, map, key, callback(value, key, map));
	    return map;
	  }
	});

	var call$2 = functionCall;
	var aCallable$1 = aCallable$n;
	var isCallable = isCallable$m;
	var anObject$1 = anObject$u;

	var $TypeError = TypeError;

	// `Map.prototype.upsert` method
	// https://github.com/thumbsupep/proposal-upsert
	var mapUpsert = function upsert(key, updateFn /* , insertFn */) {
	  var map = anObject$1(this);
	  var get = aCallable$1(map.get);
	  var has = aCallable$1(map.has);
	  var set = aCallable$1(map.set);
	  var insertFn = arguments.length > 2 ? arguments[2] : undefined;
	  var value;
	  if (!isCallable(updateFn) && !isCallable(insertFn)) {
	    throw $TypeError('At least one callback required');
	  }
	  if (call$2(has, map, key)) {
	    value = call$2(get, map, key);
	    if (isCallable(updateFn)) {
	      value = updateFn(value);
	      call$2(set, map, key, value);
	    }
	  } else if (isCallable(insertFn)) {
	    value = insertFn();
	    call$2(set, map, key, value);
	  } return value;
	};

	// TODO: remove from `core-js@4`
	var $$d = _export;
	var upsert$1 = mapUpsert;

	// `Map.prototype.upsert` method (replaced by `Map.prototype.emplace`)
	// https://github.com/thumbsupep/proposal-upsert
	$$d({ target: 'Map', proto: true, real: true, forced: true }, {
	  upsert: upsert$1
	});

	// TODO: remove from `core-js@4`
	var $$c = _export;
	var upsert = mapUpsert;

	// `Map.prototype.updateOrInsert` method (replaced by `Map.prototype.emplace`)
	// https://github.com/thumbsupep/proposal-upsert
	$$c({ target: 'Map', proto: true, real: true, name: 'upsert', forced: true }, {
	  updateOrInsert: upsert
	});

	var parent$r = map$1;


















	// TODO: remove from `core-js@4`

	// TODO: remove from `core-js@4`


	var map = parent$r;

	(function (module) {
		module.exports = map;
	} (map$2));

	(function (module) {
		module.exports = map$2.exports;
	} (map$3));

	var indexOf$3 = {exports: {}};

	var indexOf$2 = {exports: {}};

	var parent$q = indexOf$4;

	var indexOf$1 = parent$q;

	var parent$p = indexOf$1;

	var indexOf = parent$p;

	(function (module) {
		module.exports = indexOf;
	} (indexOf$2));

	(function (module) {
		module.exports = indexOf$2.exports;
	} (indexOf$3));

	var construct$3 = {exports: {}};

	var construct$2 = {exports: {}};

	var parent$o = construct$4;

	var construct$1 = parent$o;

	var parent$n = construct$1;

	var construct = parent$n;

	(function (module) {
		module.exports = construct;
	} (construct$2));

	(function (module) {
		module.exports = construct$2.exports;
	} (construct$3));

	function _arrayWithoutHoles(arr) {
	  if (_Array$isArray(arr)) return _arrayLikeToArray(arr);
	}

	function _iterableToArray(iter) {
	  if (typeof _Symbol !== "undefined" && _getIteratorMethod(iter) != null || iter["@@iterator"] != null) return _Array$from$1(iter);
	}

	function _nonIterableSpread() {
	  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	function _toConsumableArray(arr) {
	  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
	}

	var get$6 = {exports: {}};

	var get$5 = {exports: {}};

	var hasOwn$1 = hasOwnProperty_1;

	var isDataDescriptor$1 = function (descriptor) {
	  return descriptor !== undefined && (hasOwn$1(descriptor, 'value') || hasOwn$1(descriptor, 'writable'));
	};

	var $$b = _export;
	var call$1 = functionCall;
	var isObject = isObject$m;
	var anObject = anObject$u;
	var isDataDescriptor = isDataDescriptor$1;
	var getOwnPropertyDescriptorModule$1 = objectGetOwnPropertyDescriptor;
	var getPrototypeOf = objectGetPrototypeOf;

	// `Reflect.get` method
	// https://tc39.es/ecma262/#sec-reflect.get
	function get$4(target, propertyKey /* , receiver */) {
	  var receiver = arguments.length < 3 ? target : arguments[2];
	  var descriptor, prototype;
	  if (anObject(target) === receiver) return target[propertyKey];
	  descriptor = getOwnPropertyDescriptorModule$1.f(target, propertyKey);
	  if (descriptor) return isDataDescriptor(descriptor)
	    ? descriptor.value
	    : descriptor.get === undefined ? undefined : call$1(descriptor.get, receiver);
	  if (isObject(prototype = getPrototypeOf(target))) return get$4(prototype, propertyKey, receiver);
	}

	$$b({ target: 'Reflect', stat: true }, {
	  get: get$4
	});

	var path$8 = path$s;

	var get$3 = path$8.Reflect.get;

	var parent$m = get$3;

	var get$2 = parent$m;

	var parent$l = get$2;

	var get$1 = parent$l;

	var parent$k = get$1;

	var get = parent$k;

	(function (module) {
		module.exports = get;
	} (get$5));

	(function (module) {
		module.exports = get$5.exports;
	} (get$6));

	var getOwnPropertyDescriptor$6 = {exports: {}};

	var getOwnPropertyDescriptor$5 = {exports: {}};

	var getOwnPropertyDescriptor$4 = {exports: {}};

	var $$a = _export;
	var fails$3 = fails$x;
	var toIndexedObject$2 = toIndexedObject$b;
	var nativeGetOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	var DESCRIPTORS$3 = descriptors;

	var FAILS_ON_PRIMITIVES$1 = fails$3(function () { nativeGetOwnPropertyDescriptor(1); });
	var FORCED$1 = !DESCRIPTORS$3 || FAILS_ON_PRIMITIVES$1;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
	$$a({ target: 'Object', stat: true, forced: FORCED$1, sham: !DESCRIPTORS$3 }, {
	  getOwnPropertyDescriptor: function getOwnPropertyDescriptor(it, key) {
	    return nativeGetOwnPropertyDescriptor(toIndexedObject$2(it), key);
	  }
	});

	var path$7 = path$s;

	var Object$1 = path$7.Object;

	var getOwnPropertyDescriptor$3 = getOwnPropertyDescriptor$4.exports = function getOwnPropertyDescriptor(it, key) {
	  return Object$1.getOwnPropertyDescriptor(it, key);
	};

	if (Object$1.getOwnPropertyDescriptor.sham) getOwnPropertyDescriptor$3.sham = true;

	var parent$j = getOwnPropertyDescriptor$4.exports;

	var getOwnPropertyDescriptor$2 = parent$j;

	var parent$i = getOwnPropertyDescriptor$2;

	var getOwnPropertyDescriptor$1 = parent$i;

	var parent$h = getOwnPropertyDescriptor$1;

	var getOwnPropertyDescriptor = parent$h;

	(function (module) {
		module.exports = getOwnPropertyDescriptor;
	} (getOwnPropertyDescriptor$5));

	(function (module) {
		module.exports = getOwnPropertyDescriptor$5.exports;
	} (getOwnPropertyDescriptor$6));

	var create = {exports: {}};

	(function (module) {
		module.exports = create$5;
	} (create));

	var _Object$create = /*@__PURE__*/getDefaultExportFromCjs(create.exports);

	var keys$2 = {exports: {}};

	var $$9 = _export;
	var toObject$3 = toObject$c;
	var nativeKeys = objectKeys$4;
	var fails$2 = fails$x;

	var FAILS_ON_PRIMITIVES = fails$2(function () { nativeKeys(1); });

	// `Object.keys` method
	// https://tc39.es/ecma262/#sec-object.keys
	$$9({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES }, {
	  keys: function keys(it) {
	    return nativeKeys(toObject$3(it));
	  }
	});

	var path$6 = path$s;

	var keys$1 = path$6.Object.keys;

	var parent$g = keys$1;

	var keys = parent$g;

	(function (module) {
		module.exports = keys;
	} (keys$2));

	var _Object$keys = /*@__PURE__*/getDefaultExportFromCjs(keys$2.exports);

	var set$2 = {exports: {}};

	var collection = collection$3;
	var collectionStrong = collectionStrong$2;

	// `Set` constructor
	// https://tc39.es/ecma262/#sec-set-objects
	collection('Set', function (init) {
	  return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong);

	var path$5 = path$s;

	var set$1 = path$5.Set;

	var parent$f = set$1;


	var set = parent$f;

	(function (module) {
		module.exports = set;
	} (set$2));

	var _Set = /*@__PURE__*/getDefaultExportFromCjs(set$2.exports);

	var now$3 = {exports: {}};

	// TODO: Remove from `core-js@4`
	var $$8 = _export;
	var uncurryThis$5 = functionUncurryThis;

	var $Date = Date;
	var thisTimeValue = uncurryThis$5($Date.prototype.getTime);

	// `Date.now` method
	// https://tc39.es/ecma262/#sec-date.now
	$$8({ target: 'Date', stat: true }, {
	  now: function now() {
	    return thisTimeValue(new $Date());
	  }
	});

	var path$4 = path$s;

	var now$2 = path$4.Date.now;

	var parent$e = now$2;

	var now$1 = parent$e;

	(function (module) {
		module.exports = now$1;
	} (now$3));

	var _Date$now = /*@__PURE__*/getDefaultExportFromCjs(now$3.exports);

	var reverse$3 = {exports: {}};

	var $$7 = _export;
	var uncurryThis$4 = functionUncurryThis;
	var isArray = isArray$e;

	var nativeReverse = uncurryThis$4([].reverse);
	var test$1 = [1, 2];

	// `Array.prototype.reverse` method
	// https://tc39.es/ecma262/#sec-array.prototype.reverse
	// fix for Safari 12.0 bug
	// https://bugs.webkit.org/show_bug.cgi?id=188794
	$$7({ target: 'Array', proto: true, forced: String(test$1) === String(test$1.reverse()) }, {
	  reverse: function reverse() {
	    // eslint-disable-next-line no-self-assign -- dirty hack
	    if (isArray(this)) this.length = this.length;
	    return nativeReverse(this);
	  }
	});

	var entryVirtual$3 = entryVirtual$i;

	var reverse$2 = entryVirtual$3('Array').reverse;

	var isPrototypeOf$3 = objectIsPrototypeOf;
	var method$3 = reverse$2;

	var ArrayPrototype$2 = Array.prototype;

	var reverse$1 = function (it) {
	  var own = it.reverse;
	  return it === ArrayPrototype$2 || (isPrototypeOf$3(ArrayPrototype$2, it) && own === ArrayPrototype$2.reverse) ? method$3 : own;
	};

	var parent$d = reverse$1;

	var reverse = parent$d;

	(function (module) {
		module.exports = reverse;
	} (reverse$3));

	var sort$3 = {exports: {}};

	var arraySlice = arraySliceSimple;

	var floor = Math.floor;

	var mergeSort = function (array, comparefn) {
	  var length = array.length;
	  var middle = floor(length / 2);
	  return length < 8 ? insertionSort(array, comparefn) : merge(
	    array,
	    mergeSort(arraySlice(array, 0, middle), comparefn),
	    mergeSort(arraySlice(array, middle), comparefn),
	    comparefn
	  );
	};

	var insertionSort = function (array, comparefn) {
	  var length = array.length;
	  var i = 1;
	  var element, j;

	  while (i < length) {
	    j = i;
	    element = array[i];
	    while (j && comparefn(array[j - 1], element) > 0) {
	      array[j] = array[--j];
	    }
	    if (j !== i++) array[j] = element;
	  } return array;
	};

	var merge = function (array, left, right, comparefn) {
	  var llength = left.length;
	  var rlength = right.length;
	  var lindex = 0;
	  var rindex = 0;

	  while (lindex < llength || rindex < rlength) {
	    array[lindex + rindex] = (lindex < llength && rindex < rlength)
	      ? comparefn(left[lindex], right[rindex]) <= 0 ? left[lindex++] : right[rindex++]
	      : lindex < llength ? left[lindex++] : right[rindex++];
	  } return array;
	};

	var arraySort = mergeSort;

	var userAgent$1 = engineUserAgent;

	var firefox = userAgent$1.match(/firefox\/(\d+)/i);

	var engineFfVersion = !!firefox && +firefox[1];

	var UA = engineUserAgent;

	var engineIsIeOrEdge = /MSIE|Trident/.test(UA);

	var userAgent = engineUserAgent;

	var webkit = userAgent.match(/AppleWebKit\/(\d+)\./);

	var engineWebkitVersion = !!webkit && +webkit[1];

	var $$6 = _export;
	var uncurryThis$3 = functionUncurryThis;
	var aCallable = aCallable$n;
	var toObject$2 = toObject$c;
	var lengthOfArrayLike$1 = lengthOfArrayLike$a;
	var deletePropertyOrThrow = deletePropertyOrThrow$2;
	var toString$1 = toString$c;
	var fails$1 = fails$x;
	var internalSort = arraySort;
	var arrayMethodIsStrict = arrayMethodIsStrict$4;
	var FF = engineFfVersion;
	var IE_OR_EDGE = engineIsIeOrEdge;
	var V8 = engineV8Version;
	var WEBKIT = engineWebkitVersion;

	var test = [];
	var nativeSort = uncurryThis$3(test.sort);
	var push$1 = uncurryThis$3(test.push);

	// IE8-
	var FAILS_ON_UNDEFINED = fails$1(function () {
	  test.sort(undefined);
	});
	// V8 bug
	var FAILS_ON_NULL = fails$1(function () {
	  test.sort(null);
	});
	// Old WebKit
	var STRICT_METHOD = arrayMethodIsStrict('sort');

	var STABLE_SORT = !fails$1(function () {
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
	    return toString$1(x) > toString$1(y) ? 1 : -1;
	  };
	};

	// `Array.prototype.sort` method
	// https://tc39.es/ecma262/#sec-array.prototype.sort
	$$6({ target: 'Array', proto: true, forced: FORCED }, {
	  sort: function sort(comparefn) {
	    if (comparefn !== undefined) aCallable(comparefn);

	    var array = toObject$2(this);

	    if (STABLE_SORT) return comparefn === undefined ? nativeSort(array) : nativeSort(array, comparefn);

	    var items = [];
	    var arrayLength = lengthOfArrayLike$1(array);
	    var itemsLength, index;

	    for (index = 0; index < arrayLength; index++) {
	      if (index in array) push$1(items, array[index]);
	    }

	    internalSort(items, getSortCompare(comparefn));

	    itemsLength = lengthOfArrayLike$1(items);
	    index = 0;

	    while (index < itemsLength) array[index] = items[index++];
	    while (index < arrayLength) deletePropertyOrThrow(array, index++);

	    return array;
	  }
	});

	var entryVirtual$2 = entryVirtual$i;

	var sort$2 = entryVirtual$2('Array').sort;

	var isPrototypeOf$2 = objectIsPrototypeOf;
	var method$2 = sort$2;

	var ArrayPrototype$1 = Array.prototype;

	var sort$1 = function (it) {
	  var own = it.sort;
	  return it === ArrayPrototype$1 || (isPrototypeOf$2(ArrayPrototype$1, it) && own === ArrayPrototype$1.sort) ? method$2 : own;
	};

	var parent$c = sort$1;

	var sort = parent$c;

	(function (module) {
		module.exports = sort;
	} (sort$3));

	var _sortInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(sort$3.exports);

	var getOwnPropertyDescriptors$2 = {exports: {}};

	var $$5 = _export;
	var DESCRIPTORS$2 = descriptors;
	var ownKeys = ownKeys$2;
	var toIndexedObject$1 = toIndexedObject$b;
	var getOwnPropertyDescriptorModule = objectGetOwnPropertyDescriptor;
	var createProperty = createProperty$6;

	// `Object.getOwnPropertyDescriptors` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptors
	$$5({ target: 'Object', stat: true, sham: !DESCRIPTORS$2 }, {
	  getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
	    var O = toIndexedObject$1(object);
	    var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
	    var keys = ownKeys(O);
	    var result = {};
	    var index = 0;
	    var key, descriptor;
	    while (keys.length > index) {
	      descriptor = getOwnPropertyDescriptor(O, key = keys[index++]);
	      if (descriptor !== undefined) createProperty(result, key, descriptor);
	    }
	    return result;
	  }
	});

	var path$3 = path$s;

	var getOwnPropertyDescriptors$1 = path$3.Object.getOwnPropertyDescriptors;

	var parent$b = getOwnPropertyDescriptors$1;

	var getOwnPropertyDescriptors = parent$b;

	(function (module) {
		module.exports = getOwnPropertyDescriptors;
	} (getOwnPropertyDescriptors$2));

	var from = {exports: {}};

	(function (module) {
		module.exports = from$4;
	} (from));

	var _Array$from = /*@__PURE__*/getDefaultExportFromCjs(from.exports);

	var startsWith$3 = {exports: {}};

	var $$4 = _export;
	var uncurryThis$2 = functionUncurryThis;
	objectGetOwnPropertyDescriptor.f;
	var toLength = toLength$2;
	var toString = toString$c;
	var notARegExp = notARegexp;
	var requireObjectCoercible = requireObjectCoercible$7;
	var correctIsRegExpLogic = correctIsRegexpLogic;

	// eslint-disable-next-line es/no-string-prototype-startswith -- safe
	var nativeStartsWith = uncurryThis$2(''.startsWith);
	var stringSlice = uncurryThis$2(''.slice);
	var min = Math.min;

	var CORRECT_IS_REGEXP_LOGIC = correctIsRegExpLogic('startsWith');

	// `String.prototype.startsWith` method
	// https://tc39.es/ecma262/#sec-string.prototype.startswith
	$$4({ target: 'String', proto: true, forced: !CORRECT_IS_REGEXP_LOGIC }, {
	  startsWith: function startsWith(searchString /* , position = 0 */) {
	    var that = toString(requireObjectCoercible(this));
	    notARegExp(searchString);
	    var index = toLength(min(arguments.length > 1 ? arguments[1] : undefined, that.length));
	    var search = toString(searchString);
	    return nativeStartsWith
	      ? nativeStartsWith(that, search, index)
	      : stringSlice(that, index, index + search.length) === search;
	  }
	});

	var entryVirtual$1 = entryVirtual$i;

	var startsWith$2 = entryVirtual$1('String').startsWith;

	var isPrototypeOf$1 = objectIsPrototypeOf;
	var method$1 = startsWith$2;

	var StringPrototype = String.prototype;

	var startsWith$1 = function (it) {
	  var own = it.startsWith;
	  return typeof it == 'string' || it === StringPrototype
	    || (isPrototypeOf$1(StringPrototype, it) && own === StringPrototype.startsWith) ? method$1 : own;
	};

	var parent$a = startsWith$1;

	var startsWith = parent$a;

	(function (module) {
		module.exports = startsWith;
	} (startsWith$3));

	var globalThis$6 = {exports: {}};

	var globalThis$5 = {exports: {}};

	var $$3 = _export;
	var global$1 = global$t;

	// `globalThis` object
	// https://tc39.es/ecma262/#sec-globalthis
	$$3({ global: true, forced: global$1.globalThis !== global$1 }, {
	  globalThis: global$1
	});

	var globalThis$4 = {exports: {}};

	(function (module) {
		module.exports = global$t;
	} (globalThis$4));

	var parent$9 = globalThis$4.exports;

	var globalThis$3 = parent$9;

	var parent$8 = globalThis$3;

	var globalThis$2 = parent$8;

	// TODO: remove from `core-js@4`


	var parent$7 = globalThis$2;

	var globalThis$1 = parent$7;

	(function (module) {
		module.exports = globalThis$1;
	} (globalThis$5));

	(function (module) {
		module.exports = globalThis$5.exports;
	} (globalThis$6));

	var assign$4 = {exports: {}};

	var DESCRIPTORS$1 = descriptors;
	var uncurryThis$1 = functionUncurryThis;
	var call = functionCall;
	var fails = fails$x;
	var objectKeys$1 = objectKeys$4;
	var getOwnPropertySymbolsModule = objectGetOwnPropertySymbols;
	var propertyIsEnumerableModule = objectPropertyIsEnumerable;
	var toObject$1 = toObject$c;
	var IndexedObject = indexedObject;

	// eslint-disable-next-line es/no-object-assign -- safe
	var $assign = Object.assign;
	// eslint-disable-next-line es/no-object-defineproperty -- required for testing
	var defineProperty = Object.defineProperty;
	var concat = uncurryThis$1([].concat);

	// `Object.assign` method
	// https://tc39.es/ecma262/#sec-object.assign
	var objectAssign = !$assign || fails(function () {
	  // should have correct order of operations (Edge bug)
	  if (DESCRIPTORS$1 && $assign({ b: 1 }, $assign(defineProperty({}, 'a', {
	    enumerable: true,
	    get: function () {
	      defineProperty(this, 'b', {
	        value: 3,
	        enumerable: false
	      });
	    }
	  }), { b: 2 })).b !== 1) return true;
	  // should work with symbols and should have deterministic property order (V8 bug)
	  var A = {};
	  var B = {};
	  // eslint-disable-next-line es/no-symbol -- safe
	  var symbol = Symbol();
	  var alphabet = 'abcdefghijklmnopqrst';
	  A[symbol] = 7;
	  alphabet.split('').forEach(function (chr) { B[chr] = chr; });
	  return $assign({}, A)[symbol] != 7 || objectKeys$1($assign({}, B)).join('') != alphabet;
	}) ? function assign(target, source) { // eslint-disable-line no-unused-vars -- required for `.length`
	  var T = toObject$1(target);
	  var argumentsLength = arguments.length;
	  var index = 1;
	  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
	  var propertyIsEnumerable = propertyIsEnumerableModule.f;
	  while (argumentsLength > index) {
	    var S = IndexedObject(arguments[index++]);
	    var keys = getOwnPropertySymbols ? concat(objectKeys$1(S), getOwnPropertySymbols(S)) : objectKeys$1(S);
	    var length = keys.length;
	    var j = 0;
	    var key;
	    while (length > j) {
	      key = keys[j++];
	      if (!DESCRIPTORS$1 || call(propertyIsEnumerable, S, key)) T[key] = S[key];
	    }
	  } return T;
	} : $assign;

	var $$2 = _export;
	var assign$3 = objectAssign;

	// `Object.assign` method
	// https://tc39.es/ecma262/#sec-object.assign
	// eslint-disable-next-line es/no-object-assign -- required for testing
	$$2({ target: 'Object', stat: true, arity: 2, forced: Object.assign !== assign$3 }, {
	  assign: assign$3
	});

	var path$2 = path$s;

	var assign$2 = path$2.Object.assign;

	var parent$6 = assign$2;

	var assign$1 = parent$6;

	(function (module) {
		module.exports = assign$1;
	} (assign$4));

	var fill$4 = {exports: {}};

	var toObject = toObject$c;
	var toAbsoluteIndex = toAbsoluteIndex$5;
	var lengthOfArrayLike = lengthOfArrayLike$a;

	// `Array.prototype.fill` method implementation
	// https://tc39.es/ecma262/#sec-array.prototype.fill
	var arrayFill = function fill(value /* , start = 0, end = @length */) {
	  var O = toObject(this);
	  var length = lengthOfArrayLike(O);
	  var argumentsLength = arguments.length;
	  var index = toAbsoluteIndex(argumentsLength > 1 ? arguments[1] : undefined, length);
	  var end = argumentsLength > 2 ? arguments[2] : undefined;
	  var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
	  while (endPos > index) O[index++] = value;
	  return O;
	};

	var $$1 = _export;
	var fill$3 = arrayFill;

	// `Array.prototype.fill` method
	// https://tc39.es/ecma262/#sec-array.prototype.fill
	$$1({ target: 'Array', proto: true }, {
	  fill: fill$3
	});

	var entryVirtual = entryVirtual$i;

	var fill$2 = entryVirtual('Array').fill;

	var isPrototypeOf = objectIsPrototypeOf;
	var method = fill$2;

	var ArrayPrototype = Array.prototype;

	var fill$1 = function (it) {
	  var own = it.fill;
	  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.fill) ? method : own;
	};

	var parent$5 = fill$1;

	var fill = parent$5;

	(function (module) {
		module.exports = fill;
	} (fill$4));

	var _fillInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(fill$4.exports);

	var symbol = {exports: {}};

	(function (module) {
		module.exports = symbol$3;
	} (symbol));

	var isIterable$6 = {exports: {}};

	var isIterable$5 = {exports: {}};

	var classof = classof$b;
	var hasOwn = hasOwnProperty_1;
	var isNullOrUndefined = isNullOrUndefined$8;
	var wellKnownSymbol = wellKnownSymbol$o;
	var Iterators = iterators;

	var ITERATOR = wellKnownSymbol('iterator');
	var $Object = Object;

	var isIterable$4 = function (it) {
	  if (isNullOrUndefined(it)) return false;
	  var O = $Object(it);
	  return O[ITERATOR] !== undefined
	    || '@@iterator' in O
	    || hasOwn(Iterators, classof(O));
	};

	var isIterable$3 = isIterable$4;

	var isIterable_1 = isIterable$3;

	var parent$4 = isIterable_1;


	var isIterable$2 = parent$4;

	var parent$3 = isIterable$2;

	var isIterable$1 = parent$3;

	var parent$2 = isIterable$1;

	var isIterable = parent$2;

	(function (module) {
		module.exports = isIterable;
	} (isIterable$5));

	(function (module) {
		module.exports = isIterable$5.exports;
	} (isIterable$6));

	function noop() {}
	var identity = function identity(x) {
	  return x;
	};
	function assign(tar, src) {
	  // @ts-ignore
	  for (var k in src) {
	    tar[k] = src[k];
	  }
	  return tar;
	}
	function run(fn) {
	  return fn();
	}
	function blank_object() {
	  return _Object$create(null);
	}
	function run_all(fns) {
	  _forEachInstanceProperty(fns).call(fns, run);
	}
	function is_function(thing) {
	  return typeof thing === 'function';
	}
	function safe_not_equal(a, b) {
	  return a != a ? b == b : a !== b || a && _typeof(a) === 'object' || typeof a === 'function';
	}
	function is_empty(obj) {
	  return _Object$keys(obj).length === 0;
	}
	var is_client = typeof window !== 'undefined';
	var now = is_client ? function () {
	  return window.performance.now();
	} : function () {
	  return _Date$now();
	};
	var raf = is_client ? function (cb) {
	  return requestAnimationFrame(cb);
	} : noop;
	var tasks = new _Set();
	function run_tasks(now) {
	  _forEachInstanceProperty(tasks).call(tasks, function (task) {
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
	 */
	function loop(callback) {
	  var task;
	  if (tasks.size === 0) raf(run_tasks);
	  return {
	    promise: new _Promise(function (fulfill) {
	      tasks.add(task = {
	        c: callback,
	        f: fulfill
	      });
	    }),
	    abort: function abort() {
	      tasks.delete(task);
	    }
	  };
	}
	function append(target, node) {
	  target.appendChild(node);
	}
	function get_root_for_style(node) {
	  if (!node) return document;
	  var root = node.getRootNode ? node.getRootNode() : node.ownerDocument;
	  if (root && root.host) {
	    return root;
	  }
	  return node.ownerDocument;
	}
	function append_empty_stylesheet(node) {
	  var style_element = element('style');
	  append_stylesheet(get_root_for_style(node), style_element);
	  return style_element.sheet;
	}
	function append_stylesheet(node, style) {
	  append(node.head || node, style);
	  return style.sheet;
	}
	function insert(target, node, anchor) {
	  target.insertBefore(node, anchor || null);
	}
	function detach(node) {
	  if (node.parentNode) {
	    node.parentNode.removeChild(node);
	  }
	}
	function destroy_each(iterations, detaching) {
	  for (var i = 0; i < iterations.length; i += 1) {
	    if (iterations[i]) iterations[i].d(detaching);
	  }
	}
	function element(name) {
	  return document.createElement(name);
	}
	function text(data) {
	  return document.createTextNode(data);
	}
	function space() {
	  return text(' ');
	}
	function empty() {
	  return text('');
	}
	function listen(node, event, handler, options) {
	  node.addEventListener(event, handler, options);
	  return function () {
	    return node.removeEventListener(event, handler, options);
	  };
	}
	function attr(node, attribute, value) {
	  if (value == null) node.removeAttribute(attribute);else if (node.getAttribute(attribute) !== value) node.setAttribute(attribute, value);
	}
	function children(element) {
	  return _Array$from(element.childNodes);
	}
	function set_data(text, data) {
	  data = '' + data;
	  if (text.wholeText !== data) text.data = data;
	}
	function select_option(select, value) {
	  for (var i = 0; i < select.options.length; i += 1) {
	    var option = select.options[i];
	    if (option.__value === value) {
	      option.selected = true;
	      return;
	    }
	  }
	  select.selectedIndex = -1; // no option should be selected
	}
	function select_value(select) {
	  var selected_option = select.querySelector(':checked') || select.options[0];
	  return selected_option && selected_option.__value;
	}
	function custom_event(type, detail) {
	  var _ref = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
	    _ref$bubbles = _ref.bubbles,
	    bubbles = _ref$bubbles === void 0 ? false : _ref$bubbles,
	    _ref$cancelable = _ref.cancelable,
	    cancelable = _ref$cancelable === void 0 ? false : _ref$cancelable;
	  var e = document.createEvent('CustomEvent');
	  e.initCustomEvent(type, bubbles, cancelable, detail);
	  return e;
	}

	// we need to store the information for multiple documents because a Svelte application could also contain iframes
	// https://github.com/sveltejs/svelte/issues/3624
	var managed_styles = new _Map();
	var active = 0;
	// https://github.com/darkskyapp/string-hash/blob/master/index.js
	function hash(str) {
	  var hash = 5381;
	  var i = str.length;
	  while (i--) {
	    hash = (hash << 5) - hash ^ str.charCodeAt(i);
	  }
	  return hash >>> 0;
	}
	function create_style_information(doc, node) {
	  var info = {
	    stylesheet: append_empty_stylesheet(node),
	    rules: {}
	  };
	  managed_styles.set(doc, info);
	  return info;
	}
	function create_rule(node, a, b, duration, delay, ease, fn) {
	  var _context9, _context11, _context12, _context13;
	  var uid = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : 0;
	  var step = 16.666 / duration;
	  var keyframes = '{\n';
	  for (var p = 0; p <= 1; p += step) {
	    var t = a + (b - a) * ease(p);
	    keyframes += p * 100 + "%{".concat(fn(t, 1 - t), "}\n");
	  }
	  var rule = keyframes + "100% {".concat(fn(b, 1 - b), "}\n}");
	  var name = _concatInstanceProperty(_context9 = "__svelte_".concat(hash(rule), "_")).call(_context9, uid);
	  var doc = get_root_for_style(node);
	  var _ref2 = managed_styles.get(doc) || create_style_information(doc, node),
	    stylesheet = _ref2.stylesheet,
	    rules = _ref2.rules;
	  if (!rules[name]) {
	    var _context10;
	    rules[name] = true;
	    stylesheet.insertRule(_concatInstanceProperty(_context10 = "@keyframes ".concat(name, " ")).call(_context10, rule), stylesheet.cssRules.length);
	  }
	  var animation = node.style.animation || '';
	  node.style.animation = _concatInstanceProperty(_context11 = _concatInstanceProperty(_context12 = _concatInstanceProperty(_context13 = "".concat(animation ? "".concat(animation, ", ") : '')).call(_context13, name, " ")).call(_context12, duration, "ms linear ")).call(_context11, delay, "ms 1 both");
	  active += 1;
	  return name;
	}
	function delete_rule(node, name) {
	  var previous = (node.style.animation || '').split(', ');
	  var next = _filterInstanceProperty(previous).call(previous, name ? function (anim) {
	    return _indexOfInstanceProperty(anim).call(anim, name) < 0;
	  } // remove specific animation
	  : function (anim) {
	    return _indexOfInstanceProperty(anim).call(anim, '__svelte') === -1;
	  } // remove all Svelte animations
	  );

	  var deleted = previous.length - next.length;
	  if (deleted) {
	    node.style.animation = next.join(', ');
	    active -= deleted;
	    if (!active) clear_rules();
	  }
	}
	function clear_rules() {
	  raf(function () {
	    if (active) return;
	    _forEachInstanceProperty(managed_styles).call(managed_styles, function (info) {
	      var ownerNode = info.stylesheet.ownerNode;
	      // there is no ownerNode if it runs on jsdom.
	      if (ownerNode) detach(ownerNode);
	    });
	    managed_styles.clear();
	  });
	}
	var current_component;
	function set_current_component(component) {
	  current_component = component;
	}
	function get_current_component() {
	  if (!current_component) throw new Error('Function called outside component initialization');
	  return current_component;
	}
	/**
	 * Creates an event dispatcher that can be used to dispatch [component events](/docs#template-syntax-component-directives-on-eventname).
	 * Event dispatchers are functions that can take two arguments: `name` and `detail`.
	 *
	 * Component events created with `createEventDispatcher` create a
	 * [CustomEvent](https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent).
	 * These events do not [bubble](https://developer.mozilla.org/en-US/docs/Learn/JavaScript/Building_blocks/Events#Event_bubbling_and_capture).
	 * The `detail` argument corresponds to the [CustomEvent.detail](https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/detail)
	 * property and can contain any type of data.
	 *
	 * https://svelte.dev/docs#run-time-svelte-createeventdispatcher
	 */
	function createEventDispatcher() {
	  var component = get_current_component();
	  return function (type, detail) {
	    var _ref3 = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {},
	      _ref3$cancelable = _ref3.cancelable,
	      cancelable = _ref3$cancelable === void 0 ? false : _ref3$cancelable;
	    var callbacks = component.$$.callbacks[type];
	    if (callbacks) {
	      var _context16;
	      // TODO are there situations where events could be dispatched
	      // in a server (non-DOM) environment?
	      var event = custom_event(type, detail, {
	        cancelable: cancelable
	      });
	      _forEachInstanceProperty(_context16 = _sliceInstanceProperty$1(callbacks).call(callbacks)).call(_context16, function (fn) {
	        fn.call(component, event);
	      });
	      return !event.defaultPrevented;
	    }
	    return true;
	  };
	}
	var dirty_components = [];
	var binding_callbacks = [];
	var render_callbacks = [];
	var flush_callbacks = [];
	var resolved_promise = _Promise.resolve();
	var update_scheduled = false;
	function schedule_update() {
	  if (!update_scheduled) {
	    update_scheduled = true;
	    resolved_promise.then(flush);
	  }
	}
	function tick() {
	  schedule_update();
	  return resolved_promise;
	}
	function add_render_callback(fn) {
	  render_callbacks.push(fn);
	}
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
	var seen_callbacks = new _Set();
	var flushidx = 0; // Do *not* move this inside the flush() function
	function flush() {
	  var saved_component = current_component;
	  do {
	    // first, call beforeUpdate functions
	    // and update components
	    while (flushidx < dirty_components.length) {
	      var component = dirty_components[flushidx];
	      flushidx++;
	      set_current_component(component);
	      update(component.$$);
	    }
	    set_current_component(null);
	    dirty_components.length = 0;
	    flushidx = 0;
	    while (binding_callbacks.length) {
	      binding_callbacks.pop()();
	    }
	    // then, once components are updated, call
	    // afterUpdate functions. This may cause
	    // subsequent updates...
	    for (var i = 0; i < render_callbacks.length; i += 1) {
	      var callback = render_callbacks[i];
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
	function update($$) {
	  if ($$.fragment !== null) {
	    var _context18;
	    $$.update();
	    run_all($$.before_update);
	    var dirty = $$.dirty;
	    $$.dirty = [-1];
	    $$.fragment && $$.fragment.p($$.ctx, dirty);
	    _forEachInstanceProperty(_context18 = $$.after_update).call(_context18, add_render_callback);
	  }
	}
	var promise;
	function wait() {
	  if (!promise) {
	    promise = _Promise.resolve();
	    promise.then(function () {
	      promise = null;
	    });
	  }
	  return promise;
	}
	function dispatch(node, direction, kind) {
	  var _context19;
	  node.dispatchEvent(custom_event(_concatInstanceProperty(_context19 = "".concat(direction ? 'intro' : 'outro')).call(_context19, kind)));
	}
	var outroing = new _Set();
	var outros;
	function group_outros() {
	  outros = {
	    r: 0,
	    c: [],
	    p: outros // parent group
	  };
	}

	function check_outros() {
	  if (!outros.r) {
	    run_all(outros.c);
	  }
	  outros = outros.p;
	}
	function transition_in(block, local) {
	  if (block && block.i) {
	    outroing.delete(block);
	    block.i(local);
	  }
	}
	function transition_out(block, local, detach, callback) {
	  if (block && block.o) {
	    if (outroing.has(block)) return;
	    outroing.add(block);
	    outros.c.push(function () {
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
	var null_transition = {
	  duration: 0
	};
	function create_bidirectional_transition(node, fn, params, intro) {
	  var config = fn(node, params);
	  var t = intro ? 0 : 1;
	  var running_program = null;
	  var pending_program = null;
	  var animation_name = null;
	  function clear_animation() {
	    if (animation_name) delete_rule(node, animation_name);
	  }
	  function init(program, duration) {
	    var d = program.b - t;
	    duration *= Math.abs(d);
	    return {
	      a: t,
	      b: program.b,
	      d: d,
	      duration: duration,
	      start: program.start,
	      end: program.start + duration,
	      group: program.group
	    };
	  }
	  function go(b) {
	    var _ref6 = config || null_transition,
	      _ref6$delay = _ref6.delay,
	      delay = _ref6$delay === void 0 ? 0 : _ref6$delay,
	      _ref6$duration = _ref6.duration,
	      duration = _ref6$duration === void 0 ? 300 : _ref6$duration,
	      _ref6$easing = _ref6.easing,
	      easing = _ref6$easing === void 0 ? identity : _ref6$easing,
	      _ref6$tick = _ref6.tick,
	      tick = _ref6$tick === void 0 ? noop : _ref6$tick,
	      css = _ref6.css;
	    var program = {
	      start: now() + delay,
	      b: b
	    };
	    if (!b) {
	      // @ts-ignore todo: improve typings
	      program.group = outros;
	      outros.r += 1;
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
	      add_render_callback(function () {
	        return dispatch(node, b, 'start');
	      });
	      loop(function (now) {
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
	            var p = now - running_program.start;
	            t = running_program.a + running_program.d * easing(p / running_program.duration);
	            tick(t, 1 - t);
	          }
	        }
	        return !!(running_program || pending_program);
	      });
	    }
	  }
	  return {
	    run: function run(b) {
	      if (is_function(config)) {
	        wait().then(function () {
	          // @ts-ignore
	          config = config();
	          go(b);
	        });
	      } else {
	        go(b);
	      }
	    },
	    end: function end() {
	      clear_animation();
	      running_program = pending_program = null;
	    }
	  };
	}
	function outro_and_destroy_block(block, lookup) {
	  transition_out(block, 1, 1, function () {
	    lookup.delete(block.key);
	  });
	}
	function update_keyed_each(old_blocks, dirty, get_key, dynamic, ctx, list, lookup, node, destroy, create_each_block, next, get_context) {
	  var o = old_blocks.length;
	  var n = list.length;
	  var i = o;
	  var old_indexes = {};
	  while (i--) {
	    old_indexes[old_blocks[i].key] = i;
	  }
	  var new_blocks = [];
	  var new_lookup = new _Map();
	  var deltas = new _Map();
	  i = n;
	  while (i--) {
	    var child_ctx = get_context(ctx, list, i);
	    var key = get_key(child_ctx);
	    var block = lookup.get(key);
	    if (!block) {
	      block = create_each_block(key, child_ctx);
	      block.c();
	    } else if (dynamic) {
	      block.p(child_ctx, dirty);
	    }
	    new_lookup.set(key, new_blocks[i] = block);
	    if (key in old_indexes) deltas.set(key, Math.abs(i - old_indexes[key]));
	  }
	  var will_move = new _Set();
	  var did_move = new _Set();
	  function insert(block) {
	    transition_in(block, 1);
	    block.m(node, next);
	    lookup.set(block.key, block);
	    next = block.first;
	    n--;
	  }
	  while (o && n) {
	    var new_block = new_blocks[n - 1];
	    var old_block = old_blocks[o - 1];
	    var new_key = new_block.key;
	    var old_key = old_block.key;
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
	    var _old_block = old_blocks[o];
	    if (!new_lookup.has(_old_block.key)) destroy(_old_block, lookup);
	  }
	  while (n) {
	    insert(new_blocks[n - 1]);
	  }
	  return new_blocks;
	}
	function get_spread_update(levels, updates) {
	  var update = {};
	  var to_null_out = {};
	  var accounted_for = {
	    $$scope: 1
	  };
	  var i = levels.length;
	  while (i--) {
	    var o = levels[i];
	    var n = updates[i];
	    if (n) {
	      for (var key in o) {
	        if (!(key in n)) to_null_out[key] = 1;
	      }
	      for (var _key3 in n) {
	        if (!accounted_for[_key3]) {
	          update[_key3] = n[_key3];
	          accounted_for[_key3] = 1;
	        }
	      }
	      levels[i] = n;
	    } else {
	      for (var _key4 in o) {
	        accounted_for[_key4] = 1;
	      }
	    }
	  }
	  for (var _key5 in to_null_out) {
	    if (!(_key5 in update)) update[_key5] = undefined;
	  }
	  return update;
	}
	function get_spread_object(spread_props) {
	  return _typeof(spread_props) === 'object' && spread_props !== null ? spread_props : {};
	}

	// source: https://html.spec.whatwg.org/multipage/indices.html
	new _Set(['allowfullscreen', 'allowpaymentrequest', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'formnovalidate', 'hidden', 'inert', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected']);
	function bind(component, name, callback) {
	  var index = component.$$.props[name];
	  if (index !== undefined) {
	    component.$$.bound[index] = callback;
	    callback(component.$$.ctx[index]);
	  }
	}
	function create_component(block) {
	  block && block.c();
	}
	function mount_component(component, target, anchor, customElement) {
	  var _component$$$ = component.$$,
	    fragment = _component$$$.fragment,
	    after_update = _component$$$.after_update;
	  fragment && fragment.m(target, anchor);
	  if (!customElement) {
	    // onMount happens before the initial afterUpdate
	    add_render_callback(function () {
	      var _context34, _context35;
	      var new_on_destroy = _filterInstanceProperty(_context34 = _mapInstanceProperty(_context35 = component.$$.on_mount).call(_context35, run)).call(_context34, is_function);
	      // if the component was destroyed immediately
	      // it will update the `$$.on_destroy` reference to `null`.
	      // the destructured on_destroy may still reference to the old array
	      if (component.$$.on_destroy) {
	        var _component$$$$on_dest;
	        (_component$$$$on_dest = component.$$.on_destroy).push.apply(_component$$$$on_dest, _toConsumableArray(new_on_destroy));
	      } else {
	        // Edge case - component was destroyed immediately,
	        // most likely as a result of a binding initialising
	        run_all(new_on_destroy);
	      }
	      component.$$.on_mount = [];
	    });
	  }
	  _forEachInstanceProperty(after_update).call(after_update, add_render_callback);
	}
	function destroy_component(component, detaching) {
	  var $$ = component.$$;
	  if ($$.fragment !== null) {
	    run_all($$.on_destroy);
	    $$.fragment && $$.fragment.d(detaching);
	    // TODO null out other refs, including component.$$ (but need to
	    // preserve final state?)
	    $$.on_destroy = $$.fragment = null;
	    $$.ctx = [];
	  }
	}
	function make_dirty(component, i) {
	  if (component.$$.dirty[0] === -1) {
	    var _context36;
	    dirty_components.push(component);
	    schedule_update();
	    _fillInstanceProperty(_context36 = component.$$.dirty).call(_context36, 0);
	  }
	  component.$$.dirty[i / 31 | 0] |= 1 << i % 31;
	}
	function init(component, options, instance, create_fragment, not_equal, props, append_styles) {
	  var dirty = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : [-1];
	  var parent_component = current_component;
	  set_current_component(component);
	  var $$ = component.$$ = {
	    fragment: null,
	    ctx: [],
	    // state
	    props: props,
	    update: noop,
	    not_equal: not_equal,
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
	    dirty: dirty,
	    skip_bound: false,
	    root: options.target || parent_component.$$.root
	  };
	  append_styles && append_styles($$.root);
	  var ready = false;
	  $$.ctx = instance ? instance(component, options.props || {}, function (i, ret) {
	    var value = (arguments.length <= 2 ? 0 : arguments.length - 2) ? arguments.length <= 2 ? undefined : arguments[2] : ret;
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
	      var nodes = children(options.target);
	      // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
	      $$.fragment && $$.fragment.l(nodes);
	      _forEachInstanceProperty(nodes).call(nodes, detach);
	    } else {
	      // eslint-disable-next-line @typescript-eslint/no-non-null-assertion
	      $$.fragment && $$.fragment.c();
	    }
	    if (options.intro) transition_in(component.$$.fragment);
	    mount_component(component, options.target, options.anchor, options.customElement);
	    flush();
	  }
	  set_current_component(parent_component);
	}
	/**
	 * Base class for Svelte components. Used when dev=false.
	 */
	var SvelteComponent = /*#__PURE__*/function () {
	  function SvelteComponent() {
	    _classCallCheck(this, SvelteComponent);
	  }
	  _createClass(SvelteComponent, [{
	    key: "$destroy",
	    value: function $destroy() {
	      destroy_component(this, 1);
	      this.$destroy = noop;
	    }
	  }, {
	    key: "$on",
	    value: function $on(type, callback) {
	      if (!is_function(callback)) {
	        return noop;
	      }
	      var callbacks = this.$$.callbacks[type] || (this.$$.callbacks[type] = []);
	      callbacks.push(callback);
	      return function () {
	        var index = _indexOfInstanceProperty(callbacks).call(callbacks, callback);
	        if (index !== -1) _spliceInstanceProperty(callbacks).call(callbacks, index, 1);
	      };
	    }
	  }, {
	    key: "$set",
	    value: function $set($$props) {
	      if (this.$$set && !is_empty($$props)) {
	        this.$$.skip_bound = true;
	        this.$$set($$props);
	        this.$$.skip_bound = false;
	      }
	    }
	  }]);
	  return SvelteComponent;
	}();

	var values$2 = {exports: {}};

	var DESCRIPTORS = descriptors;
	var uncurryThis = functionUncurryThis;
	var objectKeys = objectKeys$4;
	var toIndexedObject = toIndexedObject$b;
	var $propertyIsEnumerable = objectPropertyIsEnumerable.f;

	var propertyIsEnumerable = uncurryThis($propertyIsEnumerable);
	var push = uncurryThis([].push);

	// `Object.{ entries, values }` methods implementation
	var createMethod = function (TO_ENTRIES) {
	  return function (it) {
	    var O = toIndexedObject(it);
	    var keys = objectKeys(O);
	    var length = keys.length;
	    var i = 0;
	    var result = [];
	    var key;
	    while (length > i) {
	      key = keys[i++];
	      if (!DESCRIPTORS || propertyIsEnumerable(O, key)) {
	        push(result, TO_ENTRIES ? [key, O[key]] : O[key]);
	      }
	    }
	    return result;
	  };
	};

	var objectToArray = {
	  // `Object.entries` method
	  // https://tc39.es/ecma262/#sec-object.entries
	  entries: createMethod(true),
	  // `Object.values` method
	  // https://tc39.es/ecma262/#sec-object.values
	  values: createMethod(false)
	};

	var $ = _export;
	var $values = objectToArray.values;

	// `Object.values` method
	// https://tc39.es/ecma262/#sec-object.values
	$({ target: 'Object', stat: true }, {
	  values: function values(O) {
	    return $values(O);
	  }
	});

	var path$1 = path$s;

	var values$1 = path$1.Object.values;

	var parent$1 = values$1;

	var values = parent$1;

	(function (module) {
		module.exports = values;
	} (values$2));

	var _Object$values = /*@__PURE__*/getDefaultExportFromCjs(values$2.exports);

	function _createSuper$2(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$2(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$2() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
	function get_each_context$1(ctx, list, i) {
	  var child_ctx = _sliceInstanceProperty$1(ctx).call(ctx);
	  child_ctx[10] = list[i];
	  return child_ctx;
	}

	// (30:8) {#if placeholder}
	function create_if_block_2$1(ctx) {
	  var option;
	  var t_value = /*placeholder*/ctx[3].name + "";
	  var t;
	  var option_value_value;
	  return {
	    c: function c() {
	      option = element("option");
	      t = text(t_value);
	      option.__value = option_value_value = /*placeholder*/ctx[3].id;
	      option.value = option.__value;
	    },
	    m: function m(target, anchor) {
	      insert(target, option, anchor);
	      append(option, t);
	    },
	    p: function p(ctx, dirty) {
	      if (dirty & /*placeholder*/8 && t_value !== (t_value = /*placeholder*/ctx[3].name + "")) set_data(t, t_value);
	      if (dirty & /*placeholder*/8 && option_value_value !== (option_value_value = /*placeholder*/ctx[3].id)) {
	        option.__value = option_value_value;
	        option.value = option.__value;
	      }
	    },
	    d: function d(detaching) {
	      if (detaching) detach(option);
	    }
	  };
	}

	// (34:12) {#if !item.hidden}
	function create_if_block_1$1(ctx) {
	  var option;
	  var t_value = /*item*/ctx[10].name + "";
	  var t;
	  var option_value_value;
	  return {
	    c: function c() {
	      option = element("option");
	      t = text(t_value);
	      option.__value = option_value_value = /*item*/ctx[10].id;
	      option.value = option.__value;
	    },
	    m: function m(target, anchor) {
	      insert(target, option, anchor);
	      append(option, t);
	    },
	    p: function p(ctx, dirty) {
	      if (dirty & /*items*/16 && t_value !== (t_value = /*item*/ctx[10].name + "")) set_data(t, t_value);
	      if (dirty & /*items*/16 && option_value_value !== (option_value_value = /*item*/ctx[10].id)) {
	        option.__value = option_value_value;
	        option.value = option.__value;
	      }
	    },
	    d: function d(detaching) {
	      if (detaching) detach(option);
	    }
	  };
	}

	// (33:8) {#each items as item}
	function create_each_block$1(ctx) {
	  var if_block_anchor;
	  var if_block = ! /*item*/ctx[10].hidden && create_if_block_1$1(ctx);
	  return {
	    c: function c() {
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	    },
	    m: function m(target, anchor) {
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	    },
	    p: function p(ctx, dirty) {
	      if (! /*item*/ctx[10].hidden) {
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
	    d: function d(detaching) {
	      if (if_block) if_block.d(detaching);
	      if (detaching) detach(if_block_anchor);
	    }
	  };
	}

	// (40:0) {#if error}
	function create_if_block$2(ctx) {
	  var div;
	  var t;
	  return {
	    c: function c() {
	      div = element("div");
	      t = text( /*error*/ctx[5]);
	      attr(div, "class", "bookly-label-error");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, t);
	    },
	    p: function p(ctx, dirty) {
	      if (dirty & /*error*/32) set_data(t, /*error*/ctx[5]);
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	    }
	  };
	}
	function create_fragment$2(ctx) {
	  var label_1;
	  var t0;
	  var t1;
	  var div;
	  var select;
	  var if_block0_anchor;
	  var t2;
	  var if_block1_anchor;
	  var mounted;
	  var dispose;
	  var if_block0 = /*placeholder*/ctx[3] && create_if_block_2$1(ctx);
	  var each_value = /*items*/ctx[4];
	  var each_blocks = [];
	  for (var i = 0; i < each_value.length; i += 1) {
	    each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));
	  }
	  var if_block1 = /*error*/ctx[5] && create_if_block$2(ctx);
	  return {
	    c: function c() {
	      label_1 = element("label");
	      t0 = text( /*label*/ctx[2]);
	      t1 = space();
	      div = element("div");
	      select = element("select");
	      if (if_block0) if_block0.c();
	      if_block0_anchor = empty();
	      for (var _i = 0; _i < each_blocks.length; _i += 1) {
	        each_blocks[_i].c();
	      }
	      t2 = space();
	      if (if_block1) if_block1.c();
	      if_block1_anchor = empty();
	      if ( /*selected*/ctx[1] === void 0) add_render_callback(function () {
	        return (/*select_change_handler*/ctx[8].call(select)
	        );
	      });
	    },
	    m: function m(target, anchor) {
	      insert(target, label_1, anchor);
	      append(label_1, t0);
	      /*label_1_binding*/
	      ctx[7](label_1);
	      insert(target, t1, anchor);
	      insert(target, div, anchor);
	      append(div, select);
	      if (if_block0) if_block0.m(select, null);
	      append(select, if_block0_anchor);
	      for (var _i2 = 0; _i2 < each_blocks.length; _i2 += 1) {
	        each_blocks[_i2].m(select, null);
	      }
	      select_option(select, /*selected*/ctx[1]);
	      insert(target, t2, anchor);
	      if (if_block1) if_block1.m(target, anchor);
	      insert(target, if_block1_anchor, anchor);
	      if (!mounted) {
	        dispose = [listen(select, "change", /*select_change_handler*/ctx[8]), listen(select, "change", /*onChange*/ctx[6])];
	        mounted = true;
	      }
	    },
	    p: function p(ctx, _ref) {
	      var _ref2 = _slicedToArray(_ref, 1),
	        dirty = _ref2[0];
	      if (dirty & /*label*/4) set_data(t0, /*label*/ctx[2]);
	      if ( /*placeholder*/ctx[3]) {
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
	        each_value = /*items*/ctx[4];
	        var _i3;
	        for (_i3 = 0; _i3 < each_value.length; _i3 += 1) {
	          var child_ctx = get_each_context$1(ctx, each_value, _i3);
	          if (each_blocks[_i3]) {
	            each_blocks[_i3].p(child_ctx, dirty);
	          } else {
	            each_blocks[_i3] = create_each_block$1(child_ctx);
	            each_blocks[_i3].c();
	            each_blocks[_i3].m(select, null);
	          }
	        }
	        for (; _i3 < each_blocks.length; _i3 += 1) {
	          each_blocks[_i3].d(1);
	        }
	        each_blocks.length = each_value.length;
	      }
	      if (dirty & /*selected, items, placeholder*/26) {
	        select_option(select, /*selected*/ctx[1]);
	      }
	      if ( /*error*/ctx[5]) {
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
	    d: function d(detaching) {
	      if (detaching) detach(label_1);
	      /*label_1_binding*/
	      ctx[7](null);
	      if (detaching) detach(t1);
	      if (detaching) detach(div);
	      if (if_block0) if_block0.d();
	      destroy_each(each_blocks, detaching);
	      if (detaching) detach(t2);
	      if (if_block1) if_block1.d(detaching);
	      if (detaching) detach(if_block1_anchor);
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
	  var _$$props$el = $$props.el,
	    el = _$$props$el === void 0 ? null : _$$props$el;
	  var _$$props$label = $$props.label,
	    label = _$$props$label === void 0 ? '' : _$$props$label;
	  var _$$props$placeholder = $$props.placeholder,
	    placeholder = _$$props$placeholder === void 0 ? null : _$$props$placeholder;
	  var _$$props$items = $$props.items,
	    items = _$$props$items === void 0 ? [] : _$$props$items;
	  var _$$props$selected = $$props.selected,
	    selected = _$$props$selected === void 0 ? '' : _$$props$selected;
	  var _$$props$error = $$props.error,
	    error = _$$props$error === void 0 ? null : _$$props$error;
	  var dispatch = createEventDispatcher();
	  function onChange() {
	    dispatch('change', selected);
	  }
	  function label_1_binding($$value) {
	    binding_callbacks[$$value ? 'unshift' : 'push'](function () {
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
	  $$self.$$set = function ($$props) {
	    if ('el' in $$props) $$invalidate(0, el = $$props.el);
	    if ('label' in $$props) $$invalidate(2, label = $$props.label);
	    if ('placeholder' in $$props) $$invalidate(3, placeholder = $$props.placeholder);
	    if ('items' in $$props) $$invalidate(4, items = $$props.items);
	    if ('selected' in $$props) $$invalidate(1, selected = $$props.selected);
	    if ('error' in $$props) $$invalidate(5, error = $$props.error);
	  };
	  $$self.$$.update = function () {
	    if ($$self.$$.dirty & /*items*/16) {
	      // Sort items by position
	      _sortInstanceProperty(items).call(items, compare);
	    }
	  };
	  return [el, selected, label, placeholder, items, error, onChange, label_1_binding, select_change_handler];
	}
	var Select = /*#__PURE__*/function (_SvelteComponent) {
	  _inherits(Select, _SvelteComponent);
	  var _super = _createSuper$2(Select);
	  function Select(options) {
	    var _this;
	    _classCallCheck(this, Select);
	    _this = _super.call(this);
	    init(_assertThisInitialized(_this), options, instance$2, create_fragment$2, safe_not_equal, {
	      el: 0,
	      label: 2,
	      placeholder: 3,
	      items: 4,
	      selected: 1,
	      error: 5
	    });
	    return _this;
	  }
	  return _createClass(Select);
	}(SvelteComponent);

	var getOwnPropertySymbols$2 = {exports: {}};

	var path = path$s;

	var getOwnPropertySymbols$1 = path.Object.getOwnPropertySymbols;

	var parent = getOwnPropertySymbols$1;

	var getOwnPropertySymbols = parent;

	(function (module) {
		module.exports = getOwnPropertySymbols;
	} (getOwnPropertySymbols$2));

	function cubicOut(t) {
	  var f = t - 1.0;
	  return f * f * f + 1.0;
	}

	function slide(node) {
	  var _ref4 = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {},
	    _ref4$delay = _ref4.delay,
	    delay = _ref4$delay === void 0 ? 0 : _ref4$delay,
	    _ref4$duration = _ref4.duration,
	    duration = _ref4$duration === void 0 ? 400 : _ref4$duration,
	    _ref4$easing = _ref4.easing,
	    easing = _ref4$easing === void 0 ? cubicOut : _ref4$easing;
	  var style = getComputedStyle(node);
	  var opacity = +style.opacity;
	  var height = _parseFloat(style.height);
	  var padding_top = _parseFloat(style.paddingTop);
	  var padding_bottom = _parseFloat(style.paddingBottom);
	  var margin_top = _parseFloat(style.marginTop);
	  var margin_bottom = _parseFloat(style.marginBottom);
	  var border_top_width = _parseFloat(style.borderTopWidth);
	  var border_bottom_width = _parseFloat(style.borderBottomWidth);
	  return {
	    delay: delay,
	    duration: duration,
	    easing: easing,
	    css: function css(t) {
	      return 'overflow: hidden;' + "opacity: ".concat(Math.min(t * 20, 1) * opacity, ";") + "height: ".concat(t * height, "px;") + "padding-top: ".concat(t * padding_top, "px;") + "padding-bottom: ".concat(t * padding_bottom, "px;") + "margin-top: ".concat(t * margin_top, "px;") + "margin-bottom: ".concat(t * margin_bottom, "px;") + "border-top-width: ".concat(t * border_top_width, "px;") + "border-bottom-width: ".concat(t * border_bottom_width, "px;");
	    }
	  };
	}

	function _createSuper$1(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$1(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$1() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
	function create_if_block_14(ctx) {
	  var div;
	  var select;
	  var updating_el;
	  var current;
	  function select_el_binding(value) {
	    /*select_el_binding*/ctx[66](value);
	  }
	  var select_props = {
	    label: /*l10n*/ctx[16].location_label,
	    placeholder: /*locationPlaceholder*/ctx[30],
	    items: _Object$values( /*locations*/ctx[0]),
	    selected: /*locationId*/ctx[17],
	    error: /*locationError*/ctx[34]
	  };
	  if ( /*locationEl*/ctx[35] !== void 0) {
	    select_props.el = /*locationEl*/ctx[35];
	  }
	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(function () {
	    return bind(select, 'el', select_el_binding);
	  });
	  select.$on("change", /*onLocationChange*/ctx[40]);
	  return {
	    c: function c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "location");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      var select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].location_label;
	      if (dirty[0] & /*locationPlaceholder*/1073741824) select_changes.placeholder = /*locationPlaceholder*/ctx[30];
	      if (dirty[0] & /*locations*/1) select_changes.items = _Object$values( /*locations*/ctx[0]);
	      if (dirty[0] & /*locationId*/131072) select_changes.selected = /*locationId*/ctx[17];
	      if (dirty[1] & /*locationError*/8) select_changes.error = /*locationError*/ctx[34];
	      if (!updating_el && dirty[1] & /*locationEl*/16) {
	        updating_el = true;
	        select_changes.el = /*locationEl*/ctx[35];
	        add_flush_callback(function () {
	          return updating_el = false;
	        });
	      }
	      select.$set(select_changes);
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      destroy_component(select);
	    }
	  };
	}

	// (488:4) {#if hasCategorySelect}
	function create_if_block_12(ctx) {
	  var div;
	  var select;
	  var t;
	  var show_if = /*showCategoryInfo*/ctx[4] && /*categoryId*/ctx[18] && /*categories*/ctx[1][/*categoryId*/ctx[18]].hasOwnProperty('info') && /*categories*/ctx[1][/*categoryId*/ctx[18]].info !== '';
	  var if_block_anchor;
	  var current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].category_label,
	      placeholder: /*categoryPlaceholder*/ctx[31],
	      items: _Object$values( /*categoryItems*/ctx[26]),
	      selected: /*categoryId*/ctx[18]
	    }
	  });
	  select.$on("change", /*onCategoryChange*/ctx[41]);
	  var if_block = show_if && create_if_block_13(ctx);
	  return {
	    c: function c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "category");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      var select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].category_label;
	      if (dirty[1] & /*categoryPlaceholder*/1) select_changes.placeholder = /*categoryPlaceholder*/ctx[31];
	      if (dirty[0] & /*categoryItems*/67108864) select_changes.items = _Object$values( /*categoryItems*/ctx[26]);
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
	        transition_out(if_block, 1, 1, function () {
	          if_block = null;
	        });
	        check_outros();
	      }
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      transition_in(if_block);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(select.$$.fragment, local);
	      transition_out(if_block);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      destroy_component(select);
	      if (detaching) detach(t);
	      if (if_block) if_block.d(detaching);
	      if (detaching) detach(if_block_anchor);
	    }
	  };
	}

	// (498:8) {#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}
	function create_if_block_13(ctx) {
	  var div;
	  var raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "";
	  var div_transition;
	  var current;
	  return {
	    c: function c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-sm bookly-category-info");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      if ((!current || dirty[0] & /*categories, categoryId*/262146) && raw_value !== (raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "")) div.innerHTML = raw_value;
	    },
	    i: function i(local) {
	      if (current) return;
	      add_render_callback(function () {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	        div_transition.run(1);
	      });
	      current = true;
	    },
	    o: function o(local) {
	      if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	      div_transition.run(0);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (504:4) {#if hasServiceSelect}
	function create_if_block_10(ctx) {
	  var div;
	  var select;
	  var updating_el;
	  var t;
	  var show_if = /*showServiceInfo*/ctx[5] && /*serviceId*/ctx[19] && /*services*/ctx[2][/*serviceId*/ctx[19]].hasOwnProperty('info') && /*services*/ctx[2][/*serviceId*/ctx[19]].info !== '';
	  var if_block_anchor;
	  var current;
	  function select_el_binding_1(value) {
	    /*select_el_binding_1*/ctx[67](value);
	  }
	  var select_props = {
	    label: /*l10n*/ctx[16].service_label,
	    placeholder: /*servicePlaceholder*/ctx[32],
	    items: _Object$values( /*serviceItems*/ctx[27]),
	    selected: /*serviceId*/ctx[19],
	    error: /*serviceError*/ctx[36]
	  };
	  if ( /*serviceEl*/ctx[37] !== void 0) {
	    select_props.el = /*serviceEl*/ctx[37];
	  }
	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(function () {
	    return bind(select, 'el', select_el_binding_1);
	  });
	  select.$on("change", /*onServiceChange*/ctx[42]);
	  var if_block = show_if && create_if_block_11(ctx);
	  return {
	    c: function c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "service");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      var select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].service_label;
	      if (dirty[1] & /*servicePlaceholder*/2) select_changes.placeholder = /*servicePlaceholder*/ctx[32];
	      if (dirty[0] & /*serviceItems*/134217728) select_changes.items = _Object$values( /*serviceItems*/ctx[27]);
	      if (dirty[0] & /*serviceId*/524288) select_changes.selected = /*serviceId*/ctx[19];
	      if (dirty[1] & /*serviceError*/32) select_changes.error = /*serviceError*/ctx[36];
	      if (!updating_el && dirty[1] & /*serviceEl*/64) {
	        updating_el = true;
	        select_changes.el = /*serviceEl*/ctx[37];
	        add_flush_callback(function () {
	          return updating_el = false;
	        });
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
	        transition_out(if_block, 1, 1, function () {
	          if_block = null;
	        });
	        check_outros();
	      }
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      transition_in(if_block);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(select.$$.fragment, local);
	      transition_out(if_block);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      destroy_component(select);
	      if (detaching) detach(t);
	      if (if_block) if_block.d(detaching);
	      if (detaching) detach(if_block_anchor);
	    }
	  };
	}

	// (516:8) {#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}
	function create_if_block_11(ctx) {
	  var div;
	  var raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "";
	  var div_transition;
	  var current;
	  return {
	    c: function c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-sm bookly-service-info");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      if ((!current || dirty[0] & /*services, serviceId*/524292) && raw_value !== (raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "")) div.innerHTML = raw_value;
	    },
	    i: function i(local) {
	      if (current) return;
	      add_render_callback(function () {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	        div_transition.run(1);
	      });
	      current = true;
	    },
	    o: function o(local) {
	      if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	      div_transition.run(0);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (522:4) {#if hasStaffSelect}
	function create_if_block_8(ctx) {
	  var div;
	  var select;
	  var updating_el;
	  var t;
	  var show_if = /*showStaffInfo*/ctx[6] && /*staffId*/ctx[20] && /*staff*/ctx[3][/*staffId*/ctx[20]].hasOwnProperty('info') && /*staff*/ctx[3][/*staffId*/ctx[20]].info !== '';
	  var if_block_anchor;
	  var current;
	  function select_el_binding_2(value) {
	    /*select_el_binding_2*/ctx[68](value);
	  }
	  var select_props = {
	    label: /*l10n*/ctx[16].staff_label,
	    placeholder: /*staffPlaceholder*/ctx[33],
	    items: _Object$values( /*staffItems*/ctx[23]),
	    selected: /*staffId*/ctx[20],
	    error: /*staffError*/ctx[38]
	  };
	  if ( /*staffEl*/ctx[39] !== void 0) {
	    select_props.el = /*staffEl*/ctx[39];
	  }
	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(function () {
	    return bind(select, 'el', select_el_binding_2);
	  });
	  select.$on("change", /*onStaffChange*/ctx[43]);
	  var if_block = show_if && create_if_block_9(ctx);
	  return {
	    c: function c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "staff");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      var select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].staff_label;
	      if (dirty[1] & /*staffPlaceholder*/4) select_changes.placeholder = /*staffPlaceholder*/ctx[33];
	      if (dirty[0] & /*staffItems*/8388608) select_changes.items = _Object$values( /*staffItems*/ctx[23]);
	      if (dirty[0] & /*staffId*/1048576) select_changes.selected = /*staffId*/ctx[20];
	      if (dirty[1] & /*staffError*/128) select_changes.error = /*staffError*/ctx[38];
	      if (!updating_el && dirty[1] & /*staffEl*/256) {
	        updating_el = true;
	        select_changes.el = /*staffEl*/ctx[39];
	        add_flush_callback(function () {
	          return updating_el = false;
	        });
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
	        transition_out(if_block, 1, 1, function () {
	          if_block = null;
	        });
	        check_outros();
	      }
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      transition_in(if_block);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(select.$$.fragment, local);
	      transition_out(if_block);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      destroy_component(select);
	      if (detaching) detach(t);
	      if (if_block) if_block.d(detaching);
	      if (detaching) detach(if_block_anchor);
	    }
	  };
	}

	// (534:8) {#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}
	function create_if_block_9(ctx) {
	  var div;
	  var raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "";
	  var div_transition;
	  var current;
	  return {
	    c: function c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-sm bookly-staff-info");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      if ((!current || dirty[0] & /*staff, staffId*/1048584) && raw_value !== (raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "")) div.innerHTML = raw_value;
	    },
	    i: function i(local) {
	      if (current) return;
	      add_render_callback(function () {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	        div_transition.run(1);
	      });
	      current = true;
	    },
	    o: function o(local) {
	      if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	      div_transition.run(0);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (540:4) {#if hasDurationSelect}
	function create_if_block_7(ctx) {
	  var div;
	  var select;
	  var current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].duration_label,
	      items: _Object$values( /*durationItems*/ctx[24]),
	      selected: /*duration*/ctx[21]
	    }
	  });
	  select.$on("change", /*onDurationChange*/ctx[44]);
	  return {
	    c: function c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "duration");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      var select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].duration_label;
	      if (dirty[0] & /*durationItems*/16777216) select_changes.items = _Object$values( /*durationItems*/ctx[24]);
	      if (dirty[0] & /*duration*/2097152) select_changes.selected = /*duration*/ctx[21];
	      select.$set(select_changes);
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      destroy_component(select);
	    }
	  };
	}

	// (550:4) {#if hasNopSelect}
	function create_if_block_6(ctx) {
	  var div;
	  var select;
	  var current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].nop_label,
	      items: _Object$values( /*nopItems*/ctx[28]),
	      selected: /*nop*/ctx[22]
	    }
	  });
	  select.$on("change", /*onNopChange*/ctx[45]);
	  return {
	    c: function c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "nop");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      var select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].nop_label;
	      if (dirty[0] & /*nopItems*/268435456) select_changes.items = _Object$values( /*nopItems*/ctx[28]);
	      if (dirty[0] & /*nop*/4194304) select_changes.selected = /*nop*/ctx[22];
	      select.$set(select_changes);
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      destroy_component(select);
	    }
	  };
	}

	// (560:4) {#if hasQuantitySelect}
	function create_if_block_5(ctx) {
	  var div;
	  var select;
	  var current;
	  select = new Select({
	    props: {
	      label: /*l10n*/ctx[16].quantity_label,
	      items: _Object$values( /*quantityItems*/ctx[29]),
	      selected: /*quantity*/ctx[25]
	    }
	  });
	  select.$on("change", /*onQuantityChange*/ctx[46]);
	  return {
	    c: function c() {
	      div = element("div");
	      create_component(select.$$.fragment);
	      attr(div, "class", "bookly-form-group");
	      attr(div, "data-type", "quantity");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      mount_component(select, div, null);
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      var select_changes = {};
	      if (dirty[0] & /*l10n*/65536) select_changes.label = /*l10n*/ctx[16].quantity_label;
	      if (dirty[0] & /*quantityItems*/536870912) select_changes.items = _Object$values( /*quantityItems*/ctx[29]);
	      if (dirty[0] & /*quantity*/33554432) select_changes.selected = /*quantity*/ctx[25];
	      select.$set(select_changes);
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(select.$$.fragment, local);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(select.$$.fragment, local);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      destroy_component(select);
	    }
	  };
	}

	// (570:4) {#if hasDropBtn}
	function create_if_block_3(ctx) {
	  var div1;
	  var label;
	  var t;
	  var div0;
	  var if_block = /*showDropBtn*/ctx[15] && create_if_block_4(ctx);
	  return {
	    c: function c() {
	      div1 = element("div");
	      label = element("label");
	      t = space();
	      div0 = element("div");
	      if (if_block) if_block.c();
	      attr(div1, "class", "bookly-form-group bookly-chain-actions");
	    },
	    m: function m(target, anchor) {
	      insert(target, div1, anchor);
	      append(div1, label);
	      append(div1, t);
	      append(div1, div0);
	      if (if_block) if_block.m(div0, null);
	    },
	    p: function p(ctx, dirty) {
	      if ( /*showDropBtn*/ctx[15]) {
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
	    d: function d(detaching) {
	      if (detaching) detach(div1);
	      if (if_block) if_block.d();
	    }
	  };
	}

	// (574:16) {#if showDropBtn}
	function create_if_block_4(ctx) {
	  var button;
	  var mounted;
	  var dispose;
	  return {
	    c: function c() {
	      button = element("button");
	      button.innerHTML = "<i class=\"bookly-icon-sm bookly-icon-drop\"></i>";
	      attr(button, "class", "bookly-round");
	    },
	    m: function m(target, anchor) {
	      insert(target, button, anchor);
	      if (!mounted) {
	        dispose = listen(button, "click", /*onDropBtnClick*/ctx[47]);
	        mounted = true;
	      }
	    },
	    p: noop,
	    d: function d(detaching) {
	      if (detaching) detach(button);
	      mounted = false;
	      dispose();
	    }
	  };
	}

	// (581:0) {#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}
	function create_if_block_2(ctx) {
	  var div;
	  var raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "";
	  var div_transition;
	  var current;
	  return {
	    c: function c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-md bookly-category-info");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      if ((!current || dirty[0] & /*categories, categoryId*/262146) && raw_value !== (raw_value = /*categories*/ctx[1][/*categoryId*/ctx[18]].info + "")) div.innerHTML = raw_value;
	    },
	    i: function i(local) {
	      if (current) return;
	      add_render_callback(function () {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	        div_transition.run(1);
	      });
	      current = true;
	    },
	    o: function o(local) {
	      if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	      div_transition.run(0);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (586:0) {#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}
	function create_if_block_1(ctx) {
	  var div;
	  var raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "";
	  var div_transition;
	  var current;
	  return {
	    c: function c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-md bookly-service-info");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      if ((!current || dirty[0] & /*services, serviceId*/524292) && raw_value !== (raw_value = /*services*/ctx[2][/*serviceId*/ctx[19]].info + "")) div.innerHTML = raw_value;
	    },
	    i: function i(local) {
	      if (current) return;
	      add_render_callback(function () {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	        div_transition.run(1);
	      });
	      current = true;
	    },
	    o: function o(local) {
	      if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	      div_transition.run(0);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}

	// (591:0) {#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}
	function create_if_block$1(ctx) {
	  var div;
	  var raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "";
	  var div_transition;
	  var current;
	  return {
	    c: function c() {
	      div = element("div");
	      attr(div, "class", "bookly-box bookly-visible-md bookly-staff-info");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      div.innerHTML = raw_value;
	      current = true;
	    },
	    p: function p(ctx, dirty) {
	      if ((!current || dirty[0] & /*staff, staffId*/1048584) && raw_value !== (raw_value = /*staff*/ctx[3][/*staffId*/ctx[20]].info + "")) div.innerHTML = raw_value;
	    },
	    i: function i(local) {
	      if (current) return;
	      add_render_callback(function () {
	        if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, true);
	        div_transition.run(1);
	      });
	      current = true;
	    },
	    o: function o(local) {
	      if (!div_transition) div_transition = create_bidirectional_transition(div, slide, {}, false);
	      div_transition.run(0);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      if (detaching && div_transition) div_transition.end();
	    }
	  };
	}
	function create_fragment$1(ctx) {
	  var div;
	  var t0;
	  var t1;
	  var t2;
	  var t3;
	  var t4;
	  var t5;
	  var t6;
	  var t7;
	  var show_if_2 = /*showCategoryInfo*/ctx[4] && /*categoryId*/ctx[18] && /*categories*/ctx[1][/*categoryId*/ctx[18]].hasOwnProperty('info') && /*categories*/ctx[1][/*categoryId*/ctx[18]].info !== '';
	  var t8;
	  var show_if_1 = /*showServiceInfo*/ctx[5] && /*serviceId*/ctx[19] && /*services*/ctx[2][/*serviceId*/ctx[19]].hasOwnProperty('info') && /*services*/ctx[2][/*serviceId*/ctx[19]].info !== '';
	  var t9;
	  var show_if = /*showStaffInfo*/ctx[6] && /*staffId*/ctx[20] && /*staff*/ctx[3][/*staffId*/ctx[20]].hasOwnProperty('info') && /*staff*/ctx[3][/*staffId*/ctx[20]].info !== '';
	  var if_block10_anchor;
	  var current;
	  var if_block0 = /*hasLocationSelect*/ctx[7] && create_if_block_14(ctx);
	  var if_block1 = /*hasCategorySelect*/ctx[8] && create_if_block_12(ctx);
	  var if_block2 = /*hasServiceSelect*/ctx[9] && create_if_block_10(ctx);
	  var if_block3 = /*hasStaffSelect*/ctx[10] && create_if_block_8(ctx);
	  var if_block4 = /*hasDurationSelect*/ctx[11] && create_if_block_7(ctx);
	  var if_block5 = /*hasNopSelect*/ctx[12] && create_if_block_6(ctx);
	  var if_block6 = /*hasQuantitySelect*/ctx[13] && create_if_block_5(ctx);
	  var if_block7 = /*hasDropBtn*/ctx[14] && create_if_block_3(ctx);
	  var if_block8 = show_if_2 && create_if_block_2(ctx);
	  var if_block9 = show_if_1 && create_if_block_1(ctx);
	  var if_block10 = show_if && create_if_block$1(ctx);
	  return {
	    c: function c() {
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
	    m: function m(target, anchor) {
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
	    p: function p(ctx, dirty) {
	      if ( /*hasLocationSelect*/ctx[7]) {
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
	        transition_out(if_block0, 1, 1, function () {
	          if_block0 = null;
	        });
	        check_outros();
	      }
	      if ( /*hasCategorySelect*/ctx[8]) {
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
	        transition_out(if_block1, 1, 1, function () {
	          if_block1 = null;
	        });
	        check_outros();
	      }
	      if ( /*hasServiceSelect*/ctx[9]) {
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
	        transition_out(if_block2, 1, 1, function () {
	          if_block2 = null;
	        });
	        check_outros();
	      }
	      if ( /*hasStaffSelect*/ctx[10]) {
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
	        transition_out(if_block3, 1, 1, function () {
	          if_block3 = null;
	        });
	        check_outros();
	      }
	      if ( /*hasDurationSelect*/ctx[11]) {
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
	        transition_out(if_block4, 1, 1, function () {
	          if_block4 = null;
	        });
	        check_outros();
	      }
	      if ( /*hasNopSelect*/ctx[12]) {
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
	        transition_out(if_block5, 1, 1, function () {
	          if_block5 = null;
	        });
	        check_outros();
	      }
	      if ( /*hasQuantitySelect*/ctx[13]) {
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
	        transition_out(if_block6, 1, 1, function () {
	          if_block6 = null;
	        });
	        check_outros();
	      }
	      if ( /*hasDropBtn*/ctx[14]) {
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
	        transition_out(if_block8, 1, 1, function () {
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
	        transition_out(if_block9, 1, 1, function () {
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
	        transition_out(if_block10, 1, 1, function () {
	          if_block10 = null;
	        });
	        check_outros();
	      }
	    },
	    i: function i(local) {
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
	    o: function o(local) {
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
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      if (if_block0) if_block0.d();
	      if (if_block1) if_block1.d();
	      if (if_block2) if_block2.d();
	      if (if_block3) if_block3.d();
	      if (if_block4) if_block4.d();
	      if (if_block5) if_block5.d();
	      if (if_block6) if_block6.d();
	      if (if_block7) if_block7.d();
	      if (detaching) detach(t7);
	      if (if_block8) if_block8.d(detaching);
	      if (detaching) detach(t8);
	      if (if_block9) if_block9.d(detaching);
	      if (detaching) detach(t9);
	      if (if_block10) if_block10.d(detaching);
	      if (detaching) detach(if_block10_anchor);
	    }
	  };
	}
	function instance$1($$self, $$props, $$invalidate) {
	  var _$$props$item = $$props.item,
	    item = _$$props$item === void 0 ? {} : _$$props$item;
	  var _$$props$index = $$props.index,
	    index = _$$props$index === void 0 ? 0 : _$$props$index;
	  var _$$props$locations = $$props.locations,
	    locations = _$$props$locations === void 0 ? [] : _$$props$locations;
	  var _$$props$categories = $$props.categories,
	    categories = _$$props$categories === void 0 ? [] : _$$props$categories;
	  var _$$props$services = $$props.services,
	    services = _$$props$services === void 0 ? [] : _$$props$services;
	  var _$$props$staff = $$props.staff,
	    staff = _$$props$staff === void 0 ? [] : _$$props$staff;
	  var _$$props$defaults = $$props.defaults,
	    defaults = _$$props$defaults === void 0 ? {} : _$$props$defaults;
	  var _$$props$required = $$props.required,
	    required = _$$props$required === void 0 ? {} : _$$props$required;
	  var _$$props$servicesPerL = $$props.servicesPerLocation,
	    servicesPerLocation = _$$props$servicesPerL === void 0 ? false : _$$props$servicesPerL;
	  var _$$props$staffNameWit = $$props.staffNameWithPrice,
	    staffNameWithPrice = _$$props$staffNameWit === void 0 ? false : _$$props$staffNameWit;
	  var _$$props$collaborativ = $$props.collaborativeHideStaff,
	    collaborativeHideStaff = _$$props$collaborativ === void 0 ? false : _$$props$collaborativ;
	  var _$$props$showRatings = $$props.showRatings,
	    showRatings = _$$props$showRatings === void 0 ? false : _$$props$showRatings;
	  var _$$props$showCategory = $$props.showCategoryInfo,
	    showCategoryInfo = _$$props$showCategory === void 0 ? false : _$$props$showCategory;
	  var _$$props$showServiceI = $$props.showServiceInfo,
	    showServiceInfo = _$$props$showServiceI === void 0 ? false : _$$props$showServiceI;
	  var _$$props$showStaffInf = $$props.showStaffInfo,
	    showStaffInfo = _$$props$showStaffInf === void 0 ? false : _$$props$showStaffInf;
	  var _$$props$maxQuantity = $$props.maxQuantity,
	    maxQuantity = _$$props$maxQuantity === void 0 ? 1 : _$$props$maxQuantity;
	  var _$$props$hasLocationS = $$props.hasLocationSelect,
	    hasLocationSelect = _$$props$hasLocationS === void 0 ? false : _$$props$hasLocationS;
	  var _$$props$hasCategoryS = $$props.hasCategorySelect,
	    hasCategorySelect = _$$props$hasCategoryS === void 0 ? true : _$$props$hasCategoryS;
	  var _$$props$hasServiceSe = $$props.hasServiceSelect,
	    hasServiceSelect = _$$props$hasServiceSe === void 0 ? true : _$$props$hasServiceSe;
	  var _$$props$hasStaffSele = $$props.hasStaffSelect,
	    hasStaffSelect = _$$props$hasStaffSele === void 0 ? true : _$$props$hasStaffSele;
	  var _$$props$hasDurationS = $$props.hasDurationSelect,
	    hasDurationSelect = _$$props$hasDurationS === void 0 ? false : _$$props$hasDurationS;
	  var _$$props$hasNopSelect = $$props.hasNopSelect,
	    hasNopSelect = _$$props$hasNopSelect === void 0 ? false : _$$props$hasNopSelect;
	  var _$$props$hasQuantityS = $$props.hasQuantitySelect,
	    hasQuantitySelect = _$$props$hasQuantityS === void 0 ? false : _$$props$hasQuantityS;
	  var _$$props$hasDropBtn = $$props.hasDropBtn,
	    hasDropBtn = _$$props$hasDropBtn === void 0 ? false : _$$props$hasDropBtn;
	  var _$$props$showDropBtn = $$props.showDropBtn,
	    showDropBtn = _$$props$showDropBtn === void 0 ? false : _$$props$showDropBtn;
	  var _$$props$l10n = $$props.l10n,
	    l10n = _$$props$l10n === void 0 ? {} : _$$props$l10n;
	  var _$$props$date_from_el = $$props.date_from_element,
	    date_from_element = _$$props$date_from_el === void 0 ? null : _$$props$date_from_el;
	  var dispatch = createEventDispatcher();
	  var locationId = 0;
	  var categoryId = 0;
	  var serviceId = 0;
	  var staffId = 0;
	  var duration = 1;
	  var nop = 1;
	  var quantity = 1;
	  var categoryItems;
	  var serviceItems;
	  var staffItems;
	  var durationItems;
	  var nopItems;
	  var quantityItems;
	  var locationPlaceholder;
	  var categoryPlaceholder;
	  var servicePlaceholder;
	  var staffPlaceholder;
	  var locationError, locationEl;
	  var serviceError, serviceEl;
	  var staffError, staffEl;
	  var lookupLocationId;
	  var categorySelected;
	  var maxCapacity;
	  var minCapacity;
	  var srvMaxCapacity;
	  var srvMinCapacity;

	  // Preselect values
	  tick().then(function () {
	    // Location
	    var selected = item.location_id || defaults.location_id;
	    if (selected) {
	      onLocationChange({
	        detail: selected
	      });
	    }
	  }).then(function () {
	    // Category
	    if (defaults.category_id) {
	      onCategoryChange({
	        detail: defaults.category_id
	      });
	    }
	  }).then(function () {
	    // Service
	    var selected = item.service_id || defaults.service_id;
	    if (selected) {
	      onServiceChange({
	        detail: selected
	      });
	    }
	  }).then(function () {
	    // Staff
	    var selected;
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
	  }).then(function () {
	    // Duration
	    if (item.units > 1) {
	      onDurationChange({
	        detail: item.units
	      });
	    }
	  }).then(function () {
	    // Nop
	    if (item.number_of_persons > 1) {
	      onNopChange({
	        detail: item.number_of_persons
	      });
	    }
	  }).then(function () {
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

	    // Update related values
	    if (locationId) {
	      var _lookupLocationId = servicesPerLocation ? locationId : 0;
	      if (staffId) {
	        if (!(staffId in locations[locationId].staff)) {
	          $$invalidate(20, staffId = 0);
	        } else if (serviceId && !(_lookupLocationId in staff[staffId].services[serviceId].locations)) {
	          $$invalidate(20, staffId = 0);
	        }
	      }
	      if (serviceId) {
	        var valid = false;
	        $__default["default"].each(locations[locationId].staff, function (id) {
	          if (serviceId in staff[id].services && _lookupLocationId in staff[id].services[serviceId].locations) {
	            valid = true;
	            return false;
	          }
	        });
	        if (!valid) {
	          $$invalidate(19, serviceId = 0);
	        }
	      }
	      if (categoryId) {
	        var _valid = false;
	        $__default["default"].each(locations[locationId].staff, function (id) {
	          $__default["default"].each(staff[id].services, function (srvId) {
	            if (services[srvId].category_id === categoryId) {
	              _valid = true;
	              return false;
	            }
	          });
	          if (_valid) {
	            return false;
	          }
	        });
	        if (!_valid) {
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
	        var valid = false;
	        $__default["default"].each(staff[staffId].services, function (id) {
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
	    var dateMin = false;
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
	    } else if (!categorySelected) {
	      $$invalidate(18, categoryId = 0);
	      if (date_from_element[0]) {
	        dateMin = date_from_element.data('date_min');
	      }
	    }
	    if (date_from_element[0]) {
	      date_from_element.pickadate('picker').set('min', dateMin);
	      if (date_from_element.data('updated')) {
	        date_from_element.pickadate('picker').set('select', date_from_element.pickadate('picker').get('select'));
	      } else {
	        date_from_element.pickadate('picker').set('select', dateMin);
	      }
	    }
	  }
	  function onStaffChange(event) {
	    $$invalidate(20, staffId = event.detail);

	    // Validate value
	    if (!(staffId in staffItems)) {
	      $$invalidate(20, staffId = 0);
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
	    var valid = true;
	    var el = null;
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
	      valid: valid,
	      el: el
	    };
	  }
	  function getValues() {
	    return {
	      locationId: locationId,
	      categoryId: categoryId,
	      serviceId: serviceId,
	      staffIds: staffId ? [staffId] : _mapInstanceProperty($__default["default"]).call($__default["default"], staffItems, function (item) {
	        return item.id;
	      }),
	      duration: duration,
	      nop: nop,
	      quantity: quantity
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
	  $$self.$$set = function ($$props) {
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
	  $$self.$$.update = function () {
	    if ($$self.$$.dirty[0] & /*locationId, staff, locations, serviceId, categoryId, services, staffItems, categories, staffId, nop, hasNopSelect, duration, durationItems, l10n*/33493007 | $$self.$$.dirty[1] & /*servicesPerLocation, lookupLocationId, staffNameWithPrice, collaborativeHideStaff, showRatings, categorySelected, maxQuantity*/1675624448 | $$self.$$.dirty[2] & /*srvMinCapacity, srvMaxCapacity, minCapacity, maxCapacity*/15) {
	      {
	        $$invalidate(60, lookupLocationId = servicesPerLocation && locationId ? locationId : 0);
	        $$invalidate(26, categoryItems = {});
	        $$invalidate(27, serviceItems = {});
	        $$invalidate(23, staffItems = {});
	        $$invalidate(28, nopItems = {});

	        // Staff
	        $__default["default"].each(staff, function (id, staffMember) {
	          if (!locationId || id in locations[locationId].staff) {
	            if (!serviceId) {
	              if (!categoryId) {
	                $$invalidate(23, staffItems[id] = $__default["default"].extend({}, staffMember), staffItems);
	              } else {
	                $__default["default"].each(staffMember.services, function (srvId) {
	                  if (services[srvId].category_id === categoryId) {
	                    $$invalidate(23, staffItems[id] = $__default["default"].extend({}, staffMember), staffItems);
	                    return false;
	                  }
	                });
	              }
	            } else if (serviceId in staffMember.services) {
	              $__default["default"].each(staffMember.services[serviceId].locations, function (locId, locSrv) {
	                if (lookupLocationId && lookupLocationId !== _parseInt(locId)) {
	                  return true;
	                }
	                $$invalidate(65, srvMinCapacity = srvMinCapacity ? Math.min(srvMinCapacity, locSrv.min_capacity) : locSrv.min_capacity);
	                $$invalidate(64, srvMaxCapacity = srvMaxCapacity ? Math.max(srvMaxCapacity, locSrv.max_capacity) : locSrv.max_capacity);
	                $$invalidate(23, staffItems[id] = $__default["default"].extend({}, staffMember, {
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
	          $__default["default"].each(staff, function (id, staffMember) {
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
	          $__default["default"].each(services, function (id, service) {
	            if (!categoryId || !categorySelected || service.category_id === categoryId) {
	              if (!staffId || id in staff[staffId].services) {
	                $$invalidate(27, serviceItems[id] = service, serviceItems);
	              }
	            }
	          });
	        } else {
	          var categoryIds = [],
	            serviceIds = [];
	          if (servicesPerLocation) {
	            $__default["default"].each(staff, function (stId) {
	              $__default["default"].each(staff[stId].services, function (srvId) {
	                if (lookupLocationId in staff[stId].services[srvId].locations) {
	                  categoryIds.push(services[srvId].category_id);
	                  serviceIds.push(srvId);
	                }
	              });
	            });
	          } else {
	            $__default["default"].each(locations[locationId].staff, function (stId) {
	              $__default["default"].each(staff[stId].services, function (srvId) {
	                categoryIds.push(services[srvId].category_id);
	                serviceIds.push(srvId);
	              });
	            });
	          }
	          $__default["default"].each(categories, function (id, category) {
	            if ($__default["default"].inArray(_parseInt(id), categoryIds) > -1) {
	              $$invalidate(26, categoryItems[id] = category, categoryItems);
	            }
	          });
	          if (categoryId && $__default["default"].inArray(categoryId, categoryIds) === -1) {
	            $$invalidate(18, categoryId = 0);
	            $$invalidate(61, categorySelected = false);
	          }
	          $__default["default"].each(services, function (id, service) {
	            if ($__default["default"].inArray(id, serviceIds) > -1) {
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
	        for (var i = minCapacity; i <= maxCapacity; ++i) {
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
	            var locId = locationId || 0;
	            var staffLocations = staff[staffId].services[serviceId].locations;
	            if (staffLocations) {
	              var staffLocation = locId in staffLocations ? staffLocations[locId] : staffLocations[0];
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
	        for (var q = 1; q <= maxQuantity; ++q) {
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
	var ChainItem = /*#__PURE__*/function (_SvelteComponent) {
	  _inherits(ChainItem, _SvelteComponent);
	  var _super = _createSuper$1(ChainItem);
	  function ChainItem(options) {
	    var _this;
	    _classCallCheck(this, ChainItem);
	    _this = _super.call(this);
	    init(_assertThisInitialized(_this), options, instance$1, create_fragment$1, safe_not_equal, {
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
	    return _this;
	  }
	  _createClass(ChainItem, [{
	    key: "validate",
	    get: function get() {
	      return this.$$.ctx[58];
	    }
	  }, {
	    key: "getValues",
	    get: function get() {
	      return this.$$.ctx[59];
	    }
	  }]);
	  return ChainItem;
	}(SvelteComponent);

	function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
	function get_each_context(ctx, list, i) {
	  var child_ctx = _sliceInstanceProperty$1(ctx).call(ctx);
	  child_ctx[9] = list[i];
	  child_ctx[10] = list;
	  child_ctx[11] = i;
	  return child_ctx;
	}

	// (30:0) {#each items as item, index (item)}
	function create_each_block(key_1, ctx) {
	  var first;
	  var chainitem;
	  var index = /*index*/ctx[11];
	  var current;
	  var chainitem_spread_levels = [/*data*/ctx[1], {
	    item: /*item*/ctx[9]
	  }, {
	    index: /*index*/ctx[11]
	  }, {
	    hasDropBtn: /*multiple*/ctx[2]
	  }, {
	    showDropBtn: /*index*/ctx[11] > 0
	  }];
	  var assign_chainitem = function assign_chainitem() {
	    return (/*chainitem_binding*/ctx[8](chainitem, index)
	    );
	  };
	  var unassign_chainitem = function unassign_chainitem() {
	    return (/*chainitem_binding*/ctx[8](null, index)
	    );
	  };
	  var chainitem_props = {};
	  for (var i = 0; i < chainitem_spread_levels.length; i += 1) {
	    chainitem_props = assign(chainitem_props, chainitem_spread_levels[i]);
	  }
	  chainitem = new ChainItem({
	    props: chainitem_props
	  });
	  assign_chainitem();
	  chainitem.$on("dropItem", /*onDropItem*/ctx[5]);
	  return {
	    key: key_1,
	    first: null,
	    c: function c() {
	      first = empty();
	      create_component(chainitem.$$.fragment);
	      this.first = first;
	    },
	    m: function m(target, anchor) {
	      insert(target, first, anchor);
	      mount_component(chainitem, target, anchor);
	      current = true;
	    },
	    p: function p(new_ctx, dirty) {
	      ctx = new_ctx;
	      if (index !== /*index*/ctx[11]) {
	        unassign_chainitem();
	        index = /*index*/ctx[11];
	        assign_chainitem();
	      }
	      var chainitem_changes = dirty & /*data, items, multiple*/7 ? get_spread_update(chainitem_spread_levels, [dirty & /*data*/2 && get_spread_object( /*data*/ctx[1]), dirty & /*items*/1 && {
	        item: /*item*/ctx[9]
	      }, dirty & /*items*/1 && {
	        index: /*index*/ctx[11]
	      }, dirty & /*multiple*/4 && {
	        hasDropBtn: /*multiple*/ctx[2]
	      }, dirty & /*items*/1 && {
	        showDropBtn: /*index*/ctx[11] > 0
	      }]) : {};
	      chainitem.$set(chainitem_changes);
	    },
	    i: function i(local) {
	      if (current) return;
	      transition_in(chainitem.$$.fragment, local);
	      current = true;
	    },
	    o: function o(local) {
	      transition_out(chainitem.$$.fragment, local);
	      current = false;
	    },
	    d: function d(detaching) {
	      if (detaching) detach(first);
	      unassign_chainitem();
	      destroy_component(chainitem, detaching);
	    }
	  };
	}

	// (33:0) {#if multiple}
	function create_if_block(ctx) {
	  var div;
	  var button;
	  var span;
	  var t_value = /*data*/ctx[1].l10n.add_service + "";
	  var t;
	  var mounted;
	  var dispose;
	  return {
	    c: function c() {
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
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, button);
	      append(button, span);
	      append(span, t);
	      if (!mounted) {
	        dispose = listen(button, "click", /*onAddItem*/ctx[4]);
	        mounted = true;
	      }
	    },
	    p: function p(ctx, dirty) {
	      if (dirty & /*data*/2 && t_value !== (t_value = /*data*/ctx[1].l10n.add_service + "")) set_data(t, t_value);
	    },
	    d: function d(detaching) {
	      if (detaching) detach(div);
	      mounted = false;
	      dispose();
	    }
	  };
	}
	function create_fragment(ctx) {
	  var each_blocks = [];
	  var each_1_lookup = new _Map();
	  var t;
	  var if_block_anchor;
	  var current;
	  var each_value = /*items*/ctx[0];
	  var get_key = function get_key(ctx) {
	    return (/*item*/ctx[9]
	    );
	  };
	  for (var i = 0; i < each_value.length; i += 1) {
	    var child_ctx = get_each_context(ctx, each_value, i);
	    var key = get_key(child_ctx);
	    each_1_lookup.set(key, each_blocks[i] = create_each_block(key, child_ctx));
	  }
	  var if_block = /*multiple*/ctx[2] && create_if_block(ctx);
	  return {
	    c: function c() {
	      for (var _i = 0; _i < each_blocks.length; _i += 1) {
	        each_blocks[_i].c();
	      }
	      t = space();
	      if (if_block) if_block.c();
	      if_block_anchor = empty();
	    },
	    m: function m(target, anchor) {
	      for (var _i2 = 0; _i2 < each_blocks.length; _i2 += 1) {
	        each_blocks[_i2].m(target, anchor);
	      }
	      insert(target, t, anchor);
	      if (if_block) if_block.m(target, anchor);
	      insert(target, if_block_anchor, anchor);
	      current = true;
	    },
	    p: function p(ctx, _ref) {
	      var _ref2 = _slicedToArray(_ref, 1),
	        dirty = _ref2[0];
	      if (dirty & /*data, items, multiple, els, onDropItem*/47) {
	        each_value = /*items*/ctx[0];
	        group_outros();
	        each_blocks = update_keyed_each(each_blocks, dirty, get_key, 1, ctx, each_value, each_1_lookup, t.parentNode, outro_and_destroy_block, create_each_block, t, get_each_context);
	        check_outros();
	      }
	      if ( /*multiple*/ctx[2]) {
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
	    i: function i(local) {
	      if (current) return;
	      for (var _i3 = 0; _i3 < each_value.length; _i3 += 1) {
	        transition_in(each_blocks[_i3]);
	      }
	      current = true;
	    },
	    o: function o(local) {
	      for (var _i4 = 0; _i4 < each_blocks.length; _i4 += 1) {
	        transition_out(each_blocks[_i4]);
	      }
	      current = false;
	    },
	    d: function d(detaching) {
	      for (var _i5 = 0; _i5 < each_blocks.length; _i5 += 1) {
	        each_blocks[_i5].d(detaching);
	      }
	      if (detaching) detach(t);
	      if (if_block) if_block.d(detaching);
	      if (detaching) detach(if_block_anchor);
	    }
	  };
	}
	function instance($$self, $$props, $$invalidate) {
	  var _$$props$items = $$props.items,
	    items = _$$props$items === void 0 ? [] : _$$props$items;
	  var _$$props$data = $$props.data,
	    data = _$$props$data === void 0 ? {} : _$$props$data;
	  var _$$props$multiple = $$props.multiple,
	    multiple = _$$props$multiple === void 0 ? false : _$$props$multiple;
	  var els = [];
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
	    return _mapInstanceProperty(_context = _filterInstanceProperty(els).call(els, function (el) {
	      return !!el;
	    })).call(_context, function (el) {
	      return el.validate();
	    });
	  }
	  function getValues() {
	    var _context2;
	    return _mapInstanceProperty(_context2 = _filterInstanceProperty(els).call(els, function (el) {
	      return !!el;
	    })).call(_context2, function (el) {
	      return el.getValues();
	    });
	  }
	  function chainitem_binding($$value, index) {
	    binding_callbacks[$$value ? 'unshift' : 'push'](function () {
	      els[index] = $$value;
	      $$invalidate(3, els);
	    });
	  }
	  $$self.$$set = function ($$props) {
	    if ('items' in $$props) $$invalidate(0, items = $$props.items);
	    if ('data' in $$props) $$invalidate(1, data = $$props.data);
	    if ('multiple' in $$props) $$invalidate(2, multiple = $$props.multiple);
	  };
	  return [items, data, multiple, els, onAddItem, onDropItem, validate, getValues, chainitem_binding];
	}
	var Chain = /*#__PURE__*/function (_SvelteComponent) {
	  _inherits(Chain, _SvelteComponent);
	  var _super = _createSuper(Chain);
	  function Chain(options) {
	    var _this;
	    _classCallCheck(this, Chain);
	    _this = _super.call(this);
	    init(_assertThisInitialized(_this), options, instance, create_fragment, safe_not_equal, {
	      items: 0,
	      data: 1,
	      multiple: 2,
	      validate: 6,
	      getValues: 7
	    });
	    return _this;
	  }
	  _createClass(Chain, [{
	    key: "validate",
	    get: function get() {
	      return this.$$.ctx[6];
	    }
	  }, {
	    key: "getValues",
	    get: function get() {
	      return this.$$.ctx[7];
	    }
	  }]);
	  return Chain;
	}(SvelteComponent);

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
	  $__default["default"].extend(data, params);
	  booklyAjax({
	    data: data
	  }).then(function (response) {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    var $chain = $__default["default"]('.bookly-js-chain', $container),
	      $date_from = $__default["default"]('.bookly-js-date-from', $container),
	      $week_days = $__default["default"]('.bookly-js-week-days', $container),
	      $select_time_from = $__default["default"]('.bookly-js-select-time-from', $container),
	      $select_time_to = $__default["default"]('.bookly-js-select-time-to', $container),
	      $next_step = $__default["default"]('.bookly-js-next-step', $container),
	      $mobile_next_step = $__default["default"]('.bookly-js-mobile-next-step', $container),
	      $mobile_prev_step = $__default["default"]('.bookly-js-mobile-prev-step', $container),
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
	      $__default["default"].each(services, function (id, service) {
	        service.name = service.name + ' ( ' + service.duration + ' )';
	      });
	    }
	    var c = new Chain({
	      target: $chain.get(0),
	      props: {
	        items: chain,
	        data: {
	          locations: locations,
	          categories: categories,
	          services: services,
	          staff: staff,
	          defaults: defaults,
	          required: required,
	          servicesPerLocation: servicesPerLocation,
	          staffNameWithPrice: staffNameWithPrice,
	          collaborativeHideStaff: collaborativeHideStaff,
	          showRatings: showRatings,
	          showCategoryInfo: showCategoryInfo,
	          showServiceInfo: showServiceInfo,
	          showStaffInfo: showStaffInfo,
	          maxQuantity: maxQuantity,
	          date_from_element: $date_from,
	          hasLocationSelect: !opt[params.form_id].form_attributes.hide_locations,
	          hasCategorySelect: !opt[params.form_id].form_attributes.hide_categories,
	          hasServiceSelect: !(opt[params.form_id].form_attributes.hide_services && defaults.service_id),
	          hasStaffSelect: !opt[params.form_id].form_attributes.hide_staff_members,
	          hasDurationSelect: !opt[params.form_id].form_attributes.hide_service_duration,
	          hasNopSelect: opt[params.form_id].form_attributes.show_number_of_persons,
	          hasQuantitySelect: !opt[params.form_id].form_attributes.hide_quantity,
	          l10n: l10n
	        },
	        multiple: multiple
	      }
	    });

	    // Init Pickadate.
	    $date_from.data('date_min', response.date_min || true);
	    $date_from.pickadate({
	      formatSubmit: 'yyyy-mm-dd',
	      format: opt[params.form_id].date_format,
	      min: response.date_min || true,
	      max: response.date_max || true,
	      clear: false,
	      close: false,
	      today: BooklyL10n.today,
	      monthsFull: BooklyL10n.months,
	      monthsShort: BooklyL10n.monthsShort,
	      weekdaysFull: BooklyL10n.days,
	      weekdaysShort: BooklyL10n.daysShort,
	      labelMonthNext: BooklyL10n.nextMonth,
	      labelMonthPrev: BooklyL10n.prevMonth,
	      firstDay: opt[params.form_id].firstDay,
	      onSet: function onSet(timestamp) {
	        if ($__default["default"].isNumeric(timestamp.select)) {
	          // Checks appropriate day of the week
	          var date = new Date(timestamp.select);
	          $__default["default"]('.bookly-js-week-days input:checkbox[value="' + (date.getDay() + 1) + '"]:not(:checked)', $container).attr('checked', true).trigger('change');
	        }
	      },
	      onClose: function onClose() {
	        $date_from.data('updated', true);
	        // Hide for skip tab navigations by days of the month when the calendar is closed
	        $__default["default"]('#' + $date_from.attr('aria-owns')).hide();
	      }
	    }).focusin(function () {
	      // Restore calendar visibility, changed on onClose
	      $__default["default"]('#' + $date_from.attr('aria-owns')).show();
	    });
	    $__default["default"]('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'service'
	      });
	    });
	    if (opt[params.form_id].form_attributes.hide_date) {
	      $__default["default"]('.bookly-js-available-date', $container).hide();
	    }
	    if (opt[params.form_id].form_attributes.hide_week_days) {
	      $__default["default"]('.bookly-js-week-days', $container).hide();
	    }
	    if (opt[params.form_id].form_attributes.hide_time_range) {
	      $__default["default"]('.bookly-js-time-range', $container).hide();
	    }

	    // time from
	    $select_time_from.on('change', function () {
	      var start_time = $__default["default"](this).val(),
	        end_time = $select_time_to.val(),
	        $last_time_entry = $__default["default"]('option:last', $select_time_from);
	      $select_time_to.empty();

	      // case when we click on the not last time entry
	      if ($select_time_from[0].selectedIndex < $last_time_entry.index()) {
	        // clone and append all next "time_from" time entries to "time_to" list
	        $__default["default"]('option', this).each(function () {
	          if ($__default["default"](this).val() > start_time) {
	            $select_time_to.append($__default["default"](this).clone());
	          }
	        });
	        // case when we click on the last time entry
	      } else {
	        $select_time_to.append($last_time_entry.clone()).val($last_time_entry.val());
	      }
	      var first_value = $__default["default"]('option:first', $select_time_to).val();
	      $select_time_to.val(end_time >= first_value ? end_time : first_value);
	    });
	    var stepServiceValidator = function stepServiceValidator() {
	      var valid = true,
	        $scroll_to = null;
	      $__default["default"](c.validate()).each(function (_, status) {
	        if (!status.valid) {
	          valid = false;
	          var $el = $__default["default"](status.el);
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
	      if ($week_days.length && !$__default["default"](':checked', $week_days).length) {
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
	            $__default["default"].globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }

	        // Prepare chain data.
	        var _chain = [],
	          has_extras = 0,
	          time_requirements = 0,
	          recurrence_enabled = 1,
	          _time_requirements = {
	            'required': 2,
	            'optional': 1,
	            'off': 0
	          };
	        $__default["default"].each(c.getValues(), function (_, values) {
	          var _service = services[values.serviceId];
	          _chain.push({
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
	        $__default["default"]('.bookly-js-week-days input:checked', $container).each(function () {
	          days.push(this.value);
	        });
	        booklyAjax({
	          type: 'POST',
	          data: {
	            action: 'bookly_session_save',
	            form_id: params.form_id,
	            chain: _chain,
	            date_from: $date_from.pickadate('picker').get('select', 'yyyy-mm-dd'),
	            days: days,
	            time_from: opt[params.form_id].form_attributes.hide_time_range ? null : $select_time_from.val(),
	            time_to: opt[params.form_id].form_attributes.hide_time_range ? null : $select_time_to.val(),
	            no_extras: has_extras == 0
	          }
	        }).then(function (response) {
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
	          $__default["default"]('.bookly-js-mobile-step-1', $container).hide();
	          $__default["default"]('.bookly-stepper li:eq(1)', $container).addClass('bookly-step-active');
	          $__default["default"]('.bookly-stepper li:eq(0)', $container).removeClass('bookly-step-active');
	          $__default["default"]('.bookly-js-mobile-step-2', $container).css('display', 'block');
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
	        $__default["default"]('.bookly-stepper li:eq(0)', $container).addClass('bookly-step-active');
	        $__default["default"]('.bookly-stepper li:eq(1)', $container).removeClass('bookly-step-active');
	      }, 0);
	      $mobile_prev_step.remove();
	    } else {
	      $mobile_prev_step.on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        $__default["default"]('.bookly-js-mobile-step-1', $container).show();
	        $__default["default"]('.bookly-js-mobile-step-2', $container).hide();
	        $__default["default"]('.bookly-stepper li:eq(0)', $container).addClass('bookly-step-active');
	        $__default["default"]('.bookly-stepper li:eq(1)', $container).removeClass('bookly-step-active');
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
	  var $container = $__default["default"]('#bookly-form-' + options.form_id);
	  if (!$container.length) {
	    return;
	  }
	  opt[options.form_id] = options;
	  opt[options.form_id].$container = $container;
	  opt[options.form_id].timeZone = (typeof Intl === "undefined" ? "undefined" : _typeof(Intl)) === 'object' ? Intl.DateTimeFormat().resolvedOptions().timeZone : undefined;
	  opt[options.form_id].timeZoneOffset = new Date().getTimezoneOffset();
	  opt[options.form_id].skip_steps.service = options.skip_steps.service_part1 && options.skip_steps.service_part2;

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
	            data: $__default["default"].extend(userInfo, {
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
	  if (async !== undefined) {
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
const bookly_js_created_at = "2023-11-07";
