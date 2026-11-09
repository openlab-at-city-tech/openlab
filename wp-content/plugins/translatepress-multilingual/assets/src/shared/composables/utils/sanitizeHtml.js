/**
 * Minimal allowlist HTML sanitizer.
 *
 * @param {string} html - Raw HTML to sanitize
 * @param {Object} allowed - Map of allowed tags → allowed attributes
 *   e.g. { a: ['href', 'target', 'rel'], strong: [], em: [] }
 * @return {string} - Sanitized HTML string
 */
export function sanitizeHtml( html, allowed = {} ) {
    const doc = new DOMParser().parseFromString( html, 'text/html' )

    const walker = doc.createTreeWalker( doc.body, NodeFilter.SHOW_ELEMENT )

    while ( walker.nextNode() ) {
        const el = walker.currentNode
        const tag = el.tagName.toLowerCase()

        if ( !allowed[tag] ) {
            // unwrap disallowed tag (replace with its text content)
            const text = doc.createTextNode( el.textContent )
            el.replaceWith( text )
            continue
        }

        // sanitize attributes
        const keep = allowed[tag]
        ;[ ...el.attributes ].forEach( attr => {
            if ( !keep.includes( attr.name.toLowerCase() ) ) {
                el.removeAttribute( attr.name )
            }
        })

        // strip javascript: from href/src
        if ( el.hasAttribute( 'href' ) ) {
            const href = el.getAttribute( 'href' ) || ''
            if ( href.trim().toLowerCase().startsWith( 'javascript:' ) ) {
                el.removeAttribute( 'href' )
            }
        }

        if ( el.hasAttribute( 'src' ) ) {
            const src = el.getAttribute( 'src' ) || ''
            if ( src.trim().toLowerCase().startsWith( 'javascript:' ) ) {
                el.removeAttribute( 'src' )
            }
        }
    }

    return doc.body.innerHTML
}
