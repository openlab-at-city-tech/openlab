import NggPostThumbnail from './components/ngg-post-thumbnail.min'
const {memoize}         = lodash

// Gets the original component which we will wrap
const getOriginalComponent = obj => memoize(prop => {
    return obj[prop]
})

// Wrap wp && wp.editor in a Proxy. We can then override the wp.editor.PostFeaturedImage component
// TODO: All of this logic should be encapsulated into a utility of some kind.
const wpEditorProxy = {
    get(obj, prop) {
        if (prop == 'isProxy') return true
        else if (prop == 'PostFeaturedImage') {
            return NggPostThumbnail(getOriginalComponent(obj)(prop))
        }
        return obj[prop]
    }
 }
const wpProxy = {
    get(obj, prop) {
        if (prop == 'editor') {
            if (obj[prop] && !obj[prop].isProxy) {
                obj[prop] = new Proxy(obj[prop], wpEditorProxy)        
            }
        }
        return obj[prop]
    },

    set(obj, prop, value) {
        if (prop == 'editor' && !value.isProxy) {
            value = new Proxy(value, wpEditorProxy)
        }
        obj[prop] = value
        return value
    }
}
window.wp.originalEditor = window.wp.editor;
window.wp = new Proxy(wp, wpProxy);
