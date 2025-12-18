<?php
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $host = $_POST['host'];
    $db_name = $_POST['db_name'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];

    try {
        // 1. Connect to MySQL Server (without DB)
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 2. Create Database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`");

        // 3. Create Tables
        $sql = "
        -- Users Table
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'vendor', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Categories Table
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            icon VARCHAR(50) DEFAULT 'folder',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Locations Table
        CREATE TABLE IF NOT EXISTS locations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            city VARCHAR(100) NOT NULL,
            state VARCHAR(100) NOT NULL,
            country VARCHAR(100) DEFAULT 'USA'
        );

        -- Listings Table
        CREATE TABLE IF NOT EXISTS listings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            category_id INT,
            location_id INT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(191) UNIQUE NOT NULL,
            description TEXT,
            address VARCHAR(255),
            phone VARCHAR(20),
            email VARCHAR(100),
            website VARCHAR(255),
            image VARCHAR(255),
            is_featured BOOLEAN DEFAULT 0,
            status ENUM('pending', 'active', 'rejected') DEFAULT 'pending',
            views INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
        );

        -- Reviews Table
        CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            listing_id INT,
            user_id INT,
            rating INT CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        -- Favorites Table
        CREATE TABLE IF NOT EXISTS favorites (
            user_id INT,
            listing_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id, listing_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
        );

        -- Messages Table
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            listing_id INT,
            sender_name VARCHAR(100) NOT NULL,
            sender_email VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
        );

        -- Payments Table
        CREATE TABLE IF NOT EXISTS payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            listing_id INT,
            amount DECIMAL(10, 2),
            transaction_id VARCHAR(100),
            status ENUM('pending', 'completed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
        );

        -- Settings Table
        CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            site_name VARCHAR(100) DEFAULT 'BizFinder',
            site_email VARCHAR(100) DEFAULT 'admin@bizfinder.com',
            site_description TEXT,
            about_text TEXT,
            footer_text VARCHAR(255) DEFAULT '© 2025 BizFinder. All Rights Reserved.',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        ";
        $pdo->exec($sql);

        // 4. Insert Default Data (UPDATED CREDENTIALS)
        // Admin User (Password: 12345678)
        $adminEmail = 'admin@gmail.com';
        $adminPass = password_hash('12345678', PASSWORD_BCRYPT);
        
        $checkAdmin = $pdo->prepare("SELECT * FROM users WHERE email=:email");
        $checkAdmin->execute([':email' => $adminEmail]);
        
        if ($checkAdmin->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES ('Super Admin', :email, :pass, 'admin')");
            $stmt->execute([':email' => $adminEmail, ':pass' => $adminPass]);
        }

        // Default Categories
        $pdo->exec("INSERT IGNORE INTO categories (name, slug, icon) VALUES 
            ('Food & Dining', 'food-dining', 'utensils'),
            ('Real Estate', 'real-estate', 'home'),
            ('Automotive', 'automotive', 'car'),
            ('Shopping', 'shopping', 'shopping-bag'),
            ('Travel', 'travel', 'plane')
        ");

        // Default Locations
        $pdo->exec("INSERT IGNORE INTO locations (city, state, country) VALUES 
            ('New York', 'NY', 'USA'),
            ('Los Angeles', 'CA', 'USA'),
            ('London', 'UK', 'UK'),
            ('Dubai', 'UAE', 'UAE')
        ");

        // Default Settings
        $pdo->exec("INSERT IGNORE INTO settings (id, site_name, about_text) VALUES (1, 'BizFinder', 'Welcome to the best directory script.')");

        // 5. Create Config File
        $configFileContent = "<?php
class Database {
    private \$host = \"$host\";
    private \$db_name = \"$db_name\";
    private \$username = \"$user\";
    private \$password = \"$pass\";
    public \$conn;

    public function getConnection() {
        \$this->conn = null;
        try {
            \$this->conn = new PDO(\"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name, \$this->username, \$this->password);
            \$this->conn->exec(\"set names utf8\");
            \$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException \$exception) {
            echo \"Connection error: \" . \$exception->getMessage();
        }
        return \$this->conn;
    }
}
?>";
        file_put_contents('config/Database.php', $configFileContent);

        $msg = "<div class='bg-green-100 text-green-700 p-4 rounded mb-4 font-bold'>
                    Installation Successful! <br>
                    <a href='login.php' class='underline'>Click here to Login</a><br>
                    <span class='text-sm font-normal'>Email: admin@gmail.com | Pass: 12345678</span>
                </div>";

    } catch (PDOException $e) {
        $msg = "<div class='bg-red-100 text-red-700 p-4 rounded mb-4'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Install BizFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-[Poppins] flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-blue-600 mb-2">BizFinder Installer</h1>
        <p class="text-gray-500 text-center text-sm mb-6">Database Setup Wizard</p>

        <?php echo $msg; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Database Host</label>
                <input type="text" name="host" value="localhost" required class="w-full border p-3 rounded focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Database Name</label>
                <input type="text" name="db_name" value="directory_db" required class="w-full border p-3 rounded focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Database User</label>
                <input type="text" name="user" value="root" required class="w-full border p-3 rounded focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Database Password</label>
                <input type="text" name="pass" class="w-full border p-3 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded hover:bg-blue-700 transition">Run Installer</button>
        </form>
    </div>

</body>
</html>