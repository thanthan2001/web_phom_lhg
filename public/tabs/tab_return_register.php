<?php
session_start();
require_once "../../configs/api.php";

$companyName = $_SESSION['user']['companyName'] ?? null;

if (!$companyName) {
    echo "<div class='alert alert-danger'>Bạn cần đăng nhập để xem thông tin.</div>";
    exit;
}

$responseJson = callAPI('getAllReturnBill', ['companyName' => $companyName]);
$response = json_decode($responseJson, true);

if ($response['status'] !== 'Success') {
    echo "<div class='alert alert-warning'>Không có dữ liệu đơn trả.</div>";
    exit;
}

$data = $response['data'];

usort($data, function ($a, $b) {
    $a_confirm = $a['isConfirm'] ?? false;
    $b_confirm = $b['isConfirm'] ?? false;

    // Ưu tiên đơn chưa duyệt
    if ($a_confirm !== $b_confirm) {
        return $a_confirm - $b_confirm;
    }

    return strtotime($b['ReturnRequestDate']) - strtotime($a['ReturnRequestDate']);
});

foreach ($data as $item):
    $status = $item['isConfirm'] ? "Đã xác nhận" : "Chưa xác nhận";
    $statusColor = $item['isConfirm'] ? "#14AE5C" : "#d9534f";
    $backgroundColor = $item['isConfirm'] ? "#d4edda" : "#F5CFCF";
    ?>
    <div class="lend-card return-card" style="background-color: <?= $backgroundColor ?>; border: 1px solid <?= $statusColor ?>;"
        data-content="<?= strtolower($item['ID_Return'] . ' ' . $item['ID_BILL'] . ' ' . $item['BorrowerName'] . ' ' . $item['DepName']) ?>">

        <div class="info-grid">
            <div><strong>Mã trả:</strong> <?= htmlspecialchars($item['ID_Return']) ?></div>
            <div><strong>Mã mượn:</strong> <?= htmlspecialchars($item['ID_BILL']) ?></div>
            <div><strong>Người mượn:</strong> <?= htmlspecialchars($item['BorrowerName']) ?></div>
            <div><strong>Đơn vị:</strong> <?= htmlspecialchars($item['DepName']) ?></div>
            <div><strong>Số lượng trả:</strong> <?= htmlspecialchars($item['totalQuantityReturn']) ?></div>
        </div>

        <div class="info-grid">
            <div><strong>Ngày yêu cầu:</strong> <?= date('d/m/Y', strtotime($item['ReturnRequestDate'])) ?></div>
            <div><strong>Trạng thái:</strong> <?= $status ?></div>
            <div><strong>Số RFID đã quét:</strong> <?= $item['TotalRFIDScanned'] ?></div>
        </div>
    </div>
<?php endforeach; ?>
