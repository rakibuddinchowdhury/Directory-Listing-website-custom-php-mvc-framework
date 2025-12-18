<?php 
include 'includes/header.php'; 
require_once 'config/Database.php';

$db = new Database();
$conn = $db->getConnection();
$settings = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<div class="bg-blue-600 py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold text-white mb-2">Contact Us</h1>
        <p class="text-blue-100">We'd love to hear from you.</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        
        <div>
            <h2 class="text-2xl font-bold text-dark mb-4">About <?php echo htmlspecialchars($settings['site_name']); ?></h2>
            <div class="text-gray-600 leading-relaxed mb-8">
                <?php echo nl2br(htmlspecialchars($settings['about_text'])); ?>
            </div>

            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                <h3 class="font-bold text-lg mb-4 text-dark">Get in Touch</h3>
                <div class="flex items-center mb-3 text-gray-600">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 text-primary">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <?php echo htmlspecialchars($settings['site_email']); ?>
                </div>
                <div class="flex items-center text-gray-600">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 text-primary">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    123 Directory Lane, Tech City
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-lg shadow-lg border border-gray-100">
            <h2 class="text-2xl font-bold text-dark mb-6">Send a Message</h2>
            <form>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:border-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea rows="4" class="w-full border border-gray-300 rounded p-2 focus:outline-none focus:border-blue-500"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700 transition">Send Message</button>
            </form>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>