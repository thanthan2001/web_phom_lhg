<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';
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
                    <button class="btn btn-sm btn-outline-secondary" onclick="filterCards()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div id="no-result-message" style="display: none; text-align: center; color: red; margin-top: 10px;">
                    Không tìm thấy đơn trả phù hợp.
                </div>

                <?php
                require_once "../configs/api.php";
                if (!$companyName) {
                    echo "<div class='alert alert-danger'>Bạn cần đăng nhập để xem thông tin.</div>";
                    exit;
                }

                $responseJson = callAPI('layTatCaDonTra', ['companyName' => $companyName]);
                $response = json_decode($responseJson, true);

                if ($response['status'] === 'Success') {
                    $bills = $response['data'];
                    usort($bills, function ($a, $b) {
                        return strtotime($b['ReturnRequestDate']) - strtotime($a['ReturnRequestDate']);
                    });
                } else {
                    $bills = [];
                }
                ?>

                <?php if (empty($bills)): ?>
                    <div class="alert alert-info text-center mt-4">
                        <i class="fas fa-box-open"></i> Chưa có đơn trả.
                    </div>
                <?php endif; ?>

                <?php foreach ($bills as $bill): ?>
                    <?php
                    $isConfirmed = $bill['isConfirm'];
                    $isScanned = $bill['TotalRFIDScanned'] >= $bill['totalQuantityReturn'];

                    if (!$isConfirmed) {
                        $statusText = 'Chưa trả';
                        $borderColor = '#d9534f';
                        $backgroundColor = '#F5CFCF';
                    } else {
                        $statusText = 'Đã trả';
                        $borderColor = '#0093D3';
                        $backgroundColor = '#d0f0fd';
                    }
                    $cardStyle = "background-color: $backgroundColor; border: 1px solid $borderColor;";
                    ?>

                    <div class="lend-card return-card" style="<?= $cardStyle ?>"
                        data-content="<?= htmlspecialchars(strtolower($bill['ID_Return'] . ' ' . $bill['ID_BILL'] . ' ' . $bill['Userid'] . ' ' . $bill['BorrowerName'] . ' ' . $bill['DepName'] . ' ' . $bill['OfficerId'] . ' ' . $bill['OfficerName'])) ?>">

                        <div class="info-grid">
                            <div><strong>Mã trả:</strong> <?= htmlspecialchars($bill['ID_Return']) ?></div>
                            <div><strong>Mã mượn:</strong> <?= htmlspecialchars($bill['ID_BILL']) ?></div>
                            <div><strong>Số thẻ:</strong> <?= htmlspecialchars($bill['Userid']) ?></div>
                            <div><strong>Tên:</strong> <?= htmlspecialchars($bill['BorrowerName']) ?></div>
                            <div><strong>Đơn vị:</strong> <?= htmlspecialchars($bill['DepName']) ?></div>
                        </div>

                        <div class="info-grid">
                            <div><strong>Số thẻ cán bộ:</strong> <?= htmlspecialchars($bill['OfficerId']) ?></div>
                            <div><strong>Tên cán bộ:</strong> <?= htmlspecialchars($bill['OfficerName']) ?></div>
                        </div>

                        <div class="info-grid">
                            <div><strong>Ngày mượn:</strong> <?= date('d/m/Y', strtotime($bill['DateBorrow'])) ?></div>
                            <div><strong>Ngày nhận:</strong> <?= date('d/m/Y', strtotime($bill['DateReceive'])) ?></div>
                            <div><strong>Ngày yêu cầu trả:</strong> <?= date('d/m/Y', strtotime($bill['ReturnRequestDate'])) ?></div>
                        </div>

                        <div class="info-grid">
                            <div><strong>SL mượn:</strong> <?= intval($bill['totalQuantityBorrow']) ?></div>
                            <div><strong>SL trả:</strong> <?= intval($bill['totalQuantityReturn']) ?></div>
                            <div><strong>Đã quét RFID:</strong> <?= intval($bill['TotalRFIDScanned']) ?></div>
                            <div><strong>Trạng thái:</strong>
                                <span style="color: <?= $isScanned ? '#28a745' : '#dc3545' ?>; font-weight: bold;">
                                    <?= $statusText ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function filterCards() {
            const keyword = document.getElementById('searchInput').value.trim().toLowerCase();
            const cards = document.querySelectorAll('.return-card');
            let visible = 0;

            cards.forEach(card => {
                const content = card.getAttribute('data-content');
                const match = !keyword || content.includes(keyword);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            document.getElementById('no-result-message').style.display = visible === 0 ? 'block' : 'none';
        }
    </script>
</body>

</html>