<?php
session_start();

use App\Database;
use App\BoardRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BoardRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId   = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Nieznany';

$db = Database::getConnection();
$boardRepo = new BoardRepository($db);

$boards = $boardRepo->findAllByUser($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moje Tablice</title>
    <link rel="stylesheet" href="css/home.css">
</head>
<body>

<div class="header">
    <div class="header-right">
        <span class="user-info"><?php echo htmlspecialchars($username); ?></span>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Wyloguj się</button>
        </form>

        <button class="logout-btn"
                onclick="if(confirm('Na pewno chcesz usunąć konto?')) { window.location.href='delete_account.php'; }">
            Usuń konto
        </button>
    </div>
</div>

<div class="main-container">
    <h1>Lista tablic</h1>
    <div class="board-container">
        <?php foreach ($boards as $board): ?>
            <a href="board_view.php?id=<?php echo $board->getId(); ?>" class="board-item">
                <?php echo htmlspecialchars($board->getTitle()); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="new-board-btn-container">
        <button onclick="window.location.href='create_board.php'">+ Dodaj Tablicę</button>
    </div>
</div>

</body>
</html>