<?php
// auto load classes
spl_autoload_register('myAutoloader');

/**
 * check if our data has been submitted
 * Validate all data and if error occur send back errors
 * On success reload to the same page
 */

if (isset($_POST['mail']) && $_POST['mail'] !== ''){
    $username = clean_str($_POST['mail']);
    $password = clean_str($_POST['pass']);
    $login = new Login();
    $login->email('mail', $username);
    $condition = "email = '$username'";
    $data = DB::getInstance()->findAll("User", $condition);
    $hash = $data[0]['Password'];
    if(! $login->verify($password, $hash)){
        $login->addToErrorArr('pass', "Incorrect password");
    }
    
    if($login->isErrorsDetected()){
        echo json_encode($login->getErrorsArr());
        die();
    }
    $name = $data[0]['Title'] . " " . substr($data[0]['Firstname'], 0, 1) . " " . $data[0]['Surname'];
    $_SESSION['login'] = $name;
    $_SESSION['userID'] = $data[0]['ID'];
    die();
}