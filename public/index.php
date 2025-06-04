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
    <title>Home</title>
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
                    <!-- Bộ lọc -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="filter-section">
                                <div class="row align-items-end">
                                    <div class="col-md-4 mb-3">
                                        <label for="filterType" class="form-label">Chọn loại lọc:</label>
                                        <select class="form-select" id="filterType">
                                            <option value="all">Tất cả</option>
                                            <option value="month">Tháng</option>
                                            <option value="quarter">Quý</option>
                                            <option value="year">Năm</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3" id="filterValueContainer"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Biểu đồ -->
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h5 class="text-center">Tổng tồn kho / Đã mượn / Tổng nhập</h5>
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
                                <h5 class="text-center">Tồn kho theo kệ (Phom - Size)</h5>
                                <canvas id="chartByShelf"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3">
                                <h5 class="text-center">Thống kê size theo mã phom</h5>
                                <canvas id="chartByDateIn"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const phomData = <?php echo json_encode($data); ?>;

        function updateFilterOptions() {
            const filterType = document.getElementById("filterType").value;
            const container = document.getElementById("filterValueContainer");
            container.innerHTML = '';

            if (filterType === 'month') {
                const select = document.createElement('select');
                select.className = 'form-select';
                select.id = 'filterValue';
                for (let m = 1; m <= 12; m++) {
                    select.innerHTML += `<option value="${m}">Tháng ${m}</option>`;
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
                const years = Array.from(new Set(phomData.map(item => new Date(item.DateIn).getFullYear()))).sort();
                years.forEach(year => {
                    select.innerHTML += `<option value="${year}">${year}</option>`;
                });
                container.appendChild(select);
            }
        }

        function filterData() {
            const type = document.getElementById('filterType').value;
            const valueEl = document.getElementById('filterValue');
            if (!valueEl || type === 'all') return phomData;

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

            let totalInStock = 0,
                totalBorrowed = 0,
                totalReturned = 0;
            const byLastNo = {},
                shelfPhomSizeMap = {},
                sizeByLastNo = {},
                allPhomSizeKeysSet = new Set();

            data.forEach(item => {
                const inStock = parseInt(item.QtyInStock || 0);
                const total = parseInt(item.TotalQty || 0);
                const borrowed = total - inStock;

                totalInStock += inStock;
                totalBorrowed += borrowed;
                totalReturned += total;

                const lastNo = item.LastNo?.trim() || '';
                const size = item.LastSize?.trim() || '';
                const shelf = item.ShelfName?.trim() || '';

                byLastNo[lastNo] = (byLastNo[lastNo] || 0) + inStock;

                const phomSizeKey = `${lastNo} - ${size}`;
                if (!shelfPhomSizeMap[shelf]) shelfPhomSizeMap[shelf] = {};
                shelfPhomSizeMap[shelf][phomSizeKey] = (shelfPhomSizeMap[shelf][phomSizeKey] || 0) + inStock;
                allPhomSizeKeysSet.add(phomSizeKey);

                if (!sizeByLastNo[lastNo]) sizeByLastNo[lastNo] = {};
                sizeByLastNo[lastNo][size] = (sizeByLastNo[lastNo][size] || 0) + total;
            });

            new Chart('chartInventory', {
                type: 'bar',
                data: {
                    labels: ['Tồn kho', 'Đã mượn', 'Tổng'],
                    datasets: [{
                        label: 'Số lượng (đôi)',
                        data: [totalInStock, totalBorrowed, totalReturned],
                        backgroundColor: ['#36a2eb', '#ff6384', '#4bc0c0'],
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tổng tồn kho / Đã mượn / Tổng nhập'
                        }
                    },
                    responsive: true
                }
            });

            new Chart('chartByLastNo', {
                type: 'pie',
                data: {
                    labels: Object.keys(byLastNo),
                    datasets: [{
                        data: Object.values(byLastNo),
                        backgroundColor: Object.keys(byLastNo).map((_, i) =>
                            `hsl(${i * 360 / Object.keys(byLastNo).length}, 70%, 60%)`
                        )
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tồn kho theo mã phom'
                        }
                    },
                    responsive: true
                }
            });

            const allShelves = Object.keys(shelfPhomSizeMap);
            const allPhomSizeKeys = [...allPhomSizeKeysSet].sort();
            const shelfDatasets = allPhomSizeKeys.map((key, i) => ({
                label: key,
                data: allShelves.map(shelf => shelfPhomSizeMap[shelf]?.[key] || 0),
                backgroundColor: `hsl(${i * 360 / allPhomSizeKeys.length}, 70%, 60%)`,
                stack: 'stack1'
            }));

            new Chart('chartByShelf', {
                type: 'bar',
                data: {
                    labels: allShelves,
                    datasets: shelfDatasets
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tồn kho theo kệ (Phom - Size)'
                        },
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Tên kệ'
                            }
                        },
                        y: {
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Số lượng'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });

            const allSizes = [...new Set(Object.values(sizeByLastNo).flatMap(obj => Object.keys(obj)))].sort();
            const sizeDatasets = Object.entries(sizeByLastNo).map(([lastNo, sizes], i) => ({
                label: lastNo,
                data: allSizes.map(size => sizes[size] || 0),
                backgroundColor: `hsl(${i * 360 / Object.keys(sizeByLastNo).length}, 60%, 60%)`
            }));

            new Chart('chartByDateIn', {
                type: 'bar',
                data: {
                    labels: allSizes,
                    datasets: sizeDatasets
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Thống kê size theo mã phom'
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Size'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Số lượng'
                            },
                            beginAtZero: true
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
                }, 10);
            }
        });
    </script>
</body>

</html>