<?php
require_once __DIR__ . "/../database/database.php";

class User
{

    private $conn;
    private $key = "mysecretkey"; // Key bí mật để mã hóa email

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // 🔹 Tạo user mới + tự động tạo cookie đăng nhập
    public function createUser($fullname, $email, $sdt, $password, $address)
    {
        try {
            // Validate phone number
            if (!preg_match('/^0[0-9]{9}$/', $sdt)) {
                return ["success" => false, "message" => "Số điện thoại phải bao gồm đúng 10 chữ số và bắt đầu bằng số 0."];
            }

            // Validate 3-level address
            if (substr_count($address, ',') < 2) {
                return ["success" => false, "message" => "Địa chỉ phải bao gồm ít nhất 3 cấp (ví dụ: Số nhà, Đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố)."];
            }

            // Kiểm tra email đã tồn tại chưa
            $check = $this->conn->prepare("SELECT id FROM user WHERE email = :email");
            $check->execute([":email" => $email]);
            if ($check->fetch()) {
                return ["success" => false, "message" => "Email đã tồn tại!"];
            }

            // Tạo user mới
            $sql = "INSERT INTO user (username, email, sdt, password, address) 
                    VALUES (:fullname, :email, :sdt, :password, :address)";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ":fullname" => $fullname,
                ":email" => $email,
                ":sdt" => $sdt,
                ":password" => password_hash($password, PASSWORD_BCRYPT),
                ":address" => $address
            ]);

            if ($result) {
                return ["success" => true, "message" => "Tạo tài khoản thành công!"];
            }
            return ["success" => false, "message" => "Không thể tạo tài khoản."];

        } catch (PDOException $e) {
            return ["success" => false, "message" => "Lỗi: " . $e->getMessage()];
        }
    }

    // 🔹 Đăng nhập -> tạo cookie và session
    public function login($email, $password)
    {
        $sql = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            // Chuẩn hóa email về chữ thường để hash luôn khớp
            $token = hash_hmac("sha256", strtolower($email), $this->key);
            
            // Set cookie với path là / để nhận diện toàn domain (Remember me)
            setcookie("token", $token, time() + (86400 * 30), "/"); // 30 ngày

            // Set session để đăng nhập ngay lập tức
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user'] = $user;


            return ["success" => true, "user" => $user];
        }
        return ["success" => false, "message" => "Email hoặc mật khẩu không đúng!"];
    }

    // 🔹 Lấy thông tin user từ session hoặc cookie
    public function getAccount()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Ưu tiên lấy từ Session
        if (isset($_SESSION['user'])) {
            return [
                "success" => true,
                "user" => $_SESSION['user']
            ];
        }

        // Nếu không có session, thử fallback về cookie (cho chức năng "remember me")
        if (!isset($_COOKIE['token'])) {
            return [
                "success" => false,
                "message" => "Chưa đăng nhập hoặc cookie không tồn tại."
            ];
        }

        $token = $_COOKIE['token'];

        try {
            $stmt = $this->conn->query("SELECT id, username, email, address FROM user");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($users as $u) {
                // So khớp hash với email chữ thường
                if (hash_hmac("sha256", strtolower($u['email']), $this->key) === $token) {
                    // Nếu tìm thấy từ cookie, cũng tạo session cho các request sau
                    $_SESSION['user'] = $u; 
                    return [
                        "success" => true,
                        "user" => $u
                    ];
                }
            }

            return [
                "success" => false,
                "message" => "Token không hợp lệ hoặc tài khoản không tồn tại."
            ];

        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Lỗi: " . $e->getMessage()
            ];
        }
    }
    // 🔹 Thay đổi mật khẩu
    public function changePassword($userId, $currentPassword, $newPassword)
    {
        try {
            // Lấy mật khẩu hiện tại
            $stmt = $this->conn->prepare("SELECT password FROM user WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return ["success" => false, "message" => "Người dùng không tồn tại."];
            }

            if (!password_verify($currentPassword, $user['password'])) {
                return ["success" => false, "message" => "Mật khẩu hiện tại không đúng."];
            }

            // Cập nhật mật khẩu mới
            $newHashed = password_hash($newPassword, PASSWORD_BCRYPT);
            $update = $this->conn->prepare("UPDATE user SET password = ? WHERE id = ?");
            $update->execute([$newHashed, $userId]);

            return ["success" => true, "message" => "Đổi mật khẩu thành công!"];

        } catch (PDOException $e) {
            return ["success" => false, "message" => "Lỗi: " . $e->getMessage()];
        }
    }

    // 🔹 Cập nhật thông tin cá nhân
    public function updateInfo($userId, $username, $email, $address) {
        try {
            // Validate 3-level address
            if (substr_count($address, ',') < 2) {
                return ["success" => false, "message" => "Địa chỉ phải bao gồm ít nhất 3 cấp (ví dụ: Số nhà, Đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố)."];
            }

            // Check email exist (nếu đổi email)
            $stmt = $this->conn->prepare("SELECT id FROM user WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                return ["success" => false, "message" => "Email này đã được sử dụng bởi tài khoản khác."];
            }

            $sql = "UPDATE user SET username = ?, email = ?, address = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username, $email, $address, $userId]);

            return ["success" => true, "message" => "Cập nhật thông tin thành công!"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Lỗi: " . $e->getMessage()];
        }
    }

    // 🔹 Xóa người dùng
    public function deleteUser($userId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM user WHERE id = ?");
            $stmt->execute([$userId]);
            return ["success" => true, "message" => "Xóa người dùng thành công!"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Lỗi: " . $e->getMessage()];
        }
    }

    // 🔹 Lấy tất cả người dùng với phân trang
    public function getAllUsers($limit, $offset) {
        try {
            $sql = "SELECT id, username, email, sdt, address FROM user ORDER BY id ASC LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return ["success" => true, "users" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Lỗi: " . $e->getMessage()];
        }
    }

    // 🔹 Lấy tổng số người dùng
    public function getTotalUsersCount() {
        try {
            $sql = "SELECT COUNT(id) FROM user";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0; // Return 0 or handle error appropriately
        }
    }
}
