(function ($) {
    $(document).ready(function () {
        var _x = document.querySelector('a.toplevel_page_oplb_gradebook');

        if (typeof _x !== 'undefined' && _x) {
            _x.setAttribute('href', _x.getAttribute('href') + '#courses');
        }

        var _x = document.querySelector('[href="admin.php?page=oplb_gradebook"]:not(.toplevel_page_oplb_gradebook)');

        if (typeof _x !== 'undefined' && _x) {
            _x.setAttribute('href', _x.getAttribute('href') + '#courses');
        }

        var _x = document.querySelector('[href$="oplb_gradebook_settings"]');

        if (typeof _x !== 'undefined' && _x) {
            _x.setAttribute('href', _x.getAttribute('href') + '#settings');
        }
        
    });
})(jQuery);