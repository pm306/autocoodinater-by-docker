<?php

const COOKIE_EXPIRY_TIME = 60 * 60 * 24 * 7;  // Cookie expiration time set to 1 week in seconds
const MAX_TEMPERATURE_LIMIT = 50;
const MIN_TEMPERATURE_LIMIT = -50;

const POST_ID_KEY = 'id';
const POST_TYPE_KEY = 'type';
const POST_PICTURE_ID_KEY = 'picture_id';
const POST_RETURN_KEY = 'return';
const POST_KEY_WEAR = 'wear';
const RETURN_TRUE_VALUE = 'true';
const COOKIE_NAME = 'Cookie';           // Name of the cookie used for session identification
const GUEST_NAME = 'ゲスト';            // Default name for guest users
const DATE_FORMAT = 'Y-m-d';
const ERROR_TEMPERATURE_BLANK = 'blank';
const ERROR_TEMPERATURE_MAXOVER = 'maxover';
const ERROR_TEMPERATURE_MINOVER = 'minover';
const ERROR_TEMPERATURE_IMPOSSIBLE = 'impossible';
const UPLOAD_DIR = 'upload/'

const SELECT_CLOTHES_BY_OWNER_AND_TYPE = 'SELECT id, picture FROM clothes WHERE owner=? and type=?';
const SELECT_CLOTHES_BY_ID = 'SELECT id, type, picture FROM clothes WHERE id=?';
const SELECT_CLOTHES_BY_ID_PICTURE = 'SELECT id, picture FROM clothes WHERE id=?';
const DELETE_CLOTHES_BY_ID = 'DELETE FROM clothes WHERE id=?';
const SELECT_MEMBER_COUNT_BY_NAME_PASSWORD = 'SELECT COUNT(*) AS cnt FROM members WHERE name=? and password=?';
const SELECT_PICTURES_BY_OWNER = 'SELECT id, picture FROM clothes WHERE owner=?';
const DELETE_CLOTHES_BY_OWNER = 'DELETE FROM clothes WHERE owner=?';
const DELETE_MEMBER_BY_NAME_PASSWORD = 'DELETE FROM members WHERE name=? and password=?';