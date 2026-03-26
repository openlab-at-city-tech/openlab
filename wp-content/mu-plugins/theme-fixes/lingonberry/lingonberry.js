(function($){
	$(document).ready(function(){
		$('#olgc-add-a-grade').change(function(){
			if ( $(this).is(':checked') ) {
				$('textarea#comment').removeAttr('required');
			} else {
				$('textarea#comment').attr('required', 'required');
			}
		});

		$('input#s').before('<label for="s" class="sr-only">Search terms</label>');

		$('.header-inner > a.logo').append('<span class="sr-only">Home</span>');

		$('.posts .post-bubbles a.format-bubble').each(function(k,v){
			var $bubble = $(this);
			$bubble.html('<span class="sr-only">' + $bubble.attr('title') + '</span>');
		});

		/* Add text to nav toggle button */
		const navToggle = document.querySelector('.nav-toggle');
		if (navToggle) {
			navToggle.innerHTML += '<span class="sr-only">Toggle navigation</span><span class="nav-toggle-icon"></span>';
		}

		/* If `.blog-title-wrapper a.logo` doesn't have any content, add the blog title as text */
		$('.blog-title-wrapper > a.logo').each(function() {
			var $link = $(this);
			if ($link.text().trim() === '') {
				var blogTitle = $('.blog-title a').text().trim();
				if ( blogTitle ) {
					$link.append( '<span class="sr-only">' + blogTitle + '</span>' );
				}
			}
		});

		/* Remove restrictions on text-scaling and zooming from the viewport meta tag */
		var viewportMeta = document.querySelector('meta[name="viewport"]');
		if (viewportMeta) {
			var content = viewportMeta.getAttribute('content');
			content = content.replace(/user-scalable=no,?\s*/g, '');
			content = content.replace(/maximum-scale=1,?\s*/g, '');
			content = content.replace(/minimum-scale=1,?\s*/g, '');
			viewportMeta.setAttribute('content', content);
		}
	});
}(jQuery));
