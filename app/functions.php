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
 * 入力されたユーザー名とパスワードのバリデーションチェックを行います。
 * TODO:同名ユーザーのチェックも行っているが、関数名からはわかりにくい。
 * 
 * @return $error_message エラーメッセージ。エラーがない場合は空文字列を返す。
 */
function validateUserRegistration(string $username, string $password) : string {
    $error_message = '';

    if ($username === '' || $password === '') {
        $error_message = ERROR_USERDATA_BLANK;
    } elseif (strlen($username) > NAME_MAX_LENGTH) {
        $error_message = ERROR_NAME_OVER_LENGTH;
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error_message = ERROR_PASSWORD_SHORT;
    } elseif (strlen($password) > PASSWORD_MAX_LENGTH) {
        $error_message = ERROR_PASSWORD_OVER_LENGTH;
    } else {
        global $db;
        $sql = $db->prepare(SELECT_MEMBER_COUNT_BY_NAME);
        $sql->execute(array($username));
        $result = $sql->fetch();
        if ($result['count_user'] > 0) {
            $error_message = ERROR_NAME_ALREADY_EXISTS;
        }
    }

    return $error_message;
}

/**
 * ユーザー名、パスワードを受け取り、データベースに登録します。
 * @param string $username ユーザー名
 * @param string $password パスワード
 * 
 * @return bool 登録に成功した場合はtrue、失敗した場合はfalse
 */
function registUser(string $username, string $password) : bool {
    global $db;

    try {
        $hashedPassword = sha1($password);
        $statement = $db->prepare(INSERT_NEW_MEMBER);
        $statement->execute(array($username, $hashedPassword));
        return true;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
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

/**
 * 指定された服の種類の中からランダムに服を1着選び、配列に格納します。
 * @param PDO $db データベース接続オブジェクト
 * @param array $output_clothes 服の情報を格納する配列
 * @param string $clothe_types 服の種類
 * 
 * @return void
 */
function selectRandomClothe(PDO $db, array &$output_clothes, ...$clothe_types){
    $yesterday_date = date(DATE_FORMAT, strtotime('-1 day'));
    $clothe_types_length = count($clothe_types);
    $username = $_SESSION[POST_LOGIN_NAME_KEY];
    
    // WHERE句の条件を配列で生成
    $type_conditions = array_fill(0, $clothe_types_length, 'type=?');
    
    // SQL文の動的部分を組み立て
    $sql = sprintf(
        "SELECT id, type, picture FROM clothes WHERE owner=? and last_used_date<'%s' and (%s) ORDER BY RAND() LIMIT 1",
        $yesterday_date,
        implode(' or ', $type_conditions)
    );
    
    $selected_clothe_stmt = $db->prepare($sql);
    $selected_clothe_stmt->execute(array_merge([$username], $clothe_types));
    
    $selected_clothe = $selected_clothe_stmt->fetch();
    
    if ($selected_clothe) {
        $output_clothes[] = $selected_clothe;
    } else {
        $output_clothes[] = ['id' => 0, 'picture' => "10195no_image_square.png"]; //image not found
    }
}

/**
 * アカウントを削除します。
 * TODO:処理の分割 (存在判定、画像の削除、ユーザー情報の削除)
 */
function deleteAccount(string &$error_message, string &$del_msg) {
    global $db;
    $username = $_POST[POST_LOGIN_NAME_KEY];
    $password = $_POST[POST_LOGIN_PASSWORD_KEY];

    // ゲストアカウントは削除できない
    if ($username === GUEST_NAME) {
        $error_message = ERROR_DELETE_GUEST_ACCOUNT;
        return;
    }
    // 空欄判定
    if (empty($username) || empty($password)) {
        $error_message = ERROR_USERDATA_BLANK;
        return;
    }

    // 存在判定
    $isfind = $db->prepare(SELECT_MEMBER_COUNT_BY_NAME_PASSWORD);
    $isfind->execute(array($username, sha1($password)));
    $del_user = $isfind->fetch();
    // 入力情報に合致するユーザーがいれば、画像を全て消去した上でユーザー情報を削除する
    if ($del_user === false) {
        $error_message = ERROR_NAME_OR_PASSWORD_NOT_FIND;
        return;
    }
    // そのユーザーが登録している画像があれば全て消去する
    $pictures = $db->prepare(SELECT_PICTURES_BY_OWNER);
    $pictures->execute(array($username));
    while ($picture = $pictures->fetch()) {
        $pass = UPLOAD_DIR;
        $pass .= $picture['picture'];
        unlink($pass);
    }
    $del_pictures = $db->prepare(DELETE_CLOTHES_BY_OWNER);
    $del_pictures->execute(array($name));

    // ユーザー情報を削除
    $statement = $db->prepare(DELETE_MEMBER_BY_NAME_PASSWORD);
    $statement->execute(array($name, $password));
    $del_msg = "消去が完了しました。";
}