<?php

namespace App;

use PDO;
require_once __DIR__ . '/Board.php';

class BoardRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findById(int $id): ?Board
    {
        $sql = "SELECT * FROM boards WHERE id = :id LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Board(
                $row['id'],
                $row['title'],
                $row['owner_id'],
                $row['created_at']
            );
        }
        return null;
    }

    public function save(Board $board): bool
    {
        $sql = "INSERT INTO boards (title, owner_id) VALUES (:title, :owner_id)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':title', $board->getTitle());
        $stmt->bindValue(':owner_id', $board->getOwnerId(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function update(Board $board): bool
    {
        if ($board->getId() === null) {
            return false;
        }
        $sql = "UPDATE boards SET title = :title WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':title', $board->getTitle());
        $stmt->bindValue(':id', $board->getId(), PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function findAllByUser(int $userId): array
    {
        $sql = "
            SELECT DISTINCT b.*
            FROM boards b
            LEFT JOIN board_users bu ON b.id = bu.board_id
            WHERE b.owner_id = :userId OR bu.user_id = :userId
            ORDER BY b.created_at ASC
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $boards = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $boards[] = new Board(
                $row['id'],
                $row['title'],
                $row['owner_id'],
                $row['created_at']
            );
        }
        return $boards;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM boards WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function addUser(int $boardId, int $userId, string $role = 'viewer'): bool
    {
        $sqlCheck = "SELECT COUNT(*) FROM board_users WHERE board_id = :b AND user_id = :u";
        $stmtCheck = $this->connection->prepare($sqlCheck);
        $stmtCheck->execute([':b' => $boardId, ':u' => $userId]);
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            return false;
        }

        if (!in_array($role, ['editor','viewer'], true)) {
            $role = 'viewer';
        }

        $sql = "INSERT INTO board_users (board_id, user_id, role) VALUES (:b, :u, :r)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            ':b' => $boardId,
            ':u' => $userId,
            ':r' => $role,
        ]);
    }

    public function removeUser(int $boardId, int $userId): bool
    {
        $sql = "DELETE FROM board_users WHERE board_id = :b AND user_id = :u";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            ':b' => $boardId,
            ':u' => $userId,
        ]);
    }

    public function updateUserRole(int $boardId, int $userId, string $role): bool
    {
        if (!in_array($role, ['viewer','editor'], true)) {
            return false;
        }
        $sql = "UPDATE board_users SET role = :role WHERE board_id = :b AND user_id = :u";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            ':role' => $role,
            ':b' => $boardId,
            ':u' => $userId,
        ]);
    }

    public function getBoardUsers(int $boardId): array
    {
        $sql = "
            SELECT bu.user_id, bu.role, u.email, u.username
            FROM board_users bu
            JOIN users u ON bu.user_id = u.id
            WHERE bu.board_id = :b
            ORDER BY bu.role DESC
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':b' => $boardId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserRole(int $boardId, int $userId): ?string
    {
        $sql = "SELECT role FROM board_users WHERE board_id = :b AND user_id = :u LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':b' => $boardId, ':u' => $userId]);
        $role = $stmt->fetchColumn();
        return $role ?: null;
    }
}
