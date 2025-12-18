<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

// 1. Secure Page (Admin Only)
$auth = new Auth();
$auth->requireAdmin();

$database = new Database();
$conn = $database->getConnection();

// 2. Fetch All Listings
$query = "SELECT l.*, c.name as category_name, u.name as owner_name 
          FROM listings l 
          LEFT JOIN categories c ON l.category_id = c.id
          LEFT JOIN users u ON l.user_id = u.id
          ORDER BY l.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Listings - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-[Poppins] flex">

    <aside class="w-64 bg-gray-900 text-white min-h-screen hidden md:block">
        <div class="p-6 text-xl font-bold border-b border-gray-700">BizFinder Admin</div>
        <nav class="mt-6">
            <a href="index.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-tachometer-alt mr-3 w-5"></i> Dashboard</a>
            <a href="listings.php" class="block py-3 px-6 bg-blue-600 text-white"><i class="fas fa-list mr-3 w-5"></i> Listings</a>
            <a href="categories.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-tags mr-3 w-5"></i> Categories</a>
            <a href="users.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-users mr-3 w-5"></i> Users</a>
            <a href="payments.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-dollar-sign mr-3 w-5"></i> Payments</a>
            <a href="settings.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-cog mr-3 w-5"></i> Settings</a>
            <a href="../logout.php" class="block py-3 px-6 text-red-400 mt-10"><i class="fas fa-sign-out-alt mr-3"></i> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 p-8">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Listings</h1>
            
            <a href="../dashboard/add-listing.php" class="bg-green-600 text-white font-bold py-2 px-6 rounded hover:bg-green-700 transition shadow-lg flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> Add New Listing
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                    <tr>
                        <th class="p-4 border-b">Title</th>
                        <th class="p-4 border-b">Owner</th>
                        <th class="p-4 border-b">Category</th>
                        <th class="p-4 border-b">Status</th>
                        <th class="p-4 border-b text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php if(count($listings) > 0): ?>
                        <?php foreach ($listings as $listing): ?>
                        <tr class="hover:bg-gray-50 border-b last:border-0 transition">
                            <td class="p-4 font-bold text-gray-800">
                                <?php echo htmlspecialchars($listing['title']); ?>
                                <br>
                                <a href="../listing-detail.php?slug=<?php echo $listing['slug']; ?>" target="_blank" class="text-xs text-blue-500 hover:underline font-normal">
                                    <i class="fas fa-external-link-alt mr-1"></i> View Live
                                </a>
                            </td>
                            <td class="p-4">
                                <?php echo htmlspecialchars($listing['owner_name']); ?>
                                <?php if($listing['owner_name'] == 'Super Admin'): ?>
                                    <span class="bg-purple-100 text-purple-600 text-[10px] px-1 rounded font-bold">ADMIN</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded text-xs">
                                    <?php echo htmlspecialchars($listing['category_name']); ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <?php if($listing['status'] == 'active'): ?>
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold border border-green-200">Active</span>
                                <?php elseif($listing['status'] == 'pending'): ?>
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-bold border border-yellow-200">Pending</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold border border-red-200">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center space-x-1">
                                <?php if($listing['status'] != 'active'): ?>
                                    <a href="listing-action.php?action=approve&id=<?php echo $listing['id']; ?>" class="inline-flex items-center justify-center w-8 h-8 bg-green-500 text-white rounded hover:bg-green-600 transition" title="Approve"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <a href="listing-action.php?action=delete&id=<?php echo $listing['id']; ?>" onclick="return confirm('Delete this listing?');" class="inline-flex items-center justify-center w-8 h-8 bg-red-500 text-white rounded hover:bg-red-600 transition" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="p-8 text-center text-gray-500">No listings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>