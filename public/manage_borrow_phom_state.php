<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
include_once __DIR__ . '/../configs/api.php';

// Hàm định dạng số thông minh: hiển thị số thập phân nếu có, ngược lại thì không
function format_number_smart($number)
{
    $number = (float) $number;
    // Nếu số có phần thập phân khác 0, hiển thị 1 chữ số sau dấu phẩy
    if (fmod($number, 1) !== 0.0) {
        return number_format($number, 1, '.', ',');
    } else {
        return number_format($number, 0, '.', ',');
    }
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';

$response = json_decode(callAPI("getBorrowPhomState", ["companyName" => $companyName]), true);
$data = isset($response["data"]) ? $response["data"] : [];

$phomTrongKho = isset($data['danhSachPhomTrongKho']) ? $data['danhSachPhomTrongKho'] : [];
$donMuon = isset($data['danhSachDonMuon']) ? $data['danhSachDonMuon'] : [];

$stockLookup = [];
$totalPairsLookup = [];
// Tạo bảng tra cứu tồn kho và tổng số lượng từ danh sách phom trong kho
foreach ($phomTrongKho as $phom) {
    if (isset($phom['LastNo']) && isset($phom['LastSize'])) {
        $key = trim($phom['LastNo']) . '|' . trim($phom['LastSize']);
        
        if (isset($phom['QtyInStock_Pairs'])) {
            $stockLookup[$key] = (float) $phom['QtyInStock_Pairs'];
        }
        if (isset($phom['TotalPairs'])) {
            $totalPairsLookup[$key] = (float) $phom['TotalPairs'];
        }
    }
}

$borrowedByDept = [];
$totalBorrowedSum = 0;
$totalInStockSum = 0;
$grandTotalAllSum = 0;

// Xử lý dữ liệu từ danh sách đơn mượn
foreach ($donMuon as $item) {
    if (!isset($item['DepName'], $item['LastName'], $item['LastSize'], $item['TotalPairsScanned'])) {
        continue;
    }
    
    if ((float)$item['TotalPairsScanned'] == 0) {
        continue;
    }

    $depName = trim($item['DepName']);
    $lastName = trim($item['LastName']);
    $lastSize = trim($item['LastSize']);
    $borrowedQty = (float) $item['TotalPairsScanned'];
    $totalBorrowedSum += $borrowedQty;

    $lastNo = $lastName;
    $pos = strpos($lastName, '(');
    if ($pos !== false) {
        $lastNo = trim(substr($lastName, 0, $pos));
    }

    if (!isset($borrowedByDept[$depName])) {
        $borrowedByDept[$depName] = [];
    }
    if (!isset($borrowedByDept[$depName][$lastNo])) {
        $borrowedByDept[$depName][$lastNo] = [
            'lastName' => $lastName,
            'totalBorrowed' => 0,
            'details' => []
        ];
    }

    $lookupKey = $lastNo . '|' . $lastSize;
    $inStockQty = isset($stockLookup[$lookupKey]) ? $stockLookup[$lookupKey] : 0;
    $totalPairsQty = isset($totalPairsLookup[$lookupKey]) ? $totalPairsLookup[$lookupKey] : ($borrowedQty + $inStockQty);

    $borrowedByDept[$depName][$lastNo]['details'][] = [
        'size' => $lastSize,
        'borrowed' => $borrowedQty,
        'inStock' => $inStockQty,
        'total' => $totalPairsQty
    ];

    $borrowedByDept[$depName][$lastNo]['totalBorrowed'] += $borrowedQty;
}

foreach ($borrowedByDept as &$lastTypes) { 
    foreach ($lastTypes as &$lastData) { 
        if (isset($lastData['details']) && is_array($lastData['details'])) {
            usort($lastData['details'], function ($a, $b) {
                $sizeA = (float)$a['size'];
                $sizeB = (float)$b['size'];
                if ($sizeA == $sizeB) return 0;
                return ($sizeA < $sizeB) ? -1 : 1;
            });
        }
    }
}
unset($lastData, $lastTypes);

// Tính toán các số liệu tổng quan
foreach ($phomTrongKho as $phom) {
    // SỬA LỖI: Thay thế toán tử ?? bằng isset() để tương thích với PHP cũ
    $totalInStockSum += (float)(isset($phom['QtyInStock_Pairs']) ? $phom['QtyInStock_Pairs'] : 0);
    $grandTotalAllSum += (float)(isset($phom['TotalPairs']) ? $phom['TotalPairs'] : 0);
}

ksort($borrowedByDept);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tình trạng mượn phom</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>

<style>
    body {
        background-color: #f8f9fa;
    }
    .table-sticky-header thead {
        background-color: #2c3e50;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .table-sticky-header th, .table-sticky-header td {
        vertical-align: middle;
        text-align: center;
        border: 1px solid #dee2e6;
    }
    .search-box {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .summary-card {
        border-left: 5px solid;
        border-radius: 8px;
    }
    .accordion-button {
        background-color: #e7f1ff !important;
        color: #0d6efd !important;
        font-weight: bold;
    }
    .accordion-button:not(.collapsed) {
        background-color: #0d6efd !important;
        color: white !important;
    }
    .total-row {
        background-color: #f0f0f0;
        font-weight: bold;
    }
</style>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php' ?>
        <div class="content">
            <div class="main-header">
                <?php include_once '../public/partials/header.php' ?>
            </div>
            <div class="main-content container-fluid" style="padding-top: 70px;">

                <?php if ($response["status"] !== "Success"): ?>
                    <div class="alert alert-warning">
                        <?php echo htmlspecialchars(isset($response["message"]) ? $response["message"] : "Không thể lấy dữ liệu.") ?>
                    </div>
                <?php else: ?>
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm summary-card" style="border-color: #17a2b8;">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Tổng SL Phom (Đôi)</h5>
                                    <p class="card-text fs-2 fw-bold text-info"><?php echo format_number_smart($grandTotalAllSum) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm summary-card" style="border-color: #dc3545;">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Tổng Đã Mượn (Đôi)</h5>
                                    <p class="card-text fs-2 fw-bold text-danger"><?php echo format_number_smart($totalBorrowedSum) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm summary-card" style="border-color: #28a745;">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Tổng Tồn Kho (Đôi)</h5>
                                    <p class="card-text fs-2 fw-bold text-success"><?php echo format_number_smart($totalInStockSum) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body search-box">
                            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm theo tên bộ phận, mã phom...">
                        </div>
                    </div>
                    
                    <?php if (empty($borrowedByDept)): ?>
                        <div class="alert alert-info">Chưa có đơn vị nào mượn phom hoặc tất cả đã được trả.</div>
                    <?php else: ?>
                    <div class="accordion" id="departmentAccordion">
                        <?php foreach ($borrowedByDept as $depName => $lastTypes): ?>
                            <div class="accordion-item" data-search-term="<?php echo strtolower(htmlspecialchars($depName)) ?>">
                                <h2 class="accordion-header" id="heading-<?php echo md5($depName) ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo md5($depName) ?>" aria-expanded="false" aria-controls="collapse-<?php echo md5($depName) ?>">
                                        <?php echo htmlspecialchars($depName) ?>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo md5($depName) ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo md5($depName) ?>" data-bs-parent="#departmentAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sticky-header align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Mã Phom</th>
                                                        <th>Tên Phom</th>
                                                        <th>Size</th>
                                                        <th>Tổng SL (Đôi)</th>
                                                        <th>Đã Mượn (Đôi)</th>
                                                        <th>Tồn Kho (Đôi)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($lastTypes as $lastNo => $lastData): ?>
                                                        <?php
                                                        $totalStockForLastNo = 0;
                                                        $totalAllForLastNo = 0;
                                                        ?>
                                                        <?php foreach ($lastData['details'] as $detail): ?>
                                                            <?php
                                                            $totalStockForLastNo += $detail['inStock'];
                                                            $totalAllForLastNo += $detail['total'];
                                                            ?>
                                                            <tr data-search-term="<?php echo strtolower(htmlspecialchars($lastNo . ' ' . $lastData['lastName'])) ?>">
                                                                <td><?php echo htmlspecialchars($lastNo) ?></td>
                                                                <td><?php echo htmlspecialchars($lastData['lastName']) ?></td>
                                                                <td><?php echo htmlspecialchars($detail['size']) ?></td>
                                                                <td class="text-info fw-bold"><?php echo format_number_smart($detail['total']) ?></td>
                                                                <td class="text-danger fw-bold"><?php echo format_number_smart($detail['borrowed']) ?></td>
                                                                <td class="text-success fw-bold"><?php echo format_number_smart($detail['inStock']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <tr class="total-row">
                                                            <td colspan="2"><strong>Tổng cộng: <?php echo htmlspecialchars($lastNo) ?></strong></td>
                                                            <td></td>
                                                            <td class="text-info"><strong><?php echo format_number_smart($totalAllForLastNo) ?></strong></td>
                                                            <td class="text-danger"><strong><?php echo format_number_smart($lastData['totalBorrowed']) ?></strong></td>
                                                            <td class="text-success"><strong><?php echo format_number_smart($totalStockForLastNo) ?></strong></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div id="noResults" class="alert alert-info mt-3" style="display: none;">Không tìm thấy kết quả phù hợp.</div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.trim().toLowerCase();
            const accordionItems = document.querySelectorAll('#departmentAccordion .accordion-item');
            let hasVisibleItems = false;

            accordionItems.forEach(item => {
                const departmentName = item.dataset.searchTerm;
                const tableRows = item.querySelectorAll('tbody tr:not(.total-row)');
                let departmentMatches = departmentName.includes(filter);
                let hasVisibleRows = false;

                tableRows.forEach(row => {
                    const rowContent = row.dataset.searchTerm;
                    if (filter === '' || rowContent.includes(filter) || departmentMatches) {
                        row.style.display = '';
                        hasVisibleRows = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const totalRows = item.querySelectorAll('.total-row');
                totalRows.forEach(totalRow => {
                    const lastNoInRow = totalRow.querySelector('td:first-child').textContent.toLowerCase();
                    let showTotalRow = false;
                    item.querySelectorAll(`tr[data-search-term*="${lastNoInRow.split(': ')[1]}"]`).forEach(detailRow => {
                        if (detailRow.style.display !== 'none') {
                            showTotalRow = true;
                        }
                    });
                    totalRow.style.display = showTotalRow ? '' : 'none';
                });

                if (departmentMatches || hasVisibleRows) {
                    item.style.display = '';
                    hasVisibleItems = true;
                    if (filter) {
                        const collapseElement = item.querySelector('.accordion-collapse');
                        if (!collapseElement.classList.contains('show')) {
                            new bootstrap.Collapse(collapseElement, { toggle: false }).show();
                        }
                    }
                } else {
                    item.style.display = 'none';
                }
            });

            document.getElementById('noResults').style.display = hasVisibleItems ? 'none' : 'block';
        });
    </script>
</body>

</html>