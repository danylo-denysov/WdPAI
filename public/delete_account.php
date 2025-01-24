<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

use App\Database;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/UserRepository.php';

$db = Database::getConnection();

$userId = (int)$_SESSION['user_id'];

$sql = "DELETE FROM users WHERE id = :id";
$stmt = $db->prepare($sql);
$deleted = $stmt->execute([':id' => $userId]);

if ($deleted) {
    session_destroy();
    header('Location: start.php');
    exit;
} else {
    die("Nie udało się usunąć konta!");
}

