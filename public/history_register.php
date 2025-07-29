<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
require_once "../configs/api.php";

// Lấy thông tin người dùng từ session (tương thích PHP 5.x)
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : null;
$Userid = isset($user['userID']) ? $user['userID'] : null;

// Gọi API
$responseJson = callAPI('layDonMuonTheoUser', array(
    'companyName' => $companyName,
    'UserID' => $Userid
));
$response = json_decode($responseJson, true);

// --- BƯỚC 1: TẠO DANH SÁCH CÁC MÃ ĐƠN ĐÃ CÓ YÊU CẦU TRẢ ---
$returnedBillIds = array();
if (isset($response['data']['returnBills']) && is_array($response['data']['returnBills'])) {
    foreach ($response['data']['returnBills'] as $returnItem) {
        if (isset($returnItem['ID_BILL']) && !in_array($returnItem['ID_BILL'], $returnedBillIds)) {
            $returnedBillIds[] = $returnItem['ID_BILL'];
        }
    }
}

// --- XỬ LÝ DỮ LIỆU ĐƠN MƯỢN ---
$borrowBillsGrouped = array();
if (isset($response['data']['borrowBills']) && is_array($response['data']['borrowBills'])) {
    foreach ($response['data']['borrowBills'] as $item) {
        $billId = $item['ID_bill'];
        if (!isset($borrowBillsGrouped[$billId])) {
            $borrowBillsGrouped[$billId] = array('meta' => array('ID_bill' => $billId, 'Userid' => $item['Userid'], 'BorrowerName' => $item['BorrowerName'], 'DepName' => $item['DepName'], 'OfficerId' => $item['OfficerId'], 'OfficerName' => $item['OfficerName'], 'isConfirm' => $item['isConfirm'], 'StateLastBill' => $item['StateLastBill'], 'ToTalPhomNotBinding' => (float)$item['ToTalPhomNotBinding'], 'DateBorrow' => $item['DateBorrow'], 'DateReceive' => $item['DateReceive']), 'details' => array());
        }
        $borrowBillsGrouped[$billId]['details'][] = $item;
    }
    usort($borrowBillsGrouped, function ($a, $b) {
        if ($a['meta']['isConfirm'] !== $b['meta']['isConfirm']) {
            return $a['meta']['isConfirm'] ? 1 : -1;
        }
        return strtotime($b['meta']['DateBorrow']) - strtotime($a['meta']['DateBorrow']);
    });
}

// --- BƯỚC 2: LỌC RA CÁC ĐƠN MƯỢN CHƯA BỊ TRẢ ---
$activeBorrowBills = array();
foreach ($borrowBillsGrouped as $group) {
    if (!in_array($group['meta']['ID_bill'], $returnedBillIds)) {
        $activeBorrowBills[] = $group;
    }
}

// --- XỬ LÝ DỮ LIỆU ĐƠN TRẢ ---
$returnBillsGrouped = array();
if (isset($response['data']['returnBills']) && is_array($response['data']['returnBills'])) {
    foreach ($response['data']['returnBills'] as $item) {
        $returnId = $item['ID_Return'];
        if (!isset($returnBillsGrouped[$returnId])) {
            $returnBillsGrouped[$returnId] = array('meta' => array('ID_Return' => $returnId, 'ID_BILL' => $item['ID_BILL'], 'Userid' => $item['Userid'], 'BorrowerName' => $item['BorrowerName'], 'DepName' => $item['DepName'], 'OfficerId' => $item['OfficerId'], 'OfficerName' => $item['OfficerName'], 'isConfirmReturn' => $item['isConfirmReturn'], 'ReturnRequestDate' => $item['ReturnRequestDate'], 'DateBorrow' => $item['DateBorrow']), 'details' => array());
        }
        $returnBillsGrouped[$returnId]['details'][] = $item;
    }
    usort($returnBillsGrouped, function ($a, $b) {
        return strtotime($b['meta']['ReturnRequestDate']) - strtotime($a['meta']['ReturnRequestDate']);
    });
}

// Hàm format số lượng, giữ lại cả 2 phiên bản để tương thích
function formatQuantity($quantity)
{
    return fmod($quantity, 1) !== 0.0 ? number_format($quantity, 1) : number_format($quantity, 0);
}
function format_quantity_detail($quantity)
{
    $quantity = (float)$quantity;
    if ($quantity == floor($quantity)) {
        return number_format($quantity, 0);
    } else {
        return rtrim(rtrim(number_format($quantity, 2), '0'), '.');
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch sử Đăng ký</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <style>
        .lend-card {
            position: relative;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .lend-table {
            background-color: #ffffff;
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: fixed;
        }

        .lend-table th {
            font-weight: 600;
            color: #333;
        }

        .lend-table th,
        .lend-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            word-wrap: break-word;
            vertical-align: middle;
        }

        .search-box {
            display: flex;
            gap: 10px;
            align-items: center;
            padding-bottom: 15px;
        }

        .search-box input {
            padding: 8px 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            height: 38px;
        }

        #searchInput {
            width: 250px;
        }

        #dateInput {
            width: 150px;
        }

        .search-box button {
            height: 38px;
            width: 40px;
            background-color: #064469;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .info-grid {
            display: grid;
            gap: 8px 15px;
            align-items: center;
            margin-bottom: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
        }

        .card-status {
            position: absolute;
            top: 15px;
            right: 15px;
        }

        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            color: #064469;
            font-weight: bold;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .lend-table tbody tr:hover {
            background-color: #F9FCDBFF !important;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php' ?>
        <div class="content">
            <div class="main-header"><?php include_once '../public/partials/header.php' ?></div>
            <div class="main-content" style="padding-top: 60px;">
                <div class="container-fluid">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Nhập mã, tên, số thẻ...">
                        <input type="text" id="dateInput" placeholder="Lọc theo ngày...">
                        <button class="btn btn-primary btn-sm" onclick="filterCards()"><i class="fas fa-search"></i></button>
                    </div>

                    <ul class="nav nav-tabs" id="historyTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="borrow-tab" data-bs-toggle="tab" data-bs-target="#borrow-content" type="button" role="tab">
                                Đơn Mượn (<?php echo count($activeBorrowBills); ?>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="return-tab" data-bs-toggle="tab" data-bs-target="#return-content" type="button" role="tab">
                                Đơn Trả (<?php echo count($returnBillsGrouped); ?>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="historyTabContent">
                        <div class="tab-pane fade show active" id="borrow-content" role="tabpanel">
                            <?php if (empty($activeBorrowBills)): ?>
                                <div class="alert alert-info text-center mt-4"><i class="fas fa-box-open"></i> Không có đơn mượn nào.</div>
                            <?php else: ?>
                                <?php foreach ($activeBorrowBills as $group): ?>
                                    <?php
                                    $meta = $group['meta'];
                                    $details = $group['details'];
                                    if (!empty($meta['isConfirm']) && !empty($meta['StateLastBill'])) {
                                        $borderColor = '#8e44ad';
                                        $backgroundColor = '#f3e5f5';
                                    } elseif (!empty($meta['isConfirm'])) {
                                        $borderColor = '#16a085';
                                        $backgroundColor = '#d1f2eb';
                                    } else {
                                        $borderColor = '#dc3545';
                                        $backgroundColor = '#f8d7da';
                                    }
                                    $cardStyle = "background-color: $backgroundColor; border: 1px solid $borderColor;";
                                    $borrowerName = isset($meta['BorrowerName']) ? $meta['BorrowerName'] : '---';
                                    $depName = isset($meta['DepName']) ? $meta['DepName'] : '---';
                                    $officerId = isset($meta['OfficerId']) ? $meta['OfficerId'] : '---';
                                    $officerName = isset($meta['OfficerName']) ? $meta['OfficerName'] : '---';
                                    $searchContent = strtolower($meta['ID_bill'] . ' ' . $meta['Userid'] . ' ' . $borrowerName . ' ' . $depName . ' ' . $officerId . ' ' . $officerName . ' ' . date('d/m/Y', strtotime($meta['DateBorrow'])) . ' ' . date('d/m/Y', strtotime($meta['DateReceive'])));
                                    ?>
                                    <div class="lend-card borrow-card" style="<?php echo $cardStyle; ?>" data-content="<?php echo htmlspecialchars($searchContent); ?>">
                                        <div class="card-status" style="font-weight: bold; color: <?php echo $meta['isConfirm'] ? 'blue' : 'gray'; ?>;" title="<?php echo $meta['isConfirm'] ? 'Đã duyệt' : 'Chờ duyệt'; ?>">
                                            <?php echo $meta['isConfirm'] ? 'Đã duyệt' : 'Chờ duyệt'; ?>
                                        </div>
                                        <div class="info-grid" style="grid-template-columns: repeat(4, 1fr);">
                                            <div><strong>Mã đơn:</strong> <?php echo htmlspecialchars($meta['ID_bill']); ?></div>
                                            <div><strong>Số thẻ:</strong> <?php echo htmlspecialchars($meta['Userid']); ?></div>
                                            <div><strong>Tên:</strong> <?php echo htmlspecialchars($borrowerName); ?></div>
                                            <div><strong>Đơn vị:</strong> <?php echo htmlspecialchars($depName); ?></div>
                                            <div><strong>Số thẻ cán bộ:</strong> <?php echo htmlspecialchars($officerId); ?></div>
                                            <div><strong>Tên cán bộ:</strong> <?php echo htmlspecialchars($officerName); ?></div>
                                            <div><strong>Ngày mượn:</strong> <?php echo date('d/m/Y', strtotime($meta['DateBorrow'])); ?></div>
                                            <div><strong>Ngày nhận:</strong> <?php echo date('d/m/Y', strtotime($meta['DateReceive'])); ?></div>
                                        </div>
                                        <div class="info-grid" style="grid-template-columns: repeat(4, 1fr);">
                                            <div><strong>Tổng SL đăng ký:</strong> <?php $totalSum = 0;
                                                                                    foreach ($details as $detail) {
                                                                                        $totalSum += intval($detail['LastSum']);
                                                                                    }
                                                                                    echo $totalSum; ?></div>
                                            <?php $totalScanned = 0;
                                            foreach ($details as $detail) {
                                                $totalScanned += (float)$detail['TotalPairsScanned'];
                                            }
                                            $totalBorrowed = (float)$meta['ToTalPhomNotBinding'] + $totalScanned; ?>
                                            <div><strong>Tổng SL cho mượn:</strong> <?php echo formatQuantity($totalBorrowed); ?></div>
                                            <div><strong>SL ghi chú:</strong> <?php echo formatQuantity($meta['ToTalPhomNotBinding']); ?></div>
                                            <?php $isScanned = $meta['StateLastBill'];
                                            $statusScanText = $isScanned ? 'Đã scan' : 'Chưa scan';
                                            $statusScanColor = $isScanned ? '#28a745' : '#dc3545'; ?>
                                            <div><strong>Trạng thái:</strong> <span style="color: <?php echo $statusScanColor; ?>; font-weight: bold;"><?php echo $statusScanText; ?></span></div>
                                        </div>
                                        <table class="lend-table">
                                            <thead style="background-color: #9CCDDB;">
                                                <tr>
                                                    <th>Mã dạng phom</th>
                                                    <th>Tên Phom</th>
                                                    <th>Size</th>
                                                    <th>Số lượng đăng ký</th>
                                                    <th>Đã quét</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($details as $detail): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($detail['LastMatNo']); ?></td>
                                                        <td><?php echo htmlspecialchars($detail['LastName']); ?></td>
                                                        <td><?php echo trim($detail['LastSize']); ?></td>
                                                        <td><?php echo formatQuantity($detail['LastSum']); ?></td>
                                                        <td><?php echo formatQuantity($detail['TotalPairsScanned']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="no-result-message" style="display: none; text-align: center; color: red; margin-top: 20px;">Không tìm thấy đơn mượn phù hợp.</div>
                        </div>

                        <!-- RETURN TAB PANE (GIAO DIỆN MỚI) -->
                        <div class="tab-pane fade" id="return-content" role="tabpanel">
                            <?php if (empty($returnBillsGrouped)): ?>
                                <div class="alert alert-info text-center mt-4"><i class="fas fa-box-open"></i> Chưa có đơn trả nào.</div>
                            <?php else: ?>
                                <?php foreach ($returnBillsGrouped as $group): ?>
                                    <?php
                                    $meta = $group['meta'];
                                    $details = $group['details'];
                                    $totalBorrowQty = 0;
                                    $totalReturnQty = 0;
                                    $search_details_content = '';
                                    foreach ($details as $detail) {
                                        $totalBorrowQty += $detail['QuantityBorrow'];
                                        $totalReturnQty += $detail['QuantityReturn'];
                                        $maDangPhom = trim(explode('(', $detail['LastName'])[0]);
                                        $search_details_content .= ' ' . strtolower($maDangPhom . ' ' . $detail['LastName']);
                                    }

                                    if (round($totalReturnQty, 2) >= round($totalBorrowQty, 2)) {
                                        $statusText = 'Đã trả';
                                        $borderColor = '#0d6efd';
                                        $backgroundColor = '#E0ECFF';
                                        $statusBadgeBg = '#0d6efd';
                                    } else {
                                        $statusText = 'Chưa trả đủ';
                                        $borderColor = '#D2691E';
                                        $backgroundColor = '#FFEBD6';
                                        $statusBadgeBg = '#D2691E';
                                    }
                                    $cardStyle = "background-color: $backgroundColor; border: 1px solid $borderColor;";

                                    $borrowerName = isset($meta['BorrowerName']) ? $meta['BorrowerName'] : '---';
                                    $depName = isset($meta['DepName']) ? $meta['DepName'] : '---';
                                    $officerName = isset($meta['OfficerName']) ? $meta['OfficerName'] : '---';
                                    $dateBorrow = isset($meta['DateBorrow']) ? date("d/m/Y", strtotime($meta['DateBorrow'])) : '---';
                                    $searchContent = strtolower($meta['ID_Return'] . ' ' . $meta['ID_BILL'] . ' ' . $meta['Userid'] . ' ' . $borrowerName . ' ' . $depName . ' ' . $officerName) . $search_details_content;
                                    ?>
                                    <div class="lend-card return-card" style="<?php echo $cardStyle; ?>" data-content="<?php echo htmlspecialchars($searchContent); ?>">
                                        <div class="card-status">
                                            <span class="status-badge" style="background-color: <?php echo $statusBadgeBg; ?>;">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </div>
                                        <div class="info-grid" style="grid-template-columns: repeat(4, 1fr);">
                                            <div><strong>Mã trả:</strong> <?php echo htmlspecialchars($meta['ID_Return']); ?></div>
                                            <div><strong>Số thẻ:</strong> <?php echo htmlspecialchars($meta['Userid']); ?></div>
                                            <div><strong>Tên người trả:</strong> <?php echo htmlspecialchars($borrowerName); ?></div>
                                            <div><strong>Đơn vị:</strong> <?php echo htmlspecialchars($depName); ?></div>
                                            <div><strong>Số thẻ cán bộ:</strong> <?php echo htmlspecialchars($meta['OfficerId']); ?></div>
                                            <div><strong>Tên cán bộ:</strong> <?php echo htmlspecialchars($officerName); ?></div>
                                            <div><strong>Ngày mượn:</strong> <?php echo htmlspecialchars($dateBorrow); ?></div>
                                            <div><strong>Ngày trả:</strong> <?php echo htmlspecialchars(date("d/m/Y", strtotime($meta['ReturnRequestDate']))); ?></div>
                                            <div><strong>Tổng SL mượn:</strong> <?php echo format_quantity_detail($totalBorrowQty); ?> đôi</div>
                                            <div><strong>Tổng SL đã trả:</strong> <span style="font-weight:bold; color: #0d6efd;"><?php echo format_quantity_detail($totalReturnQty); ?></span> đôi</div>
                                        </div>
                                        <table class="lend-table">
                                            <thead style="background-color: #9CCDDB;">
                                                <tr>
                                                    <th style="width: 18%;">Mã dạng phom</th>
                                                    <th style="width: 18%;">Tên phom</th>
                                                    <th style="width: 16%;">Size</th>
                                                    <th style="width: 16%;">SL mượn</th>
                                                    <th style="width: 16%;">SL trả</th>
                                                    <th style="width: 16%;">Mã RFID chưa trả</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($details as $detail): ?>
                                                    <?php
                                                    $maDangPhom = trim(explode('(', $detail['LastName'])[0]);
                                                    $row_style = (round($detail['QuantityReturn'], 2) < round($detail['QuantityBorrow'], 2)) ? 'background-color: #fce8e8;' : 'background-color: #ffffff;';
                                                    ?>
                                                    <tr style="<?php echo $row_style; ?>">
                                                        <td><?php echo htmlspecialchars($maDangPhom); ?></td>
                                                        <td><?php echo htmlspecialchars($detail['LastName']); ?></td>
                                                        <td><?php echo htmlspecialchars(trim($detail['LastSize'])); ?></td>
                                                        <td><?php echo format_quantity_detail($detail['QuantityBorrow']); ?></td>
                                                        <td style="font-weight: bold;"><?php echo format_quantity_detail($detail['QuantityReturn']); ?></td>
                                                        <td style="text-align: center; vertical-align: top; color: #dc3545; font-weight: 500;">
                                                            <?php if (!empty(trim($detail['RFID_Shortcut']))) {
                                                                echo implode('<br>', array_filter(array_map('trim', explode(',', $detail['RFID_Shortcut']))));
                                                            } ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="no-result-message" style="display: none; text-align: center; color: red; margin-top: 20px;">Không tìm thấy đơn trả phù hợp.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        }
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.getElementById('searchInput');
            var dateInput = document.getElementById('dateInput');
            flatpickr(dateInput, {
                dateFormat: "d/m/Y",
                locale: "vn",
                allowInput: true
            });
            window.filterCards = function() {
                const keyword = searchInput.value.trim().toLowerCase();
                const dateValue = dateInput.value.trim();
                const activeTabPane = document.querySelector('.tab-pane.active');
                if (!activeTabPane) return;

                const isReturnTab = activeTabPane.id === 'return-tab-pane';
                const cards = activeTabPane.querySelectorAll('.lend-card');
                const noResultMessage = activeTabPane.querySelector('.no-result-message');
                let visibleCount = 0;

                cards.forEach(function(card) {
                    let match = true;

                    const content = card.getAttribute('data-content');
                    if (keyword && !content.includes(keyword)) {
                        match = false;
                    }

                    if (dateValue) {
                        const targetLabel = isReturnTab ? 'Ngày trả:' : 'Ngày mượn:';
                        let dateMatched = false;

                        const infoDivs = card.querySelectorAll('.info-grid > div');
                        infoDivs.forEach(function(div) {
                            const strong = div.querySelector('strong');
                            if (strong && strong.innerText.trim() === targetLabel) {
                                const fullText = div.innerText.trim();
                                const dateText = fullText.replace(strong.innerText.trim(), '').trim();
                                if (dateText === dateValue) {
                                    dateMatched = true;
                                }
                            }
                        });

                        if (!dateMatched) {
                            match = false;
                        }
                    }

                    card.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                if (noResultMessage) {
                    noResultMessage.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            };

            searchInput.addEventListener('input', filterCards);
            dateInput.addEventListener('change', filterCards);
            var tabs = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabs.forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function() {
                    searchInput.value = '';
                    flatpickr(dateInput).clear();
                    filterCards();
                });
            });
        });
    </script>
</body>

</html>