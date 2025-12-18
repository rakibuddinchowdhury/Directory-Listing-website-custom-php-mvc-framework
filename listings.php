<?php 
include 'includes/header.php'; 
require_once 'controllers/Listing.php';

// Initialize Controller
$listingObj = new Listing();

// 1. Get Filters & Pagination Params
$filters = [
    'search' => $_GET['q'] ?? '',
    'category' => $_GET['category'] ?? '',
    'location' => $_GET['location'] ?? ''
];

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Items per page

// 2. Fetch Data & Count
$listings = $listingObj->getListings($filters, $page, $limit);
$total_records = $listingObj->getTotalCount($filters);
$total_pages = ceil($total_records / $limit);

// Fetch Sidebar Data
$db = new Database();
$conn = $db->getConnection();
$cats = $conn->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$locs = $conn->query("SELECT * FROM locations ORDER BY city ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        
        <aside class="w-full md:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 sticky top-24">
                <h3 class="font-bold text-lg mb-4 text-dark border-b pb-2">Filter Listings</h3>
                <form action="listings.php" method="GET">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Search</label>
                        <input type="text" name="q" value="<?php echo htmlspecialchars($filters['search']); ?>" placeholder="Keywords..." class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Category</label>
                        <select name="category" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary text-sm bg-white">
                            <option value="">All Categories</option>
                            <?php foreach($cats as $cat): ?>
                                <option value="<?php echo $cat['slug']; ?>" <?php echo $filters['category'] == $cat['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Location</label>
                        <select name="location" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:border-primary text-sm bg-white">
                            <option value="">All Locations</option>
                            <?php foreach($locs as $loc): ?>
                                <option value="<?php echo $loc['city']; ?>" <?php echo $filters['location'] == $loc['city'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($loc['city']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">Apply Filters</button>
                    <?php if(!empty($filters['search']) || !empty($filters['category']) || !empty($filters['location'])): ?>
                        <a href="listings.php" class="block text-center mt-3 text-sm text-gray-500 hover:text-red-500">Reset Filters</a>
                    <?php endif; ?>
                </form>
            </div>
        </aside>

        <main class="w-full md:w-3/4">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-dark"><?php echo $total_records; ?> Listings Found</h1>
                <span class="text-sm text-gray-500">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
            </div>

            <?php if (count($listings) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <?php foreach ($listings as $listing): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col group h-full">
                            <div class="relative h-56 bg-gray-200 overflow-hidden">
                                <?php if(!empty($listing['image'])): ?>
                                    <img src="<?php echo $listing['image']; ?>" alt="<?php echo htmlspecialchars($listing['title']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100"><i class="fas fa-image text-4xl"></i></div>
                                <?php endif; ?>
                                <?php if($listing['is_featured']): ?>
                                    <div class="absolute top-3 left-3 bg-yellow-400 text-white text-xs font-bold px-3 py-1 rounded shadow-sm uppercase tracking-wide"><i class="fas fa-crown mr-1"></i> Featured</div>
                                <?php endif; ?>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase tracking-wide"><?php echo htmlspecialchars($listing['category_name']); ?></span>
                                    <span class="text-xs text-gray-400"><i class="far fa-eye mr-1"></i> <?php echo $listing['views']; ?></span>
                                </div>
                                <h3 class="text-xl font-bold text-dark mb-2 hover:text-blue-600 transition">
                                    <a href="listing-detail.php?slug=<?php echo $listing['slug']; ?>"><?php echo htmlspecialchars($listing['title']); ?></a>
                                </h3>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars(substr($listing['description'], 0, 100)) . '...'; ?></p>
                                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-2 text-red-500"></i> <?php echo htmlspecialchars($listing['city'] ?? $listing['address']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                    <div class="mt-10 flex justify-center">
                        <nav class="flex space-x-2">
                            <?php 
                                // Build query string for filters
                                $queryStr = http_build_query(array_merge($filters)); 
                            ?>
                            
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&<?php echo $queryStr; ?>" class="px-4 py-2 border rounded hover:bg-gray-50 text-gray-700 transition">Prev</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&<?php echo $queryStr; ?>" class="px-4 py-2 border rounded transition <?php echo ($i == $page) ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-50 text-gray-700'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&<?php echo $queryStr; ?>" class="px-4 py-2 border rounded hover:bg-gray-50 text-gray-700 transition">Next</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-20 bg-white rounded-lg border border-dashed border-gray-300">
                    <i class="fas fa-search text-6xl text-gray-200 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-500">No listings found</h3>
                    <p class="text-gray-400">Try adjusting your search filters.</p>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>