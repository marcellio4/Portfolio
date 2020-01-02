<?php
// auto load classes
spl_autoload_register('myAutoloader');
if (!isset($_SESSION['login'])) {
    redirectPage('index.php');
}
$validation = new Validation();

if(isset($_POST['modalEdit'])){
    $query = "select Development, Language, Framework, Knowledge, Color from skills where UserID = ? and ID = ?";
    $data = DB::getInstance()->find($query, array($_SESSION['userID'], $_POST['modalEdit']));
    echo json_encode($data[0]);
    die();
}

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
            $validation->hasSpecialCharacters('lang', $lang, true);
            $query = "SELECT count(Language) as count, Framework
                        from skills
                        WHERE UserID = ? and Language = ? and (Framework is null or Framework is not null)";
            $existLang = DB::getInstance()->find($query, array($_SESSION['userID'], $lang));
            if ($existLang[0]['count'] > 0 && !$validation->isEmpty('framework', $framework)) {
                $validation->addToErrorArr('framework', 'Language already exist please add framework.');
            }
            if ($existLang[0]['count'] > 0 && $existLang[0]['Framework'] === null) {
                $validation->addToErrorArr('lang', 'Language exist. Before you add new skill edit the existing language and add framework to it.');
            }
            if (isset($framework)) {
                $validation->hasSpecialCharacters('framework', $framework, true);
                $frameworkQuery = "Select Framework from skills where UserID = ? and Language = ? and Framework = ?";
                $existFramework = DB::getInstance()->find($frameworkQuery, array($_SESSION['userID'], $lang, $framework));
                if (! empty($existFramework) && $validation->same($framework, $existFramework[0]['Framework'])) {
                    $validation->addToErrorArr('framework', 'This framework already exist.');
                }
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
            $validation->hasSpecialCharacters('editLang', $lang, true);
            if (isset($framework)) {
                $validation->hasSpecialCharacters('editFramework', $framework, true);
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
    $link = $_POST['projects']['url'];
    switch ($_POST['action']) {
        case 'add':
            $validation->isEmpty('projectName', $name);
            $validation->isEmpty('projectDesc', $desc);
            $validation->isEmpty('url', $link);
            $validation->url('url',$link);
            if ($validation->isErrorsDetected()) {
                echo json_encode($validation->getErrorsArr());
                die();
            }
            DB::getInstance()->create('projects', array(
                'UserID' => $_SESSION['userID'],
                'Name' => $name,
                'Description' => $desc,
                'Link' => $link
            ));
            break;
        case 'edit':
            $validation->isEmpty('editProjectName', $name);
            $validation->isEmpty('editProjectDesc', $desc);
            $validation->isEmpty('url', $link);
            $validation->url('editUrl',$link);
            if ($validation->isErrorsDetected()) {
                echo json_encode($validation->getErrorsArr());
                die();
            }
            $id = $_POST['id'];
            DB::getInstance()->save('projects', $id, array(
                'Name' => $name,
                'Description' => $desc,
                'Link' => $link
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


