<?php
session_start();
require_once __DIR__ . "/server/database/database.php";

try {
    $db = new Database();
    $conn = $db->connect();

    // Fetch all public, active, and valid coupons
    $sql = "SELECT * FROM coupons 
            WHERE is_public = 1 
              AND is_active = 1
              AND (end_date IS NULL OR end_date > NOW())
              AND (max_uses = 0 OR used_count < max_uses)
            ORDER BY end_date ASC, created_at DESC";
    
    $coupons = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $coupons = [];
    // You might want to log this error
    error_log("Coupon Discovery Page Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Săn Mã Giảm Giá • CHAUTFIFTH</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
    body { font-family: 'Inter', sans-serif; background-color: #f7fafc; }
    .font-playfair { font-family: 'Playfair Display', serif; }
    .coupon-card {
        background: linear-gradient(135deg, #fff 25%, #fde68a);
        border: 1px solid #fcd34d;
        box-shadow: 0 10px 20px -5px rgba(252, 211, 77, 0.1);
        transition: all 0.3s ease;
    }
    .coupon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px -5px rgba(252, 211, 77, 0.2);
    }
</style>
</head>
<body>

<?php include_once "./component/header.php"; ?>

<main class="container mx-auto px-4 py-12">
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-playfair font-bold text-amber-900">Săn Voucher Mỗi Ngày</h1>
        <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">Khám phá các ưu đãi độc quyền từ CHAUTFIFTH và tiết kiệm cho đơn hàng tiếp theo của bạn!</p>
    </div>

    <?php if (empty($coupons)): ?>
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm">
            <span class="material-symbols-rounded text-6xl text-gray-300">sentiment_dissatisfied</span>
            <p class="mt-4 text-gray-500 text-xl">Hôm nay chưa có mã công khai nào.
                <br>Hãy quay lại vào ngày mai nhé!</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($coupons as $coupon): ?>
                <div class="coupon-card rounded-2xl flex flex-col">
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-amber-700">
                                <?php if ($coupon['end_date']): ?>
                                    HSD: <?= date('d/m/Y', strtotime($coupon['end_date'])) ?>
                                <?php else: ?>
                                    Vô thời hạn
                                <?php endif; ?>
                            </p>
                            <h3 class="mt-2 text-2xl font-bold text-gray-800">
                                <?php
                                    if ($coupon['type'] == 'percentage') {
                                        echo "Giảm " . rtrim(rtrim(number_format($coupon['value'], 2), '0'), '.') . '%';
                                    } else {
                                        echo "Giảm " . number_format($coupon['value']) . 'đ';
                                    }
                                ?>
                            </h3>
                            <p class="mt-2 text-sm text-gray-600"><?= htmlspecialchars($coupon['description']) ?></p>
                        </div>
                        <div class="mt-4 text-xs text-gray-500">
                            * Áp dụng cho đơn hàng từ <?= number_format($coupon['min_order_value']) ?>đ
                            <?php if ($coupon['type'] == 'percentage' && $coupon['max_discount_amount']): ?>
                               , giảm tối đa <?= number_format($coupon['max_discount_amount']) ?>đ
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="bg-black/5 p-4 rounded-b-2xl border-t border-dashed border-amber-400 flex items-center justify-between gap-4">
                        <span class="font-mono font-bold text-lg text-amber-800 tracking-wider"><?= htmlspecialchars($coupon['code']) ?></span>
                        <button class="copy-btn px-4 py-1.5 bg-amber-800 text-white rounded-full text-sm font-semibold hover:bg-amber-900 transition-transform transform active:scale-95"
                                data-code="<?= htmlspecialchars($coupon['code']) ?>">
                            Lưu mã
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</main>

<?php include_once "./component/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const copyButtons = document.querySelectorAll('.copy-btn');

    copyButtons.forEach(button => {
        button.addEventListener('click', function () {
            const codeToCopy = this.dataset.code;
            navigator.clipboard.writeText(codeToCopy).then(() => {
                const originalText = this.textContent;
                this.textContent = 'Đã lưu!';
                this.classList.add('bg-green-600');
                this.classList.remove('bg-amber-800');
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.classList.remove('bg-green-600');
                    this.classList.add('bg-amber-800');
                }, 2000);
            }).catch(err => {
                console.error('Không thể sao chép mã: ', err);
                alert('Lỗi khi sao chép mã.');
            });
        });
    });
});
</script>

</body>
</html>
