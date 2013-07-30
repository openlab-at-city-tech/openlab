<?php
/*
* PHP Image Resize Class
*
* Class to deal with resizing images using PHP.
* Will resize any JPG, GIF or PNG file.
*
* Written By Jacob Wyke - jacob@redvodkajelly.com - www.redvodkajelly.com
* Edited by A. Huizinga 2009, 22 Jan: changed used of function imagecopyresized into imagecopyresampled
*
* LICENSE 
* Feel free to use this as you wish, just give me credit where credits due and drop me an email telling me what your using it for so I can check out all the cool ways its been used.
*
* USAGE
* To use this class simply call it with the following details:
*
*       Path to original image,
*       Path to save new image,
*       Resize type,
*       Resize Data
*     
* The resize type can be one of four:
*
*       W   =   Width
*       H   =   Height
*       P   =   Percentage
*       C   =   Custom 
*
* All of these take integers except Custom that takes an array of two integers - for width and height.
*
*       $objResize = new RVJ_ImageResize("myImage.png", "myThumb.png", "W", "400");
*       $objResize = new RVJ_ImageResize("myImage.jpg", "myThumb.jpg", "H", "150");
*       $objResize = new RVJ_ImageResize("myImage.gif", "myThumb.gif", "P", "50");
*       $objResize = new RVJ_ImageResize("myImage.png", "myThumb.png", "C", array("400", "300"));
*
* When resizing by width, height and percentage, the image will keep its original ratio. Custom will simply resizes the image to whatever values you want - without keeping the original ratio.
*
* The class can handle jpg, png and gif images.
*
* The class will always save the image that it resizes, however you can also have it display the image: 
*
*       $objResize->showImage($resize->im2);
*
* The class holds the original image in the variable "im" and the new image in "im2". Therefore the code above will show the newly created image.
*
* You can get information about the image by doing the following:
*
*       print_r($objResize->findResourceDetails($objResize->resOriginalImage));
*       print_r($objResize->findResourceDetails($objResize->resResizedImage)); 
*
* This will be useful if you wish to retrieve any details about the images.
*  
* By default the class will stop you from enlarging your images (or else they will look grainy) and if you want to do this you must turn off the protection mode by passing a 5th parameter  
*  
*      $objResize = new RVJ_ImageResize("myImage.gif", "myEnlargedImage.gif", "P", "200", false);  
*
*/
  
class RVJ_ImageResize {

   var $strOriginalImagePath;
   var $strResizedImagePath;
   var $arrOriginalDetails;
   var $arrResizedDetails;
   var $resOriginalImage; 
   var $resResizedImage;
   var $numQuality = 95;
   var $boolProtect = true;  
  
   /*  
   *  
   *   @Method:      __constructor  
   *   @Parameters:   5
   *   @Param-1:      strPath - String - The path to the image
   *   @Param-2:      strSavePath - String - The path to save the new image to 
   *   @Param-3:      strType - String - The type of resize you want to perform
   *   @Param-4:      value - Number/Array - The resize dimensions  
   *   @Param-5:      boolProect - Boolen - Protects the image so that it doesnt resize an image if its already smaller  
   *   @Param-6:      numQuality - Number - The quality of compression if output is a JPEG
   *   @Description:   Calls the RVJ_Pagination method so its php 4 compatible  
   *  
   */
  
   function __constructor($strPath, $strSavePath, $strType = "W", $value = "150", $boolProtect = true, $numQuality = 95){
      $this->RVJ_ImageResize($strPath, $strSavePath, $strType, $value); 
   }
  
   /*  
   *  
   *   @Method:      RVJ_ImageResize  
   *   @Parameters:   5
   *   @Param-1:      strPath - String - The path to the image
   *   @Param-2:      strSavePath - String - The path to save the new image to
   *   @Param-3:      strType - String - The type of resize you want to perform 
   *   @Param-4:      value - Number/Array - The resize dimensions  
   *   @Param-5:      boolProect - Boolen - Protects the image so that it doesnt resize an image if its already smaller 
   *   @Param-6:      numQuality - Number - The quality of compression if output is a JPEG 
   *   @Description:   Calls the RVJ_Pagination method so its php 4 compatible  
   *  
   */
  
   function RVJ_ImageResize($strPath, $strSavePath, $strType = "W", $value = "150", $boolProtect = true, $numQuality = 95){
      //save the image/path details
      $this->strOriginalImagePath = $strPath;
      $this->strResizedImagePath = $strSavePath; 
      $this->numQuality = $numQuality;
      $this->boolProtect = $boolProtect;  

      //get the image dimensions
      $this->arrOriginalDetails = getimagesize($this->strOriginalImagePath);
      $this->arrResizedDetails = $this->arrOriginalDetails;
  
      //create an image resouce to work with
      $this->resOriginalImage = $this->createImage($this->strOriginalImagePath);

      //select the image resize type
      switch(strtoupper($strType)){
         case "P":
            $this->resizeToPercent($value);
            break;
         case "H":
            $this->resizeToHeight($value);
            break;
         case "C":
            $this->resizeToCustom($value);
            break;
         case "W":
         default:
            $this->resizeToWidth($value); 
            break;
      }  
   }
  
   /*  
   *  
   *   @Method:      findResourceDetails  
   *   @Parameters:   1  
   *   @Param-1:      resImage - Resource - The image resource you want details on  
   *   @Description:   Returns an array of details about the resource identifier that you pass it  
   *  
   */ 
  
   function findResourceDetails($resImage){
      //check to see what image is being requested
      if($resImage==$this->resResizedImage){                              
         //return new image details
         return $this->arrResizedDetails;
      }else{
         //return original image details
         return $this->arrOriginalDetails;
      }
   }
  
   /*  
   *  
   *   @Method:      updateNewDetails     
   *   @Parameters:   0  
   *   @Description:   Updates the width and height values of the resized details array  
   *  
   */ 
  
   function updateNewDetails(){
      $this->arrResizedDetails[0] = imagesx($this->resResizedImage);
      $this->arrResizedDetails[1] = imagesy($this->resResizedImage);
   }
  
   /*  
   *  
   *   @Method:      createImage  
   *   @Parameters:   1  
   *   @Param-1:      strImagePath - String - The path to the image  
   *   @Description:   Created an image resource of the image path passed to it  
   *  
   */ 

   function createImage($strImagePath){
      //get the image details
      $arrDetails = $this->findResourceDetails($strImagePath);
        
      //choose the correct function for the image type  
      switch($arrDetails['mime']){
         case "image/jpeg":
            return imagecreatefromjpeg($strImagePath);
            break;
         case "image/png":
            return imagecreatefrompng($strImagePath);
            break;
         case "image/gif":
            return imagecreatefromgif($strImagePath);
            break;
      }
   } 
  
   /*  
   *  
   *   @Method:      saveImage  
   *   @Parameters:   1  
   *   @Param-1:      numQuality - Number - The quality to save the image at  
   *   @Description:   Saves the resize image  
   *  
   */ 

   function saveImage($numQuality = 95){
      switch($this->arrResizedDetails['mime']){
         case "image/jpeg":
            imagejpeg($this->resResizedImage, $this->strResizedImagePath, $numQuality);
            break;
         case "image/png":
            // imagepng = [0-9] (not [0-100])           
            imagepng($this->resResizedImage, $this->strResizedImagePath, 7);
            break;
         case "image/gif":
            imagegif($this->resResizedImage, $this->strResizedImagePath); 
            break;
      }
   }
  
   /*  
   *  
   *   @Method:      showImage  
   *   @Parameters:   1  
   *   @Param-1:      resImage - Resource - The resource of the image you want to display  
   *   @Description:   Displays the image resouce on the screen  
   *  
   */ 
  
   function showImage($resImage){
      //get the image details
      $arrDetails = $this->findResourceDetails($resImage);
        
      //set the correct header for the image we are displaying  
      header("Content-type: ".$arrDetails['mime']);
      switch($arrDetails['mime']){
         case "image/jpeg":
            return imagejpeg($resImage);
            break;
         case "image/png":
            return imagepng($resImage);
            break;
         case "image/gif":
            return imagegif($resImage); 
            break;
      }
   }
  
   /*  
   *  
   *   @Method:      destroyImage  
   *   @Parameters:   1  
   *   @Param-1:      resImage - Resource - The image resource you want to destroy  
   *   @Description:   Destroys the image resource and so cleans things up  
   *  
   */ 

   function destroyImage($resImage){
      imagedestroy($resImage);
   }
  
   /*  
   *  
   *   @Method:      _resize  
   *   @Parameters:   2  
   *   @Param-1:      numWidth - Number - The width of the image in pixels  
   *   @Param-2:      numHeight - Number - The height of the image in pixes  
   *   @Param-3:      numQuality - Number - The quality of compression if output is a JPEG
   *   @Description:   Resizes the image by creatin a new canvas and copying the image over onto it. DONT CALL THIS METHOD DIRECTLY - USE THE METHODS BELOW  
   *  
   */ 

   function _resize($numWidth, $numHeight, $numQuality=95){
      //check for image protection  
      if($this->_imageProtect($numWidth, $numHeight)){     
         if($this->arrOriginalDetails['mime']=="image/gif"){
            //GIF image
            $this->resResizedImage = imagecreate($numWidth, $numHeight);
         }else if($this->arrOriginalDetails['mime']=="image/jpeg"){
            //JPG image
            $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight);
         }else if($this->arrOriginalDetails['mime']=="image/png"){  
            //PNG image  
            $this->resResizedImage = imagecreatetruecolor($numWidth, $numHeight);  
            imagecolortransparent($this->resResizedImage, imagecolorallocate($this->resResizedImage, 0, 0, 0));  
            imagealphablending($this->resResizedImage, false);  
            imagesavealpha($this->resResizedImage, true);  
         }  
         //update the image size details  
         $this->updateNewDetails();  
         //do the actual image resize  
         if (function_exists('imagecopyresampled')) {
           imagecopyresampled($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $numWidth, $numHeight, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]); 
         } else {
           imagecopyresized($this->resResizedImage, $this->resOriginalImage, 0, 0, 0, 0, $numWidth, $numHeight, $this->arrOriginalDetails[0], $this->arrOriginalDetails[1]); 
         }
         //saves the image  
         $this->saveImage($numQuality);  
      }  
   }
  
   /*  
   *  
   *   @Method:      _imageProtect  
   *   @Parameters:   2  
   *   @Param-1:      numWidth - Number - The width of the image in pixels  
   *   @Param-2:      numHeight - Number - The height of the image in pixes  
   *   @Description:   Checks to see if we should allow the resize to take place or not depending on the size the image will be resized to  
   *  
   */     

   function _imageProtect($numWidth, $numHeight){  
      if($this->boolProtect AND ($numWidth > $this->arrOriginalDetails[0] OR $numHeight > $this->arrOriginalDetails[1])){  
         return 0;  
      }  
      return 1;  
   }  

   /*  
   *  
   *   @Method:      resizeToWidth  
   *   @Parameters:   1  
   *   @Param-1:      numWidth - Number - The width to resize to in pixels  
   *   @Description:   Works out the height value to go with the width value passed, then calls the resize method.  
   *  
   */
  
   function resizeToWidth($numWidth){ 
      $numHeight=(int)(($numWidth*$this->arrOriginalDetails[1])/$this->arrOriginalDetails[0]);
      $this->_resize($numWidth, $numHeight, $this->numQuality);   
   }
  
   /*  
   *  
   *   @Method:      resizeToHeight  
   *   @Parameters:   1  
   *   @Param-1:      numHeight - Number - The height to resize to in pixels  
   *   @Description:   Works out the width value to go with the height value passed, then calls the resize method.  
   *  
   */ 

   function resizeToHeight($numHeight){
      $numWidth=(int)(($numHeight*$this->arrOriginalDetails[0])/$this->arrOriginalDetails[1]);
      $this->_resize($numWidth, $numHeight);   
   }
  
   /*  
   *  
   *   @Method:      resizeToPercent  
   *   @Parameters:   1  
   *   @Param-1:      numPercent - Number - The percentage you want to resize to  
   *   @Description:   Works out the width and height value to go with the percent value passed, then calls the resize method.  
   *  
   */ 

   function resizeToPercent($numPercent){
      $numWidth = (int)(($this->arrOriginalDetails[0]/100)*$numPercent);
      $numHeight = (int)(($this->arrOriginalDetails[1]/100)*$numPercent);
      $this->_resize($numWidth, $numHeight);   
   }
  
   /*  
   *  
   *   @Method:      resizeToCustom  
   *   @Parameters:   1  
   *   @Param-1:      size - Number/Array - Either a number of array of numbers for the width and height in pixels  
   *   @Description:   Checks to see if array was passed and calls the resize method with the correct values.  
   *  
   */ 
  
   function resizeToCustom($size){
      if(!is_array($size)){
         $this->_resize((int)$size, (int)$size);
      }else{
         $this->_resize((int)$size[0], (int)$size[1]);
      }
   }
}
?>