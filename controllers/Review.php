<?php
require_once __DIR__ . '/../config/Database.php';

class Review {
    private $conn;
    private $table = 'reviews';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Submit a Review
    public function submitReview($listing_id, $user_id, $rating, $comment) {
        // Optional: Check if user already reviewed this listing to prevent duplicates
        $check = $this->conn->prepare("SELECT id FROM " . $this->table . " WHERE listing_id = :lid AND user_id = :uid");
        $check->bindParam(':lid', $listing_id);
        $check->bindParam(':uid', $user_id);
        $check->execute();
        
        if($check->rowCount() > 0) {
            return "You have already reviewed this listing.";
        }

        $query = "INSERT INTO " . $this->table . " (listing_id, user_id, rating, comment) VALUES (:lid, :uid, :rating, :comment)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':lid', $listing_id);
        $stmt->bindParam(':uid', $user_id);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);

        if($stmt->execute()) {
            return true;
        }
        return "Something went wrong.";
    }

    // Get Reviews for a Listing
    public function getReviews($listing_id) {
        $query = "SELECT r.*, u.name as user_name 
                  FROM " . $this->table . " r
                  LEFT JOIN users u ON r.user_id = u.id
                  WHERE r.listing_id = :lid
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lid', $listing_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calculate Average Rating
    public function getAverageRating($listing_id) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(id) as total_reviews FROM " . $this->table . " WHERE listing_id = :lid";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lid', $listing_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>