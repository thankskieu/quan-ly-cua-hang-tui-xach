<?php
session_start();
require_once __DIR__ . "/server/models/cart.php";

$cartModel = new Cart();


$cartData = $cartModel->getCart();
$cartItems = $cartData['success'] ? $cartData['data'] : [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giỏ hàng • CHAUTFIFTH</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
<style>
    body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #111; }
    .font-playfair { font-family: 'Playfair Display', serif; }
</style>
</head>
<body>

<?php
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        // Wrap in a container to constrain width and add top margin
        echo <<<HTML
        <div class="container mx-auto px-4 pt-4"> 
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <div class="flex">
                    <div class="py-1"><span class="material-symbols-rounded mr-3">error</span></div>
                    <div>
                        <p class="font-bold">Không thể tiếp tục thanh toán</p>
                        <p class="text-sm">{$message}</p>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }
?>

<?php include_once "./component/header.php"; ?>

<main class="container mx-auto px-4 py-10 min-h-[60vh]">
    <h1 class="text-3xl font-playfair font-bold mb-8">Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
        <div class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
            <span class="material-symbols-rounded text-6xl text-gray-300 mb-4">shopping_cart_off</span>
            <p class="text-gray-500 text-lg mb-6">Giỏ hàng đang trống</p>
            <a href="page/san-pham.php" class="px-8 py-3 bg-black text-white rounded-full font-bold hover:bg-yellow-600 transition">Mua sắm ngay</a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="lg:w-2/3 space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 text-sm text-gray-500 font-medium">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded text-yellow-600 focus:ring-yellow-500 border-gray-300">
                        Chọn tất cả
                    </label>
                    <span><?= count($cartItems) ?> sản phẩm</span>
                </div>

                <?php foreach ($cartItems as $item): 
                    $price = (float)$item['cart_price'];
                    $qty = (int)$item['quantity'];
                    $total = $price * $qty;
                ?>
                <div class="flex gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm cart-item-row" data-id="<?= $item['cart_id'] ?>" data-price="<?= $price ?>">
                    <input type="checkbox" class="item-checkbox mt-2 w-4 h-4 rounded text-yellow-600 focus:ring-yellow-500 border-gray-300" 
                           data-id="<?= $item['cart_id'] ?>" data-qty="<?= $qty ?>" data-price="<?= $price ?>">
                    
                    <a href="product_detail.php?id=<?= $item['product_id'] ?>" class="w-24 h-24 bg-gray-50 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="./server/admin/<?= htmlspecialchars($item['image']) ?>" class="w-full h-full object-cover">
                    </a>
                    
                    <div class="flex-1 flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium text-gray-900 line-clamp-1 hover:text-yellow-600 transition">
                                    <a href="product_detail.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['product_name']) ?></a>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1"><?= number_format($price, 0, ',', '.') ?>₫</p>
                            </div>
                            <button class="text-gray-400 hover:text-red-500 transition remove-btn" data-id="<?= $item['cart_id'] ?>">
                                <span class="material-symbols-rounded text-xl">delete</span>
                            </button>
                        </div>
                        
                        <div class="flex justify-between items-end">
                            <div class="flex items-center border border-gray-200 rounded-lg h-8">
                                <button class="w-8 h-full flex items-center justify-center hover:bg-gray-100 text-gray-600 change-qty" data-delta="-1">−</button>
                                <span class="w-10 text-center text-sm font-medium qty-display"><?= $qty ?></span>
                                <button class="w-8 h-full flex items-center justify-center hover:bg-gray-100 text-gray-600 change-qty" data-delta="1">+</button>
                            </div>
                            <span class="font-bold text-gray-900 item-total"><?= number_format($total, 0, ',', '.') ?>₫</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="lg:w-1/3">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm sticky top-24">
                    <h3 class="font-playfair font-bold text-xl mb-6">Tóm tắt đơn hàng</h3>
                    
                    <div class="space-y-3 mb-6 pb-6 border-b border-gray-100 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Tạm tính</span>
                            <span id="subtotal">0₫</span>
                        </div>
                        <div class="flex justify-between text-green-600" id="discountRow" style="display:none;">
                            <span>Giảm giá</span>
                            <span id="discountVal">-0₫</span>
                        </div>
                            <div class="flex gap-2">
                                <input type="text" id="couponCode" placeholder="Mã giảm giá" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-yellow-500 outline-none uppercase" autocomplete="off">
                                <button id="applyCoupon" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-yellow-600 transition">Áp dụng</button>
                            </div>
                            <p id="couponMsg" class="text-xs mt-1 min-h-[16px]"></p>
                            <!-- Voucher Wallet Container -->
                            <div id="voucherWallet" class="hidden absolute bg-white border border-gray-200 rounded-lg shadow-xl mt-1 w-full max-w-sm max-h-60 overflow-y-auto z-10">
                                <!-- Vouchers will be injected here by JS -->
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-end mb-6">
                        <span class="font-bold text-gray-900">Tổng cộng</span>
                        <div class="text-right">
                            <span class="block text-2xl font-bold text-yellow-600" id="grandTotal">0₫</span>
                            <span class="text-xs text-gray-500">(Đã bao gồm VAT)</span>
                        </div>
                    </div>

                    <form action="checkout.php" method="POST" id="checkoutForm">
                        <input type="hidden" name="selected_items" id="selectedItemsInput">
                        <input type="hidden" name="coupon_code" id="appliedCouponInput">
                        <input type="hidden" name="discount_amount" id="discountAmountInput">
                        <button type="button" id="btnCheckout" class="w-full py-4 bg-black text-white rounded-xl font-bold text-lg hover:bg-yellow-600 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            Tiến hành thanh toán
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include_once "./component/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- ELEMENTS ---
    const subtotalEl = document.getElementById('subtotal');
    const grandTotalEl = document.getElementById('grandTotal');
    const btnCheckout = document.getElementById('btnCheckout');
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const checkoutForm = document.getElementById('checkoutForm');
    
    const couponCodeInput = document.getElementById('couponCode');
    const applyCouponBtn = document.getElementById('applyCoupon');
    const couponMsgEl = document.getElementById('couponMsg');
    const discountRowEl = document.getElementById('discountRow');
    const discountValEl = document.getElementById('discountVal');
    const voucherWalletEl = document.getElementById('voucherWallet');

    const selectedItemsInput = document.getElementById('selectedItemsInput');
    const appliedCouponInput = document.getElementById('appliedCouponInput');
    const discountAmountInput = document.getElementById('discountAmountInput');

    // --- STATE ---
    let state = {
        subtotal: 0,
        discountAmount: 0,
        couponCode: '',
        selectedItemIds: []
    };

    // --- HELPERS ---
    const formatMoney = (n) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(n);

    // --- RENDER ---
    function render() {
        const grandTotal = Math.max(0, state.subtotal - state.discountAmount);
        if (subtotalEl) subtotalEl.textContent = formatMoney(state.subtotal);
        if (grandTotalEl) grandTotalEl.textContent = formatMoney(grandTotal);

        if (state.discountAmount > 0) {
            if (discountRowEl) discountRowEl.style.display = 'flex';
            if (discountValEl) discountValEl.textContent = '-' + formatMoney(state.discountAmount);
        } else {
            if (discountRowEl) discountRowEl.style.display = 'none';
        }

        if (btnCheckout) {
            btnCheckout.disabled = state.selectedItemIds.length === 0;
            btnCheckout.textContent = state.selectedItemIds.length > 0 
                ? `Tiến hành thanh toán (${state.selectedItemIds.length})`
                : 'Vui lòng chọn sản phẩm';
        }
        
        if (selectedItemsInput) selectedItemsInput.value = state.selectedItemIds.join(',');
        if (appliedCouponInput) appliedCouponInput.value = state.couponCode;
        if (discountAmountInput) discountAmountInput.value = state.discountAmount;
    }

    // --- LOGIC ---
    function calculateSubtotal() {
        let newSubtotal = 0;
        const newItemIds = [];
        checkboxes.forEach(cb => {
            if (cb.checked) {
                newSubtotal += parseFloat(cb.dataset.price) * parseInt(cb.dataset.qty);
                newItemIds.push(cb.dataset.id);
            }
        });
        state.subtotal = newSubtotal;
        state.selectedItemIds = newItemIds;

        if (state.subtotal === 0) {
            state.discountAmount = 0;
            state.couponCode = '';
            if (couponCodeInput) couponCodeInput.value = '';
            if (couponMsgEl) couponMsgEl.textContent = '';
        }
        render();
    }

    async function applyCoupon() {
        const code = couponCodeInput.value.trim().toUpperCase();
        if (couponMsgEl) couponMsgEl.textContent = '';

        if (!code) {
            state.discountAmount = 0;
            state.couponCode = '';
            render();
            return;
        }

        if (state.subtotal === 0) {
            couponMsgEl.textContent = 'Vui lòng chọn sản phẩm trước.';
            couponMsgEl.className = 'text-xs mt-1 text-red-500';
            return;
        }

        applyCouponBtn.disabled = true;
        applyCouponBtn.textContent = 'Đang...';

        try {
            const response = await fetch('api_coupon.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ coupon_code: code, subtotal: state.subtotal })
            });
            const result = await response.json();

            if (result.success) {
                state.discountAmount = result.discountAmount;
                state.couponCode = result.couponCode;
                if(couponCodeInput) couponCodeInput.value = result.couponCode;
                couponMsgEl.textContent = result.message;
                couponMsgEl.className = 'text-xs mt-1 text-green-600';
            } else {
                state.discountAmount = 0;
                state.couponCode = '';
                couponMsgEl.textContent = result.message;
                couponMsgEl.className = 'text-xs mt-1 text-red-500';
            }
            render();
        } catch (error) {
            couponMsgEl.textContent = 'Lỗi kết nối máy chủ.';
            couponMsgEl.className = 'text-xs mt-1 text-red-500';
        } finally {
            applyCouponBtn.disabled = false;
            applyCouponBtn.textContent = 'Áp dụng';
        }
    }

    // --- VOUCHER WALLET LOGIC ---
    let walletVisible = false;
    function renderVouchers(vouchers = []) {
        if (!voucherWalletEl) return;
        if (vouchers.length === 0) {
            voucherWalletEl.innerHTML = `<div class="p-3 text-sm text-center text-gray-500">Bạn không có voucher nào phù hợp.</div>`;
        } else {
            voucherWalletEl.innerHTML = vouchers.map(v => {
                let valueText = v.type === 'percentage' 
                    ? `Giảm ${parseFloat(v.value)}%`
                    : `Giảm ${formatMoney(v.value)}`;

                return `
                    <div class="voucher-item p-3 border-b border-gray-100 hover:bg-yellow-50 cursor-pointer" data-code="${v.code}">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-sm text-gray-800">${v.code}</span>
                            <span class="text-xs font-semibold text-green-600">${valueText}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">${v.description}</p>
                    </div>
                `;
            }).join('');
        }
    }

    async function fetchUserVouchers() {
        if (!voucherWalletEl) return;
        if (state.subtotal <= 0) {
             voucherWalletEl.innerHTML = `<div class="p-3 text-sm text-center text-gray-500">Vui lòng chọn sản phẩm để xem voucher.</div>`;
             voucherWalletEl.classList.remove('hidden');
             walletVisible = true;
             return;
        }

        voucherWalletEl.classList.remove('hidden');
        walletVisible = true;
        voucherWalletEl.innerHTML = `<div class="p-3 text-sm text-center text-gray-500">Đang tải voucher...</div>`;

        try {
            const response = await fetch(`api_coupon.php?subtotal=${state.subtotal}`);
            const result = await response.json();
            if (result.success) {
                renderVouchers(result.vouchers);
            } else {
                voucherWalletEl.innerHTML = `<div class="p-3 text-sm text-center text-red-500">${result.message}</div>`;
            }
        } catch (e) {
            voucherWalletEl.innerHTML = `<div class="p-3 text-sm text-center text-red-500">Lỗi khi tải voucher.</div>`;
        }
    }

    // --- EVENT LISTENERS ---
    if (couponCodeInput) {
        couponCodeInput.addEventListener('focus', fetchUserVouchers);
    }

    document.addEventListener('click', (e) => {
        if (!voucherWalletEl || !couponCodeInput) return;
        // Hide wallet if click is outside the input and the wallet itself
        if (!couponCodeInput.contains(e.target) && !voucherWalletEl.contains(e.target)) {
            voucherWalletEl.classList.add('hidden');
            walletVisible = false;
        }
        // Handle click on a voucher item
        const clickedItem = e.target.closest('.voucher-item');
        if (clickedItem) {
            couponCodeInput.value = clickedItem.dataset.code;
            voucherWalletEl.classList.add('hidden');
            walletVisible = false;
            applyCoupon();
        }
    });

    checkboxes.forEach(cb => cb.addEventListener('change', calculateSubtotal));
    if (selectAll) {
        selectAll.addEventListener('change', (e) => {
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            calculateSubtotal();
        });
    }

    if (applyCouponBtn) applyCouponBtn.addEventListener('click', applyCoupon);
    if (btnCheckout) {
        btnCheckout.addEventListener('click', () => {
            if (!btnCheckout.disabled) checkoutForm.submit();
        });
    }

    // --- Existing Functionality ---
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;
            const container = this.closest('.cart-item-row');
            const id = this.dataset.id;
            fetch('cart_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=remove&id=` + id
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    if (container) container.remove(); // Remove the item's HTML from the DOM
                    calculateSubtotal(); // Recalculate and render overall totals
                    
                    // If cart becomes empty, show the "Giỏ hàng đang trống" message
                    if (document.querySelectorAll('.cart-item-row').length === 0) {
                        window.location.reload(); // Reload to show PHP empty state
                    }
                } else {
                    alert(res.message || 'Lỗi khi xóa sản phẩm.');
                }
            }).catch(err => alert('Lỗi kết nối khi xóa sản phẩm.'));
        });
    });

    document.querySelectorAll('.change-qty').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('.cart-item-row'); // The div containing the item
            const id = container.dataset.id; // cart_id
            const checkbox = container.querySelector('.item-checkbox');
            const qtyDisplay = container.querySelector('.qty-display'); // Element to show current qty
            const itemTotalEl = container.querySelector('.item-total'); // Element to show item total
            const price = parseFloat(checkbox.dataset.price); // Price of single item
            
            const delta = parseInt(this.dataset.delta);
            let currentQty = parseInt(qtyDisplay.textContent); // Get current displayed quantity
            let newQty = currentQty + delta;

            if (newQty < 1) newQty = 1; // Prevent quantity from going below 1

            fetch('cart_action.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `action=update_qty&id=${id}&quantity=${newQty}`
            }).then(r => r.json()).then(res => {
                if (res.success) {
                    // Update UI dynamically
                    qtyDisplay.textContent = newQty; // Update displayed quantity
                    checkbox.dataset.qty = newQty; // Update data-qty on checkbox for subtotal calculation
                    itemTotalEl.textContent = formatMoney(newQty * price); // Update item total
                    calculateSubtotal(); // Recalculate and render overall totals
                } else {
                    alert(res.message || 'Lỗi khi cập nhật số lượng.');
                }
            }).catch(err => alert('Lỗi kết nối khi cập nhật số lượng.')); // Catch network errors
        });
    });

    // --- INITIALIZATION ---
    calculateSubtotal();
});
</script>
</body>
</html>