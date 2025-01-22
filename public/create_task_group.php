<?php
session_start();

use App\Database;
use App\TaskGroup;
use App\TaskGroupRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/TaskGroup.php';
require_once __DIR__ . '/../src/TaskGroupRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$boardId = $_GET['board_id'] ?? null;
if (!$boardId) {
    die("Brak board_id w parametrze.");
}


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';

    if (!empty($title)) {
        $db = Database::getConnection();
        $groupRepo = new TaskGroupRepository($db);

        $group = new TaskGroup(null, (int)$boardId, $title, 0);
        $saved = $groupRepo->save($group);

        if ($saved) {
            header("Location: board_view.php?id={$boardId}");
            exit;
        } else {
            $message = "Nie udało się utworzyć grupy.";
        }
    } else {
        $message = "Tytuł nie może być pusty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Utwórz Nową Grupę</title>
    <link rel="stylesheet" href="editing.css">
</head>
<body>
<div class="main-content">
    <h1>Utwórz Nową Grupę</h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="create_task_group.php?board_id=<?php echo $boardId; ?>" method="post">
        <label>
            Tytuł grupy:
            <input type="text" name="title" required>
        </label>
        <button type="submit">Utwórz</button>
    </form>
</div>
</body>
</html>
