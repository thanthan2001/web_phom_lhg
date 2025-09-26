<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
include_once __DIR__ . '/../configs/api.php';

// Quan trọng: Bạn cần thêm API endpoint mới vào file /configs/api.php
// Ví dụ: "getAllLastNo" => "http://localhost:8181/api/phom/getAllLastNo",

// Hàm định dạng số thông minh
function format_number_smart($number)
{
    $number = (float) $number;
    if (fmod($number, 1) !== 0.0) {
        return number_format($number, 1, '.', ',');
    } else {
        return number_format($number, 0, '.', ',');
    }
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';

// Lấy LastNo được chọn từ query string (nếu có)
$selectedLastNo = isset($_GET['last_no']) ? trim($_GET['last_no']) : '';

// --- BƯỚC 1: LẤY DANH SÁCH LastNo ĐỂ ĐIỀN VÀO DROPDOWN TỪ API MỚI ---
$lastNoApiResponse = json_decode(callAPI("getAllLastNo", ["companyName" => $companyName]), true);
$uniqueLastNos = [];

// Kiểm tra xem API có trả về dữ liệu hợp lệ không
if (isset($lastNoApiResponse['status']) && $lastNoApiResponse['status'] === 'Success' && !empty($lastNoApiResponse['data']['jsonArray'])) {
    $jsonArray = $lastNoApiResponse['data']['jsonArray'];
    
    // Lấy cột 'LastNo' từ mảng kết quả
    $lastNos = array_column($jsonArray, 'LastNo');
    
    // Làm sạch khoảng trắng thừa ở mỗi LastNo
    $lastNos = array_map('trim', $lastNos);
    
    // Lọc ra các giá trị duy nhất và sắp xếp lại
    $uniqueLastNos = array_unique($lastNos);
    sort($uniqueLastNos);
}

// --- BƯỚC 2: LẤY DỮ LIỆU ĐỂ HIỂN THỊ TRÊN BẢNG (TỪ API controlPhom) ---
// Chuẩn bị tham số để gọi API lấy dữ liệu cho bảng
$tableApiParams = ["companyName" => $companyName];

// Nếu người dùng đã chọn một LastNo cụ thể, thêm nó vào tham số
if (!empty($selectedLastNo)) {
    $tableApiParams["LastNo"] = $selectedLastNo;
}

// Gọi API 'controlPhom' để lấy dữ liệu hiển thị trên bảng
$response = json_decode(callAPI("controlPhom", $tableApiParams), true);
$dataToDisplay = isset($response["data"]) ? $response["data"] : [];


$sizeHeaders = [];
// Xử lý dữ liệu để lấy danh sách các size làm tiêu đề cột
if (!empty($dataToDisplay)) {
    $firstItemKeys = array_keys($dataToDisplay[0]);
    $sizeHeaders = array_filter($firstItemKeys, function ($key) {
        return !in_array($key, ['LastNo', 'DepName']);
    });
    sort($sizeHeaders, SORT_NUMERIC);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê kiểm soát phom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</head>
<style>
body {
    background-color: #f8f9fa;
}

.table-sticky-header thead {
    background-color: #2c3e50;
    /* Màu xanh đậm chuyên nghiệp */
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-sticky-header th,
.table-sticky-header td {
    vertical-align: middle;
    text-align: center;
    border: 1px solid #dee2e6;
    white-space: nowrap;
}

/* === CẢI TIẾN GIAO DIỆN === */

/* Style cho dòng tổng tồn kho */
.inventory-row {
    background-color: #87a5c5ff !important;
    /* Màu xám nhạt (Bootstrap table-light) */
    font-weight: bold;
}

/* Style cho dòng tổng chưa trả */
.total-row {
    background-color: #c7d190ff !important;
    /* Màu xanh dương nhạt (Bootstrap table-info) */
    font-weight: bold;
}

/* Không còn dùng đến class này, xóa đi cho gọn */
/* .remaining-row { ... } */
</style>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php' ?>
        <div class="content">
            <div class="main-header">
                <?php include_once '../public/partials/header.php' ?>
            </div>
            <div class="main-content container-fluid" style="padding-top: 70px;">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Bộ lọc và tìm kiếm</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <!-- Form để gửi lựa chọn LastNo -->
                                <form id="filterForm" action="" method="GET">
                                    <label for="lastNoSelect" class="form-label">Chọn Mã Phom</label>
                                    <select id="lastNoSelect" name="last_no" class="form-select">
                                        <option value="">-- Tất cả Mã Phom --</option>
                                        <?php foreach ($uniqueLastNos as $lastNo) : ?>
                                        <option value="<?php echo htmlspecialchars($lastNo); ?>"
                                            <?php echo ($lastNo == $selectedLastNo) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($lastNo); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <label for="searchInput" class="form-label">Tìm kiếm trong bảng</label>
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Tìm kiếm theo Đơn vị...">
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!isset($response["status"]) || $response["status"] !== "Success" || empty($dataToDisplay)) : ?>
                <div class="alert alert-warning">
                    <?php echo htmlspecialchars(isset($response["message"]) ? $response["message"] : "Không có dữ liệu để hiển thị hoặc không thể lấy dữ liệu.") ?>
                </div>
                <?php else : ?>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="phomTable"
                                class="table table-bordered table-hover table-sticky-header align-middle">
                                <thead>
                                    <tr>
                                        <th>Mã Phom</th>
                                        <th>Đơn vị</th>
                                        <?php foreach ($sizeHeaders as $size) : ?>
                                        <th><?php echo htmlspecialchars(trim($size)); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dataToDisplay as $row) : ?>
                                    <?php
                                            // === CẢI TIẾN LOGIC GÁN CLASS CHO CÁC DÒNG TỔNG ===
                                            $rowClass = '';
                                            $depNameTrimmed = trim($row['DepName']);
                                            if ($depNameTrimmed === 'Total Inventory') {
                                                $rowClass = 'inventory-row';
                                            } elseif ($depNameTrimmed === 'Total') {
                                                $rowClass = 'total-row';
                                            }
                                            
                                            // Dữ liệu cho ô tìm kiếm vẫn giữ nguyên
                                            $searchTerm = strtolower(trim($row['LastNo']) . ' ' . $depNameTrimmed);
                                            ?>
                                    <tr class="<?php echo $rowClass; ?>"
                                        data-search-term="<?php echo htmlspecialchars($searchTerm); ?>">
                                        <td><?php echo htmlspecialchars(trim($row['LastNo'])); ?></td>
                                        <td><?php echo htmlspecialchars($depNameTrimmed); ?></td>
                                        <?php foreach ($sizeHeaders as $size) : ?>
                                        <td>
                                            <?php
                                                        $quantity = isset($row[$size]) ? $row[$size] : 0;
                                                        echo format_number_smart($quantity);
                                                        ?>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="noResults" class="alert alert-info mt-3" style="display: none;">Không tìm thấy kết quả
                            phù hợp.</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
    // Tự động submit form khi người dùng thay đổi lựa chọn trong dropdown
    document.getElementById('lastNoSelect').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // Giữ nguyên chức năng tìm kiếm trên bảng dữ liệu hiện tại
    document.getElementById('searchInput').addEventListener('input', function() {
        const filter = this.value.trim().toLowerCase();
        const tableRows = document.querySelectorAll('#phomTable tbody tr');
        let hasVisibleRows = false;

        tableRows.forEach(row => {
            // Sửa lại để chỉ tìm kiếm theo DepName (Đơn vị) như placeholder gợi ý
            const depName = row.cells[1].textContent.trim().toLowerCase();
            if (filter === '' || depName.includes(filter)) {
                row.style.display = '';
                hasVisibleRows = true;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('noResults').style.display = hasVisibleRows ? 'none' : 'block';
    });
    </script>
</body>

</html>