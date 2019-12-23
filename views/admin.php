<?php
session_start();
session_regenerate_id(true);
require_once 'include/functions.php';
// auto load classes
spl_autoload_register('myAutoloader');

/*
-------------Import templates --------------
*/
// set which templates to user_error
$head = 'templates/header.html';
$body = 'templates/admin.html';
$foot = 'templates/footer.html';

//load the content of templates
$tpl_a = file_get_contents($head);
$tpl_b = file_get_contents($body);
$tpl_c = file_get_contents($foot);

// Check if the user is log in and if so store his name with log out link otherwise redirect back home if is not a user
if (isset($_SESSION['username'])) {
$name = $_SESSION['username'];
$logout = '<div class="logout">
						<a href="index.php?page=logout" name="Logout">Logout</a>
						<a href="index.php?page=admin" name="admin">Upload file</a>
					</div>';
}else{
	header('Location: index.php');	//If is unknown user then redirect beack to index for log in.
}


//build up our header HTML with function text_body
$final  = parseTemplate($tpl_a, array('[+page_title+]'  => 'PHP Web Developer front-end and back-end developer'));

//connect to database and get ready for uploading our files image
$db = new Config();
$db->db_connect();
$photo = new Images();
$form = new Admincheck();
$administrator = new Validation();

/* check if form has been submit and if so then save our picture in the folder of images and send it to database to store all neccesary information for further use plus display message of success or failure. */
$message = "";
$clean_title = "";
$clean_descr = "";
if(isset($_POST['singlefileupload'])){
	$photo->edit($_POST['title'], $_POST['description']);
	$title = $form->title_check(trim($_POST['title']));
	$descr = $form->description_check(trim($_POST['description']));

	if($title && $descr){
		$photo->attach_file($_FILES['userfile']);

		if($photo->save()) {
			$message = '<p class="message">Photo uploaded successfully.</p>';
		}else{
			$clean_title = $form->set_title($_POST['title']);
			$clean_descr = $form->set_description($_POST['description']);
			$message = '<p class="message">' . join('</br>',$photo->errors) . '</p>';
		}
	}else{
		$clean_title = $form->set_title($_POST['title']);
		$clean_descr = $form->set_description($_POST['description']);
		$message = '<p class="message">Title and description field cannot be empty.</p>';
	}
}

//close our database after we are done with uploading our files.
$db->db_close();

//continue to build up our body for the admin_page.
$final .= parseTemplate($tpl_b, array('[+header+]'      => 'ADMIN',
                                      '[+Actionlink+]'  => htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'),
																			'[+Welcome+]'     => '<h2>Welcome ' . $name . '</h2>',
                                      '[+title+]'       => $clean_title,
                                      '[+description+]' => $clean_descr,
                                      '[+message+]'     => $message
                                    ));

$final .= parseTemplate($tpl_c, array('[+date+]'       => get_year(),
																			'[+Logout+]'      => $logout,
																			));

//diplays our template file with all placeholders
$content = $final;
echo $content;

?>
