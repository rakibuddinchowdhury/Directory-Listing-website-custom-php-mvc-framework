<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'vendor') { header("Location: ../login.php"); exit; }

$db = new Database();
$conn = $db->getConnection();

// Fetch Messages for Listings owned by this Vendor
$query = "SELECT m.*, l.title as listing_title 
          FROM messages m
          JOIN listings l ON m.listing_id = l.id
          WHERE l.user_id = :uid
          ORDER BY m.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bindParam(':uid', $_SESSION['user_id']);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leads & Messages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-[Poppins]">

    <nav class="bg-white shadow px-6 py-4 mb-8 flex justify-between">
        <div class="flex items-center gap-4">
            <a href="../index.php" class="font-bold text-blue-600">BizFinder</a>
            <span class="text-gray-300">|</span>
            <a href="my-listings.php" class="text-gray-500 hover:text-blue-600">My Listings</a>
            <a href="messages.php" class="text-blue-600 font-semibold">Messages</a>
        </div>
        <a href="../logout.php" class="text-red-500">Logout</a>
    </nav>

    <div class="container mx-auto px-4 max-w-4xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Inquiry Inbox</h1>

        <div class="space-y-4">
            <?php if(count($messages) > 0): ?>
                <?php foreach($messages as $msg): ?>
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded">
                                For: <?php echo htmlspecialchars($msg['listing_title']); ?>
                            </span>
                            <h3 class="font-bold text-gray-800 mt-2"><?php echo htmlspecialchars($msg['sender_name']); ?></h3>
                            <a href="mailto:<?php echo htmlspecialchars($msg['sender_email']); ?>" class="text-sm text-gray-500 hover:text-blue-500">
                                <i class="fas fa-envelope mr-1"></i> <?php echo htmlspecialchars($msg['sender_email']); ?>
                            </a>
                        </div>
                        <span class="text-xs text-gray-400"><?php echo date('M d, H:i', strtotime($msg['created_at'])); ?></span>
                    </div>
                    <div class="mt-3 text-gray-600 bg-gray-50 p-3 rounded text-sm">
                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-10 text-gray-500">No messages yet.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>