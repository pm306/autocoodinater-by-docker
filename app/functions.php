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
 * 0および'0'を除く変数が空であるかをチェックします。
 *
 * @param mixed $var チェック対象の変数（デフォルトはnull）
 * @return bool 変数が空の場合はtrue、空でない場合はfalse
 */
function isEmptyExceptZero($var = null) {
    return empty($var) && $var !== 0 && $var !== '0';
}
/**
 * index.phpで「決定ボタン」が押されたか判定します
 */
function isDesidedClothes() : bool {
    return !empty($_POST[POST_CLOTHE_ID_KEY]);
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

/**
 * HTTP POSTメソッドで送られた服のlast_used_dateを本日に更新します。
 */
function updateLastUsedDate() : void {
    foreach ($_POST[POST_CLOTHE_ID_KEY] as $clothe_id) {
        $now_date = date(DATE_FORMAT);
        $sql = $db->prepare('UPDATE clothes SET last_used_date=? WHERE id=?');
        $sql->bindparam(1, $now_date, PDO::PARAM_STR);
        $sql->bindparam(2, $clothe_id, PDO::PARAM_INT);
        $sql->execute();
    }
}

function checkInputErrorTemperature () : string {
    $error_log = '';
    $max_temperature = 0;
    $min_temperature = 0;

    if (!isEmptyExceptZero($_POST[POST_TEMPERATURE_MAX_KEY]) && !isEmptyExceptZero($_POST[POST_TEMPERATURE_MIN_KEY])) {
        $max_temperature = $_POST[POST_TEMPERATURE_MAX_KEY];
        $min_temperature = $_POST[POST_TEMPERATURE_MIN_KEY];
        if ($max_temperature > MAX_TEMPERATURE_LIMIT) {
            $error_log = ERROR_TEMPERATURE_MAXOVER;
        } elseif ($min_temperature < MIN_TEMPERATURE_LIMIT) {
            $error_log = ERROR_TEMPERATURE_MINOVER;
        } elseif ($max_temperature < $min_temperature) {
            $error_log = ERROR_TEMPERATURE_IMPOSSIBLE;
        }
    } elseif (!empty($_POST) && empty($_POST[POST_CLOTHE_ID_KEY])) {
        $error_log = ERROR_TEMPERATURE_BLANK;
    }

    return $error_log;
}


