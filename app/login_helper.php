<?php
require_once("utils.php");

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
