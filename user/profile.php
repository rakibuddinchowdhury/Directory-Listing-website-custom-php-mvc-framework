<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
if (!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit; }

$db = new Database();
$conn = $db->getConnection();
$msg = '';

// 1. Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Update Basic Info
    $query = "UPDATE users SET name = :name, email = :email WHERE id = :id";
    $params = [':name' => $name, ':email' => $email, ':id' => $_SESSION['user_id']];

    // Update Password ONLY if filled
    if (!empty($password)) {
        $query = "UPDATE users SET name = :name, email = :email, password = :pass WHERE id = :id";
        $params[':pass'] = password_hash($password, PASSWORD_BCRYPT);
    }

    $stmt = $conn->prepare($query);
    if($stmt->execute($params)) {
        $_SESSION['user_name'] = $name; // Update session
        $msg = "<div class='bg-green-100 text-green-700 p-3 rounded mb-6'>Profile updated successfully!</div>";
    } else {
        $msg = "<div class='bg-red-100 text-red-700 p-3 rounded mb-6'>Error updating profile.</div>";
    }
}

// 2. Fetch User Data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <nav class="bg-white shadow px-6 py-4 mb-8 flex justify-between items-center">
        <a href="../index.php" class="font-bold text-blue-600">BizFinder</a>
        <a href="../logout.php" class="text-red-500">Logout</a>
    </nav>

    <div class="container mx-auto px-4 max-w-lg mt-10">
        <div class="bg-white p-8 rounded-lg shadow-md border border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Edit Profile</h1>
            <?php echo $msg; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">New Password <span class="text-xs font-normal text-gray-400">(Leave blank to keep current)</span></label>
                    <input type="password" name="password" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-blue-500" placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-6 rounded hover:bg-blue-700 transition">Save Changes</button>
                    
                    <?php if($_SESSION['user_role'] == 'vendor'): ?>
                        <a href="../dashboard/my-listings.php" class="text-gray-500 hover:text-blue-600 text-sm">Back to Dashboard</a>
                    <?php elseif($_SESSION['user_role'] == 'admin'): ?>
                        <a href="../admin/index.php" class="text-gray-500 hover:text-blue-600 text-sm">Back to Admin</a>
                    <?php else: ?>
                        <a href="saved-listings.php" class="text-gray-500 hover:text-blue-600 text-sm">Back to Saved Listings</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

</body>
</html>