<?php 
session_start();
require_once('header.php');
require_once('dbconnect.php');
require_once('lib/functions.php');
require_once('utils.php');

$error_message = '';

if(!empty($_POST)){
    $username = $_POST[POST_LOGIN_NAME_KEY];
    $email_address = $_POST[POST_LOGIN_EMAIL_KEY];
    $password = $_POST[POST_LOGIN_PASSWORD_KEY];
    $error_message = validateUserRegistration($username, $email_address, $password);

    if(empty($error_message)){
        registNewUser($username, $email_address, $password);

        header('Location: regist_user_success.php');
        exit();
    }
}
?>

<h1>アカウント作成</h1>
<?php echo '<div class="alert">',$error_message,'</div>'; ?>

<form action="" method="post"><table>
<tr><td>ニックネーム(最大16文字)</td><td><input type="text" name='<?= POST_LOGIN_NAME_KEY ?>'></td></tr>
<tr><td>メールアドレス(最大64文字)</td><td><input type="text" name='<?= POST_LOGIN_EMAIL_KEY ?>'></td></tr>
<tr><td>パスワード(8~16文字)</td><td><input type="password" name='<?= POST_LOGIN_PASSWORD_KEY?>'></td></tr>
<tr><td><input type="submit" value="ユーザー登録"></td></tr>
</table><form>
<hr>
※パスワードを忘れるとログインやアカウント削除ができなくなります。ご注意ください<br>
<a href="login.php"><img src="pictures/navigationj_back.png" width="100" height="50" style="margin-bottom: -20px;"></a>

<?php require_once('footer.php'); ?>
