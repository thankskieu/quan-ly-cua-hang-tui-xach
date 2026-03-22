<?php
require_once __DIR__ . "/../database/database.php";
require_once __DIR__ . "/bill.php"; // Need to include Bill model here

class Shipment {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Helper to get bill_id from shipment_id
    private function getBillIdFromShipmentId($shipmentId) {
        $sql = "SELECT bill_id FROM shipments WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$shipmentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['bill_id'] : null;
    }

    public function createShipment($billId, $carrierName) {
        $trackingNumber = 'TRK' . strtoupper(uniqid());
        $sql = "INSERT INTO shipments (bill_id, tracking_number, carrier_name, status, created_at) VALUES (?, ?, ?, 'created', NOW())";
        $stmt = $this->conn->prepare($sql);
        if ($stmt->execute([$billId, $trackingNumber, $carrierName])) {
            $shipmentId = $this->conn->lastInsertId();
            $this->addTrackingLog($shipmentId, 'Đơn hàng đã được tạo vận đơn', 'Kho tổng');
            
            // Optionally update bill status to 'shipping' or 'processing' once shipment is created.
            // For now, bill status remains 'confirmed' until actual shipping starts.
            return $trackingNumber;
        }
        return false;
    }

    public function getShipmentByBillId($billId) {
        $sql = "SELECT * FROM shipments WHERE bill_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$billId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getShipmentByTrackingNumber($code) {
        $sql = "SELECT s.*, b.total_price, b.created_at as order_date, b.address, b.id as bill_id, u.username as customer_name, u.email as customer_email
                FROM shipments s
                JOIN bill b ON s.bill_id = b.id 
                LEFT JOIN user u ON b.user_id = u.id
                WHERE s.tracking_number = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addTrackingLog($shipmentId, $statusText, $location) {
        $sql = "INSERT INTO shipment_tracking (shipment_id, status_text, location, updated_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$shipmentId, $statusText, $location]);
    }

    public function getTrackingLogs($shipmentId) {
        $sql = "SELECT * FROM shipment_tracking WHERE shipment_id = ? ORDER BY updated_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$shipmentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllShipments($limit, $offset) {
        $sql = "SELECT s.*, b.total_price, u.username as customer_name
                FROM shipments s
                JOIN bill b ON s.bill_id = b.id
                LEFT JOIN user u ON b.user_id = u.id
                ORDER BY s.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalShipmentsCount() {
        $sql = "SELECT COUNT(id) FROM shipments";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchColumn();
    }

    public function updateShipmentStatus($shipmentId, $status) {
        $sql = "UPDATE shipments SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([$status, $shipmentId]);

        if ($success) {
            $billId = $this->getBillIdFromShipmentId($shipmentId);
            if ($billId) {
                // Ensure Bill model is available
                $billObj = new Bill(); 
                $billStatusToUpdate = null;
                switch ($status) {
                    case 'shipping':
                        $billStatusToUpdate = 'shipping';
                        break;
                    case 'delivered':
                        $billStatusToUpdate = 'delivered';
                        break;
                    case 'returned':
                        $billStatusToUpdate = 'returned';
                        break;
                }
                if ($billStatusToUpdate) {
                    $billObj->updateStatus($billId, $billStatusToUpdate);
                }
            }
        }
        return $success;
    }
}
?>
