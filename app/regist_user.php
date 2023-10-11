<?php 
session_start();
require_once('header.php');
require_once('dbconnect.php');
require_once('utils.php');  // 追加

$error = array();

if(!empty($_POST)){
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    //空欄判定
    if($name === '') $error['name'] = ERROR_TEMPERATURE_BLANK;
    if($password === '') $error['password'] = ERROR_TEMPERATURE_BLANK;

    //長さ判定
    if(empty($error['name']) && strlen($name) > NAME_MAX_LENGTH) $error['name'] = ERROR_NAME_OVER_LENGTH;
    if(empty($error['password']) && strlen($password) < PASSWORD_MIN_LENGTH) $error['password'] = ERROR_PASSWORD_SHORT;
    if(empty($error['password']) && strlen($password) > PASSWORD_MAX_LENGTH) $error['password'] = ERROR_PASSWORD_OVER_LENGTH;

    //登録済み判定
    $sql = $db->prepare(SELECT_MEMBER_COUNT_BY_NAME);
    $sql->execute(array($name));
    $res = $sql->fetch();
    if($res['cnt'] > 0) $error['name'] = ERROR_NAME_ALREADY_EXISTS;

    //エラーがなければデータベースに登録する
    if(empty($error)){
        $statement = $db->prepare(INSERT_NEW_MEMBER);
        $statement->execute(array($name, sha1($password)));

        header('Location: regist_user_success.php');
        exit();
    }
}
?>

<h1>アカウント作成</h1>
<?php //エラーメッセージの表示
if(isset($error['name']) && $error['name'] === 'blank') echo '<div class="alart">※ニックネームが空です。</div>';
if(isset($error['name']) && $error['name'] === 'over') echo '<div class="alart">※ニックネームが長すぎます。</div>';
if(isset($error['name']) && $error['name'] === 'already') echo '<div class="alart">※登録済みのアカウント名です。</div>';
if(isset($error['password']) && $error['password'] === 'blank') echo '<div class="alart">※パスワードが空です。</div>';
if(isset($error['password']) && $error['password'] === 'shortage') echo '<div class="alart">※パスワードが短すぎます。</div>';
if(isset($error['password']) && $error['password'] === 'over') echo '<div class="alart">※パスワードが長すぎます。</div>';

?>
<form action="" method="post"><table>
<tr><td>ニックネーム(1~16文字)</td><td><input type="textbox" name="name"></td></tr>
<tr><td>パスワード(4~16文字)</td><td><input type="password" name="password"></td></tr>
<tr><td><input type="submit" value="ユーザー登録"></td></tr>
</table><form>
<hr>
※パスワードを忘れるとログインやアカウント削除ができなくなります。ご注意ください<br>
<a href="login.php"><img src="pictures/navigationj_back.png" width="100" height="50" style="margin-bottom: -20px;"></a>

<?php require_once('footer.php'); ?>