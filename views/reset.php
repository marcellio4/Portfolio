<?php
// auto load classes
spl_autoload_register('myAutoloader');

/*
-------------Import templates --------------
*/
$head = 'templates/header.html';
$body = 'templates/reset.html';
$foot = 'templates/footer.html';

//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);

if (isset($_POST['password'])) {
    /**
     * Validate the input from form
     * If any errors found redisplay errors again
     * Otherwise refresh page and display thank you message to user
     */
    
    $validation = new Login();
    $password = clean_str($_POST['password']);
    $confirmPassword = clean_str($_POST['confirmPass']);
    $user = clean_str($_POST['mail']);
    $query = "SELECT ID FROM user where email = ?";
    $data = DB::getInstance()->find($query, array($user));
    if (empty($data)) {
        $validation->addToErrorArr('mail', 'Incorrect user email.');
    }
    
    if (! $validation->same($password, $confirmPassword)) {
        $validation->addToErrorArr('confirmPass', 'Your confirm password must match with your password.');
    }
    $validation->password('password', $password);
    $validation->password('confirmPass', $confirmPassword);
    
    if (!$validation->isErrorsDetected()) {
        $id = $data[0]['ID'];
        $hash = $validation->hash($password);
        DB::getInstance()->save('user', $id, array('Password' => $hash));
        $_SESSION['resetPassword'] = '<p class="message">Thank you, your password has been successfully reset.</p>';
        die();
    }
    
    echo json_encode($validation->getErrorsArr());
    die();
}


$new_msg = '';

//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array(
    '[+button+]' => '',
    '[+modal+]' => ''
));

#calling out thank you message after register new member
if (isset($_SESSION['resetPassword'])) {
    $new_msg = $_SESSION['resetPassword'];
    unset($_SESSION['resetPassword']);
}

if (isset($_SESSION['resetError'])) {
    $new_msg = $_SESSION['resetError'];
    unset($_SESSION['resetError']);
}

//setting out our body
$final .= parseTemplate($tpl_b, array(
    '[+reset+]' => $new_msg
));

$final .= parseTemplate($tpl_c, array('[+date+]' => get_year(),
));

//display our template file with all placeholders
$content = $final;
echo $content;

