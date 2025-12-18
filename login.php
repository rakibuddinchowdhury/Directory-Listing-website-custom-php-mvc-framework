 <?php
require_once 'controllers/Auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $auth = new Auth();
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($auth->login($email, $password)) {
        // Redirect based on role
        if ($_SESSION['user_role'] == 'admin') {
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BizFinder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 font-[Poppins] h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Welcome Back</h2>

        <?php if($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-600 text-sm font-medium mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full border px-4 py-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-gray-600 text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full border px-4 py-2 rounded focus:outline-none focus:border-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">Login</button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-4">
            Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a>
        </p>
        <p class="text-center text-sm mt-2">
            <a href="index.php" class="text-gray-400 hover:text-gray-600">Back to Home</a>
        </p>
    </div>

</body>
</html>
