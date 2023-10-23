<?php

const COOKIE_EXPIRY_TIME = 60 * 60 * 24 * 7;  
const TIME_FOR_DELETE_COOKIE = 3600;
const MAX_TEMPERATURE_LIMIT = 50;
const MIN_TEMPERATURE_LIMIT = -50;
const NAME_MAX_LENGTH = 16;
const EMAIL_MAX_LENGTH = 64;
const PASSWORD_MIN_LENGTH = 8;
const PASSWORD_MAX_LENGTH = 16;
const IMAGES_PER_ROW = 5;
const BORDER_MIN_TEMPERATURE_HOT = 23;
const BORDER_MIN_TEMPERATURE_WARM = 19;
const BORDER_MIN_TEMPERATURE_COMFORTABLE = 16;
const BORDER_MIN_TEMPERATURE_COOL = 11;
const BORDER_MIN_TEMPERATURE_COLD = 7;
const BORDER_MAX_TEMPERATURE_WARM = 26;
const BORDER_MAX_TEMPERATURE_COMFORTABLE = 19;

const COLUMN_USER_ID = 'id';
const COLUMN_USER_NAME = 'name';
const COLUMN_USER_EMAIL = 'email';
const COLUMN_USER_PASSWORD = 'password';
const SESSION_ID_KEY = 'session_id';
const COOKIE_NAME_KEY = 'Cookie';   
const POST_ID_KEY = 'id';
const POST_TYPE_KEY = 'type';
const POST_PICTURE_ID_KEY = 'picture_id';
const POST_RETURN_KEY = 'return';
const POST_CLOTHES_ID_KEY = 'clothe_id';
const POST_TEMPERATURE_MAX_KEY = 'max_temperature';
const POST_TEMPERATURE_MIN_KEY = 'min_temperature';
const POST_SELECT_KEY = 'select';
const POST_LOGIN_NAME_KEY = 'name';
const POST_LOGIN_EMAIL_KEY = 'email';
const POST_LOGIN_PASSWORD_KEY = 'password';
const RETURN_TRUE_VALUE = 'true';        
const GUEST_NAME = 'ゲスト';
const GUEST_EMAIL = 'guest@com';         
const DATE_FORMAT = 'Y-m-d';
const IMAGE_NAME_FORMAT = "YmdHis";
const UPLOAD_DIR = 'upload/';
const CLOTH_TYPE_TOPS = 'tops';
const CLOTH_TYPE_BOTTOMS = 'bottoms';
const ERROR_TEMPERATURE_BLANK = '※気温を入力してください';
const ERROR_TEMPERATURE_MAXOVER = '※正しい最高気温を入力してください(50℃まで)';
const ERROR_TEMPERATURE_MINOVER = '※正しい最低気温を入力してください(-50℃まで)';
const ERROR_TEMPERATURE_IMPOSSIBLE = '※最高気温が最低気温以上になるように入力してください';
const ERROR_USERDATA_BLANK = '※メールアドレスまたはパスワードが空です';
const ERROR_NAME_OVER_LENGTH = '※ニックネームが長すぎます。';
const ERROR_PASSWORD_SHORT = '※パスワードが短すぎます。';
const ERROR_EMAIL_OVER_LENGTH = '※メールアドレスが長すぎます。';
const ERROR_PASSWORD_OVER_LENGTH = '※パスワードが長すぎます';
const ERROR_NAME_ALREADY_EXISTS = '※登録済みのアカウントです。';
const ERROR_DELETE_GUEST_ACCOUNT = '※ゲストアカウントは削除できません。';
const ERROR_NAME_OR_PASSWORD_NOT_FIND = '※名前またはパスワードが間違っています。';
const ERROR_FAILURE_LOGIN = "※ログインに失敗しました。メールアドレスかパスワードが間違っています。";
const ERROR_EMAIL_INVALID = '※メールアドレスの形式が正しくありません。';
const UPLOAD_SUCCESS_MESSAGE = '追加しました';
const UPLOAD_FAILURE_MESSAGE = 'アップロードに失敗しました';
const DELETE_USER_SUCCESS_MESSAGE = 'ユーザーの消去が完了しました。';

const SELECT_CLOTHES_BY_OWNER_AND_TYPE = 'SELECT id, picture FROM clothes WHERE owner=? and type=?';
const SELECT_CLOTHES_BY_ID = 'SELECT id, type, picture FROM clothes WHERE id=?';
const SELECT_CLOTHES_BY_ID_PICTURE = 'SELECT id, picture FROM clothes WHERE id=?';
const DELETE_CLOTHES_BY_ID = 'DELETE FROM clothes WHERE id=?';
const SELECT_PICTURES_BY_OWNER = 'SELECT id, picture FROM clothes WHERE owner=?';
const DELETE_CLOTHES_BY_OWNER = 'DELETE FROM clothes WHERE owner=?';
const DELETE_MEMBER_BY_NAME_PASSWORD = 'DELETE FROM members WHERE name=? and password=?';
const SELECT_MEMBER_COUNT_BY_NAME = 'SELECT COUNT(*) AS count_user FROM members WHERE name=?';
const SELECT_MEMBER_COUNT_BY_NAME_PASSWORD = 'SELECT COUNT(*) AS count_user FROM members WHERE name=? and password=?';
const INSERT_NEW_MEMBER = 'INSERT INTO members SET name=?, email=?, password=?';
const INSERT_NEW_CLOTH = 'INSERT INTO clothes SET owner=?, type=?,picture=?, last_used_date="2000-01-01"';