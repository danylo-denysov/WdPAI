<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

use App\Database;
use App\BoardRepository;
use App\UserRepository;

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/BoardRepository.php';
require_once __DIR__ . '/../src/UserRepository.php';

$boardId = $_GET['board_id'] ?? null;
if (!$boardId) {
    die("Brak ID tablicy.");
}

$db = Database::getConnection();
$boardRepo = new BoardRepository($db);
$userRepo  = new UserRepository($db);

$board = $boardRepo->findById((int)$boardId);
if (!$board) {
    die("Tablica nie istnieje (id=$boardId).");
}

if ($board->getOwnerId() !== $userId) {
    die("Brak uprawnień (tylko właściciel może zarządzać userami).");
}

$message = '';

if (isset($_POST['add_user'])) {
    $email = $_POST['email'] ?? '';
    $role = $_POST['role'] ?? 'viewer';

    if (!in_array($role, ['viewer','editor'], true)) {
        $role = 'viewer';
    }

    if (!empty($email)) {
        $u = $userRepo->findByEmail($email);
        if ($u) {
            $added = $boardRepo->addUser($board->getId(), $u->getId(), $role);
            if ($added) {
                $message = "Dodano użytkownika {$u->getUsername()} (Email: {$u->getEmail()}) z rolą $role.";
            } else {
                $message = "Nie udało się dodać. Prawdopodobnie użytkownik już ma dostęp.";
            }
        } else {
            $message = "Nie znaleziono użytkownika o emailu: $email";
        }
    } else {
        $message = "Email nie może być pusty.";
    }
}

if (isset($_POST['change_role'])) {
    $uid = $_POST['user_id'] ?? null;
    $newRole = $_POST['new_role'] ?? 'viewer';
    $u = $userRepo->findById($uid);

    if ($uid) {
        if (!in_array($newRole, ['viewer','editor'], true)) {
            $newRole = 'viewer';
        }

        if ((int)$uid === $board->getOwnerId()) {
            $message = "Nie można zmienić roli właściciela tablicy.";
        } else {
            $ok = $boardRepo->updateUserRole($board->getId(), (int)$uid, $newRole);
            if ($ok) {
                $message = "Zaktualizowano rolę użytkownika {$u->getUsername()} (Email: {$u->getEmail()}) na $newRole.";
            } else {
                $message = "Nie udało się zmienić roli.";
            }
        }
    }
}

if (isset($_POST['remove_user'])) {
    $uid = $_POST['user_id'] ?? null;
    $u = $userRepo->findById($uid);
    if ($uid) {
        if ((int)$uid === $board->getOwnerId()) {
            $message = "Nie można usunąć właściciela tablicy.";
        } else {
            $ok = $boardRepo->removeUser($board->getId(), (int)$uid);
            if ($ok) {
                $message = "Usunięto użytkownika {$u->getUsername()} (Email: {$u->getEmail()}) z tablicy.";
            } else {
                $message = "Nie udało się usunąć użytkownika.";
            }
        }
    }
}

$boardUsers = $boardRepo->getBoardUsers($board->getId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie użytkownikami tablicy</title>
    <link rel="stylesheet" href="editing.css">
</head>
<body>
<div class="main-content">
    <h1>Zarządzanie użytkownikami tablicy: <?php echo htmlspecialchars($board->getTitle()); ?></h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="manage_users.php?board_id=<?php echo $board->getId(); ?>" method="post">
        <h2>Dodaj użytkownika</h2>
        <label>
            Email:
            <input type="email" name="email" required>
        </label>
        <label>
            Rola:
            <select name="role">
                <option value="viewer">viewer</option>
                <option value="editor">editor</option>
            </select>
        </label>
        <button type="submit" name="add_user">Dodaj</button>
    </form>

    <hr>

    <h2>Użytkownicy w tej tablicy</h2>
    <table border="1" cellpadding="5" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Rola</th>
            <th>Akcje</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($boardUsers as $bu): ?>
            <tr>
                <td><?php echo $bu['user_id']; ?></td>
                <td><?php echo htmlspecialchars($bu['username']); ?></td>
                <td><?php echo htmlspecialchars($bu['email']); ?></td>
                <td><?php echo htmlspecialchars($bu['role']); ?></td>
                <td>
                    <?php if ((int)$bu['user_id'] === $board->getOwnerId()): ?>
                        Właściciel
                    <?php else: ?>
                        <form action="manage_users.php?board_id=<?php echo $board->getId(); ?>" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $bu['user_id']; ?>">
                            <select name="new_role">
                                <option value="viewer">viewer</option>
                                <option value="editor">editor</option>
                            </select>
                            <button type="submit" name="change_role">Zmień rolę</button>
                        </form>

                        <form action="manage_users.php?board_id=<?php echo $board->getId(); ?>" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $bu['user_id']; ?>">
                            <button type="submit" name="remove_user">Usuń</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <button onclick="window.location.href='board_view.php?id=<?php echo $board->getId(); ?>'">
        Powrót do tablicy
    </button>
</div>
</body>
</html>
