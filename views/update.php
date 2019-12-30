<?php
// auto load classes
spl_autoload_register('myAutoloader');
if(isset($_POST['name']) && $_POST['name'] === 'update'){
    try {
        $file = new File();
        $user = DB::getInstance()->find("select Firstname from user where ID = ?", array($_SESSION['userID']));
        $file->startCSV($user[0]['Firstname']);
        $query = "SELECT Language, Framework, Knowledge, Color from skills WHERE UserID = ? and Development = ? ORDER by Language";
        $Front = DB::getInstance()->find($query, array(2, 'Front-End'));
        $Back = DB::getInstance()->find($query, array(2, 'Back-End'));
        $database = DB::getInstance()->find($query, array(2, 'database'));
        $sysAdmin = DB::getInstance()->find($query, array(2, 'sysAdmin'));
        $cms = DB::getInstance()->find($query, array(2, 'cms'));
        $file->writeCSV($user[0]['Firstname'], 'Front-End', $Front);
        $file->writeCSV($user[0]['Firstname'], 'Back-End', $Back);
        $file->writeCSV($user[0]['Firstname'], 'Database', $database);
        $file->writeCSV($user[0]['Firstname'], 'sysAdmin', $sysAdmin);
        $file->writeCSV($user[0]['Firstname'], 'cms', $cms);
    } catch (Exception $e) {
        echo $e->getMessage();
        die();
    }
    
    die();
}