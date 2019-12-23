<?php

class Resize{

	private $in_img_file;
	private $out_img_file;
	private $req_width;
	private $req_height;

	public function img_resize($in_img_file, $out_img_file, $req_width, $req_height) {
    // THE COMMENTS OUTLINE SOME OF THE REQUIRED STEPS
    // Get image file details
		$details = getimagesize($in_img_file);

    // Check image type and use correct imagecreatefrom* function
    // Allow only gif, jpeg and png files
		if ($details !== false){
			switch ($details[2]){
			case  IMAGETYPE_JPEG:
				$src = imagecreatefromjpeg($this->in_img_file);
				break;
			case IMAGETYPE_GIF:
				$src = imagecreatefromgif($this->in_img_file);
				break;
			case  IMAGETYPE_PNG:
				$src = imagecreatefrompng($this->in_img_file);
				break;
			}

    // Check if image is smaller (in both directions) than required image
    // If so, use original image dimensions
    // Otherwise, Test orientation of image and set new dimensions appropriately
    // i.e. calculate the scale factor
			$this->width = $details[0];
			$this->height = $details[1];
			if ($this->width < $this->req_width && $this->height < $this->req_height){
				$this->new_width = $this->width;
				$this->new_height = $this->height;
			} else {
				$this->new_width = $this->width * $this->req_width / $this->width;
				$this->new_height = $this->height * $this->req_height / $this->height;

    // Create the new canvas ready for resampled image to be inserted into it
				$this->new = imagecreatetruecolor($this->new_width, $this->new_height);
    // Resample input image into newly created image
				imagecopyresampled($this->new, $this->src, 0, 0, 0, 0, $this->new_width, $this->new_height, $this->width, $this->height);
    // Create output jpeg at quality level of 90
				imagejpeg($this->new, $this->out_img_file, 90);
    // Destroy any intermediate image files
				imagedestroy($this->src);
				imagedestroy($this->new);
				}

    // Return a value indicating success or failure (true/false)
		return true;
		} else {
			return false;
		}
	}
}

?>
