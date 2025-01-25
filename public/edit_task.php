<?php
session_start();

use App\Database;
use App\TaskRepository;
use App\Task;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Task.php';
require_once __DIR__ . '/../src/TaskRepository.php';
require_once __DIR__ . '/../src/TaskGroupRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$taskId = $_GET['id'] ?? null;
if (!$taskId) {
    die("Brak ID zadania.");
}

$db = Database::getConnection();
$taskRepo = new TaskRepository($db);
$task = $taskRepo->findById((int)$taskId);
if (!$task) {
    die("Nie znaleziono zadania o ID $taskId");
}

use App\TaskGroupRepository;
$groupRepo = new TaskGroupRepository($db);

$taskGroup = $groupRepo->findById($task->getGroupId());
$boardId = $taskGroup->getBoardId();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    // $isDone = isset($_POST['is_done']); // zrobic pozniej?

    if (!empty($title)) {
        $task->setTitle($title);
        $task->setDescription($description);
        // $task->setDone($isDone); // zrobic pozniej?

        $updated = $taskRepo->update($task);
        if ($updated) {
            header("Location: board_view.php?id=$boardId");
            exit;
        } else {
            $message = "Nie udało się zaktualizować zadania.";
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
    <title>Edycja zadania</title>
    <link rel="stylesheet" href="css/editing.css">
</head>
<body>
<div class="main-content">
    <h1>Edycja zadania (ID: <?php echo $taskId; ?>)</h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="edit_task.php?id=<?php echo $taskId; ?>" method="post">
        <label>
            Tytuł:
            <input type="text" name="title" value="<?php echo htmlspecialchars($task->getTitle()); ?>" required>
        </label>
        <label>
            Opis:
            <textarea name="description"><?php echo htmlspecialchars($task->getDescription()); ?></textarea>
        </label>
        <button type="submit">Zapisz</button>
    </form>
</div>
</body>
</html>