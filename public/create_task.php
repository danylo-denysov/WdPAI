<?php
session_start();

use App\Database;
use App\Task;
use App\TaskRepository;
use App\TaskGroupRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Task.php';
require_once __DIR__ . '/../src/TaskRepository.php';
require_once __DIR__ . '/../src/TaskGroupRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$groupId = $_GET['group_id'] ?? null;
if (!$groupId) {
    die("Brak group_id w parametrze.");
}

$db = Database::getConnection();
$taskRepo = new TaskRepository($db);
$groupRepo = new TaskGroupRepository($db);

$taskGroup = $groupRepo->findById((int)$groupId);
if (!$taskGroup) {
    die("Nie znaleziono grupy o id: $groupId");
}
$boardId = $taskGroup->getBoardId();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    if (!empty($title)) {
        $task = new Task(
            null,
            (int)$groupId,
            null,
            $title,
            $description,
            0,
            null,
            null,
            false
        );

        $saved = $taskRepo->save($task);

        if ($saved) {
            header("Location: board_view.php?id=$boardId");
            exit;
        } else {
            $message = "Nie udało się utworzyć zadania.";
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
    <title>Utwórz zadanie</title>
    <link rel="stylesheet" href="editing.css">
</head>
<body>
<div class="main-content">
    <h1>Utwórz nowe zadanie</h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="create_task.php?group_id=<?php echo $groupId; ?>" method="post">
        <label>
            Tytuł zadania:
            <input type="text" name="title" required>
        </label>
        <label>
            Opis (opcjonalnie):
            <textarea name="description"></textarea>
        </label>
        <button type="submit">Dodaj zadanie</button>
    </form>
</div>
</body>
</html>