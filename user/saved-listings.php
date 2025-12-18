<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

$db = new Database();
$conn = $db->getConnection();

// Fetch Favorites
$query = "SELECT l.*, c.name as category_name 
          FROM favorites f
          JOIN listings l ON f.listing_id = l.id
          LEFT JOIN categories c ON l.category_id = c.id
          WHERE f.user_id = :uid";

$stmt = $conn->prepare($query);
$stmt->bindParam(':uid', $_SESSION['user_id']);
$stmt->execute();
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Saved Listings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <nav class="bg-white shadow px-6 py-4 mb-8 flex justify-between">
        <a href="../index.php" class="font-bold text-blue-600">BizFinder</a>
        <a href="../logout.php" class="text-red-500">Logout</a>
    </nav>

    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">My Saved Listings</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php if(count($favorites) > 0): ?>
                <?php foreach($favorites as $listing): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="h-40 bg-gray-200 relative">
                        <?php if($listing['image']): ?>
                            <img src="../<?php echo $listing['image']; ?>" class="w-full h-full object-cover">
                        <?php endif; ?>
                        <span class="absolute top-2 left-2 bg-white px-2 py-1 text-xs font-bold rounded text-primary">
                            <?php echo $listing['category_name']; ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 mb-1">
                            <a href="../listing-detail.php?slug=<?php echo $listing['slug']; ?>"><?php echo htmlspecialchars($listing['title']); ?></a>
                        </h3>
                        <p class="text-sm text-gray-500 mb-4"><i class="fas fa-map-marker-alt text-red-400"></i> <?php echo $listing['address']; ?></p>
                        
                        <form action="../listing-detail.php?slug=<?php echo $listing['slug']; ?>" method="POST">
                             <button type="submit" name="toggle_favorite" class="w-full border border-red-500 text-red-500 text-sm py-1 rounded hover:bg-red-50">
                                 <i class="fas fa-trash-alt mr-1"></i> Remove
                             </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center py-10 text-gray-500">No saved listings yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>