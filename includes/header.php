<?php 
// 1. Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BizFinder - Local Directory</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        primary: '#1E88E5',
                        secondary: '#43A047',
                        accent: '#FFC107',
                        dark: '#212121',
                        light: '#F5F7FA',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-light text-dark font-sans flex flex-col min-h-screen">

<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold text-primary flex items-center">
            <i class="fas fa-map-marker-alt mr-2 text-secondary"></i> BizFinder
        </a>

        <div class="hidden md:flex space-x-6 items-center">
            <a href="index.php" class="hover:text-primary transition font-medium">Home</a>
            <a href="listings.php" class="hover:text-primary transition font-medium">Explore</a>
            <a href="contact.php" class="hover:text-primary transition font-medium">Contact</a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="relative">
                    <button id="user-menu-btn" class="flex items-center text-gray-700 hover:text-primary font-medium focus:outline-none">
                        <div class="w-8 h-8 bg-blue-100 text-primary rounded-full flex items-center justify-center mr-2">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        <i class="fas fa-chevron-down text-xs ml-2 text-gray-400"></i>
                    </button>

                    <div id="user-menu-dropdown" class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-lg shadow-xl hidden z-50">
                        <div class="py-2">
                            <?php if($_SESSION['user_role'] == 'admin'): ?>
                                <a href="admin/index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">
                                    <i class="fas fa-tachometer-alt mr-2 w-5 text-center"></i> Admin Panel
                                </a>
                            <?php elseif($_SESSION['user_role'] == 'vendor'): ?>
                                <a href="dashboard/my-listings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">
                                    <i class="fas fa-store mr-2 w-5 text-center"></i> Vendor Dashboard
                                </a>
                                <a href="dashboard/messages.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">
                                    <i class="fas fa-envelope mr-2 w-5 text-center"></i> Messages
                                </a>
                            <?php else: ?>
                                <a href="user/saved-listings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">
                                    <i class="fas fa-heart mr-2 w-5 text-center"></i> Saved Listings
                                </a>
                            <?php endif; ?>
                            
                            <a href="user/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary">
                                <i class="fas fa-user-circle mr-2 w-5 text-center"></i> My Profile
                            </a>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <a href="logout.php" class="block px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2 w-5 text-center"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <?php if($_SESSION['user_role'] == 'vendor' || $_SESSION['user_role'] == 'admin'): ?>
                    <a href="dashboard/add-listing.php" class="bg-primary text-white px-5 py-2 rounded-full hover:bg-blue-600 transition shadow-lg shadow-blue-500/30 font-medium">
                        <i class="fas fa-plus-circle mr-1"></i> Add Listing
                    </a>
                <?php endif; ?>

            <?php else: ?>
                <a href="login.php" class="text-gray-600 hover:text-primary font-medium">Login</a>
                <a href="register.php" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-blue-600 transition shadow-md font-medium">
                    Register
                </a>
            <?php endif; ?>
        </div>
        
        <button id="mobile-menu-btn" class="md:hidden text-2xl text-dark focus:outline-none">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 p-4">
        <a href="index.php" class="block py-2 text-gray-700 font-medium">Home</a>
        <a href="listings.php" class="block py-2 text-gray-700 font-medium">Explore</a>
        <a href="contact.php" class="block py-2 text-gray-700 font-medium">Contact</a>
        <div class="border-t border-gray-100 my-2"></div>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="user/profile.php" class="block py-2 text-gray-700 font-medium">My Profile</a>
            <a href="logout.php" class="block py-2 text-red-500 font-medium">Logout</a>
        <?php else: ?>
            <a href="login.php" class="block py-2 text-gray-700 font-medium">Login</a>
            <a href="register.php" class="block py-2 text-primary font-bold">Register</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    // 1. Mobile Menu Toggle
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileBtn.addEventListener('click', function() {
        mobileMenu.classList.toggle('hidden');
    });

    // 2. User Dropdown Toggle (Desktop)
    const userBtn = document.getElementById('user-menu-btn');
    const userDropdown = document.getElementById('user-menu-dropdown');

    if(userBtn && userDropdown) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent click from bubbling to window
            userDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    }
</script>