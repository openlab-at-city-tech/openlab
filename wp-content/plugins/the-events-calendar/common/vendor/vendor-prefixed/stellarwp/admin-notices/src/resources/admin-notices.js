(function ($, dispatch, document) {
    // Set up the package functions using the namespace provided by the script tag.
    const currentScript = typeof document.currentScript !== 'undefined' ? document.currentScript : document.scripts[document.scripts.length - 1];
    const namespace = currentScript.getAttribute('data-stellarwp-namespace');

    if (!namespace) {
        console.error('The stellarwp/admin-notices library failed to load because the namespace attribute is missing.');
        return;
    }

    window.stellarwp = window.stellarwp || {};
    window.stellarwp.adminNotices = window.stellarwp.adminNotices || {};
    window.stellarwp.adminNotices[namespace] = {
        /**
         * Dismisses a notice with the given ID.
         *
         * @since 1.1.0
         *
         * @param {string} noticeId
         */
        dismissNotice: function (noticeId) {
            const now = Math.floor(Date.now() / 1000);
            dispatch('core/preferences').set(`stellarwp/admin-notices/${namespace}`, noticeId, now);
        },
    };

    // Begin notice dismissal code
    const noticeIdAttribute = `data-stellarwp-${namespace}-notice-id`;
    const $notices = $(`[${noticeIdAttribute}]`);

    $notices.on('click', '.notice-dismiss', function (event) {
        const noticeId = $(this).closest(`[${noticeIdAttribute}]`).data(`stellarwp-${namespace}-notice-id`);

        window.stellarwp.adminNotices[namespace].dismissNotice(noticeId);
    });
})(window.jQuery, window.wp.data.dispatch, document);
