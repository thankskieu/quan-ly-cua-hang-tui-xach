<?php
// Bắt đầu session + lấy info user + giỏ hàng
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/server/models/user.php";
require_once __DIR__ . "/server/models/cart.php";

$userObj = new User();
$account = $userObj->getAccount();
$loggedIn = $account['success'];
$user = $loggedIn ? $account['user'] : null;

// Lấy số lượng giỏ hàng
$cartCount = 0;
if ($loggedIn) {
    $cartObj = new Cart();
    $cartData = $cartObj->getTotalItems();
    $cartCount = $cartData['success'] ? $cartData['total_items'] : 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Liên hệ • CHAUTFIFTH</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3 { font-family: 'Playfair Display', serif; }
    .lux-input {
        transition: all 0.3s ease;
    }
    .lux-input:focus {
        border-color: #D4AF37;
        box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
    }
    /* Custom Scrollbar for FAQ */
    details > summary {
        list-style: none;
    }
    details > summary::-webkit-details-marker {
        display: none;
    }
    .faq-content {
        transition: max-height 0.3s ease-in-out;
    }
</style>
</head>
<body class="bg-[#fefcf8] text-gray-800 flex flex-col min-h-screen">

<!-- HEADER -->
<?php include_once "./component/header.php" ?>

<!-- MAIN CONTENT -->
<main class="flex-grow">
    <!-- Banner Title -->
    <div class="bg-[#1a1a1a] py-16 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
        <h1 class="text-4xl md:text-5xl font-bold text-[#D4AF37] relative z-10">Liên Hệ Với Chúng Tôi</h1>
        <p class="text-gray-400 mt-3 text-lg relative z-10">Chúng tôi luôn sẵn sàng lắng nghe bạn</p>
    </div>

    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            
            <!-- LEFT COLUMN: INFO & FAQ -->
            <div class="space-y-10">
                
                <!-- Contact Info Block -->
                <div>
                    <h2 class="text-3xl font-bold mb-6 text-gray-900 border-l-4 border-[#D4AF37] pl-4">Thông Tin Liên Hệ</h2>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Hãy liên hệ với Chautfifth bất cứ khi nào bạn cần hỗ trợ. Đội ngũ chăm sóc khách hàng của chúng tôi luôn túc trực để giải đáp mọi thắc mắc của bạn.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 rounded-full bg-yellow-50 text-[#D4AF37] flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-location-dot text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Địa chỉ cửa hàng</h3>
                                <p class="text-sm text-gray-600 mt-1">123 Đường Thời Trang, Quận 1, TP. Hồ Chí Minh</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 rounded-full bg-yellow-50 text-[#D4AF37] flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-phone text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Hotline hỗ trợ</h3>
                                <p class="text-sm text-gray-600 mt-1">0909 123 456 (8:00 - 22:00)</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-12 h-12 rounded-full bg-yellow-50 text-[#D4AF37] flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-envelope text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Email</h3>
                                <p class="text-sm text-gray-600 mt-1">support@chautfifth.vn (Phản hồi trong 24h)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Kết nối nhanh</h3>
                    <div class="flex gap-4">
                        <a href="tel:0909123456" class="flex-1 py-3 px-4 bg-black text-white rounded-lg text-center hover:bg-[#D4AF37] transition-colors font-medium flex items-center justify-center gap-2 group">
                            <i class="fa-solid fa-phone group-hover:rotate-12 transition-transform"></i> Gọi ngay
                        </a>
                        <a href="https://zalo.me/0909123456" target="_blank" class="flex-1 py-3 px-4 bg-blue-600 text-white rounded-lg text-center hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                            <i class="fa-solid fa-comment-dots"></i> Zalo
                        </a>
                        <a href="#" class="flex-1 py-3 px-4 bg-[#0084FF] text-white rounded-lg text-center hover:bg-[#0078e0] transition-colors font-medium flex items-center justify-center gap-2">
                            <i class="fa-brands fa-facebook-messenger"></i> Messenger
                        </a>
                    </div>
                </div>

                <!-- FAQ Accordion -->
                <div>
                    <h3 class="text-xl font-bold mb-4">Câu hỏi thường gặp</h3>
                    <div class="space-y-3">
                        <details class="group bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <summary class="flex justify-between items-center p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                <span class="font-medium text-gray-800">Thời gian giao hàng là bao lâu?</span>
                                <span class="transition-transform group-open:rotate-180 text-gray-400">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                                Nội thành TP.HCM: 1-2 ngày. Các tỉnh thành khác: 3-5 ngày làm việc tùy thuộc vào đơn vị vận chuyển.
                            </div>
                        </details>
                        <details class="group bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <summary class="flex justify-between items-center p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                <span class="font-medium text-gray-800">Chính sách đổi trả như thế nào?</span>
                                <span class="transition-transform group-open:rotate-180 text-gray-400">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                                Chúng tôi hỗ trợ đổi trả trong vòng 7 ngày nếu sản phẩm có lỗi từ nhà sản xuất hoặc không đúng kích cỡ.
                            </div>
                        </details>
                        <details class="group bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <summary class="flex justify-between items-center p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                <span class="font-medium text-gray-800">Tôi có thể kiểm tra hàng trước khi nhận không?</span>
                                <span class="transition-transform group-open:rotate-180 text-gray-400">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </summary>
                            <div class="px-4 pb-4 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3">
                                Có. CHAUTFIFTH khuyến khích khách hàng kiểm tra sản phẩm trước khi thanh toán cho nhân viên giao hàng.
                            </div>
                        </details>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: FORM -->
            <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl border border-gray-100 h-fit sticky top-24">
                <h2 class="text-2xl font-bold mb-2">Gửi Tin Nhắn</h2>
                <p class="text-sm text-gray-500 mb-6">Điền thông tin bên dưới, chúng tôi sẽ liên hệ lại sớm nhất.</p>
                
                <form id="contactForm" class="space-y-5" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-sm font-semibold text-gray-700">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 lux-input focus:bg-white outline-none text-sm" placeholder="Nhập tên của bạn">
                            <p class="text-red-500 text-xs hidden" id="err-name">Vui lòng nhập họ tên</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-semibold text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 lux-input focus:bg-white outline-none text-sm" placeholder="example@email.com">
                            <p class="text-red-500 text-xs hidden" id="err-email">Email không hợp lệ</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 lux-input focus:bg-white outline-none text-sm" placeholder="Nhập số điện thoại">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-semibold text-gray-700">Nội dung <span class="text-red-500">*</span></label>
                        <textarea id="message" name="message" rows="5" maxlength="500" class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 lux-input focus:bg-white outline-none text-sm resize-none" placeholder="Bạn cần hỗ trợ gì?"></textarea>
                        <div class="flex justify-between mt-1">
                             <p class="text-red-500 text-xs hidden" id="err-message">Vui lòng nhập nội dung</p>
                             <span class="text-xs text-gray-400" id="charCount">0/500</span>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full bg-black text-white py-4 rounded-lg font-bold hover:bg-[#D4AF37] transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2 group disabled:opacity-70 disabled:cursor-not-allowed">
                        <span>Gửi Liên Hệ</span>
                        <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                        <!-- Loader (Hidden by default) -->
                        <svg class="animate-spin h-5 w-5 text-white hidden" id="btnLoader" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAP SECTION -->
    <div class="mt-12 relative w-full h-[450px] bg-gray-200 group">
        <!-- Overlay Button -->
        <a href="https://maps.app.goo.gl/wYy1z2z2z2z2z2z2" target="_blank" class="absolute top-4 left-4 z-10 bg-white px-5 py-3 rounded-full shadow-lg font-bold text-sm text-gray-800 hover:text-[#D4AF37] hover:shadow-xl transition-all flex items-center gap-2">
            <i class="fa-solid fa-diamond-turn-right text-[#D4AF37]"></i> Chỉ đường đến CHAUTFIFTH
        </a>
        
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4946681007846!2d106.70175551480073!3d10.773374292323565!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f442e47e539%3A0x63351e39665c5896!2sBitexco%20Financial%20Tower!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" 
            width="100%" 
            height="100%" 
            style="border:0; filter: grayscale(100%) invert(90%) contrast(90%); transition: filter 0.5s;" 
            allowfullscreen="" 
            loading="lazy"
            class="group-hover:filter-none">
        </iframe>
    </div>
</main>

<!-- TOAST NOTIFICATION -->
<div id="toast" class="fixed bottom-5 right-5 z-[2000] transform translate-y-20 opacity-0 transition-all duration-300 bg-white border-l-4 rounded shadow-2xl p-4 flex items-center gap-3 min-w-[300px]">
    <div id="toastIcon" class="text-2xl"></div>
    <div>
        <h4 id="toastTitle" class="font-bold text-sm"></h4>
        <p id="toastMessage" class="text-xs text-gray-500"></p>
    </div>
    <button onclick="hideToast()" class="ml-auto text-gray-400 hover:text-gray-600">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<!-- FOOTER -->
<?php include_once "./component/footer.php" ?>

<!-- SCRIPTS -->
<script>
    // --- FORM VALIDATION & SUBMIT ---
    const form = document.getElementById('contactForm');
    const msgInput = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    // Char count
    msgInput.addEventListener('input', function() {
        const len = this.value.length;
        charCount.textContent = `${len}/500`;
        if(len >= 500) charCount.classList.add('text-red-500');
        else charCount.classList.remove('text-red-500');
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset errors
        document.querySelectorAll('p[id^="err-"]').forEach(el => el.classList.add('hidden'));
        let hasError = false;

        // Validate Name
        const name = document.getElementById('name').value.trim();
        if(!name) {
            document.getElementById('err-name').classList.remove('hidden');
            hasError = true;
        }

        // Validate Email
        const email = document.getElementById('email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!email || !emailRegex.test(email)) {
            document.getElementById('err-email').classList.remove('hidden');
            hasError = true;
        }

        // Validate Message
        const message = document.getElementById('message').value.trim();
        if(!message) {
            document.getElementById('err-message').classList.remove('hidden');
            hasError = true;
        }

        if(hasError) return;

        // --- SUBMIT VIA FETCH ---
        const btn = document.getElementById('submitBtn');
        const loader = document.getElementById('btnLoader');
        const btnText = btn.querySelector('span');
        const btnIcon = btn.querySelector('i');

        // Loading State
        btn.disabled = true;
        loader.classList.remove('hidden');
        btnText.textContent = 'Đang gửi...';
        btnIcon.classList.add('hidden');

        fetch('api_contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                name: name,
                email: email,
                phone: document.getElementById('phone').value,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                showToast('success', 'Thành công!', data.message);
                form.reset();
                charCount.textContent = "0/500";
            } else {
                showToast('error', 'Lỗi!', data.message);
            }
        })
        .catch(err => {
            showToast('error', 'Lỗi kết nối', 'Không thể gửi tin nhắn lúc này.');
        })
        .finally(() => {
            // Restore Button
            btn.disabled = false;
            loader.classList.add('hidden');
            btnText.textContent = 'Gửi Liên Hệ';
            btnIcon.classList.remove('hidden');
        });
    });

    // --- TOAST LOGIC ---
    const toast = document.getElementById('toast');
    let toastTimeout;

    function showToast(type, title, message) {
        const iconDiv = document.getElementById('toastIcon');
        const titleEl = document.getElementById('toastTitle');
        const msgEl = document.getElementById('toastMessage');

        // Style based on type
        if(type === 'success') {
            toast.className = "fixed bottom-5 right-5 z-[2000] transform translate-y-0 opacity-100 transition-all duration-300 bg-white border-l-4 border-green-500 rounded shadow-2xl p-4 flex items-center gap-3 min-w-[300px]";
            iconDiv.innerHTML = '<i class="fa-solid fa-circle-check text-green-500"></i>';
            titleEl.className = "font-bold text-sm text-green-700";
        } else {
            toast.className = "fixed bottom-5 right-5 z-[2000] transform translate-y-0 opacity-100 transition-all duration-300 bg-white border-l-4 border-red-500 rounded shadow-2xl p-4 flex items-center gap-3 min-w-[300px]";
            iconDiv.innerHTML = '<i class="fa-solid fa-circle-exclamation text-red-500"></i>';
            titleEl.className = "font-bold text-sm text-red-700";
        }

        titleEl.textContent = title;
        msgEl.textContent = message;

        // Auto hide
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(hideToast, 5000);
    }

    function hideToast() {
        toast.classList.add('translate-y-20', 'opacity-0');
    }
</script>

</body>
</html>