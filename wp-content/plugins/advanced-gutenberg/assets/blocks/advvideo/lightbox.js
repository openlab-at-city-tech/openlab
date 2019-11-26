jQuery(document).ready(function ( $ ) {
    $('.advgb-video-lightbox').colorbox({
        title: function () {
            return null;
        },
        maxWidth: '90%',
        maxHeight: '85%',
        fixed: true,
        className: 'advgb_lightbox',
        innerWidth: '80%',
        innerHeight: '80%',
        iframe: function () {
            return $(this).data('source') !== 'local';
        },
        href: function () {
            if ($(this).data('source') === 'local')
                return false;

            return $(this).data('video');
        },
        html: function () {
            if ($(this).data('source') !== 'local')
                return false;
            return '<video src="'+ $(this).data('video') +'" controls style="height: 99%; display: block; margin: auto"></video>'
        }
    })
});