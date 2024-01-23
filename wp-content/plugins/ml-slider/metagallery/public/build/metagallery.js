;(function (factory) {
    typeof define === 'function' && define.amd ? define(factory) : factory()
})(function () {
    'use strict'

    var commonjsGlobal =
        typeof globalThis !== 'undefined'
            ? globalThis
            : typeof window !== 'undefined'
            ? window
            : typeof global !== 'undefined'
            ? global
            : typeof self !== 'undefined'
            ? self
            : {}

    function createCommonjsModule(fn) {
        var module = { exports: {} }
        return fn(module, module.exports), module.exports
    }

    createCommonjsModule(function (module, exports) {
        ;(function (global, factory) {
            module.exports = factory()
        })(commonjsGlobal, function () {
            var checkForAlpine = function checkForAlpine() {
                if (!window.Alpine) {
                    throw new Error('[Magic Helpers] Alpine is required for the magic helpers to function correctly.')
                }

                if (!window.Alpine.version || !isValidVersion('2.5.0', window.Alpine.version)) {
                    throw new Error('Invalid Alpine version. Please use Alpine version 2.5.0 or above')
                }
            }
            var syncWithObservedComponent = function syncWithObservedComponent(data, observedComponent, callback) {
                if (!observedComponent.getAttribute('x-bind:data-last-refresh')) {
                    observedComponent.setAttribute('x-bind:data-last-refresh', 'Date.now()')
                }

                var handler = function handler(scope) {
                    if (scope === void 0) {
                        scope = null
                    }

                    return {
                        get: function get(target, key) {
                            if (target[key] !== null && typeof target[key] === 'object') {
                                var path = scope ? scope + '.' + key : key
                                return new Proxy(target[key], handler(path))
                            }

                            return target[key]
                        },
                        set: function set(_target, key, value) {
                            if (!observedComponent.__x) {
                                throw new Error('Error communicating with observed component')
                            }

                            var path = scope ? scope + '.' + key : key
                            callback.call(observedComponent, observedComponent.__x.$data, path, value)
                            return true
                        },
                    }
                }

                return new Proxy(data, handler())
            }
            var updateOnMutation = function updateOnMutation(componentBeingObserved, callback) {
                if (!componentBeingObserved.getAttribute('x-bind:data-last-refresh')) {
                    componentBeingObserved.setAttribute('x-bind:data-last-refresh', 'Date.now()')
                }

                var observer = new MutationObserver(function (mutations) {
                    for (var i = 0; i < mutations.length; i++) {
                        var mutatedComponent = mutations[i].target.closest('[x-data]')
                        if (mutatedComponent && !mutatedComponent.isSameNode(componentBeingObserved)) continue
                        callback()
                        return
                    }
                })
                observer.observe(componentBeingObserved, {
                    attributes: true,
                    childList: true,
                    subtree: true,
                })
            } // Borrowed from https://stackoverflow.com/a/54733755/1437789

            var objectSetDeep = function objectSetDeep(object, path, value) {
                path = path.toString().match(/[^.[\]]+/g) || [] // Iterate all of them except the last one

                path.slice(0, -1).reduce(function (a, currentKey, index) {
                    // If the key does not exist or its value is not an object, create/override the key
                    if (Object(a[currentKey]) !== a[currentKey]) {
                        // Is the next key a potential array-index?
                        a[currentKey] =
                            Math.abs(path[index + 1]) >> 0 === +path[index + 1]
                                ? [] // Yes: assign a new array object
                                : {} // No: assign a new plain object
                    }

                    return a[currentKey]
                }, object)[path[path.length - 1]] = value // Finally assign the value to the last key

                return object
            } // Returns component data if Alpine has made it available, otherwise computes it with saferEval()

            var componentData = function componentData(component) {
                if (component.__x) {
                    return component.__x.getUnobservedData()
                }

                return saferEval(component.getAttribute('x-data'), component)
            }

            function isValidVersion(required, current) {
                var requiredArray = required.split('.')
                var currentArray = current.split('.')

                for (var i = 0; i < requiredArray.length; i++) {
                    if (!currentArray[i] || parseInt(currentArray[i]) < parseInt(requiredArray[i])) {
                        return false
                    }
                }

                return true
            }

            function saferEval(expression, dataContext, additionalHelperVariables) {
                if (additionalHelperVariables === void 0) {
                    additionalHelperVariables = {}
                }

                if (typeof expression === 'function') {
                    return expression.call(dataContext)
                } // eslint-disable-next-line no-new-func

                return new Function(
                    ['$data'].concat(Object.keys(additionalHelperVariables)),
                    'var __alpine_result; with($data) { __alpine_result = ' + expression + ' }; return __alpine_result',
                ).apply(void 0, [dataContext].concat(Object.values(additionalHelperVariables)))
            }

            var AlpineComponentMagicMethod = {
                start: function start() {
                    checkForAlpine()
                    Alpine.addMagicProperty('parent', function ($el) {
                        if (typeof $el.$parent !== 'undefined') return $el.$parent
                        var parentComponent = $el.parentNode.closest('[x-data]')
                        if (!parentComponent) throw new Error('Parent component not found')
                        $el.$parent = syncWithObservedComponent(
                            componentData(parentComponent),
                            parentComponent,
                            objectSetDeep,
                        )
                        updateOnMutation(parentComponent, function () {
                            $el.$parent = syncWithObservedComponent(
                                parentComponent.__x.getUnobservedData(),
                                parentComponent,
                                objectSetDeep,
                            )

                            $el.__x.updateElements($el)
                        })
                        return $el.$parent
                    })
                    Alpine.addMagicProperty('component', function ($el) {
                        return function (componentName) {
                            var _this = this

                            if (typeof this[componentName] !== 'undefined') return this[componentName]
                            var componentBeingObserved = document.querySelector(
                                '[x-data][x-id="' + componentName + '"], [x-data]#' + componentName,
                            )
                            if (!componentBeingObserved) throw new Error('Component not found')
                            this[componentName] = syncWithObservedComponent(
                                componentData(componentBeingObserved),
                                componentBeingObserved,
                                objectSetDeep,
                            )
                            updateOnMutation(componentBeingObserved, function () {
                                _this[componentName] = syncWithObservedComponent(
                                    componentBeingObserved.__x.getUnobservedData(),
                                    componentBeingObserved,
                                    objectSetDeep,
                                )

                                $el.__x.updateElements($el)
                            })
                            return this[componentName]
                        }
                    })
                },
            }

            var alpine =
                window.deferLoadingAlpine ||
                function (alpine) {
                    return alpine()
                }

            window.deferLoadingAlpine = function (callback) {
                alpine(callback)
                AlpineComponentMagicMethod.start()
            }

            return AlpineComponentMagicMethod
        })
    })

    const e = {
            start() {
                if (!window.Alpine) throw new Error('Alpine is required for `alpine-clipboard` to work.')
                Alpine.addMagicProperty(
                    'clipboard',
                    () =>
                        function (e) {
                            let t = e
                            if ('function' == typeof t) t = t()
                            else if ('string' != typeof t)
                                try {
                                    t = JSON.stringify(t)
                                } catch (e) {
                                    console.warn(e)
                                }
                            const n = document.createElement('textarea')
                            if (
                                ((n.value = t),
                                n.setAttribute('readonly', ''),
                                (n.style.cssText = 'position:fixed;pointer-events:none;z-index:-9999;opacity:0;'),
                                document.body.appendChild(n),
                                navigator.userAgent && navigator.userAgent.match(/ipad|ipod|iphone/i))
                            ) {
                                ;(n.contentEditable = !0), (n.readOnly = !0)
                                const e = document.createRange()
                                e.selectNodeContents(n)
                                const t = window.getSelection()
                                t.removeAllRanges(), t.addRange(e), n.setSelectionRange(0, 999999)
                            } else n.select()
                            try {
                                document.execCommand('copy')
                            } catch (e) {
                                console.warn(err)
                            }
                            document.body.removeChild(n)
                        },
                )
            },
        },
        t = window.deferLoadingAlpine || ((e) => e())
    window.deferLoadingAlpine = function (n) {
        e.start(), t(n)
    }

    createCommonjsModule(function (module, exports) {
        ;(function (global, factory) {
            module.exports = factory()
        })(commonjsGlobal, function () {
            function _defineProperty(obj, key, value) {
                if (key in obj) {
                    Object.defineProperty(obj, key, {
                        value: value,
                        enumerable: true,
                        configurable: true,
                        writable: true,
                    })
                } else {
                    obj[key] = value
                }

                return obj
            }

            function ownKeys(object, enumerableOnly) {
                var keys = Object.keys(object)

                if (Object.getOwnPropertySymbols) {
                    var symbols = Object.getOwnPropertySymbols(object)
                    if (enumerableOnly)
                        symbols = symbols.filter(function (sym) {
                            return Object.getOwnPropertyDescriptor(object, sym).enumerable
                        })
                    keys.push.apply(keys, symbols)
                }

                return keys
            }

            function _objectSpread2(target) {
                for (var i = 1; i < arguments.length; i++) {
                    var source = arguments[i] != null ? arguments[i] : {}

                    if (i % 2) {
                        ownKeys(Object(source), true).forEach(function (key) {
                            _defineProperty(target, key, source[key])
                        })
                    } else if (Object.getOwnPropertyDescriptors) {
                        Object.defineProperties(target, Object.getOwnPropertyDescriptors(source))
                    } else {
                        ownKeys(Object(source)).forEach(function (key) {
                            Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key))
                        })
                    }
                }

                return target
            }

            // Thanks @stimulus:
            // https://github.com/stimulusjs/stimulus/blob/master/packages/%40stimulus/core/src/application.ts
            function domReady() {
                return new Promise((resolve) => {
                    if (document.readyState == 'loading') {
                        document.addEventListener('DOMContentLoaded', resolve)
                    } else {
                        resolve()
                    }
                })
            }
            function arrayUnique(array) {
                return Array.from(new Set(array))
            }
            function isTesting() {
                return navigator.userAgent.includes('Node.js') || navigator.userAgent.includes('jsdom')
            }
            function checkedAttrLooseCompare(valueA, valueB) {
                return valueA == valueB
            }
            function warnIfMalformedTemplate(el, directive) {
                if (el.tagName.toLowerCase() !== 'template') {
                    console.warn(
                        `Alpine: [${directive}] directive should only be added to <template> tags. See https://github.com/alpinejs/alpine#${directive}`,
                    )
                } else if (el.content.childElementCount !== 1) {
                    console.warn(
                        `Alpine: <template> tag with [${directive}] encountered with an unexpected number of root elements. Make sure <template> has a single root element. `,
                    )
                }
            }
            function kebabCase(subject) {
                return subject
                    .replace(/([a-z])([A-Z])/g, '$1-$2')
                    .replace(/[_\s]/, '-')
                    .toLowerCase()
            }
            function camelCase(subject) {
                return subject.toLowerCase().replace(/-(\w)/g, (match, char) => char.toUpperCase())
            }
            function walk(el, callback) {
                if (callback(el) === false) return
                let node = el.firstElementChild

                while (node) {
                    walk(node, callback)
                    node = node.nextElementSibling
                }
            }
            function debounce(func, wait) {
                var timeout
                return function () {
                    var context = this,
                        args = arguments

                    var later = function later() {
                        timeout = null
                        func.apply(context, args)
                    }

                    clearTimeout(timeout)
                    timeout = setTimeout(later, wait)
                }
            }

            const handleError = (el, expression, error) => {
                console.warn(`Alpine Error: "${error}"\n\nExpression: "${expression}"\nElement:`, el)

                if (!isTesting()) {
                    Object.assign(error, {
                        el,
                        expression,
                    })
                    throw error
                }
            }

            function tryCatch(cb, { el, expression }) {
                try {
                    const value = cb()
                    return value instanceof Promise ? value.catch((e) => handleError(el, expression, e)) : value
                } catch (e) {
                    handleError(el, expression, e)
                }
            }

            function saferEval(el, expression, dataContext, additionalHelperVariables = {}) {
                return tryCatch(
                    () => {
                        if (typeof expression === 'function') {
                            return expression.call(dataContext)
                        }

                        return new Function(
                            ['$data', ...Object.keys(additionalHelperVariables)],
                            `var __alpine_result; with($data) { __alpine_result = ${expression} }; return __alpine_result`,
                        )(dataContext, ...Object.values(additionalHelperVariables))
                    },
                    {
                        el,
                        expression,
                    },
                )
            }
            function saferEvalNoReturn(el, expression, dataContext, additionalHelperVariables = {}) {
                return tryCatch(
                    () => {
                        if (typeof expression === 'function') {
                            return Promise.resolve(expression.call(dataContext, additionalHelperVariables['$event']))
                        }

                        let AsyncFunction = Function
                        /* MODERN-ONLY:START */

                        AsyncFunction = Object.getPrototypeOf(async function () {}).constructor
                        /* MODERN-ONLY:END */
                        // For the cases when users pass only a function reference to the caller: `x-on:click="foo"`
                        // Where "foo" is a function. Also, we'll pass the function the event instance when we call it.

                        if (Object.keys(dataContext).includes(expression)) {
                            let methodReference = new Function(
                                ['dataContext', ...Object.keys(additionalHelperVariables)],
                                `with(dataContext) { return ${expression} }`,
                            )(dataContext, ...Object.values(additionalHelperVariables))

                            if (typeof methodReference === 'function') {
                                return Promise.resolve(
                                    methodReference.call(dataContext, additionalHelperVariables['$event']),
                                )
                            } else {
                                return Promise.resolve()
                            }
                        }

                        return Promise.resolve(
                            new AsyncFunction(
                                ['dataContext', ...Object.keys(additionalHelperVariables)],
                                `with(dataContext) { ${expression} }`,
                            )(dataContext, ...Object.values(additionalHelperVariables)),
                        )
                    },
                    {
                        el,
                        expression,
                    },
                )
            }
            const xAttrRE = /^x-(on|bind|data|text|html|model|if|for|show|cloak|transition|ref|spread)\b/
            function isXAttr(attr) {
                const name = replaceAtAndColonWithStandardSyntax(attr.name)
                return xAttrRE.test(name)
            }
            function getXAttrs(el, component, type) {
                let directives = Array.from(el.attributes).filter(isXAttr).map(parseHtmlAttribute) // Get an object of directives from x-spread.

                let spreadDirective = directives.filter((directive) => directive.type === 'spread')[0]

                if (spreadDirective) {
                    let spreadObject = saferEval(el, spreadDirective.expression, component.$data) // Add x-spread directives to the pile of existing directives.

                    directives = directives.concat(
                        Object.entries(spreadObject).map(([name, value]) =>
                            parseHtmlAttribute({
                                name,
                                value,
                            }),
                        ),
                    )
                }

                if (type) return directives.filter((i) => i.type === type)
                return sortDirectives(directives)
            }

            function sortDirectives(directives) {
                let directiveOrder = ['bind', 'model', 'show', 'catch-all']
                return directives.sort((a, b) => {
                    let typeA = directiveOrder.indexOf(a.type) === -1 ? 'catch-all' : a.type
                    let typeB = directiveOrder.indexOf(b.type) === -1 ? 'catch-all' : b.type
                    return directiveOrder.indexOf(typeA) - directiveOrder.indexOf(typeB)
                })
            }

            function parseHtmlAttribute({ name, value }) {
                const normalizedName = replaceAtAndColonWithStandardSyntax(name)
                const typeMatch = normalizedName.match(xAttrRE)
                const valueMatch = normalizedName.match(/:([a-zA-Z0-9\-:]+)/)
                const modifiers = normalizedName.match(/\.[^.\]]+(?=[^\]]*$)/g) || []
                return {
                    type: typeMatch ? typeMatch[1] : null,
                    value: valueMatch ? valueMatch[1] : null,
                    modifiers: modifiers.map((i) => i.replace('.', '')),
                    expression: value,
                }
            }
            function isBooleanAttr(attrName) {
                // As per HTML spec table https://html.spec.whatwg.org/multipage/indices.html#attributes-3:boolean-attribute
                // Array roughly ordered by estimated usage
                const booleanAttributes = [
                    'disabled',
                    'checked',
                    'required',
                    'readonly',
                    'hidden',
                    'open',
                    'selected',
                    'autofocus',
                    'itemscope',
                    'multiple',
                    'novalidate',
                    'allowfullscreen',
                    'allowpaymentrequest',
                    'formnovalidate',
                    'autoplay',
                    'controls',
                    'loop',
                    'muted',
                    'playsinline',
                    'default',
                    'ismap',
                    'reversed',
                    'async',
                    'defer',
                    'nomodule',
                ]
                return booleanAttributes.includes(attrName)
            }
            function replaceAtAndColonWithStandardSyntax(name) {
                if (name.startsWith('@')) {
                    return name.replace('@', 'x-on:')
                } else if (name.startsWith(':')) {
                    return name.replace(':', 'x-bind:')
                }

                return name
            }
            function convertClassStringToArray(classList, filterFn = Boolean) {
                return classList.split(' ').filter(filterFn)
            }
            const TRANSITION_TYPE_IN = 'in'
            const TRANSITION_TYPE_OUT = 'out'
            const TRANSITION_CANCELLED = 'cancelled'
            function transitionIn(el, show, reject, component, forceSkip = false) {
                // We don't want to transition on the initial page load.
                if (forceSkip) return show()

                if (el.__x_transition && el.__x_transition.type === TRANSITION_TYPE_IN) {
                    // there is already a similar transition going on, this was probably triggered by
                    // a change in a different property, let's just leave the previous one doing its job
                    return
                }

                const attrs = getXAttrs(el, component, 'transition')
                const showAttr = getXAttrs(el, component, 'show')[0] // If this is triggered by a x-show.transition.

                if (showAttr && showAttr.modifiers.includes('transition')) {
                    let modifiers = showAttr.modifiers // If x-show.transition.out, we'll skip the "in" transition.

                    if (modifiers.includes('out') && !modifiers.includes('in')) return show()
                    const settingBothSidesOfTransition = modifiers.includes('in') && modifiers.includes('out') // If x-show.transition.in...out... only use "in" related modifiers for this transition.

                    modifiers = settingBothSidesOfTransition
                        ? modifiers.filter((i, index) => index < modifiers.indexOf('out'))
                        : modifiers
                    transitionHelperIn(el, modifiers, show, reject) // Otherwise, we can assume x-transition:enter.
                } else if (attrs.some((attr) => ['enter', 'enter-start', 'enter-end'].includes(attr.value))) {
                    transitionClassesIn(el, component, attrs, show, reject)
                } else {
                    // If neither, just show that damn thing.
                    show()
                }
            }
            function transitionOut(el, hide, reject, component, forceSkip = false) {
                // We don't want to transition on the initial page load.
                if (forceSkip) return hide()

                if (el.__x_transition && el.__x_transition.type === TRANSITION_TYPE_OUT) {
                    // there is already a similar transition going on, this was probably triggered by
                    // a change in a different property, let's just leave the previous one doing its job
                    return
                }

                const attrs = getXAttrs(el, component, 'transition')
                const showAttr = getXAttrs(el, component, 'show')[0]

                if (showAttr && showAttr.modifiers.includes('transition')) {
                    let modifiers = showAttr.modifiers
                    if (modifiers.includes('in') && !modifiers.includes('out')) return hide()
                    const settingBothSidesOfTransition = modifiers.includes('in') && modifiers.includes('out')
                    modifiers = settingBothSidesOfTransition
                        ? modifiers.filter((i, index) => index > modifiers.indexOf('out'))
                        : modifiers
                    transitionHelperOut(el, modifiers, settingBothSidesOfTransition, hide, reject)
                } else if (attrs.some((attr) => ['leave', 'leave-start', 'leave-end'].includes(attr.value))) {
                    transitionClassesOut(el, component, attrs, hide, reject)
                } else {
                    hide()
                }
            }
            function transitionHelperIn(el, modifiers, showCallback, reject) {
                // Default values inspired by: https://material.io/design/motion/speed.html#duration
                const styleValues = {
                    duration: modifierValue(modifiers, 'duration', 150),
                    origin: modifierValue(modifiers, 'origin', 'center'),
                    first: {
                        opacity: 0,
                        scale: modifierValue(modifiers, 'scale', 95),
                    },
                    second: {
                        opacity: 1,
                        scale: 100,
                    },
                }
                transitionHelper(el, modifiers, showCallback, () => {}, reject, styleValues, TRANSITION_TYPE_IN)
            }
            function transitionHelperOut(el, modifiers, settingBothSidesOfTransition, hideCallback, reject) {
                // Make the "out" transition .5x slower than the "in". (Visually better)
                // HOWEVER, if they explicitly set a duration for the "out" transition,
                // use that.
                const duration = settingBothSidesOfTransition
                    ? modifierValue(modifiers, 'duration', 150)
                    : modifierValue(modifiers, 'duration', 150) / 2
                const styleValues = {
                    duration: duration,
                    origin: modifierValue(modifiers, 'origin', 'center'),
                    first: {
                        opacity: 1,
                        scale: 100,
                    },
                    second: {
                        opacity: 0,
                        scale: modifierValue(modifiers, 'scale', 95),
                    },
                }
                transitionHelper(el, modifiers, () => {}, hideCallback, reject, styleValues, TRANSITION_TYPE_OUT)
            }

            function modifierValue(modifiers, key, fallback) {
                // If the modifier isn't present, use the default.
                if (modifiers.indexOf(key) === -1) return fallback // If it IS present, grab the value after it: x-show.transition.duration.500ms

                const rawValue = modifiers[modifiers.indexOf(key) + 1]
                if (!rawValue) return fallback

                if (key === 'scale') {
                    // Check if the very next value is NOT a number and return the fallback.
                    // If x-show.transition.scale, we'll use the default scale value.
                    // That is how a user opts out of the opacity transition.
                    if (!isNumeric(rawValue)) return fallback
                }

                if (key === 'duration') {
                    // Support x-show.transition.duration.500ms && duration.500
                    let match = rawValue.match(/([0-9]+)ms/)
                    if (match) return match[1]
                }

                if (key === 'origin') {
                    // Support chaining origin directions: x-show.transition.top.right
                    if (['top', 'right', 'left', 'center', 'bottom'].includes(modifiers[modifiers.indexOf(key) + 2])) {
                        return [rawValue, modifiers[modifiers.indexOf(key) + 2]].join(' ')
                    }
                }

                return rawValue
            }

            function transitionHelper(el, modifiers, hook1, hook2, reject, styleValues, type) {
                // clear the previous transition if exists to avoid caching the wrong styles
                if (el.__x_transition) {
                    el.__x_transition.cancel && el.__x_transition.cancel()
                } // If the user set these style values, we'll put them back when we're done with them.

                const opacityCache = el.style.opacity
                const transformCache = el.style.transform
                const transformOriginCache = el.style.transformOrigin // If no modifiers are present: x-show.transition, we'll default to both opacity and scale.

                const noModifiers = !modifiers.includes('opacity') && !modifiers.includes('scale')
                const transitionOpacity = noModifiers || modifiers.includes('opacity')
                const transitionScale = noModifiers || modifiers.includes('scale') // These are the explicit stages of a transition (same stages for in and for out).
                // This way you can get a birds eye view of the hooks, and the differences
                // between them.

                const stages = {
                    start() {
                        if (transitionOpacity) el.style.opacity = styleValues.first.opacity
                        if (transitionScale) el.style.transform = `scale(${styleValues.first.scale / 100})`
                    },

                    during() {
                        if (transitionScale) el.style.transformOrigin = styleValues.origin
                        el.style.transitionProperty = [
                            transitionOpacity ? `opacity` : ``,
                            transitionScale ? `transform` : ``,
                        ]
                            .join(' ')
                            .trim()
                        el.style.transitionDuration = `${styleValues.duration / 1000}s`
                        el.style.transitionTimingFunction = `cubic-bezier(0.4, 0.0, 0.2, 1)`
                    },

                    show() {
                        hook1()
                    },

                    end() {
                        if (transitionOpacity) el.style.opacity = styleValues.second.opacity
                        if (transitionScale) el.style.transform = `scale(${styleValues.second.scale / 100})`
                    },

                    hide() {
                        hook2()
                    },

                    cleanup() {
                        if (transitionOpacity) el.style.opacity = opacityCache
                        if (transitionScale) el.style.transform = transformCache
                        if (transitionScale) el.style.transformOrigin = transformOriginCache
                        el.style.transitionProperty = null
                        el.style.transitionDuration = null
                        el.style.transitionTimingFunction = null
                    },
                }
                transition(el, stages, type, reject)
            }

            const ensureStringExpression = (expression, el, component) => {
                return typeof expression === 'function'
                    ? component.evaluateReturnExpression(el, expression)
                    : expression
            }

            function transitionClassesIn(el, component, directives, showCallback, reject) {
                const enter = convertClassStringToArray(
                    ensureStringExpression(
                        (
                            directives.find((i) => i.value === 'enter') || {
                                expression: '',
                            }
                        ).expression,
                        el,
                        component,
                    ),
                )
                const enterStart = convertClassStringToArray(
                    ensureStringExpression(
                        (
                            directives.find((i) => i.value === 'enter-start') || {
                                expression: '',
                            }
                        ).expression,
                        el,
                        component,
                    ),
                )
                const enterEnd = convertClassStringToArray(
                    ensureStringExpression(
                        (
                            directives.find((i) => i.value === 'enter-end') || {
                                expression: '',
                            }
                        ).expression,
                        el,
                        component,
                    ),
                )
                transitionClasses(el, enter, enterStart, enterEnd, showCallback, () => {}, TRANSITION_TYPE_IN, reject)
            }
            function transitionClassesOut(el, component, directives, hideCallback, reject) {
                const leave = convertClassStringToArray(
                    ensureStringExpression(
                        (
                            directives.find((i) => i.value === 'leave') || {
                                expression: '',
                            }
                        ).expression,
                        el,
                        component,
                    ),
                )
                const leaveStart = convertClassStringToArray(
                    ensureStringExpression(
                        (
                            directives.find((i) => i.value === 'leave-start') || {
                                expression: '',
                            }
                        ).expression,
                        el,
                        component,
                    ),
                )
                const leaveEnd = convertClassStringToArray(
                    ensureStringExpression(
                        (
                            directives.find((i) => i.value === 'leave-end') || {
                                expression: '',
                            }
                        ).expression,
                        el,
                        component,
                    ),
                )
                transitionClasses(el, leave, leaveStart, leaveEnd, () => {}, hideCallback, TRANSITION_TYPE_OUT, reject)
            }
            function transitionClasses(el, classesDuring, classesStart, classesEnd, hook1, hook2, type, reject) {
                // clear the previous transition if exists to avoid caching the wrong classes
                if (el.__x_transition) {
                    el.__x_transition.cancel && el.__x_transition.cancel()
                }

                const originalClasses = el.__x_original_classes || []
                const stages = {
                    start() {
                        el.classList.add(...classesStart)
                    },

                    during() {
                        el.classList.add(...classesDuring)
                    },

                    show() {
                        hook1()
                    },

                    end() {
                        // Don't remove classes that were in the original class attribute.
                        el.classList.remove(...classesStart.filter((i) => !originalClasses.includes(i)))
                        el.classList.add(...classesEnd)
                    },

                    hide() {
                        hook2()
                    },

                    cleanup() {
                        el.classList.remove(...classesDuring.filter((i) => !originalClasses.includes(i)))
                        el.classList.remove(...classesEnd.filter((i) => !originalClasses.includes(i)))
                    },
                }
                transition(el, stages, type, reject)
            }
            function transition(el, stages, type, reject) {
                const finish = once(() => {
                    stages.hide() // Adding an "isConnected" check, in case the callback
                    // removed the element from the DOM.

                    if (el.isConnected) {
                        stages.cleanup()
                    }

                    delete el.__x_transition
                })
                el.__x_transition = {
                    // Set transition type so we can avoid clearing transition if the direction is the same
                    type: type,
                    // create a callback for the last stages of the transition so we can call it
                    // from different point and early terminate it. Once will ensure that function
                    // is only called one time.
                    cancel: once(() => {
                        reject(TRANSITION_CANCELLED)
                        finish()
                    }),
                    finish,
                    // This store the next animation frame so we can cancel it
                    nextFrame: null,
                }
                stages.start()
                stages.during()
                el.__x_transition.nextFrame = requestAnimationFrame(() => {
                    // Note: Safari's transitionDuration property will list out comma separated transition durations
                    // for every single transition property. Let's grab the first one and call it a day.
                    let duration =
                        Number(getComputedStyle(el).transitionDuration.replace(/,.*/, '').replace('s', '')) * 1000

                    if (duration === 0) {
                        duration = Number(getComputedStyle(el).animationDuration.replace('s', '')) * 1000
                    }

                    stages.show()
                    el.__x_transition.nextFrame = requestAnimationFrame(() => {
                        stages.end()
                        setTimeout(el.__x_transition.finish, duration)
                    })
                })
            }
            function isNumeric(subject) {
                return !Array.isArray(subject) && !isNaN(subject)
            } // Thanks @vuejs
            // https://github.com/vuejs/vue/blob/4de4649d9637262a9b007720b59f80ac72a5620c/src/shared/util.js

            function once(callback) {
                let called = false
                return function () {
                    if (!called) {
                        called = true
                        callback.apply(this, arguments)
                    }
                }
            }

            function handleForDirective(component, templateEl, expression, initialUpdate, extraVars) {
                warnIfMalformedTemplate(templateEl, 'x-for')
                let iteratorNames =
                    typeof expression === 'function'
                        ? parseForExpression(component.evaluateReturnExpression(templateEl, expression))
                        : parseForExpression(expression)
                let items = evaluateItemsAndReturnEmptyIfXIfIsPresentAndFalseOnElement(
                    component,
                    templateEl,
                    iteratorNames,
                    extraVars,
                ) // As we walk the array, we'll also walk the DOM (updating/creating as we go).

                let currentEl = templateEl
                items.forEach((item, index) => {
                    let iterationScopeVariables = getIterationScopeVariables(
                        iteratorNames,
                        item,
                        index,
                        items,
                        extraVars(),
                    )
                    let currentKey = generateKeyForIteration(component, templateEl, index, iterationScopeVariables)
                    let nextEl = lookAheadForMatchingKeyedElementAndMoveItIfFound(
                        currentEl.nextElementSibling,
                        currentKey,
                    ) // If we haven't found a matching key, insert the element at the current position.

                    if (!nextEl) {
                        nextEl = addElementInLoopAfterCurrentEl(templateEl, currentEl) // And transition it in if it's not the first page load.

                        transitionIn(
                            nextEl,
                            () => {},
                            () => {},
                            component,
                            initialUpdate,
                        )
                        nextEl.__x_for = iterationScopeVariables
                        component.initializeElements(nextEl, () => nextEl.__x_for) // Otherwise update the element we found.
                    } else {
                        // Temporarily remove the key indicator to allow the normal "updateElements" to work.
                        delete nextEl.__x_for_key
                        nextEl.__x_for = iterationScopeVariables
                        component.updateElements(nextEl, () => nextEl.__x_for)
                    }

                    currentEl = nextEl
                    currentEl.__x_for_key = currentKey
                })
                removeAnyLeftOverElementsFromPreviousUpdate(currentEl, component)
            } // This was taken from VueJS 2.* core. Thanks Vue!

            function parseForExpression(expression) {
                let forIteratorRE = /,([^,\}\]]*)(?:,([^,\}\]]*))?$/
                let stripParensRE = /^\(|\)$/g
                let forAliasRE = /([\s\S]*?)\s+(?:in|of)\s+([\s\S]*)/
                let inMatch = String(expression).match(forAliasRE)
                if (!inMatch) return
                let res = {}
                res.items = inMatch[2].trim()
                let item = inMatch[1].trim().replace(stripParensRE, '')
                let iteratorMatch = item.match(forIteratorRE)

                if (iteratorMatch) {
                    res.item = item.replace(forIteratorRE, '').trim()
                    res.index = iteratorMatch[1].trim()

                    if (iteratorMatch[2]) {
                        res.collection = iteratorMatch[2].trim()
                    }
                } else {
                    res.item = item
                }

                return res
            }

            function getIterationScopeVariables(iteratorNames, item, index, items, extraVars) {
                // We must create a new object, so each iteration has a new scope
                let scopeVariables = extraVars ? _objectSpread2({}, extraVars) : {}
                scopeVariables[iteratorNames.item] = item
                if (iteratorNames.index) scopeVariables[iteratorNames.index] = index
                if (iteratorNames.collection) scopeVariables[iteratorNames.collection] = items
                return scopeVariables
            }

            function generateKeyForIteration(component, el, index, iterationScopeVariables) {
                let bindKeyAttribute = getXAttrs(el, component, 'bind').filter((attr) => attr.value === 'key')[0] // If the dev hasn't specified a key, just return the index of the iteration.

                if (!bindKeyAttribute) return index
                return component.evaluateReturnExpression(
                    el,
                    bindKeyAttribute.expression,
                    () => iterationScopeVariables,
                )
            }

            function evaluateItemsAndReturnEmptyIfXIfIsPresentAndFalseOnElement(
                component,
                el,
                iteratorNames,
                extraVars,
            ) {
                let ifAttribute = getXAttrs(el, component, 'if')[0]

                if (ifAttribute && !component.evaluateReturnExpression(el, ifAttribute.expression)) {
                    return []
                }

                let items = component.evaluateReturnExpression(el, iteratorNames.items, extraVars) // This adds support for the `i in n` syntax.

                if (isNumeric(items) && items >= 0) {
                    items = Array.from(Array(items).keys(), (i) => i + 1)
                }

                return items
            }

            function addElementInLoopAfterCurrentEl(templateEl, currentEl) {
                let clone = document.importNode(templateEl.content, true)
                currentEl.parentElement.insertBefore(clone, currentEl.nextElementSibling)
                return currentEl.nextElementSibling
            }

            function lookAheadForMatchingKeyedElementAndMoveItIfFound(nextEl, currentKey) {
                if (!nextEl) return // If we are already past the x-for generated elements, we don't need to look ahead.

                if (nextEl.__x_for_key === undefined) return // If the the key's DO match, no need to look ahead.

                if (nextEl.__x_for_key === currentKey) return nextEl // If they don't, we'll look ahead for a match.
                // If we find it, we'll move it to the current position in the loop.

                let tmpNextEl = nextEl

                while (tmpNextEl) {
                    if (tmpNextEl.__x_for_key === currentKey) {
                        return tmpNextEl.parentElement.insertBefore(tmpNextEl, nextEl)
                    }

                    tmpNextEl =
                        tmpNextEl.nextElementSibling && tmpNextEl.nextElementSibling.__x_for_key !== undefined
                            ? tmpNextEl.nextElementSibling
                            : false
                }
            }

            function removeAnyLeftOverElementsFromPreviousUpdate(currentEl, component) {
                var nextElementFromOldLoop =
                    currentEl.nextElementSibling && currentEl.nextElementSibling.__x_for_key !== undefined
                        ? currentEl.nextElementSibling
                        : false

                while (nextElementFromOldLoop) {
                    let nextElementFromOldLoopImmutable = nextElementFromOldLoop
                    let nextSibling = nextElementFromOldLoop.nextElementSibling
                    transitionOut(
                        nextElementFromOldLoop,
                        () => {
                            nextElementFromOldLoopImmutable.remove()
                        },
                        () => {},
                        component,
                    )
                    nextElementFromOldLoop = nextSibling && nextSibling.__x_for_key !== undefined ? nextSibling : false
                }
            }

            function handleAttributeBindingDirective(
                component,
                el,
                attrName,
                expression,
                extraVars,
                attrType,
                modifiers,
            ) {
                var value = component.evaluateReturnExpression(el, expression, extraVars)

                if (attrName === 'value') {
                    if (Alpine.ignoreFocusedForValueBinding && document.activeElement.isSameNode(el)) return // If nested model key is undefined, set the default value to empty string.

                    if (value === undefined && String(expression).match(/\./)) {
                        value = ''
                    }

                    if (el.type === 'radio') {
                        // Set radio value from x-bind:value, if no "value" attribute exists.
                        // If there are any initial state values, radio will have a correct
                        // "checked" value since x-bind:value is processed before x-model.
                        if (el.attributes.value === undefined && attrType === 'bind') {
                            el.value = value
                        } else if (attrType !== 'bind') {
                            el.checked = checkedAttrLooseCompare(el.value, value)
                        }
                    } else if (el.type === 'checkbox') {
                        // If we are explicitly binding a string to the :value, set the string,
                        // If the value is a boolean, leave it alone, it will be set to "on"
                        // automatically.
                        if (typeof value !== 'boolean' && ![null, undefined].includes(value) && attrType === 'bind') {
                            el.value = String(value)
                        } else if (attrType !== 'bind') {
                            if (Array.isArray(value)) {
                                // I'm purposely not using Array.includes here because it's
                                // strict, and because of Numeric/String mis-casting, I
                                // want the "includes" to be "fuzzy".
                                el.checked = value.some((val) => checkedAttrLooseCompare(val, el.value))
                            } else {
                                el.checked = !!value
                            }
                        }
                    } else if (el.tagName === 'SELECT') {
                        updateSelect(el, value)
                    } else {
                        if (el.value === value) return
                        el.value = value
                    }
                } else if (attrName === 'class') {
                    if (Array.isArray(value)) {
                        const originalClasses = el.__x_original_classes || []
                        el.setAttribute('class', arrayUnique(originalClasses.concat(value)).join(' '))
                    } else if (typeof value === 'object') {
                        // Sorting the keys / class names by their boolean value will ensure that
                        // anything that evaluates to `false` and needs to remove classes is run first.
                        const keysSortedByBooleanValue = Object.keys(value).sort((a, b) => value[a] - value[b])
                        keysSortedByBooleanValue.forEach((classNames) => {
                            if (value[classNames]) {
                                convertClassStringToArray(classNames).forEach((className) =>
                                    el.classList.add(className),
                                )
                            } else {
                                convertClassStringToArray(classNames).forEach((className) =>
                                    el.classList.remove(className),
                                )
                            }
                        })
                    } else {
                        const originalClasses = el.__x_original_classes || []
                        const newClasses = value ? convertClassStringToArray(value) : []
                        el.setAttribute('class', arrayUnique(originalClasses.concat(newClasses)).join(' '))
                    }
                } else {
                    attrName = modifiers.includes('camel') ? camelCase(attrName) : attrName // If an attribute's bound value is null, undefined or false, remove the attribute

                    if ([null, undefined, false].includes(value)) {
                        el.removeAttribute(attrName)
                    } else {
                        isBooleanAttr(attrName)
                            ? setIfChanged(el, attrName, attrName)
                            : setIfChanged(el, attrName, value)
                    }
                }
            }

            function setIfChanged(el, attrName, value) {
                if (el.getAttribute(attrName) != value) {
                    el.setAttribute(attrName, value)
                }
            }

            function updateSelect(el, value) {
                const arrayWrappedValue = [].concat(value).map((value) => {
                    return value + ''
                })
                Array.from(el.options).forEach((option) => {
                    option.selected = arrayWrappedValue.includes(option.value || option.text)
                })
            }

            function handleTextDirective(el, output, expression) {
                // If nested model key is undefined, set the default value to empty string.
                if (output === undefined && String(expression).match(/\./)) {
                    output = ''
                }

                el.textContent = output
            }

            function handleHtmlDirective(component, el, expression, extraVars) {
                el.innerHTML = component.evaluateReturnExpression(el, expression, extraVars)
            }

            function handleShowDirective(component, el, value, modifiers, initialUpdate = false) {
                const hide = () => {
                    el.style.display = 'none'
                    el.__x_is_shown = false
                }

                const show = () => {
                    if (el.style.length === 1 && el.style.display === 'none') {
                        el.removeAttribute('style')
                    } else {
                        el.style.removeProperty('display')
                    }

                    el.__x_is_shown = true
                }

                if (initialUpdate === true) {
                    if (value) {
                        show()
                    } else {
                        hide()
                    }

                    return
                }

                const handle = (resolve, reject) => {
                    if (value) {
                        if (el.style.display === 'none' || el.__x_transition) {
                            transitionIn(
                                el,
                                () => {
                                    show()
                                },
                                reject,
                                component,
                            )
                        }

                        resolve(() => {})
                    } else {
                        if (el.style.display !== 'none') {
                            transitionOut(
                                el,
                                () => {
                                    resolve(() => {
                                        hide()
                                    })
                                },
                                reject,
                                component,
                            )
                        } else {
                            resolve(() => {})
                        }
                    }
                } // The working of x-show is a bit complex because we need to
                // wait for any child transitions to finish before hiding
                // some element. Also, this has to be done recursively.
                // If x-show.immediate, foregoe the waiting.

                if (modifiers.includes('immediate')) {
                    handle(
                        (finish) => finish(),
                        () => {},
                    )
                    return
                } // x-show is encountered during a DOM tree walk. If an element
                // we encounter is NOT a child of another x-show element we
                // can execute the previous x-show stack (if one exists).

                if (component.showDirectiveLastElement && !component.showDirectiveLastElement.contains(el)) {
                    component.executeAndClearRemainingShowDirectiveStack()
                }

                component.showDirectiveStack.push(handle)
                component.showDirectiveLastElement = el
            }

            function handleIfDirective(component, el, expressionResult, initialUpdate, extraVars) {
                warnIfMalformedTemplate(el, 'x-if')
                const elementHasAlreadyBeenAdded =
                    el.nextElementSibling && el.nextElementSibling.__x_inserted_me === true

                if (expressionResult && (!elementHasAlreadyBeenAdded || el.__x_transition)) {
                    const clone = document.importNode(el.content, true)
                    el.parentElement.insertBefore(clone, el.nextElementSibling)
                    transitionIn(
                        el.nextElementSibling,
                        () => {},
                        () => {},
                        component,
                        initialUpdate,
                    )
                    component.initializeElements(el.nextElementSibling, extraVars)
                    el.nextElementSibling.__x_inserted_me = true
                } else if (!expressionResult && elementHasAlreadyBeenAdded) {
                    transitionOut(
                        el.nextElementSibling,
                        () => {
                            el.nextElementSibling.remove()
                        },
                        () => {},
                        component,
                        initialUpdate,
                    )
                }
            }

            function registerListener(component, el, event, modifiers, expression, extraVars = {}) {
                const options = {
                    passive: modifiers.includes('passive'),
                }

                if (modifiers.includes('camel')) {
                    event = camelCase(event)
                }

                let handler, listenerTarget

                if (modifiers.includes('away')) {
                    listenerTarget = document

                    handler = (e) => {
                        // Don't do anything if the click came from the element or within it.
                        if (el.contains(e.target)) return // Don't do anything if this element isn't currently visible.

                        if (el.offsetWidth < 1 && el.offsetHeight < 1) return // Now that we are sure the element is visible, AND the click
                        // is from outside it, let's run the expression.

                        runListenerHandler(component, expression, e, extraVars)

                        if (modifiers.includes('once')) {
                            document.removeEventListener(event, handler, options)
                        }
                    }
                } else {
                    listenerTarget = modifiers.includes('window')
                        ? window
                        : modifiers.includes('document')
                        ? document
                        : el

                    handler = (e) => {
                        // Remove this global event handler if the element that declared it
                        // has been removed. It's now stale.
                        if (listenerTarget === window || listenerTarget === document) {
                            if (!document.body.contains(el)) {
                                listenerTarget.removeEventListener(event, handler, options)
                                return
                            }
                        }

                        if (isKeyEvent(event)) {
                            if (isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers)) {
                                return
                            }
                        }

                        if (modifiers.includes('prevent')) e.preventDefault()
                        if (modifiers.includes('stop')) e.stopPropagation() // If the .self modifier isn't present, or if it is present and
                        // the target element matches the element we are registering the
                        // event on, run the handler

                        if (!modifiers.includes('self') || e.target === el) {
                            const returnValue = runListenerHandler(component, expression, e, extraVars)
                            returnValue.then((value) => {
                                if (value === false) {
                                    e.preventDefault()
                                } else {
                                    if (modifiers.includes('once')) {
                                        listenerTarget.removeEventListener(event, handler, options)
                                    }
                                }
                            })
                        }
                    }
                }

                if (modifiers.includes('debounce')) {
                    let nextModifier = modifiers[modifiers.indexOf('debounce') + 1] || 'invalid-wait'
                    let wait = isNumeric(nextModifier.split('ms')[0]) ? Number(nextModifier.split('ms')[0]) : 250
                    handler = debounce(handler, wait)
                }

                listenerTarget.addEventListener(event, handler, options)
            }

            function runListenerHandler(component, expression, e, extraVars) {
                return component.evaluateCommandExpression(e.target, expression, () => {
                    return _objectSpread2(
                        _objectSpread2({}, extraVars()),
                        {},
                        {
                            $event: e,
                        },
                    )
                })
            }

            function isKeyEvent(event) {
                return ['keydown', 'keyup'].includes(event)
            }

            function isListeningForASpecificKeyThatHasntBeenPressed(e, modifiers) {
                let keyModifiers = modifiers.filter((i) => {
                    return !['window', 'document', 'prevent', 'stop'].includes(i)
                })

                if (keyModifiers.includes('debounce')) {
                    let debounceIndex = keyModifiers.indexOf('debounce')
                    keyModifiers.splice(
                        debounceIndex,
                        isNumeric((keyModifiers[debounceIndex + 1] || 'invalid-wait').split('ms')[0]) ? 2 : 1,
                    )
                } // If no modifier is specified, we'll call it a press.

                if (keyModifiers.length === 0) return false // If one is passed, AND it matches the key pressed, we'll call it a press.

                if (keyModifiers.length === 1 && keyModifiers[0] === keyToModifier(e.key)) return false // The user is listening for key combinations.

                const systemKeyModifiers = ['ctrl', 'shift', 'alt', 'meta', 'cmd', 'super']
                const selectedSystemKeyModifiers = systemKeyModifiers.filter((modifier) =>
                    keyModifiers.includes(modifier),
                )
                keyModifiers = keyModifiers.filter((i) => !selectedSystemKeyModifiers.includes(i))

                if (selectedSystemKeyModifiers.length > 0) {
                    const activelyPressedKeyModifiers = selectedSystemKeyModifiers.filter((modifier) => {
                        // Alias "cmd" and "super" to "meta"
                        if (modifier === 'cmd' || modifier === 'super') modifier = 'meta'
                        return e[`${modifier}Key`]
                    }) // If all the modifiers selected are pressed, ...

                    if (activelyPressedKeyModifiers.length === selectedSystemKeyModifiers.length) {
                        // AND the remaining key is pressed as well. It's a press.
                        if (keyModifiers[0] === keyToModifier(e.key)) return false
                    }
                } // We'll call it NOT a valid keypress.

                return true
            }

            function keyToModifier(key) {
                switch (key) {
                    case '/':
                        return 'slash'

                    case ' ':
                    case 'Spacebar':
                        return 'space'

                    default:
                        return key && kebabCase(key)
                }
            }

            function registerModelListener(component, el, modifiers, expression, extraVars) {
                // If the element we are binding to is a select, a radio, or checkbox
                // we'll listen for the change event instead of the "input" event.
                var event =
                    el.tagName.toLowerCase() === 'select' ||
                    ['checkbox', 'radio'].includes(el.type) ||
                    modifiers.includes('lazy')
                        ? 'change'
                        : 'input'
                const listenerExpression = `${expression} = rightSideOfExpression($event, ${expression})`
                registerListener(component, el, event, modifiers, listenerExpression, () => {
                    return _objectSpread2(
                        _objectSpread2({}, extraVars()),
                        {},
                        {
                            rightSideOfExpression: generateModelAssignmentFunction(el, modifiers, expression),
                        },
                    )
                })
            }

            function generateModelAssignmentFunction(el, modifiers, expression) {
                if (el.type === 'radio') {
                    // Radio buttons only work properly when they share a name attribute.
                    // People might assume we take care of that for them, because
                    // they already set a shared "x-model" attribute.
                    if (!el.hasAttribute('name')) el.setAttribute('name', expression)
                }

                return (event, currentValue) => {
                    // Check for event.detail due to an issue where IE11 handles other events as a CustomEvent.
                    if (event instanceof CustomEvent && event.detail) {
                        return event.detail
                    } else if (el.type === 'checkbox') {
                        // If the data we are binding to is an array, toggle its value inside the array.
                        if (Array.isArray(currentValue)) {
                            const newValue = modifiers.includes('number')
                                ? safeParseNumber(event.target.value)
                                : event.target.value
                            return event.target.checked
                                ? currentValue.concat([newValue])
                                : currentValue.filter((el) => !checkedAttrLooseCompare(el, newValue))
                        } else {
                            return event.target.checked
                        }
                    } else if (el.tagName.toLowerCase() === 'select' && el.multiple) {
                        return modifiers.includes('number')
                            ? Array.from(event.target.selectedOptions).map((option) => {
                                  const rawValue = option.value || option.text
                                  return safeParseNumber(rawValue)
                              })
                            : Array.from(event.target.selectedOptions).map((option) => {
                                  return option.value || option.text
                              })
                    } else {
                        const rawValue = event.target.value
                        return modifiers.includes('number')
                            ? safeParseNumber(rawValue)
                            : modifiers.includes('trim')
                            ? rawValue.trim()
                            : rawValue
                    }
                }
            }

            function safeParseNumber(rawValue) {
                const number = rawValue ? parseFloat(rawValue) : null
                return isNumeric(number) ? number : rawValue
            }

            /**
             * Copyright (C) 2017 salesforce.com, inc.
             */
            const { isArray } = Array
            const {
                getPrototypeOf,
                create: ObjectCreate,
                defineProperty: ObjectDefineProperty,
                defineProperties: ObjectDefineProperties,
                isExtensible,
                getOwnPropertyDescriptor,
                getOwnPropertyNames,
                getOwnPropertySymbols,
                preventExtensions,
                hasOwnProperty,
            } = Object
            const { push: ArrayPush, concat: ArrayConcat, map: ArrayMap } = Array.prototype
            function isUndefined(obj) {
                return obj === undefined
            }
            function isFunction(obj) {
                return typeof obj === 'function'
            }
            function isObject(obj) {
                return typeof obj === 'object'
            }
            const proxyToValueMap = new WeakMap()
            function registerProxy(proxy, value) {
                proxyToValueMap.set(proxy, value)
            }
            const unwrap = (replicaOrAny) => proxyToValueMap.get(replicaOrAny) || replicaOrAny

            function wrapValue(membrane, value) {
                return membrane.valueIsObservable(value) ? membrane.getProxy(value) : value
            }
            /**
             * Unwrap property descriptors will set value on original descriptor
             * We only need to unwrap if value is specified
             * @param descriptor external descrpitor provided to define new property on original value
             */
            function unwrapDescriptor(descriptor) {
                if (hasOwnProperty.call(descriptor, 'value')) {
                    descriptor.value = unwrap(descriptor.value)
                }
                return descriptor
            }
            function lockShadowTarget(membrane, shadowTarget, originalTarget) {
                const targetKeys = ArrayConcat.call(
                    getOwnPropertyNames(originalTarget),
                    getOwnPropertySymbols(originalTarget),
                )
                targetKeys.forEach((key) => {
                    let descriptor = getOwnPropertyDescriptor(originalTarget, key)
                    // We do not need to wrap the descriptor if configurable
                    // Because we can deal with wrapping it when user goes through
                    // Get own property descriptor. There is also a chance that this descriptor
                    // could change sometime in the future, so we can defer wrapping
                    // until we need to
                    if (!descriptor.configurable) {
                        descriptor = wrapDescriptor(membrane, descriptor, wrapValue)
                    }
                    ObjectDefineProperty(shadowTarget, key, descriptor)
                })
                preventExtensions(shadowTarget)
            }
            class ReactiveProxyHandler {
                constructor(membrane, value) {
                    this.originalTarget = value
                    this.membrane = membrane
                }
                get(shadowTarget, key) {
                    const { originalTarget, membrane } = this
                    const value = originalTarget[key]
                    const { valueObserved } = membrane
                    valueObserved(originalTarget, key)
                    return membrane.getProxy(value)
                }
                set(shadowTarget, key, value) {
                    const {
                        originalTarget,
                        membrane: { valueMutated },
                    } = this
                    const oldValue = originalTarget[key]
                    if (oldValue !== value) {
                        originalTarget[key] = value
                        valueMutated(originalTarget, key)
                    } else if (key === 'length' && isArray(originalTarget)) {
                        // fix for issue #236: push will add the new index, and by the time length
                        // is updated, the internal length is already equal to the new length value
                        // therefore, the oldValue is equal to the value. This is the forking logic
                        // to support this use case.
                        valueMutated(originalTarget, key)
                    }
                    return true
                }
                deleteProperty(shadowTarget, key) {
                    const {
                        originalTarget,
                        membrane: { valueMutated },
                    } = this
                    delete originalTarget[key]
                    valueMutated(originalTarget, key)
                    return true
                }
                apply(shadowTarget, thisArg, argArray) {
                    /* No op */
                }
                construct(target, argArray, newTarget) {
                    /* No op */
                }
                has(shadowTarget, key) {
                    const {
                        originalTarget,
                        membrane: { valueObserved },
                    } = this
                    valueObserved(originalTarget, key)
                    return key in originalTarget
                }
                ownKeys(shadowTarget) {
                    const { originalTarget } = this
                    return ArrayConcat.call(getOwnPropertyNames(originalTarget), getOwnPropertySymbols(originalTarget))
                }
                isExtensible(shadowTarget) {
                    const shadowIsExtensible = isExtensible(shadowTarget)
                    if (!shadowIsExtensible) {
                        return shadowIsExtensible
                    }
                    const { originalTarget, membrane } = this
                    const targetIsExtensible = isExtensible(originalTarget)
                    if (!targetIsExtensible) {
                        lockShadowTarget(membrane, shadowTarget, originalTarget)
                    }
                    return targetIsExtensible
                }
                setPrototypeOf(shadowTarget, prototype) {}
                getPrototypeOf(shadowTarget) {
                    const { originalTarget } = this
                    return getPrototypeOf(originalTarget)
                }
                getOwnPropertyDescriptor(shadowTarget, key) {
                    const { originalTarget, membrane } = this
                    const { valueObserved } = this.membrane
                    // keys looked up via hasOwnProperty need to be reactive
                    valueObserved(originalTarget, key)
                    let desc = getOwnPropertyDescriptor(originalTarget, key)
                    if (isUndefined(desc)) {
                        return desc
                    }
                    const shadowDescriptor = getOwnPropertyDescriptor(shadowTarget, key)
                    if (!isUndefined(shadowDescriptor)) {
                        return shadowDescriptor
                    }
                    // Note: by accessing the descriptor, the key is marked as observed
                    // but access to the value, setter or getter (if available) cannot observe
                    // mutations, just like regular methods, in which case we just do nothing.
                    desc = wrapDescriptor(membrane, desc, wrapValue)
                    if (!desc.configurable) {
                        // If descriptor from original target is not configurable,
                        // We must copy the wrapped descriptor over to the shadow target.
                        // Otherwise, proxy will throw an invariant error.
                        // This is our last chance to lock the value.
                        // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy/handler/getOwnPropertyDescriptor#Invariants
                        ObjectDefineProperty(shadowTarget, key, desc)
                    }
                    return desc
                }
                preventExtensions(shadowTarget) {
                    const { originalTarget, membrane } = this
                    lockShadowTarget(membrane, shadowTarget, originalTarget)
                    preventExtensions(originalTarget)
                    return true
                }
                defineProperty(shadowTarget, key, descriptor) {
                    const { originalTarget, membrane } = this
                    const { valueMutated } = membrane
                    const { configurable } = descriptor
                    // We have to check for value in descriptor
                    // because Object.freeze(proxy) calls this method
                    // with only { configurable: false, writeable: false }
                    // Additionally, method will only be called with writeable:false
                    // if the descriptor has a value, as opposed to getter/setter
                    // So we can just check if writable is present and then see if
                    // value is present. This eliminates getter and setter descriptors
                    if (hasOwnProperty.call(descriptor, 'writable') && !hasOwnProperty.call(descriptor, 'value')) {
                        const originalDescriptor = getOwnPropertyDescriptor(originalTarget, key)
                        descriptor.value = originalDescriptor.value
                    }
                    ObjectDefineProperty(originalTarget, key, unwrapDescriptor(descriptor))
                    if (configurable === false) {
                        ObjectDefineProperty(shadowTarget, key, wrapDescriptor(membrane, descriptor, wrapValue))
                    }
                    valueMutated(originalTarget, key)
                    return true
                }
            }

            function wrapReadOnlyValue(membrane, value) {
                return membrane.valueIsObservable(value) ? membrane.getReadOnlyProxy(value) : value
            }
            class ReadOnlyHandler {
                constructor(membrane, value) {
                    this.originalTarget = value
                    this.membrane = membrane
                }
                get(shadowTarget, key) {
                    const { membrane, originalTarget } = this
                    const value = originalTarget[key]
                    const { valueObserved } = membrane
                    valueObserved(originalTarget, key)
                    return membrane.getReadOnlyProxy(value)
                }
                set(shadowTarget, key, value) {
                    return false
                }
                deleteProperty(shadowTarget, key) {
                    return false
                }
                apply(shadowTarget, thisArg, argArray) {
                    /* No op */
                }
                construct(target, argArray, newTarget) {
                    /* No op */
                }
                has(shadowTarget, key) {
                    const {
                        originalTarget,
                        membrane: { valueObserved },
                    } = this
                    valueObserved(originalTarget, key)
                    return key in originalTarget
                }
                ownKeys(shadowTarget) {
                    const { originalTarget } = this
                    return ArrayConcat.call(getOwnPropertyNames(originalTarget), getOwnPropertySymbols(originalTarget))
                }
                setPrototypeOf(shadowTarget, prototype) {}
                getOwnPropertyDescriptor(shadowTarget, key) {
                    const { originalTarget, membrane } = this
                    const { valueObserved } = membrane
                    // keys looked up via hasOwnProperty need to be reactive
                    valueObserved(originalTarget, key)
                    let desc = getOwnPropertyDescriptor(originalTarget, key)
                    if (isUndefined(desc)) {
                        return desc
                    }
                    const shadowDescriptor = getOwnPropertyDescriptor(shadowTarget, key)
                    if (!isUndefined(shadowDescriptor)) {
                        return shadowDescriptor
                    }
                    // Note: by accessing the descriptor, the key is marked as observed
                    // but access to the value or getter (if available) cannot be observed,
                    // just like regular methods, in which case we just do nothing.
                    desc = wrapDescriptor(membrane, desc, wrapReadOnlyValue)
                    if (hasOwnProperty.call(desc, 'set')) {
                        desc.set = undefined // readOnly membrane does not allow setters
                    }
                    if (!desc.configurable) {
                        // If descriptor from original target is not configurable,
                        // We must copy the wrapped descriptor over to the shadow target.
                        // Otherwise, proxy will throw an invariant error.
                        // This is our last chance to lock the value.
                        // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Proxy/handler/getOwnPropertyDescriptor#Invariants
                        ObjectDefineProperty(shadowTarget, key, desc)
                    }
                    return desc
                }
                preventExtensions(shadowTarget) {
                    return false
                }
                defineProperty(shadowTarget, key, descriptor) {
                    return false
                }
            }
            function createShadowTarget(value) {
                let shadowTarget = undefined
                if (isArray(value)) {
                    shadowTarget = []
                } else if (isObject(value)) {
                    shadowTarget = {}
                }
                return shadowTarget
            }
            const ObjectDotPrototype = Object.prototype
            function defaultValueIsObservable(value) {
                // intentionally checking for null
                if (value === null) {
                    return false
                }
                // treat all non-object types, including undefined, as non-observable values
                if (typeof value !== 'object') {
                    return false
                }
                if (isArray(value)) {
                    return true
                }
                const proto = getPrototypeOf(value)
                return proto === ObjectDotPrototype || proto === null || getPrototypeOf(proto) === null
            }
            const defaultValueObserved = (obj, key) => {
                /* do nothing */
            }
            const defaultValueMutated = (obj, key) => {
                /* do nothing */
            }
            const defaultValueDistortion = (value) => value
            function wrapDescriptor(membrane, descriptor, getValue) {
                const { set, get } = descriptor
                if (hasOwnProperty.call(descriptor, 'value')) {
                    descriptor.value = getValue(membrane, descriptor.value)
                } else {
                    if (!isUndefined(get)) {
                        descriptor.get = function () {
                            // invoking the original getter with the original target
                            return getValue(membrane, get.call(unwrap(this)))
                        }
                    }
                    if (!isUndefined(set)) {
                        descriptor.set = function (value) {
                            // At this point we don't have a clear indication of whether
                            // or not a valid mutation will occur, we don't have the key,
                            // and we are not sure why and how they are invoking this setter.
                            // Nevertheless we preserve the original semantics by invoking the
                            // original setter with the original target and the unwrapped value
                            set.call(unwrap(this), membrane.unwrapProxy(value))
                        }
                    }
                }
                return descriptor
            }
            class ReactiveMembrane {
                constructor(options) {
                    this.valueDistortion = defaultValueDistortion
                    this.valueMutated = defaultValueMutated
                    this.valueObserved = defaultValueObserved
                    this.valueIsObservable = defaultValueIsObservable
                    this.objectGraph = new WeakMap()
                    if (!isUndefined(options)) {
                        const { valueDistortion, valueMutated, valueObserved, valueIsObservable } = options
                        this.valueDistortion = isFunction(valueDistortion) ? valueDistortion : defaultValueDistortion
                        this.valueMutated = isFunction(valueMutated) ? valueMutated : defaultValueMutated
                        this.valueObserved = isFunction(valueObserved) ? valueObserved : defaultValueObserved
                        this.valueIsObservable = isFunction(valueIsObservable)
                            ? valueIsObservable
                            : defaultValueIsObservable
                    }
                }
                getProxy(value) {
                    const unwrappedValue = unwrap(value)
                    const distorted = this.valueDistortion(unwrappedValue)
                    if (this.valueIsObservable(distorted)) {
                        const o = this.getReactiveState(unwrappedValue, distorted)
                        // when trying to extract the writable version of a readonly
                        // we return the readonly.
                        return o.readOnly === value ? value : o.reactive
                    }
                    return distorted
                }
                getReadOnlyProxy(value) {
                    value = unwrap(value)
                    const distorted = this.valueDistortion(value)
                    if (this.valueIsObservable(distorted)) {
                        return this.getReactiveState(value, distorted).readOnly
                    }
                    return distorted
                }
                unwrapProxy(p) {
                    return unwrap(p)
                }
                getReactiveState(value, distortedValue) {
                    const { objectGraph } = this
                    let reactiveState = objectGraph.get(distortedValue)
                    if (reactiveState) {
                        return reactiveState
                    }
                    const membrane = this
                    reactiveState = {
                        get reactive() {
                            const reactiveHandler = new ReactiveProxyHandler(membrane, distortedValue)
                            // caching the reactive proxy after the first time it is accessed
                            const proxy = new Proxy(createShadowTarget(distortedValue), reactiveHandler)
                            registerProxy(proxy, value)
                            ObjectDefineProperty(this, 'reactive', { value: proxy })
                            return proxy
                        },
                        get readOnly() {
                            const readOnlyHandler = new ReadOnlyHandler(membrane, distortedValue)
                            // caching the readOnly proxy after the first time it is accessed
                            const proxy = new Proxy(createShadowTarget(distortedValue), readOnlyHandler)
                            registerProxy(proxy, value)
                            ObjectDefineProperty(this, 'readOnly', { value: proxy })
                            return proxy
                        },
                    }
                    objectGraph.set(distortedValue, reactiveState)
                    return reactiveState
                }
            }
            /** version: 0.26.0 */

            function wrap(data, mutationCallback) {
                let membrane = new ReactiveMembrane({
                    valueMutated(target, key) {
                        mutationCallback(target, key)
                    },
                })
                return {
                    data: membrane.getProxy(data),
                    membrane: membrane,
                }
            }
            function unwrap$1(membrane, observable) {
                let unwrappedData = membrane.unwrapProxy(observable)
                let copy = {}
                Object.keys(unwrappedData).forEach((key) => {
                    if (['$el', '$refs', '$nextTick', '$watch'].includes(key)) return
                    copy[key] = unwrappedData[key]
                })
                return copy
            }

            class Component {
                constructor(el, componentForClone = null) {
                    this.$el = el
                    const dataAttr = this.$el.getAttribute('x-data')
                    const dataExpression = dataAttr === '' ? '{}' : dataAttr
                    const initExpression = this.$el.getAttribute('x-init')
                    let dataExtras = {
                        $el: this.$el,
                    }
                    let canonicalComponentElementReference = componentForClone ? componentForClone.$el : this.$el
                    Object.entries(Alpine.magicProperties).forEach(([name, callback]) => {
                        Object.defineProperty(dataExtras, `$${name}`, {
                            get: function get() {
                                return callback(canonicalComponentElementReference)
                            },
                        })
                    })
                    this.unobservedData = componentForClone
                        ? componentForClone.getUnobservedData()
                        : saferEval(el, dataExpression, dataExtras)
                    // Construct a Proxy-based observable. This will be used to handle reactivity.

                    let { membrane, data } = this.wrapDataInObservable(this.unobservedData)
                    this.$data = data
                    this.membrane = membrane // After making user-supplied data methods reactive, we can now add
                    // our magic properties to the original data for access.

                    this.unobservedData.$el = this.$el
                    this.unobservedData.$refs = this.getRefsProxy()
                    this.nextTickStack = []

                    this.unobservedData.$nextTick = (callback) => {
                        this.nextTickStack.push(callback)
                    }

                    this.watchers = {}

                    this.unobservedData.$watch = (property, callback) => {
                        if (!this.watchers[property]) this.watchers[property] = []
                        this.watchers[property].push(callback)
                    }
                    /* MODERN-ONLY:START */
                    // We remove this piece of code from the legacy build.
                    // In IE11, we have already defined our helpers at this point.
                    // Register custom magic properties.

                    Object.entries(Alpine.magicProperties).forEach(([name, callback]) => {
                        Object.defineProperty(this.unobservedData, `$${name}`, {
                            get: function get() {
                                return callback(canonicalComponentElementReference, this.$el)
                            },
                        })
                    })
                    /* MODERN-ONLY:END */

                    this.showDirectiveStack = []
                    componentForClone || Alpine.onBeforeComponentInitializeds.forEach((callback) => callback(this))
                    var initReturnedCallback // If x-init is present AND we aren't cloning (skip x-init on clone)

                    if (initExpression && !componentForClone) {
                        // We want to allow data manipulation, but not trigger DOM updates just yet.
                        // We haven't even initialized the elements with their Alpine bindings. I mean c'mon.
                        this.pauseReactivity = true
                        initReturnedCallback = this.evaluateReturnExpression(this.$el, initExpression)
                        this.pauseReactivity = false
                    } // Register all our listeners and set all our attribute bindings.
                    // If we're cloning a component, the third parameter ensures no duplicate
                    // event listeners are registered (the mutation observer will take care of them)

                    this.initializeElements(this.$el, () => {}, componentForClone) // Use mutation observer to detect new elements being added within this component at run-time.
                    // Alpine's just so darn flexible amirite?

                    this.listenForNewElementsToInitialize()

                    if (typeof initReturnedCallback === 'function') {
                        // Run the callback returned from the "x-init" hook to allow the user to do stuff after
                        // Alpine's got it's grubby little paws all over everything.
                        initReturnedCallback.call(this.$data)
                    }

                    componentForClone ||
                        setTimeout(() => {
                            Alpine.onComponentInitializeds.forEach((callback) => callback(this))
                        }, 0)
                }

                getUnobservedData() {
                    return unwrap$1(this.membrane, this.$data)
                }

                wrapDataInObservable(data) {
                    var self = this
                    let updateDom = debounce(function () {
                        self.updateElements(self.$el)
                    }, 0)
                    return wrap(data, (target, key) => {
                        if (self.watchers[key]) {
                            // If there's a watcher for this specific key, run it.
                            self.watchers[key].forEach((callback) => callback(target[key]))
                        } else if (Array.isArray(target)) {
                            // Arrays are special cases, if any of the items change, we consider the array as mutated.
                            Object.keys(self.watchers).forEach((fullDotNotationKey) => {
                                let dotNotationParts = fullDotNotationKey.split('.') // Ignore length mutations since they would result in duplicate calls.
                                // For example, when calling push, we would get a mutation for the item's key
                                // and a second mutation for the length property.

                                if (key === 'length') return
                                dotNotationParts.reduce((comparisonData, part) => {
                                    if (Object.is(target, comparisonData[part])) {
                                        self.watchers[fullDotNotationKey].forEach((callback) => callback(target))
                                    }

                                    return comparisonData[part]
                                }, self.unobservedData)
                            })
                        } else {
                            // Let's walk through the watchers with "dot-notation" (foo.bar) and see
                            // if this mutation fits any of them.
                            Object.keys(self.watchers)
                                .filter((i) => i.includes('.'))
                                .forEach((fullDotNotationKey) => {
                                    let dotNotationParts = fullDotNotationKey.split('.') // If this dot-notation watcher's last "part" doesn't match the current
                                    // key, then skip it early for performance reasons.

                                    if (key !== dotNotationParts[dotNotationParts.length - 1]) return // Now, walk through the dot-notation "parts" recursively to find
                                    // a match, and call the watcher if one's found.

                                    dotNotationParts.reduce((comparisonData, part) => {
                                        if (Object.is(target, comparisonData)) {
                                            // Run the watchers.
                                            self.watchers[fullDotNotationKey].forEach((callback) =>
                                                callback(target[key]),
                                            )
                                        }

                                        return comparisonData[part]
                                    }, self.unobservedData)
                                })
                        } // Don't react to data changes for cases like the `x-created` hook.

                        if (self.pauseReactivity) return
                        updateDom()
                    })
                }

                walkAndSkipNestedComponents(el, callback, initializeComponentCallback = () => {}) {
                    walk(el, (el) => {
                        // We've hit a component.
                        if (el.hasAttribute('x-data')) {
                            // If it's not the current one.
                            if (!el.isSameNode(this.$el)) {
                                // Initialize it if it's not.
                                if (!el.__x) initializeComponentCallback(el) // Now we'll let that sub-component deal with itself.

                                return false
                            }
                        }

                        return callback(el)
                    })
                }

                initializeElements(rootEl, extraVars = () => {}, componentForClone = false) {
                    this.walkAndSkipNestedComponents(
                        rootEl,
                        (el) => {
                            // Don't touch spawns from for loop
                            if (el.__x_for_key !== undefined) return false // Don't touch spawns from if directives

                            if (el.__x_inserted_me !== undefined) return false
                            this.initializeElement(el, extraVars, componentForClone ? false : true)
                        },
                        (el) => {
                            if (!componentForClone) el.__x = new Component(el)
                        },
                    )
                    this.executeAndClearRemainingShowDirectiveStack()
                    this.executeAndClearNextTickStack(rootEl)
                }

                initializeElement(el, extraVars, shouldRegisterListeners = true) {
                    // To support class attribute merging, we have to know what the element's
                    // original class attribute looked like for reference.
                    if (el.hasAttribute('class') && getXAttrs(el, this).length > 0) {
                        el.__x_original_classes = convertClassStringToArray(el.getAttribute('class'))
                    }

                    shouldRegisterListeners && this.registerListeners(el, extraVars)
                    this.resolveBoundAttributes(el, true, extraVars)
                }

                updateElements(rootEl, extraVars = () => {}) {
                    this.walkAndSkipNestedComponents(
                        rootEl,
                        (el) => {
                            // Don't touch spawns from for loop (and check if the root is actually a for loop in a parent, don't skip it.)
                            if (el.__x_for_key !== undefined && !el.isSameNode(this.$el)) return false
                            this.updateElement(el, extraVars)
                        },
                        (el) => {
                            el.__x = new Component(el)
                        },
                    )
                    this.executeAndClearRemainingShowDirectiveStack()
                    this.executeAndClearNextTickStack(rootEl)
                }

                executeAndClearNextTickStack(el) {
                    // Skip spawns from alpine directives
                    if (el === this.$el && this.nextTickStack.length > 0) {
                        // We run the tick stack after the next frame to allow any
                        // running transitions to pass the initial show stage.
                        requestAnimationFrame(() => {
                            while (this.nextTickStack.length > 0) {
                                this.nextTickStack.shift()()
                            }
                        })
                    }
                }

                executeAndClearRemainingShowDirectiveStack() {
                    // The goal here is to start all the x-show transitions
                    // and build a nested promise chain so that elements
                    // only hide when the children are finished hiding.
                    this.showDirectiveStack
                        .reverse()
                        .map((handler) => {
                            return new Promise((resolve, reject) => {
                                handler(resolve, reject)
                            })
                        })
                        .reduce(
                            (promiseChain, promise) => {
                                return promiseChain.then(() => {
                                    return promise.then((finishElement) => {
                                        finishElement()
                                    })
                                })
                            },
                            Promise.resolve(() => {}),
                        )
                        .catch((e) => {
                            if (e !== TRANSITION_CANCELLED) throw e
                        }) // We've processed the handler stack. let's clear it.

                    this.showDirectiveStack = []
                    this.showDirectiveLastElement = undefined
                }

                updateElement(el, extraVars) {
                    this.resolveBoundAttributes(el, false, extraVars)
                }

                registerListeners(el, extraVars) {
                    getXAttrs(el, this).forEach(({ type, value, modifiers, expression }) => {
                        switch (type) {
                            case 'on':
                                registerListener(this, el, value, modifiers, expression, extraVars)
                                break

                            case 'model':
                                registerModelListener(this, el, modifiers, expression, extraVars)
                                break
                        }
                    })
                }

                resolveBoundAttributes(el, initialUpdate = false, extraVars) {
                    let attrs = getXAttrs(el, this)
                    attrs.forEach(({ type, value, modifiers, expression }) => {
                        switch (type) {
                            case 'model':
                                handleAttributeBindingDirective(
                                    this,
                                    el,
                                    'value',
                                    expression,
                                    extraVars,
                                    type,
                                    modifiers,
                                )
                                break

                            case 'bind':
                                // The :key binding on an x-for is special, ignore it.
                                if (el.tagName.toLowerCase() === 'template' && value === 'key') return
                                handleAttributeBindingDirective(this, el, value, expression, extraVars, type, modifiers)
                                break

                            case 'text':
                                var output = this.evaluateReturnExpression(el, expression, extraVars)
                                handleTextDirective(el, output, expression)
                                break

                            case 'html':
                                handleHtmlDirective(this, el, expression, extraVars)
                                break

                            case 'show':
                                var output = this.evaluateReturnExpression(el, expression, extraVars)
                                handleShowDirective(this, el, output, modifiers, initialUpdate)
                                break

                            case 'if':
                                // If this element also has x-for on it, don't process x-if.
                                // We will let the "x-for" directive handle the "if"ing.
                                if (attrs.some((i) => i.type === 'for')) return
                                var output = this.evaluateReturnExpression(el, expression, extraVars)
                                handleIfDirective(this, el, output, initialUpdate, extraVars)
                                break

                            case 'for':
                                handleForDirective(this, el, expression, initialUpdate, extraVars)
                                break

                            case 'cloak':
                                el.removeAttribute('x-cloak')
                                break
                        }
                    })
                }

                evaluateReturnExpression(el, expression, extraVars = () => {}) {
                    return saferEval(
                        el,
                        expression,
                        this.$data,
                        _objectSpread2(
                            _objectSpread2({}, extraVars()),
                            {},
                            {
                                $dispatch: this.getDispatchFunction(el),
                            },
                        ),
                    )
                }

                evaluateCommandExpression(el, expression, extraVars = () => {}) {
                    return saferEvalNoReturn(
                        el,
                        expression,
                        this.$data,
                        _objectSpread2(
                            _objectSpread2({}, extraVars()),
                            {},
                            {
                                $dispatch: this.getDispatchFunction(el),
                            },
                        ),
                    )
                }

                getDispatchFunction(el) {
                    return (event, detail = {}) => {
                        el.dispatchEvent(
                            new CustomEvent(event, {
                                detail,
                                bubbles: true,
                            }),
                        )
                    }
                }

                listenForNewElementsToInitialize() {
                    const targetNode = this.$el
                    const observerOptions = {
                        childList: true,
                        attributes: true,
                        subtree: true,
                    }
                    const observer = new MutationObserver((mutations) => {
                        for (let i = 0; i < mutations.length; i++) {
                            // Filter out mutations triggered from child components.
                            const closestParentComponent = mutations[i].target.closest('[x-data]')
                            if (!(closestParentComponent && closestParentComponent.isSameNode(this.$el))) continue

                            if (mutations[i].type === 'attributes' && mutations[i].attributeName === 'x-data') {
                                const xAttr = mutations[i].target.getAttribute('x-data') || '{}'
                                const rawData = saferEval(this.$el, xAttr, {
                                    $el: this.$el,
                                })
                                Object.keys(rawData).forEach((key) => {
                                    if (this.$data[key] !== rawData[key]) {
                                        this.$data[key] = rawData[key]
                                    }
                                })
                            }

                            if (mutations[i].addedNodes.length > 0) {
                                mutations[i].addedNodes.forEach((node) => {
                                    if (node.nodeType !== 1 || node.__x_inserted_me) return

                                    if (node.matches('[x-data]') && !node.__x) {
                                        node.__x = new Component(node)
                                        return
                                    }

                                    this.initializeElements(node)
                                })
                            }
                        }
                    })
                    observer.observe(targetNode, observerOptions)
                }

                getRefsProxy() {
                    var self = this
                    var refObj = {}
                    // One of the goals of this is to not hold elements in memory, but rather re-evaluate
                    // the DOM when the system needs something from it. This way, the framework is flexible and
                    // friendly to outside DOM changes from libraries like Vue/Livewire.
                    // For this reason, I'm using an "on-demand" proxy to fake a "$refs" object.

                    return new Proxy(refObj, {
                        get(object, property) {
                            if (property === '$isAlpineProxy') return true
                            var ref // We can't just query the DOM because it's hard to filter out refs in
                            // nested components.

                            self.walkAndSkipNestedComponents(self.$el, (el) => {
                                if (el.hasAttribute('x-ref') && el.getAttribute('x-ref') === property) {
                                    ref = el
                                }
                            })
                            return ref
                        },
                    })
                }
            }

            const Alpine = {
                version: '2.8.2',
                pauseMutationObserver: false,
                magicProperties: {},
                onComponentInitializeds: [],
                onBeforeComponentInitializeds: [],
                ignoreFocusedForValueBinding: false,
                start: async function start() {
                    if (!isTesting()) {
                        await domReady()
                    }

                    this.discoverComponents((el) => {
                        this.initializeComponent(el)
                    }) // It's easier and more performant to just support Turbolinks than listen
                    // to MutationObserver mutations at the document level.

                    document.addEventListener('turbolinks:load', () => {
                        this.discoverUninitializedComponents((el) => {
                            this.initializeComponent(el)
                        })
                    })
                    this.listenForNewUninitializedComponentsAtRunTime()
                },
                discoverComponents: function discoverComponents(callback) {
                    const rootEls = document.querySelectorAll('[x-data]')
                    rootEls.forEach((rootEl) => {
                        callback(rootEl)
                    })
                },
                discoverUninitializedComponents: function discoverUninitializedComponents(callback, el = null) {
                    const rootEls = (el || document).querySelectorAll('[x-data]')
                    Array.from(rootEls)
                        .filter((el) => el.__x === undefined)
                        .forEach((rootEl) => {
                            callback(rootEl)
                        })
                },
                listenForNewUninitializedComponentsAtRunTime: function listenForNewUninitializedComponentsAtRunTime() {
                    const targetNode = document.querySelector('body')
                    const observerOptions = {
                        childList: true,
                        attributes: true,
                        subtree: true,
                    }
                    const observer = new MutationObserver((mutations) => {
                        if (this.pauseMutationObserver) return

                        for (let i = 0; i < mutations.length; i++) {
                            if (mutations[i].addedNodes.length > 0) {
                                mutations[i].addedNodes.forEach((node) => {
                                    // Discard non-element nodes (like line-breaks)
                                    if (node.nodeType !== 1) return // Discard any changes happening within an existing component.
                                    // They will take care of themselves.

                                    if (node.parentElement && node.parentElement.closest('[x-data]')) return
                                    this.discoverUninitializedComponents((el) => {
                                        this.initializeComponent(el)
                                    }, node.parentElement)
                                })
                            }
                        }
                    })
                    observer.observe(targetNode, observerOptions)
                },
                initializeComponent: function initializeComponent(el) {
                    if (!el.__x) {
                        // Wrap in a try/catch so that we don't prevent other components
                        // from initializing when one component contains an error.
                        try {
                            el.__x = new Component(el)
                        } catch (error) {
                            setTimeout(() => {
                                throw error
                            }, 0)
                        }
                    }
                },
                clone: function clone(component, newEl) {
                    if (!newEl.__x) {
                        newEl.__x = new Component(newEl, component)
                    }
                },
                addMagicProperty: function addMagicProperty(name, callback) {
                    this.magicProperties[name] = callback
                },
                onComponentInitialized: function onComponentInitialized(callback) {
                    this.onComponentInitializeds.push(callback)
                },
                onBeforeComponentInitialized: function onBeforeComponentInitialized(callback) {
                    this.onBeforeComponentInitializeds.push(callback)
                },
            }

            if (!isTesting()) {
                window.Alpine = Alpine

                if (window.deferLoadingAlpine) {
                    window.deferLoadingAlpine(function () {
                        window.Alpine.start()
                    })
                } else {
                    window.Alpine.start()
                }
            }

            return Alpine
        })
    })

    var xssEscape = createCommonjsModule(function (module, exports) {
        // xss-escape
        // https://github.com/DubFriend/xss-escape

        // https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet

        ;(function () {
            var isString = function (data) {
                return typeof data === 'string'
            }

            var isArray = function (value) {
                return toString.call(value) === '[object Array]'
            }

            var isObject = function (value) {
                return !isArray(value) && value instanceof Object
            }

            var isNumber = function (value) {
                return typeof value === 'number'
            }

            var isBoolean = function (value) {
                return typeof value === 'boolean'
            }

            var charForLoopStrategy = function (unescapedString) {
                var i,
                    character,
                    escapedString = ''

                for (i = 0; i < unescapedString.length; i += 1) {
                    character = unescapedString.charAt(i)
                    switch (character) {
                        case '<':
                            escapedString += '&lt;'
                            break
                        case '>':
                            escapedString += '&gt;'
                            break
                        case '&':
                            escapedString += '&amp;'
                            break
                        case '/':
                            escapedString += '&#x2F;'
                            break
                        case '"':
                            escapedString += '&quot;'
                            break
                        case "'":
                            escapedString += '&#x27;'
                            break
                        default:
                            escapedString += character
                    }
                }

                return escapedString
            }

            var regexStrategy = function (string) {
                return string
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#x27;')
                    .replace(/\//g, '&#x2F;')
            }

            var shiftToRegexStrategyThreshold = 32

            var xssEscape = function (data, forceStrategy) {
                var escapedData, key, i, stringLength

                if (isString(data)) {
                    stringLength = data.length
                    if (forceStrategy === 'charForLoopStrategy') {
                        escapedData = charForLoopStrategy(data)
                    } else if (forceStrategy === 'regexStrategy') {
                        escapedData = regexStrategy(data)
                    } else if (stringLength > shiftToRegexStrategyThreshold) {
                        escapedData = regexStrategy(data)
                    } else {
                        escapedData = charForLoopStrategy(data)
                    }
                } else if (isNumber(data) || isBoolean(data)) {
                    escapedData = data
                } else if (isArray(data)) {
                    escapedData = []
                    for (i = 0; i < data.length; i += 1) {
                        escapedData.push(xssEscape(data[i]))
                    }
                } else if (isObject(data)) {
                    escapedData = {}
                    for (key in data) {
                        if (data.hasOwnProperty(key)) {
                            escapedData[key] = xssEscape(data[key])
                        }
                    }
                }

                return escapedData
            }

            // use in browser or nodejs
            {
                if (module.exports) {
                    exports = module.exports = xssEscape
                }
                exports.xssEscape = xssEscape
            }
        }).call(commonjsGlobal)
    })

    // import { __ } from '@wordpress/i18n' FULL OF BUGS!
    function MediaLibrary() {
        return {
            manager: {},
            init: function init() {
                var _this = this
                this.manager = wp.media.frames.file_frame = wp.media({
                    title: __('Select Images', 'metagallery'),
                    multiple: true,
                    library: {
                        type: 'image',
                    },
                })
                var viewsToRemove = this.manager.states.models.filter(function (view) {
                    return !['library'].includes(view.id)
                })
                this.manager.states.remove(viewsToRemove)
                this.manager.on('select', function () {
                    var selection = _this.manager.state().get('selection').toJSON()
                    var images = selection
                        .filter(function (image) {
                            return image.type === 'image'
                        })
                        .map(function (image) {
                            return {
                                _uid: parseInt(Date.now() + Math.floor(Math.random() * 1000000), 10),
                                height: image.height,
                                width: image.width,
                                title: image.title,
                                alt: xssEscape.xssEscape(image.alt),
                                caption: image.caption,
                                src: {
                                    main: image.sizes.full,
                                    thumbnail: image.sizes.thumbnail,
                                },
                                WP: {
                                    id: image.id,
                                },
                            }
                        })
                    _this.$component('current').addImages(images)
                })
            },
        }
    }

    function GalleryImageMarkup(image) {
        return (
            "<div\n        x-title=\"Image Wrapper\"\n        x-data=\"{\n            get itemWrapper() {\n                return $el.style.cssText +\n                'width:' + this.$component('current').settings.percentImageWidth + '%;' +\n                'min-width:' + this.$component('current').settings.minImageWidth + 'px;' +\n                'max-width:' + this.$component('current').settings.maxImageWidth + 'px;'\n            },\n\n        }\"\n        class=\"item absolute overflow-hidden\"\n        :style=\"itemWrapper\">\n        <div class=\"item-content relative h-full w-full\">\n            <div\n                x-title=\"Gallery Image\"\n                x-data=\"GalleryImage(" +
            image._uid +
            ')"\n                x-init="init()"\n                class="group cursor-move">\n                <button\n                    x-cloak\n                    class="transition p-2 rounded-full duration-200 bg-nord0 text-nord13 absolute top-2 right-2 opacity-0 group-hover:opacity-100 focus:outline-none ring-2 ring-nord2 focus:ring-nord9 ring-opacity-70 focus:ring-opacity-100 focus:text-nord9"\n                    :class="{ \'opacity-100 ring-4\': open }"\n                    :style="buttonStyles"\n                    @click="$dispatch(\'open-image-settings\', { image: ' +
            image._uid +
            ' })">\n                    <svg class="w-6 h-6 block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">\n                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />\n                    </svg>\n                    <span class="sr-only">' +
            window.__('edit', 'metagallery') +
            '</span>\n                </button>\n                <img\n                    class="border-0"\n                    :style="imageStyles"\n                    width="' +
            image.width +
            '"\n                    height="' +
            image.height +
            '"\n                    src="' +
            image.src.main.url +
            '"\n                    alt="' +
            xssEscape.xssEscape(image.alt) +
            '"/>\n            </div>\n        </div>\n    </div>'
        )
    }
    function GalleryImage(id) {
        return {
            _uid: id,
            get open() {
                return this.$component('image-settings').imageId == this._uid
            },
            get imageStyles() {
                return (
                    '\n                padding:' +
                    this.$component('current').settings.imageSpacing +
                    'px;\n            '
                )
            },
            get buttonStyles() {
                return (
                    '\n                margin-top: ' +
                    this.$component('current').settings.imageSpacing +
                    'px;\n                margin-right: ' +
                    this.$component('current').settings.imageSpacing +
                    'px;\n            '
                )
            },
            init: function init() {
                setTimeout(function () {
                    window.dispatchEvent(
                        new CustomEvent('reset-layout', {
                            detail: {},
                            bubbles: true,
                        }),
                    )
                }, 0)
            },
        }
    }

    function Gallery$1() {
        return {
            muuri: null,
            images: [],
            init: function init() {
                var _this = this
                this.images = JSON.parse(JSON.stringify(this.$component('current').images))
                if (!this.images.length) return
                window.metagalleryGrid = new window.Muuri(
                    '[id=metagallery-grid-' + this.$component('current').data.ID + ']',
                    {
                        items: this.images.map(function (i) {
                            return _this.buildImage(i)
                        }),
                        dragSortPredicate: {
                            action: 'move',
                        },
                        dragEnabled: true,
                        layout: {
                            fillGaps: true,
                            // horizontal: true,
                        },
                    },
                )

                window.metagalleryGrid.on('move', function (_data) {
                    _this.$component('current').dirty = true
                })
            },
            get containerStyles() {
                return (
                    '\n                margin: 0 -' +
                    this.$component('current').settings.imageSpacing +
                    'px;\n            '
                )
            },
            addImages: function addImages(images) {
                var _this2 = this
                if (!window.metagalleryGrid) {
                    return this.init()
                }
                window.metagalleryGrid.add(
                    images.map(function (i) {
                        return _this2.buildImage(i)
                    }),
                    {
                        index: 0,
                    },
                )
            },
            removeImages: function removeImages(images) {
                // Not the most efficient filter, but there's no mechanism
                // currently to remove multiple images, so it's fine
                this.$component('current').dirty = true
                var gridItems = window.metagalleryGrid.getItems()
                window.metagalleryGrid.remove(
                    images.map(function (i) {
                        return gridItems.find(function (img) {
                            return img.getElement().querySelector('[x-data]').__x.getUnobservedData()._uid == i
                        })
                    }),
                    {
                        removeElements: true,
                    },
                )
            },
            buildImage: function buildImage(image) {
                var itemElem = document.createElement('div')
                var itemTemplate = GalleryImageMarkup(image)
                itemElem.innerHTML = itemTemplate
                return itemElem.firstChild
            },
        }
    }

    var bind = function bind(fn, thisArg) {
        return function wrap() {
            var args = new Array(arguments.length)
            for (var i = 0; i < args.length; i++) {
                args[i] = arguments[i]
            }
            return fn.apply(thisArg, args)
        }
    }

    // utils is a library of generic helper functions non-specific to axios

    var toString$2 = Object.prototype.toString

    /**
     * Determine if a value is an Array
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an Array, otherwise false
     */
    function isArray(val) {
        return toString$2.call(val) === '[object Array]'
    }

    /**
     * Determine if a value is undefined
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if the value is undefined, otherwise false
     */
    function isUndefined(val) {
        return typeof val === 'undefined'
    }

    /**
     * Determine if a value is a Buffer
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Buffer, otherwise false
     */
    function isBuffer(val) {
        return (
            val !== null &&
            !isUndefined(val) &&
            val.constructor !== null &&
            !isUndefined(val.constructor) &&
            typeof val.constructor.isBuffer === 'function' &&
            val.constructor.isBuffer(val)
        )
    }

    /**
     * Determine if a value is an ArrayBuffer
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an ArrayBuffer, otherwise false
     */
    function isArrayBuffer(val) {
        return toString$2.call(val) === '[object ArrayBuffer]'
    }

    /**
     * Determine if a value is a FormData
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an FormData, otherwise false
     */
    function isFormData(val) {
        return typeof FormData !== 'undefined' && val instanceof FormData
    }

    /**
     * Determine if a value is a view on an ArrayBuffer
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
     */
    function isArrayBufferView(val) {
        var result
        if (typeof ArrayBuffer !== 'undefined' && ArrayBuffer.isView) {
            result = ArrayBuffer.isView(val)
        } else {
            result = val && val.buffer && val.buffer instanceof ArrayBuffer
        }
        return result
    }

    /**
     * Determine if a value is a String
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a String, otherwise false
     */
    function isString(val) {
        return typeof val === 'string'
    }

    /**
     * Determine if a value is a Number
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Number, otherwise false
     */
    function isNumber(val) {
        return typeof val === 'number'
    }

    /**
     * Determine if a value is an Object
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is an Object, otherwise false
     */
    function isObject(val) {
        return val !== null && typeof val === 'object'
    }

    /**
     * Determine if a value is a plain Object
     *
     * @param {Object} val The value to test
     * @return {boolean} True if value is a plain Object, otherwise false
     */
    function isPlainObject$1(val) {
        if (toString$2.call(val) !== '[object Object]') {
            return false
        }

        var prototype = Object.getPrototypeOf(val)
        return prototype === null || prototype === Object.prototype
    }

    /**
     * Determine if a value is a Date
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Date, otherwise false
     */
    function isDate(val) {
        return toString$2.call(val) === '[object Date]'
    }

    /**
     * Determine if a value is a File
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a File, otherwise false
     */
    function isFile(val) {
        return toString$2.call(val) === '[object File]'
    }

    /**
     * Determine if a value is a Blob
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Blob, otherwise false
     */
    function isBlob(val) {
        return toString$2.call(val) === '[object Blob]'
    }

    /**
     * Determine if a value is a Function
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Function, otherwise false
     */
    function isFunction$1(val) {
        return toString$2.call(val) === '[object Function]'
    }

    /**
     * Determine if a value is a Stream
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a Stream, otherwise false
     */
    function isStream(val) {
        return isObject(val) && isFunction$1(val.pipe)
    }

    /**
     * Determine if a value is a URLSearchParams object
     *
     * @param {Object} val The value to test
     * @returns {boolean} True if value is a URLSearchParams object, otherwise false
     */
    function isURLSearchParams(val) {
        return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams
    }

    /**
     * Trim excess whitespace off the beginning and end of a string
     *
     * @param {String} str The String to trim
     * @returns {String} The String freed of excess whitespace
     */
    function trim(str) {
        return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, '')
    }

    /**
     * Determine if we're running in a standard browser environment
     *
     * This allows axios to run in a web worker, and react-native.
     * Both environments support XMLHttpRequest, but not fully standard globals.
     *
     * web workers:
     *  typeof window -> undefined
     *  typeof document -> undefined
     *
     * react-native:
     *  navigator.product -> 'ReactNative'
     * nativescript
     *  navigator.product -> 'NativeScript' or 'NS'
     */
    function isStandardBrowserEnv() {
        if (
            typeof navigator !== 'undefined' &&
            (navigator.product === 'ReactNative' || navigator.product === 'NativeScript' || navigator.product === 'NS')
        ) {
            return false
        }
        return typeof window !== 'undefined' && typeof document !== 'undefined'
    }

    /**
     * Iterate over an Array or an Object invoking a function for each item.
     *
     * If `obj` is an Array callback will be called passing
     * the value, index, and complete array for each item.
     *
     * If 'obj' is an Object callback will be called passing
     * the value, key, and complete object for each property.
     *
     * @param {Object|Array} obj The object to iterate
     * @param {Function} fn The callback to invoke for each item
     */
    function forEach(obj, fn) {
        // Don't bother if no value provided
        if (obj === null || typeof obj === 'undefined') {
            return
        }

        // Force an array if not already something iterable
        if (typeof obj !== 'object') {
            /*eslint no-param-reassign:0*/
            obj = [obj]
        }

        if (isArray(obj)) {
            // Iterate over array values
            for (var i = 0, l = obj.length; i < l; i++) {
                fn.call(null, obj[i], i, obj)
            }
        } else {
            // Iterate over object keys
            for (var key in obj) {
                if (Object.prototype.hasOwnProperty.call(obj, key)) {
                    fn.call(null, obj[key], key, obj)
                }
            }
        }
    }

    /**
     * Accepts varargs expecting each argument to be an object, then
     * immutably merges the properties of each object and returns result.
     *
     * When multiple objects contain the same key the later object in
     * the arguments list will take precedence.
     *
     * Example:
     *
     * ```js
     * var result = merge({foo: 123}, {foo: 456});
     * console.log(result.foo); // outputs 456
     * ```
     *
     * @param {Object} obj1 Object to merge
     * @returns {Object} Result of all merge properties
     */
    function merge(/* obj1, obj2, obj3, ... */) {
        var result = {}
        function assignValue(val, key) {
            if (isPlainObject$1(result[key]) && isPlainObject$1(val)) {
                result[key] = merge(result[key], val)
            } else if (isPlainObject$1(val)) {
                result[key] = merge({}, val)
            } else if (isArray(val)) {
                result[key] = val.slice()
            } else {
                result[key] = val
            }
        }

        for (var i = 0, l = arguments.length; i < l; i++) {
            forEach(arguments[i], assignValue)
        }
        return result
    }

    /**
     * Extends object a by mutably adding to it the properties of object b.
     *
     * @param {Object} a The object to be extended
     * @param {Object} b The object to copy properties from
     * @param {Object} thisArg The object to bind function to
     * @return {Object} The resulting value of object a
     */
    function extend(a, b, thisArg) {
        forEach(b, function assignValue(val, key) {
            if (thisArg && typeof val === 'function') {
                a[key] = bind(val, thisArg)
            } else {
                a[key] = val
            }
        })
        return a
    }

    /**
     * Remove byte order marker. This catches EF BB BF (the UTF-8 BOM)
     *
     * @param {string} content with BOM
     * @return {string} content value without BOM
     */
    function stripBOM(content) {
        if (content.charCodeAt(0) === 0xfeff) {
            content = content.slice(1)
        }
        return content
    }

    var utils = {
        isArray: isArray,
        isArrayBuffer: isArrayBuffer,
        isBuffer: isBuffer,
        isFormData: isFormData,
        isArrayBufferView: isArrayBufferView,
        isString: isString,
        isNumber: isNumber,
        isObject: isObject,
        isPlainObject: isPlainObject$1,
        isUndefined: isUndefined,
        isDate: isDate,
        isFile: isFile,
        isBlob: isBlob,
        isFunction: isFunction$1,
        isStream: isStream,
        isURLSearchParams: isURLSearchParams,
        isStandardBrowserEnv: isStandardBrowserEnv,
        forEach: forEach,
        merge: merge,
        extend: extend,
        trim: trim,
        stripBOM: stripBOM,
    }

    function encode(val) {
        return encodeURIComponent(val)
            .replace(/%3A/gi, ':')
            .replace(/%24/g, '$')
            .replace(/%2C/gi, ',')
            .replace(/%20/g, '+')
            .replace(/%5B/gi, '[')
            .replace(/%5D/gi, ']')
    }

    /**
     * Build a URL by appending params to the end
     *
     * @param {string} url The base of the url (e.g., http://www.google.com)
     * @param {object} [params] The params to be appended
     * @returns {string} The formatted url
     */
    var buildURL = function buildURL(url, params, paramsSerializer) {
        /*eslint no-param-reassign:0*/
        if (!params) {
            return url
        }

        var serializedParams
        if (paramsSerializer) {
            serializedParams = paramsSerializer(params)
        } else if (utils.isURLSearchParams(params)) {
            serializedParams = params.toString()
        } else {
            var parts = []

            utils.forEach(params, function serialize(val, key) {
                if (val === null || typeof val === 'undefined') {
                    return
                }

                if (utils.isArray(val)) {
                    key = key + '[]'
                } else {
                    val = [val]
                }

                utils.forEach(val, function parseValue(v) {
                    if (utils.isDate(v)) {
                        v = v.toISOString()
                    } else if (utils.isObject(v)) {
                        v = JSON.stringify(v)
                    }
                    parts.push(encode(key) + '=' + encode(v))
                })
            })

            serializedParams = parts.join('&')
        }

        if (serializedParams) {
            var hashmarkIndex = url.indexOf('#')
            if (hashmarkIndex !== -1) {
                url = url.slice(0, hashmarkIndex)
            }

            url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams
        }

        return url
    }

    function InterceptorManager() {
        this.handlers = []
    }

    /**
     * Add a new interceptor to the stack
     *
     * @param {Function} fulfilled The function to handle `then` for a `Promise`
     * @param {Function} rejected The function to handle `reject` for a `Promise`
     *
     * @return {Number} An ID used to remove interceptor later
     */
    InterceptorManager.prototype.use = function use(fulfilled, rejected, options) {
        this.handlers.push({
            fulfilled: fulfilled,
            rejected: rejected,
            synchronous: options ? options.synchronous : false,
            runWhen: options ? options.runWhen : null,
        })
        return this.handlers.length - 1
    }

    /**
     * Remove an interceptor from the stack
     *
     * @param {Number} id The ID that was returned by `use`
     */
    InterceptorManager.prototype.eject = function eject(id) {
        if (this.handlers[id]) {
            this.handlers[id] = null
        }
    }

    /**
     * Iterate over all the registered interceptors
     *
     * This method is particularly useful for skipping over any
     * interceptors that may have become `null` calling `eject`.
     *
     * @param {Function} fn The function to call for each interceptor
     */
    InterceptorManager.prototype.forEach = function forEach(fn) {
        utils.forEach(this.handlers, function forEachHandler(h) {
            if (h !== null) {
                fn(h)
            }
        })
    }

    var InterceptorManager_1 = InterceptorManager

    var normalizeHeaderName = function normalizeHeaderName(headers, normalizedName) {
        utils.forEach(headers, function processHeader(value, name) {
            if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
                headers[normalizedName] = value
                delete headers[name]
            }
        })
    }

    /**
     * Update an Error with the specified config, error code, and response.
     *
     * @param {Error} error The error to update.
     * @param {Object} config The config.
     * @param {string} [code] The error code (for example, 'ECONNABORTED').
     * @param {Object} [request] The request.
     * @param {Object} [response] The response.
     * @returns {Error} The error.
     */
    var enhanceError = function enhanceError(error, config, code, request, response) {
        error.config = config
        if (code) {
            error.code = code
        }

        error.request = request
        error.response = response
        error.isAxiosError = true

        error.toJSON = function toJSON() {
            return {
                // Standard
                message: this.message,
                name: this.name,
                // Microsoft
                description: this.description,
                number: this.number,
                // Mozilla
                fileName: this.fileName,
                lineNumber: this.lineNumber,
                columnNumber: this.columnNumber,
                stack: this.stack,
                // Axios
                config: this.config,
                code: this.code,
            }
        }
        return error
    }

    /**
     * Create an Error with the specified message, config, error code, request and response.
     *
     * @param {string} message The error message.
     * @param {Object} config The config.
     * @param {string} [code] The error code (for example, 'ECONNABORTED').
     * @param {Object} [request] The request.
     * @param {Object} [response] The response.
     * @returns {Error} The created error.
     */
    var createError = function createError(message, config, code, request, response) {
        var error = new Error(message)
        return enhanceError(error, config, code, request, response)
    }

    /**
     * Resolve or reject a Promise based on response status.
     *
     * @param {Function} resolve A function that resolves the promise.
     * @param {Function} reject A function that rejects the promise.
     * @param {object} response The response.
     */
    var settle = function settle(resolve, reject, response) {
        var validateStatus = response.config.validateStatus
        if (!response.status || !validateStatus || validateStatus(response.status)) {
            resolve(response)
        } else {
            reject(
                createError(
                    'Request failed with status code ' + response.status,
                    response.config,
                    null,
                    response.request,
                    response,
                ),
            )
        }
    }

    var cookies = utils.isStandardBrowserEnv()
        ? // Standard browser envs support document.cookie
          (function standardBrowserEnv() {
              return {
                  write: function write(name, value, expires, path, domain, secure) {
                      var cookie = []
                      cookie.push(name + '=' + encodeURIComponent(value))

                      if (utils.isNumber(expires)) {
                          cookie.push('expires=' + new Date(expires).toGMTString())
                      }

                      if (utils.isString(path)) {
                          cookie.push('path=' + path)
                      }

                      if (utils.isString(domain)) {
                          cookie.push('domain=' + domain)
                      }

                      if (secure === true) {
                          cookie.push('secure')
                      }

                      document.cookie = cookie.join('; ')
                  },

                  read: function read(name) {
                      var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'))
                      return match ? decodeURIComponent(match[3]) : null
                  },

                  remove: function remove(name) {
                      this.write(name, '', Date.now() - 86400000)
                  },
              }
          })()
        : // Non standard browser env (web workers, react-native) lack needed support.
          (function nonStandardBrowserEnv() {
              return {
                  write: function write() {},
                  read: function read() {
                      return null
                  },
                  remove: function remove() {},
              }
          })()

    /**
     * Determines whether the specified URL is absolute
     *
     * @param {string} url The URL to test
     * @returns {boolean} True if the specified URL is absolute, otherwise false
     */
    var isAbsoluteURL = function isAbsoluteURL(url) {
        // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
        // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
        // by any combination of letters, digits, plus, period, or hyphen.
        return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url)
    }

    /**
     * Creates a new URL by combining the specified URLs
     *
     * @param {string} baseURL The base URL
     * @param {string} relativeURL The relative URL
     * @returns {string} The combined URL
     */
    var combineURLs = function combineURLs(baseURL, relativeURL) {
        return relativeURL ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '') : baseURL
    }

    /**
     * Creates a new URL by combining the baseURL with the requestedURL,
     * only when the requestedURL is not already an absolute URL.
     * If the requestURL is absolute, this function returns the requestedURL untouched.
     *
     * @param {string} baseURL The base URL
     * @param {string} requestedURL Absolute or relative URL to combine
     * @returns {string} The combined full path
     */
    var buildFullPath = function buildFullPath(baseURL, requestedURL) {
        if (baseURL && !isAbsoluteURL(requestedURL)) {
            return combineURLs(baseURL, requestedURL)
        }
        return requestedURL
    }

    // Headers whose duplicates are ignored by node
    // c.f. https://nodejs.org/api/http.html#http_message_headers
    var ignoreDuplicateOf = [
        'age',
        'authorization',
        'content-length',
        'content-type',
        'etag',
        'expires',
        'from',
        'host',
        'if-modified-since',
        'if-unmodified-since',
        'last-modified',
        'location',
        'max-forwards',
        'proxy-authorization',
        'referer',
        'retry-after',
        'user-agent',
    ]

    /**
     * Parse headers into an object
     *
     * ```
     * Date: Wed, 27 Aug 2014 08:58:49 GMT
     * Content-Type: application/json
     * Connection: keep-alive
     * Transfer-Encoding: chunked
     * ```
     *
     * @param {String} headers Headers needing to be parsed
     * @returns {Object} Headers parsed into an object
     */
    var parseHeaders = function parseHeaders(headers) {
        var parsed = {}
        var key
        var val
        var i

        if (!headers) {
            return parsed
        }

        utils.forEach(headers.split('\n'), function parser(line) {
            i = line.indexOf(':')
            key = utils.trim(line.substr(0, i)).toLowerCase()
            val = utils.trim(line.substr(i + 1))

            if (key) {
                if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
                    return
                }
                if (key === 'set-cookie') {
                    parsed[key] = (parsed[key] ? parsed[key] : []).concat([val])
                } else {
                    parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val
                }
            }
        })

        return parsed
    }

    var isURLSameOrigin = utils.isStandardBrowserEnv()
        ? // Standard browser envs have full support of the APIs needed to test
          // whether the request URL is of the same origin as current location.
          (function standardBrowserEnv() {
              var msie = /(msie|trident)/i.test(navigator.userAgent)
              var urlParsingNode = document.createElement('a')
              var originURL

              /**
               * Parse a URL to discover it's components
               *
               * @param {String} url The URL to be parsed
               * @returns {Object}
               */
              function resolveURL(url) {
                  var href = url

                  if (msie) {
                      // IE needs attribute set twice to normalize properties
                      urlParsingNode.setAttribute('href', href)
                      href = urlParsingNode.href
                  }

                  urlParsingNode.setAttribute('href', href)

                  // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
                  return {
                      href: urlParsingNode.href,
                      protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
                      host: urlParsingNode.host,
                      search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
                      hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
                      hostname: urlParsingNode.hostname,
                      port: urlParsingNode.port,
                      pathname:
                          urlParsingNode.pathname.charAt(0) === '/'
                              ? urlParsingNode.pathname
                              : '/' + urlParsingNode.pathname,
                  }
              }

              originURL = resolveURL(window.location.href)

              /**
               * Determine if a URL shares the same origin as the current location
               *
               * @param {String} requestURL The URL to test
               * @returns {boolean} True if URL shares the same origin, otherwise false
               */
              return function isURLSameOrigin(requestURL) {
                  var parsed = utils.isString(requestURL) ? resolveURL(requestURL) : requestURL
                  return parsed.protocol === originURL.protocol && parsed.host === originURL.host
              }
          })()
        : // Non standard browser envs (web workers, react-native) lack needed support.
          (function nonStandardBrowserEnv() {
              return function isURLSameOrigin() {
                  return true
              }
          })()

    var xhr = function xhrAdapter(config) {
        return new Promise(function dispatchXhrRequest(resolve, reject) {
            var requestData = config.data
            var requestHeaders = config.headers
            var responseType = config.responseType

            if (utils.isFormData(requestData)) {
                delete requestHeaders['Content-Type'] // Let the browser set it
            }

            var request = new XMLHttpRequest()

            // HTTP basic authentication
            if (config.auth) {
                var username = config.auth.username || ''
                var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : ''
                requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password)
            }

            var fullPath = buildFullPath(config.baseURL, config.url)
            request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true)

            // Set the request timeout in MS
            request.timeout = config.timeout

            function onloadend() {
                if (!request) {
                    return
                }
                // Prepare the response
                var responseHeaders =
                    'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null
                var responseData =
                    !responseType || responseType === 'text' || responseType === 'json'
                        ? request.responseText
                        : request.response
                var response = {
                    data: responseData,
                    status: request.status,
                    statusText: request.statusText,
                    headers: responseHeaders,
                    config: config,
                    request: request,
                }

                settle(resolve, reject, response)

                // Clean up request
                request = null
            }

            if ('onloadend' in request) {
                // Use onloadend if available
                request.onloadend = onloadend
            } else {
                // Listen for ready state to emulate onloadend
                request.onreadystatechange = function handleLoad() {
                    if (!request || request.readyState !== 4) {
                        return
                    }

                    // The request errored out and we didn't get a response, this will be
                    // handled by onerror instead
                    // With one exception: request that using file: protocol, most browsers
                    // will return status as 0 even though it's a successful request
                    if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
                        return
                    }
                    // readystate handler is calling before onerror or ontimeout handlers,
                    // so we should call onloadend on the next 'tick'
                    setTimeout(onloadend)
                }
            }

            // Handle browser request cancellation (as opposed to a manual cancellation)
            request.onabort = function handleAbort() {
                if (!request) {
                    return
                }

                reject(createError('Request aborted', config, 'ECONNABORTED', request))

                // Clean up request
                request = null
            }

            // Handle low level network errors
            request.onerror = function handleError() {
                // Real errors are hidden from us by the browser
                // onerror should only fire if it's a network error
                reject(createError('Network Error', config, null, request))

                // Clean up request
                request = null
            }

            // Handle timeout
            request.ontimeout = function handleTimeout() {
                var timeoutErrorMessage = 'timeout of ' + config.timeout + 'ms exceeded'
                if (config.timeoutErrorMessage) {
                    timeoutErrorMessage = config.timeoutErrorMessage
                }
                reject(
                    createError(
                        timeoutErrorMessage,
                        config,
                        config.transitional && config.transitional.clarifyTimeoutError ? 'ETIMEDOUT' : 'ECONNABORTED',
                        request,
                    ),
                )

                // Clean up request
                request = null
            }

            // Add xsrf header
            // This is only done if running in a standard browser environment.
            // Specifically not if we're in a web worker, or react-native.
            if (utils.isStandardBrowserEnv()) {
                // Add xsrf header
                var xsrfValue =
                    (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName
                        ? cookies.read(config.xsrfCookieName)
                        : undefined

                if (xsrfValue) {
                    requestHeaders[config.xsrfHeaderName] = xsrfValue
                }
            }

            // Add headers to the request
            if ('setRequestHeader' in request) {
                utils.forEach(requestHeaders, function setRequestHeader(val, key) {
                    if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
                        // Remove Content-Type if data is undefined
                        delete requestHeaders[key]
                    } else {
                        // Otherwise add header to the request
                        request.setRequestHeader(key, val)
                    }
                })
            }

            // Add withCredentials to request if needed
            if (!utils.isUndefined(config.withCredentials)) {
                request.withCredentials = !!config.withCredentials
            }

            // Add responseType to request if needed
            if (responseType && responseType !== 'json') {
                request.responseType = config.responseType
            }

            // Handle progress if needed
            if (typeof config.onDownloadProgress === 'function') {
                request.addEventListener('progress', config.onDownloadProgress)
            }

            // Not all browsers support upload events
            if (typeof config.onUploadProgress === 'function' && request.upload) {
                request.upload.addEventListener('progress', config.onUploadProgress)
            }

            if (config.cancelToken) {
                // Handle cancellation
                config.cancelToken.promise.then(function onCanceled(cancel) {
                    if (!request) {
                        return
                    }

                    request.abort()
                    reject(cancel)
                    // Clean up request
                    request = null
                })
            }

            if (!requestData) {
                requestData = null
            }

            // Send the request
            request.send(requestData)
        })
    }

    var DEFAULT_CONTENT_TYPE = {
        'Content-Type': 'application/x-www-form-urlencoded',
    }

    function setContentTypeIfUnset(headers, value) {
        if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
            headers['Content-Type'] = value
        }
    }

    function getDefaultAdapter() {
        var adapter
        if (typeof XMLHttpRequest !== 'undefined') {
            // For browsers use XHR adapter
            adapter = xhr
        } else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
            // For node use HTTP adapter
            adapter = xhr
        }
        return adapter
    }

    function stringifySafely(rawValue, parser, encoder) {
        if (utils.isString(rawValue)) {
            try {
                ;(parser || JSON.parse)(rawValue)
                return utils.trim(rawValue)
            } catch (e) {
                if (e.name !== 'SyntaxError') {
                    throw e
                }
            }
        }

        return (encoder || JSON.stringify)(rawValue)
    }

    var defaults = {
        transitional: {
            silentJSONParsing: true,
            forcedJSONParsing: true,
            clarifyTimeoutError: false,
        },

        adapter: getDefaultAdapter(),

        transformRequest: [
            function transformRequest(data, headers) {
                normalizeHeaderName(headers, 'Accept')
                normalizeHeaderName(headers, 'Content-Type')

                if (
                    utils.isFormData(data) ||
                    utils.isArrayBuffer(data) ||
                    utils.isBuffer(data) ||
                    utils.isStream(data) ||
                    utils.isFile(data) ||
                    utils.isBlob(data)
                ) {
                    return data
                }
                if (utils.isArrayBufferView(data)) {
                    return data.buffer
                }
                if (utils.isURLSearchParams(data)) {
                    setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8')
                    return data.toString()
                }
                if (utils.isObject(data) || (headers && headers['Content-Type'] === 'application/json')) {
                    setContentTypeIfUnset(headers, 'application/json')
                    return stringifySafely(data)
                }
                return data
            },
        ],

        transformResponse: [
            function transformResponse(data) {
                var transitional = this.transitional
                var silentJSONParsing = transitional && transitional.silentJSONParsing
                var forcedJSONParsing = transitional && transitional.forcedJSONParsing
                var strictJSONParsing = !silentJSONParsing && this.responseType === 'json'

                if (strictJSONParsing || (forcedJSONParsing && utils.isString(data) && data.length)) {
                    try {
                        return JSON.parse(data)
                    } catch (e) {
                        if (strictJSONParsing) {
                            if (e.name === 'SyntaxError') {
                                throw enhanceError(e, this, 'E_JSON_PARSE')
                            }
                            throw e
                        }
                    }
                }

                return data
            },
        ],

        /**
         * A timeout in milliseconds to abort a request. If set to 0 (default) a
         * timeout is not created.
         */
        timeout: 0,

        xsrfCookieName: 'XSRF-TOKEN',
        xsrfHeaderName: 'X-XSRF-TOKEN',

        maxContentLength: -1,
        maxBodyLength: -1,

        validateStatus: function validateStatus(status) {
            return status >= 200 && status < 300
        },
    }

    defaults.headers = {
        common: {
            Accept: 'application/json, text/plain, */*',
        },
    }

    utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
        defaults.headers[method] = {}
    })

    utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
        defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE)
    })

    var defaults_1 = defaults

    /**
     * Transform the data for a request or a response
     *
     * @param {Object|String} data The data to be transformed
     * @param {Array} headers The headers for the request or response
     * @param {Array|Function} fns A single function or Array of functions
     * @returns {*} The resulting transformed data
     */
    var transformData = function transformData(data, headers, fns) {
        var context = this || defaults_1
        /*eslint no-param-reassign:0*/
        utils.forEach(fns, function transform(fn) {
            data = fn.call(context, data, headers)
        })

        return data
    }

    var isCancel = function isCancel(value) {
        return !!(value && value.__CANCEL__)
    }

    /**
     * Throws a `Cancel` if cancellation has been requested.
     */
    function throwIfCancellationRequested(config) {
        if (config.cancelToken) {
            config.cancelToken.throwIfRequested()
        }
    }

    /**
     * Dispatch a request to the server using the configured adapter.
     *
     * @param {object} config The config that is to be used for the request
     * @returns {Promise} The Promise to be fulfilled
     */
    var dispatchRequest = function dispatchRequest(config) {
        throwIfCancellationRequested(config)

        // Ensure headers exist
        config.headers = config.headers || {}

        // Transform request data
        config.data = transformData.call(config, config.data, config.headers, config.transformRequest)

        // Flatten headers
        config.headers = utils.merge(config.headers.common || {}, config.headers[config.method] || {}, config.headers)

        utils.forEach(['delete', 'get', 'head', 'post', 'put', 'patch', 'common'], function cleanHeaderConfig(method) {
            delete config.headers[method]
        })

        var adapter = config.adapter || defaults_1.adapter

        return adapter(config).then(
            function onAdapterResolution(response) {
                throwIfCancellationRequested(config)

                // Transform response data
                response.data = transformData.call(config, response.data, response.headers, config.transformResponse)

                return response
            },
            function onAdapterRejection(reason) {
                if (!isCancel(reason)) {
                    throwIfCancellationRequested(config)

                    // Transform response data
                    if (reason && reason.response) {
                        reason.response.data = transformData.call(
                            config,
                            reason.response.data,
                            reason.response.headers,
                            config.transformResponse,
                        )
                    }
                }

                return Promise.reject(reason)
            },
        )
    }

    /**
     * Config-specific merge-function which creates a new config-object
     * by merging two configuration objects together.
     *
     * @param {Object} config1
     * @param {Object} config2
     * @returns {Object} New object resulting from merging config2 to config1
     */
    var mergeConfig = function mergeConfig(config1, config2) {
        // eslint-disable-next-line no-param-reassign
        config2 = config2 || {}
        var config = {}

        var valueFromConfig2Keys = ['url', 'method', 'data']
        var mergeDeepPropertiesKeys = ['headers', 'auth', 'proxy', 'params']
        var defaultToConfig2Keys = [
            'baseURL',
            'transformRequest',
            'transformResponse',
            'paramsSerializer',
            'timeout',
            'timeoutMessage',
            'withCredentials',
            'adapter',
            'responseType',
            'xsrfCookieName',
            'xsrfHeaderName',
            'onUploadProgress',
            'onDownloadProgress',
            'decompress',
            'maxContentLength',
            'maxBodyLength',
            'maxRedirects',
            'transport',
            'httpAgent',
            'httpsAgent',
            'cancelToken',
            'socketPath',
            'responseEncoding',
        ]
        var directMergeKeys = ['validateStatus']

        function getMergedValue(target, source) {
            if (utils.isPlainObject(target) && utils.isPlainObject(source)) {
                return utils.merge(target, source)
            } else if (utils.isPlainObject(source)) {
                return utils.merge({}, source)
            } else if (utils.isArray(source)) {
                return source.slice()
            }
            return source
        }

        function mergeDeepProperties(prop) {
            if (!utils.isUndefined(config2[prop])) {
                config[prop] = getMergedValue(config1[prop], config2[prop])
            } else if (!utils.isUndefined(config1[prop])) {
                config[prop] = getMergedValue(undefined, config1[prop])
            }
        }

        utils.forEach(valueFromConfig2Keys, function valueFromConfig2(prop) {
            if (!utils.isUndefined(config2[prop])) {
                config[prop] = getMergedValue(undefined, config2[prop])
            }
        })

        utils.forEach(mergeDeepPropertiesKeys, mergeDeepProperties)

        utils.forEach(defaultToConfig2Keys, function defaultToConfig2(prop) {
            if (!utils.isUndefined(config2[prop])) {
                config[prop] = getMergedValue(undefined, config2[prop])
            } else if (!utils.isUndefined(config1[prop])) {
                config[prop] = getMergedValue(undefined, config1[prop])
            }
        })

        utils.forEach(directMergeKeys, function merge(prop) {
            if (prop in config2) {
                config[prop] = getMergedValue(config1[prop], config2[prop])
            } else if (prop in config1) {
                config[prop] = getMergedValue(undefined, config1[prop])
            }
        })

        var axiosKeys = valueFromConfig2Keys
            .concat(mergeDeepPropertiesKeys)
            .concat(defaultToConfig2Keys)
            .concat(directMergeKeys)

        var otherKeys = Object.keys(config1)
            .concat(Object.keys(config2))
            .filter(function filterAxiosKeys(key) {
                return axiosKeys.indexOf(key) === -1
            })

        utils.forEach(otherKeys, mergeDeepProperties)

        return config
    }

    var name = 'axios'
    var version = '0.21.4'
    var description = 'Promise based HTTP client for the browser and node.js'
    var main = 'index.js'
    var scripts = {
        test: 'grunt test',
        start: 'node ./sandbox/server.js',
        build: 'NODE_ENV=production grunt build',
        preversion: 'npm test',
        version: 'npm run build && grunt version && git add -A dist && git add CHANGELOG.md bower.json package.json',
        postversion: 'git push && git push --tags',
        examples: 'node ./examples/server.js',
        coveralls: 'cat coverage/lcov.info | ./node_modules/coveralls/bin/coveralls.js',
        fix: 'eslint --fix lib/**/*.js',
    }
    var repository = {
        type: 'git',
        url: 'https://github.com/axios/axios.git',
    }
    var keywords = ['xhr', 'http', 'ajax', 'promise', 'node']
    var author = 'Matt Zabriskie'
    var license = 'MIT'
    var bugs = {
        url: 'https://github.com/axios/axios/issues',
    }
    var homepage = 'https://axios-http.com'
    var devDependencies = {
        coveralls: '^3.0.0',
        'es6-promise': '^4.2.4',
        grunt: '^1.3.0',
        'grunt-banner': '^0.6.0',
        'grunt-cli': '^1.2.0',
        'grunt-contrib-clean': '^1.1.0',
        'grunt-contrib-watch': '^1.0.0',
        'grunt-eslint': '^23.0.0',
        'grunt-karma': '^4.0.0',
        'grunt-mocha-test': '^0.13.3',
        'grunt-ts': '^6.0.0-beta.19',
        'grunt-webpack': '^4.0.2',
        'istanbul-instrumenter-loader': '^1.0.0',
        'jasmine-core': '^2.4.1',
        karma: '^6.3.2',
        'karma-chrome-launcher': '^3.1.0',
        'karma-firefox-launcher': '^2.1.0',
        'karma-jasmine': '^1.1.1',
        'karma-jasmine-ajax': '^0.1.13',
        'karma-safari-launcher': '^1.0.0',
        'karma-sauce-launcher': '^4.3.6',
        'karma-sinon': '^1.0.5',
        'karma-sourcemap-loader': '^0.3.8',
        'karma-webpack': '^4.0.2',
        'load-grunt-tasks': '^3.5.2',
        minimist: '^1.2.0',
        mocha: '^8.2.1',
        sinon: '^4.5.0',
        'terser-webpack-plugin': '^4.2.3',
        typescript: '^4.0.5',
        'url-search-params': '^0.10.0',
        webpack: '^4.44.2',
        'webpack-dev-server': '^3.11.0',
    }
    var browser = {
        './lib/adapters/http.js': './lib/adapters/xhr.js',
    }
    var jsdelivr = 'dist/axios.min.js'
    var unpkg = 'dist/axios.min.js'
    var typings = './index.d.ts'
    var dependencies = {
        'follow-redirects': '^1.14.0',
    }
    var bundlesize = [
        {
            path: './dist/axios.min.js',
            threshold: '5kB',
        },
    ]
    var pkg = {
        name: name,
        version: version,
        description: description,
        main: main,
        scripts: scripts,
        repository: repository,
        keywords: keywords,
        author: author,
        license: license,
        bugs: bugs,
        homepage: homepage,
        devDependencies: devDependencies,
        browser: browser,
        jsdelivr: jsdelivr,
        unpkg: unpkg,
        typings: typings,
        dependencies: dependencies,
        bundlesize: bundlesize,
    }

    var validators$1 = {}

    // eslint-disable-next-line func-names
    ;['object', 'boolean', 'number', 'function', 'string', 'symbol'].forEach(function (type, i) {
        validators$1[type] = function validator(thing) {
            return typeof thing === type || 'a' + (i < 1 ? 'n ' : ' ') + type
        }
    })

    var deprecatedWarnings = {}
    var currentVerArr = pkg.version.split('.')

    /**
     * Compare package versions
     * @param {string} version
     * @param {string?} thanVersion
     * @returns {boolean}
     */
    function isOlderVersion(version, thanVersion) {
        var pkgVersionArr = thanVersion ? thanVersion.split('.') : currentVerArr
        var destVer = version.split('.')
        for (var i = 0; i < 3; i++) {
            if (pkgVersionArr[i] > destVer[i]) {
                return true
            } else if (pkgVersionArr[i] < destVer[i]) {
                return false
            }
        }
        return false
    }

    /**
     * Transitional option validator
     * @param {function|boolean?} validator
     * @param {string?} version
     * @param {string} message
     * @returns {function}
     */
    validators$1.transitional = function transitional(validator, version, message) {
        var isDeprecated = version && isOlderVersion(version)

        function formatMessage(opt, desc) {
            return (
                '[Axios v' +
                pkg.version +
                "] Transitional option '" +
                opt +
                "'" +
                desc +
                (message ? '. ' + message : '')
            )
        }

        // eslint-disable-next-line func-names
        return function (value, opt, opts) {
            if (validator === false) {
                throw new Error(formatMessage(opt, ' has been removed in ' + version))
            }

            if (isDeprecated && !deprecatedWarnings[opt]) {
                deprecatedWarnings[opt] = true
                // eslint-disable-next-line no-console
                console.warn(
                    formatMessage(
                        opt,
                        ' has been deprecated since v' + version + ' and will be removed in the near future',
                    ),
                )
            }

            return validator ? validator(value, opt, opts) : true
        }
    }

    /**
     * Assert object's properties type
     * @param {object} options
     * @param {object} schema
     * @param {boolean?} allowUnknown
     */

    function assertOptions(options, schema, allowUnknown) {
        if (typeof options !== 'object') {
            throw new TypeError('options must be an object')
        }
        var keys = Object.keys(options)
        var i = keys.length
        while (i-- > 0) {
            var opt = keys[i]
            var validator = schema[opt]
            if (validator) {
                var value = options[opt]
                var result = value === undefined || validator(value, opt, options)
                if (result !== true) {
                    throw new TypeError('option ' + opt + ' must be ' + result)
                }
                continue
            }
            if (allowUnknown !== true) {
                throw Error('Unknown option ' + opt)
            }
        }
    }

    var validator = {
        isOlderVersion: isOlderVersion,
        assertOptions: assertOptions,
        validators: validators$1,
    }

    var validators = validator.validators
    /**
     * Create a new instance of Axios
     *
     * @param {Object} instanceConfig The default config for the instance
     */
    function Axios$1(instanceConfig) {
        this.defaults = instanceConfig
        this.interceptors = {
            request: new InterceptorManager_1(),
            response: new InterceptorManager_1(),
        }
    }

    /**
     * Dispatch a request
     *
     * @param {Object} config The config specific for this request (merged with this.defaults)
     */
    Axios$1.prototype.request = function request(config) {
        /*eslint no-param-reassign:0*/
        // Allow for axios('example/url'[, config]) a la fetch API
        if (typeof config === 'string') {
            config = arguments[1] || {}
            config.url = arguments[0]
        } else {
            config = config || {}
        }

        config = mergeConfig(this.defaults, config)

        // Set config.method
        if (config.method) {
            config.method = config.method.toLowerCase()
        } else if (this.defaults.method) {
            config.method = this.defaults.method.toLowerCase()
        } else {
            config.method = 'get'
        }

        var transitional = config.transitional

        if (transitional !== undefined) {
            validator.assertOptions(
                transitional,
                {
                    silentJSONParsing: validators.transitional(validators.boolean, '1.0.0'),
                    forcedJSONParsing: validators.transitional(validators.boolean, '1.0.0'),
                    clarifyTimeoutError: validators.transitional(validators.boolean, '1.0.0'),
                },
                false,
            )
        }

        // filter out skipped interceptors
        var requestInterceptorChain = []
        var synchronousRequestInterceptors = true
        this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
            if (typeof interceptor.runWhen === 'function' && interceptor.runWhen(config) === false) {
                return
            }

            synchronousRequestInterceptors = synchronousRequestInterceptors && interceptor.synchronous

            requestInterceptorChain.unshift(interceptor.fulfilled, interceptor.rejected)
        })

        var responseInterceptorChain = []
        this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
            responseInterceptorChain.push(interceptor.fulfilled, interceptor.rejected)
        })

        var promise

        if (!synchronousRequestInterceptors) {
            var chain = [dispatchRequest, undefined]

            Array.prototype.unshift.apply(chain, requestInterceptorChain)
            chain = chain.concat(responseInterceptorChain)

            promise = Promise.resolve(config)
            while (chain.length) {
                promise = promise.then(chain.shift(), chain.shift())
            }

            return promise
        }

        var newConfig = config
        while (requestInterceptorChain.length) {
            var onFulfilled = requestInterceptorChain.shift()
            var onRejected = requestInterceptorChain.shift()
            try {
                newConfig = onFulfilled(newConfig)
            } catch (error) {
                onRejected(error)
                break
            }
        }

        try {
            promise = dispatchRequest(newConfig)
        } catch (error) {
            return Promise.reject(error)
        }

        while (responseInterceptorChain.length) {
            promise = promise.then(responseInterceptorChain.shift(), responseInterceptorChain.shift())
        }

        return promise
    }

    Axios$1.prototype.getUri = function getUri(config) {
        config = mergeConfig(this.defaults, config)
        return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '')
    }

    // Provide aliases for supported request methods
    utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
        /*eslint func-names:0*/
        Axios$1.prototype[method] = function (url, config) {
            return this.request(
                mergeConfig(config || {}, {
                    method: method,
                    url: url,
                    data: (config || {}).data,
                }),
            )
        }
    })

    utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
        /*eslint func-names:0*/
        Axios$1.prototype[method] = function (url, data, config) {
            return this.request(
                mergeConfig(config || {}, {
                    method: method,
                    url: url,
                    data: data,
                }),
            )
        }
    })

    var Axios_1 = Axios$1

    /**
     * A `Cancel` is an object that is thrown when an operation is canceled.
     *
     * @class
     * @param {string=} message The message.
     */
    function Cancel(message) {
        this.message = message
    }

    Cancel.prototype.toString = function toString() {
        return 'Cancel' + (this.message ? ': ' + this.message : '')
    }

    Cancel.prototype.__CANCEL__ = true

    var Cancel_1 = Cancel

    /**
     * A `CancelToken` is an object that can be used to request cancellation of an operation.
     *
     * @class
     * @param {Function} executor The executor function.
     */
    function CancelToken(executor) {
        if (typeof executor !== 'function') {
            throw new TypeError('executor must be a function.')
        }

        var resolvePromise
        this.promise = new Promise(function promiseExecutor(resolve) {
            resolvePromise = resolve
        })

        var token = this
        executor(function cancel(message) {
            if (token.reason) {
                // Cancellation has already been requested
                return
            }

            token.reason = new Cancel_1(message)
            resolvePromise(token.reason)
        })
    }

    /**
     * Throws a `Cancel` if cancellation has been requested.
     */
    CancelToken.prototype.throwIfRequested = function throwIfRequested() {
        if (this.reason) {
            throw this.reason
        }
    }

    /**
     * Returns an object that contains a new `CancelToken` and a function that, when called,
     * cancels the `CancelToken`.
     */
    CancelToken.source = function source() {
        var cancel
        var token = new CancelToken(function executor(c) {
            cancel = c
        })
        return {
            token: token,
            cancel: cancel,
        }
    }

    var CancelToken_1 = CancelToken

    /**
     * Syntactic sugar for invoking a function and expanding an array for arguments.
     *
     * Common use case would be to use `Function.prototype.apply`.
     *
     *  ```js
     *  function f(x, y, z) {}
     *  var args = [1, 2, 3];
     *  f.apply(null, args);
     *  ```
     *
     * With `spread` this example can be re-written.
     *
     *  ```js
     *  spread(function(x, y, z) {})([1, 2, 3]);
     *  ```
     *
     * @param {Function} callback
     * @returns {Function}
     */
    var spread = function spread(callback) {
        return function wrap(arr) {
            return callback.apply(null, arr)
        }
    }

    /**
     * Determines whether the payload is an error thrown by Axios
     *
     * @param {*} payload The value to test
     * @returns {boolean} True if the payload is an error thrown by Axios, otherwise false
     */
    var isAxiosError = function isAxiosError(payload) {
        return typeof payload === 'object' && payload.isAxiosError === true
    }

    /**
     * Create an instance of Axios
     *
     * @param {Object} defaultConfig The default config for the instance
     * @return {Axios} A new instance of Axios
     */
    function createInstance(defaultConfig) {
        var context = new Axios_1(defaultConfig)
        var instance = bind(Axios_1.prototype.request, context)

        // Copy axios.prototype to instance
        utils.extend(instance, Axios_1.prototype, context)

        // Copy context to instance
        utils.extend(instance, context)

        return instance
    }

    // Create the default instance to be exported
    var axios$1 = createInstance(defaults_1)

    // Expose Axios class to allow class inheritance
    axios$1.Axios = Axios_1

    // Factory for creating new instances
    axios$1.create = function create(instanceConfig) {
        return createInstance(mergeConfig(axios$1.defaults, instanceConfig))
    }

    // Expose Cancel & CancelToken
    axios$1.Cancel = Cancel_1
    axios$1.CancelToken = CancelToken_1
    axios$1.isCancel = isCancel

    // Expose all/spread
    axios$1.all = function all(promises) {
        return Promise.all(promises)
    }
    axios$1.spread = spread

    // Expose isAxiosError
    axios$1.isAxiosError = isAxiosError

    var axios_1 = axios$1

    // Allow use of default import syntax in TypeScript
    var _default = axios$1
    axios_1.default = _default

    var axios = axios_1

    var Axios = axios.create({
        baseURL: window.metagalleryData.root,
        headers: {
            'X-WP-Nonce': window.metagalleryData.nonce,
            'X-Requested-With': 'XMLHttpRequest',
        },
    })

    // Note, this is a slow refactor so might appear incomplete
    var Gallery = {
        all: function all() {
            return Axios.get('gallery', {
                params: {},
            })
        },
        create: function create() {
            return Axios.post('gallery', {
                params: {},
            })
        },
        save: function save(id, title, images, settings) {
            var formData = new FormData()
            formData.append('title', title)
            formData.append('images', JSON.stringify(images))
            formData.append('settings', JSON.stringify(settings))
            return Axios.post('gallery/' + id, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            })
        },
    }

    // TODO: The "dirty" is just set on each update method but could also be more dynamic
    function Current(data) {
        return {
            data: data,
            title: '',
            dirty: false,
            saving: false,
            images: [
                {
                    _uid: 0,
                    height: 0,
                    width: 0,
                    title: '',
                    alt: '',
                    caption: '',
                },
            ],
            settings: {
                maxImageWidth: '600',
                minImageWidth: '315',
                percentImageWidth: '25',
                imageSpacing: '15',
            },
            init: function init() {
                this.title = this.data.meta.title
                this.images = this.data.meta.images
                this.settings = Object.assign(this.settings, this.data.meta.settings)
            },
            save: async function save() {
                console.log('MetaGallery: Saving...')
                await new Promise(function (resolve) {
                    return setTimeout(resolve, 250)
                })

                // Setup image order
                if (window.metagalleryGrid) {
                    this.updateImageOrder(window.metagalleryGrid.getItems())
                }

                // Reset state
                this.saving = true
                this.dirty = false
                await Gallery.save(this.data.ID, this.title, this.images, this.settings)
                await new Promise(function (resolve) {
                    return setTimeout(resolve, 1500)
                })
                this.saving = false
            },
            updateTitle: function updateTitle(title) {
                console.log('MetaGallery: Updating title to:', title)
                this.dirty = true
                this.title = title
            },
            updateSetting: function updateSetting(setting, value) {
                console.log('MetaGallery: Updating ' + setting + ' to:', value)
                this.dirty = true
                // Currently, no settings can be less than 0
                this.settings[setting] = parseInt(value, 10) < 0 ? 0 : value
                this.updateLayout()
            },
            updateImageSetting: function updateImageSetting(imageId, setting, value) {
                console.log('MetaGallery: Updating image ' + imageId + ' ' + setting + ' to:', value)
                this.dirty = true
                var image = this.images.find(function (i) {
                    return i._uid == imageId
                })
                image.alt = value
                this.updateLayout()
            },
            // getImageSetting(imageId, setting) {
            //     if (!imageId) return ''
            //     let image = this.images.find((i) => i._uid == imageId)
            //     return image[setting]
            // },
            addImages: function addImages(images) {
                var _this$images
                console.log('MetaGallery: Adding ' + images.length + ' ' + (images.length > 1 ? 'images' : 'image'))
                this.dirty = true
                ;(_this$images = this.images).push.apply(_this$images, images)
                window.dispatchEvent(
                    new CustomEvent('metagallery-images-added', {
                        detail: {
                            images: images,
                        },
                        bubbles: true,
                    }),
                )
            },
            updateImageOrder: function updateImageOrder(items) {
                var _this = this
                items = items.map(function (item) {
                    return item.getElement().querySelector('[x-data]').__x.getUnobservedData()._uid
                })
                this.images = items.reduce(function (newitems, item, index) {
                    newitems[index] = _this.images.find(function (i) {
                        return i._uid == item
                    })
                    return newitems
                }, [])
            },
            updateLayout: function updateLayout() {
                setTimeout(function () {
                    window.dispatchEvent(
                        new CustomEvent('reset-layout', {
                            detail: {},
                            bubbles: true,
                        }),
                    )
                }, 0)
            },
        }
    }

    /**
     * Muuri v0.9.5
     * https://muuri.dev/
     * Copyright (c) 2015-present, Haltu Oy
     * Released under the MIT license
     * https://github.com/haltu/muuri/blob/master/LICENSE.md
     * @license MIT
     *
     * Muuri Packer
     * Copyright (c) 2016-present, Niklas Rämö <inramo@gmail.com>
     * @license MIT
     *
     * Muuri Ticker / Muuri Emitter / Muuri Dragger
     * Copyright (c) 2018-present, Niklas Rämö <inramo@gmail.com>
     * @license MIT
     *
     * Muuri AutoScroller
     * Copyright (c) 2019-present, Niklas Rämö <inramo@gmail.com>
     * @license MIT
     */

    var GRID_INSTANCES = {}
    var ITEM_ELEMENT_MAP = typeof Map === 'function' ? new Map() : null

    var ACTION_SWAP = 'swap'
    var ACTION_MOVE = 'move'

    var EVENT_SYNCHRONIZE = 'synchronize'
    var EVENT_LAYOUT_START = 'layoutStart'
    var EVENT_LAYOUT_END = 'layoutEnd'
    var EVENT_LAYOUT_ABORT = 'layoutAbort'
    var EVENT_ADD = 'add'
    var EVENT_REMOVE = 'remove'
    var EVENT_SHOW_START = 'showStart'
    var EVENT_SHOW_END = 'showEnd'
    var EVENT_HIDE_START = 'hideStart'
    var EVENT_HIDE_END = 'hideEnd'
    var EVENT_FILTER = 'filter'
    var EVENT_SORT = 'sort'
    var EVENT_MOVE = 'move'
    var EVENT_SEND = 'send'
    var EVENT_BEFORE_SEND = 'beforeSend'
    var EVENT_RECEIVE = 'receive'
    var EVENT_BEFORE_RECEIVE = 'beforeReceive'
    var EVENT_DRAG_INIT = 'dragInit'
    var EVENT_DRAG_START = 'dragStart'
    var EVENT_DRAG_MOVE = 'dragMove'
    var EVENT_DRAG_SCROLL = 'dragScroll'
    var EVENT_DRAG_END = 'dragEnd'
    var EVENT_DRAG_RELEASE_START = 'dragReleaseStart'
    var EVENT_DRAG_RELEASE_END = 'dragReleaseEnd'
    var EVENT_DESTROY = 'destroy'

    var HAS_TOUCH_EVENTS = 'ontouchstart' in window
    var HAS_POINTER_EVENTS = !!window.PointerEvent
    var HAS_MS_POINTER_EVENTS = !!window.navigator.msPointerEnabled

    var MAX_SAFE_FLOAT32_INTEGER = 16777216

    /**
     * Event emitter constructor.
     *
     * @class
     */
    function Emitter() {
        this._events = {}
        this._queue = []
        this._counter = 0
        this._clearOnEmit = false
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Bind an event listener.
     *
     * @public
     * @param {String} event
     * @param {Function} listener
     * @returns {Emitter}
     */
    Emitter.prototype.on = function (event, listener) {
        if (!this._events || !event || !listener) return this

        // Get listeners queue and create it if it does not exist.
        var listeners = this._events[event]
        if (!listeners) listeners = this._events[event] = []

        // Add the listener to the queue.
        listeners.push(listener)

        return this
    }

    /**
     * Unbind all event listeners that match the provided listener function.
     *
     * @public
     * @param {String} event
     * @param {Function} listener
     * @returns {Emitter}
     */
    Emitter.prototype.off = function (event, listener) {
        if (!this._events || !event || !listener) return this

        // Get listeners and return immediately if none is found.
        var listeners = this._events[event]
        if (!listeners || !listeners.length) return this

        // Remove all matching listeners.
        var index
        while ((index = listeners.indexOf(listener)) !== -1) {
            listeners.splice(index, 1)
        }

        return this
    }

    /**
     * Unbind all listeners of the provided event.
     *
     * @public
     * @param {String} event
     * @returns {Emitter}
     */
    Emitter.prototype.clear = function (event) {
        if (!this._events || !event) return this

        var listeners = this._events[event]
        if (listeners) {
            listeners.length = 0
            delete this._events[event]
        }

        return this
    }

    /**
     * Emit all listeners in a specified event with the provided arguments.
     *
     * @public
     * @param {String} event
     * @param {...*} [args]
     * @returns {Emitter}
     */
    Emitter.prototype.emit = function (event) {
        if (!this._events || !event) {
            this._clearOnEmit = false
            return this
        }

        // Get event listeners and quit early if there's no listeners.
        var listeners = this._events[event]
        if (!listeners || !listeners.length) {
            this._clearOnEmit = false
            return this
        }

        var queue = this._queue
        var startIndex = queue.length
        var argsLength = arguments.length - 1
        var args

        // If we have more than 3 arguments let's put the arguments in an array and
        // apply it to the listeners.
        if (argsLength > 3) {
            args = []
            args.push.apply(args, arguments)
            args.shift()
        }

        // Add the current listeners to the callback queue before we process them.
        // This is necessary to guarantee that all of the listeners are called in
        // correct order even if new event listeners are removed/added during
        // processing and/or events are emitted during processing.
        queue.push.apply(queue, listeners)

        // Reset the event's listeners if need be.
        if (this._clearOnEmit) {
            listeners.length = 0
            this._clearOnEmit = false
        }

        // Increment queue counter. This is needed for the scenarios where emit is
        // triggered while the queue is already processing. We need to keep track of
        // how many "queue processors" there are active so that we can safely reset
        // the queue in the end when the last queue processor is finished.
        ++this._counter

        // Process the queue (the specific part of it for this emit).
        var i = startIndex
        var endIndex = queue.length
        for (; i < endIndex; i++) {
            // prettier-ignore
            argsLength === 0 ? queue[i]() :
	    argsLength === 1 ? queue[i](arguments[1]) :
	    argsLength === 2 ? queue[i](arguments[1], arguments[2]) :
	    argsLength === 3 ? queue[i](arguments[1], arguments[2], arguments[3]) :
	                       queue[i].apply(null, args);

            // Stop processing if the emitter is destroyed.
            if (!this._events) return this
        }

        // Decrement queue process counter.
        --this._counter

        // Reset the queue if there are no more queue processes running.
        if (!this._counter) queue.length = 0

        return this
    }

    /**
     * Emit all listeners in a specified event with the provided arguments and
     * remove the event's listeners just before calling the them. This method allows
     * the emitter to serve as a queue where all listeners are called only once.
     *
     * @public
     * @param {String} event
     * @param {...*} [args]
     * @returns {Emitter}
     */
    Emitter.prototype.burst = function () {
        if (!this._events) return this
        this._clearOnEmit = true
        this.emit.apply(this, arguments)
        return this
    }

    /**
     * Check how many listeners there are for a specific event.
     *
     * @public
     * @param {String} event
     * @returns {Boolean}
     */
    Emitter.prototype.countListeners = function (event) {
        if (!this._events) return 0
        var listeners = this._events[event]
        return listeners ? listeners.length : 0
    }

    /**
     * Destroy emitter instance. Basically just removes all bound listeners.
     *
     * @public
     * @returns {Emitter}
     */
    Emitter.prototype.destroy = function () {
        if (!this._events) return this
        this._queue.length = this._counter = 0
        this._events = null
        return this
    }

    var pointerout = HAS_POINTER_EVENTS ? 'pointerout' : HAS_MS_POINTER_EVENTS ? 'MSPointerOut' : ''
    var waitDuration = 100

    /**
     * If you happen to use Edge or IE on a touch capable device there is a
     * a specific case where pointercancel and pointerend events are never emitted,
     * even though one them should always be emitted when you release your finger
     * from the screen. The bug appears specifically when Muuri shifts the dragged
     * element's position in the DOM after pointerdown event, IE and Edge don't like
     * that behaviour and quite often forget to emit the pointerend/pointercancel
     * event. But, they do emit pointerout event so we utilize that here.
     * Specifically, if there has been no pointermove event within 100 milliseconds
     * since the last pointerout event we force cancel the drag operation. This hack
     * works surprisingly well 99% of the time. There is that 1% chance there still
     * that dragged items get stuck but it is what it is.
     *
     * @class
     * @param {Dragger} dragger
     */
    function EdgeHack(dragger) {
        if (!pointerout) return

        this._dragger = dragger
        this._timeout = null
        this._outEvent = null
        this._isActive = false

        this._addBehaviour = this._addBehaviour.bind(this)
        this._removeBehaviour = this._removeBehaviour.bind(this)
        this._onTimeout = this._onTimeout.bind(this)
        this._resetData = this._resetData.bind(this)
        this._onStart = this._onStart.bind(this)
        this._onOut = this._onOut.bind(this)

        this._dragger.on('start', this._onStart)
    }

    /**
     * @private
     */
    EdgeHack.prototype._addBehaviour = function () {
        if (this._isActive) return
        this._isActive = true
        this._dragger.on('move', this._resetData)
        this._dragger.on('cancel', this._removeBehaviour)
        this._dragger.on('end', this._removeBehaviour)
        window.addEventListener(pointerout, this._onOut)
    }

    /**
     * @private
     */
    EdgeHack.prototype._removeBehaviour = function () {
        if (!this._isActive) return
        this._dragger.off('move', this._resetData)
        this._dragger.off('cancel', this._removeBehaviour)
        this._dragger.off('end', this._removeBehaviour)
        window.removeEventListener(pointerout, this._onOut)
        this._resetData()
        this._isActive = false
    }

    /**
     * @private
     */
    EdgeHack.prototype._resetData = function () {
        window.clearTimeout(this._timeout)
        this._timeout = null
        this._outEvent = null
    }

    /**
     * @private
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     */
    EdgeHack.prototype._onStart = function (e) {
        if (e.pointerType === 'mouse') return
        this._addBehaviour()
    }

    /**
     * @private
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     */
    EdgeHack.prototype._onOut = function (e) {
        if (!this._dragger._getTrackedTouch(e)) return
        this._resetData()
        this._outEvent = e
        this._timeout = window.setTimeout(this._onTimeout, waitDuration)
    }

    /**
     * @private
     */
    EdgeHack.prototype._onTimeout = function () {
        var e = this._outEvent
        this._resetData()
        if (this._dragger.isActive()) this._dragger._onCancel(e)
    }

    /**
     * @public
     */
    EdgeHack.prototype.destroy = function () {
        if (!pointerout) return
        this._dragger.off('start', this._onStart)
        this._removeBehaviour()
    }

    // Playing it safe here, test all potential prefixes capitalized and lowercase.
    var vendorPrefixes = ['', 'webkit', 'moz', 'ms', 'o', 'Webkit', 'Moz', 'MS', 'O']
    var cache$2 = {}

    /**
     * Get prefixed CSS property name when given a non-prefixed CSS property name.
     * Returns null if the property is not supported at all.
     *
     * @param {CSSStyleDeclaration} style
     * @param {String} prop
     * @returns {String}
     */
    function getPrefixedPropName(style, prop) {
        var prefixedProp = cache$2[prop] || ''
        if (prefixedProp) return prefixedProp

        var camelProp = prop[0].toUpperCase() + prop.slice(1)
        var i = 0
        while (i < vendorPrefixes.length) {
            prefixedProp = vendorPrefixes[i] ? vendorPrefixes[i] + camelProp : prop
            if (prefixedProp in style) {
                cache$2[prop] = prefixedProp
                return prefixedProp
            }
            ++i
        }

        return ''
    }

    /**
     * Check if passive events are supported.
     * https://github.com/WICG/EventListenerOptions/blob/gh-pages/explainer.md#feature-detection
     *
     * @returns {Boolean}
     */
    function hasPassiveEvents() {
        var isPassiveEventsSupported = false

        try {
            var passiveOpts = Object.defineProperty({}, 'passive', {
                get: function () {
                    isPassiveEventsSupported = true
                },
            })
            window.addEventListener('testPassive', null, passiveOpts)
            window.removeEventListener('testPassive', null, passiveOpts)
        } catch (e) {}

        return isPassiveEventsSupported
    }

    var ua = window.navigator.userAgent.toLowerCase()
    var isEdge = ua.indexOf('edge') > -1
    var isIE = ua.indexOf('trident') > -1
    var isFirefox = ua.indexOf('firefox') > -1
    var isAndroid = ua.indexOf('android') > -1

    var listenerOptions = hasPassiveEvents() ? { passive: true } : false

    var taProp = 'touchAction'
    var taPropPrefixed = getPrefixedPropName(document.documentElement.style, taProp)
    var taDefaultValue = 'auto'

    /**
     * Creates a new Dragger instance for an element.
     *
     * @public
     * @class
     * @param {HTMLElement} element
     * @param {Object} [cssProps]
     */
    function Dragger(element, cssProps) {
        this._element = element
        this._emitter = new Emitter()
        this._isDestroyed = false
        this._cssProps = {}
        this._touchAction = ''
        this._isActive = false

        this._pointerId = null
        this._startTime = 0
        this._startX = 0
        this._startY = 0
        this._currentX = 0
        this._currentY = 0

        this._onStart = this._onStart.bind(this)
        this._onMove = this._onMove.bind(this)
        this._onCancel = this._onCancel.bind(this)
        this._onEnd = this._onEnd.bind(this)

        // Can't believe had to build a freaking class for a hack!
        this._edgeHack = null
        if ((isEdge || isIE) && (HAS_POINTER_EVENTS || HAS_MS_POINTER_EVENTS)) {
            this._edgeHack = new EdgeHack(this)
        }

        // Apply initial CSS props.
        this.setCssProps(cssProps)

        // If touch action was not provided with initial CSS props let's assume it's
        // auto.
        if (!this._touchAction) {
            this.setTouchAction(taDefaultValue)
        }

        // Prevent native link/image dragging for the item and it's children.
        element.addEventListener('dragstart', Dragger._preventDefault, false)

        // Listen to start event.
        element.addEventListener(Dragger._inputEvents.start, this._onStart, listenerOptions)
    }

    /**
     * Protected properties
     * ********************
     */

    Dragger._pointerEvents = {
        start: 'pointerdown',
        move: 'pointermove',
        cancel: 'pointercancel',
        end: 'pointerup',
    }

    Dragger._msPointerEvents = {
        start: 'MSPointerDown',
        move: 'MSPointerMove',
        cancel: 'MSPointerCancel',
        end: 'MSPointerUp',
    }

    Dragger._touchEvents = {
        start: 'touchstart',
        move: 'touchmove',
        cancel: 'touchcancel',
        end: 'touchend',
    }

    Dragger._mouseEvents = {
        start: 'mousedown',
        move: 'mousemove',
        cancel: '',
        end: 'mouseup',
    }

    Dragger._inputEvents = (function () {
        if (HAS_TOUCH_EVENTS) return Dragger._touchEvents
        if (HAS_POINTER_EVENTS) return Dragger._pointerEvents
        if (HAS_MS_POINTER_EVENTS) return Dragger._msPointerEvents
        return Dragger._mouseEvents
    })()

    Dragger._emitter = new Emitter()

    Dragger._emitterEvents = {
        start: 'start',
        move: 'move',
        end: 'end',
        cancel: 'cancel',
    }

    Dragger._activeInstances = []

    /**
     * Protected static methods
     * ************************
     */

    Dragger._preventDefault = function (e) {
        if (e.preventDefault && e.cancelable !== false) e.preventDefault()
    }

    Dragger._activateInstance = function (instance) {
        var index = Dragger._activeInstances.indexOf(instance)
        if (index > -1) return

        Dragger._activeInstances.push(instance)
        Dragger._emitter.on(Dragger._emitterEvents.move, instance._onMove)
        Dragger._emitter.on(Dragger._emitterEvents.cancel, instance._onCancel)
        Dragger._emitter.on(Dragger._emitterEvents.end, instance._onEnd)

        if (Dragger._activeInstances.length === 1) {
            Dragger._bindListeners()
        }
    }

    Dragger._deactivateInstance = function (instance) {
        var index = Dragger._activeInstances.indexOf(instance)
        if (index === -1) return

        Dragger._activeInstances.splice(index, 1)
        Dragger._emitter.off(Dragger._emitterEvents.move, instance._onMove)
        Dragger._emitter.off(Dragger._emitterEvents.cancel, instance._onCancel)
        Dragger._emitter.off(Dragger._emitterEvents.end, instance._onEnd)

        if (!Dragger._activeInstances.length) {
            Dragger._unbindListeners()
        }
    }

    Dragger._bindListeners = function () {
        window.addEventListener(Dragger._inputEvents.move, Dragger._onMove, listenerOptions)
        window.addEventListener(Dragger._inputEvents.end, Dragger._onEnd, listenerOptions)
        if (Dragger._inputEvents.cancel) {
            window.addEventListener(Dragger._inputEvents.cancel, Dragger._onCancel, listenerOptions)
        }
    }

    Dragger._unbindListeners = function () {
        window.removeEventListener(Dragger._inputEvents.move, Dragger._onMove, listenerOptions)
        window.removeEventListener(Dragger._inputEvents.end, Dragger._onEnd, listenerOptions)
        if (Dragger._inputEvents.cancel) {
            window.removeEventListener(Dragger._inputEvents.cancel, Dragger._onCancel, listenerOptions)
        }
    }

    Dragger._getEventPointerId = function (event) {
        // If we have pointer id available let's use it.
        if (typeof event.pointerId === 'number') {
            return event.pointerId
        }

        // For touch events let's get the first changed touch's identifier.
        if (event.changedTouches) {
            return event.changedTouches[0] ? event.changedTouches[0].identifier : null
        }

        // For mouse/other events let's provide a static id.
        return 1
    }

    Dragger._getTouchById = function (event, id) {
        // If we have a pointer event return the whole event if there's a match, and
        // null otherwise.
        if (typeof event.pointerId === 'number') {
            return event.pointerId === id ? event : null
        }

        // For touch events let's check if there's a changed touch object that matches
        // the pointerId in which case return the touch object.
        if (event.changedTouches) {
            for (var i = 0; i < event.changedTouches.length; i++) {
                if (event.changedTouches[i].identifier === id) {
                    return event.changedTouches[i]
                }
            }
            return null
        }

        // For mouse/other events let's assume there's only one pointer and just
        // return the event.
        return event
    }

    Dragger._onMove = function (e) {
        Dragger._emitter.emit(Dragger._emitterEvents.move, e)
    }

    Dragger._onCancel = function (e) {
        Dragger._emitter.emit(Dragger._emitterEvents.cancel, e)
    }

    Dragger._onEnd = function (e) {
        Dragger._emitter.emit(Dragger._emitterEvents.end, e)
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Reset current drag operation (if any).
     *
     * @private
     */
    Dragger.prototype._reset = function () {
        this._pointerId = null
        this._startTime = 0
        this._startX = 0
        this._startY = 0
        this._currentX = 0
        this._currentY = 0
        this._isActive = false
        Dragger._deactivateInstance(this)
    }

    /**
     * Create a custom dragger event from a raw event.
     *
     * @private
     * @param {String} type
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     * @returns {Object}
     */
    Dragger.prototype._createEvent = function (type, e) {
        var touch = this._getTrackedTouch(e)
        return {
            // Hammer.js compatibility interface.
            type: type,
            srcEvent: e,
            distance: this.getDistance(),
            deltaX: this.getDeltaX(),
            deltaY: this.getDeltaY(),
            deltaTime: type === Dragger._emitterEvents.start ? 0 : this.getDeltaTime(),
            isFirst: type === Dragger._emitterEvents.start,
            isFinal: type === Dragger._emitterEvents.end || type === Dragger._emitterEvents.cancel,
            pointerType: e.pointerType || (e.touches ? 'touch' : 'mouse'),
            // Partial Touch API interface.
            identifier: this._pointerId,
            screenX: touch.screenX,
            screenY: touch.screenY,
            clientX: touch.clientX,
            clientY: touch.clientY,
            pageX: touch.pageX,
            pageY: touch.pageY,
            target: touch.target,
        }
    }

    /**
     * Emit a raw event as dragger event internally.
     *
     * @private
     * @param {String} type
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     */
    Dragger.prototype._emit = function (type, e) {
        this._emitter.emit(type, this._createEvent(type, e))
    }

    /**
     * If the provided event is a PointerEvent this method will return it if it has
     * the same pointerId as the instance. If the provided event is a TouchEvent
     * this method will try to look for a Touch instance in the changedTouches that
     * has an identifier matching this instance's pointerId. If the provided event
     * is a MouseEvent (or just any other event than PointerEvent or TouchEvent)
     * it will be returned immediately.
     *
     * @private
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     * @returns {?(Touch|PointerEvent|MouseEvent)}
     */
    Dragger.prototype._getTrackedTouch = function (e) {
        if (this._pointerId === null) return null
        return Dragger._getTouchById(e, this._pointerId)
    }

    /**
     * Handler for start event.
     *
     * @private
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     */
    Dragger.prototype._onStart = function (e) {
        if (this._isDestroyed) return

        // If pointer id is already assigned let's return early.
        if (this._pointerId !== null) return

        // Get (and set) pointer id.
        this._pointerId = Dragger._getEventPointerId(e)
        if (this._pointerId === null) return

        // Setup initial data and emit start event.
        var touch = this._getTrackedTouch(e)
        this._startX = this._currentX = touch.clientX
        this._startY = this._currentY = touch.clientY
        this._startTime = Date.now()
        this._isActive = true
        this._emit(Dragger._emitterEvents.start, e)

        // If the drag procedure was not reset within the start procedure let's
        // activate the instance (start listening to move/cancel/end events).
        if (this._isActive) {
            Dragger._activateInstance(this)
        }
    }

    /**
     * Handler for move event.
     *
     * @private
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     */
    Dragger.prototype._onMove = function (e) {
        var touch = this._getTrackedTouch(e)
        if (!touch) return
        this._currentX = touch.clientX
        this._currentY = touch.clientY
        this._emit(Dragger._emitterEvents.move, e)
    }

    /**
     * Handler for cancel event.
     *
     * @private
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     */
    Dragger.prototype._onCancel = function (e) {
        if (!this._getTrackedTouch(e)) return
        this._emit(Dragger._emitterEvents.cancel, e)
        this._reset()
    }

    /**
     * Handler for end event.
     *
     * @private
     * @param {(PointerEvent|TouchEvent|MouseEvent)} e
     */
    Dragger.prototype._onEnd = function (e) {
        if (!this._getTrackedTouch(e)) return
        this._emit(Dragger._emitterEvents.end, e)
        this._reset()
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Check if the element is being dragged at the moment.
     *
     * @public
     * @returns {Boolean}
     */
    Dragger.prototype.isActive = function () {
        return this._isActive
    }

    /**
     * Set element's touch-action CSS property.
     *
     * @public
     * @param {String} value
     */
    Dragger.prototype.setTouchAction = function (value) {
        // Store unmodified touch action value (we trust user input here).
        this._touchAction = value

        // Set touch-action style.
        if (taPropPrefixed) {
            this._cssProps[taPropPrefixed] = ''
            this._element.style[taPropPrefixed] = value
        }

        // If we have an unsupported touch-action value let's add a special listener
        // that prevents default action on touch start event. A dirty hack, but best
        // we can do for now. The other options would be to somehow polyfill the
        // unsupported touch action behavior with custom heuristics which sounds like
        // a can of worms. We do a special exception here for Firefox Android which's
        // touch-action does not work properly if the dragged element is moved in the
        // the DOM tree on touchstart.
        if (HAS_TOUCH_EVENTS) {
            this._element.removeEventListener(Dragger._touchEvents.start, Dragger._preventDefault, true)
            if (this._element.style[taPropPrefixed] !== value || (isFirefox && isAndroid)) {
                this._element.addEventListener(Dragger._touchEvents.start, Dragger._preventDefault, true)
            }
        }
    }

    /**
     * Update element's CSS properties. Accepts an object with camel cased style
     * props with value pairs as it's first argument.
     *
     * @public
     * @param {Object} [newProps]
     */
    Dragger.prototype.setCssProps = function (newProps) {
        if (!newProps) return

        var currentProps = this._cssProps
        var element = this._element
        var prop
        var prefixedProp

        // Reset current props.
        for (prop in currentProps) {
            element.style[prop] = currentProps[prop]
            delete currentProps[prop]
        }

        // Set new props.
        for (prop in newProps) {
            // Make sure we have a value for the prop.
            if (!newProps[prop]) continue

            // Special handling for touch-action.
            if (prop === taProp) {
                this.setTouchAction(newProps[prop])
                continue
            }

            // Get prefixed prop and skip if it does not exist.
            prefixedProp = getPrefixedPropName(element.style, prop)
            if (!prefixedProp) continue

            // Store the prop and add the style.
            currentProps[prefixedProp] = ''
            element.style[prefixedProp] = newProps[prop]
        }
    }

    /**
     * How much the pointer has moved on x-axis from start position, in pixels.
     * Positive value indicates movement from left to right.
     *
     * @public
     * @returns {Number}
     */
    Dragger.prototype.getDeltaX = function () {
        return this._currentX - this._startX
    }

    /**
     * How much the pointer has moved on y-axis from start position, in pixels.
     * Positive value indicates movement from top to bottom.
     *
     * @public
     * @returns {Number}
     */
    Dragger.prototype.getDeltaY = function () {
        return this._currentY - this._startY
    }

    /**
     * How far (in pixels) has pointer moved from start position.
     *
     * @public
     * @returns {Number}
     */
    Dragger.prototype.getDistance = function () {
        var x = this.getDeltaX()
        var y = this.getDeltaY()
        return Math.sqrt(x * x + y * y)
    }

    /**
     * How long has pointer been dragged.
     *
     * @public
     * @returns {Number}
     */
    Dragger.prototype.getDeltaTime = function () {
        return this._startTime ? Date.now() - this._startTime : 0
    }

    /**
     * Bind drag event listeners.
     *
     * @public
     * @param {String} eventName
     *   - 'start', 'move', 'cancel' or 'end'.
     * @param {Function} listener
     */
    Dragger.prototype.on = function (eventName, listener) {
        this._emitter.on(eventName, listener)
    }

    /**
     * Unbind drag event listeners.
     *
     * @public
     * @param {String} eventName
     *   - 'start', 'move', 'cancel' or 'end'.
     * @param {Function} listener
     */
    Dragger.prototype.off = function (eventName, listener) {
        this._emitter.off(eventName, listener)
    }

    /**
     * Destroy the instance and unbind all drag event listeners.
     *
     * @public
     */
    Dragger.prototype.destroy = function () {
        if (this._isDestroyed) return

        var element = this._element

        if (this._edgeHack) this._edgeHack.destroy()

        // Reset data and deactivate the instance.
        this._reset()

        // Destroy emitter.
        this._emitter.destroy()

        // Unbind event handlers.
        element.removeEventListener(Dragger._inputEvents.start, this._onStart, listenerOptions)
        element.removeEventListener('dragstart', Dragger._preventDefault, false)
        element.removeEventListener(Dragger._touchEvents.start, Dragger._preventDefault, true)

        // Reset styles.
        for (var prop in this._cssProps) {
            element.style[prop] = this._cssProps[prop]
            delete this._cssProps[prop]
        }

        // Reset data.
        this._element = null

        // Mark as destroyed.
        this._isDestroyed = true
    }

    var dt = 1000 / 60

    var raf = (
        window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        function (callback) {
            return this.setTimeout(function () {
                callback(Date.now())
            }, dt)
        }
    ).bind(window)

    /**
     * A ticker system for handling DOM reads and writes in an efficient way.
     *
     * @class
     */
    function Ticker(numLanes) {
        this._nextStep = null
        this._lanes = []
        this._stepQueue = []
        this._stepCallbacks = {}
        this._step = this._step.bind(this)
        for (var i = 0; i < numLanes; i++) {
            this._lanes.push(new TickerLane())
        }
    }

    Ticker.prototype._step = function (time) {
        var lanes = this._lanes
        var stepQueue = this._stepQueue
        var stepCallbacks = this._stepCallbacks
        var i, j, id, laneQueue, laneCallbacks, laneIndices

        this._nextStep = null

        for (i = 0; i < lanes.length; i++) {
            laneQueue = lanes[i].queue
            laneCallbacks = lanes[i].callbacks
            laneIndices = lanes[i].indices
            for (j = 0; j < laneQueue.length; j++) {
                id = laneQueue[j]
                if (!id) continue
                stepQueue.push(id)
                stepCallbacks[id] = laneCallbacks[id]
                delete laneCallbacks[id]
                delete laneIndices[id]
            }
            laneQueue.length = 0
        }

        for (i = 0; i < stepQueue.length; i++) {
            id = stepQueue[i]
            if (stepCallbacks[id]) stepCallbacks[id](time)
            delete stepCallbacks[id]
        }

        stepQueue.length = 0
    }

    Ticker.prototype.add = function (laneIndex, id, callback) {
        this._lanes[laneIndex].add(id, callback)
        if (!this._nextStep) this._nextStep = raf(this._step)
    }

    Ticker.prototype.remove = function (laneIndex, id) {
        this._lanes[laneIndex].remove(id)
    }

    /**
     * A lane for ticker.
     *
     * @class
     */
    function TickerLane() {
        this.queue = []
        this.indices = {}
        this.callbacks = {}
    }

    TickerLane.prototype.add = function (id, callback) {
        var index = this.indices[id]
        if (index !== undefined) this.queue[index] = undefined
        this.queue.push(id)
        this.callbacks[id] = callback
        this.indices[id] = this.queue.length - 1
    }

    TickerLane.prototype.remove = function (id) {
        var index = this.indices[id]
        if (index === undefined) return
        this.queue[index] = undefined
        delete this.callbacks[id]
        delete this.indices[id]
    }

    var LAYOUT_READ = 'layoutRead'
    var LAYOUT_WRITE = 'layoutWrite'
    var VISIBILITY_READ = 'visibilityRead'
    var VISIBILITY_WRITE = 'visibilityWrite'
    var DRAG_START_READ = 'dragStartRead'
    var DRAG_START_WRITE = 'dragStartWrite'
    var DRAG_MOVE_READ = 'dragMoveRead'
    var DRAG_MOVE_WRITE = 'dragMoveWrite'
    var DRAG_SCROLL_READ = 'dragScrollRead'
    var DRAG_SCROLL_WRITE = 'dragScrollWrite'
    var DRAG_SORT_READ = 'dragSortRead'
    var PLACEHOLDER_LAYOUT_READ = 'placeholderLayoutRead'
    var PLACEHOLDER_LAYOUT_WRITE = 'placeholderLayoutWrite'
    var PLACEHOLDER_RESIZE_WRITE = 'placeholderResizeWrite'
    var AUTO_SCROLL_READ = 'autoScrollRead'
    var AUTO_SCROLL_WRITE = 'autoScrollWrite'
    var DEBOUNCE_READ = 'debounceRead'

    var LANE_READ = 0
    var LANE_READ_TAIL = 1
    var LANE_WRITE = 2

    var ticker = new Ticker(3)

    function addLayoutTick(itemId, read, write) {
        ticker.add(LANE_READ, LAYOUT_READ + itemId, read)
        ticker.add(LANE_WRITE, LAYOUT_WRITE + itemId, write)
    }

    function cancelLayoutTick(itemId) {
        ticker.remove(LANE_READ, LAYOUT_READ + itemId)
        ticker.remove(LANE_WRITE, LAYOUT_WRITE + itemId)
    }

    function addVisibilityTick(itemId, read, write) {
        ticker.add(LANE_READ, VISIBILITY_READ + itemId, read)
        ticker.add(LANE_WRITE, VISIBILITY_WRITE + itemId, write)
    }

    function cancelVisibilityTick(itemId) {
        ticker.remove(LANE_READ, VISIBILITY_READ + itemId)
        ticker.remove(LANE_WRITE, VISIBILITY_WRITE + itemId)
    }

    function addDragStartTick(itemId, read, write) {
        ticker.add(LANE_READ, DRAG_START_READ + itemId, read)
        ticker.add(LANE_WRITE, DRAG_START_WRITE + itemId, write)
    }

    function cancelDragStartTick(itemId) {
        ticker.remove(LANE_READ, DRAG_START_READ + itemId)
        ticker.remove(LANE_WRITE, DRAG_START_WRITE + itemId)
    }

    function addDragMoveTick(itemId, read, write) {
        ticker.add(LANE_READ, DRAG_MOVE_READ + itemId, read)
        ticker.add(LANE_WRITE, DRAG_MOVE_WRITE + itemId, write)
    }

    function cancelDragMoveTick(itemId) {
        ticker.remove(LANE_READ, DRAG_MOVE_READ + itemId)
        ticker.remove(LANE_WRITE, DRAG_MOVE_WRITE + itemId)
    }

    function addDragScrollTick(itemId, read, write) {
        ticker.add(LANE_READ, DRAG_SCROLL_READ + itemId, read)
        ticker.add(LANE_WRITE, DRAG_SCROLL_WRITE + itemId, write)
    }

    function cancelDragScrollTick(itemId) {
        ticker.remove(LANE_READ, DRAG_SCROLL_READ + itemId)
        ticker.remove(LANE_WRITE, DRAG_SCROLL_WRITE + itemId)
    }

    function addDragSortTick(itemId, read) {
        ticker.add(LANE_READ_TAIL, DRAG_SORT_READ + itemId, read)
    }

    function cancelDragSortTick(itemId) {
        ticker.remove(LANE_READ_TAIL, DRAG_SORT_READ + itemId)
    }

    function addPlaceholderLayoutTick(itemId, read, write) {
        ticker.add(LANE_READ, PLACEHOLDER_LAYOUT_READ + itemId, read)
        ticker.add(LANE_WRITE, PLACEHOLDER_LAYOUT_WRITE + itemId, write)
    }

    function cancelPlaceholderLayoutTick(itemId) {
        ticker.remove(LANE_READ, PLACEHOLDER_LAYOUT_READ + itemId)
        ticker.remove(LANE_WRITE, PLACEHOLDER_LAYOUT_WRITE + itemId)
    }

    function addPlaceholderResizeTick(itemId, write) {
        ticker.add(LANE_WRITE, PLACEHOLDER_RESIZE_WRITE + itemId, write)
    }

    function cancelPlaceholderResizeTick(itemId) {
        ticker.remove(LANE_WRITE, PLACEHOLDER_RESIZE_WRITE + itemId)
    }

    function addAutoScrollTick(read, write) {
        ticker.add(LANE_READ, AUTO_SCROLL_READ, read)
        ticker.add(LANE_WRITE, AUTO_SCROLL_WRITE, write)
    }

    function cancelAutoScrollTick() {
        ticker.remove(LANE_READ, AUTO_SCROLL_READ)
        ticker.remove(LANE_WRITE, AUTO_SCROLL_WRITE)
    }

    function addDebounceTick(debounceId, read) {
        ticker.add(LANE_READ, DEBOUNCE_READ + debounceId, read)
    }

    function cancelDebounceTick(debounceId) {
        ticker.remove(LANE_READ, DEBOUNCE_READ + debounceId)
    }

    var AXIS_X = 1
    var AXIS_Y = 2
    var FORWARD = 4
    var BACKWARD = 8
    var LEFT = AXIS_X | BACKWARD
    var RIGHT = AXIS_X | FORWARD
    var UP = AXIS_Y | BACKWARD
    var DOWN = AXIS_Y | FORWARD

    var functionType = 'function'

    /**
     * Check if a value is a function.
     *
     * @param {*} val
     * @returns {Boolean}
     */
    function isFunction(val) {
        return typeof val === functionType
    }

    var cache$1 = typeof WeakMap === 'function' ? new WeakMap() : null

    /**
     * Returns the computed value of an element's style property as a string.
     *
     * @param {HTMLElement} element
     * @param {String} style
     * @returns {String}
     */
    function getStyle(element, style) {
        var styles = cache$1 && cache$1.get(element)

        if (!styles) {
            styles = window.getComputedStyle(element, null)
            if (cache$1) cache$1.set(element, styles)
        }

        return styles.getPropertyValue(style)
    }

    /**
     * Returns the computed value of an element's style property transformed into
     * a float value.
     *
     * @param {HTMLElement} el
     * @param {String} style
     * @returns {Number}
     */
    function getStyleAsFloat(el, style) {
        return parseFloat(getStyle(el, style)) || 0
    }

    var DOC_ELEM = document.documentElement
    var BODY = document.body
    var THRESHOLD_DATA = { value: 0, offset: 0 }

    /**
     * @param {HTMLElement|Window} element
     * @returns {HTMLElement|Window}
     */
    function getScrollElement(element) {
        if (element === window || element === DOC_ELEM || element === BODY) {
            return window
        } else {
            return element
        }
    }

    /**
     * @param {HTMLElement|Window} element
     * @returns {Number}
     */
    function getScrollLeft(element) {
        return element === window ? element.pageXOffset : element.scrollLeft
    }

    /**
     * @param {HTMLElement|Window} element
     * @returns {Number}
     */
    function getScrollTop(element) {
        return element === window ? element.pageYOffset : element.scrollTop
    }

    /**
     * @param {HTMLElement|Window} element
     * @returns {Number}
     */
    function getScrollLeftMax(element) {
        if (element === window) {
            return DOC_ELEM.scrollWidth - DOC_ELEM.clientWidth
        } else {
            return element.scrollWidth - element.clientWidth
        }
    }

    /**
     * @param {HTMLElement|Window} element
     * @returns {Number}
     */
    function getScrollTopMax(element) {
        if (element === window) {
            return DOC_ELEM.scrollHeight - DOC_ELEM.clientHeight
        } else {
            return element.scrollHeight - element.clientHeight
        }
    }

    /**
     * Get window's or element's client rectangle data relative to the element's
     * content dimensions (includes inner size + padding, excludes scrollbars,
     * borders and margins).
     *
     * @param {HTMLElement|Window} element
     * @returns {Rectangle}
     */
    function getContentRect(element, result) {
        result = result || {}

        if (element === window) {
            result.width = DOC_ELEM.clientWidth
            result.height = DOC_ELEM.clientHeight
            result.left = 0
            result.right = result.width
            result.top = 0
            result.bottom = result.height
        } else {
            var bcr = element.getBoundingClientRect()
            var borderLeft = element.clientLeft || getStyleAsFloat(element, 'border-left-width')
            var borderTop = element.clientTop || getStyleAsFloat(element, 'border-top-width')
            result.width = element.clientWidth
            result.height = element.clientHeight
            result.left = bcr.left + borderLeft
            result.right = result.left + result.width
            result.top = bcr.top + borderTop
            result.bottom = result.top + result.height
        }

        return result
    }

    /**
     * @param {Item} item
     * @returns {Object}
     */
    function getItemAutoScrollSettings(item) {
        return item._drag._getGrid()._settings.dragAutoScroll
    }

    /**
     * @param {Item} item
     */
    function prepareItemScrollSync(item) {
        if (!item._drag) return
        item._drag._prepareScroll()
    }

    /**
     * @param {Item} item
     */
    function applyItemScrollSync(item) {
        if (!item._drag || !item._isActive) return
        var drag = item._drag
        drag._scrollDiffX = drag._scrollDiffY = 0
        item._setTranslate(drag._left, drag._top)
    }

    /**
     * Compute threshold value and edge offset.
     *
     * @param {Number} threshold
     * @param {Number} safeZone
     * @param {Number} itemSize
     * @param {Number} targetSize
     * @returns {Object}
     */
    function computeThreshold(threshold, safeZone, itemSize, targetSize) {
        THRESHOLD_DATA.value = Math.min(targetSize / 2, threshold)
        THRESHOLD_DATA.offset =
            Math.max(0, itemSize + THRESHOLD_DATA.value * 2 + targetSize * safeZone - targetSize) / 2
        return THRESHOLD_DATA
    }

    function ScrollRequest() {
        this.reset()
    }

    ScrollRequest.prototype.reset = function () {
        if (this.isActive) this.onStop()
        this.item = null
        this.element = null
        this.isActive = false
        this.isEnding = false
        this.direction = null
        this.value = null
        this.maxValue = 0
        this.threshold = 0
        this.distance = 0
        this.speed = 0
        this.duration = 0
        this.action = null
    }

    ScrollRequest.prototype.hasReachedEnd = function () {
        return FORWARD & this.direction ? this.value >= this.maxValue : this.value <= 0
    }

    ScrollRequest.prototype.computeCurrentScrollValue = function () {
        if (this.value === null) {
            return AXIS_X & this.direction ? getScrollLeft(this.element) : getScrollTop(this.element)
        }
        return Math.max(0, Math.min(this.value, this.maxValue))
    }

    ScrollRequest.prototype.computeNextScrollValue = function (deltaTime) {
        var delta = this.speed * (deltaTime / 1000)
        var nextValue = FORWARD & this.direction ? this.value + delta : this.value - delta
        return Math.max(0, Math.min(nextValue, this.maxValue))
    }

    ScrollRequest.prototype.computeSpeed = (function () {
        var data = {
            direction: null,
            threshold: 0,
            distance: 0,
            value: 0,
            maxValue: 0,
            deltaTime: 0,
            duration: 0,
            isEnding: false,
        }

        return function (deltaTime) {
            var item = this.item
            var speed = getItemAutoScrollSettings(item).speed

            if (isFunction(speed)) {
                data.direction = this.direction
                data.threshold = this.threshold
                data.distance = this.distance
                data.value = this.value
                data.maxValue = this.maxValue
                data.duration = this.duration
                data.speed = this.speed
                data.deltaTime = deltaTime
                data.isEnding = this.isEnding
                return speed(item, this.element, data)
            } else {
                return speed
            }
        }
    })()

    ScrollRequest.prototype.tick = function (deltaTime) {
        if (!this.isActive) {
            this.isActive = true
            this.onStart()
        }
        this.value = this.computeCurrentScrollValue()
        this.speed = this.computeSpeed(deltaTime)
        this.value = this.computeNextScrollValue(deltaTime)
        this.duration += deltaTime
        return this.value
    }

    ScrollRequest.prototype.onStart = function () {
        var item = this.item
        var onStart = getItemAutoScrollSettings(item).onStart
        if (isFunction(onStart)) onStart(item, this.element, this.direction)
    }

    ScrollRequest.prototype.onStop = function () {
        var item = this.item
        var onStop = getItemAutoScrollSettings(item).onStop
        if (isFunction(onStop)) onStop(item, this.element, this.direction)
        // Manually nudge sort to happen. There's a good chance that the item is still
        // after the scroll stops which means that the next sort will be triggered
        // only after the item is moved or it's parent scrolled.
        if (item._drag) item._drag.sort()
    }

    function ScrollAction() {
        this.element = null
        this.requestX = null
        this.requestY = null
        this.scrollLeft = 0
        this.scrollTop = 0
    }

    ScrollAction.prototype.reset = function () {
        if (this.requestX) this.requestX.action = null
        if (this.requestY) this.requestY.action = null
        this.element = null
        this.requestX = null
        this.requestY = null
        this.scrollLeft = 0
        this.scrollTop = 0
    }

    ScrollAction.prototype.addRequest = function (request) {
        if (AXIS_X & request.direction) {
            this.removeRequest(this.requestX)
            this.requestX = request
        } else {
            this.removeRequest(this.requestY)
            this.requestY = request
        }
        request.action = this
    }

    ScrollAction.prototype.removeRequest = function (request) {
        if (!request) return
        if (this.requestX === request) {
            this.requestX = null
            request.action = null
        } else if (this.requestY === request) {
            this.requestY = null
            request.action = null
        }
    }

    ScrollAction.prototype.computeScrollValues = function () {
        this.scrollLeft = this.requestX ? this.requestX.value : getScrollLeft(this.element)
        this.scrollTop = this.requestY ? this.requestY.value : getScrollTop(this.element)
    }

    ScrollAction.prototype.scroll = function () {
        var element = this.element
        if (!element) return

        if (element.scrollTo) {
            element.scrollTo(this.scrollLeft, this.scrollTop)
        } else {
            element.scrollLeft = this.scrollLeft
            element.scrollTop = this.scrollTop
        }
    }

    function Pool(createItem, releaseItem) {
        this.pool = []
        this.createItem = createItem
        this.releaseItem = releaseItem
    }

    Pool.prototype.pick = function () {
        return this.pool.pop() || this.createItem()
    }

    Pool.prototype.release = function (item) {
        this.releaseItem(item)
        if (this.pool.indexOf(item) !== -1) return
        this.pool.push(item)
    }

    Pool.prototype.reset = function () {
        this.pool.length = 0
    }

    /**
     * Check if two rectangles are overlapping.
     *
     * @param {Object} a
     * @param {Object} b
     * @returns {Number}
     */
    function isOverlapping(a, b) {
        return !(
            a.left + a.width <= b.left ||
            b.left + b.width <= a.left ||
            a.top + a.height <= b.top ||
            b.top + b.height <= a.top
        )
    }

    /**
     * Calculate intersection area between two rectangle.
     *
     * @param {Object} a
     * @param {Object} b
     * @returns {Number}
     */
    function getIntersectionArea(a, b) {
        if (!isOverlapping(a, b)) return 0
        var width = Math.min(a.left + a.width, b.left + b.width) - Math.max(a.left, b.left)
        var height = Math.min(a.top + a.height, b.top + b.height) - Math.max(a.top, b.top)
        return width * height
    }

    /**
     * Calculate how many percent the intersection area of two rectangles is from
     * the maximum potential intersection area between the rectangles.
     *
     * @param {Object} a
     * @param {Object} b
     * @returns {Number}
     */
    function getIntersectionScore(a, b) {
        var area = getIntersectionArea(a, b)
        if (!area) return 0
        var maxArea = Math.min(a.width, b.width) * Math.min(a.height, b.height)
        return (area / maxArea) * 100
    }

    var RECT_1 = {
        width: 0,
        height: 0,
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
    }

    var RECT_2 = {
        width: 0,
        height: 0,
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
    }

    function AutoScroller() {
        this._isDestroyed = false
        this._isTicking = false
        this._tickTime = 0
        this._tickDeltaTime = 0
        this._items = []
        this._actions = []
        this._requests = {}
        this._requests[AXIS_X] = {}
        this._requests[AXIS_Y] = {}
        this._requestOverlapCheck = {}
        this._dragPositions = {}
        this._dragDirections = {}
        this._overlapCheckInterval = 150

        this._requestPool = new Pool(
            function () {
                return new ScrollRequest()
            },
            function (request) {
                request.reset()
            },
        )

        this._actionPool = new Pool(
            function () {
                return new ScrollAction()
            },
            function (action) {
                action.reset()
            },
        )

        this._readTick = this._readTick.bind(this)
        this._writeTick = this._writeTick.bind(this)
    }

    AutoScroller.AXIS_X = AXIS_X
    AutoScroller.AXIS_Y = AXIS_Y
    AutoScroller.FORWARD = FORWARD
    AutoScroller.BACKWARD = BACKWARD
    AutoScroller.LEFT = LEFT
    AutoScroller.RIGHT = RIGHT
    AutoScroller.UP = UP
    AutoScroller.DOWN = DOWN

    AutoScroller.smoothSpeed = function (maxSpeed, acceleration, deceleration) {
        return function (item, element, data) {
            var targetSpeed = 0
            if (!data.isEnding) {
                if (data.threshold > 0) {
                    var factor = data.threshold - Math.max(0, data.distance)
                    targetSpeed = (maxSpeed / data.threshold) * factor
                } else {
                    targetSpeed = maxSpeed
                }
            }

            var currentSpeed = data.speed
            var nextSpeed = targetSpeed

            if (currentSpeed === targetSpeed) {
                return nextSpeed
            }

            if (currentSpeed < targetSpeed) {
                nextSpeed = currentSpeed + acceleration * (data.deltaTime / 1000)
                return Math.min(targetSpeed, nextSpeed)
            } else {
                nextSpeed = currentSpeed - deceleration * (data.deltaTime / 1000)
                return Math.max(targetSpeed, nextSpeed)
            }
        }
    }

    AutoScroller.pointerHandle = function (pointerSize) {
        var rect = { left: 0, top: 0, width: 0, height: 0 }
        var size = pointerSize || 1
        return function (item, x, y, w, h, pX, pY) {
            rect.left = pX - size * 0.5
            rect.top = pY - size * 0.5
            rect.width = size
            rect.height = size
            return rect
        }
    }

    AutoScroller.prototype._readTick = function (time) {
        if (this._isDestroyed) return
        if (time && this._tickTime) {
            this._tickDeltaTime = time - this._tickTime
            this._tickTime = time
            this._updateRequests()
            this._updateActions()
        } else {
            this._tickTime = time
            this._tickDeltaTime = 0
        }
    }

    AutoScroller.prototype._writeTick = function () {
        if (this._isDestroyed) return
        this._applyActions()
        addAutoScrollTick(this._readTick, this._writeTick)
    }

    AutoScroller.prototype._startTicking = function () {
        this._isTicking = true
        addAutoScrollTick(this._readTick, this._writeTick)
    }

    AutoScroller.prototype._stopTicking = function () {
        this._isTicking = false
        this._tickTime = 0
        this._tickDeltaTime = 0
        cancelAutoScrollTick()
    }

    AutoScroller.prototype._getItemHandleRect = function (item, handle, rect) {
        var itemDrag = item._drag

        if (handle) {
            var ev = itemDrag._dragMoveEvent || itemDrag._dragStartEvent
            var data = handle(
                item,
                itemDrag._clientX,
                itemDrag._clientY,
                item._width,
                item._height,
                ev.clientX,
                ev.clientY,
            )
            rect.left = data.left
            rect.top = data.top
            rect.width = data.width
            rect.height = data.height
        } else {
            rect.left = itemDrag._clientX
            rect.top = itemDrag._clientY
            rect.width = item._width
            rect.height = item._height
        }

        rect.right = rect.left + rect.width
        rect.bottom = rect.top + rect.height

        return rect
    }

    AutoScroller.prototype._requestItemScroll = function (
        item,
        axis,
        element,
        direction,
        threshold,
        distance,
        maxValue,
    ) {
        var reqMap = this._requests[axis]
        var request = reqMap[item._id]

        if (request) {
            if (request.element !== element || request.direction !== direction) {
                request.reset()
            }
        } else {
            request = this._requestPool.pick()
        }

        request.item = item
        request.element = element
        request.direction = direction
        request.threshold = threshold
        request.distance = distance
        request.maxValue = maxValue
        reqMap[item._id] = request
    }

    AutoScroller.prototype._cancelItemScroll = function (item, axis) {
        var reqMap = this._requests[axis]
        var request = reqMap[item._id]
        if (!request) return
        if (request.action) request.action.removeRequest(request)
        this._requestPool.release(request)
        delete reqMap[item._id]
    }

    AutoScroller.prototype._checkItemOverlap = function (item, checkX, checkY) {
        var settings = getItemAutoScrollSettings(item)
        var targets = isFunction(settings.targets) ? settings.targets(item) : settings.targets
        var threshold = settings.threshold
        var safeZone = settings.safeZone

        if (!targets || !targets.length) {
            checkX && this._cancelItemScroll(item, AXIS_X)
            checkY && this._cancelItemScroll(item, AXIS_Y)
            return
        }

        var dragDirections = this._dragDirections[item._id]
        var dragDirectionX = dragDirections[0]
        var dragDirectionY = dragDirections[1]

        if (!dragDirectionX && !dragDirectionY) {
            checkX && this._cancelItemScroll(item, AXIS_X)
            checkY && this._cancelItemScroll(item, AXIS_Y)
            return
        }

        var itemRect = this._getItemHandleRect(item, settings.handle, RECT_1)
        var testRect = RECT_2

        var target = null
        var testElement = null
        var testAxisX = true
        var testAxisY = true
        var testScore = 0
        var testPriority = 0
        var testThreshold = null
        var testDirection = null
        var testDistance = 0
        var testMaxScrollX = 0
        var testMaxScrollY = 0

        var xElement = null
        var xPriority = -Infinity
        var xThreshold = 0
        var xScore = 0
        var xDirection = null
        var xDistance = 0
        var xMaxScroll = 0

        var yElement = null
        var yPriority = -Infinity
        var yThreshold = 0
        var yScore = 0
        var yDirection = null
        var yDistance = 0
        var yMaxScroll = 0

        for (var i = 0; i < targets.length; i++) {
            target = targets[i]
            testAxisX = checkX && dragDirectionX && target.axis !== AXIS_Y
            testAxisY = checkY && dragDirectionY && target.axis !== AXIS_X
            testPriority = target.priority || 0

            // Ignore this item if it's x-axis and y-axis priority is lower than
            // the currently matching item's.
            if ((!testAxisX || testPriority < xPriority) && (!testAxisY || testPriority < yPriority)) {
                continue
            }

            testElement = getScrollElement(target.element || target)
            testMaxScrollX = testAxisX ? getScrollLeftMax(testElement) : -1
            testMaxScrollY = testAxisY ? getScrollTopMax(testElement) : -1

            // Ignore this item if there is no possibility to scroll.
            if (!testMaxScrollX && !testMaxScrollY) continue

            testRect = getContentRect(testElement, testRect)
            testScore = getIntersectionScore(itemRect, testRect)

            // Ignore this item if it's not overlapping at all with the dragged item.
            if (testScore <= 0) continue

            // Test x-axis.
            if (
                testAxisX &&
                testPriority >= xPriority &&
                testMaxScrollX > 0 &&
                (testPriority > xPriority || testScore > xScore)
            ) {
                testDirection = null
                testThreshold = computeThreshold(
                    typeof target.threshold === 'number' ? target.threshold : threshold,
                    safeZone,
                    itemRect.width,
                    testRect.width,
                )
                if (dragDirectionX === RIGHT) {
                    testDistance = testRect.right + testThreshold.offset - itemRect.right
                    if (testDistance <= testThreshold.value && getScrollLeft(testElement) < testMaxScrollX) {
                        testDirection = RIGHT
                    }
                } else if (dragDirectionX === LEFT) {
                    testDistance = itemRect.left - (testRect.left - testThreshold.offset)
                    if (testDistance <= testThreshold.value && getScrollLeft(testElement) > 0) {
                        testDirection = LEFT
                    }
                }

                if (testDirection !== null) {
                    xElement = testElement
                    xPriority = testPriority
                    xThreshold = testThreshold.value
                    xScore = testScore
                    xDirection = testDirection
                    xDistance = testDistance
                    xMaxScroll = testMaxScrollX
                }
            }

            // Test y-axis.
            if (
                testAxisY &&
                testPriority >= yPriority &&
                testMaxScrollY > 0 &&
                (testPriority > yPriority || testScore > yScore)
            ) {
                testDirection = null
                testThreshold = computeThreshold(
                    typeof target.threshold === 'number' ? target.threshold : threshold,
                    safeZone,
                    itemRect.height,
                    testRect.height,
                )
                if (dragDirectionY === DOWN) {
                    testDistance = testRect.bottom + testThreshold.offset - itemRect.bottom
                    if (testDistance <= testThreshold.value && getScrollTop(testElement) < testMaxScrollY) {
                        testDirection = DOWN
                    }
                } else if (dragDirectionY === UP) {
                    testDistance = itemRect.top - (testRect.top - testThreshold.offset)
                    if (testDistance <= testThreshold.value && getScrollTop(testElement) > 0) {
                        testDirection = UP
                    }
                }

                if (testDirection !== null) {
                    yElement = testElement
                    yPriority = testPriority
                    yThreshold = testThreshold.value
                    yScore = testScore
                    yDirection = testDirection
                    yDistance = testDistance
                    yMaxScroll = testMaxScrollY
                }
            }
        }

        // Request or cancel x-axis scroll.
        if (checkX) {
            if (xElement) {
                this._requestItemScroll(item, AXIS_X, xElement, xDirection, xThreshold, xDistance, xMaxScroll)
            } else {
                this._cancelItemScroll(item, AXIS_X)
            }
        }

        // Request or cancel y-axis scroll.
        if (checkY) {
            if (yElement) {
                this._requestItemScroll(item, AXIS_Y, yElement, yDirection, yThreshold, yDistance, yMaxScroll)
            } else {
                this._cancelItemScroll(item, AXIS_Y)
            }
        }
    }

    AutoScroller.prototype._updateScrollRequest = function (scrollRequest) {
        var item = scrollRequest.item
        var settings = getItemAutoScrollSettings(item)
        var targets = isFunction(settings.targets) ? settings.targets(item) : settings.targets
        var targetCount = (targets && targets.length) || 0
        var threshold = settings.threshold
        var safeZone = settings.safeZone
        var itemRect = this._getItemHandleRect(item, settings.handle, RECT_1)
        var testRect = RECT_2
        var target = null
        var testElement = null
        var testIsAxisX = false
        var testScore = null
        var testThreshold = null
        var testDistance = null
        var testScroll = null
        var testMaxScroll = null
        var hasReachedEnd = null

        for (var i = 0; i < targetCount; i++) {
            target = targets[i]

            // Make sure we have a matching element.
            testElement = getScrollElement(target.element || target)
            if (testElement !== scrollRequest.element) continue

            // Make sure we have a matching axis.
            testIsAxisX = !!(AXIS_X & scrollRequest.direction)
            if (testIsAxisX) {
                if (target.axis === AXIS_Y) continue
            } else {
                if (target.axis === AXIS_X) continue
            }

            // Stop scrolling if there is no room to scroll anymore.
            testMaxScroll = testIsAxisX ? getScrollLeftMax(testElement) : getScrollTopMax(testElement)
            if (testMaxScroll <= 0) {
                break
            }

            testRect = getContentRect(testElement, testRect)
            testScore = getIntersectionScore(itemRect, testRect)

            // Stop scrolling if dragged item is not overlapping with the scroll
            // element anymore.
            if (testScore <= 0) {
                break
            }

            // Compute threshold and edge offset.
            testThreshold = computeThreshold(
                typeof target.threshold === 'number' ? target.threshold : threshold,
                safeZone,
                testIsAxisX ? itemRect.width : itemRect.height,
                testIsAxisX ? testRect.width : testRect.height,
            )

            // Compute distance (based on current direction).
            if (scrollRequest.direction === LEFT) {
                testDistance = itemRect.left - (testRect.left - testThreshold.offset)
            } else if (scrollRequest.direction === RIGHT) {
                testDistance = testRect.right + testThreshold.offset - itemRect.right
            } else if (scrollRequest.direction === UP) {
                testDistance = itemRect.top - (testRect.top - testThreshold.offset)
            } else {
                testDistance = testRect.bottom + testThreshold.offset - itemRect.bottom
            }

            // Stop scrolling if threshold is not exceeded.
            if (testDistance > testThreshold.value) {
                break
            }

            // Stop scrolling if we have reached the end of the scroll value.
            testScroll = testIsAxisX ? getScrollLeft(testElement) : getScrollTop(testElement)
            hasReachedEnd = FORWARD & scrollRequest.direction ? testScroll >= testMaxScroll : testScroll <= 0
            if (hasReachedEnd) {
                break
            }

            // Scrolling can continue, let's update the values.
            scrollRequest.maxValue = testMaxScroll
            scrollRequest.threshold = testThreshold.value
            scrollRequest.distance = testDistance
            scrollRequest.isEnding = false
            return true
        }

        // Before we end the request, let's see if we need to stop the scrolling
        // smoothly or immediately.
        if (settings.smoothStop === true && scrollRequest.speed > 0) {
            if (hasReachedEnd === null) hasReachedEnd = scrollRequest.hasReachedEnd()
            scrollRequest.isEnding = hasReachedEnd ? false : true
        } else {
            scrollRequest.isEnding = false
        }

        return scrollRequest.isEnding
    }

    AutoScroller.prototype._updateRequests = function () {
        var items = this._items
        var requestsX = this._requests[AXIS_X]
        var requestsY = this._requests[AXIS_Y]
        var item, reqX, reqY, checkTime, needsCheck, checkX, checkY

        for (var i = 0; i < items.length; i++) {
            item = items[i]
            checkTime = this._requestOverlapCheck[item._id]
            needsCheck = checkTime > 0 && this._tickTime - checkTime > this._overlapCheckInterval

            checkX = true
            reqX = requestsX[item._id]
            if (reqX && reqX.isActive) {
                checkX = !this._updateScrollRequest(reqX)
                if (checkX) {
                    needsCheck = true
                    this._cancelItemScroll(item, AXIS_X)
                }
            }

            checkY = true
            reqY = requestsY[item._id]
            if (reqY && reqY.isActive) {
                checkY = !this._updateScrollRequest(reqY)
                if (checkY) {
                    needsCheck = true
                    this._cancelItemScroll(item, AXIS_Y)
                }
            }

            if (needsCheck) {
                this._requestOverlapCheck[item._id] = 0
                this._checkItemOverlap(item, checkX, checkY)
            }
        }
    }

    AutoScroller.prototype._requestAction = function (request, axis) {
        var actions = this._actions
        var isAxisX = axis === AXIS_X
        var action = null

        for (var i = 0; i < actions.length; i++) {
            action = actions[i]

            // If the action's request does not match the request's -> skip.
            if (request.element !== action.element) {
                action = null
                continue
            }

            // If the request and action share the same element, but the request slot
            // for the requested axis is already reserved let's ignore and cancel this
            // request.
            if (isAxisX ? action.requestX : action.requestY) {
                this._cancelItemScroll(request.item, axis)
                return
            }

            // Seems like we have found our action, let's break the loop.
            break
        }

        if (!action) action = this._actionPool.pick()
        action.element = request.element
        action.addRequest(request)

        request.tick(this._tickDeltaTime)
        actions.push(action)
    }

    AutoScroller.prototype._updateActions = function () {
        var items = this._items
        var requests = this._requests
        var actions = this._actions
        var itemId
        var reqX
        var reqY
        var i

        // Generate actions.
        for (i = 0; i < items.length; i++) {
            itemId = items[i]._id
            reqX = requests[AXIS_X][itemId]
            reqY = requests[AXIS_Y][itemId]
            if (reqX) this._requestAction(reqX, AXIS_X)
            if (reqY) this._requestAction(reqY, AXIS_Y)
        }

        // Compute actions' scroll values.
        for (i = 0; i < actions.length; i++) {
            actions[i].computeScrollValues()
        }
    }

    AutoScroller.prototype._applyActions = function () {
        var actions = this._actions
        var items = this._items
        var i

        // No actions -> no scrolling.
        if (!actions.length) return

        // Scroll all the required elements.
        for (i = 0; i < actions.length; i++) {
            actions[i].scroll()
            this._actionPool.release(actions[i])
        }

        // Reset actions.
        actions.length = 0

        // Sync the item position immediately after all the auto-scrolling business is
        // finished. Without this procedure the items will jitter during auto-scroll
        // (in some cases at least) since the drag scroll handler is async (bound to
        // raf tick). Note that this procedure should not emit any dragScroll events,
        // because otherwise they would be emitted twice for the same event.
        for (i = 0; i < items.length; i++) prepareItemScrollSync(items[i])
        for (i = 0; i < items.length; i++) applyItemScrollSync(items[i])
    }

    AutoScroller.prototype._updateDragDirection = function (item) {
        var dragPositions = this._dragPositions[item._id]
        var dragDirections = this._dragDirections[item._id]
        var x1 = item._drag._left
        var y1 = item._drag._top
        if (dragPositions.length) {
            var x2 = dragPositions[0]
            var y2 = dragPositions[1]
            dragDirections[0] = x1 > x2 ? RIGHT : x1 < x2 ? LEFT : dragDirections[0] || 0
            dragDirections[1] = y1 > y2 ? DOWN : y1 < y2 ? UP : dragDirections[1] || 0
        }
        dragPositions[0] = x1
        dragPositions[1] = y1
    }

    AutoScroller.prototype.addItem = function (item) {
        if (this._isDestroyed) return
        var index = this._items.indexOf(item)
        if (index === -1) {
            this._items.push(item)
            this._requestOverlapCheck[item._id] = this._tickTime
            this._dragDirections[item._id] = [0, 0]
            this._dragPositions[item._id] = []
            if (!this._isTicking) this._startTicking()
        }
    }

    AutoScroller.prototype.updateItem = function (item) {
        if (this._isDestroyed) return

        // Make sure the item still exists in the auto-scroller.
        if (!this._dragDirections[item._id]) return

        this._updateDragDirection(item)
        if (!this._requestOverlapCheck[item._id]) {
            this._requestOverlapCheck[item._id] = this._tickTime
        }
    }

    AutoScroller.prototype.removeItem = function (item) {
        if (this._isDestroyed) return

        var index = this._items.indexOf(item)
        if (index === -1) return

        var itemId = item._id

        var reqX = this._requests[AXIS_X][itemId]
        if (reqX) {
            this._cancelItemScroll(item, AXIS_X)
            delete this._requests[AXIS_X][itemId]
        }

        var reqY = this._requests[AXIS_Y][itemId]
        if (reqY) {
            this._cancelItemScroll(item, AXIS_Y)
            delete this._requests[AXIS_Y][itemId]
        }

        delete this._requestOverlapCheck[itemId]
        delete this._dragPositions[itemId]
        delete this._dragDirections[itemId]
        this._items.splice(index, 1)

        if (this._isTicking && !this._items.length) {
            this._stopTicking()
        }
    }

    AutoScroller.prototype.isItemScrollingX = function (item) {
        var reqX = this._requests[AXIS_X][item._id]
        return !!(reqX && reqX.isActive)
    }

    AutoScroller.prototype.isItemScrollingY = function (item) {
        var reqY = this._requests[AXIS_Y][item._id]
        return !!(reqY && reqY.isActive)
    }

    AutoScroller.prototype.isItemScrolling = function (item) {
        return this.isItemScrollingX(item) || this.isItemScrollingY(item)
    }

    AutoScroller.prototype.destroy = function () {
        if (this._isDestroyed) return

        var items = this._items.slice(0)
        for (var i = 0; i < items.length; i++) {
            this.removeItem(items[i])
        }

        this._actions.length = 0
        this._requestPool.reset()
        this._actionPool.reset()

        this._isDestroyed = true
    }

    var ElProto = window.Element.prototype
    var matchesFn =
        ElProto.matches ||
        ElProto.matchesSelector ||
        ElProto.webkitMatchesSelector ||
        ElProto.mozMatchesSelector ||
        ElProto.msMatchesSelector ||
        ElProto.oMatchesSelector ||
        function () {
            return false
        }

    /**
     * Check if element matches a CSS selector.
     *
     * @param {Element} el
     * @param {String} selector
     * @returns {Boolean}
     */
    function elementMatches(el, selector) {
        return matchesFn.call(el, selector)
    }

    /**
     * Add class to an element.
     *
     * @param {HTMLElement} element
     * @param {String} className
     */
    function addClass(element, className) {
        if (!className) return

        if (element.classList) {
            element.classList.add(className)
        } else {
            if (!elementMatches(element, '.' + className)) {
                element.className += ' ' + className
            }
        }
    }

    var tempArray = []
    var numberType = 'number'

    /**
     * Insert an item or an array of items to array to a specified index. Mutates
     * the array. The index can be negative in which case the items will be added
     * to the end of the array.
     *
     * @param {Array} array
     * @param {*} items
     * @param {Number} [index=-1]
     */
    function arrayInsert(array, items, index) {
        var startIndex = typeof index === numberType ? index : -1
        if (startIndex < 0) startIndex = array.length - startIndex + 1

        array.splice.apply(array, tempArray.concat(startIndex, 0, items))
        tempArray.length = 0
    }

    /**
     * Normalize array index. Basically this function makes sure that the provided
     * array index is within the bounds of the provided array and also transforms
     * negative index to the matching positive index. The third (optional) argument
     * allows you to define offset for array's length in case you are adding items
     * to the array or removing items from the array.
     *
     * @param {Array} array
     * @param {Number} index
     * @param {Number} [sizeOffset]
     */
    function normalizeArrayIndex(array, index, sizeOffset) {
        var maxIndex = Math.max(0, array.length - 1 + (sizeOffset || 0))
        return index > maxIndex ? maxIndex : index < 0 ? Math.max(maxIndex + index + 1, 0) : index
    }

    /**
     * Move array item to another index.
     *
     * @param {Array} array
     * @param {Number} fromIndex
     *   - Index (positive or negative) of the item that will be moved.
     * @param {Number} toIndex
     *   - Index (positive or negative) where the item should be moved to.
     */
    function arrayMove(array, fromIndex, toIndex) {
        // Make sure the array has two or more items.
        if (array.length < 2) return

        // Normalize the indices.
        var from = normalizeArrayIndex(array, fromIndex)
        var to = normalizeArrayIndex(array, toIndex)

        // Add target item to the new position.
        if (from !== to) {
            array.splice(to, 0, array.splice(from, 1)[0])
        }
    }

    /**
     * Swap array items.
     *
     * @param {Array} array
     * @param {Number} index
     *   - Index (positive or negative) of the item that will be swapped.
     * @param {Number} withIndex
     *   - Index (positive or negative) of the other item that will be swapped.
     */
    function arraySwap(array, index, withIndex) {
        // Make sure the array has two or more items.
        if (array.length < 2) return

        // Normalize the indices.
        var indexA = normalizeArrayIndex(array, index)
        var indexB = normalizeArrayIndex(array, withIndex)
        var temp

        // Swap the items.
        if (indexA !== indexB) {
            temp = array[indexA]
            array[indexA] = array[indexB]
            array[indexB] = temp
        }
    }

    var transformProp = getPrefixedPropName(document.documentElement.style, 'transform') || 'transform'

    var styleNameRegEx = /([A-Z])/g
    var prefixRegex = /^(webkit-|moz-|ms-|o-)/
    var msPrefixRegex = /^(-m-s-)/

    /**
     * Transforms a camel case style property to kebab case style property. Handles
     * vendor prefixed properties elegantly as well, e.g. "WebkitTransform" and
     * "webkitTransform" are both transformed into "-webkit-transform".
     *
     * @param {String} property
     * @returns {String}
     */
    function getStyleName(property) {
        // Initial slicing, turns "fooBarProp" into "foo-bar-prop".
        var styleName = property.replace(styleNameRegEx, '-$1').toLowerCase()

        // Handle properties that start with "webkit", "moz", "ms" or "o" prefix (we
        // need to add an extra '-' to the beginnig).
        styleName = styleName.replace(prefixRegex, '-$1')

        // Handle properties that start with "MS" prefix (we need to transform the
        // "-m-s-" into "-ms-").
        styleName = styleName.replace(msPrefixRegex, '-ms-')

        return styleName
    }

    var transformStyle = getStyleName(transformProp)

    var transformNone$1 = 'none'
    var displayInline = 'inline'
    var displayNone = 'none'
    var displayStyle = 'display'

    /**
     * Returns true if element is transformed, false if not. In practice the
     * element's display value must be anything else than "none" or "inline" as
     * well as have a valid transform value applied in order to be counted as a
     * transformed element.
     *
     * Borrowed from Mezr (v0.6.1):
     * https://github.com/niklasramo/mezr/blob/0.6.1/mezr.js#L661
     *
     * @param {HTMLElement} element
     * @returns {Boolean}
     */
    function isTransformed(element) {
        var transform = getStyle(element, transformStyle)
        if (!transform || transform === transformNone$1) return false

        var display = getStyle(element, displayStyle)
        if (display === displayInline || display === displayNone) return false

        return true
    }

    /**
     * Returns an absolute positioned element's containing block, which is
     * considered to be the closest ancestor element that the target element's
     * positioning is relative to. Disclaimer: this only works as intended for
     * absolute positioned elements.
     *
     * @param {HTMLElement} element
     * @returns {(Document|Element)}
     */
    function getContainingBlock(element) {
        // As long as the containing block is an element, static and not
        // transformed, try to get the element's parent element and fallback to
        // document. https://github.com/niklasramo/mezr/blob/0.6.1/mezr.js#L339
        var doc = document
        var res = element || doc
        while (res && res !== doc && getStyle(res, 'position') === 'static' && !isTransformed(res)) {
            res = res.parentElement || doc
        }
        return res
    }

    var offsetA = {}
    var offsetB = {}
    var offsetDiff = {}

    /**
     * Returns the element's document offset, which in practice means the vertical
     * and horizontal distance between the element's northwest corner and the
     * document's northwest corner. Note that this function always returns the same
     * object so be sure to read the data from it instead using it as a reference.
     *
     * @param {(Document|Element|Window)} element
     * @param {Object} [offsetData]
     *   - Optional data object where the offset data will be inserted to. If not
     *     provided a new object will be created for the return data.
     * @returns {Object}
     */
    function getOffset(element, offsetData) {
        var offset = offsetData || {}
        var rect

        // Set up return data.
        offset.left = 0
        offset.top = 0

        // Document's offsets are always 0.
        if (element === document) return offset

        // Add viewport scroll left/top to the respective offsets.
        offset.left = window.pageXOffset || 0
        offset.top = window.pageYOffset || 0

        // Window's offsets are the viewport scroll left/top values.
        if (element.self === window.self) return offset

        // Add element's client rects to the offsets.
        rect = element.getBoundingClientRect()
        offset.left += rect.left
        offset.top += rect.top

        // Exclude element's borders from the offset.
        offset.left += getStyleAsFloat(element, 'border-left-width')
        offset.top += getStyleAsFloat(element, 'border-top-width')

        return offset
    }

    /**
     * Calculate the offset difference two elements.
     *
     * @param {HTMLElement} elemA
     * @param {HTMLElement} elemB
     * @param {Boolean} [compareContainingBlocks=false]
     *   - When this is set to true the containing blocks of the provided elements
     *     will be used for calculating the difference. Otherwise the provided
     *     elements will be compared directly.
     * @returns {Object}
     */
    function getOffsetDiff(elemA, elemB, compareContainingBlocks) {
        offsetDiff.left = 0
        offsetDiff.top = 0

        // If elements are same let's return early.
        if (elemA === elemB) return offsetDiff

        // Compare containing blocks if necessary.
        if (compareContainingBlocks) {
            elemA = getContainingBlock(elemA)
            elemB = getContainingBlock(elemB)

            // If containing blocks are identical, let's return early.
            if (elemA === elemB) return offsetDiff
        }

        // Finally, let's calculate the offset diff.
        getOffset(elemA, offsetA)
        getOffset(elemB, offsetB)
        offsetDiff.left = offsetB.left - offsetA.left
        offsetDiff.top = offsetB.top - offsetA.top

        return offsetDiff
    }

    /**
     * Check if overflow style value is scrollable.
     *
     * @param {String} value
     * @returns {Boolean}
     */
    function isScrollableOverflow(value) {
        return value === 'auto' || value === 'scroll' || value === 'overlay'
    }

    /**
     * Check if an element is scrollable.
     *
     * @param {HTMLElement} element
     * @returns {Boolean}
     */
    function isScrollable(element) {
        return (
            isScrollableOverflow(getStyle(element, 'overflow')) ||
            isScrollableOverflow(getStyle(element, 'overflow-x')) ||
            isScrollableOverflow(getStyle(element, 'overflow-y'))
        )
    }

    /**
     * Collect element's ancestors that are potentially scrollable elements. The
     * provided element is also also included in the check, meaning that if it is
     * scrollable it is added to the result array.
     *
     * @param {HTMLElement} element
     * @param {Array} [result]
     * @returns {Array}
     */
    function getScrollableAncestors(element, result) {
        result = result || []

        // Find scroll parents.
        while (element && element !== document) {
            // If element is inside ShadowDOM let's get it's host node from the real
            // DOM and continue looping.
            if (element.getRootNode && element instanceof DocumentFragment) {
                element = element.getRootNode().host
                continue
            }

            // If element is scrollable let's add it to the scrollable list.
            if (isScrollable(element)) {
                result.push(element)
            }

            element = element.parentNode
        }

        // Always add window to the results.
        result.push(window)

        return result
    }

    var translateValue = {}
    var transformNone = 'none'
    var rxMat3d = /^matrix3d/
    var rxMatTx = /([^,]*,){4}/
    var rxMat3dTx = /([^,]*,){12}/
    var rxNextItem = /[^,]*,/

    /**
     * Returns the element's computed translateX and translateY values as a floats.
     * The returned object is always the same object and updated every time this
     * function is called.
     *
     * @param {HTMLElement} element
     * @returns {Object}
     */
    function getTranslate(element) {
        translateValue.x = 0
        translateValue.y = 0

        var transform = getStyle(element, transformStyle)
        if (!transform || transform === transformNone) {
            return translateValue
        }

        // Transform style can be in either matrix3d(...) or matrix(...).
        var isMat3d = rxMat3d.test(transform)
        var tX = transform.replace(isMat3d ? rxMat3dTx : rxMatTx, '')
        var tY = tX.replace(rxNextItem, '')

        translateValue.x = parseFloat(tX) || 0
        translateValue.y = parseFloat(tY) || 0

        return translateValue
    }

    /**
     * Remove class from an element.
     *
     * @param {HTMLElement} element
     * @param {String} className
     */
    function removeClass(element, className) {
        if (!className) return

        if (element.classList) {
            element.classList.remove(className)
        } else {
            if (elementMatches(element, '.' + className)) {
                element.className = (' ' + element.className + ' ').replace(' ' + className + ' ', ' ').trim()
            }
        }
    }

    var IS_IOS =
        /^(iPad|iPhone|iPod)/.test(window.navigator.platform) ||
        (/^Mac/.test(window.navigator.platform) && window.navigator.maxTouchPoints > 1)
    var START_PREDICATE_INACTIVE = 0
    var START_PREDICATE_PENDING = 1
    var START_PREDICATE_RESOLVED = 2
    var SCROLL_LISTENER_OPTIONS = hasPassiveEvents() ? { passive: true } : false

    /**
     * Bind touch interaction to an item.
     *
     * @class
     * @param {Item} item
     */
    function ItemDrag(item) {
        var element = item._element
        var grid = item.getGrid()
        var settings = grid._settings

        this._item = item
        this._gridId = grid._id
        this._isDestroyed = false
        this._isMigrating = false

        // Start predicate data.
        this._startPredicate = isFunction(settings.dragStartPredicate)
            ? settings.dragStartPredicate
            : ItemDrag.defaultStartPredicate
        this._startPredicateState = START_PREDICATE_INACTIVE
        this._startPredicateResult = undefined

        // Data for drag sort predicate heuristics.
        this._isSortNeeded = false
        this._sortTimer = undefined
        this._blockedSortIndex = null
        this._sortX1 = 0
        this._sortX2 = 0
        this._sortY1 = 0
        this._sortY2 = 0

        // Setup item's initial drag data.
        this._reset()

        // Bind the methods that needs binding.
        this._preStartCheck = this._preStartCheck.bind(this)
        this._preEndCheck = this._preEndCheck.bind(this)
        this._onScroll = this._onScroll.bind(this)
        this._prepareStart = this._prepareStart.bind(this)
        this._applyStart = this._applyStart.bind(this)
        this._prepareMove = this._prepareMove.bind(this)
        this._applyMove = this._applyMove.bind(this)
        this._prepareScroll = this._prepareScroll.bind(this)
        this._applyScroll = this._applyScroll.bind(this)
        this._handleSort = this._handleSort.bind(this)
        this._handleSortDelayed = this._handleSortDelayed.bind(this)

        // Get drag handle element.
        this._handle = (settings.dragHandle && element.querySelector(settings.dragHandle)) || element

        // Init dragger.
        this._dragger = new Dragger(this._handle, settings.dragCssProps)
        this._dragger.on('start', this._preStartCheck)
        this._dragger.on('move', this._preStartCheck)
        this._dragger.on('cancel', this._preEndCheck)
        this._dragger.on('end', this._preEndCheck)
    }

    /**
     * Public properties
     * *****************
     */

    /**
     * @public
     * @static
     * @type {AutoScroller}
     */
    ItemDrag.autoScroller = new AutoScroller()

    /**
     * Public static methods
     * *********************
     */

    /**
     * Default drag start predicate handler that handles anchor elements
     * gracefully. The return value of this function defines if the drag is
     * started, rejected or pending. When true is returned the dragging is started
     * and when false is returned the dragging is rejected. If nothing is returned
     * the predicate will be called again on the next drag movement.
     *
     * @public
     * @static
     * @param {Item} item
     * @param {Object} event
     * @param {Object} [options]
     *   - An optional options object which can be used to pass the predicate
     *     it's options manually. By default the predicate retrieves the options
     *     from the grid's settings.
     * @returns {(Boolean|undefined)}
     */
    ItemDrag.defaultStartPredicate = function (item, event, options) {
        var drag = item._drag

        // Make sure left button is pressed on mouse.
        if (event.isFirst && event.srcEvent.button) {
            return false
        }

        // If the start event is trusted, non-cancelable and it's default action has
        // not been prevented it is in most cases a sign that the gesture would be
        // cancelled anyways right after it has started (e.g. starting drag while
        // the page is scrolling).
        if (
            !IS_IOS &&
            event.isFirst &&
            event.srcEvent.isTrusted === true &&
            event.srcEvent.defaultPrevented === false &&
            event.srcEvent.cancelable === false
        ) {
            return false
        }

        // Final event logic. At this stage return value does not matter anymore,
        // the predicate is either resolved or it's not and there's nothing to do
        // about it. Here we just reset data and if the item element is a link
        // we follow it (if there has only been slight movement).
        if (event.isFinal) {
            drag._finishStartPredicate(event)
            return
        }

        // Setup predicate data from options if not already set.
        var predicate = drag._startPredicateData
        if (!predicate) {
            var config = options || drag._getGrid()._settings.dragStartPredicate || {}
            drag._startPredicateData = predicate = {
                distance: Math.max(config.distance, 0) || 0,
                delay: Math.max(config.delay, 0) || 0,
            }
        }

        // If delay is defined let's keep track of the latest event and initiate
        // delay if it has not been done yet.
        if (predicate.delay) {
            predicate.event = event
            if (!predicate.delayTimer) {
                predicate.delayTimer = window.setTimeout(function () {
                    predicate.delay = 0
                    if (drag._resolveStartPredicate(predicate.event)) {
                        drag._forceResolveStartPredicate(predicate.event)
                        drag._resetStartPredicate()
                    }
                }, predicate.delay)
            }
        }

        return drag._resolveStartPredicate(event)
    }

    /**
     * Default drag sort predicate.
     *
     * @public
     * @static
     * @param {Item} item
     * @param {Object} [options]
     * @param {Number} [options.threshold=50]
     * @param {String} [options.action='move']
     * @returns {?Object}
     *   - Returns `null` if no valid index was found. Otherwise returns drag sort
     *     command.
     */
    ItemDrag.defaultSortPredicate = (function () {
        var itemRect = {}
        var targetRect = {}
        var returnData = {}
        var gridsArray = []
        var minThreshold = 1
        var maxThreshold = 100

        function getTargetGrid(item, rootGrid, threshold) {
            var target = null
            var dragSort = rootGrid._settings.dragSort
            var bestScore = -1
            var gridScore
            var grids
            var grid
            var container
            var containerRect
            var left
            var top
            var right
            var bottom
            var i

            // Get potential target grids.
            if (dragSort === true) {
                gridsArray[0] = rootGrid
                grids = gridsArray
            } else if (isFunction(dragSort)) {
                grids = dragSort.call(rootGrid, item)
            }

            // Return immediately if there are no grids.
            if (!grids || !Array.isArray(grids) || !grids.length) {
                return target
            }

            // Loop through the grids and get the best match.
            for (i = 0; i < grids.length; i++) {
                grid = grids[i]

                // Filter out all destroyed grids.
                if (grid._isDestroyed) continue

                // Compute the grid's client rect an clamp the initial boundaries to
                // viewport dimensions.
                grid._updateBoundingRect()
                left = Math.max(0, grid._left)
                top = Math.max(0, grid._top)
                right = Math.min(window.innerWidth, grid._right)
                bottom = Math.min(window.innerHeight, grid._bottom)

                // The grid might be inside one or more elements that clip it's visibility
                // (e.g overflow scroll/hidden) so we want to find out the visible portion
                // of the grid in the viewport and use that in our calculations.
                container = grid._element.parentNode
                while (
                    container &&
                    container !== document &&
                    container !== document.documentElement &&
                    container !== document.body
                ) {
                    if (container.getRootNode && container instanceof DocumentFragment) {
                        container = container.getRootNode().host
                        continue
                    }

                    if (getStyle(container, 'overflow') !== 'visible') {
                        containerRect = container.getBoundingClientRect()
                        left = Math.max(left, containerRect.left)
                        top = Math.max(top, containerRect.top)
                        right = Math.min(right, containerRect.right)
                        bottom = Math.min(bottom, containerRect.bottom)
                    }

                    if (getStyle(container, 'position') === 'fixed') {
                        break
                    }

                    container = container.parentNode
                }

                // No need to go further if target rect does not have visible area.
                if (left >= right || top >= bottom) continue

                // Check how much dragged element overlaps the container element.
                targetRect.left = left
                targetRect.top = top
                targetRect.width = right - left
                targetRect.height = bottom - top
                gridScore = getIntersectionScore(itemRect, targetRect)

                // Check if this grid is the best match so far.
                if (gridScore > threshold && gridScore > bestScore) {
                    bestScore = gridScore
                    target = grid
                }
            }

            // Always reset grids array.
            gridsArray.length = 0

            return target
        }

        return function (item, options) {
            var drag = item._drag
            var rootGrid = drag._getGrid()

            // Get drag sort predicate settings.
            var sortThreshold = options && typeof options.threshold === 'number' ? options.threshold : 50
            var sortAction = options && options.action === ACTION_SWAP ? ACTION_SWAP : ACTION_MOVE
            var migrateAction = options && options.migrateAction === ACTION_SWAP ? ACTION_SWAP : ACTION_MOVE

            // Sort threshold must be a positive number capped to a max value of 100. If
            // that's not the case this function will not work correctly. So let's clamp
            // the threshold just in case.
            sortThreshold = Math.min(Math.max(sortThreshold, minThreshold), maxThreshold)

            // Populate item rect data.
            itemRect.width = item._width
            itemRect.height = item._height
            itemRect.left = drag._clientX
            itemRect.top = drag._clientY

            // Calculate the target grid.
            var grid = getTargetGrid(item, rootGrid, sortThreshold)

            // Return early if we found no grid container element that overlaps the
            // dragged item enough.
            if (!grid) return null

            var isMigration = item.getGrid() !== grid
            var gridOffsetLeft = 0
            var gridOffsetTop = 0
            var matchScore = 0
            var matchIndex = -1
            var hasValidTargets = false
            var target
            var score
            var i

            // If item is moved within it's originating grid adjust item's left and
            // top props. Otherwise if item is moved to/within another grid get the
            // container element's offset (from the element's content edge).
            if (grid === rootGrid) {
                itemRect.left = drag._gridX + item._marginLeft
                itemRect.top = drag._gridY + item._marginTop
            } else {
                grid._updateBorders(1, 0, 1, 0)
                gridOffsetLeft = grid._left + grid._borderLeft
                gridOffsetTop = grid._top + grid._borderTop
            }

            // Loop through the target grid items and try to find the best match.
            for (i = 0; i < grid._items.length; i++) {
                target = grid._items[i]

                // If the target item is not active or the target item is the dragged
                // item let's skip to the next item.
                if (!target._isActive || target === item) {
                    continue
                }

                // Mark the grid as having valid target items.
                hasValidTargets = true

                // Calculate the target's overlap score with the dragged item.
                targetRect.width = target._width
                targetRect.height = target._height
                targetRect.left = target._left + target._marginLeft + gridOffsetLeft
                targetRect.top = target._top + target._marginTop + gridOffsetTop
                score = getIntersectionScore(itemRect, targetRect)

                // Update best match index and score if the target's overlap score with
                // the dragged item is higher than the current best match score.
                if (score > matchScore) {
                    matchIndex = i
                    matchScore = score
                }
            }

            // If there is no valid match and the dragged item is being moved into
            // another grid we need to do some guess work here. If there simply are no
            // valid targets (which means that the dragged item will be the only active
            // item in the new grid) we can just add it as the first item. If we have
            // valid items in the new grid and the dragged item is overlapping one or
            // more of the items in the new grid let's make an exception with the
            // threshold and just pick the item which the dragged item is overlapping
            // most. However, if the dragged item is not overlapping any of the valid
            // items in the new grid let's position it as the last item in the grid.
            if (isMigration && matchScore < sortThreshold) {
                matchIndex = hasValidTargets ? matchIndex : 0
                matchScore = sortThreshold
            }

            // Check if the best match overlaps enough to justify a placement switch.
            if (matchScore >= sortThreshold) {
                returnData.grid = grid
                returnData.index = matchIndex
                returnData.action = isMigration ? migrateAction : sortAction
                return returnData
            }

            return null
        }
    })()

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Abort dragging and reset drag data.
     *
     * @public
     */
    ItemDrag.prototype.stop = function () {
        if (!this._isActive) return

        // If the item is being dropped into another grid, finish it up and return
        // immediately.
        if (this._isMigrating) {
            this._finishMigration()
            return
        }

        var item = this._item
        var itemId = item._id

        // Stop auto-scroll.
        ItemDrag.autoScroller.removeItem(item)

        // Cancel queued ticks.
        cancelDragStartTick(itemId)
        cancelDragMoveTick(itemId)
        cancelDragScrollTick(itemId)

        // Cancel sort procedure.
        this._cancelSort()

        if (this._isStarted) {
            // Remove scroll listeners.
            this._unbindScrollListeners()

            var element = item._element
            var grid = this._getGrid()
            var draggingClass = grid._settings.itemDraggingClass

            // Append item element to the container if it's not it's child. Also make
            // sure the translate values are adjusted to account for the DOM shift.
            if (element.parentNode !== grid._element) {
                grid._element.appendChild(element)
                item._setTranslate(this._gridX, this._gridY)
            }

            // Remove dragging class.
            removeClass(element, draggingClass)
        }

        // Reset drag data.
        this._reset()
    }

    /**
     * Manually trigger drag sort. This is only needed for special edge cases where
     * e.g. you have disabled sort and want to trigger a sort right after enabling
     * it (and don't want to wait for the next move/scroll event).
     *
     * @private
     * @param {Boolean} [force=false]
     */
    ItemDrag.prototype.sort = function (force) {
        var item = this._item
        if (this._isActive && item._isActive && this._dragMoveEvent) {
            if (force === true) {
                this._handleSort()
            } else {
                addDragSortTick(item._id, this._handleSort)
            }
        }
    }

    /**
     * Destroy instance.
     *
     * @public
     */
    ItemDrag.prototype.destroy = function () {
        if (this._isDestroyed) return
        this.stop()
        this._dragger.destroy()
        ItemDrag.autoScroller.removeItem(this._item)
        this._isDestroyed = true
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Get Grid instance.
     *
     * @private
     * @returns {?Grid}
     */
    ItemDrag.prototype._getGrid = function () {
        return GRID_INSTANCES[this._gridId] || null
    }

    /**
     * Setup/reset drag data.
     *
     * @private
     */
    ItemDrag.prototype._reset = function () {
        this._isActive = false
        this._isStarted = false

        // The dragged item's container element.
        this._container = null

        // The dragged item's containing block.
        this._containingBlock = null

        // Drag/scroll event data.
        this._dragStartEvent = null
        this._dragMoveEvent = null
        this._dragPrevMoveEvent = null
        this._scrollEvent = null

        // All the elements which need to be listened for scroll events during
        // dragging.
        this._scrollers = []

        // The current translateX/translateY position.
        this._left = 0
        this._top = 0

        // Dragged element's current position within the grid.
        this._gridX = 0
        this._gridY = 0

        // Dragged element's current offset from window's northwest corner. Does
        // not account for element's margins.
        this._clientX = 0
        this._clientY = 0

        // Keep track of the clientX/Y diff for scrolling.
        this._scrollDiffX = 0
        this._scrollDiffY = 0

        // Keep track of the clientX/Y diff for moving.
        this._moveDiffX = 0
        this._moveDiffY = 0

        // Offset difference between the dragged element's temporary drag
        // container and it's original container.
        this._containerDiffX = 0
        this._containerDiffY = 0
    }

    /**
     * Bind drag scroll handlers to all scrollable ancestor elements of the
     * dragged element and the drag container element.
     *
     * @private
     */
    ItemDrag.prototype._bindScrollListeners = function () {
        var gridContainer = this._getGrid()._element
        var dragContainer = this._container
        var scrollers = this._scrollers
        var gridScrollers
        var i

        // Get dragged element's scrolling parents.
        scrollers.length = 0
        getScrollableAncestors(this._item._element.parentNode, scrollers)

        // If drag container is defined and it's not the same element as grid
        // container then we need to add the grid container and it's scroll parents
        // to the elements which are going to be listener for scroll events.
        if (dragContainer !== gridContainer) {
            gridScrollers = []
            getScrollableAncestors(gridContainer, gridScrollers)
            for (i = 0; i < gridScrollers.length; i++) {
                if (scrollers.indexOf(gridScrollers[i]) < 0) {
                    scrollers.push(gridScrollers[i])
                }
            }
        }

        // Bind scroll listeners.
        for (i = 0; i < scrollers.length; i++) {
            scrollers[i].addEventListener('scroll', this._onScroll, SCROLL_LISTENER_OPTIONS)
        }
    }

    /**
     * Unbind currently bound drag scroll handlers from all scrollable ancestor
     * elements of the dragged element and the drag container element.
     *
     * @private
     */
    ItemDrag.prototype._unbindScrollListeners = function () {
        var scrollers = this._scrollers
        var i

        for (i = 0; i < scrollers.length; i++) {
            scrollers[i].removeEventListener('scroll', this._onScroll, SCROLL_LISTENER_OPTIONS)
        }

        scrollers.length = 0
    }

    /**
     * Unbind currently bound drag scroll handlers from all scrollable ancestor
     * elements of the dragged element and the drag container element.
     *
     * @private
     * @param {Object} event
     * @returns {Boolean}
     */
    ItemDrag.prototype._resolveStartPredicate = function (event) {
        var predicate = this._startPredicateData
        if (event.distance < predicate.distance || predicate.delay) return
        this._resetStartPredicate()
        return true
    }

    /**
     * Forcefully resolve drag start predicate.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._forceResolveStartPredicate = function (event) {
        if (!this._isDestroyed && this._startPredicateState === START_PREDICATE_PENDING) {
            this._startPredicateState = START_PREDICATE_RESOLVED
            this._onStart(event)
        }
    }

    /**
     * Finalize start predicate.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._finishStartPredicate = function (event) {
        var element = this._item._element

        // Check if this is a click (very subjective heuristics).
        var isClick = Math.abs(event.deltaX) < 2 && Math.abs(event.deltaY) < 2 && event.deltaTime < 200

        // Reset predicate.
        this._resetStartPredicate()

        // If the gesture can be interpreted as click let's try to open the element's
        // href url (if it is an anchor element).
        if (isClick) openAnchorHref(element)
    }

    /**
     * Reset drag sort heuristics.
     *
     * @private
     * @param {Number} x
     * @param {Number} y
     */
    ItemDrag.prototype._resetHeuristics = function (x, y) {
        this._blockedSortIndex = null
        this._sortX1 = this._sortX2 = x
        this._sortY1 = this._sortY2 = y
    }

    /**
     * Run heuristics and return true if overlap check can be performed, and false
     * if it can not.
     *
     * @private
     * @param {Number} x
     * @param {Number} y
     * @returns {Boolean}
     */
    ItemDrag.prototype._checkHeuristics = function (x, y) {
        var settings = this._getGrid()._settings.dragSortHeuristics
        var minDist = settings.minDragDistance

        // Skip heuristics if not needed.
        if (minDist <= 0) {
            this._blockedSortIndex = null
            return true
        }

        var diffX = x - this._sortX2
        var diffY = y - this._sortY2

        // If we can't do proper bounce back check make sure that the blocked index
        // is not set.
        var canCheckBounceBack = minDist > 3 && settings.minBounceBackAngle > 0
        if (!canCheckBounceBack) {
            this._blockedSortIndex = null
        }

        if (Math.abs(diffX) > minDist || Math.abs(diffY) > minDist) {
            // Reset blocked index if angle changed enough. This check requires a
            // minimum value of 3 for minDragDistance to function properly.
            if (canCheckBounceBack) {
                var angle = Math.atan2(diffX, diffY)
                var prevAngle = Math.atan2(this._sortX2 - this._sortX1, this._sortY2 - this._sortY1)
                var deltaAngle = Math.atan2(Math.sin(angle - prevAngle), Math.cos(angle - prevAngle))
                if (Math.abs(deltaAngle) > settings.minBounceBackAngle) {
                    this._blockedSortIndex = null
                }
            }

            // Update points.
            this._sortX1 = this._sortX2
            this._sortY1 = this._sortY2
            this._sortX2 = x
            this._sortY2 = y

            return true
        }

        return false
    }

    /**
     * Reset for default drag start predicate function.
     *
     * @private
     */
    ItemDrag.prototype._resetStartPredicate = function () {
        var predicate = this._startPredicateData
        if (predicate) {
            if (predicate.delayTimer) {
                predicate.delayTimer = window.clearTimeout(predicate.delayTimer)
            }
            this._startPredicateData = null
        }
    }

    /**
     * Handle the sorting procedure. Manage drag sort heuristics/interval and
     * check overlap when necessary.
     *
     * @private
     */
    ItemDrag.prototype._handleSort = function () {
        if (!this._isActive) return

        var settings = this._getGrid()._settings

        // No sorting when drag sort is disabled. Also, account for the scenario where
        // dragSort is temporarily disabled during drag procedure so we need to reset
        // sort timer heuristics state too.
        if (
            !settings.dragSort ||
            (!settings.dragAutoScroll.sortDuringScroll && ItemDrag.autoScroller.isItemScrolling(this._item))
        ) {
            this._sortX1 = this._sortX2 = this._gridX
            this._sortY1 = this._sortY2 = this._gridY
            // We set this to true intentionally so that overlap check would be
            // triggered as soon as possible after sort becomes enabled again.
            this._isSortNeeded = true
            if (this._sortTimer !== undefined) {
                this._sortTimer = window.clearTimeout(this._sortTimer)
            }
            return
        }

        // If sorting is enabled we always need to run the heuristics check to keep
        // the tracked coordinates updated. We also allow an exception when the sort
        // timer is finished because the heuristics are intended to prevent overlap
        // checks based on the dragged element's immediate movement and a delayed
        // overlap check is valid if it comes through, because it was valid when it
        // was invoked.
        var shouldSort = this._checkHeuristics(this._gridX, this._gridY)
        if (!this._isSortNeeded && !shouldSort) return

        var sortInterval = settings.dragSortHeuristics.sortInterval
        if (sortInterval <= 0 || this._isSortNeeded) {
            this._isSortNeeded = false
            if (this._sortTimer !== undefined) {
                this._sortTimer = window.clearTimeout(this._sortTimer)
            }
            this._checkOverlap()
        } else if (this._sortTimer === undefined) {
            this._sortTimer = window.setTimeout(this._handleSortDelayed, sortInterval)
        }
    }

    /**
     * Delayed sort handler.
     *
     * @private
     */
    ItemDrag.prototype._handleSortDelayed = function () {
        this._isSortNeeded = true
        this._sortTimer = undefined
        addDragSortTick(this._item._id, this._handleSort)
    }

    /**
     * Cancel and reset sort procedure.
     *
     * @private
     */
    ItemDrag.prototype._cancelSort = function () {
        this._isSortNeeded = false
        if (this._sortTimer !== undefined) {
            this._sortTimer = window.clearTimeout(this._sortTimer)
        }
        cancelDragSortTick(this._item._id)
    }

    /**
     * Handle the ending of the drag procedure for sorting.
     *
     * @private
     */
    ItemDrag.prototype._finishSort = function () {
        var isSortEnabled = this._getGrid()._settings.dragSort
        var needsFinalCheck = isSortEnabled && (this._isSortNeeded || this._sortTimer !== undefined)
        this._cancelSort()
        if (needsFinalCheck) this._checkOverlap()
    }

    /**
     * Check (during drag) if an item is overlapping other items and based on
     * the configuration layout the items.
     *
     * @private
     */
    ItemDrag.prototype._checkOverlap = function () {
        if (!this._isActive) return

        var item = this._item
        var settings = this._getGrid()._settings
        var result
        var currentGrid
        var currentIndex
        var targetGrid
        var targetIndex
        var targetItem
        var sortAction
        var isMigration

        // Get overlap check result.
        if (isFunction(settings.dragSortPredicate)) {
            result = settings.dragSortPredicate(item, this._dragMoveEvent)
        } else {
            result = ItemDrag.defaultSortPredicate(item, settings.dragSortPredicate)
        }

        // Let's make sure the result object has a valid index before going further.
        if (!result || typeof result.index !== 'number') return

        sortAction = result.action === ACTION_SWAP ? ACTION_SWAP : ACTION_MOVE
        currentGrid = item.getGrid()
        targetGrid = result.grid || currentGrid
        isMigration = currentGrid !== targetGrid
        currentIndex = currentGrid._items.indexOf(item)
        targetIndex = normalizeArrayIndex(
            targetGrid._items,
            result.index,
            isMigration && sortAction === ACTION_MOVE ? 1 : 0,
        )

        // Prevent position bounce.
        if (!isMigration && targetIndex === this._blockedSortIndex) {
            return
        }

        // If the item was moved within it's current grid.
        if (!isMigration) {
            // Make sure the target index is not the current index.
            if (currentIndex !== targetIndex) {
                this._blockedSortIndex = currentIndex

                // Do the sort.
                ;(sortAction === ACTION_SWAP ? arraySwap : arrayMove)(currentGrid._items, currentIndex, targetIndex)

                // Emit move event.
                if (currentGrid._hasListeners(EVENT_MOVE)) {
                    currentGrid._emit(EVENT_MOVE, {
                        item: item,
                        fromIndex: currentIndex,
                        toIndex: targetIndex,
                        action: sortAction,
                    })
                }

                // Layout the grid.
                currentGrid.layout()
            }
        }

        // If the item was moved to another grid.
        else {
            this._blockedSortIndex = null

            // Let's fetch the target item when it's still in it's original index.
            targetItem = targetGrid._items[targetIndex]

            // Emit beforeSend event.
            if (currentGrid._hasListeners(EVENT_BEFORE_SEND)) {
                currentGrid._emit(EVENT_BEFORE_SEND, {
                    item: item,
                    fromGrid: currentGrid,
                    fromIndex: currentIndex,
                    toGrid: targetGrid,
                    toIndex: targetIndex,
                })
            }

            // Emit beforeReceive event.
            if (targetGrid._hasListeners(EVENT_BEFORE_RECEIVE)) {
                targetGrid._emit(EVENT_BEFORE_RECEIVE, {
                    item: item,
                    fromGrid: currentGrid,
                    fromIndex: currentIndex,
                    toGrid: targetGrid,
                    toIndex: targetIndex,
                })
            }

            // Update item's grid id reference.
            item._gridId = targetGrid._id

            // Update drag instance's migrating indicator.
            this._isMigrating = item._gridId !== this._gridId

            // Move item instance from current grid to target grid.
            currentGrid._items.splice(currentIndex, 1)
            arrayInsert(targetGrid._items, item, targetIndex)

            // Reset sort data.
            item._sortData = null

            // Emit send event.
            if (currentGrid._hasListeners(EVENT_SEND)) {
                currentGrid._emit(EVENT_SEND, {
                    item: item,
                    fromGrid: currentGrid,
                    fromIndex: currentIndex,
                    toGrid: targetGrid,
                    toIndex: targetIndex,
                })
            }

            // Emit receive event.
            if (targetGrid._hasListeners(EVENT_RECEIVE)) {
                targetGrid._emit(EVENT_RECEIVE, {
                    item: item,
                    fromGrid: currentGrid,
                    fromIndex: currentIndex,
                    toGrid: targetGrid,
                    toIndex: targetIndex,
                })
            }

            // If the sort action is "swap" let's respect it and send the target item
            // (if it exists) from the target grid to the originating grid. This process
            // is done on purpose after the dragged item placed within the target grid
            // so that we can keep this implementation as simple as possible utilizing
            // the existing API.
            if (sortAction === ACTION_SWAP && targetItem && targetItem.isActive()) {
                // Sanity check to make sure that the target item is still part of the
                // target grid. It could have been manipulated in the event handlers.
                if (targetGrid._items.indexOf(targetItem) > -1) {
                    targetGrid.send(targetItem, currentGrid, currentIndex, {
                        appendTo: this._container || document.body,
                        layoutSender: false,
                        layoutReceiver: false,
                    })
                }
            }

            // Layout both grids.
            currentGrid.layout()
            targetGrid.layout()
        }
    }

    /**
     * If item is dragged into another grid, finish the migration process
     * gracefully.
     *
     * @private
     */
    ItemDrag.prototype._finishMigration = function () {
        var item = this._item
        var release = item._dragRelease
        var element = item._element
        var isActive = item._isActive
        var targetGrid = item.getGrid()
        var targetGridElement = targetGrid._element
        var targetSettings = targetGrid._settings
        var targetContainer = targetSettings.dragContainer || targetGridElement
        var currentSettings = this._getGrid()._settings
        var currentContainer = element.parentNode
        var currentVisClass = isActive ? currentSettings.itemVisibleClass : currentSettings.itemHiddenClass
        var nextVisClass = isActive ? targetSettings.itemVisibleClass : targetSettings.itemHiddenClass
        var translate
        var offsetDiff

        // Destroy current drag. Note that we need to set the migrating flag to
        // false first, because otherwise we create an infinite loop between this
        // and the drag.stop() method.
        this._isMigrating = false
        this.destroy()

        // Update item class.
        if (currentSettings.itemClass !== targetSettings.itemClass) {
            removeClass(element, currentSettings.itemClass)
            addClass(element, targetSettings.itemClass)
        }

        // Update visibility class.
        if (currentVisClass !== nextVisClass) {
            removeClass(element, currentVisClass)
            addClass(element, nextVisClass)
        }

        // Move the item inside the target container if it's different than the
        // current container.
        if (targetContainer !== currentContainer) {
            targetContainer.appendChild(element)
            offsetDiff = getOffsetDiff(currentContainer, targetContainer, true)
            translate = getTranslate(element)
            translate.x -= offsetDiff.left
            translate.y -= offsetDiff.top
        }

        // Update item's cached dimensions.
        item._refreshDimensions()

        // Calculate the offset difference between target's drag container (if any)
        // and actual grid container element. We save it later for the release
        // process.
        offsetDiff = getOffsetDiff(targetContainer, targetGridElement, true)
        release._containerDiffX = offsetDiff.left
        release._containerDiffY = offsetDiff.top

        // Recreate item's drag handler.
        item._drag = targetSettings.dragEnabled ? new ItemDrag(item) : null

        // Adjust the position of the item element if it was moved from a container
        // to another.
        if (targetContainer !== currentContainer) {
            item._setTranslate(translate.x, translate.y)
        }

        // Update child element's styles to reflect the current visibility state.
        item._visibility.setStyles(isActive ? targetSettings.visibleStyles : targetSettings.hiddenStyles)

        // Start the release.
        release.start()
    }

    /**
     * Drag pre-start handler.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._preStartCheck = function (event) {
        // Let's activate drag start predicate state.
        if (this._startPredicateState === START_PREDICATE_INACTIVE) {
            this._startPredicateState = START_PREDICATE_PENDING
        }

        // If predicate is pending try to resolve it.
        if (this._startPredicateState === START_PREDICATE_PENDING) {
            this._startPredicateResult = this._startPredicate(this._item, event)
            if (this._startPredicateResult === true) {
                this._startPredicateState = START_PREDICATE_RESOLVED
                this._onStart(event)
            } else if (this._startPredicateResult === false) {
                this._resetStartPredicate(event)
                this._dragger._reset()
                this._startPredicateState = START_PREDICATE_INACTIVE
            }
        }

        // Otherwise if predicate is resolved and drag is active, move the item.
        else if (this._startPredicateState === START_PREDICATE_RESOLVED && this._isActive) {
            this._onMove(event)
        }
    }

    /**
     * Drag pre-end handler.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._preEndCheck = function (event) {
        var isResolved = this._startPredicateState === START_PREDICATE_RESOLVED

        // Do final predicate check to allow user to unbind stuff for the current
        // drag procedure within the predicate callback. The return value of this
        // check will have no effect to the state of the predicate.
        this._startPredicate(this._item, event)

        this._startPredicateState = START_PREDICATE_INACTIVE

        if (!isResolved || !this._isActive) return

        if (this._isStarted) {
            this._onEnd(event)
        } else {
            this.stop()
        }
    }

    /**
     * Drag start handler.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._onStart = function (event) {
        var item = this._item
        if (!item._isActive) return

        this._isActive = true
        this._dragStartEvent = event
        ItemDrag.autoScroller.addItem(item)

        addDragStartTick(item._id, this._prepareStart, this._applyStart)
    }

    /**
     * Prepare item to be dragged.
     *
     * @private
     *  ItemDrag.prototype
     */
    ItemDrag.prototype._prepareStart = function () {
        if (!this._isActive) return

        var item = this._item
        if (!item._isActive) return

        var element = item._element
        var grid = this._getGrid()
        var settings = grid._settings
        var gridContainer = grid._element
        var dragContainer = settings.dragContainer || gridContainer
        var containingBlock = getContainingBlock(dragContainer)
        var translate = getTranslate(element)
        var elementRect = element.getBoundingClientRect()
        var hasDragContainer = dragContainer !== gridContainer

        this._container = dragContainer
        this._containingBlock = containingBlock
        this._clientX = elementRect.left
        this._clientY = elementRect.top
        this._left = this._gridX = translate.x
        this._top = this._gridY = translate.y
        this._scrollDiffX = this._scrollDiffY = 0
        this._moveDiffX = this._moveDiffY = 0

        this._resetHeuristics(this._gridX, this._gridY)

        // If a specific drag container is set and it is different from the
        // grid's container element we store the offset between containers.
        if (hasDragContainer) {
            var offsetDiff = getOffsetDiff(containingBlock, gridContainer)
            this._containerDiffX = offsetDiff.left
            this._containerDiffY = offsetDiff.top
        }
    }

    /**
     * Start drag for the item.
     *
     * @private
     */
    ItemDrag.prototype._applyStart = function () {
        if (!this._isActive) return

        var item = this._item
        if (!item._isActive) return

        var grid = this._getGrid()
        var element = item._element
        var release = item._dragRelease
        var migrate = item._migrate
        var hasDragContainer = this._container !== grid._element

        if (item.isPositioning()) {
            item._layout.stop(true, this._left, this._top)
        }

        if (migrate._isActive) {
            this._left -= migrate._containerDiffX
            this._top -= migrate._containerDiffY
            this._gridX -= migrate._containerDiffX
            this._gridY -= migrate._containerDiffY
            migrate.stop(true, this._left, this._top)
        }

        if (item.isReleasing()) {
            release._reset()
        }

        if (grid._settings.dragPlaceholder.enabled) {
            item._dragPlaceholder.create()
        }

        this._isStarted = true

        grid._emit(EVENT_DRAG_INIT, item, this._dragStartEvent)

        if (hasDragContainer) {
            // If the dragged element is a child of the drag container all we need to
            // do is setup the relative drag position data.
            if (element.parentNode === this._container) {
                this._gridX -= this._containerDiffX
                this._gridY -= this._containerDiffY
            }
            // Otherwise we need to append the element inside the correct container,
            // setup the actual drag position data and adjust the element's translate
            // values to account for the DOM position shift.
            else {
                this._left += this._containerDiffX
                this._top += this._containerDiffY
                this._container.appendChild(element)
                item._setTranslate(this._left, this._top)
            }
        }

        addClass(element, grid._settings.itemDraggingClass)
        this._bindScrollListeners()
        grid._emit(EVENT_DRAG_START, item, this._dragStartEvent)
    }

    /**
     * Drag move handler.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._onMove = function (event) {
        var item = this._item

        if (!item._isActive) {
            this.stop()
            return
        }

        this._dragMoveEvent = event
        addDragMoveTick(item._id, this._prepareMove, this._applyMove)
        addDragSortTick(item._id, this._handleSort)
    }

    /**
     * Prepare dragged item for moving.
     *
     * @private
     */
    ItemDrag.prototype._prepareMove = function () {
        if (!this._isActive) return

        var item = this._item
        if (!item._isActive) return

        var settings = this._getGrid()._settings
        var axis = settings.dragAxis
        var nextEvent = this._dragMoveEvent
        var prevEvent = this._dragPrevMoveEvent || this._dragStartEvent || nextEvent

        // Update horizontal position data.
        if (axis !== 'y') {
            var moveDiffX = nextEvent.clientX - prevEvent.clientX
            this._left = this._left - this._moveDiffX + moveDiffX
            this._gridX = this._gridX - this._moveDiffX + moveDiffX
            this._clientX = this._clientX - this._moveDiffX + moveDiffX
            this._moveDiffX = moveDiffX
        }

        // Update vertical position data.
        if (axis !== 'x') {
            var moveDiffY = nextEvent.clientY - prevEvent.clientY
            this._top = this._top - this._moveDiffY + moveDiffY
            this._gridY = this._gridY - this._moveDiffY + moveDiffY
            this._clientY = this._clientY - this._moveDiffY + moveDiffY
            this._moveDiffY = moveDiffY
        }

        this._dragPrevMoveEvent = nextEvent
    }

    /**
     * Apply movement to dragged item.
     *
     * @private
     */
    ItemDrag.prototype._applyMove = function () {
        if (!this._isActive) return

        var item = this._item
        if (!item._isActive) return

        this._moveDiffX = this._moveDiffY = 0
        item._setTranslate(this._left, this._top)
        this._getGrid()._emit(EVENT_DRAG_MOVE, item, this._dragMoveEvent)
        ItemDrag.autoScroller.updateItem(item)
    }

    /**
     * Drag scroll handler.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._onScroll = function (event) {
        var item = this._item

        if (!item._isActive) {
            this.stop()
            return
        }

        this._scrollEvent = event
        addDragScrollTick(item._id, this._prepareScroll, this._applyScroll)
        addDragSortTick(item._id, this._handleSort)
    }

    /**
     * Prepare dragged item for scrolling.
     *
     * @private
     */
    ItemDrag.prototype._prepareScroll = function () {
        if (!this._isActive) return

        // If item is not active do nothing.
        var item = this._item
        if (!item._isActive) return

        var element = item._element
        var grid = this._getGrid()
        var gridContainer = grid._element
        var rect = element.getBoundingClientRect()

        // Update container diff.
        if (this._container !== gridContainer) {
            var offsetDiff = getOffsetDiff(this._containingBlock, gridContainer)
            this._containerDiffX = offsetDiff.left
            this._containerDiffY = offsetDiff.top
        }

        // Update horizontal position data.
        var scrollDiffX = this._clientX - this._moveDiffX - rect.left
        this._left = this._left - this._scrollDiffX + scrollDiffX
        this._scrollDiffX = scrollDiffX

        // Update vertical position data.
        var scrollDiffY = this._clientY - this._moveDiffY - rect.top
        this._top = this._top - this._scrollDiffY + scrollDiffY
        this._scrollDiffY = scrollDiffY

        // Update grid position.
        this._gridX = this._left - this._containerDiffX
        this._gridY = this._top - this._containerDiffY
    }

    /**
     * Apply scroll to dragged item.
     *
     * @private
     */
    ItemDrag.prototype._applyScroll = function () {
        if (!this._isActive) return

        var item = this._item
        if (!item._isActive) return

        this._scrollDiffX = this._scrollDiffY = 0
        item._setTranslate(this._left, this._top)
        this._getGrid()._emit(EVENT_DRAG_SCROLL, item, this._scrollEvent)
    }

    /**
     * Drag end handler.
     *
     * @private
     * @param {Object} event
     */
    ItemDrag.prototype._onEnd = function (event) {
        var item = this._item
        var element = item._element
        var grid = this._getGrid()
        var settings = grid._settings
        var release = item._dragRelease

        // If item is not active, reset drag.
        if (!item._isActive) {
            this.stop()
            return
        }

        // Cancel queued ticks.
        cancelDragStartTick(item._id)
        cancelDragMoveTick(item._id)
        cancelDragScrollTick(item._id)

        // Finish sort procedure (does final overlap check if needed).
        this._finishSort()

        // Remove scroll listeners.
        this._unbindScrollListeners()

        // Setup release data.
        release._containerDiffX = this._containerDiffX
        release._containerDiffY = this._containerDiffY

        // Reset drag data.
        this._reset()

        // Remove drag class name from element.
        removeClass(element, settings.itemDraggingClass)

        // Stop auto-scroll.
        ItemDrag.autoScroller.removeItem(item)

        // Emit dragEnd event.
        grid._emit(EVENT_DRAG_END, item, event)

        // Finish up the migration process or start the release process.
        this._isMigrating ? this._finishMigration() : release.start()
    }

    /**
     * Private helpers
     * ***************
     */

    /**
     * Check if an element is an anchor element and open the href url if possible.
     *
     * @param {HTMLElement} element
     */
    function openAnchorHref(element) {
        // Make sure the element is anchor element.
        if (element.tagName.toLowerCase() !== 'a') return

        // Get href and make sure it exists.
        var href = element.getAttribute('href')
        if (!href) return

        // Finally let's navigate to the link href.
        var target = element.getAttribute('target')
        if (target && target !== '_self') {
            window.open(href, target)
        } else {
            window.location.href = href
        }
    }

    /**
     * Get current values of the provided styles definition object or array.
     *
     * @param {HTMLElement} element
     * @param {(Object|Array} styles
     * @return {Object}
     */
    function getCurrentStyles(element, styles) {
        var result = {}
        var prop, i

        if (Array.isArray(styles)) {
            for (i = 0; i < styles.length; i++) {
                prop = styles[i]
                result[prop] = getStyle(element, getStyleName(prop))
            }
        } else {
            for (prop in styles) {
                result[prop] = getStyle(element, getStyleName(prop))
            }
        }

        return result
    }

    var unprefixRegEx = /^(webkit|moz|ms|o|Webkit|Moz|MS|O)(?=[A-Z])/
    var cache = {}

    /**
     * Remove any potential vendor prefixes from a property name.
     *
     * @param {String} prop
     * @returns {String}
     */
    function getUnprefixedPropName(prop) {
        var result = cache[prop]
        if (result) return result

        result = prop.replace(unprefixRegEx, '')

        if (result !== prop) {
            result = result[0].toLowerCase() + result.slice(1)
        }

        cache[prop] = result

        return result
    }

    var nativeCode = '[native code]'

    /**
     * Check if a value (e.g. a method or constructor) is native code. Good for
     * detecting when a polyfill is used and when not.
     *
     * @param {*} feat
     * @returns {Boolean}
     */
    function isNative(feat) {
        var S = window.Symbol
        return !!(feat && isFunction(S) && isFunction(S.toString) && S(feat).toString().indexOf(nativeCode) > -1)
    }

    /**
     * Set inline styles to an element.
     *
     * @param {HTMLElement} element
     * @param {Object} styles
     */
    function setStyles(element, styles) {
        for (var prop in styles) {
            element.style[prop] = styles[prop]
        }
    }

    var HAS_WEB_ANIMATIONS = !!(Element && isFunction(Element.prototype.animate))
    var HAS_NATIVE_WEB_ANIMATIONS = !!(Element && isNative(Element.prototype.animate))

    /**
     * Item animation handler powered by Web Animations API.
     *
     * @class
     * @param {HTMLElement} element
     */
    function Animator(element) {
        this._element = element
        this._animation = null
        this._duration = 0
        this._easing = ''
        this._callback = null
        this._props = []
        this._values = []
        this._isDestroyed = false
        this._onFinish = this._onFinish.bind(this)
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Start instance's animation. Automatically stops current animation if it is
     * running.
     *
     * @public
     * @param {Object} propsFrom
     * @param {Object} propsTo
     * @param {Object} [options]
     * @param {Number} [options.duration=300]
     * @param {String} [options.easing='ease']
     * @param {Function} [options.onFinish]
     */
    Animator.prototype.start = function (propsFrom, propsTo, options) {
        if (this._isDestroyed) return

        var element = this._element
        var opts = options || {}

        // If we don't have web animations available let's not animate.
        if (!HAS_WEB_ANIMATIONS) {
            setStyles(element, propsTo)
            this._callback = isFunction(opts.onFinish) ? opts.onFinish : null
            this._onFinish()
            return
        }

        var animation = this._animation
        var currentProps = this._props
        var currentValues = this._values
        var duration = opts.duration || 300
        var easing = opts.easing || 'ease'
        var cancelAnimation = false
        var propName, propCount, propIndex

        // If we have an existing animation running, let's check if it needs to be
        // cancelled or if it can continue running.
        if (animation) {
            propCount = 0

            // Cancel animation if duration or easing has changed.
            if (duration !== this._duration || easing !== this._easing) {
                cancelAnimation = true
            }

            // Check if the requested animation target props and values match with the
            // current props and values.
            if (!cancelAnimation) {
                for (propName in propsTo) {
                    ++propCount
                    propIndex = currentProps.indexOf(propName)
                    if (propIndex === -1 || propsTo[propName] !== currentValues[propIndex]) {
                        cancelAnimation = true
                        break
                    }
                }

                // Check if the target props count matches current props count. This is
                // needed for the edge case scenario where target props contain the same
                // styles as current props, but the current props have some additional
                // props.
                if (propCount !== currentProps.length) {
                    cancelAnimation = true
                }
            }
        }

        // Cancel animation (if required).
        if (cancelAnimation) animation.cancel()

        // Store animation callback.
        this._callback = isFunction(opts.onFinish) ? opts.onFinish : null

        // If we have a running animation that does not need to be cancelled, let's
        // call it a day here and let it run.
        if (animation && !cancelAnimation) return

        // Store target props and values to instance.
        currentProps.length = currentValues.length = 0
        for (propName in propsTo) {
            currentProps.push(propName)
            currentValues.push(propsTo[propName])
        }

        // Start the animation. We need to provide unprefixed property names to the
        // Web Animations polyfill if it is being used. If we have native Web
        // Animations available we need to provide prefixed properties instead.
        this._duration = duration
        this._easing = easing
        this._animation = element.animate(
            [createFrame(propsFrom, HAS_NATIVE_WEB_ANIMATIONS), createFrame(propsTo, HAS_NATIVE_WEB_ANIMATIONS)],
            {
                duration: duration,
                easing: easing,
            },
        )
        this._animation.onfinish = this._onFinish

        // Set the end styles. This makes sure that the element stays at the end
        // values after animation is finished.
        setStyles(element, propsTo)
    }

    /**
     * Stop instance's current animation if running.
     *
     * @public
     */
    Animator.prototype.stop = function () {
        if (this._isDestroyed || !this._animation) return
        this._animation.cancel()
        this._animation = this._callback = null
        this._props.length = this._values.length = 0
    }

    /**
     * Read the current values of the element's animated styles from the DOM.
     *
     * @public
     * @return {Object}
     */
    Animator.prototype.getCurrentStyles = function () {
        return getCurrentStyles(element, currentProps)
    }

    /**
     * Check if the item is being animated currently.
     *
     * @public
     * @return {Boolean}
     */
    Animator.prototype.isAnimating = function () {
        return !!this._animation
    }

    /**
     * Destroy the instance and stop current animation if it is running.
     *
     * @public
     */
    Animator.prototype.destroy = function () {
        if (this._isDestroyed) return
        this.stop()
        this._element = null
        this._isDestroyed = true
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Animation end handler.
     *
     * @private
     */
    Animator.prototype._onFinish = function () {
        var callback = this._callback
        this._animation = this._callback = null
        this._props.length = this._values.length = 0
        callback && callback()
    }

    /**
     * Private helpers
     * ***************
     */

    function createFrame(props, prefix) {
        var frame = {}
        for (var prop in props) {
            frame[prefix ? prop : getUnprefixedPropName(prop)] = props[prop]
        }
        return frame
    }

    /**
     * Transform translateX and translateY value into CSS transform style
     * property's value.
     *
     * @param {Number} x
     * @param {Number} y
     * @returns {String}
     */
    function getTranslateString(x, y) {
        return 'translateX(' + x + 'px) translateY(' + y + 'px)'
    }

    /**
     * Drag placeholder.
     *
     * @class
     * @param {Item} item
     */
    function ItemDragPlaceholder(item) {
        this._item = item
        this._animation = new Animator()
        this._element = null
        this._className = ''
        this._didMigrate = false
        this._resetAfterLayout = false
        this._left = 0
        this._top = 0
        this._transX = 0
        this._transY = 0
        this._nextTransX = 0
        this._nextTransY = 0

        // Bind animation handlers.
        this._setupAnimation = this._setupAnimation.bind(this)
        this._startAnimation = this._startAnimation.bind(this)
        this._updateDimensions = this._updateDimensions.bind(this)

        // Bind event handlers.
        this._onLayoutStart = this._onLayoutStart.bind(this)
        this._onLayoutEnd = this._onLayoutEnd.bind(this)
        this._onReleaseEnd = this._onReleaseEnd.bind(this)
        this._onMigrate = this._onMigrate.bind(this)
        this._onHide = this._onHide.bind(this)
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Update placeholder's dimensions to match the item's dimensions.
     *
     * @private
     */
    ItemDragPlaceholder.prototype._updateDimensions = function () {
        if (!this.isActive()) return
        setStyles(this._element, {
            width: this._item._width + 'px',
            height: this._item._height + 'px',
        })
    }

    /**
     * Move placeholder to a new position.
     *
     * @private
     * @param {Item[]} items
     * @param {Boolean} isInstant
     */
    ItemDragPlaceholder.prototype._onLayoutStart = function (items, isInstant) {
        var item = this._item

        // If the item is not part of the layout anymore reset placeholder.
        if (items.indexOf(item) === -1) {
            this.reset()
            return
        }

        var nextLeft = item._left
        var nextTop = item._top
        var currentLeft = this._left
        var currentTop = this._top

        // Keep track of item layout position.
        this._left = nextLeft
        this._top = nextTop

        // If item's position did not change, and the item did not migrate and the
        // layout is not instant and we can safely skip layout.
        if (!isInstant && !this._didMigrate && currentLeft === nextLeft && currentTop === nextTop) {
            return
        }

        // Slots data is calculated with item margins added to them so we need to add
        // item's left and top margin to the slot data to get the placeholder's
        // next position.
        var nextX = nextLeft + item._marginLeft
        var nextY = nextTop + item._marginTop

        // Just snap to new position without any animations if no animation is
        // required or if placeholder moves between grids.
        var grid = item.getGrid()
        var animEnabled = !isInstant && grid._settings.layoutDuration > 0
        if (!animEnabled || this._didMigrate) {
            // Cancel potential (queued) layout tick.
            cancelPlaceholderLayoutTick(item._id)

            // Snap placeholder to correct position.
            this._element.style[transformProp] = getTranslateString(nextX, nextY)
            this._animation.stop()

            // Move placeholder inside correct container after migration.
            if (this._didMigrate) {
                grid.getElement().appendChild(this._element)
                this._didMigrate = false
            }

            return
        }

        // Start the placeholder's layout animation in the next tick. We do this to
        // avoid layout thrashing.
        this._nextTransX = nextX
        this._nextTransY = nextY
        addPlaceholderLayoutTick(item._id, this._setupAnimation, this._startAnimation)
    }

    /**
     * Prepare placeholder for layout animation.
     *
     * @private
     */
    ItemDragPlaceholder.prototype._setupAnimation = function () {
        if (!this.isActive()) return

        var translate = getTranslate(this._element)
        this._transX = translate.x
        this._transY = translate.y
    }

    /**
     * Start layout animation.
     *
     * @private
     */
    ItemDragPlaceholder.prototype._startAnimation = function () {
        if (!this.isActive()) return

        var animation = this._animation
        var currentX = this._transX
        var currentY = this._transY
        var nextX = this._nextTransX
        var nextY = this._nextTransY

        // If placeholder is already in correct position let's just stop animation
        // and be done with it.
        if (currentX === nextX && currentY === nextY) {
            if (animation.isAnimating()) {
                this._element.style[transformProp] = getTranslateString(nextX, nextY)
                animation.stop()
            }
            return
        }

        // Otherwise let's start the animation.
        var settings = this._item.getGrid()._settings
        var currentStyles = {}
        var targetStyles = {}
        currentStyles[transformProp] = getTranslateString(currentX, currentY)
        targetStyles[transformProp] = getTranslateString(nextX, nextY)
        animation.start(currentStyles, targetStyles, {
            duration: settings.layoutDuration,
            easing: settings.layoutEasing,
            onFinish: this._onLayoutEnd,
        })
    }

    /**
     * Layout end handler.
     *
     * @private
     */
    ItemDragPlaceholder.prototype._onLayoutEnd = function () {
        if (this._resetAfterLayout) {
            this.reset()
        }
    }

    /**
     * Drag end handler. This handler is called when dragReleaseEnd event is
     * emitted and receives the event data as it's argument.
     *
     * @private
     * @param {Item} item
     */
    ItemDragPlaceholder.prototype._onReleaseEnd = function (item) {
        if (item._id === this._item._id) {
            // If the placeholder is not animating anymore we can safely reset it.
            if (!this._animation.isAnimating()) {
                this.reset()
                return
            }

            // If the placeholder item is still animating here, let's wait for it to
            // finish it's animation.
            this._resetAfterLayout = true
        }
    }

    /**
     * Migration start handler. This handler is called when beforeSend event is
     * emitted and receives the event data as it's argument.
     *
     * @private
     * @param {Object} data
     * @param {Item} data.item
     * @param {Grid} data.fromGrid
     * @param {Number} data.fromIndex
     * @param {Grid} data.toGrid
     * @param {Number} data.toIndex
     */
    ItemDragPlaceholder.prototype._onMigrate = function (data) {
        // Make sure we have a matching item.
        if (data.item !== this._item) return

        var grid = this._item.getGrid()
        var nextGrid = data.toGrid

        // Unbind listeners from current grid.
        grid.off(EVENT_DRAG_RELEASE_END, this._onReleaseEnd)
        grid.off(EVENT_LAYOUT_START, this._onLayoutStart)
        grid.off(EVENT_BEFORE_SEND, this._onMigrate)
        grid.off(EVENT_HIDE_START, this._onHide)

        // Bind listeners to the next grid.
        nextGrid.on(EVENT_DRAG_RELEASE_END, this._onReleaseEnd)
        nextGrid.on(EVENT_LAYOUT_START, this._onLayoutStart)
        nextGrid.on(EVENT_BEFORE_SEND, this._onMigrate)
        nextGrid.on(EVENT_HIDE_START, this._onHide)

        // Mark the item as migrated.
        this._didMigrate = true
    }

    /**
     * Reset placeholder if the associated item is hidden.
     *
     * @private
     * @param {Item[]} items
     */
    ItemDragPlaceholder.prototype._onHide = function (items) {
        if (items.indexOf(this._item) > -1) this.reset()
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Create placeholder. Note that this method only writes to DOM and does not
     * read anything from DOM so it should not cause any additional layout
     * thrashing when it's called at the end of the drag start procedure.
     *
     * @public
     */
    ItemDragPlaceholder.prototype.create = function () {
        // If we already have placeholder set up we can skip the initiation logic.
        if (this.isActive()) {
            this._resetAfterLayout = false
            return
        }

        var item = this._item
        var grid = item.getGrid()
        var settings = grid._settings
        var animation = this._animation

        // Keep track of layout position.
        this._left = item._left
        this._top = item._top

        // Create placeholder element.
        var element
        if (isFunction(settings.dragPlaceholder.createElement)) {
            element = settings.dragPlaceholder.createElement(item)
        } else {
            element = document.createElement('div')
        }
        this._element = element

        // Update element to animation instance.
        animation._element = element

        // Add placeholder class to the placeholder element.
        this._className = settings.itemPlaceholderClass || ''
        if (this._className) {
            addClass(element, this._className)
        }

        // Set initial styles.
        setStyles(element, {
            position: 'absolute',
            left: '0px',
            top: '0px',
            width: item._width + 'px',
            height: item._height + 'px',
        })

        // Set initial position.
        element.style[transformProp] = getTranslateString(item._left + item._marginLeft, item._top + item._marginTop)

        // Bind event listeners.
        grid.on(EVENT_LAYOUT_START, this._onLayoutStart)
        grid.on(EVENT_DRAG_RELEASE_END, this._onReleaseEnd)
        grid.on(EVENT_BEFORE_SEND, this._onMigrate)
        grid.on(EVENT_HIDE_START, this._onHide)

        // onCreate hook.
        if (isFunction(settings.dragPlaceholder.onCreate)) {
            settings.dragPlaceholder.onCreate(item, element)
        }

        // Insert the placeholder element to the grid.
        grid.getElement().appendChild(element)
    }

    /**
     * Reset placeholder data.
     *
     * @public
     */
    ItemDragPlaceholder.prototype.reset = function () {
        if (!this.isActive()) return

        var element = this._element
        var item = this._item
        var grid = item.getGrid()
        var settings = grid._settings
        var animation = this._animation

        // Reset flag.
        this._resetAfterLayout = false

        // Cancel potential (queued) layout tick.
        cancelPlaceholderLayoutTick(item._id)
        cancelPlaceholderResizeTick(item._id)

        // Reset animation instance.
        animation.stop()
        animation._element = null

        // Unbind event listeners.
        grid.off(EVENT_DRAG_RELEASE_END, this._onReleaseEnd)
        grid.off(EVENT_LAYOUT_START, this._onLayoutStart)
        grid.off(EVENT_BEFORE_SEND, this._onMigrate)
        grid.off(EVENT_HIDE_START, this._onHide)

        // Remove placeholder class from the placeholder element.
        if (this._className) {
            removeClass(element, this._className)
            this._className = ''
        }

        // Remove element.
        element.parentNode.removeChild(element)
        this._element = null

        // onRemove hook. Note that here we use the current grid's onRemove callback
        // so if the item has migrated during drag the onRemove method will not be
        // the originating grid's method.
        if (isFunction(settings.dragPlaceholder.onRemove)) {
            settings.dragPlaceholder.onRemove(item, element)
        }
    }

    /**
     * Check if placeholder is currently active (visible).
     *
     * @public
     * @returns {Boolean}
     */
    ItemDragPlaceholder.prototype.isActive = function () {
        return !!this._element
    }

    /**
     * Get placeholder element.
     *
     * @public
     * @returns {?HTMLElement}
     */
    ItemDragPlaceholder.prototype.getElement = function () {
        return this._element
    }

    /**
     * Update placeholder's dimensions to match the item's dimensions. Note that
     * the updating is done asynchronously in the next tick to avoid layout
     * thrashing.
     *
     * @public
     */
    ItemDragPlaceholder.prototype.updateDimensions = function () {
        if (!this.isActive()) return
        addPlaceholderResizeTick(this._item._id, this._updateDimensions)
    }

    /**
     * Destroy placeholder instance.
     *
     * @public
     */
    ItemDragPlaceholder.prototype.destroy = function () {
        this.reset()
        this._animation.destroy()
        this._item = this._animation = null
    }

    /**
     * The release process handler constructor. Although this might seem as proper
     * fit for the drag process this needs to be separated into it's own logic
     * because there might be a scenario where drag is disabled, but the release
     * process still needs to be implemented (dragging from a grid to another).
     *
     * @class
     * @param {Item} item
     */
    function ItemDragRelease(item) {
        this._item = item
        this._isActive = false
        this._isDestroyed = false
        this._isPositioningStarted = false
        this._containerDiffX = 0
        this._containerDiffY = 0
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Start the release process of an item.
     *
     * @public
     */
    ItemDragRelease.prototype.start = function () {
        if (this._isDestroyed || this._isActive) return

        var item = this._item
        var grid = item.getGrid()
        var settings = grid._settings

        this._isActive = true
        addClass(item._element, settings.itemReleasingClass)
        if (!settings.dragRelease.useDragContainer) {
            this._placeToGrid()
        }
        grid._emit(EVENT_DRAG_RELEASE_START, item)

        // Let's start layout manually _only_ if there is no unfinished layout in
        // about to finish.
        if (!grid._nextLayoutData) item._layout.start(false)
    }

    /**
     * End the release process of an item. This method can be used to abort an
     * ongoing release process (animation) or finish the release process.
     *
     * @public
     * @param {Boolean} [abort=false]
     *  - Should the release be aborted? When true, the release end event won't be
     *    emitted. Set to true only when you need to abort the release process
     *    while the item is animating to it's position.
     * @param {Number} [left]
     *  - The element's current translateX value (optional).
     * @param {Number} [top]
     *  - The element's current translateY value (optional).
     */
    ItemDragRelease.prototype.stop = function (abort, left, top) {
        if (this._isDestroyed || !this._isActive) return

        var item = this._item
        var grid = item.getGrid()

        if (!abort && (left === undefined || top === undefined)) {
            left = item._left
            top = item._top
        }

        var didReparent = this._placeToGrid(left, top)
        this._reset(didReparent)

        if (!abort) grid._emit(EVENT_DRAG_RELEASE_END, item)
    }

    ItemDragRelease.prototype.isJustReleased = function () {
        return this._isActive && this._isPositioningStarted === false
    }

    /**
     * Destroy instance.
     *
     * @public
     */
    ItemDragRelease.prototype.destroy = function () {
        if (this._isDestroyed) return
        this.stop(true)
        this._item = null
        this._isDestroyed = true
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Move the element back to the grid container element if it does not exist
     * there already.
     *
     * @private
     * @param {Number} [left]
     *  - The element's current translateX value (optional).
     * @param {Number} [top]
     *  - The element's current translateY value (optional).
     * @returns {Boolean}
     *   - Returns `true` if the element was reparented.
     */
    ItemDragRelease.prototype._placeToGrid = function (left, top) {
        if (this._isDestroyed) return

        var item = this._item
        var element = item._element
        var container = item.getGrid()._element
        var didReparent = false

        if (element.parentNode !== container) {
            if (left === undefined || top === undefined) {
                var translate = getTranslate(element)
                left = translate.x - this._containerDiffX
                top = translate.y - this._containerDiffY
            }

            container.appendChild(element)
            item._setTranslate(left, top)
            didReparent = true
        }

        this._containerDiffX = 0
        this._containerDiffY = 0

        return didReparent
    }

    /**
     * Reset data and remove releasing class.
     *
     * @private
     * @param {Boolean} [needsReflow]
     */
    ItemDragRelease.prototype._reset = function (needsReflow) {
        if (this._isDestroyed) return

        var item = this._item
        var releasingClass = item.getGrid()._settings.itemReleasingClass

        this._isActive = false
        this._isPositioningStarted = false
        this._containerDiffX = 0
        this._containerDiffY = 0

        // If the element was just reparented we need to do a forced reflow to remove
        // the class gracefully.
        if (releasingClass) {
            removeClass(item._element, releasingClass)
        }
    }

    var MIN_ANIMATION_DISTANCE = 2

    /**
     * Layout manager for Item instance, handles the positioning of an item.
     *
     * @class
     * @param {Item} item
     */
    function ItemLayout(item) {
        var element = item._element
        var elementStyle = element.style

        this._item = item
        this._isActive = false
        this._isDestroyed = false
        this._isInterrupted = false
        this._currentStyles = {}
        this._targetStyles = {}
        this._nextLeft = 0
        this._nextTop = 0
        this._offsetLeft = 0
        this._offsetTop = 0
        this._skipNextAnimation = false
        this._animOptions = {
            onFinish: this._finish.bind(this),
            duration: 0,
            easing: 0,
        }

        // Set element's initial position styles.
        elementStyle.left = '0px'
        elementStyle.top = '0px'
        item._setTranslate(0, 0)

        this._animation = new Animator(element)
        this._queue = 'layout-' + item._id

        // Bind animation handlers and finish method.
        this._setupAnimation = this._setupAnimation.bind(this)
        this._startAnimation = this._startAnimation.bind(this)
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Start item layout based on it's current data.
     *
     * @public
     * @param {Boolean} instant
     * @param {Function} [onFinish]
     */
    ItemLayout.prototype.start = function (instant, onFinish) {
        if (this._isDestroyed) return

        var item = this._item
        var release = item._dragRelease
        var gridSettings = item.getGrid()._settings
        var isPositioning = this._isActive
        var isJustReleased = release.isJustReleased()
        var animDuration = isJustReleased ? gridSettings.dragRelease.duration : gridSettings.layoutDuration
        var animEasing = isJustReleased ? gridSettings.dragRelease.easing : gridSettings.layoutEasing
        var animEnabled = !instant && !this._skipNextAnimation && animDuration > 0

        // If the item is currently positioning cancel potential queued layout tick
        // and process current layout callback queue with interrupted flag on.
        if (isPositioning) {
            cancelLayoutTick(item._id)
            item._emitter.burst(this._queue, true, item)
        }

        // Mark release positioning as started.
        if (isJustReleased) release._isPositioningStarted = true

        // Push the callback to the callback queue.
        if (isFunction(onFinish)) {
            item._emitter.on(this._queue, onFinish)
        }

        // Reset animation skipping flag.
        this._skipNextAnimation = false

        // If no animations are needed, easy peasy!
        if (!animEnabled) {
            this._updateOffsets()
            item._setTranslate(this._nextLeft, this._nextTop)
            this._animation.stop()
            this._finish()
            return
        }

        // Let's make sure an ongoing animation's callback is cancelled before going
        // further. Without this there's a chance that the animation will finish
        // before the next tick and mess up our logic.
        if (this._animation.isAnimating()) {
            this._animation._animation.onfinish = null
        }

        // Kick off animation to be started in the next tick.
        this._isActive = true
        this._animOptions.easing = animEasing
        this._animOptions.duration = animDuration
        this._isInterrupted = isPositioning
        addLayoutTick(item._id, this._setupAnimation, this._startAnimation)
    }

    /**
     * Stop item's position animation if it is currently animating.
     *
     * @public
     * @param {Boolean} processCallbackQueue
     * @param {Number} [left]
     * @param {Number} [top]
     */
    ItemLayout.prototype.stop = function (processCallbackQueue, left, top) {
        if (this._isDestroyed || !this._isActive) return

        var item = this._item

        // Cancel animation init.
        cancelLayoutTick(item._id)

        // Stop animation.
        if (this._animation.isAnimating()) {
            if (left === undefined || top === undefined) {
                var translate = getTranslate(item._element)
                left = translate.x
                top = translate.y
            }
            item._setTranslate(left, top)
            this._animation.stop()
        }

        // Remove positioning class.
        removeClass(item._element, item.getGrid()._settings.itemPositioningClass)

        // Reset active state.
        this._isActive = false

        // Process callback queue if needed.
        if (processCallbackQueue) {
            item._emitter.burst(this._queue, true, item)
        }
    }

    /**
     * Destroy the instance and stop current animation if it is running.
     *
     * @public
     */
    ItemLayout.prototype.destroy = function () {
        if (this._isDestroyed) return

        var elementStyle = this._item._element.style

        this.stop(true, 0, 0)
        this._item._emitter.clear(this._queue)
        this._animation.destroy()

        elementStyle[transformProp] = ''
        elementStyle.left = ''
        elementStyle.top = ''

        this._item = null
        this._currentStyles = null
        this._targetStyles = null
        this._animOptions = null
        this._isDestroyed = true
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Calculate and update item's current layout offset data.
     *
     * @private
     */
    ItemLayout.prototype._updateOffsets = function () {
        if (this._isDestroyed) return

        var item = this._item
        var migrate = item._migrate
        var release = item._dragRelease

        this._offsetLeft = release._isActive ? release._containerDiffX : migrate._isActive ? migrate._containerDiffX : 0

        this._offsetTop = release._isActive ? release._containerDiffY : migrate._isActive ? migrate._containerDiffY : 0

        this._nextLeft = this._item._left + this._offsetLeft
        this._nextTop = this._item._top + this._offsetTop
    }

    /**
     * Finish item layout procedure.
     *
     * @private
     */
    ItemLayout.prototype._finish = function () {
        if (this._isDestroyed) return

        var item = this._item
        var migrate = item._migrate
        var release = item._dragRelease

        // Update internal translate values.
        item._tX = this._nextLeft
        item._tY = this._nextTop

        // Mark the item as inactive and remove positioning classes.
        if (this._isActive) {
            this._isActive = false
            removeClass(item._element, item.getGrid()._settings.itemPositioningClass)
        }

        // Finish up release and migration.
        if (release._isActive) release.stop()
        if (migrate._isActive) migrate.stop()

        // Process the callback queue.
        item._emitter.burst(this._queue, false, item)
    }

    /**
     * Prepare item for layout animation.
     *
     * @private
     */
    ItemLayout.prototype._setupAnimation = function () {
        var item = this._item
        if (item._tX === undefined || item._tY === undefined) {
            var translate = getTranslate(item._element)
            item._tX = translate.x
            item._tY = translate.y
        }
    }

    /**
     * Start layout animation.
     *
     * @private
     */
    ItemLayout.prototype._startAnimation = function () {
        var item = this._item
        var settings = item.getGrid()._settings
        var isInstant = this._animOptions.duration <= 0

        // Let's update the offset data and target styles.
        this._updateOffsets()

        var xDiff = Math.abs(item._left - (item._tX - this._offsetLeft))
        var yDiff = Math.abs(item._top - (item._tY - this._offsetTop))

        // If there is no need for animation or if the item is already in correct
        // position (or near it) let's finish the process early.
        if (isInstant || (xDiff < MIN_ANIMATION_DISTANCE && yDiff < MIN_ANIMATION_DISTANCE)) {
            if (xDiff || yDiff || this._isInterrupted) {
                item._setTranslate(this._nextLeft, this._nextTop)
            }
            this._animation.stop()
            this._finish()
            return
        }

        // Set item's positioning class if needed.
        if (!this._isInterrupted) {
            addClass(item._element, settings.itemPositioningClass)
        }

        // Get current/next styles for animation.
        this._currentStyles[transformProp] = getTranslateString(item._tX, item._tY)
        this._targetStyles[transformProp] = getTranslateString(this._nextLeft, this._nextTop)

        // Set internal translation values to undefined for the duration of the
        // animation since they will be changing on each animation frame for the
        // duration of the animation and tracking them would mean reading the DOM on
        // each frame, which is pretty darn expensive.
        item._tX = item._tY = undefined

        // Start animation.
        this._animation.start(this._currentStyles, this._targetStyles, this._animOptions)
    }

    /**
     * The migrate process handler constructor.
     *
     * @class
     * @param {Item} item
     */
    function ItemMigrate(item) {
        // Private props.
        this._item = item
        this._isActive = false
        this._isDestroyed = false
        this._container = false
        this._containerDiffX = 0
        this._containerDiffY = 0
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Start the migrate process of an item.
     *
     * @public
     * @param {Grid} targetGrid
     * @param {(HTMLElement|Number|Item)} position
     * @param {HTMLElement} [container]
     */
    ItemMigrate.prototype.start = function (targetGrid, position, container) {
        if (this._isDestroyed) return

        var item = this._item
        var element = item._element
        var isActive = item.isActive()
        var isVisible = item.isVisible()
        var grid = item.getGrid()
        var settings = grid._settings
        var targetSettings = targetGrid._settings
        var targetElement = targetGrid._element
        var targetItems = targetGrid._items
        var currentIndex = grid._items.indexOf(item)
        var targetContainer = container || document.body
        var targetIndex
        var targetItem
        var currentContainer
        var offsetDiff
        var containerDiff
        var translate
        var translateX
        var translateY
        var currentVisClass
        var nextVisClass

        // Get target index.
        if (typeof position === 'number') {
            targetIndex = normalizeArrayIndex(targetItems, position, 1)
        } else {
            targetItem = targetGrid.getItem(position)
            if (!targetItem) return
            targetIndex = targetItems.indexOf(targetItem)
        }

        // Get current translateX and translateY values if needed.
        if (item.isPositioning() || this._isActive || item.isReleasing()) {
            translate = getTranslate(element)
            translateX = translate.x
            translateY = translate.y
        }

        // Abort current positioning.
        if (item.isPositioning()) {
            item._layout.stop(true, translateX, translateY)
        }

        // Abort current migration.
        if (this._isActive) {
            translateX -= this._containerDiffX
            translateY -= this._containerDiffY
            this.stop(true, translateX, translateY)
        }

        // Abort current release.
        if (item.isReleasing()) {
            translateX -= item._dragRelease._containerDiffX
            translateY -= item._dragRelease._containerDiffY
            item._dragRelease.stop(true, translateX, translateY)
        }

        // Stop current visibility animation.
        item._visibility.stop(true)

        // Destroy current drag.
        if (item._drag) item._drag.destroy()

        // Emit beforeSend event.
        if (grid._hasListeners(EVENT_BEFORE_SEND)) {
            grid._emit(EVENT_BEFORE_SEND, {
                item: item,
                fromGrid: grid,
                fromIndex: currentIndex,
                toGrid: targetGrid,
                toIndex: targetIndex,
            })
        }

        // Emit beforeReceive event.
        if (targetGrid._hasListeners(EVENT_BEFORE_RECEIVE)) {
            targetGrid._emit(EVENT_BEFORE_RECEIVE, {
                item: item,
                fromGrid: grid,
                fromIndex: currentIndex,
                toGrid: targetGrid,
                toIndex: targetIndex,
            })
        }

        // Update item class.
        if (settings.itemClass !== targetSettings.itemClass) {
            removeClass(element, settings.itemClass)
            addClass(element, targetSettings.itemClass)
        }

        // Update visibility class.
        currentVisClass = isVisible ? settings.itemVisibleClass : settings.itemHiddenClass
        nextVisClass = isVisible ? targetSettings.itemVisibleClass : targetSettings.itemHiddenClass
        if (currentVisClass !== nextVisClass) {
            removeClass(element, currentVisClass)
            addClass(element, nextVisClass)
        }

        // Move item instance from current grid to target grid.
        grid._items.splice(currentIndex, 1)
        arrayInsert(targetItems, item, targetIndex)

        // Update item's grid id reference.
        item._gridId = targetGrid._id

        // If item is active we need to move the item inside the target container for
        // the duration of the (potential) animation if it's different than the
        // current container.
        if (isActive) {
            currentContainer = element.parentNode
            if (targetContainer !== currentContainer) {
                targetContainer.appendChild(element)
                offsetDiff = getOffsetDiff(targetContainer, currentContainer, true)
                if (!translate) {
                    translate = getTranslate(element)
                    translateX = translate.x
                    translateY = translate.y
                }
                item._setTranslate(translateX + offsetDiff.left, translateY + offsetDiff.top)
            }
        }
        // If item is not active let's just append it to the target grid's element.
        else {
            targetElement.appendChild(element)
        }

        // Update child element's styles to reflect the current visibility state.
        item._visibility.setStyles(isVisible ? targetSettings.visibleStyles : targetSettings.hiddenStyles)

        // Get offset diff for the migration data, if the item is active.
        if (isActive) {
            containerDiff = getOffsetDiff(targetContainer, targetElement, true)
        }

        // Update item's cached dimensions.
        item._refreshDimensions()

        // Reset item's sort data.
        item._sortData = null

        // Create new drag handler.
        item._drag = targetSettings.dragEnabled ? new ItemDrag(item) : null

        // Setup migration data.
        if (isActive) {
            this._isActive = true
            this._container = targetContainer
            this._containerDiffX = containerDiff.left
            this._containerDiffY = containerDiff.top
        } else {
            this._isActive = false
            this._container = null
            this._containerDiffX = 0
            this._containerDiffY = 0
        }

        // Emit send event.
        if (grid._hasListeners(EVENT_SEND)) {
            grid._emit(EVENT_SEND, {
                item: item,
                fromGrid: grid,
                fromIndex: currentIndex,
                toGrid: targetGrid,
                toIndex: targetIndex,
            })
        }

        // Emit receive event.
        if (targetGrid._hasListeners(EVENT_RECEIVE)) {
            targetGrid._emit(EVENT_RECEIVE, {
                item: item,
                fromGrid: grid,
                fromIndex: currentIndex,
                toGrid: targetGrid,
                toIndex: targetIndex,
            })
        }
    }

    /**
     * End the migrate process of an item. This method can be used to abort an
     * ongoing migrate process (animation) or finish the migrate process.
     *
     * @public
     * @param {Boolean} [abort=false]
     *  - Should the migration be aborted?
     * @param {Number} [left]
     *  - The element's current translateX value (optional).
     * @param {Number} [top]
     *  - The element's current translateY value (optional).
     */
    ItemMigrate.prototype.stop = function (abort, left, top) {
        if (this._isDestroyed || !this._isActive) return

        var item = this._item
        var element = item._element
        var grid = item.getGrid()
        var gridElement = grid._element
        var translate

        if (this._container !== gridElement) {
            if (left === undefined || top === undefined) {
                if (abort) {
                    translate = getTranslate(element)
                    left = translate.x - this._containerDiffX
                    top = translate.y - this._containerDiffY
                } else {
                    left = item._left
                    top = item._top
                }
            }

            gridElement.appendChild(element)
            item._setTranslate(left, top)
        }

        this._isActive = false
        this._container = null
        this._containerDiffX = 0
        this._containerDiffY = 0
    }

    /**
     * Destroy instance.
     *
     * @public
     */
    ItemMigrate.prototype.destroy = function () {
        if (this._isDestroyed) return
        this.stop(true)
        this._item = null
        this._isDestroyed = true
    }

    /**
     * Visibility manager for Item instance, handles visibility of an item.
     *
     * @class
     * @param {Item} item
     */
    function ItemVisibility(item) {
        var isActive = item._isActive
        var element = item._element
        var childElement = element.children[0]
        var settings = item.getGrid()._settings

        if (!childElement) {
            throw new Error('No valid child element found within item element.')
        }

        this._item = item
        this._isDestroyed = false
        this._isHidden = !isActive
        this._isHiding = false
        this._isShowing = false
        this._childElement = childElement
        this._currentStyleProps = []
        this._animation = new Animator(childElement)
        this._queue = 'visibility-' + item._id
        this._finishShow = this._finishShow.bind(this)
        this._finishHide = this._finishHide.bind(this)

        element.style.display = isActive ? '' : 'none'
        addClass(element, isActive ? settings.itemVisibleClass : settings.itemHiddenClass)
        this.setStyles(isActive ? settings.visibleStyles : settings.hiddenStyles)
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Show item.
     *
     * @public
     * @param {Boolean} instant
     * @param {Function} [onFinish]
     */
    ItemVisibility.prototype.show = function (instant, onFinish) {
        if (this._isDestroyed) return

        var item = this._item
        var element = item._element
        var callback = isFunction(onFinish) ? onFinish : null
        var grid = item.getGrid()
        var settings = grid._settings

        // If item is visible call the callback and be done with it.
        if (!this._isShowing && !this._isHidden) {
            callback && callback(false, item)
            return
        }

        // If item is showing and does not need to be shown instantly, let's just
        // push callback to the callback queue and be done with it.
        if (this._isShowing && !instant) {
            callback && item._emitter.on(this._queue, callback)
            return
        }

        // If the item is hiding or hidden process the current visibility callback
        // queue with the interrupted flag active, update classes and set display
        // to block if necessary.
        if (!this._isShowing) {
            item._emitter.burst(this._queue, true, item)
            removeClass(element, settings.itemHiddenClass)
            addClass(element, settings.itemVisibleClass)
            if (!this._isHiding) element.style.display = ''
        }

        // Push callback to the callback queue.
        callback && item._emitter.on(this._queue, callback)

        // Update visibility states.
        this._isShowing = true
        this._isHiding = this._isHidden = false

        // Finally let's start show animation.
        this._startAnimation(true, instant, this._finishShow)
    }

    /**
     * Hide item.
     *
     * @public
     * @param {Boolean} instant
     * @param {Function} [onFinish]
     */
    ItemVisibility.prototype.hide = function (instant, onFinish) {
        if (this._isDestroyed) return

        var item = this._item
        var element = item._element
        var callback = isFunction(onFinish) ? onFinish : null
        var grid = item.getGrid()
        var settings = grid._settings

        // If item is already hidden call the callback and be done with it.
        if (!this._isHiding && this._isHidden) {
            callback && callback(false, item)
            return
        }

        // If item is hiding and does not need to be hidden instantly, let's just
        // push callback to the callback queue and be done with it.
        if (this._isHiding && !instant) {
            callback && item._emitter.on(this._queue, callback)
            return
        }

        // If the item is showing or visible process the current visibility callback
        // queue with the interrupted flag active, update classes and set display
        // to block if necessary.
        if (!this._isHiding) {
            item._emitter.burst(this._queue, true, item)
            addClass(element, settings.itemHiddenClass)
            removeClass(element, settings.itemVisibleClass)
        }

        // Push callback to the callback queue.
        callback && item._emitter.on(this._queue, callback)

        // Update visibility states.
        this._isHidden = this._isHiding = true
        this._isShowing = false

        // Finally let's start hide animation.
        this._startAnimation(false, instant, this._finishHide)
    }

    /**
     * Stop current hiding/showing process.
     *
     * @public
     * @param {Boolean} processCallbackQueue
     */
    ItemVisibility.prototype.stop = function (processCallbackQueue) {
        if (this._isDestroyed) return
        if (!this._isHiding && !this._isShowing) return

        var item = this._item

        cancelVisibilityTick(item._id)
        this._animation.stop()
        if (processCallbackQueue) {
            item._emitter.burst(this._queue, true, item)
        }
    }

    /**
     * Reset all existing visibility styles and apply new visibility styles to the
     * visibility element. This method should be used to set styles when there is a
     * chance that the current style properties differ from the new ones (basically
     * on init and on migrations).
     *
     * @public
     * @param {Object} styles
     */
    ItemVisibility.prototype.setStyles = function (styles) {
        var childElement = this._childElement
        var currentStyleProps = this._currentStyleProps
        this._removeCurrentStyles()
        for (var prop in styles) {
            currentStyleProps.push(prop)
            childElement.style[prop] = styles[prop]
        }
    }

    /**
     * Destroy the instance and stop current animation if it is running.
     *
     * @public
     */
    ItemVisibility.prototype.destroy = function () {
        if (this._isDestroyed) return

        var item = this._item
        var element = item._element
        var grid = item.getGrid()
        var settings = grid._settings

        this.stop(true)
        item._emitter.clear(this._queue)
        this._animation.destroy()
        this._removeCurrentStyles()
        removeClass(element, settings.itemVisibleClass)
        removeClass(element, settings.itemHiddenClass)
        element.style.display = ''

        // Reset state.
        this._isHiding = this._isShowing = false
        this._isDestroyed = this._isHidden = true
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Start visibility animation.
     *
     * @private
     * @param {Boolean} toVisible
     * @param {Boolean} [instant]
     * @param {Function} [onFinish]
     */
    ItemVisibility.prototype._startAnimation = function (toVisible, instant, onFinish) {
        if (this._isDestroyed) return

        var item = this._item
        var animation = this._animation
        var childElement = this._childElement
        var settings = item.getGrid()._settings
        var targetStyles = toVisible ? settings.visibleStyles : settings.hiddenStyles
        var duration = toVisible ? settings.showDuration : settings.hideDuration
        var easing = toVisible ? settings.showEasing : settings.hideEasing
        var isInstant = instant || duration <= 0
        var currentStyles

        // No target styles? Let's quit early.
        if (!targetStyles) {
            onFinish && onFinish()
            return
        }

        // Cancel queued visibility tick.
        cancelVisibilityTick(item._id)

        // If we need to apply the styles instantly without animation.
        if (isInstant) {
            setStyles(childElement, targetStyles)
            animation.stop()
            onFinish && onFinish()
            return
        }

        // Let's make sure an ongoing animation's callback is cancelled before going
        // further. Without this there's a chance that the animation will finish
        // before the next tick and mess up our logic.
        if (animation.isAnimating()) {
            animation._animation.onfinish = null
        }

        // Start the animation in the next tick (to avoid layout thrashing).
        addVisibilityTick(
            item._id,
            function () {
                currentStyles = getCurrentStyles(childElement, targetStyles)
            },
            function () {
                animation.start(currentStyles, targetStyles, {
                    duration: duration,
                    easing: easing,
                    onFinish: onFinish,
                })
            },
        )
    }

    /**
     * Finish show procedure.
     *
     * @private
     */
    ItemVisibility.prototype._finishShow = function () {
        if (this._isHidden) return
        this._isShowing = false
        this._item._emitter.burst(this._queue, false, this._item)
    }

    /**
     * Finish hide procedure.
     *
     * @private
     */
    ItemVisibility.prototype._finishHide = function () {
        if (!this._isHidden) return
        var item = this._item
        this._isHiding = false
        item._layout.stop(true, 0, 0)
        item._element.style.display = 'none'
        item._emitter.burst(this._queue, false, item)
    }

    /**
     * Remove currently applied visibility related inline style properties.
     *
     * @private
     */
    ItemVisibility.prototype._removeCurrentStyles = function () {
        var childElement = this._childElement
        var currentStyleProps = this._currentStyleProps

        for (var i = 0; i < currentStyleProps.length; i++) {
            childElement.style[currentStyleProps[i]] = ''
        }

        currentStyleProps.length = 0
    }

    var id = 0

    /**
     * Returns a unique numeric id (increments a base value on every call).
     * @returns {Number}
     */
    function createUid() {
        return ++id
    }

    /**
     * Creates a new Item instance for a Grid instance.
     *
     * @class
     * @param {Grid} grid
     * @param {HTMLElement} element
     * @param {Boolean} [isActive]
     */
    function Item(grid, element, isActive) {
        var settings = grid._settings

        // Store item/element pair to a map (for faster item querying by element).
        if (ITEM_ELEMENT_MAP) {
            if (ITEM_ELEMENT_MAP.has(element)) {
                throw new Error('You can only create one Muuri Item per element!')
            } else {
                ITEM_ELEMENT_MAP.set(element, this)
            }
        }

        this._id = createUid()
        this._gridId = grid._id
        this._element = element
        this._isDestroyed = false
        this._left = 0
        this._top = 0
        this._width = 0
        this._height = 0
        this._marginLeft = 0
        this._marginRight = 0
        this._marginTop = 0
        this._marginBottom = 0
        this._tX = undefined
        this._tY = undefined
        this._sortData = null
        this._emitter = new Emitter()

        // If the provided item element is not a direct child of the grid container
        // element, append it to the grid container. Note, we are indeed reading the
        // DOM here but it's a property that does not cause reflowing.
        if (element.parentNode !== grid._element) {
            grid._element.appendChild(element)
        }

        // Set item class.
        addClass(element, settings.itemClass)

        // If isActive is not defined, let's try to auto-detect it. Note, we are
        // indeed reading the DOM here but it's a property that does not cause
        // reflowing.
        if (typeof isActive !== 'boolean') {
            isActive = getStyle(element, 'display') !== 'none'
        }

        // Set up active state (defines if the item is considered part of the layout
        // or not).
        this._isActive = isActive

        // Setup visibility handler.
        this._visibility = new ItemVisibility(this)

        // Set up layout handler.
        this._layout = new ItemLayout(this)

        // Set up migration handler data.
        this._migrate = new ItemMigrate(this)

        // Set up drag handler.
        this._drag = settings.dragEnabled ? new ItemDrag(this) : null

        // Set up release handler. Note that although this is fully linked to dragging
        // this still needs to be always instantiated to handle migration scenarios
        // correctly.
        this._dragRelease = new ItemDragRelease(this)

        // Set up drag placeholder handler. Note that although this is fully linked to
        // dragging this still needs to be always instantiated to handle migration
        // scenarios correctly.
        this._dragPlaceholder = new ItemDragPlaceholder(this)

        // Note! You must call the following methods before you start using the
        // instance. They are deliberately not called in the end as it would cause
        // potentially a massive amount of reflows if multiple items were instantiated
        // in a loop.
        // this._refreshDimensions();
        // this._refreshSortData();
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Get the instance grid reference.
     *
     * @public
     * @returns {Grid}
     */
    Item.prototype.getGrid = function () {
        return GRID_INSTANCES[this._gridId]
    }

    /**
     * Get the instance element.
     *
     * @public
     * @returns {HTMLElement}
     */
    Item.prototype.getElement = function () {
        return this._element
    }

    /**
     * Get instance element's cached width.
     *
     * @public
     * @returns {Number}
     */
    Item.prototype.getWidth = function () {
        return this._width
    }

    /**
     * Get instance element's cached height.
     *
     * @public
     * @returns {Number}
     */
    Item.prototype.getHeight = function () {
        return this._height
    }

    /**
     * Get instance element's cached margins.
     *
     * @public
     * @returns {Object}
     *   - The returned object contains left, right, top and bottom properties
     *     which indicate the item element's cached margins.
     */
    Item.prototype.getMargin = function () {
        return {
            left: this._marginLeft,
            right: this._marginRight,
            top: this._marginTop,
            bottom: this._marginBottom,
        }
    }

    /**
     * Get instance element's cached position.
     *
     * @public
     * @returns {Object}
     *   - The returned object contains left and top properties which indicate the
     *     item element's cached position in the grid.
     */
    Item.prototype.getPosition = function () {
        return {
            left: this._left,
            top: this._top,
        }
    }

    /**
     * Is the item active?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isActive = function () {
        return this._isActive
    }

    /**
     * Is the item visible?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isVisible = function () {
        return !!this._visibility && !this._visibility._isHidden
    }

    /**
     * Is the item being animated to visible?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isShowing = function () {
        return !!(this._visibility && this._visibility._isShowing)
    }

    /**
     * Is the item being animated to hidden?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isHiding = function () {
        return !!(this._visibility && this._visibility._isHiding)
    }

    /**
     * Is the item positioning?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isPositioning = function () {
        return !!(this._layout && this._layout._isActive)
    }

    /**
     * Is the item being dragged (or queued for dragging)?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isDragging = function () {
        return !!(this._drag && this._drag._isActive)
    }

    /**
     * Is the item being released?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isReleasing = function () {
        return !!(this._dragRelease && this._dragRelease._isActive)
    }

    /**
     * Is the item destroyed?
     *
     * @public
     * @returns {Boolean}
     */
    Item.prototype.isDestroyed = function () {
        return this._isDestroyed
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Recalculate item's dimensions.
     *
     * @private
     * @param {Boolean} [force=false]
     */
    Item.prototype._refreshDimensions = function (force) {
        if (this._isDestroyed) return
        if (force !== true && this._visibility._isHidden) return

        var element = this._element
        var dragPlaceholder = this._dragPlaceholder
        var rect = element.getBoundingClientRect()

        // Calculate width and height.
        this._width = rect.width
        this._height = rect.height

        // Calculate margins (ignore negative margins).
        this._marginLeft = Math.max(0, getStyleAsFloat(element, 'margin-left'))
        this._marginRight = Math.max(0, getStyleAsFloat(element, 'margin-right'))
        this._marginTop = Math.max(0, getStyleAsFloat(element, 'margin-top'))
        this._marginBottom = Math.max(0, getStyleAsFloat(element, 'margin-bottom'))

        // Keep drag placeholder's dimensions synced with the item's.
        if (dragPlaceholder) dragPlaceholder.updateDimensions()
    }

    /**
     * Fetch and store item's sort data.
     *
     * @private
     */
    Item.prototype._refreshSortData = function () {
        if (this._isDestroyed) return

        var data = (this._sortData = {})
        var getters = this.getGrid()._settings.sortData
        var prop

        for (prop in getters) {
            data[prop] = getters[prop](this, this._element)
        }
    }

    /**
     * Add item to layout.
     *
     * @private
     */
    Item.prototype._addToLayout = function (left, top) {
        if (this._isActive === true) return
        this._isActive = true
        this._left = left || 0
        this._top = top || 0
    }

    /**
     * Remove item from layout.
     *
     * @private
     */
    Item.prototype._removeFromLayout = function () {
        if (this._isActive === false) return
        this._isActive = false
        this._left = 0
        this._top = 0
    }

    /**
     * Check if the layout procedure can be skipped for the item.
     *
     * @private
     * @param {Number} left
     * @param {Number} top
     * @returns {Boolean}
     */
    Item.prototype._canSkipLayout = function (left, top) {
        return (
            this._left === left &&
            this._top === top &&
            !this._migrate._isActive &&
            !this._layout._skipNextAnimation &&
            !this._dragRelease.isJustReleased()
        )
    }

    /**
     * Set the provided left and top arguments as the item element's translate
     * values in the DOM. This method keeps track of the currently applied
     * translate values and skips the update operation if the provided values are
     * identical to the currently applied values. Returns `false` if there was no
     * need for update and `true` if the translate value was updated.
     *
     * @private
     * @param {Number} left
     * @param {Number} top
     * @returns {Boolean}
     */
    Item.prototype._setTranslate = function (left, top) {
        if (this._tX === left && this._tY === top) return false
        this._tX = left
        this._tY = top
        this._element.style[transformProp] = getTranslateString(left, top)
        return true
    }

    /**
     * Destroy item instance.
     *
     * @private
     * @param {Boolean} [removeElement=false]
     */
    Item.prototype._destroy = function (removeElement) {
        if (this._isDestroyed) return

        var element = this._element
        var grid = this.getGrid()
        var settings = grid._settings

        // Destroy handlers.
        this._dragPlaceholder.destroy()
        this._dragRelease.destroy()
        this._migrate.destroy()
        this._layout.destroy()
        this._visibility.destroy()
        if (this._drag) this._drag.destroy()

        // Destroy emitter.
        this._emitter.destroy()

        // Remove item class.
        removeClass(element, settings.itemClass)

        // Remove element from DOM.
        if (removeElement) element.parentNode.removeChild(element)

        // Remove item/element pair from map.
        if (ITEM_ELEMENT_MAP) ITEM_ELEMENT_MAP.delete(element)

        // Reset state.
        this._isActive = false
        this._isDestroyed = true
    }

    function createPackerProcessor(isWorker) {
        var FILL_GAPS = 1
        var HORIZONTAL = 2
        var ALIGN_RIGHT = 4
        var ALIGN_BOTTOM = 8
        var ROUNDING = 16

        var EPS = 0.001
        var MIN_SLOT_SIZE = 0.5

        // Rounds number first to three decimal precision and then floors the result
        // to two decimal precision.
        // Math.floor(Math.round(number * 1000) / 10) / 100
        function roundNumber(number) {
            return ((((number * 1000 + 0.5) << 0) / 10) << 0) / 100
        }

        /**
         * @class
         */
        function PackerProcessor() {
            this.currentRects = []
            this.nextRects = []
            this.rectTarget = {}
            this.rectStore = []
            this.slotSizes = []
            this.rectId = 0
            this.slotIndex = -1
            this.slotData = { left: 0, top: 0, width: 0, height: 0 }
            this.sortRectsLeftTop = this.sortRectsLeftTop.bind(this)
            this.sortRectsTopLeft = this.sortRectsTopLeft.bind(this)
        }

        /**
         * Takes a layout object as an argument and computes positions (slots) for the
         * layout items. Also computes the final width and height of the layout. The
         * provided layout object's slots array is mutated as well as the width and
         * height properties.
         *
         * @param {Object} layout
         * @param {Number} layout.width
         *   - The start (current) width of the layout in pixels.
         * @param {Number} layout.height
         *   - The start (current) height of the layout in pixels.
         * @param {(Item[]|Number[])} layout.items
         *   - List of Muuri.Item instances or a list of item dimensions
         *     (e.g [ item1Width, item1Height, item2Width, item2Height, ... ]).
         * @param {(Array|Float32Array)} layout.slots
         *   - An Array/Float32Array instance which's length should equal to
         *     the amount of items times two. The position (width and height) of each
         *     item will be written into this array.
         * @param {Number} settings
         *   - The layout's settings as bitmasks.
         * @returns {Object}
         */
        PackerProcessor.prototype.computeLayout = function (layout, settings) {
            var items = layout.items
            var slots = layout.slots
            var fillGaps = !!(settings & FILL_GAPS)
            var horizontal = !!(settings & HORIZONTAL)
            var alignRight = !!(settings & ALIGN_RIGHT)
            var alignBottom = !!(settings & ALIGN_BOTTOM)
            var rounding = !!(settings & ROUNDING)
            var isPreProcessed = typeof items[0] === 'number'
            var i, bump, item, slotWidth, slotHeight, slot

            // No need to go further if items do not exist.
            if (!items.length) return layout

            // Compute slots for the items.
            bump = isPreProcessed ? 2 : 1
            for (i = 0; i < items.length; i += bump) {
                // If items are pre-processed it means that items array contains only
                // the raw dimensions of the items. Otherwise we assume it is an array
                // of normal Muuri items.
                if (isPreProcessed) {
                    slotWidth = items[i]
                    slotHeight = items[i + 1]
                } else {
                    item = items[i]
                    slotWidth = item._width + item._marginLeft + item._marginRight
                    slotHeight = item._height + item._marginTop + item._marginBottom
                }

                // If rounding is enabled let's round the item's width and height to
                // make the layout algorithm a bit more stable. This has a performance
                // cost so don't use this if not necessary.
                if (rounding) {
                    slotWidth = roundNumber(slotWidth)
                    slotHeight = roundNumber(slotHeight)
                }

                // Get slot data.
                slot = this.computeNextSlot(layout, slotWidth, slotHeight, fillGaps, horizontal)

                // Update layout width/height.
                if (horizontal) {
                    if (slot.left + slot.width > layout.width) {
                        layout.width = slot.left + slot.width
                    }
                } else {
                    if (slot.top + slot.height > layout.height) {
                        layout.height = slot.top + slot.height
                    }
                }

                // Add item slot data to layout slots.
                slots[++this.slotIndex] = slot.left
                slots[++this.slotIndex] = slot.top

                // Store the size too (for later usage) if needed.
                if (alignRight || alignBottom) {
                    this.slotSizes.push(slot.width, slot.height)
                }
            }

            // If the alignment is set to right we need to adjust the results.
            if (alignRight) {
                for (i = 0; i < slots.length; i += 2) {
                    slots[i] = layout.width - (slots[i] + this.slotSizes[i])
                }
            }

            // If the alignment is set to bottom we need to adjust the results.
            if (alignBottom) {
                for (i = 1; i < slots.length; i += 2) {
                    slots[i] = layout.height - (slots[i] + this.slotSizes[i])
                }
            }

            // Reset stuff.
            this.slotSizes.length = 0
            this.currentRects.length = 0
            this.nextRects.length = 0
            this.rectStore.length = 0
            this.rectId = 0
            this.slotIndex = -1

            return layout
        }

        /**
         * Calculate next slot in the layout. Returns a slot object with position and
         * dimensions data. The returned object is reused between calls.
         *
         * @param {Object} layout
         * @param {Number} slotWidth
         * @param {Number} slotHeight
         * @param {Boolean} fillGaps
         * @param {Boolean} horizontal
         * @returns {Object}
         */
        PackerProcessor.prototype.computeNextSlot = function (layout, slotWidth, slotHeight, fillGaps, horizontal) {
            var slot = this.slotData
            var currentRects = this.currentRects
            var nextRects = this.nextRects
            var ignoreCurrentRects = false
            var rect
            var rectId
            var shards
            var i
            var j

            // Reset new slots.
            nextRects.length = 0

            // Set item slot initial data.
            slot.left = null
            slot.top = null
            slot.width = slotWidth
            slot.height = slotHeight

            // Try to find position for the slot from the existing free spaces in the
            // layout.
            for (i = 0; i < currentRects.length; i++) {
                rectId = currentRects[i]
                if (!rectId) continue
                rect = this.getRect(rectId)
                if (slot.width <= rect.width + EPS && slot.height <= rect.height + EPS) {
                    slot.left = rect.left
                    slot.top = rect.top
                    break
                }
            }

            // If no position was found for the slot let's position the slot to
            // the bottom left (in vertical mode) or top right (in horizontal mode) of
            // the layout.
            if (slot.left === null) {
                if (horizontal) {
                    slot.left = layout.width
                    slot.top = 0
                } else {
                    slot.left = 0
                    slot.top = layout.height
                }

                // If gaps don't need filling let's throw away all the current free spaces
                // (currentRects).
                if (!fillGaps) {
                    ignoreCurrentRects = true
                }
            }

            // In vertical mode, if the slot's bottom overlaps the layout's bottom.
            if (!horizontal && slot.top + slot.height > layout.height + EPS) {
                // If slot is not aligned to the left edge, create a new free space to the
                // left of the slot.
                if (slot.left > MIN_SLOT_SIZE) {
                    nextRects.push(this.addRect(0, layout.height, slot.left, Infinity))
                }

                // If slot is not aligned to the right edge, create a new free space to
                // the right of the slot.
                if (slot.left + slot.width < layout.width - MIN_SLOT_SIZE) {
                    nextRects.push(
                        this.addRect(
                            slot.left + slot.width,
                            layout.height,
                            layout.width - slot.left - slot.width,
                            Infinity,
                        ),
                    )
                }

                // Update layout height.
                layout.height = slot.top + slot.height
            }

            // In horizontal mode, if the slot's right overlaps the layout's right edge.
            if (horizontal && slot.left + slot.width > layout.width + EPS) {
                // If slot is not aligned to the top, create a new free space above the
                // slot.
                if (slot.top > MIN_SLOT_SIZE) {
                    nextRects.push(this.addRect(layout.width, 0, Infinity, slot.top))
                }

                // If slot is not aligned to the bottom, create a new free space below
                // the slot.
                if (slot.top + slot.height < layout.height - MIN_SLOT_SIZE) {
                    nextRects.push(
                        this.addRect(
                            layout.width,
                            slot.top + slot.height,
                            Infinity,
                            layout.height - slot.top - slot.height,
                        ),
                    )
                }

                // Update layout width.
                layout.width = slot.left + slot.width
            }

            // Clean up the current free spaces making sure none of them overlap with
            // the slot. Split all overlapping free spaces into smaller shards that do
            // not overlap with the slot.
            if (!ignoreCurrentRects) {
                if (fillGaps) i = 0
                for (; i < currentRects.length; i++) {
                    rectId = currentRects[i]
                    if (!rectId) continue
                    rect = this.getRect(rectId)
                    shards = this.splitRect(rect, slot)
                    for (j = 0; j < shards.length; j++) {
                        rectId = shards[j]
                        rect = this.getRect(rectId)
                        // Make sure that the free space is within the boundaries of the
                        // layout. This routine is critical to the algorithm as it makes sure
                        // that there are no leftover spaces with infinite height/width.
                        // It's also essential that we don't compare values absolutely to each
                        // other but leave a little headroom (EPSILON) to get rid of false
                        // positives.
                        if (horizontal ? rect.left + EPS < layout.width - EPS : rect.top + EPS < layout.height - EPS) {
                            nextRects.push(rectId)
                        }
                    }
                }
            }

            // Sanitize and sort all the new free spaces that will be used in the next
            // iteration. This procedure is critical to make the bin-packing algorithm
            // work. The free spaces have to be in correct order in the beginning of the
            // next iteration.
            if (nextRects.length > 1) {
                this.purgeRects(nextRects).sort(horizontal ? this.sortRectsLeftTop : this.sortRectsTopLeft)
            }

            // Finally we need to make sure that `this.currentRects` points to
            // `nextRects` array as that is used in the next iteration's beginning when
            // we try to find a space for the next slot.
            this.currentRects = nextRects
            this.nextRects = currentRects

            return slot
        }

        /**
         * Add a new rectangle to the rectangle store. Returns the id of the new
         * rectangle.
         *
         * @param {Number} left
         * @param {Number} top
         * @param {Number} width
         * @param {Number} height
         * @returns {Number}
         */
        PackerProcessor.prototype.addRect = function (left, top, width, height) {
            var rectId = ++this.rectId
            this.rectStore[rectId] = left || 0
            this.rectStore[++this.rectId] = top || 0
            this.rectStore[++this.rectId] = width || 0
            this.rectStore[++this.rectId] = height || 0
            return rectId
        }

        /**
         * Get rectangle data from the rectangle store by id. Optionally you can
         * provide a target object where the rectangle data will be written in. By
         * default an internal object is reused as a target object.
         *
         * @param {Number} id
         * @param {Object} [target]
         * @returns {Object}
         */
        PackerProcessor.prototype.getRect = function (id, target) {
            if (!target) target = this.rectTarget
            target.left = this.rectStore[id] || 0
            target.top = this.rectStore[++id] || 0
            target.width = this.rectStore[++id] || 0
            target.height = this.rectStore[++id] || 0
            return target
        }

        /**
         * Punch a hole into a rectangle and return the shards (1-4).
         *
         * @param {Object} rect
         * @param {Object} hole
         * @returns {Number[]}
         */
        PackerProcessor.prototype.splitRect = (function () {
            var shards = []
            var width = 0
            var height = 0
            return function (rect, hole) {
                // Reset old shards.
                shards.length = 0

                // If the slot does not overlap with the hole add slot to the return data
                // as is. Note that in this case we are eager to keep the slot as is if
                // possible so we use the EPSILON in favour of that logic.
                if (
                    rect.left + rect.width <= hole.left + EPS ||
                    hole.left + hole.width <= rect.left + EPS ||
                    rect.top + rect.height <= hole.top + EPS ||
                    hole.top + hole.height <= rect.top + EPS
                ) {
                    shards.push(this.addRect(rect.left, rect.top, rect.width, rect.height))
                    return shards
                }

                // Left split.
                width = hole.left - rect.left
                if (width >= MIN_SLOT_SIZE) {
                    shards.push(this.addRect(rect.left, rect.top, width, rect.height))
                }

                // Right split.
                width = rect.left + rect.width - (hole.left + hole.width)
                if (width >= MIN_SLOT_SIZE) {
                    shards.push(this.addRect(hole.left + hole.width, rect.top, width, rect.height))
                }

                // Top split.
                height = hole.top - rect.top
                if (height >= MIN_SLOT_SIZE) {
                    shards.push(this.addRect(rect.left, rect.top, rect.width, height))
                }

                // Bottom split.
                height = rect.top + rect.height - (hole.top + hole.height)
                if (height >= MIN_SLOT_SIZE) {
                    shards.push(this.addRect(rect.left, hole.top + hole.height, rect.width, height))
                }

                return shards
            }
        })()

        /**
         * Check if a rectangle is fully within another rectangle.
         *
         * @param {Object} a
         * @param {Object} b
         * @returns {Boolean}
         */
        PackerProcessor.prototype.isRectAWithinRectB = function (a, b) {
            return (
                a.left + EPS >= b.left &&
                a.top + EPS >= b.top &&
                a.left + a.width - EPS <= b.left + b.width &&
                a.top + a.height - EPS <= b.top + b.height
            )
        }

        /**
         * Loops through an array of rectangle ids and resets all that are fully
         * within another rectangle in the array. Resetting in this case means that
         * the rectangle id value is replaced with zero.
         *
         * @param {Number[]} rectIds
         * @returns {Number[]}
         */
        PackerProcessor.prototype.purgeRects = (function () {
            var rectA = {}
            var rectB = {}
            return function (rectIds) {
                var i = rectIds.length
                var j

                while (i--) {
                    j = rectIds.length
                    if (!rectIds[i]) continue
                    this.getRect(rectIds[i], rectA)
                    while (j--) {
                        if (!rectIds[j] || i === j) continue
                        this.getRect(rectIds[j], rectB)
                        if (this.isRectAWithinRectB(rectA, rectB)) {
                            rectIds[i] = 0
                            break
                        }
                    }
                }

                return rectIds
            }
        })()

        /**
         * Sort rectangles with top-left gravity.
         *
         * @param {Number} aId
         * @param {Number} bId
         * @returns {Number}
         */
        PackerProcessor.prototype.sortRectsTopLeft = (function () {
            var rectA = {}
            var rectB = {}
            return function (aId, bId) {
                this.getRect(aId, rectA)
                this.getRect(bId, rectB)

                return rectA.top < rectB.top && rectA.top + EPS < rectB.top
                    ? -1
                    : rectA.top > rectB.top && rectA.top - EPS > rectB.top
                    ? 1
                    : rectA.left < rectB.left && rectA.left + EPS < rectB.left
                    ? -1
                    : rectA.left > rectB.left && rectA.left - EPS > rectB.left
                    ? 1
                    : 0
            }
        })()

        /**
         * Sort rectangles with left-top gravity.
         *
         * @param {Number} aId
         * @param {Number} bId
         * @returns {Number}
         */
        PackerProcessor.prototype.sortRectsLeftTop = (function () {
            var rectA = {}
            var rectB = {}
            return function (aId, bId) {
                this.getRect(aId, rectA)
                this.getRect(bId, rectB)
                return rectA.left < rectB.left && rectA.left + EPS < rectB.left
                    ? -1
                    : rectA.left > rectB.left && rectA.left - EPS < rectB.left
                    ? 1
                    : rectA.top < rectB.top && rectA.top + EPS < rectB.top
                    ? -1
                    : rectA.top > rectB.top && rectA.top - EPS > rectB.top
                    ? 1
                    : 0
            }
        })()

        if (isWorker) {
            var PACKET_INDEX_WIDTH = 1
            var PACKET_INDEX_HEIGHT = 2
            var PACKET_INDEX_OPTIONS = 3
            var PACKET_HEADER_SLOTS = 4
            var processor = new PackerProcessor()

            self.onmessage = function (msg) {
                var data = new Float32Array(msg.data)
                var items = data.subarray(PACKET_HEADER_SLOTS, data.length)
                var slots = new Float32Array(items.length)
                var settings = data[PACKET_INDEX_OPTIONS]
                var layout = {
                    items: items,
                    slots: slots,
                    width: data[PACKET_INDEX_WIDTH],
                    height: data[PACKET_INDEX_HEIGHT],
                }

                // Compute the layout (width / height / slots).
                processor.computeLayout(layout, settings)

                // Copy layout data to the return data.
                data[PACKET_INDEX_WIDTH] = layout.width
                data[PACKET_INDEX_HEIGHT] = layout.height
                data.set(layout.slots, PACKET_HEADER_SLOTS)

                // Send layout back to the main thread.
                postMessage(data.buffer, [data.buffer])
            }
        }

        return PackerProcessor
    }

    var PackerProcessor = createPackerProcessor()

    //
    // WORKER UTILS
    //

    var blobUrl = null
    var activeWorkers = []

    function createWorkerProcessors(amount, onmessage) {
        var workers = []

        if (amount > 0) {
            if (!blobUrl) {
                blobUrl = URL.createObjectURL(
                    new Blob(['(' + createPackerProcessor.toString() + ')(true)'], {
                        type: 'application/javascript',
                    }),
                )
            }

            for (var i = 0, worker; i < amount; i++) {
                worker = new Worker(blobUrl)
                if (onmessage) worker.onmessage = onmessage
                workers.push(worker)
                activeWorkers.push(worker)
            }
        }

        return workers
    }

    function destroyWorkerProcessors(workers) {
        var worker
        var index

        for (var i = 0; i < workers.length; i++) {
            worker = workers[i]
            worker.onmessage = null
            worker.onerror = null
            worker.onmessageerror = null
            worker.terminate()

            index = activeWorkers.indexOf(worker)
            if (index > -1) activeWorkers.splice(index, 1)
        }

        if (blobUrl && !activeWorkers.length) {
            URL.revokeObjectURL(blobUrl)
            blobUrl = null
        }
    }

    function isWorkerProcessorsSupported() {
        return !!(window.Worker && window.URL && window.Blob)
    }

    var FILL_GAPS = 1
    var HORIZONTAL = 2
    var ALIGN_RIGHT = 4
    var ALIGN_BOTTOM = 8
    var ROUNDING = 16
    var PACKET_INDEX_ID = 0
    var PACKET_INDEX_WIDTH = 1
    var PACKET_INDEX_HEIGHT = 2
    var PACKET_INDEX_OPTIONS = 3
    var PACKET_HEADER_SLOTS = 4

    /**
     * @class
     * @param {Number} [numWorkers=0]
     * @param {Object} [options]
     * @param {Boolean} [options.fillGaps=false]
     * @param {Boolean} [options.horizontal=false]
     * @param {Boolean} [options.alignRight=false]
     * @param {Boolean} [options.alignBottom=false]
     * @param {Boolean} [options.rounding=false]
     */
    function Packer(numWorkers, options) {
        this._options = 0
        this._processor = null
        this._layoutQueue = []
        this._layouts = {}
        this._layoutCallbacks = {}
        this._layoutWorkers = {}
        this._layoutWorkerData = {}
        this._workers = []
        this._onWorkerMessage = this._onWorkerMessage.bind(this)

        // Set initial options.
        this.setOptions(options)

        // Init the worker(s) or the processor if workers can't be used.
        numWorkers = typeof numWorkers === 'number' ? Math.max(0, numWorkers) : 0
        if (numWorkers && isWorkerProcessorsSupported()) {
            try {
                this._workers = createWorkerProcessors(numWorkers, this._onWorkerMessage)
            } catch (e) {
                this._processor = new PackerProcessor()
            }
        } else {
            this._processor = new PackerProcessor()
        }
    }

    Packer.prototype._sendToWorker = function () {
        if (!this._layoutQueue.length || !this._workers.length) return

        var layoutId = this._layoutQueue.shift()
        var worker = this._workers.pop()
        var data = this._layoutWorkerData[layoutId]

        delete this._layoutWorkerData[layoutId]
        this._layoutWorkers[layoutId] = worker
        worker.postMessage(data.buffer, [data.buffer])
    }

    Packer.prototype._onWorkerMessage = function (msg) {
        var data = new Float32Array(msg.data)
        var layoutId = data[PACKET_INDEX_ID]
        var layout = this._layouts[layoutId]
        var callback = this._layoutCallbacks[layoutId]
        var worker = this._layoutWorkers[layoutId]

        if (layout) delete this._layouts[layoutId]
        if (callback) delete this._layoutCallbacks[layoutId]
        if (worker) delete this._layoutWorkers[layoutId]

        if (layout && callback) {
            layout.width = data[PACKET_INDEX_WIDTH]
            layout.height = data[PACKET_INDEX_HEIGHT]
            layout.slots = data.subarray(PACKET_HEADER_SLOTS, data.length)
            this._finalizeLayout(layout)
            callback(layout)
        }

        if (worker) {
            this._workers.push(worker)
            this._sendToWorker()
        }
    }

    Packer.prototype._finalizeLayout = function (layout) {
        var grid = layout._grid
        var isHorizontal = layout._settings & HORIZONTAL
        var isBorderBox = grid._boxSizing === 'border-box'

        delete layout._grid
        delete layout._settings

        layout.styles = {}

        if (isHorizontal) {
            layout.styles.width =
                (isBorderBox ? layout.width + grid._borderLeft + grid._borderRight : layout.width) + 'px'
        } else {
            layout.styles.height =
                (isBorderBox ? layout.height + grid._borderTop + grid._borderBottom : layout.height) + 'px'
        }

        return layout
    }

    /**
     * @public
     * @param {Object} [options]
     * @param {Boolean} [options.fillGaps]
     * @param {Boolean} [options.horizontal]
     * @param {Boolean} [options.alignRight]
     * @param {Boolean} [options.alignBottom]
     * @param {Boolean} [options.rounding]
     */
    Packer.prototype.setOptions = function (options) {
        if (!options) return

        var fillGaps
        if (typeof options.fillGaps === 'boolean') {
            fillGaps = options.fillGaps ? FILL_GAPS : 0
        } else {
            fillGaps = this._options & FILL_GAPS
        }

        var horizontal
        if (typeof options.horizontal === 'boolean') {
            horizontal = options.horizontal ? HORIZONTAL : 0
        } else {
            horizontal = this._options & HORIZONTAL
        }

        var alignRight
        if (typeof options.alignRight === 'boolean') {
            alignRight = options.alignRight ? ALIGN_RIGHT : 0
        } else {
            alignRight = this._options & ALIGN_RIGHT
        }

        var alignBottom
        if (typeof options.alignBottom === 'boolean') {
            alignBottom = options.alignBottom ? ALIGN_BOTTOM : 0
        } else {
            alignBottom = this._options & ALIGN_BOTTOM
        }

        var rounding
        if (typeof options.rounding === 'boolean') {
            rounding = options.rounding ? ROUNDING : 0
        } else {
            rounding = this._options & ROUNDING
        }

        this._options = fillGaps | horizontal | alignRight | alignBottom | rounding
    }

    /**
     * @public
     * @param {Grid} grid
     * @param {Number} layoutId
     * @param {Item[]} items
     * @param {Number} width
     * @param {Number} height
     * @param {Function} callback
     * @returns {?Function}
     */
    Packer.prototype.createLayout = function (grid, layoutId, items, width, height, callback) {
        if (this._layouts[layoutId]) {
            throw new Error('A layout with the provided id is currently being processed.')
        }

        var horizontal = this._options & HORIZONTAL
        var layout = {
            id: layoutId,
            items: items,
            slots: null,
            width: horizontal ? 0 : width,
            height: !horizontal ? 0 : height,
            // Temporary data, which will be removed before sending the layout data
            // outside of Packer's context.
            _grid: grid,
            _settings: this._options,
        }

        // If there are no items let's call the callback immediately.
        if (!items.length) {
            layout.slots = []
            this._finalizeLayout(layout)
            callback(layout)
            return
        }

        // Create layout synchronously if needed.
        if (this._processor) {
            layout.slots = window.Float32Array ? new Float32Array(items.length * 2) : new Array(items.length * 2)
            this._processor.computeLayout(layout, layout._settings)
            this._finalizeLayout(layout)
            callback(layout)
            return
        }

        // Worker data.
        var data = new Float32Array(PACKET_HEADER_SLOTS + items.length * 2)

        // Worker data header.
        data[PACKET_INDEX_ID] = layoutId
        data[PACKET_INDEX_WIDTH] = layout.width
        data[PACKET_INDEX_HEIGHT] = layout.height
        data[PACKET_INDEX_OPTIONS] = layout._settings

        // Worker data items.
        var i, j, item
        for (i = 0, j = PACKET_HEADER_SLOTS - 1, item; i < items.length; i++) {
            item = items[i]
            data[++j] = item._width + item._marginLeft + item._marginRight
            data[++j] = item._height + item._marginTop + item._marginBottom
        }

        this._layoutQueue.push(layoutId)
        this._layouts[layoutId] = layout
        this._layoutCallbacks[layoutId] = callback
        this._layoutWorkerData[layoutId] = data

        this._sendToWorker()

        return this.cancelLayout.bind(this, layoutId)
    }

    /**
     * @public
     * @param {Number} layoutId
     */
    Packer.prototype.cancelLayout = function (layoutId) {
        var layout = this._layouts[layoutId]
        if (!layout) return

        delete this._layouts[layoutId]
        delete this._layoutCallbacks[layoutId]

        if (this._layoutWorkerData[layoutId]) {
            delete this._layoutWorkerData[layoutId]
            var queueIndex = this._layoutQueue.indexOf(layoutId)
            if (queueIndex > -1) this._layoutQueue.splice(queueIndex, 1)
        }
    }

    /**
     * @public
     */
    Packer.prototype.destroy = function () {
        // Move all currently used workers back in the workers array.
        for (var key in this._layoutWorkers) {
            this._workers.push(this._layoutWorkers[key])
        }

        // Destroy all instance's workers.
        destroyWorkerProcessors(this._workers)

        // Reset data.
        this._workers.length = 0
        this._layoutQueue.length = 0
        this._layouts = {}
        this._layoutCallbacks = {}
        this._layoutWorkers = {}
        this._layoutWorkerData = {}
    }

    var debounceId = 0

    /**
     * Returns a function, that, as long as it continues to be invoked, will not
     * be triggered. The function will be called after it stops being called for
     * N milliseconds. The returned function accepts one argument which, when
     * being `true`, cancels the debounce function immediately. When the debounce
     * function is canceled it cannot be invoked again.
     *
     * @param {Function} fn
     * @param {Number} durationMs
     * @returns {Function}
     */
    function debounce(fn, durationMs) {
        var id = ++debounceId
        var timer = 0
        var lastTime = 0
        var isCanceled = false
        var tick = function (time) {
            if (isCanceled) return

            if (lastTime) timer -= time - lastTime
            lastTime = time

            if (timer > 0) {
                addDebounceTick(id, tick)
            } else {
                timer = lastTime = 0
                fn()
            }
        }

        return function (cancel) {
            if (isCanceled) return

            if (durationMs <= 0) {
                if (cancel !== true) fn()
                return
            }

            if (cancel === true) {
                isCanceled = true
                timer = lastTime = 0
                tick = undefined
                cancelDebounceTick(id)
                return
            }

            if (timer <= 0) {
                timer = durationMs
                tick(0)
            } else {
                timer = durationMs
            }
        }
    }

    var htmlCollectionType = '[object HTMLCollection]'
    var nodeListType = '[object NodeList]'

    /**
     * Check if a value is a node list or a html collection.
     *
     * @param {*} val
     * @returns {Boolean}
     */
    function isNodeList(val) {
        var type = Object.prototype.toString.call(val)
        return type === htmlCollectionType || type === nodeListType
    }

    var objectType = 'object'
    var objectToStringType = '[object Object]'
    var toString$1 = Object.prototype.toString

    /**
     * Check if a value is a plain object.
     *
     * @param {*} val
     * @returns {Boolean}
     */
    function isPlainObject(val) {
        return typeof val === objectType && toString$1.call(val) === objectToStringType
    }

    function noop() {}

    /**
     * Converts a value to an array or clones an array.
     *
     * @param {*} val
     * @returns {Array}
     */
    function toArray(val) {
        return isNodeList(val) ? Array.prototype.slice.call(val) : Array.prototype.concat(val)
    }

    var NUMBER_TYPE = 'number'
    var STRING_TYPE = 'string'
    var INSTANT_LAYOUT = 'instant'
    var layoutId = 0

    /**
     * Creates a new Grid instance.
     *
     * @class
     * @param {(HTMLElement|String)} element
     * @param {Object} [options]
     * @param {(String|HTMLElement[]|NodeList|HTMLCollection)} [options.items="*"]
     * @param {Number} [options.showDuration=300]
     * @param {String} [options.showEasing="ease"]
     * @param {Object} [options.visibleStyles={opacity: "1", transform: "scale(1)"}]
     * @param {Number} [options.hideDuration=300]
     * @param {String} [options.hideEasing="ease"]
     * @param {Object} [options.hiddenStyles={opacity: "0", transform: "scale(0.5)"}]
     * @param {(Function|Object)} [options.layout]
     * @param {Boolean} [options.layout.fillGaps=false]
     * @param {Boolean} [options.layout.horizontal=false]
     * @param {Boolean} [options.layout.alignRight=false]
     * @param {Boolean} [options.layout.alignBottom=false]
     * @param {Boolean} [options.layout.rounding=false]
     * @param {(Boolean|Number)} [options.layoutOnResize=150]
     * @param {Boolean} [options.layoutOnInit=true]
     * @param {Number} [options.layoutDuration=300]
     * @param {String} [options.layoutEasing="ease"]
     * @param {?Object} [options.sortData=null]
     * @param {Boolean} [options.dragEnabled=false]
     * @param {?String} [options.dragHandle=null]
     * @param {?HtmlElement} [options.dragContainer=null]
     * @param {?Function} [options.dragStartPredicate]
     * @param {Number} [options.dragStartPredicate.distance=0]
     * @param {Number} [options.dragStartPredicate.delay=0]
     * @param {String} [options.dragAxis="xy"]
     * @param {(Boolean|Function)} [options.dragSort=true]
     * @param {Object} [options.dragSortHeuristics]
     * @param {Number} [options.dragSortHeuristics.sortInterval=100]
     * @param {Number} [options.dragSortHeuristics.minDragDistance=10]
     * @param {Number} [options.dragSortHeuristics.minBounceBackAngle=1]
     * @param {(Function|Object)} [options.dragSortPredicate]
     * @param {Number} [options.dragSortPredicate.threshold=50]
     * @param {String} [options.dragSortPredicate.action="move"]
     * @param {String} [options.dragSortPredicate.migrateAction="move"]
     * @param {Object} [options.dragRelease]
     * @param {Number} [options.dragRelease.duration=300]
     * @param {String} [options.dragRelease.easing="ease"]
     * @param {Boolean} [options.dragRelease.useDragContainer=true]
     * @param {Object} [options.dragCssProps]
     * @param {Object} [options.dragPlaceholder]
     * @param {Boolean} [options.dragPlaceholder.enabled=false]
     * @param {?Function} [options.dragPlaceholder.createElement=null]
     * @param {?Function} [options.dragPlaceholder.onCreate=null]
     * @param {?Function} [options.dragPlaceholder.onRemove=null]
     * @param {Object} [options.dragAutoScroll]
     * @param {(Function|Array)} [options.dragAutoScroll.targets=[]]
     * @param {?Function} [options.dragAutoScroll.handle=null]
     * @param {Number} [options.dragAutoScroll.threshold=50]
     * @param {Number} [options.dragAutoScroll.safeZone=0.2]
     * @param {(Function|Number)} [options.dragAutoScroll.speed]
     * @param {Boolean} [options.dragAutoScroll.sortDuringScroll=true]
     * @param {Boolean} [options.dragAutoScroll.smoothStop=false]
     * @param {?Function} [options.dragAutoScroll.onStart=null]
     * @param {?Function} [options.dragAutoScroll.onStop=null]
     * @param {String} [options.containerClass="muuri"]
     * @param {String} [options.itemClass="muuri-item"]
     * @param {String} [options.itemVisibleClass="muuri-item-visible"]
     * @param {String} [options.itemHiddenClass="muuri-item-hidden"]
     * @param {String} [options.itemPositioningClass="muuri-item-positioning"]
     * @param {String} [options.itemDraggingClass="muuri-item-dragging"]
     * @param {String} [options.itemReleasingClass="muuri-item-releasing"]
     * @param {String} [options.itemPlaceholderClass="muuri-item-placeholder"]
     */
    function Grid(element, options) {
        // Allow passing element as selector string
        if (typeof element === STRING_TYPE) {
            element = document.querySelector(element)
        }

        // Throw an error if the container element is not body element or does not
        // exist within the body element.
        var isElementInDom = element.getRootNode
            ? element.getRootNode({ composed: true }) === document
            : document.body.contains(element)
        if (!isElementInDom || element === document.documentElement) {
            throw new Error('Container element must be an existing DOM element.')
        }

        // Create instance settings by merging the options with default options.
        var settings = mergeSettings(Grid.defaultOptions, options)
        settings.visibleStyles = normalizeStyles(settings.visibleStyles)
        settings.hiddenStyles = normalizeStyles(settings.hiddenStyles)
        if (!isFunction(settings.dragSort)) {
            settings.dragSort = !!settings.dragSort
        }

        this._id = createUid()
        this._element = element
        this._settings = settings
        this._isDestroyed = false
        this._items = []
        this._layout = {
            id: 0,
            items: [],
            slots: [],
        }
        this._isLayoutFinished = true
        this._nextLayoutData = null
        this._emitter = new Emitter()
        this._onLayoutDataReceived = this._onLayoutDataReceived.bind(this)

        // Store grid instance to the grid instances collection.
        GRID_INSTANCES[this._id] = this

        // Add container element's class name.
        addClass(element, settings.containerClass)

        // If layoutOnResize option is a valid number sanitize it and bind the resize
        // handler.
        bindLayoutOnResize(this, settings.layoutOnResize)

        // Add initial items.
        this.add(getInitialGridElements(element, settings.items), { layout: false })

        // Layout on init if necessary.
        if (settings.layoutOnInit) {
            this.layout(true)
        }
    }

    /**
     * Public properties
     * *****************
     */

    /**
     * @public
     * @static
     * @see Item
     */
    Grid.Item = Item

    /**
     * @public
     * @static
     * @see ItemLayout
     */
    Grid.ItemLayout = ItemLayout

    /**
     * @public
     * @static
     * @see ItemVisibility
     */
    Grid.ItemVisibility = ItemVisibility

    /**
     * @public
     * @static
     * @see ItemMigrate
     */
    Grid.ItemMigrate = ItemMigrate

    /**
     * @public
     * @static
     * @see ItemDrag
     */
    Grid.ItemDrag = ItemDrag

    /**
     * @public
     * @static
     * @see ItemDragRelease
     */
    Grid.ItemDragRelease = ItemDragRelease

    /**
     * @public
     * @static
     * @see ItemDragPlaceholder
     */
    Grid.ItemDragPlaceholder = ItemDragPlaceholder

    /**
     * @public
     * @static
     * @see Emitter
     */
    Grid.Emitter = Emitter

    /**
     * @public
     * @static
     * @see Animator
     */
    Grid.Animator = Animator

    /**
     * @public
     * @static
     * @see Dragger
     */
    Grid.Dragger = Dragger

    /**
     * @public
     * @static
     * @see Packer
     */
    Grid.Packer = Packer

    /**
     * @public
     * @static
     * @see AutoScroller
     */
    Grid.AutoScroller = AutoScroller

    /**
     * The default Packer instance used by default for all layouts.
     *
     * @public
     * @static
     * @type {Packer}
     */
    Grid.defaultPacker = new Packer(2)

    /**
     * Default options for Grid instance.
     *
     * @public
     * @static
     * @type {Object}
     */
    Grid.defaultOptions = {
        // Initial item elements
        items: '*',

        // Default show animation
        showDuration: 300,
        showEasing: 'ease',

        // Default hide animation
        hideDuration: 300,
        hideEasing: 'ease',

        // Item's visible/hidden state styles
        visibleStyles: {
            opacity: '1',
            transform: 'scale(1)',
        },
        hiddenStyles: {
            opacity: '0',
            transform: 'scale(0.5)',
        },

        // Layout
        layout: {
            fillGaps: false,
            horizontal: false,
            alignRight: false,
            alignBottom: false,
            rounding: false,
        },
        layoutOnResize: 150,
        layoutOnInit: true,
        layoutDuration: 300,
        layoutEasing: 'ease',

        // Sorting
        sortData: null,

        // Drag & Drop
        dragEnabled: false,
        dragContainer: null,
        dragHandle: null,
        dragStartPredicate: {
            distance: 0,
            delay: 0,
        },
        dragAxis: 'xy',
        dragSort: true,
        dragSortHeuristics: {
            sortInterval: 100,
            minDragDistance: 10,
            minBounceBackAngle: 1,
        },
        dragSortPredicate: {
            threshold: 50,
            action: ACTION_MOVE,
            migrateAction: ACTION_MOVE,
        },
        dragRelease: {
            duration: 300,
            easing: 'ease',
            useDragContainer: true,
        },
        dragCssProps: {
            touchAction: 'none',
            userSelect: 'none',
            userDrag: 'none',
            tapHighlightColor: 'rgba(0, 0, 0, 0)',
            touchCallout: 'none',
            contentZooming: 'none',
        },
        dragPlaceholder: {
            enabled: false,
            createElement: null,
            onCreate: null,
            onRemove: null,
        },
        dragAutoScroll: {
            targets: [],
            handle: null,
            threshold: 50,
            safeZone: 0.2,
            speed: AutoScroller.smoothSpeed(1000, 2000, 2500),
            sortDuringScroll: true,
            smoothStop: false,
            onStart: null,
            onStop: null,
        },

        // Classnames
        containerClass: 'muuri',
        itemClass: 'muuri-item',
        itemVisibleClass: 'muuri-item-shown',
        itemHiddenClass: 'muuri-item-hidden',
        itemPositioningClass: 'muuri-item-positioning',
        itemDraggingClass: 'muuri-item-dragging',
        itemReleasingClass: 'muuri-item-releasing',
        itemPlaceholderClass: 'muuri-item-placeholder',
    }

    /**
     * Public prototype methods
     * ************************
     */

    /**
     * Bind an event listener.
     *
     * @public
     * @param {String} event
     * @param {Function} listener
     * @returns {Grid}
     */
    Grid.prototype.on = function (event, listener) {
        this._emitter.on(event, listener)
        return this
    }

    /**
     * Unbind an event listener.
     *
     * @public
     * @param {String} event
     * @param {Function} listener
     * @returns {Grid}
     */
    Grid.prototype.off = function (event, listener) {
        this._emitter.off(event, listener)
        return this
    }

    /**
     * Get the container element.
     *
     * @public
     * @returns {HTMLElement}
     */
    Grid.prototype.getElement = function () {
        return this._element
    }

    /**
     * Get instance's item by element or by index. Target can also be an Item
     * instance in which case the function returns the item if it exists within
     * related Grid instance. If nothing is found with the provided target, null
     * is returned.
     *
     * @private
     * @param {(HtmlElement|Number|Item)} [target]
     * @returns {?Item}
     */
    Grid.prototype.getItem = function (target) {
        // If no target is specified or the instance is destroyed, return null.
        if (this._isDestroyed || (!target && target !== 0)) {
            return null
        }

        // If target is number return the item in that index. If the number is lower
        // than zero look for the item starting from the end of the items array. For
        // example -1 for the last item, -2 for the second last item, etc.
        if (typeof target === NUMBER_TYPE) {
            return this._items[target > -1 ? target : this._items.length + target] || null
        }

        // If the target is an instance of Item return it if it is attached to this
        // Grid instance, otherwise return null.
        if (target instanceof Item) {
            return target._gridId === this._id ? target : null
        }

        // In other cases let's assume that the target is an element, so let's try
        // to find an item that matches the element and return it. If item is not
        // found return null.
        if (ITEM_ELEMENT_MAP) {
            var item = ITEM_ELEMENT_MAP.get(target)
            return item && item._gridId === this._id ? item : null
        } else {
            for (var i = 0; i < this._items.length; i++) {
                if (this._items[i]._element === target) {
                    return this._items[i]
                }
            }
        }

        return null
    }

    /**
     * Get all items. Optionally you can provide specific targets (elements,
     * indices and item instances). All items that are not found are omitted from
     * the returned array.
     *
     * @public
     * @param {(HtmlElement|Number|Item|Array)} [targets]
     * @returns {Item[]}
     */
    Grid.prototype.getItems = function (targets) {
        // Return all items immediately if no targets were provided or if the
        // instance is destroyed.
        if (this._isDestroyed || targets === undefined) {
            return this._items.slice(0)
        }

        var items = []
        var i, item

        if (Array.isArray(targets) || isNodeList(targets)) {
            for (i = 0; i < targets.length; i++) {
                item = this.getItem(targets[i])
                if (item) items.push(item)
            }
        } else {
            item = this.getItem(targets)
            if (item) items.push(item)
        }

        return items
    }

    /**
     * Update the cached dimensions of the instance's items. By default all the
     * items are refreshed, but you can also provide an array of target items as the
     * first argument if you want to refresh specific items. Note that all hidden
     * items are not refreshed by default since their "display" property is "none"
     * and their dimensions are therefore not readable from the DOM. However, if you
     * do want to force update hidden item dimensions too you can provide `true`
     * as the second argument, which makes the elements temporarily visible while
     * their dimensions are being read.
     *
     * @public
     * @param {Item[]} [items]
     * @param {Boolean} [force=false]
     * @returns {Grid}
     */
    Grid.prototype.refreshItems = function (items, force) {
        if (this._isDestroyed) return this

        var targets = items || this._items
        var i, item, style, hiddenItemStyles

        if (force === true) {
            hiddenItemStyles = []
            for (i = 0; i < targets.length; i++) {
                item = targets[i]
                if (!item.isVisible() && !item.isHiding()) {
                    style = item.getElement().style
                    style.visibility = 'hidden'
                    style.display = ''
                    hiddenItemStyles.push(style)
                }
            }
        }

        for (i = 0; i < targets.length; i++) {
            targets[i]._refreshDimensions(force)
        }

        if (force === true) {
            for (i = 0; i < hiddenItemStyles.length; i++) {
                style = hiddenItemStyles[i]
                style.visibility = ''
                style.display = 'none'
            }
            hiddenItemStyles.length = 0
        }

        return this
    }

    /**
     * Update the sort data of the instance's items. By default all the items are
     * refreshed, but you can also provide an array of target items if you want to
     * refresh specific items.
     *
     * @public
     * @param {Item[]} [items]
     * @returns {Grid}
     */
    Grid.prototype.refreshSortData = function (items) {
        if (this._isDestroyed) return this

        var targets = items || this._items
        for (var i = 0; i < targets.length; i++) {
            targets[i]._refreshSortData()
        }

        return this
    }

    /**
     * Synchronize the item elements to match the order of the items in the DOM.
     * This comes handy if you need to keep the DOM structure matched with the
     * order of the items. Note that if an item's element is not currently a child
     * of the container element (if it is dragged for example) it is ignored and
     * left untouched.
     *
     * @public
     * @returns {Grid}
     */
    Grid.prototype.synchronize = function () {
        if (this._isDestroyed) return this

        var items = this._items
        if (!items.length) return this

        var fragment
        var element

        for (var i = 0; i < items.length; i++) {
            element = items[i]._element
            if (element.parentNode === this._element) {
                fragment = fragment || document.createDocumentFragment()
                fragment.appendChild(element)
            }
        }

        if (!fragment) return this

        this._element.appendChild(fragment)
        this._emit(EVENT_SYNCHRONIZE)

        return this
    }

    /**
     * Calculate and apply item positions.
     *
     * @public
     * @param {Boolean} [instant=false]
     * @param {Function} [onFinish]
     * @returns {Grid}
     */
    Grid.prototype.layout = function (instant, onFinish) {
        if (this._isDestroyed) return this

        // Cancel unfinished layout algorithm if possible.
        var unfinishedLayout = this._nextLayoutData
        if (unfinishedLayout && isFunction(unfinishedLayout.cancel)) {
            unfinishedLayout.cancel()
        }

        // Compute layout id (let's stay in Float32 range).
        layoutId = (layoutId % MAX_SAFE_FLOAT32_INTEGER) + 1
        var nextLayoutId = layoutId

        // Store data for next layout.
        this._nextLayoutData = {
            id: nextLayoutId,
            instant: instant,
            onFinish: onFinish,
            cancel: null,
        }

        // Collect layout items (all active grid items).
        var items = this._items
        var layoutItems = []
        for (var i = 0; i < items.length; i++) {
            if (items[i]._isActive) layoutItems.push(items[i])
        }

        // Compute new layout.
        this._refreshDimensions()
        var gridWidth = this._width - this._borderLeft - this._borderRight
        var gridHeight = this._height - this._borderTop - this._borderBottom
        var layoutSettings = this._settings.layout
        var cancelLayout
        if (isFunction(layoutSettings)) {
            cancelLayout = layoutSettings(
                this,
                nextLayoutId,
                layoutItems,
                gridWidth,
                gridHeight,
                this._onLayoutDataReceived,
            )
        } else {
            Grid.defaultPacker.setOptions(layoutSettings)
            cancelLayout = Grid.defaultPacker.createLayout(
                this,
                nextLayoutId,
                layoutItems,
                gridWidth,
                gridHeight,
                this._onLayoutDataReceived,
            )
        }

        // Store layout cancel method if available.
        if (isFunction(cancelLayout) && this._nextLayoutData && this._nextLayoutData.id === nextLayoutId) {
            this._nextLayoutData.cancel = cancelLayout
        }

        return this
    }

    /**
     * Add new items by providing the elements you wish to add to the instance and
     * optionally provide the index where you want the items to be inserted into.
     * All elements that are not already children of the container element will be
     * automatically appended to the container element. If an element has it's CSS
     * display property set to "none" it will be marked as inactive during the
     * initiation process. As long as the item is inactive it will not be part of
     * the layout, but it will retain it's index. You can activate items at any
     * point with grid.show() method. This method will automatically call
     * grid.layout() if one or more of the added elements are visible. If only
     * hidden items are added no layout will be called. All the new visible items
     * are positioned without animation during their first layout.
     *
     * @public
     * @param {(HTMLElement|HTMLElement[])} elements
     * @param {Object} [options]
     * @param {Number} [options.index=-1]
     * @param {Boolean} [options.active]
     * @param {(Boolean|Function|String)} [options.layout=true]
     * @returns {Item[]}
     */
    Grid.prototype.add = function (elements, options) {
        if (this._isDestroyed || !elements) return []

        var newItems = toArray(elements)
        if (!newItems.length) return newItems

        var opts = options || {}
        var layout = opts.layout ? opts.layout : opts.layout === undefined
        var items = this._items
        var needsLayout = false
        var fragment
        var element
        var item
        var i

        // Collect all the elements that are not child of the grid element into a
        // document fragment.
        for (i = 0; i < newItems.length; i++) {
            element = newItems[i]
            if (element.parentNode !== this._element) {
                fragment = fragment || document.createDocumentFragment()
                fragment.appendChild(element)
            }
        }

        // If we have a fragment, let's append it to the grid element. We could just
        // not do this and the `new Item()` instantiation would handle this for us,
        // but this way we can add the elements into the DOM a bit faster.
        if (fragment) {
            this._element.appendChild(fragment)
        }

        // Map provided elements into new grid items.
        for (i = 0; i < newItems.length; i++) {
            element = newItems[i]
            item = newItems[i] = new Item(this, element, opts.active)

            // If the item to be added is active, we need to do a layout. Also, we
            // need to mark the item with the skipNextAnimation flag to make it
            // position instantly (without animation) during the next layout. Without
            // the hack the item would animate to it's new position from the northwest
            // corner of the grid, which feels a bit buggy (imho).
            if (item._isActive) {
                needsLayout = true
                item._layout._skipNextAnimation = true
            }
        }

        // Set up the items' initial dimensions and sort data. This needs to be done
        // in a separate loop to avoid layout thrashing.
        for (i = 0; i < newItems.length; i++) {
            item = newItems[i]
            item._refreshDimensions()
            item._refreshSortData()
        }

        // Add the new items to the items collection to correct index.
        arrayInsert(items, newItems, opts.index)

        // Emit add event.
        if (this._hasListeners(EVENT_ADD)) {
            this._emit(EVENT_ADD, newItems.slice(0))
        }

        // If layout is needed.
        if (needsLayout && layout) {
            this.layout(layout === INSTANT_LAYOUT, isFunction(layout) ? layout : undefined)
        }

        return newItems
    }

    /**
     * Remove items from the instance.
     *
     * @public
     * @param {Item[]} items
     * @param {Object} [options]
     * @param {Boolean} [options.removeElements=false]
     * @param {(Boolean|Function|String)} [options.layout=true]
     * @returns {Item[]}
     */
    Grid.prototype.remove = function (items, options) {
        if (this._isDestroyed || !items.length) return []

        var opts = options || {}
        var layout = opts.layout ? opts.layout : opts.layout === undefined
        var needsLayout = false
        var allItems = this.getItems()
        var targetItems = []
        var indices = []
        var index
        var item
        var i

        // Remove the individual items.
        for (i = 0; i < items.length; i++) {
            item = items[i]
            if (item._isDestroyed) continue

            index = this._items.indexOf(item)
            if (index === -1) continue

            if (item._isActive) needsLayout = true

            targetItems.push(item)
            indices.push(allItems.indexOf(item))
            item._destroy(opts.removeElements)
            this._items.splice(index, 1)
        }

        // Emit remove event.
        if (this._hasListeners(EVENT_REMOVE)) {
            this._emit(EVENT_REMOVE, targetItems.slice(0), indices)
        }

        // If layout is needed.
        if (needsLayout && layout) {
            this.layout(layout === INSTANT_LAYOUT, isFunction(layout) ? layout : undefined)
        }

        return targetItems
    }

    /**
     * Show specific instance items.
     *
     * @public
     * @param {Item[]} items
     * @param {Object} [options]
     * @param {Boolean} [options.instant=false]
     * @param {Boolean} [options.syncWithLayout=true]
     * @param {Function} [options.onFinish]
     * @param {(Boolean|Function|String)} [options.layout=true]
     * @returns {Grid}
     */
    Grid.prototype.show = function (items, options) {
        if (!this._isDestroyed && items.length) {
            this._setItemsVisibility(items, true, options)
        }
        return this
    }

    /**
     * Hide specific instance items.
     *
     * @public
     * @param {Item[]} items
     * @param {Object} [options]
     * @param {Boolean} [options.instant=false]
     * @param {Boolean} [options.syncWithLayout=true]
     * @param {Function} [options.onFinish]
     * @param {(Boolean|Function|String)} [options.layout=true]
     * @returns {Grid}
     */
    Grid.prototype.hide = function (items, options) {
        if (!this._isDestroyed && items.length) {
            this._setItemsVisibility(items, false, options)
        }
        return this
    }

    /**
     * Filter items. Expects at least one argument, a predicate, which should be
     * either a function or a string. The predicate callback is executed for every
     * item in the instance. If the return value of the predicate is truthy the
     * item in question will be shown and otherwise hidden. The predicate callback
     * receives the item instance as it's argument. If the predicate is a string
     * it is considered to be a selector and it is checked against every item
     * element in the instance with the native element.matches() method. All the
     * matching items will be shown and others hidden.
     *
     * @public
     * @param {(Function|String)} predicate
     * @param {Object} [options]
     * @param {Boolean} [options.instant=false]
     * @param {Boolean} [options.syncWithLayout=true]
     * @param {FilterCallback} [options.onFinish]
     * @param {(Boolean|Function|String)} [options.layout=true]
     * @returns {Grid}
     */
    Grid.prototype.filter = function (predicate, options) {
        if (this._isDestroyed || !this._items.length) return this

        var itemsToShow = []
        var itemsToHide = []
        var isPredicateString = typeof predicate === STRING_TYPE
        var isPredicateFn = isFunction(predicate)
        var opts = options || {}
        var isInstant = opts.instant === true
        var syncWithLayout = opts.syncWithLayout
        var layout = opts.layout ? opts.layout : opts.layout === undefined
        var onFinish = isFunction(opts.onFinish) ? opts.onFinish : null
        var tryFinishCounter = -1
        var tryFinish = noop
        var item
        var i

        // If we have onFinish callback, let's create proper tryFinish callback.
        if (onFinish) {
            tryFinish = function () {
                ++tryFinishCounter && onFinish(itemsToShow.slice(0), itemsToHide.slice(0))
            }
        }

        // Check which items need to be shown and which hidden.
        if (isPredicateFn || isPredicateString) {
            for (i = 0; i < this._items.length; i++) {
                item = this._items[i]
                if (isPredicateFn ? predicate(item) : elementMatches(item._element, predicate)) {
                    itemsToShow.push(item)
                } else {
                    itemsToHide.push(item)
                }
            }
        }

        // Show items that need to be shown.
        if (itemsToShow.length) {
            this.show(itemsToShow, {
                instant: isInstant,
                syncWithLayout: syncWithLayout,
                onFinish: tryFinish,
                layout: false,
            })
        } else {
            tryFinish()
        }

        // Hide items that need to be hidden.
        if (itemsToHide.length) {
            this.hide(itemsToHide, {
                instant: isInstant,
                syncWithLayout: syncWithLayout,
                onFinish: tryFinish,
                layout: false,
            })
        } else {
            tryFinish()
        }

        // If there are any items to filter.
        if (itemsToShow.length || itemsToHide.length) {
            // Emit filter event.
            if (this._hasListeners(EVENT_FILTER)) {
                this._emit(EVENT_FILTER, itemsToShow.slice(0), itemsToHide.slice(0))
            }

            // If layout is needed.
            if (layout) {
                this.layout(layout === INSTANT_LAYOUT, isFunction(layout) ? layout : undefined)
            }
        }

        return this
    }

    /**
     * Sort items. There are three ways to sort the items. The first is simply by
     * providing a function as the comparer which works identically to native
     * array sort. Alternatively you can sort by the sort data you have provided
     * in the instance's options. Just provide the sort data key(s) as a string
     * (separated by space) and the items will be sorted based on the provided
     * sort data keys. Lastly you have the opportunity to provide a presorted
     * array of items which will be used to sync the internal items array in the
     * same order.
     *
     * @public
     * @param {(Function|String|Item[])} comparer
     * @param {Object} [options]
     * @param {Boolean} [options.descending=false]
     * @param {(Boolean|Function|String)} [options.layout=true]
     * @returns {Grid}
     */
    Grid.prototype.sort = (function () {
        var sortComparer
        var isDescending
        var origItems
        var indexMap

        function defaultComparer(a, b) {
            var result = 0
            var criteriaName
            var criteriaOrder
            var valA
            var valB

            // Loop through the list of sort criteria.
            for (var i = 0; i < sortComparer.length; i++) {
                // Get the criteria name, which should match an item's sort data key.
                criteriaName = sortComparer[i][0]
                criteriaOrder = sortComparer[i][1]

                // Get items' cached sort values for the criteria. If the item has no sort
                // data let's update the items sort data (this is a lazy load mechanism).
                valA = (a._sortData ? a : a._refreshSortData())._sortData[criteriaName]
                valB = (b._sortData ? b : b._refreshSortData())._sortData[criteriaName]

                // Sort the items in descending order if defined so explicitly. Otherwise
                // sort items in ascending order.
                if (criteriaOrder === 'desc' || (!criteriaOrder && isDescending)) {
                    result = valB < valA ? -1 : valB > valA ? 1 : 0
                } else {
                    result = valA < valB ? -1 : valA > valB ? 1 : 0
                }

                // If we have -1 or 1 as the return value, let's return it immediately.
                if (result) return result
            }

            // If values are equal let's compare the item indices to make sure we
            // have a stable sort. Note that this is not necessary in evergreen browsers
            // because Array.sort() is nowadays stable. However, in order to guarantee
            // same results in older browsers we need this.
            if (!result) {
                if (!indexMap) indexMap = createIndexMap(origItems)
                result = isDescending ? compareIndexMap(indexMap, b, a) : compareIndexMap(indexMap, a, b)
            }
            return result
        }

        function customComparer(a, b) {
            var result = isDescending ? -sortComparer(a, b) : sortComparer(a, b)
            if (!result) {
                if (!indexMap) indexMap = createIndexMap(origItems)
                result = isDescending ? compareIndexMap(indexMap, b, a) : compareIndexMap(indexMap, a, b)
            }
            return result
        }

        return function (comparer, options) {
            if (this._isDestroyed || this._items.length < 2) return this

            var items = this._items
            var opts = options || {}
            var layout = opts.layout ? opts.layout : opts.layout === undefined

            // Setup parent scope data.
            isDescending = !!opts.descending
            origItems = items.slice(0)
            indexMap = null

            // If function is provided do a native array sort.
            if (isFunction(comparer)) {
                sortComparer = comparer
                items.sort(customComparer)
            }
            // Otherwise if we got a string, let's sort by the sort data as provided in
            // the instance's options.
            else if (typeof comparer === STRING_TYPE) {
                sortComparer = comparer
                    .trim()
                    .split(' ')
                    .filter(function (val) {
                        return val
                    })
                    .map(function (val) {
                        return val.split(':')
                    })
                items.sort(defaultComparer)
            }
            // Otherwise if we got an array, let's assume it's a presorted array of the
            // items and order the items based on it. Here we blindly trust that the
            // presorted array consists of the same item instances as the current
            // `gird._items` array.
            else if (Array.isArray(comparer)) {
                items.length = 0
                items.push.apply(items, comparer)
            }
            // Otherwise let's throw an error.
            else {
                sortComparer = isDescending = origItems = indexMap = null
                throw new Error('Invalid comparer argument provided.')
            }

            // Emit sort event.
            if (this._hasListeners(EVENT_SORT)) {
                this._emit(EVENT_SORT, items.slice(0), origItems)
            }

            // If layout is needed.
            if (layout) {
                this.layout(layout === INSTANT_LAYOUT, isFunction(layout) ? layout : undefined)
            }

            // Reset data (to avoid mem leaks).
            sortComparer = isDescending = origItems = indexMap = null

            return this
        }
    })()

    /**
     * Move item to another index or in place of another item.
     *
     * @public
     * @param {(HtmlElement|Number|Item)} item
     * @param {(HtmlElement|Number|Item)} position
     * @param {Object} [options]
     * @param {String} [options.action="move"]
     *   - Accepts either "move" or "swap".
     *   - "move" moves the item in place of the other item.
     *   - "swap" swaps the position of the items.
     * @param {(Boolean|Function|String)} [options.layout=true]
     * @returns {Grid}
     */
    Grid.prototype.move = function (item, position, options) {
        if (this._isDestroyed || this._items.length < 2) return this

        var items = this._items
        var opts = options || {}
        var layout = opts.layout ? opts.layout : opts.layout === undefined
        var isSwap = opts.action === ACTION_SWAP
        var action = isSwap ? ACTION_SWAP : ACTION_MOVE
        var fromItem = this.getItem(item)
        var toItem = this.getItem(position)
        var fromIndex
        var toIndex

        // Make sure the items exist and are not the same.
        if (fromItem && toItem && fromItem !== toItem) {
            // Get the indices of the items.
            fromIndex = items.indexOf(fromItem)
            toIndex = items.indexOf(toItem)

            // Do the move/swap.
            if (isSwap) {
                arraySwap(items, fromIndex, toIndex)
            } else {
                arrayMove(items, fromIndex, toIndex)
            }

            // Emit move event.
            if (this._hasListeners(EVENT_MOVE)) {
                this._emit(EVENT_MOVE, {
                    item: fromItem,
                    fromIndex: fromIndex,
                    toIndex: toIndex,
                    action: action,
                })
            }

            // If layout is needed.
            if (layout) {
                this.layout(layout === INSTANT_LAYOUT, isFunction(layout) ? layout : undefined)
            }
        }

        return this
    }

    /**
     * Send item to another Grid instance.
     *
     * @public
     * @param {(HtmlElement|Number|Item)} item
     * @param {Grid} targetGrid
     * @param {(HtmlElement|Number|Item)} position
     * @param {Object} [options]
     * @param {HTMLElement} [options.appendTo=document.body]
     * @param {(Boolean|Function|String)} [options.layoutSender=true]
     * @param {(Boolean|Function|String)} [options.layoutReceiver=true]
     * @returns {Grid}
     */
    Grid.prototype.send = function (item, targetGrid, position, options) {
        if (this._isDestroyed || targetGrid._isDestroyed || this === targetGrid) return this

        // Make sure we have a valid target item.
        item = this.getItem(item)
        if (!item) return this

        var opts = options || {}
        var container = opts.appendTo || document.body
        var layoutSender = opts.layoutSender ? opts.layoutSender : opts.layoutSender === undefined
        var layoutReceiver = opts.layoutReceiver ? opts.layoutReceiver : opts.layoutReceiver === undefined

        // Start the migration process.
        item._migrate.start(targetGrid, position, container)

        // If migration was started successfully and the item is active, let's layout
        // the grids.
        if (item._migrate._isActive && item._isActive) {
            if (layoutSender) {
                this.layout(layoutSender === INSTANT_LAYOUT, isFunction(layoutSender) ? layoutSender : undefined)
            }
            if (layoutReceiver) {
                targetGrid.layout(
                    layoutReceiver === INSTANT_LAYOUT,
                    isFunction(layoutReceiver) ? layoutReceiver : undefined,
                )
            }
        }

        return this
    }

    /**
     * Destroy the instance.
     *
     * @public
     * @param {Boolean} [removeElements=false]
     * @returns {Grid}
     */
    Grid.prototype.destroy = function (removeElements) {
        if (this._isDestroyed) return this

        var container = this._element
        var items = this._items.slice(0)
        var layoutStyles = (this._layout && this._layout.styles) || {}
        var i, prop

        // Unbind window resize event listener.
        unbindLayoutOnResize(this)

        // Destroy items.
        for (i = 0; i < items.length; i++) items[i]._destroy(removeElements)
        this._items.length = 0

        // Restore container.
        removeClass(container, this._settings.containerClass)
        for (prop in layoutStyles) container.style[prop] = ''

        // Emit destroy event and unbind all events.
        this._emit(EVENT_DESTROY)
        this._emitter.destroy()

        // Remove reference from the grid instances collection.
        delete GRID_INSTANCES[this._id]

        // Flag instance as destroyed.
        this._isDestroyed = true

        return this
    }

    /**
     * Private prototype methods
     * *************************
     */

    /**
     * Emit a grid event.
     *
     * @private
     * @param {String} event
     * @param {...*} [arg]
     */
    Grid.prototype._emit = function () {
        if (this._isDestroyed) return
        this._emitter.emit.apply(this._emitter, arguments)
    }

    /**
     * Check if there are any events listeners for an event.
     *
     * @private
     * @param {String} event
     * @returns {Boolean}
     */
    Grid.prototype._hasListeners = function (event) {
        if (this._isDestroyed) return false
        return this._emitter.countListeners(event) > 0
    }

    /**
     * Update container's width, height and offsets.
     *
     * @private
     */
    Grid.prototype._updateBoundingRect = function () {
        var element = this._element
        var rect = element.getBoundingClientRect()
        this._width = rect.width
        this._height = rect.height
        this._left = rect.left
        this._top = rect.top
        this._right = rect.right
        this._bottom = rect.bottom
    }

    /**
     * Update container's border sizes.
     *
     * @private
     * @param {Boolean} left
     * @param {Boolean} right
     * @param {Boolean} top
     * @param {Boolean} bottom
     */
    Grid.prototype._updateBorders = function (left, right, top, bottom) {
        var element = this._element
        if (left) this._borderLeft = getStyleAsFloat(element, 'border-left-width')
        if (right) this._borderRight = getStyleAsFloat(element, 'border-right-width')
        if (top) this._borderTop = getStyleAsFloat(element, 'border-top-width')
        if (bottom) this._borderBottom = getStyleAsFloat(element, 'border-bottom-width')
    }

    /**
     * Refresh all of container's internal dimensions and offsets.
     *
     * @private
     */
    Grid.prototype._refreshDimensions = function () {
        this._updateBoundingRect()
        this._updateBorders(1, 1, 1, 1)
        this._boxSizing = getStyle(this._element, 'box-sizing')
    }

    /**
     * Calculate and apply item positions.
     *
     * @private
     * @param {Object} layout
     */
    Grid.prototype._onLayoutDataReceived = (function () {
        var itemsToLayout = []
        return function (layout) {
            if (this._isDestroyed || !this._nextLayoutData || this._nextLayoutData.id !== layout.id) return

            var grid = this
            var instant = this._nextLayoutData.instant
            var onFinish = this._nextLayoutData.onFinish
            var numItems = layout.items.length
            var counter = numItems
            var item
            var left
            var top
            var i

            // Reset next layout data.
            this._nextLayoutData = null

            if (!this._isLayoutFinished && this._hasListeners(EVENT_LAYOUT_ABORT)) {
                this._emit(EVENT_LAYOUT_ABORT, this._layout.items.slice(0))
            }

            // Update the layout reference.
            this._layout = layout

            // Update the item positions and collect all items that need to be laid
            // out. It is critical that we update the item position _before_ the
            // layoutStart event as the new data might be needed in the callback.
            itemsToLayout.length = 0
            for (i = 0; i < numItems; i++) {
                item = layout.items[i]

                // Make sure we have a matching item.
                if (!item) {
                    --counter
                    continue
                }

                // Get the item's new left and top values.
                left = layout.slots[i * 2]
                top = layout.slots[i * 2 + 1]

                // Let's skip the layout process if we can. Possibly avoids a lot of DOM
                // operations which saves us some CPU cycles.
                if (item._canSkipLayout(left, top)) {
                    --counter
                    continue
                }

                // Update the item's position.
                item._left = left
                item._top = top

                // Only active non-dragged items need to be moved.
                if (item.isActive() && !item.isDragging()) {
                    itemsToLayout.push(item)
                } else {
                    --counter
                }
            }

            // Set layout styles to the grid element.
            if (layout.styles) {
                setStyles(this._element, layout.styles)
            }

            // layoutStart event is intentionally emitted after the container element's
            // dimensions are set, because otherwise there would be no hook for reacting
            // to container dimension changes.
            if (this._hasListeners(EVENT_LAYOUT_START)) {
                this._emit(EVENT_LAYOUT_START, layout.items.slice(0), instant === true)
                // Let's make sure that the current layout process has not been overridden
                // in the layoutStart event, and if so, let's stop processing the aborted
                // layout.
                if (this._layout.id !== layout.id) return
            }

            var tryFinish = function () {
                if (--counter > 0) return

                var hasLayoutChanged = grid._layout.id !== layout.id
                var callback = isFunction(instant) ? instant : onFinish

                if (!hasLayoutChanged) {
                    grid._isLayoutFinished = true
                }

                if (isFunction(callback)) {
                    callback(layout.items.slice(0), hasLayoutChanged)
                }

                if (!hasLayoutChanged && grid._hasListeners(EVENT_LAYOUT_END)) {
                    grid._emit(EVENT_LAYOUT_END, layout.items.slice(0))
                }
            }

            if (!itemsToLayout.length) {
                tryFinish()
                return this
            }

            this._isLayoutFinished = false

            for (i = 0; i < itemsToLayout.length; i++) {
                if (this._layout.id !== layout.id) break
                itemsToLayout[i]._layout.start(instant === true, tryFinish)
            }

            if (this._layout.id === layout.id) {
                itemsToLayout.length = 0
            }

            return this
        }
    })()

    /**
     * Show or hide Grid instance's items.
     *
     * @private
     * @param {Item[]} items
     * @param {Boolean} toVisible
     * @param {Object} [options]
     * @param {Boolean} [options.instant=false]
     * @param {Boolean} [options.syncWithLayout=true]
     * @param {Function} [options.onFinish]
     * @param {(Boolean|Function|String)} [options.layout=true]
     */
    Grid.prototype._setItemsVisibility = function (items, toVisible, options) {
        var grid = this
        var targetItems = items.slice(0)
        var opts = options || {}
        var isInstant = opts.instant === true
        var callback = opts.onFinish
        var layout = opts.layout ? opts.layout : opts.layout === undefined
        var counter = targetItems.length
        var startEvent = toVisible ? EVENT_SHOW_START : EVENT_HIDE_START
        var endEvent = toVisible ? EVENT_SHOW_END : EVENT_HIDE_END
        var method = toVisible ? 'show' : 'hide'
        var needsLayout = false
        var completedItems = []
        var hiddenItems = []
        var item
        var i

        // If there are no items call the callback, but don't emit any events.
        if (!counter) {
            if (isFunction(callback)) callback(targetItems)
            return
        }

        // Prepare the items.
        for (i = 0; i < targetItems.length; i++) {
            item = targetItems[i]

            // If inactive item is shown or active item is hidden we need to do
            // layout.
            if ((toVisible && !item._isActive) || (!toVisible && item._isActive)) {
                needsLayout = true
            }

            // If inactive item is shown we also need to do a little hack to make the
            // item not animate it's next positioning (layout).
            item._layout._skipNextAnimation = !!(toVisible && !item._isActive)

            // If a hidden item is being shown we need to refresh the item's
            // dimensions.
            if (toVisible && item._visibility._isHidden) {
                hiddenItems.push(item)
            }

            // Add item to layout or remove it from layout.
            if (toVisible) {
                item._addToLayout()
            } else {
                item._removeFromLayout()
            }
        }

        // Force refresh the dimensions of all hidden items.
        if (hiddenItems.length) {
            this.refreshItems(hiddenItems, true)
            hiddenItems.length = 0
        }

        // Show the items in sync with the next layout.
        function triggerVisibilityChange() {
            if (needsLayout && opts.syncWithLayout !== false) {
                grid.off(EVENT_LAYOUT_START, triggerVisibilityChange)
            }

            if (grid._hasListeners(startEvent)) {
                grid._emit(startEvent, targetItems.slice(0))
            }

            for (i = 0; i < targetItems.length; i++) {
                // Make sure the item is still in the original grid. There is a chance
                // that the item starts migrating before tiggerVisibilityChange is called.
                if (targetItems[i]._gridId !== grid._id) {
                    if (--counter < 1) {
                        if (isFunction(callback)) callback(completedItems.slice(0))
                        if (grid._hasListeners(endEvent)) grid._emit(endEvent, completedItems.slice(0))
                    }
                    continue
                }

                targetItems[i]._visibility[method](isInstant, function (interrupted, item) {
                    // If the current item's animation was not interrupted add it to the
                    // completedItems array.
                    if (!interrupted) completedItems.push(item)

                    // If all items have finished their animations call the callback
                    // and emit showEnd/hideEnd event.
                    if (--counter < 1) {
                        if (isFunction(callback)) callback(completedItems.slice(0))
                        if (grid._hasListeners(endEvent)) grid._emit(endEvent, completedItems.slice(0))
                    }
                })
            }
        }

        // Trigger the visibility change, either async with layout or instantly.
        if (needsLayout && opts.syncWithLayout !== false) {
            this.on(EVENT_LAYOUT_START, triggerVisibilityChange)
        } else {
            triggerVisibilityChange()
        }

        // Trigger layout if needed.
        if (needsLayout && layout) {
            this.layout(layout === INSTANT_LAYOUT, isFunction(layout) ? layout : undefined)
        }
    }

    /**
     * Private helpers
     * ***************
     */

    /**
     * Merge default settings with user settings. The returned object is a new
     * object with merged values. The merging is a deep merge meaning that all
     * objects and arrays within the provided settings objects will be also merged
     * so that modifying the values of the settings object will have no effect on
     * the returned object.
     *
     * @param {Object} defaultSettings
     * @param {Object} [userSettings]
     * @returns {Object} Returns a new object.
     */
    function mergeSettings(defaultSettings, userSettings) {
        // Create a fresh copy of default settings.
        var settings = mergeObjects({}, defaultSettings)

        // Merge user settings to default settings.
        if (userSettings) {
            settings = mergeObjects(settings, userSettings)
        }

        // Handle visible/hidden styles manually so that the whole object is
        // overridden instead of the props.

        if (userSettings && userSettings.visibleStyles) {
            settings.visibleStyles = userSettings.visibleStyles
        } else if (defaultSettings && defaultSettings.visibleStyles) {
            settings.visibleStyles = defaultSettings.visibleStyles
        }

        if (userSettings && userSettings.hiddenStyles) {
            settings.hiddenStyles = userSettings.hiddenStyles
        } else if (defaultSettings && defaultSettings.hiddenStyles) {
            settings.hiddenStyles = defaultSettings.hiddenStyles
        }

        return settings
    }

    /**
     * Merge two objects recursively (deep merge). The source object's properties
     * are merged to the target object.
     *
     * @param {Object} target
     *   - The target object.
     * @param {Object} source
     *   - The source object.
     * @returns {Object} Returns the target object.
     */
    function mergeObjects(target, source) {
        var sourceKeys = Object.keys(source)
        var length = sourceKeys.length
        var isSourceObject
        var propName
        var i

        for (i = 0; i < length; i++) {
            propName = sourceKeys[i]
            isSourceObject = isPlainObject(source[propName])

            // If target and source values are both objects, merge the objects and
            // assign the merged value to the target property.
            if (isPlainObject(target[propName]) && isSourceObject) {
                target[propName] = mergeObjects(mergeObjects({}, target[propName]), source[propName])
                continue
            }

            // If source's value is object and target's is not let's clone the object as
            // the target's value.
            if (isSourceObject) {
                target[propName] = mergeObjects({}, source[propName])
                continue
            }

            // If source's value is an array let's clone the array as the target's
            // value.
            if (Array.isArray(source[propName])) {
                target[propName] = source[propName].slice(0)
                continue
            }

            // In all other cases let's just directly assign the source's value as the
            // target's value.
            target[propName] = source[propName]
        }

        return target
    }

    /**
     * Collect and return initial items for grid.
     *
     * @param {HTMLElement} gridElement
     * @param {?(HTMLElement[]|NodeList|HtmlCollection|String)} elements
     * @returns {(HTMLElement[]|NodeList|HtmlCollection)}
     */
    function getInitialGridElements(gridElement, elements) {
        // If we have a wildcard selector let's return all the children.
        if (elements === '*') {
            return gridElement.children
        }

        // If we have some more specific selector, let's filter the elements.
        if (typeof elements === STRING_TYPE) {
            var result = []
            var children = gridElement.children
            for (var i = 0; i < children.length; i++) {
                if (elementMatches(children[i], elements)) {
                    result.push(children[i])
                }
            }
            return result
        }

        // If we have an array of elements or a node list.
        if (Array.isArray(elements) || isNodeList(elements)) {
            return elements
        }

        // Otherwise just return an empty array.
        return []
    }

    /**
     * Bind grid's resize handler to window.
     *
     * @param {Grid} grid
     * @param {(Number|Boolean)} delay
     */
    function bindLayoutOnResize(grid, delay) {
        if (typeof delay !== NUMBER_TYPE) {
            delay = delay === true ? 0 : -1
        }

        if (delay >= 0) {
            grid._resizeHandler = debounce(function () {
                grid.refreshItems().layout()
            }, delay)

            window.addEventListener('resize', grid._resizeHandler)
        }
    }

    /**
     * Unbind grid's resize handler from window.
     *
     * @param {Grid} grid
     */
    function unbindLayoutOnResize(grid) {
        if (grid._resizeHandler) {
            grid._resizeHandler(true)
            window.removeEventListener('resize', grid._resizeHandler)
            grid._resizeHandler = null
        }
    }

    /**
     * Normalize style declaration object, returns a normalized (new) styles object
     * (prefixed properties and invalid properties removed).
     *
     * @param {Object} styles
     * @returns {Object}
     */
    function normalizeStyles(styles) {
        var normalized = {}
        var docElemStyle = document.documentElement.style
        var prop, prefixedProp

        // Normalize visible styles (prefix and remove invalid).
        for (prop in styles) {
            if (!styles[prop]) continue
            prefixedProp = getPrefixedPropName(docElemStyle, prop)
            if (!prefixedProp) continue
            normalized[prefixedProp] = styles[prop]
        }

        return normalized
    }

    /**
     * Create index map from items.
     *
     * @param {Item[]} items
     * @returns {Object}
     */
    function createIndexMap(items) {
        var result = {}
        for (var i = 0; i < items.length; i++) {
            result[items[i]._id] = i
        }
        return result
    }

    /**
     * Sort comparer function for items' index map.
     *
     * @param {Object} indexMap
     * @param {Item} itemA
     * @param {Item} itemB
     * @returns {Number}
     */
    function compareIndexMap(indexMap, itemA, itemB) {
        var indexA = indexMap[itemA._id]
        var indexB = indexMap[itemB._id]
        return indexA - indexB
    }

    // Copyright 2014 Google Inc. All rights reserved.
    //
    // Licensed under the Apache License, Version 2.0 (the "License");
    // you may not use this file except in compliance with the License.
    //     You may obtain a copy of the License at
    //
    // http://www.apache.org/licenses/LICENSE-2.0
    //
    // Unless required by applicable law or agreed to in writing, software
    // distributed under the License is distributed on an "AS IS" BASIS,
    // WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    //     See the License for the specific language governing permissions and
    // limitations under the License.

    !(function () {
        var a = {},
            b = {}
        !(function (a, b) {
            function c(a) {
                if ('number' == typeof a) return a
                var b = {}
                for (var c in a) b[c] = a[c]
                return b
            }
            function d() {
                ;(this._delay = 0),
                    (this._endDelay = 0),
                    (this._fill = 'none'),
                    (this._iterationStart = 0),
                    (this._iterations = 1),
                    (this._duration = 0),
                    (this._playbackRate = 1),
                    (this._direction = 'normal'),
                    (this._easing = 'linear'),
                    (this._easingFunction = x)
            }
            function e() {
                return a.isDeprecated(
                    'Invalid timing inputs',
                    '2016-03-02',
                    'TypeError exceptions will be thrown instead.',
                    !0,
                )
            }
            function f(b, c, e) {
                var f = new d()
                return (
                    c && ((f.fill = 'both'), (f.duration = 'auto')),
                    'number' != typeof b || isNaN(b)
                        ? void 0 !== b &&
                          Object.getOwnPropertyNames(b).forEach(function (c) {
                              if ('auto' != b[c]) {
                                  if (
                                      ('number' == typeof f[c] || 'duration' == c) &&
                                      ('number' != typeof b[c] || isNaN(b[c]))
                                  )
                                      return
                                  if ('fill' == c && -1 == v.indexOf(b[c])) return
                                  if ('direction' == c && -1 == w.indexOf(b[c])) return
                                  if (
                                      'playbackRate' == c &&
                                      1 !== b[c] &&
                                      a.isDeprecated(
                                          'AnimationEffectTiming.playbackRate',
                                          '2014-11-28',
                                          'Use Animation.playbackRate instead.',
                                      )
                                  )
                                      return
                                  f[c] = b[c]
                              }
                          })
                        : (f.duration = b),
                    f
                )
            }
            function g(a) {
                return 'number' == typeof a && (a = isNaN(a) ? { duration: 0 } : { duration: a }), a
            }
            function h(b, c) {
                return (b = a.numericTimingToObject(b)), f(b, c)
            }
            function i(a, b, c, d) {
                return a < 0 || a > 1 || c < 0 || c > 1
                    ? x
                    : function (e) {
                          function f(a, b, c) {
                              return 3 * a * (1 - c) * (1 - c) * c + 3 * b * (1 - c) * c * c + c * c * c
                          }
                          if (e <= 0) {
                              var g = 0
                              return a > 0 ? (g = b / a) : !b && c > 0 && (g = d / c), g * e
                          }
                          if (e >= 1) {
                              var h = 0
                              return (
                                  c < 1 ? (h = (d - 1) / (c - 1)) : 1 == c && a < 1 && (h = (b - 1) / (a - 1)),
                                  1 + h * (e - 1)
                              )
                          }
                          for (var i = 0, j = 1; i < j; ) {
                              var k = (i + j) / 2,
                                  l = f(a, c, k)
                              if (Math.abs(e - l) < 1e-5) return f(b, d, k)
                              l < e ? (i = k) : (j = k)
                          }
                          return f(b, d, k)
                      }
            }
            function j(a, b) {
                return function (c) {
                    if (c >= 1) return 1
                    var d = 1 / a
                    return (c += b * d) - (c % d)
                }
            }
            function k(a) {
                C || (C = document.createElement('div').style),
                    (C.animationTimingFunction = ''),
                    (C.animationTimingFunction = a)
                var b = C.animationTimingFunction
                if ('' == b && e()) throw new TypeError(a + ' is not a valid value for easing')
                return b
            }
            function l(a) {
                if ('linear' == a) return x
                var b = E.exec(a)
                if (b) return i.apply(this, b.slice(1).map(Number))
                var c = F.exec(a)
                if (c) return j(Number(c[1]), A)
                var d = G.exec(a)
                return d ? j(Number(d[1]), { start: y, middle: z, end: A }[d[2]]) : B[a] || x
            }
            function m(a) {
                return Math.abs(n(a) / a.playbackRate)
            }
            function n(a) {
                return 0 === a.duration || 0 === a.iterations ? 0 : a.duration * a.iterations
            }
            function o(a, b, c) {
                if (null == b) return H
                var d = c.delay + a + c.endDelay
                return b < Math.min(c.delay, d) ? I : b >= Math.min(c.delay + a, d) ? J : K
            }
            function p(a, b, c, d, e) {
                switch (d) {
                    case I:
                        return 'backwards' == b || 'both' == b ? 0 : null
                    case K:
                        return c - e
                    case J:
                        return 'forwards' == b || 'both' == b ? a : null
                    case H:
                        return null
                }
            }
            function q(a, b, c, d, e) {
                var f = e
                return 0 === a ? b !== I && (f += c) : (f += d / a), f
            }
            function r(a, b, c, d, e, f) {
                var g = a === 1 / 0 ? b % 1 : a % 1
                return 0 !== g || c !== J || 0 === d || (0 === e && 0 !== f) || (g = 1), g
            }
            function s(a, b, c, d) {
                return a === J && b === 1 / 0 ? 1 / 0 : 1 === c ? Math.floor(d) - 1 : Math.floor(d)
            }
            function t(a, b, c) {
                var d = a
                if ('normal' !== a && 'reverse' !== a) {
                    var e = b
                    'alternate-reverse' === a && (e += 1), (d = 'normal'), e !== 1 / 0 && e % 2 != 0 && (d = 'reverse')
                }
                return 'normal' === d ? c : 1 - c
            }
            function u(a, b, c) {
                var d = o(a, b, c),
                    e = p(a, c.fill, b, d, c.delay)
                if (null === e) return null
                var f = q(c.duration, d, c.iterations, e, c.iterationStart),
                    g = r(f, c.iterationStart, d, c.iterations, e, c.duration),
                    h = s(d, c.iterations, g, f),
                    i = t(c.direction, h, g)
                return c._easingFunction(i)
            }
            var v = 'backwards|forwards|both|none'.split('|'),
                w = 'reverse|alternate|alternate-reverse'.split('|'),
                x = function (a) {
                    return a
                }
            d.prototype = {
                _setMember: function (b, c) {
                    ;(this['_' + b] = c),
                        this._effect &&
                            ((this._effect._timingInput[b] = c),
                            (this._effect._timing = a.normalizeTimingInput(this._effect._timingInput)),
                            (this._effect.activeDuration = a.calculateActiveDuration(this._effect._timing)),
                            this._effect._animation && this._effect._animation._rebuildUnderlyingAnimation())
                },
                get playbackRate() {
                    return this._playbackRate
                },
                set delay(a) {
                    this._setMember('delay', a)
                },
                get delay() {
                    return this._delay
                },
                set endDelay(a) {
                    this._setMember('endDelay', a)
                },
                get endDelay() {
                    return this._endDelay
                },
                set fill(a) {
                    this._setMember('fill', a)
                },
                get fill() {
                    return this._fill
                },
                set iterationStart(a) {
                    if ((isNaN(a) || a < 0) && e())
                        throw new TypeError('iterationStart must be a non-negative number, received: ' + a)
                    this._setMember('iterationStart', a)
                },
                get iterationStart() {
                    return this._iterationStart
                },
                set duration(a) {
                    if ('auto' != a && (isNaN(a) || a < 0) && e())
                        throw new TypeError('duration must be non-negative or auto, received: ' + a)
                    this._setMember('duration', a)
                },
                get duration() {
                    return this._duration
                },
                set direction(a) {
                    this._setMember('direction', a)
                },
                get direction() {
                    return this._direction
                },
                set easing(a) {
                    ;(this._easingFunction = l(k(a))), this._setMember('easing', a)
                },
                get easing() {
                    return this._easing
                },
                set iterations(a) {
                    if ((isNaN(a) || a < 0) && e())
                        throw new TypeError('iterations must be non-negative, received: ' + a)
                    this._setMember('iterations', a)
                },
                get iterations() {
                    return this._iterations
                },
            }
            var y = 1,
                z = 0.5,
                A = 0,
                B = {
                    ease: i(0.25, 0.1, 0.25, 1),
                    'ease-in': i(0.42, 0, 1, 1),
                    'ease-out': i(0, 0, 0.58, 1),
                    'ease-in-out': i(0.42, 0, 0.58, 1),
                    'step-start': j(1, y),
                    'step-middle': j(1, z),
                    'step-end': j(1, A),
                },
                C = null,
                D = '\\s*(-?\\d+\\.?\\d*|-?\\.\\d+)\\s*',
                E = new RegExp('cubic-bezier\\(' + D + ',' + D + ',' + D + ',' + D + '\\)'),
                F = /steps\(\s*(\d+)\s*\)/,
                G = /steps\(\s*(\d+)\s*,\s*(start|middle|end)\s*\)/,
                H = 0,
                I = 1,
                J = 2,
                K = 3
            ;(a.cloneTimingInput = c),
                (a.makeTiming = f),
                (a.numericTimingToObject = g),
                (a.normalizeTimingInput = h),
                (a.calculateActiveDuration = m),
                (a.calculateIterationProgress = u),
                (a.calculatePhase = o),
                (a.normalizeEasing = k),
                (a.parseEasingFunction = l)
        })(a),
            (function (a, b) {
                function c(a, b) {
                    return a in k ? k[a][b] || b : b
                }
                function d(a) {
                    return (
                        'display' === a || 0 === a.lastIndexOf('animation', 0) || 0 === a.lastIndexOf('transition', 0)
                    )
                }
                function e(a, b, e) {
                    if (!d(a)) {
                        var f = h[a]
                        if (f) {
                            i.style[a] = b
                            for (var g in f) {
                                var j = f[g],
                                    k = i.style[j]
                                e[j] = c(j, k)
                            }
                        } else e[a] = c(a, b)
                    }
                }
                function f(a) {
                    var b = []
                    for (var c in a)
                        if (!(c in ['easing', 'offset', 'composite'])) {
                            var d = a[c]
                            Array.isArray(d) || (d = [d])
                            for (var e, f = d.length, g = 0; g < f; g++)
                                (e = {}),
                                    (e.offset = 'offset' in a ? a.offset : 1 == f ? 1 : g / (f - 1)),
                                    'easing' in a && (e.easing = a.easing),
                                    'composite' in a && (e.composite = a.composite),
                                    (e[c] = d[g]),
                                    b.push(e)
                        }
                    return (
                        b.sort(function (a, b) {
                            return a.offset - b.offset
                        }),
                        b
                    )
                }
                function g(b) {
                    function c() {
                        var a = d.length
                        null == d[a - 1].offset && (d[a - 1].offset = 1),
                            a > 1 && null == d[0].offset && (d[0].offset = 0)
                        for (var b = 0, c = d[0].offset, e = 1; e < a; e++) {
                            var f = d[e].offset
                            if (null != f) {
                                for (var g = 1; g < e - b; g++) d[b + g].offset = c + ((f - c) * g) / (e - b)
                                ;(b = e), (c = f)
                            }
                        }
                    }
                    if (null == b) return []
                    window.Symbol &&
                        Symbol.iterator &&
                        Array.prototype.from &&
                        b[Symbol.iterator] &&
                        (b = Array.from(b)),
                        Array.isArray(b) || (b = f(b))
                    for (
                        var d = b.map(function (b) {
                                var c = {}
                                for (var d in b) {
                                    var f = b[d]
                                    if ('offset' == d) {
                                        if (null != f) {
                                            if (((f = Number(f)), !isFinite(f)))
                                                throw new TypeError('Keyframe offsets must be numbers.')
                                            if (f < 0 || f > 1)
                                                throw new TypeError('Keyframe offsets must be between 0 and 1.')
                                        }
                                    } else if ('composite' == d) {
                                        if ('add' == f || 'accumulate' == f)
                                            throw {
                                                type: DOMException.NOT_SUPPORTED_ERR,
                                                name: 'NotSupportedError',
                                                message: 'add compositing is not supported',
                                            }
                                        if ('replace' != f) throw new TypeError('Invalid composite mode ' + f + '.')
                                    } else f = 'easing' == d ? a.normalizeEasing(f) : '' + f
                                    e(d, f, c)
                                }
                                return (
                                    void 0 == c.offset && (c.offset = null),
                                    void 0 == c.easing && (c.easing = 'linear'),
                                    c
                                )
                            }),
                            g = !0,
                            h = -1 / 0,
                            i = 0;
                        i < d.length;
                        i++
                    ) {
                        var j = d[i].offset
                        if (null != j) {
                            if (j < h)
                                throw new TypeError(
                                    'Keyframes are not loosely sorted by offset. Sort or specify offsets.',
                                )
                            h = j
                        } else g = !1
                    }
                    return (
                        (d = d.filter(function (a) {
                            return a.offset >= 0 && a.offset <= 1
                        })),
                        g || c(),
                        d
                    )
                }
                var h = {
                        background: [
                            'backgroundImage',
                            'backgroundPosition',
                            'backgroundSize',
                            'backgroundRepeat',
                            'backgroundAttachment',
                            'backgroundOrigin',
                            'backgroundClip',
                            'backgroundColor',
                        ],
                        border: [
                            'borderTopColor',
                            'borderTopStyle',
                            'borderTopWidth',
                            'borderRightColor',
                            'borderRightStyle',
                            'borderRightWidth',
                            'borderBottomColor',
                            'borderBottomStyle',
                            'borderBottomWidth',
                            'borderLeftColor',
                            'borderLeftStyle',
                            'borderLeftWidth',
                        ],
                        borderBottom: ['borderBottomWidth', 'borderBottomStyle', 'borderBottomColor'],
                        borderColor: ['borderTopColor', 'borderRightColor', 'borderBottomColor', 'borderLeftColor'],
                        borderLeft: ['borderLeftWidth', 'borderLeftStyle', 'borderLeftColor'],
                        borderRadius: [
                            'borderTopLeftRadius',
                            'borderTopRightRadius',
                            'borderBottomRightRadius',
                            'borderBottomLeftRadius',
                        ],
                        borderRight: ['borderRightWidth', 'borderRightStyle', 'borderRightColor'],
                        borderTop: ['borderTopWidth', 'borderTopStyle', 'borderTopColor'],
                        borderWidth: ['borderTopWidth', 'borderRightWidth', 'borderBottomWidth', 'borderLeftWidth'],
                        flex: ['flexGrow', 'flexShrink', 'flexBasis'],
                        font: ['fontFamily', 'fontSize', 'fontStyle', 'fontVariant', 'fontWeight', 'lineHeight'],
                        margin: ['marginTop', 'marginRight', 'marginBottom', 'marginLeft'],
                        outline: ['outlineColor', 'outlineStyle', 'outlineWidth'],
                        padding: ['paddingTop', 'paddingRight', 'paddingBottom', 'paddingLeft'],
                    },
                    i = document.createElementNS('http://www.w3.org/1999/xhtml', 'div'),
                    j = { thin: '1px', medium: '3px', thick: '5px' },
                    k = {
                        borderBottomWidth: j,
                        borderLeftWidth: j,
                        borderRightWidth: j,
                        borderTopWidth: j,
                        fontSize: {
                            'xx-small': '60%',
                            'x-small': '75%',
                            small: '89%',
                            medium: '100%',
                            large: '120%',
                            'x-large': '150%',
                            'xx-large': '200%',
                        },
                        fontWeight: { normal: '400', bold: '700' },
                        outlineWidth: j,
                        textShadow: { none: '0px 0px 0px transparent' },
                        boxShadow: { none: '0px 0px 0px 0px transparent' },
                    }
                ;(a.convertToArrayForm = f), (a.normalizeKeyframes = g)
            })(a),
            (function (a) {
                var b = {}
                ;(a.isDeprecated = function (a, c, d, e) {
                    var f = e ? 'are' : 'is',
                        g = new Date(),
                        h = new Date(c)
                    return (
                        h.setMonth(h.getMonth() + 3),
                        !(
                            g < h &&
                            (a in b ||
                                console.warn(
                                    'Web Animations: ' +
                                        a +
                                        ' ' +
                                        f +
                                        ' deprecated and will stop working on ' +
                                        h.toDateString() +
                                        '. ' +
                                        d,
                                ),
                            (b[a] = !0),
                            1)
                        )
                    )
                }),
                    (a.deprecated = function (b, c, d, e) {
                        var f = e ? 'are' : 'is'
                        if (a.isDeprecated(b, c, d, e)) throw new Error(b + ' ' + f + ' no longer supported. ' + d)
                    })
            })(a),
            (function () {
                if (document.documentElement.animate) {
                    var c = document.documentElement.animate([], 0),
                        d = !0
                    if (
                        (c &&
                            ((d = !1),
                            'play|currentTime|pause|reverse|playbackRate|cancel|finish|startTime|playState'
                                .split('|')
                                .forEach(function (a) {
                                    void 0 === c[a] && (d = !0)
                                })),
                        !d)
                    )
                        return
                }
                !(function (a, b, c) {
                    function d(a) {
                        for (var b = {}, c = 0; c < a.length; c++)
                            for (var d in a[c])
                                if ('offset' != d && 'easing' != d && 'composite' != d) {
                                    var e = { offset: a[c].offset, easing: a[c].easing, value: a[c][d] }
                                    ;(b[d] = b[d] || []), b[d].push(e)
                                }
                        for (var f in b) {
                            var g = b[f]
                            if (0 != g[0].offset || 1 != g[g.length - 1].offset)
                                throw {
                                    type: DOMException.NOT_SUPPORTED_ERR,
                                    name: 'NotSupportedError',
                                    message: 'Partial keyframes are not supported',
                                }
                        }
                        return b
                    }
                    function e(c) {
                        var d = []
                        for (var e in c)
                            for (var f = c[e], g = 0; g < f.length - 1; g++) {
                                var h = g,
                                    i = g + 1,
                                    j = f[h].offset,
                                    k = f[i].offset,
                                    l = j,
                                    m = k
                                0 == g && ((l = -1 / 0), 0 == k && (i = h)),
                                    g == f.length - 2 && ((m = 1 / 0), 1 == j && (h = i)),
                                    d.push({
                                        applyFrom: l,
                                        applyTo: m,
                                        startOffset: f[h].offset,
                                        endOffset: f[i].offset,
                                        easingFunction: a.parseEasingFunction(f[h].easing),
                                        property: e,
                                        interpolation: b.propertyInterpolation(e, f[h].value, f[i].value),
                                    })
                            }
                        return (
                            d.sort(function (a, b) {
                                return a.startOffset - b.startOffset
                            }),
                            d
                        )
                    }
                    b.convertEffectInput = function (c) {
                        var f = a.normalizeKeyframes(c),
                            g = d(f),
                            h = e(g)
                        return function (a, c) {
                            if (null != c)
                                h.filter(function (a) {
                                    return c >= a.applyFrom && c < a.applyTo
                                }).forEach(function (d) {
                                    var e = c - d.startOffset,
                                        f = d.endOffset - d.startOffset,
                                        g = 0 == f ? 0 : d.easingFunction(e / f)
                                    b.apply(a, d.property, d.interpolation(g))
                                })
                            else for (var d in g) 'offset' != d && 'easing' != d && 'composite' != d && b.clear(a, d)
                        }
                    }
                })(a, b),
                    (function (a, b, c) {
                        function d(a) {
                            return a.replace(/-(.)/g, function (a, b) {
                                return b.toUpperCase()
                            })
                        }
                        function e(a, b, c) {
                            ;(h[c] = h[c] || []), h[c].push([a, b])
                        }
                        function f(a, b, c) {
                            for (var f = 0; f < c.length; f++) {
                                e(a, b, d(c[f]))
                            }
                        }
                        function g(c, e, f) {
                            var g = c
                            ;/-/.test(c) &&
                                !a.isDeprecated(
                                    'Hyphenated property names',
                                    '2016-03-22',
                                    'Use camelCase instead.',
                                    !0,
                                ) &&
                                (g = d(c)),
                                ('initial' != e && 'initial' != f) ||
                                    ('initial' == e && (e = i[g]), 'initial' == f && (f = i[g]))
                            for (var j = e == f ? [] : h[g], k = 0; j && k < j.length; k++) {
                                var l = j[k][0](e),
                                    m = j[k][0](f)
                                if (void 0 !== l && void 0 !== m) {
                                    var n = j[k][1](l, m)
                                    if (n) {
                                        var o = b.Interpolation.apply(null, n)
                                        return function (a) {
                                            return 0 == a ? e : 1 == a ? f : o(a)
                                        }
                                    }
                                }
                            }
                            return b.Interpolation(!1, !0, function (a) {
                                return a ? f : e
                            })
                        }
                        var h = {}
                        b.addPropertiesHandler = f
                        var i = {
                            backgroundColor: 'transparent',
                            backgroundPosition: '0% 0%',
                            borderBottomColor: 'currentColor',
                            borderBottomLeftRadius: '0px',
                            borderBottomRightRadius: '0px',
                            borderBottomWidth: '3px',
                            borderLeftColor: 'currentColor',
                            borderLeftWidth: '3px',
                            borderRightColor: 'currentColor',
                            borderRightWidth: '3px',
                            borderSpacing: '2px',
                            borderTopColor: 'currentColor',
                            borderTopLeftRadius: '0px',
                            borderTopRightRadius: '0px',
                            borderTopWidth: '3px',
                            bottom: 'auto',
                            clip: 'rect(0px, 0px, 0px, 0px)',
                            color: 'black',
                            fontSize: '100%',
                            fontWeight: '400',
                            height: 'auto',
                            left: 'auto',
                            letterSpacing: 'normal',
                            lineHeight: '120%',
                            marginBottom: '0px',
                            marginLeft: '0px',
                            marginRight: '0px',
                            marginTop: '0px',
                            maxHeight: 'none',
                            maxWidth: 'none',
                            minHeight: '0px',
                            minWidth: '0px',
                            opacity: '1.0',
                            outlineColor: 'invert',
                            outlineOffset: '0px',
                            outlineWidth: '3px',
                            paddingBottom: '0px',
                            paddingLeft: '0px',
                            paddingRight: '0px',
                            paddingTop: '0px',
                            right: 'auto',
                            strokeDasharray: 'none',
                            strokeDashoffset: '0px',
                            textIndent: '0px',
                            textShadow: '0px 0px 0px transparent',
                            top: 'auto',
                            transform: '',
                            verticalAlign: '0px',
                            visibility: 'visible',
                            width: 'auto',
                            wordSpacing: 'normal',
                            zIndex: 'auto',
                        }
                        b.propertyInterpolation = g
                    })(a, b),
                    (function (a, b, c) {
                        function d(b) {
                            var c = a.calculateActiveDuration(b),
                                d = function (d) {
                                    return a.calculateIterationProgress(c, d, b)
                                }
                            return (d._totalDuration = b.delay + c + b.endDelay), d
                        }
                        b.KeyframeEffect = function (c, e, f, g) {
                            var h,
                                i = d(a.normalizeTimingInput(f)),
                                j = b.convertEffectInput(e),
                                k = function () {
                                    j(c, h)
                                }
                            return (
                                (k._update = function (a) {
                                    return null !== (h = i(a))
                                }),
                                (k._clear = function () {
                                    j(c, null)
                                }),
                                (k._hasSameTarget = function (a) {
                                    return c === a
                                }),
                                (k._target = c),
                                (k._totalDuration = i._totalDuration),
                                (k._id = g),
                                k
                            )
                        }
                    })(a, b),
                    (function (a, b) {
                        function c(a, b) {
                            return (
                                !(!b.namespaceURI || -1 == b.namespaceURI.indexOf('/svg')) &&
                                (g in a || (a[g] = /Trident|MSIE|IEMobile|Edge|Android 4/i.test(a.navigator.userAgent)),
                                a[g])
                            )
                        }
                        function d(a, b, c) {
                            ;(c.enumerable = !0), (c.configurable = !0), Object.defineProperty(a, b, c)
                        }
                        function e(a) {
                            ;(this._element = a),
                                (this._surrogateStyle = document.createElementNS(
                                    'http://www.w3.org/1999/xhtml',
                                    'div',
                                ).style),
                                (this._style = a.style),
                                (this._length = 0),
                                (this._isAnimatedProperty = {}),
                                (this._updateSvgTransformAttr = c(window, a)),
                                (this._savedTransformAttr = null)
                            for (var b = 0; b < this._style.length; b++) {
                                var d = this._style[b]
                                this._surrogateStyle[d] = this._style[d]
                            }
                            this._updateIndices()
                        }
                        function f(a) {
                            if (!a._webAnimationsPatchedStyle) {
                                var b = new e(a)
                                try {
                                    d(a, 'style', {
                                        get: function () {
                                            return b
                                        },
                                    })
                                } catch (b) {
                                    ;(a.style._set = function (b, c) {
                                        a.style[b] = c
                                    }),
                                        (a.style._clear = function (b) {
                                            a.style[b] = ''
                                        })
                                }
                                a._webAnimationsPatchedStyle = a.style
                            }
                        }
                        var g = '_webAnimationsUpdateSvgTransformAttr',
                            h = { cssText: 1, length: 1, parentRule: 1 },
                            i = {
                                getPropertyCSSValue: 1,
                                getPropertyPriority: 1,
                                getPropertyValue: 1,
                                item: 1,
                                removeProperty: 1,
                                setProperty: 1,
                            },
                            j = { removeProperty: 1, setProperty: 1 }
                        e.prototype = {
                            get cssText() {
                                return this._surrogateStyle.cssText
                            },
                            set cssText(a) {
                                for (var b = {}, c = 0; c < this._surrogateStyle.length; c++)
                                    b[this._surrogateStyle[c]] = !0
                                ;(this._surrogateStyle.cssText = a), this._updateIndices()
                                for (var c = 0; c < this._surrogateStyle.length; c++) b[this._surrogateStyle[c]] = !0
                                for (var d in b)
                                    this._isAnimatedProperty[d] ||
                                        this._style.setProperty(d, this._surrogateStyle.getPropertyValue(d))
                            },
                            get length() {
                                return this._surrogateStyle.length
                            },
                            get parentRule() {
                                return this._style.parentRule
                            },
                            _updateIndices: function () {
                                for (; this._length < this._surrogateStyle.length; )
                                    Object.defineProperty(this, this._length, {
                                        configurable: !0,
                                        enumerable: !1,
                                        get: (function (a) {
                                            return function () {
                                                return this._surrogateStyle[a]
                                            }
                                        })(this._length),
                                    }),
                                        this._length++
                                for (; this._length > this._surrogateStyle.length; )
                                    this._length--,
                                        Object.defineProperty(this, this._length, {
                                            configurable: !0,
                                            enumerable: !1,
                                            value: void 0,
                                        })
                            },
                            _set: function (b, c) {
                                ;(this._style[b] = c),
                                    (this._isAnimatedProperty[b] = !0),
                                    this._updateSvgTransformAttr &&
                                        'transform' == a.unprefixedPropertyName(b) &&
                                        (null == this._savedTransformAttr &&
                                            (this._savedTransformAttr = this._element.getAttribute('transform')),
                                        this._element.setAttribute('transform', a.transformToSvgMatrix(c)))
                            },
                            _clear: function (b) {
                                ;(this._style[b] = this._surrogateStyle[b]),
                                    this._updateSvgTransformAttr &&
                                        'transform' == a.unprefixedPropertyName(b) &&
                                        (this._savedTransformAttr
                                            ? this._element.setAttribute('transform', this._savedTransformAttr)
                                            : this._element.removeAttribute('transform'),
                                        (this._savedTransformAttr = null)),
                                    delete this._isAnimatedProperty[b]
                            },
                        }
                        for (var k in i)
                            e.prototype[k] = (function (a, b) {
                                return function () {
                                    var c = this._surrogateStyle[a].apply(this._surrogateStyle, arguments)
                                    return (
                                        b &&
                                            (this._isAnimatedProperty[arguments[0]] ||
                                                this._style[a].apply(this._style, arguments),
                                            this._updateIndices()),
                                        c
                                    )
                                }
                            })(k, k in j)
                        for (var l in document.documentElement.style)
                            l in h ||
                                l in i ||
                                (function (a) {
                                    d(e.prototype, a, {
                                        get: function () {
                                            return this._surrogateStyle[a]
                                        },
                                        set: function (b) {
                                            ;(this._surrogateStyle[a] = b),
                                                this._updateIndices(),
                                                this._isAnimatedProperty[a] || (this._style[a] = b)
                                        },
                                    })
                                })(l)
                        ;(a.apply = function (b, c, d) {
                            f(b), b.style._set(a.propertyName(c), d)
                        }),
                            (a.clear = function (b, c) {
                                b._webAnimationsPatchedStyle && b.style._clear(a.propertyName(c))
                            })
                    })(b),
                    (function (a) {
                        window.Element.prototype.animate = function (b, c) {
                            var d = ''
                            return c && c.id && (d = c.id), a.timeline._play(a.KeyframeEffect(this, b, c, d))
                        }
                    })(b),
                    (function (a, b) {
                        function c(a, b, d) {
                            if ('number' == typeof a && 'number' == typeof b) return a * (1 - d) + b * d
                            if ('boolean' == typeof a && 'boolean' == typeof b) return d < 0.5 ? a : b
                            if (a.length == b.length) {
                                for (var e = [], f = 0; f < a.length; f++) e.push(c(a[f], b[f], d))
                                return e
                            }
                            throw 'Mismatched interpolation arguments ' + a + ':' + b
                        }
                        a.Interpolation = function (a, b, d) {
                            return function (e) {
                                return d(c(a, b, e))
                            }
                        }
                    })(b),
                    (function (a, b) {
                        function c(a, b, c) {
                            return Math.max(Math.min(a, c), b)
                        }
                        function d(b, d, e) {
                            var f = a.dot(b, d)
                            f = c(f, -1, 1)
                            var g = []
                            if (1 === f) g = b
                            else
                                for (
                                    var h = Math.acos(f), i = (1 * Math.sin(e * h)) / Math.sqrt(1 - f * f), j = 0;
                                    j < 4;
                                    j++
                                )
                                    g.push(b[j] * (Math.cos(e * h) - f * i) + d[j] * i)
                            return g
                        }
                        var e = (function () {
                            function a(a, b) {
                                for (
                                    var c = [
                                            [0, 0, 0, 0],
                                            [0, 0, 0, 0],
                                            [0, 0, 0, 0],
                                            [0, 0, 0, 0],
                                        ],
                                        d = 0;
                                    d < 4;
                                    d++
                                )
                                    for (var e = 0; e < 4; e++) for (var f = 0; f < 4; f++) c[d][e] += b[d][f] * a[f][e]
                                return c
                            }
                            function b(a) {
                                return (
                                    0 == a[0][2] &&
                                    0 == a[0][3] &&
                                    0 == a[1][2] &&
                                    0 == a[1][3] &&
                                    0 == a[2][0] &&
                                    0 == a[2][1] &&
                                    1 == a[2][2] &&
                                    0 == a[2][3] &&
                                    0 == a[3][2] &&
                                    1 == a[3][3]
                                )
                            }
                            function c(c, d, e, f, g) {
                                for (
                                    var h = [
                                            [1, 0, 0, 0],
                                            [0, 1, 0, 0],
                                            [0, 0, 1, 0],
                                            [0, 0, 0, 1],
                                        ],
                                        i = 0;
                                    i < 4;
                                    i++
                                )
                                    h[i][3] = g[i]
                                for (var i = 0; i < 3; i++) for (var j = 0; j < 3; j++) h[3][i] += c[j] * h[j][i]
                                var k = f[0],
                                    l = f[1],
                                    m = f[2],
                                    n = f[3],
                                    o = [
                                        [1, 0, 0, 0],
                                        [0, 1, 0, 0],
                                        [0, 0, 1, 0],
                                        [0, 0, 0, 1],
                                    ]
                                ;(o[0][0] = 1 - 2 * (l * l + m * m)),
                                    (o[0][1] = 2 * (k * l - m * n)),
                                    (o[0][2] = 2 * (k * m + l * n)),
                                    (o[1][0] = 2 * (k * l + m * n)),
                                    (o[1][1] = 1 - 2 * (k * k + m * m)),
                                    (o[1][2] = 2 * (l * m - k * n)),
                                    (o[2][0] = 2 * (k * m - l * n)),
                                    (o[2][1] = 2 * (l * m + k * n)),
                                    (o[2][2] = 1 - 2 * (k * k + l * l)),
                                    (h = a(h, o))
                                var p = [
                                    [1, 0, 0, 0],
                                    [0, 1, 0, 0],
                                    [0, 0, 1, 0],
                                    [0, 0, 0, 1],
                                ]
                                e[2] && ((p[2][1] = e[2]), (h = a(h, p))),
                                    e[1] && ((p[2][1] = 0), (p[2][0] = e[0]), (h = a(h, p))),
                                    e[0] && ((p[2][0] = 0), (p[1][0] = e[0]), (h = a(h, p)))
                                for (var i = 0; i < 3; i++) for (var j = 0; j < 3; j++) h[i][j] *= d[i]
                                return b(h)
                                    ? [h[0][0], h[0][1], h[1][0], h[1][1], h[3][0], h[3][1]]
                                    : h[0].concat(h[1], h[2], h[3])
                            }
                            return c
                        })()
                        ;(a.composeMatrix = e), (a.quat = d)
                    })(b),
                    (function (a, b, c) {
                        a.sequenceNumber = 0
                        var d = function (a, b, c) {
                            ;(this.target = a),
                                (this.currentTime = b),
                                (this.timelineTime = c),
                                (this.type = 'finish'),
                                (this.bubbles = !1),
                                (this.cancelable = !1),
                                (this.currentTarget = a),
                                (this.defaultPrevented = !1),
                                (this.eventPhase = Event.AT_TARGET),
                                (this.timeStamp = Date.now())
                        }
                        ;(b.Animation = function (b) {
                            ;(this.id = ''),
                                b && b._id && (this.id = b._id),
                                (this._sequenceNumber = a.sequenceNumber++),
                                (this._currentTime = 0),
                                (this._startTime = null),
                                (this._paused = !1),
                                (this._playbackRate = 1),
                                (this._inTimeline = !0),
                                (this._finishedFlag = !0),
                                (this.onfinish = null),
                                (this._finishHandlers = []),
                                (this._effect = b),
                                (this._inEffect = this._effect._update(0)),
                                (this._idle = !0),
                                (this._currentTimePending = !1)
                        }),
                            (b.Animation.prototype = {
                                _ensureAlive: function () {
                                    this.playbackRate < 0 && 0 === this.currentTime
                                        ? (this._inEffect = this._effect._update(-1))
                                        : (this._inEffect = this._effect._update(this.currentTime)),
                                        this._inTimeline ||
                                            (!this._inEffect && this._finishedFlag) ||
                                            ((this._inTimeline = !0), b.timeline._animations.push(this))
                                },
                                _tickCurrentTime: function (a, b) {
                                    a != this._currentTime &&
                                        ((this._currentTime = a),
                                        this._isFinished &&
                                            !b &&
                                            (this._currentTime = this._playbackRate > 0 ? this._totalDuration : 0),
                                        this._ensureAlive())
                                },
                                get currentTime() {
                                    return this._idle || this._currentTimePending ? null : this._currentTime
                                },
                                set currentTime(a) {
                                    ;(a = +a),
                                        isNaN(a) ||
                                            (b.restart(),
                                            this._paused ||
                                                null == this._startTime ||
                                                (this._startTime = this._timeline.currentTime - a / this._playbackRate),
                                            (this._currentTimePending = !1),
                                            this._currentTime != a &&
                                                (this._idle && ((this._idle = !1), (this._paused = !0)),
                                                this._tickCurrentTime(a, !0),
                                                b.applyDirtiedAnimation(this)))
                                },
                                get startTime() {
                                    return this._startTime
                                },
                                set startTime(a) {
                                    ;(a = +a),
                                        isNaN(a) ||
                                            this._paused ||
                                            this._idle ||
                                            ((this._startTime = a),
                                            this._tickCurrentTime(
                                                (this._timeline.currentTime - this._startTime) * this.playbackRate,
                                            ),
                                            b.applyDirtiedAnimation(this))
                                },
                                get playbackRate() {
                                    return this._playbackRate
                                },
                                set playbackRate(a) {
                                    if (a != this._playbackRate) {
                                        var c = this.currentTime
                                        ;(this._playbackRate = a),
                                            (this._startTime = null),
                                            'paused' != this.playState &&
                                                'idle' != this.playState &&
                                                ((this._finishedFlag = !1),
                                                (this._idle = !1),
                                                this._ensureAlive(),
                                                b.applyDirtiedAnimation(this)),
                                            null != c && (this.currentTime = c)
                                    }
                                },
                                get _isFinished() {
                                    return (
                                        !this._idle &&
                                        ((this._playbackRate > 0 && this._currentTime >= this._totalDuration) ||
                                            (this._playbackRate < 0 && this._currentTime <= 0))
                                    )
                                },
                                get _totalDuration() {
                                    return this._effect._totalDuration
                                },
                                get playState() {
                                    return this._idle
                                        ? 'idle'
                                        : (null == this._startTime && !this._paused && 0 != this.playbackRate) ||
                                          this._currentTimePending
                                        ? 'pending'
                                        : this._paused
                                        ? 'paused'
                                        : this._isFinished
                                        ? 'finished'
                                        : 'running'
                                },
                                _rewind: function () {
                                    if (this._playbackRate >= 0) this._currentTime = 0
                                    else {
                                        if (!(this._totalDuration < 1 / 0))
                                            throw new DOMException(
                                                'Unable to rewind negative playback rate animation with infinite duration',
                                                'InvalidStateError',
                                            )
                                        this._currentTime = this._totalDuration
                                    }
                                },
                                play: function () {
                                    ;(this._paused = !1),
                                        (this._isFinished || this._idle) && (this._rewind(), (this._startTime = null)),
                                        (this._finishedFlag = !1),
                                        (this._idle = !1),
                                        this._ensureAlive(),
                                        b.applyDirtiedAnimation(this)
                                },
                                pause: function () {
                                    this._isFinished || this._paused || this._idle
                                        ? this._idle && (this._rewind(), (this._idle = !1))
                                        : (this._currentTimePending = !0),
                                        (this._startTime = null),
                                        (this._paused = !0)
                                },
                                finish: function () {
                                    this._idle ||
                                        ((this.currentTime = this._playbackRate > 0 ? this._totalDuration : 0),
                                        (this._startTime = this._totalDuration - this.currentTime),
                                        (this._currentTimePending = !1),
                                        b.applyDirtiedAnimation(this))
                                },
                                cancel: function () {
                                    this._inEffect &&
                                        ((this._inEffect = !1),
                                        (this._idle = !0),
                                        (this._paused = !1),
                                        (this._finishedFlag = !0),
                                        (this._currentTime = 0),
                                        (this._startTime = null),
                                        this._effect._update(null),
                                        b.applyDirtiedAnimation(this))
                                },
                                reverse: function () {
                                    ;(this.playbackRate *= -1), this.play()
                                },
                                addEventListener: function (a, b) {
                                    'function' == typeof b && 'finish' == a && this._finishHandlers.push(b)
                                },
                                removeEventListener: function (a, b) {
                                    if ('finish' == a) {
                                        var c = this._finishHandlers.indexOf(b)
                                        c >= 0 && this._finishHandlers.splice(c, 1)
                                    }
                                },
                                _fireEvents: function (a) {
                                    if (this._isFinished) {
                                        if (!this._finishedFlag) {
                                            var b = new d(this, this._currentTime, a),
                                                c = this._finishHandlers.concat(this.onfinish ? [this.onfinish] : [])
                                            setTimeout(function () {
                                                c.forEach(function (a) {
                                                    a.call(b.target, b)
                                                })
                                            }, 0),
                                                (this._finishedFlag = !0)
                                        }
                                    } else this._finishedFlag = !1
                                },
                                _tick: function (a, b) {
                                    this._idle ||
                                        this._paused ||
                                        (null == this._startTime
                                            ? b && (this.startTime = a - this._currentTime / this.playbackRate)
                                            : this._isFinished ||
                                              this._tickCurrentTime((a - this._startTime) * this.playbackRate)),
                                        b && ((this._currentTimePending = !1), this._fireEvents(a))
                                },
                                get _needsTick() {
                                    return this.playState in { pending: 1, running: 1 } || !this._finishedFlag
                                },
                                _targetAnimations: function () {
                                    var a = this._effect._target
                                    return a._activeAnimations || (a._activeAnimations = []), a._activeAnimations
                                },
                                _markTarget: function () {
                                    var a = this._targetAnimations()
                                    ;-1 === a.indexOf(this) && a.push(this)
                                },
                                _unmarkTarget: function () {
                                    var a = this._targetAnimations(),
                                        b = a.indexOf(this)
                                    ;-1 !== b && a.splice(b, 1)
                                },
                            })
                    })(a, b),
                    (function (a, b, c) {
                        function d(a) {
                            var b = j
                            ;(j = []),
                                a < q.currentTime && (a = q.currentTime),
                                q._animations.sort(e),
                                (q._animations = h(a, !0, q._animations)[0]),
                                b.forEach(function (b) {
                                    b[1](a)
                                }),
                                g()
                        }
                        function e(a, b) {
                            return a._sequenceNumber - b._sequenceNumber
                        }
                        function f() {
                            ;(this._animations = []),
                                (this.currentTime = window.performance && performance.now ? performance.now() : 0)
                        }
                        function g() {
                            o.forEach(function (a) {
                                a()
                            }),
                                (o.length = 0)
                        }
                        function h(a, c, d) {
                            ;(p = !0), (n = !1), (b.timeline.currentTime = a), (m = !1)
                            var e = [],
                                f = [],
                                g = [],
                                h = []
                            return (
                                d.forEach(function (b) {
                                    b._tick(a, c),
                                        b._inEffect
                                            ? (f.push(b._effect), b._markTarget())
                                            : (e.push(b._effect), b._unmarkTarget()),
                                        b._needsTick && (m = !0)
                                    var d = b._inEffect || b._needsTick
                                    ;(b._inTimeline = d), d ? g.push(b) : h.push(b)
                                }),
                                o.push.apply(o, e),
                                o.push.apply(o, f),
                                m && requestAnimationFrame(function () {}),
                                (p = !1),
                                [g, h]
                            )
                        }
                        var i = window.requestAnimationFrame,
                            j = [],
                            k = 0
                        ;(window.requestAnimationFrame = function (a) {
                            var b = k++
                            return 0 == j.length && i(d), j.push([b, a]), b
                        }),
                            (window.cancelAnimationFrame = function (a) {
                                j.forEach(function (b) {
                                    b[0] == a && (b[1] = function () {})
                                })
                            }),
                            (f.prototype = {
                                _play: function (c) {
                                    c._timing = a.normalizeTimingInput(c.timing)
                                    var d = new b.Animation(c)
                                    return (
                                        (d._idle = !1),
                                        (d._timeline = this),
                                        this._animations.push(d),
                                        b.restart(),
                                        b.applyDirtiedAnimation(d),
                                        d
                                    )
                                },
                            })
                        var m = !1,
                            n = !1
                        ;(b.restart = function () {
                            return m || ((m = !0), requestAnimationFrame(function () {}), (n = !0)), n
                        }),
                            (b.applyDirtiedAnimation = function (a) {
                                if (!p) {
                                    a._markTarget()
                                    var c = a._targetAnimations()
                                    c.sort(e),
                                        h(b.timeline.currentTime, !1, c.slice())[1].forEach(function (a) {
                                            var b = q._animations.indexOf(a)
                                            ;-1 !== b && q._animations.splice(b, 1)
                                        }),
                                        g()
                                }
                            })
                        var o = [],
                            p = !1,
                            q = new f()
                        b.timeline = q
                    })(a, b),
                    (function (a, b) {
                        function c(a, b) {
                            for (var c = 0, d = 0; d < a.length; d++) c += a[d] * b[d]
                            return c
                        }
                        function d(a, b) {
                            return [
                                a[0] * b[0] + a[4] * b[1] + a[8] * b[2] + a[12] * b[3],
                                a[1] * b[0] + a[5] * b[1] + a[9] * b[2] + a[13] * b[3],
                                a[2] * b[0] + a[6] * b[1] + a[10] * b[2] + a[14] * b[3],
                                a[3] * b[0] + a[7] * b[1] + a[11] * b[2] + a[15] * b[3],
                                a[0] * b[4] + a[4] * b[5] + a[8] * b[6] + a[12] * b[7],
                                a[1] * b[4] + a[5] * b[5] + a[9] * b[6] + a[13] * b[7],
                                a[2] * b[4] + a[6] * b[5] + a[10] * b[6] + a[14] * b[7],
                                a[3] * b[4] + a[7] * b[5] + a[11] * b[6] + a[15] * b[7],
                                a[0] * b[8] + a[4] * b[9] + a[8] * b[10] + a[12] * b[11],
                                a[1] * b[8] + a[5] * b[9] + a[9] * b[10] + a[13] * b[11],
                                a[2] * b[8] + a[6] * b[9] + a[10] * b[10] + a[14] * b[11],
                                a[3] * b[8] + a[7] * b[9] + a[11] * b[10] + a[15] * b[11],
                                a[0] * b[12] + a[4] * b[13] + a[8] * b[14] + a[12] * b[15],
                                a[1] * b[12] + a[5] * b[13] + a[9] * b[14] + a[13] * b[15],
                                a[2] * b[12] + a[6] * b[13] + a[10] * b[14] + a[14] * b[15],
                                a[3] * b[12] + a[7] * b[13] + a[11] * b[14] + a[15] * b[15],
                            ]
                        }
                        function e(a) {
                            var b = a.rad || 0
                            return ((a.deg || 0) / 360 + (a.grad || 0) / 400 + (a.turn || 0)) * (2 * Math.PI) + b
                        }
                        function f(a) {
                            switch (a.t) {
                                case 'rotatex':
                                    var b = e(a.d[0])
                                    return [
                                        1,
                                        0,
                                        0,
                                        0,
                                        0,
                                        Math.cos(b),
                                        Math.sin(b),
                                        0,
                                        0,
                                        -Math.sin(b),
                                        Math.cos(b),
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                    ]
                                case 'rotatey':
                                    var b = e(a.d[0])
                                    return [
                                        Math.cos(b),
                                        0,
                                        -Math.sin(b),
                                        0,
                                        0,
                                        1,
                                        0,
                                        0,
                                        Math.sin(b),
                                        0,
                                        Math.cos(b),
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                    ]
                                case 'rotate':
                                case 'rotatez':
                                    var b = e(a.d[0])
                                    return [
                                        Math.cos(b),
                                        Math.sin(b),
                                        0,
                                        0,
                                        -Math.sin(b),
                                        Math.cos(b),
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                    ]
                                case 'rotate3d':
                                    var c = a.d[0],
                                        d = a.d[1],
                                        f = a.d[2],
                                        b = e(a.d[3]),
                                        g = c * c + d * d + f * f
                                    if (0 === g) (c = 1), (d = 0), (f = 0)
                                    else if (1 !== g) {
                                        var h = Math.sqrt(g)
                                        ;(c /= h), (d /= h), (f /= h)
                                    }
                                    var i = Math.sin(b / 2),
                                        j = i * Math.cos(b / 2),
                                        k = i * i
                                    return [
                                        1 - 2 * (d * d + f * f) * k,
                                        2 * (c * d * k + f * j),
                                        2 * (c * f * k - d * j),
                                        0,
                                        2 * (c * d * k - f * j),
                                        1 - 2 * (c * c + f * f) * k,
                                        2 * (d * f * k + c * j),
                                        0,
                                        2 * (c * f * k + d * j),
                                        2 * (d * f * k - c * j),
                                        1 - 2 * (c * c + d * d) * k,
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                    ]
                                case 'scale':
                                    return [a.d[0], 0, 0, 0, 0, a.d[1], 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]
                                case 'scalex':
                                    return [a.d[0], 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]
                                case 'scaley':
                                    return [1, 0, 0, 0, 0, a.d[0], 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]
                                case 'scalez':
                                    return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, a.d[0], 0, 0, 0, 0, 1]
                                case 'scale3d':
                                    return [a.d[0], 0, 0, 0, 0, a.d[1], 0, 0, 0, 0, a.d[2], 0, 0, 0, 0, 1]
                                case 'skew':
                                    var l = e(a.d[0]),
                                        m = e(a.d[1])
                                    return [1, Math.tan(m), 0, 0, Math.tan(l), 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]
                                case 'skewx':
                                    var b = e(a.d[0])
                                    return [1, 0, 0, 0, Math.tan(b), 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]
                                case 'skewy':
                                    var b = e(a.d[0])
                                    return [1, Math.tan(b), 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]
                                case 'translate':
                                    var c = a.d[0].px || 0,
                                        d = a.d[1].px || 0
                                    return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, c, d, 0, 1]
                                case 'translatex':
                                    var c = a.d[0].px || 0
                                    return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, c, 0, 0, 1]
                                case 'translatey':
                                    var d = a.d[0].px || 0
                                    return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, d, 0, 1]
                                case 'translatez':
                                    var f = a.d[0].px || 0
                                    return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, f, 1]
                                case 'translate3d':
                                    var c = a.d[0].px || 0,
                                        d = a.d[1].px || 0,
                                        f = a.d[2].px || 0
                                    return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, c, d, f, 1]
                                case 'perspective':
                                    return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, a.d[0].px ? -1 / a.d[0].px : 0, 0, 0, 0, 1]
                                case 'matrix':
                                    return [
                                        a.d[0],
                                        a.d[1],
                                        0,
                                        0,
                                        a.d[2],
                                        a.d[3],
                                        0,
                                        0,
                                        0,
                                        0,
                                        1,
                                        0,
                                        a.d[4],
                                        a.d[5],
                                        0,
                                        1,
                                    ]
                                case 'matrix3d':
                                    return a.d
                            }
                        }
                        function g(a) {
                            return 0 === a.length
                                ? [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1]
                                : a.map(f).reduce(d)
                        }
                        function h(a) {
                            return [i(g(a))]
                        }
                        var i = (function () {
                            function a(a) {
                                return (
                                    a[0][0] * a[1][1] * a[2][2] +
                                    a[1][0] * a[2][1] * a[0][2] +
                                    a[2][0] * a[0][1] * a[1][2] -
                                    a[0][2] * a[1][1] * a[2][0] -
                                    a[1][2] * a[2][1] * a[0][0] -
                                    a[2][2] * a[0][1] * a[1][0]
                                )
                            }
                            function b(b) {
                                for (
                                    var c = 1 / a(b),
                                        d = b[0][0],
                                        e = b[0][1],
                                        f = b[0][2],
                                        g = b[1][0],
                                        h = b[1][1],
                                        i = b[1][2],
                                        j = b[2][0],
                                        k = b[2][1],
                                        l = b[2][2],
                                        m = [
                                            [(h * l - i * k) * c, (f * k - e * l) * c, (e * i - f * h) * c, 0],
                                            [(i * j - g * l) * c, (d * l - f * j) * c, (f * g - d * i) * c, 0],
                                            [(g * k - h * j) * c, (j * e - d * k) * c, (d * h - e * g) * c, 0],
                                        ],
                                        n = [],
                                        o = 0;
                                    o < 3;
                                    o++
                                ) {
                                    for (var p = 0, q = 0; q < 3; q++) p += b[3][q] * m[q][o]
                                    n.push(p)
                                }
                                return n.push(1), m.push(n), m
                            }
                            function d(a) {
                                return [
                                    [a[0][0], a[1][0], a[2][0], a[3][0]],
                                    [a[0][1], a[1][1], a[2][1], a[3][1]],
                                    [a[0][2], a[1][2], a[2][2], a[3][2]],
                                    [a[0][3], a[1][3], a[2][3], a[3][3]],
                                ]
                            }
                            function e(a, b) {
                                for (var c = [], d = 0; d < 4; d++) {
                                    for (var e = 0, f = 0; f < 4; f++) e += a[f] * b[f][d]
                                    c.push(e)
                                }
                                return c
                            }
                            function f(a) {
                                var b = g(a)
                                return [a[0] / b, a[1] / b, a[2] / b]
                            }
                            function g(a) {
                                return Math.sqrt(a[0] * a[0] + a[1] * a[1] + a[2] * a[2])
                            }
                            function h(a, b, c, d) {
                                return [c * a[0] + d * b[0], c * a[1] + d * b[1], c * a[2] + d * b[2]]
                            }
                            function i(a, b) {
                                return [a[1] * b[2] - a[2] * b[1], a[2] * b[0] - a[0] * b[2], a[0] * b[1] - a[1] * b[0]]
                            }
                            function j(j) {
                                var k = [j.slice(0, 4), j.slice(4, 8), j.slice(8, 12), j.slice(12, 16)]
                                if (1 !== k[3][3]) return null
                                for (var l = [], m = 0; m < 4; m++) l.push(k[m].slice())
                                for (var m = 0; m < 3; m++) l[m][3] = 0
                                if (0 === a(l)) return null
                                var n,
                                    o = []
                                k[0][3] || k[1][3] || k[2][3]
                                    ? (o.push(k[0][3]),
                                      o.push(k[1][3]),
                                      o.push(k[2][3]),
                                      o.push(k[3][3]),
                                      (n = e(o, d(b(l)))))
                                    : (n = [0, 0, 0, 1])
                                var p = k[3].slice(0, 3),
                                    q = []
                                q.push(k[0].slice(0, 3))
                                var r = []
                                r.push(g(q[0])), (q[0] = f(q[0]))
                                var s = []
                                q.push(k[1].slice(0, 3)),
                                    s.push(c(q[0], q[1])),
                                    (q[1] = h(q[1], q[0], 1, -s[0])),
                                    r.push(g(q[1])),
                                    (q[1] = f(q[1])),
                                    (s[0] /= r[1]),
                                    q.push(k[2].slice(0, 3)),
                                    s.push(c(q[0], q[2])),
                                    (q[2] = h(q[2], q[0], 1, -s[1])),
                                    s.push(c(q[1], q[2])),
                                    (q[2] = h(q[2], q[1], 1, -s[2])),
                                    r.push(g(q[2])),
                                    (q[2] = f(q[2])),
                                    (s[1] /= r[2]),
                                    (s[2] /= r[2])
                                var t = i(q[1], q[2])
                                if (c(q[0], t) < 0)
                                    for (var m = 0; m < 3; m++)
                                        (r[m] *= -1), (q[m][0] *= -1), (q[m][1] *= -1), (q[m][2] *= -1)
                                var u,
                                    v,
                                    w = q[0][0] + q[1][1] + q[2][2] + 1
                                return (
                                    w > 1e-4
                                        ? ((u = 0.5 / Math.sqrt(w)),
                                          (v = [
                                              (q[2][1] - q[1][2]) * u,
                                              (q[0][2] - q[2][0]) * u,
                                              (q[1][0] - q[0][1]) * u,
                                              0.25 / u,
                                          ]))
                                        : q[0][0] > q[1][1] && q[0][0] > q[2][2]
                                        ? ((u = 2 * Math.sqrt(1 + q[0][0] - q[1][1] - q[2][2])),
                                          (v = [
                                              0.25 * u,
                                              (q[0][1] + q[1][0]) / u,
                                              (q[0][2] + q[2][0]) / u,
                                              (q[2][1] - q[1][2]) / u,
                                          ]))
                                        : q[1][1] > q[2][2]
                                        ? ((u = 2 * Math.sqrt(1 + q[1][1] - q[0][0] - q[2][2])),
                                          (v = [
                                              (q[0][1] + q[1][0]) / u,
                                              0.25 * u,
                                              (q[1][2] + q[2][1]) / u,
                                              (q[0][2] - q[2][0]) / u,
                                          ]))
                                        : ((u = 2 * Math.sqrt(1 + q[2][2] - q[0][0] - q[1][1])),
                                          (v = [
                                              (q[0][2] + q[2][0]) / u,
                                              (q[1][2] + q[2][1]) / u,
                                              0.25 * u,
                                              (q[1][0] - q[0][1]) / u,
                                          ])),
                                    [p, r, s, v, n]
                                )
                            }
                            return j
                        })()
                        ;(a.dot = c), (a.makeMatrixDecomposition = h), (a.transformListToMatrix = g)
                    })(b),
                    (function (a) {
                        function b(a, b) {
                            var c = a.exec(b)
                            if (c) return (c = a.ignoreCase ? c[0].toLowerCase() : c[0]), [c, b.substr(c.length)]
                        }
                        function c(a, b) {
                            b = b.replace(/^\s*/, '')
                            var c = a(b)
                            if (c) return [c[0], c[1].replace(/^\s*/, '')]
                        }
                        function d(a, d, e) {
                            a = c.bind(null, a)
                            for (var f = []; ; ) {
                                var g = a(e)
                                if (!g) return [f, e]
                                if ((f.push(g[0]), (e = g[1]), !(g = b(d, e)) || '' == g[1])) return [f, e]
                                e = g[1]
                            }
                        }
                        function e(a, b) {
                            for (var c = 0, d = 0; d < b.length && (!/\s|,/.test(b[d]) || 0 != c); d++)
                                if ('(' == b[d]) c++
                                else if (')' == b[d] && (c--, 0 == c && d++, c <= 0)) break
                            var e = a(b.substr(0, d))
                            return void 0 == e ? void 0 : [e, b.substr(d)]
                        }
                        function f(a, b) {
                            for (var c = a, d = b; c && d; ) c > d ? (c %= d) : (d %= c)
                            return (c = (a * b) / (c + d))
                        }
                        function g(a) {
                            return function (b) {
                                var c = a(b)
                                return c && (c[0] = void 0), c
                            }
                        }
                        function h(a, b) {
                            return function (c) {
                                return a(c) || [b, c]
                            }
                        }
                        function i(b, c) {
                            for (var d = [], e = 0; e < b.length; e++) {
                                var f = a.consumeTrimmed(b[e], c)
                                if (!f || '' == f[0]) return
                                void 0 !== f[0] && d.push(f[0]), (c = f[1])
                            }
                            if ('' == c) return d
                        }
                        function j(a, b, c, d, e) {
                            for (var g = [], h = [], i = [], j = f(d.length, e.length), k = 0; k < j; k++) {
                                var l = b(d[k % d.length], e[k % e.length])
                                if (!l) return
                                g.push(l[0]), h.push(l[1]), i.push(l[2])
                            }
                            return [
                                g,
                                h,
                                function (b) {
                                    var d = b
                                        .map(function (a, b) {
                                            return i[b](a)
                                        })
                                        .join(c)
                                    return a ? a(d) : d
                                },
                            ]
                        }
                        function k(a, b, c) {
                            for (var d = [], e = [], f = [], g = 0, h = 0; h < c.length; h++)
                                if ('function' == typeof c[h]) {
                                    var i = c[h](a[g], b[g++])
                                    d.push(i[0]), e.push(i[1]), f.push(i[2])
                                } else
                                    !(function (a) {
                                        d.push(!1),
                                            e.push(!1),
                                            f.push(function () {
                                                return c[a]
                                            })
                                    })(h)
                            return [
                                d,
                                e,
                                function (a) {
                                    for (var b = '', c = 0; c < a.length; c++) b += f[c](a[c])
                                    return b
                                },
                            ]
                        }
                        ;(a.consumeToken = b),
                            (a.consumeTrimmed = c),
                            (a.consumeRepeated = d),
                            (a.consumeParenthesised = e),
                            (a.ignore = g),
                            (a.optional = h),
                            (a.consumeList = i),
                            (a.mergeNestedRepeated = j.bind(null, null)),
                            (a.mergeWrappedNestedRepeated = j),
                            (a.mergeList = k)
                    })(b),
                    (function (a) {
                        function b(b) {
                            function c(b) {
                                var c = a.consumeToken(/^inset/i, b)
                                return c
                                    ? ((d.inset = !0), c)
                                    : (c = a.consumeLengthOrPercent(b))
                                    ? (d.lengths.push(c[0]), c)
                                    : ((c = a.consumeColor(b)), c ? ((d.color = c[0]), c) : void 0)
                            }
                            var d = { inset: !1, lengths: [], color: null },
                                e = a.consumeRepeated(c, /^/, b)
                            if (e && e[0].length) return [d, e[1]]
                        }
                        function c(c) {
                            var d = a.consumeRepeated(b, /^,/, c)
                            if (d && '' == d[1]) return d[0]
                        }
                        function d(b, c) {
                            for (; b.lengths.length < Math.max(b.lengths.length, c.lengths.length); )
                                b.lengths.push({ px: 0 })
                            for (; c.lengths.length < Math.max(b.lengths.length, c.lengths.length); )
                                c.lengths.push({ px: 0 })
                            if (b.inset == c.inset && !!b.color == !!c.color) {
                                for (var d, e = [], f = [[], 0], g = [[], 0], h = 0; h < b.lengths.length; h++) {
                                    var i = a.mergeDimensions(b.lengths[h], c.lengths[h], 2 == h)
                                    f[0].push(i[0]), g[0].push(i[1]), e.push(i[2])
                                }
                                if (b.color && c.color) {
                                    var j = a.mergeColors(b.color, c.color)
                                    ;(f[1] = j[0]), (g[1] = j[1]), (d = j[2])
                                }
                                return [
                                    f,
                                    g,
                                    function (a) {
                                        for (var c = b.inset ? 'inset ' : ' ', f = 0; f < e.length; f++)
                                            c += e[f](a[0][f]) + ' '
                                        return d && (c += d(a[1])), c
                                    },
                                ]
                            }
                        }
                        function e(b, c, d, e) {
                            function f(a) {
                                return {
                                    inset: a,
                                    color: [0, 0, 0, 0],
                                    lengths: [{ px: 0 }, { px: 0 }, { px: 0 }, { px: 0 }],
                                }
                            }
                            for (var g = [], h = [], i = 0; i < d.length || i < e.length; i++) {
                                var j = d[i] || f(e[i].inset),
                                    k = e[i] || f(d[i].inset)
                                g.push(j), h.push(k)
                            }
                            return a.mergeNestedRepeated(b, c, g, h)
                        }
                        var f = e.bind(null, d, ', ')
                        a.addPropertiesHandler(c, f, ['box-shadow', 'text-shadow'])
                    })(b),
                    (function (a, b) {
                        function c(a) {
                            return a.toFixed(3).replace(/0+$/, '').replace(/\.$/, '')
                        }
                        function d(a, b, c) {
                            return Math.min(b, Math.max(a, c))
                        }
                        function e(a) {
                            if (/^\s*[-+]?(\d*\.)?\d+\s*$/.test(a)) return Number(a)
                        }
                        function f(a, b) {
                            return [a, b, c]
                        }
                        function g(a, b) {
                            if (0 != a) return i(0, 1 / 0)(a, b)
                        }
                        function h(a, b) {
                            return [
                                a,
                                b,
                                function (a) {
                                    return Math.round(d(1, 1 / 0, a))
                                },
                            ]
                        }
                        function i(a, b) {
                            return function (e, f) {
                                return [
                                    e,
                                    f,
                                    function (e) {
                                        return c(d(a, b, e))
                                    },
                                ]
                            }
                        }
                        function j(a) {
                            var b = a.trim().split(/\s*[\s,]\s*/)
                            if (0 !== b.length) {
                                for (var c = [], d = 0; d < b.length; d++) {
                                    var f = e(b[d])
                                    if (void 0 === f) return
                                    c.push(f)
                                }
                                return c
                            }
                        }
                        function k(a, b) {
                            if (a.length == b.length)
                                return [
                                    a,
                                    b,
                                    function (a) {
                                        return a.map(c).join(' ')
                                    },
                                ]
                        }
                        function l(a, b) {
                            return [a, b, Math.round]
                        }
                        ;(a.clamp = d),
                            a.addPropertiesHandler(j, k, ['stroke-dasharray']),
                            a.addPropertiesHandler(e, i(0, 1 / 0), ['border-image-width', 'line-height']),
                            a.addPropertiesHandler(e, i(0, 1), ['opacity', 'shape-image-threshold']),
                            a.addPropertiesHandler(e, g, ['flex-grow', 'flex-shrink']),
                            a.addPropertiesHandler(e, h, ['orphans', 'widows']),
                            a.addPropertiesHandler(e, l, ['z-index']),
                            (a.parseNumber = e),
                            (a.parseNumberList = j),
                            (a.mergeNumbers = f),
                            (a.numberToString = c)
                    })(b),
                    (function (a, b) {
                        function c(a, b) {
                            if ('visible' == a || 'visible' == b)
                                return [
                                    0,
                                    1,
                                    function (c) {
                                        return c <= 0 ? a : c >= 1 ? b : 'visible'
                                    },
                                ]
                        }
                        a.addPropertiesHandler(String, c, ['visibility'])
                    })(b),
                    (function (a, b) {
                        function c(a) {
                            ;(a = a.trim()), (f.fillStyle = '#000'), (f.fillStyle = a)
                            var b = f.fillStyle
                            if (((f.fillStyle = '#fff'), (f.fillStyle = a), b == f.fillStyle)) {
                                f.fillRect(0, 0, 1, 1)
                                var c = f.getImageData(0, 0, 1, 1).data
                                f.clearRect(0, 0, 1, 1)
                                var d = c[3] / 255
                                return [c[0] * d, c[1] * d, c[2] * d, d]
                            }
                        }
                        function d(b, c) {
                            return [
                                b,
                                c,
                                function (b) {
                                    function c(a) {
                                        return Math.max(0, Math.min(255, a))
                                    }
                                    if (b[3]) for (var d = 0; d < 3; d++) b[d] = Math.round(c(b[d] / b[3]))
                                    return (b[3] = a.numberToString(a.clamp(0, 1, b[3]))), 'rgba(' + b.join(',') + ')'
                                },
                            ]
                        }
                        var e = document.createElementNS('http://www.w3.org/1999/xhtml', 'canvas')
                        e.width = e.height = 1
                        var f = e.getContext('2d')
                        a.addPropertiesHandler(c, d, [
                            'background-color',
                            'border-bottom-color',
                            'border-left-color',
                            'border-right-color',
                            'border-top-color',
                            'color',
                            'fill',
                            'flood-color',
                            'lighting-color',
                            'outline-color',
                            'stop-color',
                            'stroke',
                            'text-decoration-color',
                        ]),
                            (a.consumeColor = a.consumeParenthesised.bind(null, c)),
                            (a.mergeColors = d)
                    })(b),
                    (function (a, b) {
                        function c(a) {
                            function b() {
                                var b = h.exec(a)
                                g = b ? b[0] : void 0
                            }
                            function c() {
                                var a = Number(g)
                                return b(), a
                            }
                            function d() {
                                if ('(' !== g) return c()
                                b()
                                var a = f()
                                return ')' !== g ? NaN : (b(), a)
                            }
                            function e() {
                                for (var a = d(); '*' === g || '/' === g; ) {
                                    var c = g
                                    b()
                                    var e = d()
                                    '*' === c ? (a *= e) : (a /= e)
                                }
                                return a
                            }
                            function f() {
                                for (var a = e(); '+' === g || '-' === g; ) {
                                    var c = g
                                    b()
                                    var d = e()
                                    '+' === c ? (a += d) : (a -= d)
                                }
                                return a
                            }
                            var g,
                                h = /([\+\-\w\.]+|[\(\)\*\/])/g
                            return b(), f()
                        }
                        function d(a, b) {
                            if ('0' == (b = b.trim().toLowerCase()) && 'px'.search(a) >= 0) return { px: 0 }
                            if (/^[^(]*$|^calc/.test(b)) {
                                b = b.replace(/calc\(/g, '(')
                                var d = {}
                                b = b.replace(a, function (a) {
                                    return (d[a] = null), 'U' + a
                                })
                                for (
                                    var e = 'U(' + a.source + ')',
                                        f = b
                                            .replace(/[-+]?(\d*\.)?\d+([Ee][-+]?\d+)?/g, 'N')
                                            .replace(new RegExp('N' + e, 'g'), 'D')
                                            .replace(/\s[+-]\s/g, 'O')
                                            .replace(/\s/g, ''),
                                        g = [/N\*(D)/g, /(N|D)[*\/]N/g, /(N|D)O\1/g, /\((N|D)\)/g],
                                        h = 0;
                                    h < g.length;

                                )
                                    g[h].test(f) ? ((f = f.replace(g[h], '$1')), (h = 0)) : h++
                                if ('D' == f) {
                                    for (var i in d) {
                                        var j = c(
                                            b.replace(new RegExp('U' + i, 'g'), '').replace(new RegExp(e, 'g'), '*0'),
                                        )
                                        if (!isFinite(j)) return
                                        d[i] = j
                                    }
                                    return d
                                }
                            }
                        }
                        function e(a, b) {
                            return f(a, b, !0)
                        }
                        function f(b, c, d) {
                            var e,
                                f = []
                            for (e in b) f.push(e)
                            for (e in c) f.indexOf(e) < 0 && f.push(e)
                            return (
                                (b = f.map(function (a) {
                                    return b[a] || 0
                                })),
                                (c = f.map(function (a) {
                                    return c[a] || 0
                                })),
                                [
                                    b,
                                    c,
                                    function (b) {
                                        var c = b
                                            .map(function (c, e) {
                                                return (
                                                    1 == b.length && d && (c = Math.max(c, 0)),
                                                    a.numberToString(c) + f[e]
                                                )
                                            })
                                            .join(' + ')
                                        return b.length > 1 ? 'calc(' + c + ')' : c
                                    },
                                ]
                            )
                        }
                        var g = 'px|em|ex|ch|rem|vw|vh|vmin|vmax|cm|mm|in|pt|pc',
                            h = d.bind(null, new RegExp(g, 'g')),
                            i = d.bind(null, new RegExp(g + '|%', 'g')),
                            j = d.bind(null, /deg|rad|grad|turn/g)
                        ;(a.parseLength = h),
                            (a.parseLengthOrPercent = i),
                            (a.consumeLengthOrPercent = a.consumeParenthesised.bind(null, i)),
                            (a.parseAngle = j),
                            (a.mergeDimensions = f)
                        var k = a.consumeParenthesised.bind(null, h),
                            l = a.consumeRepeated.bind(void 0, k, /^/),
                            m = a.consumeRepeated.bind(void 0, l, /^,/)
                        a.consumeSizePairList = m
                        var n = function (a) {
                                var b = m(a)
                                if (b && '' == b[1]) return b[0]
                            },
                            o = a.mergeNestedRepeated.bind(void 0, e, ' '),
                            p = a.mergeNestedRepeated.bind(void 0, o, ',')
                        ;(a.mergeNonNegativeSizePair = o),
                            a.addPropertiesHandler(n, p, ['background-size']),
                            a.addPropertiesHandler(i, e, [
                                'border-bottom-width',
                                'border-image-width',
                                'border-left-width',
                                'border-right-width',
                                'border-top-width',
                                'flex-basis',
                                'font-size',
                                'height',
                                'line-height',
                                'max-height',
                                'max-width',
                                'outline-width',
                                'width',
                            ]),
                            a.addPropertiesHandler(i, f, [
                                'border-bottom-left-radius',
                                'border-bottom-right-radius',
                                'border-top-left-radius',
                                'border-top-right-radius',
                                'bottom',
                                'left',
                                'letter-spacing',
                                'margin-bottom',
                                'margin-left',
                                'margin-right',
                                'margin-top',
                                'min-height',
                                'min-width',
                                'outline-offset',
                                'padding-bottom',
                                'padding-left',
                                'padding-right',
                                'padding-top',
                                'perspective',
                                'right',
                                'shape-margin',
                                'stroke-dashoffset',
                                'text-indent',
                                'top',
                                'vertical-align',
                                'word-spacing',
                            ])
                    })(b),
                    (function (a, b) {
                        function c(b) {
                            return a.consumeLengthOrPercent(b) || a.consumeToken(/^auto/, b)
                        }
                        function d(b) {
                            var d = a.consumeList(
                                [
                                    a.ignore(a.consumeToken.bind(null, /^rect/)),
                                    a.ignore(a.consumeToken.bind(null, /^\(/)),
                                    a.consumeRepeated.bind(null, c, /^,/),
                                    a.ignore(a.consumeToken.bind(null, /^\)/)),
                                ],
                                b,
                            )
                            if (d && 4 == d[0].length) return d[0]
                        }
                        function e(b, c) {
                            return 'auto' == b || 'auto' == c
                                ? [
                                      !0,
                                      !1,
                                      function (d) {
                                          var e = d ? b : c
                                          if ('auto' == e) return 'auto'
                                          var f = a.mergeDimensions(e, e)
                                          return f[2](f[0])
                                      },
                                  ]
                                : a.mergeDimensions(b, c)
                        }
                        function f(a) {
                            return 'rect(' + a + ')'
                        }
                        var g = a.mergeWrappedNestedRepeated.bind(null, f, e, ', ')
                        ;(a.parseBox = d), (a.mergeBoxes = g), a.addPropertiesHandler(d, g, ['clip'])
                    })(b),
                    (function (a, b) {
                        function c(a) {
                            return function (b) {
                                var c = 0
                                return a.map(function (a) {
                                    return a === k ? b[c++] : a
                                })
                            }
                        }
                        function d(a) {
                            return a
                        }
                        function e(b) {
                            if ('none' == (b = b.toLowerCase().trim())) return []
                            for (var c, d = /\s*(\w+)\(([^)]*)\)/g, e = [], f = 0; (c = d.exec(b)); ) {
                                if (c.index != f) return
                                f = c.index + c[0].length
                                var g = c[1],
                                    h = n[g]
                                if (!h) return
                                var i = c[2].split(','),
                                    j = h[0]
                                if (j.length < i.length) return
                                for (var k = [], o = 0; o < j.length; o++) {
                                    var p,
                                        q = i[o],
                                        r = j[o]
                                    if (
                                        void 0 ===
                                        (p = q
                                            ? {
                                                  A: function (b) {
                                                      return '0' == b.trim() ? m : a.parseAngle(b)
                                                  },
                                                  N: a.parseNumber,
                                                  T: a.parseLengthOrPercent,
                                                  L: a.parseLength,
                                              }[r.toUpperCase()](q)
                                            : { a: m, n: k[0], t: l }[r])
                                    )
                                        return
                                    k.push(p)
                                }
                                if ((e.push({ t: g, d: k }), d.lastIndex == b.length)) return e
                            }
                        }
                        function f(a) {
                            return a.toFixed(6).replace('.000000', '')
                        }
                        function g(b, c) {
                            if (b.decompositionPair !== c) {
                                b.decompositionPair = c
                                var d = a.makeMatrixDecomposition(b)
                            }
                            if (c.decompositionPair !== b) {
                                c.decompositionPair = b
                                var e = a.makeMatrixDecomposition(c)
                            }
                            return null == d[0] || null == e[0]
                                ? [
                                      [!1],
                                      [!0],
                                      function (a) {
                                          return a ? c[0].d : b[0].d
                                      },
                                  ]
                                : (d[0].push(0),
                                  e[0].push(1),
                                  [
                                      d,
                                      e,
                                      function (b) {
                                          var c = a.quat(d[0][3], e[0][3], b[5])
                                          return a.composeMatrix(b[0], b[1], b[2], c, b[4]).map(f).join(',')
                                      },
                                  ])
                        }
                        function h(a) {
                            return a.replace(/[xy]/, '')
                        }
                        function i(a) {
                            return a.replace(/(x|y|z|3d)?$/, '3d')
                        }
                        function j(b, c) {
                            var d = a.makeMatrixDecomposition && !0,
                                e = !1
                            if (!b.length || !c.length) {
                                b.length || ((e = !0), (b = c), (c = []))
                                for (var f = 0; f < b.length; f++) {
                                    var j = b[f].t,
                                        k = b[f].d,
                                        l = 'scale' == j.substr(0, 5) ? 1 : 0
                                    c.push({
                                        t: j,
                                        d: k.map(function (a) {
                                            if ('number' == typeof a) return l
                                            var b = {}
                                            for (var c in a) b[c] = l
                                            return b
                                        }),
                                    })
                                }
                            }
                            var m = function (a, b) {
                                    return (
                                        ('perspective' == a && 'perspective' == b) ||
                                        (('matrix' == a || 'matrix3d' == a) && ('matrix' == b || 'matrix3d' == b))
                                    )
                                },
                                o = [],
                                p = [],
                                q = []
                            if (b.length != c.length) {
                                if (!d) return
                                var r = g(b, c)
                                ;(o = [r[0]]), (p = [r[1]]), (q = [['matrix', [r[2]]]])
                            } else
                                for (var f = 0; f < b.length; f++) {
                                    var j,
                                        s = b[f].t,
                                        t = c[f].t,
                                        u = b[f].d,
                                        v = c[f].d,
                                        w = n[s],
                                        x = n[t]
                                    if (m(s, t)) {
                                        if (!d) return
                                        var r = g([b[f]], [c[f]])
                                        o.push(r[0]), p.push(r[1]), q.push(['matrix', [r[2]]])
                                    } else {
                                        if (s == t) j = s
                                        else if (w[2] && x[2] && h(s) == h(t)) (j = h(s)), (u = w[2](u)), (v = x[2](v))
                                        else {
                                            if (!w[1] || !x[1] || i(s) != i(t)) {
                                                if (!d) return
                                                var r = g(b, c)
                                                ;(o = [r[0]]), (p = [r[1]]), (q = [['matrix', [r[2]]]])
                                                break
                                            }
                                            ;(j = i(s)), (u = w[1](u)), (v = x[1](v))
                                        }
                                        for (var y = [], z = [], A = [], B = 0; B < u.length; B++) {
                                            var C = 'number' == typeof u[B] ? a.mergeNumbers : a.mergeDimensions,
                                                r = C(u[B], v[B])
                                            ;(y[B] = r[0]), (z[B] = r[1]), A.push(r[2])
                                        }
                                        o.push(y), p.push(z), q.push([j, A])
                                    }
                                }
                            if (e) {
                                var D = o
                                ;(o = p), (p = D)
                            }
                            return [
                                o,
                                p,
                                function (a) {
                                    return a
                                        .map(function (a, b) {
                                            var c = a
                                                .map(function (a, c) {
                                                    return q[b][1][c](a)
                                                })
                                                .join(',')
                                            return (
                                                'matrix' == q[b][0] &&
                                                    16 == c.split(',').length &&
                                                    (q[b][0] = 'matrix3d'),
                                                q[b][0] + '(' + c + ')'
                                            )
                                        })
                                        .join(' ')
                                },
                            ]
                        }
                        var k = null,
                            l = { px: 0 },
                            m = { deg: 0 },
                            n = {
                                matrix: ['NNNNNN', [k, k, 0, 0, k, k, 0, 0, 0, 0, 1, 0, k, k, 0, 1], d],
                                matrix3d: ['NNNNNNNNNNNNNNNN', d],
                                rotate: ['A'],
                                rotatex: ['A'],
                                rotatey: ['A'],
                                rotatez: ['A'],
                                rotate3d: ['NNNA'],
                                perspective: ['L'],
                                scale: ['Nn', c([k, k, 1]), d],
                                scalex: ['N', c([k, 1, 1]), c([k, 1])],
                                scaley: ['N', c([1, k, 1]), c([1, k])],
                                scalez: ['N', c([1, 1, k])],
                                scale3d: ['NNN', d],
                                skew: ['Aa', null, d],
                                skewx: ['A', null, c([k, m])],
                                skewy: ['A', null, c([m, k])],
                                translate: ['Tt', c([k, k, l]), d],
                                translatex: ['T', c([k, l, l]), c([k, l])],
                                translatey: ['T', c([l, k, l]), c([l, k])],
                                translatez: ['L', c([l, l, k])],
                                translate3d: ['TTL', d],
                            }
                        a.addPropertiesHandler(e, j, ['transform']),
                            (a.transformToSvgMatrix = function (b) {
                                var c = a.transformListToMatrix(e(b))
                                return (
                                    'matrix(' +
                                    f(c[0]) +
                                    ' ' +
                                    f(c[1]) +
                                    ' ' +
                                    f(c[4]) +
                                    ' ' +
                                    f(c[5]) +
                                    ' ' +
                                    f(c[12]) +
                                    ' ' +
                                    f(c[13]) +
                                    ')'
                                )
                            })
                    })(b),
                    (function (a) {
                        function b(a) {
                            var b = Number(a)
                            if (!(isNaN(b) || b < 100 || b > 900 || b % 100 != 0)) return b
                        }
                        function c(b) {
                            return (
                                (b = 100 * Math.round(b / 100)),
                                (b = a.clamp(100, 900, b)),
                                400 === b ? 'normal' : 700 === b ? 'bold' : String(b)
                            )
                        }
                        function d(a, b) {
                            return [a, b, c]
                        }
                        a.addPropertiesHandler(b, d, ['font-weight'])
                    })(b),
                    (function (a) {
                        function b(a) {
                            var b = {}
                            for (var c in a) b[c] = -a[c]
                            return b
                        }
                        function c(b) {
                            return (
                                a.consumeToken(/^(left|center|right|top|bottom)\b/i, b) || a.consumeLengthOrPercent(b)
                            )
                        }
                        function d(b, d) {
                            var e = a.consumeRepeated(c, /^/, d)
                            if (e && '' == e[1]) {
                                var f = e[0]
                                if (
                                    ((f[0] = f[0] || 'center'),
                                    (f[1] = f[1] || 'center'),
                                    3 == b && (f[2] = f[2] || { px: 0 }),
                                    f.length == b)
                                ) {
                                    if (/top|bottom/.test(f[0]) || /left|right/.test(f[1])) {
                                        var h = f[0]
                                        ;(f[0] = f[1]), (f[1] = h)
                                    }
                                    if (/left|right|center|Object/.test(f[0]) && /top|bottom|center|Object/.test(f[1]))
                                        return f.map(function (a) {
                                            return 'object' == typeof a ? a : g[a]
                                        })
                                }
                            }
                        }
                        function e(d) {
                            var e = a.consumeRepeated(c, /^/, d)
                            if (e) {
                                for (
                                    var f = e[0], h = [{ '%': 50 }, { '%': 50 }], i = 0, j = !1, k = 0;
                                    k < f.length;
                                    k++
                                ) {
                                    var l = f[k]
                                    'string' == typeof l
                                        ? ((j = /bottom|right/.test(l)),
                                          (i = { left: 0, right: 0, center: i, top: 1, bottom: 1 }[l]),
                                          (h[i] = g[l]),
                                          'center' == l && i++)
                                        : (j && ((l = b(l)), (l['%'] = (l['%'] || 0) + 100)), (h[i] = l), i++, (j = !1))
                                }
                                return [h, e[1]]
                            }
                        }
                        function f(b) {
                            var c = a.consumeRepeated(e, /^,/, b)
                            if (c && '' == c[1]) return c[0]
                        }
                        var g = {
                                left: { '%': 0 },
                                center: { '%': 50 },
                                right: { '%': 100 },
                                top: { '%': 0 },
                                bottom: { '%': 100 },
                            },
                            h = a.mergeNestedRepeated.bind(null, a.mergeDimensions, ' ')
                        a.addPropertiesHandler(d.bind(null, 3), h, ['transform-origin']),
                            a.addPropertiesHandler(d.bind(null, 2), h, ['perspective-origin']),
                            (a.consumePosition = e),
                            (a.mergeOffsetList = h)
                        var i = a.mergeNestedRepeated.bind(null, h, ', ')
                        a.addPropertiesHandler(f, i, ['background-position', 'object-position'])
                    })(b),
                    (function (a) {
                        function b(b) {
                            var c = a.consumeToken(/^circle/, b)
                            if (c && c[0])
                                return ['circle'].concat(
                                    a.consumeList(
                                        [
                                            a.ignore(a.consumeToken.bind(void 0, /^\(/)),
                                            d,
                                            a.ignore(a.consumeToken.bind(void 0, /^at/)),
                                            a.consumePosition,
                                            a.ignore(a.consumeToken.bind(void 0, /^\)/)),
                                        ],
                                        c[1],
                                    ),
                                )
                            var f = a.consumeToken(/^ellipse/, b)
                            if (f && f[0])
                                return ['ellipse'].concat(
                                    a.consumeList(
                                        [
                                            a.ignore(a.consumeToken.bind(void 0, /^\(/)),
                                            e,
                                            a.ignore(a.consumeToken.bind(void 0, /^at/)),
                                            a.consumePosition,
                                            a.ignore(a.consumeToken.bind(void 0, /^\)/)),
                                        ],
                                        f[1],
                                    ),
                                )
                            var g = a.consumeToken(/^polygon/, b)
                            return g && g[0]
                                ? ['polygon'].concat(
                                      a.consumeList(
                                          [
                                              a.ignore(a.consumeToken.bind(void 0, /^\(/)),
                                              a.optional(
                                                  a.consumeToken.bind(void 0, /^nonzero\s*,|^evenodd\s*,/),
                                                  'nonzero,',
                                              ),
                                              a.consumeSizePairList,
                                              a.ignore(a.consumeToken.bind(void 0, /^\)/)),
                                          ],
                                          g[1],
                                      ),
                                  )
                                : void 0
                        }
                        function c(b, c) {
                            if (b[0] === c[0])
                                return 'circle' == b[0]
                                    ? a.mergeList(b.slice(1), c.slice(1), [
                                          'circle(',
                                          a.mergeDimensions,
                                          ' at ',
                                          a.mergeOffsetList,
                                          ')',
                                      ])
                                    : 'ellipse' == b[0]
                                    ? a.mergeList(b.slice(1), c.slice(1), [
                                          'ellipse(',
                                          a.mergeNonNegativeSizePair,
                                          ' at ',
                                          a.mergeOffsetList,
                                          ')',
                                      ])
                                    : 'polygon' == b[0] && b[1] == c[1]
                                    ? a.mergeList(b.slice(2), c.slice(2), ['polygon(', b[1], g, ')'])
                                    : void 0
                        }
                        var d = a.consumeParenthesised.bind(null, a.parseLengthOrPercent),
                            e = a.consumeRepeated.bind(void 0, d, /^/),
                            f = a.mergeNestedRepeated.bind(void 0, a.mergeDimensions, ' '),
                            g = a.mergeNestedRepeated.bind(void 0, f, ',')
                        a.addPropertiesHandler(b, c, ['shape-outside'])
                    })(b),
                    (function (a, b) {
                        function c(a, b) {
                            b.concat([a]).forEach(function (b) {
                                b in document.documentElement.style && (d[a] = b), (e[b] = a)
                            })
                        }
                        var d = {},
                            e = {}
                        c('transform', ['webkitTransform', 'msTransform']),
                            c('transformOrigin', ['webkitTransformOrigin']),
                            c('perspective', ['webkitPerspective']),
                            c('perspectiveOrigin', ['webkitPerspectiveOrigin']),
                            (a.propertyName = function (a) {
                                return d[a] || a
                            }),
                            (a.unprefixedPropertyName = function (a) {
                                return e[a] || a
                            })
                    })(b)
            })(),
            (function () {
                if (void 0 === document.createElement('div').animate([]).oncancel) {
                    var a
                    if (window.performance && performance.now)
                        var a = function () {
                            return performance.now()
                        }
                    else
                        var a = function () {
                            return Date.now()
                        }
                    var b = function (a, b, c) {
                            ;(this.target = a),
                                (this.currentTime = b),
                                (this.timelineTime = c),
                                (this.type = 'cancel'),
                                (this.bubbles = !1),
                                (this.cancelable = !1),
                                (this.currentTarget = a),
                                (this.defaultPrevented = !1),
                                (this.eventPhase = Event.AT_TARGET),
                                (this.timeStamp = Date.now())
                        },
                        c = window.Element.prototype.animate
                    window.Element.prototype.animate = function (d, e) {
                        var f = c.call(this, d, e)
                        ;(f._cancelHandlers = []), (f.oncancel = null)
                        var g = f.cancel
                        f.cancel = function () {
                            g.call(this)
                            var c = new b(this, null, a()),
                                d = this._cancelHandlers.concat(this.oncancel ? [this.oncancel] : [])
                            setTimeout(function () {
                                d.forEach(function (a) {
                                    a.call(c.target, c)
                                })
                            }, 0)
                        }
                        var h = f.addEventListener
                        f.addEventListener = function (a, b) {
                            'function' == typeof b && 'cancel' == a ? this._cancelHandlers.push(b) : h.call(this, a, b)
                        }
                        var i = f.removeEventListener
                        return (
                            (f.removeEventListener = function (a, b) {
                                if ('cancel' == a) {
                                    var c = this._cancelHandlers.indexOf(b)
                                    c >= 0 && this._cancelHandlers.splice(c, 1)
                                } else i.call(this, a, b)
                            }),
                            f
                        )
                    }
                }
            })(),
            (function (a) {
                var b = document.documentElement,
                    c = null,
                    d = !1
                try {
                    var e = getComputedStyle(b).getPropertyValue('opacity'),
                        f = '0' == e ? '1' : '0'
                    ;(c = b.animate({ opacity: [f, f] }, { duration: 1 })),
                        (c.currentTime = 0),
                        (d = getComputedStyle(b).getPropertyValue('opacity') == f)
                } catch (a) {
                } finally {
                    c && c.cancel()
                }
                if (!d) {
                    var g = window.Element.prototype.animate
                    window.Element.prototype.animate = function (b, c) {
                        return (
                            window.Symbol &&
                                Symbol.iterator &&
                                Array.prototype.from &&
                                b[Symbol.iterator] &&
                                (b = Array.from(b)),
                            Array.isArray(b) || null === b || (b = a.convertToArrayForm(b)),
                            g.call(this, b, c)
                        )
                    }
                }
            })(a)
    })()

    // Hide nags - Doing this here instead of with CSS keeps users from thinking they have a blank screen
    // when another plugin has JS code running and breaking things.
    Array.from(document.querySelectorAll('#wpbody-content > *:not(.metagallery-allowed)')).forEach(function (element) {
        return element.style.setProperty('display', 'none', 'important')
    })

    // Since the @wordpress/i18n plugin is buggy as usual,
    // for now just mock the translation plugin until they fix it
    window.__ = function (string, _textDomain) {
        return string
    }

    // Load in any state controllers
    window.CurrentGallery = Current

    // Load in models
    window.Gallery = Gallery$1
    window.GalleryImage = GalleryImage

    // Register image sources
    window.MediaLibrary = MediaLibrary

    // Start Alpine and pause the global observer
    window.Alpine.pauseMutationObserver = true
    window.Muuri = Grid

    // GalleryAPI.all().then(({ data }) => {
    //     console.log(data)
    // })
})
