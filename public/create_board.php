<?php
session_start();

use App\Database;
use App\Board;
use App\BoardRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Board.php';
require_once __DIR__ . '/../src/BoardRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';

    if (!empty($title)) {
        $db = Database::getConnection();
        $boardRepo = new BoardRepository($db);

        $ownerId = (int)$_SESSION['user_id'];
        $board = new Board(null, $title, $ownerId);

        $saved = $boardRepo->save($board);

        if ($saved) {
            header('Location: home.php');
            exit;
        } else {
            $message = 'Nie udało się utworzyć tablicy.';
        }
    } else {
        $message = 'Tytuł nie może być pusty.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Utwórz Nową Tablicę</title>
    <link rel="stylesheet" href="editing.css">
</head>
<body>
<div class="main-content">
    <h1>Utwórz Nową Tablicę</h1>

    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="create_board.php" method="post">
        <label>
            Tytuł Tablicy:
            <input type="text" name="title" required>
        </label>
        <button type="submit">Utwórz</button>
    </form>
</div>
</body>
</html>
