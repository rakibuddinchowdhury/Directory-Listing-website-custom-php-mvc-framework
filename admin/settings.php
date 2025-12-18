<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();
$msg = '';

// 1. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_name = $_POST['site_name'];
    $site_email = $_POST['site_email'];
    $about = $_POST['about_text'];
    $footer = $_POST['footer_text'];

    // Update the single row (id=1)
    $query = "UPDATE settings SET site_name = :name, site_email = :email, about_text = :about, footer_text = :footer WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $site_name);
    $stmt->bindParam(':email', $site_email);
    $stmt->bindParam(':about', $about);
    $stmt->bindParam(':footer', $footer);

    if($stmt->execute()) {
        $msg = "<div class='bg-green-100 text-green-700 p-3 rounded mb-6'>Settings updated successfully!</div>";
    }
}

// 2. Fetch Current Settings
$settings = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Settings</title>
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
            <a href="payments.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-dollar-sign mr-3 w-5"></i> Payments</a>
            <a href="settings.php" class="block py-3 px-6 bg-blue-600 text-white"><i class="fas fa-cog mr-3 w-5"></i> Settings</a>
            <a href="../logout.php" class="block py-3 px-6 text-red-400 mt-10"><i class="fas fa-sign-out-alt mr-3"></i> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">General Settings</h1>
        <?php echo $msg; ?>

        <form method="POST" class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 max-w-3xl">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Website Name</label>
                    <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Admin/Support Email</label>
                    <input type="email" name="site_email" value="<?php echo htmlspecialchars($settings['site_email']); ?>" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">About Us Content</label>
                <textarea name="about_text" rows="5" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-blue-500"><?php echo htmlspecialchars($settings['about_text']); ?></textarea>
                <p class="text-xs text-gray-500 mt-1">This text will appear on the 'Contact / About' page.</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Footer Copyright Text</label>
                <input type="text" name="footer_text" value="<?php echo htmlspecialchars($settings['footer_text']); ?>" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-blue-500">
            </div>

            <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-8 rounded hover:bg-blue-700 transition">Save Changes</button>
        </form>
    </div>

</body>
</html>