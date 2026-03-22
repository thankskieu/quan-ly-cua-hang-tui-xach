 <?php
session_start();
require_once __DIR__ . "/server/models/products.php";
require_once __DIR__ . "/server/models/cart.php";

$productObj = new Products();
$cartObj = new Cart();

// Lấy ID sản phẩm từ URL
$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header("Location: index.php");
    exit;
}

// Lấy chi tiết sản phẩm
$product = $productObj->getById($product_id);
if (!$product) {
    echo "<h2>Sản phẩm không tồn tại!</h2>";
    exit;
}

// Xử lý thêm giỏ hàng
$popupMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    $quantity = max(1, intval($_POST['quantity']));
    $res = $cartObj->addToCart($product_id, $quantity, $product['price']);
    $popupMessage = $res['message'];
}

// Lấy sản phẩm liên quan
$relatedProducts = $productObj->getProductsByCategory($product['category'], 10);
$relatedProducts = array_filter($relatedProducts, fn($p) => $p['id'] != $product_id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($product['name_product']) ?></title>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f5f5f5; }
a { text-decoration:none; color:#333; }
.container { width:90%; max-width:1200px; margin:auto; padding:20px; }
.product-detail { display:flex; gap:50px; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,0.1); margin-top:20px; }
.product-detail img { width:400px; border-radius:10px; }
.product-info { max-width:600px; }
.product-info h1 { font-size:28px; margin-bottom:10px; }
.product-info p { font-size:16px; line-height:1.5; }
.price { font-size:22px; color:#e60023; margin:10px 0; }
.discount { font-size:18px; color:#ff6f61; }
.quantity { display:flex; align-items:center; gap:10px; margin:15px 0; }
.quantity button { width:30px; height:30px; font-size:18px; cursor:pointer; }
.quantity input { width:50px; text-align:center; padding:5px; }
.add-cart { background:#e60023; color:#fff; border:none; padding:10px 20px; cursor:pointer; border-radius:5px; }
.related-products { margin-top:40px; }
.related-products h2 { margin-bottom:20px; }
.slider { display:flex; overflow-x:auto; gap:20px; padding-bottom:10px; }
.slider::-webkit-scrollbar { height:8px; }
.slider::-webkit-scrollbar-thumb { background:#ccc; border-radius:4px; }
.related-item { min-width:180px; background:#fff; padding:10px; border-radius:10px; text-align:center; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
.related-item img { width:100%; border-radius:8px; }
.related-item p { margin:5px 0; }
.popup { position:fixed; bottom:20px; right:20px; background:#4CAF50; color:#fff; padding:15px 25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.2); display:none; animation:fadein 0.5s; }
@keyframes fadein { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }
</style>
</head>
<body>

<div class="container">
    <a href="index.php">← Quay về trang chủ</a>
    <div class="product-detail">
        <div class="product-image">
            <img src="/server/admin/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name_product']) ?>">
        </div>
        <div class="product-info">
            <h1><?= htmlspecialchars($product['name_product']) ?></h1>
            <p class="price"><?= number_format($product['price'],0,",",".") ?> ₫</p>
            <?php if($product['discount']>0): ?>
                <p class="discount">Giảm giá: <?= $product['discount'] ?>%</p>
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <form method="POST" onsubmit="return showPopup();">
                <div class="quantity">
                    <button type="button" onclick="changeQty(-1)">-</button>
                    <input type="number" name="quantity" id="quantity" value="1" min="1">
                    <button type="button" onclick="changeQty(1)">+</button>
                </div>
                <button type="submit" class="add-cart">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>

    <?php if(!empty($relatedProducts)): ?>
    <div class="related-products">
        <h2>Sản phẩm liên quan</h2>
        <div class="slider">
            <?php foreach($relatedProducts as $rp): ?>
            <div class="related-item">
                <a href="product_detail.php?id=<?= $rp['id'] ?>">
                    <img src="/server/admin/uploads/<?= htmlspecialchars($rp['image']) ?>" alt="<?= htmlspecialchars($rp['name_product']) ?>">
                    <p><?= htmlspecialchars($rp['name_product']) ?></p>
                    <p><?= number_format($rp['price'],0,",",".") ?> ₫</p>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<div id="popup" class="popup"><?= $popupMessage ?></div>

<script>
function changeQty(val){
    const qtyInput = document.getElementById('quantity');
    let current = parseInt(qtyInput.value);
    current += val;
    if(current < 1) current = 1;
    qtyInput.value = current;
}

function showPopup(){
    const popup = document.getElementById('popup');
    if(popup.innerText.trim() === '') return true; // không hiển thị popup nếu chưa submit
    popup.style.display = 'block';
    setTimeout(()=>popup.style.display='none',2000);
    return true;
}
</script>

</body>
</html>
