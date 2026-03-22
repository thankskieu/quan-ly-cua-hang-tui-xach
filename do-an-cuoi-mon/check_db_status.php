<?php
require_once "server/database/database.php";

try {
    $db = new Database();
    $conn = $db->connect();

    // Check if tables exist
    $tables = ['shipments', 'shipment_tracking'];
    $missing = [];

    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            $missing[] = $table;
        }
    }

    if (empty($missing)) {
        echo "OK: All tables exist.";
    } else {
        echo "MISSING: " . implode(", ", $missing);
        // Attempt to create them again
        $sql = "
            CREATE TABLE IF NOT EXISTS `shipments` (
              `id` int NOT NULL AUTO_INCREMENT,
              `bill_id` int NOT NULL,
              `tracking_number` varchar(50) NOT NULL,
              `carrier_name` varchar(100) NOT NULL,
              `status` enum('created','shipping','delivered','returned') DEFAULT 'created',
              `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `tracking_number` (`tracking_number`),
              KEY `bill_id` (`bill_id`),
              CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `shipment_tracking` (
              `id` int NOT NULL AUTO_INCREMENT,
              `shipment_id` int NOT NULL,
              `status_text` varchar(255) NOT NULL,
              `location` varchar(255) DEFAULT NULL,
              `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `shipment_id` (`shipment_id`),
              CONSTRAINT `shipment_tracking_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $conn->exec($sql);
        echo " -> Created missing tables.";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>