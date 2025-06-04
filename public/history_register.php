<?php
session_start();
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
        $bills[$item['ID_bill']]['meta'] = $item;
        $bills[$item['ID_bill']]['details'][] = $item;
    }

    $bills = array_values($bills);

    usort($bills, function ($a, $b) {
        $a_confirm = !empty($a['meta']['isConfirm']);
        $b_confirm = !empty($b['meta']['isConfirm']);

        if ($a_confirm !== $b_confirm) {
            return $a_confirm - $b_confirm;
        }

        $a_date = strtotime($a['meta']['DateBorrow']);
        $b_date = strtotime($b['meta']['DateBorrow']);

        return $b_date - $a_date;
    });
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Return Register</title>
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
    .lend-table td:nth-child(1) {
        width: 25%;
    }

    .lend-table th:nth-child(2),
    .lend-table td:nth-child(2) {
        width: 25%;
    }

    .lend-table th:nth-child(3),
    .lend-table td:nth-child(3) {
        width: 25%;
    }

    .lend-table th:nth-child(4),
    .lend-table td:nth-child(4) {
        width: 25%;
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
        color: green;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        z-index: 1000;
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
                        <button class="btn btn-sm btn-outline-secondary" onclick="filterCards()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <?php foreach ($bills as $billId => $group): ?>
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
                        <div class="lend-card" style="<?= $cardStyle ?>">
                            <button
                                class="confirm-btn"
                                data-bill-id="<?= $meta['ID_bill'] ?>"
                                <?= $meta['isConfirm'] ? 'disabled title="Đã duyệt"' : 'title="Chưa duyệt"' ?>>
                                <i class="fas fa-check-circle"></i>
                                <?= $meta['isConfirm'] ? 'Đã duyệt' : 'Chưa duyệt' ?>
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
                                <div><strong>Tổng SL:</strong>
                                    <?= array_sum(array_map(function ($item) {
                                        return intval($item['LastSum']);
                                    }, $details)) ?>
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
                                        <th style="display: none;">Mã vật tư</th>
                                        <th>Mã dạng phom</th>
                                        <th>Tên Phom</th>
                                        <th>Size</th>
                                        <th>Số lượng đăng ký</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($details as $detail): ?>
                                        <?php $maDangPhom = explode('(', $detail['LastName']); ?>
                                        <tr>
                                            <td style="display: none;"><?= htmlspecialchars($detail['LastMatNo']) ?></td>
                                            <td><?= htmlspecialchars(trim($maDangPhom[0])) ?></td>
                                            <td><?= htmlspecialchars($detail['LastName']) ?></td>
                                            <td><?= trim($detail['LastSize']) ?></td>
                                            <td><?= intval($detail['LastSum']) ?></td>
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

        const searchInput = document.getElementById("searchInput");

    searchInput.addEventListener("input", filterCards);

    searchInput.addEventListener("keyup", function(e) {
        if (e.key === "Enter") {
            filterCards(); 
        }
    });

    function filterCards() {
        const keyword = searchInput.value.toLowerCase().trim();
        const cards = document.querySelectorAll(".lend-card");

        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            card.style.display = text.includes(keyword) ? "" : "none";
        });
    }
    </script>
</body>

</html>