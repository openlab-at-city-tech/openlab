(function($) { 
	$(document).ready(function() {

	jQuery('#wds-accordion-slider').easyAccordion({
			autoStart: true,
			slideInterval: 6000,
			slideNum:false
	});
	
	equal_row_height();

	});//end document.ready
	
	/*this is for the homepage group list, so that cells in each row all have the same height 
	- there is a possiblity of doing this template-side, but requires extensive restructuring of the group list function*/
	function equal_row_height()
	{
	/*first we get the number of rows by finding the column with the greatest number of rows*/
	var $row_num = 0;
	$('.activity-list').each(function(){
									 
									  $row_check = $(this).find('.row').length;
									  
									  if ($row_check > $row_num)
									  {
										  $row_num = $row_check;
									  }
									  
									  });
	
	//build a loop to iterate through each row
	$i = 1;
	  while ($i <= $row_num)
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
	$('.activity-list').css('visibility','visible');
		
	}
	
})(jQuery);