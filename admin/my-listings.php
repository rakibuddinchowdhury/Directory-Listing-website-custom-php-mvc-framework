<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

// 1. Secure Page
$auth = new Auth();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'vendor') {
    header("Location: ../login.php"); exit;
}

$database = new Database();
$conn = $database->getConnection();

// 2. Fetch Vendor's Listings
$query = "SELECT l.*, c.name as category_name 
          FROM listings l 
          LEFT JOIN categories c ON l.category_id = c.id
          WHERE l.user_id = :uid 
          ORDER BY l.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':uid', $_SESSION['user_id']);
$stmt->execute();
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Handle Delete Action
if(isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    // Security check: ensure this listing belongs to this user
    $delQuery = "DELETE FROM listings WHERE id = :id AND user_id = :uid";
    $delStmt = $conn->prepare($delQuery);
    $delStmt->bindParam(':id', $delId);
    $delStmt->bindParam(':uid', $_SESSION['user_id']);
    if($delStmt->execute()) {
        header("Location: my-listings.php?msg=deleted"); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Listings - BizFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center mb-8">
        <a href="../index.php" class="text-xl font-bold text-blue-600">BizFinder</a>
        <div class="space-x-4">
            <a href="add-listing.php" class="text-gray-600 hover:text-blue-600">Add Listing</a>
            <span class="text-gray-400">|</span>
            <span class="text-gray-800 font-semibold"><?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="text-red-500 hover:underline ml-2">Logout</a>
        </div>
    </nav>

    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">My Listings</h1>
            <a href="add-listing.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition"><i class="fas fa-plus mr-2"></i> Add New</a>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">Listing deleted successfully.</div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <tr>
                        <th class="p-4 border-b">Listing</th>
                        <th class="p-4 border-b">Category</th>
                        <th class="p-4 border-b">Views</th>
                        <th class="p-4 border-b">Status</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if(count($listings) > 0): ?>
                        <?php foreach($listings as $item): ?>
                        <tr class="hover:bg-gray-50 border-b last:border-0">
                            <td class="p-4 font-semibold">
                                <?php echo htmlspecialchars($item['title']); ?>
                                <br>
                                <a href="../listing-detail.php?slug=<?php echo $item['slug']; ?>" target="_blank" class="text-xs text-blue-500 font-normal hover:underline">View Live</a>
                            </td>
                            <td class="p-4 text-sm"><?php echo htmlspecialchars($item['category_name']); ?></td>
                            <td class="p-4 text-sm"><?php echo $item['views']; ?></td>
                            <td class="p-4">
                                <?php if($item['status'] == 'active'): ?>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">Active</span>
                                <?php elseif($item['status'] == 'pending'): ?>
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">Pending</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="edit-listing.php?id=<?php echo $item['id']; ?>" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded transition"><i class="fas fa-edit"></i></a>
                                <a href="my-listings.php?delete=<?php echo $item['id']; ?>" onclick="return confirm('Delete this listing? This cannot be undone.');" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-500">You haven't posted any listings yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>