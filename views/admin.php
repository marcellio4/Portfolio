<?php
// auto load classes
spl_autoload_register('myAutoloader');
if (!isset($_SESSION['login'])) {
    redirectPage('index.php');
}

if (isset($_POST['story'])) {
    $story = clean_str($_POST['story']);
    $picture = $_FILES['picture'];
    $file = new File();
    $file->setFileProperties($picture);
    if (empty($file->getErrors())) {
        if ($file->checkSize('picture') && $file->isImage('picture') && $file->saveUploadImg('picture', 'images/db_portfolio/')) {
            DB::getInstance()->create('information', array('UserID' => $_SESSION['userID'], 'Story' => $story, 'Image' => $picture['name']));
        }
    }
    echo json_encode($file->getErrors());
    die();
}
/*
-------------Import templates --------------
*/
// set which templates to user_error
$head = 'templates/header.html';
$body = 'templates/admin.html';
$foot = 'templates/footer.html';
$logout = file_get_contents('templates/widgets/sign_out.html');

//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);

$tpl_logout = parseTemplate($logout, array('[+username+]' => $_SESSION['login']));
$button = $tpl_logout;
$modal = '';

//build up our header HTML with function text_body
$final = parseTemplate($tpl_a, array(
    '[+button+]' => $button,
    '[+modal+]' => $modal
));

$add = "<a href=\"#addStoryModal\" id=\"addStoryButton\" data-story=\"false\" class=\"btn btn-success\" data-toggle=\"modal\"><i
                                        class=\"material-icons\"></i> <span>Add New</span></a>";
$disable = "<button type=\"button\" class=\"btn btn-danger\" disabled>Add Danger</button>";
$condition = "where UserID = " . $_SESSION['userID'];
$final .= parseTemplate($tpl_b, array('[+story+]' => (empty(DB::getInstance()->findAll('User', $condition))) ? $add : $disable));
$final .= parseTemplate($tpl_c, array('[+date+]' => get_year()));

//display our template file with all placeholders
$content = $final;
echo $content;
