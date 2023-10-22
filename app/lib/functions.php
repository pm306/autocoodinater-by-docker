<?php
require_once __DIR__.'/../utils.php';

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
function isEmptyExceptZero($var = null): bool {
    return empty($var) && $var !== 0 && $var !== '0';
}
/**
 * index.phpで「決定ボタン」が押されたか判定します
 */
function isDecidedClothes() : bool {
    return !empty($_POST[POST_CLOTHES_ID_KEY]);
}

/**
 * ログインネームからユーザーを取得する。
 *
 * @param string $name ユーザー名
 * @return array|false 該当するユーザー情報またはfalse
 */
function getUserData(string $name): ?array {
    global $db;

    $stmt = $db->prepare('SELECT * FROM members WHERE name=?');
    $stmt->execute(array($name));

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

/** 
 * ログインセッションを確立します。
*/
function setLoginSessionAndCookie(): void {
    $login_name = $_POST[POST_LOGIN_NAME_KEY] ?? '';
    $userData = getUserData($login_name);

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

    foreach ($_POST[POST_CLOTHES_ID_KEY] as $clothe_id) {
        $now_date = date(DATE_FORMAT);
        $sql = $db->prepare('UPDATE clothes SET last_used_date=? WHERE id=?');
        $sql->bindparam(1, $now_date);
        $sql->bindparam(2, $clothe_id, PDO::PARAM_INT);
        $sql->execute();
    }
}


/**
 * ログインユーザーの名前とパスワードの入力に応じてエラーメッセージを返します。
 * 
 * @return string $error_message エラーメッセージ。エラーがない場合は空文字列を返す。
 */
function checkInputErrorLoginUserAndPass() : string {
    $error_message = '';
    $login_name = $_POST[POST_LOGIN_NAME_KEY] ?? '';
    $login_password = $_POST[POST_LOGIN_PASSWORD_KEY] ?? '';

    if ($login_name === '' || $login_password === '') {
        $error_message = '※ニックネームまたはパスワードが空です。';
    }
    else {
        $userData = getUserData($login_name);
        if ($userData === null) {
            $error_message = '※ログインに失敗しました。ニックネームかパスワードが間違っています。';
        }
    }

    return $error_message;
}

/**
 * データベースにユーザーが存在するかを確認します。
 * ユーザー名、あるいはユーザー名とパスワードを受け取ります。
 * @param string $username ユーザー名
 * @param ?string $password パスワード
 *
 * @return bool ユーザーが存在する場合はtrue、存在しない場合はfalse
 */
function isUserExists(string $username, ?string $password = null) : bool {
    global $db;

    $params = [$username];
    $query = SELECT_MEMBER_COUNT_BY_NAME;

    if ($password !== null) {
        $query = SELECT_MEMBER_COUNT_BY_NAME_PASSWORD;
        $params[] = sha1($password);
    }

    $sql = $db->prepare($query);
    $sql->execute($params);
    $result = $sql->fetch();

    return $result['count_user'] > 0;
}


/**
 * ユーザー登録時に入力されたユーザー名とパスワードのバリデーションチェックを行います。
 * TODO:データベースの問い合わせを分割する
 * 
 * @param string $username ユーザー名
 * @param string $pssword パスワード
 * @return string エラーメッセージ。エラーがない場合は空文字列を返す。
 */
function validateUserRegistration(string $username, string $password) : string {

    if ($username === '' || $password === '') {
        return ERROR_USERDATA_BLANK;
    } elseif (strlen($username) > NAME_MAX_LENGTH) {
        return ERROR_NAME_OVER_LENGTH;
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ERROR_PASSWORD_SHORT;
    } elseif (strlen($password) > PASSWORD_MAX_LENGTH) {
        return ERROR_PASSWORD_OVER_LENGTH;
    } else {
        global $db;
        $sql = $db->prepare(SELECT_MEMBER_COUNT_BY_NAME);
        $sql->execute(array($username));
        $result = $sql->fetch();
        if ($result['count_user'] > 0) {
            return ERROR_NAME_ALREADY_EXISTS;
        } else {
            return '';
        }
    }
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
 * @return string エラーメッセージ。エラーがない場合は空文字列を返す
 */
function checkInputErrorTemperature () : string {

    if (!isEmptyExceptZero($_POST[POST_TEMPERATURE_MAX_KEY]) && !isEmptyExceptZero($_POST[POST_TEMPERATURE_MIN_KEY])) {
        $max_temperature = $_POST[POST_TEMPERATURE_MAX_KEY];
        $min_temperature = $_POST[POST_TEMPERATURE_MIN_KEY];
        if ($max_temperature > MAX_TEMPERATURE_LIMIT) {
            return ERROR_TEMPERATURE_MAXOVER;
        } elseif ($min_temperature < MIN_TEMPERATURE_LIMIT) {
            return ERROR_TEMPERATURE_MINOVER;
        } elseif ($max_temperature < $min_temperature) {
            return ERROR_TEMPERATURE_IMPOSSIBLE;
        }
    } elseif (!empty($_POST) && empty($_POST[POST_CLOTHES_ID_KEY])) {
        return ERROR_TEMPERATURE_BLANK;
    }

    return '';
}

/**
 * 指定された服の種類の中からランダムに服を1着選び、配列に格納します。
 * @param PDO $db データベース接続オブジェクト
 * @param ?array $output_clothes 服の情報を格納する配列
 * @param string $clothe_types 服の種類
 *
 * @return void
 */
function selectRandomClothe(PDO $db, ?array &$output_clothes, ...$clothe_types){
    $output_clothes = (array) $output_clothes;
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


// 1. 入力値のバリデーション関数
function validateDeleteInput(string $username, string $password, string &$error_message): bool {
    if ($username === GUEST_NAME) {
        $error_message = ERROR_DELETE_GUEST_ACCOUNT;
        return false;
    }

    if (empty($username) || empty($password)) {
        $error_message = ERROR_USERDATA_BLANK;
        return false;
    }

    if (!isUserExists($username, $password)) {
        $error_message = ERROR_NAME_OR_PASSWORD_NOT_FIND;
        return false;
    }

    return true;
}

// 2. 関連する画像の削除関数
/**
 * @throws Exception
 */
function deleteAssociatedPictures(string $username) {
    global $db;

    try {
        $pictures = $db->prepare(SELECT_PICTURES_BY_OWNER);
        $pictures->execute(array($username));
        while ($picture = $pictures->fetch()) {
            $path = UPLOAD_DIR . $picture['picture'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $del_pictures = $db->prepare(DELETE_CLOTHES_BY_OWNER);
        $del_pictures->execute(array($username));
    } catch (PDOException $e) {
        throw new Exception('Error deleting associated pictures: ' . $e->getMessage());
    }
}
// 3. ユーザー情報の削除関数
/**
 * @throws Exception
 */
function deleteUser(string $username, string $password) {
    global $db;

    try {
        $statement = $db->prepare(DELETE_MEMBER_BY_NAME_PASSWORD);
        $statement->execute(array($username, sha1($password)));
    } catch (PDOException $e) {
        throw new Exception('Error deleting user: ' . $e->getMessage());
    }
}

// 全体の関数
function deleteAccount(string $username, string $password, string &$error_message, string &$delete_message) {
    if (!validateDeleteInput($username, $password, $error_message)) {
        return;
    }

    try {
        deleteAssociatedPictures($username);
        deleteUser($username, $password);

        $delete_message = DELETE_USER_SUCCESS_MESSAGE;
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

/**
 * closet.phpでチェックボックスを表示します。
 * @param array $clothes_type_array 服の種類を格納した配列
 *
 * @return void
 */
function displayCheckboxes(array $clothes_type_array, array $checked_array) {
    foreach($clothes_type_array as $clothes_type_key => $clothes_type_name):
        $is_checked = in_array($clothes_type_key, $checked_array) ? 'checked' : '';
        echo <<<HTML
            <li>
                <label>
                    <input type="checkbox" name="type[]" value="$clothes_type_key" $is_checked>
                    $clothes_type_name
                </label>
            </li>
            HTML;
    endforeach;
}

/**
 * closet.phpで画像を表示します。
 * @param array $imageData 画像の情報を格納した配列
 * @param int $imageIndex 画像のインデックス
 */
function displayImageForm(array $imageData, int $imageIndex) {
    $formName = "detail$imageIndex";
    $pictureId = $imageData['id'];
    $imageSrc = "upload/{$imageData['picture']}";

    include 'templates/imageFormTemplate.php';
}

/**
 * 服のIDから服の情報を取得します。
 * @param string $pictureIdKey 服のID
 * 
 * @return array 服の情報を格納した配列
 */
function fetchClotheDetails(string $pictureIdKey) {
    global $db;

    $sql = $db->prepare(SELECT_CLOTHES_BY_ID);
    $sql->bindparam(1, $pictureIdKey, PDO::PARAM_INT);
    $sql->execute();
    return $sql->fetch();
}

function getClothesDetailsById($id) {
    global $db;

    $sql = $db->prepare(SELECT_CLOTHES_BY_ID_PICTURE);
    $sql->bindparam(1, $id, PDO::PARAM_INT);
    $sql->execute();
    return $sql->fetch();
}

function deleteClothesById($id) {
    global $db;

    $sql = $db->prepare(DELETE_CLOTHES_BY_ID);
    $sql->bindparam(1, $id, PDO::PARAM_INT);
    $sql->execute();
}

function deleteUploadedFile($fileName) {
    $filePath = UPLOAD_DIR . $fileName;
    unlink($filePath);
}

/**
 * index.phpで服の画像を表示します。フォームリンクはなし
 *
 * @param array $clothesArray 服の情報を格納した配列
 * @param int $width 表示する画像の幅
 * @param int $height 表示する画像の高さ
 * @return void
 */
function displayClothesImages($clothesArray, $width=250, $height=250) {
    foreach ($clothesArray as $clothes) {
        include 'templates/clothes_image_template.php';
    }
}

/**
 * 選択された服の中から、毎日洗濯しない服を除外します。
 * 残った服のIDを返します。
 * 
 * @param array $selected_clothes
 * @param array $not_laundry_everyday
 * @return array $filtered_clothes_ids 毎日洗濯しない服を除外した服のIDの配列
 */
function filterLaundryClothes(array $selected_clothes, array $not_laundry_everyday) {
    $filtered_clothes_ids = [];

    if (!empty($selected_clothes)) {
        foreach ($selected_clothes as $clothes) {
            if (in_array($clothes['type'] ?? [], $not_laundry_everyday)) {
                $filtered_clothes_ids[] = $clothes['id'];
            }
        }
    }

    return $filtered_clothes_ids;
}
