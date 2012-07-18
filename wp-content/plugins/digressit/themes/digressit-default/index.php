<?php

/*
because Multi-sites have a completely different layout on the aggregate home page
than the introduction pages to the book/course, we've changed the standard "home" and "index"
functionality of WP.

The way it works is this way: If we are using MU and frontpage.php exists, then we use frontpage 
for the MU home page. Individual sites load up introduction.

The problem with using the standard home.php is that home.php takes priority of index.php, but we technically only 
want home.php to load as the MU homepage.

We also added two new functions to determine if we are on the MU homepage. and is_frontpage();

*/



if(is_frontpage()){
	include(get_template_directory(). '/frontpage.php');
}
else{
	include(get_template_directory(). '/mainpage.php');	
}


?> 
