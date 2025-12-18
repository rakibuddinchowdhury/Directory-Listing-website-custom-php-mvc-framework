<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'vendor') { header("Location: ../login.php"); exit; }

$db = new Database();
$conn = $db->getConnection();
$msg = '';

// 1. Handle "Purchase" Click
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $listing_id = $_POST['listing_id'];
    $amount = 29.99; // Premium Price
    $trans_id = 'TXN-' . strtoupper(uniqid()); // Simulated Transaction ID

    // Insert Payment Record
    $query = "INSERT INTO payments (user_id, listing_id, amount, transaction_id, status) VALUES (:uid, :lid, :amt, :tx, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':uid', $_SESSION['user_id']);
    $stmt->bindParam(':lid', $listing_id);
    $stmt->bindParam(':amt', $amount);
    $stmt->bindParam(':tx', $trans_id);

    if($stmt->execute()) {
        $msg = "<div class='bg-green-100 text-green-700 p-4 rounded mb-6 text-center'>
                    <i class='fas fa-check-circle text-2xl mb-2'></i><br>
                    <strong>Order Placed!</strong><br>
                    Transaction ID: $trans_id.<br>
                    Admin will approve your 'Featured' status shortly.
                </div>";
    }
}

// 2. Fetch Vendor's Listings (to choose which one to upgrade)
$stmt = $conn->prepare("SELECT id, title, is_featured FROM listings WHERE user_id = :uid");
$stmt->bindParam(':uid', $_SESSION['user_id']);
$stmt->execute();
$myListings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upgrade Plan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <nav class="bg-white shadow px-6 py-4 mb-8 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="../index.php" class="font-bold text-blue-600">BizFinder</a>
            <span class="text-gray-300">|</span>
            <a href="my-listings.php" class="text-gray-500 hover:text-blue-600">My Listings</a>
        </div>
        <a href="../logout.php" class="text-red-500">Logout</a>
    </nav>

    <div class="container mx-auto px-4 max-w-5xl">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-2">Choose Your Plan</h1>
        <p class="text-center text-gray-500 mb-10">Get more visibility and leads with Premium.</p>

        <?php echo $msg; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200 text-center opacity-75">
                <h3 class="text-xl font-bold text-gray-700">Basic</h3>
                <div class="text-4xl font-bold text-gray-800 my-4">$0</div>
                <ul class="text-gray-600 space-y-3 mb-8 text-sm">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Standard Listing</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> 3 Photos</li>
                    <li><i class="fas fa-times text-gray-300 mr-2"></i> No Featured Badge</li>
                </ul>
                <button disabled class="w-full bg-gray-200 text-gray-500 font-bold py-3 rounded cursor-not-allowed">Current Plan</button>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-xl border-2 border-blue-600 text-center transform scale-105">
                <div class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full inline-block mb-4 uppercase tracking-wide">Most Popular</div>
                <h3 class="text-xl font-bold text-gray-800">Premium Featured</h3>
                <div class="text-4xl font-bold text-blue-600 my-4">$29.99<span class="text-sm text-gray-500 font-normal">/mo</span></div>
                <ul class="text-gray-600 space-y-3 mb-8 text-sm">
                    <li><i class="fas fa-check text-green-500 mr-2"></i> <strong>Top of Search Results</strong></li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Featured Badge 👑</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i> Unlimited Photos</li>
                </ul>

                <form method="POST">
                    <div class="mb-4 text-left">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Listing to Upgrade</label>
                        <select name="listing_id" class="w-full border border-gray-300 rounded p-2 text-sm bg-gray-50">
                            <?php foreach($myListings as $l): ?>
                                <?php if($l['is_featured'] == 0): ?>
                                    <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['title']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if(count($myListings) > 0): ?>
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                            Upgrade Now
                        </button>
                    <?php else: ?>
                        <p class="text-red-500 text-xs">You need to create a listing first.</p>
                    <?php endif; ?>
                </form>
            </div>

        </div>
    </div>
</body>
</html>