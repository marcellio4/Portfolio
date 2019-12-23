<?php
/**
 * Class Config set configuration for the website
 * @param array $DB holds setting connection for database in array
 */
class Config{
	private static $DB;
	private static $mail;

	public function __construct(){

	}
    
    /**
     * @param $name
     * @return array database settings
     */
	public static function setDB($name){
		self::$DB = array(
		    "host" => "",
            "name" => "",
            "user" => "",
            "password" => ""
        );
		return self::$DB[$name];
	}
    
    /**
     * @param string $name name for setting
     * @return mixed data of mail setting
     */
    public static function Mail($name){
        self::$mail = array(
            "host" => "",
            "username" => "",
            "password" => "",
            "SMTPAuth" => true,
            "port" => "",
            "debug" => 0 //SMTP::DEBUG_SERVER;
        );
        return self::$mail[$name];
    }
}
