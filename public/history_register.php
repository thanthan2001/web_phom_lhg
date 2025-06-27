<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
require_once "../configs/api.php";

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($_SESSION['user']['companyName']) ? $_SESSION['user']['companyName'] : null;
$Userid = isset($_SESSION['user']['userID']) ? $_SESSION['user']['userID'] : null;

$responseJson = callAPI('layDonMuonTheoUser', [
    'companyName' => $companyName,
    'UserID' => $Userid
]);
$response = json_decode($responseJson, true);

$bills = [];
if ($response['status'] === 'Success') {
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
                    'ToTalPhomNotBinding' => (float)$item['ToTalPhomNotBinding'],
                    'DateBorrow' => $item['DateBorrow'],
                    'DateReceive' => $item['DateReceive'],
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

        // Prioritize unconfirmed bills (if the confirm button was active, it would show unconfirmed first)
        if ($a_confirm !== $b_confirm) {
            return $a_confirm - $b_confirm;
        }

        // Sort by DateBorrow in descending order
        $a_date = strtotime($a['meta']['DateBorrow']);
        $b_date = strtotime($b['meta']['DateBorrow']);

        return $b_date - $a_date;
    });
}

function formatQuantity($quantity)
{
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
    <title>History Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<style>
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
        background-color: #ffedcc;
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

    .search-box input:focus {
        border-color: #064469;
        box-shadow: 0 0 3px #064469;
        outline: none;
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
        color: gray;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        z-index: 1000;
        gap: 5px;
        padding: 5px 10px;
        font-weight: 500;
        background-color: transparent;
        border-radius: 5px;
    }

    .confirm-btn[disabled] {
        background-color: transparent;
        color: blue;
        cursor: default;
        font-weight: normal;
    }
</style>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php' ?>
        <div class="content">
            <div class="main-header">
                <?php include_once '../public/partials/header.php' ?>
            </div>
            <div class="main-content" style="padding-top: 60px;">
                <div class="container-fluid">
                    <div class="search-box">
                        <input type="text" id="searchInput" placeholder="Nhập để tìm kiếm...">
                        <input type="date" id="dateInput">
                        <button class="btn btn-sm btn-outline-secondary" onclick="filterCards()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <div id="no-result-message" style="display: none; text-align: center; color: red; margin-top: 10px;">
                        Không tìm thấy đơn đăng ký phù hợp.
                    </div>

                    <?php if (empty($bills)): ?>
                        <div class="alert alert-info text-center mt-4">
                            <i class="fas fa-box-open"></i> Chưa có đơn đăng ký.
                        </div>
                    <?php endif; ?>

                    <?php foreach ($bills as $group): ?>
                        <?php
                        $meta = $group['meta'];
                        $details = $group['details'];
                        if (!empty($meta['isConfirm']) && !empty($meta['StateLastBill'])) {
                            // Đã mượn
                            $borderColor = '#8e44ad';
                            $backgroundColor = '#f3e5f5';
                            $statusText = 'Đã mượn';
                        } elseif (!empty($meta['isConfirm'])) {
                            // Đã duyệt
                            $borderColor = '#16a085';
                            $backgroundColor = '#d1f2eb';
                            $statusText = 'Đã duyệt';
                        } else {
                            // Chưa duyệt
                            $borderColor = '#e67e22';
                            $backgroundColor = '#fdebd0';
                            $statusText = 'Chưa duyệt';
                        }
                        $cardStyle = "background-color: $backgroundColor; border: 1px solid $borderColor;";
                        ?>
                        <div class="lend-card" style="<?= $cardStyle ?>" data-content="<?= htmlspecialchars(strtolower($meta['ID_bill'] . ' ' . $meta['Userid'] . ' ' . (isset($meta['BorrowerName']) ? $meta['BorrowerName'] : '---') . ' ' . (isset($meta['DepName']) ? $meta['DepName'] : '---') . ' ' . (isset($meta['OfficerId']) ? $meta['OfficerId'] : '---') . ' ' . (isset($meta['OfficerName']) ? $meta['OfficerName'] : '---') . ' ' . date('d/m/Y', strtotime($meta['DateBorrow'])) . ' ' . date('d/m/Y', strtotime($meta['DateReceive'])))) ?>">
                            <button
                                class="confirm-btn"
                                data-bill-id="<?= $meta['ID_bill'] ?>"
                                <?= $meta['isConfirm'] ? 'disabled title="Đã duyệt"' : 'title="Chờ duyệt"' ?>>
                                <i class="fas fa-check-circle"></i>
                                <?= $meta['isConfirm'] ? 'Đã duyệt' : 'Chờ duyệt' ?>
                            </button>
                            <div class="info-grid">
                                <div><strong>Mã đơn:</strong> <?= htmlspecialchars($meta['ID_bill']) ?></div>
                                <div><strong>Số thẻ:</strong> <?= htmlspecialchars($meta['Userid']) ?></div>
                                <div><strong>Tên:</strong> <?= htmlspecialchars(isset($meta['BorrowerName']) ? $meta['BorrowerName'] : '---') ?></div>
                                <div><strong>Đơn vị:</strong> <?= htmlspecialchars(isset($meta['DepName']) ? $meta['DepName'] : '---') ?></div>
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
                                    <?= array_sum(array_map(function ($item) {
                                        return intval($item['LastSum']);
                                    }, $details)) ?>
                                </div>
                                <?php
                                $totalScanned = array_sum(array_column($details, 'TotalPairsScanned'));
                                $totalBorrowed = (float)$meta['ToTalPhomNotBinding'] + $totalScanned;
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
                                        <th>Số lượng đăng ký</th>
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
            const dateInput = document.getElementById('dateInput');
            const noResultMessage = document.getElementById('no-result-message');

            function filterCards() {
                const keyword = searchInput.value.trim().toLowerCase();
                const dateValue = dateInput.value; // format YYYY-MM-DD
                let visibleCount = 0;

                const cards = document.querySelectorAll('.lend-card');
                cards.forEach(card => {
                    const content = card.getAttribute('data-content');
                    let match = true;

                    if (keyword && !content.includes(keyword)) {
                        match = false;
                    }

                    if (dateValue) {
                        // Extract the DateBorrow from the data-content attribute
                        const dateBorrowMatch = content.match(/(\d{2}\/\d{2}\/\d{4})/);
                        if (dateBorrowMatch) {
                            const borrowDateStr = dateBorrowMatch[1]; // "dd/mm/yyyy"
                            const [day, month, year] = borrowDateStr.split('/');
                            const borrowDate = new Date(`${year}-${month}-${day}`); // Convert to YYYY-MM-DD for comparison

                            const selectedDate = new Date(dateValue);

                            // Compare dates by converting them to YYYY-MM-DD strings to avoid time issues
                            const borrowDateFormatted = borrowDate.toISOString().slice(0, 10);
                            const selectedDateFormatted = selectedDate.toISOString().slice(0, 10);

                            if (borrowDateFormatted !== selectedDateFormatted) {
                                match = false;
                            }
                        } else {
                            // If no date is found in content, don't match if date filter is active
                            match = false;
                        }
                    }

                    card.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                noResultMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            searchInput.addEventListener('input', filterCards);
            dateInput.addEventListener('change', filterCards);
            dateInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    filterCards();
                }
            });
        });
    </script>
</body>

</html>