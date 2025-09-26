<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';
function formatQuantity($quantity)
{
    // Chuyển đổi sang float để kiểm tra chính xác
    $quantity = (float) $quantity;
    if (fmod($quantity, 1) !== 0.0) {
        return number_format($quantity, 1);
    } else {
        return number_format($quantity, 0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn mượn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <style>
    /* CSS của bạn giữ nguyên */
    .item-box {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #f1f9ff;
    }

    .checkbox-col {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .scan-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .text-end {
        text-align: end;
    }

    .modern-pagination .page-link {
        border: none;
        background-color: transparent;
        color: #0093D3;
        font-weight: 300;
        padding: 4px 5px;
    }

    .modern-pagination .page-item.active .page-link {
        font-weight: bold;
        color: #1e40af;
        background-color: transparent;
        border: none;
    }

    .modern-pagination .page-item.disabled .page-link {
        color: #B9B9B9;
    }

    .lend-card {
        position: relative;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        font-size: 10px;
    }

    .lend-table {
        background-color: #ffffff;
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        table-layout: fixed;
    }

    .lend-table th {
        background-color: #9CCDDB;
        font-weight: 500;
    }

    .lend-table th,
    .lend-table td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .lend-table th:nth-child(1),
    .lend-table td:nth-child(1),
    .lend-table th:nth-child(2),
    .lend-table td:nth-child(2) {
        width: 25%;
    }

    .lend-table th:nth-child(3),
    .lend-table td:nth-child(3),
    .lend-table th:nth-child(4),
    .lend-table td:nth-child(4),
    .lend-table th:nth-child(5),
    .lend-table td:nth-child(5) {
        width: 20%;
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

    .info-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 8px 10px;
        margin-bottom: 10px;
        font-size: 15px;
        align-items: center;
    }

    .info-grid:nth-child(2) {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .info-grid:nth-child(3) {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    .confirm-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        border: none;
        background: transparent;
        color: green;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        font-weight: 500;
        background-color: #e9ffe9;
        border-radius: 5px;
    }

    .confirm-btn[disabled] {
        background-color: transparent;
        color: gray;
        cursor: default;
        font-weight: normal;
    }

    .confirm-btn.completed {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .lend-table tbody tr:hover {
        background-color: #fff8dc;
        cursor: pointer;
    }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php'; ?>
        <div class="content">
            <div class="main-header">
                <?php include_once '../public/partials/header.php'; ?>
            </div>
            <div class="main-content" style="padding-top: 60px;">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Nhập để tìm kiếm...">
                    <input type="date" id="dateInput">
                    <button class="btn btn-sm btn-outline-secondary" onclick="filterCards()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div id="no-result-message" style="display: none; text-align: center; color: red; margin-top: 10px;">
                    Không tìm thấy đơn mượn phù hợp.
                </div>

                <?php
                require_once "../configs/api.php";

                $companyName = isset($_SESSION['user']['companyName']) ? $_SESSION['user']['companyName'] : null;
                if (!$companyName) {
                    echo "<div class='alert alert-danger'>Bạn cần đăng nhập để xem thông tin.</div>";
                    exit;
                }

                $responseJson = callAPI('layTatCaDonMuon', ['companyName' => $companyName]);
                $response = json_decode($responseJson, true);

                // // $returnResponseJson = callAPI('layTatCaDonTra', ['companyName' => $companyName]);
                // // $returnResponse = json_decode($returnResponseJson, true);

                // // // --- BƯỚC 1: Xử lý dữ liệu trả hàng chi tiết ---
                // // $returnLookup = [];
                // // if ($returnResponse['status'] === 'Success' && !empty($returnResponse['data'])) {
                // //     foreach ($returnResponse['data'] as $returnItem) {
                // //         $billId = $returnItem['ID_BILL'];
                // //         // Tạo key duy nhất cho mỗi dòng sản phẩm (phom + size)
                // //         $itemKey = $returnItem['LastName'] . '___' . trim($returnItem['LastSize']);

                // //         if (!isset($returnLookup[$billId])) {
                // //             $returnLookup[$billId] = [
                // //                 'total_borrowed' => 0,
                // //                 'total_returned' => 0,
                // //                 'items' => []
                // //             ];
                // //         }

                // //         // Cộng dồn tổng số lượng
                // //         $returnLookup[$billId]['total_borrowed'] += (float) $returnItem['QuantityBorrow'];
                // //         $returnLookup[$billId]['total_returned'] += (float) $returnItem['QuantityReturn'];

                // //         // Lưu chi tiết từng dòng
                // //         $returnLookup[$billId]['items'][$itemKey] = [
                // //             'borrowed' => (float) $returnItem['QuantityBorrow'],
                // //             'returned' => (float) $returnItem['QuantityReturn']
                // //         ];
                //     }
                // }

                $bills = [];
                if ($response['status'] === 'Success' && !empty($response['data'])) {
                    foreach ($response['data'] as $item) {
                        $billId = $item['ID_bill'];

                        if (!isset($bills[$billId])) {
                            // --- BƯỚC 2: Gán thông tin trả hàng vào từng đơn mượn ---
                            $returnInfo = isset($returnLookup[$billId]) ? $returnLookup[$billId] : null;
                            $status = 'not_returned'; // Trạng thái mặc định
                
                            if ($returnInfo) {
                                // Sử dụng sai số để so sánh số thực
                                if (abs($returnInfo['total_borrowed'] - $returnInfo['total_returned']) < 0.001) {
                                    $status = 'completed'; // Đã hoàn thành
                                } else {
                                    $status = 'partial'; // Chưa trả đủ
                                }
                            }

                            $bills[$billId] = [
                                'meta' => [
                                    'ID_bill' => $billId,
                                    'Userid' => $item['Userid'],
                                    'BorrowerName' => $item['BorrowerName'],
                                    'DepName' => $item['DepName'],
                                    'OfficerId' => $item['OfficerId'],
                                    'OfficerName' => $item['OfficerName'],
                                    'isConfirm' => $item['isConfirm'],
                                    'StateLastBill' => $item['StateLastBill'],
                                    'ToTalPhomNotBinding' => (float) $item['ToTalPhomNotBinding'],
                                    'DateBorrow' => $item['DateBorrow'],
                                    'DateReceive' => $item['DateReceive'],
                                    'status' => $status,
                                    'returnInfo' => $returnInfo
                                ],
                                'details' => []
                            ];
                        }

                        if (strtotime($item['DateBorrow']) < strtotime($bills[$billId]['meta']['DateBorrow'])) {
                            $bills[$billId]['meta']['DateBorrow'] = $item['DateBorrow'];
                        }
                        if (strtotime($item['DateReceive']) > strtotime($bills[$billId]['meta']['DateReceive'])) {
                            $bills[$billId]['meta']['DateReceive'] = $item['DateReceive'];
                        }
                        $bills[$billId]['details'][] = $item;
                    }

                    // --- BƯỚC 3: Sắp xếp đơn hàng theo logic mới ---
                    usort($bills, function ($a, $b) {
                        $statusOrder = [
                            'not_returned_unconfirmed' => 1, // Chưa duyệt
                            'not_returned_confirmed' => 2, // Đã duyệt
                            'partial' => 3, // Chưa trả đủ
                            'completed' => 4, // Đã hoàn thành
                        ];

                        // Xác định key sắp xếp cho A
                        $a_key = $a['meta']['status'];
                        if ($a_key === 'not_returned') {
                            $a_key = !empty($a['meta']['isConfirm']) ? 'not_returned_confirmed' : 'not_returned_unconfirmed';
                        }
                        // Xác định key sắp xếp cho B
                        $b_key = $b['meta']['status'];
                        if ($b_key === 'not_returned') {
                            $b_key = !empty($b['meta']['isConfirm']) ? 'not_returned_confirmed' : 'not_returned_unconfirmed';
                        }

                        $a_prio = $statusOrder[$a_key];
                        $b_prio = $statusOrder[$b_key];

                        if ($a_prio !== $b_prio) {
                            return $a_prio - $b_prio;
                        }

                        // Nếu cùng trạng thái, sắp xếp theo ngày mượn mới nhất
                        $a_date = strtotime($a['meta']['DateBorrow']);
                        $b_date = strtotime($b['meta']['DateBorrow']);
                        return $b_date - $a_date;
                    });
                }
                ?>

                <?php if (empty($bills)): ?>
                <div class="alert alert-info text-center mt-4">
                    <i class="fas fa-box-open"></i> Chưa có đơn mượn.
                </div>
                <?php endif; ?>

                <?php foreach ($bills as $group): ?>
                <?php
                    $meta = $group['meta'];
                    $details = $group['details'];
                    $status = $meta['status'];

                    $buttonText = '';
                    $buttonAttributes = 'disabled';
                    $buttonClass = '';
                    $borderColor = '';
                    $backgroundColor = '';

                    if ($status === 'completed') {
                        $buttonText = 'Đã hoàn thành';
                        $buttonClass = 'completed';
                        $borderColor = '#17a2b8';
                        $backgroundColor = '#d1ecf1';
                    } elseif ($status === 'partial') {
                        $buttonText = 'Chưa trả đủ';
                        $buttonClass = 'partial';
                        $borderColor = '#D2691E';
                        $backgroundColor = '#FFEBD6';
                    } elseif (!empty($meta['isConfirm'])) {
                        $buttonText = 'Đã duyệt';
                        $buttonClass = 'approved';
                        $borderColor = '#28a745';
                        $backgroundColor = '#d4edda';
                    } else {
                        $buttonText = 'Duyệt đơn';
                        $buttonAttributes = 'title="Bấm duyệt đơn"';
                        $buttonClass = 'to-approve';
                        $borderColor = '#dc3545';
                        $backgroundColor = '#f8d7da';
                    }

                    $cardStyle = "background-color: $backgroundColor; border: 1px solid $borderColor;";
                    ?>
                <div class="lend-card borrow-card" style="<?= $cardStyle ?>"
                    data-content="<?= htmlspecialchars(strtolower($meta['ID_bill'] . ' ' . $meta['Userid'] . ' ' . $meta['BorrowerName'] . ' ' . $meta['DepName'] . ' ' . $meta['OfficerId'] . ' ' . $meta['OfficerName'] . ' ' . date('d/m/Y', strtotime($meta['DateBorrow'])) . ' ' . date('d/m/Y', strtotime($meta['DateReceive'])))) ?>">

                    <button class="confirm-btn <?= $buttonClass ?>" data-bill-id="<?= $meta['ID_bill'] ?>"
                        <?= $buttonAttributes ?>>
                        <i class="fas fa-check-circle"></i>
                        <?= $buttonText ?>
                    </button>

                    <div class="info-grid">
                        <div><strong>Mã đơn:</strong> <?= htmlspecialchars($meta['ID_bill']) ?></div>
                        <div><strong>Số thẻ:</strong> <?= htmlspecialchars($meta['Userid']) ?></div>
                        <div><strong>Tên:</strong>
                            <?= htmlspecialchars(isset($meta['BorrowerName']) ? $meta['BorrowerName'] : '---') ?></div>
                        <div><strong>Đơn vị:</strong> <?= htmlspecialchars($meta['DepName']) ?></div>
                    </div>

                    <div class="info-grid">
                        <div><strong>Số thẻ cán bộ:</strong>
                            <?= htmlspecialchars(isset($meta['OfficerId']) ? $meta['OfficerId'] : '---') ?></div>
                        <div><strong>Tên cán bộ:</strong>
                            <?= htmlspecialchars(isset($meta['OfficerName']) ? $meta['OfficerName'] : '---') ?></div>
                    </div>

                    <div class="info-grid">
                        <div><strong>Ngày mượn:</strong> <?= date('d/m/Y', strtotime($meta['DateBorrow'])) ?></div>
                        <div><strong>Ngày nhận:</strong> <?= date('d/m/Y', strtotime($meta['DateReceive'])) ?></div>
                    </div>

                    <div class="info-grid">
                        <div><strong>Tổng SL đăng ký:</strong>
                            <?= array_sum(array_column($details, 'LastSum')) ?>
                        </div>
                        <?php
                            $totalScanned = array_sum(array_column($details, 'TotalPairsScanned'));
                            $totalBorrowed = $meta['ToTalPhomNotBinding'] + $totalScanned;
                            ?>
                        <div><strong>Tổng SL cho mượn:</strong>
                            <?= formatQuantity($totalBorrowed) ?>
                        </div>
                        <div><strong>SL ghi chú:</strong>
                            <?= formatQuantity($meta['ToTalPhomNotBinding']) ?>
                        </div>
                        <div>
                            <?php
                                $scanStatusText = $meta['StateLastBill'] ? 'Đã scan' : 'Chưa scan';
                                $scanStatusColor = $meta['StateLastBill'] ? '#28a745' : '#dc3545';
                                ?>
                            <strong>Scan:</strong>
                            <span style="color: <?= $scanStatusColor ?>; font-weight: bold;">
                                <?= $scanStatusText ?>
                            </span>
                        </div>
                    </div>

                    <table class="lend-table">
                        <thead>
                            <tr>
                                <th>Mã dạng phom</th>
                                <th>Tên Phom</th>
                                <th>Size</th>
                                <?php if ($status === 'partial' || $status === 'completed'): ?>
                                <th>Đã mượn</th>
                                <th>Đã trả</th>
                                <?php else: ?>
                                <th>Đã đăng ký</th>
                                <th>Đã quét</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($status === 'partial' || $status === 'completed') {
                                    $returnItems = $meta['returnInfo']['items'];
                                    foreach ($returnItems as $itemKey => $itemData) {

                                        list($lastName, $lastSize) = explode('___', $itemKey);
                                        $maDangPhom = explode('(', $lastName)[0];
                                        ?>
                            <tr>
                                <td><?= htmlspecialchars(trim($maDangPhom)) ?></td>
                                <td><?= htmlspecialchars($lastName) ?></td>
                                <td><?= htmlspecialchars($lastSize) ?></td>
                                <td><?= formatQuantity($itemData['borrowed']) ?></td>
                                <td><?= formatQuantity($itemData['returned']) ?></td>
                            </tr>
                            <?php
                                    }
                                } else {
                                    foreach ($details as $detail) {
                                        $maDangPhom = explode('(', $detail['LastName'])[0];
                                        ?>
                            <tr>
                                <td><?= htmlspecialchars(trim($maDangPhom)) ?></td>
                                <td><?= htmlspecialchars($detail['LastName']) ?></td>
                                <td><?= trim($detail['LastSize']) ?></td>
                                <td><?= intval($detail['LastSum']) ?></td>
                                <td><?= floatval($detail['TotalPairsScanned']) ?></td>
                            </tr>
                            <?php
                                    }
                                }
                                ?>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>
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
        const searchInput = document.getElementById('searchInput');
        const searchButton = document.querySelector('.search-box button');
        const cards = document.querySelectorAll('.borrow-card');
        const noResultMessage = document.getElementById('no-result-message');

        function filterCards() {
            const keyword = document.getElementById('searchInput').value.trim().toLowerCase();
            const dateValue = document.getElementById('dateInput').value;
            let visibleCount = 0;

            const cards = document.querySelectorAll('.borrow-card');
            cards.forEach(card => {
                const content = card.getAttribute('data-content');
                let match = true;

                if (keyword && !content.includes(keyword)) {
                    match = false;
                }

                if (dateValue) {
                    const contentDateMatch = content.match(/(\d{2})\/(\d{2})\/(\d{4})/);
                    if (contentDateMatch) {
                        const borrowDateStr =
                            `${contentDateMatch[3]}-${contentDateMatch[2]}-${contentDateMatch[1]}`;
                        if (borrowDateStr !== dateValue) {
                            match = false;
                        }
                    } else {
                        match = false;
                    }
                }

                card.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            document.getElementById('no-result-message').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        document.getElementById('searchInput').addEventListener('input', filterCards);
        document.getElementById('dateInput').addEventListener('change', filterCards);

        document.querySelectorAll('.confirm-btn').forEach(button => {
            button.addEventListener('click', async function() {
                if (this.disabled) return;

                const billId = this.dataset.billId;
                const companyName = <?= json_encode($_SESSION['user']['companyName']) ?>;

                if (!billId || !companyName) return alert("Thiếu thông tin xác nhận.");
                if (!confirm("Bạn có chắc chắn muốn duyệt đơn này?")) return;

                try {
                    const res = await fetch('confirm_borrow.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ID_bill: billId,
                            companyName
                        })
                    });

                    const result = await res.json();
                    alert(result.status === 'Success' ? 'Duyệt đơn thành công!' :
                        'Duyệt đơn thất bại: ' + result.message);
                    if (result.status === 'Success') location.reload();
                } catch (error) {
                    console.error(error);
                    alert('Có lỗi xảy ra khi gửi yêu cầu.');
                }
            });
        });
    });
    </script>
</body>

</html>