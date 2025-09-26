<?php
include "session_start.php";
include_once __DIR__ . '/modals/login_modal.php';
// Mặc dù giao diện mới không dùng dữ liệu này trực tiếp,
// chúng ta vẫn giữ lại để bạn có thể tích hợp sau này.
include_once __DIR__ . '/../configs/api.php';
// Đọc file .env (nằm ngoài thư mục public)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue; // Bỏ comment
        if (strpos($line, '=') === false)
            continue; // Bỏ dòng sai định dạng
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

// Dùng isset() để tránh lỗi trên PHP cũ
$BASE_URL = isset($_ENV['BASE_URL']) ? $_ENV['BASE_URL'] : '';
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$companyName = isset($user['companyName']) ? $user['companyName'] : '';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Quản Lý Phom Giày</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js">
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<style>
/* CSS CỦA BẠN - GIỮ NGUYÊN */
body {
    background-color: #f4f7f6;
    font-family: Arial, sans-serif;
}

.main-content {
    padding: 20px !important;
}

.dashboard-title {
    padding-top: 25px;
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 24px;
    color: #333;
}

.kpi-card {
    background-color: #6460600e;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(88, 75, 75, 0.88);
    margin-bottom: 20px;
}

.kpi-card .number {
    font-size: 28px;
    font-weight: bold;
    color: #0d6efd;
}

.kpi-card .number.orange {
    color: #fd7e14;
}

.kpi-card .number.red {
    color: #dc3545;
}

.kpi-card .number.green {
    color: #198754;
}

.kpi-card .number.purple {
    color: #6f42c1;
}

.kpi-card .label {
    font-size: 14px;
    color: #6c757d;
    margin-top: 5px;
}

.chart-card {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(88, 75, 75, 0.88);
    width: 100%;
    height: 100%;
}

.chart-card h5 {
    text-align: center;
    font-weight: bold;
    color: #495057;
    margin-bottom: 20px;
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
                <div class="container-fluid pt-4" x-data="dashboard">
                    <h4 class="dashboard-title">DASHBOARD QUẢN LÝ PHOM</h4>

                    <!-- KPI Row -->
                    <div class="row">
                        <!-- Giữ nguyên -->
                        <div class="col">
                            <div class="kpi-card">
                                <div class="number" x-text="kpi.TongSoLuong.toLocaleString('vi-VN') || '...'"></div>
                                <div class="label">Tổng số phom</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="kpi-card">
                                <div class="number green" x-text="kpi.DangSuDung.toLocaleString('vi-VN') || '...'">
                                </div>
                                <div class="label">Phom đang sử dụng</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="kpi-card">
                                <div class="number orange" x-text="kpi.TonKho.toLocaleString('vi-VN') || '...'"></div>
                                <div class="label">Phom tồn kho</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="kpi-card">
                                <div class="number" x-text="kpi.TyLeSD ? `${kpi.TyLeSD.toFixed(1)}%` : '...'"></div>
                                <div class="label">Tỷ lệ sử dụng</div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Rows -->
                    <div class="row g-4 mb-4">
                        <div class="col-lg-6">
                            <div class="chart-card">
                                <h5>Tỷ Trọng Trạng Thái Phom</h5>
                                <div style="position: relative; height: 320px;"><canvas id="pieChartStatus"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="chart-card">
                                <h5>Tồn Kho Phom Theo Tháng</h5>
                                <div class="row mb-2 align-items-center justify-content-center">
                                    <div class="col-auto"><label for="selectMonthlyYear"
                                            class="col-form-label">Năm:</label></div>
                                    <div class="col-auto">
                                        <select class="form-select" id="selectMonthlyYear" x-model="monthlyChartYear">
                                            <template x-for="yearNum in years" :key="yearNum">
                                                <option :value="yearNum" x-text="yearNum"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-primary btn-sm"
                                            @click="fetchAndRenderMonthlyStockChart()"
                                            :disabled="isMonthlyChartLoading">
                                            <span x-show="!isMonthlyChartLoading">Cập nhật</span>
                                            <span x-show="isMonthlyChartLoading">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                Đang tải...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div style="position: relative; height: 320px;"><canvas
                                        id="barChartWeeklyStock"></canvas></div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="chart-card">
                                <h5>Tổng phom đã mượn theo đơn vị</h5>
                                <div class="row mb-3 align-items-center justify-content-center">
                                    <div class="col-auto"><label for="selectMonth" class="col-form-label">Tháng:</label>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select" id="selectMonth" x-model="selectedMonth">
                                            <option value="">Tất cả</option>
                                            <template x-for="monthNum in 12" :key="monthNum">
                                                <option :value="monthNum" x-text="monthNum"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="col-auto"><label for="selectYear" class="col-form-label">Năm:</label>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select" id="selectYear" x-model="selectedYear">
                                            <option value="">Tất cả</option>
                                            <template x-for="yearNum in years" :key="yearNum">
                                                <option :value="yearNum" x-text="yearNum"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <!-- SỬA LỖI: CẬP NHẬT NÚT BẤM NÀY -->
                                    <div class="col-auto">
                                        <button class="btn btn-primary" @click="fetchColumnChartData()"
                                            :disabled="isColumnChartLoading">
                                            <span x-show="!isColumnChartLoading">Cập nhật</span>
                                            <span x-show="isColumnChartLoading">
                                                <span class="spinner-border spinner-border-sm" role="status"
                                                    aria-hidden="true"></span>
                                                Đang tải...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div style="position: relative; height: 320px;"><canvas
                                        id="barChartStockByType"></canvas></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
            kpi: {},
            barChartStockByTypeInstance: null,
            columnChartData: {
                labels: [],
                values: []
            },
            selectedMonth: '',
            selectedYear: '',
            years: [],
            monthlyChartYear: new Date().getFullYear(),
            isMonthlyChartLoading: false,
            // SỬA LỖI: THÊM CỜ TRẠNG THÁI CHO BIỂU ĐỒ CỘT
            isColumnChartLoading: false,

            init() {
                Chart.register(ChartDataLabels);
                Chart.defaults.plugins.datalabels.font.weight = 'bold';
                Chart.defaults.plugins.datalabels.color = '#444';
                this.generateYears();
                this.getData();
                this.fetchColumnChartData();
                this.fetchAndRenderMonthlyStockChart();
            },

            generateYears() {
                const currentYear = new Date().getFullYear();
                for (let i = currentYear - 5; i <= currentYear + 5; i++) {
                    this.years.push(i);
                }
            },

            async getData() {
                // Giữ nguyên hàm này
                const BASE_URL = <?php echo json_encode(rtrim($BASE_URL, '/')); ?>;
                const companyName = <?php echo json_encode($companyName); ?>;
                try {
                    const response = await fetch(`${BASE_URL}/phom/statisticalParameters`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            companyName: companyName
                        })
                    });
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const apiResponse = await response.json();
                    if (apiResponse.data && Array.isArray(apiResponse.data) && apiResponse.data
                        .length > 0) {
                        this.kpi = apiResponse.data[0];
                    }
                    this.renderCharts();
                } catch (err) {
                    console.error("Lỗi khi lấy dữ liệu KPI:", err);
                }
            },

            // SỬA LỖI: CẬP NHẬT HÀM NÀY
            async fetchColumnChartData() {
                if (this.isColumnChartLoading) return; // Kiểm tra
                this.isColumnChartLoading = true; // Bật cờ

                const BASE_URL = <?php echo json_encode(rtrim($BASE_URL, '/')); ?>;
                const companyName = <?php echo json_encode($companyName); ?>;
                try {
                    const response = await fetch(
                        `${BASE_URL}/phom/statisticalParametersColumn`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                companyName: companyName,
                                month: this.selectedMonth,
                                year: this.selectedYear
                            })
                        });
                    if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
                    const apiResponse = await response.json();
                    if (apiResponse.data && Array.isArray(apiResponse.data)) {
                        this.columnChartData.labels = apiResponse.data.map(item => item
                        .DepName);
                        this.columnChartData.values = apiResponse.data.map(item => item
                            .TotalCount);
                    } else {
                        this.columnChartData.labels = [];
                        this.columnChartData.values = [];
                    }
                } catch (err) {
                    console.error("Lỗi khi lấy dữ liệu biểu đồ cột:", err);
                    this.columnChartData.labels = [];
                    this.columnChartData.values = [];
                } finally {
                    this.updateColumnChart(); // Luôn cập nhật biểu đồ sau khi fetch
                    this.isColumnChartLoading = false; // Tắt cờ
                }
            },

            async fetchAndRenderMonthlyStockChart() {
                // Hàm này đã được sửa ở lần trước, giữ nguyên
                if (this.isMonthlyChartLoading) return;
                this.isMonthlyChartLoading = true;

                const BASE_URL = <?php echo json_encode(rtrim($BASE_URL, '/')); ?>;
                const companyName = <?php echo json_encode($companyName); ?>;
                const canvasElement = document.getElementById('barChartWeeklyStock');
                try {
                    const response = await fetch(`${BASE_URL}/phom/statisticalMonthly`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            companyName: companyName,
                            year: this.monthlyChartYear
                        })
                    });
                    if (!response.ok) throw new Error(`Lỗi API: ${response.statusText}`);
                    const result = await response.json();

                    let existingChart = Chart.getChart(canvasElement);
                    if (existingChart) existingChart.destroy();

                    if (result.status !== "Success" || !result.data || result.data.length ===
                        0) {
                        const ctx = canvasElement.getContext('2d');
                        ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
                        ctx.font = '16px Arial';
                        ctx.textAlign = 'center';
                        ctx.fillText(`Không có dữ liệu cho năm ${this.monthlyChartYear}.`,
                            canvasElement.width / 2, canvasElement.height / 2);
                        return;
                    }

                    const sortedData = result.data.sort((a, b) => a.M - b.M);
                    const labels = sortedData.map(item => `Tháng ${item.M}`);
                    const muonData = sortedData.map(item => item.SoMuonTrongThang);
                    const traData = sortedData.map(item => item.SoTraTrongThang);
                    const tonData = sortedData.map(item => item.TonCuoiThang);

                    new Chart(canvasElement, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Số Mượn',
                                data: muonData,
                                borderColor: 'rgb(54, 162, 235)',
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                tension: 0.1
                            }, {
                                label: 'Số Trả',
                                data: traData,
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                tension: 0.1
                            }, {
                                label: 'Tồn Cuối Tháng',
                                data: tonData,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top'
                                },
                                title: {
                                    display: true,
                                    text: `Thống Kê Mượn-Trả-Tồn Năm ${this.monthlyChartYear}`
                                },
                                datalabels: {
                                    display: true,
                                    align: 'top',
                                    offset: 8,
                                    backgroundColor: 'rgba(255, 255, 255, 0.7)',
                                    borderRadius: 4,
                                    font: {
                                        size: 9
                                    },
                                    formatter: (value) => value > 0 ? value
                                        .toLocaleString('vi-VN') : ''
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error("Lỗi khi vẽ biểu đồ thống kê tháng:", error);
                } finally {
                    this.isMonthlyChartLoading = false;
                }
            },

            renderCharts() {
                // Giữ nguyên hàm này
                const dataPieStatus = {
                    labels: ['Đang sử dụng', 'Trong kho'],
                    values: [this.kpi.TyLeSD || 0, ((this.kpi.TonKho / this.kpi.TongSoLuong) *
                        100 || 0)]
                };
                Chart.helpers.each(Chart.instances, (instance) => {
                    const canvasId = instance.canvas.id;
                    if (canvasId !== 'barChartStockByType' && canvasId !==
                        'barChartWeeklyStock') {
                        instance.destroy();
                    }
                });
                new Chart(document.getElementById('pieChartStatus'), {
                    type: 'pie',
                    data: {
                        labels: dataPieStatus.labels,
                        datasets: [{
                            data: dataPieStatus.values,
                            backgroundColor: ['#28a745', '#ffc107'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            datalabels: {
                                formatter: (value) => `${value.toFixed(1)}%`,
                                color: '#fff'
                            }
                        }
                    }
                });
            },

            updateColumnChart() {
                // Giữ nguyên hàm này
                const ctx = document.getElementById('barChartStockByType');
                if (this.barChartStockByTypeInstance) this.barChartStockByTypeInstance.destroy();
                this.barChartStockByTypeInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.columnChartData.labels,
                        datasets: [{
                            label: 'Tổng số phom',
                            data: this.columnChartData.values,
                            backgroundColor: ['#20c997', '#6f42c1', '#fd7e14',
                                '#0d6efd', '#d63384', '#17a2b8', '#ffc107',
                                '#28a745', '#dc3545', '#6c757d'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Số lượng'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Đơn vị (DepID)'
                                }
                            }
                        }
                    }
                });
            }
        }));
    });
    </script>
</body>

</html>