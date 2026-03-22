<?php
session_start();

// Redirect to home if accessed directly without a processed order
if (!isset($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit;
}

$orderId = $_SESSION['last_order_id'];
$qrInfo  = $_SESSION['qr_info'] ?? null;

// Build VietQR image link
$qrImg = null;

if (is_array($qrInfo)) {
    $bankBin   = (string)($qrInfo['bank_bin'] ?? '');
    $accountNo = (string)($qrInfo['account_no'] ?? '');
    $amount    = (int)($qrInfo['amount'] ?? 0);
    $desc      = (string)($qrInfo['description'] ?? '');

    if ($bankBin !== '' && $accountNo !== '' && $amount > 0) {
        $qrImg = "https://img.vietqr.io/image/{$bankBin}-{$accountNo}-qr_only.png"
              . "?amount=" . urlencode((string)$amount)
              . "&addInfo=" . urlencode($desc);
    }
}

// Unset session variables to prevent re-display (sau khi đã lấy xong)
unset($_SESSION['last_order_id']);
unset($_SESSION['qr_info']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng • CHAUTFIFTH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.05), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            border: 1px solid #e5e7eb;
        }
        .copy-button:active { transform: scale(0.95); background-color: #fde68a; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

<main class="card w-full max-w-md text-center p-6 sm:p-8">

<?php if ($qrImg): ?>
    <!-- VIETQR PAYMENT -->
    <div class="mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center bg-blue-100 text-blue-600">
        <span class="material-symbols-rounded" style="font-size: 32px;">qr_code_scanner</span>
    </div>

    <h1 class="text-xl sm:text-2xl font-bold font-playfair text-gray-900">Quét mã để thanh toán</h1>
    <p class="text-sm text-gray-600 mt-2">
        Đơn hàng <span class="font-bold text-gray-800">#<?= htmlspecialchars((string)$orderId) ?></span> đã được tạo.
    </p>
    <p class="text-sm text-gray-500 mt-1">Vui lòng quét mã QR dưới đây bằng ứng dụng ngân hàng của bạn.</p>

    <div class="my-6">
        <img src="<?= htmlspecialchars($qrImg) ?>"
             alt="Mã QR thanh toán"
             class="mx-auto rounded-lg border-4 border-white shadow-lg"
             width="280" height="280">
    </div>

    <div class="space-y-3 text-left bg-gray-50 p-4 rounded-lg border border-dashed">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">Số tiền</span>
            <strong class="text-lg text-yellow-600">
                <?= number_format((int)($qrInfo['amount'] ?? 0), 0, ',', '.') ?>₫
            </strong>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">Nội dung</span>
            <div class="flex items-center gap-2">
                <strong id="desc" class="text-gray-800"><?= htmlspecialchars((string)($qrInfo['description'] ?? '')) ?></strong>
                <button type="button" onclick="copyToClipboard('#desc')" class="copy-button p-1 rounded-md hover:bg-gray-200 transition">
                    <span class="material-symbols-rounded text-sm">content_copy</span>
                </button>
            </div>
        </div>

        <div class="pt-2 border-t border-gray-200">
            <p class="text-xs text-center text-gray-500 mt-2">
                <strong>Ngân hàng:</strong> <?= htmlspecialchars((string)($qrInfo['bank_name'] ?? 'MB Bank')) ?><br>
                <strong>Chủ tài khoản:</strong> <?= htmlspecialchars((string)($qrInfo['account_name'] ?? '')) ?><br>
                <strong>Số tài khoản:</strong> <?= htmlspecialchars((string)($qrInfo['account_no'] ?? '')) ?>
            </p>
        </div>
    </div>

    <p class="text-xs text-red-600 mt-4 font-semibold">
        Lưu ý: Đơn hàng sẽ chỉ được xử lý sau khi chúng tôi xác nhận đã nhận được thanh toán.
    </p>

<?php else: ?>
    <!-- COD PAYMENT -->
    <div class="mx-auto mb-4 w-16 h-16 rounded-full flex items-center justify-center bg-green-100 text-green-600">
        <span class="material-symbols-rounded" style="font-size: 32px;">check_circle</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-bold font-playfair text-gray-900">Đặt hàng thành công!</h1>
    <p class="text-sm text-gray-600 mt-2">
        Mã đơn hàng của bạn là: <strong class="text-gray-800">#<?= htmlspecialchars((string)$orderId) ?></strong>
    </p>
    <p class="text-sm text-gray-500 mt-1">Cảm ơn bạn đã mua sắm. Chúng tôi sẽ liên hệ để xác nhận đơn hàng sớm nhất.</p>
<?php endif; ?>

    <div class="mt-8 flex flex-col sm:flex-row gap-3">
        <a href="index.php" class="w-full py-2.5 px-4 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition text-sm">🏠 Về trang chủ</a>
        <a href="orders.php" class="w-full py-2.5 px-4 bg-gray-900 text-white rounded-lg font-semibold hover:bg-yellow-600 transition text-sm">📄 Xem đơn hàng</a>
    </div>
</main>

<div id="toast" class="fixed top-5 right-5 bg-green-600 text-white py-2 px-5 rounded-lg shadow-lg opacity-0 translate-y-2 transition-all duration-300">
    Đã sao chép!
</div>

<script>
function copyToClipboard(elementId) {
    const el = document.querySelector(elementId);
    if (!el) return;

    const textToCopy = el.innerText || '';
    navigator.clipboard.writeText(textToCopy).then(() => {
        const toast = document.getElementById('toast');
        toast.classList.remove('opacity-0', 'translate-y-2');
        setTimeout(() => toast.classList.add('opacity-0', 'translate-y-2'), 2000);
    }).catch(err => console.error('Failed to copy text: ', err));
}
</script>

</body>
</html>
