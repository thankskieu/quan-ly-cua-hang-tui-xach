CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,    -- Cần thêm dấu phẩy ở đây
    password VARCHAR(100) NOT NULL, -- Cần thêm dấu phẩy ở đây
    address VARCHAR(1000) NOT NULL
    -- KHÔNG cần dấu phẩy sau cột cuối cùng
);