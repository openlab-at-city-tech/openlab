jQuery(document).ready(function ($) {

	jQuery('#wds-accordion-slider').easyAccordion({
			autoStart: true,
			slideInterval: 6000,
			slideNum:false
	});
	
	/*this is for the homepage group list, so that cells in each row all have the same height 
	- there is a possiblity of doing this template-side, but requires extensive restructuring of the group list function*/
	/*first we get the number of rows - since we use a function to generate each column, 
	if we just measure the first column, we'll know the max number of rows for the other columns*/
	$rows = $('.course-list .box-1').length;
	
	//build a loop to iterate through each row
	$i = 1;
	while ($i <= 4)
	{
		//check each cell in the row - find the one with the greatest height
		var $greatest_height = 0;
		$('.row-'+$i).each(function(){
								   
								   $cell_height = $(this).height();
								   
								   if ($cell_height > $greatest_height)
								   {
									   $greatest_height = $cell_height;
								   }
								   
								   });
		
		//now apply that height to the other cells in the row
		$('.row-'+$i).css('height',$greatest_height + 'px');
		
		//iterate to next row
		$i++;
	}
	//there is an inline script that hides the lists from the user on load (just so the adjusment isn't jarring) - this will show the lists
	$('#home-group-list-wrapper').css('visibility','visible');

},(jQuery));