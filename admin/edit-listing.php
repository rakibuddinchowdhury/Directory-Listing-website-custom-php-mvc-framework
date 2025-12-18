<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
if (!isset($_SESSION['user_role'])) { header("Location: ../login.php"); exit; }

$database = new Database();
$conn = $database->getConnection();
$msg = '';

// 1. Fetch Existing Data
if(!isset($_GET['id'])) { header("Location: my-listings.php"); exit; }
$id = $_GET['id'];

$query = "SELECT * FROM listings WHERE id = :id AND user_id = :uid";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':uid', $_SESSION['user_id']);
$stmt->execute();
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$listing) { echo "Listing not found or access denied."; exit; }

// 2. Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $website = $_POST['website'];
    $email = $_POST['email'];

    // Update Query
    $updateQuery = "UPDATE listings SET 
                    title = :title, description = :desc, address = :addr, 
                    phone = :phone, website = :web, email = :email, status = 'pending' 
                    WHERE id = :id AND user_id = :uid";
    
    // Note: status is set back to 'pending' on edit so admin checks changes.
    // If you trust vendors, you can remove "status = 'pending'"

    $upStmt = $conn->prepare($updateQuery);
    $upStmt->bindParam(':title', $title);
    $upStmt->bindParam(':desc', $description);
    $upStmt->bindParam(':addr', $address);
    $upStmt->bindParam(':phone', $phone);
    $upStmt->bindParam(':web', $website);
    $upStmt->bindParam(':email', $email);
    $upStmt->bindParam(':id', $id);
    $upStmt->bindParam(':uid', $_SESSION['user_id']);

    if($upStmt->execute()) {
        $msg = "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>Listing updated! It is now pending approval.</div>";
        // Refresh data
        $stmt->execute(); 
        $listing = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $msg = "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>Update failed.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Listing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-[Poppins]">
    
    <div class="container mx-auto px-4 py-10 max-w-3xl">
        <div class="flex items-center mb-6">
            <a href="my-listings.php" class="text-gray-500 hover:text-blue-600 mr-4"><i class="fas fa-arrow-left"></i> Back</a>
            <h1 class="text-2xl font-bold text-gray-800">Edit Listing: <?php echo htmlspecialchars($listing['title']); ?></h1>
        </div>

        <?php echo $msg; ?>

        <form method="POST" class="bg-white p-8 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($listing['title']); ?>" required class="w-full border p-3 rounded">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Description</label>
                <textarea name="description" rows="5" class="w-full border p-3 rounded"><?php echo htmlspecialchars($listing['description']); ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($listing['address']); ?>" class="w-full border p-3 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($listing['phone']); ?>" class="w-full border p-3 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($listing['email']); ?>" class="w-full border p-3 rounded">
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-2">Website</label>
                    <input type="text" name="website" value="<?php echo htmlspecialchars($listing['website']); ?>" class="w-full border p-3 rounded">
                </div>
            </div>

            <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-6 rounded hover:bg-blue-700 transition">Update Listing</button>
        </form>
    </div>
</body>
</html>