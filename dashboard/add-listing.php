<?php
require_once '../controllers/Auth.php';
require_once '../controllers/Listing.php';
require_once '../config/Database.php';

// 1. Secure Page (Allow Vendor AND Admin)
$auth = new Auth();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'vendor' && $_SESSION['user_role'] !== 'admin')) {
    header("Location: ../login.php");
    exit;
}

// 2. Fetch Dynamic Data
$db = new Database();
$conn = $db->getConnection();
$cats = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$locs = $conn->query("SELECT * FROM locations ORDER BY city ASC")->fetchAll(PDO::FETCH_ASSOC);

// 3. Handle Form Submission & Upload
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $listing = new Listing();
    // This function handles the file upload automatically
    if($listing->createListing($_POST, $_FILES)) {
        // If Admin is adding, we could auto-approve it, but for now it sets to 'pending'
        // You can go to admin panel and approve your own listing.
        $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 shadow-sm">
                        <p class="font-bold">Success!</p>
                        <p>Listing submitted successfully.</p>
                    </div>';
    } else {
        $message = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 shadow-sm">
                        <p class="font-bold">Error!</p>
                        <p>Something went wrong. Please check your inputs.</p>
                    </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Listing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <nav class="bg-white shadow px-6 py-4 flex justify-between items-center mb-8 sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <a href="../index.php" class="text-xl font-bold text-blue-600">BizFinder</a>
            <?php if($_SESSION['user_role'] == 'admin'): ?>
                <span class="text-gray-300">|</span>
                <a href="../admin/listings.php" class="text-gray-500 hover:text-blue-600 font-medium">Admin Panel</a>
            <?php endif; ?>
        </div>
        <div>
            <span class="mr-4 text-gray-600 hidden md:inline">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../logout.php" class="text-red-500 hover:bg-red-50 px-3 py-1 rounded transition"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Add New Listing</h1>
        <?php echo $message; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            
            <div class="p-8 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-700 mb-6">1. Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Title *</label>
                        <input type="text" name="title" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Category *</label>
                        <select name="category_id" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Select</option>
                            <?php foreach($cats as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Description *</label>
                    <textarea name="description" rows="5" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-700 mb-6">2. Location & Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">City *</label>
                        <select name="location_id" required class="w-full border border-gray-300 p-3 rounded-lg bg-white">
                            <option value="">Select</option>
                            <?php foreach($locs as $loc): ?>
                                <option value="<?php echo $loc['id']; ?>"><?php echo htmlspecialchars($loc['city']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Address</label>
                        <input type="text" name="address" class="w-full border border-gray-300 p-3 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Phone</label>
                        <input type="text" name="phone" class="w-full border border-gray-300 p-3 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Website</label>
                        <input type="url" name="website" class="w-full border border-gray-300 p-3 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                        <input type="email" name="email" class="w-full border border-gray-300 p-3 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="p-8">
                <h3 class="text-lg font-bold text-gray-700 mb-6">3. Image Upload</h3>
                <div class="mb-2">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Featured Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-2 rounded bg-gray-50">
                </div>
            </div>

            <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-lg">Submit Listing</button>
            </div>
        </form>
    </div>
</body>
</html>