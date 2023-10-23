<?php
require_once __DIR__.'/../utils.php';

/**
 * データベースにクエリを投げる関数です。
 *
 * @param string $query クエリ
 * @param array $params クエリにバインドするパラメータ
 * @return array|false クエリの実行結果。失敗した場合はfalse
 */
function executeQuery(string $query, array $params = []) {
    global $db;

    try {
        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo $e->getMessage();
        return false;
    }
}
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
 * メールアドレスからユーザー名を取得します。
 */
function getUserDataByMailAddress(string $mail_address): ?array {
    global $db;

    $statement = $db->prepare('SELECT * FROM members WHERE email=?');
    $statement->execute(array($mail_address));

    $userData = $statement->fetch(PDO::FETCH_ASSOC);

    return $userData ?: null;
}

/** 
 * ログインセッションを確立します。
 * TODO:$_POST消す、メアドでセッション確立する
*/
function setLoginSessionAndCookie($email_address): void {
    $userData = getUserDataByMailAddress($email_address);

    $_SESSION[COLUMN_USER_ID] = $userData[COLUMN_USER_ID];
    $_SESSION[COLUMN_USER_NAME] = $userData[COLUMN_USER_NAME];
    $_SESSION[COLUMN_USER_EMAIL] = $userData[COLUMN_USER_EMAIL];
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
 * ログインユーザーのメールアドレスとパスワードの入力に応じてエラーメッセージを返します。
 * 
 * @return string $error_message エラーメッセージ。エラーがない場合は空文字列を返す。
 */
function checkInputErrorLoginUserAndPass(string $mail_address, string $password) : string {

    if ($mail_address === '' || $password === '') {
        return ERROR_USERDATA_BLANK;
    }
    else {
        $userData = getUserDataByMailAddress($mail_address);
        if ($userData === null) {
            return ERROR_FAILURE_LOGIN;
        }
    }

    return '';
}

/**
 * データベースにユーザーが存在するかを確認します。
 * メールアドレス、あるいはユーザー名とパスワードを受け取ります。
 * @param string $username ユーザー名
 * @param ?string $password パスワード
 *
 * @return bool ユーザーが存在する場合はtrue、存在しない場合はfalse
 */
function isUserExists(string $email_address, ?string $password = null) : bool {
    global $db;

    $params = array($email_address);
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
 * ユーザー登録時に入力されるユーザー情報のバリデーションを行います。
 * ユーザー名、メールアドレス、パスワードです。
 * TODO:データベースの問い合わせを分割する
 * 
 * @param string $username ユーザー名
 * @param string $pssword パスワード
 * @return string エラーメッセージ。エラーがない場合は空文字列を返す。
 */
function validateUserRegistration(string $username, string $email_address, string $password) : string {

    if ($username === '' || $password === '') {
        return ERROR_USERDATA_BLANK;
    } elseif (strlen($username) > NAME_MAX_LENGTH) {
        return ERROR_NAME_OVER_LENGTH;
    } elseif (strlen($email_address) > EMAIL_MAX_LENGTH){
        return ERROR_EMAIL_OVER_LENGTH;
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ERROR_PASSWORD_SHORT;
    } elseif (strlen($password) > PASSWORD_MAX_LENGTH) {
        return ERROR_PASSWORD_OVER_LENGTH;
    } else {
        $user_data = getUserDataByMailAddress($email_address);
        if ($user_data) {
            return ERROR_NAME_ALREADY_EXISTS;
        } else {
            return '';
        }
    }
}

/**
 * ユーザーの情報を引数で受け取り、データベースに登録します。
 * @param string $username ユーザー名
 * @param string $email_address メールアドレス
 * @param string $password パスワード
 *
 * @return bool 登録に成功した場合はtrue、失敗した場合はfalse
 */
function registNewUser(string $username, string $email_address, string $password) : bool {
    global $db;

    try {
        $hashedPassword = sha1($password);
        $statement = $db->prepare(INSERT_NEW_MEMBER);
        $statement->execute(array($username, $email_address, $hashedPassword));
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
function validateDeleteInput(string $email_address, string $password, string &$error_message): bool {
    if ($email_address === GUEST_EMAIL) {
        $error_message = ERROR_DELETE_GUEST_ACCOUNT;
        return false;
    }

    if (empty($email_address) || empty($password)) {
        $error_message = ERROR_USERDATA_BLANK;
        return false;
    }

    if (!isUserExists($email_address, $password)) {
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

/**
 * ユーザーアカウントを削除します。
 *
 * @param string $email_address メールアドレス
 * @param string $password パスワード
 * @param string $error_message エラーメッセージ
 * @param string $delete_message 削除完了メッセージ
 * @return void
 */
function deleteAccount(string $email_address, string $password, string &$error_message, string &$delete_message) {
    if (!validateDeleteInput($email_address, $password, $error_message)) {
        return;
    }

    try {
        deleteAssociatedPictures($email_address);
        deleteUser($email_address, $password);

        $delete_message = DELETE_USER_SUCCESS_MESSAGE;
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

/**
 * clothes_typesテーブルから服の種類のコードを取得します。
 * @param array $categories カテゴリの配列
 *
 * @return array $clothes_array 服の種類のコードと表示名を格納した配列
 */
function fetchClothesTypes(array $categories = []) :array {
    global $db;
    $clothes_array = [];

    try {
        // 基本のクエリを設定
        $query = "SELECT code, name FROM clothes_types";

        // カテゴリが指定された場合、WHERE句を追加
        if (!empty($categories)) {
            $placeholders = implode(',', array_fill(0, count($categories), '?'));
            $query .= " WHERE category IN ($placeholders)";
        }

        $statement = $db->prepare($query);

        // カテゴリが指定された場合、bindParamを使用して各カテゴリをバインド
        if (!empty($categories)) {
            foreach ($categories as $index => $category) {
                $statement->bindValue($index + 1, $category, PDO::PARAM_STR);
            }
        }

        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $clothes_array[$row['code']] = $row['name'];
        }

    } catch (PDOException $e) {
        // エラーメッセージを出力
        echo $e->getMessage();
        return [];
    }

    return $clothes_array;
}


/**
 * closet.phpでチェックボックスを表示します。
 * @param array $clothes_type_array 服の種類のコードと表示名を格納した連想配列
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
 * 服の種類のコードから服の種類の表示名を取得します。
 *
 * @param string $code 服の種類のコード
 * @return string|null 服の種類の表示名。該当するデータがない場合はnullを返す
 */
function fetchClothesTypeNameByCode(string $code): ?string {
    global $db;

    try {
        $query = "SELECT name FROM clothes_types WHERE code=?";
        $statement = $db->prepare($query);
        $statement->bindparam(1, $code);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['name'];
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return null; // エラーや該当するデータがない場合はnullを返す
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
function fetchClothesDetails(string $pictureIdKey) {
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
 * 毎日洗濯しなくていいリストに登録されている服の種類かどうかを判定します。
 *
 * @param string $typeCode 服の種類のコード
 * @return boolean 毎日洗濯しなくていい服の種類の場合はtrue、そうでない場合はfalse
 */
function isNotLaundryEverydayType(string $typeCode): bool {
    global $db;

    try {
        $query = "SELECT 1 
                  FROM not_laundry_everyday nle
                  JOIN clothes_types ct ON nle.clothes_type_id = ct.id 
                  WHERE ct.code = ?";
        $statement = $db->prepare($query);
        $statement->bindValue(1, $typeCode, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result !== false; // データが存在すればtrue、存在しなければfalseを返す

    } catch (PDOException $e) {
        echo $e->getMessage();
    } 

    return false; // エラーが発生した場合もfalseを返す
}


/**
 * 選択された服の中から、毎日洗濯しない服を除外します。
 * 残った服のIDを返します。
 * 
 * @param array $selected_clothes
 * @param array $not_laundry_everyday 毎日洗濯しない服のコードと表示名の配列
 * @return array $filtered_clothes_ids 毎日洗濯しない服を除外した服のIDの配列
 */
function filterLaundryClothes(array $selected_clothes) :array {
    $filtered_clothes_ids = [];

    if (!empty($selected_clothes)) {
        foreach ($selected_clothes as $clothes) {
            if (!isNotLaundryEverydayType($clothes['type'] ?? '')) {
                $filtered_clothes_ids[] = $clothes['id'];
            }
        }
    }

    return $filtered_clothes_ids;
}

/**
 * 新しい服をデータベースに登録します。
 * TODO:param書き込み
 *
 * @return bool
 */
function registNewClothes(string $user_email_address, string $clothes_type, string $new_name) :void {
    $query = 'INSERT INTO clothes SET owner=?, type=?,picture=?, last_used_date="2000-01-01"';
    executeQuery($query, array(
        $user_email_address,
        $clothes_type,
        $new_name ?? '',
    ));
}
