<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';
function formatQuantity($quantity) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>
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
            /* z-index: 1000; */
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

                $bills = [];
                if ($response['status'] === 'Success' && !empty($response['data'])) {
                    foreach ($response['data'] as $item) {
                        $billId = $item['ID_bill'];

                        // Initialize bill data if not exists
                        if (!isset($bills[$billId])) {
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
                                    'ToTalPhomNotBinding' => (float)$item['ToTalPhomNotBinding'], // Take first value
                                    'DateBorrow' => $item['DateBorrow'], // Initialize with current date
                                    'DateReceive' => $item['DateReceive'], // Initialize with current date
                                ],
                                'details' => []
                            ];
                        }

                        // Update DateBorrow (earliest date)
                        if (strtotime($item['DateBorrow']) < strtotime($bills[$billId]['meta']['DateBorrow'])) {
                            $bills[$billId]['meta']['DateBorrow'] = $item['DateBorrow'];
                        }

                        // Update DateReceive (latest date)
                        if (strtotime($item['DateReceive']) > strtotime($bills[$billId]['meta']['DateReceive'])) {
                            $bills[$billId]['meta']['DateReceive'] = $item['DateReceive'];
                        }

                        $bills[$billId]['details'][] = $item;
                    }

                    // Sort bills based on isConfirm and DateBorrow
                    usort($bills, function ($a, $b) {
                        $a_confirm = !empty($a['meta']['isConfirm']);
                        $b_confirm = !empty($b['meta']['isConfirm']);

                        // Prioritize unconfirmed bills
                        if ($a_confirm !== $b_confirm) {
                            return $a_confirm - $b_confirm;
                        }

                        // Sort by DateBorrow in descending order
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
                    if (!empty($meta['isConfirm']) && !empty($meta['StateLastBill'])) {
                        // Đơn đã cho mượn
                        $borderColor = '#0093D3';
                        $backgroundColor = '#d0f0fd';
                        $statusText = 'Đã cho mượn';
                    } elseif (!empty($meta['isConfirm'])) {
                        // Đơn đã xác nhận nhưng chưa scan
                        $borderColor = '#14AE5C';
                        $backgroundColor = '#d4edda';
                        $statusText = 'Đã xác nhận';
                    } else {
                        // Đơn chưa xác nhận
                        $borderColor = '#d9534f';
                        $backgroundColor = '#F5CFCF';
                        $statusText = 'Chưa xác nhận';
                    }
                    $cardStyle = "background-color: $backgroundColor; border: 1px solid $borderColor;";

                    ?>
                    <div class="lend-card borrow-card" style="<?= $cardStyle ?>" data-content="<?= htmlspecialchars(strtolower($meta['ID_bill'] . ' ' . $meta['Userid'] . ' ' . $meta['BorrowerName'] . ' ' . $meta['DepName'] . ' ' . $meta['OfficerId'] . ' ' . $meta['OfficerName'] . ' ' . date('d/m/Y', strtotime($meta['DateBorrow'])) . ' ' . date('d/m/Y', strtotime($meta['DateReceive']))) ) ?>">
                        <button
                            class="confirm-btn"
                            data-bill-id="<?= $meta['ID_bill'] ?>"
                            <?= $meta['isConfirm'] ? 'disabled title="Đã duyệt"' : 'title="Bấm duyệt đơn"' ?>>
                            <i class="fas fa-check-circle"></i>
                            <?= $meta['isConfirm'] ? 'Đã duyệt' : 'Duyệt đơn' ?>
                        </button>

                        <div class="info-grid">
                            <div><strong>Mã đơn:</strong> <?= htmlspecialchars($meta['ID_bill']) ?></div>
                            <div><strong>Số thẻ:</strong> <?= htmlspecialchars($meta['Userid']) ?></div>
                            <div><strong>Tên:</strong> <?= htmlspecialchars(isset($meta['BorrowerName']) ? $meta['BorrowerName'] : '---') ?></div>
                            <div><strong>Đơn vị:</strong> <?= htmlspecialchars($meta['DepName']) ?></div>
                        </div>

                        <div class="info-grid">
                            <div><strong>Số thẻ cán bộ:</strong> <?= htmlspecialchars(isset($meta['OfficerId']) ? $meta['OfficerId'] : '---') ?></div>
                            <div><strong>Tên cán bộ:</strong> <?= htmlspecialchars(isset($meta['OfficerName']) ? $meta['OfficerName'] : '---') ?></div>
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
                            <?php
                            $isScanned = $meta['StateLastBill'];
                            $statusText = $isScanned ? 'Đã scan' : 'Chưa scan';
                            $statusColor = $isScanned ? '#28a745' : '#dc3545';
                            ?>
                            <div>
                                <strong>Trạng thái:</strong>
                                <span style="color: <?= $statusColor ?>; font-weight: bold;">
                                    <?= $statusText ?>
                                </span>
                            </div>
                        </div>

                        <table class="lend-table">
                            <thead>
                                <tr>
                                    <th>Mã dạng phom</th>
                                    <th>Tên Phom</th>
                                    <th>Size</th>
                                    <th>Đã đăng ký</th>
                                    <th>Đã quét</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($details as $detail): ?>
                                    <?php $maDangPhom = explode('(', $detail['LastName'])[0]; ?>
                                    <tr>
                                        <td><?= htmlspecialchars(trim($maDangPhom)) ?></td>
                                        <td><?= htmlspecialchars($detail['LastName']) ?></td>
                                        <td><?= trim($detail['LastSize']) ?></td>
                                        <td><?= intval($detail['LastSum']) ?></td>
                                        <td><?= intval($detail['TotalPairsScanned']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
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
                const dateValue = document.getElementById('dateInput').value; // dạng YYYY-mm-dd
                let visibleCount = 0;

                const cards = document.querySelectorAll('.borrow-card');
                cards.forEach(card => {
                    const content = card.getAttribute('data-content');
                    let match = true;

                    if (keyword && !content.includes(keyword)) {
                        match = false;
                    }

                    if (dateValue) {
                        const borrowDate = new Date(content.match(/\d{2}\/\d{2}\/\d{4}/)[0].split('/').reverse().join('-'));
                        const selectedDate = new Date(dateValue);
                        
                        // Compare dates by converting them to YYYY-MM-DD strings to avoid time issues
                        const borrowDateFormatted = borrowDate.toISOString().slice(0,10);
                        const selectedDateFormatted = selectedDate.toISOString().slice(0,10);

                        if (borrowDateFormatted !== selectedDateFormatted) {
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
                        alert(result.status === 'Success' ? 'Duyệt đơn thành công!' : 'Duyệt đơn thất bại: ' + result.message);
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