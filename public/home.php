<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

use App\Database;
use App\BoardRepository;
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BoardRepository.php';

$userId = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Nieznany';
$db = Database::getConnection();
$boardRepo = new BoardRepository($db);
$boards = $boardRepo->findAllByUser($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moja Tablica - Task Management</title>
    <link rel="Stylesheet" type="text/css" href="home.css" />
</head>
<body>

<div class="layout-container">

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Moje Tablice</h2>
        </div>
        <div class="board-list">
            <ul>
                <?php foreach ($boards as $board): ?>
                    <li>
                        <a href="board_view.php?id=<?php echo $board->getId(); ?>">
                            <?php echo htmlspecialchars($board->getTitle()); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div style="border-top: 1px solid #444; padding: 1rem; margin-left:40px;">
            <button class="create-board-btn" onclick="window.location.href='create_board.php'">
                + Nowa Tablica
            </button>
        </div>
    </div>

    <div class="main-content">
        <h1>Witaj w panelu z tablicami!</h1>
        <p>Tutaj możesz wybrać jedną z dostępnych tablic z menu po lewej stronie lub utworzyć nową.</p>
    </div>

    <div class="user-panel">
        <p>Zalogowano jako:</p>
        <strong><?php echo htmlspecialchars($username); ?></strong>
        <form action="logout.php" method="post">
            <button type="submit">Wyloguj</button>
        </form>
    </div>

</div>
</body>
</html>
