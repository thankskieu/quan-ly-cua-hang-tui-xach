<?php
session_start();

require_once __DIR__ . '/../models/bill.php';
require_once __DIR__ . '/../models/products.php'; // Assuming products model might be needed for item details
require_once __DIR__ . '/../models/admin.php'; // Include admin model for authentication

$adminModel = new Admin();
$admin_status = $adminModel->getAdmin();

// Check if admin is logged in (using the Admin model)
if (!$admin_status['success']) {
    header('Location: auth.login.php'); // Redirect to admin login if not authenticated
    exit();
}

$billObj = new Bill();
$productsObj = new Products();

$billObj = new Bill();
$productsObj = new Products();

$bill_id = isset($_GET['bill_id']) ? (int)$_GET['bill_id'] : 0;

if ($bill_id === 0) {
    echo "Lỗi: Không tìm thấy ID hóa đơn.";
    exit();
}

$bill = $billObj->getOrderById($bill_id);
$order_items = $billObj->getItemsByBillId($bill_id);

if (!$bill) {
    echo "Lỗi: Không tìm thấy thông tin hóa đơn.";
    exit();
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn #<?= $bill['id'] ?></title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 20px; font-size: 14px; }
        .invoice-container { width: 800px; margin: auto; border: 1px solid #eee; padding: 20px; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .company-info, .customer-info, .invoice-details { margin-bottom: 20px; overflow: hidden; }
        .company-info div, .customer-info div, .invoice-details div { width: 48%; float: left; }
        .company-info div:last-child, .customer-info div:last-child, .invoice-details div:last-child { float: right; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row td { font-weight: bold; }
        .text-right { text-align: right; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>HÓA ĐƠN BÁN HÀNG</h1>
            <p>Ngày: <?= date('d/m/Y H:i:s', strtotime($bill['created_at'])) ?></p>
        </div>

        <div class="company-info clearfix">
            <div>
                <strong>Tên Công Ty:</strong> Tên cửa hàng của bạn<br>
                <strong>Địa chỉ:</strong> Địa chỉ cửa hàng<br>
                <strong>Điện thoại:</strong> 0123 456 789<br>
                <strong>Email:</strong> info@yourstore.com
            </div>
            <div>
                <strong>Mã hóa đơn:</strong> #<?= $bill['bill_id'] ?><br>
                <strong>Mã đơn hàng:</strong> <?= $bill['bill_id'] ?><br>
                <strong>Tình trạng:</strong> <?= $bill['status'] ?>
            </div>
        </div>

        <hr>

        <div class="customer-info clearfix">
            <div>
                <strong>Khách hàng:</strong> <?= htmlspecialchars($bill['username']) ?><br>
                <strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($bill['address']) ?><br>
                <strong>Điện thoại:</strong> <?= htmlspecialchars($bill['phone']) ?>
            </div>
        </div>

        <h2>Chi tiết đơn hàng</h2>
        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php $stt = 1; ?>
                <?php foreach ($order_items as $item) : ?>
                    <tr>
                        <td><?= $stt++ ?></td>
                        <td><?= htmlspecialchars($item['name_product']) ?></td>
                        <td class="text-right"><?= $item['quantity'] ?></td>
                        <td class="text-right"><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                        <td class="text-right"><?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?>₫</td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4" class="text-right">Tổng cộng:</td>
                    <td class="text-right"><?= number_format($bill['total_price'], 0, ',', '.') ?>₫</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Cảm ơn quý khách đã mua hàng!</p>
            <p>Vui lòng liên hệ nếu có bất kỳ thắc mắc nào.</p>
        </div>
    </div>
</body>
</html>
