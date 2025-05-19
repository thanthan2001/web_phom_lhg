<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';

$user = $_SESSION['user'] ?? null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lend Manage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>

    </style>
</head>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php' ?>
        <div class="content">
            <div class="main-header">
                <div>
                    <?php include_once '../public/partials/header.php' ?>
                </div>
            </div>
            <div class="main-content" style="padding-top: 60px;">
                <div class="container mt-4">

                    <!-- Form tìm kiếm -->
                    <form class="row g-3 align-items-center mb-4">
                        <div class="col-auto">
                            <label for="materialCode" class="col-form-label">Mã vật tư:</label>
                        </div>
                        <div class="col-auto">
                            <input type="text" id="materialCode" name="materialCode" class="form-control">
                        </div>
                        <div class="col-auto">
                            <label for="borrowDate" class="col-form-label">Ngày:</label>
                        </div>
                        <div class="col-auto">
                            <input type="date" id="borrowDate" name="borrowDate" class="form-control">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary px-4">Tìm kiếm</button>
                        </div>
                    </form>

                    <!-- Bảng kết quả -->
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-info">
                                <tr>
                                    <th scope="col">Mã vật tư</th>
                                    <th scope="col">Tên Phorm</th>
                                    <th scope="col">Ngày mượn</th>
                                    <th scope="col">Tổng số lượng</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Xem chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dữ liệu đơn mượn sẽ được đổ vào đây -->
                                <tr>
                                    <td colspan="6" class="text-muted">Không có dữ liệu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
        }
    </script>
</body>

</html>