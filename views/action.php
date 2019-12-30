<?php
// auto load classes
spl_autoload_register('myAutoloader');
if (!isset($_SESSION['login'])) {
    redirectPage('index.php');
}
$validation = new Validation();

if (isset($_POST['story'])) {
    if ($_POST['action'] === 'delete') {
        $_SESSION['msg-error'] = '<p class="message-error">You can not delete story.</p>';
        die();
    }
    $story = htmlspecialchars($_POST['story']);
    $picture = $_FILES['picture'];
    $file = new File();
    $file->setFileProperties($picture);
    if (empty($file->getErrors())) {
        switch ($_POST['action']) {
            case 'add':
                if ($file->checkSize('picture') && $file->isImage('picture') && $file->saveUploadImg('picture', 'images/db_portfolio/')) {
                    DB::getInstance()->create('information', array(
                        'UserID' => $_SESSION['userID'],
                        'Story' => $story,
                        'Image' => $file->getImage()
                    ));
                }
                break;
            case 'edit':
                (int)$id = $_POST['id'];
                if (file_exists("images/db_portfolio/" . $file->getImage())) {
                    DB::getInstance()->save('information', $id, array(
                        'Story' => $story,
                    ));
                } else {
                    if ($file->checkSize('editPicture') && $file->isImage('editPicture') && $file->saveUploadImg('editPicture', 'images/db_portfolio/')) {
                        DB::getInstance()->save('information', $id, array(
                            'Story' => $story,
                            'Image' => $file->getImage()
                        ));
                    }
                }
                break;
            default:
                $_SESSION['msg-error'] = '<p class="message-error">Something went wrong please contact me.</p>';
                die();
                break;
        }
        if (empty($file->getErrors())) {
            $_SESSION['msg-success'] = '<p class="message">You successfully ' . $_POST['action'] . ' your story.</p>';
            die();
        }
    }
    echo json_encode($file->getErrors());
    die();
}

if (isset($_POST['skills'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        DB::getInstance()->delete('skills', $id);
        $value = DB::getInstance()->find("select max(ID) as value from projects", array());
        DB::getInstance()->find("ALTER TABLE projects AUTO_INCREMENT = " . $value[0]['value'], array());
        $_SESSION['msg-success'] = '<p class="message">You successfully deleted your skill.</p>';
        die();
    }
    $dev = clean_str($_POST['skills']['development']);
    $lang = clean_str($_POST['skills']['lang']);
    $framework = ($_POST['skills']['framework'] !== '') ? clean_str($_POST['skills']['framework']) : null;
    $knowledge = clean_str($_POST['skills']['knowledge']);
    $color = substr($_POST['skills']['color'], 1, 6);
    switch ($_POST['action']) {
        case 'add':
            $validation->isEmpty('development', $dev);
            $validation->isEmpty('lang', $lang);
            $validation->isEmpty('knowledge', $knowledge);
            if ($validation->hasWhitespace($lang) || !$validation->hasSpecialCharacters('', $lang)) {
                $validation->addToErrorArr('lang', 'Only one word can be used. example(PHP)');
            }
            if ($validation->isErrorsDetected()) {
                echo json_encode($validation->getErrorsArr());
                die();
            }
            DB::getInstance()->create('skills', array(
                'UserID' => $_SESSION['userID'],
                'Development' => $dev,
                'Language' => $lang,
                'Framework' => $framework,
                'Knowledge' => $knowledge,
                'Color' => $color
            ));
            break;
        case 'edit':
            $validation->isEmpty('editDevelopment', $dev);
            $validation->isEmpty('editLang', $lang);
            $validation->isEmpty('editKnowledge', $knowledge);
            if ($validation->hasWhitespace($lang) || !$validation->hasSpecialCharacters('', $lang)) {
                $validation->addToErrorArr('editLang', 'Only one word can be used. example(PHP)');
            }
            if ($validation->isErrorsDetected()) {
                echo json_encode($validation->getErrorsArr());
                die();
            }
            $id = $_POST['id'];
            DB::getInstance()->save('skills', $id, array(
                'Development' => $dev,
                'Language' => $lang,
                'Framework' => $framework,
                'Knowledge' => $knowledge,
                'Color' => $color
            ));
            break;
        default:
            $_SESSION['msg-error'] = '<p class="message-error">Something went wrong please contact me.</p>';
            die();
            break;
    }
    $_SESSION['msg-success'] = '<p class="message">You successfully ' . $_POST['action'] . ' your skill.</p>';
    die();
}

if (isset($_POST['projects'])) {
    if ($_POST['action'] === 'delete') {
        $id = $_POST['id'];
        DB::getInstance()->delete('projects', $id);
        $value = DB::getInstance()->find("select max(ID) as value from projects", array());
        DB::getInstance()->find("ALTER TABLE projects AUTO_INCREMENT = " . $value[0]['value'], array());
        $_SESSION['msg-success'] = '<p class="message">You successfully deleted your project.</p>';
        die();
    }
    $name = clean_str(nl2br($_POST['projects']['projectName']));
    $desc = clean_str($_POST['projects']['projectDesc']);
    switch ($_POST['action']) {
        case 'add':
            $validation->isEmpty('projectName', $name);
            $validation->isEmpty('projectDesc', $desc);
            if ($validation->isErrorsDetected()) {
                echo json_encode($validation->getErrorsArr());
                die();
            }
            DB::getInstance()->create('projects', array(
                'UserID' => $_SESSION['userID'],
                'Name' => $name,
                'Description' => $desc,
            ));
            break;
        case 'edit':
            $validation->isEmpty('editProjectName', $name);
            $validation->isEmpty('editProjectDesc', $desc);
            if ($validation->isErrorsDetected()) {
                echo json_encode($validation->getErrorsArr());
                die();
            }
            $id = $_POST['id'];
            DB::getInstance()->save('projects', $id, array(
                'Name' => $name,
                'Description' => $desc,
            ));
            break;
        default:
            $_SESSION['msg-error'] = '<p class="message-error">Something went wrong please contact me.</p>';
            die();
            break;
    }
    $_SESSION['msg-success'] = '<p class="message">You successfully ' . $_POST['action'] . ' your project.</p>';
    die();
}


