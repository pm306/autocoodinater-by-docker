<?php
session_start();
require_once('header.php');
require_once('dbconnect.php');
require_once("utils.php");
require_once('functions.php');

redirectIfLoggedIn();

const FIELD_NAME = 'name';
const FIELD_PASSWORD = 'password';
$error = '';

/**
* ログイン
*/
if(!empty($_POST)){
    $name = $_POST[FIELD_NAME] ?? '';
    $password = $_POST[FIELD_PASSWORD] ?? '';

    if ($name === '' || $password === '') {
        setError($error, '※ニックネームまたはパスワードが空です。');
    } else {
        $user = loginUser($name, $password, $db);

        if ($user) {
            setLoginSessionAndCookie($user);

            if ($_SESSION[COLUMN_USER_NAME] === GUEST_NAME) {
                require_once('guestlogin.php');
            }

            header('Location: index.php');
            exit();
        } else {
            setError($error,  '※ログインに失敗しました。ニックネームかパスワードが間違っています。');
        }
    } 
}
?>

<h1>自動コーディネータ</h1>
<p>自動で服を選んでくれるアプリケーションです。<br>
(このページはPHPの学習を目的として製作されたものです)<br>
<a href="explanation.php">このアプリについて</a></p><br>

<form atcion="" method="post">
    <table>
    <?php if(!empty($error))echo '<span class="alart">'.$error.'</span>';?>
    <tr>
        <td>ニックネーム</td>
        <td><input type="text" name="<?= FIELD_NAME ?>" value=""></td>
    </tr>
    <tr>
        <td>パスワード</td>
        <td><input type="password" name="<?= FIELD_PASSWORD ?>"></td>
    </tr>
    <tr>
        <td><input type="submit" value="ログイン"></td>
    </tr>
    </table>
</form>

<br><a href="regist_user.php" style="font-weight: bold;">アカウント作成</a><br><br>
<a href="delete_user.php">アカウントの削除</a>
<hr>
<br>※以下のニックネームでゲストアカウントによるお試しログインができます。<br>
ニックネーム：ゲスト<br>
パスワード：任意<br><br>
ゲストアカウントには最初からいくつかの服が登録されているため、手軽にアプリの動作を確認することができます。<br>
服の追加・削除も可能ですが、ブラウザを閉じたりログアウトすると状態がリセットされます。<br>
<?php require_once('footer.php'); ?>