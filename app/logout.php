<?php
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once("utils.php");


setcookie(COOKIE_NAME_KEY, "", time()-TIME_FOR_DELETE_COOKIE);
$_SESSION = array();

session_destroy();
?>

<h1>ログアウトしました。</h1>
<img src="pictures/haloween_cat.png" width=200><br>
<a href="login.php"><img src="pictures/navigationj_back.png" width="100" height="50"></a>

<?php require_once('footer.php'); ?>