<?php
session_start();
require_once('logincheck.php');
require_once('header.php');
require_once('dbconnect.php');
require_once('clothes_type.php');
require_once('utils.php');
require_once('lib/functions.php');

$selected_tops    = array();
$selected_bottoms = array();
$error_message    = checkInputErrorTemperature();

if (isDecidedClothes()) {
    updateLastUsedDate();
}

if (empty($error_message) && isset($_POST[POST_SELECT_KEY])) {
    require_once('select_clothes.php');
}

$top_inputs    = filterLaundryClothes($selected_tops);
$bottom_inputs = filterLaundryClothes($selected_bottoms);
?>

<h1>オートコーディネータ</h1>
<p>ようこそ、<span style='font-weight: bold;'><?= htmlspecialchars($_SESSION[COLUMN_USER_NAME], ENT_QUOTES) ?></span>さん。
    <a href="logout.php" style="margin-left: 20px;">ログアウト</a></p>

<?php if (isDecidedClothes()): ?>
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
<?php endif; ?>

<?php if (!empty($selected_tops)): try {
    displayClothesImages($selected_tops);
} catch (Exception $e) {
    echo $e->getMessage();
} endif; ?>

<?php if (!empty($selected_bottoms)): try {
    displayClothesImages($selected_bottoms);
} catch (Exception $e) {
    echo $e->getMessage();
} endif; ?>

<form action="" method="post">
    <?php foreach ($top_inputs as $top_id): ?>
        <input type="hidden" name="<?= POST_CLOTHES_ID_KEY ?>[]" value="<?= $top_id ?>">
    <?php endforeach; ?>

    <?php foreach ($bottom_inputs as $bottom_id): ?>
        <input type="hidden" name="<?= POST_CLOTHES_ID_KEY ?>[]" value="<?= $bottom_id ?>">
    <?php endforeach; ?>
    <br>
    <?php if (!empty($_POST) && empty($error_message) && !isDecidedClothes()): ?>
        画像をクリックすると拡大できます<br>
        <input type="image" src="pictures/pop_kettei.png" alt="決定" width="120" height="60">
    <?php endif; ?>
</form>

<hr>
<div style="font-size: 110%;">▼衣服の管理▼</div>
<a href="closet.php"><img src="pictures/closet.png" class="closet" width="150" height="150" alt="衣服管理"></a>
<a href="explanation.php">このアプリについて</a><br>
<?php require_once('footer.php')?>
