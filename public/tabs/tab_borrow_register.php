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
</style>

<div class="search-box">
    <input type="text" id="searchInput" placeholder="Nhập để tìm kiếm...">
    <button class="btn btn-sm btn-outline-secondary" onclick="filterCards()">
        <i class="fas fa-search"></i>
    </button>
</div>

<div id="no-result-message" style="display: none; text-align: center; color: red; margin-top: 10px;">
    Không tìm thấy đơn mượn phù hợp.
</div>

<?php
session_start();
require_once "../../configs/api.php";

$companyName = isset($_SESSION['user']['companyName']) ? $_SESSION['user']['companyName'] : null;
if (!$companyName) {
    echo "<div class='alert alert-danger'>Bạn cần đăng nhập để xem thông tin.</div>";
    exit;
}
$responseJson = callAPI('layTatCaDonMuon', ['companyName' => $companyName]);
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

        // Ưu tiên đơn chưa duyệt
        if ($a_confirm !== $b_confirm) {
            return $a_confirm - $b_confirm;
        }

        //sắp theo ngày mượn giảm dần
        $a_date = strtotime($a['meta']['DateBorrow']);
        $b_date = strtotime($b['meta']['DateBorrow']);

        return $b_date - $a_date;
    });
}
?>

<?php foreach ($bills as $billId => $group): ?>
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
    <div class="lend-card borrow-card" style="<?= $cardStyle ?>" data-content="<?= htmlspecialchars(strtolower($meta['ID_bill'] . ' ' . $meta['Userid'] . ' ' . $meta['BorrowerName'] . ' ' . $meta['DepName'] . ' ' . $meta['OfficerId'] . ' ' . $meta['OfficerName'])) ?>">
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
            <div><strong>Tổng SL:</strong>
                <?= array_sum(array_column($details, 'LastSum')) ?>
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
                    <?php $maDangPhom = explode('(', $detail['LastName'])[0]; ?>
                    <tr>
                        <td style="display: none;"><?= htmlspecialchars($detail['LastMatNo']) ?></td>
                        <td><?= htmlspecialchars(trim($maDangPhom)) ?></td>
                        <td><?= htmlspecialchars($detail['LastName']) ?></td>
                        <td><?= trim($detail['LastSize']) ?></td>
                        <td><?= intval($detail['LastSum']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endforeach; ?>