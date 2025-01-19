<?php

namespace App;

use PDO;

class TaskRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAllByGroup(int $groupId): array
    {
        $sql = "SELECT * FROM tasks WHERE group_id = :groupId ORDER BY position ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();

        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = new Task(
                $row['id'],
                $row['group_id'],
                $row['assigned_user_id'],
                $row['title'],
                $row['description'],
                $row['position'],
                $row['created_at'],
                $row['deadline'],
                $row['is_done']
            );
        }
        return $tasks;
    }

    public function findById(int $id): ?Task
    {
        $sql = "SELECT * FROM tasks WHERE id = :id LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Task(
                $row['id'],
                $row['group_id'],
                $row['assigned_user_id'],
                $row['title'],
                $row['description'],
                $row['position'],
                $row['created_at'],
                $row['deadline'],
                $row['is_done']
            );
        }
        return null;
    }


    public function save(Task $task): bool
    {
        $sql = "
            INSERT INTO tasks (group_id, assigned_user_id, title, description, position, deadline, is_done)
            VALUES (:group_id, :assigned_user_id, :title, :description, :position, :deadline, :is_done)
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':group_id', $task->getGroupId(), PDO::PARAM_INT);
        $stmt->bindValue(':assigned_user_id', $task->getAssignedUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':title', $task->getTitle());
        $stmt->bindValue(':description', $task->getDescription());
        $stmt->bindValue(':position', $task->getPosition(), PDO::PARAM_INT);
        $stmt->bindValue(':deadline', $task->getDeadline());
        $stmt->bindValue(':is_done', $task->isDone(), PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update(Task $task): bool
    {
        if (!$task->getId()) {
            return false;
        }

        $sql = "
            UPDATE tasks
            SET
                group_id = :group_id,
                assigned_user_id = :assigned_user_id,
                title = :title,
                description = :description,
                position = :position,
                deadline = :deadline,
                is_done = :is_done
            WHERE id = :id
        ";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':group_id', $task->getGroupId(), PDO::PARAM_INT);
        $stmt->bindValue(':assigned_user_id', $task->getAssignedUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':title', $task->getTitle());
        $stmt->bindValue(':description', $task->getDescription());
        $stmt->bindValue(':position', $task->getPosition(), PDO::PARAM_INT);
        $stmt->bindValue(':deadline', $task->getDeadline());
        $stmt->bindValue(':is_done', $task->isDone(), PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $task->getId(), PDO::PARAM_INT);

        return $stmt->execute();
    }
}
