<?php
// get page id and display the correct page
function pages($id_page) {
    
    switch ($id_page) {
        case 'home'     :
            return include 'views/home.php';
            break;
        case 'skills'    :
            return include 'views/skills.php';
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
        case 'action':
            return include 'views/action.php';
            break;
        default         :
            return include 'views/404.php';
    }
}

//define the autoload function
function myAutoloader($class) {
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
    $pass = preg_replace('/[*]/', '', $pass, 1);
    $content .= $pass;
    return $content;
}

//update our year of each year
function get_year() {
    date_default_timezone_set('UTC');
    return date('Y');
}

/**
 * @param string $str any string to clear
 * @return string sanitized string
 */
function clean_str($str) {
    $trimmed = trim($str);
    return htmlentities($trimmed, ENT_QUOTES, 'UTF-8');
}

/**
 * @return string clean url
 */
function sanitizedURL() {
    return htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
}

/**
 * @param string $page page to redirect example(index.php)
 */
function redirectPage($page) {
    header("Location: $page");
}

/**
 * Generate html row of data
 * @param int $count length of array data
 * @param array $arr our data
 * @param string $data start html tag
 * @param int $start we start from 0
 * @param string $editID id for edit modal
 * @param string $deleteID id for delete modal
 * @return string
 */
function produceRow($count, $arr, $data, $start, $editID, $deleteID) {
    if ($count === 0) {
        return $data;
    }
    $i = $start;
    foreach ($arr[$i] as $node => $val) {
        if (is_array($val)) {
            return produceRow($count, $val, $data, 0, $editID, $deleteID);
        }
        if ($node === 'ID'){
            $id = $val;
            $data .= "<td>" . ($i + 1) . "</td>";
            continue;
        }
        $data .= "<td>$val</td>";
    }
    $i++;
    $data .= "<td>
                <a href=\"#$editID\" data-toggle=\"modal\" class=\"edit\" data-id=\"$id\"><i
                class=\"fas fa-edit\" data-toggle=\"tooltip\" data-placement=\"top\"
                title=\"edit\"></i></a>
                <a href=\"#$deleteID\" data-toggle=\"modal\" class=\"delete\" data-id=\"$id\"><i
                class=\"fas fa-trash\" data-toggle=\"tooltip\" data-placement=\"top\"
                title=\"delete\"></i></a>
            </td>";
    $data .= '</tr>';
    return produceRow($count - 1, $arr, $data, $i, $editID, $deleteID);
}
