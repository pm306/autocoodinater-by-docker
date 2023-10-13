<?php
require_once('utils.php'); 

/**
 * ログイン判定を行う。
 */
function isLoggedIn(): bool {
    return 
    isset($_COOKIE[COOKIE_NAME_KEY]) && 
    isset($_SESSION[SESSION_ID_KEY]) && 
    $_SESSION[SESSION_ID_KEY] === $_COOKIE[COOKIE_NAME_KEY];
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

/**
 * ユーザーをデータベースで検索し、ログイン情報を検証する。
 *
 * @param string $name ユーザー名
 * @param string $password 生のパスワード
 * @param PDO $db データベースの接続
 * 
 * @return array|false 該当するユーザー情報またはfalse
 */
function loginUser(string $name, string $password, PDO $db): ?array {

    $stmt = $db->prepare('SELECT * FROM members WHERE name=?');
    $stmt->execute(array($name));
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $user ? $user : null;
}

/**
* エラーメッセージを設定します。
*
* @param string $error エラーメッセージを格納する変数
* @param string $message エラーメッセージ
* 
* @return void
*/
function setError(string &$alart, string $message): void {
    $alart = $message;
}

/** 
 * ログインセッションを確立します。
*/
function setLoginSessionAndCookie(array $user): void {
    $_SESSION[COLUMN_USER_ID] = $user[COLUMN_USER_ID];
    $_SESSION[COLUMN_USER_NAME] = $user[COLUMN_USER_NAME];
    $_SESSION[SESSION_ID_KEY] = session_id();

    setcookie(COOKIE_NAME_KEY, $_SESSION[SESSION_ID_KEY], time() + COOKIE_EXPIRY_TIME);
}