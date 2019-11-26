<?php
/**
* dkpdfg-cover.php
* This template is used to display the cover page
*
* Do not edit this template directly,
* copy this template and paste in your theme inside a directory named dkpdfg
*/
?>

<?php

	// get dkpdf generator settings
	$cover_title = get_option( 'dkpdfg_cover_title', '' );
	$cover_description = get_option( 'dkpdfg_cover_description', '' );
	$cover_text_align = get_option( 'dkpdfg_cover_text_align', 'left' );
	$cover_text_margin_top = get_option( 'dkpdfg_cover_text_margin_top', '100' );
	$cover_text_color = get_option( 'dkpdfg_cover_text_color', '#000' );
	$cover_bg_color = get_option( 'dkpdfg_cover_bg_color', '#FFF' );

?>

<html>
    <head>

    	<link type="text/css" rel="stylesheet" href="<?php echo get_bloginfo( 'stylesheet_url' ); ?>" media="all" />

      	<style type="text/css">

      		body {
      			background:<?php echo $cover_bg_color;?>;
      		}

      		h1 {font-size:180%;}
      		h2 {font-size:120%;}
      		h3 {font-size:110%;}
      		h4 {font-size:100%;}
      		h5 {font-size:90%;}
      		h6 {font-size:80%;}

		</style>

   	</head>

    <body>

		<div style="padding-top:<?php echo $cover_text_margin_top;?>;color:<?php echo $cover_text_color;?>;text-align:<?php echo $cover_text_align;?>;">

			<h1><?php echo $cover_title;?></h1>
			<p><?php echo $cover_description;?></p>

		</div>

    </body>

</html>
