<?php 
// 1. Include Config & Header
include 'includes/header.php'; 
require_once 'config/Database.php';

// 2. Fetch Dynamic Data for Search Bar
$db = new Database();
$conn = $db->getConnection();

// Fetch Categories (for dropdown)
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Locations (for dropdown)
$locations = $conn->query("SELECT * FROM locations ORDER BY city ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Featured Listings (Limit 3)
$query = "SELECT l.*, c.name as category_name, loc.city 
          FROM listings l
          LEFT JOIN categories c ON l.category_id = c.id
          LEFT JOIN locations loc ON l.location_id = loc.id
          WHERE l.is_featured = 1 AND l.status = 'active'
          ORDER BY l.created_at DESC LIMIT 3";
$featured = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<header class="relative bg-cover bg-center h-[550px] flex items-center" style="background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1519167758481-83f550bb49b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');">
    <div class="container mx-auto px-4 text-center text-white relative z-10">
        <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">Discover Your City's<br>Best Gems</h1>
        <p class="text-gray-200 text-lg md:text-xl mb-10 max-w-2xl mx-auto">Find great places to eat, stay, shop, and visit from local experts.</p>
        
        <form action="listings.php" method="GET" class="bg-white p-3 rounded-lg shadow-2xl flex flex-col md:flex-row max-w-5xl mx-auto gap-2">
            
            <div class="flex-1 flex items-center border-b md:border-b-0 md:border-r border-gray-200 px-4 py-3">
                <i class="fas fa-search text-gray-400 mr-3 text-lg"></i>
                <input type="text" name="q" placeholder="What are you looking for?" class="w-full focus:outline-none text-gray-700 placeholder-gray-400 font-medium">
            </div>

            <div class="flex-1 flex items-center border-b md:border-b-0 md:border-r border-gray-200 px-4 py-3">
                <i class="fas fa-map-marker-alt text-gray-400 mr-3 text-lg"></i>
                <div class="w-full relative">
                    <select name="location" class="w-full focus:outline-none text-gray-700 bg-transparent font-medium appearance-none cursor-pointer">
                        <option value="">All Locations</option>
                        <?php foreach($locations as $loc): ?>
                            <option value="<?php echo htmlspecialchars($loc['city']); ?>">
                                <?php echo htmlspecialchars($loc['city']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex-1 flex items-center px-4 py-3">
                <i class="fas fa-list text-gray-400 mr-3 text-lg"></i>
                <div class="w-full relative">
                    <select name="category" class="w-full focus:outline-none text-gray-700 bg-transparent font-medium appearance-none cursor-pointer">
                        <option value="">All Categories</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['slug']); ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-10 rounded-md transition duration-300 shadow-md flex items-center justify-center">
                Search
            </button>
        </form>
        
        <div class="mt-6 text-sm text-gray-300">
            <span class="mr-2">Popular:</span>
            <?php 
            $topCats = array_slice($categories, 0, 4);
            foreach($topCats as $tc): 
            ?>
                <a href="listings.php?category=<?php echo $tc['slug']; ?>" class="underline hover:text-white mr-3"><?php echo $tc['name']; ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</header>

<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Browse Categories</h2>
            <div class="w-20 h-1 bg-blue-600 mx-auto"></div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <?php foreach($categories as $cat): ?>
            <a href="listings.php?category=<?php echo $cat['slug']; ?>" class="group block bg-white border border-gray-100 p-8 rounded-xl text-center hover:shadow-xl hover:-translate-y-1 transition duration-300">
                <div class="w-20 h-20 mx-auto bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-3xl mb-6 group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fas fa-<?php echo htmlspecialchars($cat['icon']); ?>"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($cat['name']); ?></h3>
                <span class="text-sm text-gray-500 mt-2 block group-hover:text-blue-600">View Listings <i class="fas fa-arrow-right ml-1 text-xs"></i></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Featured Places</h2>
                <p class="text-gray-500">Hand-picked top rated listings for you.</p>
            </div>
            <a href="listings.php" class="text-blue-600 font-bold hover:underline">View All <i class="fas fa-arrow-right ml-1"></i></a>
        </div>

        <?php if(count($featured) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach($featured as $feat): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-2xl transition duration-300 group">
                    <div class="relative h-56 overflow-hidden">
                        <?php if($feat['image']): ?>
                            <img src="<?php echo $feat['image']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400"><i class="fas fa-image text-4xl"></i></div>
                        <?php endif; ?>
                        
                        <div class="absolute top-4 left-4 bg-yellow-400 text-white text-xs font-bold px-3 py-1 rounded shadow-sm uppercase tracking-wide">
                            <i class="fas fa-crown mr-1"></i> Featured
                        </div>
                        <div class="absolute bottom-4 left-4 bg-white text-gray-800 text-xs font-bold px-3 py-1 rounded shadow-sm">
                            <i class="fas fa-tag mr-1 text-blue-500"></i> <?php echo htmlspecialchars($feat['category_name']); ?>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <div class="text-xs text-gray-500 font-semibold uppercase tracking-wider">
                                <i class="fas fa-map-marker-alt mr-1 text-red-500"></i> <?php echo htmlspecialchars($feat['city']); ?>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-3 group-hover:text-blue-600 transition">
                            <a href="listing-detail.php?slug=<?php echo $feat['slug']; ?>"><?php echo htmlspecialchars($feat['title']); ?></a>
                        </h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($feat['description']); ?>
                        </p>
                        <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                            <a href="listing-detail.php?slug=<?php echo $feat['slug']; ?>" class="text-blue-600 font-semibold text-sm hover:underline">Read More</a>
                            <span class="text-gray-400 text-sm"><i class="far fa-eye"></i> <?php echo $feat['views']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-10 bg-white rounded shadow-sm border border-gray-100">
                <p class="text-gray-500">No featured listings available yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-24 bg-blue-600 relative overflow-hidden">
    <div class="absolute top-0 left-0 -ml-20 -mt-20 w-64 h-64 rounded-full bg-blue-500 opacity-50"></div>
    <div class="absolute bottom-0 right-0 -mr-20 -mb-20 w-80 h-80 rounded-full bg-blue-700 opacity-50"></div>

    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Grow Your Business with Us</h2>
        <p class="text-blue-100 text-xl mb-10 max-w-2xl mx-auto">Join thousands of businesses and reach a wider audience today. Listing your business is simple and effective.</p>
        <div class="flex flex-col md:flex-row justify-center gap-4">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard/add-listing.php" class="bg-white text-blue-600 font-bold py-4 px-10 rounded-full hover:bg-gray-100 transition shadow-xl">Add Your Listing</a>
            <?php else: ?>
                <a href="register.php" class="bg-white text-blue-600 font-bold py-4 px-10 rounded-full hover:bg-gray-100 transition shadow-xl">Get Started Now</a>
            <?php endif; ?>
            <a href="contact.php" class="bg-transparent border-2 border-white text-white font-bold py-4 px-10 rounded-full hover:bg-white hover:text-blue-600 transition">Contact Support</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>