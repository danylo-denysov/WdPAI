<?php
session_start();

use App\Database;
use App\TaskRepository;
use App\TaskGroupRepository;

require_once __DIR__ . '/../src/Database.php';
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
$groupRepo = new TaskGroupRepository($db);

$task = $taskRepo->findById((int)$taskId);
if (!$task) {
    die("Nie znaleziono zadania o ID $taskId");
}

$taskGroup = $groupRepo->findById($task->getGroupId());
$boardId = $taskGroup->getBoardId();

$deleted = $taskRepo->delete((int)$taskId);
if ($deleted) {
    header("Location: board_view.php?id=$boardId");
    exit;
} else {
    die("Nie udało się usunąć zadania o ID $taskId");
}