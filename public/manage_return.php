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
    <title>Quản lý đơn trả</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
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
            background-color: #9CCDDB;
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

        .lend-table tbody tr:hover {
            background-color: #fff8dc !important;
            cursor: pointer;
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
            width: 250px;
        }

        .search-box button {
            height: 38px;
            width: 40px;
            background-color: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .card-info-section {
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px 15px;
            align-items: center;
        }

        @media (max-width: 992px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
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
                    <input type="text" id="searchInput" placeholder="Nhập mã trả, tên, mã phom..." onkeyup="filterCards()">
                    <button class="btn btn-primary" onclick="filterCards()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div id="no-result-message" style="display: none; text-align: center; color: red; margin-top: 20px;">
                    Không tìm thấy đơn trả phù hợp.
                </div>

                <?php
                require_once "../configs/api.php";
                if (!$companyName) {
                    echo "<div class='alert alert-danger'>Bạn cần đăng nhập để xem thông tin.</div>";
                    exit;
                }

                function format_quantity($number)
                {
                    $number = (float)$number;
                    if ($number == floor($number)) {
                        return number_format($number, 0);
                    } else {
                        return rtrim(rtrim(number_format($number, 2), '0'), '.');
                    }
                }

                $responseJson = callAPI('layTatCaDonTra', ['companyName' => $companyName]);
                $response = json_decode($responseJson, true);

                $groupedBills = [];
                if ($response['status'] === 'Success' && !empty($response['data'])) {
                    $billsData = $response['data'];
                    usort($billsData, function ($a, $b) {
                        return strtotime($b['ReturnRequestDate']) - strtotime($a['ReturnRequestDate']);
                    });

                    foreach ($billsData as $item) {
                        $returnId = $item['ID_Return'];

                        if (!isset($groupedBills[$returnId])) {
                            $groupedBills[$returnId] = [
                                'info' => [
                                    'ID_Return' => $item['ID_Return'],
                                    'ID_BILL' => $item['ID_BILL'],
                                    'DepName' => $item['DepName'],
                                    'Userid' => $item['Userid'],
                                    'BorrowerName' => $item['BorrowerName'],
                                    'OfficerId' => $item['OfficerId'],
                                    'OfficerName' => $item['OfficerName'],
                                    'DateBorrow' => $item['DateBorrow'],
                                    'ReturnRequestDate' => $item['ReturnRequestDate'],
                                    'isConfirmReturn' => $item['isConfirmReturn'],
                                    'TotalQuantityBorrow' => 0,
                                    'TotalQuantityReturn' => 0,
                                ],
                                'items' => [],
                                'search_content' => strtolower($item['ID_Return'] . ' ' . $item['ID_BILL'] . ' ' . $item['Userid'] . ' ' . $item['BorrowerName'] . ' ' . $item['DepName'] . ' ' . $item['OfficerId'] . ' ' . $item['OfficerName'])
                            ];
                        }

                        $groupedBills[$returnId]['info']['TotalQuantityBorrow'] += $item['QuantityBorrow'];
                        $groupedBills[$returnId]['info']['TotalQuantityReturn'] += $item['QuantityReturn'];

                        $groupedBills[$returnId]['items'][] = $item;

                        $maDangPhom = trim(explode('(', $item['LastName'])[0]);
                        $groupedBills[$returnId]['search_content'] .= ' ' . strtolower($maDangPhom . ' ' . $item['LastName']);
                    }
                }
                ?>

                <?php if (empty($groupedBills)): ?>
                    <div class="alert alert-info text-center mt-4">
                        <i class="fas fa-box-open"></i> Chưa có đơn trả nào.
                    </div>
                <?php endif; ?>

                <?php foreach ($groupedBills as $returnId => $bill): ?>
                    <?php
                    $info = $bill['info'];
                    $items = $bill['items'];

                    $totalBorrowQty = $info['TotalQuantityBorrow'];
                    $totalReturnQty = $info['TotalQuantityReturn'];

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
                    ?>

                    <div class="lend-card return-card" style="<?= $cardStyle ?>" data-content="<?= htmlspecialchars($bill['search_content']) ?>">
                        <div class="card-status">
                            <span class="status-badge" style="background-color: <?= $statusBadgeBg ?>;">
                                <?= $statusText ?>
                            </span>
                        </div>

                        <div class="card-info-section">
                            <div class="info-grid">
                                <div><strong>Mã trả:</strong> <?= htmlspecialchars($info['ID_Return']) ?></div>
                                <div><strong>Số thẻ:</strong> <?= htmlspecialchars($info['Userid']) ?></div>
                                <div><strong>Tên người trả:</strong> <?= htmlspecialchars($info['BorrowerName']) ?></div>
                                <div><strong>Đơn vị:</strong> <?= htmlspecialchars($info['DepName']) ?></div>
                                <div><strong>Số thẻ cán bộ:</strong> <?= htmlspecialchars($info['OfficerId']) ?></div>
                                <div><strong>Tên cán bộ:</strong> <?= htmlspecialchars($info['OfficerName']) ?></div>
                                <div><strong>Ngày mượn:</strong> <?= htmlspecialchars(date("d/m/Y", strtotime($info['DateBorrow']))) ?></div>
                                <div><strong>Ngày trả:</strong> <?= htmlspecialchars(date("d/m/Y", strtotime($info['ReturnRequestDate']))) ?></div>
                                <div><strong>Tổng SL mượn:</strong> <?= format_quantity($totalBorrowQty) ?> đôi</div>
                                <div>
                                    <strong>Tổng SL đã trả:</strong> <span style="font-weight:bold; color: #0d6efd;"><?= format_quantity($totalReturnQty) ?></span> đôi
                                </div>
                            </div>
                        </div>

                        <!-- Bảng chi tiết các phom -->
                        <table class="lend-table">
                            <thead>
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
                                <?php foreach ($items as $item): ?>
                                    <?php
                                    $maDangPhom = trim(explode('(', $item['LastName'])[0]);

                                    $row_style = '';
                                    if (round($item['QuantityReturn'], 2) < round($item['QuantityBorrow'], 2)) {
                                        $row_style = 'background-color: #fce8e8;';
                                    } elseif (round($item['QuantityReturn'], 2) == round($item['QuantityBorrow'], 2)) {
                                        $row_style = 'background-color: #ffffff;';
                                    }
                                    ?>
                                    <tr style="<?= $row_style ?>">
                                        <td><?= htmlspecialchars($maDangPhom) ?></td>
                                        <td><?= htmlspecialchars($item['LastName']) ?></td>
                                        <td><?= htmlspecialchars(trim($item['LastSize'])) ?></td>
                                        <td><?= format_quantity($item['QuantityBorrow']) ?></td>
                                        <td style="font-weight: bold;"><?= format_quantity($item['QuantityReturn']) ?></td>

                                        <td style="text-align: center; vertical-align: top; color: #dc3545; font-weight: 500;">
                                            <?php
                                            if (!empty(trim($item['RFID_Shortcut']))) {
                                                $rfid_array = explode(',', $item['RFID_Shortcut']);

                                                $rfid_array = array_map('trim', $rfid_array);

                                                $rfid_array = array_filter($rfid_array);

                                                echo implode('<br>', $rfid_array);
                                            }
                                            ?>
                                        </td>
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

        function filterCards() {
            const keyword = document.getElementById('searchInput').value.trim().toLowerCase();
            const cards = document.querySelectorAll('.return-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const content = card.getAttribute('data-content');
                if (content.includes(keyword)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('no-result-message').style.display = visibleCount === 0 ? 'block' : 'none';
        }
    </script>
</body>

</html>