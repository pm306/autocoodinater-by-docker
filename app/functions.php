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
 * ログインネームとパスワードからユーザーを取得する。
 *
 * @param string $name ユーザー名
 * @param string $password 生のパスワード
 * 
 * @return array|false 該当するユーザー情報またはfalse
 */
function getUserData(string $name, string $password): ?array {
    global $db;

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
function setLoginSessionAndCookie(): void {
    global $db;

    $login_name = $_POST[POST_LOGIN_NAME_KEY] ?? '';
    $login_password = $_POST[POST_LOGIN_PASSWORD_KEY] ?? '';
    $userData = getUserData($login_name, $login_password);

    $_SESSION[COLUMN_USER_ID] = $userData[COLUMN_USER_ID];
    $_SESSION[COLUMN_USER_NAME] = $userData[COLUMN_USER_NAME];
    $_SESSION[SESSION_ID_KEY] = session_id();

    setcookie(COOKIE_NAME_KEY, $_SESSION[SESSION_ID_KEY], time() + COOKIE_EXPIRY_TIME);
}

/**
 * HTTP POSTメソッドで送られた服のlast_used_dateを本日に更新します。
 */
function updateLastUsedDate() : void {
    global $db;

    foreach ($_POST[POST_CLOTHE_ID_KEY] as $clothe_id) {
        $now_date = date(DATE_FORMAT);
        $sql = $db->prepare('UPDATE clothes SET last_used_date=? WHERE id=?');
        $sql->bindparam(1, $now_date, PDO::PARAM_STR);
        $sql->bindparam(2, $clothe_id, PDO::PARAM_INT);
        $sql->execute();
    }
}


/**
 * ログインユーザーの名前とパスワードの入力に応じてエラーメッセージを返します。
 * 
 * @return $error_message エラーメッセージ。エラーがない場合は空文字列を返す。
 */
function checkInputErrorLoginUserAndPass() : string {
    $error_message = '';
    $login_name = $_POST[POST_LOGIN_NAME_KEY] ?? '';
    $login_password = $_POST[POST_LOGIN_PASSWORD_KEY] ?? '';

    if ($login_name === '' || $login_password === '') {
        $error_message = '※ニックネームまたはパスワードが空です。';
    }
    else {
        $userData = getUserData($login_name, $login_password);
        if ($userData === null) {
            $error_message = '※ログインに失敗しました。ニックネームかパスワードが間違っています。';
        }
    }
    
    return $error_message;
}

/**
 * 最高気温、最低気温の入力に応じてエラーメッセージを返します。
 * 
 * @return $error_log エラーメッセージ。エラーがない場合は空文字列を返す
 */
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


