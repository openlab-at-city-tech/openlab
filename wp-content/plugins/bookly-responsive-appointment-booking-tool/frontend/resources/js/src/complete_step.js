import $ from 'jquery';
import {laddaStart, opt, scrollTo, booklyAjax} from './shared.js';
import stepService from "./service_step";

/**
 * Complete step.
 */
export default function stepComplete(params) {
    let data = $.extend({action: 'bookly_render_complete',}, params),
        $container = opt[params.form_id].$container;
    booklyAjax({
        data
    }).then(response => {
        if (response.final_step_url && !data.error) {
            document.location.href = response.final_step_url;
        } else {
            $container.html(response.html);
            let $qc = $('.bookly-js-qr', $container);
            $('img', $qc)
                .on('error', function() {$qc.remove()})
                .on('load', function() {$qc.removeClass('bookly-loading')});
            scrollTo($container, params.form_id);
            $('.bookly-js-start-over', $container).on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                laddaStart(this);
                stepService({form_id: params.form_id, reset_form: true, new_chain: true});
            });
            $('.bookly-js-download-ics', $container).on('click', function(e) {
                let ladda = laddaStart(this);
                window.location = BooklyL10n.ajaxurl + (BooklyL10n.ajaxurl.indexOf('?') > 0 ? '&' : '?') + 'action=bookly_download_ics&form_id=' + params.form_id + '&csrf_token=' + BooklyL10n.csrf_token;
                setTimeout(() => ladda.stop(), 1500);
            });
            $('.bookly-js-download-invoice', $container).on('click', function(e) {
                let ladda = laddaStart(this);
                window.location = BooklyL10n.ajaxurl + (BooklyL10n.ajaxurl.indexOf('?') > 0 ? '&' : '?') + 'action=bookly_invoices_download_invoice&form_id=' + params.form_id + '&csrf_token=' + BooklyL10n.csrf_token;
                setTimeout(() => ladda.stop(), 1500);
            });
        }
    });
}