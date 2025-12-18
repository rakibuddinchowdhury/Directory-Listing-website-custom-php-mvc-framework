<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Prevent deleting yourself
    if($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: users.php?msg=deleted"); exit;
    }
}

// Fetch Users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-[Poppins] flex">

    <aside class="w-64 bg-gray-900 text-white min-h-screen hidden md:block">
        <div class="p-6 text-xl font-bold border-b border-gray-700">BizFinder Admin</div>
        <nav class="mt-6">
            <a href="index.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-tachometer-alt mr-3 w-5"></i> Dashboard</a>
            <a href="users.php" class="block py-3 px-6 bg-blue-600 text-white"><i class="fas fa-users mr-3 w-5"></i> Users</a>
            <a href="listings.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-list mr-3 w-5"></i> Listings</a>
            <a href="settings.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-cog mr-3 w-5"></i> Settings</a>
            <a href="../logout.php" class="block py-3 px-6 text-red-400 mt-10"><i class="fas fa-sign-out-alt mr-3"></i> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Registered Users</h1>
        <?php if(isset($_GET['msg'])) echo "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>User deleted.</div>"; ?>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Joined</th>
                        <th class="p-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    <?php foreach($users as $u): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-0">
                        <td class="p-4 font-bold flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                            </div>
                            <?php echo htmlspecialchars($u['name']); ?>
                        </td>
                        <td class="p-4"><?php echo htmlspecialchars($u['email']); ?></td>
                        <td class="p-4">
                            <?php if($u['role'] == 'admin'): ?>
                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">Admin</span>
                            <?php elseif($u['role'] == 'vendor'): ?>
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">Vendor</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">User</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-gray-500"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td class="p-4 text-right">
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <a href="users.php?delete=<?php echo $u['id']; ?>" onclick="return confirm('Are you sure? This will delete all their listings too.');" class="text-red-500 hover:text-red-700 font-bold text-xs border border-red-200 bg-red-50 px-3 py-1 rounded">Delete</a>
                            <?php else: ?>
                                <span class="text-gray-300 text-xs">It's You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>