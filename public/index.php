<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
include_once __DIR__ . '/../configs/api.php';

$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';
$response = json_decode(callAPI("layTatCaPhom", ["companyName" => $companyName]), true);
$data = isset($response["data"]) ? $response["data"] : [];
?>

<!DOCTYPE html>
<html lang="en">

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
                                <h5 class="text-center">Tổng tồn kho / Tổng đã mượn / Tổng nhập</h5>
                                <canvas id="chartInventory"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h5 class="text-center">Tồn kho theo mã phom</h5>
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
                                <h5 class="text-center">Thống kê size theo mã phom</h5>
                                <canvas id="chartBySizeByLastNo"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const phomData = <?php echo json_encode($data); ?>;

        // Định nghĩa các mảng màu dễ nhìn hơn, sử dụng các tông màu sáng và đa dạng
        const vibrantColors1 = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#E7E9ED']; // Đỏ, Xanh dương, Vàng, Xanh ngọc, Tím, Cam...
        const vibrantColors2 = ['#4BC0C0', '#FFCE56', '#FF6384', '#36A2EB', '#C9CBCF', '#9966FF', '#FF9F40', '#E7E9ED']; // Xanh ngọc, Vàng, Đỏ, Xanh dương...
        const vibrantColors3 = ['#36A2EB', '#FFCE56', '#FF6384', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#E7E9ED']; // Xanh dương, Vàng, Đỏ, Xanh ngọc...
        const vibrantColors4 = ['#FFCE56', '#FF6384', '#36A2EB', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#E7E9ED']; // Vàng, Đỏ, Xanh dương, Xanh ngọc...

        function getChartColors(colorSet, index) {
            return colorSet[index % colorSet.length];
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
                const years = Array.from(new Set(phomData.map(item => new Date(item.DateIn).getFullYear()))).sort((a, b) => b - a);
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
            if (type === 'all' || !valueEl) return phomData;

            const value = valueEl.value;
            return phomData.filter(item => {
                const d = new Date(item.DateIn);
                if (type === 'month') return d.getMonth() + 1 == value;
                if (type === 'quarter') return Math.floor(d.getMonth() / 3) + 1 == value;
                if (type === 'year') return d.getFullYear() == value;
                return true;
            });
        }

        function renderCharts(data) {
            Chart.helpers.each(Chart.instances, function(instance) {
                instance.destroy();
            });

            let totalInStock = 0;
            let totalPairsEntered = 0;
            const byLastNo = {};
            const borrowedByPhomSize = {};
            const sizeByLastNo = {};
            const allSizesSet = new Set();

            data.forEach(item => {
                const pairsInStock = parseInt(item.QtyInStock || 0);
                const totalPairsForEntry = parseInt(item.TotalPairs || 0);

                totalInStock += pairsInStock;
                totalPairsEntered += totalPairsForEntry;

                const lastNo = item.LastNo?.trim() || 'Không xác định';
                const size = item.LastSize?.trim() || 'Không xác định';

                byLastNo[lastNo] = (byLastNo[lastNo] || 0) + pairsInStock;

                const borrowedForEntry = totalPairsForEntry - pairsInStock;
                if (!borrowedByPhomSize[lastNo]) {
                    borrowedByPhomSize[lastNo] = {};
                }
                borrowedByPhomSize[lastNo][size] = (borrowedByPhomSize[lastNo][size] || 0) + borrowedForEntry;
                allSizesSet.add(size);

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
                        backgroundColor: ['#3498DB', '#E74C3C', '#2ECC71'], // Xanh dương, Đỏ, Xanh lá cây
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tổng tồn kho / Tổng đã mượn / Tổng nhập kho (theo đôi)'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng (đôi)'
                            }
                        }
                    }
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
                        backgroundColor: labelsByLastNo.map((_, i) => getChartColors(vibrantColors1, i))
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tồn kho theo mã phom (số đôi)'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed + ' đôi (' + (context.parsed / dataByLastNo.reduce((a, b) => a + b, 0) * 100).toFixed(1) + '%)';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // --- Thống kê đã mượn theo mã phom và size (Grouped Bar Chart) ---
            const allSizesSorted = [...allSizesSet].sort((a, b) => {
                const numA = parseFloat(a);
                const numB = parseFloat(b);
                if (!isNaN(numA) && !isNaN(numB)) {
                    return numA - numB;
                }
                return a.localeCompare(b);
            });

            const borrowedSizeDatasets = Object.entries(borrowedByPhomSize).map(([lastNo, sizes], i) => {
                return {
                    label: lastNo,
                    data: allSizesSorted.map(size => sizes[size] || 0),
                    backgroundColor: getChartColors(vibrantColors2, i),
                };
            });

            new Chart('chartByBorrowedPhomSize', {
                type: 'bar',
                data: {
                    labels: allSizesSorted,
                    datasets: borrowedSizeDatasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Thống kê đã mượn theo mã phom và size (số đôi)'
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Size'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng đã mượn (đôi)'
                            }
                        }
                    }
                }
            });

            // Chart 4: Thống kê tồn kho theo mã phom và size (Grouped Bar Chart)
            const stockSizeDatasets = Object.entries(sizeByLastNo).map(([lastNo, sizes], i) => {
                return {
                    label: lastNo,
                    data: allSizesSorted.map(size => sizes[size] || 0),
                    backgroundColor: getChartColors(vibrantColors3, i),
                };
            });

            new Chart('chartBySizeByLastNo', {
                type: 'bar',
                data: {
                    labels: allSizesSorted,
                    datasets: stockSizeDatasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Thống kê tồn kho theo mã phom và size (số đôi)'
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Size'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng (đôi)'
                            }
                        }
                    }
                }
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            updateFilterOptions();
            renderCharts(phomData);
        });

        document.addEventListener("change", e => {
            if (['filterType', 'filterValue'].includes(e.target.id)) {
                if (e.target.id === 'filterType') updateFilterOptions();
                setTimeout(() => {
                    const filtered = filterData();
                    renderCharts(filtered);
                }, 50);
            }
        });
    </script>
</body>

</html>