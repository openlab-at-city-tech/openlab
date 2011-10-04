function navigationArrow(path){

  jQuery("#nav ul li ul").addClass("push"); // This is line 3
  jQuery("#nav ul li.drop").addClass("enhanced");
  jQuery("#nav ul li.drop").removeClass("drop");
  jQuery("#nav ul li.enhanced span").after(' <img src="' + path + '" />');
  jQuery("#nav ul li.enhanced img").wrap('<a class="arrow rest"></a>');

  jQuery("#nav ul li a.arrow").hover(function(){
    jQuery(this).removeClass("rest").addClass("hover");
  }, function(){
    jQuery(this).removeClass("hover").addClass("rest");
  });
  
  jQuery("#nav ul li a.arrow").click(function(){
    if (jQuery(this).hasClass("hover") == true) {
      jQuery("#nav ul li a.open").removeClass("open").addClass("rest");
      jQuery("#nav ul li ul").hide();
      jQuery(this).removeClass("hover").addClass("open");
      jQuery(this).parent().find("ul").fadeIn();
    } else {
      if (jQuery(this).hasClass("open") == true) {
        jQuery(this).removeClass("open").addClass("hover");
        jQuery(this).parent().find("ul").hide();
      }
    }
  });

  jQuery(document).click(function(event){
    var target = jQuery(event.target);
    if (target.parents("#nav").length == 0) {
      jQuery("#nav ul li a.arrow").removeClass("open").addClass("rest");
      jQuery("#nav ul li ul").hide();
    }
	});

}