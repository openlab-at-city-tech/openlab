String.prototype.escapeTag = function() {
  var tagsToReplace = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;'
  };

  return this.replace(/[&<>]/g, function(tag) {
    return tagsToReplace[tag] || tag;
  });
};

jQuery(document).ready(function(){
  // Escape the setting fields
  jQuery(document).on("submit", "#wtilp_admin_settings", function(e){
    jQuery(this).find('input[type=text]').each(function() {
      jQuery(this).val(jQuery(this).val().escapeTag());
    });
  });
});