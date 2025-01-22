<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success"=>false, "message"=>"Nie zalogowano"]);
    exit;
}
$userId = (int)$_SESSION['user_id'];

use App\Database;
use App\BoardRepository;
use App\TaskRepository;
use App\TaskGroupRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BoardRepository.php';
require_once __DIR__ . '/../src/TaskRepository.php';
require_once __DIR__ . '/../src/TaskGroupRepository.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['groupId']) || !isset($data['newOrder'])) {
    echo json_encode(["success"=>false, "message"=>"Brak danych"]);
    exit;
}

$groupId = (int)$data['groupId'];
$newOrder = $data['newOrder'];

$db = Database::getConnection();
$boardRepo = new BoardRepository($db);
$taskRepo = new TaskRepository($db);
$groupRepo = new TaskGroupRepository($db);

$group = $groupRepo->findById($groupId);
if (!$group) {
    echo json_encode(["success"=>false, "message"=>"Kolumna nie istnieje."]);
    exit;
}
$boardId = $group->getBoardId();

$board = $boardRepo->findById($boardId);
if (!$board) {
    echo json_encode(["success"=>false, "message"=>"Tablica nie istnieje."]);
    exit;
}
$role = null;
if ($board->getOwnerId() === $userId) {
    $role = 'owner';
} else {
    $role = $boardRepo->getUserRole($boardId, $userId);
}
if (!in_array($role, ['owner','editor'], true)) {
    echo json_encode(["success"=>false, "message"=>"Brak uprawnień."]);
    exit;
}

foreach ($newOrder as $taskData) {
    $taskId = (int)($taskData['taskId']);
    $pos    = (int)($taskData['position']);

    $task = $taskRepo->findById($taskId);
    if (!$task) {
        continue;
    }
    $task->setGroupId($groupId);
    $task->setPosition($pos);
    $taskRepo->update($task);
}

echo json_encode(["success"=>true]);
