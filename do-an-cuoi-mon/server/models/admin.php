<?php
require_once __DIR__ . "/../database/database.php";

class Admin {
    private $conn;
    private $secretKey;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
        $this->secretKey = "this_is_a_very_secret_key_that_no_one_should_know";

        // ✅ Tạo admin mặc định nếu chưa có
        $defaultEmail = "admin@example.com";
        $defaultPassword = "admin123"; // mật khẩu mặc định
        $this->createDefaultAdmin($defaultEmail, $defaultPassword);
    }

    private function createDefaultAdmin($email, $password) {
        $stmt = $this->conn->prepare("SELECT id FROM administrator WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO administrator (email, password) VALUES (:email, :password)");
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed);
            $stmt->execute();
        }
    }

    // Hàm đăng nhập
    public function login($email, $password) {
        try {
            // Debug: Log input
            error_log("Login attempt for email: " . $email);

            $sql = "SELECT id, password FROM administrator WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debug: Check if user found
            if (!$user) {
                error_log("User not found for email: " . $email);
                return [
                    "success" => false,
                    "message" => "❌ Email hoặc mật khẩu không tồn tại!"
                ];
            }

            error_log("User found. ID: " . $user['id']);
            // Debug: Password check
            $verification = password_verify($password, $user["password"]);
            error_log("Password verification result: " . ($verification ? "TRUE" : "FALSE"));
            
            // Fallback check for plain text just in case (though DB shows hash)
            if (!$verification && $password === $user['password']) {
                 error_log("Password matched as plain text");
                 $verification = true;
            }

            if ($verification) {
                $token = hash_hmac('sha256', $user['id'], $this->secretKey);
                
                // Debug: Cookie setting
                $cookieStatus = setcookie("admin_token", $token, time() + 86400, "/");
                error_log("Setcookie status: " . ($cookieStatus ? "TRUE" : "FALSE"));

                return [
                    "success" => true,
                    "user" => ["id" => $user["id"], "email" => $email],
                    "message" => "✅ Đăng nhập thành công!"
                ];
            } else {
                error_log("Password mismatch.");
                return [
                    "success" => false,
                    "message" => "❌ Email hoặc mật khẩu không tồn tại!"
                ];
            }
        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return [
                "success" => false,
                "message" => "⚠️ Lỗi hệ thống: " . $e->getMessage()
            ];
        }
    }

    // Lấy admin đã đăng nhập từ cookie
    public function getAdmin() {
        if (isset($_COOKIE['admin_token'])) {
            $token = $_COOKIE['admin_token'];

            $sql = "SELECT id FROM administrator";
            $stmt = $this->conn->query($sql);
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($admins as $admin) {
                $id = $admin['id'];
                $expectedToken = hash_hmac('sha256', $id, $this->secretKey);
                if (hash_equals($expectedToken, $token)) {
                    return [
                        "success" => true,
                        "user" => ["id" => $id]
                    ];
                }
            }
        }
        return ["success" => false];
    }
}
?>
