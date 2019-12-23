<?php
// get page id and display the correct page
function pages($id_page){

		switch ($id_page) {
			case 'home'     :
				return include 'views/home.php';
				break;
			case 'about'    :
				return include 'views/about.php';
				break;
			case 'projects' :
				return include 'views/projects.php';
				break;
			case 'contact'  :
				return include 'views/contact.php';
				break;
			case 'admin'    :
		 		return include 'views/admin.php';
				break;
			case 'login'    :
				return include 'views/login.php';
				break;
			case 'logout'    :
				return include 'views/logout.php';
				break;
            case 'reset':
                return include 'views/reset.php';
                break;
			default         :
				return include 'views/404.php';
		}
}

//define the autoload function
function myAutoloader($class){
	//constract path to the class file
	include 'classes/' . $class . '.php';
}

/* replace our placeholders for the original value from our templates */
function parseTemplate($tpl, $placeholders) {
    $pass = $tpl;
    $content = '';
    foreach ($placeholders as $key => $val) {
        $pass = str_replace($key, $val, $pass);
    }
    // Remove any missed/misspelled tags
    $pass = preg_replace('/[*]/','', $pass, 1);
    $content .= $pass;
    return $content;
}

//update our year of each year
function get_year(){
	date_default_timezone_set('UTC');
	return date('Y');
}

/**
 * @param  string $str any string to clear
 * @return string sanitized string
 */
function clean_str($str){
    $trimmed = trim($str);
    return htmlentities($trimmed, ENT_QUOTES, 'UTF-8');
}

/**
 * @return string clean url
 */
function sanitizedURL(){
    return htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
}

/**
 * @param string $page page to redirect
 */
function redirectPage($page){
    header("Location: $page");
}
