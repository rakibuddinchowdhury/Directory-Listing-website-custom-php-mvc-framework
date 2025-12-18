<?php 
include 'includes/header.php'; 
require_once 'controllers/Listing.php';
require_once 'controllers/Review.php';

// ... (Keep existing Listing & Review initialization) ...

// --- NEW: Handle Contact Form Submission ---
$msgAlert = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $sender_name = $_POST['name'];
    $sender_email = $_POST['email'];
    $message = $_POST['message'];
    $listing_id = $listing['id'];

    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("INSERT INTO messages (listing_id, sender_name, sender_email, message) VALUES (:lid, :name, :email, :msg)");
    $stmt->bindParam(':lid', $listing_id);
    $stmt->bindParam(':name', $sender_name);
    $stmt->bindParam(':email', $sender_email);
    $stmt->bindParam(':msg', $message);
    
    if($stmt->execute()) {
        $msgAlert = "<script>alert('Message sent to business owner!');</script>";
    }
}

// --- NEW: Check if Favorite (for logged in users) ---
$isFavorited = false;
if(isset($_SESSION['user_id'])) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = :uid AND listing_id = :lid");
    $stmt->bindParam(':uid', $_SESSION['user_id']);
    $stmt->bindParam(':lid', $listing['id']);
    $stmt->execute();
    if($stmt->rowCount() > 0) $isFavorited = true;
}

// --- NEW: Handle Favorite Toggle (Simple POST for now, can be AJAX) ---
if (isset($_POST['toggle_favorite'])) {
    if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
    
    $db = new Database();
    $conn = $db->getConnection();
    if($isFavorited) {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = :uid AND listing_id = :lid");
    } else {
        $stmt = $conn->prepare("INSERT INTO favorites (user_id, listing_id) VALUES (:uid, :lid)");
    }
    $stmt->bindParam(':uid', $_SESSION['user_id']);
    $stmt->bindParam(':lid', $listing['id']);
    $stmt->execute();
    
    // Refresh page to show new state
    header("Location: listing-detail.php?slug=" . $_GET['slug']); exit;
}
?>

<?php echo $msgAlert; ?>

<form method="POST" class="inline">
    <button type="submit" name="toggle_favorite" class="px-4 py-2 rounded-md transition border <?php echo $isFavorited ? 'bg-red-500 text-white border-red-500' : 'bg-white border-gray-300 text-gray-700 hover:text-red-500'; ?>">
        <i class="<?php echo $isFavorited ? 'fas' : 'far'; ?> fa-heart"></i> 
        <?php echo $isFavorited ? 'Saved' : 'Save'; ?>
    </button>
</form>

<div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100 mt-6">
    <h3 class="font-bold text-lg mb-4 text-dark">Contact Business</h3>
    <form method="POST">
        <div class="mb-3">
            <input type="text" name="name" required placeholder="Your Name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-primary">
        </div>
        <div class="mb-3">
            <input type="email" name="email" required placeholder="Your Email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-primary">
        </div>
        <div class="mb-3">
            <textarea name="message" required rows="3" placeholder="Message..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-primary"></textarea>
        </div>
        <button type="submit" name="send_message" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-blue-600 transition">Send Message</button>
    </form>
</div>