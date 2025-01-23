<?php
session_start();

use App\Database;
use App\TaskGroupRepository;

require_once __DIR__ . '/../src/Database.php';
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

$deleted = $groupRepo->delete((int)$id);
if ($deleted) {
    header("Location: board_view.php?id=$boardId");
    exit;
} else {
    die("Nie udało się usunąć kolumny o ID $id");
}