/**
 * Setmore Plus script
 */
jQuery(document).ready(function ($) {

  if (typeof( setmoreplus ) !== 'undefined') {

    var isPx = ( 'px' === setmoreplus.width.charAt(setmoreplus.width.length - 2) );

    // Adjust popup on mobile devices.
    var vWidth = document.documentElement.clientWidth;
    var vHeight = document.documentElement.clientHeight;
    if (vWidth < setmoreplus.breakpoint || ( isPx && vWidth < setmoreplus.width )) {
      var mobileSettings = {
        width: vWidth,
        height: vHeight,
        transition: 'none'
      }
      setmoreplus = $.extend(setmoreplus, mobileSettings);
    }

    // Specific elements.
    $(".widget .setmore-iframe")
      .add("a.setmore-iframe")
      .add("button.setmore-iframe")
      .add("li.setmore-iframe > a")
      .add('a[href$="#setmoreplus"]')
      .colorbox(setmoreplus);

    // For themes that already have an Appointment button
    // and you can only enter a link, use this page anchor.
    $('a[href$="#setmoreplus"]').click(function (e) {
      e.preventDefault();
    });
  }

});
