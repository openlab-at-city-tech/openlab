import $ from 'jquery';

export var opt = {};

/**
 * Start Ladda on given button.
 */
export function laddaStart(elem) {
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
export function scrollTo($elem, formId) {
    if (opt[formId].scroll) {
        if ($elem.length) {
            var elemTop = $elem.offset().top;
            var scrollTop = $(window).scrollTop();
            if (elemTop < $(window).scrollTop() || elemTop > scrollTop + window.innerHeight) {
                $('html,body').animate({scrollTop: (elemTop - 50)}, 500);
            }
        }
    } else {
        opt[formId].scroll = true;
    }
}

export function requestCancellable() {
    const request = {
        xhr: null,
        booklyAjax: () => {},
        cancel: () => {}
    };
    request.booklyAjax = (options) => {
        return new Promise((resolve, reject) => {
            request.cancel = () => {
                if (request.xhr != null) {
                    request.xhr.abort();
                    request.xhr = null;
                }
            };
            request.xhr = ajax(options, resolve, reject);
        });
    }

    return request
}

export function booklyAjax(options) {
    return new Promise((resolve, reject) => {
        ajax(options, resolve, reject);
    });
}

export class Format {
    #w;

    constructor(w) {
        this.#w = w;
    }

    price(amount) {
        let result = this.#w.format_price.format;
        amount = parseFloat(amount);
        result = result.replace('{sign}', amount < 0 ? '-' : '');
        result = result.replace(
            '{price}',
            this._formatNumber(
                Math.abs(amount),
                this.#w.format_price.decimals,
                this.#w.format_price.decimal_separator,
                this.#w.format_price.thousands_separator
            )
        );

        return result;
    }

    _formatNumber(n, c, d, t) {
        n = Math.abs(Number(n) || 0).toFixed(c);
        c = isNaN(c = Math.abs(c)) ? 2 : c;
        d = d === undefined ? '.' : d;
        t = t === undefined ? ',' : t;

        let s = n < 0 ? '-' : '',
            i = String(parseInt(n)),
            j = i.length > 3 ? i.length % 3 : 0;

        return s +
            (j ? i.substr(0, j) + t : '') +
            i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) +
            (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '')
            ;
    }
}

function ajax(options, resolve, reject) {
    options.data.csrf_token = BooklyL10n.csrf_token;
    return $.ajax(
        jQuery.extend({
            url: BooklyL10n.ajaxurl,
            dataType: 'json',
            xhrFields: {withCredentials: true},
            crossDomain: 'withCredentials' in new XMLHttpRequest(),
            beforeSend(jqXHR, settings) {}
        }, options)
    ).always(response => {
        if (processSessionSaveResponse(response)) {
            if (response.success) {
                resolve(response);
            } else {
                reject(response);
            }
        }
    })
}

function processSessionSaveResponse(response) {
    if (!response.success && response?.error === 'session_error') {
        Ladda.stopAll();
        setTimeout(function() {
            if (confirm(BooklyL10n.sessionHasExpired)) {
                location.reload();
            }
        }, 100);
        return false;
    }

    return true;
}