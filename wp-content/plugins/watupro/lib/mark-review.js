jQuery(function(){
	jQuery('.watupro-mark-review').click(function(){
		var id = jQuery(this).attr('id');
		var classes = jQuery(this).attr('class');
		classes = classes.split(' ');
		var oldImgURL = document.getElementById(id).src;
		
		// mark or unmark?
		var act = 'mark';		
		if(jQuery.inArray('marked', classes) !== -1) {			
			jQuery('#'+id).removeClass('marked');
			var newImgURL = oldImgURL.replace('unmark-review', 'mark-review');
         document.getElementById(id).src = newImgURL;
         act = 'unmark'; 
		}
		else {			
			jQuery('#'+id).addClass('marked');
			var newImgURL = oldImgURL.replace('mark-review', 'unmark-review');
			document.getElementById(id).src = newImgURL;
		}
		
		// get question ID and cnt
		var qID = qNum = 0;
		for(i=0; i < classes.length; i++) {
			if(classes[i].indexOf('question-id') >= 0) {
				var parts = classes[i].split('-');
				qID = parts[2];
			}
			
			if(classes[i].indexOf('question-cnt') >= 0) {
				var parts = classes[i].split('-');
				qNum = parts[2];
			}
		}
		
		// if there is a paginator, mark it
		if(jQuery('#WatuPROPagination' + qNum).length) {
			if(act == 'mark') jQuery('#WatuPROPagination' + qNum).addClass('marked');
			else jQuery('#WatuPROPagination' + qNum).removeClass('marked');
		}
		
		// now send ajax request
		url = watupro_i18n.ajax_url;
		data = {'action': 'watupro_ajax', 'do': 'mark_review', 'act' : act, 'question_id': qID, 
			"question_num": qNum, "exam_id" : WatuPRO.exam_id};
		jQuery.post(url, data, function(msg){
			// nothing for now
		});		
	});	
	
	// on loading we should check if there is paginator. If there is, add marked class where required
	if(jQuery('#quiz-' + WatuPRO.exam_id + ' .watupro-paginator-wrap').length) {
		jQuery('.watupro-mark-review').each(function(i, value){
			if(jQuery(this).hasClass('marked')) {
				id = i+1;
				jQuery('#WatuPROPagination' + id).addClass('marked');
			}
		});
	}
});

// is submitting allowed?
function watuproCheckPendingReview() {
	// find any questions flagged for review	
	var numMarked = jQuery('#quiz-' + WatuPRO.exam_id + ' .watupro-mark-review.marked').length;
	if(numMarked > 0) {
		var qNums = '';
		jQuery('#quiz-' + WatuPRO.exam_id + ' .watupro-mark-review.marked').each(function(index, value){
			var classes = jQuery(this).attr('class');
			classes = classes.split(' ');
			var qNum = 0;
			for(i=0; i < classes.length; i++) {
				if(classes[i].indexOf('question-cnt') >= 0) {
					var parts = classes[i].split('-');
					qNum = parts[2];
				}
			}
			
			if(index > 0 && index + 1 >= numMarked) qNums += ' & ';
			if(index > 0 && index + 1 < numMarked) qNums += ', ';
			qNums += '' + qNum;			
		});
		var msg =  watupro_i18n.questions_pending_review;
		msg = msg.replace('%s', qNums);
		if(!confirm(msg)) return false;
	}	
		
	return true;
}

// unmark given question from review. Used in live result for example
function watuproUnmarkReview(questionID) {
	if(!jQuery('#quiz-' + WatuPRO.exam_id + ' .watupro-mark-review.marked').length) return false;
	
	jQuery('#quiz-' + WatuPRO.exam_id + ' .watupro-mark-review.question-id-' + questionID).removeClass('marked');
	
	if(jQuery('#quiz-' + WatuPRO.exam_id + ' .watupro-paginator-wrap').length) {
		jQuery('#WatuPROPagination' + WatuPRO.current_question).removeClass('marked');
	}
}