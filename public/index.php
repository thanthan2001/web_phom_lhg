<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
include_once __DIR__ . '/../configs/api.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';
// API trả về một object, trong đó có key "data" chứa 2 mảng.
$response = json_decode(callAPI("getBorrowPhomState", ["companyName" => $companyName]), true);
// Lấy toàn bộ object `data` để Javascript có thể truy cập cả 2 mảng con
$data = isset($response["data"]) ? $response["data"] : ["danhSachPhomTrongKho" => [], "danhSachDonMuon" => []];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê kho</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<style>
    body {
        background-color: #f8f9fa;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .form-select {
        border-radius: 8px;
    }

    canvas {
        max-height: 400px;
    }

    .filter-section {
        background: white;
        padding: 16px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    h5 {
        font-weight: bold;
        margin-bottom: 16px;
    }
</style>

<body>
    <div class="wrapper">
        <?php include_once '../public/partials/sidebar.php' ?>
        <div class="content">
            <div class="main-header">
                <?php include_once '../public/partials/header.php' ?>
            </div>
            <div class="main-content" style="padding-top: 70px;">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="filter-section">
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-3">
                                        <select class="form-select" id="filterType">
                                            <option value="all">Tất cả</option>
                                            <option value="month">Theo tháng</option>
                                            <option value="quarter">Theo quý</option>
                                            <option value="year">Theo năm</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3" id="filterValueContainer"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h5 class="text-center">Tổng quan kho (tính theo đôi)</h5>
                                <canvas id="chartInventory"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h5 class="text-center">Tỷ lệ tồn kho theo mã phom</h5>
                                <canvas id="chartByLastNo"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h5 class="text-center">Thống kê đã mượn theo mã phom và size</h5>
                                <canvas id="chartByBorrowedPhomSize"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h5 class="text-center">Thống kê tồn kho theo mã phom và size</h5>
                                <canvas id="chartBySizeByLastNo"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dữ liệu từ API giờ là một object chứa 2 mảng
        const apiData = <?php echo json_encode($data); ?>;
        const phomData = apiData.danhSachPhomTrongKho || [];

        const vibrantColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#E7E9ED', '#6F4E37', '#B87333', '#800000', '#008080'];

        function getChartColors(index) {
            return vibrantColors[index % vibrantColors.length];
        }

        function updateFilterOptions() {
            const filterType = document.getElementById("filterType").value;
            const container = document.getElementById("filterValueContainer");
            container.innerHTML = '';

            if (filterType === 'month') {
                const select = document.createElement('select');
                select.className = 'form-select';
                select.id = 'filterValue';
                for (let m = 1; m <= 12; m++) {
                    const monthText = m < 10 ? `0${m}` : `${m}`;
                    select.innerHTML += `<option value="${m}">Tháng ${monthText}</option>`;
                }
                container.appendChild(select);
            } else if (filterType === 'quarter') {
                const select = document.createElement('select');
                select.className = 'form-select';
                select.id = 'filterValue';
                for (let q = 1; q <= 4; q++) {
                    select.innerHTML += `<option value="${q}">Quý ${q}</option>`;
                }
                container.appendChild(select);
            } else if (filterType === 'year') {
                const select = document.createElement('select');
                select.className = 'form-select';
                select.id = 'filterValue';
                // Lấy danh sách năm từ dữ liệu có ngày nhập (DateIn)
                const years = Array.from(new Set(phomData.filter(item => item.DateIn).map(item => new Date(item.DateIn).getFullYear()))).sort((a, b) => b - a);
                years.forEach(year => {
                    select.innerHTML += `<option value="${year}">${year}</option>`;
                });
                const currentYear = new Date().getFullYear();
                if (years.includes(currentYear)) {
                    select.value = currentYear;
                }
                container.appendChild(select);
            }
        }

        function filterData() {
            const type = document.getElementById('filterType').value;
            const valueEl = document.getElementById('filterValue');
            
            if (type === 'all' || !valueEl) {
                return phomData; // Trả về toàn bộ dữ liệu nếu chọn "Tất cả"
            }

            const value = valueEl.value;
            return phomData.filter(item => {
                if (!item.DateIn) return false; // Chỉ lọc các item có ngày nhập kho
                const d = new Date(item.DateIn);
                if (type === 'month') return d.getMonth() + 1 == value;
                if (type === 'quarter') return Math.floor(d.getMonth() / 3) + 1 == value;
                if (type === 'year') return d.getFullYear() == value;
                return true;
            });
        }

        function renderCharts(data) {
            // Hủy các biểu đồ cũ trước khi vẽ lại
            Chart.helpers.each(Chart.instances, function(instance) {
                instance.destroy();
            });

            // Khởi tạo các biến tổng hợp
            let totalInStock = 0;
            let totalPairsEntered = 0;
            const byLastNo = {};
            const borrowedByPhomSize = {};
            const sizeByLastNo = {};
            const allSizesSet = new Set();

            data.forEach(item => {
                // Thay đổi: Dùng parseFloat và tên trường mới (QtyInStock_Pairs, TotalPairs)
                const pairsInStock = parseFloat(item.QtyInStock_Pairs || 0);
                const totalPairsForEntry = parseFloat(item.TotalPairs || 0);

                totalInStock += pairsInStock;
                totalPairsEntered += totalPairsForEntry;

                const lastNo = item.LastNo?.trim() || 'Không xác định';
                const size = item.LastSize?.trim() || 'Không xác định';

                // Tồn kho theo mã phom
                byLastNo[lastNo] = (byLastNo[lastNo] || 0) + pairsInStock;
                
                // Tính số lượng đã mượn: Tổng nhập - Tồn kho
                const borrowedForEntry = totalPairsForEntry - pairsInStock;

                // Thống kê đã mượn theo mã phom và size
                if (!borrowedByPhomSize[lastNo]) {
                    borrowedByPhomSize[lastNo] = {};
                }
                borrowedByPhomSize[lastNo][size] = (borrowedByPhomSize[lastNo][size] || 0) + borrowedForEntry;
                allSizesSet.add(size);
                
                // Thống kê tồn kho theo mã phom và size
                if (!sizeByLastNo[lastNo]) sizeByLastNo[lastNo] = {};
                sizeByLastNo[lastNo][size] = (sizeByLastNo[lastNo][size] || 0) + pairsInStock;
            });

            const totalBorrowed = totalPairsEntered - totalInStock;

            // Chart 1: Tổng tồn kho / Tổng đã mượn / Tổng nhập
            new Chart('chartInventory', {
                type: 'bar',
                data: {
                    labels: ['Tổng tồn kho', 'Tổng đã mượn', 'Tổng nhập kho'],
                    datasets: [{
                        label: 'Số lượng (đôi)',
                        data: [totalInStock, totalBorrowed, totalPairsEntered],
                        backgroundColor: ['#36A2EB', '#FF6384', '#4BC0C0'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.label}: ${context.raw.toFixed(1)} đôi`
                            }
                        }
                    },
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Số lượng (đôi)' } } }
                }
            });

            // Chart 2: Tồn kho theo mã phom (Pie Chart)
            const labelsByLastNo = Object.keys(byLastNo);
            const dataByLastNo = Object.values(byLastNo);
            new Chart('chartByLastNo', {
                type: 'pie',
                data: {
                    labels: labelsByLastNo,
                    datasets: [{
                        data: dataByLastNo,
                        backgroundColor: labelsByLastNo.map((_, i) => getChartColors(i))
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? (context.parsed / total * 100).toFixed(1) : 0;
                                        label += `${context.parsed.toFixed(1)} đôi (${percentage}%)`;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
            
            // Sắp xếp các size để hiển thị trên trục X một cách hợp lý
            const allSizesSorted = [...allSizesSet].sort((a, b) => {
                const numA = parseFloat(a);
                const numB = parseFloat(b);
                if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
                return a.localeCompare(b);
            });
            
            // Chart 3: Thống kê đã mượn theo mã phom và size (Grouped Bar Chart)
            const borrowedSizeDatasets = Object.entries(borrowedByPhomSize).map(([lastNo, sizes], i) => ({
                label: lastNo,
                data: allSizesSorted.map(size => sizes[size] || 0),
                backgroundColor: getChartColors(i),
            }));

            new Chart('chartByBorrowedPhomSize', {
                type: 'bar',
                data: { labels: allSizesSorted, datasets: borrowedSizeDatasets },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        x: { stacked: false, title: { display: true, text: 'Size' } },
                        y: { stacked: false, beginAtZero: true, title: { display: true, text: 'Số lượng đã mượn (đôi)' } }
                    }
                }
            });

            // Chart 4: Thống kê tồn kho theo mã phom và size (Grouped Bar Chart)
            const stockSizeDatasets = Object.entries(sizeByLastNo).map(([lastNo, sizes], i) => ({
                label: lastNo,
                data: allSizesSorted.map(size => sizes[size] || 0),
                backgroundColor: getChartColors(i + 1), // Dùng màu khác để phân biệt
            }));

            new Chart('chartBySizeByLastNo', {
                type: 'bar',
                data: { labels: allSizesSorted, datasets: stockSizeDatasets },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        x: { stacked: false, title: { display: true, text: 'Size' } },
                        y: { stacked: false, beginAtZero: true, title: { display: true, text: 'Số lượng tồn kho (đôi)' } }
                    }
                }
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            updateFilterOptions();
            renderCharts(phomData); // Render lần đầu với toàn bộ dữ liệu
        });

        // Lắng nghe sự kiện thay đổi trên các bộ lọc
        document.addEventListener("change", e => {
            if (['filterType', 'filterValue'].includes(e.target.id)) {
                if (e.target.id === 'filterType') {
                    updateFilterOptions();
                }
                // Dùng setTimeout để đảm bảo DOM đã cập nhật (đặc biệt khi đổi filterType)
                setTimeout(() => {
                    const filtered = filterData();
                    renderCharts(filtered);
                }, 50);
            }
        });
    </script>
</body>

</html>