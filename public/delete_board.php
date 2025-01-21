<?php
session_start();

use App\Database;
use App\BoardRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BoardRepository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$boardId = $_GET['id'] ?? null;
if (!$boardId) {
    die("Brak ID tablicy do usunięcia.");
}

$db = Database::getConnection();
$boardRepo = new BoardRepository($db);

$board = $boardRepo->findById((int)$boardId);
if (!$board) {
    die("Nie znaleziono tablicy o ID $boardId.");
}

if ($board->getOwnerId() !== $userId) {
    die("Brak uprawnień – nie jesteś właścicielem tej tablicy.");
}

$deleted = $boardRepo->delete($board->getId());
if ($deleted) {
    header('Location: home.php');
    exit;
} else {
    die("Błąd podczas usuwania tablicy o ID $boardId.");
}
