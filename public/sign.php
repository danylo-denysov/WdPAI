<?php

use App\Database;
use App\UserRepository;
use App\RegisterController;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/User.php';
require_once __DIR__ . '/../src/UserRepository.php';
require_once __DIR__ . '/../src/RegisterController.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordRepeat = $_POST['password_repeat'] ?? '';

    $db = Database::getConnection();
    $userRepository = new UserRepository($db);
    $registerController = new RegisterController($userRepository);

    $success = $registerController->register($email, $username, $password, $passwordRepeat);

    if ($success) {
        header('Location: login.php');
        exit;
    } else {
        $message = 'Rejestracja nieudana. Sprawdź dane i spróbuj ponownie.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="Stylesheet" type="text/css" href="sign.css" />
</head>
<body>

<div class = "main">
    <?php if (!empty($message)): ?>
        <p style="color:red;"><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="sign.php" method="POST" class="right">
        <div>
            <label>
                <input class ="input" type="text" placeholder="email@example.com" name="email" required>
            </label>
        </div>
        <div>
            <label>
                <input class ="input" type="text" placeholder="username" name="username" required>
            </label>
        </div>
        <div>
            <label>
                <input class ="input" type="password" placeholder="password" name="password" required>
            </label>
        </div>
        <div>
            <label>
                <input class ="input" type="password" placeholder="repeat password" name="password_repeat" required>
            </label>
        </div>
        <div>
            <label>
                <input class ="button" type="submit" value="Sign in">
            </label>
        </div>
    </form>
    <div class="left">
        <div class="logo-container">
            <img src="/img/logo.png" alt="Logo" class="logo">
        </div>
    </div>
</div>


</body>
</html>