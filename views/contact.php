<?php
// auto load classes
spl_autoload_register('myAutoloader');

/*
-------------Import templates --------------
*/
$head = 'templates/header.html';
$body = 'templates/contact.html';
$foot = 'templates/footer.html';
$mail = 'templates/email.html';
$loginWidget = file_get_contents("templates/widgets/login_modal.html");
$login_button = file_get_contents("templates/widgets/sign_in.html");
$logout = file_get_contents('templates/widgets/sign_out.html');

//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);
$tpl_mail = file_get_contents($mail);

if(isset($_POST['submit'])){
    /**
     * Validate the input from form
     * If any errors found redisplay errors again
     * Otherwise refresh page and display thank you message to user
     */
    
    $validation = new Validation();
    $title = clean_str($_POST['title']);
    $fname = clean_str($_POST['fname']);
    $sname = clean_str($_POST['sname']);
    $email = clean_str($_POST['email']);
    $subject = clean_str($_POST['sub']);
    $message = clean_str($_POST['msg']);
    
    $validation->Titles('title', $title);
    $validation->isAlphabetical('fname', $fname);
    $validation->isAlphabetical('sname', $sname);
    $validation->email('email', $email);
    $validation->isAlphabetical('sub', $subject);
    $validation->isEmpty('msg', $message);
    
    if ($validation->same($fname, $sname)){
        $validation->addToErrorArr('sname', 'Your first name can not be the same as your surname.');
    }
    
    if(! $validation->isErrorsDetected()){
        $phpMailer = new Mail();
        $email_body = parseTemplate($tpl_mail, array(
            '[+name+]' => $fname . " " . $sname,
            '[+message+]' => $message,
            '[+year+]' => get_year()
        ));
        try {
            $phpMailer->sendMail($email,"mzacharias@mzacharias.co.uk" , $subject, $email_body);
        } catch (\Exception $e){
            $_SESSION['msg_Error'] = "<p class='message-error'>" . $e->getMessage() . "</p>";
            die();
        }
        
        $_SESSION['message'] = '<p class="message">Thank you, your message has been successfully send.</p>';
        die();
    }
    
    echo json_encode($validation->getErrorsArr());
    die();
}


$new_msg = '';
if (isset($_SESSION['login'])){
    $tpl_logout = parseTemplate($logout, array('[+username+]' => $_SESSION['login']));
    $button = $tpl_logout;
    $modal = '';
}else{
    $button = $login_button;
    $modal = $loginWidget;
}


//build up our header HTML with function text_body
$final  = parseTemplate($tpl_a, array(
    '[+button+]' => $button,
    '[+modal+]' => $modal
));

#calling out thank you message after register new member
if (isset($_SESSION['message'])){
    $new_msg = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['msg_Error'])){
    $new_msg = $_SESSION['msg_Error'];
    unset($_SESSION['msg_Error']);
}

//setting out our body
$final .= parseTemplate($tpl_b, array(
    '[+confirm_message+]' => $new_msg
));

$final .= parseTemplate($tpl_c,array('[+date+]' => get_year(),
));

//display our template file with all placeholders
$content = $final;
echo $content;
