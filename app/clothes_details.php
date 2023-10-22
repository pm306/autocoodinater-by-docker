<?php 
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('clothes_type.php');
require_once('utils.php'); 
require_once('lib/functions.php');

$clothes_details = array();

if(isset($_POST[POST_PICTURE_ID_KEY])){
    $clothes_details = fetchclothesDetails($_POST[POST_PICTURE_ID_KEY]);
}

$clothes_type = fetchClothesTypeNameByCode($clothes_details['type']);
$escaped_clothes_id = htmlspecialchars($clothes_details['id'], ENT_QUOTES, 'UTF-8');
$escaped_clothes_picture = htmlspecialchars($clothes_details['picture'], ENT_QUOTES, 'UTF-8');

?>
<p>種別：<?php echo $clothes_type; ?></p>
<form method="post" name="deleteForm" action="delete_clothes.php">
    <input type="hidden" name="id" value="<?php echo $escaped_clothes_id ?>">
    <a href="javascript:deleteForm.submit()">データの削除(クリックすると消去されます)</a>
</form>

<hr>
<img id="detail" src="upload/<?php echo $escaped_clothes_picture?>">
<form action="closet.php" name="returnForm" method="post">
    <input type="hidden" name="return" value="true">
    <a class="return" href="javascript:returnForm.submit()"><img src="pictures/navigationj_back.png" width="100" height="50"></a>
</form>

<?php require_once('footer.php')?>
