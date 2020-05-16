<?php
/**
* imagemagick.inc.php
*
* @author 		Frederic De Ranter
* @copyright	Copyright 2008
* @version 		0.4 (PHP4)
* @based 		on thumbnail.inc.php by Ian Selby (gen-x-design.com)
* @since		NextGEN V1.0.0
*
*/
/**
* PHP class for dynamically resizing, cropping, and rotating images for thumbnail purposes and either displaying them on-the-fly or saving them.
* with ImageMagick
*/

class ngg_Thumbnail {
/**
* Error message to display, if any
* @var string
*/
var $errmsg;
/**
* Whether or not there is an error
* @var boolean
*/
var $error;
/**
* File name and path of the image file
* @var string
*/
var $fileName;
/**
* Image meta data if any is available (jpeg/tiff) via the exif library
* @var array
*/
var $imageMeta;
/**
* Current dimensions of working image
* @var array
*/
var $currentDimensions;
/**
* New dimensions of working image
* @var array
*/
var $newDimensions;
/**
* Percentage to resize image b
* @var int
* @access private
*/
var $percent;
/**
* Maximum width of image during resize
* @var int
* @access private
*/
var $maxWidth;
/**
* Maximum height of image during resize
* @var int
* @access private
*/
var $maxHeight;
/**
* Image for Watermark
* @var string
*/
var $watermarkImgPath;
/**
* Text for Watermark
* @var string
*/
var $watermarkText;
/**
* Path to ImageMagick convert
* @var string
*/
var $imageMagickDir;
/**
* String to execute ImageMagick convert.
* @var string
*/
var $imageMagickExec;
/**
* String to execute ImageMagick composite.
* @var string
*/
var $imageMagickComp;
/**
* String to execute ImageMagick (before the filename).
* @var string
*/
var $imageMagickBefore;

  /*
   * in: filename, error
   * out: nothing 
   * init of class: init of variables, detect needed memory (gd), image format (gd), detect image size (GetImageSize is general PHP, not GD), Image Meta?
   */
	function __construct($fileName, $no_ErrorImage = false)
	{
		//initialize variables
      	$this->errmsg				= '';
      	$this->error				= false;
      	$this->currentDimensions	= array();
      	$this->newDimensions		= array();
      	$this->fileName				= $fileName;
      	$this->imageMeta			= array();
      	$this->percent				= 100;
      	$this->maxWidth				= 0;
      	$this->maxHeight			= 0;
      	$this->watermarkImgPath		= '';
      	$this->watermarkText		= '';
		$this->imageMagickExec		= '';
      	$this->imageMagickComp		= '';
      	$this->imageMagickBefore	= '';

		//make sure ImageMagick is installed
		$this->checkVersion();
      
		//check to see if file exists
		if(!file_exists($this->fileName)) {
			$this->errmsg = 'File not found';
			$this->error = true;
		}
		//check to see if file is readable
		elseif(!is_readable($this->fileName)) {
			$this->errmsg = 'File is not readable';
			$this->error = true;
		}

		if($this->error == false) { 
	    	$size = GetImageSize($this->fileName);
      		$this->currentDimensions = array('width'=>$size[0],'height'=>$size[1]);
	  	}
	    
		if($this->error == true) {
			// for SinglePic send the error message out
	    	if(!$no_ErrorImage) 
	    		echo $this->errmsg;
	    	return;
	    }
	}

	function checkVersion() {
		
		// very often exec()or passthru() is disabled. No chance for Imagick
		if ( ini_get('disable_functions') ) {
			$not_allowed = ini_get('disable_functions');
			if ( stristr($not_allowed, 'exec') || stristr($not_allowed, 'passthru') ) {
				$this->errmsg = 'exec() or passthru() is not allowed. Could not execute Imagick';
				$this->error = true;
				return false;
			}
		}
		
		// get the path to imageMagick
		$ngg_options = get_option('ngg_options');
		$this->imageMagickDir = trim( $ngg_options['imageMagickDir']);
		$this->imageMagickDir = str_replace( "\\", "/", $this->imageMagickDir );

		// Try to get the ImageMagick version		
		$magickv = $this->execute('convert', '-version');
		
		if ( empty($magickv) ) {
			$this->errmsg = 'Could not execute ImageMagick. Check path ';
			$this->error = true;
			return false;
		}
		
		// We need as least version 6 or higher	
		$helper = preg_match('/Version: ImageMagick ([0-9])/', $magickv[0], $magickversion);
		if ( !$magickversion[0] > '5' ) {
			$this->errmsg = 'Require ImageMagick Version 6 or higher';
			$this->error = true;
			return false;
		}

      	return true;
	}
	

	/**
     * Execute ImageMagick/GraphicsMagick commands
     *
     * @param string $cmd an ImageMagick command (eg. "convert")
     * @param string $args the arguments which should be passed
     * @param bool $passthru (optional) Output the result to the webserver instead
	 * @return array|string
     */
	function execute( $cmd, $args, $passthru = false) {
		
		// in error case we do not continue
		if($this->error == true)
			return '';

		//if path is not empty
		if ($this->imageMagickDir != '') {
		// the path must have a slash at the end
			if ( $this->imageMagickDir{strlen($this->imageMagickDir)-1} != '/')
		    	$this->imageMagickDir .= '/';
		}
	
		//$args = escapeshellarg($args);
		//var_dump( escapeshellcmd ( "{$this->imageMagickDir}/{$cmd} {$args}" ) ); return;
		//$this->errmsg = escapeshellcmd( "{$this->imageMagickDir}{$cmd} {$args}" );
		
		if ( !$passthru ) {
			exec( "{$this->imageMagickDir}{$cmd} {$args}", $result );
			//var_dump( "{$this->imageMagickDir}/{$cmd} {$args}" );
			return $result;
			
		}
		//var_dump( escapeshellcmd ( "{$this->imageMagickDir}/{$cmd} {$args}" ) ); return;

		// for single pic we need the direct output
		header('Content-type: image/jpeg');
		$this->errmsg = "{$this->imageMagickDir}{$cmd} {$args}";
		return passthru( "{$this->imageMagickDir}{$cmd} {$args}" );
	}


    /**
     * Must be called to free up allocated memory after all manipulations are done
     */
    function destruct() {
     	//not needed for ImageMagick
		return;
    }
    
    /**
     * Returns the current width of the image
     * @return int
     */
    function getCurrentWidth() {
        return $this->currentDimensions['width'];
    }

    /**
     * Returns the current height of the image
     * @return int
     */
    function getCurrentHeight() {
        return $this->currentDimensions['height'];
    }

    /**
     * Calculates new image width
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcWidth($width, $height) {
        $newWp = (100 * $this->maxWidth) / $width;
        $newHeight = ($height * $newWp) / 100;
        return array('newWidth'=>intval($this->maxWidth), 'newHeight'=>intval($newHeight));
    }

    /**
     * Calculates new image height
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcHeight($width, $height) {
        $newHp = (100 * $this->maxHeight) / $height;
        $newWidth = ($width * $newHp) / 100;
        return array('newWidth'=>intval($newWidth), 'newHeight'=>intval($this->maxHeight));
    }

    /**
     * Calculates new image size based on percentage
     * @param int $width
     * @param int $height
     * @return array
     */
    function calcPercent($width, $height) {
        $newWidth = ($width * $this->percent) / 100;
        $newHeight = ($height * $this->percent) / 100;
        return array('newWidth'=>intval($newWidth), 'newHeight'=>intval($newHeight));
    }

    /**
     * Calculates new image size based on width and height, while constraining to maxWidth and maxHeight
     * @param int $width
     * @param int $height
     */
    function calcImageSize($width,$height) {
        $newSize = array('newWidth'=>$width,'newHeight'=>$height);

        if($this->maxWidth > 0) {

            $newSize = $this->calcWidth($width,$height);

            if($this->maxHeight > 0 && $newSize['newHeight'] > $this->maxHeight) {
                $newSize = $this->calcHeight($newSize['newWidth'],$newSize['newHeight']);
            }

            //$this->newDimensions = $newSize;
        }

        if($this->maxHeight > 0) {
            $newSize = $this->calcHeight($width,$height);

            if($this->maxWidth > 0 && $newSize['newWidth'] > $this->maxWidth) {
                $newSize = $this->calcWidth($newSize['newWidth'],$newSize['newHeight']);
            }

            //$this->newDimensions = $newSize;
        }

        $this->newDimensions = $newSize;
    }

    /**
     * Calculates new image size based percentage
     * @param int $width
     * @param int $height
     */
    function calcImageSizePercent($width,$height) {
        if($this->percent > 0) {
            $this->newDimensions = $this->calcPercent($width,$height);
        }
    }

    /**
     * Resizes image to maxWidth x maxHeight
     *
     * @param int $maxWidth
     * @param int $maxHeight
     */
	  
	function resize($maxWidth = 0, $maxHeight = 0, $resampleMode = 3) {
		$this->maxWidth = $maxWidth;
    	$this->maxHeight = $maxHeight;

    	$this->calcImageSize($this->currentDimensions['width'],$this->currentDimensions['height']);

		//string to resize the picture to $this->newDimensions['newWidth'],$this->newDimensions['newHeight']
		//should result in: -thumbnail $this->newDimensions['newWidth']x$this->newDimensions['newHeight']
		if($maxWidth=='0')
			$this->imageMagickExec .= " -resize x".$maxHeight;
		elseif($maxHeight=='0')
			$this->imageMagickExec .= " -resize ".$maxWidth."x";
		elseif($maxHeight!='0' && $maxWidth!='0')
			$this->imageMagickExec .= " -resize ".$maxWidth."x".$maxHeight;
			
		// next calculations should be done with the 'new' dimensions
		$this->currentDimensions['width'] = $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] = $this->newDimensions['newHeight'];
		
	}

	/**
	 * Flip an image.
	 *
	 * @param bool $horz flip the image in horizontal mode
	 * @param bool $vert flip the image in vertical mode
	 */
	function flipImage( $horz = false, $vert = false ) {
		
		//TODO: need to be added

	}
	
	/**
     * Rotates image either 90 degrees clockwise or counter-clockwise
     *
     * @param string $dir
     */
	function rotateImage($dir = 'CW')
    {
		$angle = ($dir == 'CW') ? 90 : -90;

  		$this->imageMagickExec .= " -rotate $angle ";
		
		$newWidth = $this->currentDimensions['height'];
	   	$newHeight = $this->currentDimensions['width'];
		$this->currentDimensions['width'] = $newWidth;
		$this->currentDimensions['height'] = $newHeight;
	}

   /**
	 * Crops the image from calculated center in a square of $cropSize pixels
	 *
	 * @param int $cropSize
	 */
	function cropFromCenter($cropSize, $resampleMode = 3) {
	   if($cropSize > $this->currentDimensions['width']) $cropSize = $this->currentDimensions['width'];
	   if($cropSize > $this->currentDimensions['height']) $cropSize = $this->currentDimensions['height'];

	   //$cropX = intval(($this->currentDimensions['width'] - $cropSize) / 2);
	   //$cropY = intval(($this->currentDimensions['height'] - $cropSize) / 2);

		//string to crop the picture to $cropSize,$cropSize (from center)
		//result: -gravity Center -crop $cropSizex$cropSize+0+0
		$this->imageMagickExec .= ' -gravity Center -crop ' . $cropSize . 'x' . $cropSize . '+0+0';
		
		// next calculations should be done with the 'new' dimensions
		$this->currentDimensions['width'] = $cropSize;
		$this->currentDimensions['height'] = $cropSize;		
	}

	/**
	 * Advanced cropping function that crops an image using $startX and $startY as the upper-left hand corner.
	 *
	 * @param int $startX
	 * @param int $startY
	 * @param int $width
	 * @param int $height
	 */
	function crop($startX,$startY,$width,$height) {
	    //make sure the cropped area is not greater than the size of the image
	   if($width > $this->currentDimensions['width']) $width = $this->currentDimensions['width'];
	   if($height > $this->currentDimensions['height']) $height = $this->currentDimensions['height'];
	    //make sure not starting outside the image
	   if(($startX + $width) > $this->currentDimensions['width']) $startX = ($this->currentDimensions['width'] - $width);
	   if(($startY + $height) > $this->currentDimensions['height']) $startY = ($this->currentDimensions['height'] - $height);
	   if($startX < 0) $startX = 0;
	   if($startY < 0) $startY = 0;

		//string to crop the picture to $width,$height (from $startX,$startY)
		//result: -crop $widthx$height+$startX+$startY
		$this->imageMagickExec .= ' -crop ' . $width . 'x' . $height . '+' . $startX .'+' . $startY;

		$this->currentDimensions['width'] = $width;
		$this->currentDimensions['height'] = $height;
	}

	/**
	 * Creates Apple-style reflection under image, optionally adding a border to main image
	 *
	 * @param int $percent
	 * @param int $reflection
	 * @param int $white
	 * @param bool $border
	 * @param string $borderColor
	 */
	function createReflection($percent, $reflection, $white, $border = true, $borderColor = '#a4a4a4') {

	    $width = $this->currentDimensions['width'];
	    $height = $this->currentDimensions['height'];
	
	    $reflectionHeight = intval($height * ($reflection / 100));
	    $newHeight = $height + $reflectionHeight;
	        //$reflectedPart = $height * ((100-$percent) / 100);
	    $reflectedsize = intval($height * ((100 - (100 - $percent) + $reflection) / 100)); 
			
		$this->imageMagickBefore = "-size $width" . "x" ."$newHeight xc:white ";
			
		if($border == true) {
			$this->imageMagickBefore .= " \( ";	 
			$this->imageMagickExec = " -bordercolor '$borderColor' -border 1 \) ";
		}

		$this->imageMagickExec .= " -geometry +0+0 -composite ";
		$gradientWhite = 100-$white;
		$this->imageMagickExec .= " \( '$this->fileName' -flip -resize $width"."x"."$reflectedsize\! \( -size $width"."x"."$reflectionHeight gradient: -fill black -colorize $gradientWhite \) +matte -compose copy_opacity -composite \) -geometry +0+$height -composite ";

		$this->currentDimensions['width'] = $width;
		$this->currentDimensions['height'] = $newHeight;
	}
	
	/**
 	 * @param string $color
	 * @param string $wmFont
	 * @param int $wmSize
 	 * @param int $wmOpaque
     */
	function watermarkCreateText($color = '000000', $wmFont, $wmSize = 10, $wmOpaque = 90 ){
		//create a watermark.png image with the requested text.
		
		// set font path
		$wmFontPath = NGGALLERY_ABSPATH . 'fonts/' . $wmFont;
		if ( !is_readable($wmFontPath) )
			return;	
			
		/*
		$exec = "convert -size 800x500 xc:grey30 -font $wmFontPath -pointsize $wmSize -gravity center -draw \"fill '#$color$wmOpaque'  text 0,0  '$this->watermarkText'\" stamp_fgnd.png"; 
		$make_magick = system($exec);
		$exec = "convert -size 800x500 xc:black -font $wmFontPath -pointsize $wmSize -gravity center -draw \"fill white  text  1,1  '$this->watermarkText'  text  0,0  '$this->watermarkText' fill black  text -1,-1 '$this->watermarkText'\" +matte stamp_mask.png";
		$make_magick = system($exec);
		$exec = "composite -compose CopyOpacity  stamp_mask.png  stamp_fgnd.png  watermark.png";*/

		//convert the opacity between FF or 00; 100->0 and 0->FF (256)
		$opacity = dechex( round( (100-$wmOpaque) * 256/100 ) );
		if ($opacity == "0") {$opacity = "00";} 
		
		$cmd = "-size 800x500 xc:none -fill '#{$color}{$opacity}' -font {$wmFontPath} -pointsize {$wmSize} -gravity center -annotate 0 '{$this->watermarkText}' watermark_text.png";
		$this->execute('convert', $cmd);
		
		$cmd = "-trim +repage watermark_text.png";		 
		$this->execute('mogrify', $cmd);
	
		$this->watermarkImgPath = NGGALLERY_ABSPATH . 'watermark_text.png';

		return;		
	}
    
    /**
     *
 	 * @param string $relPOS
	 * @param int $xPOS
 	 * @param int $yPOS
     */
    function watermarkImage( $relPOS = 'botRight', $xPOS = 0, $yPOS = 0) {

		// if it's not a valid file die... 
		/*if ( !is_readable($this->watermarkImgPath))
		{
			echo $this->watermarkImgPath;
			return;
		}	*/

		$size = GetImageSize($this->watermarkImgPath);
    	$watermarkDimensions = array('width'=>$size[0],'height'=>$size[1]);
		
		$sourcefile_width=$this->currentDimensions['width'];
		$sourcefile_height=$this->currentDimensions['height'];
		
		$watermarkfile_width=$watermarkDimensions['width'];
		$watermarkfile_height=$watermarkDimensions['height'];

		switch( substr($relPOS, 0, 3) ){
			case 'top': $dest_y = 0 + $yPOS; break;
			case 'mid': $dest_y = ($sourcefile_height / 2) - ($watermarkfile_height / 2); break;
			case 'bot': $dest_y = $sourcefile_height - $watermarkfile_height - $yPOS; break;
			default   : $dest_y = 0; break;
		}
		switch( substr($relPOS, 3) ){
			case 'Left'	:	$dest_x = 0 + $xPOS; break;
			case 'Center':	$dest_x = ($sourcefile_width / 2) - ($watermarkfile_width / 2); break;
			case 'Right':	$dest_x = $sourcefile_width - $watermarkfile_width - $xPOS; break;
			default : 		$dest_x = 0; break;
		}
		if ($dest_y<0) {
			$dest_y = $dest_y; 
		} else { 
			$dest_y = '+' . $dest_y;
		}
		if ($dest_x<0) {
			$dest_x = $dest_x; 
		} else { 
			$dest_x = '+' . $dest_x;
		}
		
		$this->imageMagickComp .=  "'$this->watermarkImgPath' -geometry $dest_x$dest_y  -composite";
		//" -dissolve 80% -geometry +$dest_x+$dest_y $this->watermarkImgPath";
	}
    
	/**
	 * Saves image as $name (can include file path), with quality of # percent if file is a jpeg
	 *
	 * @param string $name
	 * @param int $quality
	 * @return bool errorstate
	 */
	function save( $name, $quality = 85 ) {
	    $this->show($quality,$name);
	    if ($this->error == true) {
	    	//$this->errmsg = 'Create Image failed. Check safe mode settings';
	    	return false;
	    }
        
        if( function_exists('do_action') )
	       do_action('ngg_ajax_image_save', $name);

	    return true;
	}
	    
	/**
	 * Outputs the image to the screen, or saves to $name if supplied.  Quality of JPEG images can be controlled with the $quality variable
	 *
	 * @param int $quality
	 * @param string $name
	 */
	function show( $quality = 85, $name = '') {
		//save the image if we get a filename
		if( $name != '' ) {
			$args = "{$this->imageMagickBefore} ";
			$args .= escapeshellarg("$this->fileName");
			$args .= " $this->imageMagickExec $this->imageMagickComp -quality '$quality' ";
			$args .= escapeshellarg("$name");
			//$args = "{$this->imageMagickBefore} '$this->fileName' $this->imageMagickExec $this->imageMagickComp -quality $quality '$name'";
			$this->execute('convert', $args);
			//$this->error = true;			
	  } else {
	  	//return a raw image stream
			$args = "{$this->imageMagickBefore} '$this->fileName' $this->imageMagickExec $this->imageMagickComp -quality $quality JPG:-"; 
			$this->execute('convert', $args, true);
			$this->error = true;
		}
	}
}
