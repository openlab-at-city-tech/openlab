// This file will also be used for other question-specific scripts to avoid loading too many individual script files

/***** Flashcards *******/
function WatuPROFlipCard(ansID) {	
	// is it currently checked
	var isChecked = jQuery('#answer-id-' + ansID).is(":checked");

	if(isChecked) {
		jQuery('#watuproFlashcard-' + ansID).flip(false);
		jQuery('#answer-id-' + ansID).prop('checked', false);
	}
	else {
		jQuery('#watuproFlashcard-' + ansID).flip(true);
		jQuery('#answer-id-' + ansID).prop('checked', true);
	}
}

jQuery(function(){
	jQuery(".watupro_flashcard:not(.processed)").flip({trigger: 'manual'});	
	
	// go through each once on load to check if checked
	jQuery(".watupro_flashcard:not(.processed)").each(function(){		
	
		var id = jQuery(this).attr('id');
		// extract answer ID to call the flip card on it
		parts = id.split('-');
		var ansID = parts[1];
		var isChecked = jQuery('#answer-id-' + ansID).is(":checked");		
		if(isChecked) jQuery('#watuproFlashcard-' + ansID).flip(true);
		
		return true;
	});
}); 

/***** Open-end questions *******/
jQuery(function() {
  jQuery(".watupro-word-count").on('keyup', function() {
    var words = this.value.match(/\S+/g).length;
    
    // figure out the number of words required in this question. We have to extract the class names and find it there
    // the class name will be like this: watuprowordcount-X-Y where X is the number of words and Y is the question ID
    var classList = jQuery(this).attr('class').split(/\s+/);
    var wordsCount = 0;
    var qID = 0;
    for (var i = 0; i < classList.length; i++) {
		    if (classList[i].indexOf('watuprowordcount') != -1) {
		        var parts = classList[i].split('-');
		        wordsCount = parts[1];
		        qID = parts[2];
		    }
	 } // end classes loop

    if (words > wordsCount) {
      // Split the string on first 200 words and rejoin on spaces
      var trimmed = jQuery(this).val().split(/\s+/, wordsCount).join(" ");
      // Add a space at the end to make sure more typing creates new words
      jQuery(this).val(trimmed + " ");
    }
    else {
      jQuery('#words_count_' + qID).text(words);
    }
  });
}); 