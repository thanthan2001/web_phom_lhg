<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
include_once __DIR__ . '/../configs/api.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';

// Khởi tạo $response với giá trị mặc định để tránh lỗi "Undefined variable"
$response = ["status" => "Error", "message" => "Không thể lấy dữ liệu từ API."];
$apiResult = callAPI("layTatCaPhom", ["companyName" => $companyName]);

// Kiểm tra kết quả trả về từ API trước khi giải mã JSON
if ($apiResult !== false && $apiResult !== null) {
    $decodedResponse = json_decode($apiResult, true);
    // Kiểm tra xem việc giải mã JSON có thành công không
    if (json_last_error() === JSON_ERROR_NONE) {
        $response = $decodedResponse;
    } else {
        $response["message"] = "Dữ liệu trả về từ API không phải là JSON hợp lệ: " . json_last_error_msg();
        error_log("Lỗi giải mã JSON: " . json_last_error_msg() . " - Kết quả API: " . $apiResult);
    }
} else {
    $response["message"] = "Lỗi khi gọi API layTatCaPhom. Vui lòng kiểm tra kết nối hoặc đường dẫn API.";
    error_log("Lỗi gọi API layTatCaPhom. Kết quả: " . var_export($apiResult, true));
}

$data = isset($response["data"]) ? $response["data"] : [];

$totalPairsSum = 0;
$groupedData = [];
foreach ($data as $phom) {
    $groupedData[$phom['LastNo']][] = $phom;
    $totalPairsSum += $phom['TotalPairs'];
}

/**
 * Hàm định dạng số: chỉ hiển thị phần thập phân nếu nó khác 0.
 * Ví dụ: 5.0 -> "5", 5.5 -> "5.5"
 * @param float|int $number Số cần định dạng
 * @return string Số đã được định dạng
 */
function formatNumberAuto($number) {
    // Nếu số đó là số nguyên (không có phần dư)
    if (fmod($number, 1) == 0) {
        return number_format($number, 0); // Định dạng không có số thập phân
    } else {
        return number_format($number, 1); // Định dạng có 1 số thập phân
    }
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
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
                <?php if ($response["status"] !== "Success"): ?>
                <div class="alert alert-warning">
                    <?= htmlspecialchars(isset($response["message"]) ? $response["message"] : "Không thể lấy dữ liệu.") ?>
                </div>
                <?php else: ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="form-control" placeholder="Nhập để tìm kiếm..."
                            aria-label="Search...">
                        <button class="btn btn-sm btn-outline-secondary" onclick="handleSearchClick()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="fw-bold">
                        Tổng số đôi: <?= formatNumberAuto($totalPairsSum) ?>
                    </div>
                </div>

                <div class="table-responsive" id="phomTableContainer">
                    <table class="table table-bordered table-hover table-custom-blue align-middle">
                        <thead class="">
                            <tr>
                                <th>Mã dạng phom</th>
                                <th>Size</th>
                                <th>Tên Phom</th>
                                <th>Chất Liệu</th>
                                <th>Loại Phom</th>
                                <th>Tổng SL</th>
                                <th>Trong kho</th>
                                <th>Ngoài kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedData as $lastNo => $items): ?>
                            <?php
                                    $totalPairsByLastNo = 0;
                                    $totalStockByLastNo = 0;
                                    ?>
                            <?php foreach ($items as $phom): ?>
                            <?php
                                        $totalPairsByLastNo += $phom['TotalPairs'];
                                        $totalStockByLastNo += $phom['QtyInStock_Pairs'];
                                        ?>
                            <tr>
                                <td><?= htmlspecialchars($phom["LastNo"]) ?></td>
                                <td><?= htmlspecialchars($phom["LastSize"]) ?></td>
                                <td><?= htmlspecialchars($phom["LastName"]) ?></td>
                                <td><?= htmlspecialchars($phom["Material"]) ?></td>
                                <td><?= htmlspecialchars($phom["LastType"]) ?></td>
                                <td><?= formatNumberAuto($phom["TotalPairs"]) ?></td>
                                <td><?= formatNumberAuto($phom["QtyInStock_Pairs"]) ?></td>
                                <td><?= formatNumberAuto($phom["TotalPairs"] - $phom["QtyInStock_Pairs"]) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr style="background-color: #AFECD1FF; font-weight: bold;">
                                <td><?= htmlspecialchars($lastNo) ?></td>
                                <td>Tất cả size</td>
                                <td><?= htmlspecialchars(isset($items[0]["LastName"]) ? $items[0]["LastName"] : '') ?>
                                </td>
                                <td><?= htmlspecialchars(isset($items[0]["Material"]) ? $items[0]["Material"] : '') ?>
                                </td>
                                <td><?= htmlspecialchars(isset($items[0]["LastType"]) ? $items[0]["LastType"] : '') ?>
                                </td>
                                <td><?= formatNumberAuto($totalPairsByLastNo) ?></td>
                                <td><?= formatNumberAuto($totalStockByLastNo) ?></td>
                                <td><?= formatNumberAuto($totalPairsByLastNo - $totalStockByLastNo) ?></td>
                            </tr>
                            <?php endforeach; ?>
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
        const phomTableContainer = document.getElementById('phomTableContainer'); // Lấy phần tử chứa bảng

        rows.forEach(row => {
            // Thêm một lớp để phân biệt dòng tổng kết
            if (row.style.backgroundColor === 'rgb(175, 236, 209)') {
                row.classList.add('summary-row');
            }

            row.addEventListener('click', (event) => {
                event.stopPropagation();
                if (!row.classList.contains('summary-row')) {
                    rows.forEach(r => r.classList.remove('selected'));
                    row.classList.add('selected');
                }
            });
        });

        // Lắng nghe sự kiện click 
        document.addEventListener('click', (event) => {
            if (!phomTableContainer.contains(event.target)) {
                rows.forEach(r => r.classList.remove('selected'));
            }
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
            if (row.id === 'noResultsRow') {
                return;
            }

            const maPhom = row.cells[0].textContent.trim().toLowerCase();
            const matched = maPhom.includes(filter);
            row.style.display = matched ? '' : 'none';
            if (matched) {
                matchCount++;
            }
        });

        noResultsRow.style.display = matchCount === 0 ? '' : 'none';
    }
    </script>
</body>

</html>