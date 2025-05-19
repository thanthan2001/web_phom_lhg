<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';

$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo đơn mượn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-buttons button {
            width: 130px;
            padding: 8px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 500;
            background-color: #B9B9B9;
        }

        .tab-buttons button.active {
            background-color: #0093D3;
            color: white;
        }

        .tab-main {
            padding-left: 50px;
            padding-right: 50px;
        }

        .lend-card {
            background-color: rgba(20, 174, 92, 0.2);
            border: 1px solid #14AE5C;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .lend-table {
            background-color: #ffffff;
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
        }

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

        .btn-create-lend {
            background-color: #064469;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            float: right;
        }

        .search-box {
            display: flex;
            gap: 10px;
            align-items: center;

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


        .text-end {
            text-align: end;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px 10px;
            margin-bottom: 10px;
            font-size: 14px;
            align-items: center;
        }

        .info-grid:nth-child(2) {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .info-grid:nth-child(3) {
            grid-template-columns: repeat(5, minmax(0, 1fr));
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
                <!-- Tabs -->
                <div class="tab-buttons">
                    <button class="tab-btn active" data-tab="borrow-register">Đăng ký mượn</button>
                    <button class="tab-btn" data-tab="return-register">Đăng ký trả</button>
                    <button class="tab-btn" data-tab="transfer">Chuyển mượn</button>
                </div>

                <!-- Nội dung từng tab -->
                <div class="tab-main" id="tab-borrow-register">
                    <div id="borrow-register-content">Đang tải...</div>
                </div>
                <div class="tab-main d-none" id="tab-return-register"><?php include "tabs/tab_return_register.php"; ?></div>
                <div class="tab-main d-none" id="tab-transfer"><?php include "tabs/tab_transfer.php"; ?></div>

            </div>
        </div>
    </div>

    <script>
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = {
            "borrow-register": document.getElementById('tab-borrow-register'),
            "return-register": document.getElementById('tab-return-register'),
            "transfer": document.getElementById('tab-transfer')
        };

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tab = button.dataset.tab;

                tabButtons.forEach(b => b.classList.remove('active'));
                button.classList.add('active');

                Object.values(tabContents).forEach(c => c.classList.add('d-none'));
                tabContents[tab].classList.remove('d-none');
            });
        });


        function toggleAll(checkbox, tabId) {
            const checkboxes = document.querySelectorAll(`.item-checkbox[data-tab="${tabId}"]`);
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateCount(tabId);
        }

        function updateCount(tabId) {
            const checkboxes = document.querySelectorAll(`.item-checkbox[data-tab="${tabId}"]`);
            const count = [...checkboxes].filter(cb => cb.checked).length;
            document.getElementById(`count-${tabId}`).innerText = count;
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
        }

        function loadBorrowRegister(page = 1) {
            const container = document.getElementById('borrow-register-content');
            container.innerHTML = "Đang tải...";

            fetch(`tabs/tab_borrow_register.php?page=${page}`)
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;

                    // Gán lại sự kiện cho các nút phân trang mới
                    document.querySelectorAll('.borrow-page-link').forEach(link => {
                        link.addEventListener('click', e => {
                            e.preventDefault();
                            const page = link.getAttribute('data-page');
                            loadBorrowRegister(page);
                        });
                    });
                });
        }

        // Tải nội dung khi bấm tab
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                const tab = button.dataset.tab;
                if (tab === 'borrow-register') {
                    loadBorrowRegister();
                }
            });
        });

        // Tải lần đầu nếu tab đăng ký mượn đang hiển thị
        document.addEventListener("DOMContentLoaded", () => {
            if (!document.getElementById("tab-borrow-register").classList.contains('d-none')) {
                loadBorrowRegister();
            }
        });
    </script>
</body>

</html>