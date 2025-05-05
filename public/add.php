<?php
include '../configs/conn.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Chỉ chèn tên, mã sinh viên tự động sinh
    $sql = "INSERT INTO sinhvien (ho_ten_sv) VALUES ('$name')";
    odbc_exec($conn, $sql);
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THÊM MỚI</title>
</head>

<body>
    <h2>Thêm User</h2>
    <form method="POST">
        <input type="text" name="name" required>
        <button type="submit">Thêm</button>
    </form>
</body>

</html>