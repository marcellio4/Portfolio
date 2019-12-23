<?php

/**
 * Class Validation
 * Validate any data for reuse or to be stored in database
 */
class Validation {
    private $errors_detected = false;
    protected $errors_arr = array();
    private $titles = array('Mr', 'Mrs', 'Miss', 'Ms');
    
    /**
     * @return bool
     */
    public function isErrorsDetected() {
        return $this->errors_detected;
    }
    
    /**
     * @return array
     */
    public function getErrorsArr() {
        return $this->errors_arr;
    }
    
    /**
     * Check if choosen title is valid (exist in our array)
     * @param string $key set for errors array key as name of the validated field
     * @param string $str name of the validated field
     * @return bool
     */
    public function Titles($key, $str) {
        if (in_array($str, $this->titles)){
            return true;
        }
        $this->errors_detected = true;
        $this->errors_arr[$key] = 'Please choose your title.';
        return false;
    }
    
    /**
     * @param string $key set for errors array key as name of the validated field
     * @param string $str name of the validated field
     * @return bool
     */
    public function isAlphabetical($key, $str){
        // Must start with letter and whole string is alphabetical
        if (ctype_alpha(substr($str,0,1)) && ! $this->hasDigit($str) && strlen($str) > 2 && $this->hasSpecialCharacters($key, $str)){
            return true;
        }
        $this->errors_detected = true;
        $this->errors_arr[$key] = 'Your input must be longer then 2 characters and must contain only letters.';
        return false;
    }
    
    /**
     * Must contain letters, numbers, length between 6-12 and non special characters
     * @param string $key set for errors array key as name of the validated field
     * @param string $str password
     * @return bool
     */
    public function password($key, $str) {
        if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $str) && ! $this->hasWhitespace($str) && $this->checkLength($str, 6, 12) && $this->hasSpecialCharacters($key, $str)){
            return true;
        }
        $this->errors_detected = true;
        $this->errors_arr[$key] = 'Your password must be 6-12 characters long, contain letters and numbers, and must not contain spaces or special characters.';
        return false;
    }
    
    /**
     *
     * @param string|int $data any data for check of the length
     * @param int $min set minimum
     * @param int $max set maximum
     * @return bool
     */
    public function checkLength($data,$min,$max){
        if(strlen($data)>=$min && strlen($data)<=$max){
            return true;
        }
        return false;
    }
    
    /**
     * @param string $key set for errors array key as name of the validated field
     * @param string $msg error message to display
     */
    public function addToErrorArr($key, $msg){
        $this->errors_detected = true;
        $this->errors_arr[$key] = $msg;
    }
    
    /**
     * @param string $key set for errors array key as name of the validated field
     * @param string $mail name of the validated field
     * @return bool
     */
    public function email($key,$mail){
        if (filter_var($mail,FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        $this->errors_detected = true;
        $this->errors_arr[$key] = 'Email is invalid.';
        return false;
    }
    
    /**
     * @param string $key set for errors array key as name of the validated field
     * @param string $str name of the validated field
     * @param bool $add add into error array for displaying message
     * @return bool
     */
    public function hasSpecialCharacters($key, $str, $add = false) {
        if(! preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $str)){
            return true;
        }
        if ($add) {
            $this->errors_arr[$key] = 'Please do not use special characters such $%*~£ etc.';
        }
        $this->errors_detected = true;
        return false;
    }
    
    /**
     * @param string $key set for errors array key as name of the validated field
     * @param string $str name of the validated field
     * @return bool
     */
    public function isEmpty($key,$str) {
        if(isset($str) && $str !== ''){
            return true;
        }
        $this->errors_detected = true;
        $this->errors_arr[$key] = 'This field cannot be empty.';
        return false;
    }
    
    /**
     * @param string|int $input any data to check if has a digit
     * @return bool
     */
    public function hasDigit($input) {
        return (preg_match('/(\d)/mx', $input));
    }
    
    /**
     * Compare 2 values if they are the same
     * @param mixed $str1
     * @param mixed $str2
     * @return bool
     */
    public function same($str1, $str2) {
        return ($str1 === $str2);
    }
    
    /**
     * @param string $str
     * @return false|int
     */
    public function hasWhitespace($str){
        return (preg_match('/\s/', $str));
    }
    
}
