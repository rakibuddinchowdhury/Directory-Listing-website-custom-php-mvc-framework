<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

// 1. Secure Access
$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

// 2. Fetch Dashboard Statistics
// Count Total Users
$stmt = $conn->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

// Count Active Listings
$stmt = $conn->query("SELECT COUNT(*) FROM listings WHERE status = 'active'");
$activeListings = $stmt->fetchColumn();

// Count Pending Listings (For Attention)
$stmt = $conn->query("SELECT COUNT(*) FROM listings WHERE status = 'pending'");
$pendingListings = $stmt->fetchColumn();

// Calculate Total Revenue
$stmt = $conn->query("SELECT SUM(amount) FROM payments WHERE status = 'completed'");
$totalRevenue = $stmt->fetchColumn();
$totalRevenue = $totalRevenue ? $totalRevenue : 0; // Handle null if no payments

// 3. Fetch Recent Listings (Limit 5)
$query = "SELECT l.*, c.name as category_name, u.name as owner_name 
          FROM listings l 
          LEFT JOIN categories c ON l.category_id = c.id
          LEFT JOIN users u ON l.user_id = u.id
          ORDER BY l.created_at DESC LIMIT 5";
$recents = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - BizFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-[Poppins] flex">

    <aside class="w-64 bg-gray-900 text-white min-h-screen hidden md:block flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-gray-700 flex items-center">
            <i class="fas fa-shield-alt mr-2 text-blue-500"></i> Admin Panel
        </div>
        <nav class="mt-6">
            <a href="index.php" class="block py-3 px-6 bg-blue-600 text-white border-r-4 border-blue-400">
                <i class="fas fa-tachometer-alt mr-3 w-5"></i> Dashboard
            </a>
            <a href="listings.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <i class="fas fa-list mr-3 w-5"></i> Listings
                <?php if($pendingListings > 0): ?>
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full ml-2"><?php echo $pendingListings; ?></span>
                <?php endif; ?>
            </a>
            <a href="payments.php" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition">
                <i class="fas fa-dollar-sign mr-3 w-5"></i> Payments
            </a>
            <a href="../index.php" target="_blank" class="block py-3 px-6 text-gray-400 hover:bg-gray-800 hover:text-white transition mt-8">
                <i class="fas fa-external-link-alt mr-3 w-5"></i> Visit Website
            </a>
            <a href="../logout.php" class="block py-3 px-6 text-red-400 hover:bg-gray-800 hover:text-red-300 transition">
                <i class="fas fa-sign-out-alt mr-3 w-5"></i> Logout
            </a>
        </nav>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="bg-white shadow-sm p-4 flex justify-between items-center z-10">
            <h2 class="text-xl font-semibold text-gray-800">Overview</h2>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <div class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                    <div class="text-xs text-gray-500">Administrator</div>
                </div>
                <div class="h-10 w-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-lg border border-blue-200">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 md:p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500 flex items-center">
                    <div class="p-3 bg-blue-50 rounded-full mr-4 text-blue-500">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase">Total Users</div>
                        <div class="text-2xl font-bold text-gray-800"><?php echo number_format($totalUsers); ?></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500 flex items-center">
                    <div class="p-3 bg-green-50 rounded-full mr-4 text-green-500">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase">Total Revenue</div>
                        <div class="text-2xl font-bold text-gray-800">$<?php echo number_format($totalRevenue, 2); ?></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500 flex items-center">
                    <div class="p-3 bg-yellow-50 rounded-full mr-4 text-yellow-500">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase">Pending Approval</div>
                        <div class="text-2xl font-bold text-gray-800"><?php echo number_format($pendingListings); ?></div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500 flex items-center">
                    <div class="p-3 bg-purple-50 rounded-full mr-4 text-purple-500">
                        <i class="fas fa-store text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-gray-500 text-sm font-medium uppercase">Active Listings</div>
                        <div class="text-2xl font-bold text-gray-800"><?php echo number_format($activeListings); ?></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-700">Recently Added Listings</h3>
                    <a href="listings.php" class="text-sm text-blue-600 hover:underline">View All</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-100 text-gray-600 text-xs uppercase font-semibold">
                            <tr>
                                <th class="p-4">Listing Title</th>
                                <th class="p-4">Owner</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-100">
                            <?php foreach($recents as $row): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </td>
                                <td class="p-4"><?php echo htmlspecialchars($row['owner_name']); ?></td>
                                <td class="p-4">
                                    <span class="bg-gray-100 text-gray-600 py-1 px-2 rounded text-xs">
                                        <?php echo htmlspecialchars($row['category_name']); ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php if($row['status'] == 'active'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    <?php elseif($row['status'] == 'pending'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Rejected
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-right text-gray-500">
                                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>