<?php
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('clothes_type.php');
require_once('utils.php');  
require_once('functions.php');

$selectd_tops = array();
$selected_bottoms = array();
$name = $_SESSION[COLUMN_USER_NAME];
$error_log = checkInputErrorTemperature();

if (isDesidedClothes()) {
    updateLastUsedDate();  
}

if (empty($error_log)) {
    require_once('select_clothes.php');
}

?>

<h1>オートコーディネータ</h1>
<p>ようこそ、<?php echo '<span style="font-weight: bold;">'.htmlspecialchars($name, ENT_QUOTES).'</span>';?>さん。
<a href="logout.php" style="mergin-left: 20px;">ログアウト</a></p>

<!---「決定」が押された場合はメッセージと画像を表示--->
<?php if(isDesidedClothes()):?>
<p id="after_msg">Have a nice day!</p>
<img src='pictures/halloween_nekomajo.png'><br>
<a href="index.php"><img src="pictures/navigationj_back.png" width="100" height="50"></a>
<!--- 入力フォーム--->
<?php else: ?>
<form action="" method="post">
    <div class='alart'><?php echo $error_log; ?></div>
    最高気温：<input type="number" name='<?=POST_TEMPERATURE_MAX_KEY?>' value="<?= $_POST[POST_TEMPERATURE_MAX_KEY]?>"><br>
    最低気温：<input type="number" name='<?=POST_TEMPERATURE_MIN_KEY?>' value="<?= $_POST[POST_TEMPERATURE_MIN_KEY]?>"><br>
    <input class= "coordinate" type="submit" name='<?= POST_SELECT_KEY ?>' value=
    <?php if($_POST[POST_SELECT_KEY] && empty($error_log)) echo '"もう一度！"'; else echo '" 選ぶ "'; ?>>
</form>
<?php endif;?>

<!---検索結果の表示--->
<?php if(!empty($selected_tops)):
    foreach($selected_tops as $top):?>
        <a href="upload/<?= $top['picture']?>" target="_blank"><img src="upload/<?= $top['picture']?>" width="230" height="230"></a>
    <?php endforeach;
endif; ?>

<?php if(!empty($selected_bottoms)):
    foreach($selected_bottoms as $bottom):?>
        <a href="upload/<?= $bottom['picture']?>" target="_blank"><img src="upload/<?= $bottom['picture']?>" width="250" height="250"></a>
    <?php endforeach;
endif;?>
<!---決定ボタン　表示されている服のidを配列に格納して送信する--->
<form action="" method="post"> 
    <?php if(!empty($selected_tops)):
        foreach($selected_tops as $top):
            //毎日洗濯しなくてもいい服は除外する
            if(array_search($top['type'], $not_laundly_everyday)!==false)continue;     
        ?>
        <input type="hidden" name="<?= POST_CLOTHE_ID_KEY ?>[]" value="<?php echo $top['id']?>">
    <?php endforeach;
        endif;?>
    <?php if(!empty($selected_bottoms)):   
        foreach($selected_bottoms as $bottom): ?>
        <input type="hidden" name="<?=POST_CLOTHE_ID_KEY?>[]" value="<?php echo $bottom['id']?>">
    <?php endforeach;
    endif;?>
    <br>
    <?php if(!empty($_POST) && empty($error_log) && !isDesidedClothes()):?>
    画像をクリックすると拡大できます<br>
    <input type="image" src="pictures/pop_kettei.png" alt="決定" width="120" height="60">
    <?php endif; ?>
</form>

<hr>
<div style="font-size: 110%;">▼管理ページへ▼</div>
<a href="closet.php"><img src="pictures/closet.png" class="closet"width="150" height="150" alt="衣服管理"></a>
<a href="explanation.php">このアプリについて</a><br>
<?php require_once('footer.php')?>