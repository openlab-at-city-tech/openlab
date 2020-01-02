(function($) {
  'use strict';

  $(function() {

    $('.bp-toolkit-rating-link').on('click', function() {
      $(this).parent().text($(this).data("rated"));
    });

  });

  $(function() {

    if ($('.bptk-userlist')) {
      $('.bptk-userlist').select2({
        width: 'resolve'
      });
    }


  });

})(jQuery);