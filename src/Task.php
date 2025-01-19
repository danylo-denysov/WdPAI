<?php

namespace App;

class Task
{
    private ?int $id;
    private int $groupId;
    private ?int $assignedUserId;
    private string $title;
    private ?string $description;
    private int $position;
    private ?string $createdAt;
    private ?string $deadline;
    private bool $isDone;

    public function __construct(
        ?int $id,
        int $groupId,
        ?int $assignedUserId,
        string $title,
        ?string $description,
        int $position,
        ?string $createdAt,
        ?string $deadline,
        bool $isDone
    ) {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->assignedUserId = $assignedUserId;
        $this->title = $title;
        $this->description = $description;
        $this->position = $position;
        $this->createdAt = $createdAt;
        $this->deadline = $deadline;
        $this->isDone = $isDone;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getAssignedUserId(): ?int
    {
        return $this->assignedUserId;
    }

    public function setAssignedUserId(?int $userId): void
    {
        $this->assignedUserId = $userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $desc): void
    {
        $this->description = $desc;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $pos): void
    {
        $this->position = $pos;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getDeadline(): ?string
    {
        return $this->deadline;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function setDone(bool $done): void
    {
        $this->isDone = $done;
    }
}
