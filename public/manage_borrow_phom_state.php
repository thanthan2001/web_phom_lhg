<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
include_once __DIR__ . '/../configs/api.php';

function format_number_smart($number)
{
    $number = (float) $number;
    if (floor($number) != $number) {
        return number_format($number, 1, '.', ',');
    } else {
        return number_format($number, 0, '.', ',');
    }
}

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';

$response = json_decode(callAPI("getBorrowPhomState", array("companyName" => $companyName)), true);
$data = isset($response["data"]) ? $response["data"] : array();

$phomTrongKho = isset($data['danhSachPhomTrongKho']) ? $data['danhSachPhomTrongKho'] : array();
$donMuon = isset($data['danhSachDonMuon']) ? $data['danhSachDonMuon'] : array();

$stockLookup = array();
$totalPairsLookup = array();
foreach ($phomTrongKho as $phom) {
    if (isset($phom['LastNo']) && isset($phom['LastSize'])) {
        $key = trim($phom['LastNo']) . '|' . trim($phom['LastSize']);
        if (isset($phom['QtyInStock'])) {
            $stockLookup[$key] = $phom['QtyInStock'];
        }
        if (isset($phom['TotalPairs'])) {
            $totalPairsLookup[$key] = $phom['TotalPairs'];
        }
    }
}

$borrowedByDept = array();
$totalBorrowedSum = 0;
$totalInStockSum = 0;
$grandTotalAllSum = 0;

foreach ($donMuon as $item) {
    if (!isset($item['DepName']) || !isset($item['LastName']) || !isset($item['LastSize']) || !isset($item['TotalPairsScanned'])) {
        continue;
    }
    
    if ((int)$item['TotalPairsScanned'] === 0) {
        continue;
    }

    $depName = trim($item['DepName']);
    $lastName = trim($item['LastName']);
    $lastSize = trim($item['LastSize']);
    $borrowedQty = $item['TotalPairsScanned'];
    $totalBorrowedSum += $borrowedQty;

    $lastNo = $lastName;
    $pos = strpos($lastName, '(');
    if ($pos !== false) {
        $lastNo = trim(substr($lastName, 0, $pos));
    }

    if (!isset($borrowedByDept[$depName])) {
        $borrowedByDept[$depName] = array();
    }
    if (!isset($borrowedByDept[$depName][$lastNo])) {
        $borrowedByDept[$depName][$lastNo] = array(
            'lastName' => $lastName,
            'totalBorrowed' => 0,
            'details' => []
        );
    }

    $lookupKey = $lastNo . '|' . $lastSize;
    $inStockQty = isset($stockLookup[$lookupKey]) ? $stockLookup[$lookupKey] : 0;
    $totalPairsQty = isset($totalPairsLookup[$lookupKey]) ? $totalPairsLookup[$lookupKey] : $borrowedQty + $inStockQty;

    $borrowedByDept[$depName][$lastNo]['details'][] = array(
        'size' => $lastSize,
        'borrowed' => $borrowedQty,
        'inStock' => $inStockQty,
        'total' => $totalPairsQty
    );

    $borrowedByDept[$depName][$lastNo]['totalBorrowed'] += $borrowedQty;
}

foreach ($borrowedByDept as &$lastTypes) { 
    foreach ($lastTypes as &$lastData) { 
        if (isset($lastData['details']) && is_array($lastData['details'])) {
            usort($lastData['details'], function ($a, $b) {
                $sizeA = (float)$a['size'];
                $sizeB = (float)$b['size'];

                if ($sizeA == $sizeB) {
                    return 0;
                }
                return ($sizeA < $sizeB) ? -1 : 1;
            });
        }
    }
}
unset($lastData); 
unset($lastTypes);

foreach ($phomTrongKho as $phom) {
    if (isset($phom['QtyInStock'])) {
        $totalInStockSum += $phom['QtyInStock'];
    }
    if (isset($phom['TotalPairs'])) {
        $grandTotalAllSum += $phom['TotalPairs'];
    }
}

ksort($borrowedByDept);

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage borrow phom state</title>
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
    }

    .table-sticky-header th,
    .table-sticky-header td {
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
    }

    .accordion-button {
        background-color: #e7f1ff !important;
        color: #0d6efd !important;
        font-weight: bold;
    }

    .accordion-button:not(.collapsed) {
        background-color: #064569 !important;
        color: white !important;
    }

    .total-row {
        background-color: #d1e7dd;
        font-weight: bold;
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
            <div class="main-content container-fluid" style="padding-top: 70px;">

                <?php if ($response["status"] !== "Success"): ?>
                    <div class="alert alert-warning">
                        <?php echo htmlspecialchars(isset($response["message"]) ? $response["message"] : "Không thể lấy dữ liệu.") ?>
                    </div>
                <?php elseif (empty($borrowedByDept)): ?>
                    <div class="alert alert-info">Chưa có đơn vị nào mượn phom.</div>
                <?php else: ?>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card shadow-sm summary-card" style="border-color: #6c757d;">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Tổng SL Phom</h5>
                                    <p class="card-text fs-2 fw-bold text-secondary"><?php echo format_number_smart($grandTotalAllSum) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm summary-card" style="border-color: #dc3545;">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Tổng đã mượn</h5>
                                    <p class="card-text fs-2 fw-bold text-danger"><?php echo format_number_smart($totalBorrowedSum) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm summary-card" style="border-color: #198754;">
                                <div class="card-body">
                                    <h5 class="card-title text-muted">Tổng tồn kho</h5>
                                    <p class="card-text fs-2 fw-bold text-success"><?php echo format_number_smart($totalInStockSum) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="search-box mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm theo tên bộ phận, mã phom...">
                    </div>

                    <div class="accordion" id="departmentAccordion">
                        <?php foreach ($borrowedByDept as $depName => $lastTypes): ?>
                            <div class="accordion-item" data-search-term="<?php echo strtolower(htmlspecialchars($depName)) ?>">
                                <h2 class="accordion-header" id="heading-<?php echo md5($depName) ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo md5($depName) ?>" aria-expanded="true" aria-controls="collapse-<?php echo md5($depName) ?>">
                                        <?php echo htmlspecialchars($depName) ?>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo md5($depName) ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo md5($depName) ?>" data-bs-parent="#departmentAccordion">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <!-- === THAY ĐỔI Ở ĐÂY: Cập nhật tên class cho bảng === -->
                                            <table class="table table-bordered table-hover table-sticky-header align-middle">
                                                <thead>
                                                    <tr>
                                                        <th>Mã Dạng Phom</th>
                                                        <th>Tên Phom</th>
                                                        <th>Size</th>
                                                        <th>Tổng SL</th>
                                                        <th>Đã Mượn</th>
                                                        <th>Tồn Kho</th>
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
                                                                <td class="text-secondary fw-bold"><?php echo format_number_smart($detail['total']) ?></td>
                                                                <td class="text-danger fw-bold"><?php echo format_number_smart($detail['borrowed']) ?></td>
                                                                <td class="text-success fw-bold"><?php echo format_number_smart($detail['inStock']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                        <tr class="total-row">
                                                            <td colspan="2"><strong>Tổng cộng cho mã phom: <?php echo htmlspecialchars($lastNo) ?></strong></td>
                                                            <td><strong>Tất cả size</strong></td>
                                                            <td class="text-secondary"><strong><?php echo format_number_smart($totalAllForLastNo) ?></strong></td>
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

        document.getElementById('searchInput').addEventListener('input', function() {
            const filter = this.value.trim().toLowerCase();
            const accordionItems = document.querySelectorAll('#departmentAccordion .accordion-item');
            let hasVisibleItems = false;

            accordionItems.forEach(item => {
                const departmentName = item.dataset.searchTerm;
                const tableRows = item.querySelectorAll('tbody tr:not(.total-row)');
                let departmentMatches = departmentName.includes(filter);
                let rowMatches = 0;

                tableRows.forEach(row => {
                    const rowContent = row.dataset.searchTerm;
                    if (rowContent.includes(filter)) {
                        row.style.display = '';
                        rowMatches++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (departmentMatches || rowMatches > 0) {
                    item.style.display = '';
                    hasVisibleItems = true;
                    if (filter) {
                        const collapseElement = item.querySelector('.accordion-collapse');
                        if (!collapseElement.classList.contains('show')) {
                            new bootstrap.Collapse(collapseElement, {
                                toggle: true
                            });
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