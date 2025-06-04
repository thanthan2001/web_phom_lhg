<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
include_once __DIR__ . '/../configs/api.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';
$response = json_decode(callAPI("layTatCaPhom", ["companyName" => $companyName]), true);
$data = isset($response["data"]) ? $response["data"] : [];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Phom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>

    </style>
</head>

<style>
    .table-custom-blue thead {
        background-color: #064569D5;
        color: white;
    }

    .table-custom-blue tbody tr:hover {
        background-color: #FFF9C4;
        cursor: pointer;
    }

    .table-custom-blue tbody tr.selected {
        background-color: #FFF176 !important;
    }

    .table-custom-blue th,
    .table-custom-blue td {
        vertical-align: middle;
        text-align: center;
        border: 0.5px solid #adb5bd !important;
    }

    .table-custom-blue {
        border-collapse: collapse;
    }

    .search-box {
        display: flex;
        gap: 10px;
        align-items: center;
        padding-bottom: 10px;
    }

    .search-box input {
        padding: 4px 10px;
        border-radius: 4px;
        border: 1px solid #ccc;
        height: 36px;
        width: 200px;
    }

    .search-box button {
        height: 36px;
        width: 36px;
        background-color: #064469;
        color: white;
        border: none;
        border-radius: 4px;
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
                <?php if ($response["status"] !== "Success"): ?>
                    <div class="alert alert-warning">
                        <?= htmlspecialchars(isset($response["message"]) ? $response["message"] : "Không thể lấy dữ liệu.") ?>
                    </div>
                <?php else: ?>
                    <div class="search-box">
                        <input type="text" id="searchInput" pclass="form-control" placeholder="Nhập để tìm kiếm..." aria-label="Search...">
                        <button class="btn btn-sm btn-outline-secondary" onclick="handleSearchClick()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-custom-blue align-middle">
                            <thead class="">
                                <tr>
                                    <th>Mã dạng phom</th>
                                    <th>Size</th>
                                    <th>Tên Phom</th>
                                    <th>Chất Liệu</th>
                                    <th>Loại Phom</th>
                                    <th>Kệ</th>
                                    <th>Trái</th>
                                    <th>Phải</th>
                                    <th>Số đôi</th>
                                    <th>Tổng SL</th>
                                    <th>Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $phom): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($phom["LastNo"]) ?></td>
                                        <td><?= htmlspecialchars($phom["LastSize"]) ?></td>
                                        <td><?= htmlspecialchars($phom["LastName"]) ?></td>
                                        <td><?= htmlspecialchars($phom["Material"]) ?></td>
                                        <td><?= htmlspecialchars($phom["LastType"]) ?></td>
                                        <td><?= htmlspecialchars($phom["ShelfName"]) ?></td>
                                        <td><?= $phom["QtyLeft"] ?></td>
                                        <td><?= $phom["QtyRight"] ?></td>
                                        <td><?= $phom["TotalPairs"] ?></td>
                                        <td><?= $phom["TotalQty"] ?></td>
                                        <td><?= $phom["QtyInStock"] ?></td>
                                    </tr>
                                <?php endforeach ?>
                                <tr id="noResultsRow" style="display: none;">
                                    <td colspan="11" class="text-center text-muted">Không tìm thấy...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
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

        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.table-custom-blue tbody tr');
            rows.forEach(row => {
                row.addEventListener('click', () => {
                    rows.forEach(r => r.classList.remove('selected'));
                    row.classList.add('selected');
                });
            });
        });

        document.getElementById('searchInput').addEventListener('input', function() {
            filterTable();
        });

        function handleSearchClick() {
            filterTable();
        }

        function filterTable() {
            const filter = document.getElementById('searchInput').value.trim().toLowerCase();
            const rows = document.querySelectorAll('.table-custom-blue tbody tr');
            const noResultsRow = document.getElementById('noResultsRow');

            let matchCount = 0;

            rows.forEach(row => {
                if (row.id === 'noResultsRow') return;

                const maPhom = row.cells[0].textContent.trim().toLowerCase();
                const matched = maPhom.includes(filter);
                row.style.display = matched ? '' : 'none';
                if (matched) matchCount++;
            });

            noResultsRow.style.display = matchCount === 0 ? '' : 'none';
        }
    </script>
</body>

</html>