<?php
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('clothes_type.php');
require_once('utils.php');  
require_once('functions.php');

$selected_tops = array();
$selected_bottoms = array();
$error_message = checkInputErrorTemperature();

if (isDesidedClothes()) {
    updateLastUsedDate();  
}


if (empty($error_message) && isset($_POST[POST_SELECT_KEY])) {
    require_once('select_clothes.php');
}
?>

<h1>オートコーディネータ</h1>
<p>ようこそ、<span style='font-weight: bold;'><?= htmlspecialchars($_SESSION[COLUMN_USER_NAME], ENT_QUOTES) ?></span>さん。
<a href="logout.php" style="margin-left: 20px;">ログアウト</a></p>

<?php if(isDesidedClothes()):?>
    <p id="after_msg">Have a nice day!</p>
    <img src='pictures/halloween_nekomajo.png'><br>
    <a href="index.php"><img src="pictures/navigationj_back.png" width="100" height="50"></a>
<?php else: ?>
    <form action="" method="post">
        <div class='alart'><?=$error_message;?></div>
        最高気温：<input type="number" name='<?=POST_TEMPERATURE_MAX_KEY?>' value="<?= $_POST[POST_TEMPERATURE_MAX_KEY]?>"><br>
        最低気温：<input type="number" name='<?=POST_TEMPERATURE_MIN_KEY?>' value="<?= $_POST[POST_TEMPERATURE_MIN_KEY]?>"><br>
        <input class= "coordinate" type="submit" name='<?= POST_SELECT_KEY ?>' value=
        <?php if(isset($_POST[POST_SELECT_KEY]) && empty($error_message)) echo '"もう一度！"'; else echo '" 選ぶ "'; ?>>
    </form>
<?php endif;?>

<?php if (!empty($selected_tops)): displayClothesImages($selected_tops); endif; ?>

<?php if (!empty($selected_bottoms)): displayClothesImages($selected_bottoms); endif; ?>

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
    <?php if(!empty($_POST) && empty($error_message) && !isDesidedClothes()):?>
    画像をクリックすると拡大できます<br>
    <input type="image" src="pictures/pop_kettei.png" alt="決定" width="120" height="60">
    <?php endif; ?>
</form>

<hr>
<div style="font-size: 110%;">▼衣服の管理▼</div>
<a href="closet.php"><img src="pictures/closet.png" class="closet"width="150" height="150" alt="衣服管理"></a>
<a href="explanation.php">このアプリについて</a><br>
<?php require_once('footer.php')?>