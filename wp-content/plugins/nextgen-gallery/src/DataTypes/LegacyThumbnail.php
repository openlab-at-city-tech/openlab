<?php

namespace Imagely\NGG\DataTypes;

/**
 * PHP class for dynamically resizing, cropping, and rotating images for thumbnail purposes and either displaying them on-the-fly or saving them.
 */
class LegacyThumbnail {

	/**
	 * Error message to display, if any
	 *
	 * @var string
	 */
	public $errmsg;
	/**
	 * Whether or not there is an error
	 *
	 * @var boolean
	 */
	public $error;
	/**
	 * Format of the image file
	 *
	 * @var string
	 */
	public $format;
	/**
	 * File name and path of the image file
	 *
	 * @var string
	 */
	public $fileName;
	/**
	 * Current dimensions of working image
	 *
	 * @var array
	 */
	public $currentDimensions;
	/**
	 * New dimensions of working image
	 *
	 * @var array
	 */
	public $newDimensions;
	/**
	 * Image resource for newly manipulated image
	 *
	 * @var resource
	 * @access private
	 */
	public $newImage;
	/**
	 * Image resource for image before previous manipulation
	 *
	 * @var resource
	 * @access private
	 */
	public $oldImage;
	/**
	 * Image resource for image being currently manipulated
	 *
	 * @var resource
	 * @access private
	 */
	public $workingImage;
	/**
	 * Percentage to resize image by
	 *
	 * @var int
	 * @access private
	 */
	public $percent;
	/**
	 * Maximum width of image during resize
	 *
	 * @var int
	 * @access private
	 */
	public $maxWidth;
	/**
	 * Maximum height of image during resize
	 *
	 * @var int
	 * @access private
	 */
	public $maxHeight;
	/**
	 * Image for Watermark
	 *
	 * @var string
	 */
	public $watermarkImgPath;
	/**
	 * Text for Watermark
	 *
	 * @var string
	 */
	public $watermarkText;

	public $newWidth;
	public $newHeight;

	/**
	 * Image Resource ID for Watermark
	 *
	 * @var string
	 */
	public function __construct( $fileName, $no_ErrorImage = false ) {
		// make sure the GD library is installed.
		if ( ! function_exists( 'gd_info' ) ) {
			echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
			echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
			throw new \E_No_Image_Library_Exception();
		}
		// initialize variables.
		$this->errmsg            = '';
		$this->error             = false;
		$this->currentDimensions = [];
		$this->newDimensions     = [];
		$this->fileName          = $fileName;
		$this->percent           = 100;
		$this->maxWidth          = 0;
		$this->maxHeight         = 0;
		$this->watermarkImgPath  = '';
		$this->watermarkText     = '';

		// check to see if file exists.
		if ( ! @file_exists( $this->fileName ) ) {
			$this->errmsg = 'File not found';
			$this->error  = true;
		}
		// check to see if file is readable.
		elseif ( ! is_readable( $this->fileName ) ) {
			$this->errmsg = 'File is not readable';
			$this->error  = true;
		}

		$image_size = null;

		// if there are no errors, determine the file format.
		if ( $this->error == false ) {
			@ini_set( 'memory_limit', -1 );
			$image_size = @getimagesize( $this->fileName );
			if ( isset( $image_size ) && is_array( $image_size ) ) {
				$extensions = [
					IMAGETYPE_GIF  => 'GIF',
					IMAGETYPE_JPEG => 'JPG',
					IMAGETYPE_PNG  => 'PNG',
					IMAGETYPE_WEBP => 'WEBP',
				];
				$extension  = array_key_exists( $image_size[2], $extensions ) ? $extensions[ $image_size[2] ] : '';
				if ( $extension ) {
					$this->format = $extension;
				} else {
					$this->errmsg = 'Unknown file format';
					$this->error  = true;
				}
			} else {
				$this->errmsg = 'File is not an image';
				$this->error  = true;
			}
		}

		// increase memory-limit if possible, GD needs this for large images.
		if ( ! extension_loaded( 'suhosin' ) ) {
			@ini_set( 'memory_limit', '512M' );
		}

		if ( $this->error == false ) {
			// Check memory consumption if file exists.
			$this->checkMemoryForImage( $this->fileName );
		}

		// initialize resources if no errors.
		if ( $this->error == false ) {
			$img_err = null;

			switch ( $this->format ) {
				case 'GIF':
					if ( function_exists( 'ImageCreateFromGif' ) ) {
						$this->oldImage = @ImageCreateFromGif( $this->fileName );
					} else {
						$img_err = __( 'Support for GIF format is missing.', 'nggallery' );
					}
					break;
				case 'JPG':
					if ( function_exists( 'ImageCreateFromJpeg' ) ) {
						$this->oldImage = @ImageCreateFromJpeg( $this->fileName );
					} else {
						$img_err = __( 'Support for JPEG format is missing.', 'nggallery' );
					}
					break;
				case 'PNG':
					if ( function_exists( 'ImageCreateFromPng' ) ) {
						$this->oldImage = @ImageCreateFromPng( $this->fileName );
					} else {
						$img_err = __( 'Support for PNG format is missing.', 'nggallery' );
					}
					break;
				case 'WEBP':
					if ( function_exists( 'imagecreatefromwebp' ) ) {
						$this->oldImage = @imagecreatefromwebp( $this->fileName );
					} else {
						$img_err = __( 'Support for WEBP format is missing.', 'nggallery' );
					}
					break;
			}

			if ( ! $this->oldImage ) {
				if ( $img_err == null ) {
					$img_err = __( 'Check memory limit', 'nggallery' );
				}

				$this->errmsg = sprintf( __( 'Create Image failed. %1$s', 'nggallery' ), $img_err );
				$this->error  = true;
			} else {
				$this->currentDimensions = [
					'width'  => $image_size[0],
					'height' => $image_size[1],
				];
				$this->newImage          = $this->oldImage;
			}
		}

		if ( $this->error == true ) {
			if ( ! $no_ErrorImage ) {
				$this->showErrorImage();
			}
			return;
		}
	}

	/**
	 * Calculate the memory limit
	 *
	 * @param string $filename
	 */
	public function checkMemoryForImage( $filename ) {
		$imageInfo = getimagesize( $filename );
		switch ( $this->format ) {
			case 'GIF':
				// measured factor 1 is better.
				$CHANNEL = 1;
				break;
			case 'JPG':
				$CHANNEL = $imageInfo['channels'];
				break;
			case 'PNG':
				// didn't get the channel for png.
				$CHANNEL = 3;
				break;
			case 'WEBP':
				$CHANNEL = $imageInfo['bits'];
				break;
		}
		$bits = ( ! empty( $imageInfo['bits'] ) ? $imageInfo['bits'] : 32 ); // imgInfo[bits] is not always available.

		return $this->checkMemoryForData( $imageInfo[0], $imageInfo[1], $CHANNEL, $bits );
	}

	public function checkMemoryForData( $width, $height, $channels = null, $bits = null ) {
		$imageInfo = getimagesize( $this->fileName );

		if ( $channels == null ) {
			switch ( $this->format ) {
				case 'GIF':
					// measured factor 1 is better.
					$channels = 1;
					break;
				case 'JPG':
					$channels = $imageInfo['channels'];
					break;
				case 'PNG':
					// didn't get the channel for png.
					$channels = 3;
					break;
				case 'WEBP':
					$channels = $imageInfo['bits'];
					break;
			}
		}
		if ( $bits == null ) {
			$bits = ( ! empty( $imageInfo['bits'] ) ? $imageInfo['bits'] : 32 ); // imgInfo[bits] is not always available.
		}

		if ( ( function_exists( 'memory_get_usage' ) ) && ( ini_get( 'memory_limit' ) ) ) {
			$MB           = 1048576;  // number of bytes in 1M.
			$K64          = 65536;    // number of bytes in 64K.
			$TWEAKFACTOR  = 1.68;  // Or whatever works for you.
			$memoryNeeded = round( ( doubleval( $width * $height * $bits * $channels ) / 8 + $K64 ) * $TWEAKFACTOR );
			$memoryNeeded = memory_get_usage() + $memoryNeeded;

			// get memory limit.
			$memory_limit = ini_get( 'memory_limit' );

			// PHP docs : Note that to have no memory limit, set this directive to -1.
			if ( $memory_limit == -1 ) {
				return true;
			}

			// Just check megabyte limits, not higher.
			if ( strtolower( substr( $memory_limit, -1 ) ) == 'm' ) {

				if ( $memory_limit != '' ) {
					$memory_limit = intval( substr( $memory_limit, 0, -1 ) ) * 1024 * 1024;
				}

				if ( $memoryNeeded > $memory_limit ) {
					$memoryNeeded = round( $memoryNeeded / 1024 / 1024, 2 );
					$this->errmsg = 'Exceed Memory limit. Require : ' . $memoryNeeded . ' MByte';
					$this->error  = true;

					return false;
				}
			}
		}

		return true;
	}

	public function __destruct() {
		$this->destruct();
	}

	/**
	 * Must be called to free up allocated memory after all manipulations are done
	 */
	public function destruct() {
		if ( is_resource( $this->newImage ) || $this->newImage instanceof \GdImage ) {
			@imagedestroy( $this->newImage );
		}

		if ( is_resource( $this->oldImage ) || $this->oldImage instanceof \GdImage ) {
			@imagedestroy( $this->oldImage );
		}

		if ( is_resource( $this->workingImage ) || $this->workingImage instanceof \GdImage ) {
			@imagedestroy( $this->workingImage );
		}
	}

	/**
	 * Returns the current width of the image
	 *
	 * @return int
	 */
	public function getCurrentWidth() {
		return $this->currentDimensions['width'];
	}

	/**
	 * Returns the current height of the image
	 *
	 * @return int
	 */
	public function getCurrentHeight() {
		return $this->currentDimensions['height'];
	}

	/**
	 * Calculates new image width
	 *
	 * @param int $width
	 * @param int $height
	 * @return array
	 */
	public function calcWidth( $width, $height ) {
		$newWp     = ( 100 * $this->maxWidth ) / $width;
		$newHeight = ( $height * $newWp ) / 100;

		if ( intval( $newHeight ) == $this->maxHeight - 1 ) {
			$newHeight = $this->maxHeight;
		}

		return [
			'newWidth'  => intval( $this->maxWidth ),
			'newHeight' => intval( $newHeight ),
		];
	}

	/**
	 * Calculates new image height
	 *
	 * @param int $width
	 * @param int $height
	 * @return array
	 */
	public function calcHeight( $width, $height ) {
		$newHp    = ( 100 * $this->maxHeight ) / $height;
		$newWidth = ( $width * $newHp ) / 100;

		if ( intval( $newWidth ) == $this->maxWidth - 1 ) {
			$newWidth = $this->maxWidth;
		}

		return [
			'newWidth'  => intval( $newWidth ),
			'newHeight' => intval( $this->maxHeight ),
		];
	}

	/**
	 * Calculates new image size based on percentage
	 *
	 * @param int $width
	 * @param int $height
	 * @return array
	 */
	public function calcPercent( $width, $height, $percent = -1 ) {
		if ( $percent == -1 ) {
			$percent = $this->percent;
		}
		$newWidth  = ( $width * $percent ) / 100;
		$newHeight = ( $height * $percent ) / 100;
		return [
			'newWidth'  => intval( $newWidth ),
			'newHeight' => intval( $newHeight ),
		];
	}

	/**
	 * Calculates new image size based on width and height, while constraining to maxWidth and maxHeight
	 *
	 * @param int $width
	 * @param int $height
	 */
	public function calcImageSize( $width, $height ) {
		// $width and $height are the CURRENT image resolutions
		$ratio_w = $this->maxWidth / $width;
		$ratio_h = $this->maxHeight / $height;

		if ( $ratio_w >= $ratio_h ) {
			$width  = $this->maxWidth;
			$height = (int) round( $height * $ratio_h, 0 );
		} else {
			$height = $this->maxHeight;
			$width  = (int) round( $width * $ratio_w, 0 );
		}

		$this->newDimensions = [
			'newWidth'  => $width,
			'newHeight' => $height,
		];
	}

	/**
	 * Calculates new image size based percentage
	 *
	 * @param int $width
	 * @param int $height
	 */
	public function calcImageSizePercent( $width, $height ) {
		if ( $this->percent > 0 ) {
			$this->newDimensions = $this->calcPercent( $width, $height );
		}
	}

	/**
	 * Displays error image
	 */
	public function showErrorImage() {
		header( 'Content-type: image/png' );
		$errImg   = ImageCreate( 220, 25 );
		$bgColor  = imagecolorallocate( $errImg, 0, 0, 0 );
		$fgColor1 = imagecolorallocate( $errImg, 255, 255, 255 );
		$fgColor2 = imagecolorallocate( $errImg, 255, 0, 0 );
		imagestring( $errImg, 3, 6, 6, 'Error:', $fgColor2 );
		imagestring( $errImg, 3, 55, 6, $this->errmsg, $fgColor1 );
		imagepng( $errImg );
		imagedestroy( $errImg );
	}

	/**
	 * Resizes image to fixed Width x Height
	 *
	 * @param int $Width
	 * @param int $Height
	 * @param int $deprecated Unused
	 */
	public function resizeFix( $Width = 0, $Height = 0, $deprecated = 3 ) {
		if ( ! $this->checkMemoryForData( $Width, $Height ) ) {
			return;
		}

		$this->newWidth  = $Width;
		$this->newHeight = $Height;

		if ( function_exists( 'ImageCreateTrueColor' ) ) {
			$this->workingImage = ImageCreateTrueColor( $this->newWidth, $this->newHeight );
		} else {
			$this->workingImage = ImageCreate( $this->newWidth, $this->newHeight );
		}

		// ImageCopyResampled(.
		$this->imagecopyresampled(
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			0,
			0,
			$this->newWidth,
			$this->newHeight,
			$this->currentDimensions['width'],
			$this->currentDimensions['height']
		);

		$this->oldImage                    = $this->workingImage;
		$this->newImage                    = $this->workingImage;
		$this->currentDimensions['width']  = $this->newWidth;
		$this->currentDimensions['height'] = $this->newHeight;
	}


	/**
	 * Resizes image to maxWidth x maxHeight
	 *
	 * @param int $maxWidth
	 * @param int $maxHeight
	 * @param int $deprecated Unused
	 */
	public function resize( $maxWidth = 0, $maxHeight = 0, $deprecated = 3 ) {
		if ( ! $this->checkMemoryForData( $maxWidth, $maxHeight ) ) {
			return;
		}

		$this->maxWidth  = $maxWidth;
		$this->maxHeight = $maxHeight;

		$this->calcImageSize( $this->currentDimensions['width'], $this->currentDimensions['height'] );

		if ( $this->workingImage != null && $this->workingImage != $this->oldImage ) {
				ImageDestroy( $this->workingImage );
				$this->workingImage = null;
		}

		if ( function_exists( 'ImageCreateTrueColor' ) ) {
			$this->workingImage = ImageCreateTrueColor( $this->newDimensions['newWidth'], $this->newDimensions['newHeight'] );
		} else {
			$this->workingImage = ImageCreate( $this->newDimensions['newWidth'], $this->newDimensions['newHeight'] );
		}

		$this->imagecopyresampled(
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			0,
			0,
			$this->newDimensions['newWidth'],
			$this->newDimensions['newHeight'],
			$this->currentDimensions['width'],
			$this->currentDimensions['height']
		);

		ImageDestroy( $this->oldImage );

		$this->oldImage                    = $this->workingImage;
		$this->newImage                    = $this->workingImage;
		$this->currentDimensions['width']  = $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] = $this->newDimensions['newHeight'];
	}

	/**
	 * Resizes the image by $percent percent
	 *
	 * @param int $percent
	 */
	public function resizePercent( $percent = 0 ) {
		$dims = $this->calcPercent( $this->currentDimensions['width'], $this->currentDimensions['height'], $percent );
		if ( ! $this->checkMemoryForData( $dims['newWidth'], $dims['newHeight'] ) ) {
			return;
		}
		$this->percent = $percent;

		$this->calcImageSizePercent( $this->currentDimensions['width'], $this->currentDimensions['height'] );

		if ( function_exists( 'ImageCreateTrueColor' ) ) {
			$this->workingImage = ImageCreateTrueColor( $this->newDimensions['newWidth'], $this->newDimensions['newHeight'] );
		} else {
			$this->workingImage = ImageCreate( $this->newDimensions['newWidth'], $this->newDimensions['newHeight'] );
		}

		$this->ImageCopyResampled(
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			0,
			0,
			$this->newDimensions['newWidth'],
			$this->newDimensions['newHeight'],
			$this->currentDimensions['width'],
			$this->currentDimensions['height']
		);

		$this->oldImage                    = $this->workingImage;
		$this->newImage                    = $this->workingImage;
		$this->currentDimensions['width']  = $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] = $this->newDimensions['newHeight'];
	}

	/**
	 * Crops the image from calculated center in a square of $cropSize pixels
	 *
	 * @param int $cropSize
	 */
	public function cropFromCenter( $cropSize ) {
		if ( $cropSize > $this->currentDimensions['width'] ) {
			$cropSize = $this->currentDimensions['width'];
		}
		if ( $cropSize > $this->currentDimensions['height'] ) {
			$cropSize = $this->currentDimensions['height'];
		}

		$cropX = intval( ( $this->currentDimensions['width'] - $cropSize ) / 2 );
		$cropY = intval( ( $this->currentDimensions['height'] - $cropSize ) / 2 );

		if ( $this->workingImage != null && $this->workingImage != $this->oldImage ) {
				ImageDestroy( $this->workingImage );
				$this->workingImage = null;
		}

		if ( function_exists( 'ImageCreateTrueColor' ) ) {
			$this->workingImage = ImageCreateTrueColor( $cropSize, $cropSize );
		} else {
			$this->workingImage = ImageCreate( $cropSize, $cropSize );
		}

		$this->imagecopyresampled(
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			$cropX,
			$cropY,
			$cropSize,
			$cropSize,
			$cropSize,
			$cropSize
		);

		$this->oldImage                    = $this->workingImage;
		$this->newImage                    = $this->workingImage;
		$this->currentDimensions['width']  = $cropSize;
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
	public function crop( $startX, $startY, $width, $height ) {
		if ( ! $this->checkMemoryForData( $width, $height ) ) {
			return;
		}
		// make sure the cropped area is not greater than the size of the image.
		if ( $width > $this->currentDimensions['width'] ) {
			$width = $this->currentDimensions['width'];
		}
		if ( $height > $this->currentDimensions['height'] ) {
			$height = $this->currentDimensions['height'];
		}
		// make sure not starting outside the image.
		if ( ( $startX + $width ) > $this->currentDimensions['width'] ) {
			$startX = ( $this->currentDimensions['width'] - $width );
		}
		if ( ( $startY + $height ) > $this->currentDimensions['height'] ) {
			$startY = ( $this->currentDimensions['height'] - $height );
		}
		if ( $startX < 0 ) {
			$startX = 0;
		}
		if ( $startY < 0 ) {
			$startY = 0;
		}

		if ( $this->workingImage != null && $this->workingImage != $this->oldImage ) {
				ImageDestroy( $this->workingImage );
				$this->workingImage = null;
		}

		if ( function_exists( 'ImageCreateTrueColor' ) ) {
			$this->workingImage = ImageCreateTrueColor( $width, $height );
		} else {
			$this->workingImage = ImageCreate( $width, $height );
		}

		$this->imagecopyresampled(
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			$startX,
			$startY,
			$width,
			$height,
			$width,
			$height
		);

		ImageDestroy( $this->oldImage );

		$this->oldImage                    = $this->workingImage;
		$this->newImage                    = $this->workingImage;
		$this->currentDimensions['width']  = $width;
		$this->currentDimensions['height'] = $height;
	}

	/**
	 * Outputs the image to the screen, or saves to $name if supplied.  Quality of JPEG images can be controlled with the $quality variable
	 *
	 * @param int    $quality
	 * @param string $name
	 */
	public function show( $quality = 100, $name = '' ) {
		switch ( $this->format ) {
			case 'GIF':
				if ( $name != '' ) {
					@ImageGif( $this->newImage, $name ) or $this->error = true;
				} else {
					header( 'Content-type: image/gif' );
					ImageGif( $this->newImage );
				}
				break;
			case 'JPG':
				if ( $name != '' ) {
					@ImageJpeg( $this->newImage, $name, $quality ) or $this->error = true;
				} else {
					header( 'Content-type: image/jpeg' );
					ImageJpeg( $this->newImage, null, $quality );
				}
				break;
			case 'PNG':
				if ( $name != '' ) {
					@ImagePng( $this->newImage, $name ) or $this->error = true;
				} else {
					header( 'Content-type: image/png' );
					ImagePng( $this->newImage );
				}
				break;
			case 'WEBP':
				if ( $name != '' ) {
					$this->error = ! @imagewebp( $this->newImage, $name );
				} else {
					header( 'Content-type: image/webp' );
					imagewebp( $this->newImage );
				}
				break;
		}
	}

	/**
	 * Saves image as $name (can include file path), with quality of # percent if file is a jpeg
	 *
	 * @param string $name
	 * @param int    $quality
	 * @return bool errorstate
	 */
	public function save( $name, $quality = 100 ) {
		$this->show( $quality, $name );
		if ( $this->error == true ) {
			$this->errmsg = 'Create Image failed. Check safe mode settings';
			return false;
		}

		if ( function_exists( 'do_action' ) ) {
			do_action( 'ngg_ajax_image_save', $name );
		}

		return true;
	}

	/**
	 * Creates Apple-style reflection under image, optionally adding a border to main image
	 *
	 * @param int    $percent
	 * @param int    $reflection
	 * @param int    $white
	 * @param bool   $border
	 * @param string $borderColor
	 */
	public function createReflection( $percent, $reflection, $white, $border = true, $borderColor = '#a4a4a4' ) {
		$width  = $this->currentDimensions['width'];
		$height = $this->currentDimensions['height'];

		$reflectionHeight = intval( $height * ( $reflection / 100 ) );
		$newHeight        = $height + $reflectionHeight;
		$reflectedPart    = $height * ( $percent / 100 );

		$this->workingImage = ImageCreateTrueColor( $width, $newHeight );

		ImageAlphaBlending( $this->workingImage, true );

		$colorToPaint = ImageColorAllocateAlpha( $this->workingImage, 255, 255, 255, 0 );
		ImageFilledRectangle( $this->workingImage, 0, 0, $width, $newHeight, $colorToPaint );

		imagecopyresampled(
			$this->workingImage,
			$this->newImage,
			0,
			0,
			0,
			$reflectedPart,
			$width,
			$reflectionHeight,
			$width,
			( $height - $reflectedPart )
		);
		$this->imageFlipVertical();

		imagecopy( $this->workingImage, $this->newImage, 0, 0, 0, 0, $width, $height );

		imagealphablending( $this->workingImage, true );

		for ( $i = 0;$i < $reflectionHeight;$i++ ) {
			$colorToPaint = imagecolorallocatealpha( $this->workingImage, 255, 255, 255, ( $i / $reflectionHeight * -1 + 1 ) * $white );
			imagefilledrectangle( $this->workingImage, 0, $height + $i, $width, $height + $i, $colorToPaint );
		}

		if ( $border == true ) {
			$rgb          = $this->hex2rgb( $borderColor, false );
			$colorToPaint = imagecolorallocate( $this->workingImage, $rgb[0], $rgb[1], $rgb[2] );
			imageline( $this->workingImage, 0, 0, $width, 0, $colorToPaint ); // top line.
			imageline( $this->workingImage, 0, $height, $width, $height, $colorToPaint ); // bottom line.
			imageline( $this->workingImage, 0, 0, 0, $height, $colorToPaint ); // left line.
			imageline( $this->workingImage, $width - 1, 0, $width - 1, $height, $colorToPaint ); // right line.
		}

		$this->oldImage                    = $this->workingImage;
		$this->newImage                    = $this->workingImage;
		$this->currentDimensions['width']  = $width;
		$this->currentDimensions['height'] = $newHeight;
	}

	/**
	 * Flip an image.
	 *
	 * @param bool $horz flip the image in horizontal mode
	 * @param bool $vert flip the image in vertical mode
	 * @return true
	 */
	public function flipImage( $horz = false, $vert = false ) {

		$sx = $vert ? ( $this->currentDimensions['width'] - 1 ) : 0;
		$sy = $horz ? ( $this->currentDimensions['height'] - 1 ) : 0;
		$sw = $vert ? -$this->currentDimensions['width'] : $this->currentDimensions['width'];
		$sh = $horz ? -$this->currentDimensions['height'] : $this->currentDimensions['height'];

		$this->workingImage = imagecreatetruecolor( $this->currentDimensions['width'], $this->currentDimensions['height'] );

		$this->imagecopyresampled( $this->workingImage, $this->oldImage, 0, 0, $sx, $sy, $this->currentDimensions['width'], $this->currentDimensions['height'], $sw, $sh );
		$this->oldImage = $this->workingImage;
		$this->newImage = $this->workingImage;

		return true;
	}

	/**
	 * Rotate an image clockwise or counter clockwise
	 *
	 * @param string $dir Either CW or CCW
	 * @return bool
	 */
	public function rotateImage( $dir = 'CW' ) {
		$angle = ( $dir == 'CW' ) ? 90 : -90;

		return $this->rotateImageAngle( $angle );
	}

	/**
	 * Rotate an image clockwise or counter clockwise
	 *
	 * @param int $angle Degrees to rotate the target image
	 * @return bool
	 */
	public function rotateImageAngle( $angle = 90 ) {
		if ( function_exists( 'imagerotate' ) ) {
			$this->currentDimensions['width']  = imagesx( $this->workingImage );
			$this->currentDimensions['height'] = imagesy( $this->workingImage );
			$this->oldImage                    = $this->workingImage;

			// imagerotate() rotates CCW ;.
			// See for help: https://evertpot.com/115/.
			$this->newImage = imagerotate( $this->oldImage, 360 - $angle, 0 );
			return true;
		}

		$this->workingImage = imagecreatetruecolor( $this->currentDimensions['height'], $this->currentDimensions['width'] );

		imagealphablending( $this->workingImage, false );
		imagesavealpha( $this->workingImage, true );

		switch ( $angle ) {

			case 90:
				for ( $x = 0; $x < $this->currentDimensions['width']; $x++ ) {
					for ( $y = 0; $y < $this->currentDimensions['height']; $y++ ) {
						if ( ! imagecopy( $this->workingImage, $this->oldImage, $this->currentDimensions['height'] - $y - 1, $x, $x, $y, 1, 1 ) ) {
							return false;
						}
					}
				}
				break;

			case -90:
				for ( $x = 0; $x < $this->currentDimensions['width']; $x++ ) {
					for ( $y = 0; $y < $this->currentDimensions['height']; $y++ ) {
						if ( ! imagecopy( $this->workingImage, $this->oldImage, $y, $this->currentDimensions['width'] - $x - 1, $x, $y, 1, 1 ) ) {
							return false;
						}
					}
				}
				break;

			default:
				return false;
		}

		$this->currentDimensions['width']  = imagesx( $this->workingImage );
		$this->currentDimensions['height'] = imagesy( $this->workingImage );
		$this->oldImage                    = $this->workingImage;
		$this->newImage                    = $this->workingImage;

		return true;
	}

	/**
	 * Inverts working image, used by reflection function
	 *
	 * @access  private
	 */
	public function imageFlipVertical() {
		$x_i = imagesx( $this->workingImage );
		$y_i = imagesy( $this->workingImage );

		for ( $x = 0; $x < $x_i; $x++ ) {
			for ( $y = 0; $y < $y_i; $y++ ) {
				imagecopy( $this->workingImage, $this->workingImage, $x, $y_i - $y - 1, $x, $y, 1, 1 );
			}
		}
	}

	/**
	 * Converts hexidecimal color value to rgb values and returns as array/string
	 *
	 * @param string $hex
	 * @param bool   $asString
	 * @return array|string
	 */
	public function hex2rgb( $hex, $asString = false ) {
		// strip off any leading #.
		if ( 0 === strpos( $hex, '#' ) ) {
			$hex = substr( $hex, 1 );
		} elseif ( 0 === strpos( $hex, '&H' ) ) {
			$hex = substr( $hex, 2 );
		}

		// break into hex 3-tuple.
		$cutpoint = ceil( strlen( $hex ) / 2 ) - 1;
		$rgb      = explode( ':', wordwrap( $hex, $cutpoint, ':', $cutpoint ), 3 );

		// convert each tuple to decimal.
		$rgb[0] = ( isset( $rgb[0] ) ? hexdec( $rgb[0] ) : 0 );
		$rgb[1] = ( isset( $rgb[1] ) ? hexdec( $rgb[1] ) : 0 );
		$rgb[2] = ( isset( $rgb[2] ) ? hexdec( $rgb[2] ) : 0 );

		return ( $asString ? "{$rgb[0]} {$rgb[1]} {$rgb[2]}" : $rgb );
	}

	/**
	 * Based on the Watermark function by Marek Malcherek
	 * http://www.malcherek.de
	 *
	 * @param string $color
	 * @param string $wmFont
	 * @param int    $wmSize
	 * @param int    $wmOpaque
	 */
	public function watermarkCreateText( $color, $wmFont, $wmSize = 10, $wmOpaque = 90 ) {
		if ( ! $color ) {
			$color = '000000';
		}
		// set font path.
		$wmFontPath = NGGALLERY_ABSPATH . 'fonts/' . $wmFont;
		if ( ! is_readable( $wmFontPath ) ) {
			return;
		}

		// This function requires both the GD library and the FreeType library.
		if ( ! function_exists( 'ImageTTFBBox' ) ) {
			return;
		}

		$words                 = preg_split( '/ /', $this->watermarkText );
		$lines                 = [];
		$line                  = '';
		$watermark_image_width = 0;

		// attempt adding a new word until the width is too large; then start a new line and start again.
		foreach ( $words as $word ) {
			// sanitize the text being input; imagettftext() can be sensitive.
			$TextSize = $this->ImageTTFBBoxDimensions(
				$wmSize,
				0,
				$this->correct_gd_unc_path( $wmFontPath ),
				$line . preg_replace(
					'~^(&([a-zA-Z0-9]);)~',
					htmlentities( '${1}' ),
					mb_convert_encoding( $word, 'HTML-ENTITIES', 'UTF-8' )
				)
			);

			if ( $watermark_image_width == 0 ) {
				$watermark_image_width = $TextSize['width'];
			}

			if ( $TextSize['width'] > $this->newDimensions['newWidth'] ) {
				$lines[] = trim( $line );
				$line    = '';
			} elseif ( $TextSize['width'] > $watermark_image_width ) {
					$watermark_image_width = $TextSize['width'];
			}
			$line .= $word . ' ';
		}
		$lines[] = trim( $line );

		// use this string to determine our largest possible line height.
		$line_dimensions = $this->ImageTTFBBoxDimensions( $wmSize, 0, $this->correct_gd_unc_path( $wmFontPath ), 'MXQJALYmxqjabdfghjklpqry019`@$^&*(,!132' );
		$line_height     = (float) $line_dimensions['height'] * 1.05;

		// Create an image to apply our text to.
		$this->workingImage = ImageCreateTrueColor( $watermark_image_width, (int) ( count( $lines ) * $line_height ) );
		ImageSaveAlpha( $this->workingImage, true );
		ImageAlphaBlending( $this->workingImage, false );
		$bgText = imagecolorallocatealpha( $this->workingImage, 255, 255, 255, 127 );
		imagefill( $this->workingImage, 0, 0, $bgText );
		$wmTransp  = 127 - ( (int) $wmOpaque * 1.27 );
		$rgb       = $this->hex2rgb( $color, false );
		$TextColor = imagecolorallocatealpha( $this->workingImage, (int) $rgb[0], (int) $rgb[1], (int) $rgb[2], (int) $wmTransp );

		// Put text on the image, line-by-line.
		$y_pos = $wmSize;
		foreach ( $lines as $line ) {
			imagettftext( $this->workingImage, $wmSize, 0, 0, $y_pos, $TextColor, $this->correct_gd_unc_path( $wmFontPath ), $line );
			$y_pos += $line_height;
		}

		$this->watermarkImgPath = $this->workingImage;

		return;
	}

	/**
	 * Returns a path that can be used with imagettftext() and ImageTTFBBox()
	 *
	 * imagettftext() and ImageTTFBBox() cannot load resources from Windows UNC paths
	 * and require they be mangled to be like //server\filename instead of \\server\filename
	 *
	 * @param string $path Absolute file path
	 * @return string $path Mangled absolute file path
	 */
	public function correct_gd_unc_path( $path ) {
		if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == 'WIN' && substr( $path, 0, 2 ) === '\\\\' ) {
			$path = ltrim( $path, '\\\\' );
			$path = '//' . $path;
		}
		return $path;
	}

	/**
	 * Calculates the width & height dimensions of ImageTTFBBox().
	 *
	 * Note: ImageTTFBBox() is unreliable with large font sizes
	 *
	 * @param $wmSize
	 * @param $fontAngle
	 * @param $wmFontPath
	 * @param $text
	 * @return array
	 */
	public function ImageTTFBBoxDimensions( $wmSize, $fontAngle, $wmFontPath, $text ) {
		$box   = @ImageTTFBBox( $wmSize, $fontAngle, $this->correct_gd_unc_path( $wmFontPath ), $text );
		$max_x = max( [ $box[0], $box[2], $box[4], $box[6] ] );
		$max_y = max( [ $box[1], $box[3], $box[5], $box[7] ] );
		$min_x = min( [ $box[0], $box[2], $box[4], $box[6] ] );
		$min_y = min( [ $box[1], $box[3], $box[5], $box[7] ] );
		return [
			'width'  => ( $max_x - $min_x ),
			'height' => ( $max_y - $min_y ),
		];
	}

	public function applyFilter( $filterType ) {
		$args = func_get_args();
		array_unshift( $args, $this->newImage );

		return call_user_func_array( 'imagefilter', $args );
	}


	/**
	 * Modfied Watermark function by Steve Peart
	 * http://parasitehosting.com/
	 *
	 * @param string $relPOS
	 * @param int    $xPOS
	 * @param int    $yPOS
	 */
	public function watermarkImage( $relPOS = 'botRight', $xPOS = 0, $yPOS = 0 ) {

		// if it's a resource ID take it as watermark text image.
		if ( is_resource( $this->watermarkImgPath ) || $this->watermarkImgPath instanceof \GdImage ) {
			$this->workingImage = $this->watermarkImgPath;
		} else {
			// (possibly) search for the file from the document root.
			if ( ! is_file( $this->watermarkImgPath ) ) {
				$fs = \Imagely\NGG\Util\Filesystem::get_instance();
				if ( is_file( $fs->join_paths( $fs->get_document_root( 'content' ), $this->watermarkImgPath ) ) ) {
					$this->watermarkImgPath = $fs->get_document_root( 'content' ) . $this->watermarkImgPath;
				}
			}

			// Would you really want to use anything other than a png?
			$this->workingImage = @imagecreatefrompng( $this->watermarkImgPath );
			// if it's not a valid file die...
			if ( empty( $this->workingImage ) or ( ! $this->workingImage ) ) {
				return;
			}
		}

		imagealphablending( $this->workingImage, false );
		imagesavealpha( $this->workingImage, true );
		$sourcefile_width     = imageSX( $this->oldImage );
		$sourcefile_height    = imageSY( $this->oldImage );
		$watermarkfile_width  = imageSX( $this->workingImage );
		$watermarkfile_height = imageSY( $this->workingImage );

		switch ( substr( $relPOS, 0, 3 ) ) {
			case 'top':
				$dest_y = 0 + $yPOS;
				break;
			case 'mid':
				$dest_y = ( $sourcefile_height / 2 ) - ( $watermarkfile_height / 2 );
				break;
			case 'bot':
				$dest_y = $sourcefile_height - $watermarkfile_height - $yPOS;
				break;
			default:
				$dest_y = 0;
				break;
		}
		switch ( substr( $relPOS, 3 ) ) {
			case 'Left':
				$dest_x = 0 + $xPOS;
				break;
			case 'Center':
				$dest_x = ( $sourcefile_width / 2 ) - ( $watermarkfile_width / 2 );
				break;
			case 'Right':
				$dest_x = $sourcefile_width - $watermarkfile_width - $xPOS;
				break;
			default:
				$dest_x = 0;
				break;
		}

		// if a gif, we have to upsample it to a truecolor image.
		if ( $this->format == 'GIF' ) {
			$tempimage = imagecreatetruecolor( $sourcefile_width, $sourcefile_height );
			imagecopy( $tempimage, $this->oldImage, 0, 0, 0, 0, $sourcefile_width, $sourcefile_height );
			$this->newImage = $tempimage;
		}

		$this->imagecopymerge_alpha(
			$this->newImage,
			$this->workingImage,
			$dest_x,
			$dest_y,
			0,
			0,
			$watermarkfile_width,
			$watermarkfile_height,
			100
		);
	}

	/**
	 * Wrapper to imagecopymerge() that allows PNG transparency
	 */
	public function imagecopymerge_alpha( $destination_image, $source_image, $destination_x, $destination_y, $source_x, $source_y, $source_w, $source_h, $pct ) {
		$cut = imagecreatetruecolor( $source_w, $source_h );
		imagecopy( $cut, $destination_image, 0, 0, (int) $destination_x, (int) $destination_y, (int) $source_w, (int) $source_h );
		imagecopy( $cut, $source_image, 0, 0, $source_x, $source_y, $source_w, $source_h );
		imagecopymerge( $destination_image, $cut, (int) $destination_x, (int) $destination_y, 0, 0, (int) $source_w, (int) $source_h, (int) $pct );
	}

	/**
	 * Modfied imagecopyresampled function to save transparent images
	 * See : http://www.akemapa.com/2008/07/10/php-gd-resize-transparent-image-png-gif/
	 *
	 * @since 1.9.0
	 *
	 * @param resource $dst_image
	 * @param resource $src_image
	 * @param int      $dst_x
	 * @param int      $dst_y
	 * @param int      $src_x
	 * @param int      $src_y
	 * @param int      $dst_w
	 * @param int      $dst_h
	 * @param int      $src_w
	 * @param int      $src_h
	 * @return bool
	 */
	public function imagecopyresampled( &$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h ) {

		// Check if this image is PNG or GIF, then set if Transparent.
		if ( $this->format == 'GIF' || $this->format == 'PNG' ) {
			imagealphablending( $dst_image, false );
			imagesavealpha( $dst_image, true );
			$transparent = imagecolorallocatealpha( $dst_image, 255, 255, 255, 127 );
			imagefilledrectangle( $dst_image, 0, 0, $dst_w, $dst_h, $transparent );
		}

		imagecopyresampled( $dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );
		return true;
	}
}
