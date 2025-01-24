<?php
session_start();

use App\Database;
use App\TaskGroupRepository;
use App\TaskGroup;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/TaskGroup.php';
require_once __DIR__ . '/../src/TaskGroupRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Brak ID kolumny.");
}

$db = Database::getConnection();
$groupRepo = new TaskGroupRepository($db);

$taskGroup = $groupRepo->findById((int)$id);
if (!$taskGroup) {
    die("Nie znaleziono kolumny o ID $id");
}

$boardId = $taskGroup->getBoardId();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';

    if (!empty($title)) {
        $taskGroup->setTitle($title);
        $updated = $groupRepo->update($taskGroup);

        if ($updated) {
            header("Location: board_view.php?id=$boardId");
            exit;
        } else {
            $message = "Nie udało się zaktualizować kolumny.";
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
    <title>Edytuj kolumnę</title>
    <link rel="stylesheet" href="../css/editing.css">
</head>
<body>
<div class="main-content">
    <h1>Edytuj kolumnę</h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="edit_task_group.php?id=<?php echo $id; ?>" method="post">
        <label>
            Nowa nazwa grupy:
            <input type="text" name="title" value="<?php echo htmlspecialchars($taskGroup->getTitle()); ?>" required>
        </label>
        <button type="submit">Zapisz</button>
    </form>
</div>
</body>
</html>