var bookly = (function ($) {
	'use strict';

	function _interopDefaultLegacy (e) { return e && typeof e === 'object' && 'default' in e ? e : { 'default': e }; }

	var $__default = /*#__PURE__*/_interopDefaultLegacy($);

	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function createCommonjsModule(fn) {
	  var module = { exports: {} };
		return fn(module, module.exports), module.exports;
	}

	var check = function (it) {
	  return it && it.Math == Math && it;
	};

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global$1 =
	  // eslint-disable-next-line es/no-global-this -- safe
	  check(typeof globalThis == 'object' && globalThis) ||
	  check(typeof window == 'object' && window) ||
	  // eslint-disable-next-line no-restricted-globals -- safe
	  check(typeof self == 'object' && self) ||
	  check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
	  // eslint-disable-next-line no-new-func -- fallback
	  (function () { return this; })() || Function('return this')();

	var fails = function (exec) {
	  try {
	    return !!exec();
	  } catch (error) {
	    return true;
	  }
	};

	// Detect IE8's incomplete defineProperty implementation
	var descriptors = !fails(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- required for testing
	  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
	});

	var $propertyIsEnumerable$1 = {}.propertyIsEnumerable;
	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var getOwnPropertyDescriptor$2 = Object.getOwnPropertyDescriptor;

	// Nashorn ~ JDK8 bug
	var NASHORN_BUG = getOwnPropertyDescriptor$2 && !$propertyIsEnumerable$1.call({ 1: 2 }, 1);

	// `Object.prototype.propertyIsEnumerable` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.propertyisenumerable
	var f$7 = NASHORN_BUG ? function propertyIsEnumerable(V) {
	  var descriptor = getOwnPropertyDescriptor$2(this, V);
	  return !!descriptor && descriptor.enumerable;
	} : $propertyIsEnumerable$1;

	var objectPropertyIsEnumerable = {
		f: f$7
	};

	var createPropertyDescriptor = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};

	var toString$1 = {}.toString;

	var classofRaw = function (it) {
	  return toString$1.call(it).slice(8, -1);
	};

	var split = ''.split;

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var indexedObject = fails(function () {
	  // throws an error in rhino, see https://github.com/mozilla/rhino/issues/346
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  return !Object('z').propertyIsEnumerable(0);
	}) ? function (it) {
	  return classofRaw(it) == 'String' ? split.call(it, '') : Object(it);
	} : Object;

	// `RequireObjectCoercible` abstract operation
	// https://tc39.es/ecma262/#sec-requireobjectcoercible
	var requireObjectCoercible = function (it) {
	  if (it == undefined) throw TypeError("Can't call method on " + it);
	  return it;
	};

	// toObject with fallback for non-array-like ES3 strings



	var toIndexedObject = function (it) {
	  return indexedObject(requireObjectCoercible(it));
	};

	var isObject = function (it) {
	  return typeof it === 'object' ? it !== null : typeof it === 'function';
	};

	// `ToPrimitive` abstract operation
	// https://tc39.es/ecma262/#sec-toprimitive
	// instead of the ES6 spec version, we didn't implement @@toPrimitive case
	// and the second argument - flag - preferred type is a string
	var toPrimitive = function (input, PREFERRED_STRING) {
	  if (!isObject(input)) return input;
	  var fn, val;
	  if (PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
	  if (typeof (fn = input.valueOf) == 'function' && !isObject(val = fn.call(input))) return val;
	  if (!PREFERRED_STRING && typeof (fn = input.toString) == 'function' && !isObject(val = fn.call(input))) return val;
	  throw TypeError("Can't convert object to primitive value");
	};

	var hasOwnProperty = {}.hasOwnProperty;

	var has$1 = function (it, key) {
	  return hasOwnProperty.call(it, key);
	};

	var document$3 = global$1.document;
	// typeof document.createElement is 'object' in old IE
	var EXISTS = isObject(document$3) && isObject(document$3.createElement);

	var documentCreateElement = function (it) {
	  return EXISTS ? document$3.createElement(it) : {};
	};

	// Thank's IE8 for his funny defineProperty
	var ie8DomDefine = !descriptors && !fails(function () {
	  // eslint-disable-next-line es/no-object-defineproperty -- requied for testing
	  return Object.defineProperty(documentCreateElement('div'), 'a', {
	    get: function () { return 7; }
	  }).a != 7;
	});

	// eslint-disable-next-line es/no-object-getownpropertydescriptor -- safe
	var $getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
	var f$6 = descriptors ? $getOwnPropertyDescriptor$1 : function getOwnPropertyDescriptor(O, P) {
	  O = toIndexedObject(O);
	  P = toPrimitive(P, true);
	  if (ie8DomDefine) try {
	    return $getOwnPropertyDescriptor$1(O, P);
	  } catch (error) { /* empty */ }
	  if (has$1(O, P)) return createPropertyDescriptor(!objectPropertyIsEnumerable.f.call(O, P), O[P]);
	};

	var objectGetOwnPropertyDescriptor = {
		f: f$6
	};

	var replacement = /#|\.prototype\./;

	var isForced = function (feature, detection) {
	  var value = data[normalize(feature)];
	  return value == POLYFILL ? true
	    : value == NATIVE ? false
	    : typeof detection == 'function' ? fails(detection)
	    : !!detection;
	};

	var normalize = isForced.normalize = function (string) {
	  return String(string).replace(replacement, '.').toLowerCase();
	};

	var data = isForced.data = {};
	var NATIVE = isForced.NATIVE = 'N';
	var POLYFILL = isForced.POLYFILL = 'P';

	var isForced_1 = isForced;

	var path = {};

	var aFunction$1 = function (it) {
	  if (typeof it != 'function') {
	    throw TypeError(String(it) + ' is not a function');
	  } return it;
	};

	// optional / simple context binding
	var functionBindContext = function (fn, that, length) {
	  aFunction$1(fn);
	  if (that === undefined) return fn;
	  switch (length) {
	    case 0: return function () {
	      return fn.call(that);
	    };
	    case 1: return function (a) {
	      return fn.call(that, a);
	    };
	    case 2: return function (a, b) {
	      return fn.call(that, a, b);
	    };
	    case 3: return function (a, b, c) {
	      return fn.call(that, a, b, c);
	    };
	  }
	  return function (/* ...args */) {
	    return fn.apply(that, arguments);
	  };
	};

	var anObject = function (it) {
	  if (!isObject(it)) {
	    throw TypeError(String(it) + ' is not an object');
	  } return it;
	};

	// eslint-disable-next-line es/no-object-defineproperty -- safe
	var $defineProperty$1 = Object.defineProperty;

	// `Object.defineProperty` method
	// https://tc39.es/ecma262/#sec-object.defineproperty
	var f$5 = descriptors ? $defineProperty$1 : function defineProperty(O, P, Attributes) {
	  anObject(O);
	  P = toPrimitive(P, true);
	  anObject(Attributes);
	  if (ie8DomDefine) try {
	    return $defineProperty$1(O, P, Attributes);
	  } catch (error) { /* empty */ }
	  if ('get' in Attributes || 'set' in Attributes) throw TypeError('Accessors not supported');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};

	var objectDefineProperty = {
		f: f$5
	};

	var createNonEnumerableProperty = descriptors ? function (object, key, value) {
	  return objectDefineProperty.f(object, key, createPropertyDescriptor(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};

	var getOwnPropertyDescriptor$1 = objectGetOwnPropertyDescriptor.f;






	var wrapConstructor = function (NativeConstructor) {
	  var Wrapper = function (a, b, c) {
	    if (this instanceof NativeConstructor) {
	      switch (arguments.length) {
	        case 0: return new NativeConstructor();
	        case 1: return new NativeConstructor(a);
	        case 2: return new NativeConstructor(a, b);
	      } return new NativeConstructor(a, b, c);
	    } return NativeConstructor.apply(this, arguments);
	  };
	  Wrapper.prototype = NativeConstructor.prototype;
	  return Wrapper;
	};

	/*
	  options.target      - name of the target object
	  options.global      - target is the global object
	  options.stat        - export as static methods of target
	  options.proto       - export as prototype methods of target
	  options.real        - real prototype method for the `pure` version
	  options.forced      - export even if the native feature is available
	  options.bind        - bind methods to the target, required for the `pure` version
	  options.wrap        - wrap constructors to preventing global pollution, required for the `pure` version
	  options.unsafe      - use the simple assignment of property instead of delete + defineProperty
	  options.sham        - add a flag to not completely full polyfills
	  options.enumerable  - export as enumerable property
	  options.noTargetGet - prevent calling a getter on target
	*/
	var _export = function (options, source) {
	  var TARGET = options.target;
	  var GLOBAL = options.global;
	  var STATIC = options.stat;
	  var PROTO = options.proto;

	  var nativeSource = GLOBAL ? global$1 : STATIC ? global$1[TARGET] : (global$1[TARGET] || {}).prototype;

	  var target = GLOBAL ? path : path[TARGET] || (path[TARGET] = {});
	  var targetPrototype = target.prototype;

	  var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
	  var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

	  for (key in source) {
	    FORCED = isForced_1(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
	    // contains in native
	    USE_NATIVE = !FORCED && nativeSource && has$1(nativeSource, key);

	    targetProperty = target[key];

	    if (USE_NATIVE) if (options.noTargetGet) {
	      descriptor = getOwnPropertyDescriptor$1(nativeSource, key);
	      nativeProperty = descriptor && descriptor.value;
	    } else nativeProperty = nativeSource[key];

	    // export native or implementation
	    sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

	    if (USE_NATIVE && typeof targetProperty === typeof sourceProperty) continue;

	    // bind timers to global for call from export context
	    if (options.bind && USE_NATIVE) resultProperty = functionBindContext(sourceProperty, global$1);
	    // wrap global constructors for prevent changs in this version
	    else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor(sourceProperty);
	    // make static versions for prototype methods
	    else if (PROTO && typeof sourceProperty == 'function') resultProperty = functionBindContext(Function.call, sourceProperty);
	    // default case
	    else resultProperty = sourceProperty;

	    // add a flag to not completely full polyfills
	    if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
	      createNonEnumerableProperty(resultProperty, 'sham', true);
	    }

	    target[key] = resultProperty;

	    if (PROTO) {
	      VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
	      if (!has$1(path, VIRTUAL_PROTOTYPE)) {
	        createNonEnumerableProperty(path, VIRTUAL_PROTOTYPE, {});
	      }
	      // export virtual prototype methods
	      path[VIRTUAL_PROTOTYPE][key] = sourceProperty;
	      // export real prototype methods
	      if (options.real && targetPrototype && !targetPrototype[key]) {
	        createNonEnumerableProperty(targetPrototype, key, sourceProperty);
	      }
	    }
	  }
	};

	// `IsArray` abstract operation
	// https://tc39.es/ecma262/#sec-isarray
	// eslint-disable-next-line es/no-array-isarray -- safe
	var isArray$3 = Array.isArray || function isArray(arg) {
	  return classofRaw(arg) == 'Array';
	};

	// `ToObject` abstract operation
	// https://tc39.es/ecma262/#sec-toobject
	var toObject = function (argument) {
	  return Object(requireObjectCoercible(argument));
	};

	var ceil = Math.ceil;
	var floor = Math.floor;

	// `ToInteger` abstract operation
	// https://tc39.es/ecma262/#sec-tointeger
	var toInteger = function (argument) {
	  return isNaN(argument = +argument) ? 0 : (argument > 0 ? floor : ceil)(argument);
	};

	var min$2 = Math.min;

	// `ToLength` abstract operation
	// https://tc39.es/ecma262/#sec-tolength
	var toLength = function (argument) {
	  return argument > 0 ? min$2(toInteger(argument), 0x1FFFFFFFFFFFFF) : 0; // 2 ** 53 - 1 == 9007199254740991
	};

	var createProperty = function (object, key, value) {
	  var propertyKey = toPrimitive(key);
	  if (propertyKey in object) objectDefineProperty.f(object, propertyKey, createPropertyDescriptor(0, value));
	  else object[propertyKey] = value;
	};

	var isPure = true;

	var setGlobal = function (key, value) {
	  try {
	    createNonEnumerableProperty(global$1, key, value);
	  } catch (error) {
	    global$1[key] = value;
	  } return value;
	};

	var SHARED = '__core-js_shared__';
	var store$1 = global$1[SHARED] || setGlobal(SHARED, {});

	var sharedStore = store$1;

	var shared = createCommonjsModule(function (module) {
	(module.exports = function (key, value) {
	  return sharedStore[key] || (sharedStore[key] = value !== undefined ? value : {});
	})('versions', []).push({
	  version: '3.10.0',
	  mode: 'pure' ,
	  copyright: '© 2021 Denis Pushkarev (zloirock.ru)'
	});
	});

	var id$1 = 0;
	var postfix = Math.random();

	var uid = function (key) {
	  return 'Symbol(' + String(key === undefined ? '' : key) + ')_' + (++id$1 + postfix).toString(36);
	};

	var engineIsNode = classofRaw(global$1.process) == 'process';

	var aFunction = function (variable) {
	  return typeof variable == 'function' ? variable : undefined;
	};

	var getBuiltIn = function (namespace, method) {
	  return arguments.length < 2 ? aFunction(path[namespace]) || aFunction(global$1[namespace])
	    : path[namespace] && path[namespace][method] || global$1[namespace] && global$1[namespace][method];
	};

	var engineUserAgent = getBuiltIn('navigator', 'userAgent') || '';

	var process$3 = global$1.process;
	var versions = process$3 && process$3.versions;
	var v8 = versions && versions.v8;
	var match, version;

	if (v8) {
	  match = v8.split('.');
	  version = match[0] + match[1];
	} else if (engineUserAgent) {
	  match = engineUserAgent.match(/Edge\/(\d+)/);
	  if (!match || match[1] >= 74) {
	    match = engineUserAgent.match(/Chrome\/(\d+)/);
	    if (match) version = match[1];
	  }
	}

	var engineV8Version = version && +version;

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- required for testing
	var nativeSymbol = !!Object.getOwnPropertySymbols && !fails(function () {
	  // eslint-disable-next-line es/no-symbol -- required for testing
	  return !Symbol.sham &&
	    // Chrome 38 Symbol has incorrect toString conversion
	    // Chrome 38-40 symbols are not inherited from DOM collections prototypes to instances
	    (engineIsNode ? engineV8Version === 38 : engineV8Version > 37 && engineV8Version < 41);
	});

	/* eslint-disable es/no-symbol -- required for testing */

	var useSymbolAsUid = nativeSymbol
	  && !Symbol.sham
	  && typeof Symbol.iterator == 'symbol';

	var WellKnownSymbolsStore$1 = shared('wks');
	var Symbol$1 = global$1.Symbol;
	var createWellKnownSymbol = useSymbolAsUid ? Symbol$1 : Symbol$1 && Symbol$1.withoutSetter || uid;

	var wellKnownSymbol = function (name) {
	  if (!has$1(WellKnownSymbolsStore$1, name) || !(nativeSymbol || typeof WellKnownSymbolsStore$1[name] == 'string')) {
	    if (nativeSymbol && has$1(Symbol$1, name)) {
	      WellKnownSymbolsStore$1[name] = Symbol$1[name];
	    } else {
	      WellKnownSymbolsStore$1[name] = createWellKnownSymbol('Symbol.' + name);
	    }
	  } return WellKnownSymbolsStore$1[name];
	};

	var SPECIES$5 = wellKnownSymbol('species');

	// `ArraySpeciesCreate` abstract operation
	// https://tc39.es/ecma262/#sec-arrayspeciescreate
	var arraySpeciesCreate = function (originalArray, length) {
	  var C;
	  if (isArray$3(originalArray)) {
	    C = originalArray.constructor;
	    // cross-realm fallback
	    if (typeof C == 'function' && (C === Array || isArray$3(C.prototype))) C = undefined;
	    else if (isObject(C)) {
	      C = C[SPECIES$5];
	      if (C === null) C = undefined;
	    }
	  } return new (C === undefined ? Array : C)(length === 0 ? 0 : length);
	};

	var SPECIES$4 = wellKnownSymbol('species');

	var arrayMethodHasSpeciesSupport = function (METHOD_NAME) {
	  // We can't use this feature detection in V8 since it causes
	  // deoptimization and serious performance degradation
	  // https://github.com/zloirock/core-js/issues/677
	  return engineV8Version >= 51 || !fails(function () {
	    var array = [];
	    var constructor = array.constructor = {};
	    constructor[SPECIES$4] = function () {
	      return { foo: 1 };
	    };
	    return array[METHOD_NAME](Boolean).foo !== 1;
	  });
	};

	var IS_CONCAT_SPREADABLE = wellKnownSymbol('isConcatSpreadable');
	var MAX_SAFE_INTEGER$1 = 0x1FFFFFFFFFFFFF;
	var MAXIMUM_ALLOWED_INDEX_EXCEEDED = 'Maximum allowed index exceeded';

	// We can't use this feature detection in V8 since it causes
	// deoptimization and serious performance degradation
	// https://github.com/zloirock/core-js/issues/679
	var IS_CONCAT_SPREADABLE_SUPPORT = engineV8Version >= 51 || !fails(function () {
	  var array = [];
	  array[IS_CONCAT_SPREADABLE] = false;
	  return array.concat()[0] !== array;
	});

	var SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('concat');

	var isConcatSpreadable = function (O) {
	  if (!isObject(O)) return false;
	  var spreadable = O[IS_CONCAT_SPREADABLE];
	  return spreadable !== undefined ? !!spreadable : isArray$3(O);
	};

	var FORCED$7 = !IS_CONCAT_SPREADABLE_SUPPORT || !SPECIES_SUPPORT;

	// `Array.prototype.concat` method
	// https://tc39.es/ecma262/#sec-array.prototype.concat
	// with adding support of @@isConcatSpreadable and @@species
	_export({ target: 'Array', proto: true, forced: FORCED$7 }, {
	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  concat: function concat(arg) {
	    var O = toObject(this);
	    var A = arraySpeciesCreate(O, 0);
	    var n = 0;
	    var i, k, length, len, E;
	    for (i = -1, length = arguments.length; i < length; i++) {
	      E = i === -1 ? O : arguments[i];
	      if (isConcatSpreadable(E)) {
	        len = toLength(E.length);
	        if (n + len > MAX_SAFE_INTEGER$1) throw TypeError(MAXIMUM_ALLOWED_INDEX_EXCEEDED);
	        for (k = 0; k < len; k++, n++) if (k in E) createProperty(A, n, E[k]);
	      } else {
	        if (n >= MAX_SAFE_INTEGER$1) throw TypeError(MAXIMUM_ALLOWED_INDEX_EXCEEDED);
	        createProperty(A, n++, E);
	      }
	    }
	    A.length = n;
	    return A;
	  }
	});

	var max$2 = Math.max;
	var min$1 = Math.min;

	// Helper for a popular repeating case of the spec:
	// Let integer be ? ToInteger(index).
	// If integer < 0, let result be max((length + integer), 0); else let result be min(integer, length).
	var toAbsoluteIndex = function (index, length) {
	  var integer = toInteger(index);
	  return integer < 0 ? max$2(integer + length, 0) : min$1(integer, length);
	};

	// `Array.prototype.{ indexOf, includes }` methods implementation
	var createMethod$4 = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIndexedObject($this);
	    var length = toLength(O.length);
	    var index = toAbsoluteIndex(fromIndex, length);
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

	var hiddenKeys$1 = {};

	var indexOf$3 = arrayIncludes.indexOf;


	var objectKeysInternal = function (object, names) {
	  var O = toIndexedObject(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) !has$1(hiddenKeys$1, key) && has$1(O, key) && result.push(key);
	  // Don't enum bug & hidden keys
	  while (names.length > i) if (has$1(O, key = names[i++])) {
	    ~indexOf$3(result, key) || result.push(key);
	  }
	  return result;
	};

	// IE8- don't enum bug keys
	var enumBugKeys = [
	  'constructor',
	  'hasOwnProperty',
	  'isPrototypeOf',
	  'propertyIsEnumerable',
	  'toLocaleString',
	  'toString',
	  'valueOf'
	];

	// `Object.keys` method
	// https://tc39.es/ecma262/#sec-object.keys
	// eslint-disable-next-line es/no-object-keys -- safe
	var objectKeys = Object.keys || function keys(O) {
	  return objectKeysInternal(O, enumBugKeys);
	};

	// `Object.defineProperties` method
	// https://tc39.es/ecma262/#sec-object.defineproperties
	// eslint-disable-next-line es/no-object-defineproperties -- safe
	var objectDefineProperties = descriptors ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject(O);
	  var keys = objectKeys(Properties);
	  var length = keys.length;
	  var index = 0;
	  var key;
	  while (length > index) objectDefineProperty.f(O, key = keys[index++], Properties[key]);
	  return O;
	};

	var html = getBuiltIn('document', 'documentElement');

	var keys$3 = shared('keys');

	var sharedKey = function (key) {
	  return keys$3[key] || (keys$3[key] = uid(key));
	};

	var GT = '>';
	var LT = '<';
	var PROTOTYPE$1 = 'prototype';
	var SCRIPT = 'script';
	var IE_PROTO$1 = sharedKey('IE_PROTO');

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
	    /* global ActiveXObject -- old IE */
	    activeXDocument = document.domain && new ActiveXObject('htmlfile');
	  } catch (error) { /* ignore */ }
	  NullProtoObject = activeXDocument ? NullProtoObjectViaActiveX(activeXDocument) : NullProtoObjectViaIFrame();
	  var length = enumBugKeys.length;
	  while (length--) delete NullProtoObject[PROTOTYPE$1][enumBugKeys[length]];
	  return NullProtoObject();
	};

	hiddenKeys$1[IE_PROTO$1] = true;

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	var objectCreate = Object.create || function create(O, Properties) {
	  var result;
	  if (O !== null) {
	    EmptyConstructor[PROTOTYPE$1] = anObject(O);
	    result = new EmptyConstructor();
	    EmptyConstructor[PROTOTYPE$1] = null;
	    // add "__proto__" for Object.getPrototypeOf polyfill
	    result[IE_PROTO$1] = O;
	  } else result = NullProtoObject();
	  return Properties === undefined ? result : objectDefineProperties(result, Properties);
	};

	var hiddenKeys = enumBugKeys.concat('length', 'prototype');

	// `Object.getOwnPropertyNames` method
	// https://tc39.es/ecma262/#sec-object.getownpropertynames
	// eslint-disable-next-line es/no-object-getownpropertynames -- safe
	var f$4 = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
	  return objectKeysInternal(O, hiddenKeys);
	};

	var objectGetOwnPropertyNames = {
		f: f$4
	};

	/* eslint-disable es/no-object-getownpropertynames -- safe */

	var $getOwnPropertyNames$1 = objectGetOwnPropertyNames.f;

	var toString = {}.toString;

	var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
	  ? Object.getOwnPropertyNames(window) : [];

	var getWindowNames = function (it) {
	  try {
	    return $getOwnPropertyNames$1(it);
	  } catch (error) {
	    return windowNames.slice();
	  }
	};

	// fallback for IE11 buggy Object.getOwnPropertyNames with iframe and window
	var f$3 = function getOwnPropertyNames(it) {
	  return windowNames && toString.call(it) == '[object Window]'
	    ? getWindowNames(it)
	    : $getOwnPropertyNames$1(toIndexedObject(it));
	};

	var objectGetOwnPropertyNamesExternal = {
		f: f$3
	};

	// eslint-disable-next-line es/no-object-getownpropertysymbols -- safe
	var f$2 = Object.getOwnPropertySymbols;

	var objectGetOwnPropertySymbols = {
		f: f$2
	};

	var redefine = function (target, key, value, options) {
	  if (options && options.enumerable) target[key] = value;
	  else createNonEnumerableProperty(target, key, value);
	};

	var f$1 = wellKnownSymbol;

	var wellKnownSymbolWrapped = {
		f: f$1
	};

	var defineProperty$6 = objectDefineProperty.f;

	var defineWellKnownSymbol = function (NAME) {
	  var Symbol = path.Symbol || (path.Symbol = {});
	  if (!has$1(Symbol, NAME)) defineProperty$6(Symbol, NAME, {
	    value: wellKnownSymbolWrapped.f(NAME)
	  });
	};

	var TO_STRING_TAG$3 = wellKnownSymbol('toStringTag');
	var test$1 = {};

	test$1[TO_STRING_TAG$3] = 'z';

	var toStringTagSupport = String(test$1) === '[object z]';

	var TO_STRING_TAG$2 = wellKnownSymbol('toStringTag');
	// ES3 wrong here
	var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) == 'Arguments';

	// fallback for IE11 Script Access Denied error
	var tryGet = function (it, key) {
	  try {
	    return it[key];
	  } catch (error) { /* empty */ }
	};

	// getting tag from ES6+ `Object.prototype.toString`
	var classof = toStringTagSupport ? classofRaw : function (it) {
	  var O, tag, result;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    // @@toStringTag case
	    : typeof (tag = tryGet(O = Object(it), TO_STRING_TAG$2)) == 'string' ? tag
	    // builtinTag case
	    : CORRECT_ARGUMENTS ? classofRaw(O)
	    // ES3 arguments fallback
	    : (result = classofRaw(O)) == 'Object' && typeof O.callee == 'function' ? 'Arguments' : result;
	};

	// `Object.prototype.toString` method implementation
	// https://tc39.es/ecma262/#sec-object.prototype.tostring
	var objectToString = toStringTagSupport ? {}.toString : function toString() {
	  return '[object ' + classof(this) + ']';
	};

	var defineProperty$5 = objectDefineProperty.f;





	var TO_STRING_TAG$1 = wellKnownSymbol('toStringTag');

	var setToStringTag = function (it, TAG, STATIC, SET_METHOD) {
	  if (it) {
	    var target = STATIC ? it : it.prototype;
	    if (!has$1(target, TO_STRING_TAG$1)) {
	      defineProperty$5(target, TO_STRING_TAG$1, { configurable: true, value: TAG });
	    }
	    if (SET_METHOD && !toStringTagSupport) {
	      createNonEnumerableProperty(target, 'toString', objectToString);
	    }
	  }
	};

	var functionToString = Function.toString;

	// this helper broken in `3.4.1-3.4.4`, so we can't use `shared` helper
	if (typeof sharedStore.inspectSource != 'function') {
	  sharedStore.inspectSource = function (it) {
	    return functionToString.call(it);
	  };
	}

	var inspectSource = sharedStore.inspectSource;

	var WeakMap$1 = global$1.WeakMap;

	var nativeWeakMap = typeof WeakMap$1 === 'function' && /native code/.test(inspectSource(WeakMap$1));

	var WeakMap = global$1.WeakMap;
	var set$4, get$1, has;

	var enforce = function (it) {
	  return has(it) ? get$1(it) : set$4(it, {});
	};

	var getterFor = function (TYPE) {
	  return function (it) {
	    var state;
	    if (!isObject(it) || (state = get$1(it)).type !== TYPE) {
	      throw TypeError('Incompatible receiver, ' + TYPE + ' required');
	    } return state;
	  };
	};

	if (nativeWeakMap) {
	  var store = sharedStore.state || (sharedStore.state = new WeakMap());
	  var wmget = store.get;
	  var wmhas = store.has;
	  var wmset = store.set;
	  set$4 = function (it, metadata) {
	    metadata.facade = it;
	    wmset.call(store, it, metadata);
	    return metadata;
	  };
	  get$1 = function (it) {
	    return wmget.call(store, it) || {};
	  };
	  has = function (it) {
	    return wmhas.call(store, it);
	  };
	} else {
	  var STATE = sharedKey('state');
	  hiddenKeys$1[STATE] = true;
	  set$4 = function (it, metadata) {
	    metadata.facade = it;
	    createNonEnumerableProperty(it, STATE, metadata);
	    return metadata;
	  };
	  get$1 = function (it) {
	    return has$1(it, STATE) ? it[STATE] : {};
	  };
	  has = function (it) {
	    return has$1(it, STATE);
	  };
	}

	var internalState = {
	  set: set$4,
	  get: get$1,
	  has: has,
	  enforce: enforce,
	  getterFor: getterFor
	};

	var push = [].push;

	// `Array.prototype.{ forEach, map, filter, some, every, find, findIndex, filterOut }` methods implementation
	var createMethod$3 = function (TYPE) {
	  var IS_MAP = TYPE == 1;
	  var IS_FILTER = TYPE == 2;
	  var IS_SOME = TYPE == 3;
	  var IS_EVERY = TYPE == 4;
	  var IS_FIND_INDEX = TYPE == 6;
	  var IS_FILTER_OUT = TYPE == 7;
	  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
	  return function ($this, callbackfn, that, specificCreate) {
	    var O = toObject($this);
	    var self = indexedObject(O);
	    var boundFunction = functionBindContext(callbackfn, that, 3);
	    var length = toLength(self.length);
	    var index = 0;
	    var create = specificCreate || arraySpeciesCreate;
	    var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_OUT ? create($this, 0) : undefined;
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
	          case 2: push.call(target, value); // filter
	        } else switch (TYPE) {
	          case 4: return false;             // every
	          case 7: push.call(target, value); // filterOut
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
	  // `Array.prototype.filterOut` method
	  // https://github.com/tc39/proposal-array-filtering
	  filterOut: createMethod$3(7)
	};

	var $forEach$1 = arrayIteration.forEach;

	var HIDDEN = sharedKey('hidden');
	var SYMBOL = 'Symbol';
	var PROTOTYPE = 'prototype';
	var TO_PRIMITIVE = wellKnownSymbol('toPrimitive');
	var setInternalState$6 = internalState.set;
	var getInternalState$3 = internalState.getterFor(SYMBOL);
	var ObjectPrototype$1 = Object[PROTOTYPE];
	var $Symbol = global$1.Symbol;
	var $stringify$1 = getBuiltIn('JSON', 'stringify');
	var nativeGetOwnPropertyDescriptor$1 = objectGetOwnPropertyDescriptor.f;
	var nativeDefineProperty = objectDefineProperty.f;
	var nativeGetOwnPropertyNames = objectGetOwnPropertyNamesExternal.f;
	var nativePropertyIsEnumerable = objectPropertyIsEnumerable.f;
	var AllSymbols = shared('symbols');
	var ObjectPrototypeSymbols = shared('op-symbols');
	var StringToSymbolRegistry = shared('string-to-symbol-registry');
	var SymbolToStringRegistry = shared('symbol-to-string-registry');
	var WellKnownSymbolsStore = shared('wks');
	var QObject = global$1.QObject;
	// Don't use setters in Qt Script, https://github.com/zloirock/core-js/issues/173
	var USE_SETTER = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

	// fallback for old Android, https://code.google.com/p/v8/issues/detail?id=687
	var setSymbolDescriptor = descriptors && fails(function () {
	  return objectCreate(nativeDefineProperty({}, 'a', {
	    get: function () { return nativeDefineProperty(this, 'a', { value: 7 }).a; }
	  })).a != 7;
	}) ? function (O, P, Attributes) {
	  var ObjectPrototypeDescriptor = nativeGetOwnPropertyDescriptor$1(ObjectPrototype$1, P);
	  if (ObjectPrototypeDescriptor) delete ObjectPrototype$1[P];
	  nativeDefineProperty(O, P, Attributes);
	  if (ObjectPrototypeDescriptor && O !== ObjectPrototype$1) {
	    nativeDefineProperty(ObjectPrototype$1, P, ObjectPrototypeDescriptor);
	  }
	} : nativeDefineProperty;

	var wrap$1 = function (tag, description) {
	  var symbol = AllSymbols[tag] = objectCreate($Symbol[PROTOTYPE]);
	  setInternalState$6(symbol, {
	    type: SYMBOL,
	    tag: tag,
	    description: description
	  });
	  if (!descriptors) symbol.description = description;
	  return symbol;
	};

	var isSymbol = useSymbolAsUid ? function (it) {
	  return typeof it == 'symbol';
	} : function (it) {
	  return Object(it) instanceof $Symbol;
	};

	var $defineProperty = function defineProperty(O, P, Attributes) {
	  if (O === ObjectPrototype$1) $defineProperty(ObjectPrototypeSymbols, P, Attributes);
	  anObject(O);
	  var key = toPrimitive(P, true);
	  anObject(Attributes);
	  if (has$1(AllSymbols, key)) {
	    if (!Attributes.enumerable) {
	      if (!has$1(O, HIDDEN)) nativeDefineProperty(O, HIDDEN, createPropertyDescriptor(1, {}));
	      O[HIDDEN][key] = true;
	    } else {
	      if (has$1(O, HIDDEN) && O[HIDDEN][key]) O[HIDDEN][key] = false;
	      Attributes = objectCreate(Attributes, { enumerable: createPropertyDescriptor(0, false) });
	    } return setSymbolDescriptor(O, key, Attributes);
	  } return nativeDefineProperty(O, key, Attributes);
	};

	var $defineProperties = function defineProperties(O, Properties) {
	  anObject(O);
	  var properties = toIndexedObject(Properties);
	  var keys = objectKeys(properties).concat($getOwnPropertySymbols(properties));
	  $forEach$1(keys, function (key) {
	    if (!descriptors || $propertyIsEnumerable.call(properties, key)) $defineProperty(O, key, properties[key]);
	  });
	  return O;
	};

	var $create = function create(O, Properties) {
	  return Properties === undefined ? objectCreate(O) : $defineProperties(objectCreate(O), Properties);
	};

	var $propertyIsEnumerable = function propertyIsEnumerable(V) {
	  var P = toPrimitive(V, true);
	  var enumerable = nativePropertyIsEnumerable.call(this, P);
	  if (this === ObjectPrototype$1 && has$1(AllSymbols, P) && !has$1(ObjectPrototypeSymbols, P)) return false;
	  return enumerable || !has$1(this, P) || !has$1(AllSymbols, P) || has$1(this, HIDDEN) && this[HIDDEN][P] ? enumerable : true;
	};

	var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(O, P) {
	  var it = toIndexedObject(O);
	  var key = toPrimitive(P, true);
	  if (it === ObjectPrototype$1 && has$1(AllSymbols, key) && !has$1(ObjectPrototypeSymbols, key)) return;
	  var descriptor = nativeGetOwnPropertyDescriptor$1(it, key);
	  if (descriptor && has$1(AllSymbols, key) && !(has$1(it, HIDDEN) && it[HIDDEN][key])) {
	    descriptor.enumerable = true;
	  }
	  return descriptor;
	};

	var $getOwnPropertyNames = function getOwnPropertyNames(O) {
	  var names = nativeGetOwnPropertyNames(toIndexedObject(O));
	  var result = [];
	  $forEach$1(names, function (key) {
	    if (!has$1(AllSymbols, key) && !has$1(hiddenKeys$1, key)) result.push(key);
	  });
	  return result;
	};

	var $getOwnPropertySymbols = function getOwnPropertySymbols(O) {
	  var IS_OBJECT_PROTOTYPE = O === ObjectPrototype$1;
	  var names = nativeGetOwnPropertyNames(IS_OBJECT_PROTOTYPE ? ObjectPrototypeSymbols : toIndexedObject(O));
	  var result = [];
	  $forEach$1(names, function (key) {
	    if (has$1(AllSymbols, key) && (!IS_OBJECT_PROTOTYPE || has$1(ObjectPrototype$1, key))) {
	      result.push(AllSymbols[key]);
	    }
	  });
	  return result;
	};

	// `Symbol` constructor
	// https://tc39.es/ecma262/#sec-symbol-constructor
	if (!nativeSymbol) {
	  $Symbol = function Symbol() {
	    if (this instanceof $Symbol) throw TypeError('Symbol is not a constructor');
	    var description = !arguments.length || arguments[0] === undefined ? undefined : String(arguments[0]);
	    var tag = uid(description);
	    var setter = function (value) {
	      if (this === ObjectPrototype$1) setter.call(ObjectPrototypeSymbols, value);
	      if (has$1(this, HIDDEN) && has$1(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
	      setSymbolDescriptor(this, tag, createPropertyDescriptor(1, value));
	    };
	    if (descriptors && USE_SETTER) setSymbolDescriptor(ObjectPrototype$1, tag, { configurable: true, set: setter });
	    return wrap$1(tag, description);
	  };

	  redefine($Symbol[PROTOTYPE], 'toString', function toString() {
	    return getInternalState$3(this).tag;
	  });

	  redefine($Symbol, 'withoutSetter', function (description) {
	    return wrap$1(uid(description), description);
	  });

	  objectPropertyIsEnumerable.f = $propertyIsEnumerable;
	  objectDefineProperty.f = $defineProperty;
	  objectGetOwnPropertyDescriptor.f = $getOwnPropertyDescriptor;
	  objectGetOwnPropertyNames.f = objectGetOwnPropertyNamesExternal.f = $getOwnPropertyNames;
	  objectGetOwnPropertySymbols.f = $getOwnPropertySymbols;

	  wellKnownSymbolWrapped.f = function (name) {
	    return wrap$1(wellKnownSymbol(name), name);
	  };

	  if (descriptors) {
	    // https://github.com/tc39/proposal-Symbol-description
	    nativeDefineProperty($Symbol[PROTOTYPE], 'description', {
	      configurable: true,
	      get: function description() {
	        return getInternalState$3(this).description;
	      }
	    });
	  }
	}

	_export({ global: true, wrap: true, forced: !nativeSymbol, sham: !nativeSymbol }, {
	  Symbol: $Symbol
	});

	$forEach$1(objectKeys(WellKnownSymbolsStore), function (name) {
	  defineWellKnownSymbol(name);
	});

	_export({ target: SYMBOL, stat: true, forced: !nativeSymbol }, {
	  // `Symbol.for` method
	  // https://tc39.es/ecma262/#sec-symbol.for
	  'for': function (key) {
	    var string = String(key);
	    if (has$1(StringToSymbolRegistry, string)) return StringToSymbolRegistry[string];
	    var symbol = $Symbol(string);
	    StringToSymbolRegistry[string] = symbol;
	    SymbolToStringRegistry[symbol] = string;
	    return symbol;
	  },
	  // `Symbol.keyFor` method
	  // https://tc39.es/ecma262/#sec-symbol.keyfor
	  keyFor: function keyFor(sym) {
	    if (!isSymbol(sym)) throw TypeError(sym + ' is not a symbol');
	    if (has$1(SymbolToStringRegistry, sym)) return SymbolToStringRegistry[sym];
	  },
	  useSetter: function () { USE_SETTER = true; },
	  useSimple: function () { USE_SETTER = false; }
	});

	_export({ target: 'Object', stat: true, forced: !nativeSymbol, sham: !descriptors }, {
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

	_export({ target: 'Object', stat: true, forced: !nativeSymbol }, {
	  // `Object.getOwnPropertyNames` method
	  // https://tc39.es/ecma262/#sec-object.getownpropertynames
	  getOwnPropertyNames: $getOwnPropertyNames,
	  // `Object.getOwnPropertySymbols` method
	  // https://tc39.es/ecma262/#sec-object.getownpropertysymbols
	  getOwnPropertySymbols: $getOwnPropertySymbols
	});

	// Chrome 38 and 39 `Object.getOwnPropertySymbols` fails on primitives
	// https://bugs.chromium.org/p/v8/issues/detail?id=3443
	_export({ target: 'Object', stat: true, forced: fails(function () { objectGetOwnPropertySymbols.f(1); }) }, {
	  getOwnPropertySymbols: function getOwnPropertySymbols(it) {
	    return objectGetOwnPropertySymbols.f(toObject(it));
	  }
	});

	// `JSON.stringify` method behavior with symbols
	// https://tc39.es/ecma262/#sec-json.stringify
	if ($stringify$1) {
	  var FORCED_JSON_STRINGIFY = !nativeSymbol || fails(function () {
	    var symbol = $Symbol();
	    // MS Edge converts symbol values to JSON as {}
	    return $stringify$1([symbol]) != '[null]'
	      // WebKit converts symbol values to JSON as null
	      || $stringify$1({ a: symbol }) != '{}'
	      // V8 throws on boxed symbols
	      || $stringify$1(Object(symbol)) != '{}';
	  });

	  _export({ target: 'JSON', stat: true, forced: FORCED_JSON_STRINGIFY }, {
	    // eslint-disable-next-line no-unused-vars -- required for `.length`
	    stringify: function stringify(it, replacer, space) {
	      var args = [it];
	      var index = 1;
	      var $replacer;
	      while (arguments.length > index) args.push(arguments[index++]);
	      $replacer = replacer;
	      if (!isObject(replacer) && it === undefined || isSymbol(it)) return; // IE8 returns string on undefined
	      if (!isArray$3(replacer)) replacer = function (key, value) {
	        if (typeof $replacer == 'function') value = $replacer.call(this, key, value);
	        if (!isSymbol(value)) return value;
	      };
	      args[1] = replacer;
	      return $stringify$1.apply(null, args);
	    }
	  });
	}

	// `Symbol.prototype[@@toPrimitive]` method
	// https://tc39.es/ecma262/#sec-symbol.prototype-@@toprimitive
	if (!$Symbol[PROTOTYPE][TO_PRIMITIVE]) {
	  createNonEnumerableProperty($Symbol[PROTOTYPE], TO_PRIMITIVE, $Symbol[PROTOTYPE].valueOf);
	}
	// `Symbol.prototype[@@toStringTag]` property
	// https://tc39.es/ecma262/#sec-symbol.prototype-@@tostringtag
	setToStringTag($Symbol, SYMBOL);

	hiddenKeys$1[HIDDEN] = true;

	// `Symbol.asyncIterator` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.asynciterator
	defineWellKnownSymbol('asyncIterator');

	// `Symbol.hasInstance` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.hasinstance
	defineWellKnownSymbol('hasInstance');

	// `Symbol.isConcatSpreadable` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.isconcatspreadable
	defineWellKnownSymbol('isConcatSpreadable');

	// `Symbol.iterator` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.iterator
	defineWellKnownSymbol('iterator');

	// `Symbol.match` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.match
	defineWellKnownSymbol('match');

	// `Symbol.matchAll` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.matchall
	defineWellKnownSymbol('matchAll');

	// `Symbol.replace` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.replace
	defineWellKnownSymbol('replace');

	// `Symbol.search` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.search
	defineWellKnownSymbol('search');

	// `Symbol.species` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.species
	defineWellKnownSymbol('species');

	// `Symbol.split` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.split
	defineWellKnownSymbol('split');

	// `Symbol.toPrimitive` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.toprimitive
	defineWellKnownSymbol('toPrimitive');

	// `Symbol.toStringTag` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.tostringtag
	defineWellKnownSymbol('toStringTag');

	// `Symbol.unscopables` well-known symbol
	// https://tc39.es/ecma262/#sec-symbol.unscopables
	defineWellKnownSymbol('unscopables');

	// JSON[@@toStringTag] property
	// https://tc39.es/ecma262/#sec-json-@@tostringtag
	setToStringTag(global$1.JSON, 'JSON', true);

	var symbol$2 = path.Symbol;

	// `Symbol.asyncDispose` well-known symbol
	// https://github.com/tc39/proposal-using-statement
	defineWellKnownSymbol('asyncDispose');

	// `Symbol.dispose` well-known symbol
	// https://github.com/tc39/proposal-using-statement
	defineWellKnownSymbol('dispose');

	// `Symbol.observable` well-known symbol
	// https://github.com/tc39/proposal-observable
	defineWellKnownSymbol('observable');

	// `Symbol.patternMatch` well-known symbol
	// https://github.com/tc39/proposal-pattern-matching
	defineWellKnownSymbol('patternMatch');

	// TODO: remove from `core-js@4`


	defineWellKnownSymbol('replaceAll');

	// TODO: Remove from `core-js@4`


	var symbol$1 = symbol$2;

	var symbol = symbol$1;

	// `String.prototype.{ codePointAt, at }` methods implementation
	var createMethod$2 = function (CONVERT_TO_STRING) {
	  return function ($this, pos) {
	    var S = String(requireObjectCoercible($this));
	    var position = toInteger(pos);
	    var size = S.length;
	    var first, second;
	    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
	    first = S.charCodeAt(position);
	    return first < 0xD800 || first > 0xDBFF || position + 1 === size
	      || (second = S.charCodeAt(position + 1)) < 0xDC00 || second > 0xDFFF
	        ? CONVERT_TO_STRING ? S.charAt(position) : first
	        : CONVERT_TO_STRING ? S.slice(position, position + 2) : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
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

	var correctPrototypeGetter = !fails(function () {
	  function F() { /* empty */ }
	  F.prototype.constructor = null;
	  // eslint-disable-next-line es/no-object-getprototypeof -- required for testing
	  return Object.getPrototypeOf(new F()) !== F.prototype;
	});

	var IE_PROTO = sharedKey('IE_PROTO');
	var ObjectPrototype = Object.prototype;

	// `Object.getPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.getprototypeof
	// eslint-disable-next-line es/no-object-getprototypeof -- safe
	var objectGetPrototypeOf = correctPrototypeGetter ? Object.getPrototypeOf : function (O) {
	  O = toObject(O);
	  if (has$1(O, IE_PROTO)) return O[IE_PROTO];
	  if (typeof O.constructor == 'function' && O instanceof O.constructor) {
	    return O.constructor.prototype;
	  } return O instanceof Object ? ObjectPrototype : null;
	};

	var ITERATOR$5 = wellKnownSymbol('iterator');
	var BUGGY_SAFARI_ITERATORS$1 = false;

	var returnThis$2 = function () { return this; };

	// `%IteratorPrototype%` object
	// https://tc39.es/ecma262/#sec-%iteratorprototype%-object
	var IteratorPrototype$2, PrototypeOfArrayIteratorPrototype, arrayIterator;

	/* eslint-disable es/no-array-prototype-keys -- safe */
	if ([].keys) {
	  arrayIterator = [].keys();
	  // Safari 8 has buggy iterators w/o `next`
	  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS$1 = true;
	  else {
	    PrototypeOfArrayIteratorPrototype = objectGetPrototypeOf(objectGetPrototypeOf(arrayIterator));
	    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$2 = PrototypeOfArrayIteratorPrototype;
	  }
	}

	var NEW_ITERATOR_PROTOTYPE = IteratorPrototype$2 == undefined || fails(function () {
	  var test = {};
	  // FF44- legacy iterators case
	  return IteratorPrototype$2[ITERATOR$5].call(test) !== test;
	});

	if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$2 = {};

	// 25.1.2.1.1 %IteratorPrototype%[@@iterator]()
	if ((NEW_ITERATOR_PROTOTYPE) && !has$1(IteratorPrototype$2, ITERATOR$5)) {
	  createNonEnumerableProperty(IteratorPrototype$2, ITERATOR$5, returnThis$2);
	}

	var iteratorsCore = {
	  IteratorPrototype: IteratorPrototype$2,
	  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
	};

	var iterators = {};

	var IteratorPrototype$1 = iteratorsCore.IteratorPrototype;





	var returnThis$1 = function () { return this; };

	var createIteratorConstructor = function (IteratorConstructor, NAME, next) {
	  var TO_STRING_TAG = NAME + ' Iterator';
	  IteratorConstructor.prototype = objectCreate(IteratorPrototype$1, { next: createPropertyDescriptor(1, next) });
	  setToStringTag(IteratorConstructor, TO_STRING_TAG, false, true);
	  iterators[TO_STRING_TAG] = returnThis$1;
	  return IteratorConstructor;
	};

	var aPossiblePrototype = function (it) {
	  if (!isObject(it) && it !== null) {
	    throw TypeError("Can't set " + String(it) + ' as a prototype');
	  } return it;
	};

	/* eslint-disable no-proto -- safe */

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
	    setter = Object.getOwnPropertyDescriptor(Object.prototype, '__proto__').set;
	    setter.call(test, []);
	    CORRECT_SETTER = test instanceof Array;
	  } catch (error) { /* empty */ }
	  return function setPrototypeOf(O, proto) {
	    anObject(O);
	    aPossiblePrototype(proto);
	    if (CORRECT_SETTER) setter.call(O, proto);
	    else O.__proto__ = proto;
	    return O;
	  };
	}() : undefined);

	var IteratorPrototype = iteratorsCore.IteratorPrototype;
	var BUGGY_SAFARI_ITERATORS = iteratorsCore.BUGGY_SAFARI_ITERATORS;
	var ITERATOR$4 = wellKnownSymbol('iterator');
	var KEYS = 'keys';
	var VALUES = 'values';
	var ENTRIES = 'entries';

	var returnThis = function () { return this; };

	var defineIterator = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
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
	  var nativeIterator = IterablePrototype[ITERATOR$4]
	    || IterablePrototype['@@iterator']
	    || DEFAULT && IterablePrototype[DEFAULT];
	  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
	  var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
	  var CurrentIteratorPrototype, methods, KEY;

	  // fix native
	  if (anyNativeIterator) {
	    CurrentIteratorPrototype = objectGetPrototypeOf(anyNativeIterator.call(new Iterable()));
	    if (IteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
	      // Set @@toStringTag to native iterators
	      setToStringTag(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
	      iterators[TO_STRING_TAG] = returnThis;
	    }
	  }

	  // fix Array#{values, @@iterator}.name in V8 / FF
	  if (DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
	    INCORRECT_VALUES_NAME = true;
	    defaultIterator = function values() { return nativeIterator.call(this); };
	  }

	  // define iterator
	  if ((FORCED) && IterablePrototype[ITERATOR$4] !== defaultIterator) {
	    createNonEnumerableProperty(IterablePrototype, ITERATOR$4, defaultIterator);
	  }
	  iterators[NAME] = defaultIterator;

	  // export additional methods
	  if (DEFAULT) {
	    methods = {
	      values: getIterationMethod(VALUES),
	      keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
	      entries: getIterationMethod(ENTRIES)
	    };
	    if (FORCED) for (KEY in methods) {
	      if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
	        redefine(IterablePrototype, KEY, methods[KEY]);
	      }
	    } else _export({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
	  }

	  return methods;
	};

	var charAt = stringMultibyte.charAt;



	var STRING_ITERATOR = 'String Iterator';
	var setInternalState$5 = internalState.set;
	var getInternalState$2 = internalState.getterFor(STRING_ITERATOR);

	// `String.prototype[@@iterator]` method
	// https://tc39.es/ecma262/#sec-string.prototype-@@iterator
	defineIterator(String, 'String', function (iterated) {
	  setInternalState$5(this, {
	    type: STRING_ITERATOR,
	    string: String(iterated),
	    index: 0
	  });
	// `%StringIteratorPrototype%.next` method
	// https://tc39.es/ecma262/#sec-%stringiteratorprototype%.next
	}, function next() {
	  var state = getInternalState$2(this);
	  var string = state.string;
	  var index = state.index;
	  var point;
	  if (index >= string.length) return { value: undefined, done: true };
	  point = charAt(string, index);
	  state.index += point.length;
	  return { value: point, done: false };
	});

	var ARRAY_ITERATOR = 'Array Iterator';
	var setInternalState$4 = internalState.set;
	var getInternalState$1 = internalState.getterFor(ARRAY_ITERATOR);

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
	defineIterator(Array, 'Array', function (iterated, kind) {
	  setInternalState$4(this, {
	    type: ARRAY_ITERATOR,
	    target: toIndexedObject(iterated), // target
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
	    return { value: undefined, done: true };
	  }
	  if (kind == 'keys') return { value: index, done: false };
	  if (kind == 'values') return { value: target[index], done: false };
	  return { value: [index, target[index]], done: false };
	}, 'values');

	// argumentsList[@@iterator] is %ArrayProto_values%
	// https://tc39.es/ecma262/#sec-createunmappedargumentsobject
	// https://tc39.es/ecma262/#sec-createmappedargumentsobject
	iterators.Arguments = iterators.Array;

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

	var TO_STRING_TAG = wellKnownSymbol('toStringTag');

	for (var COLLECTION_NAME in domIterables) {
	  var Collection = global$1[COLLECTION_NAME];
	  var CollectionPrototype = Collection && Collection.prototype;
	  if (CollectionPrototype && classof(CollectionPrototype) !== TO_STRING_TAG) {
	    createNonEnumerableProperty(CollectionPrototype, TO_STRING_TAG, COLLECTION_NAME);
	  }
	  iterators[COLLECTION_NAME] = iterators.Array;
	}

	var iterator$2 = wellKnownSymbolWrapped.f('iterator');

	var iterator$1 = iterator$2;

	var iterator = iterator$1;

	function _typeof(obj) {
	  "@babel/helpers - typeof";

	  if (typeof symbol === "function" && typeof iterator === "symbol") {
	    _typeof = function _typeof(obj) {
	      return typeof obj;
	    };
	  } else {
	    _typeof = function _typeof(obj) {
	      return obj && typeof symbol === "function" && obj.constructor === symbol && obj !== symbol.prototype ? "symbol" : typeof obj;
	    };
	  }

	  return _typeof(obj);
	}

	function _classCallCheck(instance, Constructor) {
	  if (!(instance instanceof Constructor)) {
	    throw new TypeError("Cannot call a class as a function");
	  }
	}

	// `Object.defineProperty` method
	// https://tc39.es/ecma262/#sec-object.defineproperty
	_export({ target: 'Object', stat: true, forced: !descriptors, sham: !descriptors }, {
	  defineProperty: objectDefineProperty.f
	});

	var defineProperty_1 = createCommonjsModule(function (module) {
	var Object = path.Object;

	var defineProperty = module.exports = function defineProperty(it, key, desc) {
	  return Object.defineProperty(it, key, desc);
	};

	if (Object.defineProperty.sham) defineProperty.sham = true;
	});

	var defineProperty$4 = defineProperty_1;

	var defineProperty$3 = defineProperty$4;

	function _defineProperties(target, props) {
	  for (var i = 0; i < props.length; i++) {
	    var descriptor = props[i];
	    descriptor.enumerable = descriptor.enumerable || false;
	    descriptor.configurable = true;
	    if ("value" in descriptor) descriptor.writable = true;

	    defineProperty$3(target, descriptor.key, descriptor);
	  }
	}

	function _createClass(Constructor, protoProps, staticProps) {
	  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
	  if (staticProps) _defineProperties(Constructor, staticProps);
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

	var ITERATOR$3 = wellKnownSymbol('iterator');
	var ArrayPrototype$c = Array.prototype;

	// check on default Array iterator
	var isArrayIteratorMethod = function (it) {
	  return it !== undefined && (iterators.Array === it || ArrayPrototype$c[ITERATOR$3] === it);
	};

	var ITERATOR$2 = wellKnownSymbol('iterator');

	var getIteratorMethod = function (it) {
	  if (it != undefined) return it[ITERATOR$2]
	    || it['@@iterator']
	    || iterators[classof(it)];
	};

	var iteratorClose = function (iterator) {
	  var returnMethod = iterator['return'];
	  if (returnMethod !== undefined) {
	    return anObject(returnMethod.call(iterator)).value;
	  }
	};

	var Result = function (stopped, result) {
	  this.stopped = stopped;
	  this.result = result;
	};

	var iterate = function (iterable, unboundFunction, options) {
	  var that = options && options.that;
	  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
	  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
	  var INTERRUPTED = !!(options && options.INTERRUPTED);
	  var fn = functionBindContext(unboundFunction, that, 1 + AS_ENTRIES + INTERRUPTED);
	  var iterator, iterFn, index, length, result, next, step;

	  var stop = function (condition) {
	    if (iterator) iteratorClose(iterator);
	    return new Result(true, condition);
	  };

	  var callFn = function (value) {
	    if (AS_ENTRIES) {
	      anObject(value);
	      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
	    } return INTERRUPTED ? fn(value, stop) : fn(value);
	  };

	  if (IS_ITERATOR) {
	    iterator = iterable;
	  } else {
	    iterFn = getIteratorMethod(iterable);
	    if (typeof iterFn != 'function') throw TypeError('Target is not iterable');
	    // optimisation for array iterators
	    if (isArrayIteratorMethod(iterFn)) {
	      for (index = 0, length = toLength(iterable.length); length > index; index++) {
	        result = callFn(iterable[index]);
	        if (result && result instanceof Result) return result;
	      } return new Result(false);
	    }
	    iterator = iterFn.call(iterable);
	  }

	  next = iterator.next;
	  while (!(step = next.call(iterator)).done) {
	    try {
	      result = callFn(step.value);
	    } catch (error) {
	      iteratorClose(iterator);
	      throw error;
	    }
	    if (typeof result == 'object' && result && result instanceof Result) return result;
	  } return new Result(false);
	};

	var $AggregateError = function AggregateError(errors, message) {
	  var that = this;
	  if (!(that instanceof $AggregateError)) return new $AggregateError(errors, message);
	  if (objectSetPrototypeOf) {
	    // eslint-disable-next-line unicorn/error-message -- expected
	    that = objectSetPrototypeOf(new Error(undefined), objectGetPrototypeOf(that));
	  }
	  if (message !== undefined) createNonEnumerableProperty(that, 'message', String(message));
	  var errorsArray = [];
	  iterate(errors, errorsArray.push, { that: errorsArray });
	  createNonEnumerableProperty(that, 'errors', errorsArray);
	  return that;
	};

	$AggregateError.prototype = objectCreate(Error.prototype, {
	  constructor: createPropertyDescriptor(5, $AggregateError),
	  message: createPropertyDescriptor(5, ''),
	  name: createPropertyDescriptor(5, 'AggregateError')
	});

	// `AggregateError` constructor
	// https://tc39.es/ecma262/#sec-aggregate-error-constructor
	_export({ global: true }, {
	  AggregateError: $AggregateError
	});

	var nativePromiseConstructor = global$1.Promise;

	var redefineAll = function (target, src, options) {
	  for (var key in src) {
	    if (options && options.unsafe && target[key]) target[key] = src[key];
	    else redefine(target, key, src[key], options);
	  } return target;
	};

	var SPECIES$3 = wellKnownSymbol('species');

	var setSpecies = function (CONSTRUCTOR_NAME) {
	  var Constructor = getBuiltIn(CONSTRUCTOR_NAME);
	  var defineProperty = objectDefineProperty.f;

	  if (descriptors && Constructor && !Constructor[SPECIES$3]) {
	    defineProperty(Constructor, SPECIES$3, {
	      configurable: true,
	      get: function () { return this; }
	    });
	  }
	};

	var anInstance = function (it, Constructor, name) {
	  if (!(it instanceof Constructor)) {
	    throw TypeError('Incorrect ' + (name ? name + ' ' : '') + 'invocation');
	  } return it;
	};

	var ITERATOR$1 = wellKnownSymbol('iterator');
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
	  iteratorWithReturn[ITERATOR$1] = function () {
	    return this;
	  };
	  // eslint-disable-next-line es/no-array-from, no-throw-literal -- required for testing
	  Array.from(iteratorWithReturn, function () { throw 2; });
	} catch (error) { /* empty */ }

	var checkCorrectnessOfIteration = function (exec, SKIP_CLOSING) {
	  if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
	  var ITERATION_SUPPORT = false;
	  try {
	    var object = {};
	    object[ITERATOR$1] = function () {
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

	var SPECIES$2 = wellKnownSymbol('species');

	// `SpeciesConstructor` abstract operation
	// https://tc39.es/ecma262/#sec-speciesconstructor
	var speciesConstructor = function (O, defaultConstructor) {
	  var C = anObject(O).constructor;
	  var S;
	  return C === undefined || (S = anObject(C)[SPECIES$2]) == undefined ? defaultConstructor : aFunction$1(S);
	};

	var engineIsIos = /(iphone|ipod|ipad).*applewebkit/i.test(engineUserAgent);

	var location$1 = global$1.location;
	var set$3 = global$1.setImmediate;
	var clear = global$1.clearImmediate;
	var process$2 = global$1.process;
	var MessageChannel = global$1.MessageChannel;
	var Dispatch = global$1.Dispatch;
	var counter = 0;
	var queue = {};
	var ONREADYSTATECHANGE = 'onreadystatechange';
	var defer, channel, port;

	var run$1 = function (id) {
	  // eslint-disable-next-line no-prototype-builtins -- safe
	  if (queue.hasOwnProperty(id)) {
	    var fn = queue[id];
	    delete queue[id];
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
	  global$1.postMessage(id + '', location$1.protocol + '//' + location$1.host);
	};

	// Node.js 0.9+ & IE10+ has setImmediate, otherwise:
	if (!set$3 || !clear) {
	  set$3 = function setImmediate(fn) {
	    var args = [];
	    var i = 1;
	    while (arguments.length > i) args.push(arguments[i++]);
	    queue[++counter] = function () {
	      // eslint-disable-next-line no-new-func -- spec requirement
	      (typeof fn == 'function' ? fn : Function(fn)).apply(undefined, args);
	    };
	    defer(counter);
	    return counter;
	  };
	  clear = function clearImmediate(id) {
	    delete queue[id];
	  };
	  // Node.js 0.8-
	  if (engineIsNode) {
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
	  } else if (MessageChannel && !engineIsIos) {
	    channel = new MessageChannel();
	    port = channel.port2;
	    channel.port1.onmessage = listener;
	    defer = functionBindContext(port.postMessage, port, 1);
	  // Browsers with postMessage, skip WebWorkers
	  // IE8 has postMessage, but it's sync & typeof its postMessage is 'object'
	  } else if (
	    global$1.addEventListener &&
	    typeof postMessage == 'function' &&
	    !global$1.importScripts &&
	    location$1 && location$1.protocol !== 'file:' &&
	    !fails(post)
	  ) {
	    defer = post;
	    global$1.addEventListener('message', listener, false);
	  // IE8-
	  } else if (ONREADYSTATECHANGE in documentCreateElement('script')) {
	    defer = function (id) {
	      html.appendChild(documentCreateElement('script'))[ONREADYSTATECHANGE] = function () {
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

	var engineIsWebosWebkit = /web0s(?!.*chrome)/i.test(engineUserAgent);

	var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	var macrotask = task$1.set;




	var MutationObserver = global$1.MutationObserver || global$1.WebKitMutationObserver;
	var document$2 = global$1.document;
	var process$1 = global$1.process;
	var Promise$1 = global$1.Promise;
	// Node.js 11 shows ExperimentalWarning on getting `queueMicrotask`
	var queueMicrotaskDescriptor = getOwnPropertyDescriptor(global$1, 'queueMicrotask');
	var queueMicrotask = queueMicrotaskDescriptor && queueMicrotaskDescriptor.value;

	var flush$1, head, last, notify$1, toggle, node, promise$4, then;

	// modern engines have queueMicrotask method
	if (!queueMicrotask) {
	  flush$1 = function () {
	    var parent, fn;
	    if (engineIsNode && (parent = process$1.domain)) parent.exit();
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
	  if (!engineIsIos && !engineIsNode && !engineIsWebosWebkit && MutationObserver && document$2) {
	    toggle = true;
	    node = document$2.createTextNode('');
	    new MutationObserver(flush$1).observe(node, { characterData: true });
	    notify$1 = function () {
	      node.data = toggle = !toggle;
	    };
	  // environments with maybe non-completely correct, but existent Promise
	  } else if (Promise$1 && Promise$1.resolve) {
	    // Promise.resolve without an argument throws an error in LG WebOS 2
	    promise$4 = Promise$1.resolve(undefined);
	    then = promise$4.then;
	    notify$1 = function () {
	      then.call(promise$4, flush$1);
	    };
	  // Node.js without promises
	  } else if (engineIsNode) {
	    notify$1 = function () {
	      process$1.nextTick(flush$1);
	    };
	  // for other environments - macrotask based on:
	  // - setImmediate
	  // - MessageChannel
	  // - window.postMessag
	  // - onreadystatechange
	  // - setTimeout
	  } else {
	    notify$1 = function () {
	      // strange IE + webpack dev server bug - use .call(global)
	      macrotask.call(global$1, flush$1);
	    };
	  }
	}

	var microtask = queueMicrotask || function (fn) {
	  var task = { fn: fn, next: undefined };
	  if (last) last.next = task;
	  if (!head) {
	    head = task;
	    notify$1();
	  } last = task;
	};

	var PromiseCapability = function (C) {
	  var resolve, reject;
	  this.promise = new C(function ($$resolve, $$reject) {
	    if (resolve !== undefined || reject !== undefined) throw TypeError('Bad Promise constructor');
	    resolve = $$resolve;
	    reject = $$reject;
	  });
	  this.resolve = aFunction$1(resolve);
	  this.reject = aFunction$1(reject);
	};

	// 25.4.1.5 NewPromiseCapability(C)
	var f = function (C) {
	  return new PromiseCapability(C);
	};

	var newPromiseCapability$1 = {
		f: f
	};

	var promiseResolve = function (C, x) {
	  anObject(C);
	  if (isObject(x) && x.constructor === C) return x;
	  var promiseCapability = newPromiseCapability$1.f(C);
	  var resolve = promiseCapability.resolve;
	  resolve(x);
	  return promiseCapability.promise;
	};

	var hostReportErrors = function (a, b) {
	  var console = global$1.console;
	  if (console && console.error) {
	    arguments.length === 1 ? console.error(a) : console.error(a, b);
	  }
	};

	var perform = function (exec) {
	  try {
	    return { error: false, value: exec() };
	  } catch (error) {
	    return { error: true, value: error };
	  }
	};

	var task = task$1.set;











	var SPECIES$1 = wellKnownSymbol('species');
	var PROMISE = 'Promise';
	var getInternalState = internalState.get;
	var setInternalState$3 = internalState.set;
	var getInternalPromiseState = internalState.getterFor(PROMISE);
	var PromiseConstructor = nativePromiseConstructor;
	var TypeError$1 = global$1.TypeError;
	var document$1 = global$1.document;
	var process = global$1.process;
	getBuiltIn('fetch');
	var newPromiseCapability = newPromiseCapability$1.f;
	var newGenericPromiseCapability = newPromiseCapability;
	var DISPATCH_EVENT = !!(document$1 && document$1.createEvent && global$1.dispatchEvent);
	var NATIVE_REJECTION_EVENT = typeof PromiseRejectionEvent == 'function';
	var UNHANDLED_REJECTION = 'unhandledrejection';
	var REJECTION_HANDLED = 'rejectionhandled';
	var PENDING = 0;
	var FULFILLED = 1;
	var REJECTED = 2;
	var HANDLED = 1;
	var UNHANDLED = 2;
	var Internal, OwnPromiseCapability, PromiseWrapper;

	var FORCED$6 = isForced_1(PROMISE, function () {
	  var GLOBAL_CORE_JS_PROMISE = inspectSource(PromiseConstructor) !== String(PromiseConstructor);
	  if (!GLOBAL_CORE_JS_PROMISE) {
	    // V8 6.6 (Node 10 and Chrome 66) have a bug with resolving custom thenables
	    // https://bugs.chromium.org/p/chromium/issues/detail?id=830565
	    // We can't detect it synchronously, so just check versions
	    if (engineV8Version === 66) return true;
	    // Unhandled rejections tracking support, NodeJS Promise without it fails @@species test
	    if (!engineIsNode && !NATIVE_REJECTION_EVENT) return true;
	  }
	  // We need Promise#finally in the pure version for preventing prototype pollution
	  if (!PromiseConstructor.prototype['finally']) return true;
	  // We can't use @@species feature detection in V8 since it causes
	  // deoptimization and performance degradation
	  // https://github.com/zloirock/core-js/issues/679
	  if (engineV8Version >= 51 && /native code/.test(PromiseConstructor)) return false;
	  // Detect correctness of subclassing with @@species support
	  var promise = PromiseConstructor.resolve(1);
	  var FakePromise = function (exec) {
	    exec(function () { /* empty */ }, function () { /* empty */ });
	  };
	  var constructor = promise.constructor = {};
	  constructor[SPECIES$1] = FakePromise;
	  return !(promise.then(function () { /* empty */ }) instanceof FakePromise);
	});

	var INCORRECT_ITERATION$1 = FORCED$6 || !checkCorrectnessOfIteration(function (iterable) {
	  PromiseConstructor.all(iterable)['catch'](function () { /* empty */ });
	});

	// helpers
	var isThenable = function (it) {
	  var then;
	  return isObject(it) && typeof (then = it.then) == 'function' ? then : false;
	};

	var notify = function (state, isReject) {
	  if (state.notified) return;
	  state.notified = true;
	  var chain = state.reactions;
	  microtask(function () {
	    var value = state.value;
	    var ok = state.state == FULFILLED;
	    var index = 0;
	    // variable length - can't use forEach
	    while (chain.length > index) {
	      var reaction = chain[index++];
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
	            then.call(result, resolve, reject);
	          } else resolve(result);
	        } else reject(value);
	      } catch (error) {
	        if (domain && !exited) domain.exit();
	        reject(error);
	      }
	    }
	    state.reactions = [];
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
	    global$1.dispatchEvent(event);
	  } else event = { promise: promise, reason: reason };
	  if (!NATIVE_REJECTION_EVENT && (handler = global$1['on' + name])) handler(event);
	  else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
	};

	var onUnhandled = function (state) {
	  task.call(global$1, function () {
	    var promise = state.facade;
	    var value = state.value;
	    var IS_UNHANDLED = isUnhandled(state);
	    var result;
	    if (IS_UNHANDLED) {
	      result = perform(function () {
	        if (engineIsNode) {
	          process.emit('unhandledRejection', value, promise);
	        } else dispatchEvent(UNHANDLED_REJECTION, promise, value);
	      });
	      // Browsers should not trigger `rejectionHandled` event if it was handled here, NodeJS - should
	      state.rejection = engineIsNode || isUnhandled(state) ? UNHANDLED : HANDLED;
	      if (result.error) throw result.value;
	    }
	  });
	};

	var isUnhandled = function (state) {
	  return state.rejection !== HANDLED && !state.parent;
	};

	var onHandleUnhandled = function (state) {
	  task.call(global$1, function () {
	    var promise = state.facade;
	    if (engineIsNode) {
	      process.emit('rejectionHandled', promise);
	    } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
	  });
	};

	var bind$1 = function (fn, state, unwrap) {
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
	          then.call(value,
	            bind$1(internalResolve, wrapper, state),
	            bind$1(internalReject, wrapper, state)
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
	if (FORCED$6) {
	  // 25.4.3.1 Promise(executor)
	  PromiseConstructor = function Promise(executor) {
	    anInstance(this, PromiseConstructor, PROMISE);
	    aFunction$1(executor);
	    Internal.call(this);
	    var state = getInternalState(this);
	    try {
	      executor(bind$1(internalResolve, state), bind$1(internalReject, state));
	    } catch (error) {
	      internalReject(state, error);
	    }
	  };
	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  Internal = function Promise(executor) {
	    setInternalState$3(this, {
	      type: PROMISE,
	      done: false,
	      notified: false,
	      parent: false,
	      reactions: [],
	      rejection: false,
	      state: PENDING,
	      value: undefined
	    });
	  };
	  Internal.prototype = redefineAll(PromiseConstructor.prototype, {
	    // `Promise.prototype.then` method
	    // https://tc39.es/ecma262/#sec-promise.prototype.then
	    then: function then(onFulfilled, onRejected) {
	      var state = getInternalPromiseState(this);
	      var reaction = newPromiseCapability(speciesConstructor(this, PromiseConstructor));
	      reaction.ok = typeof onFulfilled == 'function' ? onFulfilled : true;
	      reaction.fail = typeof onRejected == 'function' && onRejected;
	      reaction.domain = engineIsNode ? process.domain : undefined;
	      state.parent = true;
	      state.reactions.push(reaction);
	      if (state.state != PENDING) notify(state, false);
	      return reaction.promise;
	    },
	    // `Promise.prototype.catch` method
	    // https://tc39.es/ecma262/#sec-promise.prototype.catch
	    'catch': function (onRejected) {
	      return this.then(undefined, onRejected);
	    }
	  });
	  OwnPromiseCapability = function () {
	    var promise = new Internal();
	    var state = getInternalState(promise);
	    this.promise = promise;
	    this.resolve = bind$1(internalResolve, state);
	    this.reject = bind$1(internalReject, state);
	  };
	  newPromiseCapability$1.f = newPromiseCapability = function (C) {
	    return C === PromiseConstructor || C === PromiseWrapper
	      ? new OwnPromiseCapability(C)
	      : newGenericPromiseCapability(C);
	  };
	}

	_export({ global: true, wrap: true, forced: FORCED$6 }, {
	  Promise: PromiseConstructor
	});

	setToStringTag(PromiseConstructor, PROMISE, false, true);
	setSpecies(PROMISE);

	PromiseWrapper = getBuiltIn(PROMISE);

	// statics
	_export({ target: PROMISE, stat: true, forced: FORCED$6 }, {
	  // `Promise.reject` method
	  // https://tc39.es/ecma262/#sec-promise.reject
	  reject: function reject(r) {
	    var capability = newPromiseCapability(this);
	    capability.reject.call(undefined, r);
	    return capability.promise;
	  }
	});

	_export({ target: PROMISE, stat: true, forced: isPure  }, {
	  // `Promise.resolve` method
	  // https://tc39.es/ecma262/#sec-promise.resolve
	  resolve: function resolve(x) {
	    return promiseResolve(this === PromiseWrapper ? PromiseConstructor : this, x);
	  }
	});

	_export({ target: PROMISE, stat: true, forced: INCORRECT_ITERATION$1 }, {
	  // `Promise.all` method
	  // https://tc39.es/ecma262/#sec-promise.all
	  all: function all(iterable) {
	    var C = this;
	    var capability = newPromiseCapability(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform(function () {
	      var $promiseResolve = aFunction$1(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        values.push(undefined);
	        remaining++;
	        $promiseResolve.call(C, promise).then(function (value) {
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
	  },
	  // `Promise.race` method
	  // https://tc39.es/ecma262/#sec-promise.race
	  race: function race(iterable) {
	    var C = this;
	    var capability = newPromiseCapability(C);
	    var reject = capability.reject;
	    var result = perform(function () {
	      var $promiseResolve = aFunction$1(C.resolve);
	      iterate(iterable, function (promise) {
	        $promiseResolve.call(C, promise).then(capability.resolve, reject);
	      });
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	// `Promise.allSettled` method
	// https://tc39.es/ecma262/#sec-promise.allsettled
	_export({ target: 'Promise', stat: true }, {
	  allSettled: function allSettled(iterable) {
	    var C = this;
	    var capability = newPromiseCapability$1.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform(function () {
	      var promiseResolve = aFunction$1(C.resolve);
	      var values = [];
	      var counter = 0;
	      var remaining = 1;
	      iterate(iterable, function (promise) {
	        var index = counter++;
	        var alreadyCalled = false;
	        values.push(undefined);
	        remaining++;
	        promiseResolve.call(C, promise).then(function (value) {
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

	var PROMISE_ANY_ERROR = 'No one promise resolved';

	// `Promise.any` method
	// https://tc39.es/ecma262/#sec-promise.any
	_export({ target: 'Promise', stat: true }, {
	  any: function any(iterable) {
	    var C = this;
	    var capability = newPromiseCapability$1.f(C);
	    var resolve = capability.resolve;
	    var reject = capability.reject;
	    var result = perform(function () {
	      var promiseResolve = aFunction$1(C.resolve);
	      var errors = [];
	      var counter = 0;
	      var remaining = 1;
	      var alreadyResolved = false;
	      iterate(iterable, function (promise) {
	        var index = counter++;
	        var alreadyRejected = false;
	        errors.push(undefined);
	        remaining++;
	        promiseResolve.call(C, promise).then(function (value) {
	          if (alreadyRejected || alreadyResolved) return;
	          alreadyResolved = true;
	          resolve(value);
	        }, function (error) {
	          if (alreadyRejected || alreadyResolved) return;
	          alreadyRejected = true;
	          errors[index] = error;
	          --remaining || reject(new (getBuiltIn('AggregateError'))(errors, PROMISE_ANY_ERROR));
	        });
	      });
	      --remaining || reject(new (getBuiltIn('AggregateError'))(errors, PROMISE_ANY_ERROR));
	    });
	    if (result.error) reject(result.value);
	    return capability.promise;
	  }
	});

	// Safari bug https://bugs.webkit.org/show_bug.cgi?id=200829
	var NON_GENERIC = !!nativePromiseConstructor && fails(function () {
	  nativePromiseConstructor.prototype['finally'].call({ then: function () { /* empty */ } }, function () { /* empty */ });
	});

	// `Promise.prototype.finally` method
	// https://tc39.es/ecma262/#sec-promise.prototype.finally
	_export({ target: 'Promise', proto: true, real: true, forced: NON_GENERIC }, {
	  'finally': function (onFinally) {
	    var C = speciesConstructor(this, getBuiltIn('Promise'));
	    var isFunction = typeof onFinally == 'function';
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

	var promise$3 = path.Promise;

	var promise$2 = promise$3;

	var promise$1 = promise$2;

	// a string of all valid unicode whitespaces
	var whitespaces = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
	  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

	var whitespace = '[' + whitespaces + ']';
	var ltrim = RegExp('^' + whitespace + whitespace + '*');
	var rtrim = RegExp(whitespace + whitespace + '*$');

	// `String.prototype.{ trim, trimStart, trimEnd, trimLeft, trimRight }` methods implementation
	var createMethod$1 = function (TYPE) {
	  return function ($this) {
	    var string = String(requireObjectCoercible($this));
	    if (TYPE & 1) string = string.replace(ltrim, '');
	    if (TYPE & 2) string = string.replace(rtrim, '');
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

	var trim$4 = stringTrim.trim;


	var $parseFloat = global$1.parseFloat;
	var FORCED$5 = 1 / $parseFloat(whitespaces + '-0') !== -Infinity;

	// `parseFloat` method
	// https://tc39.es/ecma262/#sec-parsefloat-string
	var numberParseFloat = FORCED$5 ? function parseFloat(string) {
	  var trimmedString = trim$4(String(string));
	  var result = $parseFloat(trimmedString);
	  return result === 0 && trimmedString.charAt(0) == '-' ? -0 : result;
	} : $parseFloat;

	// `parseFloat` method
	// https://tc39.es/ecma262/#sec-parsefloat-string
	_export({ global: true, forced: parseFloat != numberParseFloat }, {
	  parseFloat: numberParseFloat
	});

	var _parseFloat$2 = path.parseFloat;

	var _parseFloat$1 = _parseFloat$2;

	var _parseFloat = _parseFloat$1;

	var trim$3 = stringTrim.trim;


	var $parseInt = global$1.parseInt;
	var hex = /^[+-]?0[Xx]/;
	var FORCED$4 = $parseInt(whitespaces + '08') !== 8 || $parseInt(whitespaces + '0x16') !== 22;

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	var numberParseInt = FORCED$4 ? function parseInt(string, radix) {
	  var S = trim$3(String(string));
	  return $parseInt(S, (radix >>> 0) || (hex.test(S) ? 16 : 10));
	} : $parseInt;

	// `parseInt` method
	// https://tc39.es/ecma262/#sec-parseint-string-radix
	_export({ global: true, forced: parseInt != numberParseInt }, {
	  parseInt: numberParseInt
	});

	var _parseInt$2 = path.parseInt;

	var _parseInt$1 = _parseInt$2;

	var _parseInt = _parseInt$1;

	var HAS_SPECIES_SUPPORT$3 = arrayMethodHasSpeciesSupport('slice');

	var SPECIES = wellKnownSymbol('species');
	var nativeSlice = [].slice;
	var max$1 = Math.max;

	// `Array.prototype.slice` method
	// https://tc39.es/ecma262/#sec-array.prototype.slice
	// fallback for not array-like ES3 strings and DOM objects
	_export({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$3 }, {
	  slice: function slice(start, end) {
	    var O = toIndexedObject(this);
	    var length = toLength(O.length);
	    var k = toAbsoluteIndex(start, length);
	    var fin = toAbsoluteIndex(end === undefined ? length : end, length);
	    // inline `ArraySpeciesCreate` for usage native `Array#slice` where it's possible
	    var Constructor, result, n;
	    if (isArray$3(O)) {
	      Constructor = O.constructor;
	      // cross-realm fallback
	      if (typeof Constructor == 'function' && (Constructor === Array || isArray$3(Constructor.prototype))) {
	        Constructor = undefined;
	      } else if (isObject(Constructor)) {
	        Constructor = Constructor[SPECIES];
	        if (Constructor === null) Constructor = undefined;
	      }
	      if (Constructor === Array || Constructor === undefined) {
	        return nativeSlice.call(O, k, fin);
	      }
	    }
	    result = new (Constructor === undefined ? Array : Constructor)(max$1(fin - k, 0));
	    for (n = 0; k < fin; k++, n++) if (k in O) createProperty(result, n, O[k]);
	    result.length = n;
	    return result;
	  }
	});

	var entryVirtual = function (CONSTRUCTOR) {
	  return path[CONSTRUCTOR + 'Prototype'];
	};

	var slice$6 = entryVirtual('Array').slice;

	var ArrayPrototype$b = Array.prototype;

	var slice_1 = function (it) {
	  var own = it.slice;
	  return it === ArrayPrototype$b || (it instanceof Array && own === ArrayPrototype$b.slice) ? slice$6 : own;
	};

	var slice$5 = slice_1;

	var slice$4 = slice$5;

	var slice$3 = [].slice;
	var MSIE = /MSIE .\./.test(engineUserAgent); // <- dirty ie9- check

	var wrap = function (scheduler) {
	  return function (handler, timeout /* , ...arguments */) {
	    var boundArgs = arguments.length > 2;
	    var args = boundArgs ? slice$3.call(arguments, 2) : undefined;
	    return scheduler(boundArgs ? function () {
	      // eslint-disable-next-line no-new-func -- spec requirement
	      (typeof handler == 'function' ? handler : Function(handler)).apply(this, args);
	    } : handler, timeout);
	  };
	};

	// ie9- setTimeout & setInterval additional parameters fix
	// https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#timers
	_export({ global: true, bind: true, forced: MSIE }, {
	  // `setTimeout` method
	  // https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-settimeout
	  setTimeout: wrap(global$1.setTimeout),
	  // `setInterval` method
	  // https://html.spec.whatwg.org/multipage/timers-and-user-prompts.html#dom-setinterval
	  setInterval: wrap(global$1.setInterval)
	});

	var setTimeout$2 = path.setTimeout;

	var setTimeout$1 = setTimeout$2;

	var freezing = !fails(function () {
	  // eslint-disable-next-line es/no-object-isextensible, es/no-object-preventextensions -- required for testing
	  return Object.isExtensible(Object.preventExtensions({}));
	});

	var internalMetadata = createCommonjsModule(function (module) {
	var defineProperty = objectDefineProperty.f;



	var METADATA = uid('meta');
	var id = 0;

	// eslint-disable-next-line es/no-object-isextensible -- safe
	var isExtensible = Object.isExtensible || function () {
	  return true;
	};

	var setMetadata = function (it) {
	  defineProperty(it, METADATA, { value: {
	    objectID: 'O' + ++id, // object ID
	    weakData: {}          // weak collections IDs
	  } });
	};

	var fastKey = function (it, create) {
	  // return a primitive with prefix
	  if (!isObject(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
	  if (!has$1(it, METADATA)) {
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
	  if (!has$1(it, METADATA)) {
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
	  if (freezing && meta.REQUIRED && isExtensible(it) && !has$1(it, METADATA)) setMetadata(it);
	  return it;
	};

	var meta = module.exports = {
	  REQUIRED: false,
	  fastKey: fastKey,
	  getWeakData: getWeakData,
	  onFreeze: onFreeze
	};

	hiddenKeys$1[METADATA] = true;
	});

	var defineProperty$2 = objectDefineProperty.f;
	var forEach$3 = arrayIteration.forEach;



	var setInternalState$2 = internalState.set;
	var internalStateGetterFor$2 = internalState.getterFor;

	var collection = function (CONSTRUCTOR_NAME, wrapper, common) {
	  var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
	  var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
	  var ADDER = IS_MAP ? 'set' : 'add';
	  var NativeConstructor = global$1[CONSTRUCTOR_NAME];
	  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
	  var exported = {};
	  var Constructor;

	  if (!descriptors || typeof NativeConstructor != 'function'
	    || !(IS_WEAK || NativePrototype.forEach && !fails(function () { new NativeConstructor().entries().next(); }))
	  ) {
	    // create collection constructor
	    Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
	    internalMetadata.REQUIRED = true;
	  } else {
	    Constructor = wrapper(function (target, iterable) {
	      setInternalState$2(anInstance(target, Constructor, CONSTRUCTOR_NAME), {
	        type: CONSTRUCTOR_NAME,
	        collection: new NativeConstructor()
	      });
	      if (iterable != undefined) iterate(iterable, target[ADDER], { that: target, AS_ENTRIES: IS_MAP });
	    });

	    var getInternalState = internalStateGetterFor$2(CONSTRUCTOR_NAME);

	    forEach$3(['add', 'clear', 'delete', 'forEach', 'get', 'has', 'set', 'keys', 'values', 'entries'], function (KEY) {
	      var IS_ADDER = KEY == 'add' || KEY == 'set';
	      if (KEY in NativePrototype && !(IS_WEAK && KEY == 'clear')) {
	        createNonEnumerableProperty(Constructor.prototype, KEY, function (a, b) {
	          var collection = getInternalState(this).collection;
	          if (!IS_ADDER && IS_WEAK && !isObject(a)) return KEY == 'get' ? undefined : false;
	          var result = collection[KEY](a === 0 ? 0 : a, b);
	          return IS_ADDER ? this : result;
	        });
	      }
	    });

	    IS_WEAK || defineProperty$2(Constructor.prototype, 'size', {
	      configurable: true,
	      get: function () {
	        return getInternalState(this).collection.size;
	      }
	    });
	  }

	  setToStringTag(Constructor, CONSTRUCTOR_NAME, false, true);

	  exported[CONSTRUCTOR_NAME] = Constructor;
	  _export({ global: true, forced: true }, exported);

	  if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

	  return Constructor;
	};

	var getWeakData = internalMetadata.getWeakData;








	var setInternalState$1 = internalState.set;
	var internalStateGetterFor$1 = internalState.getterFor;
	var find$3 = arrayIteration.find;
	var findIndex = arrayIteration.findIndex;
	var id = 0;

	// fallback for uncaught frozen keys
	var uncaughtFrozenStore = function (store) {
	  return store.frozen || (store.frozen = new UncaughtFrozenStore());
	};

	var UncaughtFrozenStore = function () {
	  this.entries = [];
	};

	var findUncaughtFrozen = function (store, key) {
	  return find$3(store.entries, function (it) {
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
	    if (~index) this.entries.splice(index, 1);
	    return !!~index;
	  }
	};

	var collectionWeak = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var C = wrapper(function (that, iterable) {
	      anInstance(that, C, CONSTRUCTOR_NAME);
	      setInternalState$1(that, {
	        type: CONSTRUCTOR_NAME,
	        id: id++,
	        frozen: undefined
	      });
	      if (iterable != undefined) iterate(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
	    });

	    var getInternalState = internalStateGetterFor$1(CONSTRUCTOR_NAME);

	    var define = function (that, key, value) {
	      var state = getInternalState(that);
	      var data = getWeakData(anObject(key), true);
	      if (data === true) uncaughtFrozenStore(state).set(key, value);
	      else data[state.id] = value;
	      return that;
	    };

	    redefineAll(C.prototype, {
	      // 23.3.3.2 WeakMap.prototype.delete(key)
	      // 23.4.3.3 WeakSet.prototype.delete(value)
	      'delete': function (key) {
	        var state = getInternalState(this);
	        if (!isObject(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state)['delete'](key);
	        return data && has$1(data, state.id) && delete data[state.id];
	      },
	      // 23.3.3.4 WeakMap.prototype.has(key)
	      // 23.4.3.4 WeakSet.prototype.has(value)
	      has: function has(key) {
	        var state = getInternalState(this);
	        if (!isObject(key)) return false;
	        var data = getWeakData(key);
	        if (data === true) return uncaughtFrozenStore(state).has(key);
	        return data && has$1(data, state.id);
	      }
	    });

	    redefineAll(C.prototype, IS_MAP ? {
	      // 23.3.3.3 WeakMap.prototype.get(key)
	      get: function get(key) {
	        var state = getInternalState(this);
	        if (isObject(key)) {
	          var data = getWeakData(key);
	          if (data === true) return uncaughtFrozenStore(state).get(key);
	          return data ? data[state.id] : undefined;
	        }
	      },
	      // 23.3.3.5 WeakMap.prototype.set(key, value)
	      set: function set(key, value) {
	        return define(this, key, value);
	      }
	    } : {
	      // 23.4.3.1 WeakSet.prototype.add(value)
	      add: function add(value) {
	        return define(this, value, true);
	      }
	    });

	    return C;
	  }
	};

	createCommonjsModule(function (module) {






	var enforceIternalState = internalState.enforce;


	var IS_IE11 = !global$1.ActiveXObject && 'ActiveXObject' in global$1;
	// eslint-disable-next-line es/no-object-isextensible -- safe
	var isExtensible = Object.isExtensible;
	var InternalWeakMap;

	var wrapper = function (init) {
	  return function WeakMap() {
	    return init(this, arguments.length ? arguments[0] : undefined);
	  };
	};

	// `WeakMap` constructor
	// https://tc39.es/ecma262/#sec-weakmap-constructor
	var $WeakMap = module.exports = collection('WeakMap', wrapper, collectionWeak);

	// IE11 WeakMap frozen keys fix
	// We can't use feature detection because it crash some old IE builds
	// https://github.com/zloirock/core-js/issues/485
	if (nativeWeakMap && IS_IE11) {
	  InternalWeakMap = collectionWeak.getConstructor(wrapper, 'WeakMap', true);
	  internalMetadata.REQUIRED = true;
	  var WeakMapPrototype = $WeakMap.prototype;
	  var nativeDelete = WeakMapPrototype['delete'];
	  var nativeHas = WeakMapPrototype.has;
	  var nativeGet = WeakMapPrototype.get;
	  var nativeSet = WeakMapPrototype.set;
	  redefineAll(WeakMapPrototype, {
	    'delete': function (key) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeDelete.call(this, key) || state.frozen['delete'](key);
	      } return nativeDelete.call(this, key);
	    },
	    has: function has(key) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeHas.call(this, key) || state.frozen.has(key);
	      } return nativeHas.call(this, key);
	    },
	    get: function get(key) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        return nativeHas.call(this, key) ? nativeGet.call(this, key) : state.frozen.get(key);
	      } return nativeGet.call(this, key);
	    },
	    set: function set(key, value) {
	      if (isObject(key) && !isExtensible(key)) {
	        var state = enforceIternalState(this);
	        if (!state.frozen) state.frozen = new InternalWeakMap();
	        nativeHas.call(this, key) ? nativeSet.call(this, key, value) : state.frozen.set(key, value);
	      } else nativeSet.call(this, key, value);
	      return this;
	    }
	  });
	}
	});

	var weakMap$2 = path.WeakMap;

	var weakMap$1 = weakMap$2;

	var weakMap = weakMap$1;

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
	      var scrollTop = $__default['default'](window).scrollTop();

	      if (elemTop < $__default['default'](window).scrollTop() || elemTop > scrollTop + window.innerHeight) {
	        $__default['default']('html,body').animate({
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
	    return new promise$1(function (resolve, reject) {
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
	  return new promise$1(function (resolve, reject) {
	    ajax(options, resolve, reject);
	  });
	}

	var _w = new weakMap();

	var Format = /*#__PURE__*/function () {
	  function Format(w) {
	    _classCallCheck(this, Format);

	    _w.set(this, {
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
	      return s + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) + (c ? d + slice$4(_context = Math.abs(n - i).toFixed(c)).call(_context, 2) : '');
	    }
	  }]);

	  return Format;
	}();

	function ajax(options, resolve, reject) {
	  options.data.csrf_token = BooklyL10n.csrf_token;
	  return $__default['default'].ajax(jQuery.extend({
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

	    setTimeout$1(function () {
	      if (confirm(BooklyL10n.sessionHasExpired)) {
	        location.reload();
	      }
	    }, 100);

	    return false;
	  }

	  return true;
	}

	var $find = arrayIteration.find;


	var FIND = 'find';
	var SKIPS_HOLES = true;

	// Shouldn't skip holes
	if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES = false; });

	// `Array.prototype.find` method
	// https://tc39.es/ecma262/#sec-array.prototype.find
	_export({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
	  find: function find(callbackfn /* , that = undefined */) {
	    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var find$2 = entryVirtual('Array').find;

	var ArrayPrototype$a = Array.prototype;

	var find_1 = function (it) {
	  var own = it.find;
	  return it === ArrayPrototype$a || (it instanceof Array && own === ArrayPrototype$a.find) ? find$2 : own;
	};

	var find$1 = find_1;

	var find = find$1;

	var $stringify = getBuiltIn('JSON', 'stringify');
	var re = /[\uD800-\uDFFF]/g;
	var low = /^[\uD800-\uDBFF]$/;
	var hi = /^[\uDC00-\uDFFF]$/;

	var fix = function (match, offset, string) {
	  var prev = string.charAt(offset - 1);
	  var next = string.charAt(offset + 1);
	  if ((low.test(match) && !hi.test(next)) || (hi.test(match) && !low.test(prev))) {
	    return '\\u' + match.charCodeAt(0).toString(16);
	  } return match;
	};

	var FORCED$3 = fails(function () {
	  return $stringify('\uDF06\uD834') !== '"\\udf06\\ud834"'
	    || $stringify('\uDEAD') !== '"\\udead"';
	});

	if ($stringify) {
	  // `JSON.stringify` method
	  // https://tc39.es/ecma262/#sec-json.stringify
	  // https://github.com/tc39/proposal-well-formed-stringify
	  _export({ target: 'JSON', stat: true, forced: FORCED$3 }, {
	    // eslint-disable-next-line no-unused-vars -- required for `.length`
	    stringify: function stringify(it, replacer, space) {
	      var result = $stringify.apply(null, arguments);
	      return typeof result == 'string' ? result.replace(re, fix) : result;
	    }
	  });
	}

	// eslint-disable-next-line es/no-json -- safe
	if (!path.JSON) path.JSON = { stringify: JSON.stringify };

	// eslint-disable-next-line no-unused-vars -- required for `.length`
	var stringify$2 = function stringify(it, replacer, space) {
	  return path.JSON.stringify.apply(null, arguments);
	};

	var stringify$1 = stringify$2;

	var stringify = stringify$1;

	// `String.prototype.repeat` method implementation
	// https://tc39.es/ecma262/#sec-string.prototype.repeat
	var stringRepeat = function repeat(count) {
	  var str = String(requireObjectCoercible(this));
	  var result = '';
	  var n = toInteger(count);
	  if (n < 0 || n == Infinity) throw RangeError('Wrong number of repetitions');
	  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
	  return result;
	};

	// `String.prototype.repeat` method
	// https://tc39.es/ecma262/#sec-string.prototype.repeat
	_export({ target: 'String', proto: true }, {
	  repeat: stringRepeat
	});

	var repeat$2 = entryVirtual('String').repeat;

	var StringPrototype$2 = String.prototype;

	var repeat_1 = function (it) {
	  var own = it.repeat;
	  return typeof it === 'string' || it === StringPrototype$2
	    || (it instanceof String && own === StringPrototype$2.repeat) ? repeat$2 : own;
	};

	var repeat$1 = repeat_1;

	var repeat = repeat$1;

	var arrayMethodIsStrict = function (METHOD_NAME, argument) {
	  var method = [][METHOD_NAME];
	  return !!method && fails(function () {
	    // eslint-disable-next-line no-useless-call,no-throw-literal -- required for testing
	    method.call(null, argument || function () { throw 1; }, 1);
	  });
	};

	var $forEach = arrayIteration.forEach;


	var STRICT_METHOD$3 = arrayMethodIsStrict('forEach');

	// `Array.prototype.forEach` method implementation
	// https://tc39.es/ecma262/#sec-array.prototype.foreach
	var arrayForEach = !STRICT_METHOD$3 ? function forEach(callbackfn /* , thisArg */) {
	  return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	// eslint-disable-next-line es/no-array-prototype-foreach -- safe
	} : [].forEach;

	// `Array.prototype.forEach` method
	// https://tc39.es/ecma262/#sec-array.prototype.foreach
	// eslint-disable-next-line es/no-array-prototype-foreach -- safe
	_export({ target: 'Array', proto: true, forced: [].forEach != arrayForEach }, {
	  forEach: arrayForEach
	});

	var forEach$2 = entryVirtual('Array').forEach;

	var forEach$1 = forEach$2;

	var ArrayPrototype$9 = Array.prototype;

	var DOMIterables = {
	  DOMTokenList: true,
	  NodeList: true
	};

	var forEach_1 = function (it) {
	  var own = it.forEach;
	  return it === ArrayPrototype$9 || (it instanceof Array && own === ArrayPrototype$9.forEach)
	    // eslint-disable-next-line no-prototype-builtins -- safe
	    || DOMIterables.hasOwnProperty(classof(it)) ? forEach$1 : own;
	};

	var forEach = forEach_1;

	var HAS_SPECIES_SUPPORT$2 = arrayMethodHasSpeciesSupport('splice');

	var max = Math.max;
	var min = Math.min;
	var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF;
	var MAXIMUM_ALLOWED_LENGTH_EXCEEDED = 'Maximum allowed length exceeded';

	// `Array.prototype.splice` method
	// https://tc39.es/ecma262/#sec-array.prototype.splice
	// with adding support of @@species
	_export({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$2 }, {
	  splice: function splice(start, deleteCount /* , ...items */) {
	    var O = toObject(this);
	    var len = toLength(O.length);
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
	      actualDeleteCount = min(max(toInteger(deleteCount), 0), len - actualStart);
	    }
	    if (len + insertCount - actualDeleteCount > MAX_SAFE_INTEGER) {
	      throw TypeError(MAXIMUM_ALLOWED_LENGTH_EXCEEDED);
	    }
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
	        else delete O[to];
	      }
	      for (k = len; k > len - actualDeleteCount + insertCount; k--) delete O[k - 1];
	    } else if (insertCount > actualDeleteCount) {
	      for (k = len - actualDeleteCount; k > actualStart; k--) {
	        from = k + actualDeleteCount - 1;
	        to = k + insertCount - 1;
	        if (from in O) O[to] = O[from];
	        else delete O[to];
	      }
	    }
	    for (k = 0; k < insertCount; k++) {
	      O[k + actualStart] = arguments[k + 2];
	    }
	    O.length = len - actualDeleteCount + insertCount;
	    return A;
	  }
	});

	var splice$2 = entryVirtual('Array').splice;

	var ArrayPrototype$8 = Array.prototype;

	var splice_1 = function (it) {
	  var own = it.splice;
	  return it === ArrayPrototype$8 || (it instanceof Array && own === ArrayPrototype$8.splice) ? splice$2 : own;
	};

	var splice$1 = splice_1;

	var splice = splice$1;

	var $every = arrayIteration.every;


	var STRICT_METHOD$2 = arrayMethodIsStrict('every');

	// `Array.prototype.every` method
	// https://tc39.es/ecma262/#sec-array.prototype.every
	_export({ target: 'Array', proto: true, forced: !STRICT_METHOD$2 }, {
	  every: function every(callbackfn /* , thisArg */) {
	    return $every(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var every$2 = entryVirtual('Array').every;

	var ArrayPrototype$7 = Array.prototype;

	var every_1 = function (it) {
	  var own = it.every;
	  return it === ArrayPrototype$7 || (it instanceof Array && own === ArrayPrototype$7.every) ? every$2 : own;
	};

	var every$1 = every_1;

	var every = every$1;

	var concat$2 = entryVirtual('Array').concat;

	var ArrayPrototype$6 = Array.prototype;

	var concat_1 = function (it) {
	  var own = it.concat;
	  return it === ArrayPrototype$6 || (it instanceof Array && own === ArrayPrototype$6.concat) ? concat$2 : own;
	};

	var concat$1 = concat_1;

	var concat = concat$1;

	var $map = arrayIteration.map;


	var HAS_SPECIES_SUPPORT$1 = arrayMethodHasSpeciesSupport('map');

	// `Array.prototype.map` method
	// https://tc39.es/ecma262/#sec-array.prototype.map
	// with adding support of @@species
	_export({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$1 }, {
	  map: function map(callbackfn /* , thisArg */) {
	    return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var map$5 = entryVirtual('Array').map;

	var ArrayPrototype$5 = Array.prototype;

	var map_1 = function (it) {
	  var own = it.map;
	  return it === ArrayPrototype$5 || (it instanceof Array && own === ArrayPrototype$5.map) ? map$5 : own;
	};

	var map$4 = map_1;

	var map$3 = map$4;

	var $filter = arrayIteration.filter;


	var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('filter');

	// `Array.prototype.filter` method
	// https://tc39.es/ecma262/#sec-array.prototype.filter
	// with adding support of @@species
	_export({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
	  filter: function filter(callbackfn /* , thisArg */) {
	    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var filter$2 = entryVirtual('Array').filter;

	var ArrayPrototype$4 = Array.prototype;

	var filter_1 = function (it) {
	  var own = it.filter;
	  return it === ArrayPrototype$4 || (it instanceof Array && own === ArrayPrototype$4.filter) ? filter$2 : own;
	};

	var filter$1 = filter_1;

	var filter = filter$1;

	var $includes = arrayIncludes.includes;


	// `Array.prototype.includes` method
	// https://tc39.es/ecma262/#sec-array.prototype.includes
	_export({ target: 'Array', proto: true }, {
	  includes: function includes(el /* , fromIndex = 0 */) {
	    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var includes$4 = entryVirtual('Array').includes;

	var MATCH$1 = wellKnownSymbol('match');

	// `IsRegExp` abstract operation
	// https://tc39.es/ecma262/#sec-isregexp
	var isRegexp = function (it) {
	  var isRegExp;
	  return isObject(it) && ((isRegExp = it[MATCH$1]) !== undefined ? !!isRegExp : classofRaw(it) == 'RegExp');
	};

	var notARegexp = function (it) {
	  if (isRegexp(it)) {
	    throw TypeError("The method doesn't accept regular expressions");
	  } return it;
	};

	var MATCH = wellKnownSymbol('match');

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

	// `String.prototype.includes` method
	// https://tc39.es/ecma262/#sec-string.prototype.includes
	_export({ target: 'String', proto: true, forced: !correctIsRegexpLogic('includes') }, {
	  includes: function includes(searchString /* , position = 0 */) {
	    return !!~String(requireObjectCoercible(this))
	      .indexOf(notARegexp(searchString), arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var includes$3 = entryVirtual('String').includes;

	var ArrayPrototype$3 = Array.prototype;
	var StringPrototype$1 = String.prototype;

	var includes$2 = function (it) {
	  var own = it.includes;
	  if (it === ArrayPrototype$3 || (it instanceof Array && own === ArrayPrototype$3.includes)) return includes$4;
	  if (typeof it === 'string' || it === StringPrototype$1 || (it instanceof String && own === StringPrototype$1.includes)) {
	    return includes$3;
	  } return own;
	};

	var includes$1 = includes$2;

	var includes = includes$1;

	var non = '\u200B\u0085\u180E';

	// check that a method works with the correct list
	// of whitespaces and has a correct name
	var stringTrimForced = function (METHOD_NAME) {
	  return fails(function () {
	    return !!whitespaces[METHOD_NAME]() || non[METHOD_NAME]() != non || whitespaces[METHOD_NAME].name !== METHOD_NAME;
	  });
	};

	var $trim = stringTrim.trim;


	// `String.prototype.trim` method
	// https://tc39.es/ecma262/#sec-string.prototype.trim
	_export({ target: 'String', proto: true, forced: stringTrimForced('trim') }, {
	  trim: function trim() {
	    return $trim(this);
	  }
	});

	var trim$2 = entryVirtual('String').trim;

	var StringPrototype = String.prototype;

	var trim_1 = function (it) {
	  var own = it.trim;
	  return typeof it === 'string' || it === StringPrototype
	    || (it instanceof String && own === StringPrototype.trim) ? trim$2 : own;
	};

	var trim$1 = trim_1;

	var trim = trim$1;

	/* eslint-disable es/no-array-prototype-indexof -- required for testing */

	var $indexOf = arrayIncludes.indexOf;


	var nativeIndexOf = [].indexOf;

	var NEGATIVE_ZERO = !!nativeIndexOf && 1 / [1].indexOf(1, -0) < 0;
	var STRICT_METHOD$1 = arrayMethodIsStrict('indexOf');

	// `Array.prototype.indexOf` method
	// https://tc39.es/ecma262/#sec-array.prototype.indexof
	_export({ target: 'Array', proto: true, forced: NEGATIVE_ZERO || !STRICT_METHOD$1 }, {
	  indexOf: function indexOf(searchElement /* , fromIndex = 0 */) {
	    return NEGATIVE_ZERO
	      // convert -0 to +0
	      ? nativeIndexOf.apply(this, arguments) || 0
	      : $indexOf(this, searchElement, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var indexOf$2 = entryVirtual('Array').indexOf;

	var ArrayPrototype$2 = Array.prototype;

	var indexOf_1 = function (it) {
	  var own = it.indexOf;
	  return it === ArrayPrototype$2 || (it instanceof Array && own === ArrayPrototype$2.indexOf) ? indexOf$2 : own;
	};

	var indexOf$1 = indexOf_1;

	var indexOf = indexOf$1;

	/**
	 * Complete step.
	 */

	function stepComplete(params) {
	  var data = $__default['default'].extend({
	    action: 'bookly_render_complete'
	  }, params),
	      $container = opt[params.form_id].$container;
	  booklyAjax({
	    data: data
	  }).then(function (response) {
	    if (response.final_step_url && !data.error) {
	      document.location.href = response.final_step_url;
	    } else {
	      $container.html(response.html);
	      var $qc = $__default['default']('.bookly-js-qr', $container);
	      $__default['default']('img', $qc).on('error', function () {
	        $qc.remove();
	      }).on('load', function () {
	        $qc.removeClass('bookly-loading');
	      });
	      scrollTo($container, params.form_id);
	      $__default['default']('.bookly-js-start-over', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          reset_form: true,
	          new_chain: true
	        });
	      });
	      $__default['default']('.bookly-js-download-ics', $container).on('click', function (e) {
	        var _context;

	        var ladda = laddaStart(this);
	        window.location = BooklyL10n.ajaxurl + (indexOf(_context = BooklyL10n.ajaxurl).call(_context, '?') > 0 ? '&' : '?') + 'action=bookly_download_ics&form_id=' + params.form_id + '&csrf_token=' + BooklyL10n.csrf_token;

	        setTimeout$1(function () {
	          return ladda.stop();
	        }, 1500);
	      });
	      $__default['default']('.bookly-js-download-invoice', $container).on('click', function (e) {
	        var _context2;

	        var ladda = laddaStart(this);
	        window.location = BooklyL10n.ajaxurl + (indexOf(_context2 = BooklyL10n.ajaxurl).call(_context2, '?') > 0 ? '&' : '?') + 'action=bookly_invoices_download_invoice&form_id=' + params.form_id + '&csrf_token=' + BooklyL10n.csrf_token;

	        setTimeout$1(function () {
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
	    var $stripe_card_field = $__default['default']('#bookly-stripe-card-field', $container); // Init stripe intents form

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
	        $__default['default']('.pay-card .bookly-js-next-step', $container).prop('disabled', true);
	        var $details = $stripe_card_field.closest('.bookly-js-details');
	        $__default['default']('.bookly-form-group', $details).hide();
	        $__default['default']('.bookly-js-card-error', $details).text('Please call Stripe() with your publishable key. You used an empty string.');
	      }
	    }

	    var $payments = $__default['default']('.bookly-payment', $container),
	        $apply_coupon_button = $__default['default']('.bookly-js-apply-coupon', $container),
	        $coupon_input = $__default['default']('input.bookly-user-coupon', $container),
	        $coupon_error = $__default['default']('.bookly-js-coupon-error', $container),
	        $apply_gift_card_button = $__default['default']('.bookly-js-apply-gift-card', $container),
	        $gift_card_input = $__default['default']('input.bookly-user-gift', $container),
	        $gift_card_error = $__default['default']('.bookly-js-gift-card-error', $container),
	        $apply_tips_button = $__default['default']('.bookly-js-apply-tips', $container),
	        $applied_tips_button = $__default['default']('.bookly-js-applied-tips', $container),
	        $tips_input = $__default['default']('input.bookly-user-tips', $container),
	        $tips_error = $__default['default']('.bookly-js-tips-error', $container),
	        $deposit_mode = $__default['default']('input[type=radio][name=bookly-full-payment]', $container),
	        $coupon_info_text = $__default['default']('.bookly-info-text-coupon', $container),
	        $buttons = $__default['default']('.bookly-gateway-buttons,.bookly-js-details', $container),
	        $payment_details;
	    $payments.on('click', function () {
	      $buttons.hide();
	      $__default['default']('.bookly-gateway-buttons.pay-' + $__default['default'](this).val(), $container).show();

	      if ($__default['default'](this).data('with-details') == 1) {
	        var $parent = $__default['default'](this).closest('.bookly-list');
	        $payment_details = $__default['default']('.bookly-js-details', $parent);
	        $__default['default']('.bookly-js-details', $parent).show();
	      } else {
	        $payment_details = null;
	      }
	    });
	    $payments.eq(0).trigger('click');
	    $deposit_mode.on('change', function () {
	      var data = {
	        action: 'bookly_deposit_payments_apply_payment_method',
	        form_id: params.form_id,
	        deposit_full: $__default['default'](this).val()
	      };
	      $__default['default'](this).hide();
	      $__default['default'](this).prev().css('display', 'inline-block');
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
	      $coupon_error.text('');
	      $coupon_input.removeClass('bookly-error');
	      var data = {
	        action: 'bookly_coupons_apply_coupon',
	        form_id: params.form_id,
	        coupon_code: $coupon_input.val()
	      };
	      booklyAjax({
	        type: 'POST',
	        data: data,
	        error: function error() {
	          ladda.stop();
	        }
	      }).then(function (response) {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(function (response) {
	        $coupon_error.html(response.error);
	        $coupon_input.addClass('bookly-error');
	        $coupon_info_text.html(response.text);
	        scrollTo($coupon_error, params.form_id);
	      }).finally(function () {
	        ladda.stop();
	      });
	    });
	    $apply_gift_card_button.on('click', function (e) {
	      var ladda = laddaStart(this);
	      $gift_card_error.text('');
	      $gift_card_input.removeClass('bookly-error');
	      var data = {
	        action: 'bookly_pro_apply_gift_card',
	        form_id: params.form_id,
	        gift_card: $gift_card_input.val()
	      };
	      booklyAjax({
	        type: 'POST',
	        data: data,
	        error: function error() {
	          ladda.stop();
	        }
	      }).then(function (response) {
	        stepPayment({
	          form_id: params.form_id
	        });
	      }).catch(function (response) {
	        $gift_card_error.html(response.error);
	        $gift_card_input.addClass('bookly-error');
	        scrollTo($gift_card_error, params.form_id);
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
	      var data = {
	        action: 'bookly_pro_apply_tips',
	        form_id: params.form_id,
	        tips: $tips_input.val()
	      };
	      booklyAjax({
	        type: 'POST',
	        data: data,
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
	    $__default['default']('.bookly-js-next-step', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      var ladda = laddaStart(this),
	          $form; // Execute custom JavaScript

	      if (customJS) {
	        try {
	          $__default['default'].globalEval(customJS.next_button);
	        } catch (e) {// Do nothing
	        }
	      }

	      if ($__default['default']('.bookly-payment[value=local]', $container).is(':checked') || $__default['default'](this).hasClass('bookly-js-coupon-payment')) {
	        // handle only if was selected local payment !
	        e.preventDefault();
	        save(params.form_id);
	      } else if ($__default['default']('.bookly-payment[value=card]', $container).is(':checked')) {
	        if ($__default['default']('.bookly-payment[data-form=stripe]', $container).is(':checked')) {
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_stripe_create_intent',
	              form_id: params.form_id
	            }
	          }).then(function (response) {
	            stripe.confirmCardPayment(response.intent_secret, {
	              payment_method: {
	                card: stripe_card
	              }
	            }).then(function (result) {
	              if (result.error) {
	                booklyAjax({
	                  type: 'POST',
	                  data: {
	                    action: 'bookly_stripe_failed_payment',
	                    form_id: params.form_id,
	                    intent_id: response.intent_id
	                  }
	                }).then(function (response) {
	                  ladda.stop();
	                  $__default['default']('.bookly-js-card-error', $payment_details).text(result.error.message);
	                });
	              } else {
	                stepComplete({
	                  form_id: params.form_id
	                });
	              }
	            });
	          }).catch(function (response) {
	            if (response.error === 'cart_item_not_available') {
	              handleErrorCartItemNotAvailable(response, params.form_id);
	            }

	            ladda.stop();
	            $__default['default']('.bookly-js-card-error', $payment_details).text(response.error_message);
	          });
	        } else {
	          e.preventDefault();

	          var data = {
	            action: 'bookly_authorize_net_aim_payment',
	            card: {
	              number: $__default['default']('input[name="card_number"]', $payment_details).val(),
	              cvc: $__default['default']('input[name="card_cvc"]', $payment_details).val(),
	              exp_month: $__default['default']('select[name="card_exp_month"]', $payment_details).val(),
	              exp_year: $__default['default']('select[name="card_exp_year"]', $payment_details).val()
	            },
	            form_id: params.form_id
	          },
	              cardPayment = function cardPayment(data) {
	            booklyAjax({
	              type: 'POST',
	              data: data
	            }).then(function (response) {
	              stepComplete({
	                form_id: params.form_id
	              });
	            }).catch(function (response) {
	              if (response.error == 'cart_item_not_available') {
	                handleErrorCartItemNotAvailable(response, params.form_id);
	              } else if (response.error == 'payment_error') {
	                ladda.stop();
	                $__default['default']('.bookly-js-card-error', $payment_details).text(response.error_message);
	              }
	            });
	          };

	          cardPayment(data);
	        }
	      } else if ($__default['default']('.bookly-js-checkout', $container).is(':checked')) {
	        e.preventDefault();
	        $form = $__default['default'](this).closest('form');
	        var $payment_id = $__default['default']('input.bookly-payment-id', $form),
	            $payment_token = $__default['default']('input.bookly-js-add-payment-token', $form);

	        if ($payment_id.length > 0 || $payment_token.length > 0) {
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_pro_save_pending_appointment',
	              form_id: params.form_id,
	              payment_type: $form.data('gateway')
	            }
	          }).then(function (response) {
	            $payment_id.val(response.payment.id);

	            if ($payment_token.length) {
	              var url = $payment_token.val();
	              url += (indexOf(url).call(url, '?') ? '&' : '?') + 'bookly-token=' + response.payment.token;
	              $payment_token.val(url);
	            }

	            $form.submit();
	          }).catch(function (response) {
	            if (response.error == 'cart_item_not_available') {
	              handleErrorCartItemNotAvailable(response, params.form_id);
	            }
	          });
	        } else {
	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_check_cart',
	              form_id: params.form_id
	            }
	          }).then(function (response) {
	            $form.submit();
	          }).catch(function (response) {
	            if (response.error == 'cart_item_not_available') {
	              handleErrorCartItemNotAvailable(response, params.form_id);
	            }
	          });
	        }
	      }
	    });
	    $__default['default']('.bookly-js-back-step', $container).on('click', function (e) {
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

	/**
	 * Details step.
	 */

	function stepDetails(params) {
	  var data = $__default['default'].extend({
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

	    $__default['default'](document.body).trigger('bookly.render.step_detail', [$container]); // Init.

	    var phone_number = '',
	        $guest_info = $__default['default']('.bookly-js-guest', $container),
	        $phone_field = $__default['default']('.bookly-js-user-phone-input', $container),
	        $email_field = $__default['default']('.bookly-js-user-email', $container),
	        $email_confirm_field = $__default['default']('.bookly-js-user-email-confirm', $container),
	        $birthday_day_field = $__default['default']('.bookly-js-select-birthday-day', $container),
	        $birthday_month_field = $__default['default']('.bookly-js-select-birthday-month', $container),
	        $birthday_year_field = $__default['default']('.bookly-js-select-birthday-year', $container),
	        $address_country_field = $__default['default']('.bookly-js-address-country', $container),
	        $address_state_field = $__default['default']('.bookly-js-address-state', $container),
	        $address_postcode_field = $__default['default']('.bookly-js-address-postcode', $container),
	        $address_city_field = $__default['default']('.bookly-js-address-city', $container),
	        $address_street_field = $__default['default']('.bookly-js-address-street', $container),
	        $address_street_number_field = $__default['default']('.bookly-js-address-street_number', $container),
	        $address_additional_field = $__default['default']('.bookly-js-address-additional_address', $container),
	        $address_country_error = $__default['default']('.bookly-js-address-country-error', $container),
	        $address_state_error = $__default['default']('.bookly-js-address-state-error', $container),
	        $address_postcode_error = $__default['default']('.bookly-js-address-postcode-error', $container),
	        $address_city_error = $__default['default']('.bookly-js-address-city-error', $container),
	        $address_street_error = $__default['default']('.bookly-js-address-street-error', $container),
	        $address_street_number_error = $__default['default']('.bookly-js-address-street_number-error', $container),
	        $address_additional_error = $__default['default']('.bookly-js-address-additional_address-error', $container),
	        $birthday_day_error = $__default['default']('.bookly-js-select-birthday-day-error', $container),
	        $birthday_month_error = $__default['default']('.bookly-js-select-birthday-month-error', $container),
	        $birthday_year_error = $__default['default']('.bookly-js-select-birthday-year-error', $container),
	        $full_name_field = $__default['default']('.bookly-js-full-name', $container),
	        $first_name_field = $__default['default']('.bookly-js-first-name', $container),
	        $last_name_field = $__default['default']('.bookly-js-last-name', $container),
	        $notes_field = $__default['default']('.bookly-js-user-notes', $container),
	        $custom_field = $__default['default']('.bookly-custom-field', $container),
	        $info_field = $__default['default']('.bookly-js-info-field', $container),
	        $phone_error = $__default['default']('.bookly-js-user-phone-error', $container),
	        $email_error = $__default['default']('.bookly-js-user-email-error', $container),
	        $email_confirm_error = $__default['default']('.bookly-js-user-email-confirm-error', $container),
	        $name_error = $__default['default']('.bookly-js-full-name-error', $container),
	        $first_name_error = $__default['default']('.bookly-js-first-name-error', $container),
	        $last_name_error = $__default['default']('.bookly-js-last-name-error', $container),
	        $captcha = $__default['default']('.bookly-js-captcha-img', $container),
	        $custom_error = $__default['default']('.bookly-custom-field-error', $container),
	        $info_error = $__default['default']('.bookly-js-info-field-error', $container),
	        $modals = $__default['default']('.bookly-js-modal', $container),
	        $login_modal = $__default['default']('.bookly-js-login', $container),
	        $cst_modal = $__default['default']('.bookly-js-cst-duplicate', $container),
	        $verification_modal = $__default['default']('.bookly-js-verification-code', $container),
	        $verification_code = $__default['default']('#bookly-verification-code', $container),
	        $next_btn = $__default['default']('.bookly-js-next-step', $container),
	        $errors = map$3(_context = $__default['default']([$birthday_day_error, $birthday_month_error, $birthday_year_error, $address_country_error, $address_state_error, $address_postcode_error, $address_city_error, $address_street_error, $address_street_number_error, $address_additional_error, $name_error, $first_name_error, $last_name_error, $phone_error, $email_error, $email_confirm_error, $custom_error, $info_error])).call(_context, $__default['default'].fn.toArray),
	        $fields = map$3(_context2 = $__default['default']([$birthday_day_field, $birthday_month_field, $birthday_year_field, $address_city_field, $address_country_field, $address_postcode_field, $address_state_field, $address_street_field, $address_street_number_field, $address_additional_field, $full_name_field, $first_name_field, $last_name_field, $phone_field, $email_field, $email_confirm_field, $custom_field, $info_field])).call(_context2, $__default['default'].fn.toArray); // Populate form after login.


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

	        forEach(_context3 = response.data.info_fields).call(_context3, function (field) {
	          var _context4, _context6;

	          var $info_field = find($container).call($container, '.bookly-js-info-field-row[data-id="' + field.id + '"]');

	          switch ($info_field.data('type')) {
	            case 'checkboxes':
	              forEach(_context4 = field.value).call(_context4, function (value) {
	                var _context5;

	                filter(_context5 = find($info_field).call($info_field, '.bookly-js-info-field')).call(_context5, function () {
	                  return this.value == value;
	                }).prop('checked', true);
	              });

	              break;

	            case 'radio-buttons':
	              filter(_context6 = find($info_field).call($info_field, '.bookly-js-info-field')).call(_context6, function () {
	                return this.value == field.value;
	              }).prop('checked', true);

	              break;

	            default:
	              find($info_field).call($info_field, '.bookly-js-info-field').val(field.value);

	              break;
	          }
	        });
	      }

	      filter($errors).call($errors, ':not(.bookly-custom-field-error)').html('');
	    }; // Conditional custom fields


	    $__default['default']('.bookly-custom-field-row').on('change', 'select, input[type="checkbox"], input[type="radio"]', function () {
	      var $row = $__default['default'](this).closest('.bookly-custom-field-row'),
	          id = $row.data('id'),
	          $that = $__default['default'](this);
	      $__default['default'].each(custom_fields_conditions, function (i, condition) {
	        var _context7, _context8;

	        var $target = $__default['default']('.bookly-custom-field-row[data-id="' + condition.target + '"]');

	        if (_parseInt(condition.source) === id) {
	          switch ($row.data('type')) {
	            case 'drop-down':
	            case 'radio-buttons':
	              if (includes(_context7 = condition.value).call(_context7, $that.val()) && condition.equal === '1' || !includes(_context8 = condition.value).call(_context8, $that.val()) && condition.equal !== '1') {
	                $target.show();
	              } else {
	                $target.hide();
	              }

	              break;

	            case 'checkboxes':
	              var show = false;

	              find($row).call($row, 'input').each(function () {
	                var _context9, _context10;

	                if ($__default['default'](this).prop('checked') && (includes(_context9 = condition.value).call(_context9, $__default['default'](this).val()) && condition.equal === '1' || !includes(_context10 = condition.value).call(_context10, $__default['default'](this).val()) && condition.equal !== '1')) {
	                  show = true;
	                }
	              });

	              $target.toggle(show);
	              break;
	          }
	        }
	      });
	    }); // Custom fields date fields

	    $__default['default']('.bookly-js-cf-date', $container).each(function () {
	      var _context11, _context12;

	      var $cf_date = $__default['default'](this);
	      $cf_date.pickadate({
	        formatSubmit: 'yyyy-mm-dd',
	        format: opt[params.form_id].date_format,
	        min: $__default['default'](this).data('min') !== '' ? map$3(_context11 = $__default['default'](this).data('min').split('-')).call(_context11, function (value, index) {
	          if (index === 1) return value - 1;else return _parseInt(value);
	        }) : false,
	        max: $__default['default'](this).data('max') !== '' ? map$3(_context12 = $__default['default'](this).data('max').split('-')).call(_context12, function (value, index) {
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
	          $__default['default']('#' + $cf_date.attr('aria-owns')).hide();
	        }
	      }).focusin(function () {
	        // Restore calendar visibility, changed on onClose
	        $__default['default']('#' + $cf_date.attr('aria-owns')).show();
	      });
	    });

	    if (intlTelInput.enabled) {
	      $phone_field.intlTelInput({
	        preferredCountries: [intlTelInput.country],
	        initialCountry: intlTelInput.country,
	        geoIpLookup: function geoIpLookup(callback) {
	          $__default['default'].get('https://ipinfo.io', function () {}, 'jsonp').always(function (resp) {
	            var countryCode = resp && resp.country ? resp.country : '';
	            callback(countryCode);
	          });
	        },
	        utilsScript: intlTelInput.utils
	      });
	    } // Init modals.


	    find($container).call($container, '.bookly-js-modal.' + params.form_id).remove();

	    $modals.addClass(params.form_id).appendTo($container).on('click', '.bookly-js-close', function (e) {
	      var _context13, _context14, _context15;

	      e.preventDefault();

	      find(_context13 = find(_context14 = find(_context15 = $__default['default'](e.delegateTarget).removeClass('bookly-in')).call(_context15, 'form').trigger('reset').end()).call(_context14, 'input').removeClass('bookly-error').end()).call(_context13, '.bookly-label-error').html('');
	    }); // Login modal.

	    $__default['default']('.bookly-js-login-show', $container).on('click', function (e) {
	      e.preventDefault();
	      $login_modal.addClass('bookly-in');
	    });
	    $__default['default']('button:submit', $login_modal).on('click', function (e) {
	      e.preventDefault();
	      var ladda = Ladda.create(this);
	      ladda.start();
	      booklyAjax({
	        type: 'POST',
	        data: {
	          action: 'bookly_wp_user_login',
	          form_id: params.form_id,
	          log: find($login_modal).call($login_modal, '[name="log"]').val(),
	          pwd: find($login_modal).call($login_modal, '[name="pwd"]').val(),
	          rememberme: find($login_modal).call($login_modal, '[name="rememberme"]').prop('checked') ? 1 : 0
	        }
	      }).then(function (response) {
	        BooklyL10n.csrf_token = response.data.csrf_token;
	        $guest_info.fadeOut('slow');
	        populateForm(response);
	        $login_modal.removeClass('bookly-in');
	      }).catch(function (response) {
	        if (response.error == 'incorrect_username_password') {
	          find($login_modal).call($login_modal, 'input').addClass('bookly-error');

	          find($login_modal).call($login_modal, '.bookly-label-error').html(opt[params.form_id].errors[response.error]);
	        }
	      }).finally(function () {
	        ladda.stop();
	      });
	    }); // Customer duplicate modal.

	    $__default['default']('button:submit', $cst_modal).on('click', function (e) {
	      e.preventDefault();
	      $cst_modal.removeClass('bookly-in');
	      $next_btn.trigger('click', [1]);
	    }); // Verification code modal.

	    $__default['default']('button:submit', $verification_modal).on('click', function (e) {
	      e.preventDefault();
	      $verification_modal.removeClass('bookly-in');
	      $next_btn.trigger('click');
	    }); // Facebook login button.

	    if (opt[params.form_id].hasOwnProperty('facebook') && opt[params.form_id].facebook.enabled && typeof FB !== 'undefined') {
	      FB.XFBML.parse($__default['default']('.bookly-js-fb-login-button', $container).parent().get(0));

	      opt[params.form_id].facebook.onStatusChange = function (response) {
	        if (response.status === 'connected') {
	          opt[params.form_id].facebook.enabled = false;
	          opt[params.form_id].facebook.onStatusChange = undefined;
	          $guest_info.fadeOut('slow', function () {
	            // Hide buttons in all Bookly forms on the page.
	            $__default['default']('.bookly-js-fb-login-button').hide();
	          });
	          FB.api('/me', {
	            fields: 'id,name,first_name,last_name,email'
	          }, function (userInfo) {
	            booklyAjax({
	              type: 'POST',
	              data: $__default['default'].extend(userInfo, {
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
	      e.preventDefault(); // Terms and conditions checkbox

	      var $terms = $__default['default']('.bookly-js-terms', $container),
	          $terms_error = $__default['default']('.bookly-js-terms-error', $container);
	      $terms_error.html('');

	      if ($terms.length && !$terms.prop('checked')) {
	        $terms_error.html(terms_error);
	      } else {
	        var _context16, _context17;

	        var info_fields = [],
	            custom_fields = {},
	            checkbox_values,
	            captcha_ids = [],
	            ladda = laddaStart(this); // Execute custom JavaScript

	        if (customJS) {
	          try {
	            $__default['default'].globalEval(customJS.next_button);
	          } catch (e) {// Do nothing
	          }
	        } // Customer information fields.


	        $__default['default']('div.bookly-js-info-field-row', $container).each(function () {
	          var $this = $__default['default'](this);

	          switch ($this.data('type')) {
	            case 'text-field':
	              info_fields.push({
	                id: $this.data('id'),
	                value: find($this).call($this, 'input.bookly-js-info-field').val()
	              });
	              break;

	            case 'textarea':
	              info_fields.push({
	                id: $this.data('id'),
	                value: find($this).call($this, 'textarea.bookly-js-info-field').val()
	              });
	              break;

	            case 'checkboxes':
	              checkbox_values = [];

	              find($this).call($this, 'input.bookly-js-info-field:checked').each(function () {
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
	                value: find($this).call($this, 'input.bookly-js-info-field:checked').val() || null
	              });
	              break;

	            case 'drop-down':
	              info_fields.push({
	                id: $this.data('id'),
	                value: find($this).call($this, 'select.bookly-js-info-field').val()
	              });
	              break;
	          }
	        }); // Custom fields.

	        $__default['default']('.bookly-custom-fields-container', $container).each(function () {
	          var $cf_container = $__default['default'](this),
	              key = $cf_container.data('key'),
	              custom_fields_data = [];
	          $__default['default']('div.bookly-custom-field-row', $cf_container).each(function () {
	            var $this = $__default['default'](this);

	            if ($this.css('display') !== 'none') {
	              switch ($this.data('type')) {
	                case 'text-field':
	                case 'file':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: find($this).call($this, 'input.bookly-custom-field').val()
	                  });
	                  break;

	                case 'textarea':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: find($this).call($this, 'textarea.bookly-custom-field').val()
	                  });
	                  break;

	                case 'checkboxes':
	                  checkbox_values = [];

	                  find($this).call($this, 'input.bookly-custom-field:checked').each(function () {
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
	                    value: find($this).call($this, 'input.bookly-custom-field:checked').val() || null
	                  });
	                  break;

	                case 'drop-down':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: find($this).call($this, 'select.bookly-custom-field').val()
	                  });
	                  break;

	                case 'number':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: find($this).call($this, 'input.bookly-custom-field').val()
	                  });
	                  break;

	                case 'time':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: find($this).call($this, 'select.bookly-custom-field').val()
	                  });
	                  break;

	                case 'date':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: find($this).call($this, 'input.bookly-custom-field').pickadate('picker').get('select', 'yyyy-mm-dd')
	                  });
	                  break;

	                case 'captcha':
	                  custom_fields_data.push({
	                    id: $this.data('id'),
	                    value: find($this).call($this, 'input.bookly-custom-field').val()
	                  });
	                  captcha_ids.push($this.data('id'));
	                  break;
	              }
	            }
	          });
	          custom_fields[key] = {
	            custom_fields: stringify(custom_fields_data)
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
	          email: trim(_context16 = $email_field.val()).call(_context16),
	          email_confirm: $email_confirm_field.length === 1 ? trim(_context17 = $email_confirm_field.val()).call(_context17) : undefined,
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
	          captcha_ids: stringify(captcha_ids),
	          force_update_customer: !update_details_dialog || force_update_customer,
	          verification_code: $verification_code.val()
	        }; // Error messages

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
	              window.location.href = woocommerce.cart_url;
	            }).catch(function (response) {
	              ladda.stop();
	              stepTime({
	                form_id: params.form_id
	              }, opt[params.form_id].errors[response.error]);
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

	            find($verification_modal).call($verification_modal, '#bookly-verification-code-text').html(response.verify_text).end().addClass('bookly-in');
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

	            forEach(validateFields).call(validateFields, function (field) {
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
	              $__default['default'].each(response.info_fields, function (field_id, message) {
	                var $div = $__default['default']('div.bookly-js-info-field-row[data-id="' + field_id + '"]', $container);

	                find($div).call($div, '.bookly-js-info-field-error').html(message);

	                find($div).call($div, '.bookly-js-info-field').addClass('bookly-error');

	                if ($scroll_to === null) {
	                  $scroll_to = find($div).call($div, '.bookly-js-info-field');
	                }
	              });
	            }

	            if (response.custom_fields) {
	              $__default['default'].each(response.custom_fields, function (key, fields) {
	                $__default['default'].each(fields, function (field_id, message) {
	                  var $custom_fields_collector = $__default['default']('.bookly-custom-fields-container[data-key="' + key + '"]', $container);
	                  var $div = $__default['default']('[data-id="' + field_id + '"]', $custom_fields_collector);

	                  find($div).call($div, '.bookly-custom-field-error').html(message);

	                  find($div).call($div, '.bookly-custom-field').addClass('bookly-error');

	                  if ($scroll_to === null) {
	                    $scroll_to = find($div).call($div, '.bookly-custom-field');
	                  }
	                });
	              });
	            }

	            if (response.customer) {
	              find($cst_modal).call($cst_modal, '.bookly-js-modal-body').html(response.customer).end().addClass('bookly-in');
	            }
	          }

	          if ($scroll_to !== null) {
	            scrollTo($scroll_to, params.form_id);
	          }
	        });
	      }
	    });
	    $__default['default']('.bookly-js-back-step', $container).on('click', function (e) {
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
	      } else if (!repeat(opt[params.form_id].skip_steps) && opt[params.form_id].recurrence_enabled) {
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
	    $__default['default']('.bookly-js-captcha-refresh', $container).on('click', function () {
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
	    bookly_forms = bookly_forms || $__default['default']('.bookly-form .bookly-details-step');
	    bookly_forms.each(function () {
	      initGooglePlacesAutocomplete($__default['default'](this));
	    });
	  }
	  /**
	   * Addon: Google Maps Address
	   * @param {jQuery} [$container]
	   * @returns {boolean}
	   */


	  function initGooglePlacesAutocomplete($container) {
	    var autocompleteInput = find($container).call($container, '.bookly-js-cst-address-autocomplete');

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
	      forEach(autocompleteFields).call(autocompleteFields, function (field) {
	        var element = find($container).call($container, field.selector);

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

	    var data = $__default['default'].extend({
	      action: 'bookly_render_cart'
	    }, params),
	        $container = opt[params.form_id].$container;
	    booklyAjax({
	      data: data
	    }).then(function (response) {
	      $container.html(response.html);

	      if (error) {
	        $__default['default']('.bookly-label-error', $container).html(error.message);
	        $__default['default']('tr[data-cart-key="' + error.failed_key + '"]', $container).addClass('bookly-label-error');
	      } else {
	        $__default['default']('.bookly-label-error', $container).hide();
	      }

	      scrollTo($container, params.form_id);
	      var customJS = response.custom_js;
	      $__default['default']('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this); // Execute custom JavaScript

	        if (customJS) {
	          try {
	            $__default['default'].globalEval(customJS.next_button);
	          } catch (e) {// Do nothing
	          }
	        }

	        stepDetails({
	          form_id: params.form_id
	        });
	      });
	      $__default['default']('.bookly-add-item', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepService({
	          form_id: params.form_id,
	          new_chain: true
	        });
	      }); // 'BACK' button.

	      $__default['default']('.bookly-js-back-step', $container).on('click', function (e) {
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
	      $__default['default']('.bookly-js-actions button', $container).on('click', function () {
	        laddaStart(this);
	        var $this = $__default['default'](this),
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
	                  $trs_to_remove = $__default['default']('tr[data-cart-key="' + remove_cart_key + '"]', $container);
	              $cart_item.delay(300).fadeOut(200, function () {
	                if (response.data.total_waiting_list) {
	                  $__default['default']('.bookly-js-waiting-list-price', $container).html(response.data.waiting_list_price);
	                  $__default['default']('.bookly-js-waiting-list-deposit', $container).html(response.data.waiting_list_deposit);
	                } else {
	                  $__default['default']('.bookly-js-waiting-list-price', $container).closest('tr').remove();
	                }

	                $__default['default']('.bookly-js-subtotal-price', $container).html(response.data.subtotal_price);
	                $__default['default']('.bookly-js-subtotal-deposit', $container).html(response.data.subtotal_deposit);
	                $__default['default']('.bookly-js-pay-now-deposit', $container).html(response.data.pay_now_deposit);
	                $__default['default']('.bookly-js-pay-now-tax', $container).html(response.data.pay_now_tax);
	                $__default['default']('.bookly-js-total-price', $container).html(response.data.total_price);
	                $__default['default']('.bookly-js-total-tax', $container).html(response.data.total_tax);
	                $trs_to_remove.remove();

	                if ($__default['default']('tr[data-cart-key]').length == 0) {
	                  $__default['default']('.bookly-js-back-step', $container).hide();
	                  $__default['default']('.bookly-js-next-step', $container).hide();
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
	  if (repeat(opt[params.form_id].skip_steps)) {
	    stepCart(params, error);
	  } else {
	    var data = $__default['default'].extend({
	      action: 'bookly_render_repeat'
	    }, params),
	        $container = opt[params.form_id].$container;
	    booklyAjax({
	      data: data
	    }).then(function (response) {
	      var _context3;

	      $container.html(response.html);
	      scrollTo($container, params.form_id);
	      var $repeat_enabled = $__default['default']('.bookly-js-repeat-appointment-enabled', $container),
	          $next_step = $__default['default']('.bookly-js-next-step', $container),
	          $repeat_container = $__default['default']('.bookly-js-repeat-variants-container', $container),
	          $variants = $__default['default']('[class^="bookly-js-variant"]', $repeat_container),
	          $repeat_variant = $__default['default']('.bookly-js-repeat-variant', $repeat_container),
	          $button_get_schedule = $__default['default']('.bookly-js-get-schedule', $repeat_container),
	          $variant_weekly = $__default['default']('.bookly-js-variant-weekly', $repeat_container),
	          $variant_monthly = $__default['default']('.bookly-js-repeat-variant-monthly', $repeat_container),
	          $date_until = $__default['default']('.bookly-js-repeat-until', $repeat_container),
	          $repeat_times = $__default['default']('.bookly-js-repeat-times', $repeat_container),
	          $monthly_specific_day = $__default['default']('.bookly-js-monthly-specific-day', $repeat_container),
	          $monthly_week_day = $__default['default']('.bookly-js-monthly-week-day', $repeat_container),
	          $repeat_every_day = $__default['default']('.bookly-js-repeat-daily-every', $repeat_container),
	          $schedule_container = $__default['default']('.bookly-js-schedule-container', $container),
	          $days_error = $__default['default']('.bookly-js-days-error', $repeat_container),
	          $schedule_slots = $__default['default']('.bookly-js-schedule-slots', $schedule_container),
	          $intersection_info = $__default['default']('.bookly-js-intersection-info', $schedule_container),
	          $info_help = $__default['default']('.bookly-js-schedule-help', $schedule_container),
	          $info_wells = $__default['default']('.bookly-well', $schedule_container),
	          $pagination = $__default['default']('.bookly-pagination', $schedule_container),
	          $schedule_row_template = $__default['default']('.bookly-schedule-row-template .bookly-schedule-row', $schedule_container),
	          pages_warning_info = response.pages_warning_info,
	          short_date_format = response.short_date_format,
	          bound_date = {
	        min: response.date_min || true,
	        max: response.date_max || true
	      },
	          schedule = [],
	          customJS = response.custom_js;
	      var repeat$1 = {
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
	            $time = $__default['default']('<select/>');
	            $__default['default'].each(options, function (index, option) {
	              var $option = $__default['default']('<option/>');
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

	          find($schedule_row).call($schedule_row, '.bookly-js-schedule-time').html($time);

	          find($schedule_row).call($schedule_row, 'div.bookly-label-error').toggle(!options.length);
	        },
	        renderSchedulePage: function renderSchedulePage(page) {
	          var $row,
	              count = schedule.length,
	              rows_on_page = 5,
	              start = rows_on_page * page - rows_on_page,
	              warning_pages = [],
	              previousPage = function previousPage(e) {
	            e.preventDefault();

	            var page = _parseInt(find($pagination).call($pagination, '.active').data('page'));

	            if (page > 1) {
	              repeat$1.renderSchedulePage(page - 1);
	            }
	          },
	              nextPage = function nextPage(e) {
	            e.preventDefault();

	            var page = _parseInt(find($pagination).call($pagination, '.active').data('page'));

	            if (page < count / rows_on_page) {
	              repeat$1.renderSchedulePage(page + 1);
	            }
	          };

	          $schedule_slots.html('');

	          for (var i = start, j = 0; j < rows_on_page && i < count; i++, j++) {
	            $row = $schedule_row_template.clone();
	            $row.data('datetime', schedule[i].datetime);
	            $row.data('index', schedule[i].index);
	            $__default['default']('> div:first-child', $row).html(schedule[i].index);
	            $__default['default']('.bookly-schedule-date', $row).html(schedule[i].display_date);

	            if (schedule[i].all_day_service_time !== undefined) {
	              $__default['default']('.bookly-js-schedule-time', $row).hide();
	              $__default['default']('.bookly-js-schedule-all-day-time', $row).html(schedule[i].all_day_service_time).show();
	            } else {
	              $__default['default']('.bookly-js-schedule-time', $row).html(schedule[i].display_time).show();
	              $__default['default']('.bookly-js-schedule-all-day-time', $row).hide();
	            }

	            if (schedule[i].another_time) {
	              $__default['default']('.bookly-schedule-intersect', $row).show();
	            }

	            if (schedule[i].deleted) {
	              find($row).call($row, '.bookly-schedule-appointment').addClass('bookly-appointment-hidden');
	            }

	            $schedule_slots.append($row);
	          }

	          if (count > rows_on_page) {
	            var $btn = $__default['default']('<li/>').append($__default['default']('<a>', {
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
	              $btn = $__default['default']('<li/>', {
	                'data-page': j
	              }).append($__default['default']('<a>', {
	                href: '#',
	                text: j
	              }));
	              $pagination.append($btn);
	              $btn.on('click', function (e) {
	                e.preventDefault();
	                repeat$1.renderSchedulePage($__default['default'](this).data('page'));
	              }).keypress(function (e) {
	                e.preventDefault();

	                if (e.which == 13 || e.which == 32) {
	                  repeat$1.renderSchedulePage($__default['default'](this).data('page'));
	                }
	              });
	            }

	            find($pagination).call($pagination, 'li:eq(' + page + ')').addClass('active');

	            $btn = $__default['default']('<li/>').append($__default['default']('<a>', {
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
	          $__default['default'].each(schedule, function (index, item) {
	            if (!preferred_time && !item.another_time) {
	              preferred_time = item.display_time;
	            }
	          });
	          repeat$1.renderSchedulePage(1);
	          $schedule_container.show();
	          $next_step.prop('disabled', schedule.length == 0);
	          $schedule_slots.on('click', 'button[data-action]', function () {
	            var $schedule_row = $__default['default'](this).closest('.bookly-schedule-row');
	            var row_index = $schedule_row.data('index') - 1;

	            switch ($__default['default'](this).data('action')) {
	              case 'drop':
	                schedule[row_index].deleted = true;

	                find($schedule_row).call($schedule_row, '.bookly-schedule-appointment').addClass('bookly-appointment-hidden');

	                repeat$1.prepareButtonNextState();
	                break;

	              case 'restore':
	                schedule[row_index].deleted = false;

	                find($schedule_row).call($schedule_row, '.bookly-schedule-appointment').removeClass('bookly-appointment-hidden');

	                $next_step.prop('disabled', false);
	                break;

	              case 'edit':
	                var $date = $__default['default']('<input/>', {
	                  type: 'text'
	                }),
	                    $edit_button = $__default['default'](this),
	                    ladda_round = laddaStart(this);

	                find($schedule_row).call($schedule_row, '.bookly-schedule-date').html($date);

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
	                    $__default['default'].each(schedule, function (index, item) {
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
	                        repeat$1.addTimeSlotControl($schedule_row, response.data[0].options, preferred_time, schedule[row_index].display_time, response.data[0].all_day_service_time);

	                        find($schedule_row).call($schedule_row, 'button[data-action="save"]').show();
	                      } else {
	                        repeat$1.addTimeSlotControl($schedule_row, []);

	                        find($schedule_row).call($schedule_row, 'button[data-action="save"]').hide();
	                      }
	                    });
	                  },
	                  onClose: function onClose() {
	                    // Hide for skip tab navigations by days of the month when the calendar is closed
	                    $__default['default']('#' + $date.attr('aria-owns')).hide();
	                  }
	                }).focusin(function () {
	                  // Restore calendar visibility, changed on onClose
	                  $__default['default']('#' + $date.attr('aria-owns')).show();
	                });
	                var slots = JSON.parse(schedule[row_index].slots);
	                $date.pickadate('picker').set('select', new Date(slots[0][2]));
	                break;

	              case 'save':
	                $__default['default'](this).hide();

	                find($schedule_row).call($schedule_row, 'button[data-action="edit"]').show();

	                var $date_container = find($schedule_row).call($schedule_row, '.bookly-schedule-date'),
	                    $time_container = find($schedule_row).call($schedule_row, '.bookly-js-schedule-time'),
	                    $select = find($time_container).call($time_container, 'select'),
	                    option = find($select).call($select, 'option:selected');

	                schedule[row_index].slots = $select.val();
	                schedule[row_index].display_date = find($date_container).call($date_container, 'input').val();
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
	              if (($repeat_every_day.val() > 6 || $__default['default'].inArray(current_date.format('ddd').toLowerCase(), repeat$1.week_days) != -1) && current_date.diff(repeat$1.date_from, 'days') % $repeat_every_day.val() == 0) {
	                return true;
	              }

	              break;

	            case 'weekly':
	            case 'biweekly':
	              if (($repeat_variant.val() == 'weekly' || current_date.diff(repeat$1.date_from.clone().startOf('isoWeek'), 'weeks') % 2 == 0) && $__default['default'].inArray(current_date.format('ddd').toLowerCase(), repeat$1.checked_week_days) != -1) {
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
	              date_from = slice$4(_context = bound_date.min).call(_context),
	              date_until = $date_until.pickadate('picker').get('select'),
	              moment_until = moment().year(date_until.year).month(date_until.month).date(date_until.date).add(5, 'years');

	          date_from[1]++;
	          repeat$1.date_from = moment(date_from.join(','), 'YYYY,M,D');
	          repeat$1.week_days = [];

	          find($monthly_week_day).call($monthly_week_day, 'option').each(function () {
	            repeat$1.week_days.push($__default['default'](this).val());
	          });

	          repeat$1.checked_week_days = [];
	          $__default['default']('.bookly-js-week-days input:checked', $repeat_container).each(function () {
	            repeat$1.checked_week_days.push(this.value);
	          });
	          var current_date = repeat$1.date_from.clone();

	          do {
	            if (repeat$1.isDateMatchesSelections(current_date)) {
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
	              date_from = slice$4(_context2 = bound_date.min).call(_context2),
	              date_until = $date_until.pickadate('picker').get('select'),
	              moment_until = moment().year(date_until.year).month(date_until.month).date(date_until.date);

	          date_from[1]++;
	          repeat$1.date_from = moment(date_from.join(','), 'YYYY,M,D');
	          repeat$1.week_days = [];

	          find($monthly_week_day).call($monthly_week_day, 'option').each(function () {
	            repeat$1.week_days.push($__default['default'](this).val());
	          });

	          repeat$1.checked_week_days = [];
	          $__default['default']('.bookly-js-week-days input:checked', $repeat_container).each(function () {
	            repeat$1.checked_week_days.push(this.value);
	          });
	          var current_date = repeat$1.date_from.clone();

	          do {
	            if (repeat$1.isDateMatchesSelections(current_date)) {
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
	          $__default['default']('#' + $date_until.attr('aria-owns')).hide();
	        }
	      }).focusin(function () {
	        // Restore calendar visibility, changed on onClose
	        $__default['default']('#' + $date_until.attr('aria-owns')).show();
	      });
	      var open_repeat_onchange = $repeat_enabled.on('change', function () {
	        $repeat_container.toggle($__default['default'](this).prop('checked'));

	        if ($__default['default'](this).prop('checked')) {
	          repeat$1.prepareButtonNextState();
	        } else {
	          $next_step.prop('disabled', false);
	        }
	      });

	      if (response.repeated) {
	        var repeat_data = response.repeat_data;
	        var repeat_params = repeat_data.params;
	        $repeat_enabled.prop('checked', true);
	        $repeat_variant.val(repeat(repeat_data));
	        var until = repeat_data.until.split('-');
	        $date_until.pickadate('set').set('select', new Date(until[0], until[1] - 1, until[2]));

	        switch (repeat(repeat_data)) {
	          case 'daily':
	            $repeat_every_day.val(every(repeat_params));
	            break;

	          case 'weekly': //break skipped

	          case 'biweekly':
	            $__default['default']('.bookly-js-week-days input[type="checkbox"]', $repeat_container).prop('checked', false).parent().removeClass('active');

	            forEach(_context3 = repeat_params.on).call(_context3, function (val) {
	              $__default['default']('.bookly-js-week-days input:checkbox[value=' + val + ']', $repeat_container).prop('checked', true);
	            });

	            break;

	          case 'monthly':
	            if (repeat_params.on === 'day') {
	              $variant_monthly.val('specific');
	              $__default['default']('.bookly-js-monthly-specific-day[value=' + repeat_params.day + ']', $repeat_container).prop('checked', true);
	            } else {
	              $variant_monthly.val(repeat_params.on);
	              $monthly_week_day.val(repeat_params.weekday);
	            }

	            break;
	        }

	        repeat$1.renderFullSchedule(response.schedule);
	      }

	      open_repeat_onchange.trigger('change');

	      if (!response.could_be_repeated) {
	        $repeat_enabled.attr('disabled', true);
	      }

	      $repeat_variant.on('change', function () {
	        $variants.hide();

	        find($repeat_container).call($repeat_container, '.bookly-js-variant-' + this.value).show();

	        repeat$1.updateRepeatTimes();
	      }).trigger('change');
	      $variant_monthly.on('change', function () {
	        $monthly_week_day.toggle(this.value != 'specific');
	        $monthly_specific_day.toggle(this.value == 'specific');
	        repeat$1.updateRepeatTimes();
	      }).trigger('change');
	      $__default['default']('.bookly-js-week-days input', $repeat_container).on('change', function () {
	        repeat$1.updateRepeatTimes();
	      });
	      $monthly_specific_day.val(response.date_min[2]);
	      $monthly_specific_day.on('change', function () {
	        repeat$1.updateRepeatTimes();
	      });
	      $monthly_week_day.on('change', function () {
	        repeat$1.updateRepeatTimes();
	      });
	      $date_until.on('change', function () {
	        repeat$1.updateRepeatTimes();
	      });
	      $repeat_every_day.on('change', function () {
	        repeat$1.updateRepeatTimes();
	      });
	      $repeat_times.on('change', function () {
	        repeat$1.updateRepeatDate();
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

	        switch (repeat(data)) {
	          case 'daily':
	            data.params = {
	              every: $repeat_every_day.val()
	            };
	            break;

	          case 'weekly':
	          case 'biweekly':
	            data.params.on = [];
	            $__default['default']('.bookly-js-week-days input[type="checkbox"]:checked', $variant_weekly).each(function () {
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
	          repeat$1.renderFullSchedule(response.data);
	          ladda.stop();
	        });
	      });
	      $__default['default']('.bookly-js-back-step', $container).on('click', function (e) {
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
	      $__default['default']('.bookly-js-go-to-cart', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this);
	        stepCart({
	          form_id: params.form_id,
	          from_step: 'repeat'
	        });
	      });
	      $__default['default']('.bookly-js-next-step', $container).on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        laddaStart(this); // Execute custom JavaScript

	        if (customJS) {
	          try {
	            $__default['default'].globalEval(customJS.next_button);
	          } catch (e) {// Do nothing
	          }
	        }

	        if ($repeat_enabled.is(':checked')) {
	          var slots_to_send = [];
	          var repeat = 0;

	          forEach(schedule).call(schedule, function (item) {
	            if (!item.deleted) {
	              var slots = JSON.parse(item.slots);
	              slots_to_send = concat(slots_to_send).call(slots_to_send, slots);
	              repeat++;
	            }
	          });

	          booklyAjax({
	            type: 'POST',
	            data: {
	              action: 'bookly_session_save',
	              form_id: params.form_id,
	              slots: stringify(slots_to_send),
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

	  $__default['default'].extend(data, params); // Build slots html

	  function prepareSlotsHtml(slots_data, selected_date) {
	    var response = {};
	    $__default['default'].each(slots_data, function (group, group_slots) {
	      var html = '<button class="bookly-day" value="' + group + '">' + group_slots.title + '</button>';
	      $__default['default'].each(group_slots.slots, function (id, slot) {
	        html += '<button value="' + stringify(slot.data).replace(/"/g, '&quot;') + '" data-group="' + group + '" class="bookly-hour' + (slot.special_hour ? ' bookly-slot-in-special-hour' : '') + (slot.status == 'waiting-list' ? ' bookly-slot-in-waiting-list' : slot.status == 'booked' ? ' booked' : '') + '"' + (slot.status == 'booked' ? ' disabled' : '') + '>' + '<span class="ladda-label bookly-time-main' + (slot.data[0][2] == selected_date ? ' bookly-bold' : '') + '">' + '<i class="bookly-hour-icon"><span></span></i>' + slot.time_text + '</span>' + '<span class="bookly-time-additional' + (slot.status == 'waiting-list' ? ' bookly-waiting-list' : '') + '"> ' + slot.additional_text + '</span>' + '</button>';
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
	    var $columnizer_wrap = $__default['default']('.bookly-columnizer-wrap', $container),
	        $columnizer = $__default['default']('.bookly-columnizer', $columnizer_wrap),
	        $time_next_button = $__default['default']('.bookly-time-next', $container),
	        $time_prev_button = $__default['default']('.bookly-time-prev', $container),
	        $current_screen = null,
	        slot_height = 36,
	        column_width = response.time_slots_wide ? 205 : 127,
	        column_class = response.time_slots_wide ? 'bookly-column bookly-column-wide' : 'bookly-column',
	        columns = 0,
	        screen_index = 0,
	        has_more_slots = response.has_more_slots,
	        form_hidden = false,
	        show_calendar = response.show_calendar,
	        is_rtl = response.is_rtl,
	        $screens,
	        slots_per_column,
	        columns_per_screen,
	        show_day_per_column = response.day_one_column,
	        slots = prepareSlotsHtml(response.slots_data, response.selected_date),
	        customJS = response.custom_js; // 'BACK' button.

	    $__default['default']('.bookly-js-back-step', $container).on('click', function (e) {
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
	    $__default['default']('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'time'
	      });
	    }); // Time zone switcher.

	    $__default['default']('.bookly-js-time-zone-switcher', $container).on('change', function (e) {
	      opt[params.form_id].timeZone = this.value;
	      opt[params.form_id].timeZoneOffset = undefined;
	      showSpinner();
	      requestRenderTime.cancel();
	      stepTime({
	        form_id: params.form_id,
	        time_zone: opt[params.form_id].timeZone
	      });
	    });

	    if (show_calendar) {
	      // Init calendar.
	      var $input = $__default['default']('.bookly-js-selected-date', $container);
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
	          $__default['default']('.picker__nav--next', $container).on('click', function (e) {
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
	          $__default['default']('.picker__nav--prev', $container).on('click', function (e) {
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
	      }); // Insert slots for selected day.

	      var date = $input.pickadate('picker').get('select', 'yyyy-mm-dd');
	      $columnizer.html(slots[date]);
	    } else {
	      // Insert all slots.
	      var slots_data = '';
	      $__default['default'].each(slots, function (group, group_slots) {
	        slots_data += group_slots;
	      });
	      $columnizer.html(slots_data);
	    }

	    if (response.has_slots) {
	      if (error_message) {
	        find($container).call($container, '.bookly-label-error').html(error_message);
	      } else {
	        find($container).call($container, '.bookly-label-error').hide();
	      } // Calculate number of slots per column.


	      slots_per_column = _parseInt($__default['default'](window).height() / slot_height, 10);

	      if (slots_per_column < 4) {
	        slots_per_column = 4;
	      } else if (slots_per_column > 10) {
	        slots_per_column = 10;
	      }

	      columns_per_screen = _parseInt($columnizer_wrap.width() / column_width, 10);

	      if (columns_per_screen > 10) {
	        columns_per_screen = 10;
	      } else if (columns_per_screen == 0) {
	        // Bookly form display hidden.
	        form_hidden = true;
	        columns_per_screen = 4;
	      }

	      initSlots();

	      if (!has_more_slots && $screens.length == 1) {
	        $time_next_button.hide();
	      }

	      var hammertime = $__default['default']('.bookly-time-step', $container).hammer({
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

	          if (screen_index + 1 == $screens.length && !has_more_slots) {
	            $time_next_button.hide();
	          }
	        } else if (has_more_slots) {
	          // Do ajax request when there are more slots.
	          var $button = $__default['default']('> button:last', $columnizer);

	          if ($button.length == 0) {
	            $button = $__default['default']('.bookly-column:hidden:last > button:last', $columnizer);

	            if ($button.length == 0) {
	              $button = $__default['default']('.bookly-column:last > button:last', $columnizer);
	            }
	          } // Render Next Time


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
	              $__default['default'].each(prepareSlotsHtml(response.slots_data, response.selected_date), function (group, group_slots) {
	                slots_data += group_slots;
	              });
	              var $html = $__default['default'](slots_data); // The first slot is always a day slot.
	              // Check if such day slot already exists (this can happen
	              // because of time zone offset) and then remove the first slot.

	              var $first_day = $html.eq(0);

	              if ($__default['default']('button.bookly-day[value="' + $first_day.attr('value') + '"]', $container).length) {
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
	      $__default['default']('.bookly-time-screen,.bookly-not-time-screen', $container).addClass('bookly-spin-overlay');
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
	        new Spinner(opts).spin($__default['default']('.bookly-not-time-screen', $container).get(0));
	      }
	    }

	    function initSlots() {
	      var $buttons = $__default['default']('> button', $columnizer),
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
	            $column = $__default['default']('<div class="' + column_class + '" />');
	            $button = $__default['default'](splice($buttons).call($buttons, 0, 1));
	            $button.addClass('bookly-js-first-child');
	            $column.append($button);
	          } else {
	            slots_count++;
	            $button = $__default['default'](splice($buttons).call($buttons, 0, 1)); // If it is last slot in the column.

	            if (!$buttons.length || $buttons.eq(0).hasClass('bookly-day')) {
	              $button.addClass('bookly-last-child');
	              $column.append($button);
	              $columnizer.append($column);
	            } else {
	              $column.append($button);
	            }
	          } // Calculate max number of slots.


	          if (slots_count > max_slots) {
	            max_slots = slots_count;
	          }
	        }
	      } else {
	        /**
	         * Create columns for normal mode.
	         */
	        while (has_more_slots ? $buttons.length > slots_per_column : $buttons.length) {
	          $column = $__default['default']('<div class="' + column_class + '" />');
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

	            $button = $__default['default'](splice($buttons).call($buttons, 0, 1));

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


	      var $columns = $__default['default']('> .bookly-column', $columnizer);

	      while (has_more_slots ? $columns.length >= columns_per_screen : $columns.length) {
	        $screen = $__default['default']('<div class="bookly-time-screen"/>');

	        for (var i = 0; i < columns_per_screen; ++i) {
	          $column = $__default['default'](splice($columns).call($columns, 0, 1));

	          if (i == 0) {
	            $column.addClass('bookly-js-first-column');

	            var $first_slot = find($column).call($column, '.bookly-js-first-child'); // In the first column the first slot is time.


	            if (!$first_slot.hasClass('bookly-day')) {
	              var group = $first_slot.data('group'),
	                  $group_slot = $__default['default']('button.bookly-day[value="' + group + '"]:last', $container); // Copy group slot to the first column.

	              $column.prepend($group_slot.clone());
	            }
	          }

	          $screen.append($column);
	        }

	        $columnizer.append($screen);
	      }

	      $screens = $__default['default']('.bookly-time-screen', $columnizer);

	      if ($current_screen === null) {
	        $current_screen = $screens.eq(0);
	      }

	      $__default['default']('button.bookly-time-skip', $container).off('click').on('click', function (e) {
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
	      }); // On click on a slot.

	      $__default['default']('button.bookly-hour', $container).off('click').on('click', function (e) {
	        requestSessionSave.cancel();
	        e.stopPropagation();
	        e.preventDefault();
	        var $this = $__default['default'](this),
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
	        laddaStart(this); // Execute custom JavaScript

	        if (customJS) {
	          try {
	            $__default['default'].globalEval(customJS.next_button);
	          } catch (e) {// Do nothing
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
	          } else if (!repeat(opt[params.form_id].skip_steps) && opt[params.form_id].recurrence_enabled) {
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
	      }); // Columnizer width & height.

	      $__default['default']('.bookly-time-step', $container).width(columns_per_screen * column_width);
	      $columnizer_wrap.height(form_hidden ? $__default['default']('.bookly-column.bookly-js-first-column button', $current_screen).length * (slot_height + 3) : $current_screen.height());
	      form_hidden = false;
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

	  $__default['default'].extend(data, params);
	  booklyAjax({
	    data: data
	  }).then(function (response) {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    var $next_step = $__default['default']('.bookly-js-next-step', $container),
	        $back_step = $__default['default']('.bookly-js-back-step', $container),
	        $goto_cart = $__default['default']('.bookly-js-go-to-cart', $container),
	        $extras_items = $__default['default']('.bookly-js-extras-item', $container),
	        $extras_summary = $__default['default']('.bookly-js-extras-summary span', $container),
	        customJS = response.custom_js,
	        $this,
	        $input,
	        format = new Format(response);

	    var extrasChanged = function extrasChanged($extras_item, quantity) {
	      var $input = find($extras_item).call($extras_item, 'input'),
	          $total = find($extras_item).call($extras_item, '.bookly-js-extras-total-price'),
	          total_price = quantity * _parseFloat($extras_item.data('price'));

	      $total.text(format.price(total_price));
	      $input.val(quantity);

	      find($extras_item).call($extras_item, '.bookly-js-extras-thumb').toggleClass('bookly-extras-selected', quantity > 0); // Updating summary


	      var amount = 0;
	      $extras_items.each(function (index, elem) {
	        var $this = $__default['default'](this),
	            multiplier = $this.closest('.bookly-js-extras-container').data('multiplier');
	        amount += _parseFloat($this.data('price')) * find($this).call($this, 'input').val() * multiplier;
	      });

	      if (amount) {
	        $extras_summary.html(' + ' + format.price(amount));
	      } else {
	        $extras_summary.html('');
	      }
	    };

	    $extras_items.each(function (index, elem) {
	      var $this = $__default['default'](this);

	      var $input = find($this).call($this, 'input');

	      $__default['default']('.bookly-js-extras-thumb', $this).on('click', function () {
	        extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	      }).keypress(function (e) {
	        e.preventDefault();

	        if (e.which == 13 || e.which == 32) {
	          extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : $this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity'));
	        }
	      });

	      find($this).call($this, '.bookly-js-count-control').on('click', function () {
	        var count = _parseInt($input.val());

	        count = $__default['default'](this).hasClass('bookly-js-extras-increment') ? Math.min($this.data('max_quantity'), count + 1) : Math.max($this.data('min_quantity'), count - 1);
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
	      laddaStart(this); // Execute custom JavaScript

	      if (customJS) {
	        try {
	          $__default['default'].globalEval(customJS.next_button);
	        } catch (e) {// Do nothing
	        }
	      }

	      var extras = {};
	      $__default['default']('.bookly-js-extras-container', $container).each(function () {
	        var $extras_container = $__default['default'](this);
	        var chain_id = $extras_container.data('chain');
	        var chain_extras = {}; // Get checked extras for chain.

	        find($extras_container).call($extras_container, '.bookly-js-extras-item').each(function (index, elem) {
	          $this = $__default['default'](this);
	          $input = find($this).call($this, 'input');

	          if ($input.val() > 0) {
	            chain_extras[$this.data('id')] = $input.val();
	          }
	        });

	        extras[chain_id] = stringify(chain_extras);
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
	        } else if (!repeat(opt[params.form_id].skip_steps) && opt[params.form_id].recurrence_enabled) {
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

	  forEach(_context = ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"]).call(_context, function (event) {
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

	// `Object.create` method
	// https://tc39.es/ecma262/#sec-object.create
	_export({ target: 'Object', stat: true, sham: !descriptors }, {
	  create: objectCreate
	});

	var Object$1 = path.Object;

	var create$4 = function create(P, D) {
	  return Object$1.create(P, D);
	};

	var create$3 = create$4;

	var create$2 = create$3;

	// `Object.setPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.setprototypeof
	_export({ target: 'Object', stat: true }, {
	  setPrototypeOf: objectSetPrototypeOf
	});

	var setPrototypeOf$2 = path.Object.setPrototypeOf;

	var setPrototypeOf$1 = setPrototypeOf$2;

	var setPrototypeOf = setPrototypeOf$1;

	function _setPrototypeOf(o, p) {
	  _setPrototypeOf = setPrototypeOf || function _setPrototypeOf(o, p) {
	    o.__proto__ = p;
	    return o;
	  };

	  return _setPrototypeOf(o, p);
	}

	function _inherits(subClass, superClass) {
	  if (typeof superClass !== "function" && superClass !== null) {
	    throw new TypeError("Super expression must either be null or a function");
	  }

	  subClass.prototype = create$2(superClass && superClass.prototype, {
	    constructor: {
	      value: subClass,
	      writable: true,
	      configurable: true
	    }
	  });
	  if (superClass) _setPrototypeOf(subClass, superClass);
	}

	function _possibleConstructorReturn(self, call) {
	  if (call && (_typeof(call) === "object" || typeof call === "function")) {
	    return call;
	  }

	  return _assertThisInitialized(self);
	}

	var FAILS_ON_PRIMITIVES$2 = fails(function () { objectGetPrototypeOf(1); });

	// `Object.getPrototypeOf` method
	// https://tc39.es/ecma262/#sec-object.getprototypeof
	_export({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$2, sham: !correctPrototypeGetter }, {
	  getPrototypeOf: function getPrototypeOf(it) {
	    return objectGetPrototypeOf(toObject(it));
	  }
	});

	var getPrototypeOf$2 = path.Object.getPrototypeOf;

	var getPrototypeOf$1 = getPrototypeOf$2;

	var getPrototypeOf = getPrototypeOf$1;

	function _getPrototypeOf(o) {
	  _getPrototypeOf = setPrototypeOf ? getPrototypeOf : function _getPrototypeOf(o) {
	    return o.__proto__ || getPrototypeOf(o);
	  };
	  return _getPrototypeOf(o);
	}

	// `Array.isArray` method
	// https://tc39.es/ecma262/#sec-array.isarray
	_export({ target: 'Array', stat: true }, {
	  isArray: isArray$3
	});

	var isArray$2 = path.Array.isArray;

	var isArray$1 = isArray$2;

	var isArray = isArray$1;

	function _arrayWithHoles(arr) {
	  if (isArray(arr)) return arr;
	}

	var ITERATOR = wellKnownSymbol('iterator');

	var isIterable$1 = function (it) {
	  var O = Object(it);
	  return O[ITERATOR] !== undefined
	    || '@@iterator' in O
	    // eslint-disable-next-line no-prototype-builtins -- safe
	    || iterators.hasOwnProperty(classof(O));
	};

	var isIterable_1 = isIterable$1;

	var isIterable = isIterable_1;

	var getIterator$1 = function (it) {
	  var iteratorMethod = getIteratorMethod(it);
	  if (typeof iteratorMethod != 'function') {
	    throw TypeError(String(it) + ' is not iterable');
	  } return anObject(iteratorMethod.call(it));
	};

	var getIterator_1 = getIterator$1;

	var getIterator = getIterator_1;

	function _iterableToArrayLimit(arr, i) {
	  if (typeof symbol === "undefined" || !isIterable(Object(arr))) return;
	  var _arr = [];
	  var _n = true;
	  var _d = false;
	  var _e = undefined;

	  try {
	    for (var _i = getIterator(arr), _s; !(_n = (_s = _i.next()).done); _n = true) {
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

	var slice$2 = slice_1;

	var slice$1 = slice$2;

	// call something on iterator step with safe closing on error
	var callWithSafeIterationClosing = function (iterator, fn, value, ENTRIES) {
	  try {
	    return ENTRIES ? fn(anObject(value)[0], value[1]) : fn(value);
	  // 7.4.6 IteratorClose(iterator, completion)
	  } catch (error) {
	    iteratorClose(iterator);
	    throw error;
	  }
	};

	// `Array.from` method implementation
	// https://tc39.es/ecma262/#sec-array.from
	var arrayFrom = function from(arrayLike /* , mapfn = undefined, thisArg = undefined */) {
	  var O = toObject(arrayLike);
	  var C = typeof this == 'function' ? this : Array;
	  var argumentsLength = arguments.length;
	  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
	  var mapping = mapfn !== undefined;
	  var iteratorMethod = getIteratorMethod(O);
	  var index = 0;
	  var length, result, step, iterator, next, value;
	  if (mapping) mapfn = functionBindContext(mapfn, argumentsLength > 2 ? arguments[2] : undefined, 2);
	  // if the target is not iterable or it's an array with the default iterator - use a simple case
	  if (iteratorMethod != undefined && !(C == Array && isArrayIteratorMethod(iteratorMethod))) {
	    iterator = iteratorMethod.call(O);
	    next = iterator.next;
	    result = new C();
	    for (;!(step = next.call(iterator)).done; index++) {
	      value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
	      createProperty(result, index, value);
	    }
	  } else {
	    length = toLength(O.length);
	    result = new C(length);
	    for (;length > index; index++) {
	      value = mapping ? mapfn(O[index], index) : O[index];
	      createProperty(result, index, value);
	    }
	  }
	  result.length = index;
	  return result;
	};

	var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
	  // eslint-disable-next-line es/no-array-from -- required for testing
	  Array.from(iterable);
	});

	// `Array.from` method
	// https://tc39.es/ecma262/#sec-array.from
	_export({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
	  from: arrayFrom
	});

	var from$4 = path.Array.from;

	var from$3 = from$4;

	var from$2 = from$3;

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

	  var n = slice$1(_context = Object.prototype.toString.call(o)).call(_context, 8, -1);

	  if (n === "Object" && o.constructor) n = o.constructor.name;
	  if (n === "Map" || n === "Set") return from$2(o);
	  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
	}

	function _nonIterableRest() {
	  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	function _slicedToArray(arr, i) {
	  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest();
	}

	var defineProperty$1 = objectDefineProperty.f;








	var fastKey = internalMetadata.fastKey;


	var setInternalState = internalState.set;
	var internalStateGetterFor = internalState.getterFor;

	var collectionStrong = {
	  getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
	    var C = wrapper(function (that, iterable) {
	      anInstance(that, C, CONSTRUCTOR_NAME);
	      setInternalState(that, {
	        type: CONSTRUCTOR_NAME,
	        index: objectCreate(null),
	        first: undefined,
	        last: undefined,
	        size: 0
	      });
	      if (!descriptors) that.size = 0;
	      if (iterable != undefined) iterate(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
	    });

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
	        if (descriptors) state.size++;
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

	    redefineAll(C.prototype, {
	      // 23.1.3.1 Map.prototype.clear()
	      // 23.2.3.2 Set.prototype.clear()
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
	        if (descriptors) state.size = 0;
	        else that.size = 0;
	      },
	      // 23.1.3.3 Map.prototype.delete(key)
	      // 23.2.3.4 Set.prototype.delete(value)
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
	          if (descriptors) state.size--;
	          else that.size--;
	        } return !!entry;
	      },
	      // 23.2.3.6 Set.prototype.forEach(callbackfn, thisArg = undefined)
	      // 23.1.3.5 Map.prototype.forEach(callbackfn, thisArg = undefined)
	      forEach: function forEach(callbackfn /* , that = undefined */) {
	        var state = getInternalState(this);
	        var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	        var entry;
	        while (entry = entry ? entry.next : state.first) {
	          boundFunction(entry.value, entry.key, this);
	          // revert to the last existing entry
	          while (entry && entry.removed) entry = entry.previous;
	        }
	      },
	      // 23.1.3.7 Map.prototype.has(key)
	      // 23.2.3.7 Set.prototype.has(value)
	      has: function has(key) {
	        return !!getEntry(this, key);
	      }
	    });

	    redefineAll(C.prototype, IS_MAP ? {
	      // 23.1.3.6 Map.prototype.get(key)
	      get: function get(key) {
	        var entry = getEntry(this, key);
	        return entry && entry.value;
	      },
	      // 23.1.3.9 Map.prototype.set(key, value)
	      set: function set(key, value) {
	        return define(this, key === 0 ? 0 : key, value);
	      }
	    } : {
	      // 23.2.3.1 Set.prototype.add(value)
	      add: function add(value) {
	        return define(this, value = value === 0 ? 0 : value, value);
	      }
	    });
	    if (descriptors) defineProperty$1(C.prototype, 'size', {
	      get: function () {
	        return getInternalState(this).size;
	      }
	    });
	    return C;
	  },
	  setStrong: function (C, CONSTRUCTOR_NAME, IS_MAP) {
	    var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
	    var getInternalCollectionState = internalStateGetterFor(CONSTRUCTOR_NAME);
	    var getInternalIteratorState = internalStateGetterFor(ITERATOR_NAME);
	    // add .keys, .values, .entries, [@@iterator]
	    // 23.1.3.4, 23.1.3.8, 23.1.3.11, 23.1.3.12, 23.2.3.5, 23.2.3.8, 23.2.3.10, 23.2.3.11
	    defineIterator(C, CONSTRUCTOR_NAME, function (iterated, kind) {
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
	        return { value: undefined, done: true };
	      }
	      // return step by kind
	      if (kind == 'keys') return { value: entry.key, done: false };
	      if (kind == 'values') return { value: entry.value, done: false };
	      return { value: [entry.key, entry.value], done: false };
	    }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

	    // add [@@species], 23.1.2.2, 23.2.2.2
	    setSpecies(CONSTRUCTOR_NAME);
	  }
	};

	// `Map` constructor
	// https://tc39.es/ecma262/#sec-map-objects
	collection('Map', function (init) {
	  return function Map() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong);

	var map$2 = path.Map;

	var map$1 = map$2;

	var map = map$1;

	var slice = [].slice;
	var factories = {};

	var construct$3 = function (C, argsLength, args) {
	  if (!(argsLength in factories)) {
	    for (var list = [], i = 0; i < argsLength; i++) list[i] = 'a[' + i + ']';
	    // eslint-disable-next-line no-new-func -- we have no proper alternatives, IE8- only
	    factories[argsLength] = Function('C,a', 'return new C(' + list.join(',') + ')');
	  } return factories[argsLength](C, args);
	};

	// `Function.prototype.bind` method implementation
	// https://tc39.es/ecma262/#sec-function.prototype.bind
	var functionBind = Function.bind || function bind(that /* , ...args */) {
	  var fn = aFunction$1(this);
	  var partArgs = slice.call(arguments, 1);
	  var boundFunction = function bound(/* args... */) {
	    var args = partArgs.concat(slice.call(arguments));
	    return this instanceof boundFunction ? construct$3(fn, args.length, args) : fn.apply(that, args);
	  };
	  if (isObject(fn.prototype)) boundFunction.prototype = fn.prototype;
	  return boundFunction;
	};

	var nativeConstruct = getBuiltIn('Reflect', 'construct');

	// `Reflect.construct` method
	// https://tc39.es/ecma262/#sec-reflect.construct
	// MS Edge supports only 2 arguments and argumentsList argument is optional
	// FF Nightly sets third argument as `new.target`, but does not create `this` from it
	var NEW_TARGET_BUG = fails(function () {
	  function F() { /* empty */ }
	  return !(nativeConstruct(function () { /* empty */ }, [], F) instanceof F);
	});
	var ARGS_BUG = !fails(function () {
	  nativeConstruct(function () { /* empty */ });
	});
	var FORCED$2 = NEW_TARGET_BUG || ARGS_BUG;

	_export({ target: 'Reflect', stat: true, forced: FORCED$2, sham: FORCED$2 }, {
	  construct: function construct(Target, args /* , newTarget */) {
	    aFunction$1(Target);
	    anObject(args);
	    var newTarget = arguments.length < 3 ? Target : aFunction$1(arguments[2]);
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
	      $args.push.apply($args, args);
	      return new (functionBind.apply(Target, $args))();
	    }
	    // with altered newTarget, not support built-in constructors
	    var proto = newTarget.prototype;
	    var instance = objectCreate(isObject(proto) ? proto : Object.prototype);
	    var result = Function.apply.call(Target, instance, args);
	    return isObject(result) ? result : instance;
	  }
	});

	var construct$2 = path.Reflect.construct;

	var construct$1 = construct$2;

	var construct = construct$1;

	// `Reflect.get` method
	// https://tc39.es/ecma262/#sec-reflect.get
	function get(target, propertyKey /* , receiver */) {
	  var receiver = arguments.length < 3 ? target : arguments[2];
	  var descriptor, prototype;
	  if (anObject(target) === receiver) return target[propertyKey];
	  if (descriptor = objectGetOwnPropertyDescriptor.f(target, propertyKey)) return has$1(descriptor, 'value')
	    ? descriptor.value
	    : descriptor.get === undefined
	      ? undefined
	      : descriptor.get.call(receiver);
	  if (isObject(prototype = objectGetPrototypeOf(target))) return get(prototype, propertyKey, receiver);
	}

	_export({ target: 'Reflect', stat: true }, {
	  get: get
	});

	path.Reflect.get;

	var nativeGetOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;


	var FAILS_ON_PRIMITIVES$1 = fails(function () { nativeGetOwnPropertyDescriptor(1); });
	var FORCED$1 = !descriptors || FAILS_ON_PRIMITIVES$1;

	// `Object.getOwnPropertyDescriptor` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptor
	_export({ target: 'Object', stat: true, forced: FORCED$1, sham: !descriptors }, {
	  getOwnPropertyDescriptor: function getOwnPropertyDescriptor(it, key) {
	    return nativeGetOwnPropertyDescriptor(toIndexedObject(it), key);
	  }
	});

	createCommonjsModule(function (module) {
	var Object = path.Object;

	var getOwnPropertyDescriptor = module.exports = function getOwnPropertyDescriptor(it, key) {
	  return Object.getOwnPropertyDescriptor(it, key);
	};

	if (Object.getOwnPropertyDescriptor.sham) getOwnPropertyDescriptor.sham = true;
	});

	// https://tc39.github.io/proposal-setmap-offrom/




	var collectionFrom = function from(source /* , mapFn, thisArg */) {
	  var length = arguments.length;
	  var mapFn = length > 1 ? arguments[1] : undefined;
	  var mapping, array, n, boundFunction;
	  aFunction$1(this);
	  mapping = mapFn !== undefined;
	  if (mapping) aFunction$1(mapFn);
	  if (source == undefined) return new this();
	  array = [];
	  if (mapping) {
	    n = 0;
	    boundFunction = functionBindContext(mapFn, length > 2 ? arguments[2] : undefined, 2);
	    iterate(source, function (nextItem) {
	      array.push(boundFunction(nextItem, n++));
	    });
	  } else {
	    iterate(source, array.push, { that: array });
	  }
	  return new this(array);
	};

	// `Map.from` method
	// https://tc39.github.io/proposal-setmap-offrom/#sec-map.from
	_export({ target: 'Map', stat: true }, {
	  from: collectionFrom
	});

	// https://tc39.github.io/proposal-setmap-offrom/
	var collectionOf = function of() {
	  var length = arguments.length;
	  var A = new Array(length);
	  while (length--) A[length] = arguments[length];
	  return new this(A);
	};

	// `Map.of` method
	// https://tc39.github.io/proposal-setmap-offrom/#sec-map.of
	_export({ target: 'Map', stat: true }, {
	  of: collectionOf
	});

	// https://github.com/tc39/collection-methods
	var collectionDeleteAll = function (/* ...elements */) {
	  var collection = anObject(this);
	  var remover = aFunction$1(collection['delete']);
	  var allDeleted = true;
	  var wasDeleted;
	  for (var k = 0, len = arguments.length; k < len; k++) {
	    wasDeleted = remover.call(collection, arguments[k]);
	    allDeleted = allDeleted && wasDeleted;
	  }
	  return !!allDeleted;
	};

	// `Map.prototype.deleteAll` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  deleteAll: function deleteAll(/* ...elements */) {
	    return collectionDeleteAll.apply(this, arguments);
	  }
	});

	// `Map.prototype.emplace` method
	// https://github.com/thumbsupep/proposal-upsert
	var mapEmplace = function emplace(key, handler) {
	  var map = anObject(this);
	  var value = (map.has(key) && 'update' in handler)
	    ? handler.update(map.get(key), key, map)
	    : handler.insert(key, map);
	  map.set(key, value);
	  return value;
	};

	// `Map.prototype.emplace` method
	// https://github.com/thumbsupep/proposal-upsert
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  emplace: mapEmplace
	});

	var getMapIterator = getIterator$1 ;

	// `Map.prototype.every` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  every: function every(callbackfn /* , thisArg */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	    return !iterate(iterator, function (key, value, stop) {
	      if (!boundFunction(value, key, map)) return stop();
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).stopped;
	  }
	});

	// `Map.prototype.filter` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  filter: function filter(callbackfn /* , thisArg */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	    var newMap = new (speciesConstructor(map, getBuiltIn('Map')))();
	    var setter = aFunction$1(newMap.set);
	    iterate(iterator, function (key, value) {
	      if (boundFunction(value, key, map)) setter.call(newMap, key, value);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    return newMap;
	  }
	});

	// `Map.prototype.find` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  find: function find(callbackfn /* , thisArg */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	    return iterate(iterator, function (key, value, stop) {
	      if (boundFunction(value, key, map)) return stop(value);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).result;
	  }
	});

	// `Map.prototype.findKey` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  findKey: function findKey(callbackfn /* , thisArg */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	    return iterate(iterator, function (key, value, stop) {
	      if (boundFunction(value, key, map)) return stop(key);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).result;
	  }
	});

	// `Map.groupBy` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', stat: true }, {
	  groupBy: function groupBy(iterable, keyDerivative) {
	    var newMap = new this();
	    aFunction$1(keyDerivative);
	    var has = aFunction$1(newMap.has);
	    var get = aFunction$1(newMap.get);
	    var set = aFunction$1(newMap.set);
	    iterate(iterable, function (element) {
	      var derivedKey = keyDerivative(element);
	      if (!has.call(newMap, derivedKey)) set.call(newMap, derivedKey, [element]);
	      else get.call(newMap, derivedKey).push(element);
	    });
	    return newMap;
	  }
	});

	// `SameValueZero` abstract operation
	// https://tc39.es/ecma262/#sec-samevaluezero
	var sameValueZero = function (x, y) {
	  // eslint-disable-next-line no-self-compare -- NaN check
	  return x === y || x != x && y != y;
	};

	// `Map.prototype.includes` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  includes: function includes(searchElement) {
	    return iterate(getMapIterator(anObject(this)), function (key, value, stop) {
	      if (sameValueZero(value, searchElement)) return stop();
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).stopped;
	  }
	});

	// `Map.keyBy` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', stat: true }, {
	  keyBy: function keyBy(iterable, keyDerivative) {
	    var newMap = new this();
	    aFunction$1(keyDerivative);
	    var setter = aFunction$1(newMap.set);
	    iterate(iterable, function (element) {
	      setter.call(newMap, keyDerivative(element), element);
	    });
	    return newMap;
	  }
	});

	// `Map.prototype.includes` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  keyOf: function keyOf(searchElement) {
	    return iterate(getMapIterator(anObject(this)), function (key, value, stop) {
	      if (value === searchElement) return stop(key);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).result;
	  }
	});

	// `Map.prototype.mapKeys` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  mapKeys: function mapKeys(callbackfn /* , thisArg */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	    var newMap = new (speciesConstructor(map, getBuiltIn('Map')))();
	    var setter = aFunction$1(newMap.set);
	    iterate(iterator, function (key, value) {
	      setter.call(newMap, boundFunction(value, key, map), value);
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    return newMap;
	  }
	});

	// `Map.prototype.mapValues` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  mapValues: function mapValues(callbackfn /* , thisArg */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	    var newMap = new (speciesConstructor(map, getBuiltIn('Map')))();
	    var setter = aFunction$1(newMap.set);
	    iterate(iterator, function (key, value) {
	      setter.call(newMap, key, boundFunction(value, key, map));
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    return newMap;
	  }
	});

	// `Map.prototype.merge` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  // eslint-disable-next-line no-unused-vars -- required for `.length`
	  merge: function merge(iterable /* ...iterbles */) {
	    var map = anObject(this);
	    var setter = aFunction$1(map.set);
	    var i = 0;
	    while (i < arguments.length) {
	      iterate(arguments[i++], setter, { that: map, AS_ENTRIES: true });
	    }
	    return map;
	  }
	});

	// `Map.prototype.reduce` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  reduce: function reduce(callbackfn /* , initialValue */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var noInitial = arguments.length < 2;
	    var accumulator = noInitial ? undefined : arguments[1];
	    aFunction$1(callbackfn);
	    iterate(iterator, function (key, value) {
	      if (noInitial) {
	        noInitial = false;
	        accumulator = value;
	      } else {
	        accumulator = callbackfn(accumulator, value, key, map);
	      }
	    }, { AS_ENTRIES: true, IS_ITERATOR: true });
	    if (noInitial) throw TypeError('Reduce of empty map with no initial value');
	    return accumulator;
	  }
	});

	// `Set.prototype.some` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  some: function some(callbackfn /* , thisArg */) {
	    var map = anObject(this);
	    var iterator = getMapIterator(map);
	    var boundFunction = functionBindContext(callbackfn, arguments.length > 1 ? arguments[1] : undefined, 3);
	    return iterate(iterator, function (key, value, stop) {
	      if (boundFunction(value, key, map)) return stop();
	    }, { AS_ENTRIES: true, IS_ITERATOR: true, INTERRUPTED: true }).stopped;
	  }
	});

	// `Set.prototype.update` method
	// https://github.com/tc39/proposal-collection-methods
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  update: function update(key, callback /* , thunk */) {
	    var map = anObject(this);
	    var length = arguments.length;
	    aFunction$1(callback);
	    var isPresentInMap = map.has(key);
	    if (!isPresentInMap && length < 3) {
	      throw TypeError('Updating absent value');
	    }
	    var value = isPresentInMap ? map.get(key) : aFunction$1(length > 2 ? arguments[2] : undefined)(key, map);
	    map.set(key, callback(value, key, map));
	    return map;
	  }
	});

	// `Map.prototype.upsert` method
	// https://github.com/thumbsupep/proposal-upsert
	var mapUpsert = function upsert(key, updateFn /* , insertFn */) {
	  var map = anObject(this);
	  var insertFn = arguments.length > 2 ? arguments[2] : undefined;
	  var value;
	  if (typeof updateFn != 'function' && typeof insertFn != 'function') {
	    throw TypeError('At least one callback required');
	  }
	  if (map.has(key)) {
	    value = map.get(key);
	    if (typeof updateFn == 'function') {
	      value = updateFn(value);
	      map.set(key, value);
	    }
	  } else if (typeof insertFn == 'function') {
	    value = insertFn();
	    map.set(key, value);
	  } return value;
	};

	// TODO: remove from `core-js@4`




	// `Map.prototype.upsert` method (replaced by `Map.prototype.emplace`)
	// https://github.com/thumbsupep/proposal-upsert
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  upsert: mapUpsert
	});

	// TODO: remove from `core-js@4`




	// `Map.prototype.updateOrInsert` method (replaced by `Map.prototype.emplace`)
	// https://github.com/thumbsupep/proposal-upsert
	_export({ target: 'Map', proto: true, real: true, forced: isPure }, {
	  updateOrInsert: mapUpsert
	});

	// `Function.prototype.bind` method
	// https://tc39.es/ecma262/#sec-function.prototype.bind
	_export({ target: 'Function', proto: true }, {
	  bind: functionBind
	});

	entryVirtual('Function').bind;

	function _arrayWithoutHoles(arr) {
	  if (isArray(arr)) return _arrayLikeToArray(arr);
	}

	function _iterableToArray(iter) {
	  if (typeof symbol !== "undefined" && isIterable(Object(iter))) return from$2(iter);
	}

	function _nonIterableSpread() {
	  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	function _toConsumableArray(arr) {
	  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
	}

	var create$1 = create$4;

	var create = create$1;

	var FAILS_ON_PRIMITIVES = fails(function () { objectKeys(1); });

	// `Object.keys` method
	// https://tc39.es/ecma262/#sec-object.keys
	_export({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES }, {
	  keys: function keys(it) {
	    return objectKeys(toObject(it));
	  }
	});

	var keys$2 = path.Object.keys;

	var keys$1 = keys$2;

	var keys = keys$1;

	// `Set` constructor
	// https://tc39.es/ecma262/#sec-set-objects
	collection('Set', function (init) {
	  return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong);

	var set$2 = path.Set;

	var set$1 = set$2;

	var set = set$1;

	// `Date.now` method
	// https://tc39.es/ecma262/#sec-date.now
	_export({ target: 'Date', stat: true }, {
	  now: function now() {
	    return new Date().getTime();
	  }
	});

	var now$3 = path.Date.now;

	var now$2 = now$3;

	var now$1 = now$2;

	// all object keys, includes non-enumerable and symbols
	var ownKeys = getBuiltIn('Reflect', 'ownKeys') || function ownKeys(it) {
	  var keys = objectGetOwnPropertyNames.f(anObject(it));
	  var getOwnPropertySymbols = objectGetOwnPropertySymbols.f;
	  return getOwnPropertySymbols ? keys.concat(getOwnPropertySymbols(it)) : keys;
	};

	// `Object.getOwnPropertyDescriptors` method
	// https://tc39.es/ecma262/#sec-object.getownpropertydescriptors
	_export({ target: 'Object', stat: true, sham: !descriptors }, {
	  getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
	    var O = toIndexedObject(object);
	    var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
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

	path.Object.getOwnPropertyDescriptors;

	var from$1 = from$4;

	var from = from$1;

	// eslint-disable-next-line es/no-object-assign -- safe
	var $assign = Object.assign;
	// eslint-disable-next-line es/no-object-defineproperty -- required for testing
	var defineProperty = Object.defineProperty;

	// `Object.assign` method
	// https://tc39.es/ecma262/#sec-object.assign
	var objectAssign = !$assign || fails(function () {
	  // should have correct order of operations (Edge bug)
	  if (descriptors && $assign({ b: 1 }, $assign(defineProperty({}, 'a', {
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
	  return $assign({}, A)[symbol] != 7 || objectKeys($assign({}, B)).join('') != alphabet;
	}) ? function assign(target, source) { // eslint-disable-line no-unused-vars -- required for `.length`
	  var T = toObject(target);
	  var argumentsLength = arguments.length;
	  var index = 1;
	  var getOwnPropertySymbols = objectGetOwnPropertySymbols.f;
	  var propertyIsEnumerable = objectPropertyIsEnumerable.f;
	  while (argumentsLength > index) {
	    var S = indexedObject(arguments[index++]);
	    var keys = getOwnPropertySymbols ? objectKeys(S).concat(getOwnPropertySymbols(S)) : objectKeys(S);
	    var length = keys.length;
	    var j = 0;
	    var key;
	    while (length > j) {
	      key = keys[j++];
	      if (!descriptors || propertyIsEnumerable.call(S, key)) T[key] = S[key];
	    }
	  } return T;
	} : $assign;

	// `Object.assign` method
	// https://tc39.es/ecma262/#sec-object.assign
	// eslint-disable-next-line es/no-object-assign -- required for testing
	_export({ target: 'Object', stat: true, forced: Object.assign !== objectAssign }, {
	  assign: objectAssign
	});

	path.Object.assign;

	// `Array.prototype.fill` method implementation
	// https://tc39.es/ecma262/#sec-array.prototype.fill
	var arrayFill = function fill(value /* , start = 0, end = @length */) {
	  var O = toObject(this);
	  var length = toLength(O.length);
	  var argumentsLength = arguments.length;
	  var index = toAbsoluteIndex(argumentsLength > 1 ? arguments[1] : undefined, length);
	  var end = argumentsLength > 2 ? arguments[2] : undefined;
	  var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
	  while (endPos > index) O[index++] = value;
	  return O;
	};

	// `Array.prototype.fill` method
	// https://tc39.es/ecma262/#sec-array.prototype.fill
	_export({ target: 'Array', proto: true }, {
	  fill: arrayFill
	});

	var fill$2 = entryVirtual('Array').fill;

	var ArrayPrototype$1 = Array.prototype;

	var fill_1 = function (it) {
	  var own = it.fill;
	  return it === ArrayPrototype$1 || (it instanceof Array && own === ArrayPrototype$1.fill) ? fill$2 : own;
	};

	var fill$1 = fill_1;

	var fill = fill$1;

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
	  return create(null);
	}

	function run_all(fns) {
	  forEach(fns).call(fns, run);
	}

	function is_function(thing) {
	  return typeof thing === 'function';
	}

	function safe_not_equal(a, b) {
	  return a != a ? b == b : a !== b || a && _typeof(a) === 'object' || typeof a === 'function';
	}

	function is_empty(obj) {
	  return keys(obj).length === 0;
	}

	var is_client = typeof window !== 'undefined';
	var now = is_client ? function () {
	  return window.performance.now();
	} : function () {
	  return now$1();
	};
	var raf = is_client ? function (cb) {
	  return requestAnimationFrame(cb);
	} : noop; // used internally for testing

	var tasks = new set();

	function run_tasks(now) {
	  forEach(tasks).call(tasks, function (task) {
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
	    promise: new promise$1(function (fulfill) {
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

	function insert(target, node, anchor) {
	  target.insertBefore(node, anchor || null);
	}

	function detach(node) {
	  node.parentNode.removeChild(node);
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
	  return from(element.childNodes);
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
	}

	function select_value(select) {
	  var selected_option = select.querySelector(':checked') || select.options[0];
	  return selected_option && selected_option.__value;
	}

	function custom_event(type, detail) {
	  var e = document.createEvent('CustomEvent');
	  e.initCustomEvent(type, false, false, detail);
	  return e;
	}

	var active_docs = new set();
	var active = 0; // https://github.com/darkskyapp/string-hash/blob/master/index.js

	function hash(str) {
	  var hash = 5381;
	  var i = str.length;

	  while (i--) {
	    hash = (hash << 5) - hash ^ str.charCodeAt(i);
	  }

	  return hash >>> 0;
	}

	function create_rule(node, a, b, duration, delay, ease, fn) {
	  var _context5, _context7, _context8, _context9;

	  var uid = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : 0;
	  var step = 16.666 / duration;
	  var keyframes = '{\n';

	  for (var p = 0; p <= 1; p += step) {
	    var t = a + (b - a) * ease(p);
	    keyframes += p * 100 + "%{".concat(fn(t, 1 - t), "}\n");
	  }

	  var rule = keyframes + "100% {".concat(fn(b, 1 - b), "}\n}");

	  var name = concat(_context5 = "__svelte_".concat(hash(rule), "_")).call(_context5, uid);

	  var doc = node.ownerDocument;
	  active_docs.add(doc);
	  var stylesheet = doc.__svelte_stylesheet || (doc.__svelte_stylesheet = doc.head.appendChild(element('style')).sheet);
	  var current_rules = doc.__svelte_rules || (doc.__svelte_rules = {});

	  if (!current_rules[name]) {
	    var _context6;

	    current_rules[name] = true;
	    stylesheet.insertRule(concat(_context6 = "@keyframes ".concat(name, " ")).call(_context6, rule), stylesheet.cssRules.length);
	  }

	  var animation = node.style.animation || '';
	  node.style.animation = concat(_context7 = concat(_context8 = concat(_context9 = "".concat(animation ? "".concat(animation, ", ") : '')).call(_context9, name, " ")).call(_context8, duration, "ms linear ")).call(_context7, delay, "ms 1 both");
	  active += 1;
	  return name;
	}

	function delete_rule(node, name) {
	  var previous = (node.style.animation || '').split(', ');

	  var next = filter(previous).call(previous, name ? function (anim) {
	    return indexOf(anim).call(anim, name) < 0;
	  } // remove specific animation
	  : function (anim) {
	    return indexOf(anim).call(anim, '__svelte') === -1;
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

	    forEach(active_docs).call(active_docs, function (doc) {
	      var stylesheet = doc.__svelte_stylesheet;
	      var i = stylesheet.cssRules.length;

	      while (i--) {
	        stylesheet.deleteRule(i);
	      }

	      doc.__svelte_rules = {};
	    });

	    active_docs.clear();
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

	function createEventDispatcher() {
	  var component = get_current_component();
	  return function (type, detail) {
	    var callbacks = component.$$.callbacks[type];

	    if (callbacks) {
	      var _context12;

	      // TODO are there situations where events could be dispatched
	      // in a server (non-DOM) environment?
	      var event = custom_event(type, detail);

	      forEach(_context12 = slice$4(callbacks).call(callbacks)).call(_context12, function (fn) {
	        fn.call(component, event);
	      });
	    }
	  };
	}

	var dirty_components = [];
	var binding_callbacks = [];
	var render_callbacks = [];
	var flush_callbacks = [];

	var resolved_promise = promise$1.resolve();

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

	var flushing = false;
	var seen_callbacks = new set();

	function flush() {
	  if (flushing) return;
	  flushing = true;

	  do {
	    // first, call beforeUpdate functions
	    // and update components
	    for (var i = 0; i < dirty_components.length; i += 1) {
	      var component = dirty_components[i];
	      set_current_component(component);
	      update(component.$$);
	    }

	    set_current_component(null);
	    dirty_components.length = 0;

	    while (binding_callbacks.length) {
	      binding_callbacks.pop()();
	    } // then, once components are updated, call
	    // afterUpdate functions. This may cause
	    // subsequent updates...


	    for (var _i = 0; _i < render_callbacks.length; _i += 1) {
	      var callback = render_callbacks[_i];

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
	  flushing = false;
	  seen_callbacks.clear();
	}

	function update($$) {
	  if ($$.fragment !== null) {
	    var _context14;

	    $$.update();
	    run_all($$.before_update);
	    var dirty = $$.dirty;
	    $$.dirty = [-1];
	    $$.fragment && $$.fragment.p($$.ctx, dirty);

	    forEach(_context14 = $$.after_update).call(_context14, add_render_callback);
	  }
	}

	var promise;

	function wait() {
	  if (!promise) {
	    promise = promise$1.resolve();
	    promise.then(function () {
	      promise = null;
	    });
	  }

	  return promise;
	}

	function dispatch(node, direction, kind) {
	  var _context15;

	  node.dispatchEvent(custom_event(concat(_context15 = "".concat(direction ? 'intro' : 'outro')).call(_context15, kind)));
	}

	var outroing = new set();
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
	    var _ref3 = config || null_transition,
	        _ref3$delay = _ref3.delay,
	        delay = _ref3$delay === void 0 ? 0 : _ref3$delay,
	        _ref3$duration = _ref3.duration,
	        duration = _ref3$duration === void 0 ? 300 : _ref3$duration,
	        _ref3$easing = _ref3.easing,
	        easing = _ref3$easing === void 0 ? identity : _ref3$easing,
	        _ref3$tick = _ref3.tick,
	        tick = _ref3$tick === void 0 ? noop : _ref3$tick,
	        css = _ref3.css;

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
	  var new_lookup = new map();
	  var deltas = new map();
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

	  var will_move = new set();
	  var did_move = new set();

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
	} // source: https://html.spec.whatwg.org/multipage/indices.html


	new set(['allowfullscreen', 'allowpaymentrequest', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'formnovalidate', 'hidden', 'ismap', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected']);

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
	      on_mount = _component$$$.on_mount,
	      on_destroy = _component$$$.on_destroy,
	      after_update = _component$$$.after_update;
	  fragment && fragment.m(target, anchor);

	  if (!customElement) {
	    // onMount happens before the initial afterUpdate
	    add_render_callback(function () {
	      var _context24;

	      var new_on_destroy = filter(_context24 = map$3(on_mount).call(on_mount, run)).call(_context24, is_function);

	      if (on_destroy) {
	        on_destroy.push.apply(on_destroy, _toConsumableArray(new_on_destroy));
	      } else {
	        // Edge case - component was destroyed immediately,
	        // most likely as a result of a binding initialising
	        run_all(new_on_destroy);
	      }

	      component.$$.on_mount = [];
	    });
	  }

	  forEach(after_update).call(after_update, add_render_callback);
	}

	function destroy_component(component, detaching) {
	  var $$ = component.$$;

	  if ($$.fragment !== null) {
	    run_all($$.on_destroy);
	    $$.fragment && $$.fragment.d(detaching); // TODO null out other refs, including component.$$ (but need to
	    // preserve final state?)

	    $$.on_destroy = $$.fragment = null;
	    $$.ctx = [];
	  }
	}

	function make_dirty(component, i) {
	  if (component.$$.dirty[0] === -1) {
	    var _context25;

	    dirty_components.push(component);
	    schedule_update();

	    fill(_context25 = component.$$.dirty).call(_context25, 0);
	  }

	  component.$$.dirty[i / 31 | 0] |= 1 << i % 31;
	}

	function init(component, options, instance, create_fragment, not_equal, props) {
	  var dirty = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : [-1];
	  var parent_component = current_component;
	  set_current_component(component);
	  var $$ = component.$$ = {
	    fragment: null,
	    ctx: null,
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
	    context: new map(parent_component ? parent_component.$$.context : options.context || []),
	    // everything else
	    callbacks: blank_object(),
	    dirty: dirty,
	    skip_bound: false
	  };
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
	  run_all($$.before_update); // `false` as a special case of no DOM component

	  $$.fragment = create_fragment ? create_fragment($$.ctx) : false;

	  if (options.target) {
	    if (options.hydrate) {
	      var nodes = children(options.target); // eslint-disable-next-line @typescript-eslint/no-non-null-assertion

	      $$.fragment && $$.fragment.l(nodes);

	      forEach(nodes).call(nodes, detach);
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
	      var callbacks = this.$$.callbacks[type] || (this.$$.callbacks[type] = []);
	      callbacks.push(callback);
	      return function () {
	        var index = indexOf(callbacks).call(callbacks, callback);

	        if (index !== -1) splice(callbacks).call(callbacks, index, 1);
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

	var propertyIsEnumerable = objectPropertyIsEnumerable.f;

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
	      if (!descriptors || propertyIsEnumerable.call(O, key)) {
	        result.push(TO_ENTRIES ? [key, O[key]] : O[key]);
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

	var $values = objectToArray.values;

	// `Object.values` method
	// https://tc39.es/ecma262/#sec-object.values
	_export({ target: 'Object', stat: true }, {
	  values: function values(O) {
	    return $values(O);
	  }
	});

	var values$2 = path.Object.values;

	var values$1 = values$2;

	var values = values$1;

	var test = [];
	var nativeSort = test.sort;

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

	var FORCED = FAILS_ON_UNDEFINED || !FAILS_ON_NULL || !STRICT_METHOD;

	// `Array.prototype.sort` method
	// https://tc39.es/ecma262/#sec-array.prototype.sort
	_export({ target: 'Array', proto: true, forced: FORCED }, {
	  sort: function sort(comparefn) {
	    return comparefn === undefined
	      ? nativeSort.call(toObject(this))
	      : nativeSort.call(toObject(this), aFunction$1(comparefn));
	  }
	});

	var sort$2 = entryVirtual('Array').sort;

	var ArrayPrototype = Array.prototype;

	var sort_1 = function (it) {
	  var own = it.sort;
	  return it === ArrayPrototype || (it instanceof Array && own === ArrayPrototype.sort) ? sort$2 : own;
	};

	var sort$1 = sort_1;

	var sort = sort$1;

	function _createSuper$2(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$2(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

	function _isNativeReflectConstruct$2() { if (typeof Reflect === "undefined" || !construct) return false; if (construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	function get_each_context$1(ctx, list, i) {
	  var child_ctx = slice$4(ctx).call(ctx);

	  child_ctx[10] = list[i];
	  return child_ctx;
	} // (30:8) {#if placeholder}


	function create_if_block_2$1(ctx) {
	  var option;
	  var t_value =
	  /*placeholder*/
	  ctx[3].name + "";
	  var t;
	  var option_value_value;
	  return {
	    c: function c() {
	      option = element("option");
	      t = text(t_value);
	      option.__value = option_value_value =
	      /*placeholder*/
	      ctx[3].id;
	      option.value = option.__value;
	    },
	    m: function m(target, anchor) {
	      insert(target, option, anchor);
	      append(option, t);
	    },
	    p: function p(ctx, dirty) {
	      if (dirty &
	      /*placeholder*/
	      8 && t_value !== (t_value =
	      /*placeholder*/
	      ctx[3].name + "")) set_data(t, t_value);

	      if (dirty &
	      /*placeholder*/
	      8 && option_value_value !== (option_value_value =
	      /*placeholder*/
	      ctx[3].id)) {
	        option.__value = option_value_value;
	        option.value = option.__value;
	      }
	    },
	    d: function d(detaching) {
	      if (detaching) detach(option);
	    }
	  };
	} // (34:12) {#if !item.hidden}


	function create_if_block_1$1(ctx) {
	  var option;
	  var t_value =
	  /*item*/
	  ctx[10].name + "";
	  var t;
	  var option_value_value;
	  return {
	    c: function c() {
	      option = element("option");
	      t = text(t_value);
	      option.__value = option_value_value =
	      /*item*/
	      ctx[10].id;
	      option.value = option.__value;
	    },
	    m: function m(target, anchor) {
	      insert(target, option, anchor);
	      append(option, t);
	    },
	    p: function p(ctx, dirty) {
	      if (dirty &
	      /*items*/
	      16 && t_value !== (t_value =
	      /*item*/
	      ctx[10].name + "")) set_data(t, t_value);

	      if (dirty &
	      /*items*/
	      16 && option_value_value !== (option_value_value =
	      /*item*/
	      ctx[10].id)) {
	        option.__value = option_value_value;
	        option.value = option.__value;
	      }
	    },
	    d: function d(detaching) {
	      if (detaching) detach(option);
	    }
	  };
	} // (33:8) {#each items as item}


	function create_each_block$1(ctx) {
	  var if_block_anchor;
	  var if_block = !
	  /*item*/
	  ctx[10].hidden && create_if_block_1$1(ctx);
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
	      if (!
	      /*item*/
	      ctx[10].hidden) {
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
	} // (40:0) {#if error}


	function create_if_block$2(ctx) {
	  var div;
	  var t;
	  return {
	    c: function c() {
	      div = element("div");
	      t = text(
	      /*error*/
	      ctx[5]);
	      attr(div, "class", "bookly-label-error");
	    },
	    m: function m(target, anchor) {
	      insert(target, div, anchor);
	      append(div, t);
	    },
	    p: function p(ctx, dirty) {
	      if (dirty &
	      /*error*/
	      32) set_data(t,
	      /*error*/
	      ctx[5]);
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
	  var if_block0 =
	  /*placeholder*/
	  ctx[3] && create_if_block_2$1(ctx);
	  var each_value =
	  /*items*/
	  ctx[4];
	  var each_blocks = [];

	  for (var i = 0; i < each_value.length; i += 1) {
	    each_blocks[i] = create_each_block$1(get_each_context$1(ctx, each_value, i));
	  }

	  var if_block1 =
	  /*error*/
	  ctx[5] && create_if_block$2(ctx);
	  return {
	    c: function c() {
	      label_1 = element("label");
	      t0 = text(
	      /*label*/
	      ctx[2]);
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
	      if (
	      /*selected*/
	      ctx[1] === void 0) add_render_callback(function () {
	        return (
	          /*select_change_handler*/
	          ctx[8].call(select)
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

	      select_option(select,
	      /*selected*/
	      ctx[1]);
	      insert(target, t2, anchor);
	      if (if_block1) if_block1.m(target, anchor);
	      insert(target, if_block1_anchor, anchor);

	      if (!mounted) {
	        dispose = [listen(select, "change",
	        /*select_change_handler*/
	        ctx[8]), listen(select, "change",
	        /*onChange*/
	        ctx[6])];
	        mounted = true;
	      }
	    },
	    p: function p(ctx, _ref) {
	      var _ref2 = _slicedToArray(_ref, 1),
	          dirty = _ref2[0];

	      if (dirty &
	      /*label*/
	      4) set_data(t0,
	      /*label*/
	      ctx[2]);

	      if (
	      /*placeholder*/
	      ctx[3]) {
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

	      if (dirty &
	      /*items*/
	      16) {
	        each_value =
	        /*items*/
	        ctx[4];

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

	      if (dirty &
	      /*selected, items, placeholder*/
	      26) {
	        select_option(select,
	        /*selected*/
	        ctx[1]);
	      }

	      if (
	      /*error*/
	      ctx[5]) {
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
	      label = _$$props$label === void 0 ? "" : _$$props$label;
	  var _$$props$placeholder = $$props.placeholder,
	      placeholder = _$$props$placeholder === void 0 ? null : _$$props$placeholder;
	  var _$$props$items = $$props.items,
	      items = _$$props$items === void 0 ? [] : _$$props$items;
	  var _$$props$selected = $$props.selected,
	      selected = _$$props$selected === void 0 ? "" : _$$props$selected;
	  var _$$props$error = $$props.error,
	      error = _$$props$error === void 0 ? null : _$$props$error;
	  var dispatch = createEventDispatcher();

	  function onChange() {
	    dispatch("change", selected);
	  }

	  function label_1_binding($$value) {
	    binding_callbacks[$$value ? "unshift" : "push"](function () {
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
	    if ("el" in $$props) $$invalidate(0, el = $$props.el);
	    if ("label" in $$props) $$invalidate(2, label = $$props.label);
	    if ("placeholder" in $$props) $$invalidate(3, placeholder = $$props.placeholder);
	    if ("items" in $$props) $$invalidate(4, items = $$props.items);
	    if ("selected" in $$props) $$invalidate(1, selected = $$props.selected);
	    if ("error" in $$props) $$invalidate(5, error = $$props.error);
	  };

	  $$self.$$.update = function () {
	    if ($$self.$$.dirty &
	    /*items*/
	    16) {
	      // Sort items by position
	      sort(items).call(items, compare);
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

	  return Select;
	}(SvelteComponent);

	path.Object.getOwnPropertySymbols;

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

	function _createSuper$1(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$1(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

	function _isNativeReflectConstruct$1() { if (typeof Reflect === "undefined" || !construct) return false; if (construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	function create_if_block_14(ctx) {
	  var div;
	  var select;
	  var updating_el;
	  var current;

	  function select_el_binding(value) {
	    /*select_el_binding*/
	    ctx[66](value);
	  }

	  var select_props = {
	    label:
	    /*l10n*/
	    ctx[16].location_label,
	    placeholder:
	    /*locationPlaceholder*/
	    ctx[30],
	    items: values(
	    /*locations*/
	    ctx[0]),
	    selected:
	    /*locationId*/
	    ctx[17],
	    error:
	    /*locationError*/
	    ctx[34]
	  };

	  if (
	  /*locationEl*/
	  ctx[35] !== void 0) {
	    select_props.el =
	    /*locationEl*/
	    ctx[35];
	  }

	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(function () {
	    return bind(select, "el", select_el_binding);
	  });
	  select.$on("change",
	  /*onLocationChange*/
	  ctx[40]);
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
	      if (dirty[0] &
	      /*l10n*/
	      65536) select_changes.label =
	      /*l10n*/
	      ctx[16].location_label;
	      if (dirty[0] &
	      /*locationPlaceholder*/
	      1073741824) select_changes.placeholder =
	      /*locationPlaceholder*/
	      ctx[30];
	      if (dirty[0] &
	      /*locations*/
	      1) select_changes.items = values(
	      /*locations*/
	      ctx[0]);
	      if (dirty[0] &
	      /*locationId*/
	      131072) select_changes.selected =
	      /*locationId*/
	      ctx[17];
	      if (dirty[1] &
	      /*locationError*/
	      8) select_changes.error =
	      /*locationError*/
	      ctx[34];

	      if (!updating_el && dirty[1] &
	      /*locationEl*/
	      16) {
	        updating_el = true;
	        select_changes.el =
	        /*locationEl*/
	        ctx[35];
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
	} // (488:4) {#if hasCategorySelect}


	function create_if_block_12(ctx) {
	  var div;
	  var select;
	  var t;
	  var show_if =
	  /*showCategoryInfo*/
	  ctx[4] &&
	  /*categoryId*/
	  ctx[18] &&
	  /*categories*/
	  ctx[1][
	  /*categoryId*/
	  ctx[18]].hasOwnProperty("info") &&
	  /*categories*/
	  ctx[1][
	  /*categoryId*/
	  ctx[18]].info !== "";
	  var if_block_anchor;
	  var current;
	  select = new Select({
	    props: {
	      label:
	      /*l10n*/
	      ctx[16].category_label,
	      placeholder:
	      /*categoryPlaceholder*/
	      ctx[31],
	      items: values(
	      /*categoryItems*/
	      ctx[26]),
	      selected:
	      /*categoryId*/
	      ctx[18]
	    }
	  });
	  select.$on("change",
	  /*onCategoryChange*/
	  ctx[41]);
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
	      if (dirty[0] &
	      /*l10n*/
	      65536) select_changes.label =
	      /*l10n*/
	      ctx[16].category_label;
	      if (dirty[1] &
	      /*categoryPlaceholder*/
	      1) select_changes.placeholder =
	      /*categoryPlaceholder*/
	      ctx[31];
	      if (dirty[0] &
	      /*categoryItems*/
	      67108864) select_changes.items = values(
	      /*categoryItems*/
	      ctx[26]);
	      if (dirty[0] &
	      /*categoryId*/
	      262144) select_changes.selected =
	      /*categoryId*/
	      ctx[18];
	      select.$set(select_changes);
	      if (dirty[0] &
	      /*showCategoryInfo, categoryId, categories*/
	      262162) show_if =
	      /*showCategoryInfo*/
	      ctx[4] &&
	      /*categoryId*/
	      ctx[18] &&
	      /*categories*/
	      ctx[1][
	      /*categoryId*/
	      ctx[18]].hasOwnProperty("info") &&
	      /*categories*/
	      ctx[1][
	      /*categoryId*/
	      ctx[18]].info !== "";

	      if (show_if) {
	        if (if_block) {
	          if_block.p(ctx, dirty);

	          if (dirty[0] &
	          /*showCategoryInfo, categoryId, categories*/
	          262162) {
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
	} // (498:8) {#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}


	function create_if_block_13(ctx) {
	  var div;
	  var raw_value =
	  /*categories*/
	  ctx[1][
	  /*categoryId*/
	  ctx[18]].info + "";
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
	      if ((!current || dirty[0] &
	      /*categories, categoryId*/
	      262146) && raw_value !== (raw_value =
	      /*categories*/
	      ctx[1][
	      /*categoryId*/
	      ctx[18]].info + "")) div.innerHTML = raw_value;
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
	} // (504:4) {#if hasServiceSelect}


	function create_if_block_10(ctx) {
	  var div;
	  var select;
	  var updating_el;
	  var t;
	  var show_if =
	  /*showServiceInfo*/
	  ctx[5] &&
	  /*serviceId*/
	  ctx[19] &&
	  /*services*/
	  ctx[2][
	  /*serviceId*/
	  ctx[19]].hasOwnProperty("info") &&
	  /*services*/
	  ctx[2][
	  /*serviceId*/
	  ctx[19]].info !== "";
	  var if_block_anchor;
	  var current;

	  function select_el_binding_1(value) {
	    /*select_el_binding_1*/
	    ctx[67](value);
	  }

	  var select_props = {
	    label:
	    /*l10n*/
	    ctx[16].service_label,
	    placeholder:
	    /*servicePlaceholder*/
	    ctx[32],
	    items: values(
	    /*serviceItems*/
	    ctx[27]),
	    selected:
	    /*serviceId*/
	    ctx[19],
	    error:
	    /*serviceError*/
	    ctx[36]
	  };

	  if (
	  /*serviceEl*/
	  ctx[37] !== void 0) {
	    select_props.el =
	    /*serviceEl*/
	    ctx[37];
	  }

	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(function () {
	    return bind(select, "el", select_el_binding_1);
	  });
	  select.$on("change",
	  /*onServiceChange*/
	  ctx[42]);
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
	      if (dirty[0] &
	      /*l10n*/
	      65536) select_changes.label =
	      /*l10n*/
	      ctx[16].service_label;
	      if (dirty[1] &
	      /*servicePlaceholder*/
	      2) select_changes.placeholder =
	      /*servicePlaceholder*/
	      ctx[32];
	      if (dirty[0] &
	      /*serviceItems*/
	      134217728) select_changes.items = values(
	      /*serviceItems*/
	      ctx[27]);
	      if (dirty[0] &
	      /*serviceId*/
	      524288) select_changes.selected =
	      /*serviceId*/
	      ctx[19];
	      if (dirty[1] &
	      /*serviceError*/
	      32) select_changes.error =
	      /*serviceError*/
	      ctx[36];

	      if (!updating_el && dirty[1] &
	      /*serviceEl*/
	      64) {
	        updating_el = true;
	        select_changes.el =
	        /*serviceEl*/
	        ctx[37];
	        add_flush_callback(function () {
	          return updating_el = false;
	        });
	      }

	      select.$set(select_changes);
	      if (dirty[0] &
	      /*showServiceInfo, serviceId, services*/
	      524324) show_if =
	      /*showServiceInfo*/
	      ctx[5] &&
	      /*serviceId*/
	      ctx[19] &&
	      /*services*/
	      ctx[2][
	      /*serviceId*/
	      ctx[19]].hasOwnProperty("info") &&
	      /*services*/
	      ctx[2][
	      /*serviceId*/
	      ctx[19]].info !== "";

	      if (show_if) {
	        if (if_block) {
	          if_block.p(ctx, dirty);

	          if (dirty[0] &
	          /*showServiceInfo, serviceId, services*/
	          524324) {
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
	} // (516:8) {#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}


	function create_if_block_11(ctx) {
	  var div;
	  var raw_value =
	  /*services*/
	  ctx[2][
	  /*serviceId*/
	  ctx[19]].info + "";
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
	      if ((!current || dirty[0] &
	      /*services, serviceId*/
	      524292) && raw_value !== (raw_value =
	      /*services*/
	      ctx[2][
	      /*serviceId*/
	      ctx[19]].info + "")) div.innerHTML = raw_value;
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
	} // (522:4) {#if hasStaffSelect}


	function create_if_block_8(ctx) {
	  var div;
	  var select;
	  var updating_el;
	  var t;
	  var show_if =
	  /*showStaffInfo*/
	  ctx[6] &&
	  /*staffId*/
	  ctx[20] &&
	  /*staff*/
	  ctx[3][
	  /*staffId*/
	  ctx[20]].hasOwnProperty("info") &&
	  /*staff*/
	  ctx[3][
	  /*staffId*/
	  ctx[20]].info !== "";
	  var if_block_anchor;
	  var current;

	  function select_el_binding_2(value) {
	    /*select_el_binding_2*/
	    ctx[68](value);
	  }

	  var select_props = {
	    label:
	    /*l10n*/
	    ctx[16].staff_label,
	    placeholder:
	    /*staffPlaceholder*/
	    ctx[33],
	    items: values(
	    /*staffItems*/
	    ctx[23]),
	    selected:
	    /*staffId*/
	    ctx[20],
	    error:
	    /*staffError*/
	    ctx[38]
	  };

	  if (
	  /*staffEl*/
	  ctx[39] !== void 0) {
	    select_props.el =
	    /*staffEl*/
	    ctx[39];
	  }

	  select = new Select({
	    props: select_props
	  });
	  binding_callbacks.push(function () {
	    return bind(select, "el", select_el_binding_2);
	  });
	  select.$on("change",
	  /*onStaffChange*/
	  ctx[43]);
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
	      if (dirty[0] &
	      /*l10n*/
	      65536) select_changes.label =
	      /*l10n*/
	      ctx[16].staff_label;
	      if (dirty[1] &
	      /*staffPlaceholder*/
	      4) select_changes.placeholder =
	      /*staffPlaceholder*/
	      ctx[33];
	      if (dirty[0] &
	      /*staffItems*/
	      8388608) select_changes.items = values(
	      /*staffItems*/
	      ctx[23]);
	      if (dirty[0] &
	      /*staffId*/
	      1048576) select_changes.selected =
	      /*staffId*/
	      ctx[20];
	      if (dirty[1] &
	      /*staffError*/
	      128) select_changes.error =
	      /*staffError*/
	      ctx[38];

	      if (!updating_el && dirty[1] &
	      /*staffEl*/
	      256) {
	        updating_el = true;
	        select_changes.el =
	        /*staffEl*/
	        ctx[39];
	        add_flush_callback(function () {
	          return updating_el = false;
	        });
	      }

	      select.$set(select_changes);
	      if (dirty[0] &
	      /*showStaffInfo, staffId, staff*/
	      1048648) show_if =
	      /*showStaffInfo*/
	      ctx[6] &&
	      /*staffId*/
	      ctx[20] &&
	      /*staff*/
	      ctx[3][
	      /*staffId*/
	      ctx[20]].hasOwnProperty("info") &&
	      /*staff*/
	      ctx[3][
	      /*staffId*/
	      ctx[20]].info !== "";

	      if (show_if) {
	        if (if_block) {
	          if_block.p(ctx, dirty);

	          if (dirty[0] &
	          /*showStaffInfo, staffId, staff*/
	          1048648) {
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
	} // (534:8) {#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}


	function create_if_block_9(ctx) {
	  var div;
	  var raw_value =
	  /*staff*/
	  ctx[3][
	  /*staffId*/
	  ctx[20]].info + "";
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
	      if ((!current || dirty[0] &
	      /*staff, staffId*/
	      1048584) && raw_value !== (raw_value =
	      /*staff*/
	      ctx[3][
	      /*staffId*/
	      ctx[20]].info + "")) div.innerHTML = raw_value;
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
	} // (540:4) {#if hasDurationSelect}


	function create_if_block_7(ctx) {
	  var div;
	  var select;
	  var current;
	  select = new Select({
	    props: {
	      label:
	      /*l10n*/
	      ctx[16].duration_label,
	      items: values(
	      /*durationItems*/
	      ctx[24]),
	      selected:
	      /*duration*/
	      ctx[21]
	    }
	  });
	  select.$on("change",
	  /*onDurationChange*/
	  ctx[44]);
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
	      if (dirty[0] &
	      /*l10n*/
	      65536) select_changes.label =
	      /*l10n*/
	      ctx[16].duration_label;
	      if (dirty[0] &
	      /*durationItems*/
	      16777216) select_changes.items = values(
	      /*durationItems*/
	      ctx[24]);
	      if (dirty[0] &
	      /*duration*/
	      2097152) select_changes.selected =
	      /*duration*/
	      ctx[21];
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
	} // (550:4) {#if hasNopSelect}


	function create_if_block_6(ctx) {
	  var div;
	  var select;
	  var current;
	  select = new Select({
	    props: {
	      label:
	      /*l10n*/
	      ctx[16].nop_label,
	      items: values(
	      /*nopItems*/
	      ctx[28]),
	      selected:
	      /*nop*/
	      ctx[22]
	    }
	  });
	  select.$on("change",
	  /*onNopChange*/
	  ctx[45]);
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
	      if (dirty[0] &
	      /*l10n*/
	      65536) select_changes.label =
	      /*l10n*/
	      ctx[16].nop_label;
	      if (dirty[0] &
	      /*nopItems*/
	      268435456) select_changes.items = values(
	      /*nopItems*/
	      ctx[28]);
	      if (dirty[0] &
	      /*nop*/
	      4194304) select_changes.selected =
	      /*nop*/
	      ctx[22];
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
	} // (560:4) {#if hasQuantitySelect}


	function create_if_block_5(ctx) {
	  var div;
	  var select;
	  var current;
	  select = new Select({
	    props: {
	      label:
	      /*l10n*/
	      ctx[16].quantity_label,
	      items: values(
	      /*quantityItems*/
	      ctx[29]),
	      selected:
	      /*quantity*/
	      ctx[25]
	    }
	  });
	  select.$on("change",
	  /*onQuantityChange*/
	  ctx[46]);
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
	      if (dirty[0] &
	      /*l10n*/
	      65536) select_changes.label =
	      /*l10n*/
	      ctx[16].quantity_label;
	      if (dirty[0] &
	      /*quantityItems*/
	      536870912) select_changes.items = values(
	      /*quantityItems*/
	      ctx[29]);
	      if (dirty[0] &
	      /*quantity*/
	      33554432) select_changes.selected =
	      /*quantity*/
	      ctx[25];
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
	} // (570:4) {#if hasDropBtn}


	function create_if_block_3(ctx) {
	  var div1;
	  var label;
	  var t;
	  var div0;
	  var if_block =
	  /*showDropBtn*/
	  ctx[15] && create_if_block_4(ctx);
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
	      if (
	      /*showDropBtn*/
	      ctx[15]) {
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
	} // (574:16) {#if showDropBtn}


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
	        dispose = listen(button, "click",
	        /*onDropBtnClick*/
	        ctx[47]);
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
	} // (581:0) {#if showCategoryInfo && categoryId && categories[categoryId].hasOwnProperty('info') && categories[categoryId].info !== ''}


	function create_if_block_2(ctx) {
	  var div;
	  var raw_value =
	  /*categories*/
	  ctx[1][
	  /*categoryId*/
	  ctx[18]].info + "";
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
	      if ((!current || dirty[0] &
	      /*categories, categoryId*/
	      262146) && raw_value !== (raw_value =
	      /*categories*/
	      ctx[1][
	      /*categoryId*/
	      ctx[18]].info + "")) div.innerHTML = raw_value;
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
	} // (586:0) {#if showServiceInfo && serviceId && services[serviceId].hasOwnProperty('info') && services[serviceId].info !== ''}


	function create_if_block_1(ctx) {
	  var div;
	  var raw_value =
	  /*services*/
	  ctx[2][
	  /*serviceId*/
	  ctx[19]].info + "";
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
	      if ((!current || dirty[0] &
	      /*services, serviceId*/
	      524292) && raw_value !== (raw_value =
	      /*services*/
	      ctx[2][
	      /*serviceId*/
	      ctx[19]].info + "")) div.innerHTML = raw_value;
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
	} // (591:0) {#if showStaffInfo && staffId && staff[staffId].hasOwnProperty('info') && staff[staffId].info !== ''}


	function create_if_block$1(ctx) {
	  var div;
	  var raw_value =
	  /*staff*/
	  ctx[3][
	  /*staffId*/
	  ctx[20]].info + "";
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
	      if ((!current || dirty[0] &
	      /*staff, staffId*/
	      1048584) && raw_value !== (raw_value =
	      /*staff*/
	      ctx[3][
	      /*staffId*/
	      ctx[20]].info + "")) div.innerHTML = raw_value;
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
	  var show_if_2 =
	  /*showCategoryInfo*/
	  ctx[4] &&
	  /*categoryId*/
	  ctx[18] &&
	  /*categories*/
	  ctx[1][
	  /*categoryId*/
	  ctx[18]].hasOwnProperty("info") &&
	  /*categories*/
	  ctx[1][
	  /*categoryId*/
	  ctx[18]].info !== "";
	  var t8;
	  var show_if_1 =
	  /*showServiceInfo*/
	  ctx[5] &&
	  /*serviceId*/
	  ctx[19] &&
	  /*services*/
	  ctx[2][
	  /*serviceId*/
	  ctx[19]].hasOwnProperty("info") &&
	  /*services*/
	  ctx[2][
	  /*serviceId*/
	  ctx[19]].info !== "";
	  var t9;
	  var show_if =
	  /*showStaffInfo*/
	  ctx[6] &&
	  /*staffId*/
	  ctx[20] &&
	  /*staff*/
	  ctx[3][
	  /*staffId*/
	  ctx[20]].hasOwnProperty("info") &&
	  /*staff*/
	  ctx[3][
	  /*staffId*/
	  ctx[20]].info !== "";
	  var if_block10_anchor;
	  var current;
	  var if_block0 =
	  /*hasLocationSelect*/
	  ctx[7] && create_if_block_14(ctx);
	  var if_block1 =
	  /*hasCategorySelect*/
	  ctx[8] && create_if_block_12(ctx);
	  var if_block2 =
	  /*hasServiceSelect*/
	  ctx[9] && create_if_block_10(ctx);
	  var if_block3 =
	  /*hasStaffSelect*/
	  ctx[10] && create_if_block_8(ctx);
	  var if_block4 =
	  /*hasDurationSelect*/
	  ctx[11] && create_if_block_7(ctx);
	  var if_block5 =
	  /*hasNopSelect*/
	  ctx[12] && create_if_block_6(ctx);
	  var if_block6 =
	  /*hasQuantitySelect*/
	  ctx[13] && create_if_block_5(ctx);
	  var if_block7 =
	  /*hasDropBtn*/
	  ctx[14] && create_if_block_3(ctx);
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
	      if (
	      /*hasLocationSelect*/
	      ctx[7]) {
	        if (if_block0) {
	          if_block0.p(ctx, dirty);

	          if (dirty[0] &
	          /*hasLocationSelect*/
	          128) {
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

	      if (
	      /*hasCategorySelect*/
	      ctx[8]) {
	        if (if_block1) {
	          if_block1.p(ctx, dirty);

	          if (dirty[0] &
	          /*hasCategorySelect*/
	          256) {
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

	      if (
	      /*hasServiceSelect*/
	      ctx[9]) {
	        if (if_block2) {
	          if_block2.p(ctx, dirty);

	          if (dirty[0] &
	          /*hasServiceSelect*/
	          512) {
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

	      if (
	      /*hasStaffSelect*/
	      ctx[10]) {
	        if (if_block3) {
	          if_block3.p(ctx, dirty);

	          if (dirty[0] &
	          /*hasStaffSelect*/
	          1024) {
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

	      if (
	      /*hasDurationSelect*/
	      ctx[11]) {
	        if (if_block4) {
	          if_block4.p(ctx, dirty);

	          if (dirty[0] &
	          /*hasDurationSelect*/
	          2048) {
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

	      if (
	      /*hasNopSelect*/
	      ctx[12]) {
	        if (if_block5) {
	          if_block5.p(ctx, dirty);

	          if (dirty[0] &
	          /*hasNopSelect*/
	          4096) {
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

	      if (
	      /*hasQuantitySelect*/
	      ctx[13]) {
	        if (if_block6) {
	          if_block6.p(ctx, dirty);

	          if (dirty[0] &
	          /*hasQuantitySelect*/
	          8192) {
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

	      if (
	      /*hasDropBtn*/
	      ctx[14]) {
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

	      if (dirty[0] &
	      /*showCategoryInfo, categoryId, categories*/
	      262162) show_if_2 =
	      /*showCategoryInfo*/
	      ctx[4] &&
	      /*categoryId*/
	      ctx[18] &&
	      /*categories*/
	      ctx[1][
	      /*categoryId*/
	      ctx[18]].hasOwnProperty("info") &&
	      /*categories*/
	      ctx[1][
	      /*categoryId*/
	      ctx[18]].info !== "";

	      if (show_if_2) {
	        if (if_block8) {
	          if_block8.p(ctx, dirty);

	          if (dirty[0] &
	          /*showCategoryInfo, categoryId, categories*/
	          262162) {
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

	      if (dirty[0] &
	      /*showServiceInfo, serviceId, services*/
	      524324) show_if_1 =
	      /*showServiceInfo*/
	      ctx[5] &&
	      /*serviceId*/
	      ctx[19] &&
	      /*services*/
	      ctx[2][
	      /*serviceId*/
	      ctx[19]].hasOwnProperty("info") &&
	      /*services*/
	      ctx[2][
	      /*serviceId*/
	      ctx[19]].info !== "";

	      if (show_if_1) {
	        if (if_block9) {
	          if_block9.p(ctx, dirty);

	          if (dirty[0] &
	          /*showServiceInfo, serviceId, services*/
	          524324) {
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

	      if (dirty[0] &
	      /*showStaffInfo, staffId, staff*/
	      1048648) show_if =
	      /*showStaffInfo*/
	      ctx[6] &&
	      /*staffId*/
	      ctx[20] &&
	      /*staff*/
	      ctx[3][
	      /*staffId*/
	      ctx[20]].hasOwnProperty("info") &&
	      /*staff*/
	      ctx[3][
	      /*staffId*/
	      ctx[20]].info !== "";

	      if (show_if) {
	        if (if_block10) {
	          if_block10.p(ctx, dirty);

	          if (dirty[0] &
	          /*showStaffInfo, staffId, staff*/
	          1048648) {
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
	  var srvMinCapacity; // Preselect values

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
	    $$invalidate(17, locationId = event.detail); // Validate value

	    if (!(locationId in locations)) {
	      $$invalidate(17, locationId = 0);
	    } // Update related values


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
	        $__default['default'].each(locations[locationId].staff, function (id) {
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
	        $__default['default'].each(locations[locationId].staff, function (id) {
	          $__default['default'].each(staff[id].services, function (srvId) {
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
	    $$invalidate(18, categoryId = event.detail); // Validate value

	    if (!(categoryId in categoryItems)) {
	      $$invalidate(18, categoryId = 0);
	    } // Update related values


	    if (categoryId) {
	      $$invalidate(61, categorySelected = true);

	      if (serviceId) {
	        if (services[serviceId].category_id !== categoryId) {
	          $$invalidate(19, serviceId = 0);
	        }
	      }

	      if (staffId) {
	        var valid = false;
	        $__default['default'].each(staff[staffId].services, function (id) {
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
	    $$invalidate(19, serviceId = event.detail); // Validate value

	    if (!(serviceId in serviceItems)) {
	      $$invalidate(19, serviceId = 0);
	    } // Update related values


	    if (serviceId) {
	      $$invalidate(18, categoryId = services[serviceId].category_id);

	      if (staffId && !(serviceId in staff[staffId].services)) {
	        $$invalidate(20, staffId = 0);
	      }

	      if (date_from_element[0]) {
	        dateMin = services[serviceId].hasOwnProperty("min_time_prior_booking") ? services[serviceId].min_time_prior_booking : date_from_element.data("date_min");
	      }
	    } else if (!categorySelected) {
	      $$invalidate(18, categoryId = 0);

	      if (date_from_element[0]) {
	        dateMin = date_from_element.data("date_min");
	      }
	    }

	    if (date_from_element[0]) {
	      date_from_element.pickadate("picker").set("min", dateMin);

	      if (date_from_element.data("updated")) {
	        date_from_element.pickadate("picker").set("select", date_from_element.pickadate("picker").get("select"));
	      } else {
	        date_from_element.pickadate("picker").set("select", dateMin);
	      }
	    }
	  }

	  function onStaffChange(event) {
	    $$invalidate(20, staffId = event.detail); // Validate value

	    if (!(staffId in staffItems)) {
	      $$invalidate(20, staffId = 0);
	    }
	  }

	  function onDurationChange(event) {
	    $$invalidate(21, duration = event.detail); // Validate value

	    if (!(duration in durationItems)) {
	      $$invalidate(21, duration = 1);
	    }
	  }

	  function onNopChange(event) {
	    $$invalidate(22, nop = event.detail); // Validate value

	    if (!(nop in nopItems)) {
	      $$invalidate(22, nop = 1);
	    }
	  }

	  function onQuantityChange(event) {
	    $$invalidate(25, quantity = event.detail); // Validate value

	    if (!(quantity in quantityItems)) {
	      $$invalidate(25, quantity = 1);
	    }
	  }

	  function onDropBtnClick() {
	    dispatch("dropItem", index);
	  }

	  function validate() {
	    var valid = true;
	    var el = null;
	    $$invalidate(38, staffError = $$invalidate(36, serviceError = $$invalidate(34, locationError = null)));

	    if (required.staff && !staffId && (!collaborativeHideStaff || !serviceId || services[serviceId].type !== "collaborative")) {
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
	      staffIds: staffId ? [staffId] : map$3($__default['default']).call($__default['default'], staffItems, function (item) {
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
	    if ("item" in $$props) $$invalidate(48, item = $$props.item);
	    if ("index" in $$props) $$invalidate(49, index = $$props.index);
	    if ("locations" in $$props) $$invalidate(0, locations = $$props.locations);
	    if ("categories" in $$props) $$invalidate(1, categories = $$props.categories);
	    if ("services" in $$props) $$invalidate(2, services = $$props.services);
	    if ("staff" in $$props) $$invalidate(3, staff = $$props.staff);
	    if ("defaults" in $$props) $$invalidate(50, defaults = $$props.defaults);
	    if ("required" in $$props) $$invalidate(51, required = $$props.required);
	    if ("servicesPerLocation" in $$props) $$invalidate(52, servicesPerLocation = $$props.servicesPerLocation);
	    if ("staffNameWithPrice" in $$props) $$invalidate(53, staffNameWithPrice = $$props.staffNameWithPrice);
	    if ("collaborativeHideStaff" in $$props) $$invalidate(54, collaborativeHideStaff = $$props.collaborativeHideStaff);
	    if ("showRatings" in $$props) $$invalidate(55, showRatings = $$props.showRatings);
	    if ("showCategoryInfo" in $$props) $$invalidate(4, showCategoryInfo = $$props.showCategoryInfo);
	    if ("showServiceInfo" in $$props) $$invalidate(5, showServiceInfo = $$props.showServiceInfo);
	    if ("showStaffInfo" in $$props) $$invalidate(6, showStaffInfo = $$props.showStaffInfo);
	    if ("maxQuantity" in $$props) $$invalidate(56, maxQuantity = $$props.maxQuantity);
	    if ("hasLocationSelect" in $$props) $$invalidate(7, hasLocationSelect = $$props.hasLocationSelect);
	    if ("hasCategorySelect" in $$props) $$invalidate(8, hasCategorySelect = $$props.hasCategorySelect);
	    if ("hasServiceSelect" in $$props) $$invalidate(9, hasServiceSelect = $$props.hasServiceSelect);
	    if ("hasStaffSelect" in $$props) $$invalidate(10, hasStaffSelect = $$props.hasStaffSelect);
	    if ("hasDurationSelect" in $$props) $$invalidate(11, hasDurationSelect = $$props.hasDurationSelect);
	    if ("hasNopSelect" in $$props) $$invalidate(12, hasNopSelect = $$props.hasNopSelect);
	    if ("hasQuantitySelect" in $$props) $$invalidate(13, hasQuantitySelect = $$props.hasQuantitySelect);
	    if ("hasDropBtn" in $$props) $$invalidate(14, hasDropBtn = $$props.hasDropBtn);
	    if ("showDropBtn" in $$props) $$invalidate(15, showDropBtn = $$props.showDropBtn);
	    if ("l10n" in $$props) $$invalidate(16, l10n = $$props.l10n);
	    if ("date_from_element" in $$props) $$invalidate(57, date_from_element = $$props.date_from_element);
	  };

	  $$self.$$.update = function () {
	    if ($$self.$$.dirty[0] &
	    /*locationId, staff, locations, serviceId, categoryId, services, staffItems, categories, staffId, nop, hasNopSelect, duration, durationItems, l10n*/
	    33493007 | $$self.$$.dirty[1] &
	    /*servicesPerLocation, lookupLocationId, staffNameWithPrice, collaborativeHideStaff, showRatings, categorySelected, maxQuantity*/
	    1675624448 | $$self.$$.dirty[2] &
	    /*srvMinCapacity, srvMaxCapacity, minCapacity, maxCapacity*/
	    15) {
	      {
	        $$invalidate(60, lookupLocationId = servicesPerLocation && locationId ? locationId : 0);
	        $$invalidate(26, categoryItems = {});
	        $$invalidate(27, serviceItems = {});
	        $$invalidate(23, staffItems = {});
	        $$invalidate(28, nopItems = {}); // Staff

	        $__default['default'].each(staff, function (id, staffMember) {
	          if (!locationId || id in locations[locationId].staff) {
	            if (!serviceId) {
	              if (!categoryId) {
	                $$invalidate(23, staffItems[id] = $__default['default'].extend({}, staffMember), staffItems);
	              } else {
	                $__default['default'].each(staffMember.services, function (srvId) {
	                  if (services[srvId].category_id === categoryId) {
	                    $$invalidate(23, staffItems[id] = $__default['default'].extend({}, staffMember), staffItems);
	                    return false;
	                  }
	                });
	              }
	            } else if (serviceId in staffMember.services) {
	              $__default['default'].each(staffMember.services[serviceId].locations, function (locId, locSrv) {
	                if (lookupLocationId && lookupLocationId !== _parseInt(locId)) {
	                  return true;
	                }

	                $$invalidate(65, srvMinCapacity = srvMinCapacity ? Math.min(srvMinCapacity, locSrv.min_capacity) : locSrv.min_capacity);
	                $$invalidate(64, srvMaxCapacity = srvMaxCapacity ? Math.max(srvMaxCapacity, locSrv.max_capacity) : locSrv.max_capacity);
	                $$invalidate(23, staffItems[id] = $__default['default'].extend({}, staffMember, {
	                  name: staffMember.name + (staffNameWithPrice && locSrv.price !== null && (lookupLocationId || !servicesPerLocation) ? " (" + locSrv.price + ")" : ""),
	                  hidden: collaborativeHideStaff && services[serviceId].type === "collaborative"
	                }), staffItems);

	                if (collaborativeHideStaff && services[serviceId].type === "collaborative") {
	                  $$invalidate(20, staffId = 0);
	                }
	              });
	            }
	          }
	        }); // Add ratings to staff names

	        if (showRatings) {
	          $__default['default'].each(staff, function (id, staffMember) {
	            if (staffMember.id in staffItems) {
	              if (serviceId) {
	                if (serviceId in staffMember.services && staffMember.services[serviceId].rating) {
	                  $$invalidate(23, staffItems[staffMember.id].name = "★" + staffMember.services[serviceId].rating + " " + staffItems[staffMember.id].name, staffItems);
	                }
	              } else if (staffMember.rating) {
	                $$invalidate(23, staffItems[staffMember.id].name = "★" + staffMember.rating + " " + staffItems[staffMember.id].name, staffItems);
	              }
	            }
	          });
	        } // Category & service


	        if (!locationId) {
	          $$invalidate(26, categoryItems = categories);
	          $__default['default'].each(services, function (id, service) {
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
	            $__default['default'].each(staff, function (stId) {
	              $__default['default'].each(staff[stId].services, function (srvId) {
	                if (lookupLocationId in staff[stId].services[srvId].locations) {
	                  categoryIds.push(services[srvId].category_id);
	                  serviceIds.push(srvId);
	                }
	              });
	            });
	          } else {
	            $__default['default'].each(locations[locationId].staff, function (stId) {
	              $__default['default'].each(staff[stId].services, function (srvId) {
	                categoryIds.push(services[srvId].category_id);
	                serviceIds.push(srvId);
	              });
	            });
	          }

	          $__default['default'].each(categories, function (id, category) {
	            if ($__default['default'].inArray(_parseInt(id), categoryIds) > -1) {
	              $$invalidate(26, categoryItems[id] = category, categoryItems);
	            }
	          });

	          if (categoryId && $__default['default'].inArray(categoryId, categoryIds) === -1) {
	            $$invalidate(18, categoryId = 0);
	            $$invalidate(61, categorySelected = false);
	          }

	          $__default['default'].each(services, function (id, service) {
	            if ($__default['default'].inArray(id, serviceIds) > -1) {
	              if (!categoryId || !categorySelected || service.category_id === categoryId) {
	                if (!staffId || id in staff[staffId].services) {
	                  $$invalidate(27, serviceItems[id] = service, serviceItems);
	                }
	              }
	            }
	          });
	        } // Number of persons


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
	        } // Duration


	        $$invalidate(24, durationItems = {
	          1: {
	            id: 1,
	            name: "-"
	          }
	        });

	        if (serviceId) {
	          if (!staffId || servicesPerLocation && !locationId) {
	            if ("units" in services[serviceId]) {
	              $$invalidate(24, durationItems = services[serviceId].units);
	            }
	          } else {
	            var locId = locationId || 0;
	            var staffLocations = staff[staffId].services[serviceId].locations;

	            if (staffLocations) {
	              var staffLocation = locId in staffLocations ? staffLocations[locId] : staffLocations[0];

	              if ("units" in staffLocation) {
	                $$invalidate(24, durationItems = staffLocation.units);
	              }
	            }
	          }
	        }

	        if (!(duration in durationItems)) {
	          if (keys(durationItems).length > 0) {
	            $$invalidate(21, duration = values(durationItems)[0].id);
	          } else {
	            $$invalidate(21, duration = 1);
	          }
	        } // Quantity


	        $$invalidate(29, quantityItems = {});

	        for (var q = 1; q <= maxQuantity; ++q) {
	          $$invalidate(29, quantityItems[q] = {
	            id: q,
	            name: q
	          }, quantityItems);
	        } // Placeholders


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
	    }, [-1, -1, -1]);
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

	function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

	function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !construct) return false; if (construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	function get_each_context(ctx, list, i) {
	  var child_ctx = slice$4(ctx).call(ctx);

	  child_ctx[9] = list[i];
	  child_ctx[10] = list;
	  child_ctx[11] = i;
	  return child_ctx;
	} // (30:0) {#each items as item, index (item)}


	function create_each_block(key_1, ctx) {
	  var first;
	  var chainitem;
	  var index =
	  /*index*/
	  ctx[11];
	  var current;
	  var chainitem_spread_levels = [
	  /*data*/
	  ctx[1], {
	    item:
	    /*item*/
	    ctx[9]
	  }, {
	    index:
	    /*index*/
	    ctx[11]
	  }, {
	    hasDropBtn:
	    /*multiple*/
	    ctx[2]
	  }, {
	    showDropBtn:
	    /*index*/
	    ctx[11] > 0
	  }];

	  var assign_chainitem = function assign_chainitem() {
	    return (
	      /*chainitem_binding*/
	      ctx[8](chainitem, index)
	    );
	  };

	  var unassign_chainitem = function unassign_chainitem() {
	    return (
	      /*chainitem_binding*/
	      ctx[8](null, index)
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
	  chainitem.$on("dropItem",
	  /*onDropItem*/
	  ctx[5]);
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

	      if (index !==
	      /*index*/
	      ctx[11]) {
	        unassign_chainitem();
	        index =
	        /*index*/
	        ctx[11];
	        assign_chainitem();
	      }

	      var chainitem_changes = dirty &
	      /*data, items, multiple*/
	      7 ? get_spread_update(chainitem_spread_levels, [dirty &
	      /*data*/
	      2 && get_spread_object(
	      /*data*/
	      ctx[1]), dirty &
	      /*items*/
	      1 && {
	        item:
	        /*item*/
	        ctx[9]
	      }, dirty &
	      /*items*/
	      1 && {
	        index:
	        /*index*/
	        ctx[11]
	      }, dirty &
	      /*multiple*/
	      4 && {
	        hasDropBtn:
	        /*multiple*/
	        ctx[2]
	      }, dirty &
	      /*items*/
	      1 && {
	        showDropBtn:
	        /*index*/
	        ctx[11] > 0
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
	} // (33:0) {#if multiple}


	function create_if_block(ctx) {
	  var div;
	  var button;
	  var span;
	  var t_value =
	  /*data*/
	  ctx[1].l10n.add_service + "";
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
	        dispose = listen(button, "click",
	        /*onAddItem*/
	        ctx[4]);
	        mounted = true;
	      }
	    },
	    p: function p(ctx, dirty) {
	      if (dirty &
	      /*data*/
	      2 && t_value !== (t_value =
	      /*data*/
	      ctx[1].l10n.add_service + "")) set_data(t, t_value);
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
	  var each_1_lookup = new map();
	  var t;
	  var if_block_anchor;
	  var current;
	  var each_value =
	  /*items*/
	  ctx[0];

	  var get_key = function get_key(ctx) {
	    return (
	      /*item*/
	      ctx[9]
	    );
	  };

	  for (var i = 0; i < each_value.length; i += 1) {
	    var child_ctx = get_each_context(ctx, each_value, i);
	    var key = get_key(child_ctx);
	    each_1_lookup.set(key, each_blocks[i] = create_each_block(key, child_ctx));
	  }

	  var if_block =
	  /*multiple*/
	  ctx[2] && create_if_block(ctx);
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

	      if (dirty &
	      /*data, items, multiple, els, onDropItem*/
	      47) {
	        each_value =
	        /*items*/
	        ctx[0];
	        group_outros();
	        each_blocks = update_keyed_each(each_blocks, dirty, get_key, 1, ctx, each_value, each_1_lookup, t.parentNode, outro_and_destroy_block, create_each_block, t, get_each_context);
	        check_outros();
	      }

	      if (
	      /*multiple*/
	      ctx[2]) {
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
	    splice(items).call(items, event.detail, 1);

	    $$invalidate(0, items);

	    splice(els).call(els, event.detail, 1);
	  }

	  function validate() {
	    var _context;

	    return map$3(_context = filter(els).call(els, function (el) {
	      return !!el;
	    })).call(_context, function (el) {
	      return el.validate();
	    });
	  }

	  function getValues() {
	    var _context2;

	    return map$3(_context2 = filter(els).call(els, function (el) {
	      return !!el;
	    })).call(_context2, function (el) {
	      return el.getValues();
	    });
	  }

	  function chainitem_binding($$value, index) {
	    binding_callbacks[$$value ? "unshift" : "push"](function () {
	      els[index] = $$value;
	      $$invalidate(3, els);
	      $$invalidate(0, items);
	    });
	  }

	  $$self.$$set = function ($$props) {
	    if ("items" in $$props) $$invalidate(0, items = $$props.items);
	    if ("data" in $$props) $$invalidate(1, data = $$props.data);
	    if ("multiple" in $$props) $$invalidate(2, multiple = $$props.multiple);
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

	  $__default['default'].extend(data, params);
	  booklyAjax({
	    data: data
	  }).then(function (response) {
	    BooklyL10n.csrf_token = response.csrf_token;
	    $container.html(response.html);
	    scrollTo($container, params.form_id);
	    var $chain = $__default['default']('.bookly-js-chain', $container),
	        $date_from = $__default['default']('.bookly-js-date-from', $container),
	        $week_days = $__default['default']('.bookly-js-week-days', $container),
	        $select_time_from = $__default['default']('.bookly-js-select-time-from', $container),
	        $select_time_to = $__default['default']('.bookly-js-select-time-to', $container),
	        $next_step = $__default['default']('.bookly-js-next-step', $container),
	        $mobile_next_step = $__default['default']('.bookly-js-mobile-next-step', $container),
	        $mobile_prev_step = $__default['default']('.bookly-js-mobile-prev-step', $container),
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
	        customJS = response.custom_js; // Set up selects.

	    if (serviceNameWithDuration) {
	      $__default['default'].each(services, function (id, service) {
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
	    }); // Init Pickadate.

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
	        if ($__default['default'].isNumeric(timestamp.select)) {
	          // Checks appropriate day of the week
	          var date = new Date(timestamp.select);
	          $__default['default']('.bookly-js-week-days input:checkbox[value="' + (date.getDay() + 1) + '"]:not(:checked)', $container).attr('checked', true).trigger('change');
	        }
	      },
	      onClose: function onClose() {
	        $date_from.data('updated', true); // Hide for skip tab navigations by days of the month when the calendar is closed

	        $__default['default']('#' + $date_from.attr('aria-owns')).hide();
	      }
	    }).focusin(function () {
	      // Restore calendar visibility, changed on onClose
	      $__default['default']('#' + $date_from.attr('aria-owns')).show();
	    });
	    $__default['default']('.bookly-js-go-to-cart', $container).on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();
	      laddaStart(this);
	      stepCart({
	        form_id: params.form_id,
	        from_step: 'service'
	      });
	    });

	    if (opt[params.form_id].form_attributes.hide_date) {
	      $__default['default']('.bookly-js-available-date', $container).hide();
	    }

	    if (opt[params.form_id].form_attributes.hide_week_days) {
	      $__default['default']('.bookly-js-week-days', $container).hide();
	    }

	    if (opt[params.form_id].form_attributes.hide_time_range) {
	      $__default['default']('.bookly-js-time-range', $container).hide();
	    } // time from


	    $select_time_from.on('change', function () {
	      var start_time = $__default['default'](this).val(),
	          end_time = $select_time_to.val(),
	          $last_time_entry = $__default['default']('option:last', $select_time_from);
	      $select_time_to.empty(); // case when we click on the not last time entry

	      if ($select_time_from[0].selectedIndex < $last_time_entry.index()) {
	        // clone and append all next "time_from" time entries to "time_to" list
	        $__default['default']('option', this).each(function () {
	          if ($__default['default'](this).val() > start_time) {
	            $select_time_to.append($__default['default'](this).clone());
	          }
	        }); // case when we click on the last time entry
	      } else {
	        $select_time_to.append($last_time_entry.clone()).val($last_time_entry.val());
	      }

	      var first_value = $__default['default']('option:first', $select_time_to).val();
	      $select_time_to.val(end_time >= first_value ? end_time : first_value);
	    });

	    var stepServiceValidator = function stepServiceValidator() {
	      var valid = true,
	          $scroll_to = null;
	      $__default['default'](c.validate()).each(function (_, status) {
	        if (!status.valid) {
	          valid = false;
	          var $el = $__default['default'](status.el);

	          if ($el.is(':visible')) {
	            $scroll_to = $el;
	            return false;
	          }
	        }
	      });
	      $date_from.removeClass('bookly-error'); // date validation

	      if (!$date_from.val()) {
	        valid = false;
	        $date_from.addClass('bookly-error');

	        if ($scroll_to === null) {
	          $scroll_to = $date_from;
	        }
	      } // week days


	      if ($week_days.length && !$__default['default'](':checked', $week_days).length) {
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
	    }; // "Next" click


	    $next_step.on('click', function (e) {
	      e.stopPropagation();
	      e.preventDefault();

	      if (stepServiceValidator()) {
	        laddaStart(this); // Execute custom JavaScript

	        if (customJS) {
	          try {
	            $__default['default'].globalEval(customJS.next_button);
	          } catch (e) {// Do nothing
	          }
	        } // Prepare chain data.


	        var _chain = [],
	            has_extras = 0,
	            time_requirements = 0,
	            recurrence_enabled = 1,
	            _time_requirements = {
	          'required': 2,
	          'optional': 1,
	          'off': 0
	        };
	        $__default['default'].each(c.getValues(), function (_, values) {
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
	        }); // Prepare days.

	        var days = [];
	        $__default['default']('.bookly-js-week-days input:checked', $container).each(function () {
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
	          $__default['default']('.bookly-js-mobile-step-1', $container).hide();
	          $__default['default']('.bookly-js-mobile-step-2', $container).css('display', 'block');
	          scrollTo($container, params.form_id);
	        }
	      }

	      return false;
	    });

	    if (opt[params.form_id].skip_steps.service_part1) {
	      // Skip scrolling
	      // Timeout to let form set default values
	      setTimeout$1(function () {
	        opt[params.form_id].scroll = false;
	        $mobile_next_step.trigger('click');
	      }, 0);

	      $mobile_prev_step.remove();
	    } else {
	      $mobile_prev_step.on('click', function (e) {
	        e.stopPropagation();
	        e.preventDefault();
	        $__default['default']('.bookly-js-mobile-step-1', $container).show();
	        $__default['default']('.bookly-js-mobile-step-2', $container).hide();
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
	  var $container = $__default['default']('#bookly-form-' + options.form_id);

	  if (!$container.length) {
	    return;
	  }

	  opt[options.form_id] = options;
	  opt[options.form_id].$container = $container;
	  opt[options.form_id].timeZone = (typeof Intl === "undefined" ? "undefined" : _typeof(Intl)) === 'object' ? Intl.DateTimeFormat().resolvedOptions().timeZone : undefined;
	  opt[options.form_id].timeZoneOffset = new Date().getTimezoneOffset();
	  opt[options.form_id].skip_steps.service = options.skip_steps.service_part1 && options.skip_steps.service_part2; // initialize

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
	  } // init google places


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
	            data: $__default['default'].extend(userInfo, {
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

}(jQuery));
