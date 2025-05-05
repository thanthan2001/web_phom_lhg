<?php
session_start();
include '../configs/conn.php';

$id = $_GET['id'];
$sql = "SELECT * FROM sinhvien WHERE ma_sinh_vien='$id'";
$result = odbc_exec($conn, $sql);
$row = odbc_fetch_array($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $sql = "UPDATE sinhvien SET ho_ten_sv=N'$name' WHERE ma_sinh_vien=N'$id'";
    odbc_exec($conn, $sql);
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h2>Sửa User</h2>
    <form method="POST">
        <input type="text" name="name" value="<?= $row['ho_ten_sv'] ?>" required>
        <button type="submit">Cập nhật</button>
    </form>
</body>

</html>