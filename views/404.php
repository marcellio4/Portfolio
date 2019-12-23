<?php
require_once 'include/functions.php';
/*
-------------Import templates --------------
*/
// set which templates to user_error
$head = 'templates/header.html';
$body = 'templates/404.html';
$foot = 'templates/footer.html';

//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);

//build up our header HTML with function text_body
$final  = parseTemplate($tpl_a, array('[+page_title+]' => 'PHP Web Developer front-end and back-end developer'));
$final .= parseTemplate($tpl_b, array('[+header+]' => 'Welcome to the Portfolio of Marcel Zacharias.'));
$final .= parseTemplate($tpl_c,array('[+date+]' => get_year()));

//diplays our template file with all placeholders
$content = $final;

echo $content;

?>
