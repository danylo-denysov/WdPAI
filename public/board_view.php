<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Nieznany';

use App\Database;
use App\BoardRepository;
use App\TaskGroupRepository;
use App\TaskRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BoardRepository.php';
require_once __DIR__ . '/../src/TaskGroupRepository.php';
require_once __DIR__ . '/../src/TaskRepository.php';

$boardId = $_GET['id'] ?? null;
if (!$boardId) {
    die("Brak ID tablicy (board).");
}

$db = Database::getConnection();
$boardRepo = new BoardRepository($db);
$groupRepo = new TaskGroupRepository($db);
$taskRepo  = new TaskRepository($db);

$board = $boardRepo->findById((int)$boardId);
if (!$board) {
    die("Tablica nie istnieje (id=$boardId).");
}

$userRole = null;
if ($board->getOwnerId() === $userId) {
    $userRole = 'owner';
} else {
    $userRole = $boardRepo->getUserRole($board->getId(), $userId);
    if (!$userRole) {
        die("Brak dostępu do tej tablicy (nie przypisano Cię do board_users).");
    }
}

$canEdit = ($userRole === 'owner' || $userRole === 'editor');

$groups = $groupRepo->findAllByBoard($board->getId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($board->getTitle()); ?></title>
    <link rel="Stylesheet" type="text/css" href="home.css" />
    <script>
        const userRole = '<?php echo $userRole; ?>';
    </script>
    <script src="dragdrop.js"></script>
</head>
<body>
<div class="layout-container">

    <div class="sidebar">
        <div style="padding: 1rem; margin-left:30px;">
            <button class="create-board-btn" onclick="window.location.href='home.php'">
                Wróć do listy tablic
            </button>
        </div>
    </div>

    <div class="main-content">
        <div class="board-header">
            <div class="board-title">
                <h1><?php echo htmlspecialchars($board->getTitle()); ?></h1>
                <span class="board-subtitle">Zarządzaj zadaniami, użytkownikami itp.</span>
            </div>
            <div class="board-actions">
                <!-- Przyciski tylko dla Ownera -->
                <?php if ($userRole === 'owner'): ?>
                    <button class="board-action-btn"
                            onclick="window.location.href='edit_board.php?id=<?php echo $board->getId(); ?>'">
                        Edytuj nazwę
                    </button>
                    <button class="board-action-btn"
                            onclick="window.location.href='manage_users.php?board_id=<?php echo $board->getId(); ?>'">
                        Zarządzaj użytkownikami
                    </button>
                    <button class="board-action-btn"
                        onclick="if(confirm('Na pewno chcesz usunąć tę tablicę?')) {
                                window.location.href='delete_board.php?id=<?php echo $board->getId(); ?>';
                                }">
                        Usuń tablicę
                <?php endif; ?>
            </div>
        </div>

        <div class="board-content" id="boardContent">
            <?php foreach ($groups as $group): ?>
                <div class="task-group" data-group-id="ID_GRUPY">
                    <div class="task-group-header">
                        <h2><?php echo htmlspecialchars($group->getTitle()); ?></h2>
                        <div class="task-group-options">
                            <?php if ($canEdit): ?>
                                <button class="task-group-btn"
                                        onclick="window.location.href='create_task.php?group_id=<?php echo $group->getId(); ?>'">
                                    + Zadanie
                                </button>
                                <button class="task-group-btn"
                                        onclick="window.location.href='edit_task_group.php?id=<?php echo $group->getId(); ?>'">
                                    Edytuj
                                </button>
                                <button class="task-group-btn"
                                        onclick="window.location.href='delete_task_group.php?id=<?php echo $group->getId(); ?>'">
                                    Usuń
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="task-group-body">
                        <?php
                        $tasks = $taskRepo->findAllByGroup($group->getId());
                        foreach ($tasks as $task): ?>
                            <div class="task-item" data-task-id="<?php echo $task->getId(); ?>">
                                <p><strong><?php echo htmlspecialchars($task->getTitle()); ?></strong></p>
                                <?php if ($task->getDescription()): ?>
                                    <p><?php echo nl2br(htmlspecialchars($task->getDescription())); ?></p>
                                <?php endif; ?>

                                <?php if ($canEdit): ?>
                                    <button onclick="window.location.href='edit_task.php?id=<?php echo $task->getId(); ?>'">
                                        Edytuj
                                    </button>
                                    <button onclick="window.location.href='delete_task.php?id=<?php echo $task->getId(); ?>'">
                                        Usuń
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Przyciski tylko dla canEdit -->
            <?php if ($canEdit): ?>
                <div class="add-group">
                    <button class="add-group-btn"
                            onclick="window.location.href='create_task_group.php?board_id=<?php echo $board->getId(); ?>'">
                        + Dodaj Grupę
                    </button>
                </div>
            <?php endif; ?>
        </div>
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
