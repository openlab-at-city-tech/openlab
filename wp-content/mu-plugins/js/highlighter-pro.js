(function($){
	$(document).ready(function(){
		// Add alt text to highlighter stats toggle img.
		$('.highlighter-stats-toggle img').attr('alt', 'Open Highlighter Stats');

		// Add form label to comment textarea.
		$('#highlighter-comment-textarea').append( '<label for="highlighter-comment-textarea" class="screen-reader-only">Comment Text</label>' );
	});
}(jQuery));
