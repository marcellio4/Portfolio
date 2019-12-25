<?php

class File {
    
    /* Initialize our errors for further use plus other variables */
    private $upload_errors = array(
        UPLOAD_ERR_OK => 'No errors.',
        UPLOAD_ERR_INI_SIZE => 'Larger than upload_max_filesize.',
        UPLOAD_ERR_FORM_SIZE => 'Larger than MAX_FILE_SIZE.',
        UPLOAD_ERR_PARTIAL => 'Partial upload.',
        UPLOAD_ERR_NO_FILE => 'No file selected.',
        UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory.',
        UPLOAD_ERR_CANT_WRITE => 'Can\'t write to disk.',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.'
    );
    private $errors = array();
    private $tmpname;
    private $upfilename;
    private $type;
    private $size;
    private $updir;
    private $newname;
    
    /* check if we have any errors in our uploaded file and if not initialize our attributes */
    public function errorsCheck($file) {
        //Perform error checking on the form parameters
        if (!$file || empty($file) || !is_array($file)) {
            $this->errors[] = 'No file was uploaded.';
            return false;
        } elseif ($file['error'] != 0) {
            //error report what PHP says went wrong
            $this->errors[] = $this->upload_errors[$file['error']];
            return false;
        } else {
            //set object attributes to the form parameters
            $this->tmpname = $file['tmp_name'];
            $this->upfilename = basename($file['name']);
            $this->type = $file['type'];
            $this->size = $file['size'];
            return true;
        }
    }
    
    /* Save our image into directory  */
    public function saveImg() {
        //check if there are any errors if so we can't save the image.
        if (empty($this->errors)) {
            //check the file size if is not larger than 3MB if is do not continue
            if ($this->size <= 3000000) {
                //if no erros found then check if is jpeg or png
                if (substr($this->upfilename, -4) != (".jpg" || ".JPG" || ".png")) {
                    $this->errors[] = 'Only files ending with .jpeg or .png can be uploaded.';
                    return false;
                } else {
                    //if is jpeg than check the type of the file (image)
                    if ($this->type != "image/jpeg" && $this->type != "image/png") {
                        $this->errors[] = 'Only image files can be uploaded.';
                        return false;
                    } else {
                        if (is_uploaded_file($this->tmpname)) {
                            $this->updir = dirname(dirname(__FILE__)) . '/images/db_portfolio/';
                            $this->newname = $this->updir . $this->upfilename;
                            if (file_exists($this->newname)) {
                                $this->errors[] = 'File already exist';
                                return false;
                            } else if (move_uploaded_file($this->tmpname, $this->newname)) {
                                //File successfully uploaded write method create
                                if ($this->create()) {
                                    unset($this->tmpname);
                                    return true;
                                }
                            } else {
                                $this->errors[] = 'File upload failed';
                                return false;
                            }
                        } else {
                            $this->errors[] = 'No File Selected';
                            return false;
                        }
                    }
                }
            }
            $this->errors[] = 'The file size is too large. Only file under 1MB can be upload.';
            return false;
        }
        return false;
    }
}

