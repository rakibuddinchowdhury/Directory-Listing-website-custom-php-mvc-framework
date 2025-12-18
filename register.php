<?php
require_once 'controllers/Auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Auth();
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // 'user' or 'vendor'

    $result = $auth->register($name, $email, $password, $role);

    if ($result === true) {
        $success = "Registration successful! You can now login.";
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BizFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-[Poppins] h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create an Account</h2>

        <?php if($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">
                <?php echo $success; ?> <a href="login.php" class="font-bold underline">Login here</a>.
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-medium mb-1">Full Name</label>
                <input type="text" name="name" required class="w-full border px-4 py-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-medium mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full border px-4 py-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full border px-4 py-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-600 text-sm font-medium mb-1">I want to...</label>
                <select name="role" class="w-full border px-4 py-2 rounded focus:outline-none focus:border-blue-500 bg-white">
                    <option value="user">Discover Businesses (User)</option>
                    <option value="vendor">List My Business (Vendor)</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">Register</button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a>
        </p>
    </div>

</body>
</html>