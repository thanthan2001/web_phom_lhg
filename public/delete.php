<?php
include '../configs/conn.php';
session_start();
$id = $_GET['id'];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM sinhvien WHERE ma_sinh_vien='$id'";
    odbc_exec($conn, $sql);
}
header("Location: index.php");
