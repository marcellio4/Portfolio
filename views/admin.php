<?php
// auto load classes
spl_autoload_register('myAutoloader');
if (!isset($_SESSION['login'])) {
    redirectPage('index.php');
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
$disable = "<button type=\"button\" class=\"btn btn-danger\" disabled>Disable</button>";
$queryStory = "select ID, Story, Image from information where UserID = ?";
$dataStory = DB::getInstance()->find($queryStory, array($_SESSION['userID']));
$querySkills = "select ID, Development, Language, Framework, Knowledge from skills where UserID = ?";
$dataSkills = DB::getInstance()->find($querySkills, array($_SESSION['userID']));
$queryProjects = "select ID, Name, Description, Link from projects where UserID = ?";
$dataProjects = DB::getInstance()->find($queryProjects, array($_SESSION['userID']));
$msg = '';
if (isset($_SESSION['msg-success'])){
    $msg = $_SESSION['msg-success'];
    unset($_SESSION['msg-success']);
}

if (isset($_SESSION['msg-error'])){
    $msg = $_SESSION['msg-error'];
    unset($_SESSION['msg-error']);
}
$final .= parseTemplate($tpl_b, array(
    '[+msg+]' => $msg,
    '[+story+]' => (empty($dataStory)) ? $add : $disable,
    '[+storyRows+]' => produceRow(count($dataStory), $dataStory, '<tr>', 0, 'editStoryModal', 'deleteStoryModal'),
    '[+skillsRows+]' => produceRow(count($dataSkills), $dataSkills, '<tr>', 0, 'editSkillsModal', 'deleteSkillModal'),
    '[+projectRows+]' => produceRow(count($dataProjects), $dataProjects, '<tr>', 0, 'editProjectModal', 'deleteProjectModal')
));
$final .= parseTemplate($tpl_c, array('[+date+]' => get_year()));

//display our template file with all placeholders
$content = $final;
echo $content;
