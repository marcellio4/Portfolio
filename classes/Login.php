<?php
/**
 * Class Login
 * Check if user exist in database
 * Destroy login sessions
 * Hash password and compare with password in database
 */

class Login extends Validation {
    
    /**
     * @param string $pass password
     * @return false|string|null
     */
    public function hash($pass) {
        return  password_hash($pass, PASSWORD_DEFAULT);
    }
    
    /**
     * @param string $pass password
     * @param string $hash
     * @return bool
     */
    public function verify($pass, $hash) {
        return password_verify($pass, $hash);
    }
    
    /**
     * @param string $name session name
     * @return bool
     */
    public static function logout($name) {
        if (isset($name)) { #delete session
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $yesterday = time() - (1 * 60 * 60);
                $params = session_get_cookie_params();
                setcookie(session_name(), '', $yesterday,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]);
            }
            session_destroy();
           return true;
        }
        return false;
    }
}