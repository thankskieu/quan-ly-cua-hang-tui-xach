<?php
include_once './phan-quyen.php';
require_once "../models/coupon.php";

$couponObj = new Coupon();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect with an error message
    header("Location: dashboard.php?tab=ma-giam-gia&error=1");
    exit;
}

$id = intval($_GET['id']);

if ($couponObj->delete($id)) {
    // Redirect with a success message
    header("Location: dashboard.php?tab=ma-giam-gia&success=3");
    exit;
} else {
    // Redirect with an error message
    header("Location: dashboard.php?tab=ma-giam-gia&error=2");
    exit;
}
?>
