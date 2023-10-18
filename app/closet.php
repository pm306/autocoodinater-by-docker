<?php 
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('clothes_type.php');
require_once('utils.php'); 
require_once('functions.php');

// 初期化とセッションからの情報取得
$fetchedClothes = array();
$userName = $_SESSION[COLUMN_USER_NAME];
$_SESSION['checkbox'] = array();

if (!empty($_POST[POST_TYPE_KEY])) {
    foreach ($_POST[POST_TYPE_KEY] as $clothesType) {
        $sql = $db->prepare(SELECT_CLOTHES_BY_OWNER_AND_TYPE);
        $sql->bindparam(1, $userName, PDO::PARAM_STR);
        $sql->bindparam(2, $clothesType, PDO::PARAM_STR);    
        $sql->execute();
        
        while ($clothesData = $sql->fetch()) {
            $fetchedClothes[] = $clothesData;
        }
        
        $_SESSION['checkbox'][] = $clothesType;
    }
}
?>

<h1>管理ページ</h1>
<p>ここでは服の登録、検索、削除を行うことができます。<br>
画像をクリックすると拡大できます。</p>
<a href="regist_clothe.php" class="add">●服を追加する</a><br>
<a href="index.php"><img src="pictures/navigationj_back.png" width="100" height="50" style="margin-bottom: 20px;"></a>
<div style="font-size: 125%">【検索】</div>

<!---検索フォーム--->
<form id="search" name="form" action="" method="post">
    <ul>
        <?php displayCheckboxes($clothes_type_tops, $_SESSION['checkbox']); ?>
        <?php displayCheckboxes($clothes_type_bottoms, $_SESSION['checkbox']); ?>
    </ul>
    <input id="view" type="submit" value="表示" style="float:left;">
</form>

<!---一度に全チェックを入れる/外すボタン--->
<form action="" method="post" onsubmit="return false;">
    <button class="allcheck" type="button" onClick="setAllCheckboxes(true);" >すべて選択</button>
    <button class="allcheck" type="button" onClick="setAllCheckboxes(false);">チェックを外す</button>
</form>

<hr>
<!---画像を表示する--->
<?php
if (!empty($fetchedClothes)):
    $imageIndex = 0;
    foreach ($fetchedClothes as $imageData):
        displayImageForm($imageData, $imageIndex);
        $imageIndex++;
        if ($imageIndex % IMAGES_PER_ROW == 0) {
            echo '<br>';
        }
    endforeach;
endif;
?>
<br>
        </form>
<?php require_once('footer.php')?>