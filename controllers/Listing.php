<?php
require_once __DIR__ . '/../config/Database.php';

class Listing {
    private $conn;
    private $table = 'listings';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. Get Paginated Listings
    public function getListings($filters = [], $page = 1, $limit = 6) {
        $offset = ($page - 1) * $limit;

        $query = "SELECT l.*, c.name as category_name, loc.city, loc.state 
                  FROM " . $this->table . " l
                  LEFT JOIN categories c ON l.category_id = c.id
                  LEFT JOIN locations loc ON l.location_id = loc.id
                  WHERE l.status = 'active'";

        // Apply Filters (Same logic as before)
        if (!empty($filters['search'])) {
            $query .= " AND (l.title LIKE :search OR l.description LIKE :search)";
        }
        if (!empty($filters['category'])) {
            $query .= " AND c.slug = :category";
        }
        if (!empty($filters['location'])) {
            $query .= " AND loc.city = :location";
        }

        $query .= " ORDER BY l.is_featured DESC, l.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        // Bind Params
        if (!empty($filters['search'])) {
            $searchTerm = "%" . $filters['search'] . "%";
            $stmt->bindValue(':search', $searchTerm);
        }
        if (!empty($filters['category'])) {
            $stmt->bindValue(':category', $filters['category']);
        }
        if (!empty($filters['location'])) {
            $stmt->bindValue(':location', $filters['location']);
        }
        
        // Bind Pagination Params (Must be integers)
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Get Total Count (For Pagination Numbers)
    public function getTotalCount($filters = []) {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " l
                  LEFT JOIN categories c ON l.category_id = c.id
                  LEFT JOIN locations loc ON l.location_id = loc.id
                  WHERE l.status = 'active'";

        if (!empty($filters['search'])) {
            $query .= " AND (l.title LIKE :search OR l.description LIKE :search)";
        }
        if (!empty($filters['category'])) {
            $query .= " AND c.slug = :category";
        }
        if (!empty($filters['location'])) {
            $query .= " AND loc.city = :location";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($filters['search'])) {
            $searchTerm = "%" . $filters['search'] . "%";
            $stmt->bindValue(':search', $searchTerm);
        }
        if (!empty($filters['category'])) {
            $stmt->bindValue(':category', $filters['category']);
        }
        if (!empty($filters['location'])) {
            $stmt->bindValue(':location', $filters['location']);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    
    // ... keep your createListing and getListingBySlug methods here ...
}
?>