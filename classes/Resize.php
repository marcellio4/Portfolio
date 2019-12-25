<?php

class Resize {
    
    private static $image;
    private $resizedImg;
    private $req_width;
    private $req_height;
    
    public function __construct($image, $resizedImg, $req_height, $req_width) {
        self::$image = $image;
        $this->resizedImg = $resizedImg;
        $this->req_height = $req_height;
        $this->req_width = $req_width;
    }
    
    public static function getImageDetails() {
        // Get image file details
        return self::getimagesize(self::$image);
    }
    
    public function createThumbnail() {
        // THE COMMENTS OUTLINE SOME OF THE REQUIRED STEPS
        $src = $this->createImage();
        if ($src === false) {
            return false;
        }
        // Check if image is smaller (in both directions) than required image
        // If so, use original image dimensions
        // Otherwise, Test orientation of image and set new dimensions appropriately
        // i.e. calculate the scale factor
        $width = self::getImageDetails()[0];
        $height = self::getImageDetails()[1];
        if ($width < $this->req_width && $height < $this->req_height) {
            $new_width = $width;
            $new_height = $height;
        } else {
            $new_width = $width * $this->req_width / $width;
            $new_height = $height * $this->req_height / $height;
        }
        
        // Create the new canvas ready for resampled image to be inserted into it
        $new = imagecreatetruecolor($new_width, $new_height);
        // Resample input image into newly created image
        imagecopyresampled($new, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        // Create output jpeg at quality level of 90
        imagejpeg($new, $this->resizedImg, 90);
        // Destroy any intermediate image files
        imagedestroy($src);
        imagedestroy($new);
        
        // Return a value indicating success or failure (true/false)
        return true;
    }
    
    public function createImage() {
        
        if (self::getImageDetails() === false) {
            return false;
        }
        
        // Check image type and use correct imagecreatefrom* function
        // Allow only gif, jpeg and png files
        switch (self::getImageDetails()[2]) {
            case  IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg(self::$image);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif(self::$image);
                break;
            case  IMAGETYPE_PNG:
                $src = imagecreatefrompng(self::$image);
                break;
            default:
                return false;
        }
        return $src;
    }
}

