const booklyJsVersion="24.4";
/*!*/
var bookly = (function ($$O) {
	'use strict';

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function getDefaultExportFromCjs (x) {
		return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
	}

	var fails$L = function (exec) {
	  try {
	    return !!exec();
	  } catch (error) {
	    return true;
	  }
	};

	var fails$K = fails$L;

	var functionBindNative$1 = !fails$K(function () {
	  // eslint-disable-next-line es/no-function-prototype-bind -- safe
	  var test = (function () { /* empty */ }).bind();
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return typeof test != 'function' || test.hasOwnProperty('prototype');
	});

	var NATIVE_BIND$7 = functionBindNative$1;

	var FunctionPrototype$5 = Function.prototype;
	var call$q = FunctionPrototype$5.call;
	var uncurryThisWithBind$1 = NATIVE_BIND$7 && FunctionPrototype$5.bind.bind(call$q, call$q);

	var functionUncurryThis$1 = NATIVE_BIND$7 ? uncurryThisWithBind$1 : function (fn) {
	  return function () {
	    return call$q.apply(fn, arguments);
	  };
	};

	var uncurryThis$N = functionUncurryThis$1;

	var objectIsPrototypeOf$1 = uncurryThis$N({}.isPrototypeOf);

	var check$1 = function (it) {
	  return it && it.Math === Math && it;
	};

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global$G =
	  // eslint-disable-next-line es/no-global-this -- safe
	  check$1(typeof globalThis == 'object' && globalThis) ||
	  check$1(typeof window == 'object' && window) ||
	  // eslint-disable-next-line no-restricted-globals -- safe
	  check$1(typeof self == 'object' && self) ||
	  check$1(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
	  check$1(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
	  // eslint-disable-next-line no-new-func -- fallback
	  (function () { return this; })() || Function('return this')();

	var NATIVE_BIND$6 = functionBindNative$1;

	var FunctionPrototype$4 = Function.prototype;
	var apply$7 = FunctionPrototype$4.apply;
	var call$p = FunctionPrototype$4.call;

	// eslint-disable-next-line es/no-reflect -- safe
	var functionApply$1 = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND$6 ? call$p.bind(apply$7) : function () {
	  return call$p.apply(apply$7, arguments);
	});

	var uncurryThis$M = functionUncurryThis$1;

	var toString$n = uncurryThis$M({}.toString);
	var stringSlice$4 = uncurryThis$M(''.slice);

	var classofRaw$5 = function (it) {
	  return stringSlice$4(toString$n(it), 8, -1);
	};

	var classofRaw$4 = classofRaw$5;
	var uncurryThis$L = functionUncurryThis$1;

	var functionUncurryThisClause$1 = function (fn) {
	  // Nashorn bug:
	  //   https://github.com/zloirock/core-js/issues/1128
	  //   https://github.com/zloirock/core-js/issues/1130
	  if (classofRaw$4(fn) === 'Function') return uncurryThis$L(fn);
	};

	// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
	var documentAll$3 = typeof document == 'object' && document.all;

	// `IsCallable` abstract operation
	// https://tc39.es/ecma262/#sec-iscallable
	// eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
	var isCallable$A = typeof documentAll$3 == 'undefined' && documentAll$3 !== undefined ? function (argument) {
	  return typeof argument == 'function' || argument === documentAll$3;
	} : function (argument) {
	  return typeof argument == 'function';
	};

	var objectGetOwnPropertyDescriptor$1 = {};

	var fails$J = fails$L;

	// Detect IE8's incomplete defineProperty implementation
	var descriptors$1 = !fails$J(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] !== 7;
	});

	var NATIVE_BIND$5 = functionBindNative$1;

	var call$o = Function.prototype.call;

	var functionCall$1 = NATIVE_BIND$5 ? call$o.bind(call$o) : function () {
	  return call$o.apply(call$o, arguments);
	};

	var objectPropertyIsEnumerable$1 = {};

	var $propertyIsEnumerable$2 = {}.propertyIsEnumerable;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$5 = Object.getOwnPropertyDescriptor;

	// Nashorn ~ JDK8 bug
	var NASHORN_BUG$1 = getOwnPropertyDescriptor$5 && !$propertyIsEnumerable$2.call({ 1: 2 }, 1);

	// `Object.prototype.propertyIsEnumerable` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
	objectPropertyIsEnumerable$1.f = NASHORN_BUG$1 ? function propertyIsEnumerable(V) {
	  var descriptor = getOwnPropertyDescriptor$5(this, V);
	  return !!descriptor && descriptor.enumerable;
	} : $propertyIsEnumerable$2;

	var createPropertyDescriptor$b = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};

	var uncurryThis$K = functionUncurryThis$1;
	var fails$I = fails$L;
	var classof$n = classofRaw$5;

	var $Object$a = Object;
	var split$1 = uncurryThis$K(''.split);

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var indexedObject$1 = fails$I(function () {
	  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return !$Object$a('z').propertyIsEnumerable(0);
	}) ? function (it) {
	  return classof$n(it) === 'String' ? split$1(it, '') : $Object$a(it);
	} : $Object$a;

	// we can't use just `it == null` since of `document.all` special case
	// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
	var isNullOrUndefined$c = function (it) {
	  return it === null || it === undefined;
	};

	var isNullOrUndefined$b = isNullOrUndefined$c;

	var $TypeError$q = TypeError;

	// `RequireObjectCoercible` abstract operation
	// https://tc39.es/ecma262/#sec-requireobjectcoercible
	var requireObjectCoercible$e = function (it) {
	  if (isNullOrUndefined$b(it)) throw new $TypeError$q("Can't call method on " + it);
	  return it;
	};

	// toObject with fallback for non-array-like ES3 strings
	var IndexedObject$3 = indexedObject$1;
	var requireObjectCoercible$d = requireObjectCoercible$e;

	var toIndexedObject$g = function (it) {
	  return IndexedObject$3(requireObjectCoercible$d(it));
	};

	var isCallable$z = isCallable$A;

	var isObject$w = function (it) {
	  return typeof it == 'object' ? it !== null : isCallable$z(it);
	};

	var path$m = {};

	var path$l = path$m;
	var global$F = global$G;
	var isCallable$y = isCallable$A;

	var aFunction$1 = function (variable) {
	  return isCallable$y(variable) ? variable : undefined;
	};

	var getBuiltIn$f = function (namespace, method) {
	  return arguments.length < 2 ? aFunction$1(path$l[namespace]) || aFunction$1(global$F[namespace])
	    : path$l[namespace] && path$l[namespace][method] || global$F[namespace] && global$F[namespace][method];
	};

	var engineUserAgent$1 = typeof navigator != 'undefined' && String(navigator.userAgent) || '';

	var global$E = global$G;
	var userAgent$7 = engineUserAgent$1;

	var process$4 = global$E.process;
	var Deno$2 = global$E.Deno;
	var versions$1 = process$4 && process$4.versions || Deno$2 && Deno$2.version;
	var v8$1 = versions$1 && versions$1.v8;
	var match$1, version$1;

	if (v8$1) {
	  match$1 = v8$1.split('.');
	  // in old Chrome, versions of V8 isn't V8 = Chrome / 10
	  // but their correct versions are not interesting for us
	  version$1 = match$1[0] > 0 && match$1[0] < 4 ? 1 : +(match$1[0] + match$1[1]);
	}

	// BrowserFS NodeJS `process` polyfill incorrectly set `.v8` to `0.0`
	// so check `userAgent` even if `.v8` exists, but 0
	if (!version$1 && userAgent$7) {
	  match$1 = userAgent$7.match(/Edge\/(\d+)/);
	  if (!match$1 || match$1[1] >= 74) {
	    match$1 = userAgent$7.match(/Chrome\/(\d+)/);
	    if (match$1) version$1 = +match$1[1];
	  }
	}

	var engineV8Version$1 = version$1;

	/* eslint-disable es/no-symbol -- required for testing */
	var V8_VERSION$5 = engineV8Version$1;
	var fails$H = fails$L;
	var global$D = global$G;

	var $String$9 = global$D.String;

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
	var symbolConstructorDetection$1 = !!Object.getOwnPropertySymbols && !fails$H(function () {
	  var symbol = Symbol('symbol detection');
	  // Chrome 38 Symbol has incorrect toString conversion
	  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
	  // nb: Do not call `String` directly to avoid this being optimized out to `symbol+''` which will,
	  // of course, fail.
	  return !$String$9(symbol) || !(Object(symbol) instanceof Symbol) ||
	    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
	    !Symbol.sham && V8_VERSION$5 && V8_VERSION$5 < 41;
	});

	/* eslint-disable es/no-symbol -- required for testing */
	var NATIVE_SYMBOL$4 = symbolConstructorDetection$1;

	var useSymbolAsUid$1 = NATIVE_SYMBOL$4
	  && !Symbol.sham
	  && typeof Symbol.iterator == 'symbol';

	var getBuiltIn$e = getBuiltIn$f;
	var isCallable$x = isCallable$A;
	var isPrototypeOf$o = objectIsPrototypeOf$1;
	var USE_SYMBOL_AS_UID$3 = useSymbolAsUid$1;

	var $Object$9 = Object;

	var isSymbol$6 = USE_SYMBOL_AS_UID$3 ? function (it) {
	  return typeof it == 'symbol';
	} : function (it) {
	  var $Symbol = getBuiltIn$e('Symbol');
	  return isCallable$x($Symbol) && isPrototypeOf$o($Symbol.prototype, $Object$9(it));
	};

	var $String$8 = String;

	var tryToString$9 = function (argument) {
	  try {
	    return $String$8(argument);
	  } catch (error) {
	    return 'Object';
	  }
	};

	var isCallable$w = isCallable$A;
	var tryToString$8 = tryToString$9;

	var $TypeError$p = TypeError;

	// `Assert: IsCallable(argument) is true`
	var aCallable$g = function (argument) {
	  if (isCallable$w(argument)) return argument;
	  throw new $TypeError$p(tryToString$8(argument) + ' is not a function');
	};

	var aCallable$f = aCallable$g;
	var isNullOrUndefined$a = isNullOrUndefined$c;

	// `GetMethod` abstract operation
	// https://tc39.es/ecma262/#sec-getmethod
	var getMethod$7 = function (V, P) {
	  var func = V[P];
	  return isNullOrUndefined$a(func) ? undefined : aCallable$f(func);
	};

	var call$n = functionCall$1;
	var isCallable$v = isCallable$A;
	var isObject$v = isObject$w;

	var $TypeError$o = TypeError;

	// `OrdinaryToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-ordinarytoprimitive
	var ordinaryToPrimitive$3 = function (input, pref) {
	  var fn, val;
	  if (pref === 'string' && isCallable$v(fn = input.toString) && !isObject$v(val = call$n(fn, input))) return val;
	  if (isCallable$v(fn = input.valueOf) && !isObject$v(val = call$n(fn, input))) return val;
	  if (pref !== 'string' && isCallable$v(fn = input.toString) && !isObject$v(val = call$n(fn, input))) return val;
	  throw new $TypeError$o("Can't convert object to primitive value");
	};

	var shared$7 = {exports: {}};

	var isPure = true;

	var global$C = global$G;

	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var defineProperty$9 = Object.defineProperty;

	var defineGlobalProperty$3 = function (key, value) {
	  try {
	    defineProperty$9(global$C, key, { value: value, configurable: true, writable: true });
	  } catch (error) {
	    global$C[key] = value;
	  } return value;
	};

	var global$B = global$G;
	var defineGlobalProperty$2 = defineGlobalProperty$3;

	var SHARED$1 = '__core-js_shared__';
	var store$7 = global$B[SHARED$1] || defineGlobalProperty$2(SHARED$1, {});

	var sharedStore$1 = store$7;

	var store$6 = sharedStore$1;

	(shared$7.exports = function (key, value) {
	  return store$6[key] || (store$6[key] = value !== undefined ? value : {});
	})('versions', []).push({
	  version: '3.35.0',
	  mode: 'pure' ,
	  copyright: '© 2014-2023 Denis Pushkarev (zloirock.ru)',
	  license: 'https://github.com/zloirock/core-js/blob/v3.35.0/LICENSE',
	  source: 'https://github.com/zloirock/core-js'
	});

	var sharedExports$1 = shared$7.exports;

	var requireObjectCoercible$c = requireObjectCoercible$e;

	var $Object$8 = Object;

	// `ToObject` abstract operation
	// https://tc39.es/ecma262/#sec-toobject
	var toObject$d = function (argument) {
	  return $Object$8(requireObjectCoercible$c(argument));
	};

	var uncurryThis$J = functionUncurryThis$1;
	var toObject$c = toObject$d;

	var hasOwnProperty$1 = uncurryThis$J({}.hasOwnProperty);

	// `HasOwnProperty` abstract operation
	// https://tc39.es/ecma262/#sec-hasownproperty
	// eslint-disable-next-line es/no-object-hasown -- safe
	var hasOwnProperty_1$1 = Object.hasOwn || function hasOwn(it, key) {
	  return hasOwnProperty$1(toObject$c(it), key);
	};

	var uncurryThis$I = functionUncurryThis$1;

	var id$4 = 0;
	var postfix$1 = Math.random();
	var toString$m = uncurryThis$I(1.0.toString);

	var uid$7 = function (key) {
	  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString$m(++id$4 + postfix$1, 36);
	};

	var global$A = global$G;
	var shared$6 = sharedExports$1;
	var hasOwn$l = hasOwnProperty_1$1;
	var uid$6 = uid$7;
	var NATIVE_SYMBOL$3 = symbolConstructorDetection$1;
	var USE_SYMBOL_AS_UID$2 = useSymbolAsUid$1;

	var Symbol$5 = global$A.Symbol;
	var WellKnownSymbolsStore$1 = shared$6('wks');
	var createWellKnownSymbol$1 = USE_SYMBOL_AS_UID$2 ? Symbol$5['for'] || Symbol$5 : Symbol$5 && Symbol$5.withoutSetter || uid$6;

	var wellKnownSymbol$z = function (name) {
	  if (!hasOwn$l(WellKnownSymbolsStore$1, name)) {
	    WellKnownSymbolsStore$1[name] = NATIVE_SYMBOL$3 && hasOwn$l(Symbol$5, name)
	      ? Symbol$5[name]
	      : createWellKnownSymbol$1('Symbol.' + name);
	  } return WellKnownSymbolsStore$1[name];
	};

	var call$m = functionCall$1;
	var isObject$u = isObject$w;
	var isSymbol$5 = isSymbol$6;
	var getMethod$6 = getMethod$7;
	var ordinaryToPrimitive$2 = ordinaryToPrimitive$3;
	var wellKnownSymbol$y = wellKnownSymbol$z;

	var $TypeError$n = TypeError;
	var TO_PRIMITIVE$1 = wellKnownSymbol$y('toPrimitive');

	// `ToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-toprimitive
	var toPrimitive$3 = function (input, pref) {
	  if (!isObject$u(input) || isSymbol$5(input)) return input;
	  var exoticToPrim = getMethod$6(input, TO_PRIMITIVE$1);
	  var result;
	  if (exoticToPrim) {
	    if (pref === undefined) pref = 'default';
	    result = call$m(exoticToPrim, input, pref);
	    if (!isObject$u(result) || isSymbol$5(result)) return result;
	    throw new $TypeError$n("Can't convert object to primitive value");
	  }
	  if (pref === undefined) pref = 'number';
	  return ordinaryToPrimitive$2(input, pref);
	};

	var toPrimitive$2 = toPrimitive$3;
	var isSymbol$4 = isSymbol$6;

	// `ToPropertyKey` abstract operation
	// https://tc39.es/ecma262/#sec-topropertykey
	var toPropertyKey$7 = function (argument) {
	  var key = toPrimitive$2(argument, 'string');
	  return isSymbol$4(key) ? key : key + '';
	};

	var global$z = global$G;
	var isObject$t = isObject$w;

	var document$4 = global$z.document;
	// typeof document.createElement is 'object' in old IE
	var EXISTS$3 = isObject$t(document$4) && isObject$t(document$4.createElement);

	var documentCreateElement$3 = function (it) {
	  return EXISTS$3 ? document$4.createElement(it) : {};
	};

	var DESCRIPTORS$n = descriptors$1;
	var fails$G = fails$L;
	var createElement$2 = documentCreateElement$3;

	// Thanks to IE8 for its funny defineProperty
	var ie8DomDefine$1 = !DESCRIPTORS$n && !fails$G(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty(createElement$2('div'), 'a', {
	    get: function () { return 7; }
	  }).a !== 7;
	});

	var DESCRIPTORS$m = descriptors$1;
	var call$l = functionCall$1;
	var propertyIsEnumerableModule$1 = objectPropertyIsEnumerable$1;
	var createPropertyDescriptor$a = createPropertyDescriptor$b;
	var toIndexedObject$f = toIndexedObject$g;
	var toPropertyKey$6 = toPropertyKey$7;
	var hasOwn$k = hasOwnProperty_1$1;
	var IE8_DOM_DEFINE$3 = ie8DomDefine$1;

	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor$3 = Object.getOwnPropertyDescriptor;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
	objectGetOwnPropertyDescriptor$1.f = DESCRIPTORS$m ? $getOwnPropertyDescriptor$3 : function getOwnPropertyDescriptor(O, P) {
	  O = toIndexedObject$f(O);
	  P = toPropertyKey$6(P);
	  if (IE8_DOM_DEFINE$3) try {
	    return $getOwnPropertyDescriptor$3(O, P);
	  } catch (error) { /* empty */ }
	  if (hasOwn$k(O, P)) return createPropertyDescriptor$a(!call$l(propertyIsEnumerableModule$1.f, O, P), O[P]);
	};

	var fails$F = fails$L;
	var isCallable$u = isCallable$A;

	var replacement$1 = /#|\.prototype\./;

	var isForced$4 = function (feature, detection) {
	  var value = data$1[normalize$1(feature)];
	  return value === POLYFILL$1 ? true
	    : value === NATIVE$1 ? false
	    : isCallable$u(detection) ? fails$F(detection)
	    : !!detection;
	};

	var normalize$1 = isForced$4.normalize = function (string) {
	  return String(string).replace(replacement$1, '.').toLowerCase();
	};

	var data$1 = isForced$4.data = {};
	var NATIVE$1 = isForced$4.NATIVE = 'N';
	var POLYFILL$1 = isForced$4.POLYFILL = 'P';

	var isForced_1$1 = isForced$4;

	var uncurryThis$H = functionUncurryThisClause$1;
	var aCallable$e = aCallable$g;
	var NATIVE_BIND$4 = functionBindNative$1;

	var bind$e = uncurryThis$H(uncurryThis$H.bind);

	// optional / simple context binding
	var functionBindContext$1 = function (fn, that) {
	  aCallable$e(fn);
	  return that === undefined ? fn : NATIVE_BIND$4 ? bind$e(fn, that) : function (/* ...args */) {
	    return fn.apply(that, arguments);
	  };
	};

	var objectDefineProperty$1 = {};

	var DESCRIPTORS$l = descriptors$1;
	var fails$E = fails$L;

	// V8 ~ Chrome 36-
	// https://bugs.chromium.org/p/v8/issues/detail?id=3334
	var v8PrototypeDefineBug$1 = DESCRIPTORS$l && fails$E(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty(function () { /* empty */ }, 'prototype', {
	    value: 42,
	    writable: false
	  }).prototype !== 42;
	});

	var isObject$s = isObject$w;

	var $String$7 = String;
	var $TypeError$m = TypeError;

	// `Assert: Type(argument) is Object`
	var anObject$j = function (argument) {
	  if (isObject$s(argument)) return argument;
	  throw new $TypeError$m($String$7(argument) + ' is not an object');
	};

	var DESCRIPTORS$k = descriptors$1;
	var IE8_DOM_DEFINE$2 = ie8DomDefine$1;
	var V8_PROTOTYPE_DEFINE_BUG$3 = v8PrototypeDefineBug$1;
	var anObject$i = anObject$j;
	var toPropertyKey$5 = toPropertyKey$7;

	var $TypeError$l = TypeError;
	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var $defineProperty$1 = Object.defineProperty;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor$2 = Object.getOwnPropertyDescriptor;
	var ENUMERABLE$1 = 'enumerable';
	var CONFIGURABLE$3 = 'configurable';
	var WRITABLE$1 = 'writable';

	// `Object.defineProperty` method
	// https://tc39.es/ecma262/#sec-object.defineproperty
	objectDefineProperty$1.f = DESCRIPTORS$k ? V8_PROTOTYPE_DEFINE_BUG$3 ? function defineProperty(O, P, Attributes) {
	  anObject$i(O);
	  P = toPropertyKey$5(P);
	  anObject$i(Attributes);
	  if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE$1 in Attributes && !Attributes[WRITABLE$1]) {
	    var current = $getOwnPropertyDescriptor$2(O, P);
	    if (current && current[WRITABLE$1]) {
	      O[P] = Attributes.value;
	      Attributes = {
	        configurable: CONFIGURABLE$3 in Attributes ? Attributes[CONFIGURABLE$3] : current[CONFIGURABLE$3],
	        enumerable: ENUMERABLE$1 in Attributes ? Attributes[ENUMERABLE$1] : current[ENUMERABLE$1],
	        writable: false
	      };
	    }
	  } return $defineProperty$1(O, P, Attributes);
	} : $defineProperty$1 : function defineProperty(O, P, Attributes) {
	  anObject$i(O);
	  P = toPropertyKey$5(P);
	  anObject$i(Attributes);
	  if (IE8_DOM_DEFINE$2) try {
	    return $defineProperty$1(O, P, Attributes);
	  } catch (error) { /* empty */ }
	  if ('get' in Attributes || 'set' in Attributes) throw new $TypeError$l('Accessors not supported');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};

	var DESCRIPTORS$j = descriptors$1;
	var definePropertyModule$6 = objectDefineProperty$1;
	var createPropertyDescriptor$9 = createPropertyDescriptor$b;

	var createNonEnumerableProperty$f = DESCRIPTORS$j ? function (object, key, value) {
	  return definePropertyModule$6.f(object, key, createPropertyDescriptor$9(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};

	var global$y = global$G;
	var apply$6 = functionApply$1;
	var uncurryThis$G = functionUncurryThisClause$1;
	var isCallable$t = isCallable$A;
	var getOwnPropertyDescriptor$4 = objectGetOwnPropertyDescriptor$1.f;
	var isForced$3 = isForced_1$1;
	var path$k = path$m;
	var bind$d = functionBindContext$1;
	var createNonEnumerableProperty$e = createNonEnumerableProperty$f;
	var hasOwn$j = hasOwnProperty_1$1;

	var wrapConstructor$1 = function (NativeConstructor) {
	  var Wrapper = function (a, b, c) {
	    if (this instanceof Wrapper) {
	      switch (arguments.length) {
	        case 0: return new NativeConstructor();
	        case 1: return new NativeConstructor(a);
	        case 2: return new NativeConstructor(a, b);
	      } return new NativeConstructor(a, b, c);
	    } return apply$6(NativeConstructor, this, arguments);
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
	var _export$1 = function (options, source) {
	  var TARGET = options.target;
	  var GLOBAL = options.global;
	  var STATIC = options.stat;
	  var PROTO = options.proto;

	  var nativeSource = GLOBAL ? global$y : STATIC ? global$y[TARGET] : (global$y[TARGET] || {}).prototype;

	  var target = GLOBAL ? path$k : path$k[TARGET] || createNonEnumerableProperty$e(path$k, TARGET, {})[TARGET];
	  var targetPrototype = target.prototype;

	  var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
	  var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

	  for (key in source) {
	    FORCED = isForced$3(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
	    // contains in native
	    USE_NATIVE = !FORCED && nativeSource && hasOwn$j(nativeSource, key);

	    targetProperty = target[key];

	    if (USE_NATIVE) if (options.dontCallGetSet) {
	      descriptor = getOwnPropertyDescriptor$4(nativeSource, key);
	      nativeProperty = descriptor && descriptor.value;
	    } else nativeProperty = nativeSource[key];

	    // export native or implementation
	    sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

	    if (USE_NATIVE && typeof targetProperty == typeof sourceProperty) continue;

	    // bind methods to global for calling from export context
	    if (options.bind && USE_NATIVE) resultProperty = bind$d(sourceProperty, global$y);
	    // wrap global constructors for prevent changes in this version
	    else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor$1(sourceProperty);
	    // make static versions for prototype methods
	    else if (PROTO && isCallable$t(sourceProperty)) resultProperty = uncurryThis$G(sourceProperty);
	    // default case
	    else resultProperty = sourceProperty;

	    // add a flag to not completely full polyfills
	    if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
	      createNonEnumerableProperty$e(resultProperty, 'sham', true);
	    }

	    createNonEnumerableProperty$e(target, key, resultProperty);

	    if (PROTO) {
	      VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
	      if (!hasOwn$j(path$k, VIRTUAL_PROTOTYPE)) {
	        createNonEnumerableProperty$e(path$k, VIRTUAL_PROTOTYPE, {});
	      }
	      // export virtual prototype methods
	      createNonEnumerableProperty$e(path$k[VIRTUAL_PROTOTYPE], key, sourceProperty);
	      // export real prototype methods
	      if (options.real && targetPrototype && (FORCED || !targetPrototype[key])) {
	        createNonEnumerableProperty$e(targetPrototype, key, sourceProperty);
	      }
	    }
	  }
	};

	var ceil$2 = Math.ceil;
	var floor$2 = Math.floor;

	// `Math.trunc` method
	// https://tc39.es/ecma262/#sec-math.trunc
	// eslint-disable-next-line es/no-math-trunc -- safe
	var mathTrunc$1 = Math.trunc || function trunc(x) {
	  var n = +x;
	  return (n > 0 ? floor$2 : ceil$2)(n);
	};

	var trunc$1 = mathTrunc$1;

	// `ToIntegerOrInfinity` abstract operation
	// https://tc39.es/ecma262/#sec-tointegerorinfinity
	var toIntegerOrInfinity$9 = function (argument) {
	  var number = +argument;
	  // eslint-disable-next-line no-self-compare -- NaN check
	  return number !== number || number === 0 ? 0 : trunc$1(number);
	};

	var toIntegerOrInfinity$8 = toIntegerOrInfinity$9;

	var max$5 = Math.max;
	var min$4 = Math.min;

	// Helper for a popular repeating case of the spec:
	// Let integer be ? ToInteger(index).
	// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
	var toAbsoluteIndex$8 = function (index, length) {
	  var integer = toIntegerOrInfinity$8(index);
	  return integer < 0 ? max$5(integer + length, 0) : min$4(integer, length);
	};

	var toIntegerOrInfinity$7 = toIntegerOrInfinity$9;

	var min$3 = Math.min;

	// `ToLength` abstract operation
	// https://tc39.es/ecma262/#sec-tolength
	var toLength$4 = function (argument) {
	  return argument > 0 ? min$3(toIntegerOrInfinity$7(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
	};

	var toLength$3 = toLength$4;

	// `LengthOfArrayLike` abstract operation
	// https://tc39.es/ecma262/#sec-lengthofarraylike
	var lengthOfArrayLike$f = function (obj) {
	  return toLength$3(obj.length);
	};

	var toIndexedObject$e = toIndexedObject$g;
	var toAbsoluteIndex$7 = toAbsoluteIndex$8;
	var lengthOfArrayLike$e = lengthOfArrayLike$f;

	// `Array.prototype.{ indexOf, includes }` methods implementation
	var createMethod$9 = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIndexedObject$e($this);
	    var length = lengthOfArrayLike$e(O);
	    var index = toAbsoluteIndex$7(fromIndex, length);
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

	var arrayIncludes$1 = {
	  // `Array.prototype.includes` method
	  // https://tc39.es/ecma262/#sec-array.prototype.includes
	  includes: createMethod$9(true),
	  // `Array.prototype.indexOf` method
	  // https://tc39.es/ecma262/#sec-array.prototype.indexof
	  indexOf: createMethod$9(false)
	};

	var $$N = _export$1;
	var $includes$1 = arrayIncludes$1.includes;
	var fails$D = fails$L;

	// FF99+ bug
	var BROKEN_ON_SPARSE$1 = fails$D(function () {
	  // eslint-disable-next-line es/no-array-prototype-includes -- detection
	  return !Array(1).includes();
	});

	// `Array.prototype.includes` method
	// https://tc39.es/ecma262/#sec-array.prototype.includes
	$$N({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE$1 }, {
	  includes: function includes(el /* , fromIndex = 0 */) {
	    return $includes$1(this, el, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var global$x = global$G;
	var path$j = path$m;

	var getBuiltInPrototypeMethod$g = function (CONSTRUCTOR, METHOD) {
	  var Namespace = path$j[CONSTRUCTOR + 'Prototype'];
	  var pureMethod = Namespace && Namespace[METHOD];
	  if (pureMethod) return pureMethod;
	  var NativeConstructor = global$x[CONSTRUCTOR];
	  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
	  return NativePrototype && NativePrototype[METHOD];
	};

	var getBuiltInPrototypeMethod$f = getBuiltInPrototypeMethod$g;

	var includes$9 = getBuiltInPrototypeMethod$f('Array', 'includes');

	var isObject$r = isObject$w;
	var classof$m = classofRaw$5;
	var wellKnownSymbol$x = wellKnownSymbol$z;

	var MATCH$3 = wellKnownSymbol$x('match');

	// `IsRegExp` abstract operation
	// https://tc39.es/ecma262/#sec-isregexp
	var isRegexp$1 = function (it) {
	  var isRegExp;
	  return isObject$r(it) && ((isRegExp = it[MATCH$3]) !== undefined ? !!isRegExp : classof$m(it) === 'RegExp');
	};

	var isRegExp$1 = isRegexp$1;

	var $TypeError$k = TypeError;

	var notARegexp$1 = function (it) {
	  if (isRegExp$1(it)) {
	    throw new $TypeError$k("The method doesn't accept regular expressions");
	  } return it;
	};

	var wellKnownSymbol$w = wellKnownSymbol$z;

	var TO_STRING_TAG$7 = wellKnownSymbol$w('toStringTag');
	var test$2 = {};

	test$2[TO_STRING_TAG$7] = 'z';

	var toStringTagSupport$1 = String(test$2) === '[object z]';

	var TO_STRING_TAG_SUPPORT$5 = toStringTagSupport$1;
	var isCallable$s = isCallable$A;
	var classofRaw$3 = classofRaw$5;
	var wellKnownSymbol$v = wellKnownSymbol$z;

	var TO_STRING_TAG$6 = wellKnownSymbol$v('toStringTag');
	var $Object$7 = Object;

	// ES3 wrong here
	var CORRECT_ARGUMENTS$1 = classofRaw$3(function () { return arguments; }()) === 'Arguments';

	// fallback for IE11 Script Access Denied error
	var tryGet$1 = function (it, key) {
	  try {
	    return it[key];
	  } catch (error) { /* empty */ }
	};

	// getting tag from ES6+ `Object.prototype.toString`
	var classof$l = TO_STRING_TAG_SUPPORT$5 ? classofRaw$3 : function (it) {
	  var O, tag, result;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    // @@toStringTag case
	    : typeof (tag = tryGet$1(O = $Object$7(it), TO_STRING_TAG$6)) == 'string' ? tag
	    // builtinTag case
	    : CORRECT_ARGUMENTS$1 ? classofRaw$3(O)
	    // ES3 arguments fallback
	    : (result = classofRaw$3(O)) === 'Object' && isCallable$s(O.callee) ? 'Arguments' : result;
	};

	var classof$k = classof$l;

	var $String$6 = String;

	var toString$l = function (argument) {
	  if (classof$k(argument) === 'Symbol') throw new TypeError('Cannot convert a Symbol value to a string');
	  return $String$6(argument);
	};

	var wellKnownSymbol$u = wellKnownSymbol$z;

	var MATCH$2 = wellKnownSymbol$u('match');

	var correctIsRegexpLogic$1 = function (METHOD_NAME) {
	  var regexp = /./;
	  try {
	    '/./'[METHOD_NAME](regexp);
	  } catch (error1) {
	    try {
	      regexp[MATCH$2] = false;
	      return '/./'[METHOD_NAME](regexp);
	    } catch (error2) { /* empty */ }
	  } return false;
	};

	var $$M = _export$1;
	var uncurryThis$F = functionUncurryThis$1;
	var notARegExp$1 = notARegexp$1;
	var requireObjectCoercible$b = requireObjectCoercible$e;
	var toString$k = toString$l;
	var correctIsRegExpLogic$1 = correctIsRegexpLogic$1;

	var stringIndexOf$1 = uncurryThis$F(''.indexOf);

	// `String.prototype.includes` method
	// https://tc39.es/ecma262/#sec-string.prototype.includes
	$$M({ target: 'String', proto: true, forced: !correctIsRegExpLogic$1('includes') }, {
	  includes: function includes(searchString /* , position = 0 */) {
	    return !!~stringIndexOf$1(
	      toString$k(requireObjectCoercible$b(this)),
	      toString$k(notARegExp$1(searchString)),
	      arguments.length > 1 ? arguments[1] : undefined
	    );
	  }
	});

	var getBuiltInPrototypeMethod$e = getBuiltInPrototypeMethod$g;

	var includes$8 = getBuiltInPrototypeMethod$e('String', 'includes');

	var isPrototypeOf$n = objectIsPrototypeOf$1;
	var arrayMethod$1 = includes$9;
	var stringMethod$1 = includes$8;

	var ArrayPrototype$g = Array.prototype;
	var StringPrototype$4 = String.prototype;

	var includes$7 = function (it) {
	  var own = it.includes;
	  if (it === ArrayPrototype$g || (isPrototypeOf$n(ArrayPrototype$g, it) && own === ArrayPrototype$g.includes)) return arrayMethod$1;
	  if (typeof it == 'string' || it === StringPrototype$4 || (isPrototypeOf$n(StringPrototype$4, it) && own === StringPrototype$4.includes)) {
	    return stringMethod$1;
	  } return own;
	};

	var parent$y = includes$7;

	var includes$6 = parent$y;

	var includes$5 = includes$6;

	var _includesInstanceProperty$1 = /*@__PURE__*/getDefaultExportFromCjs(includes$5);

	var shared$5 = sharedExports$1;
	var uid$5 = uid$7;

	var keys$4 = shared$5('keys');

	var sharedKey$7 = function (key) {
	  return keys$4[key] || (keys$4[key] = uid$5(key));
	};

	var fails$C = fails$L;

	var correctPrototypeGetter$1 = !fails$C(function () {
	  function F() { /* empty */ }
	  F.prototype.constructor = null;
	  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
	  return Object.getPrototypeOf(new F()) !== F.prototype;
	});

	var hasOwn$i = hasOwnProperty_1$1;
	var isCallable$r = isCallable$A;
	var toObject$b = toObject$d;
	var sharedKey$6 = sharedKey$7;
	var CORRECT_PROTOTYPE_GETTER$1 = correctPrototypeGetter$1;

	var IE_PROTO$3 = sharedKey$6('IE_PROTO');
	var $Object$6 = Object;
	var ObjectPrototype$1 = $Object$6.prototype;

	// `Object.getPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.getprototypeof
	// eslint-disable-next-line es/no-object-getprototypeof -- safe
	var objectGetPrototypeOf$2 = CORRECT_PROTOTYPE_GETTER$1 ? $Object$6.getPrototypeOf : function (O) {
	  var object = toObject$b(O);
	  if (hasOwn$i(object, IE_PROTO$3)) return object[IE_PROTO$3];
	  var constructor = object.constructor;
	  if (isCallable$r(constructor) && object instanceof constructor) {
	    return constructor.prototype;
	  } return object instanceof $Object$6 ? ObjectPrototype$1 : null;
	};

	var uncurryThis$E = functionUncurryThis$1;
	var aCallable$d = aCallable$g;

	var functionUncurryThisAccessor = function (object, key, method) {
	  try {
	    // eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	    return uncurryThis$E(aCallable$d(Object.getOwnPropertyDescriptor(object, key)[method]));
	  } catch (error) { /* empty */ }
	};

	var isObject$q = isObject$w;

	var isPossiblePrototype$1 = function (argument) {
	  return isObject$q(argument) || argument === null;
	};

	var isPossiblePrototype = isPossiblePrototype$1;

	var $String$5 = String;
	var $TypeError$j = TypeError;

	var aPossiblePrototype$1 = function (argument) {
	  if (isPossiblePrototype(argument)) return argument;
	  throw new $TypeError$j("Can't set " + $String$5(argument) + ' as a prototype');
	};

	/* eslint-disable no-proto -- safe */
	var uncurryThisAccessor = functionUncurryThisAccessor;
	var anObject$h = anObject$j;
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
	    setter = uncurryThisAccessor(Object.prototype, '__proto__', 'set');
	    setter(test, []);
	    CORRECT_SETTER = test instanceof Array;
	  } catch (error) { /* empty */ }
	  return function setPrototypeOf(O, proto) {
	    anObject$h(O);
	    aPossiblePrototype(proto);
	    if (CORRECT_SETTER) setter(O, proto);
	    else O.__proto__ = proto;
	    return O;
	  };
	}() : undefined);

	var objectGetOwnPropertyNames$1 = {};

	var hiddenKeys$b = {};

	var uncurryThis$D = functionUncurryThis$1;
	var hasOwn$h = hasOwnProperty_1$1;
	var toIndexedObject$d = toIndexedObject$g;
	var indexOf$5 = arrayIncludes$1.indexOf;
	var hiddenKeys$a = hiddenKeys$b;

	var push$8 = uncurryThis$D([].push);

	var objectKeysInternal$1 = function (object, names) {
	  var O = toIndexedObject$d(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) !hasOwn$h(hiddenKeys$a, key) && hasOwn$h(O, key) && push$8(result, key);
	  // Don't enum bug & hidden keys
	  while (names.length > i) if (hasOwn$h(O, key = names[i++])) {
	    ~indexOf$5(result, key) || push$8(result, key);
	  }
	  return result;
	};

	// IE8- don't enum bug keys
	var enumBugKeys$7 = [
	  'constructor',
	  'hasOwnProperty',
	  'isPrototypeOf',
	  'propertyIsEnumerable',
	  'toLocaleString',
	  'toString',
	  'valueOf'
	];

	var internalObjectKeys$3 = objectKeysInternal$1;
	var enumBugKeys$6 = enumBugKeys$7;

	var hiddenKeys$9 = enumBugKeys$6.concat('length', 'prototype');

	// `Object.getOwnPropertyNames` method
	// https://tc39.es/ecma262/#sec-object.getownpropertynames
	// eslint-disable-next-line es/no-object-getownpropertynames -- safe
	objectGetOwnPropertyNames$1.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
	  return internalObjectKeys$3(O, hiddenKeys$9);
	};

	var objectGetOwnPropertySymbols = {};

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
	objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;

	var getBuiltIn$d = getBuiltIn$f;
	var uncurryThis$C = functionUncurryThis$1;
	var getOwnPropertyNamesModule$2 = objectGetOwnPropertyNames$1;
	var getOwnPropertySymbolsModule = objectGetOwnPropertySymbols;
	var anObject$g = anObject$j;

	var concat$4 = uncurryThis$C([].concat);

	// all object keys, includes non-enumerable and symbols
	var ownKeys$1 = getBuiltIn$d('Reflect', 'ownKeys') || function ownKeys(it) {
	  var keys = getOwnPropertyNamesModule$2.f(anObject$g(it));
	  var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
	  return getOwnPropertySymbols ? concat$4(keys, getOwnPropertySymbols(it)) : keys;
	};

	var hasOwn$g = hasOwnProperty_1$1;
	var ownKeys = ownKeys$1;
	var getOwnPropertyDescriptorModule = objectGetOwnPropertyDescriptor$1;
	var definePropertyModule$5 = objectDefineProperty$1;

	var copyConstructorProperties$1 = function (target, source, exceptions) {
	  var keys = ownKeys(source);
	  var defineProperty = definePropertyModule$5.f;
	  var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
	  for (var i = 0; i < keys.length; i++) {
	    var key = keys[i];
	    if (!hasOwn$g(target, key) && !(exceptions && hasOwn$g(exceptions, key))) {
	      defineProperty(target, key, getOwnPropertyDescriptor(source, key));
	    }
	  }
	};

	var objectDefineProperties$1 = {};

	var internalObjectKeys$2 = objectKeysInternal$1;
	var enumBugKeys$5 = enumBugKeys$7;

	// `Object.keys` method
	// https://tc39.es/ecma262/#sec-object.keys
	// eslint-disable-next-line es/no-object-keys -- safe
	var objectKeys$4 = Object.keys || function keys(O) {
	  return internalObjectKeys$2(O, enumBugKeys$5);
	};

	var DESCRIPTORS$i = descriptors$1;
	var V8_PROTOTYPE_DEFINE_BUG$2 = v8PrototypeDefineBug$1;
	var definePropertyModule$4 = objectDefineProperty$1;
	var anObject$f = anObject$j;
	var toIndexedObject$c = toIndexedObject$g;
	var objectKeys$3 = objectKeys$4;

	// `Object.defineProperties` method
	// https://tc39.es/ecma262/#sec-object.defineproperties
	// eslint-disable-next-line es/no-object-defineproperties -- safe
	objectDefineProperties$1.f = DESCRIPTORS$i && !V8_PROTOTYPE_DEFINE_BUG$2 ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject$f(O);
	  var props = toIndexedObject$c(Properties);
	  var keys = objectKeys$3(Properties);
	  var length = keys.length;
	  var index = 0;
	  var key;
	  while (length > index) definePropertyModule$4.f(O, key = keys[index++], props[key]);
	  return O;
	};

	var getBuiltIn$c = getBuiltIn$f;

	var html$4 = getBuiltIn$c('document', 'documentElement');

	/* global ActiveXObject -- old IE, WSH */
	var anObject$e = anObject$j;
	var definePropertiesModule$1 = objectDefineProperties$1;
	var enumBugKeys$4 = enumBugKeys$7;
	var hiddenKeys$8 = hiddenKeys$b;
	var html$3 = html$4;
	var documentCreateElement$2 = documentCreateElement$3;
	var sharedKey$5 = sharedKey$7;

	var GT$1 = '>';
	var LT$1 = '<';
	var PROTOTYPE$1 = 'prototype';
	var SCRIPT$1 = 'script';
	var IE_PROTO$2 = sharedKey$5('IE_PROTO');

	var EmptyConstructor$1 = function () { /* empty */ };

	var scriptTag$1 = function (content) {
	  return LT$1 + SCRIPT$1 + GT$1 + content + LT$1 + '/' + SCRIPT$1 + GT$1;
	};

	// Create object with fake `null` prototype: use ActiveX Object with cleared prototype
	var NullProtoObjectViaActiveX$1 = function (activeXDocument) {
	  activeXDocument.write(scriptTag$1(''));
	  activeXDocument.close();
	  var temp = activeXDocument.parentWindow.Object;
	  activeXDocument = null; // avoid memory leak
	  return temp;
	};

	// Create object with fake `null` prototype: use iframe Object with cleared prototype
	var NullProtoObjectViaIFrame$1 = function () {
	  // Thrash, waste and sodomy: IE GC bug
	  var iframe = documentCreateElement$2('iframe');
	  var JS = 'java' + SCRIPT$1 + ':';
	  var iframeDocument;
	  iframe.style.display = 'none';
	  html$3.appendChild(iframe);
	  // https://github.com/zloirock/core-js/issues/475
	  iframe.src = String(JS);
	  iframeDocument = iframe.contentWindow.document;
	  iframeDocument.open();
	  iframeDocument.write(scriptTag$1('document.F=Object'));
	  iframeDocument.close();
	  return iframeDocument.F;
	};

	// Check for document.domain and active x support
	// No need to use active x approach when document.domain is not set
	// see https://github.com/es-shims/es5-shim/issues/150
	// variation of https://github.com/kitcambridge/es5-shim/commit/4f738ac066346
	// avoid IE GC bug
	var activeXDocument$1;
	var NullProtoObject$1 = function () {
	  try {
	    activeXDocument$1 = new ActiveXObject('htmlfile');
	  } catch (error) { /* ignore */ }
	  NullProtoObject$1 = typeof document != 'undefined'
	    ? document.domain && activeXDocument$1
	      ? NullProtoObjectViaActiveX$1(activeXDocument$1) // old IE
	      : NullProtoObjectViaIFrame$1()
	    : NullProtoObjectViaActiveX$1(activeXDocument$1); // WSH
	  var length = enumBugKeys$4.length;
	  while (length--) delete NullProtoObject$1[PROTOTYPE$1][enumBugKeys$4[length]];
	  return NullProtoObject$1();
	};

	hiddenKeys$8[IE_PROTO$2] = true;

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	// eslint-disable-next-line es/no-object-create -- safe
	var objectCreate$1 = Object.create || function create(O, Properties) {
	  var result;
	  if (O !== null) {
	    EmptyConstructor$1[PROTOTYPE$1] = anObject$e(O);
	    result = new EmptyConstructor$1();
	    EmptyConstructor$1[PROTOTYPE$1] = null;
	    // add "__proto__" for Object.getPrototypeOf polyfill
	    result[IE_PROTO$2] = O;
	  } else result = NullProtoObject$1();
	  return Properties === undefined ? result : definePropertiesModule$1.f(result, Properties);
	};

	var isObject$p = isObject$w;
	var createNonEnumerableProperty$d = createNonEnumerableProperty$f;

	// `InstallErrorCause` abstract operation
	// https://tc39.es/proposal-error-cause/#sec-errorobjects-install-error-cause
	var installErrorCause$1 = function (O, options) {
	  if (isObject$p(options) && 'cause' in options) {
	    createNonEnumerableProperty$d(O, 'cause', options.cause);
	  }
	};

	var uncurryThis$B = functionUncurryThis$1;

	var $Error$1 = Error;
	var replace$3 = uncurryThis$B(''.replace);

	var TEST = (function (arg) { return String(new $Error$1(arg).stack); })('zxcasd');
	// eslint-disable-next-line redos/no-vulnerable -- safe
	var V8_OR_CHAKRA_STACK_ENTRY = /\n\s*at [^:]*:[^\n]*/;
	var IS_V8_OR_CHAKRA_STACK = V8_OR_CHAKRA_STACK_ENTRY.test(TEST);

	var errorStackClear = function (stack, dropEntries) {
	  if (IS_V8_OR_CHAKRA_STACK && typeof stack == 'string' && !$Error$1.prepareStackTrace) {
	    while (dropEntries--) stack = replace$3(stack, V8_OR_CHAKRA_STACK_ENTRY, '');
	  } return stack;
	};

	var fails$B = fails$L;
	var createPropertyDescriptor$8 = createPropertyDescriptor$b;

	var errorStackInstallable = !fails$B(function () {
	  var error = new Error('a');
	  if (!('stack' in error)) return true;
	  // eslint-disable-next-line es/no-object-defineproperty -- safe
	  Object.defineProperty(error, 'stack', createPropertyDescriptor$8(1, 7));
	  return error.stack !== 7;
	});

	var createNonEnumerableProperty$c = createNonEnumerableProperty$f;
	var clearErrorStack = errorStackClear;
	var ERROR_STACK_INSTALLABLE = errorStackInstallable;

	// non-standard V8
	var captureStackTrace = Error.captureStackTrace;

	var errorStackInstall = function (error, C, stack, dropEntries) {
	  if (ERROR_STACK_INSTALLABLE) {
	    if (captureStackTrace) captureStackTrace(error, C);
	    else createNonEnumerableProperty$c(error, 'stack', clearErrorStack(stack, dropEntries));
	  }
	};

	var iterators$1 = {};

	var wellKnownSymbol$t = wellKnownSymbol$z;
	var Iterators$b = iterators$1;

	var ITERATOR$b = wellKnownSymbol$t('iterator');
	var ArrayPrototype$f = Array.prototype;

	// check on default Array iterator
	var isArrayIteratorMethod$4 = function (it) {
	  return it !== undefined && (Iterators$b.Array === it || ArrayPrototype$f[ITERATOR$b] === it);
	};

	var classof$j = classof$l;
	var getMethod$5 = getMethod$7;
	var isNullOrUndefined$9 = isNullOrUndefined$c;
	var Iterators$a = iterators$1;
	var wellKnownSymbol$s = wellKnownSymbol$z;

	var ITERATOR$a = wellKnownSymbol$s('iterator');

	var getIteratorMethod$6 = function (it) {
	  if (!isNullOrUndefined$9(it)) return getMethod$5(it, ITERATOR$a)
	    || getMethod$5(it, '@@iterator')
	    || Iterators$a[classof$j(it)];
	};

	var call$k = functionCall$1;
	var aCallable$c = aCallable$g;
	var anObject$d = anObject$j;
	var tryToString$7 = tryToString$9;
	var getIteratorMethod$5 = getIteratorMethod$6;

	var $TypeError$i = TypeError;

	var getIterator$4 = function (argument, usingIterator) {
	  var iteratorMethod = arguments.length < 2 ? getIteratorMethod$5(argument) : usingIterator;
	  if (aCallable$c(iteratorMethod)) return anObject$d(call$k(iteratorMethod, argument));
	  throw new $TypeError$i(tryToString$7(argument) + ' is not iterable');
	};

	var call$j = functionCall$1;
	var anObject$c = anObject$j;
	var getMethod$4 = getMethod$7;

	var iteratorClose$4 = function (iterator, kind, value) {
	  var innerResult, innerError;
	  anObject$c(iterator);
	  try {
	    innerResult = getMethod$4(iterator, 'return');
	    if (!innerResult) {
	      if (kind === 'throw') throw value;
	      return value;
	    }
	    innerResult = call$j(innerResult, iterator);
	  } catch (error) {
	    innerError = true;
	    innerResult = error;
	  }
	  if (kind === 'throw') throw value;
	  if (innerError) throw innerResult;
	  anObject$c(innerResult);
	  return value;
	};

	var bind$c = functionBindContext$1;
	var call$i = functionCall$1;
	var anObject$b = anObject$j;
	var tryToString$6 = tryToString$9;
	var isArrayIteratorMethod$3 = isArrayIteratorMethod$4;
	var lengthOfArrayLike$d = lengthOfArrayLike$f;
	var isPrototypeOf$m = objectIsPrototypeOf$1;
	var getIterator$3 = getIterator$4;
	var getIteratorMethod$4 = getIteratorMethod$6;
	var iteratorClose$3 = iteratorClose$4;

	var $TypeError$h = TypeError;

	var Result$1 = function (stopped, result) {
	  this.stopped = stopped;
	  this.result = result;
	};

	var ResultPrototype$1 = Result$1.prototype;

	var iterate$c = function (iterable, unboundFunction, options) {
	  var that = options && options.that;
	  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
	  var IS_RECORD = !!(options && options.IS_RECORD);
	  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
	  var INTERRUPTED = !!(options && options.INTERRUPTED);
	  var fn = bind$c(unboundFunction, that);
	  var iterator, iterFn, index, length, result, next, step;

	  var stop = function (condition) {
	    if (iterator) iteratorClose$3(iterator, 'normal', condition);
	    return new Result$1(true, condition);
	  };

	  var callFn = function (value) {
	    if (AS_ENTRIES) {
	      anObject$b(value);
	      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
	    } return INTERRUPTED ? fn(value, stop) : fn(value);
	  };

	  if (IS_RECORD) {
	    iterator = iterable.iterator;
	  } else if (IS_ITERATOR) {
	    iterator = iterable;
	  } else {
	    iterFn = getIteratorMethod$4(iterable);
	    if (!iterFn) throw new $TypeError$h(tryToString$6(iterable) + ' is not iterable');
	    // optimisation for array iterators
	    if (isArrayIteratorMethod$3(iterFn)) {
	      for (index = 0, length = lengthOfArrayLike$d(iterable); length > index; index++) {
	        result = callFn(iterable[index]);
	        if (result && isPrototypeOf$m(ResultPrototype$1, result)) return result;
	      } return new Result$1(false);
	    }
	    iterator = getIterator$3(iterable, iterFn);
	  }

	  next = IS_RECORD ? iterable.next : iterator.next;
	  while (!(step = call$i(next, iterator)).done) {
	    try {
	      result = callFn(step.value);
	    } catch (error) {
	      iteratorClose$3(iterator, 'throw', error);
	    }
	    if (typeof result == 'object' && result && isPrototypeOf$m(ResultPrototype$1, result)) return result;
	  } return new Result$1(false);
	};

	var toString$j = toString$l;

	var normalizeStringArgument$1 = function (argument, $default) {
	  return argument === undefined ? arguments.length < 2 ? '' : $default : toString$j(argument);
	};

	var $$L = _export$1;
	var isPrototypeOf$l = objectIsPrototypeOf$1;
	var getPrototypeOf$4 = objectGetPrototypeOf$2;
	var setPrototypeOf = objectSetPrototypeOf;
	var copyConstructorProperties = copyConstructorProperties$1;
	var create$a = objectCreate$1;
	var createNonEnumerableProperty$b = createNonEnumerableProperty$f;
	var createPropertyDescriptor$7 = createPropertyDescriptor$b;
	var installErrorCause = installErrorCause$1;
	var installErrorStack = errorStackInstall;
	var iterate$b = iterate$c;
	var normalizeStringArgument = normalizeStringArgument$1;
	var wellKnownSymbol$r = wellKnownSymbol$z;

	var TO_STRING_TAG$5 = wellKnownSymbol$r('toStringTag');
	var $Error = Error;
	var push$7 = [].push;

	var $AggregateError = function AggregateError(errors, message /* , options */) {
	  var isInstance = isPrototypeOf$l(AggregateErrorPrototype, this);
	  var that;
	  if (setPrototypeOf) {
	    that = setPrototypeOf(new $Error(), isInstance ? getPrototypeOf$4(this) : AggregateErrorPrototype);
	  } else {
	    that = isInstance ? this : create$a(AggregateErrorPrototype);
	    createNonEnumerableProperty$b(that, TO_STRING_TAG$5, 'Error');
	  }
	  if (message !== undefined) createNonEnumerableProperty$b(that, 'message', normalizeStringArgument(message));
	  installErrorStack(that, $AggregateError, that.stack, 1);
	  if (arguments.length > 2) installErrorCause(that, arguments[2]);
	  var errorsArray = [];
	  iterate$b(errors, push$7, { that: errorsArray });
	  createNonEnumerableProperty$b(that, 'errors', errorsArray);
	  return that;
	};

	if (setPrototypeOf) setPrototypeOf($AggregateError, $Error);
	else copyConstructorProperties($AggregateError, $Error, { name: true });

	var AggregateErrorPrototype = $AggregateError.prototype = create$a($Error.prototype, {
	  constructor: createPropertyDescriptor$7(1, $AggregateError),
	  message: createPropertyDescriptor$7(1, ''),
	  name: createPropertyDescriptor$7(1, 'AggregateError')
	});

	// `AggregateError` constructor
	// https://tc39.es/ecma262/#sec-aggregate-error-constructor
	$$L({ global: true, constructor: true, arity: 2 }, {
	  AggregateError: $AggregateError
	});

	var global$w = global$G;
	var isCallable$q = isCallable$A;

	var WeakMap$3 = global$w.WeakMap;

	var weakMapBasicDetection$1 = isCallable$q(WeakMap$3) && /native code/.test(String(WeakMap$3));

	var NATIVE_WEAK_MAP$2 = weakMapBasicDetection$1;
	var global$v = global$G;
	var isObject$o = isObject$w;
	var createNonEnumerableProperty$a = createNonEnumerableProperty$f;
	var hasOwn$f = hasOwnProperty_1$1;
	var shared$4 = sharedStore$1;
	var sharedKey$4 = sharedKey$7;
	var hiddenKeys$7 = hiddenKeys$b;

	var OBJECT_ALREADY_INITIALIZED$1 = 'Object already initialized';
	var TypeError$3 = global$v.TypeError;
	var WeakMap$2 = global$v.WeakMap;
	var set$9, get$2, has$2;

	var enforce$1 = function (it) {
	  return has$2(it) ? get$2(it) : set$9(it, {});
	};

	var getterFor$1 = function (TYPE) {
	  return function (it) {
	    var state;
	    if (!isObject$o(it) || (state = get$2(it)).type !== TYPE) {
	      throw new TypeError$3('Incompatible receiver, ' + TYPE + ' required');
	    } return state;
	  };
	};

	if (NATIVE_WEAK_MAP$2 || shared$4.state) {
	  var store$5 = shared$4.state || (shared$4.state = new WeakMap$2());
	  /* eslint-disable no-self-assign -- prototype methods protection */
	  store$5.get = store$5.get;
	  store$5.has = store$5.has;
	  store$5.set = store$5.set;
	  /* eslint-enable no-self-assign -- prototype methods protection */
	  set$9 = function (it, metadata) {
	    if (store$5.has(it)) throw new TypeError$3(OBJECT_ALREADY_INITIALIZED$1);
	    metadata.facade = it;
	    store$5.set(it, metadata);
	    return metadata;
	  };
	  get$2 = function (it) {
	    return store$5.get(it) || {};
	  };
	  has$2 = function (it) {
	    return store$5.has(it);
	  };
	} else {
	  var STATE$1 = sharedKey$4('state');
	  hiddenKeys$7[STATE$1] = true;
	  set$9 = function (it, metadata) {
	    if (hasOwn$f(it, STATE$1)) throw new TypeError$3(OBJECT_ALREADY_INITIALIZED$1);
	    metadata.facade = it;
	    createNonEnumerableProperty$a(it, STATE$1, metadata);
	    return metadata;
	  };
	  get$2 = function (it) {
	    return hasOwn$f(it, STATE$1) ? it[STATE$1] : {};
	  };
	  has$2 = function (it) {
	    return hasOwn$f(it, STATE$1);
	  };
	}

	var internalState$1 = {
	  set: set$9,
	  get: get$2,
	  has: has$2,
	  enforce: enforce$1,
	  getterFor: getterFor$1
	};

	var DESCRIPTORS$h = descriptors$1;
	var hasOwn$e = hasOwnProperty_1$1;

	var FunctionPrototype$3 = Function.prototype;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getDescriptor$1 = DESCRIPTORS$h && Object.getOwnPropertyDescriptor;

	var EXISTS$2 = hasOwn$e(FunctionPrototype$3, 'name');
	// additional protection from minified / mangled / dropped function names
	var PROPER$1 = EXISTS$2 && (function something() { /* empty */ }).name === 'something';
	var CONFIGURABLE$2 = EXISTS$2 && (!DESCRIPTORS$h || (DESCRIPTORS$h && getDescriptor$1(FunctionPrototype$3, 'name').configurable));

	var functionName$1 = {
	  EXISTS: EXISTS$2,
	  PROPER: PROPER$1,
	  CONFIGURABLE: CONFIGURABLE$2
	};

	var createNonEnumerableProperty$9 = createNonEnumerableProperty$f;

	var defineBuiltIn$8 = function (target, key, value, options) {
	  if (options && options.enumerable) target[key] = value;
	  else createNonEnumerableProperty$9(target, key, value);
	  return target;
	};

	var fails$A = fails$L;
	var isCallable$p = isCallable$A;
	var isObject$n = isObject$w;
	var create$9 = objectCreate$1;
	var getPrototypeOf$3 = objectGetPrototypeOf$2;
	var defineBuiltIn$7 = defineBuiltIn$8;
	var wellKnownSymbol$q = wellKnownSymbol$z;

	var ITERATOR$9 = wellKnownSymbol$q('iterator');
	var BUGGY_SAFARI_ITERATORS$3 = false;

	// `%IteratorPrototype%` object
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
	var IteratorPrototype$3, PrototypeOfArrayIteratorPrototype$1, arrayIterator$1;

	/* eslint-disable es/no-array-prototype-keys -- safe */
	if ([].keys) {
	  arrayIterator$1 = [].keys();
	  // Safari 8 has buggy iterators w/o `next`
	  if (!('next' in arrayIterator$1)) BUGGY_SAFARI_ITERATORS$3 = true;
	  else {
	    PrototypeOfArrayIteratorPrototype$1 = getPrototypeOf$3(getPrototypeOf$3(arrayIterator$1));
	    if (PrototypeOfArrayIteratorPrototype$1 !== Object.prototype) IteratorPrototype$3 = PrototypeOfArrayIteratorPrototype$1;
	  }
	}

	var NEW_ITERATOR_PROTOTYPE$1 = !isObject$n(IteratorPrototype$3) || fails$A(function () {
	  var test = {};
	  // FF44- legacy iterators case
	  return IteratorPrototype$3[ITERATOR$9].call(test) !== test;
	});

	if (NEW_ITERATOR_PROTOTYPE$1) IteratorPrototype$3 = {};
	else IteratorPrototype$3 = create$9(IteratorPrototype$3);

	// `%IteratorPrototype%[@@iterator]()` method
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
	if (!isCallable$p(IteratorPrototype$3[ITERATOR$9])) {
	  defineBuiltIn$7(IteratorPrototype$3, ITERATOR$9, function () {
	    return this;
	  });
	}

	var iteratorsCore$1 = {
	  IteratorPrototype: IteratorPrototype$3,
	  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$3
	};

	var TO_STRING_TAG_SUPPORT$4 = toStringTagSupport$1;
	var classof$i = classof$l;

	// `Object.prototype.toString` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.tostring
	var objectToString$1 = TO_STRING_TAG_SUPPORT$4 ? {}.toString : function toString() {
	  return '[object ' + classof$i(this) + ']';
	};

	var TO_STRING_TAG_SUPPORT$3 = toStringTagSupport$1;
	var defineProperty$8 = objectDefineProperty$1.f;
	var createNonEnumerableProperty$8 = createNonEnumerableProperty$f;
	var hasOwn$d = hasOwnProperty_1$1;
	var toString$i = objectToString$1;
	var wellKnownSymbol$p = wellKnownSymbol$z;

	var TO_STRING_TAG$4 = wellKnownSymbol$p('toStringTag');

	var setToStringTag$9 = function (it, TAG, STATIC, SET_METHOD) {
	  var target = STATIC ? it : it && it.prototype;
	  if (target) {
	    if (!hasOwn$d(target, TO_STRING_TAG$4)) {
	      defineProperty$8(target, TO_STRING_TAG$4, { configurable: true, value: TAG });
	    }
	    if (SET_METHOD && !TO_STRING_TAG_SUPPORT$3) {
	      createNonEnumerableProperty$8(target, 'toString', toString$i);
	    }
	  }
	};

	var IteratorPrototype$2 = iteratorsCore$1.IteratorPrototype;
	var create$8 = objectCreate$1;
	var createPropertyDescriptor$6 = createPropertyDescriptor$b;
	var setToStringTag$8 = setToStringTag$9;
	var Iterators$9 = iterators$1;

	var returnThis$3 = function () { return this; };

	var iteratorCreateConstructor$1 = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
	  var TO_STRING_TAG = NAME + ' Iterator';
	  IteratorConstructor.prototype = create$8(IteratorPrototype$2, { next: createPropertyDescriptor$6(+!ENUMERABLE_NEXT, next) });
	  setToStringTag$8(IteratorConstructor, TO_STRING_TAG, false, true);
	  Iterators$9[TO_STRING_TAG] = returnThis$3;
	  return IteratorConstructor;
	};

	var $$K = _export$1;
	var call$h = functionCall$1;
	var FunctionName$1 = functionName$1;
	var createIteratorConstructor$1 = iteratorCreateConstructor$1;
	var getPrototypeOf$2 = objectGetPrototypeOf$2;
	var setToStringTag$7 = setToStringTag$9;
	var defineBuiltIn$6 = defineBuiltIn$8;
	var wellKnownSymbol$o = wellKnownSymbol$z;
	var Iterators$8 = iterators$1;
	var IteratorsCore$1 = iteratorsCore$1;

	var PROPER_FUNCTION_NAME$2 = FunctionName$1.PROPER;
	FunctionName$1.CONFIGURABLE;
	IteratorsCore$1.IteratorPrototype;
	var BUGGY_SAFARI_ITERATORS$2 = IteratorsCore$1.BUGGY_SAFARI_ITERATORS;
	var ITERATOR$8 = wellKnownSymbol$o('iterator');
	var KEYS$1 = 'keys';
	var VALUES$1 = 'values';
	var ENTRIES$1 = 'entries';

	var returnThis$2 = function () { return this; };

	var iteratorDefine$1 = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
	  createIteratorConstructor$1(IteratorConstructor, NAME, next);

	  var getIterationMethod = function (KIND) {
	    if (KIND === DEFAULT && defaultIterator) return defaultIterator;
	    if (!BUGGY_SAFARI_ITERATORS$2 && KIND && KIND in IterablePrototype) return IterablePrototype[KIND];

	    switch (KIND) {
	      case KEYS$1: return function keys() { return new IteratorConstructor(this, KIND); };
	      case VALUES$1: return function values() { return new IteratorConstructor(this, KIND); };
	      case ENTRIES$1: return function entries() { return new IteratorConstructor(this, KIND); };
	    }

	    return function () { return new IteratorConstructor(this); };
	  };

	  var TO_STRING_TAG = NAME + ' Iterator';
	  var INCORRECT_VALUES_NAME = false;
	  var IterablePrototype = Iterable.prototype;
	  var nativeIterator = IterablePrototype[ITERATOR$8]
	    || IterablePrototype['@@iterator']
	    || DEFAULT && IterablePrototype[DEFAULT];
	  var defaultIterator = !BUGGY_SAFARI_ITERATORS$2 && nativeIterator || getIterationMethod(DEFAULT);
	  var anyNativeIterator = NAME === 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
	  var CurrentIteratorPrototype, methods, KEY;

	  // fix native
	  if (anyNativeIterator) {
	    CurrentIteratorPrototype = getPrototypeOf$2(anyNativeIterator.call(new Iterable()));
	    if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
	      // Set @@toStringTag to native iterators
	      setToStringTag$7(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
	      Iterators$8[TO_STRING_TAG] = returnThis$2;
	    }
	  }

	  // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
	  if (PROPER_FUNCTION_NAME$2 && DEFAULT === VALUES$1 && nativeIterator && nativeIterator.name !== VALUES$1) {
	    {
	      INCORRECT_VALUES_NAME = true;
	      defaultIterator = function values() { return call$h(nativeIterator, this); };
	    }
	  }

	  // export additional methods
	  if (DEFAULT) {
	    methods = {
	      values: getIterationMethod(VALUES$1),
	      keys: IS_SET ? defaultIterator : getIterationMethod(KEYS$1),
	      entries: getIterationMethod(ENTRIES$1)
	    };
	    if (FORCED) for (KEY in methods) {
	      if (BUGGY_SAFARI_ITERATORS$2 || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
	        defineBuiltIn$6(IterablePrototype, KEY, methods[KEY]);
	      }
	    } else $$K({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS$2 || INCORRECT_VALUES_NAME }, methods);
	  }

	  // define iterator
	  if ((FORCED) && IterablePrototype[ITERATOR$8] !== defaultIterator) {
	    defineBuiltIn$6(IterablePrototype, ITERATOR$8, defaultIterator, { name: DEFAULT });
	  }
	  Iterators$8[NAME] = defaultIterator;

	  return methods;
	};

	// `CreateIterResultObject` abstract operation
	// https://tc39.es/ecma262/#sec-createiterresultobject
	var createIterResultObject$7 = function (value, done) {
	  return { value: value, done: done };
	};

	var toIndexedObject$b = toIndexedObject$g;
	var Iterators$7 = iterators$1;
	var InternalStateModule$9 = internalState$1;
	objectDefineProperty$1.f;
	var defineIterator$5 = iteratorDefine$1;
	var createIterResultObject$6 = createIterResultObject$7;

	var ARRAY_ITERATOR$1 = 'Array Iterator';
	var setInternalState$9 = InternalStateModule$9.set;
	var getInternalState$3 = InternalStateModule$9.getterFor(ARRAY_ITERATOR$1);

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
	defineIterator$5(Array, 'Array', function (iterated, kind) {
	  setInternalState$9(this, {
	    type: ARRAY_ITERATOR$1,
	    target: toIndexedObject$b(iterated), // target
	    index: 0,                          // next index
	    kind: kind                         // kind
	  });
	// `%ArrayIteratorPrototype%.next` method
	// https://tc39.es/ecma262/#sec-%arrayiteratorprototype%.next
	}, function () {
	  var state = getInternalState$3(this);
	  var target = state.target;
	  var index = state.index++;
	  if (!target || index >= target.length) {
	    state.target = undefined;
	    return createIterResultObject$6(undefined, true);
	  }
	  switch (state.kind) {
	    case 'keys': return createIterResultObject$6(index, false);
	    case 'values': return createIterResultObject$6(target[index], false);
	  } return createIterResultObject$6([index, target[index]], false);
	}, 'values');

	// argumentsList[@@iterator] is %ArrayProto_values%
	// https://tc39.es/ecma262/#sec-createunmappedargumentsobject
	// https://tc39.es/ecma262/#sec-createmappedargumentsobject
	Iterators$7.Arguments = Iterators$7.Array;

	var global$u = global$G;
	var classof$h = classofRaw$5;

	var engineIsNode = classof$h(global$u.process) === 'process';

	var defineProperty$7 = objectDefineProperty$1;

	var defineBuiltInAccessor$5 = function (target, name, descriptor) {
	  return defineProperty$7.f(target, name, descriptor);
	};

	var getBuiltIn$b = getBuiltIn$f;
	var defineBuiltInAccessor$4 = defineBuiltInAccessor$5;
	var wellKnownSymbol$n = wellKnownSymbol$z;
	var DESCRIPTORS$g = descriptors$1;

	var SPECIES$9 = wellKnownSymbol$n('species');

	var setSpecies$4 = function (CONSTRUCTOR_NAME) {
	  var Constructor = getBuiltIn$b(CONSTRUCTOR_NAME);

	  if (DESCRIPTORS$g && Constructor && !Constructor[SPECIES$9]) {
	    defineBuiltInAccessor$4(Constructor, SPECIES$9, {
	      configurable: true,
	      get: function () { return this; }
	    });
	  }
	};

	var isPrototypeOf$k = objectIsPrototypeOf$1;

	var $TypeError$g = TypeError;

	var anInstance$7 = function (it, Prototype) {
	  if (isPrototypeOf$k(Prototype, it)) return it;
	  throw new $TypeError$g('Incorrect invocation');
	};

	var uncurryThis$A = functionUncurryThis$1;
	var isCallable$o = isCallable$A;
	var store$4 = sharedStore$1;

	var functionToString$1 = uncurryThis$A(Function.toString);

	// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
	if (!isCallable$o(store$4.inspectSource)) {
	  store$4.inspectSource = function (it) {
	    return functionToString$1(it);
	  };
	}

	var inspectSource$4 = store$4.inspectSource;

	var uncurryThis$z = functionUncurryThis$1;
	var fails$z = fails$L;
	var isCallable$n = isCallable$A;
	var classof$g = classof$l;
	var getBuiltIn$a = getBuiltIn$f;
	var inspectSource$3 = inspectSource$4;

	var noop$2 = function () { /* empty */ };
	var empty$2 = [];
	var construct$1 = getBuiltIn$a('Reflect', 'construct');
	var constructorRegExp$1 = /^\s*(?:class|function)\b/;
	var exec$4 = uncurryThis$z(constructorRegExp$1.exec);
	var INCORRECT_TO_STRING$1 = !constructorRegExp$1.test(noop$2);

	var isConstructorModern$1 = function isConstructor(argument) {
	  if (!isCallable$n(argument)) return false;
	  try {
	    construct$1(noop$2, empty$2, argument);
	    return true;
	  } catch (error) {
	    return false;
	  }
	};

	var isConstructorLegacy$1 = function isConstructor(argument) {
	  if (!isCallable$n(argument)) return false;
	  switch (classof$g(argument)) {
	    case 'AsyncFunction':
	    case 'GeneratorFunction':
	    case 'AsyncGeneratorFunction': return false;
	  }
	  try {
	    // we can't check .prototype since constructors produced by .bind haven't it
	    // `Function#toString` throws on some built-it function in some legacy engines
	    // (for example, `DOMQuad` and similar in FF41-)
	    return INCORRECT_TO_STRING$1 || !!exec$4(constructorRegExp$1, inspectSource$3(argument));
	  } catch (error) {
	    return true;
	  }
	};

	isConstructorLegacy$1.sham = true;

	// `IsConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-isconstructor
	var isConstructor$7 = !construct$1 || fails$z(function () {
	  var called;
	  return isConstructorModern$1(isConstructorModern$1.call)
	    || !isConstructorModern$1(Object)
	    || !isConstructorModern$1(function () { called = true; })
	    || called;
	}) ? isConstructorLegacy$1 : isConstructorModern$1;

	var isConstructor$6 = isConstructor$7;
	var tryToString$5 = tryToString$9;

	var $TypeError$f = TypeError;

	// `Assert: IsConstructor(argument) is true`
	var aConstructor$1 = function (argument) {
	  if (isConstructor$6(argument)) return argument;
	  throw new $TypeError$f(tryToString$5(argument) + ' is not a constructor');
	};

	var anObject$a = anObject$j;
	var aConstructor = aConstructor$1;
	var isNullOrUndefined$8 = isNullOrUndefined$c;
	var wellKnownSymbol$m = wellKnownSymbol$z;

	var SPECIES$8 = wellKnownSymbol$m('species');

	// `SpeciesConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-speciesconstructor
	var speciesConstructor$2 = function (O, defaultConstructor) {
	  var C = anObject$a(O).constructor;
	  var S;
	  return C === undefined || isNullOrUndefined$8(S = anObject$a(C)[SPECIES$8]) ? defaultConstructor : aConstructor(S);
	};

	var uncurryThis$y = functionUncurryThis$1;

	var arraySlice$7 = uncurryThis$y([].slice);

	var $TypeError$e = TypeError;

	var validateArgumentsLength$2 = function (passed, required) {
	  if (passed < required) throw new $TypeError$e('Not enough arguments');
	  return passed;
	};

	var userAgent$6 = engineUserAgent$1;

	// eslint-disable-next-line redos/no-vulnerable -- safe
	var engineIsIos = /(?:ipad|iphone|ipod).*applewebkit/i.test(userAgent$6);

	var global$t = global$G;
	var apply$5 = functionApply$1;
	var bind$b = functionBindContext$1;
	var isCallable$m = isCallable$A;
	var hasOwn$c = hasOwnProperty_1$1;
	var fails$y = fails$L;
	var html$2 = html$4;
	var arraySlice$6 = arraySlice$7;
	var createElement$1 = documentCreateElement$3;
	var validateArgumentsLength$1 = validateArgumentsLength$2;
	var IS_IOS$1 = engineIsIos;
	var IS_NODE$3 = engineIsNode;

	var set$8 = global$t.setImmediate;
	var clear = global$t.clearImmediate;
	var process$3 = global$t.process;
	var Dispatch = global$t.Dispatch;
	var Function$2 = global$t.Function;
	var MessageChannel = global$t.MessageChannel;
	var String$1 = global$t.String;
	var counter = 0;
	var queue$2 = {};
	var ONREADYSTATECHANGE = 'onreadystatechange';
	var $location, defer, channel, port;

	fails$y(function () {
	  // Deno throws a ReferenceError on `location` access without `--location` flag
	  $location = global$t.location;
	});

	var run$1 = function (id) {
	  if (hasOwn$c(queue$2, id)) {
	    var fn = queue$2[id];
	    delete queue$2[id];
	    fn();
	  }
	};

	var runner = function (id) {
	  return function () {
	    run$1(id);
	  };
	};

	var eventListener = function (event) {
	  run$1(event.data);
	};

	var globalPostMessageDefer = function (id) {
	  // old engines have not location.origin
	  global$t.postMessage(String$1(id), $location.protocol + '//' + $location.host);
	};

	// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
	if (!set$8 || !clear) {
	  set$8 = function setImmediate(handler) {
	    validateArgumentsLength$1(arguments.length, 1);
	    var fn = isCallable$m(handler) ? handler : Function$2(handler);
	    var args = arraySlice$6(arguments, 1);
	    queue$2[++counter] = function () {
	      apply$5(fn, undefined, args);
	    };
	    defer(counter);
	    return counter;
	  };
	  clear = function clearImmediate(id) {
	    delete queue$2[id];
	  };
	  // Node.js 0.8-
	  if (IS_NODE$3) {
	    defer = function (id) {
	      process$3.nextTick(runner(id));
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
	    channel.port1.onmessage = eventListener;
	    defer = bind$b(port.postMessage, port);
	  // Browsers with postMessage, skip WebWorkers
	  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
	  } else if (
	    global$t.addEventListener &&
	    isCallable$m(global$t.postMessage) &&
	    !global$t.importScripts &&
	    $location && $location.protocol !== 'file:' &&
	    !fails$y(globalPostMessageDefer)
	  ) {
	    defer = globalPostMessageDefer;
	    global$t.addEventListener('message', eventListener, false);
	  // IE8-
	  } else if (ONREADYSTATECHANGE in createElement$1('script')) {
	    defer = function (id) {
	      html$2.appendChild(createElement$1('script'))[ONREADYSTATECHANGE] = function () {
	        html$2.removeChild(this);
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
	  set: set$8,
	  clear: clear
	};

	var global$s = global$G;
	var DESCRIPTORS$f = descriptors$1;

	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$3 = Object.getOwnPropertyDescriptor;

	// Avoid NodeJS experimental warning
	var safeGetBuiltIn$1 = function (name) {
	  if (!DESCRIPTORS$f) return global$s[name];
	  var descriptor = getOwnPropertyDescriptor$3(global$s, name);
	  return descriptor && descriptor.value;
	};

	var Queue$2 = function () {
	  this.head = null;
	  this.tail = null;
	};

	Queue$2.prototype = {
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

	var queue$1 = Queue$2;

	var userAgent$5 = engineUserAgent$1;

	var engineIsIosPebble = /ipad|iphone|ipod/i.test(userAgent$5) && typeof Pebble != 'undefined';

	var userAgent$4 = engineUserAgent$1;

	var engineIsWebosWebkit = /web0s(?!.*chrome)/i.test(userAgent$4);

	var global$r = global$G;
	var safeGetBuiltIn = safeGetBuiltIn$1;
	var bind$a = functionBindContext$1;
	var macrotask = task$1.set;
	var Queue$1 = queue$1;
	var IS_IOS = engineIsIos;
	var IS_IOS_PEBBLE = engineIsIosPebble;
	var IS_WEBOS_WEBKIT = engineIsWebosWebkit;
	var IS_NODE$2 = engineIsNode;

	var MutationObserver = global$r.MutationObserver || global$r.WebKitMutationObserver;
	var document$3 = global$r.document;
	var process$2 = global$r.process;
	var Promise$1 = global$r.Promise;
	var microtask$1 = safeGetBuiltIn('queueMicrotask');
	var notify$1, toggle, node, promise$4, then;

	// modern engines have queueMicrotask method
	if (!microtask$1) {
	  var queue = new Queue$1();

	  var flush$1 = function () {
	    var parent, fn;
	    if (IS_NODE$2 && (parent = process$2.domain)) parent.exit();
	    while (fn = queue.get()) try {
	      fn();
	    } catch (error) {
	      if (queue.head) notify$1();
	      throw error;
	    }
	    if (parent) parent.enter();
	  };

	  // browsers with MutationObserver, except iOS - https://github.com/zloirock/core-js/issues/339
	  // also except WebOS Webkit https://github.com/zloirock/core-js/issues/898
	  if (!IS_IOS && !IS_NODE$2 && !IS_WEBOS_WEBKIT && MutationObserver && document$3) {
	    toggle = true;
	    node = document$3.createTextNode('');
	    new MutationObserver(flush$1).observe(node, { characterData: true });
	    notify$1 = function () {
	      node.data = toggle = !toggle;
	    };
	  // environments with maybe non-completely correct, but existent Promise
	  } else if (!IS_IOS_PEBBLE && Promise$1 && Promise$1.resolve) {
	    // Promise.resolve without an argument throws an error in LG WebOS 2
	    promise$4 = Promise$1.resolve(undefined);
	    // workaround of WebKit ~ iOS Safari 10.1 bug
	    promise$4.constructor = Promise$1;
	    then = bind$a(promise$4.then, promise$4);
	    notify$1 = function () {
	      then(flush$1);
	    };
	  // Node.js without promises
	  } else if (IS_NODE$2) {
	    notify$1 = function () {
	      process$2.nextTick(flush$1);
	    };
	  // for other environments - macrotask based on:
	  // - setImmediate
	  // - MessageChannel
	  // - window.postMessage
	  // - onreadystatechange
	  // - setTimeout
	  } else {
	    // `webpack` dev server bug on IE global methods - use bind(fn, global)
	    macrotask = bind$a(macrotask, global$r);
	    notify$1 = function () {
	      macrotask(flush$1);
	    };
	  }

	  microtask$1 = function (fn) {
	    if (!queue.head) notify$1();
	    queue.add(fn);
	  };
	}

	var microtask_1 = microtask$1;

	var hostReportErrors$1 = function (a, b) {
	  try {
	    // eslint-disable-next-line no-console -- safe
	    arguments.length === 1 ? console.error(a) : console.error(a, b);
	  } catch (error) { /* empty */ }
	};

	var perform$5 = function (exec) {
	  try {
	    return { error: false, value: exec() };
	  } catch (error) {
	    return { error: true, value: error };
	  }
	};

	var global$q = global$G;

	var promiseNativeConstructor = global$q.Promise;

	/* global Deno -- Deno case */
	var engineIsDeno = typeof Deno == 'object' && Deno && typeof Deno.version == 'object';

	var IS_DENO$1 = engineIsDeno;
	var IS_NODE$1 = engineIsNode;

	var engineIsBrowser = !IS_DENO$1 && !IS_NODE$1
	  && typeof window == 'object'
	  && typeof document == 'object';

	var global$p = global$G;
	var NativePromiseConstructor$5 = promiseNativeConstructor;
	var isCallable$l = isCallable$A;
	var isForced$2 = isForced_1$1;
	var inspectSource$2 = inspectSource$4;
	var wellKnownSymbol$l = wellKnownSymbol$z;
	var IS_BROWSER = engineIsBrowser;
	var IS_DENO = engineIsDeno;
	var V8_VERSION$4 = engineV8Version$1;

	var NativePromisePrototype$2 = NativePromiseConstructor$5 && NativePromiseConstructor$5.prototype;
	var SPECIES$7 = wellKnownSymbol$l('species');
	var SUBCLASSING = false;
	var NATIVE_PROMISE_REJECTION_EVENT$1 = isCallable$l(global$p.PromiseRejectionEvent);

	var FORCED_PROMISE_CONSTRUCTOR$5 = isForced$2('Promise', function () {
	  var PROMISE_CONSTRUCTOR_SOURCE = inspectSource$2(NativePromiseConstructor$5);
	  var GLOBAL_CORE_JS_PROMISE = PROMISE_CONSTRUCTOR_SOURCE !== String(NativePromiseConstructor$5);
	  // V8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
	  // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
	  // We can't detect it synchronously, so just check versions
	  if (!GLOBAL_CORE_JS_PROMISE && V8_VERSION$4 === 66) return true;
	  // We need Promise#{ catch, finally } in the pure version for preventing prototype pollution
	  if (!(NativePromisePrototype$2['catch'] && NativePromisePrototype$2['finally'])) return true;
	  // We can't use @@species feature detection in V8 since it causes
	  // deoptimization and performance degradation
	  // https://github.com/zloirock/core-js/issues/679
	  if (!V8_VERSION$4 || V8_VERSION$4 < 51 || !/native code/.test(PROMISE_CONSTRUCTOR_SOURCE)) {
	    // Detect correctness of subclassing with @@species support
	    var promise = new NativePromiseConstructor$5(function (resolve) { resolve(1); });
	    var FakePromise = function (exec) {
	      exec(function () { /* empty */ }, function () { /* empty */ });
	    };
	    var constructor = promise.constructor = {};
	    constructor[SPECIES$7] = FakePromise;
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

	var aCallable$b = aCallable$g;

	var $TypeError$d = TypeError;

	var PromiseCapability = function (C) {
	  var resolve, reject;
	  this.promise = new C(function ($$resolve, $$reject) {
	    if (resolve !== undefined || reject !== undefined) throw new $TypeError$d('Bad Promise constructor');
	    resolve = $$resolve;
	    reject = $$reject;
	  });
	  this.resolve = aCallable$b(resolve);
	  this.reject = aCallable$b(reject);
	};

	// `NewPromiseCapability` abstract operation
	// https://tc39.es/ecma262/#sec-newpromisecapability
	newPromiseCapability$2.f = function (C) {
	  return new PromiseCapability(C);
	};

	var $$J = _export$1;
	var IS_NODE = engineIsNode;
	var global$o = global$G;
	var call$g = functionCall$1;
	var defineBuiltIn$5 = defineBuiltIn$8;
	var setToStringTag$6 = setToStringTag$9;
	var setSpecies$3 = setSpecies$4;
	var aCallable$a = aCallable$g;
	var isCallable$k = isCallable$A;
	var isObject$m = isObject$w;
	var anInstance$6 = anInstance$7;
	var speciesConstructor$1 = speciesConstructor$2;
	var task = task$1.set;
	var microtask = microtask_1;
	var hostReportErrors = hostReportErrors$1;
	var perform$4 = perform$5;
	var Queue = queue$1;
	var InternalStateModule$8 = internalState$1;
	var NativePromiseConstructor$4 = promiseNativeConstructor;
	var PromiseConstructorDetection = promiseConstructorDetection;
	var newPromiseCapabilityModule$6 = newPromiseCapability$2;

	var PROMISE = 'Promise';
	var FORCED_PROMISE_CONSTRUCTOR$4 = PromiseConstructorDetection.CONSTRUCTOR;
	var NATIVE_PROMISE_REJECTION_EVENT = PromiseConstructorDetection.REJECTION_EVENT;
	PromiseConstructorDetection.SUBCLASSING;
	var getInternalPromiseState = InternalStateModule$8.getterFor(PROMISE);
	var setInternalState$8 = InternalStateModule$8.set;
	var NativePromisePrototype$1 = NativePromiseConstructor$4 && NativePromiseConstructor$4.prototype;
	var PromiseConstructor = NativePromiseConstructor$4;
	var PromisePrototype = NativePromisePrototype$1;
	var TypeError$2 = global$o.TypeError;
	var document$2 = global$o.document;
	var process$1 = global$o.process;
	var newPromiseCapability$1 = newPromiseCapabilityModule$6.f;
	var newGenericPromiseCapability = newPromiseCapability$1;

	var DISPATCH_EVENT = !!(document$2 && document$2.createEvent && global$o.dispatchEvent);
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
	  return isObject$m(it) && isCallable$k(then = it.then) ? then : false;
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
	        reject(new TypeError$2('Promise-chain cycle'));
	      } else if (then = isThenable(result)) {
	        call$g(then, result, resolve, reject);
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
	    event = document$2.createEvent('Event');
	    event.promise = promise;
	    event.reason = reason;
	    event.initEvent(name, false, true);
	    global$o.dispatchEvent(event);
	  } else event = { promise: promise, reason: reason };
	  if (!NATIVE_PROMISE_REJECTION_EVENT && (handler = global$o['on' + name])) handler(event);
	  else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
	};

	var onUnhandled = function (state) {
	  call$g(task, global$o, function () {
	    var promise = state.facade;
	    var value = state.value;
	    var IS_UNHANDLED = isUnhandled(state);
	    var result;
	    if (IS_UNHANDLED) {
	      result = perform$4(function () {
	        if (IS_NODE) {
	          process$1.emit('unhandledRejection', value, promise);
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
	  call$g(task, global$o, function () {
	    var promise = state.facade;
	    if (IS_NODE) {
	      process$1.emit('rejectionHandled', promise);
	    } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
	  });
	};

	var bind$9 = function (fn, state, unwrap) {
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
	    if (state.facade === value) throw new TypeError$2("Promise can't be resolved itself");
	    var then = isThenable(value);
	    if (then) {
	      microtask(function () {
	        var wrapper = { done: false };
	        try {
	          call$g(then, value,
	            bind$9(internalResolve, wrapper, state),
	            bind$9(internalReject, wrapper, state)
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
	    anInstance$6(this, PromisePrototype);
	    aCallable$a(executor);
	    call$g(Internal, this);
	    var state = getInternalPromiseState(this);
	    try {
	      executor(bind$9(internalResolve, state), bind$9(internalReject, state));
	    } catch (error) {
	      internalReject(state, error);
	    }
	  };

	  PromisePrototype = PromiseConstructor.prototype;

	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  Internal = function Promise(executor) {
	    setInternalState$8(this, {
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
	  Internal.prototype = defineBuiltIn$5(PromisePrototype, 'then', function then(onFulfilled, onRejected) {
	    var state = getInternalPromiseState(this);
	    var reaction = newPromiseCapability$1(speciesConstructor$1(this, PromiseConstructor));
	    state.parent = true;
	    reaction.ok = isCallable$k(onFulfilled) ? onFulfilled : true;
	    reaction.fail = isCallable$k(onRejected) && onRejected;
	    reaction.domain = IS_NODE ? process$1.domain : undefined;
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
	    this.resolve = bind$9(internalResolve, state);
	    this.reject = bind$9(internalReject, state);
	  };

	  newPromiseCapabilityModule$6.f = newPromiseCapability$1 = function (C) {
	    return C === PromiseConstructor || C === PromiseWrapper
	      ? new OwnPromiseCapability(C)
	      : newGenericPromiseCapability(C);
	  };
	}

	$$J({ global: true, constructor: true, wrap: true, forced: FORCED_PROMISE_CONSTRUCTOR$4 }, {
	  Promise: PromiseConstructor
	});

	setToStringTag$6(PromiseConstructor, PROMISE, false, true);
	setSpecies$3(PROMISE);

	var wellKnownSymbol$k = wellKnownSymbol$z;

	var ITERATOR$7 = wellKnownSymbol$k('iterator');
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
	  iteratorWithReturn[ITERATOR$7] = function () {
	    return this;
	  };
	  // eslint-disable-next-line es/no-array-from, no-throw-literal -- required for testing
	  Array.from(iteratorWithReturn, function () { throw 2; });
	} catch (error) { /* empty */ }

	var checkCorrectnessOfIteration$2 = function (exec, SKIP_CLOSING) {
	  try {
	    if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
	  } catch (error) { return false; } // workaround of old WebKit + `eval` bug
	  var ITERATION_SUPPORT = false;
	  try {
	    var object = {};
	    object[ITERATOR$7] = function () {
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

	var $$I = _export$1;
	var call$f = functionCall$1;
	var aCallable$9 = aCallable$g;
	var newPromiseCapabilityModule$5 = newPromiseCapability$2;
	var perform$3 = perform$5;
	var iterate$a = iterate$c;
	var PROMISE_STATICS_INCORRECT_ITERATION$3 = promiseStaticsIncorrectIteration;

	// `Promise.all` method
	// https://tc39.es/ecma262/#sec-promise.all
	$$I({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$3 }, {
	  all: function all(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$5.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform$3(function () {
	      var $promiseResolve = aCallable$9(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate$a(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        remaining++;
	        call$f($promiseResolve, C, promise).then(function (value) {
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

	var $$H = _export$1;
	var FORCED_PROMISE_CONSTRUCTOR$2 = promiseConstructorDetection.CONSTRUCTOR;
	var NativePromiseConstructor$2 = promiseNativeConstructor;

	NativePromiseConstructor$2 && NativePromiseConstructor$2.prototype;

	// `Promise.prototype.catch` method
	// https://tc39.es/ecma262/#sec-promise.prototype.catch
	$$H({ target: 'Promise', proto: true, forced: FORCED_PROMISE_CONSTRUCTOR$2, real: true }, {
	  'catch': function (onRejected) {
	    return this.then(undefined, onRejected);
	  }
	});

	var $$G = _export$1;
	var call$e = functionCall$1;
	var aCallable$8 = aCallable$g;
	var newPromiseCapabilityModule$4 = newPromiseCapability$2;
	var perform$2 = perform$5;
	var iterate$9 = iterate$c;
	var PROMISE_STATICS_INCORRECT_ITERATION$2 = promiseStaticsIncorrectIteration;

	// `Promise.race` method
	// https://tc39.es/ecma262/#sec-promise.race
	$$G({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$2 }, {
	  race: function race(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$4.f(C);
	    var reject = capability.reject;
	    var result = perform$2(function () {
	      var $promiseResolve = aCallable$8(C.resolve);
	      iterate$9(iterable, function (promise) {
	        call$e($promiseResolve, C, promise).then(capability.resolve, reject);
	      });
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	var $$F = _export$1;
	var newPromiseCapabilityModule$3 = newPromiseCapability$2;
	var FORCED_PROMISE_CONSTRUCTOR$1 = promiseConstructorDetection.CONSTRUCTOR;

	// `Promise.reject` method
	// https://tc39.es/ecma262/#sec-promise.reject
	$$F({ target: 'Promise', stat: true, forced: FORCED_PROMISE_CONSTRUCTOR$1 }, {
	  reject: function reject(r) {
	    var capability = newPromiseCapabilityModule$3.f(this);
	    var capabilityReject = capability.reject;
	    capabilityReject(r);
	    return capability.promise;
	  }
	});

	var anObject$9 = anObject$j;
	var isObject$l = isObject$w;
	var newPromiseCapability = newPromiseCapability$2;

	var promiseResolve$2 = function (C, x) {
	  anObject$9(C);
	  if (isObject$l(x) && x.constructor === C) return x;
	  var promiseCapability = newPromiseCapability.f(C);
	  var resolve = promiseCapability.resolve;
	  resolve(x);
	  return promiseCapability.promise;
	};

	var $$E = _export$1;
	var getBuiltIn$9 = getBuiltIn$f;
	var IS_PURE$1 = isPure;
	var NativePromiseConstructor$1 = promiseNativeConstructor;
	var FORCED_PROMISE_CONSTRUCTOR = promiseConstructorDetection.CONSTRUCTOR;
	var promiseResolve$1 = promiseResolve$2;

	var PromiseConstructorWrapper = getBuiltIn$9('Promise');
	var CHECK_WRAPPER = !FORCED_PROMISE_CONSTRUCTOR;

	// `Promise.resolve` method
	// https://tc39.es/ecma262/#sec-promise.resolve
	$$E({ target: 'Promise', stat: true, forced: IS_PURE$1 }, {
	  resolve: function resolve(x) {
	    return promiseResolve$1(CHECK_WRAPPER && this === PromiseConstructorWrapper ? NativePromiseConstructor$1 : this, x);
	  }
	});

	var $$D = _export$1;
	var call$d = functionCall$1;
	var aCallable$7 = aCallable$g;
	var newPromiseCapabilityModule$2 = newPromiseCapability$2;
	var perform$1 = perform$5;
	var iterate$8 = iterate$c;
	var PROMISE_STATICS_INCORRECT_ITERATION$1 = promiseStaticsIncorrectIteration;

	// `Promise.allSettled` method
	// https://tc39.es/ecma262/#sec-promise.allsettled
	$$D({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$1 }, {
	  allSettled: function allSettled(iterable) {
	    var C = this;
	    var capability = newPromiseCapabilityModule$2.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform$1(function () {
	      var promiseResolve = aCallable$7(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate$8(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        remaining++;
	        call$d(promiseResolve, C, promise).then(function (value) {
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

	var $$C = _export$1;
	var call$c = functionCall$1;
	var aCallable$6 = aCallable$g;
	var getBuiltIn$8 = getBuiltIn$f;
	var newPromiseCapabilityModule$1 = newPromiseCapability$2;
	var perform = perform$5;
	var iterate$7 = iterate$c;
	var PROMISE_STATICS_INCORRECT_ITERATION = promiseStaticsIncorrectIteration;

	var PROMISE_ANY_ERROR = 'No one promise resolved';

	// `Promise.any` method
	// https://tc39.es/ecma262/#sec-promise.any
	$$C({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
	  any: function any(iterable) {
	    var C = this;
	    var AggregateError = getBuiltIn$8('AggregateError');
	    var capability = newPromiseCapabilityModule$1.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform(function () {
	      var promiseResolve = aCallable$6(C.resolve);
	      var errors = [];
	      var counter = 0;
	      var remaining = 1;
	      var alreadyResolved = false;
	      iterate$7(iterable, function (promise) {
	        var index = counter++;
	        var alreadyRejected = false;
	        remaining++;
	        call$c(promiseResolve, C, promise).then(function (value) {
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

	var $$B = _export$1;
	var newPromiseCapabilityModule = newPromiseCapability$2;

	// `Promise.withResolvers` method
	// https://github.com/tc39/proposal-promise-with-resolvers
	$$B({ target: 'Promise', stat: true }, {
	  withResolvers: function withResolvers() {
	    var promiseCapability = newPromiseCapabilityModule.f(this);
	    return {
	      promise: promiseCapability.promise,
	      resolve: promiseCapability.resolve,
	      reject: promiseCapability.reject
	    };
	  }
	});

	var $$A = _export$1;
	var NativePromiseConstructor = promiseNativeConstructor;
	var fails$x = fails$L;
	var getBuiltIn$7 = getBuiltIn$f;
	var isCallable$j = isCallable$A;
	var speciesConstructor = speciesConstructor$2;
	var promiseResolve = promiseResolve$2;

	var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;

	// Safari bug https://bugs.webkit.org/show_bug.cgi?id=200829
	var NON_GENERIC = !!NativePromiseConstructor && fails$x(function () {
	  // eslint-disable-next-line unicorn/no-thenable -- required for testing
	  NativePromisePrototype['finally'].call({ then: function () { /* empty */ } }, function () { /* empty */ });
	});

	// `Promise.prototype.finally` method
	// https://tc39.es/ecma262/#sec-promise.prototype.finally
	$$A({ target: 'Promise', proto: true, real: true, forced: NON_GENERIC }, {
	  'finally': function (onFinally) {
	    var C = speciesConstructor(this, getBuiltIn$7('Promise'));
	    var isFunction = isCallable$j(onFinally);
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

	var uncurryThis$x = functionUncurryThis$1;
	var toIntegerOrInfinity$6 = toIntegerOrInfinity$9;
	var toString$h = toString$l;
	var requireObjectCoercible$a = requireObjectCoercible$e;

	var charAt$5 = uncurryThis$x(''.charAt);
	var charCodeAt$2 = uncurryThis$x(''.charCodeAt);
	var stringSlice$3 = uncurryThis$x(''.slice);

	var createMethod$8 = function (CONVERT_TO_STRING) {
	  return function ($this, pos) {
	    var S = toString$h(requireObjectCoercible$a($this));
	    var position = toIntegerOrInfinity$6(pos);
	    var size = S.length;
	    var first, second;
	    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
	    first = charCodeAt$2(S, position);
	    return first < 0xD800 || first > 0xDBFF || position + 1 === size
	      || (second = charCodeAt$2(S, position + 1)) < 0xDC00 || second > 0xDFFF
	        ? CONVERT_TO_STRING
	          ? charAt$5(S, position)
	          : first
	        : CONVERT_TO_STRING
	          ? stringSlice$3(S, position, position + 2)
	          : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
	  };
	};

	var stringMultibyte$1 = {
	  // `String.prototype.codePointAt` method
	  // https://tc39.es/ecma262/#sec-string.prototype.codepointat
	  codeAt: createMethod$8(false),
	  // `String.prototype.at` method
	  // https://github.com/mathiasbynens/String.prototype.at
	  charAt: createMethod$8(true)
	};

	var charAt$4 = stringMultibyte$1.charAt;
	var toString$g = toString$l;
	var InternalStateModule$7 = internalState$1;
	var defineIterator$4 = iteratorDefine$1;
	var createIterResultObject$5 = createIterResultObject$7;

	var STRING_ITERATOR$1 = 'String Iterator';
	var setInternalState$7 = InternalStateModule$7.set;
	var getInternalState$2 = InternalStateModule$7.getterFor(STRING_ITERATOR$1);

	// `String.prototype[@@iterator]` method
	// https://tc39.es/ecma262/#sec-string.prototype-@@iterator
	defineIterator$4(String, 'String', function (iterated) {
	  setInternalState$7(this, {
	    type: STRING_ITERATOR$1,
	    string: toString$g(iterated),
	    index: 0
	  });
	// `%StringIteratorPrototype%.next` method
	// https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
	}, function next() {
	  var state = getInternalState$2(this);
	  var string = state.string;
	  var index = state.index;
	  var point;
	  if (index >= string.length) return createIterResultObject$5(undefined, true);
	  point = charAt$4(string, index);
	  state.index += point.length;
	  return createIterResultObject$5(point, false);
	});

	var path$i = path$m;

	var promise$3 = path$i.Promise;

	// iterable DOM collections
	// flag - `iterable` interface - 'entries', 'keys', 'values', 'forEach' methods
	var domIterables$1 = {
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

	var DOMIterables$2 = domIterables$1;
	var global$n = global$G;
	var setToStringTag$5 = setToStringTag$9;
	var Iterators$6 = iterators$1;

	for (var COLLECTION_NAME$1 in DOMIterables$2) {
	  setToStringTag$5(global$n[COLLECTION_NAME$1], COLLECTION_NAME$1);
	  Iterators$6[COLLECTION_NAME$1] = Iterators$6.Array;
	}

	var parent$x = promise$3;


	var promise$2 = parent$x;

	var promise$1 = promise$2;

	var _Promise = /*@__PURE__*/getDefaultExportFromCjs(promise$1);

	// a string of all valid unicode whitespaces
	var whitespaces$7 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
	  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

	var uncurryThis$w = functionUncurryThis$1;
	var requireObjectCoercible$9 = requireObjectCoercible$e;
	var toString$f = toString$l;
	var whitespaces$6 = whitespaces$7;

	var replace$2 = uncurryThis$w(''.replace);
	var ltrim$1 = RegExp('^[' + whitespaces$6 + ']+');
	var rtrim$1 = RegExp('(^|[^' + whitespaces$6 + '])[' + whitespaces$6 + ']+$');

	// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
	var createMethod$7 = function (TYPE) {
	  return function ($this) {
	    var string = toString$f(requireObjectCoercible$9($this));
	    if (TYPE & 1) string = replace$2(string, ltrim$1, '');
	    if (TYPE & 2) string = replace$2(string, rtrim$1, '$1');
	    return string;
	  };
	};

	var stringTrim$1 = {
	  // `String.prototype.{ trimLeft, trimStart }` methods
	  // https://tc39.es/ecma262/#sec-string.prototype.trimstart
	  start: createMethod$7(1),
	  // `String.prototype.{ trimRight, trimEnd }` methods
	  // https://tc39.es/ecma262/#sec-string.prototype.trimend
	  end: createMethod$7(2),
	  // `String.prototype.trim` method
	  // https://tc39.es/ecma262/#sec-string.prototype.trim
	  trim: createMethod$7(3)
	};

	var global$m = global$G;
	var fails$w = fails$L;
	var uncurryThis$v = functionUncurryThis$1;
	var toString$e = toString$l;
	var trim$6 = stringTrim$1.trim;
	var whitespaces$5 = whitespaces$7;

	var charAt$3 = uncurryThis$v(''.charAt);
	var $parseFloat$1 = global$m.parseFloat;
	var Symbol$4 = global$m.Symbol;
	var ITERATOR$6 = Symbol$4 && Symbol$4.iterator;
	var FORCED$5 = 1 / $parseFloat$1(whitespaces$5 + '-0') !== -Infinity
	  // MS Edge 18- broken with boxed symbols
	  || (ITERATOR$6 && !fails$w(function () { $parseFloat$1(Object(ITERATOR$6)); }));

	// `parseFloat` method
	// https://tc39.es/ecma262/#sec-parsefloat-string
	var numberParseFloat = FORCED$5 ? function parseFloat(string) {
	  var trimmedString = trim$6(toString$e(string));
	  var result = $parseFloat$1(trimmedString);
	  return result === 0 && charAt$3(trimmedString, 0) === '-' ? -0 : result;
	} : $parseFloat$1;

	var $$z = _export$1;
	var $parseFloat = numberParseFloat;

	// `parseFloat` method
	// https://tc39.es/ecma262/#sec-parsefloat-string
	$$z({ global: true, forced: parseFloat !== $parseFloat }, {
	  parseFloat: $parseFloat
	});

	var path$h = path$m;

	var _parseFloat$3 = path$h.parseFloat;

	var parent$w = _parseFloat$3;

	var _parseFloat$2 = parent$w;

	var _parseFloat = _parseFloat$2;

	var _parseFloat$1 = /*@__PURE__*/getDefaultExportFromCjs(_parseFloat);

	var global$l = global$G;
	var fails$v = fails$L;
	var uncurryThis$u = functionUncurryThis$1;
	var toString$d = toString$l;
	var trim$5 = stringTrim$1.trim;
	var whitespaces$4 = whitespaces$7;

	var $parseInt$3 = global$l.parseInt;
	var Symbol$3 = global$l.Symbol;
	var ITERATOR$5 = Symbol$3 && Symbol$3.iterator;
	var hex$1 = /^[+-]?0x/i;
	var exec$3 = uncurryThis$u(hex$1.exec);
	var FORCED$4 = $parseInt$3(whitespaces$4 + '08') !== 8 || $parseInt$3(whitespaces$4 + '0x16') !== 22
	  // MS Edge 18- broken with boxed symbols
	  || (ITERATOR$5 && !fails$v(function () { $parseInt$3(Object(ITERATOR$5)); }));

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	var numberParseInt$1 = FORCED$4 ? function parseInt(string, radix) {
	  var S = trim$5(toString$d(string));
	  return $parseInt$3(S, (radix >>> 0) || (exec$3(hex$1, S) ? 16 : 10));
	} : $parseInt$3;

	var $$y = _export$1;
	var $parseInt$2 = numberParseInt$1;

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	$$y({ global: true, forced: parseInt !== $parseInt$2 }, {
	  parseInt: $parseInt$2
	});

	var path$g = path$m;

	var _parseInt$7 = path$g.parseInt;

	var parent$v = _parseInt$7;

	var _parseInt$6 = parent$v;

	var _parseInt$4 = _parseInt$6;

	var _parseInt$5 = /*@__PURE__*/getDefaultExportFromCjs(_parseInt$4);

	var classof$f = classofRaw$5;

	// `IsArray` abstract operation
	// https://tc39.es/ecma262/#sec-isarray
	// eslint-disable-next-line es/no-array-isarray -- safe
	var isArray$9 = Array.isArray || function isArray(argument) {
	  return classof$f(argument) === 'Array';
	};

	var toPropertyKey$4 = toPropertyKey$7;
	var definePropertyModule$3 = objectDefineProperty$1;
	var createPropertyDescriptor$5 = createPropertyDescriptor$b;

	var createProperty$7 = function (object, key, value) {
	  var propertyKey = toPropertyKey$4(key);
	  if (propertyKey in object) definePropertyModule$3.f(object, propertyKey, createPropertyDescriptor$5(0, value));
	  else object[propertyKey] = value;
	};

	var fails$u = fails$L;
	var wellKnownSymbol$j = wellKnownSymbol$z;
	var V8_VERSION$3 = engineV8Version$1;

	var SPECIES$6 = wellKnownSymbol$j('species');

	var arrayMethodHasSpeciesSupport$8 = function (METHOD_NAME) {
	  // We can't use this feature detection in V8 since it causes
	  // deoptimization and serious performance degradation
	  // https://github.com/zloirock/core-js/issues/677
	  return V8_VERSION$3 >= 51 || !fails$u(function () {
	    var array = [];
	    var constructor = array.constructor = {};
	    constructor[SPECIES$6] = function () {
	      return { foo: 1 };
	    };
	    return array[METHOD_NAME](Boolean).foo !== 1;
	  });
	};

	var $$x = _export$1;
	var isArray$8 = isArray$9;
	var isConstructor$5 = isConstructor$7;
	var isObject$k = isObject$w;
	var toAbsoluteIndex$6 = toAbsoluteIndex$8;
	var lengthOfArrayLike$c = lengthOfArrayLike$f;
	var toIndexedObject$a = toIndexedObject$g;
	var createProperty$6 = createProperty$7;
	var wellKnownSymbol$i = wellKnownSymbol$z;
	var arrayMethodHasSpeciesSupport$7 = arrayMethodHasSpeciesSupport$8;
	var nativeSlice$1 = arraySlice$7;

	var HAS_SPECIES_SUPPORT$5 = arrayMethodHasSpeciesSupport$7('slice');

	var SPECIES$5 = wellKnownSymbol$i('species');
	var $Array$5 = Array;
	var max$4 = Math.max;

	// `Array.prototype.slice` method
	// https://tc39.es/ecma262/#sec-array.prototype.slice
	// fallback for not array-like ES3 strings and DOM objects
	$$x({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$5 }, {
	  slice: function slice(start, end) {
	    var O = toIndexedObject$a(this);
	    var length = lengthOfArrayLike$c(O);
	    var k = toAbsoluteIndex$6(start, length);
	    var fin = toAbsoluteIndex$6(end === undefined ? length : end, length);
	    // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
	    var Constructor, result, n;
	    if (isArray$8(O)) {
	      Constructor = O.constructor;
	      // cross-realm fallback
	      if (isConstructor$5(Constructor) && (Constructor === $Array$5 || isArray$8(Constructor.prototype))) {
	        Constructor = undefined;
	      } else if (isObject$k(Constructor)) {
	        Constructor = Constructor[SPECIES$5];
	        if (Constructor === null) Constructor = undefined;
	      }
	      if (Constructor === $Array$5 || Constructor === undefined) {
	        return nativeSlice$1(O, k, fin);
	      }
	    }
	    result = new (Constructor === undefined ? $Array$5 : Constructor)(max$4(fin - k, 0));
	    for (n = 0; k < fin; k++, n++) if (k in O) createProperty$6(result, n, O[k]);
	    result.length = n;
	    return result;
	  }
	});

	var getBuiltInPrototypeMethod$d = getBuiltInPrototypeMethod$g;

	var slice$7 = getBuiltInPrototypeMethod$d('Array', 'slice');

	var isPrototypeOf$j = objectIsPrototypeOf$1;
	var method$f = slice$7;

	var ArrayPrototype$e = Array.prototype;

	var slice$6 = function (it) {
	  var own = it.slice;
	  return it === ArrayPrototype$e || (isPrototypeOf$j(ArrayPrototype$e, it) && own === ArrayPrototype$e.slice) ? method$f : own;
	};

	var parent$u = slice$6;

	var slice$5 = parent$u;

	var slice$4 = slice$5;

	var _sliceInstanceProperty$1 = /*@__PURE__*/getDefaultExportFromCjs(slice$4);

	/* global Bun -- Bun case */
	var engineIsBun = typeof Bun == 'function' && Bun && typeof Bun.version == 'string';

	var global$k = global$G;
	var apply$4 = functionApply$1;
	var isCallable$i = isCallable$A;
	var ENGINE_IS_BUN = engineIsBun;
	var USER_AGENT = engineUserAgent$1;
	var arraySlice$5 = arraySlice$7;
	var validateArgumentsLength = validateArgumentsLength$2;

	var Function$1 = global$k.Function;
	// dirty IE9- and Bun 0.3.0- checks
	var WRAP = /MSIE .\./.test(USER_AGENT) || ENGINE_IS_BUN && (function () {
	  var version = global$k.Bun.version.split('.');
	  return version.length < 3 || version[0] === '0' && (version[1] < 3 || version[1] === '3' && version[2] === '0');
	})();

	// IE9- / Bun 0.3.0- setTimeout / setInterval / setImmediate additional parameters fix
	// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#timers
	// https://github.com/oven-sh/bun/issues/1633
	var schedulersFix$2 = function (scheduler, hasTimeArg) {
	  var firstParamIndex = hasTimeArg ? 2 : 1;
	  return WRAP ? function (handler, timeout /* , ...arguments */) {
	    var boundArgs = validateArgumentsLength(arguments.length, 1) > firstParamIndex;
	    var fn = isCallable$i(handler) ? handler : Function$1(handler);
	    var params = boundArgs ? arraySlice$5(arguments, firstParamIndex) : [];
	    var callback = boundArgs ? function () {
	      apply$4(fn, this, params);
	    } : fn;
	    return hasTimeArg ? scheduler(callback, timeout) : scheduler(callback);
	  } : scheduler;
	};

	var $$w = _export$1;
	var global$j = global$G;
	var schedulersFix$1 = schedulersFix$2;

	var setInterval = schedulersFix$1(global$j.setInterval, true);

	// Bun / IE9- setInterval additional parameters fix
	// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-setinterval
	$$w({ global: true, bind: true, forced: global$j.setInterval !== setInterval }, {
	  setInterval: setInterval
	});

	var $$v = _export$1;
	var global$i = global$G;
	var schedulersFix = schedulersFix$2;

	var setTimeout$3 = schedulersFix(global$i.setTimeout, true);

	// Bun / IE9- setTimeout additional parameters fix
	// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-settimeout
	$$v({ global: true, bind: true, forced: global$i.setTimeout !== setTimeout$3 }, {
	  setTimeout: setTimeout$3
	});

	var path$f = path$m;

	var setTimeout$2 = path$f.setTimeout;

	var setTimeout$1 = setTimeout$2;

	var _setTimeout = /*@__PURE__*/getDefaultExportFromCjs(setTimeout$1);

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
	      var scrollTop = $$O(window).scrollTop();
	      if (elemTop < $$O(window).scrollTop() || elemTop > scrollTop + window.innerHeight) {
	        $$O('html,body').animate({
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
	    amount = _parseFloat$1(amount);
	    result = result.replace('{sign}', amount < 0 ? '-' : '');
	    result = result.replace('{price}', this._formatNumber(Math.abs(amount), this.#w.format_price.decimals, this.#w.format_price.decimal_separator, this.#w.format_price.thousands_separator));
	    return result;
	  }
	  _formatNumber(n, c, d, t) {
	    var _context;
	    n = Math.abs(Number(n) || 0).toFixed(c);
	    c = isNaN(c = Math.abs(c)) ? 2 : c;
	    d = d === undefined ? '.' : d;
	    t = t === undefined ? ',' : t;
	    let s = n < 0 ? '-' : '',
	      i = String(_parseInt$5(n)),
	      j = i.length > 3 ? i.length % 3 : 0;
	    return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + _sliceInstanceProperty$1(_context = Math.abs(n - i).toFixed(c)).call(_context, 2) : '');
	  }
	}
	function ajax(options, resolve, reject) {
	  options.data.csrf_token = BooklyL10n.csrf_token;
	  return $$O.ajax(jQuery.extend({
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

	var toIntegerOrInfinity$5 = toIntegerOrInfinity$9;
	var toString$c = toString$l;
	var requireObjectCoercible$8 = requireObjectCoercible$e;

	var $RangeError = RangeError;

	// `String.prototype.repeat` method implementation
	// https://tc39.es/ecma262/#sec-string.prototype.repeat
	var stringRepeat = function repeat(count) {
	  var str = toString$c(requireObjectCoercible$8(this));
	  var result = '';
	  var n = toIntegerOrInfinity$5(count);
	  if (n < 0 || n === Infinity) throw new $RangeError('Wrong number of repetitions');
	  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
	  return result;
	};

	// https://github.com/tc39/proposal-string-pad-start-end
	var uncurryThis$t = functionUncurryThis$1;
	var toLength$2 = toLength$4;
	var toString$b = toString$l;
	var $repeat = stringRepeat;
	var requireObjectCoercible$7 = requireObjectCoercible$e;

	var repeat$5 = uncurryThis$t($repeat);
	var stringSlice$2 = uncurryThis$t(''.slice);
	var ceil$1 = Math.ceil;

	// `String.prototype.{ padStart, padEnd }` methods implementation
	var createMethod$6 = function (IS_END) {
	  return function ($this, maxLength, fillString) {
	    var S = toString$b(requireObjectCoercible$7($this));
	    var intMaxLength = toLength$2(maxLength);
	    var stringLength = S.length;
	    var fillStr = fillString === undefined ? ' ' : toString$b(fillString);
	    var fillLen, stringFiller;
	    if (intMaxLength <= stringLength || fillStr === '') return S;
	    fillLen = intMaxLength - stringLength;
	    stringFiller = repeat$5(fillStr, ceil$1(fillLen / fillStr.length));
	    if (stringFiller.length > fillLen) stringFiller = stringSlice$2(stringFiller, 0, fillLen);
	    return IS_END ? S + stringFiller : stringFiller + S;
	  };
	};

	var stringPad = {
	  // `String.prototype.padStart` method
	  // https://tc39.es/ecma262/#sec-string.prototype.padstart
	  start: createMethod$6(false),
	  // `String.prototype.padEnd` method
	  // https://tc39.es/ecma262/#sec-string.prototype.padend
	  end: createMethod$6(true)
	};

	// https://github.com/zloirock/core-js/issues/280
	var userAgent$3 = engineUserAgent$1;

	var stringPadWebkitBug = /Version\/10(?:\.\d+){1,2}(?: [\w./]+)?(?: Mobile\/\w+)? Safari\//.test(userAgent$3);

	var $$u = _export$1;
	var $padStart = stringPad.start;
	var WEBKIT_BUG = stringPadWebkitBug;

	// `String.prototype.padStart` method
	// https://tc39.es/ecma262/#sec-string.prototype.padstart
	$$u({ target: 'String', proto: true, forced: WEBKIT_BUG }, {
	  padStart: function padStart(maxLength /* , fillString = ' ' */) {
	    return $padStart(this, maxLength, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var getBuiltInPrototypeMethod$c = getBuiltInPrototypeMethod$g;

	var padStart$3 = getBuiltInPrototypeMethod$c('String', 'padStart');

	var isPrototypeOf$i = objectIsPrototypeOf$1;
	var method$e = padStart$3;

	var StringPrototype$3 = String.prototype;

	var padStart$2 = function (it) {
	  var own = it.padStart;
	  return typeof it == 'string' || it === StringPrototype$3
	    || (isPrototypeOf$i(StringPrototype$3, it) && own === StringPrototype$3.padStart) ? method$e : own;
	};

	var parent$t = padStart$2;

	var padStart$1 = parent$t;

	var padStart = padStart$1;

	var _padStartInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(padStart);

	var isArray$7 = isArray$9;
	var isConstructor$4 = isConstructor$7;
	var isObject$j = isObject$w;
	var wellKnownSymbol$h = wellKnownSymbol$z;

	var SPECIES$4 = wellKnownSymbol$h('species');
	var $Array$4 = Array;

	// a part of `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesConstructor$3 = function (originalArray) {
	  var C;
	  if (isArray$7(originalArray)) {
	    C = originalArray.constructor;
	    // cross-realm fallback
	    if (isConstructor$4(C) && (C === $Array$4 || isArray$7(C.prototype))) C = undefined;
	    else if (isObject$j(C)) {
	      C = C[SPECIES$4];
	      if (C === null) C = undefined;
	    }
	  } return C === undefined ? $Array$4 : C;
	};

	var arraySpeciesConstructor$2 = arraySpeciesConstructor$3;

	// `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesCreate$5 = function (originalArray, length) {
	  return new (arraySpeciesConstructor$2(originalArray))(length === 0 ? 0 : length);
	};

	var bind$8 = functionBindContext$1;
	var uncurryThis$s = functionUncurryThis$1;
	var IndexedObject$2 = indexedObject$1;
	var toObject$a = toObject$d;
	var lengthOfArrayLike$b = lengthOfArrayLike$f;
	var arraySpeciesCreate$4 = arraySpeciesCreate$5;

	var push$6 = uncurryThis$s([].push);

	// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
	var createMethod$5 = function (TYPE) {
	  var IS_MAP = TYPE === 1;
	  var IS_FILTER = TYPE === 2;
	  var IS_SOME = TYPE === 3;
	  var IS_EVERY = TYPE === 4;
	  var IS_FIND_INDEX = TYPE === 6;
	  var IS_FILTER_REJECT = TYPE === 7;
	  var NO_HOLES = TYPE === 5 || IS_FIND_INDEX;
	  return function ($this, callbackfn, that, specificCreate) {
	    var O = toObject$a($this);
	    var self = IndexedObject$2(O);
	    var length = lengthOfArrayLike$b(self);
	    var boundFunction = bind$8(callbackfn, that);
	    var index = 0;
	    var create = specificCreate || arraySpeciesCreate$4;
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
	          case 2: push$6(target, value);      // filter
	        } else switch (TYPE) {
	          case 4: return false;             // every
	          case 7: push$6(target, value);      // filterReject
	        }
	      }
	    }
	    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
	  };
	};

	var arrayIteration$1 = {
	  // `Array.prototype.forEach` method
	  // https://tc39.es/ecma262/#sec-array.prototype.foreach
	  forEach: createMethod$5(0),
	  // `Array.prototype.map` method
	  // https://tc39.es/ecma262/#sec-array.prototype.map
	  map: createMethod$5(1),
	  // `Array.prototype.filter` method
	  // https://tc39.es/ecma262/#sec-array.prototype.filter
	  filter: createMethod$5(2),
	  // `Array.prototype.some` method
	  // https://tc39.es/ecma262/#sec-array.prototype.some
	  some: createMethod$5(3),
	  // `Array.prototype.every` method
	  // https://tc39.es/ecma262/#sec-array.prototype.every
	  every: createMethod$5(4),
	  // `Array.prototype.find` method
	  // https://tc39.es/ecma262/#sec-array.prototype.find
	  find: createMethod$5(5),
	  // `Array.prototype.findIndex` method
	  // https://tc39.es/ecma262/#sec-array.prototype.findIndex
	  findIndex: createMethod$5(6),
	  // `Array.prototype.filterReject` method
	  // https://github.com/tc39/proposal-array-filtering
	  filterReject: createMethod$5(7)
	};

	var $$t = _export$1;
	var $find = arrayIteration$1.find;

	var FIND = 'find';
	var SKIPS_HOLES = true;

	// Shouldn't skip holes
	// eslint-disable-next-line es/no-array-prototype-find -- testing
	if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

	// `Array.prototype.find` method
	// https://tc39.es/ecma262/#sec-array.prototype.find
	$$t({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
	  find: function find(callbackfn /* , that = undefined */) {
	    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var getBuiltInPrototypeMethod$b = getBuiltInPrototypeMethod$g;

	var find$4 = getBuiltInPrototypeMethod$b('Array', 'find');

	var isPrototypeOf$h = objectIsPrototypeOf$1;
	var method$d = find$4;

	var ArrayPrototype$d = Array.prototype;

	var find$3 = function (it) {
	  var own = it.find;
	  return it === ArrayPrototype$d || (isPrototypeOf$h(ArrayPrototype$d, it) && own === ArrayPrototype$d.find) ? method$d : own;
	};

	var parent$s = find$3;

	var find$2 = parent$s;

	var find$1 = find$2;

	var _findInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(find$1);

	var uncurryThis$r = functionUncurryThis$1;
	var isArray$6 = isArray$9;
	var isCallable$h = isCallable$A;
	var classof$e = classofRaw$5;
	var toString$a = toString$l;

	var push$5 = uncurryThis$r([].push);

	var getJsonReplacerFunction = function (replacer) {
	  if (isCallable$h(replacer)) return replacer;
	  if (!isArray$6(replacer)) return;
	  var rawLength = replacer.length;
	  var keys = [];
	  for (var i = 0; i < rawLength; i++) {
	    var element = replacer[i];
	    if (typeof element == 'string') push$5(keys, element);
	    else if (typeof element == 'number' || classof$e(element) === 'Number' || classof$e(element) === 'String') push$5(keys, toString$a(element));
	  }
	  var keysLength = keys.length;
	  var root = true;
	  return function (key, value) {
	    if (root) {
	      root = false;
	      return value;
	    }
	    if (isArray$6(this)) return value;
	    for (var j = 0; j < keysLength; j++) if (keys[j] === key) return value;
	  };
	};

	var $$s = _export$1;
	var getBuiltIn$6 = getBuiltIn$f;
	var apply$3 = functionApply$1;
	var call$b = functionCall$1;
	var uncurryThis$q = functionUncurryThis$1;
	var fails$t = fails$L;
	var isCallable$g = isCallable$A;
	var isSymbol$3 = isSymbol$6;
	var arraySlice$4 = arraySlice$7;
	var getReplacerFunction = getJsonReplacerFunction;
	var NATIVE_SYMBOL$2 = symbolConstructorDetection$1;

	var $String$4 = String;
	var $stringify = getBuiltIn$6('JSON', 'stringify');
	var exec$2 = uncurryThis$q(/./.exec);
	var charAt$2 = uncurryThis$q(''.charAt);
	var charCodeAt$1 = uncurryThis$q(''.charCodeAt);
	var replace$1 = uncurryThis$q(''.replace);
	var numberToString = uncurryThis$q(1.0.toString);

	var tester = /[\uD800-\uDFFF]/g;
	var low = /^[\uD800-\uDBFF]$/;
	var hi = /^[\uDC00-\uDFFF]$/;

	var WRONG_SYMBOLS_CONVERSION = !NATIVE_SYMBOL$2 || fails$t(function () {
	  var symbol = getBuiltIn$6('Symbol')('stringify detection');
	  // MS Edge converts symbol values to JSON as {}
	  return $stringify([symbol]) !== '[null]'
	    // WebKit converts symbol values to JSON as null
	    || $stringify({ a: symbol }) !== '{}'
	    // V8 throws on boxed symbols
	    || $stringify(Object(symbol)) !== '{}';
	});

	// https://github.com/tc39/proposal-well-formed-stringify
	var ILL_FORMED_UNICODE = fails$t(function () {
	  return $stringify('\uDF06\uD834') !== '"\\udf06\\ud834"'
	    || $stringify('\uDEAD') !== '"\\udead"';
	});

	var stringifyWithSymbolsFix = function (it, replacer) {
	  var args = arraySlice$4(arguments);
	  var $replacer = getReplacerFunction(replacer);
	  if (!isCallable$g($replacer) && (it === undefined || isSymbol$3(it))) return; // IE8 returns string on undefined
	  args[1] = function (key, value) {
	    // some old implementations (like WebKit) could pass numbers as keys
	    if (isCallable$g($replacer)) value = call$b($replacer, this, $String$4(key), value);
	    if (!isSymbol$3(value)) return value;
	  };
	  return apply$3($stringify, null, args);
	};

	var fixIllFormed = function (match, offset, string) {
	  var prev = charAt$2(string, offset - 1);
	  var next = charAt$2(string, offset + 1);
	  if ((exec$2(low, match) && !exec$2(hi, next)) || (exec$2(hi, match) && !exec$2(low, prev))) {
	    return '\\u' + numberToString(charCodeAt$1(match, 0), 16);
	  } return match;
	};

	if ($stringify) {
	  // `JSON.stringify` method
	  // https://tc39.es/ecma262/#sec-json.stringify
	  $$s({ target: 'JSON', stat: true, arity: 3, forced: WRONG_SYMBOLS_CONVERSION || ILL_FORMED_UNICODE }, {
	    // eslint-disable-next-line no-unused-vars -- required for `.length`
	    stringify: function stringify(it, replacer, space) {
	      var args = arraySlice$4(arguments);
	      var result = apply$3(WRONG_SYMBOLS_CONVERSION ? stringifyWithSymbolsFix : $stringify, null, args);
	      return ILL_FORMED_UNICODE && typeof result == 'string' ? replace$1(result, tester, fixIllFormed) : result;
	    }
	  });
	}

	var path$e = path$m;
	var apply$2 = functionApply$1;

	// eslint-disable-next-line es/no-json -- safe
	if (!path$e.JSON) path$e.JSON = { stringify: JSON.stringify };

	// eslint-disable-next-line no-unused-vars -- required for `.length`
	var stringify$2 = function stringify(it, replacer, space) {
	  return apply$2(path$e.JSON.stringify, null, arguments);
	};

	var parent$r = stringify$2;

	var stringify$1 = parent$r;

	var stringify = stringify$1;

	var _JSON$stringify = /*@__PURE__*/getDefaultExportFromCjs(stringify);

	var $$r = _export$1;
	var repeat$4 = stringRepeat;

	// `String.prototype.repeat` method
	// https://tc39.es/ecma262/#sec-string.prototype.repeat
	$$r({ target: 'String', proto: true }, {
	  repeat: repeat$4
	});

	var getBuiltInPrototypeMethod$a = getBuiltInPrototypeMethod$g;

	var repeat$3 = getBuiltInPrototypeMethod$a('String', 'repeat');

	var isPrototypeOf$g = objectIsPrototypeOf$1;
	var method$c = repeat$3;

	var StringPrototype$2 = String.prototype;

	var repeat$2 = function (it) {
	  var own = it.repeat;
	  return typeof it == 'string' || it === StringPrototype$2
	    || (isPrototypeOf$g(StringPrototype$2, it) && own === StringPrototype$2.repeat) ? method$c : own;
	};

	var parent$q = repeat$2;

	var repeat$1 = parent$q;

	var repeat = repeat$1;

	var _repeatInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(repeat);

	var fails$s = fails$L;

	var arrayMethodIsStrict$4 = function (METHOD_NAME, argument) {
	  var method = [][METHOD_NAME];
	  return !!method && fails$s(function () {
	    // eslint-disable-next-line no-useless-call -- required for testing
	    method.call(null, argument || function () { return 1; }, 1);
	  });
	};

	var $forEach = arrayIteration$1.forEach;
	var arrayMethodIsStrict$3 = arrayMethodIsStrict$4;

	var STRICT_METHOD$2 = arrayMethodIsStrict$3('forEach');

	// `Array.prototype.forEach` method implementation
	// https://tc39.es/ecma262/#sec-array.prototype.foreach
	var arrayForEach = !STRICT_METHOD$2 ? function forEach(callbackfn /* , thisArg */) {
	  return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	// eslint-disable-next-line es/no-array-prototype-foreach -- safe
	} : [].forEach;

	var $$q = _export$1;
	var forEach$6 = arrayForEach;

	// `Array.prototype.forEach` method
	// https://tc39.es/ecma262/#sec-array.prototype.foreach
	// eslint-disable-next-line es/no-array-prototype-foreach -- safe
	$$q({ target: 'Array', proto: true, forced: [].forEach !== forEach$6 }, {
	  forEach: forEach$6
	});

	var getBuiltInPrototypeMethod$9 = getBuiltInPrototypeMethod$g;

	var forEach$5 = getBuiltInPrototypeMethod$9('Array', 'forEach');

	var parent$p = forEach$5;

	var forEach$4 = parent$p;

	var classof$d = classof$l;
	var hasOwn$b = hasOwnProperty_1$1;
	var isPrototypeOf$f = objectIsPrototypeOf$1;
	var method$b = forEach$4;


	var ArrayPrototype$c = Array.prototype;

	var DOMIterables$1 = {
	  DOMTokenList: true,
	  NodeList: true
	};

	var forEach$3 = function (it) {
	  var own = it.forEach;
	  return it === ArrayPrototype$c || (isPrototypeOf$f(ArrayPrototype$c, it) && own === ArrayPrototype$c.forEach)
	    || hasOwn$b(DOMIterables$1, classof$d(it)) ? method$b : own;
	};

	var forEach$2 = forEach$3;

	var _forEachInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(forEach$2);

	var DESCRIPTORS$e = descriptors$1;
	var isArray$5 = isArray$9;

	var $TypeError$c = TypeError;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$2 = Object.getOwnPropertyDescriptor;

	// Safari < 13 does not throw an error in this case
	var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS$e && !function () {
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
	  if (isArray$5(O) && !getOwnPropertyDescriptor$2(O, 'length').writable) {
	    throw new $TypeError$c('Cannot set read only .length');
	  } return O.length = length;
	} : function (O, length) {
	  return O.length = length;
	};

	var $TypeError$b = TypeError;
	var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; // 2 ** 53 - 1 == 9007199254740991

	var doesNotExceedSafeInteger$2 = function (it) {
	  if (it > MAX_SAFE_INTEGER) throw $TypeError$b('Maximum allowed index exceeded');
	  return it;
	};

	var tryToString$4 = tryToString$9;

	var $TypeError$a = TypeError;

	var deletePropertyOrThrow$2 = function (O, P) {
	  if (!delete O[P]) throw new $TypeError$a('Cannot delete property ' + tryToString$4(P) + ' of ' + tryToString$4(O));
	};

	var $$p = _export$1;
	var toObject$9 = toObject$d;
	var toAbsoluteIndex$5 = toAbsoluteIndex$8;
	var toIntegerOrInfinity$4 = toIntegerOrInfinity$9;
	var lengthOfArrayLike$a = lengthOfArrayLike$f;
	var setArrayLength = arraySetLength;
	var doesNotExceedSafeInteger$1 = doesNotExceedSafeInteger$2;
	var arraySpeciesCreate$3 = arraySpeciesCreate$5;
	var createProperty$5 = createProperty$7;
	var deletePropertyOrThrow$1 = deletePropertyOrThrow$2;
	var arrayMethodHasSpeciesSupport$6 = arrayMethodHasSpeciesSupport$8;

	var HAS_SPECIES_SUPPORT$4 = arrayMethodHasSpeciesSupport$6('splice');

	var max$3 = Math.max;
	var min$2 = Math.min;

	// `Array.prototype.splice` method
	// https://tc39.es/ecma262/#sec-array.prototype.splice
	// with adding support of @@species
	$$p({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$4 }, {
	  splice: function splice(start, deleteCount /* , ...items */) {
	    var O = toObject$9(this);
	    var len = lengthOfArrayLike$a(O);
	    var actualStart = toAbsoluteIndex$5(start, len);
	    var argumentsLength = arguments.length;
	    var insertCount, actualDeleteCount, A, k, from, to;
	    if (argumentsLength === 0) {
	      insertCount = actualDeleteCount = 0;
	    } else if (argumentsLength === 1) {
	      insertCount = 0;
	      actualDeleteCount = len - actualStart;
	    } else {
	      insertCount = argumentsLength - 2;
	      actualDeleteCount = min$2(max$3(toIntegerOrInfinity$4(deleteCount), 0), len - actualStart);
	    }
	    doesNotExceedSafeInteger$1(len + insertCount - actualDeleteCount);
	    A = arraySpeciesCreate$3(O, actualDeleteCount);
	    for (k = 0; k < actualDeleteCount; k++) {
	      from = actualStart + k;
	      if (from in O) createProperty$5(A, k, O[from]);
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

	var getBuiltInPrototypeMethod$8 = getBuiltInPrototypeMethod$g;

	var splice$4 = getBuiltInPrototypeMethod$8('Array', 'splice');

	var isPrototypeOf$e = objectIsPrototypeOf$1;
	var method$a = splice$4;

	var ArrayPrototype$b = Array.prototype;

	var splice$3 = function (it) {
	  var own = it.splice;
	  return it === ArrayPrototype$b || (isPrototypeOf$e(ArrayPrototype$b, it) && own === ArrayPrototype$b.splice) ? method$a : own;
	};

	var parent$o = splice$3;

	var splice$2 = parent$o;

	var splice$1 = splice$2;

	var _spliceInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(splice$1);

	var $$o = _export$1;
	var $every = arrayIteration$1.every;
	var arrayMethodIsStrict$2 = arrayMethodIsStrict$4;

	var STRICT_METHOD$1 = arrayMethodIsStrict$2('every');

	// `Array.prototype.every` method
	// https://tc39.es/ecma262/#sec-array.prototype.every
	$$o({ target: 'Array', proto: true, forced: !STRICT_METHOD$1 }, {
	  every: function every(callbackfn /* , thisArg */) {
	    return $every(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var getBuiltInPrototypeMethod$7 = getBuiltInPrototypeMethod$g;

	var every$3 = getBuiltInPrototypeMethod$7('Array', 'every');

	var isPrototypeOf$d = objectIsPrototypeOf$1;
	var method$9 = every$3;

	var ArrayPrototype$a = Array.prototype;

	var every$2 = function (it) {
	  var own = it.every;
	  return it === ArrayPrototype$a || (isPrototypeOf$d(ArrayPrototype$a, it) && own === ArrayPrototype$a.every) ? method$9 : own;
	};

	var parent$n = every$2;

	var every$1 = parent$n;

	var every = every$1;

	var _everyInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(every);

	var $$n = _export$1;
	var fails$r = fails$L;
	var isArray$4 = isArray$9;
	var isObject$i = isObject$w;
	var toObject$8 = toObject$d;
	var lengthOfArrayLike$9 = lengthOfArrayLike$f;
	var doesNotExceedSafeInteger = doesNotExceedSafeInteger$2;
	var createProperty$4 = createProperty$7;
	var arraySpeciesCreate$2 = arraySpeciesCreate$5;
	var arrayMethodHasSpeciesSupport$5 = arrayMethodHasSpeciesSupport$8;
	var wellKnownSymbol$g = wellKnownSymbol$z;
	var V8_VERSION$2 = engineV8Version$1;

	var IS_CONCAT_SPREADABLE = wellKnownSymbol$g('isConcatSpreadable');

	// We can't use this feature detection in V8 since it causes
	// deoptimization and serious performance degradation
	// https://github.com/zloirock/core-js/issues/679
	var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION$2 >= 51 || !fails$r(function () {
	  var array = [];
	  array[IS_CONCAT_SPREADABLE] = false;
	  return array.concat()[0] !== array;
	});

	var isConcatSpreadable = function (O) {
	  if (!isObject$i(O)) return false;
	  var spreadable = O[IS_CONCAT_SPREADABLE];
	  return spreadable !== undefined ? !!spreadable : isArray$4(O);
	};

	var FORCED$3 = !IS_CONCAT_SPREADABLE_SUPPORT || !arrayMethodHasSpeciesSupport$5('concat');

	// `Array.prototype.concat` method
	// https://tc39.es/ecma262/#sec-array.prototype.concat
	// with adding support of @@isConcatSpreadable and @@species
	$$n({ target: 'Array', proto: true, arity: 1, forced: FORCED$3 }, {
	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  concat: function concat(arg) {
	    var O = toObject$8(this);
	    var A = arraySpeciesCreate$2(O, 0);
	    var n = 0;
	    var i, k, length, len, E;
	    for (i = -1, length = arguments.length; i < length; i++) {
	      E = i === -1 ? O : arguments[i];
	      if (isConcatSpreadable(E)) {
	        len = lengthOfArrayLike$9(E);
	        doesNotExceedSafeInteger(n + len);
	        for (k = 0; k < len; k++, n++) if (k in E) createProperty$4(A, n, E[k]);
	      } else {
	        doesNotExceedSafeInteger(n + 1);
	        createProperty$4(A, n++, E);
	      }
	    }
	    A.length = n;
	    return A;
	  }
	});

	var getBuiltInPrototypeMethod$6 = getBuiltInPrototypeMethod$g;

	var concat$3 = getBuiltInPrototypeMethod$6('Array', 'concat');

	var isPrototypeOf$c = objectIsPrototypeOf$1;
	var method$8 = concat$3;

	var ArrayPrototype$9 = Array.prototype;

	var concat$2 = function (it) {
	  var own = it.concat;
	  return it === ArrayPrototype$9 || (isPrototypeOf$c(ArrayPrototype$9, it) && own === ArrayPrototype$9.concat) ? method$8 : own;
	};

	var parent$m = concat$2;

	var concat$1 = parent$m;

	var concat = concat$1;

	var _concatInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(concat);

	var $$m = _export$1;
	var $map = arrayIteration$1.map;
	var arrayMethodHasSpeciesSupport$4 = arrayMethodHasSpeciesSupport$8;

	var HAS_SPECIES_SUPPORT$3 = arrayMethodHasSpeciesSupport$4('map');

	// `Array.prototype.map` method
	// https://tc39.es/ecma262/#sec-array.prototype.map
	// with adding support of @@species
	$$m({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$3 }, {
	  map: function map(callbackfn /* , thisArg */) {
	    return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var getBuiltInPrototypeMethod$5 = getBuiltInPrototypeMethod$g;

	var map$6 = getBuiltInPrototypeMethod$5('Array', 'map');

	var isPrototypeOf$b = objectIsPrototypeOf$1;
	var method$7 = map$6;

	var ArrayPrototype$8 = Array.prototype;

	var map$5 = function (it) {
	  var own = it.map;
	  return it === ArrayPrototype$8 || (isPrototypeOf$b(ArrayPrototype$8, it) && own === ArrayPrototype$8.map) ? method$7 : own;
	};

	var parent$l = map$5;

	var map$4 = parent$l;

	var map$3 = map$4;

	var _mapInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(map$3);

	var $$l = _export$1;
	var $filter$1 = arrayIteration$1.filter;
	var arrayMethodHasSpeciesSupport$3 = arrayMethodHasSpeciesSupport$8;

	var HAS_SPECIES_SUPPORT$2 = arrayMethodHasSpeciesSupport$3('filter');

	// `Array.prototype.filter` method
	// https://tc39.es/ecma262/#sec-array.prototype.filter
	// with adding support of @@species
	$$l({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$2 }, {
	  filter: function filter(callbackfn /* , thisArg */) {
	    return $filter$1(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var getBuiltInPrototypeMethod$4 = getBuiltInPrototypeMethod$g;

	var filter$7 = getBuiltInPrototypeMethod$4('Array', 'filter');

	var isPrototypeOf$a = objectIsPrototypeOf$1;
	var method$6 = filter$7;

	var ArrayPrototype$7 = Array.prototype;

	var filter$6 = function (it) {
	  var own = it.filter;
	  return it === ArrayPrototype$7 || (isPrototypeOf$a(ArrayPrototype$7, it) && own === ArrayPrototype$7.filter) ? method$6 : own;
	};

	var parent$k = filter$6;

	var filter$5 = parent$k;

	var filter$4 = filter$5;

	var _filterInstanceProperty$1 = /*@__PURE__*/getDefaultExportFromCjs(filter$4);

	var $$k = _export$1;
	var toObject$7 = toObject$d;
	var nativeKeys = objectKeys$4;
	var fails$q = fails$L;

	var FAILS_ON_PRIMITIVES$2 = fails$q(function () { nativeKeys(1); });

	// `Object.keys` method
	// https://tc39.es/ecma262/#sec-object.keys
	$$k({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$2 }, {
	  keys: function keys(it) {
	    return nativeKeys(toObject$7(it));
	  }
	});

	var path$d = path$m;

	var keys$3 = path$d.Object.keys;

	var parent$j = keys$3;

	var keys$2 = parent$j;

	var keys$1 = keys$2;

	var _Object$keys = /*@__PURE__*/getDefaultExportFromCjs(keys$1);

	var PROPER_FUNCTION_NAME$1 = functionName$1.PROPER;
	var fails$p = fails$L;
	var whitespaces$3 = whitespaces$7;

	var non = '\u200B\u0085\u180E';

	// check that a method works with the correct list
	// of whitespaces and has a correct name
	var stringTrimForced = function (METHOD_NAME) {
	  return fails$p(function () {
	    return !!whitespaces$3[METHOD_NAME]()
	      || non[METHOD_NAME]() !== non
	      || (PROPER_FUNCTION_NAME$1 && whitespaces$3[METHOD_NAME].name !== METHOD_NAME);
	  });
	};

	var $$j = _export$1;
	var $trim = stringTrim$1.trim;
	var forcedStringTrimMethod = stringTrimForced;

	// `String.prototype.trim` method
	// https://tc39.es/ecma262/#sec-string.prototype.trim
	$$j({ target: 'String', proto: true, forced: forcedStringTrimMethod('trim') }, {
	  trim: function trim() {
	    return $trim(this);
	  }
	});

	var getBuiltInPrototypeMethod$3 = getBuiltInPrototypeMethod$g;

	var trim$4 = getBuiltInPrototypeMethod$3('String', 'trim');

	var isPrototypeOf$9 = objectIsPrototypeOf$1;
	var method$5 = trim$4;

	var StringPrototype$1 = String.prototype;

	var trim$3 = function (it) {
	  var own = it.trim;
	  return typeof it == 'string' || it === StringPrototype$1
	    || (isPrototypeOf$9(StringPrototype$1, it) && own === StringPrototype$1.trim) ? method$5 : own;
	};

	var parent$i = trim$3;

	var trim$2 = parent$i;

	var trim$1 = trim$2;

	var _trimInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(trim$1);

	/* eslint-disable es/no-array-prototype-indexof -- required for testing */
	var $$i = _export$1;
	var uncurryThis$p = functionUncurryThisClause$1;
	var $indexOf = arrayIncludes$1.indexOf;
	var arrayMethodIsStrict$1 = arrayMethodIsStrict$4;

	var nativeIndexOf = uncurryThis$p([].indexOf);

	var NEGATIVE_ZERO = !!nativeIndexOf && 1 / nativeIndexOf([1], 1, -0) < 0;
	var FORCED$2 = NEGATIVE_ZERO || !arrayMethodIsStrict$1('indexOf');

	// `Array.prototype.indexOf` method
	// https://tc39.es/ecma262/#sec-array.prototype.indexof
	$$i({ target: 'Array', proto: true, forced: FORCED$2 }, {
	  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
	    var fromIndex = arguments.length > 1 ? arguments[1] : undefined;
	    return NEGATIVE_ZERO
	      // convert -0 to +0
	      ? nativeIndexOf(this, searchElement, fromIndex) || 0
	      : $indexOf(this, searchElement, fromIndex);
	  }
	});

	var getBuiltInPrototypeMethod$2 = getBuiltInPrototypeMethod$g;

	var indexOf$4 = getBuiltInPrototypeMethod$2('Array', 'indexOf');

	var isPrototypeOf$8 = objectIsPrototypeOf$1;
	var method$4 = indexOf$4;

	var ArrayPrototype$6 = Array.prototype;

	var indexOf$3 = function (it) {
	  var own = it.indexOf;
	  return it === ArrayPrototype$6 || (isPrototypeOf$8(ArrayPrototype$6, it) && own === ArrayPrototype$6.indexOf) ? method$4 : own;
	};

	var parent$h = indexOf$3;

	var indexOf$2 = parent$h;

	var indexOf$1 = indexOf$2;

	var _indexOfInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(indexOf$1);

	/**
	 * Complete step.
	 */
	function stepComplete(params) {
	  let data = $$O.extend({
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
	      let $qc = $$O('.bookly-js-qr', $container),
	        url = BooklyL10n.ajaxurl + (_indexOfInstanceProperty(_context = BooklyL10n.ajaxurl).call(_context, '?') > 0 ? '&' : '?') + 'bookly_order=' + response.bookly_order + '&csrf_token=' + BooklyL10n.csrf_token;
	      new QRCode($qc.get(0), {
	        text: response.qr,
	        width: 256,
	        height: 256,
	        useSVG: true,
	        correctLevel: 1
	      });
	      scrollTo($container, params.form_id);
	      $$O('.bookly-js-start-over', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          reset_form: true,
	          new_chain: true
	        });
	      });
	      $$O('.bookly-js-download-ics', $container).on('click', function (e) {
	        let ladda = laddaStart(this);
	        window.location = url + '&action=bookly_add_to_calendar&calendar=ics';
	        _setTimeout(() => ladda.stop(), 1500);
	      });
	      $$O('.bookly-js-download-invoice', $container).on('click', function (e) {
	        let ladda = laddaStart(this);
	        window.location = url + '&action=bookly_invoices_download_invoice';
	        _setTimeout(() => ladda.stop(), 1500);
	      });
	      $$O('.bookly-js-add-to-calendar', $container).on('click', function (e) {
	        e.preventDefault();
	        let ladda = laddaStart(this);
	        window.open(url + '&action=bookly_add_to_calendar&calendar=' + $$O(this).data('calendar'), '_blank');
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
	    let $stripe_card_field = $$O('#bookly-stripe-card-field', $container);

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
	        $$O('.pay-card .bookly-js-next-step', $container).prop('disabled', true);
	        let $details = $stripe_card_field.closest('.bookly-js-details');
	        $$O('.bookly-form-group', $details).hide();
	        $$O('.bookly-js-card-error', $details).text('Please call Stripe() with your publishable key. You used an empty string.');
	      }
	    }
	    var $payments = $$O('.bookly-js-payment', $container),
	      $apply_coupon_button = $$O('.bookly-js-apply-coupon', $container),
	      $coupon_input = $$O('input.bookly-user-coupon', $container),
	      $apply_gift_card_button = $$O('.bookly-js-apply-gift-card', $container),
	      $gift_card_input = $$O('input.bookly-user-gift', $container),
	      $apply_tips_button = $$O('.bookly-js-apply-tips', $container),
	      $applied_tips_button = $$O('.bookly-js-applied-tips', $container),
	      $tips_input = $$O('input.bookly-user-tips', $container),
	      $tips_error = $$O('.bookly-js-tips-error', $container),
	      $deposit_mode = $$O('input[type=radio][name=bookly-full-payment]', $container),
	      $coupon_info_text = $$O('.bookly-info-text-coupon', $container),
	      $buttons = $$O('.bookly-gateway-buttons,.bookly-js-details', $container),
	      $payment_details;
	    $payments.on('click', function () {
	      $buttons.hide();
	      $$O('.bookly-gateway-buttons.pay-' + $$O(this).val(), $container).show();
	      if ($$O(this).data('with-details') == 1) {
	        let $parent = $$O(this).closest('.bookly-list');
	        $payment_details = $$O('.bookly-js-details', $parent);
	        $$O('.bookly-js-details', $parent).show();
	      } else {
	        $payment_details = null;
	      }
	    });
	    $payments.eq(0).trigger('click');
	    $deposit_mode.on('change', function () {
	      let data = {
	        action: 'bookly_deposit_payments_apply_payment_method',
	        form_id: params.form_id,
	        deposit_full: $$O(this).val()
	      };
	      $$O(this).hide();
	      $$O(this).prev().css('display', 'inline-block');
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
	        let $error = $$O('<div>', {
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
	        if ($$O('.bookly-js-payment[value!=free]', $container).length > 0) {
	          $gift_card_input.addClass('bookly-error');
	          $apply_gift_card_button.next('.bookly-label-error').remove();
	          let $error = $$O('<div>', {
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
	    $$O('.bookly-js-next-step', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      var ladda = laddaStart(this),
	        $gateway_checked = _filterInstanceProperty$1($payments).call($payments, ':checked');

	      // Execute custom JavaScript
	      if (customJS) {
	        try {
	          $$O.globalEval(customJS.next_button);
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
	                number: $$O('input[name="card_number"]', $payment_details).val(),
	                cvc: $$O('input[name="card_cvc"]', $payment_details).val(),
	                exp_month: $$O('select[name="card_exp_month"]', $payment_details).val(),
	                exp_year: $$O('select[name="card_exp_year"]', $payment_details).val()
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
	                  $$O('.bookly-label-error', $stripe_container).remove();
	                  $stripe_container.append($$O('<div>', {
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
	    $$O('.bookly-js-back-step', $container).on('click', function (e) {
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
	    $$O('.bookly-label-error', $gateway_selector).remove();
	    $gateway_selector.append($$O('<div>', {
	      class: 'bookly-label-error',
	      text: response?.error_message || 'Error'
	    }));
	  }
	}
	function retrieveRequest(data, form_id) {
	  if (data.on_site) {
	    $$O.ajax({
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

	var fails$o = function (exec) {
	  try {
	    return !!exec();
	  } catch (error) {
	    return true;
	  }
	};

	var fails$n = fails$o;

	var functionBindNative = !fails$n(function () {
	  // eslint-disable-next-line es/no-function-prototype-bind -- safe
	  var test = (function () { /* empty */ }).bind();
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return typeof test != 'function' || test.hasOwnProperty('prototype');
	});

	var NATIVE_BIND$3 = functionBindNative;

	var FunctionPrototype$2 = Function.prototype;
	var call$a = FunctionPrototype$2.call;
	var uncurryThisWithBind = NATIVE_BIND$3 && FunctionPrototype$2.bind.bind(call$a, call$a);

	var functionUncurryThis = NATIVE_BIND$3 ? uncurryThisWithBind : function (fn) {
	  return function () {
	    return call$a.apply(fn, arguments);
	  };
	};

	var uncurryThis$o = functionUncurryThis;

	var objectIsPrototypeOf = uncurryThis$o({}.isPrototypeOf);

	var check = function (it) {
	  return it && it.Math == Math && it;
	};

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global$h =
	  // eslint-disable-next-line es/no-global-this -- safe
	  check(typeof globalThis == 'object' && globalThis) ||
	  check(typeof window == 'object' && window) ||
	  // eslint-disable-next-line no-restricted-globals -- safe
	  check(typeof self == 'object' && self) ||
	  check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
	  // eslint-disable-next-line no-new-func -- fallback
	  (function () { return this; })() || commonjsGlobal || Function('return this')();

	var NATIVE_BIND$2 = functionBindNative;

	var FunctionPrototype$1 = Function.prototype;
	var apply$1 = FunctionPrototype$1.apply;
	var call$9 = FunctionPrototype$1.call;

	// eslint-disable-next-line es/no-reflect -- safe
	var functionApply = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND$2 ? call$9.bind(apply$1) : function () {
	  return call$9.apply(apply$1, arguments);
	});

	var uncurryThis$n = functionUncurryThis;

	var toString$9 = uncurryThis$n({}.toString);
	var stringSlice$1 = uncurryThis$n(''.slice);

	var classofRaw$2 = function (it) {
	  return stringSlice$1(toString$9(it), 8, -1);
	};

	var classofRaw$1 = classofRaw$2;
	var uncurryThis$m = functionUncurryThis;

	var functionUncurryThisClause = function (fn) {
	  // Nashorn bug:
	  //   https://github.com/zloirock/core-js/issues/1128
	  //   https://github.com/zloirock/core-js/issues/1130
	  if (classofRaw$1(fn) === 'Function') return uncurryThis$m(fn);
	};

	var documentAll$2 = typeof document == 'object' && document.all;

	// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot
	// eslint-disable-next-line unicorn/no-typeof-undefined -- required for testing
	var IS_HTMLDDA = typeof documentAll$2 == 'undefined' && documentAll$2 !== undefined;

	var documentAll_1 = {
	  all: documentAll$2,
	  IS_HTMLDDA: IS_HTMLDDA
	};

	var $documentAll$1 = documentAll_1;

	var documentAll$1 = $documentAll$1.all;

	// `IsCallable` abstract operation
	// https://tc39.es/ecma262/#sec-iscallable
	var isCallable$f = $documentAll$1.IS_HTMLDDA ? function (argument) {
	  return typeof argument == 'function' || argument === documentAll$1;
	} : function (argument) {
	  return typeof argument == 'function';
	};

	var objectGetOwnPropertyDescriptor = {};

	var fails$m = fails$o;

	// Detect IE8's incomplete defineProperty implementation
	var descriptors = !fails$m(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
	});

	var NATIVE_BIND$1 = functionBindNative;

	var call$8 = Function.prototype.call;

	var functionCall = NATIVE_BIND$1 ? call$8.bind(call$8) : function () {
	  return call$8.apply(call$8, arguments);
	};

	var objectPropertyIsEnumerable = {};

	var $propertyIsEnumerable$1 = {}.propertyIsEnumerable;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;

	// Nashorn ~ JDK8 bug
	var NASHORN_BUG = getOwnPropertyDescriptor$1 && !$propertyIsEnumerable$1.call({ 1: 2 }, 1);

	// `Object.prototype.propertyIsEnumerable` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
	objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
	  var descriptor = getOwnPropertyDescriptor$1(this, V);
	  return !!descriptor && descriptor.enumerable;
	} : $propertyIsEnumerable$1;

	var createPropertyDescriptor$4 = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};

	var uncurryThis$l = functionUncurryThis;
	var fails$l = fails$o;
	var classof$c = classofRaw$2;

	var $Object$5 = Object;
	var split = uncurryThis$l(''.split);

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var indexedObject = fails$l(function () {
	  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return !$Object$5('z').propertyIsEnumerable(0);
	}) ? function (it) {
	  return classof$c(it) == 'String' ? split(it, '') : $Object$5(it);
	} : $Object$5;

	// we can't use just `it == null` since of `document.all` special case
	// https://tc39.es/ecma262/#sec-IsHTMLDDA-internal-slot-aec
	var isNullOrUndefined$7 = function (it) {
	  return it === null || it === undefined;
	};

	var isNullOrUndefined$6 = isNullOrUndefined$7;

	var $TypeError$9 = TypeError;

	// `RequireObjectCoercible` abstract operation
	// https://tc39.es/ecma262/#sec-requireobjectcoercible
	var requireObjectCoercible$6 = function (it) {
	  if (isNullOrUndefined$6(it)) throw $TypeError$9("Can't call method on " + it);
	  return it;
	};

	// toObject with fallback for non-array-like ES3 strings
	var IndexedObject$1 = indexedObject;
	var requireObjectCoercible$5 = requireObjectCoercible$6;

	var toIndexedObject$9 = function (it) {
	  return IndexedObject$1(requireObjectCoercible$5(it));
	};

	var isCallable$e = isCallable$f;
	var $documentAll = documentAll_1;

	var documentAll = $documentAll.all;

	var isObject$h = $documentAll.IS_HTMLDDA ? function (it) {
	  return typeof it == 'object' ? it !== null : isCallable$e(it) || it === documentAll;
	} : function (it) {
	  return typeof it == 'object' ? it !== null : isCallable$e(it);
	};

	var path$c = {};

	var path$b = path$c;
	var global$g = global$h;
	var isCallable$d = isCallable$f;

	var aFunction = function (variable) {
	  return isCallable$d(variable) ? variable : undefined;
	};

	var getBuiltIn$5 = function (namespace, method) {
	  return arguments.length < 2 ? aFunction(path$b[namespace]) || aFunction(global$g[namespace])
	    : path$b[namespace] && path$b[namespace][method] || global$g[namespace] && global$g[namespace][method];
	};

	var engineUserAgent = typeof navigator != 'undefined' && String(navigator.userAgent) || '';

	var global$f = global$h;
	var userAgent$2 = engineUserAgent;

	var process = global$f.process;
	var Deno$1 = global$f.Deno;
	var versions = process && process.versions || Deno$1 && Deno$1.version;
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
	if (!version && userAgent$2) {
	  match = userAgent$2.match(/Edge\/(\d+)/);
	  if (!match || match[1] >= 74) {
	    match = userAgent$2.match(/Chrome\/(\d+)/);
	    if (match) version = +match[1];
	  }
	}

	var engineV8Version = version;

	/* eslint-disable es/no-symbol -- required for testing */

	var V8_VERSION$1 = engineV8Version;
	var fails$k = fails$o;
	var global$e = global$h;

	var $String$3 = global$e.String;

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
	var symbolConstructorDetection = !!Object.getOwnPropertySymbols && !fails$k(function () {
	  var symbol = Symbol();
	  // Chrome 38 Symbol has incorrect toString conversion
	  // `get-own-property-symbols` polyfill symbols converted to object are not Symbol instances
	  // nb: Do not call `String` directly to avoid this being optimized out to `symbol+''` which will,
	  // of course, fail.
	  return !$String$3(symbol) || !(Object(symbol) instanceof Symbol) ||
	    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
	    !Symbol.sham && V8_VERSION$1 && V8_VERSION$1 < 41;
	});

	/* eslint-disable es/no-symbol -- required for testing */

	var NATIVE_SYMBOL$1 = symbolConstructorDetection;

	var useSymbolAsUid = NATIVE_SYMBOL$1
	  && !Symbol.sham
	  && typeof Symbol.iterator == 'symbol';

	var getBuiltIn$4 = getBuiltIn$5;
	var isCallable$c = isCallable$f;
	var isPrototypeOf$7 = objectIsPrototypeOf;
	var USE_SYMBOL_AS_UID$1 = useSymbolAsUid;

	var $Object$4 = Object;

	var isSymbol$2 = USE_SYMBOL_AS_UID$1 ? function (it) {
	  return typeof it == 'symbol';
	} : function (it) {
	  var $Symbol = getBuiltIn$4('Symbol');
	  return isCallable$c($Symbol) && isPrototypeOf$7($Symbol.prototype, $Object$4(it));
	};

	var $String$2 = String;

	var tryToString$3 = function (argument) {
	  try {
	    return $String$2(argument);
	  } catch (error) {
	    return 'Object';
	  }
	};

	var isCallable$b = isCallable$f;
	var tryToString$2 = tryToString$3;

	var $TypeError$8 = TypeError;

	// `Assert: IsCallable(argument) is true`
	var aCallable$5 = function (argument) {
	  if (isCallable$b(argument)) return argument;
	  throw $TypeError$8(tryToString$2(argument) + ' is not a function');
	};

	var aCallable$4 = aCallable$5;
	var isNullOrUndefined$5 = isNullOrUndefined$7;

	// `GetMethod` abstract operation
	// https://tc39.es/ecma262/#sec-getmethod
	var getMethod$3 = function (V, P) {
	  var func = V[P];
	  return isNullOrUndefined$5(func) ? undefined : aCallable$4(func);
	};

	var call$7 = functionCall;
	var isCallable$a = isCallable$f;
	var isObject$g = isObject$h;

	var $TypeError$7 = TypeError;

	// `OrdinaryToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-ordinarytoprimitive
	var ordinaryToPrimitive$1 = function (input, pref) {
	  var fn, val;
	  if (pref === 'string' && isCallable$a(fn = input.toString) && !isObject$g(val = call$7(fn, input))) return val;
	  if (isCallable$a(fn = input.valueOf) && !isObject$g(val = call$7(fn, input))) return val;
	  if (pref !== 'string' && isCallable$a(fn = input.toString) && !isObject$g(val = call$7(fn, input))) return val;
	  throw $TypeError$7("Can't convert object to primitive value");
	};

	var shared$3 = {exports: {}};

	var global$d = global$h;

	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var defineProperty$6 = Object.defineProperty;

	var defineGlobalProperty$1 = function (key, value) {
	  try {
	    defineProperty$6(global$d, key, { value: value, configurable: true, writable: true });
	  } catch (error) {
	    global$d[key] = value;
	  } return value;
	};

	var global$c = global$h;
	var defineGlobalProperty = defineGlobalProperty$1;

	var SHARED = '__core-js_shared__';
	var store$3 = global$c[SHARED] || defineGlobalProperty(SHARED, {});

	var sharedStore = store$3;

	var store$2 = sharedStore;

	(shared$3.exports = function (key, value) {
	  return store$2[key] || (store$2[key] = value !== undefined ? value : {});
	})('versions', []).push({
	  version: '3.31.0',
	  mode: 'pure' ,
	  copyright: '© 2014-2023 Denis Pushkarev (zloirock.ru)',
	  license: 'https://github.com/zloirock/core-js/blob/v3.31.0/LICENSE',
	  source: 'https://github.com/zloirock/core-js'
	});

	var sharedExports = shared$3.exports;

	var requireObjectCoercible$4 = requireObjectCoercible$6;

	var $Object$3 = Object;

	// `ToObject` abstract operation
	// https://tc39.es/ecma262/#sec-toobject
	var toObject$6 = function (argument) {
	  return $Object$3(requireObjectCoercible$4(argument));
	};

	var uncurryThis$k = functionUncurryThis;
	var toObject$5 = toObject$6;

	var hasOwnProperty = uncurryThis$k({}.hasOwnProperty);

	// `HasOwnProperty` abstract operation
	// https://tc39.es/ecma262/#sec-hasownproperty
	// eslint-disable-next-line es/no-object-hasown -- safe
	var hasOwnProperty_1 = Object.hasOwn || function hasOwn(it, key) {
	  return hasOwnProperty(toObject$5(it), key);
	};

	var uncurryThis$j = functionUncurryThis;

	var id$3 = 0;
	var postfix = Math.random();
	var toString$8 = uncurryThis$j(1.0.toString);

	var uid$4 = function (key) {
	  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString$8(++id$3 + postfix, 36);
	};

	var global$b = global$h;
	var shared$2 = sharedExports;
	var hasOwn$a = hasOwnProperty_1;
	var uid$3 = uid$4;
	var NATIVE_SYMBOL = symbolConstructorDetection;
	var USE_SYMBOL_AS_UID = useSymbolAsUid;

	var Symbol$2 = global$b.Symbol;
	var WellKnownSymbolsStore = shared$2('wks');
	var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol$2['for'] || Symbol$2 : Symbol$2 && Symbol$2.withoutSetter || uid$3;

	var wellKnownSymbol$f = function (name) {
	  if (!hasOwn$a(WellKnownSymbolsStore, name)) {
	    WellKnownSymbolsStore[name] = NATIVE_SYMBOL && hasOwn$a(Symbol$2, name)
	      ? Symbol$2[name]
	      : createWellKnownSymbol('Symbol.' + name);
	  } return WellKnownSymbolsStore[name];
	};

	var call$6 = functionCall;
	var isObject$f = isObject$h;
	var isSymbol$1 = isSymbol$2;
	var getMethod$2 = getMethod$3;
	var ordinaryToPrimitive = ordinaryToPrimitive$1;
	var wellKnownSymbol$e = wellKnownSymbol$f;

	var $TypeError$6 = TypeError;
	var TO_PRIMITIVE = wellKnownSymbol$e('toPrimitive');

	// `ToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-toprimitive
	var toPrimitive$1 = function (input, pref) {
	  if (!isObject$f(input) || isSymbol$1(input)) return input;
	  var exoticToPrim = getMethod$2(input, TO_PRIMITIVE);
	  var result;
	  if (exoticToPrim) {
	    if (pref === undefined) pref = 'default';
	    result = call$6(exoticToPrim, input, pref);
	    if (!isObject$f(result) || isSymbol$1(result)) return result;
	    throw $TypeError$6("Can't convert object to primitive value");
	  }
	  if (pref === undefined) pref = 'number';
	  return ordinaryToPrimitive(input, pref);
	};

	var toPrimitive = toPrimitive$1;
	var isSymbol = isSymbol$2;

	// `ToPropertyKey` abstract operation
	// https://tc39.es/ecma262/#sec-topropertykey
	var toPropertyKey$3 = function (argument) {
	  var key = toPrimitive(argument, 'string');
	  return isSymbol(key) ? key : key + '';
	};

	var global$a = global$h;
	var isObject$e = isObject$h;

	var document$1 = global$a.document;
	// typeof document.createElement is 'object' in old IE
	var EXISTS$1 = isObject$e(document$1) && isObject$e(document$1.createElement);

	var documentCreateElement$1 = function (it) {
	  return EXISTS$1 ? document$1.createElement(it) : {};
	};

	var DESCRIPTORS$d = descriptors;
	var fails$j = fails$o;
	var createElement = documentCreateElement$1;

	// Thanks to IE8 for its funny defineProperty
	var ie8DomDefine = !DESCRIPTORS$d && !fails$j(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty(createElement('div'), 'a', {
	    get: function () { return 7; }
	  }).a != 7;
	});

	var DESCRIPTORS$c = descriptors;
	var call$5 = functionCall;
	var propertyIsEnumerableModule = objectPropertyIsEnumerable;
	var createPropertyDescriptor$3 = createPropertyDescriptor$4;
	var toIndexedObject$8 = toIndexedObject$9;
	var toPropertyKey$2 = toPropertyKey$3;
	var hasOwn$9 = hasOwnProperty_1;
	var IE8_DOM_DEFINE$1 = ie8DomDefine;

	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
	objectGetOwnPropertyDescriptor.f = DESCRIPTORS$c ? $getOwnPropertyDescriptor$1 : function getOwnPropertyDescriptor(O, P) {
	  O = toIndexedObject$8(O);
	  P = toPropertyKey$2(P);
	  if (IE8_DOM_DEFINE$1) try {
	    return $getOwnPropertyDescriptor$1(O, P);
	  } catch (error) { /* empty */ }
	  if (hasOwn$9(O, P)) return createPropertyDescriptor$3(!call$5(propertyIsEnumerableModule.f, O, P), O[P]);
	};

	var fails$i = fails$o;
	var isCallable$9 = isCallable$f;

	var replacement = /#|\.prototype\./;

	var isForced$1 = function (feature, detection) {
	  var value = data[normalize(feature)];
	  return value == POLYFILL ? true
	    : value == NATIVE ? false
	    : isCallable$9(detection) ? fails$i(detection)
	    : !!detection;
	};

	var normalize = isForced$1.normalize = function (string) {
	  return String(string).replace(replacement, '.').toLowerCase();
	};

	var data = isForced$1.data = {};
	var NATIVE = isForced$1.NATIVE = 'N';
	var POLYFILL = isForced$1.POLYFILL = 'P';

	var isForced_1 = isForced$1;

	var uncurryThis$i = functionUncurryThisClause;
	var aCallable$3 = aCallable$5;
	var NATIVE_BIND = functionBindNative;

	var bind$7 = uncurryThis$i(uncurryThis$i.bind);

	// optional / simple context binding
	var functionBindContext = function (fn, that) {
	  aCallable$3(fn);
	  return that === undefined ? fn : NATIVE_BIND ? bind$7(fn, that) : function (/* ...args */) {
	    return fn.apply(that, arguments);
	  };
	};

	var objectDefineProperty = {};

	var DESCRIPTORS$b = descriptors;
	var fails$h = fails$o;

	// V8 ~ Chrome 36-
	// https://bugs.chromium.org/p/v8/issues/detail?id=3334
	var v8PrototypeDefineBug = DESCRIPTORS$b && fails$h(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty(function () { /* empty */ }, 'prototype', {
	    value: 42,
	    writable: false
	  }).prototype != 42;
	});

	var isObject$d = isObject$h;

	var $String$1 = String;
	var $TypeError$5 = TypeError;

	// `Assert: Type(argument) is Object`
	var anObject$8 = function (argument) {
	  if (isObject$d(argument)) return argument;
	  throw $TypeError$5($String$1(argument) + ' is not an object');
	};

	var DESCRIPTORS$a = descriptors;
	var IE8_DOM_DEFINE = ie8DomDefine;
	var V8_PROTOTYPE_DEFINE_BUG$1 = v8PrototypeDefineBug;
	var anObject$7 = anObject$8;
	var toPropertyKey$1 = toPropertyKey$3;

	var $TypeError$4 = TypeError;
	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var $defineProperty = Object.defineProperty;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
	var ENUMERABLE = 'enumerable';
	var CONFIGURABLE$1 = 'configurable';
	var WRITABLE = 'writable';

	// `Object.defineProperty` method
	// https://tc39.es/ecma262/#sec-object.defineproperty
	objectDefineProperty.f = DESCRIPTORS$a ? V8_PROTOTYPE_DEFINE_BUG$1 ? function defineProperty(O, P, Attributes) {
	  anObject$7(O);
	  P = toPropertyKey$1(P);
	  anObject$7(Attributes);
	  if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
	    var current = $getOwnPropertyDescriptor(O, P);
	    if (current && current[WRITABLE]) {
	      O[P] = Attributes.value;
	      Attributes = {
	        configurable: CONFIGURABLE$1 in Attributes ? Attributes[CONFIGURABLE$1] : current[CONFIGURABLE$1],
	        enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
	        writable: false
	      };
	    }
	  } return $defineProperty(O, P, Attributes);
	} : $defineProperty : function defineProperty(O, P, Attributes) {
	  anObject$7(O);
	  P = toPropertyKey$1(P);
	  anObject$7(Attributes);
	  if (IE8_DOM_DEFINE) try {
	    return $defineProperty(O, P, Attributes);
	  } catch (error) { /* empty */ }
	  if ('get' in Attributes || 'set' in Attributes) throw $TypeError$4('Accessors not supported');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};

	var DESCRIPTORS$9 = descriptors;
	var definePropertyModule$2 = objectDefineProperty;
	var createPropertyDescriptor$2 = createPropertyDescriptor$4;

	var createNonEnumerableProperty$7 = DESCRIPTORS$9 ? function (object, key, value) {
	  return definePropertyModule$2.f(object, key, createPropertyDescriptor$2(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};

	var global$9 = global$h;
	var apply = functionApply;
	var uncurryThis$h = functionUncurryThisClause;
	var isCallable$8 = isCallable$f;
	var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	var isForced = isForced_1;
	var path$a = path$c;
	var bind$6 = functionBindContext;
	var createNonEnumerableProperty$6 = createNonEnumerableProperty$7;
	var hasOwn$8 = hasOwnProperty_1;

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
	var _export = function (options, source) {
	  var TARGET = options.target;
	  var GLOBAL = options.global;
	  var STATIC = options.stat;
	  var PROTO = options.proto;

	  var nativeSource = GLOBAL ? global$9 : STATIC ? global$9[TARGET] : (global$9[TARGET] || {}).prototype;

	  var target = GLOBAL ? path$a : path$a[TARGET] || createNonEnumerableProperty$6(path$a, TARGET, {})[TARGET];
	  var targetPrototype = target.prototype;

	  var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
	  var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

	  for (key in source) {
	    FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
	    // contains in native
	    USE_NATIVE = !FORCED && nativeSource && hasOwn$8(nativeSource, key);

	    targetProperty = target[key];

	    if (USE_NATIVE) if (options.dontCallGetSet) {
	      descriptor = getOwnPropertyDescriptor(nativeSource, key);
	      nativeProperty = descriptor && descriptor.value;
	    } else nativeProperty = nativeSource[key];

	    // export native or implementation
	    sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

	    if (USE_NATIVE && typeof targetProperty == typeof sourceProperty) continue;

	    // bind methods to global for calling from export context
	    if (options.bind && USE_NATIVE) resultProperty = bind$6(sourceProperty, global$9);
	    // wrap global constructors for prevent changes in this version
	    else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor(sourceProperty);
	    // make static versions for prototype methods
	    else if (PROTO && isCallable$8(sourceProperty)) resultProperty = uncurryThis$h(sourceProperty);
	    // default case
	    else resultProperty = sourceProperty;

	    // add a flag to not completely full polyfills
	    if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
	      createNonEnumerableProperty$6(resultProperty, 'sham', true);
	    }

	    createNonEnumerableProperty$6(target, key, resultProperty);

	    if (PROTO) {
	      VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
	      if (!hasOwn$8(path$a, VIRTUAL_PROTOTYPE)) {
	        createNonEnumerableProperty$6(path$a, VIRTUAL_PROTOTYPE, {});
	      }
	      // export virtual prototype methods
	      createNonEnumerableProperty$6(path$a[VIRTUAL_PROTOTYPE], key, sourceProperty);
	      // export real prototype methods
	      if (options.real && targetPrototype && (FORCED || !targetPrototype[key])) {
	        createNonEnumerableProperty$6(targetPrototype, key, sourceProperty);
	      }
	    }
	  }
	};

	var classof$b = classofRaw$2;

	// `IsArray` abstract operation
	// https://tc39.es/ecma262/#sec-isarray
	// eslint-disable-next-line es/no-array-isarray -- safe
	var isArray$3 = Array.isArray || function isArray(argument) {
	  return classof$b(argument) == 'Array';
	};

	var wellKnownSymbol$d = wellKnownSymbol$f;

	var TO_STRING_TAG$3 = wellKnownSymbol$d('toStringTag');
	var test$1 = {};

	test$1[TO_STRING_TAG$3] = 'z';

	var toStringTagSupport = String(test$1) === '[object z]';

	var TO_STRING_TAG_SUPPORT$2 = toStringTagSupport;
	var isCallable$7 = isCallable$f;
	var classofRaw = classofRaw$2;
	var wellKnownSymbol$c = wellKnownSymbol$f;

	var TO_STRING_TAG$2 = wellKnownSymbol$c('toStringTag');
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
	var classof$a = TO_STRING_TAG_SUPPORT$2 ? classofRaw : function (it) {
	  var O, tag, result;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    // @@toStringTag case
	    : typeof (tag = tryGet(O = $Object$2(it), TO_STRING_TAG$2)) == 'string' ? tag
	    // builtinTag case
	    : CORRECT_ARGUMENTS ? classofRaw(O)
	    // ES3 arguments fallback
	    : (result = classofRaw(O)) == 'Object' && isCallable$7(O.callee) ? 'Arguments' : result;
	};

	var uncurryThis$g = functionUncurryThis;
	var isCallable$6 = isCallable$f;
	var store$1 = sharedStore;

	var functionToString = uncurryThis$g(Function.toString);

	// this helper broken in `core-js@3.4.1-3.4.4`, so we can't use `shared` helper
	if (!isCallable$6(store$1.inspectSource)) {
	  store$1.inspectSource = function (it) {
	    return functionToString(it);
	  };
	}

	var inspectSource$1 = store$1.inspectSource;

	var uncurryThis$f = functionUncurryThis;
	var fails$g = fails$o;
	var isCallable$5 = isCallable$f;
	var classof$9 = classof$a;
	var getBuiltIn$3 = getBuiltIn$5;
	var inspectSource = inspectSource$1;

	var noop$1 = function () { /* empty */ };
	var empty$1 = [];
	var construct = getBuiltIn$3('Reflect', 'construct');
	var constructorRegExp = /^\s*(?:class|function)\b/;
	var exec$1 = uncurryThis$f(constructorRegExp.exec);
	var INCORRECT_TO_STRING = !constructorRegExp.exec(noop$1);

	var isConstructorModern = function isConstructor(argument) {
	  if (!isCallable$5(argument)) return false;
	  try {
	    construct(noop$1, empty$1, argument);
	    return true;
	  } catch (error) {
	    return false;
	  }
	};

	var isConstructorLegacy = function isConstructor(argument) {
	  if (!isCallable$5(argument)) return false;
	  switch (classof$9(argument)) {
	    case 'AsyncFunction':
	    case 'GeneratorFunction':
	    case 'AsyncGeneratorFunction': return false;
	  }
	  try {
	    // we can't check .prototype since constructors produced by .bind haven't it
	    // `Function#toString` throws on some built-it function in some legacy engines
	    // (for example, `DOMQuad` and similar in FF41-)
	    return INCORRECT_TO_STRING || !!exec$1(constructorRegExp, inspectSource(argument));
	  } catch (error) {
	    return true;
	  }
	};

	isConstructorLegacy.sham = true;

	// `IsConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-isconstructor
	var isConstructor$3 = !construct || fails$g(function () {
	  var called;
	  return isConstructorModern(isConstructorModern.call)
	    || !isConstructorModern(Object)
	    || !isConstructorModern(function () { called = true; })
	    || called;
	}) ? isConstructorLegacy : isConstructorModern;

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
	var toIntegerOrInfinity$3 = function (argument) {
	  var number = +argument;
	  // eslint-disable-next-line no-self-compare -- NaN check
	  return number !== number || number === 0 ? 0 : trunc(number);
	};

	var toIntegerOrInfinity$2 = toIntegerOrInfinity$3;

	var max$2 = Math.max;
	var min$1 = Math.min;

	// Helper for a popular repeating case of the spec:
	// Let integer be ? ToInteger(index).
	// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
	var toAbsoluteIndex$4 = function (index, length) {
	  var integer = toIntegerOrInfinity$2(index);
	  return integer < 0 ? max$2(integer + length, 0) : min$1(integer, length);
	};

	var toIntegerOrInfinity$1 = toIntegerOrInfinity$3;

	var min = Math.min;

	// `ToLength` abstract operation
	// https://tc39.es/ecma262/#sec-tolength
	var toLength$1 = function (argument) {
	  return argument > 0 ? min(toIntegerOrInfinity$1(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
	};

	var toLength = toLength$1;

	// `LengthOfArrayLike` abstract operation
	// https://tc39.es/ecma262/#sec-lengthofarraylike
	var lengthOfArrayLike$8 = function (obj) {
	  return toLength(obj.length);
	};

	var toPropertyKey = toPropertyKey$3;
	var definePropertyModule$1 = objectDefineProperty;
	var createPropertyDescriptor$1 = createPropertyDescriptor$4;

	var createProperty$3 = function (object, key, value) {
	  var propertyKey = toPropertyKey(key);
	  if (propertyKey in object) definePropertyModule$1.f(object, propertyKey, createPropertyDescriptor$1(0, value));
	  else object[propertyKey] = value;
	};

	var fails$f = fails$o;
	var wellKnownSymbol$b = wellKnownSymbol$f;
	var V8_VERSION = engineV8Version;

	var SPECIES$3 = wellKnownSymbol$b('species');

	var arrayMethodHasSpeciesSupport$2 = function (METHOD_NAME) {
	  // We can't use this feature detection in V8 since it causes
	  // deoptimization and serious performance degradation
	  // https://github.com/zloirock/core-js/issues/677
	  return V8_VERSION >= 51 || !fails$f(function () {
	    var array = [];
	    var constructor = array.constructor = {};
	    constructor[SPECIES$3] = function () {
	      return { foo: 1 };
	    };
	    return array[METHOD_NAME](Boolean).foo !== 1;
	  });
	};

	var uncurryThis$e = functionUncurryThis;

	var arraySlice$3 = uncurryThis$e([].slice);

	var $$h = _export;
	var isArray$2 = isArray$3;
	var isConstructor$2 = isConstructor$3;
	var isObject$c = isObject$h;
	var toAbsoluteIndex$3 = toAbsoluteIndex$4;
	var lengthOfArrayLike$7 = lengthOfArrayLike$8;
	var toIndexedObject$7 = toIndexedObject$9;
	var createProperty$2 = createProperty$3;
	var wellKnownSymbol$a = wellKnownSymbol$f;
	var arrayMethodHasSpeciesSupport$1 = arrayMethodHasSpeciesSupport$2;
	var nativeSlice = arraySlice$3;

	var HAS_SPECIES_SUPPORT$1 = arrayMethodHasSpeciesSupport$1('slice');

	var SPECIES$2 = wellKnownSymbol$a('species');
	var $Array$3 = Array;
	var max$1 = Math.max;

	// `Array.prototype.slice` method
	// https://tc39.es/ecma262/#sec-array.prototype.slice
	// fallback for not array-like ES3 strings and DOM objects
	$$h({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$1 }, {
	  slice: function slice(start, end) {
	    var O = toIndexedObject$7(this);
	    var length = lengthOfArrayLike$7(O);
	    var k = toAbsoluteIndex$3(start, length);
	    var fin = toAbsoluteIndex$3(end === undefined ? length : end, length);
	    // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
	    var Constructor, result, n;
	    if (isArray$2(O)) {
	      Constructor = O.constructor;
	      // cross-realm fallback
	      if (isConstructor$2(Constructor) && (Constructor === $Array$3 || isArray$2(Constructor.prototype))) {
	        Constructor = undefined;
	      } else if (isObject$c(Constructor)) {
	        Constructor = Constructor[SPECIES$2];
	        if (Constructor === null) Constructor = undefined;
	      }
	      if (Constructor === $Array$3 || Constructor === undefined) {
	        return nativeSlice(O, k, fin);
	      }
	    }
	    result = new (Constructor === undefined ? $Array$3 : Constructor)(max$1(fin - k, 0));
	    for (n = 0; k < fin; k++, n++) if (k in O) createProperty$2(result, n, O[k]);
	    result.length = n;
	    return result;
	  }
	});

	var path$9 = path$c;

	var entryVirtual$4 = function (CONSTRUCTOR) {
	  return path$9[CONSTRUCTOR + 'Prototype'];
	};

	var entryVirtual$3 = entryVirtual$4;

	var slice$3 = entryVirtual$3('Array').slice;

	var isPrototypeOf$6 = objectIsPrototypeOf;
	var method$3 = slice$3;

	var ArrayPrototype$5 = Array.prototype;

	var slice$2 = function (it) {
	  var own = it.slice;
	  return it === ArrayPrototype$5 || (isPrototypeOf$6(ArrayPrototype$5, it) && own === ArrayPrototype$5.slice) ? method$3 : own;
	};

	var parent$g = slice$2;

	var slice$1 = parent$g;

	var slice = slice$1;

	var _sliceInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(slice);

	var isArray$1 = isArray$3;
	var isConstructor$1 = isConstructor$3;
	var isObject$b = isObject$h;
	var wellKnownSymbol$9 = wellKnownSymbol$f;

	var SPECIES$1 = wellKnownSymbol$9('species');
	var $Array$2 = Array;

	// a part of `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesConstructor$1 = function (originalArray) {
	  var C;
	  if (isArray$1(originalArray)) {
	    C = originalArray.constructor;
	    // cross-realm fallback
	    if (isConstructor$1(C) && (C === $Array$2 || isArray$1(C.prototype))) C = undefined;
	    else if (isObject$b(C)) {
	      C = C[SPECIES$1];
	      if (C === null) C = undefined;
	    }
	  } return C === undefined ? $Array$2 : C;
	};

	var arraySpeciesConstructor = arraySpeciesConstructor$1;

	// `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesCreate$1 = function (originalArray, length) {
	  return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
	};

	var bind$5 = functionBindContext;
	var uncurryThis$d = functionUncurryThis;
	var IndexedObject = indexedObject;
	var toObject$4 = toObject$6;
	var lengthOfArrayLike$6 = lengthOfArrayLike$8;
	var arraySpeciesCreate = arraySpeciesCreate$1;

	var push$4 = uncurryThis$d([].push);

	// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterReject }` methods implementation
	var createMethod$4 = function (TYPE) {
	  var IS_MAP = TYPE == 1;
	  var IS_FILTER = TYPE == 2;
	  var IS_SOME = TYPE == 3;
	  var IS_EVERY = TYPE == 4;
	  var IS_FIND_INDEX = TYPE == 6;
	  var IS_FILTER_REJECT = TYPE == 7;
	  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
	  return function ($this, callbackfn, that, specificCreate) {
	    var O = toObject$4($this);
	    var self = IndexedObject(O);
	    var boundFunction = bind$5(callbackfn, that);
	    var length = lengthOfArrayLike$6(self);
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
	          case 2: push$4(target, value);      // filter
	        } else switch (TYPE) {
	          case 4: return false;             // every
	          case 7: push$4(target, value);      // filterReject
	        }
	      }
	    }
	    return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
	  };
	};

	var arrayIteration = {
	  // `Array.prototype.forEach` method
	  // https://tc39.es/ecma262/#sec-array.prototype.foreach
	  forEach: createMethod$4(0),
	  // `Array.prototype.map` method
	  // https://tc39.es/ecma262/#sec-array.prototype.map
	  map: createMethod$4(1),
	  // `Array.prototype.filter` method
	  // https://tc39.es/ecma262/#sec-array.prototype.filter
	  filter: createMethod$4(2),
	  // `Array.prototype.some` method
	  // https://tc39.es/ecma262/#sec-array.prototype.some
	  some: createMethod$4(3),
	  // `Array.prototype.every` method
	  // https://tc39.es/ecma262/#sec-array.prototype.every
	  every: createMethod$4(4),
	  // `Array.prototype.find` method
	  // https://tc39.es/ecma262/#sec-array.prototype.find
	  find: createMethod$4(5),
	  // `Array.prototype.findIndex` method
	  // https://tc39.es/ecma262/#sec-array.prototype.findIndex
	  findIndex: createMethod$4(6),
	  // `Array.prototype.filterReject` method
	  // https://github.com/tc39/proposal-array-filtering
	  filterReject: createMethod$4(7)
	};

	var $$g = _export;
	var $filter = arrayIteration.filter;
	var arrayMethodHasSpeciesSupport = arrayMethodHasSpeciesSupport$2;

	var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter');

	// `Array.prototype.filter` method
	// https://tc39.es/ecma262/#sec-array.prototype.filter
	// with adding support of @@species
	$$g({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
	  filter: function filter(callbackfn /* , thisArg */) {
	    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$2 = entryVirtual$4;

	var filter$3 = entryVirtual$2('Array').filter;

	var isPrototypeOf$5 = objectIsPrototypeOf;
	var method$2 = filter$3;

	var ArrayPrototype$4 = Array.prototype;

	var filter$2 = function (it) {
	  var own = it.filter;
	  return it === ArrayPrototype$4 || (isPrototypeOf$5(ArrayPrototype$4, it) && own === ArrayPrototype$4.filter) ? method$2 : own;
	};

	var parent$f = filter$2;

	var filter$1 = parent$f;

	var filter = filter$1;

	var _filterInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(filter);

	var classof$8 = classof$a;

	var $String = String;

	var toString$7 = function (argument) {
	  if (classof$8(argument) === 'Symbol') throw TypeError('Cannot convert a Symbol value to a string');
	  return $String(argument);
	};

	// a string of all valid unicode whitespaces
	var whitespaces$2 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
	  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

	var uncurryThis$c = functionUncurryThis;
	var requireObjectCoercible$3 = requireObjectCoercible$6;
	var toString$6 = toString$7;
	var whitespaces$1 = whitespaces$2;

	var replace = uncurryThis$c(''.replace);
	var ltrim = RegExp('^[' + whitespaces$1 + ']+');
	var rtrim = RegExp('(^|[^' + whitespaces$1 + '])[' + whitespaces$1 + ']+$');

	// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
	var createMethod$3 = function (TYPE) {
	  return function ($this) {
	    var string = toString$6(requireObjectCoercible$3($this));
	    if (TYPE & 1) string = replace(string, ltrim, '');
	    if (TYPE & 2) string = replace(string, rtrim, '$1');
	    return string;
	  };
	};

	var stringTrim = {
	  // `String.prototype.{ trimLeft, trimStart }` methods
	  // https://tc39.es/ecma262/#sec-string.prototype.trimstart
	  start: createMethod$3(1),
	  // `String.prototype.{ trimRight, trimEnd }` methods
	  // https://tc39.es/ecma262/#sec-string.prototype.trimend
	  end: createMethod$3(2),
	  // `String.prototype.trim` method
	  // https://tc39.es/ecma262/#sec-string.prototype.trim
	  trim: createMethod$3(3)
	};

	var global$8 = global$h;
	var fails$e = fails$o;
	var uncurryThis$b = functionUncurryThis;
	var toString$5 = toString$7;
	var trim = stringTrim.trim;
	var whitespaces = whitespaces$2;

	var $parseInt$1 = global$8.parseInt;
	var Symbol$1 = global$8.Symbol;
	var ITERATOR$4 = Symbol$1 && Symbol$1.iterator;
	var hex = /^[+-]?0x/i;
	var exec = uncurryThis$b(hex.exec);
	var FORCED$1 = $parseInt$1(whitespaces + '08') !== 8 || $parseInt$1(whitespaces + '0x16') !== 22
	  // MS Edge 18- broken with boxed symbols
	  || (ITERATOR$4 && !fails$e(function () { $parseInt$1(Object(ITERATOR$4)); }));

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	var numberParseInt = FORCED$1 ? function parseInt(string, radix) {
	  var S = trim(toString$5(string));
	  return $parseInt$1(S, (radix >>> 0) || (exec(hex, S) ? 16 : 10));
	} : $parseInt$1;

	var $$f = _export;
	var $parseInt = numberParseInt;

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	$$f({ global: true, forced: parseInt != $parseInt }, {
	  parseInt: $parseInt
	});

	var path$8 = path$c;

	var _parseInt$3 = path$8.parseInt;

	var parent$e = _parseInt$3;

	var _parseInt$2 = parent$e;

	var _parseInt = _parseInt$2;

	var _parseInt$1 = /*@__PURE__*/getDefaultExportFromCjs(_parseInt);

	var toIndexedObject$6 = toIndexedObject$9;
	var toAbsoluteIndex$2 = toAbsoluteIndex$4;
	var lengthOfArrayLike$5 = lengthOfArrayLike$8;

	// `Array.prototype.{ indexOf, includes }` methods implementation
	var createMethod$2 = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIndexedObject$6($this);
	    var length = lengthOfArrayLike$5(O);
	    var index = toAbsoluteIndex$2(fromIndex, length);
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
	  includes: createMethod$2(true),
	  // `Array.prototype.indexOf` method
	  // https://tc39.es/ecma262/#sec-array.prototype.indexof
	  indexOf: createMethod$2(false)
	};

	var $$e = _export;
	var $includes = arrayIncludes.includes;
	var fails$d = fails$o;

	// FF99+ bug
	var BROKEN_ON_SPARSE = fails$d(function () {
	  // eslint-disable-next-line es/no-array-prototype-includes -- detection
	  return !Array(1).includes();
	});

	// `Array.prototype.includes` method
	// https://tc39.es/ecma262/#sec-array.prototype.includes
	$$e({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
	  includes: function includes(el /* , fromIndex = 0 */) {
	    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$1 = entryVirtual$4;

	var includes$4 = entryVirtual$1('Array').includes;

	var isObject$a = isObject$h;
	var classof$7 = classofRaw$2;
	var wellKnownSymbol$8 = wellKnownSymbol$f;

	var MATCH$1 = wellKnownSymbol$8('match');

	// `IsRegExp` abstract operation
	// https://tc39.es/ecma262/#sec-isregexp
	var isRegexp = function (it) {
	  var isRegExp;
	  return isObject$a(it) && ((isRegExp = it[MATCH$1]) !== undefined ? !!isRegExp : classof$7(it) == 'RegExp');
	};

	var isRegExp = isRegexp;

	var $TypeError$3 = TypeError;

	var notARegexp = function (it) {
	  if (isRegExp(it)) {
	    throw $TypeError$3("The method doesn't accept regular expressions");
	  } return it;
	};

	var wellKnownSymbol$7 = wellKnownSymbol$f;

	var MATCH = wellKnownSymbol$7('match');

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

	var $$d = _export;
	var uncurryThis$a = functionUncurryThis;
	var notARegExp = notARegexp;
	var requireObjectCoercible$2 = requireObjectCoercible$6;
	var toString$4 = toString$7;
	var correctIsRegExpLogic = correctIsRegexpLogic;

	var stringIndexOf = uncurryThis$a(''.indexOf);

	// `String.prototype.includes` method
	// https://tc39.es/ecma262/#sec-string.prototype.includes
	$$d({ target: 'String', proto: true, forced: !correctIsRegExpLogic('includes') }, {
	  includes: function includes(searchString /* , position = 0 */) {
	    return !!~stringIndexOf(
	      toString$4(requireObjectCoercible$2(this)),
	      toString$4(notARegExp(searchString)),
	      arguments.length > 1 ? arguments[1] : undefined
	    );
	  }
	});

	var entryVirtual = entryVirtual$4;

	var includes$3 = entryVirtual('String').includes;

	var isPrototypeOf$4 = objectIsPrototypeOf;
	var arrayMethod = includes$4;
	var stringMethod = includes$3;

	var ArrayPrototype$3 = Array.prototype;
	var StringPrototype = String.prototype;

	var includes$2 = function (it) {
	  var own = it.includes;
	  if (it === ArrayPrototype$3 || (isPrototypeOf$4(ArrayPrototype$3, it) && own === ArrayPrototype$3.includes)) return arrayMethod;
	  if (typeof it == 'string' || it === StringPrototype || (isPrototypeOf$4(StringPrototype, it) && own === StringPrototype.includes)) {
	    return stringMethod;
	  } return own;
	};

	var parent$d = includes$2;

	var includes$1 = parent$d;

	var includes = includes$1;

	var _includesInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(includes);

	var iterators = {};

	var global$7 = global$h;
	var isCallable$4 = isCallable$f;

	var WeakMap$1 = global$7.WeakMap;

	var weakMapBasicDetection = isCallable$4(WeakMap$1) && /native code/.test(String(WeakMap$1));

	var shared$1 = sharedExports;
	var uid$2 = uid$4;

	var keys = shared$1('keys');

	var sharedKey$3 = function (key) {
	  return keys[key] || (keys[key] = uid$2(key));
	};

	var hiddenKeys$6 = {};

	var NATIVE_WEAK_MAP$1 = weakMapBasicDetection;
	var global$6 = global$h;
	var isObject$9 = isObject$h;
	var createNonEnumerableProperty$5 = createNonEnumerableProperty$7;
	var hasOwn$7 = hasOwnProperty_1;
	var shared = sharedStore;
	var sharedKey$2 = sharedKey$3;
	var hiddenKeys$5 = hiddenKeys$6;

	var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
	var TypeError$1 = global$6.TypeError;
	var WeakMap = global$6.WeakMap;
	var set$7, get$1, has$1;

	var enforce = function (it) {
	  return has$1(it) ? get$1(it) : set$7(it, {});
	};

	var getterFor = function (TYPE) {
	  return function (it) {
	    var state;
	    if (!isObject$9(it) || (state = get$1(it)).type !== TYPE) {
	      throw TypeError$1('Incompatible receiver, ' + TYPE + ' required');
	    } return state;
	  };
	};

	if (NATIVE_WEAK_MAP$1 || shared.state) {
	  var store = shared.state || (shared.state = new WeakMap());
	  /* eslint-disable no-self-assign -- prototype methods protection */
	  store.get = store.get;
	  store.has = store.has;
	  store.set = store.set;
	  /* eslint-enable no-self-assign -- prototype methods protection */
	  set$7 = function (it, metadata) {
	    if (store.has(it)) throw TypeError$1(OBJECT_ALREADY_INITIALIZED);
	    metadata.facade = it;
	    store.set(it, metadata);
	    return metadata;
	  };
	  get$1 = function (it) {
	    return store.get(it) || {};
	  };
	  has$1 = function (it) {
	    return store.has(it);
	  };
	} else {
	  var STATE = sharedKey$2('state');
	  hiddenKeys$5[STATE] = true;
	  set$7 = function (it, metadata) {
	    if (hasOwn$7(it, STATE)) throw TypeError$1(OBJECT_ALREADY_INITIALIZED);
	    metadata.facade = it;
	    createNonEnumerableProperty$5(it, STATE, metadata);
	    return metadata;
	  };
	  get$1 = function (it) {
	    return hasOwn$7(it, STATE) ? it[STATE] : {};
	  };
	  has$1 = function (it) {
	    return hasOwn$7(it, STATE);
	  };
	}

	var internalState = {
	  set: set$7,
	  get: get$1,
	  has: has$1,
	  enforce: enforce,
	  getterFor: getterFor
	};

	var DESCRIPTORS$8 = descriptors;
	var hasOwn$6 = hasOwnProperty_1;

	var FunctionPrototype = Function.prototype;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getDescriptor = DESCRIPTORS$8 && Object.getOwnPropertyDescriptor;

	var EXISTS = hasOwn$6(FunctionPrototype, 'name');
	// additional protection from minified / mangled / dropped function names
	var PROPER = EXISTS && (function something() { /* empty */ }).name === 'something';
	var CONFIGURABLE = EXISTS && (!DESCRIPTORS$8 || (DESCRIPTORS$8 && getDescriptor(FunctionPrototype, 'name').configurable));

	var functionName = {
	  EXISTS: EXISTS,
	  PROPER: PROPER,
	  CONFIGURABLE: CONFIGURABLE
	};

	var objectDefineProperties = {};

	var uncurryThis$9 = functionUncurryThis;
	var hasOwn$5 = hasOwnProperty_1;
	var toIndexedObject$5 = toIndexedObject$9;
	var indexOf = arrayIncludes.indexOf;
	var hiddenKeys$4 = hiddenKeys$6;

	var push$3 = uncurryThis$9([].push);

	var objectKeysInternal = function (object, names) {
	  var O = toIndexedObject$5(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) !hasOwn$5(hiddenKeys$4, key) && hasOwn$5(O, key) && push$3(result, key);
	  // Don't enum bug & hidden keys
	  while (names.length > i) if (hasOwn$5(O, key = names[i++])) {
	    ~indexOf(result, key) || push$3(result, key);
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
	var objectKeys$2 = Object.keys || function keys(O) {
	  return internalObjectKeys$1(O, enumBugKeys$2);
	};

	var DESCRIPTORS$7 = descriptors;
	var V8_PROTOTYPE_DEFINE_BUG = v8PrototypeDefineBug;
	var definePropertyModule = objectDefineProperty;
	var anObject$6 = anObject$8;
	var toIndexedObject$4 = toIndexedObject$9;
	var objectKeys$1 = objectKeys$2;

	// `Object.defineProperties` method
	// https://tc39.es/ecma262/#sec-object.defineproperties
	// eslint-disable-next-line es/no-object-defineproperties -- safe
	objectDefineProperties.f = DESCRIPTORS$7 && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject$6(O);
	  var props = toIndexedObject$4(Properties);
	  var keys = objectKeys$1(Properties);
	  var length = keys.length;
	  var index = 0;
	  var key;
	  while (length > index) definePropertyModule.f(O, key = keys[index++], props[key]);
	  return O;
	};

	var getBuiltIn$2 = getBuiltIn$5;

	var html$1 = getBuiltIn$2('document', 'documentElement');

	/* global ActiveXObject -- old IE, WSH */

	var anObject$5 = anObject$8;
	var definePropertiesModule = objectDefineProperties;
	var enumBugKeys$1 = enumBugKeys$3;
	var hiddenKeys$3 = hiddenKeys$6;
	var html = html$1;
	var documentCreateElement = documentCreateElement$1;
	var sharedKey$1 = sharedKey$3;

	var GT = '>';
	var LT = '<';
	var PROTOTYPE = 'prototype';
	var SCRIPT = 'script';
	var IE_PROTO$1 = sharedKey$1('IE_PROTO');

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
	  var length = enumBugKeys$1.length;
	  while (length--) delete NullProtoObject[PROTOTYPE][enumBugKeys$1[length]];
	  return NullProtoObject();
	};

	hiddenKeys$3[IE_PROTO$1] = true;

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	// eslint-disable-next-line es/no-object-create -- safe
	var objectCreate = Object.create || function create(O, Properties) {
	  var result;
	  if (O !== null) {
	    EmptyConstructor[PROTOTYPE] = anObject$5(O);
	    result = new EmptyConstructor();
	    EmptyConstructor[PROTOTYPE] = null;
	    // add "__proto__" for Object.getPrototypeOf polyfill
	    result[IE_PROTO$1] = O;
	  } else result = NullProtoObject();
	  return Properties === undefined ? result : definePropertiesModule.f(result, Properties);
	};

	var fails$c = fails$o;

	var correctPrototypeGetter = !fails$c(function () {
	  function F() { /* empty */ }
	  F.prototype.constructor = null;
	  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
	  return Object.getPrototypeOf(new F()) !== F.prototype;
	});

	var hasOwn$4 = hasOwnProperty_1;
	var isCallable$3 = isCallable$f;
	var toObject$3 = toObject$6;
	var sharedKey = sharedKey$3;
	var CORRECT_PROTOTYPE_GETTER = correctPrototypeGetter;

	var IE_PROTO = sharedKey('IE_PROTO');
	var $Object$1 = Object;
	var ObjectPrototype = $Object$1.prototype;

	// `Object.getPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.getprototypeof
	// eslint-disable-next-line es/no-object-getprototypeof -- safe
	var objectGetPrototypeOf$1 = CORRECT_PROTOTYPE_GETTER ? $Object$1.getPrototypeOf : function (O) {
	  var object = toObject$3(O);
	  if (hasOwn$4(object, IE_PROTO)) return object[IE_PROTO];
	  var constructor = object.constructor;
	  if (isCallable$3(constructor) && object instanceof constructor) {
	    return constructor.prototype;
	  } return object instanceof $Object$1 ? ObjectPrototype : null;
	};

	var createNonEnumerableProperty$4 = createNonEnumerableProperty$7;

	var defineBuiltIn$4 = function (target, key, value, options) {
	  if (options && options.enumerable) target[key] = value;
	  else createNonEnumerableProperty$4(target, key, value);
	  return target;
	};

	var fails$b = fails$o;
	var isCallable$2 = isCallable$f;
	var isObject$8 = isObject$h;
	var create$7 = objectCreate;
	var getPrototypeOf$1 = objectGetPrototypeOf$1;
	var defineBuiltIn$3 = defineBuiltIn$4;
	var wellKnownSymbol$6 = wellKnownSymbol$f;

	var ITERATOR$3 = wellKnownSymbol$6('iterator');
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
	    PrototypeOfArrayIteratorPrototype = getPrototypeOf$1(getPrototypeOf$1(arrayIterator));
	    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$1 = PrototypeOfArrayIteratorPrototype;
	  }
	}

	var NEW_ITERATOR_PROTOTYPE = !isObject$8(IteratorPrototype$1) || fails$b(function () {
	  var test = {};
	  // FF44- legacy iterators case
	  return IteratorPrototype$1[ITERATOR$3].call(test) !== test;
	});

	if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$1 = {};
	else IteratorPrototype$1 = create$7(IteratorPrototype$1);

	// `%IteratorPrototype%[@@iterator]()` method
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-@@iterator
	if (!isCallable$2(IteratorPrototype$1[ITERATOR$3])) {
	  defineBuiltIn$3(IteratorPrototype$1, ITERATOR$3, function () {
	    return this;
	  });
	}

	var iteratorsCore = {
	  IteratorPrototype: IteratorPrototype$1,
	  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
	};

	var TO_STRING_TAG_SUPPORT$1 = toStringTagSupport;
	var classof$6 = classof$a;

	// `Object.prototype.toString` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.tostring
	var objectToString = TO_STRING_TAG_SUPPORT$1 ? {}.toString : function toString() {
	  return '[object ' + classof$6(this) + ']';
	};

	var TO_STRING_TAG_SUPPORT = toStringTagSupport;
	var defineProperty$5 = objectDefineProperty.f;
	var createNonEnumerableProperty$3 = createNonEnumerableProperty$7;
	var hasOwn$3 = hasOwnProperty_1;
	var toString$3 = objectToString;
	var wellKnownSymbol$5 = wellKnownSymbol$f;

	var TO_STRING_TAG$1 = wellKnownSymbol$5('toStringTag');

	var setToStringTag$4 = function (it, TAG, STATIC, SET_METHOD) {
	  if (it) {
	    var target = STATIC ? it : it.prototype;
	    if (!hasOwn$3(target, TO_STRING_TAG$1)) {
	      defineProperty$5(target, TO_STRING_TAG$1, { configurable: true, value: TAG });
	    }
	    if (SET_METHOD && !TO_STRING_TAG_SUPPORT) {
	      createNonEnumerableProperty$3(target, 'toString', toString$3);
	    }
	  }
	};

	var IteratorPrototype = iteratorsCore.IteratorPrototype;
	var create$6 = objectCreate;
	var createPropertyDescriptor = createPropertyDescriptor$4;
	var setToStringTag$3 = setToStringTag$4;
	var Iterators$5 = iterators;

	var returnThis$1 = function () { return this; };

	var iteratorCreateConstructor = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
	  var TO_STRING_TAG = NAME + ' Iterator';
	  IteratorConstructor.prototype = create$6(IteratorPrototype, { next: createPropertyDescriptor(+!ENUMERABLE_NEXT, next) });
	  setToStringTag$3(IteratorConstructor, TO_STRING_TAG, false, true);
	  Iterators$5[TO_STRING_TAG] = returnThis$1;
	  return IteratorConstructor;
	};

	var $$c = _export;
	var call$4 = functionCall;
	var FunctionName = functionName;
	var createIteratorConstructor = iteratorCreateConstructor;
	var getPrototypeOf = objectGetPrototypeOf$1;
	var setToStringTag$2 = setToStringTag$4;
	var defineBuiltIn$2 = defineBuiltIn$4;
	var wellKnownSymbol$4 = wellKnownSymbol$f;
	var Iterators$4 = iterators;
	var IteratorsCore = iteratorsCore;

	var PROPER_FUNCTION_NAME = FunctionName.PROPER;
	FunctionName.CONFIGURABLE;
	IteratorsCore.IteratorPrototype;
	var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
	var ITERATOR$2 = wellKnownSymbol$4('iterator');
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
	  var nativeIterator = IterablePrototype[ITERATOR$2]
	    || IterablePrototype['@@iterator']
	    || DEFAULT && IterablePrototype[DEFAULT];
	  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
	  var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
	  var CurrentIteratorPrototype, methods, KEY;

	  // fix native
	  if (anyNativeIterator) {
	    CurrentIteratorPrototype = getPrototypeOf(anyNativeIterator.call(new Iterable()));
	    if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
	      // Set @@toStringTag to native iterators
	      setToStringTag$2(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
	      Iterators$4[TO_STRING_TAG] = returnThis;
	    }
	  }

	  // fix Array.prototype.{ values, @@iterator }.name in V8 / FF
	  if (PROPER_FUNCTION_NAME && DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
	    {
	      INCORRECT_VALUES_NAME = true;
	      defaultIterator = function values() { return call$4(nativeIterator, this); };
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
	    } else $$c({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
	  }

	  // define iterator
	  if ((FORCED) && IterablePrototype[ITERATOR$2] !== defaultIterator) {
	    defineBuiltIn$2(IterablePrototype, ITERATOR$2, defaultIterator, { name: DEFAULT });
	  }
	  Iterators$4[NAME] = defaultIterator;

	  return methods;
	};

	// `CreateIterResultObject` abstract operation
	// https://tc39.es/ecma262/#sec-createiterresultobject
	var createIterResultObject$4 = function (value, done) {
	  return { value: value, done: done };
	};

	var toIndexedObject$3 = toIndexedObject$9;
	var Iterators$3 = iterators;
	var InternalStateModule$6 = internalState;
	objectDefineProperty.f;
	var defineIterator$3 = iteratorDefine;
	var createIterResultObject$3 = createIterResultObject$4;

	var ARRAY_ITERATOR = 'Array Iterator';
	var setInternalState$6 = InternalStateModule$6.set;
	var getInternalState$1 = InternalStateModule$6.getterFor(ARRAY_ITERATOR);

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
	defineIterator$3(Array, 'Array', function (iterated, kind) {
	  setInternalState$6(this, {
	    type: ARRAY_ITERATOR,
	    target: toIndexedObject$3(iterated), // target
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
	    return createIterResultObject$3(undefined, true);
	  }
	  if (kind == 'keys') return createIterResultObject$3(index, false);
	  if (kind == 'values') return createIterResultObject$3(target[index], false);
	  return createIterResultObject$3([index, target[index]], false);
	}, 'values');

	// argumentsList[@@iterator] is %ArrayProto_values%
	// https://tc39.es/ecma262/#sec-createunmappedargumentsobject
	// https://tc39.es/ecma262/#sec-createmappedargumentsobject
	Iterators$3.Arguments = Iterators$3.Array;

	var internalMetadata$1 = {exports: {}};

	var objectGetOwnPropertyNames = {};

	var internalObjectKeys = objectKeysInternal;
	var enumBugKeys = enumBugKeys$3;

	var hiddenKeys$2 = enumBugKeys.concat('length', 'prototype');

	// `Object.getOwnPropertyNames` method
	// https://tc39.es/ecma262/#sec-object.getownpropertynames
	// eslint-disable-next-line es/no-object-getownpropertynames -- safe
	objectGetOwnPropertyNames.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
	  return internalObjectKeys(O, hiddenKeys$2);
	};

	var objectGetOwnPropertyNamesExternal$1 = {};

	var toAbsoluteIndex$1 = toAbsoluteIndex$4;
	var lengthOfArrayLike$4 = lengthOfArrayLike$8;
	var createProperty$1 = createProperty$3;

	var $Array$1 = Array;
	var max = Math.max;

	var arraySliceSimple = function (O, start, end) {
	  var length = lengthOfArrayLike$4(O);
	  var k = toAbsoluteIndex$1(start, length);
	  var fin = toAbsoluteIndex$1(end === undefined ? length : end, length);
	  var result = $Array$1(max(fin - k, 0));
	  for (var n = 0; k < fin; k++, n++) createProperty$1(result, n, O[k]);
	  result.length = n;
	  return result;
	};

	/* eslint-disable es/no-object-getownpropertynames -- safe */

	var classof$5 = classofRaw$2;
	var toIndexedObject$2 = toIndexedObject$9;
	var $getOwnPropertyNames$1 = objectGetOwnPropertyNames.f;
	var arraySlice$2 = arraySliceSimple;

	var windowNames$1 = typeof window == 'object' && window && Object.getOwnPropertyNames
	  ? Object.getOwnPropertyNames(window) : [];

	var getWindowNames$1 = function (it) {
	  try {
	    return $getOwnPropertyNames$1(it);
	  } catch (error) {
	    return arraySlice$2(windowNames$1);
	  }
	};

	// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
	objectGetOwnPropertyNamesExternal$1.f = function getOwnPropertyNames(it) {
	  return windowNames$1 && classof$5(it) == 'Window'
	    ? getWindowNames$1(it)
	    : $getOwnPropertyNames$1(toIndexedObject$2(it));
	};

	// FF26- bug: ArrayBuffers are non-extensible, but Object.isExtensible does not report it
	var fails$a = fails$o;

	var arrayBufferNonExtensible$1 = fails$a(function () {
	  if (typeof ArrayBuffer == 'function') {
	    var buffer = new ArrayBuffer(8);
	    // eslint-disable-next-line es/no-object-isextensible, es/no-object-defineproperty -- safe
	    if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
	  }
	});

	var fails$9 = fails$o;
	var isObject$7 = isObject$h;
	var classof$4 = classofRaw$2;
	var ARRAY_BUFFER_NON_EXTENSIBLE$1 = arrayBufferNonExtensible$1;

	// eslint-disable-next-line es/no-object-isextensible -- safe
	var $isExtensible$1 = Object.isExtensible;
	var FAILS_ON_PRIMITIVES$1 = fails$9(function () { $isExtensible$1(1); });

	// `Object.isExtensible` method
	// https://tc39.es/ecma262/#sec-object.isextensible
	var objectIsExtensible$1 = (FAILS_ON_PRIMITIVES$1 || ARRAY_BUFFER_NON_EXTENSIBLE$1) ? function isExtensible(it) {
	  if (!isObject$7(it)) return false;
	  if (ARRAY_BUFFER_NON_EXTENSIBLE$1 && classof$4(it) == 'ArrayBuffer') return false;
	  return $isExtensible$1 ? $isExtensible$1(it) : true;
	} : $isExtensible$1;

	var fails$8 = fails$o;

	var freezing$1 = !fails$8(function () {
	  // eslint-disable-next-line es/no-object-isextensible, es/no-object-preventextensions -- required for testing
	  return Object.isExtensible(Object.preventExtensions({}));
	});

	var $$b = _export;
	var uncurryThis$8 = functionUncurryThis;
	var hiddenKeys$1 = hiddenKeys$6;
	var isObject$6 = isObject$h;
	var hasOwn$2 = hasOwnProperty_1;
	var defineProperty$4 = objectDefineProperty.f;
	var getOwnPropertyNamesModule$1 = objectGetOwnPropertyNames;
	var getOwnPropertyNamesExternalModule$1 = objectGetOwnPropertyNamesExternal$1;
	var isExtensible$2 = objectIsExtensible$1;
	var uid$1 = uid$4;
	var FREEZING$2 = freezing$1;

	var REQUIRED$1 = false;
	var METADATA$1 = uid$1('meta');
	var id$2 = 0;

	var setMetadata$1 = function (it) {
	  defineProperty$4(it, METADATA$1, { value: {
	    objectID: 'O' + id$2++, // object ID
	    weakData: {}          // weak collections IDs
	  } });
	};

	var fastKey$3 = function (it, create) {
	  // return a primitive with prefix
	  if (!isObject$6(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
	  if (!hasOwn$2(it, METADATA$1)) {
	    // can't set metadata to uncaught frozen object
	    if (!isExtensible$2(it)) return 'F';
	    // not necessary to add metadata
	    if (!create) return 'E';
	    // add missing metadata
	    setMetadata$1(it);
	  // return object ID
	  } return it[METADATA$1].objectID;
	};

	var getWeakData$2 = function (it, create) {
	  if (!hasOwn$2(it, METADATA$1)) {
	    // can't set metadata to uncaught frozen object
	    if (!isExtensible$2(it)) return true;
	    // not necessary to add metadata
	    if (!create) return false;
	    // add missing metadata
	    setMetadata$1(it);
	  // return the store of weak collections IDs
	  } return it[METADATA$1].weakData;
	};

	// add metadata on freeze-family methods calling
	var onFreeze$1 = function (it) {
	  if (FREEZING$2 && REQUIRED$1 && isExtensible$2(it) && !hasOwn$2(it, METADATA$1)) setMetadata$1(it);
	  return it;
	};

	var enable$1 = function () {
	  meta$1.enable = function () { /* empty */ };
	  REQUIRED$1 = true;
	  var getOwnPropertyNames = getOwnPropertyNamesModule$1.f;
	  var splice = uncurryThis$8([].splice);
	  var test = {};
	  test[METADATA$1] = 1;

	  // prevent exposing of metadata key
	  if (getOwnPropertyNames(test).length) {
	    getOwnPropertyNamesModule$1.f = function (it) {
	      var result = getOwnPropertyNames(it);
	      for (var i = 0, length = result.length; i < length; i++) {
	        if (result[i] === METADATA$1) {
	          splice(result, i, 1);
	          break;
	        }
	      } return result;
	    };

	    $$b({ target: 'Object', stat: true, forced: true }, {
	      getOwnPropertyNames: getOwnPropertyNamesExternalModule$1.f
	    });
	  }
	};

	var meta$1 = internalMetadata$1.exports = {
	  enable: enable$1,
	  fastKey: fastKey$3,
	  getWeakData: getWeakData$2,
	  onFreeze: onFreeze$1
	};

	hiddenKeys$1[METADATA$1] = true;

	var internalMetadataExports$1 = internalMetadata$1.exports;

	var wellKnownSymbol$3 = wellKnownSymbol$f;
	var Iterators$2 = iterators;

	var ITERATOR$1 = wellKnownSymbol$3('iterator');
	var ArrayPrototype$2 = Array.prototype;

	// check on default Array iterator
	var isArrayIteratorMethod$2 = function (it) {
	  return it !== undefined && (Iterators$2.Array === it || ArrayPrototype$2[ITERATOR$1] === it);
	};

	var classof$3 = classof$a;
	var getMethod$1 = getMethod$3;
	var isNullOrUndefined$4 = isNullOrUndefined$7;
	var Iterators$1 = iterators;
	var wellKnownSymbol$2 = wellKnownSymbol$f;

	var ITERATOR = wellKnownSymbol$2('iterator');

	var getIteratorMethod$3 = function (it) {
	  if (!isNullOrUndefined$4(it)) return getMethod$1(it, ITERATOR)
	    || getMethod$1(it, '@@iterator')
	    || Iterators$1[classof$3(it)];
	};

	var call$3 = functionCall;
	var aCallable$2 = aCallable$5;
	var anObject$4 = anObject$8;
	var tryToString$1 = tryToString$3;
	var getIteratorMethod$2 = getIteratorMethod$3;

	var $TypeError$2 = TypeError;

	var getIterator$2 = function (argument, usingIterator) {
	  var iteratorMethod = arguments.length < 2 ? getIteratorMethod$2(argument) : usingIterator;
	  if (aCallable$2(iteratorMethod)) return anObject$4(call$3(iteratorMethod, argument));
	  throw $TypeError$2(tryToString$1(argument) + ' is not iterable');
	};

	var call$2 = functionCall;
	var anObject$3 = anObject$8;
	var getMethod = getMethod$3;

	var iteratorClose$2 = function (iterator, kind, value) {
	  var innerResult, innerError;
	  anObject$3(iterator);
	  try {
	    innerResult = getMethod(iterator, 'return');
	    if (!innerResult) {
	      if (kind === 'throw') throw value;
	      return value;
	    }
	    innerResult = call$2(innerResult, iterator);
	  } catch (error) {
	    innerError = true;
	    innerResult = error;
	  }
	  if (kind === 'throw') throw value;
	  if (innerError) throw innerResult;
	  anObject$3(innerResult);
	  return value;
	};

	var bind$4 = functionBindContext;
	var call$1 = functionCall;
	var anObject$2 = anObject$8;
	var tryToString = tryToString$3;
	var isArrayIteratorMethod$1 = isArrayIteratorMethod$2;
	var lengthOfArrayLike$3 = lengthOfArrayLike$8;
	var isPrototypeOf$3 = objectIsPrototypeOf;
	var getIterator$1 = getIterator$2;
	var getIteratorMethod$1 = getIteratorMethod$3;
	var iteratorClose$1 = iteratorClose$2;

	var $TypeError$1 = TypeError;

	var Result = function (stopped, result) {
	  this.stopped = stopped;
	  this.result = result;
	};

	var ResultPrototype = Result.prototype;

	var iterate$6 = function (iterable, unboundFunction, options) {
	  var that = options && options.that;
	  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
	  var IS_RECORD = !!(options && options.IS_RECORD);
	  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
	  var INTERRUPTED = !!(options && options.INTERRUPTED);
	  var fn = bind$4(unboundFunction, that);
	  var iterator, iterFn, index, length, result, next, step;

	  var stop = function (condition) {
	    if (iterator) iteratorClose$1(iterator, 'normal', condition);
	    return new Result(true, condition);
	  };

	  var callFn = function (value) {
	    if (AS_ENTRIES) {
	      anObject$2(value);
	      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
	    } return INTERRUPTED ? fn(value, stop) : fn(value);
	  };

	  if (IS_RECORD) {
	    iterator = iterable.iterator;
	  } else if (IS_ITERATOR) {
	    iterator = iterable;
	  } else {
	    iterFn = getIteratorMethod$1(iterable);
	    if (!iterFn) throw $TypeError$1(tryToString(iterable) + ' is not iterable');
	    // optimisation for array iterators
	    if (isArrayIteratorMethod$1(iterFn)) {
	      for (index = 0, length = lengthOfArrayLike$3(iterable); length > index; index++) {
	        result = callFn(iterable[index]);
	        if (result && isPrototypeOf$3(ResultPrototype, result)) return result;
	      } return new Result(false);
	    }
	    iterator = getIterator$1(iterable, iterFn);
	  }

	  next = IS_RECORD ? iterable.next : iterator.next;
	  while (!(step = call$1(next, iterator)).done) {
	    try {
	      result = callFn(step.value);
	    } catch (error) {
	      iteratorClose$1(iterator, 'throw', error);
	    }
	    if (typeof result == 'object' && result && isPrototypeOf$3(ResultPrototype, result)) return result;
	  } return new Result(false);
	};

	var isPrototypeOf$2 = objectIsPrototypeOf;

	var $TypeError = TypeError;

	var anInstance$5 = function (it, Prototype) {
	  if (isPrototypeOf$2(Prototype, it)) return it;
	  throw $TypeError('Incorrect invocation');
	};

	var $$a = _export;
	var global$5 = global$h;
	var InternalMetadataModule$2 = internalMetadataExports$1;
	var fails$7 = fails$o;
	var createNonEnumerableProperty$2 = createNonEnumerableProperty$7;
	var iterate$5 = iterate$6;
	var anInstance$4 = anInstance$5;
	var isCallable$1 = isCallable$f;
	var isObject$5 = isObject$h;
	var setToStringTag$1 = setToStringTag$4;
	var defineProperty$3 = objectDefineProperty.f;
	var forEach$1 = arrayIteration.forEach;
	var DESCRIPTORS$6 = descriptors;
	var InternalStateModule$5 = internalState;

	var setInternalState$5 = InternalStateModule$5.set;
	var internalStateGetterFor$4 = InternalStateModule$5.getterFor;

	var collection$5 = function (CONSTRUCTOR_NAME, wrapper, common) {
	  var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
	  var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
	  var ADDER = IS_MAP ? 'set' : 'add';
	  var NativeConstructor = global$5[CONSTRUCTOR_NAME];
	  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
	  var exported = {};
	  var Constructor;

	  if (!DESCRIPTORS$6 || !isCallable$1(NativeConstructor)
	    || !(IS_WEAK || NativePrototype.forEach && !fails$7(function () { new NativeConstructor().entries().next(); }))
	  ) {
	    // create collection constructor
	    Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
	    InternalMetadataModule$2.enable();
	  } else {
	    Constructor = wrapper(function (target, iterable) {
	      setInternalState$5(anInstance$4(target, Prototype), {
	        type: CONSTRUCTOR_NAME,
	        collection: new NativeConstructor()
	      });
	      if (iterable != undefined) iterate$5(iterable, target[ADDER], { that: target, AS_ENTRIES: IS_MAP });
	    });

	    var Prototype = Constructor.prototype;

	    var getInternalState = internalStateGetterFor$4(CONSTRUCTOR_NAME);

	    forEach$1(['add', 'clear', 'delete', 'forEach', 'get', 'has', 'set', 'keys', 'values', 'entries'], function (KEY) {
	      var IS_ADDER = KEY == 'add' || KEY == 'set';
	      if (KEY in NativePrototype && !(IS_WEAK && KEY == 'clear')) {
	        createNonEnumerableProperty$2(Prototype, KEY, function (a, b) {
	          var collection = getInternalState(this).collection;
	          if (!IS_ADDER && IS_WEAK && !isObject$5(a)) return KEY == 'get' ? undefined : false;
	          var result = collection[KEY](a === 0 ? 0 : a, b);
	          return IS_ADDER ? this : result;
	        });
	      }
	    });

	    IS_WEAK || defineProperty$3(Prototype, 'size', {
	      configurable: true,
	      get: function () {
	        return getInternalState(this).collection.size;
	      }
	    });
	  }

	  setToStringTag$1(Constructor, CONSTRUCTOR_NAME, false, true);

	  exported[CONSTRUCTOR_NAME] = Constructor;
	  $$a({ global: true, forced: true }, exported);

	  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

	  return Constructor;
	};

	var defineProperty$2 = objectDefineProperty;

	var defineBuiltInAccessor$3 = function (target, name, descriptor) {
	  return defineProperty$2.f(target, name, descriptor);
	};

	var defineBuiltIn$1 = defineBuiltIn$4;

	var defineBuiltIns$5 = function (target, src, options) {
	  for (var key in src) {
	    if (options && options.unsafe && target[key]) target[key] = src[key];
	    else defineBuiltIn$1(target, key, src[key], options);
	  } return target;
	};

	var getBuiltIn$1 = getBuiltIn$5;
	var defineBuiltInAccessor$2 = defineBuiltInAccessor$3;
	var wellKnownSymbol$1 = wellKnownSymbol$f;
	var DESCRIPTORS$5 = descriptors;

	var SPECIES = wellKnownSymbol$1('species');

	var setSpecies$2 = function (CONSTRUCTOR_NAME) {
	  var Constructor = getBuiltIn$1(CONSTRUCTOR_NAME);

	  if (DESCRIPTORS$5 && Constructor && !Constructor[SPECIES]) {
	    defineBuiltInAccessor$2(Constructor, SPECIES, {
	      configurable: true,
	      get: function () { return this; }
	    });
	  }
	};

	var create$5 = objectCreate;
	var defineBuiltInAccessor$1 = defineBuiltInAccessor$3;
	var defineBuiltIns$4 = defineBuiltIns$5;
	var bind$3 = functionBindContext;
	var anInstance$3 = anInstance$5;
	var isNullOrUndefined$3 = isNullOrUndefined$7;
	var iterate$4 = iterate$6;
	var defineIterator$2 = iteratorDefine;
	var createIterResultObject$2 = createIterResultObject$4;
	var setSpecies$1 = setSpecies$2;
	var DESCRIPTORS$4 = descriptors;
	var fastKey$2 = internalMetadataExports$1.fastKey;
	var InternalStateModule$4 = internalState;

	var setInternalState$4 = InternalStateModule$4.set;
	var internalStateGetterFor$3 = InternalStateModule$4.getterFor;

	var collectionStrong$4 = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var Constructor = wrapper(function (that, iterable) {
	      anInstance$3(that, Prototype);
	      setInternalState$4(that, {
	        type: CONSTRUCTOR_NAME,
	        index: create$5(null),
	        first: undefined,
	        last: undefined,
	        size: 0
	      });
	      if (!DESCRIPTORS$4) that.size = 0;
	      if (!isNullOrUndefined$3(iterable)) iterate$4(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
	    });

	    var Prototype = Constructor.prototype;

	    var getInternalState = internalStateGetterFor$3(CONSTRUCTOR_NAME);

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
	          index: index = fastKey$2(key, true),
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
	      var index = fastKey$2(key);
	      var entry;
	      if (index !== 'F') return state.index[index];
	      // frozen object case
	      for (entry = state.first; entry; entry = entry.next) {
	        if (entry.key == key) return entry;
	      }
	    };

	    defineBuiltIns$4(Prototype, {
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
	        var boundFunction = bind$3(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
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

	    defineBuiltIns$4(Prototype, IS_MAP ? {
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
	    if (DESCRIPTORS$4) defineBuiltInAccessor$1(Prototype, 'size', {
	      configurable: true,
	      get: function () {
	        return getInternalState(this).size;
	      }
	    });
	    return Constructor;
	  },
	  setStrong: function (Constructor, CONSTRUCTOR_NAME, IS_MAP) {
	    var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
	    var getInternalCollectionState = internalStateGetterFor$3(CONSTRUCTOR_NAME);
	    var getInternalIteratorState = internalStateGetterFor$3(ITERATOR_NAME);
	    // `{ Map, Set }.prototype.{ keys, values, entries, @@iterator }()` methods
	    // https://tc39.es/ecma262/#sec-map.prototype.entries
	    // https://tc39.es/ecma262/#sec-map.prototype.keys
	    // https://tc39.es/ecma262/#sec-map.prototype.values
	    // https://tc39.es/ecma262/#sec-map.prototype-@@iterator
	    // https://tc39.es/ecma262/#sec-set.prototype.entries
	    // https://tc39.es/ecma262/#sec-set.prototype.keys
	    // https://tc39.es/ecma262/#sec-set.prototype.values
	    // https://tc39.es/ecma262/#sec-set.prototype-@@iterator
	    defineIterator$2(Constructor, CONSTRUCTOR_NAME, function (iterated, kind) {
	      setInternalState$4(this, {
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
	        return createIterResultObject$2(undefined, true);
	      }
	      // return step by kind
	      if (kind == 'keys') return createIterResultObject$2(entry.key, false);
	      if (kind == 'values') return createIterResultObject$2(entry.value, false);
	      return createIterResultObject$2([entry.key, entry.value], false);
	    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

	    // `{ Map, Set }.prototype[@@species]` accessors
	    // https://tc39.es/ecma262/#sec-get-map-@@species
	    // https://tc39.es/ecma262/#sec-get-set-@@species
	    setSpecies$1(CONSTRUCTOR_NAME);
	  }
	};

	var collection$4 = collection$5;
	var collectionStrong$3 = collectionStrong$4;

	// `Set` constructor
	// https://tc39.es/ecma262/#sec-set-objects
	collection$4('Set', function (init) {
	  return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong$3);

	var uncurryThis$7 = functionUncurryThis;
	var toIntegerOrInfinity = toIntegerOrInfinity$3;
	var toString$2 = toString$7;
	var requireObjectCoercible$1 = requireObjectCoercible$6;

	var charAt$1 = uncurryThis$7(''.charAt);
	var charCodeAt = uncurryThis$7(''.charCodeAt);
	var stringSlice = uncurryThis$7(''.slice);

	var createMethod$1 = function (CONVERT_TO_STRING) {
	  return function ($this, pos) {
	    var S = toString$2(requireObjectCoercible$1($this));
	    var position = toIntegerOrInfinity(pos);
	    var size = S.length;
	    var first, second;
	    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
	    first = charCodeAt(S, position);
	    return first < 0xD800 || first > 0xDBFF || position + 1 === size
	      || (second = charCodeAt(S, position + 1)) < 0xDC00 || second > 0xDFFF
	        ? CONVERT_TO_STRING
	          ? charAt$1(S, position)
	          : first
	        : CONVERT_TO_STRING
	          ? stringSlice(S, position, position + 2)
	          : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
	  };
	};

	var stringMultibyte = {
	  // `String.prototype.codePointAt` method
	  // https://tc39.es/ecma262/#sec-string.prototype.codepointat
	  codeAt: createMethod$1(false),
	  // `String.prototype.at` method
	  // https://github.com/mathiasbynens/String.prototype.at
	  charAt: createMethod$1(true)
	};

	var charAt = stringMultibyte.charAt;
	var toString$1 = toString$7;
	var InternalStateModule$3 = internalState;
	var defineIterator$1 = iteratorDefine;
	var createIterResultObject$1 = createIterResultObject$4;

	var STRING_ITERATOR = 'String Iterator';
	var setInternalState$3 = InternalStateModule$3.set;
	var getInternalState = InternalStateModule$3.getterFor(STRING_ITERATOR);

	// `String.prototype[@@iterator]` method
	// https://tc39.es/ecma262/#sec-string.prototype-@@iterator
	defineIterator$1(String, 'String', function (iterated) {
	  setInternalState$3(this, {
	    type: STRING_ITERATOR,
	    string: toString$1(iterated),
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
	  point = charAt(string, index);
	  state.index += point.length;
	  return createIterResultObject$1(point, false);
	});

	var path$7 = path$c;

	var set$6 = path$7.Set;

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

	var DOMIterables = domIterables;
	var global$4 = global$h;
	var classof$2 = classof$a;
	var createNonEnumerableProperty$1 = createNonEnumerableProperty$7;
	var Iterators = iterators;
	var wellKnownSymbol = wellKnownSymbol$f;

	var TO_STRING_TAG = wellKnownSymbol('toStringTag');

	for (var COLLECTION_NAME in DOMIterables) {
	  var Collection = global$4[COLLECTION_NAME];
	  var CollectionPrototype = Collection && Collection.prototype;
	  if (CollectionPrototype && classof$2(CollectionPrototype) !== TO_STRING_TAG) {
	    createNonEnumerableProperty$1(CollectionPrototype, TO_STRING_TAG, COLLECTION_NAME);
	  }
	  Iterators[COLLECTION_NAME] = Iterators.Array;
	}

	var parent$c = set$6;


	var set$5 = parent$c;

	var set$4 = set$5;

	var _Set$1 = /*@__PURE__*/getDefaultExportFromCjs(set$4);

	// TODO: Remove from `core-js@4`
	var $$9 = _export$1;
	var DESCRIPTORS$3 = descriptors$1;
	var create$4 = objectCreate$1;

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	$$9({ target: 'Object', stat: true, sham: !DESCRIPTORS$3 }, {
	  create: create$4
	});

	var path$6 = path$m;

	var Object$1 = path$6.Object;

	var create$3 = function create(P, D) {
	  return Object$1.create(P, D);
	};

	var parent$b = create$3;

	var create$2 = parent$b;

	var create$1 = create$2;

	var _Object$create = /*@__PURE__*/getDefaultExportFromCjs(create$1);

	var internalMetadata = {exports: {}};

	var objectGetOwnPropertyNamesExternal = {};

	/* eslint-disable es/no-object-getownpropertynames -- safe */
	var classof$1 = classofRaw$5;
	var toIndexedObject$1 = toIndexedObject$g;
	var $getOwnPropertyNames = objectGetOwnPropertyNames$1.f;
	var arraySlice$1 = arraySlice$7;

	var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
	  ? Object.getOwnPropertyNames(window) : [];

	var getWindowNames = function (it) {
	  try {
	    return $getOwnPropertyNames(it);
	  } catch (error) {
	    return arraySlice$1(windowNames);
	  }
	};

	// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
	objectGetOwnPropertyNamesExternal.f = function getOwnPropertyNames(it) {
	  return windowNames && classof$1(it) === 'Window'
	    ? getWindowNames(it)
	    : $getOwnPropertyNames(toIndexedObject$1(it));
	};

	// FF26- bug: ArrayBuffers are non-extensible, but Object.isExtensible does not report it
	var fails$6 = fails$L;

	var arrayBufferNonExtensible = fails$6(function () {
	  if (typeof ArrayBuffer == 'function') {
	    var buffer = new ArrayBuffer(8);
	    // eslint-disable-next-line es/no-object-isextensible, es/no-object-defineproperty -- safe
	    if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
	  }
	});

	var fails$5 = fails$L;
	var isObject$4 = isObject$w;
	var classof = classofRaw$5;
	var ARRAY_BUFFER_NON_EXTENSIBLE = arrayBufferNonExtensible;

	// eslint-disable-next-line es/no-object-isextensible -- safe
	var $isExtensible = Object.isExtensible;
	var FAILS_ON_PRIMITIVES = fails$5(function () { $isExtensible(1); });

	// `Object.isExtensible` method
	// https://tc39.es/ecma262/#sec-object.isextensible
	var objectIsExtensible = (FAILS_ON_PRIMITIVES || ARRAY_BUFFER_NON_EXTENSIBLE) ? function isExtensible(it) {
	  if (!isObject$4(it)) return false;
	  if (ARRAY_BUFFER_NON_EXTENSIBLE && classof(it) === 'ArrayBuffer') return false;
	  return $isExtensible ? $isExtensible(it) : true;
	} : $isExtensible;

	var fails$4 = fails$L;

	var freezing = !fails$4(function () {
	  // eslint-disable-next-line es/no-object-isextensible, es/no-object-preventextensions -- required for testing
	  return Object.isExtensible(Object.preventExtensions({}));
	});

	var $$8 = _export$1;
	var uncurryThis$6 = functionUncurryThis$1;
	var hiddenKeys = hiddenKeys$b;
	var isObject$3 = isObject$w;
	var hasOwn$1 = hasOwnProperty_1$1;
	var defineProperty$1 = objectDefineProperty$1.f;
	var getOwnPropertyNamesModule = objectGetOwnPropertyNames$1;
	var getOwnPropertyNamesExternalModule = objectGetOwnPropertyNamesExternal;
	var isExtensible$1 = objectIsExtensible;
	var uid = uid$7;
	var FREEZING$1 = freezing;

	var REQUIRED = false;
	var METADATA = uid('meta');
	var id$1 = 0;

	var setMetadata = function (it) {
	  defineProperty$1(it, METADATA, { value: {
	    objectID: 'O' + id$1++, // object ID
	    weakData: {}          // weak collections IDs
	  } });
	};

	var fastKey$1 = function (it, create) {
	  // return a primitive with prefix
	  if (!isObject$3(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
	  if (!hasOwn$1(it, METADATA)) {
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
	  if (!hasOwn$1(it, METADATA)) {
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
	  if (FREEZING$1 && REQUIRED && isExtensible$1(it) && !hasOwn$1(it, METADATA)) setMetadata(it);
	  return it;
	};

	var enable = function () {
	  meta.enable = function () { /* empty */ };
	  REQUIRED = true;
	  var getOwnPropertyNames = getOwnPropertyNamesModule.f;
	  var splice = uncurryThis$6([].splice);
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

	    $$8({ target: 'Object', stat: true, forced: true }, {
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

	var internalMetadataExports = internalMetadata.exports;

	var $$7 = _export$1;
	var global$3 = global$G;
	var InternalMetadataModule$1 = internalMetadataExports;
	var fails$3 = fails$L;
	var createNonEnumerableProperty = createNonEnumerableProperty$f;
	var iterate$3 = iterate$c;
	var anInstance$2 = anInstance$7;
	var isCallable = isCallable$A;
	var isObject$2 = isObject$w;
	var isNullOrUndefined$2 = isNullOrUndefined$c;
	var setToStringTag = setToStringTag$9;
	var defineProperty = objectDefineProperty$1.f;
	var forEach = arrayIteration$1.forEach;
	var DESCRIPTORS$2 = descriptors$1;
	var InternalStateModule$2 = internalState$1;

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

	  if (!DESCRIPTORS$2 || !isCallable(NativeConstructor)
	    || !(IS_WEAK || NativePrototype.forEach && !fails$3(function () { new NativeConstructor().entries().next(); }))
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
	      if (!isNullOrUndefined$2(iterable)) iterate$3(iterable, target[ADDER], { that: target, AS_ENTRIES: IS_MAP });
	    });

	    var Prototype = Constructor.prototype;

	    var getInternalState = internalStateGetterFor$2(CONSTRUCTOR_NAME);

	    forEach(['add', 'clear', 'delete', 'forEach', 'get', 'has', 'set', 'keys', 'values', 'entries'], function (KEY) {
	      var IS_ADDER = KEY === 'add' || KEY === 'set';
	      if (KEY in NativePrototype && !(IS_WEAK && KEY === 'clear')) {
	        createNonEnumerableProperty(Prototype, KEY, function (a, b) {
	          var collection = getInternalState(this).collection;
	          if (!IS_ADDER && IS_WEAK && !isObject$2(a)) return KEY === 'get' ? undefined : false;
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
	  $$7({ global: true, forced: true }, exported);

	  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

	  return Constructor;
	};

	var defineBuiltIn = defineBuiltIn$8;

	var defineBuiltIns$3 = function (target, src, options) {
	  for (var key in src) {
	    if (options && options.unsafe && target[key]) target[key] = src[key];
	    else defineBuiltIn(target, key, src[key], options);
	  } return target;
	};

	var create = objectCreate$1;
	var defineBuiltInAccessor = defineBuiltInAccessor$5;
	var defineBuiltIns$2 = defineBuiltIns$3;
	var bind$2 = functionBindContext$1;
	var anInstance$1 = anInstance$7;
	var isNullOrUndefined$1 = isNullOrUndefined$c;
	var iterate$2 = iterate$c;
	var defineIterator = iteratorDefine$1;
	var createIterResultObject = createIterResultObject$7;
	var setSpecies = setSpecies$4;
	var DESCRIPTORS$1 = descriptors$1;
	var fastKey = internalMetadataExports.fastKey;
	var InternalStateModule$1 = internalState$1;

	var setInternalState$1 = InternalStateModule$1.set;
	var internalStateGetterFor$1 = InternalStateModule$1.getterFor;

	var collectionStrong$2 = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var Constructor = wrapper(function (that, iterable) {
	      anInstance$1(that, Prototype);
	      setInternalState$1(that, {
	        type: CONSTRUCTOR_NAME,
	        index: create(null),
	        first: undefined,
	        last: undefined,
	        size: 0
	      });
	      if (!DESCRIPTORS$1) that.size = 0;
	      if (!isNullOrUndefined$1(iterable)) iterate$2(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
	    });

	    var Prototype = Constructor.prototype;

	    var getInternalState = internalStateGetterFor$1(CONSTRUCTOR_NAME);

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
	        if (DESCRIPTORS$1) state.size++;
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

	    defineBuiltIns$2(Prototype, {
	      // `{ Map, Set }.prototype.clear()` methods
	      // https://tc39.es/ecma262/#sec-map.prototype.clear
	      // https://tc39.es/ecma262/#sec-set.prototype.clear
	      clear: function clear() {
	        var that = this;
	        var state = getInternalState(that);
	        var entry = state.first;
	        while (entry) {
	          entry.removed = true;
	          if (entry.previous) entry.previous = entry.previous.next = undefined;
	          entry = entry.next;
	        }
	        state.first = state.last = undefined;
	        state.index = create(null);
	        if (DESCRIPTORS$1) state.size = 0;
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
	          if (DESCRIPTORS$1) state.size--;
	          else that.size--;
	        } return !!entry;
	      },
	      // `{ Map, Set }.prototype.forEach(callbackfn, thisArg = undefined)` methods
	      // https://tc39.es/ecma262/#sec-map.prototype.foreach
	      // https://tc39.es/ecma262/#sec-set.prototype.foreach
	      forEach: function forEach(callbackfn /* , that = undefined */) {
	        var state = getInternalState(this);
	        var boundFunction = bind$2(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
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

	    defineBuiltIns$2(Prototype, IS_MAP ? {
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
	    if (DESCRIPTORS$1) defineBuiltInAccessor(Prototype, 'size', {
	      configurable: true,
	      get: function () {
	        return getInternalState(this).size;
	      }
	    });
	    return Constructor;
	  },
	  setStrong: function (Constructor, CONSTRUCTOR_NAME, IS_MAP) {
	    var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
	    var getInternalCollectionState = internalStateGetterFor$1(CONSTRUCTOR_NAME);
	    var getInternalIteratorState = internalStateGetterFor$1(ITERATOR_NAME);
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
	      setInternalState$1(this, {
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

	var collection$2 = collection$3;
	var collectionStrong$1 = collectionStrong$2;

	// `Set` constructor
	// https://tc39.es/ecma262/#sec-set-objects
	collection$2('Set', function (init) {
	  return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong$1);

	var path$5 = path$m;

	var set$3 = path$5.Set;

	var parent$a = set$3;


	var set$2 = parent$a;

	var set$1 = set$2;

	var _Set = /*@__PURE__*/getDefaultExportFromCjs(set$1);

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
	  if (definition[2] && fn) {
	    const lets = definition[2](fn(dirty));
	    if ($$scope.dirty === undefined) {
	      return lets;
	    }
	    if (typeof lets === 'object') {
	      const merged = [];
	      const len = Math.max($$scope.dirty.length, lets.length);
	      for (let i = 0; i < len; i += 1) {
	        merged[i] = $$scope.dirty[i] | lets[i];
	      }
	      return merged;
	    }
	    return $$scope.dirty | lets;
	  }
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

	// TODO: Remove from `core-js@4`
	var $$6 = _export$1;
	var uncurryThis$5 = functionUncurryThis$1;

	var $Date = Date;
	var thisTimeValue = uncurryThis$5($Date.prototype.getTime);

	// `Date.now` method
	// https://tc39.es/ecma262/#sec-date.now
	$$6({ target: 'Date', stat: true }, {
	  now: function now() {
	    return thisTimeValue(new $Date());
	  }
	});

	var path$4 = path$m;

	var now$3 = path$4.Date.now;

	var parent$9 = now$3;

	var now$2 = parent$9;

	var now$1 = now$2;

	var _Date$now = /*@__PURE__*/getDefaultExportFromCjs(now$1);

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

	var collection$1 = collection$3;
	var collectionStrong = collectionStrong$2;

	// `Map` constructor
	// https://tc39.es/ecma262/#sec-map-objects
	collection$1('Map', function (init) {
	  return function Map() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong);

	var caller$1 = function (methodName, numArgs) {
	  return numArgs === 1 ? function (object, arg) {
	    return object[methodName](arg);
	  } : function (object, arg1, arg2) {
	    return object[methodName](arg1, arg2);
	  };
	};

	var getBuiltIn = getBuiltIn$f;
	var caller = caller$1;

	var Map$1 = getBuiltIn('Map');

	var mapHelpers = {
	  Map: Map$1,
	  set: caller('set', 2),
	  get: caller('get', 1),
	  has: caller('has', 1),
	  remove: caller('delete', 1),
	  proto: Map$1.prototype
	};

	var $$5 = _export$1;
	var uncurryThis$4 = functionUncurryThis$1;
	var aCallable$1 = aCallable$g;
	var requireObjectCoercible = requireObjectCoercible$e;
	var iterate$1 = iterate$c;
	var MapHelpers = mapHelpers;
	var IS_PURE = isPure;

	var Map = MapHelpers.Map;
	var has = MapHelpers.has;
	var get = MapHelpers.get;
	var set = MapHelpers.set;
	var push$2 = uncurryThis$4([].push);

	// `Map.groupBy` method
	// https://github.com/tc39/proposal-array-grouping
	$$5({ target: 'Map', stat: true, forced: IS_PURE }, {
	  groupBy: function groupBy(items, callbackfn) {
	    requireObjectCoercible(items);
	    aCallable$1(callbackfn);
	    var map = new Map();
	    var k = 0;
	    iterate$1(items, function (value) {
	      var key = callbackfn(value, k++);
	      if (!has(map, key)) set(map, key, [value]);
	      else push$2(get(map, key), value);
	    });
	    return map;
	  }
	});

	var path$3 = path$m;

	var map$2 = path$3.Map;

	var parent$8 = map$2;


	var map$1 = parent$8;

	var map = map$1;

	var _Map = /*@__PURE__*/getDefaultExportFromCjs(map);

	var arraySlice = arraySlice$7;

	var floor = Math.floor;

	var sort$4 = function (array, comparefn) {
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
	    var left = sort$4(arraySlice(array, 0, middle), comparefn);
	    var right = sort$4(arraySlice(array, middle), comparefn);
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

	var arraySort = sort$4;

	var userAgent$1 = engineUserAgent$1;

	var firefox = userAgent$1.match(/firefox\/(\d+)/i);

	var engineFfVersion = !!firefox && +firefox[1];

	var UA = engineUserAgent$1;

	var engineIsIeOrEdge = /MSIE|Trident/.test(UA);

	var userAgent = engineUserAgent$1;

	var webkit = userAgent.match(/AppleWebKit\/(\d+)\./);

	var engineWebkitVersion = !!webkit && +webkit[1];

	var $$4 = _export$1;
	var uncurryThis$3 = functionUncurryThis$1;
	var aCallable = aCallable$g;
	var toObject$2 = toObject$d;
	var lengthOfArrayLike$2 = lengthOfArrayLike$f;
	var deletePropertyOrThrow = deletePropertyOrThrow$2;
	var toString = toString$l;
	var fails$2 = fails$L;
	var internalSort = arraySort;
	var arrayMethodIsStrict = arrayMethodIsStrict$4;
	var FF = engineFfVersion;
	var IE_OR_EDGE = engineIsIeOrEdge;
	var V8 = engineV8Version$1;
	var WEBKIT = engineWebkitVersion;

	var test = [];
	var nativeSort = uncurryThis$3(test.sort);
	var push$1 = uncurryThis$3(test.push);

	// IE8-
	var FAILS_ON_UNDEFINED = fails$2(function () {
	  test.sort(undefined);
	});
	// V8 bug
	var FAILS_ON_NULL = fails$2(function () {
	  test.sort(null);
	});
	// Old WebKit
	var STRICT_METHOD = arrayMethodIsStrict('sort');

	var STABLE_SORT = !fails$2(function () {
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
	$$4({ target: 'Array', proto: true, forced: FORCED }, {
	  sort: function sort(comparefn) {
	    if (comparefn !== undefined) aCallable(comparefn);

	    var array = toObject$2(this);

	    if (STABLE_SORT) return comparefn === undefined ? nativeSort(array) : nativeSort(array, comparefn);

	    var items = [];
	    var arrayLength = lengthOfArrayLike$2(array);
	    var itemsLength, index;

	    for (index = 0; index < arrayLength; index++) {
	      if (index in array) push$1(items, array[index]);
	    }

	    internalSort(items, getSortCompare(comparefn));

	    itemsLength = lengthOfArrayLike$2(items);
	    index = 0;

	    while (index < itemsLength) array[index] = items[index++];
	    while (index < arrayLength) deletePropertyOrThrow(array, index++);

	    return array;
	  }
	});

	var getBuiltInPrototypeMethod$1 = getBuiltInPrototypeMethod$g;

	var sort$3 = getBuiltInPrototypeMethod$1('Array', 'sort');

	var isPrototypeOf$1 = objectIsPrototypeOf$1;
	var method$1 = sort$3;

	var ArrayPrototype$1 = Array.prototype;

	var sort$2 = function (it) {
	  var own = it.sort;
	  return it === ArrayPrototype$1 || (isPrototypeOf$1(ArrayPrototype$1, it) && own === ArrayPrototype$1.sort) ? method$1 : own;
	};

	var parent$7 = sort$2;

	var sort$1 = parent$7;

	var sort = sort$1;

	var _sortInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(sort);

	var anObject$1 = anObject$j;
	var iteratorClose = iteratorClose$4;

	// call something on iterator step with safe closing on error
	var callWithSafeIterationClosing$1 = function (iterator, fn, value, ENTRIES) {
	  try {
	    return ENTRIES ? fn(anObject$1(value)[0], value[1]) : fn(value);
	  } catch (error) {
	    iteratorClose(iterator, 'throw', error);
	  }
	};

	var bind$1 = functionBindContext$1;
	var call = functionCall$1;
	var toObject$1 = toObject$d;
	var callWithSafeIterationClosing = callWithSafeIterationClosing$1;
	var isArrayIteratorMethod = isArrayIteratorMethod$4;
	var isConstructor = isConstructor$7;
	var lengthOfArrayLike$1 = lengthOfArrayLike$f;
	var createProperty = createProperty$7;
	var getIterator = getIterator$4;
	var getIteratorMethod = getIteratorMethod$6;

	var $Array = Array;

	// `Array.from` method implementation
	// https://tc39.es/ecma262/#sec-array.from
	var arrayFrom = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
	  var O = toObject$1(arrayLike);
	  var IS_CONSTRUCTOR = isConstructor(this);
	  var argumentsLength = arguments.length;
	  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
	  var mapping = mapfn !== undefined;
	  if (mapping) mapfn = bind$1(mapfn, argumentsLength > 2 ? arguments[2] : undefined);
	  var iteratorMethod = getIteratorMethod(O);
	  var index = 0;
	  var length, result, step, iterator, next, value;
	  // if the target is not iterable or it's an array with the default iterator - use a simple case
	  if (iteratorMethod && !(this === $Array && isArrayIteratorMethod(iteratorMethod))) {
	    iterator = getIterator(O, iteratorMethod);
	    next = iterator.next;
	    result = IS_CONSTRUCTOR ? new this() : [];
	    for (;!(step = call(next, iterator)).done; index++) {
	      value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
	      createProperty(result, index, value);
	    }
	  } else {
	    length = lengthOfArrayLike$1(O);
	    result = IS_CONSTRUCTOR ? new this(length) : $Array(length);
	    for (;length > index; index++) {
	      value = mapping ? mapfn(O[index], index) : O[index];
	      createProperty(result, index, value);
	    }
	  }
	  result.length = index;
	  return result;
	};

	var $$3 = _export$1;
	var from$3 = arrayFrom;
	var checkCorrectnessOfIteration = checkCorrectnessOfIteration$2;

	var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
	  // eslint-disable-next-line es/no-array-from -- required for testing
	  Array.from(iterable);
	});

	// `Array.from` method
	// https://tc39.es/ecma262/#sec-array.from
	$$3({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
	  from: from$3
	});

	var path$2 = path$m;

	var from$2 = path$2.Array.from;

	var parent$6 = from$2;

	var from$1 = parent$6;

	var from = from$1;

	var _Array$from = /*@__PURE__*/getDefaultExportFromCjs(from);

	var uncurryThis$2 = functionUncurryThis$1;
	var defineBuiltIns$1 = defineBuiltIns$3;
	var getWeakData = internalMetadataExports.getWeakData;
	var anInstance = anInstance$7;
	var anObject = anObject$j;
	var isNullOrUndefined = isNullOrUndefined$c;
	var isObject$1 = isObject$w;
	var iterate = iterate$c;
	var ArrayIterationModule = arrayIteration$1;
	var hasOwn = hasOwnProperty_1$1;
	var InternalStateModule = internalState$1;

	var setInternalState = InternalStateModule.set;
	var internalStateGetterFor = InternalStateModule.getterFor;
	var find = ArrayIterationModule.find;
	var findIndex = ArrayIterationModule.findIndex;
	var splice = uncurryThis$2([].splice);
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

	var collectionWeak$1 = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var Constructor = wrapper(function (that, iterable) {
	      anInstance(that, Prototype);
	      setInternalState(that, {
	        type: CONSTRUCTOR_NAME,
	        id: id++,
	        frozen: undefined
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

	    defineBuiltIns$1(Prototype, {
	      // `{ WeakMap, WeakSet }.prototype.delete(key)` methods
	      // https://tc39.es/ecma262/#sec-weakmap.prototype.delete
	      // https://tc39.es/ecma262/#sec-weakset.prototype.delete
	      'delete': function (key) {
	        var state = getInternalState(this);
	        if (!isObject$1(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state)['delete'](key);
	        return data && hasOwn(data, state.id) && delete data[state.id];
	      },
	      // `{ WeakMap, WeakSet }.prototype.has(key)` methods
	      // https://tc39.es/ecma262/#sec-weakmap.prototype.has
	      // https://tc39.es/ecma262/#sec-weakset.prototype.has
	      has: function has(key) {
	        var state = getInternalState(this);
	        if (!isObject$1(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state).has(key);
	        return data && hasOwn(data, state.id);
	      }
	    });

	    defineBuiltIns$1(Prototype, IS_MAP ? {
	      // `WeakMap.prototype.get(key)` method
	      // https://tc39.es/ecma262/#sec-weakmap.prototype.get
	      get: function get(key) {
	        var state = getInternalState(this);
	        if (isObject$1(key)) {
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

	var FREEZING = freezing;
	var global$2 = global$G;
	var uncurryThis$1 = functionUncurryThis$1;
	var defineBuiltIns = defineBuiltIns$3;
	var InternalMetadataModule = internalMetadataExports;
	var collection = collection$3;
	var collectionWeak = collectionWeak$1;
	var isObject = isObject$w;
	var enforceInternalState = internalState$1.enforce;
	var fails$1 = fails$L;
	var NATIVE_WEAK_MAP = weakMapBasicDetection$1;

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

	var IS_IE11 = !global$2.ActiveXObject && 'ActiveXObject' in global$2;
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
	var nativeSet = uncurryThis$1(WeakMapPrototype.set);

	// Chakra Edge bug: adding frozen arrays to WeakMap unfreeze them
	var hasMSEdgeFreezingBug = function () {
	  return FREEZING && fails$1(function () {
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
	  var nativeDelete = uncurryThis$1(WeakMapPrototype['delete']);
	  var nativeHas = uncurryThis$1(WeakMapPrototype.has);
	  var nativeGet = uncurryThis$1(WeakMapPrototype.get);
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

	var path$1 = path$m;

	var weakMap$2 = path$1.WeakMap;

	var parent$5 = weakMap$2;


	var weakMap$1 = parent$5;

	var weakMap = weakMap$1;

	var _WeakMap = /*@__PURE__*/getDefaultExportFromCjs(weakMap);

	var $$2 = _export$1;
	var global$1 = global$G;

	// `globalThis` object
	// https://tc39.es/ecma262/#sec-globalthis
	$$2({ global: true, forced: global$1.globalThis !== global$1 }, {
	  globalThis: global$1
	});

	var globalThis$6 = global$G;

	var parent$4 = globalThis$6;

	var globalThis$5 = parent$4;

	var parent$3 = globalThis$5;

	var globalThis$4 = parent$3;

	// TODO: remove from `core-js@4`


	var parent$2 = globalThis$4;

	var globalThis$3 = parent$2;

	var globalThis$2 = globalThis$3;

	var globalThis$1 = globalThis$2;

	var _globalThis = /*@__PURE__*/getDefaultExportFromCjs(globalThis$1);

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
	  append( /** @type {Document} */node.head || node, style);
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
	      const event = custom_event( /** @type {string} */type, detail, {
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

	var toObject = toObject$d;
	var toAbsoluteIndex = toAbsoluteIndex$8;
	var lengthOfArrayLike = lengthOfArrayLike$f;

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

	var $$1 = _export$1;
	var fill$4 = arrayFill;

	// `Array.prototype.fill` method
	// https://tc39.es/ecma262/#sec-array.prototype.fill
	$$1({ target: 'Array', proto: true }, {
	  fill: fill$4
	});

	var getBuiltInPrototypeMethod = getBuiltInPrototypeMethod$g;

	var fill$3 = getBuiltInPrototypeMethod('Array', 'fill');

	var isPrototypeOf = objectIsPrototypeOf$1;
	var method = fill$3;

	var ArrayPrototype = Array.prototype;

	var fill$2 = function (it) {
	  var own = it.fill;
	  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.fill) ? method : own;
	};

	var parent$1 = fill$2;

	var fill$1 = parent$1;

	var fill = fill$1;

	var _fillInstanceProperty = /*@__PURE__*/getDefaultExportFromCjs(fill);

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
	  const primary_property_value = _parseFloat$1(style[primary_property]);
	  const secondary_properties = axis === 'y' ? ['top', 'bottom'] : ['left', 'right'];
	  const capitalized_secondary_properties = _mapInstanceProperty(secondary_properties).call(secondary_properties, e => `${e[0].toUpperCase()}${_sliceInstanceProperty$1(e).call(e, 1)}`);
	  const padding_start_value = _parseFloat$1(style[`padding${capitalized_secondary_properties[0]}`]);
	  const padding_end_value = _parseFloat$1(style[`padding${capitalized_secondary_properties[1]}`]);
	  const margin_start_value = _parseFloat$1(style[`margin${capitalized_secondary_properties[0]}`]);
	  const margin_end_value = _parseFloat$1(style[`margin${capitalized_secondary_properties[1]}`]);
	  const border_width_start_value = _parseFloat$1(style[`border${capitalized_secondary_properties[0]}Width`]);
	  const border_width_end_value = _parseFloat$1(style[`border${capitalized_secondary_properties[1]}Width`]);
	  return {
	    delay,
	    duration,
	    easing,
	    css: t => 'overflow: hidden;' + `opacity: ${Math.min(t * 20, 1) * opacity};` + `${primary_property}: ${t * primary_property_value}px;` + `padding-${secondary_properties[0]}: ${t * padding_start_value}px;` + `padding-${secondary_properties[1]}: ${t * padding_end_value}px;` + `margin-${secondary_properties[0]}: ${t * margin_start_value}px;` + `margin-${secondary_properties[1]}: ${t * margin_end_value}px;` + `border-${secondary_properties[0]}-width: ${t * border_width_start_value}px;` + `border-${secondary_properties[1]}-width: ${t * border_width_end_value}px;`
	  };
	}

	/* ../../../../../../../assets/js/frontend/components/Spinner.svelte generated by Svelte v4.2.19 */
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
	      attr(svg, "class", svg_class_value = "bookly-inline bookly-text-gray-200 bookly-animate-spin fill-bookly " + ( /*full_size*/ctx[1] ? 'bookly-absolute bookly-inset-0 bookly-h-full bookly-w-full' : 'bookly-w-8 bookly-h-8'));
	      attr(svg, "viewBox", "0 0 100 101");
	      attr(svg, "fill", "none");
	      attr(svg, "xmlns", "http://www.w3.org/2000/svg");
	      attr(div, "class", "bookly-flex bookly-flex-col bookly-justify-center bookly-items-center bookly-w-full bookly-loading-mark");
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
	      if (dirty & /*full_size*/2 && svg_class_value !== (svg_class_value = "bookly-inline bookly-text-gray-200 bookly-animate-spin fill-bookly " + ( /*full_size*/ctx[1] ? 'bookly-absolute bookly-inset-0 bookly-h-full bookly-w-full' : 'bookly-w-8 bookly-h-8'))) {
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

	/* ../../../../../../../assets/js/frontend/components/Button.svelte generated by Svelte v4.2.19 */
	function create_else_block_1(ctx) {
	  let button;
	  let t;
	  let span;
	  let button_class_value;
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
	      toggle_class(span, "bookly-opacity-0", /*loading*/ctx[3]);
	      attr(button, "type", "button");
	      attr(button, "title", /*title*/ctx[2]);
	      attr(button, "class", button_class_value = "" + ( /*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly-drop-shadow-none bookly-box-border"));
	      attr(button, "style", /*styles*/ctx[4]);
	      button.disabled = /*disabled*/ctx[0];
	      toggle_class(button, "pointer-events-none", /*disabled*/ctx[0]);
	      toggle_class(button, "bookly-opacity-50", /*disabled*/ctx[0]);
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
	        dispose = listen(button, "click", stop_propagation( /*click_handler_1*/ctx[20]));
	        mounted = true;
	      }
	    },
	    p(ctx, dirty) {
	      if ( /*loading*/ctx[3]) {
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
	          update_slot_base(default_slot, default_slot_template, ctx, /*$$scope*/ctx[16], !current ? get_all_dirty_from_scope( /*$$scope*/ctx[16]) : get_slot_changes(default_slot_template, /*$$scope*/ctx[16], dirty, null), null);
	        }
	      }
	      if (!current || dirty & /*loading*/8) {
	        toggle_class(span, "bookly-opacity-0", /*loading*/ctx[3]);
	      }
	      if (!current || dirty & /*title*/4) {
	        attr(button, "title", /*title*/ctx[2]);
	      }
	      if (!current || dirty & /*classes, buttonClasses*/96 && button_class_value !== (button_class_value = "" + ( /*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly-drop-shadow-none bookly-box-border"))) {
	        attr(button, "class", button_class_value);
	      }
	      if (!current || dirty & /*styles*/16) {
	        attr(button, "style", /*styles*/ctx[4]);
	      }
	      if (!current || dirty & /*disabled*/1) {
	        button.disabled = /*disabled*/ctx[0];
	      }
	      if (!current || dirty & /*classes, buttonClasses, disabled*/97) {
	        toggle_class(button, "pointer-events-none", /*disabled*/ctx[0]);
	      }
	      if (!current || dirty & /*classes, buttonClasses, disabled*/97) {
	        toggle_class(button, "bookly-opacity-50", /*disabled*/ctx[0]);
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

	// (193:8) {#if loading}
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
	      attr(span, "class", "bookly-absolute bookly-inset-1");
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
	      toggle_class(span, "bookly-opacity-0", /*loading*/ctx[3]);
	      attr(div, "title", /*title*/ctx[2]);
	      attr(div, "class", div_class_value = "" + ( /*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly-drop-shadow-none bookly-box-border bookly-text-center bookly-flex bookly-items-center bookly-justify-center pointer-events-none bookly-opacity-50 bookly-pointer-events-none"));
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
	      if ( /*loading*/ctx[3]) {
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
	          update_slot_base(default_slot, default_slot_template, ctx, /*$$scope*/ctx[16], !current ? get_all_dirty_from_scope( /*$$scope*/ctx[16]) : get_slot_changes(default_slot_template, /*$$scope*/ctx[16], dirty, null), null);
	        }
	      }
	      if (!current || dirty & /*loading*/8) {
	        toggle_class(span, "bookly-opacity-0", /*loading*/ctx[3]);
	      }
	      if (!current || dirty & /*title*/4) {
	        attr(div, "title", /*title*/ctx[2]);
	      }
	      if (!current || dirty & /*classes, buttonClasses*/96 && div_class_value !== (div_class_value = "" + ( /*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly-drop-shadow-none bookly-box-border bookly-text-center bookly-flex bookly-items-center bookly-justify-center pointer-events-none bookly-opacity-50 bookly-pointer-events-none"))) {
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
	      toggle_class(span, "bookly-opacity-0", /*loading*/ctx[3]);
	      attr(div, "title", /*title*/ctx[2]);
	      attr(div, "class", div_class_value = "" + ( /*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly-drop-shadow-none bookly-box-border bookly-text-center bookly-flex bookly-items-center bookly-justify-center"));
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
	        dispose = [listen(div, "click", stop_propagation( /*click_handler*/ctx[18])), listen(div, "keypress", stop_propagation( /*keypress_handler*/ctx[19]))];
	        mounted = true;
	      }
	    },
	    p(ctx, dirty) {
	      if ( /*loading*/ctx[3]) {
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
	          update_slot_base(default_slot, default_slot_template, ctx, /*$$scope*/ctx[16], !current ? get_all_dirty_from_scope( /*$$scope*/ctx[16]) : get_slot_changes(default_slot_template, /*$$scope*/ctx[16], dirty, null), null);
	        }
	      }
	      if (!current || dirty & /*loading*/8) {
	        toggle_class(span, "bookly-opacity-0", /*loading*/ctx[3]);
	      }
	      if (!current || dirty & /*title*/4) {
	        attr(div, "title", /*title*/ctx[2]);
	      }
	      if (!current || dirty & /*classes, buttonClasses*/96 && div_class_value !== (div_class_value = "" + ( /*classes*/ctx[5] + " " + /*buttonClasses*/ctx[6] + " bookly-drop-shadow-none bookly-box-border bookly-text-center bookly-flex bookly-items-center bookly-justify-center"))) {
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
	      attr(span, "class", "bookly-absolute bookly-inset-1");
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
	      attr(span, "class", "bookly-absolute bookly-inset-1");
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
	    if ( /*container*/ctx[1] === 'div') return 0;
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
	            $$invalidate(6, buttonClasses = 'bookly-text-slate-600 bookly-bg-white bookly-border-slate-600');
	            $$invalidate(15, hover = 'hover:bookly-text-slate-50 hover:bookly-bg-slate-400 hover:bookly-border-slate-400');
	            break;
	          case 'white':
	            $$invalidate(6, buttonClasses = 'bookly-text-slate-600 bookly-bg-white bookly-border-slate-600');
	            $$invalidate(15, hover = 'hover:bookly-text-slate-50 hover:bookly-bg-gray-400 hover:bookly-border-gray-400');
	            break;
	          case 'transparent':
	            $$invalidate(6, buttonClasses = (color ? color : 'bookly-text-slate-600') + ' bookly-bg-transparent bookly-border-slate-600');
	            $$invalidate(15, hover = 'hover:bookly-text-slate-50 hover:bookly-bg-gray-400 hover:bookly-border-gray-400');
	            break;
	          case 'bookly':
	            $$invalidate(6, buttonClasses = 'text-bookly bookly-bg-white border-bookly');
	            $$invalidate(15, hover = 'hover:bookly-text-white hover:bg-bookly hover:bookly-opacity-80 hover:border-bookly');
	            break;
	          case 'bookly-active':
	            $$invalidate(6, buttonClasses = 'bg-bookly bookly-text-white border-bookly');
	            $$invalidate(15, hover = 'hover:bookly-text-slate-100 hover:bg-bookly hover:border-bookly');
	            break;
	          case 'bookly-gray':
	            $$invalidate(6, buttonClasses = 'text-bookly bookly-bg-gray-200 border-bookly');
	            $$invalidate(15, hover = 'hover:bookly-text-white hover:bg-bookly hover:border-bookly');
	            break;
	          case 'link':
	            $$invalidate(6, buttonClasses = 'bookly-border-none bookly-rounded-none bookly-p-0 ' + (disabled ? 'bookly-text-gray-600' : 'text-bookly'));
	            $$invalidate(15, hover = 'hover:bookly-text-gray-600');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            $$invalidate(12, size = 'link');
	            break;
	          case 'calendar':
	            $$invalidate(6, buttonClasses = '');
	            $$invalidate(15, hover = 'hover:bookly-opacity-80');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          case 'calendar-normal':
	            $$invalidate(6, buttonClasses = 'text-bookly border-bookly bookly-rounded-none bookly-m-0 ' + (disabled ? 'bookly-bg-slate-50 hover:text-bookly' : 'bookly-bg-white'));
	            $$invalidate(15, hover = 'hover:bg-bookly hover:border-bookly ' + (disabled ? 'hover:text-bookly' : 'hover:bookly-text-white'));
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          case 'calendar-active':
	            $$invalidate(6, buttonClasses = 'bg-bookly bookly-text-white border-bookly bookly-rounded-none bookly-m-0');
	            $$invalidate(15, hover = 'hover:bookly-text-slate-200');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          case 'calendar-inactive':
	            $$invalidate(6, buttonClasses = 'bookly-text-gray-400 border-bookly bookly-rounded-none bookly-m-0 ' + (disabled ? 'bookly-bg-slate-50' : 'bookly-bg-white'));
	            $$invalidate(15, hover = 'hover:bookly-text-white hover:bookly-bg-gray-400 hover:border-bookly');
	            $$invalidate(7, rounded = false);
	            $$invalidate(8, bordered = false);
	            $$invalidate(9, paddings = false);
	            $$invalidate(10, margins = false);
	            $$invalidate(11, shadows = false);
	            break;
	          default:
	            $$invalidate(6, buttonClasses = 'bookly-text-black bookly-bg-gray-100 bookly-border-gray');
	            $$invalidate(15, hover = 'hover:bookly-text-slate-50 hover:bookly-bg-gray-400');
	            break;
	        }
	        if (!shadows) {
	          $$invalidate(6, buttonClasses += ' bookly-shadow-none');
	        }
	        if (!disabled && !loading && shadows) {
	          $$invalidate(6, buttonClasses += ' active:bookly-shadow-md');
	        }
	        if (!disabled && !loading) {
	          $$invalidate(6, buttonClasses += ' ' + hover);
	        }
	        if (rounded) {
	          $$invalidate(6, buttonClasses += ' bookly-rounded');
	        }
	        if (bordered) {
	          $$invalidate(6, buttonClasses += ' bookly-border bookly-border-solid');
	        }
	        if (paddings) {
	          switch (size) {
	            case 'lg':
	              $$invalidate(6, buttonClasses += ' bookly-px-5 bookly-py-0');
	              break;
	            default:
	              $$invalidate(6, buttonClasses += ' bookly-px-4 bookly-py-0');
	              break;
	          }
	        }
	        if (margins) {
	          $$invalidate(6, buttonClasses += ' bookly-ms-2 bookly-my-0 bookly-me-0');
	        }
	        switch (size) {
	          case 'link':
	          case 'custom':
	            break;
	          case 'lg':
	            $$invalidate(6, buttonClasses += ' bookly-text-xl bookly-h-14');
	            break;
	          default:
	            $$invalidate(6, buttonClasses += ' bookly-text-lg bookly-h-10');
	            break;
	        }
	        if (margins) {
	          $$invalidate(6, buttonClasses += ' bookly-relative');
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
	  child_ctx[45] = list[i];
	  child_ctx[59] = i;
	  const constants_0 = /*year*/child_ctx[1] + /*_year*/child_ctx[59] - 4;
	  child_ctx[57] = constants_0;
	  const constants_1 = new Date( /*__year*/child_ctx[57], 12, 0);
	  child_ctx[53] = constants_1;
	  const constants_2 = /*limits*/child_ctx[0] && ( /*limits*/child_ctx[0].hasOwnProperty('start') && /*limits*/child_ctx[0].start.getFullYear() > /*_date*/child_ctx[53].getFullYear() || /*limits*/child_ctx[0].hasOwnProperty('end') && /*limits*/child_ctx[0].end.getFullYear() < /*_date*/child_ctx[53].getFullYear());
	  child_ctx[49] = constants_2;
	  return child_ctx;
	}
	function get_each_context_3(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[45] = list[i];
	  child_ctx[56] = i;
	  const constants_0 = new Date( /*year*/child_ctx[1], /*_month*/child_ctx[56] + 1, 0);
	  child_ctx[53] = constants_0;
	  const constants_1 = new Date( /*year*/child_ctx[1], /*_month*/child_ctx[56], 1);
	  child_ctx[54] = constants_1;
	  const constants_2 = /*limits*/child_ctx[0] && ( /*limits*/child_ctx[0].hasOwnProperty('start') && /*limits*/child_ctx[0].start > /*_date*/child_ctx[53] || /*limits*/child_ctx[0].hasOwnProperty('end') && /*limits*/child_ctx[0].end < /*_end_date*/child_ctx[54]);
	  child_ctx[49] = constants_2;
	  return child_ctx;
	}
	function get_each_context$2(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[45] = list[i];
	  child_ctx[47] = i;
	  return child_ctx;
	}
	function get_each_context_1(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[45] = list[i];
	  child_ctx[51] = i;
	  const constants_0 = /*days*/child_ctx[9][/*i*/child_ctx[47] * 7 + /*j*/child_ctx[51]];
	  child_ctx[48] = constants_0;
	  const constants_1 = /*_day*/child_ctx[48].disabled;
	  child_ctx[49] = constants_1;
	  return child_ctx;
	}
	function get_each_context_2(ctx, list, i) {
	  const child_ctx = _sliceInstanceProperty(ctx).call(ctx);
	  child_ctx[45] = list[i];
	  child_ctx[47] = i;
	  return child_ctx;
	}

	// (252:0) {#if show}
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
	  let if_block0 = /*loading*/ctx[3] && create_if_block_3$1();
	  button0 = new Button({
	    props: {
	      class: "bookly-grow-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-left-button-mark bookly-m-0 bookly-px-4 bookly-text-xl bookly-shadow-none " + /*controlButtonClasses*/ctx[18],
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
	  button0.$on("click", /*onClickLeft*/ctx[22]);
	  button0.$on("keypress", /*onClickLeft*/ctx[22]);
	  button1 = new Button({
	    props: {
	      class: "bookly-grow bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-middle-button-mark bookly-m-0 bookly-text-lg bookly-shadow-none " + /*controlButtonClasses*/ctx[18],
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
	  button1.$on("click", /*changeView*/ctx[21]);
	  button1.$on("keypress", /*changeView*/ctx[21]);
	  button2 = new Button({
	    props: {
	      class: "bookly-grow-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-right-button-mark bookly-m-0 bookly-px-4 bookly-text-xl bookly-shadow-none " + /*controlButtonClasses*/ctx[18],
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
	  button2.$on("click", /*onClickRight*/ctx[23]);
	  button2.$on("keypress", /*onClickRight*/ctx[23]);
	  const if_block_creators = [create_if_block_1$2, create_if_block_2$2, create_else_block];
	  const if_blocks = [];
	  function select_block_type(ctx, dirty) {
	    if ( /*view*/ctx[8] === 'calendar') return 0;
	    if ( /*view*/ctx[8] === 'month') return 1;
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
	      attr(div0, "class", "bookly-flex bookly-text-gray-400");
	      attr(div0, "role", "group");
	      attr(div1, "class", div1_class_value = "bookly-w-full bookly-border-b " + /*borderColor*/ctx[13] + " bookly-mb-0.5 bookly-pb-0.5 bookly-calendar-controls-mark" + " svelte-trnmqx");
	      attr(div2, "class", "bookly-w-full");
	      attr(div3, "class", div3_class_value = "bookly-w-full bookly-min-h-full bookly-p-0.5 bookly-relative " + /*bgColor*/ctx[11] + " " + /*borderColor*/ctx[13] + " bookly-rounded " + ( /*border*/ctx[6] ? 'bookly-border bookly-p-0.5 bookly-rounded' : '') + " svelte-trnmqx");
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
	      ctx[42](div3);
	      current = true;
	    },
	    p(ctx, dirty) {
	      if ( /*loading*/ctx[3]) {
	        if (if_block0) {
	          if (dirty[0] & /*loading*/8) {
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
	      if (dirty[0] & /*controlButtonClasses*/262144) button0_changes.class = "bookly-grow-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-left-button-mark bookly-m-0 bookly-px-4 bookly-text-xl bookly-shadow-none " + /*controlButtonClasses*/ctx[18];
	      if (dirty[0] & /*loading, limits, month, year*/15) button0_changes.disabled = /*loading*/ctx[3] || /*limits*/ctx[0] && /*limits*/ctx[0].hasOwnProperty('start') && /*month*/ctx[2] <= /*limits*/ctx[0].start.getMonth() && /*year*/ctx[1] === /*limits*/ctx[0].start.getFullYear();
	      if (dirty[0] & /*rtl*/1024 | dirty[1] & /*$$scope*/536870912) {
	        button0_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button0.$set(button0_changes);
	      const button1_changes = {};
	      if (dirty[0] & /*controlButtonClasses*/262144) button1_changes.class = "bookly-grow bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-middle-button-mark bookly-m-0 bookly-text-lg bookly-shadow-none " + /*controlButtonClasses*/ctx[18];
	      if (dirty[0] & /*title*/524288 | dirty[1] & /*$$scope*/536870912) {
	        button1_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button1.$set(button1_changes);
	      const button2_changes = {};
	      if (dirty[0] & /*controlButtonClasses*/262144) button2_changes.class = "bookly-grow-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-right-button-mark bookly-m-0 bookly-px-4 bookly-text-xl bookly-shadow-none " + /*controlButtonClasses*/ctx[18];
	      if (dirty[0] & /*loading, limits, month, year*/15) button2_changes.disabled = /*loading*/ctx[3] || /*limits*/ctx[0] && /*limits*/ctx[0].hasOwnProperty('end') && /*month*/ctx[2] >= /*limits*/ctx[0].end.getMonth() && /*year*/ctx[1] === /*limits*/ctx[0].end.getFullYear();
	      if (dirty[0] & /*rtl*/1024 | dirty[1] & /*$$scope*/536870912) {
	        button2_changes.$$scope = {
	          dirty,
	          ctx
	        };
	      }
	      button2.$set(button2_changes);
	      if (!current || dirty[0] & /*borderColor*/8192 && div1_class_value !== (div1_class_value = "bookly-w-full bookly-border-b " + /*borderColor*/ctx[13] + " bookly-mb-0.5 bookly-pb-0.5 bookly-calendar-controls-mark" + " svelte-trnmqx")) {
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
	      if (!current || dirty[0] & /*bgColor, borderColor, border*/10304 && div3_class_value !== (div3_class_value = "bookly-w-full bookly-min-h-full bookly-p-0.5 bookly-relative " + /*bgColor*/ctx[11] + " " + /*borderColor*/ctx[13] + " bookly-rounded " + ( /*border*/ctx[6] ? 'bookly-border bookly-p-0.5 bookly-rounded' : '') + " svelte-trnmqx")) {
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
	      ctx[42](null);
	    }
	  };
	}

	// (254:8) {#if loading}
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

	// (261:16) <Button                         class="bookly-grow-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-left-button-mark bookly-m-0 bookly-px-4 bookly-text-xl bookly-shadow-none {controlButtonClasses}"                         type="calendar"                         bordered={false}                         rounded={false}                         margins={false}                         disabled={loading || (limits && limits.hasOwnProperty('start') && month <= limits.start.getMonth() && year === limits.start.getFullYear())}                         on:click={onClickLeft}                         on:keypress={onClickLeft}                         container="div"                 >
	function create_default_slot_5(ctx) {
	  let i;
	  return {
	    c() {
	      i = element("i");
	      attr(i, "class", "bi");
	      toggle_class(i, "bi-chevron-left", ! /*rtl*/ctx[10]);
	      toggle_class(i, "bi-chevron-right", /*rtl*/ctx[10]);
	    },
	    m(target, anchor) {
	      insert(target, i, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*rtl*/1024) {
	        toggle_class(i, "bi-chevron-left", ! /*rtl*/ctx[10]);
	      }
	      if (dirty[0] & /*rtl*/1024) {
	        toggle_class(i, "bi-chevron-right", /*rtl*/ctx[10]);
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(i);
	      }
	    }
	  };
	}

	// (274:16) <Button                         class="bookly-grow bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-middle-button-mark bookly-m-0 bookly-text-lg bookly-shadow-none {controlButtonClasses}"                         type="calendar"                         bordered={false}                         rounded={false}                         margins={false}                         on:click={changeView}                         on:keypress={changeView}                         container="div"                 >
	function create_default_slot_4(ctx) {
	  let t;
	  return {
	    c() {
	      t = text( /*title*/ctx[19]);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*title*/524288) set_data(t, /*title*/ctx[19]);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (286:16) <Button                         class="bookly-grow-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-calendar-right-button-mark bookly-m-0 bookly-px-4 bookly-text-xl bookly-shadow-none {controlButtonClasses}"                         type="calendar"                         bordered={false}                         rounded={false}                         margins={false}                         disabled={loading || (limits && limits.hasOwnProperty('end') && month >= limits.end.getMonth() && year === limits.end.getFullYear())}                         on:click={onClickRight}                         on:keypress={onClickRight}                         container="div"                 >
	function create_default_slot_3(ctx) {
	  let i;
	  return {
	    c() {
	      i = element("i");
	      attr(i, "class", "bi");
	      toggle_class(i, "bi-chevron-left", /*rtl*/ctx[10]);
	      toggle_class(i, "bi-chevron-right", ! /*rtl*/ctx[10]);
	    },
	    m(target, anchor) {
	      insert(target, i, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*rtl*/1024) {
	        toggle_class(i, "bi-chevron-left", /*rtl*/ctx[10]);
	      }
	      if (dirty[0] & /*rtl*/1024) {
	        toggle_class(i, "bi-chevron-right", ! /*rtl*/ctx[10]);
	      }
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(i);
	      }
	    }
	  };
	}

	// (356:12) {:else}
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
	      attr(div, "class", "bookly-w-full bookly-text-center bookly-grid bookly-grid-cols-3 bookly-calendar-years-mark");
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
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses, view*/82179) {
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

	// (331:39) 
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
	      attr(div, "class", "bookly-w-full bookly-text-center bookly-grid bookly-grid-cols-4 bookly-calendar-months-mark");
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
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses, month, dispatch, view, datePicker*/1130775) {
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

	// (300:12) {#if view === 'calendar'}
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
	    length: _parseInt$1( /*days*/ctx[9].length / 7)
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
	      attr(div0, "class", div0_class_value = "bookly-flex bookly-flex-row fw-bold bookly-text-center bookly-text-muted bookly-w-full bookly-border-b " + /*borderColor*/ctx[13] + " bookly-mb-0.5 bookly-py-2 bookly-max-w-full" + " svelte-trnmqx");
	      attr(div1, "class", "bookly-relative bookly-rounded");
	      attr(div2, "class", "bookly-w-full bookly-calendar-dates-mark");
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
	      if (dirty[0] & /*textColor, datePicker*/4112) {
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
	      if (!current || dirty[0] & /*borderColor*/8192 && div0_class_value !== (div0_class_value = "bookly-flex bookly-flex-row fw-bold bookly-text-center bookly-text-muted bookly-w-full bookly-border-b " + /*borderColor*/ctx[13] + " bookly-mb-0.5 bookly-py-2 bookly-max-w-full" + " svelte-trnmqx")) {
	        attr(div0, "class", div0_class_value);
	      }
	      if (dirty[0] & /*days, disabledButtonClasses, activeButtonClasses, buttonClasses, otherMonthButtonClasses, onClickDate*/17023488) {
	        each_value = ensure_array_like({
	          length: _parseInt$1( /*days*/ctx[9].length / 7)
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

	// (363:28) <Button                                     type="calendar"                                     bordered={false}                                     rounded={false}                                     paddings={false}                                     margins={false}                                     class="bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-px-2 bookly-py-0 bookly-m-0 bookly-text-xl bookly-h-16 {_disabled ? disabledButtonClasses : ''} {buttonClasses}"                                     on:click={() => {year = __year; view='month'}}                                     on:keypress={() => {year = __year; view='month'}}                                     disabled={_disabled}                                     container="div"                                     size="custom"                             >
	function create_default_slot_2(ctx) {
	  let t_value = /*__year*/ctx[57] + "";
	  let t;
	  return {
	    c() {
	      t = text(t_value);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*year*/2 && t_value !== (t_value = /*__year*/ctx[57] + "")) set_data(t, t_value);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (358:20) {#each {length: 9} as _,_year}
	function create_each_block_4(ctx) {
	  let div;
	  let button;
	  let t;
	  let current;
	  function click_handler_2() {
	    return /*click_handler_2*/ctx[40]( /*__year*/ctx[57]);
	  }
	  function keypress_handler_2() {
	    return /*keypress_handler_2*/ctx[41]( /*__year*/ctx[57]);
	  }
	  button = new Button({
	    props: {
	      type: "calendar",
	      bordered: false,
	      rounded: false,
	      paddings: false,
	      margins: false,
	      class: "bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-px-2 bookly-py-0 bookly-m-0 bookly-text-xl bookly-h-16 " + ( /*_disabled*/ctx[49] ? /*disabledButtonClasses*/ctx[16] : '') + " " + /*buttonClasses*/ctx[14],
	      disabled: /*_disabled*/ctx[49],
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
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses*/81923) button_changes.class = "bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-px-2 bookly-py-0 bookly-m-0 bookly-text-xl bookly-h-16 " + ( /*_disabled*/ctx[49] ? /*disabledButtonClasses*/ctx[16] : '') + " " + /*buttonClasses*/ctx[14];
	      if (dirty[0] & /*limits, year*/3) button_changes.disabled = /*_disabled*/ctx[49];
	      if (dirty[0] & /*year*/2 | dirty[1] & /*$$scope*/536870912) {
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

	// (338:28) <Button                                     type="calendar"                                     class="bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-px-2 bookly-py-0 bookly-m-0 bookly-text-xl bookly-h-16 {_disabled ? disabledButtonClasses : ''} {buttonClasses}"                                     bordered={false}                                     rounded={false}                                     margins={false}                                     paddings={false}                                     on:click={() => {month = _month; dispatch('month-change'); view='calendar'}}                                     on:keypress={() => {month = _month; dispatch('month-change'); view='calendar'}}                                     disabled={_disabled}                                     container="div"                                     size="custom"                             >
	function create_default_slot_1(ctx) {
	  let t_value = /*datePicker*/ctx[4].monthNamesShort[/*_month*/ctx[56]] + "";
	  let t;
	  return {
	    c() {
	      t = text(t_value);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*datePicker*/16 && t_value !== (t_value = /*datePicker*/ctx[4].monthNamesShort[/*_month*/ctx[56]] + "")) set_data(t, t_value);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (333:20) {#each {length: 12} as _,_month}
	function create_each_block_3(ctx) {
	  let div;
	  let button;
	  let t;
	  let current;
	  function click_handler_1() {
	    return /*click_handler_1*/ctx[38]( /*_month*/ctx[56]);
	  }
	  function keypress_handler_1() {
	    return /*keypress_handler_1*/ctx[39]( /*_month*/ctx[56]);
	  }
	  button = new Button({
	    props: {
	      type: "calendar",
	      class: "bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-px-2 bookly-py-0 bookly-m-0 bookly-text-xl bookly-h-16 " + ( /*_disabled*/ctx[49] ? /*disabledButtonClasses*/ctx[16] : '') + " " + /*buttonClasses*/ctx[14],
	      bordered: false,
	      rounded: false,
	      margins: false,
	      paddings: false,
	      disabled: /*_disabled*/ctx[49],
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
	      if (dirty[0] & /*limits, year, disabledButtonClasses, buttonClasses*/81923) button_changes.class = "bookly-border-none focus:bookly-border-none focus:bookly-outline-none bookly-leading-normal bookly-px-2 bookly-py-0 bookly-m-0 bookly-text-xl bookly-h-16 " + ( /*_disabled*/ctx[49] ? /*disabledButtonClasses*/ctx[16] : '') + " " + /*buttonClasses*/ctx[14];
	      if (dirty[0] & /*limits, year*/3) button_changes.disabled = /*_disabled*/ctx[49];
	      if (dirty[0] & /*datePicker*/16 | dirty[1] & /*$$scope*/536870912) {
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

	// (303:24) {#each {length: 7} as _, i}
	function create_each_block_2(ctx) {
	  let div;
	  let t_value = /*datePicker*/ctx[4].dayNamesShort[( /*i*/ctx[47] + /*datePicker*/ctx[4].firstDay) % 7] + "";
	  let t;
	  let div_class_value;
	  return {
	    c() {
	      div = element("div");
	      t = text(t_value);
	      attr(div, "class", div_class_value = "bookly-flex-1 bookly-px-0 bookly-overflow-hidden bookly-text-sm " + /*textColor*/ctx[12] + " bookly-cursor-default" + " svelte-trnmqx");
	    },
	    m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, t);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*datePicker*/16 && t_value !== (t_value = /*datePicker*/ctx[4].dayNamesShort[( /*i*/ctx[47] + /*datePicker*/ctx[4].firstDay) % 7] + "")) set_data(t, t_value);
	      if (dirty[0] & /*textColor*/4096 && div_class_value !== (div_class_value = "bookly-flex-1 bookly-px-0 bookly-overflow-hidden bookly-text-sm " + /*textColor*/ctx[12] + " bookly-cursor-default" + " svelte-trnmqx")) {
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

	// (313:36) <Button                                             type='calendar'                                             class="bookly-text-sm bookly-h-10 bookly-leading-4 bookly-shadow-none bookly-flex-1 bookly-py-2 bookly-px-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none {_disabled ? disabledButtonClasses : ''} {_day.active ? activeButtonClasses : (_day.current ? buttonClasses : otherMonthButtonClasses)} {_day.current ? 'bookly-calendar-current-month-mark' : ''}"                                             bordered={false}                                             margins={false}                                             on:click={() => !_disabled && onClickDate(_day)}                                             on:keypress={() => !_disabled && onClickDate(_day)}                                             disabled={_disabled}                                             container="div"                                             size="custom"                                     >
	function create_default_slot(ctx) {
	  let t_value = /*_day*/ctx[48].title + "";
	  let t;
	  return {
	    c() {
	      t = text(t_value);
	    },
	    m(target, anchor) {
	      insert(target, t, anchor);
	    },
	    p(ctx, dirty) {
	      if (dirty[0] & /*days*/512 && t_value !== (t_value = /*_day*/ctx[48].title + "")) set_data(t, t_value);
	    },
	    d(detaching) {
	      if (detaching) {
	        detach(t);
	      }
	    }
	  };
	}

	// (310:32) {#each {length: 7} as _, j}
	function create_each_block_1(ctx) {
	  let button;
	  let current;
	  function click_handler() {
	    return /*click_handler*/ctx[36]( /*_disabled*/ctx[49], /*_day*/ctx[48]);
	  }
	  function keypress_handler() {
	    return /*keypress_handler*/ctx[37]( /*_disabled*/ctx[49], /*_day*/ctx[48]);
	  }
	  button = new Button({
	    props: {
	      type: "calendar",
	      class: "bookly-text-sm bookly-h-10 bookly-leading-4 bookly-shadow-none bookly-flex-1 bookly-py-2 bookly-px-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none " + ( /*_disabled*/ctx[49] ? /*disabledButtonClasses*/ctx[16] : '') + " " + ( /*_day*/ctx[48].active ? /*activeButtonClasses*/ctx[15] : /*_day*/ctx[48].current ? /*buttonClasses*/ctx[14] : /*otherMonthButtonClasses*/ctx[17]) + " " + ( /*_day*/ctx[48].current ? 'bookly-calendar-current-month-mark' : ''),
	      bordered: false,
	      margins: false,
	      disabled: /*_disabled*/ctx[49],
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
	      if (dirty[0] & /*days, disabledButtonClasses, activeButtonClasses, buttonClasses, otherMonthButtonClasses*/246272) button_changes.class = "bookly-text-sm bookly-h-10 bookly-leading-4 bookly-shadow-none bookly-flex-1 bookly-py-2 bookly-px-0 bookly-border-none focus:bookly-border-none focus:bookly-outline-none " + ( /*_disabled*/ctx[49] ? /*disabledButtonClasses*/ctx[16] : '') + " " + ( /*_day*/ctx[48].active ? /*activeButtonClasses*/ctx[15] : /*_day*/ctx[48].current ? /*buttonClasses*/ctx[14] : /*otherMonthButtonClasses*/ctx[17]) + " " + ( /*_day*/ctx[48].current ? 'bookly-calendar-current-month-mark' : '');
	      if (dirty[0] & /*days*/512) button_changes.disabled = /*_disabled*/ctx[49];
	      if (dirty[0] & /*days*/512 | dirty[1] & /*$$scope*/536870912) {
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

	// (308:24) {#each {length: parseInt(days.length / 7)} as _, i}
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
	      attr(div, "class", "bookly-flex bookly-w-full");
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
	      if (dirty[0] & /*days, disabledButtonClasses, activeButtonClasses, buttonClasses, otherMonthButtonClasses, onClickDate*/17023488) {
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
	  let if_block = /*show*/ctx[5] && create_if_block$3(ctx);
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
	      if ( /*show*/ctx[5]) {
	        if (if_block) {
	          if_block.p(ctx, dirty);
	          if (dirty[0] & /*show*/32) {
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
	      textColor = 'bookly-text-white';
	      borderColor = 'border-bookly';
	      buttonClasses = 'bookly-text-white bg-bookly-not-hover hover:bookly-bg-white hover:text-bookly';
	      otherMonthButtonClasses = 'bookly-text-slate-300 bg-bookly-not-hover hover:bookly-bg-white hover:text-bookly';
	      activeButtonClasses = 'bookly-bg-white text-bookly hover:text-bookly';
	      disabledButtonClasses = '';
	      controlButtonClasses = 'bookly-text-white bg-bookly-not-hover hover:bookly-bg-white hover:text-bookly';
	      break;
	    default:
	      bgColor = 'bookly-bg-white';
	      textColor = 'bookly-text-slate-600 hover:bookly-text-slate-600';
	      borderColor = 'bookly-border-slate-100';
	      buttonClasses = 'text-bookly hover:bg-bookly hover:bookly-text-white';
	      otherMonthButtonClasses = 'bookly-text-slate-400 hover:bg-bookly hover:bookly-text-white';
	      activeButtonClasses = 'bookly-text-white bg-bookly';
	      disabledButtonClasses = 'bookly-bg-slate-100';
	      controlButtonClasses = 'bookly-text-slate-600 hover:bg-bookly hover:bookly-text-white';
	      break;
	  }
	  function forceLoadSchedule() {
	    $$invalidate(27, holidays = []);
	    $$invalidate(28, loadedMonths = []);
	    $$invalidate(3, loading = true);
	  }
	  if (maxDays) {
	    limits.end = new Date();
	    limits.end.setDate(limits.end.getDate() + _parseInt$1(maxDays));
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
	        $$invalidate(8, view = 'month');
	        break;
	      case 'month':
	        $$invalidate(8, view = 'year');
	        break;
	      case 'year':
	        $$invalidate(8, view = 'calendar');
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
	        $$invalidate(28, loadedMonths = [...new _Set$1([...loadedMonths, ...(response?.data.parsed_months || [])])]);
	        $$invalidate(27, holidays = [...new _Set$1([...holidays, ...(response?.data.holidays || [])])]);
	        if (date === null) {
	          let firstDate = new Date();
	          while (_includesInstanceProperty(holidays).call(holidays, $$invalidate(25, date = formatDate(firstDate)))) {
	            firstDate.setDate(firstDate.getDate() + 1);
	          }
	          $$invalidate(25, date = formatDate(firstDate));
	          $$invalidate(2, month = firstDate.getMonth());
	          $$invalidate(1, year = firstDate.getFullYear());
	          dispatch('change');
	        }
	      }).catch(() => {
	        if (date === null) {
	          let firstDate = new Date();
	          $$invalidate(25, date = formatDate(firstDate));
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
	    $$invalidate(25, date = formatDate(_day.date));
	    dispatch('change');
	  }
	  const click_handler = (_disabled, _day) => !_disabled && onClickDate(_day);
	  const keypress_handler = (_disabled, _day) => !_disabled && onClickDate(_day);
	  const click_handler_1 = _month => {
	    $$invalidate(2, month = _month);
	    dispatch('month-change');
	    $$invalidate(8, view = 'calendar');
	  };
	  const keypress_handler_1 = _month => {
	    $$invalidate(2, month = _month);
	    dispatch('month-change');
	    $$invalidate(8, view = 'calendar');
	  };
	  const click_handler_2 = __year => {
	    $$invalidate(1, year = __year);
	    $$invalidate(8, view = 'month');
	  };
	  const keypress_handler_2 = __year => {
	    $$invalidate(1, year = __year);
	    $$invalidate(8, view = 'month');
	  };
	  function div3_binding($$value) {
	    binding_callbacks[$$value ? 'unshift' : 'push'](() => {
	      el = $$value;
	      $$invalidate(7, el);
	    });
	  }
	  $$self.$$set = $$props => {
	    if ('layout' in $$props) $$invalidate(29, layout = $$props.layout);
	    if ('date' in $$props) $$invalidate(25, date = $$props.date);
	    if ('startDate' in $$props) $$invalidate(26, startDate = $$props.startDate);
	    if ('holidays' in $$props) $$invalidate(27, holidays = $$props.holidays);
	    if ('datePicker' in $$props) $$invalidate(4, datePicker = $$props.datePicker);
	    if ('maxDays' in $$props) $$invalidate(30, maxDays = $$props.maxDays);
	    if ('limits' in $$props) $$invalidate(0, limits = $$props.limits);
	    if ('disabledWeekDays' in $$props) $$invalidate(31, disabledWeekDays = $$props.disabledWeekDays);
	    if ('loadSchedule' in $$props) $$invalidate(32, loadSchedule = $$props.loadSchedule);
	    if ('year' in $$props) $$invalidate(1, year = $$props.year);
	    if ('month' in $$props) $$invalidate(2, month = $$props.month);
	    if ('loadedMonths' in $$props) $$invalidate(28, loadedMonths = $$props.loadedMonths);
	    if ('loading' in $$props) $$invalidate(3, loading = $$props.loading);
	    if ('show' in $$props) $$invalidate(5, show = $$props.show);
	    if ('border' in $$props) $$invalidate(6, border = $$props.border);
	  };
	  $$self.$$.update = () => {
	    if ($$self.$$.dirty[0] & /*el*/128) {
	      if (el) {
	        $$invalidate(10, rtl = getComputedStyle(el).direction === 'rtl');
	      }
	    }
	    if ($$self.$$.dirty[0] & /*startDate*/67108864) {
	      if (startDate === null) {
	        $$invalidate(26, startDate = new Date());
	      } else {
	        $$invalidate(1, year = startDate.getFullYear());
	        $$invalidate(2, month = startDate.getMonth());
	      }
	    }
	    if ($$self.$$.dirty[0] & /*month, year*/6) {
	      $$invalidate(34, monthWithYear = month + '-' + year);
	    }
	    if ($$self.$$.dirty[0] & /*view, year, month*/262 | $$self.$$.dirty[1] & /*loadSchedule, lastDate, monthWithYear*/26) {
	      if (loadSchedule !== false && view === 'calendar' && (year || month)) {
	        if (lastDate !== monthWithYear) {
	          $$invalidate(35, lastDate = monthWithYear);
	          $$invalidate(3, loading = true);
	        }
	      }
	    }
	    if ($$self.$$.dirty[0] & /*loading*/8 | $$self.$$.dirty[1] & /*loadSchedule*/2) {
	      if (loadSchedule !== false && loading) {
	        _loadSchedule();
	      }
	    }
	    if ($$self.$$.dirty[0] & /*year, month, datePicker, days, limits, loadedMonths, holidays, date*/436208151 | $$self.$$.dirty[1] & /*disabledWeekDays, monthWithYear*/9) {
	      {
	        let _day = new Date(year, month, 1);
	        _day.setDate(_day.getDate() - ((_day.getDay() - datePicker.firstDay) % 7 + 7) % 7);
	        let lastDay = new Date(year, month + 1, 0);
	        lastDay.setDate(lastDay.getDate() - ((lastDay.getDay() - datePicker.firstDay) % 7 + 7) % 7 + 6);
	        $$invalidate(9, days = []);
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
	    if ($$self.$$.dirty[0] & /*view, datePicker, month, year*/278) {
	      if (view) {
	        switch (view) {
	          case 'calendar':
	            $$invalidate(19, title = datePicker.monthNamesShort[month] + ' ' + year);
	            break;
	          case 'month':
	          case 'year':
	            $$invalidate(19, title = year);
	            break;
	        }
	      }
	    }
	  };
	  return [limits, year, month, loading, datePicker, show, border, el, view, days, rtl, bgColor, textColor, borderColor, buttonClasses, activeButtonClasses, disabledButtonClasses, otherMonthButtonClasses, controlButtonClasses, title, dispatch, changeView, onClickLeft, onClickRight, onClickDate, date, startDate, holidays, loadedMonths, layout, maxDays, disabledWeekDays, loadSchedule, forceLoadSchedule, monthWithYear, lastDate, click_handler, keypress_handler, click_handler_1, keypress_handler_1, click_handler_2, keypress_handler_2, div3_binding];
	}
	class Calendar extends SvelteComponent {
	  constructor(options) {
	    super();
	    init(this, options, instance$3, create_fragment$3, safe_not_equal, {
	      layout: 29,
	      date: 25,
	      startDate: 26,
	      holidays: 27,
	      datePicker: 4,
	      maxDays: 30,
	      limits: 0,
	      disabledWeekDays: 31,
	      loadSchedule: 32,
	      forceLoadSchedule: 33,
	      year: 1,
	      month: 2,
	      loadedMonths: 28,
	      loading: 3,
	      show: 5,
	      border: 6
	    }, null, [-1, -1]);
	  }
	  get layout() {
	    return this.$$.ctx[29];
	  }
	  set layout(layout) {
	    this.$$set({
	      layout
	    });
	    flush();
	  }
	  get date() {
	    return this.$$.ctx[25];
	  }
	  set date(date) {
	    this.$$set({
	      date
	    });
	    flush();
	  }
	  get startDate() {
	    return this.$$.ctx[26];
	  }
	  set startDate(startDate) {
	    this.$$set({
	      startDate
	    });
	    flush();
	  }
	  get holidays() {
	    return this.$$.ctx[27];
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
	    return this.$$.ctx[30];
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
	  get disabledWeekDays() {
	    return this.$$.ctx[31];
	  }
	  set disabledWeekDays(disabledWeekDays) {
	    this.$$set({
	      disabledWeekDays
	    });
	    flush();
	  }
	  get loadSchedule() {
	    return this.$$.ctx[32];
	  }
	  set loadSchedule(loadSchedule) {
	    this.$$set({
	      loadSchedule
	    });
	    flush();
	  }
	  get forceLoadSchedule() {
	    return this.$$.ctx[33];
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
	    return this.$$.ctx[28];
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
	    return this.$$.ctx[5];
	  }
	  set show(show) {
	    this.$$set({
	      show
	    });
	    flush();
	  }
	  get border() {
	    return this.$$.ctx[6];
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
	  let data = $$O.extend({
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
	    $$O(document.body).trigger('bookly.render.step_detail', [$container]);
	    // Init.
	    let $guest_info = $$O('.bookly-js-guest', $container),
	      $phone_field = $$O('.bookly-js-user-phone-input', $container),
	      $email_field = $$O('.bookly-js-user-email', $container),
	      $email_confirm_field = $$O('.bookly-js-user-email-confirm', $container),
	      $birthday_day_field = $$O('.bookly-js-select-birthday-day', $container),
	      $birthday_month_field = $$O('.bookly-js-select-birthday-month', $container),
	      $birthday_year_field = $$O('.bookly-js-select-birthday-year', $container),
	      $address_country_field = $$O('.bookly-js-address-country', $container),
	      $address_state_field = $$O('.bookly-js-address-state', $container),
	      $address_postcode_field = $$O('.bookly-js-address-postcode', $container),
	      $address_city_field = $$O('.bookly-js-address-city', $container),
	      $address_street_field = $$O('.bookly-js-address-street', $container),
	      $address_street_number_field = $$O('.bookly-js-address-street_number', $container),
	      $address_additional_field = $$O('.bookly-js-address-additional_address', $container),
	      $address_country_error = $$O('.bookly-js-address-country-error', $container),
	      $address_state_error = $$O('.bookly-js-address-state-error', $container),
	      $address_postcode_error = $$O('.bookly-js-address-postcode-error', $container),
	      $address_city_error = $$O('.bookly-js-address-city-error', $container),
	      $address_street_error = $$O('.bookly-js-address-street-error', $container),
	      $address_street_number_error = $$O('.bookly-js-address-street_number-error', $container),
	      $address_additional_error = $$O('.bookly-js-address-additional_address-error', $container),
	      $birthday_day_error = $$O('.bookly-js-select-birthday-day-error', $container),
	      $birthday_month_error = $$O('.bookly-js-select-birthday-month-error', $container),
	      $birthday_year_error = $$O('.bookly-js-select-birthday-year-error', $container),
	      $full_name_field = $$O('.bookly-js-full-name', $container),
	      $first_name_field = $$O('.bookly-js-first-name', $container),
	      $last_name_field = $$O('.bookly-js-last-name', $container),
	      $notes_field = $$O('.bookly-js-user-notes', $container),
	      $custom_field = $$O('.bookly-js-custom-field', $container),
	      $info_field = $$O('.bookly-js-info-field', $container),
	      $phone_error = $$O('.bookly-js-user-phone-error', $container),
	      $email_error = $$O('.bookly-js-user-email-error', $container),
	      $email_confirm_error = $$O('.bookly-js-user-email-confirm-error', $container),
	      $name_error = $$O('.bookly-js-full-name-error', $container),
	      $first_name_error = $$O('.bookly-js-first-name-error', $container),
	      $last_name_error = $$O('.bookly-js-last-name-error', $container),
	      $captcha = $$O('.bookly-js-captcha-img', $container),
	      $custom_error = $$O('.bookly-custom-field-error', $container),
	      $info_error = $$O('.bookly-js-info-field-error', $container),
	      $modals = $$O('.bookly-js-modal', $container),
	      $login_modal = $$O('.bookly-js-login', $container),
	      $cst_modal = $$O('.bookly-js-cst-duplicate', $container),
	      $verification_modal = $$O('.bookly-js-verification-code', $container),
	      $verification_code = $$O('#bookly-verification-code', $container),
	      $next_btn = $$O('.bookly-js-next-step', $container),
	      $errors = _mapInstanceProperty(_context = $$O([$birthday_day_error, $birthday_month_error, $birthday_year_error, $address_country_error, $address_state_error, $address_postcode_error, $address_city_error, $address_street_error, $address_street_number_error, $address_additional_error, $name_error, $first_name_error, $last_name_error, $phone_error, $email_error, $email_confirm_error, $custom_error, $info_error])).call(_context, $$O.fn.toArray),
	      $fields = _mapInstanceProperty(_context2 = $$O([$birthday_day_field, $birthday_month_field, $birthday_year_field, $address_city_field, $address_country_field, $address_postcode_field, $address_state_field, $address_street_field, $address_street_number_field, $address_additional_field, $full_name_field, $first_name_field, $last_name_field, $phone_field, $email_field, $email_confirm_field, $custom_field, $info_field])).call(_context2, $$O.fn.toArray);

	    // Populate form after login.
	    var populateForm = function (response) {
	      $full_name_field.val(response.data.full_name).removeClass('bookly-error');
	      $first_name_field.val(response.data.first_name).removeClass('bookly-error');
	      $last_name_field.val(response.data.last_name).removeClass('bookly-error');
	      if (response.data.birthday) {
	        var dateParts = response.data.birthday.split('-'),
	          year = _parseInt$5(dateParts[0]),
	          month = _parseInt$5(dateParts[1]),
	          day = _parseInt$5(dateParts[2]);
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
	            if ($$O(this).prop('checked')) {
	              value.push($$O(this).val());
	            }
	          });
	          break;
	      }
	      $$O.each(custom_fields_conditions, function (i, condition) {
	        let $target = $$O('.bookly-custom-field-row[data-id="' + condition.target + '"]'),
	          target_visibility = $target.is(':visible');
	        if (_parseInt$5(condition.source) === id) {
	          let show = false;
	          $$O.each(value, function (i, v) {
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
	    $$O('.bookly-custom-field-row').on('change', 'select, input[type="checkbox"], input[type="radio"]', function () {
	      checkCustomFieldConditions($$O(this).closest('.bookly-custom-field-row'));
	    });
	    $$O('.bookly-custom-field-row').each(function () {
	      var _context9;
	      const _type = $$O(this).data('type');
	      if (_includesInstanceProperty$1(_context9 = ['drop-down', 'radio-buttons', 'checkboxes']).call(_context9, _type)) {
	        if (_type === 'drop-down') {
	          var _context10;
	          _findInstanceProperty(_context10 = $$O(this)).call(_context10, 'select').trigger('change');
	        } else {
	          var _context11;
	          _findInstanceProperty(_context11 = $$O(this)).call(_context11, 'input:checked').trigger('change');
	        }
	      }
	    });

	    // Custom fields date fields
	    let calendars = {};
	    $$O(document).on('click', function (e) {
	      var _context12;
	      let $calendar = $$O(e.target).closest('.bookly-js-datepicker-calendar-wrap'),
	        _id;
	      if ($calendar.length !== 0) {
	        _id = $calendar.data('id');
	      }
	      _forEachInstanceProperty(_context12 = _Object$keys(calendars)).call(_context12, id => {
	        if (id !== _id) calendars[id].show = false;
	      });
	    });
	    $$O('.bookly-js-cf-date', $container).each(function () {
	      var _context13;
	      let $that = $$O(this),
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
	      if ($$O(this).data('min') !== '') {
	        let startDate = new Date($$O(this).data('min'));
	        props.limits.start = startDate;
	        if (startDate > today) {
	          props.month = startDate.getMonth();
	          props.year = startDate.getFullYear();
	        }
	      }
	      if ($$O(this).data('max') !== '') {
	        let endDate = new Date($$O(this).data('max'));
	        props.limits.end = new Date($$O(this).data('max'));
	        if (endDate < today) {
	          props.month = endDate.getMonth();
	          props.year = endDate.getFullYear();
	        }
	      }
	      calendars[id] = new Calendar({
	        target: _findInstanceProperty(_context13 = $that.parent()).call(_context13, '.bookly-js-datepicker-calendar').get(0),
	        props: props
	      });
	      $$O(this).on('focus', function (e) {
	        calendars[id].show = true;
	      });
	      calendars[id].$on('change', function () {
	        calendars[id].show = false;
	        $that.val(formatDate$1(calendars[id].date));
	      });
	    });
	    if (intlTelInput.enabled) {
	      window.booklyIntlTelInput($phone_field.get(0), {
	        preferredCountries: [intlTelInput.country],
	        initialCountry: intlTelInput.country,
	        geoIpLookup: function (callback) {
	          $$O.get('https://ipinfo.io', function () {}, 'jsonp').always(function (resp) {
	            var countryCode = resp && resp.country ? resp.country : '';
	            callback(countryCode);
	          });
	        }
	      });
	    }
	    // Init modals.
	    _findInstanceProperty($container).call($container, '.bookly-js-modal.' + params.form_id).remove();
	    $modals.addClass(params.form_id).appendTo($container).on('click', '.bookly-js-close', function (e) {
	      var _context14, _context15, _context16;
	      e.preventDefault();
	      _findInstanceProperty(_context14 = _findInstanceProperty(_context15 = _findInstanceProperty(_context16 = $$O(e.delegateTarget).removeClass('bookly-in')).call(_context16, 'form').trigger('reset').end()).call(_context15, 'input').removeClass('bookly-error').end()).call(_context14, '.bookly-label-error').html('');
	    });
	    // Login modal.
	    $$O('.bookly-js-login-show', $container).on('click', function (e) {
	      e.preventDefault();
	      $login_modal.addClass('bookly-in');
	    });
	    $$O('button:submit', $login_modal).on('click', function (e) {
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
	    $$O('button:submit', $cst_modal).on('click', function (e) {
	      e.preventDefault();
	      $cst_modal.removeClass('bookly-in');
	      $next_btn.trigger('click', [1]);
	    });
	    // Verification code modal.
	    $$O('button:submit', $verification_modal).on('click', function (e) {
	      e.preventDefault();
	      $verification_modal.removeClass('bookly-in');
	      $next_btn.trigger('click');
	    });
	    // Facebook login button.
	    if (opt[params.form_id].hasOwnProperty('facebook') && opt[params.form_id].facebook.enabled && typeof FB !== 'undefined') {
	      FB.XFBML.parse($$O('.bookly-js-fb-login-button', $container).parent().get(0));
	      opt[params.form_id].facebook.onStatusChange = function (response) {
	        if (response.status === 'connected') {
	          opt[params.form_id].facebook.enabled = false;
	          opt[params.form_id].facebook.onStatusChange = undefined;
	          $guest_info.fadeOut('slow', function () {
	            // Hide buttons in all Bookly forms on the page.
	            $$O('.bookly-js-fb-login-button').hide();
	          });
	          FB.api('/me', {
	            fields: 'id,name,first_name,last_name,email'
	          }, function (userInfo) {
	            booklyAjax({
	              type: 'POST',
	              data: $$O.extend(userInfo, {
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
	      let $terms = $$O('.bookly-js-terms', $container),
	        $terms_error = $$O('.bookly-js-terms-error', $container);
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
	            $$O.globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }

	        // Customer information fields.
	        $$O('div.bookly-js-info-field-row', $container).each(function () {
	          var $this = $$O(this);
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
	        $$O('.bookly-custom-fields-container', $container).each(function () {
	          let $cf_container = $$O(this),
	            key = $cf_container.data('key'),
	            custom_fields_data = [];
	          $$O('div.bookly-custom-field-row', $cf_container).each(function () {
	            var $this = $$O(this);
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
	          email: _trimInstanceProperty(_context17 = $email_field.val()).call(_context17),
	          email_confirm: $email_confirm_field.length === 1 ? _trimInstanceProperty(_context18 = $email_confirm_field.val()).call(_context18) : undefined,
	          birthday: {
	            day: $birthday_day_field.val(),
	            month: $birthday_month_field.val(),
	            year: $birthday_year_field.val()
	          },
	          full_address: $$O('.bookly-js-cst-address-autocomplete', $container).val(),
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
	              $$O.each(response.info_fields, function (field_id, message) {
	                var $div = $$O('div.bookly-js-info-field-row[data-id="' + field_id + '"]', $container);
	                _findInstanceProperty($div).call($div, '.bookly-js-info-field-error').html(message);
	                _findInstanceProperty($div).call($div, '.bookly-js-info-field').addClass('bookly-error');
	                if ($scroll_to === null) {
	                  $scroll_to = _findInstanceProperty($div).call($div, '.bookly-js-info-field');
	                }
	              });
	            }
	            if (response.custom_fields) {
	              $$O.each(response.custom_fields, function (key, fields) {
	                $$O.each(fields, function (field_id, message) {
	                  var $custom_fields_collector = $$O('.bookly-custom-fields-container[data-key="' + key + '"]', $container);
	                  var $div = $$O('[data-id="' + field_id + '"]', $custom_fields_collector);
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
	    $$O('.bookly-js-back-step', $container).on('click', function (e) {
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
	    $$O('.bookly-js-captcha-refresh', $container).on('click', function () {
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
	    bookly_forms = bookly_forms || $$O('.bookly-form .bookly-details-step');
	    bookly_forms.each(function () {
	      initGooglePlacesAutocomplete($$O(this));
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
	          return getFieldValueByType('locality') || getFieldValueByType('administrative_area_level_3') || getFieldValueByType('postal_town');
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
	          return getFieldValueByType('street_number');
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
	    let data = $$O.extend({
	        action: 'bookly_render_cart'
	      }, params),
	      $container = opt[params.form_id].$container;
	    booklyAjax({
	      data
	    }).then(response => {
	      $container.html(response.html);
	      if (error) {
	        $$O('.bookly-label-error', $container).html(error.message);
	        $$O('tr[data-cart-key="' + error.failed_key + '"]', $container).addClass('bookly-label-error');
	      } else {
	        $$O('.bookly-label-error', $container).hide();
	      }
	      scrollTo($container, params.form_id);
	      const customJS = response.custom_js;
	      $$O('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $$O.globalEval(customJS.next_button);
	          } catch (e) {
	            // Do nothing
	          }
	        }
	        stepDetails({
	          form_id: params.form_id
	        });
	      });
	      $$O('.bookly-add-item', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          new_chain: true
	        });
	      });
	      // 'BACK' button.
	      $$O('.bookly-js-back-step', $container).on('click', function (e) {
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
	      $$O('.bookly-js-actions button', $container).on('click', function () {
	        laddaStart(this);
	        let $this = $$O(this),
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
	                $trs_to_remove = $$O('tr[data-cart-key="' + remove_cart_key + '"]', $container);
	              $cart_item.delay(300).fadeOut(200, function () {
	                if (response.data.total_waiting_list) {
	                  $$O('.bookly-js-waiting-list-price', $container).html(response.data.waiting_list_price);
	                  $$O('.bookly-js-waiting-list-deposit', $container).html(response.data.waiting_list_deposit);
	                } else {
	                  $$O('.bookly-js-waiting-list-price', $container).closest('tr').remove();
	                }
	                $$O('.bookly-js-subtotal-price', $container).html(response.data.subtotal_price);
	                $$O('.bookly-js-subtotal-deposit', $container).html(response.data.subtotal_deposit);
	                $$O('.bookly-js-pay-now-deposit', $container).html(response.data.pay_now_deposit);
	                $$O('.bookly-js-pay-now-tax', $container).html(response.data.pay_now_tax);
	                $$O('.bookly-js-total-price', $container).html(response.data.total_price);
	                $$O('.bookly-js-total-tax', $container).html(response.data.total_tax);
	                $trs_to_remove.remove();
	                if ($$O('tr[data-cart-key]').length == 0) {
	                  $$O('.bookly-js-back-step', $container).hide();
	                  $$O('.bookly-js-next-step', $container).hide();
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
	    let data = $$O.extend({
	        action: 'bookly_render_repeat'
	      }, params),
	      $container = opt[params.form_id].$container;
	    booklyAjax({
	      data
	    }).then(response => {
	      var _context3, _context4;
	      $container.html(response.html);
	      scrollTo($container, params.form_id);
	      let $repeat_enabled = $$O('.bookly-js-repeat-appointment-enabled', $container),
	        $next_step = $$O('.bookly-js-next-step', $container),
	        $repeat_container = $$O('.bookly-js-repeat-variants-container', $container),
	        $variants = $$O('[class^="bookly-js-variant"]', $repeat_container),
	        $repeat_variant = $$O('.bookly-js-repeat-variant', $repeat_container),
	        $button_get_schedule = $$O('.bookly-js-get-schedule', $repeat_container),
	        $variant_weekly = $$O('.bookly-js-variant-weekly', $repeat_container),
	        $variant_monthly = $$O('.bookly-js-repeat-variant-monthly', $repeat_container),
	        $date_until = $$O('.bookly-js-repeat-until', $repeat_container),
	        $repeat_times = $$O('.bookly-js-repeat-times', $repeat_container),
	        $monthly_specific_day = $$O('.bookly-js-monthly-specific-day', $repeat_container),
	        $monthly_week_day = $$O('.bookly-js-monthly-week-day', $repeat_container),
	        $repeat_every_day = $$O('.bookly-js-repeat-daily-every', $repeat_container),
	        $schedule_container = $$O('.bookly-js-schedule-container', $container),
	        $days_error = $$O('.bookly-js-days-error', $repeat_container),
	        $schedule_slots = $$O('.bookly-js-schedule-slots', $schedule_container),
	        $intersection_info = $$O('.bookly-js-intersection-info', $schedule_container),
	        $info_help = $$O('.bookly-js-schedule-help', $schedule_container),
	        $info_wells = $$O('.bookly-well', $schedule_container),
	        $pagination = $$O('.bookly-pagination', $schedule_container),
	        $schedule_row_template = $$O('.bookly-schedule-row-template .bookly-schedule-row', $schedule_container),
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
	            $time = $$O('<select/>');
	            $$O.each(options, function (index, option) {
	              var $option = $$O('<option/>');
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
	              let page = _parseInt$5(_findInstanceProperty($pagination).call($pagination, '.active').data('page'));
	              if (page > 1) {
	                repeat.renderSchedulePage(page - 1);
	              }
	            },
	            nextPage = function (e) {
	              e.preventDefault();
	              let page = _parseInt$5(_findInstanceProperty($pagination).call($pagination, '.active').data('page'));
	              if (page < count / rows_on_page) {
	                repeat.renderSchedulePage(page + 1);
	              }
	            };
	          $schedule_slots.html('');
	          for (var i = start, j = 0; j < rows_on_page && i < count; i++, j++) {
	            $row = $schedule_row_template.clone();
	            $row.data('datetime', schedule[i].datetime);
	            $row.data('index', schedule[i].index);
	            $$O('> div:first-child', $row).html(schedule[i].index);
	            $$O('.bookly-schedule-date', $row).html(schedule[i].display_date);
	            if (schedule[i].all_day_service_time !== undefined) {
	              $$O('.bookly-js-schedule-time', $row).hide();
	              $$O('.bookly-js-schedule-all-day-time', $row).html(schedule[i].all_day_service_time).show();
	            } else {
	              $$O('.bookly-js-schedule-time', $row).html(schedule[i].display_time).show();
	              $$O('.bookly-js-schedule-all-day-time', $row).hide();
	            }
	            if (schedule[i].another_time) {
	              $$O('.bookly-schedule-intersect', $row).show();
	            }
	            if (schedule[i].deleted) {
	              _findInstanceProperty($row).call($row, '.bookly-schedule-appointment').addClass('bookly-appointment-hidden');
	            }
	            $schedule_slots.append($row);
	          }
	          if (count > rows_on_page) {
	            var $btn = $$O('<li/>').append($$O('<a>', {
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
	              $btn = $$O('<li/>', {
	                'data-page': j
	              }).append($$O('<a>', {
	                href: '#',
	                text: j
	              }));
	              $pagination.append($btn);
	              $btn.on('click', function (e) {
	                e.preventDefault();
	                repeat.renderSchedulePage($$O(this).data('page'));
	              }).keypress(function (e) {
	                e.preventDefault();
	                if (e.which == 13 || e.which == 32) {
	                  repeat.renderSchedulePage($$O(this).data('page'));
	                }
	              });
	            }
	            _findInstanceProperty($pagination).call($pagination, 'li:eq(' + page + ')').addClass('active');
	            $btn = $$O('<li/>').append($$O('<a>', {
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
	                page = _parseInt$5(i / rows_on_page) + 1;
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
	          $$O.each(schedule, function (index, item) {
	            if (!preferred_time && !item.another_time) {
	              preferred_time = item.display_time;
	            }
	          });
	          repeat.renderSchedulePage(1);
	          $schedule_container.show();
	          $next_step.prop('disabled', schedule.length == 0);
	          $schedule_slots.on('click', 'button[data-action]', function () {
	            var $schedule_row = $$O(this).closest('.bookly-schedule-row');
	            var row_index = $schedule_row.data('index') - 1;
	            switch ($$O(this).data('action')) {
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
	                  let $row = $$O(this).closest('.bookly-schedule-row'),
	                    index = $row.data('index') - 1;
	                  _findInstanceProperty($row).call($row, 'button[data-action="edit"]').show();
	                  _findInstanceProperty($row).call($row, 'button[data-action="save"]').hide();
	                  _findInstanceProperty($row).call($row, '.bookly-schedule-date').html(schedule[index].display_date);
	                  _findInstanceProperty($row).call($row, '.bookly-js-schedule-time').html(schedule[index].display_time);
	                });
	                let slots = JSON.parse(schedule[row_index].slots),
	                  current_date = slots[0][2].split(' ')[0],
	                  $date = $$O('<input/>', {
	                    type: 'text',
	                    value: formatDate$1(current_date, short_date_format)
	                  }),
	                  $edit_button = $$O(this),
	                  ladda_round = laddaStart(this);
	                $date.data('date', current_date);
	                _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-date').html($$O.merge($date, $$O('<div class="bookly-relative bookly-w-full bookly-z-10 bookly-js-datepicker-container" style="font-weight: normal;"><div class="bookly-absolute bookly-top-1 bookly-w-72 bookly-p-0 bookly-bg-white bookly-js-datepicker-calendar"></div></div>')));
	                $date = _findInstanceProperty($schedule_row).call($schedule_row, '.bookly-schedule-date input');
	                if (schedule_calendar) {
	                  schedule_calendar.$destroy();
	                }
	                $$O(document).on('click', function (e) {
	                  if ($$O(e.target).closest('.bookly-schedule-date').length === 0) {
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
	                  $$O.each(schedule, function (index, item) {
	                    if (row_index != index && !item.deleted) {
	                      exclude.push(item.slots);
	                    }
	                  });
	                  booklyAjax({
	                    type: 'POST',
	                    data: {
	                      action: 'bookly_recurring_appointments_get_daily_customer_schedule',
	                      date: $$O(this).data('date'),
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
	                $$O(this).hide();
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
	              if (($repeat_every_day.val() > 6 || $$O.inArray(current_date.format('ddd').toLowerCase(), repeat.week_days) != -1) && current_date.diff(repeat.date_from, 'days') % $repeat_every_day.val() == 0) {
	                return true;
	              }
	              break;
	            case 'weekly':
	            case 'biweekly':
	              if (($repeat_variant.val() == 'weekly' || current_date.diff(repeat.date_from.clone().startOf('isoWeek'), 'weeks') % 2 == 0) && $$O.inArray(current_date.format('ddd').toLowerCase(), repeat.checked_week_days) != -1) {
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
	            repeat.week_days.push($$O(this).val());
	          });
	          repeat.checked_week_days = [];
	          $$O('.bookly-js-week-days input:checked', $repeat_container).each(function () {
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
	            repeat.week_days.push($$O(this).val());
	          });
	          repeat.checked_week_days = [];
	          $$O('.bookly-js-week-days input:checked', $repeat_container).each(function () {
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
	      $$O(document).on('click', function (e) {
	        if ($$O(e.target).closest('.bookly-js-repeat-until-wrap').length === 0) {
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
	        $repeat_container.toggle($$O(this).prop('checked'));
	        if ($$O(this).prop('checked')) {
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
	            $$O('.bookly-js-week-days input[type="checkbox"]', $repeat_container).prop('checked', false).parent().removeClass('active');
	            _forEachInstanceProperty(_context4 = repeat_params.on).call(_context4, function (val) {
	              $$O('.bookly-js-week-days input:checkbox[value=' + val + ']', $repeat_container).prop('checked', true);
	            });
	            break;
	          case 'monthly':
	            if (repeat_params.on === 'day') {
	              $variant_monthly.val('specific');
	              $$O('.bookly-js-monthly-specific-day[value=' + repeat_params.day + ']', $repeat_container).prop('checked', true);
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
	      $$O('.bookly-js-week-days input', $repeat_container).on('change', function () {
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
	            $$O('.bookly-js-week-days input[type="checkbox"]:checked', $variant_weekly).each(function () {
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
	      $$O('.bookly-js-back-step', $container).on('click', function (e) {
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
	      $$O('.bookly-js-go-to-cart', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepCart({
	          form_id: params.form_id,
	          from_step: 'repeat'
	        });
	      });
	      $$O('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);

	        // Execute custom JavaScript
	        if (customJS) {
	          try {
	            $$O.globalEval(customJS.next_button);
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
	  $$O.extend(data, params);
	  let columnizerObserver = false;
	  let lastObserverTime = 0;
	  let lastObserverWidth = 0;
	  let loadedMonths = [];

	  // Build slots html
	  function prepareSlotsHtml(slots_data, selected_date) {
	    var response = {};
	    $$O.each(slots_data, function (group, group_slots) {
	      var html = '<button class="bookly-day" value="' + group + '">' + group_slots.title + '</button>';
	      $$O.each(group_slots.slots, function (id, slot) {
	        html += '<button value="' + _JSON$stringify(slot.data).replace(/"/g, '&quot;') + '" data-group="' + group + '" class="bookly-hour' + (slot.special_hour ? ' bookly-slot-in-special-hour' : '') + (slot.status == 'waiting-list' ? ' bookly-slot-in-waiting-list' : slot.status == 'booked' ? ' booked' : '') + '"' + (slot.status == 'booked' ? ' disabled' : '') + '>' + '<span class="ladda-label bookly-time-main' + (slot.data[0][2] == selected_date ? ' bookly-bold' : '') + '">' + '<i class="bookly-hour-icon"><span></span></i>' + slot.time_text + '</span>' + '<span class="bookly-time-additional' + (slot.status == 'waiting-list' ? ' bookly-waiting-list' : '') + '"> ' + slot.additional_text + '</span>' + '</button>';
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
	    var $columnizer_wrap = $$O('.bookly-columnizer-wrap', $container),
	      $columnizer = $$O('.bookly-columnizer', $columnizer_wrap),
	      $time_next_button = $$O('.bookly-time-next', $container),
	      $time_prev_button = $$O('.bookly-time-prev', $container),
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
	    $$O('.bookly-js-back-step', $container).on('click', function (e) {
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
	    $$O('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'time'
	      });
	    });

	    // Time zone switcher.
	    $$O('.bookly-js-time-zone-switcher', $container).on('change', function (e) {
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
	      let date = response.current_date ? response.first_available_date ? response.first_available_date : response.current_date : response.selected_date ? response.selected_date.substring(0, 10) : $$O('.bookly-js-selected-date', $container).data('value');
	      loadedMonths.push(moment(date).month() + '-' + moment(date).year());
	      let _cal = new Calendar({
	        target: $$O('.bookly-js-slot-calendar', $container).get(0),
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
	      $$O.each(slots, function (group, group_slots) {
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
	      slots_per_column = _parseInt$5($$O(window).height() / slot_height, 10);
	      if (slots_per_column < 4) {
	        slots_per_column = 4;
	      } else if (slots_per_column > 10) {
	        slots_per_column = 10;
	      }
	      var hammertime = $$O('.bookly-time-step', $container).hammer({
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
	          var $button = $$O('> button:last', $columnizer);
	          if ($button.length === 0) {
	            $button = $$O('.bookly-column:hidden:last > button:last', $columnizer);
	            if ($button.length === 0) {
	              $button = $$O('.bookly-column:last > button:last', $columnizer);
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
	              $$O.each(prepareSlotsHtml(response.slots_data, response.selected_date), function (group, group_slots) {
	                slots_data += group_slots;
	              });
	              var $html = $$O(slots_data);
	              // The first slot is always a day slot.
	              // Check if such day slot already exists (this can happen
	              // because of time zone offset) and then remove the first slot.
	              var $first_day = $html.eq(0);
	              if ($$O('button.bookly-day[value="' + $first_day.attr('value') + '"]', $container).length) {
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
	      $$O('.bookly-time-screen,.bookly-not-time-screen', $container).addClass('bookly-spin-overlay');
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
	        new Spinner(opts).spin($$O('.bookly-not-time-screen', $container).get(0));
	      }
	    }
	    function initSlots() {
	      var $buttons = $$O('> button', $columnizer),
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
	            $column = $$O('<div class="' + column_class + '" />');
	            $button = $$O(_spliceInstanceProperty($buttons).call($buttons, 0, 1));
	            $button.addClass('bookly-js-first-child');
	            $column.append($button);
	          } else {
	            slots_count++;
	            $button = $$O(_spliceInstanceProperty($buttons).call($buttons, 0, 1));
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
	          $column = $$O('<div class="' + column_class + '" />');
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
	            $button = $$O(_spliceInstanceProperty($buttons).call($buttons, 0, 1));
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
	      var $columns = $$O('> .bookly-column', $columnizer);
	      while (has_more_slots ? $columns.length >= columns_per_screen : $columns.length) {
	        $screen = $$O('<div class="bookly-time-screen"/>');
	        for (var i = 0; i < columns_per_screen; ++i) {
	          $column = $$O(_spliceInstanceProperty($columns).call($columns, 0, 1));
	          if (i == 0) {
	            $column.addClass('bookly-js-first-column');
	            var $first_slot = _findInstanceProperty($column).call($column, '.bookly-js-first-child');
	            // In the first column the first slot is time.
	            if (!$first_slot.hasClass('bookly-day')) {
	              var group = $first_slot.data('group'),
	                $group_slot = $$O('button.bookly-day[value="' + group + '"]:last', $container);
	              // Copy group slot to the first column.
	              $column.prepend($group_slot.clone());
	            }
	          }
	          $screen.append($column);
	        }
	        $columnizer.append($screen);
	      }
	      $screens = $$O('.bookly-time-screen', $columnizer);
	      if ($current_screen === null) {
	        $current_screen = $screens.eq(0);
	      }
	      $$O('button.bookly-time-skip', $container).off('click').on('click', function (e) {
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
	      $$O('button.bookly-hour', $container).off('click').on('click', function (e) {
	        requestSessionSave.cancel();
	        e.stopPropagation();
	        e.preventDefault();
	        var $this = $$O(this),
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
	            $$O.globalEval(customJS.next_button);
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
	      $$O('.bookly-time-step', $container).width(columns_per_screen * column_width);
	      $columnizer_wrap.height($current_screen.height());
	    }
	    function observeResizeColumnizer() {
	      if ($$O('.bookly-time-step', $container).length > 0) {
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
	          let calendarWidth = $$O('.bookly-js-slot-calendar', $container).width();
	          if (formWidth > calendarWidth + column_width + 24) {
	            columns_per_screen = _parseInt$5((formWidth - calendarWidth - 24) / column_width, 10);
	          } else {
	            columns_per_screen = _parseInt$5(formWidth / column_width, 10);
	          }
	        } else {
	          columns_per_screen = _parseInt$5(formWidth / column_width, 10);
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
	  $$O.extend(data, params);
	  booklyAjax({
	    data
	  }).then(response => {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    let $next_step = $$O('.bookly-js-next-step', $container),
	      $back_step = $$O('.bookly-js-back-step', $container),
	      $goto_cart = $$O('.bookly-js-go-to-cart', $container),
	      $extras_items = $$O('.bookly-js-extras-item', $container),
	      $extras_summary = $$O('.bookly-js-extras-summary span', $container),
	      customJS = response.custom_js,
	      $this,
	      $input,
	      format = new Format(response);
	    var extrasChanged = function ($extras_item, quantity) {
	      var $input = _findInstanceProperty($extras_item).call($extras_item, 'input'),
	        $total = _findInstanceProperty($extras_item).call($extras_item, '.bookly-js-extras-total-price'),
	        total_price = quantity * _parseFloat$1($extras_item.data('price'));
	      $total.text(format.price(total_price));
	      $input.val(quantity);
	      _findInstanceProperty($extras_item).call($extras_item, '.bookly-js-extras-thumb').toggleClass('bookly-extras-selected', quantity > 0);

	      // Updating summary
	      var amount = 0;
	      $extras_items.each(function (index, elem) {
	        var $this = $$O(this),
	          multiplier = $this.closest('.bookly-js-extras-container').data('multiplier');
	        amount += _parseFloat$1($this.data('price')) * _findInstanceProperty($this).call($this, 'input').val() * multiplier;
	      });
	      if (amount) {
	        $extras_summary.html(' + ' + format.price(amount));
	      } else {
	        $extras_summary.html('');
	      }
	    };
	    $extras_items.each(function (index, elem) {
	      var $this = $$O(this);
	      var $input = _findInstanceProperty($this).call($this, 'input');
	      $$O('.bookly-js-extras-thumb', $this).on('click', function () {
	        extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	      }).keypress(function (e) {
	        e.preventDefault();
	        if (e.which == 13 || e.which == 32) {
	          extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	        }
	      });
	      _findInstanceProperty($this).call($this, '.bookly-js-count-control').on('click', function () {
	        var count = _parseInt$5($input.val());
	        count = $$O(this).hasClass('bookly-js-extras-increment') ? Math.min($this.data('max_quantity'), count + 1) : Math.max($this.data('min_quantity'), count - 1);
	        extrasChanged($this, count);
	      });
	      setInputFilter($input.get(0), function (value) {
	        let valid = /^\d*$/.test(value) && (value === '' || _parseInt$5(value) <= $this.data('max_quantity') && _parseInt$5(value) >= $this.data('min_quantity'));
	        if (valid) {
	          extrasChanged($this, value === '' ? $this.data('min_quantity') : _parseInt$5(value));
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
	          $$O.globalEval(customJS.next_button);
	        } catch (e) {
	          // Do nothing
	        }
	      }
	      var extras = {};
	      $$O('.bookly-js-extras-container', $container).each(function () {
	        var $extras_container = $$O(this);
	        var chain_id = $extras_container.data('chain');
	        var chain_extras = {};
	        // Get checked extras for chain.
	        _findInstanceProperty($extras_container).call($extras_container, '.bookly-js-extras-item').each(function (index, elem) {
	          $this = $$O(this);
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

	var DESCRIPTORS = descriptors$1;
	var fails = fails$L;
	var uncurryThis = functionUncurryThis$1;
	var objectGetPrototypeOf = objectGetPrototypeOf$2;
	var objectKeys = objectKeys$4;
	var toIndexedObject = toIndexedObject$g;
	var $propertyIsEnumerable = objectPropertyIsEnumerable$1.f;

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

	var objectToArray = {
	  // `Object.entries` method
	  // https://tc39.es/ecma262/#sec-object.entries
	  entries: createMethod(true),
	  // `Object.values` method
	  // https://tc39.es/ecma262/#sec-object.values
	  values: createMethod(false)
	};

	var $ = _export$1;
	var $values = objectToArray.values;

	// `Object.values` method
	// https://tc39.es/ecma262/#sec-object.values
	$({ target: 'Object', stat: true }, {
	  values: function values(O) {
	    return $values(O);
	  }
	});

	var path = path$m;

	var values$2 = path.Object.values;

	var parent = values$2;

	var values$1 = parent;

	var values = values$1;

	var _Object$values = /*@__PURE__*/getDefaultExportFromCjs(values);

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
	      t = text( /*error*/ctx[5]);
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
	  let each_value = ensure_array_like( /*items*/ctx[4]);
	  let each_blocks = [];
	  for (let i = 0; i < each_value.length; i += 1) {
	    each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));
	  }
	  let if_block1 = /*error*/ctx[5] && create_if_block$2(ctx);
	  return {
	    c() {
	      label_1 = element("label");
	      t0 = text( /*label*/ctx[2]);
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
	      if ( /*selected*/ctx[1] === void 0) add_render_callback(() => /*select_change_handler*/ctx[9].call(select));
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
	        each_value = ensure_array_like( /*items*/ctx[4]);
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
	      if (dirty[0] & /*locations*/1) select_changes.items = _Object$values( /*locations*/ctx[0]);
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
	      items: _Object$values( /*categoryItems*/ctx[26]),
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
	      if (dirty[0] & /*serviceItems*/134217728) select_changes.items = _Object$values( /*serviceItems*/ctx[27]);
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
	      if (dirty[0] & /*staffItems*/8388608) select_changes.items = _Object$values( /*staffItems*/ctx[23]);
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
	      items: _Object$values( /*durationItems*/ctx[24]),
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
	      if (dirty[0] & /*durationItems*/16777216) select_changes.items = _Object$values( /*durationItems*/ctx[24]);
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
	      items: _Object$values( /*nopItems*/ctx[28]),
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
	      if (dirty[0] & /*nopItems*/268435456) select_changes.items = _Object$values( /*nopItems*/ctx[28]);
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
	      items: _Object$values( /*quantityItems*/ctx[29]),
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
	      if (dirty[0] & /*quantityItems*/536870912) select_changes.items = _Object$values( /*quantityItems*/ctx[29]);
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
	        transition_out(if_block0, 1, 1, () => {
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
	        transition_out(if_block1, 1, 1, () => {
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
	        transition_out(if_block2, 1, 1, () => {
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
	        transition_out(if_block3, 1, 1, () => {
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
	        transition_out(if_block4, 1, 1, () => {
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
	        transition_out(if_block5, 1, 1, () => {
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
	        transition_out(if_block6, 1, 1, () => {
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
	        $$O.each(locations[locationId].staff, id => {
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
	        $$O.each(locations[locationId].staff, id => {
	          $$O.each(staff[id].services, srvId => {
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
	        $$O.each(staff[staffId].services, id => {
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
	      staffIds: staffId ? [staffId] : _mapInstanceProperty($$O).call($$O, staffItems, item => item.id),
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
	        $$O.each(staff, (id, staffMember) => {
	          if (!locationId || id in locations[locationId].staff) {
	            if (!serviceId) {
	              if (!categoryId) {
	                $$invalidate(23, staffItems[id] = $$O.extend({}, staffMember), staffItems);
	              } else {
	                $$O.each(staffMember.services, srvId => {
	                  if (services[srvId].category_id === categoryId) {
	                    $$invalidate(23, staffItems[id] = $$O.extend({}, staffMember), staffItems);
	                    return false;
	                  }
	                });
	              }
	            } else if (serviceId in staffMember.services) {
	              $$O.each(staffMember.services[serviceId].locations, (locId, locSrv) => {
	                if (lookupLocationId && lookupLocationId !== _parseInt$5(locId)) {
	                  return true;
	                }
	                $$invalidate(65, srvMinCapacity = srvMinCapacity ? Math.min(srvMinCapacity, locSrv.min_capacity) : locSrv.min_capacity);
	                $$invalidate(64, srvMaxCapacity = srvMaxCapacity ? Math.max(srvMaxCapacity, locSrv.max_capacity) : locSrv.max_capacity);
	                $$invalidate(23, staffItems[id] = $$O.extend({}, staffMember, {
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
	          $$O.each(staff, (id, staffMember) => {
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
	          $$O.each(services, (id, service) => {
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
	            $$O.each(staff, stId => {
	              $$O.each(staff[stId].services, srvId => {
	                if (lookupLocationId in staff[stId].services[srvId].locations) {
	                  categoryIds.push(services[srvId].category_id);
	                  serviceIds.push(srvId);
	                }
	              });
	            });
	          } else {
	            $$O.each(locations[locationId].staff, stId => {
	              $$O.each(staff[stId].services, srvId => {
	                categoryIds.push(services[srvId].category_id);
	                serviceIds.push(srvId);
	              });
	            });
	          }
	          $$O.each(categories, (id, category) => {
	            if ($$O.inArray(_parseInt$5(id), categoryIds) > -1) {
	              $$invalidate(26, categoryItems[id] = category, categoryItems);
	            }
	          });
	          if (categoryId && $$O.inArray(categoryId, categoryIds) === -1) {
	            $$invalidate(18, categoryId = 0);
	            $$invalidate(61, categorySelected = false);
	          }
	          $$O.each(services, (id, service) => {
	            if ($$O.inArray(id, serviceIds) > -1) {
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
	      const chainitem_changes = dirty & /*data, items, multiple*/7 ? get_spread_update(chainitem_spread_levels, [dirty & /*data*/2 && get_spread_object( /*data*/ctx[1]), dirty & /*items*/1 && {
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
	  let each_value = ensure_array_like( /*items*/ctx[0]);
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
	        each_value = ensure_array_like( /*items*/ctx[0]);
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
	  $$O.extend(data, params);
	  booklyAjax({
	    data
	  }).then(response => {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    var $chain = $$O('.bookly-js-chain', $container),
	      $date_from = $$O('.bookly-js-date-from', $container),
	      $week_days = $$O('.bookly-js-week-days', $container),
	      $select_time_from = $$O('.bookly-js-select-time-from', $container),
	      $select_time_to = $$O('.bookly-js-select-time-to', $container),
	      $next_step = $$O('.bookly-js-next-step', $container),
	      $mobile_next_step = $$O('.bookly-js-mobile-next-step', $container),
	      $mobile_prev_step = $$O('.bookly-js-mobile-prev-step', $container),
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
	      $$O.each(services, function (id, service) {
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
	      target: $$O('.bookly-js-datepicker-calendar', $container).get(0),
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
	    $$O(document).on('click', function (e) {
	      if ($$O(e.target).closest('.bookly-js-available-date').length === 0) {
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
	    $$O('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'service'
	      });
	    });
	    if (opt[params.form_id].form_attributes.hide_date) {
	      $$O('.bookly-js-available-date', $container).hide();
	    }
	    if (opt[params.form_id].form_attributes.hide_week_days) {
	      $$O('.bookly-js-week-days', $container).hide();
	    }
	    if (opt[params.form_id].form_attributes.hide_time_range) {
	      $$O('.bookly-js-time-range', $container).hide();
	    }

	    // time from
	    $select_time_from.on('change', function () {
	      var start_time = $$O(this).val(),
	        end_time = $select_time_to.val(),
	        $last_time_entry = $$O('option:last', $select_time_from);
	      $select_time_to.empty();

	      // case when we click on the not last time entry
	      if ($select_time_from[0].selectedIndex < $last_time_entry.index()) {
	        // clone and append all next "time_from" time entries to "time_to" list
	        $$O('option', this).each(function () {
	          if ($$O(this).val() > start_time) {
	            $select_time_to.append($$O(this).clone());
	          }
	        });
	        // case when we click on the last time entry
	      } else {
	        $select_time_to.append($last_time_entry.clone()).val($last_time_entry.val());
	      }
	      var first_value = $$O('option:first', $select_time_to).val();
	      $select_time_to.val(end_time >= first_value ? end_time : first_value);
	    });
	    let stepServiceValidator = function () {
	      let valid = true,
	        $scroll_to = null;
	      $$O(c.validate()).each(function (_, status) {
	        if (!status.valid) {
	          valid = false;
	          let $el = $$O(status.el);
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
	      if ($week_days.length && !$$O(':checked', $week_days).length) {
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
	            $$O.globalEval(customJS.next_button);
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
	        $$O.each(c.getValues(), function (_, values) {
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
	        $$O('.bookly-js-week-days input:checked', $container).each(function () {
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
	          $$O('.bookly-js-mobile-step-1', $container).hide();
	          $$O('.bookly-stepper li:eq(1)', $container).addClass('bookly-step-active');
	          $$O('.bookly-stepper li:eq(0)', $container).removeClass('bookly-step-active');
	          $$O('.bookly-js-mobile-step-2', $container).css('display', 'block');
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
	        $$O('.bookly-stepper li:eq(0)', $container).addClass('bookly-step-active');
	        $$O('.bookly-stepper li:eq(1)', $container).removeClass('bookly-step-active');
	      }, 0);
	      $mobile_prev_step.remove();
	    } else {
	      $mobile_prev_step.on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        $$O('.bookly-js-mobile-step-1', $container).show();
	        $$O('.bookly-js-mobile-step-2', $container).hide();
	        $$O('.bookly-stepper li:eq(0)', $container).addClass('bookly-step-active');
	        $$O('.bookly-stepper li:eq(1)', $container).removeClass('bookly-step-active');
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
	  let $container = $$O('#bookly-form-' + options.form_id);
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
	            data: $$O.extend(userInfo, {
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
