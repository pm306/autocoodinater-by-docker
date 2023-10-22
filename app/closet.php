<?php 
global $db, $clothes_type_tops, $clothes_type_bottoms;
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('utils.php'); 
require_once('lib/functions.php');

// 初期化とセッションからの情報取得
$fetched_clothes = array();
$user_email_address = $_SESSION[COLUMN_USER_EMAIL];
$_SESSION['checkbox'] = array();

if (!empty($_POST[POST_TYPE_KEY])) {
    foreach ($_POST[POST_TYPE_KEY] as $clothes_type) {
        $query = 'SELECT id, picture FROM clothes WHERE owner=? and type=?';
        // $sql = $db->prepare($query);
        // $sql->bindparam(1, $user_email_address);
        // $sql->bindparam(2, $clothes_type);    
        // $sql->execute();
        
        // while ($clothesData = $sql->fetch()) {
        //     $fetched_clothes[] = $clothesData;
        // }
        $results = executeQuery($query, array($user_email_address, $clothes_type));
        $fetched_clothes = array_merge($fetched_clothes, $results);
        $_SESSION['checkbox'][] = $clothes_type;
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
        <?php displayCheckboxes(fetchClothesTypes(), $_SESSION['checkbox']); ?>
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
if (!empty($fetched_clothes)):
    $image_index = 0;
    foreach ($fetched_clothes as $image_data):
        displayImageForm($image_data, $image_index);
        $image_index++;
        if ($image_index % IMAGES_PER_ROW == 0) {
            echo '<br>';
        }
    endforeach;
endif;
?>
<br>
        </form>
<?php require_once('footer.php')?>
