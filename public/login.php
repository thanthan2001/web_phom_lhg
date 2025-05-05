<?php
include '../configs/conn.php'; // Assuming you have a conn.php for ODBC connection
include '../public/function.php'; // Assuming you have a function.php with mystrreplace

session_start();
@$uid = trim($_POST['username']);
@$pwd = trim($_POST['password']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./../public/css/login.css"> <!-- Assuming you have a login.css -->
</head>

<body>
    <main>
        <?php
        $msg = "";
        @$userid = mystrreplace($uid); // Sanitize input
        @$password = $pwd; // Sanitize input (consider using password_hash/password_verify for real passwords)

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($userid) || empty($password)) {
                $msg = "Please enter your username and password.";
            } else {
                // ODBC Query (Adjust table and column names if needed)
                $query = "SELECT ma_sinh_vien, ho_ten_sv, sdt, password, dia_chi FROM sinhvien WHERE rtrim(ltrim(sdt)) = '" . trim($userid) . "' AND rtrim(ltrim(password)) = '" . trim($password) . "'";


                $result = odbc_exec($conn, $query); // Execute the query using the connection from conn.php

                if ($result) {
                    $count = odbc_num_rows($result);

                    if ($count > 0) {
                        // Login successful
                        $ma_sinh_vien = trim(odbc_result($result, "ma_sinh_vien")); // Adjusted to use column names
                        $ho_ten_sv = trim(odbc_result($result, "ho_ten_sv"));     // Adjusted to use column names
                        $sdt = trim(odbc_result($result, "sdt"));              // Adjusted to use column names
                        $dia_chi = trim(odbc_result($result, "dia_chi"));        // Adjusted to use column names
                        $_SESSION["ma_sinh_vien"] = $ma_sinh_vien;
                        $_SESSION["ho_ten_sv"] = $ho_ten_sv;
                        $_SESSION["sdt"] = $sdt;
                        $_SESSION["dia_chi"] = $dia_chi;
                        $_SESSION["login"] = "true"; // Set a login session variable

                        $msg = "Login successful! Welcome, " . $ho_ten_sv . ".";
                        // Redirect to a member area or display a welcome message
                        header("Location: index.php"); // Redirect to welcome page

                    } else {
                        // Login failed
                        $msg = "Login failed. Please check your username and password.";
                    }

                    odbc_free_result($result); // Free the result set
                } else {
                    $msg = "Error executing the query: " . odbc_errormsg($conn);
                }
            }
        }
        ?>

        <div class="login_modal container-fluid py-4">
            <div class="container">
                <div class="text-black row g-5 align-items-center justify-content-center">
                    <form class="col-lg-6 login_form py-3" method="POST" action="">
                        <div class="row g-3 ">
                            <div class="col-12">
                                <label for="username">Username (SDT)</label>
                                <input type="text" class="form-control" name="username" placeholder="Username"
                                    value="<?php echo htmlspecialchars($uid); ?>" required>
                            </div>
                            <div class="col-12">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Password"
                                    required>
                            </div>
                            <div class="form__input-wrapper text-right d-flex justify-content-between">
                                <p>Chưa Có Tài Khoản ?</p>
                                <a href="register.php" data-box="acc-recovery-box"
                                    class="btn btn-orange text-black btn-recovery acc-trigger js-acc">Đăng Ký
                                    Ngay!</a>
                            </div>
                            <div class="col-12 text-center">
                                <?php if (!empty($msg)) { ?>
                                    <div class="alert <?php echo ($count > 0) ? 'alert-success' : 'alert-danger'; ?>">
                                        <?php echo $msg; ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-12 mx-2 d-flex justify-content-between w-100 gap-5">
                                <button class="btn bg-secondary text-light w-100 py-1" type="button">Đóng</button>
                                <button class="btn bg-warning text-light w-100 py-1" type="reset">Dọn dẹp</button>
                                <button class="btn bg-success text-light w-100 py-1" type="submit">Đăng Nhập</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </main>

    <footer>
        <!-- Footer Content -->
    </footer>
</body>

</html>