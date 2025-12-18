<?php
// Fetch Settings if not already fetched
if(!isset($conn)) {
    require_once __DIR__ . '/../config/Database.php';
    $db = new Database();
    $conn = $db->getConnection();
}
$settings = $conn->query("SELECT * FROM settings WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<footer class="bg-dark text-white py-10 mt-auto">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <h3 class="text-xl font-bold mb-4 text-primary">
                <?php echo htmlspecialchars($settings['site_name'] ?? 'BizFinder'); ?>
            </h3>
            <p class="text-gray-400 text-sm">
                <?php echo htmlspecialchars(substr($settings['about_text'] ?? 'The best local business directory.', 0, 100)) . '...'; ?>
            </p>
        </div>
        <div>
            <h4 class="font-semibold mb-4 text-accent">Quick Links</h4>
            <ul class="text-gray-400 space-y-2 text-sm">
                <li><a href="index.php" class="hover:text-white">Home</a></li>
                <li><a href="listings.php" class="hover:text-white">Explore</a></li>
                <li><a href="contact.php" class="hover:text-white">Contact Us</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold mb-4 text-accent">Legal</h4>
            <ul class="text-gray-400 space-y-2 text-sm">
                <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-white">Terms of Service</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold mb-4 text-accent">Contact</h4>
            <ul class="text-gray-400 space-y-2 text-sm">
                <li><i class="fas fa-envelope mr-2"></i> <?php echo htmlspecialchars($settings['site_email'] ?? 'info@example.com'); ?></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-500 text-sm">
        <?php echo htmlspecialchars($settings['footer_text'] ?? '© 2025 BizFinder. All rights reserved.'); ?>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Simple mobile menu toggle
    $('button.md\\:hidden').click(function(){
        $('.md\\:flex').toggleClass('hidden flex flex-col absolute top-16 left-0 w-full bg-white p-4 shadow-lg');
    });
</script>
</body>
</html>