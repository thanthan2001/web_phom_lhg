<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['language'])) {
    $_SESSION['lang'] = $_POST['language'];
}

// Mặc định Tiếng Anh nếu chưa có session
$language = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Kiểm tra file ngôn ngữ có tồn tại không, nếu không thì dùng Tiếng Anh
$langFile = "lang/$language.php";
if (!file_exists($langFile)) {
    $langFile = "../lang/en.php";
}

$lang = include $langFile;
