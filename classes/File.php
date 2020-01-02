<?php

/**
 * Class File
 * @param array $upload_errors initialized errors
 * @param array $errors store errors on validation
 * @param string $tmpName temp file path
 * @param string $upFileName
 * @param string $type
 * @param int $size
 * @param string $newName
 */
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
    private $tmpName;
    private $baseName;
    private $image;
    private $type;
    private $size;
    private $newName;
    
    /**
     * check if we have any errors in our uploaded file and if not initialize our attributes
     * set tmpname $file['tmp_name']
     * set filename basename($file['name'])
     * set type $file['type']
     * set size $file['size']
     * @param file $file
     * @return bool
     */
    public function setFileProperties($file) {
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
            $this->image = $file['name'];
            $this->tmpName = $file['tmp_name'];
            $this->baseName = basename($file['name']);
            $this->type = $file['type'];
            $this->size = $file['size'];
            return true;
        }
    }
    
    /**
     * Save our image into directory
     * @param string $key name of the key in array
     * @param string $folder path to folder
     * @return bool
     */
    public function saveUploadImg($key, $folder) {
        if (is_uploaded_file($this->tmpName)) {
            $this->newName = $folder . $this->image;
            if (file_exists($this->newName)) {
                $this->errors[$key] = 'File already exist';
                return false;
            } else if (move_uploaded_file($this->tmpName, $this->newName)) {
                //File successfully uploaded write method create
                unset($this->tmpname);
                return true;
            } else {
                $this->errors[$key] = 'File upload failed';
                return false;
            }
        }
        $this->errors[$key] = 'No File Selected';
        return false;
    }
    
    /**
     * check if is jpeg or png
     * if is jpeg than check the type of the file (image)
     * @param string $key name of the key in array
     * @return bool
     */
    public function isImage($key) {
        if (substr($this->baseName, -4) != (".jpg" || ".JPG" || ".png")) {
            $this->errors[$key] = 'Only files ending with .jpeg or .png can be uploaded.';
            return false;
        }
        
        if ($this->type != "image/jpeg" && $this->type != "image/png") {
            $this->errors[$key] = 'Only image files can be uploaded.';
            return false;
        }
        return true;
    }
    
    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * check the file size if is not larger than 1MB if is do not continue
     * @param string $key name of the key in array
     * @return bool
     */
    public function checkSize($key) {
        if ($this->size <= 1000000) {
            return true;
        }
        $this->errors[$key] = 'The file size is too large. Only file under 1MB can be upload.';
        return false;
    }
    
    /**
     * @return mixed
     */
    public function getImage() {
        return $this->image;
    }
    
    /**
     * @param string $user
     * @throws Exception
     */
    public function startCSV($user) {
        $handle = fopen('media/skills.csv', 'w');
        if ($handle === false) {
            throw new Exception('Could not open skills data file.');
        }
        fputcsv($handle, array('id', 'value', 'color'));
        fputcsv($handle, array($user));
        fclose($handle);
    }
    
    /**
     * @param string $user
     * @param string $development
     * @param array $data
     * @throws Exception
     */
    public function writeCSV($user, $development, $data) {
        if (empty($data)){
            return;
        }
        $handle = fopen('media/skills.csv', 'a');
        if ($handle === false) {
            throw new Exception('Could not open the file.');
        }
        fputcsv($handle, array("$user.$development"));
        $flag = true;
        $tempVal = '';
        foreach ($data as $key => $value) {
            if (!empty($value['Framework'])) {
                if($tempVal !== $value['Language']){
                   $flag = true;
                }
                if ($flag) {
                    fputcsv($handle, array("$user.$development." . html_entity_decode($value['Language'])));
                    $flag = false;
                    $tempVal = $value['Language'];
                }
                fputcsv($handle, array("$user.$development." . html_entity_decode($value['Language']) . "." . html_entity_decode($value['Framework']), $value['Knowledge'], "#" . $value['Color']));
                continue;
            }
            fputcsv($handle, array("$user.$development." . html_entity_decode($value['Language']), html_entity_decode($value['Knowledge']), "#" . $value['Color']));
        }
        fclose($handle);
    }
}

