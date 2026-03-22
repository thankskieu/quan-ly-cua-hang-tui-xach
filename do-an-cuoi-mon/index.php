<?php
session_start();
$_SESSION['backpage'] = $_SERVER['REQUEST_URI'];

// Tạm thời gọi hàm cập nhật giá sản phẩm ngẫu nhiên theo danh mục
require_once __DIR__ . '/server/models/products.php';
$products_obj = new Products();
$update_success = $products_obj->updateCategoryPricesRandomly();

if ($update_success) {
    echo "<script>console.log('Cập nhật giá sản phẩm ngẫu nhiên theo danh mục thành công!');</script>";
} else {
    echo "<script>console.error('Không thể cập nhật giá sản phẩm ngẫu nhiên theo danh mục.');</script>";
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CHAUTFIFTH</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Font Material Icons -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,400,0,0" />

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
</head>

<body class="bg-[#fefcf8]">

  <!-- HEADER -->
  <?php include_once "./component/header.php" ?>

  <!-- NỘI DUNG TRANG CHỦ -->
  <?php include_once "./page/trang-chu.php" ?>

  <!-- FOOTER -->
  <?php include_once "./component/footer.php" ?>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
  <script>
    const swiper = new Swiper(".mySwiper", {
      loop: true,
      autoplay: { delay: 4000, disableOnInteraction: false },
      pagination: { el: ".swiper-pagination", clickable: true },
      navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
    });
  </script>
</body>

</html>