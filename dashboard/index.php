<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

// 1. Secure Page
$auth = new Auth();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'vendor') {
    header("Location: ../login.php"); exit;
}

$db = new Database();
$conn = $db->getConnection();
$uid = $_SESSION['user_id'];

// 2. Fetch Vendor Stats
// Count Listings
$stmt = $conn->prepare("SELECT COUNT(*) FROM listings WHERE user_id = :uid");
$stmt->execute([':uid' => $uid]);
$totalListings = $stmt->fetchColumn();

// Count Total Views (Sum of views on all listings)
$stmt = $conn->prepare("SELECT SUM(views) FROM listings WHERE user_id = :uid");
$stmt->execute([':uid' => $uid]);
$totalViews = $stmt->fetchColumn() ?: 0;

// Count Messages
$stmt = $conn->prepare("SELECT COUNT(m.id) FROM messages m JOIN listings l ON m.listing_id = l.id WHERE l.user_id = :uid");
$stmt->execute([':uid' => $uid]);
$totalMessages = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <a href="../index.php" class="text-xl font-bold text-blue-600">BizFinder</a>
            <span class="text-gray-300">|</span>
            <span class="font-bold text-gray-700">Dashboard</span>
        </div>
        <div>
            <span class="mr-4 text-gray-600">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../logout.php" class="text-red-500 hover:underline">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-6 max-w-5xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Overview</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl mr-4">
                    <i class="fas fa-list"></i>
                </div>
                <div>
                    <div class="text-gray-500 text-sm font-medium">My Listings</div>
                    <div class="text-2xl font-bold text-gray-800"><?php echo $totalListings; ?></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xl mr-4">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <div class="text-gray-500 text-sm font-medium">Total Views</div>
                    <div class="text-2xl font-bold text-gray-800"><?php echo number_format($totalViews); ?></div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center">
                <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xl mr-4">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <div class="text-gray-500 text-sm font-medium">Inquiries</div>
                    <div class="text-2xl font-bold text-gray-800"><?php echo $totalMessages; ?></div>
                </div>
            </div>
        </div>

        <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="add-listing.php" class="block bg-blue-600 text-white p-6 rounded-lg shadow hover:bg-blue-700 transition text-center">
                <i class="fas fa-plus-circle text-3xl mb-3"></i>
                <div class="font-bold">Add New Listing</div>
            </a>
            <a href="my-listings.php" class="block bg-white text-gray-700 border border-gray-200 p-6 rounded-lg shadow-sm hover:border-blue-500 hover:text-blue-600 transition text-center">
                <i class="fas fa-edit text-3xl mb-3 text-gray-400"></i>
                <div class="font-bold">Manage Listings</div>
            </a>
            <a href="messages.php" class="block bg-white text-gray-700 border border-gray-200 p-6 rounded-lg shadow-sm hover:border-purple-500 hover:text-purple-600 transition text-center">
                <i class="fas fa-comments text-3xl mb-3 text-gray-400"></i>
                <div class="font-bold">View Messages</div>
            </a>
        </div>
    </div>

</body>
</html> 
