<?php
session_start();
require_once('header.php');
require_once('dbconnect.php');
require_once("utils.php");
require_once('lib/functions.php');


$error_message = '';
$delete_message = '';

if(!empty($_POST)){
    $username = $_POST[POST_LOGIN_NAME_KEY];
    $password = $_POST[POST_LOGIN_PASSWORD_KEY];
    
    deleteAccount($username, $password, $error_message, $delete_message);
}
?>

<h1>アカウント削除</h1>
<p>削除したいアカウントのニックネームとパスワードを入力してください。<br>
アカウントを削除すると登録していた服のデータもすべて消去されます。</p>
<?php if(!empty($error_message))echo '<span class="alert">'.$error_message.'</span>';?>

<?php if(!empty($delete_message)):?>
<div style="color: red; font-size: 150%;"><?php echo $delete_message ?></div>
<?php else: ?>
<form action="" method="post"><table>
<tr><td>ニックネーム</td><td><input type="text" name="name"></td></tr>
<tr><td>パスワード</td><td><input type="password" name="password"></td></tr>
<tr><td><input type="submit" value="削除"></td></tr>
</table><form>
<?php endif; ?>
<br><br>
<a href="login.php"><img src="pictures/navigationj_back.png" width="100" height="50" style="margin-bottom: -20px;"></a>

<?php require_once('footer.php'); ?>
