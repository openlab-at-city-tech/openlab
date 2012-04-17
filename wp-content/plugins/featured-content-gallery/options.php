<?php
$location = $options_page; // Form Action URI
?>

<div class="wrap">
	<h2>Featured Content Gallery Configuration</h2>
	<p>Use the options below to select the content for your gallery, to style your gallery, and to configure the gallery slides and transitions.<br /> 
    Visit the <a href="http://www.featuredcontentgallery.com">Featured Content Gallery Plugin</a> website for more information.</p>
        <form method="post" action="options.php"><?php wp_nonce_field('update-options'); ?>
		<fieldset name="general_options" class="options">
        <div style="padding-top: 15px"></div>
        <u><strong>Featured Content Gallery Code</strong></u> - If not already included, add this code to your template file where you want the Featured Content Gallery to be displayed:<br />
        <blockquote>&lt;&#63;php include &#40;ABSPATH &#46; '/wp-content/plugins/featured-content-gallery/gallery.php'&#41;&#59; &#63;&#62;</blockquote>
        <div style="padding-top: 10px"></div>
        <?php  $galleryoldway = (get_option('gallery-way') == 'old' || get_option('gallery-way') == '') ? "checked" : ""; 
        		   $gallerynewway = get_option('gallery-way') == 'new' ? "checked" : ""; 
        ?>
        <u><strong>Featured Content Selection</strong></u> - Select either a blog category or individual post/page IDs for your featured content:<br />
        <div style="padding-top: 5px"></div>
        <table width="690" border="0" cellpadding="0" cellspacing="7">
        <tr>
    	<td width="330">
        <input type="radio" name="gallery-way" id="gallery-way" size="25" value="old"  <?php print $galleryoldway; ?>>
        			Select here to use category selection
        </td>
  		<td width="360">
        <input type="radio" name="gallery-way" id="gallery-way" size="25" value="new"  <?php print $gallerynewway; ?>>
        			Select here to use individual post or page IDs
        </td>
		</tr>
  	    <tr>
    	<td>
        Category Name:<br />
                    <input name="gallery-category" id="gallery-category" size="25" value="<?php echo get_option('gallery-category'); ?>"></input> 
        </td>
    	<td>
        Post or Page IDs <span class="style1">(comma separated no spaces)</span>:<br />
                    <input name="gallery-items-pages" id="gallery-items-pages" size="25" value="<?php echo get_option('gallery-items-pages'); ?>"></input>
        </td>
  	    </tr>
  	    <tr>
        <td>
        Number of Items to Display:<br />
        			<input name="gallery-items" id="gallery-items" size="25" value="<?php echo get_option('gallery-items'); ?>"></input> 
        </td>
        <td>
        <?php $checked3 = get_option('gallery-randomize-pages') ? "checked" : ""; ?>
                    <input type="checkbox" name="gallery-randomize-pages" id="gallery-randomize-pages" <?php print $checked3 ?>> 
        Check here to randomize post/page ID display
        </td>
  	    </tr>
		</table>
        <div style="padding-top: 10px"></div>
        <u><strong>Gallery Style</strong></u> - Choose your gallery size and colors:<br />
        <div style="padding-top: 10px"></div>
        <table width="690" border="0" cellpadding="0" cellspacing="7">
        <tr>
    	<td width="330">
        Gallery Width in Pixels:<br />
        <input name="gallery-width" id="gallery-width" size="25" value="<?php echo get_option('gallery-width'); ?>"></input>
        </td>
  		<td width="360">
        Gallery Border Color (#hex or color name):<br />
        <input name="gallery-border-color" id="gallery-border-color" size="25" value="<?php echo get_option('gallery-border-color'); ?>"></input> 
        </td>
		</tr>
  	    <tr>
    	<td>
        Gallery Height in Pixels:<br />
        <input name="gallery-height" id="gallery-height" size="25" value="<?php echo get_option('gallery-height'); ?>"></input>
        </td>
    	<td>
        Gallery Background Color (#hex or color name):<br />
        <input name="gallery-bg-color" id="gallery-bg-color" size="25" value="<?php echo get_option('gallery-bg-color'); ?>"></input>   
        </td>
  	    </tr>
  	    <tr>
        <td>
        Text Overlay Height in Pixels:<br />
        <input name="gallery-info" id="gallery-info" size="25" value="<?php echo get_option('gallery-info'); ?>"></input> 
        </td>
        <td>
        Gallery Text Color (#hex or color name):<br />
        <input name="gallery-text-color" id="gallery-text-color" size="25" value="<?php echo get_option('gallery-text-color'); ?>"></input> 
        </td>
  	    </tr>
		</table>
        <div style="padding-top: 10px"></div>
        <u><strong>Slide Transition Times and Other Options</strong></u> - Choose your slide and fade duration, carousel button name and text overlay word quantity:<br />
        <div style="padding-top: 10px"></div>
        <table width="690" border="0" cellpadding="0" cellspacing="10">
        <tr>
    	<td width="330">
        Slide Display Duration (milliseconds):<br />
        <input name="gallery-delay" id="gallery-delay" size="25" value="<?php echo get_option('gallery-delay'); ?>"></input><br />
        (Default: 9000 milliseconds / 9 seconds)
        </td>
  		<td width="360">
        Carousel Button Name:<br />
        <input name="gallery-fcg-button" id="gallery-fcg-button" size="25" value="<?php echo get_option('gallery-fcg-button'); ?>"></input><br />
        (Default: "Featured Content")
        </td>
		</tr>
  	    <tr>
    	<td>
        Slide Fade Duration (milliseconds):<br />
        <input name="gallery-fade-duration" id="gallery-fade-duration" size="25" value="<?php echo get_option('gallery-fade-duration'); ?>"></input><br />
        (Default: 500 milliseconds / .5 seconds)
        </td>
    	<td>
        Number of Words in Text Overlay:<br />
        <input name="gallery-rss-word-quantity" id="gallery-rss-word-quantity" size="25" value="<?php echo get_option('gallery-rss-word-quantity'); ?>"></input><br />
        (Default: 100 words)
        </td>
  	    </tr>
		</table>
        <div style="padding-top: 10px"></div>
        <u><strong>Slide Transition Type</strong></u> - Choose your slide transition effect:<br />
        <div style="padding-top: 10px"></div>
        <?php  $galleryfade = (get_option('gallery-default-transaction') == 'fade' || get_option('gallery-default-transaction') == '') ? "checked" : ""; 
        	   $galleryfadeslideleft = get_option('gallery-default-transaction') == 'fadeslideleft' ? "checked" : "";
        	   $gallerycontinuoushorizontal = get_option('gallery-default-transaction') == 'continuoushorizontal' ? "checked" : "";  
        	   $gallerycontinuousvertical = get_option('gallery-default-transaction') == 'continuousvertical' ? "checked" : ""; 
        ?>
        <table width="500" border="0" cellpadding="0" cellspacing="10">
        <tr>
    	<td width="250">
        <input type="radio" name="gallery-default-transaction" id="gallery-default-transaction" size="25" value="fade"  <?php print $galleryfade; ?>> Simple Fade
        </td>
  		<td width="250">
		<input type="radio" name="gallery-default-transaction" id="gallery-default-transaction" size="25" value="fadeslideleft"  <?php print $galleryfadeslideleft; ?>> Slide Left with Fade
        </td>
		</tr>
  	    <tr>
    	<td>
		<input type="radio" name="gallery-default-transaction" id="gallery-default-transaction" size="25" value="continuoushorizontal"  <?php print $gallerycontinuoushorizontal; ?>> Continuous Horizontal
        </td>
    	<td>
		<input type="radio" name="gallery-default-transaction" id="gallery-default-transaction" size="25" value="continuousvertical"  <?php print $gallerycontinuousvertical; ?>> Continuous Vertical
        </td>
  	    </tr>
		</table>
        <div style="padding-top: 10px"></div>
        <u><strong>Required Custom Fields</strong></u>
        <div style="padding-top: 5px"></div>
        For each post or page you want to display in your gallery, regardless of your selections above, you <strong>must</strong> include a custom field. For the main gallery image, use the key <strong>articleimg</strong> and the full url of your image in the value. You <strong>must</strong> have at least two (2) items featured for the gallery to work.
        <div style="padding-top: 10px"></div>
        <u><strong>Advanced Custom Fields</strong></u>
        <div style="padding-top: 5px"></div>
		<?php $checked1 = get_option('gallery-use-featured-content') ? "checked" : ""; ?>
        <input type="checkbox" name="gallery-use-featured-content" id="gallery-use-featured-content" <?php print $checked1 ?>> 
        Check here if you want to use custom text under the post/page title.<br />
        Key: <strong>featuredtext</strong> - Insert custom text in the value. HTML is allowed.
        <div style="padding-top: 10px"></div>
        <?php $checked2 = get_option('gallery-use-thumb-image') ? "checked" : ""; ?>
        <input type="checkbox" name="gallery-use-thumb-image" id="gallery-use-thumb-image" <?php print $checked2 ?>> 
        Check here if you want to use a custom thumbnail image in your gallery.<br />
        Key: <strong>thumbnailimg</strong> - Insert the url of the image in the value.
        <div style="padding-top: 5px"></div>
		You can also add alt text to your gallery images with a custom field.<br />
        Key: <strong>alttext</strong> - Insert the alt text in the value.
        <div style="padding-top: 15px"></div>
        For more information, please visit the <a href="http://www.featuredcontentgallery.com/install-setup">Featured Content Gallery Install & Setup</a> page.
                        
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="gallery-width,gallery-height,gallery-info,gallery-items,gallery-border-color,gallery-bg-color,gallery-text-color,gallery-use-featured-content,gallery-use-thumb-image,gallery-way,gallery-items-pages,gallery-category,gallery-fcg-button,gallery-fade-duration,gallery-delay,gallery-randomize-pages,gallery-rss-word-quantity,gallery-default-transaction" />

		</fieldset>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Update Options') ?>" /></p>
        <p><em>Featured Content Gallery WordPress Plugin v3.2.0 by <a href="http://www.ieplexus.com">iePlexus</a></em></p>
	</form>      
</div>