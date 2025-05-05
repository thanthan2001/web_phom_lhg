<?php
$dsn = "Driver={SQL Server};Server=localhost;Database=TEST_DB;";
$user = "sa";
$pass = "123";

$conn = odbc_connect($dsn, $user, $pass);
if (!$conn) {
    die("Kết nối thất bại: " . odbc_errormsg());
}
