<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();
$msg = '';

// 1. Handle Add Category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $icon = $_POST['icon'];

    if(!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, icon) VALUES (:name, :slug, :icon)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':icon', $icon);
        
        if($stmt->execute()) {
            $msg = "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>Category added successfully!</div>";
        } else {
            $msg = "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>Error adding category.</div>";
        }
    }
}

// 2. Handle Delete Category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Optional: Check if listings exist in this category before deleting
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->bindParam(':id', $id);
    if($stmt->execute()) {
        header("Location: categories.php?msg=deleted"); exit;
    }
}

// 3. Fetch All Categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-[Poppins] flex">

    <aside class="w-64 bg-gray-900 text-white min-h-screen hidden md:block">
        <div class="p-6 text-xl font-bold border-b border-gray-700">BizFinder Admin</div>
        <nav class="mt-6">
            <a href="index.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-tachometer-alt mr-3 w-5"></i> Dashboard</a>
            <a href="listings.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-list mr-3 w-5"></i> Listings</a>
            <a href="categories.php" class="block py-3 px-6 bg-blue-600 text-white"><i class="fas fa-tags mr-3 w-5"></i> Categories</a>
            <a href="payments.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-dollar-sign mr-3 w-5"></i> Payments</a>
            <a href="settings.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-cog mr-3 w-5"></i> Settings</a>
            <a href="../logout.php" class="block py-3 px-6 text-red-400 mt-10"><i class="fas fa-sign-out-alt mr-3"></i> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Categories</h1>
        
        <?php echo $msg; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted') echo "<div class='bg-yellow-100 text-yellow-700 p-3 rounded mb-4'>Category deleted.</div>"; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-lg shadow-sm h-fit">
                <h3 class="font-bold text-lg mb-4 text-gray-700">Add New Category</h3>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Category Name</label>
                        <input type="text" name="name" required placeholder="e.g. Nightlife" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">FontAwesome Icon Class</label>
                        <div class="flex items-center">
                            <span class="bg-gray-100 border border-r-0 border-gray-300 p-2 rounded-l text-gray-500">fa-</span>
                            <input type="text" name="icon" required placeholder="glass-cheers" class="w-full border border-gray-300 rounded-r p-2 focus:outline-none focus:border-blue-500">
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Example: 'utensils', 'hotel', 'car'</p>
                    </div>
                    <button type="submit" name="add_category" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">Add Category</button>
                </form>
            </div>

            <div class="md:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="p-4">Icon</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Slug</th>
                            <th class="p-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        <?php foreach($categories as $cat): ?>
                        <tr class="hover:bg-gray-50 border-b last:border-0">
                            <td class="p-4 text-blue-500 text-lg"><i class="fas fa-<?php echo htmlspecialchars($cat['icon']); ?>"></i></td>
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td class="p-4 text-gray-400"><?php echo htmlspecialchars($cat['slug']); ?></td>
                            <td class="p-4 text-right">
                                <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Delete this category?');" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded transition">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>
</html>