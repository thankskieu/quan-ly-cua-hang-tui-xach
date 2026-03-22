<?php

// Lấy giá trị đã lưu, nếu không có thì về index.php
$backpage = isset($_SESSION['backpage']) ? $_SESSION['backpage'] : 'index.php';

// Tránh trường hợp quay lại chính trang này -> thành fallback
if ($backpage === $_SERVER['REQUEST_URI']) {
    $backpage = 'index.php';
}
?>

<a href="<?php echo htmlspecialchars($backpage, ENT_QUOTES, 'UTF-8'); ?>"
   class="inline-flex items-center gap-2 px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-400 transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0L3.586 10a1 1 0 010-1.414l4.707-4.707a1 1 0 011.414 1.414L6.414 9H16a1 1 0 110 2H6.414l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd" />
    </svg>
    Quay lại
</a>