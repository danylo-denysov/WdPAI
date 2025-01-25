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
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($board->getTitle()); ?></title>
    <link rel="stylesheet" href="css/board_view.css">
    <script>
        const userRole = '<?php echo $userRole; ?>';
    </script>
    <script src="dragdrop.js"></script>
</head>
<body>

<div class="header">
    <div class="header-left">
        <form action="home.php" method="post">
            <button type="submit" class="back-btn">Wróć do tablic</button>
        </form>
    </div>

    <!-- Przyciski tylko dla owner -->
    <div class="header-center">
        <h1 class="board-name"><?php echo htmlspecialchars($board->getTitle()); ?></h1>
        <?php if ($userRole === 'owner'): ?>
            <div class="board-manage-btns">
                <button class="manage-btn" onclick="window.location.href='edit_board.php?id=<?php echo $board->getId(); ?>'">
                    Edytuj nazwę
                </button>
                <button class="manage-btn" onclick="window.location.href='manage_users.php?board_id=<?php echo $board->getId(); ?>'">
                    Zarządzaj użytkownikami
                </button>
                <button class="manage-btn delete-btn" onclick="if(confirm('Na pewno chcesz usunąć tę tablicę?')) { window.location.href='delete_board.php?id=<?php echo $board->getId(); ?>'; }">
                    Usuń tablicę
                </button>
            </div>
        <?php endif; ?>
    </div>

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

<!-- Przyciski tylko dla canEdit -->
<div class="main-content">
    <div class="board-content" id="boardContent">
        <?php foreach ($groups as $group): ?>
            <div class="task-group" data-group-id="<?php echo $group->getId(); ?>" draggable="true">
                <div class="task-group-header">
                    <h2><?php echo htmlspecialchars($group->getTitle()); ?></h2>
                    <?php if ($canEdit): ?>
                        <div class="task-group-options">
                            <button class="task-group-btn" onclick="window.location.href='create_task.php?group_id=<?php echo $group->getId(); ?>'">
                                + Zadanie
                            </button>
                            <button class="task-group-btn" onclick="window.location.href='edit_task_group.php?id=<?php echo $group->getId(); ?>'">
                                Edytuj
                            </button>
                            <button class="task-group-btn delete-btn" onclick="window.location.href='delete_task_group.php?id=<?php echo $group->getId(); ?>'">
                                Usuń
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="task-group-body">
                    <?php
                    $tasks = $taskRepo->findAllByGroup($group->getId());
                    foreach ($tasks as $task):
                        ?>
                        <div class="task-item" data-task-id="<?php echo $task->getId(); ?>" draggable="true">
                            <strong><?php echo htmlspecialchars($task->getTitle()); ?></strong>
                            <?php if ($task->getDescription()): ?>
                                <p><?php echo nl2br(htmlspecialchars($task->getDescription())); ?></p>
                            <?php endif; ?>
                            <?php if ($canEdit): ?>
                                <button class="edit-task-btn" onclick="window.location.href='edit_task.php?id=<?php echo $task->getId(); ?>'">
                                    Edytuj
                                </button>
                                <button class="delete-task-btn" onclick="window.location.href='delete_task.php?id=<?php echo $task->getId(); ?>'">
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
            <div class="add-group" draggable="false">
                <button class="add-group-btn" onclick="window.location.href='create_task_group.php?board_id=<?php echo $board->getId(); ?>'">
                    + Dodaj Grupę
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>