<?php
require_once '../controllers/Auth.php';
require_once '../config/Database.php';

$auth = new Auth();
$auth->requireAdmin();

$db = new Database();
$conn = $db->getConnection();

// Handle Approval
if(isset($_GET['approve_id']) && isset($_GET['listing_id'])) {
    $payId = $_GET['approve_id'];
    $listId = $_GET['listing_id'];

    // 1. Mark Payment Completed
    $stmt = $conn->prepare("UPDATE payments SET status = 'completed' WHERE id = :pid");
    $stmt->bindParam(':pid', $payId);
    $stmt->execute();

    // 2. Mark Listing Featured
    $stmt2 = $conn->prepare("UPDATE listings SET is_featured = 1 WHERE id = :lid");
    $stmt2->bindParam(':lid', $listId);
    $stmt2->execute();

    header("Location: payments.php?msg=success"); exit;
}

// Fetch Pending Payments
$query = "SELECT p.*, l.title as listing_title, u.name as vendor_name 
          FROM payments p
          JOIN listings l ON p.listing_id = l.id
          JOIN users u ON p.user_id = u.id
          ORDER BY p.created_at DESC";
$payments = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans flex">

    <aside class="w-64 bg-gray-900 text-white min-h-screen hidden md:block">
        <div class="p-6 text-xl font-bold border-b border-gray-700">BizFinder Admin</div>
        <nav class="mt-6">
            <a href="index.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-tachometer-alt mr-3"></i> Dashboard</a>
            <a href="listings.php" class="block py-3 px-6 text-gray-400 hover:text-white"><i class="fas fa-list mr-3"></i> Listings</a>
            <a href="payments.php" class="block py-3 px-6 bg-blue-600 text-white"><i class="fas fa-dollar-sign mr-3"></i> Payments</a>
            <a href="../logout.php" class="block py-3 px-6 text-red-400 mt-10"><i class="fas fa-sign-out-alt mr-3"></i> Logout</a>
        </nav>
    </aside>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Payment Requests</h1>

        <?php if(isset($_GET['msg'])): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">Payment approved & Listing featured!</div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 text-gray-600 uppercase text-sm font-semibold">
                    <tr>
                        <th class="p-4 border-b">Ref ID</th>
                        <th class="p-4 border-b">Vendor</th>
                        <th class="p-4 border-b">Listing</th>
                        <th class="p-4 border-b">Amount</th>
                        <th class="p-4 border-b">Status</th>
                        <th class="p-4 border-b text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach($payments as $pay): ?>
                    <tr class="hover:bg-gray-50 border-b last:border-0">
                        <td class="p-4 font-mono text-xs"><?php echo $pay['transaction_id']; ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($pay['vendor_name']); ?></td>
                        <td class="p-4"><?php echo htmlspecialchars($pay['listing_title']); ?></td>
                        <td class="p-4 font-bold text-green-600">$<?php echo $pay['amount']; ?></td>
                        <td class="p-4">
                            <?php if($pay['status'] == 'completed'): ?>
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">Paid</span>
                            <?php else: ?>
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded font-bold">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-right">
                            <?php if($pay['status'] == 'pending'): ?>
                                <a href="payments.php?approve_id=<?php echo $pay['id']; ?>&listing_id=<?php echo $pay['listing_id']; ?>" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition shadow">Approve</a>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm"><i class="fas fa-check"></i> Done</span>
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