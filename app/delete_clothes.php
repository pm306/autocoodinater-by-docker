<?php 
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('utils.php'); 
require_once('lib/functions.php');

$clothes_id = $_POST[POST_ID_KEY];
$clothes_details = getClothesDetailsById($clothes_id);

deleteClothesById($clothes_id);
deleteUploadedFile($clothes_details['picture']);
?>

<h1>削除しました</h1>
<img src="pictures/oosouji_gomidashi.png">
<form action="closet.php" name="returnForm" method="post">
    <input type="hidden" name="return" value="true">
    <a class="return" href="javascript:returnForm.submit()"><img src="pictures/navigationj_back.png" width="100" height="50"></a>
</form>

<?php require_once('footer.php') ?>