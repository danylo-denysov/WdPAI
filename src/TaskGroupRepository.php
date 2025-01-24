<?php

namespace App;

use PDO;
require_once __DIR__ . '/TaskGroup.php';
require_once __DIR__ . '/Task.php';

class TaskGroupRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAllByBoard(int $boardId): array
    {
        $sql = "SELECT * FROM task_groups WHERE board_id = :boardId ORDER BY position ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':boardId', $boardId, PDO::PARAM_INT);
        $stmt->execute();

        $groups = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = new TaskGroup(
                $row['id'],
                $row['board_id'],
                $row['title'],
                $row['position'],
                $row['created_at']
            );
        }
        return $groups;
    }

    public function findById(int $id): ?TaskGroup
    {
        $sql = "SELECT * FROM task_groups WHERE id = :id LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new TaskGroup(
                $row['id'],
                $row['board_id'],
                $row['title'],
                $row['position'],
                $row['created_at']
            );
        }
        return null;
    }


    public function save(TaskGroup $group): bool {
        $sqlMax = "SELECT MAX(position) AS max_pos FROM task_groups WHERE board_id = :b";
        $stmtMax = $this->connection->prepare($sqlMax);
        $stmtMax->bindValue(':b', $group->getBoardId(), PDO::PARAM_INT);
        $stmtMax->execute();
        $row = $stmtMax->fetch(PDO::FETCH_ASSOC);
        $maxPosition = $row['max_pos'] ?? 0;

        $group->setPosition($maxPosition + 1);

        $sql = "INSERT INTO task_groups (board_id, title, position) VALUES (:b, :title, :pos)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':b', $group->getBoardId());
        $stmt->bindValue(':title', $group->getTitle());
        $stmt->bindValue(':pos', $group->getPosition());
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM task_groups WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update(TaskGroup $group): bool
    {
        if (!$group->getId()) {
            return false;
        }
        $sql = "UPDATE task_groups SET title = :title, position = :position WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':title', $group->getTitle());
        $stmt->bindValue(':position', $group->getPosition(), PDO::PARAM_INT);
        $stmt->bindValue(':id', $group->getId(), PDO::PARAM_INT);

        return $stmt->execute();
    }
}
