<?php
// auto load classes
spl_autoload_register('myAutoloader');

if(Login::logout($_SESSION['login'])){
    redirectPage("index.php");
}
echo "Something went wrong. Please contact me <a href=\"index.php?page=contact\">here</a>";