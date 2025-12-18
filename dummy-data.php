<?php
require_once 'config/Database.php';

$db = new Database();
$conn = $db->getConnection();

echo "<h1>Generating Dummy Data...</h1>";

// 1. Clear existing dummy data (Optional - keeps your manual testing safe)
// $conn->exec("TRUNCATE TABLE listings"); 

// 2. Define Real Unsplash Images by Category
// We use direct URLs so you don't need to download/upload 50 files.
$images = [
    'food-dining' => [
        'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1559339352-11d035aa65de?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1544148103-0773bf10d330?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80'
    ],
    'real-estate' => [
        'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1600596542815-6ad4c727dd2d?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80'
    ],
    'automotive' => [
        'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1503376763036-066120622c74?auto=format&fit=crop&w=800&q=80'
    ],
    'shopping' => [
        'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1472851294608-4155f2118c67?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=800&q=80'
    ],
    'travel' => [
        'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80'
    ]
];

// 3. Sample Titles & Companies
$adjectives = ['Luxury', 'Modern', 'Vintage', 'Cozy', 'Exclusive', 'Urban', 'Family', 'Premium', 'Affordable', 'Best'];
$nouns = ['Cafe', 'Bistro', 'Motors', 'Apartments', 'Villas', 'Resort', 'Boutique', 'Garage', 'Studio', 'Agency'];

// 4. Fetch IDs from DB to ensure validity
$users = $conn->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);
$cats = $conn->query("SELECT id, slug FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$locs = $conn->query("SELECT id FROM locations")->fetchAll(PDO::FETCH_COLUMN);

if(empty($users)) die("Error: No users found. Please register at least one user first.");
if(empty($cats)) die("Error: No categories found.");
if(empty($locs)) die("Error: No locations found.");

// 5. Generate 25 Listings
for ($i = 0; $i < 25; $i++) {
    // Pick Random Data
    $user_id = $users[array_rand($users)];
    $cat = $cats[array_rand($cats)]; // Contains id and slug
    $loc_id = $locs[array_rand($locs)];
    
    // Generate Title
    $title = $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)] . ' ' . rand(100, 999);
    $slug = strtolower(str_replace(' ', '-', $title)) . '-' . uniqid();
    
    // Pick Image based on Category Slug (fallback to 'travel' if key missing)
    $catSlug = $cat['slug'];
    $imgList = isset($images[$catSlug]) ? $images[$catSlug] : $images['travel'];
    $image = $imgList[array_rand($imgList)];

    $desc = "Experience the best service in town. We offer top-notch quality and customer satisfaction. Open daily from 9 AM to 9 PM. Contact us for more details.";
    $phone = "+1 555 01" . rand(10, 99);
    $views = rand(50, 5000);
    $featured = (rand(1, 10) > 8) ? 1 : 0; // 20% chance of being featured
    $status = 'active';

    // Insert
    $sql = "INSERT INTO listings (user_id, category_id, location_id, title, slug, description, address, phone, email, website, image, is_featured, status, views) 
            VALUES (:uid, :cid, :lid, :title, :slug, :desc, '123 Fake St, City Center', :phone, 'contact@demo.com', 'https://example.com', :img, :feat, :status, :views)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':uid' => $user_id,
        ':cid' => $cat['id'],
        ':lid' => $loc_id,
        ':title' => $title,
        ':slug' => $slug,
        ':desc' => $desc,
        ':phone' => $phone,
        ':img' => $image,
        ':feat' => $featured,
        ':status' => $status,
        ':views' => $views
    ]);

    echo "<div style='color: green; margin-bottom: 5px;'>Created: $title</div>";
}

echo "<h2>Done! Added 25 Listings. <a href='index.php'>Go Home</a></h2>";
?>