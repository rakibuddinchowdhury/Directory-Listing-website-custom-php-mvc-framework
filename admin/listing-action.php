<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
$auth->requireAdmin();

if (isset($_GET['action']) && isset($_GET['id'])) {
    $database = new Database();
    $conn = $database->getConnection();
    
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'approve') {
        $query = "UPDATE listings SET status = 'active' WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    } 
    elseif ($action == 'reject') {
        $query = "UPDATE listings SET status = 'rejected' WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    } 
    elseif ($action == 'delete') {
        // Optional: Delete associated image file before deleting record
        // $stmt = $conn->prepare("SELECT image FROM listings WHERE id = :id"); ... unlink($path);

        $query = "DELETE FROM listings WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}

// Redirect back to listings page
header("Location: listings.php");
exit;
?>