<?php
// auto load classes
spl_autoload_register('myAutoloader');

/*
-------------Import templates --------------
*/
// set which templates to user_error
$head = 'templates/header.html';
$body = 'templates/projects.html';
$foot = 'templates/footer.html';
$loginWidget = file_get_contents("templates/widgets/login_modal.html");
$login_button = file_get_contents("templates/widgets/sign_in.html");
$logout = file_get_contents('templates/widgets/sign_out.html');

//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);

if (isset($_SESSION['login'])) {
    $tpl_logout = parseTemplate($logout, array('[+username+]' => $_SESSION['login']));
    $button = $tpl_logout;
    $modal = '';
} else {
    $button = $login_button;
    $modal = $loginWidget;
}


//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array(
    '[+button+]' => $button,
    '[+modal+]' => $modal
));
$data = DB::getInstance()->findAll('projects', 'UserID = 2');
$content = '';
foreach ($data as $value) {
    $content .= "<div class='col-md-4'>
                        <div class='card mb-4 shadow-sm'>
                            <div class='card-body'>
                                <h5 class='card-title'>{$value['Name']}</h5>
                                <p class='card-text'>{$value['Description']}</p>
                                <a href='{$value['Link']}' target='_blank' class='btn btn-sm btn-outline-secondary'>View</a>
                            </div>
                        </div>
                   </div>";
}
$final .= parseTemplate($tpl_b, array(
    '[+content+]' => $content
));
$final .= parseTemplate($tpl_c, array('[+date+]' => get_year(),
));

//display our template file with all placeholders
echo $final;
