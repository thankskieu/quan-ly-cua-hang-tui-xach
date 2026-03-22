<?php
header('Content-Type: application/json');

// Simulate server latency
sleep(1);

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');

// Basic validation
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Email không đúng định dạng.']);
    exit;
}

// In a real app, save to DB or send email here

echo json_encode(['status' => 'success', 'message' => 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất.']);