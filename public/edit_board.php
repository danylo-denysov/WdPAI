<?php
session_start();

use App\Database;
use App\BoardRepository;
use App\Board;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BoardRepository.php';
require_once __DIR__ . '/../src/Board.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

$boardId = $_GET['id'] ?? null;
if (!$boardId) {
    die("Brak ID tablicy (board).");
}

$db = Database::getConnection();
$boardRepo = new BoardRepository($db);

$board = $boardRepo->findById((int)$boardId);
if (!$board) {
    die("Nie znaleziono tablicy o ID $boardId");
}

if ($board->getOwnerId() !== $userId) {
    die("Brak uprawnień do edycji nazwy tej tablicy.");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTitle = $_POST['title'] ?? '';
    if (!empty($newTitle)) {
        $board->setTitle($newTitle);
        $updated = $boardRepo->update($board);
        if ($updated) {
            header("Location: board_view.php?id=$boardId");
            exit;
        } else {
            $message = "Nie udało się zaktualizować nazwy tablicy.";
        }
    } else {
        $message = "Nazwa tablicy nie może być pusta.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edytuj nazwę tablicy</title>
    <link rel="stylesheet" href="editing.css">
</head>
<body>
<div class="main-content">
    <h1>Edytuj nazwę tablicy</h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="edit_board.php?id=<?php echo $boardId; ?>" method="post">
        <label>
            Nowa nazwa:
            <input type="text" name="title" value="<?php echo htmlspecialchars($board->getTitle()); ?>" required>
        </label>
        <button type="submit">Zapisz</button>
    </form>
    <p>
        <button onclick="window.location.href='board_view.php?id=<?php echo $boardId; ?>'">Powrót do tablicy</button>
    </p>
</div>
</body>
</html>

