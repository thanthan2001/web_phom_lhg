<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuyển mượn phom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>

    </style>
</head>

<style>
    .input-group-text {
        background-color: transparent !important;
    }

    .table .form-control {
        border: none;
        box-shadow: none;
    }

    .input-group .input-group-text {
        border-right: none;
    }

    .input-group .form-control {
        border-left: none;
        box-shadow: none !important;
        outline: none;
    }

    .input-group .form-control:focus {
        box-shadow: none;
        outline: none;
    }

    .input-group .form-select {
        border-left: none;
        box-shadow: none !important;
        outline: none;
    }

    .input-group .form-select:focus {
        box-shadow: none !important;
        outline: none;
    }
</style>

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
                <div class="container-fluid">
                    <!-- Đơn vị chuyển -->
                    <h5 class="fw-bold mt-4">ĐƠN VỊ CHUYỂN</h5>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Số thẻ:</strong></span>
                                <input type="text" class="form-control" name="cardNumberFrom">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Tên người chuyển:</strong></span>
                                <input type="text" class="form-control" name="senderName">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Đơn vị:</strong></span>
                                <select class="form-select" name="unit" id="unitSelect">
                                    <option selected disabled>Chọn đơn vị</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Ngày chuyển:</strong></span>
                                <input type="date" class="form-control" name="transferDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Cán bộ xác nhận:</strong></span>
                                <input type="text" class="form-control" name="officerFrom">
                            </div>
                        </div>
                    </div>

                    <!-- Đơn vị nhận -->
                    <h5 class="fw-bold mt-6">ĐƠN VỊ NHẬN</h5>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Số thẻ:</strong></span>
                                <input type="text" class="form-control" name="cardNumberTo">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Tên người mượn:</strong></span>
                                <input type="text" class="form-control" name="borrowerName">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Đơn vị:</strong></span>
                                <select class="form-select" name="unit" id="unitSelect">
                                    <option selected disabled>Chọn đơn vị</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Ngày mượn:</strong></span>
                                <input type="date" class="form-control" name="borrowDate">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Cán bộ xác nhận:</strong></span>
                                <input type="text" class="form-control" name="officerTo">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Mã vật tư:</strong></span>
                                <input type="text" class="form-control" id="itemCodeInput">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><strong>Tổng số lượng:</strong></span>
                                <input type="number" class="form-control" id="quantityInput">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng danh sách -->
                <div class="table-responsive mt-4">
                    <table class="table table-bordered text-center align-middle">
                        <thead style="background-color: #bde0f6;">
                            <tr>
                                <th>Mã dạng phom</th>
                                <th>Tên phom</th>
                                <th>Size</th>

                                <th>Chất liệu</th>
                                <th>Tồn kho</th>
                                <th>Số lượng</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">

                <button type="submit" class="btn btn-primary px-4">Chuyển mượn</button>
            </div>

        </div>

    </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
    </script>
</body>

</html>