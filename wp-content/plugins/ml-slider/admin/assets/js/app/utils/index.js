import EventManager from './events'
import Helpers from './helpers'
const { __, _x, _n, _nx, sprintf, setLocaleData } = wp.i18n;

// Let others hook into events
if (window.metaslider.eventHooks) {
	Object.keys(window.metaslider.eventHooks).forEach(hook => {
		EventManager.$on(hook, window.metaslider.eventHooks[hook])
	})
}

export { EventManager, Helpers, __, _x, setLocaleData, _n, _nx, sprintf }
