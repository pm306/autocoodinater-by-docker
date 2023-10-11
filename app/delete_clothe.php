<?php 
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('utils.php'); 

$id = $_POST[POST_ID_KEY];
$sql = $db->prepare(SELECT_CLOTHES_BY_ID_PICTURE);
$sql->bindparam(1, $id, PDO::PARAM_INT);
$sql->execute();
$res = $sql->fetch();

$sql = $db->prepare(DELETE_CLOTHES_BY_ID);
$sql->bindparam(1, $id, PDO::PARAM_INT);
$sql->execute();

$pass = UPLOAD_DIR;
$pass .= $res['picture'];
unlink($pass);
?>
<h1>削除しました</h1>
<img src="pictures/oosouji_gomidashi.png">
<form action="closet.php" name="re" method="post">
    <input type="hidden" name="return" value="true">
    <a class="return" href="javascript:re.submit()"><img src="pictures/navigationj_back.png" width="100" height="50"></a>
</form>

<?php require_once('footer.php') ?>
