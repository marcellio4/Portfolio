<?php

/**
 * Class Resize
 */
class Resize {
    
    private static $image;
    private $path;
    private $req_width;
    private $req_height;
    
    /**
     * Resize constructor.
     * @param img $image
     * @param string $path where to save the image
     * @param int $req_height requested height
     * @param int $req_width requested width
     */
    public function __construct($image, $path, $req_height, $req_width) {
        self::$image = $image;
        $this->path = $path;
        $this->req_height = $req_height;
        $this->req_width = $req_width;
    }
    
    /**
     * @return mixed
     */
    public static function getImageDetails() {
        // Get image file details
        return self::getimagesize(self::$image);
    }
    
    /**
     * @return bool
     */
    public function createThumbnail() {
        // THE COMMENTS OUTLINE SOME OF THE REQUIRED STEPS
        $src = $this->createImage();
        if (! $src) {
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
        $img = imagejpeg($new, $this->path, 90);
        // Destroy any intermediate image files
        imagedestroy($src);
        imagedestroy($new);
        return $img;
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

