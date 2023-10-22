<?php 
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('clothes_type.php');
require_once('utils.php'); 

$clotheDetails = array();

if(isset($_POST[POST_PICTURE_ID_KEY])){
    $clotheDetails = fetchClotheDetails($_POST[POST_PICTURE_ID_KEY]);
}

$chothes_type = $clothes_type_tops[$clotheDetails['type']] ?? $clothes_type_bottoms[$clotheDetails['type']] ?? "";
$escaped_clothe_id = htmlspecialchars($clotheDetails['id'], ENT_QUOTES, 'UTF-8');
$escaped_clothe_picture = htmlspecialchars($clotheDetails['picture'], ENT_QUOTES, 'UTF-8');

?>
<p>種別：<?php echo $clothetype; ?>
<form method="post" name="deleteForm" action="delete_clothes.php">
    <input type="hidden" name="id" value="<?php echo $escaped_clothe_id ?>">
    <a href="javascript:deleteForm.submit()">データの削除(クリックすると消去されます)</a>
</form>

<hr>
<img id="detail" src="upload/<?php echo $escaped_clothe_picture?>">
<form action="closet.php" name="returnForm" method="post">
    <input type="hidden" name="return" value="true">
    <a class="return" href="javascript:returnForm.submit()"><img src="pictures/navigationj_back.png" width="100" height="50"></a>
</form>

<?php require_once('footer.php')?>
