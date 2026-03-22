<?php
require_once __DIR__ . "/../models/bill.php";
require_once __DIR__ . "/../models/products.php"; // Include Products model

$billObj = new Bill();
$productObj = new Products(); // Instantiate Products model

$stats = $billObj->getRevenueStats();
$completedOrdersCount = $billObj->getCompletedOrdersCount();

$stats = $billObj->getRevenueStats();
$completedOrdersCount = $billObj->getCompletedOrdersCount();

$bestsellingProducts = $productObj->getBestsellingProducts(5); // Fetch top 5 bestselling products

$dates = [];
$revenues = [];

foreach ($stats as $row) {
    $dates[] = date('d/m', strtotime($row['date']));
    $revenues[] = (float)$row['revenue'];
}

// Tính toán chỉ số phụ
$totalRevenue = array_sum($revenues);
$maxRevenue = empty($revenues) ? 0 : max($revenues);
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Card 1: Tổng doanh thu -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-yellow-100 flex items-center transition-transform hover:-translate-y-1 duration-300">
        <div class="w-14 h-14 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mr-5 shadow-sm">
            <i class="fa-solid fa-sack-dollar text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-400 font-bold uppercase tracking-wider mb-1">Doanh thu 7 ngày</p>
            <h4 class="text-3xl font-bold text-gray-900 font-playfair"><?= number_format($totalRevenue, 0, ',', '.') ?>₫</h4>
        </div>
    </div>

    <!-- Card 2: Đỉnh điểm -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center transition-transform hover:-translate-y-1 duration-300">
        <div class="w-14 h-14 rounded-full bg-black text-white flex items-center justify-center mr-5 shadow-sm">
            <i class="fa-solid fa-arrow-trend-up text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-400 font-bold uppercase tracking-wider mb-1">Đỉnh điểm ngày</p>
            <h4 class="text-3xl font-bold text-gray-900 font-playfair"><?= number_format($maxRevenue, 0, ',', '.') ?>₫</h4>
        </div>
    </div>
    
    <!-- Card 3: Số lượng đơn hàng hoàn thành -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center transition-transform hover:-translate-y-1 duration-300">
        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-5">
            <i class="fa-solid fa-clipboard-check text-xl"></i>
        </div>
        <div>
            <p class="text-sm text-gray-400 font-bold uppercase tracking-wider mb-1">Đơn hàng hoàn thành</p>
            <h4 class="text-3xl font-bold text-gray-900 font-playfair"><?= $completedOrdersCount ?> <span class="text-lg font-sans font-medium text-gray-400">đơn</span></h4>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="flex flex-col lg:flex-row gap-8">
    <div class="lg:w-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-xl font-bold text-gray-900 font-playfair">Biểu đồ tăng trưởng doanh thu</h3>
                <p class="text-sm text-gray-400 mt-1">Dữ liệu được cập nhật theo thời gian thực</p>
            </div>
            <span class="text-xs font-bold px-3 py-1.5 bg-gray-900 text-white rounded-full uppercase tracking-widest">Tuần này</span>
        </div>
        <div class="relative h-96 w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Tạo gradient Vàng Gold sang trọng
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(234, 179, 8, 0.4)'); // Yellow-500
    gradient.addColorStop(1, 'rgba(234, 179, 8, 0.0)');

    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($dates) ?>,
            datasets: [{
                label: 'Doanh thu',
                data: <?= json_encode($revenues) ?>,
                backgroundColor: gradient,
                borderColor: '#ca8a04', // Yellow-600
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#ca8a04',
                pointHoverBackgroundColor: '#ca8a04',
                pointHoverBorderColor: '#ffffff',
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4, // Đường cong mềm mại
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#000000',
                    titleFont: { family: 'Inter', size: 13 },
                    bodyFont: { family: 'Inter', size: 13, weight: 'bold' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [4, 4],
                        color: '#f3f4f6',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af',
                        font: { family: 'Inter', size: 11 },
                        padding: 10,
                        callback: function(value) {
                            if (value >= 1000000) return (value/1000000).toFixed(1) + 'M';
                            if (value >= 1000) return (value/1000).toFixed(0) + 'k';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { 
                        color: '#6b7280',
                        font: { family: 'Inter', size: 12, weight: '500' },
                        padding: 10
                    }
                }
            }
        }
    });
</script>