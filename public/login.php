<?php

use App\Database;
use App\UserRepository;
use App\LoginController;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/UserRepository.php';
require_once __DIR__ . '/../src/LoginController.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $db = Database::getConnection();
    $userRepository = new UserRepository($db);
    $loginController = new LoginController($userRepository);

    $success = $loginController->login($email, $password);

    if ($success) {
        header('Location: home.php');
        exit;
    } else {
        $message = 'Niepoprawny email lub hasło.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="Stylesheet" type="text/css" href="login.css" />
</head>
<body>

<div class = "main">
        <?php if (!empty($message)): ?>
            <p style="color:red;"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post" class="right">
            <div>
                <label>
                    <input class ="input" type="text" placeholder="email@example.com" name="email" required>
                </label>
            </div>
            <div>
                <label>
                    <input class ="input" type="password" placeholder="password" name="password" required>
                </label>
            </div>
            <div>
                <label>
                    <input class ="button" type="submit" name="login" value="Log in">
                </label>
            </div>
        </form>

    <div class="left">
        <div class="logo-container">
            <img src="/img/logo.png" alt="Logo" class="logo">
        </div>
    </div>

</body>
</html>
